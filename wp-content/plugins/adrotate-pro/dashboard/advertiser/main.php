<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2021 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */
?>

<h3><?php _e('Active Adverts', 'adrotate-pro'); ?></h3>
<p><em><?php _e('These are active and currently in the pool of adverts shown on the website.', 'adrotate-pro'); ?></em></p>

<table class="widefat" style="margin-top: .5em">
	<thead>
		<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th width="15%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
		<th><?php _e('Title', 'adrotate-pro'); ?></th>
		<th width="5%"><center><?php _e('Device', 'adrotate-pro'); ?></center></th>
		<?php if($adrotate_config['stats'] == 1) { ?>
			<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
			<th width="7%"><center><?php _e('CTR', 'adrotate-pro'); ?></center></th>
		<?php } ?>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($activebanners as $banner) {
		if($adrotate_config['stats'] == 1) {
			$stats = adrotate_stats($banner['id']);
			$stats_today = adrotate_stats($banner['id'], false, $today);
			$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);
		}

		$errorclass = $class = '';
		if('alternate' == $class) $class = 'alternate'; else $class = '';
		if($banner['type'] == '2days') $errorclass = ' row_orange'; 
		if($banner['type'] == '7days') $errorclass = ' row_orange';
		if($banner['type'] == 'expired') $errorclass = ' row_red';

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
	    <tr id='banner-<?php echo $banner['id']; ?> <?php echo $banner['type']; ?>' class='<?php echo $class.$errorclass; ?>'>
			<td><center><?php echo $banner['id'];?></center></td>
			<td><?php echo date_i18n("F d, Y", $banner['firstactive']);?><br /><span style="color: <?php echo adrotate_prepare_color($banner['lastactive']);?>;"><?php echo date_i18n("F d, Y", $banner['lastactive']);?></span></td>
			<td>
				<strong><?php if($advertiser_permissions['edit'] == 'Y') { ?><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes(html_entity_decode($banner['title']));?></a><?php } else { echo stripslashes(html_entity_decode($banner['title'])); } ?></strong>
				<?php if($adrotate_config['stats'] == 1) { ?> - <a href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=report&ad='.$banner['id']);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>">Stats</a><?php } ?>
				<span style="color:#999;">
					<?php if($banner['crate'] > 0 OR $banner['irate'] > 0) {
						echo '<br /><span style="font-weight:bold;">'.__('Budget:', 'adrotate-pro').'</span> '.number_format($banner['budget'], 2, '.', '').' - '; 
						echo __('CPC:', 'adrotate-pro').' '.number_format($banner['crate'], 2, '.', '').' - ';
						echo __('CPM:', 'adrotate-pro').' '.number_format($banner['irate'], 2, '.', '');
					} ?>
				</span>
			</td>
			<td><center><?php echo $mobile;?></center></td>
			<?php if($adrotate_config['stats'] == 1) { ?>
				<td><center><?php echo $stats['impressions'];?></center></td>
				<td><center><?php echo $stats_today['impressions'];?></center></td>
				<td><center><?php echo $stats['clicks'];?></center></td>
				<td><center><?php echo $stats_today['clicks'];?></center></td>
				<td><center><?php echo $ctr; ?> %</center></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>

</table>
<p><center>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?>
</center></p>