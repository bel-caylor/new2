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
<h2><?php _e('Manage Groups', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Groups are usually used as locations. This lets you manage all of your adverts from your dashboard without having to re-edit many posts, pages or widgest/blocks when adverts expire or need replacing.', 'adrotate-pro'); ?></span>

<form name="groups" id="post" method="post" action="admin.php?page=adrotate-groups">
	<?php wp_nonce_field('adrotate_bulk_groups','adrotate_nonce'); ?>

	<div class="tablenav">
		<div class="alignleft">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="group_delete"><?php _e('Delete Group', 'adrotate-pro'); ?></option>
				<option value="group_delete_banners"><?php _e('Delete Group including adverts', 'adrotate-pro'); ?></option>
			</select>
			<input onclick="return confirm('<?php _e('You are about to delete a group', 'adrotate-pro'); ?>\n<?php _e('This action can not be undone!', 'adrotate-pro'); ?>\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate-pro'); ?>')" type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>
	</div>

   	<table class="widefat tablesorter manage-groups-main" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column">&nbsp;</td>
			<th width="5%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
			<th width="5%"><center><?php _e('Adverts', 'adrotate-pro'); ?></center></th>
			<th width="5%"><center><?php _e('Active', 'adrotate-pro'); ?></center></th>
	        <?php if($adrotate_config['stats'] == 1) { ?>
				<th width="5%"><center><?php _e('Shown', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Clicks', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Today', 'adrotate-pro'); ?></center></th>
			<?php } ?>
		</tr>
		</thead>

		<tbody>

		<?php 
		$groups = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");

		if(count($groups) > 0) {
			$class = '';
			$modus = array();
			foreach($groups as $group) {
				if($adrotate_config['stats'] == 1) {
					$stats = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `group` = {$group->id};", ARRAY_A);
					$stats_today = $wpdb->get_row("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `group` = {$group->id} AND `thetime` = {$today};", ARRAY_A);
					if(empty($stats['impressions'])) $stats['impressions'] = 0;
					if(empty($stats['clicks']))	$stats['clicks'] = 0;
					if(empty($stats_today['impressions'])) $stats_today['impressions'] = 0;
					if(empty($stats_today['clicks'])) $stats_today['clicks'] = 0;
				}

				if($group->adspeed > 0) $adspeed = $group->adspeed / 1000;
		        if($group->modus == 0) $modus[] = __('Default', 'adrotate-pro');
		        if($group->modus == 1) $modus[] = __('Dynamic', 'adrotate-pro').' ('.$adspeed.' '. __('second rotation', 'adrotate-pro').')';
		        if($group->modus == 2) $modus[] = __('Block', 'adrotate-pro').' ('.$group->gridrows.' x '.$group->gridcolumns.' '. __('grid', 'adrotate-pro').')';
				if(is_numeric($group->adwidth) AND is_numeric($group->adheight) AND $group->adwidth > 0 AND $group->adheight > 0) $modus[] = $group->adwidth.'x'.$group->adheight.'px';
		        if($group->cat_loc > 0 OR $group->page_loc > 0 OR $group->woo_loc > 0 OR $group->bbpress_loc > 0) $modus[] = __('Post Injection', 'adrotate-pro');
		        if($group->geo == 1 AND $adrotate_config['enable_geo'] > 0) $modus[] = __('Geolocation', 'adrotate-pro');
		        if($group->mobile == 1) $modus[] = __('Mobile', 'adrotate-pro');
		        if($group->fallback > 0) $modus[] = __('Fallback', 'adrotate-pro').' ('.__('Group', 'adrotate-pro').' '.$group->fallback.')';
		        if($group->network > 0) $modus[] = __('Network', 'adrotate-pro').' ('.__('Group', 'adrotate-pro').' '.$group->network.')';

				$ads_in_group = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = {$group->id};");
				$active_ads_in_group = $wpdb->get_var("SELECT COUNT(*) FROM  `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days') AND `group` = {$group->id};");

				if(adrotate_is_networked() AND $license['type'] == 'Developer' AND $group->network > 0) {
					$current_blog = $wpdb->blogid;
					switch_to_blog($network['primary']);
			
					$ads_in_network_group = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = {$group->network};");
					$active_ads_in_network_group = $wpdb->get_var("SELECT COUNT(*) FROM  `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days') AND `group` = {$group->network};");
			
					$ads_in_group += $ads_in_network_group;
					$active_ads_in_group += $active_ads_in_network_group;
			
					unset($ads_in_network_group, $active_ads_in_network_group);
			
					switch_to_blog($current_blog);
				}

				$class = ('alternate' != $class) ? 'alternate' : '';
				// Errors
				if($group->modus == 2 AND $group->gridrows == 1 AND $group->gridcolumns == 1)  $class = 'row_yellow';
				if($group->cat_loc > 0 AND strlen($group->cat) == 0) $class = 'row_yellow';
				if($group->page_loc > 0 AND strlen($group->page) == 0) $class = 'row_yellow';
				if($group->woo_loc > 0 AND strlen($group->woo_cat) == 0) $class = 'row_yellow';
				if($group->bbpress_loc > 0 AND strlen($group->bbpress) == 0) $class = 'row_yellow';
				?>
			    <tr class='<?php echo $class; ?>'>
					<th class="check-column"><input type="checkbox" name="groupcheck[]" value="<?php echo $group->id; ?>" /></th>
					<td><center><?php echo $group->id;?></center></td>
					<td>
						<strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=edit&group='.$group->id);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes($group->name);?></a></strong> <?php if($adrotate_config['stats'] == 1) { ?>- <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=group&id='.$group->id);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a><?php } ?>
						<span style="color:#999;">
							<?php echo '<br /><span style="font-weight:bold;">'.__('Mode', 'adrotate-pro').':</span> '.implode(', ', $modus); ?>
						</span>
					</td>
					<td><center><?php echo $ads_in_group; ?></center></td>
					<td><center><?php echo $active_ads_in_group; ?></center></td>
					<?php if($adrotate_config['stats'] == 1) { ?>
						<td><center><?php echo $stats['impressions']; ?></center></td>
						<td><center><?php echo $stats_today['impressions']; ?></center></td>
						<td><center><?php echo $stats['clicks']; ?></center></td>
						<td><center><?php echo $stats_today['clicks']; ?></center></td>
					<?php } ?>
				</tr>
				<?php unset($stats, $stats_today, $adspeed, $modus);?>
 			<?php } ?>
		<?php } else { ?>
				<tr>
					<th class="check-column">&nbsp;</th>
					<td colspan="<?php echo ($adrotate_config['stats'] == 1) ? '9' : '5'; ?>"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
				</tr>
		<?php } ?>
			</tbody>
	</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Warnings", "adrotate-pro"); ?>
</center></p>
</form>
