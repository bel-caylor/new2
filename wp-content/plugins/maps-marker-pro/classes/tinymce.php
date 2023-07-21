<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class TinyMCE {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		if (!MMP::$settings['tinyMce'] || !is_admin() || (isset($_GET['page']) && $_GET['page'] === 'mapsmarkerpro_marker')) {
			return;
		}

		// Don't load button in Gravity Forms if no-conflict mode is active
		if (isset($_GET['page']) && $_GET['page'] === 'gf_edit_forms' && get_option('gform_enable_noconflict') === '1') {
			return;
		}

		add_action('wp_enqueue_media', array($this, 'load_resources'));
		add_action('media_buttons', array($this, 'add_button'));
		add_action('wp_ajax_mmp_get_shortcode_list', array($this, 'get_shortcode_list'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 */
	public function load_resources() {
		wp_enqueue_style('mmp-shortcode');
		wp_enqueue_script('mmp-shortcode');
	}

	/**
	 * Adds the shortcode button above the editor
	 *
	 * @since 4.0
	 *
	 * @param string $editor_id HTML ID of the editor that executed the hook
	 */
	public function add_button($editor_id) {
		add_action('admin_footer', array($this, 'add_modal'));

		?><button type="button" id="mmp-shortcode-button" class="button button-secondary"><?= esc_html__('Add Map', 'mmp') ?></button><?php
	}

	/**
	 * AJAX request for getting a list of shortcodes
	 *
	 * @since 4.7
	 */
	public function get_shortcode_list() {
		$db = MMP::get_instance('MMP\DB');

		check_ajax_referer('mmp-shortcode-modal', 'nonce');

		$filters = array(
			'orderby'   => 'id',
			'sortorder' => 'desc'
		);
		if (isset($_POST['search']) && $_POST['search']) {
			$filters['name'] = $_POST['search'];
		}
		$maps = $db->get_all_maps(false, $filters);
		$shortcodes = array();
		foreach ($maps as $map) {
			$shortcodes[] = array(
				'id'   => $map->id,
				'name' => "[ID {$map->id}] " . (($map->name) ? esc_html($map->name) : esc_html__('(no name)', 'mmp'))
			);
		}

		if (count($shortcodes)) {
			wp_send_json_success($shortcodes);
		} else {
			wp_send_json_error(esc_html__('No results', 'mmp'));
		}
	}

	/**
	 * Adds the shortcode modal to the admin page
	 *
	 * @since 4.11
	 */
	public function add_modal() {
		?>
		<div id="mmp-shortcode-modal" class="mmp-shortcode-modal">
			<input type="hidden" id="mmp-shortcode-nonce" value="<?= wp_create_nonce('mmp-shortcode-modal') ?>" />
			<input type="hidden" id="mmp-shortcode-string" value="<?= MMP::$settings['shortcode'] ?>" />
			<div class="mmp-shortcode-modal-content">
				<span class="mmp-shortcode-modal-close">&times;</span>
				<div class="mmp-shortcode-modal-header">
					<p class="mmp-shortcode-modal-title"><?= esc_html__('Insert map', 'mmp') ?></p>
				</div>
				<div class="mmp-shortcode-modal-body">
					<input type="text" id="mmp-shortcode-search" placeholder="<?= esc_attr__('Search', 'mmp') ?>" />
					<?php if (current_user_can('mmp_add_maps')): ?>
						<a id="mmp-shortcode-add-map" class="button button-secondary" href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_map') ?>" target="_blank">
							<?= esc_html__('Add new map', 'mmp') ?>
						</a>
					<?php endif; ?>
					<p><?= esc_html__('Please select the map you would like to add.', 'mmp') ?></p>
					<div id="mmp-shortcode-list-container">
						<ul id="mmp-shortcode-list"></ul>
					</div>
				</div>
				<div class="mmp-shortcode-modal-footer">
					<button id="mmp-shortcode-insert" class="button button-primary"><?= esc_html__('Insert map', 'mmp') ?></button>
				</div>
			</div>
		</div>
		<?php
	}
}
