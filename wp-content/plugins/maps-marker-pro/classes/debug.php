<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Debug {
	/**
	 * Registers the hooks
	 *
	 * @since 4.20
	 */
	public function init() {
		add_action('wp_ajax_nopriv_mmp_ajax_test', array($this, 'ajax_test'));
	}

	/**
	 * Performs an AJAX test
	 *
	 * @since 4.20
	 */
	public function ajax_test() {
		wp_send_json_success();
	}

	/**
	 * Returns debug information
	 *
	 * @since 4.13
	 */
	public function get_info() {
		global $wp_version, $wp_rewrite;

		return array(
			'mmp_version'   => MMP::$version,
			'wp_version'    => $wp_version,
			'php_version'   => PHP_VERSION,
			'wp_rewrite'    => $wp_rewrite->using_mod_rewrite_permalinks(),
			'ajax_response' => wp_remote_post(get_admin_url(null, 'admin-ajax.php'), array('body' => array('action' => 'mmp_ajax_test'))),
			'api_response'  => wp_remote_head(API::$base_url . API::$slug . '/'),
			'LC_COLLATE'    => setlocale(LC_COLLATE, 0),
			'LC_CTYPE'      => setlocale(LC_CTYPE, 0),
			'LC_MONETARY'   => setlocale(LC_MONETARY, 0),
			'LC_NUMERIC'    => setlocale(LC_NUMERIC, 0),
			'LC_TIME'       => setlocale(LC_TIME, 0),
			'LC_MESSAGES'   => (defined('LC_MESSAGES')) ? setlocale(LC_MESSAGES, 0) : false
		);
	}
}
