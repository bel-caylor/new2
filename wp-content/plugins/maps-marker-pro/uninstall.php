<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}

if (is_multisite()) {
	global $wpdb;

	$blogs = $wpdb->get_col($wpdb->prepare(
		"SELECT blog_id
		FROM {$wpdb->blogs}
		WHERE site_id = %d",
		$wpdb->siteid
	));

	foreach ($blogs as $blog_id) {
		switch_to_blog($blog_id);
		mmp_delete_data();
		restore_current_blog();
	}
} else {
	mmp_delete_data();
}

function mmp_delete_data() {
	global $wpdb, $wp_roles;

	foreach ($wp_roles->roles as $role => $values) {
		$wp_roles->remove_cap($role, 'mmp_view_maps');
		$wp_roles->remove_cap($role, 'mmp_add_maps');
		$wp_roles->remove_cap($role, 'mmp_edit_other_maps');
		$wp_roles->remove_cap($role, 'mmp_delete_other_maps');
		$wp_roles->remove_cap($role, 'mmp_view_markers');
		$wp_roles->remove_cap($role, 'mmp_add_markers');
		$wp_roles->remove_cap($role, 'mmp_edit_other_markers');
		$wp_roles->remove_cap($role, 'mmp_delete_other_markers');
		$wp_roles->remove_cap($role, 'mmp_use_tools');
		$wp_roles->remove_cap($role, 'mmp_change_settings');
	}

	delete_option('mapsmarkerpro_version');
	delete_option('mapsmarkerpro_update');
	delete_option('mapsmarkerpro_changelog');
	delete_option('mapsmarkerpro_settings');
	delete_option('mapsmarkerpro_map_defaults');
	delete_option('mapsmarkerpro_marker_defaults');
	delete_option('mapsmarkerpro_notices');
	delete_option('mapsmarkerpro_key');
	delete_option('mapsmarkerpro_key_trial');
	delete_option('mapsmarkerpro_key_local');

	$widgets = get_option('dashboard_widget_options');
	if (isset($widgets['dashboard_maps_marker_pro'])) {
		unset($widgets['dashboard_maps_marker_pro']);
		if (count($widgets)) {
			update_option('dashboard_widget_options', $widgets);
		} else {
			delete_option('dashboard_widget_options');
		}
	}

	delete_transient('mapsmarkerpro_latest');
	delete_transient('mapsmarkerpro_license_error');

	delete_metadata('user', 0, 'mapsmarkerpro_maps_options', null, true);
	delete_metadata('user', 0, 'mapsmarkerpro_markers_options', null, true);
	delete_metadata('user', 0, 'mapsmarkerpro_advanced_map_settings', null, true);

	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_geocoding_cache");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_layers");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_maps");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_markers");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_relationships");

	$upload_dir = wp_get_upload_dir();
	$base_dir = $upload_dir['basedir'] . '/maps-marker-pro/';
	$dirs = array('cache/', 'icons/', 'temp/');
	foreach ($dirs as $dir) {
		$path = $base_dir . $dir;
		if (($handle = opendir($path)) !== false) {
			while (($file = readdir($handle)) !== false) {
				if ($file !== '.' && $file !== '..') {
					unlink($path . $file);
				}
			}
			closedir($handle);
			rmdir($path);
		}
	}
	rmdir($base_dir);

	wp_clear_scheduled_hook('mmp_cleanup');
}
