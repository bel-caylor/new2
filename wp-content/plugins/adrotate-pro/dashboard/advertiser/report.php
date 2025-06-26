<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$banner = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = '$ad_edit_id';");
$advertiser	= $wpdb->get_var($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $ad_edit_id, $current_user->ID));
$schedules = $wpdb->get_results("SELECT `starttime`, `stoptime`, `maxclicks`, `maximpressions`, COUNT(`clicks`) as `clicks`, COUNT(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta`, `{$wpdb->prefix}adrotate_stats` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`ad` = '$banner->id' AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` AND `thetime` > `starttime` AND `thetime` < `stoptime` ORDER BY `{$wpdb->prefix}adrotate_schedule`.`id` ASC;"); 

if($banner->id == $advertiser) {
	$stats = adrotate_stats($ad_edit_id, false);
	$stats_today = adrotate_stats($ad_edit_id, false, $today);
	$stats_last_month = adrotate_stats($ad_edit_id, false, mktime(0, 0, 0, date("m")-1, 1, date("Y")), mktime(0, 0, 0, date("m")-1, date("t"), date("Y")));
	$stats_this_month = adrotate_stats($ad_edit_id, false, mktime(0, 0, 0, date("m"), 1, date("Y")), mktime(0, 0, 0, date("m"), date("t"), date("Y")));
	$stats_graph_month = adrotate_stats($ad_edit_id, false, $monthstart, $monthend);

	// Prevent gaps in display
	if(empty($stats['impressions'])) $stats['impressions'] = 0;
	if(empty($stats['clicks']))	$stats['clicks'] = 0;
	if(empty($stats_today['impressions'])) $stats_today['impressions'] = 0;
	if(empty($stats_today['clicks'])) $stats_today['clicks'] = 0;
	if(empty($stats_last_month['impressions'])) $stats_last_month['impressions'] = 0;
	if(empty($stats_last_month['clicks'])) $stats_last_month['clicks'] = 0;
	if(empty($stats_this_month['impressions'])) $stats_this_month['impressions'] = 0;
	if(empty($stats_this_month['clicks'])) $stats_this_month['clicks'] = 0;
	if(empty($stats_graph_month['impressions'])) $stats_graph_month['impressions'] = 0;
	if(empty($stats_graph_month['clicks'])) $stats_graph_month['clicks'] = 0;

	// Get Click Through Rate
	$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);						
	$ctr_this_month = adrotate_ctr($stats_this_month['clicks'], $stats_this_month['impressions']);
	$ctr_graph_month = adrotate_ctr($stats_graph_month['clicks'], $stats_graph_month['impressions']);
?>
	
	<h3><?php _e('Statistics for', 'adrotate-pro'); ?> '<?php echo $banner->title; ?>'</h3>
	<table class="widefat" style="margin-top: .5em">
		<tbody>
	  	<tr>
	        <td width="20%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['impressions']; ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y" OR $banner->tracker == "C") { echo $stats['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('Impressions today', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_today['impressions']; ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('Clicks today', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y" OR $banner->tracker == "C") { echo $stats_today['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $ctr.' %'; } else { echo '--'; } ?></div></div></td>
	  	</tr>
	  	<tr>
	        <td width="20%"><div class="stats_large"><?php _e('Impressions last month', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_last_month['impressions']; ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('Clicks last month', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y" OR $banner->tracker == "C") { echo $stats_last_month['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td><div class="stats_large"><?php _e('Impressions this month', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_this_month['impressions']; ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('Clicks this month', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y" OR $banner->tracker == "C") { echo $stats_this_month['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="20%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $ctr_this_month.' %'; } else { echo '--'; } ?></div></div></td>
	  	</tr>
		<tbody>
	</table>
	
	<h3><?php _e('Monthly overview of clicks and impressions', 'adrotate-pro'); ?></h3>
	<table class="widefat" style="margin-top: .5em">	
		<tbody>
	  	<tr>
	        <th colspan="5">
	        	<div style="text-align:center;"><?php echo adrotate_stats_nav('advertiser', $ad_edit_id, $month, $year); ?></div>
	        	<?php echo adrotate_stats_graph('advertiser', false, $ad_edit_id, 1, $monthstart, $monthend); ?>
	        </th>
	  	</tr>
		<tr>
	        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['impressions']; ?></div></div></td>
	        <td width="33%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y" OR $banner->tracker == "C") { echo $stats_graph_month['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $ctr_graph_month.' %'; } else { echo '--'; } ?></div></div></td>
		</tr>
		</tbody>
	</table>

	<h3><?php _e('Periodic overview of clicks and impressions', 'adrotate-pro'); ?></h3>
	<table class="widefat" style="margin-top: .5em">	
		<thead>
		<tr>
	        <th><?php _e('Shown from', 'adrotate-pro'); ?></th>
	        <th colspan="2"><?php _e('Shown until', 'adrotate-pro'); ?></th>
	        <th><center><?php _e('Max Clicks', 'adrotate-pro'); ?> / <?php _e('Used', 'adrotate-pro'); ?></center></th>
	        <th><center><?php _e('Max Impressions', 'adrotate-pro'); ?> / <?php _e('Used', 'adrotate-pro'); ?></center></th>
		</tr>
		</thead>

		<tbody>
		<?php 
		foreach($schedules as $schedule) {
			$stats_schedule = adrotate_stats($banner->id, false, $schedule->starttime, $schedule->stoptime);
			if($schedule->maxclicks == 0) $schedule->maxclicks = '&infin;';
			if($schedule->maximpressions == 0) $schedule->maximpressions = '&infin;';
		?>
      	<tr id='schedule-<?php echo $schedule->id; ?>'>
	        <td><?php echo date_i18n("F d, Y - H:i", $schedule->starttime);?></td>
	        <td colspan="2"><?php echo date_i18n("F d, Y - H:i", $schedule->stoptime);?></td>
	        <td><center><?php echo $schedule->maxclicks; ?> / <?php echo $stats_schedule['clicks']; ?></center></td>
	        <td><center><?php echo $schedule->maximpressions; ?> / <?php echo $stats_schedule['impressions']; ?></center></td>
      	</tr>
      	<?php 
      		unset($stats_schedule);
      	} 
      	?>
      	</tbody>
	</table>

	<h3><?php _e('Preview', 'adrotate-pro'); ?></h3>
	<table class="widefat" style="margin-top: .5em">
		<tbody>
      	<tr>
	        <td colspan="5">
	        	<div><?php 
					$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);		
					echo adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image, 'N');		        	
				?></div>
		        <br /><em><?php _e('Note: While this preview is an accurate one, it might look different then it does on the website.', 'adrotate-pro'); ?>
				<br /><?php _e('This is because of CSS differences. The themes CSS file is not active here!', 'adrotate-pro'); ?></em>
			</td>
      	</tr>
      	</tbody>
	</table>
	
	
	<form method="post" action="admin.php?page=adrotate-advertiser&view=fullreport">
	<h3><?php _e('Export options', 'adrotate-pro'); ?></h3>
	<table class="widefat" style="margin-top: .5em">
	    <tbody>
	    <tr>
			<th width="10%"><?php _e('Select period', 'adrotate-pro'); ?></th>
			<td width="40%" colspan="4">
				<?php wp_nonce_field('adrotate_report_advertiser','adrotate_nonce'); ?>
		    	<input type="hidden" name="adrotate_export_id" value="<?php echo $ad_edit_id; ?>" />
				<input type="hidden" name="adrotate_export_type" value="advertiser-single" />
		        <select name="adrotate_export_month" id="cat" class="postform">
			        <option value="0"><?php _e('Whole year', 'adrotate-pro'); ?></option>
			        <option value="1" <?php if($month == "1") { echo 'selected'; } ?>><?php _e('January', 'adrotate-pro'); ?></option>
			        <option value="2" <?php if($month == "2") { echo 'selected'; } ?>><?php _e('February', 'adrotate-pro'); ?></option>
			        <option value="3" <?php if($month == "3") { echo 'selected'; } ?>><?php _e('March', 'adrotate-pro'); ?></option>
			        <option value="4" <?php if($month == "4") { echo 'selected'; } ?>><?php _e('April', 'adrotate-pro'); ?></option>
			        <option value="5" <?php if($month == "5") { echo 'selected'; } ?>><?php _e('May', 'adrotate-pro'); ?></option>
			        <option value="6" <?php if($month == "6") { echo 'selected'; } ?>><?php _e('June', 'adrotate-pro'); ?></option>
			        <option value="7" <?php if($month == "7") { echo 'selected'; } ?>><?php _e('July', 'adrotate-pro'); ?></option>
			        <option value="8" <?php if($month == "8") { echo 'selected'; } ?>><?php _e('August', 'adrotate-pro'); ?></option>
			        <option value="9" <?php if($month == "9") { echo 'selected'; } ?>><?php _e('September', 'adrotate-pro'); ?></option>
			        <option value="10" <?php if($month == "10") { echo 'selected'; } ?>><?php _e('October', 'adrotate-pro'); ?></option>
			        <option value="11" <?php if($month == "11") { echo 'selected'; } ?>><?php _e('November', 'adrotate-pro'); ?></option>
			        <option value="12" <?php if($month == "12") { echo 'selected'; } ?>><?php _e('December', 'adrotate-pro'); ?></option>
				</select> 
				<input type="text" name="adrotate_export_year" size="10" class="search-input" value="<?php echo date('Y'); ?>" autocomplete="off" />
			</td>
		</tr>
	    <tr>
			<th width="10%"><?php _e('Email options', 'adrotate-pro'); ?></th>
			<td width="40%" colspan="4">
	  			<input type="text" name="adrotate_export_addresses" size="45" class="search-input" value="" autocomplete="off" /> <em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate-pro'); ?></em>
			</td>
		</tr>
	    <tr>
			<th width="10%">&nbsp;</th>
			<td width="40%" colspan="4">
	  			<input type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate-pro'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate-pro'); ?></em>
			</td>
		</tr>
		</tbody>
	</table>
	</form>

	<p><em><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <em><?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro'); ?></em></p>
<?php } else { ?>
	<table class="widefat" style="margin-top: .5em">
		<thead>
			<tr>
				<th><?php _e('Notice', 'adrotate-pro'); ?></th>
			</tr>
		</thead>
		<tbody>
		    <tr>
				<td><?php _e('Invalid ad ID.', 'adrotate-pro'); ?></td>
			</tr>
		</tbody>
	</table>
<?php } ?>