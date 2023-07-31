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
<h2><?php _e('Moderation Queue', 'adrotate-pro'); ?></h2>
<p><em><?php _e('The moderation queue lists adverts assigned to your advertisers that need reviewing. This includes changes made by advertisers.', 'adrotate-pro'); ?></em></p>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-moderate">
	<?php wp_nonce_field('adrotate_bulk_ads_queue','adrotate_nonce'); ?>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="approve"><?php _e('Approve', 'adrotate-pro'); ?></option>
		        <option value="queue"><?php _e('Queue', 'adrotate-pro'); ?></option>
		        <option value="reject"><?php _e('Reject', 'adrotate-pro'); ?></option>
		        <option value="delete"><?php _e('Delete', 'adrotate-pro'); ?></option>
			</select>
			<input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>
	
		<br class="clear" />
	</div>

	<table class="widefat tablesorter moderate-queue" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
			<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
			<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
			<th width="5%"><?php _e('Device', 'adrotate-pro'); ?></th>
			<th width="5%"><?php _e('Weight', 'adrotate-pro'); ?></th>
			<th width="20%"><?php _e('Advertiser', 'adrotate-pro'); ?></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if ($queued) {
		$class = '';
		foreach($queued as $queue) {			
			$advertiser = $wpdb->get_var("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$queue['id']}' AND `group` = 0 AND `schedule` = 0;");
			$advertiser_name = $wpdb->get_var("SELECT `display_name` FROM `{$wpdb->users}` WHERE `ID` = $advertiser;");
			
			$groups	= $wpdb->get_results("
				SELECT 
					`{$wpdb->prefix}adrotate_groups`.`name` 
				FROM 
					`{$wpdb->prefix}adrotate_groups`, 
					`{$wpdb->prefix}adrotate_linkmeta` 
				WHERE 
					`{$wpdb->prefix}adrotate_linkmeta`.`ad` = '{$queue['id']}'
					AND `{$wpdb->prefix}adrotate_linkmeta`.`group` = `{$wpdb->prefix}adrotate_groups`.`id`
					AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0
				;");
			$grouplist = '';
			foreach($groups as $group) {
				$grouplist .= $group->name.", ";
			}
			$grouplist = rtrim($grouplist, ", ");
			
			$errorclass = '';
			if($queue['type'] == 'error' OR $queue['type'] == 'a_error') $errorclass = ' row_yellow';
			if($queue['type'] == 'reject') $errorclass = ' row_orange';
			if($queue['lastactive'] <= $in2days OR $queue['lastactive'] <= $in7days) $errorclass = ' row_red';
			if($queue['lastactive'] <= $now OR (($queue['crate'] > 0 OR $queue['irate'] > 0) AND $queue['budget'] == 0)) $errorclass = ' row_blue';

			$class = ('alternate' != $class) ? 'alternate' : '';
			$class = ($errorclass != '') ? $errorclass : $class;

			$mobile = '';
			if($queue['desktop'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/desktop.png', __FILE__).'" width="12" height="12" title="Desktop" />';
			}
			if($queue['mobile'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/mobile.png', __FILE__).'" width="12" height="12" title="Mobile" />';
			}
			if($queue['tablet'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/tablet.png', __FILE__).'" width="12" height="12" title="Tablet" />';
			}
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="bannercheck[]" value="<?php echo $queue['id']; ?>" /></th>
				<td><center><?php echo $queue['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y", $queue['firstactive']);?><br /><span style="color: <?php echo adrotate_prepare_color($queue['lastactive']);?>;"><?php echo date_i18n("F d, Y", $queue['lastactive']);?></span></td>
				<td><strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=edit&ad='.$queue['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes($queue['title']);?></a></strong><?php if($groups) { echo '<br /><span style="color:#999"><strong>'.__('Groups:', 'adrotate-pro').'</strong> '.$grouplist.'</span>'; } ?></td>
				<td><center><?php echo $mobile; ?></center></td>
				<td><center><?php echo $queue['weight']; ?></center></td>
				<td><?php echo $advertiser_name; ?></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-groups'>
			<th class="check-column">&nbsp;</th>
			<td colspan="6"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Configuration errors", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?> / <?php _e("Rejected.", "adrotate-pro"); ?>
</center></p>
</form>