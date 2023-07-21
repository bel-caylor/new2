<?php
/* Block : Hotspot
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_hotspot_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$pinlistRepeater = (!empty($attributes['pinlistRepeater'])) ? $attributes['pinlistRepeater'] : [];
	$hotspotImage = (!empty($attributes['hotspotImage'])) ? $attributes['hotspotImage'] : [] ;
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] :'';
	$delaytimeout = (!empty($attributes['delaytimeout'])) ? $attributes['delaytimeout'] : 0;

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$i=0;
	$pin_content = '';
	if(!empty($pinlistRepeater)){
		foreach ( $pinlistRepeater as $index => $item ) {

			$i++;
			//Get Attributes of ToolTip
			$itemtooltip = '';
			$uniqid=uniqid("tooltip");
			if(!empty($item['itemTooltip'])){
				$itemtooltip .= ' data-tippy="" ';
				$itemtooltip .= ' data-tippy-interactive="'.(!empty($item['tipInteractive']) ? 'true' : 'false').'" ';
				$itemtooltip .= ' data-tippy-placement="'.(!empty($item['tipPlacement']) ? $item['tipPlacement'] : 'top').'" ';
				$itemtooltip .= ' data-tippy-followCursor="'.(!empty($item['followCursor']) ? 'true' : 'false').'" ';
				$itemtooltip .= ' data-tippy-theme="'.(!empty($item['tipTheme']) ? $item['tipTheme'] : 'material').'"';
				$itemtooltip .= ' data-tippy-arrow="'.(!empty($item['tipArrow']) ? 'true' : 'false').'"';
				
				$itemtooltip .= ' data-tippy-animation="'.(!empty($item['tipAnimation']) ? $item['tipAnimation'] : 'fade').'"';
				$itemtooltip .= ' data-tippy-offset="['.(!empty($item['tipOffset']) ? (int)$item['tipOffset'] : 0).','.(!empty($item['tipDistance']) ? (int)$item['tipDistance'] : 0).']"';
				$itemtooltip .= ' data-tippy-duration="['.(!empty($item['tipDurationIn']) ? (int)$item['tipDurationIn'] : '1').','.(!empty($item['tipDurationOut']) ? (int)$item['tipDurationOut'] : '1').']"';
				$itemtooltip .= ' data-tippy-delay="['.(!empty($item['tipDelayIn']) ? (int)$item['tipDelayIn'] : '1').','.(!empty($item['tipDelayOut']) ? (int)$item['tipDelayOut'] : '1').']"';
			}
			
			//Set Link to Tooltip
			$pincurl_open = '';
			$pinurl_close = '';
			if(!empty($item['pinLink'])){
				$link = (isset($item['pinUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['pinUrl']) : (!empty($item['pinUrl']['url']) ? $item['pinUrl']['url'] : '#');
				$target = ($item['pinUrl']['target']!='') ? 'target="_blank"' : '';
				$nofollow = ($item['pinUrl']['nofollow']!='') ? 'rel="nofollow"' : '';
				$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['pinUrl']);
				$ariaLabelT = (!empty($item['ariaLabel'])) ? esc_attr($item['ariaLabel']) : esc_attr__('Pin Point', 'tpgbp');
				$pincurl_open ='<a href="'.esc_url($link).'" '.$target.' '.$nofollow.' '.$link_attr.' aria-label="'.$ariaLabelT.'">';
				$pinurl_close ='</a>';
			}

			$contentItem =[];
			$contentItem['content'] = (!empty($item['tooltipText'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['tooltipText']) : '';
			$contentItem['trigger'] = (!empty($item['tipTriggers'])  ? $item['tipTriggers'] : 'mouseenter');
			$contentItem['MaxWidth'] = (!empty($item['tipMaxWidth']) ? (int)$item['tipMaxWidth'] : 'none');
			$contentItem = htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8');

			//get Pin icon
			$pin_icon = '';
			$pin_icon .= '<div id="'.esc_attr($uniqid).'" class="pin-hotspot tpgb-trans-easeinout tp-repeater-item-'.esc_attr($item['_key']).' " '.$itemtooltip.'  data-tooltip-opt= \'' .$contentItem. '\'  data-hotspot="'.esc_attr($i).'" >';
				$pin_icon .= '<div  class="pin-hotspot-wrapper amimation-in">';
					$pin_icon .= '<div class="pin-hover '.(!empty($item['contEffect']) ? ( $item['contEffect'] == 'pulse' || $item['contEffect'] == 'floating' || $item['contEffect'] == 'tossing' ? 'tpgb-'.$item['contEffect'] : $item['contEffect']  ) : '' ).' ">';
						$pin_icon .= '<div class="pin-content pin-type-'.esc_attr($item['pinType']).' tpgb-trans-easeinout ">';
							if($item['pinType'] == 'icon' && $item['pinIconType'] == 'font_awesome'){
								$pin_icon .= '<i class="pin-icon tpgb-trans-easeinout '.esc_attr($item['pinIcon']).'"></i>';
							}
							if($item['pinType'] == 'image'){
								if(!empty($item['pinImage']) && !empty($item['pinImage']['id'])){
									$icon_image=$item['pinImage']['id'];
									$pinimgsize = (!empty($item['pinimgSize']) ? $item['pinimgSize'] : 'thumbnail' );
									$icon_image = wp_get_attachment_image($icon_image,$pinimgsize, false, ['class' => 'pin-icon tpgb-trans-easeinout']);
								}else if(!empty($item['pinImage']['url'])){
									$icon_image = (isset($item['pinImage']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['pinImage']) : (!empty($item['pinImage']['url']) ? $item['pinImage']['url'] : '');
									$icon_image = '<img class="pin-icon tpgb-trans-easeinout" src="'.esc_url($icon_image).'" alt="'.esc_html__('pin-image','tpgbp').'" />';
								}else{
									$icon_image='<img class="pin-icon tpgb-trans-easeinout" src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'" alt="'.esc_html__('pin-image','tpgbp').'" />';
								}
								$pin_icon .= $icon_image;
							}
							if($item['pinType'] == 'text' && !empty($item['pinText'])){
								$pin_icon .= '<div class="pin-icon tpgb-trans-easeinout">';
									$pin_icon .= wp_kses_post($item['pinText']);
								$pin_icon .= '</div>';
							}
						$pin_icon .= '</div>';
					$pin_icon .= '</div>';
				$pin_icon .= '</div>';
			$pin_icon .= '</div>';
			
			$pin_content .= $pincurl_open;
				$pin_content .= $pin_icon;
			$pin_content .= $pinurl_close;
			
		}	
	}

	//Set Image Url
	if(!empty($hotspotImage) && !empty($hotspotImage['id'])){
		$imgSrc = wp_get_attachment_image($hotspotImage['id'] , $imageSize, false, ['class' => 'hotspot-image']);
	}else if(!empty($hotspotImage['url'])){
		$imgSrc = '<img class="hotspot-image" src="'.esc_url($hotspotImage['url']).'" alt="'.esc_attr__('hotspot-image','tpgbp').'" />';
	}else{
		$imgSrc = '<img class="hotspot-image" src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'" alt="'.esc_attr__('hotspot-image','tpgbp').'" />';
	}

    $output .= '<div class="tpgb-hotspot tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' ">';
		$output .= '<div class="tpgb-hotspot-inner tpgb-relative-block overlay-bg-color" >';
			$output .= $imgSrc;
			$output .= '<div class="hotspot-overlay tpgb-trans-easeinout">';
				$output .= $pin_content;
			$output .= "</div>";
		$output .= "</div>";
    $output .= "</div>";
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_hotspot() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'hotspotImage' => [
				'type' => 'object',
				'default'=> [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],
			],
			'imageSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'pinlistRepeater' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'pinType' => [
							'type' => 'string',
							'default' => 'icon'
						],
						'pinIconType' => [
							'type' => 'string',
							'default' => 'font_awesome'
						],
						'pinIcon' => [
							'type' => 'string',
							'default' => 'fas fa-plus'
						],
						'pinImage' => [
							'type' => 'object'
						],
						'pinText' => [
							'type' => 'string',
							'default' => 'Pin',
						],
						'DleftAuto' => [
							'type' => 'boolean',
							'default' => true
						],
						'DrightAuto' => [
							'type' => 'boolean',
							'default' => false
						],
						'DtopAuto' => [
							'type' => 'boolean',
							'default' => true
						],
						'DbottomAuto' => [
							'type' => 'boolean',
							'default' => false
						],
						'TabRespo' => [
							'type' => 'boolean',
							'default' => false
						],
						'itemTooltip' => [
							'type' => 'boolean',
							'default' => true
						],
						'tooltipContentType' => [
							'type' => 'string',
							'default' => 'text',
						],
						'tooltipText' => [
							'type' => 'string',
							'default' => 'Your content will be here.',
						],
						'piniconColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .pin-hotspot-wrapper .pin-icon{color:{{piniconColor}};}',
								],
							],
						],
						'pinBgcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .pin-hotspot-wrapper .pin-content {background:{{pinBgcolor}};}',
								],
							],
						],
						'piniconHvrColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .pin-hotspot-wrapper:hover .pin-icon {color:{{piniconHvrColor}};}',
								],
							],
						],
						'pinHvrBgcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .pin-hotspot-wrapper:hover .pin-content{background:{{pinHvrBgcolor}};}',
								],
							],
						],
						'leftPos' => [
							'type' => 'string',
							'default' => 20,
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'DleftAuto', 'relation' => '==', 'value' => true]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {left:{{leftPos}}%;}',
								],
							],
						],
						'righttPos' => [
							'type' => 'string',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'DrightAuto', 'relation' => '==', 'value' => true]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {right:{{righttPos}}%;}',
								],
							],
						],
						'topPos' => [
							'type' => 'string',
							'default' => 25,
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'DtopAuto', 'relation' => '==', 'value' => true]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {top:{{topPos}}%;}',
								],
							],
						],
						'bottomPos' => [
							'type' => 'string',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'DbottomAuto', 'relation' => '==', 'value' => true]],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} {bottom:{{bottomPos}}%;}',
								],
							],
						],
						'TleftPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'TabRespo', 'relation' => '==', 'value' => true],
										['key' => 'TleftAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:1024px){ {{PLUS_WRAP}} {{TP_REPEAT_ID}} {left:{{TleftPos}}%;} }',
								],
							],
						],
						'TtopPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'TabRespo', 'relation' => '==', 'value' => true],
										['key' => 'TtopAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:1024px){ {{PLUS_WRAP}} {{TP_REPEAT_ID}} {top:{{TtopPos}}%;} }',
								],
							],
						],
						'TrightPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'TabRespo', 'relation' => '==', 'value' => true],
										['key' => 'TrightAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:1024px){ {{PLUS_WRAP}} {{TP_REPEAT_ID}} {right:{{TrightPos}}%;} }',
								],
							],
						],
						'TbottomPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'TabRespo', 'relation' => '==', 'value' => true],
										['key' => 'TbottomAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:1024px){ {{PLUS_WRAP}} {{TP_REPEAT_ID}} {bottom:{{TbottomPos}}%;} }',
								],
							],
						],
						'MleftPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'MobRespo', 'relation' => '==', 'value' => true],
										['key' => 'MleftAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:767px) { {{PLUS_WRAP}} {{TP_REPEAT_ID}} {left:{{MleftPos}}%;} }',
								],
							],
						],
						'MrightPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'MobRespo', 'relation' => '==', 'value' => true],
										['key' => 'MrightAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:767px) { {{PLUS_WRAP}} {{TP_REPEAT_ID}} {right:{{MrightPos}}%;} }',
								],
							],
						],
						'MtopPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'MobRespo', 'relation' => '==', 'value' => true],
										['key' => 'MtopAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:767px) { {{PLUS_WRAP}} {{TP_REPEAT_ID}} {top:{{MtopPos}}%;} }',
								],
							],
						],
						'MbottomPos' => [
							'style' => [
								(object) [
									'condition' => [
										(object) ['key' => 'MobRespo', 'relation' => '==', 'value' => true],
										['key' => 'MbottomAuto', 'relation' => '==', 'value' => true]
									],
									'selector' => '@media (max-width:767px) { {{PLUS_WRAP}} {{TP_REPEAT_ID}} {bottom:{{MbottomPos}}%;} }',
								],
							],
						],
						'toltipAlign' => [
							'type' => 'string',
							'default' => 'center',
							'style' => [
								(object) [
									'selector' => ' {{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content{text-align: {{toltipAlign}} }',
								],
							],
						],
						'tooltipTypo' => [
							'type' => 'object',
							'default' => (object) [
								'openTypography' => 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content',
								],
							],
						],
						'tooltipColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-content{color:{{tooltipColor}};}',
								],
							],
						],
						'contEffect' => [
							'type' => 'string',
							'default' => 'normal_drop_waves'
						],
						'pinimgSize' => [
							'type' => 'string',
							'default' => 'full'
						],
						'tipInteractive' => [
							'type' => 'boolean',
							'default' => false,
						],
						'followCursor' => [
							'type' => 'boolean',
							'default' => false,
						],
						'tipPlacement' => [
							'type' => 'string',
							'default' => 'top',
						],
						'tipArrow' => [
							'type' => 'boolean',
							'default' => true
						],
						'tipTriggers' => [
							'type' => 'string',
							'default' => 'mouseenter'
						],
						'tipTheme' => [
							'type' => 'string',
							'default' => 'light',
						],
						'tipMaxWidth' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}}.tpgb-hotspot {{TP_REPEAT_ID}} .tippy-box{width : {{tipMaxWidth}}px; max-width : {{tipMaxWidth}}px; }  ',
								],
							],
						],
						'tiprespo' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'tiptabWidth' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'tiprespo', 'relation' => '==', 'value' => true]],
									'selector' => '@media (max-width:1024px){ {{PLUS_WRAP}}.tpgb-hotspot {{TP_REPEAT_ID}} .tippy-box{width : {{tiptabWidth}}px; max-width : {{tiptabWidth}}px; } } ',
								],
							],
						],
						'tipmobWidth' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'tiprespo', 'relation' => '==', 'value' => true]],
									'selector' => '@media (max-width:767px) { {{PLUS_WRAP}}.tpgb-hotspot {{TP_REPEAT_ID}} .tippy-box{width : {{tipmobWidth}}px; max-width : {{tipmobWidth}}px; } } ',
								],
							],
						],
						'tipArrowColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'condition' => [(object) [ 'key' => 'tipPlacement', 'relation' => '==', 'value' => ['top', 'top-start','top-end'] ],],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-arrow:before{color: {{tipArrowColor}}; border-top-color: {{tipArrowColor}}; }',
								],
								(object) [
									'condition' => [(object) [ 'key' => 'tipPlacement', 'relation' => '==', 'value' => ['right', 'right-start','right-end'] ],],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-arrow:before{color: {{tipArrowColor}}; border-right-color: {{tipArrowColor}}; }',
								],
								(object) [
									'condition' => [(object) [ 'key' => 'tipPlacement', 'relation' => '==', 'value' => ['bottom', 'bottom-start','bottom-end'] ],],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-arrow:before{color: {{tipArrowColor}}; border-bottom-color: {{tipArrowColor}}; }',
								],
								(object) [
									'condition' => [(object) [ 'key' => 'tipPlacement', 'relation' => '==', 'value' => ['left', 'left-start','left-end'] ],],
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box .tippy-arrow:before{color: {{tipArrowColor}}; border-left-color: {{tipArrowColor}}; }',
								],
							],
						],
						'tipPadding' => [
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box{padding: {{tipPadding}};}',
								],
							],
						],
						'tipBorder' => [
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box',
								],
							],
						],
						'tipBorderRadius' => [
							'type' => 'object',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}  .tippy-box{border-radius: {{tipBorderRadius}};}',
								],
							],
						],
						'tipBg' => [
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}  .tippy-box',
								],
							],
						],
						'tipBoxShadow' => [
							'default' => (object) [
								'openShadow' => 0,
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tippy-box',
								],
							],
						],
						'pinUrl' => [
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
						'waveColor' => [
							'default' => '#31313180',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .normal_drop_waves:after,{{PLUS_WRAP}} {{TP_REPEAT_ID}} .image_drop_waves:after,{{PLUS_WRAP}} {{TP_REPEAT_ID}} .hover_drop_waves:after{ background : {{waveColor}}; } ',
								],
							],
						],
					],
				],
				'default' => [
					[
						"_key" => '0',
						'pinType' => 'icon',
						'pinIconType' => 'font_awesome',
						'pinIcon' => 'fas fa-plus',
						'contEffect' => 'normal_drop_waves',
						'pinimgSize' => 'thumbnail',
						'tipPlacement' => 'top',
						'tipArrow' => true,
						'tipTriggers' => 'mouseenter',
						'tipTheme' => 'light',
						'pinUrl' => '#',
						'ariaLabel' => '',
						'pinText' => 'Pin',
						'itemTooltip' => true,
						'piniconColor' => '',
						'pinBgcolor' => '',
						'tooltipColor' => '',
						'tooltipContentType' => 'text',
						'tooltipText' => 'Your content will be here.',
						'tooltipTypo' => ['openTypography' => 0 ],
						'tipBoxShadow' => ['openShadow' => 0 ],
						'DleftAuto' => true,
						'leftPos' => 20,
						'topPos' => 25,
						'DtopAuto' => true,
						'waveColor' => '#31313180',
						'tipArrowColor' => '',
					]
				],
			],
			'iconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-type-icon .pin-icon { font-size: {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'pinWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-icon { width: {{pinWidth}}; height: {{pinWidth}}; line-height: {{pinWidth}} }',
					],
				],
				'scopy' => true,
			],
			'pinRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-icon,{{PLUS_WRAP}} .pin-hotspot-wrapper.normal_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.image_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.hover_drop_waves:after { border-radius : {{pinRadius}} }',
					],
				],
				'scopy' => true,
			],
			'pinBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-icon',
					],
				],
				'scopy' => true,
			],
			'pinHvrBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot:hover .pin-content.pin-type-icon',
					],
				],
				'scopy' => true,
			],
			'imgSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-type-image img.pin-icon { max-width: {{imgSize}}; }',
					],
				],
				'scopy' => true,
			],
			'pinimgWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-image { width: {{pinimgWidth}}; height: {{pinimgWidth}}; line-height: {{pinimgWidth}} }',
					],
				],
				'scopy' => true,
			],
			'pinimgRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-image,{{PLUS_WRAP}} .pin-hotspot-wrapper.normal_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.image_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.hover_drop_waves:after { border-radius : {{pinimgRadius}} }',
					],
				],
				'scopy' => true,
			],
			'pinimgBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-image',
					],
				],
				'scopy' => true,
			],
			'pinimgHvrBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot:hover .pin-content.pin-type-image',
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-text .pin-icon',
					],
				],
				'scopy' => true,
			],
			'txtPadding' => [
				'type' => 'string',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-text { padding: {{txtPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'txtRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-text,{{PLUS_WRAP}} .pin-hotspot-wrapper.normal_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.image_drop_waves:after,{{PLUS_WRAP}} .pin-hotspot-wrapper.hover_drop_waves:after { border-radius : {{txtRadius}} }',
					],
				],
				'scopy' => true,
			],
			'txtBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot .pin-content.pin-type-text',
					],
				],
				'scopy' => true,
			],
			'txtHvrBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .pin-hotspot:hover .pin-content.pin-type-text',
					],
				],
				'scopy' => true,
			],
			'hveOverlay' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			
			'hvrBgoverlay' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'hveOverlay', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-hotspot .tpgb-hotspot-inner.overlay-bg-color:after',
					],
				],
				'scopy' => true,
			],
			'transfoemcss' => [
				'type' => 'string',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .pin-hotspot:hover .pin-content{ transform : {{transfoemcss}} }',
					],
				],
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-hotspot', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_hotspot_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_hotspot' );