<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

if(!$schedule_edit_id) { ?>
	<h2><?php _e('New Schedule', 'adrotate-pro'); ?></h2>
<?php
	$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` = '' ORDER BY `id` DESC LIMIT 1;");
	if($edit_id == 0) {
		$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => '', 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'spread_all' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y', 'autodelete' => 'N'));
	    $edit_id = $wpdb->insert_id;
	}
	$schedule_edit_id = $edit_id;
} else { ?>
	<h2><?php _e('Edit Schedule', 'adrotate-pro'); ?></h2>
<?php
}

$edit_schedule = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = '{$schedule_edit_id}';");

if($edit_schedule) {
	$ads = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `desktop`, `mobile`, `tablet`, `weight`, `crate`, `budget`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE (`type` != 'empty' AND `type` != 'a_empty' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'generator') ORDER BY `id` ASC;");
	$linkmeta = $wpdb->get_results("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = '{$schedule_edit_id}' AND `group` = 0 AND `user` = 0;");

	$class = '';
	$meta_array = array();
	foreach($linkmeta as $meta) {
		$meta_array[] = $meta->ad;
	}

	if(!empty($edit_schedule->name)) {
		list($start_day, $start_month, $start_year, $start_hour, $start_minute) = explode(" ", date("d m Y H i", $edit_schedule->starttime));
		list($end_day, $end_month, $end_year, $end_hour, $end_minute) = explode(" ", date("d m Y H i", $edit_schedule->stoptime));

		$start_day_hour = substr($edit_schedule->daystarttime, 0, 2);
		$start_day_minute = substr($edit_schedule->daystarttime, 2, 2);
		$end_day_hour = substr($edit_schedule->daystoptime, 0, 2);
		$end_day_minute = substr($edit_schedule->daystoptime, 2, 2);
	} else {
		list($start_day, $start_month, $start_year) = explode(" ", date("d m Y", $now));
		list($end_day, $end_month, $end_year) = explode(" ", date("d m Y", $in84days));

		$start_hour = $start_minute = $end_hour = $end_minute = '00';
		$start_day_hour = $start_day_minute = $end_day_hour = $end_day_minute = '00';
	}
	$start_date = $start_day.'-'.$start_month.'-'.$start_year;
	$end_date = $end_day.'-'.$end_month.'-'.$end_year;

	if($schedule_edit_id AND $edit_schedule->name != '') {
		// Errors
		if($edit_schedule->stoptime < $now)
			echo '<div class="error"><p>'. __('This schedule has expired!', 'adrotate-pro').'</p></div>';

		if($edit_schedule->stoptime > $now AND $edit_schedule->stoptime < $in2days)
			echo '<div class="error"><p>'. __('This schedule is almost expired!', 'adrotate-pro').'</p></div>';

		if($edit_schedule->day_mon == 'N' AND $edit_schedule->day_tue == 'N' AND $edit_schedule->day_wed == 'N' AND $edit_schedule->day_thu == 'N' AND $edit_schedule->day_fri == 'N' AND $edit_schedule->day_sat == 'N' AND $edit_schedule->day_sun == 'N')
			echo '<div class="error"><p>'. __('This schedule has all weekdays disabled. Enable at least one day of the week.', 'adrotate-pro').'</p></div>';

		if($edit_schedule->maximpressions < 1000 AND ($edit_schedule->spread == 'Y' OR $edit_schedule->spread_all == 'Y'))
			echo '<div class="error"><p>'. __('Maximum Impressions is set very low. This may cause adverts to intermittently not show. Raise the impression limit or shorten the schedule time period.', 'adrotate-pro').'</p></div>';

		if($edit_schedule->maximpressions == 0 AND ($edit_schedule->spread == 'Y' OR $edit_schedule->spread_all == 'Y'))
			echo '<div class="error"><p>'. __('Maximum Impressions is not configured. No adverts will show. Raise the impression limit or disable both spread options.', 'adrotate-pro').'</p></div>';

		if($edit_schedule->spread == 'Y' AND $edit_schedule->spread_all == 'Y')
			echo '<div class="error"><p>'. __('You have both Impression spread methods enabled. This is not recommended and renders the advert spread ineffective.', 'adrotate-pro').'</p></div>';
	}
	?>
	<p><em><?php _e('Time uses a 24 hour clock. When you are used to AM/PM: If the start or end time is after lunch, add 12 hours. 2PM is 14:00 hours. 6AM is 6:00 hours.', 'adrotate-pro'); ?></em></p>

	<form method="post" action="admin.php?page=adrotate-schedules">
		<?php wp_nonce_field('adrotate_save_schedule','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_schedule->id;?>" />

		<table class="widefat" style="margin-top: .5em">
			<tbody>
	      	<tr>
	      		<th width="15%"><?php _e('Name', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_schedulename"><input tabindex="1" name="adrotate_schedulename" type="text" class="ajdg-fullwidth ajdg-inputfield" size="50" value="<?php echo stripslashes($edit_schedule->name); ?>" autocomplete="off" /></label>
				</td>
		        <td colspan="2">
		        	<em><?php _e('Visible on the Advertiser dashboard!', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<tr>
		        <th><?php _e('Start date', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
		        <td width="35%">
					<input tabindex="2" type="text" id="startdate_picker" name="adrotate_start_date" value="<?php echo $start_date; ?>" class="datepicker ajdg-datepicker ajdg-inputfield" />
		        </td>
		        <th width="15%"><?php _e('End date', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
		        <td>
					<input tabindex="3" type="text" id="enddate_picker" name="adrotate_end_date" value="<?php echo $end_date; ?>" class="datepicker ajdg-datepicker ajdg-inputfield" />
				</td>
	      	</tr>
			<tr>
		        <th><?php _e('Start time', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td>
		        	<label for="adrotate_sday">
					<input tabindex="4" name="adrotate_start_hour" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="<?php echo $start_hour; ?>" /> :
					<input tabindex="5" name="adrotate_start_minute" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="<?php echo $start_minute; ?>" />
					</label>
		        </td>
		        <th><?php _e('End time', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td>
		        	<label for="adrotate_eday">
					<input tabindex="6" name="adrotate_end_hour" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="<?php echo $end_hour; ?>" /> :
					<input tabindex="7" name="adrotate_end_minute" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="<?php echo $end_minute; ?>" />
					</label>
				</td>
	      	</tr>
			</tbody>
		</table>

		<h2><?php _e('Advanced', 'adrotate-pro'); ?></h2>
		<p><em><?php _e('Everything below is optional.', 'adrotate-pro'); ?> <?php _e('These settings may cause adverts to intermittently not show. Use with care!', 'adrotate-pro'); ?><br /><?php _e('The maximum clicks and impressions are measured only for the schedule that has the limit set up. And applies to each individual advert in the group.', 'adrotate-pro'); ?></em></p>

		<table class="widefat" style="margin-top: .5em">
			<tbody>

	      	<tr>
		        <th width="15%" valign="top"><?php _e('Show only on', 'adrotate-pro'); ?></th>
		        <td colspan="3">
			        <table width="100%">
				        <tr>
							<td width="14%"><label for="adrotate_mon"><center><input tabindex="8" type="checkbox" name="adrotate_mon" id="adrotate_mon" value="1" <?php if($edit_schedule->day_mon == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Monday', 'adrotate-pro'); ?></center></label></td>
							<td width="14%"><label for="adrotate_tue"><center><input tabindex="9" type="checkbox" name="adrotate_tue" id="adrotate_tue" value="1" <?php if($edit_schedule->day_tue == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Tuesday', 'adrotate-pro'); ?></center></label></td>
							<td width="14%"><label for="adrotate_wed"><center><input tabindex="10" type="checkbox" name="adrotate_wed" id="adrotate_wed" value="1" <?php if($edit_schedule->day_wed == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Wednesday', 'adrotate-pro'); ?></center></label></td>
							<td width="14%"><label for="adrotate_thu"><center><input tabindex="11" type="checkbox" name="adrotate_thu" id="adrotate_thu" value="1" <?php if($edit_schedule->day_thu == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Thursday', 'adrotate-pro'); ?></center></label></td>
							<td width="14%"><label for="adrotate_fri"><center><input tabindex="12" type="checkbox" name="adrotate_fri" id="adrotate_fri" value="1" <?php if($edit_schedule->day_fri == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Friday', 'adrotate-pro'); ?></center></label></td>
							<td width="14%"><label for="adrotate_sat"><center><input tabindex="13" type="checkbox" name="adrotate_sat" id="adrotate_sat" value="1" <?php if($edit_schedule->day_sat == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Saturday', 'adrotate-pro'); ?></center></label></td>
							<td><label for="adrotate_sun"><center><input tabindex="14" type="checkbox" name="adrotate_sun" id="adrotate_sun" value="1" <?php if($edit_schedule->day_sun == 'Y') { ?>checked<?php } ?> /><br /><?php _e('Sunday', 'adrotate-pro'); ?></center></label></td>
				        </tr>
			        </table>
		        </td>
	      	</tr>
	      	<tr>
		        <th><?php _e('Daily start at', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td width="35%">
		        	<label for="adrotate_sday">
					<input tabindex="15" name="adrotate_start_day_hour" class="ajdg-inputfield" type="text" size="2" maxlength="2" value="<?php echo $start_day_hour;?>" /> :
					<input tabindex="16" name="adrotate_start_day_minute" class="ajdg-inputfield" type="text" size="2" maxlength="2" value="<?php echo $start_day_minute;?>" />
					</label>
		        </td>
		        <th width="15%"><?php _e('End on', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td>
		        	<label for="adrotate_eday">
					<input tabindex="17" name="adrotate_end_day_hour" class="ajdg-inputfield" type="text" size="2" maxlength="2" value="<?php echo $end_day_hour;?>" /> :
					<input tabindex="18" name="adrotate_end_day_minute" class="ajdg-inputfield" type="text" size="2" maxlength="2" value="<?php echo $end_day_minute;?>" />
					</label>
				</td>
	      	</tr>
			<?php if($adrotate_config['stats'] == 1) { ?>
	      	<tr>
	      		<th><?php _e('Maximum Clicks', 'adrotate-pro'); ?></th>
		        <td><input tabindex="19" name="adrotate_maxclicks" type="text" size="5" class="ajdg-inputfield" autocomplete="off" value="<?php echo $edit_schedule->maxclicks; ?>" /> <em><?php _e('Leave empty or 0 to skip this.', 'adrotate-pro'); ?></em></td>
			    <th><?php _e('Maximum Impressions', 'adrotate-pro'); ?></th>
		        <td><input tabindex="20" name="adrotate_maxshown" type="text" size="5" class="ajdg-inputfield" autocomplete="off" value="<?php echo $edit_schedule->maximpressions; ?>" /> <em><?php _e('Leave empty or 0 to skip this.', 'adrotate-pro'); ?></em></td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Spread Impressions', 'adrotate-pro'); ?></th>
				<td colspan="3">
					<label for="adrotate_spread"><input tabindex="21" type="checkbox" name="adrotate_spread" id="adrotate_spread" value="1" <?php if($edit_schedule->spread == 'Y') { ?>checked<?php } ?> /> <?php _e('Evenly spread impressions for each advert over the duration of this schedule. This is useful for individual adverts having similar restrictions.', 'adrotate-pro'); ?></label><br />
					<label for="adrotate_spread_all"><input tabindex="22" type="checkbox" name="adrotate_spread_all" id="adrotate_spread_all" value="1" <?php if($edit_schedule->spread_all == 'Y') { ?>checked<?php } ?> /> <?php _e('Evenly spread impressions for all adverts using this schedule, this is useful for campaigns.', 'adrotate-pro'); ?></label><br />
					<em><?php _e('Make sure all applicable adverts have Impression counting enabled. You should not enable both spread options at the same time.', 'adrotate-pro'); ?><br />
					<em><?php _e('Impression spread works better if you configure the Maximum Impressions with a high number. Several thousand at the very least.', 'adrotate-pro'); ?></td>
			</tr>
			<?php } ?>
	     	<tr>
		        <th valign="top"><?php _e('Auto-delete', 'adrotate-pro'); ?></th>
		        <td colspan="3">
		        	<label for="adrotate_autodelete"><input tabindex="22" type="checkbox" name="adrotate_autodelete" id="adrotate_autodelete" <?php if($edit_schedule->autodelete == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Automatically delete the schedule 1 day after it expires?', 'adrotate-pro'); ?></label><br /><em><?php _e('This is useful for short running campaigns that do not require attention after they finish.', 'adrotate-pro'); ?></em>
		        </td>
			</tr>
			</tbody>

		</table>

		<?php if($adrotate_config['hide_schedules'] == "Y") { ?>
		<p><em><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e("Adverts hide schedules that are not used by that advert.", "adrotate-pro"); ?></em></p>
		<?php } ?>

		<p class="submit">
			<input tabindex="23" type="submit" name="adrotate_schedule_submit" class="button-primary" value="<?php _e('Save Schedule', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-schedules" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>

		<h2><?php _e('Select Adverts', 'adrotate-pro'); ?></h2>
	   	<table class="widefat" style="margin-top: .5em">
			<thead>
			<tr>
				<td width="2%" scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
				<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
				<th><?php _e('Name', 'adrotate-pro'); ?></th>
				<th width="5%"><center><?php _e('Device', 'adrotate-pro'); ?></center></th>
				<th width="15%"><?php _e('Visible until', 'adrotate-pro'); ?></th>
				<th width="5%"><center><?php _e('Weight', 'adrotate-pro'); ?></center></th>
		        <?php if($adrotate_config['stats'] == 1) { ?>
					<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
					<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
				<?php } ?>
			</tr>
			</thead>

			<tbody>
			<?php if($ads) {
				$class = '';
				foreach($ads as $ad) {
					$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

					$errorclass = '';
					if($ad->type == 'error' OR $ad->type == 'a_error') $errorclass = ' row_yellow';
					if($stoptime <= $in2days OR $stoptime <= $in7days) $errorclass = ' row_orange';
					if($stoptime <= $now OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget == 0)) $errorclass = ' row_red';

					if($adrotate_config['stats'] == 1) {
						$stats = adrotate_stats($ad->id);
					}

					$class = ('alternate' != $class) ? 'alternate' : '';
					$class = ($errorclass != '') ? $errorclass : $class;

					$mobile = '';
					if($ad->desktop == 'Y') {
						$mobile .= '<img src="'.plugins_url('../../images/desktop.png', __FILE__).'" width="12" height="12" title="Desktop" />';
					}
					if($ad->mobile == 'Y') {
						$mobile .= '<img src="'.plugins_url('../../images/mobile.png', __FILE__).'" width="12" height="12" title="Mobile" />';
					}
					if($ad->tablet == 'Y') {
						$mobile .= '<img src="'.plugins_url('../../images/tablet.png', __FILE__).'" width="12" height="12" title="Tablet" />';
					}
					?>
				    <tr class='<?php echo $class; ?>'>
						<th class="check-column"><input type="checkbox" name="adselect[]" value="<?php echo $ad->id; ?>" <?php if(in_array($ad->id, $meta_array)) echo "checked"; ?> /></th>
						<td><center><?php echo $ad->id; ?></center></td>
						<td><?php echo stripslashes($ad->title); ?></td>
						<td><center><?php echo $mobile; ?></center></td>
						<td><span style="color: <?php echo adrotate_prepare_color($stoptime);?>;"><?php echo date_i18n("F d, Y", $stoptime); ?></span></td>
						<td><center><?php echo $ad->weight; ?></center></td>
						<?php if($adrotate_config['stats'] == 1) { ?>
							<td><center><?php echo $stats['impressions']; ?></center></td>
							<td><center><?php if($ad->tracker != 'N') { echo $stats['clicks']; } else { ?>--<?php } ?></center></td>
						<?php } ?>
					</tr>
				<?php unset($stats);?>
	 			<?php } ?>
			<?php } else { ?>
			<tr>
				<th class="check-column">&nbsp;</th>
				<td colspan="<?php echo ($adrotate_config['stats'] == 1) ? '6' : '4';?>"><em><?php _e('No adverts created!', 'adrotate-pro'); ?></em></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>

		<p><center>
			<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Configuration errors", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?>
		</center></p>

		<p class="submit">
			<input tabindex="24" type="submit" name="adrotate_schedule_submit" class="button-primary" value="<?php _e('Save Schedule', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-schedules" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
	</form>
<?php
} else {
	echo adrotate_error('error_loading_item');
}
?>
