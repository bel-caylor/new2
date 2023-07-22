<?php
/* Block : Tp Column
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_section_column_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("column");
    $Width = (!empty($attributes['Width'])) ? $attributes['Width'] : [ 'md' => 100, 'sm' => 100, 'xs' => 100 ];
	$customClasses = (!empty($attributes['customClasses'])) ? $attributes['customClasses'] : '';
	$wrapLink = (!empty($attributes['wrapLink'])) ? $attributes['wrapLink'] : false;


	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	if(!empty($customClasses)){
		$blockClass .= ' '.esc_attr($customClasses);
	}
	if(!empty($Width)){
		if(!empty($Width['md'])){
			$blockClass .= ' tpgb-md-col-'.intval($Width['md']);
		}
		if(!empty($Width['sm'])){
			$blockClass .= ' tpgb-sm-col-'.intval($Width['sm']);
		}
		if(!empty($Width['xs'])){
			$blockClass .= ' tpgb-xs-col-'.intval($Width['xs']);
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
			$colLink .= ' data-target="_blank" ';
		}else{
			$colLink .= ' data-target="_self" ';
		}
		$colLink .= Tp_Blocks_Helper::add_link_attributes($attributes['colUrl']);
	}

	
	$output .= '<div class="tpgb-column tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-id="'.esc_attr($block_id).'" '.$colLink.' >';
		$output .= '<div class="tpgb-column-wrap">';
			$output .= '<div class="tpgb-column-inner">';
				$output .= $content;
			$output .= "</div>";
		$output .= "</div>";
		if(!empty($attributes['horizontalPos'])){
			$colStyle = '';
			$horizontal = $attributes['horizontalPos'];
			$selector = '.tpgb-block-'.esc_attr($block_id).' > .tpgb-column-wrap > .tpgb-column-inner';
			if(isset($horizontal['md']) && !empty($horizontal['md'])){
				$colStyle .= ($horizontal['md']=='flex-start') ? $selector.'{text-align:left}' : '';
				$colStyle .= ($horizontal['md']=='center') ? $selector.'{text-align:center}' : '';
				$colStyle .= ($horizontal['md']=='flex-end') ? $selector.'{text-align:right}' : '';
			}
			if(isset($horizontal['sm']) && !empty($horizontal['sm'])){
				$colStyleSm = ($horizontal['sm']=='flex-start') ? $selector.'{text-align:left}' : '';
				$colStyleSm .= ($horizontal['sm']=='center') ? $selector.'{text-align:center}' : '';
				$colStyleSm .= ($horizontal['sm']=='flex-end') ? $selector.'{text-align:right}' : '';
				$colStyle .= !empty($colStyleSm) ? '@media (max-width: 1024px) {'.$colStyleSm.'}' : '';
			}
			if(isset($horizontal['xs']) && !empty($horizontal['xs'])){
				$colStyleXs = ($horizontal['xs']=='flex-start') ? $selector.'{text-align:left}' : '';
				$colStyleXs .= ($horizontal['xs']=='center') ? $selector.'{text-align:center}' : '';
				$colStyleXs .= ($horizontal['xs']=='flex-end') ? $selector.'{text-align:right}' : '';
				$colStyle .= !empty($colStyleXs) ? '@media (max-width: 767px) {'.$colStyleXs.'}' : '';
			}
			$output .= !empty($colStyle) ? '<style>'.$colStyle.'</style>' : '';
		}
	$output .= "</div>";
	
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_section_column() {
	
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
			'verticalPos' => [
                'type' => 'object',
				'default' => [ 'md' => '','sm' => '','xs' => '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner{ align-content:{{verticalPos}} !important; align-items:{{verticalPos}} !important; }',
					],
				],
			],
			'verticalPosition' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner{ align-content:{{verticalPosition}} !important; align-items:{{verticalPosition}} !important; }',
					],
				],
			],
			'horizontalPos' => [
                'type' => 'object',
				'default' => [ 'md' => '','sm' => '','xs' => '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner,{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner > .block-editor-inner-blocks > .block-editor-block-list__layout{ justify-content:{{horizontalPos}}; }{{PLUS_WRAP}}.tpgb-column-editor > .tpgb-column-wrap > .tpgb-column-inner > .block-editor-inner-blocks > .block-editor-block-list__layout { display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;}',
					],
				],
			],
			'horizontalPosition' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner,{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner > .block-editor-inner-blocks > .block-editor-block-list__layout{ justify-content:{{horizontalPosition}}; }{{PLUS_WRAP}}.tpgb-column-editor > .tpgb-column-wrap > .tpgb-column-inner > .block-editor-inner-blocks > .block-editor-block-list__layout { display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;}',
					],
				],
			],
			'blockSpace' => [
                'type' => 'object',
				'default' => [ 'md' => '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner > *:not(:last-child){ margin-bottom:{{blockSpace}}; } {{PLUS_WRAP}} > .tpgb-column-wrap > .tpgb-column-inner > .block-editor-inner-blocks > .block-editor-block-list__layout > .block-editor-block-list__block:not(:nth-last-child(2)){ margin-bottom:{{blockSpace}}; }',
					],
				],
			],
			'NormalBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
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
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{PLUS_WRAP}}:hover > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}:hover > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
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
							'top' => '',
							'bottom' => '',
							'left' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
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
							'top' => '',
							'bottom' => '',
							'left' => '',
							'right' => '',
						],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}:hover > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}:hover > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
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
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap{ border-radius: {{NormalBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} > .inner-wrapper-sticky > .tpgb-column-wrap{ border-radius: {{NormalBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col{ border-radius: {{NormalBradius}}; }',
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
						'selector' => '{{PLUS_WRAP}}:hover > .tpgb-column-wrap{ border-radius: {{HoverBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}:hover > .inner-wrapper-sticky > .tpgb-column-wrap{ border-radius: {{HoverBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover{ border-radius: {{NormalBradius}}; }',
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
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col',
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
						'selector' => '{{PLUS_WRAP}}:hover > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}:hover > .inner-wrapper-sticky > .tpgb-column-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col:hover',
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
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'default']],
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap{margin: {{Margin}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
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
						'selector' => '{{PLUS_WRAP}} > .tpgb-column-wrap{padding: {{Padding}} !important;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'stickycol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} > .inner-wrapper-sticky > .tpgb-column-wrap{padding: {{Padding}} !important;}',
					],
					(object) [
						'condition' => [(object) ['key' => 'columnLay', 'relation' => '==', 'value' => 'flexbox']],
						'selector' => '{{PLUS_WRAP}}.tpgb-container-col{padding: {{Padding}};}',
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
						'selector' => '@media (max-width: 1024px){.text-center{text-align: center;}}@media (max-width: 767px){ .edit-post-visual-editor {{PLUS_WRAP}},.editor-styles-wrapper {{PLUS_WRAP}}{display: block;opacity: .5;} {{PLUS_WRAP}}{ display:none !important; } }',
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
		];
		
	$attributesOptions = array_merge( $attributesOptions );
	
	register_block_type( 'tpgb/tp-column', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_section_column_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_section_column' );