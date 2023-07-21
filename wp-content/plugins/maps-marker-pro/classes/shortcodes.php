<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Shortcodes {
	/**
	 * List of active shortcodes
	 *
	 * @since 4.0
	 * @var array
	 */
	private $shortcodes;

	/**
	 * Sets up the class
	 *
	 * @since 4.0
	 */
	public function __construct() {
		$this->shortcodes = array();
	}

	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('wp_enqueue_scripts', array($this, 'load_resources'));
		add_action('init', array($this, 'add_shortcodes'));
		add_action('wp_footer', array($this, 'load_shortcodes'), 21);
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.6
	 */
	public function load_resources() {
		wp_enqueue_style('mapsmarkerpro');
		if (is_rtl()) {
			wp_enqueue_style('mapsmarkerpro-rtl');
		}
	}

	/**
	 * Adds the shortcodes
	 *
	 * @since 4.0
	 */
	public function add_shortcodes() {
		add_shortcode(MMP::$settings['shortcode'], array($this, 'map_shortcode'));
	}

	/**
	 * Processes the map shortcode
	 *
	 * @since 4.0
	 *
	 * @param array $atts Attributes used in the shortcode
	 */
	public function map_shortcode($atts) {
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');
		$api = MMP::get_instance('MMP\API');
		$l10n = MMP::get_instance('MMP\L10n');

		// Backwards compatibility for obsolete attributes
		$attributes = array(
			'layer'           => 'map',
			'custom'          => 'map',
			'highlightmarker' => 'highlight'
		);
		foreach ($attributes as $old => $new) {
			if (isset($atts[$old])) {
				$atts[$new] = $atts[$old];
			}
		}

		if (isset($atts['map'])) {
			$map_id = absint($atts['map']);
			if (!$map_id) {
				return $this->error(esc_html__('Error: map could not be loaded - invalid shortcode. Please contact the site owner.', 'mmp'));
			}
			$map = $db->get_map($map_id);
			if (!$map) {
				return $this->error(sprintf(esc_html__('Error: map could not be loaded - a map with ID %1$s does not exist. Please contact the site owner.', 'mmp'), $map_id));
			}
			$map_settings = $mmp_settings->validate_map_settings(json_decode($map->settings, true));
		} else {
			$map_id = null;
			$map_settings = $mmp_settings->get_map_defaults();
		}

		if (isset($atts['marker'])) {
			$type = 'marker';
			$marker_id = absint($atts['marker']);
			if (!$marker_id) {
				return $this->error(esc_html__('Error: map could not be loaded - invalid shortcode. Please contact the site owner.', 'mmp'));
			}
			$marker = $db->get_marker($marker_id);
			if (!$marker) {
				return $this->error(sprintf(esc_html__('Error: map could not be loaded - a marker with ID %1$s does not exist. Please contact the site owner.', 'mmp'), $marker_id));
			}
		} else if (isset($atts['markers'])) {
			$type = 'custom';
			$marker_ids = $db->sanitize_ids($atts['markers']);
			if (!count($marker_ids)) {
				return $this->error(esc_html__('Error: map could not be loaded - invalid shortcode. Please contact the site owner.', 'mmp'));
			}
		} else {
			$type = 'map';
			if (!$map_id) {
				return $this->error(esc_html__('Error: map could not be loaded - invalid shortcode. Please contact the site owner.', 'mmp'));
			}
		}

		if (isset($atts['highlight'])) {
			$highlight = array(
				'id'   => absint($atts['highlight']),
				'open' => (isset($atts['open']) && $atts['open'] === 'true') // Defaults to false
			);
		} else if (isset($atts['openpopup'])) {
			$highlight = array(
				'id'   => absint($atts['openpopup']),
				'open' => (!isset($atts['open']) || $atts['open'] !== 'false') // Defaults to true
			);
		}

		// Array containing map setting keys and their respective lowercase versions
		// Needed because WordPress converts shortcode attributes to lowercase
		// Also prevents issues when incorrect capitalization for overrides is used
		$allowed_settings = array();
		foreach (array_keys($mmp_settings->map_settings_sanity()) as $setting) {
			$allowed_settings[strtolower($setting)] = $setting;
		}

		// Array containing the correctly capitalized setting keys and overrides
		$overrides = array();
		foreach ($atts as $key => $att) {
			if (isset($allowed_settings[$key])) {
				$overrides[$allowed_settings[$key]] = $att;
			}
		}
		$overrides = $mmp_settings->validate_map_settings($overrides, true, true);

		$map_settings = array_merge($map_settings, $overrides);

		$uid = (isset($atts['uid'])) ? esc_js($atts['uid']) : substr(md5(rand()), 0, 8);
		$lazy = (isset($atts['lazy'])) ? ($atts['lazy'] === 'true') : MMP::$settings['lazyLoadMaps'];

		if (MMP::$settings['googleApiKey'] && count(array_intersect(array('googleRoadmap', 'googleSatellite', 'googleHybrid', 'googleTerrain'), $map_settings['basemaps']))) {
			wp_enqueue_script('mmp-googlemaps');
		}
		wp_enqueue_script('mapsmarkerpro');

		$shortcode['uid'] = $uid;
		$shortcode['type'] = $type;
		$shortcode['id'] = strval($map_id);
		$shortcode['lazy'] = $lazy;
		if (isset($marker_id)) {
			$shortcode['marker'] = $marker_id;
		}
		if (isset($marker_ids)) {
			$shortcode['markers'] = $marker_ids;
		}
		if (count($overrides)) {
			$shortcode['overrides'] = $overrides;
		}
		if (isset($highlight)) {
			$shortcode['highlight'] = $highlight;
		}

		$this->shortcodes[] = $shortcode;

		if ($map_settings['list'] === 1) {
			$list_css = ' mmp-list-below';
		} else if ($map_settings['list'] === 2) {
			$list_css = ' mmp-list-right';
		} else if ($map_settings['list'] === 3) {
			$list_css = ' mmp-list-left';
		} else {
			$list_css = '';
		}

		ob_start();
		?>
		<div id="maps-marker-pro-<?= $uid ?>" class="maps-marker-pro<?= $list_css ?>" style="width: <?= $map_settings['width'] . $map_settings['widthUnit'] ?>;">
			<div id="mmp-map-wrap-<?= $uid ?>" class="mmp-map-wrap">
				<?php if ($map_settings['panel']): ?>
					<div id="mmp-panel-<?= $uid ?>" class="mmp-panel"></div>
				<?php endif; ?>
				<div id="mmp-map-<?= $uid ?>" class="mmp-map" style="height: <?= $map_settings['height'] . $map_settings['heightUnit']?>;"></div>
				<?php if ($map_settings['gpxUrl'] && $map_settings['gpxChart']): ?>
					<div id="mmp-chart-wrap-<?= $uid ?>" class="mmp-gpx-chart-wrap" style="height: <?= $map_settings['gpxChartHeight'] ?>px;"></div>
				<?php endif; ?>
			</div>
			<?php if ($map_settings['list'] > 0): ?>
				<div id="mmp-list-<?= $uid ?>" class="mmp-list" style="flex-basis: <?= $map_settings['listWidth'] ?>px;"></div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Loads the active shortcodes
	 *
	 * @since 4.0
	 */
	public function load_shortcodes() {
		if (!count($this->shortcodes)) {
			return;
		}

		?>
		<script>
			var mapsMarkerPro = {};
			<?php foreach ($this->shortcodes as $shortcode): ?>
				mapsMarkerPro['<?= $shortcode['uid'] ?>'] = <?= json_encode($shortcode) ?>;
			<?php endforeach; ?>
			if (document.readyState !== 'loading') {
				MapsMarkerPro.init();
			} else {
				document.addEventListener('DOMContentLoaded', function() {
					if (typeof MapsMarkerPro !== 'undefined') {
						MapsMarkerPro.init();
					} else {
						window.addEventListener('load', function() {
							MapsMarkerPro.init();
						});
					}
				});
			}
		</script>
		<?php
	}

	/**
	 * Displays an error message if the shortcode is invalid
	 *
	 * @since 4.0
	 *
	 * @param string $message Message to be displayed
	 */
	private function error($message) {
		return '<div class="maps-marker-pro mmp-map-error">' . $message . '</div>';
	}
}
