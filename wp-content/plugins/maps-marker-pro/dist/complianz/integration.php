<?php
if (!defined('ABSPATH')) {
	die;
}

add_filter('cmplz_known_script_tags', function ($tags) {
	$tag = array(
		'name' => 'maps-marker-pro',
		'category' => 'marketing',
		'urls' => array(
			'MapsMarkerPro.init'
		),
		'enable_placeholder' => '1',
		'placeholder' => 'openstreetmaps',
		'placeholder_class' => 'maps-marker-pro',
		'enable_dependency' => '1',
		'dependency' => array()
	);

	if (wp_script_is('mmp-googlemaps', 'enqueued')) {
		$tag['urls'][] = 'maps.googleapis.com/maps/api/js';
		$tag['dependency']['MapsMarkerPro.init'] = 'maps.googleapis.com/maps/api/js';
	}

	$tags[] = $tag;

	return $tags;
});

add_action('wp_enqueue_scripts', function () {
	wp_add_inline_style('mapsmarkerpro', '.maps-marker-pro.cmplz-blocked-content-container{max-height:500px}');
});
