<?php
/**
 * Block : Scroll Navigation
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_scroll_navigation_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$menuList = (!empty($attributes['menuList'])) ? $attributes['menuList'] : [];
	$styletype = (!empty($attributes['styletype'])) ? $attributes['styletype'] : '';
	$navdire = (!empty($attributes['navdire'])) ? $attributes['navdire'] : '';
	$navposi = (!empty($attributes['navposi'])) ? $attributes['navposi'] : '';
	$disCounter = (!empty($attributes['disCounter'])) ? $attributes['disCounter'] : false;
   	$countersty = (!empty($attributes['countersty'])) ? $attributes['countersty'] : '';
	$tooltipsty = (!empty($attributes['tooltipsty'])) ? $attributes['tooltipsty'] : '';
	$totipAlign = (!empty($attributes['totipAlign'])) ? $attributes['totipAlign'] : '';
	$tooltiparrow = (!empty($attributes['tooltiparrow'])) ? $attributes['tooltiparrow'] : false;
	$scrolloff = (!empty($attributes['scrolloff'])) ? $attributes['scrolloff'] : false;
	$sTopoffset = (!empty($attributes['sTopoffset'])) ? $attributes['sTopoffset'] : '';
	$carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$i = 0;
	//Set Id Connection 
	$dataAttr='';
	if(!empty($carouselId)){
		$dataAttr .= ' id="tptab_'.esc_attr($carouselId).'" ';
		$dataAttr .= ' data-tab-id="tptab_'.esc_attr($carouselId).'" ' ;
		$dataAttr .= ' data-connection="tpca-'.esc_attr($carouselId).'" ';
	}

    //Get Navigation
	if(!empty($menuList)){ 
		$nav = '';

		foreach ( $menuList as $index => $item ) : 
			$i++;
			//Get Icon
			$icons = '';
			if(!empty($item['icon']) && $item['iconName'] != '' ){
				$icons .= '<i class="'.esc_attr($item['iconName']).' tooltip-icon"> </i>';
			}else{
				$icons .= '<i class="tooltip-icon fas fa-home"> </i>';
			}
			$ariaLabelT = (!empty($item['ariaLabel'])) ? esc_attr($item['ariaLabel']) : esc_attr__('Navigation Button', 'tpgbp');
			$nav .= '<a id="scroll121" href="#'.esc_attr($item['secId']).'" class="tpgb-scroll-nav-item" data-tab="'.esc_attr($index).'" aria-label="'.$ariaLabelT.'">';
				$nav .= '<div class="tpgb-scroll-nav-dots '.(!empty($disCounter) && $countersty != '' ? esc_attr($countersty)  :'' ).'">';
					if($styletype == 'style-5'){
						$nav .= $icons;
					}
					if(!empty($item['tooltip']) && $item['tooltiptxt'] != ''){
						$nav .= '<span class="tooltip-title nav-'.esc_attr($navdire).' '.esc_attr($tooltipsty).' '.esc_attr($totipAlign).' '.(!empty($tooltiparrow) ? 'tooltip-arrow' : '') .' ">';
							if($styletype != 'style-5' && !empty($item['icon'])){
								$nav .= $icons;
							}
							$nav .= wp_kses_post($item['tooltiptxt']);
						$nav .= '</span>';
					}
				$nav .= "</div>";
			$nav .= '</a>';
		endforeach;
	}

	$output .= '<div class="tpgb-scroll-nav tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($styletype).' nav-'.esc_attr($navdire).' '.($navdire == 'top' || $navdire == 'bottom' ? esc_attr($navposi)  : ''  ).' '.(!empty($scrolloff) ? 'scroll-view' :'').'" data-scroll-view="'.((!empty($scrolloff) && $sTopoffset != '') ? esc_attr( $sTopoffset ) : '' ).'" >';
		$output .= '<div class="tpgb-scroll-nav-inner" '.$dataAttr.'>';
			$output .= $nav;
		$output .= "</div>";
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_scroll_navigation() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'menuList' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'secId' => [
							'type' => 'string',
							'default' => ''
						],
						'tooltip' =>[
							'type' => 'boolean',
							'default' => false,	
						],
						'tooltiptxt' => [
							'type' => 'string',
							'default' => ''
						],
						'icon' => [
							'type' => 'boolean',
							'default' => false
						],
						'iconName' => [
							'type' => 'string',
							'default' => 'fas fa-home'
						],
						'ariaLabel' => [
							'type' => 'string',
							'default' => '',	
						],
					]
				],
				'default' => [
					[
						"_key" => '0',
						'secId' => 'Id1',
						'tooltip' => false,
						'tooltitxt' => '',
						'icon' => false,
						'iconName' => 'fas fa-home',
						'ariaLabel' => '',
					],
				],
			],
			'styletype' => [
				'type' => 'string',
				'default' => 'style-1'
			],
			'navdire' => [
				'type' => 'string',
				'default' => 'left'
			],
			'carouselId' => [
				'type' => 'string',
				'default' => ''
			],
			'navposi' => [
				'type' => 'string',
				'default' => 'center'
			],
			'tooltipsty' => [
				'type' => 'string',
				'default' => 'on-hover'
			],
			'tooltiparrow' => [
				'type' => 'boolean',
				'default' => true
			],
			'disCounter' => [
				'type' => 'boolean',
				'default' => false
			],
			'countersty' => [
				'type' => 'string',
				'default' => 'number-normal'
			],
			'iconHW' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => ['style-1','style-2','style-3']]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav a.tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav a.tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots:before { width: {{iconHW}}; height: {{iconHW}}; line-height: {{iconHW}}; }  {{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner{ min-width: {{iconHW}} }',
					]
				],
				'scopy' => true,
			],
			'iconSpacing' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'styletype', 'relation' => '!=', 'value' => ['style-4','style-2'] ],
							['key' => 'navdire', 'relation' => '==', 'value' => ['top','bottom']]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.nav-top a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-bottom a.tpgb-scroll-nav-item{ margin-right: {{iconSpacing}}; margin-left:{{iconSpacing}}  }'
					],
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => ['style-2','style-4']]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 a.tpgb-scroll-nav-item{ margin-top: {{iconSpacing}}; margin-bottom:{{iconSpacing}}  }'
					],
					(object) [
						'condition' => [
							(object) ['key' => 'styletype', 'relation' => '!=', 'value' => ['style-4','style-2'] ],
							['key' => 'navdire', 'relation' => '==', 'value' => ['left','right','top_left','top_right','bottom_left','bottom_right']]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.nav-left a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-right a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-top_left a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-top_right a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-bottom_left a.tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.nav-bottom_right a.tpgb-scroll-nav-item{ margin-top: {{iconSpacing}}; margin-bottom:{{iconSpacing}}  }'
					],
				],
				'scopy' => true,
			],
			'iconSize' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-5']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-dots{font-size : {{iconSize}};} {{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-dots{line-height : {{iconSize}};}  {{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-inner{min-width: {{iconSize}};}',
					]
				],
				'scopy' => true,
			],
			'navColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots{background-color: {{navColor}};}  {{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-dots .tooltip-icon{color : {{navColor}};} ',
					]
				],
				'scopy' => true,
			],
			'navBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '!=', 'value' => 'style-5']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots',
					]
				],
				'scopy' => true,
			],
			'navHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:hover:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots{background-color: {{navHvrColor}};}  {{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-dots:hover .tooltip-icon,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots .tooltip-icon{color : {{navHvrColor}};} ',
					]
				],
				'scopy' => true,
			],
			'navHvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '!=', 'value' => 'style-5']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:hover:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots:before,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots',
					]
				],
				'scopy' => true,
			],
			'navBshadow' => [
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
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:before',
					],
				],
				'scopy' => true,
			],
			'navBHvshadow' => [
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
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-dots:hover:before',
					],
				],
				'scopy' => true,
			],
			'lineWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots{width : {{lineWidth}}px;}',
					]
				],
				'scopy' => true,
			],
			'lineheight' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-dots{height : {{lineheight}}px;}',
					]
				],
				'scopy' => true,
			],
			'HvrlineWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots{width : {{HvrlineWidth}}px;}',
					]
				],
				'scopy' => true,
			],
			'navBg' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'navBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item ',
					]
				],
				'scopy' => true,
			],
			'navngBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item ',
					]
				],
				'scopy' => true,
			],
			'navBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item ,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item{border-radius: {{navBradius}}}',
					],
				],
				'scopy' => true,
			],
			'navHvrBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots ',
					]
				],
				'scopy' => true,
			],
			'navHbgBorder' => [
				'type' => 'object',	
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots ',
					]
				],
				'scopy' => true,
			],
			'HvrBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item:hover ,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item:hover,{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item.active,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item.active .tpgb-scroll-nav-dots{border-radius: {{HvrBradius}}}',
					],
				],
				'scopy' => true,
			],
			'navBgshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navBg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-1 .tpgb-scroll-nav-item ,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-3 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item',
					],
				],
				'scopy' => true,
			],
			'navInpadding' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'navBg', 'relation' => '==', 'value' => true ],
							['key' => 'styletype', 'relation' => '==', 'value' => ['style-4','style-2','style-5']]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4 .tpgb-scroll-nav-item,{{PLUS_WRAP}}.tpgb-scroll-nav.style-5 .tpgb-scroll-nav-item{padding : {{navInpadding}};}',
					],
				],
				'scopy' => true,
			],
			'totipmargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ margin: {{totipmargin}}; }',
					],
				],
				'scopy' => true,
			],
			'totippadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ padding: {{totippadding}}; }',
					],
				],
				'scopy' => true,
			],
			'totipAlign' => [
				'type' => 'string',
				'default' => 'text-center',
				'scopy' => true,
			],
			'totipTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title',
					]
				],
				'scopy' => true,
			],
			'totipColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ color: {{totipColor}}; }',
					],
				],
				'scopy' => true,
			],
			'totipHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title:hover{ color: {{totipHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'totipBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ background-color: {{totipBgcolor}}; } {{PLUS_WRAP}} .tpgb-scroll-nav-item .tpgb-scroll-nav-dots span.tooltip-title:after{ border-right-color: {{totipBgcolor}} }',
					],
				],
				'scopy' => true,
			],
			'totipHBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title:hover{ background-color: {{totipHBgcolor}}; } {{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title:hover:after{ border-right-color: {{totipHBgcolor}} }',
					],
				],
				'scopy' => true,
			],
			'totipHgh' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ height: {{totipHgh}}px; }',
					],
				],
				'scopy' => true,
			],
			'totipBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title',
					]
				],
				'scopy' => true,
			],
			'tipBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-scroll-nav-dots span.tooltip-title{ border-radius: {{tipBradius}} }',
					]
				],
				'scopy' => true,
			],
			'coumargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ margin: {{coumargin}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ margin: {{coumargin}}; }',
					],
				],
				'scopy' => true,
			],
			'counSize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ font-size: {{counSize}}px; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ font-size: {{counSize}}px; }',
					],
				],
				'scopy' => true,
			],
			'counColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}} .tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-2.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ color : {{counColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styletype', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}} .tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-right .tpgb-scroll-nav-dots.lower-greek:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.number-normal:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.decimal-leading-zero:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-alpha:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.upper-roman:after,{{PLUS_WRAP}}.tpgb-scroll-nav.style-4.nav-left .tpgb-scroll-nav-dots.lower-greek:after{ color : {{counColor}}; }',
					],	
				],
				'scopy' => true,
			],
			'snavoffset' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav{ margin : {{snavoffset}} }',
					]
				],
				'scopy' => true,
			],
			'snavpadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner{ padding : {{snavpadding}} }',
					]
				],
				'scopy' => true,
			],
			'snavbg' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'snavbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'snavbg', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner ',
					]
				],
				'scopy' => true,
			],
			'snavBor' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'snavBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'snavBor', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner ',
					]
				],
				'scopy' => true,
			],
			'snavBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'snavBor', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner{border-radius: {{snavBradius}}} ',
					]
				],
				'scopy' => true,
			],
			'snavBshadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-scroll-nav .tpgb-scroll-nav-inner ',
					],
				],
				'scopy' => true,
			],
			'scrolloff' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'sTopoffset' => [
				'type' => 'string',
				'default' => 0,
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-scroll-navigation', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_scroll_navigation_render_callback'
    ) );
}
add_action( 'init', 'tpgb_scroll_navigation' );