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
<h2><?php _e('Trashed Adverts', 'adrotate-pro'); ?></h2>
<p><em><?php _e('The trash is a temporary place for deleted adverts. If an advert is deleted by accident you can restore it from here. If nothing is done the advert is deleted after 3 days. Once the advert has been deleted it can no longer be recovered.', 'adrotate-pro'); ?></em></p>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate&view=trash">
	<?php wp_nonce_field('adrotate_bulk_ads_trash','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="restore"><?php _e('Restore', 'adrotate-pro'); ?></option>
		        <option value="delete"><?php _e('Delete (Permanently)', 'adrotate-pro'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat tablesorter manage-ads-archived" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
			<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
			<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
			<th width="5%"><center><?php _e('Device', 'adrotate-pro'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if (count($trash) > 0) {
		$class = '';
		foreach($trash as $banner) {
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
				<td><?php echo date_i18n("F d, Y", $banner['firstactive']);?><br /><?php echo date_i18n("F d, Y", $banner['lastactive']);?></td>
				<td>
					<strong><?php echo stripslashes($banner['title']);?></strong>
					<span style="color:#999;">
						<?php if(strlen($grouplist) > 0) echo '<br /><span style="font-weight:bold;">'.__('Groups:', 'adrotate-pro').'</span> '.$grouplist; ?>
						<?php if(strlen($banner['advertiser']) > 0 AND $adrotate_config['enable_advertisers'] == 'Y') echo '<br /><span style="font-weight:bold;">'.__('Advertiser:', 'adrotate-pro').'</span> '.$banner['advertiser'];
						if($banner['crate'] > 0 OR $banner['irate'] > 0) { echo ' <span style="font-weight:bold;">'.__('Budget:', 'adrotate-pro').'</span> '.number_format($banner['budget'], 2, '.', '').' - '.__('CPC:', 'adrotate-pro').' '.number_format($banner['crate'], 2, '.', '').' - '.__('CPM:', 'adrotate-pro').' '.number_format($banner['irate'], 2, '.', ''); } ?>
				</td>
				<td><center><?php echo $mobile; ?></center></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr id='no-adverts'>
			<th class="check-column">&nbsp;</th>
			<td colspan="5"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>

</form>
