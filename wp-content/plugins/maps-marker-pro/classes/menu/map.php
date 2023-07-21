<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Map extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_save_map', array($this, 'save_map'));
		add_action('wp_ajax_mmp_save_map_defaults', array($this, 'save_map_defaults'));
		add_action('wp_ajax_mmp_advanced_map_settings_state', array($this, 'advanced_map_settings_state'));
		add_action('wp_ajax_mmp_delete_map_direct', array($this, 'delete_map'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook Name of the current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_map')) !== 'mapsmarkerpro_map') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_media();
		if (MMP::$settings['googleApiKey']) {
			wp_enqueue_script('mmp-googlemaps');
		}
		wp_enqueue_script('mmp-admin');
	}

	/**
	 * Saves the map
	 *
	 * @since 4.0
	 */
	public function save_map() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-map', 'nonce');

		$current_user = wp_get_current_user();
		$date = gmdate('Y-m-d H:i:s');
		$settings = wp_unslash($_POST['settings']);
		$geojson = json_decode(wp_unslash($_POST['geoJson']));
		if (!isset($geojson->features) || !is_array($geojson->features) || !count($geojson->features)) {
			$geojson = null;
		}
		parse_str($settings, $settings);

		$id = $settings['id'];
		$name = $settings['name'];
		$settings['maxBounds'] = preg_replace('/[^0-9.,-]/', '', $settings['maxBounds']);
		$settings['basemaps'] = (isset($settings['basemaps'])) ? $settings['basemaps'] : array();
		$settings['overlays'] = (isset($settings['overlays'])) ? $settings['overlays'] : array();
		$index = 0;
		$filters = array();
		if (isset($settings['filtersList']) && is_array($settings['filtersList'])) {
			foreach ($settings['filtersList'] as $map_id => $filter) {
				$filters[$map_id] = array(
					'index'  => $index++,
					'active' => (isset($filter['active'])) ? true : false,
					'name'   => $filter['name'],
					'icon'   => $filter['icon']
				);
			}
		}
		$settings = $mmp_settings->validate_map_settings($settings, false, false);
		$settings = json_encode($settings, JSON_FORCE_OBJECT);
		$filters = json_encode($filters, JSON_FORCE_OBJECT);
		$geojson = ($geojson === null) ? '{}' : json_encode($geojson);
		$data = array(
			'name'          => $name,
			'settings'      => $settings,
			'filters'       => $filters,
			'geojson'       => $geojson,
			'created_by_id' => $current_user->ID,
			'created_on'    => $date,
			'updated_by_id' => $current_user->ID,
			'updated_on'    => $date
		);
		if ($id === 'new') {
			if (!current_user_can('mmp_add_maps')) {
				wp_send_json_error(esc_html__('You do not have the required capabilities to add maps.', 'mmp'));
			}
			$id = $db->add_map((object) $data);
			if ($id === false) {
				wp_send_json_error(esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')');
			}
			do_action('mmp_save_map', $id, $data, true);
			do_action('mmp_add_map', $id, $data);
		} else {
			$id = absint($id);
			if (!$id) {
				wp_send_json_error(esc_html__('Invalid map ID', 'mmp'));
			}
			$map = $db->get_map($id);
			if (!$map) {
				wp_send_json_error(sprintf(esc_html__('A map with ID %1$s does not exist.', 'mmp'), $id));
			}
			if ($map->created_by_id != $current_user->ID && !current_user_can('mmp_edit_other_maps')) {
				wp_send_json_error(sprintf(esc_html__('You do not have the required capabilities to edit the map with ID %1$s.', 'mmp'), $id));
			}
			$update = $db->update_map((object) $data, $id);
			if ($update === false) {
				wp_send_json_error(esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')');
			}
			do_action('mmp_save_map', $id, $data, false);
			do_action('mmp_update_map', $id, $data);
		}

		wp_send_json_success(array(
			'id'      => $id,
			'message' => esc_html__('Map saved successfully', 'mmp')
		));
	}

	/**
	 * Saves the map defaults
	 *
	 * @since 4.0
	 */
	public function save_map_defaults() {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-map', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$settings = $mmp_settings->validate_map_settings($settings, false, false);
		update_option('mapsmarkerpro_map_defaults', $settings);

		wp_send_json_success();
	}

	/**
	 * Saves the current state of the advanced map settings toggle
	 *
	 * @since 4.8
	 */
	public function advanced_map_settings_state() {
		check_ajax_referer('mmp-map', 'nonce');

		$state = (isset($_POST['state']) && $_POST['state']);
		update_user_meta(get_current_user_id(), 'mapsmarkerpro_advanced_map_settings', $state);

		wp_send_json_success();
	}

	/**
	 * Deletes the map
	 *
	 * @since 4.0
	 */
	public function delete_map() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-map', 'nonce');

		$id = absint($_POST['id']);
		if (!$id) {
			wp_send_json_error();
		}

		$map = $db->get_map($id);
		if (!$map) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();
		if ($map->created_by_id != $current_user->ID && !current_user_can('mmp_delete_other_maps')) {
			wp_send_json_error();
		}

		if (!isset($_POST['con']) || !$_POST['con']) {
			$message = sprintf(esc_html__('Are you sure you want to delete the map with ID %1$s?', 'mmp'), $id) . "\n";

			$shortcodes = $db->get_map_shortcodes($id);
			if (count($shortcodes)) {
				$message .= esc_html__('The map is used in the following content:', 'mmp') . "\n";
				foreach ($shortcodes as $shortcode) {
					$message .= $shortcode['title'] . "\n";
				}
			} else {
				$message .= esc_html__('The map is not used in any content.', 'mmp');
			}

			wp_send_json_success(array(
				'id'      => $id,
				'message' => $message
			));
		}

		$db->delete_map($id);

		wp_send_json_success(array(
			'id' => $id
		));
	}

	/**
	 * Shows the map page
	 *
	 * @since 4.0
	 */
	protected function show() {
		global $wp;
		$db = MMP::get_instance('MMP\DB');
		$upload = MMP::get_instance('MMP\FS\Upload');
		$l10n = MMP::get_instance('MMP\L10n');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		$settings['id'] = (isset($_GET['id'])) ? absint($_GET['id']) : 'new';

		$current_user = wp_get_current_user();
		$shortcodes = $db->get_map_shortcodes($settings['id']);

		$maps = $db->get_all_maps(true);
		if ($settings['id'] !== 'new') {
			$map = $db->get_map($settings['id']);
			if (!$map) {
				$this->error(sprintf(esc_html__('A map with ID %1$s does not exist.', 'mmp'), $settings['id']));
				return;
			}
			if ($map->created_by_id != $current_user->ID && !current_user_can('mmp_edit_other_maps')) {
				$this->error(sprintf(esc_html__('You do not have the required capabilities to edit the map with ID %1$s.', 'mmp'), $settings['id']));
				return;
			}
			$filters = json_decode($map->filters, true);
			$settings['name'] = $map->name;
			$settings = array_merge($settings, $mmp_settings->validate_map_settings(json_decode($map->settings, true)));
		} else {
			$filters = json_decode('{}', true);
			$settings['name'] = '';
			$settings = array_merge($settings, $mmp_settings->get_map_defaults());
		}
		$settings['geocodingProvider'] = MMP::$settings['geocodingProvider'];
		$settings['geocodingMinChars'] = MMP::$settings['geocodingMinChars'];
		$settings['geocodingLocationIqApiKey'] = MMP::$settings['geocodingLocationIqApiKey'];
		$settings['geocodingMapQuestApiKey'] = MMP::$settings['geocodingMapQuestApiKey'];
		$settings['geocodingGoogleApiKey'] = MMP::$settings['geocodingGoogleApiKey'];
		$settings['geocodingTomTomApiKey'] = MMP::$settings['geocodingTomTomApiKey'];

		wp_add_inline_script('mmp-admin',
			"var mmpAdmin = new MapsMarkerPro({
				uid: 'admin',
				type: 'map',
				id: '{$settings['id']}',
				edit: true,
				overrides: {
					callback: 'editMapActions'
				}
			});"
		);

		?>
		<div class="wrap mmp-wrap">
			<h1><?= ($settings['id'] !== 'new') ? esc_html__('Edit map', 'mmp') : esc_html__('Add map', 'mmp') ?></h1>
			<input type="hidden" id="nonce" name="nonce" value="<?= wp_create_nonce('mmp-map') ?>" />
			<div class="mmp-main">
				<form id="mapSettings" method="POST">
					<input type="hidden" id="id" name="id" value="<?= $settings['id'] ?>" />
					<div class="mmp-flexwrap mmp-edit-map">
						<div class="mmp-left">
							<div class="mmp-top-bar">
								<div class="mmp-top-bar-left">
									<button id="save" class="button button-primary" disabled="disabled"><?= esc_html__('Save', 'mmp') ?></button>
								</div>
								<div class="mmp-top-bar-right">
									<label>
										<div class="switch">
											<input type="checkbox" id="advancedSettings" <?= !(get_user_meta(get_current_user_id(), 'mapsmarkerpro_advanced_map_settings', true)) ?: 'checked="checked"' ?> />
											<span class="slider"></span>
										</div>
										<span><?= esc_html__('Show advanced settings', 'mmp') ?></span>
									</label>
								</div>
							</div>
							<div class="mmp-tabs">
								<button type="button" id="tabMap" class="mmp-tablink"><?= esc_html__('Map', 'mmp') ?></button>
								<button type="button" id="tabLayers" class="mmp-tablink"><?= esc_html__('Layers', 'mmp') ?></button>
								<button type="button" id="tabControl" class="mmp-tablink"><?= esc_html__('Controls', 'mmp') ?></button>
								<button type="button" id="tabMarker" class="mmp-tablink"><?= esc_html__('Markers', 'mmp') ?></button>
								<button type="button" id="tabFilter" class="mmp-tablink"><?= esc_html__('Filters', 'mmp') ?></button>
								<button type="button" id="tabList" class="mmp-tablink"><?= esc_html__('List', 'mmp') ?></button>
								<button type="button" id="tabShare" class="mmp-tablink"><?= esc_html__('Share', 'mmp') ?></button>
								<button type="button" id="tabInteraction" class="mmp-tablink"><?= esc_html__('Interaction', 'mmp') ?></button>
								<button type="button" id="tabGpx" class="mmp-tablink"><?= esc_html__('GPX', 'mmp') ?></button>
								<button type="button" id="tabDraw" class="mmp-tablink"><?= esc_html__('Draw', 'mmp') ?></button>
							</div>
							<div id="mmp-tabMap-settings" class="mmp-tab">
								<input type="hidden" id="iconTarget" name="iconTarget" value="" />
								<button type="button" id="fitMarkers" class="button button-secondary"><?= esc_html__('Fit all markers', 'mmp') ?></button>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="address"><?= esc_html__('Find a location', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<?php if ($settings['geocodingProvider'] !== 'none' && ($settings['geocodingLocationIqApiKey'] || $settings['geocodingMapQuestApiKey'] || $settings['geocodingGoogleApiKey'] || $settings['geocodingTomTomApiKey'])): ?>
											<div id="geocodingError"></div>
											<input type="text" id="address" name="address" placeholder="<?= ($settings['geocodingMinChars'] < 2) ? esc_attr__('Start typing for suggestions', 'mmp') : sprintf(esc_attr__('Start typing for suggestions (%1$s characters minimum)', 'mmp'), $settings['geocodingMinChars']) ?>" /><br />
											<select id="geocodingProvider">
												<optgroup label="<?= esc_attr__('Available providers', 'mmp') ?>">
													<?php if ($settings['geocodingLocationIqApiKey']): ?>
														<option value="locationiq" <?= $settings['geocodingProvider'] !== 'locationiq' ?: 'selected="selected"' ?>>LocationIQ</option>
													<?php endif; ?>
													<?php if ($settings['geocodingMapQuestApiKey']): ?>
														<option value="mapquest" <?= $settings['geocodingProvider'] !== 'mapquest' ?: 'selected="selected"' ?>>MapQuest</option>
													<?php endif; ?>
													<?php if ($settings['geocodingGoogleApiKey']): ?>
														<option value="google" <?= $settings['geocodingProvider'] !== 'google' ?: 'selected="selected"' ?>>Google</option>
													<?php endif; ?>
													<?php if ($settings['geocodingTomTomApiKey']): ?>
														<option value="tomtom" <?= $settings['geocodingProvider'] !== 'tomtom' ?: 'selected="selected"' ?>>TomTom</option>
													<?php endif; ?>
												</optgroup>
												<?php if (!$settings['geocodingLocationIqApiKey'] || !$settings['geocodingMapQuestApiKey'] || !$settings['geocodingGoogleApiKey'] || !$settings['geocodingTomTomApiKey']): ?>
													<optgroup label="<?= esc_attr__('Inactive (API key required)', 'mmp') ?>">
														<?php if (!$settings['geocodingLocationIqApiKey']): ?>
															<option value="locationiq" disabled="disabled">LocationIQ</option>
														<?php endif; ?>
														<?php if (!$settings['geocodingMapQuestApiKey']): ?>
															<option value="mapquest" disabled="disabled">MapQuest</option>
														<?php endif; ?>
														<?php if (!$settings['geocodingGoogleApiKey']): ?>
															<option value="google" disabled="disabled">Google</option>
														<?php endif; ?>
														<?php if (!$settings['geocodingTomTomApiKey']): ?>
															<option value="tomtom" disabled="disabled">TomTom</option>
														<?php endif; ?>
													</optgroup>
												<?php endif; ?>
											</select>
											<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider') ?>" target="_blank"><?= esc_html__('Geocoding settings', 'mmp') ?></a>
										<?php else: ?>
											<span class="mmp-warning"><?= sprintf($l10n->kses__('To use the geocoding feature, please activate one or more providers in the <a href="%1$s" target="_blank">geocoding settings</a>', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider')) ?></span>
										<?php endif; ?>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="name"><?= esc_html__('Name', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="name" name="name" value="<?= esc_attr($settings['name']) ?>" />
										<?php if ($settings['id'] !== 'new'): ?>
											<br />
											<?php if ($l10n->check_ml() === 'wpml'): ?>
												(<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' . urlencode($settings['name'])) ?>"><?= esc_html__('translate', 'mmp') ?></a>)
											<?php elseif ($l10n->check_ml() === 'pll'): ?>
												(<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Map+%28ID+' . $settings['id'] . '%29+name&group=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
											<?php else: ?>
												(<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('translate', 'mmp') ?></a>)
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="width"><?= esc_html__('Width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="width" name="width" value="<?= $settings['width'] ?>" min="1" />
										<label><input type="radio" id="widthUnitPct" name="widthUnit" value="%" <?= !($settings['widthUnit'] === '%') ?: 'checked="checked"' ?> />%</label>
										<label><input type="radio" id="widthUnitPx" name="widthUnit" value="px" <?= !($settings['widthUnit'] === 'px') ?: 'checked="checked"' ?> />px</label>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="height"><?= esc_html__('Height', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="height" name="height" value="<?= $settings['height'] ?>" min="1" />
										<label><input type="radio" id="heightUnitPx" name="heightUnit" value="px" <?= !($settings['heightUnit'] === 'px') ?: 'checked="checked"' ?> />px</label>
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="lat"><?= esc_html__('Latitude', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="lat" name="lat" value="<?= $settings['lat'] ?>" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="lng"><?= esc_html__('Longitude', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="lng" name="lng" value="<?= $settings['lng'] ?>" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="maxBounds"><?= esc_html__('Max bounds', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="maxBounds" name="maxBounds"><?= str_replace(',', ",\n", $settings['maxBounds']) ?></textarea><br />
										<button type="button" id="restrictView" class="button button-secondary"><?= esc_html__('Restrict to current view', 'mmp') ?></button>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="zoom"><?= esc_html__('Zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="zoom" name="zoom" value="<?= $settings['zoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="minZoom"><?= esc_html__('Min zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="minZoom" name="minZoom" value="<?= $settings['minZoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="maxZoom"><?= esc_html__('Max zoom', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="maxZoom" name="maxZoom" value="<?= $settings['maxZoom'] ?>" min="0" max="23" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="zoomStep"><?= esc_html__('Zoom step', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="zoomStep" name="zoomStep" value="<?= $settings['zoomStep'] ?>" min="0.1" max="1" step="0.1" />
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<?= esc_html__('Panel', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="panel" name="panel" <?= !$settings['panel'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('Show', 'mmp') ?></span>
										</label>
										<input type="text" id="panelColor" name="panelColor" value="<?= $settings['panelColor'] ?>" /><br />
										<label>
											<div class="switch">
												<input type="checkbox" id="panelFs" name="panelFs" <?= !$settings['panelFs'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('Fullscreen', 'mmp') ?></span>
										</label><br />
										<?php if (!MMP::$settings['apiFullscreen']): ?>
											<span class="mmp-warning"><?= esc_html__('The fullscreen endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)<br />
										<?php endif; ?>
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGpx" name="panelGpx" <?= !$settings['panelGpx'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('GPX download', 'mmp') ?></span>
										</label><br />
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGeoJson" name="panelGeoJson" <?= !$settings['panelGeoJson'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('GeoJSON export', 'mmp') ?></span>
										</label><br />
										<?php if (!MMP::$settings['apiExport']): ?>
											<span class="mmp-warning"><?= esc_html__('The export endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)<br />
										<?php endif; ?>
										<label>
											<div class="switch">
												<input type="checkbox" id="panelKml" name="panelKml" <?= !$settings['panelKml'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('KML export', 'mmp') ?></span>
										</label><br />
										<?php if (!MMP::$settings['apiExport']): ?>
											<span class="mmp-warning"><?= esc_html__('The export endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)<br />
										<?php endif; ?>
										<label>
											<div class="switch">
												<input type="checkbox" id="panelGeoRss" name="panelGeoRss" <?= !$settings['panelGeoRss'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
											<span><?= esc_html__('GeoRSS export', 'mmp') ?></span>
										</label>
										<?php if (!MMP::$settings['apiExport']): ?>
											<br /><span class="mmp-warning"><?= esc_html__('The export endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)
										<?php endif; ?>
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="callback"><?= esc_html__('JavaScript callback', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="callback" name="callback" value="<?= esc_attr($settings['callback']) ?>" />
									</div>
								</div>
							</div>
							<div id="mmp-tabLayers-settings" class="mmp-tab">
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="basemapGoogleStyles"><?= esc_html__('Google styles', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="basemapGoogleStyles" name="basemapGoogleStyles"><?= $settings['basemapGoogleStyles'] ?></textarea><br />
										<a href="https://www.mapsmarker.com/google-styles/" target="_blank"><?= esc_html__('Tutorial and example styles', 'mmp') ?></a>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<?= esc_html__('Basemaps', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<ul id="basemapList"></ul>
										<select id="basemapsList"></select><br />
										<button type="button" id="basemapsAdd" class="button button-secondary"><?= esc_html__('Add basemap', 'mmp') ?></button>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<?= esc_html__('Overlays', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<ul id="overlayList"></ul>
										<select id="overlaysList"></select><br />
										<button type="button" id="overlaysAdd" class="button button-secondary"><?= esc_html__('Add overlay', 'mmp') ?></button>
									</div>
								</div>
							</div>
							<div id="mmp-tabControl-settings" class="mmp-tab">
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Geocoding control', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<?= esc_html__('Position', 'mmp') ?>
										</div>
										<div class="mmp-map-setting-input">
											<?php if ($settings['geocodingProvider'] === 'none' || (!$settings['geocodingLocationIqApiKey'] && !$settings['geocodingMapQuestApiKey'] && !$settings['geocodingGoogleApiKey'] && !$settings['geocodingTomTomApiKey'])): ?>
												<span class="mmp-warning"><?= sprintf($l10n->kses__('To use the geocoding feature, please activate one or more providers in the <a href="%1$s" target="_blank">geocoding settings</a>', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider')) ?></span><br />
											<?php endif; ?>
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="geocodingControlIndex" name="geocodingControlIndex" value="<?= $settings['geocodingControlIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<?= esc_html__('Marker icon', 'mmp') ?>
										</div>
										<div class="mmp-map-setting-input">
											<input type="hidden" id="geocodingControlMarkerIcon" name="geocodingControlMarkerIcon" value="<?= $settings['geocodingControlMarkerIcon'] ?>" />
											<img class="mmp-geocoding-control-icon mmp-align-middle" src="<?= (!$settings['geocodingControlMarkerIcon']) ? plugins_url('images/leaflet/pin.png', MMP::$path) : MMP::$icons_url . $settings['geocodingControlMarkerIcon'] ?>" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Zoom buttons', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="zoomControlIndex" name="zoomControlIndex" value="<?= $settings['zoomControlIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Fullscreen button', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="fullscreenIndex" name="fullscreenIndex" value="<?= $settings['fullscreenIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Reset button', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="resetIndex" name="resetIndex" value="<?= $settings['resetIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Locate button', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="locateIndex" name="locateIndex" value="<?= $settings['locateIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="locateClickBehaviorInView"><?= esc_html__('Click behavior in view', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="locateClickBehaviorInView" name="locateClickBehaviorInView">
												<option value="stop" <?= !($settings['locateClickBehaviorInView'] === 'stop') ?: 'selected="selected"' ?>><?= esc_html__('Stop', 'mmp') ?></option>
												<option value="setView" <?= !($settings['locateClickBehaviorInView'] === 'setView') ?: 'selected="selected"' ?>><?= esc_html__('Set view', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="locateClickBehaviorOutOfView"><?= esc_html__('Click behavior out of view', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="locateClickBehaviorOutOfView" name="locateClickBehaviorOutOfView">
												<option value="stop" <?= !($settings['locateClickBehaviorOutOfView'] === 'stop') ?: 'selected="selected"' ?>><?= esc_html__('Stop', 'mmp') ?></option>
												<option value="setView" <?= !($settings['locateClickBehaviorOutOfView'] === 'setView') ?: 'selected="selected"' ?>><?= esc_html__('Set view', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Measure button', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="measureIndex" name="measureIndex" value="<?= $settings['measureIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="measureUnit"><?= esc_html__('Unit', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="measureUnit" name="measureUnit">
												<option value="metric" <?= !($settings['measureUnit'] === 'metric') ?: 'selected="selected"' ?>><?= esc_html__('Metric', 'mmp') ?></option>
												<option value="imperial" <?= !($settings['measureUnit'] === 'imperial') ?: 'selected="selected"' ?>><?= esc_html__('Imperial', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Scale', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="scaleIndex" name="scaleIndex" value="<?= $settings['scaleIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="scaleMaxWidth"><?= esc_html__('Max width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="scaleMaxWidth" name="scaleMaxWidth" value="<?= $settings['scaleMaxWidth'] ?>" min="0" step="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Layers control', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="layersIndex" name="layersIndex" value="<?= $settings['layersIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('GPX control', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="gpxControlIndex" name="gpxControlIndex" value="<?= $settings['gpxControlIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Minimap', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="minimapIndex" name="minimapIndex" value="<?= $settings['minimapIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapWidth"><?= esc_html__('Width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapWidth" name="minimapWidth" value="<?= $settings['minimapWidth'] ?>" min="1" step="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapHeight"><?= esc_html__('Height', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapHeight" name="minimapHeight" value="<?= $settings['minimapHeight'] ?>" min="1" step="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapCollapsedWidth"><?= esc_html__('Collapsed width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapCollapsedWidth" name="minimapCollapsedWidth" value="<?= $settings['minimapCollapsedWidth'] ?>" min="1" step="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapCollapsedHeight"><?= esc_html__('Collapsed height', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapCollapsedHeight" name="minimapCollapsedHeight" value="<?= $settings['minimapCollapsedHeight'] ?>" min="1" step="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapZoomLevelOffset"><?= esc_html__('Zoom level offset', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapZoomLevelOffset" name="minimapZoomLevelOffset" value="<?= $settings['minimapZoomLevelOffset'] ?>" min="-23" max="23" step="0.1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="minimapZoomLevelFixed"><?= esc_html__('Fixed zoom level', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="minimapZoomLevelFixed" name="minimapZoomLevelFixed" value="<?= $settings['minimapZoomLevelFixed'] ?>" min="0" max="23" step="0.1" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Attribution', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="attributionIndex" name="attributionIndex" value="<?= $settings['attributionIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
							</div>
							<div id="mmp-tabMarker-settings" class="mmp-tab">
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Icon', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="markerOpacity"><?= esc_html__('Opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="markerOpacity" name="markerOpacity" value="<?= $settings['markerOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Clustering', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="disableClusteringAtZoom"><?= esc_html__('Disable at zoom', 'mmp') ?>*</label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="disableClusteringAtZoom" name="disableClusteringAtZoom" value="<?= $settings['disableClusteringAtZoom'] ?>" min="0" max="23" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="maxClusterRadius"><?= esc_html__('Max cluster radius', 'mmp') ?>*</label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="maxClusterRadius" name="maxClusterRadius" value="<?= $settings['maxClusterRadius'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="spiderfyDistanceMultiplier"><?= esc_html__('Spiderfy multiplier', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="spiderfyDistanceMultiplier" name="spiderfyDistanceMultiplier" value="<?= $settings['spiderfyDistanceMultiplier'] ?>" min="0" max="10" step="0.1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Tooltip', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="tooltipOpacity"><?= esc_html__('Opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="tooltipOpacity" name="tooltipOpacity" value="<?= $settings['tooltipOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Popup', 'mmp') ?></span>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="popupMinWidth"><?= esc_html__('Min width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="popupMinWidth" name="popupMinWidth" value="<?= $settings['popupMinWidth'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="popupMaxWidth"><?= esc_html__('Max width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="popupMaxWidth" name="popupMaxWidth" value="<?= $settings['popupMaxWidth'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="popupMaxHeight"><?= esc_html__('Max height', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="popupMaxHeight" name="popupMaxHeight" value="<?= $settings['popupMaxHeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="popupCloseButton"><?= esc_html__('Add close button', 'mmp') ?>*</label>
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
							</div>
							<div id="mmp-tabFilter-settings" class="mmp-tab">
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Filters control', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
											<label class="mmp-advanced">
												<input class="mmp-control-index" type="number" id="filtersIndex" name="filtersIndex" value="<?= $settings['filtersIndex'] ?>" min="0" max="99" />
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="filtersOrderBy"><?= esc_html__('Sorting', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="filtersOrderBy" name="filtersOrderBy">
												<option value="id" <?= !($settings['filtersOrderBy'] === 'id') ?: 'selected="selected"' ?>><?= esc_html__('ID', 'mmp') ?></option>
												<option value="name" <?= !($settings['filtersOrderBy'] === 'name') ?: 'selected="selected"' ?>><?= esc_html__('Name', 'mmp') ?></option>
												<option value="count" <?= !($settings['filtersOrderBy'] === 'count') ?: 'selected="selected"' ?>><?= esc_html__('Count', 'mmp') ?></option>
												<option value="custom" <?= !($settings['filtersOrderBy'] === 'custom') ?: 'selected="selected"' ?>><?= esc_html__('Custom', 'mmp') ?></option>
											</select>
											<select id="filtersSortOrder" name="filtersSortOrder" <?= !($settings['filtersOrderBy'] === 'custom') ? '' : 'disabled="disabled"' ?>>
												<option value="asc" <?= !($settings['filtersSortOrder'] === 'asc') ?: 'selected="selected"' ?>><?= esc_html__('Ascending', 'mmp') ?></option>
												<option value="desc" <?= !($settings['filtersSortOrder'] === 'desc') ?: 'selected="selected"' ?>><?= esc_html__('Descending', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<label>
									<div class="switch">
										<input type="checkbox" id="filtersAllMarkers" name="filtersAllMarkers" <?= !$settings['filtersAllMarkers'] ?: 'checked="checked"' ?> />
										<span class="slider"></span>
									</div>
									<?= esc_html__('Show all available markers (disables individual filters)', 'mmp') ?>
								</label>
								<div id="filtersWrap">
									<label>
										<div class="switch">
											<input type="checkbox" id="filtersGeoJson" name="filtersGeoJson" <?= !$settings['filtersGeoJson'] ?: 'checked="checked"' ?> />
											<span class="slider"></span>
										</div>
										<?= esc_html__('Also load shapes for added filters', 'mmp') ?>
									</label>
									<ul id="filterList"></ul>
									<select id="filtersMapList">
										<?php foreach ($maps as $map): ?>
											<option value="<?= $map->id ?>" <?= ($map->id != $settings['id'] && !isset($filters[$map->id])) ? '' : 'disabled="disabled"' ?>>[<?= $map->id ?>] <?= esc_html($map->name) ?> (<?= $map->markers ?> markers)</option>
										<?php endforeach; ?>
									</select><br />
									<button type="button" id="filtersAdd" class="button button-secondary"><?= esc_html__('Add filter', 'mmp') ?></button>
								</div>
							</div>
							<div id="mmp-tabList-settings" class="mmp-tab">
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="list"><?= esc_html__('Marker list', 'mmp') ?></label>
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="listWidth"><?= esc_html__('Width', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listWidth" name="listWidth" value="<?= $settings['listWidth'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="listBreakpoint"><?= esc_html__('Breakpoint', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listBreakpoint" name="listBreakpoint" value="<?= $settings['listBreakpoint'] ?>" min="0" /><br />
										<?= esc_html__('If the list is set to right or left and the width of the map falls below this value, the list will be shown below the map instead', 'mmp') ?>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="listDistancePrecision"><?= esc_html__('Distance precision', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="listDistancePrecision" name="listDistancePrecision" value="<?= $settings['listDistancePrecision'] ?>" min="0" max="6" />
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('List settings', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listDateType"><?= esc_html__('Date type', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="listDateType" name="listDateType">
												<option value="created" <?= !($settings['listDateType'] === 'created') ?: 'selected="selected"' ?>><?= esc_html__('Created', 'mmp') ?></option>
												<option value="updated" <?= !($settings['listDateType'] === 'updated') ?: 'selected="selected"' ?>><?= esc_html__('Updated', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listFs"><?= esc_html__('Show fullscreen link', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="listFs" name="listFs" <?= !$settings['listFs'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
											<?php if (!MMP::$settings['apiFullscreen']): ?>
												<br /><span class="mmp-warning"><?= esc_html__('The fullscreen endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)
											<?php endif; ?>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listLimit"><?= esc_html__('Markers per page', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="listLimit" name="listLimit" value="<?= $settings['listLimit'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Search and sort', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listOrderBy"><?= esc_html__('Default sorting', 'mmp') ?></label>
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
											<select id="listSortOrder" name="listSortOrder">
												<option value="asc" <?= !($settings['listSortOrder'] === 'asc') ?: 'selected="selected"' ?>><?= esc_html__('Ascending', 'mmp') ?></option>
												<option value="desc" <?= !($settings['listSortOrder'] === 'desc') ?: 'selected="selected"' ?>><?= esc_html__('Descending', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Location finder', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listLocation"><?= esc_html__('Show location finder', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<?php if ($settings['geocodingProvider'] === 'none' || (!$settings['geocodingLocationIqApiKey'] && !$settings['geocodingMapQuestApiKey'] && !$settings['geocodingGoogleApiKey'] && !$settings['geocodingTomTomApiKey'])): ?>
												<span class="mmp-warning"><?= sprintf($l10n->kses__('To use the geocoding feature, please activate one or more providers in the <a href="%1$s" target="_blank">geocoding settings</a>', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider')) ?></span><br />
											<?php endif; ?>
											<label>
												<div class="switch">
													<input type="checkbox" id="listLocation" name="listLocation" <?= !$settings['listLocation'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="listGeocodingZoom"><?= esc_html__('Zoom', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="listGeocodingZoom" name="listGeocodingZoom" value="<?= $settings['listGeocodingZoom'] ?>" min="0" max="23" step="0.1" /><br />
											<?= esc_html__('When looking up a location without choosing a maximum distance, the map will zoom to this level (0 to disable).', 'mmp') ?>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listGeocodingColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="listGeocodingColor" name="listGeocodingColor" value="<?= $settings['listGeocodingColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listGeocodingWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="listGeocodingWeight" name="listGeocodingWeight" value="<?= $settings['listGeocodingWeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listGeocodingFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="listGeocodingFillColor" name="listGeocodingFillColor" value="<?= $settings['listGeocodingFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="listGeocodingFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="listGeocodingFillOpacity" name="listGeocodingFillOpacity" value="<?= $settings['listGeocodingFillOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
							</div>
							<div id="mmp-tabShare-settings" class="mmp-tab">
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="shareUrl"><?= esc_html__('Share URL', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<select id="shareUrl" name="shareUrl">
											<option value="page" <?= !($settings['shareUrl'] === 'page') ?: 'selected="selected"' ?>><?= esc_html__('Current page', 'mmp') ?></option>
											<option value="fs" <?= !($settings['shareUrl'] === 'fs') ?: 'selected="selected"' ?>><?= esc_html__('Fullscreen map', 'mmp') ?></option>
										</select>
										<?php if (!MMP::$settings['apiFullscreen']): ?>
											<br /><span class="mmp-warning"><?= esc_html__('The fullscreen endpoint is disabled, so this setting has no effect', 'mmp') ?></span> (<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#misc_wordpress') ?>" target="_blank"><?= esc_html__('settings', 'mmp') ?></a>)
										<?php endif; ?>
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="shareText"><?= esc_html__('Share text', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<textarea id="shareText" name="shareText"><?= $settings['shareText'] ?></textarea>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Share button', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
							<div id="mmp-tabInteraction-settings" class="mmp-tab">
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="inertiaDeceleration"><?= esc_html__('Inertia deceleration', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="inertiaDeceleration" name="inertiaDeceleration" value="<?= $settings['inertiaDeceleration'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="inertiaMaxSpeed"><?= esc_html__('Inertia max speed', 'mmp') ?></label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="inertiaMaxSpeed" name="inertiaMaxSpeed" value="<?= $settings['inertiaMaxSpeed'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="keyboardPanDelta"><?= esc_html__('Keyboard pan delta', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<input type="number" id="keyboardPanDelta" name="keyboardPanDelta" value="<?= $settings['keyboardPanDelta'] ?>" min="0" />
									</div>
								</div>
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<label for="bounceAtZoomLimits"><?= esc_html__('Bounce at zoom limits', 'mmp') ?>*</label>
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
								<div class="mmp-map-setting mmp-advanced">
									<div class="mmp-map-setting-desc">
										<label for="worldCopyJump"><?= esc_html__('Move objects to map copies', 'mmp') ?>*</label>
									</div>
									<div class="mmp-map-setting-input">
										<label>
											<div class="switch">
												<input type="checkbox" id="worldCopyJump" name="worldCopyJump" <?= !$settings['worldCopyJump'] ?: 'checked="checked"' ?> />
												<span class="slider"></span>
											</div>
										</label><br />
										<?= esc_html__('When panning past the edges of a map, objects such as markers will seamlessly be moved to the new copy of the map', 'mmp') ?>
									</div>
								</div>
							</div>
							<div id="mmp-tabGpx-settings" class="mmp-tab">
								<div class="mmp-map-setting">
									<div class="mmp-map-setting-desc">
										<?= esc_html__('GPX URL', 'mmp') ?>
									</div>
									<div class="mmp-map-setting-input">
										<input type="text" id="gpxUrl" name="gpxUrl" value="<?= $settings['gpxUrl'] ?>" /><br />
										<button type="button" id="chooseGpx" class="button button-secondary"><?= esc_html__('Open Media Library', 'mmp') ?></button>
										<button type="button" id="updateGpx" class="button button-secondary"><?= esc_html__('Update GPX', 'mmp') ?></button><br />
										<?= esc_html__('External URLs require an "allow origin" header', 'mmp') ?>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Track', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxShowStartIcon"><?= esc_html__('Show start icon', 'mmp') ?></label>
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<?= esc_html__('Start icon', 'mmp') ?>
										</div>
										<div class="mmp-map-setting-input">
											<input type="hidden" id="gpxStartIcon" name="gpxStartIcon" value="<?= $settings['gpxStartIcon'] ?>" />
											<img class="mmp-gpx-start-icon mmp-align-middle" src="<?= (!$settings['gpxStartIcon']) ? plugins_url('images/leaflet/gpx-start.png', MMP::$path) : MMP::$icons_url . $settings['gpxStartIcon'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxShowEndIcon"><?= esc_html__('Show end icon', 'mmp') ?></label>
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<?= esc_html__('End icon', 'mmp') ?>
										</div>
										<div class="mmp-map-setting-input">
											<input type="hidden" id="gpxEndIcon" name="gpxEndIcon" value="<?= $settings['gpxEndIcon'] ?>" />
											<img class="mmp-gpx-end-icon mmp-align-middle" src="<?= (!$settings['gpxEndIcon']) ? plugins_url('images/leaflet/gpx-end.png', MMP::$path) : MMP::$icons_url . $settings['gpxEndIcon'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxIntervalMarkers"><?= esc_html__('Show interval markers', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="gpxIntervalMarkers" name="gpxIntervalMarkers" <?= !$settings['gpxIntervalMarkers'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label><br />
											<?= esc_html__('Will display a marker each kilometer or mile, depending on the units setting', 'mmp') ?>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxTrackSmoothFactor"><?= esc_html__('Track smooth factor', 'mmp') ?>*</label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxTrackSmoothFactor" name="gpxTrackSmoothFactor" value="<?= $settings['gpxTrackSmoothFactor'] ?>" min="0" step="0.1" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxTrackColor"><?= esc_html__('Track color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxTrackColor" name="gpxTrackColor" value="<?= $settings['gpxTrackColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxTrackWeight"><?= esc_html__('Track weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxTrackWeight" name="gpxTrackWeight" value="<?= $settings['gpxTrackWeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxTrackOpacity"><?= esc_html__('Track opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxTrackOpacity" name="gpxTrackOpacity" value="<?= $settings['gpxTrackOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Metadata', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxMetaInterval"><?= esc_html__('Max interval', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxMetaInterval" name="gpxMetaInterval" value="<?= $settings['gpxMetaInterval'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Waypoints', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxWaypointsRadius"><?= esc_html__('Waypoints radius', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxWaypointsRadius" name="gpxWaypointsRadius" value="<?= $settings['gpxWaypointsRadius'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxWaypointsColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxWaypointsColor" name="gpxWaypointsColor" value="<?= $settings['gpxWaypointsColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxWaypointsWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxWaypointsWeight" name="gpxWaypointsWeight" value="<?= $settings['gpxWaypointsWeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxWaypointsFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxWaypointsFillColor" name="gpxWaypointsFillColor" value="<?= $settings['gpxWaypointsFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxWaypointsFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxWaypointsFillOpacity" name="gpxWaypointsFillOpacity" value="<?= $settings['gpxWaypointsFillOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Elevation chart', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartHeight"><?= esc_html__('Height', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartHeight" name="gpxChartHeight" value="<?= $settings['gpxChartHeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartYMin"><?= esc_html__('Y-axis min value', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartYMin" name="gpxChartYMin" value="<?= $settings['gpxChartYMin'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartYMax"><?= esc_html__('Y-axis max value', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartYMax" name="gpxChartYMax" value="<?= $settings['gpxChartYMax'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartYOffset"><?= esc_html__('Y-axis offset value', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartYOffset" name="gpxChartYOffset" value="<?= $settings['gpxChartYOffset'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLineTension"><?= esc_html__('Line tension', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLineTension" name="gpxChartLineTension" value="<?= $settings['gpxChartLineTension'] ?>" min="0" step="0.01" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartBgColor"><?= esc_html__('Background color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartBgColor" name="gpxChartBgColor" value="<?= $settings['gpxChartBgColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartGridLinesColor"><?= esc_html__('Grid lines color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartGridLinesColor" name="gpxChartGridLinesColor" value="<?= $settings['gpxChartGridLinesColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartTicksFontColor"><?= esc_html__('Ticks font color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartTicksFontColor" name="gpxChartTicksFontColor" value="<?= $settings['gpxChartTicksFontColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLineWidth"><?= esc_html__('Line width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLineWidth" name="gpxChartLineWidth" value="<?= $settings['gpxChartLineWidth'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLineColor"><?= esc_html__('Line color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartLineColor" name="gpxChartLineColor" value="<?= $settings['gpxChartLineColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartFillColor" name="gpxChartFillColor" value="<?= $settings['gpxChartFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartTooltipBgColor"><?= esc_html__('Tooltip background color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartTooltipBgColor" name="gpxChartTooltipBgColor" value="<?= $settings['gpxChartTooltipBgColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartTooltipFontColor"><?= esc_html__('Tooltip font color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartTooltipFontColor" name="gpxChartTooltipFontColor" value="<?= $settings['gpxChartTooltipFontColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartIndicatorLineWidth"><?= esc_html__('Indicator line width', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartIndicatorLineWidth" name="gpxChartIndicatorLineWidth" value="<?= $settings['gpxChartIndicatorLineWidth'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartIndicatorLineColor"><?= esc_html__('Indicator line color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartIndicatorLineColor" name="gpxChartIndicatorLineColor" value="<?= $settings['gpxChartIndicatorLineColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLocatorRadius"><?= esc_html__('Locator radius', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLocatorRadius" name="gpxChartLocatorRadius" value="<?= $settings['gpxChartLocatorRadius'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
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
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLocatorColor"><?= esc_html__('Locator stroke color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartLocatorColor" name="gpxChartLocatorColor" value="<?= $settings['gpxChartLocatorColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLocatorWeight"><?= esc_html__('Locator stroke weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLocatorWeight" name="gpxChartLocatorWeight" value="<?= $settings['gpxChartLocatorWeight'] ?>" min="0" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLocatorFillColor"><?= esc_html__('Locator fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="gpxChartLocatorFillColor" name="gpxChartLocatorFillColor" value="<?= $settings['gpxChartLocatorFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="gpxChartLocatorFillOpacity"><?= esc_html__('Locator fill opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="gpxChartLocatorFillOpacity" name="gpxChartLocatorFillOpacity" value="<?= $settings['gpxChartLocatorFillOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
							</div>
							<div id="mmp-tabDraw-settings" class="mmp-tab">
								<button type="button" id="addGeoJson" class="button button-secondary"><?= esc_html__('Add from GeoJSON', 'mmp') ?></button>
								<p><?= esc_html__('Shapes added via filters are hidden while this tab is active', 'mmp') ?></p>
								<div id="geoJsonModal" class="mmp-hidden">
									<textarea id="geoJsonText"></textarea>
									<button type="button" id="geoJsonCancel" class="button button-secondary"><?= esc_html__('Cancel', 'mmp') ?></button>
									<button type="button" id="geoJsonSave" class="button button-secondary"><?= esc_html__('Save', 'mmp') ?></button>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('New shape settings', 'mmp') ?></span>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawStroke"><?= esc_html__('Stroke', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="drawStroke" name="drawStroke" <?= !$settings['drawStroke'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawStrokeColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="drawStrokeColor" name="drawStrokeColor" value="<?= $settings['drawStrokeColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawStrokeWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="drawStrokeWeight" name="drawStrokeWeight" value="<?= $settings['drawStrokeWeight'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawStrokeOpacity"><?= esc_html__('Stroke opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="drawStrokeOpacity" name="drawStrokeOpacity" value="<?= $settings['drawStrokeOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="drawLineCap"><?= esc_html__('Line cap', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="drawLineCap" name="drawLineCap">
												<option value="butt" <?= !($settings['drawLineCap'] === 'butt') ?: 'selected="selected"' ?>><?= esc_html__('Butt', 'mmp') ?></option>
												<option value="round" <?= !($settings['drawLineCap'] === 'round') ?: 'selected="selected"' ?>><?= esc_html__('Round', 'mmp') ?></option>
												<option value="square" <?= !($settings['drawLineCap'] === 'square') ?: 'selected="selected"' ?>><?= esc_html__('Square', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="drawLineJoin"><?= esc_html__('Line join', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="drawLineJoin" name="drawLineJoin">
												<option value="arcs" <?= !($settings['drawLineJoin'] === 'arcs') ?: 'selected="selected"' ?>><?= esc_html__('Arcs', 'mmp') ?></option>
												<option value="bevel" <?= !($settings['drawLineJoin'] === 'bevel') ?: 'selected="selected"' ?>><?= esc_html__('Bevel', 'mmp') ?></option>
												<option value="miter" <?= !($settings['drawLineJoin'] === 'miter') ?: 'selected="selected"' ?>><?= esc_html__('Miter', 'mmp') ?></option>
												<option value="miter-clip" <?= !($settings['drawLineJoin'] === 'miter-clip') ?: 'selected="selected"' ?>><?= esc_html__('Miter-Clip', 'mmp') ?></option>
												<option value="round" <?= !($settings['drawLineJoin'] === 'round') ?: 'selected="selected"' ?>><?= esc_html__('Round', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawFill"><?= esc_html__('Fill', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="drawFill" name="drawFill" <?= !$settings['drawFill'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="drawFillColor" name="drawFillColor" value="<?= $settings['drawFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="drawFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="drawFillOpacity" name="drawFillOpacity" value="<?= $settings['drawFillOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="drawFillRule"><?= esc_html__('Fill rule', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="drawFillRule" name="drawFillRule">
												<option value="nonzero" <?= !($settings['drawFillRule'] === 'nonzero') ?: 'selected="selected"' ?>><?= esc_html__('Nonzero', 'mmp') ?></option>
												<option value="evenodd" <?= !($settings['drawFillRule'] === 'evenodd') ?: 'selected="selected"' ?>><?= esc_html__('Evenodd', 'mmp') ?></option>
											</select>
										</div>
									</div>
								</div>
								<div class="mmp-map-settings-group">
									<span><?= esc_html__('Added shapes', 'mmp') ?></span>
									<div id="shapesList">
										<label><input type="checkbox" id="shapesSelectAll" /> <?= esc_html__('Select all', 'mmp') ?></label>
										<ul id="geoJson"></ul>
										<span id="shapesDeleteSelected" class="mmp-delete" href=""><?= esc_html__('Delete selected', 'mmp') ?></span>
									</div>
								</div>
								<div id="editShape" class="mmp-edit-shape">
									<input type="hidden" id="shapeId" name="shapeId" value="" />
									<input type="hidden" id="shapeBackup" name="shapeBackup" value="" />
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawStroke"><?= esc_html__('Stroke', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="editDrawStroke" name="editDrawStroke" <?= !$settings['drawStroke'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawStrokeColor"><?= esc_html__('Stroke color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="editDrawStrokeColor" name="editDrawStrokeColor" value="<?= $settings['drawStrokeColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawStrokeWeight"><?= esc_html__('Stroke weight', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="editDrawStrokeWeight" name="editDrawStrokeWeight" value="<?= $settings['drawStrokeWeight'] ?>" min="1" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawStrokeOpacity"><?= esc_html__('Stroke opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="editDrawStrokeOpacity" name="editDrawStrokeOpacity" value="<?= $settings['drawStrokeOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="editDrawLineCap"><?= esc_html__('Line cap', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="editDrawLineCap" name="editDrawLineCap">
												<option value="butt" <?= !($settings['drawLineCap'] === 'butt') ?: 'selected="selected"' ?>><?= esc_html__('Butt', 'mmp') ?></option>
												<option value="round" <?= !($settings['drawLineCap'] === 'round') ?: 'selected="selected"' ?>><?= esc_html__('Round', 'mmp') ?></option>
												<option value="square" <?= !($settings['drawLineCap'] === 'square') ?: 'selected="selected"' ?>><?= esc_html__('Square', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="editDrawLineJoin"><?= esc_html__('Line join', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="editDrawLineJoin" name="editDrawLineJoin">
												<option value="arcs" <?= !($settings['drawLineJoin'] === 'arcs') ?: 'selected="selected"' ?>><?= esc_html__('Arcs', 'mmp') ?></option>
												<option value="bevel" <?= !($settings['drawLineJoin'] === 'bevel') ?: 'selected="selected"' ?>><?= esc_html__('Bevel', 'mmp') ?></option>
												<option value="miter" <?= !($settings['drawLineJoin'] === 'miter') ?: 'selected="selected"' ?>><?= esc_html__('Miter', 'mmp') ?></option>
												<option value="miter-clip" <?= !($settings['drawLineJoin'] === 'miter-clip') ?: 'selected="selected"' ?>><?= esc_html__('Miter-Clip', 'mmp') ?></option>
												<option value="round" <?= !($settings['drawLineJoin'] === 'round') ?: 'selected="selected"' ?>><?= esc_html__('Round', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawFill"><?= esc_html__('Fill', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<label>
												<div class="switch">
													<input type="checkbox" id="editDrawFill" name="editDrawFill" <?= !$settings['drawFill'] ?: 'checked="checked"' ?> />
													<span class="slider"></span>
												</div>
											</label>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawFillColor"><?= esc_html__('Fill color', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="text" id="editDrawFillColor" name="editDrawFillColor" value="<?= $settings['drawFillColor'] ?>" />
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawFillOpacity"><?= esc_html__('Fill opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<input type="number" id="editDrawFillOpacity" name="editDrawFillOpacity" value="<?= $settings['drawFillOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
									<div class="mmp-map-setting mmp-advanced">
										<div class="mmp-map-setting-desc">
											<label for="editDrawFillRule"><?= esc_html__('Fill rule', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<select id="editDrawFillRule" name="editDrawFillRule">
												<option value="nonzero" <?= !($settings['drawFillRule'] === 'nonzero') ?: 'selected="selected"' ?>><?= esc_html__('Nonzero', 'mmp') ?></option>
												<option value="evenodd" <?= !($settings['drawFillRule'] === 'evenodd') ?: 'selected="selected"' ?>><?= esc_html__('Evenodd', 'mmp') ?></option>
											</select>
										</div>
									</div>
									<div class="mmp-map-setting">
										<div class="mmp-map-setting-desc">
											<label for="editDrawPopup"><?= esc_html__('Popup', 'mmp') ?></label>
										</div>
										<div class="mmp-map-setting-input">
											<textarea id="editDrawPopup" name="editDrawPopup"></textarea>
										</div>
									</div>
									<button type="button" id="editDrawCancel" class="button button-secondary"><?= esc_html__('Cancel', 'mmp') ?></button>
									<button type="button" id="editDrawSave" class="button button-secondary"><?= esc_html__('Save', 'mmp') ?></button>
									<button type="button" id="editDrawDelete" class="button button-secondary"><?= esc_html__('Delete', 'mmp') ?></button>
								</div>
							</div>
							<?php if ($settings['id'] !== 'new'): ?>
								<div class="mmp-bottom-bar">
									<div>
										<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_marker&basemap=' . $settings['basemapDefault'] . '&lat=' . $settings['lat'] . '&lng=' . $settings['lng'] . '&zoom=' . $settings['zoom'] . '&map=' . $settings['id']) ?>" target="_blank"><?= esc_html__('Add marker', 'mmp') ?></a>
										<?php if ($map->created_by_id == $current_user->ID || current_user_can('mmp_delete_other_maps')): ?>
											| <span id="deleteMap" class="mmp-delete" href=""><?= esc_html__('Delete', 'mmp') ?></span>
										<?php endif; ?>
									</div>
									<div>
										<table>
											<tr>
												<th><?= esc_html__('Shortcode', 'mmp') ?></th>
												<td>
													<input class="mmp-shortcode" type="text" value="[<?= MMP::$settings['shortcode'] ?> map=&quot;<?= $settings['id'] ?>&quot;]" readonly="readonly" />
												</td>
											</tr>
											<tr>
												<th><?= esc_html__('Used in content', 'mmp') ?></th>
												<td>
													<?php if ($shortcodes): ?>
														<ul class="mmp-used-in">
															<?php foreach ($shortcodes as $shortcode): ?>
																<li>
																	<a href="<?= $shortcode['edit'] ?>" title="<?= esc_attr__('Edit post', 'mmp') ?>" target="_blank"><img src="<?= plugins_url('images/icons/edit-layer.png', MMP::$path) ?>" /></a>
																	<a href="<?= $shortcode['link'] ?>" title="<?= esc_attr__('View post', 'mmp') ?>" target="_blank"><?= $shortcode['title'] ?></a>
																</li>
															<?php endforeach; ?>
														</ul>
													<?php else: ?>
														<?= esc_html__('Not used in any content', 'mmp') ?>
													<?php endif; ?>
												</td>
											</tr>
										</table>
									</div>
								</div>
							<?php endif; ?>
							<p>*<?= esc_html__('No preview - save and reload to see changes', 'mmp') ?></p>
							<?php if (current_user_can('mmp_change_settings')): ?>
								<a id="saveDefaultsLink" href="#"><?= esc_html__('Save current values as defaults for new maps', 'mmp') ?></a>
								<div class="mmp-save-defaults">
									<button type="button" id="saveDefaultsConfirm" class="button button-secondary"><?= esc_html__('OK', 'mmp') ?></button>
									<button type="button" id="saveDefaultsCancel" class="button button-secondary"><?= esc_html__('Cancel', 'mmp') ?></button>
								</div>
							<?php endif; ?>
						</div>
						<div class="mmp-right">
							<div id="maps-marker-pro-admin" class="maps-marker-pro"></div>
						</div>
					</div>
				</form>
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
						<img class="mmp-icon" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" data-icon="" />
						<img class="mmp-icon" src="<?= plugins_url('images/leaflet/gpx-start.png', MMP::$path) ?>" data-icon="" />
						<img class="mmp-icon" src="<?= plugins_url('images/leaflet/gpx-end.png', MMP::$path) ?>" data-icon="" />
						<img class="mmp-icon" src="<?= plugins_url('images/leaflet/pin.png', MMP::$path) ?>" data-icon="" />
						<?php foreach ($upload->get_icons() as $icon): ?>
							<img class="mmp-icon" src="<?= MMP::$icons_url . $icon ?>" data-icon="<?= $icon ?>" />
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
