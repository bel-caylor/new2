<?php
/* Block : Social Icons
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_social_icons_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$hoverStyle = (!empty($attributes['hoverStyle'])) ? $attributes['hoverStyle'] : 'faded';
	$socialIcon = (!empty($attributes['socialIcon'])) ? $attributes['socialIcon'] : [];
	$Alignment = (!empty($attributes['Alignment'])) ? $attributes['Alignment'] : 'text-center';
	
	$alignattr ='';
	if($Alignment!==''){
		$alignattr .= (!empty($Alignment['md'])) ? ' text-'.esc_attr($Alignment['md']) : ' text-center';
		$alignattr .= (!empty($Alignment['sm'])) ? ' tsocialtext-'.esc_attr($Alignment['sm']) : '';
		$alignattr .= (!empty($Alignment['xs'])) ? ' msocialtext-'.esc_attr($Alignment['xs']) : '';
	}
	
	$social_animation ='';
	if($style=='style-14' || $style=='style-15'){
		if($hoverStyle == 'faded'){
			$social_animation ='social-faded';
		}else if($hoverStyle == 'chaffal'){
			$social_animation ='social-chaffal';
		}
	}
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$i =0;
	$output = ''; 
    $output .= '<div class="tpgb-social-icons '.esc_attr($style).' '.esc_attr($alignattr).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		if(!empty($socialIcon)){
		$output .='<div class="tpgb-social-list '.esc_attr($social_animation).'">';
			
				foreach ( $socialIcon as $index => $network ) :
					//Tooltip
					$i++;
						$itemtooltip =$tooltip_trigger=$tooltipdata='';
						$contentItem =[];
						$uniqid=uniqid("tooltip");
						if(!empty($network['itemTooltip'])){
							$itemtooltip .= ' data-tippy=""';
							$itemtooltip .= ' data-tippy-interactive="'.(!empty($attributes['tipInteractive']) ? 'true' : 'false').'"';
							$itemtooltip .= ' data-tippy-placement="'.(!empty($attributes['tipPlacement']) ? $attributes['tipPlacement'] : 'top').'"';
							$itemtooltip .= ' data-tippy-theme="'.$attributes['tipTheme'].'"';
							$itemtooltip .= ' data-tippy-arrow="'.(!empty($attributes['tipArrow']) ? 'true' : 'false').'"';
							
							$itemtooltip .= ' data-tippy-animation="'.(!empty($attributes['tipAnimation']) ? $attributes['tipAnimation'] : 'fade').'"';
							$itemtooltip .= ' data-tippy-offset="['.(!empty($attributes['tipOffset']) ? (int)$attributes['tipOffset'] : 0 ).','.(!empty($attributes['tipDistance']) ? (int)$attributes['tipDistance'] : 0 ).']"';
							$itemtooltip .= ' data-tippy-duration="['.(!empty($attributes['tipDurationIn']) ? (int)$attributes['tipDurationIn'] : '1').','.(!empty($attributes['tipDurationOut']) ? (int)$attributes['tipDurationOut'] : '1').']"';

							if(class_exists('Tpgbp_Pro_Blocks_Helper')){
								$contentItem['content'] = (!empty($network['tooltipText'])  ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($network['tooltipText']) : '');
							}else{
								$contentItem['content'] = (!empty($network['tooltipText'])  ? $network['tooltipText'] : '');
							}
							
							$contentItem['trigger'] = (!empty($attributes['tipTriggers'])  ? $attributes['tipTriggers'] : 'mouseenter');
							$contentItem['MaxWidth'] = (!empty($attributes['tipMaxWidth']) ? (int)$attributes['tipMaxWidth'] : 'none');
							$contentItem = htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8');
							$tooltipdata = 'data-tooltip-opt= \'' .$contentItem. '\'';
						}
						
						
					$output .= '<div id="'.esc_attr($uniqid).'" class=" social-icon-tooltip tp-repeater-item-'.esc_attr($network['_key']).' '.esc_attr($style).' '.$itemtooltip.'" '.$tooltipdata.' >';
						if(!empty($network['linkUrl']['url']) && !empty($network['socialNtwk'])){
							$socialUrl = (class_exists('Tpgbp_Pro_Blocks_Helper') && isset($network['linkUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($network['linkUrl']) : (!empty($network['linkUrl']['url']) ? $network['linkUrl']['url'] : '');
							$target = (!empty($network['linkUrl']['target'])) ? '_blank' : '';
							$nofollow = (!empty($network['linkUrl']['nofollow'])) ? 'rel="nofollow"' : '';
							$link_attr = Tp_Blocks_Helper::add_link_attributes($network['linkUrl']);
							$output .= '<div class="tpgb-social-loop-inner '.($style=='style-14' ? 'tpgb-rel-flex' : '').'">';
								$output .= '<a class="tpgb-icon-link '.(($style=='style-14' || $style=='style-15') ? 'tpgb-rel-flex' : '').'" href="'.esc_url($socialUrl).'" aria-label="'.esc_attr($network['title']).'" target="'.esc_attr($target).'" '.$nofollow.' '.$link_attr.'>';
									if($network['socialNtwk']=='custom' && $network['customType']=='icon' && !empty($network['customIcons'])) {
										$output .= '<span class="tpgb-social-icn '.($style=='style-12' ? 'tpgb-abs-flex' : '').'">';
											$output .= '<i class="'.esc_attr($network['customIcons']).'"></i>';
										$output .= '</span>';
									}else if($network['socialNtwk']=='custom' && $network['customType']=='image' && !empty($network['imgField']) && !empty($network['imgField']['url'])) {
										$imgSrc='';
										if(!empty($network['imgField']) && !empty($network['imgField']['id'])){
											$imgSrc = wp_get_attachment_image($network['imgField']['id'] , 'full');
										}else if(!empty($network['imgField']['url'])){
											if(class_exists('Tpgbp_Pro_Blocks_Helper')){
												$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($network['imgField']);
											}else{
												$imgUrl = $network['imgField']['url'];
											}
											$imgSrc = '<img src="'.esc_url($imgUrl).'" alt="'.esc_attr__('Custom icon','tpgb').'" />';
										}
										$output .= '<span class="tpgb-social-icn social-img '.($style=='style-7' ? 'tpgb-rel-flex' : '').' '.($style=='style-12' ? 'tpgb-abs-flex' : '').'">';
											$output .= $imgSrc;
										$output .= '</span>';
									}else if($network['socialNtwk']!='custom'){
										$output .= '<span class="tpgb-social-icn '.($style=='style-12' ? 'tpgb-abs-flex' : '').'">';
											$output .= '<i class="'.esc_attr($network['socialNtwk']).'"></i>';
										$output .= '</span>';
									}
										if($style=='style-6'){
											$output .= '<i class="social-hover-style"></i>';
										}
									if(!empty($network['title']) && $style=='style-1' || $style=='style-2' || $style=='style-4' || $style=='style-10' || $style=='style-12' || $style=='style-14' || $style=='style-15'){
										$output .= '<span class="tpgb-social-title '.(($style=='style-10' || $style=='style-12') ? 'tpgb-abs-flex' : '').'" data-lang="en">'.wp_kses_post($network['title']).'</span>';
									}
									if($style=='style-9'){
										$output .= '<span class="tpgb-line-blink line-top-left "></span>';
										$output .= '<span class="tpgb-line-blink line-top-center "></span>';
										$output .= '<span class="tpgb-line-blink line-top-right "></span>';
										$output .= '<span class="tpgb-line-blink line-bottom-left "></span>';
										$output .= '<span class="tpgb-line-blink line-bottom-center "></span>';
										$output .= '<span class="tpgb-line-blink line-bottom-right "></span>';
									}
								$output .= '</a>';
							$output .= '</div>';
						}
					$output .= '</div>';
					
					
						endforeach;
				$output .='</div>';
			}
			
		
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}
/**
 * Render for the server-side
 */
function tpgb_social_icons() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'style' => [
			'type' => 'string',
			'default' => 'style-1',
		],
		'hoverStyle' => [
			'type' => 'string',
			'default' => 'faded',
		],
		'socialIcon' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'socialNtwk' => [
						'type' => 'string',
						'default' => 'fab fa-facebook'
					],
					'customType' => [
						'type' => 'string',
						'default' => 'icon',
					],
					'customIcons' => [
						'type'=> 'string',
						'default'=> 'fab fa-whatsapp',
					],
					'imgField' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=>[
							'url' => '#',	
							'target' => '',	
							'nofollow' => ''	
						]
					],
					'title' => [
						'type' => 'string',
						'default' => 'Network'
					],
					'iconNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}:not(.style-12) .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-12 .tpgb-icon-link .tpgb-social-icn{ color: {{iconNmlColor}}; }',
							],
						],
					],
					'iconHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}:not(.style-12):not(.style-4):hover .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-12 .tpgb-icon-link .tpgb-social-title , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-4 .tpgb-icon-link .tpgb-social-icn , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-5:hover .tpgb-icon-link .tpgb-social-icn , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-14:hover .tpgb-icon-link .tpgb-social-title , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list.social-faded {{TP_REPEAT_ID}}.style-15 .tpgb-icon-link .tpgb-social-title{ color: {{iconHvrColor}}; }',
							], 
						],
					],
					'nmlBG' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}:not(.style-3):not(.style-9):not(.style-11):not(.style-12) .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-12 .tpgb-icon-link .tpgb-social-icn{ background: {{nmlBG}}; }  {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-3 { background: {{nmlBG}}; border-color: {{nmlBG}}; background-clip:content-box; }  {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-9:hover .tpgb-icon-link .tpgb-social-title:before{ background: {{nmlBG}}; }  {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-11 .tpgb-icon-link:before{ -webkit-box-shadow: inset 0 0 0 70px {{nmlBG}}; --moz-box-shadow: inset 0 0 0 70px {{nmlBG}}; box-shadow: inset 0 0 0 70px {{nmlBG}}; }',
							], 
						],
					],
					'hvrBG' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}:not(.style-3):not(.style-9):not(.style-11):not(.style-12):hover .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-6 .tpgb-icon-link .social-hover-style , {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-12:hover .tpgb-icon-link .tpgb-social-title , {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-9:hover .tpgb-line-blink::before { background: {{hvrBG}}; }  {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-3:hover { background: {{hvrBG}}; border-color: {{hvrBG}}; background-clip:content-box}  {{PLUS_WRAP}}.tpgb-social-icons {{TP_REPEAT_ID}}.style-11:hover .tpgb-icon-link:before { -webkit-box-shadow: inset 0 0 0 4px {{hvrBG}}; -moz-box-shadow: inset 0 0 0 4px {{hvrBG}}; box-shadow: inset 0 0 0 4px {{hvrBG}};}',
							], 
						],
					],
					'nmlBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}:not(.style-11):not(.style-12):not(.style-13) .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-12 .tpgb-icon-link .tpgb-social-icn , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-13 .tpgb-icon-link:after , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-13 .tpgb-icon-link:before{ border-color: {{nmlBColor}}; }',
							],
						],
					],
					'hvrBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}:not(.style-11):not(.style-12):not(.style-13):hover .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-12:hover .tpgb-icon-link .tpgb-social-title , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-13:hover .tpgb-icon-link:after , {{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list {{TP_REPEAT_ID}}.style-13:hover .tpgb-icon-link:before{ border-color: {{hvrBColor}}; }',
							], 
						], 
					],
					'tooltipTypo' => [
						'default' => ['openTypography' => 0 ],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content',
							],
						],
					],
					'tooltipColor' => [
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content{color:{{tooltipColor}};}',
							],
						],
					],
					'itemTooltip' => [
						'type' => 'boolean',
						'default' => false,
					],
					'tooltipText' => [
						'type' => 'string',
						'default' => '',
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'socialNtwk' => 'fab fa-facebook-f',
					'title' => 'Facebook',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'nmlBG' => '#3a579a',
					'nmlBColor' => '#3a579a',
					'customType' => 'icon',
					'customIcons' => 'fab fa-whatsapp',
					'imgField' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'tooltipTypo' => ['openTypography' => 0 ],
					'tooltipText' => ''
				],
				[
					'_key' => '1',
					'socialNtwk' => 'fab fa-youtube',
					'title' => 'Youtube',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'nmlBG' => '#FF0000',
					'nmlBColor' => '#FF0000',
					'customType' => 'icon',
					'customIcons' => 'fab fa-whatsapp',
					'imgField' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'tooltipTypo' => ['openTypography' => 0 ],
					'tooltipText' => ''
				],
				[
					'_key' => '2',
					'socialNtwk' => 'fab fa-twitter',
					'title' => 'Twitter',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'nmlBG' => '#0aaded',
					'nmlBColor' => '#0aaded',
					'customType' => 'icon',
					'customIcons' => 'fab fa-whatsapp',
					'imgField' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'tooltipTypo' => ['openTypography' => 0 ],
					'tooltipText' => ''
				],
			],
		],
		'Alignment' => [
			'type' => 'object',
			'default' => ['md' => 'center'],
			'scopy' => true,
		],
		'iconPadd' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.style-1 .tpgb-social-list > div .tpgb-icon-link{padding: {{iconPadd}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.style-2 .tpgb-social-list > div .tpgb-icon-link{padding: {{iconPadd}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-14' ] ],
					'selector' => '{{PLUS_WRAP}}.style-14 .tpgb-social-list > div .tpgb-icon-link{padding: {{iconPadd}};}',
				],
			],
			'scopy' => true,
		],
		'iconGap' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list > div{margin: {{iconGap}};}',
				],
			],
			'scopy' => true,
		],
		'iconHgt' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-15' ] ],
					'selector' => ' {{PLUS_WRAP}}.style-15 .tpgb-social-list > div.style-15 .tpgb-icon-link{height: {{iconHgt}};}',
				],
			],
			'scopy' => true,
		],
		'iconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .style-1 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-2 .tpgb-icon-link .tpgb-social-icn, {{PLUS_WRAP}} .tpgb-social-list .style-3 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-4 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-5 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-6 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-7 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-8 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-9 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-10 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-11 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-12 .tpgb-icon-link .tpgb-social-icn, {{PLUS_WRAP}} .tpgb-social-list .style-13 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-14 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-15 .tpgb-icon-link , {{PLUS_WRAP}} .tpgb-social-list .style-16 .tpgb-icon-link{ font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'imgWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => ' {{PLUS_WRAP}} .tpgb-social-list .tpgb-social-icn.social-img img{ max-width: {{imgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'iconWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16']],
					'selector' => ' {{PLUS_WRAP}}.style-16 .tpgb-social-list .style-16 .tpgb-icon-link{ width: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'iconHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16']],
					'selector' => ' {{PLUS_WRAP}}.style-16 .tpgb-social-list .style-16 .tpgb-icon-link{ height: {{iconHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'borderStyle' => [
			'type' => 'string',
			'default' => 'solid',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16' ] ],
					'selector' => ' {{PLUS_WRAP}}.tpgb-social-icons.style-16 .tpgb-social-list .tpgb-icon-link{border-style: {{borderStyle}};}',
				],
			],
			'scopy' => true,
		],
		'borderWidth' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16' ] , ['key' => 'borderStyle', 'relation' => '!=', 'value' => 'none' ]],
					'selector' => ' {{PLUS_WRAP}}.tpgb-social-icons.style-16 .tpgb-social-list .tpgb-icon-link{border-width: {{borderWidth}};}',
				],
			],
			'scopy' => true,
		],
		'iconBRadius' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-4','style-5','style-6','style-7','style-10','style-16'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-social-list .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons.style-4 .tpgb-social-list .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons.style-5 .tpgb-social-list .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons.style-10 .tpgb-social-list .tpgb-icon-link , {{PLUS_WRAP}}.tpgb-social-icons.style-16 .tpgb-social-list .tpgb-icon-link{border-radius: {{iconBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-4','style-10','style-12','style-14','style-15'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tpgb-icon-link .tpgb-social-title',
				],
			],
			'scopy' => true,
		],
		'nmlIcnShadow' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-7' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons.style-7 .tpgb-social-list .tpgb-icon-link',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons.style-16 .tpgb-social-list .tpgb-icon-link',
				],
			],
			'scopy' => true,
		],
		'hvrIcnShadow' => [
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
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-7' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons.style-7 .tpgb-social-list > div:hover .tpgb-icon-link',
				],
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-16' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons.style-16 .tpgb-social-list > div:hover .tpgb-icon-link',
				],
			],
			'scopy' => true,
		],
		
		'tipInteractive' => [
            'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'tipPlacement' => [
			'type' => 'string',
			'default' => 'top',
			'scopy' => true,
		],
		'tipTheme' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipMaxWidth' => [
			'type' => 'string',
			'default' => '100',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-social-icons .tippy-box{width : {{tipMaxWidth}}px; max-width : {{tipMaxWidth}}px; }  ',
				],
			],
			'scopy' => true,
		],
		'tipOffset' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipDistance' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipArrow' => [
			'type' => 'boolean',
			'default' => true,
			'scopy' => true,
		],
		'tipTriggers' => [
			'type' => 'string',
			'default' => 'mouseenter',
			'scopy' => true,
		],
		'tipAnimation' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipDurationIn' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipDurationOut' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tipArrowColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'tipArrow', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-arrow{color: {{tipArrowColor}};}',
				],
			],
			'scopy' => true,
		],
		'tipPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-box{padding: {{tipPadding}};}',
				],
			],
			'scopy' => true,
		],
		'tipBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-box',
				],
			],
			'scopy' => true,
		],
		'tipBorderRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-box{border-radius: {{tipBorderRadius}};}',
				],
			],
			'scopy' => true,
		],
		'tipBg' => [
			'type' => 'object',
			'default' => (object) [
				'bgType' => 'color',
				'bgGradient' => (object) [],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-box',
				],
			],
			'scopy' => true,
		],
		'tipBoxShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'blur' => 8,
				'color' => "rgba(0,0,0,0.40)",
				'horizontal' => 0,
				'inset' => 0,
				'spread' => 0,
				'vertical' => 4
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tippy-box',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-social-icons', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_social_icons_render_callback'
    ) );
}
add_action( 'init', 'tpgb_social_icons' );