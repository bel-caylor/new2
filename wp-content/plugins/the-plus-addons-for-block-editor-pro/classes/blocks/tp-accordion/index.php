<?php
/**
 * Block : Accordion
 * @since 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_accordion_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$accordianList = (!empty($attributes['accordianList'])) ? $attributes['accordianList'] : [];
	$onHvrtab = (!empty($attributes['onHvrtab'])) ? $attributes['onHvrtab'] : '';
	$titleAlign = (!empty($attributes['titleAlign'])) ? $attributes['titleAlign'] : 'text-left';
	$toggleIcon = (!empty($attributes['toggleIcon'])) ? $attributes['toggleIcon'] : false;
	$iconFont = (!empty($attributes['iconFont'])) ? $attributes['iconFont'] : 'font_awesome';
	$iconName = (!empty($attributes['iconName'])) ? $attributes['iconName'] : 'fas fa-plus';
	$ActiconName = (!empty($attributes['ActiconName'])) ? $attributes['ActiconName'] : 'fas fa-minus';
	$iconAlign = (!empty($attributes['iconAlign'])) ? $attributes['iconAlign'] : 'end';
	$defaultAct = (!empty($attributes['defaultAct'])) ? $attributes['defaultAct'] : '0';
	$atOneOpen = (!empty($attributes['atOneOpen'])) ? "yes" : "no";
	$titleTag = (!empty($attributes['titleTag'])) ? $attributes['titleTag'] : 'div';
	$markupSch = (!empty($attributes['markupSch'])) ? $attributes['markupSch'] : false;
	$hoverStyle = (!empty($attributes['hoverStyle'])) ? $attributes['hoverStyle'] : '';
	$carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	$descAlign = (!empty($attributes['descAlign'])) ? $attributes['descAlign'] : '';
	
	$accorType = (!empty($attributes['accorType'])) ? $attributes['accorType'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$i=0;

	//Get Toogle icon
	$tgicon = '';
	if(!empty($toggleIcon)){	
		$tgicon .= '<div class="accordion-toggle-icon">';
			$tgicon .= '<span class="close-toggle-icon  toggle-icon">';
				$iconFont == 'font_awesome' ? $tgicon .= '<i class="'.esc_attr($iconName).'"></i>' : ''; 
			$tgicon .= '</span>';
			$tgicon .= '<span class="open-toggle-icon  toggle-icon">';
				$iconFont == 'font_awesome' ? $tgicon .= '<i class="'.esc_attr($ActiconName).'"></i>' : ''; 
			$tgicon .= '</span>';
		$tgicon .= '</div>';
	}
	
	//call Schema Markup
	$mainschema = $schemaAttr = $schemaAttr1 = $schemaAttr2 = $schemaAttr3 = '';
	if(!empty($markupSch)) {
		$mainschema = 'itemscope itemtype="https://schema.org/FAQPage"';
		$schemaAttr = 'itemscope itemprop="mainEntity" itemtype="https://schema.org/Question"';
		$schemaAttr1 = 'itemprop="name"';
		$schemaAttr2 = 'itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer"';
		$schemaAttr3 = 'itemprop="text"';
	}

	$loop_content = '';
	if(!empty($accordianList)){
		foreach ( $accordianList as $index => $item ) :
			$i++;
			
			//set active class
			$active = '';
			if($i==$defaultAct){
				$active = 'active';
			}

			$loop_content .= '<div class="tpgb-accor-item tpgb-relative-block" '.$schemaAttr.'>';
				$loop_content .= '<div id="'.(!empty($item['UniqueId']) ? esc_attr($item['UniqueId']) : 'tpag-tab-title-'.esc_attr($block_id).esc_attr($i) ).'" class="tpgb-accordion-header tpgb-trans-linear-before '.esc_attr($titleAlign).' '.esc_attr($active).'" role="tab" data-tab="'.esc_attr($i).'" aria-controls="tpag-tab-content-'.esc_attr($block_id).esc_attr($i).'">';
					if($iconAlign == 'start'){
						$loop_content .= $tgicon;
					}
					$loop_content .= '<span class="accordion-title-icon-wrap">';
						if(!empty($item['innerIcon'])){
							$loop_content .= '<span class="accordion-tab-icon">';
								$item['iconFonts'] == 'font_awesome' ?   $loop_content .= '<i class="'.esc_attr($item['innericonName']).'"></i>' : '';
							$loop_content .= '</span>';
						}
						$loop_content .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).' class="accordion-title" '.$schemaAttr1.'> '.wp_kses_post($item['title']).'</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($titleTag).'>';
					$loop_content .= '</span>';

					if($iconAlign == 'end'){
						$loop_content .= $tgicon;
					}

				$loop_content .= '</div>';

				$loop_content .= '<div id="tpag-tab-content-'.esc_attr($block_id).$i.'" class="tpgb-accordion-content '.esc_attr($active).'" role="tabpanel" data-tab="'.esc_attr($i).'" '.$schemaAttr2.' aria-labelledby="'.(!empty($item['UniqueId']) ? esc_attr($item['UniqueId']) : 'tpag-tab-title-'.esc_attr($block_id).esc_attr($i) ).'">';
					$loop_content .= '<div class="tpgb-content-editor '.esc_attr($descAlign).'" '.$schemaAttr3.'>';
						if( !empty($item['contentType']) && $item['contentType'] == 'content'){
							$loop_content .= Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['desc']);
						}else if($item['contentType'] == 'template' && !empty($item['blockTemp']) && $item['blockTemp']!='none'){
							ob_start();
								if(!empty($item['blockTemp'])) {
									echo Tpgb_Library()->plus_do_block($item['blockTemp']);
								}
								$loop_content .= ob_get_contents();
							ob_end_clean();
						}
					$loop_content .= '</div>';
				$loop_content .= '</div>';
			$loop_content .= '</div>';
		endforeach;
	}
	
	$dataAttr = '';
	if(!empty($carouselId)){
		$dataAttr .= ' id="tptab_'.esc_attr($carouselId).'"';
		$dataAttr .= ' data-accordion-id="tptab_'.esc_attr($carouselId).'"';
		$dataAttr .= ' data-connection="tpca-'.esc_attr($carouselId).'"';
	}
	$dataClass = '';
	if(!empty($hoverStyle) && $hoverStyle!='none'){
		$dataClass .= ' hover-'.esc_attr($hoverStyle);
	}
	$output .= '<div class="tpgb-accordion tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" '.$mainschema.'>';
		$output .= '<div class="tpgb-accor-wrap '.$dataClass.'" data-type="'.($onHvrtab == 'hover' ? 'hover' : 'accordion').'" '.$dataAttr.' data-one-onen="'.esc_attr($atOneOpen).'" role="tablist">';
			if( $accorType == 'editor' ){
				$output .= $content;
			}else{
				$output .= $loop_content;
			}
		$output .= '</div>';
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_accordion() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'accorType' => [
				'type' => 'string',
				'default' => 'content',
			],
			'accordianList' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'title' => [
							'type' => 'string',
							'default' => 'Accordion'
						],
						'desc' => [
							'type' => 'string',
							'default' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'
						],
						'innerIcon' => [
							'type' => 'boolean',
							'default' => false
						],
						'UniqueId' => [
							'type' => 'string',
							'default' => ''
						],
						'iconFonts' => [
							'type' => 'string',
							'default' => 'font_awesome'
						],
						'innericonName' => [
							'type'=> 'string',
							'default'=> 'fas fa-home',
						],
						'contentType' => [
							'type' => 'string',
							'default' => 'content'
						],
						'blockTemp' => [
							'type' => 'string',
							'default' => 'none'
						],
						'backendVisi' => [
							'type' => 'boolean',
							'default' => true
						],
						'stepImage' => [
							'type' => 'string'
						],
					],
				],
				'default' => [
					[
						"_key" => '0',
						"title" => 'Accordion 1',
						"contentType" => 'content',
						'desc' => 'This is just dummy content. Put your relevant content over here. We want to remind you, smile and passion are contagious, be a carrier.',
						'innerIcon' => false,
						'iconFonts' => 'font_awesome',
						'innericonName' => 'fas fa-home',
					],
					[
						"_key" => '1',
						"title" => 'Accordion 2',
						"contentType" => 'content',
						'desc' => 'Enter your relevant content over here. This is just dummy content.  We want to remind you, smile and passion are contagious, be a carrier.',
						'innerIcon' => false,
						'iconFonts' => 'font_awesome',
						'innericonName' => 'fas fa-home',
					],
				],
			],
			'toggleIcon' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'iconFont' => [
				'type' => 'string',
				'default' => 'font_awesome',	
			],
			'iconName' => [
				'type'=> 'string',
				'default'=> 'fas fa-plus ',
			],
			'ActiconName' => [
				'type'=> 'string',
				'default'=> 'fas fa-minus',
			],
			'titleTag' => [
				'type' => 'string',
				'default' => 'h3',
			],
			'defaultAct' => [
				'type' => 'string',
				'default' => '',
			],
			'onHvrtab' => [
				'type' => 'string',
				'default' => 'click',	
			],
			'atOneOpen' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'carouselId' => [
				'type' => 'string',
				'default' => '',
			],
			'markupSch' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'iconAlign' => [
				'type' => 'string',
				'default' => 'end',
			],
			'howTotitle' => [
				'type' => 'string',
				'default' => 'How To'
			],
			'howTodesc' => [
				'type' => 'string',
				'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo. '
			],
			'howToimg' => [
				'type' => 'object',
				'default'=> [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],	
			],
			'imageSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'howTostep' => [
				'type' => 'string',
				'default' => 'Steps to configure the How-to Schema:'
			],
			'inIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-tab-icon{ color: {{inIconColor}}; }',
					]
				],
				'scopy' => true,
			],
			'inIconActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active .accordion-tab-icon{ color: {{inIconActcolor}}; }',
					]
				],
				'scopy' => true,
			],
			'inIconGap' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-tab-icon { margin-right: {{inIconGap}}; }',
					]
				],
				'scopy' => true,
			],
			'inIconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-tab-icon{ font-size: {{inIconSize}}; }',
					]
				],
				'scopy' => true,
			],
			'tgiconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'toggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .close-toggle-icon{ color: {{tgiconColor}}; }',
					]
				],
				'scopy' => true,
			],
			'tgiconActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'toggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .open-toggle-icon{ color: {{tgiconActcolor}}; }',
					]
				],
				'scopy' => true,
			],
			'tgiconGap' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconAlign', 'relation' => '==', 'value' => 'start']],
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-toggle-icon { margin-right: {{tgiconGap}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'iconAlign', 'relation' => '==', 'value' => 'end']],
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-toggle-icon { margin-left: {{tgiconGap}}; }',
					],
				],
				'scopy' => true,
			],
			'tgiconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'toggleIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .toggle-icon{ font-size: {{tgiconSize}}; }',
					]
				],
				'scopy' => true,
			],
			'titleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-title',
					]
				],
				'scopy' => true,
			],
			'titleAlign' =>[
				'type' => 'string',
				'default' => 'text-left',
				'scopy' => true,
			],
			'titleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header .accordion-title{color : {{titleColor}}}',
					]
				],
				'scopy' => true,
			],
			'titleActcolor' => [
				'type' => 'string',
				'default' => '#6f14f1',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active .accordion-title{color : {{titleActcolor}}}',
					]
				],
				'scopy' => true,
			],
			'titleHvrcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header:hover .accordion-title{color : {{titleHvrcolor}}}',
					]
				],
				'scopy' => true,
			], 
			'titlePadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '10', 'bottom' => '10', 'left' => '10', 'right' => '10'],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header{ padding : {{titlePadding}}}',
					]
				],
				'scopy' => true,
			],
			'accorBetspace' => [
				'type' => 'object',
				'default' => [
					'md' => '5',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accor-wrap .tpgb-accor-item{ margin-bottom : {{accorBetspace}}}',
					]
				],
				'scopy' => true,
			],
			'titleBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header',
					]
				],
				'scopy' => true,
			],
			'titleBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header{ border-radius : {{titleBradius}} }',
					]
				],
				'scopy' => true,
			],
			'titleActborder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active',
					]
				],
				'scopy' => true,
			],
			'titleActBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active{ border-radius : {{titleActBradius}} }',
					]
				],
				'scopy' => true,
			],
			'titlebgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header',
					]
				],
				'scopy' => true,
			],
			'titleBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header',
					]
				],
				'scopy' => true,
			],
			'titleActbgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 1,
					'bgType' => 'color',
					'bgDefaultColor' => '#f7f7f7'
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active',
					]
				],
				'scopy' => true,
			],
			'titleActBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-header.active',
					]
				],
				'scopy' => true,
			],
			'descTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content .tpgb-content-editor',
					]
				],
				'scopy' => true,
			],
			'descAlign' => [
				'type'=> 'string',
				'default' => 'text-left',
				'scopy' => true,
			],
			'descColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content .tpgb-content-editor{color : {{descColor}}}',
					]
				],
				'scopy' => true,
			],
			'descMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content { margin : {{descMargin}}}',
					]
				],
				'scopy' => true,
			],
			'descPadding' => [	
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content { padding : {{descPadding}}}',
					]
				],
				'scopy' => true,
			],
			'descBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content',
					]
				],
				'scopy' => true,
			],
			'descBRedius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content{ border-radius : {{descBRedius}} }',
					]
				],
				'scopy' => true,
			],
			'descbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content',
					]
				],
				'scopy' => true,
			],
			'descboxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-accordion-content',
					]
				],
				'scopy' => true,
			],
			'hoverStyle' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'hoverColor' => [
				'type' => 'string',
				'default' => '#232323',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'hoverStyle', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-accor-wrap.hover-style-1 .tpgb-accordion-header:before{ background: {{hoverColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'hoverStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-accor-wrap.hover-style-2 .tpgb-accordion-header:before{ background: {{hoverColor}}; }',
					],
				],
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-accordion', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_accordion_render_callback'
    ) );
}


add_action( 'init', 'tpgb_tp_accordion' );