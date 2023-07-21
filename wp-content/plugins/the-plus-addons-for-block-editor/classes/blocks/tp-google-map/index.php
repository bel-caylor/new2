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
		"mapTypeId"		=> esc_attr($gmapType)
	];
	
	if(!empty($locationPoint)){
		foreach($locationPoint as $index => $item ) {
			$longitude = (!empty($item['longitude'])) ? $item['longitude'] : '';
			$latitude = (!empty($item['latitude'])) ? $item['latitude'] : '';
			$address = (!empty($item['address'])) ? $item['address'] : '';
			$pin_icon='';
			if(!empty($item['pinIcon']["id"]) && !empty($item['pinIcon']["url"])){
				$pinIconSize=(!empty($item['pinIconSize'])) ? $item['pinIconSize'] : 'full';
				$img = wp_get_attachment_image_src($item['pinIcon']['id'],$pinIconSize);
				$pin_icon = (!empty($img)) ? $img[0] : $item['pinIcon']["url"];
			}else if(!empty($item['pinIcon']["url"])){
				$pin_icon=$item['pinIcon']["url"];
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
	
	$json_map = str_replace("'", "&apos;", json_encode( $json_map ) );
	
	$output = '';
    $output .= '<div class="tpgb-google-map tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'">';
		
		$output .= '<div id="gmap-'.esc_attr($block_id).'" class="tpgb-adv-map" data-id="gmap-'.esc_attr($block_id).'" data-map-settings="'.htmlentities($json_map, ENT_QUOTES, "UTF-8").'" ></div>';
		
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