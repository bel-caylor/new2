<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class JSON {
	public $error;

	/**
	 * Sets up the class
	 *
	 * @since 4.20
	 */
	public function __construct() {
		$this->error = null;
	}

	/**
	 * Reads a JSON file and converts it to an associative array
	 *
	 * @since 4.20
	 *
	 * @param string $file Absolute path to the file
	 */
	public function parse($file) {
		$content = file_get_contents($file);
		if ($content === false) {
			$this->error = esc_html__('File could not be read', 'mmp');
			return false;
		}

		$json = json_decode($content, true);
		if ($json === null) {
			$this->error = esc_html__('File could not be parsed', 'mmp');
			return false;
		}

		return $json;
	}
}
