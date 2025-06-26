<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Setup {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		register_activation_hook(MMP::$path, array($this, 'activate'));
		register_deactivation_hook(MMP::$path, array($this, 'deactivate'));

		add_action('wpmu_new_blog', array($this, 'add_blog'));
		add_action('delete_blog', array($this, 'delete_blog'));
		add_action('mmp_cleanup', array($this, 'mmp_cleanup'));
	}

	/**
	 * Executes when the plugin is activated
	 *
	 * @since 4.0
	 *
	 * @param bool $networkwide Whether the plugin was set to network active on a multisite installation
	 */
	public function activate($networkwide) {
		global $wpdb, $wp_version;
		$php_version = PHP_VERSION;

		$wp_min = 4.5;
		$php_min = 5.6;

		if (!version_compare($wp_version, $wp_min, '>=')) {
			die("[Maps Marker Pro - activation failed!]: WordPress Version $wp_min or higher is needed for this plugin to run properly (you are using version $wp_version) - please upgrade your WordPress installation!");
		}
		if (!version_compare($php_version, $php_min, '>=')) {
			die("[Maps Marker Pro - activation failed]: PHP $php_min or higher is needed for this plugin to run properly (you are using PHP $php_version) - please contact your hoster to upgrade your PHP installation!");
		}

		if (is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php') || class_exists('Leafletmapsmarker')) {
			$version = get_option('leafletmapsmarker_version', '');
			die("[Maps Marker Pro - activation failed]: Please deactivate Leaflet Maps Marker $version first.");
		}
		if (is_plugin_active('leaflet-maps-marker-pro/leaflet-maps-marker.php') || class_exists('LeafletmapsmarkerPro')) {
			$version = get_option('leafletmapsmarker_version_pro', '');
			die("[Maps Marker Pro - activation failed]: Please deactivate Maps Marker Pro $version first.");
		}

		if (is_multisite() && $networkwide) {
			$blogs = $wpdb->get_col($wpdb->prepare(
				"SELECT blog_id
				FROM {$wpdb->blogs}
				WHERE site_id = %d",
				$wpdb->siteid
			));
			foreach ($blogs as $blog_id) {
				switch_to_blog($blog_id);
				$this->setup();
				restore_current_blog();
			}
		} else {
			$this->setup();
		}
	}

	/**
	 * Executes when the plugin is deactivated
	 *
	 * @since 4.0
	 */
	public function deactivate() {
		wp_clear_scheduled_hook('mmp_cleanup');
	}

	/**
	 * Executes when a new blog is created on a multisite installation
	 *
	 * @since 4.0
	 *
	 * @param $blog_id The ID of the newly created blog
	 */
	public function add_blog($blog_id) {
		if (is_plugin_active_for_network(MMP::$file)) {
			switch_to_blog($blog_id);
			$this->setup();
			restore_current_blog();
		}
	}

	/**
	 * Executes when a blog is deleted on a multisite installation
	 *
	 * @since 4.0
	 *
	 * @param $blog_id ID of the deleted blog
	 */
	public function delete_blog($blog_id) {
		$db = MMP::get_instance('MMP\DB');

		switch_to_blog($blog_id);
		$db->delete_tables();
		restore_current_blog();
	}

	/**
	 * Cleans up the plugin
	 *
	 * @since 4.7
	 */
	public function mmp_cleanup() {
		$db = MMP::get_instance('MMP\DB');

		$db->clear_geocoding_cache(30);
		$db->delete_orphaned_rels();

		$handle = opendir(MMP::$temp_dir);
		if ($handle === false) {
			return;
		}
		while (($file = readdir($handle)) !== false) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			if (time() - filemtime(MMP::$temp_dir . $file) >= 604800) {
				unlink(MMP::$temp_dir . $file);
			}
		}
		closedir($handle);
	}

	/**
	 * Initializes the plugin
	 *
	 * @since 4.0
	 */
	public function setup() {
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$notice = MMP::get_instance('MMP\Notice');
		$api = MMP::get_instance('MMP\API');

		$db->create_tables();

		// Give administrators all capabilities to prevent lockouts
		$admin = get_role('administrator');
		foreach (MMP::$capabilities as $cap) {
			$admin->add_cap($cap, true);
		}

		add_option('mapsmarkerpro_version', MMP::$version);
		add_option('mapsmarkerpro_update', null);
		add_option('mapsmarkerpro_changelog', null);
		add_option('mapsmarkerpro_settings', $mmp_settings->get_default_settings());
		add_option('mapsmarkerpro_map_defaults', $mmp_settings->get_default_map_settings());
		add_option('mapsmarkerpro_marker_defaults', $mmp_settings->get_default_marker_settings());
		add_option('mapsmarkerpro_notices', array());
		add_option('mapsmarkerpro_key', null);
		add_option('mapsmarkerpro_key_trial', null);
		add_option('mapsmarkerpro_key_local', null);

		// Copy keys if a version prior to 4.0 exists
		$key = get_option('leafletmapsmarkerpro_license_key');
		if ($key && !get_option('mapsmarkerpro_key')) {
			update_option('mapsmarkerpro_key', $key);
		}
		$key_trial = get_option('leafletmapsmarkerpro_license_key_trial');
		if ($key_trial && !get_option('mapsmarkerpro_key_trial')) {
			update_option('mapsmarkerpro_key_trial', $key_trial);
		}

		// Show notice to finish installation if no key is present
		if (!get_option('mapsmarkerpro_key') && !get_option('mapsmarkerpro_key_trial')) {
			$notice->add_admin_notice('finish_install');
		}

		// Show data migration notice if a version prior to 4.0 exists
		if (!$db->count_maps() && !$db->count_markers()) {
			$old_pro = get_option('leafletmapsmarker_version_pro');
			$old_free = get_option('leafletmapsmarker_version');
			if (version_compare($old_pro, '3.1.1', '>=')) {
				$notice->remove_admin_notice('migration_update_pro');
				$notice->remove_admin_notice('migration_update_free');
				$notice->remove_admin_notice('migration_ok_free');
				$notice->add_admin_notice('migration_ok_pro');
			} else if ($old_pro !== false) {
				$notice->remove_admin_notice('migration_ok_pro');
				$notice->remove_admin_notice('migration_ok_free');
				$notice->remove_admin_notice('migration_update_free');
				$notice->add_admin_notice('migration_update_pro');
			} else if (version_compare($old_free, '3.12.7', '>=')) {
				$notice->remove_admin_notice('migration_update_pro');
				$notice->remove_admin_notice('migration_update_free');
				$notice->remove_admin_notice('migration_ok_pro');
				$notice->add_admin_notice('migration_ok_free');
			} else if ($old_free !== false) {
				$notice->remove_admin_notice('migration_ok_pro');
				$notice->remove_admin_notice('migration_ok_free');
				$notice->remove_admin_notice('migration_update_pro');
				$notice->add_admin_notice('migration_update_free');
			}
		}

		set_transient('mapsmarkerpro_flush_rewrite_rules', true);

		// WP_Filesystem is only available in the admin area and after the wp_loaded hook
		if (function_exists('WP_Filesystem')) {
			WP_Filesystem();
			if (!is_dir(MMP::$cache_dir)) {
				wp_mkdir_p(MMP::$cache_dir);
			}
			if (!is_dir(MMP::$temp_dir)) {
				wp_mkdir_p(MMP::$temp_dir);
			}
			if (!is_dir(MMP::$icons_dir)) {
				wp_mkdir_p(MMP::$icons_dir);
				unzip_file(MMP::$dir . 'images/mapicons/mapicons.zip', MMP::$icons_dir);
			}
		}

		if (!wp_next_scheduled('mmp_cleanup')) {
			wp_schedule_event(time(), 'weekly', 'mmp_cleanup');
		}
	}
}
