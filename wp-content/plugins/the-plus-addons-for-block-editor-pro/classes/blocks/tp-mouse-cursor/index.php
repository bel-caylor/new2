<?php
/**
 * Block : Mouse Cursor
 * @since 1.3.0
 */
defined( 'ABSPATH' ) || exit;
function tpgb_mouse_cursor_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$cursorEffect = (!empty($attributes['cursorEffect'])) ? $attributes['cursorEffect'] : 'mc-body';
	$cursorType = (!empty($attributes['cursorType'])) ? $attributes['cursorType'] : 'mouse-cursor-icon';
	$curIconType = (!empty($attributes['curIconType'])) ? $attributes['curIconType'] : 'icon-predefine';
	$curPreIcon = (!empty($attributes['curPreIcon'])) ? $attributes['curPreIcon'] : 'crosshair';
	$circleStyle = (!empty($attributes['circleStyle'])) ? $attributes['circleStyle'] : 'mc-cs1';
	$mcPointerIcon = (!empty($attributes['mcPointerIcon']['url'])) ? $attributes['mcPointerIcon'] : '';
	$pointerText = (!empty($attributes['pointerText'])) ? $attributes['pointerText'] : '';
	$firstCircleSize = (!empty($attributes['firstCircleSize'])) ? $attributes['firstCircleSize'] : '';
	$secondCircleSize = (!empty($attributes['secondCircleSize'])) ? $attributes['secondCircleSize'] : '';
	
	$textBlockSize = (!empty($attributes['textBlockSize'])) ? $attributes['textBlockSize'] : '';
	$textBlockColor = (!empty($attributes['textBlockColor'])) ? $attributes['textBlockColor'] : '';
	$textBlockWidth = (!empty($attributes['textBlockWidth'])) ? $attributes['textBlockWidth'] : '';
	
	$listTagHover = (!empty($attributes['listTagHover'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['listTagHover']) : 'a';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$mouse_cursor_attr = array();
	
	$iconWidth = (array)$attributes['iconMaxWidth'];
	$mouse_cursor_attr['block_id'] = $block_id;
	$mouse_cursor_attr['mc_cursor_adjust_left'] = (!empty($attributes["pointLeftOffset"])) ? $attributes["pointLeftOffset"] : 0;
	$mouse_cursor_attr['mc_cursor_adjust_top'] = (!empty($attributes["pointTopOffset"])) ? $attributes["pointTopOffset"] : 0;
	$mouse_cursor_attr['effect'] = $cursorEffect;
	if ($cursorEffect =='mc-column' || $cursorEffect =='mc-row' || $cursorEffect =='mc-block' || $cursorEffect =='mc-body' ) {
		$mouse_cursor_attr['type'] = $cursorType;
		if($cursorType =='mouse-cursor-icon'){
			$mouse_cursor_attr['icon_type'] = $curIconType;
			if($curIconType =='icon-predefine'){
				$mouse_cursor_attr['mc_cursor_icon'] = $curPreIcon;
			}else if($curIconType =='icon-custom'){
				$dyMcPointerIcon = (isset($attributes['mcPointerIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcPointerIcon']) : (!empty($attributes['mcPointerIcon']['url']) ? $attributes['mcPointerIcon']['url'] : '');
				$mouse_cursor_attr['mc_cursor_icon'] = $dyMcPointerIcon;
				if( !empty($attributes['mcClick']) && !empty($attributes['mcClickIcon']['url']) ){
					$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
					$dymcClickIcon = (isset($attributes['mcClickIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcClickIcon']) : (!empty($attributes['mcClickIcon']['url']) ? $attributes['mcClickIcon']['url'] : '');
					$mouse_cursor_attr['mc_cursor_see_icon'] = $dymcClickIcon;
				}
			}
		}else if($cursorType =='mouse-follow-image'){
			$dyMcPointerIcon = (isset($attributes['mcPointerIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcPointerIcon']) : (!empty($attributes['mcPointerIcon']['url']) ? $attributes['mcPointerIcon']['url'] : '');
			$mouse_cursor_attr['mc_cursor_icon'] = $dyMcPointerIcon;
			if($cursorEffect =='mc-block'){
				$mouse_cursor_attr['mc_cursor_adjust_width'] = (!empty($iconWidth['md'])) ? $iconWidth['md'].$iconWidth['unit'] : "100px";
			}
			
			if( !empty($attributes['mcClick']) && !empty($attributes['mcClickIcon']['url']) ){
				$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
				$dymcClickIcon = (isset($attributes['mcClickIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcClickIcon']) : (!empty($attributes['mcClickIcon']['url']) ? $attributes['mcClickIcon']['url'] : '');
				$mouse_cursor_attr['mc_cursor_see_icon'] = $dymcClickIcon;
			}
		}else if($cursorType=='mouse-follow-text'){
			$mouse_cursor_attr['mc_cursor_text'] = (!empty($attributes['pointerText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['pointerText']) : '';
			if($cursorEffect=='mc-block'){
				if(!empty($textBlockSize)){
					$mouse_cursor_attr['mc_cursor_text_size'] = $textBlockSize;
				}
				if(!empty($textBlockColor)){
					$mouse_cursor_attr['mc_cursor_text_color'] = $textBlockColor;
				}
				if(!empty($textBlockWidth)){
					$mouse_cursor_attr['mc_cursor_text_width'] = $textBlockWidth;
				}
			}
			if(!empty($attributes['mcClick']) ){
				$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
				$mouse_cursor_attr['mc_cursor_see_text'] = (!empty($attributes['mcClickText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['mcClickText']) : '';
			}	
		}else if($cursorType=='mouse-follow-circle') {	
			$mouse_cursor_attr['circle_type'] = (!empty($attributes['circleCursorType'])) ? $attributes['circleCursorType'] : 'cursor-predefine';
			
			if($attributes['circleCursorType'] == 'cursor-predefine'){
				$mouse_cursor_attr['mc_cursor_adjust_symbol'] = (!empty($attributes["mcCursorSymbol"])) ? $attributes["mcCursorSymbol"] : 'crosshair';
				$mouse_cursor_attr['mc_cursor_adjust_style'] = $circleStyle;
				
				$mouse_cursor_attr['circle_tag_selector'] = $listTagHover;
				if($cursorEffect=='mc-block'){
					$cirMWidth = (array)$attributes['circleMaxWidth'];
					$cirMHeight = (array)$attributes['circleMaxHeight'];
					$mouse_cursor_attr['mc_cursor_adjust_width'] = (!empty($cirMWidth['md']) && !empty($cirMWidth['unit'])) ? $cirMWidth['md'].$cirMWidth['unit'] : "50px";
					$mouse_cursor_attr['mc_cursor_adjust_height'] = (!empty($cirMHeight['md']) && !empty($cirMHeight['unit'])) ? $cirMHeight['md'].$cirMHeight['unit'] : "50px";
					
					$mouse_cursor_attr['mc_circle_transformNml'] = (!empty($attributes["circleTansNmlCss"])) ? $attributes["circleTansNmlCss"] : '';
					$mouse_cursor_attr['mc_circle_transformHvr'] = (!empty($attributes["circleTansHvrCss"])) ? $attributes["circleTansHvrCss"] : '';
					$mouse_cursor_attr['mc_circle_transitionNml'] = (!empty($attributes["circleNmlTranDur"])) ? $attributes["circleNmlTranDur"] : '0.3';
					$mouse_cursor_attr['mc_circle_transitionHvr'] = (!empty($attributes["circleHvrTranDur"])) ? $attributes["circleHvrTranDur"] : '0.3';
					$mouse_cursor_attr['mc_circle_zindex'] = (!empty($attributes["circleZindex"])) ? (int)$attributes["circleZindex"] : 1;
					
			        if($circleStyle == 'mc-cs3'){
						$mouse_cursor_attr['style_two_blend_mode'] = (!empty($attributes["crclMixBMode"])) ? $attributes["crclMixBMode"] : 'difference';
			        }
					$mouse_cursor_attr['style_two_bg'] = (!empty($attributes["circleNmlBG"])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes["circleNmlBG"]) : '';
					$mouse_cursor_attr['style_two_bgh'] = (!empty($attributes["circleHvrBG"])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes["circleHvrBG"]) : '';
				}	       
			}else if($attributes['circleCursorType'] == 'cursor-custom'){
				$dyMcPointerCirIcon = (isset($attributes['mcPointerCirIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcPointerCirIcon']) : (!empty($attributes['mcPointerCirIcon']['url']) ? $attributes['mcPointerCirIcon']['url'] : '');
				$mouse_cursor_attr['mc_cursor_icon'] = $dyMcPointerCirIcon;
				if( !empty($attributes['mcClick']) && !empty($attributes['mcClickIcon']['url']) ){
					$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
					$dyMcClickIcon = (isset($attributes['mcClickIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['mcClickIcon']) : (!empty($attributes['mcClickIcon']['url']) ? $attributes['mcClickIcon']['url'] : '');
					$mouse_cursor_attr['mc_cursor_see_icon'] = $dyMcClickIcon;
				}
			}
		}
	}
	$mouse_cursor_attr = htmlspecialchars(json_encode($mouse_cursor_attr), ENT_QUOTES, 'UTF-8');
	$progressClass = '';
	if($cursorEffect=='mc-body' && $cursorType=='mouse-follow-circle' && $circleStyle=='mc-cs2'){
		$progressClass = 'tpgb-percent-circle';
	}
	$output = '';
    $output .= '<div class="tpgb-mouse-cursor tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'"  data-tpgb_mc_settings=\'' .$mouse_cursor_attr. '\'>';
		if($cursorEffect!='mc-block'){
			if($cursorType=='mouse-follow-text'){
				 $output .= '<div class="tpgb-cursor-pointer-follow-text">'.wp_kses_post($pointerText).'</div>';
			}else if( $cursorType=='mouse-follow-image' && !empty($mcPointerIcon['url']) ){
				$output .= '<img src="'.esc_url($mcPointerIcon['url']).'" class="tpgb-cursor-pointer-follow " />';
			}else if($cursorType=='mouse-follow-circle'){
				$output .= '<div class="tpgb-cursor-follow-circle '.esc_attr($progressClass).'">';
				
				if($cursorEffect=='mc-body' && $circleStyle=='mc-cs2'){
					$output .='<svg class="tpgb-mc-svg-circle" width="200" height="200" viewport="0 0 100 100" xmlns="https://www.w3.org/2000/svg"><circle class="tpgb-mc-circle-st1" cx="100" cy="100" r="'.esc_attr($firstCircleSize).'"></circle><circle class="tpgb-mc-circle-st1 tpgb-mc-circle-progress-bar" cx="100" cy="100" r="'.esc_attr($secondCircleSize).'"></circle></svg>';
				}
				$output .= '</div>';
			}
		}
    $output .= '</div>';
  
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_mouse_cursor() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'backVis' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'cursorEffect' => [
			'type' => 'string',
			'default' => 'mc-body',	
		],
		'cursorType' => [
			'type' => 'string',
			'default' => 'mouse-cursor-icon',	
		],
		'curIconType' => [
			'type' => 'string',
			'default' => 'icon-predefine',	
		],
		'curPreIcon' => [
			'type' => 'string',
			'default' => 'crosshair',	
		],
		'circleCursorType' => [
			'type' => 'string',
			'default' => 'cursor-predefine',	
		],
		'mcCursorSymbol' => [
			'type' => 'string',
			'default' => 'crosshair',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ cursor: {{mcCursorSymbol}}; }',
				],
			],
			'scopy' => true,
		],
		'circleStyle' => [
			'type' => 'string',
			'default' => 'mc-cs1',	
		],
		'mcPointerCirIcon' => [
			'type' => 'object',
			'default' => [],
		],
		'mcPointerIcon' => [
			'type' => 'object',
			'default' => [],
		],
		'iconMaxWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				'unit' => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-image']],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow { max-width: {{iconMaxWidth}};}',
				],
			],
			'scopy' => true,
		],
		'circleMaxWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-body']],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle { max-width: {{circleMaxWidth}}; width: {{circleMaxWidth}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body'], ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle'], ['key' => 'circleStyle', 'relation' => '!=', 'value' => 'mc-cs2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle { max-width: {{circleMaxWidth}}; width: {{circleMaxWidth}};}',
				],
			],
			'scopy' => true,
		],
		'circleMaxHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-body']],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle { max-height: {{circleMaxHeight}}; height: {{circleMaxHeight}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body'], ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle'], ['key' => 'circleStyle', 'relation' => '!=', 'value' => 'mc-cs2']],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle { max-height: {{circleMaxHeight}}; height: {{circleMaxHeight}};}',
				],
			],
			'scopy' => true,
		],
		'firstCircleSize' => [
			'type' => 'string',
			'default' => '50',
		],
		'secondCircleSize' => [
			'type' => 'string',
			'default' => '30',
		],
		'pointerText' => [
			'type' => 'string',
			'default' => 'Follow Text',	
		],
		'pointLeftOffset' => [
			'type' => 'string',
			'default' => '10',
		],
		'pointTopOffset' => [
			'type' => 'string',
			'default' => '10',
		],
		'circleZindex' => [
			'type' => 'string',
			'default' => '99',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ z-index: {{circleZindex}}; }',
				],
			],
			'scopy' => true,
		],
		'mcClick' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'mcClickIcon' => [
			'type' => 'object',
			'default' => [],
		],
		'mcClickText' => [
			'type' => 'string',
			'default' => 'See More',	
		],
		'listTagHover' => [
			'type' => 'string',
			'default' => 'a',	
		],
		'textTyp' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text',
				],
			],
			'scopy' => true,
		],
		'textColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text{ color: {{textColor}}; }',
				],
			],
		],
		'textMWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text{ max-width: {{textMWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'textPadding' => [
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
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text{padding: {{textPadding}};} ',
				],
			],
			'scopy' => true,
		],
		'textBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text',
				],
			],
			'scopy' => true,
		],
		'textBorder' => [
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
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text',
				],
			],
			'scopy' => true,
		],
		'textBRadius' => [
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
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text{border-radius: {{textBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'textBoxShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-block' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-pointer-follow-text',
				],
			],
			'scopy' => true,
		],
		'textBlockSize' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'textBlockColor' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'textBlockWidth' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'circleCNmlBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ],(object) ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-custom' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ background-color: {{circleCNmlBG}}; }',
				],
			],
			'scopy' => true,
		],
		'crclMixBMode' => [
			'type' => 'string',
			'default' => 'difference',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs3' ]],
					'selector' => '{{PLUS_WRAP}},{{PLUS_WRAP}} .tpgb-cursor-follow-circle{mix-blend-mode: {{crclMixBMode}};}',
				],
			],
			'scopy' => true,
		],
		'circleNmlBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ],(object) ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ],(object) ['key' => 'circleStyle', 'relation' => '!=', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ background-color: {{circleNmlBG}}; }',
				],(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ],(object) ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ],(object) ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ],['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-body' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ background-color: {{circleNmlBG}}; }',
				],
			],
			'scopy' => true,
		],
		'circleOpacity' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs1' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ opacity: {{circleOpacity}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2NmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ stroke: {{circles2NmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2StrokeNWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ stroke-width: {{circles2StrokeNWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2NmlFill' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ fill: {{circles2NmlFill}}; }',
				],
			],
			'scopy' => true,
		],
		'circlecs2NPrgrssColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-progress-bar{ stroke: {{circlecs2NPrgrssColor}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2NPrgrssWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-progress-bar{ stroke-width: {{circles2NPrgrssWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'circleTansNmlCss' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ transform: {{circleTansNmlCss}}; }',
				],
			],
			'scopy' => true,
		],
		'circleNmlTranDur' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-cursor-follow-circle{ transition: transform {{circleNmlTranDur}}s ease, background {{circleNmlTranDur}}s ease; }',
				],
			],
			'scopy' => true,
		],
		'circleHvrBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ],(object) ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ],(object) ['key' => 'circleStyle', 'relation' => '!=', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-cursor-follow-circle{ background-color: {{circleHvrBG}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ],(object) ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ],(object) ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ],['key' => 'cursorEffect', 'relation' => '!=', 'value' => 'mc-body' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-cursor-follow-circle{ background-color: {{circleHvrBG}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2HvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ], ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ stroke: {{circles2HvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2StrokeHWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ stroke-width: {{circles2StrokeHWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2HvrFill' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-st1{ fill: {{circles2HvrFill}}; }',
				],
			],
			'scopy' => true,
		],
		'circlecs2HPrgrssColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-progress-bar{ stroke: {{circlecs2HPrgrssColor}}; }',
				],
			],
			'scopy' => true,
		],
		'circles2HPrgrssWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorEffect', 'relation' => '==', 'value' => 'mc-body' ],['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ], ['key' => 'circleStyle', 'relation' => '==', 'value' => 'mc-cs2' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-mc-svg-circle .tpgb-mc-circle-progress-bar{ stroke-width: {{circles2HPrgrssWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'circleTansHvrCss' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-cursor-follow-circle{ transform: {{circleTansHvrCss}}; }',
				],
			],
			'scopy' => true,
		],
		'circleHvrTranDur' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cursorType', 'relation' => '==', 'value' => 'mouse-follow-circle' ], ['key' => 'circleCursorType', 'relation' => '==', 'value' => 'cursor-predefine' ]],
					'selector' => '.tpgb-mouse-hover-active {{PLUS_WRAP}} .tpgb-cursor-follow-circle{ transition: transform {{circleHvrTranDur}}s ease, background {{circleHvrTranDur}}s ease; }',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption);
	
	register_block_type( 'tpgb/tp-mouse-cursor', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_mouse_cursor_render_callback'
    ) );
}
add_action( 'init', 'tpgb_mouse_cursor' );