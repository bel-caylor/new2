<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

/**
 * Database abstraction class
 *
 * @since 4.0
 */
class DB {
	/**
	 * Creates the database tables
	 *
	 * @since 4.0
	 */
	public function create_tables() {
		global $wpdb;

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mmp_geocoding_cache` (
				`query` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`lat` decimal(10,6) NOT NULL,
				`lng` decimal(10,6) NOT NULL,
				`created` datetime NOT NULL,
				UNIQUE KEY `query` (`query`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mmp_layers` (
				`id` int(8) NOT NULL AUTO_INCREMENT,
				`wms` int(1) NOT NULL,
				`overlay` int(1) NOT NULL,
				`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
				`options` text COLLATE utf8mb4_unicode_ci NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mmp_maps` (
				`id` int(8) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`filters` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`geojson` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
				`created_by_id` bigint(20) NOT NULL,
				`created_on` datetime NOT NULL,
				`updated_by_id` bigint(20) NOT NULL,
				`updated_on` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mmp_markers` (
				`id` int(8) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`lat` decimal(10,6) NOT NULL,
				`lng` decimal(10,6) NOT NULL,
				`zoom` decimal(3,1) NOT NULL,
				`icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`popup` text COLLATE utf8mb4_unicode_ci NOT NULL,
				`link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`blank` int(1) NOT NULL,
				`schedule_from` DATETIME NULL DEFAULT NULL,
				`schedule_until` DATETIME NULL DEFAULT NULL,
				`created_by_id` bigint(20) NOT NULL,
				`created_on` datetime NOT NULL,
				`updated_by_id` bigint(20) NOT NULL,
				`updated_on` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mmp_relationships` (
				`map_id` int(8) NOT NULL,
				`type_id` int(1) NOT NULL,
				`object_id` int(8) NOT NULL,
				UNIQUE KEY `key` (`map_id`,`type_id`,`object_id`),
				KEY `map_id` (`map_id`),
				KEY `type_id` (`type_id`),
				KEY `object_id` (`object_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
		);
	}

	/**
	 * Deletes the database tables
	 *
	 * @since 4.0
	 */
	public function delete_tables() {
		global $wpdb;

		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_geocoding_cache");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_layers");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_maps");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_markers");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mmp_relationships");
	}

	/**
	 * Resets the database tables
	 *
	 * @since 4.0
	 */
	public function reset_tables() {
		$this->delete_tables();
		$this->create_tables();
	}

	/**
	 * Adds an address to the geocoding cache
	 *
	 * @since 4.20
	 * @param string $query Query
	 * @param string $query Address
	 * @param float $lat Latitude
	 * @param float $lng Longitude
	 * @return int|false Number of affected rows or false if the result could not be added
	 */
	public function cache_address($query, $address, $lat, $lng) {
		global $wpdb;

		$add = $wpdb->query($wpdb->prepare(
			"INSERT IGNORE INTO {$wpdb->prefix}mmp_geocoding_cache (query, address, lat, lng, created)
			VALUES ('%s', '%s', '%f', '%f', '%s')",
			$query, $address, $lat, $lng, gmdate('Y-m-d H:i:s')
		));

		return $add;
	}

	/**
	 * Returns an address from the geocoding cache
	 *
	 * @since 4.20
	 * @param string $query Query
	 * @return object|null Cache object or null if no result is found
	 */
	public function get_cached_address($query) {
		global $wpdb;

		$cache = $wpdb->get_row($wpdb->prepare(
			"SELECT address, lat, lng
			FROM {$wpdb->prefix}mmp_geocoding_cache
			WHERE query = %s",
			$query
		));

		if ($cache === null) {
			return null;
		}

		return $cache;
	}

	/**
	 * Clears the geocoding cache
	 *
	 * @since 4.20
	 * @param int $days (optional) Number of days the entries must have existed
	 * @return int|false Number of affected rows or false if the cache could not be cleared
	 */
	public function clear_geocoding_cache($days = 0) {
		global $wpdb;

		$results = $wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}mmp_geocoding_cache
			WHERE created < NOW() - INTERVAL %d DAY",
			$days
		));

		return $results;
	}

	/**
	 * Returns the total number of maps
	 * Optionally accepts a list of filters
	 *
	 * @since 4.0
	 * @param array $filters (optional) List of filters
	 * @return int Total number of maps
	 */
	public function count_maps($filters = array()) {
		global $wpdb;

		$filter_query = $this->parse_map_filters($filters);

		$count = $wpdb->get_var(
			"SELECT COUNT(1)
			FROM {$wpdb->prefix}mmp_maps AS maps
			$filter_query"
		);

		return intval($count);
	}

	/**
	 * Returns the map for the given ID
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param int $id Map ID
	 * @param bool $count (optional) Whether to count the assigned markers
	 * @param string $output (optional) Return type
	 * @return object|array|null Map object/array or null if no result is found
	 */
	public function get_map($id, $count = false, $output = OBJECT) {
		global $wpdb;

		$map = $wpdb->get_row($wpdb->prepare(
			"SELECT maps.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by
			FROM {$wpdb->prefix}mmp_maps AS maps
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = maps.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = maps.updated_by_id)
			WHERE maps.id = %d",
			$id
		), $output);

		if ($map === null) {
			return null;
		}

		if ($count) {
			$map->markers = $this->count_map_markers($map->id);
		}

		return $map;
	}

	/**
	 * Returns the maps for the given IDs
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param array|string $ids List or CSV of map IDs
	 * @param bool $count (optional) Whether to count the assigned markers
	 * @param string $output (optional) Return type
	 * @return array List of map objects/arrays
	 */
	public function get_maps($ids, $count = false, $output = OBJECT) {
		global $wpdb;

		$ids = $this->sanitize_ids($ids, true);

		$maps = $wpdb->get_results(
			"SELECT maps.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by
			FROM {$wpdb->prefix}mmp_maps AS maps
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = maps.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = maps.updated_by_id)
			WHERE maps.id IN ($ids)",
			$output
		);

		if ($maps === null) {
			return array();
		}

		if ($count) {
			foreach ($maps as $key => $map) {
				$maps[$key]->markers = $this->count_map_markers($map->id);
			}
		}

		return $maps;
	}

	/**
	 * Returns all maps
	 * Optionally accepts a list of filters
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param bool $count (optional) Whether to count the assigned markers
	 * @param array $filters (optional) List of filters
	 * @param string $output (optional) Return type
	 * @return array List of map objects/arrays
	 */
	public function get_all_maps($count = false, $filters = array(), $output = OBJECT) {
		global $wpdb;

		$filter_query = $this->parse_map_filters($filters);

		$maps = $wpdb->get_results(
			"SELECT maps.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by
			FROM {$wpdb->prefix}mmp_maps AS maps
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = maps.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = maps.updated_by_id)
			$filter_query",
			$output
		);

		if ($maps === null) {
			return array();
		}

		if ($count) {
			foreach ($maps as $key => $map) {
				$maps[$key]->markers = $this->count_map_markers($map->id);
			}
		}

		return $maps;
	}

	/**
	 * Returns all posts that use a shortcode for the given map ID
	 *
	 * @since 4.0
	 * @param int $id Map ID
	 * @return array List of posts
	 */
	public function get_map_shortcodes($id) {
		global $wpdb;

		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT ID, post_title
			FROM {$wpdb->posts}
			WHERE post_status = 'publish' AND (post_content LIKE %s OR post_content LIKE %s)",
			'%[' . $wpdb->esc_like(MMP::$settings['shortcode']) . '%map="' . $wpdb->esc_like($id) . '"%]%',
			'%[' . $wpdb->esc_like(MMP::$settings['shortcode']) . '%layer="' . $wpdb->esc_like($id) . '"%]%'
		));

		if ($results === null) {
			return array();
		}

		$posts = array();
		foreach ($results as $result) {
			$posts[] = array(
				'title' => ($result->post_title) ? esc_html($result->post_title) : esc_html__('(no title)', 'mmp'),
				'link'  => get_permalink($result->ID),
				'edit'  => get_edit_post_link($result->ID)
			);
		}

		return $posts;
	}

	/**
	 * Adds a map
	 *
	 * @since 4.0
	 * @param object $data Map data to be written
	 * @param int $id (optional) ID for the new map
	 * @return int|false Map ID or false if the map could not be added
	 */
	public function add_map($data, $id = 0) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$insert = $wpdb->insert(
			"{$wpdb->prefix}mmp_maps",
			array(
				'id' => $id,
				'name' => $data->name,
				'settings' => $data->settings,
				'filters' => $data->filters,
				'geojson' => $data->geojson,
				'created_by_id' => $data->created_by_id,
				'created_on' => $data->created_on,
				'updated_by_id' => $data->updated_by_id,
				'updated_on' => $data->updated_on
			),
			array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
		);

		if ($insert === false) {
			return false;
		}

		$insert_id = $wpdb->insert_id;

		$l10n->register("Map (ID {$insert_id}) name", $data->name);

		return $insert_id;
	}

	/**
	 * Adds multiple maps
	 *
	 * @since 4.0
	 * @param array $data List of map data to be written
	 * @return int|false Number of affected rows or false if the maps could not be added
	 */
	public function add_maps($data) {
		global $wpdb;

		if (!is_array($data) || !count($data)) {
			return false;
		}

		$sql = $this->build_insert_query("{$wpdb->prefix}mmp_maps", $data, $this->prepare_maps());
		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Updates a map
	 *
	 * @since 4.0
	 * @param object $data Map data to be written
	 * @param int $id ID of the map to be updated
	 * @return int|false Number of affected rows or false if the map could not be updated
	 */
	public function update_map($data, $id) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$update = $wpdb->update(
			"{$wpdb->prefix}mmp_maps",
			array(
				'name' => $data->name,
				'settings' => $data->settings,
				'filters' => $data->filters,
				'geojson' => $data->geojson,
				'updated_by_id' => $data->updated_by_id,
				'updated_on' => $data->updated_on
			),
			array('id' => $id),
			array('%s', '%s', '%s', '%s', '%d', '%s'),
			array('%d')
		);

		if ($update === false) {
			return false;
		}

		$l10n->register("Map (ID {$id}) name", $data->name);

		return $update;
	}

	/**
	 * Updates multiple maps
	 *
	 * @since 4.0
	 * @param object $data Map data to be written
	 * @param array|string $ids List or CSV of map IDs
	 * @return int Number of affected rows
	 */
	public function update_maps($data, $ids) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$ids = $this->sanitize_ids($ids);

		$rows = 0;
		foreach ($ids as $id) {
			$update = $wpdb->update(
				"{$wpdb->prefix}mmp_maps",
				array(
					'name' => $data->name,
					'settings' => $data->settings,
					'filters' => $data->filters,
					'geojson' => $data->geojson,
					'updated_by_id' => $data->updated_by_id,
					'updated_on' => $data->updated_on
				),
				array('id' => $id),
				array('%s', '%s', '%s', '%s', '%d', '%s'),
				array('%d')
			);

			if ($update) {
				$l10n->register("Map (ID {$id}) name", $data->name);
				$rows += $update;
			}
		}

		return $rows;
	}

	/**
	 * Deletes a map and its relationships
	 *
	 * @since 4.0
	 * @param int $id ID of the map to be deleted
	 * @return int|false Number of affected rows or false if the map could not be deleted
	 */
	public function delete_map($id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_maps",
			array('id' => $id),
			array('%d')
		);
		$wpdb->delete(
			"{$wpdb->prefix}mmp_relationships",
			array('map_id' => $id),
			array('%d')
		);

		return $delete;
	}

	/**
	 * Deletes multiple maps and their relationships
	 *
	 * @since 4.0
	 * @param array|string $ids List or CSV of map IDs
	 * @return int|false Number of affected rows or false if the maps could not be deleted
	 */
	public function delete_maps($ids) {
		global $wpdb;

		$ids = $this->sanitize_ids($ids, true);

		$results = $wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_maps
			WHERE `id` IN ($ids)"
		);
		$wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_relationships
			WHERE `map_id` IN ($ids)"
		);

		return $results;
	}

	/**
	 * Returns the total number of markers
	 * Optionally accepts a list of filters
	 *
	 * @since 4.0
	 * @param array $filters (optional) List of filters
	 * @return int Total number of markers
	 */
	public function count_markers($filters = array()) {
		global $wpdb;

		if (isset($filters['lat']) && isset($filters['lng'])) {
			$lat = floatval($filters['lat']);
			$lng = floatval($filters['lng']);
			$distance = ", 6371000 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(markers.lat)) * COS(RADIANS(markers.lng) - RADIANS($lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(markers.lat))) AS distance";
		} else {
			$distance = '';
		}

		$filter_query = $this->parse_marker_filters($filters);

		$count = $wpdb->get_var(
			"SELECT COUNT(markers.id) FROM (
				SELECT markers.id $distance
				FROM {$wpdb->prefix}mmp_markers AS markers
				LEFT JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.object_id = markers.id AND rels.type_id = 2)
				$filter_query
			) AS markers"
		);

		return intval($count);
	}

	/**
	 * Returns the total number of markers for the given map ID
	 *
	 * @since 4.0
	 * @param int $id Map ID
	 * @return int Total number of markers for the given map ID
	 */
	public function count_map_markers($id) {
		global $wpdb;
		$mmp_settings = MMP::get_instance('MMP\Settings');

		$map = $this->get_map($id);

		if ($map === null) {
			return null;
		}

		$settings = $mmp_settings->validate_map_settings(json_decode($map->settings, true));
		if ($settings['filtersAllMarkers']) {
			$count = $this->count_markers();
		} else {
			$filters = json_decode($map->filters, true);
			$ids = $this->sanitize_ids(array_merge(array($map->id), array_keys($filters)), true);
			$count = $wpdb->get_var(
				"SELECT COUNT(DISTINCT markers.id)
				FROM {$wpdb->prefix}mmp_markers AS markers
				JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.map_id IN ($ids) AND rels.type_id = 2 AND rels.object_id = markers.id)"
			);
		}

		return intval($count);
	}

	/**
	 * Returns the marker for the given ID
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param int $id Marker ID
	 * @param string $output (optional) Return type
	 * @return object|array|null Marker object/array or null if no result is found
	 */
	public function get_marker($id, $output = OBJECT) {
		global $wpdb;

		$marker = $wpdb->get_row($wpdb->prepare(
			"SELECT markers.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by, GROUP_CONCAT(rels.map_id) AS maps
			FROM {$wpdb->prefix}mmp_markers AS markers
			LEFT JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.type_id = 2 AND rels.object_id = markers.id)
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = markers.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = markers.updated_by_id)
			WHERE markers.id = %d
			GROUP BY markers.id",
			$id
		), $output);

		if ($marker === null) {
			return null;
		}

		return $marker;
	}

	/**
	 * Returns the markers for the given IDs
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param array|string $ids List or CSV of marker IDs
	 * @param string $output (optional) Return type
	 * @return array List of marker objects/arrays
	 */
	public function get_markers($ids, $output = OBJECT) {
		global $wpdb;

		$ids = $this->sanitize_ids($ids, true);

		$markers = $wpdb->get_results(
			"SELECT markers.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by, GROUP_CONCAT(rels.map_id) AS maps
			FROM {$wpdb->prefix}mmp_markers AS markers
			LEFT JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.type_id = 2 AND rels.object_id = markers.id)
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = markers.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = markers.updated_by_id)
			WHERE markers.id IN ($ids)
			GROUP BY markers.id",
			$output
		);

		if ($markers === null) {
			return array();
		}

		return $markers;
	}

	/**
	 * Returns all markers
	 * Optionally accepts a list of filters
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param array $filters (optional) List of filters
	 * @param string $output (optional) Return type
	 * @return array List of marker objects/arrays
	 */
	public function get_all_markers($filters = array(), $output = OBJECT) {
		global $wpdb;

		if (isset($filters['lat']) && isset($filters['lng'])) {
			$lat = floatval($filters['lat']);
			$lng = floatval($filters['lng']);
			$distance = ", 6371000 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(markers.lat)) * COS(RADIANS(markers.lng) - RADIANS($lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(markers.lat))) AS distance";
		} else {
			$distance = '';
		}

		$filter_query = $this->parse_marker_filters($filters);

		$markers = $wpdb->get_results(
			"SELECT markers.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by, GROUP_CONCAT(rels.map_id) AS maps $distance
			FROM {$wpdb->prefix}mmp_markers AS markers
			LEFT JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.object_id = markers.id AND rels.type_id = 2)
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = markers.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = markers.updated_by_id)
			$filter_query",
			$output
		);

		if ($markers === null) {
			return array();
		}

		return $markers;
	}

	/**
	 * Returns the markers for the given map ID
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param int $id Map ID
	 * @param string $output (optional) Return type
	 * @return array List of marker objects/arrays
	 */
	public function get_map_markers($id, $output = OBJECT) {
		global $wpdb;

		$markers = $wpdb->get_results($wpdb->prepare(
			"SELECT markers.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by, GROUP_CONCAT(maps.id) AS maps
			FROM {$wpdb->prefix}mmp_markers AS markers
			JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.map_id = %d AND rels.type_id = 2 AND rels.object_id = markers.id)
			JOIN {$wpdb->prefix}mmp_maps AS maps ON (rels.map_id = maps.id)
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = markers.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = markers.updated_by_id)
			GROUP BY markers.id",
			$id
		), $output);

		if ($markers === null) {
			return array();
		}

		return $markers;
	}

	/**
	 * Returns the markers for the given map IDs
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param int $ids Map IDs
	 * @param string $output (optional) Return type
	 * @return array List of marker objects/arrays
	 */
	public function get_maps_markers($ids, $output = OBJECT) {
		global $wpdb;

		$ids = $this->sanitize_ids($ids, true);

		$markers = $wpdb->get_results(
			"SELECT markers.*, user_created.display_name AS created_by, user_updated.display_name AS updated_by, GROUP_CONCAT(maps.id) AS maps
			FROM {$wpdb->prefix}mmp_markers AS markers
			JOIN {$wpdb->prefix}mmp_relationships AS rels ON (rels.map_id IN ($ids) AND rels.type_id = 2 AND rels.object_id = markers.id)
			JOIN {$wpdb->prefix}mmp_maps AS maps ON (rels.map_id = maps.id)
			LEFT JOIN {$wpdb->users} AS user_created ON (user_created.ID = markers.created_by_id)
			LEFT JOIN {$wpdb->users} AS user_updated ON (user_updated.ID = markers.updated_by_id)
			GROUP BY markers.id",
			$output
		);

		if ($markers === null) {
			return array();
		}

		return $markers;
	}

	/**
	 * Adds a marker
	 *
	 * @since 4.0
	 * @param object $data Marker data to be written
	 * @param int $id (optional) ID for the new marker
	 * @return int|false Marker ID or false if the marker could not be added
	 */
	public function add_marker($data, $id = 0) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$insert = $wpdb->insert(
			"{$wpdb->prefix}mmp_markers",
			array(
				'id' => $id,
				'name' => $data->name,
				'address' => $data->address,
				'lat' => $data->lat,
				'lng' => $data->lng,
				'zoom' => $data->zoom,
				'icon' => $data->icon,
				'popup' => $data->popup,
				'link' => $data->link,
				'blank' => $data->blank,
				'schedule_from' => $data->schedule_from,
				'schedule_until' => $data->schedule_until,
				'created_by_id' => $data->created_by_id,
				'created_on' => $data->created_on,
				'updated_by_id' => $data->updated_by_id,
				'updated_on' => $data->updated_on
			),
			array('%d', '%s', '%s', '%f', '%f', '%f', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s', '%d', '%s')
		);

		if ($insert === false) {
			return false;
		}

		$insert_id = $wpdb->insert_id;

		$l10n->register("Marker (ID {$insert_id}) name", $data->name);
		$l10n->register("Marker (ID {$insert_id}) address", $data->address);
		$l10n->register("Marker (ID {$insert_id}) link", $data->link);
		$l10n->register("Marker (ID {$insert_id}) popup", $data->popup);

		return $insert_id;
	}

	/**
	 * Adds multiple markers
	 *
	 * @since 4.0
	 * @param array $data List of marker data to be written
	 * @return int|false Number of affected rows or false if the markers could not be added
	 */
	public function add_markers($data) {
		global $wpdb;

		if (!is_array($data) || !count($data)) {
			return false;
		}

		$sql = $this->build_insert_query("{$wpdb->prefix}mmp_markers", $data, $this->prepare_markers());
		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Updates a marker
	 *
	 * @since 4.0
	 * @param object $data Marker data to be written
	 * @param int $id ID of the marker to be updated
	 * @return int|false Number of affected rows or false if the marker could not be updated
	 */
	public function update_marker($data, $id) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$update = $wpdb->update(
			"{$wpdb->prefix}mmp_markers",
			array(
				'name' => $data->name,
				'address' => $data->address,
				'lat' => $data->lat,
				'lng' => $data->lng,
				'zoom' => $data->zoom,
				'icon' => $data->icon,
				'popup' => $data->popup,
				'link' => $data->link,
				'blank' => $data->blank,
				'schedule_from' => $data->schedule_from,
				'schedule_until' => $data->schedule_until,
				'updated_by_id' => $data->updated_by_id,
				'updated_on' => $data->updated_on
			),
			array('id' => $id),
			array('%s', '%s', '%f', '%f', '%f', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s'),
			array('%d')
		);

		if ($update === false) {
			return false;
		}

		$l10n->register("Marker (ID {$id}) name", $data->name);
		$l10n->register("Marker (ID {$id}) address", $data->address);
		$l10n->register("Marker (ID {$id}) link", $data->link);
		$l10n->register("Marker (ID {$id}) popup", $data->popup);

		return $update;
	}

	/**
	 * Updates multiple markers
	 *
	 * @since 4.0
	 * @param object $data Marker data to be written
	 * @param array|string $ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be updated
	 */
	public function update_markers($data, $ids) {
		global $wpdb;
		$l10n = MMP::get_instance('MMP\L10n');

		$ids = $this->sanitize_ids($ids);

		$rows = 0;
		foreach ($ids as $id) {
			$update = $wpdb->update(
				"{$wpdb->prefix}mmp_markers",
				array(
					'name' => $data->name,
					'address' => $data->address,
					'lat' => $data->lat,
					'lng' => $data->lng,
					'zoom' => $data->zoom,
					'icon' => $data->icon,
					'popup' => $data->popup,
					'link' => $data->link,
					'blank' => $data->blank,
					'schedule_from' => $data->schedule_from,
					'schedule_until' => $data->schedule_until,
					'updated_by_id' => $data->updated_by_id,
					'updated_on' => $data->updated_on
				),
				array('id' => $id),
				array('%s', '%s', '%f', '%f', '%f', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s'),
				array('%d')
			);

			if ($update) {
				$l10n->register("Marker (ID {$id}) name", $data->name);
				$l10n->register("Marker (ID {$id}) address", $data->address);
				$l10n->register("Marker (ID {$id}) link", $data->link);
				$l10n->register("Marker (ID {$id}) popup", $data->popup);
				$rows += $update;
			}
		}

		return $rows;
	}

	/**
	 * Assigns a marker to a map
	 *
	 * @since 4.0
	 * @param int $map_id Map ID
	 * @param int $marker_id Marker ID
	 * @return int|false Number of affected rows or false if the marker could not be assigned
	 */
	public function assign_marker($map_id, $marker_id) {
		global $wpdb;

		$map_id = absint($map_id);
		$marker_id = absint($marker_id);

		if (!$map_id || !$marker_id) {
			return false;
		}

		$assign = $wpdb->query($wpdb->prepare(
			"INSERT IGNORE INTO {$wpdb->prefix}mmp_relationships (map_id, type_id, object_id)
			VALUES ('%d', '%d', '%d')",
			$map_id, 2, $marker_id
		));

		return $assign;
	}

	/**
	 * Assigns multiple markers to a map
	 *
	 * @since 4.0
	 * @param int $map_id Map ID
	 * @param array|string $marker_ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be assigned
	 */
	public function assign_markers($map_id, $marker_ids) {
		global $wpdb;

		$marker_ids = $this->sanitize_ids($marker_ids);

		if (!count($marker_ids)) {
			return false;
		}

		$cols = implode(',', array_keys($this->prepare_rels()));
		$prep = implode(',', array_values($this->prepare_rels()));
		$sql = "INSERT IGNORE INTO {$wpdb->prefix}mmp_relationships ({$cols}) VALUES ";
		foreach ($marker_ids as $marker_id) {
			$sql .= $wpdb->prepare("({$prep}),", $map_id, 2, $marker_id);
		}
		$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query

		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Assigns a marker to multiple maps
	 *
	 * @since 4.0
	 * @param array|string $map_ids List or CSV of map IDs
	 * @param int $marker_id Marker ID
	 * @return int|false Number of affected rows or false if the marker could not be assigned
	 */
	public function assign_maps_marker($map_ids, $marker_id) {
		global $wpdb;

		$map_ids = $this->sanitize_ids($map_ids);

		if (!count($map_ids)) {
			return false;
		}

		$cols = implode(',', array_keys($this->prepare_rels()));
		$prep = implode(',', array_values($this->prepare_rels()));
		$sql = "INSERT IGNORE INTO {$wpdb->prefix}mmp_relationships ({$cols}) VALUES ";
		foreach ($map_ids as $map_id) {
			$sql .= $wpdb->prepare("({$prep}),", $map_id, 2, $marker_id);
		}
		$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query

		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Assigns multiple markers to multiple maps
	 *
	 * @since 4.0
	 * @param array|string $map_ids List or CSV of map IDs
	 * @param array|string $marker_ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be assigned
	 */
	public function assign_maps_markers($map_ids, $marker_ids) {
		global $wpdb;

		$map_ids = $this->sanitize_ids($map_ids);
		$marker_ids = $this->sanitize_ids($marker_ids);

		if (!count($map_ids) || !count($marker_ids)) {
			return false;
		}

		$cols = implode(',', array_keys($this->prepare_rels()));
		$prep = implode(',', array_values($this->prepare_rels()));
		$sql = "INSERT IGNORE INTO {$wpdb->prefix}mmp_relationships ({$cols}) VALUES ";
		foreach ($map_ids as $map_id) {
			foreach ($marker_ids as $marker_id) {
				$sql .= $wpdb->prepare("({$prep}),", $map_id, 2, $marker_id);
			}
		}
		$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query

		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Assigns markers from an associative list
	 *
	 * @since 4.9
	 * @param array $assoc Associative list of marker ID => map IDs
	 * @return int|false Number of affected rows or false if the markers could not be assigned
	 */
	public function assign_assoc($assoc) {
		global $wpdb;

		if (!is_array($assoc) || !count($assoc)) {
			return false;
		}

		$cols = implode(',', array_keys($this->prepare_rels()));
		$prep = implode(',', array_values($this->prepare_rels()));
		$sql = "INSERT IGNORE INTO {$wpdb->prefix}mmp_relationships ({$cols}) VALUES ";
		foreach ($assoc as $marker_id => $map_ids) {
			$marker_id = absint($marker_id);
			$map_ids = $this->sanitize_ids($map_ids);

			if (!$marker_id || !count($map_ids)) {
				continue;
			}

			foreach ($map_ids as $map_id) {
				$sql .= $wpdb->prepare("({$prep}),", $map_id, 2, $marker_id);
			}
		}
		$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query

		$result = $wpdb->query($sql);

		return $result;
	}

	/**
	 * Unassigns a marker from a map
	 *
	 * @since 4.0
	 * @param int $map_id Map ID
	 * @param int $marker_id Marker ID
	 * @return int|false Number of affected rows or false if the marker could not be unassigned
	 */
	public function unassign_marker($map_id, $marker_id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_relationships",
			array(
				'map_id'    => $map_id,
				'type_id'   => 2,
				'object_id' => $marker_id
			),
			array('%d', '%d', '%d')
		);

		return $delete;
	}

	/**
	 * Unassigns multiple markers from a map
	 *
	 * @since 4.0
	 * @param int $map_id Map ID
	 * @param array|string $marker_ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be unassigned
	 */
	public function unassign_markers($map_id, $marker_ids) {
		global $wpdb;

		$marker_ids = $this->sanitize_ids($marker_ids, true);

		$results = $wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}mmp_relationships
			WHERE `map_id` = %d AND `type_id` = 2 AND `object_id` IN ($marker_ids)",
			$map_id
		));

		return $results;
	}

	/**
	 * Unassigns a marker from multiple maps
	 *
	 * @since 4.0
	 * @param array|string $map_ids List or CSV of map IDs
	 * @param int $marker_id Marker ID
	 * @return int|false Number of affected rows or false if the marker could not be unassigned
	 */
	public function unassign_maps_marker($map_ids, $marker_id) {
		global $wpdb;

		$map_ids = $this->sanitize_ids($map_ids, true);

		$results = $wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_relationships
			WHERE `map_id` IN ($map_ids) AND `type_id` = 2 AND `object_id` = $marker_id"
		);

		return $results;
	}

	/**
	 * Unassigns all markers from a map
	 *
	 * @since 4.0
	 * @param int $map_id Map ID
	 * @return int|false Number of affected rows or false if the markers could not be unassigned
	 */
	public function unassign_all_markers($map_id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_relationships",
			array(
				'map_id'  => $map_id,
				'type_id' => 2
			),
			array('%d', '%d')
		);

		return $delete;
	}

	/**
	 * Unassigns a marker from all maps
	 *
	 * @since 4.14
	 * @param int $marker_id Marker ID
	 * @return int|false Number of affected rows or false if the marker could not be unassigned
	 */
	public function unassign_all_maps($marker_id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_relationships",
			array(
				'type_id'   => 2,
				'object_id' => $marker_id
			),
			array('%d', '%d')
		);

		return $delete;
	}

	/**
	 * Unassigns multiple markers from all maps
	 *
	 * @since 4.20
	 * @param array|string $marker_ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be unassigned
	 */
	public function unassign_all_maps_markers($marker_ids) {
		global $wpdb;

		$marker_ids = $this->sanitize_ids($marker_ids, true);

		$results = $wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_relationships
			WHERE `type_id` = 2 AND `object_id` IN ($marker_ids)"
		);

		return $results;
	}

	/**
	 * Deletes a marker and its relationships
	 *
	 * @since 4.0
	 * @param int $id ID of the marker to be deleted
	 * @return int|false Number of affected rows or false if the marker could not be deleted
	 */
	public function delete_marker($id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_markers",
			array('id' => $id),
			array('%d')
		);
		$wpdb->delete(
			"{$wpdb->prefix}mmp_relationships",
			array(
				'type_id'   => 2,
				'object_id' => $id
			),
			array('%d', '%d')
		);

		return $delete;
	}

	/**
	 * Deletes multiple markers and their relationships
	 *
	 * @since 4.0
	 * @param array|string $ids List or CSV of marker IDs
	 * @return int|false Number of affected rows or false if the markers could not be deleted
	 */
	public function delete_markers($ids) {
		global $wpdb;

		$ids = $this->sanitize_ids($ids, true);

		$results = $wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_markers
			WHERE `id` IN ($ids)"
		);
		$wpdb->query(
			"DELETE FROM {$wpdb->prefix}mmp_relationships
			WHERE `type_id` = 2 AND `object_id` IN ($ids)"
		);

		return $results;
	}

	/**
	 * Returns the layer for the given ID
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param int $id Layer ID
	 * @param string $output (optional) Return type
	 * @return object|array|null Layer object/array or null if no result is found
	 */
	public function get_layer($id, $output = OBJECT) {
		global $wpdb;

		$layer = $wpdb->get_row($wpdb->prepare(
			"SELECT layers.*
			FROM {$wpdb->prefix}mmp_layers AS layers
			WHERE layers.id = %d",
			$id
		), $output);

		if ($layer === null) {
			return null;
		}

		return $layer;
	}

	/**
	 * Returns all layers
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param string $output (optional) Return type
	 * @return array List of layer objects/arrays
	 */
	public function get_all_layers($output = OBJECT) {
		global $wpdb;

		$layers = $wpdb->get_results(
			"SELECT layers.*
			FROM {$wpdb->prefix}mmp_layers AS layers",
			$output
		);

		if ($layers === null) {
			return array();
		}

		return $layers;
	}

	/**
	 * Returns all basemaps
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param string $output (optional) Return type
	 * @return array List of layer objects/arrays
	 */
	public function get_all_basemaps($output = OBJECT) {
		global $wpdb;

		$basemaps = $wpdb->get_results(
			"SELECT layers.*
			FROM {$wpdb->prefix}mmp_layers AS layers
			WHERE layers.overlay = 0",
			$output
		);

		if ($basemaps === null) {
			return array();
		}

		return $basemaps;
	}

	/**
	 * Returns all overlays
	 *
	 * @since 4.0
	 * @since 4.18 $output parameter added
	 * @param string $output (optional) Return type
	 * @return array List of layer objects/arrays
	 */
	public function get_all_overlays($output = OBJECT) {
		global $wpdb;

		$overlays = $wpdb->get_results(
			"SELECT layers.*
			FROM {$wpdb->prefix}mmp_layers AS layers
			WHERE layers.overlay = 1",
			$output
		);

		if ($overlays === null) {
			return array();
		}

		return $overlays;
	}

	/**
	 * Adds a layer
	 *
	 * @since 4.0
	 * @param object $data Layer data to be written
	 * @param int $id (optional) ID for the new layer
	 * @return int|false Layer ID or false if the layer could not be added
	 */
	public function add_layer($data, $id = 0) {
		global $wpdb;

		$insert = $wpdb->insert(
			"{$wpdb->prefix}mmp_layers",
			array(
				'id' => $id,
				'wms' => $data->wms,
				'overlay' => $data->overlay,
				'name' => $data->name,
				'url' => $data->url,
				'options' => $data->options
			),
			array('%d', '%d', '%d', '%s', '%s', '%s')
		);

		if ($insert === false) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Updates a layer
	 *
	 * @since 4.0
	 * @param object $data Layer data to be written
	 * @param int $id ID of the layer to be updated
	 * @return int|false Number of affected rows or false if the layer could not be updated
	 */
	public function update_layer($data, $id) {
		global $wpdb;

		$update = $wpdb->update(
			"{$wpdb->prefix}mmp_layers",
			array(
				'wms' => $data->wms,
				'overlay' => $data->overlay,
				'name' => $data->name,
				'url' => $data->url,
				'options' => $data->options
			),
			array('id' => $id),
			array('%d', '%d', '%s', '%s', '%s'),
			array('%d')
		);

		return $update;
	}

	/**
	 * Deletes a layer
	 *
	 * @since 4.0
	 * @param int $id Layer ID
	 * @return int|false Number of affected rows or false if the layer could not be deleted
	 */
	public function delete_layer($id) {
		global $wpdb;

		$delete = $wpdb->delete(
			"{$wpdb->prefix}mmp_layers",
			array('id' => $id),
			array('%d')
		);

		return $delete;
	}

	/**
	 * Deletes orphaned relationships
	 *
	 * @since 4.7
	 * @return int|false Number of affected rows or false if the orphans could not be deleted
	 */
	public function delete_orphaned_rels() {
		global $wpdb;

		$results = $wpdb->query(
			"DELETE rels FROM {$wpdb->prefix}mmp_relationships AS rels
			LEFT JOIN {$wpdb->prefix}mmp_maps AS maps ON rels.map_id = maps.id
			LEFT JOIN {$wpdb->prefix}mmp_markers AS markers ON rels.type_id = 2 AND rels.object_id = markers.id
			WHERE maps.id IS NULL OR markers.id IS NULL"
		);

		return $results;
	}

	/**
	 * Builds a valid marker object
	 *
	 * @since 4.9
	 * @param array $data List of marker data
	 * @param bool $geojson (optional) Whether the data is in GeoJSON format
	 * @return array Marker object
	 */
	public function build_marker($data, $geojson = false) {
		$current_user = wp_get_current_user();

		if ($geojson) {
			$temp = array();
			if (isset($data['properties']) && is_array($data['properties'])) {
				$temp = $data['properties'];
			}
			if (isset($data['geometry']['type']) && $data['geometry']['type'] === 'Point') {
				if (isset($data['geometry']['coordinates'][0])) {
					$temp['lng'] = $data['geometry']['coordinates'][0];
				}
				if (isset($data['geometry']['coordinates'][1])) {
					$temp['lat'] = $data['geometry']['coordinates'][1];
				}
			}
			$data = $temp;
		}

		$data = array_change_key_case($data);

		if (!isset($data['lat'])) {
			if (isset($data['latitude'])) {
				$data['lat'] = $data['latitude'];
			}
		}
		if (!isset($data['lng'])) {
			if (isset($data['lon'])) {
				$data['lng'] = $data['lon'];
			} else if (isset($data['long'])) {
				$data['lng'] = $data['long'];
			} else if (isset($data['longitude'])) {
				$data['lng'] = $data['longitude'];
			}
		}

		$time = gmdate('Y-m-d H:i:s');

		$marker = array(
			'id'             => (isset($data['id'])) ? $data['id'] : null,
			'name'           => (isset($data['name'])) ? $data['name'] : '',
			'address'        => (isset($data['address'])) ? $data['address'] : '',
			'lat'            => (isset($data['lat'])) ? $data['lat'] : null,
			'lng'            => (isset($data['lng'])) ? $data['lng'] : null,
			'zoom'           => (isset($data['zoom'])) ? $data['zoom'] : 11,
			'icon'           => (isset($data['icon'])) ? $data['icon'] : '',
			'popup'          => (isset($data['popup'])) ? $data['popup'] : '',
			'link'           => (isset($data['link'])) ? $data['link'] : '',
			'blank'          => (isset($data['blank'])) ? $data['blank'] : '1',
			'schedule_from'  => (isset($data['schedule_from'])) ? $data['schedule_from'] : null,
			'schedule_until' => (isset($data['schedule_until'])) ? $data['schedule_until'] : null,
			'created_by_id'  => (isset($data['created_by_id'])) ? $data['created_by_id'] : $current_user->ID,
			'created_on'     => (isset($data['created_on'])) ? $data['created_on'] : $time,
			'updated_by_id'  => (isset($data['updated_by_id'])) ? $data['updated_by_id'] : $current_user->ID,
			'updated_on'     => (isset($data['updated_on'])) ? $data['updated_on'] : $time,
			'maps'           => (isset($data['maps'])) ? $this->sanitize_ids($data['maps']) : array()
		);

		return $marker;
	}

	/**
	 * Sanitizes an array or comma-separated list of IDs
	 *
	 * @since 4.0
	 * @param array|string $ids List or CSV of IDs
	 * @param bool $csv (optional) Whether to return the sanitized IDs as CSV
	 * @return array|string List or CSV of sanitized IDs
	 */
	public function sanitize_ids($ids, $csv = false) {
		if (!is_array($ids)) {
			$ids = explode(',', $ids);
		}

		$ids = array_map('absint', $ids);
		$ids = array_unique($ids);
		$ids = array_filter($ids);

		natsort($ids);

		if ($csv) {
			$ids = implode(',', $ids);
		}

		return $ids;
	}

	/**
	 * Builds and escapes an insert query from a list of data
	 *
	 * @since 4.16
	 * @param string $table Table to insert into
	 * @param array $data List of data
	 * @param array $sanity List of sanitization rules
	 * @return string Escaped insert query
	 */
	public function build_insert_query($table, $data, $sanity) {
		// Need to manually escape, because $wpdb->prepare() does not support NULL values
		foreach ($data as $key => $row) {
			foreach ($row as $column => $value) {
				if (!isset($sanity[$column])) {
					unset($data[$key][$column]);
					continue;
				}
				if ($value === null) {
					$data[$key][$column] = 'NULL';
				} else if ($sanity[$column] === '%d') {
					$data[$key][$column] = intval($value);
				} else if ($sanity[$column] === '%f') {
					$data[$key][$column] = floatval($value);
				} else {
					$data[$key][$column] = "'" . esc_sql($value) . "'";
				}
			}
		}

		// Temporarily set number locale to system default to prevent issues from casting to string
		$locale = setlocale(LC_NUMERIC, 0);
		setlocale(LC_NUMERIC, 'C');

		$sql = "INSERT INTO $table (" . implode(',', array_keys($sanity)) . ") VALUES ";
		foreach ($data as $row) {
			$sql .= '(' . implode(',', array_values($row)) . '),';
		}
		$sql = substr($sql, 0, -1); // Remove trailing comma from loop-generated query

		// Restore original number locale
		setlocale(LC_NUMERIC, $locale);

		return $sql;
	}

	/**
	 * Returns the layers table sanitization rules for prepare statements
	 *
	 * @since 4.0
	 * @return array List of sanitization rules (column => rule)
	 */
	public function prepare_layers() {
		$cols = array(
			'id'      => '%d',
			'wms'     => '%d',
			'overlay' => '%d',
			'name'    => '%s',
			'url'     => '%s',
			'options' => '%s'
		);

		return $cols;
	}

	/**
	 * Returns the maps table sanitization rules for prepare statements
	 *
	 * @since 4.0
	 * @return array List of sanitization rules (column => rule)
	 */
	public function prepare_maps() {
		$cols = array(
			'id'            => '%d',
			'name'          => '%s',
			'settings'      => '%s',
			'filters'       => '%s',
			'geojson'       => '%s',
			'created_by_id' => '%d',
			'created_on'    => '%s',
			'updated_by_id' => '%d',
			'updated_on'    => '%s'
		);

		return $cols;
	}

	/**
	 * Returns the markers table sanitization rules for prepare statements
	 *
	 * @since 4.0
	 * @return array List of sanitization rules (column => rule)
	 */
	public function prepare_markers() {
		$cols = array(
			'id'             => '%d',
			'name'           => '%s',
			'address'        => '%s',
			'lat'            => '%f',
			'lng'            => '%f',
			'zoom'           => '%f',
			'icon'           => '%s',
			'popup'          => '%s',
			'link'           => '%s',
			'blank'          => '%d',
			'schedule_from'  => '%s',
			'schedule_until' => '%s',
			'created_by_id'  => '%d',
			'created_on'     => '%s',
			'updated_by_id'  => '%d',
			'updated_on'     => '%s'
		);

		return $cols;
	}

	/**
	 * Returns the relationships table sanitization rules for prepare statements
	 *
	 * @since 4.0
	 * @return array List of sanitization rules (column => rule)
	 */
	public function prepare_rels() {
		$cols = array(
			'map_id'    => '%d',
			'type_id'   => '%d',
			'object_id' => '%d'
		);

		return $cols;
	}

	/**
	 * Parses filters for map queries
	 *
	 * @since 4.0
	 * @param array $filters List of filters
	 * @return string Filters query
	 */
	private function parse_map_filters($filters) {
		global $wpdb;

		$query = 'WHERE 1';
		if (isset($filters['exclude'])) {
			$filters['exclude'] = $this->sanitize_ids($filters['exclude'], true);
			if ($filters['exclude']) {
				$query .= " AND maps.id NOT IN ({$filters['exclude']})";
			}
		}
		if (isset($filters['include'])) {
			$filters['include'] = $this->sanitize_ids($filters['include'], true);
			if ($filters['include']) {
				$query .= " AND maps.id IN ({$filters['include']})";
			}
		}
		if (isset($filters['name'])) {
			$query .= $wpdb->prepare(" AND maps.name LIKE '%%%s%%'", $filters['name']);
		}
		if (isset($filters['created_by'])) {
			$query .= $wpdb->prepare(" AND maps.created_by LIKE '%%%s%%'", $filters['created_by']);
		}
		if (isset($filters['created_by_id'])) {
			$filters['created_by_id'] = $this->sanitize_ids($filters['created_by_id'], true);
			$query .= " AND maps.created_by_id IN ({$filters['created_by_id']})";
		}
		if (isset($filters['updated_by'])) {
			$query .= $wpdb->prepare(" AND maps.updated_by LIKE '%%%s%%'", $filters['updated_by']);
		}
		if (isset($filters['updated_by_id'])) {
			$filters['updated_by_id'] = $this->sanitize_ids($filters['updated_by_id'], true);
			$query .= " AND maps.updated_by_id IN ({$filters['updated_by_id']})";
		}
		if (isset($filters['orderby']) && (in_array($filters['orderby'], array('created_by', 'updated_by')) || array_key_exists($filters['orderby'], $this->prepare_maps()))) {
			$query .= " ORDER BY {$filters['orderby']} ";
			$query .= (isset($filters['sortorder']) && $filters['sortorder'] === 'desc') ? 'DESC' : 'ASC';
		}
		if (isset($filters['limit'])) {
			$query .= ' LIMIT ' . absint($filters['limit']);
		}
		if (isset($filters['offset'])) {
			$query .= ' OFFSET ' . absint($filters['offset']);
		}

		return $query;
	}

	/**
	 * Parses filters for marker queries
	 *
	 * @since 4.0
	 * @param array $filters List of filters
	 * @param bool $group (optional) Whether to add the GROUP BY argument
	 * @return string Filters query
	 */
	private function parse_marker_filters($filters, $group = true) {
		global $wpdb;

		$query = 'WHERE 1';
		if (isset($filters['exclude'])) {
			$filters['exclude'] = $this->sanitize_ids($filters['exclude'], true);
			if ($filters['exclude']) {
				$query .= " AND markers.id NOT IN ({$filters['exclude']})";
			}
		}
		if (isset($filters['include'])) {
			$filters['include'] = $this->sanitize_ids($filters['include'], true);
			if ($filters['include']) {
				$query .= " AND markers.id IN ({$filters['include']})";
			}
		}
		if (isset($filters['exclude_maps'])) {
			if ($filters['exclude_maps'] === -1) {
				$query .= " AND rels.map_id IS NOT NULL";
			} else {
				$filters['exclude_maps'] = $this->sanitize_ids($filters['exclude_maps'], true);
				if ($filters['exclude_maps']) {
					$query .= " AND rels.map_id NOT IN ({$filters['exclude_maps']})";
				}
			}
		}
		if (isset($filters['include_maps'])) {
			if ($filters['include_maps'] === -1) {
				$query .= " AND rels.map_id IS NULL";
			} else {
				$filters['include_maps'] = $this->sanitize_ids($filters['include_maps'], true);
				if ($filters['include_maps']) {
					$query .= " AND rels.map_id IN ({$filters['include_maps']})";
				}
			}
		}
		if (isset($filters['id_offset'])) {
			$query .= ' AND markers.id > ' . absint($filters['id_offset']);
		}
		if (isset($filters['contains'])) {
			$query .= $wpdb->prepare(" AND (markers.name LIKE '%%%s%%' OR markers.address LIKE '%%%s%%' OR markers.popup LIKE '%%%s%%')", $filters['contains'], $filters['contains'], $filters['contains']);
		}
		if (isset($filters['name'])) {
			$query .= $wpdb->prepare(" AND markers.name LIKE '%%%s%%'", $filters['name']);
		}
		if (isset($filters['address'])) {
			$query .= $wpdb->prepare(" AND markers.address LIKE '%%%s%%'", $filters['address']);
		}
		if (isset($filters['popup'])) {
			$query .= $wpdb->prepare(" AND markers.popup LIKE '%%%s%%'", $filters['popup']);
		}
		if (isset($filters['created_by'])) {
			$query .= $wpdb->prepare(" AND markers.created_by LIKE '%%%s%%'", $filters['created_by']);
		}
		if (isset($filters['created_by_id'])) {
			$filters['created_by_id'] = $this->sanitize_ids($filters['created_by_id'], true);
			$query .= " AND markers.created_by_id IN ({$filters['created_by_id']})";
		}
		if (isset($filters['updated_by'])) {
			$query .= $wpdb->prepare(" AND markers.updated_by LIKE '%%%s%%'", $filters['updated_by']);
		}
		if (isset($filters['updated_by_id'])) {
			$filters['updated_by_id'] = $this->sanitize_ids($filters['updated_by_id'], true);
			$query .= " AND markers.updated_by_id IN ({$filters['updated_by_id']})";
		}
		if (isset($filters['scheduled']) && $filters['scheduled'] === false) {
			$query .= " AND (markers.schedule_from IS NULL OR markers.schedule_from = '0000-00-00 00:00:00' OR markers.schedule_from <= UTC_TIMESTAMP()) AND (markers.schedule_until IS NULL OR markers.schedule_until = '0000-00-00 00:00:00' OR markers.schedule_until >= UTC_TIMESTAMP())";
		}
		if ($group) {
			$query .= ' GROUP BY markers.id';
		}
		if (isset($filters['radius']) && isset($filters['lat']) && isset($filters['lng'])) {
			$query .= ' HAVING distance <= ' . absint($filters['radius']);
		}
		if (isset($filters['include_maps_logic']) && $filters['include_maps_logic'] === 'and') {
			if (isset($filters['include_maps']) && $filters['include_maps'] !== -1) {
				$query .= " HAVING maps = '{$filters['include_maps']}'";
			}
		}
		if (isset($filters['orderby']) && ((isset($filters['lat']) && isset($filters['lng']) && $filters['orderby'] === 'distance') || in_array($filters['orderby'], array('created_by', 'updated_by')) || array_key_exists($filters['orderby'], $this->prepare_markers()))) {
			$query .= " ORDER BY {$filters['orderby']} ";
			$query .= (isset($filters['sortorder']) && $filters['sortorder'] === 'desc') ? 'DESC' : 'ASC';
		}
		if (isset($filters['limit'])) {
			$query .= ' LIMIT ' . absint($filters['limit']);
		}
		if (isset($filters['offset'])) {
			$query .= ' OFFSET ' . absint($filters['offset']);
		}

		return $query;
	}
}
