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
<h2><?php _e('Summary', 'adrotate-pro'); ?></h2>
<p><em><?php _e('Overall statistics over active adverts', 'adrotate-pro'); ?></em></p>

<table class="widefat" style="margin-top: .5em">					

	<tbody>
	<tr>
        <td width="33%"><div class="stats_large"><?php _e('Adverts', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['ad_amount']; ?></div></div></td>
        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['total_impressions']; ?></div></div></td>
        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr; ?> %</div></div></td>
	</tr>
	<tr>
        <td colspan="3">
	        <div class="stats_large">
		        <?php _e('Best performing advert', 'adrotate-pro'); ?> 
		        <?php if($stats['thebest']) {
		        	echo '\''.$stats['thebest']['title'].'\' ';
		        	echo __('with', 'adrotate-pro').' '.$stats['thebest']['clicks'].' '.__('clicks.', 'adrotate-pro'); ?><br />
		        	<div style="margin: 10px;"><?php echo adrotate_preview($stats['thebest']['id']); ?></div>
		        <?php } else { ?>
		        	<br /><?php _e('No advert stands out at this time.', 'adrotate-pro'); ?>
				<?php } ?>
			</div>
        </td>
	</tr>
	<tr>
        <td colspan="3">
	        <div class="stats_large">
		        <?php _e('Least performing advert', 'adrotate-pro'); ?> 
		        <?php if($stats['theworst']) {
		        	echo '\''.$stats['theworst']['title'].'\' ';
		        	echo __('with', 'adrotate-pro').' '.$stats['theworst']['clicks'].' '.__('clicks.', 'adrotate-pro'); ?><br />
		        	<div style="margin: 10px;"><?php echo adrotate_preview($stats['theworst']['id']); ?></div>
		        <?php } else { ?>
		        	<br /><?php _e('No advert stands out at this time.', 'adrotate-pro'); ?>
				<?php } ?>
			</div>
		</td>
	</tr>
	</tbody>

</table>

<h2><?php _e('Monthly overview of clicks and impressions', 'adrotate-pro'); ?></h2>
<table class="widefat" style="margin-top: .5em">					

	<tbody>
  	<tr>
        <th colspan="3">
			<div style="text-align:center;"><?php echo adrotate_stats_nav('advertiserfull', 0, $month, $year); ?></div>
        	<?php echo adrotate_stats_graph('advertiserfull', false, $current_user->ID, 1, $monthstart, $monthend); ?>
        </th>
  	</tr>
	<tr>
        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['impressions']; ?></div></div></td>
        <td width="33%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['clicks']; ?></div></div></td>
        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_graph_month; ?> %</div></div></td>
	</tr>
	</tbody>

</table>

<h2><?php _e('Export options for montly overview', 'adrotate-pro'); ?></h2>
<form method="post" action="admin.php?page=adrotate-advertiser">
<?php wp_nonce_field('adrotate_export_advertiser','adrotate_nonce'); ?>
<input type="hidden" name="adrotate_export_id" value="<?php echo $current_user->ID; ?>" />
<input type="hidden" name="adrotate_export_type" value="advertiser" />

<?php 
$start_date	= adrotate_date_start('day');
$end_date = $start_date + (86400 * 7);
?>

<table class="widefat" style="margin-top: .5em">					

    <tbody>
    <tr>
		<th><?php _e('Select period', 'adrotate-pro'); ?></th>
		<td>
			<input tabindex="1" type="date" id="datepicker" name="adrotate_start_date" value="<?php echo gmdate("Y-m-d", $start_date); ?>" class="datepicker" /> / <input tabindex="2" type="date" id="datepicker" name="adrotate_end_date" value="<?php echo gmdate("Y-m-d", $end_date); ?>" class="datepicker" />
		</td>
	</tr>
    <tr>
		<th><?php _e('Email options', 'adrotate-pro'); ?></th>
		<td>
  			<input tabindex="3" type="text" name="adrotate_export_addresses" size="45" class="search-input" value="" autocomplete="off" /> <em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate-pro'); ?></em>
		</td>
	</tr>
    <tr>
		<th>&nbsp;</th>
		<td>
  			<input tabindex="4" type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate-pro'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate-pro'); ?></em>
		</td>
	</tr>
	</tbody>

</table>
</form>