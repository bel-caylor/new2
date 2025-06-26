<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
$permissions = get_user_meta($user_id, 'adrotate_permissions', 1);
$notes = get_user_meta($user_id, 'adrotate_notes', 1);

$ads = $wpdb->get_results("SELECT `{$wpdb->prefix}adrotate`.`id`, `title`, `type`, `crate`, `budget`, `irate` FROM `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE (`type` != 'empty' AND `type` != 'a_empty' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'generator') AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$user_id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate`.`id` ORDER BY `{$wpdb->prefix}adrotate`.`id` ASC;");

if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
if(!isset($permissions['mobile'])) $permissions['mobile'] = 'N';
if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
?>

<h2><?php _e('Advertiser Profile', 'adrotate-pro'); ?></h2>
<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">

		<form name="request" id="post" method="post" action="admin.php?page=adrotate-advertisers&view=profile">
		<?php wp_nonce_field('adrotate_save_advertiser','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_user" value="<?php echo $user_id;?>" />

		<div id="postbox-container-1" class="postbox-container" style="width:50%;">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				
				<h3><?php _e('Profile', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<table width="100%">
							<thead>
							<tr class="first">
								<td width="50%"><strong><?php _e('Who', 'adrotate-pro'); ?></strong></td>
								<td width="50%"><strong><?php _e('What', 'adrotate-pro'); ?></strong></td>
							</tr>
							</thead>
							
							<tbody>
							<tr class="first">
								<td class="first b"><?php echo $advertisers[$user_id]['name']; ?><br /><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertisers&view=contact&user='.$user_id);?>" title="<?php _e('Contact', 'adrotate-pro'); ?>"><?php echo $advertisers[$user_id]['email']; ?></a></td>
								<td class="b"><?php echo $advertisers[$user_id]['has_adverts']; ?> Adverts</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>

				<h3><?php _e('Notes', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<textarea tabindex="1" name="adrotate_notes" cols="50" rows="5" class="noborder"><?php echo esc_attr($notes); ?></textarea><br />
						<em><?php _e('No HTML/Javascript or code allowed.', 'adrotate-pro'); ?></em>
					</div>
				</div>

			</div>
		</div>

		<div id="postbox-container-3" class="postbox-container" style="width:50%;">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
						
				<h3><?php _e('Permissions', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
			        	<label for="adrotate_can_edit"><input tabindex="2" type="checkbox" name="adrotate_can_edit" <?php if($permissions['edit'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Create and edit their own adverts?', 'adrotate-pro'); ?></label><br />
			        	<label for="adrotate_can_mobile"><input tabindex="3" type="checkbox" name="adrotate_can_mobile" <?php if($permissions['mobile'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Specify devices for mobile adverts?', 'adrotate-pro'); ?></label><br />
			        	<label for="adrotate_can_geo"><input tabindex="4" type="checkbox" name="adrotate_can_geo" <?php if($permissions['geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can set up Geo Targeting?', 'adrotate-pro'); ?></label>
					</div>
				</div>

			</div>	
		</div>

		<div class="clear"></div>

		<p class="submit">
			<input tabindex="4" type="submit" name="adrotate_advertiser_submit" class="button-primary" value="<?php _e('Save', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-advertisers" class="button"><?php _e('Back', 'adrotate-pro'); ?></a>
		</p>
		</form>		

	</div>
</div>

<h2><?php _e('Advertiser Adverts', 'adrotate-pro'); ?></h2>
<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Name', 'adrotate-pro'); ?></th>
		<th width="15%"><?php _e('Visible until', 'adrotate-pro'); ?></th>
	</tr>
	</thead>

	<tbody>
	<?php
	$class = '';
	foreach($ads as $ad) {
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		$grouplist = adrotate_ad_is_in_groups($ad->id);

		$errorclass = '';
		if($ad->type == 'queue' OR $ad->type == 'reject') $errorclass = ' row_yellow';
		if($stoptime <= $in2days OR $stoptime <= $in7days) $errorclass = ' row_orange';
		if($stoptime <= $now OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget == 0)) $errorclass = ' row_red';

		$class = ('alternate' != $class) ? 'alternate' : '';
		$class = ($errorclass != '') ? $errorclass : $class;
		?>
	    <tr class='<?php echo $class; ?>'>
			<td><center><?php echo $ad->id; ?></center></td>
			<td><?php echo stripslashes($ad->title); ?>
			<span style="color:#999;">
				<?php if(strlen($grouplist) > 0) echo '<br /><span style="font-weight:bold;">'.__('Groups:', 'adrotate-pro').'</span> '.$grouplist; ?>
			</span></td>
			<td><span style="color: <?php echo adrotate_prepare_color($stoptime);?>;"><?php echo date_i18n("F d, Y", $stoptime); ?></span></td>
		</tr>
		<?php unset($stoptime);?>
	<?php } ?>
	</tbody>					
</table>

<p><center>
			<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Pending or rejected", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?>
</center></p>

