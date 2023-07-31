<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=license">
<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('AdRotate Pro License', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Activate your AdRotate Pro License to receive automatic updates, use AdRotate Geo and be eligble for premium support.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('License', 'adrotate-pro'); ?></th>
		<td>
			<p><?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'].' License' : __('Not activated - Not eligible for support and updates.', 'adrotate-pro'); ?></p>
			<?php if($adrotate_activate['created'] > 0) { ?>
				<p><strong>Created: </strong> <?php echo date_i18n('d M Y H:i', $adrotate_activate['created']); ?> - <strong>Expires (Approx):</strong> <?php echo date_i18n('d M Y H:i', $adrotate_activate['created'] + (DAY_IN_SECONDS * 365)); ?></p>
			<?php } ?>
		</td>
	</tr>
	<?php if($adrotate_activate['created'] > 0 AND $adrotate_activate['created'] < (current_time('timestamp') - (DAY_IN_SECONDS * 365))) { ?>
	<tr>
		<th valign="top"><?php _e('Notice', 'adrotate-pro'); ?></th>
		<td>
			<strong><?php _e('Your license has expired. In order to continue to receive further updates, support and access to AdRotate Geo please get a new license.', 'adrotate-pro'); ?></strong><br /><?php _e('As a thank you for your continued usage and support of AdRotate Pro you can get a new license at a special discounted price.', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/support/adrotate-manuals/adrotate-pro-license-renewal/" target="_blank"><?php _e('Get a new license', 'adrotate-pro'); ?> &raquo;</a>
		</td>
	</tr>
	<?php } ?>
	<?php if(!$adrotate_is_networked) { ?>
	<tr>
		<th valign="top"><?php _e('License Email', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used in your purchase of AdRotate Pro.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('License Key', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
		<?php if($adrotate_activate['status'] == 1) { ?>
	<tr>
		<th valign="top"><?php _e('Force de-activate', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_license_force"><input type="checkbox" name="adrotate_license_force" id="adrotate_license_force" /> <span class="description"><?php _e('If your license has expired you may need to force de-activate the old license before you can activate the new key.', 'adrotate-pro'); ?></span></label>
		</td>
	</tr>
		<?php } ?>
	<?php } ?>
</table>

<?php if(!$adrotate_is_networked) { ?>
	<p class="submit">
		<?php if($adrotate_activate['status'] == 0) { ?>
		<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate license', 'adrotate-pro'); ?>" class="button-primary" />
		<?php } else { ?>
		<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate license', 'adrotate-pro'); ?>" class="button-primary" />
		<?php } ?>
		&nbsp;&nbsp;<em><?php _e('Click only once! this may take a few seconds.', 'adrotate-pro'); ?></em>
	</p>
<?php } ?>
<p><strong>Note:</strong> If you are having trouble with your license or ran out of activations you can reset unused sites in your account on the AJdG Solutions website.<br />If that does not help or you have another problem with our license please <a href="<?php echo admin_url('/admin.php?page=adrotate-support');?>" target="_blank">contact support</a> for assistance.</p>
</form>
