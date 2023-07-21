<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class Upload {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('wp_ajax_mmp_icon_upload', array($this, 'icon_upload'));
	}

	/**
	 * AJAX request for uploading a marker icon to the icons directory
	 *
	 * @since 4.0
	 */
	public function icon_upload() {
		check_ajax_referer('mmp-icon-upload', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		if (!isset($_FILES['upload'])) {
			wp_send_json_error(esc_html__('File missing', 'mmp'));
		}

		add_filter('upload_dir', function($upload) {
			$upload['subdir'] = '';
			$upload['path'] = untrailingslashit(MMP::$icons_dir);
			$upload['url'] = untrailingslashit(MMP::$icons_url);

			return $upload;
		});

		$upload = wp_handle_upload($_FILES['upload'], array(
			'test_form' => false,
			'mimes'     => array(
				'png'  => 'image/png',
				'gif'  => 'image/gif',
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg'
			)
		));

		if (isset($upload['error'])) {
			wp_send_json_error($upload['error']);
		}

		$upload['name'] = basename($upload['file']);

		wp_send_json_success($upload);
	}

	/**
	 * Determines the maximum permitted file size for uploads
	 *
	 * @since 4.0
	 */
	public function get_max_upload_size() {
		$post = $this->parse_size(ini_get('post_max_size'));
		$upload = $this->parse_size(ini_get('upload_max_filesize'));
		$memory = $this->parse_size(ini_get('memory_limit'));

		return min($post, $upload, $memory);
	}

	/**
	 * Parses a size string (e.g. 8M) into bytes
	 *
	 * @since 4.0
	 *
	 * @param string $size Size string to parse
	 */
	public function parse_size($size) {
		if (intval($size) <= 0) {
			return 0;
		}

		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
		$size = preg_replace('/[^0-9\.]/', '', $size);
		if ($unit) {
			$size = $size * pow(1024, stripos('bkmgtpezy', $unit[0]));
		}

		return round($size);
	}

	/**
	 * Returns a list of available marker icons
	 *
	 * @since 4.0
	 */
	public function get_icons() {
		$dir = opendir(MMP::$icons_dir);
		if ($dir === false) {
			return array();
		}

		$icons = array();
		$allowed = array('png', 'gif', 'jpg', 'jpeg');
		while (($file = readdir($dir)) !== false) {
			if (!is_file(MMP::$icons_dir . $file)) {
				continue;
			}

			$info = pathinfo($file);
			$ext = strtolower($info['extension']);
			if (!in_array($ext, $allowed, true)) {
				continue;
			}

			$icons[] = $file;
		}
		closedir($dir);
		sort($icons);

		return $icons;
	}
}
