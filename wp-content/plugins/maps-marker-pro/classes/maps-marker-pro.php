<?php
namespace MMP;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Main plugin class
 *
 * @since 4.0
 */
class Maps_Marker_Pro {
	/**
	 * Plugin version
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $version;

	/**
	 * Absolute path to the main plugin file
	 *
	 * @since 4.3
	 * @var string
	 */
	public static $path;

	/**
	 * Absolute path to the root plugin directory
	 * Includes the trailing slash
	 *
	 * @since 4.3
	 * @var string
	 */
	public static $dir;

	/**
	 * Path to the main plugin file
	 * Relative to the WordPress plugins directory
	 * Does not include the leading and trailing slashes
	 *
	 * @since 4.3
	 * @var string
	 */
	public static $file;

	/**
	 * Plugin capabilities
	 *
	 * @since 4.0
	 * @var array
	 */
	public static $capabilities;

	/**
	 * Plugin settings
	 *
	 * @since 4.0
	 * @var array
	 */
	public static $settings;

	/**
	 * Absolute path to the plugin cache directory
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $cache_dir;

	/**
	 * Absolute path to the plugin temp directory
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $temp_dir;

	/**
	 * Absolute path to the plugin icons directory
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $icons_dir;

	/**
	 * Absolute URL to the plugin icons directory
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $icons_url;

	/**
	 * List of instantiated classes
	 *
	 * @since 4.0
	 * @see get_instance()
	 * @var array
	 */
	public static $instances;

	/**
	 * Returns a class object, instantiating it if necessary
	 *
	 * @since 4.0
	 * @param string $class Namespace and name of the class
	 * @return object Class object
	 */
	public static function get_instance($class) {
		if (!isset(self::$instances[$class])) {
			self::$instances[$class] = new $class();
		}

		return self::$instances[$class];
	}

	/**
	 * Sets up the class
	 *
	 * @since 4.0
	 * @param string $path Absolute path to the main plugin file
	 */
	public function __construct($path) {
		self::$version = '4.25';

		self::$path = $path;
		self::$dir  = plugin_dir_path($path);
		self::$file = plugin_basename($path);

		self::$capabilities = array(
			'mmp_view_maps',
			'mmp_add_maps',
			'mmp_edit_other_maps',
			'mmp_delete_other_maps',
			'mmp_view_markers',
			'mmp_add_markers',
			'mmp_edit_other_markers',
			'mmp_delete_other_markers',
			'mmp_use_tools',
			'mmp_change_settings'
		);

		$settings = self::get_instance('MMP\Settings');
		self::$settings = $settings->get_settings();

		$upload_dir = wp_get_upload_dir();
		self::$cache_dir = $upload_dir['basedir'] . '/maps-marker-pro/cache/';
		self::$temp_dir  = $upload_dir['basedir'] . '/maps-marker-pro/temp/';
		self::$icons_dir = $upload_dir['basedir'] . '/maps-marker-pro/icons/';
		self::$icons_url = $upload_dir['baseurl'] . '/maps-marker-pro/icons/';
	}

	/**
	 * Initializes the class
	 *
	 * @since 4.0
	 */
	public function init() {
		add_filter('widget_text', 'do_shortcode'); // Parse shortcode in widgets
		add_filter('term_description', 'do_shortcode'); // Parse shortcodes in term descriptions
		add_filter('upload_mimes', array($this, 'filter_upload_mimes'));
		add_filter('post_mime_types', array($this, 'filter_post_mime_types'));
		add_filter('wp_check_filetype_and_ext', array($this, 'filter_check_filetype_and_ext'), 99, 5);
		add_filter('plugin_action_links_' . self::$file, array($this, 'filter_plugin_action_links'));
		add_filter('network_admin_plugin_action_links_' . self::$file, array($this, 'filter_plugin_action_links'));
		add_filter('users_have_additional_content', array($this, 'filter_users_have_additional_content'), 10, 2);
		add_filter('cmplz_integrations', array($this, 'complianz_integration'));
		add_filter('cmplz_integration_path', array($this, 'complianz_integration_path'), 10, 2);

		add_action('delete_user', array($this, 'deleted_user'), 10, 2);

		self::get_instance('MMP\Debug')->init();
		self::get_instance('MMP\License')->init();
		self::get_instance('MMP\FS\Upload')->init();
		self::get_instance('MMP\Resources')->init();
		self::get_instance('MMP\API')->init();
		self::get_instance('MMP\Update')->init();
		self::get_instance('MMP\L10n')->init();
		self::get_instance('MMP\Map')->init();
		self::get_instance('MMP\Shortcodes')->init();
		self::get_instance('MMP\Geocoding')->init();
		self::get_instance('MMP\Menus')->init();
		self::get_instance('MMP\Menu\License')->init();
		self::get_instance('MMP\Menu\Maps')->init();
		self::get_instance('MMP\Menu\Map')->init();
		self::get_instance('MMP\Menu\Markers')->init();
		self::get_instance('MMP\Menu\Marker')->init();
		self::get_instance('MMP\Menu\Tools')->init();
		self::get_instance('MMP\Menu\Settings')->init();
		self::get_instance('MMP\Menu\Support')->init();
		self::get_instance('MMP\Migration')->init();
		self::get_instance('MMP\Geo_Sitemap')->init();
		self::get_instance('MMP\Setup')->init();
		self::get_instance('MMP\TinyMCE')->init();
		self::get_instance('MMP\Notice')->init();
		self::get_instance('MMP\Compatibility')->init();
		self::get_instance('MMP\Dashboard')->init();
		self::get_instance('MMP\Widget\Shortcode')->init();

		self::init_puc();
	}

	/**
	 * Initializes the update checker
	 *
	 * @since 4.3
	 */
	public function init_puc() {
		require_once self::$dir . 'dist/plugin-update-checker/plugin-update-checker.php';

		$endpoint = 'https://www.mapsmarker.com/updates_pro/?action=get_metadata&slug=maps-marker-pro';
		if (self::$settings['betaTesting']) {
			$endpoint .= '-beta';
		}

		PucFactory::buildUpdateChecker(
			$endpoint,
			self::$path,
			'maps-marker-pro',
			'24',
			'mapsmarkerpro_update'
		);
	}

	/**
	 * Modifies the allowed mime types for uploads
	 *
	 * @since 4.3
	 * @param array $mimes Current allowed mime types
	 * @return array Modified allowed mime types
	 */
	public function filter_upload_mimes($mimes) {
		$mimes['gpx'] = 'application/gpx+xml';

		return $mimes;
	}

	/**
	 * Modifies the mime type filters for the media library
	 *
	 * @since 4.3
	 * @param array $mimes Current post mime types
	 * @return array Modified post mime types
	 */
	public function filter_post_mime_types($mimes) {
		$mimes['application/gpx+xml'] = array(
			__('GPX tracks', 'mmp'),
			__('Manage GPX tracks', 'mmp'),
			_n_noop('GPX track <span class="count">(%s)</span>', 'GPX tracks <span class="count">(%s)</span>', 'mmp')
		);

		return $mimes;
	}

	/**
	 * Modifies the filetype and extension check
	 *
	 * @since 4.3
	 * @param array $check List containing the current file data
	 * @param string $file Absolute path to the file
	 * @param string $filename Name of the file
	 * @param array $mimes List of extensions and mime types to check against
	 * @param string|false $real_mime Actual mime type or false if it cannot be determined
	 * @return array List containing the modified file data
	 */
	public function filter_check_filetype_and_ext($check, $file, $filename, $mimes, $real_mime = false) {
		global $wp_version;

		if (!version_compare($wp_version, '5.0.1', '>=')) {
			return $check;
		}

		$gpx_mimes = array(
			'application/gpx+xml',
			'text/xml'
		);

		$info = pathinfo($filename);
		$ext = strtolower($info['extension']);
		if ($ext === 'gpx' && ($real_mime === false || in_array($real_mime, $gpx_mimes))) {
			$check['ext']  = 'gpx';
			$check['type'] = 'application/gpx+xml';
		}

		return $check;
	}

	/**
	 * Modifies the action links in the plugins list
	 *
	 * @since 4.3
	 * @param array $links Current plugin action links
	 * @return array Modified plugin action links
	 */
	public function filter_plugin_action_links($links) {
		array_unshift(
			$links,
			'<a href="' . get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings') . '">' . esc_html__('Settings', 'mmp') . '</a>',
			'<a href="' . get_admin_url(null, 'admin.php?page=mapsmarkerpro_license') . '">' . esc_html__('License', 'mmp') . '</a>'
		);

		return $links;
	}

	/**
	 * Modifies the additional content status
	 *
	 * @since 4.13
	 * @param bool $status Current additional content status
	 * @param array $user_ids List of IDs for users being deleted
	 * @return boolean Modified additional content status
	 */
	public function filter_users_have_additional_content($status, $user_ids) {
		global $wpdb;

		if ($status) {
			return $status;
		}

		$user_ids = implode(',', $user_ids);

		$has_maps = $wpdb->get_var(
			"SELECT id
			FROM {$wpdb->prefix}mmp_maps
			WHERE created_by_id IN ($user_ids)
			LIMIT 1"
		);
		$has_markers = $wpdb->get_var(
			"SELECT id
			FROM {$wpdb->prefix}mmp_markers
			WHERE created_by_id IN ($user_ids)
			LIMIT 1"
		);

		return $has_maps || $has_markers;
	}

	/**
	 * Removes or updates content after a user is deleted
	 *
	 * @since 4.13
	 * @param int $user_id ID of the deleted user
	 * @param int|null $reassign_id ID of the user to reassign content to or null if no reassignment
	 */
	public function deleted_user($user_id, $reassign_id) {
		global $wpdb;
		$db = self::get_instance('MMP\DB');

		if ($reassign_id) {
			$wpdb->update(
				"{$wpdb->prefix}mmp_maps",
				array('created_by_id' => $reassign_id),
				array('created_by_id' => $user_id),
				array('%d'),
				array('%d')
			);
			$wpdb->update(
				"{$wpdb->prefix}mmp_markers",
				array('created_by_id' => $reassign_id),
				array('created_by_id' => $user_id),
				array('%d'),
				array('%d')
			);
		} else {
			$wpdb->delete(
				"{$wpdb->prefix}mmp_maps",
				array('created_by_id' => $user_id),
				array('%d')
			);
			$wpdb->delete(
				"{$wpdb->prefix}mmp_markers",
				array('created_by_id' => $user_id),
				array('%d')
			);

			$db->delete_orphaned_rels();
		}

		$wpdb->query(
			"UPDATE {$wpdb->prefix}mmp_maps
			SET updated_by_id = created_by_id
			WHERE updated_by_id = $user_id"
		);
		$wpdb->query(
			"UPDATE {$wpdb->prefix}mmp_markers
			SET updated_by_id = created_by_id
			WHERE updated_by_id = $user_id"
		);
	}

	/**
	 * Enables Complianz integration
	 *
	 * @since 4.18
	 * @param array $plugins List of plugins
	 */
	public function complianz_integration($plugins) {
		$plugins['maps-marker-pro'] = array(
			'constant_or_function' => 'MMP\Maps_Marker_Pro',
			'label'                => 'Maps Marker Pro'
		);

		return $plugins;
	}

	/**
	 * Sets the Complianz integration path
	 *
	 * @since 4.18
	 * @param string $path Path to the integration file
	 * @param array $plugins Current plugin
	 */
	public function complianz_integration_path($path, $plugin) {
		if ($plugin === 'maps-marker-pro'){
			$path = self::$dir . 'dist/complianz/integration.php';
		}

		return $path;
	}
}
