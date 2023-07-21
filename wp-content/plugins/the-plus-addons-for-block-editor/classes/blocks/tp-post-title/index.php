<?php
/* Tp Block : Post Title
 * @since	: 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_title_render_callback( $attr, $content) {
	$output = '';
	$post_id = get_the_ID();
    $post = get_queried_object();

    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$types = (!empty($attr['types'])) ? $attr['types'] : 'singular';
	$titlePrefix = (!empty($attr['titlePrefix'])) ? $attr['titlePrefix'] : '';
	$titlePostfix = (!empty($attr['titlePostfix'])) ? $attr['titlePostfix'] : false;
	$postLink = (!empty($attr['postLink'])) ? $attr['postLink'] : false;
	$titleTag = (!empty($attr['titleTag'])) ? $attr['titleTag'] : 'h1';
	$limitCountType = (!empty($attr['limitCountType'])) ? $attr['limitCountType'] : '';
    $titleLimit = (!empty($attr['titleLimit'])) ? $attr['titleLimit'] : '';
	$hideDots = (!empty($attr['hideDots'])) ? $attr['hideDots'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	if( $types == 'archive' ){
		
		$is_archive = is_archive();
		if ( ! $is_archive && !is_search()) {
			return '';
		}

		$title = '';
		if ( $is_archive || is_search()) {
			add_filter( 'get_the_archive_title', function ($title) {    
				if ( is_category() ) {    
					$title = single_cat_title( '', false );    
				} else if ( is_tag() ) {    
					$title = single_tag_title( '', false );    
				} else if ( is_author() ) {    
					$title = '<span class="vcard">' . get_the_author() . '</span>' ;    
				} else if ( is_tax() ) {
					$title = single_term_title( '', false );
				} else if (is_post_type_archive()) {
					$title = post_type_archive_title( '', false );
				} else if (is_search()) {
					$title = get_search_query();
				}
				return $title;    
			});
			
			$title = get_the_archive_title();
		}

		if( $limitCountType == 'words' ){
			$title = wp_trim_words( $title,$titleLimit);
		} else if( $limitCountType == 'letters' ){
			$title = substr(wp_trim_words($title),0, $titleLimit) . (!empty($hideDots) ? '' : '...' );
		}
	}else if($types =='singular'){
		if( $limitCountType == 'words' ){
			$title = wp_trim_words(get_the_title($post_id),$titleLimit);
		} else if( $limitCountType == 'letters' ){
			$title = substr(wp_trim_words(get_the_title($post_id)),0, $titleLimit) . (!empty($hideDots) ? '' : '...' );
		} else {
			$title = get_the_title($post_id);
		}
	}
	
	$prefixOutput = '';
	if(!empty($titlePrefix)){
		$prefixOutput = '<span class="tp-prepost-title tp-prefix-title">'.esc_html($titlePrefix).'</span>';
	}
	$postfixOutput = '';
	if(!empty($titlePostfix)){
		$postfixOutput = '<span class="tp-prepost-title tp-postfix-title">'.esc_html($titlePostfix).'</span>';
	}
	
    $output .= '<div class="tpgb-post-title tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" >';
		if(!empty($postLink)){
			$output .= '<a href="'.esc_url(get_the_permalink()).'" >'; 
		}
			$output .= '<'.Tp_Blocks_Helper::validate_html_tag($titleTag).' class="tpgb-entry-title" >';
				$output .= $prefixOutput . wp_kses_post($title) . $postfixOutput;	
			$output .= '</'.Tp_Blocks_Helper::validate_html_tag($titleTag).'>';
		if(!empty($postLink)){
			$output .= '</a>';
		}
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);

    return $output;
	}

/**
 * Render for the server-side
 */
function tpgb_post_title_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'types' => [
				'type'=> 'string',
				'default'=> 'singular',
            ],
			'titlePrefix' => [
				'type'=> 'string',
				'default'=> '',
            ],
			'titlePostfix' => [
				'type'=> 'string',
				'default'=> '',
            ],
			'postLink' => [
				'type' => 'boolean',
				'default' => false,
			],
			'titleTag' => [
				'type'=> 'string',
				'default'=> 'h1',
            ],
			'limitCountType' => [
				'type'=> 'string',
				'default'=> 'default',
            ],
			'titleLimit' => [
				'type'=> 'string',
				'default'=> '',
            ],
			'hideDots' => [
				'type' => 'boolean',
				'default' => false,
			],
			'titleAlign' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title{text-align: {{titleAlign}};}',
					],
				],
				'scopy' => true,
			],
			'titleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title{padding: {{padding}};}',
					],
				],
				'scopy' => true,
			],
			'titleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title { color : {{titleColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'titleHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title:hover { color : {{titleHvrColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'titleBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title',
					],
				],
				'scopy' => true,
			],
			'titleHvrbg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title:hover',
					],
				],
				'scopy' => true,
			],
			'titleBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title',
					],
				],
				'scopy' => true,
			],
			'titleHvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title:hover',
					],
				],
				'scopy' => true,
			],
			'titleBRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title{ border-radius : {{titleBRadius}} }',
					],
				],
				'scopy' => true,
			],
			'titleHvrBra' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title:hover { border-radius : {{titleHvrBra}} }',
					],
				],
				'scopy' => true,
			],
			'titleBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title',
					],
				],
				'scopy' => true,
			],
			'titleHvrSha' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tpgb-entry-title:hover',
					],
				],
				'scopy' => true,
			],
			
			'prePostPadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title{padding: {{prePostPadding}};}',
					],
				],
				'scopy' => true,
			],
			'prefixOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'titlePrefix', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prefix-title{ margin-right: {{prefixOffset}}; }',
					],
				],
				'scopy' => true,
			],
			'postfixOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'titlePostfix', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-postfix-title{ margin-left: {{postfixOffset}}; }',
					],
				],
				'scopy' => true,
			],
			'prePostTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title',
					],
				],
				'scopy' => true,
			],
			'prePostColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title { color : {{prePostColor}}; }',
					],
				],
				'scopy' => true,
			],
			'prePostBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title',
					],
				],
				'scopy' => true,
			],
			'prePostBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title',
					],
				],
				'scopy' => true,
			],
			'prePostBRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title{ border-radius : {{prePostBRadius}} }',
					],
				],
				'scopy' => true,
			],
			'prePostBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-title .tp-prepost-title',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption );
	
	register_block_type( 'tpgb/tp-post-title', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_title_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_title_content' );