<?php
/**
 * The Plus Block Generate BLocks Css 
 *
 * @since   1.1.3
 * @package TPGB
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Tp_Generate_Blocks_Css {
	
	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	protected static $all_attributes= []; 
	
	/**
	 * Constructor
	 */
	public function __construct() {
		if( !class_exists('csstidy') ){
			require_once TPGB_PATH . 'classes/parse_css/class.csstidy.php';
		}
	}
	
	public function google_font_load(){
		$googleFont_Load = Tp_Blocks_Helper::get_extra_option('gfont_load');
		$googleFonts = false;
		if( empty($googleFont_Load) || (!empty($googleFont_Load) && $googleFont_Load!='disable') ){
			$googleFonts = true;
			$googleFonts = apply_filters( 'tpgb_google_font_load', $googleFonts );
		}
		return $googleFonts;
	}
	
	/*
	 * Generate Dynamic Css
 	 */
	public function generate_dynamic_css( $post_id = '', $dynamic = false ){
		
		self::$all_attributes = [];
		$post_id = (!empty($post_id)) ? $post_id : $this->is_post_id();
		$post_data = get_post( $post_id );
		$content = (isset($post_data->post_content)) ? $post_data->post_content : ''; 
		$parse_blocks = parse_blocks( $content );
		
		foreach ($parse_blocks as $block) {
			$this->parse_block_settings( $block, $dynamic );
		}
		
		$css_render = $this->tpgb_cssGenerator();
		
		if( !empty($css_render) ){
			$csstidy = new csstidy();
			
			$csstidy->set_cfg('optimise_shorthands', 1);
			$csstidy->set_cfg('merge_selectors', 2);
			$csstidy->set_cfg('remove_bslash',false);
			$csstidy->set_cfg('sort_selectors',true);
			//$csstidy->set_cfg('sort_properties',true);
			//$csstidy->set_cfg('preserve_css',true);
			$csstidy->set_cfg('template', 'high');
			
			$csstidy->parse($css_render);
			
			$css_render = $csstidy->print->plain();
		}
		
		return $this->minify_css($css_render);
	}
	
	/*
	 * @since 1.3.0
	 */
	public function parse_block_settings( $block = [], $dynamic = false){
		$settings = [];
		
		if(!empty($block)){
			$context = array();
			
			global $post;
			if ( $post instanceof WP_Post ) {
				$context['postId'] = $post->ID;
				$context['postType'] = $post->post_type;
			}
			
			$context = apply_filters( 'render_block_context', $context, $block );
			$wpblock = new WP_Block( $block, $context );
			
			$attributes = isset( $wpblock->parsed_block['attrs'] ) ? $wpblock->parsed_block['attrs'] : array();
			
			if ( ! is_null( $wpblock->block_type ) ) {
				if ( ! isset( $wpblock->block_type->attributes ) ) {
					return $attributes;
				}
				
				$block_attribute = $wpblock->block_type->attributes;
				
				foreach ( $attributes as $attribute_name => $value ) {
					if ( ! isset( $block_attribute[ $attribute_name ] ) ) {
						continue;
					}
					$schema = $block_attribute[ $attribute_name ];
		 
					$is_valid = rest_validate_value_from_schema( $value, $schema, $attribute_name );
					if ( is_wp_error( $is_valid ) ) {
						unset( $attributes[ $attribute_name ] );
					}
				}
				
				foreach ( $block_attribute as $attribute_name => $schema ) {
					
					if ( isset( $schema['default'] ) && !isset($schema['repeaterField']) && !isset($schema['groupField'])) {
						$attributes[ $attribute_name ] = ( isset( $attributes[ $attribute_name ] ) ) ? [ 'value' => $attributes[ $attribute_name ], ] : [ 'value' => $schema['default'], ];
						
						if( isset($schema['style']) ){
							$attributes[ $attribute_name ]['style'] = $schema['style'];
						}
						
					}else if( isset($schema['repeaterField']) ){
						$repeatField = [];
						if(isset($attributes[ $attribute_name ])){
							foreach($attributes[ $attribute_name ] as $repeatKey => $repeat){
								foreach($repeat as $key => $repeatValue){
									$repeatField[$repeatKey][$key] = [ 'value' => $repeatValue ];
									if( isset($schema['repeaterField'][0]->$key['style']) ){
										$repeatField[$repeatKey][$key]['style'] = $schema['repeaterField'][0]->$key['style'];
									}
								}
							}
						}else if(isset($schema['repeaterField']) && isset($schema['default'])){
							foreach($schema['default'] as $repeatKey => $repeat){
								foreach($repeat as $key => $repeatValue){
									$repeatField[$repeatKey][$key] = [ 'value' => $repeatValue ];
									if( isset($schema['repeaterField'][0]->$key['style']) ){
										$repeatField[$repeatKey][$key]['style'] = $schema['repeaterField'][0]->$key['style'];
									}
								}
							}
						}
						$attributes[ $attribute_name ]['repeaterField']= $repeatField;
					}else if( isset($schema['groupField']) ){
						$repeatField = [];
						if(isset($attributes[ $attribute_name ])){
							foreach($attributes[ $attribute_name ] as $repeatKey => $repeat){
								$repeatField[$repeatKey] = [ 'value' => $repeat ];
								if( isset($schema['groupField'][0]->$repeatKey['style']) ){
									$repeatField[$repeatKey]['style'] = $schema['groupField'][0]->$repeatKey['style'];
								}
							}
						}else if(isset($schema['groupField']) && isset($schema['default'])){
							foreach($schema['default'] as $repeatKey => $repeat){
								$repeatField[$repeatKey] = [ 'value' => $repeat ];
								if( isset($schema['groupField'][0]->$repeatKey['style']) ){
									$repeatField[$repeatKey]['style'] = $schema['groupField'][0]->$repeatKey['style'];
								}
							}
						}
						$attributes[ $attribute_name ]['groupField']= $repeatField;
					}
				}
			}
			
			if(!empty($attributes)){
				
				//Dynamic Value Attr List
				$dynamicAttr = [];
				if( !empty($dynamic) ){
					foreach ( $attributes as $key => $val ) {
						$dynamicpara = [];
						if($key =='block_id'){
							$dynamicAttr[$key] = $val;
						}else if( isset($val['value']) && !empty($val['value']) && gettype($val['value']) == 'string' && preg_match_all( '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/' , $val['value'], $matches ) ){
							//Color Dynamic
							if( isset($matches[1]) && !empty($matches[1]) ){
								$jsonString = "[".str_replace("'", "", $matches[1][0]). "]";
								$array = json_decode($jsonString, true);
								$dynamicpara['field'] = (isset($array[0]) && !empty($array[0])) ? (array) $array[0] : [];
								$dynamicpara['id'] = get_queried_object_id();
								
								$dynamicVal = TPGBP_Pro_Init_Blocks::tpgb_get_dynamic_content($dynamicpara);
								$dynamicAttr[$key] = $val;
								$dynamicAttr[$key]['value'] = $dynamicVal;
							}
						}else if( isset($val['value']) && !empty($val['value']) && gettype($val['value']) == 'array'  ){
							if( isset($val['value']['openBg']) && $val['value']['openBg'] === 1 && $val['value']['bgType']=='color' && isset($val['value']['bgDefaultColor']) && preg_match_all( '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/' , $val['value']['bgDefaultColor'], $matches ) ){
								//Bg Dynamic Color
								if( isset($matches[1]) && !empty($matches[1]) ){
									$jsonString = "[".str_replace("'", "", $matches[1][0]). "]";
									$array = json_decode($jsonString, true);
									$dynamicpara['field'] = (isset($array[0]) && !empty($array[0])) ? (array) $array[0] : [];
									$dynamicpara['id'] = get_queried_object_id();
									
									$dynamicVal = TPGBP_Pro_Init_Blocks::tpgb_get_dynamic_content($dynamicpara);
									$dynamicAttr[$key] = $val;
									$dynamicAttr[$key]['value']['bgDefaultColor'] = $dynamicVal;
								}
							}else if( isset($val['value']['openBg']) && $val['value']['openBg'] === 1 && $val['value']['bgType']=='image' && isset($val['value']['bgImage']) && isset($val['value']['bgImage']['dynamic']) && isset($val['value']['bgImage']['dynamic']['dynamicUrl']) ){
								//Bg Dynamic Image
								$dynamicpara['field'] = ( !empty($val['value']['bgImage']['dynamic']) ) ? (array) $val['value']['bgImage']['dynamic'] : [];
								$dynamicpara['id'] = get_queried_object_id();
								
								$dynamicVal = TPGBP_Pro_Init_Blocks::tpgb_get_dynamic_content($dynamicpara);
								
								$dynamicAttr[$key] = $val;
								
								$dynamicAttr[$key]['value']['bgImage']['id'] = isset($dynamicVal['id']) ? $dynamicVal['id'] : '';
								$dynamicAttr[$key]['value']['bgImage']['url'] = isset($dynamicVal['url']) ? $dynamicVal['url'] : '';
							
							}else if( ((isset($val['value']['openBorder']) && $val['value']['openBorder'] === 1) || (isset($val['value']['openShadow']) && $val['value']['openShadow'] === true)) && isset($val['value']['color']) && preg_match_all( '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/' , $val['value']['color'], $matches ) ){
								//Border/BoxShadow Dynamic Color
								if( isset($matches[1]) && !empty($matches[1]) ){
									$jsonString = "[".str_replace("'", "", $matches[1][0]). "]";
									$array = json_decode($jsonString, true);
									$dynamicpara['field'] = (isset($array[0]) && !empty($array[0])) ? (array) $array[0] : [];
									$dynamicpara['id'] = get_queried_object_id();
									
									$dynamicVal = TPGBP_Pro_Init_Blocks::tpgb_get_dynamic_content($dynamicpara);
									$dynamicAttr[$key] = $val;
									$dynamicAttr[$key]['value']['color'] = $dynamicVal;
								}
							}
						}else if( isset($val['value']) && !isset($val['style']) ){
							$dynamicAttr[$key] = $val;
						}
					}
					if ( isset($attributes['ref']) && !empty( $attributes['ref'] ) ) {
						$post_data = get_post( $attributes['ref'] );
						$content = (isset($post_data->post_content)) ? $post_data->post_content : ''; 
						$parse_blocks = parse_blocks( $content );
						if(!empty($parse_blocks)){
							foreach ($parse_blocks as $block) {
								self::$all_attributes = $this->parse_block_settings($block, $dynamic);
							}
						}
					}
					$attributes = $dynamicAttr;
				}
				
				//Tpgb Block 
				if(preg_match('/\btpgb\/\b/', $block['blockName'])){
					$settings[ $block['blockName'] ] = $attributes;
					self::$all_attributes[] = $settings;
					
				}
			}
			if ( !empty( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as $inner_block ) {
					self::$all_attributes = $this->parse_block_settings($inner_block, $dynamic);
				}
			}
		}

		return self::$all_attributes;
	}
	
	/*
	 * The Plus Dynamic Css Generator
	 */
	public function tpgb_cssGenerator(){
		$Make_CSS ='';
		$md = [];
		$sm = [];
		$xs = [];
		$notResponsiveCss = [];
		
		if(!empty(self::$all_attributes)){
			foreach(self::$all_attributes as $key => $value){
				
				if (is_array($value) && !empty($value) ){
					foreach($value as $block_key => $block_value) {
						$blockID='';
						foreach($block_value as $attr_key => $attr_value) {
						
							$blockID = ( $attr_key==='block_id' && isset($attr_value['value'])) ? $attr_value['value'] : $blockID;
							
							if( isset( $attr_value['style'] ) && !empty( $attr_value['style'] ) ){
							
								foreach($attr_value['style'] as $indexStyle => $selectData) {
									$selectData = (array)$selectData;
									$cssSelecor = isset($selectData['selector']) ? $selectData['selector'] : '';
									
									if ($this->conditions_styling($block_value, $selectData, $attr_key, $indexStyle)) {
										
										if ( isset( $block_value[$attr_key]['value'] ) && (gettype( $block_value[$attr_key]['value'] ) == 'array' || gettype( $block_value[$attr_key]['value'] ) == 'object') ) {
											$values = $block_value[$attr_key]['value'];
											$device = false;
											$dimension = '';
											//Desktop
											$values = (array)$values;
											
											if (isset($values['md'])) {
												$device = true;
												
												if(gettype($values['md']) == 'object' || gettype($values['md']) == 'array'){
													$dimension = $this->tp_objectField($values['md'])['data'];
												}else{
													$dimension = (!empty($values['md']) || $values['md']==='0') ? $values['md'] . (isset($values['unit']) ? $values['unit'] : '') : '';
												}
												
												if($dimension!=''){
													$SelectorData = $this->singleField($cssSelecor, $blockID, $attr_key, $dimension, 'tpgb');
													if(isset($SelectorData[0]) && strpos($SelectorData[0], '{{') ){
														
														$matches = preg_match_all('/\{{(.*?)\}}/', $SelectorData[0], $output_array);
														if($matches){
															if( !empty($output_array[1]) ){
																foreach( $output_array[1] as $newKey ){
																	if(gettype($block_value[$newKey]['value']) == 'object'){
																		$block_value[$newKey]['value'] = (array)$block_value[$newKey]['value'];
																	}
																	if( (isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['md']) ) && (gettype($block_value[$newKey]['value']['md']) == 'object' || gettype($block_value[$newKey]['value']['md']) == 'array')){
																		$dimension = $this->tp_objectField($block_value[$newKey]['value']['md'])['data'];
																	}else if(isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['md'])){
																			$dimension = $block_value[$newKey]['value']['md'] . (isset($block_value[$newKey]['value']['unit']) ? $block_value[$newKey]['value']['unit'] : '');
																	}
																	$SelectorData = $this->singleField($SelectorData[0], $blockID, $newKey, $dimension,'tpgb');
																}
															}
														}
													}
													$md = array_merge($md,$SelectorData);
												}
											}
											// Tablet
											if (isset($values['sm'])) {
												$device = true;
												
												if(gettype($values['sm']) == 'object' || gettype($values['sm']) == 'array'){
													$dimension = $this->tp_objectField($values['sm'])['data'];
												}else{
													$dimension = (!empty($values['sm']) || $values['sm']==='0') ? $values['sm'] . (isset($values['unit']) ? $values['unit'] : '') : '';
												}
												if($dimension!=''){
													$SelectorData = $this->singleField($cssSelecor, $blockID, $attr_key, $dimension,'tpgb');
													if(isset($SelectorData[0]) && strpos($SelectorData[0], '{{') ){
														$matches = preg_match_all('/\{{(.*?)\}}/', $SelectorData[0], $output_array);
														if($matches){
															if( !empty($output_array[1]) ){
																foreach( $output_array[1] as $newKey ){
																	if(gettype($block_value[$newKey]['value']) == 'object'){
																		$block_value[$newKey]['value'] = (array)$block_value[$newKey]['value'];
																	}
																	if( (isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['sm'])) && (gettype($block_value[$newKey]['value']['sm']) == 'object' || gettype($block_value[$newKey]['value']['sm']) == 'array')){
																		$dimension = $this->tp_objectField($block_value[$newKey]['value']['sm'])['data'];
																	}else if(isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['sm'])){
																			$dimension = $block_value[$newKey]['value']['sm'] . (isset($block_value[$newKey]['value']['unit']) ? $block_value[$newKey]['value']['unit'] : '');
																	}
																	$SelectorData = $this->singleField($SelectorData[0], $blockID, $newKey, $dimension,'tpgb');
																}
															}
														}
													}
													$sm = array_merge($sm, $SelectorData);
												}
											}
											// Mobile
											if ( isset($values['xs']) ) {
												$device = true;
												
												if(gettype($values['xs']) == 'object' || gettype($values['xs']) == 'array'){
													$dimension = $this->tp_objectField($values['xs'])['data'];
												}else{
													$dimension = (!empty($values['xs']) || $values['xs']==='0') ? $values['xs'] . (isset($values['unit']) ? $values['unit'] : '') : '';
												}
												if( $dimension!='' ){
													$SelectorData = $this->singleField($cssSelecor, $blockID, $attr_key, $dimension,'tpgb');
													if(isset($SelectorData[0]) && strpos($SelectorData[0], '{{') ){
														$matches = preg_match_all('/\{{(.*?)\}}/', $SelectorData[0], $output_array);
														if($matches){
															if( !empty($output_array[1]) ){
																foreach( $output_array[1] as $newKey ){
																	if(gettype($block_value[$newKey]['value']) == 'object'){
																		$block_value[$newKey]['value'] = (array)$block_value[$newKey]['value'];
																	}
																	if(isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['xs']) && (gettype($block_value[$newKey]['value']['xs']) == 'object' || gettype($block_value[$newKey]['value']['xs']) == 'array')){
																		$dimension = $this->tp_objectField($block_value[$newKey]['value']['xs'])['data'];
																	}else if(isset($block_value[$newKey]['value']) && isset($block_value[$newKey]['value']['xs'])){
																		$dimension = $block_value[$newKey]['value']['xs'] . (isset($block_value[$newKey]['value']['unit']) ? $block_value[$newKey]['value']['unit'] : '');
																	}
																	$SelectorData = $this->singleField($SelectorData[0], $blockID, $newKey, $dimension,'tpgb');
																}
															}
														}
													}
													$xs = array_merge($xs, $SelectorData);
												}
											}
											
											//Normal Responsive
											if ( !$device ) {
												$objectCss = $this->tp_objectField($block_value[$attr_key]['value']);
												
												$repWarp = $this->replaceWarp($cssSelecor, $blockID, 'tpgb');
												
												if (gettype($objectCss['data']) == 'array') {
													
													if (count($objectCss['data']) > 0) {
														if (isset($objectCss['data']['background'])) {
															array_push($notResponsiveCss, $repWarp . $objectCss['data']['background']);
														}
														//Typography
														if ($objectCss['data']['md']) {
															if(gettype($objectCss['data']['md']) == 'array' && $objectCss['data']['md'] != '' ){
																array_push( $md, $this->objectReplace($repWarp, $objectCss['data']['md']) );
															}else if( $objectCss['data']['md'] != '' ){
																array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data']['md'] . '}');
															}
														}
														if ($objectCss['data']['sm']) {
															if(gettype($objectCss['data']['sm']) == 'array' && $objectCss['data']['sm'] != '' ){
																array_push( $sm, $this->objectReplace($repWarp, $objectCss['data']['sm']) );
															}else if( $objectCss['data']['sm'] != '' ){
																array_push( $sm, $repWarp . '{' . $objectCss['data']['sm'] . '}');
															}
														}
														if ($objectCss['data']['xs']) {
															if(gettype($objectCss['data']['xs']) == 'array' && $objectCss['data']['xs'] != '' ){
																array_push($xs, $this->objectReplace($repWarp, $objectCss['data']['xs']) );
															}else if( $objectCss['data']['xs'] != '' ){
																array_push( $xs, $repWarp . '{' . $objectCss['data']['xs'] . '}' );
															}
														}
														if (isset($objectCss['data']['simple'])) {
															array_push($notResponsiveCss, $repWarp . '{' . $objectCss['data']['simple'] . '}');
														}
														if (isset($objectCss['data']['font'])) {
															array_unshift($notResponsiveCss, $objectCss['data']['font']);
														}
													}
												} else if ($objectCss['data'] && !strpos($objectCss['data'], '{{') ) {
													if ($objectCss['action'] == 'append') {
														array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data'] . '}' );
													} else {
														array_push( $notResponsiveCss, $this->singleField($cssSelecor, $blockID, $attr_key, $objectCss['data'], 'tpgb') );
													}
												}
											}
										} else {
											if ($attr_key == 'hideDesktop' || $attr_key == 'globalHideDesktop') {
												$notResponsiveCss = array_merge($notResponsiveCss, $this->singleField($cssSelecor, $blockID, $attr_key, $block_value[$attr_key]['value'], 'tpgb'));
											}else if ($attr_key == 'hideTablet' || $attr_key == 'globalHideTablet') {
												$notResponsiveCss = array_merge($notResponsiveCss, $this->singleField($cssSelecor, $blockID, $attr_key, $block_value[$attr_key]['value'], 'tpgb'));
											} else if ($attr_key == 'hideMobile' || $attr_key == 'globalHideMobile') {
												$notResponsiveCss = array_merge($notResponsiveCss, $this->singleField($cssSelecor, $blockID, $attr_key, $block_value[$attr_key]['value'], 'tpgb'));
											} else {
												if ($block_value[$attr_key]['value'] != '') {
													$isDynamic = false;
													if( gettype($block_value[$attr_key]['value']) == 'string' && preg_match_all( '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/' , $block_value[$attr_key]['value'], $matches )){
														if( isset($matches[1]) && !empty($matches[1]) ){
															$isDynamic = true;
														}
													}
													if(!$isDynamic){
														$notResponsiveCss = array_merge($notResponsiveCss, $this->singleField($cssSelecor, $blockID, $attr_key, $block_value[$attr_key]['value'], 'tpgb'));
													}
												}
											}
										}
									}
								}
							}else if( isset( $attr_value['repeaterField'] ) && !empty( $attr_value['repeaterField'] ) ){
							
								foreach($attr_value['repeaterField'] as $itemIndex => $itemData) {
									$itemData = (array)$itemData;
									foreach($itemData as $attrKey => $attrValue) {
										
										if( isset( $attrValue['style'] ) && !empty( $attrValue['style'] ) ){
											
											foreach($attrValue['style'] as $indexStyle => $selectData) {
												$selectData = (array)$selectData;
												$cssSelecor = isset($selectData['selector']) ? $selectData['selector'] : '';
												
												if ($this->conditions_styling($itemData, $selectData, $attrKey, $indexStyle)) {
													
													if ( isset( $itemData[$attrKey]['value'] ) && (gettype( $itemData[$attrKey]['value'] ) == 'array' || gettype( $itemData[$attrKey]['value'] ) == 'object') ) {
														$values = $itemData[$attrKey]['value'];
														$device = false;
														$dimension = '';
														
														//Desktop
														$values = (array)$values;
														
														// Desktop Responsive
														if (isset($values['md'])) {
															$device = true;

															if(gettype($values['md']) == 'object' || gettype($values['md']) == 'array'){
																$dimension = $this->tp_objectField($values['md'])['data'];
															}else{
																$dimension = (!empty($values['md']) || $values['md']==='0') ? $values['md'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $attrKey, $dimension, 'tpgb', $itemData['_key']['value']);
																$md = array_merge($md,$SelectorData);
															}
														}
														// Tablet Responsive
														if (isset($values['sm'])) {
															$device = true;

															if(gettype($values['sm']) == 'object' || gettype($values['sm']) == 'array'){
																$dimension = $this->tp_objectField($values['sm'])['data'];
															}else{
																$dimension = (!empty($values['sm']) || $values['sm']==='0') ? $values['sm'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $attrKey, $dimension, 'tpgb', $itemData['_key']['value']);
																$sm = array_merge($sm,$SelectorData);
															}
														}
														// Mobile Responsive
														if (isset($values['xs'])) {
															$device = true;

															if(gettype($values['xs']) == 'object' || gettype($values['xs']) == 'array'){
																$dimension = $this->tp_objectField($values['xs'])['data'];
															}else{
																$dimension = (!empty($values['xs']) || $values['xs']==='0') ? $values['xs'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $attrKey, $dimension, 'tpgb', $itemData['_key']['value']);
																$xs = array_merge($xs,$SelectorData);
															}
														}
														if ( !$device ) {
															$objectCss = $this->tp_objectField($itemData[$attrKey]['value']);
															
															$repWarp = $this->replaceWarp($cssSelecor, $blockID, 'tpgb');
															$repWarp = $this->replaceWarpItem($repWarp, $itemData['_key']['value']);
															
															if (gettype($objectCss['data']) == 'array') {
													
																if (count($objectCss['data']) > 0) {
																	if (isset($objectCss['data']['background'])) {
																		array_push($notResponsiveCss, $repWarp . $objectCss['data']['background']);
																	}
																}
																
																//Typography
																if ($objectCss['data']['md']) {
																	if(gettype($objectCss['data']['md']) == 'array' && $objectCss['data']['md'] != '' ){
																		array_push( $md, $this->objectReplace($repWarp, $objectCss['data']['md']) );
																	}else if( $objectCss['data']['md'] != '' ){
																		array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data']['md'] . '}');
																	}
																}
																if ($objectCss['data']['sm']) {
																	if(gettype($objectCss['data']['sm']) == 'array' && $objectCss['data']['sm'] != '' ){
																		array_push( $sm, $this->objectReplace($repWarp, $objectCss['data']['sm']) );
																	}else if( $objectCss['data']['sm'] != '' ){
																		array_push( $sm, $repWarp . '{' . $objectCss['data']['sm'] . '}');
																	}
																}
																if ($objectCss['data']['xs']) {
																	if(gettype($objectCss['data']['xs']) == 'array' && $objectCss['data']['xs'] != '' ){
																		array_push($xs, $this->objectReplace($repWarp, $objectCss['data']['xs']) );
																	}else if( $objectCss['data']['xs'] != '' ){
																		array_push( $xs, $repWarp . '{' . $objectCss['data']['xs'] . '}' );
																	}
																}
																if (isset($objectCss['data']['simple'])) {
																	array_push($notResponsiveCss, $repWarp . '{' . $objectCss['data']['simple'] . '}');
																}
																if (isset($objectCss['data']['font'])) {
																	array_unshift($notResponsiveCss, $objectCss['data']['font']);
																}
															} else if ($objectCss['data'] && !strpos($objectCss['data'], '{{') ) {
																if ($objectCss['action'] == 'append') {
																	array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data'] . '}' );
																} else {
																	array_push( $notResponsiveCss, $this->singleField( $cssSelecor, $blockID, $attrKey, $objectCss['data'], 'tpgb', $itemData['_key']['value'] ) );
																}
															}
														}
													}else{
														if ($itemData[$attrKey]['value'] != '') {
															
															$objectCss = $this->singleField($cssSelecor, $blockID, $attrKey, $itemData[$attrKey]['value'], 'tpgb', $itemData['_key']['value'] );
															if(isset($objectCss[0]) && strpos($objectCss[0], '{{')){
																$matches = preg_match_all('/\{{(.*?)\}}/', $objectCss[0], $output_array);
																if($matches){
																	if( !empty($output_array[1]) ){
																		foreach( $output_array[1] as $newKey ){
																			if(gettype($block_value[$newKey]['value']) == 'object'){
																				$block_value[$newKey]['value'] = (array)$block_value[$newKey]['value'];
																			}
																			if(isset($block_value[$newKey]['value']) && (gettype($block_value[$newKey]['value']) == 'object' || gettype($block_value[$newKey]['value']) == 'array')){
																				$dimension = $this->tp_objectField($block_value[$newKey]['value'])['data'];
																			}else{
																				$dimension = $block_value[$newKey]['value'];
																			}
																			$objectCss = $this->singleField($objectCss[0], $blockID, $newKey, $dimension,'tpgb');
																		}
																	}
																}
															}
															$notResponsiveCss = array_merge($notResponsiveCss, $objectCss);
														}
													}
												}
												
											}
										}
									}
								}
							}else if( isset( $attr_value['groupField'] ) && !empty( $attr_value['groupField'] ) ){
							
								foreach($attr_value['groupField'] as $itemIndex => $itemData) {
									$itemData = (array)$itemData;
										if( isset( $itemData['style'] ) && !empty( $itemData['style'] ) ){
												
											foreach($itemData['style'] as $indexStyle => $selectData) {
												$selectData = (array)$selectData;
												$cssSelecor = isset($selectData['selector']) ? $selectData['selector'] : '';
												
												
												if ($this->conditions_styling($itemData, $selectData, $itemIndex, $indexStyle)) {
													
													if ( isset( $itemData['value'] ) && (gettype( $itemData['value'] ) == 'array' || gettype( $itemData['value'] ) == 'object') ) {
														$values = $itemData['value'];
														$device = false;
														$dimension = '';
														
														//Desktop
														$values = (array)$values;
														
														// Desktop Responsive
														if (isset($values['md'])) {
															$device = true;

															if(gettype($values['md']) == 'object' || gettype($values['md']) == 'array'){
																$dimension = $this->tp_objectField($values['md'])['data'];
															}else{
																$dimension = (!empty($values['md']) || $values['md']==='0') ? $values['md'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $itemIndex, $dimension, 'tpgb');
																$md = array_merge($md,$SelectorData);
															}
														}
														// Tablet Responsive
														if (isset($values['sm'])) {
															$device = true;

															if(gettype($values['sm']) == 'object' || gettype($values['sm']) == 'array'){
																$dimension = $this->tp_objectField($values['sm'])['data'];
															}else{
																$dimension = (!empty($values['sm']) || $values['sm']==='0') ? $values['sm'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $itemIndex, $dimension, 'tpgb');
																$sm = array_merge($sm,$SelectorData);
															}
														}
														// Mobile Responsive
														if (isset($values['xs'])) {
															$device = true;

															if(gettype($values['xs']) == 'object' || gettype($values['xs']) == 'array'){
																$dimension = $this->tp_objectField($values['xs'])['data'];
															}else{
																$dimension = (!empty($values['xs']) || $values['xs']==='0') ? $values['xs'] . (isset($values['unit']) ? $values['unit'] : '') : '';
															}
															if( $dimension!='' ){
																$SelectorData = $this->singleField($cssSelecor, $blockID, $itemIndex, $dimension, 'tpgb');
																$xs = array_merge($xs,$SelectorData);
															}
														}
														if ( !$device ) {
															$objectCss = $this->tp_objectField($itemData['value']);
															
															$repWarp = $this->replaceWarp($cssSelecor, $blockID, 'tpgb');
															if (gettype($objectCss['data']) == 'array') {
													
																if (count($objectCss['data']) > 0) {
																	if (isset($objectCss['data']['background'])) {
																		array_push($notResponsiveCss, $repWarp . $objectCss['data']['background']);
																	}
																}
																
																//Typography
																if ($objectCss['data']['md']) {
																	if(gettype($objectCss['data']['md']) == 'array' && $objectCss['data']['md'] != '' ){
																		array_push( $md, $this->objectReplace($repWarp, $objectCss['data']['md']) );
																	}else if( $objectCss['data']['md'] != '' ){
																		array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data']['md'] . '}');
																	}
																}
																if ($objectCss['data']['sm']) {
																	if(gettype($objectCss['data']['sm']) == 'array' && $objectCss['data']['sm'] != '' ){
																		array_push( $sm, $this->objectReplace($repWarp, $objectCss['data']['sm']) );
																	}else if( $objectCss['data']['sm'] != '' ){
																		array_push( $sm, $repWarp . '{' . $objectCss['data']['sm'] . '}');
																	}
																}
																if ($objectCss['data']['xs']) {
																	if(gettype($objectCss['data']['xs']) == 'array' && $objectCss['data']['xs'] != '' ){
																		array_push($xs, $this->objectReplace($repWarp, $objectCss['data']['xs']) );
																	}else if( $objectCss['data']['xs'] != '' ){
																		array_push( $xs, $repWarp . '{' . $objectCss['data']['xs'] . '}' );
																	}
																}
																if (isset($objectCss['data']['simple'])) {
																	array_push($notResponsiveCss, $repWarp . '{' . $objectCss['data']['simple'] . '}');
																}
																if (isset($objectCss['data']['font'])) {
																	array_unshift($notResponsiveCss, $objectCss['data']['font']);
																}
															} else if ($objectCss['data'] && !strpos($objectCss['data'], '{{') ) {
																if ($objectCss['action'] == 'append') {
																	array_push( $notResponsiveCss, $repWarp . '{' . $objectCss['data'] . '}' );
																} else {
																	array_push( $notResponsiveCss, $this->singleField( $cssSelecor, $blockID, $itemIndex, $objectCss['data'], 'tpgb' ) );
																}
															}
														}
													}else{
														if ($itemData['value'] != '') {
															$objectCss = $this->singleField($cssSelecor, $blockID, $itemIndex, $itemData['value'], 'tpgb' );
															if(isset($objectCss[0]) && strpos($objectCss[0], '{{')){
																$matches = preg_match_all('/\{{(.*?)\}}/', $objectCss[0], $output_array);
																if($matches){
																	if( !empty($output_array[1]) ){
																		foreach( $output_array[1] as $newKey ){
																			if(gettype($block_value[$newKey]['value']) == 'object'){
																				$block_value[$newKey]['value'] = (array)$block_value[$newKey]['value'];
																			}
																			if(isset($block_value[$newKey]['value']) && (gettype($block_value[$newKey]['value']) == 'object' || gettype($block_value[$newKey]['value']) == 'array')){
																			
																				$dimension = $this->tp_objectField($block_value[$newKey]['value'])['data'];
																			}else{
																				$dimension = $block_value[$newKey]['value'];
																			}
																			$objectCss = $this->singleField($objectCss[0], $blockID, $newKey, $dimension,'tpgb');
																		}
																	}
																}
															}
															$notResponsiveCss = array_merge($notResponsiveCss, $objectCss);
														}
													}
												}
												
											}
										}
								}
							}
						}
					}
				
				}
			}
			
			//Combine Css
			$fonts_url = [];
			foreach ( $notResponsiveCss as $key => $font_url ) {
				if ( !is_array($font_url) && strpos ( $font_url, '@import url' ) !== FALSE ) {
					$fonts_url[] = $font_url;
					unset($notResponsiveCss[$key]);
				}
			}
			
			if ( !empty($fonts_url) ) {
				$unique_font = array_unique($fonts_url);
				$Make_CSS .= join("", $unique_font );
			}
			if ( !empty($notResponsiveCss) ) {
				$new_arr = [];
				array_walk_recursive($notResponsiveCss, function($v) use(&$new_arr){ $new_arr[] = $v; });
				$Make_CSS .= join("",$new_arr);
			}
			
			if ( !empty($md) ) {
				$Make_CSS .= join("",$md);
			}
			if ( !empty($sm) ) {
				$Make_CSS .= '@media (max-width: 1024px) {' . join("",$sm) . '}';
			}
			if ( !empty($xs) ) {
				$Make_CSS .= '@media (max-width: 767px) {' . join("",$xs) . '}';
			}
			
			return $Make_CSS;
		}
	}
	
	/*
	 * Condition attribute Style
	 */
	public function conditions_styling( $settings = [], $selectData=[], $key='', $indexStyle='' ){
		$check = true;
			
			$selectData = (array)$selectData;
			if (isset($selectData['condition']) && !empty($selectData['condition'])) {
					
				foreach($selectData['condition'] as $index => $data) {
					
					$previous_cond = $check;
					$objData = (array)$data;
					
					if( isset( $settings[ $objData['key'] ] ) ){
						
						if (isset($objData['relation']) && $objData['relation'] == '==' ) {
							
							if (isset($objData['value']) && (gettype($objData['value']) == 'string' || gettype($objData['value']) == 'number' || gettype($objData['value']) == 'boolean')) {
								
								if ( $settings[$objData['key']]['value'] === $objData['value']) {
									$check = true;
								}else if( gettype( $settings[$objData['key']]['value'] ) == 'object' || gettype( $settings[$objData['key']]['value'] ) == 'array' ){
									$select = false;
									
									if( (isset($settings[$objData['key']]['value']['md']) && $settings[$objData['key']]['value']['md'] == $objData['value']) || (isset($settings[$objData['key']]['value']['sm']) && $settings[$objData['key']]['value']['sm'] == $objData['value']) || (isset($settings[$objData['key']]['value']['xs']) && $settings[$objData['key']]['value']['xs'] == $objData['value']) ){
										$select = true;
									}
									
									if ( $select ) {
										$check = true;
									}else{
										$check = false;
									}
									
								} else {
									$check = false;
								}
								
							} else {
								$select = false;
								if(!empty($objData['value'])){
									foreach( $objData['value'] as $arrData ) {
										
										if ( isset($settings[ $objData['key'] ]['value']) && $settings[ $objData['key'] ]['value'] == $arrData) {
											$select = true;
										}
									}
								}
								
								if ($select) {
									$check = true;
								}else{
									$check = false;
								}
							}
							
						} else if ( isset($objData['relation']) && $objData['relation'] == '!=' ) {
							if (isset($objData['value']) && (gettype($objData['value']) == 'string' || gettype($objData['value']) == 'number' || gettype($objData['value']) == 'boolean') ) {
								$attr_key = explode(".", $objData['key']);
								if(count($attr_key)> 1){
									if(is_array($settings[$attr_key[0]]['value'])){
										$attr_key_value = $settings[$attr_key[0]]['value'][$attr_key[1]];
									}else{
										$attr_key_value = $settings[$attr_key[0]]['value'];
									}
								}else if(isset($settings[$attr_key[0]]['value'])){
									$attr_key_value = $settings[$attr_key[0]]['value'];
								}else{
									$attr_key_value = $attr_key[0];
								}
								
								if ($attr_key_value != $objData['value']) {
									$check = true;
								} else {
									$check = false;
								}
							} else {
								$_select = false;
								foreach( $objData['value'] as $arrData ) {
									if (isset($settings[$objData['key']]['value']) && $settings[$objData['key']]['value'] != $arrData) {
										$_select = true;
									}
								}
								if ($_select) {
									$check = true;
								}else{
									$check = false;
								}
							}
						}
					}
					if ($previous_cond == false) {
						$check = false;
					}
				}
			}
		
		return $check;
	}
	
	/*
	 * Object Field Check
	 */
	public function tp_objectField( $data ){
		$data = (array) $data;
		if (isset($data['openTypography']) && $data['openTypography']) {
			return [ 'data' => $this->cssTypography($data) , 'action' => "append" ]; //Typography
		}else if (isset($data['openBorder']) && $data['openBorder']) {
			return [ 'data' => $this->cssBorder($data) , 'action' => "append" ]; //Border
		}else if (isset($data['openShadow']) && $data['openShadow']) {
			return [ 'data' => $this->cssBoxShadow($data), 'action' => "append" ]; //BoxShadow
		}else if (isset($data['openBg']) && $data['openBg']) {
			return [ 'data' => $this->cssBackground($data), 'action' => "append" ]; //Background Color/Image/Video
		}else if (isset($data['openTransform']) && $data['openTransform']) {
			return [ 'data' => $this->cssTransform($data), 'action' => "append" ]; //Transform
		}else if (isset($data['openFilter']) && $data['openFilter']) {
			return [ 'data' => $this->cssFilter($data), 'action' => "append" ]; //cssFilter Ex. blur,contrast...
		}else if (isset($data['top']) || isset($data['left']) || isset($data['right']) || isset($data['bottom'])) {
			return [ 'data' => $this->cssDimension($data), 'action' => "replace" ]; //Css Dimension Ex.Padding/Margin...
		}else {
			return [ 'data' => '', 'action' => "append" ];
		}
	}
	
	public function replaceDimension($selector, $value){
		if( gettype($value) === 'string' && !empty($value) && strpos($value, ' ') !== false ){
				$dimValue = explode(' ',$value);
			
			if(strpos($selector,'--tpgb-padding-left') !== false ){
				if($dimValue[3] == '0'){
					$dimValue[3] = '0px';
				}
				$selector = str_replace('{{LEFT}}'.$value, $dimValue[3], $selector);
			}
			if(strpos($selector,'--tpgb-padding-right') !== false ){
				if($dimValue[1] == '0'){
					$dimValue[1] = '0px';
				}
				$selector = str_replace('{{RIGHT}}'.$value, $dimValue[1], $selector);
			}
			if(strpos($selector,'--tpgb-padding-top') !== false ){
				$selector = str_replace('{{TOP}}'.$value, $dimValue[0], $selector);
			}
			if(strpos($selector,'--tpgb-padding-bottom') !== false ){
				$selector = str_replace('{{BOTTOM}}'.$value, $dimValue[2], $selector);
			}
		}
		return $selector;
	}


	/*
	 * Single Field Check
	 */
	public function singleField($style, $blockID, $key, $value, $category, $repeater=''){
		$value = (gettype($value) === 'undefined' ? 'undefined' : gettype($value)) != 'object' ? $value : $this->tp_objectField($value)['data'];
		if (gettype($style) == 'string') {
			if ( !empty($style) ) {
				if ( $value != '' ) {
					
					$warpData = $this->replaceWarp($style, $blockID, $category);
					if( $repeater!='' ){
						$warpData = $this->replaceWarpItem($warpData, $repeater);
					}
					
					if (gettype($value) == 'boolean') {
						return [ $warpData ];
					} else {
						if (strpos($warpData, '{{') == -1 && strpos($warpData, '{') < 0) {
							return [ $warpData . $value];
						} else {
							return [ $this->replaceDimension( $this->replaceData($warpData, '{{' . $key . '}}', $value) , $value ) ];
						}
					}
				} else {
					return [];
				}
			} else {
				return [ $this->replaceWarp($value, $blockID, $category)]; // Custom CSS Field
			}
		} else {
			$output = [];
			if(!empty($style)){
				foreach($style as $sel) {
					array_push($output, $this->replaceData( $this->replaceWarp( $sel, $blockID, $category), '{{' . $key . '}}', $value));
				}
			}
			return $output;
		}
	}
	
	/*
	 * Replace Wrap
	 */
	public function replaceWarp($selector, $ID, $category){
		$selector = str_replace('{{PLUS_WRAP}}', '.'.($category ? $category : 'tpgb') . '-block-' . $ID , $selector);
		return str_replace('{{PLUS_BLOCK}}' , '.'.($category ? $category : 'tpgb') . '-wrap-' . $ID, $selector);
	}
	
	/*
	 * Replace Data
	 */
	public function replaceData($selector, $key, $value){
		return str_replace($key, $value, $selector);
	}
	
	/*
	 * Replace Repeater Wrap Item
	 */
	public function replaceWarpItem($selector, $ID){
		return str_replace('{{TP_REPEAT_ID}}', '.tp-repeater-item-' . $ID, $selector);
	}
	
	/*
	 * object Replace Data
	 */
	public function objectReplace( $warp, $value ){
		$output = '';
		foreach($value as $sel) {
			$output .= $sel . ';';
		}
		return $warp . '{' . $output . '}';
	}
	
	/*
	 * css Dimension Style
	 */
	public function cssDimension( $val ){
		$unit = (isset($val['unit']) && !empty($val['unit'])) ? $val['unit'] : 'px';
		$output ='';
		if( (isset($val['top']) && $val['top']!='') || (isset($val['right']) && $val['right']!='') || (isset($val['bottom']) && $val['bottom']!='') || (isset($val['left'])  && $val['left']!='') ){
			$output .= (!empty($val['top']) ? $val['top'].$unit : 0) . ' ' . (!empty($val['right']) ? $val['right'] . $unit : 0) . ' ' . (!empty($val['bottom']) ? $val['bottom'] . $unit : 0) .' ' . (!empty($val['left']) ? $val['left'] . $unit : 0);
		}
		return $output;
	}
	
	/*
	 * css Border Style
	 */
	public function cssBorder( $val ){
		if( !empty($val) ){
			$val['type'] = (isset($val['type']) && !empty($val['type'])) ? $val['type'] : "solid";
			$val['width'] = (isset($val['width']) && !empty($val['width'])) ? $val['width'] : [];
			$val['color'] = (isset($val['color']) && !empty($val['color'])) ? $val['color'] : '#000';
		
			$defaultCss = 'border-style: ' . ($val['type'] ? $val['type'] : 'solid'). ';';
			if(isset($val['disableWidthColor']) && !empty($val['disableWidthColor'])){
				
			}else{
				$defaultCss .= 'border-color: ' . ($val['color'] ? $val['color'] : '#000') . ';';
			}
			if (gettype($val['width']) === 'array') {
				$data = [ 'md' => [], 'sm' => [], 'xs' => [] ];
				$data = $this->_push($this->_customDevice($val['width'], 'border-width:{{key}};'), $data);
				array_push($data['md'], $defaultCss);
				return [ 'md' => $data['md'], 'sm' => $data['sm'], 'xs' => $data['xs'] ];
			}
		}
	}
	
	/*
	 * Css BoxShadow Style
	 */
	public function cssBoxShadow( $val ){
		$css='';
		if(!empty($val['openShadow']) && isset($val['globalShadow']) && !empty($val['globalShadow']) ){
			$gShadow = $val['globalShadow'];
			if (isset($val['typeShadow']) && $val['typeShadow'] == 'text-shadow') {
				return "text-shadow:var(--tpgb-BS". $gShadow . ");";
			}else if(isset($val['typeShadow']) && $val['typeShadow'] == 'drop-shadow'){
				return 'filter: drop-shadow(var(--tpgb-BS'. $gShadow . '));';
			}else{
				return "box-shadow:var(--tpgb-BS". $gShadow . ");";
			}
		}
		if (!empty($val['openShadow']) && isset($val['typeShadow']) && $val['typeShadow'] == 'text-shadow') {
			return $val['typeShadow'].':' . $val['horizontal'] . 'px ' . $val['vertical'] . 'px ' . $val['blur'] . 'px ' . $val['color'] . ';';
		}else if(!empty($val['openShadow']) && isset($val['typeShadow']) && $val['typeShadow'] == 'drop-shadow'){
			return 'filter: drop-shadow('. $val['horizontal'] . 'px ' . $val['vertical'] . 'px ' . $val['blur'] . 'px ' . $val['color'] .');';
		}else if (!empty($val['openShadow'])) {
			return 'box-shadow:' . ((isset($val['inset']) && !empty($val['inset'])) ? $val['inset'] : '') . ' ' . $val['horizontal'] . 'px ' . $val['vertical'] . 'px ' . $val['blur'] . 'px ' . $val['spread'] . 'px ' . $val['color'] . ';';
		} else {
			return;
		}
	}
	
	/*
	 * css Typography Style
	 */
	public function cssBackground( $val ){
		$background = '';
		if (isset($val['bgType']) && $val['bgType'] == '') {
			$val['bgDefaultColor'] = '';
		}
		if (isset($val['bgType']) && $val['bgType'] !== '') {
			$bgType = isset($val['bgType']) ? $val['bgType'] : '';
			$bgImage = isset($val['bgImage']) ? $val['bgImage'] : '';
			$bgimgPosition = isset($val['bgimgPosition']) ? $val['bgimgPosition'] : '';
			$bgimgAttachment = isset($val['bgimgAttachment']) ? $val['bgimgAttachment'] : '';
			$bgimgRepeat = isset($val['bgimgRepeat']) ? $val['bgimgRepeat'] : '';
			$bgimgSize = isset($val['bgimgSize']) ? $val['bgimgSize'] : '';
			$bgDefaultColor = isset($val['bgDefaultColor']) ? $val['bgDefaultColor'] : '';
			$bgGradient = isset($val['bgGradient']) ? $val['bgGradient'] : '';
			$bgimgPositionTablet = isset($val['bgimgPositionTablet']) ? $val['bgimgPositionTablet'] : '';
			$bgimgPositionMobile = isset($val['bgimgPositionMobile']) ? $val['bgimgPositionMobile'] : '';
			$bgimgRepeatTablet = isset($val['bgimgRepeatTablet']) ? $val['bgimgRepeatTablet'] : '';
			$bgimgRepeatMobile = isset($val['bgimgRepeatMobile']) ? $val['bgimgRepeatMobile'] : '';
			$bgimgSizeTablet = isset($val['bgimgSizeTablet']) ? $val['bgimgSizeTablet'] : '';
			$bgimgSizeMobile = isset($val['bgimgSizeMobile']) ? $val['bgimgSizeMobile'] : '';
			$background = $this->split_bg( $bgType, $bgImage, $bgimgPosition, $bgimgAttachment, $bgimgRepeat, $bgimgSize, $bgDefaultColor, $bgGradient, $bgimgPositionTablet, $bgimgPositionMobile, $bgimgRepeatTablet, $bgimgRepeatMobile, $bgimgSizeTablet, $bgimgSizeMobile);

			if (!empty($background) ) {
				return $background;
			}
			return [];
		} else {
			return '';
		}
	}
	
	/*
	 * Css Background Style
	 */
	public function split_bg($type, $image = [], $imgPosition= '', $imgAttachment ='', $imgRepeat='', $imgSize='', $DefaultColor='', $bgGradient='', $bgimgPositionTablet='', $bgimgPositionMobile='', $bgimgRepeatTablet='', $bgimgRepeatMobile='', $bgimgSizeTablet='', $bgimgSizeMobile='') {

		$dk_selectors = $DefaultColor ? 'background-color:' . $DefaultColor . ';' : '';
		
		$tb_selectors = '';
		$mb_selectors = '';

		if ($type == 'image') {
			$dk_selectors .= ((!empty($image) && isset($image['url'])) ? 'background-image: url(' . $image['url'] . ');' : '');

			$dk_selectors .= (!empty($imgPosition) ? 'background-position: '. $imgPosition .';' : '');
			$tb_selectors .= (!empty($bgimgPositionTablet) ? 'background-position: '. $bgimgPositionTablet .';' : '');
			$mb_selectors .= (!empty($bgimgPositionMobile) ? 'background-position: '. $bgimgPositionMobile .';' : '');

			$dk_selectors .= (!empty($imgAttachment) ? 'background-attachment: '. $imgAttachment .';' : '');
			$dk_selectors .= (!empty($imgRepeat) ? 'background-repeat: '. $imgRepeat . ';' : '');

			$tb_selectors .= (!empty($bgimgRepeatTablet) ? 'background-repeat: '. $bgimgRepeatTablet .';' : '');
			$mb_selectors .= (!empty($bgimgRepeatMobile) ? 'background-repeat:' . $bgimgRepeatMobile .';' : '');

			$dk_selectors .= (!empty($imgSize) ? 'background-size: '. $imgSize : '');
			$tb_selectors .= (!empty($bgimgSizeTablet) ? 'background-size: '. $bgimgSizeTablet .';' : '');
			$mb_selectors .= (!empty($bgimgSizeMobile) ? 'background-size: '. $bgimgSizeMobile .';' : '');
		} else if ($type == 'gradient') {
			if (!empty($bgGradient) && $bgGradient != '' && !is_array($bgGradient)) {
				$dk_selectors .= 'background-image : ' . $bgGradient. ';';
			}
		}

		$dk_res = [];
		$dk_res["md"] = $dk_selectors;
		$tab_res = [];
		$tab_res["sm"] = $tb_selectors;
		$mob_res = [];
		$mob_res["xs"] = $mb_selectors;

		$all_selectors =	array_merge($dk_res, $tab_res, $mob_res);

		return $all_selectors;
	}
	
	/*
	 * Css Typography Style
	 * @since 1.1.7
	 */
	public function cssTypography( $val ){
		$cssfont = '';
	
		if(isset($val['globalTypo'])){
			$css='';
			$typo = $val['globalTypo'];
			$css .= "font-family:var(--tpgb-T". $typo . "-font-family);";
			$css .= "font-weight:var(--tpgb-T". $typo . "-font-weight);";
			$css .= "font-style:var(--tpgb-T". $typo . "-font-style);";
			$css .= "font-size:var(--tpgb-T". $typo . "-font-size);";
			$css .= "line-height:var(--tpgb-T". $typo . "-line-height);";
			$css .= "letter-spacing:var(--tpgb-T". $typo . "-letter-spacing);";
			$css .= "text-transform:var(--tpgb-T". $typo . "-text-transform);";
			$css .= "text-decoration:var(--tpgb-T". $typo . "-text-decoration);";
			return $css;
		}
		if(isset($val['fontFamily']) && $val['fontFamily']!='' && isset($val['fontFamily']['family']) && $val['fontFamily']['family'] !='' && isset($val['fontFamily']['customFont']) && $val['fontFamily']['customFont'] !=''){
		}else if (isset($val['fontFamily']) && $val['fontFamily']!='' && isset($val['fontFamily']['family']) && $val['fontFamily']['family'] !='' && $this->google_font_load()) {
			if ( !in_array($val['fontFamily']['family'], ['Arial', 'Tahoma', 'Verdana', 'Helvetica', 'Times New Roman', 'Trebuchet MS', 'Georgia']) ) {
				$cssfont = '@import url(https://fonts.googleapis.com/css?family=' . preg_replace('/\s/i', '+', $val['fontFamily']['family']) . ':' . (isset($val['fontFamily']['fontWeight']) ? $val['fontFamily']['fontWeight'] : 400) . '&display=swap);';
			}
		}
		$data = [ 'md' => [], 'sm' => [], 'xs' => [] ];
		if (isset($val['size']) && $val['size']!='') {
			$data = $this->_push( $this->_device( $val['size'], 'font-size:{{key}}'), $data);
		}
		if (isset($val['height']) && $val['height']!='') {
			$data = $this->_push( $this->_device( $val['height'], 'line-height:{{key}}'), $data);
		}
		if (isset($val['spacing']) && $val['spacing']!='') {
			$data = $this->_push( $this->_device( $val['spacing'], 'letter-spacing:{{key}}'), $data);
		}
		$css ='';
		if( isset($val['fontFamily']) && $val['fontFamily'] != '' ){
			if( isset($val['fontFamily']['family']) && $val['fontFamily']['family'] != '' && isset($val['fontFamily']['customFont']) && $val['fontFamily']['customFont'] !=''){
				$css .= ( $val['fontFamily']['family'] ) ? "font-family:'" . $val['fontFamily']['family'] . "',Sans-serif;" : '';
			}else if( isset($val['fontFamily']['family']) && $val['fontFamily']['family'] != '' ){
				$css .= ( $val['fontFamily']['family'] ) ? "font-family:'" . $val['fontFamily']['family'] . ( $val['fontFamily']['type'] ? "'," . $val['fontFamily']['type'] : "'") .";" : '';
			}
			if(isset($val['fontFamily']['fontWeight'])){
				("string" == gettype($val['fontFamily']['fontWeight']) && preg_match("/[a-z]/i", $val['fontFamily']['fontWeight'])) ?
				$css .= 'font-weight:'. substr($val['fontFamily']['fontWeight'], 0, -1) .';font-style:italic;'
				:
				$css .= (isset($val['fontFamily']['fontWeight']) && !empty($val['fontFamily']['fontWeight'])) ? 'font-weight:' . $val['fontFamily']['fontWeight'] . ';' : '';
			}
		}
		if( isset($val['fontStyle']) && $val['fontStyle'] != 'default'){
			$css .= ($val['fontStyle']) ? 'font-style:' . $val['fontStyle'] . ';' : '';
		}
		if( isset($val['textTransform']) && !empty($val['textTransform'])){
			$css .= ($val['textTransform']) ? 'text-transform:' . $val['textTransform'] . ';' : '';
		}
		if( isset($val['textDecoration']) && $val['textDecoration'] != 'default'){
			$css .= ($val['textDecoration']) ? 'text-decoration:' . $val['textDecoration'] . ';' : '';
		}
		return [ 'md' => $data['md'], 'sm' => $data['sm'], 'xs' => $data['xs'], 'simple' => $css, 'font' => $cssfont ];
	}
	
	/*
	 * Css Filter Style
	 */
	public function cssFilter( $val ){
		if(!empty($val['openFilter'])){
			$filter ='';
			if(isset($val['blur']) && $val['blur']!=''){
				$filter .= ' blur('.$val['blur'].'px)';
			}
			if(isset($val['brightness']) && $val['brightness']!=''){
				$filter .= ' brightness('.$val['brightness'].'%)';
			}
			if(isset($val['contrast']) && $val['contrast']!=''){
				$filter .= ' contrast('.$val['contrast'].'%)';
			}
			if(isset($val['saturate']) && $val['saturate']!=''){
				$filter .= ' saturate('.$val['saturate'].'%)';
			}
			if(isset($val['hue']) && $val['hue']!=''){
				$filter .= ' hue-rotate('.$val['hue'].'deg)';
			}
			if($filter!=''){
				$filter = 'filter : '.$filter.';';
			}
			return $filter;
		}else{
			return;
		}
	}
	
	/*
	 * css Transform Style
	 */
	public function cssTransform(){
		if (!empty( $val['openTransform'] ) ) {
			
			$data = [ 'md' => [], 'sm' => [], 'xs' => [], ];
			$data = $this->_push( $this->_device( $val['perspective'], 'perspective({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['translateX'], 'translateX({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['translateY'], 'translateY({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['translateZ'], 'translateZ({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['scaleX'], 'scaleX({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['scaleY'], 'scaleY({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['scaleZ'], 'scaleZ({{key}})'), $data);
			$data = $this->_push( $this->_device( $val['rotateX'], 'rotateX({{key}}deg)'), $data);
			$data = $this->_push( $this->_device( $val['rotateY'], 'rotateY({{key}}deg)'), $data);
			$data = $this->_push( $this->_device( $val['rotateZ'], 'rotateZ({{key}}deg)'), $data);
			$data = $this->_push( $this->_device( $val['skewX'], 'skewX({{key}}deg)'), $data);
			$data = $this->_push( $this->_device( $val['skewY'], 'skewY({{key}}deg)'), $data);
				if(isset($data['md']) && !empty($data['md']) ){
					$data['md'] = [ 'transform : ' .join(" ",$data['md']).';' ];
				}
				if(isset($val['origin']) && $val['origin']!=''){
					$OriginCss = ($val['origin']!='custom' ? 'transform-origin:'.$val['origin'].';' : ((isset($val['customOrigin']) && !empty($val['customOrigin'])) ? 'transform-origin:'.$val['customOrigin'].';' : ''));
					array_push( $data['md'],$OriginCss );
				}
				if( isset($val['Transition']) && $val['Transition'] != '' ){
					$TransitionEff = ($val['Transition'] != 'custom' ? $val['Transition'] : ((isset($val['customTransition']) && $val['customTransition']!='') ? $val['customTransition'] : 'linear'));
					$TransitionDur = ($val['TraDuration']!='') ? $val['TraDuration'].'s' : '0.3s';
					$TransitionCss = ($val['Transition']!='none') ? 'transition : transform '.$TransitionDur.' '.$TransitionEff : '';
					array_push( $data['md'],$TransitionCss );
				}
				if( isset( $data['sm'] ) ){
					$data['sm'] = 'transform : ' .join(" ", $data['sm'] );
				}
				if( isset( $data['xs'] ) ){
					$data['xs'] = 'transform : ' .join(" ", $data['xs'] );
				}
				
			return [ 'md' => $data['md'], 'sm' => $data['sm'], 'xs' => $data['xs'], ];
		} else {
			return;
		}
	}
	
	/*
	 * custom Device Set
	 */
	public function _customDevice( $val, $selector ){
		$data = [];
		
		if ( $val && isset($val['md']) ) {
			if(gettype($val['md']) == 'object' || gettype($val['md']) == 'array' ){
				$val_md = is_array($val['md']) ? '' : $val['md'];
				$selectorReplaceSpl = explode(":", str_replace('{{key}}', $val_md, $selector) );
				//$selectorReplaceSpl2 = array_slice($selectorReplaceSpl, 2);
				$cssSyntax = $selectorReplaceSpl[0];
				$top = isset($val['md']['top']) ? $val['md']['top'] : '';
				$right = isset($val['md']['right']) ? $val['md']['right'] : '';
				$bottom = isset($val['md']['bottom']) ? $val['md']['bottom'] : '';
				$left = isset($val['md']['left']) ? $val['md']['left'] : '';
				if($top!=='' || $right!=='' || $bottom!=='' || $left!==''){
					$data['md'] = $cssSyntax . ':' . ($top ? $top : '0') . $val['unit'] . ' ' . ($right ? $right : '0') . $val['unit'] . ' ' . ($bottom ? $bottom : '0') . $val['unit'] . ' ' . ($left ? $left : '0') . $val['unit'];
				}
			}
		}
		if ( $val && isset($val['sm']) ) {
			if( gettype($val['sm']) == 'object' || gettype($val['sm']) == 'array' ){
				$val_sm = is_array($val['sm']) ? '' : $val['sm'];
				$selectorReplaceSpl3 = explode(":", str_replace('{{key}}', $val_sm, $selector) );
				//$selector$replace$spl4 = _slicedToArray(_selector$replace$spl3, 2),
				$cssSyntax = $selectorReplaceSpl3[0];
				$top = isset($val['sm']['top']) ? $val['sm']['top'] : '';
				$right = isset($val['sm']['right']) ? $val['sm']['right'] : '';
				$bottom = isset($val['sm']['bottom']) ? $val['sm']['bottom'] : '';
				$left = isset($val['sm']['left']) ? $val['sm']['left'] : '';
				if($top!=='' || $right!=='' || $bottom!=='' || $left!==''){
					$data['sm'] = $cssSyntax . ':' . ($top ? $top : '0') . $val['unit'] . ' ' . ($right ? $right : '0') . $val['unit'] . ' ' . ($bottom ? $bottom : '0') . $val['unit'] . ' ' . ($left ? $left : '0') . $val['unit'];
				}
			}
		}
		if ( $val && isset($val['xs']) ) {
			if( gettype($val['xs']) == 'object' || gettype($val['xs']) == 'array' ){
				$val_xs = is_array($val['xs']) ? '' : $val['xs'];
				$selectorReplaceSpl3 = explode(":", str_replace('{{key}}', $val_xs, $selector) );
				//$selector$replace$spl4 = _slicedToArray(_selector$replace$spl3, 2),
				$cssSyntax = $selectorReplaceSpl3[0];
				$top = isset($val['xs']['top']) ? $val['xs']['top'] : '';
				$right = isset($val['xs']['right']) ? $val['xs']['right'] : '';
				$bottom = isset($val['xs']['bottom']) ? $val['xs']['bottom'] : '';
				$left = isset($val['xs']['left']) ? $val['xs']['left'] : '';
				if($top!=='' || $right!=='' || $bottom!=='' || $left!==''){
					$data['xs'] = $cssSyntax . ':' . ($top ? $top : '0') . $val['unit'] . ' ' . ($right ? $right : '0') . $val['unit'] . ' ' . ($bottom ? $bottom : '0') . $val['unit'] . ' ' . ($left ? $left : '0') . $val['unit'];
				}
			}
		}
		
		return $data;
	}
	
	/*
	 * Devices
	 */
	public function _device( $val, $selector ){
		$val = (array) $val;
		$data = [];

		$unit = '';
		if(!empty($val) && isset($val['unit']) && !empty($val['unit']) && $val['unit']!='c'){
			$unit = $val['unit'];
		}
		if ($val && isset($val['md']) && $val['md']!='') {
			$data['md'] =  str_replace('{{key}}', $val['md'] . $unit, $selector);
		}
		if ($val && isset($val['sm']) && $val['sm']!='') {
			$data['sm'] = str_replace('{{key}}', $val['sm'] . $unit, $selector);
		}
		if ($val && isset($val['xs']) && $val['xs']!='') {
			$data['xs'] = str_replace('{{key}}', $val['xs'] . $unit, $selector);
		}
		return $data;
	}
	/*
	 * Array Device Push
	 */
	public function _push( $val, $data ){
		
		if (isset($val['md'])) {
			array_push( $data['md'], $val['md'] );
		}
		if (isset($val['sm'])) {
			array_push( $data['sm'], $val['sm'] );
		}
		if (isset($val['xs'])) {
			array_push( $data['xs'], $val['xs'] );
		}
		return $data;
	}
	
	/*
	 * minify Style Css
	 */
	public function minify_css( $css ){
		if(trim((string) $css) === "") return $css;
		return preg_replace(
			array(
				// Remove comment(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
				// Remove unused white-space(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
				// Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
				'#(?<=[\s:])(0)(cm|ex|in|mm|pc|pt|vh|vw|%)#si',
				// Replace `:0 0 0 0` with `:0`
				'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
				// Replace `background-position:0` with `background-position:0 0`
				'#(background-position):0(?=[;\}])#si',
				// Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
				'#(?<=[\s:,\-])0+\.(\d+)#s',
				// Minify string value
				'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
				'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
				// Minify HEX color code
				//'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
				// Replace `(border|outline):none` with `(border|outline):0`
				'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
				// Remove empty selector(s)
				'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
			),
			array(
				'$1',
				'$1$2$3$4$5$6$7',
				'$1',
				':0',
				'$1:0 0',
				'.$1',
				'$1$3',
				'$1$2$4$5',
				'$1$2$3',
				'$1:0',
				'$1$2'
			),
		$css);

	}
	
	/**
	 * @return bool|false|int
	 *
	 * get post id current page id
	 */
	private function is_post_id(){
		$post_id = get_the_ID();
		
		if (!$post_id) {
			return false;
		}
		return $post_id;
	}
}