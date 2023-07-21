<?php
/* Block : Social Sharing
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_social_sharing( $attributes, $content) {
	$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
	$socialSharing = (!empty($attributes['socialSharing'])) ? $attributes['socialSharing'] : [];
	$sociallayout = (!empty($attributes['sociallayout'])) ? $attributes['sociallayout'] : 'horizontal';
	$alignment = (!empty($attributes['alignment'])) ? $attributes['alignment'] : 'left';
	$contentAlign = (!empty($attributes['contentAlign'])) ? $attributes['contentAlign'] : 'text-left';
	$column = (!empty($attributes['column'])) ? $attributes['column'] : 'auto';
	$hrzntlStyle = (!empty($attributes['hrzntlStyle'])) ? $attributes['hrzntlStyle'] : 'style-1';
	$vrtclStyle = (!empty($attributes['vrtclStyle'])) ? $attributes['vrtclStyle'] : 'style-1';
	$toggleStyle = (!empty($attributes['toggleStyle'])) ? $attributes['toggleStyle'] : 'style-1';
	$displayCounter = (!empty($attributes['displayCounter'])) ? $attributes['displayCounter'] : false;
	$viewtype = (!empty($attributes['viewtype'])) ? $attributes['viewtype'] : 'iconText';
	$hDirection = (!empty($attributes['hDirection'])) ? $attributes['hDirection'] : 'top';
	$shareNumber = (!empty($attributes['shareNumber'])) ? $attributes['shareNumber'] : '';
	$shareLabel = (!empty($attributes['shareLabel'])) ? $attributes['shareLabel'] : '';
	$iconStore = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : '';
	$toggleWidth = (!empty($attributes['toggleWidth'])) ? $attributes['toggleWidth'] : 40;
	$iconGap = (!empty($attributes['iconGap'])) ? $attributes['iconGap'] : 0;
	$tglBtnText = (!empty($attributes['tglBtnText'])) ? $attributes['tglBtnText'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$p = 1;
	$selectStyle = '';
	$direction = '';
	
	if($sociallayout=='horizontal'){
		$selectStyle = $hrzntlStyle ;
	}else if($sociallayout=='vertical'){
		$selectStyle = $vrtclStyle ;
	}else if($sociallayout=='toggle'){
		$selectStyle = $toggleStyle ;
	}
	$columnAuto='';
	if($sociallayout=='horizontal' && (($column['md']!='auto' && $column['md']!='')) || ($column['sm']!='auto' && $column['sm']!='') || ($column['xs']!='auto' && $column['xs']!='')){
		$columnAuto = 'full-column' ;
	}
		
	if($sociallayout=='toggle' && $toggleStyle=='style-2'){
		$direction = ( $hDirection == 'left' ? 'right' : ($hDirection == 'right' ? 'left' : ( $hDirection == 'top' ? 'bottom' : ( $hDirection == 'bottom' ? 'top' : ''))));
	}
	
	$getCounter = '' ;
	$getCounter .= '<div class="totalcount ">';
		$getCounter .= '<span class="totalcount-item">';
			$getCounter .= '<span class="total-count-number">'.wp_kses_post($shareNumber).'</span>';
			$getCounter .= '<span class="total-number-label">'.wp_kses_post($shareLabel).'</span>';
		$getCounter .= '</span>';
	$getCounter .= '</div>';
	
	
	
	$post_id = get_the_ID();
	$get_link = get_the_permalink($post_id);
	$get_title = get_the_title($post_id);
	$media_url = get_the_post_thumbnail_url($post_id, 'full');
	$description = wp_strip_all_tags( get_the_excerpt(), true );
	$ShareLink = [
		'fab fa-amazon' => "https://www.amazon.com/gp/wishlist/static-add?u=".$get_link,
		'fab fa-digg' => "https://digg.com/submit?url=".$get_link,
		'fab fa-delicious' => "https://del.icio.us/save?url=".$get_link."&title=".$get_title,
		'far fa-envelope' => "mailto:?subject=".$get_title."&body=".$description."\n".$get_link,
		'fab fa-facebook-f' => "https://www.facebook.com/sharer.php?u=".$get_link,
		'fab fa-facebook-messenger' => "fb-messenger://share/?link=".$get_link,
		'fab fa-get-pocket' => "https://getpocket.com/save?url=".$get_link."&title=".$get_title,
		'fab fa-linkedin-in' => "https://www.linkedin.com/shareArticle?mini=true&url=".$get_link."&title=".$get_title,
		'fab fa-odnoklassniki' => "https://connect.ok.ru/offer?url=".$get_link."&title=".$get_title."&imageUrl=".$media_url,
		'fab fa-pinterest-p' => "https://www.pinterest.com/pin/create/button/?url=".$get_link."&media=".$media_url,
		'fab fa-reddit' => "https://reddit.com/submit?url=".$get_link."&title=".$get_title,
		'fab fa-skype' => "https://web.skype.com/share?url=".$get_link,
		'fab fa-snapchat-ghost' => "https://www.snapchat.com/scan?attachmentUrl=".$get_link,
		'fab fa-stumbleupon' => "https://www.stumbleupon.com/submit?url=".$get_link."&title=".$get_title,
		'fab fa-telegram-plane' => "https://telegram.me/share/url?url=".$get_link."&text=".$get_title,
		'fab fa-tumblr' => "https://tumblr.com/share/link?url=".$get_link,
		'fab fa-twitter' => "https://twitter.com/intent/tweet?text=".$get_title.' '.$get_link,
		'fab fa-viber' => "viber://forward?text=".$get_title." ".$get_link,
		'fab fa-vk' => "https://vkontakte.ru/share.php?url=".$get_link."&title=".$get_title."&description=".$description."&image=".$media_url,
		'fab fa-weibo' => "https://service.weibo.com/share/share.php?url=".$get_link."&title=".$get_title."&pic=".$media_url,
		'fab fa-whatsapp' => "https://api.whatsapp.com/send?text=*".$get_title."*\n".$description."\n".$get_link,
		'fab fa-xing' => "https://www.xing.com/app/user?op=share&url=".$get_link,
	];
	
	$loopStyle = '';
	$output = '';
    	$output .= '<div class="tpgb-social-sharing sharing-'.esc_attr($sociallayout).' sharing-'.esc_attr($selectStyle).' tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' ">';
			if($sociallayout=='toggle' && $toggleStyle=='style-1'){
				$output .= '<div class="toggle-share tpgb-rel-flex">';
					$output .= '<div class="toggle-icon tpgb-rel-flex">';
					  $output .= '<span class="toggle-label">'.wp_kses_post($tglBtnText).'</span>';
					  $output .= '<div class="toggle-btn"><i class="'.esc_attr($iconStore).'"></i></div>';
					$output .= '</div>';
				$output .= '</div>';
			}
			$output .= '<div class="tpgb-social-list '.esc_attr($columnAuto).' '.esc_attr($hDirection).' ">';
			if(!empty($displayCounter) && ($sociallayout=='horizontal' || $sociallayout=='vertical')){
				$output .= $getCounter;
			}
			if($sociallayout=='toggle' && $toggleStyle=='style-2'){
				$output .= '<div class="tpgb-main-menu">';
					$output .= '<a class="tpgb-share-btn tpgb-rel-flex tpgb-rel-flex">';
						$output .= '<i class="'.esc_attr($iconStore).'"></i>';
					$output .= '</a>';
				$output .= '</div>';
			}
			
			if(!empty($socialSharing)){
				$leftValue = 0;
				
				foreach ( $socialSharing as $index => $network ) {
					$p++;
					
					$getIcon ='<span class="social-btn-icon tpgb-rel-flex">';
						$getIcon .='<i class="'.esc_attr($network['socialNtwk']).'"></i>';
					$getIcon .='</span>';
					
					$getTitle ='<span class="social-btn-title tpgb-trans-easeinout">'.wp_kses_post($network['title']).'</span>';
					
					$getCountNumber ='<div class="social-count-number">';
						if(isset($network['countNumber'])){
							$getCountNumber .='<span class="social-count tpgb-trans-easeinout">'.wp_kses_post($network['countNumber']).'</span>';
						}
						if(!empty($network['countLabel'])){
							$getCountNumber .='<span class="count-label">'.wp_kses_post($network['countLabel']).'</span>';
						}
					$getCountNumber .='</div>';
					
					$leftValue = $leftValue+$toggleWidth+$iconGap ;
					
					$output .= '<div class="tpgb-social-menu  tp-repeater-item-'.esc_attr($network['_key']).' ">';
						
						$getCustomLink = $link_attr ='';
						if(!empty($network['customURL']) && !empty($network['customLink']['url'])){
							$getCustomLink = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($network['customLink']);
							$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($network['customLink']);
						}else{
							$iconname= $network['socialNtwk'];
							$getCustomLink= (isset($ShareLink[$iconname])) ? $ShareLink[$iconname] : '#';
						}
						$target = (!empty($network['customLink']['target'])) ? '_blank' : ' ';
						$nofollow = (!empty($network['customLink']['nofollow'])) ? 'nofollow' : ' ';
						$ariaLabel = (!empty($network['ariaLabel'])) ? esc_attr($network['ariaLabel']) : ((!empty($network['title'])) ? esc_attr($network['title']) : esc_attr__("Social", 'tpgbp'));
						$output .= '<a href="'.esc_url($getCustomLink).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" class="share-btn tpgb-rel-flex tpgb-trans-easeinout '.esc_attr($contentAlign).'" '.$link_attr.' aria-label="'.$ariaLabel.'">';
							if(($sociallayout=='horizontal' && $hrzntlStyle!='style-4' && $hrzntlStyle!='style-5') || $sociallayout=='vertical'){
								if($viewtype!='text' && $viewtype!='textCount'){
									$output .= $getIcon;
								}
								if(!empty($network['title']) && $viewtype!='icon' && $viewtype!='iconCount'){
									$output .= $getTitle;
								}
							if($viewtype=='iconCount' || $viewtype=='textCount' || $viewtype=='iconTextCount'){
								$output .= $getCountNumber;
							}
							}
							if($sociallayout=='horizontal' && $hrzntlStyle=='style-4'){
								$output .= $getIcon;
								$output .=$getCountNumber;
							}
							if($sociallayout=='horizontal' && $hrzntlStyle=='style-5'){
								$output .='<div class="custom-style-5">';
									$output .= $getIcon;
									$output .=$getCountNumber;
								$output .='</div>';
								$output .= $getTitle;
							}
							if($sociallayout=='toggle'){
								$output .=$getIcon;
							}
						$output .= '</a>';
					$output .= '</div>';
					if($sociallayout=='toggle' && $toggleStyle=='style-2'){
						$loopStyle .= '.tpgb-block-'.esc_attr($block_id).'.sharing-toggle.sharing-style-2 .tpgb-social-list.'.esc_attr($hDirection).'.active .tpgb-social-menu:nth-child('.esc_attr($p).'){ '.esc_attr($direction).': '.esc_attr($leftValue).'px;}';
					}
				}
			}
			$output .= '</div>';
		$output .= '</div>';
	
	$dywidth = (array)$attributes['bgWidth'];
	if(($sociallayout=='horizontal' && ($column['md']==='' || $column['md']=='auto')) || $sociallayout=='vertical'){
		$mdWidth = (!empty($dywidth) && !empty($dywidth['md'])) ? $dywidth['md'].$dywidth['unit'] : '100%';
		$loopStyle .= '.tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: '.esc_attr($mdWidth).'; }';
	}
	else if(($sociallayout=='horizontal' &&  $column['md']!='' && $column['md']!='auto') || $sociallayout=='vertical'){
		$loopStyle .= '.tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: 100%; }';
	}
	if(($sociallayout=='horizontal' &&  ($column['sm']==='' || $column['sm']=='auto')) || $sociallayout=='vertical'){
		$smWidth = (!empty($dywidth) && !empty($dywidth['sm'])) ? $dywidth['sm'].$dywidth['unit'] : '100%';
		$loopStyle .= '@media (min-width:768px) and (max-width:1024px) { .tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: '.esc_attr($smWidth).'; }}';
	}else if(($sociallayout=='horizontal' &&  $column['sm']!='' && $column['sm']!='auto') || $sociallayout=='vertical'){
		$loopStyle .= '@media (min-width:768px) and (max-width:1024px) { .tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: 100%; }}';
	}
	if(($sociallayout=='horizontal' &&  ($column['xs']==='' || $column['xs']=='auto')) || $sociallayout=='vertical'){
		$xsWidth = (!empty($dywidth) && !empty($dywidth['xs'])) ? $dywidth['xs'].$dywidth['unit'] : '100%';
		$loopStyle .= '@media (max-width:767px){ .tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: '.esc_attr($xsWidth).'; }}';
	}else if(($sociallayout=='horizontal' &&  $column['xs']!='' && $column['xs']!='auto') || $sociallayout=='vertical'){
		$loopStyle .= '@media (max-width:767px){ .tpgb-block-'.esc_attr($block_id).' .tpgb-social-list .tpgb-social-menu .share-btn{ width: 100%; }}';
	}
	
	if(!empty($loopStyle)){
		$output .= '<style>'.$loopStyle.'</style>';
	}

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

  	return $output;
}
/**
 * Render for the server-side
 */
function tpgb_tp_social_sharing() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'sociallayout' => [
			'type' => 'string',
			'default' => 'horizontal',	
		],
		'hrzntlStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'vrtclStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'toggleStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'hDirection' => [
			'type' => 'string',
			'default' => 'top',	
		],
		'viewtype' => [
			'type' => 'string',
			'default' => 'iconText',	
		],
		'column' => [
			'type' => 'object',
			'default' => [ 'md' => 'auto','sm' => 'auto','xs' => 'auto' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'column', 'relation' => '!=', 'value' => 'auto' ] , ['key' => 'sociallayout', 'relation' => '==', 'value' => 'horizontal']],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tpgb-social-menu , {{PLUS_WRAP}} .tpgb-social-list .totalcount{width: calc(100%/{{column}});} ',
				],
			],
			'scopy' => true,
		],
		'alignment' => [
			'type' => 'object',
			'default' => 'left',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-social-sharing { text-align: {{alignment}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'horizontal'] , ['key' => 'hrzntlStyle', 'relation' => '==', 'value' => 'style-5'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'left']],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list { justify-content: flex-start; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'horizontal'] , ['key' => 'hrzntlStyle', 'relation' => '==', 'value' => 'style-5'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'center']],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list { justify-content: center; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'horizontal'] , ['key' => 'hrzntlStyle', 'relation' => '==', 'value' => 'style-5'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'right']],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list { justify-content: flex-end; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle'] , ['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'left']],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share { margin: 0 auto; margin-left: 0; } {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .tpgb-social-list { justify-content: flex-start}',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle'] , ['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'center']],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share { margin: 0 auto; } {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .tpgb-social-list { justify-content: center}',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle'] , ['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1'] , ['key' => 'alignment', 'relation' => '==', 'value' => 'right']],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share { margin: 0 auto; margin-right: 0; } {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .tpgb-social-list { justify-content: flex-end}',
				],
			],
			'scopy' => true,
		],
		'contentAlign' => [
			'type' => 'string',
			'default' => 'text-left',
			'scopy' => true,
		],
		'displayCounter' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'shareNumber' => [
			'type' => 'string',
			'default' => '9.9 K',	
		],
		'shareLabel' => [
			'type' => 'string',
			'default' => 'Share',
		],
		'socialSharing' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'socialNtwk' => [
						'type' => 'string',
						'default' => 'fab fa-facebook-f'
					],
					'title' => [
						'type' => 'string',
						'default' => 'Network'
					],
					'countNumber' => [
						'type' => 'string',
						'default' => '7'
					],
					'countLabel' => [
						'type' => 'string',
						'default' => ''
					],
					'customURL' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'customLink' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',	
							'target' => '',	
							'nofollow' => ''
						],
					],
					'ariaLabel' => [
						'type' => 'string',
						'default' => ''
					],
					'titleNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .social-btn-title{ color: {{titleNmlColor}}; }',
							],
						],
					],
					'titleHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .social-btn-title{ color: {{titleHvrColor}}; }',
							],
						],
					],
					'countNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .social-count{ color: {{countNmlColor}}; }',
							],
						],
					],
					'countHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .social-count{ color: {{countHvrColor}}; }',
							],
						],
					],
					'countLblNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .count-label{ color: {{countLblNmlColor}}; }',
							],
							],
					],
					'countLblHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .count-label{ color: {{countLblHvrColor}}; }',
							],
						],
					],
					'iconNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .social-btn-icon{ color: {{iconNmlColor}}; }',
							],
						],
					],
					'iconHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .social-btn-icon{ color: {{iconHvrColor}}; }',
							],
						],
					],
					'iconNBdrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn .social-btn-icon{ border-color: {{iconNBdrColor}}; }',
							],
						],
					],
					'iconHBdrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .social-btn-icon{ border-color: {{iconHBdrColor}}; }',
							],
						],
					],
					'iconNmlBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .social-btn-icon',
							],
						],
					],
					'iconHvrBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover .social-btn-icon',
							],
						],
					],
					'nmlBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn{ border-color: {{nmlBColor}}; }',
							],
						],
					],
					'hvrBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover{ border-color: {{hvrBColor}}; }',
							],
						],
					],
					'normalBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn',
							],
						],
					],
					'hoverBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-social-list {{TP_REPEAT_ID}} .share-btn:hover',
							],
						],
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'socialNtwk' => 'fab fa-facebook-f',
					'title' => 'Facebook',
					'countNumber' => '109',
					'countLabel' => 'Share',
					'customLink'=> [
						'url' => '',
						'target' => '',
						'nofollow' => ''
					],
					'normalBG' => ['openBg' => 1,'bgDefaultColor' => "#3a579a",],
				],
				[
					'_key' => '1',
					'socialNtwk' => 'fab fa-twitter',
					'title' => 'Twitter',
					'countNumber' => '23',
					'countLabel' => 'Share',
					'customLink'=> [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''	
					],
					'normalBG' => ['openBg' => 1,'bgDefaultColor' => "#0aaded",],
				],
				[
					'_key' => '2',
					'socialNtwk' => 'fab fa-pinterest-p',
					'title' => 'Pinterest',
					'countNumber' => '0',
					'countLabel' => 'Share',
					'customLink'=> [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''	
					],
					'normalBG' => ['openBg' => 1,'bgDefaultColor' => "#cd1c1f",],
				],
				[
					'_key' => '3',
					'socialNtwk' => 'fab fa-linkedin-in',
					'title' => 'LinkedIn',
					'countNumber' => '2',
					'countLabel' => 'Share',
					'customLink'=> [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''	
					],
					'normalBG' => ['openBg' => 1,'bgDefaultColor' => "#127bb6",],
				],
			],
		],
		
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fa fa-share-alt',
		],
		'tglBtnText' => [
			'type' => 'string',
			'default' => 'Share',	
		],
		
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tpgb-social-menu .social-btn-title',
				],
			],
			'scopy' => true,
		],
		'titleSpace' => [
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
					'selector' => '{{PLUS_WRAP}}.sharing-horizontal .tpgb-social-list .social-btn-title , {{PLUS_WRAP}}.sharing-vertical .tpgb-social-list .social-btn-title{margin: {{titleSpace}};}',
				],
			],
			'scopy' => true,
		],
		'countNumTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.sharing-horizontal .tpgb-social-list .social-count , {{PLUS_WRAP}}.sharing-vertical .tpgb-social-list .social-count',
				],
			],
			'scopy' => true,
		],
		'countNumSpace' => [
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
					'selector' => '{{PLUS_WRAP}}.sharing-horizontal .tpgb-social-list .social-count , {{PLUS_WRAP}}.sharing-vertical .tpgb-social-list .social-count{margin: {{countNumSpace}};}',
				],
			],
			'scopy' => true,
		],
		'countLblTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.sharing-horizontal .tpgb-social-list .count-label , {{PLUS_WRAP}}.sharing-vertical .tpgb-social-list .count-label',
				],
			],
			'scopy' => true,
		],
		'countLblSpace' => [
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
					'selector' => '{{PLUS_WRAP}}.sharing-horizontal  .tpgb-social-list .count-label , {{PLUS_WRAP}}.sharing-vertical  .tpgb-social-list .count-label{margin: {{countLblSpace}};}',
				],
			],
			'scopy' => true,
		],
		'iconWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .social-btn-icon{width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}};} ',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .social-btn-icon { font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconGap' => [
			'type' => 'string',
			'default' => '0',
			'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-2' ] ],
			'scopy' => true,
		],
		'iconAbvSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .tpgb-social-list.active { margin-top: {{iconAbvSpace}}; }',
				],
			],
			'scopy' => true,
		],
		
		'icnNmlBG' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		'icnHvrBG' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		'icnNmlBdr' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		'icnHvrBdr' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		'icnNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle .tpgb-social-list .share-btn { border-radius: {{icnNmlBRadius}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn .social-btn-icon { border-radius: {{icnNmlBRadius}}; }',
				],
			],
			'scopy' => true,
		],
		'icnHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover { border-radius: {{icnHvrBRadius}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover .social-btn-icon { border-radius: {{icnHvrBRadius}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		'icnHvrShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover .social-btn-icon',
				],
			],
			'scopy' => true,
		],
		
		'toggleWidth' => [
			'type' => 'string',
			'default' => '40',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .toggle-share  { width: {{toggleWidth}}px; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn  { width: {{toggleWidth}}px; height: {{toggleWidth}}px; }',
				],
			],
			'scopy' => true,
		],
		'tglIconWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => ' {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn{ width: {{tglIconWidth}}; height: {{tglIconWidth}}; line-height: {{tglIconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		
		'tglTextTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle .toggle-label',
				],
			],
			'scopy' => true,
		],
		'tgls1Padding' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share {padding: {{tgls1Padding}};}',
				],
			],
			'scopy' => true,
		],
		'tglIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-btn{ font-size: {{tglIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'tglTitleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-label{ color: {{tglTitleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tglTitleHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-label, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover .toggle-label{ color: {{tglTitleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tglIcnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-sharing.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn{ color: {{tglIcnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tglIcnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-social-sharing.sharing-toggle.sharing-style-2 .tpgb-social-list.active .tpgb-main-menu a.tpgb-share-btn ,{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-btn, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover .toggle-btn{ color: {{tglIcnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tglIcnNmlBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-icon .toggle-btn{ background: {{tglIcnNmlBG}}; }',
				],
			],
			'scopy' => true,
		],
		'tglIcnHvrBG' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ] ,['key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active .toggle-btn, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover .toggle-btn{ background: {{tglIcnHvrBG}}; }',
				],
			],
			'scopy' => true,
		],
		'tglNmlBG' => [
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
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share',
				],
			],
			'scopy' => true,
		],
		'tglHvrBG' => [
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
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list.active .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover',
				],
			],
			'scopy' => true,
		],
		'tglNmlBdr' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share',
				],
			],
			'scopy' => true,
		],
		'tglHvrBdr' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list.active .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover',
				],
			],
			'scopy' => true,
		],
		'tglNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share { border-radius: {{tglNmlBRadius}}; }',
				],
			],
			'scopy' => true,
		],
		'tglHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list.active .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover { border-radius: {{tglHvrBRadius}}; }',
				],
			],
			'scopy' => true,
		],
		'tglNmlShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share',
				],
			],
			'scopy' => true,
		],
		'tglHvrShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '==', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}}.sharing-toggle.sharing-style-2 .tpgb-social-list.active .tpgb-main-menu a.tpgb-share-btn , {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share.menu-active, {{PLUS_WRAP}}.sharing-toggle.sharing-style-1 .toggle-share:hover',
				],
			],
			'scopy' => true,
		],
		'totalNumTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ] ,['key' => 'shareNumber', 'relation' => '!=', 'value' => '' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount-item .total-count-number',
				],
			],
			'scopy' => true,
		],
		'totalNumColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ] ,['key' => 'shareNumber', 'relation' => '!=', 'value' => '' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount-item .total-count-number{ color: {{totalNumColor}}; }',
				],
			],
			'scopy' => true,
		],
		'totalLblTypo' => [
			'type'=> 'object',
			'default'=> (object) [
			'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ] ,['key' => 'shareLabel', 'relation' => '!=', 'value' => '' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount-item .total-number-label',
				],
			],
			'scopy' => true,
		],
		'totalLblColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ] ,['key' => 'shareLabel', 'relation' => '!=', 'value' => '' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount-item .total-number-label{ color: {{totalLblColor}}; }',
				],
			],
			'scopy' => true,
		],
		'totalSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayCounter', 'relation' => '==', 'value' => true ] , ['key' => 'shareNumber', 'relation' => '!=', 'value' => '' ] ,['key' => 'shareLabel', 'relation' => '!=', 'value' => '' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount-item .total-number-label  { margin-top: {{totalSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'bgWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'scopy' => true,
		],
		'bgHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				'sm' => '',
				'xs' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tpgb-social-menu .share-btn{ height: {{bgHeight}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .totalcount { height: {{bgHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'iconSpaceBtwn' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tpgb-social-menu , .tpgb-social-list .totalcount {padding: {{iconSpaceBtwn}};}',
				],
			],
			'scopy' => true,
		],
		'netPadding' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .tpgb-social-menu .share-btn {padding: {{netPadding}};}',
				],
			],
			'scopy' => true,
		],
		'bgNmlBorder' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn',
				],
			],
			'scopy' => true,
		],
		'bgHvrBorder' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover',
				],
			],
			'scopy' => true,
		],
		'bgNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn{border-radius: {{bgNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'bgHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover{border-radius: {{bgHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'bgNmlBShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn',
				],
			],
			'scopy' => true,
		],
		'bgHvrBShadow' => [
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
					'condition' => [(object) ['key' => 'sociallayout', 'relation' => '!=', 'value' => 'toggle' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-social-list .share-btn:hover',
				],
			],
			'scopy' => true,
		],
		);
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-social-sharing', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_social_sharing'
    ) );
}
add_action( 'init', 'tpgb_tp_social_sharing' );