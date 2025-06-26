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

<h3><?php _e('Disabled Adverts', 'adrotate-pro'); ?></h3>
<p><em><?php _e('The adverts in here are disabled but kept for reference or later use.', 'adrotate-pro'); ?></em></p>

<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Title', 'adrotate-pro'); ?></th>
		<?php if($adrotate_config['stats'] == 1) { ?>
			<th width="5%"><center><?php _e('Impressions', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('CTR', 'adrotate-pro'); ?></center></th>
		<?php } ?>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($disabledbanners as $banner) {
		if($adrotate_config['stats'] == 1) {
			$stat = adrotate_stats($banner['id']);
			$ctr = adrotate_ctr($stat['clicks'], $stat['impressions']);
		}
		
		$class = ('alternate' != $class) ? 'alternate' : '';
?>
	    <tr id='banner-<?php echo $banner['id']; ?>' class='<?php echo $class; ?>'>
			<td><center><?php echo $banner['id'];?></center></td>
			<td>
				<strong><?php if($advertiser_permissions['edit'] == 'Y') { ?><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes(html_entity_decode($banner['title']));?></a><?php } else { echo stripslashes(html_entity_decode($banner['title'])); } ?></strong>
			</td>
			<?php if($adrotate_config['stats'] == 1) { ?>
				<td><center><?php echo $stat['impressions']; ?></center></td>
				<td><center><?php echo $stat['clicks']; ?></center></td>
				<td><center><?php echo $ctr; ?> %</center></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>