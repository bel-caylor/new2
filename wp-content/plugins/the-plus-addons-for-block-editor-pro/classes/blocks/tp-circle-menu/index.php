<?php
/* Block : Circle Menu
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_circle_menu_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$circleMenu = (!empty($attributes['circleMenu'])) ? $attributes['circleMenu'] : [];
	$layoutType = (!empty($attributes['layoutType'])) ? $attributes['layoutType'] : 'circle';
	$cDirection = (!empty($attributes['cDirection'])) ? $attributes['cDirection'] : 'bottom-right';
	$menuStyle = (!empty($attributes['menuStyle'])) ? $attributes['menuStyle'] : 'style-1';
	$sDirection = (!empty($attributes['sDirection'])) ? $attributes['sDirection'] : 'right';
	$tglIcnType = (!empty($attributes['tglIcnType'])) ? $attributes['tglIcnType'] : 'icon';
	$iconStore = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : '';
	$imageStore = (!empty($attributes['imageStore']['url'])) ? $attributes['imageStore'] : '';
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'full';
	$tglStyle = (!empty($attributes['tglStyle'])) ? $attributes['tglStyle'] : 'style-1';
	$iconPos = (!empty($attributes['iconPos'])) ? $attributes['iconPos'] : 'absolute';
	
	$leftAuto = (!empty($attributes['leftAuto'])) ? $attributes['leftAuto'] : false;
	$rightAuto = (!empty($attributes['rightAuto'])) ? $attributes['rightAuto'] : false;
	
	$iconGap = (!empty($attributes['iconGap'])) ? $attributes['iconGap'] : 0;
	
	$angleStart = (!empty($attributes['angleStart'])) ? $attributes['angleStart'] : 0;
	$angleEnd = (!empty($attributes['angleEnd'])) ? $attributes['angleEnd'] : 90;
	$circleRadius = (!empty($attributes['circleRadius'])) ? $attributes['circleRadius'] : 150;
	$iconDelay = (!empty($attributes['iconDelay'])) ? $attributes['iconDelay'] : 1000;
	$menuOSpeed = (!empty($attributes['menuOSpeed'])) ? $attributes['menuOSpeed'] : 500;
	$icnStepIn = (!empty($attributes['icnStepIn'])) ? $attributes['icnStepIn'] : -20;
	$icnStepOut = (!empty($attributes['icnStepOut'])) ? $attributes['icnStepOut'] : 20;
	$icnTrans = (!empty($attributes['icnTrans'])) ? $attributes['icnTrans'] : 'ease';
	$icnTrigger = (!empty($attributes['icnTrigger'])) ? $attributes['icnTrigger'] : 'hover';
	
	$scrollToggle = (!empty($attributes['scrollToggle'])) ? $attributes['scrollToggle'] : false;
	$scrollValue = (!empty($attributes['scrollValue'])) ? $attributes['scrollValue'] : '';
	$overlayColorTgl = (!empty($attributes['overlayColorTgl'])) ? $attributes['overlayColorTgl'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$imgSrc = '';
	if(!empty($imageStore) && !empty($imageStore['id'])){
		$imgSrc = wp_get_attachment_image($imageStore['id'] , $imageSize, false, ['class' => 'toggle-icon-wrap']);
	}else if(!empty($imageStore['url'])){
		$imgSrc = '<img src="'.esc_url($imageStore['url']).'" class="toggle-icon-wrap" />';
	}else{
		$imgSrc = '<img src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'" class="toggle-icon-wrap" />';
	}
	
	$position_class=$icon_layout_straight_style=$layout_straight_menu_direction='';
	if($iconPos == 'absolute'){
		$position_class = 'circle_menu_position_abs';
	}else if($iconPos == 'fixed'){
		$position_class = 'circle_menu_position_fix';
	}
	
	$loopStyle = '';
	if($layoutType=='straight'){
		$icon_layout_straight_style = 'menu-'.$menuStyle;
		$layout_straight_menu_direction = 'menu-direction-'.$sDirection;
	}
	$p = 1;
	$direction = '';
	
	$ariaLabel = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : esc_attr__('Toggle Button', 'tpgbp');
	
	/*circle start (fatched)*/
	$angle_start=$angle_end=$circleRadiusDesktop=$circleRadiusTablet=$circleRadiusMobile='';
	if($layoutType=='circle'){
		if($circleRadius!==''){
			$circleRadiusDesktop .= (!empty($circleRadius['md'])) ? $circleRadius['md'] : 150;
			$circleRadiusTablet .= (!empty($circleRadius['sm'])) ? $circleRadius['sm'] : $circleRadiusDesktop;
			$circleRadiusMobile .= (!empty($circleRadius['xs'])) ? $circleRadius['xs'] : $circleRadiusTablet;
		}	
	}
	if($layoutType=='circle'){
		if($cDirection =='none'){
			$angle_start = $angleStart;
			$angle_end = $angleEnd;
		}else{
			$angle_start = 0;
			$angle_end = 0;
		}
	}
	
	// Set Dataattr For Circle Menu
	$cirmenupara = [
		'direction' => $cDirection,
		'anglestart' => $angle_start,
		'angleend' => $angle_end,
		'circle_radius' => $circleRadiusDesktop,
		'circle_radius_tablet' => $circleRadiusTablet,
		'circle_radius_mobile' => $circleRadiusMobile,
		'delay' => $iconDelay,			
		'item_diameter' => 0,
		'speed' => $menuOSpeed,
		'step_in' => $icnStepIn,
		'step_out' => $icnStepOut,
		'transition_function' => $icnTrans,
		'trigger' => $icnTrigger
	];
	$cirmenupara = htmlspecialchars(json_encode($cirmenupara), ENT_QUOTES, 'UTF-8');
	/*circle end*/
	
	//Scroll Offset Value
	$dataScrollValue = $scrollViewClass = '';
	if(!empty($scrollToggle)){
		$scrollViewClass = 'scroll-view';
	}
	if(!empty($scrollToggle) && !empty($scrollValue)){
		$dataScrollValue = 'data-scroll-view="'.esc_attr($scrollValue).'"';
	}
	
	if($layoutType=='straight'){
		$direction = ( $sDirection == 'left' ? 'right' : ($sDirection == 'right' ? 'left' : ( $sDirection == 'top' ? 'bottom' : ( $sDirection == 'bottom' ? 'top' : ''))));
	}
	$leftAValue = (array)$attributes['leftASize'];
	$rightAValue = (array)$attributes['rightASize'];
	$toggleIcnWidth = (array)$attributes['tIcnWidth'];
	if(!empty($leftAuto) && !empty($rightAuto)){
		$selector = '.tpgb-block-'.esc_attr($block_id).'.layout-circle .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap';
		if(isset($leftAValue['md']) && $leftAValue['md']=='0' && $rightAValue['md']=='0') {
			$loopStyle.= $selector.'{left: calc('.$leftAValue['md'].$leftAValue['unit'].' - '.$toggleIcnWidth["md"].$toggleIcnWidth["unit"].' );}';
		}
		if(isset($leftAValue['sm']) && $leftAValue['sm']=='0' && $rightAValue['sm']=='0') {
			$loopStyle .= '@media (max-width: 1024px){'.$selector.'{left: calc('.$leftAValue['sm'].$leftAValue['unit'].' - '.$toggleIcnWidth["sm"].$toggleIcnWidth["unit"].' );}}';
		}
		if(isset($leftAValue['xs']) && $leftAValue['xs']=='0' && $rightAValue['xs']=='0') {
			$loopStyle .= '@media (max-width: 767px){'.$selector.'{left: calc('.$leftAValue['xs'].$leftAValue['unit'].' - '.$toggleIcnWidth["xs"].$toggleIcnWidth["unit"].' );}}';
		}
	}
	
	$output = '';
	$output .= '<div id="tpgb-block-'.esc_attr($block_id).'" class="tpgb-circle-menu tpgb-relative-block tpgb-block-'.esc_attr($block_id).' layout-'.esc_attr($layoutType).' '.esc_attr($scrollViewClass).' '.esc_attr($blockClass).'" data-block-id="tpgb-block-'.esc_attr($block_id).'" data-cirmenu-opt= \'' .$cirmenupara. '\'  '.$dataScrollValue.'>';
		$output .= '<div class="tpgb-circle-menu-inner-wrapper">';
			if(!empty($overlayColorTgl)){
				$output .='<div id="show-bg-overlay" class="show-bg-overlay"></div>';
			}
			$output .= '<ul class="tpgb-circle-menu-wrap circleMenu-closed '.esc_attr($position_class).' '.esc_attr($layout_straight_menu_direction).' '.esc_attr($icon_layout_straight_style).'">';
				$output .= '<li class="tpgb-circle-main-menu-list tpgb-circle-menu-list '.esc_attr($tglStyle).'">';
					$output .= '<a class="main_menu_icon tpgb-rel-flex" style="cursor:pointer" href="#" aria-label="'.$ariaLabel.'">';
						if($tglIcnType=='icon'){
							$output .= '<span class="toggle-icon-wrap">';
								$output .= '<i class="'.esc_attr($iconStore).'"></i>';
							$output .= '</span>';
						}
						if($tglIcnType=='image' && !empty($imageStore)){
							$output .= $imgSrc;
						}
						if($tglStyle=='style-3'){
							$output .= '<span class="close-toggle-icon"></span>';
						}
					$output .= '</a>';
				$output .= '</li>';
				if(!empty($circleMenu)){
					$leftValue = 0;
					$tIcnWidth = (array)$attributes['tIcnWidth'];
					foreach ( $circleMenu as $index => $network ) {
						$p++;
						$target =$nofollow = $link_attr = '';
						if(!empty($network['linkType']) && $network['linkType']=='email' && !empty($network['emailtxt'])){
							$icon_url='mailto:'.$network['emailtxt'];
						}else if(!empty($network['linkType']) && $network['linkType']=='phone' && !empty($network['phone'])){
							$icon_url='tel:'.$network['phoneNo'];
						}else if(!empty($network['linkType']) && $network['linkType']=='url' && !empty($network['linkUrl']['url'])){
							$target = $network['linkUrl']['target'] ? ' target="_blank"' : '';
							$nofollow = $network['linkUrl']['nofollow'] ? ' rel="nofollow"' : '';
							$icon_url = (isset($network['linkUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($network['linkUrl']) : (!empty($network['linkUrl']['url']) ? $network['linkUrl']['url'] : '');
							
							$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($network['linkUrl']);
						}else{
						$target = ' target="_blank"';
							$nofollow = ' rel="nofollow"';
							$icon_url='#';
						}
						if(!empty($network['linkType']) && $network['linkType'] != 'nolink'){
							$nolink='href="'.esc_url($icon_url).'" '.$target.' '.$nofollow;
						}else{
							$nolink='';
						}
						$leftValue = $leftValue+(int)$tIcnWidth['md']+$iconGap;
						//tooltip
						$itemtooltip ='';
						
						$uniqid=uniqid("tooltip");
						if(($layoutType=='circle' || ($layoutType=='straight' && $menuStyle=='style-1')) && !empty($network['itemTooltip'])){
							$itemtooltip .= ' data-tippy=""';
							$itemtooltip .= ' data-tippy-interactive="'.($attributes['tipInteractive'] ? 'true' : 'false').'"';
							$itemtooltip .= ' data-tippy-placement="'.($attributes['tipPlacement'] ? $attributes['tipPlacement'] : 'top').'"';
							$itemtooltip .= ' data-tippy-theme="'.$attributes['tipTheme'].'"';
							$itemtooltip .= ' data-tippy-arrow="'.($attributes['tipArrow'] ? 'true' : 'false').'"';
							$itemtooltip .= ' data-tippy-animation="'.($attributes['tipAnimation'] ? $attributes['tipAnimation'] : 'fade').'"';
							$itemtooltip .= ' data-tippy-offset="['.(!empty($attributes['tipOffset']) ? (int)$attributes['tipOffset'] : 0).','.(!empty($attributes['tipDistance']) ? (int)$attributes['tipDistance'] : 0).']"';

							$itemtooltip .= ' data-tippy-duration="['.(int)$attributes['tipDurationIn'].','.(int)$attributes['tipDurationOut'].']"';
						}
						
						$contentItem =[];
						if(($layoutType=='circle' || ($layoutType=='straight' && $menuStyle=='style-1')) && !empty($network['itemTooltip'])){
							$contentItem['content'] = (!empty($network['tooltipText']) && preg_match( '/data-tpgb-dynamic=(.*?)\}/', $network['tooltipText'], $route ))  ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($network['tooltipText']) : (!empty($network['tooltipText']) ? $network['tooltipText'] : '');
							$contentItem['trigger'] = (!empty($attributes['tipTriggers'])  ? $attributes['tipTriggers'] : 'mouseenter');
							$contentItem = htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8');
						}
						$ariaLabelT = (!empty($network['ariaLabel'])) ? esc_attr($network['ariaLabel']) : ((!empty($network['title'])) ? esc_attr($network['title']) : esc_attr__("Button", 'tpgbp'));
						$output .= '<li id="'.esc_attr($uniqid).'" class="tpgb-circle-menu-list tp-repeater-item-'.esc_attr($network['_key']).'" '.$itemtooltip.' data-tooltip-opt= \'' .(!empty($contentItem) ? $contentItem : '' ). '\'>';
							$output .= '<a '.$nolink.' class="menu_icon tpgb-rel-flex" aria-label="'.$ariaLabelT.'" '.$link_attr.'>';
							if($layoutType=='circle' || ($layoutType=='straight' && $menuStyle=='style-1')){
								if($network['iconType']=="icon"){
									$output .= '<i class="'.esc_attr($network['iconStore']).'"></i>';
								}
								if($network['iconType']=="image" && !empty($network['imageStore'])){
									$imageSize = (!empty($network['imageSize'])) ? $network['imageSize'] : 'thumbnail';
									$imgISrc = '';
									if(!empty($network['imageStore']) && !empty($network['imageStore']['dynamic'])){
										$imgISrc = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($network['imageStore']);
										$imgISrc = '<img class="img" src="'.esc_url($imgISrc).'"/>';
									}else if(!empty($network['imageStore']) && !empty($network['imageStore']['id'])){
										$imgISrc = wp_get_attachment_image($network['imageStore']['id'] , $imageSize, false, ['class' => 'img']);
									}else if(!empty($network['imageStore']['url'])){
										$imgISrc = '<img class="img" src="'.esc_url($network['imageStore']['url']).'"/>';
									}
									$output .= $imgISrc;
								}
							}
							if($layoutType=='straight' && $menuStyle=='style-2' && !empty($network['title'])){
								$output .= '<span class="menu-tooltip-title">'.wp_kses_post($network['title']).'</span>';
							}
							$output .= '</a>';
							if($layoutType=='straight'){
								$loopStyle .= '.tpgb-block-'.esc_attr($block_id).'.layout-straight .tpgb-circle-menu-wrap.circleMenu-open.menu-direction-'.esc_attr($sDirection).' li:nth-child('.esc_attr($p).'){ '.esc_attr($direction).': '.esc_attr($leftValue).'px;}';
							}
						$output .= '</li>';
					}
				}
						
			$output .= '</ul>';
		$output .= '</div>';
	$output .= '</div>';
	if(!empty($loopStyle)){
		$output .= '<style>'.$loopStyle.'</style>';
	}
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_circle_menu() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'layoutType' => [
			'type' => 'string',
			'default' => 'circle',	
		],
		'cDirection' => [
			'type' => 'string',
			'default' => 'bottom-right',	
		],
		'menuStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'sDirection' => [
			'type' => 'string',
			'default' => 'right',	
		],
		'circleMenu' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'iconType' => [
						'type' => 'string',
						'default' => 'icon',
					],
					'iconStore' => [
						'type'=> 'string',
						'default'=> 'fab fa-whatsapp',
					],
					'imageStore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'thumbnail',	
					],
					'title' => [
						'type' => 'string',
						'default' => 'New Menu'
					],
					'linkType' => [
						'type' => 'string',
						'default' => 'url',
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=>[
							'url' => '#',	
							'target' => '',	
							'nofollow' => ''	
						]
					],
					'emailtxt' => [
						'type' => 'string',
						'default' => ''
					],
					'phoneNo' => [
						'type' => 'string',
						'default' => ''
					],
					'ariaLabel' => [
						'type' => 'string',
						'default' => '',	
					],
					'iconNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}} .menu_icon { color: {{iconNmlColor}}; }',
							],
						],
					],
					'iconHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}}:hover .menu_icon { color: {{iconHvrColor}}; }',
							], 
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}}:hover .menu-tooltip-title{color: {{iconHvrColor}};}',
							],
						],
					],
					'textNmlColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}} .menu-tooltip-title{color: {{textNmlColor}};}',
							],
						],
					],
					'textHvrColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}}:hover .menu-tooltip-title{color: {{textHvrColor}};}',
							],
						],
					],
					'iconNmlBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}} .menu_icon',
							],
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}} .menu-tooltip-title',
							],
						],
					],
					'iconHvrBG' => [
						'type' => 'object',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}}:hover .menu_icon',
							],
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}}:hover .menu-tooltip-title',
							],
						],
					],
					'nmlBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}} .menu_icon { border-color: {{nmlBColor}}; }',
							],
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}} .menu-tooltip-title{border-color: {{nmlBColor}};}',
							],
						],
					],
					'hvrBColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list{{TP_REPEAT_ID}}:hover .menu_icon { border-color: {{hvrBColor}}; }',
							], 
							(object) [
								'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 {{TP_REPEAT_ID}}:hover .menu-tooltip-title{border-color: {{hvrBColor}};}',
							],
						], 
					],
					'tooltipText' => [
						'type' => 'string',
						'default' => 'I am tooltip.'
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'iconType' => 'icon',
					'iconStore' => 'fab fa-facebook-f',
					'title' => 'Facebook',
					'linkType' => 'url',
					'emailtxt' => '',
					'iconNmlBG' => ['openBg' => 1,'bgDefaultColor' => "#3a579a",],
					'tooltipText' => 'Facebook',
				],
				[
					'_key' => '1',
					'iconType' => 'icon',
					'iconStore' => 'fab fa-twitter',
					'title' => 'Twitter',
					'linkType' => 'url',
					'emailtxt' => '',
					'iconNmlBG' => ['openBg' => 1,'bgDefaultColor' => "#0aaded",],
					'tooltipText' => 'Twitter',
				],
				[
					'_key' => '2',
					'iconType' => 'icon',
					'iconStore' => 'fab fa-instagram',
					'title' => 'Instagram',
					'linkType' => 'url',
					'emailtxt' => '',
					'iconNmlBG' => ['openBg' => 1,'bgDefaultColor' => "#c32aa3",],
					'tooltipText' => 'Instagram',
				],
				[
					'_key' => '3',
					'iconType' => 'icon',
					'iconStore' => 'fab fa-linkedin-in',
					'title' => 'LinkedIn',
					'linkType' => 'url',
					'emailtxt' => '',
					'iconNmlBG' => ['openBg' => 1,'bgDefaultColor' => "#127bb6",],
					'tooltipText' => 'LinkedIn',
				],
			],
		],
		'tglIcnType' => [
			'type' => 'string',
			'default' => 'icon',
		],
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-home',
		],
		'imageStore' => [
			'type' => 'object',
			'default' => [],
		],
		'imageSize' => [
			'type' => 'string',
			'default' => 'thumbnail',	
		],
		'tglStyle' => [
			'type' => 'string',
			'default' => 'style-1',
		],
		'ariaLabel' => [
			'type' => 'string',
			'default' => '',	
		],
		'iconPos' => [
			'type' => 'string',
			'default' => 'absolute',
		],
		'leftAuto' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'leftASize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '20',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'leftAuto', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ margin: 0 auto !important; margin-top: auto !important; left: {{leftASize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'leftAuto', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ left: auto; }',
				],
			],
		],
		'rightAuto' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'rightASize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '20',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'rightAuto', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ margin: 0 auto !important; margin-top: auto !important; right: {{rightASize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'rightAuto', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ right: auto; }',
				],
			],
		],
		'topAuto' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'topASize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '0',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'topAuto', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ margin-top: {{topASize}} !important; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'topAuto', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ top: auto; }',
				], 
			],
		],
		'bottomAuto' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'bottomASize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '0',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'bottomAuto', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ bottom: {{bottomASize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'bottomAuto', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-wrap{ bottom: auto; }',
				],
			],
		],
		'iconGap' => [
			'type' => 'string',
			'default' => '0',
			'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ]],
		],
		'openSpeed' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu.layout-straight .tpgb-circle-menu-wrap .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) { transition-duration: {{openSpeed}}ms; }',
				], 
			], 
		],
		'angleStart' => [
			'type' => 'string',
			'default' => '0',
		],
		'angleEnd' => [
			'type' => 'string',
			'default' => '90',
		],
		'circleRadius' => [
			'type' => 'object',
			'default' => [ 
				'md' => '150',
			],
		],
		'iconDelay' => [
			'type' => 'string',
			'default' => '1000',
		],
		'menuOSpeed' => [
			'type' => 'string',
			'default' => '500',
		],
		'icnStepIn' => [
			'type' => 'string',
			'default' => '-20',
		],
		'icnStepOut' => [
			'type' => 'string',
			'default' => '20',
		],
		'icnTrigger' => [
			'type' => 'string',
			'default' => 'hover', 
		],
		'icnTrans' => [
			'type' => 'string',
			'default' => 'ease',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu.layout-straight .tpgb-circle-menu-wrap .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) { transition-timing-function: {{icnTrans}}; }',
				], 
			], 
		],
		'icnSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon{ font-size: {{icnSize}}; }',
				],
			],
			'scopy' => true,
		],
		'icnWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'circle' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .tpgb-circle-menu-wrap .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) , {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon { width: {{icnWidth}} !important; height: {{icnWidth}} !important; line-height: {{icnWidth}} !important; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .tpgb-circle-menu-wrap .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) , {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon { width: {{icnWidth}} !important; height: {{icnWidth}} !important; line-height: {{icnWidth}} !important; }',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon img{ width: {{imgWidth}}; }',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .menu_icon',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon , {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon img{border-radius: {{icnNmlBRadius}};}',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .menu_icon , {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .menu_icon img{border-radius: {{icnHvrBRadius}};}',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .menu_icon',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => ' {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon{ font-size: {{tIcnSize}}; }',
				],
			],
			'scopy' => true,
		],
		'tIcnWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '40',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => ' {{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon{ width: {{tIcnWidth}}; height: {{tIcnWidth}}; line-height: {{tIcnWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'tImgWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon img{ width: {{tImgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'tIcnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon{ color: {{tIcnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tIcnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon{ color: {{tIcnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tIcnNmlBG' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnHvrBG' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnNmlBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnHvrBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnNmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon , .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon img{border-radius: {{tIcnNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'tIcnHvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon , .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon img{border-radius: {{tIcnHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'tIcnNmlShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list .main_menu_icon',
				],
			],
			'scopy' => true,
		],
		'tIcnHvrShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .tpgb-circle-menu-list:hover .main_menu_icon',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .tippy-box{width : {{tipMaxWidth}}px; max-width : {{tipMaxWidth}}px; }  ',
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
		'deskHide' => [
			'type' => 'boolean',
			'default' => false,	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) .tippy-box{display: none;}',
				],
			],
			'scopy' => true,
		],
		'tabHide' => [
			'type' => 'boolean',
			'default' => false,	
			'style' => [
				(object) [
					'selector' => '@media (min-width:768px) and (max-width:1024px){ {{PLUS_WRAP}} .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) .tippy-box{display: none;} }',
				],
			],
			'scopy' => true,
		],
		'mobHide' => [
			'type' => 'boolean',
			'default' => false,
			'style' => [
				(object) [
					'selector' => '@media (max-width:767px){ {{PLUS_WRAP}} .tpgb-circle-menu-list:not(.tpgb-circle-main-menu-list) .tippy-box{display: none;} }',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title',
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title{padding: {{textPadding}};}',
				],
			],
			'scopy' => true,
		],
		'textNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title{color: {{textNmlColor}};}',
				],
			],
			'scopy' => true,
		],
		'textHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .tpgb-circle-menu-list:hover .menu-tooltip-title{color: {{textHvrColor}};}',
				],
			],
			'scopy' => true,
		],
		'textNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'bgType' => 'color',
				'bgGradient' => (object) [],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		'textHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'bgType' => 'color',
				'bgGradient' => (object) [],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .tpgb-circle-menu-list:hover .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		'textNmlBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		'textHvrBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .tpgb-circle-menu-list:hover .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		'textNmlBR' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title{border-radius: {{textNmlBR}};}',
				],
			],
			'scopy' => true,
		],
		'textHvrBR' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .tpgb-circle-menu-list:hover .menu-tooltip-title{border-radius: {{textHvrBR}};}',
				],
			],
			'scopy' => true,
		],
		'textNmlBShadow' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		'textHvrBShadow' => [
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
					'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'straight' ], ['key' => 'menuStyle', 'relation' => '==', 'value' => 'style-2' ] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-circle-menu .menu-style-2 .tpgb-circle-menu-list:hover .menu-tooltip-title',
				],
			],
			'scopy' => true,
		],
		
		'tooltipTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tippy-box .tippy-content',
				],
			],
			'scopy' => true,
		],
		'tooltipColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tippy-box .tippy-content{ color: {{tooltipColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tipArrowColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'tipArrow', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tippy-arrow{color: {{tipArrowColor}};}',
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
					'selector' => '{{PLUS_WRAP}} .tippy-box{padding: {{tipPadding}};}',
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
					'selector' => '{{PLUS_WRAP}} .tippy-box',
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
					'selector' => '{{PLUS_WRAP}} .tippy-box{border-radius: {{tipBorderRadius}};}',
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
					'selector' => '{{PLUS_WRAP}} .tippy-box',
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
					'selector' => '{{PLUS_WRAP}} .tippy-box',
				],
			],
			'scopy' => true,
		],
		'scrollToggle' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'scrollValue' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'overlayColorTgl' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'overlayColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'overlayColorTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-circle-menu-inner-wrapper .show-bg-overlay.activebg{ background: {{overlayColor}}; }',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-circle-menu', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_circle_menu_render_callback'
    ) );
}
add_action( 'init', 'tpgb_circle_menu' );