<?php
/* Block : TP Container inner
 * @since : 1.4.4
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_grid_render_callback( $attributes, $content) {

	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$Width = (!empty($attributes['Width'])) ? $attributes['Width'] : [ 'md' => 100, 'sm' => 100, 'xs' => 100 ];
	
	$stickycol = (!empty($attributes['stickycol'])) ? $attributes['stickycol'] : false;
	$topSpace = (isset($attributes['topSpace'])) ? (int) $attributes['topSpace'] : 40;
	$botSpace = (isset($attributes['botSpace'])) ? (int) $attributes['botSpace'] : 40;
	$customClasses = (!empty($attributes['customClasses'])) ? $attributes['customClasses'] : '';
	$stickyDes = (!empty($attributes['stickyDes'])) ? $attributes['stickyDes'] : false;
	$stickyTab = (!empty($attributes['stickyTab'])) ? $attributes['stickyTab'] : false;
	$stickyMob = (!empty($attributes['stickyMob'])) ? $attributes['stickyMob'] : false;
	$wrapLink = (!empty($attributes['wrapLink'])) ? $attributes['wrapLink'] : false;
	$showchild = (!empty($attributes['showchild'])) ? $attributes['showchild'] : false;
	$flexChild = (!empty($attributes['flexChild'])) ? $attributes['flexChild'] : [];


	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$dataAttr = '';
	if(isset($stickycol) && $stickycol == true ){
		$array_enable=array();
		if(!empty($stickyDes) && isset($stickyDes) ){
			$array_enable[]= 'desktop';
		}
		if(!empty($stickyTab) && isset($stickyTab)  ){
			$array_enable[]= 'tablet';
		}
		if(!empty($stickyMob) && isset($stickyMob) ){
			$array_enable[]= 'mobile';
		}

		$column_settings = array(
			'id'            => esc_attr($block_id),
			'sticky'        => $stickycol,
			'topSpacing'    => isset($topSpace) ? $topSpace: 40,
			'bottomSpacing' => isset($botSpace) ? $botSpace : 40,
			'stickyOn'      => !empty( $array_enable ) ? $array_enable : array( 'desktop', 'tablet' ),
		);

		$blockClass .= ' tpgb-column-sticky ';
		$dataAttr = 'data-sticky-column = '.json_encode( $column_settings ).' ';
	}
	
	
	if(!empty($customClasses)){
		$blockClass .= ' '.esc_attr($customClasses);
	}
    
	// Set Flex child css
	if(!empty($showchild) ){
		if(!empty($flexChild)){
			$flexChildCss = Tpgbp_Pro_Blocks_Helper::tpgbp_flex_child_css( $flexChild , '.tpgb-block-'.esc_attr($block_id).'.tpgb-container-col > *:nth-child' );
		}
	}

	// Set Link Data
	$colLink = '';
	if(!empty($wrapLink)){
		$colUrl = (!empty($attributes['colUrl'])) ? $attributes['colUrl'] : '';
		$blockClass .= ' tpgb-col-link';
		
		if( !empty($colUrl) && isset($colUrl['url']) && !empty($colUrl['url']) ){
			$colLink .= ' data-tpgb-col-link= "'.esc_url($colUrl['url']).'" ';
		}
		if(!empty($colUrl) && isset($colUrl['target']) && !empty($colUrl['target'])){
			$colLink .= ' data-target="_blank"';
		}else{
			$colLink .= ' data-target="_self"';
		}
		$colLink .= Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['colUrl']);
	}

	$output .= '<div class="tpgb-container-col tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-id="'.esc_attr($block_id).'" '.$colLink.' '.$dataAttr.' >';
		$output .= $content;
	$output .= '</div>';
	
	if(!empty($flexChildCss)){
		$output .= !empty($flexChildCss) ? '<style>'.$flexChildCss.'</style>' : '';
	}

    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_grid() {
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'className' => [
				'type' => 'string',
				'default' => '',
			],
			'Width' => [
                'type' => 'object',
				'default' => [ 'md' => 50, 'sm' => 50, 'xs' => 100, 'unit' => '%', 'device' => 'md' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}:not(.tpgb-column-editor):not(.tpgb-container-col-editor){ width:{{Width}}; }',
					],
				],
			],
			'minHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout,{{PLUS_WRAP}}.tpgb-container-col{ min-height: {{minHeight}}; }',
					],
				],
			],
			'stickycol' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'topSpace' => [
				'type'=> 'string',
				'default'=> '',
				'scopy' => true,
			],
			'botSpace' => [
				'type'=> 'string',
				'default'=> '',
				'scopy' => true,
			],
			'stickyDes' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'stickyTab' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'stickyMob' => [
				'type' => 'boolean',
				'default' => false,
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor',
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor:hover',
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
						'md' => (object)[
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor',
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
						'md' => (object)[
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor:hover',
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col{ border-radius: {{NormalBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor{ border-radius: {{NormalBradius}}; }',
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover{ border-radius: {{HoverBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor:hover{ border-radius: {{HoverBradius}}; }',
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor',
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
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor:hover',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col{margin: {{Margin}};}',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col{padding: {{Padding}} !important; }',
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
			'customClasses' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
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
			'hideDesktop' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (min-width: 1201px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none } }',
					],
				],
				'scopy' => true,
			],
			'hideTablet' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (min-width: 768px) and (max-width: 1200px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none } }',
					],
				],
				'scopy' => true,
			],
			'hideMobile' => [
				'type' => 'boolean',
				'default' => false,
				'style' => [
					(object) [
						'selector' => '@media (max-width: 1024px){.text-center{text-align: center;}}@media (max-width: 767px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block!important;opacity: .5;} {{PLUS_WRAP}}{ display:none !important; } }',
					],
				],
				'scopy' => true,
			],
			'wrapLink' => [
				'type' => 'boolean',
				'default' => false,
			],
			'colUrl' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',
					'target' => '',
					'nofollow' => ''
				],
			],
			
			// Flex Box Css
			'flexreverse' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}} }',
					],
					(object) [
						'condition' => [ (object) ['key' => 'flexreverse', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-direction: {{flexDirection}}-reverse }',
					],
				],
				'scopy' => true,
			],
			'flexAlign' => [
				'type' => 'object',
				'default' => [ 'md' => 'flex-start', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ align-items : {{flexAlign}} }',
					],
				],
				'scopy' => true,
			],
			'flexJustify' => [
				'type' => 'object',
				'default' => [ 'md' => 'flex-start', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ justify-content : {{flexJustify}} }',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ gap : {{flexGap}} }',
					],
				],
				'scopy' => true,
			],
			'flexwrap' => [
				'type' => 'object',
				'default' => [ 'md' => 'nowrap', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [ (object) [ 'key' => 'reverseWrap', 'relation' => '==', 'value' => false ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-wrap : {{flexwrap}} }',
					],
					(object) [
						'condition' => [ (object) ['key' => 'reverseWrap', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ flex-wrap : {{flexwrap}}-reverse }',
					],
				],
				'scopy' => true,
			],
			'alignWrap' => [
				'type' => 'object',
				'default' => [ 'md' => 'flex-end', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col,{{PLUS_WRAP}}.tpgb-container-col .inner-wrapper-sticky,{{PLUS_WRAP}}.tpgb-container-col-editor > .block-editor-inner-blocks > .block-editor-block-list__layout{ align-content : {{alignWrap}} }',
					],
				],
				'scopy' => true,
			],
			'reverseWrap' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'showchild' => [
				'type' => 'boolean',
				'default' => false,
			],
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
		];
		
	$attributesOptions = array_merge( $attributesOptions );
	
	register_block_type( 'tpgb/tp-container-inner', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_grid_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_grid' );