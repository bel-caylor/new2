<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Marker extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_preview_map_markers', array($this, 'preview_map_markers'));
		add_action('wp_ajax_mmp_save_marker', array($this, 'save_marker'));
		add_action('wp_ajax_mmp_save_marker_defaults', array($this, 'save_marker_defaults'));
		add_action('wp_ajax_mmp_delete_marker_direct', array($this, 'delete_marker'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook The current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_marker')) !== 'mapsmarkerpro_marker') {
			return;
		}

		$this->load_global_resources($hook);

		if (MMP::$settings['googleApiKey']) {
			wp_enqueue_script('mmp-googlemaps');
		}
		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'var editMarker = new editMarkerActions();');
	}

	/**
	 * Retrieves the preview map markers
	 *
	 * @since 4.13
	 */
	public function preview_map_markers() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-marker', 'nonce');

		$id = (isset($_POST['id'])) ? absint($_POST['id']) : null;
		if (!$id) {
			wp_send_json_success(array());
		}
		$map = $db->get_map($id);
		if (!$map) {
			wp_send_json_success(array());
		}
		$settings = $mmp_settings->validate_map_settings(json_decode($map->settings, true));
		if ($settings['filtersAllMarkers']) {
			$filters = array('scheduled' => false);
		} else {
			$ids = array_keys(json_decode($map->filters, true));
			$ids[] = $id;
			$filters = array(
				'include_maps' => $ids,
				'scheduled'    => false
			);
		}
		$markers = $db->get_all_markers($filters);
		$geojson['type'] = 'FeatureCollection';
		if (!count($markers)) {
			$geojson['features']['type'] = 'Feature';
		} else {
			foreach ($markers as $marker) {
				$geojson['features'][] = array(
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
						'icon' => ($marker->icon) ? MMP::$icons_url . $marker->icon : plugins_url('images/leaflet/marker.png', MMP::$path)
					)
				);
			}
		}

		wp_send_json_success($geojson);
	}

	/**
	 * Saves the marker
	 *
	 * @since 4.0
	 */
	public function save_marker() {
		global $wpdb;
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-marker', 'nonce');

		$current_user = wp_get_current_user();
		$date = gmdate('Y-m-d H:i:s');
		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$id = $settings['id'];
		$data = array(
			'name'           => $settings['name'],
			'address'        => $settings['address'],
			'lat'            => $settings['lat'],
			'lng'            => $settings['lng'],
			'zoom'           => $settings['zoom'],
			'icon'           => $settings['iconTarget'],
			'popup'          => $settings['popup'],
			'link'           => $settings['link'],
			'blank'          => $settings['blank'],
			'schedule_from'  => ($settings['scheduleFrom']) ? get_gmt_from_date($settings['scheduleFrom']) : null,
			'schedule_until' => ($settings['scheduleUntil']) ? get_gmt_from_date($settings['scheduleUntil']) : null,
			'created_by_id'  => $current_user->ID,
			'created_on'     => $date,
			'updated_by_id'  => $current_user->ID,
			'updated_on'     => $date
		);
		if ($id === 'new') {
			if (!current_user_can('mmp_add_markers')) {
				wp_send_json_error(esc_html__('You do not have the required capabilities to add markers.', 'mmp'));
			}
			$id = $db->add_marker((object) $data);
			if ($id === false) {
				wp_send_json_error(esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')');
			}
			do_action('mmp_save_marker', $id, $data, true);
			do_action('mmp_add_marker', $id, $data);
		} else {
			$id = absint($id);
			if (!$id) {
				wp_send_json_error(esc_html__('Invalid marker ID', 'mmp'));
			}
			$marker = $db->get_marker($id);
			if (!$marker) {
				wp_send_json_error(sprintf(esc_html__('A marker with ID %1$s does not exist.', 'mmp'), $id));
			}
			if ($marker->created_by_id != $current_user->ID && !current_user_can('mmp_edit_other_markers')) {
				wp_send_json_error(sprintf(esc_html__('You do not have the required capabilities to edit the marker with ID %1$s.', 'mmp'), $id));
			}
			$update = $db->update_marker((object) $data, $id);
			if ($update === false) {
				wp_send_json_error(esc_html__('Database error', 'mmp') . ' (' . $wpdb->last_error . ')');
			}
			do_action('mmp_save_marker', $id, $data, false);
			do_action('mmp_update_marker', $id, $data);
			if ($marker->maps) {
				$db->unassign_maps_marker($marker->maps, $id);
			}
		}
		if (isset($settings['assignedMaps']) && is_array($settings['assignedMaps'])) {
			foreach ($settings['assignedMaps'] as $map) {
				$db->assign_marker($map, $id);
			}
		}

		wp_send_json_success(array(
			'id'      => $id,
			'message' => esc_html__('Marker saved successfully', 'mmp')
		));
	}

	/**
	 * Saves the marker defaults
	 *
	 * @since 4.0
	 */
	public function save_marker_defaults() {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-marker', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$settings['icon'] = $settings['iconTarget']; // Workaround, needs to be improved

		$settings = $mmp_settings->validate_marker_settings($settings, false, false);
		update_option('mapsmarkerpro_marker_defaults', $settings);

		wp_send_json_success();
	}

	/**
	 * Deletes the marker
	 *
	 * @since 4.0
	 */
	public function delete_marker() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-marker', 'nonce');

		$id = absint($_POST['id']);
		if (!$id) {
			wp_send_json_error();
		}

		$marker = $db->get_marker($id);
		if (!$marker) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();
		if ($marker->created_by_id != $current_user->ID && !current_user_can('mmp_delete_other_markers')) {
			wp_send_json_error();
		}

		$db->delete_marker($id);

		wp_send_json_success();
	}

	/**
	 * Shows the marker page
	 *
	 * @since 4.0
	 */
	protected function show() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');
		$upload = MMP::get_instance('MMP\FS\Upload');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$layers = MMP::get_instance('MMP\Layers');

		$current_user = wp_get_current_user();

		$maps = $db->get_all_maps(true);
		$basemaps = $layers->get_basemaps();

		$id = (isset($_GET['id'])) ? absint($_GET['id']) : 'new';
		if ($id !== 'new') {
			$marker = $db->get_marker($id);
			if (!$marker) {
				$this->error(sprintf(esc_html__('A marker with ID %1$s does not exist.', 'mmp'), $id));
				return;
			}
			if ($marker->created_by_id != $current_user->ID && !current_user_can('mmp_edit_other_markers')) {
				$this->error(sprintf(esc_html__('You do not have the required capabilities to edit the marker with ID %1$s.', 'mmp'), $id));
				return;
			}
			$settings = $mmp_settings->get_marker_defaults();
			$settings['name'] = $marker->name;
			$settings['address'] = $marker->address;
			$settings['lat'] = $marker->lat;
			$settings['lng'] = $marker->lng;
			$settings['zoom'] = $marker->zoom;
			$settings['icon'] = $marker->icon;
			$settings['popup'] = $marker->popup;
			$settings['link'] = $marker->link;
			$settings['blank'] = $marker->blank;
			$settings['scheduleFrom'] = ($marker->schedule_from && $marker->schedule_from !== '0000-00-00 00:00:00') ? get_date_from_gmt($marker->schedule_from) : null;
			$settings['scheduleUntil'] = ($marker->schedule_until && $marker->schedule_until !== '0000-00-00 00:00:00') ? get_date_from_gmt($marker->schedule_until) : null;
			$settings['maps'] = $db->sanitize_ids($marker->maps);
		} else {
			$settings = $mmp_settings->get_marker_defaults();
			$settings['basemap'] = isset($_GET['basemap']) ? preg_replace('/[^0-9A-Za-z]/', '', $_GET['basemap']) : $settings['basemap'];
			$settings['name'] = '';
			$settings['address'] = '';
			$settings['lat'] = isset($_GET['lat']) ? floatval($_GET['lat']) : $settings['lat'];
			$settings['lng'] = isset($_GET['lng']) ? floatval($_GET['lng']) : $settings['lng'];
			$settings['zoom'] = isset($_GET['zoom']) ? abs(floatval($_GET['zoom'])) : $settings['zoom'];
			$settings['popup'] = '';
			$settings['link'] = '';
			$settings['blank'] = '1';
			$settings['scheduleFrom'] = null;
			$settings['scheduleUntil'] = null;
			$settings['maps'] = isset($_GET['map']) ? array(absint($_GET['map'])) : array();
		}

		$globals = array(
			'googleApiKey' => MMP::$settings['googleApiKey'],
			'bingApiKey' => MMP::$settings['bingApiKey'],
			'bingCulture' => (MMP::$settings['bingCulture'] === 'automatic') ? str_replace('_', '-', get_locale()) : MMP::$settings['bingCulture'],
			'hereApiKey' => MMP::$settings['hereApiKey'],
			'hereAppId' => MMP::$settings['hereAppId'],
			'hereAppCode' => MMP::$settings['hereAppCode'],
			'tomApiKey' => MMP::$settings['tomApiKey'],
			'geocodingMinChars' => MMP::$settings['geocodingMinChars'],
			'geocodingLocationIqApiKey' => MMP::$settings['geocodingLocationIqApiKey'],
			'geocodingMapQuestApiKey' => MMP::$settings['geocodingMapQuestApiKey'],
			'geocodingGoogleApiKey' => MMP::$settings['geocodingGoogleApiKey'],
			'geocodingTomTomApiKey' => MMP::$settings['geocodingTomTomApiKey']
		);

		$settings = array_merge($globals, $settings);

		$tinymce_settings = array(
			'tinymce' => array(
				'setup' => "function(ed) {
					ed.on('change', function() {
						jQuery('#popup').val(this.getContent()).trigger('change');
					});
				}",
			)
		);

		?>
		<div class="wrap mmp-wrap">
			<h1><?= ($id !== 'new') ? esc_html__('Edit marker', 'mmp') : esc_html__('Add marker', 'mmp') ?></h1>
			<input type="hidden" id="nonce" name="nonce" value="<?= wp_create_nonce('mmp-marker') ?>" />
			<div class="mmp-main">
				<form id="markerSettings" method="POST">
					<input type="hidden" id="id" name="id" value="<?= $id ?>" />
					<div class="mmp-flexwrap mmp-edit-marker">
						<div class="mmp-left">
							<div class="mmp-settings-widget">
								<div class="mmp-marker-settings">
									<h2><?= esc_html__('Settings', 'mmp') ?></h2>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="name"><?= esc_html__('Name', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<input type="text" id="name" name="name" value="<?= esc_attr($settings['name']) ?>" />
											<?php if ($id !== 'new'): ?>
												<br />
												<?php if ($l10n->check_ml() === 'wpml'): ?>
													(<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
												<?php elseif ($l10n->check_ml() === 'pll'): ?>
													(<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Marker+%28ID+' . $id . '%29+name&group=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
												<?php else: ?>
													(<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('translate', 'mmp') ?></a>)
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="address"><?= esc_html__('Address', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<?php if (!$settings['geocodingLocationIqApiKey'] && !$settings['geocodingMapQuestApiKey'] && !$settings['geocodingGoogleApiKey'] && !$settings['geocodingTomTomApiKey']): ?>
												<span class="mmp-warning"><?= sprintf($l10n->kses__('To use the geocoding feature, please activate one or more providers in the <a href="%1$s" target="_blank">geocoding settings</a>', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_settings#geocoding_provider')) ?></span><br />
											<?php endif; ?>
											<div id="geocodingError"></div>
											<input type="text" id="address" name="address" placeholder="<?= ($settings['geocodingMinChars'] < 2) ? esc_attr__('Start typing for suggestions', 'mmp') : sprintf(esc_attr__('Start typing for suggestions (%1$s characters minimum)', 'mmp'), $settings['geocodingMinChars']) ?>" value="<?= $settings['address'] ?>" /><br />
											<div id="markerLocationWarning">
												<p><?= esc_html__('The marker has been moved since this address was selected and may no longer correspond to the correct location.', 'mmp') ?></p>
												<button type="button" id="resetMarkerLocation"><?= esc_html__('Reset location', 'mmp') ?></button>
											</div>
											<?php if ($id !== 'new'): ?>
												<?php if ($l10n->check_ml() === 'wpml'): ?>
													(<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)<br />
												<?php elseif ($l10n->check_ml() === 'pll'): ?>
													(<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Marker+%28ID+' . $id . '%29+address&group=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)<br />
												<?php else: ?>
													(<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('translate', 'mmp') ?></a>)<br />
												<?php endif; ?>
											<?php endif; ?>
											<?php if ($settings['geocodingLocationIqApiKey'] || $settings['geocodingMapQuestApiKey'] || $settings['geocodingGoogleApiKey'] || $settings['geocodingTomTomApiKey']): ?>
												<select id="geocodingProvider">
													<optgroup label="<?= esc_attr__('Available providers', 'mmp') ?>">
														<?php if ($settings['geocodingLocationIqApiKey']): ?>
															<option value="locationiq" <?= MMP::$settings['geocodingProvider'] !== 'locationiq' ?: 'selected="selected"' ?>>LocationIQ</option>
														<?php endif; ?>
														<?php if ($settings['geocodingMapQuestApiKey']): ?>
															<option value="mapquest" <?= MMP::$settings['geocodingProvider'] !== 'mapquest' ?: 'selected="selected"' ?>>MapQuest</option>
														<?php endif; ?>
														<?php if ($settings['geocodingGoogleApiKey']): ?>
															<option value="google" <?= MMP::$settings['geocodingProvider'] !== 'google' ?: 'selected="selected"' ?>>Google</option>
														<?php endif; ?>
														<?php if ($settings['geocodingTomTomApiKey']): ?>
															<option value="tomtom" <?= MMP::$settings['geocodingProvider'] !== 'tomtom' ?: 'selected="selected"' ?>>TomTom</option>
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
											<?php endif; ?>
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="lat"><?= esc_html__('Latitude', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<input type="text" id="lat" name="lat" value="<?= $settings['lat'] ?>" />
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="lng"><?= esc_html__('Longitude', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<input type="text" id="lng" name="lng" value="<?= $settings['lng'] ?>" />
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="zoom"><?= esc_html__('Zoom', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<input type="number" id="zoom" name="zoom" min="0" max="23" step="0.5" value="<?= $settings['zoom'] ?>" />
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="changeIcon"><?= esc_html__('Icon', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<input type="hidden" id="iconTarget" name="iconTarget" value="<?= $settings['icon'] ?>" />
											<button type="button" id="changeIcon"><?= esc_html__('Change', 'mmp') ?></button>
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="assignedMaps"><?= esc_html__('Maps', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<select id="assignedMaps" name="assignedMaps[]" multiple="multiple">
												<?php foreach ($maps as $map): ?>
													<option value="<?= $map->id ?>" <?= (!in_array($map->id, $settings['maps'])) ?: 'selected="selected"' ?>>[<?= $map->id ?>] <?= esc_html($map->name) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="mmp-marker-setting">
										<div class="mmp-marker-setting-desc">
											<label for="action"><?= esc_html__('Action', 'mmp') ?></label>
										</div>
										<div class="mmp-marker-setting-input">
											<label><input type="radio" name="action" value="popup" <?= ($settings['link']) ?: 'checked="checked"' ?> /> <?= esc_html__('Show popup', 'mmp') ?></label><br />
											<label><input type="radio" name="action" value="link" <?= (!$settings['link']) ?: 'checked="checked"' ?> /> <?= esc_html__('Open link', 'mmp') ?></label>
										</div>
									</div>
									<div id="link_settings">
										<div class="mmp-marker-setting">
											<div class="mmp-marker-setting-desc">
												<label for="link"><?= esc_html__('URL', 'mmp') ?></label>
											</div>
											<div class="mmp-marker-setting-input">
												<input type="text" id="link" name="link" value="<?= $settings['link'] ?>" />
												<?php if ($id !== 'new'): ?>
													<br />
													<?php if ($l10n->check_ml() === 'wpml'): ?>
														(<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
													<?php elseif ($l10n->check_ml() === 'pll'): ?>
														(<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Marker+%28ID+' . $id . '%29+link&group=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
													<?php else: ?>
														(<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('translate', 'mmp') ?></a>)
													<?php endif; ?>
												<?php endif; ?>
											</div>
										</div>
										<div class="mmp-marker-setting">
											<div class="mmp-marker-setting-desc">
												<label for="blank"><?= esc_html__('Target', 'mmp') ?></label>
											</div>
											<div class="mmp-marker-setting-input">
												<label><input type="radio" name="blank" value="0" <?= !($settings['blank'] == '0') ?: 'checked="checked"' ?> /> <?= esc_html__('Same tab', 'mmp') ?></label>
												<label><input type="radio" name="blank" value="1" <?= !($settings['blank'] == '1') ?: 'checked="checked"' ?> /> <?= esc_html__('New tab', 'mmp') ?></label>
											</div>
										</div>
									</div>
									<?php if (current_user_can('mmp_change_settings')): ?>
										<a id="saveDefaultsLink" href="#"><?= esc_html__('Save current values as defaults for new markers', 'mmp') ?></a>
										<div class="mmp-save-defaults">
											<button type="button" id="saveDefaultsConfirm" class="button button-secondary"><?= esc_html__('OK', 'mmp') ?></button>
											<button type="button" id="saveDefaultsCancel" class="button button-secondary"><?= esc_html__('Cancel', 'mmp') ?></button>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="mmp-middle">
							<div id="maps-marker-pro-marker" class="maps-marker-pro"></div>
						</div>
						<div class="mmp-right">
							<div class="mmp-settings-widget">
								<div class="mmp-publish">
									<h2><?= esc_html__('Publish', 'mmp') ?></h2>
									<p><?= esc_html__('Marker will only appear on maps within the set time span', 'mmp') ?></p>
									<div class="mmp-widget-setting">
										<div class="mmp-widget-setting-desc">
											<label for="scheduleFrom"><?= esc_html__('From', 'mmp') ?></label>
										</div>
										<div class="mmp-widget-setting-input">
											<input type="text" id="scheduleFrom" name="scheduleFrom" class="mmp-scheduler-input" placeholder="<?= esc_html__('Always', 'mmp') ?>" value="<?= $settings['scheduleFrom'] ?>" />
											<i id="scheduleFromClear" class="dashicons dashicons-no"></i>
										</div>
									</div>
									<div class="mmp-widget-setting">
										<div class="mmp-widget-setting-desc">
											<label for="scheduleUntil"><?= esc_html__('Until', 'mmp') ?></label>
										</div>
										<div class="mmp-widget-setting-input">
											<input type="text" id="scheduleUntil" name="scheduleUntil" class="mmp-scheduler-input" placeholder="<?= esc_html__('Always', 'mmp') ?>" value="<?= $settings['scheduleUntil'] ?>" />
											<i id="scheduleUntilClear" class="dashicons dashicons-no"></i>
										</div>
									</div>
								</div>
								<div class="mmp-publish-actions">
									<?php if ($id !== 'new' && ($marker->created_by_id == $current_user->ID || current_user_can('mmp_delete_other_markers'))): ?>
										<div class="mmp-publish-delete">
											<span class="mmp-delete" href=""><?= esc_html__('Delete', 'mmp') ?></span>
										</div>
									<?php endif; ?>
									<div class="mmp-publish-submit">
										<button id="save" class="button button-primary" disabled="disabled">
											<?php if ($id === 'new'): ?>
												<?= esc_html__('Publish', 'mmp') ?>
											<?php else: ?>
												<?= esc_html__('Update', 'mmp') ?>
											<?php endif; ?>
										</button>
									</div>
								</div>
							</div>
							<div class="mmp-settings-widget">
								<div class="mmp-preview">
									<h2><?= esc_html__('Preview', 'mmp') ?></h2>
									<div class="mmp-widget-setting">
										<div class="mmp-widget-setting-desc">
											<label for="name"><?= esc_html__('Basemap', 'mmp') ?></label>
										</div>
										<div class="mmp-widget-setting-input">
											<select id="basemap" name="basemap">
												<?php foreach ($basemaps as $bid => $basemap): ?>
													<option value="<?= $bid ?>" <?= !($settings['basemap'] == $bid) ?: 'selected="selected"' ?>><?= esc_html($basemap['name']) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="mmp-widget-setting">
										<div class="mmp-widget-setting-desc">
											<label for="previewMap"><?= esc_html__('Map', 'mmp') ?></label>
										</div>
										<div class="mmp-widget-setting-input">
											<select id="previewMap" name="previewMap">
												<option value="0"><?= esc_html__('No preview', 'mmp') ?></option>
												<?php foreach ($maps as $map): ?>
													<option value="<?= $map->id ?>">[<?= $map->id ?>] <?= esc_html($map->name) ?> (<?= $map->markers ?> <?= esc_html__('markers', 'mmp') ?>)</option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="mmp-widget-setting">
										<div class="mmp-widget-setting-desc">
											<label for="previewOpacity"><?= esc_html__('Opacity', 'mmp') ?></label>
										</div>
										<div class="mmp-widget-setting-input">
											<input type="range" id="previewOpacity" name="previewOpacity" value="<?= $settings['previewOpacity'] ?>" min="0" max="1" step="0.01" />
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mmp-below">
							<div id="editor" class="mmp-editor">
								<?php wp_editor($settings['popup'], 'popup', $tinymce_settings) ?>
								<?php if ($id !== 'new'): ?>
									<?php if ($l10n->check_ml() === 'wpml'): ?>
										(<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
									<?php elseif ($l10n->check_ml() === 'pll'): ?>
										(<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Marker+%28ID+' . $id . '%29+popup&group=Maps+Marker+Pro') ?>"><?= esc_html__('translate', 'mmp') ?></a>)
									<?php else: ?>
										(<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('translate', 'mmp') ?></a>)
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div id="icons" class="mmp-admin-modal">
						<div class="mmp-admin-modal-content">
							<span class="mmp-admin-modal-close">×</span>
							<div class="mmp-admin-modal-header">
								<p class="mmp-admin-modal-title"><?= esc_html__('Change marker icon', 'mmp') ?></p>
							</div>
							<div class="mmp-admin-modal-body">
								<?php if (current_user_can('mmp_change_settings')): ?>
									<div>
										<label>
											<span><?= esc_html__('Search', 'mmp') ?></span>
											<input type="text" id="iconSearch" value="" />
										</label>
										<button type="button" id="toggleUpload"><?= esc_html__('Upload new icon', 'mmp') ?></button>
										<div style="float:right;">
											<a href="https://mapicons.mapsmarker.com/" target="_blank" title="<?= esc_attr__('click here for 1000+ free icons', 'mmp') ?>"><img src="<?= plugins_url('images/logo-mapicons.png', MMP::$path) ?>" /></a>
										</div>
									</div>
									<div id="iconUpload">
										<input type="hidden" id="upload_nonce" value="<?= wp_create_nonce('mmp-icon-upload') ?>" />
										<?= esc_html__('Allowed file types', 'mmp') ?>: png, gif, jpg<br />
										<?= esc_html__('New icons will be uploaded to the following directory', 'mmp') ?>:<br />
										<?= MMP::$icons_url ?><br />
										<input type="file" id="uploadFile" name="uploadFile" />
										<button type="button" id="upload" name="upload" class="button button-primary"><?= esc_html__('Upload', 'mmp') ?></button>
									</div>
								<?php endif; ?>
								<div id="iconList">
									<label class="mmp-radio">
										<input type="radio" name="icon" value="" <?= ($settings['icon']) ?: 'checked="checked"' ?> />
										<img class="mmp-icon" src="<?= plugins_url('images/leaflet/marker.png', MMP::$path) ?>" />
									</label>
									<?php foreach ($upload->get_icons() as $icon): ?>
										<label class="mmp-radio">
											<input type="radio" name="icon" value="<?= $icon ?>" <?= !($settings['icon'] === $icon) ?: 'checked="checked"' ?> />
											<img class="mmp-icon" src="<?= MMP::$icons_url . $icon ?>" title="<?= $icon ?>" />
										</label>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}
