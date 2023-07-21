<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Maps extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_filter('screen_settings', array($this, 'screen_settings'), 10, 2);

		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_map_list', array($this, 'map_list'));
		add_action('wp_ajax_mmp_delete_map', array($this, 'delete_map'));
		add_action('wp_ajax_mmp_bulk_action_maps', array($this, 'bulk_action_maps'));
		add_action('wp_ajax_mmp_maps_screen_options', array($this, 'mmp_maps_screen_options'));
	}

	/**
	 * Adds the screen options to the page
	 *
	 * @since 4.8
	 *
	 * @param string $screen_settings Current screen options
	 * @param WP_Screen $screen Current screen object instance
	 */
	public function screen_settings($screen_settings, $screen) {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		if ($screen->id !== 'toplevel_page_mapsmarkerpro_maps') {
			return $screen_settings;
		}

		$options = get_user_meta(get_current_user_id(), 'mapsmarkerpro_maps_options', true);
		$options = $mmp_settings->validate_maps_screen_settings($options);

		ob_start();
		?>
		<fieldset id="mmp-maps-columns">
			<legend><?= esc_html__('Columns', 'mmp') ?></legend>
			<label><input type="checkbox" class="mmp-maps-column" value="markers" <?= $this->checked(!in_array('markers', $options['hiddenColumns'])) ?>/><?= esc_html__('Markers', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="created_by" <?= $this->checked(!in_array('created_by', $options['hiddenColumns'])) ?>/><?= esc_html__('Created by', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="created_on" <?= $this->checked(!in_array('created_on', $options['hiddenColumns'])) ?>/><?= esc_html__('Created on', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="updated_by" <?= $this->checked(!in_array('updated_by', $options['hiddenColumns'])) ?>/><?= esc_html__('Updated by', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="updated_on" <?= $this->checked(!in_array('updated_on', $options['hiddenColumns'])) ?>/><?= esc_html__('Updated on', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="used_in" <?= $this->checked(!in_array('used_in', $options['hiddenColumns'])) ?>/><?= esc_html__('Used in content', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-maps-column" value="shortcode" <?= $this->checked(!in_array('shortcode', $options['hiddenColumns'])) ?>/><?= esc_html__('Shortcode', 'mmp') ?></label>
		</fieldset>
		<fieldset id="mmp-maps-pagination">
			<legend><?= esc_html__('Pagination', 'mmp') ?></legend>
			<label for="mmp-maps-per-page"><?= esc_html__('Maps per page', 'mmp') ?>:</label>
			<input type="number" id="mmp-maps-per-page" step="1" min="1" max="1000" value="<?= $options['perPage'] ?>" />
		</fieldset>
		<p><input type="button" id="mmp-maps-screen-options-apply" class="button button-primary" value="<?= esc_html__('Apply', 'mmp') ?>" /></p>
		<?php
		$screen_settings .= ob_get_clean();

		return $screen_settings;
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook The current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_maps')) !== 'mapsmarkerpro_maps') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'listMapsActions();');
	}

	/**
	 * Renders the HTML for the current range of the map list
	 *
	 * @since 4.0
	 */
	public function map_list() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');
		$api = MMP::get_instance('MMP\API');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-map-list', 'nonce');

		if (!current_user_can('mmp_view_maps')) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();

		$options = get_user_meta($current_user->ID, 'mapsmarkerpro_maps_options', true);
		$options = $mmp_settings->validate_maps_screen_settings($options);

		$page = isset($_POST['page']) && absint($_POST['page']) ? absint($_POST['page']) : 1;
		$limit = $options['perPage'];
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		$sort = isset($_POST['sort']) ? $_POST['sort'] : 'id';
		$order = isset($_POST['order']) && $_POST['order'] === 'desc' ? 'desc' : 'asc';

		$total = $db->count_maps(array(
			'name' => $search
		));

		$page = ($page > ceil($total / $limit)) ? ceil($total / $limit) : $page;

		$maps = $db->get_all_maps(true, array(
			'name'      => $search,
			'sortorder' => $order,
			'orderby'   => $sort,
			'limit'     => $limit,
			'offset'    => ($page - 1) * $limit
		));

		$shortcodes = array();
		foreach ($maps as $key => $map) {
			$shortcodes[$map->id] = $db->get_map_shortcodes($map->id);
			$maps[$key]->settings = json_decode($map->settings);
		}

		ob_start();
		?>
		<tr>
			<th><input type="checkbox" id="selectAll" name="selectAll" /></th>
			<th><a href="" class="mmp-sortable" data-orderby="id" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('ID', 'mmp') ?></a></th>
			<th><a href="" class="mmp-sortable" data-orderby="name" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Name', 'mmp') ?></a></th>
			<?php if (!in_array('markers', $options['hiddenColumns'])): ?>
				<th><?= esc_html__('Markers', 'mmp') ?></th>
			<?php endif; ?>
			<?php if (!in_array('created_by', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="created_by" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Created by', 'mmp') ?></a></th>
			<?php endif; ?>
			<?php if (!in_array('created_on', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="created_on" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Created on', 'mmp') ?></a></th>
			<?php endif; ?>
			<?php if (!in_array('updated_by', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="updated_by" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Updated by', 'mmp') ?></a></th>
			<?php endif; ?>
			<?php if (!in_array('updated_on', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="updated_on" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Updated on', 'mmp') ?></a></th>
			<?php endif; ?>
			<?php if (!in_array('used_in', $options['hiddenColumns'])): ?>
				<th><?= esc_html__('Used in content', 'mmp') ?></th>
			<?php endif; ?>
			<?php if (!in_array('shortcode', $options['hiddenColumns'])): ?>
				<th><?= esc_html__('Shortcode', 'mmp') ?></th>
			<?php endif; ?>
		</tr>
		<?php if (!count($maps)): ?>
			<tr><td class="mmp-no-results" colspan="7"><?= esc_html__('No results') ?></td></tr>
		<?php else: ?>
			<?php foreach ($maps as $map): ?>
				<tr>
					<td><input type="checkbox" name="selected[]" value="<?= $map->id ?>" /></td>
					<td><?= $map->id ?></td>
					<td>
						<?php if ($map->created_by_id == $current_user->ID || current_user_can('mmp_edit_other_maps')): ?>
							<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_map&id=' . $map->id) ?>" title="<?= esc_html__('Edit map', 'mmp') ?>"><?= ($map->name) ? esc_html($map->name) : esc_html__('(no name)', 'mmp') ?></a>
						<?php else: ?>
							<?= ($map->name) ? esc_html($map->name) : esc_html__('(no name)', 'mmp') ?>
						<?php endif; ?>
						<div class="mmp-action">
							<ul>
								<?php if ($map->created_by_id == $current_user->ID || current_user_can('mmp_edit_other_maps')): ?>
									<li><a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_map&id=' . $map->id) ?>" title="<?= esc_html__('Edit map', 'mmp') ?>"><?= esc_html__('Edit', 'mmp') ?></a></li>
								<?php endif; ?>
								<?php if ($map->created_by_id == $current_user->ID || current_user_can('mmp_delete_other_maps')): ?>
									<li><span class="mmp-delete" href="" data-id="<?= $map->id ?>" title="<?= esc_html__('Delete map', 'mmp') ?>"><?= esc_html__('Delete', 'mmp') ?></span></li>
								<?php endif; ?>
								<li>
									<?php if ($l10n->check_ml() === 'wpml'): ?>
										<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro&search=' . urlencode($map->name)) ?>" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php elseif ($l10n->check_ml() === 'pll'): ?>
										<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Map+%28ID+' . $map->id . '%29&group=Maps+Marker+Pro') ?>" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php else: ?>
										<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php endif; ?>
								</li>
								<li><a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_marker&basemap=' . $map->settings->basemapDefault . '&lat=' . $map->settings->lat . '&lng=' . $map->settings->lng . '&zoom=' . $map->settings->zoom . '&map=' . $map->id) ?>" target="_blank"><?= esc_html__('Add marker', 'mmp') ?></a></li>
								<?php if (MMP::$settings['apiFullscreen']): ?>
									<li><a href="<?= $api->link("/fullscreen/{$map->id}/") ?>" target="_blank" title="<?= esc_attr__('Open standalone map in fullscreen mode', 'mmp') ?>"><img src="<?= plugins_url('images/icons/fullscreen.png', MMP::$path) ?>" /></a></li>
									<li><a class="mmp-qrcode-link" href="" data-id="<?= $map->id ?>" data-url="<?= $api->link("/fullscreen/{$map->id}/") ?>" title="<?= esc_attr__('Show QR code for fullscreen map', 'mmp') ?>"><img src="<?= plugins_url('images/icons/qr-code.png', MMP::$path) ?>" /></a></li>
								<?php endif; ?>
								<?php if (MMP::$settings['apiExport']): ?>
									<li><a href="<?= $api->link("/export/geojson/{$map->id}/") ?>" target="_blank" title="<?= esc_attr__('Export as GeoJSON', 'mmp') ?>"><img src="<?= plugins_url('images/icons/geojson.png', MMP::$path) ?>" /></a></li>
									<li><a href="<?= $api->link("/export/kml/{$map->id}/") ?>" target="_blank" title="<?= esc_attr__('Export as KML', 'mmp') ?>"><img src="<?= plugins_url('images/icons/kml.png', MMP::$path) ?>" /></a></li>
									<li><a href="<?= $api->link("/export/georss/{$map->id}/") ?>" target="_blank" title="<?= esc_attr__('Export as GeoRSS', 'mmp') ?>"><img src="<?= plugins_url('images/icons/georss.png', MMP::$path) ?>" /></a></li>
								<?php endif; ?>
							</ul>
							<div id="qrcode_<?= $map->id ?>" class="mmp-qrcode"></div>
						</div>
					</td>
					<?php if (!in_array('markers', $options['hiddenColumns'])): ?>
						<td>
							<?php if ($map->settings->filtersAllMarkers): ?>
								<?= esc_html__('All', 'mmp') ?> (<?= $map->markers ?>)
							<?php else: ?>
								<?= $map->markers ?>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<?php if (!in_array('created_by', $options['hiddenColumns'])): ?>
						<td><?= esc_html($map->created_by) ?></td>
					<?php endif; ?>
					<?php if (!in_array('created_on', $options['hiddenColumns'])): ?>
						<td><abbr class="mmp-datetime" title="<?= $l10n->date('datetime', $map->created_on) ?>"><?= $l10n->date('date', $map->created_on) ?></abbr></td>
					<?php endif; ?>
					<?php if (!in_array('updated_by', $options['hiddenColumns'])): ?>
						<td><?= esc_html($map->updated_by) ?></td>
					<?php endif; ?>
					<?php if (!in_array('updated_on', $options['hiddenColumns'])): ?>
						<td><abbr class="mmp-datetime" title="<?= $l10n->date('datetime', $map->updated_on) ?>"><?= $l10n->date('date', $map->updated_on) ?></abbr></td>
					<?php endif; ?>
					<?php if (!in_array('used_in', $options['hiddenColumns'])): ?>
						<td>
							<?php if ($shortcodes[$map->id]): ?>
								<ul class="mmp-used-in">
									<?php foreach ($shortcodes[$map->id] as $shortcode): ?>
										<li>
											<a href="<?= $shortcode['edit'] ?>" title="<?= esc_attr__('Edit post', 'mmp') ?>"><img src="<?= plugins_url('images/icons/edit-layer.png', MMP::$path) ?>" /></a>
											<a href="<?= $shortcode['link'] ?>" title="<?= esc_attr__('View post', 'mmp') ?>"><?= $shortcode['title'] ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php else: ?>
								<?= esc_html__('Not used in any content', 'mmp') ?>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<?php if (!in_array('shortcode', $options['hiddenColumns'])): ?>
						<td><input class="mmp-shortcode" type="text" value="[<?= MMP::$settings['shortcode'] ?> map=&quot;<?= $map->id ?>&quot;]" readonly="readonly" /></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
		$rows = ob_get_clean();

		wp_send_json_success(array(
			'html'   => $rows,
			'total'  => $total,
			'page'   => $page,
			'limit'  => $limit,
			'search' => $search,
			'sort'   => $sort,
			'order'  => $order
		));
	}

	/**
	 * Deletes the map
	 *
	 * @since 4.0
	 */
	public function delete_map() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-map-list', 'nonce');

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
	 * Executes the map bulk actions
	 *
	 * @since 4.0
	 */
	public function bulk_action_maps() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-bulk-action-map', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		parse_str($_POST['data'], $data);
		if ($data['bulkAction'] === 'duplicate') {
			$add = array();
			$maps = $db->get_maps($data['selected'], false, ARRAY_A);
			foreach ($maps as $map) {
				$map['id'] = 0;
				$add[] = $map;
			}
			$db->add_maps($add);
		} else if ($data['bulkAction'] === 'duplicate-assign') {
			$maps = $db->get_maps($data['selected']);
			foreach ($maps as $map) {
				$assign = array();
				$id = $db->add_map($map);
				$markers = $db->get_map_markers($map->id);
				foreach ($markers as $marker) {
					$assign[] = $marker->id;
				}
				$db->assign_markers($id, $assign);
			}
		} else if ($data['bulkAction'] === 'delete') {
			$delete = array();
			$maps = $db->get_maps($data['selected']);
			foreach ($maps as $map) {
				$delete[] = $map->id;
			}
			$db->delete_maps($delete);
		} else if ($data['bulkAction'] === 'delete-assign') {
			$maps = $db->get_maps($data['selected']);
			foreach ($maps as $map) {
				$assign = array();
				$markers = $db->get_map_markers($map->id);
				foreach ($markers as $marker) {
					$assign[] = $marker->id;
				}
				$db->assign_markers($data['assignTarget'], $assign);
				$db->delete_map($map->id);
			}
		} else if ($data['bulkAction'] === 'delete-delete') {
			$maps = $db->get_maps($data['selected']);
			foreach ($maps as $map) {
				$delete = array();
				$markers = $db->get_map_markers($map->id);
				foreach ($markers as $marker) {
					$delete[] = $marker->id;
				}
				$db->delete_markers($delete);
				$db->delete_map($map->id);
			}
		}

		wp_send_json_success();
	}

	/**
	 * AJAX request for saving the screen options
	 *
	 * @since 4.8
	 */
	public function mmp_maps_screen_options() {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('screen-options-nonce', 'nonce');

		if (!current_user_can('mmp_view_maps')) {
			wp_send_json_error();
		}

		// Workaround for jQuery not sending empty arrays
		if (!isset($_POST['hiddenColumns'])) {
			$_POST['hiddenColumns'] = array();
		}

		$options = $mmp_settings->validate_maps_screen_settings($_POST, false, false);
		update_user_meta(get_current_user_id(), 'mapsmarkerpro_maps_options', $options);

		wp_send_json_success();
	}

	/**
	 * Shows the maps page
	 *
	 * @since 4.0
	 */
	protected function show() {
		$db = MMP::get_instance('MMP\DB');

		$maps = $db->get_all_maps();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?= esc_html__('List all maps', 'mmp') ?></h1>
			<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_map') ?>" class="page-title-action"><?= esc_html__('Add new map', 'mmp') ?></a>
			<form id="mapList" method="POST">
				<input type="hidden" id="mapListNonce" name="mapListNonce" value="<?= wp_create_nonce('mmp-map-list') ?>" />
				<div id="pagination_top" class="mmp-pagination mmp-pagination-maps">
					<div>
						<?= esc_html__('Total maps:', 'mmp') ?> <span id="mapcount_top">0</span>
					</div>
					<div>
						<button type="button" id="first_top" value="1"><span>&laquo;</span></button>
						<button type="button" id="previous_top" value="1"><span>&lsaquo;</span></button>
						<button type="button" id="next_top" value="1"><span>&rsaquo;</span></button>
						<button type="button" id="last_top" value="1"><span>&raquo;</span></i></button>
					</div>
					<div>
						<?= esc_html__('Page', 'mmp') ?> <input type="text" id="page_top" value="1" /> <?= esc_html__('of', 'mmp') ?> <span id="pagecount_top">1</span>
					</div>
					<div>
						<input type="text" id="search_top" class="mmp-search" placeholder="<?= esc_html__('Search maps', 'mmp') ?>" />
					</div>
					<input type="hidden" id="sortorder" value="asc" />
					<input type="hidden" id="orderby" value="id" />
				</div>
				<table id="map_list" class="mmp-table mmp-maps"></table>
				<div id="pagination_bottom" class="mmp-pagination mmp-pagination-maps">
					<div>
						<?= esc_html__('Total maps:', 'mmp') ?> <span id="mapcount_bottom">0</span>
					</div>
					<div>
						<button type="button" id="first_bottom" value="1"><span>&laquo;</span></button>
						<button type="button" id="previous_bottom" value="1"><span>&lsaquo;</span></button>
						<button type="button" id="next_bottom" value="1"><span>&rsaquo;</span></button>
						<button type="button" id="last_bottom" value="1"><span>&raquo;</span></button>
					</div>
					<div>
						<?= esc_html__('Page', 'mmp') ?> <input type="text" id="page_bottom" value="1" /> <?= esc_html__('of', 'mmp') ?> <span id="pagecount_bottom">1</span>
					</div>
					<div>
						<input type="text" id="search_bottom" class="mmp-search" placeholder="<?= esc_html__('Search maps', 'mmp') ?>" />
					</div>
				</div>
				<?php if (current_user_can('mmp_use_tools')): ?>
					<div class="mmp-bulk">
						<input type="hidden" id="bulkNonce" name="bulkNonce" value="<?= wp_create_nonce('mmp-bulk-action-map') ?>" />
						<ul>
							<li>
								<input type="radio" id="duplicate" name="bulkAction" value="duplicate" />
								<label for="duplicate"><?= esc_html__('Duplicate maps', 'mmp') ?></label>
							</li>
							<li>
								<input type="radio" id="duplicateAssign" name="bulkAction" value="duplicate-assign" />
								<label for="duplicateAssign"><?= esc_html__('Duplicate maps and assign the same markers', 'mmp') ?></label>
							</li>
							<li>
								<input type="radio" id="delete" name="bulkAction" value="delete" />
								<label for="delete"><?= esc_html__('Delete maps and unassign markers', 'mmp') ?></label>
							</li>
							<li>
								<input type="radio" id="deleteAssign" name="bulkAction" value="delete-assign" />
								<label for="deleteAssign"><?= esc_html__('Delete maps and assign markers to this map', 'mmp') ?></label>
								<select id="assignTarget" name="assignTarget">
									<?php foreach ($maps as $map): ?>
										<option value="<?= $map->id ?>"><?= "[{$map->id}] " . esc_html($map->name) ?></option>
									<?php endforeach; ?>
								</select>
							</li>
							<li>
								<input type="radio" id="deleteDelete" name="bulkAction" value="delete-delete" />
								<label for="deleteDelete"><?= esc_html__('Delete maps and assigned markers', 'mmp') ?></label>
							</li>
						</ul>
						<button id="bulkActionSubmit" class="button button-primary" disabled="disabled"><?= esc_html__('Submit', 'mmp') ?></button>
					</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}
}
