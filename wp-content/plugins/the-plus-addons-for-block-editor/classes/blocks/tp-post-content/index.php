<?php
/* Tp Block : Post Content
 * @since	: 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_content_render_callback( $attr, $content, $block) {
	$output = '';
	$post_id = get_the_ID();
    $post = get_queried_object();
    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$types = (!empty($attr['types'])) ? $attr['types'] : 'singular';
	$contentType = (!empty($attr['contentType'])) ? $attr['contentType'] :'';
	$limitCountType = (!empty($attr['limitCountType'])) ? $attr['limitCountType'] :'';
	$titleLimit = (!empty($attr['titleLimit'])) ? $attr['titleLimit'] :'';
    $chideDots = (!empty($attr['chideDots'])) ? $attr['chideDots'] : false ;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );
	
	$content = '';
	if($types == 'archive'){
		if ( is_category() || is_tag() || is_tax() ) {
			$content = term_description();
		}
	}else{
		if($contentType == 'postContent'){
			
			static $views_ids = array();
			if ( ! isset( $post_id ) ) {
				return '';
			}
			if ( isset( $views_ids[ $post_id ] ) ) {
				$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
					defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

				return $is_debug ?
					esc_html__( 'Block Re-rendering halted', 'tpgb' ) :
					'';
			}

			$views_ids[ $post_id ] = true;

			global $current_screen;
			if ( isset($current_screen) && method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ) {
				$content = wp_strip_all_tags(get_the_content( '',true, $post));
			} else {
				$post = get_post($post_id);
				if ( ! $post || 'nxt_builder' == $post->post_type) {
					return '';
				}
				
				if ( ('publish' !== $post->post_status && 'draft' !== $post->post_status && 'private' !== $post->post_status) || ! empty( $post->post_password ) ) {
					return '';
				}
				
				$content = apply_filters( 'the_content', $post->post_content );
			}
			unset( $views_ids[ $post_id ] );
		}else{
			$excerpt = get_post_field('post_excerpt', $post_id, 'display');
			if( $limitCountType == 'words' && !empty($excerpt) ){
				$content = wp_trim_words( $excerpt ,$titleLimit);
			} else if( $limitCountType == 'letters' && !empty($excerpt) ){
				$content = substr(wp_trim_words( $excerpt ),0, $titleLimit) . ( !empty($chideDots) ? '' : '...' );
			} else if( !empty($excerpt) ) {
				$content = $excerpt;
			}else {
				$content = get_the_excerpt();
				if( $limitCountType == 'words' && !empty($content) ){
					$content = wp_trim_words( $content ,$titleLimit);
				} else if( $limitCountType == 'letters' && !empty($content) ){
					$content = substr(wp_trim_words( $content ),0, $titleLimit) . ( !empty($chideDots) ? '' : '...' );
				}
			}
		}
	}

    $output .= '<div class="tpgb-post-content tpgb-block-'.esc_attr($block_id ).' '.esc_attr($blockClass).'" >';
		$output .= '<div class="tpgb-entry-content tpgb-trans-linear">';
			$output .= $content;
		$output .= '</div>';
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_post_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'className' => [
				'type' => 'string',
				'default' => '',
			],
			'types' => [
				'type' => 'string',
				'default' => 'singular',
			],
			'contentType' => [
				'type' => 'string',
				'default' => 'postExcerpt',
			],
			'limitCountType' => [
				'type' => 'string',
				'default' => 'default',
			],
			'titleLimit' => [
				'type' => 'number',
				'default' => '',
			],
			'chideDots' => [
				'type' => 'boolean',
				'default' => false,
			],
			'contentAlign' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content {text-align: {{contentAlign}};}',
					],
				],
				'scopy' => true,
			],
			'contentTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content{padding: {{padding}};}',
					],
				],
				'scopy' => true,
			],
			'contentColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content { color : {{contentColor}}; }',
					],
				],
				'scopy' => true,
			],
			'contentHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content:hover { color : {{contentHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'contentBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content',
					],
				],
				'scopy' => true,
			],
			'contentHvrbg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content:hover',
					],
				],
				'scopy' => true,
			],
			'contentBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content',
					],
				],
				'scopy' => true,
			],
			'contentHvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content:hover',
					],
				],
				'scopy' => true,
			],
			'contentBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content{ border-radius : {{contentBRadius}} }',
					],
				],
				'scopy' => true,
			],
			'contentHvrBra' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content:hover { border-radius : {{contentHvrBra}} }',
					],
				],
				'scopy' => true,
			],
			'contentBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content',
					],
				],
				'scopy' => true,
			],
			'contentHvrSha' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-entry-content:hover',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-post-content', array(
		'attributes' => $attributesOptions,
		"usesContext" => [ "postId", "postType", "queryId" ],
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_content_render_callback'
    ) );
}
add_action( 'init', 'tpgb_post_content' );