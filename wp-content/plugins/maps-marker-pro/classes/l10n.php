<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class L10n {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_filter('plugin_locale', array($this,'set_plugin_locale'), 10, 2);

		add_action('init', array($this, 'load_translations'));
	}

	/**
	 * Sets the plugin locale
	 *
	 * @since 4.0
	 *
	 * @param string $locale Current plugin locale
	 * @param string $domain Unique identifier for retrieving translated strings
	 */
	public function set_plugin_locale($locale, $domain) {
		if ($domain !== 'mmp') {
			return $locale;
		}

		if ($locale === 'de_AT' || $locale === 'de_CH') {
			$locale = 'de_DE_formal';
		} else if ($locale === 'de_CH_informal') {
			$locale = 'de_DE';
		}

		if (is_admin()) {
			if (MMP::$settings['pluginLanguageAdmin'] === 'automatic') {
				return $locale;
			} else {
				return MMP::$settings['pluginLanguageAdmin'];
			}
		} else {
			if (MMP::$settings['pluginLanguageFrontend'] === 'automatic') {
				return $locale;
			} else {
				return MMP::$settings['pluginLanguageFrontend'];
			}
		}
	}

	/**
	 * Loads the plugin translations
	 *
	 * @since 4.0
	 */
	public function load_translations() {
		load_plugin_textdomain('mmp', false, basename(MMP::$dir) . '/languages');
	}

	/**
	 * Returns a localized date
	 *
	 * @since 4.9.1
	 *
	 * @param string $format Output format
	 * @param int|string $date (optional) GMT date to be localized (Unix timestamp or MySQL datetime)
	 */
	public function date($format, $date = null) {
		if ($format === 'date') {
			$format = get_option('date_format');
		} else if ($format === 'time') {
			$format = get_option('time_format');
		} else if ($format === 'datetime') {
			$format = get_option('date_format') . ' ' . get_option('time_format');
		}

		if (!$date) {
			$date = '@' . time();
		} else if (is_numeric($date)) {
			$date = '@' . $date;
		}

		$datetime = (new \DateTime($date, new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
		$local_timestamp = get_date_from_gmt($datetime, 'U');

		return date_i18n($format, $local_timestamp);
	}

	/**
	 * Translates a string and strips all HTML tags except links
	 *
	 * @since 4.0
	 *
	 * @param string $string String to be translated
	 * @param string $domain Translation text domain
	 */
	public function kses__($string, $domain) {
		$allowed = array(
			'a' => array(
				'href' => array(),
				'target' => array(),
				'title' => array()
			)
		);

		return wp_kses(__($string, $domain), $allowed);
	}

	/**
	 * Checks if a translation plugin is active
	 *
	 * @since 4.0
	 */
	public function check_ml() {
		if (!defined('ICL_LANGUAGE_CODE')) {
			return false;
		}

		if (defined('ICL_SITEPRESS_VERSION') && defined('WPML_ST_VERSION')) {
			return 'wpml';
		} else if (defined('POLYLANG_VERSION')) {
			return 'pll';
		} else {
			return false;
		}
	}

	/**
	 * Registers a string for translation
	 *
	 * @since 4.0
	 *
	 * @param string $name Unique name for the string
	 * @param string $string String to be translated
	 */
	public function register($name, $string) {
		if ($this->check_ml() === 'wpml' || $this->check_ml() === 'pll') {
			do_action('wpml_register_single_string', 'Maps Marker Pro', $name, $string, false, apply_filters('wpml_default_language', null));
		}
	}

	/**
	 * Translates a string into the current language
	 *
	 * @since 4.0
	 *
	 * @param string $string String to be translated
	 * @param string $name Unique name for the string
	 */
	public function __($string, $name) {
		if ($this->check_ml() === 'wpml') {
			return apply_filters('wpml_translate_single_string', $string, 'Maps Marker Pro', $name);
		} else if ($this->check_ml() === 'pll' && function_exists('pll__')) {
			return pll__($string);
		} else {
			return $string;
		}
	}

	/**
	 * Adds the current language to a link
	 *
	 * @since 4.0
	 *
	 * @param string $link Link to add the language to
	 */
	public function link($link) {
		if ($this->check_ml()) {
			return add_query_arg('lang', ICL_LANGUAGE_CODE, $link);
		} else {
			return $link;
		}
	}

	/**
	 * Returns the map strings needed for JavaScript localization
	 *
	 * @since 4.0
	 */
	public function map_strings() {
		return array(
			'api' => array(
				'editMap'    => __('Edit map', 'mmp'),
				'editMarker' => __('Edit marker', 'mmp'),
				'dir'        => __('Get directions', 'mmp'),
				'fs'         => __('Open standalone map in fullscreen mode', 'mmp'),
				'geoJson'    => __('Export as GeoJSON', 'mmp'),
				'kml'        => __('Export as KML', 'mmp'),
				'geoRss'     => __('Export as GeoRSS', 'mmp'),
				'share'      => __('Share', 'mmp'),
			),
			'control' => array(
				'geocodingTitle'            => __('Find a location', 'mmp'),
				'geocodingError'            => __('Geocoding error', 'mmp'),
				'zoomIn'                    => __('Zoom in', 'mmp'),
				'zoomOut'                   => __('Zoom out', 'mmp'),
				'fullscreenFalse'           => __('View fullscreen', 'mmp'),
				'fullscreenTrue'            => __('Exit fullscreen', 'mmp'),
				'reset'                     => __('Reset map view', 'mmp'),
				'locateTitle'               => __('Show me where I am', 'mmp'),
				'locateMetersUnit'          => __('meters', 'mmp'),
				'locateFeetUnit'            => __('feet', 'mmp'),
				'locatePopup'               => sprintf(__('You are within %1$s %2$s from this point', 'mmp'), '{distance}', '{unit}'),
				'locateOutsideMapBoundsMsg' => __('You seem located outside the boundaries of the map', 'mmp'),
				'locateError'               => __('Geolocation error', 'mmp'),
				'locateErrorUnknown'        => __('The geolocation has failed for an unknown reason', 'mmp'),
				'locateErrorDenied'         => __('The geolocation has been denied', 'mmp'),
				'locateErrorUnavailable'    => __('The geolocation is unavailable', 'mmp'),
				'locateErrorTimeout'        => __('The geolocation has timed out', 'mmp'),
				'measureOn'                 => __('Turn on measuring', 'mmp'),
				'measureOff'                => __('Turn off measuring', 'mmp'),
				'measureIn'                 => _x('In', 'Angle of incidence', 'mmp'),
				'measureOut'                => _x('Out', 'Angle of emergence', 'mmp'),
				'measureClear'              => __('Clear measurements', 'mmp'),
				'measureFinish'             => __('Click to <b>finish line</b>', 'mmp'),
				'measureDelete'             => __('Press SHIFT-key and click to <b>delete point</b>', 'mmp'),
				'measureMove'               => __('Click and drag to <b>move point</b>', 'mmp'),
				'measureResume'             => __('Press CTRL-key and click to <b>resume line</b>', 'mmp'),
				'measureAdd'                => __('Press CTRL-key and click to <b>add point</b>', 'mmp'),
				'measureChangeUnits'        => __('Change units', 'mmp'),
				'measureMeters'             => __('meters', 'mmp'),
				'measureMiles'              => __('miles', 'mmp'),
				'measureNauticalMiles'      => __('nautical miles', 'mmp'),
				'filtersAll'                => __('all', 'mmp'),
				'filtersNone'               => __('none', 'mmp'),
				'minimapHideText'           => __('Hide minimap', 'mmp'),
				'minimapShowText'           => __('Show minimap', 'mmp')
			),
			'popup' => array(
				'directions' => __('Directions', 'mmp'),
				'info'       => __('If a popup text is set, it will appear here', 'mmp')
			),
			'list' => array(
				'id'            => __('Marker ID', 'mmp'),
				'name'          => __('Marker name', 'mmp'),
				'address'       => __('Address', 'mmp'),
				'distance'      => __('Distance', 'mmp'),
				'icon'          => __('Icon', 'mmp'),
				'created_on'    => __('Created', 'mmp'),
				'updated_on'    => __('Updated', 'mmp'),
				'noResults'     => __('No results', 'mmp'),
				'oneResult'     => __('One result', 'mmp'),
				'results'       => __('results', 'mmp'),
				'search'        => __('Search markers', 'mmp'),
				'location'      => __('Find a location', 'mmp'),
				'locationError' => __('Geocoding error', 'mmp'),
				'noLimit'       => __('No limit', 'mmp')
			),
			'interaction' => array(
				'gestureTouch'     => __('Use two fingers to move the map', 'mmp'),
				'gestureScroll'    => __('Use ctrl + scroll to zoom the map', 'mmp'),
				'gestureScrollMac' => sprintf(__('Use %1$s + scroll to zoom the map', 'mmp'), json_decode('"\u2318"'))
			),
			'gpx' => array(
				'loadingError'    => __('Error loading GPX', 'mmp'),
				'loadingErrorMsg' => __('The GPX file could not be loaded. Please contact the site owner.', 'mmp'),
				'metaName'        => __('Track name', 'mmp'),
				'metaStart'       => __('Start', 'mmp'),
				'metaEnd'         => __('End', 'mmp'),
				'metaTotal'       => __('Duration', 'mmp'),
				'metaMoving'      => __('Moving time', 'mmp'),
				'metaDistance'    => __('Distance', 'mmp'),
				'metaPace'        => __('Pace', 'mmp'),
				'metaHeartRate'   => __('Heart rate', 'mmp'),
				'metaElevation'   => __('Elevation', 'mmp'),
				'metaDownload'    => __('download GPX file', 'mmp'),
				'noElevationData' => __('No elevation data available', 'mmp')
			)
		);
	}

	/**
	 * Returns the admin strings needed for JavaScript localization
	 *
	 * @since 4.0
	 */
	public function admin_strings() {
		return array(
			'global' => array(
				'ajaxError'  => __('Failed to send request', 'mmp'),
				'dateFormat' => get_option('date_format'),
				'timeFormat' => get_option('time_format')
			),
			'map' => array(
				'chooseGpx'      => __('Select or Upload GPX file', 'mmp'),
				'choose'         => __('Choose', 'mmp'),
				'saved'          => __('Map saved successfully', 'mmp'),
				'invalidJson'    => __('Invalid JSON', 'mmp'),
				'invalidGeoJson' => __('Invalid GeoJSON', 'mmp'),
				'cancel'         => __('Cancel', 'mmp'),
				'save'           => __('Save', 'mmp'),
				'add'            => __('Add', 'mmp')
			),
			'maps' => array(
				'bulkDuplicate'       => __('Duplicate the selected maps?', 'mmp'),
				'bulkDuplicateAssign' => __('Duplicate the selected maps and assign their respective markers?', 'mmp'),
				'bulkDelete'          => __('Delete the selected maps and unassign their markers?', 'mmp'),
				'bulkDeleteAssign'    => sprintf(__('Delete the selected maps and assign all their markers to the map with ID %1$s', 'mmp'), '{map}'),
				'bulkDeleteDelete'    => __('Delete the selected maps and their markers?', 'mmp'),
			),
			'marker' => array(
				'notAssigned' => __('Not assigned to any map', 'mmp'),
				'delete'      => __('Are you sure you want to delete this marker?', 'mmp'),
				'saved'       => __('Marker saved successfully', 'mmp'),
				'edit'        => __('edit', 'mmp')
			),
			'markers' => array(
				'delete'        => sprintf(__('Are you sure you want to delete the marker with ID %1$s', 'mmp'), '{marker}'),
				'bulkDuplicate' => __('Duplicate the selected markers?', 'mmp'),
				'bulkDelete'    => __('Delete the selected markers?', 'mmp'),
				'bulkAssign'    => sprintf(__('Assign the selected markers to the map with ID %1$s', 'mmp'), '{map}')
			),
			'settings' => array(
				'search'        => __('Start full-text search', 'mmp'),
				'selectIcons'   => __('Select icons', 'mmp'),
				'confirmDelete' => __('Are you sure you want to delete the selected icons? This cannot be undone!', 'mmp'),
				'confirmReset'  => __('Are you sure you want to reset the settings to their defaults? This cannot be undone!', 'mmp')
			),
			'tools' => array(
				'batchSettings'      => __('Are you sure you want to apply the chosen settings to the selected maps? This cannot be undone.', 'mmp'),
				'batchMarker'        => __('Are you sure you want to apply the chosen settings to the selected markers? This cannot be undone.', 'mmp'),
				'batchLayers'        => __('Are you sure you want to apply the chosen layers to the selected maps? This cannot be undone.', 'mmp'),
				'replaceIcon'        => __('Are you sure you want to replace these icons? This cannot be undone.', 'mmp'),
				'testModeOn'         => __('Test mode on - no changes will be made to the database.', 'mmp'),
				'testModeOff'        => __('Test mode off - changes will be saved to the database.', 'mmp'),
				'maxFileSize'        => sprintf(__('The maximum upload file size on your server is %1$s', 'mmp'), '{maxFileSize}'),
				'chosenFileSizeGood' => sprintf(__('Your chosen file has %1$s, we are good to go', 'mmp'), '{chosenFileSize}'),
				'chosenFileSizeBad'  => sprintf(__('Your chosen file has %1$s, cannot continue', 'mmp'), '{chosenFileSize}'),
				'close'              => __('Close', 'mmp')
			),
			'geoJson' => array(
				'polyline'  => __('Polyline', 'mmp'),
				'rectangle' => __('Rectangle', 'mmp'),
				'polygon'   => __('Polygon', 'mmp'),
				'circle'    => __('Circle', 'mmp')
			)
		);
	}

	/**
	 * Returns the Gutenberg strings needed for JavaScript localization
	 *
	 * @since 4.0
	 */
	public function gb_strings() {
		return array(
			'selectMap' => __('Select the map you want to display', 'mmp')
		);
	}
}
