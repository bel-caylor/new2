<?php
/* Block : TP Design Tool
 * @since : 1.3.2
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_design_tool_render_callback( $attributes ) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$designToolOpt = (!empty($attributes['designToolOpt'])) ? $attributes['designToolOpt'] : 'grid_stystem';
	$gridSystemOpt = (!empty($attributes['gridSystemOpt'])) ? $attributes['gridSystemOpt'] : 'gs_default';
	$gridDirection = (!empty($attributes['gridDirection'])) ? $attributes['gridDirection'] : 'ltr';
	$gridOnFront = (!empty($attributes['gridOnFront'])) ? $attributes['gridOnFront'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$designTool = '';
	if(!empty($gridOnFront) && $designToolOpt=='grid_stystem'){
		$designTool .= 'html:before{content: "";position:fixed;pointer-events:none;top:0;right:0;bottom:0;left:0;margin-right:auto;margin-left:auto;width: calc(100% - (2 * var(--tp_grid_left_right_offset)));max-width: var(--tp_grid_cont_max_width);min-height: 100vh;background-image: var(--tp_grid_background-col-opt);background-size: var(--tp_grid_background-width-opt) 100%;z-index:999;}';
		if($gridSystemOpt=='gs_default'){
			$designTool .=':root{--tp_grid_repeate-columns-width: calc(100% / var(--tp_grid_columns));--tp_grid_column-width: calc((100% / var(--tp_grid_columns)) - var(--tp_grid_alley));--tp_grid_background-width-opt: calc(100% + var(--tp_grid_alley));--tp_grid_background-col-opt: repeating-linear-gradient(to right,var(--tp_grid_color), var(--tp_grid_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_repeate-columns-width));} html {--tp_grid_cont_max_width: 1140px;--tp_grid_columns: 12;--tp_grid_color: rgba(128, 114, 252, 0.25);--tp_grid_alley: 30px; --tp_grid_alley_color: transparent;--tp_grid_left_right_offset:0px;} @media (max-width: 1024px){ html {--tp_grid_columns: 6;--tp_grid_alley:15px;}} @media (max-width: 767px){ html {--tp_grid_columns: 4;--tp_grid_alley:10px;}} ';
		}else{
			$designTool .=':root {--tp_grid_repeate-columns-width: calc(100% / var(--tp_grid_columns));--tp_grid_column-width: calc((100% / var(--tp_grid_columns)) - var(--tp_grid_alley));--tp_grid_background-width-opt: calc(100% + var(--tp_grid_alley)); --tp_grid_background-col-opt: repeating-linear-gradient(';
			$direction ='';
			if($gridDirection=='ltr'){
				$direction ='to right,';
			}else if($gridDirection=='ttb'){
				$direction ='';
			}
			$designTool .=$direction . 'var(--tp_grid_color), var(--tp_grid_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_repeate-columns-width) );}';

		}
	}

    $output .= '<div class="tpgb-design-tool tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' ">';
	$output .= '<style>'.$designTool.'</style>';
    $output .= '</div>';
	

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_design_tool() {
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'designToolOpt' => [
				'type' => 'string',
				'default' => 'grid_stystem',	
			],
			'gridSystemOpt' => [
				'type' => 'string',
				'default' => 'gs_default',	
			],
			'gridDirection' => [
				'type' => 'string',
				'default' => 'ltr',	
			],
			'gridMaxWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '1140',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html {--tp_grid_cont_max_width: {{gridMaxWidth}} !important; }',
					],
				],
				'scopy' => true,
			],
			'gridColumn' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '12',
					"unit" => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html {--tp_grid_columns: {{gridColumn}} !important; }',
					],
				],
				'scopy' => true,
			],
			'gridBGcolor' => [
				'type' => 'string',
				'default' => '#8072fc40',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html { --tp_grid_color: {{gridBGcolor}} !important; }',
					],
				],
				'scopy' => true,
			],
			'alleySpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '30',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html {--tp_grid_alley: {{alleySpace}} !important; }',
					],
				],
				'scopy' => true,
			],
			'alleyBGcolor' => [
				'type' => 'string',
				'default' => '#00000000',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html { --tp_grid_alley_color: {{alleyBGcolor}} ; }',
					],
				],
				'scopy' => true,
			],
			'gridOffset' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '30',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gridSystemOpt', 'relation' => '==', 'value' => 'gs_custom' ]],
						'selector' => 'html { --tp_grid_left_right_offset: {{gridOffset}} !important; }',
					],
				],
				'scopy' => true,
			],
			'gridOnFront' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
		);
	$attributesOptions = array_merge($attributesOptions);
	
	register_block_type( 'tpgb/tp-design-tool', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_design_tool_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_design_tool' );