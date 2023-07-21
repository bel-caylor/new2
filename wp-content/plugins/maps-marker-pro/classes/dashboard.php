<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Dashboard {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		if (!MMP::$settings['dashboardWidget']) {
			return;
		}

		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
		add_action('wp_ajax_mmp_dashboard_health', array($this, 'get_health'));
		add_action('wp_ajax_mmp_dashboard_blog', array($this, 'get_blog'));
		add_action('wp_dashboard_setup', array($this, 'add_widget'));
		add_action('wp_network_dashboard_setup', array($this, 'add_widget'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook Name of the current admin page
	 */
	public function load_resources($hook) {
		if ($hook !== 'index.php') {
			return;
		}

		wp_enqueue_style('mmp-dashboard');
		wp_enqueue_script('mmp-dashboard');
	}

	/**
	 * Performs a health check
	 *
	 * @since 4.20
	 */
	public function get_health() {
		$debug = MMP::get_instance('MMP\Debug');

		check_ajax_referer('mmp-dashboard', 'nonce');

		if (!current_user_can('activate_plugins')) {
			wp_send_json_error();
		}

		$debug_info = $debug->get_info();
		$ajax_response = json_decode(wp_remote_retrieve_body($debug_info['ajax_response']), true);

		ob_start();
		if (
			!version_compare($debug_info['wp_version'], '4.5', '>=') ||
			!version_compare($debug_info['php_version'], '5.6', '>=') ||
			!$debug_info['wp_rewrite'] ||
			(!isset($ajax_response['success']) || $ajax_response['success'] !== true) ||
			wp_remote_retrieve_response_code($debug_info['api_response']) !== 302
		) {
			?>
			<div class="mmp-dashboard-error">
				<?= esc_html__('There are issues with your installation.', 'mmp') ?><br />
				<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_tools#health') ?>"><?= esc_html__('Please go to the health page for more information.', 'mmp') ?></a>
			</div>
			<?php
		} else {
			?>
			<div>
				<i class="dashicons dashicons-yes"></i> <?= esc_html__('No issues have been found.', 'mmp') ?>
			</div>
			<?php
		}
		$health = ob_get_clean();

		wp_send_json_success($health);
	}

	/**
	 * Retrieves the RSS feed
	 *
	 * @since 4.20
	 */
	public function get_blog() {
		$l10n = MMP::get_instance('MMP\L10n');

		check_ajax_referer('mmp-dashboard', 'nonce');

		if (!current_user_can('activate_plugins')) {
			wp_send_json_error();
		}

		$feed = fetch_feed('https://www.mapsmarker.com/feed?post_type=news');
		if (is_wp_error($feed)) {
			wp_send_json_error(sprintf(esc_html__('Feed could not be retrieved, please try again later or read the latest blog posts at %1$s.', 'mmp'), '<a href="https://www.mapsmarker.com/news/" target="_blank">https://www.mapsmarker.com/news/</a>'));
		}

		ob_start();
		?>
		<ul>
			<?php foreach ($feed->get_items(0, 3) as $item): ?>
				<li><?= $l10n->date('date', $item->get_date('Y-m-d H:i:s')) ?>: <a href="<?= esc_url($item->get_permalink()) ?>?ref=dashboard" target="_blank"><?= esc_html(wp_strip_all_tags($item->get_title())) ?></a>
			<?php endforeach; ?>
		</ul>
		<?php
		$rss = ob_get_clean();

		wp_send_json_success($rss);
	}

	/**
	 * Adds the dashboard widget
	 *
	 * @since 4.0
	 */
	public function add_widget() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		wp_add_dashboard_widget(
			'dashboard_maps_marker_pro',
			'Maps Marker Pro',
			array($this, 'dashboard_widget'),
			array($this, 'dashboard_widget_control')
		);
	}

	/**
	 * Displays the dashboard widget
	 *
	 * @since 4.0
	 */
	public function dashboard_widget() {
		global $wpdb;
		$license = MMP::get_instance('MMP\License');
		$db = MMP::get_instance('MMP\DB');
		$l10n = MMP::get_instance('MMP\L10n');

		$latest_version = get_transient('mapsmarkerpro_latest');
		$widgets = get_option('dashboard_widget_options');
		$options = array(
			'health'  => (isset($widgets['dashboard_maps_marker_pro']['health'])) ? !!$widgets['dashboard_maps_marker_pro']['health'] : true,
			'markers' => (isset($widgets['dashboard_maps_marker_pro']['markers'])) ? absint($widgets['dashboard_maps_marker_pro']['markers']) : 5,
			'blog'    => (isset($widgets['dashboard_maps_marker_pro']['blog'])) ? !!$widgets['dashboard_maps_marker_pro']['blog'] : true
		);
		if (!$options['markers']) {
			$markers = false;
		} else {
			$markers = $db->get_all_markers(array(
				'orderby'   => 'id',
				'sortorder' => 'desc',
				'limit'     => $options['markers']
			));
		}

		?>
		<div class="mmp-dashboard-widget">
			<input type="hidden" id="mmp-dashboard-nonce" value="<?= wp_create_nonce('mmp-dashboard') ?>" />
			<?php if (!$license->check_for_updates(true)): ?>
				<div class="mmp-dashboard-error">
					<?= sprintf(esc_html__('Warning: your license is invalid!', 'mmp'), MMP::$version) ?><br />
					<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_license') ?>"><?= esc_html__('Please go to the license page for more information.', 'mmp') ?></a>
				</div>
			<?php else: ?>
				<?php if (!$license->check_for_updates()): ?>
					<div class="mmp-dashboard-error">
						<?= esc_html__('Warning: your access to updates and support for Maps Marker Pro has expired!', 'mmp') ?><br />
						<?php if ($latest_version !== false && !version_compare(MMP::$version, $latest_version, '>=')): ?>
							<?= esc_html__('Latest available version:', 'mmp') ?> <a href="https://www.mapsmarker.com/v<?= $latest_version ?>" target="_blank" title="<?= esc_attr__('Show release notes', 'mmp') ?>"><?= $latest_version ?></a> (<a href="https://www.mapsmarker.com/changelog/pro/" target="_blank"><?= esc_html__('show all available changelogs', 'mmp') ?></a>)<br />
						<?php endif; ?>
						<?= sprintf(esc_html__('You can continue using version %1$s without any limitations. However, you will not be able access the support system or get updates including bugfixes, new features and optimizations.', 'mmp'), MMP::$version) ?><br />
						<a href="<?= get_admin_url(null, 'admin.php?page=mapsmarkerpro_license') ?>"><?= esc_html__('Please renew your access to updates and support to keep your plugin up-to-date and safe.', 'mmp') ?></a>
					</div>
				<?php endif; ?>
				<?php if ($options['health']): ?>
					<div id="mmp_dashboard_health" class="mmp-dashboard-health">
						<h3><?= esc_html__('Health check', 'mmp') ?></h3>
						<div id="mmp_health">
							<img src="<?= plugins_url('images/icons/paging-ajax-loader.gif', MMP::$path) ?>" />
						</div>
					</div>
				<?php endif; ?>
				<?php if ($markers !== false): ?>
					<?php if ($options['health']): ?>
						<hr class="mmp-dashboard-separator" />
					<?php endif; ?>
					<div class="mmp-dashboard-markers">
						<h3><?= esc_html__('Recent markers', 'mmp') ?></h3>
						<?php if (!count($markers)): ?>
							<p><?= esc_html__('No markers found', 'mmp') ?></p>
						<?php else: ?>
							<ul>
								<?php foreach ($markers as $marker): ?>
									<li>
										<span>
											<a href="<?= get_admin_url(null, "admin.php?page=mapsmarkerpro_marker&id={$marker->id}") ?>" title="<?= esc_attr__('Edit marker', 'mmp') ?>">
												<img src="<?= ($marker->icon) ? MMP::$icons_url . $marker->icon : plugins_url('images/leaflet/marker.png', MMP::$path) ?>" />
											</a>
										</span>
										<span>
											<a href="<?= get_admin_url(null, "admin.php?page=mapsmarkerpro_marker&id={$marker->id}") ?>" title="<?= esc_attr__('Edit marker', 'mmp') ?>">
												<?= ($marker->name) ? esc_html($marker->name) : esc_html__('(no name)', 'mmp') ?>
											</a><br />
											<?= sprintf(esc_html__('Created on %1$s by %2$s', 'mmp'), $l10n->date('datetime', $marker->created_on), esc_html($marker->created_by)) ?>
										</span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($options['blog']): ?>
				<?php if ($license->check_for_updates(true) && ($markers !== false || $options['health'])): ?>
					<hr class="mmp-dashboard-separator" />
				<?php endif; ?>
				<div id="mmp_dashboard_blog" class="mmp-dashboard-blog">
					<h3><?= esc_html__('Latest news', 'mmp') ?></h3>
					<div id="mmp_blog">
						<img src="<?= plugins_url('images/icons/paging-ajax-loader.gif', MMP::$path) ?>" />
					</div>
				</div>
				<hr class="mmp-dashboard-separator" />
				<div class="mmp-dashboard-links">
					<a href="https://www.mapsmarker.com/" target="_blank">
						<img src="<?= plugins_url('images/icons/website-home.png', MMP::$path) ?>" /> MapsMarker.com
					</a>
					<a href="https://affiliates.mapsmarker.com/" target="_blank" title="<?= esc_attr__('MapsMarker affiliate program - sign up now and receive commissions up to 50%!', 'mmp') ?>">
						<img src="<?= plugins_url('images/icons/affiliates.png', MMP::$path) ?>" /> <?= esc_html__('Affiliates', 'mmp') ?>
					</a>
					<a href="https://www.mapsmarker.com/reseller/" target="_blank" title="<?= esc_attr__('MapsMarker reseller program - re-sell with a 20% discount!', 'mmp') ?>">
						<img src="<?= plugins_url('images/icons/resellers.png', MMP::$path) ?>" /> <?= esc_html__('Resellers', 'mmp') ?>
					</a>
					<a href="https://www.mapsmarker.com/newsletter/" target="_blank" title="<?= esc_attr__('News via email', 'mmp') ?>">
						<img src="<?= plugins_url('images/icons/rss-email.png', MMP::$path) ?>" /> Newsletter
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Displays and handles the dashboard widget settings
	 *
	 * @since 4.0
	 */
	public function dashboard_widget_control() {
		$widgets = get_option('dashboard_widget_options');
		$options = array(
			'health'  => (isset($widgets['dashboard_maps_marker_pro']['health'])) ? !!$widgets['dashboard_maps_marker_pro']['health'] : true,
			'markers' => (isset($widgets['dashboard_maps_marker_pro']['markers'])) ? absint($widgets['dashboard_maps_marker_pro']['markers']) : 5,
			'blog'    => (isset($widgets['dashboard_maps_marker_pro']['blog'])) ? !!$widgets['dashboard_maps_marker_pro']['blog'] : true
		);

		if (isset($_POST['dashboard_maps_marker_pro_control'])) {
			$options['health'] = isset($_POST['dashboard_maps_marker_pro_control']['health']);
			$options['markers'] = (isset($_POST['dashboard_maps_marker_pro_control']['markers'])) ? absint($_POST['dashboard_maps_marker_pro_control']['markers']) : 5;
			$options['blog'] = isset($_POST['dashboard_maps_marker_pro_control']['blog']);
			$widgets['dashboard_maps_marker_pro'] = $options;
			update_option('dashboard_widget_options', $widgets);
		}

		?>
		<p>
			<label>
				<?= esc_html__('Show health check:', 'mmp') ?>
				<input type="checkbox" name="dashboard_maps_marker_pro_control[health]" <?php checked($options['health']) ?> />
			</label>
		</p>
		<p>
			<label>
				<?= esc_html__('Number of markers to show:', 'mmp') ?>
				<input type="number" name="dashboard_maps_marker_pro_control[markers]" min="0" max="100" value="<?= $options['markers'] ?>" />
			</label>
		</p>
		<p>
			<label>
				<?= esc_html__('Show news and links:', 'mmp') ?>
				<input type="checkbox" name="dashboard_maps_marker_pro_control[blog]" <?php checked($options['blog']) ?> />
			</label>
		</p>
		<?php
	}
}
