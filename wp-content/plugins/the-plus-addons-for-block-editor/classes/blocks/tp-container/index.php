<?php
/* Block : TP Row(Section)
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_container_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $stretchRow = (!empty($attributes['stretchRow'])) ? $attributes['stretchRow'] : false;
    $height = (!empty($attributes['height'])) ? $attributes['height'] : '';
    $customClass = (!empty($attributes['customClass'])) ? $attributes['customClass'] : '';
	$customId = (!empty($attributes['customId'])) ? 'id="'.esc_attr($attributes['customId']).'"' : '';
	$tagName = (!empty($attributes['tagName'])) ? $attributes['tagName'] : '';

	$wrapLink = (!empty($attributes['wrapLink'])) ? $attributes['wrapLink'] : false;

	$showchild = (!empty($attributes['showchild'])) ? $attributes['showchild'] : false;
	$flexChild = (!empty($attributes['flexChild'])) ? $attributes['flexChild'] : [];
	$contentWidth = (!empty($attributes['contentWidth'])) ? $attributes['contentWidth'] : 'wide';
	$colDir = (!empty($attributes['colDir'])) ? $attributes['colDir'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	if( !empty($contentWidth) && $contentWidth=='wide' && strpos($blockClass, 'alignwide') !== false ){
		$blockClass = str_replace("alignwide","", $blockClass);
	}

	$sectionClass = '';
	if( !empty( $height ) ){
		$sectionClass .= ' tpgb-section-height-'.$height;
	}
	if( !empty( $stretchRow ) ){
		$sectionClass .= ' tpgb-section-stretch-row alignfull';
	}
	
	// Toogle Class For wrapper Link

	$linkdata = '';
	if(!empty($wrapLink)){
		$rowUrl = (!empty($attributes['rowUrl'])) ? $attributes['rowUrl'] : '';
		$sectionClass .= ' tpgb-row-link';
		
		if( !empty($rowUrl) && isset($rowUrl['url']) && !empty($rowUrl['url']) ){
			$linkdata .= 'data-tpgb-row-link="'.esc_url($rowUrl['url']).'" ';
		}
		if(!empty($rowUrl) && isset($rowUrl['target']) && !empty($rowUrl['target'])){
			$linkdata .= 'data-target="_blank" ';
		}else{
			$linkdata .= 'data-target="_self" ';
		}
		$linkdata .= Tp_Blocks_Helper::add_link_attributes($attributes['rowUrl']);
	}


	// Set Flex child css
	if(!empty($showchild) ){
		if(!empty($flexChild)){
			$flexChildCss = Tp_Blocks_Helper::tpgb_flex_child_css( $flexChild , '.tpgb-block-'.esc_attr($block_id).'.tpgb-container-row > *:nth-child'  );
		}
	}

	$output .= '<'.Tp_Blocks_Helper::validate_html_tag($tagName).' '.$customId.' class="tpgb-container-row tpgb-block-'.esc_attr($block_id).' '.esc_attr($sectionClass).' '.esc_attr($customClass).' '.esc_attr($blockClass).' '.($colDir == 'c100' || $colDir == 'r100' ? ' tpgb-container-inline' : '').'  tpgb-container-'.$contentWidth.' " data-id="'.esc_attr($block_id).'" '.$linkdata.' >';
		$output .= $content;
	$output .= '</'.Tp_Blocks_Helper::validate_html_tag($tagName).'>';
	if(!empty($flexChildCss)){
		$output .= !empty($flexChildCss) ? '<style>'.$flexChildCss.'</style>' : '';
	}
	
	
	if ( class_exists( 'Tpgb_Blocks_Global_Options' ) ) {
		$global_block = Tpgb_Blocks_Global_Options::get_instance();
		if ( !empty($global_block) && is_callable( array( $global_block, 'block_row_conditional_render' ) ) ) {
			$output = Tpgb_Blocks_Global_Options::block_row_conditional_render($attributes, $output);
		}
	}
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_container_row() {
	
	$displayRules = [];
	if ( class_exists( 'Tpgb_Display_Conditions_Rules' ) ) {
		$display_Conditions = Tpgb_Display_Conditions_Rules::get_instance();
		if ( !empty($display_Conditions) && is_callable( array( $display_Conditions, 'tpgb_display_option' ) ) ) {
			$displayRules = Tpgb_Display_Conditions_Rules::tpgb_display_option();
		}
	}
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'className' => [
				'type' => 'string',
				'default' => '',
			],
			'columns' => [
                'type' => 'number',
				'default' => '',
			],
			'contentWidth' => [
				'type' => 'string',
				'default' => 'wide',
			],
			'align' => [
				'type' => 'string',
				'default' => 'wide',
			],
			'containerWide' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'contentWidth', 'relation' => '==', 'value' => 'wide']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row{ --content-width : Min(100%, {{containerWide}});} {{PLUS_WRAP}}.tpgb-container-row.tpgb-container-wide{ max-width : 100% !important;}',
					],
				],
			],
			'containerFull' => [
				'type' => 'object',
				'default' => [ 
					'md' => 100,
					"unit" => '%',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'contentWidth', 'relation' => '==', 'value' => 'full']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row{ --content-width : {{containerFull}}; } {{PLUS_WRAP}}.tpgb-container-full{ max-width : {{containerFull}} !important; width : {{containerFull}} }',
					],
				],
			],
			'colDir' => [
				'type' => 'string',
				'default' => '',
			],
			'sectionWidth' => [
				'type' => 'string',
				'default' => 'boxed',	
			],
			'height' => [
				'type' => 'string',
				'default' => '',	
			],
			'minHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => 300,
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'height', 'relation' => '==', 'value' => 'min-height']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row, {{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ height: {{minHeight}}; }',
					],
				],
			],
			'gutterSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => 15,	
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row > .tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-row > .block-editor-inner-blocks > .block-editor-block-list__layout > [data-type="tpgb/tp-container-inner"],{{PLUS_WRAP}}.tpgb-container-row > .tpgb-container-col .inner-wrapper-sticky{ padding: {{gutterSpace}}; }',
					],
				],
			],
			'tagName' => [
                'type' => 'string',
				'default' => 'div',
			],
			'overflow' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row{ overflow: {{overflow}}; }',
					],
				],
			],
			'customClass' => [
				'type' => 'string',
				'default' => '',	
			],
			'customId' => [
				'type' => 'string',
				'default' => '',	
			],
			'customCss' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '',
					],
				],
			],
			
			'shapeTop' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			
			'shapeBottom' => [
                'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			
			'NormalBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}',
					],
				],
				'scopy' => true,
			],
			'HoverBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}:hover',
					],
				],
				'scopy' => true,
			],
			'NormalBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
					'color' => '',
					'width' => (object) [
						'md' => [
							"top" => '',
							'bottom' => '',
							'left' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}',
					],
				],
				'scopy' => true,
			],
			'HoverBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
					'color' => '',
					'width' => (object) [
						'md' => [
							"top" => '',
							'bottom' => '',
							'left' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}:hover',
					],
				],
				'scopy' => true,
			],
			'NormalBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}},{{PLUS_WRAP}} .tpgb-row-background{ border-radius: {{NormalBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'HoverBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}:hover,{{PLUS_WRAP}}:hover .tpgb-row-background{ border-radius: {{HoverBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'NormalBShadow' => [
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
						'selector' => '{{PLUS_WRAP}}',
					],
				],
				'scopy' => true,
			],
			'HoverBShadow' => [
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
						'selector' => '{{PLUS_WRAP}}:hover',
					],
				],
				'scopy' => true,
			],
			'Margin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}{margin: {{Margin}};}',
					],
				],
				'scopy' => true,
			],
			'Padding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'contentWidth', 'relation' => '==', 'value' => 'wide']],
						'selector' => '{{PLUS_WRAP}}{ --tpgb-padding-left: {{LEFT}}{{Padding}} } {{PLUS_WRAP}}{ --tpgb-padding-right: {{RIGHT}}{{Padding}} } {{PLUS_WRAP}}{ --tpgb-padding-top: {{TOP}}{{Padding}} } {{PLUS_WRAP}}{ --tpgb-padding-bottom: {{BOTTOM}}{{Padding}} }',
					],
					(object) [
						'condition' => [ (object) ['key' => 'contentWidth', 'relation' => '==', 'value' => 'full']],
						'selector' => '{{PLUS_WRAP}}{ padding : {{Padding}} }',
					],
				],
				'scopy' => true,
			],
			'ZIndex' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}{z-index: {{ZIndex}};}',
					],
				],
				'scopy' => true,
			],
			
			'HideDesktop' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (min-width: 1201px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none } }',
					],
				],
				'scopy' => true,
			],
			'HideTablet' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (min-width: 768px) and (max-width: 1200px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none } }',
					],
				],
				'scopy' => true,
			],
			'HideMobile' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (max-width: 1024px){.text-center{text-align: center;}}@media (max-width: 767px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none !important; } }',
					],
				],
				'scopy' => true,
			],
		
			'wrapLink' => [
				'type' => 'boolean',
				'default' => false,
			],
			'rowUrl' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',
					'target' => '',
					'nofollow' => ''
				],
			],
			
			// Flex Css
			'flexreverse' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'flexRespreverse' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'flexTabreverse' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'flexMobreverse' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'flexDirection' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'flexreverse', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}} }',
					],
					(object) [
						'condition' => [ (object) ['key' => 'flexreverse', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}}-reverse }',
					],
					(object) [
						'condition' => [ (object) [ 'key' => 'flexRespreverse', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'flexTabreverse', 'relation' => '==', 'value' => false] 
						],
						'selector' => '@media (max-width: 1024px) and (min-width:768px) { {{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}} } }' ,
					],
					(object) [
						'condition' => [ (object) [ 'key' => 'flexRespreverse', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'flexTabreverse', 'relation' => '==', 'value' => true] 
						],
						'selector' => '@media (max-width: 1024px) and (min-width:768px) { {{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}}-reverse  } }',
					],
					(object) [
						'condition' => [ (object) [ 'key' => 'flexRespreverse', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'flexMobreverse', 'relation' => '==', 'value' => false] 
						],
						'selector' => '@media (max-width: 767px) { {{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}} } }' ,
					],
					(object) [
						'condition' => [ (object) [ 'key' => 'flexRespreverse', 'relation' => '==', 'value' => true ], 
							(object) ['key' => 'flexMobreverse', 'relation' => '==', 'value' => true] 
						],
						'selector' => '@media (max-width: 767px){ {{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}}-reverse  } }',
					],
				],
				'scopy' => true,
			],
			'flexAlign' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ align-items : {{flexAlign}} }',
					],
				],
				'scopy' => true,
			],
			'flexJustify' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ justify-content : {{flexJustify}} }',
					],
				],
				'scopy' => true,
			],
			'flexGap' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ gap : {{flexGap}} }',
					],
				],
				'scopy' => true,
			],
			'flexwrap' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'reverseWrap', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-wrap : {{flexwrap}} }',
					],
					(object) [
						'condition' => [ (object) ['key' => 'reverseWrap', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-wrap : {{flexwrap}}-reverse }',
					],
				],
				'scopy' => true,
			],
			'alignWrap' => [
				'type' => 'object',
				'default' => [ 'md' => 'flex-end', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-row,{{PLUS_WRAP}}.tpgb-container-row-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ align-content : {{alignWrap}} }',
					],
				],
				'scopy' => true,
			],
			'reverseWrap' => [
				'type' => 'boolean',
				'default' => false,
			],
			// child Css
			'flexChild' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'flexShrink' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
							],
							'scopy' => true,
						],
						'flexGrow' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
							],
							'scopy' => true,
						],
						'flexBasis' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => '%',
							],
							'scopy' => true,
						],
						'flexselfAlign' => [
							'type' => 'object',
							'default' => [ 'md' => 'auto', 'sm' =>  '', 'xs' =>  '' ],
							'scopy' => true,
						],
						'flexOrder' => [
							'type' => 'object',
							'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
							'scopy' => true,
						],
					],
				],
				'default' => [
					(object)[ 'flexShrink' => [ 'md' => '' ] , 'flexGrow' => [ 'md' => '' ], 'flexBasis' => [ 'md' => '' ] ,'flexselfAlign' => [ 'md' => '' ] ,'flexOrder' => [ 'md' => '' ] ],
				],
			],
			'showchild' => [
				'type' => 'boolean',
				'default' => false,
			],
 		];
		
	$attributesOptions = array_merge( $attributesOptions, $displayRules );
	
	register_block_type( 'tpgb/tp-container', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_container_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_container_row' );