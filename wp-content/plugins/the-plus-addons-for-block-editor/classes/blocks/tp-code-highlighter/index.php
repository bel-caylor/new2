<?php
/**
 * Block : Code highlighter
 * @since 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_code_highlighter_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$className = (!empty($attributes['className'])) ? $attributes['className'] : '';
	$align = (!empty($attributes['align'])) ? $attributes['align'] : '';
	$languageType = (!empty($attributes['languageType'])) ? $attributes['languageType'] : 'markup';
	$themeType = (!empty($attributes['themeType'])) ? $attributes['themeType'] : 'prism-default';
	if(class_exists('Tpgbp_Pro_Blocks_Helper')){
		$sourceCode = (!empty($attributes['sourceCode'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['sourceCode'],['blockName' => 'tpgb/tp-code-highlighter']) : '';
		$languageText = (!empty($attributes['languageText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['languageText']) : '';
		$copyText = (!empty($attributes['copyText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['copyText']) : '';
	}else{
		$sourceCode = (!empty($attributes['sourceCode'])) ? $attributes['sourceCode'] : '';
		$languageText = (!empty($attributes['languageText'])) ? $attributes['languageText'] : '';
		$copyText = (!empty($attributes['copyText'])) ? $attributes['copyText'] : '';
	}
	
	$copyIcnType = (!empty($attributes['copyIcnType'])) ? $attributes['copyIcnType'] : 'none';
	$copyIconStore = (!empty($attributes['copyIconStore'])) ? $attributes['copyIconStore'] : '';
	if(class_exists('Tpgbp_Pro_Blocks_Helper')){
		$copiedText = (!empty($attributes['copiedText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['copiedText']) : '';
		$copyErrorText = (!empty($attributes['copyErrorText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['copyErrorText']) : '';
	}else{
		$copiedText = (!empty($attributes['copiedText'])) ? $attributes['copiedText'] : '';
		$copyErrorText = (!empty($attributes['copyErrorText'])) ? $attributes['copyErrorText'] : '';
	}
	$copiedIcnType = (!empty($attributes['copiedIcnType'])) ? $attributes['copiedIcnType'] : 'none';
	$copiedIconStore = (!empty($attributes['copiedIconStore'])) ? $attributes['copiedIconStore'] : '';
	
	$lineNumber = (!empty($attributes['lineNumber'])) ? $attributes['lineNumber'] : false;
	if(class_exists('Tpgbp_Pro_Blocks_Helper')){
		$lineHighlight = (!empty($attributes['lineHighlight'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['lineHighlight']) : '';
		$dwnldBtnText = (!empty($attributes['dwnldBtnText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['dwnldBtnText']) : '';
	}else{
		$lineHighlight = (!empty($attributes['lineHighlight'])) ? $attributes['lineHighlight'] : '';
		$dwnldBtnText = (!empty($attributes['dwnldBtnText'])) ? $attributes['dwnldBtnText'] : '';
	}
	$dnloadBtn = (!empty($attributes['dnloadBtn'])) ? $attributes['dnloadBtn'] : false;
	
	$dwnldIcnType = (!empty($attributes['dwnldIcnType'])) ? $attributes['dwnldIcnType'] : 'none';
	$dwnldIconStore = (!empty($attributes['dwnldIconStore'])) ? $attributes['dwnldIconStore'] : '';
	$fileLink = (!empty($attributes['fileLink']['url'])) ? $attributes['fileLink']['url'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$langtext = $lineNumClass = $dwnldBtnClass = $cpybtnicon = $copiedbtnicon = $dwndicon = '';
	if(!empty($languageText)){
		$langtext =  'data-label="'.esc_html($languageText).'"';
	}
	if(!empty($lineNumber)){
		$lineNumClass = 'line-numbers';
	}
	if(!empty($dnloadBtn)) {
		$dwnldBtnClass = 'data-src="'.esc_url($fileLink).'" data-download-link="'.esc_url($fileLink).'" data-download-link-label="'.esc_attr($dwnldBtnText).'"';
		if($dwnldIcnType=='icon'){
			$dwndicon = $dwnldIconStore;
		}
	}
	if($copyIcnType=='icon'){
		$cpybtnicon = $copyIconStore;
	}
	if($copiedIcnType=='icon'){
		$copiedbtnicon = $copiedIconStore;
	}
	
	// Set Dataattr For Circle Menu
	$codeAttr = [
		'id' => $block_id,
		'copytext' => $copyText,
		'copyicon' => $cpybtnicon,
		'copiedText' => $copiedText,
		'copiedicon' => $copiedbtnicon,
		'downloadtext' => $dwnldBtnText,
		'downloadicon' => $dwndicon
	];
	$codeAttr = htmlspecialchars(json_encode($codeAttr), ENT_QUOTES, 'UTF-8');

	$output = '';
    $output .= '<div class="tpgb-code-highlighter tpgb-relative-block code-'.esc_attr($themeType).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" data-code-atr= \'' .$codeAttr. '\'>';
		$output .='<pre class="language-'.esc_attr($languageType).' '.esc_attr($lineNumClass).'" data-line="'.esc_attr($lineHighlight).'" '.$dwnldBtnClass.' '.$langtext.'>';
			$output .='<code class="language-'.esc_attr($languageType).'" data-prismjs-copy="'.esc_attr($copyText).'" data-prismjs-copy-error="'.esc_attr($copyErrorText).'" data-prismjs-copy-success="'.esc_attr($copiedText).'">';
				$output .= esc_html($sourceCode);
			$output .='</code>';
		$output .='</pre>';
		$output .='<style> body.admin-bar .prism-previewer { margin-top: -15px !important; } body.admin-bar .prism-previewer-easing { margin-top: -40px !important; } </style>';
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_code_highlighter() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'languageType' => [
			'type' => 'string',
			'default' => 'markup',
		],
		'themeType' => [
			'type' => 'string',
			'default' => 'prism-default',
		],
		'sourceCode' => [
			'type' => 'string',
			'default' => '<h1>Welcome To Posimyth Innovation</h1>',	
		],
		'Alignment' => [
			'type' => 'object',
			'default' => 'left',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter pre{ text-align: {{Alignment}}; }',
				],
			],
			'scopy' => true,
		],
		
		'languageText' => [
			'type' => 'string',
			'default' => '',	
		],
		'copyText' => [
			'type' => 'string',
			'default' => 'Copy',	
		],
		'copyIcnType' => [
			'type' => 'string',
			'default' => 'none',	
		],
		'copyIconStore' => [
			'type'=> 'string',
			'default'=> 'far fa-copy',
		],
		'copiedText' => [
			'type' => 'string',
			'default' => 'Copied!',	
		],
		'copiedIcnType' => [
			'type' => 'string',
			'default' => 'none',	
		],
		'copiedIconStore' => [
			'type'=> 'string',
			'default'=> 'far fa-copy',
		],
		'copyErrorText' => [
			'type' => 'string',
			'default' => 'Error',	
		],
		'lineNumber' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'lineHighlight' => [
			'type' => 'string',
			'default' => '',	
		],
		'dnloadBtn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'dwnldBtnText' => [
			'type' => 'string',
			'default' => 'Download',	
		],
		'dwnldIcnType' => [
			'type' => 'string',
			'default' => 'none',	
		],
		'dwnldIconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-download',
		],
		'fileLink' => [
			'type'=> 'object',
			'default'=> [
				'url' => '#',	
				'target' => '',
				'nofollow' => ''
			],
		],
		
		/* Source Code Style Start */
		'scodePadding' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter pre{padding: {{scodePadding}};}',
				],
			],
			'scopy' => true,
		],
		'scodeMargin' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter pre{margin: {{scodeMargin}};}',
				],
			],
			'scopy' => true,
		],
		'scodeHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter pre{ max-height: {{scodeHeight}}; }',
				],
			],
			'scopy' => true,
		],
		
		/* Scroll Bar Start */
		'scrollBarTgl' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'scrollBarWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '10',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar{ width: {{scrollBarWidth}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar{ width: {{scrollBarWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'scrollBarHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar{ height: {{scrollBarHeight}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar{ height: {{scrollBarHeight}}; }',
				],
			],
			'scopy' => true,
		],
		
		'thumbBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 1,
				'bgType' => 'color',
				'bgDefaultColor' => '#ff844a',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'thumbRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '10',
					"right" => '10',
					"bottom" => '10',
					"left" => '10',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb{border-radius: {{thumbRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-thumb{border-radius: {{thumbRadius}};}',
				],
			],
			'scopy' => true,
		],
		'thumbShadow' => [
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
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-thumb',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'trackBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 1,
				'bgType' => 'color',
				'bgDefaultColor' => '#6f1ef150',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-track',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		'trackRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '10',
					"right" => '10',
					"bottom" => '10',
					"left" => '10',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-track{border-radius: {{trackRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-track{border-radius: {{trackRadius}};}',
				],
			],
			'scopy' => true,
		],
		'trackShadow' => [
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
					'condition' => [(object) ['key' => 'themeType', 'relation' => '!=', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre::-webkit-scrollbar-track',
				],
				(object) [
					'condition' => [(object) ['key' => 'themeType', 'relation' => '==', 'value' => 'prism-coy' ],['key' => 'scrollBarTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter .code-toolbar pre > code::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		/* Scroll Bar End */
		
		/* Source Code Style End */
		
		/* Language Text Style Start */
		'langTextPadding' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span{padding: {{langTextPadding}};}',
				],
			],
			'scopy' => true,
		],
		'langTextMargin' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span{margin: {{langTextMargin}};}',
				],
			],
			'scopy' => true,
		],
		'langTextTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',
				],
			],
			'scopy' => true,
		],
		'langTextNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span{ color: {{langTextNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'langTextHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover{ color: {{langTextHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'langTextNBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',
				],
			],
			'scopy' => true,
		],
		'langTextHBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',
				],
			],
			'scopy' => true,
		],
		'langTextNBorder' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',
				],
			],
			'scopy' => true,
		],
		'langTextHBorder' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',
				],
			],
			'scopy' => true,
		],
		'langTextNBRadius' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span{border-radius: {{langTextNBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'langTextHBRadius' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover{border-radius: {{langTextHBRadius}};} ',
				],
			],
			'scopy' => true,
		],
		'langTextNShadow' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span',
				],
			],
			'scopy' => true,
		],
		'langTextHShadow' => [
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
					'condition' => [(object) ['key' => 'languageText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-code-highlighter div.code-toolbar>.toolbar .toolbar-item > span:hover',
				],
			],
			'scopy' => true,
		],
		/* Language Text Style End */
		
		/* Line Number Style Start */
		'numberColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'lineNumber', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .line-numbers-rows > span:before{ color: {{numberColor}}; }',
				],
			],
			'scopy' => true,
		],
		'bdrColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'lineNumber', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .line-numbers .line-numbers-rows{ border-right-color: {{bdrColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* Line Number Style End */
		
		/* Highlight Number Style Start */
		'highlightBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'lineHighlight', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .line-highlight',
				],
			],
			'scopy' => true,
		],
		/* Highlight Number Style End */
		
		/* Copy/Download Style Start */
		'copyDwlBtnPadding' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button{padding: {{copyDwlBtnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a{padding: {{copyDwlBtnPadding}};}',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnMargin' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button{margin: {{copyDwlBtnMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a{margin: {{copyDwlBtnMargin}};}',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a',
				],
			],
			'scopy' => true,
		],
		'cpdwIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'copyIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item .code-copy-icon{ font-size: {{cpdwIconSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'copiedIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item .code-copied-icon{ font-size: {{cpdwIconSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ], ['key' => 'dwnldIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a .code-download-icon{ font-size: {{cpdwIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button{ color: {{copyDwlBtnNColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a{ color: {{copyDwlBtnNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover{ color: {{copyDwlBtnHColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover{ color: {{copyDwlBtnHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyDwlIconNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'copyIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button .code-copy-icon{ color: {{copyDwlIconNColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'copiedIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button .code-copied-icon{ color: {{copyDwlIconNColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ], ['key' => 'dwnldIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a .code-download-icon{ color: {{copyDwlIconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyDwlIconHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'copyIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover .code-copy-icon{ color: {{copyDwlIconHColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'copiedIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover .code-copied-icon{ color: {{copyDwlIconHColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ], ['key' => 'dwnldIcnType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover .code-download-icon{ color: {{copyDwlIconHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnNBorder' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnHBorder' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnNRadius' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button{border-radius: {{copyDwlBtnNRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a{border-radius: {{copyDwlBtnNRadius}};}',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnHRadius' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover{border-radius: {{copyDwlBtnHRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover{border-radius: {{copyDwlBtnHRadius}};}',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnNShadow' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a',
				],
			],
			'scopy' => true,
		],
		'copyDwlBtnHShadow' => [
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
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > button:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'dnloadBtn', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} div.code-toolbar .toolbar-item > a:hover',
				],
			],
			'scopy' => true,
		],
		/* Copy/Download Style End */
	];
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-code-highlighter', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_code_highlighter_render_callback'
    ) );
}
add_action( 'init', 'tpgb_code_highlighter' );