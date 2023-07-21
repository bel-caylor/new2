<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Settings extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_save_settings', array($this, 'save_settings'));
		add_action('wp_ajax_mmp_get_custom_layers', array($this, 'get_custom_layers'));
		add_action('wp_ajax_mmp_save_custom_layer', array($this, 'save_custom_layer'));
		add_action('wp_ajax_mmp_delete_custom_layer', array($this, 'delete_custom_layer'));
		add_action('wp_ajax_mmp_delete_icons', array($this, 'delete_icons'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook The current admin page
	 */
	public function load_resources($hook) {
		global $wp_scripts;

		if (substr($hook, -strlen('mapsmarkerpro_settings')) !== 'mapsmarkerpro_settings') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'settingsActions();');
	}

	/**
	 * Saves the settings
	 *
	 * @since 4.0
	 */
	public function save_settings() {
		global $wp_roles;
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$layers = MMP::get_instance('MMP\Layers');
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-settings', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$basemaps = $layers->get_basemaps(true, false);
		foreach ($basemaps as $key => $basemap) {
			if (!in_array($key, $settings['enabledBasemaps'])) {
				$settings['disabledBasemaps'][] = $key;
			}
		}

		foreach ($wp_roles->roles as $role => $values) {
			if ($role === 'administrator') {
				continue;
			}

			foreach (MMP::$capabilities as $cap) {
				if (isset($settings['role_capabilities'][$role][$cap])) {
					$wp_roles->add_cap($role, $cap);
				} else {
					$wp_roles->remove_cap($role, $cap);
				}
			}
		}

		$settings = $mmp_settings->validate_settings($settings, false, false);
		update_option('mapsmarkerpro_settings', $settings);

		// Clear geocoding cache if settings are changed that affect results
		$geocoding_options = array(
			'geocodingProvider',
			'geocodingLocationIqBounds',
			'geocodingLocationIqBoundsLat1',
			'geocodingLocationIqBoundsLon1',
			'geocodingLocationIqBoundsLat2',
			'geocodingLocationIqBoundsLon2',
			'geocodingLocationIqLanguage',
			'geocodingLocationIqCountries',
			'geocodingMapQuestBounds',
			'geocodingMapQuestBoundsLat1',
			'geocodingMapQuestBoundsLon1',
			'geocodingMapQuestBoundsLat2',
			'geocodingMapQuestBoundsLon2',
			'geocodingGoogleLocation',
			'geocodingGoogleRadius',
			'geocodingGoogleLanguage',
			'geocodingGoogleRegion',
			'geocodingGoogleComponents',
			'geocodingTomTomLat',
			'geocodingTomTomLon',
			'geocodingTomTomRadius',
			'geocodingTomTomLanguage',
			'geocodingTomTomCountrySet'
		);
		foreach ($geocoding_options as $geocoding_option) {
			if ($settings[$geocoding_option] !== MMP::$settings[$geocoding_option]) {
				$db->clear_geocoding_cache();
				break;
			}
		}

		// Clear Rank Math sitemap cache if related settings are changed
		$rankmath_options = array(
			'apiSitemap',
			'sitemapRankMath'
		);
		foreach ($rankmath_options as $rankmath_option) {
			if ($settings[$rankmath_option] !== MMP::$settings[$rankmath_option]) {
				if (class_exists('RankMath\Sitemap\Cache') && method_exists('RankMath\Sitemap\Cache', 'invalidate_storage')) {
					\RankMath\Sitemap\Cache::invalidate_storage();
				}
				break;
			}
		}

		set_transient('mapsmarkerpro_flush_rewrite_rules', true);

		wp_send_json_success(esc_html__('Settings saved successfully', 'mmp'));
	}

	/**
	 * Returns all custom layers
	 *
	 * @since 4.0
	 */
	public function get_custom_layers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-settings', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$layers = $db->get_all_layers();

		wp_send_json_success($layers);
	}

	/**
	 * Saves the custom layer
	 *
	 * @since 4.0
	 */
	public function save_custom_layer() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-settings', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$settings = wp_unslash($_POST['settings']);
		parse_str($settings, $settings);

		$data = array(
			'wms'     => (isset($settings['customLayerWms'])) ? '1' : '0',
			'overlay' => $settings['customLayerType'],
			'name'    => $settings['customLayerName'],
			'url'     => $settings['customLayerUrl'],
			'options' => array(
				'tms'           => (isset($settings['customLayerTms'])),
				'rasterTiles'   => (isset($settings['customLayerRasterTiles'])),
				'noWrap'        => (isset($settings['customLayerNoWrap'])),
				'errorTiles'    => (isset($settings['customLayerErrorTiles'])),
				'subdomains'    => preg_replace('/[^a-z0-9]/i', '', $settings['customLayerSubdomains']),
				'bounds'        => preg_replace('/[^0-9.,-]/', '', $settings['customLayerBounds']),
				'minNativeZoom' => absint($settings['customLayerMinZoom']),
				'maxNativeZoom' => absint($settings['customLayerMaxZoom']),
				'opacity'       => abs(floatval($settings['customLayerOpacity'])),
				'attribution'   => $settings['customLayerAttribution']
			)
		);
		if (isset($settings['customLayerWms'])) {
			$data['options'] = array_merge($data['options'], array(
				'transparent' => (isset($settings['customLayerTransparent'])),
				'uppercase'   => (isset($settings['customLayerUppercase'])),
				'layers'      => $settings['customLayerLayers'],
				'styles'      => $settings['customLayerStyles'],
				'format'      => $settings['customLayerFormat'],
				'version'     => $settings['customLayerVersion']
			));
		}
		$data['options'] = json_encode($data['options']);

		if (!$settings['customLayerId']) {
			$db->add_layer((object) $data);
		} else {
			$db->update_layer((object) $data, $settings['customLayerId']);
		}

		wp_send_json_success();
	}

	/**
	 * Deletes the custom layer
	 *
	 * @since 4.0
	 */
	public function delete_custom_layer() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-settings', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		$db->delete_layer($_POST['id']);

		wp_send_json_success();
	}

	/**
	 * Deletes icons
	 *
	 * @since 4.14
	 */
	public function delete_icons() {
		check_ajax_referer('mmp-settings', 'nonce');

		if (!current_user_can('mmp_change_settings')) {
			wp_send_json_error();
		}

		if (!isset($_POST['icons']) || !is_array($_POST['icons'])) {
			wp_send_json_error();
		}

		foreach ($_POST['icons'] as $icon) {
			$icon = basename($icon);
			if (!$icon || validate_file($icon) !== 0) {
				continue;
			}
			unlink(MMP::$icons_dir . $icon);
		}

		wp_send_json_success();
	}

	/**
	 * Shows the settings page
	 *
	 * @since 4.0
	 */
	protected function show() {
		global $wp_roles;
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');
		$api = MMP::get_instance('MMP\API');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$layers = MMP::get_instance('MMP\Layers');
		$upload = MMP::get_instance('MMP\FS\Upload');

		$user_caps = get_option('mapsmarkerpro_user_capabilities');
		if (!is_array($user_caps)) {
			$user_caps = array();
		}

		$settings = $mmp_settings->get_settings();
		$basemaps = $layers->get_basemaps(true, false);

		?>
		<div class="wrap mmp-wrap">
			<h1><?= esc_html__('Settings', 'mmp') ?></h1>
			<input type="hidden" name="nonce" value="<?= wp_create_nonce('mmp-settings') ?>" />
			<form id="settings" method="POST">
				<div id="top" class="mmp-settings-header">
					<button id="save" class="button button-primary" disabled="disabled"><?= esc_html__('Save', 'mmp') ?></button>
				</div>
				<div class="mmp-settings-wrap">
					<div class="mmp-settings-nav">
						<div class="mmp-settings-nav-group">
							<span><?= esc_html__('Layers', 'mmp') ?></span>
							<ul>
								<li id="layers_general_link" class="mmp-tablink"><?= esc_html__('General', 'mmp') ?></li>
								<li id="layers_google_link" class="mmp-tablink">Google Maps</li>
								<li id="layers_bing_link" class="mmp-tablink">Bing Maps</li>
								<li id="layers_here_link" class="mmp-tablink">HERE Maps</li>
								<li id="layers_tom_link" class="mmp-tablink">TomTom</li>
								<li id="layers_lima_link" class="mmp-tablink">Lima Labs</li>
								<li id="layers_custom_link" class="mmp-tablink"><?= esc_html__('Custom', 'mmp') ?></li>
							</ul>
						</div>
						<div class="mmp-settings-nav-group">
							<span><?= esc_html__('Geocoding', 'mmp') ?></span>
							<ul>
								<li id="geocoding_provider_link" class="mmp-tablink"><?= esc_html__('Provider', 'mmp') ?></li>
								<li id="geocoding_locationiq_link" class="mmp-tablink">LocationIQ</li>
								<li id="geocoding_mapquest_link" class="mmp-tablink">MapQuest</li>
								<li id="geocoding_google_link" class="mmp-tablink">Google</li>
								<li id="geocoding_tomtom_link" class="mmp-tablink">TomTom</li>
							</ul>
						</div>
						<div class="mmp-settings-nav-group">
							<span><?= esc_html__('Directions', 'mmp') ?></span>
							<ul>
								<li id="directions_provider_link" class="mmp-tablink"><?= esc_html__('Provider', 'mmp') ?></li>
								<li id="directions_google_link" class="mmp-tablink">Google Maps</li>
								<li id="directions_ors_link" class="mmp-tablink">openrouteservice.org</li>
							</ul>
						</div>
						<div class="mmp-settings-nav-group">
							<span><?= esc_html__('Misc', 'mmp') ?></span>
							<ul>
								<li id="misc_general_link" class="mmp-tablink"><?= esc_html__('General', 'mmp') ?></li>
								<li id="misc_icons_link" class="mmp-tablink"><?= esc_html__('Icons', 'mmp') ?></li>
								<li id="misc_capabilities_link" class="mmp-tablink"><?= esc_html__('Capabilities', 'mmp') ?></li>
								<li id="misc_sitemaps_link" class="mmp-tablink"><?= esc_html__('Sitemaps', 'mmp') ?></li>
								<li id="misc_wordpress_link" class="mmp-tablink"><?= esc_html__('WordPress integration', 'mmp') ?></li>
								<li id="misc_custom_js_link" class="mmp-tablink"><?= esc_html__('Custom JavaScript', 'mmp') ?></li>
								<li id="misc_backup_restore_reset_link" class="mmp-tablink mmp-warning"><?= esc_html__('Backup, restore & reset', 'mmp') ?></li>
							</ul>
						</div>
					</div>
					<div class="mmp-settings-tabs">
						<div id="layers_general_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('General', 'mmp') ?></h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Show error tiles', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="errorTiles" value="enabled" <?= $this->checked($settings['errorTiles'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="errorTiles" value="disabled" <?= $this->checked($settings['errorTiles'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Whether or not to show an error image in place of map imagery for each map tile that cannot be retrieved from the selected basemap provider. When disabled, the tiles that fail to load will show as empty squares on the map.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Fallback basemap', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="fallbackBasemap" value="enabled" <?= $this->checked($settings['fallbackBasemap'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="fallbackBasemap" value="disabled" <?= $this->checked($settings['fallbackBasemap'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, the first available basemap will be used as a fallback whenever there are no other valid basemaps available for a given map (e.g. when the API key for the only added basemap is removed or no basemap was added to the map at all).', 'mmp') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Enable / disable', 'mmp') ?></h3>
							<p>
								<?= esc_html__('You can enable or disable the built-in layers. When a layer is disabled, it will not be available when creating, editing or viewing maps. Please note that layers that require registration will not be available until credentials (API key etc.) have been added, even if they are enabled here.', 'mmp') ?>
							</p>
							<?php foreach ($basemaps as $key => $basemap): ?>
								<div class="mmp-settings-setting">
									<div class="mmp-settings-desc"><?= $basemap['name'] ?></div>
									<div class="mmp-settings-input">
										<label><input type="checkbox" name="enabledBasemaps[]" value="<?= $key ?>" <?= (in_array($key, $settings['disabledBasemaps'])) ?: 'checked="checked"' ?> /> <?= esc_html__('enabled', 'mmp') ?></label>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div id="layers_google_tab" class="mmp-settings-tab">
							<h2>Google Maps</h2>
							<p>
								<a href="https://www.mapsmarker.com/google-maps-javascript-api/" target="_blank"><img src="<?= plugins_url('images/options/google-maps-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('If you want to use Google Maps, you have to register a personal Google Maps JavaScript API key. For terms of services, pricing, usage limits and more, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/google-maps-javascript-api/" target="_blank">https://www.mapsmarker.com/google-maps-javascript-api/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Google Maps JavaScript API key', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="googleApiKey" value="<?= $settings['googleApiKey'] ?>" /></div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Default language', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="googleLanguage">
										<option value="browser_setting" <?= $this->selected($settings['googleLanguage'], 'browser_setting') ?>><?= esc_html__('Automatic (use the browser language setting)', 'mmp') ?></option>
										<option value="wordpress_setting" <?= $this->selected($settings['googleLanguage'], 'wordpress_setting') ?>><?= esc_html__('Automatic (use the WordPress language setting)', 'mmp') ?></option>
										<option value="ar" <?= $this->selected($settings['googleLanguage'], 'ar') ?>><?= esc_html__('Arabic', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ar)</option>
										<option value="be" <?= $this->selected($settings['googleLanguage'], 'be') ?>><?= esc_html__('Belarusian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: be)</option>
										<option value="bg" <?= $this->selected($settings['googleLanguage'], 'bg') ?>><?= esc_html__('Bulgarian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: bg)</option>
										<option value="bn" <?= $this->selected($settings['googleLanguage'], 'bn') ?>><?= esc_html__('Bengali', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: bn)</option>
										<option value="ca" <?= $this->selected($settings['googleLanguage'], 'ca') ?>><?= esc_html__('Catalan', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ca)</option>
										<option value="cs" <?= $this->selected($settings['googleLanguage'], 'cs') ?>><?= esc_html__('Czech', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: cs)</option>
										<option value="da" <?= $this->selected($settings['googleLanguage'], 'da') ?>><?= esc_html__('Danish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: da)</option>
										<option value="de" <?= $this->selected($settings['googleLanguage'], 'de') ?>><?= esc_html__('German', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: de)</option>
										<option value="el" <?= $this->selected($settings['googleLanguage'], 'el') ?>><?= esc_html__('Greek', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: el)</option>
										<option value="en" <?= $this->selected($settings['googleLanguage'], 'en') ?>><?= esc_html__('English', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: en)</option>
										<option value="en-AU" <?= $this->selected($settings['googleLanguage'], 'en-AU') ?>><?= esc_html__('English (Australian)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: en-AU)</option>
										<option value="en-GB" <?= $this->selected($settings['googleLanguage'], 'en-GB') ?>><?= esc_html__('English (Great Britain)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: en-GB)</option>
										<option value="es" <?= $this->selected($settings['googleLanguage'], 'es') ?>><?= esc_html__('Spanish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: es)</option>
										<option value="eu" <?= $this->selected($settings['googleLanguage'], 'eu') ?>><?= esc_html__('Basque', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: eu)</option>
										<option value="fa" <?= $this->selected($settings['googleLanguage'], 'fa') ?>><?= esc_html__('Farsi', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: fa)</option>
										<option value="fi" <?= $this->selected($settings['googleLanguage'], 'fi') ?>><?= esc_html__('Finnish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: fi)</option>
										<option value="fil" <?= $this->selected($settings['googleLanguage'], 'fil') ?>><?= esc_html__('Filipino', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: fil)</option>
										<option value="fr" <?= $this->selected($settings['googleLanguage'], 'fr') ?>><?= esc_html__('French', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: fr)</option>
										<option value="gl" <?= $this->selected($settings['googleLanguage'], 'gl') ?>><?= esc_html__('Galician', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: gl)</option>
										<option value="gu" <?= $this->selected($settings['googleLanguage'], 'gu') ?>><?= esc_html__('Gujarati', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: gu)</option>
										<option value="hi" <?= $this->selected($settings['googleLanguage'], 'hi') ?>><?= esc_html__('Hindi', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: hi)</option>
										<option value="hr" <?= $this->selected($settings['googleLanguage'], 'hr') ?>><?= esc_html__('Croatian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: hr)</option>
										<option value="hu" <?= $this->selected($settings['googleLanguage'], 'hu') ?>><?= esc_html__('Hungarian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: hu)</option>
										<option value="id" <?= $this->selected($settings['googleLanguage'], 'id') ?>><?= esc_html__('Indonesian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: id)</option>
										<option value="it" <?= $this->selected($settings['googleLanguage'], 'it') ?>><?= esc_html__('Italian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: it)</option>
										<option value="iw" <?= $this->selected($settings['googleLanguage'], 'iw') ?>><?= esc_html__('Hebrew', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: iw)</option>
										<option value="ja" <?= $this->selected($settings['googleLanguage'], 'ja') ?>><?= esc_html__('Japanese', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ja)</option>
										<option value="kk" <?= $this->selected($settings['googleLanguage'], 'kk') ?>><?= esc_html__('Kazakh', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: kk)</option>
										<option value="kn" <?= $this->selected($settings['googleLanguage'], 'kn') ?>><?= esc_html__('Kannada', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: kn)</option>
										<option value="ko" <?= $this->selected($settings['googleLanguage'], 'ko') ?>><?= esc_html__('Korean', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ko)</option>
										<option value="ky" <?= $this->selected($settings['googleLanguage'], 'ky') ?>><?= esc_html__('Kyrgyz', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ky)</option>
										<option value="lt" <?= $this->selected($settings['googleLanguage'], 'lt') ?>><?= esc_html__('Lithuanian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: lt)</option>
										<option value="lv" <?= $this->selected($settings['googleLanguage'], 'lv') ?>><?= esc_html__('Latvian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: lv)</option>
										<option value="mk" <?= $this->selected($settings['googleLanguage'], 'mk') ?>><?= esc_html__('Macedonian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: mk)</option>
										<option value="ml" <?= $this->selected($settings['googleLanguage'], 'ml') ?>><?= esc_html__('Malayalam', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ml)</option>
										<option value="mr" <?= $this->selected($settings['googleLanguage'], 'mr') ?>><?= esc_html__('Marathi', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: mr)</option>
										<option value="my" <?= $this->selected($settings['googleLanguage'], 'my') ?>><?= esc_html__('Burmese', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: my)</option>
										<option value="nl" <?= $this->selected($settings['googleLanguage'], 'nl') ?>><?= esc_html__('Dutch', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: nl)</option>
										<option value="no" <?= $this->selected($settings['googleLanguage'], 'no') ?>><?= esc_html__('Norwegian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: no)</option>
										<option value="pa" <?= $this->selected($settings['googleLanguage'], 'pa') ?>><?= esc_html__('Punjabi', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: pa)</option>
										<option value="pl" <?= $this->selected($settings['googleLanguage'], 'pl') ?>><?= esc_html__('Polish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: pl)</option>
										<option value="pt" <?= $this->selected($settings['googleLanguage'], 'pt') ?>><?= esc_html__('Portuguese', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: pt)</option>
										<option value="pt-BR" <?= $this->selected($settings['googleLanguage'], 'pt-BR') ?>><?= esc_html__('Portuguese (Brazil)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: pt-BR)</option>
										<option value="pt-PT" <?= $this->selected($settings['googleLanguage'], 'pt-PT') ?>><?= esc_html__('Portuguese (Portugal)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: pt-PT)</option>
										<option value="ro" <?= $this->selected($settings['googleLanguage'], 'ro') ?>><?= esc_html__('Romanian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ro)</option>
										<option value="ru" <?= $this->selected($settings['googleLanguage'], 'ru') ?>><?= esc_html__('Russian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ru)</option>
										<option value="sk" <?= $this->selected($settings['googleLanguage'], 'sk') ?>><?= esc_html__('Slovak', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: sk)</option>
										<option value="sl" <?= $this->selected($settings['googleLanguage'], 'sl') ?>><?= esc_html__('Slovenian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: sl)</option>
										<option value="sq" <?= $this->selected($settings['googleLanguage'], 'sq') ?>><?= esc_html__('Albanian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: sq)</option>
										<option value="sr" <?= $this->selected($settings['googleLanguage'], 'sr') ?>><?= esc_html__('Serbian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: sr)</option>
										<option value="sv" <?= $this->selected($settings['googleLanguage'], 'sv') ?>><?= esc_html__('Swedish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: sv)</option>
										<option value="ta" <?= $this->selected($settings['googleLanguage'], 'ta') ?>><?= esc_html__('Tamil', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: ta)</option>
										<option value="te" <?= $this->selected($settings['googleLanguage'], 'te') ?>><?= esc_html__('Telugu', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: te)</option>
										<option value="th" <?= $this->selected($settings['googleLanguage'], 'th') ?>><?= esc_html__('Thai', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: th)</option>
										<option value="tl" <?= $this->selected($settings['googleLanguage'], 'tl') ?>><?= esc_html__('Tagalog', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: tl)</option>
										<option value="tr" <?= $this->selected($settings['googleLanguage'], 'tr') ?>><?= esc_html__('Turkish', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: tr)</option>
										<option value="uk" <?= $this->selected($settings['googleLanguage'], 'uk') ?>><?= esc_html__('Ukrainian', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: uk)</option>
										<option value="uz" <?= $this->selected($settings['googleLanguage'], 'uz') ?>><?= esc_html__('Uzbek', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: uz)</option>
										<option value="vi" <?= $this->selected($settings['googleLanguage'], 'vi') ?>><?= esc_html__('Vietnamese', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: vi)</option>
										<option value="zh-CN" <?= $this->selected($settings['googleLanguage'], 'zh-CN') ?>><?= esc_html__('Chinese (simplified)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: zh-CN)</option>
										<option value="zh-TW" <?= $this->selected($settings['googleLanguage'], 'zh-TW') ?>><?= esc_html__('Chinese (traditional)', 'mmp') ?> (<?= esc_html__('language code', 'mmp') ?>: zh-TW)</option>
									</select><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('The language used when displaying textual information such as the names for controls, copyright notices, and labels.', 'mmp') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="layers_bing_tab" class="mmp-settings-tab">
							<h2>Bing Maps</h2>
							<p>
								<a href="https://www.mapsmarker.com/bing-maps/" target="_blank"><img src="<?= plugins_url('images/options/bing-maps-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('An API key is required if you want to use Bing Maps. For more information on how to get an API key, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/bing-maps/" target="_blank">https://www.mapsmarker.com/bing-maps/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Bing Maps API key', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="bingApiKey" value="<?= $settings['bingApiKey'] ?>" /></div>
							</div>
							<h3><?= esc_html__('Culture parameter', 'mmp') ?></h3>
							<p>
								<?= sprintf(esc_html__('The culture parameter allows you to select the language of the culture for geographic entities, place names and map labels on Bing map images. For supported cultures, street names are localized to the local culture. For example, if you request a location in France, the street names are localized in French. For other localized data such as country names, the level of localization will vary for each culture. For example, there may not be a localized name for the "United States" for every culture code. See %1$s for more details.', 'mmp'), '<a href="http://msdn.microsoft.com/en-us/library/hh441729.aspx" target="_blank">http://msdn.microsoft.com/en-us/library/hh441729.aspx</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Default culture', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="bingCulture">
										<option value="automatic" <?= $this->selected($settings['bingCulture'], 'automatic') ?>><?= esc_html__('Automatic (use the WordPress language setting)', 'mmp') ?></option>
										<option value="af" <?= $this->selected($settings['bingCulture'], 'af') ?>><?= esc_html__('Afrikaans', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: af)</option>
										<option value="am" <?= $this->selected($settings['bingCulture'], 'am') ?>><?= esc_html__('Amharic', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: am)</option>
										<option value="ar-sa" <?= $this->selected($settings['bingCulture'], 'ar-sa') ?>><?= esc_html__('Arabic (Saudi Arabia)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ar-sa)</option>
										<option value="as" <?= $this->selected($settings['bingCulture'], 'as') ?>><?= esc_html__('Assamese', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: as)</option>
										<option value="az-Latn" <?= $this->selected($settings['bingCulture'], 'az-Latn') ?>><?= esc_html__('Azerbaijani (Latin)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: az-Latn)</option>
										<option value="be" <?= $this->selected($settings['bingCulture'], 'be') ?>><?= esc_html__('Belarusian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: be)</option>
										<option value="bg" <?= $this->selected($settings['bingCulture'], 'bg') ?>><?= esc_html__('Bulgarian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: bg)</option>
										<option value="bn-BD" <?= $this->selected($settings['bingCulture'], 'bn-BD') ?>><?= esc_html__('Bangla (Bangladesh)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: bn-BD)</option>
										<option value="bn-IN" <?= $this->selected($settings['bingCulture'], 'bn-IN') ?>><?= esc_html__('Bangla (India)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: bn-IN)</option>
										<option value="bs" <?= $this->selected($settings['bingCulture'], 'bs') ?>><?= esc_html__('Bosnian (Latin)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: bs)</option>
										<option value="ca" <?= $this->selected($settings['bingCulture'], 'ca') ?>><?= esc_html__('Catalan Spanish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ca)</option>
										<option value="ca-ES-valencia" <?= $this->selected($settings['bingCulture'], 'ca-ES-valencia') ?>><?= esc_html__('Valencian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ca-ES-valencia)</option>
										<option value="cs" <?= $this->selected($settings['bingCulture'], 'cs') ?>><?= esc_html__('Czech', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: cs)</option>
										<option value="cy" <?= $this->selected($settings['bingCulture'], 'cy') ?>><?= esc_html__('Welsh', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: cy)</option>
										<option value="da" <?= $this->selected($settings['bingCulture'], 'da') ?>><?= esc_html__('Danish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: da)</option>
										<option value="de" <?= $this->selected($settings['bingCulture'], 'de') ?>><?= esc_html__('German (Germany)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: de)</option>
										<option value="de-de" <?= $this->selected($settings['bingCulture'], 'de-de') ?>><?= esc_html__('German (Germany)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: de-de)</option>
										<option value="el" <?= $this->selected($settings['bingCulture'], 'el') ?>><?= esc_html__('Greek', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: el)</option>
										<option value="en-GB" <?= $this->selected($settings['bingCulture'], 'en-GB') ?>><?= esc_html__('English (United Kingdom)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: en-GB)</option>
										<option value="en-US" <?= $this->selected($settings['bingCulture'], 'en-US') ?>><?= esc_html__('English (United States)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: en-US)</option>
										<option value="es" <?= $this->selected($settings['bingCulture'], 'es') ?>><?= esc_html__('Spanish (Spain)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: es)</option>
										<option value="es-ES" <?= $this->selected($settings['bingCulture'], 'es-ES') ?>><?= esc_html__('Spanish (Spain)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: es-ES)</option>
										<option value="es-US" <?= $this->selected($settings['bingCulture'], 'es-US') ?>><?= esc_html__('Spanish (United States)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: es-US)</option>
										<option value="es-MX" <?= $this->selected($settings['bingCulture'], 'es-MX') ?>><?= esc_html__('Spanish (Mexico)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: es-MX)</option>
										<option value="et" <?= $this->selected($settings['bingCulture'], 'et') ?>><?= esc_html__('Estonian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: et)</option>
										<option value="eu" <?= $this->selected($settings['bingCulture'], 'eu') ?>><?= esc_html__('Basque', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: eu)</option>
										<option value="fa" <?= $this->selected($settings['bingCulture'], 'fa') ?>><?= esc_html__('Persian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fa)</option>
										<option value="fi" <?= $this->selected($settings['bingCulture'], 'fi') ?>><?= esc_html__('Finnish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fi)</option>
										<option value="fil-Latn" <?= $this->selected($settings['bingCulture'], 'fil-Latn') ?>><?= esc_html__('Filipino', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fil-Latn)</option>
										<option value="fr" <?= $this->selected($settings['bingCulture'], 'fr') ?>><?= esc_html__('French (France)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fr)</option>
										<option value="fr-FR" <?= $this->selected($settings['bingCulture'], 'fr-FR') ?>><?= esc_html__('French (France)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fr-FR)</option>
										<option value="fr-CA" <?= $this->selected($settings['bingCulture'], 'fr-CA') ?>><?= esc_html__('French (Canada)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: fr-CA)</option>
										<option value="ga" <?= $this->selected($settings['bingCulture'], 'ga') ?>><?= esc_html__('Irish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ga)</option>
										<option value="gd-Latn" <?= $this->selected($settings['bingCulture'], 'gd-Latn') ?>><?= esc_html__('Scottish Gaelic', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: gd-Latn)</option>
										<option value="gl" <?= $this->selected($settings['bingCulture'], 'gl') ?>><?= esc_html__('Galician', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: gl)</option>
										<option value="gu" <?= $this->selected($settings['bingCulture'], 'gu') ?>><?= esc_html__('Gujarati', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: gu)</option>
										<option value="ha-Latn" <?= $this->selected($settings['bingCulture'], 'ha-Latn') ?>><?= esc_html__('Hausa (Latin)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ha-Latn)</option>
										<option value="he" <?= $this->selected($settings['bingCulture'], 'he') ?>><?= esc_html__('Hebrew', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: he)</option>
										<option value="hi" <?= $this->selected($settings['bingCulture'], 'hi') ?>><?= esc_html__('Hindi', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: hi)</option>
										<option value="hr" <?= $this->selected($settings['bingCulture'], 'hr') ?>><?= esc_html__('Croatian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: hr)</option>
										<option value="hu" <?= $this->selected($settings['bingCulture'], 'hu') ?>><?= esc_html__('Hungarian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: hu)</option>
										<option value="hy" <?= $this->selected($settings['bingCulture'], 'hy') ?>><?= esc_html__('Armenian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: hy)</option>
										<option value="id" <?= $this->selected($settings['bingCulture'], 'id') ?>><?= esc_html__('Indonesian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: id)</option>
										<option value="ig-Latn" <?= $this->selected($settings['bingCulture'], 'ig-Latn') ?>><?= esc_html__('Igbo', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ig-Latn)</option>
										<option value="is" <?= $this->selected($settings['bingCulture'], 'is') ?>><?= esc_html__('Icelandic', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: )</option>
										<option value="it" <?= $this->selected($settings['bingCulture'], 'it') ?>><?= esc_html__('Italian (Italy)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: it)</option>
										<option value="it-it" <?= $this->selected($settings['bingCulture'], 'it-it') ?>><?= esc_html__('Italian (Italy)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: it-it)</option>
										<option value="ja" <?= $this->selected($settings['bingCulture'], 'ja') ?>><?= esc_html__('Japanese', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ja)</option>
										<option value="ka" <?= $this->selected($settings['bingCulture'], 'ka') ?>><?= esc_html__('Georgian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ka)</option>
										<option value="kk" <?= $this->selected($settings['bingCulture'], 'kk') ?>><?= esc_html__('Kazakh', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: kk)</option>
										<option value="km" <?= $this->selected($settings['bingCulture'], 'km') ?>><?= esc_html__('Khmer', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: km)</option>
										<option value="kn" <?= $this->selected($settings['bingCulture'], 'kn') ?>><?= esc_html__('Kannada', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: kn)</option>
										<option value="ko" <?= $this->selected($settings['bingCulture'], 'ko') ?>><?= esc_html__('Korean', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ko)</option>
										<option value="kok" <?= $this->selected($settings['bingCulture'], 'kok') ?>><?= esc_html__('Konkani', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: kok)</option>
										<option value="ku-Arab" <?= $this->selected($settings['bingCulture'], 'ku-Arab') ?>><?= esc_html__('Central Curdish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ku-Arab)</option>
										<option value="ky-Cyrl" <?= $this->selected($settings['bingCulture'], 'ky-Cyrl') ?>><?= esc_html__('Kyrgyz', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ky-Cyrl)</option>
										<option value="lb" <?= $this->selected($settings['bingCulture'], 'lb') ?>><?= esc_html__('Luxembourgish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: lb)</option>
										<option value="lt" <?= $this->selected($settings['bingCulture'], 'lt') ?>><?= esc_html__('Lithuanian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: lt)</option>
										<option value="lv" <?= $this->selected($settings['bingCulture'], 'lv') ?>><?= esc_html__('Latvian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: lv)</option>
										<option value="mi-Latn" <?= $this->selected($settings['bingCulture'], 'mi-Latn') ?>><?= esc_html__('Maori', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: mi-Latn)</option>
										<option value="mk" <?= $this->selected($settings['bingCulture'], 'mk') ?>><?= esc_html__('Macedonian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: mk)</option>
										<option value="ml" <?= $this->selected($settings['bingCulture'], 'ml') ?>><?= esc_html__('Malayalam', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ml)</option>
										<option value="mn-Cyrl" <?= $this->selected($settings['bingCulture'], 'mn-Cyrl') ?>><?= esc_html__('Mongolian (Cyrillic)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: mn-Cyrl)</option>
										<option value="mr" <?= $this->selected($settings['bingCulture'], 'mr') ?>><?= esc_html__('Marathi', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: mr)</option>
										<option value="ms" <?= $this->selected($settings['bingCulture'], 'ms') ?>><?= esc_html__('Malay (Malaysia)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ms)</option>
										<option value="mt" <?= $this->selected($settings['bingCulture'], 'mt') ?>><?= esc_html__('Maltese', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: mt)</option>
										<option value="nb" <?= $this->selected($settings['bingCulture'], 'nb') ?>><?= esc_html__('Norwegian (Bokmål)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: nb)</option>
										<option value="ne" <?= $this->selected($settings['bingCulture'], 'ne') ?>><?= esc_html__('Nepali (Nepal)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ne)</option>
										<option value="nl" <?= $this->selected($settings['bingCulture'], 'nl') ?>><?= esc_html__('Dutch (Netherlands)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: nl)</option>
										<option value="nl-BE" <?= $this->selected($settings['bingCulture'], 'nl-BE') ?>><?= esc_html__('Dutch (Netherlands)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: nl-BE)</option>
										<option value="nn" <?= $this->selected($settings['bingCulture'], 'nn') ?>><?= esc_html__('Norwegian (Nynorsk)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: nn)</option>
										<option value="nso" <?= $this->selected($settings['bingCulture'], 'nso') ?>><?= esc_html__('Sesotho sa Leboa', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: nso)</option>
										<option value="or" <?= $this->selected($settings['bingCulture'], 'or') ?>><?= esc_html__('Odia', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: or)</option>
										<option value="pa" <?= $this->selected($settings['bingCulture'], 'pa') ?>><?= esc_html__('Punjabi (Gurmukhi)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: pa)</option>
										<option value="pa-Arab" <?= $this->selected($settings['bingCulture'], 'pa-Arab') ?>><?= esc_html__('Punjabi (Arabic)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: pa-Arab)</option>
										<option value="pl" <?= $this->selected($settings['bingCulture'], 'pl') ?>><?= esc_html__('Polish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: pl)</option>
										<option value="prs-Arab" <?= $this->selected($settings['bingCulture'], 'prs-Arab') ?>><?= esc_html__('Dari', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: prs-Arab)</option>
										<option value="pt-BR" <?= $this->selected($settings['bingCulture'], 'pt-BR') ?>><?= esc_html__('Portuguese (Brazil)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: pt-BR)</option>
										<option value="pt-PT" <?= $this->selected($settings['bingCulture'], 'pt-PT') ?>><?= esc_html__('Portuguese (Portugal)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: pt-PT)</option>
										<option value="qut-Latn" <?= $this->selected($settings['bingCulture'], 'qut-Latn') ?>><?= esc_html__("K'iche'", 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: qut-Latn)</option>
										<option value="quz" <?= $this->selected($settings['bingCulture'], 'quz') ?>><?= esc_html__('Quechua (Peru)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: quz)</option>
										<option value="ro" <?= $this->selected($settings['bingCulture'], 'ro') ?>><?= esc_html__('Romanian (Romania)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ro)</option>
										<option value="ru" <?= $this->selected($settings['bingCulture'], 'ru') ?>><?= esc_html__('Russian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ru)</option>
										<option value="rw" <?= $this->selected($settings['bingCulture'], 'rw') ?>><?= esc_html__('Kinyarwanda', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: rw)</option>
										<option value="sd-Arab" <?= $this->selected($settings['bingCulture'], 'sd-Arab') ?>><?= esc_html__('Sindhi (Arabic)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sd-Arab)</option>
										<option value="si" <?= $this->selected($settings['bingCulture'], 'si') ?>><?= esc_html__('Sinhala', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: si)</option>
										<option value="sk" <?= $this->selected($settings['bingCulture'], 'sk') ?>><?= esc_html__('Slovak', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sk)</option>
										<option value="sl" <?= $this->selected($settings['bingCulture'], 'sl') ?>><?= esc_html__('Slovenian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sl)</option>
										<option value="sq" <?= $this->selected($settings['bingCulture'], 'sq') ?>><?= esc_html__('Albanian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sq)</option>
										<option value="sr-Cyrl-BA" <?= $this->selected($settings['bingCulture'], 'sr-Cyrl-BA') ?>><?= esc_html__('Serbian (Cyrillic, Bosnia and Herzegovina)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sr-Cyrl-BA)</option>
										<option value="sr-Cyrl-RS" <?= $this->selected($settings['bingCulture'], 'sr-Cyrl-RS') ?>><?= esc_html__('Serbian (Cyrillic, Serbia)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sr-Cyrl-RS)</option>
										<option value="sr-Latn-RS" <?= $this->selected($settings['bingCulture'], 'sr-Latn-RS') ?>><?= esc_html__('Serbian (Latin, Serbia)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sr-Latn-RS)</option>
										<option value="sv" <?= $this->selected($settings['bingCulture'], 'sv') ?>><?= esc_html__('Swedish (Sweden)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sv)</option>
										<option value="sw" <?= $this->selected($settings['bingCulture'], 'sw') ?>><?= esc_html__('Kiswahili', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: sw)</option>
										<option value="ta" <?= $this->selected($settings['bingCulture'], 'ta') ?>><?= esc_html__('Tamil', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ta)</option>
										<option value="te" <?= $this->selected($settings['bingCulture'], 'te') ?>><?= esc_html__('Telugu', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: te)</option>
										<option value="tg-Cyrl" <?= $this->selected($settings['bingCulture'], 'tg-Cyrl') ?>><?= esc_html__('Tajik (Cyrillic)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: tg-Cyrl)</option>
										<option value="th" <?= $this->selected($settings['bingCulture'], 'th') ?>><?= esc_html__('Thai', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: th)</option>
										<option value="ti" <?= $this->selected($settings['bingCulture'], 'ti') ?>><?= esc_html__('Tigrinya', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ti)</option>
										<option value="tk-Latn" <?= $this->selected($settings['bingCulture'], 'tk-Latn') ?>><?= esc_html__('Turkmen (Latin)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: tk-Latn)</option>
										<option value="tn" <?= $this->selected($settings['bingCulture'], 'tn') ?>><?= esc_html__('Setswana', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: tn)</option>
										<option value="tr" <?= $this->selected($settings['bingCulture'], 'tr') ?>><?= esc_html__('Turkish', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: tr)</option>
										<option value="tt-Cyrl" <?= $this->selected($settings['bingCulture'], 'tt-Cyrl') ?>><?= esc_html__('Tatar (Cyrillic)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: tt-Cyrl)</option>
										<option value="ug-Arab" <?= $this->selected($settings['bingCulture'], 'ug-Arab') ?>><?= esc_html__('Uyghur', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ug-Arab)</option>
										<option value="uk" <?= $this->selected($settings['bingCulture'], 'uk') ?>><?= esc_html__('Ukrainian', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: uk)</option>
										<option value="ur" <?= $this->selected($settings['bingCulture'], 'ur') ?>><?= esc_html__('Urdu', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: ur)</option>
										<option value="uz-Latn" <?= $this->selected($settings['bingCulture'], 'uz-Latn') ?>><?= esc_html__('Uzbek (Latin)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: uz-Latn)</option>
										<option value="vi" <?= $this->selected($settings['bingCulture'], 'vi') ?>><?= esc_html__('Vietnamese', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: vi)</option>
										<option value="wo" <?= $this->selected($settings['bingCulture'], 'wo') ?>><?= esc_html__('Wolof', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: wo)</option>
										<option value="xh" <?= $this->selected($settings['bingCulture'], 'xh') ?>><?= esc_html__('isiXhosa', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: xh)</option>
										<option value="yo-Latn" <?= $this->selected($settings['bingCulture'], 'yo-Latn') ?>><?= esc_html__('Yoruba', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: yo-Latn)</option>
										<option value="zh-Hans" <?= $this->selected($settings['bingCulture'], 'zh-Hans') ?>><?= esc_html__('Chinese (Simplified)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: zh-Hans)</option>
										<option value="zh-Hant" <?= $this->selected($settings['bingCulture'], 'zh-Hant') ?>><?= esc_html__('Chinese (Traditional)', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: zh-Hant)</option>
										<option value="zu" <?= $this->selected($settings['bingCulture'], 'zu') ?>><?= esc_html__('isiZulu', 'mmp') ?> (<?= esc_html__('culture code', 'mmp') ?>: zu)</option>
									</select>
								</div>
							</div>
						</div>
						<div id="layers_here_tab" class="mmp-settings-tab">
							<h2>HERE Maps</h2>
							<p>
								<a href="https://www.mapsmarker.com/here-maps/" target="_blank"><img src="<?= plugins_url('images/options/here-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('If you want to use HERE Maps, you have to register a personal HERE account. For a tutorial, terms of services, pricing, usage limits and more, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/here-maps/" target="_blank">https://www.mapsmarker.com/here-maps/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('HERE Maps API key', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="hereApiKey" value="<?= $settings['hereApiKey'] ?>" /></div>
							</div>
							<h3><?= esc_html__('Legacy authentication', 'mmp') ?></h3>
							<p>
								<?= esc_html__('Leave the API key empty if you want to authenticate using App ID and App Code.', 'mmp') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('App ID', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="hereAppId" value="<?= $settings['hereAppId'] ?>" /></div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('App Code', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="hereAppCode" value="<?= $settings['hereAppCode'] ?>" /></div>
							</div>
						</div>
						<div id="layers_tom_tab" class="mmp-settings-tab">
							<h2>TomTom</h2>
							<p>
								<a href="https://www.mapsmarker.com/tomtom/" target="_blank"><img src="<?= plugins_url('images/options/tomtom-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('If you want to use TomTom Maps, you have to register a personal TomTom account. For a tutorial, terms of services, pricing, usage limits and more, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/tomtom/" target="_blank">https://www.mapsmarker.com/tomtom/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('TomTom API key', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="tomApiKey" value="<?= $settings['tomApiKey'] ?>" /></div>
							</div>
						</div>
						<div id="layers_lima_tab" class="mmp-settings-tab">
							<h2>Lima Labs</h2>
							<p>
								<a href="https://www.mapsmarker.com/limalabs/" target="_blank"><img src="<?= plugins_url('images/options/limalabs-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('If you want to use Lima Labs Maps, you have to register a personal Lima Labs account. For a tutorial, terms of services, pricing, usage limits and more, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/limalabs/" target="_blank">https://www.mapsmarker.com/limalabs/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Lima Labs API key', 'mmp') ?></div>
								<div class="mmp-settings-input"><input type="text" name="limaApiKey" value="<?= $settings['limaApiKey'] ?>" /></div>
							</div>
						</div>
						<div id="layers_custom_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Custom layers', 'mmp') ?></h2>
							<p>
								<?= sprintf(esc_html__('For a community-curated list of custom layers and WMS services, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/custom-layers/" target="_blank">https://www.mapsmarker.com/custom-layers/</a>') ?>
							</p>
							<h3><?= esc_html__('Basemaps', 'mmp') ?></h3>
							<div id="custom-basemaps"></div>
							<h3><?= esc_html__('Overlays', 'mmp') ?></h3>
							<div id="custom-overlays"></div>
							<button type="button" id="mmp-custom-layer-add" class="button button-secondary"><?= esc_html__('Add new layer', 'mmp') ?></button>
						</div>
						<div id="geocoding_provider_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Geocoding provider', 'mmp') ?></h2>
							<p>
								<?= esc_html__("Geocoding is the process of transforming a description of a location - like an address, name or place - to a location on the earth's surface.", 'mmp') ?><br />
								<?= esc_html__('You can choose from different geocoding providers, which enables you to get the best results according to your needs.', 'mmp') ?><br />
								<?= sprintf(esc_html__('For a comparison of supported geocoding providers, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/geocoding/" target="_blank">https://www.mapsmarker.com/geocoding/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Geocoding provider', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="geocodingProvider" value="none" <?= $this->checked($settings['geocodingProvider'], 'none') ?> /> <?= esc_html__('None (geocoding disabled)', 'mmp') ?></label></li>
										<li><label><input type="radio" name="geocodingProvider" value="locationiq" <?= $this->checked($settings['geocodingProvider'], 'locationiq') ?> /> LocationIQ (<a href="https://www.mapsmarker.com/locationiq-geocoding/" target="_blank"><?= esc_html__('API key required', 'mmp') ?></a>)</label></li>
										<li><label><input type="radio" name="geocodingProvider" value="mapquest" <?= $this->checked($settings['geocodingProvider'], 'mapquest') ?> /> MapQuest (<a href="https://www.mapsmarker.com/mapquest-geocoding/" target="_blank"><?= esc_html__('API key required', 'mmp') ?></a>)</label></li>
										<li><label><input type="radio" name="geocodingProvider" value="google" <?= $this->checked($settings['geocodingProvider'], 'google') ?> /> Google (<a href="https://www.mapsmarker.com/google-geocoding/" target="_blank"><?= esc_html__('API key required', 'mmp') ?>)</a></label></li>
										<li><label><input type="radio" name="geocodingProvider" value="tomtom" <?= $this->checked($settings['geocodingProvider'], 'tomtom') ?> /> TomTom (<a href="https://www.mapsmarker.com/tomtom-geocoding/" target="_blank"><?= esc_html__('API key required', 'mmp') ?>)</a></label></li>
									</ul>
								</div>
							</div>
							<h3><?= esc_html__('Rate limit savings and performance', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Typing interval delay', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="number" name="geocodingTypingDelay" placeholder="400" value="<?= $settings['geocodingTypingDelay'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Delay in milliseconds between character inputs before a request to the geocoding provider is sent.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Typeahead suggestions character limit', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="number" name="geocodingMinChars" placeholder="3" value="<?= $settings['geocodingMinChars'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Minimum amount of characters that need to be typed before a request to the geocoding provider is sent.', 'mmp') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="geocoding_locationiq_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('LocationIQ', 'mmp') ?></h2>
							<p>
								<a href="https://www.mapsmarker.com/locationiq-geocoding/" target="_blank"><img src="<?= plugins_url('images/options/locationiq-logo.png', MMP::$path) ?>" /></a>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('LocationIQ Geocoding API key', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqApiKey" value="<?= $settings['geocodingLocationIqApiKey'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('For a tutorial on how to get your free LocationIQ Geocoding API key, please visit %1$s.', 'mmp'),'<a href="https://www.mapsmarker.com/locationiq-geocoding/" target="_blank">https://www.mapsmarker.com/locationiq-geocoding/</a>') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Location biasing', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Geocoding bounds', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="geocodingLocationIqBounds" value="enabled" <?= $this->checked($settings['geocodingLocationIqBounds'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="geocodingLocationIqBounds" value="disabled" <?= $this->checked($settings['geocodingLocationIqBounds'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('When using batch geocoding or when ambiguous results are returned, any results within the provided bounding box will be moved to the top of the results list. Below you will find an example for Vienna/Austria:', 'mmp') ?><br />
										<img src="<?= plugins_url('images/options/bounds-example.jpg', MMP::$path) ?>" />
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Latitude', 'mmp') ?> 1</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqBoundsLat1" placeholder="48.326583" value="<?= $settings['geocodingLocationIqBoundsLat1'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Longitude', 'mmp') ?> 1</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqBoundsLon1" placeholder="16.55056" value="<?= $settings['geocodingLocationIqBoundsLon1'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Latitude', 'mmp') ?> 2</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqBoundsLat2" placeholder="48.114308" value="<?= $settings['geocodingLocationIqBoundsLat2'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Longitude', 'mmp') ?> 2</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqBoundsLon2" placeholder="16.187325" value="<?= $settings['geocodingLocationIqBoundsLon2'] ?>" />
								</div>
							</div>
							<h3><?= esc_html__('Advanced', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Language', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqLanguage" value="<?= $settings['geocodingLocationIqLanguage'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Changes the language of the results. You can pass a two-letter country code (%1$s).', 'mmp'), '<a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">ISO 639-1</a>') ?><br />
										<?= esc_html__('If empty, the language set in WordPress will be used.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Countries', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingLocationIqCountries" value="<?= $settings['geocodingLocationIqCountries'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Changes the countries to search in. You can pass a comma-separated list of two-letter country codes (%1$s).', 'mmp'), '<a href="https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank">ISO 3166-1 alpha-2</a>') ?><br />
										<?= esc_html__('If empty, the entire planet will be searched.', 'mmp') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="geocoding_mapquest_tab" class="mmp-settings-tab">
							<h2>MapQuest</h2>
							<p>
								<a href="https://www.mapsmarker.com/mapquest-geocoding/" target="_blank"><img src="<?= plugins_url('images/options/mapquest-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('MapQuest Geocoding API allows up to %1$s transactions/month and a maximum of %2$s requests/second with a free API key. Higher quotas are available on demand - %3$s.', 'mmp'), '15.000', '10', '<a href="https://developer.mapquest.com/plans" target="_blank">' . esc_html__('click here for more details', 'mmp') . '</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('MapQuest Geocoding API key', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingMapQuestApiKey" value="<?= $settings['geocodingMapQuestApiKey'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('For a tutorial on how to get your free MapQuest Geocoding API key, please visit %1$s.', 'mmp'),'<a href="https://www.mapsmarker.com/mapquest-geocoding/" target="_blank">https://www.mapsmarker.com/mapquest-geocoding/</a>') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Location biasing', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Geocoding bounds', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="geocodingMapQuestBounds" value="enabled" <?= $this->checked($settings['geocodingMapQuestBounds'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="geocodingMapQuestBounds" value="disabled" <?= $this->checked($settings['geocodingMapQuestBounds'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('When using batch geocoding or when ambiguous results are returned, any results within the provided bounding box will be moved to the top of the results list. Below you will find an example for Vienna/Austria:', 'mmp') ?><br />
										<img src="<?= plugins_url('images/options/bounds-example.jpg', MMP::$path) ?>" />
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Latitude', 'mmp') ?> 1</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingMapQuestBoundsLat1" placeholder="48.326583" value="<?= $settings['geocodingMapQuestBoundsLat1'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Longitude', 'mmp') ?> 1</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingMapQuestBoundsLon1" placeholder="16.55056" value="<?= $settings['geocodingMapQuestBoundsLon1'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Latitude', 'mmp') ?> 2</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingMapQuestBoundsLat2" placeholder="48.114308" value="<?= $settings['geocodingMapQuestBoundsLat2'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Longitude', 'mmp') ?> 2</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingMapQuestBoundsLon2" placeholder="16.187325" value="<?= $settings['geocodingMapQuestBoundsLon2'] ?>" />
								</div>
							</div>
						</div>
						<div id="geocoding_google_tab" class="mmp-settings-tab">
							<h2>Google</h2>
							<p>
								<?= sprintf(esc_html__('For terms of services, pricing, usage limits and a tutorial on how to register a Google Geocoding API key, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/google-geocoding/" target="_blank">https://www.mapsmarker.com/google-geocoding/</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Authentication method', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="geocodingGoogleAuthMethod" value="api-key" <?= $this->checked($settings['geocodingGoogleAuthMethod'], 'api-key') ?> /> server key</label></li>
										<li><label><input type="radio" name="geocodingGoogleAuthMethod" value="clientid-signature" <?= $this->checked($settings['geocodingGoogleAuthMethod'], 'clientid-signature') ?> /> client ID + signature (<?= esc_html__('Google Maps APIs Premium Plan customers only', 'mmp') ?>)</label></li>
									</ul>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">server key</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleApiKey" placeholder="" value="<?= $settings['geocodingGoogleApiKey'] ?>" /><br />
								</div>
							</div>
							<h3><?= esc_html__('Authentication for Google Maps APIs Premium Plan customers', 'mmp') ?></h3>
							<p>
								<?= sprintf(esc_html__('For terms of services, pricing, usage limits and more please visit %1$s.', 'mmp'), '<a href="https://developers.google.com/maps/premium/overview" target="_blank">https://developers.google.com/maps/premium/overview</a>') ?>
							</p>
							<p>
								<?= sprintf(esc_html__('If you are a Google Maps APIs Premium Plan customer, please change the authentication method above to "%1$s" and fill in the credentials below, which you received in the welcome email from Google.', 'mmp'), 'client ID + signature') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">client ID (<?= esc_html__('required', 'mmp') ?>)</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleClient" value="<?= $settings['geocodingGoogleClient'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">signature (<?= esc_html__('required', 'mmp') ?>)</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleSignature" value="<?= $settings['geocodingGoogleSignature'] ?>" />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">channel (<?= esc_html__('optional', 'mmp') ?>)</div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleChannel" value="<?= $settings['geocodingGoogleChannel'] ?>" />
								</div>
							</div>
							<h3><?= esc_html__('Location biasing', 'mmp') ?></h3>
							<p>
								<?= esc_html__('You may bias results to a specified circle by passing a location and a radius parameter. This instructs the Place Autocomplete service to prefer showing results within that circle. Results outside of the defined area may still be displayed. You can use the components parameter to filter results to show only those places within a specified country.', 'mmp') ?> <?= esc_html__('If you would prefer to have no location bias, set the location to 0,0 and radius to 20000000 (20 thousand kilometers), to encompass the entire world.', 'mmp') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Location', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleLocation" placeholder="0,0" value="<?= $settings['geocodingGoogleLocation'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('The point around which you wish to retrieve place information. Must be specified as latitude,longitude (e.g. %1$s).', 'mmp'), '48.216038,16.378984') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Radius', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleRadius" placeholder="20000000" value="<?= $settings['geocodingGoogleRadius'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('The distance (in meters) within which to return place results. Note that setting a radius biases results to the indicated area, but may not fully restrict results to the specified area.', 'mmp') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Advanced', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Language', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleLanguage" value="<?= $settings['geocodingGoogleLanguage'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('The language in which to return results. For a list of supported languages, please visit %1$s.', 'mmp'), '<a href="https://developers.google.com/maps/faq#languagesupport" target="_blank">https://developers.google.com/maps/faq#languagesupport</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Region', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleRegion" value="<?= $settings['geocodingGoogleRegion'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Optional region code, specified as a ccTLD (country code top-level domain). This parameter will only influence, not fully restrict, results from the geocoder. For a list of ccTLDs, please visit %1$s.', 'mmp'), '<a href="https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains" target="_blank">https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Country_code_top-level_domains</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Components', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingGoogleComponents" placeholder="" value="<?= $settings['geocodingGoogleComponents'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Optional component filters, separated by a pipe (|). Each component filter consists of a component:value pair and will fully restrict the results from the geocoder. For more information, please visit %1$s.', 'mmp'), '<a href="https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering" target="_blank">https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering</a>') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="geocoding_tomtom_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('TomTom', 'mmp') ?></h2>
							<p>
								<a href="https://www.mapsmarker.com/tomtom-geocoding/" target="_blank"><img src="<?= plugins_url('images/options/tomtom-logo.png', MMP::$path) ?>" /></a><br />
								<?= sprintf(esc_html__('TomTom Geocoding API allows up to %1$s transactions/month and a maximum of %2$s requests/second with a free API key. Higher quotas are available on demand - %3$s.', 'mmp'), '2.500', '5', '<a href="https://developer.tomtom.com/store/maps-api" target="_blank">' . esc_html__('click here for more details', 'mmp') . '</a>') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('TomTom Geocoding API key', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomApiKey" value="<?= $settings['geocodingTomTomApiKey'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('For a tutorial on how to get your free TomTom Geocoding API key, please visit %1$s.', 'mmp'),'<a href="https://www.mapsmarker.com/tomtom-geocoding/" target="_blank">https://www.mapsmarker.com/tomtom-geocoding/</a>') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Location biasing', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Latitude', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomLat" value="<?= $settings['geocodingTomTomLat'] ?>" /><br />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Longitude', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomLon" value="<?= $settings['geocodingTomTomLon'] ?>" /><br />
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Radius', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomRadius" value="<?= $settings['geocodingTomTomRadius'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('The distance (in meters) within which to return place results. Note that setting a radius biases results to the indicated area, but may not fully restrict results to the specified area. Set to zero to disable.', 'mmp') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Advanced', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Language', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomLanguage" value="<?= $settings['geocodingTomTomLanguage'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf($l10n->kses__('Changes the language of the results. You can pass an IETF language tag (<a href="%1$s" target="_blank">supported tags</a>).', 'mmp'), 'https://developer.tomtom.com/search-api/search-api/supported-languages') ?><br />
										<?= esc_html__('If empty, the language set in WordPress will be used.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Countries', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="geocodingTomTomCountrySet" value="<?= $settings['geocodingTomTomCountrySet'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Changes the countries to search in. You can pass a comma-separated list of two-letter or three-letter country codes (%1$s).', 'mmp'), '<a href="https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank">ISO 3166-1 alpha-2/3</a>') ?><br />
										<?= esc_html__('If empty, the entire planet will be searched.', 'mmp') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="directions_provider_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Directions provider', 'mmp') ?></h2>
							<p>
								<?= esc_html__('Please select your preferred directions provider. This setting will be used for the directions link that gets attached to the popup text on each marker if enabled.', 'mmp') ?>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Use the following directions provider', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsProvider" value="googlemaps" <?= $this->checked($settings['directionsProvider'], 'googlemaps') ?> /> <?= esc_html__('Google Maps (worldwide)', 'mmp') ?> - <a href="https://maps.google.com/maps?saddr=Vienna&daddr=Linz&hl=de&sll=37.0625,-95.677068&sspn=59.986788,135.263672&geocode=FS6Z3wIdO9j5ACmfyjZRngdtRzFGW6JRiuXC_Q%3BFfwa4QIdBvzZAClNhZn6lZVzRzHEdXlXLClTfA&vpsrc=0&mra=ls&t=m&z=9&layer=t" target="_blank">Demo</a></label></li>
										<li><label><input type="radio" name="directionsProvider" value="ors" <?= $this->checked($settings['directionsProvider'], 'ors') ?> /> <?= esc_html__('openrouteservice.org (based on OpenStreetMap, Europe only)', 'mmp') ?> - <a href="https://maps.openrouteservice.org/directions?n1=48.156615&n2=16.327391&n3=13&a=48.1083,16.2725,48.2083,16.3725&b=0&c=0&k1=en-US&k2=km" target="_blank">Demo</a></label></li>
										<li><label><input type="radio" name="directionsProvider" value="bingmaps" <?= $this->checked($settings['directionsProvider'], 'bingmaps') ?> /> <?= esc_html__('Bing Maps (worldwide)', 'mmp') ?> - <a href="http://www.bing.com/maps/default.aspx?v=2&rtp=pos.48.208614_16.370541___e_~pos.48.207321_16.330513" target="_blank">Demo</a></label></li>
									</ul>
								</div>
							</div>
						</div>
						<div id="directions_google_tab" class="mmp-settings-tab">
							<h2>Google Maps</h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Map type', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsGoogleType" value="m" <?= $this->checked($settings['directionsGoogleType'], 'm') ?> /> <?= esc_html__('Map', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsGoogleType" value="k" <?= $this->checked($settings['directionsGoogleType'], 'k') ?> /> <?= esc_html__('Satellite', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsGoogleType" value="h" <?= $this->checked($settings['directionsGoogleType'], 'h') ?> /> <?= esc_html__('Hybrid', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsGoogleType" value="p" <?= $this->checked($settings['directionsGoogleType'], 'p') ?> /> <?= esc_html__('Terrain', 'mmp') ?></label></li>
									</ul>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Show traffic layer?', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsGoogleTraffic" value="1" <?= $this->checked($settings['directionsGoogleTraffic'], true) ?> /> <?= esc_html__('yes', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsGoogleTraffic" value="0" <?= $this->checked($settings['directionsGoogleTraffic'], false) ?> /> <?= esc_html__('no', 'mmp') ?></label></li>
									</ul>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Distance units', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsGoogleUnits" value="ptk" <?= $this->checked($settings['directionsGoogleUnits'], 'ptk') ?> /> <?= esc_html__('metric (km)', 'mmp') ?></label></li>
										<li><label><input type="radio" class="radio" name="directionsGoogleUnits" value="ptm" <?= $this->checked($settings['directionsGoogleUnits'], 'ptm') ?> /> <?= esc_html__('imperial (miles)', 'mmp') ?></label></li>
									</ul>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Route type', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<label><input type="checkbox" name="directionsGoogleAvoidHighways" <?= $this->checked($settings['directionsGoogleAvoidHighways']) ?> /> <?= esc_html__('Avoid highways', 'mmp') ?></label><br />
									<label><input type="checkbox" name="directionsGoogleAvoidTolls" <?= $this->checked($settings['directionsGoogleAvoidTolls']) ?> /> <?= esc_html__('Avoid tolls', 'mmp') ?></label><br />
									<label><input type="checkbox" name="directionsGooglePublicTransport" <?= $this->checked($settings['directionsGooglePublicTransport']) ?> /> <?= esc_html__('Public transport (works only in some areas)', 'mmp') ?></label><br />
									<label><input type="checkbox" name="directionsGoogleWalking" <?= $this->checked($settings['directionsGoogleWalking']) ?> /> <?= esc_html__('Walking directions', 'mmp') ?></label>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Overview map', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsGoogleOverview" value="0" <?= $this->checked($settings['directionsGoogleOverview'], false) ?> /> <?= esc_html__('hidden', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsGoogleOverview" value="1" <?= $this->checked($settings['directionsGoogleOverview'], true) ?> /> <?= esc_html__('visible', 'mmp') ?></label></li>
									</ul>
								</div>
							</div>
						</div>
						<div id="directions_ors_tab" class="mmp-settings-tab">
							<h2>openrouteservice.org</h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">routeWeigh</div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsOrsRoute" value="Recommended" <?= $this->checked($settings['directionsOrsRoute'], 'Recommended') ?>> <?= esc_html__('Recommended', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsOrsRoute" value="Shortest" <?= $this->checked($settings['directionsOrsRoute'], 'Shortest') ?> /> <?= esc_html__('Shortest', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote"><?= esc_html__('Weighting method of routing', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc">routeOpt</div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="directionsOrsType" value="Car" <?= $this->checked($settings['directionsOrsType'], 'Car') ?> /> <?= esc_html__('Car', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsOrsType" value="Bicycle" <?= $this->checked($settings['directionsOrsType'], 'Bicycle') ?> /> <?= esc_html__('Bicycle', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsOrsType" value="Pedestrian" <?= $this->checked($settings['directionsOrsType'], 'Pedestrian') ?> /> <?= esc_html__('Pedestrian', 'mmp') ?></label></li>
										<li><label><input type="radio" name="directionsOrsType" value="HeavyVehicle" <?= $this->checked($settings['directionsOrsType'], 'HeavyVehicle') ?> /> <?= esc_html__('HeavyVehicle', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote"><?= esc_html__('Preferred route profile', 'mmp') ?></span>
								</div>
							</div>
						</div>
						<div id="misc_general_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('General', 'mmp') ?></h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Beta testing', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="betaTesting" value="disabled" <?= $this->checked($settings['betaTesting'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="betaTesting" value="enabled" <?= $this->checked($settings['betaTesting'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Set to enabled if you want to easily upgrade to beta releases.', 'mmp') ?><br />
										<span class="mmp-settings-warning"><?= esc_html__('Warning: not recommended on production sites - use at your own risk!', 'mmp') ?></span>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('App icon URL', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="appIcon" value="<?= $settings['appIcon'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Will be used if a link to a fullscreen map gets added to the homescreen on mobile devices. If empty, the Maps Marker Pro logo will be used.', 'mmp') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Attribution', 'mmp') ?></h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Affiliate ID', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="affiliateId" value="<?= $settings['affiliateId'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Enter your affiliate ID to replace the default MapsMarker.com backlink on all maps with your personal affiliate link - enabling you to receive commissions up to 50% from sales of the pro version.', 'mmp') ?><br />
										<?= sprintf(esc_html__('For more info on the Maps Marker affiliate program and how to get your affiliate ID, please visit %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/affiliateid/" target="_blank">https://www.mapsmarker.com/affiliateid/</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('MapsMarker.com backlinks', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="backlinks" value="show" <?= $this->checked($settings['backlinks'], true) ?> /> <?= esc_html__('show', 'mmp') ?></label></li>
										<li><label><input type="radio" name="backlinks" value="hide" <?= $this->checked($settings['backlinks'], false) ?> /> <?= esc_html__('hide', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Option to hide backlinks to Mapsmarker.com on maps and screen overlays in KML files.', 'mmp') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="misc_icons_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Icons', 'mmp') ?></h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Icon size', 'mmp') ?> (x)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconSizeX" placeholder="32" value="<?= $settings['iconSizeX'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('Width of the icons in pixels.', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Icon size', 'mmp') ?> (y)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconSizeY" placeholder="37" value="<?= $settings['iconSizeY'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('Height of the icons in pixels.', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Icon anchor', 'mmp') ?> (x)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconAnchorX" placeholder="17" value="<?= $settings['iconAnchorX'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('The x-coordinate of the "tip" of the icons (relative to its top left corner).', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Icon anchor', 'mmp') ?> (y)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconAnchorY" placeholder="36" value="<?= $settings['iconAnchorY'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('The y-coordinate of the "tip" of the icons (relative to its top left corner).', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Popup anchor', 'mmp') ?> (x)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconPopupAnchorX" placeholder="-1" value="<?= $settings['iconPopupAnchorX'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('The x-coordinate of the popup anchor (relative to the icon anchor).', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Popup anchor', 'mmp') ?> (y)</div>
								<div class="mmp-settings-input">
									<input type="text" name="iconPopupAnchorY" placeholder="-32" value="<?= $settings['iconPopupAnchorY'] ?>" /><br />
									<span class="mmp-settings-footnote"><?= esc_html__('The y-coordinate of the popup anchor (relative to the icon anchor).', 'mmp') ?></span>
								</div>
							</div>
							<h3><?= esc_html__('Manage icons', 'mmp') ?></h3>
							<div>
								<select id="icons_list" name="icons_list[]" multiple="multiple">
									<?php foreach ($upload->get_icons() as $icon): ?>
										<option value="<?= $icon ?>"><?= $icon ?></option>
									<?php endforeach; ?>
								</select><br />
								<button type="button" id="delete_icons" class="button button-secondary mmp-button-delete"><?= esc_html__('Delete selected', 'mmp') ?></button>
							</div>
						</div>
						<div id="misc_capabilities_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Capabilities', 'mmp') ?></h2>
							<p>
								<?= esc_html__('Here you can set the backend capabilities for each user role. Administrators always have all capabilities.', 'mmp') ?>
							</p>
							<table id="user_capabilities" class="mmp-role-capabilities">
								<tr>
									<th><?= esc_html__('Role', 'mmp') ?></th>
									<th><?= esc_html__('View maps', 'mmp') ?></th>
									<th><?= esc_html__('Add maps', 'mmp') ?></th>
									<th><?= esc_html__('Edit other maps', 'mmp') ?></th>
									<th><?= esc_html__('Delete other maps', 'mmp') ?></th>
									<th><?= esc_html__('View markers', 'mmp') ?></th>
									<th><?= esc_html__('Add markers', 'mmp') ?></th>
									<th><?= esc_html__('Edit other markers', 'mmp') ?></th>
									<th><?= esc_html__('Delete other markers', 'mmp') ?></th>
									<th><?= esc_html__('Use tools', 'mmp') ?></th>
									<th><?= esc_html__('Change settings', 'mmp') ?></th>
								</tr>
								<?php foreach ($wp_roles->roles as $role => $values): ?>
									<?php if ($role === 'administrator') continue ?>
									<tr>
										<td><?= translate_user_role($values['name']) ?></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_view_maps]" <?= $this->checked((isset($values['capabilities']['mmp_view_maps']) && $values['capabilities']['mmp_view_maps'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_add_maps]" <?= $this->checked((isset($values['capabilities']['mmp_add_maps']) && $values['capabilities']['mmp_add_maps'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_edit_other_maps]" <?= $this->checked((isset($values['capabilities']['mmp_edit_other_maps']) && $values['capabilities']['mmp_edit_other_maps'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_delete_other_maps]" <?= $this->checked((isset($values['capabilities']['mmp_delete_other_maps']) && $values['capabilities']['mmp_delete_other_maps'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_view_markers]" <?= $this->checked((isset($values['capabilities']['mmp_view_markers']) && $values['capabilities']['mmp_view_markers'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_add_markers]" <?= $this->checked((isset($values['capabilities']['mmp_add_markers']) && $values['capabilities']['mmp_add_markers'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_edit_other_markers]" <?= $this->checked((isset($values['capabilities']['mmp_edit_other_markers']) && $values['capabilities']['mmp_edit_other_markers'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_delete_other_markers]" <?= $this->checked((isset($values['capabilities']['mmp_delete_other_markers']) && $values['capabilities']['mmp_delete_other_markers'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_use_tools]" <?= $this->checked((isset($values['capabilities']['mmp_use_tools']) && $values['capabilities']['mmp_use_tools'])) ?> /></td>
										<td><input type="checkbox" name="role_capabilities[<?= $role ?>][mmp_change_settings]" <?= $this->checked((isset($values['capabilities']['mmp_change_settings']) && $values['capabilities']['mmp_change_settings'])) ?> /></td>
									</tr>
								<?php endforeach; ?>
							</table>
						</div>
						<div id="misc_sitemaps_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Sitemaps', 'mmp') ?></h2>
							<p>
								<?= esc_html__('XML sitemaps help search engines like Google, Bing, Yahoo and Ask.com to better index your blog. With such a sitemap, it is much easier for the crawlers to see the complete structure of your site and retrieve it more efficiently. Geolocation information can also be added to sitemaps in order to improve your local SEO value for services like Google Places.', 'mmp') ?>
							</p>
							<p>
								<?= sprintf($l10n->kses__('Maps Marker Pro includes a <a href="%1$s" target="_blank">geo sitemap</a>. To learn how to manually register this sitemap, please visit <a href="%2$s" target="_blank">this tutorial</a>. Alternatively, you can use one of the supported plugins to automate the process.', 'mmp'), $api->link('/geo-sitemap/'), 'https://www.mapsmarker.com/geo-sitemap/') ?>
							</p>
							<h3>Google XML Sitemaps</h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Integration', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="sitemapGoogle" value="enabled" <?= $this->checked($settings['sitemapGoogle'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="sitemapGoogle" value="disabled" <?= $this->checked($settings['sitemapGoogle'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('If enabled, and the %1$s plugin is active, KML links will automatically be added to the sitemap.', 'mmp'), '<a href="https://wordpress.org/plugins/google-sitemap-generator/" target="_blank">Google XML Sitemaps</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Include specific maps', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="sitemapGoogleInclude" value="<?= $settings['sitemapGoogleInclude'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Please enter a comma-separted list of IDs (e.g. 1,2,3).', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Exclude specific maps', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="sitemapGoogleExclude" value="<?= $settings['sitemapGoogleExclude'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Please enter a comma-separted list of IDs (e.g. 1,2,3).', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Priority for maps', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="sitemapGooglePriority">
										<option value="0" <?= $this->selected($settings['sitemapGooglePriority'], '0') ?>>0</option>
										<option value="0.1" <?= $this->selected($settings['sitemapGooglePriority'], '0.1') ?>>0.1</option>
										<option value="0.2" <?= $this->selected($settings['sitemapGooglePriority'], '0.2') ?>>0.2</option>
										<option value="0.3" <?= $this->selected($settings['sitemapGooglePriority'], '0.3') ?>>0.3</option>
										<option value="0.4" <?= $this->selected($settings['sitemapGooglePriority'], '0.4') ?>>0.4</option>
										<option value="0.5" <?= $this->selected($settings['sitemapGooglePriority'], '0.5') ?>>0.5</option>
										<option value="0.6" <?= $this->selected($settings['sitemapGooglePriority'], '0.6') ?>>0.6</option>
										<option value="0.7" <?= $this->selected($settings['sitemapGooglePriority'], '0.7') ?>>0.7</option>
										<option value="0.8" <?= $this->selected($settings['sitemapGooglePriority'], '0.8') ?>>0.8</option>
										<option value="0.9" <?= $this->selected($settings['sitemapGooglePriority'], '0.9') ?>>0.9</option>
										<option value="1" <?= $this->selected($settings['sitemapGooglePriority'], '1') ?>>1</option>
									</select><br />
									<span class="mmp-settings-footnote"><?= esc_html__('The priority of maps relative to other URLs on your site.', 'mmp') ?></span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Update frequency', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="sitemapGoogleFrequency">
										<option value="always" <?= $this->selected($settings['sitemapGoogleFrequency'], 'always') ?>><?= esc_html__('Always', 'mmp') ?></option>
										<option value="hourly" <?= $this->selected($settings['sitemapGoogleFrequency'], 'hourly') ?>><?= esc_html__('Hourly', 'mmp') ?></option>
										<option value="daily" <?= $this->selected($settings['sitemapGoogleFrequency'], 'daily') ?>><?= esc_html__('Daily', 'mmp') ?></option>
										<option value="weekly" <?= $this->selected($settings['sitemapGoogleFrequency'], 'weekly') ?>><?= esc_html__('Weekly', 'mmp') ?></option>
										<option value="monthly" <?= $this->selected($settings['sitemapGoogleFrequency'], 'monthly') ?>><?= esc_html__('Monthly', 'mmp') ?></option>
										<option value="yearly" <?= $this->selected($settings['sitemapGoogleFrequency'], 'yearly') ?>><?= esc_html__('Yearly', 'mmp') ?></option>
										<option value="never" <?= $this->selected($settings['sitemapGoogleFrequency'], 'never') ?>><?= esc_html__('Never', 'mmp') ?></option>
									</select><br />
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('How frequently the maps are likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page. Additional information available at %1$s.', 'mmp'), '<a href="http://www.sitemaps.org/protocol.html" target="_blank">sitemaps.org</a>') ?>
									</span>
								</div>
							</div>
							<h3>Yoast SEO</h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Integration', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="sitemapYoast" value="enabled" <?= $this->checked($settings['sitemapYoast'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="sitemapYoast" value="disabled" <?= $this->checked($settings['sitemapYoast'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('If enabled, and the %1$s plugin is active, the geo sitemap will automatically be added to the sitemap index.', 'mmp'), '<a href="https://wordpress.org/plugins/wordpress-seo/" target="_blank">Yoast SEO</a>') ?>
									</span>
								</div>
							</div>
							<h3>Rank Math SEO</h3>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Integration', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="sitemapRankMath" value="enabled" <?= $this->checked($settings['sitemapRankMath'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="sitemapRankMath" value="disabled" <?= $this->checked($settings['sitemapRankMath'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('If enabled, and the %1$s plugin is active, the geo sitemap will automatically be added to the sitemap index.', 'mmp'), '<a href="https://www.mapsmarker.com/rankmath/" target="_blank">Rank Math SEO</a>') ?>
									</span>
								</div>
							</div>
						</div>
						<div id="misc_wordpress_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('WordPress integration', 'mmp') ?></h2>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Shortcode', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="shortcode" placeholder="mapsmarker" value="<?= $settings['shortcode'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Shortcode to add maps - Example: [mapsmarker map="1"]', 'mmp') ?><br />
										<?= esc_html__('Attention: if you change the shortcode after having embedded shortcodes into content, the shortcode on these pages has to be changed manually. Otherwise, these maps will not be show!', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('TinyMCE button', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="tinyMce" value="enabled" <?= $this->checked($settings['tinyMce'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="tinyMce" value="disabled" <?= $this->checked($settings['tinyMce'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, an "Insert map" button gets added above the TinyMCE editor on post and page edit screens for easily searching and inserting maps.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('WordPress Admin Bar integration', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="adminBar" value="enabled" <?= $this->checked($settings['adminBar'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="adminBar" value="disabled" <?= $this->checked($settings['adminBar'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, show a dropdown menu in the Wordpress Admin Bar.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('WordPress admin dashboard widget', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="dashboardWidget" value="enabled" <?= $this->checked($settings['dashboardWidget'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="dashboardWidget" value="disabled" <?= $this->checked($settings['dashboardWidget'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, shows a widget on the admin dashboard which displays latest markers and blog posts from mapsmarker.com.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Fullscreen endpoint', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="apiFullscreen" value="enabled" <?= $this->checked($settings['apiFullscreen'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="apiFullscreen" value="disabled" <?= $this->checked($settings['apiFullscreen'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Globally enables or disables the fullscreen endpoint for all maps.', 'mmp') ?><br />
										<?= sprintf(esc_html__('Example link for map with ID 1: %1$s', 'mmp'), '<a href="' . $api->link('/fullscreen/1/') . '" target="_blank">' . $api->link('/fullscreen/1/') . '</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Export endpoint', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="apiExport" value="enabled" <?= $this->checked($settings['apiExport'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="apiExport" value="disabled" <?= $this->checked($settings['apiExport'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Globally enables or disables the GeoJSON, KML and GeoRSS export endpoints for all maps.', 'mmp') ?><br />
										<?= sprintf(esc_html__('Example links for map with ID 1: %1$s, %2$s, %3$s', 'mmp'), '<a href="' . $api->link('/export/geojson/1/') . '" target="_blank">' . $api->link('/export/geojson/1/') . '</a>', '<a href="' . $api->link('/export/kml/1/') . '" target="_blank">' . $api->link('/export/kml/1/') . '</a>', '<a href="' . $api->link('/export/georss/1/') . '" target="_blank">' . $api->link('/export/georss/1/') . '</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Geo sitemap endpoint', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="apiSitemap" value="enabled" <?= $this->checked($settings['apiSitemap'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="apiSitemap" value="disabled" <?= $this->checked($settings['apiSitemap'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= sprintf(esc_html__('Globally enables or disables the geo sitemap endpoint %1$s', 'mmp'), '<a href="' . $api->link('/geo-sitemap/') . '" target="_blank">' . $api->link('/geo-sitemap/') . '</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Redirect to external GPX files', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="redirectExternalGpx" value="enabled" <?= $this->checked($settings['redirectExternalGpx'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="redirectExternalGpx" value="disabled" <?= $this->checked($settings['redirectExternalGpx'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('Redirects to external GPX files rather than processing them like internal GPX files when clicking on the download button. Enabling this will save server traffic, but can cause external GPX files to be opened in the browser instead of being downloaded.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Permalinks slug', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="permalinkSlug" placeholder="maps" value="<?= $settings['permalinkSlug'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Used to create pretty links to fullscreen maps or API endpoints.', 'mmp') ?><br />
										<?= sprintf(esc_html__('Example link to fullscreen map with ID 1: %1$s', 'mmp'), '<a href="' . $api->link('/fullscreen/1/') . '" target="_blank">' . $api->link('/fullscreen/1/') . '</a>') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Permalinks base URL', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<input type="text" name="permalinkBaseUrl" value="<?= $settings['permalinkBaseUrl'] ?>" /><br />
									<span class="mmp-settings-footnote">
										<?= esc_html__('Needed for creating pretty links to fullscreen maps or API endpoints.', 'mmp') ?><br />
										<?= esc_html__('Only set this option to the URL of your WordPress folder if you are experiencing issues or recommended so by support!', 'mmp') ?><br />
										<?= sprintf(esc_html__('If empty, "WordPress Address (URL)" - %1$s - will be used.', 'mmp'), get_site_url()) ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('HTML filter for popups', 'mmp') ?> (wp_kses)</div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="popupKses" value="enabled" <?= $this->checked($settings['popupKses'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="popupKses" value="disabled" <?= $this->checked($settings['popupKses'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, unsupported code tags are stripped from popups to prevent injection of malicious code.', 'mmp') ?><br />
										<?= esc_html__('Disabling this option allows you to display unfiltered popups and is only recommended if special HTML tags are needed.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Map lazy loading', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="lazyLoadMaps" value="enabled" <?= $this->checked($settings['lazyLoadMaps'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="lazyLoadMaps" value="disabled" <?= $this->checked($settings['lazyLoadMaps'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, maps will only be loaded after they become visible in the viewport.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Popup lazy loading', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="lazyLoadPopups" value="enabled" <?= $this->checked($settings['lazyLoadPopups'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="lazyLoadPopups" value="disabled" <?= $this->checked($settings['lazyLoadPopups'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, popups will only be loaded when needed.', 'mmp') ?>
									</span>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Gzip compression', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<ul>
										<li><label><input type="radio" name="gzipCompression" value="enabled" <?= $this->checked($settings['gzipCompression'], true) ?> /> <?= esc_html__('enabled', 'mmp') ?></label></li>
										<li><label><input type="radio" name="gzipCompression" value="disabled" <?= $this->checked($settings['gzipCompression'], false) ?> /> <?= esc_html__('disabled', 'mmp') ?></label></li>
									</ul>
									<span class="mmp-settings-footnote">
										<?= esc_html__('If enabled, map data is compressed to improve load times.', 'mmp') ?>
									</span>
								</div>
							</div>
							<h3><?= esc_html__('Interface language', 'mmp') ?></h3>
							<p>
								<?= esc_html__('The interface language to use on backend and/or on maps on frontend. Please note that the language for Google Maps and Bing maps can be set separately via the according basemap settings section.', 'mmp') ?><br />
								<?= esc_html__('If your language is missing or not fully translated yet, you are invited to help on the web-based translation plattform:', 'mmp') ?> <a href="https://translate.mapsmarker.com/" target="_blank">https://translate.mapsmarker.com/</a>
							</p>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Admin area', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="pluginLanguageAdmin">
										<option value="automatic" <?= $this->selected($settings['pluginLanguageAdmin'], 'automatic') ?>><?= esc_html__('Automatic (use WordPress default)', 'mmp') ?></option>
										<option value="ar" <?= $this->selected($settings['pluginLanguageAdmin'], 'ar') ?>><?= esc_html__('Arabic', 'mmp') ?> (ar)</option>
										<option value="af" <?= $this->selected($settings['pluginLanguageAdmin'], 'af') ?>><?= esc_html__('Afrikaans', 'mmp') ?> (af)</option>
										<option value="bn_BD" <?= $this->selected($settings['pluginLanguageAdmin'], 'bn_BD') ?>><?= esc_html__('Bengali', 'mmp') ?> (bn_BD)</option>
										<option value="bs_BA" <?= $this->selected($settings['pluginLanguageAdmin'], 'bs_BA') ?>><?= esc_html__('Bosnian', 'mmp') ?> (bs_BA)</option>
										<option value="bg_BG" <?= $this->selected($settings['pluginLanguageAdmin'], 'bg_BG') ?>><?= esc_html__('Bulgarian', 'mmp') ?> (bg_BG)</option>
										<option value="ca" <?= $this->selected($settings['pluginLanguageAdmin'], 'ca') ?>><?= esc_html__('Catalan', 'mmp') ?> (ca)</option>
										<option value="zh_CN" <?= $this->selected($settings['pluginLanguageAdmin'], 'zh_CN') ?>><?= esc_html__('Chinese', 'mmp') ?> (zh_CN)</option>
										<option value="zh_TW" <?= $this->selected($settings['pluginLanguageAdmin'], 'zh_TW') ?>><?= esc_html__('Chinese', 'mmp') ?> (zh_TW)</option>
										<option value="hr" <?= $this->selected($settings['pluginLanguageAdmin'], 'hr') ?>><?= esc_html__('Croatian', 'mmp') ?> (hr)</option>
										<option value="cs_CZ" <?= $this->selected($settings['pluginLanguageAdmin'], 'cs_CZ') ?>><?= esc_html__('Czech', 'mmp') ?> (cs_CZ)</option>
										<option value="da_DK" <?= $this->selected($settings['pluginLanguageAdmin'], 'da_DK') ?>><?= esc_html__('Danish', 'mmp') ?> (da_DK)</option>
										<option value="nl_NL" <?= $this->selected($settings['pluginLanguageAdmin'], 'nl_NL') ?>><?= esc_html__('Dutch', 'mmp') ?> (nl_NL)</option>
										<option value="en_US" <?= $this->selected($settings['pluginLanguageAdmin'], 'en_US') ?>><?= esc_html__('English', 'mmp') ?> (en_US)</option>
										<option value="fi_FI" <?= $this->selected($settings['pluginLanguageAdmin'], 'fi_FI') ?>><?= esc_html__('Finnish', 'mmp') ?> (fi_FI)</option>
										<option value="fr_FR" <?= $this->selected($settings['pluginLanguageAdmin'], 'fr_FR') ?>><?= esc_html__('French', 'mmp') ?> (fr_FR)</option>
										<option value="gl_ES" <?= $this->selected($settings['pluginLanguageAdmin'], 'gl_ES') ?>><?= esc_html__('Galician', 'mmp') ?> (gl_ES)</option>
										<option value="de_DE" <?= $this->selected($settings['pluginLanguageAdmin'], 'de_DE') ?>><?= esc_html__('German', 'mmp') ?> (de_DE)</option>
										<option value="el" <?= $this->selected($settings['pluginLanguageAdmin'], 'el') ?>><?= esc_html__('Greek', 'mmp') ?> (el)</option>
										<option value="he_IL" <?= $this->selected($settings['pluginLanguageAdmin'], 'he_IL') ?>><?= esc_html__('Hebrew', 'mmp') ?> (he_IL)</option>
										<option value="hi_IN" <?= $this->selected($settings['pluginLanguageAdmin'], 'hi_IN') ?>><?= esc_html__('Hindi', 'mmp') ?> (hi_IN)</option>
										<option value="hu_HU" <?= $this->selected($settings['pluginLanguageAdmin'], 'hu_HU') ?>><?= esc_html__('Hungarian', 'mmp') ?> (hu_HU)</option>
										<option value="id_ID" <?= $this->selected($settings['pluginLanguageAdmin'], 'id_ID') ?>><?= esc_html__('Indonesian', 'mmp') ?> (id_ID)</option>
										<option value="it_IT" <?= $this->selected($settings['pluginLanguageAdmin'], 'it_IT') ?>><?= esc_html__('Italian', 'mmp') ?> (it_IT)</option>
										<option value="ja" <?= $this->selected($settings['pluginLanguageAdmin'], 'ja') ?>><?= esc_html__('Japanese', 'mmp') ?> (ja)</option>
										<option value="ko_KR" <?= $this->selected($settings['pluginLanguageAdmin'], 'ko_KR') ?>><?= esc_html__('Korean', 'mmp') ?> (ko_KR)</option>
										<option value="lv" <?= $this->selected($settings['pluginLanguageAdmin'], 'lv') ?>><?= esc_html__('Latvian', 'mmp') ?> (lv)</option>
										<option value="lt_LT" <?= $this->selected($settings['pluginLanguageAdmin'], 'lt_LT') ?>><?= esc_html__('Lithuanian', 'mmp') ?> (lt_LT)</option>
										<option value="ms_MY" <?= $this->selected($settings['pluginLanguageAdmin'], 'ms_MY') ?>><?= esc_html__('Malay', 'mmp') ?> (ms_MY)</option>
										<option value="nb_NO" <?= $this->selected($settings['pluginLanguageAdmin'], 'nb_NO') ?>><?= esc_html__('Norwegian (Bokmål)', 'mmp') ?> (nb_NO)</option>
										<option value="pl_PL" <?= $this->selected($settings['pluginLanguageAdmin'], 'pl_PL') ?>><?= esc_html__('Polish', 'mmp') ?> (pl_PL)</option>
										<option value="pt_BR" <?= $this->selected($settings['pluginLanguageAdmin'], 'pt_BR') ?>><?= esc_html__('Portuguese', 'mmp') ?> (pt_BR)</option>
										<option value="pt_PT" <?= $this->selected($settings['pluginLanguageAdmin'], 'pt_PT') ?>><?= esc_html__('Portuguese', 'mmp') ?> (pt_PT)</option>
										<option value="ro_RO" <?= $this->selected($settings['pluginLanguageAdmin'], 'ro_RO') ?>><?= esc_html__('Romanian', 'mmp') ?> (ro_RO)</option>
										<option value="ru_RU" <?= $this->selected($settings['pluginLanguageAdmin'], 'ru_RU') ?>><?= esc_html__('Russian', 'mmp') ?> (ru_RU)</option>
										<option value="sk_SK" <?= $this->selected($settings['pluginLanguageAdmin'], 'sk_SK') ?>><?= esc_html__('Slovak', 'mmp') ?> (sk_SK)</option>
										<option value="sl_SI" <?= $this->selected($settings['pluginLanguageAdmin'], 'sl_SI') ?>><?= esc_html__('Slovenian', 'mmp') ?> (sl_SI)</option>
										<option value="sv_SE" <?= $this->selected($settings['pluginLanguageAdmin'], 'sv_SE') ?>><?= esc_html__('Swedish', 'mmp') ?> (sv_SE)</option>
										<option value="es_ES" <?= $this->selected($settings['pluginLanguageAdmin'], 'es_ES') ?>><?= esc_html__('Spanish', 'mmp') ?> (es_ES)</option>
										<option value="es_MX" <?= $this->selected($settings['pluginLanguageAdmin'], 'es_MX') ?>><?= esc_html__('Spanish', 'mmp') ?> (es_MX)</option>
										<option value="th" <?= $this->selected($settings['pluginLanguageAdmin'], 'th') ?>><?= esc_html__('Thai', 'mmp') ?> (th)</option>
										<option value="tr_TR" <?= $this->selected($settings['pluginLanguageAdmin'], 'tr_TR') ?>><?= esc_html__('Turkish', 'mmp') ?> (tr_TR)</option>
										<option value="ug" <?= $this->selected($settings['pluginLanguageAdmin'], 'ug') ?>><?= esc_html__('Uighur', 'mmp') ?> (ug)</option>
										<option value="uk_UK" <?= $this->selected($settings['pluginLanguageAdmin'], 'uk_UK') ?>><?= esc_html__('Ukrainian', 'mmp') ?> (uk_UK)</option>
										<option value="vi" <?= $this->selected($settings['pluginLanguageAdmin'], 'vi') ?>><?= esc_html__('Vietnamese', 'mmp') ?> (vi)</option>
										<option value="yi" <?= $this->selected($settings['pluginLanguageAdmin'], 'yi') ?>><?= esc_html__('Yiddish', 'mmp') ?> (yi)</option>
									</select>
								</div>
							</div>
							<div class="mmp-settings-setting">
								<div class="mmp-settings-desc"><?= esc_html__('Frontend', 'mmp') ?></div>
								<div class="mmp-settings-input">
									<select name="pluginLanguageFrontend">
										<option value="automatic" <?= $this->selected($settings['pluginLanguageFrontend'], 'automatic') ?>><?= esc_html__('Automatic (use WordPress default)', 'mmp') ?></option>
										<option value="ar" <?= $this->selected($settings['pluginLanguageFrontend'], 'ar') ?>><?= esc_html__('Arabic', 'mmp') ?> (ar)</option>
										<option value="af" <?= $this->selected($settings['pluginLanguageFrontend'], 'af') ?>><?= esc_html__('Afrikaans', 'mmp') ?> (af)</option>
										<option value="bn_BD" <?= $this->selected($settings['pluginLanguageFrontend'], 'bn_BD') ?>><?= esc_html__('Bengali', 'mmp') ?> (bn_BD)</option>
										<option value="bs_BA" <?= $this->selected($settings['pluginLanguageFrontend'], 'bs_BA') ?>><?= esc_html__('Bosnian', 'mmp') ?> (bs_BA)</option>
										<option value="bg_BG" <?= $this->selected($settings['pluginLanguageFrontend'], 'bg_BG') ?>><?= esc_html__('Bulgarian', 'mmp') ?> (bg_BG)</option>
										<option value="ca" <?= $this->selected($settings['pluginLanguageFrontend'], 'ca') ?>><?= esc_html__('Catalan', 'mmp') ?> (ca)</option>
										<option value="zh_CN" <?= $this->selected($settings['pluginLanguageFrontend'], 'zh_CN') ?>><?= esc_html__('Chinese', 'mmp') ?> (zh_CN)</option>
										<option value="zh_TW" <?= $this->selected($settings['pluginLanguageFrontend'], 'zh_TW') ?>><?= esc_html__('Chinese', 'mmp') ?> (zh_TW)</option>
										<option value="hr" <?= $this->selected($settings['pluginLanguageFrontend'], 'hr') ?>><?= esc_html__('Croatian', 'mmp') ?> (hr)</option>
										<option value="cs_CZ" <?= $this->selected($settings['pluginLanguageFrontend'], 'cs_CZ') ?>><?= esc_html__('Czech', 'mmp') ?> (cs_CZ)</option>
										<option value="da_DK" <?= $this->selected($settings['pluginLanguageFrontend'], 'da_DK') ?>><?= esc_html__('Danish', 'mmp') ?> (da_DK)</option>
										<option value="nl_NL" <?= $this->selected($settings['pluginLanguageFrontend'], 'nl_NL') ?>><?= esc_html__('Dutch', 'mmp') ?> (nl_NL)</option>
										<option value="en_US" <?= $this->selected($settings['pluginLanguageFrontend'], 'en_US') ?>><?= esc_html__('English', 'mmp') ?> (en_US)</option>
										<option value="fi_FI" <?= $this->selected($settings['pluginLanguageFrontend'], 'fi_FI') ?>><?= esc_html__('Finnish', 'mmp') ?> (fi_FI)</option>
										<option value="fr_FR" <?= $this->selected($settings['pluginLanguageFrontend'], 'fr_FR') ?>><?= esc_html__('French', 'mmp') ?> (fr_FR)</option>
										<option value="gl_ES" <?= $this->selected($settings['pluginLanguageFrontend'], 'gl_ES') ?>><?= esc_html__('Galician', 'mmp') ?> (gl_ES)</option>
										<option value="de_DE" <?= $this->selected($settings['pluginLanguageFrontend'], 'de_DE') ?>><?= esc_html__('German', 'mmp') ?> (de_DE)</option>
										<option value="el" <?= $this->selected($settings['pluginLanguageFrontend'], 'el') ?>><?= esc_html__('Greek', 'mmp') ?> (el)</option>
										<option value="he_IL" <?= $this->selected($settings['pluginLanguageFrontend'], 'he_IL') ?>><?= esc_html__('Hebrew', 'mmp') ?> (he_IL)</option>
										<option value="hi_IN" <?= $this->selected($settings['pluginLanguageFrontend'], 'hi_IN') ?>><?= esc_html__('Hindi', 'mmp') ?> (hi_IN)</option>
										<option value="hu_HU" <?= $this->selected($settings['pluginLanguageFrontend'], 'hu_HU') ?>><?= esc_html__('Hungarian', 'mmp') ?> (hu_HU)</option>
										<option value="id_ID" <?= $this->selected($settings['pluginLanguageFrontend'], 'id_ID') ?>><?= esc_html__('Indonesian', 'mmp') ?> (id_ID)</option>
										<option value="it_IT" <?= $this->selected($settings['pluginLanguageFrontend'], 'it_IT') ?>><?= esc_html__('Italian', 'mmp') ?> (it_IT)</option>
										<option value="ja" <?= $this->selected($settings['pluginLanguageFrontend'], 'ja') ?>><?= esc_html__('Japanese', 'mmp') ?> (ja)</option>
										<option value="ko_KR" <?= $this->selected($settings['pluginLanguageFrontend'], 'ko_KR') ?>><?= esc_html__('Korean', 'mmp') ?> (ko_KR)</option>
										<option value="lv" <?= $this->selected($settings['pluginLanguageFrontend'], 'lv') ?>><?= esc_html__('Latvian', 'mmp') ?> (lv)</option>
										<option value="lt_LT" <?= $this->selected($settings['pluginLanguageFrontend'], 'lt_LT') ?>><?= esc_html__('Lithuanian', 'mmp') ?> (lt_LT)</option>
										<option value="ms_MY" <?= $this->selected($settings['pluginLanguageFrontend'], 'ms_MY') ?>><?= esc_html__('Malay', 'mmp') ?> (ms_MY)</option>
										<option value="nb_NO" <?= $this->selected($settings['pluginLanguageFrontend'], 'nb_NO') ?>><?= esc_html__('Norwegian (Bokmål)', 'mmp') ?> (nb_NO)</option>
										<option value="pl_PL" <?= $this->selected($settings['pluginLanguageFrontend'], 'pl_PL') ?>><?= esc_html__('Polish', 'mmp') ?> (pl_PL)</option>
										<option value="pt_BR" <?= $this->selected($settings['pluginLanguageFrontend'], 'pt_BR') ?>><?= esc_html__('Portuguese', 'mmp') ?> (pt_BR)</option>
										<option value="pt_PT" <?= $this->selected($settings['pluginLanguageFrontend'], 'pt_PT') ?>><?= esc_html__('Portuguese', 'mmp') ?> (pt_PT)</option>
										<option value="ro_RO" <?= $this->selected($settings['pluginLanguageFrontend'], 'ro_RO') ?>><?= esc_html__('Romanian', 'mmp') ?> (ro_RO)</option>
										<option value="ru_RU" <?= $this->selected($settings['pluginLanguageFrontend'], 'ru_RU') ?>><?= esc_html__('Russian', 'mmp') ?> (ru_RU)</option>
										<option value="sk_SK" <?= $this->selected($settings['pluginLanguageFrontend'], 'sk_SK') ?>><?= esc_html__('Slovak', 'mmp') ?> (sk_SK)</option>
										<option value="sl_SI" <?= $this->selected($settings['pluginLanguageFrontend'], 'sl_SI') ?>><?= esc_html__('Slovenian', 'mmp') ?> (sl_SI)</option>
										<option value="sv_SE" <?= $this->selected($settings['pluginLanguageFrontend'], 'sv_SE') ?>><?= esc_html__('Swedish', 'mmp') ?> (sv_SE)</option>
										<option value="es_ES" <?= $this->selected($settings['pluginLanguageFrontend'], 'es_ES') ?>><?= esc_html__('Spanish', 'mmp') ?> (es_ES)</option>
										<option value="es_MX" <?= $this->selected($settings['pluginLanguageFrontend'], 'es_MX') ?>><?= esc_html__('Spanish', 'mmp') ?> (es_MX)</option>
										<option value="th" <?= $this->selected($settings['pluginLanguageFrontend'], 'th') ?>><?= esc_html__('Thai', 'mmp') ?> (th)</option>
										<option value="tr_TR" <?= $this->selected($settings['pluginLanguageFrontend'], 'tr_TR') ?>><?= esc_html__('Turkish', 'mmp') ?> (tr_TR)</option>
										<option value="ug" <?= $this->selected($settings['pluginLanguageFrontend'], 'ug') ?>><?= esc_html__('Uighur', 'mmp') ?> (ug)</option>
										<option value="uk_UK" <?= $this->selected($settings['pluginLanguageFrontend'], 'uk_UK') ?>><?= esc_html__('Ukrainian', 'mmp') ?> (uk_UK)</option>
										<option value="vi" <?= $this->selected($settings['pluginLanguageFrontend'], 'vi') ?>><?= esc_html__('Vietnamese', 'mmp') ?> (vi)</option>
										<option value="yi" <?= $this->selected($settings['pluginLanguageFrontend'], 'yi') ?>><?= esc_html__('Yiddish', 'mmp') ?> (yi)</option>
									</select>
								</div>
							</div>
						</div>
						<div id="misc_custom_js_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Custom JavaScript', 'mmp') ?></h2>
							<div>
								<textarea name="customJs" class="mmp-custom-js"><?= $settings['customJs'] ?></textarea>
							</div>
						</div>
						<div id="misc_backup_restore_reset_tab" class="mmp-settings-tab">
							<h2><?= esc_html__('Backup, restore & reset', 'mmp') ?></h2>
							<p>
								<?= sprintf($l10n->kses__('You can backup, restore and reset the settings on the <a href="%1$s">tools page</a>.', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#backup_restore')) ?>
							</p>
						</div>
					</div>
				</div>
			</form>
			<div id="mmp-custom-layer-modal" class="mmp-admin-modal">
				<div class="mmp-admin-modal-content">
					<span class="mmp-admin-modal-close">&times;</span>
					<div class="mmp-admin-modal-header">
						<p class="mmp-admin-modal-title"><?= esc_html__('Add/edit custom layer', 'mmp') ?></p>
					</div>
					<div class="mmp-admin-modal-body">
						<form id="mmp-custom-layer-form" method="POST">
							<input type="hidden" id="customLayerId" name="customLayerId" value="0" />
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Type', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Whether the layer should be treated as a basemap or an overlay', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input">
									<ul>
										<li><label><input type="radio" id="customLayerTypeBasemap" name="customLayerType" value="0" checked="checked" /> <?= esc_html__('Basemap', 'mmp') ?></label></li>
										<li><label><input type="radio" id="customLayerTypeOverlay" name="customLayerType" value="1" /> <?= esc_html__('Overlay', 'mmp') ?></label></li>
									</ul>
								</div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc"></div>
								<div class="mmp-custom-layer-input">
									<ul>
										<li>
											<label>
												<input type="checkbox" id="customLayerWms" name="customLayerWms" />
												<?= esc_html__('WMS', 'mmp') ?>
												<div class="mmp-info mmp-info-center">
													<span><?= esc_html__('The URL points to a Web Map Service', 'mmp') ?></span>
												</div>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="customLayerTms" name="customLayerTms" />
												<?= esc_html__('TMS', 'mmp') ?>
												<div class="mmp-info mmp-info-center">
													<span><?= esc_html__('Inverses Y-axis numbering for tiles', 'mmp') ?></span>
												</div>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="customLayerRasterTiles" name="customLayerRasterTiles" />
												<?= esc_html__('Raster tiles', 'mmp') ?>
												<div class="mmp-info mmp-info-center">
													<span>
														<?= esc_html__('The tiles are non-geographical', 'mmp') ?><br />
														<?= esc_html__('Bounds will be in pixels with a top-left origin', 'mmp') ?>
													</span>
												</div>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="customLayerNoWrap" name="customLayerNoWrap" />
												<?= esc_html__('No wrap', 'mmp') ?>
												<div class="mmp-info mmp-info-center">
													<span><?= esc_html__('Prevents the layer from getting wrapped around the antimeridian', 'mmp') ?></span>
												</div>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" id="customLayerErrorTiles" name="customLayerErrorTiles" checked="checked" />
												<?= esc_html__('Show error tiles', 'mmp') ?>
												<div class="mmp-info mmp-info-center">
													<span><?= esc_html__('Shows a placeholder image for tiles that cannot be loaded', 'mmp') ?></span>
												</div>
											</label>
										</li>
									</ul>
								</div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Name', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Name of the layer', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="text" id="customLayerName" name="customLayerName" /></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('URL', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span>
											<?= esc_html__('URL template for the tile server', 'mmp') ?><br /><br />
											<?= esc_html__('Available placeholders', 'mmp') ?>:<br />
											<?= esc_html__('{s} - subdomain', 'mmp') ?><br />
											<?= esc_html__('{z} - zoom level', 'mmp') ?><br />
											<?= esc_html__('{x} and {y} - tile coordinates', 'mmp') ?><br /><br />
											<?= esc_html__('Example', 'mmp') ?>: https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
										</span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="text" id="customLayerUrl" name="customLayerUrl" /></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Subdomains', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('String of available tile server subdomains (each letter is a subdomain name)', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="text" id="customLayerSubdomains" name="customLayerSubdomains" value="abc" /></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Bounds', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span>
											<?= esc_html__('Comma-separated list of bounds', 'mmp') ?><br />
											<?= esc_html__('If set, only tiles inside these bounds will be loaded', 'mmp') ?><br /><br />
											<?= esc_html__('Format', 'mmp') ?>:<br />
											<?= esc_html__('Latitude (south), Longitude (west), Latitude (north), Longitude (east)', 'mmp') ?>
										</span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="text" id="customLayerBounds" name="customLayerBounds" /></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Min zoom', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Minimum zoom level for which the server has tiles available', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="number" id="customLayerMinZoom" name="customLayerMinZoom" value="0" min="0" max="23" step="1"/></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Max zoom', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Maximum zoom level for which the server has tiles available', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="number" id="customLayerMaxZoom" name="customLayerMaxZoom" value="21" min="0" max="23" step="1"/></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Opacity', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Opacity of the layer', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="number" id="customLayerOpacity" name="customLayerOpacity" value="1" min="0" max="1" step="0.01" /></div>
							</div>
							<div class="mmp-custom-layer-setting">
								<div class="mmp-custom-layer-desc">
									<?= esc_html__('Attribution', 'mmp') ?>
									<div class="mmp-info mmp-info-right">
										<span><?= esc_html__('Attribution text to show on the map when the layer is active', 'mmp') ?></span>
									</div>
								</div>
								<div class="mmp-custom-layer-input"><input type="text" id="customLayerAttribution" name="customLayerAttribution" /></div>
							</div>
							<div id="custom-layer-wms" class="mmp-custom-layer-settings-group">
								<span><?= esc_html__('WMS settings', 'mmp') ?></span>
								<div class="mmp-custom-layer-setting">
									<div class="mmp-custom-layer-desc"></div>
									<div class="mmp-custom-layer-input">
										<ul>
											<li>
												<label>
													<input type="checkbox" id="customLayerTransparent" name="customLayerTransparent" />
													<?= esc_html__('Transparent tiles', 'mmp') ?>
													<div class="mmp-info mmp-info-center">
														<span><?= esc_html__('Return images with transparency', 'mmp') ?></span>
													</div>
												</label>
											</li>
											<li>
												<label>
													<input type="checkbox" id="customLayerUppercase" name="customLayerUppercase" />
													<?= esc_html__('Uppercase parameters', 'mmp') ?>
													<div class="mmp-info mmp-info-center">
														<span><?= esc_html__('Request parameter keys will be uppercase', 'mmp') ?></span>
													</div>
												</label>
											</li>
										</ul>
									</div>
								</div>
								<div class="mmp-custom-layer-setting">
									<div class="mmp-custom-layer-desc">
										<?= esc_html__('Layers', 'mmp') ?>
										<div class="mmp-info mmp-info-right">
											<span><?= esc_html__('Comma-separated list of layers to show', 'mmp') ?></span>
										</div>
									</div>
									<div class="mmp-custom-layer-input"><input type="text" id="customLayerLayers" name="customLayerLayers" /></div>
								</div>
								<div class="mmp-custom-layer-setting">
									<div class="mmp-custom-layer-desc">
										<?= esc_html__('Styles', 'mmp') ?>
										<div class="mmp-info mmp-info-right">
											<span><?= esc_html__('Comma-separated list of styles', 'mmp') ?></span>
										</div>
									</div>
									<div class="mmp-custom-layer-input"><input type="text" id="customLayerStyles" name="customLayerStyles" /></div>
								</div>
								<div class="mmp-custom-layer-setting">
									<div class="mmp-custom-layer-desc">
										<?= esc_html__('Format', 'mmp') ?>
										<div class="mmp-info mmp-info-right">
											<span><?= esc_html__('Image format', 'mmp') ?></span>
										</div>
									</div>
									<div class="mmp-custom-layer-input"><input type="text" id="customLayerFormat" name="customLayerFormat" value="image/png" /></div>
								</div>
								<div class="mmp-custom-layer-setting">
									<div class="mmp-custom-layer-desc">
										<?= esc_html__('Version', 'mmp') ?>
										<div class="mmp-info mmp-info-right">
											<span><?= esc_html__('Version of the service to use', 'mmp') ?></span>
										</div>
									</div>
									<div class="mmp-custom-layer-input"><input type="text" id="customLayerVersion" name="customLayerVersion" value="1.0.0" /></div>
								</div>
							</div>
						</form>
					</div>
					<div class="mmp-admin-modal-footer">
						<button id="mmp-custom-layer-save" class="button button-primary"><?= esc_html__('Save', 'mmp') ?></button>
						<button id="mmp-custom-layer-delete" class="button button-secondary"><?= esc_html__('Delete', 'mmp') ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
