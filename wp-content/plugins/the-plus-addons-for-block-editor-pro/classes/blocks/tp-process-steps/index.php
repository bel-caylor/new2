<?php
/* Block : Process Steps
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_process_steps( $attributes, $content) {
	$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$dfltActive = (!empty($attributes['dfltActive'])) ? $attributes['dfltActive'] :'none';
	$nmlLoutMobile = (!empty($attributes['nmlLoutMobile'])) ? $attributes['nmlLoutMobile'] : false;
	$vertOnTablet = (!empty($attributes['vertOnTablet'])) ? $attributes['vertOnTablet'] : false;
	$imgSt2Align = (!empty($attributes['imgSt2Align'])) ? $attributes['imgSt2Align'] : '';
	$cntntSt2Align = (!empty($attributes['cntntSt2Align'])) ? $attributes['cntntSt2Align'] : '';
	$processSteps = (!empty($attributes['processSteps'])) ? $attributes['processSteps'] : [];
	$carouselToggle = (!empty($attributes['carouselToggle'])) ? $attributes['carouselToggle'] : false;
	$carouselID = (!empty($attributes['carouselID'])) ? $attributes['carouselID'] : '';
	$carouselEffect = (!empty($attributes['carouselEffect'])) ? $attributes['carouselEffect'] : 'con_pro_hover';
	$specialBG = (!empty($attributes['specialBG'])) ? $attributes['specialBG'] : false;
	$displayCounter = (!empty($attributes['displayCounter'])) ? $attributes['displayCounter'] : false;
	$counterStyle = (!empty($attributes['counterStyle'])) ? $attributes['counterStyle'] : 'number_normal';
	$bType = (!empty($attributes['bType'])) ? $attributes['bType'] : 'solid';
	$customImg = (!empty($attributes['customImg'])) ? $attributes['customImg'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}

	$connect_carousel = $connection_hover_click = $connect_id = '';
	if(!empty($carouselToggle) && !empty($carouselID)){
		$connect_carousel = 'tpca-'.$carouselID ;
		$connect_id = 'tptab_'.$carouselID ;
		$connection_hover_click = $carouselEffect ;
	}
		
	$j=0;
	
	$specbg='';
	if(!empty($specialBG)){
		$specbg='tp-ps-special-bg';
	}
	$custom_sep='';
	if($bType=='custom'){
		$custom_sep = 'tp_ps_sep_img';
	}
	$verti_tablet_class = $mobile_class='';
	if(!empty($nmlLoutMobile)){
		$mobile_class = 'mobile';
	}
	if(!empty($vertOnTablet)){
		$verti_tablet_class = 'verticle-tablet';
	}
	$flexCss = ''; 
	if($style=='style-2'){
		$flexCss = 'tpgb-rel-flex';
	}
	$output = '';
    	$output .= '<div class="tpgb-process-steps '.esc_attr($flexCss).' '.esc_attr($style).' '.esc_attr($custom_sep).' '.esc_attr($mobile_class).' '.esc_attr($verti_tablet_class).' '.esc_attr($imgSt2Align).' '.esc_attr($cntntSt2Align).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($equalHclass).'" id="'.esc_attr($connect_id).'" data-connection="'.esc_attr($connect_carousel).'" data-eventtype="'.esc_attr($connection_hover_click).'" '.$equalHeightAtt.'>';
			if(!empty($processSteps)){
				foreach ( $processSteps as $index => $item ) { 
					$j++;
					
					//Set Active class 
					$active_class = '';
					if($j == $dfltActive){
						$active_class = 'active';
					}
					
					$output .='<div class="tp-repeater-item-'.esc_attr($item['_key']).' tpgb-p-s-wrap tpgb-trans-easeinout '.esc_attr($active_class).'" data-index="'.esc_attr($j).'">';
						if(!empty($item['selectIcon']) && $item['selectIcon']!='none'){
							$output .='<div class="tp-ps-left-imt '.esc_attr($specbg).'">';
							if($bType=='custom'){
								if(isset($customImg['dynamic'])){
									$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($customImg);
									$customImgRender = '<img class="tp-sep-custom-img-inner" src="'.esc_url($imgUrl).'" />';
								}else if(!empty($customImg) && !empty($customImg['id'])){
									$customImgRender = wp_get_attachment_image($customImg['id'] , 'full', false, ['class' => 'tp-sep-custom-img-inner']);
								}else if(!empty($customImg['url'])){
									$customImgRender = '<img class="tp-sep-custom-img-inner" src="'.esc_url($customImg['url']).'" />';
								}else{
									$customImgRender = '<img class="tp-sep-custom-img-inner" src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'" alt=""/>';
								}
								$output .='<span class="separator_custom_img">';
									$output .= $customImgRender;
								$output .='</span>';
							}
								
							if(!empty($item['selectIcon']) && $item['selectIcon']=='icon' && !empty($item['fontAwesomeIcon'])){
								$output .='<span class="tp-ps-icon-img tpgb-rel-flex tpgb-trans-easeinout '.esc_attr($specbg).'">';
									$output .='<i aria-hidden="true" class="'.esc_attr($item['fontAwesomeIcon']).'"></i>';
								$output .='</span>';
							}
							if(!empty($item['selectIcon']) && $item['selectIcon']=='image'){
								$output .='<div class="tp-ps-icon-img tpgb-rel-flex tpgb-trans-easeinout tp-pro-step-icon-img">';
								if(!empty($item['stepImage']['url'])){
									$imgSrc = '';
									$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'thumbnail';
									if( !empty($item['stepImage']) && !empty($item['stepImage']['id']) ){
										$imgSrc = wp_get_attachment_image($item['stepImage']['id'] , $imageSize, false, ['class' => 'tp-icon-img tpgb-trans-easeinout']);
									}else if(!empty($item['stepImage']['url'])){
										$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['stepImage']);
										$imgSrc = '<img class="tp-icon-img tpgb-trans-easeinout" src="'.esc_url($imgUrl).'" alt=""/>';
									}
									$output .= $imgSrc;
								}
								$output .='</div>';
							}
							if(!empty($item['selectIcon']) && $item['selectIcon']=='text'){
								$output .='<div class="tp-ps-icon-img tpgb-rel-flex tpgb-trans-easeinout tp-pro-step-icon-img">';
									$output .='<span class="tp-ps-text tpgb-trans-easeinout">'.wp_kses_post($item['stepText']).'</span>';
								$output .='</div>';
							}
							
							if(!empty($displayCounter)){
								$output .='<div class="tp-ps-dc tpgb-trans-easeinout-after '.esc_attr($counterStyle).'">';
									if($counterStyle=='dc_custom_text'){
										$output .='<span class="ds_custom_text_label">'.wp_kses_post($item['customText']).'</span>';
									}
								$output .='</div>';
							}
							$output .='</div>';
						}
						$output .='<div class="tp-ps-right-content tpgb-trans-easeinout">';
							$output .='<span class="tp-ps-content">';
								if(!empty($item['linkUrl']) && !empty($item['linkUrl']['url'])){
									$linkUrl = (isset($item['linkUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['linkUrl']) : (!empty($item['linkUrl']['url']) ? $item['linkUrl']['url'] : '');
									$target = (!empty($item['linkUrl']['target'])) ? '_blank' : '';
									$nofollow = (!empty($item['linkUrl']['nofollow'])) ? 'nofollow' : '';
									$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['linkUrl']);
									$output .='<a href="'.esc_url($linkUrl).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.'>';
								}
								if(!empty($item['title'])){
									$output .='<h6 class="tp-pro-step-title">'.wp_kses_post($item['title']).'</h6>';
								}
								if(!empty($item['linkUrl']) && !empty($item['linkUrl']['url'])){
									$output .='</a>';
								}
								if(!empty($item['desc'])){
									$output .='<div class="tp-pro-step-desc">'.wp_kses_post($item['desc']).'</div>';
								}
							$output .='</span>';
						$output .='</div>';
					$output .='</div>';
				}
			}
		$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
  	return $output;
}
/**
 * Render for the server-side
 */
function tpgb_tp_process_steps() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'style' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'displayCounter' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'counterStyle' => [
			'type' => 'string',
			'default' => 'number_normal',	
		],
		'vertOnTablet' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'nmlLoutMobile' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'imgSt2Align' => [
			'type' => 'string',
			'default' => 'center',
		],
		'cntntSt2Align' => [
			'type' => 'string',
			'default' => 'center',
		],
		'specialBG' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'dfltActive' => [
			'type' => 'string',
			'default' => 'none',	
		],
		'processSteps' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'title' => [
						'type' => 'string',
						'default' => 'Next Step'
					],
					'desc' => [
						'type' => 'string',
						'default' => 'It`s a step description, Which will be changed with your required text content over here. Add content you want to add over here.'
					],
					'selectIcon' => [
						'type' => 'string',
						'default' => 'icon'
					],
					'fontAwesomeIcon' => [
						'type'=> 'string',
						'default' => 'fas fa-star'
					],
					'stepImage' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'thumbnail',	
					],
					'stepText' => [
						'type' => 'string',
						'default' => 'The Plus'
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=>[]
					],
					'customText' => [
						'type' => 'string',	
						'default' => 'step'
					],
					'iconNormalBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
							'bgType' => 'color',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img',
							],
						],
					],
					'iconHoverBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
							'bgType' => 'color',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img , {{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img',
							],
						],
					],
					'contentHeight' => [
						'type' => 'object',
						'default' => (object) [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap{{TP_REPEAT_ID}} .tp-ps-left-imt:after , {{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap{{TP_REPEAT_ID}} { min-height: {{contentHeight}};}',
							],
						],
						'scopy' => true,
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'title' => 'Step 1',
					'desc' => 'Begin with your self. Improve, Learn, discover and become master with complete self-discipline.',
					'selectIcon' => 'icon',
					'fontAwesomeIcon'=> 'fas fa-chess-king',
					'stepImage' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'imageSize' => 'thumbnail',	
					'stepText' => 'The Plus',
					'linkUrl'=> [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''	
					],
					'customText' => 'step',
				],
				[
					'_key' => '1',
					'title' => 'Step 2',
					'desc' => 'Now, It’s time to share and help others. Spread all your have learned to others and help them get free.',
					'selectIcon' => 'icon',
					'fontAwesomeIcon'=> 'fas fa-bullhorn',
					'stepImage' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'imageSize' => 'thumbnail',	
					'stepText' => 'The Plus',
					'linkUrl'=> [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''	
					],
					'customText' => 'step',
				] 
			]
		],
		'carouselToggle' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'carouselID' => [
			'type' => 'string',
			'default' => '',	
		],
		'carouselEffect' => [
			'type' => 'string',
			'default' => 'con_pro_hover',	
		],
		
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-title',
				],
			],
			'scopy' => true,
		],
		'titleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-title{ color: {{titleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-pro-step-title,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-pro-step-title{ color: {{titleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleTopSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-title{ margin-top: {{titleTopSpace}}; }',
				],
			],
			'scopy' => true,
		],
		
		'descTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-desc',
				],
			],
			'scopy' => true,
		],
		'descNmlColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-desc{ color: {{descNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-pro-step-desc,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-pro-step-desc{ color: {{descHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descTopSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-pro-step-desc{ margin-top: {{descTopSpace}}; }',
				],
			],
			'scopy' => true,
		],
		
		'iconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img { font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconNmlColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img { color: {{iconNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img , {{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img { color: {{iconHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'imgSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-icon-img{ width: {{imgSize}}; height: {{imgSize}}; line-height: {{imgSize}};}',
				],
			],
			'scopy' => true,
		],
		'imgBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-icon-img{border-radius: {{imgBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'textTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-text',
				],
			],
			'scopy' => true,
		],
		'textNmlColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-text{ color: {{textNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'textHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-text,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-text{ color: {{textHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'bgSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img { width: {{bgSize}}; height: {{bgSize}}; }  
					{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-special-bg:after { width: calc({{bgSize}} + 20px); height: calc({{bgSize}} + 20px); } 
					{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-special-bg:before { width: calc({{bgSize}} + 40px); height: calc({{bgSize}} + 40px); } 
					{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt:after ,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tpgb-p-s-wrap .tp-ps-left-imt:after { left: calc(({{bgSize}} /2) - {{sepBSize}}); } 
					{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt { margin-right: calc({{bgSize}} /1.3); } 
					{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-right-content { width: calc(100% - ({{bgSize}} * 2)); }',
				],
			],
			'scopy' => true,
		],
		'contentHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt:after , {{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap{ min-height: {{contentHeight}};}',
				],
			],
			'scopy' => true,
		],
		'normalBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img ',
				],
			],
			'scopy' => true,
		],
		'hoverBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img ,
					{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img',
				],
			],
			'scopy' => true,
		],
		'bgNmlB' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img',
				],
			],
			'scopy' => true,
		],
		'bgHvrB' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img',
				],
			],
			'scopy' => true,
		],
		'nmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img{border-radius: {{nmlBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'hvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img{border-radius: {{hvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'nmlBGShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img',
				],
			],
			'scopy' => true,
		],
		'hvrBGShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img',
				],
			],
			'scopy' => true,
		],
		'transformNmlCSS' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-icon-img .tp-ps-text , {{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-icon-img .tp-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-icon-img i{ transform: {{transformNmlCSS}}; -ms-transform: {{transformNmlCSS}}; -moz-transform: {{transformNmlCSS}}; -webkit-transform: {{transformNmlCSS}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;-webkit-transition: all .3s ease-in-out; -moz-transition: all .3s ease-in-out;-o-transition: all .3s ease-in-out;transition: all .3s ease-in-out; }',
				],
			],
			'scopy' => true,
		],
		'transformHvrCSS' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-icon-img .tp-ps-text , {{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-icon-img .tp-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-icon-img i , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-icon-img .tp-ps-text , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-icon-img .tp-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-icon-img i{ transform: {{transformHvrCSS}}; -ms-transform: {{transformHvrCSS}}; -moz-transform: {{transformHvrCSS}}; -webkit-transform: {{transformHvrCSS}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;-webkit-transition: all .3s ease-in-out; -moz-transition: all .3s ease-in-out;-o-transition: all .3s ease-in-out;transition: all .3s ease-in-out; }',
				],
			],
			'scopy' => true,
		],
		'nmlOverlay' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-icon-img{ box-shadow: {{nmlOverlay}} 0 0 0 100px inset; }',
				],
			],
			'scopy' => true,
		],
		'hvrOverlay' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-icon-img , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-icon-img{ box-shadow: {{hvrOverlay}} 0 0 0 100px inset; }',
				],
			],
			'scopy' => true,
		],
		
		'bType' => [
			'type' => 'string',
			'default' => 'solid',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'bType', 'relation' => '!=', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt:after,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:before,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:after{ border-style: {{bType}}; }',
				],
			],
			'scopy' => true,
		],
		'sepNmlColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'bType', 'relation' => '!=', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt:after,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:before,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:after{ border-color: {{sepNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'sepBSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '1',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'bType', 'relation' => '!=', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tp-ps-left-imt:after,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:before ,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tp-ps-left-imt:after{ border-width: {{sepBSize}}; }',
				],
			],
			'scopy' => true,
		],
		'sepSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ] , ['key' => 'bType', 'relation' => '!=', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tpgb-p-s-wrap .tp-ps-left-imt:before{ width: {{sepSize}} !important;  right : calc( (-{{sepSize}}/2) - 10px ) !important;}',
				],
			],
			'scopy' => true,
		],
		'sepHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap:hover .tp-ps-left-imt:after,{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap.active .tp-ps-left-imt:after,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tpgb-p-s-wrap:hover .tp-ps-left-imt:before,{{PLUS_WRAP}}.tpgb-process-steps.style-2 .tpgb-p-s-wrap.active .tp-ps-left-imt:before{ border-color: {{sepHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'customImg' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'sepOffset' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'bType', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1.tp_ps_sep_img .tp-sep-custom-img-inner,{{PLUS_WRAP}}.tpgb-process-steps.style-2.tp_ps_sep_img .tp-sep-custom-img-inner{ left: {{sepOffset}}; position:relative;}',
				],
			],
			'scopy' => true,
		],
		'imgMaxSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ] , ['key' => 'bType', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1.tp_ps_sep_img .tp-sep-custom-img-inner{ max-height: {{imgMaxSize}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ] , ['key' => 'bType', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-2.tp_ps_sep_img .tp-sep-custom-img-inner{ width: {{imgMaxSize}};max-width: {{imgMaxSize}};height:auto;}',
				],
			],
			'scopy' => true,
		],
		'sepBRadius' => [
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
					'condition' => [(object) ['key' => 'bType', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1.tp_ps_sep_img .tp-sep-custom-img-inner , {{PLUS_WRAP}}.tpgb-process-steps.style-2.tp_ps_sep_img .tp-sep-custom-img-inner{border-radius: {{sepBRadius}};}',
				],
			],
			'scopy' => true,
		],
		
		'counterPadding' => [
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
					'condition' => [(object) ['key' => 'counterStyle', 'relation' => '==', 'value' => 'dc_custom_text' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label{padding: {{counterPadding}};} ',
				],
			],
			'scopy' => true,
		],
		'counterLeftSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc{ margin-left: {{counterLeftSpace}};}',
				],
			],
			'scopy' => true,
		],
		'counterTopSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc{ margin-top: {{counterTopSpace}};}',
				],
			],
			'scopy' => true,
		],
		'counterTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after ,{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterNmlColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label{ color: {{counterNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'counterHvrColor' => [
			'type' => 'string',
			'default' =>'',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label ,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-dc.dc_custom_text .ds_custom_text_label{ color: {{counterHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'counterNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label ,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterNmlB' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterHvrB' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label ,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label{border-radius: {{counterNmlBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'counterHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label ,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}}.tpgb-process-steps .tpgb-p-s-wrap.active .tp-ps-dc.dc_custom_text .ds_custom_text_label{border-radius: {{counterHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'counterNmlShadow' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				],
			],
			'scopy' => true,
		],
		'counterHvrShadow' => [
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
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label ,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-left-imt .tp-ps-dc:after,{{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content{padding: {{contentPadding}};} ',
				],
			],
			'scopy' => true,
		],
		'contentSt2Margin' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content{margin: {{contentSt2Margin}};} ',
				],
			],
			'scopy' => true,
		],
		'contentMargin' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-process-steps.style-1 .tpgb-p-s-wrap .tp-ps-left-imt{ margin-right: {{contentMargin}};}',
				],
			],
			'scopy' => true,
		],
		'contentNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		'contentHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-right-content , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		'contentNmlB' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		'contentHvrB' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-right-content , {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		'contentNmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content{border-radius: {{contentNmlBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'contentHvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-right-content{border-radius: {{contentHvrBRadius}};}  {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-right-content{border-radius: {{contentHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'contentNmlShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		'contentHvrShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover .tp-ps-right-content, {{PLUS_WRAP}} .tpgb-p-s-wrap.active .tp-ps-right-content',
				],
			],
			'scopy' => true,
		],
		
		/* Repeater Background Start*/
		'repPadding' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap {padding: {{repPadding}};} ',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.style-2 .tpgb-p-s-wrap {padding: {{repPadding}};} ',
				],
			],
			'scopy' => true,
		],
		'repMargin' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap {margin: {{repMargin}};} ',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}}.style-2 .tpgb-p-s-wrap {margin: {{repMargin}};} ',
				],
			],
			'scopy' => true,
		],
		'repNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap',
				],
			],
			'scopy' => true,
		],
		'repHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover, {{PLUS_WRAP}} .tpgb-p-s-wrap.active',
				],
			],
			'scopy' => true,
		],
		'repNmlB' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap',
				],
			],
			'scopy' => true,
		],
		'repHvrB' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover, {{PLUS_WRAP}} .tpgb-p-s-wrap.active',
				],
			],
			'scopy' => true,
		],
		'repNmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap {border-radius: {{repNmlBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'repHvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover {border-radius: {{contentHvrBRadius}};} {{PLUS_WRAP}} .tpgb-p-s-wrap.active {border-radius: {{repHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'repNmlShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap',
				],
			],
			'scopy' => true,
		],
		'repHvrShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-p-s-wrap:hover, {{PLUS_WRAP}} .tpgb-p-s-wrap.active',
				],
			],
			'scopy' => true,
		],
		/* Repeater Background End*/
		);
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-process-steps', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_process_steps'
    ) );
}
add_action( 'init', 'tpgb_tp_process_steps' );