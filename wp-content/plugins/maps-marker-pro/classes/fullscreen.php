<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Fullscreen {
	/**
	 * Replaces the page title with the map name
	 *
	 * @since 4.0
	 *
	 * @param string $title Current page title
	 */
	public function filter_title($title) {
		$db = MMP::get_instance('MMP\DB');

		$id = absint(get_query_var('map'));
		if ($id === 0) {
			return $title;
		}
		$map = $db->get_map($id);
		if (!$map) {
			return $title;
		}

		return esc_html($map->name);
	}

	/**
	 * Sets the correct canonical link
	 *
	 * @since 4.10
	 *
	 * @param string $canonical Current canonical link
	 */
	public function filter_canonical($canonical) {
		$api = MMP::get_instance('MMP\API');

		$id = absint(get_query_var('map'));
		if ($id === 0) {
			return $canonical;
		}

		return $api->link("/fullscreen/{$id}/");
	}

	/**
	 * Shows the fullscreen map
	 *
	 * @since 4.0
	 */
	public function show() {
		add_filter('pre_get_document_title', array($this, 'filter_title'));
		add_filter('wpseo_title', array($this, 'filter_title'));
		add_filter('wpseo_canonical', array($this, 'filter_canonical'));
		add_filter('wpseo_prev_rel_link', '__return_false');
		add_filter('wpseo_next_rel_link', '__return_false');

		$id = absint(get_query_var('map'));

		?>
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
			<style>
				.maps-marker-pro {
					width: 100% !important;
					min-height: 100vh !important;
				}
				.maps-marker-pro .mmp-map {
					flex: auto !important;
				}
			</style>
			<?php wp_head(); ?>
		</head>
		<body>
			<?= do_shortcode('[' . MMP::$settings['shortcode'] . ' map="' . $id . '" gestureHandling="false"]'); ?>
			<?php wp_footer(); ?>
		</body>
		</html>
		<?php
	}
}
