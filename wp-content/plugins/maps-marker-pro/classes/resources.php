<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Resources {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('wp_enqueue_scripts', array($this, 'register_frontend_resources'));
		add_action('admin_enqueue_scripts', array($this, 'register_backend_resources'));
		add_action('wp_enqueue_media', array($this, 'register_media_resources'));
		add_action('enqueue_block_editor_assets', array($this, 'register_block_resources'));
	}

	/**
	 * Registers the resources used on the front end
	 *
	 * @since 4.0
	 */
	public function register_frontend_resources() {
		$l10n = MMP::get_instance('MMP\L10n');

		wp_register_style('mapsmarkerpro', plugins_url('css/mapsmarkerpro.css', MMP::$path), array(), MMP::$version);
		wp_register_style('mapsmarkerpro-rtl', plugins_url('css/mapsmarkerpro-rtl.css', MMP::$path), array('mapsmarkerpro'), MMP::$version);

		wp_register_script('mmp-googlemaps', $this->get_google_maps_url(), array(), null, true);
		wp_register_script('mapsmarkerpro', plugins_url('js/mapsmarkerpro.js', MMP::$path), array(), MMP::$version, true);
		wp_localize_script('mapsmarkerpro', 'mmpVars', $this->get_plugin_vars());
		wp_localize_script('mapsmarkerpro', 'mmpL10n', $l10n->map_strings());
		if (MMP::$settings['customJs']) {
			wp_add_inline_script('mapsmarkerpro', MMP::$settings['customJs']);
		}
	}

	/**
	 * Registers the resources used on the back end
	 *
	 * @since 4.0
	 */
	public function register_backend_resources() {
		$l10n = MMP::get_instance('MMP\L10n');

		wp_register_style('mmp-admin', plugins_url('css/admin.css', MMP::$path), array(), MMP::$version);
		wp_register_style('mmp-admin-rtl', plugins_url('css/admin-rtl.css', MMP::$path), array(), MMP::$version);
		wp_register_style('mmp-dashboard', plugins_url('css/dashboard.css', MMP::$path), array(), MMP::$version);

		wp_register_script('mmp-googlemaps', $this->get_google_maps_url(), array(), null, true);
		wp_register_script('mmp-admin', plugins_url('js/admin.js', MMP::$path), array('jquery', 'jquery-ui-sortable'), MMP::$version, true);
		wp_localize_script('mmp-admin', 'mmpVars', $this->get_plugin_vars());
		wp_localize_script('mmp-admin', 'mmpL10n', $l10n->map_strings());
		wp_localize_script('mmp-admin', 'mmpAdminL10n', $l10n->admin_strings());
		wp_register_script('mmp-dashboard', plugins_url('js/dashboard.js', MMP::$path), array('jquery'), MMP::$version, true);
	}

	/**
	 * Registers the resources used for the TinyMCE editor
	 *
	 * @since 4.0
	 */
	public function register_media_resources() {
		wp_register_style('mmp-shortcode', plugins_url('css/shortcode.css', MMP::$path), array(), MMP::$version);

		wp_register_script('mmp-shortcode', plugins_url('js/shortcode.js', MMP::$path), array('jquery'), MMP::$version, true);
	}

	/**
	 * Registers the resources used for the Gutenberg block editor
	 *
	 * @since 4.3
	 */
	public function register_block_resources() {
		$l10n = MMP::get_instance('MMP\L10n');

		wp_register_style('mmp-gb-block', plugins_url('css/block.css', MMP::$path), array('wp-edit-blocks'), MMP::$version);

		wp_register_script('mmp-gb-block', plugins_url('js/block.js', MMP::$path), array('wp-blocks', 'wp-element'), MMP::$version, true);
		wp_localize_script('mmp-gb-block', 'mmpGbVars', $this->gb_vars());
		wp_localize_script('mmp-gb-block', 'mmpGbL10n', $l10n->gb_strings());

		register_block_type('mmp/map', array(
			'editor_style'  => 'mmp-gb-block',
			'editor_script' => 'mmp-gb-block'
		));
	}

	/**
	 * Returns the URL for the Google Maps API
	 *
	 * @since 4.0
	 */
	private function get_google_maps_url() {
		$params['key'] = MMP::$settings['googleApiKey'];
		$params['callback'] = "Function.prototype";

		if (MMP::$settings['googleLanguage'] === 'wordpress_setting') {
			$params['language'] = substr(get_locale(), 0, 2);
		} else if (MMP::$settings['googleLanguage'] !== 'browser_setting') {
			$params['language'] = MMP::$settings['googleLanguage'];
		}

		return 'https://maps.googleapis.com/maps/api/js?' . http_build_query($params, '', '&');
	}

	/**
	 * Returns the plugin vars needed for JavaScript
	 *
	 * @since 4.0
	 */
	private function get_plugin_vars() {
		global $wp;
		$l10n = MMP::get_instance('MMP\L10n');

		return array(
			'page'       => trailingslashit(home_url(add_query_arg(array(), $wp->request))),
			'baseUrl'    => API::$base_url,
			'slug'       => API::$slug,
			'apiUrl'     => API::$base_url . API::$slug . '/',
			'adminUrl'   => get_admin_url(),
			'ajaxurl'    => get_admin_url(null, 'admin-ajax.php'),
			'pluginUrl'  => plugins_url('/', MMP::$path),
			'iconsUrl'   => MMP::$icons_url,
			'language'   => ($l10n->check_ml()) ? ICL_LANGUAGE_CODE : '',
			'dateFormat' => get_option('date_format'),
			'timeFormat' => get_option('time_format'),
			'isAdmin'    => current_user_can('activate_plugins')
		);
	}

	/**
	 * Returns the Gutenberg vars needed for JavaScript
	 *
	 * @since 4.3
	 */
	private function gb_vars() {
		$db = MMP::get_instance('MMP\DB');

		$maps = $db->get_all_maps(false, array(
			'orderby'   => 'id',
			'sortorder' => 'desc'
		));
		$data = array();
		foreach ($maps as $map) {
			$data[] = array(
				'id'   => $map->id,
				'name' => "[ID {$map->id}] " . (($map->name) ? esc_html($map->name) : esc_html__('(no name)', 'mmp'))
			);
		}

		return array(
			'iconUrl'   => plugins_url('images/mmp-icon.svg', MMP::$path),
			'shortcode' => MMP::$settings['shortcode'],
			'maps'      => $data
		);
	}
}
