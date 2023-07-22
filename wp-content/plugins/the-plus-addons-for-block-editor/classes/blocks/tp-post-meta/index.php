<?php
/* Tp Block : Post Meta
 * @since	: 3.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_meta_render_callback( $attr, $content) {
	$output = '';
	$post_id = '';

	if( is_archive() ){
		$post_id = get_queried_object_id();
	}else{
		$post_id = get_the_ID();
	}
    

    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$showDate = (!empty($attr['showDate'])) ? $attr['showDate'] : false;
	$showCategory = (!empty($attr['showCategory'])) ? $attr['showCategory'] : false;
	$showAuthor = (!empty($attr['showAuthor'])) ? $attr['showAuthor'] : false;
	$showComment = (!empty($attr['showComment'])) ? $attr['showComment'] : false;
	$metaSort = (!empty($attr['metaSort'])) ? (Array)$attr['metaSort'] :'';
	$metaLayout = (!empty($attr['metaLayout'])) ? $attr['metaLayout'] :'';
	$taxonomySlug = (!empty($attr['taxonomySlug'])) ? $attr['taxonomySlug'] : 'category';
	$metafieldRep = (!empty($attr['metafieldRep'])) ? $attr['metafieldRep'] : [] ;
    $readPrefix = (!empty($attr['readPrefix'])) ? $attr['readPrefix'] : '';
	$showreadTime = (!empty($attr['showreadTime'])) ? $attr['showreadTime'] : false;
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	$outputDate='';
	if($showDate){
		$datePrefix = (!empty($attr['datePrefix'])) ? '<span class="tpgb-meta-date-label">'.wp_kses_post($attr['datePrefix']).'</span>' : '';
		$dateIcon = (!empty($attr['dateIcon'])) ? '<i class="meta-date-icon '.esc_attr($attr['dateIcon']).'"></i>' : '';
		$outputDate .='<span class="tpgb-meta-date" >'.$datePrefix.'<a href="'.esc_url(get_the_permalink()).'">'.$dateIcon.esc_html(get_the_date()).'</a></span>';
	}
	
	
	$outputCategory='';
	if( $showCategory ){  //&& !empty(get_the_category($post_id)) 
		$catePrefix = (!empty($attr['catePrefix'])) ? '<span class="tpgb-meta-category-label">'.wp_kses_post($attr['catePrefix']).'</span>' : '';
		$cateDisplayNo = (!empty($attr['cateDisplayNo'])) ? $attr['cateDisplayNo'] : 0;
		$cateStyle = (!empty($attr['cateStyle'])) ? $attr['cateStyle'] : 'style-1';
		$terms = get_the_terms( $post_id, $taxonomySlug, array("hide_empty" => true) );
		$category_list ='';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$i = 1;
			$category_list .= '<span class="tpgb-meta-category-list">';
			foreach ( $terms as $term ) {
				if($cateDisplayNo >= $i){
					$category_list .= '<a href="' . esc_url( get_term_link( $term ) ) . '" alt="' . esc_attr( $term->name ) . '">' . esc_html($term->name) . '</a>';
				}
				$i++;
			}
			$category_list .= '</span>';
		}
		$outputCategory .='<span class="tpgb-meta-category '.esc_attr($cateStyle).'" >'.$catePrefix . $category_list.'</span>';
	}
	
	$outputAuthor='';
	if($showAuthor){
		global $post;
		$author_id = (!empty($post) && isset($post->post_author)) ? $post->post_author : '';
		$authorPrefix = (!empty($attr['authorPrefix'])) ? '<span class="tpgb-meta-author-label">'.wp_kses_post($attr['authorPrefix']).'</span>' : '';
		$authorIcon = (!empty($attr['authorIcon'])) ? $attr['authorIcon'] : '';
		$iconauthor = '';
		if(!empty($authorIcon) && $authorIcon=='profile'){
			$iconauthor = '<span>'.get_avatar( get_the_author_meta('ID'), 200).'</span>';
		}else if(!empty($authorIcon)){
			$iconauthor = '<i class="meta-author-icon '.esc_attr($authorIcon).'"></i>';
		}
		$outputAuthor .='<span class="tpgb-meta-author" >'.$authorPrefix.'<a href="'.esc_url(get_author_posts_url($author_id)).'" rel="'.esc_attr__('author','tpgb').'">'.$iconauthor.''.get_the_author_meta( 'display_name', $author_id ).'</a></span>';
	}
	
	$outputComment='';
	if($showComment){
		$commentIcon =(!empty($attr['commentIcon'])) ? '<i class="meta-comment-icon '.wp_kses_post($attr['commentIcon']).'"></i>' : '';
		$comments_count = wp_count_comments($post_id);
		$count=0;
		if(!empty($comments_count)){
			$count = $comments_count->total_comments;
		}
		if($count===0){
			$comment_text = esc_html__('No Comments','tpgb');
		}else if($count > 0){
			$comment_text = 'Comments('.$count.')';
		}
		$commentPrefix = (!empty($attr['commentPrefix'])) ? '<span class="tpgb-meta-comment-label">'.wp_kses_post($attr['commentPrefix']).'</span>' : '';
		$outputComment .='<span class="tpgb-meta-comment" >'.$commentPrefix.'<a href="'.esc_url(get_the_permalink()).'#respond" rel="'.esc_attr__('comment','tpgb').'">'.$commentIcon.$comment_text.'</a></span>';
	}
	
	$metaExtra = '';
	// Extra Field 
	if(!empty($metafieldRep)){
		foreach ($metafieldRep as $item ) {
			if(isset( $item['metaDynamic'] ) && !empty( $item['metaDynamic'] ) ){
				$metaExtra .= '<span class="tpgb-meta-extra" >';
					if(isset( $item['metaLabel'] ) && !empty( $item['metaLabel'] ) ){
						$metaExtra .= '<span class="tpgb-meta-extra-label">'.wp_kses_post($item['metaLabel']).'</span>';
					}
					
					$metaExtra .= '<span class="tpgb-meta-value">'.wp_kses_post( $item['metaDynamic'] ).'</span>';

					if(isset( $item['metapostfix'] ) && !empty( $item['metapostfix'] ) ){
						$metaExtra .= '<span class="tpgb-meta-epostfix">'.wp_kses_post($item['metapostfix']).'</span>';
					}
					$metaExtra .= '';
				$metaExtra .= '</span>';
			}
		}
	}

	$postRead = '';
	if($showreadTime){
		$content = get_the_content();
		$average_reading_rate = 189;
		$word_count_type = tpgb_get_word_count_type();
		$minutes_to_read = max( 1, (int) round( tpgb_word_count( $content, $word_count_type ) / $average_reading_rate ) );
		$minutes_to_read_string = sprintf(
			_n( '%s minute', '%s minutes', $minutes_to_read ),
			$minutes_to_read
		);

		$postRead .= '<span class="tpgb-meta-read" >';
			if(!empty($readPrefix)){
				$postRead .= '<span class="tpgb-meta-read-label">';
					$postRead .= $readPrefix;
				$postRead .= '</span>';
			}
			$postRead .= $minutes_to_read_string;
		$postRead .= '</span>';
	}
	


    $output .= '<div class="tpgb-post-meta tpgb-block-'.esc_attr($block_id ).' '.esc_attr($blockClass).'" >';
		$output .= '<div class="tpgb-meta-info '.esc_attr($metaLayout).'">';
			foreach($metaSort['sort'] as $item => $value){
				if($value == 'Date') { $output .= $outputDate;  }
				if($value == 'Category') { $output .= $outputCategory;  }
				if($value == 'Author') { $output .= $outputAuthor;  }
				if($value == 'Comments') { $output .= $outputComment;  }
				if($value == 'Post Reading Time') { $output .= $postRead;  }
			}
		$output .= $metaExtra;
		$output .= '</div>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
	}

/**
 * Render for the server-side
 */
function tpgb_post_meta_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'metaLayout' => [
				'type' => 'string',
				'default' => 'layout-1',
			],
			'metaSort' => [
                'type' => 'object',
				'default' => (object)[
					'sort' => ['Date', 'Category', 'Author', 'Comments' , 'Post Reading Time'],
				],
			],
			'metafieldRep' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'metaLabel' => [
							'type' => 'string',
							'default' => '',
						],
						'metaDynamic' => [
							'type' => 'string',
							'default' => '',
						],
						'metapostfix' => [
							'type' => 'string',
							'default' => '',
						],
					],
				],
				'default' => [ 
					[ 'metaLabel' => '', 'metaDynamic' => '', 'metapostfix' => '' ]
				],

			],
			'alignment' => [
				'type' => 'object',
				'default' => [ 'md' => 'left' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info,{{PLUS_WRAP}}.tpgb-post-meta {justify-content: {{alignment}};}',
					],
				],
				'scopy' => true,
			],
			'metaTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info',
					],
				],
				'scopy' => true,
			],
			'metaColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info a{color: {{metaColor}};}',
					],
				],
				'scopy' => true,
            ],
			'labelTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-date-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-author-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-comment-label',
					],
				],
				'scopy' => true,
			],
			'labelColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-date-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-author-label,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-comment-label{color: {{labelColor}};}',
					],
				],
				'scopy' => true,
            ],
			'separator' => [
                'type' => 'string',
				'default' => '|',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'metaLayout', 'relation' => '==', 'value' => 'layout-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info>span:after{content: "{{separator}}";}',
					],
				],
				'scopy' => true,
			],
			'sepLeftSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info > span:after{margin-left: {{sepLeftSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'sepRightSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info>span:after{margin-right: {{sepRightSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'sepSize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info>span:after{font-size: {{sepSize}}px;}',
					],
				],
				'scopy' => true,
			],
			
			'sepColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-info>span:after{color: {{sepColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'showDate' => [
                'type' => 'boolean',
				'default' => true,
			],
			'datePrefix' => [
                'type' => 'string',
				'default' => 'Published On ',
			],
			'dateColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDate', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-date a{color: {{dateColor}};}',
					],
				],
				'scopy' => true,
            ],
			'dateHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDate', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-date a:hover{color: {{dateHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'dateIcon' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'dateIconSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-date .meta-date-icon{margin-right: {{dateIconSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'dateIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDate', 'relation' => '==', 'value' => true],
										['key' => 'dateIcon', 'relation' => '!=', 'value' => '']
										],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-date .meta-date-icon{color: {{dateIconColor}};}',
					],
				],
				'scopy' => true,
            ],
			'dateIconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDate', 'relation' => '==', 'value' => true],
										['key' => 'dateIcon', 'relation' => '!=', 'value' => '']
										],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-date a:hover .meta-date-icon{color: {{dateIconHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'showCategory' => [
                'type' => 'boolean',
				'default' => true,
			],
			'catePrefix' => [
                'type' => 'string',
				'default' => 'in ',
			],
			'taxonomySlug' => [
                'type' => 'string',
				'default' => 'category',
			],
			'cateDisplayNo' => [
                'type' => 'string',
				'default' => 5,
			],
			'cateColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category a,{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category:after{color: {{cateColor}};}',
					],
				],
				'scopy' => true,
            ],
			'cateHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showCategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-category a:hover{color: {{cateHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'cateStyle' => [
                'type' => 'string',
				'default' => 'style-1',
				'scopy' => true,
			],
			'cateSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a{margin-right: {{cateSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'catepadding' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a{padding: {{catepadding}};}',
					],
				],
				'scopy' => true,
			],
			'catemargin' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category a{margin: {{catemargin}};}',
					],
				],
				'scopy' => true,
			],
			'cateBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a',
					],
				],
				'scopy' => true,
			],
			'cateBorderHover' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a:hover',
					],
				],
				'scopy' => true,
			],
			
			'cateBorderRadius' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a{border-radius: {{cateBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'cateBorderRadiusHover' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a:hover{border-radius: {{cateBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'cateBg' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a',
					],
				],
				'scopy' => true,
			],
			'cateBgHover' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a:hover',
					],
				],
				'scopy' => true,
			],
			'cateBoxShadow' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a',
					],
				],
				'scopy' => true,
			],
			'cateBoxShadowHover' => [
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
						'condition' => [(object) ['key' => 'cateStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-meta .tpgb-meta-category.style-2 a:hover',
					],
				],
				'scopy' => true,
			],
			
			'showAuthor' => [
                'type' => 'boolean',
				'default' => true,
			],
			'authorPrefix' => [
                'type' => 'string',
				'default' => 'By ',
			],
			'authorIcon' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'proBradius' => [
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
						'condition' => [(object) ['key' => 'authorIcon', 'relation' => '==', 'value' => 'profile']],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author img{ border-radius : {{proBradius}} }',
					],
				],
				'scopy' => true,
			],
			'authorIconSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author .meta-author-icon,{{PLUS_WRAP}} .tpgb-meta-author img{margin-right: {{authorIconSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'authorIconSize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author img{max-width: {{authorIconSize}}px;}',
					],
				],
				'scopy' => true,
			],
			
			'authorColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showAuthor', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author a{color: {{authorColor}};}',
					],
				],
				'scopy' => true,
            ],
			'authorHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showAuthor', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author a:hover{color: {{authorHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'authorIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showAuthor', 'relation' => '==', 'value' => true],
										['key' => 'authorIcon', 'relation' => '!=', 'value' => 'profile']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author .meta-author-icon{color: {{authorIconColor}};}',
					],
				],
				'scopy' => true,
            ],
			'authorIconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showAuthor', 'relation' => '==', 'value' => true],
										['key' => 'authorIcon', 'relation' => '!=', 'value' => 'profile']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-author a:hover .meta-author-icon{color: {{authorIconHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'showComment' => [
                'type' => 'boolean',
				'default' => true,
			],
			'commentPrefix' => [
                'type' => 'string',
				'default' => 'Comments ',
			],
			'commentIcon' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'commentIconSpace' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-comment .meta-comment-icon{margin-right: {{commentIconSpace}}px;}',
					],
				],
				'scopy' => true,
			],
			'commentColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showComment', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-comment a{color: {{commentColor}};}',
					],
				],
				'scopy' => true,
            ],
			'commentHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showComment', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-comment a:hover{color: {{commentHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'commentIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showComment', 'relation' => '==', 'value' => true],
												['key' => 'commentIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-comment .meta-comment-icon{color: {{commentIconColor}};}',
					],
				],
				'scopy' => true,
            ],
			'commentIconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showComment', 'relation' => '==', 'value' => true],
							['key' => 'commentIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-comment a:hover .meta-comment-icon{color: {{commentIconHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			'showreadTime' => [
				'type' => 'boolean',
				'default' => false,
			],
			'readPrefix' => [
				'type' => 'string',
				'default' => 'Time To Read : ',
			],
			'mreadColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'showreadTime', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-read{color: {{mreadColor}};}',
					],
				],
				'scopy' => true,
			],
			'mreadHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'showreadTime', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-read:hover{color: {{mreadHColor}};}',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra{padding: {{padding}};}',
					],
				],
				'scopy' => true,
			],
			'inMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra{margin: {{inMargin}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra',
					],
				],
				'scopy' => true,
			],
			'boxBorderHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra:hover',
					],
				],
				'scopy' => true,
			],
			
			'boxBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra {border-radius: {{boxBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read:hover ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra:hover {border-radius: {{boxBRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'boxBg' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra',
					],
				],
				'scopy' => true,
			],
			'boxBgHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra:hover',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read ,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-comment:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-category:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-views:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-post-likes:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-date:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-author:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-read:hover,{{PLUS_WRAP}} .tpgb-meta-info .tpgb-meta-extra:hover',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-meta', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_meta_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_meta_content' );

if ( ! function_exists( 'tpgb_get_word_count_type' ) ) {
	function tpgb_get_word_count_type() {
		$word_count_type = _x( 'words', 'Word count type. Do not translate!', 'tpgb' );

		if ( 'characters_excluding_spaces' !== $word_count_type && 'characters_including_spaces' !== $word_count_type ) {
			$word_count_type = 'words';
		}
		return $word_count_type;
	}
}

if ( ! function_exists( 'tpgb_word_count' ) ) {
	function tpgb_word_count( $text, $type, $settings = array() ) {
		$defaults = array(
			'html_regexp'                        => '/<\/?[a-z][^>]*?>/i',
			'html_comment_regexp'                => '/<!--[\s\S]*?-->/',
			'space_regexp'                       => '/&nbsp;|&#160;/i',
			'html_entity_regexp'                 => '/&\S+?;/',
			'connector_regexp'                   => "/--|\x{2014}/u",
			'remove_regexp'                      => "/[\x{0021}-\x{0040}\x{005B}-\x{0060}\x{007B}-\x{007E}\x{0080}-\x{00BF}\x{00D7}\x{00F7}\x{2000}-\x{2BFF}\x{2E00}-\x{2E7F}]/u",
			'astral_regexp'                      => "/[\x{010000}-\x{10FFFF}]/u",
			'words_regexp'                       => '/\S\s+/u',
			'characters_excluding_spaces_regexp' => '/\S/u',
			'characters_including_spaces_regexp' => "/[^\f\n\r\t\v\x{00AD}\x{2028}\x{2029}]/u",
			'shortcodes'                         => array(),
		);

		$count = 0;
		if ( ! $text ) {
			return $count;
		}

		$settings = wp_parse_args( $settings, $defaults );

		// If there are any shortcodes, add this as a shortcode regular expression.
		if ( is_array( $settings['shortcodes'] ) && ! empty( $settings['shortcodes'] ) ) {
			$settings['shortcodes_regexp'] = '/\\[\\/?(?:' . implode( '|', $settings['shortcodes'] ) . ')[^\\]]*?\\]/';
		}

		// Sanitize type to one of three possibilities: 'words', 'characters_excluding_spaces' or 'characters_including_spaces'.
		if ( 'characters_excluding_spaces' !== $type && 'characters_including_spaces' !== $type ) {
			$type = 'words';
		}

		$text .= "\n";

		// Replace all HTML with a new-line.
		$text = preg_replace( $settings['html_regexp'], "\n", $text );

		// Remove all HTML comments.
		$text = preg_replace( $settings['html_comment_regexp'], '', $text );

		// If a shortcode regular expression has been provided use it to remove shortcodes.
		if ( ! empty( $settings['shortcodes_regexp'] ) ) {
			$text = preg_replace( $settings['shortcodes_regexp'], "\n", $text );
		}

		// Normalize non-breaking space to a normal space.
		$text = preg_replace( $settings['space_regexp'], ' ', $text );

		if ( 'words' === $type ) {
			// Remove HTML Entities.
			$text = preg_replace( $settings['html_entity_regexp'], '', $text );

			// Convert connectors to spaces to count attached text as words.
			$text = preg_replace( $settings['connector_regexp'], ' ', $text );

			// Remove unwanted characters.
			$text = preg_replace( $settings['remove_regexp'], '', $text );
		} else {
			// Convert HTML Entities to "a".
			$text = preg_replace( $settings['html_entity_regexp'], 'a', $text );

			// Remove surrogate points.
			$text = preg_replace( $settings['astral_regexp'], 'a', $text );
		}

		// Match with the selected type regular expression to count the items.
		preg_match_all( $settings[ $type . '_regexp' ], $text, $matches );

		if ( $matches ) {
			return count( $matches[0] );
		}

		return $count;
	}
}