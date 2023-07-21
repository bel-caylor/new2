<?php
/* Tp Block : Smooth Scroll
 * @since	: 1.1.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_smooth_scroll_render_callback( $attributes ) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$frameRate = (!empty($attributes['frameRate'])) ? (int)$attributes['frameRate'] : 150;
	$aniTime = (!empty($attributes['aniTime'])) ? (int)$attributes['aniTime'] : 400;
	$stepSize = (!empty($attributes['stepSize'])) ? (int)$attributes['stepSize']  : 100;
	$plusAlgo = (!empty($attributes['plusAlgo'])) ? 1 : 0; 
	$pulseScale = (!empty($attributes['pulseScale'])) ? (int)$attributes['pulseScale'] : 4;
	$pulseNorma = (!empty($attributes['pulseNorma'])) ? (int)$attributes['pulseNorma'] : 1;
	$accDelta = (!empty($attributes['accDelta'])) ? (int)$attributes['accDelta'] : 50;
	$accMax = (!empty($attributes['accMax'])) ? (int)$attributes['accMax'] : 3;
	$keySupp = (!empty($attributes['keySupp'])) ? 1 : 0;
	$arrowSco = (!empty($attributes['arrowSco'])) ? (int)$attributes['arrowSco'] : 50;
	$touchSupp = (!empty($attributes['touchSupp'])) ? 1 : 0;
	$fixSupp = (!empty($attributes['fixSupp'])) ? 1 : 0;
	$browsers = (!empty($attributes['browsers'])) ? $attributes['browsers'] : [];
	$responsive = (!empty($attributes['responsive'])) ? 'yes' : 'no';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	//Set Data Attr For Js
	$dataAttr = [
		'frameRate' => $frameRate,
		'animationTime' => $aniTime,
		'stepSize' => $stepSize,
		'pulseAlgorithm' => $plusAlgo,
		'pulseScale' => $pulseScale,
		'pulseNormalize' => $pulseNorma,
		'accelerationDelta' => $accDelta,
		'accelerationMax' => $accMax,
		'keyboardSupport' => $keySupp,
		'arrowScroll' => $arrowSco,
		'touchpadSupport' => $touchSupp,
		'fixedBackground' => $fixSupp,
		'responsive' => $responsive,
	];
	$bro_arr = array();
	if ( is_string($browsers )) {
		$browsers = json_decode($browsers);
		if (is_array($browsers) || is_object($browsers)) {
			foreach ($browsers as $value) {
				$bro_arr[] = $value->value;
			}
		}
	}
	$bro_arr = !empty($bro_arr) ? $bro_arr : ["ieWin7","chrome","firefox","safari"];
	$bro_arr = json_encode($bro_arr);
	
	$dataAttr = json_encode($dataAttr);

    $output .= '<div class="tpgb-smooth-scroll tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' " data-scrollAttr= \'' . $dataAttr . '\' >';
		$output .= "<script>var smoothAllowedBrowsers = ".($bro_arr)."</script>";
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_smooth_scroll() {
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'className' => [
				'type' => 'string',
				'default' => '',
			],
			'editorOff' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'frameRate' => [
				'type' => 'string',
				'default' => '150',			
			],
			'aniTime' => [
				'type' => 'string',
				'default' => '400',
			],
			'stepSize' => [
				'type' => 'string',
				'default' => '100',
			],
			'plusAlgo' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'pulseScale' => [
				'type' => 'string',
				'default' => '4',
			],
			'pulseNorma' => [
				'type' => 'string',
				'default' => '1',
			],
			'accDelta' => [
				'type' => 'string',
				'default' => '50',
			],
			'accMax' => [
				'type' => 'string',
				'default' => '3',
			],
			'keySupp' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'arrowSco' => [
				'type' => 'string',
				'default' => '50',
			],
			'touchSupp' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'fixSupp' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'browsers'=> [
				'type' => 'string',
				'default' => '[]',
			],
			'responsive' => [
				'type' => 'boolean',
				'default' => true,	
			],
		];
	
	$attributesOptions = array_merge($attributesOptions,$globalpositioningOption);
	
	register_block_type( 'tpgb/tp-smooth-scroll', [
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_smooth_scroll_render_callback'
    ] );
}
add_action( 'init', 'tpgb_tp_smooth_scroll' );