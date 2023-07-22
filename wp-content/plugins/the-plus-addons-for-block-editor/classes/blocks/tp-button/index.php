<?php
/* Block : TP Button
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_button_render_callback( $attributes ) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$btnHvrType = (!empty($attributes['btnHvrType'])) ? $attributes['btnHvrType'] : 'hover-left';
	$iconHvrType = (!empty($attributes['iconHvrType'])) ? $attributes['iconHvrType'] : 'hover-top';
	$iconPosition = (!empty($attributes['iconPosition'])) ? $attributes['iconPosition'] : 'iconAfter';
	$icnVrtcal = (!empty($attributes['icnVrtcal'])) ? $attributes['icnVrtcal'] : 'icon-top';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'fontAwesome';
	$fontAwesomeIcon = (!empty($attributes['fontAwesomeIcon'])) ? $attributes['fontAwesomeIcon'] : '';
	$imageName = (!empty($attributes['imageName']['url'])) ? $attributes['imageName'] : '';
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'full';
	$btnText = (!empty($attributes['btnText'])) ? $attributes['btnText'] : '';
	$btnTagText = (!empty($attributes['btnTagText'])) ? $attributes['btnTagText'] : '';
	$hoverText = (!empty($attributes['hoverText'])) ? $attributes['hoverText'] : '';
	$btnLink = (!empty($attributes['btnLink']['url'])) ? $attributes['btnLink']['url'] : '';
	$target = (!empty($attributes['btnLink']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['btnLink']['nofollow'])) ? 'nofollow' : '';
	$link_attr = Tp_Blocks_Helper::add_link_attributes($attributes['btnLink']);
	$ariaLabel = (!empty($attributes['ariaLabel'])) ? $attributes['ariaLabel'] : '';
	$shakeAnimate = (!empty($attributes['shakeAnimate'])) ? $attributes['shakeAnimate'] : false;
	$btnHvrCnt = (!empty($attributes['btnHvrCnt'])) ? $attributes['btnHvrCnt'] : false;
	$selectHvrCnt = (!empty($attributes['selectHvrCnt'])) ? $attributes['selectHvrCnt'] : '';
	$fancyBox = (!empty($attributes['fancyBox'])) ? $attributes['fancyBox'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	if(class_exists('Tpgbp_Pro_Blocks_Helper')){
		$btnLink = (isset($attributes['btnLink']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['btnLink']) : (!empty($attributes['btnLink']['url']) ? $attributes['btnLink']['url'] : '');
	}

	$IShakeAnimate='';
	if(!empty($shakeAnimate)){
		$IShakeAnimate='shake_animate';
	}
	$iconHover='';
	if($styleType=='style-11' || $styleType=='style-13'){
		$iconHover .=$btnHvrType;
	}
	if($styleType=='style-17'){
		$iconHover .=$iconHvrType;
	}
	$s23VrtclCntr ='';
	if($styleType=='style-23'){
		$s23VrtclCntr .=$icnVrtcal;
	}
	$translin = '';
	if($styleType!='style-10' && $styleType!='style-13'){
		$translin = 'tpgb-trans-linear';
	}
	$getBfrIcon ='';
	$getBfrIcon .='<span class="btn-icon '.esc_attr($translin).' '.($styleType!='style-17' ? ' button-before' : ' tpgb-rel-flex').'">';
	$getBfrIcon .='<i class="'.esc_attr($fontAwesomeIcon).'"></i>';
	$getBfrIcon .='</span>';
	
	$getAftrIcon ='';
	$getAftrIcon .='<span class="btn-icon '.esc_attr($translin).' '.($styleType!='style-17' ? ' button-after' : ' tpgb-rel-flex').'">';
	$getAftrIcon .='<i class="'.esc_attr($fontAwesomeIcon).'"></i>';
	$getAftrIcon .='</span>';


	$imgSrc ='';
	if(!empty($imageName) && !empty($imageName['id'])){
		$imgBfAf = '';
		if($styleType!='style-17'){
			if($iconPosition == 'iconBefore'){
				$imgBfAf = 'button-before';
			}else if($iconPosition == 'iconAfter'){
				$imgBfAf = 'button-after';
			}
		}else{
			$imgBfAf = 'tpgb-rel-flex';
		}
		$imgSrc = wp_get_attachment_image($imageName['id'] , $imageSize, false, ['class' => 'btn-icon '.esc_attr($translin).' '.$imgBfAf]);
	}else if(!empty($imageName['url'])){
		$imgSrc = '<img src="'.esc_url($imageName['url']).'" class="btn-icon '.esc_attr($translin).' '.esc_attr($imgBfAf).'" />';
	}
	
	$getButtonSource='';
	
		if($styleType!='style-3' && $styleType!='style-6' && $styleType!='style-7' && $styleType!='style-9' && $styleType!='style-23' && $iconPosition=='iconBefore'){
			if($iconType=='fontAwesome'){
				$getButtonSource .= $getBfrIcon;
			}else if($iconType=='image'){
				$getButtonSource .= $imgSrc;
			}
		}
		if($styleType=='style-6'){
			$getButtonSource .='<span class="btn-left-arrow"><i class="fas fa-chevron-right"></i></span>';
		}
		if($styleType=='style-17'){
			$getButtonSource .='<span class="tpgb-rel-flex">'.wp_kses_post($btnText).'</span>';
		}
		if($styleType!='style-17' && $styleType!='style-23'){
			$getButtonSource.= wp_kses_post($btnText);
		}
		if($styleType=='style-23'){
			if($icnVrtcal=='icon-top'){
				$getButtonSource .='<span class="button-tag-hint">';
					if($iconPosition=='iconBefore'){
						if($iconType=='fontAwesome'){
							$getButtonSource .= $getBfrIcon;
						}else if($iconType=='image'){
							$getButtonSource .= $imgSrc;
						}
					}
						$getButtonSource .= wp_kses_post($btnTagText);
					if($iconPosition=='iconAfter'){
						if($iconType=='fontAwesome'){
							$getButtonSource .= $getAftrIcon;
						}else if($iconType=='image'){
							$getButtonSource .= $imgSrc;
						}
					}
				$getButtonSource .= '</span>';
				$getButtonSource .='<span>'.wp_kses_post($btnText).'</span>';
			}
			if($icnVrtcal=='icon-middle'){
				if($iconPosition=='iconBefore'){
					if($iconType=='fontAwesome'){
						$getButtonSource .= $getBfrIcon;
					}else if($iconType=='image'){
						$getButtonSource .= $imgSrc;
					}
				}
				$getButtonSource .='<span>';	
					$getButtonSource .='<span class="button-tag-hint">'.wp_kses_post($btnTagText).'</span>';
					$getButtonSource.= wp_kses_post($btnText);
				$getButtonSource .='</span>';
				if($iconPosition=='iconAfter'){
					if($iconType=='fontAwesome'){
						$getButtonSource .= $getAftrIcon;
					}else if($iconType=='image'){
						$getButtonSource .= $imgSrc;
					}
				}
			}
			if($icnVrtcal=='icon-bottom'){
				$getButtonSource .='<span class="button-tag-hint">';
					$getButtonSource .= wp_kses_post($btnTagText);
				$getButtonSource .= '</span>';
				$getButtonSource .='<span>';
				if($iconPosition=='iconBefore'){
					if($iconType=='fontAwesome'){
						$getButtonSource .= $getBfrIcon;
					}else if($iconType=='image'){
						$getButtonSource .= $imgSrc;
					}
				}
				$getButtonSource .= wp_kses_post($btnText);
				if($iconPosition=='iconAfter'){
					if($iconType=='fontAwesome'){
						$getButtonSource .= $getAftrIcon;
					}else if($iconType=='image'){
						$getButtonSource .= $imgSrc;
					}
				}
				$getButtonSource .='</span>';
			}
			}
		if($styleType=='style-3'){
			$getButtonSource .='<svg class="arrow" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9">';
				$getButtonSource .='<path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path>';
			$getButtonSource .='</svg>';
			$getButtonSource .='<svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9">';
				$getButtonSource .='<path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path>';
			$getButtonSource .='</svg>';
		}
		if($styleType!='style-3' && $styleType!='style-6' && $styleType!='style-7' && $styleType!='style-9' && $styleType!='style-23' && $iconPosition=='iconAfter'){
			if($iconType=='fontAwesome'){
				$getButtonSource .= $getAftrIcon;
			}else if($iconType=='image'){
				$getButtonSource .= $imgSrc;
			}
		}
		if($styleType=='style-7'){
			$getButtonSource .='<span class="btn-arrow '.esc_attr($translin).'"><span class="btn-right-arrow"><i class="fas fa-chevron-right"></i></span></span>';
		}
		if($styleType=='style-9'){
			$getButtonSource .='<span class="btn-arrow '.esc_attr($translin).'">';
				$getButtonSource .='<i class="btn-show fa fa-chevron-right" aria-hidden="true"></i>';
				$getButtonSource .='<i class="btn-hide fa fa-chevron-right" aria-hidden="true"></i>';
			$getButtonSource .='</span>';
		}
		if($styleType=='style-12'){
			$getButtonSource .='<div class="button_line"></div>';
		}
	$contentHvrClass='';
	if(!empty($btnHvrCnt) && !empty($selectHvrCnt) ){
		$contentHvrClass = ' tpgb_cnt_hvr_effect cnt_hvr_'.esc_attr($selectHvrCnt);
	}

	$extrAttr = ''; 
	$fancyData = [];
	 
	if(!empty($fancyBox)){
		global $post;
		$extrAttr .= 'data-src="#tpgb-query-'.(isset($post->ID) ? $post->ID : get_queried_object_id() ).'" data-touch="false" href="javascript:;" ';
		
		$autoDimen = (!empty($attributes['autoDimen'])) ? $attributes['autoDimen'] : false ;


		$fancyData['autoDimensions'] = (int) $autoDimen ;
		$fancyData = htmlspecialchars(json_encode($fancyData), ENT_QUOTES, 'UTF-8');

		$extrAttr .= ' data-fancy-opt= \'' .$fancyData. '\' ';

	}else{
		$extrAttr = 'href="'.esc_url($btnLink).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" ';
	}

	$ariaLabelT = (!empty($ariaLabel)) ? esc_attr($ariaLabel) : ((!empty($btnText)) ? esc_attr($btnText) : esc_attr__("Button", 'tpgb'));
    $output .= '<div class="tpgb-plus-button tpgb-relative-block tpgb-block-'.esc_attr($block_id).' button-'.esc_attr($styleType).' '.esc_attr($iconHover).' '.esc_attr($blockClass).' ">';
		$output .='<div class="animted-content-inner'.esc_attr($contentHvrClass).'">';
			$output .='<a '.$extrAttr.' class="button-link-wrap '.esc_attr($translin).' '.esc_attr($IShakeAnimate).' '.esc_attr($s23VrtclCntr).' '.(!empty($fancyBox) ? ' tpgb-fancy-popup' : '').' " role="button" aria-label="'.$ariaLabelT.'" data-hover="'.wp_kses_post($hoverText).'" '.$link_attr.'>';
				if($styleType != 'style-17' && $styleType != 'style-23'){
					$output .='<span>'.$getButtonSource.'</span>';
				}
				if($styleType == 'style-17' || $styleType == 'style-23'){
					$output .=$getButtonSource;
				}
			$output .='</a>';
		$output .='</div>';

		// Load Fancy Box Content 
		if(!empty($fancyBox)){
			$output .= '<div class="tpgb-btn-fpopup" id="tpgb-query-'.(isset($post->ID) ? $post->ID : get_queried_object_id() ).'" >';
				ob_start();
				if(!empty($attributes['templates']) && $attributes['templates'] != 'none') {
					echo Tpgb_Library()->plus_do_block($attributes['templates']);
				}
				$output .= ob_get_contents();
				ob_end_clean();
			$output .= '</div>';
		}
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_button() {
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
  
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'styleType' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'btnText' => [
				'type' => 'string',
				'default' => 'Buy Now',	
			],
			'hoverText' => [
				'type' => 'string',
				'default' => 'Click Here',
			],
			'btnTagText' => [
				'type' => 'string',
				'default' => 'Click Here',	
			],
			'fancyBox' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'backendVisi' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'templates' => [
				'type' => 'string',
           		'default' => '',	
			],
			'btnLink' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',	
					'target' => '',	
					'nofollow' => ''
				],
			],
			'Alignment' => [
				'type' => 'object',
				'default' => 'left',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button{ text-align: {{Alignment}}; }',
					],
				],
				'scopy' => true,
			],
			'btnHvrType' => [
				'type' => 'string',
				'default' => 'hover-left',	
			],
			'iconHvrType' => [
				'type' => 'string',
				'default' => 'hover-top',	
			],
			'iconType' => [
				'type' => 'string',
				'default' => 'fontAwesome',	
			],
			'fontAwesomeIcon' => [
				'type'=> 'string',
				'default'=> 'fa fa-chevron-right',
			],
			'imageName' => [
				'type' => 'object',
				'default' => [],
			],
			'imageSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'ariaLabel' => [
				'type' => 'string',
				'default' => '',	
			],
			'iconPosition' => [
				'type' => 'string',
				'default' => 'iconAfter',
				'scopy' => true,
			],
			'icnVrtcal' => [
				'type' => 'string',
				'default' => 'icon-top',	
				'scopy' => true,
			],
			'iconSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3', 'style-6', 'style-7', 'style-9'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .button-before { margin-right: {{iconSpace}}; } {{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .button-after { margin-left: {{iconSpace}}; } {{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap .button-before{ padding-left: {{iconSpace}}; } {{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap .button-after{ padding-left: {{iconSpace}}; } ',
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ], ['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome']],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .btn-icon { font-size: {{iconSize}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ], ['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome']],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap .btn-left-arrow { font-size: {{iconSize}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ], ['key' => 'iconType', 'relation' => '==', 'value' => 'image']],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .btn-icon { width: {{iconSize}}; height: {{iconSize}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ], ['key' => 'iconType', 'relation' => '==', 'value' => 'image']],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap .btn-left-arrow { width: {{iconSize}}; height: {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'innerPadding' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button:not(.button-style-11):not(.button-style-17) .button-link-wrap , {{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap > span , {{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap>span:not(.btn-icon){padding: {{innerPadding}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 .button-link-wrap{padding: {{innerPadding}};}',
					],
				],
				'scopy' => true,
			],
			'texTyp' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'tagTyp' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ],['key' => 'btnTagText', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .button-tag-hint',
					],
				],
				'scopy' => true,
			],
			'btnTextNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap{ color: {{btnTextNmlColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ],['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .btn-icon{ color: {{btnTextNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 .button-link-wrap .arrow *{ fill: {{btnTextNmlColor}}; stroke: {{btnTextNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap .btn-left-arrow{ color: {{btnTextNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-7' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap .btn-arrow{ color: {{btnTextNmlColor}}; }{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap:after{ border-color: {{btnTextNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-9 .button-link-wrap .btn-arrow{ color: {{btnTextNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap{ color: {{btnTextNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ],['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .btn-icon{ color: {{iconNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 .button-link-wrap .arrow *{ fill: {{iconNmlColor}}; stroke: {{iconNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap .btn-left-arrow{ color: {{iconNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-7' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap .btn-arrow{ color: {{iconNmlColor}}; }{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap:after{ border-color: {{iconNmlColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-9 .button-link-wrap .btn-arrow{ color: {{iconNmlColor}};}',
					],
				],
				'scopy' => true,
			],
			'BNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => ['style-12','style-18'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .button_line{ background: {{BNmlColor}}; } {{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap{ background: {{BNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'normalBG' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-2 .button-link-wrap .btn-icon',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 a.button-link-wrap:before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 a.button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-15' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-15 .button-link-wrap::before , {{PLUS_WRAP}}.tpgb-plus-button.button-style-15 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-18' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'bgNormalB' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'normalBRadius' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-12', 'style-2', 'style-3', 'style-5', 'style-6', 'style-7', 'style-9', 'style-18'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap{border-radius: {{normalBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'nmlboxShadow' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-2 .button-link-wrap .btn-icon',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-15' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-15 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-18' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'borderHeight' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-12' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap .button_line{ height: {{borderHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'btnTextHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap:hover{ color: {{btnTextHvrColor}}; }{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap::before{ color: {{btnTextHvrColor}}; }{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap::after{ color: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ],['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap:hover .btn-icon{ color: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 a.button-link-wrap:hover .arrow-1 *{ fill: {{btnTextHvrColor}}; stroke: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap:hover .btn-left-arrow{ color: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-7' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap:hover .btn-arrow{ color: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-9 .button-link-wrap:hover .btn-arrow{ color: {{btnTextHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap:hover{ color: {{btnTextHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-3','style-6','style-7','style-9'] ],['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap:hover .btn-icon{ color: {{iconHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 a.button-link-wrap:hover .arrow-1 *{ fill: {{iconHvrColor}}; stroke: {{iconHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-6 .button-link-wrap:hover .btn-left-arrow{ color: {{iconHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-7' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-7 .button-link-wrap:hover .btn-arrow{ color: {{iconHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-9 .button-link-wrap:hover .btn-arrow{ color: {{iconHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'BHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => ['style-12','style-18'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap:hover .button_line{ background: {{BHoverColor}}; }{{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap::before{ background: {{BHoverColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hoverBG' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-2 .button-link-wrap:hover .btn-icon',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-3 a.button-link-wrap:hover:before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 a.button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap:hover,{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap:before,{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap:after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap::before ,{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-15' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-15 .button-link-wrap:hover::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-18' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap:hover::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap::after',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'bgHoverB' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap:hover, {{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'hoverBRadius' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => ['style-12','style-2','style-3','style-5', 'style-6','style-7','style-9','style-18'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap:hover{border-radius: {{hoverBRadius}};} ',
					],
				],
				'scopy' => true,
			],
			'hvrboxShadow' => [
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
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-1 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-2 .button-link-wrap:hover .btn-icon',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-4 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-5 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-8 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-10 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-11 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-13 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-14' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-14 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-15' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-15 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-16' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-16 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-17' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-17 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-18' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-18 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-19' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-19 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-20' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-20 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-21' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-21 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-22 .button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-23' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button.button-style-23 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'btnWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-3' ],['key' => 'styleType', 'relation' => '!=', 'value' => 'style-6' ],['key' => 'styleType', 'relation' => '!=', 'value' => 'style-7' ],['key' => 'styleType', 'relation' => '!=', 'value' => 'style-12' ],['key' => 'styleType', 'relation' => '!=', 'value' => 'style-17' ],['key' => 'styleType', 'relation' => '!=', 'value' => 'style-22' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .animted-content-inner { width: 100%; max-width: {{btnWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'shakeAnimate' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'shakeDuration' => [
				'type' => 'string',
				'default' => '5',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'shakeAnimate', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-plus-button .button-link-wrap.shake_animate { animation-duration: {{shakeDuration}}s; -o-animation-duration: {{shakeDuration}}s; -ms-animation-duration: {{shakeDuration}}s; -moz-animation-duration: {{shakeDuration}}s; -webkit-animation-duration: {{shakeDuration}}s; }',
					],
				],
				'scopy' => true,
			],
			'btnHvrCnt' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'selectHvrCnt' => [
				'type' => 'string',
				'default' => '',	
				'scopy' => true,
			],
			'cntHvrcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnHvrCnt', 'relation' => '==', 'value' => true ],
							['key' => 'selectHvrCnt', 'relation' => '==', 'value' => 'float_shadow' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb_cnt_hvr_effect.cnt_hvr_float_shadow:before{background: -webkit-radial-gradient(center, ellipse, {{cntHvrcolor}} 0%, rgba(60, 60, 60, 0) 70%);background: radial-gradient(ellipse at 50% 150%,{{cntHvrcolor}} 0%, rgba(60, 60, 60, 0) 70%); }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnHvrCnt', 'relation' => '==', 'value' => true ],
							['key' => 'selectHvrCnt', 'relation' => '==', 'value' => 'grow_shadow' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb_cnt_hvr_effect.cnt_hvr_grow_shadow:hover {-webkit-box-shadow: 0 10px 10px -10px {{cntHvrcolor}};-moz-box-shadow: 0 10px 10px -10px {{cntHvrcolor}};box-shadow: 0 10px 10px -10px {{cntHvrcolor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnHvrCnt', 'relation' => '==', 'value' => true ],
							['key' => 'selectHvrCnt', 'relation' => '==', 'value' => 'shadow_radial' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb_cnt_hvr_effect.cnt_hvr_shadow_radial:before{background: -webkit-radial-gradient(center, ellipse at 50% 150%, {{cntHvrcolor}} 0%, rgba(60, 60, 60, 0) 70%);background: radial-gradient(ellipse at 50% 150%,{{cntHvrcolor}} 0%, rgba(60, 60, 60, 0) 70%); }{{PLUS_WRAP}} .tpgb_cnt_hvr_effect.cnt_hvr_shadow_radial:after {background: -webkit-radial-gradient(50% -50%, ellipse, {{cntHvrcolor}} 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% -50%, {{cntHvrcolor}} 0%, rgba(0, 0, 0, 0) 80%);}',
					],
				],
				'scopy' => true,
			],
			'fancWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'fancyBox', 'relation' => '==', 'value' => true]],
						'selector' => '.tpgb-button-fancy .tpgb-btn-fpopup { width: 100%; max-width : {{fancWidth}} }',
					],
				],
			],
			'fanoverlay' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'fancyBox', 'relation' => '==', 'value' => true]],
						'selector' => '.tpgb-button-fancy .fancybox-bg { background : {{fanoverlay}} }',
					],
				],
			],
		);
	$attributesOptions = array_merge($attributesOptions,$globalPlusExtrasOption,$globalBgOption,$globalpositioningOption);
	
	register_block_type( 'tpgb/tp-button', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_button_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_button' );