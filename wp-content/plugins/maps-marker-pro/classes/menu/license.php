<?php
namespace MMP\Menu;

use MMP\Maps_Marker_Pro as MMP;

class License extends Menu {
	/**
	 * Registers the hooks
	 *
	 * @since 4.0
	 */
	public function init() {
		add_action('admin_enqueue_scripts', array($this, 'load_resources'));
	}

	/**
	 * Loads the required resources
	 *
	 * @since 4.0
	 *
	 * @param string $hook Name of the current admin page
	 */
	public function load_resources($hook) {
		if (substr($hook, -strlen('mapsmarkerpro_license')) !== 'mapsmarkerpro_license') {
			return;
		}

		$this->load_global_resources($hook);

		wp_enqueue_script('mmp-admin');
		wp_add_inline_script('mmp-admin', 'licenseActions();');
	}

	/**
	 * Shows the license page
	 *
	 * @since 4.0
	 */
	protected function show() {
		$license = MMP::get_instance('MMP\License');
		$l10n = MMP::get_instance('MMP\L10n');

		$current_user = wp_get_current_user();

		$license_key = get_option('mapsmarkerpro_key');
		$license_key_trial = get_option('mapsmarkerpro_key_trial');
		$latest_version = get_transient('mapsmarkerpro_latest');

		$license->check_for_updates(true);
		$key_data = $license->get_key_data();
		$license_error = get_transient('mapsmarkerpro_license_error');

		?>
		<div class="wrap mmp-wrap">
			<h1><?= esc_html__('License', 'mmp') ?></h1>
			<?php if ($license->errors): ?>
				<div class="notice notice-error">
					<p><?= $license->errors ?></p>
					<?php if ($license_error !== false): ?>
						<p>
							<?= sprintf($l10n->kses__('Once the error is resolved, please click the update button. If you need further assistance, you can <a href="%1$s" target="_blank">open a support ticket</a>.', 'mmp'), 'https://www.mapsmarker.com/helpdesk/') ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($license_key): ?>
				<div class="mmp-main mmp-license-section <?= ($license->errors) ? 'mmp-license-invalid' : 'mmp-license-valid' ?>">
					<p>
						<label for="license_key" class="mmp-label"><strong><?= esc_html__('License Key', 'mmp') ?></strong></label>
						<input type="text" id="license_key" value="<?= $license_key ?>" />
						<button type="button" id="update_license" class="butto button-primary" data-nonce="<?= wp_create_nonce('mmp-update-license') ?>"><?= esc_html__('Update', 'mmp') ?></button>
						<?php if (is_multisite() && is_super_admin()): ?>
							<label>
								<input type="checkbox" id="distribute_multisite" />
								<?= esc_html__('Distribute the license key to all subsites', 'mmp') ?>
							</label>
						<?php endif; ?>
					</p>
					<?php if (isset($key_data['customer']['id']) && $key_data['customer']['id'] !== '17164'): ?>
						<p>
							<?= sprintf($l10n->kses__('Please note that a license is bound to the domain it was activated on. If you want to use your license on another domain, please follow <a href="%1$s" target="_blank">this tutorial</a>.', 'mmp'), 'https://www.mapsmarker.com/transfer/') ?><br />
							<?= sprintf($l10n->kses__('If you have any issues with your license, please <a href="%1$s" target="_blank">open a support ticket</a>.', 'mmp'), 'https://www.mapsmarker.com/helpdesk/') ?>
						</p>
					<?php endif; ?>
					<?php
					if ($license->check_for_updates(true)) {
						?>
						<p>
							<?php
							if (isset($key_data['customer']['name'])) {
								?>
								<strong><?= esc_html__('License registered to', 'mmp') ?>:</strong> <?= $key_data['customer']['name'] ?><br />
								<?php
							}
							if ($license->check_for_updates()) {
								if ($license->is_paid_version()) {
									$download_expires = $key_data['download_access_expires'];
									$download_expires_diff = abs(floor((time() - $download_expires) / (60 * 60 * 24)));
									?>
									<strong><?= esc_html__('Access to updates and support valid until', 'mmp') ?>:</strong> <?= $l10n->date('date', $download_expires) ?> (<?= $download_expires_diff ?> <?= esc_html__('days left', 'mmp') ?>) &rarr; <a href="https://www.mapsmarker.com/renew/" target="_blank"><?= esc_html__('Renew your access to updates and support', 'mmp') ?></a>
									<?php
								} else {
									$download_expires = $key_data['license_expires'];
									$download_expires_diff = abs(floor((time() - $download_expires) / (60 * 60 * 24)));
									?>
									<strong><?= esc_html__('Free trial license valid until', 'mmp') ?>:</strong> <?= $l10n->date('date', $download_expires) ?> (<?= $download_expires_diff ?> <?= esc_html__('days left', 'mmp') ?>) &rarr; <a href="https://www.mapsmarker.com/order/" target="_blank"><?= esc_html__('Get a non-expiring license key', 'mmp') ?></a>
									<?php
								}
							} else {
								?>
								<strong><?= esc_html__('Warning: your access to updates and support for Maps Marker Pro has expired!', 'mmp') ?></strong><br />
								<?php if ($latest_version !== false && version_compare($latest_version, MMP::$version, '>')): ?>
									<?= esc_html__('Latest available version:', 'mmp') ?> <a href="https://www.mapsmarker.com/v<?= $latest_version ?>" target="_blank" title="<?= esc_attr__('Show release notes', 'mmp') ?>"><?= $latest_version ?></a> (<a href="www.mapsmarker.com/changelog/pro/" target="_blank"><?= esc_html__('show all available changelogs', 'mmp') ?></a>)<br />
								<?php endif; ?>
								<?= sprintf(esc_html__('You can continue using version %1$s without any limitations. However, you will not be able access the support system or get updates including bugfixes, new features and optimizations.', 'mmp'), MMP::$version) ?><br />
								<a href="https://www.mapsmarker.com/renew/" target="_blank">&raquo; <?= esc_html__('Please click here to renew your access to updates and support', 'mmp') ?> &laquo;</a><br />
								<?= esc_html__('Important: please click the update button next to the license key after purchasing a renewal to finish your order.', 'mmp') ?>
								<?php
							}
							?>
						</p>
						<?php
					}
					?>
				</div>
			<?php else: ?>
				<div class="mmp-main mmp-license-section">
					<h2>
						<img class="mmp-license-icon" src="<?= plugins_url('images/icons/license-option-a.svg', MMP::$path) ?>" />
						<?= esc_html__('Option A: activate an unexpiring license key', 'mmp') ?>
					</h2>
					<p>
						<?= sprintf(esc_html__('Get an unexpiring license key at %1$s and activate the license key below.', 'mmp'), '<a href="https://www.mapsmarker.com/order/" target="_blank">mapsmarker.com/order/</a>') ?>
					</p>
					<label for="license_key" class="mmp-label"><?= esc_html__('License Key', 'mmp') ?></label>
					<input type="text" id="license_key" />
					<button type="button" id="update_license" class="butto button-primary" data-nonce="<?= wp_create_nonce('mmp-update-license') ?>"><?= esc_html__('Activate', 'mmp') ?></button>
					<?php if (is_multisite() && is_super_admin()): ?>
						<label>
							<input type="checkbox" id="distribute_multisite" />
							<?= esc_html__('Distribute the license key to all subsites', 'mmp') ?>
						</label>
					<?php endif; ?>
				</div>
				<div class="mmp-main mmp-license-section">
					<h2>
						<img class="mmp-license-icon" src="<?= plugins_url('images/icons/license-option-b.svg', MMP::$path) ?>" />
						<?= esc_html__('Option B: get a personalized trial license key', 'mmp') ?>
					</h2>
					<?php if ($license_key_trial): ?>
						<p>
							<?= sprintf(esc_html__('You already started a free 30-day-trial for this site - free trial license key: %1$s', 'mmp'), "<code>$license_key_trial</code>") ?>
						</p>
					<?php else: ?>
						<p>
							<?= esc_html__('You can test Maps Marker Pro for 30 days for free without any obligations.', 'mmp') ?>
						</p>
						<label for="personal_trial_first_name" class="mmp-label"><?= esc_html__('First name', 'mmp') ?></label>
						<input type="text" id="personal_trial_first_name" value="<?= $current_user->user_firstname ?>" /><br />
						<label for="personal_trial_last_name" class="mmp-label"><?= esc_html__('Last name', 'mmp') ?></label>
						<input type="text" id="personal_trial_last_name" value="<?= $current_user->user_lastname ?>" /><br />
						<label for="personal_trial_email" class="mmp-label"><?= esc_html__('Email', 'mmp') ?></label>
						<input type="text" id="personal_trial_email" value="<?= $current_user->user_email ?>" /><br />
						<p>
							<label>
								<input type="checkbox" id="personal_trial_tos" />
								<?= sprintf($l10n->kses__('I have read the <a href="%1$s" target="_blank">Terms of Service</a> and <a href="%2$s" target="_blank">Privacy Policy</a>.', 'mmp'), 'https://www.mapsmarker.com/terms-of-services/', 'https://www.mapsmarker.com/privacy-policy/') ?>
							</label>
						</p>
						<button type="button" id="personal_trial_submit" class="button button-secondary" data-nonce="<?= wp_create_nonce('mmp-register-personal-trial') ?>"><?= esc_html__('Start personalized free 30-day trial period', 'mmp') ?></button>
					<?php endif; ?>
				</div>
				<div class="mmp-main mmp-license-section">
					<h2>
						<img class="mmp-license-icon" src="<?= plugins_url('images/icons/license-option-c.svg', MMP::$path) ?>" />
						<?= esc_html__('Option C: get an anonymous trial license key', 'mmp') ?>
					</h2>
					<?php if ($license_key_trial): ?>
						<p>
							<?= sprintf(esc_html__('You already started a free 30-day-trial for this site - free trial license key: %1$s', 'mmp'), "<code>$license_key_trial</code>") ?>
						</p>
					<?php else: ?>
						<div id="anonymous_trial" class="mmp-hidden">
							<p>
								<?= sprintf($l10n->kses__('Please note that in contrast to a personalized trial license, you will not be able to <a href="%1s" target="_blank">open support tickets</a> or get a reminder when your trial license has expired.', 'mmp'), 'https://www.mapsmarker.com/helpdesk/') ?>
							</p>
							<p>
								<label>
									<input type="checkbox" id="anonymous_trial_tos" />
									<?= sprintf($l10n->kses__('I have read the <a href="%1$s" target="_blank">Terms of Service</a> and <a href="%2$s" target="_blank">Privacy Policy</a>.', 'mmp'), 'https://www.mapsmarker.com/terms-of-services/', 'https://www.mapsmarker.com/privacy-policy/') ?>
								</label>
							</p>
							<button type="button" id="anonymous_trial_submit" class="button button-secondary" data-nonce="<?= wp_create_nonce('mmp-register-anonymous-trial') ?>"><?= esc_html__('Start anonymous free 30-day trial period', 'mmp') ?></button>
						</div>
						<a id="anonymous_trial_show" href="#"><?= esc_html__('Click here for more info', 'mmp') ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
