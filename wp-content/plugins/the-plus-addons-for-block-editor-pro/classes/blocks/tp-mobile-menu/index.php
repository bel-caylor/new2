<?php
/* Block : Mobile Menu
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_mobile_menu_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$mmStyle  = (!empty($attributes['mmStyle'])) ? $attributes['mmStyle'] : 'style-1';
	$posType  = (!empty($attributes['posType'])) ? $attributes['posType'] : 'absolute';
	$openMenu  = (!empty($attributes['openMenu'])) ? $attributes['openMenu'] : '';
	$extraToggle  = (!empty($attributes['extraToggle'])) ? $attributes['extraToggle'] : false;
	$contentType  = (!empty($attributes['contentType'])) ? $attributes['contentType'] : 'link';
	$contentLink = (!empty($attributes['contentLink']['url'])) ? $attributes['contentLink']['url'] : '';
	$tglIconType  = (!empty($attributes['tglIconType'])) ? $attributes['tglIconType'] : 'icon';
	$iconStore  = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : '';
	$imageStore  = (!empty($attributes['imageStore'])) ? $attributes['imageStore'] : '';
	$imageSize  = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'thumbnail';
	$tglText  = (!empty($attributes['tglText'])) ? $attributes['tglText'] : '';
	$displayMode  = (!empty($attributes['displayMode'])) ? $attributes['displayMode'] : 'swiper';
	$fixPosType  = (!empty($attributes['fixPosType'])) ? $attributes['fixPosType'] : 'top';
	$menu1Item  = (!empty($attributes['menu1Item'])) ? $attributes['menu1Item'] : [];
	$menu2Item  = (!empty($attributes['menu2Item'])) ? $attributes['menu2Item'] : [];
	$tempList  = (!empty($attributes['tempList'])) ? $attributes['tempList'] : '';
	$pageIndicator  = (!empty($attributes['pageIndicator'])) ? $attributes['pageIndicator'] : false;
	$indiStyle  = (!empty($attributes['indiStyle'])) ? $attributes['indiStyle'] : 'line';
	$indiPos  = (!empty($attributes['indiPos'])) ? $attributes['indiPos'] : 'indi-top';
	
	$oCntntStyle  = (!empty($attributes['oCntntStyle'])) ? $attributes['oCntntStyle'] : 'style-1';
	$cntntWidth  = (!empty($attributes['cntntWidth'])) ? $attributes['cntntWidth'] : 'custom';
	$toggleDirection  = (!empty($attributes['toggleDirection'])) ? $attributes['toggleDirection'] : 'right';
	$cIconPos  = (!empty($attributes['cIconPos'])) ? $attributes['cIconPos'] : 'mm-ci-top-right';
	$tempOverflow  = (!empty($attributes['tempOverflow'])) ? $attributes['tempOverflow'] : 'tpgb-of-h';
	$scrollOffsetTgl  = (!empty($attributes['scrollOffsetTgl'])) ? $attributes['scrollOffsetTgl'] : false;
	$scrollTopValue  = (!empty($attributes['scrollTopValue'])) ? $attributes['scrollTopValue'] : '';
	
	//Responsive Hide
	$globalHideDesktop  = (!empty($attributes['globalHideDesktop'])) ? $attributes['globalHideDesktop'] : false;
	$globalHideTablet  = (!empty($attributes['globalHideTablet'])) ? $attributes['globalHideTablet'] : false;
	$globalHideMobile  = (!empty($attributes['globalHideMobile'])) ? $attributes['globalHideMobile'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$etClass = $position_class = $fixPosClass = $wrapper_main_class = $wrapper_class = $inner_class = $main_class = $inner_class_loop = '';
	
	//page indicator
	$indicateClass = '';
	if(!empty($pageIndicator) && $indiStyle=='line'){
		$indicateClass = $indiStyle." ".$indiPos ;
	} else if(!empty($pageIndicator) && $indiStyle=='dot'){
		$indicateClass = $indiStyle;
	}
	if($displayMode=='swiper'){			
		$wrapper_main_class = ' swiper-container swiper-free-mode';
		$wrapper_class = ' swiper-wrapper';
		$inner_class = ' swiper-slide swiper-slide-active';				
	}else if($displayMode=='columns'){
		$inner_class = ' tpgb-row';
		$main_class = ' tpgb-column-base';
		$inner_class_loop = ' grid-item tpgb-mm-eq-col';
	}	
	if(!empty($extraToggle)){
		$etClass = 'tpet-on';
	}
	if($posType == 'absolute'){
		$position_class = 'tpgb-mm-absolute';
	}else if($posType == 'fixed'){
		$position_class = 'tpgb-mm-fix';
		$fixPosClass = $fixPosType;
	}	
	
	if(!empty($imageStore) && !empty($imageStore['id'])){
		$mm_t_img = $imageStore['id'];
		$imgSrc = wp_get_attachment_image($mm_t_img , $imageSize, false, ['class' => 'tpgb-mm-img tpgb-mm-et-img tpgb-trans-easeinout']);
	}else if(!empty($imageStore['url'])){
		$imgSrc = '<img class="tpgb-mm-img tpgb-mm-et-img tpgb-trans-easeinout" src="'.esc_url($imageStore['url']).'" />';
	}else{
		$imgSrc = '';
	}
	
	$show_scroll_window_offset = (!empty($scrollOffsetTgl)) ? 'scroll-view' : '';
	$dataArr = [
		"ScrollVal"		=> (isset($scrollTopValue) && !empty($scrollOffsetTgl)) ? $scrollTopValue : '',
		"DeskTopHide"	=> $globalHideDesktop,
		"TabletHide"	=> $globalHideTablet,
		"MobileHide"	=> $globalHideMobile,
		"uid"		=> 'tpgb-block-'.$block_id,
	];
	$dataArr = htmlspecialchars(json_encode($dataArr), ENT_QUOTES, 'UTF-8');
	
	$css_rule='';
	if(!empty($openMenu)){
		$open_mobile_menu=($openMenu)."px";
		$close_mobile_menu=($openMenu+1)."px";
		
		$css_rule .='@media (min-width:'.esc_attr($close_mobile_menu).'){.tpgb-mobile-menu.tpgb-block-'.esc_attr($block_id).'{display:none;}}';
		
		$css_rule .='@media (max-width:'.esc_attr($open_mobile_menu).'){.tpgb-mobile-menu.tpgb-block-'.esc_attr($block_id).'{display:flex;}}';
	}
	$getmenu1 = $getmenu2 = $toggleLink = '';
	if(!empty($menu1Item)){
		foreach ( $menu1Item as $index => $item ) :
			$getmenu1 .= '<div class="tpgb-mm-li tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($inner_class_loop).' '.esc_attr($indicateClass).'">';
				$getmenu1 .= '<div class="tpgb-loop-inner tpgb-rel-flex">';
				$target = (!empty($item['linkUrl']['target'])) ? '_blank' : '';
				$nofollow = (!empty($item['linkUrl']['nofollow'])) ? 'nofollow' : '';
				$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['linkUrl']);
					$getmenu1 .= '<a class="tpgb-menu-link tp-mm-normal tpgb-rel-flex" href="'.esc_url($item['linkUrl']['url']).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.'>';
						if($item['iconType']=='icon'){
							$getmenu1 .= '<span class="tpgb-mm-icon">';
								$getmenu1 .= '<i class="'.esc_attr($item['iconStore']).'"></i>';
							$getmenu1 .= '</span>';
						}
						if($item['iconType']=='image' && !empty($item['imgStore'])){
							$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'thumbnail';
							if(!empty($item['imgStore']['id'])){
								$imgISrc = wp_get_attachment_image($item['imgStore']['id'] , $imageSize,false, ['class' => 'tpgb-mm-img tpgb-mm-st1-img']);
							}else if(!empty($item['imgStore']['url'])){
								$imgISrc = '<img class="tpgb-mm-img tpgb-mm-st1-img" src="'.esc_url($item['imgStore']['url']).'"/>';
							}
							$getmenu1 .= $imgISrc;
						}
						$getmenu1 .= '<span class="tpgb-mm-st1-title">'.esc_html($item['textVal']).'</span>';
					$getmenu1 .= '</a>';
					if(!empty($item['pinText'])){
						$getmenu1 .= '<span class="tpgb-menu-pintext">'.esc_html($item['pinText']).'</span>';
					}
				$getmenu1 .= '</div>';
			$getmenu1 .= '</div>';
			
			endforeach;
	}
	
	if(!empty($menu2Item)){
		foreach ( $menu2Item as $index => $item ) :
			$getmenu2 .= '<div class="tpgb-mm-li tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($inner_class_loop).' '.esc_attr($indicateClass).'">';
				$getmenu2 .= '<div class="tpgb-loop-inner tpgb-rel-flex">';
				$target = (!empty($item['linkUrl']['target'])) ? '_blank' : '';
				$nofollow = (!empty($item['linkUrl']['nofollow'])) ? 'nofollow' : '';
					$getmenu2 .= '<a class="tpgb-menu-link tp-mm-normal tpgb-rel-flex" href="'.esc_url($item['linkUrl']['url']).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'">';
						if($item['iconType']=='icon'){
							$getmenu2 .= '<span class="tpgb-mm-icon">';
								$getmenu2 .= '<i class="'.esc_attr($item['iconStore']).'"></i>';
							$getmenu2 .= '</span>';
						}
						if($item['iconType']=='image' && !empty($item['imgStore'])){
							$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'thumbnail';
							if(!empty($item['imgStore']['id'])){
								$imgISrc = wp_get_attachment_image($item['imgStore']['id'] , $imageSize, false, ['class' => 'tpgb-mm-img tpgb-mm-st1-img']);
							}else if(!empty($item['imgStore']['url'])){
								$imgISrc = '<img class="tpgb-mm-img tpgb-mm-st1-img" src="'.esc_url($item['imgStore']['url']).'"/>';
							}
							$getmenu2 .= $imgISrc;
						}
						$getmenu2 .= '<span class="tpgb-mm-st1-title">'.esc_html($item['textVal']).'</span>';
					$getmenu2 .= '</a>';
					if(!empty($item['pinText'])){
						$getmenu2 .= '<span class="tpgb-menu-pintext">'.esc_html($item['pinText']).'</span>';
					}
				$getmenu2 .= '</div>';
			$getmenu2 .= '</div>';
			
			endforeach;
	}

	$contentALink = '';
	if($contentType=='link' && !empty($contentLink)){
		$contentALink .= 'href="'.esc_url($contentLink).'" ';
		$contentALink .= Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['contentLink']);
	}
	
	$toggleLink .= '<a class="tpgb-menu-link tpgb-mm-et-link tpgb-rel-flex" '.$contentALink.'>';
		if($tglIconType=='icon'){
			$toggleLink .= '<span class="tpgb-mm-icon tpgb-trans-easeinout">';
				$toggleLink .= '<i aria-hidden="true" class="'.esc_attr($iconStore).'"></i>';
			$toggleLink .= '</span>';
		}
		if($tglIconType=='image'){
			$toggleLink .= $imgSrc;
		}
		$toggleLink .= '<span class="tpgb-mm-extra-toggle tpgb-trans-easeinout">'.esc_html($tglText).'</span>';
	$toggleLink .= '</a>';
	
	$fullwidthclass = '';
	if($cntntWidth=='fullwidth'){
		$fullwidthclass .='full-width-content';
	}
	$easeinoutC = 'tpgb-trans-easeinout tpgb-trans-easeinout-after tpgb-trans-easeinout-before';
	$toggleTemp = '';
	if(!empty($extraToggle) && $contentType=='template'){
		if($cIconPos=='mm-ci-auto'){
			$toggleTemp .= '<div class="extra-toggle-close-menu-auto '.esc_attr($easeinoutC).'"></div>';
		}
		$toggleTemp .= '<div class="header-extra-toggle-content mm-ett-'.esc_attr($oCntntStyle).' '.esc_attr($fullwidthclass).' '.esc_attr($toggleDirection).' '.esc_attr($tempOverflow).'">';
		if($oCntntStyle=='style-2'){
			$toggleTemp .= '<div class="tpgb-con-open-st2">';
		}
			$toggleTemp .= '<div class="extra-toggle-close-menu '.esc_attr($cIconPos).' '.esc_attr($easeinoutC).'"></div>';
			if(!empty($tempList) ){
				ob_start();
					if(!empty($tempList)) {
						echo Tpgb_Library()->plus_do_block($tempList);
					}
				$toggleTemp .= ob_get_contents();
				ob_end_clean();
			}
		if($oCntntStyle=='style-2'){
			$toggleTemp .= '</div>';
		}
		$toggleTemp .= '</div>';
		$toggleTemp .= '<div class="extra-toggle-content-overlay"></div>';
	}
	$output = '';
    $output .= '<div class="tpgb-mobile-menu tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($mmStyle).' '.esc_attr($etClass).' '.esc_attr($main_class).' '.esc_attr($position_class).' '.esc_attr($fixPosClass).' '.esc_attr($show_scroll_window_offset).'" data-mm-option= \'' .$dataArr. '\'>';
		if($mmStyle=='style-1'){
			$output .= '<div class="tpgb-mm-wrapper'.esc_attr($wrapper_main_class).'">';
				$output .= '<div class="tpgb-mm-wrapper-inner'.esc_attr($wrapper_class).'">';
					$output .= '<div class="tpgb-mm-ul'.esc_attr($inner_class).'">';
						$output .= $getmenu1;
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
			if(!empty($extraToggle)){
				$output .= '<div class="tpgb-mm-et-wrapper">';
					$output .= '<div class="tpgb-mm-et-ul">';
						$output .= '<div class="tpgb-mm-et-li">';
							$output .= '<div class="tpgb-loop-inner tpgb-rel-flex">';
								$output .= $toggleLink;
								$output .= $toggleTemp;
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}
		}
		if($mmStyle=='style-2'){
			$output .= '<div class="tpgb-mm-l-wrapper'.esc_attr($wrapper_main_class).'">';
				$output .= '<div class="tpgb-mm-l-wrapper-inner'.esc_attr($wrapper_class).'">';
					$output .= '<div class="tpgb-mm-l-ul'.esc_attr($inner_class).'">';
						$output .= $getmenu1;
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';	
			
			if(!empty($extraToggle)){
				$output .= '<div class="tpgb-mm-c-wrapper">';
					$output .= '<div class="tpgb-mm-c-et-ul">';
						$output .= '<div class="tpgb-mm-c-et-li">';
							$output .= '<div class="tpgb-loop-inner tpgb-rel-flex">';
								$output .= $toggleLink;
								$output .= $toggleTemp;
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			}
			$output .= '<div class="tpgb-mm-r-wrapper'.esc_attr($wrapper_main_class).'">';
				$output .= '<div class="tpgb-mm-r-wrapper-inner'.esc_attr($wrapper_class).'">';
					$output .= '<div class="tpgb-mm-r-ul'.esc_attr($inner_class).'">';
						$output .= $getmenu2;
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		}	
		$output .= '<style>'.$css_rule.'</style>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_mobile_menu() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();

	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'mmStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'posType' => [
			'type' => 'string',
			'default' => 'absolute',	
		],
		'fixPosType' => [
			'type' => 'string',
			'default' => 'top',	
		],
		'openMenu' => [
			'type' => 'string',
			'default' => '',
		],
		'menu1Item' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'textVal' => [
						'type' => 'string',
						'default' => 'Menu'
					],
					'iconType' => [
						'type' => 'string',
						'default' => 'icon',
					],
					'iconStore' => [
						'type'=> 'string',
						'default'=> 'far fa-calendar-alt',
					],
					'imgStore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'thumbnail',	
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
					],
					'pinText' => [
						'type' => 'string',
						'default' => 'New'
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'textVal' => 'Home',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-home',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
				[
					'_key' => '1',
					'textVal' => 'About',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-users',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
				[
					'_key' => '2',
					'textVal' => 'Contact',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-address-card',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
				[
					'_key' => '3',
					'textVal' => 'Offers',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-barcode',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
				[
					'_key' => '4',
					'textVal' => 'Support',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-ticket-alt',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
			],
		],
		'menu2Item' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'textVal' => [
						'type' => 'string',
						'default' => 'Menu'
					],
					'iconType' => [
						'type' => 'string',
						'default' => 'icon',
					],
					'iconStore' => [
						'type'=> 'string',
						'default'=> 'far fa-calendar-alt',
					],
					'imgStore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'thumbnail',	
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
					],
					'pinText' => [
						'type' => 'string',
						'default' => 'New'
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'textVal' => 'Offer',
					'iconType' => 'icon',
					'iconStore' => 'fas fa-home',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
				[
					'_key' => '1',
					'textVal' => 'Contact',
					'iconType' => 'icon',
					'iconStore' => 'far fa-calendar-alt',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
			],
		],
		'extraToggle' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'tglText' => [
			'type' => 'string',
			'default' => 'Home',	
		],
		'tglIconType' => [
			'type' => 'string',
			'default' => 'icon',	
		],
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-home',
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
		'contentType' => [
			'type' => 'string',
			'default' => 'link',	
		],
		'contentLink' => [
			'type'=> 'object',
			'default'=> [
				'url' => '',
				'target' => '',
				'nofollow' => ''
			],
		],
		'tempList' => [
			'type' => 'string',
			'default' => ''
		],
		'backendVisi' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'toggleDirection' => [
			'type' => 'string',
			'default' => 'right'
		],
		'oCntntStyle' => [
			'type' => 'string',
			'default' => 'style-1'
		],
		'cntntWidth' => [
			'type' => 'string',
			'default' => 'custom'
		],
		'fullWMargin' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cntntWidth', 'relation' => '==', 'value' => 'fullwidth' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content.open { width:calc(100% - {{fullWMargin}});height:calc(100% -  {{fullWMargin}}); max-width:calc(100% - {{fullWMargin}});max-height:calc(100% - {{fullWMargin}}); align-items: center;justify-content: center;vertical-align: middle;right: 0;left: 0;margin: 0 auto;top: 50%;transform: translateY(-50%);} {{PLUS_WRAP}}.tpgb-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content { transition: all 0.3s ease-in-out; }',
				],
			],
			'scopy' => true,
		],
		'customWH' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '400',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'cntntWidth', 'relation' => '==', 'value' => 'custom' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.left , {{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.right { max-width: {{customWH}}; }  {{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.top , {{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.bottom{ max-height: {{customWH}}; }',
				],
			],
			'scopy' => true,
		],
		'displayMode' => [
			'type' => 'string',
			'default' => 'columns',
		],
		'pageIndicator' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'indiStyle' => [
			'type' => 'string',
			'default' => 'line'
		],
		'indiPos' => [
			'type' => 'string',
			'default' => 'indi-top'
		],
		'indiOffset' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '50',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ] , ['key' => 'indiStyle', 'relation' => '==', 'value' => 'dot' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-li.dot.active .tpgb-menu-link:after { bottom: {{indiOffset}}; }',
				],
			],
			'scopy' => true,
		],
		'pinOverflow' => [
			'type' => 'string',
			'default' => 'hidden',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu.style-2 .tpgb-mm-l-wrapper .tpgb-mm-li, {{PLUS_WRAP}}.tpgb-mobile-menu.style-2 .tpgb-mm-r-wrapper .tpgb-mm-li, {{PLUS_WRAP}}.tpgb-mobile-menu.style-2 .tpgb-mm-c-wrapper .tpgb-mm-c-et-li{ visible: {{pinOverflow}}; }',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-li .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-icon{ font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-li .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-icon{ color: {{iconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-li.active .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-icon{ color: {{iconAColor}}; }',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img{ width: {{imgSize}}; }',
				],
			],
			'scopy' => true,
		],
		'imgNBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img',
				],
			],
			'scopy' => true,
		],
		'imgABdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner .tpgb-mm-img',
				],
			],
			'scopy' => true,
		],
		'imgNBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img{border-radius: {{imgNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'imgABRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner .tpgb-mm-img{border-radius: {{imgABRadius}};}',
				],
			],
			'scopy' => true,
		],
		'imgNBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img',
				],
			],
			'scopy' => true,
		],
		'imgABShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner .tpgb-mm-img',
				],
			],
			'scopy' => true,
		],
		'etIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-menu-link .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-icon{ font-size: {{etIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'etIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-menu-link .tpgb-mm-icon,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-icon{ color: {{etIconColor}}; }',
				],
			],
			'scopy' => true,
		],
		'etImgSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img.tpgb-mm-et-img{ width: {{etImgSize}}; }',
				],
			],
			'scopy' => true,
		],
		'etImgBdr' => [
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
					'condition' => [(object) ['key' => 'tglIconType', 'relation' => '==', 'value' => 'image' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img.tpgb-mm-et-img',
				],
			],
			'scopy' => true,
		],
		'etImgBRadius' => [
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
					'condition' => [(object) ['key' => 'tglIconType', 'relation' => '==', 'value' => 'image' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img.tpgb-mm-et-img{border-radius: {{etImgBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'etImgBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-mm-img.tpgb-mm-et-img',
				],
			],
			'scopy' => true,
		],
		'indicateWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ], ['key' => 'indiStyle', 'relation' => '==', 'value' => 'line' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active:before, {{PLUS_WRAP}} .tpgb-mm-li.active:after{ width: {{indicateWidth}} !important; }',
				],
			],
			'scopy' => true,
		],
		'indicateHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ], ['key' => 'indiStyle', 'relation' => '==', 'value' => 'line' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active:before, {{PLUS_WRAP}} .tpgb-mm-li.active:after{ border: {{indicateHeight}} solid !important; }',
				],
			],
			'scopy' => true,
		],
		'indicateColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ], ['key' => 'indiStyle', 'relation' => '==', 'value' => 'line' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active:before, {{PLUS_WRAP}} .tpgb-mm-li.active:after{ border-color: {{indicateColor}} !important; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ], ['key' => 'indiStyle', 'relation' => '==', 'value' => 'dot' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.dot.active .tpgb-menu-link:after{ background: {{indicateColor}} !important; }',
				],
			],
			'scopy' => true,
		],
		'indiDotSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'pageIndicator', 'relation' => '==', 'value' => true ], ['key' => 'indiStyle', 'relation' => '==', 'value' => 'dot' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.dot.active .tpgb-menu-link:after{ width: {{indiDotSize}}; height: {{indiDotSize}}; }',
				],
			],
			'scopy' => true,
		],
		'menuPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-wrapper .tpgb-mm-wrapper-inner .tpgb-mm-li, {{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-ul .tpgb-mm-et-li, {{PLUS_WRAP}} .tpgb-mm-l-wrapper .tpgb-mm-li, {{PLUS_WRAP}} .tpgb-mm-r-wrapper .tpgb-mm-li, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-mm-li, {{PLUS_WRAP}}.style-2 .tpgb-mm-c-wrapper .tpgb-mm-c-et-li{padding: {{menuPadding}};}',
				],
			],
			'scopy' => true,
		],
		'menuWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayMode', 'relation' => '==', 'value' => 'swiper' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-wrapper .tpgb-mm-wrapper-inner .tpgb-mm-li,{{PLUS_WRAP}} .tpgb-mm-l-wrapper .tpgb-mm-li,{{PLUS_WRAP}} .tpgb-mm-r-wrapper .tpgb-mm-li{ max-width: {{menuWidth}}; min-width: {{menuWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'menuHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'displayMode', 'relation' => '==', 'value' => 'swiper' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-wrapper .tpgb-mm-wrapper-inner .tpgb-mm-li,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-ul .tpgb-mm-et-li,{{PLUS_WRAP}} .tpgb-mm-l-wrapper .tpgb-mm-li,{{PLUS_WRAP}} .tpgb-mm-r-wrapper .tpgb-mm-li,{{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-mm-li, {{PLUS_WRAP}}.style-2 .tpgb-mm-c-wrapper .tpgb-mm-c-et-li{ max-height: {{menuHeight}}; min-height: {{menuHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'menuTexTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-wrapper .tpgb-mm-wrapper-inner .tpgb-mm-li .tpgb-mm-st1-title, {{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-extra-toggle, {{PLUS_WRAP}}.tpgb-mobile-menu.style-2 .tpgb-mm-st1-title, {{PLUS_WRAP}}.tpgb-mobile-menu.style-2 .tpgb-mm-extra-toggle',
				],
			],
			'scopy' => true,
		],
		'titleNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-wrapper .tpgb-mm-wrapper-inner .tpgb-mm-li .tpgb-mm-st1-title,{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-extra-toggle,{{PLUS_WRAP}}.style-2 .tpgb-mm-st1-title, {{PLUS_WRAP}}.style-2 .tpgb-mm-extra-toggle{ color: {{titleNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'etTitleColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-et-wrapper .tpgb-mm-et-li .tpgb-mm-extra-toggle, {{PLUS_WRAP}}.style-2 .tpgb-mm-extra-toggle{ color: {{etTitleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'menuBG' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'titleAColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-mm-st1-title{ color: {{titleAColor}}; }',
				],
			],
			'scopy' => true,
		],
		'menuActBG' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner',
				],
			],
			'scopy' => true,
		],
		'menuNmlBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'menuActBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner',
				],
			],
			'scopy' => true,
		],
		'menuNBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper {border-radius: {{menuNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'menuABRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner {border-radius: {{menuABRadius}};}',
				],
			],
			'scopy' => true,
		],
		'menuNBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'menuABShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-li.active .tpgb-loop-inner',
				],
			],
			'scopy' => true,
		],
		'etEqualToggle' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'etWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ],['key' => 'etEqualToggle', 'relation' => '!=', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.style-1.tpet-on .tpgb-mm-et-wrapper,{{PLUS_WRAP}}.style-2.tpet-on .tpgb-mm-c-wrapper{ width: {{etWidth}}; }  {{PLUS_WRAP}}.style-2.tpet-on .tpgb-mm-l-wrapper, {{PLUS_WRAP}}.style-2.tpet-on .tpgb-mm-r-wrapper{ width: calc((100% - {{etWidth}})/2); } {{PLUS_WRAP}}.style-2.tpet-on .tpgb-mm-l-wrapper, {{PLUS_WRAP}}.style-1.tpet-on .tpgb-mm-wrapper{ width: calc((100% - {{etWidth}})); }',
				],
			],
			'scopy' => true,
		],
		'etSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ],['key' => 'etEqualToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper{ width: {{etSize}} !important; max-width: {{etSize}} !important; min-width: {{etSize}} !important; height: {{etSize}} !important; max-height: {{etSize}} !important; min-height: {{etSize}} !important; }',
				],
			],
			'scopy' => true,
		],
		'etOffset' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-et-wrapper, {{PLUS_WRAP}}.tpgb-mobile-menu .tpgb-mm-c-wrapper{ margin-top: {{etOffset}} !important; }',
				],
			],
			'scopy' => true,
		],
		'etBG' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'etBdr' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'etBRadius' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper {border-radius: {{etBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'etBShadow' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-mm-c-wrapper, {{PLUS_WRAP}} .tpgb-mm-c-wrapper .tpgb-loop-inner, {{PLUS_WRAP}} .tpgb-mm-et-wrapper',
				],
			],
			'scopy' => true,
		],
		'tempOverflow' => [
			'type' => 'string',
			'default' => 'tpgb-of-h',	
			'scopy' => true,
		],
		'tempPadding' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ], ['key' => 'contentType', 'relation' => '==', 'value' => 'template' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content{padding: {{tempPadding}};}',
				],
			],
			'scopy' => true,
		],
		'tempBG' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ], ['key' => 'contentType', 'relation' => '==', 'value' => 'template' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.mm-ett-style-1, {{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.mm-ett-style-2:after',
				],
			],
			'scopy' => true,
		],
		'tempBdr' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ], ['key' => 'contentType', 'relation' => '==', 'value' => 'template' ], ['key' => 'cntntWidth', 'relation' => '==', 'value' => 'fullwidth' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.full-width-content.open',
				],
			],
			'scopy' => true,
		],
		'tempBRadius' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ], ['key' => 'contentType', 'relation' => '==', 'value' => 'template' ], ['key' => 'cntntWidth', 'relation' => '==', 'value' => 'fullwidth' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.full-width-content.open {border-radius: {{tempBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'tempBShadow' => [
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
					'condition' => [(object) ['key' => 'extraToggle', 'relation' => '==', 'value' => true ], ['key' => 'contentType', 'relation' => '==', 'value' => 'template' ], ['key' => 'cntntWidth', 'relation' => '==', 'value' => 'fullwidth' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.full-width-content.open',
				],
			],
			'scopy' => true,
		],
		'cIconPos' => [
			'type' => 'string',
			'default' => 'mm-ci-top-right',
			'scopy' => true,
		],
		'cIconNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:before,{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:after, {{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:before, {{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:after{ background: {{cIconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:hover:before,{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:hover:after, {{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:hover:before, {{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:hover:after{ background: {{cIconHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconNBG' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu',
				],
			],
			'scopy' => true,
		],
		'cIconHBG' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:hover',
				],
			],
			'scopy' => true,
		],
		'cIconNBRadius' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .header-extra-toggle-content.full-width-content.open {border-radius: {{cIconNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cIconHBRadius' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:hover {border-radius: {{cIconHBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cIconNBShadow' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu',
				],
			],
			'scopy' => true,
		],
		'cIconHBShadow' => [
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
					'condition' => [(object) ['key' => 'cIconPos', 'relation' => '!=', 'value' => 'mm-ci-auto' ]],
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-close-menu:hover',
				],
			],
			'scopy' => true,
		],
		'overlayBG' => [
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
					'selector' => '{{PLUS_WRAP}}.tpet-on .extra-toggle-content-overlay',
				],
			],
			'scopy' => true,
		],
		'cntntPadding' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu{padding: {{cntntPadding}};}',
				],
			],
			'scopy' => true,
		],
		'cntntBG' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu',
				],
			],
			'scopy' => true,
		],
		'cntntBdr' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu',
				],
			],
			'scopy' => true,
		],
		'cntntBRadius' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu {border-radius: {{cntntBRadius}}; overflow: hidden;}',
				],
			],
			'scopy' => true,
		],
		'cntntBShadow' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu',
				],
			],
			'scopy' => true,
		],
		'cntntOverflow' => [
			'type' => 'string',
			'default' => 'hidden',	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-mobile-menu {overflow: {{cntntOverflow}} !important;}',
				],
			],
			'scopy' => true,
		],
		'pinPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{padding: {{pinPadding}};}',
				],
			],
			'scopy' => true,
		],
		'pinTopOffset' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{ top: {{pinTopOffset}}; }',
				],
			],
			'scopy' => true,
		],
		'pinRightOffset' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{ right: {{pinRightOffset}}; }',
				],
			],
			'scopy' => true,
		],
		'pinTextSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{ font-size: {{pinTextSize}}; }',
				],
			],
			'scopy' => true,
		],
		'pinTextColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{ color: {{pinTextColor}}; }',
				],
			],
			'scopy' => true,
		],
		'pinTextBG' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext',
				],
			],
			'scopy' => true,
		],
		'pinTextBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext',
				],
			],
			'scopy' => true,
		],
		'pinTextBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext{border-radius: {{pinTextBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'pinTextBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loop-inner .tpgb-menu-pintext',
				],
			],
			'scopy' => true,
		],
		'scrollOffsetTgl' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'scrollTopValue' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);

	register_block_type( 'tpgb/tp-mobile-menu', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_mobile_menu_render_callback'
    ) );
}
add_action( 'init', 'tpgb_mobile_menu' );