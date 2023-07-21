<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;
use MMP\API;

class Tools extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_query_maps', array($this, 'query_maps'));
		add_action('wp_ajax_mmp_query_markers', array($this, 'query_markers'));
		add_action('wp_ajax_mmp_batch_settings', array($this, 'batch_settings'));
		add_action('wp_ajax_mmp_batch_layers', array($this, 'batch_layers'));
		add_action('wp_ajax_mmp_batch_marker', array($this, 'batch_marker'));
		add_action('wp_ajax_mmp_replace_icon', array($this, 'replace_icon'));
		add_action('wp_ajax_mmp_backup', array($this, 'backup'));
		add_action('wp_ajax_mmp_restore', array($this, 'restore'));
		add_action('wp_ajax_mmp_update_settings', array($this, 'update_settings'));
		add_action('wp_ajax_mmp_move_markers', array($this, 'move_markers'));
		add_action('wp_ajax_mmp_remove_markers', array($this, 'remove_markers'));
		add_action('wp_ajax_mmp_assign_markers', array($this, 'assign_markers'));
		add_action('wp_ajax_mmp_delete_all_maps', array($this, 'delete_all_maps'));
		add_action('wp_ajax_mmp_delete_all_markers', array($this, 'delete_all_markers'));
		add_action('wp_ajax_mmp_register_strings', array($this, 'register_strings'));
		add_action('wp_ajax_mmp_import', array($this, 'import'));
		add_action('wp_ajax_mmp_export', array($this, 'export'));
		add_action('wp_ajax_mmp_reset_database', array($this, 'reset_database'));
		add_action('wp_ajax_mmp_reset_settings', array($this, 'reset_settings'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook The current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_tools')) !== 'mapsmarkerpro_tools') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'toolsActions();');
	}

	/**
	 * AJAX request for retrieving available maps in Select2 data format
	 *
	 * @since 4.15
	 */
	public function query_maps() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-query-maps', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$filters = array();
		if (isset($_POST['term']) && $_POST['term']) {
			$filters['name'] = $_POST['term'];
		}

		$total = $db->count_maps($filters);

		$filters['limit'] = 25;
		$page = (isset($_POST['page'])) ? absint($_POST['page']) : 1;
		if ($page > 1) {
			$filters['offset'] = ($page - 1) * 25;
		}

		$maps = $db->get_all_maps(true, $filters);

		$results = array();
		foreach ($maps as $map) {
			$results[] = array(
				'id'   => $map->id,
				'text' => '[' . $map->id . '] ' . esc_html($map->name) . ' (' . $map->markers . ' ' . esc_html__('markers', 'mmp') . ')'
			);
		}

		wp_send_json_success(array(
			'results' => $results,
			'more'    => (($page * 25) < $total)
		));
	}

	/**
	 * AJAX request for retrieving available markers in Select2 data format
	 *
	 * @since 4.15
	 */
	public function query_markers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-query-markers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$filters = array();
		if (isset($_POST['term']) && $_POST['term']) {
			$filters['name'] = $_POST['term'];
		}

		$total = $db->count_markers($filters);

		$filters['limit'] = 25;
		$page = (isset($_POST['page'])) ? absint($_POST['page']) : 1;
		if ($page > 1) {
			$filters['offset'] = ($page - 1) * 25;
		}

		$markers = $db->get_all_markers($filters);

		$results = array();
		foreach ($markers as $marker) {
			$results[] = array(
				'id'   => $marker->id,
				'text' => '[' . $marker->id . '] ' . esc_html($marker->name)
			);
		}

		wp_send_json_success(array(
			'results' => $results,
			'more'    => (($page * 25) < $total)
		));
	}

	/**
	 * Changes settings for multiple maps
	 *
	 * @since 4.1
	 */
	 public function batch_settings() {
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-tools-batch-settings', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();
		$date = gmdate('Y-m-d H:i:s');
		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$batch_settings_mode = (isset($settings['batch_settings_mode']) && $settings['batch_settings_mode'] === 'all') ? 'all' : 'include';

		if ($batch_settings_mode === 'include' && !isset($settings['batch_settings_maps'])) {
			wp_send_json_error(esc_html__('No maps selected', 'mmp'));
		}

		$batch_settings = array();
		$keys = array_keys($mmp_settings->get_map_defaults());
		foreach ($keys as $key) {
			if (isset($settings["{$key}Check"])) {
				$batch_settings[$key] = (isset($settings[$key])) ? $settings[$key] : false;
			}
		}

		if ($batch_settings_mode === 'all') {
			$maps = $db->get_all_maps();
		} else {
			$maps = $db->get_maps($settings['batch_settings_maps']);
		}
		foreach ($maps as $map) {
			$new_settings = array_merge(json_decode($map->settings, true), $batch_settings);
			$new_settings = $mmp_settings->validate_map_settings($new_settings, false, false);
			$map->settings = json_encode($new_settings, JSON_FORCE_OBJECT);
			$map->updated_by_id = $current_user->ID;
			$map->updated_on = $date;
			$db->update_map($map, $map->id);
		}

		wp_send_json_success(esc_html__('Settings updated successfully', 'mmp'));
	 }

	 /**
	 * Changes layers for multiple maps
	 *
	 * @since 4.3
	 */
	public function batch_layers() {
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-tools-batch-layers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();
		$date = gmdate('Y-m-d H:i:s');
		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$batch_layers_mode = (isset($settings['batch_layers_mode']) && $settings['batch_layers_mode'] === 'all') ? 'all' : 'include';

		if ($batch_layers_mode === 'include' && !isset($settings['batch_layers_maps'])) {
			wp_send_json_error(esc_html__('No maps selected', 'mmp'));
		}

		$batch_layers = array(
			'basemaps'       => (isset($settings['basemaps'])) ? $settings['basemaps'] : array(),
			'basemapDefault' => (isset($settings['basemapDefault'])) ? $settings['basemapDefault'] : null,
			'overlays'       => (isset($settings['overlays'])) ? $settings['overlays'] : array()
		);

		if ($batch_layers_mode === 'all') {
			$maps = $db->get_all_maps();
		} else {
			$maps = $db->get_maps($settings['batch_layers_maps']);
		}
		foreach ($maps as $map) {
			$new_settings = array_merge(json_decode($map->settings, true), $batch_layers);
			$new_settings = $mmp_settings->validate_map_settings($new_settings, false, false);
			$map->settings = json_encode($new_settings, JSON_FORCE_OBJECT);
			$map->updated_by_id = $current_user->ID;
			$map->updated_on = $date;
			$db->update_map($map, $map->id);
		}

		wp_send_json_success(esc_html__('Settings updated successfully', 'mmp'));
	 }

	 /**
	 * Changes settings for multiple markers
	 *
	 * @since 4.14
	 */
	public function batch_marker() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-batch-marker', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();
		$date = gmdate('Y-m-d H:i:s');
		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		if (!isset($settings['batch_marker_mode']) || !in_array($settings['batch_marker_mode'], array('all', 'markers', 'maps'), true)) {
			wp_send_json_error(esc_html__('Invalid request', 'mmp'));
		}
		if ($settings['batch_marker_mode'] === 'markers' && !isset($settings['batch_marker_markers'])) {
			wp_send_json_error(esc_html__('No markers selected', 'mmp'));
		}
		if ($settings['batch_marker_mode'] === 'maps' && !isset($settings['batch_marker_maps'])) {
			wp_send_json_error(esc_html__('No maps selected', 'mmp'));
		}

		if ($settings['batch_marker_mode'] === 'markers') {
			$markers = $db->get_markers($settings['batch_marker_markers']);
		} else if ($settings['batch_marker_mode'] === 'maps') {
			$markers = $db->get_maps_markers($settings['batch_marker_maps']);
		} else {
			$markers = $db->get_all_markers();
		}
		foreach ($markers as $marker) {
			if (isset($settings['markerZoomCheck'])) {
				$marker->zoom = abs(floatval($settings['markerZoom']));
			}
			if (isset($settings['markerIconCheck'])) {
				$icon = ($settings['markerIcon'] === plugins_url('images/leaflet/marker.png', MMP::$path)) ? '' : basename($settings['markerIcon']);
				$marker->icon = $icon;
			}
			if (isset($settings['markerBlankCheck'])) {
				$marker->blank = ($settings['markerBlank'] === '1') ? '1' : '0';
			}
			$marker->updated_by_id = $current_user->ID;
			$marker->updated_on = $date;
			$db->update_marker($marker, $marker->id);
		}

		wp_send_json_success(esc_html__('Settings updated successfully', 'mmp'));
	 }

	 /**
	 * Replaces a marker icon
	 *
	 * @since 4.1
	 */
	public function replace_icon() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-replace-icon', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['source']) || !isset($_POST['target'])) {
			wp_send_json_error(esc_html__('Source or target missing', 'mmp'));
		}
		$source = ($_POST['source'] === plugins_url('images/leaflet/marker.png', MMP::$path)) ? '' : basename($_POST['source']);
		$target = ($_POST['target'] === plugins_url('images/leaflet/marker.png', MMP::$path)) ? '' : basename($_POST['target']);

		$wpdb->update(
			"{$wpdb->prefix}mmp_markers",
			array('icon' => $target),
			array('icon' => $source),
			array('%s'),
			array('%s')
		);

		wp_send_json_success(esc_html__('Icon replaced successfully', 'mmp'));
	}

	/**
	 * Backs up the database
	 *
	 * @since 4.0
	 */
	public function backup() {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		check_ajax_referer('mmp-tools-backup', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$table = (isset($_POST['table'])) ? absint($_POST['table']) : 0;
		$offset = (isset($_POST['offset'])) ? absint($_POST['offset']) : 0;
		$total = (isset($_POST['offset'])) ? json_decode($_POST['total'], true) : array();
		$file = (isset($_POST['filename']) && $_POST['filename']) ? MMP::$temp_dir . $_POST['filename'] : MMP::$temp_dir . 'backup-' . gmdate('Y-m-d-his') . '.mmp';

		if (!count($total)) {
			$index = 0;
			while (($cur_table = $this->get_table($index)) !== false) {
				$rows = $wpdb->get_var("SELECT COUNT(1) FROM $cur_table");
				$total[] = intval($rows); // MySQL always returns a string
				$index++;
			}
			fclose(fopen($file, 'w'));
		}

		$handle = fopen($file, 'a');
		$batch = $wpdb->get_results("SELECT * FROM " . $this->get_table($table) . " LIMIT $offset, 1000");
		if (!count($batch)) {
			$log[] = '[OK] Table ' . $this->get_table($table) . ' skipped (empty)';
		} else {
			foreach ($batch as $line) {
				$data = "$table:" . json_encode($line) . "\n";
				fwrite($handle, $data);
			}
			$log[] = '[OK] Processed table ' . $this->get_table($table) . ' (' . ($offset / 1000 + 1) . ' of ' . ceil($total[$table] / 1000) . ')';
		}
		fclose($handle);

		$filename = basename($file);
		$response = array(
			'table'    => $table,
			'offset'   => $offset,
			'total'    => $total,
			'log'      => $log,
			'filename' => basename($filename)
		);
		if (($table + 1) > 3) {
			$url = API::$base_url . "index.php?mapsmarkerpro=download_temp&filename={$filename}&nonce=" . wp_create_nonce('mmp-download-temp');
			$response['message'] = esc_html__('Backup completed successfully', 'mmp') . '<br />' . sprintf($l10n->kses__('If the download does not start automatically, please <a href="%1$s">click here</a>', 'mmp'), $url);
			$response['url'] = $url;
		}
		wp_send_json_success($response);
	}

	/**
	 * Restores a database backup
	 *
	 * @since 4.0
	 */
	public function restore() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-restore-backup', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		$table = (isset($_POST['table'])) ? absint($_POST['table']) : 0;
		$offset = (isset($_POST['offset'])) ? absint($_POST['offset']) : 0;
		$total = (isset($_POST['offset'])) ? json_decode($_POST['total'], true) : array();

		$file = sys_get_temp_dir() . '/restore.mmp';
		if (isset($_FILES['upload'])) {
			move_uploaded_file($_FILES['upload']['tmp_name'], $file);
		}
		$handle = fopen($file, 'r');

		if (!count($total)) {
			$db->create_tables();
			$index = 0;
			while (($cur_table = $this->get_table($index)) !== false) {
				$total[] = 0;
				$index++;
			}
			while (($buffer = fgets($handle)) !== false) {
				$cur_table = substr($buffer, 0, 1);
				$total[$cur_table]++;
			}
			rewind($handle);
		}

		if ($offset === 0) {
			$wpdb->query('TRUNCATE TABLE ' . $this->get_table($table));
		}

		$batch = array();
		$count = 0;
		while (($buffer = fgets($handle)) !== false) {
			if (substr($buffer, 0, 1) < $table) {
				continue;
			}
			if ($count >= $offset && $count < $offset + 1000) {
				if (substr($buffer, 0, 1) > $table) {
					break;
				}
				$batch[] = substr($buffer, 2);
			}
			$count++;
		}
		fclose($handle);

		if (!count($batch)) {
			$log[] = '[OK] Table ' . $this->get_table($table) . ' skipped (empty)';
		} else {
			// Chaining the rows and only calling the query once is significantly faster
			if ($table === 0) {
				$sanity = $db->prepare_layers();
			} else if ($table === 1) {
				$sanity = $db->prepare_maps();
			} else if ($table === 2) {
				$sanity = $db->prepare_markers();
			} else {
				$sanity = $db->prepare_rels();
			}
			$cols = implode(',', array_keys($batch[0]));
			$sql = 'INSERT INTO ' . $this->get_table($table) . " ($cols) VALUES ";
			foreach ($batch as $line) {
				$data = json_decode($line, true);
				foreach ($data as $column => $value) {
					if (!isset($sanity[$column])) {
						unset($data[$column]);
						continue;
					}
					if ($value === null) {
						$data[$column] = 'NULL';
					} else if ($sanity[$column] === '%d') {
						$data[$column] = intval($value);
					} else if ($sanity[$column] === '%f') {
						$data[$column] = floatval($value);
					} else {
						$data[$column] = "'" . esc_sql($value) . "'";
					}
				}
				$sql .= '(' . implode(',', array_values($data)) . '),';
			}
			$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query
			$wpdb->query($sql);
			$log[] = '[OK] Processed table ' . $this->get_table($table) . ' (' . ($offset / 1000 + 1) . ' of ' . ceil($total[$table] / 1000) . ')';
		}

		$response = array(
			'table'  => $table,
			'offset' => $offset,
			'total'  => $total,
			'log'    => $log
		);
		if (($table + 1) > 3) {
			$response['message'] = esc_html__('Restore completed successfully', 'mmp');
			$response['maps'] = $this->get_map_list();
		}
		wp_send_json_success($response);
	}

	/**
	 * Updates the settings
	 *
	 * @since 4.0
	 */
	public function update_settings() {
		check_ajax_referer('mmp-tools-update-settings', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['settings'])) {
			wp_send_json_error(esc_html__('Settings missing', 'mmp'));
		}

		$settings = json_decode(stripslashes($_POST['settings']), true);
		if ($settings === null) {
			wp_send_json_error(esc_html__('Could not parse settings', 'mmp'));
		}

		update_option('mapsmarkerpro_settings', $settings);
		set_transient('mapsmarkerpro_flush_rewrite_rules', true);

		wp_send_json_success(esc_html__('Settings updated successfully', 'mmp'));
	}

	/**
	 * Moves markers from a map to a different map
	 *
	 * @since 4.0
	 */
	public function move_markers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-move-markers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['source']) || !isset($_POST['target'])) {
			wp_send_json_error(esc_html__('Source or target missing', 'mmp'));
		}

		$source = $db->get_map($_POST['source']);
		$target = $db->get_map($_POST['target']);
		if (!$source || !$target) {
			wp_send_json_error(esc_html__('Source or target not found', 'mmp'));
		}

		$ids = array();
		foreach ($db->get_map_markers($source->id) as $marker) {
			$ids[] = $marker->id;
		}
		$db->unassign_all_markers($source->id);
		$db->assign_markers($target->id, $ids);

		wp_send_json_success(array(
			'message' => sprintf(esc_html__('Markers from map %1$s successfully moved to map %2$s', 'mmp'), $source->id, $target->id),
			'maps'    => $this->get_map_list()
		));
	}

	/**
	 * Removes markers from a map
	 *
	 * @since 4.0
	 */
	public function remove_markers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-remove-markers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['map'])) {
			wp_send_json_error(esc_html__('Map missing', 'mmp'));
		}

		$map = $db->get_map($_POST['map']);
		if (!$map) {
			wp_send_json_error(esc_html__('Map not found', 'mmp'));
		}

		$db->unassign_all_markers($map->id);

		wp_send_json_success(array(
			'message' => sprintf(esc_html__('Markers successfully removed from map %1$s', 'mmp'), $map->id),
			'maps'    => $this->get_map_list()
		));
	}

	/**
	 * Assigns markers to a map
	 *
	 * @since 4.9
	 */
	public function assign_markers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-assign-markers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['map'])) {
			wp_send_json_error(esc_html__('Map missing', 'mmp'));
		}

		$map = $db->get_map($_POST['map']);
		if (!$map) {
			wp_send_json_error(esc_html__('Map not found', 'mmp'));
		}

		$markers = $db->get_all_markers(array(
			'include_maps' => -1
		));
		if (!$markers) {
			wp_send_json_error(esc_html__('No markers found', 'mmp'));
		}

		$marker_ids = array();
		foreach ($markers as $marker) {
			$marker_ids[] = $marker->id;
		}
		$db->assign_markers($map->id, $marker_ids);

		wp_send_json_success(array(
			'message' => sprintf(esc_html__('Markers successfully assigned to map %1$s', 'mmp'), $map->id),
			'maps'    => $this->get_map_list()
		));
	}

	/**
	 * Delete all maps
	 *
	 * @since 4.14
	 */
	public function delete_all_maps() {
		global $wpdb;

		check_ajax_referer('mmp-tools-delete-all-maps', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['confirm']) || $_POST['confirm'] === 'false') {
			wp_send_json_error(esc_html__('You need to confirm this action', 'mmp'));
		}

		if (isset($_POST['truncate']) && $_POST['truncate'] === 'true') {
			$wpdb->query("TRUNCATE {$wpdb->prefix}mmp_maps");
		} else {
			$wpdb->query("DELETE FROM {$wpdb->prefix}mmp_maps");
		}
		$wpdb->query("TRUNCATE {$wpdb->prefix}mmp_relationships");

		wp_send_json_success(esc_html__('Maps deleted successfully', 'mmp'));
	}

	/**
	 * Delete all markers
	 *
	 * @since 4.14
	 */
	public function delete_all_markers() {
		global $wpdb;

		check_ajax_referer('mmp-tools-delete-all-markers', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['confirm']) || $_POST['confirm'] === 'false') {
			wp_send_json_error(esc_html__('You need to confirm this action', 'mmp'));
		}

		if (isset($_POST['truncate']) && $_POST['truncate'] === 'true') {
			$wpdb->query("TRUNCATE {$wpdb->prefix}mmp_markers");
		} else {
			$wpdb->query("DELETE FROM {$wpdb->prefix}mmp_markers");
		}
		$wpdb->query("TRUNCATE {$wpdb->prefix}mmp_relationships");

		wp_send_json_success(esc_html__('Markers deleted successfully', 'mmp'));
	}

	/**
	 * Initializes all existing maps and markers for multilingual support
	 *
	 * @since 4.0
	 */
	public function register_strings() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');

		check_ajax_referer('mmp-tools-register-strings', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!$l10n->check_ml()) {
			wp_send_json_error(esc_html__('No supported multilingual plugin found', 'mmp'));
		}

		$maps = $db->get_all_maps();
		foreach ($maps as $map) {
			$l10n->register("Map (ID {$map->id}) name", $map->name);
		}
		$markers = $db->get_all_markers();
		foreach ($markers as $marker) {
			$l10n->register("Marker (ID {$marker->id}) name", $marker->name);
			$l10n->register("Marker (ID {$marker->id}) address", $marker->address);
			$l10n->register("Marker (ID {$marker->id}) link", $marker->link);
			$l10n->register("Marker (ID {$marker->id}) popup", $marker->popup);
		}

		wp_send_json_success(esc_html__('Strings for all maps and markers successfully registered for translation', 'mmp'));
	}

	/**
	 * Imports markers to the database
	 *
	 * @since 4.0
	 */
	public function import() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');
		$mmp_json = MMP::get_instance('MMP\FS\JSON');
		$mmp_csv = MMP::get_instance('MMP\FS\CSV');
		$mmp_import = MMP::get_instance('MMP\FS\Import');

		$file_type = (isset($_POST['file_type']) && $_POST['file_type'] === 'geojson') ? 'geojson' : 'csv';
		$test_mode = (isset($_POST['test_mode']) && $_POST['test_mode'] === 'off') ? false : true;
		$marker_mode = (isset($_POST['marker_mode']) && in_array($_POST['marker_mode'], array('add', 'update', 'both'))) ? $_POST['marker_mode'] : 'add';
		$geocoding = (isset($_POST['geocoding']) && in_array($_POST['geocoding'], array('on', 'missing', 'off'))) ? $_POST['geocoding'] : 'off';
		$geocoding_provider = (isset($_POST['geocoding_provider']) && in_array($_POST['geocoding_provider'], array('none', 'locationiq', 'mapquest', 'google', 'tomtom'))) ? $_POST['geocoding_provider'] : 'none';
		$assignments = (isset($_POST['assignments']) && $_POST['assignments'] === 'off') ? false : true;
		$assign_mode = (isset($_POST['assign_mode']) && in_array($_POST['assign_mode'], array('file', 'missing', 'fixed'))) ? $_POST['assign_mode'] : 'file';
		$assign_maps = (isset($_POST['assign_maps'])) ? $db->sanitize_ids($_POST['assign_maps']) : null;

		check_ajax_referer('mmp-tools-import', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_FILES['file'])) {
			wp_send_json_error(esc_html__('File missing', 'mmp'));
		}

		$time = microtime(true);
		$mmp_import->test = $test_mode;

		if (!$test_mode) {
			$wpdb->query('START TRANSACTION');
		}

		$details = array();
		if ($file_type === 'geojson') {
			$json = $mmp_json->parse($_FILES['file']['tmp_name']);
			if ($mmp_json->error) {
				wp_send_json_error($mmp_json->error);
			}
			if (!isset($json['features']) || !is_array($json['features'])) {
				wp_send_json_error(esc_html__('No geographical data found', 'mmp'));
			}
			foreach ($json['features'] as $feature) {
				if (!isset($feature['geometry']['type']) || $feature['geometry']['type'] !== 'Point') {
					continue;
				}
				$feature = $db->build_marker($feature, true);
				$mmp_import->add($feature, $geocoding, $geocoding_provider, $marker_mode);
			}
			$mmp_import->write($assignments, $assign_mode, $assign_maps);
			if ($mmp_import->error) {
				wp_send_json_error($mmp_import->error);
			}
		} else {
			$mmp_csv->open($_FILES['file']['tmp_name']);
			if ($mmp_csv->error) {
				wp_send_json_error($mmp_csv->error);
			}
			while ($mmp_csv->has_more_rows()) {
				$rows = $mmp_csv->get_rows(1000);
				if ($mmp_csv->error) {
					wp_send_json_error($mmp_csv->error);
				}
				foreach ($rows as $row) {
					$row = $db->build_marker($row);
					$mmp_import->add($row, $geocoding, $geocoding_provider, $marker_mode);
				}
				$mmp_import->write($assignments, $assign_mode, $assign_maps);
				if ($mmp_import->error) {
					wp_send_json_error($mmp_import->error);
				}
			}
		}

		$stats = array_count_values(array_column($mmp_import->log, 'status'));
		$counts = array(
			isset($stats[1]) ? $stats[1] : 0,
			isset($stats[2]) ? $stats[2] : 0,
			isset($stats[3]) ? $stats[3] : 0,
			isset($stats[4]) ? $stats[4] : 0
		);

		if (!$test_mode) {
			if ($counts[3]) {
				$wpdb->query('ROLLBACK');
			} else {
				$wpdb->query('COMMIT');
			}
		}

		wp_send_json_success(array(
			'summary' => sprintf(esc_html__('%1$s markers added, %2$s markers updated, %3$s markers skipped, %4$s errors', 'mmp'), $counts[0], $counts[1], $counts[2], $counts[3]),
			'details' => $mmp_import->log,
			'maps'    => $this->get_map_list(),
			'mem'     => memory_get_peak_usage(),
			'time'    => microtime(true) - $time
		));
	}

	/**
	 * Exports markers from the database
	 *
	 * @since 4.0
	 */
	public function export() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');

		$file_type = (isset($_POST['file_type']) && in_array($_POST['file_type'], array('geojson', 'csv'))) ? $_POST['file_type'] : 'csv';
		$filter_mode = (isset($_POST['filter_mode']) && in_array($_POST['filter_mode'], array('all', 'include', 'exclude'))) ? $_POST['filter_mode'] : 'all';
		$filter_include = (isset($_POST['filter_include'])) ? $_POST['filter_include'] : array();
		$filter_exclude = (isset($_POST['filter_exclude'])) ? $_POST['filter_exclude'] : array();

		check_ajax_referer('mmp-tools-export', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if ($filter_mode === 'include') {
			$filters = array(
				'include_maps' => $filter_include
			);
		} else if ($filter_mode === 'exclude') {
			$filters = array(
				'exclude_maps' => $filter_exclude
			);
		} else {
			$filters = array();
		}

		if ($file_type === 'geojson') {
			$json = array(
				'type' => 'FeatureCollection',
				'features' => array()
			);
			$total = $db->count_markers($filters);
			$batches = ceil($total / 1000);
			for ($i = 1; $i <= $batches; $i++) {
				$filters = array_merge($filters, array(
					'offset' => ($i - 1) * 1000,
					'limit' => 1000
				));
				$markers = $db->get_all_markers($filters);
				foreach ($markers as $marker) {
					$json['features'][] = array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(
								floatval($marker->lng),
								floatval($marker->lat)
							)
						),
						'properties' => array(
							'id' => $marker->id,
							'name' => $marker->name,
							'address' => $marker->address,
							'zoom' => $marker->zoom,
							'icon' => $marker->icon,
							'popup' => $marker->popup,
							'link' => $marker->link,
							'blank' => $marker->blank,
							'maps' => $marker->maps
						)
					);
				}
			}
			$json = json_encode($json, JSON_PRETTY_PRINT);

			$file = MMP::$temp_dir . 'export-' . gmdate('Y-m-d-his') . '.geojson';
			$handle = file_put_contents($file, $json);
			if ($handle === false) {
				wp_send_json_error(esc_html__('File could not be written', 'mmp'));
			}
		} else {
			$csv = array(
				array(
					'id',
					'name',
					'address',
					'lat',
					'lng',
					'zoom',
					'icon',
					'popup',
					'link',
					'blank',
					'maps',
				)
			);
			$total = $db->count_markers($filters);
			$batches = ceil($total / 1000);
			for ($i = 1; $i <= $batches; $i++) {
				$filters = array_merge($filters, array(
					'offset' => ($i - 1) * 1000,
					'limit' => 1000
				));
				$markers = $db->get_all_markers($filters);
				foreach ($markers as $marker) {
					$csv[] = array(
						$marker->id,
						$marker->name,
						$marker->address,
						$marker->lat,
						$marker->lng,
						$marker->zoom,
						$marker->icon,
						$marker->popup,
						$marker->link,
						$marker->blank,
						$marker->maps
					);
				}
			}

			$file = MMP::$temp_dir . 'export-' . gmdate('Y-m-d-his') . '.csv';
			$handle = fopen($file, 'w');
			if ($handle === false) {
				wp_send_json_error(esc_html__('File could not be written', 'mmp'));
			}
			foreach ($csv as $row) {
				fputcsv($handle, $row);
			}
			fclose($handle);
		}

		$url = API::$base_url . 'index.php?mapsmarkerpro=download_temp&filename=' . basename($file) . '&nonce=' . wp_create_nonce('mmp-download-temp');
		wp_send_json_success(array(
			'message' => esc_html__('Export completed successfully', 'mmp') . '<br />' . sprintf($l10n->kses__('If the download does not start automatically, please <a href="%1$s">click here</a>', 'mmp'), $url),
			'url'     => $url
		));
	}

	/**
	 * Resets the database
	 *
	 * @since 4.0
	 */
	public function reset_database() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-tools-reset-db', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['confirm']) || $_POST['confirm'] === 'false') {
			wp_send_json_error(esc_html__('You need to confirm this action', 'mmp'));
		}

		$db->reset_tables();

		wp_send_json_success(esc_html__('Database reset successfully', 'mmp'));
	}

	/**
	 * Resets the settings
	 *
	 * @since 4.0
	 */
	public function reset_settings() {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-tools-reset-settings', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		if (!isset($_POST['confirm']) || $_POST['confirm'] === 'false') {
			wp_send_json_error(esc_html__('You need to confirm this action', 'mmp'));
		}

		if (isset($_POST['reset']['plugin']) && $_POST['reset']['plugin'] === 'true') {
			update_option('mapsmarkerpro_settings', $mmp_settings->get_default_settings());
			set_transient('mapsmarkerpro_flush_rewrite_rules', true);
		}
		if (isset($_POST['reset']['map']) && $_POST['reset']['map'] === 'true') {
			update_option('mapsmarkerpro_map_defaults', $mmp_settings->get_default_map_settings());
		}
		if (isset($_POST['reset']['marker']) && $_POST['reset']['marker'] === 'true') {
			update_option('mapsmarkerpro_marker_defaults', $mmp_settings->get_default_marker_settings());
		}

		wp_send_json_success(esc_html__('Settings reset successfully', 'mmp'));
	}

	/**
	 * Returns the table name for a given table index
	 *
	 * @since 4.0
	 */
	private function get_table($index) {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');

		$tables = array(
			"{$wpdb->prefix}mmp_layers",
			"{$wpdb->prefix}mmp_maps",
			"{$wpdb->prefix}mmp_markers",
			"{$wpdb->prefix}mmp_relationships"
		);
		if (isset($tables[$index])) {
			return $tables[$index];
		} else {
			return false;
		}
	}

	/**
	 * Returns a list of all maps
	 *
	 * @since 4.0
	 */
	public function get_map_list() {
		$db = MMP::get_instance('MMP\DB');

		$maps = $db->get_all_maps(true);
		$map_list = array();
		foreach ($maps as $map) {
			// No escaping, since wp_send_json() escapes automatically
			$map_list[$map->id] = "[{$map->id}] " . $map->name . " ({$map->markers} " . __('markers', 'mmp') . ')';
		}

		return $map_list;
	}

	/**
	 * Shows the tools page
	 *
	 * @since 4.0
	 */
	protected function show() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');
		$upload = MMP::get_instance('MMP\FS\Upload');
		$l10n = MMP::get_instance('MMP\L10n');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$layers = MMP::get_instance('MMP\Layers');
		$debug = MMP::get_instance('MMP\Debug');

		$unassigned_count = $db->count_markers(array(
			'include_maps' => -1
		));
		$settings = $mmp_settings->get_map_defaults();
		$old_version = get_option('leafletmapsmarker_version_pro');

		$basemaps = $layers->get_basemaps();
		$overlays = $layers->get_overlays();
		$settings['geocodingMinChars'] = MMP::$settings['geocodingMinChars'];
		$settings['geocodingLocationIqApiKey'] = MMP::$settings['geocodingLocationIqApiKey'];
		$settings['geocodingMapQuestApiKey'] = MMP::$settings['geocodingMapQuestApiKey'];
		$settings['geocodingGoogleApiKey'] = MMP::$settings['geocodingGoogleApiKey'];
		$settings['geocodingTomTomApiKey'] = MMP::$settings['geocodingTomTomApiKey'];

		$debug_info = $debug->get_info();
		$ajax_test_result = json_decode(wp_remote_retrieve_body($debug_info['ajax_response']), true);
		$ajax_test_result = (isset($ajax_test_result['success']) && $ajax_test_result['success'] === true);

		?>
		<div class="wrap mmp-wrap">
			<h1><?= esc_html__('Tools', 'mmp') ?></h1>
			<div class="mmp-tools-tabs">
				<button id="maps_markers_tab" class="mmp-tablink" type="button"><?= esc_html__('Maps and markers', 'mmp') ?></button>
				<button id="import_tab" class="mmp-tablink" type="button"><?= esc_html__('Import markers', 'mmp') ?></button>
				<button id="export_tab" class="mmp-tablink" type="button"><?= esc_html__('Export markers', 'mmp') ?></button>
				<button id="backup_restore_tab" class="mmp-tablink" type="button"><?= esc_html__('Backup and restore', 'mmp') ?></button>
				<?php if ($old_version !== false): ?>
					<button id="migration_tab" class="mmp-tablink" type="button"><?= esc_html__('Data migration', 'mmp') ?></button>
				<?php endif; ?>
				<button id="health_tab" class="mmp-tablink" type="button"><?= esc_html__('Health check', 'mmp') ?></button>
				<button id="reset_tab" class="mmp-tablink" type="button"><?= esc_html__('Reset', 'mmp') ?></button>
			</div>
			<div id="maps_markers_tab_content" class="mmp-tools-tab">
				<div id="batch_settings_section" class="mmp-tools-section">
					<h2><?= esc_html__('Batch update map settings', 'mmp') ?></h2>
					<div class="mmp-batch-settings-tabs">
						<button type="button" class="mmp-batch-settings-tablink" data-target="mapDimensions"><?= esc_html__('Map dimensions', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="initialView"><?= esc_html__('Initial view', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="panel"><?= esc_html__('Panel', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="layers"><?= esc_html__('Layers', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="zoomButtons"><?= esc_html__('Zoom buttons', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="geocodingControl"><?= esc_html__('Geocoding control', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="fullscreenButton"><?= esc_html__('Fullscreen button', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="resetButton"><?= esc_html__('Reset button', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="locateButton"><?= esc_html__('Locate button', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="measureButton"><?= esc_html__('Measure button', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="scale"><?= esc_html__('Scale', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="layersControl"><?= esc_html__('Layers control', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="filtersControl"><?= esc_html__('Filters control', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="gpxControl"><?= esc_html__('GPX control', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="minimap"><?= esc_html__('Minimap', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="attribution"><?= esc_html__('Attribution', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="icon"><?= esc_html__('Icon', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="clustering"><?= esc_html__('Clustering', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="tooltip"><?= esc_html__('Tooltip', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="share"><?= esc_html__('Share', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="popup"><?= esc_html__('Popup', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="list"><?= esc_html__('List', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="interaction"><?= esc_html__('Interaction', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="track"><?= esc_html__('Track', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="metadata"><?= esc_html__('Metadata', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="waypoints"><?= esc_html__('Waypoints', 'mmp') ?></button>
						<button type="button" class="mmp-batch-settings-tablink" data-target="elevationChart"><?= esc_html__('Elevation chart', 'mmp') ?></button>
					</div>
					<div>
						<p><?= esc_html__('Only active settings will be applied to the selected maps. To activate a setting, tick the checkbox on the left.', 'mmp') ?></p>
					</div>
					<div class="mmp-batch-settings">
						<form id="mapSettings" method="POST">
							<div id="mapDimensionsContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="widthCheck" class="batch-settings-check" name="widthCheck" />
										<label for="width"><?= esc_html__('Width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="width" name="width" value="<?= $settings['width'] ?>" min="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="widthUnitCheck" class="batch-settings-check" name="widthUnitCheck" />
										<?= esc_html__('Width unit', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label><input type="radio" id="widthUnitPct" name="widthUnit" value="%" <?= !($settings['widthUnit'] === '%') ?: 'checked="checked"' ?> />%</label>
										<label><input type="radio" id="widthUnitPx" name="widthUnit" value="px" <?= !($settings['widthUnit'] === 'px') ?: 'checked="checked"' ?> />px</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="heightCheck" class="batch-settings-check" name="heightCheck" />
										<label for="height"><?= esc_html__('Height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="height" name="height" value="<?= $settings['height'] ?>" min="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="callbackCheck" class="batch-settings-check" name="callbackCheck" />
										<label for="callback"><?= esc_html__('JavaScript callback', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="callback" name="callback" value="<?= esc_attr($settings['callback']) ?>" />
									</div>
								</div>
							</div>
							<div id="initialViewContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="latCheck" class="batch-settings-check" name="latCheck" />
										<label for="lat"><?= esc_html__('Latitude', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="lat" name="lat" value="<?= $settings['lat'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="lngCheck" class="batch-settings-check" name="lngCheck" />
										<label for="lng"><?= esc_html__('Longitude', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="lng" name="lng" value="<?= $settings['lng'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="maxBoundsCheck" class="batch-settings-check" name="maxBoundsCheck" />
										<label for="maxBounds"><?= esc_html__('Max bounds', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="maxBounds" name="maxBounds"><?= str_replace(',', ",\n", $settings['maxBounds']) ?></textarea>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="zoomCheck" class="batch-settings-check" name="zoomCheck" />
										<label for="zoom"><?= esc_html__('Zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="zoom" name="zoom" value="<?= $settings['zoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minZoomCheck" class="batch-settings-check" name="minZoomCheck" />
										<label for="minZoom"><?= esc_html__('Min zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minZoom" name="minZoom" value="<?= $settings['minZoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="maxZoomCheck" class="batch-settings-check" name="maxZoomCheck" />
										<label for="maxZoom"><?= esc_html__('Max zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="maxZoom" name="maxZoom" value="<?= $settings['maxZoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="zoomStepCheck" class="batch-settings-check" name="zoomStepCheck" />
										<label for="zoomStep"><?= esc_html__('Zoom step', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="zoomStep" name="zoomStep" value="<?= $settings['zoomStep'] ?>" min="0.1" max="1" step="0.1" />
									</div>
								</div>
							</div>
							<div id="panelContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelCheck" class="batch-settings-check" name="panelCheck" />
										<label for="panel"><?= esc_html__('Show', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panel" name="panel" <?= !$settings['panel'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelColorCheck" class="batch-settings-check" name="panelColorCheck" />
										<label for="panelColor"><?= esc_html__('Color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="panelColor" name="panelColor" value="<?= $settings['panelColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelFsCheck" class="batch-settings-check" name="panelFsCheck" />
										<label for="panelFs"><?= esc_html__('Fullscreen button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panelFs" name="panelFs" <?= !$settings['panelFs'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelGpxCheck" class="batch-settings-check" name="panelGpxCheck" />
										<label for="panelGpx"><?= esc_html__('GPX download button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGpx" name="panelGpx" <?= !$settings['panelGpx'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelGeoJsonCheck" class="batch-settings-check" name="panelGeoJsonCheck" />
										<label for="panelGeoJson"><?= esc_html__('GeoJSON button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGeoJson" name="panelGeoJson" <?= !$settings['panelGeoJson'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelKmlCheck" class="batch-settings-check" name="panelKmlCheck" />
										<label for="panelKml"><?= esc_html__('KML button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panelKml" name="panelKml" <?= !$settings['panelKml'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="panelGeoRssCheck" class="batch-settings-check" name="panelGeoRssCheck" />
										<label for="panelGeoRss"><?= esc_html__('GeoRss button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGeoRss" name="panelGeoRss" <?= !$settings['panelGeoRss'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span></span>
										</label>
									</div>
								</div>
							</div>
							<div id="layersContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="basemapEdgeBufferTilesCheck" class="batch-settings-check" name="basemapEdgeBufferTilesCheck" />
										<label for="basemapEdgeBufferTiles"><?= esc_html__('Edge buffer tiles', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="basemapEdgeBufferTiles" name="basemapEdgeBufferTiles">
											<option value="0" <?= !($settings['basemapEdgeBufferTiles'] === 0) ?: 'selected="selected"' ?>><?= esc_html__('Off', 'mmp') ?></option>
											<option value="1" <?= !($settings['basemapEdgeBufferTiles'] === 1) ?: 'selected="selected"' ?>>1</option>
											<option value="2" <?= !($settings['basemapEdgeBufferTiles'] === 2) ?: 'selected="selected"' ?>>2</option>
											<option value="3" <?= !($settings['basemapEdgeBufferTiles'] === 3) ?: 'selected="selected"' ?>>3</option>
											<option value="4" <?= !($settings['basemapEdgeBufferTiles'] === 4) ?: 'selected="selected"' ?>>4</option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="basemapGoogleStylesCheck" class="batch-settings-check" name="basemapGoogleStylesCheck" />
										<label for="basemapGoogleStyles"><?= esc_html__('Google styles', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="basemapGoogleStyles" name="basemapGoogleStyles"><?= $settings['basemapGoogleStyles'] ?></textarea><br />
									</div>
								</div>
							</div>
							<div id="geocodingControlContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="geocodingControlPositionCheck" class="batch-settings-check" name="geocodingControlPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="geocodingControlPosition" value="hidden" <?= !($settings['geocodingControlPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="geocodingControlPosition" value="topleft" <?= !($settings['geocodingControlPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="geocodingControlPosition" value="topright" <?= !($settings['geocodingControlPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="geocodingControlPosition" value="bottomleft" <?= !($settings['geocodingControlPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="geocodingControlPosition" value="bottomright" <?= !($settings['geocodingControlPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="geocodingControlCollapsedCheck" class="batch-settings-check" name="geocodingControlCollapsedCheck" />
										<label for="geocodingControlCollapsed"><?= esc_html__('Collapsed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="geocodingControlCollapsed" name="geocodingControlCollapsed">
											<option value="collapsed" <?= !($settings['geocodingControlCollapsed'] === 'collapsed') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed', 'mmp') ?></option>
											<option value="collapsed-mobile" <?= !($settings['geocodingControlCollapsed'] === 'collapsed-mobile') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed on mobile', 'mmp') ?></option>
											<option value="expanded" <?= !($settings['geocodingControlCollapsed'] === 'expanded') ?: 'selected="selected"' ?>><?= esc_html__('Expanded', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="geocodingControlShowMarkerCheck" class="batch-settings-check" name="geocodingControlShowMarkerCheck" />
										<label for="geocodingControlShowMarker"><?= esc_html__('Show location marker', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="geocodingControlShowMarker" name="geocodingControlShowMarker" <?= !$settings['geocodingControlShowMarker'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="geocodingControlMarkerIconCheck" class="batch-settings-check" name="geocodingControlMarkerIconCheck" />
										<?= esc_html__('Marker icon', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<input type="hidden" id="geocodingControlMarkerIcon" name="geocodingControlMarkerIcon" value="<?= $settings['geocodingControlMarkerIcon'] ?>" />
										<img class="mmp-geocoding-control-icon mmp-align-middle" src="<?= (!$settings['geocodingControlMarkerIcon']) ? plugins_url('images/leaflet/pin.png', MMP::$path) : MMP::$icons_url . $settings['geocodingControlMarkerIcon'] ?>" />
									</div>
								</div>
							</div>
							<div id="zoomButtonsContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="zoomControlPositionCheck" class="batch-settings-check" name="zoomControlPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="zoomControlPosition" value="hidden" <?= !($settings['zoomControlPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="zoomControlPosition" value="topleft" <?= !($settings['zoomControlPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="zoomControlPosition" value="topright" <?= !($settings['zoomControlPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="zoomControlPosition" value="bottomleft" <?= !($settings['zoomControlPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="zoomControlPosition" value="bottomright" <?= !($settings['zoomControlPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
							</div>
							<div id="fullscreenButtonContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="fullscreenPositionCheck" class="batch-settings-check" name="fullscreenPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="fullscreenPosition" value="hidden" <?= !($settings['fullscreenPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="fullscreenPosition" value="topleft" <?= !($settings['fullscreenPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="fullscreenPosition" value="topright" <?= !($settings['fullscreenPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="fullscreenPosition" value="bottomleft" <?= !($settings['fullscreenPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="fullscreenPosition" value="bottomright" <?= !($settings['fullscreenPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
							</div>
							<div id="resetButtonContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="resetPositionCheck" class="batch-settings-check" name="resetPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="resetPosition" value="hidden" <?= !($settings['resetPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="resetPosition" value="topleft" <?= !($settings['resetPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="resetPosition" value="topright" <?= !($settings['resetPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="resetPosition" value="bottomleft" <?= !($settings['resetPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="resetPosition" value="bottomright" <?= !($settings['resetPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="resetOnDemandCheck" class="batch-settings-check" name="resetOnDemandCheck" />
										<label for="resetOnDemand"><?= esc_html__('On demand', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="resetOnDemand" name="resetOnDemand" <?= !$settings['resetOnDemand'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="locateButtonContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locatePositionCheck" class="batch-settings-check" name="locatePositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="locatePosition" value="hidden" <?= !($settings['locatePosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="locatePosition" value="topleft" <?= !($settings['locatePosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="locatePosition" value="topright" <?= !($settings['locatePosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="locatePosition" value="bottomleft" <?= !($settings['locatePosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="locatePosition" value="bottomright" <?= !($settings['locatePosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateDrawCircleCheck" class="batch-settings-check" name="locateDrawCircleCheck" />
										<label for="locateDrawCircle"><?= esc_html__('Draw circle', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateDrawCircle" name="locateDrawCircle" <?= !$settings['locateDrawCircle'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateDrawMarkerCheck" class="batch-settings-check" name="locateDrawMarkerCheck" />
										<label for="locateDrawMarker"><?= esc_html__('Draw marker', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateDrawMarker" name="locateDrawMarker" <?= !$settings['locateDrawMarker'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateSetViewCheck" class="batch-settings-check" name="locateSetViewCheck" />
										<label for="locateSetView"><?= esc_html__('Set view', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="locateSetView" name="locateSetView">
											<option value="once" <?= !($settings['locateSetView'] === 'once') ?: 'selected="selected"' ?>><?= esc_html__('Once', 'mmp') ?></option>
											<option value="always" <?= !($settings['locateSetView'] === 'always') ?: 'selected="selected"' ?>><?= esc_html__('Always', 'mmp') ?></option>
											<option value="untilPan" <?= !($settings['locateSetView'] === 'untilPan') ?: 'selected="selected"' ?>><?= esc_html__('Until pan', 'mmp') ?></option>
											<option value="untilPanOrZoom" <?= !($settings['locateSetView'] === 'untilPanOrZoom') ?: 'selected="selected"' ?>><?= esc_html__('Until pan or zoom', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateKeepCurrentZoomLevelCheck" class="batch-settings-check" name="locateKeepCurrentZoomLevelCheck" />
										<label for="locateKeepCurrentZoomLevel"><?= esc_html__('Keep current zoom level', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateKeepCurrentZoomLevel" name="locateKeepCurrentZoomLevel" <?= !$settings['locateKeepCurrentZoomLevel'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateClickBehaviorInViewCheck" class="batch-settings-check" name="locateClickBehaviorInViewCheck" />
										<label for="locateClickBehaviorInView"><?= esc_html__('Click behavior in view', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="locateClickBehaviorInView" name="locateClickBehaviorInView">
											<option value="stop" <?= !($settings['locateClickBehaviorInView'] === 'stop') ?: 'selected="selected"' ?>><?= esc_html__('Stop', 'mmp') ?></option>
											<option value="setView" <?= !($settings['locateClickBehaviorInView'] === 'setView') ?: 'selected="selected"' ?>><?= esc_html__('Set view', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateClickBehaviorOutOfViewCheck" class="batch-settings-check" name="locateClickBehaviorOutOfViewCheck" />
										<label for="locateClickBehaviorOutOfView"><?= esc_html__('Click behavior out of view', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="locateClickBehaviorOutOfView" name="locateClickBehaviorOutOfView">
											<option value="stop" <?= !($settings['locateClickBehaviorOutOfView'] === 'stop') ?: 'selected="selected"' ?>><?= esc_html__('Stop', 'mmp') ?></option>
											<option value="setView" <?= !($settings['locateClickBehaviorOutOfView'] === 'setView') ?: 'selected="selected"' ?>><?= esc_html__('Set view', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateMetricCheck" class="batch-settings-check" name="locateMetricCheck" />
										<label for="locateMetric"><?= esc_html__('Metric units', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateMetric" name="locateMetric" <?= !$settings['locateMetric'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateShowPopupCheck" class="batch-settings-check" name="locateShowPopupCheck" />
										<label for="locateShowPopup"><?= esc_html__('Show popup', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateShowPopup" name="locateShowPopup" <?= !$settings['locateShowPopup'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="locateAutostartCheck" class="batch-settings-check" name="locateAutostartCheck" />
										<label for="locateAutostart"><?= esc_html__('Autostart', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="locateAutostart" name="locateAutostart" <?= !$settings['locateAutostart'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="measureButtonContent" class="mmp-map-batch-settings-group">
								<span><?= esc_html__('Measure button', 'mmp') ?></span>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measurePositionCheck" class="batch-settings-check" name="measurePositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="measurePosition" value="hidden" <?= !($settings['measurePosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="measurePosition" value="topleft" <?= !($settings['measurePosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="measurePosition" value="topright" <?= !($settings['measurePosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="measurePosition" value="bottomleft" <?= !($settings['measurePosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="measurePosition" value="bottomright" <?= !($settings['measurePosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measureUnitCheck" class="batch-settings-check" name="measureUnitCheck" />
										<label for="measureUnit"><?= esc_html__('Unit', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="measureUnit" name="measureUnit">
											<option value="metric" <?= !($settings['measureUnit'] === 'metric') ?: 'selected="selected"' ?>><?= esc_html__('Metric', 'mmp') ?></option>
											<option value="imperial" <?= !($settings['measureUnit'] === 'imperial') ?: 'selected="selected"' ?>><?= esc_html__('Imperial', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measureShowBearingsCheck" class="batch-settings-check" name="measureShowBearingsCheck" />
										<label for="measureShowBearings"><?= esc_html__('Show bearings', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="measureShowBearings" name="measureShowBearings" <?= !$settings['measureShowBearings'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measureClearMeasurementsOnStopCheck" class="batch-settings-check" name="measureClearMeasurementsOnStopCheck" />
										<label for="measureClearMeasurementsOnStop"><?= esc_html__('Clear measurements on stop', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="measureClearMeasurementsOnStop" name="measureClearMeasurementsOnStop" <?= !$settings['measureClearMeasurementsOnStop'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measureShowClearControlCheck" class="batch-settings-check" name="measureShowClearControlCheck" />
										<label for="measureShowClearControl"><?= esc_html__('Show clear button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="measureShowClearControl" name="measureShowClearControl" <?= !$settings['measureShowClearControl'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="measureShowUnitControlCheck" class="batch-settings-check" name="measureShowUnitControlCheck" />
										<label for="measureShowUnitControl"><?= esc_html__('Show unit button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="measureShowUnitControl" name="measureShowUnitControl" <?= !$settings['measureShowUnitControl'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="scaleContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="scalePositionCheck" class="batch-settings-check" name="scalePositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="scalePosition" value="hidden" <?= !($settings['scalePosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="scalePosition" value="topleft" <?= !($settings['scalePosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="scalePosition" value="topright" <?= !($settings['scalePosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="scalePosition" value="bottomleft" <?= !($settings['scalePosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="scalePosition" value="bottomright" <?= !($settings['scalePosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="scaleMaxWidthCheck" class="batch-settings-check" name="scaleMaxWidthCheck" />
										<label for="scaleMaxWidth"><?= esc_html__('Max width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="scaleMaxWidth" name="scaleMaxWidth" value="<?= $settings['scaleMaxWidth'] ?>" min="0" step="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="scaleMetricCheck" class="batch-settings-check" name="scaleMetricCheck" />
										<label for="scaleMetric"><?= esc_html__('Show metric', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="scaleMetric" name="scaleMetric" <?= !$settings['scaleMetric'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="scaleImperialCheck" class="batch-settings-check" name="scaleImperialCheck" />
										<label for="scaleImperial"><?= esc_html__('Show imperial', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="scaleImperial" name="scaleImperial" <?= !$settings['scaleImperial'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="layersControlContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="layersPositionCheck" class="batch-settings-check" name="layersPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="layersPosition" value="hidden" <?= !($settings['layersPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="layersPosition" value="topleft" <?= !($settings['layersPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="layersPosition" value="topright" <?= !($settings['layersPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="layersPosition" value="bottomleft" <?= !($settings['layersPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="layersPosition" value="bottomright" <?= !($settings['layersPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="layersCollapsedCheck" class="batch-settings-check" name="layersCollapsedCheck" />
										<label for="layersCollapsed"><?= esc_html__('Collapsed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="layersCollapsed" name="layersCollapsed">
											<option value="collapsed" <?= !($settings['layersCollapsed'] === 'collapsed') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed', 'mmp') ?></option>
											<option value="collapsed-mobile" <?= !($settings['layersCollapsed'] === 'collapsed-mobile') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed on mobile', 'mmp') ?></option>
											<option value="expanded" <?= !($settings['layersCollapsed'] === 'expanded') ?: 'selected="selected"' ?>><?= esc_html__('Expanded', 'mmp') ?></option>
										</select>
									</div>
								</div>
							</div>
							<div id="filtersControlContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersPositionCheck" class="batch-settings-check" name="filtersPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="filtersPosition" value="hidden" <?= !($settings['filtersPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="filtersPosition" value="topleft" <?= !($settings['filtersPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="filtersPosition" value="topright" <?= !($settings['filtersPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="filtersPosition" value="bottomleft" <?= !($settings['filtersPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="filtersPosition" value="bottomright" <?= !($settings['filtersPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersCollapsedCheck" class="batch-settings-check" name="filtersCollapsedCheck" />
										<label for="filtersCollapsed"><?= esc_html__('Collapsed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="filtersCollapsed" name="filtersCollapsed">
											<option value="collapsed" <?= !($settings['filtersCollapsed'] === 'collapsed') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed', 'mmp') ?></option>
											<option value="collapsed-mobile" <?= !($settings['filtersCollapsed'] === 'collapsed-mobile') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed on mobile', 'mmp') ?></option>
											<option value="expanded" <?= !($settings['filtersCollapsed'] === 'expanded') ?: 'selected="selected"' ?>><?= esc_html__('Expanded', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersButtonsCheck" class="batch-settings-check" name="filtersButtonsCheck" />
										<label for="filtersButtons"><?= esc_html__('Buttons', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="filtersButtons" name="filtersButtons" <?= !$settings['filtersButtons'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersIconCheck" class="batch-settings-check" name="filtersIconCheck" />
										<label for="filtersIcon"><?= esc_html__('Icon', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="filtersIcon" name="filtersIcon" <?= !$settings['filtersIcon'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersNameCheck" class="batch-settings-check" name="filtersNameCheck" />
										<label for="filtersName"><?= esc_html__('Name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="filtersName" name="filtersName" <?= !$settings['filtersName'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersCountCheck" class="batch-settings-check" name="filtersCountCheck" />
										<label for="filtersCount"><?= esc_html__('Count', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="filtersCount" name="filtersCount" <?= !$settings['filtersCount'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersOrderByCheck" class="batch-settings-check" name="filtersOrderByCheck" />
										<label for="filtersOrderBy"><?= esc_html__('Order by', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="filtersOrderBy" name="filtersOrderBy">
											<option value="id" <?= !($settings['filtersOrderBy'] === 'id') ?: 'selected="selected"' ?>><?= esc_html__('ID', 'mmp') ?></option>
											<option value="name" <?= !($settings['filtersOrderBy'] === 'name') ?: 'selected="selected"' ?>><?= esc_html__('Name', 'mmp') ?></option>
											<option value="count" <?= !($settings['filtersOrderBy'] === 'count') ?: 'selected="selected"' ?>><?= esc_html__('Count', 'mmp') ?></option>
											<option value="custom" <?= !($settings['filtersOrderBy'] === 'custom') ?: 'selected="selected"' ?>><?= esc_html__('Custom', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersSortOrderCheck" class="batch-settings-check" name="filtersSortOrderCheck" />
										<label for="filtersSortOrder"><?= esc_html__('Sort order', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="filtersSortOrder" name="filtersSortOrder" <?= !($settings['filtersOrderBy'] === 'custom') ? '' : 'disabled="disabled"' ?>>
											<option value="asc" <?= !($settings['filtersSortOrder'] === 'asc') ?: 'selected="selected"' ?>><?= esc_html__('Ascending', 'mmp') ?></option>
											<option value="desc" <?= !($settings['filtersSortOrder'] === 'desc') ?: 'selected="selected"' ?>><?= esc_html__('Descending', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="filtersLogicCheck" class="batch-settings-check" name="filtersLogicCheck" />
										<label for="filtersLogic"><?= esc_html__('Logic', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="filtersLogic" name="filtersLogic">
											<option value="or" <?= !($settings['filtersLogic'] === 'or') ?: 'selected="selected"' ?>><?= esc_html__('Or', 'mmp') ?></option>
											<option value="and" <?= !($settings['filtersLogic'] === 'and') ?: 'selected="selected"' ?>><?= esc_html__('And', 'mmp') ?></option>
										</select>
									</div>
								</div>
							</div>
							<div id="gpxControlContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxControlPositionCheck" class="batch-settings-check" name="gpxControlPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="gpxControlPosition" value="hidden" <?= !($settings['gpxControlPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="gpxControlPosition" value="topleft" <?= !($settings['gpxControlPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="gpxControlPosition" value="topright" <?= !($settings['gpxControlPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="gpxControlPosition" value="bottomleft" <?= !($settings['gpxControlPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="gpxControlPosition" value="bottomright" <?= !($settings['gpxControlPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxControlCollapsedCheck" class="batch-settings-check" name="gpxControlCollapsedCheck" />
										<label for="gpxControlCollapsed"><?= esc_html__('Collapsed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="gpxControlCollapsed" name="gpxControlCollapsed">
											<option value="collapsed" <?= !($settings['gpxControlCollapsed'] === 'collapsed') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed', 'mmp') ?></option>
											<option value="collapsed-mobile" <?= !($settings['gpxControlCollapsed'] === 'collapsed-mobile') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed on mobile', 'mmp') ?></option>
											<option value="expanded" <?= !($settings['gpxControlCollapsed'] === 'expanded') ?: 'selected="selected"' ?>><?= esc_html__('Expanded', 'mmp') ?></option>
										</select>
									</div>
								</div>
							</div>
							<div id="minimapContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapPositionCheck" class="batch-settings-check" name="minimapPositionCheck" />
										<?= esc_html__('Position', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="minimapPosition" value="hidden" <?= !($settings['minimapPosition'] === 'hidden') ?: 'checked="checked"' ?> />
											<i class="dashicons dashicons-no"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="minimapPosition" value="topleft" <?= !($settings['minimapPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="minimapPosition" value="topright" <?= !($settings['minimapPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="minimapPosition" value="bottomleft" <?= !($settings['minimapPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="minimapPosition" value="bottomright" <?= !($settings['minimapPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapMinimizedCheck" class="batch-settings-check" name="minimapMinimizedCheck" />
										<label for="minimapMinimized"><?= esc_html__('Collapsed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="minimapMinimized" name="minimapMinimized">
											<option value="collapsed" <?= !($settings['minimapMinimized'] === 'collapsed') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed', 'mmp') ?></option>
											<option value="collapsed-mobile" <?= !($settings['minimapMinimized'] === 'collapsed-mobile') ?: 'selected="selected"' ?>><?= esc_html__('Collapsed on mobile', 'mmp') ?></option>
											<option value="expanded" <?= !($settings['minimapMinimized'] === 'expanded') ?: 'selected="selected"' ?>><?= esc_html__('Expanded', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapWidthCheck" class="batch-settings-check" name="minimapWidthCheck" />
										<label for="minimapWidth"><?= esc_html__('Width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapWidth" name="minimapWidth" value="<?= $settings['minimapWidth'] ?>" min="1" step="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapHeightCheck" class="batch-settings-check" name="minimapHeightCheck" />
										<label for="minimapHeight"><?= esc_html__('Height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapHeight" name="minimapHeight" value="<?= $settings['minimapHeight'] ?>" min="1" step="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapCollapsedWidthCheck" class="batch-settings-check" name="minimapCollapsedWidthCheck" />
										<label for="minimapCollapsedWidth"><?= esc_html__('Collapsed width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapCollapsedWidth" name="minimapCollapsedWidth" value="<?= $settings['minimapCollapsedWidth'] ?>" min="1" step="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapCollapsedHeightCheck" class="batch-settings-check" name="minimapCollapsedHeightCheck" />
										<label for="minimapCollapsedHeight"><?= esc_html__('Collapsed height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapCollapsedHeight" name="minimapCollapsedHeight" value="<?= $settings['minimapCollapsedHeight'] ?>" min="1" step="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapZoomLevelOffsetCheck" class="batch-settings-check" name="minimapZoomLevelOffsetCheck" />
										<label for="minimapZoomLevelOffset"><?= esc_html__('Zoom level offset', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapZoomLevelOffset" name="minimapZoomLevelOffset" value="<?= $settings['minimapZoomLevelOffset'] ?>" min="-23" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="minimapZoomLevelFixedCheck" class="batch-settings-check" name="minimapZoomLevelFixedCheck" />
										<label for="minimapZoomLevelFixed"><?= esc_html__('Fixed zoom level', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minimapZoomLevelFixed" name="minimapZoomLevelFixed" value="<?= $settings['minimapZoomLevelFixed'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
							</div>
							<div id="attributionContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="attributionPositionCheck" class="batch-settings-check" name="attributionPositionCheck" />
										<?= esc_html__('Positon', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label class="mmp-radio">
											<input type="radio" name="attributionPosition" value="topleft" <?= !($settings['attributionPosition'] === 'topleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="attributionPosition" value="topright" <?= !($settings['attributionPosition'] === 'topright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-topright"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="attributionPosition" value="bottomleft" <?= !($settings['attributionPosition'] === 'bottomleft') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomleft"></i>
										</label>
										<label class="mmp-radio">
											<input type="radio" name="attributionPosition" value="bottomright" <?= !($settings['attributionPosition'] === 'bottomright') ?: 'checked="checked"' ?> />
											<i class="dashicons mmp-dashicons-bottomright"></i>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="attributionCondensedCheck" class="batch-settings-check" name="attributionCondensedCheck" />
										<label for="attributionCondensed"><?= esc_html__('Condensed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="attributionCondensed" name="attributionCondensed" <?= !$settings['attributionCondensed'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="iconContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="markerOpacityCheck" class="batch-settings-check" name="markerOpacityCheck" />
										<label for="markerOpacity"><?= esc_html__('Opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="markerOpacity" name="markerOpacity" value="<?= $settings['markerOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<div id="clusteringContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="clusteringCheck" class="batch-settings-check" name="clusteringCheck" />
										<label for="clustering"><?= esc_html__('Enable', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="clustering" name="clustering" <?= !$settings['clustering'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="showCoverageOnHoverCheck" class="batch-settings-check" name="showCoverageOnHoverCheck" />
										<label for="showCoverageOnHover"><?= esc_html__('Show bounds on hover', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="showCoverageOnHover" name="showCoverageOnHover" <?= !$settings['showCoverageOnHover'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="disableClusteringAtZoomCheck" class="batch-settings-check" name="disableClusteringAtZoomCheck" />
										<label for="disableClusteringAtZoom"><?= esc_html__('Disable at zoom', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="disableClusteringAtZoom" name="disableClusteringAtZoom" value="<?= $settings['disableClusteringAtZoom'] ?>" min="0" max="23" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="maxClusterRadiusCheck" class="batch-settings-check" name="maxClusterRadiusCheck" />
										<label for="maxClusterRadius"><?= esc_html__('Max cluster radius', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="maxClusterRadius" name="maxClusterRadius" value="<?= $settings['maxClusterRadius'] ?>" min="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="singleMarkerModeCheck" class="batch-settings-check" name="singleMarkerModeCheck" />
										<label for="singleMarkerMode"><?= esc_html__('Single marker mode', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="singleMarkerMode" name="singleMarkerMode" <?= !$settings['singleMarkerMode'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="spiderfyDistanceMultiplierCheck" class="batch-settings-check" name="spiderfyDistanceMultiplierCheck" />
										<label for="spiderfyDistanceMultiplier"><?= esc_html__('Spiderfy multiplier', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="spiderfyDistanceMultiplier" name="spiderfyDistanceMultiplier" value="<?= $settings['spiderfyDistanceMultiplier'] ?>" min="0" max="10" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="spiderfyOnEveryZoomCheck" class="batch-settings-check" name="spiderfyOnEveryZoomCheck" />
										<label for="spiderfyOnEveryZoom"><?= esc_html__('Spiderfy on every zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="spiderfyOnEveryZoom" name="spiderfyOnEveryZoom" <?= !$settings['spiderfyOnEveryZoom'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="tooltipContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="tooltipCheck" class="batch-settings-check" name="tooltipCheck" />
										<label for="tooltip"><?= esc_html__('Show', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="tooltip" name="tooltip" <?= !$settings['tooltip'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="tooltipDirectionCheck" class="batch-settings-check" name="tooltipDirectionCheck" />
										<label for="tooltipDirection"><?= esc_html__('Direction', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="tooltipDirection" name="tooltipDirection">
											<option value="auto" <?= !($settings['tooltipDirection'] === 'auto') ?: 'selected="selected"' ?>><?= esc_html__('Auto', 'mmp') ?></option>
											<option value="right" <?= !($settings['tooltipDirection'] === 'right') ?: 'selected="selected"' ?>><?= esc_html__('Right', 'mmp') ?></option>
											<option value="left" <?= !($settings['tooltipDirection'] === 'left') ?: 'selected="selected"' ?>><?= esc_html__('Left', 'mmp') ?></option>
											<option value="top" <?= !($settings['tooltipDirection'] === 'top') ?: 'selected="selected"' ?>><?= esc_html__('Top', 'mmp') ?></option>
											<option value="bottom" <?= !($settings['tooltipDirection'] === 'bottom') ?: 'selected="selected"' ?>><?= esc_html__('Bottom', 'mmp') ?></option>
											<option value="center" <?= !($settings['tooltipDirection'] === 'center') ?: 'selected="selected"' ?>><?= esc_html__('Center', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="tooltipPermanentCheck" class="batch-settings-check" name="tooltipPermanentCheck" />
										<label for="tooltipPermanent"><?= esc_html__('Permanent', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="tooltipPermanent" name="tooltipPermanent" <?= !$settings['tooltipPermanent'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="tooltipStickyCheck" class="batch-settings-check" name="tooltipStickyCheck" />
										<label for="tooltipSticky"><?= esc_html__('Sticky', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="tooltipSticky" name="tooltipSticky" <?= !$settings['tooltipSticky'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="tooltipOpacityCheck" class="batch-settings-check" name="tooltipOpacityCheck" />
										<label for="tooltipOpacity"><?= esc_html__('Opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="tooltipOpacity" name="tooltipOpacity" value="<?= $settings['tooltipOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<div id="shareContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="shareUrlCheck" class="batch-settings-check" name="shareUrlCheck" />
										<label for="shareUrl"><?= esc_html__('Share URL', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="shareUrl" name="shareUrl">
											<option value="page" <?= !($settings['shareUrl'] === 'page') ?: 'selected="selected"' ?>><?= esc_html__('Current page', 'mmp') ?></option>
											<option value="fs" <?= !($settings['shareUrl'] === 'fs') ?: 'selected="selected"' ?>><?= esc_html__('Fullscreen map', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="shareTextCheck" class="batch-settings-check" name="shareTextCheck" />
										<label for="shareText"><?= esc_html__('Share text', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="shareText" name="shareText"><?= $settings['shareText'] ?></textarea>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Share button', 'mmp') ?></span>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="popupShareCheck" class="batch-settings-check" name="popupShareCheck" />
											<label for="popupShare"><?= esc_html__('Show in popup', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="popupShare" name="popupShare" <?= !$settings['popupShare'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="listShareCheck" class="batch-settings-check" name="listShareCheck" />
											<label for="listShare"><?= esc_html__('Show in markers list', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="listShare" name="listShare" <?= !$settings['listShare'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Share window', 'mmp') ?></span>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="shareFacebookCheck" class="batch-settings-check" name="shareFacebookCheck" />
											<label for="shareFacebook">
												<div class="mmp-settings-share-button mmp-settings-share-button-facebook">
													<div class="mmp-settings-share-button-icon">
														<svg viewBox="0 0 24 24">
															<path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z" />
														</svg>
													</div>
												</div>
											</label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="shareFacebook" name="shareFacebook" <?= !$settings['shareFacebook'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="shareTwitterCheck" class="batch-settings-check" name="shareTwitterCheck" />
											<label for="shareTwitter">
												<div class="mmp-settings-share-button mmp-settings-share-button-twitter">
													<div class="mmp-settings-share-button-icon">
														<svg viewBox="0 0 24 24">
															<path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z" />
														</svg>
													</div>
												</div>
											</label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="shareTwitter" name="shareTwitter" <?= !$settings['shareTwitter'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="shareLinkedInCheck" class="batch-settings-check" name="shareLinkedInCheck" />
											<label for="shareLinkedIn">
												<div class="mmp-settings-share-button mmp-settings-share-button-linkedin">
													<div class="mmp-settings-share-button-icon">
														<svg viewBox="0 0 24 24">
															<path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z" />
														</svg>
													</div>
												</div>
											</label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="shareLinkedIn" name="shareLinkedIn" <?= !$settings['shareLinkedIn'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="shareWhatsAppCheck" class="batch-settings-check" name="shareWhatsAppCheck" />
											<label for="shareWhatsApp">
												<div class="mmp-settings-share-button mmp-settings-share-button-whatsapp">
													<div class="mmp-settings-share-button-icon">
														<svg viewBox="0 0 24 24">
															<path d="M20.1 3.9C17.9 1.7 15 .5 12 .5 5.8.5.7 5.6.7 11.9c0 2 .5 3.9 1.5 5.6L.6 23.4l6-1.6c1.6.9 3.5 1.3 5.4 1.3 6.3 0 11.4-5.1 11.4-11.4-.1-2.8-1.2-5.7-3.3-7.8zM12 21.4c-1.7 0-3.3-.5-4.8-1.3l-.4-.2-3.5 1 1-3.4L4 17c-1-1.5-1.4-3.2-1.4-5.1 0-5.2 4.2-9.4 9.4-9.4 2.5 0 4.9 1 6.7 2.8 1.8 1.8 2.8 4.2 2.8 6.7-.1 5.2-4.3 9.4-9.5 9.4zm5.1-7.1c-.3-.1-1.7-.9-1.9-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.1-.2.2-.3.2-.6.1s-1.2-.5-2.3-1.4c-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6s.3-.3.4-.5c.2-.1.3-.3.4-.5.1-.2 0-.4 0-.5C10 9 9.3 7.6 9 7c-.1-.4-.4-.3-.5-.3h-.6s-.4.1-.7.3c-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.3.2-.7.2-1.2.2-1.3-.1-.3-.3-.4-.6-.5z" />
														</svg>
													</div>
												</div>
											</label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="shareWhatsApp" name="shareWhatsApp" <?= !$settings['shareWhatsApp'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-batch-setting">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="shareEmailCheck" class="batch-settings-check" name="shareEmailCheck" />
											<label for="shareEmail">
												<div class="mmp-settings-share-button mmp-settings-share-button-email">
													<div class="mmp-settings-share-button-icon">
														<svg viewBox="0 0 24 24">
															<path d="M22 4H2C.9 4 0 4.9 0 6v12c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM7.25 14.43l-3.5 2c-.08.05-.17.07-.25.07-.17 0-.34-.1-.43-.25-.14-.24-.06-.55.18-.68l3.5-2c.24-.14.55-.06.68.18.14.24.06.55-.18.68zm4.75.07c-.1 0-.2-.03-.27-.08l-8.5-5.5c-.23-.15-.3-.46-.15-.7.15-.22.46-.3.7-.14L12 13.4l8.23-5.32c.23-.15.54-.08.7.15.14.23.07.54-.16.7l-8.5 5.5c-.08.04-.17.07-.27.07zm8.93 1.75c-.1.16-.26.25-.43.25-.08 0-.17-.02-.25-.07l-3.5-2c-.24-.13-.32-.44-.18-.68s.44-.32.68-.18l3.5 2c.24.13.32.44.18.68z" />
														</svg>
													</div>
												</div>
											</label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="shareEmail" name="shareEmail" <?= !$settings['shareEmail'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div id="popupContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupOpenOnHoverCheck" class="batch-settings-check" name="popupOpenOnHoverCheck" />
										<label for="popupOpenOnHover"><?= esc_html__('Open on hover', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupOpenOnHover" name="popupOpenOnHover" <?= !$settings['popupOpenOnHover'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupCenterOnMapCheck" class="batch-settings-check" name="popupCenterOnMapCheck" />
										<label for="popupCenterOnMap"><?= esc_html__('Center on map', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupCenterOnMap" name="popupCenterOnMap" <?= !$settings['popupCenterOnMap'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupMarkernameCheck" class="batch-settings-check" name="popupMarkernameCheck" />
										<label for="popupMarkername"><?= esc_html__('Show marker name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupMarkername" name="popupMarkername" <?= !$settings['popupMarkername'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupAddressCheck" class="batch-settings-check" name="popupAddressCheck" />
										<label for="popupAddress"><?= esc_html__('Show address', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupAddress" name="popupAddress" <?= !$settings['popupAddress'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupCoordinatesCheck" class="batch-settings-check" name="popupCoordinatesCheck" />
										<label for="popupCoordinates"><?= esc_html__('Show coordinates', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupCoordinates" name="popupCoordinates" <?= !$settings['popupCoordinates'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupDirectionsCheck" class="batch-settings-check" name="popupDirectionsCheck" />
										<label for="popupDirections"><?= esc_html__('Show directions link', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupDirections" name="popupDirections" <?= !$settings['popupDirections'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupMinWidthCheck" class="batch-settings-check" name="popupMinWidthCheck" />
										<label for="popupMinWidth"><?= esc_html__('Min width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="popupMinWidth" name="popupMinWidth" value="<?= $settings['popupMinWidth'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupMaxWidthCheck" class="batch-settings-check" name="popupMaxWidthCheck" />
										<label for="popupMaxWidth"><?= esc_html__('Max width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="popupMaxWidth" name="popupMaxWidth" value="<?= $settings['popupMaxWidth'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupMaxHeightCheck" class="batch-settings-check" name="popupMaxHeightCheck" />
										<label for="popupMaxHeight"><?= esc_html__('Max height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="popupMaxHeight" name="popupMaxHeight" value="<?= $settings['popupMaxHeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupCloseButtonCheck" class="batch-settings-check" name="popupCloseButtonCheck" />
										<label for="popupCloseButton"><?= esc_html__('Add close button', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupCloseButton" name="popupCloseButton" <?= !$settings['popupCloseButton'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="popupAutoCloseCheck" class="batch-settings-check" name="popupAutoCloseCheck" />
										<label for="popupAutoClose"><?= esc_html__('Auto close', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="popupAutoClose" name="popupAutoClose" <?= !$settings['popupAutoClose'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="listContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listCheck" class="batch-settings-check" name="listCheck" />
										<label for="list"><?= esc_html__('Show', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="list" name="list">
											<option value="0" <?= !($settings['list'] === 0) ?: 'selected="selected"' ?>><?= esc_html__('None', 'mmp') ?></option>
											<option value="1" <?= !($settings['list'] === 1) ?: 'selected="selected"' ?>><?= esc_html__('Below', 'mmp') ?></option>
											<option value="2" <?= !($settings['list'] === 2) ?: 'selected="selected"' ?>><?= esc_html__('Right', 'mmp') ?></option>
											<option value="3" <?= !($settings['list'] === 3) ?: 'selected="selected"' ?>><?= esc_html__('Left', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listWidthCheck" class="batch-settings-check" name="listWidthCheck" />
										<label for="listWidth"><?= esc_html__('Width', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listWidth" name="listWidth" value="<?= $settings['listWidth'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listBreakpointCheck" class="batch-settings-check" name="listBreakpointCheck" />
										<label for="listBreakpoint"><?= esc_html__('Breakpoint', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listBreakpoint" name="listBreakpoint" value="<?= $settings['listBreakpoint'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDistanceUnitCheck" class="batch-settings-check" name="listDistanceUnitCheck" />
										<label for="listDistanceUnit"><?= esc_html__('Distance unit', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listDistanceUnit" name="listDistanceUnit">
											<option value="metric" <?= !($settings['listDistanceUnit'] === 'metric') ?: 'selected="selected"' ?>><?= esc_html__('Metric', 'mmp') ?></option>
											<option value="imperial" <?= !($settings['listDistanceUnit'] === 'imperial') ?: 'selected="selected"' ?>><?= esc_html__('Imperial', 'mmp') ?></option>
											<option value="metric-imperial" <?= !($settings['listDistanceUnit'] === 'metric-imperial') ?: 'selected="selected"' ?>><?= esc_html__('Metric (imperial)', 'mmp') ?></option>
											<option value="imperial-metric" <?= !($settings['listDistanceUnit'] === 'imperial-metric') ?: 'selected="selected"' ?>><?= esc_html__('Imperial (metric)', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDistancePrecisionCheck" class="batch-settings-check" name="listDistancePrecisionCheck" />
										<label for="listDistancePrecision"><?= esc_html__('Distance precision', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listDistancePrecision" name="listDistancePrecision" value="<?= $settings['listDistancePrecision'] ?>" min="0" max="6" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listIconCheck" class="batch-settings-check" name="listIconCheck" />
										<label for="listIcon"><?= esc_html__('Icon', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listIcon" name="listIcon" <?= !$settings['listIcon'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listNameCheck" class="batch-settings-check" name="listNameCheck" />
										<label for="listName"><?= esc_html__('Name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listName" name="listName" <?= !$settings['listName'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDateCheck" class="batch-settings-check" name="listDateCheck" />
										<label for="listDate"><?= esc_html__('Date', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listDate" name="listDate" <?= !$settings['listDate'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDateTypeCheck" class="batch-settings-check" name="listDateTypeCheck" />
										<label for="listDateType"><?= esc_html__('Date type', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listDateType" name="listDateType">
											<option value="created" <?= !($settings['listDateType'] === 'created') ?: 'selected="selected"' ?>><?= esc_html__('Created', 'mmp') ?></option>
											<option value="updated" <?= !($settings['listDateType'] === 'updated') ?: 'selected="selected"' ?>><?= esc_html__('Updated', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDateFormatCheck" class="batch-settings-check" name="listDateFormatCheck" />
										<label for="listDateFormat"><?= esc_html__('Date format', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listDateFormat" name="listDateFormat">
											<option value="date" <?= !($settings['listDateFormat'] === 'date') ?: 'selected="selected"' ?>><?= esc_html__('Date', 'mmp') ?></option>
											<option value="time" <?= !($settings['listDateFormat'] === 'time') ?: 'selected="selected"' ?>><?= esc_html__('Time', 'mmp') ?></option>
											<option value="datetime" <?= !($settings['listDateFormat'] === 'datetime') ?: 'selected="selected"' ?>><?= esc_html__('Date & time', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listPopupCheck" class="batch-settings-check" name="listPopupCheck" />
										<label for="listPopup"><?= esc_html__('Popup', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listPopup" name="listPopup" <?= !$settings['listPopup'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listAddressCheck" class="batch-settings-check" name="listAddressCheck" />
										<label for="listAddress"><?= esc_html__('Address', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listAddress" name="listAddress" <?= !$settings['listAddress'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listCoordinatesCheck" class="batch-settings-check" name="listCoordinatesCheck" />
										<label for="listCoordinates"><?= esc_html__('Coordinates', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listCoordinates" name="listCoordinates" <?= !$settings['listCoordinates'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDistanceCheck" class="batch-settings-check" name="listDistanceCheck" />
										<label for="listDistance"><?= esc_html__('Distance', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listDistance" name="listDistance" <?= !$settings['listDistance'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listDirCheck" class="batch-settings-check" name="listDirCheck" />
										<label for="listDir"><?= esc_html__('Show directions link', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listDir" name="listDir" <?= !$settings['listDir'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listFsCheck" class="batch-settings-check" name="listFsCheck" />
										<label for="listFs"><?= esc_html__('Show fullscreen link', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listFs" name="listFs" <?= !$settings['listFs'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listLimitCheck" class="batch-settings-check" name="listLimitCheck" />
										<label for="listLimit"><?= esc_html__('Markers per page', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listLimit" name="listLimit" value="<?= $settings['listLimit'] ?>" min="1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listActionCheck" class="batch-settings-check" name="listActionCheck" />
										<label for="listAction"><?= esc_html__('List action', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listAction" name="listAction">
											<option value="none" <?= !($settings['listAction'] === 'none') ?: 'selected="selected"' ?>><?= esc_html__('None', 'mmp') ?></option>
											<option value="setview" <?= !($settings['listAction'] === 'setview') ?: 'selected="selected"' ?>><?= esc_html__('Jump to marker', 'mmp') ?></option>
											<option value="setviewzoom" <?= !($settings['listAction'] === 'setviewzoom') ?: 'selected="selected"' ?>><?= esc_html__('Jump to marker and zoom', 'mmp') ?></option>
											<option value="popup" <?= !($settings['listAction'] === 'popup') ?: 'selected="selected"' ?>><?= esc_html__('Open popup', 'mmp') ?></option>
											<option value="popupzoom" <?= !($settings['listAction'] === 'popupzoom') ?: 'selected="selected"' ?>><?= esc_html__('Open popup and zoom', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listSearchCheck" class="batch-settings-check" name="listSearchCheck" />
										<label for="listSearch"><?= esc_html__('Show search and sort', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listSearch" name="listSearch" <?= !$settings['listSearch'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByCheck" class="batch-settings-check" name="listOrderByCheck" />
										<label for="listOrderBy"><?= esc_html__('Default order by', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listOrderBy" name="listOrderBy">
											<option value="id" <?= !($settings['listOrderBy'] === 'id') ?: 'selected="selected"' ?>><?= esc_html__('ID', 'mmp') ?></option>
											<option value="name" <?= !($settings['listOrderBy'] === 'name') ?: 'selected="selected"' ?>><?= esc_html__('Name', 'mmp') ?></option>
											<option value="address" <?= !($settings['listOrderBy'] === 'address') ?: 'selected="selected"' ?>><?= esc_html__('Address', 'mmp') ?></option>
											<option value="distance" <?= !($settings['listOrderBy'] === 'distance') ?: 'selected="selected"' ?>><?= esc_html__('Distance', 'mmp') ?></option>
											<option value="icon" <?= !($settings['listOrderBy'] === 'icon') ?: 'selected="selected"' ?>><?= esc_html__('Icon', 'mmp') ?></option>
											<option value="created_on" <?= !($settings['listOrderBy'] === 'created_on') ?: 'selected="selected"' ?>><?= esc_html__('Created', 'mmp') ?></option>
											<option value="updated_on" <?= !($settings['listOrderBy'] === 'updated_on') ?: 'selected="selected"' ?>><?= esc_html__('Updated', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listSortOrderCheck" class="batch-settings-check" name="listSortOrderCheck" />
										<label for="listSortOrder"><?= esc_html__('Default sort order', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="listSortOrder" name="listSortOrder">
											<option value="asc" <?= !($settings['listSortOrder'] === 'asc') ?: 'selected="selected"' ?>><?= esc_html__('Ascending', 'mmp') ?></option>
											<option value="desc" <?= !($settings['listSortOrder'] === 'desc') ?: 'selected="selected"' ?>><?= esc_html__('Descending', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByIdCheck" class="batch-settings-check" name="listOrderByIdCheck" />
										<label for="listOrderById"><?= esc_html__('ID', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderById" name="listOrderById" <?= !$settings['listOrderById'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByNameCheck" class="batch-settings-check" name="listOrderByNameCheck" />
										<label for="listOrderByName"><?= esc_html__('Name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByName" name="listOrderByName" <?= !$settings['listOrderByName'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByAddressCheck" class="batch-settings-check" name="listOrderByAddressCheck" />
										<label for="listOrderByAddress"><?= esc_html__('Address', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByAddress" name="listOrderByAddress" <?= !$settings['listOrderByAddress'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByDistanceCheck" class="batch-settings-check" name="listOrderByDistanceCheck" />
										<label for="listOrderByDistance"><?= esc_html__('Distance', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByDistance" name="listOrderByDistance" <?= !$settings['listOrderByDistance'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByIconCheck" class="batch-settings-check" name="listOrderByIconCheck" />
										<label for="listOrderByIcon"><?= esc_html__('Icon', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByIcon" name="listOrderByIcon" <?= !$settings['listOrderByIcon'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByCreatedCheck" class="batch-settings-check" name="listOrderByCreatedCheck" />
										<label for="listOrderByCreated"><?= esc_html__('Created', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByCreated" name="listOrderByCreated" <?= !$settings['listOrderByCreated'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listOrderByUpdatedCheck" class="batch-settings-check" name="listOrderByUpdatedCheck" />
										<label for="listOrderByUpdated"><?= esc_html__('Updated', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listOrderByUpdated" name="listOrderByUpdated" <?= !$settings['listOrderByUpdated'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listLocationCheck" class="batch-settings-check" name="listLocationCheck" />
										<label for="listLocation"><?= esc_html__('Show location finder', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listLocation" name="listLocation" <?= !$settings['listLocation'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingZoomCheck" class="batch-settings-check" name="listGeocodingZoomCheck" />
										<label for="listGeocodingZoom"><?= esc_html__('Zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listGeocodingZoom" name="listGeocodingZoom" value="<?= $settings['listGeocodingZoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingDrawCircleCheck" class="batch-settings-check" name="listGeocodingDrawCircleCheck" />
										<label for="listGeocodingDrawCircle"><?= esc_html__('Draw radius', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listGeocodingDrawCircle" name="listGeocodingDrawCircle" <?= !$settings['listGeocodingDrawCircle'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingStrokeCheck" class="batch-settings-check" name="listGeocodingStrokeCheck" />
										<label for="listGeocodingStroke"><?= esc_html__('Stroke', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listGeocodingStroke" name="listGeocodingStroke" <?= !$settings['listGeocodingStroke'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingColorCheck" class="batch-settings-check" name="listGeocodingColorCheck" />
										<label for="listGeocodingColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="listGeocodingColor" name="listGeocodingColor" value="<?= $settings['listGeocodingColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingWeightCheck" class="batch-settings-check" name="listGeocodingWeightCheck" />
										<label for="listGeocodingWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listGeocodingWeight" name="listGeocodingWeight" value="<?= $settings['listGeocodingWeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingFillCheck" class="batch-settings-check" name="listGeocodingFillCheck" />
										<label for="listGeocodingFill"><?= esc_html__('Fill', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="listGeocodingFill" name="listGeocodingFill" <?= !$settings['listGeocodingFill'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingFillColorCheck" class="batch-settings-check" name="listGeocodingFillColorCheck" />
										<label for="listGeocodingFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="listGeocodingFillColor" name="listGeocodingFillColor" value="<?= $settings['listGeocodingFillColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="listGeocodingFillOpacityCheck" class="batch-settings-check" name="listGeocodingFillOpacityCheck" />
										<label for="listGeocodingFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listGeocodingFillOpacity" name="listGeocodingFillOpacity" value="<?= $settings['listGeocodingFillOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<div id="interactionContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gestureHandlingCheck" class="batch-settings-check" name="gestureHandlingCheck" />
										<label for="gestureHandling"><?= esc_html__('Gesture handling', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gestureHandling" name="gestureHandling" <?= !$settings['gestureHandling'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="responsiveCheck" class="batch-settings-check" name="responsiveCheck" />
										<label for="responsive"><?= esc_html__('Responsive map', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="responsive" name="responsive" <?= !$settings['responsive'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="boxZoomCheck" class="batch-settings-check" name="boxZoomCheck" />
										<label for="boxZoom"><?= esc_html__('Box zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="boxZoom" name="boxZoom" <?= !$settings['boxZoom'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="doubleClickZoomCheck" class="batch-settings-check" name="doubleClickZoomCheck" />
										<label for="doubleClickZoom"><?= esc_html__('Double click zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="doubleClickZoom" name="doubleClickZoom" <?= !$settings['doubleClickZoom'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="draggingCheck" class="batch-settings-check" name="draggingCheck" />
										<label for="dragging"><?= esc_html__('Dragging', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="dragging" name="dragging" <?= !$settings['dragging'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="inertiaCheck" class="batch-settings-check" name="inertiaCheck" />
										<label for="inertia"><?= esc_html__('Inertia', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="inertia" name="inertia" <?= !$settings['inertia'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="inertiaDecelerationCheck" class="batch-settings-check" name="inertiaDecelerationCheck" />
										<label for="inertiaDeceleration"><?= esc_html__('Inertia deceleration', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="inertiaDeceleration" name="inertiaDeceleration" value="<?= $settings['inertiaDeceleration'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="inertiaMaxSpeedCheck" class="batch-settings-check" name="inertiaMaxSpeedCheck" />
										<label for="inertiaMaxSpeed"><?= esc_html__('Inertia max speed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="inertiaMaxSpeed" name="inertiaMaxSpeed" value="<?= $settings['inertiaMaxSpeed'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="keyboardCheck" class="batch-settings-check" name="keyboardCheck" />
										<label for="keyboard"><?= esc_html__('Keyboard navigation', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="keyboard" name="keyboard" <?= !$settings['keyboard'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="keyboardPanDeltaCheck" class="batch-settings-check" name="keyboardPanDeltaCheck" />
										<label for="keyboardPanDelta"><?= esc_html__('Keyboard pan delta', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="keyboardPanDelta" name="keyboardPanDelta" value="<?= $settings['keyboardPanDelta'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="scrollWheelZoomCheck" class="batch-settings-check" name="scrollWheelZoomCheck" />
										<label for="scrollWheelZoom"><?= esc_html__('Scroll wheel zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="scrollWheelZoom" name="scrollWheelZoom" <?= !$settings['scrollWheelZoom'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="touchZoomCheck" class="batch-settings-check" name="touchZoomCheck" />
										<label for="touchZoom"><?= esc_html__('Two finger zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="touchZoom" name="touchZoom" <?= !$settings['touchZoom'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="bounceAtZoomLimitsCheck" class="batch-settings-check" name="bounceAtZoomLimitsCheck" />
										<label for="bounceAtZoomLimits"><?= esc_html__('Bounce at zoom limits', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="bounceAtZoomLimits" name="bounceAtZoomLimits" <?= !$settings['bounceAtZoomLimits'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="worldCopyJumpCheck" class="batch-settings-check" name="worldCopyJumpCheck" />
										<label for="worldCopyJump"><?= esc_html__('Move objects to map copies', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="worldCopyJump" name="worldCopyJump" <?= !$settings['worldCopyJump'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="trackContent" class="mmp-map-batch-settings-group">
								<input type="hidden" id="gpxIconTarget" value="" />
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxShowStartIconCheck" class="batch-settings-check" name="gpxShowStartIconCheck" />
										<label for="gpxShowStartIcon"><?= esc_html__('Show start/end icons', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxShowStartIcon" name="gpxShowStartIcon" <?= !$settings['gpxShowStartIcon'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxStartIconCheck" class="batch-settings-check" name="gpxStartIconCheck" />
										<?= esc_html__('Start icon', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<input type="hidden" id="gpxStartIcon" name="gpxStartIcon" value="<?= $settings['gpxStartIcon'] ?>" />
										<img class="mmp-gpx-start-icon mmp-align-middle" src="<?= (!$settings['gpxStartIcon']) ? plugins_url('images/leaflet/gpx-start.png', MMP::$path) : MMP::$icons_url . $settings['gpxStartIcon'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxShowEndIconCheck" class="batch-settings-check" name="gpxShowEndIconCheck" />
										<label for="gpxShowEndIcon"><?= esc_html__('Show start/end icons', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxShowEndIcon" name="gpxShowEndIcon" <?= !$settings['gpxShowEndIcon'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxEndIconCheck" class="batch-settings-check" name="gpxEndIconCheck" />
										<?= esc_html__('End icon', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<input type="hidden" id="gpxEndIcon" name="gpxEndIcon" value="<?= $settings['gpxEndIcon'] ?>" />
										<img class="mmp-gpx-end-icon mmp-align-middle" src="<?= (!$settings['gpxEndIcon']) ? plugins_url('images/leaflet/gpx-end.png', MMP::$path) : MMP::$icons_url . $settings['gpxEndIcon'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxTrackSmoothFactorCheck" class="batch-settings-check" name="gpxTrackSmoothFactorCheck" />
										<label for="gpxTrackSmoothFactor"><?= esc_html__('Track smooth factor', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxTrackSmoothFactor" name="gpxTrackSmoothFactor" value="<?= $settings['gpxTrackSmoothFactor'] ?>" min="0" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxTrackColorCheck" class="batch-settings-check" name="gpxTrackColorCheck" />
										<label for="gpxTrackColor"><?= esc_html__('Track color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxTrackColor" name="gpxTrackColor" value="<?= $settings['gpxTrackColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxTrackWeightCheck" class="batch-settings-check" name="gpxTrackWeightCheck" />
										<label for="gpxTrackWeight"><?= esc_html__('Track weight', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxTrackWeight" name="gpxTrackWeight" value="<?= $settings['gpxTrackWeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxTrackOpacityCheck" class="batch-settings-check" name="gpxTrackOpacityCheck" />
										<label for="gpxTrackOpacity"><?= esc_html__('Track opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxTrackOpacity" name="gpxTrackOpacity" value="<?= $settings['gpxTrackOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<div id="metadataContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaCheck" class="batch-settings-check" name="gpxMetaCheck" />
										<label for="gpxMeta"><?= esc_html__('Add popup to track', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMeta" name="gpxMeta" <?= !$settings['gpxMeta'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaUnitsCheck" class="batch-settings-check" name="gpxMetaUnitsCheck" />
										<label for="gpxMetaUnits"><?= esc_html__('Units', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="gpxMetaUnits" name="gpxMetaUnits">
											<option value="metric" <?= !($settings['gpxMetaUnits'] === 'metric') ?: 'selected="selected"' ?>><?= esc_html__('Metric', 'mmp') ?></option>
											<option value="imperial" <?= !($settings['gpxMetaUnits'] === 'imperial') ?: 'selected="selected"' ?>><?= esc_html__('Imperial', 'mmp') ?></option>
											<option value="metric-imperial" <?= !($settings['gpxMetaUnits'] === 'metric-imperial') ?: 'selected="selected"' ?>><?= esc_html__('Metric (imperial)', 'mmp') ?></option>
											<option value="imperial-metric" <?= !($settings['gpxMetaUnits'] === 'imperial-metric') ?: 'selected="selected"' ?>><?= esc_html__('Imperial (metric)', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaIntervalCheck" class="batch-settings-check" name="gpxMetaIntervalCheck" />
										<label for="gpxMetaInterval"><?= esc_html__('Max interval', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxMetaInterval" name="gpxMetaInterval" value="<?= $settings['gpxMetaInterval'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaNameCheck" class="batch-settings-check" name="gpxMetaNameCheck" />
										<label for="gpxMetaName"><?= esc_html__('Name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaName" name="gpxMetaName" <?= !$settings['gpxMetaName'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaDescCheck" class="batch-settings-check" name="gpxMetaDescCheck" />
										<label for="gpxMetaDesc"><?= esc_html__('Description', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaDesc" name="gpxMetaDesc" <?= !$settings['gpxMetaDesc'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaStartCheck" class="batch-settings-check" name="gpxMetaStartCheck" />
										<label for="gpxMetaStart"><?= esc_html__('Start', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaStart" name="gpxMetaStart" <?= !$settings['gpxMetaStart'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaEndCheck" class="batch-settings-check" name="gpxMetaEndCheck" />
										<label for="gpxMetaEnd"><?= esc_html__('End', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaEnd" name="gpxMetaEnd" <?= !$settings['gpxMetaEnd'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaTotalCheck" class="batch-settings-check" name="gpxMetaTotalCheck" />
										<label for="gpxMetaTotal"><?= esc_html__('Total', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaTotal" name="gpxMetaTotal" <?= !$settings['gpxMetaTotal'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaMovingCheck" class="batch-settings-check" name="gpxMetaMovingCheck" />
										<label for="gpxMetaMoving"><?= esc_html__('Moving', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaMoving" name="gpxMetaMoving" <?= !$settings['gpxMetaMoving'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaDistanceCheck" class="batch-settings-check" name="gpxMetaDistanceCheck" />
										<label for="gpxMetaDistance"><?= esc_html__('Distance', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaDistance" name="gpxMetaDistance" <?= !$settings['gpxMetaDistance'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaPaceCheck" class="batch-settings-check" name="gpxMetaPaceCheck" />
										<label for="gpxMetaPace"><?= esc_html__('Pace', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaPace" name="gpxMetaPace" <?= !$settings['gpxMetaPace'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaHeartRateCheck" class="batch-settings-check" name="gpxMetaHeartRateCheck" />
										<label for="gpxMetaHeartRate"><?= esc_html__('Heart rate', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaHeartRate" name="gpxMetaHeartRate" <?= !$settings['gpxMetaHeartRate'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaElevationCheck" class="batch-settings-check" name="gpxMetaElevationCheck" />
										<label for="gpxMetaElevation"><?= esc_html__('Elevation', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaElevation" name="gpxMetaElevation" <?= !$settings['gpxMetaElevation'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaDownloadCheck" class="batch-settings-check" name="gpxMetaDownloadCheck" />
										<label for="gpxMetaDownload"><?= esc_html__('Download', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaDownload" name="gpxMetaDownload" <?= !$settings['gpxMetaDownload'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxMetaHideMissingCheck" class="batch-settings-check" name="gpxMetaHideMissingCheck" />
										<label for="gpxMetaHideMissing"><?= esc_html__('Hide fields with no value', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxMetaHideMissing" name="gpxMetaHideMissing" <?= !$settings['gpxMetaHideMissing'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
							</div>
							<div id="waypointsContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsCheck" class="batch-settings-check" name="gpxWaypointsCheck" />
										<label for="gpxWaypoints"><?= esc_html__('Show', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxWaypoints" name="gpxWaypoints" <?= !$settings['gpxWaypoints'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsRadiusCheck" class="batch-settings-check" name="gpxWaypointsRadiusCheck" />
										<label for="gpxWaypointsRadius"><?= esc_html__('Waypoints radius', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxWaypointsRadius" name="gpxWaypointsRadius" value="<?= $settings['gpxWaypointsRadius'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsStrokeCheck" class="batch-settings-check" name="gpxWaypointsStrokeCheck" />
										<label for="gpxWaypointsStroke"><?= esc_html__('Stroke', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxWaypointsStroke" name="gpxWaypointsStroke" <?= !$settings['gpxWaypointsStroke'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsColorCheck" class="batch-settings-check" name="gpxWaypointsColorCheck" />
										<label for="gpxWaypointsColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxWaypointsColor" name="gpxWaypointsColor" value="<?= $settings['gpxWaypointsColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsWeightCheck" class="batch-settings-check" name="gpxWaypointsWeightCheck" />
										<label for="gpxWaypointsWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxWaypointsWeight" name="gpxWaypointsWeight" value="<?= $settings['gpxWaypointsWeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsFillColorCheck" class="batch-settings-check" name="gpxWaypointsFillColorCheck" />
										<label for="gpxWaypointsFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxWaypointsFillColor" name="gpxWaypointsFillColor" value="<?= $settings['gpxWaypointsFillColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxWaypointsFillOpacityCheck" class="batch-settings-check" name="gpxWaypointsFillOpacityCheck" />
										<label for="gpxWaypointsFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxWaypointsFillOpacity" name="gpxWaypointsFillOpacity" value="<?= $settings['gpxWaypointsFillOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<div id="elevationChartContent" class="mmp-map-batch-settings-group">
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartCheck" class="batch-settings-check" name="gpxChartCheck" />
										<label for="gpxChart"><?= esc_html__('Show', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChart" name="gpxChart" <?= !$settings['gpxChart'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartUnitsCheck" class="batch-settings-check" name="gpxChartUnitsCheck" />
										<label for="gpxChartUnits"><?= esc_html__('Units', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="gpxChartUnits" name="gpxChartUnits">
											<option value="metric" <?= !($settings['gpxChartUnits'] === 'metric') ?: 'selected="selected"' ?>><?= esc_html__('Metric', 'mmp') ?></option>
											<option value="imperial" <?= !($settings['gpxChartUnits'] === 'imperial') ?: 'selected="selected"' ?>><?= esc_html__('Imperial', 'mmp') ?></option>
											<option value="metric-imperial" <?= !($settings['gpxChartUnits'] === 'metric-imperial') ?: 'selected="selected"' ?>><?= esc_html__('Metric (imperial)', 'mmp') ?></option>
											<option value="imperial-metric" <?= !($settings['gpxChartUnits'] === 'imperial-metric') ?: 'selected="selected"' ?>><?= esc_html__('Imperial (metric)', 'mmp') ?></option>
										</select>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartHeightCheck" class="batch-settings-check" name="gpxChartHeightCheck" />
										<label for="gpxChartHeight"><?= esc_html__('Height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxChartHeight" name="gpxChartHeight" value="<?= $settings['gpxChartHeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartReverseXCheck" class="batch-settings-check" name="gpxChartReverseXCheck" />
										<label for="gpxChartReverseX"><?= esc_html__('Reverse X-axis', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChartReverseX" name="gpxChartReverseX" <?= !$settings['gpxChartReverseX'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartReverseYCheck" class="batch-settings-check" name="gpxChartReverseYCheck" />
										<label for="gpxChartReverseY"><?= esc_html__('Reverse Y-axis', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChartReverseY" name="gpxChartReverseY" <?= !$settings['gpxChartReverseY'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartYMinCheck" class="batch-settings-check" name="gpxChartYMinCheck" />
										<label for="gpxChartYMin"><?= esc_html__('Y-axis min value', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartYMin" name="gpxChartYMin" value="<?= $settings['gpxChartYMin'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartYMaxCheck" class="batch-settings-check" name="gpxChartYMaxCheck" />
										<label for="gpxChartYMax"><?= esc_html__('Y-axis max value', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartYMax" name="gpxChartYMax" value="<?= $settings['gpxChartYMax'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartYOffsetCheck" class="batch-settings-check" name="gpxChartYOffsetCheck" />
										<label for="gpxChartYOffset"><?= esc_html__('Y-axis offset value', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartYOffset" name="gpxChartYOffset" value="<?= $settings['gpxChartYOffset'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<input type="checkbox" id="gpxChartLineTensionCheck" class="batch-settings-check" name="gpxChartLineTensionCheck" />
											<label for="gpxChartLineTension"><?= esc_html__('Line tension', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLineTension" name="gpxChartLineTension" value="<?= $settings['gpxChartLineTension'] ?>" min="0" step="0.01" />
										</div>
									</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartBgColorCheck" class="batch-settings-check" name="gpxChartBgColorCheck" />
										<label for="gpxChartBgColor"><?= esc_html__('Background color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartBgColor" name="gpxChartBgColor" value="<?= $settings['gpxChartBgColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartGridLinesColorCheck" class="batch-settings-check" name="gpxChartGridLinesColorCheck" />
										<label for="gpxChartGridLinesColor"><?= esc_html__('Grid lines color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartGridLinesColor" name="gpxChartGridLinesColor" value="<?= $settings['gpxChartGridLinesColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartTicksFontColorCheck" class="batch-settings-check" name="gpxChartTicksFontColorCheck" />
										<label for="gpxChartTicksFontColor"><?= esc_html__('Ticks font color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartTicksFontColor" name="gpxChartTicksFontColor" value="<?= $settings['gpxChartTicksFontColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLineWidthCheck" class="batch-settings-check" name="gpxChartLineWidthCheck" />
										<label for="gpxChartLineWidth"><?= esc_html__('Line width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxChartLineWidth" name="gpxChartLineWidth" value="<?= $settings['gpxChartLineWidth'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLineColorCheck" class="batch-settings-check" name="gpxChartLineColorCheck" />
										<label for="gpxChartLineColor"><?= esc_html__('Line color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartLineColor" name="gpxChartLineColor" value="<?= $settings['gpxChartLineColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartFillCheck" class="batch-settings-check" name="gpxChartFillCheck" />
										<label for="gpxChartFill"><?= esc_html__('Fill', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChartFill" name="gpxChartFill" <?= !$settings['gpxChartFill'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartFillColorCheck" class="batch-settings-check" name="gpxChartFillColorCheck" />
										<label for="gpxChartFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartFillColor" name="gpxChartFillColor" value="<?= $settings['gpxChartFillColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartTooltipBgColorCheck" class="batch-settings-check" name="gpxChartTooltipBgColorCheck" />
										<label for="gpxChartTooltipBgColor"><?= esc_html__('Tooltip background color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartTooltipBgColor" name="gpxChartTooltipBgColor" value="<?= $settings['gpxChartTooltipBgColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartTooltipFontColorCheck" class="batch-settings-check" name="gpxChartTooltipFontColorCheck" />
										<label for="gpxChartTooltipFontColor"><?= esc_html__('Tooltip font color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartTooltipFontColor" name="gpxChartTooltipFontColor" value="<?= $settings['gpxChartTooltipFontColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorCheck" class="batch-settings-check" name="gpxChartLocatorCheck" />
										<label for="gpxChartLocator"><?= esc_html__('Locator', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChartLocator" name="gpxChartLocator" <?= !$settings['gpxChartLocator'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorRadiusCheck" class="batch-settings-check" name="gpxChartLocatorRadiusCheck" />
										<label for="gpxChartLocatorRadius"><?= esc_html__('Locator radius', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxChartLocatorRadius" name="gpxChartLocatorRadius" value="<?= $settings['gpxChartLocatorRadius'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorStrokeCheck" class="batch-settings-check" name="gpxChartLocatorStrokeCheck" />
										<label for="gpxChartLocatorStroke"><?= esc_html__('Locator stroke', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="gpxChartLocatorStroke" name="gpxChartLocatorStroke" <?= !$settings['gpxChartLocatorStroke'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label>
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorColorCheck" class="batch-settings-check" name="gpxChartLocatorColorCheck" />
										<label for="gpxChartLocatorColor"><?= esc_html__('Locator stroke color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartLocatorColor" name="gpxChartLocatorColor" value="<?= $settings['gpxChartLocatorColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorWeightCheck" class="batch-settings-check" name="gpxChartLocatorWeightCheck" />
										<label for="gpxChartLocatorWeight"><?= esc_html__('Locator stroke weight', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxChartLocatorWeight" name="gpxChartLocatorWeight" value="<?= $settings['gpxChartLocatorWeight'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorFillColorCheck" class="batch-settings-check" name="gpxChartLocatorFillColorCheck" />
										<label for="gpxChartLocatorFillColor"><?= esc_html__('Locator fill color', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxChartLocatorFillColor" name="gpxChartLocatorFillColor" value="<?= $settings['gpxChartLocatorFillColor'] ?>" />
									</div>
								</div>
								<div class="mmp-map-batch-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<input type="checkbox" id="gpxChartLocatorFillOpacityCheck" class="batch-settings-check" name="gpxChartLocatorFillOpacityCheck" />
										<label for="gpxChartLocatorFillOpacity"><?= esc_html__('Locator fill opacity', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="gpxChartLocatorFillOpacity" name="gpxChartLocatorFillOpacity" value="<?= $settings['gpxChartLocatorFillOpacity'] ?>" min="0" max="1" step="0.01" />
									</div>
								</div>
							</div>
							<label><input name="batch_settings_mode" type="radio" value="all" checked="checked" /> <?= esc_html__('Apply settings to all maps', 'mmp') ?></label><br />
							<label><input name="batch_settings_mode" type="radio" value="include" /> <?= esc_html__('Apply settings to these maps', 'mmp') ?></label><br />
							<select id="batch_settings_maps" name="batch_settings_maps[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<button type="button" id="save_batch_settings" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-batch-settings') ?>"><?= esc_html__('Save', 'mmp') ?></button>
						</form>
					</div>
				</div>
				<div id="batch_layers_section" class="mmp-tools-section">
					<h2><?= esc_html__('Batch update layers', 'mmp') ?></h2>
					<div>
						<p><?= esc_html__('Please note that the selected basemaps and overlays will completely replace the current settings on the respective maps.', 'mmp') ?></p>
						<form id="mapLayers" method="POST">
							<ul id="basemapList"></ul>
							<select id="basemapsList">
								<?php foreach ($basemaps as $bid => $basemaps): ?>
									<option value="<?= $bid ?>"><?= esc_html($basemaps['name']) ?></option>
								<?php endforeach; ?>
							</select><br />
							<button type="button" id="basemapsAdd" class="button button-secondary"><?= esc_html__('Add basemap', 'mmp') ?></button><br />
							<ul id="overlayList"></ul>
							<select id="overlaysList">
								<?php foreach ($overlays as $oid => $overlays): ?>
									<option value="<?= $oid ?>"><?= esc_html($overlays['name']) ?></option>
								<?php endforeach; ?>
							</select><br />
							<button type="button" id="overlaysAdd" class="button button-secondary"><?= esc_html__('Add overlay', 'mmp') ?></button><br />
							<label><input name="batch_layers_mode" type="radio" value="all" checked="checked" /> <?= esc_html__('Apply settings to all maps', 'mmp') ?></label><br />
							<label><input name="batch_layers_mode" type="radio" value="include" /> <?= esc_html__('Apply settings to these maps', 'mmp') ?></label><br />
							<select id="batch_layers_maps" name="batch_layers_maps[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<button type="button" id="save_batch_layers" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-batch-layers') ?>"><?= esc_html__('Save', 'mmp') ?></button>
						</form>
					</div>
				</div>
				<div id="batch_marker_section" class="mmp-tools-section">
					<h2><?= esc_html__('Batch update markers', 'mmp') ?></h2>
					<div>
						<form id="markerSettings" method="POST">
							<div class="mmp-map-batch-setting">
								<div class="mmp-map-setting-desc">
									<input type="checkbox" id="markerZoomCheck" class="batch-settings-check" name="markerZoomCheck" />
									<label for="markerZoom"><?= esc_html__('Zoom', 'mmp') ?></label>
								</div>
								<div class="mmp-map-setting-input">
									<input type="number" id="markerZoom" name="markerZoom" value="10" min="0" max="23" step="0.1" />
								</div>
							</div>
							<div class="mmp-map-batch-setting">
								<div class="mmp-map-setting-desc">
									<input type="checkbox" id="markerIconCheck" class="batch-settings-check" name="markerIconCheck" />
									<?= esc_html__('Icon', 'mmp') ?>
								</div>
								<div class="mmp-map-setting-input">
									<input type="hidden" id="markerIcon" name="markerIcon" value="" />
									<img id="markerIconSelection" class="mmp-align-middle" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" />
								</div>
							</div>
							<div class="mmp-map-batch-setting">
								<div class="mmp-map-setting-desc">
									<input type="checkbox" id="markerBlankCheck" class="batch-settings-check" name="markerBlankCheck" />
									<label for="markerBlank"><?= esc_html__('Link target', 'mmp') ?></label>
								</div>
								<div class="mmp-map-setting-input">
									<label><input type="radio" name="markerBlank" value="0" /> <?= esc_html__('Same tab', 'mmp') ?></label>
									<label><input type="radio" name="markerBlank" value="1" checked="checked" /> <?= esc_html__('New tab', 'mmp') ?></label>
								</div>
							</div>
							<label><input name="batch_marker_mode" type="radio" value="all" checked="checked" /> <?= esc_html__('Apply settings to all markers', 'mmp') ?></label><br />
							<label><input name="batch_marker_mode" type="radio" value="markers" /> <?= esc_html__('Apply settings to these markers', 'mmp') ?></label><br />
							<select id="batch_marker_markers" name="batch_marker_markers[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-markers') ?>" multiple="multiple"></select><br />
							<label><input name="batch_marker_mode" type="radio" value="maps" /> <?= esc_html__('Apply settings to all markers assigned to these maps (will not apply to markers added via filters)', 'mmp') ?></label><br />
							<select id="batch_marker_maps" name="batch_marker_maps[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<button type="button" id="save_batch_marker" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-batch-marker') ?>"><?= esc_html__('Save', 'mmp') ?></button>
						</form>
					</div>
				</div>
				<div id="replace_icon_section" class="mmp-tools-section">
					<h2><?= esc_html__('Replace marker icons', 'mmp') ?></h2>
					<div>
						<input type="hidden" id="replaceIcon" value="0" />
						<?= esc_html__('Icon to replace', 'mmp') ?>: <img id="sourceIcon" class="mmp-align-middle" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" /><br />
						<?= esc_html__('Replacement icon', 'mmp') ?>: <img id="targetIcon" class="mmp-align-middle" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" /><br />
						<button type="button" id="replace_icon" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-replace-icon') ?>"><?= esc_html__('Replace', 'mmp') ?></button>
					</div>
				</div>
				<div id="move_markers_section" class="mmp-tools-section">
					<h2><?= esc_html__('Move markers to a map', 'mmp') ?></h2>
					<div>
						<label>
							<?= esc_html__('Source', 'mmp') ?>
							<select id="move_markers_source" name="move_markers_source" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>"></select>
						</label><br />
						<label>
							<?= esc_html__('Target', 'mmp') ?>
							<select id="move_markers_target" name="move_markers_target" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>"></select>
						</label><br />
						<button type="button" id="move_markers" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-move-markers') ?>"><?= esc_html__('Move markers', 'mmp') ?></button>
					</div>
				</div>
				<div id="remove_markers_section" class="mmp-tools-section">
					<h2><?= esc_html__('Remove all markers from a map', 'mmp') ?></h2>
					<div>
						<p><?= esc_html__('Note: markers are only unassigned, but not deleted.', 'mmp') ?></p>
						<label>
							<?= esc_html__('Source', 'mmp') ?>
							<select id="remove_markers_map" name="remove_markers_map" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>"></select>
						</label><br />
						<button type="button" id="remove_markers" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-remove-markers') ?>"><?= esc_html__('Remove markers', 'mmp') ?></button>
					</div>
				</div>
				<div id="assign_markers_section" class="mmp-tools-section">
					<h2><?= esc_html__('Assign all unassigned markers to a map', 'mmp') ?></h2>
					<div>
						<p><?= sprintf(esc_html__('There are %1$s unassigned markers.', 'mmp'), $unassigned_count) ?></p>
						<label>
							<?= esc_html__('Target', 'mmp') ?>
							<select id="assign_markers_map" name="assign_markers_map" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>"></select>
						</label><br />
						<button type="button" id="assign_markers" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-assign-markers') ?>"><?= esc_html__('Assign markers', 'mmp') ?></button>
					</div>
				</div>
				<div id="delete_all_maps_section" class="mmp-tools-section">
					<h2><?= esc_html__('Delete all maps', 'mmp') ?></h2>
					<div>
						<label><input type="checkbox" id="delete_all_maps_reset_ids" name="delete_all_maps_reset_ids" /><?= esc_html__('Reset IDs (new maps will start with ID 1)', 'mmp') ?></label>
						<p class="mmp-warning"><?= esc_html__('WARNING: this cannot be undone.', 'mmp') ?></p>
						<button type="button" id="delete_all_maps" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-delete-all-maps') ?>" disabled="disabled"><?= esc_html__('Delete all maps', 'mmp') ?></button>
						<label><input type="checkbox" id="delete_all_maps_confirm" name="delete_all_maps_confirm" /><?= esc_html__('Are you sure?', 'mmp') ?></label>
					</div>
				</div>
				<div id="delete_all_markers_section" class="mmp-tools-section">
					<h2><?= esc_html__('Delete all markers', 'mmp') ?></h2>
					<div>
						<label><input type="checkbox" id="delete_all_markers_reset_ids" name="delete_all_markers_reset_ids" /><?= esc_html__('Reset IDs (new markers will start with ID 1)', 'mmp') ?></label>
						<p class="mmp-warning"><?= esc_html__('WARNING: this cannot be undone.', 'mmp') ?></p>
						<button type="button" id="delete_all_markers" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-delete-all-markers') ?>" disabled="disabled"><?= esc_html__('Delete all markers', 'mmp') ?></button>
						<label><input type="checkbox" id="delete_all_markers_confirm" name="delete_all_markers_confirm" /><?= esc_html__('Are you sure?', 'mmp') ?></label>
					</div>
				</div>
				<div id="register_strings_section" class="mmp-tools-section">
					<h2><?= esc_html__('Register strings for translation', 'mmp') ?></h2>
					<div>
						<?php if (!$l10n->check_ml()): ?>
							<p><a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('No supported multilingual plugin installed.', 'mmp') ?></a></p>
						<?php else: ?>
							<button type="button" id="register_strings" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-register-strings') ?>"><?= esc_html__('Register strings', 'mmp') ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div id="import_tab_content" class="mmp-tools-tab">
				<div id="import_markers" class="mmp-tools-section">
					<h2><?= esc_html__('Import markers', 'mmp') ?></h2>
					<div>
						<p>
							<?= esc_html__('Please note that only files with UTF-8 encoding are currently supported.', 'mmp') ?><br />
							<?= sprintf(esc_html__('For more details about the import feature, file requirements and a tutorial on how to convert between formats, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/import-export/" target="_blank">https://www.mapsmarker.com/import-export/</a>') ?>
						</p>
						<form id="import_form" method="POST">
							<input name="action" type="hidden" value="mmp_import" />
							<input name="nonce" type="hidden" value="<?= wp_create_nonce('mmp-tools-import') ?>" />
							<div id="import_log" class="mmp-log"></div>
							<?= esc_html__('File type', 'mmp') ?>:<br />
							<label><input name="file_type" type="radio" value="csv" checked="checked" /> <?= esc_html__('CSV', 'mmp') ?></label><br />
							<label><input name="file_type" type="radio" value="geojson" /> <?= esc_html__('GeoJSON', 'mmp') ?></label><br />
							<?= esc_html__('Test mode', 'mmp') ?>:<br />
							<label><input name="test_mode" type="radio" value="on" checked="checked" /> <?= esc_html__('On', 'mmp') ?></label><br />
							<label><input name="test_mode" type="radio" value="off" /> <?= esc_html__('Off', 'mmp') ?></label><br />
							<?= esc_html__('Marker mode', 'mmp') ?>:<br />
							<label><input name="marker_mode" type="radio" value="add" checked="checked" /> <?= esc_html__('Add markers', 'mmp') ?></label><br />
							<label><input name="marker_mode" type="radio" value="update" /> <?= esc_html__('Update markers', 'mmp') ?></label><br />
							<label><input name="marker_mode" type="radio" value="both" /> <?= esc_html__('Update existing, add remaining', 'mmp') ?></label><br />
							<?= esc_html__('Geocoding', 'mmp') ?>:<br />
							<label><input name="geocoding" type="radio" value="off" checked="checked" /> <?= esc_html__('Off (markers with missing coordinates will be skipped)', 'mmp') ?></label><br />
							<?php if (MMP::$settings['geocodingLocationIqApiKey'] || MMP::$settings['geocodingMapQuestApiKey'] || MMP::$settings['geocodingGoogleApiKey'] || MMP::$settings['geocodingTomTomApiKey']): ?>
								<label><input name="geocoding" type="radio" value="missing" /> <?= esc_html__('Missing (markers with missing coordinates will be geocoded)', 'mmp') ?></label><br />
								<label><input name="geocoding" type="radio" value="on" /> <?= esc_html__('On (all markers will be geocoded)', 'mmp') ?></label><br />
								<label><select name="geocoding_provider">
									<optgroup label="<?= esc_attr__('Available providers', 'mmp') ?>">
										<?php if (MMP::$settings['geocodingLocationIqApiKey']): ?>
											<option value="locationiq" <?= MMP::$settings['geocodingProvider'] !== 'locationiq' ?: 'selected="selected"' ?>>LocationIQ</option>
										<?php endif; ?>
										<?php if (MMP::$settings['geocodingMapQuestApiKey']): ?>
											<option value="mapquest" <?= MMP::$settings['geocodingProvider'] !== 'mapquest' ?: 'selected="selected"' ?>>MapQuest</option>
										<?php endif; ?>
										<?php if (MMP::$settings['geocodingGoogleApiKey']): ?>
											<option value="google" <?= MMP::$settings['geocodingProvider'] !== 'google' ?: 'selected="selected"' ?>>Google</option>
										<?php endif; ?>
										<?php if (MMP::$settings['geocodingTomTomApiKey']): ?>
											<option value="tomtom" <?= MMP::$settings['geocodingProvider'] !== 'tomtom' ?: 'selected="selected"' ?>>TomTom</option>
										<?php endif; ?>
									</optgroup>
									<?php if (!MMP::$settings['geocodingLocationIqApiKey'] || !MMP::$settings['geocodingMapQuestApiKey'] || !MMP::$settings['geocodingGoogleApiKey'] || !MMP::$settings['geocodingTomTomApiKey']): ?>
										<optgroup label="<?= esc_attr__('Inactive (API key required)', 'mmp') ?>">
											<?php if (!MMP::$settings['geocodingLocationIqApiKey']): ?>
												<option value="locationiq" disabled="disabled">LocationIQ</option>
											<?php endif; ?>
											<?php if (!MMP::$settings['geocodingMapQuestApiKey']): ?>
												<option value="mapquest" disabled="disabled">MapQuest</option>
											<?php endif; ?>
											<?php if (!MMP::$settings['geocodingGoogleApiKey']): ?>
												<option value="google" disabled="disabled">Google</option>
											<?php endif; ?>
											<?php if (!MMP::$settings['geocodingTomTomApiKey']): ?>
												<option value="tomtom" disabled="disabled">TomTom</option>
											<?php endif; ?>
										</optgroup>
									<?php endif; ?>
								</select></label><br />
							<?php else: ?>
								<span class="mmp-warning"><?= sprintf($l10n->kses__('To use the geocoding feature, please activate one or more providers in the <a href="%1$s" target="_blank">geocoding settings</a>', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider')) ?></span><br />
							<?php endif; ?>
							<?= esc_html__('Assignments', 'mmp') ?>:<br />
							<label><input name="assignments" type="radio" value="off" checked="checked" /> <?= esc_html__('Off (no changes to marker assignments)', 'mmp') ?></label><br />
							<label><input name="assignments" type="radio" value="on" /> <?= esc_html__('On (imported markers will be assigned or unassigned)', 'mmp') ?></label><br />
							<?= esc_html__('Assignment mode', 'mmp') ?>:<br />
							<label><input name="assign_mode" type="radio" value="file" checked="checked" /> <?= esc_html__('File (all markers will be assigned according to their assignment info in the file)', 'mmp') ?></label><br />
							<label><input name="assign_mode" type="radio" value="missing" /> <?= esc_html__('Missing (markers with missing assignment info will be assigned to the selected maps)', 'mmp') ?></label><br />
							<label><input name="assign_mode" type="radio" value="fixed" /> <?= esc_html__('Fixed (all markers will be assigned to the selected maps, even if they have assignment info in the file)', 'mmp') ?></label><br />
							<select id="assign_maps" name="assign_maps[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<div id="test_mode_wrap" class="mmp-test-mode-on">
								<p id="test_mode_status"><?= esc_html__('Test mode on - no changes will be made to the database.', 'mmp') ?></p>
								<button id="import_start" class="button button-primary" disabled="disabled"><?= esc_html__('Start import', 'mmp') ?></button>
								<input id="import_file" name="file" type="file" />
								<input id="import_max_size" type="hidden" value="<?= $upload->get_max_upload_size(); ?>" />
								<span id="import_filesize_error">(<?= esc_html__('Maximum upload size exceeded', 'mmp') ?>)</span>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div id="export_tab_content" class="mmp-tools-tab">
				<div id="export_markers" class="mmp-tools-section">
					<h2><?= esc_html__('Export markers', 'mmp') ?></h2>
					<div>
						<p>
							<?= sprintf(esc_html__('For more details about the export feature and a tutorial on how to convert the output file into other formats, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/import-export/" target="_blank">https://www.mapsmarker.com/import-export/</a>') ?>
						</p>
						<form id="export_form" method="POST">
							<input name="action" type="hidden" value="mmp_export" />
							<input name="nonce" type="hidden" value="<?= wp_create_nonce('mmp-tools-export') ?>" />
							<div id="export_log" class="mmp-log"></div>
							<?= esc_html__('File type', 'mmp') ?>:<br />
							<label><input name="file_type" type="radio" value="csv" checked="checked" /> <?= esc_html__('CSV', 'mmp') ?></label><br />
							<label><input name="file_type" type="radio" value="geojson" /> <?= esc_html__('GeoJSON', 'mmp') ?></label><br />
							<?= esc_html__('Filter mode', 'mmp') ?>:<br />
							<label><input name="filter_mode" type="radio" value="all" checked="checked" /> <?= esc_html__('All markers', 'mmp') ?></label><br />
							<label><input name="filter_mode" type="radio" value="include" /> <?= esc_html__('Only markers from these maps', 'mmp') ?></label><br />
							<select id="export_include" name="filter_include[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<label><input name="filter_mode" type="radio" value="exclude" /> <?= esc_html__('All markers except from these maps', 'mmp') ?></label><br />
							<select id="export_exclude" name="filter_exclude[]" data-nonce="<?= wp_create_nonce('mmp-tools-query-maps') ?>" multiple="multiple"></select><br />
							<button id="export_start" class="button button-primary"><?= esc_html__('Start export', 'mmp') ?></button>
						</form>
					</div>
				</div>
			</div>
			<div id="backup_restore_tab_content" class="mmp-tools-tab">
				<div id="backup_restore_section" class="mmp-tools-section">
					<h2><?= esc_html__('Backup or restore database', 'mmp') ?></h2>
					<div>
						<p>
							<?= esc_html__('This includes custom layers, maps, markers and relationships. Settings need to be backed up separately.', 'mmp') ?>
						</p>
						<div id="backup_log" class="mmp-log"></div>
						<div class="mmp-progress-bar">
							<div id="backup_progress" class="mmp-progress-bar-fill"></div>
						</div>
						<button id="backup_start" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-backup') ?>"><?= esc_html__('Start backup', 'mmp') ?></button>
						<div class="mmp-restore">
							<p class="mmp-warning"><?= esc_html__('WARNING: If you restore a backup, the entire Maps Marker Pro database will be wiped and replaced with the data from the file. This cannot be undone.', 'mmp') ?></p>
							<button id="restore_start" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-restore-backup') ?>" disabled="disabled"><?= esc_html__('Restore backup', 'mmp') ?></button>
							<input id="backup_max_size" type="hidden" name="MAX_FILE_SIZE" value="<?= $upload->get_max_upload_size(); ?>" />
							<input id="backup_file" name="file" type="file" />
						</div>
					</div>
				</div>
				<div id="update_settings_section" class="mmp-tools-section">
					<h2><?= esc_html__('Backup or restore settings', 'mmp') ?></h2>
					<div>
						<p>
							<?= esc_html__('Below are your current settings, encoded in JSON. Use copy and paste to create or restore a backup.', 'mmp') ?><br/>
							<?= sprintf(esc_html__('Please be aware that restoring settings from a version other than %1$s will result in settings that have been added, changed or removed in this version to revert to their default values.', 'mmp'), MMP::$version) ?><br />
							<?= sprintf($l10n->kses__('In case of any issues, you can always <a href="%1$s">reset the plugin settings</a>.', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#reset')) ?>
						</p>
						<textarea id="settings" class="mmp-tools-settings" name="settings"><?= json_encode(MMP::$settings) ?></textarea><br />
						<button type="button" id="update_settings" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-update-settings') ?>"><?= esc_html__('Update settings', 'mmp') ?></button>
						<?php if (is_multisite() && is_super_admin()): ?>
							<label><input type="checkbox" id="update_settings_multisite" name="update_settings_multisite" /><?= esc_html__('Multisite-only: also update settings on all subsites', 'mmp') ?></label>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php if ($old_version !== false): ?>
				<div id="migration_tab_content" class="mmp-tools-tab">
					<div id="data_migration" class="mmp-tools-section">
						<h2><?= esc_html__('Maps Marker Pro 3.1.1 data migration', 'mmp') ?></h2>
						<div>
							<?php if ($old_version === '3.1.1'): ?>
								<p><?= esc_html__('Maps Marker Pro 4.0 was completely rewritten from scratch and received a new database structure. As a result, existing data needs to be migrated. To make this as risk-free as possible, the plugin folder was renamed (which means it is considered a different plugin by WordPress) and a new database was created. This allows you to easily go back to the old plugin, should you encounter any issues, by simply deactivating this version and reactivating Maps Marker 3.1.1.', 'mmp') ?></p>
								<p class="mmp-warning"><?= esc_html__('Warning: If you migrate your data, any maps or markers created with Maps Marker Pro 4.0 will be deleted and replaced with the Maps Marker Pro 3.1.1 data (which will remain unchanged). This cannot be undone.', 'mmp') ?></p>
								<p><?= sprintf(esc_html__('Please do not delete Maps Marker Pro 3.1.1 until you have verified that all data has been migrated correctly. We also recommend to make a backup of the %1$s and %2$s database tables, to be able to run the migration again at a later point, should it become necessary.', 'mmp'), "<code>{$wpdb->prefix}leafletmapsmarker_layers</code>", "<code>{$wpdb->prefix}leafletmapsmarker_markers</code>") ?></p>
								<p><?= esc_html__('Starting with Maps Marker Pro 4.0, marker maps have been deprecated, but can still be used for backwards compatibility. However, additional shortcode attributes are needed in order to make them look the same. Due to the high risk of doing this programmatically, we require you to replace these shortcodes manually. Start the migration check to get a list of used shortcodes and how they need to be updated. Only shortcodes in posts and pages can be detected, so please also check if you are using any shortcodes in widgets or other places. This only affects shortcodes for marker maps. Shortcodes for layer maps do not need to be updated.', 'mmp') ?></p>
								<div id="migration_log"></div>
								<button id="data_migration_check" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-check-migration') ?>"><?= esc_html__('Check migration', 'mmp') ?></button>
								<button id="data_migration_start" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-migration') ?>" disabled="disabled"><?= esc_html__('Start migration', 'mmp') ?></button>
							<?php else: ?>
								<?= sprintf($l10n->kses__('If you want to copy your existing maps to this version, you need to update the old Maps Marker Pro installation to version %1$s first.', 'mmp'), '3.1.1') ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div id="health_tab_content" class="mmp-tools-tab">
				<div id="health_section" class="mmp-tools-section">
					<h2><?= esc_html__('Health check', 'mmp') ?></h2>
					<div>
						<p><?= esc_html__('This will test the plugin for problems and incompatibilities.', 'mmp') ?></p>
						<div id="health_check_result">
							<ul>
								<li><?= esc_html__('Plugin version', 'mmp') ?>: <?= $debug_info['mmp_version'] ?></li>
								<li><?= esc_html__('WordPress version', 'mmp') ?>: <?= $debug_info['wp_version'] ?> <?= $this->yes_no(version_compare($debug_info['wp_version'], '4.5', '>=')) ?></li>
								<li><?= esc_html__('PHP version', 'mmp') ?>: <?= $debug_info['php_version'] ?> <?= $this->yes_no(version_compare($debug_info['php_version'], '5.6', '>=')) ?></li>
								<li><?= esc_html__('Permalinks', 'mmp') ?>: <?= $this->yes_no($debug_info['wp_rewrite']) ?></li>
								<li><?= esc_html__('AJAX request', 'mmp') ?>: <?= $this->yes_no($ajax_test_result) ?></li>
								<li><?= esc_html__('API endpoint', 'mmp') ?>: <?= $this->yes_no(wp_remote_retrieve_response_code($debug_info['api_response']) === 302) ?></li>
							</ul>
							<p><?= sprintf($l10n->kses__('To download a detailed report, please <a href="%1$s">click here</a>.', 'mmp'), API::$base_url . 'index.php?mapsmarkerpro=download_debug&nonce=' . wp_create_nonce('mmp-download-debug')) ?></p>
						</div>
					</div>
				</div>
			</div>
			<div id="reset_tab_content" class="mmp-tools-tab">
				<div id="reset_database_section" class="mmp-tools-section">
					<h2><?= esc_html__('Reset database', 'mmp') ?></h2>
					<div>
						<p><?= esc_html__('This will reset the Maps Marker Pro database. All custom layers, maps, markers and relationships will be deleted. Settings are not affected.', 'mmp') ?></p>
						<p class="mmp-warning"><?= esc_html__('WARNING: this cannot be undone.', 'mmp') ?></p>
						<button type="button" id="reset_database" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-reset-db') ?>" disabled="disabled"><?= esc_html__('Reset database', 'mmp') ?></button>
						<label><input type="checkbox" id="reset_database_confirm" name="reset_database_confirm" /><?= esc_html__('Are you sure?', 'mmp') ?></label>
					</div>
				</div>
				<div id="reset_settings_section" class="mmp-tools-section">
					<h2><?= esc_html__('Reset settings', 'mmp') ?></h2>
					<div>
						<p><?= esc_html__('This will reset the selected Maps Marker Pro settings to their default values.', 'mmp') ?></p>
						<label><input type="checkbox" id="reset_plugin_settings" name="reset_plugin_settings" /><?= esc_html__('Plugin settings', 'mmp') ?></label><br />
						<label><input type="checkbox" id="reset_map_settings" name="reset_map_settings" /><?= esc_html__('Map settings for new maps', 'mmp') ?></label><br />
						<label><input type="checkbox" id="reset_marker_settings" name="reset_marker_settings" /><?= esc_html__('Marker settings for new markers', 'mmp') ?></label>
						<p class="mmp-warning"><?= esc_html__('WARNING: this cannot be undone.', 'mmp') ?></p>
						<button type="button" id="reset_settings" class="button button-primary" data-nonce="<?= wp_create_nonce('mmp-tools-reset-settings') ?>" disabled="disabled"><?= esc_html__('Reset settings', 'mmp') ?></button>
						<label><input type="checkbox" id="reset_settings_confirm" name="reset_settings_confirm" /><?= esc_html__('Are you sure?', 'mmp') ?></label>
					</div>
				</div>
			</div>
			<div id="markerIcons" class="mmp-admin-modal">
				<div class="mmp-admin-modal-content">
					<span class="mmp-admin-modal-close">&times;</span>
					<div class="mmp-admin-modal-header">
						<p class="mmp-admin-modal-title"><?= esc_html__('Change icon', 'mmp') ?></p>
					</div>
					<div class="mmp-admin-modal-body">
						<div id="markerIconsList">
							<img class="mmp-icon" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" />
							<?php foreach ($upload->get_icons() as $icon): ?>
								<img class="mmp-icon" src="<?= MMP::$icons_url . $icon ?>" />
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="icons" class="mmp-admin-modal">
				<div class="mmp-admin-modal-content">
					<span class="mmp-admin-modal-close">&times;</span>
					<div class="mmp-admin-modal-header">
						<p class="mmp-admin-modal-title"><?= esc_html__('Change icon', 'mmp') ?></p>
					</div>
					<div class="mmp-admin-modal-body">
						<div id="iconsList">
							<img class="mmp-icon" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" />
							<?php foreach ($upload->get_icons() as $icon): ?>
								<img class="mmp-icon" src="<?= MMP::$icons_url . $icon ?>" />
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="iconsGpx" class="mmp-admin-modal">
				<div class="mmp-admin-modal-content">
					<span class="mmp-admin-modal-close">&times;</span>
					<div class="mmp-admin-modal-header">
						<p class="mmp-admin-modal-title"><?= esc_html__('Change icon', 'mmp') ?></p>
					</div>
					<div class="mmp-admin-modal-body">
						<div id="iconsListGpx">
							<img class="mmp-icon" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" data-icon="" />
							<img class="mmp-icon" src="<?= plugins_url('images/leaflet/gpx-start.png', MMP::$path) ?>" data-icon="" />
							<img class="mmp-icon" src="<?= plugins_url('images/leaflet/gpx-end.png', MMP::$path) ?>" data-icon="" />
							<?php foreach ($upload->get_icons() as $icon): ?>
								<img class="mmp-icon" src="<?= MMP::$icons_url . $icon ?>" data-icon="<?= $icon ?>" />
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
