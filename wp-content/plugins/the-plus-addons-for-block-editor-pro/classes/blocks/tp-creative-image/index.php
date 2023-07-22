<?php
/* Block : Creative Image
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_creative_image_callback( $settings, $content) {
	
	$block_id	= !empty($settings['block_id']) ? $settings['block_id'] : '';
	$showCaption	= !empty($settings['showCaption']) ? $settings['showCaption'] : false;
	$ImgCaption	= !empty($settings['ImgCaption']) ? $settings['ImgCaption'] : '';
	$fancyBox = (!empty($settings['fancyBox'])) ? $settings['fancyBox'] : false;
	$floatAlign = !empty($settings['floatAlign']) ? $settings['floatAlign'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $settings );
	$AnimDirection = $settings['AnimDirection'];

	// Float Align Class
	if(!empty($floatAlign) && $floatAlign!='none'){
		$blockClass .= 'tpgb-image-'.esc_attr($floatAlign);
	}

	$contentImage = $imgID ='';
	if ( isset( $settings['SelectImg']['id'] ) && !empty($settings['SelectImg']['id'])) {
		$imgID = $settings['SelectImg']['id'];
	}
	
	if ( ! empty( $settings['SelectImg']['url'] ) && ! empty( $settings['SelectImg']['id'] ) ) {
		$attr = array( 'class' => "hover__img info_img" );
		$contentImage = wp_get_attachment_image($imgID, $settings['ImgSize'],"",$attr);				
	}else if ( ! empty( $settings['SelectImg']['url'] ) ) {
		$contentImage .= '<img src="'.esc_url($settings['SelectImg']['url']).'" class="hover__img info_img" />';
	} else {
		$contentImage .= tpgb_loading_image_grid(get_the_ID());
	}
	
	$scrollImage='';
	if(!empty($settings["ScrollImgEffect"])) {
		$fancyImg = (isset($settings['SelectImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings['SelectImg']) : (!empty($settings['SelectImg']['url']) ? $settings['SelectImg']['url'] : '');
		$contentImage = '<div class="creative-scroll-image" style="background-image: url('.esc_url($fancyImg).')"></div>';
		$scrollImage = 'scroll-image-wrap';
	}
	
	$href = $target = $rel = '';
	$href  = (isset($settings['link']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings['link']) : (!empty($settings['link']['url']) ? $settings['link']['url'] : ''); 
	$target  = (!empty($settings['link']['target'])) ? 'target="_blank"' : ''; 
	$rel = (!empty($settings['link']['rel'])) ? 'rel="nofollow"' : '';
	

	$maskImage='';
	if(!empty($settings["showMaskImg"])){
		$maskImage=' tpgb-creative-mask-media';
	}
	$wrapperClass='tpgb-creative-img-wrap '.esc_attr($maskImage).' '.esc_attr($scrollImage);

	$dataImage = '';
	$fancyImg = TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
	if( !empty($settings["ScrollRevelImg"]) || !empty($fancyBox) ){
		if(!empty($settings['SelectImg']['id'])) {
			$fullImage = wp_get_attachment_image_src( $imgID, 'full' );
			$fancyImg= isset($fullImage[0]) ? $fullImage[0] : '';
			$dataImage = (!empty($fullImage)) ? 'background: url('.esc_url($fullImage[0]).');' : '';
		}else if(!empty($settings['SelectImg']['url'])){
			$fancyImg = (isset($settings['SelectImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings['SelectImg']) : (!empty($settings['SelectImg']['url']) ? $settings['SelectImg']['url'] : '');
			$fullImage = '<img src="'.esc_url($fancyImg).'" />';
			$dataImage = (!empty($fancyImg)) ? 'background: url('.esc_url($fancyImg).');' : '';
		} else {
			$dataImage = tpgb_loading_image_grid('','background');
		}
	}
	
	$data_settings = '';
	if(!empty($fancyBox)){
		$FancyData = (!empty($settings['FancyOption'])) ? json_decode($settings['FancyOption']) : [];

		$button = array();
		if (is_array($FancyData) || is_object($FancyData)) {
			foreach ($FancyData as $value) {
				$button[] = $value->value;
			}
		}
		$fancybox = array();
		$fancybox['button'] = $button;
		$fancybox['animationEffect'] = $settings['AnimationFancy'];
		$fancybox['animationDuration'] = $settings['DurationFancy'];
		$data_settings .= ' data-fancy-option=\''.json_encode($fancybox).'\'';
		$data_settings .= ' data-id="'.esc_attr($block_id).'"';
	}
	
	if ( !empty( $href ) ) {
		$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($settings['link']);
		$ariaLabelT = (!empty($settings['ariaLabel'])) ? esc_attr($settings['ariaLabel']) : esc_attr__('Creative Image','tpgbp');
		if(!empty($settings["ScrollRevelImg"])) {			
			$html = '<a href="'.esc_url($href).'" '.$target.' '.$rel.' class="'.esc_attr($wrapperClass).' tpgb-bg-animate-img '.esc_attr($AnimDirection).'" style="'.$dataImage.'" '.$link_attr.' aria-label="'.$ariaLabelT.'">' .$contentImage. '</a>';
		} else {
			$html = '<a href="'.esc_url($href).'" '.$target.' '.$rel.' class="'.esc_attr($wrapperClass).'"  '.$link_attr.' aria-label="'.$ariaLabelT.'">' .$contentImage. '</a>';
		}
	} else {
		$tag = !empty($fancyBox) && empty($settings['ScrollParallax']) ? 'a' : 'div';
		$fancyAttr =  !empty($fancyBox) ? 'href= "'.esc_url($fancyImg).'" data-fancybox="fancyImg-'.esc_attr($block_id).'"' : '';
		
		if(!empty($settings["ScrollRevelImg"])) {			
			$html = '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($tag).' class="' . esc_attr($wrapperClass) . ' tpgb-bg-animate-img '.esc_attr($AnimDirection).'" style="'.$dataImage.'" '.$fancyAttr.'>' .$contentImage. '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($tag).'>';
		} else {
			$html = '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($tag).' class="' . esc_attr($wrapperClass) . '" '.$fancyAttr.'>' .$contentImage. '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($tag).'>';
		}
	}

	$uid=uniqid('bg-image');
	$cssRule=$cssData=$animatedClass='';

	if(!empty($settings["ScrollRevelImg"])) {
		$bgAnimated    = ' tpgb-bg-img-anim ';
		$bgAnim        = ' tpgb-bg-img-animated ';
		$animatedClass = ' animate-general';
		$cssData       = '.' . esc_js ( $uid ) . ' .tpgb-bg-animate-img:after{background:' . esc_js ( $settings[ "AnimBgColor" ] ) . ';}';
	} else {
		$bgAnimated = $bgAnim = '';
	}
	if(!empty($settings["showMaskImg"]) && !empty($settings['MaskImg']['url'])) {
		$maskImg = (isset($settings['MaskImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings['MaskImg']) : (!empty($settings['MaskImg']['url']) ? $settings['MaskImg']['url'] : '');
		$cssData .= '.' . esc_js ( $uid ) . '.tpgb-animate-image .tpgb-creative-img-wrap.tpgb-creative-mask-media{mask-image: url('.esc_url($maskImg).');-webkit-mask-image: url('.esc_url($maskImg).');}';
	}
	$cssClass = '';
	$cssClass = ' text-' . $settings["Alignment"]['md'] . ' '.esc_attr($animatedClass);
	$cssClass .= (!empty($settings["Alignment"]['sm'])) ? ' text-tablet-' . esc_attr($settings["Alignment"]['sm']) : '';
	$cssClass .= (!empty($settings["Alignment"]['xs'])) ? ' text-mobile-' . esc_attr($settings["Alignment"]['xs']) : '';

	$parallaxImageScroll = '';
	if(!empty($settings['ScrollParallax'])) {
		$parallaxImageScroll = 'section-parallax-img';
		$html .='<figure class="tpgb-creative-img-parallax" data-scroll-parallax-x="'.esc_attr($settings["ScrollMoveX"]).'" data-scroll-parallax-y="'.esc_attr($settings["ScrollMoveY"]).'"><figure class="tpgb-parallax-img-parent"><div class="tpgb-parallax-img-container">';
		$imageUrl = (!empty($imgID)) ? wp_get_attachment_image( $imgID, 'full', false, ['class' => 'tpgb-simple-parallax-img']) : '';
		if(!empty($imageUrl)){
			$imageUrl = $imageUrl;
		}else{
			$imageUrl = '<img class="tpgb-simple-parallax-img" src="'.esc_url($settings['SelectImg']['url']).'"  title="">';
		}
		$html .= $imageUrl;
		$html .='</div></figure></figure>';
	}

	$ImageCaption ='';
	if(!empty($showCaption) && !empty($ImgCaption)){
		$ImageCaption .= '<figcaption class="tpgb-img-caption">'.wp_kses_post($ImgCaption).'</figcaption>';
	}
	
	$uidWidget = uniqid("plus");
	$output = '<div id="'.esc_attr($uidWidget).'" class="tpgb-creative-image tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr( $blockClass ).'">';
		$output .= '<div class="tpgb-anim-img-parallax tpgb-relative-block" >';
			$output .= '<div class="tpgb-animate-image '.esc_attr($uid).' ' .  trim( $cssClass ) . ' '.esc_attr($bgAnim).' '.(!empty($fancyBox) ? 'tpgb-fancy-add' : '').'" '.$data_settings.'>
				<figure class="'.esc_attr($parallaxImageScroll).' '.esc_attr($bgAnimated).' ">
						' . $html . '								
				</figure>
				'.$ImageCaption.'
			</div>';
		$output .= '</div>';
		$cssRule='';
		if(!empty($cssData)){
			$cssRule='<style>';
			$cssRule .= $cssData;
			$cssRule .= '</style>';
		}
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($settings, $output);
	
	return $cssRule.$output;
}

function tpgb_tp_creative_image_render() {
	
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'SelectImg' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg'
				],
			],
			'ScrollRevelImg' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'AnimBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ScrollRevelImg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-bg-animate-img:after{background:{{AnimBgColor}};}'
					],
				],
				'scopy' => true,
			],
			'AnimDirection' => [
				'type' => 'string',
				'default' => 'left',	
				'scopy' => true,
			],
			'ImgSize' => [
				'type' => 'string',
				'default' => 'full',
			],
			'Alignment' => [
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'scopy' => true,
			],
			'link' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',	
					'target' => '',	
					'nofollow' => ''
				],
			],
			'ariaLabel' => [
				'type' => 'string',
				'default' => '',	
			],
			'ImgWidth' => [
				'type' => 'object',
				'default' => ['md' => '',"unit" => 'px'],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-animate-image .tpgb-creative-img-wrap img,{{PLUS_WRAP}} .tpgb-animate-image .scroll-image-wrap,{{PLUS_WRAP}} .tpgb-animate-image figure:not(.tpgb-parallax-img-parent):not(.tpgb-creative-img-parallax){max-width: {{ImgWidth}};width:100%;}'
					],
				],
				'scopy' => true,
			],
			'showCaption' => [
				'type' => 'boolean',
				'default' => false,
			],
			'ImgCaption' => [
				'type' => 'string',
				'default' => 'Credit : Gutenberg, WordPress',
			],
			'floatAlign' => [
				'type' => 'string',
				'default' => 'none',
			],
			'captionTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showCaption', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-animate-image figcaption.tpgb-img-caption',
					],
				],
				'scopy' => true,
			],
			'captionNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showCaption', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-animate-image figcaption.tpgb-img-caption{color: {{captionNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'captionHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showCaption', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-animate-image:hover figcaption.tpgb-img-caption{color: {{captionHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'ScrollImgEffect' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'ScrollImgHeight' => [
				'type' => 'object',
				'default' => [
					'md' => 400,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ScrollImgEffect', 'relation' => '==', 'value' => TRUE]],
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap .creative-scroll-image{ min-height: {{ScrollImgHeight}}px; }',
					],
				],
				'scopy' => true,
			],
			'ScrollTransDur' => [
				'type' => 'object',
				'default' => [
					'md' => 2,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ScrollImgEffect', 'relation' => '==', 'value' => TRUE]],
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap .creative-scroll-image{ transition: background-position {{ScrollTransDur}}s ease-in-out;-webkit-transition: background-position {{ScrollTransDur}}s ease-in-out; }',
					],
				],
				'scopy' => true,
			],
			'showMaskImg' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'MaskImg' => [
				'type' => 'object',
				'default' => [
                    'url' => TPGB_ASSETS_URL. 'assets/images/team-mask.png'
				],
			],
			'MaskShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-animate-image',
					],
				],
				'scopy' => true,
			],
			'ScrollParallax' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'ScrollMoveX' => [
				'type' => 'string',
				'default' => '120',
				'scopy' => true,
			],
			'ScrollMoveY' => [
				'type' => 'string',
				'default' => '0',
				'scopy' => true,
			],
            'border' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
					'color' => '',
					'width' => (object) [
						'md' => (object)[
								'top' => '',
								'left' => '',
								'bottom' => '',
								'right' => '',
						],
						"unit" => "",
					],
				],
                'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ScrollParallax', 'relation' => '!=', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img, {{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'ScrollParallax', 'relation' => '==', 'value' => true]],
						'selector' => ' {{PLUS_WRAP}}.tpgb-creative-image .section-parallax-img',
					],
				],
				'scopy' => true,
            ],
			'borderHover' => [
					'type' => 'object',
					'default' => (object) [
						'openBorder' => 0,
						'type' => '',
						'color' => '',
						'width' => (object) [
							'md' => (object)[
									'top' => '',
									'left' => '',
									'bottom' => '',
									'right' => '',
							],
							"unit" => "",
						],
					],
					'style' => [
						(object) [
						'condition' => [(object) ['key' => 'ScrollParallax', 'relation' => '!=', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover, {{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'ScrollParallax', 'relation' => '==', 'value' => true]],
						'selector' => ' {{PLUS_WRAP}}.tpgb-creative-image .section-parallax-img:hover',
					],
				],
				'scopy' => true,
            ],
			'borderRadius' => [
				'type' => 'object',
				'default' => (object)[ 'md' => (object)['top' => '','right' => '','left' => '','bottom' => '',],],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap,{{PLUS_WRAP}}.tpgb-creative-image .section-parallax-img{border-radius: {{borderRadius}};}'
					],
				],
				'scopy' => true,
			],
			'borderRadiusHover' => [
				'type' => 'object',
				'default' => (object)[ 'md' => (object)['top' => '','right' => '','left' => '','bottom' => '',],],
				'style' => [
					(object) [
                        'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover,{{PLUS_WRAP}}.tpgb-creative-image .section-parallax-img:hover{border-radius: {{borderRadiusHover}};}'
					],
				],
				'scopy' => true,
			],
			'shadow' => [
				'type' => 'object',
				'default' => ['openShadow' => 0],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap',
					],
				],
				'scopy' => true,
			],
			'nmlDropShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap',
					],
				],
				'scopy' => true,
			],
			'shadowHover' => [
				'type' => 'object',
				'default' => ['openShadow' => 0,],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'hvrDropShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'imgNFilter' => [
				'type' => 'object',
				'default' =>  [
					'openFilter' => false,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} img.hover__img',
					],
				],
				'scopy' => true,
			],
			'imgHFilter' => [
				'type' => 'object',
				'default' =>  [
					'openFilter' => false,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} img.hover__img:hover',
					],
				],
				'scopy' => true,
			],

			'fancyBox' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'FancyOption' => [
				'type' => 'string',
        		'default' => '[]',
				'scopy' => true,
			],
			'AnimationFancy' => [
				'type' => 'string',
				'default' => 'zoom',
				'scopy' => true,
			],
			'DurationFancy' => [
				'type' => 'string',
				'default' => 300,
				'scopy' => true,
			],
		);
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-creative-image', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_creative_image_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_creative_image_render' );

function tpgb_loading_image_grid($postid = '', $type = '') {
	global $post;
	$contentImage = '';
	if($type!='background'){		
		$imageUrl = TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
		$contentImage = '<img src="'.esc_url($imageUrl).'" alt="'.esc_attr(get_the_title()).'"/>';
		return $contentImage;
	} elseif($type == 'background') {
		$imageUrl = TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
		$dataSrc = "background:url(".esc_url($imageUrl).") #f7f7f7;";
		return $dataSrc;
	}
}