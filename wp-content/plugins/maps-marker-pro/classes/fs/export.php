<?php
namespace MMP\FS;

use MMP\Maps_Marker_Pro as MMP;

class Export {
	/**
	 * Processes the export request
	 *
	 * @since 4.0
	 */
	public function request() {
		$db = MMP::get_instance('MMP\DB');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		$id = get_query_var('map');
		if ($id === '') {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Map ID missing', 'mmp'));
		}
		$id = absint($id);
		if ($id === 0) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Invalid map ID', 'mmp'));
		}
		$map = $db->get_map($id);
		if (!$map) {
			die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Map not found', 'mmp'));
		}

		$settings = $mmp_settings->validate_map_settings(json_decode($map->settings, true));
		if ($settings['filtersAllMarkers']) {
			$marker_filters = array();
			$map_filters = array(
				'include' => $id
			);
		} else {
			$filter_ids = array_merge(array($id), array_keys(json_decode($map->filters, true)));
			$marker_filters = array(
				'include_maps' => $filter_ids,
				'scheduled'    => false
			);
			if ($settings['filtersGeoJson']) {
				$map_filters = array(
					'include' => $filter_ids
				);
			} else {
				$map_filters = array(
					'include' => $id
				);
			}
		}
		$markers = $db->get_all_markers($marker_filters);
		$maps_geosjon = $db->get_all_maps(false, $map_filters);
		$feature_collection = array();
		foreach ($maps_geosjon as $map_geojson) {
			$geojson = json_decode(($map_geojson->geojson) ? $map_geojson->geojson : '{}', true);
			if ($geojson === false || !isset($geojson['features']) || !is_array($geojson['features'])) {
				continue;
			}
			$feature_collection = array_merge($feature_collection, $geojson['features']);
		}

		header('Access-Control-Allow-Origin: *');

		$format = get_query_var('format');
		switch ($format) {
			case 'geojson':
				$output = $this->geojson($markers, $feature_collection);
				$callback = (isset($_GET['callback'])) ? esc_js($_GET['callback']) : null;
				if ($callback) {
					header('Content-type: application/javascript; charset=utf-8');
					echo "$callback($output);";
				} else {
					header('Content-type: application/json; charset=utf-8');
					echo $output;
				}
				break;
			case 'kml':
				$output = $this->kml($markers, $feature_collection);
				header('Content-type: application/vnd.google-earth.kml+xml; charset=utf-8');
				header('Content-Disposition: attachment; filename="map-' . $id . '.kml"');
				echo $output->asXML();
				break;
			case 'georss':
				$output = $this->georss($map, $markers);
				header('Content-type: application/rss+xml; charset=utf-8');
				echo $output->asXML();
				break;
			case 'atom':
				$output = $this->atom($map, $markers);
				header('Content-type: application/atom+xml; charset=utf-8');
				echo $output->asXML();
				break;
			default:
				die(esc_html__('Error', 'mmp') . ': ' . esc_html__('Invalid format', 'mmp'));
		}
	}

	/**
	 * Converts marker data into the GeoJSON format
	 *
	 * @since 4.0
	 * @since 4.15 $feature_collection parameter added
	 *
	 * @param array $markers List of marker data to be converted
	 * @param array $feature_collection (optional) List of GeoJSON features to be added
	 */
	private function geojson($markers, $feature_collection = array()) {
		$l10n = MMP::get_instance('MMP\L10n');

		$geojson['type'] = 'FeatureCollection';
		$geojson['features'] = array();
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
					'name' => $l10n->__($marker->name, "Marker (ID {$marker->id}) name"),
					'address' => $l10n->__($marker->address, "Marker (ID {$marker->id}) address"),
					'zoom' => $marker->zoom,
					'icon' => ($marker->icon) ? MMP::$icons_url . $marker->icon : plugins_url('images/leaflet/marker.png', MMP::$path),
					'popup' => apply_filters('mmp_popup', $l10n->__($marker->popup, "Marker (ID {$marker->id}) popup"), $marker),
					'link' => $l10n->__($marker->link, "Marker (ID {$marker->id}) link"),
					'maps' => explode(',', $marker->maps)
				)
			);
		}
		$geojson['features'] = array_merge($geojson['features'], $feature_collection);
		$geojson = json_encode($geojson, JSON_PRETTY_PRINT);

		return $geojson;
	}

	/**
	 * Converts marker data into the KML format
	 *
	 * @since 4.0
	 * @since 4.15 $feature_collection parameter added
	 *
	 * @param array $markers List of marker data to be converted
	 * @param array $feature_collection (optional) List of GeoJSON features to be converted
	 */
	private function kml($markers, $feature_collection = array()) {
		$l10n = MMP::get_instance('MMP\L10n');

		$kml = new \SimpleXMLElement(
			  '<?xml version="1.0" encoding="UTF-8"?>'
			. '<kml xmlns="http://www.opengis.net/kml/2.2"></kml>'
		);
		$document = $kml->addChild('Document');
		if (MMP::$settings['backlinks']) {
			$screen_overlay = $document->addChild('ScreenOverlay');
			$screen_overlay->addChild('name', 'MapsMarker.com');
			$icon = $screen_overlay->addChild('Icon');
			$icon->addChild('href', plugins_url('images/icons/kml-overlay.png', MMP::$path));
			$overlay_xy = $screen_overlay->addChild('overlayXY');
			$overlay_xy->addAttribute('x', 0);
			$overlay_xy->addAttribute('y', 1);
			$overlay_xy->addAttribute('xunits', 'fraction');
			$overlay_xy->addAttribute('yunits', 'fraction');
			$screen_xy = $screen_overlay->addChild('screenXY');
			$screen_xy->addAttribute('x', 0);
			$screen_xy->addAttribute('y', 1);
			$screen_xy->addAttribute('xunits', 'fraction');
			$screen_xy->addAttribute('yunits', 'fraction');
			$rotation_xy = $screen_overlay->addChild('rotationXY');
			$rotation_xy->addAttribute('x', 0);
			$rotation_xy->addAttribute('y', 0);
			$rotation_xy->addAttribute('xunits', 'fraction');
			$rotation_xy->addAttribute('yunits', 'fraction');
			$size = $screen_overlay->addChild('size');
			$size->addAttribute('x', 0);
			$size->addAttribute('y', 0);
			$size->addAttribute('xunits', 'fraction');
			$size->addAttribute('yunits', 'fraction');
		}
		foreach ($markers as $marker) {
			$placemark = $document->addChild('Placemark');
			$this->add_cdata('name', $l10n->__($marker->name, "Marker (ID {$marker->id}) name"), $placemark);
			$this->add_cdata('description', apply_filters('mmp_popup', $l10n->__($marker->popup, "Marker (ID {$marker->id}) popup"), $marker), $placemark);
			$style = $placemark->addChild('Style');
			$style->addAttribute('id', "marker{$marker->id}");
			$icon_style = $style->addChild('IconStyle');
			$icon = $icon_style->addChild('Icon');
			$href = $icon->addChild('href', ($marker->icon) ? MMP::$icons_url . $marker->icon : plugins_url('images/leaflet/marker.png', MMP::$path));
			$hotspot = $icon_style->addChild('hotspot');
			$hotspot->addAttribute('x', MMP::$settings['iconAnchorX']);
			$hotspot->addAttribute('y', MMP::$settings['iconAnchorY']);
			$hotspot->addAttribute('xunits', 'pixels');
			$hotspot->addAttribute('yunits', 'pixels');
			$point = $placemark->addChild('Point');
			$point->addChild('coordinates', "{$marker->lng},{$marker->lat}");
		}
		foreach ($feature_collection as $feature) {
			if ($feature['geometry']['type'] === 'LineString') {
				$placemark = $document->addChild('Placemark');
				$style = $placemark->addChild('Style');
				$line_style = $style->addChild('LineStyle');
				$color = $line_style->addChild('color', $this->format_color_opacity_kml($feature['properties']['color'], $feature['properties']['opacity']));
				$width = $line_style->addChild('width', $feature['properties']['weight']);
				$line_string = $placemark->addChild('LineString');
				$coords = array();
				foreach ($feature['geometry']['coordinates'] as $coord) {
					$coords[] = "{$coord[0]},{$coord[1]}";
				}
				$coordinates = $line_string->addChild('coordinates', implode(' ', $coords));
			} else if ($feature['geometry']['type'] === 'Polygon') {
				$placemark = $document->addChild('Placemark');
				$style = $placemark->addChild('Style');
				$line_style = $style->addChild('LineStyle');
				$color = $line_style->addChild('color', $this->format_color_opacity_kml($feature['properties']['color'], $feature['properties']['opacity']));
				$width = $line_style->addChild('width', $feature['properties']['weight']);
				$poly_style = $style->addChild('PolyStyle');
				$color = $poly_style->addChild('color', $this->format_color_opacity_kml($feature['properties']['fillColor'], $feature['properties']['fillOpacity']));
				$fill = $poly_style->addChild('fill', ($feature['properties']['fill']) ? 1 : 0);
				$outline = $poly_style->addChild('outline', ($feature['properties']['stroke']) ? 1 : 0);
				$polygon = $placemark->addChild('Polygon');
				for ($i = 0; $i < count($feature['geometry']['coordinates']); $i++) {
					if ($i === 0) {
						$outer_boundary_is = $polygon->addChild('outerBoundaryIs');
						$linear_ring = $outer_boundary_is->addChild('LinearRing');
					} else {
						$inner_boundary_is = $polygon->addChild('innerBoundaryIs');
						$linear_ring = $inner_boundary_is->addChild('LinearRing');
					}
					$coords = array();
					foreach ($feature['geometry']['coordinates'][$i] as $coord) {
						$coords[] = "{$coord[0]},{$coord[1]}";
					}
					$coordinates = $linear_ring->addChild('coordinates', implode(' ', $coords));
				}
			} else if ($feature['geometry']['type'] === 'MultiPolygon') {
				$placemark = $document->addChild('Placemark');
				$style = $placemark->addChild('Style');
				$line_style = $style->addChild('LineStyle');
				$color = $line_style->addChild('color', $this->format_color_opacity_kml($feature['properties']['color'], $feature['properties']['opacity']));
				$width = $line_style->addChild('width', $feature['properties']['weight']);
				$poly_style = $style->addChild('PolyStyle');
				$color = $poly_style->addChild('color', $this->format_color_opacity_kml($feature['properties']['fillColor'], $feature['properties']['fillOpacity']));
				$fill = $poly_style->addChild('fill', ($feature['properties']['fill']) ? 1 : 0);
				$outline = $poly_style->addChild('outline', ($feature['properties']['stroke']) ? 1 : 0);
				$multi_geometry = $placemark->addChild('MultiGeometry');
				foreach ($feature['geometry']['coordinates'] as $polygon_coords) {
					$polygon = $multi_geometry->addChild('Polygon');
					for ($i = 0; $i < count($polygon_coords); $i++) {
						if ($i === 0) {
							$outer_boundary_is = $polygon->addChild('outerBoundaryIs');
							$linear_ring = $outer_boundary_is->addChild('LinearRing');
						} else {
							$inner_boundary_is = $polygon->addChild('innerBoundaryIs');
							$linear_ring = $inner_boundary_is->addChild('LinearRing');
						}
						$coords = array();
						foreach ($polygon_coords[$i] as $coord) {
							$coords[] = "{$coord[0]},{$coord[1]}";
						}
						$coordinates = $linear_ring->addChild('coordinates', implode(' ', $coords));
					}
				}
			}
		}

		return $kml;
	}

	/**
	 * Converts marker data into the GeoRSS format
	 *
	 * @since 4.0
	 *
	 * @param object $map Map object the markers are assigned to
	 * @param array $markers List of marker data to be converted
	 */
	private function georss($map, $markers) {
		$api = MMP::get_instance('MMP\API');
		$l10n = MMP::get_instance('MMP\L10n');

		$georss = new \SimpleXMLElement(
			  '<?xml version="1.0" encoding="UTF-8"?>'
			. '<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml"></rss>'
		);
		$channel = $georss->addChild('channel');
		$link = $channel->addChild('link');
		$link->addAttribute('href', $api->link("/fullscreen/{$map->id}/"));
		$this->add_cdata('title', get_bloginfo('name') . ' - ' . $l10n->__($map->name, "Map (ID {$map->id}) name"), $channel);
		foreach ($markers as $marker) {
			$item = $channel->addChild('item');
			$guid = $item->addChild('guid', $api->link("/fullscreen/{$map->id}/?marker={$marker->id}"));
			$item->addChild('pubDate', (new \DateTime($marker->created_on, new \DateTimeZone('UTC')))->format('D, d M Y H:i:s T'));
			$this->add_cdata('title', $l10n->__($marker->name, "Marker (ID {$marker->id}) name"), $item);
			$this->add_cdata('description', apply_filters('mmp_popup', $l10n->__($marker->popup, "Marker (ID {$marker->id}) popup"), $marker), $item);
			$item->addChild('author', $marker->created_by);
			$where = $item->addChild('georss:where', null, 'http://www.georss.org/georss');
			$point = $where->addChild('gml:Point', null, 'http://www.opengis.net/gml');
			$pos = $point->addChild('gml:pos', $marker->lat . ' ' . $marker->lng, 'http://www.opengis.net/gml');
		}

		return $georss;
	}

	/**
	 * Converts marker data into the Atom format
	 *
	 * @since 4.0
	 *
	 * @param object $map Map object the markers are assigned to
	 * @param array $markers List of marker data to be converted
	 */
	private function atom($map, $markers) {
		$api = MMP::get_instance('MMP\API');
		$l10n = MMP::get_instance('MMP\L10n');

		$atom = new \SimpleXMLElement(
			  '<?xml version="1.0" encoding="UTF-8"?>'
			. '<atom xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss"></atom>'
		);
		$this->add_cdata('title', get_bloginfo('name') . ' - ' . $l10n->__($map->name, "Map (ID {$map->id}) name"), $atom);
		$link = $atom->addChild('link');
		$link->addAttribute('href', $api->link("/fullscreen/{$map->id}/"));
		$atom->addChild('updated', (new \DateTime($map->updated_on, new \DateTimeZone('UTC')))->format('Y-m-d\Th:m:s\Z'));
		$author = $atom->addChild('author');
		$author->addChild('name', $map->created_by);
		$atom->addChild('id', $api->link("/fullscreen/{$map->id}/"));
		foreach ($markers as $marker) {
			$entry = $atom->addChild('entry');
			$this->add_cdata('title', $l10n->__($marker->name, "Marker (ID {$marker->id}) name"), $entry);
			$link = $entry->addChild('link');
			$link->addAttribute('href', $api->link("/fullscreen/{$map->id}/?marker={$marker->id}"));
			$entry->addChild('id', $api->link("/fullscreen/{$map->id}/?marker={$marker->id}"));
			$entry->addChild('updated', (new \DateTime($marker->updated_on, new \DateTimeZone('UTC')))->format('Y-m-d\Th:m:s\Z'));
			$author = $entry->addChild('author');
			$author->addChild('name', $marker->created_by);
			$this->add_cdata('content', apply_filters('mmp_popup', $l10n->__($marker->popup, "Marker (ID {$marker->id}) popup"), $marker), $entry);
			$entry->addChild('georss:point', $marker->lat . ' ' . $marker->lng, 'http://www.georss.org/georss');
		}

		return $atom;
	}

	/**
	 * Adds a CDATA child to a SimpleXMLElement parent
	 *
	 * @since 4.3
	 *
	 * @param string $name Name of the child
	 * @param string $value CDATA value
	 * @param object $parent Parent node
	 */
	private function add_cdata($name, $value, &$parent) {
		$child = $parent->addChild($name);
		$child_node = dom_import_simplexml($child);
		$child_owner = $child_node->ownerDocument;
		$child_node->appendChild($child_owner->createCDATASection($value));

		return $child;
	}

	/**
	 * Formats color and opacity for use with KML
	 *
	 * @since 4.15
	 *
	 * @param string $color Color
	 * @param float $opacity Opacity
	 */
	private function format_color_opacity_kml($color, $opacity) {
		$rgb = str_split(strtolower(substr($color, 1)), 2);
		$a = substr('0' . dechex($opacity * 255), -2);

		return $a . $rgb[2] . $rgb[1] . $rgb[0];
	}
}
