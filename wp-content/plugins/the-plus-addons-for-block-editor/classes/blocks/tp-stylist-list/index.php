<?php
/* Block : Stylist List
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_stylist_list_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $alignment = (!empty($attributes['alignment'])) ? $attributes['alignment'] : 'align-left';
    $iconAlignment = (!empty($attributes['iconAlignment'])) ? $attributes['iconAlignment'] : false;
    $listsRepeater = (!empty($attributes['listsRepeater'])) ? $attributes['listsRepeater'] : [];
    $hoverInverseEffect = (!empty($attributes['hoverInverseEffect'])) ? $attributes['hoverInverseEffect'] : false;
	
    $readMoreToggle = (!empty($attributes['readMoreToggle'])) ? $attributes['readMoreToggle'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$alignattr ='';
	if($alignment!==''){
		$alignattr .= (!empty($alignment['md'])) ? ' align-'.esc_attr($alignment['md']) : ' align-left';
		$alignattr .= (!empty($alignment['sm'])) ? ' tablet-align-'.esc_attr($alignment['sm']) : '';
		$alignattr .= (!empty($alignment['xs'])) ? ' mobile-align-'.esc_attr($alignment['xs']) : '';
	}
	$iconalignattr = (!empty($iconAlignment)) ? ' d-flex-center' : ' d-flex-top';
	
	$hoverInvertClass ='';
	if( $hoverInverseEffect ){
		$hoverInvertClass = ($hoverInverseEffect) ? ' hover-inverse-effect' : '';
	}
	
	$i=0;$j=0;
	
    $output .= '<div class="tpgb-stylist-list tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($alignattr).' '.esc_attr($hoverInvertClass).' '.esc_attr($blockClass).'">';
		if(!empty($listsRepeater)){
		
			
			$output .= '<div class="tpgb-icon-list-items'.esc_attr($iconalignattr).'">';
				foreach ( $listsRepeater as $index => $item ) :
					
					$i++;
					$active_class=$descurl_open=$descurl_close='';
					if($i==1){
						$active_class='active';
					}
					//Url
					if(!empty($item['descurl']) && !empty($item['descurl']['url'])){
						$target = ($item['descurl']['target']!='') ? '_blank' : '';
						$nofollow = ($item['descurl']['nofollow']!='') ? 'nofollow' : '';
						$link_attr = Tp_Blocks_Helper::add_link_attributes($item['descurl']);
						$descurl_open ='<a href="'.esc_url($item['descurl']['url']).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.'>';
						$descurl_close ='</a>';
					}
					
					//Icon
					$icons ='';
					if(!empty($item['selectIcon'])){
						$icons .= '<div class="tpgb-icon-list-icon">';
							if($item['selectIcon']=='fontawesome' && !empty($item['iconFontawesome'])){ 
								$icons .='<i class="list-icon '.esc_attr($item['iconFontawesome']).'" aria-hidden="true"></i>';
							}else if($item['selectIcon'] == 'img' && !empty($item['iconImg']['url'])){
								$imgSrc = '';
								if(!empty($item['iconImg']) && !empty($item['iconImg']['id'])){
									$imgSrc = wp_get_attachment_image($item['iconImg']['id'] , 'full');
								}else if( !empty($item['iconImg']['url']) ){
									$imgSrc = '<img src="'.esc_url($item['iconImg']['url']).'"  alt="'.esc_attr__('icon-img','tpgb').'" />';
								}
								$icons .= $imgSrc;
							} 
						$icons .= '</div>';
					}
					
					//Description and Pin
					$itemdesc = '';
					if(!empty($item['description'])){
						$itemdesc .= '<div class="tpgb-icon-list-text"><p>'.wp_kses_post($item['description']).'</p></div>';
					}

					//Item Content
					$output .= '<div class="tpgb-icon-list-item tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($active_class).'" >';
						$output .= $descurl_open;
						$output .= $icons;
						$output .= $itemdesc;
						$output .= $descurl_close;
					$output .= "</div>";
				endforeach;
			$output .= "</div>";
			
		}
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_stylist_list() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'hover_bg_style' => array(
                'type' => 'boolean',
				'default' => false,
			),
			'listsRepeater' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'description' => [
							'type' => 'string',
							'default' => 'List item',
						],
						'selectIcon' => [
							'type' => 'string',
							'default' => 'fontawesome',
						],
						'iconFontawesome' => [
							'type' => 'string',
							'default' => 'fas fa-check-circle',
						],
						'itemTooltip' => [
							'type' => 'boolean',
							'default' => false,
						],
						'tooltipContentType' => [
							'type' => 'string',
							'default' => '',
						],
						'tooltipTypo' => [
							'type' => 'object',
							'default' => (object) [
								'openTypography' => 0,
							],
						],
						'tooltipColor' => [
							'type' => 'string',
							'default' => '',
						],
					],
				], 
				'default' => [
					[
						"_key" => '0',
						"description" => "List item 1",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
					],
					[
						"_key" => '1',
						"description" => "List item 2",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
					],
					[ 
						"_key" => '2',
						"description" => "List item 3",
						"selectIcon" => "fontawesome",
						"iconFontawesome" => "fas fa-check-circle",
						'tooltipTypo' => ['openTypography' => 0 ],
					]
				],
			],
			'listType' => [
                'type' => 'string',
				'default' => 'vertical',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items, {{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{flex-wrap: wrap;flex-flow: wrap;}  {{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{ margin : 0px }',
					],
				],
				'scopy' => true,
			],
			'readMoreToggle' => [
                'type' => 'boolean',
				'default' => false,
			],
			
			'listSpaceBetween' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'vertical']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-top: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-bottom: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child):before{ top: calc(100% + {{listSpaceBetween}}/2); }',
					],
					(object) [
						'condition' => [(object) ['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-top: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-bottom: calc({{listSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:before{ top: calc(100% + {{listSpaceBetween}}/2);}',
					],
				],
				'scopy' => true,
			],
			'horizontalSpaceBetween' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'left'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: {{horizontalSpaceBetween}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'right'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: {{horizontalSpaceBetween}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'center'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-left:0;margin-right:0}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: calc({{horizontalSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: calc({{horizontalSpaceBetween}}/2);}',
					],
					(object) [
						'condition' => [(object) ['key' => 'alignment', 'relation' => '==', 'value' => 'justify'],['key' => 'listType', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{margin-left:0;margin-right:0}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:first-child){margin-left: calc({{horizontalSpaceBetween}}/2);}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child){margin-right: calc({{horizontalSpaceBetween}}/2);}',
					],
				],
				'scopy' => true,
			],
			'alignment' => [
                'type' => 'object',
				'default' => ['md' => 'left'],
				'scopy' => true,
			],
			'separatorColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item:not(:last-child):before{border-bottom : 1px solid {{separatorColor}};}{{PLUS_WRAP}} .tpgb-icon-list-items .tpgb-icon-list-item{width: 100%;}',
					],
				],
				'scopy' => true,
			],
			
			'iconNormalColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-icon .list-icon{color: {{iconNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'iconHoverColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-icon .list-icon{color: {{iconHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'iconSize' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon .list-icon{font-size: {{iconSize}};}',
					],
				],
				'scopy' => true,
			],
			'iconImgSize' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item .tpgb-icon-list-icon img{max-width: {{iconImgSize}};}',
					],
				],
				'scopy' => true,
			],
			'iconAlignment' => [
                'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			
			'iconAdvancedStyle' => [
                'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			
			'textTypo' => [
                'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text,{{PLUS_WRAP}} .tpgb-icon-list-text p',
					],
				],
				'scopy' => true,
			],
			'textNormalColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-text{color: {{textNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'textHoverColor' => [
                'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover .tpgb-icon-list-text{color: {{textHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'textIndent' => [
                'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-stylist-list .tpgb-icon-list-text{padding-left: {{textIndent}};}',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item{padding: {{textPadding}};}',
					],
				],
				'scopy' => true,
			],
			'textBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			'textBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item{border-radius: {{textBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'textBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			'titleBShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item',
					],
				],
				'scopy' => true,
			],
			
			'textHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			'textHBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover{border-radius: {{textHBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'textBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			'titleHBShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-icon-list-item:hover',
					],
				],
				'scopy' => true,
			],
			
			'hoverInverseEffect' => [
                'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'unhoverItemOpacity' => [
                'type' => 'string',
				'default' => 0.6,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'hoverInverseEffect', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.hover-inverse-effect:hover .on-hover .tpgb-icon-list-item{opacity: {{unhoverItemOpacity}};}',
					],
				],
				'scopy' => true,
			],
		);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-stylist-list', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_stylist_list_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_stylist_list' );