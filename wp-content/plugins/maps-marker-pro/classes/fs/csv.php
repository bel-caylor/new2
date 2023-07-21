<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class CSV {
	public $error;

	private $handle;
	private $delimiter;
	private $enclosure;
	private $escape;
	private $header;
	private $current_row;

	/**
	 * Sets up the class
	 *
	 * @since 4.20
	 *
	 * @param string $delimiter (optional) Field delimiter (autodetected if empty)
	 * @param string $enclosure (optional) Field enclosure
	 * @param string $escape (optional) Escape character (disabled if empty)
	 */
	public function __construct($delimiter = '', $enclosure = '"', $escape = "\0") {
		$this->error = null;

		$this->handle = null;
		$this->delimiter = $delimiter;
		$this->enclosure = $enclosure;
		$this->escape = $escape;
		$this->header = array();
		$this->current_row = 0;
	}

	/**
	 * Opens a CSV file for processing
	 *
	 * @since 4.20
	 *
	 * @param string $file Absolute path to the file
	 */
	public function open($file) {
		ini_set('auto_detect_line_endings', '1');

		$handle = fopen($file, 'r');
		if ($handle === false) {
			$this->error = esc_html__('File could not be read', 'mmp');
			return false;
		};
		$this->handle = $handle;

		if (!$this->delimiter) {
			$delimiter = $this->detect_delimiter();
			if ($delimiter === false) {
				$this->error = esc_html__('Unable to determine delimiter', 'mmp');
				return false;
			}
			$this->delimiter = $delimiter;
		}

		$header = $this->get_header();
		if ($header === false) {
			$this->error = esc_html__('Invalid header row', 'mmp');
			return false;
		}
		$this->header = $header;
	}

	/**
	 * Reads rows and combines them with the header
	 *
	 * @since 4.20
	 *
	 * @param int $n (optional) Maximum number of rows to get (disabled if 0)
	 */
	public function get_rows($n = 0) {
		$row_count = 0;
		$data = array();
		while (($row = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
			// Skip empty lines
			if ($row === array(null)) {
				continue;
			}

			$row_count++;
			$this->current_row++;

			if (count($this->header) !== count($row)) {
				$this->error = sprintf(esc_html__('Header and data size mismatch at row %1$u', 'mmp'), $this->current_row);
				return false;
			}

			$data[$this->current_row] = array_combine($this->header, $row);

			if ($n && $row_count >= $n) {
				break;
			}
		}

		return $data;
	}

	/**
	 * Checks if there are more rows to be read
	 *
	 * @since 4.20
	 */
	public function has_more_rows() {
		return !feof($this->handle);
	}

	/**
	 * Attempts to detect which delimiter is being used
	 *
	 * @since 4.20
	 */
	private function detect_delimiter() {
		foreach (array(',', ';', "\t") as $delimiter) {
			$this->rewind();

			$row1 = fgetcsv($this->handle, 0, $delimiter, $this->enclosure, $this->escape);
			$row2 = fgetcsv($this->handle, 0, $delimiter, $this->enclosure, $this->escape);

			if (count($row1) > 1 && count($row1) === count($row2)) {
				return $delimiter;
			}
		}

		return false;
	}

	/**
	 * Returns the header
	 *
	 * @since 4.20
	 */
	private function get_header() {
		$this->rewind();

		$row = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);

		if (count($row) < 2) {
			return false;
		}

		return $row;
	}

	/**
	 * Rewinds the file
	 *
	 * @since 4.20
	 */
	private function rewind() {
		$this->current_row = 0;

		rewind($this->handle);

		// Move past the BOM magic number if present
		if (fread($this->handle, 3) !== "\xef\xbb\xbf") {
			rewind($this->handle);
		}

		// Skip empty lines at the beginning
		$offset = ftell($this->handle);
		while(($row = fgetcsv($this->handle)) !== false) {
			if ($row !== array(null)) {
				fseek($this->handle, $offset);
				break;
			}
			$offset = ftell($this->handle);
		};
	}
}
