<?php
/* Block : Creative Image
 * @since : 1.2.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_creative_image_callback( $settings, $content) {
	
	$block_id	= !empty($settings['block_id']) ? $settings['block_id'] : '';
	$fancyBox = (!empty($settings['fancyBox'])) ? $settings['fancyBox'] : false;
	$floatAlign = !empty($settings['floatAlign']) ? $settings['floatAlign'] : '';
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $settings );
	
	// Float Align Class
	if(!empty($floatAlign) && $floatAlign!='none'){
		$blockClass .= 'tpgb-image-'.esc_attr($floatAlign);
	}

	$contentImage = $imgID ='';
	if ( isset( $settings['SelectImg']['id'] ) && !empty($settings['SelectImg']['id'])) {
		$imgID = $settings['SelectImg']['id'];
	}
	if ( ! empty( $settings['SelectImg']['url'] ) && isset( $settings['SelectImg']['id'] ) ) {
		$attr = array( 'class' => "hover__img info_img" );
		$contentImage = wp_get_attachment_image($imgID, $settings['ImgSize'],"",$attr);				
	} else { 
		$contentImage .= tpgb_loading_image_grid(get_the_ID());
	}
	
	$href = $target = $rel = '';
	if (!empty($settings['link']['url'])) {
		$href  = ($settings['link']['url'] !== '' ) ? $settings['link']['url'] : ''; 
		$target  = (!empty($settings['link']['target'])) ? 'target="_blank"' : ''; 
		$rel = (!empty($settings['link']['rel'])) ? 'rel="nofollow"' : '';
	}

	$maskImage='';
	if(!empty($settings["showMaskImg"])){
		$maskImage=' tpgb-creative-mask-media';
	}
	$wrapperClass='tpgb-creative-img-wrap '.esc_attr($maskImage);

	$dataImage='';
	$fancyImg = TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
	if(isset($settings['SelectImg']['id'])) {
		$fullImage = wp_get_attachment_image_src( $imgID, 'full' );
		$fancyImg= isset($fullImage[0]) ? $fullImage[0] : '';
		$dataImage = (!empty($fullImage) && !empty($fullImage[0])) ? 'background: url('.esc_url($fullImage[0]).');' : '';
	} else {
		$dataImage = tpgb_loading_image_grid('','background');
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
	
	if ( ! empty( $settings['link']['url'] ) ) {
		$link_attr = Tp_Blocks_Helper::add_link_attributes($settings['link']);
		$ariaLabelT = (!empty($settings['ariaLabel'])) ? esc_attr($settings['ariaLabel']) : esc_attr__('Creative Image','tpgb');
		$html = '<a href="'.esc_url($href).'" '.$target.' '.$rel.' class="'.esc_attr($wrapperClass).'" '.$link_attr.' aria-label="'.$ariaLabelT.'">' .$contentImage. '</a>';
	} else {
		$tag = !empty($fancyBox) && empty($settings['ScrollParallax']) ? 'a' : 'div';
		$fancyAttr =  !empty($fancyBox) ? 'href= "'.esc_url($fancyImg).'" data-fancybox="fancyImg-'.esc_attr($block_id).'"' : '';
		
		$html = '<'.Tp_Blocks_Helper::validate_html_tag($tag).' class="' . esc_attr($wrapperClass) . '" '.$fancyAttr.'>' .$contentImage. '</'.Tp_Blocks_Helper::validate_html_tag($tag).'>';
	}

	$uid=uniqid('bg-image');
	$cssRule=$cssData=$animatedClass='';

	if(!empty($settings["showMaskImg"]) && !empty($settings['MaskImg']['url'])) {
		$cssData .= '.' . esc_attr( $uid ) . '.tpgb-animate-image .tpgb-creative-img-wrap.tpgb-creative-mask-media{mask-image: url('.esc_url($settings['MaskImg']['url']).');-webkit-mask-image: url('.esc_url($settings['MaskImg']['url']).');}';
	}
	$cssClass = '';
	$cssClass = ' text-' . esc_attr($settings["Alignment"]['md']) . ' '.esc_attr($animatedClass);
	$cssClass .= (!empty($settings["Alignment"]['sm'])) ? ' text-tablet-' . esc_attr($settings["Alignment"]['sm']) : '';
	$cssClass .= (!empty($settings["Alignment"]['xs'])) ? ' text-mobile-' . esc_attr($settings["Alignment"]['xs']) : '';

	$uidWidget = uniqid("plus");
	$output = '<div id="'.esc_attr($uidWidget).'" class="tpgb-creative-image tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		$output .= '<div class="tpgb-anim-img-parallax tpgb-relative-block" >';
			$output .= '<div class="tpgb-animate-image '.esc_attr($uid).' ' . trim( $cssClass ) . ' '.(!empty($fancyBox) ? 'tpgb-fancy-add' : '').'" '.$data_settings.'>
				<figure>' . $html . '</figure>
				</div>';
		$output .= '</div>';
	$output .= '</div>';
	
	$cssRule='';
	if(!empty($cssData)){
		$cssRule='<style>';
		$cssRule .= $cssData;
		$cssRule .= '</style>';
	}
	
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
			'ScrollImgEffect' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'showMaskImg' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'floatAlign' => [
				'type' => 'string',
				'default' => 'none',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img, {{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover, {{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover',
					],
				],
				'scopy' => true,
            ],
			'borderRadius' => [
				'type' => 'object',
				'default' => (object)[ 'md' => (object)['top' => '','right' => '','left' => '','bottom' => '',],],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap{border-radius: {{borderRadius}};}'
					],
				],
				'scopy' => true,
			],
			'borderRadiusHover' => [
				'type' => 'object',
				'default' => (object)[ 'md' => (object)['top' => '','right' => '','left' => '','bottom' => '',],],
				'style' => [
					(object) [
                        'selector' => '{{PLUS_WRAP}}.tpgb-creative-image .tpgb-animate-image img:hover,{{PLUS_WRAP}}.tpgb-creative-image .scroll-image-wrap:hover{border-radius: {{borderRadiusHover}};}'
					],
				],
				'scopy' => true,
			],
			'shadow' => [
				'type' => 'object',
				'default' => ['openShadow' => 0,],
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