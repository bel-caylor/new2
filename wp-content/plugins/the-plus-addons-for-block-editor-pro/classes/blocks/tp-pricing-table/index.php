<?php
/* Block : Pricing Table 
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_pricing_table_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$contentStyle = (!empty($attributes['contentStyle'])) ? $attributes['contentStyle'] : 'wysiwyg';
	$conListStyle = (!empty($attributes['conListStyle'])) ? $attributes['conListStyle'] : 'style-1';
	$stylishList = (!empty($attributes['stylishList'])) ? $attributes['stylishList'] : [];
	$wyStyle = (!empty($attributes['wyStyle'])) ? $attributes['wyStyle'] : 'style-1';
	$wyContent = (!empty($attributes['wyContent'])) ? $attributes['wyContent'] : '';
	
	$disRibbon = (!empty($attributes['disRibbon'])) ? $attributes['disRibbon'] : false;
	$ribbonStyle = (!empty($attributes['ribbonStyle'])) ? $attributes['ribbonStyle'] : 'style-1';
	$ribbonText = (!empty($attributes['ribbonText'])) ? $attributes['ribbonText'] : '';
	
	$titleStyle = (!empty($attributes['titleStyle'])) ? $attributes['titleStyle'] : 'style-1';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'none';
	$iconStyle = (!empty($attributes['iconStyle'])) ? $attributes['iconStyle'] : 'square';
	$iconStore = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : 'fas fa-home';
	$imgStore = (!empty($attributes['imgStore'])) ? $attributes['imgStore'] : '';
	$title = (!empty($attributes['title'])) ? $attributes['title'] : '';
	$subTitle = (!empty($attributes['subTitle'])) ? $attributes['subTitle'] : '';
	
	$priceStyle = (!empty($attributes['priceStyle'])) ? $attributes['priceStyle'] : 'style-1';
	$disPrePrice = (!empty($attributes['disPrePrice'])) ? $attributes['disPrePrice'] : false;
	$prevPreText = (!empty($attributes['prevPreText'])) ? $attributes['prevPreText'] : '';
	$prevPriceValue = (!empty($attributes['prevPriceValue'])) ? $attributes['prevPriceValue'] : '';
	$prevPostText = (!empty($attributes['prevPostText'])) ? $attributes['prevPostText'] : '';
	$preText = (!empty($attributes['preText'])) ? $attributes['preText'] : '';
	$priceValue = (isset($attributes['priceValue'])) ? $attributes['priceValue'] : '';
	$postText = (!empty($attributes['postText'])) ? $attributes['postText'] : '';
	
	$readMoreToggle = (!empty($attributes['readMoreToggle'])) ? $attributes['readMoreToggle'] : false;
    $showListToggle = (!empty($attributes['showListToggle'])) ? (int)$attributes['showListToggle'] : 3;
    $readMoreText = (!empty($attributes['readMoreText'])) ? $attributes['readMoreText'] : '';
    $readLessText = (!empty($attributes['readLessText'])) ? $attributes['readLessText'] : '';
	
	$ctaText = (!empty($attributes['ctaText'])) ? $attributes['ctaText'] : '';
	
	$extBtnshow = (!empty($attributes['extBtnshow'])) ? $attributes['extBtnshow'] : false ;
	
	$hoverStyle = (!empty($attributes['hoverStyle'])) ? $attributes['hoverStyle'] : 'hover_normal';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$svgIcon = (!empty($attributes['svgIcon'])) ? $attributes['svgIcon'] : '';
	$svgDraw = (!empty($attributes['svgDraw'])) ? $attributes['svgDraw'] : 'delayed';
	$svgstroColor = (!empty($attributes['svgstroColor'])) ? $attributes['svgstroColor'] : '';
	$svgfillColor = (!empty($attributes['svgfillColor'])) ? $attributes['svgfillColor'] : 'none';
	$svgDura = (!empty($attributes['svgDura'])) ? $attributes['svgDura'] : 90;
	
	$i = 0;
	// Overlay
	$contentOverlay = '';
	$contentOverlay .= '<div class="content-overlay-bg-color tpgb-trans-easeinout"></div>';
	
	//Get Icon
	$getPriceIcon = '';
	$icon_style = '';
	$trlinr = 'tpgb-trans-linear';
	if($iconType=='icon'){
		$icon_style = $iconStyle ;
	}
	$getPriceIcon .= '<div class="price-table-icon '.($iconType=='svg' ? 'tpgb-draw-svg' : 'pricing-icon '.esc_attr($trlinr) ).' icon-'.esc_attr($icon_style).'" '.($iconType=='svg' ? 'data-id="service-svg-'.esc_attr($block_id).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes"': '' ).' >';
		if($iconType=='icon'){
			$getPriceIcon .= '<i class="'.esc_attr($iconStore).'"></i>';
		}
		if($iconType=='img' && !empty($imgStore)){
			if(!empty($imgStore['id'])){
				$imgSrc = wp_get_attachment_image($imgStore['id'] , 'full', false, ['class' => 'pricing-icon-img']);
			}else if(!empty($imgStore['url'])){
				$imgUrl = (isset($imgStore['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($imgStore) : (!empty($imgStore['url']) ? $imgStore['url'] : '');
				$imgSrc = '<img src='.esc_url($imgUrl).' class="pricing-icon-img" alt="'.esc_attr__('Icon','tpgbp').'"/>';
			}
			$getPriceIcon .= $imgSrc;
		}
		if($iconType=='svg' && !empty($svgIcon) && !empty($svgIcon['url'])){
			$svgUrl = (isset($svgIcon['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($svgIcon) : (!empty($svgIcon['url']) ? $svgIcon['url'] : '');
			$getPriceIcon .= '<object id="service-svg-'.esc_attr($block_id).'" type="image/svg+xml" role="none" data="'.esc_url($svgUrl).'">';
			$getPriceIcon .= '</object>';
		}
	$getPriceIcon .= '</div>';
		
	//Get Title
	$getPriceTitle = '';
	if(!empty($title)){
		$getPriceTitle .= '<div class="pricing-title-wrap">';
			$getPriceTitle .= '<div class="pricing-title '.esc_attr($trlinr).'">'.wp_kses_post($title).'</div>';
		$getPriceTitle .= '</div>';
	}
	
	//Get Sub Title
	$getPriceSubTitle = '';
	if(!empty($subTitle)){
		$getPriceSubTitle .= '<div class="pricing-subtitle-wrap">';
			$getPriceSubTitle .= '<div class="pricing-subtitle '.esc_attr($trlinr).'">'.wp_kses_post($subTitle).'</div>';
		$getPriceSubTitle .= '</div>'; 
	}
	
	//Get Ribbon/Pin
	$getRibbon = '';
	if(!empty($disRibbon) && !empty($ribbonText)){
		$getRibbon .= '<div class="pricing-ribbon-pin tpgb-relative-block '.esc_attr($ribbonStyle).'">';
			$getRibbon .= '<div class="ribbon-pin-inner '.esc_attr($trlinr).'">'.wp_kses_post($ribbonText).'</div>';
		$getRibbon .= '</div>';
	}
	
	//Get Title-SubTitle Content
	$getTitleContent = '';
	$getTitleContent .= '<div class="pricing-title-content tpgb-relative-block '.esc_attr($titleStyle).'">';
		if($iconType!='none'){
			$getTitleContent .= $getPriceIcon;
		}
		$getTitleContent .= $getPriceTitle;
		$getTitleContent .= $getPriceSubTitle;
	$getTitleContent .= '</div>';
	
	//Get Price Content
	$getPriceContent = '';
	$getPriceContent .= '<div class="pricing-price-wrap '.esc_attr($priceStyle).'">';
		if(!empty($disPrePrice)){
			$getPriceContent .= '<span class="pricing-previous-price-wrap '.esc_attr($trlinr).'">';
				$getPriceContent .= wp_kses_post($prevPreText);
				$getPriceContent .= wp_kses_post($prevPriceValue);
				$getPriceContent .= wp_kses_post($prevPostText);
			$getPriceContent .='</span>';
		}
		if(!empty($preText)){
			$getPriceContent .= '<span class="price-prefix-text '.esc_attr($trlinr).'">'.wp_kses_post($preText).'</span>';
		}
		if(isset($priceValue) && !empty($priceValue)){
			$getPriceContent .= '<span class="pricing-price '.esc_attr($trlinr).'">'.wp_kses_post($priceValue).'</span>'; 
		}
		if(!empty($postText)){
			$getPriceContent .= '<span class="price-postfix-text '.esc_attr($trlinr).'">'.wp_kses_post($postText).'</span>';
		}
	$getPriceContent .= '</div>';
		
	//Get Button & CTA Text
	$getBtnCta = '';
	if(!empty($extBtnshow)){
		$getBtnCta .= '<div class="pricing-table-button">';
			$getBtnCta .= Tpgb_Blocks_Global_Options::load_plusButton_saves($attributes);
		$getBtnCta .= '</div>';
	}
	if(!empty($ctaText)){
		$getBtnCta .= '<div class="pricing-cta-text">'.wp_kses_post($ctaText).'</div>';
	}
	
	//Get Stylish List Content
	$getStylishContent = '';
	if($contentStyle=='stylish'){
		$getStylishContent .= '<div class="pricing-content-wrap listing-content tpgb-relative-block '.esc_attr($conListStyle).'">';
			if(!empty($stylishList)){
				$getStylishContent .= '<div class="tpgb-icon-list-items '.esc_attr($trlinr).'">';
					foreach ( $stylishList as $index => $item ) :
						
						$i++;
						//Tooltip
				
						$itemtooltip = $tooltipdata = '';
						$uniqid=uniqid("tooltip");

						$contentItem =[];
						if(!empty($item['itemTooltip'])){
							$contentItem['content'] = (!empty($item['tooltipText'])  ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['tooltipText']) : '');
							$contentItem['trigger'] = (!empty($attributes['tipTriggers'])  ? $attributes['tipTriggers'] : 'mouseenter');
							$contentItem['MaxWidth'] = (!empty($attributes['tipMaxWidth']) ? (int)$attributes['tipMaxWidth'] : 'none');
							$contentItem = htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8');
							$tooltipdata = ' data-tooltip-opt= \'' .$contentItem. '\' ';
						}
						
						if(!empty($item['itemTooltip'])){
							$itemtooltip .= ' data-tippy=""';
							$itemtooltip .= ' data-tippy-interactive="'.($attributes['tipInteractive'] ? 'true' : 'false').'"';
							$itemtooltip .= ' data-tippy-placement="'.($attributes['tipPlacement'] ? $attributes['tipPlacement'] : 'top').'"';
							$itemtooltip .= ' data-tippy-followCursor="'.(!empty($attributes['followCursor']) ? 'true' : 'false').'" ';
							$itemtooltip .= ' data-tippy-theme="'.$attributes['tipTheme'].'"';
							$itemtooltip .= ' data-tippy-arrow="'.($attributes['tipArrow'] ? 'true' : 'false').'"';
							$itemtooltip .= ' data-tippy-animation="'.($attributes['tipAnimation'] ? $attributes['tipAnimation'] : 'fade').'"';
							$itemtooltip .= ' data-tippy-offset="['.(int)$attributes['tipOffset'].','.(int)$attributes['tipDistance'].']"';
							$itemtooltip .= ' data-tippy-duration="['.(int)$attributes['tipDurationIn'].','.(int)$attributes['tipDurationOut'].']"';
						}
						//Item Content
						$getStylishContent .= '<div id="'.esc_attr($uniqid).'" class="tpgb-icon-list-item '.esc_attr($trlinr).' tp-repeater-item-'.esc_attr($item['_key']).'" '.$itemtooltip.'  '.$tooltipdata.' >';
						
							//Get Item Icon
							$getItemIcon = '';
							$getItemIcon .= '<span class="tpgb-icon-list-icon '.esc_attr($trlinr).'">'; 
								$getItemIcon .='<i class="'.esc_attr($item['iconStore']).'" aria-hidden="true"></i>';
							$getItemIcon .= '</span>';

							//Get Item Extra Icon
							$getItemExIcon = '';
							if(!empty($item['eIcnToggle']) && !empty($item['eIconStore'])){
								$getItemExIcon .= '<span class="tpgb-extra-list-icon '.esc_attr($trlinr).'">'; 
									$getItemExIcon .='<i class="'.esc_attr($item['eIconStore']).'" aria-hidden="true"></i>';
								$getItemExIcon .= '</span>';
							}
							
							//Get Item Description
							$getItemDesc = '';
							if(!empty($item['listDesc'])){
								$getItemDesc .= '<span class="tpgb-icon-list-text '.esc_attr($trlinr).'">'.wp_kses_post($item['listDesc']).'</span>';
							}
							$getStylishContent .= $getItemIcon;
							$getStylishContent .= $getItemDesc;
							$getStylishContent .= $getItemExIcon;
						$getStylishContent .= "</div>";
						
					endforeach;
				$getStylishContent .= "</div>";
				
				if($conListStyle!='style-2' && !empty($readMoreToggle) && $i > $showListToggle){
					$getStylishContent .= '<a href="#" class="read-more-options tpgb-relative-block '.esc_attr($trlinr).' more" data-default-load="'.(int)$showListToggle.'" data-more-text="'.esc_attr($readMoreText).'" data-less-text="'.esc_attr($readLessText).'" >'.wp_kses_post($readMoreText).'</a>';
				}
				if($conListStyle=='style-1'){
					$getStylishContent .= $contentOverlay;
				}
			}
		$getStylishContent .= "</div>";
	}
	
	//Get wysiwyg Content
	$getWysiwygContent = '';
	if($contentStyle=='wysiwyg'){
		$getWysiwygContent .= '<div class="pricing-content-wrap content-desc '.esc_attr($wyStyle).'">';
			if($wyStyle=='style-2'){
				$getWysiwygContent .= '<hr class="border-line"/>';
			}
			$getWysiwygContent .= '<div class="pricing-content '.esc_attr($trlinr).'">'.wp_kses_post($wyContent).'</div>';
			$getWysiwygContent .= $contentOverlay;
		$getWysiwygContent .= '</div>';
	}

	$dyStyle = '';
	$titleAlign = (!empty($attributes['titleAlign'])) ? $attributes['titleAlign'] : ['md'=>'', 'sm'=>'', 'xs'=> ''];
	if($iconType!='none' && !empty($titleAlign)){
			$leftStyle = ' .tpgb-block-'.esc_attr($block_id).' .pricing-table-inner .price-table-icon { margin-left: 0}';
			$rightStyle = ' .tpgb-block-'.esc_attr($block_id).' .pricing-table-inner .price-table-icon { margin-right: 0}';
		if(!empty($titleAlign['md'])){
			if($titleAlign['md']=='left'){
				$dyStyle .= $leftStyle;
			}
			if($titleAlign['md']=='right'){
				$dyStyle .= $rightStyle;
			}
		}
		if(!empty($titleAlign['sm'])){
			if($titleAlign['sm']=='left'){
				$dyStyle .= ' @media (max-width:1024px) and (min-width:768px){ '.$leftStyle.' }';
			}
			if($titleAlign['sm']=='right'){
				$dyStyle .= ' @media (max-width:1024px) and (min-width:768px){ '.$rightStyle.' }';
			}
		}
		if(!empty($titleAlign['xs'])){
			if($titleAlign['xs']=='left'){
				$dyStyle .= ' @media (max-width:767px){ '.$leftStyle.' }';
			}
			if($titleAlign['xs']=='right'){
				$dyStyle .= ' @media (max-width:767px){ '.$rightStyle.' }';
			}
		}
	}
		
	$output = '';
    $output .= '<div class="tpgb-pricing-table tpgb-relative-block '.esc_attr($trlinr).' pricing-'.esc_attr($style).' '.esc_attr($hoverStyle).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		 $output .= '<div class="pricing-table-inner '.esc_attr($trlinr).'">';
		if($style=='style-1' || $style=='style-2'){
			$output .= $getRibbon;
			$output .= $getTitleContent;
			$output .= $getPriceContent;
			$output .= $getBtnCta;
						
			$output .= $getStylishContent;
			$output .= $getWysiwygContent;
			$output .= '<div class="pricing-overlay-color tpgb-trans-easeinout"></div>';
		}
		if($style=='style-3'){
			$output .= '<div class="pricing-top-part '.esc_attr($trlinr).'">';
				$output .= $getRibbon;
				$output .= $getTitleContent;
				$output .= $getPriceContent;
				$output .= $getBtnCta;
				$output .= '<div class="pricing-overlay-color tpgb-trans-easeinout"></div>';
			$output .= '</div>';		
				$output .= $getStylishContent;
				$output .= $getWysiwygContent;
			
		}
		$output .= '</div>';
	$output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

	if(!empty($dyStyle)){
		$output .= '<style>'.$dyStyle.'</style>';
	}
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_pricing_table() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'style' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'titleStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'title' => [
			'type' => 'string',
			'default' => 'Professional',	
		],
		'subTitle' => [
			'type' => 'string',
			'default' => 'Designed for Agency',	
		],
		'iconType' => [
			'type' => 'string',
			'default' => 'none',	
		],
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-home',
		],
		'imgStore' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'imgSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'img' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon-img,{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{ width: {{imgSize}}; }',
				],
			],
		],
		'titleAlign' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-title { text-align: {{titleAlign}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-subtitle { text-align: {{titleAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'priceStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'preText' => [
			'type' => 'string',
			'default' => '$',	
		],
		'priceValue' => [
			'type' => 'string',
			'default' => '99.99',	
		],
		'postText' => [
			'type' => 'string',
			'default' => 'Per Year',	
		],
		'priceAlign' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap { text-align: {{priceAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'disPrePrice' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'prevPreText' => [
			'type' => 'string',
			'default' => '$',	
		],
		'prevPriceValue' => [
			'type' => 'string',
			'default' => '199.99',	
		],
		'prevPostText' => [
			'type' => 'string',
			'default' => '',	
		],
		'contentStyle' => [
			'type' => 'string',
			'default' => 'wysiwyg',	
		],
		'conListStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'stylishList' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'listDesc' => [
						'type' => 'string',
						'default' => 'Feature 1'
					],
					'iconStore' => [
						'type'=> 'string',
						'default' => 'fas fa-plus'
					],
					'iconNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-icon-list-icon { color: {{iconNmlColor}}; }',
							],
						],
					],
					'iconHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover {{TP_REPEAT_ID}} .tpgb-icon-list-icon { color: {{iconHvrColor}}; }',
							],
						],
					],
					'tooltipText' => [
						'type' => 'string',
						'default' => 'Special Feature'
					],
					'tooltipTypo' => [
						'type' => 'object',
						'default' => (object)[
							'openTypography' => 0
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
					'eIcnToggle' => [
						'type' => 'boolean',
						'default' => false,
					],
					'eIconStore' => [
						'type'=> 'string',
						'default' => 'fas fa-question-circle'
					],
					'eIconNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-extra-list-icon { color: {{eIconNColor}}; }',
							],
						],
					],
					'eIconHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover {{TP_REPEAT_ID}} .tpgb-extra-list-icon { color: {{eIconHColor}}; }',
							],
						],
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'listDesc' => 'Feature 1',
					'iconStore'=> 'fas fa-check-circle',
					'iconNmlColor'=> '',
					'iconHvrColor'=> '',
					'tooltipText'=> 'Special Feature',
					"tooltipTypo" => ['openTypography' => 0 ],
					'eIcnToggle'=> false,
					'eIconStore'=> 'fas fa-question-circle',
					'eIconNColor'=> '',
					'eIconHColor'=> '',
 				],
				[
					'_key' => '1',
					'listDesc' => 'Feature 2',
					'iconStore'=> 'fas fa-check-circle',
					'iconNmlColor'=> '',
					'iconHvrColor'=> '',
					'tooltipText'=> 'Special Feature',
					"tooltipTypo" => ['openTypography' => 0 ],
					'eIcnToggle'=> false,
					'eIconStore'=> 'fas fa-question-circle',
					'eIconNColor'=> '',
					'eIconHColor'=> '',
				],
				[
					'_key' => '2',
					'listDesc' => 'Feature 3',
					'iconStore'=> 'fas fa-check-circle',
					'iconNmlColor'=> '',
					'iconHvrColor'=> '',
					'tooltipText'=> 'Special Feature',
					"tooltipTypo" => ['openTypography' => 0 ],
					'eIcnToggle'=> false,
					'eIconStore'=> 'fas fa-question-circle',
					'eIconNColor'=> '',
					'eIconHColor'=> '',
				] 
			]
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
			
		'wyStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'wyContent' => [
			'type' => 'string',
			'default' => 'All features of plan will be available here.</br></br>- Feature 1</br>- Feature 2</br>- Feature 3',	
		],
		
		'ctaText' => [
			'type' => 'string',
			'default' => '*30 Days Refund Policy </br></br>',	
		],
		'ctaAlign' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ctaText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-cta-text { text-align: {{ctaAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'disRibbon' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'ribbonStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'ribbonText' => [
			'type' => 'string',
			'default' => 'Recommended',	
		],
		
		'iconStyle' => [
			'type' => 'string',
			'default' => 'square',	
			'scopy' => true,
		],
		'iconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{ font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{ width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{ color: {{icnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-icon{ color: {{icnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNormalBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon',
				],
			],
			'scopy' => true,
		],
		'icnHoverBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-icon',
				],
			],
			'scopy' => true,
		],
		'nmlBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{ border:1px solid ; border-color: {{nmlBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hvrBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-icon{ border:1px solid ; border-color: {{hvrBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'nmlIcnBRadius' => [
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
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon{border-radius: {{nmlIcnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'hvrIcnBRadius' => [
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
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-icon{border-radius: {{hvrIcnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'nmlIcnShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-icon',
				],
			],
			'scopy' => true,
		],
		'hvrIcnShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-icon',
				],
			],
			'scopy' => true,
		],
		
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.pricing-style-1 .pricing-title-wrap .pricing-title , {{PLUS_WRAP}}.pricing-style-2 .pricing-title-wrap .pricing-title , {{PLUS_WRAP}}.pricing-style-3 .pricing-title-wrap .pricing-title',
				],
			],
			'scopy' => true,
		],
		'titleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.pricing-style-1 .pricing-title-wrap .pricing-title , {{PLUS_WRAP}}.pricing-style-2 .pricing-title-wrap .pricing-title , {{PLUS_WRAP}}.pricing-style-3 .pricing-title-wrap .pricing-title{ color: {{titleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.pricing-style-1 .pricing-table-inner:hover .pricing-title , {{PLUS_WRAP}}.pricing-style-2 .pricing-table-inner:hover .pricing-title , {{PLUS_WRAP}}.pricing-style-3 .pricing-table-inner:hover .pricing-title{ color: {{titleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		
		'subTitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table .pricing-subtitle',
				],
			],
			'scopy' => true,
		],
		'subTitleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table .pricing-subtitle{ color: {{subTitleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'subTitleHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table .pricing-table-inner:hover .pricing-subtitle{ color: {{subTitleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		
		'prevPriceTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disPrePrice', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-previous-price-wrap',
				],
			],
			'scopy' => true,
		],
		'prevPriceAlign' => [
			'type' => 'string',
			'default' => 'top',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disPrePrice', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-previous-price-wrap{ vertical-align: {{prevPriceAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'prevPriceNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disPrePrice', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-previous-price-wrap{ color: {{prevPriceNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'prevPriceHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'disPrePrice', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-previous-price-wrap{ color: {{prevPriceHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'prefixTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'priceStyle', 'relation' => '!=', 'value' => 'style-1' ] , ['key' => 'preText', 'relation' => '!=', 'value' => '' ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap span.price-prefix-text',
				],
			],
			'scopy' => true,
		],
		'prefixNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'priceStyle', 'relation' => '!=', 'value' => 'style-1' ] , ['key' => 'preText', 'relation' => '!=', 'value' => '' ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap span.price-prefix-text{ color: {{prefixNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'prefixHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [
						(object) ['key' => 'priceStyle', 'relation' => '!=', 'value' => 'style-1' ] , ['key' => 'preText', 'relation' => '!=', 'value' => '' ]
					],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover span.price-prefix-text{ color: {{prefixHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'priceTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'priceValue', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap.style-1 .pricing-price ,{{PLUS_WRAP}} .pricing-price-wrap.style-1 span.price-prefix-text , {{PLUS_WRAP}} .pricing-price-wrap.style-2 .pricing-price , {{PLUS_WRAP}} .pricing-price-wrap.style-3 .pricing-price',
				],
			],
			'scopy' => true,
		],
		'priceNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'priceValue', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap.style-1 .pricing-price , {{PLUS_WRAP}} .pricing-price-wrap.style-1 span.price-prefix-text , {{PLUS_WRAP}} .pricing-price-wrap.style-2 .pricing-price , {{PLUS_WRAP}} .pricing-price-wrap.style-3 .pricing-price{ color: {{priceNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'priceHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'priceValue', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-price-wrap.style-1 .pricing-price , {{PLUS_WRAP}} .pricing-table-inner:hover .pricing-price-wrap.style-1 span.price-prefix-text , {{PLUS_WRAP}} .pricing-table-inner:hover .pricing-price-wrap.style-2 .pricing-price , {{PLUS_WRAP}} .pricing-table-inner:hover .pricing-price-wrap.style-3 .pricing-price{ color: {{priceHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'postfixTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap span.price-postfix-text',
				],
			],
			'scopy' => true,
		],
		'postfixNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'postText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-price-wrap span.price-postfix-text{ color: {{postfixNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'postfixHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'postText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover span.price-postfix-text{ color: {{postfixHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		
		'listContentTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-text',
				],
			],
			'scopy' => true,
		],
		'listIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-icon{ font-size: {{listIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'extraIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-extra-list-icon{ font-size: {{extraIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'listAlign' => [
			'type' => 'string',
			'default' => 'flex-start',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{ justify-content: {{listAlign}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-2' ] , ['key' => 'listAlign', 'relation' => '==', 'value' => 'flex-start' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-2{ text-align: left; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-2' ] , ['key' => 'listAlign', 'relation' => '==', 'value' => 'flex-end' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-2{ text-align: right; }',
				],
			],
			'scopy' => true,
		],
		'listTextNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-text{ color: {{listTextNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listTextHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .tpgb-icon-list-text{ color: {{listTextHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listIcnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-icon{ color: {{listIcnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listIcnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .tpgb-icon-list-icon{ color: {{listIcnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'extraIcnNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-extra-list-icon{ color: {{extraIcnNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'extraIcnHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .tpgb-extra-list-icon{ color: {{extraIcnHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .listing-content.style-2 .tpgb-icon-list-item{ border-color: {{listBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 .tpgb-icon-list-item{ margin-bottom: {{listSpace}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .listing-content.style-2 .tpgb-icon-list-item{ padding: {{listSpace}}; }',
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
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 a.read-more-options',
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
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 a.read-more-options{color: {{toggleNormalColor}};}',
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
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 a.read-more-options:hover{color: {{toggleHoverColor}};}',
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
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 a.read-more-options{margin-top: {{toggleIndent}};}',
				],
			],
			'scopy' => true,
		],
		
		'wysiwygTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.content-desc .pricing-content',
				],
			],
			'scopy' => true,
		],
		'wysiwygTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.content-desc .pricing-content , {{PLUS_WRAP}} .pricing-content-wrap.content-desc .pricing-content p{ color: {{wysiwygTextColor}}; }',
				],
			],
			'scopy' => true,
		],
		'wysiwygHTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner:hover .pricing-content , {{PLUS_WRAP}} .pricing-table-inner:hover .pricing-content p{ color: {{wysiwygHTextColor}}; }',
				],
			],
			'scopy' => true,
		],
		'wyBorderWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ] , ['key' => 'wyStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .content-desc.style-2 hr.border-line{ margin: 30px {{wyBorderWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'wysiwygBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ] , ['key' => 'wyStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .content-desc.style-2 hr.border-line{ border-color: {{wysiwygBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'wysiwygAlign' => [
			'type' => 'string',
			'default' => 'center',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'wysiwyg' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.content-desc .pricing-content , {{PLUS_WRAP}} .pricing-content-wrap.content-desc .pricing-content p{ text-align: {{wysiwygAlign}}; }',
				],
			],
			'scopy' => true,
		],
		
		'listNmlBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1 .tpgb-icon-list-items, {{PLUS_WRAP}} .listing-content.style-1 a.read-more-options',
				],
			],
			'scopy' => true,
		],
		'listNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1 .tpgb-icon-list-items, {{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1 a.read-more-options, {{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color{border-radius: {{listNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'listNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color',
				],
			],
			'scopy' => true,
		],
		'listNmlShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1 .content-overlay-bg-color',
				],
			],
			'scopy' => true,
		],
		'listHvrBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .listing-content.style-1:hover .tpgb-icon-list-items, {{PLUS_WRAP}} .listing-content.style-1:hover a.read-more-options',
				],
			],
			'scopy' => true,
		],
		'listHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1:hover .tpgb-icon-list-items, {{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1:hover a.read-more-options, {{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color{border-radius: {{listHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'listHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color',
				],
			],
			'scopy' => true,
		],
		'listHvrShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'contentStyle', 'relation' => '==', 'value' => 'stylish' ] , ['key' => 'conListStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-content-wrap.listing-content.style-1:hover .content-overlay-bg-color',
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
			'scopy' => true,
		],
		'tipOffset' => [
			'type' => 'string',
			'default' => '',
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
		'followCursor' => [
			'type' => 'boolean',
			'default' => false,
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
		
		'ctaTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ctaText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-cta-text, {{PLUS_WRAP}} .pricing-table-inner .pricing-cta-text p',
				],
			],
			'scopy' => true,
		],
		'ctaColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ctaText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-table-inner .pricing-cta-text, {{PLUS_WRAP}} .pricing-table-inner .pricing-cta-text p{ color: {{ctaColor}}; }',
				],
			],
			'scopy' => true,
		],
		
		'pinTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disRibbon', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-ribbon-pin .ribbon-pin-inner',
				],
			],
			'scopy' => true,
		],
		'pinColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disRibbon', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-ribbon-pin .ribbon-pin-inner{ color: {{pinColor}}; }',
				],
			],
			'scopy' => true,
		],
		'pinBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disRibbon', 'relation' => '==', 'value' => true ] , ['key' => 'ribbonStyle', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}} .pricing-ribbon-pin.style-1 .ribbon-pin-inner',
				],
			],
			'scopy' => true,
		],
		'pinS3BG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disRibbon', 'relation' => '==', 'value' => true ] , ['key' => 'ribbonStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .pricing-ribbon-pin.style-2{ background: {{pinS3BG}}; }  {{PLUS_WRAP}} .pricing-ribbon-pin.style-3:after{ border-top-color: {{pinS3BG}}; border-left-color: {{pinS3BG}}; }',
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
					'condition' => [(object) ['key' => 'disRibbon', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .pricing-ribbon-pin.style-1 .ribbon-pin-inner , {{PLUS_WRAP}} .pricing-ribbon-pin.style-2 {border-radius: {{pinBRadius}};}',
				],
			],
			'scopy' => true,
		],
		
		'innerPadding' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part{padding: {{innerPadding}};}',
				],
			],
			'scopy' => true,
		],
		'bgNmlBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		'bgNmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-overlay-color{border-radius: {{bgNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'bgHvrBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3:hover .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		'bgHvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3:hover .pricing-top-part , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2:hover .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3:hover .pricing-overlay-color{border-radius: {{bgHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'hoverStyle' => [
			'type' => 'string',
			'default' => 'hover_normal',	
			'scopy' => true,
		],
		'normalBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		'nmlOverlay' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'hoverStyle', 'relation' => '==', 'value' => 'hover_normal' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-overlay-color{ background: {{nmlOverlay}}; }',
				],
			],
			'scopy' => true,
		],
		'bgNmlShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		'hoverBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.hover_fadein .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.hover_slide_left .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.hover_slide_right .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.hover_slide_top .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.hover_slide_bottom .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.hover_normal.pricing-style-2:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.hover_normal.pricing-style-3:hover .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		'hvrOverlay' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'hoverStyle', 'relation' => '==', 'value' => 'hover_normal' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2:hover .pricing-overlay-color , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3:hover .pricing-overlay-color{ background: {{hvrOverlay}}; }',
				],
			],
			'scopy' => true,
		],
		'bgHvrShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2:hover .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3:hover .pricing-top-part',
				],
			],
			'scopy' => true,
		],
		
		'scaleZoom' => [
			'type' => 'string',
			'default' => 1,
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part{ transform: scale({{scaleZoom}}); }',
				],
			],
			'scopy' => true,
		],
		'resposZoom' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'scaleZoomT' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'resposZoom', 'relation' => '==', 'value' => true ]],
					'selector' => '@media (max-width: 1024px) { {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part{ transform: scale({{scaleZoomT}}); } }',
				],
			],
			'scopy' => true,
		],
		'scaleZoomM' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'resposZoom', 'relation' => '==', 'value' => true ]],
					'selector' => '@media (max-width: 767px) { {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-1 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-2 .pricing-table-inner , {{PLUS_WRAP}}.tpgb-pricing-table.pricing-style-3 .pricing-top-part{ transform: scale({{scaleZoomM}}); } }',
				],
			],
			'scopy' => true,
		],

		'svgIcon' => [
			'type' => 'object',
			'default' => [],
		],
		'svgDraw' => [
			'type' => 'string',
			'default' => 'delayed',	
			'scopy' => true,
		],
		'svgDura' => [
			'type' => 'string',
			'default' => '90',
			'scopy' => true,
		],
		'svgmaxWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-draw-svg{ max-width: {{svgmaxWidth}}; max-height: {{svgmaxWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'svgstroColor' => [
			'type' => 'string',
			'default' => '#000000',
			'scopy' => true,
		],
		'svgfillColor' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$plusButton_options, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-pricing-table', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_pricing_table_render_callback'
    ) );
}
add_action( 'init', 'tpgb_pricing_table' );