<?php
/* Tp Block : Dark Mode
 * @since	: 1.2.1
 */
function tpgb_tp_dark_mode_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$dmStyle = (!empty($attributes['dmStyle'])) ? $attributes['dmStyle'] : 'style-1';
	$dmPosition = (!empty($attributes['dmPosition'])) ? $attributes['dmPosition'] : 'relative';
	$fixedPos = (!empty($attributes['fixedPos'])) ? $attributes['fixedPos'] : 'left-top';
	$S2IconType = (!empty($attributes['S2IconType'])) ? $attributes['S2IconType'] : 'icon';
	$IconName = (!empty($attributes['IconName'])) ? $attributes['IconName'] : '';
	$darkIconEn = (!empty($attributes['darkIconEn'])) ? $attributes['darkIconEn'] : false;
	$darkIcon = (!empty($attributes['darkIcon'])) ? $attributes['darkIcon'] : '';
	$saveCookies = (!empty($attributes['saveCookies'])) ? $attributes['saveCookies'] : false;
	$matchOsTheme = (!empty($attributes['matchOsTheme'])) ? $attributes['matchOsTheme'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$output = $hideNmlIcon = $fixPosClass = '';
	
	if(!empty($darkIconEn)) {
		$hideNmlIcon = ' hide-normal-icon';
	}
	if($dmPosition=='fixed') {
		$fixPosClass = 'fix-'.$fixedPos;
	}
	
	$output .= '<div class="tpgb-dark-mode tpgb-relative-block dark-pos-'.esc_attr($dmPosition).' darkmode-'.esc_attr($dmStyle).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-id="tpgb-block-'.esc_attr($block_id).'" data-save-cookies="'.esc_attr($saveCookies).'" data-match-os="'.esc_attr($matchOsTheme).'">';
		$output .= '<div class="tpgb-dark-mode-wrap">';
			
			$output .= '<div class="tpgb-darkmode-toggle '.esc_attr($fixPosClass).'">';
				if($dmStyle=='style-1' || $dmStyle=='style-2'){
					$output .= '<span class="tpgb-dark-mode-slider"></span>';
				}else{
					if($S2IconType=='icon'){
						$output .= '<span class="tpgb-normal-icon'.esc_attr($hideNmlIcon).'">';
							$output .= '<i class="'.esc_attr($IconName).'"></i>';
						$output .= '</span>';
						if(!empty($darkIconEn)) {
							$output .= '<span class="tpgb-dark-icon">';
								$output .= '<i class="'.esc_attr($darkIcon).'"></i>';
							$output .= '</span>';
						}
					}
				}
			$output .= '</div>';
		$output .= '</div>';
	$output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_dark_mod() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'dmStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'S2IconType' => [
			'type' => 'string',
			'default' => 'icon',	
		],
		'IconName' => [
			'type'=> 'string',
			'default'=> 'fas fa-sun',
		],
		'darkIconEn' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'darkIcon' => [
			'type'=> 'string',
			'default'=> 'fas fa-moon',
		],
		'saveCookies' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'matchOsTheme' => [
			'type'=> 'boolean',
			'default'=> true,
		],
		'darkIconEn' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'dmPosition' => [
			'type' => 'string',
			'default' => 'relative',
		],
		'Alignment' => [
			'type' => 'object',
			'default' => 'center',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'relative' ]],
					'selector' => '{{PLUS_WRAP}}{ text-align: {{Alignment}}; }',
				],
			],
			'scopy' => true,
		],
		'absoluteOff' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'absolute' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-absolute .tpgb-darkmode-toggle{ left: {{absoluteOff}}; } ',
				],
			],
		],
		'fixedPos' => [
			'type' => 'string',
			'default' => 'left-top',
		],
		'dmRightOf' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'left-top' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ left: {{dmRightOf}}; right: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'left-bottom' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ left: {{dmRightOf}}; right: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'right-top' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ right: {{dmRightOf}}; left: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'right-bottom' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ right: {{dmRightOf}}; left: auto; } ',
				],
			],
		],
		'dmBottomOf' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'left-top' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ top: {{dmBottomOf}}; bottom: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'left-bottom' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ bottom: {{dmBottomOf}}; top: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'right-top' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ top: {{dmBottomOf}}; bottom: auto; } ',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmPosition', 'relation' => '==', 'value' => 'fixed' ], ['key' => 'fixedPos', 'relation' => '==', 'value' => 'right-bottom' ]],
					'selector' => '{{PLUS_WRAP}}.dark-pos-fixed .tpgb-darkmode-toggle{ bottom: {{dmBottomOf}}; top: auto; } ',
				],
			],
		],
		
		/* Switcher Style Start */
		'switchSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle { font-size: {{switchSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle{ font-size: {{switchSize}}; }',
				],
			],
			'scopy' => true,
		],
		'icons2Size' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle{ font-size: {{icons2Size}}; }',
				],
			],
			'scopy' => true,
		],
		'bgs3Size' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle{ width: {{bgs3Size}}; height: {{bgs3Size}}; }',
				],
			],
			'scopy' => true,
		],
		
		'iconLgtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-normal-icon{ color: {{iconLgtColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconDarkColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-normal-icon, {{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-dark-icon{ color: {{iconDarkColor}}; }',
				],
			],
			'scopy' => true,
		],
		'dotLgtBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		'dotDarkBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		'dotLgtBorder' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		'dotDarkBorder' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		'dotLgtBRadius' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-dark-mode-slider:before {border-radius: {{dotLgtBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-dark-mode-slider:before {border-radius: {{dotLgtBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'dotDarkBRadius' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-dark-mode-slider:before {border-radius: {{dotDarkBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-dark-mode-slider:before {border-radius: {{dotDarkBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'dotLgtShadow' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		'dotDarkShadow' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-dark-mode-slider:before',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-dark-mode-slider:before',
				],
			],
			'scopy' => true,
		],
		
		'switchLgtBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		'switchDarkBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		'switchLgtBorder' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		'switchDarkBorder' => [
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
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		'switchLgtBRadius' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle {border-radius: {{switchLgtBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle {border-radius: {{switchLgtBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle {border-radius: {{switchLgtBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'switchDarkBRadius' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-darkmode-toggle {border-radius: {{switchDarkBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-darkmode-toggle {border-radius: {{switchDarkBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-darkmode-toggle {border-radius: {{switchDarkBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'switchLgtShadow' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3 .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		'switchDarkShadow' => [
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
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-2.darkmode-activated .tpgb-darkmode-toggle',
				],
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-3.darkmode-activated .tpgb-darkmode-toggle',
				],
			],
			'scopy' => true,
		],
		/* Switcher Style End */
		
		/* Before-After Text Style Start */
		'beforeText' => [
			'type' => 'string',
			'default' => 'Normal',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'beforeText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:before, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:before{ content: "{{beforeText}}"; }',
				],
			],
		],
		'beforeTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'beforeText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:before, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:before',
				],
			],
			'scopy' => true,
		],
		'beforeColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'beforeText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:before, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:before{ color: {{beforeColor}}; }',
				],
			],
			'scopy' => true,
		],
		'beforeOffset' => [
			'type' => 'object',
			'default' => [ 
				'md' => '-63',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'beforeText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:before, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:before{ left: {{beforeOffset}}; }',
				],
			],
			'scopy' => true,
		],
		'afterText' => [
			'type' => 'string',
			'default' => 'Dark',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'afterText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:after, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:after{ content: "{{afterText}}"; }',
				],
			],
		],
		'afterTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'afterText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:after, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:after',
				],
			],
			'scopy' => true,
		],
		'afterColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'afterText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:after, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:after{ color: {{afterColor}}; }',
				],
			],
			'scopy' => true,
		],
		'afterOffset' => [
			'type' => 'object',
			'default' => [ 
				'md' => '-45',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'dmStyle', 'relation' => '!=', 'value' => 'style-3' ], ['key' => 'afterText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.darkmode-style-1 .tpgb-darkmode-toggle:after, {{PLUS_WRAP}}.darkmode-style-2 .tpgb-darkmode-toggle:after{ right: {{afterOffset}}; }',
				],
			],
			'scopy' => true,
		],
		/* Before-After Text Style End */
	);
	$attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-dark-mode', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_dark_mode_render_callback'
    ) );
}
add_action( 'init', 'tpgb_dark_mod' );