<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Compatibility {
	/**
	 * Name of the current page
	 *
	 * @since 4.0
	 * @var string
	 */
	private $page;

	/**
	 * Sets up the class
	 *
	 * @since 4.0
	 */
	public function __construct() {
		$this->page = isset($_GET['page']) ? $_GET['page'] : '';
	}

	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('all_admin_notices', array($this, 'check_compatibilities'));
	}

	/**
	 * Checks for compatibility issues
	 *
	 * @since 4.0
	 */
	public function check_compatibilities() {
		global $wp_rewrite;
		$l10n = MMP::get_instance('MMP\L10n');

		if (!current_user_can('activate_plugins')) {
			return;
		}

		// Notices only shown on plugin pages
		if (strpos($this->page, 'mapsmarkerpro') !== false) {
			// Beta information
			if (MMP::$settings['betaTesting']) {
				$this->show_notice('info', sprintf($l10n->kses__('Beta testing is enabled - updates will be downloaded from the beta release channel. Use these versions at your own risk, as they might be unstable, and please use the <a href="%1$s" target="_blank">helpdesk</a> for feedback.', 'mmp'), 'https://www.mapsmarker.com/helpdesk/'));
			}

			// Permalinks compatibility check
			if ($wp_rewrite->using_mod_rewrite_permalinks()) {
				$response_code = wp_remote_retrieve_response_code(wp_remote_head(
					API::$base_url . API::$slug . '/'
				));
				if ($response_code === 404) {
					$message = sprintf($l10n->kses__('Permalinks for the Maps Marker Pro API endpoints are not working correctly, which means API links (e.g. fullscreen) will not work. To fix this, please navigate to the <a href="%1$s">WordPress integration settings</a> and add the URL to your WordPress folder to the option "Permalinks base URL".', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress'));
					$guesses = array('wordpress', 'wp', 'blog');
					foreach ($guesses as $guess) {
						$response_code = wp_remote_retrieve_response_code(wp_remote_head(
							API::$base_url . $guess . '/' . API::$slug . '/'
						));
						if ($response_code !== 404) {
							$message .= ' ' . sprintf(esc_html__('The correct URL is: %1$s'), '<code>' . API::$base_url . $guess . '/' . '</code>');
							break;
						}
					}
					$this->show_notice('error', $message);
				}
			}
		}

		// Incompatible plugins
		$plugins = array(
			array(
				'name' => 'Better WordPress Minify',
				'file' => 'bwp-minify/bwp-minify.php'
			),
			array(
				'name' => 'WP deferred javaScripts',
				'file' => 'wp-deferred-javascripts/wp-deferred-javascripts.php'
			)
		);
		foreach ($plugins as $plugin) {
			if (is_plugin_active($plugin['file'])) {
				$this->show_notice('error', sprintf(esc_html__('You are using the plugin "%1$s", which is severely outdated and incompatible with Maps Marker Pro - please deactivate it.', 'mmp'), $plugin['name']));
			}
		}
	}

	/**
	 * Outputs an admin notice
	 *
	 * @since 4.0
	 *
	 * @param string $level Notice level (info, warning, error)
	 * @param string $message Message to be displayed
	 */
	private function show_notice($level, $message) {
		?><div class="notice notice-<?= $level ?>"><p><?= $message ?></p></div><?php
	}
}
