<?php
/* Block : Draw Svg
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_draw_svg_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$duration = (!empty($attributes['duration'])) ? $attributes['duration'] : 90;
	$drawType = (!empty($attributes['drawType'])) ? $attributes['drawType'] : 'delayed';
	$selectSvg = (!empty($attributes['selectSvg'])) ? $attributes['selectSvg'] : 'preBuild';
	$svgList = (!empty($attributes['svgList'])) ? $attributes['svgList'] : 'app';
	$hoverDraw = (!empty($attributes['hoverDraw'])) ? $attributes['hoverDraw'] : 'onScroll';
	$strokeColor = (!empty($attributes['strokeColor'])) ? $attributes['strokeColor'] : '';
	$fillToggle = (!empty($attributes['fillToggle'])) ? $attributes['fillToggle'] : false;
	$fillColor = (!empty($attributes['fillColor'])) ? $attributes['fillColor'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$fillEnable=$fill_color = '';
	if(!empty($fillToggle)){
		$fillEnable = 'yes';
		$fill_color = $fillColor;
	}else{
		$fillEnable = 'no';
		$fill_color = 'none';
	}
	
	$draw_hover = '';
	if($hoverDraw=='onHover'){
		$draw_hover = 'tpgb-hover-draw-svg';
	}
	$svgsrc = '';
	if($selectSvg=='custom'){
		$svgsrc = (isset($attributes['customSVG']['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['customSVG']) : (!empty($attributes['customSVG']['url']) ? $attributes['customSVG']['url'] : '');
	}else{
		$svgsrc = TPGB_URL.'assets/images/svg/'.esc_attr($svgList).'.svg';
	}
	$output = '';
	$output .= '<div class="tpgb-draw-svg tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($draw_hover).'" data-id="tpgb-block-'.esc_attr($block_id).'" data-type="'.esc_attr($drawType).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($strokeColor).'" data-fillcolor="'.esc_attr($fill_color).'" data-fillenable="'.esc_attr($fillEnable).'">';
		$output .= '<div class="svg-inner-block">';
			$output .= '<object id="tpgb-block-'.esc_attr($block_id).'" type="image/svg+xml" role="none" data="'.esc_url($svgsrc).'">';
			$output .= '</object>';
		$output .= '</div>';
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_draw_svg() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'selectSvg' => [
			'type' => 'string',
			'default' => 'preBuild',	
		],
		'svgList' => [
			'type' => 'string',
			'default' => 'app',	
		],
		'customSVG' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/svg/app.svg',
			],
		],
		'alignment' => [
			'type' => 'string',
			'default' => 'center',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}{ text-align: {{alignment}}; }',
				],
			],
			'scopy' => true,
		],
		'maxWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-draw-svg .svg-inner-block{ max-width: {{maxWidth}}; max-height: {{maxWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'strokeColor' => [
			'type' => 'string',
			'default' => '#8072fc',
			'scopy' => true,
		],
		'fillToggle' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'fillColor' => [
			'type' => 'string',
			'default' => '#000000',
			'scopy' => true,
		],
		'drawType' => [
			'type' => 'string',
			'default' => 'delayed',	
			'scopy' => true,
		],
		'duration' => [
			'type' => 'string',
			'default' => '90',	
			'scopy' => true,
		],
		'hoverDraw' => [
			'type' => 'string',
			'default' => 'onScroll',
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-draw-svg', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_draw_svg_render_callback'
    ) );
}
add_action( 'init', 'tpgb_draw_svg' );