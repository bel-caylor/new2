<?php
/* Block : Testimonials
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_testimonials_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
    $styleLayout = (!empty($attributes['styleLayout'])) ? $attributes['styleLayout'] : 'style-1';
    $style4Alignment = (!empty($attributes['style4Alignment'])) ? $attributes['style4Alignment'] : 'left';
    
	$ItemRepeater = (!empty($attributes['ItemRepeater'])) ? $attributes['ItemRepeater'] : [];
    $carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	
	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	$rating = (!empty($attributes['rating'])) ? $attributes['rating'] : false;
	$telayout = (!empty($attributes['telayout'])) ? $attributes['telayout'] : '';

	$descByLimit	= !empty($attributes['descByLimit']) ? $attributes['descByLimit'] : '';
	$descLimit = !empty($attributes['descLimit']) ? $attributes['descLimit'] : '' ;
	$cntscrollOn = (!empty($attributes['cntscrollOn'])) ? $attributes['cntscrollOn'] : '';
	$caroByheight = (!empty($attributes['caroByheight'])) ? $attributes['caroByheight'] : '';

	$titleByLimit = !empty($attributes['titleByLimit']) ? $attributes['titleByLimit'] : '';
	$titleLimit = !empty($attributes['titleLimit']) ? $attributes['titleLimit'] : '' ;

	$redmorTxt = (!empty($attributes['redmorTxt'])) ? $attributes['redmorTxt'] : '';
	$redlesTxt = (!empty($attributes['redlesTxt'])) ? $attributes['redlesTxt'] : '';

	$starIcon = (!empty($attributes['starIcon'])) ? $attributes['starIcon'] : '';
	$sIcon = (!empty($attributes['sIcon'])) ? $attributes['sIcon'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	//Carousel Options
	$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}
	
	$Style3Layout ='';
	if($style=='style-3' && !empty($styleLayout)){
		$Style3Layout ='layout-'.$styleLayout;
	}
	$style4Class = '';
	if($style=="style-4" && $style4Alignment){
		$style4Class = ' content-'.$style4Alignment;
	}
		
	$Sliderclass = '';
	$dataAttr = '';
	if($telayout == 'carousel'){

		$dataAttr .= 'data-splide=\'' . json_encode($carousel_settings) . '\' ';

		if($slideHoverDots==true && ( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
			$Sliderclass .= ' hover-slider-dots';
		}
		if($outerArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$Sliderclass .= ' outer-slider-arrow';
		}
		if($slideHoverArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$Sliderclass .= ' hover-slider-arrow';
		}
		if( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
			$Sliderclass .= ' dots-'.esc_attr($dotsStyle);
		}


		if(!empty($carouselId)){
			$dataAttr .=' id="tpca-'.esc_attr($carouselId).'"';
			$dataAttr .=' data-id="tpca-'.esc_attr($carouselId).'"';
			$dataAttr .=' data-connection="tptab_'.esc_attr($carouselId).'"';
			
		}
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

    $output .= '<div class="tpgb-testimonials tpgb-relative-block testimonial-'.esc_attr($style).' '.esc_attr($Style3Layout).' '.esc_attr($Sliderclass).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($equalHclass).' '.esc_attr($list_layout).' " '.$dataAttr.' '.$equalHeightAtt.'>';
		if( $telayout == 'carousel' && ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
			$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
		}
		$output .= '<div class="'.($telayout == 'carousel' ? 'splide__track' : 'tpgb-row').' post-loop-inner '.esc_attr($style4Class).'">';
			if($telayout == 'carousel'){
				$output .= '<div class="splide__list">';
			}
				if( !empty( $ItemRepeater ) ){
					foreach ( $ItemRepeater as $index => $item ) :
						if(is_array($item)){

							$itemContent = '';
							if( !empty($item['content']) ){
								if($descByLimit == 'default'){
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
							
							$itemAuthorTitle = $title = '';
							if( !empty($item['authorTitle']) ){

								if($telayout != 'carousel'){
									$itemAuthorTitle .= '<h3 class="testi-author-title title-scroll-'.esc_attr($cntscrollOn).'">'.wp_kses_post($item['authorTitle']).'</h3>';
								}else{
									if( $titleByLimit === 'words' ){
										$titotal = explode(' ', $item['authorTitle']);
										$tilimit_words = explode(' ', $item['authorTitle'], $titleLimit);
										$tiltn = count($tilimit_words);
										$tiremaining_words = implode(" " , array_slice($titotal, $titleLimit-1));
										if (count($tilimit_words)>=$titleLimit) {
											array_pop($tilimit_words);
											$title = implode(" ",$tilimit_words).' <span class="testi-more-text" style = "display: none" >'.wp_kses_post($tiremaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
										} else {
											$title = implode(" ",$tilimit_words);
										}
										
									}else if( $titleByLimit === 'letters' ){
										$tiltn = strlen($item['authorTitle']);
										$tilimit_words = substr($item['authorTitle'],0,$titleLimit); 
										$tiremaining_words = substr($item['authorTitle'], $titleLimit, $tiltn);
										if(strlen($item['authorTitle'])>$titleLimit){
											$title = $tilimit_words.'<span class="testi-more-text" style = "display:none" >'.wp_kses_post($tiremaining_words).'</span><a '.$attr.' class="testi-readbtn"> '.esc_attr($redmorTxt).' </a>';
										}else{
											$title = $tilimit_words;
										}
									}else{
										$title = $item['authorTitle'];
									}

									$itemAuthorTitle .= '<h3 class="testi-author-title">'.$title.'</h3>';
								}

								
							}
							
							$itemTitle ='';
							if(!empty($item['testiTitle'])){
								$itemTitle .= '<div class="testi-post-title">'.wp_kses_post($item['testiTitle']).'</div>';
							}
							
							$itemDesignation ='';
							if(!empty($item['designation'])){
								$itemDesignation .= '<div class="testi-post-designation">'.wp_kses_post($item['designation']).'</div>';
							}
							
							//Star Rating
							$itemStarAct = '';
							if(!empty($item['starRating'])){
								$nuMatch = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['starRating']);
								$racAct = (int) $nuMatch;
								for($i=0;$i<$racAct;$i++){
									$itemStarAct .= '<span class="tpgb-testi-star checked '.($starIcon == 'custom' ? esc_attr($sIcon) : 'fa fa-star' ).'"></span>';
								}
							}

							$itemStarnor = '';
							if(!empty($item['starRating'])){
								$renuMatch = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['starRating']);
								$ratDis =  5 - (int) $renuMatch;
								for($i=0;$i<$ratDis;$i++){
									$itemStarnor .= '<span class="tpgb-testi-star '.($starIcon == 'custom' ? esc_attr($sIcon) : 'fa fa-star' ).'"></span>';
								}
							}
							
							$imgUrl ='';
							if(!empty($item['avatar']) && !empty($item['avatar']['id'])){
								$imgUrl = wp_get_attachment_image($item['avatar']['id'],'medium');
							}else if(!empty($item['avatar']) && !empty($item['avatar']['url'])){
								$urlImg = (isset($item['avatar']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['avatar']) : (!empty($item['avatar']['url']) ? $item['avatar']['url'] : '');
								$imgUrl = '<img src="'.esc_url($urlImg).'" alt="'.esc_html__('author avatar','tpgbp').'"/>';
							}else{
								$imgUrl ='<img src="'.esc_url(TPGB_URL.'assets/images/tpgb-placeholder-grid.jpg').'" alt="'.esc_html__('author avatar','tpgbp').'"/>';
							}
							//'.$telayout=='carousel' ? 'splide__slide' : $column_class .'
							$output .= '<div class="grid-item tp-repeater-item-'.esc_attr($item['_key']).' '.($telayout=='carousel' ? 'splide__slide' : $column_class).' " >';
								$output .= '<div class="testimonial-list-content'.( $style=='style-4' ? ' tpgb-align-items-center tpgb-d-flex tpgb-flex-row tpgb-flex-wrap' : '').'" >';
									
									if($style!='style-4'){
										$output .= '<div class="testimonial-content-text">';
											if($style=="style-1" || $style=="style-2"){
												if($style=="style-2" && !empty($rating)){
													$output .= '<div class="tpgb-testim-rating">';
														$output .= $itemStarAct;
														$output .= $itemStarnor;
													$output .= '</div>';
												}
												$output .= $itemContent;
												$output .= $itemAuthorTitle;
											}
											if($style=="style-3"){
												$output .= $itemAuthorTitle;
												$output .= $itemContent;
											}
										$output .= '</div>';
									}
									
									$output .= '<div class="post-content-image'.($style=='style-4' ? ' tpgb-flex-column tpgb-flex-wrap' : '').'">';
										$output .= '<div class="author-thumb">';
											$output .= $imgUrl;
										$output .= '</div>';
										if($style=="style-1" || $style=="style-2"){
											$output .= $itemTitle;
											$output .= $itemDesignation;
											if($style=="style-1" && !empty($rating)){
												$output .= '<div class="tpgb-testim-rating">';
													$output .= $itemStarAct;
													$output .= $itemStarnor;
												$output .= '</div>';
											}
										}
										if($style=="style-3"){
											$output .= '<div class="author-left-text">';
												$output .= $itemTitle;
												$output .= $itemDesignation;
											$output .= '</div>';
											if(!empty($rating)){
												$output .= '<div class="tpgb-testim-rating">';
													$output .= $itemStarAct;
													$output .= $itemStarnor;
												$output .= '</div>';
											}
										}
									$output .= '</div>';
									
									if($style=='style-4'){
										$output .= '<div class="testimonial-content-text tpgb-flex-column tpgb-flex-wrap">';
											if(!empty($rating)){
												$output .= '<div class="tpgb-testim-rating">';
													$output .= $itemStarAct;
													$output .= $itemStarnor;
												$output .= '</div>';
											}
											$output .= $itemAuthorTitle;
											$output .= $itemContent;
											$output .= '<div class="author-left-text">';
												$output .= $itemTitle;
												$output .= $itemDesignation;
											$output .= '</div>';
										$output .= '</div>';
									}
									
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
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	
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
			'styleLayout' =>[
                'type' => 'string',
				'default' => 'style-1',
			],
			'style4Alignment' => [
                'type' => 'string',
				'default' => 'left',
			],
			'carouselId' => [
				'type' => 'string',
				'default' => '',
			],
			'rating' => [
				'type' => 'boolean',
				'default' => false,	
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
						'avatar' => [
							'type' => 'object',
							'default' => [
								'url' => '',
							],
						],
						'starRating' => [
							'type' => 'string',
							'default' => '3',
						],
					],
				], 
				'default' => [ 
					[ '_key'=> 'cvi9', 'testiTitle' => 'John Doe', 'designation' => 'MD at Orange', 'content' => ' I am pretty satisfied with The Plus Gutenberg Addons. The Plus has completely surpassed our expectations. I was amazed at the quality of The Plus Gutenberg Addons.','authorTitle' => 'Supercharge ⚡ Gutenberg', 'avatar' => ['url' => ''] , 'starRating' => '' ]
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
			'starIcon' => [
				'type' => 'string',
				'default' => 'default',
			],
			'titleByLimit' => [
				'type' => 'string',
				'default' => 'default',
			],
			'titleLimit' => [
				'type' => 'string',
				'default' => 30,
			],
			'sIcon' => [
				'type' => 'string',
				'default' => '',
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
						'selector' => '{{PLUS_WRAP}} .post-content-image .testi-post-title, {{PLUS_WRAP}}.testimonial-style-4 .testi-post-title',
					],
				],
				'scopy' => true,
			],
			'titleNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .post-content-image .testi-post-title, {{PLUS_WRAP}}.testimonial-style-4 .testi-post-title{color: {{titleNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'titleHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .testi-post-title, {{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content:hover .testi-post-title{color: {{titleHoverColor}};}',
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
			
			'starSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'rating', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star { font-size: {{starSize}}; }',
					],
				],
				'scopy' => true,
			],
			'TopSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'rating', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating { margin-top: {{TopSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'bottomSpc' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'rating', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating { margin-bottom: {{bottomSpc}}; }',
					],
				],
				'scopy' => true,
			],
			'betSpc' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'rating', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star:not(:last-child){ margin-right: {{betSpc}}; }',
					],
				],
				'scopy' => true,
			],
			'stoutLine' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'stnrmColor' => [
				'type' => 'string',
				'default' => '#bcb0b0',			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'stoutLine', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star {  -webkit-text-stroke : 1px {{stnrmColor}}; color : transparent }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stoutLine', 'relation' => '==', 'value' => false ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star {  color :  {{stnrmColor}}; }',
					],
				],
				'scopy' => true,
			],
			'strActColor' => [
				'type' => 'string',
				'default' => '#F3A30E',			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'stoutLine', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star.checked {  -webkit-text-stroke: 1px {{strActColor}}; color : transparent }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stoutLine', 'relation' => '==', 'value' => false ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-testim-rating .tpgb-testi-star.checked {  color :  {{strActColor}}; }',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content{margin: {{boxMargin}};}',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content{padding: {{boxPadding}};}',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content{border-radius: {{boxBorderRadius}};}',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content:hover{border-radius: {{boxBorderRadiusHover}};}',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content',
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
			'bottomBorderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'style', 'relation' => '==', 'value' => 'style-3' ],
							(object) [ 'key' => 'styleLayout', 'relation' => '==', 'value' => 'style-1' ],
						],
						'selector' => '{{PLUS_WRAP}}.testimonial-style-3 .testimonial-content-text:after{background: {{bottomBorderColor}};}',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content:hover',
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
			'bottomBorderHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'style', 'relation' => '==', 'value' => 'style-3' ],
							(object) [ 'key' => 'styleLayout', 'relation' => '==', 'value' => 'style-1' ],
						],
						'selector' => '{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content:hover .testimonial-content-text:after{background: {{bottomBorderHoverColor}};}',
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
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content',
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
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .testimonial-list-content:hover .testimonial-content-text,{{PLUS_WRAP}}.testimonial-style-2 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-3 .testimonial-list-content:hover,{{PLUS_WRAP}}.testimonial-style-4 .testimonial-list-content:hover',
					],
				],
				'scopy' => true,
			],
			
			'imgMaxWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.testimonial-style-1 .author-thumb,{{PLUS_WRAP}}.testimonial-style-2 .author-thumb,{{PLUS_WRAP}}.testimonial-style-3 .author-thumb{max-width: {{imgMaxWidth}}px;}',
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
						'selector' => '{{PLUS_WRAP}} .testimonial-content-text .entry-content a.testi-readbtn,{{PLUS_WRAP}} .testimonial-content-text .testi-author-title a.testi-readbtn',
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
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content .entry-content a.testi-readbtn,{{PLUS_WRAP}} .testimonial-content-text .testi-author-title a.testi-readbtn{ color : {{readColor}} }',
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
						'selector' => '{{PLUS_WRAP}} .testimonial-list-content:hover .entry-content a.testi-readbtn,{{PLUS_WRAP}} .testimonial-content-text .testi-author-title a.testi-readbtn{ color : {{readmhvrColor}} }',
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
	
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-testimonials', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_testimonials_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_testimonials' );