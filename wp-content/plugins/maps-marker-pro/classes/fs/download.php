<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class Download {
	/**
	 * Downloads the GPX file attached to a map
	 *
	 * @since 4.0
	 */
	public function download_gpx() {
		if (!isset($_GET['url'])) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('URL missing', 'mmp'));
		}
		$url = esc_url_raw($_GET['url']);
		if (substr(strtolower($url), 0, 4) !== 'http') {
			$url = get_site_url(null, $url);
		}
		if (wp_http_validate_url($url) === false) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Invalid URL', 'mmp'));
		}
		$id = attachment_url_to_postid($url);
		if ($id === 0) {
			if (MMP::$settings['redirectExternalGpx'] === true) {
				wp_redirect($url);
				die;
			}
			$file = wp_safe_remote_get($url);
			if (wp_remote_retrieve_response_code($file) !== 200) {
				die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Could not retrieve file', 'mmp'));
			}
			$content = wp_remote_retrieve_body($file);
			$filename = basename($url);
			$filesize = wp_remote_retrieve_header($file, 'content-length');
		} else {
			$file = get_attached_file($id);
			if (!file_exists($file)) {
				die(esc_html__('Error', 'mmp') . ': ' . esc_html__('File not found', 'mmp'));
			}
			$content = file_get_contents($file);
			if ($content === false) {
				die(esc_html__('File could not be read', 'mmp'));
			}
			$filename = basename($file);
			$filesize = strlen($content);
		}
		if (substr(strtolower($filename), -4) !== '.gpx') {
			$filename .= '.gpx';
		}

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: application/gpx+xml');
		header('Content-Length: ' . $filesize);

		echo $content;
	}

	/**
	 * Downloads a file stored in the plugin's temp directory
	 *
	 * @since 4.0
	 */
	public function download_temp() {
		if (!isset($_GET['nonce']) || wp_verify_nonce($_GET['nonce'], 'mmp-download-temp') === false || !current_user_can('activate_plugins')) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Security check failed', 'mmp'));
		}
		if (!isset($_GET['filename'])) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Filename missing', 'mmp'));
		}
		$filename = basename($_GET['filename']);
		if (!$filename || validate_file($filename) !== 0) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Invalid filename', 'mmp'));
		}
		$file = MMP::$temp_dir . $filename;
		if (!file_exists($file)) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('File not found', 'mmp'));
		}
		if (!is_readable($file)) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('File could not be read', 'mmp'));
		}

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . filesize($file));

		readfile($file);
	}

	/**
	 * Downloads debug information
	 *
	 * @since 4.13
	 */
	public function download_debug() {
		$debug = MMP::get_instance('MMP\Debug');

		if (!isset($_GET['nonce']) || wp_verify_nonce($_GET['nonce'], 'mmp-download-debug') === false || !current_user_can('activate_plugins')) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Security check failed', 'mmp'));
		}
		$debug_info = $debug->get_info();
		$filename = 'debug-' . gmdate('Y-m-d-his') . '.log';

		header('Cache-Control: no-store, no-cache');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: text/plain');

		var_export($debug_info);
	}
}
