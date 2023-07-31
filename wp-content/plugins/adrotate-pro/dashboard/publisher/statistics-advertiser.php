<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2022 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

$user = get_user_by('id', $id);

$stats = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad`;", ARRAY_A);
$stats_today = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`,  `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `thetime` = {$today};", ARRAY_A);

$start_last_month = gmmktime(0, 0, 0, gmdate("m")-1, 1, gmdate("Y"));
$end_last_month = gmmktime(0, 0, 0, gmdate("m")-1, gmdate("t"), gmdate("Y"));
$stats_last_month = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `thetime` >= {$start_last_month} AND `thetime` <= {$end_last_month};", ARRAY_A);

$start_this_month = gmmktime(0, 0, 0, gmdate("m"), 1, gmdate("Y"));
$end_this_month = gmmktime(0, 0, 0, gmdate("m"), gmdate("t"), gmdate("Y"));
$stats_last_month = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `thetime` >= {$start_this_month} AND `thetime` <= {$end_this_month};", ARRAY_A);

$stats_graph_month = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `thetime` >= {$graph_start_date} AND `thetime` <= {$graph_end_date};", ARRAY_A);

$ads = $wpdb->get_results("SELECT `{$wpdb->prefix}adrotate`.`id`, `title`, `weight`, `crate`, `budget`, `irate` FROM `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE (`type` != 'empty' AND `type` != 'a_empty' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'generator') AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = {$id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate`.`id` ORDER BY `{$wpdb->prefix}adrotate`.`id` ASC;");

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
$ctr_today = adrotate_ctr($stats_today['clicks'], $stats_today['impressions']);
$ctr_last_month = adrotate_ctr($stats_last_month['clicks'], $stats_last_month['impressions']);
$ctr_this_month = adrotate_ctr($stats_this_month['clicks'], $stats_this_month['impressions']);
$ctr_graph_month = adrotate_ctr($stats_graph_month['clicks'], $stats_graph_month['impressions']);
?>

<h2><?php _e('Overview for', 'adrotate-pro'); ?> '<?php echo stripslashes($user->display_name); ?>'</h2>
<table class="widefat" style="margin-top: .5em">

	<thead>
  	<tr>
        <th colspan="3"><center><strong><?php _e('Today', 'adrotate-pro'); ?></strong></center></th>
        <th>&nbsp;</th>
		<th colspan="3"><center><strong><?php _e('All time', 'adrotate-pro'); ?></strong></center></th>
  	</tr>
	</thead>
	<tbody>
  	<tr>
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_today['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_today['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_today.' %'; ?></div></div></td>

		<td>&nbsp;</td>
 
		<td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr.' %'; ?></div></div></td>
  	</tr>
	</tbody>
	<thead>
  	<tr>
        <th colspan="3"><center><strong><?php _e('Last month', 'adrotate-pro'); ?></strong></center></th>
        <th>&nbsp;</th>
        <th colspan="3"><center><strong><?php _e('This month', 'adrotate-pro'); ?></strong></center></th>
  	</tr>
	</thead>
	<tbody>
  	<tr>
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_last_month['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_last_month['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_last_month.' %'; ?></div></div></td>

        <td>&nbsp;</td>
 
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_this_month['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_this_month['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_this_month.' %'; ?></div></div></td>
  	</tr>
	<tbody>

</table>

<h2><?php _e('Adverts', 'adrotate-pro'); ?></h2>
<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Name', 'adrotate-pro'); ?></th>
		<th width="15%"><?php _e('Visible until', 'adrotate-pro'); ?></th>
		<th width="5%"><center><?php _e('Weight', 'adrotate-pro'); ?></center></th>
		<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
		<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
	</tr>
	</thead>

	<tbody>
	<?php
	$class = '';
	foreach($ads as $ad) {
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		$stats = adrotate_stats($ad->id);

		$errorclass = '';
		if($stoptime <= $in2days OR $stoptime <= $in7days) $errorclass = ' row_orange';
		if($stoptime <= $now OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget == 0)) $errorclass = ' row_red';

		$class = ('alternate' != $class) ? 'alternate' : '';
		$class = ($errorclass != '') ? $errorclass : $class;
		?>
	    <tr class='<?php echo $class; ?>'>
			<td><center><?php echo $ad->id; ?></center></td>
			<td><?php echo stripslashes($ad->title); ?> - <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=advert&id='.$ad->id);?>" title="<?php _e('More stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a></td>
			<td><span style="color: <?php echo adrotate_prepare_color($stoptime);?>;"><?php echo date_i18n("F d, Y", $stoptime); ?></span></td>
			<td><center><?php echo $ad->weight; ?></center></td>
			<td><center><?php echo $stats['impressions']; ?></center></td>
			<td><center><?php echo $stats['clicks']; ?></center></td>
		</tr>
		<?php unset($stoptime, $stats);?>
	<?php } ?>
	</tbody>					
</table>

<p><center>
	<span><span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expired", "adrotate-pro"); ?>
</center></p>

<h2><?php _e('Clicks and Impressions', 'adrotate-pro'); ?></h2>
<form method="get" action="admin.php">
	<table class="widefat" style="margin-top: .5em">
		<tbody>
		<tr>
	        <td colspan="3">
					<input type="hidden" name="page" value="adrotate-statistics" />
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<input type="hidden" name="view" value="group" />
					<?php _e('Period', 'adrotate-pro'); ?> (dd-mm-yyyy): 
					
					<input tabindex="2" type="text" id="startdate_graph_picker" name="graph_start" value="<?php echo gmdate("d-m-Y", $graph_start_date); ?>" class="datepicker ajdg-datepicker ajdg-inputfield" autocomplete="off" />
					<input tabindex="3" type="text" id="enddate_graph_picker" name="graph_end" value="<?php echo gmdate("d-m-Y", $graph_end_date); ?>" class="datepicker ajdg-datepicker ajdg-inputfield" autocomplete="off" />
					<input tabindex="4" type="submit" name="graph_submit" class="button-secondary" value="<?php _e('Go', 'adrotate-pro'); ?>" /> <em>The maximum range is about 3 months, larger ranges will make the graph unreadable.</em>
			</td>
	  	</tr>	
		<tr>
	        <th colspan="3">
				<?php echo adrotate_stats_graph('advertiser', false, $id, 1, $graph_start_date, $graph_end_date); ?>
	        </th>
		</tr>
		<tr>
	        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['impressions']; ?></div></div></td>
	        <td width="33%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['clicks']; ?></div></div></td>
	        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_graph_month; ?> %</div></div></td>
		</tr>
		</tbody>

	</table>
</form>

<h2><?php _e('Export options', 'adrotate-pro'); ?></h2>
<form method="post" action="admin.php?page=adrotate-groups">
	<?php wp_nonce_field('adrotate_export_groups','adrotate_nonce'); ?>
	<input type="hidden" name="adrotate_export_id" value="<?php echo $id; ?>" />
	<input type="hidden" name="adrotate_export_type" value="group" />
	
	<?php 
	$end_date	= adrotate_date_start('day');
	$start_date = $end_date - (86400 * 14);
	?>

	<table class="widefat" style="margin-top: .5em">
	
		<tbody>
		<tr>
	        <th width="15%"><?php _e('From', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
	        <td width="35%">
				<input tabindex="2" type="text" id="startdate_picker" name="adrotate_start_date" value="<?php echo gmdate("d-m-Y", $start_date); ?>" class="ajdg-datepicker datepicker" />
	        </td>
	        <th width="15%"><?php _e('Until', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
	        <td>
				<input tabindex="3" type="text" id="enddate_picker" name="adrotate_end_date" value="<?php echo gmdate("d-m-Y", $end_date); ?>" class="ajdg-datepicker datepicker" />
			</td>
	  	</tr>	
	    <tr>
			<th><?php _e('Export options', 'adrotate-pro'); ?></th>
			<td>
		        <label for="adrotate_export_format">
			        <select tabindex="4" name="adrotate_export_format">
						<option value="default"><?php _e('Default, total number of impressions and clicks per day', 'adrotate-pro'); ?></option>
						<option value="individual"><?php _e('Individual, total number of impressions and clicks per advert', 'adrotate-pro'); ?></option>
					</select>
				</label>
			</td>
			<td colspan="2">
	  			<em><?php _e('Select how you want your stats formatted.', 'adrotate-pro'); ?></em>
			</td>
		</tr>
	    <tr>
			<th><?php _e('Email options', 'adrotate-pro'); ?></th>
			<td>
	  			<input tabindex="5" type="text" name="adrotate_export_addresses" size="45" class="ajdg-inputfield" value="" autocomplete="off" />
			</td>
			<td colspan="2">
	  			<em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate-pro'); ?></em>
			</td>
		</tr>
		</tbody>
	
	</table>
	
	<p class="submit">
		<input tabindex="6" type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate-pro'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate-pro'); ?></em>
	</p>
</form>

<p><center>
	<em><small><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro'); ?></small></em>
</center></p>