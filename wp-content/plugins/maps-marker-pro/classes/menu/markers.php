<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class Markers extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_filter('screen_settings', array($this, 'screen_settings'), 10, 2);

		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_marker_list', array($this, 'marker_list'));
		add_action('wp_ajax_mmp_delete_marker', array($this, 'delete_marker'));
		add_action('wp_ajax_mmp_bulk_action_markers', array($this, 'bulk_action_markers'));
		add_action('wp_ajax_mmp_markers_screen_options', array($this, 'mmp_markers_screen_options'));
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

		if ($screen->id !== 'maps-marker-pro_page_mapsmarkerpro_markers') {
			return $screen_settings;
		}

		$options = get_user_meta(get_current_user_id(), 'mapsmarkerpro_markers_options', true);
		$options = $mmp_settings->validate_markers_screen_settings($options);

		ob_start();
		?>
		<fieldset id="mmp-markers-columns">
			<legend><?= esc_html__('Columns', 'mmp') ?></legend>
			<label><input type="checkbox" class="mmp-markers-column" value="address" <?= $this->checked(!in_array('address', $options['hiddenColumns'])) ?>/><?= esc_html__('Location', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="popup" <?= $this->checked(!in_array('popup', $options['hiddenColumns'])) ?>/><?= esc_html__('Popup', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="created_by" <?= $this->checked(!in_array('created_by', $options['hiddenColumns'])) ?>/><?= esc_html__('Created by', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="created_on" <?= $this->checked(!in_array('created_on', $options['hiddenColumns'])) ?>/><?= esc_html__('Created on', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="updated_by" <?= $this->checked(!in_array('updated_by', $options['hiddenColumns'])) ?>/><?= esc_html__('Updated by', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="updated_on" <?= $this->checked(!in_array('updated_on', $options['hiddenColumns'])) ?>/><?= esc_html__('Updated on', 'mmp') ?></label>
			<label><input type="checkbox" class="mmp-markers-column" value="assigned_to" <?= $this->checked(!in_array('assigned_to', $options['hiddenColumns'])) ?>/><?= esc_html__('Assigned to map', 'mmp') ?></label>
		</fieldset>
		<fieldset id="mmp-markers-pagination">
			<legend><?= esc_html__('Pagination', 'mmp') ?></legend>
			<label for="mmp-markers-per-page"><?= esc_html__('Markers per page', 'mmp') ?>:</label>
			<input type="number" id="mmp-markers-per-page" step="1" min="1" max="1000" value="<?= $options['perPage'] ?>" />
		</fieldset>
		<p><input type="button" id="mmp-markers-screen-options-apply" class="button button-primary" value="<?= esc_html__('Apply', 'mmp') ?>" /></p>
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
		if (substr($hook, -strlen('mapsmarkerpro_markers')) !== 'mapsmarkerpro_markers') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'listMarkersActions();');
	}

	/**
	 * Renders the HTML for the current range of the marker list
	 *
	 * @since 4.0
	 */
	public function marker_list() {
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('mmp-marker-list', 'nonce');

		if (!current_user_can('mmp_view_markers')) {
			wp_send_json_error();
		}

		$current_user = wp_get_current_user();

		$options = get_user_meta(get_current_user_id(), 'mapsmarkerpro_markers_options', true);
		$options = $mmp_settings->validate_markers_screen_settings($options);

		$page = isset($_POST['page']) && absint($_POST['page']) ? absint($_POST['page']) : 1;
		$limit = $options['perPage'];
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		$map_filter = isset($_POST['map']) ? intval($_POST['map']) : 0;
		$sort = isset($_POST['sort']) ? $_POST['sort'] : 'id';
		$order = isset($_POST['order']) && $_POST['order'] === 'desc' ? 'desc' : 'asc';

		$total = $db->count_markers(array(
			'include_maps' => $map_filter,
			'contains'     => $search
		));

		$page = ($page > ceil($total / $limit)) ? ceil($total / $limit) : $page;

		$markers = $db->get_all_markers(array(
			'include_maps' => $map_filter,
			'contains'     => $search,
			'limit'        => $limit,
			'sortorder'    => $order,
			'orderby'      => $sort,
			'offset'       => ($page - 1) * $limit
		));

		ob_start();
		?>
		<tr>
			<th><input type="checkbox" id="selectAll" name="selectAll" /></th>
			<th><a href="" class="mmp-sortable" data-orderby="id" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('ID', 'mmp') ?></a></th>
			<th><a href="" class="mmp-sortable" data-orderby="icon" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Icon', 'mmp') ?></a></th>
			<th><a href="" class="mmp-sortable" data-orderby="name" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Name', 'mmp') ?></a></th>
			<?php if (!in_array('address', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="address" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Location', 'mmp') ?></a></th>
			<?php endif; ?>
			<?php if (!in_array('popup', $options['hiddenColumns'])): ?>
				<th><a href="" class="mmp-sortable" data-orderby="popup" title="<?= esc_html__('click to sort', 'mmp') ?>"><?= esc_html__('Popup', 'mmp') ?></a></th>
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
			<?php if (!in_array('assigned_to', $options['hiddenColumns'])): ?>
				<th><?= esc_html__('Assigned to map', 'mmp') ?></th>
			<?php endif; ?>
		</tr>
		<?php if (!count($markers)): ?>
			<tr><td class="mmp-no-results" colspan="7"><?= esc_html__('No results') ?></td></tr>
		<?php else: ?>
			<?php foreach ($markers as $marker): ?>
				<tr>
					<td><input type="checkbox" name="selected[]" value="<?= $marker->id ?>" /></td>
					<td><?= $marker->id ?></td>
					<td><img src="<?= ($marker->icon) ? MMP::$icons_url . $marker->icon : plugins_url('images/leaflet/marker.png', MMP::$path) ?>" /></td>
					<td>
						<?php if (($marker->schedule_from && $marker->schedule_from !== '0000-00-00 00:00:00') || ($marker->schedule_until && $marker->schedule_until !== '0000-00-00 00:00:00')): ?>
							<?php
								if ($marker->schedule_from && !$marker->schedule_until) {
									$title = sprintf(esc_html__('Visible from %1$s', 'mmp'), $l10n->date('datetime', $marker->schedule_from));
								} else if (!$marker->schedule_from && $marker->schedule_until) {
									$title = sprintf(esc_html__('Visible until %1$s', 'mmp'), $l10n->date('datetime', $marker->schedule_until));
								} else {
									$title = sprintf(esc_html__('Visible between %1$s and %2$s', 'mmp'), $l10n->date('datetime', $marker->schedule_from), $l10n->date('datetime', $marker->schedule_until));
								}
							?>
							<i class="dashicons dashicons-calendar-alt" title="<?= $title ?>"></i>
						<?php endif; ?>
						<?php if ($marker->created_by_id == $current_user->ID || current_user_can('mmp_edit_other_markers')): ?>
							<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_marker&id=' . $marker->id) ?>" title="<?= esc_html__('Edit marker', 'mmp') ?>"><?= ($marker->name) ? esc_html($marker->name) : esc_html__('(no name)', 'mmp') ?></a>
						<?php else: ?>
							<?= ($marker->name) ? esc_html($marker->name) : esc_html__('(no name)', 'mmp') ?>
						<?php endif; ?>
						<div class="mmp-action">
							<ul>
								<?php if ($marker->created_by_id == $current_user->ID || current_user_can('mmp_edit_other_markers')): ?>
									<li><a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_marker&id=' . $marker->id) ?>" title="<?= esc_html__('Edit marker', 'mmp') ?>"><?= esc_html__('Edit', 'mmp') ?></a></li>
								<?php endif; ?>
								<?php if ($marker->created_by_id == $current_user->ID || current_user_can('mmp_delete_other_markers')): ?>
									<li><span class="mmp-delete" href="" data-id="<?= $marker->id ?>" title="<?= esc_html__('Delete marker', 'mmp') ?>"><?= esc_html__('Delete', 'mmp') ?></span></li>
								<?php endif; ?>
								<li>
									<?php if ($l10n->check_ml() === 'wpml'): ?>
										<a href="<?= get_admin_url(null, 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Maps+Marker+Pro') ?>" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php elseif ($l10n->check_ml() === 'pll'): ?>
										<a href="<?= get_admin_url(null, 'admin.php?page=mlang_strings&s=Marker+%28ID+' . $marker->id . '%29&group=Maps+Marker+Pro') ?>" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php else: ?>
										<a href="https://www.mapsmarker.com/multilingual/" target="_blank"><?= esc_html__('Translate', 'mmp') ?></a>
									<?php endif; ?>
								</li>
							</ul>
						</div>
					</td>
					<?php if (!in_array('address', $options['hiddenColumns'])): ?>
						<td><?= esc_html($marker->address) ?></td>
					<?php endif; ?>
					<?php if (!in_array('popup', $options['hiddenColumns'])): ?>
						<td><?= wp_strip_all_tags($marker->popup) ?></td>
					<?php endif; ?>
					<?php if (!in_array('created_by', $options['hiddenColumns'])): ?>
						<td><?= esc_html($marker->created_by) ?></td>
					<?php endif; ?>
					<?php if (!in_array('created_on', $options['hiddenColumns'])): ?>
						<td><abbr class="mmp-datetime" title="<?= $l10n->date('datetime', $marker->created_on) ?>"><?= $l10n->date('date', $marker->created_on) ?></abbr></td>
					<?php endif; ?>
					<?php if (!in_array('updated_by', $options['hiddenColumns'])): ?>
						<td><?= esc_html($marker->updated_by) ?></td>
					<?php endif; ?>
					<?php if (!in_array('updated_on', $options['hiddenColumns'])): ?>
						<td><abbr class="mmp-datetime" title="<?= $l10n->date('datetime', $marker->updated_on) ?>"><?= $l10n->date('date', $marker->updated_on) ?></abbr></td>
					<?php endif; ?>
					<?php if (!in_array('assigned_to', $options['hiddenColumns'])): ?>
						<td>
							<?php if ($marker->maps): ?>
								<?php $map_details = $db->get_maps($marker->maps) ?>
								<?php if (count($map_details)): ?>
									<ul class="mmp-used-in">
										<?php foreach ($map_details as $map_detail): ?>
											<li>
												<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_map&id=' . $map_detail->id) ?>" title="<?= esc_attr__('Edit map', 'mmp') ?>"><?= esc_html($map_detail->name) ?> (<?= esc_html__('ID', 'mmp') ?> <?= $map_detail->id ?>)</a>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php else: ?>
									<?= esc_html__('Not assigned to any map', 'mmp') ?>
								<?php endif; ?>
							<?php endif; ?>
						</td>
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
			'map'    => $map_filter,
			'sort'   => $sort,
			'order'  => $order
		));
	}

	/**
	 * Deletes the marker
	 *
	 * @since 4.0
	 */
	public function delete_marker() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-marker-list', 'nonce');

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

	public function bulk_action_markers() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-bulk-action-marker', 'nonce');

		if (!current_user_can('mmp_use_tools')) {
			wp_send_json_error();
		}

		parse_str($_POST['data'], $data);
		if ($data['bulkAction'] === 'duplicate') {
			$add = array();
			$markers = $db->get_markers($data['selected'], ARRAY_A);
			foreach ($markers as $marker) {
				$marker['id'] = 0;
				$add[] = $marker;
			}
			$db->add_markers($add);
		} else if ($data['bulkAction'] === 'delete') {
			$db->delete_markers($data['selected']);
		} else if ($data['bulkAction'] === 'assign') {
			$db->assign_markers($data['assignTarget'], $data['selected']);
		}

		wp_send_json_success();
	}

	/**
	 * AJAX request for saving the screen options
	 *
	 * @since 4.8
	 */
	public function mmp_markers_screen_options() {
		$mmp_settings = MMP::get_instance('MMP\Settings');

		check_ajax_referer('screen-options-nonce', 'nonce');

		if (!current_user_can('mmp_view_markers')) {
			wp_send_json_error();
		}

		// Workaround for jQuery not sending empty arrays
		if (!isset($_POST['hiddenColumns'])) {
			$_POST['hiddenColumns'] = array();
		}

		$options = $mmp_settings->validate_markers_screen_settings($_POST, false, false);
		update_user_meta(get_current_user_id(), 'mapsmarkerpro_markers_options', $options);

		wp_send_json_success();
	}

	/**
	 * Shows the markers page
	 *
	 * @since 4.0
	 */
	protected function show() {
		$db = MMP::get_instance('MMP\DB');

		$maps = $db->get_all_maps();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?= esc_html__('List all markers', 'mmp') ?></h1>
			<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_marker') ?>" class="page-title-action"><?= esc_html__('Add new marker', 'mmp') ?></a>
			<form id="markerList" method="POST">
				<input type="hidden" id="markerListNonce" name="markerListNonce" value="<?= wp_create_nonce('mmp-marker-list') ?>" />
				<div id="pagination_top" class="mmp-pagination mmp-pagination-markers">
					<div>
						<?= esc_html__('Total markers:', 'mmp') ?> <span id="markercount_top">0</span>
					</div>
					<div>
						<button type="button" id="first_top" value="1"><span>&laquo;</span></button>
						<button type="button" id="previous_top" value="1"><span>&lsaquo;</span></button>
						<button type="button" id="next_top" value="1"><span>&rsaquo;</span></button>
						<button type="button" id="last_top" value="1"><span>&raquo;</span></button>
					</div>
					<div>
						<?= esc_html__('Page', 'mmp') ?> <input type="text" id="page_top" value="1" /> <?= esc_html__('of', 'mmp') ?> <span id="pagecount_top">1</span>
					</div>
					<div>
						<input type="text" id="search_top" class="mmp-search" placeholder="<?= esc_html__('Search markers', 'mmp') ?>" />
					</div>
					<div>
						<select id="map_top" name="map_top">
							<option value="0"><?= esc_html__('All maps', 'mmp') ?></option>
							<option value="-1"><?= esc_html__('Not assigned to any map', 'mmp') ?></option>
							<?php foreach ($maps as $map): ?>
								<option value="<?= $map->id ?>">[<?= $map->id ?>] <?= esc_html($map->name) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<input type="hidden" id="sortorder" value="asc" />
					<input type="hidden" id="orderby" value="id" />
				</div>
				<table id="marker_list" class="mmp-table mmp-markers"></table>
				<div id="pagination_bottom" class="mmp-pagination mmp-pagination-markers">
					<div>
						<?= esc_html__('Total markers:', 'mmp') ?> <span id="markercount_bottom">0</span>
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
						<input type="text" id="search_bottom" class="mmp-search" placeholder="<?= esc_html__('Search markers', 'mmp') ?>" />
					</div>
					<div>
						<select id="map_bottom" name="map_bottom">
							<option value="0"><?= esc_html__('All maps', 'mmp') ?></option>
							<option value="-1"><?= esc_html__('Not assigned to any map', 'mmp') ?></option>
							<?php foreach ($maps as $map): ?>
								<option value="<?= $map->id ?>">[<?= $map->id ?>] <?= esc_html($map->name) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<?php if (current_user_can('mmp_use_tools')): ?>
					<div class="mmp-bulk">
						<input type="hidden" id="bulkNonce" name="bulkNonce" value="<?= wp_create_nonce('mmp-bulk-action-marker') ?>" />
						<ul>
							<li>
								<input type="radio" id="duplicate" name="bulkAction" value="duplicate" />
								<label for="duplicate"><?= esc_html__('Duplicate markers', 'mmp') ?></label>
							</li>
							<li>
								<input type="radio" id="delete" name="bulkAction" value="delete" />
								<label for="delete"><?= esc_html__('Delete markers', 'mmp') ?></label>
							</li>
							<li>
								<input type="radio" id="assign" name="bulkAction" value="assign" />
								<label for="assign"><?= esc_html__('Assign markers to this map', 'mmp') ?></label>
								<select id="assignTarget" name="assignTarget">
									<?php foreach ($maps as $map): ?>
										<option value="<?= $map->id ?>"><?= "[{$map->id}] " . esc_html($map->name) ?></option>
									<?php endforeach; ?>
								</select>
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
