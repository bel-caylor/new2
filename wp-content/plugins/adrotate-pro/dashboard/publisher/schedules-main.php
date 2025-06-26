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
<h2><?php _e('Manage Schedules', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Schedules are used to determine when adverts should show up. One schedule can be used by multiple adverts for example to create campaigns.', 'adrotate-pro'); ?></span>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-schedules">
	<?php wp_nonce_field('adrotate_bulk_schedules','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="schedule_delete"><?php _e('Delete', 'adrotate-pro'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>
		<br class="clear" />
	</div>

	<table class="widefat tablesorter manage-schedules-main" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
			<th width="4%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
			<th width="20%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
	        <th width="4%"><center><?php _e('Adverts', 'adrotate-pro'); ?></center></th>
	        <?php if($adrotate_config['stats'] == 1) { ?>
		        <th width="10%"><center><?php _e('Max Shown', 'adrotate-pro'); ?></center></th>
		        <th width="10%"><center><?php _e('Max Clicks', 'adrotate-pro'); ?></center></th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
	<?php
	$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' ORDER BY `id` ASC;");

	if(count($schedules) > 0) {
		$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
		$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';

		$class = '';
		foreach($schedules as $schedule) {
			$ads_use_schedule = $wpdb->get_results("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta`, `{$wpdb->prefix}adrotate` WHERE `group` = 0 AND `user` = 0 AND `schedule` = ".$schedule->id." AND `ad` =  `{$wpdb->prefix}adrotate`.`id`;");

			($class != 'alternate') ? $class = 'alternate' : $class = '';
			// Errors
			if($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') $class = 'row_yellow';
			if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions == 0) $class = 'row_yellow';
			if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions < 1000) $class = 'row_yellow';
			if($schedule->stoptime < $in2days) $class = 'row_orange';
			if($schedule->stoptime < $now) $class = 'row_red';
			if($schedule->day_mon == 'N' AND $schedule->day_tue == 'N' AND $schedule->day_wed == 'N' AND $schedule->day_thu == 'N' AND $schedule->day_fri == 'N' AND $schedule->day_sat == 'N' AND $schedule->day_sun == 'N') $class = 'row_yellow';

			$sdayhour = substr($schedule->daystarttime, 0, 2);
			$sdayminute = substr($schedule->daystarttime, 2, 2);
			$edayhour = substr($schedule->daystoptime, 0, 2);
			$edayminute = substr($schedule->daystoptime, 2, 2);

			if($adrotate_config['stats'] == 1) {
				if($schedule->maxclicks == 0) $schedule->maxclicks = '&infin;';
				if($schedule->maximpressions == 0) $schedule->maximpressions = '&infin;';
			}
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="schedulecheck[]" value="<?php echo $schedule->id; ?>" /></th>
				<td><center><?php echo $schedule->id;?></center></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
				<td><a href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=edit&schedule='.$schedule->id);?>"><?php echo stripslashes($schedule->name); ?></a><span style="color:#999;"><br /><?php _e('Mon:', 'adrotate-pro'); ?> <?php echo ($schedule->day_mon == 'Y') ? $tick : $cross; ?> <?php _e('Tue:', 'adrotate-pro'); ?> <?php echo ($schedule->day_tue == 'Y') ? $tick : $cross; ?> <?php _e('Wed:', 'adrotate-pro'); ?> <?php echo ($schedule->day_wed == 'Y') ? $tick : $cross; ?> <?php _e('Thu:', 'adrotate-pro'); ?> <?php echo ($schedule->day_thu == 'Y') ? $tick : $cross; ?> <?php _e('Fri:', 'adrotate-pro'); ?> <?php echo ($schedule->day_fri == 'Y') ? $tick : $cross; ?> <?php _e('Sat:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sat == 'Y') ? $tick : $cross; ?> <?php _e('Sun:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sun == 'Y') ? $tick : $cross; ?> <?php if($schedule->daystarttime  > 0) { ?><?php _e('Between:', 'adrotate-pro'); ?> <?php echo $sdayhour; ?>:<?php echo $sdayminute; ?> - <?php echo $edayhour; ?>:<?php echo $edayminute; ?> <?php } ?><br /><?php if($adrotate_config['stats'] == 1) { _e('Impression Spread:', 'adrotate-pro'); ?> <?php echo ($schedule->spread == 'Y') ? $tick : $cross; ?> <?php echo ($schedule->spread_all == 'Y') ? $tick : $cross; ?>, <?php } _e('Auto Delete:', 'adrotate-pro'); ?> <?php echo ($schedule->autodelete == 'Y') ? $tick : $cross; ?></span></td>
		        <td><center><?php echo count($ads_use_schedule); ?></center></td>
		        <?php if($adrotate_config['stats'] == 1) { ?>
			        <td><center><?php echo $schedule->maximpressions; ?></center></td>
			        <td><center><?php echo $schedule->maxclicks; ?></center></td>
				<?php } ?>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-schedules'>
			<th class="check-column">&nbsp;</th>
			<td colspan="<?php echo ($adrotate_config['stats'] == 1) ? '7' : '5'; ?>"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Warnings", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has expired.", "adrotate-pro"); ?>
</center></p>
</form>
