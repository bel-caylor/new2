<?php
/* Block : Progress Tracker
 * @since : 3.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_progress_tracker_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$progressType = (!empty($attributes['progressType'])) ? $attributes['progressType'] : 'horizontal';
	$horizontalPos = (!empty($attributes['horizontalPos'])) ? $attributes['horizontalPos'] : 'top';
	$hzDirection = (!empty($attributes['hzDirection'])) ? $attributes['hzDirection'] : 'ltr';
	$verticalPos = (!empty($attributes['verticalPos'])) ? $attributes['verticalPos'] : 'left';
	$circularPos = (!empty($attributes['circularPos'])) ? $attributes['circularPos'] : 'top-left';
	$percentageText = (!empty($attributes['percentageText'])) ? $attributes['percentageText'] : false;
	$percentageStyle = (!empty($attributes['percentageStyle'])) ? $attributes['percentageStyle'] : 'style-1';
	$circleSize = (!empty($attributes['circleSize'])) ? $attributes['circleSize'] : '50';
	$applyTo = (!empty($attributes['applyTo'])) ? $attributes['applyTo'] : 'entire';
	$unqSelector = (!empty($attributes['unqSelector'])) ? $attributes['unqSelector'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$relTselector = (!empty($attributes['relTselector']) && !empty($unqSelector) && $applyTo=='selector') ? 'tracker-rel-sel' : '';

	$positionClass = $posClass = '';
	$positionClass = 'tpgb-fixed-block';
	if($progressType=='horizontal'){
		$posClass = 'pos-'.$horizontalPos.' direction-'.$hzDirection;
	}else if($progressType=='vertical'){
		$posClass = 'pos-'.$verticalPos;
	}else{
		$posClass = 'pos-'.$circularPos;
	}

	$data_attr=[];
	$data_attr['apply_to'] = $applyTo;
	if($applyTo=='selector' && !empty($unqSelector)){
		$data_attr['selector'] = $unqSelector;
	}
	$data_attr = 'data-attr="'.htmlspecialchars(json_encode($data_attr, true), ENT_QUOTES, 'UTF-8').'"';
		
	$output = '';
    $output .= '<div class="tpgb-progress-tracker tpgb-relative-block type-'.esc_attr($progressType).' '.esc_attr($relTselector).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" '.$data_attr.'>';
		$output .= '<div class="tpgb-progress-track '.esc_attr($positionClass).' '.esc_attr($posClass).'">';
		if($progressType!='circular'){
			$output .= '<div class="progress-track-fill">';
			if(!empty($percentageText)){
				$output .= '<div class="progress-track-percentage '.esc_attr($percentageStyle).'"></div>';
			}
			$output .= '</div>';
		}else{
			$output .='<svg class="tpgb-pt-svg-circle" width="200" height="200" viewport="0 0 100 100" xmlns="https://www.w3.org/2000/svg">
			<circle class="tpgb-pt-circle-st" cx="100" cy="100" r="'.esc_attr($circleSize).'"></circle>
			<circle class="tpgb-pt-circle-st1" cx="100" cy="100" r="'.esc_attr($circleSize).'"></circle>
			<circle class="tpgb-pt-circle-st2" cx="100" cy="100" r="'.esc_attr($circleSize).'"></circle></svg>';
			if(!empty($percentageText)){
				$output .= '<div class="progress-track-percentage"></div>';
			}
		}
		$output .= '</div>';
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_progress_tracker() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'progressType' => [
			'type' => 'string',
			'default' => 'horizontal',	
		],
		'horizontalPos' => [
			'type' => 'string',
			'default' => 'top',	
		],
		'hzDirection' => [
			'type' => 'string',
			'default' => 'ltr',	
		],
		'verticalPos' => [
			'type' => 'string',
			'default' => 'left',	
		],
		'circularPos' => [
			'type' => 'string',
			'default' => 'top-left',	
		],
		'cPosTopOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '0',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'top-left' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-top-left{ top : {{cPosTopOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'top-right' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-top-right{ top : {{cPosTopOff}}; }',
				],
			],
			'scopy' => true,
		],
		'cPosBottomOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '0',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'bottom-left' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-bottom-left{ bottom : {{cPosBottomOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'bottom-right' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-bottom-right{ bottom : {{cPosBottomOff}}; }',
				],
			],
			'scopy' => true,
		],
		'cPosLeftOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '0',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'top-left' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-top-left{ left : {{cPosLeftOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'center-left' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-center-left{ left : {{cPosLeftOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'bottom-left' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-bottom-left{ left : {{cPosLeftOff}}; }',
				],
			],
			'scopy' => true,
		],
		'cPosRightOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '0',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'top-right' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-top-right{ right : {{cPosRightOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'center-right' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-center-right{ right : {{cPosRightOff}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ], ['key' => 'circularPos', 'relation' => '==', 'value' => 'bottom-right' ] ],
					'selector' => '{{PLUS_WRAP}}.type-circular .pos-bottom-right{ right : {{cPosRightOff}}; }',
				],
			],
			'scopy' => true,
		],
		'applyTo' => [
			'type' => 'string',
			'default' => 'entire',	
		],
		'unqSelector' => [
			'type' => 'string',
			'default' => '',	
		],
		'relTselector' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'percentageText' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'percentageStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'circleSize' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'circleBGColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ] ],
					'selector' => '{{PLUS_WRAP}} circle.tpgb-pt-circle-st { fill: {{circleBGColor}}; }',
				],
			],
			'scopy' => true,
		],
		'trackSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .tpgb-progress-track{ height : {{trackSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .tpgb-progress-track{ width : {{trackSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ] ],
					'selector' => '{{PLUS_WRAP}} circle.tpgb-pt-circle-st1, {{PLUS_WRAP}} circle.tpgb-pt-circle-st2{ stroke-width : {{trackSize}}; }',
				],
			],
			'scopy' => true,
		],
		'trackBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .tpgb-progress-track',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .tpgb-progress-track',
				],
			],
			'scopy' => true,
		],
		'trackBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .tpgb-progress-track',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .tpgb-progress-track',
				],
			],
			'scopy' => true,
		],
		'trackBRadius' => [
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
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .tpgb-progress-track, {{PLUS_WRAP}}.type-horizontal .progress-track-fill{border-radius: {{trackBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .tpgb-progress-track, {{PLUS_WRAP}}.type-vertical .progress-track-fill{border-radius: {{trackBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'trackBShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => 0,
				'vertical' => 8,
				'blur' => 20,
				'spread' => 1,
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .tpgb-progress-track',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .tpgb-progress-track',
				],
			],
			'scopy' => true,
		],
		'fillBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ] ],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .progress-track-fill',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ] ],
					'selector' => '{{PLUS_WRAP}}.type-vertical .progress-track-fill',
				],
			],
			'scopy' => true,
		],
		'cTrackColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ] ],
					'selector' => '{{PLUS_WRAP}} circle.tpgb-pt-circle-st1 { stroke: {{cTrackColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cTrackDShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'typeShadow' => 'drop-shadow', 
				'horizontal' => 2,
				'vertical' => 3,
				'blur' => 2,
				'color' => "rgba(0,0,0,0.5)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-pt-svg-circle',
				],
			],
			'scopy' => true,
		],
		'cFillColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'circular' ] ],
					'selector' => '{{PLUS_WRAP}} circle.tpgb-pt-circle-st2 { stroke: {{cFillColor}}; }',
				],
			],
			'scopy' => true,
		],

		'texTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'percentageText', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .progress-track-percentage',
				],
			],
			'scopy' => true,
		],
		'ttPadding' => [
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
					'condition' => [(object) ['key' => 'progressType', 'relation' => '!=', 'value' => 'circular' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .progress-track-percentage.style-2, {{PLUS_WRAP}}.type-vertical .progress-track-percentage.style-2{padding: {{ttPadding}};}',
				],
			],
			'scopy' => true,
		],
		'textColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'percentageText', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .progress-track-percentage { color: {{textColor}}; }',
				],
			],
			'scopy' => true,
		],
		'ttBGColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '!=', 'value' => 'circular' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .progress-track-percentage.style-2, {{PLUS_WRAP}}.type-vertical .progress-track-percentage.style-2 { background-color: {{ttBGColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ],['key' => 'horizontalPos', 'relation' => '==', 'value' => 'top' ]],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .progress-track-percentage.style-2::before { border-color: transparent transparent {{ttBGColor}} transparent; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'horizontal' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ],['key' => 'horizontalPos', 'relation' => '==', 'value' => 'bottom' ]],
					'selector' => '{{PLUS_WRAP}}.type-horizontal .progress-track-percentage.style-2::before { border-color: {{ttBGColor}} transparent transparent transparent; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ],['key' => 'verticalPos', 'relation' => '==', 'value' => 'left' ]],
					'selector' => '{{PLUS_WRAP}}.type-vertical .progress-track-percentage.style-2::before { border-color: transparent {{ttBGColor}} transparent transparent; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'vertical' ],['key' => 'percentageStyle', 'relation' => '==', 'value' => 'style-2' ],['key' => 'percentageText', 'relation' => '==', 'value' => true ],['key' => 'verticalPos', 'relation' => '==', 'value' => 'right' ]],
					'selector' => '{{PLUS_WRAP}}.type-vertical .progress-track-percentage.style-2::before { border-color: transparent transparent transparent {{ttBGColor}} }',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$plusButton_options, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-progress-tracker', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_progress_tracker_render_callback'
    ) );
}
add_action( 'init', 'tpgb_progress_tracker' );