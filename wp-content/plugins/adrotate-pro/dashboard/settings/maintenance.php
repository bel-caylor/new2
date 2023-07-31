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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=maintenance">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Maintenance', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Use these functions when you are running into trouble with your adverts or you notice your database is slow, unresponsive and sluggish. Normally you should not need these functions, but sometimes they are a lifesaver!', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Check adverts', 'adrotate-pro'); ?></th>
		<td>
			<input type="submit" id="post-role-submit" name="adrotate_evaluate_submit" value="<?php _e('Check all adverts for configuration errors', 'adrotate-pro'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to check all adverts for errors.', 'adrotate-pro'); ?>\n\n<?php _e('This might take a few seconds!', 'adrotate-pro'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate-pro'); ?>')" />
			<br /><br />
			<span class="description"><?php _e('Apply all evaluation rules to all adverts to see if any error slipped in. This may take a few seconds.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
</table>

<h2><?php _e('Status indicators', 'adrotate-pro'); ?></h2>
<table class="form-table">
	<tr>
		<th width="15%"><?php _e('Current status of adverts', 'adrotate-pro'); ?></th>
		<td colspan="3"><?php _e('Normal', 'adrotate-pro'); ?>: <?php echo $advert_status['normal']; ?>, <?php _e('Over Limit', 'adrotate-pro'); ?>: <?php echo $advert_status['limit']; ?>, <?php _e('Error', 'adrotate-pro'); ?>: <?php echo $advert_status['error']; ?>, <?php _e('Expired', 'adrotate-pro'); ?>: <?php echo $advert_status['expired']; ?>, <?php _e('Expires Soon', 'adrotate-pro'); ?>: <?php echo $advert_status['expiressoon']; ?>, <?php _e('Unknown', 'adrotate-pro'); ?>: <?php echo $advert_status['unknown']; ?>.</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Banners/assets Folder', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'].'/ -> ';
			echo (is_writeable(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder']).'/') ? '<span style="color:#009900;">'.__('Exists and appears writable', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not writable or does not exist', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Reports Folder', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo WP_CONTENT_DIR.'/reports/'.' -> ';
			echo (is_writable(WP_CONTENT_DIR.'/reports/')) ? '<span style="color:#009900;">'.__('Exists and appears writable', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not writable or does not exist', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('ads.txt file', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo ABSPATH.$adrotate_config['adstxt_file'].'ads.txt. -> ';
			echo (file_exists(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt')) ? '<span style="color:#009900;">'.__('Exists', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not found', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Fix folder/ads.txt issue', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<input type="submit" id="post-role-submit" name="adrotate_create_folders_submit" value="<?php _e('Create missing folders/files', 'adrotate-pro'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to create a banners folder, reports folder and ads.txt file. If these already exists the task is skipped.', 'adrotate-pro'); ?>\n\n<?php _e('This may fail due to file permissions set by your hosting provider. Contact them if the issue is not resolved after using this function.', 'adrotate-pro'); ?>\n\n<?php _e('Are you sure you want to continue?', 'adrotate-pro'); ?>')" />
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Check adverts for errors', 'adrotate-pro'); ?></th>
		<td width="35%"><?php if(!$adevaluate) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $adevaluate).'</span>'; ?></td>
		<th width="15%"><?php _e('Send email notifications', 'adrotate-pro'); ?></th>
		<td><?php if(!$adschedule) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $adschedule).'</span>'; ?></td>
	</tr>
	<tr>
		<th><?php _e('Delete adverts from trash', 'adrotate-pro'); ?></th>
		<td><?php if(!$trash) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $trash).'</span>'; ?></td>
		<th><?php _e('Delete expired trackerdata', 'adrotate-pro'); ?></th>
		<td><?php if(!$tracker) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $tracker).'</span>'; ?></td>
	</tr>
	<tr>
		<th><?php _e('Delete expired adverts', 'adrotate-pro'); ?></th>
		<td><?php if(!$autodelete) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $autodelete).'</span>'; ?></td>
		<th>&nbsp;</th>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Background tasks', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<a class="button" href="<?php echo admin_url('admin.php?page=adrotate-settings&tab=maintenance&action=reset-tasks'); ?>"><?php _e('Reset background tasks', 'adrotate-pro'); ?></a>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Update server response', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			$update_response = get_transient('ajdg_update_response');
			if($update_response) {
				$api_status = $update_response['last_checked'].' - '.$update_response['code'].' '.$update_response['message'];
				echo ($update_response['code'] != 200) ? ' <span style="color:#CC2900;">'.$api_status.'</span>' : '<span style="color:#009900;">'.$api_status.'</span>';
			} else {
				echo 'N/A';
			}
			?>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Geo Targeting server status', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			$geo_response = get_transient('ajdg_geo_response');
			if($geo_response) {
				$geo_status = $geo_response['last_checked'].' - '.$geo_response['code'].' '.$geo_response['message'];
				echo ($geo_response['code'] != 200) ? ' <span style="color:#CC2900;">'.$geo_status.'</span>' : '<span style="color:#009900;">'.$geo_status.'</span>';
			} else {
				echo 'N/A';
			}
			?>
		</td>
	</tr>
</table>

<h2><?php _e('Internal Versions', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Unless you experience database issues or a warning shows below, these numbers are not really relevant for troubleshooting. Support may ask for them to verify your database status.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th width="15%" valign="top"><?php _e('AdRotate version', 'adrotate-pro'); ?></th>
		<td><?php _e('Current:', 'adrotate-pro'); ?> <?php echo '<span style="color:#009900;">'.$adrotate_version['current'].'</span>'; ?> <?php if($adrotate_version['current'] != ADROTATE_VERSION) { echo '<span style="color:#CC2900;">'; _e('Should be:', 'adrotate-pro'); echo ' '.ADROTATE_VERSION; echo '</span>'; } ?><br /><?php _e('Previous:', 'adrotate-pro'); ?> <?php echo $adrotate_version['previous']; ?></td>
		<th width="15%" valign="top"><?php _e('Database version', 'adrotate-pro'); ?></th>
		<td><?php _e('Current:', 'adrotate-pro'); ?> <?php echo '<span style="color:#009900;">'.$adrotate_db_version['current'].'</span>'; ?> <?php if($adrotate_db_version['current'] != ADROTATE_DB_VERSION) { echo '<span style="color:#CC2900;">'; _e('Should be:', 'adrotate-pro'); echo ' '.ADROTATE_DB_VERSION; echo '</span>'; } ?><br /><?php _e('Previous:', 'adrotate-pro'); ?> <?php echo $adrotate_db_version['previous']; ?></td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manual upgrade', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<a class="button" href="admin.php?page=adrotate-settings&tab=maintenance&action=update-db"><?php _e('Update Settings and Database', 'adrotate-pro'); ?></a>
		</td>
	</tr>
</table>

</form>
