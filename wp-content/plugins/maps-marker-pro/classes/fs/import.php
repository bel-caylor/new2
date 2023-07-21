<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class Import {
	public $test;
	public $log;
	public $error;

	private $queue;

	/**
	 * Sets up the class
	 *
	 * @since 4.20
	 */
	public function __construct() {
		$this->test = true;
		$this->log = array();
		$this->error = null;

		$this->queue = array();
	}

	/**
	 * Adds a marker to the import queue
	 *
	 * @since 4.20
	 *
	 * @param array $marker Associative array of marker data
	 * @param string $geocoding Sets the gocoding behavior (on, missing, off)
	 * @param string $geocoding_provider Selects the geocoding provider (locationiq, mapquest, google, tomtom)
	 * @param string $marker_mode Sets the marker mode (add, update, both)
	 */
	public function add($marker, $geocoding, $geocoding_provider, $marker_mode) {
		$db = MMP::get_instance('MMP\DB');
		$mmp_geocoding = MMP::get_instance('MMP\Geocoding');

		$geocoding_flag = false;
		if (!$marker['lat'] || !$marker['lng']) {
			if ($geocoding === 'off') {
				$this->queue[] = array(
					'status'  => 3,
					'message' => esc_html__('Missing or incomplete coordinates', 'mmp')
				);
				return;
			}
			if (!$marker['address']) {
				$this->queue[] = array(
					'status'  => 3,
					'message' => esc_html__('Missing address for geocoding', 'mmp')
				);
				return;
			}
			$geocoding_flag = true;
		}
		if ($geocoding === 'on') {
			if (!$marker['address']) {
				$this->queue[] = array(
					'status'  => 3,
					'message' => esc_html__('Missing address for geocoding', 'mmp')
				);
				return;
			}
			$geocoding_flag = true;
		}
		if (!$this->test && $geocoding_flag) {
			$result = $mmp_geocoding->getLatLng($marker['address'], $geocoding_provider);
			if (!$result['success']) {
				$this->queue[] = array(
					'status'  => 3,
					'message' => esc_html__('Geocoding error', 'mmp') . ' (' . $result['message'] . ')'
				);
				return;
			}
			$marker['lat'] = $result['lat'];
			$marker['lng'] = $result['lon'];
		}

		$mode_flag = 'add';
		if ($marker_mode !== 'add') {
			$old_marker = $db->get_marker($marker['id']);
			if ($marker_mode === 'update') {
				if (!$marker['id']) {
					$this->queue[] = array(
						'status'  => 3,
						'message' => esc_html__('Missing marker ID', 'mmp')
					);
					return;
				} else if (!$old_marker) {
					$this->queue[] = array(
						'status'  => 3,
						'message' => sprintf(esc_html__('Marker with ID %1$s not found', 'mmp'), $marker['id'])
					);
					return;
				} else {
					$mode_flag = 'update';
				}
			} else {
				if ($old_marker) {
					$mode_flag = 'update';
				}
			}
		}
		if ($mode_flag === 'add') {
			$marker['id'] = 0;
			$this->queue[] = array(
				'status'  => 1,
				'message' => esc_html__('New marker added', 'mmp'),
				'marker'  => $marker
			);
			return;
		} else {
			$this->queue[] = array(
				'status'  => 2,
				'message' => sprintf(esc_html__('Marker with ID %1$s updated', 'mmp'), $marker['id']),
				'marker'  => $marker
			);
			return;
		}
	}

	/**
	 * Processes the import queue
	 *
	 * @since 4.20
	 *
	 * @param bool $assignments Whether to assign imported markers
	 * @param string $assign_mode Sets the assignment mode (file, missing, fixed)
	 * @param array $assign_maps List of map IDs to assign the markers to
	 */
	public function write($assignments, $assign_mode, $assign_maps) {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');

		$adding = array();
		$updating = array();
		$assigning = array();
		foreach ($this->queue as $key => $marker) {
			if ($marker['status'] === 1) {
				$adding[] = $marker['marker'];
				unset($marker['marker']);
			} else if ($marker['status'] === 2) {
				$updating[] = $marker['marker'];
				unset($marker['marker']);
			}
			$this->log[] = $marker;
			unset($this->queue[$key]);
		}

		if ($this->test) {
			return;
		}

		if (count($adding)) {
			$result = $db->add_markers($adding);
			if ($result !== false) {
				if ($assignments) {
					$current_id = $wpdb->insert_id;
					foreach ($adding as $add) {
						if ($assign_mode === 'fixed' || ($assign_mode === 'missing' && !count($add['maps']))) {
							$assigning[$current_id] = $assign_maps;
						} else if (count($add['maps'])) {
							$assigning[$current_id] = $add['maps'];
						}
						$current_id++;
					}
				}
			} else {
				$this->error = esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')';
				return false;
			}
		}

		foreach ($updating as $update) {
			$result = $db->update_marker((object) $update, $update['id']);
			if ($result !== false) {
				if ($assignments) {
					if ($assign_mode === 'fixed' || ($assign_mode === 'missing' && !count($update['maps']))) {
						$assigning[$update['id']] = $assign_maps;
					} else if (count($update['maps'])) {
						$assigning[$update['id']] = $update['maps'];
					}
				}
			} else {
				$this->error = esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')';
				return false;
			}
		}

		if (count($assigning)) {
			$db->unassign_all_maps_markers(array_keys($assigning));
			$db->assign_assoc($assigning);
		}
	}
}
