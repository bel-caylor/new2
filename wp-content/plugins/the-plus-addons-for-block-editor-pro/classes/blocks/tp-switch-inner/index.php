<?php
/* Block : TP Column
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_switch_inner_render_callback( $attributes, $content) {

	$output = '';
	$index = (!empty($attributes['index'])) ? $attributes['index'] : '';

	$output .= '<div class="switch-content-'.esc_attr($index).'">';
		$output .= $content;
	$output .= '</div>';

	return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_switch_inner() {
	
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'className' => [
			'type' => 'string',
			'default' => '',
		],
		'index' => [
			'type' => 'number',
			'default' => '',
		],
	];
		
	$attributesOptions = array_merge( $attributesOptions );
	
	register_block_type( 'tpgb/tp-switch-inner', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_switch_inner_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_switch_inner' );