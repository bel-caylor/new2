<?php
/* Block : BlockQuote
 * Author : ThePlus
 * @since : 1.0.0
 */

function tpgb_global_settings_render() {
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
		'PresetColor1' => [
            'type' => 'string',
            'default' => '#8072FC',
        ],
		'PresetColor2' => [
            'type' => 'string',
            'default' => '#6FC784',
        ],
		'PresetColor3' => [
            'type' => 'string',
            'default' => '#FF5A6E',
        ],
		'PresetColor4' => [
            'type' => 'string',
            'default' => '#F3F3F3',
        ],
		'PresetColor5' => [
            'type' => 'string',
            'default' => '#888888',
        ],
		'PresetColor6' => [
            'type' => 'string',
            'default' => '#FFFFFF',
        ],
    ];

    register_block_type( 'tpgb/tpgb-settings', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_global_settings_callback'
    ));
}
add_action( 'init', 'tpgb_global_settings_render' );

/**
 * After rendring from the block editor display output on front-end
 */
function tpgb_global_settings_callback( $attributes, $content ){
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	
    return $output;
}