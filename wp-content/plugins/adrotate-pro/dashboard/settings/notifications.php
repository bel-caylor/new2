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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=notifications">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<?php wp_nonce_field('adrotate_email_test','adrotate_nonce'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Notifications', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Set up who gets notifications if adverts need your attention.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('How to notify', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_notification_dash"><input type="checkbox" name="adrotate_notification_dash" id="adrotate_notification_dash" <?php if($adrotate_notifications['notification_dash'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Dashboard banner.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_email"><input type="checkbox" name="adrotate_notification_email" id="adrotate_notification_email" <?php if($adrotate_notifications['notification_email'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Email message.', 'adrotate-pro'); ?></label><br />
		</td>
	</tr>
</table>

<h3><?php _e('Dashboard Banner', 'adrotate-pro'); ?></h3>
<span class="description"><?php _e('These go in a dashboard banner visible to all users with access to AdRotate on every WordPress dashboard page.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('What', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_notification_dash_expired"><input type="checkbox" name="adrotate_notification_dash_expired" id="adrotate_notification_dash_expired" <?php if($adrotate_notifications['notification_dash_expired'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Expired adverts.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_dash_soon"><input type="checkbox" name="adrotate_notification_dash_soon" id="adrotate_notification_dash_soon" <?php if($adrotate_notifications['notification_dash_soon'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Adverts expiring in less than 2 days.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_dash_week"><input type="checkbox" name="adrotate_notification_dash_week" id="adrotate_notification_dash_week" <?php if($adrotate_notifications['notification_dash_week'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Adverts expiring in less than 7 days.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_schedules"><input type="checkbox" name="adrotate_notification_schedules" id="adrotate_notification_schedules" <?php if($adrotate_notifications['notification_schedules'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Schedules with warnings.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
</table>

<h3><?php _e('Email Message', 'adrotate-pro'); ?></h3>
<span class="description"><?php _e('Receive email notifications about what is happening with your AdRotate setup.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('What', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_notification_mail_status"><input type="checkbox" name="adrotate_notification_mail_status" id="adrotate_notification_mail_status" <?php if($adrotate_notifications['notification_mail_status'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Daily digest of any advert status other than normal.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_mail_geo"><input type="checkbox" name="adrotate_notification_mail_geo" id="adrotate_notification_mail_geo" <?php if($adrotate_notifications['notification_mail_geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('When you are running out of Geo Targeting Lookups.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_mail_queue"><input type="checkbox" name="adrotate_notification_mail_queue" id="adrotate_notification_mail_queue" <?php if($adrotate_notifications['notification_mail_queue'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Any advertiser saving an advert in your moderation queue.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_mail_approved"><input type="checkbox" name="adrotate_notification_mail_approved" id="adrotate_notification_mail_approved" <?php if($adrotate_notifications['notification_mail_approved'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('A moderator approved an advert from the moderation queue.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_notification_mail_rejected"><input type="checkbox" name="adrotate_notification_mail_rejected" id="adrotate_notification_mail_rejected" <?php if($adrotate_notifications['notification_mail_rejected'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('A moderator rejected an advert from the moderation queue.', 'adrotate-pro'); ?></label><br /><span class="description"><?php _e('If you have a lot of activity with many advertisers adding/changing adverts you may get a lot of messages!', 'adrotate-pro'); ?><br /><br /><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('Sending out a lot of email is sometimes seen as automated mailing and deemed spammy. This may result in automated filters such as those used in services like Google Gmail and Microsoft Hotmail/Outlook.com blocking your server. Make sure you whitelist the sending address in your email account once you start receiving notifications!', 'adrotate-pro'); ?></span>

		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Publishers', 'adrotate-pro'); ?></th>
		<td>
			<textarea name="adrotate_notification_email_publisher" cols="50" rows="2"><?php echo $notification_mails; ?></textarea><br />
			<span class="description"><?php _e('Messages are sent once every 24 hours.  Maximum of 5 addresses. Comma separated. This field may not be empty!', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Test notification', 'adrotate-pro'); ?></th>
		<td>
			<input type="submit" name="adrotate_notification_test_submit" class="button-secondary" value="<?php _e('Test', 'adrotate-pro'); ?>" /> <?php _e('Send a test email notification. Before you test, save the settings first!', 'adrotate-pro'); ?>
		</td>
	</tr>
</table>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>