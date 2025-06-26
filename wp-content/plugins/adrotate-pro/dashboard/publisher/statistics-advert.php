<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$banner = $wpdb->get_row("SELECT `title`, `tracker`, `type` FROM `{$wpdb->prefix}adrotate` WHERE `id` = '{$id}';");

// Determine archived or not
$archive = ($banner->type == "archived") ? true : false;

$table = 'adrotate_stats';
if($archive) {
	$table = 'adrotate_stats_archive';
}

$stats = adrotate_stats($id, $archive);
$stats_today = adrotate_stats($id, $archive, adrotate_date_start('day'), null, 1);
$stats_last_month = adrotate_stats($id, $archive, gmmktime(0, 0, 0, gmdate("m")-1, 1, gmdate("Y")), gmmktime(0, 0, 0, gmdate("m"), 0, gmdate("Y")));
$stats_this_month = adrotate_stats($id, $archive, gmmktime(0, 0, 0, gmdate("m"), 1, gmdate("Y")), gmmktime(0, 0, 0, gmdate("m"), gmdate("t"), gmdate("Y")));
$stats_graph_month = adrotate_stats($id, $archive, $graph_start_date, $graph_end_date);

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
<h2><?php _e('Statistics for advert', 'adrotate-pro'); ?> '<?php echo stripslashes($banner->title); ?>'</h2>
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
 
		 <td><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['impressions']; ?></div></div></td>
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

<h2><?php _e('Overview of clicks and impressions', 'adrotate-pro'); ?></h2>
<form method="get" action="admin.php">
	<table class="widefat" style="margin-top: .5em">
		<tbody>
		<tr>
	        <td colspan="3">
					<input type="hidden" name="page" value="adrotate-statistics" />
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<input type="hidden" name="view" value="advert" />
					<?php _e('Period', 'adrotate-pro'); ?> (dd-mm-yyyy): 
					
					<input tabindex="2" type="text" id="startdate_graph_picker" name="graph_start" value="<?php echo gmdate("d-m-Y", $graph_start_date); ?>" class="datepicker ajdg-datepicker ajdg-inputfield" autocomplete="off" />
					<input tabindex="3" type="text" id="enddate_graph_picker" name="graph_end" value="<?php echo gmdate("d-m-Y", $graph_end_date); ?>" class="datepicker ajdg-datepicker ajdg-inputfield" autocomplete="off" />
					<input tabindex="4" type="submit" name="graph_submit" class="button-secondary" value="<?php _e('Go', 'adrotate-pro'); ?>" /> <em>The maximum range is about 3 months, larger ranges will make the graph unreadable.</em>
			</td>
	  	</tr>	
		<tr>
	        <th colspan="3">
				<?php echo adrotate_stats_graph('ads', $archive, $id, 1, $graph_start_date, $graph_end_date); ?>
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
<form method="post" action="admin.php?page=adrotate">
	<?php wp_nonce_field('adrotate_export_ads','adrotate_nonce'); ?>
	<input type="hidden" name="adrotate_export_id" value="<?php echo $id; ?>" />
	<input type="hidden" name="adrotate_export_type" value="single" />
	
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
			<th><?php _e('Email options', 'adrotate-pro'); ?></th>
			<td>
	  			<input tabindex="3" type="text" name="adrotate_export_addresses" size="45" class="ajdg-inputfield" value="" autocomplete="off" />
			</td>
			<td colspan="2">
	  			<em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate-pro'); ?></em>
			</td>
		</tr>
		</tbody>
	
	</table>
	
	<p class="submit">
		<input tabindex="4" type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate-pro'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate-pro'); ?></em>
	</p>
</form>

<p><center>
	<em><small><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro'); ?></small></em>
</center></p>