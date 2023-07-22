<?php
/* Block : Table Of Content
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_table_content_render_callback( $attr, $content) {
	$output = '';
    $block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
    $Style = (!empty($attr['Style'])) ? $attr['Style'] : 'none';
    $ToggleIcon = (!empty($attr['ToggleIcon'])) ? $attr['ToggleIcon'] : false;
    $TableDescText = (!empty($attr['TableDescText'])) ? $attr['TableDescText'] : '';
    $openIcon = (!empty($attr['openIcon'])) ? $attr['openIcon'] : '';
    $closeIcon = (!empty($attr['closeIcon'])) ? $attr['closeIcon'] : '';
    $DefaultToggle = (!empty($attr['DefaultToggle'])) ? $attr['DefaultToggle'] : ['md' => true, 'sm' => true, 'xs' => false];
	$totitleAlign = (!empty($attr['totitleAlign'])) ? $attr['totitleAlign'] : ['md' => '', 'sm' => '', 'xs' => ''];
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );

	$selectorHeading ='';
		$selectorHeading .= (!empty($attr['selectorH1'])) ? 'h1' : '';
		$selectorHeading .= (!empty($attr['selectorH2'])) ? ($selectorHeading) ? ',h2' : 'h2' : '';
		$selectorHeading .= (!empty($attr['selectorH3'])) ? ($selectorHeading) ? ',h3' : 'h3' : '';
		$selectorHeading .= (!empty($attr['selectorH4'])) ? ($selectorHeading) ? ',h4' : 'h4' : '';
		$selectorHeading .= (!empty($attr['selectorH5'])) ? ($selectorHeading) ? ',h5' : 'h5' : '';
		$selectorHeading .= (!empty($attr['selectorH6'])) ? ($selectorHeading) ? ',h6' : 'h6' : '';
	$settings = [];
	
	$settings['tocSelector'] = '.tpgb-toc';
	$settings['contentSelector'] = (!empty($attr['contentSelector'])) ? $attr['contentSelector'] : '#content';
	$settings['headingSelector'] = $selectorHeading;
	$settings['isCollapsedClass'] = (!empty($attr['ChildToggle'])) ? ' is-collapsed' : '';
	$settings['headingsOffset'] = (!empty($attr['headingsOffset'])) ? (int)$attr['headingsOffset'] : 1;
	
	$settings['scrollSmooth'] = (!empty($attr['smoothScroll'])) ? true : false;
	$settings['scrollSmoothDuration'] = (!empty($attr['smoothDuration'])) ? (int)$attr['smoothDuration'] : 420;
	$settings['scrollSmoothOffset'] = (!empty($attr['scrollOffset'])) ? (int)$attr['scrollOffset'] : 0;
	
	$settings['orderedList'] = (!empty($attr['typeList']) && $attr['typeList']==='OL') ? true : false;
	$settings['positionFixedSelector'] = (!empty($attr['fixedPosition'])) ? '.tpgb-table-content' : null;
	$settings['fixedSidebarOffset'] = (!empty($attr['fixedPosition']) && !empty($attr['fixedOffset'])) ? (int)$attr['fixedOffset'] : 'auto';
	
	$settings['hasInnerContainers'] = true;
	
	$toggleClass='';
	$toggleAttr ='';
	if(!empty($ToggleIcon)){
		$toggleClass = 'table-toggle-wrap';
		$toggleAttr .= ' data-open="'.esc_attr($openIcon).'"';
		$toggleAttr .= ' data-close="'.esc_attr($closeIcon).'"';
		$toggleAttr .= ' data-default-toggle="'.htmlspecialchars(json_encode($DefaultToggle), ENT_QUOTES, 'UTF-8').'"';
	}
	
	$toggleActive=' active';

	// Alignment Css
	$style_atts = '';
	if(( isset($totitleAlign['md']) && !empty($totitleAlign['md']) && $totitleAlign[ 'md'] == 'right' )) {
		$style_atts .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto;} } ';
	}
	if( isset($totitleAlign['sm']) && !empty($totitleAlign['sm']) && $totitleAlign['sm'] == 'right'){
		$style_atts .= '@media (max-width: 1024px) and (min-width:768px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto} } ';
	}
	if( isset($totitleAlign['xs']) && !empty($totitleAlign['xs']) && $totitleAlign['xs'] == 'right'){
		$style_atts .= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto} } ';
	}
	if(( isset($totitleAlign['md']) && !empty($totitleAlign['md']) && $totitleAlign['md'] == 'center' )){
		$style_atts .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto; margin-right: auto;} }';
	}
	if( isset($totitleAlign['sm']) && !empty($totitleAlign['sm']) && $totitleAlign['sm'] == 'center' ) {
		$style_atts .= '@media (max-width: 1024px) and (min-width:768px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto;margin-right: auto;} } ';
	}
	if( isset($totitleAlign['xs']) && !empty($totitleAlign['xs']) && $totitleAlign['xs'] == 'center' ) {
		$style_atts .= '@media (max-width: 767px){ .tpgb-block-'.esc_attr($block_id).'.tpgb-table-content{margin-left: auto; margin-right: auto;} }';
	}

    $output .= '<div class="tpgb-table-content tpgb-block-'.esc_attr($block_id).' table-'.esc_attr($Style).' '.esc_attr($blockClass).'" data-settings="'.htmlspecialchars(json_encode($settings), ENT_QUOTES, 'UTF-8').'" >';
		$output .= '<div class="tpgb-toc-wrap '.esc_attr($toggleClass).esc_attr($toggleActive).'" '.$toggleAttr.'>';
			if( !empty($attr['showText']) && !empty($attr['contentText']) ) {
				$table_desc='';
				if(!empty($TableDescText)){
					$table_desc= '<div class="tpgb-table-desc tpgb-trans-linear">'.wp_kses_post($TableDescText).'</div>';
				}
				$Icon = (!empty($attr['showIcon']) && !empty($attr['PrefixIcon'])) ? '<i class="'.esc_attr($attr['PrefixIcon']).' table-prefix-icon tpgb-trans-linear"></i>' : '';
				$output .= '<div class="tpgb-toc-heading tpgb-trans-linear"><span>'. $Icon .'<span>'. wp_kses_post($attr['contentText']) .$table_desc.'</span></span>';
				if(!empty($ToggleIcon)){
					$output .= '<span><i class="table-toggle-icon tpgb-trans-linear '.esc_attr($openIcon).'"></i></span>';
				}
				$output .= '</div>';
			}
			$output .= '<div class="tpgb-toc toc"></div>';
		$output .= '</div>';
    $output .= '</div>';

	if(!empty($style_atts)){
		$output .= '<style>'.$style_atts.'</style>';
	}
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);
	
	return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_table_content() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'typeList' => [
				'type' => 'string',
				'default' => 'UL',
			],
			'Style' => [
				'type' => 'string',
				'default' => 'none',
			],
			'selectorH1' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'selectorH2' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'selectorH3' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'selectorH4' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'selectorH5' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'selectorH6' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'contentSelector' => array(
				'type' => 'string',
				'default' => '#content',
			),
			'ChildToggle' => array(
                'type' => 'boolean',
				'default' => false,
			),
			'headingsOffset' => array(
                'type' => 'string',
				'default' => 1,
			),
			
			'smoothScroll' => array(
                'type' => 'boolean',
				'default' => true,
			),
			'smoothDuration' => array(
                'type' => 'string',
				'default' => 420,
			),
			'scrollOffset' => array(
                'type' => 'string',
				'default' => 0,
			),
			
			
			'fixedPosition' => array(
                'type' => 'boolean',
				'default' => false,
			),
			'fixedOffset' => array(
                'type' => 'string',
				'default' => '',
			),
			
			'showText' => [
				'type' => 'boolean',
				'default' => true,
			],
			'contentText' => [
				'type' => 'string',
				'default' => 'Table of Contents',
			],
			'TableDescText' => [
				'type' => 'string',
				'default' => '',
			],
			
			'showIcon' => [
				'type' => 'boolean',
				'default' => false,
			],
			'PrefixIcon' => [
				'type'=> 'string',
				'default'=> '',
			],
			
			'ToggleIcon' => [
				'type' => 'boolean',
				'default' => false,
			],
			'openIcon' => [
				'type'=> 'string',
				'default'=> 'fas fa-angle-up',
			],
			'closeIcon' => [
				'type'=> 'string',
				'default'=> 'fas fa-angle-down',
			],
			'DefaultToggle' => [
				'type' => 'object',
				'default' => ['md' => true,'sm' => true,'xs' => false],
			],

			'totitleAlign' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			],

			'Level1Typo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list > li > a',
					],
				],
				'scopy' => true,
			],
			'Level1NormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list > li > a{color: {{Level1NormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'Level1ActiveColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list > li:hover > a, {{PLUS_WRAP}} .tpgb-toc > .toc-list > li.is-active-li > a{color: {{Level1ActiveColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'LevelSubTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list .toc-list > li > a',
					],
				],
				'scopy' => true,
			],
			'LevelSubNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list .toc-list > li > a{color: {{LevelSubNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'LevelSubActiveColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc .toc-list .toc-list > li:hover > a, {{PLUS_WRAP}} .tpgb-toc .toc-list .toc-list > li.is-active-li > a{color: {{LevelSubActiveColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'leftOffset' => [
				'type' => 'string',
				'default' => 20,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '.editor-styles-wrapper {{PLUS_WRAP}} .toc-list,{{PLUS_WRAP}} .toc-list{padding-left: {{leftOffset}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li{padding-left: {{leftOffset}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .tpgb-toc .toc-list .toc-list li{padding-left: {{leftOffset}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .tpgb-toc .toc-list .toc-list li{padding-left: {{leftOffset}}px;}',
					],
				],
				'scopy' => true,
            ],
			'bottomOffset' => [
				'type' => 'string',
				'default' => 10,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li a, {{PLUS_WRAP}}.table-style-2 .toc-list li{margin-bottom: {{bottomOffset}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .toc-list li a, {{PLUS_WRAP}}.table-style-3 .toc-list li{margin-bottom: {{bottomOffset}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .toc-list li a, {{PLUS_WRAP}}.table-style-4 .toc-list li{margin-bottom: {{bottomOffset}}px;}',
					],
				],
				'scopy' => true,
            ],
			'outerMargin' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc{margin: {{outerMargin}};}',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc{padding: {{contentPadding}};}',
					],
				],
				'scopy' => true,
			],
			
			'Style4Padding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => 5,
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc > .toc-list > li .toc-list{padding-left: {{Style4Padding}};}',
					],
				],
				'scopy' => true,
			],
			'TableSetMinHeight' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'TableMinHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableSetMinHeight', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc{max-height: {{TableMinHeight}};}',
					],
				],
				'scopy' => true,
			],
			'ScrollBarWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableSetMinHeight', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc::-webkit-scrollbar{width: {{ScrollBarWidth}}px;}',
					],
				],
				'scopy' => true,
			],
			'ScrollBarThumb' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableSetMinHeight', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc::-webkit-scrollbar-thumb{background-color: {{ScrollBarThumb}}}',
					],
				],
				'scopy' => true,
            ],
			'ScrollBarTrack' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableSetMinHeight', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc::-webkit-scrollbar-track{background-color: {{ScrollBarTrack}}}',
					],
				],
				'scopy' => true,
            ],
			
			'LineWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.table-style-1 .toc-link::before{width: {{LineWidth}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li{border-left-width: {{LineWidth}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .tpgb-toc> .toc-list .toc-list li:before{width: {{LineWidth}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .tpgb-toc> .toc-list .toc-list li:before{width: {{LineWidth}}px;}{{PLUS_WRAP}}.table-style-4 .tpgb-toc> .toc-list .toc-list li.is-active-li:before{left: calc({{LineWidth}} / 2 * 1px );}',
					],
				],
				'scopy' => true,
            ],
			'Line2Width' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li.is-active-li{border-left-width: {{Line2Width}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .tpgb-toc> .toc-list .toc-list li.is-active-li:before{width: {{Line2Width}}px;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .tpgb-toc> .toc-list .toc-list li.is-active-li:before{width: {{Line2Width}}px;}',
					],
				],
				'scopy' => true,
            ],
			'LineColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.table-style-1 .toc-link::before{background-color: {{LineColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li{border-left-color: {{LineColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .tpgb-toc> .toc-list .toc-list li:before{background: {{LineColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .tpgb-toc> .toc-list .toc-list li:before{background: {{LineColor}};}',
					],
				],
				'scopy' => true,
            ],
			'LineActiveColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.table-style-1 .toc-link.is-active-link::before{background-color: {{LineActiveColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.table-style-2 .toc-list li.is-active-li{border-left-color: {{LineActiveColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.table-style-3 .tpgb-toc> .toc-list .toc-list li.is-active-li:before{background: {{LineActiveColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.table-style-4 .tpgb-toc> .toc-list .toc-list li.is-active-li:before{background: {{LineActiveColor}};}',
					],
					
				],
				'scopy' => true,
            ],
			
			'TextTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			'TextNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading{color: {{TextNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'TextHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading{color: {{TextHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'DescTextTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableDescText', 'relation' => '!=', 'value' => '']],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading .tpgb-table-desc',
					],
				],
				'scopy' => true,
			],
			'DescTextNormalColor' => [
				'type' => 'string',
				'default' => '#888',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableDescText', 'relation' => '!=', 'value' => '']],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading .tpgb-table-desc{color: {{DescTextNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'DescTextHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TableDescText', 'relation' => '!=', 'value' => '']],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading .tpgb-table-desc{color: {{DescTextHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'IconSize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-heading .table-prefix-icon{font-size: {{IconSize}}px;}',
					],
				],
				'scopy' => true,
            ],
			'IconNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-heading .table-prefix-icon{color: {{IconNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'IconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading .table-prefix-icon{color: {{IconHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'ToggleIconSize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ToggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .table-toggle-wrap .table-toggle-icon{font-size: {{ToggleIconSize}}px;}',
					],
				],
				'scopy' => true,
            ],
			'ToggleIconNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ToggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .table-toggle-wrap .table-toggle-icon{color: {{ToggleIconNormalColor}};}',
					],
				],
				'scopy' => true,
            ],
			'ToggleIconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ToggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .table-toggle-wrap.tpgb-toc-wrap:hover .table-toggle-icon{color: {{ToggleIconHoverColor}};}',
					],
				],
				'scopy' => true,
            ],
			
			'TextMargin' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading{margin: {{TextMargin}};}',
					],
				],
				'scopy' => true,
			],
			'TextPadding' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading{padding: {{TextPadding}};}',
					],
				],
				'scopy' => true,
			],
			'TextBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			'TextBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			
			'TextBorderRadius' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading{border-radius: {{TextBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'TextBorderRadiusHover' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading{border-radius: {{TextBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'TextBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			'TextBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			'TextBoxShadow' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			'TextBoxShadowHover' => [
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
						'condition' => [(object) ['key' => 'showText', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover .tpgb-toc-heading',
					],
				],
				'scopy' => true,
			],
			
			'boxPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap{padding: {{boxPadding}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap',
					],
				],
				'scopy' => true,
			],
			'boxBorderHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover',
					],
				],
				'scopy' => true,
			],
			
			'boxBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap{border-radius: {{boxBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover{border-radius: {{boxBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'boxBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap',
					],
				],
				'scopy' => true,
			],
			'boxBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-toc-wrap:hover',
					],
				],
				'scopy' => true,
			],
		);
	
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-table-content', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_table_content_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_table_content' );