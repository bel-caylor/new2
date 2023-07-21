<?php
/* Block : Empty Space
 * @since : 1.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_empty_space_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
    $output .= '<div class="tpgb-empty-space tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
    $output .= '</div>';
  
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_empty_space() {
  register_block_type( 'tpgb/tp-empty-space', array(
		'attributes' => array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'className' => [
				'type' => 'string',
				'default' => '',
			],
			'toggle' => [
				'type' => 'string',
				'default' => 'normal',
			],
			'space' => [
			    'type' => 'object',
				'default' => [ 'md' => 50 ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'toggle', 'relation' => '==', 'value' => 'normal' ]],
						'selector' => '{{PLUS_WRAP}}{height: {{space}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'toggle', 'relation' => '==', 'value' => 'global' ]],
						'selector' => '{{PLUS_WRAP}}{height: {{space}};}',
					],
				],
				'scopy' => true,
			],
		),
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_empty_space_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_empty_space' );