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
<form name="disabled_banners" id="post" method="post" action="admin.php?page=adrotate">
	<?php wp_nonce_field('adrotate_bulk_ads_disable','adrotate_nonce'); ?>
	
	<h2><?php _e('Disabled Adverts', 'adrotate-pro'); ?></h2>
	<p><em><?php _e('These adverts are temporarily disabled. You can archive adverts to permanently disable them.', 'adrotate-pro'); ?><br /><?php _e('Archiving adverts moves gathered statistics away from the live database which may speed up your website.', 'adrotate-pro'); ?></em></p>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="activate"><?php _e('Activate', 'adrotate-pro'); ?></option>
		        <option value="archive"><?php _e('Archive (Permanently)', 'adrotate-pro'); ?></option>
		        <option value="trash"><?php _e('Move to Trash', 'adrotate-pro'); ?></option>
		        <option value="reset"><?php _e('Reset stats', 'adrotate-pro'); ?></option>
			</select>
			<input type="submit" id="post-action-submit" name="adrotate_disabled_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>
	
		<br class="clear" />
	</div>
	
		<table class="widefat tablesorter manage-ads-disabled" style="margin-top: .5em">
			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
				<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
				<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
				<th><?php _e('Name', 'adrotate-pro'); ?></th>
				<?php if($adrotate_config['stats'] == 1) { ?>
					<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
					<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
					<th width="5%"><center><?php _e('CTR', 'adrotate-pro'); ?></center></th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>
		<?php
		$class = '';
		foreach($disabled as $banner) {
			if($adrotate_config['stats'] == 1 AND $banner['tracker'] != 'N') {
				$stats = adrotate_stats($banner['id']);
				$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);
			}

			$grouplist = adrotate_ad_is_in_groups($banner['id']);
			$class = ($class != 'alternate') ? 'alternate' : '';

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
				<td>
					<strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes($banner['title']);?></a></strong> <?php if($adrotate_config['stats'] == 1 AND $banner['tracker'] != 'N') { ?>
- <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=advert&id='.$banner['id']);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a><?php } ?>
					<span style="color:#999;">
						<br /><strong><?php echo __('Devices:', 'adrotate-pro'); ?></strong> <?php echo $mobile; ?>, <strong><?php echo __('Weight:', 'adrotate-pro'); ?></strong> <?php echo $banner['weight']; ?>
						<?php if(strlen($grouplist) > 0) echo '<br /><strong>'.__('Groups:', 'adrotate-pro').'</strong> '.$grouplist; ?>
						<?php if(strlen($banner['advertiser']) > 0) echo '<br /><strong>'.__('Advertiser:', 'adrotate-pro').'</strong> '.$banner['advertiser']; ?>
					</span>
				</td>
				<?php if($adrotate_config['stats'] == 1 AND $banner['tracker'] != 'N') { ?>
					<td><center><?php echo $stats['impressions']; ?></center></td>
					<td><center><?php echo $stats['clicks']; ?></center></td>
					<td><center><?php echo $ctr; ?> %</center></td>
				<?php } else { ?>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	
</form>
