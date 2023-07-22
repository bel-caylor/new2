<?php
/* Block : BlockQuote
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_blockquote_callback($attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $quoteIcon = (!empty($attributes['quoteIcon'])) ? $attributes['quoteIcon'] : '' ;
    $quotecnt = (!empty($attributes['content'])) ? $attributes['content'] : '' ;
	
    if(class_exists('Tpgbp_Pro_Blocks_Helper')){
        $quotecnt = (!empty($attributes['content'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['content'],['blockName' => 'tpgb/tp-blockquote']) : '';
	}

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
    $output ='<div class="tp-blockquote tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
        $output .='<div class="tpgb-blockquote-inner tpgb-quote-'.esc_attr($attributes['style']).'">';
            if($attributes['style'] == 'style-2') {
                $output .= '<span class="tpgb-quote-left"><i class=" '.(!empty($quoteIcon) ? $quoteIcon : 'fa fa-quote-left' ).' " aria-hidden="true"></i></span>';
            }
            $output .= '<blockquote class="tpgb-quote-text">';
            $output .= '<div class="quote-text-wrap">'.wp_kses_post($quotecnt).'</div>';
            if($attributes['style'] == 'style-2' && !empty($attributes['authorName'])) {
                $output .= '<div class="tpgb-quote-author">'.wp_kses_post($attributes['authorName']).'</div>';
            }
            $output .= '</blockquote>';
        $output .='</div>';
    $output .='</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

function tpgb_tp_blockquote_render() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'style' => [
            'type' => 'string',
            'default' => 'style-1',
        ],
        'content' => [
            'type' => 'string',
            'default' => "You can't connect the dots looking forward; you can only connect them looking backwards. So you have to trust that the dots will somehow connect in your future."
        ],
        'authorName' => [
            'type' => 'string',
            'default' => 'Steve Jobs',
        ],
        'contentAlignment' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner div{text-align: {{contentAlignment}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'typography' => [
            'type' => 'object',
            'default' => (object) [
                'openTypography' => 0,
                'size' => [ 'md' => '', 'unit' => 'px' ],
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner blockquote.tpgb-quote-text > span,{{PLUS_WRAP}} .tpgb-blockquote-inner blockquote.tpgb-quote-text',
                ],
            ],
			'scopy' => true,
        ],
        'textNormalColor' => [
            'type' => 'string',
            'default' => '#747474',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-text{color: {{textNormalColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'textHoverColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-text:hover{color: {{textHoverColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'authorNormalColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-text .tpgb-quote-author{color: {{authorNormalColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'authorHoverColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-text .tpgb-quote-author:hover{color: {{authorHoverColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'quoteColor' => [
            'type' => 'string',
            'default' => '#888',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-left{color: {{quoteColor}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner{padding: {{boxPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'boxMargin' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner{margin: {{boxMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        'borderNormal' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
                'type' => '',
                    'color' => '',
                'width' => (object) [
                    'md' => (object)[
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                    "unit" => "",
                ],
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner',
                ],
            ],
			'scopy' => true,
        ],
        'borderHover' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
                'type' => '',
                    'color' => '',
                'width' => (object) [
                    'md' => (object)[
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                    "unit" => "",
                ],
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner:hover',
                ],
            ],
			'scopy' => true,
        ],
        'borderRadius' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner{border-radius: {{borderRadius}};}',
                ],
            ],
			'scopy' => true,
        ],
        'HvrborderRadius' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner:hover{border-radius: {{HvrborderRadius}};}',
                ],
            ],
			'scopy' => true,
        ],
        'catBg' => [
            'type' => 'object',
            'default' => (object) [
                'bgType' => 'color',
                'bgDefaultColor' => '',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner',
                ],
            ],
			'scopy' => true,
        ],
        'catBgHover' => [
            'type' => 'object',
            'default' => (object) [
                'bgType' => 'color',
                'bgDefaultColor' => '',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner:hover',
                ],
            ],
			'scopy' => true,
        ],
        'catBoxShadow' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner',
                ],
            ],
			'scopy' => true,
        ],
        'catBoxShadowHover' => [
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner:hover',
                ],
            ],
			'scopy' => true,
        ],
        'quoteIcon' => [
            'type'=> 'string',
	        'default'=> '',
        ],
        'qiconSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '60',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-blockquote-inner .tpgb-quote-left{ font-size : {{qiconSize}}; }',
                ],
            ],
        ],
    ];

    $attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-blockquote', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_blockquote_callback'
    ));
}
add_action( 'init', 'tpgb_tp_blockquote_render' );