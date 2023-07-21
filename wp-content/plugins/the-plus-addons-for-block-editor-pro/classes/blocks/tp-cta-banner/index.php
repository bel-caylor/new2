<?php
/* Block : CTA Banner
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_cta_banner_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] :'';
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$contentHoverEffect = (!empty($attributes['contentHoverEffect'])) ? $attributes['contentHoverEffect'] : false;
	$selectHoverEffect = (!empty($attributes['selectHoverEffect'])) ? $attributes['selectHoverEffect'] : '';
	$hoverStyle = (!empty($attributes['hoverStyle'])) ? $attributes['hoverStyle'] : 'cta_img_blur';
	$extBtnshow = (!empty($attributes['extBtnshow'])) ? $attributes['extBtnshow'] : false;
	$subTitle = (!empty($attributes['subTitle'])) ? $attributes['subTitle'] : '';
	$Title = (!empty($attributes['Title'])) ? $attributes['Title'] : '';
	$bannerImage  =  (!empty($attributes['bannerImage'])) ? $attributes['bannerImage'] : [] ;
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'full';
	$desc = (!empty($attributes['desc'])) ? $attributes['desc'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	// Set Size of banner image 
	if(!empty($bannerImage) && !empty($bannerImage['id'])){
		$banner_img = $bannerImage['id'];
		$imgRender = wp_get_attachment_image($banner_img , $imageSize,false, ['class' => 'banner-img']);
		$imgSrc = wp_get_attachment_image_src($banner_img , $imageSize);
		$imgSrc = ( isset($imgSrc[0]) && !empty($imgSrc[0]) ) ? $imgSrc[0] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
	}else if(!empty($bannerImage['url'])){
		$imgRender = '<img class="banner-img" src="'.esc_url($bannerImage['url']).'"  alt="'.esc_html__('banner image','tpgbp').'" />';
		$imgSrc = $bannerImage['url'];
	}else{
		$imgRender = '<img class="banner-img" src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'"  alt="'.esc_html__('banner image','tpgbp').'" />';
		$imgSrc = TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
	}
	
	//Set text wrap class on change style type

	$text_wrap_style = "";	
	if($styleType=='style-1'){ 	
		$text_wrap_style = 'top-left';
	}else if($styleType=='style-2'){ 
		$text_wrap_style = 'center-left';
	}else if($styleType=='style-3'){ 
		$text_wrap_style = 'bottom-left';
	}else if($styleType=='style-4'){ 
		$text_wrap_style = 'top-right text-right';
	}else if($styleType=='style-5'){ 
		$text_wrap_style = 'center-right text-right';
	}else if($styleType=='style-6'){ 
		$text_wrap_style = 'bottom-right text-right';
	}else if($styleType=='style-7'){ 
		$text_wrap_style = 'text-center';
	}else if($styleType=='style-8'){ 
		$text_wrap_style = 'bottom-right';
	}

	// Get Title of Banner
	$getTitle = '';
	if(!empty($Title)){
		$getTitle .= '<h3 class="cta-title tpgb-trans-easeinout">'. wp_kses_post($Title) .'</h3>';
	}

	// Get SubTitle of Banner
	$getSubtitle = '';
	if(!empty($subTitle)){
		$getSubtitle .= '<h4 class="cta-subtitle tpgb-trans-easeinout">'.wp_kses_post($subTitle).'</h4>';
	}
	//Set Description
	$getDesc = '';
	if(!empty($desc)){
		$getDesc .= '<div class="cta-desc tpgb-trans-easeinout">'.wp_kses_post($desc).'</div>';
	}

	//Get Button 
	$getbutton = '';
	$getbutton .= Tpgb_Blocks_Global_Options::load_plusButton_saves($attributes);

    $output .= '<div class="tpgb-cta-banner tpgb-block-'.esc_attr($block_id).' cta-'.esc_attr($styleType).' '.($contentHoverEffect ? 'tpgb_cnt_hvr_effect cnt_hvr_'.esc_attr($selectHoverEffect) : '').' '.esc_attr($blockClass).' ">';
		if($styleType !='style-8'){
			$output .= '<div class="cta-block tpgb-relative-block '.esc_attr($hoverStyle).'">';
				$output .= '<div class="cta-block-inner tpgb-relative-block tpgb-trans-easeinout">';
					$output .= '<div class="'.esc_attr($text_wrap_style).'">';
						$output .= '<div class="content-level2"> ';
							$output .= '<div class="content-level3">';
								if(!empty($extBtnshow)){
									$extBtnUrl = (!empty($attributes['extBtnUrl'])) ? $attributes['extBtnUrl'] : '';
									$output .= $getSubtitle;
										$output .= '<a href="'.(!empty($extBtnUrl['url']) ? $extBtnUrl['url']  : '').'" target="'.(!empty($extBtnUrl['target']) ? '_blank' : '').'">';
											$output .= $getTitle;
										$output .= '</a>';
									$output .= $getDesc;
									$output .= $getbutton;
								}else{
									$output .= $getSubtitle;
									$output .= $getTitle;
									$output .= $getDesc;
								}
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="cta-block-inner_img ">';
						$output .= $imgRender;
					$output .= '</div>';
					$output .= '<div class="entry-thumb"> ';
						$output .= '<div class="entry-hover tpgb-trans-easeinout-before"> ';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		}else{
			$output .= '<div class="cta-product-box"> ';
				$output .= '<div class="cta-product-box-inner" style = "background-image: url('.esc_url($imgSrc).')" > ';
					$output .= '<div class="cta-img-hide"> ';
						$output .= $imgRender;
					$output .= "</div>";
					$output .= '<div class="cta-content"> ';
						$output .= $getTitle;
						$output .= $getSubtitle;
						$output .= $getDesc;
					$output .= "</div>";
				$output .= "</div>";
				$output .= '<div class="cta-btn-block">';
					if(!empty($extBtnshow)){
						$output .= $getbutton;
					}
				$output .= "</div>";
			$output .= "</div>";
		}
		
	$output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_cta_banner() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();

	$attributesOptions = array(
		'block_id' => array(
			'type' => 'string',
			'default' => '',
		),
		
		'styleType' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'bannerImage' => [
			'type' => 'object',
			'default'=> [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
			],	
		],
		'imageSize' => [
			'type' => 'string',
			'default' => 'full',	
		],
		'Title' => [
			'type' => 'string',
			'default' => 'Exclusive Offers',
		],
		'subTitle' => [
			'type' => 'string',
			'default' => 'Never Before',
		],
		'desc' => [
			'type' => 'string',
			'default' => ''
		],
		'hoverStyle' => [
			'type' => 'string',
			'default' => 'cta_img_vertical',
		],
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}}.tpgb-cta-banner h3.cta-title',
				],
			],
			'scopy' => true,
		],
		'titleColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}}.tpgb-cta-banner h3.cta-title{ color: {{titleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titlehoverColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'Title', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '!=', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .cta-title { color: {{titlehoverColor}}; }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'Title', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-product-box:hover .cta-title{ color: {{titlehoverColor}}; }',
				],
			],
			'scopy' => true,
		],
		'subtitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}}.tpgb-cta-banner h4.cta-subtitle',
				],
			],
			'scopy' => true,
		],
		'subtitleColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}}.tpgb-cta-banner h4.cta-subtitle{ color: {{subtitleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'subtitlehoverColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'subTitle', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '!=', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .cta-subtitle{ color: {{subtitlehoverColor}}; }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'subTitle', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-product-box:hover .cta-subtitle{ color: {{subtitlehoverColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'desc', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .cta-desc',
				],
			],
			'scopy' => true,
		],
		'descColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'desc', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .cta-desc{ color: {{descColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'desc', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '!=', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .cta-desc{ color: {{descHvrColor}}; }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'desc', 'relation' => '!=', 'value' => '' ],
						(object) [ 'key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ],
					],
					'selector' => '{{PLUS_WRAP}} .cta-product-box:hover .cta-desc{ color: {{descHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'desctopsp' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'unit' => 'px' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'desc', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .cta-desc{margin-top: {{desctopsp}};}',
				],
			],
			'scopy' => true,
		],
		'descbottomsp' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'unit' => 'px' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'desc', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .cta-desc{margin-bottom: {{descbottomsp}};}',
				],
			],
			'scopy' => true,
		],
		'normalbgBorder' => [
			'type' => 'object',
			'default' => (object) [ 
				"unit" => [ 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block .cta-block-inner{ border-radius: {{normalbgBorder}}; }',
				],
			],
			'scopy' => true,
		],
		'normalbgShadow' => [
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
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block .cta-block-inner',
				],
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-product-box',
				],
			],
			'scopy' => true,
		],
		'normalbgType' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block .entry-thumb .entry-hover:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-product-box .cta-product-box-inner:after',
				],
			],
			'scopy' => true,
		],
		'borderRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				"unit" => [ 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .cta-block-inner{ border-radius: {{borderRadius}}; }',
				],
			],
			'scopy' => true,
		],
		'hoverbgShadow' => [
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
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .cta-block-inner',
				],
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-product-box:hover',
				],
			],
			'scopy' => true,
		],
		'hoverbgType' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-block:hover .entry-thumb .entry-hover:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '=	=', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-product-box:hover .cta-product-box-inner:after',
				],
			],
			'scopy' => true,
		],
		'btndivBgcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8']],
					'selector' => '{{PLUS_WRAP}} .cta-product-box .cta-btn-block{ background : {{btndivBgcolor}}; }',
				],
			],
			'scopy' => true,
		],
	);
	
	$attributesOptions = array_merge($attributesOptions,$globalPlusExtrasOption,$globalBgOption,$globalpositioningOption,$plusButton_options);
	
	register_block_type( 'tpgb/tp-cta-banner', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_cta_banner_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_cta_banner' );