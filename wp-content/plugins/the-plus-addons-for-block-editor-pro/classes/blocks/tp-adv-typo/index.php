<?php
/* Block : Advanced Typography
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_adv_typo_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$typoListing = (!empty($attributes['typoListing'])) ? $attributes['typoListing'] : 'normal';
	$typoText = (!empty($attributes['typoText'])) ? $attributes['typoText'] : '';
	$textListing = (!empty($attributes['textListing'])) ? $attributes['textListing'] : [];

	$strokeFill = (!empty($attributes['strokeFill'])) ? $attributes['strokeFill'] : false;
	$knockoutText = (!empty($attributes['knockoutText'])) ? $attributes['knockoutText'] : false;

	$cirTextEn = (!empty($attributes['cirTextEn'])) ? $attributes['cirTextEn'] : false;
	$customRadius = (!empty($attributes['customRadius'])) ? $attributes['customRadius'] : '';
	$revDirection = (!empty($attributes['revDirection'])) ? $attributes['revDirection'] : false;

	$blendMode = (!empty($attributes['blendMode'])) ? $attributes['blendMode'] : false;

	$marquee = (!empty($attributes['marquee'])) ? $attributes['marquee'] : false;
	$marqueeType = (!empty($attributes['marqueeType'])) ? $attributes['marqueeType'] : 'default';
	$marqueeDir = (!empty($attributes['marqueeDir'])) ? $attributes['marqueeDir'] : 'left';
	$marqueeBeh = (!empty($attributes['marqueeBeh'])) ? $attributes['marqueeBeh'] : 'initial';
	$marqueeLoop = (!empty($attributes['marqueeLoop'])) ? $attributes['marqueeLoop'] : '';
	$marqueeScroll = (!empty($attributes['marqueeScroll'])) ? $attributes['marqueeScroll'] : '';
	$marqueeAni = (!empty($attributes['marqueeAni'])) ? $attributes['marqueeAni'] : '';

	$onHoverImg = (!empty($attributes['onHoverImg'])) ? $attributes['onHoverImg'] : false;
	$hoverImg = (!empty($attributes['hoverImg'])) ? $attributes['hoverImg'] : '';
	$hoverStyle = (!empty($attributes['hoverStyle'])) ? $attributes['hoverStyle'] : '1';

	$advUnderline = (!empty($attributes['advUnderline'])) ? $attributes['advUnderline'] : 'none';
	$overlayStyle = (!empty($attributes['overlayStyle'])) ? $attributes['overlayStyle'] : 'style-1';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$innerClass = $strokeClass = $marqueeClass= $marqueeStyle = $advLineCls=$circular_attr='';
	if($typoListing=='normal'){
		$innerClass = 'tpgb-adv-single-typo tpgb-trans-linear';
		if(!empty($strokeFill)){
			$strokeClass = 'typo_stroke';
		}

		if(!empty($knockoutText)){
			$strokeClass .= ' typo_gif_based_text';
		}
		if(!empty($blendMode)){
			$strokeClass .= ' typo_bg_based_text';
		}

		if($advUnderline=='overlay'){
			$advLineCls = 'under_overlay overlay-'.$overlayStyle;
		}

		if(!empty($cirTextEn)){
			$strokeClass .= ' typo_circular';
			if(!empty($customRadius)){
				$circular_attr .= ' data-custom-radius="' . esc_attr($customRadius) . '" ';
			}
			if(!empty($revDirection)){				
				$circular_attr .= ' data-custom-reversed="yes" ';
			}
		}
	}else{
		$innerClass = 'tpgb-adv-list-typo';
	}

	if(!empty($marquee) && $marqueeType=='on_transition' && !empty($marqueeDir)){
		$marqueeClass = 'tpgb_adv_typo_'.esc_attr($marqueeDir);
		if(!empty($marqueeAni)){
			$marqueeStyle = '.tpgb-adv-typo .'.esc_attr($marqueeClass).' { animation: '.esc_attr($marqueeClass).' '.esc_attr($marqueeAni).'s linear infinite; }';
		}
	}
	
	$output = '';
    $output .= '<div class="tpgb-adv-typo tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		$output .='<div class="'.esc_attr($innerClass).' '.esc_attr($advLineCls).'">';

		if($typoListing=='normal'){
			if(!empty($typoText)){
				if(!empty($onHoverImg) && !empty($hoverImg)){
					$output .= '<div class="tpgb-block" data-fx="'.esc_attr($hoverStyle).'">';
						$output .= '<p class="block__title" data-img="'.esc_attr($hoverImg['url']).'">';
				}

				if(!empty($marquee) && $marqueeType=='default'){
					$output .= '<marquee id="tpgb-adv-'.esc_attr($block_id).'" class="text-content-block '.esc_attr($strokeClass).'" direction="'.esc_attr($marqueeDir).'" behavior="'.esc_attr($marqueeBeh).'" loop="'.esc_attr($marqueeLoop).'" scrollamount="'.esc_attr($marqueeScroll).'" scrolldelay="'.esc_attr($marqueeAni).'" '.$circular_attr.' >'.wp_kses_post( $typoText ).'</marquee>';
				}

				if(empty($marquee) || (!empty($marquee) && $marqueeType=='on_transition')){
					$output .= '<a href="#" id="tpgb-adv-'.esc_attr($block_id).'" class="text-content-block '.esc_attr($strokeClass).' '.esc_attr($marqueeClass).'" '.$circular_attr.' aria-label="'.esc_attr($typoText).'">'.wp_kses_post( $typoText ).'</a>';
				}
				
				if(!empty($onHoverImg) && !empty($hoverImg)){
					$output .= '</p></div>';
				}

				if(!empty($marqueeStyle)){
					$output .= '<style>'.wp_strip_all_tags($marqueeStyle).'</style>';
				}
			}
		}else if($typoListing=='multiple'){
			if(!empty($textListing)){
				foreach ( $textListing as $index => $item ) :
					$dataClass = $advLineClass = $transMarqueeStyle = $transMarqueeClass = $text_cont_animation = '';
					if(!empty($item['strokeFill'])){
						$dataClass .= 'list_typo_stroke';
					}
					if(!empty($item['knockoutText'])){
						$dataClass .= ' typo_gif_based_text';
					}

					if(!empty($item['contiAnimation'])){
						$text_animation_class = '';
						if(!empty($item['aniOnHover'])){
							$text_animation_class = 'hover_';
						}else{
							$text_animation_class = 'image-';
						}
						$text_cont_animation = $text_animation_class.$item['aniEffect'];
					}
					
					if(!empty($item['marquee']) && $item['marqueeType']=='on_transition' && !empty($item['marqueeDir'])){
						$transMarqueeClass = 'tpgb_adv_typo_'.esc_attr($item['marqueeDir']);
						if(!empty($item['marqueeAni'])){
							$transMarqueeStyle = '.tpgb-adv-typo .tp-repeater-item-'.esc_attr($item['_key']).' .'.esc_attr($transMarqueeClass).' { animation: '.esc_attr($transMarqueeClass).' '.esc_attr($item['marqueeAni']).'s linear infinite; }';
						}
					}

					if($item['advUnderline']=='overlay'){
						$advLineClass = 'under_overlay overlay-'.esc_attr($item['overlayStyle']);
					}
					$output .= '<div class="tpgb-text-typo tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($advLineClass).'">';

						if(!empty($item['onHoverImg']) && !empty($item['hoverImg'])){
							$output .= '<div class="tpgb-block" data-fx="'.$item['hoverStyle'].'">';
								$output .= '<p class="block__title" data-img="'.esc_attr($item['hoverImg']['url']).'">';
						}

						if(!empty($item['marquee']) && $item['marqueeType']=='default'){

							$mDir = ($item['marqueeDir']) ? $item['marqueeDir'] : '';
							$mBeh = ($item['marqueeBeh']) ? $item['marqueeBeh'] : '';
							$mLoop = ($item['marqueeLoop']) ? $item['marqueeLoop'] : '';
							$mScrl = ($item['marqueeScroll']) ? $item['marqueeScroll'] : '';
							$mAni = ($item['marqueeAni']) ? $item['marqueeAni'] : '';

							$output .= '<marquee class="list-typo-text '.esc_attr($dataClass).' '.esc_attr($text_cont_animation).'" direction="'.esc_attr($mDir).'" behavior="'.esc_attr($mBeh).'" loop="'.esc_attr($mLoop).'" scrollamount="'.esc_attr($mScrl).'" scrolldelay="'.esc_attr($mAni).'">'.wp_kses_post($item['lText']).'</marquee>';
						}

						$textLink = (!empty($item['linkUrl']['url'])) ? $item['linkUrl']['url'] : '';
						$target = (!empty($item['linkUrl']['target'])) ? '_blank' : '';
						$nofollow = (!empty($item['linkUrl']['nofollow'])) ? 'nofollow' : '';
						$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['linkUrl']);
						if(!empty($textLink)){
							if(empty($item['marquee']) || (!empty($item['marquee']) && $item['marqueeType']=='on_transition')){
								$ariaLabelT = (!empty($item['ariaLabel'])) ? $item['ariaLabel'] : $item['lText'];
								$output .= '<a class="list-typo-text '.esc_attr($dataClass).' '.esc_attr($transMarqueeClass).' '.esc_attr($text_cont_animation).'" href="'.esc_url($textLink).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.esc_attr($ariaLabelT).'">'.wp_kses_post($item['lText']).'</a>';
							}
						}else{
							if(empty($item['marquee']) || (!empty($item['marquee']) && $item['marqueeType']=='on_transition')){
								$output .= '<span class="list-typo-text '.esc_attr($dataClass).' '.esc_attr($transMarqueeClass).' '.esc_attr($text_cont_animation).'">'.wp_kses_post($item['lText']).'</span>';
							}
						}

						
						if(!empty($transMarqueeStyle)){
							$output .= '<style>'.wp_strip_all_tags($transMarqueeStyle).'</style>';
						}

						if(!empty($item['onHoverImg']) && !empty($item['hoverImg'])){
							$output .= '</p></div>';
						}
						
					$output .= '</div>';

				endforeach;
			}
		}
		$output .= '</div>';
		
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_adv_typo() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'typoListing' => [
			'type' => 'string',
			'default' => 'normal',
		],
		'typoText' => [
			'type' => 'string',
			'default' => 'Your Text Content',
		],
		'textListing' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'lText' => [
						'type' => 'string',
						'default' => 'Your Text Content'
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=>[
							'url' => '#',	
							'target' => '',	
							'nofollow' => ''	
						]
					],
					'ariaLabel' => [
						'type' => 'string',
						'default' => '',	
					],
					'strokeFill' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'strokeWidth' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text { -webkit-text-stroke-width: {{strokeWidth}}; }',
							],
						],
						'scopy' => true,
					],
					'gradientTgl' => [
						'type' => 'boolean',
						'default' => false,	
					],
					
					'strokeNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'gradientTgl', 'relation' => '!=', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ -webkit-text-stroke-color: {{strokeNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'strokeHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'gradientTgl', 'relation' => '!=', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ -webkit-text-stroke-color: {{strokeHColor}}; }',
							],
						],
						'scopy' => true,
					],

					'strokeNGColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'gradientTgl', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ background: {{strokeNGColor}}; -webkit-background-clip: text;-webkit-text-stroke-color: transparent; }',
							],
						],
						'scopy' => true,
					],
					'strokeHGColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'gradientTgl', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ background: {{strokeHGColor}}; -webkit-background-clip: text;-webkit-text-stroke-color: transparent; }',
							],
						],
						'scopy' => true,
					],

					'fillNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ -webkit-text-fill-color: {{fillNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'fillHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'strokeFill', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ -webkit-text-fill-color: {{fillHColor}}; }',
							],
						],
						'scopy' => true,
					],

					'blendMode' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'blendVariation' => [
						'type' => 'string',
						'default' => 'color',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'blendMode', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ mix-blend-mode: {{blendVariation}}; }',
							],
						],
						'scopy' => true,
					],

					'knockoutText' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'koTextBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'knockoutText', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text',
							],
						],
						'scopy' => true,
					],
					'onHoverImg' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'hoverImg' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'hoverStyle' => [
						'type' => 'string',
						'default' => '1',
					],
					'marquee' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'marqueeType' => [
						'type' => 'string',
						'default' => 'default',
					],
					'marqueeDir' => [
						'type' => 'string',
						'default' => 'left',
					],
					'marqueeBeh' => [
						'type' => 'string',
						'default' => 'initial',
					],
					'marqueeLoop' => [
						'type' => 'number',
						'default' => -1,
					],
					'marqueeScroll' => [
						'type' => 'number',
						'default' => 6,
					],
					'marqueeAni' => [
						'type' => 'number',
						'default' => 3,
					],
					'marqueeTwidth' => [
						'type' => 'string',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'marqueeType', 'relation' => '==', 'value' => 'default' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} marquee { width: {{marqueeTwidth}}; max-width: {{marqueeTwidth}}; white-space: nowrap; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'marqueeType', 'relation' => '==', 'value' => 'on_transition' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {  width: {{marqueeTwidth}}; max-width: {{marqueeTwidth}}; display: inline-block; }',
							],
						],
						'scopy' => true,
					],
					
					'contiAnimation' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'aniEffect' => [
						'type' => 'string',
						'default' => 'pulse',	
					],
					'aniOnHover' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'aniDurTime' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'contiAnimation', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .image-pulse, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .hover_pulse:hover, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .image-floating, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .hover_floating:hover, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .image-tossing, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .hover_tossing:hover { animation-duration: {{aniDurTime}}s; -webkit-animation-duration: {{aniDurTime}}s; }',
							],
						],
					],
					'advUnderline' => [
						'type' => 'string',
						'default' => 'none',
					],
					'overlayStyle' => [
						'type' => 'string',
						'default' => 'style-1',
					],
					'nLineType' => [
						'type' => 'string',
						'default' => 'none',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ text-decoration-line: {{nLineType}}; }',
							],
						],
					],
					'hLineType' => [
						'type' => 'string',
						'default' => 'none',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ text-decoration-line: {{hLineType}}; }',
							],
						],
					],
					'nLineStyle' => [
						'type' => 'string',
						'default' => 'none',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'nLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ text-decoration-style: {{nLineStyle}}; }',
							],
						],
					],
					'hLineStyle' => [
						'type' => 'string',
						'default' => 'none',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'hLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ text-decoration-style: {{hLineStyle}}; }',
							],
						],
					],
					'nLineColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'nLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ text-decoration-color: {{nLineColor}}; }',
							],
						],
						'scopy' => true,
					],
					'hLineColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'hLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover{ text-decoration-color: {{hLineColor}}; }',
							],
						],
						'scopy' => true,
					],
					'ovBottomOff' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '==', 'value' => 'style-1' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-1:before { bottom: {{ovBottomOff}}; }',
							],
						],
						'scopy' => true,
					],
					'ovLineNHeight' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-1:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-2:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-3:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:before,{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:after, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-5:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-6:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-7:before { height: {{ovLineNHeight}}; }',
							],
						],
						'scopy' => true,
					],
					'ovLineHHeight' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '!=', 'value' => 'style-7' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-1:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-2:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-3:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:after, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-5:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-6:hover:before { height: {{ovLineHHeight}}; }',
							],
						],
						'scopy' => true,
					],
					'ovLineNBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-1:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-2:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-3:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:before,{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:after, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-5:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-6:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-7:before',
							],
						],
						'scopy' => true,
					],
					'ovLineHBG' => [
						'type' => 'object',
						'default' => (object) [
							'openBg'=> 0,
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '!=', 'value' => 'style-7' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-1:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-2:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-3:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-4:hover:after, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-5:hover:before, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.overlay-style-6:hover:before',
							],
						],
						'scopy' => true,
					],
					'textTypo' => [
						'type'=> 'object',
						'default'=> (object) [
							'openTypography' => 0,
							'size' => [ 'md' => '', 'unit' => 'px' ],
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text',
							],
						],
						'scopy' => true,
					],
					'textPadding' => [
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
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text {padding: {{textPadding}};}',
							],
						],
						'scopy' => true,
					],
					'advTextStyle' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'advTextWidth' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advTextStyle', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} { max-width: {{advTextWidth}}; width: {{advTextWidth}}; } {{PLUS_WRAP}} {{TP_REPEAT_ID}} { white-space: nowrap; }',
							],
						],
						'scopy' => true,
					],
					'advTextHZalign' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advTextStyle', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} { left: {{advTextHZalign}}; }',
							],
						],
						'scopy' => true,
					],
					'advTextVRalign' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'advTextStyle', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} { bottom: {{advTextVRalign}}; }',
							],
						],
						'scopy' => true,
					],

					'textNColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'textGradNtgl', 'relation' => '!=', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text{ color: {{textNColor}}; } {{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text.bg_based_text{ -webkit-text-fill-color: {{textNColor}}; }',
							],
						],
						'scopy' => true,
					],
					'textHColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'textGradHtgl', 'relation' => '!=', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover { color: {{textHColor}}; } {{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text.bg_based_text:hover { -webkit-text-fill-color: {{textHColor}}; }',
							],
						],
						'scopy' => true,
					],
					'textGradNtgl' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'textNGcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'textGradNtgl', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text { background: {{textNGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
							],
						],
						'scopy' => true,
					],
					'textGradHtgl' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'textHGcolor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'textGradHtgl', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover { background: {{textHGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
							],
						],
						'scopy' => true,
					],
					'textNshadow' => [
						'type' => 'object',
						'default' => (object) [
							'openShadow' => 0,
							'typeShadow' => 'text-shadow',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text',
							],
						],
						'scopy' => true,
					],
					'textHshadow' => [
						'type' => 'object',
						'default' => (object) [
							'openShadow' => 0,
							'typeShadow' => 'text-shadow',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover',
							],
						],
						'scopy' => true,
					],
					'textNFilter' => [
						'type' => 'object',
						'default' =>  [
							'openFilter' => false,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text',
							],
						],
						'scopy' => true,
					],		
					'textHFilter' => [
						'type' => 'object',
						'default' =>  [
							'openFilter' => false,
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover',
							],
						],
						'scopy' => true,
					],		
					'transNcss' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text { -webkit-transform: {{transNcss}}; -ms-transform: {{transNcss}}; -moz-transform: {{transNcss}}; transform: {{transNcss}}; transform-style: preserve-3d; -ms-transform-style: preserve-3d; -moz-transform-style: preserve-3d; -webkit-transform-style: preserve-3d; display: inline-block; }',
							],
						],
						'scopy' => true,
					],
					'transHcss' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .list-typo-text:hover { -webkit-transform: {{transHcss}};-ms-transform: {{transHcss}}; -moz-transform: {{transHcss}}; transform: {{transHcss}}; transform-style: preserve-3d; -ms-transform-style: preserve-3d; -moz-transform-style: preserve-3d; -webkit-transform-style: preserve-3d; display: inline-block; }',
							],
						],
						'scopy' => true,
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'lText' => 'Your Text Content',
					'linkUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'ariaLabel' => '',
					'gradientTgl' => false,
					'blendMode' => false,
					'blendVariation' => 'normal',
					'knockoutText' => false,
					'onHoverImg' => false,
					'hoverImg' => [],
					'hoverStyle' => '1',
					'marquee' => false,
					'marqueeType' => 'default',
					'marqueeDir' => 'left',
					'marqueeBeh' => 'initial',
					'marqueeLoop' => '-1',
					'marqueeScroll' => '',
					'marqueeAni' => '',
					'marqueeTwidth' => '',
					'contiAnimation' => false,
					'aniEffect' => 'pulse',
					'aniOnHover' => false,
					'aniDurTime' => '',
					'advUnderline' => 'none',
					'overlayStyle' => 'style-1',
					'nLineType' => 'none',
					'nLineStyle' => 'solid',
					'hLineType' => 'none',
					'hLineStyle' => 'solid',
					'textNshadow' => [
						'typeShadow' => 'text-shadow',
					],
					'textHshadow' => [
						'typeShadow' => 'text-shadow',
					],
				],
			],
		],
		'textAlign' => [
			'type' => 'object',
			'default' => 'left',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} { text-align: {{textAlign}}; }',
				],
			],
			'scopy' => true,
		],

		'textMode' => [
			'type' => 'string',
			'default' => 'unset',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'textMode', 'relation' => '!=', 'value' => 'unset' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { max-block-size: max-content; writing-mode: {{textMode}}; -webkit-writing-mode: {{textMode}}; -ms-writing-mode: {{textMode}}; }',
				],
			],
			'scopy' => true,
		],
		'verLetters' => [
			'type' => 'boolean',
			'default' => false,	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'verLetters', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { text-orientation: upright; }',
				],
			],
			'scopy' => true,
		],
		'textDirection' => [
			'type' => 'string',
			'default' => 'initial',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'textDirection', 'relation' => '!=', 'value' => 'initial' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { unicode-bidi: bidi-override; direction: {{textDirection}} }',
				],
			],
			'scopy' => true,
		],

		'cirTextEn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'customRadius' => [
			'type' => 'string',
			'default' => '',	
		],
		'revDirection' => [
			'type' => 'boolean',
			'default' => false,	
		],

		'blendMode' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'blendVariation' => [
			'type' => 'string',
			'default' => 'normal',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'blendMode', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_bg_based_text{ mix-blend-mode: {{blendVariation}}; }',
				],
			],
			'scopy' => true,
		],

		'knockoutText' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'koTextBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'image',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'knockoutText', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block',
				],
			],
			'scopy' => true,
		],

		'onHoverImg' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'hoverImg' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'hoverStyle' => [
			'type' => 'string',
			'default' => '1',
		],
		'marquee' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'marqueeType' => [
			'type' => 'string',
			'default' => 'default',
		],
		'marqueeDir' => [
			'type' => 'string',
			'default' => 'left',
		],
		'marqueeBeh' => [
			'type' => 'string',
			'default' => 'initial',
		],
		'marqueeLoop' => [
			'type' => 'number',
			'default' => -1,
		],
		'marqueeScroll' => [
			'type' => 'number',
			'default' => 6,
		],
		'marqueeAni' => [
			'type' => 'number',
			'default' => 3,
		],
		'marqueeTwidth' => [
			'type' => 'string',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'marqueeType', 'relation' => '==', 'value' => 'default' ] ],
					'selector' => '{{PLUS_WRAP}} marquee { width: {{marqueeTwidth}}; max-width: {{marqueeTwidth}}; white-space: nowrap; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'marqueeType', 'relation' => '==', 'value' => 'on_transition' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo {  width: {{marqueeTwidth}}; max-width: {{marqueeTwidth}}; display: inline-block; }',
				],
			],
			'scopy' => true,
		],

		/* Style Start */
		'textTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo .text-content-block',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-list-typo .list-typo-text',
				],
			],
			'scopy' => true,
		],
		'textPadding' => [
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
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo .text-content-block {padding: {{textPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-list-typo .list-typo-text {padding: {{textPadding}};}',
				],
			],
			'scopy' => true,
		],

		'textNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'textGradNtgl', 'relation' => '!=', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block{ color: {{textNColor}}; } {{PLUS_WRAP}} .text-content-block.bg_based_text{ -webkit-text-fill-color: {{textNColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ], ['key' => 'textGradNtgl', 'relation' => '!=', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text{ color: {{textNColor}}; } {{PLUS_WRAP}} .list-typo-text.bg_based_text{ -webkit-text-fill-color: {{textNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'textGradNtgl' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'textNGcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'textGradNtgl', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { background: {{textNGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ], ['key' => 'textGradNtgl', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text { background: {{textNGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
				],
			],
			'scopy' => true,
		],
		'textNshadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'typeShadow' => 'text-shadow',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text',
				],
			],
			'scopy' => true,
		],
		'textNFilter' => [
			'type' => 'object',
			'default' =>  [
				'openFilter' => false,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text',
				],
			],
			'scopy' => true,
		],	
		'transNcss' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { -webkit-transform: {{transNcss}}; -ms-transform: {{transNcss}}; -moz-transform: {{transNcss}}; transform: {{transNcss}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text { -webkit-transform: {{transNcss}}; -ms-transform: {{transNcss}}; -moz-transform: {{transNcss}}; transform: {{transNcss}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d; }',
				],
			],
			'scopy' => true,
		],
		'transHcss' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover { -webkit-transform: {{transHcss}};-ms-transform: {{transHcss}}; -moz-transform: {{transHcss}}; transform: {{transHcss}};  }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover { -webkit-transform: {{transHcss}};-ms-transform: {{transHcss}}; -moz-transform: {{transHcss}}; transform: {{transHcss}};  }',
				],
			],
			'scopy' => true,
		],
		'transOrigin' => [
			'type' => 'string',
			'default' => 'center',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block { transform-origin: {{transOrigin}};  }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover { transform-origin: {{transOrigin}}; }',
				],
			],
			'scopy' => true,
		],

		'textHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'textGradHtgl', 'relation' => '!=', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover { color: {{textHColor}}; } {{PLUS_WRAP}} .text-content-block.bg_based_text{ -webkit-text-fill-color: {{textHColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ], ['key' => 'textGradHtgl', 'relation' => '!=', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover { color: {{textHColor}}; } {{PLUS_WRAP}} .list-typo-text.bg_based_text{ -webkit-text-fill-color: {{textHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'textGradHtgl' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'textHGcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'textGradHtgl', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover { background: {{textHGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ], ['key' => 'textGradHtgl', 'relation' => '==', 'value' => true ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover { background: {{textHGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-fill-color: transparent; }',
				],
			],
			'scopy' => true,
		],
		'textHshadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'typeShadow' => 'text-shadow',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover',
				],
			],
			'scopy' => true,
		],
		'textHFilter' => [
			'type' => 'object',
			'default' =>  [
				'openFilter' => false,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover',
				],
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'multiple' ] ],
					'selector' => '{{PLUS_WRAP}} .list-typo-text:hover',
				],
			],
			'scopy' => true,
		],	
		
		'strokeFill' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'strokeWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke, {{PLUS_WRAP}} .text-content-block.typo_stroke span { -webkit-text-stroke-width: {{strokeWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'strokeNGrad' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'strokeNcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'strokeNGrad', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke, {{PLUS_WRAP}} .text-content-block.typo_stroke span { -webkit-text-stroke-color: {{strokeNcolor}};}',
				],
			],
			'scopy' => true,
		],
		'strokeNGcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'strokeNGrad', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke, {{PLUS_WRAP}} .text-content-block.typo_stroke span { background: {{strokeNGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-stroke-color: transparent; }',
				],
			],
			'scopy' => true,
		],
		'fillNcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke, {{PLUS_WRAP}} .text-content-block.typo_stroke span { -webkit-text-fill-color: {{fillNcolor}};}',
				],
			],
			'scopy' => true,
		],

		'strokeHGrad' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'strokeHcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'strokeHGrad', 'relation' => '==', 'value' => false ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke:hover, {{PLUS_WRAP}} .text-content-block.typo_stroke:hover span { -webkit-text-stroke-color: {{strokeHcolor}};}',
				],
			],
			'scopy' => true,
		],
		'strokeHGcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ], ['key' => 'strokeHGrad', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke:hover, {{PLUS_WRAP}} .text-content-block.typo_stroke:hover span { background: {{strokeHGcolor}}; background-color: transparent; -webkit-background-clip: text; -webkit-text-stroke-color: transparent; }',
				],
			],
			'scopy' => true,
		],
		'fillHcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'typoListing', 'relation' => '==', 'value' => 'normal' ], ['key' => 'strokeFill', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .text-content-block.typo_stroke:hover, {{PLUS_WRAP}} .text-content-block.typo_stroke:hover span { -webkit-text-fill-color: {{fillHcolor}};}',
				],
			],
			'scopy' => true,
		],

		'advUnderline' => [
			'type' => 'string',
			'default' => 'none',
		],
		'overlayStyle' => [
			'type' => 'string',
			'default' => 'style-1',
		],
		'nLineType' => [
			'type' => 'string',
			'default' => 'none',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block{ text-decoration-line: {{nLineType}}; }',
				],
			],
		],
		'hLineType' => [
			'type' => 'string',
			'default' => 'none',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover{ text-decoration-line: {{hLineType}}; }',
				],
			],
		],
		'nLineStyle' => [
			'type' => 'string',
			'default' => 'none',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'nLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block{ text-decoration-style: {{nLineStyle}}; }',
				],
			],
		],
		'hLineStyle' => [
			'type' => 'string',
			'default' => 'none',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'hLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover{ text-decoration-style: {{hLineStyle}}; }',
				],
			],
		],
		'nLineColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'nLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block{ text-decoration-color: {{nLineColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hLineColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'hLineType', 'relation' => '!=', 'value' => 'none' ],['key' => 'advUnderline', 'relation' => '==', 'value' => 'classic' ] ],
					'selector' => '{{PLUS_WRAP}} .text-content-block:hover{ text-decoration-color: {{hLineColor}}; }',
				],
			],
			'scopy' => true,
		],
		'ovBottomOff' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '==', 'value' => 'style-1' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-1:before { bottom: {{ovBottomOff}}; }',
				],
			],
			'scopy' => true,
		],
		'ovLineNHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-1:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-2:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-3:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:after, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-5:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-6:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-7:before { height: {{ovLineNHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'ovLineHHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '!=', 'value' => 'style-7' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-1:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-2:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-3:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:after, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-5:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-6:hover:before { height: {{ovLineHHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'ovLineNBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-1:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-2:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-3:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:before,{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:after, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-5:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-6:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-7:before',
				],
			],
			'scopy' => true,
		],
		'ovLineHBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'advUnderline', 'relation' => '==', 'value' => 'overlay' ],['key' => 'overlayStyle', 'relation' => '!=', 'value' => 'style-7' ] ],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-1:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-2:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-3:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-4:hover:after, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-5:hover:before, {{PLUS_WRAP}} .tpgb-adv-single-typo.overlay-style-6:hover:before',
				],
			],
			'scopy' => true,
		],

		/* Style End */
	);

	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-adv-typo', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_adv_typo_render_callback'
    ) );
}
add_action( 'init', 'tpgb_adv_typo' );