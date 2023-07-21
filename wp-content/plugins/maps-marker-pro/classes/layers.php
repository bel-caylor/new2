<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Layers {
	/**
	 * Retrieves the basemaps
	 *
	 * @since 4.14
	 *
	 * @param bool $all (optional) Whether to return all or only available basemaps
	 * @param bool $custom (optional) Whether to include custom basemaps
	 */
	public function get_basemaps($all = false, $custom = true) {
		$db = MMP::get_instance('MMP\DB');

		$osm = esc_html__('Map', 'mmp') . ': &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap ' . esc_html__('contributors', 'mmp') . '</a>';
		$osm_france = esc_html__('Map', 'mmp') . ': &copy; <a href="https://www.openstreetmap.fr" target="_blank">OpenStreetMap France</a> &amp; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap ' . esc_html__('contributors', 'mmp') . '</a>';
		$osm_hot = esc_html__('Map', 'mmp') . ': &copy; <a href="https://www.openstreetmap.fr" target="_blank">OpenStreetMap France</a> &amp; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap ' . esc_html__('contributors', 'mmp') . '</a>. ' . esc_html__('Tiles courtesy of', 'mmp') . ' <a href="https://hotosm.org" target="_blank">Humanitarian OpenStreetMap Team</a>';
		$otm = esc_html__('Map', 'mmp') . ': &copy; <a href="https://openstreetmap.org/copyright" target="_blank">OpenStreetMap ' . esc_html__('contributors', 'mmp') . '</a>, <a href="http://viewfinderpanoramas.org" target="_blank">SRTM</a>. ' . esc_html__('Map style', 'mmp') . ': &copy; <a href="https://opentopomap.org" target="_blank">OpenTopoMap</a>, ' . esc_html__('under', 'mmp') . ' <a href="https://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC BY SA</a>.';
		$cyclosm = esc_html__('Map', 'mmp') . ': &copy; <a href="https://openstreetmap.org/copyright" target="_blank">OpenStreetMap ' . esc_html__('contributors', 'mmp'). '</a>. ' . esc_html__('Map style', 'mmp') . ': &copy; <a href="https://cyclosm.org" target="_blank">CyclOSM</a>.';
		$stamen = esc_html__('Map tiles by', 'mmp') . ' <a href="http://stamen.com" target="_blank">Stamen Design</a>, ' . esc_html__('under', 'mmp') . ' <a href="http://creativecommons.org/licenses/by/3.0" target="_blank">CC BY 3.0</a>. ' . esc_html__('Data by', 'mmp') . ' <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a>, ' . esc_html__('under', 'mmp') . ' <a href="http://www.openstreetmap.org/copyright" target="_blank">ODbL</a>.';
		$stamen_watercolor = esc_html__('Map tiles by', 'mmp') . ' <a href="http://stamen.com" target="_blank">Stamen Design</a>, ' . esc_html__('under', 'mmp') . ' <a href="http://creativecommons.org/licenses/by/3.0" target="_blank">CC BY 3.0</a>. ' . esc_html__('Data by', 'mmp') . ' <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a>, ' . esc_html__('under', 'mmp') . ' <a href="http://creativecommons.org/licenses/by-sa/3.0" target="_blank">CC BY SA</a>.';
		$basemap_at = esc_html__('Map', 'mmp') . ': &copy; <a href="https://www.basemap.at" target="_blank">basemap.at</a>';

		$basemaps['osm'] = array(
			'id'      => 'osm',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'OpenStreetMap',
			'url'     => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 19,
				'attribution'   => $osm
			)
		);
		$basemaps['osmDe'] = array(
			'id'      => 'osmDe',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'OpenStreetMap (DE)',
			'url'     => 'https://tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 19,
				'attribution'   => $osm
			)
		);
		$basemaps['osmFrance'] = array(
			'id'      => 'osmFrance',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'OpenStreetMap (France)',
			'url'     => 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 20,
				'attribution'   => $osm_france
			)
		);
		$basemaps['osmHot'] = array(
			'id'      => 'osmHot',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'OpenStreetMap (HOT)',
			'url'     => 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 20,
				'attribution'   => $osm_hot
			)
		);
		$basemaps['otm'] = array(
			'id'      => 'otm',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'OpenTopoMap',
			'url'     => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 17,
				'attribution'   => $otm
			)
		);
		$basemaps['cyclosm'] = array(
			'id'      => 'cyclosm',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'CyclOSM',
			'url'     => 'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abc',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 20,
				'attribution'   => $cyclosm
			)
		);
		$basemaps['stamenTerrain'] = array(
			'id'      => 'stamenTerrain',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Terrain)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 18,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTerrainBackground'] = array(
			'id'      => 'stamenTerrainBackground',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Terrain Background)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain-background/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 18,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTerrainLines'] = array(
			'id'      => 'stamenTerrainLines',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Terrain Lines)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain-lines/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 18,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenToner'] = array(
			'id'      => 'stamenToner',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Toner)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 20,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTonerBackground'] = array(
			'id'      => 'stamenTonerBackground',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Toner Background)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 20,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTonerHybrid'] = array(
			'id'      => 'stamenTonerHybrid',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Toner Hybrid)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-hybrid/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 17,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTonerLines'] = array(
			'id'      => 'stamenTonerLines',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Toner Lines)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lines/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 18,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenTonerLite'] = array(
			'id'      => 'stamenTonerLite',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Toner Lite)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 0,
				'maxNativeZoom' => 20,
				'attribution'   => $stamen
			)
		);
		$basemaps['stamenWatercolor'] = array(
			'id'      => 'stamenWatercolor',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'Stamen (Watercolor)',
			'url'     => 'https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.png',
			'options' => array(
				'subdomains'    => 'abcd',
				'minNativeZoom' => 1,
				'maxNativeZoom' => 18,
				'attribution'   => $stamen_watercolor
			)
		);
		$basemaps['basemapAt'] = array(
			'id'      => 'basemapAt',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'basemap.at',
			'url'     => 'https://{s}.wien.gv.at/basemap/geolandbasemap/normal/google3857/{z}/{y}/{x}.png',
			'options' => array(
				'bounds'        => array([46.358770, 8.782379], [49.037872, 17.5]),
				'subdomains'    => array('maps', 'maps1', 'maps2', 'maps3', 'maps4'),
				'minNativeZoom' => 1,
				'maxNativeZoom' => 19,
				'attribution'   => $basemap_at
			)
		);
		$basemaps['basemapAtSatellite'] = array(
			'id'      => 'basemapAtSatellite',
			'type'    => 1,
			'wms'     => 0,
			'name'    => 'basemap.at (Satellite)',
			'url'     => 'https://{s}.wien.gv.at/basemap/bmaporthofoto30cm/normal/google3857/{z}/{y}/{x}.jpeg',
			'options' => array(
				'bounds'        => array([46.358770, 8.782379], [49.037872, 17.5]),
				'subdomains'    => array('maps', 'maps1', 'maps2', 'maps3', 'maps4'),
				'minNativeZoom' => 1,
				'maxNativeZoom' => 19,
				'attribution'   => $basemap_at
			)
		);

		if ($all || MMP::$settings['googleApiKey']) {
			$basemaps['googleRoadmap'] = array(
				'id'      => 'googleRoadmap',
				'type'    => 2,
				'name'    => 'Google (Roadmap)',
				'options' => array(
					'type'   => 'roadmap'
				)
			);
			$basemaps['googleSatellite'] = array(
				'id'      => 'googleSatellite',
				'type'    => 2,
				'name'    => 'Google (Satellite)',
				'options' => array(
					'type'   => 'satellite'
				)
			);
			$basemaps['googleHybrid'] = array(
				'id'      => 'googleHybrid',
				'type'    => 2,
				'name'    => 'Google (Hybrid)',
				'options' => array(
					'type'   => 'hybrid'
				)
			);
			$basemaps['googleTerrain'] = array(
				'id'      => 'googleTerrain',
				'type'    => 2,
				'name'    => 'Google (Terrain)',
				'options' => array(
					'type'   => 'terrain'
				)
			);
		}

		if ($all || MMP::$settings['bingApiKey']) {
			$basemaps['bingRoad'] = array(
				'id'      => 'bingRoad',
				'type'    => 3,
				'name'    => 'Bing (Road)',
				'options' => array(
					'imagerySet'    => 'Road',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
			$basemaps['bingAerial'] = array(
				'id'      => 'bingAerial',
				'type'    => 3,
				'name'    => 'Bing (Aerial)',
				'options' => array(
					'imagerySet'    => 'Aerial',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
			$basemaps['bingAerialLabels'] = array(
				'id'      => 'bingAerialLabels',
				'type'    => 3,
				'name'    => 'Bing (Aerial with Labels)',
				'options' => array(
					'imagerySet'    => 'AerialWithLabels',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
			$basemaps['bingCanvasDark'] = array(
				'id'      => 'bingCanvasDark',
				'type'    => 3,
				'name'    => 'Bing (Canvas Dark)',
				'options' => array(
					'imagerySet'    => 'CanvasDark',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
			$basemaps['bingCanvasLight'] = array(
				'id'      => 'bingCanvasLight',
				'type'    => 3,
				'name'    => 'Bing (Canvas Light)',
				'options' => array(
					'imagerySet'    => 'CanvasLight',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
			$basemaps['bingCanvasGray'] = array(
				'id'      => 'bingCanvasGray',
				'type'    => 3,
				'name'    => 'Bing (Canvas Gray)',
				'options' => array(
					'imagerySet'    => 'CanvasGray',
					'minNativeZoom' => 1,
					'maxNativeZoom' => 19
				)
			);
		}

		if ($all || MMP::$settings['hereApiKey'] || (MMP::$settings['hereAppId'] && MMP::$settings['hereAppCode'])) {
			$basemaps['hereNormalDay'] = array(
				'id'      => 'hereNormalDay',
				'type'    => 4,
				'name'    => 'HERE (Normal Day)',
				'options' => array(
					'scheme'        => 'normal.day',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 20
				)
			);
			$basemaps['hereNormalNight'] = array(
				'id'      => 'hereNormalNight',
				'type'    => 4,
				'name'    => 'HERE (Normal Night)',
				'options' => array(
					'scheme'        => 'normal.night',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 20
				)
			);
			$basemaps['hereTerrain'] = array(
				'id'      => 'hereTerrain',
				'type'    => 4,
				'name'    => 'HERE (Terrain)',
				'options' => array(
					'scheme'        => 'terrain.day',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 20
				)
			);
			$basemaps['hereSatellite'] = array(
				'id'      => 'hereSatellite',
				'type'    => 4,
				'name'    => 'HERE (Satellite)',
				'options' => array(
					'scheme'        => 'satellite.day',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 20
				)
			);
			$basemaps['hereHybrid'] = array(
				'id'      => 'hereHybrid',
				'type'    => 4,
				'name'    => 'HERE (Hybrid)',
				'options' => array(
					'scheme'        => 'hybrid.day',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 20
				)
			);
		}

		if ($all || MMP::$settings['tomApiKey']) {
			$basemaps['tom'] = array(
				'id'      => 'tom',
				'type'    => 5,
				'name'    => 'TomTom',
				'options' => array(
					'style'         => 'main',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 22
				)
			);
			$basemaps['tomNight'] = array(
				'id'      => 'tomNight',
				'type'    => 5,
				'name'    => 'TomTom (Night)',
				'options' => array(
					'style'         => 'night',
					'minNativeZoom' => 0,
					'maxNativeZoom' => 22
				)
			);
		}

		if ($all || MMP::$settings['limaApiKey']) {
			$basemaps['lima'] = array(
				'id'      => 'lima',
				'type'    => 1,
				'wms'     => 0,
				'name'    => 'Lima Labs',
				'url'     => 'https://cdn.lima-labs.com/{z}/{x}/{y}.png?api=' . MMP::$settings['limaApiKey'],
				'options' => array(
					'tileSize'      => 512,
					'minNativeZoom' => 1,
					'maxNativeZoom' => 18,
					'zoomOffset'    => -1,
					'attribution'   => $osm
				)
			);
		}

		$basemaps['esriStreets'] = array(
			'id'      => 'esriStreets',
			'type'    => 6,
			'name'    => 'ESRI Streets',
			'key'     => 'Streets',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 19,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriTopographic'] = array(
			'id'      => 'esriTopographic',
			'type'    => 6,
			'name'    => 'ESRI Topographic',
			'key'     => 'Topographic',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 19,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriNationalGeographic'] = array(
			'id'      => 'esriNationalGeographic',
			'type'    => 6,
			'name'    => 'ESRI National Geographic',
			'key'     => 'NationalGeographic',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 16,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriGray'] = array(
			'id'      => 'esriGray',
			'type'    => 6,
			'name'    => 'ESRI Gray',
			'key'     => 'Gray',
			'labels'  => 'GrayLabels',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 16,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriDarkGray'] = array(
			'id'      => 'esriDarkGray',
			'type'    => 6,
			'name'    => 'ESRI Dark Gray',
			'key'     => 'DarkGray',
			'labels'  => 'GrayLabels',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 16,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriOceans'] = array(
			'id'      => 'esriOceans',
			'type'    => 6,
			'name'    => 'ESRI Oceans',
			'key'     => 'Oceans',
			'labels'  => 'OceansLabels',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 16,
				'ignoreDeprecationWarning' => true
			)
		);
		$basemaps['esriImagery'] = array(
			'id'      => 'esriImagery',
			'type'    => 6,
			'name'    => 'ESRI Imagery',
			'key'     => 'Imagery',
			'labels'  => 'ImageryLabels',
			'options' => array(
				'minNativeZoom'            => 1,
				'maxNativeZoom'            => 19,
				'ignoreDeprecationWarning' => true
			)
		);

		if (!$all) {
			$basemaps = array_diff_key($basemaps, array_flip(MMP::$settings['disabledBasemaps']));
		}

		if ($custom) {
			foreach ($db->get_all_basemaps() as $custom) {
				$basemaps[$custom->id] = array(
					'id'      => $custom->id,
					'type'    => 1,
					'wms'     => absint($custom->wms),
					'name'    => $custom->name,
					'url'     => $custom->url,
					'options' => json_decode($custom->options)
				);
			}
		}

		return $basemaps;
	}

	/**
	 * Retrieves the overlays
	 *
	 * @since 4.14
	 */
	public function get_overlays() {
		$db = MMP::get_instance('MMP\DB');

		$overlays = array();
		foreach ($db->get_all_overlays() as $custom) {
			$overlays[$custom->id] = array(
				'id'      => $custom->id,
				'wms'     => absint($custom->wms),
				'name'    => $custom->name,
				'url'     => $custom->url,
				'options' => json_decode($custom->options)
			);
		}

		return $overlays;
	}
}
