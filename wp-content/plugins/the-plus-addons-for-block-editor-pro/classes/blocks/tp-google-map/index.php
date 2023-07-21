<?php
/* Block : Google Map
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_google_map_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("map");
	$contentTgl = (!empty($attributes['contentTgl'])) ? $attributes['contentTgl'] : false;
	$title = (!empty($attributes['title'])) ? $attributes['title'] : '';
	$description = (!empty($attributes['description'])) ? $attributes['description'] : '';
	$locationPoint = (!empty($attributes['locationPoint'])) ? $attributes['locationPoint'] : '';
	
	$Zoom = (!empty($attributes['Zoom'])) ? $attributes['Zoom'] : 10;
	$scrollWheel = (!empty($attributes['scrollWheel'])) ? $attributes['scrollWheel'] : false;
	$panCtrl = (!empty($attributes['panCtrl'])) ? $attributes['panCtrl'] : false;
	$Draggable = (!empty($attributes['Draggable'])) ? $attributes['Draggable'] : false;
	$zoomCtrl = (!empty($attributes['zoomCtrl'])) ? $attributes['zoomCtrl'] : false;
	$mapTypeCtrl = (!empty($attributes['mapTypeCtrl'])) ? $attributes['mapTypeCtrl'] : false;
	$scaleCtrl = (!empty($attributes['scaleCtrl'])) ? $attributes['scaleCtrl'] : false;
	$fullScreenCtrl = (!empty($attributes['fullScreenCtrl'])) ? $attributes['fullScreenCtrl'] : false;
	$streetViewCtrl = (!empty($attributes['streetViewCtrl'])) ? $attributes['streetViewCtrl'] : false;
	
	$customStyleTgl = (!empty($attributes['customStyleTgl'])) ? $attributes['customStyleTgl'] : false;
	$customStyle = (!empty($attributes['customStyle'])) ? $attributes['customStyle'] : 'style-1';
	
	$modifyColors = (!empty($attributes['modifyColors'])) ? $attributes['modifyColors'] : false;
	$hue = (!empty($attributes['hue'])) ? $attributes['hue'] : '';
	$saturation = (!empty($attributes['saturation'])) ? $attributes['saturation'] : '';
	$lightness = (!empty($attributes['lightness'])) ? $attributes['lightness'] : '';
	
	$gmapType = (!empty($attributes['gmapType'])) ? $attributes['gmapType'] : 'roadmap';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$json_map  = [];
	$json_map['places']  = [];
	$json_map['options'] = [
		"zoom" => intval($Zoom),
		"scrollwheel"		=> $scrollWheel,
		"draggable"		=> $Draggable,
		"panControl"		=> $panCtrl,
		"zoomControl"		=> $zoomCtrl,
		"scaleControl"		=> $scaleCtrl,
		"mapTypeControl"	=> $mapTypeCtrl,
		"fullscreenControl"	=> $fullScreenCtrl,
		"streetViewControl"	=> $streetViewCtrl,
		"mapTypeId"		=> $gmapType
	];
	
	if(!empty($locationPoint)){
		foreach($locationPoint as $index => $item ) {
			$longitude = (!empty($item['longitude'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['longitude']) : '';
			$latitude = (!empty($item['latitude'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['latitude']) : '';
			$address = (!empty($item['address'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['address']) : '';
			$pin_icon='';
			if(!empty($item['pinIcon']["id"]) && !empty($item['pinIcon']["url"])){
				$pinIconSize=(!empty($item['pinIconSize'])) ? $item['pinIconSize'] : 'full';
				$img = wp_get_attachment_image_src($item['pinIcon']['id'],$pinIconSize);
				$pin_icon = (!empty($img)) ? esc_url($img[0]) : esc_url($item['pinIcon']["url"]);
			}else if(!empty($item['pinIcon']["url"])){
				$pin_icon = (isset($item['pinIcon']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['pinIcon']) : ( !empty($item['pinIcon']['url']) ? $item['pinIcon']['url'] : '' );
			}
			if(!empty($longitude) || !empty($latitude)){
				$json_map['places'][] = array(
					"address"   => wp_kses_post($address),
					"latitude"  => (float) $latitude,
					"longitude" => (float) $longitude,
					"pin_icon" => esc_url($pin_icon)
				);
			}
		}
	}
	
	$maps_style='';
	if( !empty($customStyleTgl) ) {
		$maps_style= 'data-map-style="'.esc_attr($customStyle).'"';
	}
	$json_map = str_replace("'", "&apos;", json_encode( $json_map ) );
	
	$output = '';
    $output .= '<div class="tpgb-google-map tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'" >';
		
		$output .= '<div id="gmap-'.esc_attr($block_id).'" class="tpgb-adv-map" data-id="gmap-'.esc_attr($block_id).'" data-map-settings="'.htmlentities($json_map, ENT_QUOTES, "UTF-8").'" '.$maps_style.'></div>';
		
		if(!empty($contentTgl)){
			$output .= '<div class="tpgb-overlay-map-content">';
				$output .= '<div class="gmap-title">'.wp_kses_post($title).'</div>';
				$output .= '<div class="gmap-desc">'.wp_kses_post($description).'</div>';
				$output .= '<div class="overlay-list-item">';
					$output .= '<input id="toggle_'.esc_attr($block_id).'" type="checkbox" class="tpgb-overlay-gmap tpgb-overlay-gmap-tgl tpgb-block-'.esc_attr($block_id).'-checked"/>';
					$output .= '<label for="toggle_'.esc_attr($block_id).'" class="tpgb-overlay-gmap-btn tpgb-block-'.esc_attr($block_id).'-label"></label>';
				$output .= '</div>';
			$output .= '</div>';
		}
		
    $output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}
/**
 * Render for the server-side
 */
function tpgb_google_map() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'locationPoint' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'latitude' => [
						'type' => 'string',
						'default' => ''
					],
					'longitude' => [
						'type' => 'string',
						'default' => ''
					],
					'address' => [
						'type' => 'string',
						'default' => ''
					],
					'pinIcon' => [
						'type' => 'object',
						'default' => [
							'url' => '',
						],
					],
					'pinIconSize' => [
						'type' => 'string',
						'default' => 'thumbnail',	
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'latitude' => '40.6884135',
					'longitude' => '-74.3606169',
					'address' => '',
					'pinIcon' => ['url' => ''],
				],
			],
		],
		'mapHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => 300,
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-map{ min-height: {{mapHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'Zoom' => [
			'type' => 'string',
			'default' => 8.5,
			'scopy' => true,
		],
		
		
		'scrollWheel' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'panCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'Draggable' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'zoomCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'mapTypeCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'scaleCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'fullScreenCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'streetViewCtrl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		
		'gmapType' => [
			'type' => 'string',
			'default' => 'roadmap',
			'scopy' => true,
		],
		'customStyleTgl' => [
			'type' => 'boolean',
			'default' => false,
			'scopy' => true,
		],
		'customStyle' => [
			'type' => 'string',
			'default' => 'style-1',
			'scopy' => true,
		],
		
		'contentTgl' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'title' => [
			'type' => 'string',
			'default' => 'Location Here',	
		],
		'description' => [
			'type' => 'string',
			'default' => 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',	
		],
		'contentBG' => [
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
					'condition' => [(object) ['key' => 'contentTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-overlay-map-content',
				],
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
					'condition' => [(object) ['key' => 'title', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .gmap-title',
				],
			],
			'scopy' => true,
		],
		'titleColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .gmap-title{ color: {{titleColor}}; }',
				],
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
					'condition' => [(object) ['key' => 'description', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .gmap-desc',
				],
			],
			'scopy' => true,
		],
		'descColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .gmap-desc{ color: {{descColor}}; }',
				],
			],
			'scopy' => true,
		],
		'toggleColor' => [
			'type' => 'string',
			'default' => 'rgba(0, 0, 0, 0.4)',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}-checked:not(checked) + {{PLUS_WRAP}}-label:after,{{PLUS_WRAP}}-checked + {{PLUS_WRAP}}-label:before{ border-color: {{toggleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'tglActiveClr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'contentTgl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}-checked:checked + {{PLUS_WRAP}}-label:after{ border-color: {{tglActiveClr}}; }',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-google-map', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_google_map_render_callback'
    ) );
}
add_action( 'init', 'tpgb_google_map' );