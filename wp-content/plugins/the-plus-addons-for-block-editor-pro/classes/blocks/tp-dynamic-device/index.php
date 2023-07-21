<?php
/* Block : Dynamic Device
 * @since : 1.4.2
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_dynamic_device_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$layoutType = (!empty($attributes['layoutType'])) ? $attributes['layoutType'] : 'normal';
	$deviceType = (!empty($attributes['deviceType'])) ? $attributes['deviceType'] : 'mobile';
	$mobileDevice = (!empty($attributes['mobileDevice'])) ? $attributes['mobileDevice'] : 'iphone-white-flat';
	$tabletDevice = (!empty($attributes['tabletDevice'])) ? $attributes['tabletDevice'] : 'ipad-vertical-white';
	$laptopDevice = (!empty($attributes['laptopDevice'])) ? $attributes['laptopDevice'] : 'laptop-macbook-black';
	$desktopDevice = (!empty($attributes['desktopDevice'])) ? $attributes['desktopDevice'] : 'desktop-imac-minimal';
	$customMedia = (!empty($attributes['customMedia'])) ? $attributes['customMedia'] : [];

	$cDeviceType = (!empty($attributes['cDeviceType'])) ? $attributes['cDeviceType'] : 'mobile';
	$cMobileDevice = (!empty($attributes['cMobileDevice'])) ? $attributes['cMobileDevice'] : 'iphone-white-flat';
	$cLaptopDevice = (!empty($attributes['cLaptopDevice'])) ? $attributes['cLaptopDevice'] : 'laptop-macbook-black';
	$cDesktopDevice = (!empty($attributes['cDesktopDevice'])) ? $attributes['cDesktopDevice'] : 'desktop-imac-minimal';
	$cCustomMedia = (!empty($attributes['cCustomMedia'])) ? $attributes['cCustomMedia'] : [];

	$contentType = (!empty($attributes['contentType'])) ? $attributes['contentType'] : 'image';
	$conImage = (!empty($attributes['conImage'])) ? $attributes['conImage'] : [];
	$onClickEfct = (!empty($attributes['onClickEfct'])) ? $attributes['onClickEfct'] : 'nothing';
	$onClickLink = (!empty($attributes['onClickLink']['url'])) ? $attributes['onClickLink']['url'] : '';
	$target = (!empty($attributes['onClickLink']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['onClickLink']['nofollow'])) ? 'nofollow' : '';

	$blockTemp = (!empty($attributes['blockTemp'])) ? $attributes['blockTemp'] : 'none';
	$conIframe = (!empty($attributes['conIframe'])) ? $attributes['conIframe'] : [];
	$showIcon = (!empty($attributes['showIcon'])) ? $attributes['showIcon'] : false;
	$iconSrc = (!empty($attributes['iconSrc'])) ? $attributes['iconSrc'] : [];
	$cConImg = (!empty($attributes['cConImg'])) ? $attributes['cConImg'] : [];

	$scrollDimage = (!empty($attributes['scrollDimage'])) ? $attributes['scrollDimage'] : false;
	$scrollManual = (!empty($attributes['scrollManual'])) ? $attributes['scrollManual'] : false;
	$dyDevConId = (!empty($attributes['dyDevConId'])) ? $attributes['dyDevConId'] : '';

	$rebHoverScroll = (!empty($attributes['rebHoverScroll'])) ? $attributes['rebHoverScroll'] : false;
	$dyDevRebConId = (!empty($attributes['dyDevRebConId'])) ? $attributes['dyDevRebConId'] : '';

	$iconConAni = (!empty($attributes['iconConAni'])) ? $attributes['iconConAni'] : false;
	$iconConHoverAnimation = (!empty($attributes['iconConHoverAnimation'])) ? $attributes['iconConHoverAnimation'] : false;
	$iconConAniStyle = (!empty($attributes['iconConAniStyle'])) ? $attributes['iconConAniStyle'] : 'pulse';

	$columnSlide = (!empty($attributes['columnSlide'])) ? $attributes['columnSlide'] : 'single';
	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$ddImages = TPGB_ASSETS_URL.'assets/images/dynamic-devices/';
	$shapeImage = $deviceTclass = $deviceNclass = $devUrlStart = $devUrlEnd =  $fancybox_settings = $FancyBoxJS= $continuesAniClass = '';
	if($layoutType=='normal'){
		if($deviceType=='mobile'){
			$shapeImage = $ddImages.$mobileDevice.'.png';
			$deviceNclass = $mobileDevice;
		}else if($deviceType=='tablet'){
			$shapeImage = $ddImages.$tabletDevice.'.png';
			$deviceNclass = $tabletDevice;
		}else if($deviceType=='laptop'){
			$shapeImage = $ddImages.$laptopDevice.'.png';
			$deviceNclass = $laptopDevice;
		}else if($deviceType=='desktop'){
			$shapeImage = $ddImages.$desktopDevice.'.png';
			$deviceNclass = $desktopDevice;
		}else if($deviceType=='custom' && !empty($customMedia) && !empty($customMedia['url'])){
			$shapeImage = $customMedia['url'];
			$deviceNclass = "custom-device-mockup";
		}
		$deviceTclass = 'device-type-'.$deviceType;

		if(!empty($iconConAni)){
			if(!empty($iconConHoverAnimation)){
				$continuesAniClass = 'tpgb-hover-'.$iconConAniStyle;
			}else{
				$continuesAniClass = 'tpgb-normal-'.$iconConAniStyle;
			}
		}
		
		if(($onClickEfct=='link' || $onClickEfct=='popup') && !empty($onClickLink)){
			if($onClickEfct=='popup'){
				$fancybox_settings = tpgb_dy_device_fancybox($attributes);
				$fancybox_settings = json_encode($fancybox_settings);
				
				$FancyBoxJS = 'data-fancybox="'.esc_attr($block_id).'"';
			}
			$devUrlStart = '<a class="tpgb-media-link" href="'.esc_url($onClickLink).'" target="'.esc_attr($target).'" nofollow="'.esc_attr($nofollow).'" '.$FancyBoxJS.'>';
			$devUrlEnd = '</a>';
		}
	}

	if($layoutType=='carousel'){
		if($cDeviceType=='mobile'){
			$shapeImage = $ddImages.$cMobileDevice.'.png';
		}else if($cDeviceType=='laptop'){
			$shapeImage = $ddImages.$cLaptopDevice.'.png';
		}else if($cDeviceType=='desktop'){
			$shapeImage = $ddImages.$cDesktopDevice.'.png';
		}else if($cDeviceType=='custom' && !empty($cCustomMedia) && !empty($cCustomMedia['url'])){
			$shapeImage = $cCustomMedia['url'];
		}
	}

	$getDDmedia = $mulConnClass = $ddattr = $rebTempScroll = $rebMulScrollClass = $rebMulScrollAttr = $scroll_class = '';
	$getDDmedia .= '<div class="tpgb-media-inner tpgb-dd-trans-cb">';
		$getDDmedia .= '<div class="tpgb-media-screen tpgb-dd-trans-cb">';
			$getDDmedia .= '<div class="tpgb-media-screen-inner">';
				if($contentType=='image' && !empty($conImage) && !empty($conImage['url'])){
					if($layoutType=='normal' && !empty($scrollDimage)){
						if(!empty($scrollManual)){
							$getDDmedia .= '<div class="creative-scroll-image tpgb-relative-block manual"><img src="'.esc_url($conImage['url']).'" /></div>';
						}else{
							if(!empty($dyDevConId)){
								$mulConnClass = "tpgb-dd-multi-connect ".$dyDevConId;
								$ddattr = ' data-connectdd="'.esc_attr($dyDevConId).'"';
							}
							$getDDmedia .= '<div class="creative-scroll-image tpgb-relative-block" style="background-image: url('.esc_url($conImage['url']).')"></div>';
						}
						$scroll_class = ' tpgb-img-scrl-enable';
					}else{
						$getDDmedia .= '<img class="tpgb-media-image" src="'.esc_url($conImage['url']).'"></img>';
					}
				}
				if($contentType=='iframe' && !empty($conIframe) && !empty($conIframe['url'])){
					$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Content Frame','tpgbp');
					$getDDmedia .= '<iframe width="100%" height="100%" frameborder="0" src="'.esc_url($conIframe['url']).'" title="'.$iframeTitle.'"></iframe>';
				}
				if($contentType=='reusableBlock' && !empty($blockTemp) && $blockTemp!='none'){
					if(!empty($rebHoverScroll)){
						if(!empty($dyDevRebConId)){
							$rebMulScrollClass = 'tpgb-mul-reb-connect '.$dyDevRebConId;
							$rebMulScrollAttr = ' data-connectdd="'.esc_attr($dyDevRebConId).'"';
						}else{
							$rebTempScroll = 'reusable-block-hover-scroll';
						}
					}
					ob_start();
						if(!empty($blockTemp)) {
							echo Tpgb_Library()->plus_do_block($blockTemp);
						}
						$getDDmedia .= ob_get_contents();
					ob_end_clean();
				}
				$getDDmedia .= $devUrlStart;
				if($contentType!='iframe' && !empty($showIcon) && !empty($iconSrc) && !empty($iconSrc['url'])){
					$getDDmedia .= '<div class="tpgb-device-icon">';
						$getDDmedia .= '<div class="tpgb-device-icon-inner '.esc_attr($continuesAniClass).'">';
							$getDDmedia .= '<img src="'.esc_url($iconSrc['url']).'"></img>';
						$getDDmedia .= '</div>';
					$getDDmedia .= '</div>';
				}
				$getDDmedia .= $devUrlEnd;
			$getDDmedia .= '</div>';
		$getDDmedia .= '</div>';
	$getDDmedia .= '</div>';

	//Carousel Options
	$count = $carouselClass = $carousel_settings = $columnClass = '';
	if($layoutType=='carousel'){
		$columnClass = 'column-'.$columnSlide;
		$carouselClass = 'tpgb-carousel splide';

		$cenpadding = isset( $attributes['centerPadding'] ) ? (array) $attributes['centerPadding'] : '';

		$carousel_settings = [
			'updateOnMove' => true,
			'autoplay' => isset( $attributes['slideAutoplay'] ) ? $attributes['slideAutoplay'] : false,
			'speed' => isset( $attributes['slideSpeed'] ) ? (int)$attributes['slideSpeed'] : 1500,
			'interval' => isset( $attributes['slideAutoplaySpeed'] ) ? (int)$attributes['slideAutoplaySpeed'] : '',
			'drag' => true ,
			'focus' => 'center' ,
			'type' => !empty( $attributes['slideInfinite'] ) ? 'loop' : 'slide',
			'pauseOnHover' => isset( $attributes['slideHoverPause'] ) ? $attributes['slideHoverPause'] : false,
			'pagination' => isset( $attributes['showDots']['md'] ) ? $attributes['showDots']['md'] : false ,
			'padding' =>  isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '',
			'arrows' => ( !empty($attributes['showArrows']['md']) || !empty($attributes['showArrows']['sm']) || !empty($attributes['showArrows']['xs']) ) ? true : false,
			'breakpoints' => [
				'1024' => [
					'pagination' => ( !isset($attributes['showDots']['sm']) ) ? $attributes['showDots']['md'] : ( isset($attributes['showDots']['sm'])  ? $attributes['showDots']['sm'] : false ) ,
					'padding' => ( !isset( $cenpadding['sm']) ) ? (isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '') : ( isset($cenpadding['sm'])  ? $cenpadding['sm'] : '' ),
					'drag' => true,
					'focus' => 'center' ,
				],
				'767' => [
					'pagination' => ( !isset($attributes['showDots']['xs']) ) ? ( (!isset($attributes['showDots']['sm'])) ? $attributes['showDots']['md'] : $attributes['showDots']['sm'] ) : (isset($attributes['showDots']['xs']) ? $attributes['showDots']['xs'] : false),
					'padding' =>  ( !isset($cenpadding['xs']) ) ? ( (!isset($cenpadding['sm'])) ? (isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '') : $cenpadding['sm'] ) : (isset($cenpadding['xs']) ? $cenpadding['xs'] : ''),
					'drag' => true ,
					'focus' => 'center' ,
				]
			],
		];
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

	$output = $scrolljsclass = '';
    $output .= '<div class="tpgb-dynamic-device tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($deviceNclass).' '.esc_attr($deviceTclass).' '.esc_attr($blockClass).' '.esc_attr($mulConnClass).' '.esc_attr($rebTempScroll).' '.esc_attr($rebMulScrollClass).'" '.$ddattr.' '.$rebMulScrollAttr.' data-id="'.esc_attr($block_id).'" data-fancy-option=\''.$fancybox_settings.'\'>';
		$output .= '<div class="tpgb-device-inner tpgb-dd-trans-cb">';
			if($layoutType=='normal'){
				if(!empty($scrollDimage)){
					$scrolljsclass = 'tpgb-scroll-img-js';
				}
				$output .= '<div class="tpgb-device-content tpgb-dd-trans-cb '.esc_attr($scrolljsclass).'">';
					$output .= '<div class="tpgb-device-shape tpgb-dd-trans-cb">';
					if(!empty($shapeImage)){
						$output .= '<img class="tpgb-device-image tpgb-dd-trans-cb" src="'.esc_url($shapeImage).'" />';
					}
					$output .= '</div>';
					$output .= '<div class="tpgb-device-media tpgb-dd-trans-cb '.esc_attr($scroll_class).'">';
						$output .= $getDDmedia;
					$output .= '</div>';
				$output .= '</div>';
			}else if($layoutType=='carousel'){
				$output .= '<div class="tpgb-carousel-device-mokeup tpgb-dd-trans-cb">';
					$output .= '<div class="tpgb-device-content tpgb-dd-trans-cb">';
					if(!empty($shapeImage)){
						$output .= '<img class="tpgb-device-image tpgb-dd-trans-cb" src="'.esc_url($shapeImage).'" />';
					}
					$output .= '</div>';
				$output .= '</div>';

				$output .= '<div class="tpgb-device-carousel '.esc_attr($columnClass).' '.esc_attr($carouselClass).' '.esc_attr($Sliderclass).'" '.$carousel_settings.' >';
					if(( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
						$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
					}
					$output .= '<div class="splide__track ">';
						$output .= '<div class="splide__list">';
							if(!empty($cConImg)){
								foreach ( $cConImg as $index => $item ) {
									$count++;
									$output .= '<div class="splide__slide tpgb-device-slide tpgb-dd-trans-cb" data-index="'.esc_attr($count).'">';
										$output .= '<img src="'.esc_url($item['url']).'" />';
									$output .= '</div>';
								}
							}
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}

		$output .= '</div>';
		if($layoutType=='carousel'){
			$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
			if( !empty($arrowCss) ){
				$output .= $arrowCss;
			}
		}
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	
    return $output;
}

function tpgb_dy_device_fancybox($attr){
	$FancyData = (!empty($attr['FancyOption'])) ? json_decode($attr['FancyOption']) : [];

	$button = array();
	if (is_array($FancyData) || is_object($FancyData)) {
		foreach ($FancyData as $value) {
			$button[] = $value->value;
		}
	}

	$fancybox = array();
	$fancybox['loop'] = $attr['LoopFancy'];
	$fancybox['infobar'] = $attr['infobar'];
	$fancybox['arrows'] = $attr['ArrowsFancy'];
	$fancybox['animationEffect'] = $attr['AnimationFancy'];
	$fancybox['animationDuration'] = $attr['DurationFancy'];
	$fancybox['transitionEffect'] = $attr['TransitionFancy'];
	$fancybox['transitionDuration'] = $attr['TranDuration'];
	$fancybox['button'] = $button;
	
	return $fancybox;
}

/**
 * Render for the server-side
 */
function tpgb_dynamic_device() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'layoutType' => [
			'type' => 'string',
			'default' => 'normal',
		],
		'deviceType' => [
			'type' => 'string',
			'default' => 'mobile',
		],
		'mobileDevice' => [
			'type' => 'string',
			'default' => 'iphone-white-flat',
		],
		'tabletDevice' => [
			'type' => 'string',
			'default' => 'ipad-vertical-white',
		],
		'laptopDevice' => [
			'type' => 'string',
			'default' => 'laptop-macbook-black',
		],
		'desktopDevice' => [
			'type' => 'string',
			'default' => 'desktop-imac-minimal',
		],
		'customMedia' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'contentType' => [
			'type' => 'string',
			'default' => 'image',
		],
		'conImage' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'onClickEfct' => [
			'type' => 'string',
			'default' => 'nothing'
		],
		'onClickLink' => [
			'type'=> 'object',
			'default'=>[
				'url' => '',	
				'target' => '',	
				'nofollow' => ''	
			]
		],
		'blockTemp' => [
			'type' => 'string',
			'default' => 'none'
		],
		'backendVisi' => [
			'type' => 'boolean',
			'default' => false
		],
		'conIframe' => [
			'type'=> 'object',
			'default'=>[
				'url' => '',	
				'target' => '',	
				'nofollow' => ''	
			]
		],
		'iframeTitle' => [
			'type' => 'string',
			'default' => ''
		],
		'showIcon' => [
			'type' => 'boolean',
			'default' => false
		],
		'iconSrc' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],

		'cDeviceType' => [
			'type' => 'string',
			'default' => 'mobile',
		],
		'cMobileDevice' => [
			'type' => 'string',
			'default' => 'iphone-white-flat',
		],
		'cLaptopDevice' => [
			'type' => 'string',
			'default' => 'laptop-macbook-black',
		],
		'cDesktopDevice' => [
			'type' => 'string',
			'default' => 'desktop-imac-minimal',
		],
		'cCustomMedia' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'cConImg' => [
			'type' => 'array',
			'default' => [
				[ 
					'url' => '',
					'Id' => '',
				],
			],
		],

		'deviceWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-dynamic-device  { width: {{deviceWidth}}; margin: 0 auto; text-align: center; display: block; }',
				]
			],
			'scopy' => true,
		],
		'ddAlignment' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ddAlignment', 'relation' => '==', 'value' => 'left' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-dynamic-device { float: left; margin-left: 0; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'ddAlignment', 'relation' => '==', 'value' => 'center' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-dynamic-device { margin: 0 auto; float: unset; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'ddAlignment', 'relation' => '==', 'value' => 'right' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-dynamic-device { float: right; margin-right: 0; }',
				],
			],
			'scopy' => true,
		],
		'deviceMargin' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} { margin : {{deviceMargin}} }',
				]
			],
			'scopy' => true,
		],
		'devicePadding' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} { padding : {{devicePadding}} }',
				]
			],
			'scopy' => true,
		],

		'iconConAni' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'iconConAniStyle' => [
			'type' => 'string',
			'default' => 'pulse',	
			'scopy' => true,
		],
		'iconConHoverAnimation' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'iconConAniDuration' => [
			'type' => 'string',
			'default' => '2',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconConAniStyle', 'relation' => '==', 'value' => 'pulse' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-normal-pulse, {{PLUS_WRAP}} .tpgb-hover-pulse:hover { animation-duration: {{iconConAniDuration}}s; -webkit-animation-duration: {{iconConAniDuration}}s; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'iconConAniStyle', 'relation' => '==', 'value' => 'floating' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-normal-floating, {{PLUS_WRAP}} .tpgb-hover-floating:hover { animation-duration: {{iconConAniDuration}}s; -webkit-animation-duration: {{iconConAniDuration}}s; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'iconConAniStyle', 'relation' => '==', 'value' => 'tossing' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-normal-tossing, {{PLUS_WRAP}} .tpgb-hover-tossing:hover{ animation-duration: {{iconConAniDuration}}s; -webkit-animation-duration: {{iconConAniDuration}}s; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'iconConAniStyle', 'relation' => '==', 'value' => 'rotating' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-normal-rotating, {{PLUS_WRAP}} .tpgb-hover-rotating:hover{ animation-duration: {{iconConAniDuration}}s; -webkit-animation-duration: {{iconConAniDuration}}s; }',
				],
			],
			'scopy' => true,
		],
		'iconBdrRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-device-icon-inner, {{PLUS_WRAP}} .tpgb-device-icon-inner > img { border-radius : {{iconBdrRadius}} }',
				]
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
					'selector' => '{{PLUS_WRAP}} .tpgb-device-icon-inner > img { max-width: {{iconWidth}}; }',
				]
			],
			'scopy' => true,
		],

		'FancyOption' => [
			'type' => 'string',
			'default' => '[]',
			'scopy' => true,
		],
		'LoopFancy' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'infobar' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'ArrowsFancy' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'TitleFancy' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'AnimationFancy' => [
			'type' => 'string',
			'default' => 'zoom',
			'scopy' => true,
		],
		'DurationFancy' => [
			'type' => 'string',
			'default' => 366,
			'scopy' => true,
		],
		'TransitionFancy' => [
			'type' => 'string',
			'default' => 'slide',
			'scopy' => true,
		],
		'TranDuration' => [
			'type' => 'string',
			'default' => 366,
			'scopy' => true,
		],
		'ThumbsOption' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'ThumbsBrCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'onClickEfct', 'relation' => '==', 'value' => 'popup']],
					'selector' => '.fancybox-thumbs__list a.fancybox-thumbs-active:before,.fancybox-thumbs__list a:before',
				],
			],
			'scopy' => true,
		],
		'ThumbsBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'onClickEfct', 'relation' => '==', 'value' => 'popup']],
					'selector' => '.fancybox-thumbs .fancybox-thumbs__list',
				],
			],
			'scopy' => true,
		],

		'rebTopOffset' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-device-media { margin-top: {{rebTopOffset}}; }',
				]
			],
			'scopy' => true,
		],
		'rebLeftOffset' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-device-media { margin-left: {{rebLeftOffset}}; }',
				]
			],
			'scopy' => true,
		],
		'rebHoverScroll' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'rebTranDur' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'rebHoverScroll', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-container-row, {{PLUS_WRAP}} .tpgb-section { transition-duration: {{rebTranDur}}s; }',
				]
			],
			'scopy' => true,
		],
		'dyDevRebConId' => [
			'type' => 'string',
			'default' => '',
		],
		
		'imgWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}}.custom-device-mockup .tpgb-device-media img { width: {{imgWidth}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.device-type-custom.custom-device-mockup .tpgb-device-inner .tpgb-img-scrl-enable { width: {{imgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'imgHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}}.custom-device-mockup .tpgb-device-media img { height: {{imgHeight}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.device-type-custom.custom-device-mockup .tpgb-device-inner .tpgb-img-scrl-enable { height: {{imgHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'imgTopOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}}.custom-device-mockup .tpgb-device-media img { margin-top: {{imgTopOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.device-type-custom.custom-device-mockup .tpgb-device-inner .tpgb-img-scrl-enable { margin-top: {{imgTopOff}}; }',
				],
			],
			'scopy' => true,
		],
		'imgLeftOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}}.custom-device-mockup .tpgb-device-media img { margin-left: {{imgLeftOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ], ['key' => 'scrollDimage', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.device-type-custom.custom-device-mockup .tpgb-device-inner .tpgb-img-scrl-enable { margin-left: {{imgLeftOff}}; }',
				],
			],
			'scopy' => true,
		],
		'imgZindex' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ] , ['key' => 'deviceType', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-media { z-index: {{imgZindex}}; }',
				]
			],
			'scopy' => true,
		],

		'scrollDimage' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'scrollTranDur' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'scrollDimage', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-media-inner .creative-scroll-image { transition-duration: {{scrollTranDur}}s; }',
				]
			],
			'scopy' => true,
		],
		'scrollManual' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'dyDevConId' => [
			'type' => 'string',
			'default' => '',
		],
		'imgBdrRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-media img { border-radius : {{imgBdrRadius}} }',
				]
			],
			'scopy' => true,
		],
		'outerBdrRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image { border-radius : {{outerBdrRadius}} }',
				]
			],
			'scopy' => true,
		],

		'scrollBarWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar{ width: {{scrollBarWidth}}; }',
				],
			],
			'scopy' => true,
		],
		
		'thumbBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'thumbRadius' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-thumb{border-radius: {{thumbRadius}};}',
				],
			],
			'scopy' => true,
		],
		'thumbShadow' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'trackBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		'trackRadius' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-track{border-radius: {{trackRadius}};}',
				],
			],
			'scopy' => true,
		],
		'trackShadow' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'normal' ],['key' => 'contentType', 'relation' => '==', 'value' => 'image' ],['key' => 'scrollManual', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .creative-scroll-image::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],

		'cImgBdrRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-carousel .tpgb-device-slide img { border-radius : {{cImgBdrRadius}} }',
				]
			],
			'scopy' => true,
		],
		'cImgWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-slide img { width: {{cImgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'cImgHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-slide img { height: {{cImgHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'cImgTopOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-slide img { margin-top: {{cImgTopOff}}; }',
				],
			],
			'scopy' => true,
		],
		'cImgLeftOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-slide img { margin-left: {{cImgLeftOff}}; }',
				],
			],
			'scopy' => true,
		],
		'cImgZindex' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-carousel { z-index: {{cImgZindex}}; }',
				]
			],
			'scopy' => true,
		],

		'shapeNshadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'typeShadow' => 'drop-shadow', 
				'horizontal' => 2,
				'vertical' => 3,
				'blur' => 2,
				'color' => "rgba(0,0,0,0.5)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-carousel-device-mokeup, {{PLUS_WRAP}} .tpgb-device-shape',
				],
			],
			'scopy' => true,
		],
		'shapeHshadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'typeShadow' => 'drop-shadow', 
				'horizontal' => 2,
				'vertical' => 3,
				'blur' => 2,
				'color' => "rgba(0,0,0,0.5)",
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}:hover .tpgb-carousel-device-mokeup, {{PLUS_WRAP}}:hover .tpgb-device-shape',
				],
			],
			'scopy' => true,
		],
		'centerPadding' => [
			'type' => 'object',
			'default' => (object)[ 'md' => 0,'sm' => 0,'xs' => 0 ],
			'scopy' => true,
		],
		'centerSlideEffect' => [
			'type' => 'string',
			'default' => 'none',
			'scopy' => true,
		],
		'centerslideScale' => [
			'type' => 'string',
			'default' => 1,
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ] ],
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > img{-webkit-transform: scale({{centerslideScale}});-moz-transform: scale({{centerslideScale}});-ms-transform: scale({{centerslideScale}});-o-transform: scale({{centerslideScale}});transform: scale({{centerslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
				],
			],
			'scopy' => true,
		],
		'normalslideScale' => [
			'type' => 'string',
			'default' => 1,
			'style' => [
				(object) [
					'condition' => [ (object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ] ],
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide  > img{-webkit-transform: scale({{normalslideScale}});-moz-transform: scale({{normalslideScale}});-ms-transform: scale({{normalslideScale}});-o-transform: scale({{normalslideScale}});transform: scale({{normalslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
				],
			],
			'scopy' => true,
		],
		'slideOpacity' => [
			'type' => 'object',
			'default' => (object)[ 'md' => 1,'sm' => 1,'xs' => 1 ],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide:not(.is-active) > img{opacity:{{slideOpacity}} !important;}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
				],
			],
			'scopy' => true,
		],
		'slideBoxShadow' => [
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
					'condition' => [ (object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'shadow' ] ],
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > img',
				],
			],
			'scopy' => true,
		],

		'cSlideListMargin' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-device-inner .tpgb-device-carousel .splide__track { margin : {{cSlideListMargin}} }',
				]
			],
			'scopy' => true,
		],
		'cSlideSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-device-carousel .tpgb-device-slide.splide__slide { margin-top: {{cSlideSpace}}; margin-bottom: {{cSlideSpace}}; }',
				]
			],
			'scopy' => true,
		],
		'carouselWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-carousel-device-mokeup, .tpgb-device-carousel.column-single { max-width: {{carouselWidth}}; }
					{{PLUS_WRAP}} .tpgb-device-carousel .tpgb-device-slide.splide__slide { width: {{carouselWidth}} !important; }',
				]
			],
			'scopy' => true,
		],
		'mockUpWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-device-inner .tpgb-carousel-device-mokeup, {{PLUS_WRAP}} .tpgb-device-inner .tpgb-device-carousel.column-single { max-width: {{mockUpWidth}}; }',
				]
			],
			'scopy' => true,
		],
		'mockUpHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-carousel-device-mokeup, {{PLUS_WRAP}} .tpgb-device-carouselcolumn-single { max-height: {{mockUpHeight}}; min-height: {{mockUpHeight}}; }',
				]
			],
			'scopy' => true,
		],
		'mockUpOffset' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-carousel-device-mokeup { top: {{mockUpOffset}}; }',
				]
			],
			'scopy' => true,
		],

		'columnSlide' => [
			'type' => 'string',
			'default' => 'single',
			'scopy' => true,
		],
		'slideSpeed' => [
			'type' => 'string',
			'default' => 1500,
			'scopy' => true,
		],
		'slideColumnSpace' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-device-carousel .tpgb-device-slide.splide__slide {padding: {{slideColumnSpace}};}',
				],
			],
			'scopy' => true,
		],
		'slideInfinite' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'slideAutoplay' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'slideAutoplaySpeed' => [
			'type' => 'string',
			'default' => 1500,
			'scopy' => true,
		],
		'showDots' => [
			'type' => 'object',
			'default' => [ 'md' => true ],
			'scopy' => true,
		],
		'dotsStyle' => [
			'type' => 'string',
			'default' => 'style-1',
			'scopy' => true,
		],
		'dotSize' => [
			'type' => 'object',
			'default' => [ 'md' => 0,'sm' => 0,'xs' => 0,'unit' => 'px' ],
			'style' => [
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '!=', 'value' => 'style-5' ], [ 'key' => 'dotsStyle', 'relation' => '!=', 'value' => 'style-7' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} ul.splide__pagination li, {{PLUS_WRAP}} ul.splide__pagination li button{ width: {{dotSize}}; height: {{dotSize}}; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-4' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-4 .splide__pagination li button::before{ width: {{dotSize}}; height: {{dotSize}}; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-5' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} ul.splide__pagination li{ height: {{dotSize}}; } {{PLUS_WRAP}} ul.splide__pagination li button{ width: {{dotSize}}; height: {{dotSize}}; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-7' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-7 .splide__pagination li button{ width: {{dotSize}}; }',
				],
			],
			'scopy' => true,
		],
		'dotASize' => [
			'type' => 'object',
			'default' => [ 'md' => 0,'sm' => 0,'xs' => 0,'unit' => 'px' ],
			'style' => [
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-2' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-2 ul.splide__pagination li button::after{ width: {{dotASize}}; height: {{dotASize}}; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-5' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-5 .splide__pagination li button.is-active, {{PLUS_WRAP}} .dots-style-5 .splide__pagination li:hover button { width: {{dotASize}} !important; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-6' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-6 .splide__pagination li button:after { font-size: {{dotASize}}; }',
				],
				(object) [
					'condition' => [ (object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => 'style-7' ], [ 'key' => 'showDots', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .dots-style-7 .splide__pagination li:hover button, {{PLUS_WRAP}} .dots-style-7 .splide__pagination li button.is-active { width: {{dotASize}}; }',
				],
			],
			'scopy' => true,
		],
		'dotSpace' => [
			'type' => 'object',
			'default' => [ 'md' => 0,'sm' => 0,'xs' => 0,'unit' => 'px' ],
			'style' => [
					(object) [
					'condition' => [ 
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} ul.splide__pagination li, {{PLUS_WRAP}} .splide--ltr.dots-style-2 ul.splide__pagination li, {{PLUS_WRAP}} .dots-style-2 .splide__pagination li button { margin: 0 {{dotSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'dotsTopSpace' => [
			'type' => 'object',
			'default' => [ 'md' => 0,'sm' => 0,'xs' => 0,'unit' => 'px' ],
			'style' => [
					(object) [
					'condition' => [ 
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} ul.splide__pagination{ bottom: {{dotsTopSpace}} !important;}',
				],
			],
			'scopy' => true,
		],
		'slideHoverDots' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'showArrows' => [
			'type' => 'object',
			'default' => [ 'md' => false ],
			'scopy' => true,
		],
		'arrowsStyle' => [
			'type' => 'string',
			'default' => 'style-1',
			'scopy' => true,
		],
		'arrowsPosition' => [
			'type' => 'string',
			'default' => 'top-right',
			'scopy' => true,
		],
		
		'outerArrows' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'slideHoverArrows' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'dotsBorderColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-6'] ],
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .dots-style-1 ul.splide__pagination li button.splide__pagination__page{-webkit-box-shadow:inset 0 0 0 8px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};box-shadow: inset 0 0 0 8px {{dotsBorderColor}};} {{PLUS_WRAP}} .dots-style-1 ul.splide__pagination li button.splide__pagination__page.is-active{-webkit-box-shadow:inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}} .dots-style-2 ul.splide__pagination li button.splide__pagination__page, {{PLUS_WRAP}} .dots-style-6 .splide__pagination button{border: 1px solid {{dotsBorderColor}};}{{PLUS_WRAP}} .dots-style-3 .splide__pagination li button{-webkit-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}} .dots-style-3 .splide__pagination li button.is-active{-webkit-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};box-shadow: inset 0 0 0 8px {{dotsBorderColor}};}{{PLUS_WRAP}} .dots-style-4 .splide__pagination li button::before{-webkit-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}} .dots-style-1 ul.splide__pagination li button.splide__pagination__page{background: transparent;color: {{dotsBorderColor}};}',
				],
			],
			'scopy' => true,
		],
		'dotsBgColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-3','style-4','style-5','style-7'] ],
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}} .dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}} .dots-style-3 .splide__pagination li button,{{PLUS_WRAP}} .dots-style-4 .splide__pagination li button::before,{{PLUS_WRAP}} .dots-style-5 .splide__pagination li button,{{PLUS_WRAP}} .dots-style-7 .splide__pagination li button{background:{{dotsBgColor}};}',
				],
			],
			'scopy' => true,
		],
		'dotsActiveBorderColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-6'] ],
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .dots-style-2 .splide__pagination li button.is-active::after{border-color: {{dotsActiveBorderColor}};}{{PLUS_WRAP}} .dots-style-4 .splide__pagination li button.is-active{-webkit-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};}{{PLUS_WRAP}} .dots-style-6 .splide__pagination button::after{color: {{dotsActiveBorderColor}};}',
				],
			],
			'scopy' => true,
		],
		'dotsActiveBgColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [ 
						(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-5','style-7'] ],
						(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .dots-style-2 .splide__pagination li button.is-active::after,{{PLUS_WRAP}} .dots-style-4 .splide__pagination li button.is-active,{{PLUS_WRAP}} .dots-style-5 .splide__pagination li:hover button,{{PLUS_WRAP}} .dots-style-5 .splide__pagination li button.is-active,{{PLUS_WRAP}} .dots-style-7 .splide__pagination li button.is-active{background: {{dotsActiveBgColor}};}',
				],
			],
			'scopy' => true,
		],
		
		'arrowsBgColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'arrowsStyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-6'] ],
						(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6:before{background:{{arrowsBgColor}};}{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4 .icon-wrap{border-color:{{arrowsBgColor}}}',
				],
			],
			'scopy' => true,
		],
		'arrowsIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6 .icon-wrap svg{color:{{arrowsIconColor}};}{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2 .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2 .icon-wrap:after,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5 .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5 .icon-wrap:after{background:{{arrowsIconColor}};}',
				],
			],
			'scopy' => true,
		],
		'arrowsHoverBgColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'arrowsStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4'] ],
						(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:hover,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3:hover .icon-wrap{background:{{arrowsHoverBgColor}};}{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover:before,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover .icon-wrap{border-color:{{arrowsHoverBgColor}};}',
				],
			],
			'scopy' => true,
		],
		'arrowsHoverIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
					(object) [
					'condition' => [
						(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
					],
					'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:hover:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3:hover .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6:hover .icon-wrap svg{color:{{arrowsHoverIconColor}};}{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover .icon-wrap:after,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5:hover .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5:hover .icon-wrap:after{background:{{arrowsHoverIconColor}};}',
				],
			],
			'scopy' => true,
		],
	);

	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-dynamic-device', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_dynamic_device_render_callback'
    ) );
}
add_action( 'init', 'tpgb_dynamic_device' );