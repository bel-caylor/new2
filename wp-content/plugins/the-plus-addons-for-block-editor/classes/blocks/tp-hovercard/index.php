<?php
/* Block : Hover Card
 * @since : 1.1.3
 */
defined( 'ABSPATH' ) || exit;
function tpgb_tp_hovercard_render_callback( $attributes, $content) {

	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$cardList = (!empty($attributes['cardList'])) ? $attributes['cardList'] : [];
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
    $output .= '<div class="tpgb-hovercard tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		$output .= '<div class="tpgb-hovercard-wrap tpgb-relative-block">';
			$output .= tpgb_get_html_structure($attributes,'attr');
		$output .= "</div>";
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */

function tpgb_tp_hovercard() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'cardList' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'openTag' => [
							'type' => 'string',
							'default' => 'none',
						],
						'closeTag' => [
							'type' => 'string',
							'default' => 'none',
						],
						'className' => [
							'type' => 'string',
							'default' => '',
						],
						'content' => [
							'type' => 'string',
							'default' => 'none',
						],
						'aLink' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'cntText' => [
							'type' => 'string',
							'default' => '',
						],
						'cntImg' => [
							'type' => 'object',
							'default' => [
								'url' => '',
							],
						],
						'position' => [
							'type' => 'string',
							'default' => 'relative',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {position:{{position}};}',
								],
							],
						],
						'display' => [
							'type' => 'string',
							'default' => 'initial',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {display:{{display}};}',
								],
							],
						],
						'flexdir' => [
							'type' => 'string',
							'default' => 'unset',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'display', 'relation' => '==', 'value' => ['flex','inline-flex']]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {flex-direction:{{flexdir}};}',
								],
							],
						],
						'showAlign' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'Align' => [
							'type' => 'string',
							'default' => 'center',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {text-align:{{Align}};}',
								],
							],
						],
						'Alignitem' => [
							'type' => 'string',
							'default' => 'center',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'display', 'relation' => '==', 'value' => ['flex','inline-flex']]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {align-items:{{Alignitem}};}',
								],
							],
						],
						'justicnt' => [
							'type' => 'string',
							'default' => 'center',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'display', 'relation' => '==', 'value' => ['flex','inline-flex']]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {justify-content:{{justicnt}};}',
								],
							],
						],
						'verAlign' => [
							'type' => 'string',
							'default' => 'middle',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'display', 'relation' => '==', 'value' => ['flex','inline-flex']]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {vertical-align:{{verAlign}};}',
								],
							],
						],
						'margin' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {margin:{{margin}};}',
								],
							],
						],
						'padding' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {padding:{{padding}};}',
								],
							],
						],
						'Hwoption' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'width' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {width:{{width}};}',
								],
							],
						],
						'minWidth' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {min-width:{{minWidth}};}',
								],
							],
						],
						'height' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {height:{{height}};}',
								],
							],
						],
						'minheight' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {min-height:{{minheight}};}',
								],
							],
						],
						'left' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'leftoffset' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 10,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [ (object) ['key' => 'left', 'relation' => '==', 'value' => true] ],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {left:{{leftoffset}};}',
								],
							],
						],
						'right' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'rightoffset' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [ (object) ['key' => 'right', 'relation' => '==', 'value' => true] ],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {right:{{rightoffset}};}',
								],
							],
						],
						'top' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'topoffset' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 10,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [ (object) ['key' => 'top', 'relation' => '==', 'value' => true] ],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {top:{{topoffset}};}',
								],
							],
						],
						'bottom' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'bottomoffset' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [ (object) ['key' => 'bottom', 'relation' => '==', 'value' => true] ],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {bottom:{{bottomoffset}};}',
								],
							],
						],
						'zindex' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {z-index:{{zindex}};}',
								],
							],
						],
						'overflow' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {overflow:{{overflow}};}',
								],
							],
						],
						'visibility' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {visibility:{{visibility}};}',
								],
							],
						],
						'normalBg' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'normalBorder' => [
							'type' => 'object',
							'default' => (object) [
								'openBorder' => 0,	
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'normalBradius' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 0,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {border-radius:{{normalBradius}};}',
								],
							],
						],
						'normalBshadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'transition' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {-webkit-transition:{{transition}};-moz-transition: {{transition}};-o-transition: {{transition}};-ms-transition: {{transition}}; }',
								],
							],
						],
						'transform' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} { transform: {{transform}};-ms-transform: {{transform}};-moz-transform: {{transform}};-webkit-transform: {{transform}}; }',
								],
							],
						],
						'opacity' => [
							'type' => 'string',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {opacity:{{opacity}};}',
								],
							],
						],
						'customHvr' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'Hvrclass' => [
							'type' => 'string',
							'default' => '',
						],
						'hoverBg' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'HvrBorder' => [
							'type' => 'object',
							'default' => (object) [
								'openBorder' => 0,	
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'HvrBradius' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 0,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover {border-radius:{{HvrBradius}};}',
								],
							],
						],
						'HvrBshadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'Hvrtransition' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover {-webkit-transition:{{Hvrtransition}};-moz-transition: {{Hvrtransition}};-o-transition: {{Hvrtransition}};-ms-transition: {{Hvrtransition}}; }',
								],
							],
						],
						'Hvrtransform' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover { transform: {{Hvrtransform}};-ms-transform: {{Hvrtransform}};-moz-transform: {{Hvrtransform}};-webkit-transform: {{Hvrtransform}}; }',
								],
							],
						],
						'Hvropacity' => [
							'type' => 'string',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'customHvr', 'relation' => '==', 'value' => false]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover {opacity:{{Hvropacity}};}',
								],
							],
						],
						'txtTypo' => [
							'type' => 'object',
							'default' => (object) [
								'openTypography' => 0,
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'text' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'txtcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'text' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {color : {{txtcolor}}; }',
								],
							],
						],
						'txtShadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
								'typeShadow' => 'text-shadow',
								'horizontal' => 2,
								'vertical' => 3,
								'blur' => 2,
								'color' => "rgba(0,0,0,0.5)",
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'text' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'customtxtHvr' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'txtHvrclass' => [
							'type' => 'string',
							'default' => '',
						],
						'txtHvrcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'text' ],
										['key' => 'customtxtHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover{color : {{txtHvrcolor}}; }',
								],
							],
						],
						'txtHvrShadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
								'typeShadow' => 'text-shadow',
								'horizontal' => 2,
								'vertical' => 3,
								'blur' => 2,
								'color' => "rgba(0,0,0,0.5)",
							],
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'text' ],
										['key' => 'customtxtHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'imgWidth' => [
							'type' => 'string',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} img{width:{{imgWidth}};}',
								],
							],
						],
						'imgmaxWidth' => [
							'type' => 'string',
							'default' => (object) [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} img{max-width:{{imgmaxWidth}};}',
								],
							],
						],
						'imgBorder' => [
							'type' => 'object',
							'default' => (object) [
								'openBorder' => 0,	
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'imgBradius' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 0,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {border-radius:{{imgBradius}};}',
								],
							],
						],
						'imgBShadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}',
								],
							],
						],
						'imgopacity' => [
							'type' => 'string',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {opacity:{{imgopacity}};}',
								],
							],
						], 
						'customimgHvr' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'Hvrimgclass' => [
							'type' => 'string',
							'default' => '',	
						],
						'HvrimgBorder' => [
							'type' => 'object',
							'default' => (object) [
								'openBorder' => 0,	
							],
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ],
										['key' => 'customimgHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'HvrimgBradius' => [
							'type' => 'object',
							'default' => (object) [ 
								'md' => 0,
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ],
										['key' => 'customimgHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover {border-radius:{{HvrimgBradius}};}',
								],
							],
						],
						'HvrimgBShadow' => [
							'type' => 'object',
							'default' => (object) [
								'openShadow' => 0,
							],
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ],
										['key' => 'customimgHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover',
								],
							],
						],
						'hvrimgopacity' => [
							'type' => 'string',
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'content', 'relation' => '==', 'value' => 'img' ],
										['key' => 'customimgHvr', 'relation' => '==', 'value' => false]
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover {opacity:{{hvrimgopacity}};}',
								],
							],
						],
					],
				], 
				'default' => [
					[
						"_key" => '0',
						'openTag' => 'div',
						'content' => 'none',
						'closeTag' => 'close',
						'cntText' => '',
						'position' => 'relative',
						'txtTypo' => ['openTypography' => 0 ],
						'normalBshadow' => ['openShadow' => 0],
						'HvrBshadow' => ['openShadow' => 0],
						'txtShadow' => ['openShadow' => 0],
						'txtHvrShadow' => ['openShadow' => 0],
						'imgBShadow' => ['openShadow' => 0],
						'HvrimgBShadow' =>  ['openShadow' => 0],
					],
				],
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-hovercard', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_hovercard_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_hovercard' );

function tpgb_get_html_structure($attr,$load=''){
	$tagname = (isset($_POST['tagname']) && !empty($_POST['tagname'])) ? map_deep( wp_unslash( $_POST['tagname'] ), 'sanitize_text_field' ) : $attr['cardList'];
	$block_id = (isset($_POST['block_id']) && !empty($_POST['block_id'])) ? sanitize_key($_POST['block_id']) : $attr['block_id'];
	
	global $post;

	$html= '';
	$i = 0;

	if(empty($tagname) && $load==''){
		exit();
	}else if(empty($tagname) && $load=='attr'){
		return null;
	}

	$cssStyle = $dyImgUrl = '';
	foreach($tagname as $item){

		//Set OpenTag
		$open_tag='';
		if(!empty($item['openTag']) && $item['openTag']!='none'){
			$open_tag = $item['openTag'];
		}

		//Set CloseTag
		$close_tag='';
		if(!empty($item['closeTag']) && $item['closeTag']=='close'){
			$close_tag = $open_tag;
		}else if(!empty($item['closeTag']) && $item['closeTag']!='close' && $item['closeTag']!='none'){
			$close_tag = $item['closeTag'];
		}

		//Set Anchor Tag Link
		$linkattr = ''; 
		
		if(!empty($item['openTag']) && $item['openTag']=="a"){
			
			if ( !empty( $item['aLink']) ) {	
				if(class_exists('Tpgbp_Pro_Blocks_Helper') && isset($item['aLink']['dynamic'])){
					$linkattr .= 'href = "'.esc_url(Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['aLink'])).'" ';
				}else{
					if( !empty($item['aLink']['url']) ){
						$linkattr .= 'href = "'.esc_url($item['aLink']['url']).'" ';
					}
				}
				$linkattr .= 'target = "'.(($item['aLink']['target']!='') ? '_blank' : '').'" ';
				$linkattr .= 'rel = "'.(($item['aLink']['nofollow']!='') ? 'nofollow' : '').'" ';
				$linkattr .= Tp_Blocks_Helper::add_link_attributes($item['aLink']);
			}									
		}

		$uniClass = '';
		//Set Backgorund Dynamic
		if(isset($item['normalBg']) && !empty($item['normalBg']) && $item['normalBg']['openBg'] == 1 && $item['normalBg']['bgType']== 'image' && isset($item['normalBg']['bgImage']['dynamic']) && isset($item['normalBg']['bgImage']['dynamic']['dynamicUrl']) ){
			if(isset($post->ID) && !empty($post->ID)){
				$uniClass .= ' tpgb-qupost-'.esc_attr($post->ID).'';
				if( class_exists('Tpgbp_Pro_Blocks_Helper') ) {
					$dyImgUrl .= Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['normalBg']['bgImage']);
					$cssStyle .= '.tpgb-block-'.esc_attr($block_id).'  .tpgb-qupost-'.esc_attr($post->ID).'.tp-repeater-item-'.esc_attr($item ['_key']).'{ background-image : url('.esc_url($dyImgUrl ).') }';
				}
			}
		}

		//Output Html
		if(!empty($open_tag)){ 
			$html .= '<'.esc_attr($open_tag).' class="tp-repeater-item-'.esc_attr($item ['_key']).' '.$uniClass.' '.(!empty($item['className']) ? esc_attr($item['className']) : '' ).' '.(!empty($item['Hvrclass'] ) ? esc_attr($item['Hvrclass']) : '' ).' '.(($item['content'] == 'text' && !empty($item['customtxtHvr']) && $item['txtHvrclass'] != '') ? esc_attr($item['txtHvrclass']) : '').'  '.(($item['content'] == 'img' && !empty($item['customimgHvr']) && $item['Hvrimgclass'] != '') ? esc_attr($item['Hvrimgclass']) : '').'  " '.($item['openTag']=="a" ? $linkattr :'').' >';
		}
		// Content
		if(!empty($item['content']) && $item['content'] != 'none'){
			if($item['content']=='text' && !empty($item['cntText'])){
				$html .= $item['cntText'];
			}
			
			if($item['content']=='img' && !empty($item['cntImg']['url'])){
				$cntImg = $item['cntImg'];
				if(isset($cntImg['id']) && !empty($cntImg['id'])){
					$cntUrl = wp_get_attachment_image( $cntImg['id'], 'full');
				}else{
					if(isset($item['cntImg']['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) {
						$dyImgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['cntImg']);
						$cntUrl = '<img src="'.esc_url($dyImgUrl).'" alt="'.esc_attr__('card-Img','tpgb').'" />';
					}else{
						$cntUrl = '<img src="'.esc_url($item['cntImg']['url']).'" alt="'.esc_attr__('card-Img','tpgb').'" />';
					}
					
				}
				$html .= $cntUrl; 
			}
			
			if($item['content']=='html' && !empty($item['cnthtml'])){
				$html .= wp_kses_post($item['cnthtml']) ;
			} 
		}

		if(!empty($item['closeTag']) && $item['closeTag']!='none'){
			$html .= '</'.esc_attr($close_tag).'>';
		}

		// Add css For Custom Hover Class For OpenTag
		$cssStyle .= tpgb_hovercard_style($item,$block_id);
		$i++;
	}
				
	if(!empty($cssStyle)){
		$html .= '<style>';
		$html .= $cssStyle;
		$html .= '</style>';
	}
	if($load==''){
		echo $html;
		exit();
	}else{
		return $html;
	}
}

add_action('wp_ajax_get_html_structure', 'tpgb_get_html_structure');

//Fuction to get Dynamic css for fornt end and backend
function tpgb_hovercard_style($item,$block_id){
	$selector = '';
	$css = '';
		if(!empty($item['customHvr']) && !empty($item['Hvrclass'])){
			$selector = '.tpgb-block-'.esc_attr($block_id).' .'.esc_attr($item['Hvrclass']).':hover .tp-repeater-item-'.esc_attr($item ['_key']);
			if(!empty($item['hoverBg']) && $item['hoverBg']['openBg'] == 1) {
				if($item['hoverBg']['bgType']=='color'){
					if(!empty($item['hoverBg']['bgDefaultColor'])){
						$css .=  $selector.'{background-color:'.esc_attr($item['hoverBg']['bgDefaultColor']).';}';
					}										
				}else if( $item['hoverBg']['bgType']=='gradient'){
					if(!empty($item['hoverBg']['bgGradient'] ['color1']) && !empty($item['hoverBg']['bgGradient']['color2']) && $item['hoverBg']['bgGradient']['type']=='linear' ){
						$css .=$selector.'{ background-image:linear-gradient('.esc_attr($item['hoverBg']['bgGradient']['direction']).'deg ,'.esc_attr($item['hoverBg']['bgGradient'] ['color1']).' '.esc_attr($item['hoverBg']['bgGradient']['start']).'%, '.esc_attr($item['hoverBg']['bgGradient'] ['color2']).' '.esc_attr($item['hoverBg']['bgGradient']['stop']).'% );';
					}else if(!empty($item['hoverBg']['bgGradient']['color1']) && !empty($item['hoverBg']['bgGradient']['color2']) && $item['hoverBg']['bgGradient']['type']=='radial'){
						$css .=$selector.'{ background-image:radial-gradient( circle at '.esc_attr($item['hoverBg']['bgGradient']['radial']).' ,'.esc_attr($item['hoverBg']['bgGradient'] ['color1']).' '.esc_attr($item['hoverBg']['bgGradient']['start']).'%, '.esc_attr($item['hoverBg']['bgGradient'] ['color2']).' '.esc_attr($item['hoverBg']['bgGradient']['stop']).'% );';
					}
				}else if(!empty($item['hoverBg']) && $item['hoverBg']['bgType']=='image'){
					if(!empty($item['hoverBg']['bgImage']['url'])){
						$css .=$selector.'{background-image:url('.esc_url($item['hoverBg']['bgImage']['url']).');background-position:'.esc_attr($item['hoverBg']['bgImage']['bgimgPosition']).';background-attachment:'.esc_attr($item['hoverBg']['bgImage']['bgimgAttachment']).';background-repeat:'.esc_attr($item['hoverBg']['bgImage']['bgimgRepeat']).';}';	
					}
				}
			}
			if(!empty($item['HvrBorder']) && $item['HvrBorder']['openBorder'] == 1) {
				$css .=$selector.'{border-style:'.esc_attr($item['HvrBorder']['type']).';border-color:'.esc_attr($item['HvrBorder']['color']).';}';
				if($item['HvrBorder'] !='' && $item['HvrBorder']['width'] !='' && $item['HvrBorder']['width']['md'] !=''){
					$css .=$selector.'{border-width:'.esc_attr($item['HvrBorder']['width']['md']['top']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['md']['right']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['md']['bottom']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['md']['left']).esc_attr($item['HvrBorder']['width']['unit']).'}';
				}
				 else if($item['HvrBorder'] !='' && $item['HvrBorder']['width'] !='' && $item['HvrBorder']['width']['sm'] !=''){
				 	$css .= '@media (max-width: 1024px){' .$selector.'{border-width:'.esc_attr($item['HvrBorder']['width']['sm']['top']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['sm']['right']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['sm']['bottom']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['sm']['left']).esc_attr($item['HvrBorder']['width']['unit']).'}}';
				 }else if($item['HvrBorder'] !='' && $item['HvrBorder']['width'] !='' && $item['HvrBorder']['width']['xs'] !=''){
				 	$css .= '@media (max-width: 767px){' .$selector.'{border-width:'.esc_attr($item['HvrBorder']['width']['xs']['top']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['xs']['right']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['xs']['bottom']).esc_attr($item['HvrBorder']['width']['unit']).' '.esc_attr($item['HvrBorder']['width']['xs']['left']).esc_attr($item['HvrBorder']['width']['unit']).'}}';
				}
			}
			if(!empty($item['HvrBradius'])){										
				if(!empty($item['HvrBradius']['md']['top']) || !empty($item['HvrBradius']['md']['right']) || !empty($item['HvrBradius']['md']['bottom']) || !empty($item['HvrBradius']['md']['left'])){
					$css .=$selector.'{border-radius: '.esc_attr($item['HvrBradius']['md']['top']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['md']['right']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['md']['bottom']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['md']['left']).esc_attr($item['HvrBradius']['unit']).';}';
				}
				if(!empty($item['HvrBradius']['sm']['top']) || !empty($item['HvrBradius']['sm']['right']) || !empty($item['HvrBradius']['sm']['bottom']) || !empty($item['HvrBradius']['sm']['left'])){
					$css .= '@media (max-width: 1024px){' .$selector.'{border-radius:'.esc_attr($item['HvrBradius']['sm']['top']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['sm']['right']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['sm']['bottom']).esc_attr($item['HvrBradius']['unit']).esc_attr($item['HvrBorder']['sm']['left']).esc_attr($item['HvrBorder']['unit']).'}}';
				}
				if(!empty($item['HvrBradius']['xs']['top']) || !empty($item['HvrBradius']['xs']['right']) || !empty($item['HvrBradius']['xs']['bottom']) || !empty($item['HvrBradius']['xs']['left'])){
					$css .= '@media (max-width: 767px){' .$selector.'{border-radius:'.esc_attr($item['HvrBradius']['xs']['top']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['xs']['right']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBradius']['xs']['bottom']).esc_attr($item['HvrBradius']['unit']).' '.esc_attr($item['HvrBorder']['xs']['left']).esc_attr($item['HvrBorder']['unit']).'}}';
				}		
			}
			if(!empty($item['HvrBshadow'])){
				if($item['HvrBshadow']['openShadow'] == true){
					if(!empty($item['HvrBshadow']['inset'])){
						$css .=$selector.'{ box-shadow: inset '.esc_attr($item['HvrBshadow']['horizontal']).'px '.esc_attr($item['HvrBshadow']['vertical']).'px '.esc_attr($item['HvrBshadow']['blur']).'px '.esc_attr($item['HvrBshadow']['spread']).'px '.esc_attr($item['HvrBshadow']['color']).' }';
					}else{
						$css .=$selector.'{ box-shadow: '.esc_attr($item['HvrBshadow']['horizontal']).'px '.esc_attr($item['HvrBshadow']['vertical']).'px '.esc_attr($item['HvrBshadow']['blur']).'px '.esc_attr($item['HvrBshadow']['spread']).'px '.esc_attr($item['HvrBshadow']['color']).' }';
					}
				}
			}
			if(!empty($item['Hvrtransition'])){
				$css .= $selector.'{ -webkit-transition: '.esc_attr($item['Hvrtransition']).';-moz-transition: '.esc_attr($item['Hvrtransition']).';-o-transition:'.esc_attr($item['Hvrtransition']).';-ms-transition: '.esc_attr($item['Hvrtransition']).';}';
			}
			if(!empty($item['Hvrtransform'])){
				$css .= $selector.'{ transform: '.esc_attr($item['Hvrtransform']).';-ms-transform: '.esc_attr($item['Hvrtransform']).';-moz-transform:'.esc_attr($item['Hvrtransform']).';-webkit-transform: '.esc_attr($item['Hvrtransform']).';}';
			}
			if(!empty($item['Hvropacity'])){
				$css .= $selector.'{ opacity: '.esc_attr($item['Hvropacity']).';}';
			}
		}
		if($item['content'] == 'text' && !empty($item['customtxtHvr']) && !empty($item['txtHvrclass'])){
			$selector = '.tpgb-block-'.esc_attr($block_id).' .'.esc_attr($item['txtHvrclass']).':hover .tp-repeater-item-'.esc_attr($item ['_key']);
			if(!empty($item['txtHvrcolor'])){
				$css .= $selector.'{ color: '.esc_attr($item['txtHvrcolor']).';}';
			}
			if(!empty($item['txtHvrShadow']) && !empty($item['txtHvrShadow']['openShadow']) ){
				$css .= $selector.'{ text-shadow: '.esc_attr($item['txtHvrShadow']['horizontal']).'px '.esc_attr($item['txtHvrShadow']['vertical']).'px '.esc_attr($item['txtHvrShadow']['blur']).'px '.esc_attr($item['txtHvrShadow']['color']).'}';
			}
		}

		if($item['content'] == 'img' && !empty($item['customimgHvr']) && !empty($item['Hvrimgclass'])){
			$selector = '.tpgb-block-'.esc_attr($block_id).' .'.esc_attr($item['Hvrimgclass']).':hover .tp-repeater-item-'.esc_attr($item ['_key']);
			if(!empty($item['HvrimgBorder']) && $item['HvrimgBorder']['openBorder'] == 1) {
				$css .= $selector.'{border-style:'.esc_attr($item['HvrimgBorder']['type']).';border-color:'.esc_attr($item['HvrimgBorder']['color']).';}';
				if($item['HvrimgBorder'] !='' && $item['HvrimgBorder']['width'] !='' && $item['HvrimgBorder']['width']['md'] !=''){
					$css .= $selector.'{border-width:'.esc_attr($item['HvrimgBorder']['width']['md']['top']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['md']['right']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['md']['bottom']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['md']['left']).esc_attr($item['HvrimgBorder']['width']['unit']).'}';
				}
				 else if($item['HvrimgBorder'] !='' && $item['HvrimgBorder']['width'] !='' && $item['HvrimgBorder']['width']['sm'] !=''){
				 	$css .= '@media (max-width: 1024px){' .$selector.'{border-width:'.esc_attr($item['HvrimgBorder']['width']['sm']['top']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['sm']['right']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['sm']['bottom']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['sm']['left']).esc_attr($item['HvrimgBorder']['width']['unit']).'}}';
				 }else if($item['HvrimgBorder'] !='' && $item['HvrimgBorder']['width'] !='' && $item['HvrimgBorder']['width']['xs'] !=''){
				 	$css .= '@media (max-width: 767px){' .$selector.'{border-width:'.esc_attr($item['HvrimgBorder']['width']['xs']['top']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['xs']['right']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['xs']['bottom']).esc_attr($item['HvrimgBorder']['width']['unit']).' '.esc_attr($item['HvrimgBorder']['width']['xs']['left']).esc_attr($item['HvrimgBorder']['width']['unit']).'}}';
				}
			}
			if(!empty($item['HvrimgBradius'])){										
				if(!empty($item['HvrimgBradius']['md']['top']) || !empty($item['HvrimgBradius']['md']['right']) || !empty($item['HvrimgBradius']['md']['bottom']) || !empty($item['HvrimgBradius']['md']['left'])){
					$css .= $selector.'{border-radius: '.esc_attr($item['HvrimgBradius']['md']['top']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['md']['right']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['md']['bottom']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['md']['left']).esc_attr($item['HvrimgBradius']['unit']).';}';
				}
				if(!empty($item['HvrimgBradius']['sm']['top']) || !empty($item['HvrimgBradius']['sm']['right']) || !empty($item['HvrimgBradius']['sm']['bottom']) || !empty($item['HvrimgBradius']['sm']['left'])){
					$css .= '@media (max-width: 1024px){' .$selector.'{border-radius:'.esc_attr($item['HvrimgBradius']['sm']['top']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['sm']['right']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['sm']['bottom']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrBorder']['sm']['left']).esc_attr($item['HvrBorder']['unit']).'}}';
				}
				if(!empty($item['HvrimgBradius']['xs']['top']) || !empty($item['HvrimgBradius']['xs']['right']) || !empty($item['HvrimgBradius']['xs']['bottom']) || !empty($item['HvrimgBradius']['xs']['left'])){
					$css .= '@media (max-width: 767px){' .$selector.'{border-radius:'.esc_attr($item['HvrimgBradius']['xs']['top']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['xs']['right']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrimgBradius']['xs']['bottom']).esc_attr($item['HvrimgBradius']['unit']).' '.esc_attr($item['HvrBorder']['xs']['left']).esc_attr($item['HvrBorder']['unit']).'}}';
				}		
			}
			if(!empty($item['HvrimgBShadow'])){
				if($item['HvrimgBShadow']['openShadow'] == true){
					if(!empty($item['HvrimgBShadow']['inset'])){
						$css .= $selector.'{ box-shadow: inset '.esc_attr($item['HvrimgBShadow']['horizontal']).'px '.esc_attr($item['HvrimgBShadow']['vertical']).'px '.esc_attr($item['HvrimgBShadow']['blur']).'px '.esc_attr($item['HvrimgBShadow']['spread']).'px '.esc_attr($item['HvrimgBShadow']['color']).' }';
					}else{
						$css .= $selector.'{ box-shadow: '.esc_attr($item['HvrimgBShadow']['horizontal']).'px '.esc_attr($item['HvrimgBShadow']['vertical']).'px '.esc_attr($item['HvrimgBShadow']['blur']).'px '.esc_attr($item['HvrimgBShadow']['spread']).'px '.esc_attr($item['HvrimgBShadow']['color']).' }';
					}
				}
			}
			if(!empty($item['hvrimgopacity'])){
				$css .= $selector.'{ opacity: '.esc_attr($item['hvrimgopacity']).';}';
			}
		}
		
	return $css;
}