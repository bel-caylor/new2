<?php
/* Block : Tp Heading Animation
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_heading_animation_callback( $attributes, $content) {

    $block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
    $style = isset($attributes['style']) ? $attributes['style'] : 'highlights';
    $titleTag = isset($attributes['titleTag']) ? $attributes['titleTag'] : 'div';
    $prefixText = isset($attributes['prefixText']) ? $attributes['prefixText'] : '';
    $postfixText = isset($attributes['postfixText']) ? $attributes['postfixText'] : '';
    $animText = isset($attributes['animText']) ? $attributes['animText'] : '';
    $textAnimStyle = isset($attributes['textAnimStyle']) ? $attributes['textAnimStyle'] : 'style-1';
    $highLightStyle = isset($attributes['highLightStyle']) ? $attributes['highLightStyle'] : 'underline';
    $highlightsText = isset($attributes['highlightsText']) ? $attributes['highlightsText'] : '';
	$durationTiming = isset($attributes['durationTiming']) ? array_map('intval', explode('|', $attributes['durationTiming'])) : 600;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$settings = [];
	$settings['style'] = $style;
	$settings['animStyle'] = $textAnimStyle;
	$settings = json_encode($settings);
	
	$headingAnim    = '';
    $headingAnim .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).' class="tpgb-heading-animation tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' tpgb-animation-head" data-settings=\'' . $settings . '\'>';
		if(!empty($prefixText)){
			$headingAnim .= '<span class="heading-prefix">'.wp_kses_post($prefixText).'</span>';
		}
		if(!empty($animText) && $style=='textAnim'){
			$headingAnim .= '<span class="heading-text-wrap heading-text-'.esc_attr($textAnimStyle).'">';
			foreach($animText as $key => $item){
				if(!empty($item['singleText'])){
					$singleText ='';
					if($textAnimStyle=='style-2' || $textAnimStyle=='style-3' || $textAnimStyle=='style-5' || $textAnimStyle=='style-6' || $textAnimStyle=='style-7' || $textAnimStyle=='style-8'){
						$text = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['singleText']);
						$text = wp_strip_all_tags($text);
						$splitText = tpgb_str_split_unicode($text);
						foreach($splitText as $itemkey=>$val){
							$singleText .= '<span class="letter'.(!$key ? ' letter-anim-in' : '').'">'.wp_kses_post($val).'</span>';
						}
					}else{
						$singleText = wp_kses_post($item['singleText']);
					}
					$duration_attr = '';
					if( $textAnimStyle=='style-1' ){
						$duration = (isset($durationTiming[0]) && !empty($durationTiming[0])) ? $durationTiming[0] : 600;
						if( !empty($durationTiming) && isset($durationTiming[$key]) && !empty($durationTiming[$key]) ){
							$duration = $durationTiming[$key];
						}
						$duration_attr = ' data-duration="'.esc_attr($duration).'"';
					}
					$headingAnim .= '<span class="heading-anim-text heading-word-'.($key+1).' '.(!$key ? ' heading-text-active' : '').'" '.$duration_attr.' >'.$singleText.'</span>';
				}
			}
			$headingAnim .= '</span>';	
		}
		if($style=='highlights' && !empty($highLightStyle) && !empty($highlightsText)){
			$SvgPath = $SvgPath1= '';
			$SvgView = '0 0 500 150';
			if($highLightStyle=='underline'){
				$SvgPath ="M7,140.9c30.6-3.8,192.1-23.5,326.4-20.7c134.3,2.7,163.7,14.2,161.5,24.6";
			}else if($highLightStyle=='widecircle'){
				$SvgPath = "M161.9,18.9c68.6-21,108.9-25,180.1-9.6C400.8,22,456.6,48.8,484.1,89.4s19.8,95.7-27.4,124.5	c-17.1,10.4-37.8,16.9-58.9,21.9c-77.4,18.4-160.1,19.5-237.4-0.8c-32.8-8.6-64.8-21.5-95.4-38.4c-13-7.2-25.7-15.2-37-25.9	C5.8,149.6-9.3,116,6.6,82.6C16.8,61.2,35.1,49,52.4,38.1c33.9-21.4,71.4-34.7,107.3-37c43.4-2.9,86.6,7.8,129.4,20";
				$SvgView = '0 0 500 250';
			}else if($highLightStyle=='tinycirlce'){
				$SvgPath = "M39.3,24.8C51.2,12,76.4,7.1,93,11.7c63.6,17.9,63.8,120.7-6.2,127.9c-38.1,4-69.6-27.6-75.7-62.5	C6.4,51.1,16.9,20,45.7,12.7c0.4-0.1,0.7-0.2,1.1-0.3c2.4-0.6,4.9-0.9,7.4-1.1c5.2-0.4,10.5-0.2,15.7,0.1c5.1,0.3,8.4,1,17.4,4.8";
				$SvgView = '0 0 150 150';
			}else if($highLightStyle=='zigzag'){
				$SvgPath = "M15,113.4c0,0,365.3-21.2,514,0c0,0-414.3,8.4-442.4,15.8c69.3-5.9,341.1,3.9,348.1,5.4c7,1.5-141-8.4-204,12.3";
			}else if($highLightStyle=='checkmark'){
				$SvgPath = "M22,84.6c15.4,16,26.4,39.2,39.9,60.4C192.1,84.7,337.1,49.6,482,7";
			}else if($highLightStyle=='crossmark'){
				$SvgPath = "M30,24.9c69.4,8,138.2,20.1,206.3,35.4c69.6,15.6,138.5,34.5,206.3,56.6c24.3,7.9,48.4,16.8,72.4,25.5";
				$SvgPath1 = "M515,7.6C445,23,375.5,40.5,306.6,60.2s-137.1,41.5-204.6,65.5c-15.1,5.3-29.9,11.1-44.9,16.6";
				$SvgView = '0 0 540 150';
			}else if($highLightStyle=='curlyline'){
				$SvgPath = "M6,141.4c22.1-15.3,45.9-26.9,56.6-20.1s14.9,21.1,29.8,20.1c14.9-1,36.4-29.3,50.7-24.5s22.1,24.6,38.1,24.5	c16.1,0,37.6-29,50.1-25.1c12.5,3.9,20.3,27,37.6,25.1c17.3-2,31.6-21.1,45.9-20.2c14.3,1,16.1,22.9,35.2,20.3	c19.1-2.6,36.9-23.2,47.4-22.2c10.5,0.9,18.8,25.7,35,25.7c16.2,0,47.7-12.4,61.3-21.5";
			}else if($highLightStyle=='diagonal'){
				$SvgPath = "M7,2.1C157.5,31.2,300.2,77,432.7,125.2c20.3,7.4,40.4,15,60.3,22.7";
			}else if($highLightStyle=='doubleline'){
				$SvgPath = "M495,8.7C330.7,2.1,165.9,7.7,2.8,25.5";
				$SvgPath1 = "M3,145c163.2-14.8,327.7-19.5,491.8-13.9";
			}else if($highLightStyle=='doubleunderline'){
				$SvgPath = "M0,104.9c166.7-5.1,333.7-0.9,500,12.6";
				$SvgPath1 = "M15.2,125.1c153.1-6.9,306.9-0.3,458.8,19.7";
			}else if($highLightStyle=='exclamationmark'){
				$SvgPath = "M8.7,108.6C8.4,89.2,8,69.7,7.7,50.3c-0.2-13.3-0.4-27,4.8-39.2c1.3-3,4-6.4,7.1-5.3c1.9,0.7,2.9,2.8,3.5,4.7	c4.6,14.1,2.9,29.4-0.4,43.9S14.6,82.9,13,97.6";
				$SvgPath1 = "M16.4,119.4c4.1,4.5,6,11.5,5.8,17.3c-0.1,3.6-1.8,7.5-5,9.2c-5.3,2.9-9.8-0.5-12.3-5.2c-3-5.8-4.1-13.8-1.4-20	c1.1-2.4,3.2-4.5,5.8-4.8c1.7-0.2,3.5,0.5,4.9,1.5C15,118,15.8,118.7,16.4,119.4z";
				$SvgView = '0 0 40 150';
			}
			
			$headingAnim .= '<span class="heading-highlights heading-text-wrap heading-highlight-'.esc_attr($highLightStyle).'">';
				$headingAnim .= '<span class="heading-anim-text">'.wp_kses_post($highlightsText).'</span>';
				if($highLightStyle!='tinycirlce'){
					$headingAnim .= '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="'.esc_attr($SvgView).'" preserveAspectRatio="none">';
						$headingAnim .= '<path d="'.$SvgPath.'" />';
							$headingAnim .= ($SvgPath1!='' ? '<path d="'.$SvgPath1.'" />' : '');
						$headingAnim .= '</svg>';
				}else{
					$headingAnim .= '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="'.esc_attr($SvgView).'">';
						$headingAnim .= '<path d="'.$SvgPath.'" />';
					$headingAnim .= '</svg>';
				}
			$headingAnim .= '</span>';
		}
		if(!empty($postfixText)){
			$headingAnim .= '<span class="heading-postfix">'.wp_kses_post($postfixText).'</span>';
		}
    $headingAnim .= '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).'>';
	
	$headingAnim = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $headingAnim);
	
    return $headingAnim;
}

function tpgb_str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * Render for the server-side
 */
function tpgb_tp_heading_animation_render() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
                'default' => '',
            ],
			'style' => [
                'type' => 'string',
                'default' => 'highlights',
            ],
			'highLightStyle' => [
                'type' => 'string',
                'default' => 'underline',
            ],
			'textAnimStyle' => [
                'type' => 'string',
                'default' => 'style-1',
            ],
			'prefixText' => [
                'type' => 'string',
                'default' => 'We are ',
            ],
			'highlightsText' => [
                'type' => 'string',
                'default' => 'Innovative',
            ],
			'animText' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'singleText' => [
							'type' => 'string',
							'default' => 'Creative',
						],
					],
				], 
				'default' => [ 
					[ '_key'=> 'cvi9', 'singleText' => 'Innovative'],
					[ '_key'=> 'sci9', 'singleText' => 'Creative'],
				],
			],
			'postfixText' => [
                'type' => 'string',
                'default' => '',
            ],
			'durationTiming' => [
                'type' => 'string',
                'default' => '600',
            ],
			'alignment' => [
                'type' => 'object',
                'default' => (object)['md' => 'center'],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}{ text-align: {{alignment}}; }',
					],
				],
				'scopy' => true,
            ],
			'titleTag' => [
				'type' => 'string',
                'default' => 'div',
				'scopy' => true,
			],
			'textTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-heading-animation.tpgb-animation-head',
					],
				],
				'scopy' => true,
			],
			'textColor' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-heading-animation.tpgb-animation-head{ color: {{textColor}}; }',
					],
				],
				'scopy' => true,
            ],
			'animTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .heading-text-wrap',
					],
				],
				'scopy' => true,
			],
			'animColor' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .heading-text-wrap{ color: {{animColor}}; }',
					],
				],
				'scopy' => true,
            ],
			'lineColor' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'textAnim'],
							['key' => 'textAnimStyle', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .heading-text-style-1:after{ background: {{lineColor}}; }',
					],
				],
				'scopy' => true,
            ],
			'AnimationDur' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'highlights']],
						'selector' => '{{PLUS_WRAP}} .heading-highlights svg path{ animation-duration : {{AnimationDur}}s; }',
					],
				],
				'scopy' => true,
            ],
			'strokeWidth' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'highlights']],
						'selector' => '{{PLUS_WRAP}} .heading-highlights svg path{ stroke-width: {{strokeWidth}}; }',
					],
				],
				'scopy' => true,
            ],
			'strokeColor' => [
                'type' => 'string',
                'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'highlights']],
						'selector' => '{{PLUS_WRAP}} .heading-highlights svg path{ stroke: {{strokeColor}}; }',
					],
				],
				'scopy' => true,
            ],
		);
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
    register_block_type( 'tpgb/tp-heading-animation', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_heading_animation_callback'
    ));
}
add_action( 'init', 'tpgb_tp_heading_animation_render' );