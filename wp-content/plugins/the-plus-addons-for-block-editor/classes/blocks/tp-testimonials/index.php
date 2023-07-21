<?php
/* Block : Testimonials
 * @since : 1.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_testimonials_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
   
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	
	$ItemRepeater = (!empty($attributes['ItemRepeater'])) ? $attributes['ItemRepeater'] : [];
	
	$telayout = (!empty($attributes['telayout'])) ? $attributes['telayout'] : '';

	$descByLimit	= isset($attributes['descByLimit']) ? $attributes['descByLimit'] : 'default';
	$descLimit = isset($attributes['descLimit']) ? $attributes['descLimit'] : 30;
	$cntscrollOn = (!empty($attributes['cntscrollOn'])) ? $attributes['cntscrollOn'] : '';
	$caroByheight = (!empty($attributes['caroByheight'])) ? $attributes['caroByheight'] : '';

	$redmorTxt = (!empty($attributes['redmorTxt'])) ? $attributes['redmorTxt'] : '';
	$redlesTxt = (!empty($attributes['redlesTxt'])) ? $attributes['redlesTxt'] : '';


	//Carousel Options
	
	$dataAttr = '';
	$Sliderclass ='';
	if($telayout == 'carousel'){
		if( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
			$Sliderclass .= ' dots-'.esc_attr($dotsStyle);
		}

		$carousel_settings = Tp_Blocks_Helper::carousel_settings($attributes);
		$dataAttr = 'data-splide=\'' . json_encode($carousel_settings) . '\'';
	}

	
	$readAttr = [];
	$attr = '';
	if($telayout == 'masonry' || ( $telayout == 'carousel' && $caroByheight == 'text-limit' )){
		
		$readAttr['readMore'] = $redmorTxt;
		$readAttr['readLess'] = $redlesTxt;
		
		$readAttr = htmlspecialchars(json_encode($readAttr), ENT_QUOTES, 'UTF-8');

		$attr = 'data-readData = \'' .$readAttr. '\'';
	}

	$list_layout = '';
	if($telayout=='grid' || $telayout=='masonry'){
		$list_layout = 'tpgb-isotope';
	}else if($telayout=='carousel'){
		$list_layout = 'tpgb-carousel splide';
	}

	$column_class = ' tpgb-col';
	if( $telayout!='carousel' && !empty($attributes['columns']) && is_array($attributes['columns'])){
		$column_class .= isset($attributes['columns']['md']) ? " tpgb-col-lg-".$attributes['columns']['md'] : ' tpgb-col-lg-3';
		$column_class .= isset($attributes['columns']['sm']) ? " tpgb-col-md-".$attributes['columns']['sm'] : ' tpgb-col-md-4';
		$column_class .= isset($attributes['columns']['xs']) ? " tpgb-col-sm-".$attributes['columns']['xs'] : ' tpgb-col-sm-6';
		$column_class .= isset($attributes['columns']['xs']) ? " tpgb-col-".$attributes['columns']['xs'] : ' tpgb-col-6';
	}

    $output .= '<div class="tpgb-testimonials tpgb-relative-block testimonial-'.esc_attr($style).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($Sliderclass).' '.esc_attr($list_layout).' " '.$dataAttr.' >';

		if( $telayout == 'carousel' && ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
			$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,'');
		}
		$output .= '<div class="post-loop-inner '.($telayout == 'carousel' ? 'splide__track' : 'tpgb-row').'">';
			if($telayout == 'carousel'){
				$output .= '<div class="splide__list">';
			}
				if( !empty( $ItemRepeater ) ){
					foreach ( $ItemRepeater as $index => $item ) :
						if(is_array($item)){
						
							$itemContent = '';
							if( !empty($item['content']) ){
								if($descByLimit == 'default' || ($telayout == 'carousel' && ($caroByheight == '' || $caroByheight == 'height' )) ){
									$itemContent .= '<div class="entry-content scroll-'.esc_attr($cntscrollOn).'">';
										$itemContent .= wp_kses_post($item['content']);
									$itemContent .= '</div>';
								}else{
									if( $descByLimit === 'words' ){
										$total = explode(' ', $item['content']);
										$limit_words = explode(' ', $item['content'], $descLimit);
										$ltn = count($limit_words);
										$remaining_words = implode(" " , array_slice($total, $descLimit-1));
										if (count($limit_words)>=$descLimit) {
											array_pop($limit_words);
											$excerpt = implode(" ",$limit_words).' <span class="testi-more-text" style = "display: none" >'.wp_kses_post($remaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
										} else {
											$excerpt = implode(" ",$limit_words);
										}
										
									}else if( $descByLimit === 'letters' ){
										$ltn = strlen($item['content']);
										$limit_words = substr($item['content'],0,$descLimit); 
										$remaining_words = substr($item['content'], $descLimit, $ltn);
										if(strlen($item['content'])>$descLimit){
											$excerpt = $limit_words.'<span class="testi-more-text" style = "display:none" >'.wp_kses_post($remaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
										}else{
											$excerpt = $limit_words;
										}
									}

									$itemContent .= '<div class="entry-content">';
										$itemContent .= $excerpt;
									$itemContent .= '</div>';
								}
							}
							
							$itemAuthorTitle = '';
							if( !empty($item['authorTitle']) ){
								$itemAuthorTitle .= '<h3 class="testi-author-title title-scroll-'.esc_attr($cntscrollOn).'">'.esc_html($item['authorTitle']).'</h3>';
							}
							
							$itemTitle ='';
							if(!empty($item['testiTitle'])){
								$itemTitle .= '<div class="testi-post-title">'.esc_html($item['testiTitle']).'</div>';
							}
							
							$itemDesignation ='';
							if(!empty($item['designation'])){
								$itemDesignation .= '<div class="testi-post-designation">'.esc_html($item['designation']).'</div>';
							}
							
							$imgUrl ='';
							if(!empty($item['avatar']) && !empty($item['avatar']['id'])){
								$imgUrl = wp_get_attachment_image($item['avatar']['id'],'medium');
							}else if(!empty($item['avatar']) && !empty($item['avatar']['url'])){
								$imgUrl = '<img src="'.esc_url($item['avatar']['url']).'" alt="'.esc_html__('author avatar','tpgb').'"/>';
							}else{
								$imgUrl ='<img src="'.esc_url(TPGB_URL.'assets/images/tpgb-placeholder-grid.jpg').'" alt="'.esc_html__('author avatar','tpgb').'"/>';
							}
							
							$output .= '<div class="grid-item '.($telayout=='carousel' ? 'splide__slide' : $column_class).' tp-repeater-item-'.esc_attr($item['_key']).'" >';
								$output .= '<div class="testimonial-list-content" >';
									
									if($style!='style-4'){
										$output .= '<div class="testimonial-content-text">';
											if($style=="style-1" || $style=="style-2"){
												$output .= $itemContent;
												$output .= $itemAuthorTitle;
											}
										$output .= '</div>';
									}
									
									$output .= '<div class="post-content-image">';
										$output .= '<div class="author-thumb">';
											$output .= $imgUrl;
										$output .= '</div>';
										if($style=="style-1" || $style=="style-2"){
											$output .= $itemTitle;
											$output .= $itemDesignation;
										}
									$output .= '</div>';
									
									
								$output .= "</div>";
							$output .= "</div>";
						}
					endforeach;
				}
			if($telayout == 'carousel'){
				$output .= '</div>';
			}
		$output .= "</div>";
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
	if( !empty($arrowCss) ){
		$output .= $arrowCss;
	}
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_testimonials() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'style' => [
                'type' => 'string',
				'default' => 'style-1',
			],
			'telayout' => [
				'type' => 'string',
				'default' => 'carousel',
			],
			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 6,'sm' => 6,'xs' => 12 ],
			],
			'columnSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => 15,
						"right" => 15,
						"bottom" => 15,
						"left" => 15,
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .grid-item{padding: {{columnSpace}};}',
					],
				],
			],
			'ItemRepeater' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'testiTitle' => [
							'type' => 'string',
							'default' => 'John Doe',
						],
						'designation' => [
							'type' => 'string',
							'default' => 'MD at Orange',
						],
						'content' => [
							'type' => 'string',
							'default' => ' I am pretty satisfied with The Plus Gutenberg Addons. The Plus has completely surpassed our expectations. I was amazed at the quality of The Plus Gutenberg Addons.',
						],
						'authorTitle' => [
							'type' => 'string',
							'default' => 'Supercharge ⚡ Gutenberg',
						],
					],
				], 
				'default' => [ 
					[ '_key'=> 'cvi9', 'testiTitle' => 'John Doe', 'designation' => 'MD at Orange', 'content' => ' I am pretty satisfied with The Plus Gutenberg Addons. The Plus has completely surpassed our expectations. I was amazed at the quality of The Plus Gutenberg Addons.','authorTitle' => 'Supercharge ⚡ Gutenberg' ]
				],
			],
			
			'contentHei' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testimonial-list-content .entry-content{ height : {{contentHei}}; overflow-y: auto; padding-right: 5px; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],
						['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testimonial-list-content .entry-content{ height : {{contentHei}}; overflow-y: auto; padding-right: 5px; }',
					],
				],
			],
			'titleHei' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testimonial-list-content .testi-author-title{ height : {{titleHei}}; overflow-y: auto; padding-right: 5px; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testimonial-list-content .testi-author-title{ height : {{titleHei}}; overflow-y: auto; padding-right: 5px; }',
					],
				],
			],
			'cntscrollOn' => [
				'type' => 'string',
				'default' => 'on-hover',
			],
			'descByLimit' => [
				'type' => 'string',
				'default' => 'default',
			],
			'caroByheight' => [
				'type' => 'string',
				'default' => '',
			],
			'descLimit' => [
				'type' => 'string',
				'default' => 30,
			],
			'redmorTxt' => [
				'type' => 'string',
				'default' => 'Read More',
			],
			'redlesTxt' => [
				'type' => 'string',
				'default' => 'Read Less',
			],

			'titleTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .post-content-image .testi-post-title',
					],
				],
				'scopy' => true,
			],
			'titleNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .post-content-image .testi-post-title{color: {{titleNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'titleHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .testi-post-title{color: {{titleHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'AuthortitleTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testi-author-title',
					],
				],
				'scopy' => true,
			],
			'AuthortitleNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-testimonials .testi-author-title{color: {{AuthortitleNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'AuthortitleHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .testi-author-title{color: {{AuthortitleHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'DesTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testi-post-designation',
					],
				],
				'scopy' => true,
			],
			'DesNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testi-post-designation{color: {{DesNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'DesHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .testi-post-designation{color: {{DesHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'contentTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content',
					],
				],
				'scopy' => true,
			],
			'contentNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content{color: {{contentNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'cntHovercolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .entry-content{color: {{cntHovercolor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'boxMargin' => [
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content{margin: {{boxMargin}};}',
					],
				],
				'scopy' => true,
			],
			'boxPadding' => [
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content{padding: {{boxPadding}};}',
					],
				],
				'scopy' => true,
			],
			
			'boxBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content{border-radius: {{boxBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover{border-radius: {{boxBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'boxBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content',
					],
				],
				'scopy' => true,
			],
			'arrowNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text:after{border-top-color: {{arrowNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'boxBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover',
					],
				],
				'scopy' => true,
			],
			'arrowHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text:after{border-top-color: {{arrowHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'boxBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				] ,
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover',
					],
				],
				'scopy' => true,
			],
			
			'imgMaxWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .author-thumb,{{PLUS_WRAP}}.testimonial-style-2 .author-thumb{max-width: {{imgMaxWidth}}px;}',
					],
				],
				'scopy' => true,
			],
			'imageBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .author-thumb img{border-radius: {{imageBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'imageBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .author-thumb img',
					],
				],
				'scopy' => true,
			],
			'imageBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .author-thumb img',
					],
				],
				'scopy' => true,
			],

			'readTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 1,
					'size' => [ 'md' => 14, 'unit' => 'px' ],
					'height' => [ 'md' => 26,'unit' => 'px' ], 
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'descByLimit', 'relation' => '!=', 'value' => 'default' ]],
						'selector' => '{{PLUS_WRAP}} .testimonial-content-text .entry-content a.testi-readbtn',
					],
				],
				'scopy' => true,
			],
			'readColor' => [
				'type' => 'string',
				'default' => '#8072FC',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'descByLimit', 'relation' => '!=', 'value' => 'default' ]],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content a.testi-readbtn{ color : {{readColor}} }',
					],
				],
				'scopy' => true,
			],
			'readmhvrColor' => [
				'type' => 'string',
				'default' => '#FF5A6E',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'descByLimit', 'relation' => '!=', 'value' => 'default' ]],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .entry-content a.testi-readbtn{ color : {{readmhvrColor}} }',
					],
				],
				'scopy' => true,
			],
			'tesSclWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar{ width:{{tesSclWidth}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar{ width:{{tesSclWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'tesThumbBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'tesThumbBrs' => [
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
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb{border-radius:{{tesThumbBrs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb{border-radius:{{tesThumbBrs}};}',
					],
				],
				'scopy' => true,
			],
			'tesThumbBsw' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-thumb,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'tesTrackBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
			'tesTrackBRs' => [
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
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track{border-radius:{{tesTrackBRs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track{border-radius:{{tesTrackBRs}};}',
					],
				],
				'scopy' => true,
			], 
			'tesTrackBsw' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'grid']],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track',
					],
					(object) [
						'condition' => [(object) ['key' => 'telayout', 'relation' => '==', 'value' => 'carousel'],['key' => 'caroByheight', 'relation' => '==', 'value' => 'height' ] ],
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content::-webkit-scrollbar-track,{{PLUS_WRAP}} .testimonial-list-content .testi-author-title::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-testimonials', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_testimonials_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_testimonials' );