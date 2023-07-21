<?php
/* Block : Carousel Remote
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_carousel_remote_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$remType = (!empty($attributes['remType'])) ? $attributes['remType'] : '';
	$rbtnAlign = (!empty($attributes['rbtnAlign'])) ? $attributes['rbtnAlign'] : '';
	$btntxt1 = (!empty($attributes['btntxt1'])) ? $attributes['btntxt1'] : '';
	$btntxt2 = (!empty($attributes['btntxt2'])) ? $attributes['btntxt2'] : '';
	$Ctmicon1 = (!empty($attributes['Ctmicon1'])) ? $attributes['Ctmicon1'] : '';
	$Ctmicon2 = (!empty($attributes['Ctmicon2'])) ? $attributes['Ctmicon2'] : '';
	$imgSize = (!empty($attributes['imgSize'])) ? $attributes['imgSize'] : '';
	$carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	$dotList = (!empty($attributes['dotList'])) ? $attributes['dotList'] : [];
	$showDot = (!empty($attributes['showDot'])) ? $attributes['showDot'] : false;
	$dotLayout = (!empty($attributes['dotLayout'])) ? $attributes['dotLayout'] : '';
	$dotstyle = (!empty($attributes['dotstyle'])) ? $attributes['dotstyle'] : '';
	$tooltiparrow = (!empty($attributes['tooltiparrow'])) ? $attributes['tooltiparrow'] : false;
	$AborderColor = (!empty($attributes['AborderColor'])) ? $attributes['AborderColor'] : '';
	$showpagi = (!empty($attributes['showpagi'])) ? $attributes['showpagi'] : false;
	$carobtn = (!empty($attributes['carobtn'])) ? $attributes['carobtn'] : false;
	$tooltipDir = (!empty($attributes['tooltipDir'])) ? $attributes['tooltipDir'] : '';
	$vtooltipDir = (!empty($attributes['vtooltipDir'])) ? $attributes['vtooltipDir'] : '';
	$BiconFont = (!empty($attributes['BiconFont'])) ? $attributes['BiconFont'] : '';
	$btnIcon1 = (!empty($attributes['btnIcon1'])) ? $attributes['btnIcon1'] : '';
	$btnIcon2 = (!empty($attributes['btnIcon2'])) ? $attributes['btnIcon2'] : '';
	$sliderInd = (!empty($attributes['sliderInd'])) ? $attributes['sliderInd'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	//Set Id Connection 
	$dataAttr='';
	if(!empty($carouselId)){
		$dataAttr .= ' id="tptab_'.esc_attr($carouselId).'" ';
		$dataAttr .= ' data-tab-id="tptab_'.esc_attr($carouselId).'" ' ;
		$dataAttr .= ' data-connection="tpca-'.esc_attr($carouselId).'" ';
		$dataAttr .= ' data-extra-conn="tpex-'.esc_attr($carouselId).'"';
	}
	
	// Set Icon For Button
	$nav_next =$nav_prev = $nav_prev_icon = $nav_next_icon = '';
	$nav_next_text=$nav_prev_text ='';
	if($btntxt1!=''){
		$nav_prev_text ='<span>'.wp_kses_post($btntxt1).'</span>';
	}
	if($btntxt2!=''){
		$nav_next_text ='<span>'.wp_kses_post($btntxt2).'</span>';
	}

	//Svg For Animation 
	$Asvg = '';
	$Asvg .= '<svg height="32" data-v-d3e9c2e8="" width="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" svg-inline="" role="presentation" focusable="false" tabindex="-1" class="active-border">';
		$Asvg .= '<path data-v-d3e9c2e8="" d="M14.7974701,0 C16.6202545,0 19.3544312,0 23,0 C26.8659932,0 30,3.13400675 30,7 L30,23 C30,26.8659932 26.8659932,30 23,30 L7,30 C3.13400675,30 0,26.8659932 0,23 L0,7 C0,3.13400675 3.13400675,0 7,0 L14.7602345,0" transform="translate(1.000000, 1.000000)" fill="none" stroke="'.esc_attr($AborderColor).'" stroke-width="2" class="border"></path>';
	$Asvg .= '</svg>';


	
	if($BiconFont == 'font_awesome'){
		$nav_prev = '<span class="nav-icon"><i class="'.($btnIcon1 != '' ? esc_attr($btnIcon1) : '').'" aria-hidden="true"></i></span>'.wp_kses_post($btntxt1);
		$nav_next = wp_kses_post($btntxt2).'<span class="nav-icon"><i class="'.($btnIcon2 != '' ? esc_attr($btnIcon2) : '').'" aria-hidden="true"></i></span>';
	}else if($BiconFont == 'img'){
		if(!empty($Ctmicon1['id']) && !empty($Ctmicon1) ) {
			$nav_prev_icon = wp_get_attachment_image($Ctmicon1['id'],$imgSize);
		}else if( !empty($Ctmicon1) && !empty($Ctmicon1['url'])){
			$nav_prev_icon = '<img src="'.esc_url($Ctmicon1['url']).'" />';
		}
		if(!empty($Ctmicon2['id']) && !empty($Ctmicon2) ) {
			$nav_next_icon = wp_get_attachment_image($Ctmicon2['id'],$imgSize);
		}else if( !empty($Ctmicon2) && !empty($Ctmicon2['url'])){
			$nav_next_icon = '<img src="'.esc_url($Ctmicon2['url']).'" />';
		}
		
		$nav_prev = '<span class="nav-icon">'.$nav_prev_icon.'</span>'.$nav_prev_text;
		$nav_next = $nav_next_text.'<span class="nav-icon">'.$nav_next_icon.'</span>';
	}
	else {
		$nav_prev = wp_kses_post($btntxt1);
		$nav_next = wp_kses_post($btntxt2);
	}
	
	
	$output .= '<div class="carousel-remote-wrap '.esc_attr($rbtnAlign).'">';
		$output .= '<div class="tpgb-carousel-remote tpex-'.(!empty($carouselId) ? esc_attr($carouselId)  : '' ).' tpgb-block-'.esc_attr($block_id).' remote-'.esc_attr($remType).' '.esc_attr($blockClass).' " data-remote="'.esc_attr($remType).'" '.$dataAttr.' ">';
			if(!empty($carobtn)){
				$pAriaLabel = (!empty($attributes['pAriaLabel'])) ? esc_attr($attributes['pAriaLabel']) : esc_attr__('Prev','tpgbp');
				$nAriaLabel = (!empty($attributes['nAriaLabel'])) ? esc_attr($attributes['nAriaLabel']) : esc_attr__('Next','tpgbp');

				$output .= '<div class="slider-btn-wrap">';
					$output .= '<a href="#" class="slider-btn tpgb-trans-easeinout tpgb-prev-btn '.(($remType == 'switcher') ? ' active' : '' ).'" data-id="tpca-'.esc_attr($carouselId).'" data-nav="'.esc_attr("prev","tpgbp").'" aria-label="'.$pAriaLabel.'">';
						$output .= $nav_prev;
					$output .= "</a>";
					$output .= '<a href="#" class="slider-btn tpgb-trans-easeinout tpgb-next-btn" data-id="tpca-'.esc_attr($carouselId).'" data-nav="'.esc_attr("next","tpgbp").'" aria-label="'.$nAriaLabel.'">';
						$output .= $nav_next;
					$output .= "</a>";
				$output .= "</div>";
			}
			if(!empty($showDot)){
				$output .= '<div class="tpgb-carousel-dots dot-'.esc_attr($dotLayout).'">';
					if(!empty($dotList)){
						foreach ( $dotList as $index => $item ) :
							$output .= '<div class="tpgb-carodots-item tpgb-rel-flex tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($dotstyle).'" data-tab="'.esc_attr( $index).'">';
								$output .= '<div class="tpgb-dots tpgb-rel-flex  tooltip-'.($dotLayout == 'horizontal' ? esc_attr($tooltipDir) : esc_attr($vtooltipDir) ).'">';
									if($item['iconFonts'] == 'font_awesome'){
										$output .= '<i class="dot-icon '.($item['iconName'] != '' ? esc_attr($item['iconName']) : 'fas fa-home' ).'"></i>';
									}else if($item['iconFonts'] == 'image' && !empty($item['iconImage']) && !empty($item['iconImage']['id']) ){
										$iconImgSize = (!empty($item['iconimageSize']) ? $item['iconimageSize'] : 'full' );
										$output .= wp_get_attachment_image($item['iconImage']['id'],$iconImgSize);
									}
									$output .= '<span class="tooltip-txt '.(!empty($tooltiparrow) ? 'tooltip-arrow' : '').'"> '.wp_kses_post($item['label']).' </span>';
									$output .= $Asvg;
								$output .= "</div>";
							$output .= "</div>";
						endforeach;
					}
				$output .= "</div>";
			}
			if(!empty($showpagi)){
				$output .= '<div class="carousel-pagination">';
					$output .= '<div class="pagination-list">';
						$output .= '<div class="active">01</div>';
					$output .= '</div>';
					$output .= '<span class="tpgb-caropagi-line">&#47;</span> ';
					$output .= '<span class="totalpage">'.($sliderInd <= 9 ? '0'.esc_html($sliderInd) : esc_html($sliderInd) ).'</span>';
				$output .= "</div>";
			}
		$output .= "</div>";
	$output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
	* Render for the server-side
 */
function tpgb_tp_carousel_remote() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'carouselId' => [
				'type' => 'string',
				'default' => 'Id1',
			],
			'carobtn' => [
				'type' => 'boolean',
				'default' => true,
			],
			'remType' => [
				'type' => 'string',
				'default' => 'carousel',
			],
			'btntxt1' => [
				'type' => 'string',
				'default' => 'Prev',
			],
			'btntxt2' => [
				'type' => 'string',
				'default' => 'Next',
			],
			'rbtnAlign' => [
				'type' => 'string',
				'default' => 'text-left',
				'scopy' => true,
			],
			'pAriaLabel' => [
				'type' => 'string',
				'default' => '',
			],
			'nAriaLabel' => [
				'type' => 'string',
				'default' => '',
			],
			'BiconFont' => [
				'type' => 'string',
				'default' => 'font_awesome'
			],
			'btnIcon1' => [
				'type' => 'string',
				'default' => 'fas fa-angle-left'
			],
			'btnIcon2' => [
				'type' => 'string',
				'default' => 'fas fa-angle-right'
			],
			'Ctmicon1' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],
			],
			'Ctmicon2' => [
				'type' => 'object',
                'default' => [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],
			],
			'imgSize' => [
				'type' => 'string',
				'default' => 'full'
			],
			'iconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ],
							['key' => 'BiconFont', 'relation' => '==', 'value' => 'font_awesome']
						],
						'selector' => '{{PLUS_WRAP}} .slider-btn span.nav-icon { font-size: {{iconSize}}; }',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ],
							['key' => 'BiconFont', 'relation' => '==', 'value' => 'img']
						],
						'selector' => '{{PLUS_WRAP}} .slider-btn span.nav-icon img{ max-width: {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'iconSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} a.slider-btn.tpgb-prev-btn span.nav-icon{ margin-right: {{iconSpace}}; } {{PLUS_WRAP}} a.slider-btn.tpgb-next-btn span.nav-icon{ margin-left: {{iconSpace}}; } ',
					],
				],
				'scopy' => true,
			],
			'iconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ],
							['key' => 'BiconFont', 'relation' => '==', 'value' => 'font_awesome']
						],
						'selector' => '{{PLUS_WRAP}} a.slider-btn span.nav-icon{ color: {{iconColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'HvrIcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ],
							['key' => 'BiconFont', 'relation' => '==', 'value' => 'font_awesome']
						],
						'selector' => '{{PLUS_WRAP}} a.slider-btn:hover span.nav-icon,{{PLUS_WRAP}} a.slider-btn.active span.nav-icon{ color: {{HvrIcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'btnSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.tpgb-prev-btn{ margin-right: {{btnSpace}}; } {{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.tpgb-next-btn{ margin-left: {{btnSpace}}; } ',
					],
				],
				'scopy' => true,
			],
			'rbtnpadding' => [
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
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn{ padding: {{rbtnpadding}}; }',
					],
				],
				'scopy' => true,
			],
			'rbtnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'btntxt1', 'relation' => '!=', 'value' => '' ],
							(object) [ 'key' => 'btntxt2', 'relation' => '!=', 'value' => '' ],
						],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn',
					],
				],
				'scopy' => true,
			],
			'txtcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'btntxt1', 'relation' => '!=', 'value' => '' ],
							(object) [ 'key' => 'btntxt2', 'relation' => '!=', 'value' => '' ],
						],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn{ color: {{txtcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'btnBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn',
					],
				],
				'scopy' => true,
			],
			'btnBradius' => [
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
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn{ border-radius: {{btnBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 1,
					'bgType' => 'color',
					'bgDefaultColor' => '#3882f7',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn',
					],
				],
				'scopy' => true,
			],
			'btnBshadow' => [
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
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn',
					],
				],
				'scopy' => true,
			],
			'Hvrtxtcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'btntxt1', 'relation' => '!=', 'value' => '' ],
							(object) [ 'key' => 'btntxt2', 'relation' => '!=', 'value' => '' ],
						],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn:hover,{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.active{ color: {{Hvrtxtcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'btnHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn:hover,{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.active',
					],
				],
				'scopy' => true,
			],
			'btnHBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn:hover,{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.active{ border-radius: {{btnHBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn:hover,{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.active',
					],
				],
				'scopy' => true,
			],
			'hvrBshadow' => [
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
						'condition' => [(object) ['key' => 'carobtn', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn:hover,{{PLUS_WRAP}} .slider-btn-wrap a.slider-btn.active',
					],
				],
				'scopy' => true,
			],
			'showDot' => [
				'type' => 'boolean',
				'default' => false,
			],
			'dotList' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'label' => [
							'type' => 'string',
							'default' => 'Slide',
						],
						'iconFonts' => [
							'type' => 'string',
							'default' => 'font_awesome'
						],
						'iconName' => [
							'type' => 'string',
							'default'=> 'fas fa-home',
						],
						'iconImage' => [
							'type' => 'object',
							'default'=> [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
							],	
						],
						'iconimageSize' => [
							'type' => 'string',
							'default' => 'full',	
						],
						'doticonColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-dots .dot-icon{ color:{{doticonColor}};}',
								],
							], 
						],
						'dotBgtype' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 1,
								'bgType' => 'color',
								'bgDefaultColor' => '#c2ccc4'
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-carodots-item ',
								],
							], 
						],
						'acticonColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.active .tpgb-dots .dot-icon,{{PLUS_WRAP}} {{TP_REPEAT_ID}}:hover .tpgb-dots .dot-icon{color:{{acticonColor}};}',
								],
							], 
						],
						'actdotBgtype' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-carodots-item.active,{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-carodots-item:hover',
								],
							], 
						],
					],
				],
				'default' => [ 
					[ 
						"_key" => '0',
						'label' => 'Slide 1',
						'iconFonts' => 'font_awesome',
						'iconName' => 'fas fa-home',
						'txtcolor' => '',
						'iconColor' => '',
						'acttxtcolor' => '',
						'acticonColor' => '',
						'iconimageSize' => 'full',
						'dotBgtype' => [ 'openBg'=> 1,'bgType' => 'color','bgDefaultColor' => '#c2ccc4' ]
					],
					[ 
						"_key" => '1',
						'label' => 'Slide 2', 
						'iconFonts' => 'font_awesome',
						'iconName' => 'fas fa-globe-europe',
						'txtcolor' => '',
						'iconColor' => '',
						'acttxtcolor' => '',
						'acticonColor' => '',
						'iconimageSize' => 'full',
						'dotBgtype' => [ 'openBg'=> 1,'bgType' => 'color','bgDefaultColor' => '#c2ccc4' ]
					]
				],
			],
			'dotLayout' => [
				'type' => 'string',
				'default' => 'horizontal',
			],
			'dotstyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'AborderColor' => [
				'type' => 'string',
				'default' => '#6f14f1',
			],
			'AniDuration' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-1' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item.style-1.active .active-border .border{ animation-duration: {{AniDuration}}ms ; }',
					],
				], 
			],
			'tooltipDir' => [
				'type' => 'string',
				'default' => 'top',
			],
			'vtooltipDir' => [
				'type' => 'string',
				'default' => 'left',
			],
			'dotSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item{ width : {{dotSize}}; height: {{dotSize}}; line-height:{{dotSize}}; }',
					],
				], 
				'scopy' => true,
			],
			'dotSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							['key' => 'dotLayout', 'relation' => '==', 'value' => 'horizontal']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-carousel-dots.dot-horizontal .tpgb-carodots-item{ margin-left: {{dotSpace}}; }  {{PLUS_WRAP}} .tpgb-carousel-dots.dot-horizontal .tpgb-carodots-item:first-child { margin-left: 0 ; }  {{PLUS_WRAP}}.tpgb-carousel-dots.dot-horizontal .tpgb-carodots-item::last-child { margin-right: 0; }',
						
						
					],
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							['key' => 'dotLayout', 'relation' => '==', 'value' => 'vertical']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-carousel-dots.dot-vertical .tpgb-carodots-item{ margin-top: {{dotSpace}}; }  {{PLUS_WRAP}} .tpgb-carousel-dots.dot-vertical .tpgb-carodots-item:first-child{ margin-top: 0 ; }  {{PLUS_WRAP}} .tpgb-carousel-dots.dot-vertical .tpgb-carodots-item:last-child{ margin-bottom: 0; }',
					],
				],
				'scopy' => true,
			],
			'diconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '20',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item .dot-icon{ font-size : {{diconSize}}; }',
					],
				], 
				'scopy' => true,
			],
			'dimgSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item .tpgb-dots img{ max-width : {{dimgSize}}; }',
					],
				], 
				'scopy' => true,
			],
			'dmargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item{ margin : {{dmargin}}; }',
					],
				], 
				'scopy' => true,
			],
			'dpadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ] ],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item{ padding : {{dpadding}}; }',
					],
				], 
				'scopy' => true,
			],
			'totipmargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt { margin : {{totipmargin}}; }',
					],
				], 
				'scopy' => true,
			],
			'totippadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>'',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-carodots-item .tpgb-dots .tooltip-txt { padding : {{totippadding}}; }',
					],
				], 
				'scopy' => true,
			],
			'totipAlign' => [
				'type' => 'string',
				'default' => 'center',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt { justify-content : {{totipAlign}}; }',
					],
				], 
				'scopy' => true,
			],
			'totipTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt',
					],
				],	
				'scopy' => true,
			],
			'totipColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt{color : {{totipColor}};}',
					],
				], 
				'scopy' => true,
			],
			'totipHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt:hover{color : {{totipHColor}};}',
					],
				], 
				'scopy' => true,
			],
			'totipBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt{ background: {{totipBgcolor}};}',
					],
				], 
				'scopy' => true,
			],
			'totipHBgcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt:hover{ background: {{totipHBgcolor}};}',
					],
				], 
				'scopy' => true,
			],
			'totipHgh' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt{ height : {{totipHgh}}px }',
					],
				], 
				'scopy' => true,
			],
			'tooltiparrow' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'arrowColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [	
					(object) [
						'condition' => [
							(object) [ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ],
							['key' => 'tooltiparrow', 'relation' => '==', 'value' => true],
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt.tooltip-arrow:after,.tpgb-carousel-dots.dot-vertical .tpgb-carodots-item.style-2 .tooltip-txt.tooltip-arrow:after{border-right-color : {{arrowColor}} }',
					],
				], 
				'scopy' => true,
			],
			'totipBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt',
					],
				], 
				'scopy' => true,
			],
			'tipBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showDot', 'relation' => '==', 'value' => true ],
							[ 'key' => 'dotstyle', 'relation' => '==', 'value' => 'style-2' ]
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-dots .tooltip-txt{ border-radius: {{tipBradius}}; }',
					],
				], 
				'scopy' => true,
			],
			'showpagi' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'sliderInd' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'noColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'showpagi', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .carousel-pagination .totalpage{ color : {{noColor}}; }',
					],
				], 
				'scopy' => true,
			],
			'noTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'showpagi', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .carousel-pagination .totalpage',
					],
				], 
				'scopy' => true,
			],
			'ActnoColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'showpagi', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .carousel-pagination .pagination-list .active{ color : {{ActnoColor}}; }',
					],
				], 
				'scopy' => true,
			],
			'ActnoTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'showpagi', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .carousel-pagination .pagination-list .active',
					],
				], 
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-carousel-remote', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_carousel_remote_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_carousel_remote' );