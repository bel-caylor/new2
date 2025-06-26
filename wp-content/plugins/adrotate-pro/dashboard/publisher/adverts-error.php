<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>
<h2><?php _e('Adverts that need attention', 'adrotate-pro'); ?></h2>
<p><?php _e('The adverts listed here are currently not showing on your website. This is because they are almost expired, have expired or have configuration issues.', 'adrotate-pro'); ?><br /><?php _e('To fix the issue edit each advert and look for one or more notification at the top to see what AdRotate thinks is wrong with it.', 'adrotate-pro'); ?></p>

<form name="errorbanners" id="post" method="post" action="admin.php?page=adrotate">
	<?php wp_nonce_field('adrotate_bulk_ads_error','adrotate_nonce'); ?>
	<div class="tablenav">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="deactivate"><?php _e('Deactivate', 'adrotate-pro'); ?></option>
		        <option value="archive"><?php _e('Archive (Permanently)', 'adrotate-pro'); ?></option>
		        <option value="trash"><?php _e('Move to Trash', 'adrotate-pro'); ?></option>
		        <option value="reset"><?php _e('Reset stats', 'adrotate-pro'); ?></option>
		        <option value="" disabled><?php _e('-- Renew --', 'adrotate-pro'); ?></option>
		        <option value="renew-31536000"><?php _e('For 1 year', 'adrotate-pro'); ?></option>
		        <option value="renew-5184000"><?php _e('For 180 days', 'adrotate-pro'); ?></option>
		        <option value="renew-2592000"><?php _e('For 30 days', 'adrotate-pro'); ?></option>
		        <option value="renew-604800"><?php _e('For 7 days', 'adrotate-pro'); ?></option>
			</select>
			<input type="submit" id="post-action-submit" name="adrotate_error_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>
	
		<br class="clear" />
	</div>
	
		<table class="widefat tablesorter manage-ads-error" style="margin-top: .5em">
			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
				<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
				<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
				<th><?php _e('Name', 'adrotate-pro'); ?></th>
			</tr>
			</thead>
			<tbody>
		<?php 
		foreach($error as $banner) {
			$grouplist = adrotate_ad_is_in_groups($banner['id']);
			
			$class = '';
			if($banner['type'] == 'error') $class = 'row_yellow'; 
			if($banner['type'] == 'a_error') $class = 'row_yellow'; 
			if($banner['type'] == 'expired') $class = 'row_red';
			if($banner['type'] == '2days') $class = 'row_orange';
			if($banner['type'] == 'limit') $class = 'row_blue';

			$mobile = '';
			if($banner['desktop'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/desktop.png', __FILE__).'" width="12" height="12" title="Desktop" />';
			}
			if($banner['mobile'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/mobile.png', __FILE__).'" width="12" height="12" title="Mobile" />';
			}
			if($banner['tablet'] == 'Y') {
				$mobile .= '<img src="'.plugins_url('../../images/tablet.png', __FILE__).'" width="12" height="12" title="Tablet" />';
			}
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="bannercheck[]" value="<?php echo $banner['id']; ?>" /></th>
				<td><center><?php echo $banner['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y", $banner['firstactive']);?><br /><span style="color: <?php echo adrotate_prepare_color($banner['lastactive']);?>;"><?php echo date_i18n("F d, Y", $banner['lastactive']);?></span></td>
				<td><strong><a class="row-title" href="<?php echo admin_url("/admin.php?page=adrotate&view=edit&ad=".$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes($banner['title']);?></a></strong> <?php if($adrotate_config['stats'] == 1 AND $banner['type'] != 'error' AND $banner['type'] != 'a_error') { ?>- <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=advert&id='.$banner['id']);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a><?php } ?>
					<span style="color:#999;">
						<br /><strong><?php echo __('Devices:', 'adrotate-pro'); ?></strong> <?php echo $mobile; ?>, <strong><?php echo __('Weight:', 'adrotate-pro'); ?></strong> <?php echo $banner['weight']; ?>
						<?php if(!empty($banner['advertiser']) > 0) echo '<span style="font-weight:bold;">'.__('Advertiser:', 'adrotate-pro').'</span> '.$banner['advertiser']; ?>
						<?php if(strlen($grouplist) > 0) echo '<br /><span style="font-weight:bold;">'.__('Groups:', 'adrotate-pro').'</span> '.$grouplist; ?>
					</span>
				</td>
			</tr>
			<?php } ?>
		</tbody>

	</table>
	<p><center>
		<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Configuration errors", "adrotate-pro"); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #ebf3fa">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Limit reached", "adrotate-pro"); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?>
	</center></p>
</form>
