<?php
/* Block : Stylist List
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_stylist_list_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $alignment = (!empty($attributes['alignment'])) ? $attributes['alignment'] : 'align-left';
    $iconAlignment = (!empty($attributes['iconAlignment'])) ? $attributes['iconAlignment'] : false;
    $listsRepeater = (!empty($attributes['listsRepeater'])) ? $attributes['listsRepeater'] : [];
    $hover_bg_style = (!empty($attributes['hover_bg_style'])) ? $attributes['hover_bg_style'] : false;
    $pinAlignment = (!empty($attributes['pinAlignment'])) ? $attributes['pinAlignment'] : 'right';
    $hoverInverseEffect = (!empty($attributes['hoverInverseEffect'])) ? $attributes['hoverInverseEffect'] : false;
	
    $readMoreToggle = (!empty($attributes['readMoreToggle'])) ? $attributes['readMoreToggle'] : false;
    $showListToggle = (!empty($attributes['showListToggle'])) ? (int)$attributes['showListToggle'] : 3;
    $readMoreText = (!empty($attributes['readMoreText'])) ? $attributes['readMoreText'] : '';
	$readLessText = (!empty($attributes['readLessText'])) ? $attributes['readLessText'] : '';
	$effectArea = (!empty($attributes['effectArea'])) ? $attributes['effectArea'] : 'individual';
	$globalId = (!empty($attributes['globalId'])) ? $attributes['globalId'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	
	$alignattr ='';
	if($alignment!==''){
		$alignattr .= (!empty($alignment['md'])) ? ' align-'.$alignment['md'] : ' align-left';
		$alignattr .= (!empty($alignment['sm'])) ? ' tablet-align-'.$alignment['sm'] : '';
		$alignattr .= (!empty($alignment['xs'])) ? ' mobile-align-'.$alignment['xs'] : '';
	}
	
	$iconalignattr = (!empty($iconAlignment)) ? ' d-flex-center' : ' d-flex-top';
	
	$hoverInvertClass = $inverAttr ='';
	if( $hoverInverseEffect ){
		$hoverInvertClass .= ($effectArea == 'global') ? ' hover-inverse-effect-global' : ' hover-inverse-effect';
		$hoverInvertClass .= ($effectArea == 'global' && !empty($globalId) ) ? ' hover-'.$globalId : '' ;
		$inverAttr .= ($effectArea == 'global' && !empty($globalId) ) ? 'data-hover-inverse = hover-'.esc_attr($globalId).'' : '';
	}
	
	$i=0;$j=0;
	
    $output .= '<div class="tpgb-stylist-list tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($alignattr).' '.esc_attr($blockClass).' '.esc_attr($hoverInvertClass).'" '.$inverAttr.' >';
		if(!empty($listsRepeater)){
		
			if($hover_bg_style){
			
				$output .= '<div class="tpgb-bg-hover-effect">';
					foreach ( $listsRepeater as $index => $item ) :
						$active='';
						if($j==0){
							$active=' active';
						}
						$output .= '<div class="hover-item-content tp-repeater-item-'.esc_attr($item['_key']).esc_attr($active).'"></div>';
						$j++;
					endforeach;
				$output .= "</div>";
			}
			
			$output .= '<div class="tpgb-icon-list-items'.esc_attr($iconalignattr).'">';
				foreach ( $listsRepeater as $index => $item ) :
					
					$i++;
					$active_class=$descurl_open=$descurl_close='';
					if($i==1){
						$active_class='active';
					}
					//Url
					if(!empty($item['descurl']) && !empty($item['descurl']['url'])){
						$descurl = (isset($item['descurl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['descurl']) : (!empty($item['descurl']['url']) ? $item['descurl']['url'] : '');
						$target = ($item['descurl']['target']!='') ? 'target="_blank"' : '';
						$nofollow = ($item['descurl']['nofollow']!='') ? 'rel="nofollow"' : '';
						$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['descurl']);
						$descurl_open ='<a href="'.esc_url($descurl).'" '.$target.' '.$nofollow.' '.$link_attr.'>';
						$descurl_close ='</a>';
					}
					
					//Icon
					$icons ='';
					if(!empty($item['selectIcon'])){
						$icons .= '<div class="tpgb-icon-list-icon">';
							if($item['selectIcon']=='fontawesome' && !empty($item['iconFontawesome'])){ 
								$icons .='<i class="list-icon '.$item['iconFontawesome'].'" aria-hidden="true"></i>';
							}else if($item['selectIcon'] == 'img' && !empty($item['iconImg']['url'])){
								$imgSrc = '';
								if(!empty($item['iconImg']) && !empty($item['iconImg']['id'])){
									$imgSrc = wp_get_attachment_image($item['iconImg']['id'] , 'full');
								}else if( !empty($item['iconImg']['url']) ){
									$imgurl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['iconImg']);
									$imgSrc = '<img src="'.esc_url($imgurl).'"  alt="'.esc_attr__('icon-img','tpgbp').'" />';
								}
								$icons .= $imgSrc;
							} 
						$icons .= '</div>';
					}
					
					//Description and Pin
					$itemdesc = '';
					if(!empty($item['description'])){
						$pinHint = (!empty($item['pinHint']) && !empty($item['hintText'])) ? ' pin-hint-inline' : '';
						$itemdesc .= '<div class="tpgb-icon-list-text'.esc_attr($pinHint).'"><p>'.wp_kses_post($item['description']).'</p>';
						if(!empty($item['pinHint']) && !empty($item['hintText'])){ 
							$itemdesc .='<span class="tpgb-hint-text '.esc_attr($pinAlignment).'">'.wp_kses_post($item['hintText']).'</span>';
						}
						$itemdesc .= '</div>';
					}
					
					$tooltipdata = '';
					$contentItem =[];
					if(!empty($item['itemTooltip'])){
						$contentItem['content'] = (!empty($item['tooltipText'])  ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['tooltipText']) : '');
						$contentItem['trigger'] = (!empty($attributes['tipTriggers'])  ? $attributes['tipTriggers'] : 'mouseenter');
						$contentItem['MaxWidth'] = (!empty($attributes['tipMaxWidth']) ? (int)$attributes['tipMaxWidth'] : 'none');
						$contentItem = htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8');
						$tooltipdata = 'data-tooltip-opt= \'' .$contentItem. '\'';
					}
					
					//Tooltip
					$itemtooltip =$tooltip_trigger='';
					$uniqid=uniqid("tooltip");
					if(!empty($item['itemTooltip'])){
						$itemtooltip .= ' data-tippy=""';
						$itemtooltip .= ' data-tippy-interactive="'.(!empty($attributes['tipInteractive']) ? 'true' : 'false').'"';
						$itemtooltip .= ' data-tippy-placement="'.(!empty($attributes['tipPlacement']) ? $attributes['tipPlacement'] : 'top').'"';
						$itemtooltip .= ' data-tippy-theme="'.$attributes['tipTheme'].'"';
						$itemtooltip .= ' data-tippy-arrow="'.(!empty($attributes['tipArrow']) ? 'true' : 'false').'"';
						$itemtooltip .= ' data-tippy-followCursor="'.(!empty($attributes['followCursor']) ? 'true' : 'false').'" ';
						$itemtooltip .= ' data-tippy-animation="'.(!empty($attributes['tipAnimation']) ? $attributes['tipAnimation'] : 'fade').'"';
						$itemtooltip .= ' data-tippy-offset="['.(!empty($attributes['tipOffset']) ? (int)$attributes['tipOffset'] : 0 ).','.(!empty($attributes['tipDistance']) ? (int)$attributes['tipDistance'] : 0).']"';
						$itemtooltip .= ' data-tippy-duration="['.(!empty($attributes['tipDurationIn']) ? (int)$attributes['tipDurationIn'] : '1').','.(!empty($attributes['tipDurationOut']) ? (int)$attributes['tipDurationOut'] : '1').']"';
					}
					//Item Content
					$output .= '<div id="'.$uniqid.'" class="tpgb-icon-list-item tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($active_class).'" '.$itemtooltip.' '.$tooltipdata.'>';
						$output .= $descurl_open;
						$output .= $icons;
						$output .= $itemdesc;
						$output .= $descurl_close;
					$output .= "</div>";
				endforeach;
			$output .= "</div>";
			
			if(!empty($readMoreToggle) && $i > $showListToggle){
				$output .= '<a href="#" class="read-more-options more" data-default-load="'.(int)$showListToggle.'" data-more-text="'.esc_attr($readMoreText).'" data-less-text="'.esc_attr($readLessText).'" >'.wp_kses_post($readMoreText).'</a>';
			}
		}
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_stylist_list() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'hover_bg_style' => array(
                'type' => 'boolean',
				'default' => false,
			),
			'listsRepeater' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'description' => [
							'type' => 'string',
							'default' => 'List item',
						],
						'selectIcon' => [
							'type' => 'string',
							'default' => 'fontawesome',
						],
						'iconFontawesome' => [
							'type' => 'string',
							'default' => 'fas fa-check-circle',
						],
						'descurl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',	
								'target' => '',	
								'nofollow' => ''
							],
						],
						'hintText' => [
							'type' => 'string',
							'default' => '',
						],
						'iconImg' => [
							'type' => 'object',
							'default' => [
								'url' => '',
							],
						],
						'hintColor' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'pinHint', 'relation' => '==', 'value' => true],
										['key' => 'hintText', 'relation' => '!=', 'value' => '']
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-icon-list-text span.tpgb-hint-text{color: {{hintColor}};}',
								],
							],
						],
						'hintBgColor' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'pinHint', 'relation' => '==', 'value' => true],
										['key' => 'hintText', 'relation' => '!=', 'value' => '']
									],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-icon-list-text span.tpgb-hint-text{background: {{hintBgColor}};}',
								],
							],
						],
						'hoverBgItem' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'hoverItemBg', 'relation' => '==', 'value' => true]
									],
									'selector' => '{{PLUS_WRAP}} .tpgb-bg-hover-effect {{TP_REPEAT_ID}}',
								],
							],
						],
						'tooltipText' => [
							'type' => 'string',
							'default' => '',
						],
						'tooltipTypo' => [
							'type' => 'object',
							'default' => (object) [
								'openTypography' => 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content',
								],
							],
						],
						'tooltipColor' => [
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content{color:{{tooltipColor}};}',
								],
							],
						],
					],
				], 
				'default' => [
					[
						"_key" => '0',
						"description" => "List item 1",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
						'tooltipText' => '',
						'hintText' => '',
						'descurl'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
						'iconImg' => [
							'url' => '',
						],
					],
					[
						"_key" => '1',
						"description" => "List item 2",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
						'tooltipText' => '',
						'hintText' => '',
						'descurl'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
						'iconImg' => [
							'url' => '',
						],
					],
					[ 
						"_key" => '2',
						"description" => "List item 3",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
						'tooltipText' => '',
						'hintText' => '',
						'descurl'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
						'iconImg' => [
							'url' => '',
						],
					]
				],
			],
			'listType' => [
                'type' => 'string',
				'default' => 'vertical',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items, {{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{flex-wrap: wrap;flex-flow: wrap;}  {{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{ margin : 0px }',
					],
				],
			],
			'readMoreToggle' => [
                'type' => 'boolean',
				'default' => false,
			],
			'showListToggle' => [
                'type' => 'string',
				'default' => '3',
			],
			'readMoreText' => [
                'type' => 'string',
				'default' => '+ Show all options',
			],
			'readLessText' => [
                'type' => 'string',
				'default' => '- Less options',
			],
			
			'listSpaceBetween' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'vertical']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-top: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-bottom: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child):before{ top: calc(100% + {{listSpaceBetween}}/2); }',
					],
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-top: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-bottom: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:before{ top: calc(100% + {{listSpaceBetween}}/2);}',
					],
				],
				'scopy' => true,
			],
			'horizontalSpaceBetween' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'left'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: {{horizontalSpaceBetween}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'right'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: {{horizontalSpaceBetween}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'center'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-left:0;margin-right:0}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: calc({{horizontalSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: calc({{horizontalSpaceBetween}}/2);}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'justify'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-left:0;margin-right:0}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: calc({{horizontalSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: calc({{horizontalSpaceBetween}}/2);}',
					],
				],
				'scopy' => true,
			],
			'alignment' => [
                'type' => 'object',
				'default' => ['md' => 'left'],
				'scopy' => true,
			],
			'separatorColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child):before{border-bottom : 1px solid {{separatorColor}};}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{width: 100%;}',
					],
				],
				'scopy' => true,
			],
			
			'iconNormalColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-icon .list-icon{color: {{iconNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'iconHoverColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon .list-icon{color: {{iconHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'iconSize' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon .list-icon{font-size: {{iconSize}};}',
					],
				],
				'scopy' => true,
			],
			'iconImgSize' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon img{max-width: {{iconImgSize}};}',
					],
				],
				'scopy' => true,
			],
			'iconAlignment' => [
                'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			
			'iconAdvancedStyle' => [
                'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			
			'iconWidth' => [
                'type' => 'object',
				'default' => ['md' =>''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-stylist-list .tpgb-icon-list-item div.tpgb-icon-list-icon{width: {{iconWidth}};height: {{iconWidth}};line-height: {{iconWidth}};text-align:center;align-items: center;justify-content: center;}',
					],
				],
				'scopy' => true,
			],
			'iconBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			'iconBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			'iconBorderRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon{border-radius: {{iconBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'iconBorderRadiusHover' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon{border-radius: {{iconBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'iconBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgGradient' => (object) [],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			'iconBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgGradient' => (object) [],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			'iconBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			'iconBoxShadowHover' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAdvancedStyle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon',
					],
				],
				'scopy' => true,
			],
			
			'textTypo' => [
                'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text,{{PLUS_WRAP}} .tpgb-icon-list-text p',
					],
				],
				'scopy' => true,
			],
			'textNormalColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text{color: {{textNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'textHoverColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-text{color: {{textHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'textIndent' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-stylist-list .tpgb-icon-list-text{padding-left: {{textIndent}};}',
					],
				],
				'scopy' => true,
			],
			
			'textPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item{padding: {{textPadding}};}',
					],
				],
				'scopy' => true,
			],
			'textBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			'textBRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item{border-radius: {{textBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'textBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			'titleBShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			
			'textHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			'textHBRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover{border-radius: {{textHBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'textBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			'titleHBShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			
			'toggleTypo' => [
                'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'readMoreToggle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} a.read-more-options',
					],
				],
				'scopy' => true,
			],
			'toggleNormalColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'readMoreToggle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} a.read-more-options{color: {{toggleNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'toggleHoverColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'readMoreToggle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} a.read-more-options:hover{color: {{toggleHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'toggleIndent' => [
                'type' => 'object',
				'default' => ['md' => 0],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'readMoreToggle', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} a.read-more-options{margin-top: {{toggleIndent}};}',
					],
				],
				'scopy' => true,
			],
			
			'pinAlignment' => [
				'type' => 'string',
				'default' => 'right',
				'scopy' => true,
			],
			'pinTypo' => [
                'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text',
					],
				],
				'scopy' => true,
			],
			'pinBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text',
					],
				],
				'scopy' => true,
			],
			'pinBRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text{border-radius: {{pinBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'pinPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text{padding: {{pinPadding}};}',
					],
				],
				'scopy' => true,
			],
			'pinHorizontalAdjust' => [
                'type' => 'object',
				'default' => ['md' => 0],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text{margin-left: {{pinHorizontalAdjust}};}',
					],
				],
				'scopy' => true,
			],
			'pinLeftWidth' => [
                'type' => 'object',
				'default' => ['md' => 60],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'pinAlignment', 'relation' => '==', 'value' => 'left']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text.left{min-width: {{pinLeftWidth}};}',
					],
				],
				'scopy' => true,
			],
			'pinRightWidth' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'pinAlignment', 'relation' => '==', 'value' => 'right']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text.right{min-width: {{pinRightWidth}};}',
					],
				],
				'scopy' => true,
			],
			'pinVerticalAdjust' => [
                'type' => 'object',
				'default' => ['md' => 0],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text span.tpgb-hint-text{margin-top: {{pinVerticalAdjust}};}',
					],
				],
				'scopy' => true,
			],
			
			'tipInteractive' => [
                'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'tipPlacement' => [
                'type' => 'string',
				'default' => 'top',
				'scopy' => true,
			],
			'tipTheme' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'tipMaxWidth' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box{width : {{tipMaxWidth}}px; max-width : {{tipMaxWidth}}px; }  ',
					],
				],
				'scopy' => true,
			],
			'tipOffset' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'followCursor' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'tipDistance' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'tipArrow' => [
                'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'tipTriggers' => [
                'type' => 'string',
				'default' => 'mouseenter',
				'scopy' => true,
			],
			'tipAnimation' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'tipDurationIn' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'tipDurationOut' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'tipArrowColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'tipArrow', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tippy-arrow{color: {{tipArrowColor}};}',
					],
				],
				'scopy' => true,
			],
			'tipPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box{padding: {{tipPadding}};}',
					],
				],
				'scopy' => true,
			],
			'tipBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box',
					],
				],
				'scopy' => true,
			],
			'tipBorderRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box{border-radius: {{tipBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'tipBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgGradient' => (object) [],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box',
					],
				],
				'scopy' => true,
			],
			'tipBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tippy-box',
					],
				],
				'scopy' => true,
			],
			
			'hoverInverseEffect' => [
                'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'unhoverItemOpacity' => [
                'type' => 'string',
				'default' => 0.6,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'hoverInverseEffect', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.hover-inverse-effect:hover .on-hover .tpgb-icon-list-item,{{PLUS_WRAP}}.hover-inverse-effect-global .on-hover .tpgb-icon-list-item{opacity: {{unhoverItemOpacity}};} {{PLUS_WRAP}}.hover-inverse-effect:hover .on-hover .tpgb-icon-list-item:hover,body.hover-stylist-global,{{PLUS_WRAP}}.hover-inverse-effect-global .on-hover .tpgb-icon-list-item:hover{opacity:1;}',
					],
				],
				'scopy' => true,
			],
			'effectArea' => [
				'type' => 'string',
				'default' => 'individual',
			],
			'globalId' => [
				'type' => 'string',
				'default' => '',
			],
		);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-stylist-list', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_stylist_list_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_stylist_list' );