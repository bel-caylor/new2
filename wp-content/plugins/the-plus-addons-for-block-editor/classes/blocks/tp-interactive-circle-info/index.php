<?php
/* Block : Interactive Circle Info
 * @since : 1.3.2
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_interactive_circle_info_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$intCircle = (!empty($attributes['intCircle'])) ? $attributes['intCircle'] : [];
	$mouseTrigger = (!empty($attributes['mouseTrigger'])) ? $attributes['mouseTrigger'] : 'hover';
	$autoTime = (!empty($attributes['autoTime'])) ? $attributes['autoTime'] : 1000;
	$defaultActive = (!empty($attributes['defaultActive'])) ? $attributes['defaultActive'] : 1;
	$outAnimation = (!empty($attributes['outAnimation'])) ? $attributes['outAnimation'] : false;
	$selAnimation = (!empty($attributes['selAnimation'])) ? $attributes['selAnimation'] : 'bounce';
	$carouselToggle = (!empty($attributes['carouselToggle'])) ? $attributes['carouselToggle'] : false;
	$extIndicator = (!empty($attributes['extIndicator'])) ? $attributes['extIndicator'] : [];
	$contiRotate = (!empty($attributes['contiRotate'])) ? $attributes['contiRotate'] : [];
	$carouselID = (!empty($attributes['carouselID'])) ? $attributes['carouselID'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$totalItems = $animationClass = $indicatClass = $contiRotateClass = '';
	foreach ( $intCircle as $index => $item ):
		$totalItems = 1+(int)$index; 
	endforeach;

	if(!empty($outAnimation)){
		$animationClass = 'ia-circle-animation-'.$selAnimation;
	}
	if(!empty($extIndicator) && !empty($extIndicator['tpgbReset'])){
		$indicatClass = 'indicator-'.$extIndicator['indiStyle'];
	}
	if(!empty($contiRotate) && !empty($contiRotate['tpgbReset'])){
		if($contiRotate['animDirection']=='clock-wise'){
			$contiRotateClass = 'circle-continue-rotate';
		}else{
			$contiRotateClass = 'circle-continue-rotate direction-reverse';
		}
	}
	$dAutoTimeAttr = '';
	if($mouseTrigger=='auto'){
		$dAutoTimeAttr = 'data-auto-time="'.esc_attr($autoTime).'"';
	}
	$connect_carousel = $connection_hover_click = $connect_id = '';
	if(!empty($carouselToggle) && !empty($carouselID) && $mouseTrigger!='auto'){
		$connect_carousel = 'tpca-'.$carouselID ;
		$connect_id = 'tptab_'.$carouselID ;
		$connection_hover_click = $mouseTrigger ;
	}
	
	$output = '';
    $output .= '<div class="tpgb-ia-circle-info tpgb-relative-block circle-'.esc_attr($styleType).' '.esc_attr($animationClass).' '.esc_attr($indicatClass).' '.esc_attr($contiRotateClass).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-trigger="'.esc_attr($mouseTrigger).'" '.$dAutoTimeAttr.' id="'.esc_attr($connect_id).'" data-connection="'.esc_attr($connect_carousel).'" data-eventtype="'.esc_attr($connection_hover_click).'">';
		$output .= '<div class="ia-circle-wrap tpgb-rel-flex">';
			$output .= '<div class="ia-circle-inner-wrap" data-total="'.esc_attr($totalItems).'">';
				$output .= '<div class="ia-circle-inner tpgb-trans-linear">';
					foreach ( $intCircle as $index => $item ):
						$itemCount = $defActive = '';
						if(1+(int)$index==$defaultActive){
							$defActive = 'active';
						}
						$imgSrc ='';
						$itemCount = 1+(int)$index;

						$output .= '<div class="tpgb-ia-circle-item tpgb-circle-item-'.esc_attr($itemCount).' tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($defActive).'" data-index="'.esc_attr($itemCount).'">';
							$output .= '<div class="tpgb-circle-icon-wrap tpgb-trans-linear">';
								$output .= '<div class="circle-icon-inner tpgb-rel-flex tpgb-trans-linear">';
									if($item['iconType']=='icon' && !empty($item['iconStore'])){
										$output .= '<i class="'.esc_attr($item['iconStore']).' tpgb-in-circle-icon" aria-hidden="true"></i>';
									}else if($item['iconType']=='image' && !empty($item['imageName'])){
										$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'full';
										if(!empty($item['imageName']) && !empty($item['imageName']['id'])){
											$imgSrc = wp_get_attachment_image($item['imageName']['id'] , $imageSize, false, ['class' => 'tpgb-in-circle-image']);
										}else if(!empty($item['imageName']['url'])){
											$imgSrc = '<img src="'.esc_url($item['imageName']['url']).'" class="tpgb-in-circle-image " />';
										}
										$output .= $imgSrc;
									}
									if(!empty($item['iconTitle'])){
										$output .= '<div class="circle-icon-title">'.wp_kses_post($item['iconTitle']).'</div>';
									}
								$output .= '</div>';

								if(!empty($extIndicator) && !empty($extIndicator['tpgbReset'])){
									$output .= '<div class="tpgb-circle-ext-indicator">';
										$output .= '<div class="tpgb-circle-shape-wrap"><div class="tpgb-circle-shape-inner"></div></div>';
									$output .= '</div>';
								}
								
							$output .= '</div>';
							$output .= '<div class="tpgb-circle-content-wrap tpgb-abs-flex">';
								$output .= '<div class="circle-content-inner tpgb-rel-flex">';
									if(!empty($item['conTitle'])){
										$output .= '<div class="circle-content-title">'.wp_kses_post($item['conTitle']).'</div>';
									}
									if(!empty($item['conDesc'])){
										$output .= '<div class="circle-content-desc">'.wp_kses_post($item['conDesc']).'</div>';
									}
								$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';

					endforeach;
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}
/**
 * Render for the server-side
 */
function tpgb_tp_interactive_circle_info() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'styleType' => [
			'type'=> 'string',
			'default'=> 'style-1',
		],
		'intCircle' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'iconType' => 'icon',
					'iconStore' => [
						'type' => 'string',
						'default' => 'fas fa-check-circle',
					],
					'imageName' => [
						'type' => 'object',
						'default' => [],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'full',	
					],
					'iconTitle' => 'Item',
					'conTitle' => 'Amazing Feature',
					'conDesc' => 'Disrupt inspire and think tank, social entrepreneur but preliminary thinking think tank compelling. Inspiring, invest synergy capacity building, white paper; silo, unprecedented challenge B-corp problem-solvers.',

					'iconNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .circle-icon-inner .tpgb-in-circle-icon{ color: {{iconNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iconHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap:hover .tpgb-in-circle-icon{ color: {{iconHColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iconAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon']],
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item{{TP_REPEAT_ID}}.active .tpgb-in-circle-icon{ color: {{iconAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iTitleNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .circle-icon-inner .circle-icon-title{ color: {{iTitleNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iTitleHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap:hover .circle-icon-title{ color: {{iTitleHColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iTitleAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'iconTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item{{TP_REPEAT_ID}}.active .circle-icon-title{ color: {{iTitleAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'iconNBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .ia-circle-wrap {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap',
							],
						],
						'scopy' => true,
					],
					'iconHBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .ia-circle-wrap {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap:hover',
							],
						],
						'scopy' => true,
					],
					'iconABG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item{{TP_REPEAT_ID}}.active .tpgb-circle-icon-wrap',
							],
						],
						'scopy' => true,
					],
					'iconNBcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap { border-color: {{iconNBcolor}}; }',
							],
						],
						'scopy' => true,
					],
					'iconHBcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-icon-wrap:hover { border-color: {{iconHBcolor}}; }',
							],
						],
						'scopy' => true,
					],
					'iconABcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item{{TP_REPEAT_ID}}.active .tpgb-circle-icon-wrap { border-color: {{iconABcolor}}; }',
							],
						],
						'scopy' => true,
					],

					'cTitleNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap .circle-content-title { color: {{cTitleNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'cTitleHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap:hover .circle-content-title{ color: {{cTitleHColor}}; }',
							],
						],
						'scopy' => true,
					],
					'cTitleAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conTitle', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.active .tpgb-circle-content-wrap .circle-content-title{ color: {{cTitleAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'cDescNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conDesc', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap .circle-content-desc { color: {{cDescNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'cDescHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conDesc', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap:hover .circle-content-desc { color: {{cDescHColor}}; }',
							],
						],
						'scopy' => true,
					],
					'cDescAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'conDesc', 'relation' => '!=', 'value' => '']],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.active .tpgb-circle-content-wrap .circle-content-desc { color: {{cDescAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'contentNBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap .circle-content-inner',
							],
						],
						'scopy' => true,
					],
					'contentHBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap:hover .circle-content-inner',
							],
						],
						'scopy' => true,
					],
					'contentABG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.active .tpgb-circle-content-wrap .circle-content-inner',
							],
						],
						'scopy' => true,
					],
					'contentNBcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap .circle-content-inner { border-color: {{contentNBcolor}}; }',
							],
						],
						'scopy' => true,
					],
					'contentHBcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-circle-content-wrap:hover .circle-content-inner { border-color: {{contentHBcolor}}; }',
							],
						],
						'scopy' => true,
					],
					'contentABcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.active .tpgb-circle-content-wrap .circle-content-inner { border-color: {{contentABcolor}}; }',
							],
						],
						'scopy' => true,
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'iconType' => 'icon',
					"iconStore" => "fas fa-check-circle",
					'iconTitle' => 'Item 1',
					'conTitle' => 'Amazing Feature 1',
					'conDesc' => 'Disrupt inspire and think tank, social entrepreneur but preliminary thinking think tank compelling. Inspiring, invest synergy capacity building, white paper; silo, unprecedented challenge B-corp problem-solvers.',
				],
				[
					'_key' => '1',
					'iconType' => 'icon',
					"iconStore" => "fas fa-check-circle",
					'iconTitle' => 'Item 2',
					'conTitle' => 'Amazing Feature 2',
					'conDesc' => 'Disrupt inspire and think tank, social entrepreneur but preliminary thinking think tank compelling. Inspiring, invest synergy capacity building, white paper; silo, unprecedented challenge B-corp problem-solvers.',
				],
				[
					'_key' => '2',
					'iconType' => 'icon',
					"iconStore" => "fas fa-check-circle",
					'iconTitle' => 'Item 3',
					'conTitle' => 'Amazing Feature 3',
					'conDesc' => 'Disrupt inspire and think tank, social entrepreneur but preliminary thinking think tank compelling. Inspiring, invest synergy capacity building, white paper; silo, unprecedented challenge B-corp problem-solvers.',
				],
			],
		],
		'Alignment' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap{ justify-content: {{Alignment}}; }',
				],
			],
			'scopy' => true,
		],
		'mouseTrigger' => [
			'type'=> 'string',
			'default'=> 'hover',
		],
		'autoTime' => [
			'type'=> 'string',
			'default'=> '1000',
		],
		'defaultActive' => [
			'type' => 'string',
			'default' => '1',	
		],
		'outAnimation' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'selAnimation' => [
			'type'=> 'string',
			'default'=> 'bounce',
		],
		'carouselToggle' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'carouselID' => [
			'type' => 'string',
			'default' => '',	
		],

		'circleWidth' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner{ width: {{circleWidth}}; height: {{circleWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'circleNBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner',
				],
			],
			'scopy' => true,
		],
		'circleHBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner:hover',
				],
			],
			'scopy' => true,
		],
		'circleNbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner',
				],
			],
			'scopy' => true,
		],
		'circleHbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner:hover',
				],
			],
			'scopy' => true,
		],
		'circleNRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner {border-radius: {{circleNRadius}};}',
				],
			],
			'scopy' => true,
		],
		'circleHRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner:hover {border-radius: {{circleHRadius}};}',
				],
			],
			'scopy' => true,
		],
		'circleNShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner',
				],
			],
			'scopy' => true,
		],
		'circleHShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-inner:hover',
				],
			],
			'scopy' => true,
		],

		'iconWidth' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap{ width: {{iconWidth}}; height: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'iconSize' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-inner .tpgb-in-circle-icon{ font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'imageWidth' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-inner .tpgb-in-circle-image{ width: {{imageWidth}}; height: {{imageWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'iTitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-inner .circle-icon-title',
				],
			],
			'scopy' => true,
		],
		'iTitleSpace' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-title { margin-top: {{iTitleSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'iconNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-inner .tpgb-in-circle-icon{ color: {{iconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-icon-wrap:hover .tpgb-in-circle-icon { color: {{iconHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-in-circle-icon{ color: {{iconAColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iTitleNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-icon-inner .circle-icon-title{ color: {{iTitleNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iTitleHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-icon-wrap:hover .circle-icon-title { color: {{iTitleHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iTitleAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-icon-title{ color: {{iTitleAColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconNBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],
		'iconHBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'iconABG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],
		'iconNbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],
		'iconHbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'iconAbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],
		'iconNRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap {border-radius: {{iconNRadius}};}',
				],
			],
			'scopy' => true,
		],
		'iconHRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap:hover {border-radius: {{iconNRadius}};}',
				],
			],
			'scopy' => true,
		],
		'iconARadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-icon-wrap {border-radius: {{iconARadius}};}',
				],
			],
			'scopy' => true,
		],
		'iconNShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],
		'iconHShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .ia-circle-wrap .tpgb-circle-icon-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'iconAShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-icon-wrap',
				],
			],
			'scopy' => true,
		],

		'cTitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-content-inner .circle-content-title',
				],
			],
			'scopy' => true,
		],
		'cDescTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-content-inner .circle-content-desc',
				],
			],
			'scopy' => true,
		],
		'cDescSpace' => [
			'type' => 'object',
			'default' => ["md" => "","unit" => "px"],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .circle-content-inner .circle-content-desc { margin-top: {{cDescSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'contentPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-inner{padding: {{contentPadding}};}',
				],
			],
			'scopy' => true,
		],
		'contentMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap {margin: {{contentMargin}};}',
				],
			],
			'scopy' => true,
		],
		'cTitleNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-title { color: {{cTitleNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cTitleHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-title{ color: {{cTitleHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cTitleAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-title{ color: {{cTitleAColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cDescNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-desc { color: {{cDescNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cDescHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-desc { color: {{cDescHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cDescAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-desc { color: {{cDescAColor}}; }',
				],
			],
			'scopy' => true,
		],
		'contentNBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentHBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentABG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentNbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentHbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentAbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-inner',
				],
			],
			'scopy' => true,
		],
		'contentNRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-circle-content-wrap {border-radius: {{contentNRadius}};}',
				],
			],
			'scopy' => true,
		],
		'contentHRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-circle-content-wrap:hover {border-radius: {{contentHRadius}};}',
				],
			],
			'scopy' => true,
		],
		'contentARadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-ia-circle-item.active .tpgb-circle-content-wrap {border-radius: {{contentARadius}};}',
				],
			],
			'scopy' => true,
		],
		'contentNShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-circle-content-wrap',
				],
			],
			'scopy' => true,
		],
		'contentHShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-content-wrap:hover .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-circle-content-wrap:hover ',
				],
			],
			'scopy' => true,
		],
		'contentAShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .circle-content-inner, {{PLUS_WRAP}}.circle-style-1 .tpgb-ia-circle-item.active .tpgb-circle-content-wrap',
				],
			],
			'scopy' => true,
		],

		'extIndicator' => [
			'type' => 'object',
			'groupField' => [
				(object) [
					'indiStyle' => [
						'type' => 'string',
						'default' => 'style-1',	
					],
					'indiLineWidth' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "px"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap { height: {{indiLineWidth}}; }',
							],
						],
						'scopy' => true,
					],
					'indiLineHeight' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "px"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap { width: {{indiLineHeight}}; }',
							],
						],
						'scopy' => true,
					],
					'edgeWidth' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "px"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { width: {{edgeWidth}};  height: {{edgeWidth}}; left: calc(100% + {{edgeWidth}}/2);}',
							],
						],
						'scopy' => true,
					],
					'edgeDotwidth' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "px"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap::before { width: {{edgeDotwidth}}; height: {{edgeDotwidth}}; }',
							],
						],
						'scopy' => true,
					],
					'edgeCircwidth' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "px"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { border-width: {{edgeCircwidth}}; }',
							],
						],
						'scopy' => true,
					],
					'lineNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap { background: {{lineNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'lineAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap { background: {{lineAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'edgeNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { background: {{edgeNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'edgeAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-ia-circle-item.active .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { background: {{edgeAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'dotNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap::before { background: {{dotNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'dotAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-ia-circle-item.active .tpgb-circle-ext-indicator .tpgb-circle-shape-wrap::before { background: {{dotAColor}}; }',
							],
						],
						'scopy' => true,
					],
					'circleNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { border-color: {{circleNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'circleAColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.indicator-style-5 .tpgb-ia-circle-item.active .tpgb-circle-ext-indicator .tpgb-circle-shape-inner { border-color: {{circleAColor}}; }',
							],
						],
						'scopy' => true,
					],
				],
			],
			'default' => [
				'indiStyle' => 'style-1',
				'indiLineWidth' => ['md' => '', 'unit'=> 'px'],
				'indiLineHeight' => ['md' => '', 'unit'=> 'px'],
				'edgeWidth' => ['md' => '', 'unit'=> 'px'],
				'edgeDotwidth' => ['md' => '', 'unit'=> 'px'],
				'edgeCircwidth' => ['md' => '', 'unit'=> 'px'],
				'lineNColor' => '',
				'edgeNColor' => '',
				'dotNColor' => '',
				'circleNColor' => '',
				'lineAColor' => '',
				'edgeAColor' => '',
				'dotAColor' => '',
				'circleAColor' => '',
			],	
		],

		'contiRotate' => [
			'type' => 'object',
			'groupField' => [
				(object) [
					'animDirection' => [
						'type' => 'string',
						'default' => 'clock-wise',	
					],
					'contiRotateSpeed' => [
						'type' => 'object',
						'default' => ["md" => "","unit" => "s"],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.circle-continue-rotate .ia-circle-inner, {{PLUS_WRAP}}.circle-continue-rotate .ia-circle-inner .tpgb-circle-icon-wrap .circle-icon-inner, {{PLUS_WRAP}}.circle-continue-rotate .ia-circle-inner .tpgb-circle-content-wrap { animation-duration: {{contiRotateSpeed}}; -moz-animation-duration: {{contiRotateSpeed}}; -webkit-animation-duration: {{contiRotateSpeed}}; }',
							],
						],
						'scopy' => true,
					],
				],
			],
			'default' => [
				'animDirection' => 'clock-wise',
				'contiRotateSpeed' => ['md' => '', 'unit'=> 's'],
			],	
		],
		
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-interactive-circle-info', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_interactive_circle_info_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_interactive_circle_info' );