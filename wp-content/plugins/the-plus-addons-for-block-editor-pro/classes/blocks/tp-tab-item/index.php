<?php
/* Block : TP Column
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_tab_item_render_callback( $attributes, $content) {

	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$uniqueKey = (!empty($attributes['uniqueKey'])) ? $attributes['uniqueKey'] : '' ;
    $tabtoIndex = (!empty($attributes['tabtoIndex'])) ? $attributes['tabtoIndex'] : '' ;
	$activeTab = (!empty($attributes['activeTab'])) ? $attributes['activeTab'] :'1';

	$active = '';
	if($tabtoIndex==$activeTab){
		$active=' active';
	}
	
	$output .= '<div class="tpgb-tab-content '.esc_attr($active).' " data-tab="'.esc_attr($tabtoIndex).'" role="tabpanel" >';
		$output .= $content;
	$output .= '</div>';

	return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_tab_item() {
	
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'className' => [
			'type' => 'string',
			'default' => '',
		],
		'tabtoIndex' => [
			'type' => 'number',
			'default' => '',
		],
		'tabinTitle' => [
			'type' => 'string',
			'default' => '',
		],
		'uniqueKey' => [
			'type' => 'string',
			'default' => '',
		],
		'active' => [
			'type' => 'string',
			'default' => '',
		],
		'activeTab' => [
			'type' => 'number',
			'default' => 1,
		],
	];
		
	$attributesOptions = array_merge( $attributesOptions );
	
	register_block_type( 'tpgb/tp-tab-item', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_tab_item_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_tab_item' );