<?php
/* Block : Flip Box
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_flipbox_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$layoutType = (!empty($attributes['layoutType'])) ? $attributes['layoutType'] : 'listing';
	$flipType = (!empty($attributes['flipType'])) ? $attributes['flipType'] : 'horizontal';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'icon';
	$iconStore = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : '';
	$iconStyle = (!empty($attributes['iconStyle'])) ? $attributes['iconStyle'] : 'none';
	$svgIcon = (!empty($attributes['svgIcon'])) ? $attributes['svgIcon'] : '';
	$imagestore = (!empty($attributes['imagestore'])) ? $attributes['imagestore'] : '';
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'thumbnail';
	$titleTag = (!empty($attributes['titleTag'])) ? $attributes['titleTag'] : 'div';
	$title = (!empty($attributes['title'])) ? $attributes['title'] : '';
	$description = (!empty($attributes['description'])) ? $attributes['description'] : '';
	
	$backBtn = (!empty($attributes['backBtn'])) ? $attributes['backBtn'] : false;
	$backCarouselBtn = (!empty($attributes['backCarouselBtn'])) ? $attributes['backCarouselBtn'] : false;
	
	$flipcarousel = (!empty($attributes['flipcarousel'])) ? $attributes['flipcarousel'] : [];
	
	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	$backAlign = (!empty($attributes['backAlign'])) ? $attributes['backAlign'] : 'center';
	
	$svgDraw = (!empty($attributes['svgDraw'])) ? $attributes['svgDraw'] : 'delayed';
	$svgstroColor = (!empty($attributes['svgstroColor'])) ? $attributes['svgstroColor'] : '';
	$svgfillColor = (!empty($attributes['svgfillColor'])) ? $attributes['svgfillColor'] : 'none';
	$svgDura = (!empty($attributes['svgDura'])) ? $attributes['svgDura'] : 90;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	//Carousel Options
	$count = '';
	$carouselClass = '';
	$carousel_settings = '';
	if($layoutType=='carousel'){
		$carouselClass = 'tpgb-carousel splide';
		$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );
		$carousel_settings = 'data-splide=\'' . json_encode($carousel_settings) . '\'';
	}
	
	$Sliderclass = '';
	if($slideHoverDots==true && ( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
		$Sliderclass .= ' hover-slider-dots';
	}
	if($outerArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' outer-slider-arrow';
	}
	if($slideHoverArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' hover-slider-arrow';
	}
	if( $layoutType=='carousel' && ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
		$Sliderclass .= ' dots-'.esc_attr($dotsStyle);
	}
	
	//img src
	if(!empty($imagestore) && !empty($imagestore['id'])){
		$counter_img = $imagestore['id'];
		$imgSrc = wp_get_attachment_image($counter_img , $imageSize, false, ['class' => 'service-img']);
	}else if(!empty($imagestore['url'])){
		$imgSrc = '<img src="'.esc_url($imagestore['url']).'" class="service-img" alt="'.esc_attr__('FlipBox','tpgbp').'"/>';
	}else{
		$imgSrc = '';
	}
			
	$output = '';
    $output .= '<div class="tpgb-flipbox tpgb-relative-block '.esc_attr($carouselClass).' '.esc_attr($Sliderclass).' list-'.esc_attr($layoutType).' flip-box-style-1 tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" '.$carousel_settings.'>';
		if($layoutType=='listing'){
			$output .= '<div class="flip-box-inner content_hover_effect ">';
				$output .= '<div class="flip-box-bg-box">';
					$output .= '<div class="service-flipbox flip-'.esc_attr($flipType).' height-full">';
						$output .= '<div class="service-flipbox-holder height-full text-center perspective bezier-1">';
							$output .= '<div class="service-flipbox-front bezier-1 no-backface origin-center">';
								$output .= '<div class="service-flipbox-content width-full">';
									if($iconType=='icon'){
										$output .= '<span class="service-icon tpgb-trans-linear icon-'.esc_attr($iconStyle).'">';
											$output .= '<i class="'.esc_attr($iconStore).'"></i>';
										$output .= '</span>';
									}
									if($iconType=='img' && !empty($imagestore)){
										$output .= $imgSrc;
									}
									if($iconType=='svg' && !empty($svgIcon) && !empty($svgIcon['url']) ){
										$output .= '<div class="tpgb-draw-svg" data-id="service-svg-'.esc_attr($block_id).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes">';
											$output .= '<object id="service-svg-'.esc_attr($block_id).'" type="image/svg+xml" role="none" data="'.esc_url($svgIcon['url']).'">';
											$output .= '</object>';
										$output .= '</div>';
									}
									$output .= '<div class="service-content">';
										$output .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).' class="service-title tpgb-trans-linear">'.wp_kses_post($title).'</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).'>';
									$output .= '</div>';
								$output .= '</div>';
								$output .= '<div class="flipbox-front-overlay tpgb-trans-linear"></div>';
							$output .= '</div>';
							$output .= '<div class="service-flipbox-back fold-back-'.esc_attr($flipType).' no-backface bezier-1 origin-center text-'.esc_attr($backAlign).'">';
								$output .= '<div class="service-flipbox-content width-full">';
									$output .= '<div class="service-desc tpgb-trans-linear">'.wp_kses_post($description).'</div>';
									if(!empty($backBtn)){
										$output .= tpgb_getButtonRender($attributes);
									}
								$output .= '</div>';
								$output .= '<div class="flipbox-back-overlay tpgb-trans-linear"></div>';
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		}
		if($layoutType=='carousel'){
			if( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
				$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
			}
			$output .= '<div class="splide__track post-loop-inner">';
				$output .= '<div class="splide__list">';
					if(!empty($flipcarousel)){
						foreach ( $flipcarousel as $index => $item ) {
							$count++;
							$output .= '<div class="splide__slide flip-box-inner content_hover_effect tp-repeater-item-'.esc_attr($item['_key']).'" data-index="'.esc_attr($count).'">';
								$output .= '<div class="flip-box-bg-box">';
									$output .= '<div class="service-flipbox flip-'.esc_attr($flipType).'" height-full"}>';
										$output .= '<div class="service-flipbox-holder height-full text-center perspective bezier-1">';
											$output .= '<div class="service-flipbox-front bezier-1 no-backface origin-center">';
												$output .= '<div class="service-flipbox-content width-full">';
													if($item['iconType']=='icon'){
														$output .= '<span class="service-icon tpgb-trans-linear icon-'.esc_attr($iconStyle).'"></i>';
															$output .= '<i class="'.esc_attr($item['iconStore']).'"></i>';
														$output .= '</span>';
													}
													if($item['iconType']=='img' && !empty($item['imagestore'])){
														$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'thumbnail';
														$imgSrc ='';
														if(!empty($item['imagestore']['id'])){
															$imgSrc = wp_get_attachment_image($item['imagestore']['id'] , $imageSize, false, ['class' => 'service-img']);
														}else if(!empty($item['imagestore']['url'])){
															$imgUrl = (isset($item['imagestore']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['imagestore']) : (!empty($item['imagestore']['url']) ? $item['imagestore']['url'] : '');
															$imgSrc = '<img src="'.esc_url($imgUrl).'" class="service-img" alt="'.esc_attr__('FlipBox','tpgbp').'"/>';
														}
														$output .= $imgSrc;
													}
													if($item['iconType']=='svg' && isset($item['svgFIcon']) && isset($item['svgFIcon']['url'])){
														$svgUrl = (isset($item['svgFIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['svgFIcon']) : (!empty($item['svgFIcon']['url']) ? $item['svgFIcon']['url'] : '');
														$output .= '<div class="tpgb-draw-svg" data-id="service-svg-'.esc_attr($item['_key']).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes">';
															$output .= '<object id="service-svg-'.esc_attr($item['_key']).'" type="image/svg+xml" role="none" data="'.esc_url($svgUrl).'">';
															$output .= '</object>';
														$output .= '</div>';
													}
													$output .= '<div class="service-content">';
														$output .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).' class="service-title tpgb-trans-linear">'.wp_kses_post($item['title']).'</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).'>';
													$output .= '</div>';
												$output .= '</div>';
												$output .= '<div class="flipbox-front-overlay tpgb-trans-linear"></div>';
											$output .= '</div>';
											$output .= '<div class="service-flipbox-back fold-back-'.esc_attr($flipType).' no-backface bezier-1 origin-center text-'.esc_attr($backAlign).'">';
												$output .= '<div class="service-flipbox-content width-full">';
													$output .= '<div class="service-desc tpgb-trans-linear">'.wp_kses_post($item['description']).'</div>';
													if(!empty($backCarouselBtn)){
														$output .=tpgb_getButtonRender($attributes,$item['btnUrl'],$item['btnText']);
													}
												$output .= '</div>';
												$output .= '<div class="flipbox-back-overlay tpgb-trans-linear"></div>';
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						}
					}
				$output .= '</div>';
			$output .= '</div>';
		}
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	if($layoutType=='carousel'){
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		if( !empty($arrowCss) ){
			$output .= $arrowCss;
		}
	}
    return $output;
}

function tpgb_getButtonRender($attributes,$itemBtnUrl='',$itemBtnText=''){
	$layoutType = (!empty($attributes['layoutType'])) ? $attributes['layoutType'] : 'listing';
	$btnStyle = (!empty($attributes['btnStyle'])) ? $attributes['btnStyle'] : 'style-7';
	$btnCarouselStyle = (!empty($attributes['btnCarouselStyle'])) ? $attributes['btnCarouselStyle'] : 'style-7';
	$btnIconType = (!empty($attributes['btnIconType'])) ? $attributes['btnIconType'] : 'none';
	$btnCarouselIconType = (!empty($attributes['btnCarouselIconType'])) ? $attributes['btnCarouselIconType'] : 'none';
	$btnIconName = (!empty($attributes['btnIconName'])) ? $attributes['btnIconName'] : '';
	$btnCarouselIconName = (!empty($attributes['btnCarouselIconName'])) ? $attributes['btnCarouselIconName'] : '';
	$btnIconPosition = (!empty($attributes['btnIconPosition'])) ? $attributes['btnIconPosition'] : 'after';
	$btnCarouselIconPosition = (!empty($attributes['btnCarouselIconPosition'])) ? $attributes['btnCarouselIconPosition'] : 'after';
	$btnText = (!empty($attributes['btnText'])) ? $attributes['btnText'] : '';
	$btnUrl = (!empty($attributes['btnUrl'])) ? $attributes['btnUrl'] : '';
	
	$NewBtnText = ($layoutType=='carousel') ? $itemBtnText : $btnText;
	$getBtnText = '<div class="btn-text">'.wp_kses_post($NewBtnText).'</div>';
	
	$getbutton = '';
	
	$NewBtnStyle = ($layoutType=='carousel') ? $btnCarouselStyle : $btnStyle;
	$NewBtnType = ($layoutType=='carousel' ) ? $btnCarouselIconType : $btnIconType;
	$NewBtnIconPosition = ($layoutType=='carousel' ) ? $btnCarouselIconPosition : $btnIconPosition;
	$NewBtnIconName = ($layoutType=='carousel' ) ? $btnCarouselIconName : $btnIconName;
	$NewBtnUrl = ($layoutType=='carousel') ? $itemBtnUrl : $btnUrl;
	$btnlink = (isset($NewBtnUrl['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($NewBtnUrl) : (!empty($NewBtnUrl['url']) ? $NewBtnUrl['url'] : '');
	$target = (!empty($NewBtnUrl['target']) ? '_blank' : '' ) ;
	$nofollow = (!empty($NewBtnUrl['nofollow'])) ? 'nofollow' : '';
	$btn_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($NewBtnUrl);
	$getbutton .= '<div class="tpgb-adv-button button-'.esc_attr($NewBtnStyle).'">';
		$getbutton .= '<a href="'.esc_url($btnlink).'" class="button-link-wrap" role="button" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$btn_attr.'>';
		if($NewBtnStyle == 'style-8'){
			if($NewBtnIconPosition == 'before'){
				if($NewBtnType == 'icon'){
					$getbutton .= '<span class="btn-icon  button-'.esc_attr($NewBtnIconPosition).'">';
						$getbutton .= '<i class="'.esc_attr($NewBtnIconName).'"></i>';
					$getbutton .= '</span>';
				}
				$getbutton .= $getBtnText;
			}
			if($NewBtnIconPosition == 'after'){
				$getbutton .= $getBtnText;
				if($NewBtnType == 'icon'){
					$getbutton .= '<span class="btn-icon  button-'.esc_attr($NewBtnIconPosition).'">';
						$getbutton .= '<i class="'.esc_attr($NewBtnIconName).'"></i>';
					$getbutton .= '</span>';
				}
			}
		}
		if($NewBtnStyle == 'style-7' || $NewBtnStyle == 'style-9' ){
			$getbutton .= $getBtnText;
			
			$getbutton .= '<span class="button-arrow">';
			if($NewBtnStyle == 'style-7'){
				$getbutton .= '<span class="btn-right-arrow"><i class="fas fa-chevron-right"></i></span>';
			}
			if($NewBtnStyle == 'style-9'){
				$getbutton .= '<i class="btn-show fas fa-chevron-right"></i>';
				$getbutton .= '<i class="btn-hide fas fa-chevron-right"></i>';
			}
			$getbutton .= '</span>';
		}
		$getbutton .= '</a>';
	$getbutton .= '</div>';
	return $getbutton;
}

/**
 * Render for the server-side
 */
function tpgb_flipbox() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'layoutType' => [
			'type' => 'string',
			'default' => 'listing',	
		],
		'flipType' => [
			'type' => 'string',
			'default' => 'horizontal',	
		],
		'boxHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-flipbox .flip-box-inner .service-flipbox, {{PLUS_WRAP}}.tpgb-flipbox .flip-box-inner .service-flipbox-front, {{PLUS_WRAP}}.tpgb-flipbox .flip-box-inner .service-flipbox-back{ min-height: {{boxHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'backAlign' => [
			'type' => 'string',
			'default' => 'center',
		],
		'backCarouselBtn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'btnCarouselStyle' => [
			'type' => 'string',
			'default' => 'style-7',	
		],
		'btnCarouselIconType'  => [
			'type' => 'string' ,
			'default' => 'none',	
		],
			
		'btnCarouselIconName' => [
			'type'=> 'string',
			'default'=> '',
		],
		'btnCarouselIconPosition' => [
			'type'=> 'string',
			'default'=> 'after',
		],
		'flipcarousel' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'title' => [
						'type' => 'string',
						'default' => 'Special Feature'
					],
					'description' => [
						'type' => 'string',
						'default' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.'
					],
					'btnText' => [
						'type' => 'string',
						'default' => 'Read more',	
					],
					'btnUrl' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
					],
					'iconType' => [
						'type' => 'string',
						'default' => 'icon'
					],
					'iconStore' => [
						'type'=> 'string',
						'default' => 'fas fa-box-open'
					],
					'imagestore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'svgFIcon' => [
						'type' => 'object',
						'default' => [
							'url' => '',
						],
					],
					'nmlBG' => [
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
								'selector' => '{{PLUS_WRAP}} .flip-box-inner{{TP_REPEAT_ID}} .service-flipbox-front',
							],
						],
					],
					'hvrBG' => [
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
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .service-flipbox-back',
							],
						],
					],
					'overNmlBG' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .flipbox-front-overlay{ background: {{overNmlBG}}; }',
							],
						],
					],
					'overHvrBG' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .flipbox-back-overlay{ background: {{overHvrBG}}; }',
							],
						],
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'title' => 'Special Feature 1',
					'description' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.',
					'iconType' => 'icon',
					'iconStore'=> 'fas fa-box-open',
					'btnText'=> 'Read More',
					'btnUrl' => ['url'  => ''],
					'imagestore' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'svgFIcon' => [
						'url' => '',
					],
				],
				[
					'_key' => '1',
					'title' => 'Special Feature 2',
					'description' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.',
					'iconType' => 'icon',
					'iconStore'=> 'fas fa-box-open',
					'btnText'=> 'Read More',
					'btnUrl' => ['url'  => ''],
					'imagestore' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'svgFIcon' => [
						'url' => '',
					],
				] 
			]
		],
		'title' => [
			'type' => 'string',
			'default' => 'Special Feature',	
		],
		'iconType' => [
			'type' => 'string',
			'default' => 'icon',	
		],
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-box-open',
		],
		'imagestore' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'svgIcon' => [
			'type' => 'object',
			'default' => [
				'url' => ''
			],
		],
		'imageSize' => [
			'type' => 'string',
			'default' => 'thumbnail',	
		],
		'description' => [
			'type' => 'string',
			'default' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.',	
		],
		'backBtn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'btnStyle' => [
			'type' => 'string',
			'default' => 'style-7',	
		],
		'btnText' => [
			'type' => 'string',
			'default' => 'Read more',	
		],
		'btnUrl' => [
			'type'=> 'object',
			'default'=> [
				'url' => '',
				'target' => '',
				'nofollow' => ''
			],
		],
		'btnIconType'  => [
			'type' => 'string' ,
			'default' => 'none',
		],
			
		'btnIconName' => [
			'type'=> 'string',
			'default'=> '',
		],
		'btnIconPosition' => [
			'type'=> 'string',
			'default'=> 'after',
		],
		
		'iconStyle' => [
			'type' => 'string',
			'default' => 'none',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ font-size: {{iconSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ font-size: {{iconSize}}; }',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ color: {{icnNmlColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ color: {{icnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{ color: {{icnHvrColor}}; }',
				],
				(object) [ 
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{ color: {{icnHvrColor}}; }',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon',
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
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ border-color: {{nmlBColor}}; }',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{ border-color: {{nmlBColor}}; }',
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
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{ border-color: {{hvrBColor}}; }',
				],
				(object) [ 
					'condition' => [
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{ border-color: {{hvrBColor}}; }',
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
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{border-radius: {{nmlIcnBRadius}};}',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon{border-radius: {{nmlIcnBRadius}};}',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{border-radius: {{hvrIcnBRadius}};}',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ],(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon{border-radius: {{hvrIcnBRadius}};}',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-icon',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-icon',
				],
			],
			'scopy' => true,
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
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .tpgb-draw-svg{ max-width: {{svgmaxWidth}}; max-height: {{svgmaxWidth}}; }',
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
		'imgWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ],(object)['key' => 'iconType', 'relation' => '==', 'value' => 'img' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-img{ max-width: {{imgWidth}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-img{ max-width: {{imgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		
		'titleTag' => [
			'type' => 'string',
			'default' => 'div',
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
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-title',
				],
			],
			'scopy' => true,
		],
		'titleNmlColor' => [
			'type' => 'string',
			'default' => '#313131',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-title{ color: {{titleNmlColor}}; }',
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
					'selector' => '{{PLUS_WRAP}} .flip-horizontal:hover .service-title{ color: {{titleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleTopSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.flip-box-style-1 .flip-box-inner .service-title{ margin-top: {{titleTopSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'titleBottomSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.flip-box-style-1 .flip-box-inner .service-title{ margin-bottom: {{titleBottomSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'descTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'description', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-desc',
				],
			],
			'scopy' => true,
		],
		'descColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'description', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-desc{ color: {{descColor}}; }',
				],
			],
			'scopy' => true,
		],
		'backBtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'backBtnTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap{ color: {{backBtnTextColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap{ color: {{backBtnTextColor}}; }',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true],
						['key' => 'btnStyle' , 'relation' => '==', 'value' => 'style-7']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-7 .button-link-wrap:after{ border-color: {{backBtnTextColor}}; }',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true],
						['key' => 'btnCarouselStyle' , 'relation' => '==', 'value' => 'style-7']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-7 .button-link-wrap:after{ border-color: {{backBtnTextColor}}; }',
				],
			],
			'scopy' => true,
		],
		'backBThoverColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover{ color: {{backBThoverColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover{ color: {{backBThoverColor}}; }',
				],
			],
			'scopy' => true,
		],
		'backBtnSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-top: {{backBtnSpace}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-top: {{backBtnSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'backBtnbottomSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-bottom : {{backBtnbottomSpace}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-bottom : {{backBtnbottomSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconSpacing' => [
			'type' => 'object',
			'default' => [ 
				'md' => 5,
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .button-before { margin-right: {{btnIconSpacing}}; } {{PLUS_WRAP}} .button-link-wrap .button-after { margin-left: {{btnIconSpacing}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .button-before { margin-right: {{btnIconSpacing}}; } {{PLUS_WRAP}} .button-link-wrap .button-after { margin-left: {{btnIconSpacing}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .btn-icon { font-size: {{btnIconSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .btn-icon { font-size: {{btnIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'backBtnPadding' => [
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
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' =>true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{ padding: {{backBtnPadding}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' =>true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{ padding: {{backBtnPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'backBtnNormalB' => [
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
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'backBtnBRadius' => [
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
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{border-radius: {{backBtnBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{border-radius: {{backBtnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'backBtnBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'backBtnShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => 0,
				'vertical' => 8,
				'blur' => 20,
				'spread' => 1,
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'backBtnHvrB' => [
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
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'backBtnHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover{border-radius: {{backBtnHvrBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover{border-radius: {{backBtnHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'backBtnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'backBtnHvrShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => '',
				'vertical' => '',
				'blur' => '',
				'spread' => '',
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'backBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'backCarouselBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'bgBorder' => [
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
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-front, {{PLUS_WRAP}} .flip-box-inner .service-flipbox-back',
				],
			],
			'scopy' => true,
		],
		'bgBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-front, {{PLUS_WRAP}} .flip-box-inner .service-flipbox-back, {{PLUS_WRAP}} .flipbox-front-overlay, {{PLUS_WRAP}} .flipbox-back-overlay{border-radius: {{bgBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'normalBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-front',
				],
			],
			'scopy' => true,
		],
		'hoverBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ]],
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-back',
				],
			],
			'scopy' => true,
		],
		'overlayNmlBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ]],
					'selector' => '{{PLUS_WRAP}} .flipbox-front-overlay{ background: {{overlayNmlBG}}; }',
				],
			],
			'scopy' => true,
		],
		'overlayHvrBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'listing' ]],
					'selector' => '{{PLUS_WRAP}} .flipbox-back-overlay{ background: {{overlayHvrBG}}; }',
				],
			],
			'scopy' => true,
		],
		'bgNmlShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => true,
				'inset' => 0,
				'horizontal' => 2,
				'vertical' => 1,
				'blur' => 30,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.10)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-front',
				],
			],
			'scopy' => true,
		],
		'bgHvrShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => true,
				'inset' => 0,
				'horizontal' => 2,
				'vertical' => 1,
				'blur' => 30,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.10)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .flip-box-inner .service-flipbox-back',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-flipbox', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_flipbox_render_callback'
    ) );
}
add_action( 'init', 'tpgb_flipbox' );