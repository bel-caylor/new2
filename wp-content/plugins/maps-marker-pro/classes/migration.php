<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Migration {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('wp_ajax_mmp_check_migration', array($this, 'check_migration'));
		add_action('wp_ajax_mmp_data_migration', array($this, 'data_migration'));
	}

	/**
	 * Performs several checks for the data migration
	 *
	 * @since 4.0
	 */
	public function check_migration() {
		global $wpdb;

		check_ajax_referer('mmp-tools-check-migration', 'nonce');

		if (!current_user_can('activate_plugins')) {
			wp_send_json_error();
		}

		$settings = get_option('leafletmapsmarker_options');

		$marker_maps = array();
		$markers_count = $wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}leafletmapsmarker_markers");
		$markers_batches = ceil($markers_count / 1000);
		for ($i = 1; $i <= $markers_batches; $i++) {
			$batch_start = ($i - 1) * 1000;
			$markers = $wpdb->get_results("SELECT id, basemap, openpopup, mapwidth, mapwidthunit, mapheight, panel, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, gpx_url, gpx_panel FROM {$wpdb->prefix}leafletmapsmarker_markers LIMIT {$batch_start}, 1000");
			foreach ($markers as $marker) {
				$results = $wpdb->get_results($wpdb->prepare(
					"SELECT ID, post_title
					FROM {$wpdb->posts}
					WHERE post_status IN ('publish', 'pending', 'draft') AND post_content LIKE %s",
					'%[' . $wpdb->esc_like($settings['shortcode']) . '%marker="' . $wpdb->esc_like($marker->id) . '"%]%'
				));
				if ($results) {
					$shortcode_atts = array();
					$shortcode_atts['marker'] = $marker->id;
					if ($marker->openpopup == '1') {
						$shortcode_atts['highlight'] = $marker->id;
					}
					switch ($marker->basemap) {
						case 'osm_mapnik':
							switch ($settings['openstreetmap_variants']) {
								case 'osm-blackandwhite':
									$shortcode_atts['basemapDefault'] = 'osm';
									break;
								case 'osm-de':
									$shortcode_atts['basemapDefault'] = 'osmDe';
									break;
								case 'osm-france':
									$shortcode_atts['basemapDefault'] = 'osmFrance';
									break;
								case 'osm-hot':
									$shortcode_atts['basemapDefault'] = 'osmHot';
									break;
								default:
									$shortcode_atts['basemapDefault'] = 'osm';
									break;
							}
							break;
						case 'stamen_terrain':
							switch ($settings['stamen_terrain_flavor']) {
								case 'terrain-background':
									$shortcode_atts['basemapDefault'] = 'stamenTerrainBackground';
									break;
								case 'terrain-lines':
									$shortcode_atts['basemapDefault'] = 'stamenTerrainLines';
									break;
								default:
									$shortcode_atts['basemapDefault'] = 'stamenTerrain';
									break;
							}
							break;
						case 'stamen_toner':
							switch ($settings['stamen_toner_flavor']) {
								case 'toner-background':
									$converted['basemapDefault'] = 'stamenTonerBackground';
									break;
								case 'toner-hybrid':
									$converted['basemapDefault'] = 'stamenTonerHybrid';
									break;
								case 'toner-lines':
									$converted['basemapDefault'] = 'stamenTonerLines';
									break;
								case 'toner-lite':
									$converted['basemapDefault'] = 'stamenTonerLite';
									break;
								default:
									$converted['basemapDefault'] = 'stamenToner';
									break;
							}
							break;
						case 'stamen_watercolor':
							$shortcode_atts['basemapDefault'] = 'stamenWatercolor';
							break;
						case 'googleLayer_roadmap':
							$shortcode_atts['basemapDefault'] = 'googleRoadmap';
							break;
						case 'googleLayer_satellite':
							$shortcode_atts['basemapDefault'] = 'googleSatellite';
							break;
						case 'googleLayer_hybrid':
							$shortcode_atts['basemapDefault'] = 'googleHybrid';
							break;
						case 'googleLayer_terrain':
							$shortcode_atts['basemapDefault'] = 'googleTerrain';
							break;
						case 'bingroad':
							$shortcode_atts['basemapDefault'] = 'bingRoad';
							break;
						case 'bingaerial':
							$shortcode_atts['basemapDefault'] = 'bingAerial';
							break;
						case 'bingaerialwithlabels':
							$shortcode_atts['basemapDefault'] = 'bingAerialLabels';
							break;
						case 'ogdwien_basemap':
							$shortcode_atts['basemapDefault'] = 'basemapAt';
							break;
						case 'ogdwien_satellite':
							$shortcode_atts['basemapDefault'] = 'basemapAtSatellite';
							break;
						default:
							$shortcode_atts['basemapDefault'] = 'osm';
							break;
					}
					$shortcode_atts['width'] = $marker->mapwidth;
					$shortcode_atts['widthUnit'] = $marker->mapwidthunit;
					$shortcode_atts['height'] = $marker->mapheight;
					$shortcode_atts['panel'] = ($marker->panel == '1') ? 'true' : 'false';
					if ($marker->gpx_url) {
						$shortcode_atts['gpxUrl'] = $marker->gpx_url;
						$shortcode_atts['gpxMeta'] = ($marker->gpx_panel == '1') ? 'true' : 'false';
					}
					$shortcode = '[' . $settings['shortcode'];
					foreach ($shortcode_atts as $att => $value) {
						$shortcode .= ' ' . $att . '="' . $value . '"';
					}
					$shortcode .= ']';
					$marker_maps[$marker->id]['shortcode'] = $shortcode;
					$marker_maps[$marker->id]['warning'] = $marker->overlays_custom || $marker->overlays_custom2 || $marker->overlays_custom3 || $marker->overlays_custom4 || $marker->wms || $marker->wms2 || $marker->wms3 || $marker->wms4 || $marker->wms5 || $marker->wms6 || $marker->wms7 || $marker->wms8 || $marker->wms9 || $marker->wms10;
					foreach ($results as $result) {
						$marker_maps[$marker->id]['posts'][] = array(
							'title' => ($result->post_title) ? esc_html($result->post_title) : esc_html__('(no title)', 'mmp'),
							'link'  => get_permalink($result->ID),
							'edit'  => get_edit_post_link($result->ID)
						);
					}
				}
			}
		}

		ob_start();
		?>
		<?php if (count($marker_maps)): ?>
			<table class="mmp-migration-table">
				<tr>
					<th><?= esc_html__('ID', 'mmp') ?></th>
					<th><?= esc_html__('New shortcode', 'mmp') ?></th>
					<th><?= esc_html__('Used in content', 'mmp') ?></th>
				</tr>
				<?php foreach ($marker_maps as $id => $marker_map): ?>
					<tr>
						<td><?= $id ?></td>
						<td>
							<input type="text" class="mmp-migration-shortcode" value="<?= esc_html($marker_map['shortcode']) ?>" readonly="readonly" /><br />
							<?php if ($marker_map['warning']): ?>
								<span class="mmp-warning"><?= esc_html__('This marker uses custom overlays and/or WMS layers, which are unsupported for marker maps. Please create a map for this marker and replace the old marker shortcode with the new map shortcode.', 'mmp') ?></span><br />
							<?php endif; ?>
						</td>
						<td>
							<ul>
								<?php foreach ($marker_map['posts'] as $post): ?>
									<li><a href="<?= $post['link'] ?>" target="_blank"><?= $post['title'] ?></a> (<a href="<?= $post['edit'] ?>" target="_blank"><?= esc_html__('edit', 'mmp') ?></a>)</li>
								<?php endforeach; ?>
							</ul>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<p><?= esc_html__('No marker map shortcodes found - please click on "Start migration" to continue.', 'mmp') ?></p>
		<?php endif; ?>
		<?php
		$log = ob_get_clean();

		wp_send_json_success($log);
	}

	/**
	 * Executes the data migration
	 *
	 * @since 4.0
	 */
	public function data_migration() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$tools = MMP::get_instance('MMP\Menu\Tools');

		check_ajax_referer('mmp-tools-migration', 'nonce');

		if (!current_user_can('activate_plugins')) {
			wp_send_json_error();
		}

		$settings = get_option('leafletmapsmarker_options');
		if ($settings) {
			$map_defaults = $this->convert_map_defaults($settings);
			$new_settings = $this->convert_settings($settings);
		} else {
			$map_defaults = $mmp_settings->get_default_map_settings();
			$new_settings = $mmp_settings->get_default_settings();
		}
		update_option('mapsmarkerpro_settings', $new_settings);
		update_option('mapsmarkerpro_map_defaults', $map_defaults);

		set_transient('mapsmarkerpro_flush_rewrite_rules', true);

		$db->reset_tables();

		for ($i = 1; $i <= 3; $i++) {
			$index = ($i === 1) ? '' : $i;
			$data = array(
				'wms'     => 0,
				'overlay' => 0,
				'name'    => $settings["custom_basemap{$index}_name"],
				'url'     => $settings["custom_basemap{$index}_tileurl"],
				'options' => array(
					'subdomains'    => ($settings["custom_basemap{$index}_subdomains_enabled"] == 'yes') ? preg_replace('/[^a-z0-9]/i', '', $settings["custom_basemap{$index}_subdomains_names"]) : '',
					'minNativeZoom' => absint($settings["custom_basemap{$index}_minzoom"]),
					'maxNativeZoom' => absint($settings["custom_basemap{$index}_maxzoom"]),
					'attribution'   => $settings["custom_basemap{$index}_attribution"],
					'tms'           => ($settings["custom_basemap{$index}_tms"] == 'true'),
					'opacity'       => 1
				)
			);
			$data['options'] = json_encode($data['options']);
			$db->add_layer((object) $data);
		}

		for ($i = 1; $i <= 4; $i++) {
			$index = ($i === 1) ? '' : $i;
			$data = array(
				'wms'     => 0,
				'overlay' => 1,
				'name'    => $settings["overlays_custom{$index}_name"],
				'url'     => $settings["overlays_custom{$index}_tileurl"],
				'options' => array(
					'subdomains'    => ($settings["overlays_custom{$index}_subdomains_enabled"] == 'yes') ? preg_replace('/[^a-z0-9]/i', '', $settings["overlays_custom{$index}_subdomains_names"]) : '',
					'minNativeZoom' => absint($settings["overlays_custom{$index}_minzoom"]),
					'maxNativeZoom' => absint($settings["overlays_custom{$index}_maxzoom"]),
					'attribution'   => $settings["overlays_custom{$index}_attribution"],
					'tms'           => ($settings["overlays_custom{$index}_tms"] == 'true'),
					'opacity'       => abs(floatval($settings["overlays_custom{$index}_opacity"]))
				)
			);
			$data['options'] = json_encode($data['options']);
			$db->add_layer((object) $data);
		}

		for ($i = 1; $i <= 10; $i++) {
			$index = ($i === 1) ? '' : $i;
			$data = array(
				'wms'     => 1,
				'overlay' => 1,
				'name'    => wp_kses($settings["wms_wms{$index}_name"], array()),
				'url'     => $settings["wms_wms{$index}_baseurl"],
				'options' => array(
					'subdomains'    => ($settings["wms_wms{$index}_subdomains_enabled"] == 'yes') ? preg_replace('/[^a-z0-9]/i', '', $settings["wms_wms{$index}_subdomains_names"]) : '',
					'minNativeZoom' => 0,
					'maxNativeZoom' => absint($settings["global_maxzoom_level"]),
					'attribution'   => $settings["wms_wms{$index}_attribution"],
					'tms'           => ($settings["wms_wms{$index}_tms"] == 'true'),
					'opacity'       => 1,
					'layers'        => $settings["wms_wms{$index}_layers"],
					'styles'        => $settings["wms_wms{$index}_styles"],
					'format'        => $settings["wms_wms{$index}_format"],
					'transparent'   => ($settings["wms_wms{$index}_transparent"] == 'TRUE'),
					'version'       => $settings["wms_wms{$index}_version"]
				)
			);
			$data['options'] = json_encode($data['options']);
			$db->add_layer((object) $data);
		}

		$layers_count = $wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}leafletmapsmarker_layers WHERE id != 0");
		$layers_batches = ceil($layers_count / 1000);
		for ($i = 1; $i <= $layers_batches; $i++) {
			$batch_start = ($i - 1) * 1000;
			$layers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}leafletmapsmarker_layers WHERE id != 0 ORDER BY id LIMIT {$batch_start}, 1000");
			$layers_converted = array();
			foreach ($layers as $layer) {
				$layers_converted[] = $this->convert_layer($layer, $settings);
			}
			$db->add_maps($layers_converted);
		}

		$markers_count = $wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}leafletmapsmarker_markers");
		$markers_batches = ceil($markers_count / 1000);
		for ($i = 1; $i <= $markers_batches; $i++) {
			$batch_start = ($i - 1) * 1000;
			$markers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}leafletmapsmarker_markers ORDER BY id LIMIT {$batch_start}, 1000");
			$markers_converted = array();
			foreach ($markers as $marker) {
				$markers_converted[] = $this->convert_marker($marker);
				$map_ids = json_decode($marker->layer, true);
				$db->assign_maps_marker($map_ids, $marker->id);
			}
			$db->add_markers($markers_converted);
		}

		$this->copy_icons($settings);

		wp_send_json_success(esc_html__('Data migration completed successfully', 'mmp'));
	}

	/**
	 * Converts settings to the new format
	 *
	 * @since 4.0
	 */
	private function convert_settings($settings) {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		$keys = array(
			'googleApiKey'                      => 'google_maps_api_key',
			'googleLanguage'                    => 'google_maps_language_localization',
			'bingApiKey'                        => 'bingmaps_api_key',
			'bingCulture'                       => 'bingmaps_culture',
			'geocodingTypingDelay'              => 'geocoding_typing_delay',
			'geocodingMinChars'                 => 'geocoding_min_chars_search_autostart',
			'geocodingMapQuestApiKey'           => 'geocoding_mapquest_geocoding_api_key',
			'geocodingMapQuestBounds'           => 'geocoding_mapquest_geocoding_bounds_status',
			'geocodingMapQuestBoundsLat1'       => 'geocoding_mapquest_geocoding_bounds_lat1',
			'geocodingMapQuestBoundsLon1'       => 'geocoding_mapquest_geocoding_bounds_lon1',
			'geocodingMapQuestBoundsLat2'       => 'geocoding_mapquest_geocoding_bounds_lat2',
			'geocodingMapQuestBoundsLon2'       => 'geocoding_mapquest_geocoding_bounds_lon2',
			'geocodingGoogleAuthMethod'         => 'geocoding_google_geocoding_auth_method',
			'geocodingGoogleApiKey'             => 'geocoding_google_geocoding_api_key',
			'geocodingGoogleClient'             => 'geocoding_google_geocoding_premium_client',
			'geocodingGoogleSignature'          => 'geocoding_google_geocoding_premium_signature',
			'geocodingGoogleChannel'            => 'geocoding_google_geocoding_premium_channel',
			'geocodingGoogleLocation'           => 'geocoding_google_geocoding_location',
			'geocodingGoogleRadius'             => 'geocoding_google_geocoding_radius',
			'geocodingGoogleLanguage'           => 'geocoding_google_geocoding_language',
			'geocodingGoogleRegion'             => 'geocoding_google_geocoding_region',
			'geocodingGoogleComponents'         => 'geocoding_google_geocoding_components',
			'directionsProvider'                => 'directions_provider',
			'directionsGoogleType'              => 'directions_googlemaps_map_type',
			'directionsGoogleTraffic'           => 'directions_googlemaps_traffic',
			'directionsGoogleUnits'             => 'directions_googlemaps_distance_units',
			'directionsGoogleAvoidHighways'     => 'directions_googlemaps_route_type_highways',
			'directionsGoogleAvoidTolls'        => 'directions_googlemaps_route_type_tolls',
			'directionsGooglePublicTransport'   => 'directions_googlemaps_route_type_public_transport',
			'directionsGoogleWalking'           => 'directions_googlemaps_route_type_walking',
			'directionsGoogleOverview'          => 'directions_googlemaps_overview_map',
			'directionsOrsRoute'                => 'directions_ors_routeWeigh',
			'directionsOrsType'                 => 'directions_ors_routeOpt',
			'betaTesting'                       => 'misc_betatest',
			'affiliateId'                       => 'affiliate_id',
			'backlinks'                         => 'misc_backlinks',
			'iconSizeX'                         => 'defaults_marker_icon_iconsize_x',
			'iconSizeY'                         => 'defaults_marker_icon_iconsize_y',
			'iconAnchorX'                       => 'defaults_marker_icon_iconanchor_x',
			'iconAnchorY'                       => 'defaults_marker_icon_iconanchor_y',
			'iconPopupAnchorX'                  => 'defaults_marker_icon_popupanchor_x',
			'iconPopupAnchorY'                  => 'defaults_marker_icon_popupanchor_y',
			'sitemapGoogle'                     => 'xml_sitemaps_status',
			'sitemapGoogleExclude'              => 'xml_sitemaps_exclude_layers',
			'sitemapGooglePriority'             => 'xml_sitemaps_priority_layers',
			'sitemapGoogleFrequency'            => 'xml_sitemaps_change_frequency_layers',
			'shortcode'                         => 'shortcode',
			'tinyMce'                           => 'misc_tinymce_button',
			'adminBar'                          => 'admin_bar_integration',
			'dashboardWidget'                   => 'misc_admin_dashboard_widget',
			'permalinkSlug'                     => 'rewrite_slug',
			'permalinkBaseUrl'                  => 'rewrite_baseurl',
			'popupKses'                         => 'wp_kses_status'
		);

		// Needed because checkboxes in 3.1.1 were only saved when they were checked
		// Therefore, checkboxes that are unset must be considered false
		$checkboxes = array(
			'directions_googlemaps_route_type_highways',
			'directions_googlemaps_route_type_tolls',
			'directions_googlemaps_route_type_public_transport',
			'directions_googlemaps_route_type_walking'
		);

		$converted = array();
		foreach ($keys as $new => $old) {
			if (in_array($old, $checkboxes)) {
				$converted[$new] = false;
			}
			if (!isset($settings[$old])) {
				continue;
			}

			$converted[$new] = $settings[$old];
		}

		if (isset($settings['geocoding_provider'])) {
			switch ($settings['geocoding_provider']) {
				case 'algolia-places':
				case 'photon':
					$converted['geocodingProvider'] = 'none';
					break;
				case 'mapquest-geocoding':
					$converted['geocodingProvider'] = 'mapquest';
					break;
				case 'google-geocoding':
					$converted['geocodingProvider'] = 'google';
					break;
				default:
					break;
			}
		}

		if (isset($settings['misc_plugin_language_area'])) {
			switch ($settings['misc_plugin_language_area']) {
				case 'backend':
					$converted['pluginLanguageAdmin'] = $settings['misc_plugin_language'];
					break;
				case 'frontend':
					$converted['pluginLanguageFrontend'] = $settings['misc_plugin_language'];
					break;
				case 'both':
					$converted['pluginLanguageAdmin'] = $settings['misc_plugin_language'];
					$converted['pluginLanguageFrontend'] = $settings['misc_plugin_language'];
					break;
				default:
					break;
			}
		}

		return $mmp_settings->validate_settings($converted);
	}

	/**
	 * Converts map defaults to the new format
	 *
	 * @since 4.0
	 */
	private function convert_map_defaults($settings) {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		$keys = array(
			'width'                        => 'defaults_layer_mapwidth',
			'widthUnit'                    => 'defaults_layer_mapwidthunit',
			'height'                       => 'defaults_layer_mapheight',
			'lat'                          => 'defaults_layer_lat',
			'lng'                          => 'defaults_layer_lon',
			'zoom'                         => 'defaults_layer_zoom',
			'maxZoom'                      => 'global_maxzoom_level',
			'panel'                        => 'defaults_layer_panel',
			'panelColor'                   => 'defaults_layer_panel_background_color',
			'panelFs'                      => 'defaults_layer_panel_fullscreen',
			'panelGeoJson'                 => 'defaults_layer_panel_geojson',
			'panelKml'                     => 'defaults_layer_panel_kml',
			'panelGeoRss'                  => 'defaults_layer_panel_georss',
			'basemapEdgeBufferTiles'       => 'edgeBufferTiles',
			'basemapGoogleStyles'          => 'google_styling_json',
			'locateDrawCircle'             => 'geolocate_drawCircle',
			'locateDrawMarker'             => 'geolocate_drawMarker',
			'locateKeepCurrentZoomLevel'   => 'geolocate_keepCurrentZoomLevel',
			'locateClickBehaviorInView'    => 'geolocate_clickBehavior_inView',
			'locateClickBehaviorOutOfView' => 'geolocate_clickBehavior_outOfView',
			'locateMetric'                 => 'geolocate_units',
			'locateShowPopup'              => 'geolocate_showPopup',
			'locateAutostart'              => 'geolocate_autostart',
			'scaleMaxWidth'                => 'map_scale_control_maxwidth',
			'scaleMetric'                  => 'map_scale_control_metric',
			'scaleImperial'                => 'map_scale_control_imperial',
			'filtersName'                  => 'mlm_filter_controlbox_name',
			'filtersIcon'                  => 'mlm_filter_controlbox_icon',
			'filtersCount'                 => 'mlm_filter_controlbox_markercount',
			'minimapWidth'                 => 'minimap_width',
			'minimapHeight'                => 'minimap_height',
			'minimapCollapsedWidth'        => 'minimap_collapsedWidth',
			'minimapCollapsedHeight'       => 'minimap_collapsedHeight',
			'minimapZoomLevelOffset'       => 'minimap_zoomLevelOffset',
			'minimapZoomLevelFixed'        => 'minimap_zoomLevelFixed',
			'markerOpacity'                => 'defaults_marker_icon_opacity',
			'clustering'                   => 'defaults_layer_clustering',
			'showCoverageOnHover'          => 'clustering_showCoverageOnHover',
			'disableClusteringAtZoom'      => 'clustering_disableClusteringAtZoom',
			'maxClusterRadius'             => 'clustering_maxClusterRadius',
			'singleMarkerMode'             => 'clustering_singleMarkerMode',
			'spiderfyDistanceMultiplier'   => 'clustering_spiderfyDistanceMultiplier',
			'tooltip'                      => 'marker_tooltip_status',
			'tooltipDirection'             => 'marker_tooltip_direction',
			'tooltipPermanent'             => 'marker_tooltip_permanent',
			'tooltipSticky'                => 'marker_tooltip_sticky',
			'tooltipOpacity'               => 'marker_tooltip_opacity',
			'popupOpenOnHover'             => 'defaults_marker_popups_rise_on_hover',
			'popupCenterOnMap'             => 'defaults_marker_popups_center_map',
			'popupMarkername'              => 'defaults_marker_popups_add_markername',
			'popupAddress'                 => 'directions_popuptext_panel',
			'popupDirections'              => 'directions_popuptext_panel',
			'popupMinWidth'                => 'defaults_marker_popups_minwidth',
			'popupMaxWidth'                => 'defaults_marker_popups_maxwidth',
			'popupMaxHeight'               => 'defaults_marker_popups_maxheight',
			'popupCloseButton'             => 'defaults_marker_popups_closebutton',
			'popupAutoClose'               => 'misc_map_closepopuponclick',
			'list'                         => 'defaults_layer_listmarkers',
			'listIcon'                     => 'defaults_layer_listmarkers_show_icon',
			'listName'                     => 'defaults_layer_listmarkers_show_markername',
			'listPopup'                    => 'defaults_layer_listmarkers_show_popuptext',
			'listAddress'                  => 'defaults_layer_listmarkers_show_address',
			'listDistance'                 => 'defaults_layer_listmarkers_show_distance',
			'listDistancePrecision'        => 'defaults_layer_listmarkers_show_distance_precision',
			'listLimit'                    => 'defaults_layer_listmarkers_limit',
			'listDir'                      => 'defaults_layer_listmarkers_api_directions',
			'listFs'                       => 'defaults_layer_listmarkers_api_fullscreen',
			'responsive'                   => 'enabled',
			'inertia'                      => 'map_panning_inertia_options_inertia',
			'inertiaDeceleration'          => 'map_panning_inertia_options_inertiadeceleration',
			'inertiaMaxSpeed'              => 'map_panning_inertia_options_inertiamaxspeed',
			'keyboard'                     => 'map_keyboard_navigation_options_keyboard',
			'keyboardPanDelta'             => 'map_keyboard_navigation_options_keyboardpandelta',
			'scrollWheelZoom'              => 'misc_map_scrollwheelzoom',
			'doubleClickZoom'              => 'misc_map_doubleclickzoom',
			'touchZoom'                    => 'misc_map_touchzoom',
			'boxZoom'                      => 'map_interaction_options_boxzoom',
			'bounceAtZoomLimits'           => 'map_interaction_options_bounceatzoomlimits',
			'gpxMetaUnits'                 => 'gpx_metadata_units',
			'gpxMetaInterval'              => 'gpx_max_point_interval',
			'gpxMetaName'                  => 'gpx_metadata_name',
			'gpxMetaStart'                 => 'gpx_metadata_start',
			'gpxMetaEnd'                   => 'gpx_metadata_end',
			'gpxMetaTotal'                 => 'gpx_metadata_duration_total',
			'gpxMetaMoving'                => 'gpx_metadata_duration_moving',
			'gpxMetaDistance'              => 'gpx_metadata_distance',
			'gpxMetaPace'                  => 'gpx_metadata_avpace',
			'gpxMetaHeartRate'             => 'gpx_metadata_avhr',
			'gpxMetaDownload'              => 'gpx_metadata_gpx_download',
			'gpxShowStartIcon'             => 'gpx_icons_status',
			'gpxShowEndIcon'               => 'gpx_icons_status',
			'gpxTrackSmoothFactor'         => 'gpx_track_smoothFactor',
			'gpxTrackColor'                => 'gpx_track_color',
			'gpxTrackWeight'               => 'gpx_track_weight',
			'gpxTrackOpacity'              => 'gpx_track_opacity'
		);

		// Needed because checkboxes in 3.1.1 were only saved when they were checked
		// Therefore, checkboxes that are unset must be considered false
		$checkboxes = array(
			'defaults_layer_listmarkers_show_icon',
			'defaults_layer_listmarkers_show_markername',
			'defaults_layer_listmarkers_show_popuptext',
			'defaults_layer_listmarkers_show_address',
			'defaults_layer_listmarkers_show_distance',
			'defaults_layer_listmarkers_api_directions',
			'defaults_layer_listmarkers_api_fullscreen',
			'mlm_filter_controlbox_name',
			'mlm_filter_controlbox_icon',
			'mlm_filter_controlbox_markercount',
			'gpx_metadata_name',
			'gpx_metadata_start',
			'gpx_metadata_end',
			'gpx_metadata_duration_total',
			'gpx_metadata_duration_moving',
			'gpx_metadata_distance',
			'gpx_metadata_avpace',
			'gpx_metadata_avhr',
			'gpx_metadata_gpx_download'
		);

		$converted = array();
		foreach ($keys as $new => $old) {
			if (!isset($settings[$old])) {
				if (in_array($old, $checkboxes)) {
					$converted[$new] = false;
				}
				continue;
			}

			$converted[$new] = $settings[$old];
		}

		$basemaps = array(
			array(
				'controlbox' => 'controlbox_osm_mapnik',
				'variant'    => 'openstreetmap_variants',
				'variants'   => array(
					'osm'       => 'osm-mapnik',
					'osmDe'     => 'osm-de',
					'osmFrance' => 'osm-france',
					'osmHot'    => 'osm-hot'
				)
			),
			array(
				'controlbox' => 'controlbox_stamen_terrain',
				'variant'    => 'stamen_terrain_flavor',
				'variants'   => array(
					'stamenTerrain'           => 'terrain',
					'stamenTerrainBackground' => 'terrain-background',
					'stamenTerrainLines'      => 'terrain-lines'
				)
			),
			array(
				'controlbox' => 'controlbox_stamen_toner',
				'variant'    => 'stamen_toner_flavor',
				'variants'   => array(
					'stamenToner'           => 'toner',
					'stamenTonerBackground' => 'toner-background',
					'stamenTonerHybrid'     => 'toner-hybrid',
					'stamenTonerLines'      => 'toner-lines',
					'stamenTonerLite'       => 'toner-lite'
				)
			),
			array(
				'controlbox' => 'controlbox_stamen_watercolor',
				'key'        => 'stamenWatercolor'
			),
			array(
				'controlbox' => 'controlbox_googleLayer_roadmap',
				'key'        => 'googleRoadmap'
			),
			array(
				'controlbox' => 'controlbox_googleLayer_satellite',
				'key'        => 'googleSatellite'
			),
			array(
				'controlbox' => 'controlbox_googleLayer_hybrid',
				'key'        => 'googleHybrid'
			),
			array(
				'controlbox' => 'controlbox_googleLayer_terrain',
				'key'        => 'googleTerrain'
			),
			array(
				'controlbox' => 'controlbox_bingroad',
				'key'        => 'bingRoad'
			),
			array(
				'controlbox' => 'controlbox_bingaerial',
				'key'        => 'bingAerial'
			),
			array(
				'controlbox' => 'controlbox_bingaerialwithlabels',
				'key'        => 'bingAerialLabels'
			),
			array(
				'controlbox' => 'controlbox_ogdwien_basemap',
				'key'        => 'basemapAt'
			),
			array(
				'controlbox' => 'controlbox_ogdwien_satellite',
				'key'        => 'basemapAtSatellite'
			)
		);

		$converted['basemaps'] = array();
		foreach ($basemaps as $basemap) {
			if (!isset($settings[$basemap['controlbox']]) || $settings[$basemap['controlbox']] != '1') {
				continue;
			}
			if (isset($basemap['variant'])) {
				if (!isset($settings[$basemap['variant']])) {
					continue;
				}
				foreach ($basemap['variants'] as $new => $old) {
					if ($settings[$basemap['variant']] == $old) {
						$converted['basemaps'][] = $new;
						break;
					}
				}
			} else {
				$converted['basemaps'][] = $basemap['key'];
			}
		}

		$default_basemaps = array(
			'osm'                => 'osm_mapnik',
			'stamenTerrain'      => 'stamen_terrain',
			'stamenToner'        => 'stamen_toner',
			'stamenWatercolor'   => 'stamen_watercolor',
			'googleRoadmap'      => 'googleLayer_roadmap',
			'googleSatellite'    => 'googleLayer_satellite',
			'googleHybrid'       => 'googleLayer_hybrid',
			'googleTerrain'      => 'googleLayer_terrain',
			'bingRoad'           => 'bingroad',
			'bingAerial'         => 'bingaerial',
			'bingAerialLabels'   => 'bingaerialwithlabels',
			'basemapAt'          => 'ogdwien_basemap',
			'basemapAtSatellite' => 'ogdwien_satellite'
		);

		if (isset($settings['standard_basemap'])) {
			$key = array_search($settings['standard_basemap'], $default_basemaps);
			if ($key !== false) {
				$converted['basemapDefault'] = $key;
			}
		}

		if (isset($settings['misc_map_zoomcontrol'])) {
			$converted['zoomControlPosition'] = ($settings['misc_map_zoomcontrol'] == 'false') ? 'hidden' : 'topleft';
		}
		if (isset($settings['map_fullscreen_button']) && isset($settings['map_fullscreen_button_position'])) {
			$converted['fullscreenPosition'] = ($settings['map_fullscreen_button'] == 'false') ? 'hidden' : $settings['map_fullscreen_button_position'];
		}
		if (isset($settings['map_home_button']) && isset($settings['map_home_button_position'])) {
			$converted['resetPosition'] = ($settings['map_home_button'] == 'false') ? 'hidden' : $settings['map_home_button_position'];
			$converted['resetOnDemand'] = ($settings['map_home_button'] == 'true-ondemand');
		}
		if (isset($settings['geolocate_status']) && isset($settings['geolocate_position'])) {
			$converted['locatePosition'] = ($settings['geolocate_status'] == 'false') ? 'hidden' : $settings['geolocate_position'];
		}
		if (isset($settings['geolocate_setView'])) {
			$converted['locateSetView'] = ($settings['geolocate_setView'] == 'false') ? false : $settings['geolocate_setView'];
		}
		if (isset($settings['map_scale_control']) && isset($settings['map_scale_control_position'])) {
			$converted['scalePosition'] = ($settings['map_scale_control'] == 'disabled') ? 'hidden' : $settings['map_scale_control_position'];
		}
		if (isset($settings['defaults_layer_controlbox'])) {
			$converted['layersPosition'] = ($settings['defaults_layer_controlbox'] == '0') ? 'hidden' : 'topright';
		}
		if (isset($settings['defaults_layer_controlbox'])) {
			$converted['layersCollapsed'] = ($settings['defaults_layer_controlbox'] == 'collapsed') ? 'collapsed' : 'expanded';
		}
		if (isset($settings['defaults_layer_mlm_filter_controlbox']) && isset($settings['mlm_filter_controlbox_position'])) {
			$converted['filtersPosition'] = ($settings['defaults_layer_mlm_filter_controlbox'] == '0') ? 'hidden' : $settings['mlm_filter_controlbox_position'];
		}
		if (isset($settings['defaults_layer_mlm_filter_controlbox'])) {
			$converted['filtersCollapsed'] = ($settings['defaults_layer_mlm_filter_controlbox'] == 'collapsed') ? 'collapsed' : 'expanded';
		}
		if (isset($settings['mlm_filter_active_orderby'])) {
			switch ($settings['mlm_filter_active_orderby']) {
				case 'name':
					$converted['filtersOrderBy'] = 'name';
					break;
				case 'markercount':
					$converted['filtersOrderBy'] = 'count';
					break;
				default:
					$converted['filtersOrderBy'] = 'id';
					break;
			}
		}
		if (isset($settings['mlm_filter_active_sort_order'])) {
			$converted['filtersSortOrder'] = ($settings['mlm_filter_active_sort_order'] == 'ASC') ? 'asc' : 'desc';
		}
		if (isset($settings['minimap_status']) && isset($settings['minimap_position'])) {
			$converted['minimapPosition'] = ($settings['minimap_status'] == 'hidden') ? 'hidden' : $settings['minimap_position'];
		}
		if (isset($settings['minimap_status'])) {
			$converted['minimapMinimized'] = ($settings['minimap_status'] == 'collapsed') ? 'collapsed' : 'expanded';
		}
		if (isset($settings['defaults_layer_listmarkers_action_bar'])) {
			$converted['listSearch'] = ($settings['defaults_layer_listmarkers_action_bar'] != 'hide');
		}
		if (isset($settings['defaults_layer_listmarkers_show_distance_unit'])) {
			$converted['listDistanceUnit'] = ($settings['defaults_layer_listmarkers_show_distance_unit'] == 'km') ? 'metric' : 'imperial';
		}
		if (isset($settings['defaults_layer_listmarkers_order_by'])) {
			switch ($settings['defaults_layer_listmarkers_order_by']) {
				case 'm.markername':
					$converted['listOrderBy'] = 'name';
					break;
				case 'm.address':
					$converted['listOrderBy'] = 'address';
					break;
				case 'distance_current_position':
					$converted['listOrderBy'] = 'distance';
					break;
				default:
					$converted['listOrderBy'] = 'id';
					break;
			}
		}
		if (isset($settings['defaults_layer_listmarkers_sort_order'])) {
			$converted['listSortOrder'] = ($settings['defaults_layer_listmarkers_sort_order'] == 'ASC') ? 'asc' : 'desc';
		}
		if (isset($settings['defaults_layer_listmarkers_link_action'])) {
			switch ($settings['defaults_layer_listmarkers_link_action']) {
				case 'disabled':
					$converted['listAction'] = 'none';
					break;
				case 'setview-only':
					$converted['listAction'] = 'setview';
					break;
				default:
					$converted['listAction'] = 'popup';
					break;
			}
		}
		if (isset($settings['misc_map_dragging'])) {
			$converted['gestureHandling'] = ($settings['misc_map_dragging'] == 'false-touch');
		}
		if (isset($settings['misc_map_dragging'])) {
			$converted['dragging'] = ($settings['misc_map_dragging'] != 'false');
		}

		return $mmp_settings->validate_map_settings($converted);
	}

	/**
	 * Converts a layer to the new format
	 *
	 * @since 4.0
	 */
	private function convert_layer($layer, $settings) {
		global $wpdb;

		$converted['id'] = absint($layer->id);
		$converted['name'] = stripslashes($layer->name);
		$converted['settings'] = get_option('mapsmarkerpro_map_defaults');
		$converted['settings']['width'] = absint($layer->mapwidth);
		$converted['settings']['widthUnit'] = ($layer->mapwidthunit === 'px') ? 'px' : '%';
		$converted['settings']['height'] = absint($layer->mapheight);
		$converted['settings']['panel'] = ($layer->panel) ? true : false;
		$converted['settings']['lat'] = floatval($layer->layerviewlat);
		$converted['settings']['lng'] = floatval($layer->layerviewlon);
		$converted['settings']['zoom'] = floatval($layer->layerzoom);
		$converted['settings']['basemaps'] = array();
		if ($settings['controlbox_osm_mapnik'] == '1') {
			switch ($settings['openstreetmap_variants']) {
				case 'osm-blackandwhite':
					$converted['settings']['basemaps'][] = 'osm';
					break;
				case 'osm-de':
					$converted['settings']['basemaps'][] = 'osmDe';
					break;
				case 'osm-france':
					$converted['settings']['basemaps'][] = 'osmFrance';
					break;
				case 'osm-hot':
					$converted['settings']['basemaps'][] = 'osmHot';
					break;
				default:
					$converted['settings']['basemaps'][] = 'osm';
					break;
			}
		}
		if ($settings['controlbox_stamen_terrain'] == '1') {
			switch ($settings['stamen_terrain_flavor']) {
				case 'terrain-background':
					$converted['settings']['basemaps'][] = 'stamenTerrainBackground';
					break;
				case 'terrain-lines':
					$converted['settings']['basemaps'][] = 'stamenTerrainLines';
					break;
				default:
					$converted['settings']['basemaps'][] = 'stamenTerrain';
					break;
			}
		}
		if ($settings['controlbox_stamen_toner'] == '1') {
			switch ($settings['stamen_toner_flavor']) {
				case 'toner-background':
					$converted['settings']['basemaps'][] = 'stamenTonerBackground';
					break;
				case 'toner-hybrid':
					$converted['settings']['basemaps'][] = 'stamenTonerHybrid';
					break;
				case 'toner-lines':
					$converted['settings']['basemaps'][] = 'stamenTonerLines';
					break;
				case 'toner-lite':
					$converted['settings']['basemaps'][] = 'stamenTonerLite';
					break;
				default:
					$converted['settings']['basemaps'][] = 'stamenToner';
					break;
			}
		}
		if ($settings['controlbox_stamen_watercolor'] == '1') {
			$converted['settings']['basemaps'][] = 'stamenWatercolor';
		}
		if ($settings['controlbox_googleLayer_roadmap'] == '1') {
			$converted['settings']['basemaps'][] = 'googleRoadmap';
		}
		if ($settings['controlbox_googleLayer_satellite'] == '1') {
			$converted['settings']['basemaps'][] = 'googleSatellite';
		}
		if ($settings['controlbox_googleLayer_hybrid'] == '1') {
			$converted['settings']['basemaps'][] = 'googleHybrid';
		}
		if ($settings['controlbox_googleLayer_terrain'] == '1') {
			$converted['settings']['basemaps'][] = 'googleTerrain';
		}
		if ($settings['controlbox_bingroad'] == '1') {
			$converted['settings']['basemaps'][] = 'bingRoad';
		}
		if ($settings['controlbox_bingaerial'] == '1') {
			$converted['settings']['basemaps'][] = 'bingAerial';
		}
		if ($settings['controlbox_bingaerialwithlabels'] == '1') {
			$converted['settings']['basemaps'][] = 'bingAerialLabels';
		}
		if ($settings['controlbox_ogdwien_basemap'] == '1') {
			$converted['settings']['basemaps'][] = 'basemapAt';
		}
		if ($settings['controlbox_ogdwien_satellite'] == '1') {
			$converted['settings']['basemaps'][] = 'basemapAtSatellite';
		}
		for ($i = 1; $i <= 3; $i++) {
			$index = ($i === 1) ? '' : $i;
			if (isset($settings["controlbox_custom_basemap{$index}"]) && $settings["controlbox_custom_basemap{$index}"] == '1') {
				$converted['settings']['basemaps'][] = $i;
			}
		}
		$converted['settings']['overlays'] = array();
		for ($i = 1; $i <= 4; $i++) {
			$index = ($i === 1) ? '' : $i;
			if ($layer->overlays_custom . $index == '1') {
				$converted['settings']['overlays'][] = $i + 3;
			}
		}
		for ($i = 1; $i <= 10; $i++) {
			$index = ($i === 1) ? '' : $i;
			if ($layer->wms . $index == '1') {
				$converted['settings']['overlays'][] = $i + 7;
			}
		}
		switch ($layer->basemap) {
			case 'osm_mapnik':
				switch ($settings['openstreetmap_variants']) {
					case 'osm-blackandwhite':
						$converted['settings']['basemapDefault'] = 'osm';
						break;
					case 'osm-de':
						$converted['settings']['basemapDefault'] = 'osmDe';
						break;
					case 'osm-france':
						$converted['settings']['basebasemapDefaultmaps'] = 'osmFrance';
						break;
					case 'osm-hot':
						$converted['settings']['basemapDefault'] = 'osmHot';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'osm';
						break;
				}
				break;
			case 'stamen_terrain':
				switch ($settings['stamen_terrain_flavor']) {
					case 'terrain-background':
						$converted['settings']['basemapDefault'] = 'stamenTerrainBackground';
						break;
					case 'terrain-lines':
						$converted['settings']['basemapDefault'] = 'stamenTerrainLines';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'stamenTerrain';
						break;
				}
				break;
			case 'stamen_toner':
				switch ($settings['stamen_toner_flavor']) {
					case 'toner-background':
						$converted['settings']['basemapDefault'] = 'stamenTonerBackground';
						break;
					case 'toner-hybrid':
						$converted['settings']['basemapDefault'] = 'stamenTonerHybrid';
						break;
					case 'toner-lines':
						$converted['settings']['basemapDefault'] = 'stamenTonerLines';
						break;
					case 'toner-lite':
						$converted['settings']['basemapDefault'] = 'stamenTonerLite';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'stamenToner';
						break;
				}
				break;
			case 'stamen_watercolor':
				$converted['settings']['basemapDefault'] = 'stamenWatercolor';
				break;
			case 'googleLayer_roadmap':
				$converted['settings']['basemapDefault'] = 'googleRoadmap';
				break;
			case 'googleLayer_satellite':
				$converted['settings']['basemapDefault'] = 'googleSatellite';
				break;
			case 'googleLayer_hybrid':
				$converted['settings']['basemapDefault'] = 'googleHybrid';
				break;
			case 'googleLayer_terrain':
				$converted['settings']['basemapDefault'] = 'googleTerrain';
				break;
			case 'bingroad':
				$converted['settings']['basemapDefault'] = 'bingRoad';
				break;
			case 'bingaerial':
				$converted['settings']['basemapDefault'] = 'bingAerial';
				break;
			case 'bingaerialwithlabels':
				$converted['settings']['basemapDefault'] = 'bingAerialLabels';
				break;
			case 'ogdwien_basemap':
				$converted['settings']['basemapDefault'] = 'basemapAt';
				break;
			case 'ogdwien_satellite':
				$converted['settings']['basemapDefault'] = 'basemapAtSatellite';
				break;
			case 'custom_basemap':
				$converted['settings']['basemapDefault'] = '1';
				break;
			case 'custom_basemap2':
				$converted['settings']['basemapDefault'] = '2';
				break;
			case 'custom_basemap3':
				$converted['settings']['basemapDefault'] = '3';
				break;
			default:
				$converted['settings']['basemapDefault'] = 'osm';
				break;
		}
		$converted['settings']['layers'] = ($layer->controlbox) ? true : false;
		$converted['settings']['layersCollapsed'] = ($layer->controlbox === '2') ? true : false;
		$converted['settings']['filters'] = ($layer->mlm_filter) ? true : false;
		$converted['settings']['filtersCollapsed'] = ($layer->mlm_filter === '2') ? true : false;
		$converted['settings']['clustering'] = ($layer->clustering) ? true : false;
		$converted['settings']['list'] = ($layer->listmarkers) ? true : false;
		$converted['settings']['gpxUrl'] = $layer->gpx_url;
		$converted['settings']['gpxMeta'] = ($layer->gpx_panel) ? true: false;
		$converted['settings']['filtersAllMarkers'] = ($layer->multi_layer_map && $layer->multi_layer_map_list === 'all') ? true : false;
		$converted['settings'] = json_encode($converted['settings']);
		$converted['filters'] = array();
		if ($layer->multi_layer_map && $layer->multi_layer_map_list && $layer->multi_layer_map_list !== 'all') {
			$ids = explode(',', $layer->multi_layer_map_list);
			foreach ($ids as $id) {
				$converted['filters'][$id] = array(
					'index' => 0,
					'active' => true,
					'name' => $wpdb->get_var("SELECT `name` FROM {$wpdb->prefix}leafletmapsmarker_layers WHERE id = $id"),
					'icon' => ''
				);
			}
			$filters = json_decode($layer->mlm_filter_details);
			if ($filters !== null) {
				foreach ($filters as $key => $filter) {
					if (!is_object($filter) || !property_exists($filter, 'status') || !property_exists($filter, 'name') || !property_exists($filter, 'icon')) {
						continue;
					}
					$converted['filters'][$key] = array(
						'index' => 0,
						'active' => ($filter->status === 'active') ? true : false,
						'name' => $filter->name,
						'icon' => str_replace('/leaflet-maps-marker-icons/', '/maps-marker-pro/icons/', $filter->icon)
					);
				}
			}
			$index = 0;
			foreach ($converted['filters'] as $key => $filter) {
				$converted['filters'][$key]['index'] = $index++;
			}
		}
		$converted['filters'] = json_encode($converted['filters'], JSON_FORCE_OBJECT);
		$converted['geojson'] = '{}';
		$user = get_user_by('login', $layer->createdby);
		$converted['created_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['created_on'] = $layer->createdon;
		$user = get_user_by('login', $layer->updatedby);
		$converted['updated_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['updated_on'] = $layer->updatedon;

		return $converted;
	}

	/**
	 * Converts a marker to the new format
	 *
	 * @since 4.0
	 */
	private function convert_marker($marker) {
		$converted['id'] = absint($marker->id);
		$converted['name'] = stripslashes($marker->markername);
		$converted['address'] = stripslashes($marker->address);
		$converted['lat'] = floatval($marker->lat);
		$converted['lng'] = floatval($marker->lon);
		$converted['zoom'] = floatval($marker->zoom);
		$converted['icon'] = $marker->icon;
		$converted['popup'] = $this->sanitize_popup($marker->popuptext);
		$converted['link'] = '';
		$converted['blank'] = '1';
		$converted['schedule_from'] = null;
		$converted['schedule_until'] = null;
		$user = get_user_by('login', $marker->createdby);
		$converted['created_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['created_on'] = $marker->createdon;
		$user = get_user_by('login', $marker->updatedby);
		$converted['updated_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['updated_on'] = $marker->updatedon;

		return $converted;
	}

	/**
	 * Converts a marker to a map
	 *
	 * @since 4.6
	 */
	private function convert_marker_to_map($marker, $settings) {
		global $wpdb;

		$converted['name'] = stripslashes($marker->markername);
		$converted['settings'] = get_option('mapsmarkerpro_map_defaults');
		$converted['settings']['width'] = absint($marker->mapwidth);
		$converted['settings']['widthUnit'] = ($marker->mapwidthunit === 'px') ? 'px' : '%';
		$converted['settings']['height'] = absint($marker->mapheight);
		$converted['settings']['panel'] = ($marker->panel) ? true : false;
		$converted['settings']['panelColor'] = $settings['defaults_marker_panel_background_color'];
		$converted['settings']['panelFs'] = (isset($settings['defaults_marker_panel_fullscreen']) && $settings['defaults_marker_panel_fullscreen']);
		$converted['settings']['panelGeoJson'] = (isset($settings['defaults_marker_panel_geojson']) && $settings['defaults_marker_panel_geojson']);
		$converted['settings']['panelKml'] = (isset($settings['defaults_marker_panel_kml']) && $settings['defaults_marker_panel_kml']);
		$converted['settings']['panelGeoRss'] = (isset($settings['defaults_marker_panel_georss']) && $settings['defaults_marker_panel_georss']);
		$converted['settings']['lat'] = floatval($marker->lat);
		$converted['settings']['lng'] = floatval($marker->lon);
		$converted['settings']['zoom'] = floatval($marker->zoom);
		$converted['settings']['basemaps'] = array();
		if ($settings['controlbox_osm_mapnik'] == '1') {
			switch ($settings['openstreetmap_variants']) {
				case 'osm-blackandwhite':
					$converted['settings']['basemaps'][] = 'osm';
					break;
				case 'osm-de':
					$converted['settings']['basemaps'][] = 'osmDe';
					break;
				case 'osm-france':
					$converted['settings']['basemaps'][] = 'osmFrance';
					break;
				case 'osm-hot':
					$converted['settings']['basemaps'][] = 'osmHot';
					break;
				default:
					$converted['settings']['basemaps'][] = 'osm';
					break;
			}
		}
		if ($settings['controlbox_stamen_terrain'] == '1') {
			switch ($settings['stamen_terrain_flavor']) {
				case 'terrain-background':
					$converted['settings']['basemaps'][] = 'stamenTerrainBackground';
					break;
				case 'terrain-lines':
					$converted['settings']['basemaps'][] = 'stamenTerrainLines';
					break;
				default:
					$converted['settings']['basemaps'][] = 'stamenTerrain';
					break;
			}
		}
		if ($settings['controlbox_stamen_toner'] == '1') {
			switch ($settings['stamen_toner_flavor']) {
				case 'toner-background':
					$converted['settings']['basemaps'][] = 'stamenTonerBackground';
					break;
				case 'toner-hybrid':
					$converted['settings']['basemaps'][] = 'stamenTonerHybrid';
					break;
				case 'toner-lines':
					$converted['settings']['basemaps'][] = 'stamenTonerLines';
					break;
				case 'toner-lite':
					$converted['settings']['basemaps'][] = 'stamenTonerLite';
					break;
				default:
					$converted['settings']['basemaps'][] = 'stamenToner';
					break;
			}
		}
		if ($settings['controlbox_stamen_watercolor'] == '1') {
			$converted['settings']['basemaps'][] = 'stamenWatercolor';
		}
		if ($settings['controlbox_googleLayer_roadmap'] == '1') {
			$converted['settings']['basemaps'][] = 'googleRoadmap';
		}
		if ($settings['controlbox_googleLayer_satellite'] == '1') {
			$converted['settings']['basemaps'][] = 'googleSatellite';
		}
		if ($settings['controlbox_googleLayer_hybrid'] == '1') {
			$converted['settings']['basemaps'][] = 'googleHybrid';
		}
		if ($settings['controlbox_googleLayer_terrain'] == '1') {
			$converted['settings']['basemaps'][] = 'googleTerrain';
		}
		if ($settings['controlbox_bingroad'] == '1') {
			$converted['settings']['basemaps'][] = 'bingRoad';
		}
		if ($settings['controlbox_bingaerial'] == '1') {
			$converted['settings']['basemaps'][] = 'bingAerial';
		}
		if ($settings['controlbox_bingaerialwithlabels'] == '1') {
			$converted['settings']['basemaps'][] = 'bingAerialLabels';
		}
		if ($settings['controlbox_ogdwien_basemap'] == '1') {
			$converted['settings']['basemaps'][] = 'basemapAt';
		}
		if ($settings['controlbox_ogdwien_satellite'] == '1') {
			$converted['settings']['basemaps'][] = 'basemapAtSatellite';
		}
		for ($i = 1; $i <= 3; $i++) {
			$index = ($i === 1) ? '' : $i;
			if (isset($settings["controlbox_custom_basemap{$index}"]) && $settings["controlbox_custom_basemap{$index}"] == '1') {
				$converted['settings']['basemaps'][] = $i;
			}
		}
		$converted['settings']['overlays'] = array();
		for ($i = 1; $i <= 4; $i++) {
			$index = ($i === 1) ? '' : $i;
			if ($marker->overlays_custom . $index == '1') {
				$converted['settings']['overlays'][] = $i + 3;
			}
		}
		for ($i = 1; $i <= 10; $i++) {
			$index = ($i === 1) ? '' : $i;
			if ($marker->wms . $index == '1') {
				$converted['settings']['overlays'][] = $i + 7;
			}
		}
		switch ($marker->basemap) {
			case 'osm_mapnik':
				switch ($settings['openstreetmap_variants']) {
					case 'osm-blackandwhite':
						$converted['settings']['basemapDefault'] = 'osm';
						break;
					case 'osm-de':
						$converted['settings']['basemapDefault'] = 'osmDe';
						break;
					case 'osm-france':
						$converted['settings']['basebasemapDefaultmaps'] = 'osmFrance';
						break;
					case 'osm-hot':
						$converted['settings']['basemapDefault'] = 'osmHot';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'osm';
						break;
				}
				break;
			case 'stamen_terrain':
				switch ($settings['stamen_terrain_flavor']) {
					case 'terrain-background':
						$converted['settings']['basemapDefault'] = 'stamenTerrainBackground';
						break;
					case 'terrain-lines':
						$converted['settings']['basemapDefault'] = 'stamenTerrainLines';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'stamenTerrain';
						break;
				}
				break;
			case 'stamen_toner':
				switch ($settings['stamen_toner_flavor']) {
					case 'toner-background':
						$converted['settings']['basemapDefault'] = 'stamenTonerBackground';
						break;
					case 'toner-hybrid':
						$converted['settings']['basemapDefault'] = 'stamenTonerHybrid';
						break;
					case 'toner-lines':
						$converted['settings']['basemapDefault'] = 'stamenTonerLines';
						break;
					case 'toner-lite':
						$converted['settings']['basemapDefault'] = 'stamenTonerLite';
						break;
					default:
						$converted['settings']['basemapDefault'] = 'stamenToner';
						break;
				}
				break;
			case 'stamen_watercolor':
				$converted['settings']['basemapDefault'] = 'stamenWatercolor';
				break;
			case 'googleLayer_roadmap':
				$converted['settings']['basemapDefault'] = 'googleRoadmap';
				break;
			case 'googleLayer_satellite':
				$converted['settings']['basemapDefault'] = 'googleSatellite';
				break;
			case 'googleLayer_hybrid':
				$converted['settings']['basemapDefault'] = 'googleHybrid';
				break;
			case 'googleLayer_terrain':
				$converted['settings']['basemapDefault'] = 'googleTerrain';
				break;
			case 'bingroad':
				$converted['settings']['basemapDefault'] = 'bingRoad';
				break;
			case 'bingaerial':
				$converted['settings']['basemapDefault'] = 'bingAerial';
				break;
			case 'bingaerialwithlabels':
				$converted['settings']['basemapDefault'] = 'bingAerialLabels';
				break;
			case 'ogdwien_basemap':
				$converted['settings']['basemapDefault'] = 'basemapAt';
				break;
			case 'ogdwien_satellite':
				$converted['settings']['basemapDefault'] = 'basemapAtSatellite';
				break;
			case 'custom_basemap':
				$converted['settings']['basemapDefault'] = '1';
				break;
			case 'custom_basemap2':
				$converted['settings']['basemapDefault'] = '2';
				break;
			case 'custom_basemap3':
				$converted['settings']['basemapDefault'] = '3';
				break;
			default:
				$converted['settings']['basemapDefault'] = 'osm';
				break;
		}
		$converted['settings']['layers'] = ($marker->controlbox) ? true : false;
		$converted['settings']['layersCollapsed'] = ($marker->controlbox === '2') ? true : false;
		$converted['settings']['list'] = false;
		$converted['settings']['gpxUrl'] = $marker->gpx_url;
		$converted['settings']['gpxMeta'] = ($marker->gpx_panel) ? true: false;
		$converted['settings']['filtersAllMarkers'] = false;
		$converted['settings'] = json_encode($converted['settings']);
		$converted['filters'] = '{}';
		$converted['geojson'] = '{}';
		$user = get_user_by('login', $marker->createdby);
		$converted['created_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['created_on'] = $marker->createdon;
		$user = get_user_by('login', $marker->updatedby);
		$converted['updated_by_id'] = ($user) ? $user->ID : get_current_user_id();
		$converted['updated_on'] = $marker->updatedon;

		return $converted;
	}

	/**
	 * Applies the old sanitization rules to a popup
	 *
	 * @since 4.0
	 */
	private function sanitize_popup($popup) {
		$sanitize_from = array(
			'#<ul(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*</ul>#si',
			'#<ol(.*?)>(\s)*(<br\s*/?>)*(\s)*<li(.*?)>#si',
			'#</li>(\s)*(<br\s*/?>)*(\s)*</ol>#si',
			'#(<br\s*/?>){1}\s*<ul(.*?)>#si',
			'#(<br\s*/?>){1}\s*<ol(.*?)>#si',
			'#</ul>\s*(<br\s*/?>){1}#si',
			'#</ol>\s*(<br\s*/?>){1}#si'
		);
		$sanitize_to = array(
			'<ul$1><li$5>',
			'</li><li$4>',
			'</li></ul>',
			'<ol$1><li$5>',
			'</li></ol>',
			'<ul$2>',
			'<ol$2>',
			'</ul>',
			'</ol>'
		);

		return preg_replace($sanitize_from, $sanitize_to, stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/', '<br />', $popup)));
	}

	/**
	 * Copies the icons to the new location
	 *
	 * @since 4.0
	 */
	public function copy_icons($settings) {
		if (($handle = opendir(MMP::$icons_dir)) !== false) {
			while (($file = readdir($handle)) !== false) {
				if ($file !== '.' && $file !== '..') {
					unlink(MMP::$icons_dir . $file);
				}
			}
			closedir($handle);
		}

		if (isset($settings['defaults_marker_custom_icon_url_dir']) && $settings['defaults_marker_custom_icon_url_dir'] == 'yes' && isset($settings['defaults_marker_icon_dir'])) {
			$icons_dir = trailingslashit($settings['defaults_marker_icon_dir']);
		} else {
			$upload_dir = wp_get_upload_dir();
			$icons_dir = $upload_dir['basedir'] . '/leaflet-maps-marker-icons/';
		}

		$allowed = array('png', 'gif', 'jpg', 'jpeg');
		if (($handle = opendir($icons_dir)) !== false) {
			while (($file = readdir($handle)) !== false) {
				$info = pathinfo($file);
				$ext = strtolower($info['extension']);
				if (!is_dir($icons_dir . $file) && in_array($ext, $allowed)) {
					copy($icons_dir . $file, MMP::$icons_dir . $file);
				}
			}
			closedir($handle);
		}
	}
}
