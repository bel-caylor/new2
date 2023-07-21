<?php
/* Block : Switcher
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_switcher_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$switchStyle = (!empty($attributes['switchStyle'])) ? $attributes['switchStyle'] : 'style-1' ;
	$switchalign = (!empty($attributes['switchalign'])) ? $attributes['switchalign'] : 'text-left';
	$title1 = (!empty($attributes['title1'])) ? $attributes['title1'] : '';
	$title2 = (!empty($attributes['title2'])) ? $attributes['title2'] : '';
	$showBtn = (!empty($attributes['showBtn'])) ? $attributes['showBtn'] : false;
	$desc1 = (!empty($attributes['desc1'])) ? $attributes['desc1'] : '';
	$desc2 = (!empty($attributes['desc2'])) ? $attributes['desc2'] : '';
	$source1 = (!empty($attributes['source1'])) ? $attributes['source1'] : '';
	$source2 = (!empty($attributes['source2'])) ? $attributes['source2'] : '';
	$blockTemp1 = (!empty($attributes['blockTemp1'])) ? $attributes['blockTemp1'] : '';
	$blockTemp2 = (!empty($attributes['blockTemp2'])) ? $attributes['blockTemp2'] : '';
	$carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	
	$lblIcon = (!empty($attributes['lblIcon'])) ? $attributes['lblIcon'] : false;
	$switch1Icn = (!empty($attributes['switch1Icn'])) ? $attributes['switch1Icn'] : '';
	$switch2Icn = (!empty($attributes['switch2Icn'])) ? $attributes['switch2Icn'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
    $output .= '<div class="tpgb-switcher tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' ">';
		$output .= '<div id="tpca-'.(!empty($carouselId) ? esc_attr($carouselId) : '').'" class="tpgb-switch-wrap">';
			$output .= '<div class="switch-toggle-wrap switch-'.esc_attr($switchStyle).' '.esc_attr($switchalign). ' inactive ">';
				$output .= '<div class="switch-1">';
					$output .= '<div class="switch-label">';
						if(!empty($lblIcon)){
							$output .= '<i class="tpgb-swt-icon '.esc_attr($switch1Icn).'"></i>';
						}
						$output .= wp_kses_post($title1);
					$output .= '</div>';
				$output .= '</div>';
				if(!empty($showBtn)){
					$output .= '<div class="switcher-button">';
						$output .= '<label class="switch-btn-label">';
							$output .= '<input type="checkbox" class="switch-toggle '.esc_attr($switchStyle).'" />';
							$output .= '<span class="switch-slider switch-round '.esc_attr($switchStyle).'">  </span>';
						$output .= '</label>';
					$output .= '</div>';
				}
				$output .= '<div class="switch-2">';
					$output .= '<div class="switch-label">';
						if(!empty($lblIcon)){
							$output .= '<i class="tpgb-swt-icon '.esc_attr($switch2Icn).'"></i>';
						}
						$output .= wp_kses_post($title2);
					$output .= '</div>';
				$output .= '</div>';
				if($switchStyle == 'style-3'){
					$output .= '<div class="underline"> </div>';
				}
			$output .= '</div>';
			$output .= '<div class="switch-toggle-content">';
				if($source1 == 'editor' || $source2 == 'editor'){
					$output .= $content;
				}else{
					$output .= '<div class="switch-content-1">';
						if(!empty($source1) && $source1 == 'content'){
							$output .= wp_kses_post($desc1);
						}else if($source1 == 'template' && $blockTemp1 != '' && $blockTemp1!='none'){
							ob_start();
								echo Tpgb_Library()->plus_do_block($attributes['blockTemp1']);
							$output .= ob_get_contents();
							ob_end_clean();
						}
					$output .= '</div>';
					$output .= '<div class="switch-content-2">';
						if(!empty($source2) && $source2 == 'content'){
							$output .= wp_kses_post($desc2);
						}else if($source2 == 'template' && $blockTemp2 != '' && $blockTemp2!='none'){
							ob_start();
								echo Tpgb_Library()->plus_do_block($attributes['blockTemp2']);
							$output .= ob_get_contents();
							ob_end_clean();
						}
					$output .=  '</div>';
				}
			$output .= "</div>";
		$output .= "</div>";
    $output .= "</div>";

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_switcher() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'title1' => [
				'type'=> 'string',
				'default'=> 'Switch 1',
			],
			'source1' => [
				'type' => 'string',
				'default' => 'content',	
			],
			'blockTemp1' => [
				'type' => 'string',
				'default' => '',	
			],
			'carouselId' => [
				'type' => 'string',
				'default' => '',	
			],
			'desc1' => [
				'type'=> 'string',
				'default'=> 'This is just dummy content. Put your relevant content over here. We want to remind you, smile and passion are contagious, be a carrier.',
			],
			'title2' => [
				'type'=> 'string',
				'default'=> 'Switch 2',
			],
			'source2' => [
				'type' => 'string',
				'default' => 'content',	
			],
			'blockTemp2' => [
				'type' => 'string',
				'default' => '',	
			],
			'desc2' => [
				'type'=> 'string',
				'default'=> 'Enter your relevant content over here. This is just dummy content. We want to remind you, smile and passion are contagious, be a carrier.',
			],
			'showBtn' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'switchStyle' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'switchalign' => [
				'type' => 'string',
				'default' => 'text-left',
				'scopy' => true,
			],
			'labSpacebet' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => ['style-1' , 'style-2' ]]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap .switch-1{ margin-right: {{labSpacebet}}; }  {{PLUS_WRAP}} .switch-toggle-wrap .switch-2 { margin-left: {{labSpacebet}}; }',
					],
				],
				'scopy' => true,
			],
			'toggleSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => ['style-1' , 'style-2' ]]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap .switcher-button{font-size: {{toggleSize}}}',
					],
				],
				'scopy' => true,
			],
			'switchWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => ['style-3' , 'style-4' ]]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-3,{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-4{ max-width: {{switchWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'switchColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => ['style-1' , 'style-2' ]]],
						'selector' => '{{PLUS_WRAP}} .switch-slider.style-1:before,{{PLUS_WRAP}} .switch-slider.style-2:before{ background: {{switchColor}}; }',
					],
				],
				'scopy' => true,
			],
			'swichBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '!=', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle + .switch-slider,{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-4{ background: {{swichBgcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'labelColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.inactive .switch-2 , {{PLUS_WRAP}} .switch-toggle-wrap.active .switch-1 , {{PLUS_WRAP}} .switch-style-4.active .switch-1 .switch-label  , {{PLUS_WRAP}} .switch-style-4.inactive .switch-2 .switch-label {color : {{labelColor}}; } ',
					],
				],
				'scopy' => true,
			],

			'ActswichBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '!=', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle:checked + .switch-slider,{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-4:before{ background: {{ActswichBgcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'ActlabelColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.inactive .switch-1 , {{PLUS_WRAP}} .switch-toggle-wrap.active .switch-2 , {{PLUS_WRAP}} .switch-style-4.inactive .switch-1 .switch-label  , {{PLUS_WRAP}} .switch-style-4.active .switch-2 .switch-label{color : {{ActlabelColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'switchBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 0,
					'blur' => 10,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.5)",
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '!=', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-slider.style-1:before,{{PLUS_WRAP}} .switch-slider.style-2:before , {{PLUS_WRAP}} .switch-toggle-wrap.switch-style-4 ',
					],
				],
				'scopy' => true,
			],
			'label1Typo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'title1', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-1 .switch-label',
					],
				],
				'scopy' => true,
			],
			'label2Typo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'title2', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-2 .switch-label',
					],
				],
				'scopy' => true,
			],
			'desc1Color' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'desc1', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-content .switch-content-1{color : {{desc1Color}}; }',
					],
				],
				'scopy' => true,
			],
			'desc1Typo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'desc1', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-content .switch-content-1 ',
					],
				],
				'scopy' => true,
			],
			'desc2Color' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'desc2', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-content .switch-content-2{color : {{desc2Color}}; }',
					],
				],
				'scopy' => true,
			],
			'desc2Typo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'desc2', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-content .switch-content-2 ',
					],
				],
				'scopy' => true,
			],
			'lineColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-3 .underline {background : linear-gradient(to right, rgba(0, 227, 246, .04) 0%, {{lineColor}} 50%, rgba(255, 255, 255, .1) 100%) }',
					],
				],
				'scopy' => true,
			],
			'lineOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => 2,
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-3 .underline{ bottom : -{{lineOffset}};}',
					],
				],
				'scopy' => true,
			],
			'lineHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .switch-toggle-wrap.switch-style-3 .underline{ height :  {{lineHeight}};}',
					],
				],
				'scopy' => true,
			],
			
			'lblIcon' => [
				'type' => 'boolean',
				'default' => false,
			],
			'switch1Icn' => [
				'type'=> 'string',
				'default'=> 'fas fa-home',
			],
			'switch2Icn' => [
				'type'=> 'string',
				'default'=> 'fas fa-home',
			],
			'swiIconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'lblIcon', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-switch-wrap .tpgb-swt-icon{ font-size: {{swiIconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'swiIconSpac' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'lblIcon', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-switch-wrap .tpgb-swt-icon{ margin-right : {{swiIconSpac}}; }',
					],
				],
				'scopy' => true,
			],
			'iconNcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'lblIcon', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-switch-wrap .tpgb-swt-icon{ color : {{iconNcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconHvrcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'lblIcon', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-switch-wrap .switch-toggle-wrap.active .switch-2 .tpgb-swt-icon,{{PLUS_WRAP}} .tpgb-switch-wrap .switch-toggle-wrap.inactive .switch-1 .tpgb-swt-icon{ color : {{iconHvrcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'wrapRadius' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'switchStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-switch-wrap .switch-toggle-wrap.switch-style-4{ border-radius : {{wrapRadius}}; }',
					],
				],
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions,$globalPlusExtrasOption, $globalBgOption, $globalpositioningOption);
	
	register_block_type( 'tpgb/tp-switcher', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_switcher_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_switcher' );