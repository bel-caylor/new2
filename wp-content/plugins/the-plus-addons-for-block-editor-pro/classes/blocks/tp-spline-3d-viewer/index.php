<?php
/* Block : Spline 3D Viewer
 * @since : 3.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_spline_3d_viewer_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$sFileUrl = (!empty($attributes['sFileUrl'])) ? $attributes['sFileUrl'] : '';
	$svLoadIcon = (!empty($attributes['svLoadIcon'])) ? 'loading-anim="true"' : '';
	$svHintIcon = (!empty($attributes['svHintIcon'])) ? 'hint="true"' : '';
	$targetArea = (!empty($attributes['targetArea']) && $attributes['targetArea'] !='unset') ? 'events-target="'.esc_attr($attributes['targetArea']).'"' : '';
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
			
	$output = '';
    $output .= '<div class="tpgb-spline-3d-viewer tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		$output .= '<div class="tpgb-sv-inner">';
		if(!empty($sFileUrl)){
			$output .= '<spline-viewer url="'.esc_url($sFileUrl).'" '.$svLoadIcon.' '.$svHintIcon.' '.$targetArea.'></spline-viewer>';
			$output .= '<div class="tpgb-sv-loading"></div>';
		}
    	$output .= '</div>';
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_spline_3d_viewer() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'backVisible' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'sFileUrl' => [
			'type' => 'string',
			'default' => TPGB_ASSETS_URL.'assets/file/scene.splinecode',	
		],
		'svLoadIcon' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'svHintIcon' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'targetArea' => [
			'type' => 'string',
			'default' => 'unset',	
		],
		'canWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-sv-inner { width: {{canWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'canHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-sv-inner { height: {{canHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'circleColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-sv-loading { border-color: {{circleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'loaderColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-sv-loading { border-right-color: {{loaderColor}}; }',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-spline-3d-viewer', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_spline_3d_viewer_render_callback'
    ) );
}
add_action( 'init', 'tpgb_spline_3d_viewer' );