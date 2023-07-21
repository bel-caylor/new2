<?php
/* Block : Number Counter
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_number_counter_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$title = (!empty($attributes['title'])) ? $attributes['title'] : '';
	$style1Align = (!empty($attributes['style1Align'])) ? $attributes['style1Align'] : 'text-center';
	$style2Align = (!empty($attributes['style2Align'])) ? $attributes['style2Align'] : 'text-left';
	$numValue = (!empty($attributes['numValue'])) ? $attributes['numValue'] : '1000';
	$startValue = (!empty($attributes['startValue'])) ? $attributes['startValue'] : '0';
	$timeDelay = (!empty($attributes['timeDelay'])) ? $attributes['timeDelay'] : '5';
	$numGap = (!empty($attributes['numGap'])) ? $attributes['numGap'] : '5';
	$symbol = (!empty($attributes['symbol'])) ? $attributes['symbol'] : '';
	$symbolPos = (!empty($attributes['symbolPos'])) ? $attributes['symbolPos'] : 'after';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'icon';
	$iconStyle = (!empty($attributes['iconStyle'])) ? $attributes['iconStyle'] : 'square';
	$iconStore = (!empty($attributes['iconStore'])) ? $attributes['iconStore'] : '';
	$linkURL = (!empty($attributes['linkURL']['url'])) ? $attributes['linkURL']['url'] : '';
	$imagestore = (!empty($attributes['imagestore'])) ? $attributes['imagestore'] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'thumbnail';
	$target = (!empty($attributes['linkURL']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['linkURL']['nofollow'])) ? 'nofollow' : '';
	$verticalCenter = (!empty($attributes['verticalCenter'])) ? $attributes['verticalCenter'] : false;
	$preSymbol = (!empty($attributes['preSymbol'])) ? $attributes['preSymbol'] : '';
	
	$svgIcon = (!empty($attributes['svgIcon'])) ? $attributes['svgIcon'] : '';
	$svgDraw = (!empty($attributes['svgDraw'])) ? $attributes['svgDraw'] : 'delayed';
	$svgstroColor = (!empty($attributes['svgstroColor'])) ? $attributes['svgstroColor'] : '';
	$svgfillColor = (!empty($attributes['svgfillColor'])) ? $attributes['svgfillColor'] : 'none';
	$svgDura = (!empty($attributes['svgDura'])) ? $attributes['svgDura'] : 90;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	if(!empty($imagestore) && !empty($imagestore['id'])){
		$imgSrc = wp_get_attachment_image($imagestore['id'] , $imageSize, false, ['class' => 'counter-icon-image']);
	}else if(!empty($imagestore['url'])){

		$imgUrl = (isset($imagestore['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($imagestore) : $imagestore['url'];
		$imgSrc = '<img class="counter-icon-image" src='.esc_url($imgUrl).' alt="'.esc_attr__('Counter Number','tpgb').'"/>';
	}else{
		$imgSrc = '<img class="counter-icon-image" src='.esc_url($imagestore).' alt="'.esc_attr__('Counter Number','tpgb').'"/>';
	}
	
	$vCenter = '';
	if(!empty($verticalCenter)){
		$vCenter='vertical-center';
	}
	
	$alignment = '';
	if($style=='style-1'){
		$alignment=$style1Align;
	}
	if($style=='style-2'){
		$alignment=$style2Align;
	}
	$tranease = 'tpgb-trans-ease';
		
	$getCounterNo = '';
	$getCounterNo .= '<h5 class="nc-counter-number '.esc_attr($tranease).'">';
		if( (!empty($symbol) && $symbolPos=='before') || (!empty($preSymbol) && $symbolPos=='both') ){
			$getCounterNo .= '<span class="counter-symbol-text">'.( (!empty($preSymbol) && $symbolPos=='both') ? wp_kses_post($preSymbol) : wp_kses_post($symbol)).'</span>';
		}

		//Get Dynamic Value
		$numValue = (!empty($numValue) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($numValue) : $numValue;
		$startValue = (!empty($startValue) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($startValue) : $startValue;

		$getCounterNo .= '<span class="counter-number-inner numscroller" data-min="'.esc_attr($startValue).'" data-max="'.esc_attr($numValue).'" data-delay="'.esc_attr($timeDelay).'" data-increment="'.esc_attr($numGap).'">';
			$getCounterNo .= $startValue;
		$getCounterNo .= '</span>';
		if( (!empty($symbol) && $symbolPos=='after') || $symbolPos=='both' ){
			$getCounterNo .= '<span class="counter-symbol-text">'.wp_kses_post($symbol).'</span>';
		}
	$getCounterNo .= '</h5>';
	
	$getTitle = '';
	$link_attr = Tp_Blocks_Helper::add_link_attributes($attributes['linkURL']);
	$ariaLabel = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : ((!empty($title)) ? esc_attr($title) : esc_attr__("Number Counter", 'tpgb'));
	if(!empty($linkURL)){
		$getTitle .='<a href="'.esc_url($linkURL).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.esc_attr($title).'">';
	}
	$getTitle .= '<h6 class="counter-title '.esc_attr($tranease).'">'.wp_kses_post($title).'</h6>';
	if(!empty($linkURL)){
		$getTitle .= '</a>';
	}
	
	$getIcon = '';
	if(!empty($linkURL)){
		$getIcon .='<a href="'.esc_url($linkURL).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.$ariaLabel.'">';
	}
			$getIcon .= '<div class="counter-icon-inner shape-icon-'.esc_attr($iconStyle).' '.esc_attr($tranease).'">';
				$getIcon .= '<span class="counter-icon '.esc_attr($tranease).'">';
					$getIcon .= '<i class="'.esc_attr($iconStore).'"></i>';
				$getIcon .= '</span>';
			$getIcon .= '</div>';
	if(!empty($linkURL)){
		$getIcon .= '</a>';
	}
	
	$getImg = '';
	if(!empty($linkURL)){
		$getImg .= '<a href="'.esc_url($linkURL).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.$ariaLabel.'">';
	}
			$getImg .= '<div class="counter-image-inner '.esc_attr($tranease).'">';
				$getImg .= $imgSrc;
			$getImg .= '</div>';
	if(!empty($linkURL)){
		$getImg .= '</a>';
	}
	
	$getsvg = '';
	$getsvg .= '<div class="tpgb-draw-svg" data-id="service-svg-'.esc_attr($block_id).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes">';
	if(!empty($linkURL)){
		$getsvg .= '<a href="'.esc_url($linkURL).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.$ariaLabel.'">';
	}
		
		$getsvg .= '<object id="service-svg-'.esc_attr($block_id).'" role="none" type="image/svg+xml" data="'.$svgIcon['url'].'">';
		$getsvg .= '</object>';

	if(!empty($linkURL)){
		$getsvg .= '</a>';
	}
	$getsvg .= '</div>';
	$output = '';
    $output .= '<div class="tpgb-number-counter tpgb-relative-block counter-'.esc_attr($style).' '.esc_attr($alignment).' tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		$output .= '<div class="number-counter-inner tpgb-relative-block '.esc_attr($tranease).' '.esc_attr($vCenter).'">';
			if($style=='style-1'){
				$output .= '<div class="counter-wrap-content">';
					if($iconType=='icon'){
						$output .= $getIcon;
					}
					if($iconType=='img'){
						$output .= $getImg;
					}
					if($iconType=='svg'){
						$output .= $getsvg;
					}
					$output .= $getCounterNo;
					if(!empty($title)){
						$output .= $getTitle;
					}
				$output .= '</div>';
			}
			if($style=='style-2'){
				$output .= '<div class="icn-header">';
					if($iconType=='icon'){
						$output .= $getIcon;
					}
					if($iconType=='img'){
						$output .= $getImg;
					}
					if($iconType=='svg'){
						$output .= $getsvg;
					}
				$output .= '</div>';
				$output .= '<div class="counter-content">';
					$output .= $getCounterNo;
					if(!empty($title)){
						$output .= $getTitle;
					}
				$output .= '</div>';
			}
		$output .= '</div>';
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_number_counter() {
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
			'default' => 'style-1',	
		],
		'style1Align' => [
			'type' => 'string',
			'default' => 'text-center',
		],
		'style2Align' => [
			'type' => 'string',
			'default' => 'text-left',
		],
		'title' => [
			'type' => 'string',
			'default' => 'Awards Won',	
		],
		'linkURL' => [
			'type'=> 'object',
			'default'=> [
				'url' => '#',	
				'target' => '',
				'nofollow' => ''
			],
		],
		'ariaLabel' => [
			'type' => 'string',
			'default' => '',	
		],
		'numValue' => [
			'type' => 'string',
			'default' => '999',	
		],
		'startValue' => [
			'type' => 'string',
			'default' => '0',	
		],
		'numGap' => [
			'type' => 'string',
			'default' => '5',	
		],
		'timeDelay' => [
			'type' => 'string',
			'default' => '5',	
		],
		'symbol' => [
			'type' => 'string',
			'default' => '',	
		],
		'symbolPos' => [
			'type' => 'string',
			'default' => 'after',	
		],
		'preSymbol' => [
			'type' => 'string',
			'default' => '',
		],
		'iconType' => [
			'type' => 'string',
			'default' => 'icon',	
		],
		'iconStore' => [
			'type'=> 'string',
			'default'=> 'fas fa-award',
		],
		'imagestore' => [
			'type' => 'object',
			'default' => [
				'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
			],
		],
		'imageSize' => [
			'type' => 'string',
			'default' => 'thumbnail',	
		],
		'svgIcon' => [
			'type' => 'object',
			'default' => [
				'url' => '',
			],
		],
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-title',
				],
			],
			'scopy' => true,
		],
		'titleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-title{ color: {{titleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner:hover .counter-title{ color: {{titleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleTopSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-title{ margin-top: {{titleTopSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'titleBottomSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-title{ margin-bottom: {{titleBottomSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'digitTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .nc-counter-number',
				],
			],
			'scopy' => true,
		],
		'digitNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .nc-counter-number{ color: {{digitNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'digitHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'selector' => '{{PLUS_WRAP}} .number-counter-inner:hover .nc-counter-number{ color: {{digitHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'digitTopSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .nc-counter-number{ margin-top: {{digitTopSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'symbolTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'symbol', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-symbol-text',
				],
			],
			'scopy' => true,
		],
		'symbolNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'symbol', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-symbol-text{ color: {{symbolNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'symbolHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'symbol', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner:hover .counter-symbol-text{ color: {{symbolHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconStyle' => [
			'type' => 'string',
			'default' => 'none',
			'scopy' => true,
		],
		'iconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-icon-inner .counter-icon{ font-size: {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-icon-inner { width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-icon-inner .counter-icon{ color: {{icnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner:hover .counter-icon{ color: {{icnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNormalBG' => [
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
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner .counter-icon-inner',
				],
			],
			'scopy' => true,
		],
		'icnHoverBG' => [
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
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}} .number-counter-inner:hover .counter-icon-inner',
				],
			],
			'scopy' => true,
		],
		'nmlBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .counter-icon-inner{ border-color: {{nmlBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hvrBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover .counter-icon-inner{ border-color: {{hvrBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'nmlIcnBRadius' => [
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
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .counter-icon-inner{border-radius: {{nmlIcnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'hvrIcnBRadius' => [
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
					'condition' => [
						(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ],
						(object) [ 'key' => 'iconStyle', 'relation' => '==', 'value' => ['none','square','rounded'] ]
					],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover .counter-icon-inner{border-radius: {{hvrIcnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'nmlIcnShadow' => [
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
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .counter-icon-inner',
				],
			],
			'scopy' => true,
		],
		'hvrIcnShadow' => [
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
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover .counter-icon-inner',
				],
			],
			'scopy' => true,
		],
		'imgWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'img' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .counter-image-inner { max-width: {{imgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'bgNmlBorder' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner',
				],
			],
			'scopy' => true,
		],
		'bgHvrBorder' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover',
				],
			],
			'scopy' => true,
		],
		'bgNmlBRadius' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner{border-radius: {{bgNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'bgHvrBRadius' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover {border-radius: {{bgHvrBRadius}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover',
				],
			],
			'scopy' => true,
		],
		'bgNmlShadow' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner',
				],
			],
			'scopy' => true,
		],
		'bgHvrShadow' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner:hover',
				],
			],
			'scopy' => true,
		],
		'bgPadding' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-number-counter .number-counter-inner{padding: {{bgPadding}};}',
				],
			],
			'scopy' => true,
		],
		'verticalCenter' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'svgDraw' => [
			'type' => 'string',
			'default' => 'delayed',	
			'scopy' => true,
		],
		'svgDura' => [
			'type' => 'string',
			'default' => '90',
			'scopy' => true,
		],
		'svgmaxWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .counter-wrap-content .tpgb-draw-svg{ max-width: {{svgmaxWidth}}; max-height: {{svgmaxWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'svgstroColor' => [
			'type' => 'string',
			'default' => '#000000',
			'scopy' => true,
		],
		'svgfillColor' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-number-counter', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_number_counter_render_callback'
    ) );
}
add_action( 'init', 'tpgb_number_counter' );