<?php
/* Block : Advanced Buttons
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_advanced_buttons_render_callback( $attributes ) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$btnType = (!empty($attributes['btnType'])) ? $attributes['btnType'] : 'cta';
	$ctaStyle = (!empty($attributes['ctaStyle'])) ? $attributes['ctaStyle'] : 'style-1';
	$dwnldStyle = (!empty($attributes['dwnldStyle'])) ? $attributes['dwnldStyle'] : 'style-1';
	$btnText = (!empty($attributes['btnText'])) ? $attributes['btnText'] : '';
	$extraText = (!empty($attributes['extraText'])) ? $attributes['extraText'] : '';
	$extraText1 = (!empty($attributes['extraText1'])) ? $attributes['extraText1'] : '';
	$btnLink = (!empty($attributes['btnLink']['url'])) ? $attributes['btnLink']['url'] : '';
	$target = (!empty($attributes['btnLink']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['btnLink']['nofollow'])) ? 'nofollow' : '';
	$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['btnLink']);
	$dwnldFileName = (!empty($attributes['dwnldFileName'])) ? $attributes['dwnldFileName'] : '';
	$extraTextColor = (!empty($attributes['extraTextColor'])) ? $attributes['extraTextColor'] : '';
	
	$marqueeSpeed = (!empty($attributes['marqueeSpeed'])) ? $attributes['marqueeSpeed'] : '12';
	$marqueeDir = (!empty($attributes['marqueeDir'])) ? $attributes['marqueeDir'] : 'left';
	$ariaLabel = (!empty($attributes['ariaLabel'])) ? $attributes['ariaLabel'] : '';
	
	$tooltipPos = (!empty($attributes['tooltipPos'])) ? $attributes['tooltipPos'] : 'left';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$dyWidth = (array)$attributes['cta10Width'];
	$dyHeight = (array)$attributes['cta10Height'];
	$ctaMDWidth = $ctaMDHeight = '';
	if($btnType=='cta' && $ctaStyle=='style-10'){
		$ctaMDWidth = (!empty($dyWidth) && !empty($dyWidth['md'])) ? $dyWidth['md'] : '150';
	}
	if($btnType=='cta' && $ctaStyle=='style-10'){
		$ctaMDHeight = (!empty($dyHeight) && !empty($dyHeight['md'])) ? $dyHeight['md'] : '50';
	}
	
	$styleClass = $dycss = '' ;
	if($btnType=='cta' && $ctaStyle=='style-9'){
		$dycss = '.tpgb-block-'.esc_attr($block_id).' .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-9 .adv-btn-parrot{animation: tp-blink-'.esc_attr($block_id).' 0.8s infinite;} @keyframes tp-blink-'.esc_attr($block_id).' { 25%, 75% { color: transparent; } 40%, 60% { color: '.esc_attr($extraTextColor).'; } }';
	}
	
	if($btnType=='cta'){
		$styleClass .= ' tpgb-cta-'.$ctaStyle;
	} else {
		$styleClass .= ' tpgb-download-'.$dwnldStyle;
	}
	if($btnType=='cta' && $ctaStyle=='style-13' && $tooltipPos=='left'){
		$styleClass .= ' style-13-align-left';
	}
	if($btnType=='cta' && $ctaStyle=='style-13' && $tooltipPos=='right'){
		$styleClass .= ' style-13-align-right';
	}
	
	$data_attr = '';
	
	$download_attr = '';
	if($btnType=='download'){
		$data_attr .= ' data-dfname='.esc_attr($dwnldFileName).'';
		$download_attr .=' download='.esc_attr($dwnldFileName).'';
	}
	$uid_advbutton=uniqid("advbutton");
	$ariaLabelT = (!empty($ariaLabel)) ? esc_attr($ariaLabel) : ((!empty($btnText)) ? esc_attr($btnText) : esc_attr__("Button", 'tpgbp'));
	
    $output .= '<div class="tpgb-advanced-buttons tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		if($btnType=='download' && $dwnldStyle!='style-3' && $dwnldStyle!='style-5') {
			$output .='<div class="adv_btn_ext_txt">'.wp_kses_post($btnText).'</div>';
		}
		$output .= '<div id="'.esc_attr($uid_advbutton).'" class="tpgb-adv-btn-inner ab-'.esc_attr($btnType).' '.esc_attr($styleClass).'" '.$data_attr.'>';
			if($btnType=='cta' && $ctaStyle=='style-4'){
				$output .= '<div class="pulsing"></div>';
			}
			$output .= '<a href="'.esc_url($btnLink).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" class="adv-button-link-wrap tpgb-trans-ease tpgb-trans-ease-before" role="button" aria-label="'.$ariaLabelT.'" '.$download_attr.' '.$link_attr.'>';
				if($btnType=='cta' && ($ctaStyle!='style-5' && $ctaStyle!='style-6' && $ctaStyle!='style-8' && $ctaStyle!='style-9' && $ctaStyle!='style-13')){
					$output .= '<span class="tpgb-trans-ease">'.wp_kses_post($btnText).'</span>';
				}
				if($btnType=='cta' && $ctaStyle=='style-5'){
					$output .= '<p class="tpgb-cta-style-5-text">'.wp_kses_post($btnText).'</p>';
				}
				if($btnType=='cta' && ($ctaStyle=='style-6' || $ctaStyle=='style-8' || $ctaStyle=='style-9' || $ctaStyle=='style-13')){
					if($ctaStyle!='style-13'){
						$output .= wp_kses_post($btnText);
					}
					if($btnType=='cta' && $ctaStyle=='style-6'){
						$output .= '<marquee scrollamount="'.esc_attr($marqueeSpeed).'" direction="'.esc_attr($marqueeDir).'">';
							$output .= '<span class="tpgb-trans-ease">'.wp_kses_post($extraText1).'</span>';
						$output .= '</marquee>';
					}
					if($btnType=='cta' && $ctaStyle=='style-8'){
						for ($ij = 1; $ij <= 3; $ij++) {
						  $output .= '<div class="adv-btn-emoji"></div>';
						}
					}
					if($btnType=='cta' && $ctaStyle=='style-9'){
						for ($ij = 1; $ij <= 6; $ij++) {
							$output .= '<div class="adv-btn-parrot"></div>';
						}
					}
					if($btnType=='cta' && $ctaStyle=='style-13'){
						$output .= '<span class="tpgb-trans-ease sty13-main-text">'.wp_kses_post($btnText).'</span>';
						$output .= '<span class="tpgb-trans-ease sty13-extra-text">'.wp_kses_post($extraText1).'</span>';
					}
				}
				if($btnType=='cta' && $ctaStyle=='style-7'){
					$output .= '<div class="hands"></div>';
				}
				if($btnType=='cta' && $ctaStyle=='style-10'){
					$output .= '<svg>';
						$output .='<polyline class="tpgb-cpt-btn01" points="0 0, '.esc_attr($ctaMDWidth).' 0, '.esc_attr($ctaMDWidth).' '.esc_attr($ctaMDHeight).', 0 '.esc_attr($ctaMDHeight).', 0 0"></polyline>';
						$output .='<polyline class="tpgb-cpt-btn02" points="0 0, '.esc_attr($ctaMDWidth).' 0, '.esc_attr($ctaMDWidth).' '.esc_attr($ctaMDHeight).', 0 '.esc_attr($ctaMDHeight).', 0 0"></polyline>';
					$output .= '</svg>';
				}
				if($btnType=='download' && $dwnldStyle=='style-1') {
					$output .= '<svg width="22px" height="16px" viewBox="0 0 22 16">
						<path d="M2,10 L6,13 L12.8760559,4.5959317 C14.1180021,3.0779974 16.2457925,2.62289624 18,3.5 L18,3.5 C19.8385982,4.4192991 21,6.29848669 21,8.35410197 L21,10 C21,12.7614237 18.7614237,15 16,15 L1,15" id="check"></path>
							<polyline points="4.5 8.5 8 11 11.5 8.5" class="svg-out"></polyline>
						<path d="M8,1 L8,11" class="svg-out"></path>
					</svg>';
				}
				if($btnType=='download' && $dwnldStyle=='style-2') {
					$output .= '<svg id="arrow" width="14px" height="20px" viewBox="17 14 14 20">
						<path d="M24,15 L24,32"></path>
						<polyline points="30 27 24 33 18 27"></polyline>
					</svg>
					<svg id="check" width="21px" height="15px" viewBox="13 17 21 15">
						<polyline points="32.5 18.5 20 31 14.5 25.5"></polyline>
					</svg>
					<svg id="border" width="48px" height="48px" viewBox="0 0 48 48">
						<path d="M24,1 L24,1 L24,1 C36.7025492,1 47,11.2974508 47,24 L47,24 L47,24 C47,36.7025492 36.7025492,47 24,47 L24,47 L24,47 C11.2974508,47 1,36.7025492 1,24 L1,24 L1,24 C1,11.2974508 11.2974508,1 24,1 L24,1 Z"></path>
					</svg>';
				}
				if($btnType=='download' && $dwnldStyle=='style-3') {
					$output .= '<span class="tpgb-trans-ease dw-sty3-extra-text">'.wp_kses_post($extraText).'</span><span class="tpgb-trans-ease dw-sty3-main-text">'.wp_kses_post($btnText).'</span>';
				}
				if($btnType=='download' && $dwnldStyle=='style-4') {
					$output .= '<span class="tpgb-trans-ease adv-btn-icon">';
						$output .= '<i class="fas fa-download cmn-icon btn-icon-start"></i>';
						$output .= '<i class="fas fa-circle-notch cmn-icon btn-icon-load"></i>';
						$output .= '<i class="fas fa-check cmn-icon btn-icon-success"></i>';
					$output .= '</span>';
				}
				if($btnType=='download' && $dwnldStyle=='style-5') {
					$output .= wp_kses_post($btnText);
					 $output .= '<span class="tpgb-trans-ease icon-wrap">';
						$output .= '<i class="icon-download"></i>';
					$output .= '</span>';
				}
			$output .= '</a>';
				if($btnType=='download' && $dwnldStyle=='style-5') {
					$output .= '<div class="tp-meter">';
						$output .= '<span class="tpgb-trans-ease tp-meter-progress"></span>';
					$output .= '</div>';
				}
		$output .= '</div>';
		if(!empty($dycss)){
			$output .= '<style>'.$dycss.'</style>';
		}
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
}

function tpgb_tp_advanced_buttons() {
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
  
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'btnType' => [
				'type' => 'string',
				'default' => 'cta',	
			],
			'ctaStyle' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'dwnldStyle' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'btnText' => [
				'type' => 'string',
				'default' => 'Buy Now',	
			],
			'dst5LoadingText' => [
				'type' => 'string',
				'default' => 'downloading...',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .tp-meter:before{ content: "{{dst5LoadingText}}"; }',
					],
				],
			],
			'dst5SuccessText' => [
				'type' => 'string',
				'default' => 'done!',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .tp-meter.is-done:after{ content: "{{dst5SuccessText}}"; }',
					],
				],
			],
			'extraText' => [
				'type' => 'string',
				'default' => 'The Plus',	
			],
			'dst3MWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap{ min-width: {{dst3MWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'extraText1' => [
				'type' => 'string',
				'default' => 'The Plus',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-6','style-9'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-9 .adv-btn-parrot:before{ content: "{{extraText1}}"; }',
					],
				],
			],
			'extraText2' => [
				'type' => 'string',
				'default' => 'The Plus',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-6','style-9','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-6 .adv-button-link-wrap:before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-9 .adv-button-link-wrap:hover .adv-btn-parrot::before, {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::after{ content: "{{extraText2}}"; }',
					],
				],
			],
			'nmlEmoji' => [
				'type' => 'string',
				'default' => '💯',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-8']],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-8 .adv-btn-emoji:before{ content: "{{nmlEmoji}}"; }',
					],
				],
			],
			'hvrEmoji' => [
				'type' => 'string',
				'default' => '👏',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-8']],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-8 .adv-button-link-wrap:hover .adv-btn-emoji:before{ content: "{{hvrEmoji}}"; }',
					],
				],
			],
			'cta10Width' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg{ width: {{cta10Width}}; }',
					],
				],
				'scopy' => true,
			],
			'cta10Height' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg{ height: {{cta10Height}}; }',
					],
				],
				'scopy' => true,
			],
			'btnLink' => [
				'type'=> 'object',
				'default'=> [
					'url' => '#',	
					'target' => '',	
					'nofollow' => ''
				],
			],
			'dwnldFileName' => [
				'type' => 'string',
				'default' => 'download',	
			],
			
			'Alignment' => [
				'type' => 'object',
				'default' => 'left',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-advanced-buttons{ text-align: {{Alignment}}; }',
					]
				],
				'scopy' => true,
			],
			'tooltipPos' => [
				'type' => 'string',
				'default' => 'left',	
			],
			'minWidthSt5' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .adv-button-link-wrap{ min-width: calc({{minWidthSt5}} + 1px); }',
					],
				],
				'scopy' => true,
			],
			'nmlAnSpeed' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-4','style-5'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing::before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing::after ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .tpgb-cta-style-5-text{ animation-duration: {{nmlAnSpeed}}ms; -o-animation-duration: {{nmlAnSpeed}}ms; -ms-animation-duration: {{nmlAnSpeed}}ms; -moz-animation-duration: {{nmlAnSpeed}}ms; -webkit-animation-duration: {{nmlAnSpeed}}ms; }',
					],
				],
				'scopy' => true,
			],
			'hvrAnSpeed' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-4','style-5'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap:hover , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4:hover .pulsing::before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4:hover .pulsing::after ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .adv-button-link-wrap:hover .tpgb-cta-style-5-text{ animation-duration: {{hvrAnSpeed}}ms; -o-animation-duration: {{hvrAnSpeed}}ms; -ms-animation-duration: {{hvrAnSpeed}}ms; -moz-animation-duration: {{hvrAnSpeed}}ms; -webkit-animation-duration: {{hvrAnSpeed}}ms; }',
					],
				],
				'scopy' => true,
			],
			'marqueeSpeed' => [
				'type' => 'string',
				'default' => '12',
				'scopy' => true,
			],
			'marqueeDir' => [
				'type' => 'string',
				'default' => 'left',
				'scopy' => true,
			],
			'ariaLabel' => [
				'type' => 'string',
				'default' => '',	
			],
			
			/*CTA Style Start*/
			'texTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ],['key' => 'ctaStyle', 'relation' => '!=', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta .adv-button-link-wrap ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .adv-button-link-wrap .tpgb-cta-style-5-text',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap .sty13-main-text,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap .sty13-extra-text',
					],
				],
				'scopy' => true,
			],
			'textNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ],['key' => 'ctaStyle', 'relation' => '!=', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta .adv-button-link-wrap , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .adv-button-link-wrap .tpgb-cta-style-5-text{ color: {{textNmlColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ],['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap .sty13-main-text{ color: {{textNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'extraTextColor' => [
				'type' => 'string',
				'default' => '#000',
				'scopy' => true,
			],
			'textHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '!=', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta .adv-button-link-wrap:hover , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-5 .adv-button-link-wrap:hover .tpgb-cta-style-5-text{ color: {{textHvrColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover > span.sty13-extra-text{ color: {{textHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'nmlFillColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg .tpgb-cpt-btn01{ fill: {{nmlFillColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrFillColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap:hover svg .tpgb-cpt-btn01{ fill: {{hvrFillColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrDotColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg .tpgb-cpt-btn02{ stroke: {{hvrDotColor}}; }',
					],
				],
				'scopy' => true,
			],
			'normalBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .adv-button-link-wrap , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing:before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing:after',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-5','style-6','style-7','style-8','style-9','style-11','style-12','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'hoverBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap:hover::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap:hover::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-4','style-5','style-6','style-7','style-8','style-9','style-11','style-12','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'nmlB11Color' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap{ border-color: {{nmlB11Color}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrB11Color' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap:hover{ border-color: {{hvrB11Color}}; }',
					],
				],
				'scopy' => true,
			],
			'nmlDots11' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap::before, {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap::after',
					],
				],
				'scopy' => true,
			],
			'hvrDots11' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnText', 'relation' => '!=', 'value' => '' ] ,['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-11' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap:hover::before, {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-11 .adv-button-link-wrap:hover::after',
					],
				],
				'scopy' => true,
			],
			'bgNormalB' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-4','style-5','style-6','style-7','style-8','style-9','style-12'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'bgHoverB' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap:hover::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap:hover::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-4','style-5','style-6','style-7','style-8','style-9','style-12'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap:hover ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'nmlBRadius' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap::before{border-radius: {{nmlBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .adv-button-link-wrap , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing:before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .pulsing:after{border-radius: {{nmlBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-5']],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.tpgb-cta-style-5 .adv-button-link-wrap{border-radius: {{nmlBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-6','style-7','style-8','style-9','style-12'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap{border-radius: {{nmlBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap{border-radius: {{nmlBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'hvrBRadius' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap:hover::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap:hover::before{border-radius: {{hvrBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4 .adv-button-link-wrap:hover , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4:hover .pulsing::before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-4:hover .pulsing::after{border-radius: {{hvrBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-5']],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.tpgb-cta-style-5 .adv-button-link-wrap:hover{border-radius: {{hvrBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-6','style-7','style-8','style-9','style-12'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap:hover{border-radius: {{hvrBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-3 .adv-button-link-wrap:hover , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover{border-radius: {{hvrBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'nmlboxShadow' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-4','style-5','style-6','style-7','style-8','style-9','style-12','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'hvrboxShadow' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap:hover::before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap:hover::before',
					],
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-3','style-4','style-5','style-6','style-7','style-8','style-9','style-12','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'bdrWH' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-1 .adv-button-link-wrap:before , {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-2 .adv-button-link-wrap:before{ width: {{bdrWH}}; height: {{bdrWH}}; }',
					],
				],
				'scopy' => true,
			],
			'btnPadding' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'cta' ], ['key' => 'ctaStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6','style-7','style-8','style-9','style-12','style-13'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta .adv-button-link-wrap{padding: {{btnPadding}};}',
					],
				],
				'scopy' => true,
			],
			'strokeWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg{ stroke-width: {{strokeWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'strokeColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-10' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-10 .adv-button-link-wrap svg .tpgb-cpt-btn01{ stroke: {{strokeColor}}; }',
					],
				],
				'scopy' => true,
			],
			
			'tipTexTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:after',
					],
				],
				'scopy' => true,
			],
			'tipTextNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::after{ color: {{tipTextNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'tipTextHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::after{ color: {{tipTextHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'tipNormalBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::before',
					],
				],
				'scopy' => true,
			],
			'tipHoverBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::before',
					],
				],
				'scopy' => true,
			],
			'tipNormalB' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::before ',
					],
				],
				'scopy' => true,
			],
			'tipHoverB' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::before ',
					],
				],
				'scopy' => true,
			],
			'tipNmlBRadius' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::before{border-radius: {{tipNmlBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'tipHvrBRadius' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::before{border-radius: {{tipHvrBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'tipNmlboxShadow' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::before',
					],
				],
				'scopy' => true,
			],
			'tipHvrboxShadow' => [
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
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::before',
					],
				],
				'scopy' => true,
			],
			'tipNmlTransCss' => [
				'type' => 'string',
				'default' => 'skew(-25deg)',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap::before{transform: {{tipNmlTransCss}}; -ms-transform:{{tipNmlTransCss}}; -moz-transform: {{tipNmlTransCss}}; -webkit-transform: {{tipNmlTransCss}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;}',
					],
				],
				'scopy' => true,
			],
			'tipHvrTransCss' => [
				'type' => 'string',
				'default' => 'skew(-25deg)',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ctaStyle', 'relation' => '==', 'value' => 'style-13' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-cta.tpgb-cta-style-13 .adv-button-link-wrap:hover::before{transform: {{tipHvrTransCss}}; -ms-transform:{{tipHvrTransCss}}; -moz-transform: {{tipHvrTransCss}}; -webkit-transform: {{tipHvrTransCss}}; transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;}',
					],
				],
				'scopy' => true,
			],
			/*CTA Style End*/
			
			/*Download Style Start*/
			'dSt4IcnSize' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-4 .adv-button-link-wrap .cmn-icon{ font-size: {{dSt4IcnSize}}; }',
					],
				],
				'scopy' => true,
			],
			'dst35TexTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 span.dw-sty3-extra-text, {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 span.dw-sty3-main-text',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .tp-meter:before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .tp-meter.is-done:after',
					],
				],
				'scopy' => true,
			],
			'dSt35NmlTextClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-main-text{ color: {{dSt35NmlTextClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap{ color: {{dSt35NmlTextClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt35HvrTextClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover span.dw-sty3-extra-text { color: {{dSt35HvrTextClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover{ color: {{dSt35HvrTextClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt5CmltTextClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .tp-meter.is-done:after{ color: {{dSt5CmltTextClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt3NmlIcnClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:after{ border-color: {{dSt3NmlIcnClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt3HvrIcnClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover:before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover:after{ border-color: {{dSt3HvrIcnClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt3NmlIcnBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:after{ background: {{dSt3NmlIcnBG}}; }',
					],
				],
				'scopy' => true,
			],
			'dSt3HvrIcnBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover:before ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover:after{ background: {{dSt3HvrIcnBG}}; }',
					],
				],
				'scopy' => true,
			],
			'dnst3IcnNmlClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-1 .adv-button-link-wrap svg polyline ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-1 .adv-button-link-wrap svg path{ stroke: {{dnst3IcnNmlClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap #arrow polyline ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap #arrow path{ stroke: {{dnst3IcnNmlClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-4 .adv-button-link-wrap .cmn-icon{ color: {{dnst3IcnNmlClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap .icon-download{ border-color: {{dnst3IcnNmlClr}}; } {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap .icon-download::after{ border-top-color: {{dnst3IcnNmlClr}}; } {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap .icon-download::before{ background: {{dnst3IcnNmlClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dnst13IcnHvrClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap:hover #arrow polyline ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap:hover #arrow path{ stroke: {{dnst13IcnHvrClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-4 .adv-button-link-wrap:hover .cmn-icon{ color: {{dnst13IcnHvrClr}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover .icon-download{ border-color: {{dnst13IcnHvrClr}}; } {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover .icon-download::after{ border-top-color: {{dnst13IcnHvrClr}}; } {{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover .icon-download::before{ background: {{dnst13IcnHvrClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dst12IcnDlClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-1 .adv-button-link-wrap.downloaded svg path#check ,{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap svg#check{ stroke: {{dst12IcnDlClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dst2IcnBClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-btn-inner.ab-download.tpgb-download-style-2 .adv-button-link-wrap.load #border{ stroke: {{dst2IcnBClr}}; }',
					],
				],
				'scopy' => true,
			],
			
			'downloadNmlBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap ,{{PLUS_WRAP}} .ab-download.tpgb-download-style-2, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-main-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'downloadHvrBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap:hover ,{{PLUS_WRAP}} .ab-download.tpgb-download-style-2:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover span.dw-sty3-extra-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap.success, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'downloadNBdr' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap ,{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-main-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .tp-meter',
					],
				],
				'scopy' => true,
			],
			'downloadHBdr' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '!=', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap:hover , {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover span.dw-sty3-extra-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap .tp-meter',
					],
				],
				'scopy' => true,
			],
			'downloadNmlBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '!=', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-main-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap .tp-meter{border-radius: {{downloadNmlBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'downloadHvrBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap:hover,{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap.downloaded:hover,{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-extra-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap:hover{border-radius: {{downloadHvrBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'downloadNmlBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap span.dw-sty3-main-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'downloadHvrBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-1 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-2 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover span.dw-sty3-extra-text, {{PLUS_WRAP}} .ab-download.tpgb-download-style-4 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap:hover, {{PLUS_WRAP}} .ab-download.tpgb-download-style-5 .adv-button-link-wrap .tp-meter',
					],
				],
				'scopy' => true,
			],
			
			'dwnldTextPadding' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt{padding: {{dwnldTextPadding}};}',
					],
				],
				'scopy' => true,
			],
			'dwnldTextTopOffset' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt{ top: {{dwnldTextTopOffset}}; }',
					],
				],
				'scopy' => true,
			],
			'dwnldTextRightOffset' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt{ margin-right: {{dwnldTextRightOffset}}; }',
					],
				],
				'scopy' => true,
			],
			'dwnldTexTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt',
					],
				],
				'scopy' => true,
			],
			'dwnldTextNClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt{ color: {{dwnldTextNClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dwnldTextHClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt:hover{ color: {{dwnldTextHClr}}; }',
					],
				],
				'scopy' => true,
			],
			'dwnldTextNmlBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt',
					],
				],
				'scopy' => true,
			],
			'dwnldTextHvrBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt:hover',
					],
				],
				'scopy' => true,
			],
			'dwnldTextNmlB' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt',
					],
				],
				'scopy' => true,
			],
			'dwnldTextHvrB' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt:hover',
					],
				],
				'scopy' => true,
			],
			'dwnldTextNBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt{border-radius: {{dwnldTextNBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'dwnldTextHBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt:hover{border-radius: {{dwnldTextHBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'dwnldTextNBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt',
					],
				],
				'scopy' => true,
			],
			'dwnldTextHBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4'] ]],
						'selector' => '{{PLUS_WRAP}} .adv_btn_ext_txt:hover',
					],
				],
				'scopy' => true,
			],
			
			'dst3BoxNmlBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'dst3BoxHvrBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'dst3BoxNmlBdr' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'dst3BoxHvrBdr' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'dst3BoxNBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap{border-radius: {{dst3BoxNBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'dst3BoxHBRadius' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover{border-radius: {{dst3BoxHBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'dst3BoxNBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'dst3BoxHBShadow' => [
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
						'condition' => [(object) ['key' => 'btnType', 'relation' => '==', 'value' => 'download' ], ['key' => 'dwnldStyle', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .ab-download.tpgb-download-style-3 .adv-button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			/*Download Style End*/
		);
	$attributesOptions = array_merge($attributesOptions,$globalPlusExtrasOption,$globalBgOption,$globalpositioningOption);
	
	register_block_type( 'tpgb/tp-advanced-buttons', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_advanced_buttons_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_advanced_buttons' );