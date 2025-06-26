<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Notice {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('all_admin_notices', array($this, 'show_notice'));
		add_action('wp_ajax_mmp_dismiss_admin_notice', array($this, 'dismiss_admin_notice'));
	}

	/**
	 * Shows previously addeded admin notices
	 *
	 * @since 4.0
	 */
	public function show_notice() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		if (isset($_GET['page']) && $_GET['page'] === 'mapsmarkerpro_license') {
			return;
		}

		$notices = get_option('mapsmarkerpro_notices');
		if (!is_array($notices) || !count($notices)) {
			return;
		}

		foreach ($notices as $key => $value) {
			$notice = $this->get_notice($value);
			if ($notice === false) {
				unset($notices[$key]);
				update_option('mapsmarkerpro_notices', $notices);
				continue;
			}

			// Styles are inline, because MMP CSS isn't loaded outside of MMP menu pages
			?>
			<div class="notice notice-<?= $notice['level'] ?> is-dismissible mmp-dismissible" data-notice="<?= $value ?>">
				<div style="display: flex;">
					<img style="width: auto; height: 64px; margin: 0.5em 10px 0.5em 0; padding: 2px; vertical-align: middle;" src="<?= plugins_url('images/mmp-logo.svg', MMP::$path) ?>" />
					<p style="align-self: center;"><?= $notice['msg'] ?></p>
				</div>
			</div>
			<?php
		}

		?>
		<script>
			jQuery(function($) {
				$('.mmp-dismissible').on('click', function(e) {
					if (!$(e.target).hasClass('notice-dismiss')) {
						return;
					}

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						context: this,
						data: {
							action: 'mmp_dismiss_admin_notice',
							nonce: '<?= wp_create_nonce('mmp-dismiss-admin-notice') ?>',
							notice: $(this).data('notice')
						}
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Dismisses an admin notice
	 *
	 * @since 4.0
	 */
	public function dismiss_admin_notice() {
		check_ajax_referer('mmp-dismiss-admin-notice', 'nonce');

		if (!isset($_POST['notice'])) {
			wp_send_json_error();
		}

		$this->remove_admin_notice($_POST['notice']);

		wp_send_json_success();
	}

	/**
	 * Adds an admin notice
	 *
	 * @since 4.0
	 *
	 * @param string $notice Admin notice index
	 */
	public function add_admin_notice($notice) {
		$notices = get_option('mapsmarkerpro_notices');
		if (!is_array($notices)) {
			$notices = array();
		}

		$key = array_search($notice, $notices);
		if ($key === false) {
			$notices[] = $notice;
			update_option('mapsmarkerpro_notices', $notices);
		}
	}

	/**
	 * Removes an admin notice
	 *
	 * @since 4.0
	 *
	 * @param string $notice Admin notice index
	 */
	public function remove_admin_notice($notice) {
		$notices = get_option('mapsmarkerpro_notices');
		if (!is_array($notices)) {
			$notices = array();
		}

		$key = array_search($notice, $notices);
		if ($key !== false) {
			unset($notices[$key]);
			update_option('mapsmarkerpro_notices', $notices);
		}
	}

	/**
	 * Retrieves an admin notice
	 *
	 * @since 4.0
	 *
	 * @param string $notice Admin notice index (possible levels are error, warning, success and info)
	 */
	private function get_notice($notice) {
		$l10n = MMP::get_instance('MMP\L10n');

		$notices = array(
			'finish_install' => array(
				'level' => 'info',
				'msg'   => '<a href="' . get_admin_url(null, 'admin.php?page=mapsmarkerpro_license') . '">' . esc_html__('Please click here to finish the installation of Maps Marker Pro.', 'mmp') . '</a>'
			),
			'new_install' => array(
				'level' => 'info',
				'msg'   => esc_html__('Installation finished - you can now start creating maps!', 'mmp') . ' (<a href="https://www.mapsmarker.com/starter-guide/" target="_blank">' . esc_html__('open starter guide', 'mmp') . '</a>)<br />' . sprintf($l10n->kses__('We recommend using OpenStreetMap, but if you also want to use Google Maps, you need to register a <a href="%1$s" target="_blank">Google Maps Javascript API key</a>.', 'mmp'), 'https://www.mapsmarker.com/google-maps-javascript-api/')
			),
			'migration_ok_pro' => array(
				'level' => 'info',
				'msg'   => sprintf(esc_html__('An installation of Maps Marker Pro %1$s or later was detected.', 'mmp'), '3.1.1') . '<br />' . sprintf($l10n->kses__('You can copy your existing maps to this version using the <a href="%1$s">data migration tool</a>.', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#migration'))
			),
			'migration_update_pro' => array(
				'level' => 'info',
				'msg'   => esc_html__('An older installation of Maps Marker Pro was detected.', 'mmp') . '<br />' . sprintf($l10n->kses__('If you want to copy your existing maps to this version, you need to update the old Maps Marker Pro installation to version %1$s or later first. For more information, please see the <a href="%2$s">data migration tool</a>.', 'mmp'), '3.1.1', get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#migration'))
			),
			'migration_ok_free' => array(
				'level' => 'info',
				'msg'   => sprintf(esc_html__('An installation of Leaflet Maps Marker %1$s or later was detected.', 'mmp'), '3.12.7') . '<br />' . sprintf($l10n->kses__('You can copy your existing maps to this version using the <a href="%1$s">data migration tool</a>.', 'mmp'), get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#migration'))
			),
			'migration_update_free' => array(
				'level' => 'info',
				'msg'   => esc_html__('An older installation of Leaflet Maps Marker was detected.', 'mmp') . '<br />' . sprintf($l10n->kses__('If you want to copy your existing maps to this version, you need to update the old Leaflet Maps Marker installation to version %1$s or later first. For more information, please see the <a href="%2$s">data migration tool</a>.', 'mmp'), '3.12.7', get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#migration'))
			),
			'algolia_removed' => array(
				'level' => 'warning',
				'msg'   => sprintf($l10n->kses__('Unfortunately, Algolia has discontinued their geocoding services. In order to enable geocoding again, you need to register an API key for at least one of the other available geocoding providers and activate it as the new default. For more information on how to register and set up geocoding API keys, please <a href="%1$s">click here</a>.', 'mmp'), 'https://www.mapsmarker.com/algolia-places-sunset/')
			)
		);

		return (isset($notices[$notice])) ? $notices[$notice] : false;
	}
}
