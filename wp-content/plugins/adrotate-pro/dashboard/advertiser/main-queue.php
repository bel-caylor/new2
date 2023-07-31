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

<h3><?php _e('Queued Adverts', 'adrotate-pro'); ?></h3>
<p><em><?php _e('Adverts listed here are queued for review, awaiting payment, rejected by a reviewer or have a configuration error.', 'adrotate-pro'); ?></em></p>

<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Title', 'adrotate-pro'); ?></th>
		<th width="5%"><center><?php _e('Device', 'adrotate-pro'); ?></center></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($queuebanners as $banner) {
		$class = $errorclass = '';
		if('alternate' == $class) $class = 'alternate'; else $class = '';
		if($banner['type'] == 'error' OR $banner['type'] == 'a_error') $errorclass = ' row_yellow';
		if($banner['type'] == 'reject') $errorclass = ' row_red';

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
	    <tr id='banner-<?php echo $banner['id']; ?>' class='<?php echo $class.$errorclass; ?>'>
			<td><center><?php echo $banner['id'];?></center></td>
			<td>
				<strong><?php if($advertiser_permissions['edit'] == 'Y') { ?><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes(html_entity_decode($banner['title']));?></a><?php } else { echo stripslashes(html_entity_decode($banner['title'])); } ?></strong>

				<span style="color:#999;">
					<?php if($banner['crate'] > 0 OR $banner['irate'] > 0) {
						echo '<br /><span style="font-weight:bold;">'.__('Budget:', 'adrotate-pro').'</span> '.number_format($banner['budget'], 2, '.', '').' - '; 
						echo __('CPC:', 'adrotate-pro').' '.number_format($banner['crate'], 2, '.', '').' - ';
						echo __('CPM:', 'adrotate-pro').' '.number_format($banner['irate'], 2, '.', '');
					} ?>
				</span>
			</td>
			<td><center><?php echo $mobile;?></center></td>
		</tr>
		<?php } ?>
	</tbody>

</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Configuration errors", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Rejected", "adrotate-pro"); ?>
</center></p>