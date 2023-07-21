<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class API {
	/**
	 * Absolute URL to the WordPress root directory
	 * Includes the trailing slash
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $base_url;

	/**
	 * Pretty permalinks slug
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $slug;

	/**
	 * Sets up the class
	 *
	 * @since 4.0
	 */
	public function __construct() {
		if (MMP::$settings['permalinkBaseUrl']) {
			self::$base_url = trailingslashit(MMP::$settings['permalinkBaseUrl']);
		} else {
			self::$base_url = trailingslashit(get_site_url());
		}
		self::$slug = MMP::$settings['permalinkSlug'];
	}

	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_filter('query_vars', array($this, 'add_query_vars'));

		add_action('init', array($this, 'add_rewrite_rules'));
		add_action('wp', array($this, 'redirect_endpoints'));
	}

	/**
	 * Adds additional query vars
	 *
	 * @since 4.0
	 *
	 * @param array $vars Current query vars
	 */
	public function add_query_vars($vars) {
		$vars[] = 'mapsmarkerpro';
		$vars[] = 'map';
		$vars[] = 'marker';
		$vars[] = 'format';
		$vars[] = 'address';
		$vars[] = 'place_id';

		return $vars;
	}

	/**
	 * Adds additional rewrite rules
	 *
	 * @since 4.0
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule(
			'^' . self::$slug . '/?$',
			'index.php?mapsmarkerpro',
			'top'
		);
		if (MMP::$settings['apiFullscreen']) {
			add_rewrite_rule(
				'^' . self::$slug . '/fullscreen/(.+)/?',
				'index.php?mapsmarkerpro=fullscreen&map=$matches[1]',
				'top'
			);
		}
		if (MMP::$settings['apiExport']) {
			add_rewrite_rule(
				'^' . self::$slug . '/export/(geojson|kml|georss|atom)/(.+)/?',
				'index.php?mapsmarkerpro=export&map=$matches[2]&format=$matches[1]',
				'top'
			);
		}
		if (MMP::$settings['apiSitemap']) {
			add_rewrite_rule(
				'^' . self::$slug . '/(geo-sitemap)/?',
				'index.php?mapsmarkerpro=$matches[1]',
				'top'
			);
		}

		$flush = get_transient('mapsmarkerpro_flush_rewrite_rules');
		if ($flush !== false) {
			flush_rewrite_rules(true);
			delete_transient('mapsmarkerpro_flush_rewrite_rules');
		}
	}

	/**
	 * Redirects the API endpoints
	 *
	 * @since 4.0
	 *
	 * @param object $wp WP class object
	 */
	public function redirect_endpoints($wp) {
		if (!isset($wp->query_vars['mapsmarkerpro'])) {
			return;
		}

		if (ob_get_length()) {
			ob_end_clean();
		}

		switch ($wp->query_vars['mapsmarkerpro']) {
			case 'fullscreen':
				if (!MMP::$settings['apiFullscreen']) {
					break;
				}
				MMP::get_instance('MMP\Fullscreen')->show();
				die;
			case 'export':
				if (!MMP::$settings['apiExport']) {
					break;
				}
				MMP::get_instance('MMP\FS\Export')->request();
				die;
			case 'geo-sitemap':
				if (!MMP::$settings['apiSitemap']) {
					break;
				}
				MMP::get_instance('MMP\Geo_Sitemap')->show_sitemap();
				die;
			case 'download_gpx':
				MMP::get_instance('MMP\FS\Download')->download_gpx();
				die;
			case 'download_temp':
				MMP::get_instance('MMP\FS\Download')->download_temp();
				die;
			case 'download_debug':
				MMP::get_instance('MMP\FS\Download')->download_debug();
				die;
		}

		wp_redirect(home_url());
		die;
	}

	/**
	 * Builds the link to the API endpoint
	 *
	 * @since 4.0
	 *
	 * @param string $endpoint API endpoint
	 */
	public function link($endpoint) {
		$l10n = MMP::get_instance('MMP\L10n');

		$endpoint = '/' . ltrim($endpoint, '/\\');

		return $l10n->link(self::$base_url . self::$slug . $endpoint);
	}
}
