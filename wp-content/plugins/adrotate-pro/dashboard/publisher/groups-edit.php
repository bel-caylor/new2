<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2023 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

if(!$group_edit_id) {
	$action = "group_new";
	$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` = '' ORDER BY `id` DESC LIMIT 1;");
	if($edit_id == 0) {
		$wpdb->insert($wpdb->prefix.'adrotate_groups', array('name' => '', 'modus' => 0, 'swap' => 0, 'fallback' => '0', 'cat' => '', 'cat_loc' => 0, 'cat_par' => 0, 'page' => '', 'page_loc' => 0, 'page_par' => 0, 'woo_cat' => '', 'woo_loc' => 0, 'bbpress' => '', 'bbpress_loc' => 0, 'mobile' => 0, 'geo' => 0, 'wrapper_before' => '', 'wrapper_after' => '', 'gridrows' => 2, 'gridcolumns' => 2, 'admargin' => 0, 'admargin_bottom' => 0, 'admargin_left' => 0, 'admargin_right' => 0, 'adwidth' => '728', 'adheight' => '90', 'adspeed' => 6000, 'repeat_impressions' => 'Y'));
	    $edit_id = $wpdb->insert_id;
	}
	$group_edit_id = $edit_id;
	?>
<?php } else {
	$action = "group_edit";
}

$edit_group = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = '{$group_edit_id}';");

if($edit_group) {
	$groups = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");
	$ads = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `desktop`, `mobile`, `tablet`, `weight`, `crate`, `budget`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE (`type` != 'empty' AND `type` != 'a_empty' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'generator') ORDER BY `id` ASC;");
	$linkmeta = $wpdb->get_results("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = '{$group_edit_id}' AND `user` = 0;");

	$class = '';
	$meta_array = array();
	foreach($linkmeta as $meta) {
		$meta_array[] = $meta->ad;
	}
	if($group_edit_id AND $edit_group->name != '') {
		// Errors
		if($edit_group->modus == 2 AND $edit_group->gridrows == 1 AND $edit_group->gridcolumns == 1)
			echo '<div class="error"><p>'. __('Your block is a 1x1 grid. Either add more rows or columns or set the group to Default mode.', 'adrotate-pro').'</p></div>';

		if($edit_group->cat_loc > 0 AND strlen($edit_group->cat) == 0)
			echo '<div class="error"><p>'. __('You have enabled Post Injection but did not select any categories.', 'adrotate-pro').'</p></div>';

		if($edit_group->page_loc > 0 AND strlen($edit_group->page) == 0)
			echo '<div class="error"><p>'. __('You have enabled Page Injection but did not select any pages.', 'adrotate-pro').'</p></div>';

		if($edit_group->woo_loc > 0 AND strlen($edit_group->woo_cat) == 0)
			echo '<div class="error"><p>'. __('You have enabled Post Injection but did not select any WooCommerce categories.', 'adrotate-pro').'</p></div>';

		if($edit_group->bbpress_loc > 0 AND strlen($edit_group->bbpress) == 0)
			echo '<div class="error"><p>'. __('You have enabled Page Injection but did not select any bbPress forums.', 'adrotate-pro').'</p></div>';
	}
	?>

	<form name="editgroup" id="post" method="post" action="admin.php?page=adrotate-groups">
		<?php wp_nonce_field('adrotate_save_group','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_group->id;?>" />
		<input type="hidden" name="adrotate_action" value="<?php echo $action;?>" />

		<?php if($edit_group->name == '') { ?>
			<h2><?php _e('New Group', 'adrotate-pro'); ?></h2>
		<?php } else { ?>
			<h2><?php _e('Edit Group', 'adrotate-pro'); ?></h2>
		<?php } ?>

	   	<table class="widefat" style="margin-top: .5em">

			<tbody>
		    <tr>
				<th width="15%"><?php _e('Name', 'adrotate-pro'); ?></th>
				<td width="35%">
					<label for="adrotate_groupname"><input tabindex="1" name="adrotate_groupname" type="text" class="ajdg-fullwidth ajdg-inputfield" size="50" value="<?php echo stripslashes($edit_group->name); ?>" autocomplete="off" /></label>
				</td>
				<td>
					<em><?php _e('Visible on the Advertiser dashboard!', 'adrotate-pro'); ?></em>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Mode', 'adrotate-pro'); ?></strong></th>
				<td>
			       	<select tabindex="2" name="adrotate_modus" class="ajdg-fullwidth">
			        	<option value="0" <?php if($edit_group->modus == 0) { echo 'selected'; } ?>><?php _e('Default - Show one ad at a time', 'adrotate-pro'); ?></option>
			        	<option value="1" <?php if($edit_group->modus == 1) { echo 'selected'; } ?>><?php _e('Dynamic Mode - Show a different ad every few seconds', 'adrotate-pro'); ?></option>
			        	<option value="2" <?php if($edit_group->modus == 2) { echo 'selected'; } ?>><?php _e('Block Mode - Show a block of adverts', 'adrotate-pro'); ?></option>
			        </select>
				</td>
				<td>
			        <p><em><?php _e('Dynamic mode requires jQuery. Enable this in AdRotate Settings if your theme does not load it.', 'adrotate-pro'); ?></em></p>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Advert size', 'adrotate-pro'); ?></strong></th>
				<td width="35%">
					<label for="adrotate_adwidth"><input tabindex="3" name="adrotate_adwidth" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->adwidth; ?>" autocomplete="off" /> <?php _e('px wide', 'adrotate-pro'); ?>,</label> <label for="adrotate_adheight"><input tabindex="4" name="adrotate_adheight" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->adheight; ?>" autocomplete="off" /> <?php _e('px high.', 'adrotate-pro'); ?></label>
				</td>
				<td>
			        <em><?php _e('Define the maximum size of the adverts in pixels. Size can be \'auto\' (Not recommended).', 'adrotate-pro'); ?> <?php _e('The size will be visible in management and is for reference when in default mode.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('AdRotate Swap', 'adrotate-pro'); ?></strong></th>
				<td>
			       	<select tabindex="2" name="adrotate_swap" class="ajdg-fullwidth" disabled>
			        	<option value="0" <?php if($edit_group->swap == 0) { echo 'selected'; } ?>><?php _e('No', 'adrotate-pro'); ?></option>
			        	<option value="1" <?php if($edit_group->swap == 1) { echo 'selected'; } ?>><?php _e('Yes - Allow 1 advert', 'adrotate-pro'); ?></option>
			        	<option value="1" <?php if($edit_group->swap == 1) { echo 'selected'; } ?>><?php _e('Yes - Up to 3 adverts', 'adrotate-pro'); ?></option>
			        	<option value="2" <?php if($edit_group->swap == 2) { echo 'selected'; } ?>><?php _e('Yes - Up to 5 adverts', 'adrotate-pro'); ?></option>
			        </select>
				</td>
				<td>
			        <p><em><?php _e('AdRotate Swap is an upcoming advert sharing system for AdRotate users.', 'adrotate-pro'); ?></em></p>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php _e('Dynamic and Block Mode', 'adrotate-pro'); ?></h2>
	   	<table class="widefat" style="margin-top: .5em">

			<tbody>
		    <tr>
				<th width="15%"><?php _e('Block size', 'adrotate-pro'); ?></strong></th>
				<td width="35%">
			       	<label for="adrotate_gridrows"><select tabindex="5" name="adrotate_gridrows">
				       	<?php for($rows=1;$rows<=32;$rows++) { ?>
			        	<option value="<?php echo $rows; ?>" <?php if($edit_group->gridrows == $rows) { echo 'selected'; } ?>><?php echo $rows; ?></option>
						<?php } ?>
			        </select> <?php _e('rows', 'adrotate-pro'); ?>,</label> <label for="adrotate_gridcolumns"><select tabindex="6" name="adrotate_gridcolumns">
				       	<?php for($columns=1;$columns<=12;$columns++) { ?>
			        	<option value="<?php echo $columns; ?>" <?php if($edit_group->gridcolumns == $columns) { echo 'selected'; } ?>><?php echo $columns; ?></option>
						<?php } ?>
			        </select> <?php _e('columns', 'adrotate-pro'); ?>.</label>
				</td>
				<td>
			        <em><?php _e('Block Mode', 'adrotate-pro'); ?> - <?php _e('Larger blocks will degrade your sites performance! Default: 2/2.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Automatic refresh', 'adrotate-pro'); ?></strong></th>
				<td>
			       	<label for="adrotate_adspeed"><select tabindex="7" name="adrotate_adspeed">
			        	<option value="3000" <?php if($edit_group->adspeed == 3000) { echo 'selected'; } ?>>3</option>
			        	<option value="4000" <?php if($edit_group->adspeed == 4000) { echo 'selected'; } ?>>4</option>
			        	<option value="5000" <?php if($edit_group->adspeed == 5000) { echo 'selected'; } ?>>5</option>
			        	<option value="6000" <?php if($edit_group->adspeed == 6000) { echo 'selected'; } ?>>6</option>
			        	<option value="7000" <?php if($edit_group->adspeed == 7000) { echo 'selected'; } ?>>7</option>
			        	<option value="8000" <?php if($edit_group->adspeed == 8000) { echo 'selected'; } ?>>8</option>
			        	<option value="9000" <?php if($edit_group->adspeed == 9000) { echo 'selected'; } ?>>9</option>
			        	<option value="10000" <?php if($edit_group->adspeed == 10000) { echo 'selected'; } ?>>10</option>
			        	<option value="12000" <?php if($edit_group->adspeed == 12000) { echo 'selected'; } ?>>12</option>
			        	<option value="15000" <?php if($edit_group->adspeed == 15000) { echo 'selected'; } ?>>15</option>
			        	<option value="20000" <?php if($edit_group->adspeed == 20000) { echo 'selected'; } ?>>20</option>
			        	<option value="25000" <?php if($edit_group->adspeed == 25000) { echo 'selected'; } ?>>25</option>
			        	<option value="35000" <?php if($edit_group->adspeed == 35000) { echo 'selected'; } ?>>35</option>
			        	<option value="45000" <?php if($edit_group->adspeed == 45000) { echo 'selected'; } ?>>45</option>
			        	<option value="60000" <?php if($edit_group->adspeed == 60000) { echo 'selected'; } ?>>60</option>
			        	<option value="90000" <?php if($edit_group->adspeed == 90000) { echo 'selected'; } ?>>90</option>
			        </select> <?php _e('seconds.', 'adrotate-pro'); ?></label>
				</td>
				<td>
			        <em><?php _e('Dynamic Mode', 'adrotate-pro'); ?> - <?php _e('Load a new advert in this interval without reloading the page. Default: 6.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<tr>
				<th valign="top"><?php _e('Repeat impressions', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_repeat_impressions"><input type="checkbox" name="adrotate_repeat_impressions" id="adrotate_repeat_impressions" <?php if($edit_group->repeat_impressions == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Count impressions for every cycle of adverts?', 'adrotate-pro'); ?></label>
				</td>
				<td>
					<em><?php _e('Dynamic Mode', 'adrotate-pro'); ?> - <?php _e('Uncheck this option to only count impressions for the first cycle of adverts.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php _e('Usage', 'adrotate-pro'); ?></h2>
	   	<table class="widefat" style="margin-top: .5em">
			<tbody>
			<tr>
		        <th width="15%"><?php _e('Widget', 'adrotate-pro'); ?></th>
		        <td colspan="3"><?php _e('Drag the AdRotate widget to the sidebar where you want to place the group and select the group.', 'adrotate-pro'); ?></td>
	    	</tr>
			<tr>
		        <th width="15%"><?php _e('In a post or page', 'adrotate-pro'); ?></th>
		        <td width="35%">[adrotate group="<?php echo $edit_group->id; ?>"]</td>
		        <th width="15%"><?php _e('Directly in a theme', 'adrotate-pro'); ?></th>
		        <td width="35%">&lt;?php echo adrotate_group(<?php echo $edit_group->id; ?>); ?&gt;</td>
	      	</tr>
	      	</tbody>
		</table>

		<p class="submit">
			<input tabindex="8" type="submit" name="adrotate_group_submit" class="button-primary" value="<?php _e('Save Group', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-groups&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>

		<h2><?php _e('Advanced', 'adrotate-pro'); ?></h2>
	   	<table class="widefat" style="margin-top: .5em">
		    <tr>
				<th width="15%" valign="top"><?php _e('Advert Margin', 'adrotate-pro'); ?></strong></th>
				<td width="35%">
					<label for="adrotate_admargin_top">Top: <input tabindex="9" name="adrotate_admargin_top" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->admargin; ?>" autocomplete="off" />
					Bottom: <input tabindex="10" name="adrotate_admargin_bottom" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->admargin_bottom; ?>" autocomplete="off" /> <?php _e('pixel(s)', 'adrotate-pro'); ?>.<br />
					Left: <input tabindex="11" name="adrotate_admargin_left" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->admargin_left; ?>" autocomplete="off" />
					Right: <input tabindex="12" name="adrotate_admargin_right" type="text" class="ajdg-inputfield" size="3" value="<?php echo $edit_group->admargin_right; ?>" autocomplete="off" /> <?php _e('pixel(s)', 'adrotate-pro'); ?>.</label>
				</td>
				<td>
			        <em><?php _e('A transparent area outside the advert in pixels. Default: 0/0/0/0.', 'adrotate-pro'); ?> <?php _e('Set to 0 to disable.', 'adrotate-pro'); ?> <?php _e('Margins are automatically disabled for blocks where required.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Align the group', 'adrotate-pro'); ?></strong></th>
				<td>
			       	<label for="adrotate_align"><select tabindex="13" name="adrotate_align">
			        	<option value="0" <?php if($edit_group->align == 0) { echo 'selected'; } ?>><?php _e('None (Default)', 'adrotate-pro'); ?></option>
			        	<option value="1" <?php if($edit_group->align == 1) { echo 'selected'; } ?>><?php _e('Left', 'adrotate-pro'); ?></option>
			        	<option value="2" <?php if($edit_group->align == 2) { echo 'selected'; } ?>><?php _e('Right', 'adrotate-pro'); ?></option>
			        	<option value="3" <?php if($edit_group->align == 3) { echo 'selected'; } ?>><?php _e('Center', 'adrotate-pro'); ?></option>
			        </select></label>
				</td>
				<td>
			        <em><?php _e('Align the group in your post or page. Using \'center\' may affect your margin setting. Not every theme supports this feature.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<?php if($adrotate_config['enable_geo'] > 0) { ?>
		    <tr>
				<th width="15%" valign="top"><?php _e('Geo Targeting', 'adrotate-pro'); ?></th>
				<td width="35%">
					<label for="adrotate_geo"><input tabindex="14" type="checkbox" name="adrotate_geo" id="adrotate_geo" value="1" <?php if($edit_group->geo == '1') { ?>checked="checked"<?php } ?> /> <?php _e('Enable Geo Targeting for this group.', 'adrotate-pro'); ?></label>
				</td>
				<td>
					<em><?php _e('Do not forget to set up Geo Targeting for your adverts as well.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<?php } ?>
		    <tr>
				<th width="15%" valign="top"><?php _e('Mobile support', 'adrotate-pro'); ?></th>
				<td width="35%">
					<label for="adrotate_mobile"><input tabindex="15" type="checkbox" name="adrotate_mobile" id="adrotate_mobile" value="1" <?php if($edit_group->mobile == '1') { ?>checked="checked"<?php } ?> /> <?php _e('Enable mobile support for this group.', 'adrotate-pro'); ?></label>
				</td>
				<td>
					<em><?php _e('Do not forget to put at least one mobile advert in this group or no adverts may show.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Fallback group', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_fallback">
					<select tabindex="16" name="adrotate_fallback">
			        <option value="0"><?php _e('No', 'adrotate-pro'); ?></option>
				<?php if($groups) { ?>
					<?php foreach($groups as $group) { ?>
						<?php if($group->id == $edit_group->id) continue; ?>
				        <option value="<?php echo $group->id;?>" <?php if($edit_group->fallback == $group->id) { echo 'selected'; } ?>><?php echo $group->id;?> - <?php echo $group->name;?></option>
		 			<?php } ?>
				<?php } ?>
					</select>
				</td>
		        <td>
			        <em><?php _e('Select another group to fall back on when all adverts are expired, not in the visitors geographic area or are otherwise unavailable.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php _e('Post Injection', 'adrotate-pro'); ?></h2>
	   	<p><em><?php _e('Do not create groups doing that add adverts in (almost) the same location or too many adverts may show on posts or pages causing overlaps and double showings. Also make sure to not choose settings that saturate the content with adverts or it may become hard to read. Use with care!', 'adrotate-pro'); ?></em></p>
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="left-column" class="ajdg-postbox-container">

					<div class="ajdg-postbox">
						<h2 class="ajdg-postbox-title"><?php _e('For posts', 'adrotate-pro'); ?></h2>
						<div id="postinjection" class="ajdg-postbox-content">
							<p><label for="adrotate_cat_location">
							    <select tabindex="18" name="adrotate_cat_location">
							    	<option value="0" <?php if($edit_group->cat_loc == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate-pro'); ?></option>
							    	<option value="5" <?php if($edit_group->cat_loc == 5) { echo 'selected'; } ?>><?php _e('Widget', 'adrotate-pro'); ?></option>
							    	<option value="1" <?php if($edit_group->cat_loc == 1) { echo 'selected'; } ?>><?php _e('Before content', 'adrotate-pro'); ?></option>
							    	<option value="2" <?php if($edit_group->cat_loc == 2) { echo 'selected'; } ?>><?php _e('After content', 'adrotate-pro'); ?></option>
							    	<option value="3" <?php if($edit_group->cat_loc == 3) { echo 'selected'; } ?>><?php _e('Before and after content', 'adrotate-pro'); ?></option>
							    	<option value="4" <?php if($edit_group->cat_loc == 4) { echo 'selected'; } ?>><?php _e('Inside the content...', 'adrotate-pro'); ?></option>
							    </select>
							</label>
							<label for="adrotate_cat_paragraph">
							    <select tabindex="19" name="adrotate_cat_paragraph">
							    	<option value="0" <?php if($edit_group->cat_par == 0) { echo 'selected'; } ?>>...</option>
							    	<option value="99" <?php if($edit_group->cat_par == 99) { echo 'selected'; } ?>><?php _e('after the middle paragraph', 'adrotate-pro'); ?></option>
							    	<option value="110" <?php if($edit_group->cat_par == 110) { echo 'selected'; } ?>><?php _e('after about a 3rd of the content', 'adrotate-pro'); ?></option>
							    	<option value="111" <?php if($edit_group->cat_par == 111) { echo 'selected'; } ?>><?php _e('after about 2/3rds of the content', 'adrotate-pro'); ?></option>
							    	<option value="100" <?php if($edit_group->cat_par == 100) { echo 'selected'; } ?>><?php _e('every 3 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="101" <?php if($edit_group->cat_par == 101) { echo 'selected'; } ?>><?php _e('every 4 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="102" <?php if($edit_group->cat_par == 102) { echo 'selected'; } ?>><?php _e('every 5 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="103" <?php if($edit_group->cat_par == 103) { echo 'selected'; } ?>><?php _e('every 6 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="104" <?php if($edit_group->cat_par == 104) { echo 'selected'; } ?>><?php _e('every 7 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="105" <?php if($edit_group->cat_par == 105) { echo 'selected'; } ?>><?php _e('every 8 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="1" <?php if($edit_group->cat_par == 1) { echo 'selected'; } ?>><?php _e('after the 1st paragraph', 'adrotate-pro'); ?></option>
							    	<option value="2" <?php if($edit_group->cat_par == 2) { echo 'selected'; } ?>><?php _e('after the 2nd paragraph', 'adrotate-pro'); ?></option>
							    	<option value="3" <?php if($edit_group->cat_par == 3) { echo 'selected'; } ?>><?php _e('after the 3rd paragraph', 'adrotate-pro'); ?></option>
							    	<option value="4" <?php if($edit_group->cat_par == 4) { echo 'selected'; } ?>><?php _e('after the 4th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="5" <?php if($edit_group->cat_par == 5) { echo 'selected'; } ?>><?php _e('after the 5th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="6" <?php if($edit_group->cat_par == 6) { echo 'selected'; } ?>><?php _e('after the 6th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="7" <?php if($edit_group->cat_par == 7) { echo 'selected'; } ?>><?php _e('after the 7th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="8" <?php if($edit_group->cat_par == 8) { echo 'selected'; } ?>><?php _e('after the 8th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="10" <?php if($edit_group->cat_par == 10) { echo 'selected'; } ?>><?php _e('after the 10th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="12" <?php if($edit_group->cat_par == 12) { echo 'selected'; } ?>><?php _e('after the 12th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="14" <?php if($edit_group->cat_par == 14) { echo 'selected'; } ?>><?php _e('after the 14th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="16" <?php if($edit_group->cat_par == 16) { echo 'selected'; } ?>><?php _e('after the 16th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="18" <?php if($edit_group->cat_par == 18) { echo 'selected'; } ?>><?php _e('after the 18th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="20" <?php if($edit_group->cat_par == 20) { echo 'selected'; } ?>><?php _e('after the 20th paragraph', 'adrotate-pro'); ?></option>
							    </select>
							</label></p>
							<p><strong>Select post categories</strong></p>
							<div class="adrotate-select">
								<?php echo adrotate_select_categories($edit_group->cat, 0, 0, 0); ?>
							</div>
						</div>
					</div>

				</div>
				<div id="right-column" class="ajdg-postbox-container">

					<div class="ajdg-postbox">
						<h2 class="ajdg-postbox-title"><?php _e('For pages', 'adrotate-pro'); ?></h2>
						<div id="pageinjection" class="ajdg-postbox-content">
							<p><label for="adrotate_page_location">
						        <select tabindex="20" name="adrotate_page_location">
						        	<option value="0" <?php if($edit_group->page_loc == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate-pro'); ?></option>
						        	<option value="5" <?php if($edit_group->page_loc == 5) { echo 'selected'; } ?>><?php _e('Widget', 'adrotate-pro'); ?></option>
						        	<option value="1" <?php if($edit_group->page_loc == 1) { echo 'selected'; } ?>><?php _e('Before content', 'adrotate-pro'); ?></option>
						        	<option value="2" <?php if($edit_group->page_loc == 2) { echo 'selected'; } ?>><?php _e('After content', 'adrotate-pro'); ?></option>
						        	<option value="3" <?php if($edit_group->page_loc == 3) { echo 'selected'; } ?>><?php _e('Before and after content', 'adrotate-pro'); ?></option>
						        	<option value="4" <?php if($edit_group->page_loc == 4) { echo 'selected'; } ?>><?php _e('Inside the content...', 'adrotate-pro'); ?></option>
						        </select>
							</label>
					        <label for="adrotate_page_paragraph">
						        <select tabindex="21" name="adrotate_page_paragraph">
						        	<option value="0" <?php if($edit_group->page_par == 0) { echo 'selected'; } ?>>...</option>
						        	<option value="99" <?php if($edit_group->page_par == 99) { echo 'selected'; } ?>><?php _e('after the middle paragraph', 'adrotate-pro'); ?></option>
							    	<option value="110" <?php if($edit_group->page_par == 110) { echo 'selected'; } ?>><?php _e('after about a 3rd of the content', 'adrotate-pro'); ?></option>
							    	<option value="111" <?php if($edit_group->page_par == 111) { echo 'selected'; } ?>><?php _e('after about 2/3rds of the content', 'adrotate-pro'); ?></option>
							    	<option value="100" <?php if($edit_group->page_par == 100) { echo 'selected'; } ?>><?php _e('every 3 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="101" <?php if($edit_group->page_par == 101) { echo 'selected'; } ?>><?php _e('every 4 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="102" <?php if($edit_group->page_par == 102) { echo 'selected'; } ?>><?php _e('every 5 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="103" <?php if($edit_group->page_par == 103) { echo 'selected'; } ?>><?php _e('every 6 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="104" <?php if($edit_group->page_par == 104) { echo 'selected'; } ?>><?php _e('every 7 paragraphs', 'adrotate-pro'); ?></option>
							    	<option value="105" <?php if($edit_group->page_par == 105) { echo 'selected'; } ?>><?php _e('every 8 paragraphs', 'adrotate-pro'); ?></option>
						        	<option value="1" <?php if($edit_group->page_par == 1) { echo 'selected'; } ?>><?php _e('after the 1st paragraph', 'adrotate-pro'); ?></option>
						        	<option value="2" <?php if($edit_group->page_par == 2) { echo 'selected'; } ?>><?php _e('after the 2nd paragraph', 'adrotate-pro'); ?></option>
						        	<option value="3" <?php if($edit_group->page_par == 3) { echo 'selected'; } ?>><?php _e('after the 3rd paragraph', 'adrotate-pro'); ?></option>
						        	<option value="4" <?php if($edit_group->page_par == 4) { echo 'selected'; } ?>><?php _e('after the 4th paragraph', 'adrotate-pro'); ?></option>
						        	<option value="5" <?php if($edit_group->page_par == 5) { echo 'selected'; } ?>><?php _e('after the 5th paragraph', 'adrotate-pro'); ?></option>
						        	<option value="6" <?php if($edit_group->page_par == 6) { echo 'selected'; } ?>><?php _e('after the 6th paragraph', 'adrotate-pro'); ?></option>
						        	<option value="7" <?php if($edit_group->page_par == 7) { echo 'selected'; } ?>><?php _e('after the 7th paragraph', 'adrotate-pro'); ?></option>
						        	<option value="8" <?php if($edit_group->page_par == 8) { echo 'selected'; } ?>><?php _e('after the 8th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="10" <?php if($edit_group->page_par == 10) { echo 'selected'; } ?>><?php _e('after the 10th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="12" <?php if($edit_group->page_par == 12) { echo 'selected'; } ?>><?php _e('after the 12th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="14" <?php if($edit_group->page_par == 14) { echo 'selected'; } ?>><?php _e('after the 14th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="16" <?php if($edit_group->page_par == 16) { echo 'selected'; } ?>><?php _e('after the 16th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="18" <?php if($edit_group->page_par == 18) { echo 'selected'; } ?>><?php _e('after the 18th paragraph', 'adrotate-pro'); ?></option>
							    	<option value="20" <?php if($edit_group->page_par == 20) { echo 'selected'; } ?>><?php _e('after the 20th paragraph', 'adrotate-pro'); ?></option>
						        </select>
							</label></p>

							<p><strong>Select pages</strong></p>
							<div class="adrotate-select">
								<?php echo adrotate_select_pages($edit_group->page, 0, 0, 0); ?>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="left-column" class="ajdg-postbox-container">

					<?php if(is_plugin_active('woocommerce/woocommerce.php')) { ?>
					<div class="ajdg-postbox">
						<h2 class="ajdg-postbox-title"><?php _e('For WooCommerce products', 'adrotate-pro'); ?></h2>
						<div id="postinjection" class="ajdg-postbox-content">
							<p><label for="adrotate_woo_location">
							    <select tabindex="18" name="adrotate_woo_location">
							    	<option value="0" <?php if($edit_group->woo_loc == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate-pro'); ?></option>
							    	<option value="1" <?php if($edit_group->woo_loc == 1) { echo 'selected'; } ?>><?php _e('Before content', 'adrotate-pro'); ?></option>
							    	<option value="2" <?php if($edit_group->woo_loc == 2) { echo 'selected'; } ?>><?php _e('After content', 'adrotate-pro'); ?></option>
							    	<option value="3" <?php if($edit_group->woo_loc == 3) { echo 'selected'; } ?>><?php _e('Before and after content', 'adrotate-pro'); ?></option>
							    </select>
							</label></p>
							<p><strong>Select product categories</strong></p>
							<div class="adrotate-select">
								<?php echo adrotate_select_woo_categories($edit_group->woo_cat, 0, 0, 0); ?>
							</div>
						</div>
					</div>
					<?php } ?>

				</div>
				<div id="right-column" class="ajdg-postbox-container">

					<?php if(is_plugin_active('bbpress/bbpress.php')) { ?>
					<div class="ajdg-postbox">
						<h2 class="ajdg-postbox-title"><?php _e('For bbPress forum pages and topics', 'adrotate-pro'); ?></h2>
						<div id="pageinjection" class="ajdg-postbox-content">
							<p><label for="adrotate_bbpress_location">
						        <select tabindex="20" name="adrotate_bbpress_location">
						        	<option value="0" <?php if($edit_group->bbpress_loc == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate-pro'); ?></option>
						        	<option value="1" <?php if($edit_group->bbpress_loc == 1) { echo 'selected'; } ?>><?php _e('Before content', 'adrotate-pro'); ?></option>
						        	<option value="2" <?php if($edit_group->bbpress_loc == 2) { echo 'selected'; } ?>><?php _e('After content', 'adrotate-pro'); ?></option>
						        	<option value="3" <?php if($edit_group->bbpress_loc == 3) { echo 'selected'; } ?>><?php _e('Before and after content', 'adrotate-pro'); ?></option>
						        </select>
							</label></p>

							<p><strong>Select bbPress forums</strong></p>
							<div class="adrotate-select">
								<?php echo adrotate_select_bbpress_forums($edit_group->bbpress, 0, 0, 0); ?>
							</div>
						</div>
					</div>
					<?php } ?>

				</div>
			</div>
		</div>

	   	<div class="clear"></div>

		<h2><?php _e('Usage', 'adrotate-pro'); ?></h2>
	   	<table class="widefat" style="margin-top: .5em">
			<tbody>
			<tr>
		        <th width="15%"><?php _e('Widget', 'adrotate-pro'); ?></th>
		        <td colspan="3"><?php _e('Drag the AdRotate widget to the sidebar where you want to place the group and select the group.', 'adrotate-pro'); ?></td>
	    	</tr>
			<tr>
		        <th width="15%"><?php _e('In a post or page', 'adrotate-pro'); ?></th>
		        <td width="35%">[adrotate group="<?php echo $edit_group->id; ?>"]</td>
		        <th width="15%"><?php _e('Directly in a theme', 'adrotate-pro'); ?></th>
		        <td width="35%">&lt;?php echo adrotate_group(<?php echo $edit_group->id; ?>); ?&gt;</td>
	      	</tr>
	      	</tbody>
		</table>

		<p class="submit">
			<input tabindex="22" type="submit" name="adrotate_group_submit" class="button-primary" value="<?php _e('Save Group', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-groups&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>

	   	<h2><?php _e('Wrapper code', 'adrotate-pro'); ?></h2>
	   	<p><em><?php _e('Wraps around each advert. HTML/JavaScript allowed, use with care!', 'adrotate-pro'); ?></em></p>
	   	<table class="widefat" style="margin-top: .5em">

			<tbody>
		    <tr>
				<th width="15%" valign="top"><?php _e('Before advert', 'adrotate-pro'); ?></strong></th>
				<td width="35%"><textarea tabindex="23" name="adrotate_wrapper_before" rows="3" class="ajdg-fullwidth"><?php echo stripslashes($edit_group->wrapper_before); ?></textarea></td>
				<td>
			        <p><strong><?php _e('Example:', 'adrotate-pro'); ?></strong> <em>&lt;span style="background-color:#aaa;"&gt;</em></p>
			        <p><strong><?php _e('Options:', 'adrotate-pro'); ?></strong> <em>%id%</em></p>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('After advert', 'adrotate-pro'); ?></strong></th>
				<td><textarea tabindex="24" name="adrotate_wrapper_after" rows="3" class="ajdg-fullwidth"><?php echo stripslashes($edit_group->wrapper_after); ?></textarea></td>
				<td>
					<p><strong><?php _e('Example:', 'adrotate-pro'); ?></strong> <em>&lt;/span&gt;</em></p>
				</td>
			</tr>
			</tbody>

		</table>

		<h2><?php _e('Select adverts', 'adrotate-pro'); ?></h2>
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
					$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->id}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

					if($adrotate_config['stats'] == 1) {
						$stats = adrotate_stats($ad->id);
					}

					$errorclass = '';
					if($ad->type == 'error' OR $ad->type == 'a_error') $errorclass = ' row_yellow';
					if($stoptime <= $in2days OR $stoptime <= $in7days) $errorclass = ' row_orange';
					if($stoptime <= $now OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget == 0)) $errorclass = ' row_red';

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
						<?php if($adrotate_config['stats'] == 1) {
							if($ad->tracker != 'N') { ?>
								<td><center><?php echo $stats['impressions']; ?></center></td>
								<td><center><?php echo $stats['clicks']; ?></center></td>
							<?php } else { ?>
								<td><center>--</center></td>
								<td><center>--</center></td>
							<?php } ?>
						<?php } ?>
					</tr>
				<?php unset($stoptime, $stats);?>
	 			<?php } ?>
			<?php } else { ?>
			<tr>
				<th class="check-column">&nbsp;</th>
				<td colspan="<?php echo ($adrotate_config['stats'] == 1) ? '6' : '4'; ?>"><em><?php _e('No adverts created!', 'adrotate-pro'); ?></em></td>
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
			<input tabindex="25" type="submit" name="adrotate_group_submit" class="button-primary" value="<?php _e('Save Group', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-groups&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>

	</form>
<?php
} else {
	echo adrotate_error('error_loading_item');
}
?>
