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
<h2><?php _e('Active Adverts', 'adrotate-pro'); ?></h2>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate">
	<?php wp_nonce_field('adrotate_bulk_ads_active','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="duplicate"><?php _e('Duplicate', 'adrotate-pro'); ?></option>
		        <option value="deactivate"><?php _e('Deactivate', 'adrotate-pro'); ?></option>
		        <option value="archive"><?php _e('Archive (Permanently)', 'adrotate-pro'); ?></option>
		        <option value="trash"><?php _e('Move to Trash', 'adrotate-pro'); ?></option>
		        <option value="reset"><?php _e('Reset stats', 'adrotate-pro'); ?></option>
		        <option value="export-csv"><?php _e('Export to CSV', 'adrotate-pro'); ?></option>
		        <option value="" disabled><?php _e('-- Renew --', 'adrotate-pro'); ?></option>
		        <option value="renew-31536000"><?php _e('For 1 year', 'adrotate-pro'); ?></option>
		        <option value="renew-15552000"><?php _e('For 180 days', 'adrotate-pro'); ?></option>
		        <option value="renew-5184000"><?php _e('For 60 days', 'adrotate-pro'); ?></option>
		        <option value="renew-2592000"><?php _e('For 30 days', 'adrotate-pro'); ?></option>
		        <option value="renew-604800"><?php _e('For 7 days', 'adrotate-pro'); ?></option>
		        <option value="" disabled><?php _e('-- Weight --', 'adrotate-pro'); ?></option>
		        <option value="weight-2">2 - <?php _e('Barely visible', 'adrotate-pro'); ?></option>
		        <option value="weight-4">4 - <?php _e('Less than average', 'adrotate-pro'); ?></option>
		        <option value="weight-6">6 - <?php _e('Normal coverage', 'adrotate-pro'); ?></option>
		        <option value="weight-8">8 - <?php _e('More than average', 'adrotate-pro'); ?></option>
		        <option value="weight-10">10 - <?php _e('Best visibility', 'adrotate-pro'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat tablesorter manage-ads-main" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
			<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
			<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
			<?php if($adrotate_config['stats'] == 1) { ?>
				<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
				<th width="7%"><center><?php _e('CTR', 'adrotate-pro'); ?></center></th>
			<?php } ?>
			<?php if($adrotate_config['stats'] > 1) { ?>
				<th width="7%"><center><?php _e('Stats', 'adrotate-pro'); ?></center></th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
	<?php
	if($active) {
		$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
		$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';

		$class = '';
		foreach($active as $banner) {
			if($adrotate_config['stats'] == 1 AND $banner['tracker'] != 'N') {
				$stats = adrotate_stats($banner['id']);
				$stats_today = adrotate_stats($banner['id'], false, $today);
				$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);						
			}

			$grouplist = adrotate_ad_is_in_groups($banner['id']);
			
			$class = ($class != 'alternate') ? 'alternate' : '';
			if($banner['type'] == '7days') $class .= ' row_orange';

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
					<strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes($banner['title']);?></a></strong> <?php if($adrotate_config['stats'] == 1 AND $banner['tracker'] != 'N') { ?>- <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=advert&id='.$banner['id']);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a><?php } ?>
					<span style="color:#999;">
						<br /><strong><?php echo __('Devices:', 'adrotate-pro'); ?></strong> <?php echo $mobile; ?>, <strong><?php echo __('Weight:', 'adrotate-pro'); ?></strong> <?php echo $banner['weight']; ?>, <?php _e('Auto Delete:', 'adrotate-pro'); ?> <?php echo ($banner['autodelete'] == 'Y') ? $tick : $cross; ?>
						<?php if(strlen($grouplist) > 0) echo '<br /><span style="font-weight:bold;">'.__('Groups:', 'adrotate-pro').'</span> '.$grouplist; ?>
						<?php if(strlen($banner['advertiser']) > 0 AND $adrotate_config['enable_advertisers'] == 'Y') echo '<br /><span style="font-weight:bold;">'.__('Advertiser:', 'adrotate-pro').'</span> '.$banner['advertiser'];
						if($banner['crate'] > 0 OR $banner['irate'] > 0) { echo ' <span style="font-weight:bold;">'.__('Budget:', 'adrotate-pro').'</span> '.number_format($banner['budget'], 2, '.', '').' - '.__('CPC:', 'adrotate-pro').' '.number_format($banner['crate'], 2, '.', '').' - '.__('CPM:', 'adrotate-pro').' '.number_format($banner['irate'], 2, '.', ''); } ?>
				</td>
				<?php if($adrotate_config['stats'] == 1) { ?>
					<?php if($banner['tracker'] != "N") { ?>
					<td><center><?php echo $stats['impressions']; ?></center></td>
					<td><center><?php echo $stats_today['impressions']; ?></center></td>
					<td><center><?php echo $stats['clicks']; ?></center></td>
					<td><center><?php echo $stats_today['clicks']; ?></center></td>
					<td><center><?php echo $ctr; ?> %</center></td>
					<?php } else { ?>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
					<td><center>&hellip;</center></td>
					<?php } ?>
				<?php } ?>
				<?php if($adrotate_config['stats'] > 1) { ?>
					<td><center><?php echo ($banner['tracker'] != 'N') ? $tick : $cross; ?></center></td>
				<?php } ?>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr id='no-adverts'>
			<th class="check-column">&nbsp;</th>
			<td colspan="<?php echo ($adrotate_config['stats'] == 1) ? '10' : '5'; ?>"><em><?php _e('No adverts created yet!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
</center></p>

</form>
