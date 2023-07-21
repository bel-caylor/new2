<?php
/* Block : Post Navigation(Next/Prev)
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_nav_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
    $prevText = (!empty($attributes['prevText'])) ? $attributes['prevText'] : '';
    $nextText = (!empty($attributes['nextText'])) ? $attributes['nextText'] : '';
	$taxobased = (!empty($attributes['taxobased'])) ? 'yes' : 'no';
	$taxonomySlug = (!empty($attributes['taxonomySlug'])) ? $attributes['taxonomySlug'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	if($taxobased == 'yes' && !empty($taxonomySlug) ){
		$prev_post = get_previous_post(true, '', $taxonomySlug);
		$next_post = get_next_post(true, '', $taxonomySlug);
	}else{
		$prev_post = get_previous_post();
		$next_post = get_next_post();
	}

	$next_id = $prev_id = $next_title = $prev_title=  '';

	if(is_object($prev_post)){
		$prev_id = !empty($prev_post->ID) ? $prev_post->ID : '' ;
		$prev_title = !empty($prev_post->post_title) ? $prev_post->post_title : '' ;
	}
	if(is_object($next_post)){ 
		$next_id = !empty($next_post->ID) ? $next_post->ID : '' ;
		$next_title = !empty($next_post->post_title) ? $next_post->post_title : '';
	}
	//Get Post Thumbnail
	$preImg = '';
	if (has_post_thumbnail( $prev_id ) ){
		$preImg .= '<div class="post-image">';
			$preImg .= get_the_post_thumbnail( $prev_id, 'thumbnail', array( 'class' => 'tpgb-nav-trans' ) );
		$preImg .= '</div>';
	}else{
		$preImg .= '<div class="post-image">';
			$preImg .= '<img src="'.TPGB_URL .'/assets/images/tpgb-placeholder.jpg" class="tpgb-nav-trans" />';
		$preImg .= '</div>';
	}

	//Get Post Image Content
	$pervIcon = '';
	$pervIcon .= '<div class="prev-post-content">';
		$pervIcon .= '<b>'.esc_html($prevText).'</b>';
		$pervIcon .= '<span>'.esc_html($prev_title).'</span>';
	$pervIcon .= '</div>';
	
	$nextImg = '';
	if (has_post_thumbnail( $next_id ) ){
		$nextImg .= '<div class="post-image">';
			$nextImg .= get_the_post_thumbnail( $next_id, 'thumbnail', array( 'class' => 'tpgb-nav-trans' ) );
		$nextImg .= '</div>';
	}else{
		$nextImg .= '<div class="post-image">';
			$nextImg .= '<img src="'.TPGB_URL .'/assets/images/tpgb-placeholder.jpg" class="tpgb-nav-trans" />';
		$nextImg .= '</div>';
	}

	//Get Post Image Content
	$nextIcon = '';
	$nextIcon .= '<div class="next-post-content">';
		$nextIcon .= '<b>'.esc_html($nextText).'</b>';
		$nextIcon .= '<span>'.esc_html($next_title).'</span>';
	$nextIcon .= '</div>';

	
	$prevnav = '';
	if( !empty( $prev_post ) && !empty($style) ){
		if($style=='style-1'){
			$prevnav .= '<a href="'.esc_url(get_permalink( $prev_id )).'" class="post_nav_link prev tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
				$prevnav .= $preImg;
				$prevnav .= $pervIcon;
			$prevnav .= '</a>';
		}else if($style=='style-2'){
			$prevnav .= '<a href="'.esc_url(get_permalink( $prev_id )).'" class="post_nav_link prev tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'"><i aria-hidden="true" class="prev-hveicon far fa-arrow-alt-circle-left"></i></a>';
			$prevnav .='<div class="tpgb-post-nav-hover-con">'.$preImg.$pervIcon.'</div>';
		}else if($style=='style-3'){
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $prev_id ),'full');
			$imgUrl = (!empty($img) && !empty($img[0])) ? $img[0] : '';
			$prevnav .= '<a href="'.esc_url(get_permalink( $prev_id )).'" class="post_nav_link prev tp-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
			$prevnav .='<div class="tpgb-post-nav-hover-con" style="background-image: url('.esc_url($imgUrl).');">'.$pervIcon.'</div></a>';
		}else if($style=='style-4'){
			$prevnav .= '<a href="'.esc_url(get_permalink( $prev_id )).'" class="post_nav_link prev tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
				$prevnav .=$preImg;
				$prevnav .='<div class="tpgb-post-nav-hover-con"></div>';
				$prevnav .=$pervIcon;
			$prevnav .='</a>';
		}
	}

	$nextnav = '';
	if(!empty( $next_post ) && !empty($style) ){
		if($style=='style-1'){
			$nextnav .= '<a href="'.esc_url(get_permalink( $next_id )).'" class="post_nav_link next tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
				$nextnav .= $nextIcon;
				$nextnav .= $nextImg;
			$nextnav .= '</a>';
		}else if($style=='style-2'){
			$nextnav .= '<a href="'.esc_url(get_permalink( $next_id )).'" class="post_nav_link next tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'"><i aria-hidden="true" class="prev-hveicon far fa-arrow-alt-circle-right"></i></a>';
			$nextnav .='<div class="tpgb-post-nav-hover-con">'.$nextImg.$nextIcon.'</div>';
		}else if($style=='style-3'){
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $next_id ),'full');
			$imgUrl = (!empty($img) && !empty($img[0])) ? $img[0] : '';
			$nextnav .= '<a href="'.esc_url(get_permalink( $next_id )).'" class="post_nav_link next tp-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
			$nextnav .='<div class="tpgb-post-nav-hover-con" style="background-image: url('.esc_url($imgUrl).');">'.$nextIcon.'</div></a>';
		}else if($style=='style-4'){
			$nextnav .= '<a href="'.esc_url(get_permalink( $next_id )).'" class="post_nav_link next tpgb-nav-trans" rel="'.esc_attr__('prev','tpgbp').'">';
				$nextnav .=$nextImg;
				$nextnav .='<div class="tpgb-post-nav-hover-con"></div>';
				$nextnav .=$nextIcon;
			$nextnav .='</a>';
		}
	}
    $output .= '<div class="tpgb-post-navigation tpgb-nav-trans tpgb-block-'.esc_attr($block_id).' tpgb-nav-'.esc_attr($style).' '.esc_attr($blockClass).'">';
		$output .= '<div class="tpgb-post-nav '.($style == 'style-1' || $style == 'style-3' ? ' tpgb-row' : '').'">';
			$output .= '<div class="post-prev '.($style == 'style-1' || $style == 'style-3' ? ' tpgb-col tpgb-col-md-6 tpgb-col-sm-6 tpgb-col-xs-12' : '').'">';
				$output .= $prevnav;
			$output .= '</div>';
			$output .= '<div class="post-next '.($style == 'style-1' || $style == 'style-3' ? ' tpgb-col tpgb-col-md-6 tpgb-col-sm-6 tpgb-col-xs-12' : '').'">';
				$output .= $nextnav;
			$output .= '</div>';
		$output .= '</div>';
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_post_nav_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'style' => array(
                'type' => 'string',
				'default' => 'style-1',
			),
			'prevText' => array(
                'type' => 'string',
				'default' => 'Previous Post',
			),
			'nextText' => array(
                'type' => 'string',
				'default' => 'Next Post',
			),
			'taxobased' => [
				'type' => 'boolean',
				'default' => false,
			],
			'taxonomySlug' => [
				'type' => 'string',
				'default' => '',
			],
			'minHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .tpgb-post-nav .tpgb-post-nav-hover-con{ min-height: {{minHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'navTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-1 .next-post-content b',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-2 .next-post-content b',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-3 .next-post-content b',
					],
				],
				'scopy' => true,
			],
			
			'navNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-1 .next-post-content b{color: {{navNormalColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-2 .next-post-content b{color: {{navNormalColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-3 .next-post-content b{color: {{navNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'navHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next-post-content b{color: {{navHoverColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .next-post-content b{color: {{navHoverColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post-prev:hover .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-3 .post-next:hover .next-post-content b{color: {{navHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'titleTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-1 .next-post-content span',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-2 .next-post-content span',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-3 .next-post-content span',
					],
				],
				'scopy' => true,
			],
			
			'titleNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-1 .next-post-content span{color: {{titleNormalColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-2 .next-post-content span{color: {{titleNormalColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-3 .next-post-content span{color: {{titleNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'titleHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next-post-content span{color: {{titleHoverColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .next-post-content span{color: {{titleHoverColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post-prev:hover .prev-post-content span,{{PLUS_WRAP}}.tpgb-nav-style-3 .post-next:hover .next-post-content span{color: {{titleHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'imgBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 img',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 img',
					],
				],
				'scopy' => true,
			],
			'imgBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover img,{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover img',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover img,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover img',
					],
				],
				'scopy' => true,
			],
			'imgBorderRadius' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 img{border-radius: {{imgBorderRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 img{border-radius: {{imgBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'imgBorderRadiusHover' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover img,{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover img{border-radius: {{imgBorderRadiusHover}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover img,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover img{border-radius: {{imgBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'imgBoxShadow' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 img',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 img',
					],
				],
				'scopy' => true,
			],
			'imgBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover img,{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover img',
					],
				],
				'scopy' => true,
			],
			
			'prevBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'prevBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			
			'prevBorderRadius' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev .prev{border-radius: {{prevBorderRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .tpgb-post-nav-hover-con{border-radius: {{prevBorderRadius}} !important ;}',
					],
				],
				'scopy' => true,
			],
			'prevBorderRadiusHover' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev{border-radius: {{prevBorderRadiusHover}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con:hover{border-radius: {{prevBorderRadiusHover}} !important ;}',
					],
				],
				'scopy' => true,
			],
			'prevBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'prevBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			'prevBoxShadow' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'prevBoxShadowHover' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-prev:hover .prev',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			
			'nextBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'nextBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			
			'nextBorderRadius' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next .next{border-radius: {{nextBorderRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .tpgb-post-nav-hover-con{border-radius: {{nextBorderRadius}} !important ;}',
					],
				],
				'scopy' => true,
			],
			'nextBorderRadiusHover' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next{border-radius: {{nextBorderRadiusHover}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con:hover{border-radius: {{nextBorderRadiusHover}} !important ;}',
					],
				],
				'scopy' => true,
			],
			'nextBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'nextBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			'nextBoxShadow' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'nextBoxShadowHover' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-1 .post-next:hover .next',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-populate_roles_250(  )']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con:hover',
					],
				],
				'scopy' => true,
			],
			'conpadding' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con{ padding: {{conpadding}}; }',
					],
				],
				'scopy' => true,
			],
			'contBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'contBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'contGradius' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con{ border-radius : {{contGradius}}; }',
					],
				],
				'scopy' => true,
			],
			'contBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .tpgb-post-nav-hover-con',
					],
				],
				'scopy' => true,
			],
			'iconAlign' => [
				'type' => 'object',
				'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 { text-align: {{iconAlign}}; }',
					],
				],
				'scopy' => true,
			],
			'padding' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon{ padding : {{padding}}; }',
					],
				],
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon{ font-size : {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'iconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon{ color : {{iconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .prev-hveicon{ color : {{iconHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'iconHvrBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'iconBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'iconHvrBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'iconBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'iconHvrSha' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow'=> 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-2 .post-prev:hover .prev-hveicon,{{PLUS_WRAP}}.tpgb-nav-style-2 .post-next:hover .prev-hveicon',
					],
				],
				'scopy' => true,
			],
			'sticonColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .tpgb-post-nav-hover-con{ color : {{sticonColor}}; }',
					],
				],
				'scopy' => true,
			],
			'stHviconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-next:hover .tpgb-post-nav-hover-con{ color : {{stHviconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'sticonBg' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .tpgb-post-nav-hover-con{ background : {{sticonBg}}; }',
					],
				],
				'scopy' => true,
			],
			'sthviconBg' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-prev:hover .tpgb-post-nav-hover-con,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-next:hover .tpgb-post-nav-hover-con{ background : {{sthviconBg}}; }',
					],
				],
				'scopy' => true,
			],
			'stypadding' => [
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
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .prev-post-content,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .next-post-content{ padding : {{stypadding}}; }',
					],
				],
				'scopy' => true,
			],
			'styBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .prev-post-content,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .next-post-content{ background : {{styBgcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'stytitleCol' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-prev:hover span,{{PLUS_WRAP}}.tpgb-nav-style-4  .tpgb-post-nav .post-next:hover span{ color : {{stytitleCol}}; }',
					],
				],
				'scopy' => true,
			],
			'potitleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .post-prev:hover span,{{PLUS_WRAP}}.tpgb-nav-style-4
						.tpgb-post-nav .post-next:hover span',
					],
				],
				'scopy' => true,
			],
			'polabelTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .next-post-content b',
					],
				],
				'scopy' => true,
			],
			'stylabelCol' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .prev-post-content b,{{PLUS_WRAP}}.tpgb-nav-style-4 .tpgb-post-nav .next-post-content b{ color : {{stylabelCol}} }',
					],
				],
				'scopy' => true,
			],
			'imgBg' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link .tpgb-post-nav-hover-con:before{ background : {{imgBg}} }',
					],
				],
				'scopy' => true,
			],
			'imgHvrBg' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link:hover .tpgb-post-nav-hover-con:before{ background : {{imgHvrBg}} }',
					],
				],
				'scopy' => true,
			],
			'imgPosi' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link .tpgb-post-nav-hover-con{ background-position : {{imgPosi}} }',
					],
				],
				'scopy' => true,
			],
			'imgAttachment' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link .tpgb-post-nav-hover-con{ background-attachment : {{imgAttachment}} }',
					],
				],
				'scopy' => true,
			],
			'imgRepeat' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link .tpgb-post-nav-hover-con{ background-repeat : {{imgRepeat}} }',
					],
				],
				'scopy' => true,
			],
			'imgBgsize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-nav-style-3 .post_nav_link .tpgb-post-nav-hover-con{ background-size : {{imgBgsize}} }',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-navigation', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_nav_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_nav_content' );