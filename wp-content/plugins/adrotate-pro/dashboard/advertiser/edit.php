<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2022 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

//Permissions
$permissions = get_user_meta($current_user->ID, 'adrotate_permissions', 1);

// If permissions are not explicitly defined
if(!is_array($permissions) or empty($permissions)) $permissions = array('create' => 'N', 'edit' => 'N', 'advanced' => 'N', 'geo' => 'N', 'group' => 'N', 'schedule' => 'N');
if(!isset($permissions['create'])) $permissions['create'] = 'N';
if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
if(!isset($permissions['advanced'])) $permissions['advanced'] = 'N';
if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
if(!isset($permissions['group'])) $permissions['group'] = 'N';
if(!isset($permissions['schedule'])) $permissions['schedule'] = 'N';

if($permissions['edit'] == 'Y') {
	if(!$ad_edit_id) {
		$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'a_empty' AND `author` = '{$current_user->user_login}' ORDER BY `id` DESC LIMIT 1;");
		if($edit_id == 0) {
		    $wpdb->insert($wpdb->prefix."adrotate", array('title' => '', 'bannercode' => '', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => 'dropdown', 'image' => '', 'tracker' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'os_other' => 'Y', 'type' => 'a_empty', 'weight' => 5, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'state_req' => 'N', 'cities' => serialize(array()), 'states' => serialize(array()), 'cities' => serialize(array()), 'countries' => serialize(array())));
		    $edit_id = $wpdb->insert_id;
		    $wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $edit_id, 'group' => 0, 'user' => $current_user->ID, 'schedule' => 0));
		}
		$ad_edit_id = $edit_id;
	}

	$edit_banner = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$ad_edit_id};");
	$groups	= $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");
	$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' AND `stoptime` > {$now} ORDER BY `id` ASC;");
	$linkmeta = $wpdb->get_results("SELECT `group` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad_edit_id} AND `user` = 0 AND `schedule` = 0;");
	$schedulemeta = $wpdb->get_results("SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad_edit_id} AND `group` = 0 AND `user` = 0;");

	$fallback = $meta_array = $schedule_array = array();
	foreach($groups as $group) { // Which groups are fallback groups?
		$fallback[] = $group->fallback;
	}
	foreach($linkmeta as $meta) { // Sort out meta data
		$meta_array[] = $meta->group;
		unset($meta);
	}
	foreach($schedulemeta as $meta) { // Sort out schedules
		$schedule_array[] = $meta->schedule;
		unset($meta);
	}

	if($ad_edit_id AND $edit_banner->type != 'a_empty') {
		// Errors
		if($edit_banner->tracker == 'N')
			echo '<div class="error"><p>'. __("Please contact staff, click statistics are not enabled for this advert!", 'adrotate-pro').'</p></div>';

		if(!preg_match_all("/(%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image != '')
			echo '<div class="error"><p>'. __('You did not use %asset% in your AdCode but did select a file to use!', 'adrotate-pro') .' '. __("Please contact staff if you don't know what this means.", 'adrotate-pro').'</p></div>';

		if(preg_match_all("/(%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image == '')
			echo '<div class="error"><p>'. __('You did use %asset% in your AdCode but did not select a file to use!', 'adrotate-pro') .' '. __("Please contact staff if you don't know what this means.", 'adrotate-pro').'</p></div>';

		if(count($schedule_array) == 0)
			echo '<div class="error"><p>'. __('This advert has no schedules!', 'adrotate-pro').'</p></div>';

		if((!preg_match_all('/<(a)[^>](.*?)>/i', stripslashes(htmlspecialchars_decode($edit_banner->bannercode, ENT_QUOTES)), $things) OR preg_match_all('/<(ins|script|embed|iframe)[^>](.*?)>/i', stripslashes(htmlspecialchars_decode($edit_banner->bannercode, ENT_QUOTES)), $things)) AND $edit_banner->tracker == 'Y')
			echo '<div class="error"><p>'. __("This kind of advert can only count impressions.", 'adrotate-pro').'</p></div>';

		if($edit_banner->tracker == 'N' AND ($edit_banner->crate > 0 OR $edit_banner->irate > 0))
			echo '<div class="error"><p>'. __("Please contact staff, CPC and/or CPM is enabled but statistics are not enabled!", 'adrotate-pro').'</p></div>';

		// Ad Notices
		$adstate = adrotate_evaluate_ad($edit_banner->id);
		if($edit_banner->type == 'error' AND $adstate == 'normal')
			echo '<div class="error"><p>'. __('AdRotate can not find an error but the ad is marked erroneous, try re-saving the ad or contact staff if the issue persists!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'reject')
			echo '<div class="error"><p>'. __('This advert has been rejected by staff. Please adjust the ad to conform with the requirements!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'queue')
			echo '<div class="error"><p>'. __('This advert is queued and awaiting review!', 'adrotate-pro').'</p></div>';

		if($adstate == 'expired')
			echo '<div class="error"><p>'. __('This ad is expired and currently not rotating!', 'adrotate-pro').'</p></div>';

		if($adstate == 'limit')
			echo '<div class="error"><p>'. __('This advert is over limits. Check its current schedule and/or advertiser budget!', 'adrotate-pro').'</p></div>';

		if($adstate == '2days')
			echo '<div class="updated"><p>'. __('The ad will expire in less than 2 days!', 'adrotate-pro').'</p></div>';

		if($adstate == '7days')
			echo '<div class="updated"><p>'. __('This ad will expire in less than 7 days!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'disabled')
			echo '<div class="updated"><p>'. __('This ad has been disabled and is not rotating!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'active')
			echo '<div class="updated"><p>'. __('This advert is approved and currently showing on the site! Saving the advert now will put it in the moderation queue for review!', 'adrotate-pro').'</p></div>';
	}

	// Determine image field
	if($edit_banner->imagetype == "dropdown") {
		$image_dropdown = $edit_banner->image;
	} else {
		$image_dropdown = '';
	}
	?>

	<?php if($adrotate_config['live_preview'] == "Y") { ?>
		<!-- AdRotate JS -->
		<script type="text/javascript">
		jQuery(document).ready(function(){
		    function livePreview(){
		        var input = jQuery("#adrotate_bannercode").val();
		        if(jQuery("#adrotate_title").val().length > 0) var ad_title = jQuery("#adrotate_title").val();
		        var ad_image = '';
		        if(jQuery("#adrotate_image_dropdown").val().length > 0) var ad_image = '<?php echo WP_CONTENT_URL.'/'.$adrotate_config['banner_folder']; ?>/'+jQuery("#adrotate_image_dropdown").val();

		        var input = input.replace(/%id%/g, <?php echo $edit_banner->id;?>);
		        var input = input.replace(/%title%/g, ad_title);
		        var input = input.replace(/%asset%/g, ad_image);
		        var input = input.replace(/%random%/g, <?php echo rand(100000,999999); ?>);
		        jQuery("#adrotate_preview").html(input);
		    }
		    livePreview();

		    jQuery('#adrotate_bannercode').on("paste change focus focusout input", function(){ livePreview(); });
		    jQuery('#adrotate_image_dropdown').on("change", function(){ livePreview(); });
		});
		</script>
		<!-- /AdRotate JS -->
	<?php } ?>

	<form method="post" action="admin.php?page=adrotate-advertiser" enctype="multipart/form-data">
		<?php wp_nonce_field('adrotate_save_ad','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_username" value="<?php echo $current_user->user_login;?>" />
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_banner->id;?>" />
		<input type="hidden" name="adrotate_type" value="<?php echo $edit_banner->type;?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="512000" />

	<?php if($edit_banner->type == 'a_empty') { ?>
		<h3><?php _e('New Advert', 'adrotate-pro'); ?></h3>
	<?php } else { ?>
		<h3><?php _e('Edit Advert', 'adrotate-pro'); ?></h3>
	<?php } ?>

		<table class="widefat" style="margin-top: .5em">

			<tbody>
	      	<tr>
		        <th width="20%"><?php _e('Name', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_title"><input tabindex="1" id="adrotate_title" name="adrotate_title" type="text" size="70" class="ajdg-inputfield ajdg-fullwidth" value="<?php echo stripslashes($edit_banner->title);?>" autocomplete="off" /></label>
		        </td>
		        <td width="35%">
		        	<em><?php _e('For your and the staffs reference.', 'adrotate-pro'); ?></em>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('AdCode', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_bannercode"><textarea tabindex="2" id="adrotate_bannercode" name="adrotate_bannercode" cols="70" rows="10" class="ajdg-fullwidth"><?php echo stripslashes($edit_banner->bannercode); ?></textarea></label>
		        </td>
		        <td width="35%" rowspan="2">
			        <p><strong><?php _e('Basic Examples:', 'adrotate-pro'); ?></strong></p>
			        <p><em><?php _e('Click any of the examples to use it.', 'adrotate-pro'); ?></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://www.ajdg.net/&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://www.ajdg.net/"&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
			        <p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;');return false;">&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;http://www.arnan.me/&quot;&gt;Visit arnan.me&lt;/a&gt;');return false;">&lt;a href="http://www.arnan.me/"&gt;Visit arnan.me&lt;/a&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://www.ajdg.net/&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://www.ajdg.net/" target=&quot;_blank&quot;&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://www.ajdg.net/?timestamp=%random%&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://www.ajdg.net/?timestamp=%random%"&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
		        </td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Useful tags', 'adrotate-pro'); ?></th>
		        <td>
			        <span class="description"><a href="#" onclick="textatcursor('adrotate_bannercode','%id%');return false;"><span class="ajdg-tooltip">%id%<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Insert the advert ID Number.', 'adrotate-pro'); ?></span></span></a> <a href="#" onclick="textatcursor('adrotate_bannercode','%asset%');return false;"><span class="ajdg-tooltip">%asset%<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Use this tag when selecting a image below.', 'adrotate-pro'); ?></span></span></a> <a href="#" onclick="textatcursor('adrotate_bannercode','%title%');return false;"><span class="ajdg-tooltip">%title%<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Insert the advert name.', 'adrotate-pro'); ?></span></span></a> <a href="#" onclick="textatcursor('adrotate_bannercode','%random%');return false;"><span class="ajdg-tooltip">%random%<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Insert a random string. Useful for DFP/DoubleClick type adverts.', 'adrotate-pro'); ?></span></span></a> <a href="#" onclick="textatcursor('adrotate_bannercode','target=&quot;_blank&quot;');return false;"><span class="ajdg-tooltip">target="_blank"<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Add inside the &lt;a&gt; tag to open the advert in a new window.', 'adrotate-pro'); ?></span></span></a> <a href="#" onclick="textatcursor('adrotate_bannercode','rel=&quot;nofollow&quot;');return false;"><span class="ajdg-tooltip">rel="nofollow"<span class="ajdg-tooltiptext ajdg-tooltip-top"><?php _e('Add inside the &lt;a&gt; tag to tell crawlers to ignore this link.', 'adrotate-pro'); ?></span></span></a></em><br />
			        <?php _e('Place the cursor where you want to add a tag and click to add it to your AdCode.', 'adrotate-pro'); ?></p>
		        </td>
	      	</tr>
		  	<?php if($edit_banner->type != 'a_empty' AND $edit_banner->type != 'empty') { ?>
	      	<tr>
		  		<th valign="top"><?php _e('Live Preview', 'adrotate-pro'); ?></th>
		        <td colspan="2">
					<?php if($adrotate_config['live_preview'] == "Y") { ?>
			        	<div id="adrotate_preview"></div>
		        	<?php } else { ?>
			        	<div><?php echo adrotate_preview($edit_banner->id); ?></div>
		        	<?php } ?>
			        <br /><em><?php _e('Note: While this preview is an accurate one, it might look different then it does on the website.', 'adrotate-pro'); ?>
					<br /><?php _e('This is because of CSS differences. Your themes CSS file is not active here!', 'adrotate-pro'); ?></em>
				</td>
	      	</tr>
		  	<?php } ?>
			<tr>
		        <th valign="top"><?php _e('Banner asset', 'adrotate-pro'); ?></th>
				<td colspan="2">
					<label for="adrotate_image">
						<?php _e('Upload a file', 'adrotate-pro'); ?> <input tabindex="3" type="file" name="adrotate_image" /><br /><em><?php _e('Accepted files are:', 'adrotate-pro'); ?> jpg, jpeg, gif, png <?php _e('and', 'adrotate-pro'); ?> svg.</em>
					</label><br />
					<?php _e('- OR -', 'adrotate-pro'); ?><br />
					<label for="adrotate_image_dropdown">
						<?php _e('Banner folder', 'adrotate-pro'); ?> <select tabindex="5" id="adrotate_image_dropdown" name="adrotate_image_dropdown" style="min-width: 200px;">
							<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php
							$assets = adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png', 'svg', 'html', 'htm'));
							foreach($assets as $key => $option) {
								echo "<option value=\"$option\"";
								if($image_dropdown == WP_CONTENT_URL."/%folder%/".$option) { echo " selected"; }
								echo ">$option</option>";
							}
							?>
						</select><br />
					</label>
					<em><?php _e('Use %asset% in the adcode instead of the file path.', 'adrotate-pro'); ?> <?php _e('Use either the upload option or the dropdown menu.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Statistics', 'adrotate-pro'); ?></th>
		        <td colspan="2">
					<?php
					if($edit_banner->tracker == "Y") {
						_e('Counts Clicks and Impressions.', 'adrotate-pro'); 
					} else if($edit_banner->tracker == "C") {
						_e('Counts only Clicks.', 'adrotate-pro');
					} else if($edit_banner->tracker == "I") { 
						_e('Counts only Impressions.', 'adrotate-pro');
					} else {
						_e('Statistics are disabled, contact staff!', 'adrotate-pro');	
					}
					?>
				</td>
	      	</tr>
	  		</tbody>

		</table>

		<?php if($permissions['advanced'] == "Y") { ?>
		<h3><?php _e('Advanced', 'adrotate-pro'); ?></h3>
<?php
// Temporary
if($edit_banner->weight == 2) $edit_banner->weight = 1;
if($edit_banner->weight == 4) $edit_banner->weight = 3;
if($edit_banner->weight == 6) $edit_banner->weight = 5;
if($edit_banner->weight == 8) $edit_banner->weight = 7;
if($edit_banner->weight == 10) $edit_banner->weight = 9;
?>
		<table class="widefat" style="margin-top: .5em">

			<tbody>
	       	<tr>
			    <th width="15%" valign="top"><?php _e('Weight', 'adrotate-pro'); ?></th>
				<td colspan="2">
					<table width="100%">
						<tr>
					        <td width="20%">
					        	<label for="adrotate_weight2">
					        	<center><input type="radio" tabindex="5" name="adrotate_weight" id="adrotate_weight2" value="1" <?php if($edit_banner->weight == "1") { echo 'checked'; } ?> /><br /><?php _e('Few impressions', 'adrotate-pro'); ?></center>
					        	</label>
							</td>
					        <td width="20%">
					        	<label for="adrotate_weight4">
					        	<center><input type="radio" tabindex="6" name="adrotate_weight" id="adrotate_weight4" value="3" <?php if($edit_banner->weight == "3") { echo 'checked'; } ?> /><br /><?php _e('Less than average', 'adrotate-pro'); ?></center>
					        	</label>
							</td>
					        <td width="20%">
					        	<label for="adrotate_weight6">
					        	<center><input type="radio" tabindex="7" name="adrotate_weight" id="adrotate_weight6" value="5" <?php if($edit_banner->weight == "5") { echo 'checked'; } ?> /><br /><?php _e('Normal impressions', 'adrotate-pro'); ?></center>
					        	</label>
							</td>
					        <td width="20%">
					        	<label for="adrotate_weight8">
					        	<center><input type="radio" tabindex="8" name="adrotate_weight" id="adrotate_weight8" value="7" <?php if($edit_banner->weight == "7") { echo 'checked'; } ?> /><br /><?php _e('More than average', 'adrotate-pro'); ?></center>
					        	</label>
							</td>
					        <td>
					        	<label for="adrotate_weight10">
					        	<center><input type="radio" tabindex="9" name="adrotate_weight" id="adrotate_weight10" value="9" <?php if($edit_banner->weight == "9") { echo 'checked'; } ?> /><br /><?php _e('Many impressions', 'adrotate-pro'); ?>
					        	</label>
							</td>
						</tr>
					</table>
					<em><?php _e("Each weight level increases or decreases the chance for the advert to be shown by up-to 20% compared to the other adverts in the group."); ?></em>
				</td>
			</tr>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Device', 'adrotate-pro'); ?></th>
				<td width="45%">
					<table width="100%">
						<tr>
					        <td width="33%">
					        	<label for="adrotate_desktop"><center><input tabindex="9" type="checkbox" name="adrotate_desktop" id="adrotate_desktop" <?php if($edit_banner->desktop == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Computers', 'adrotate-pro'); ?></center></label>
					        </td>
					        <td width="33%">
					        	<label for="adrotate_mobile"><center><input tabindex="10" type="checkbox" name="adrotate_mobile" id="adrotate_mobile" <?php if($edit_banner->mobile == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Smartphones', 'adrotate-pro'); ?></center></label>
					        </td>
					        <td>
					        	<label for="adrotate_tablet"><center><input tabindex="11" type="checkbox" name="adrotate_tablet" id="adrotate_tablet" <?php if($edit_banner->tablet == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Tablets', 'adrotate-pro'); ?></center></label>
					        </td>
						</tr>
					</table>
				</td>
		        <td rowspan="2">
		        	<em><?php _e("Also enable 'Mobile Support' in the group this advert goes in or 'Device' and 'Operating System' are ignored!", 'adrotate-pro'); ?><br /><?php _e("Operating system detection only detects iOS and Android, select 'Others' for everything else. Device type is determined by screensize and user-agent as reported by the device.", 'adrotate-pro'); ?></em>
		        </td>
			</tr>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Mobile OS', 'adrotate-pro'); ?></th>
				<td width="45%">
					<table width="100%">
						<tr>
					        <td width="33%">
					        	<label for="adrotate_ios"><center><input tabindex="12" type="checkbox" name="adrotate_ios" id="adrotate_ios" <?php if($edit_banner->os_ios == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('iOS', 'adrotate-pro'); ?></center></label>
					        </td>
					        <td width="33%">
					        	<label for="adrotate_android"><center><input tabindex="13" type="checkbox" name="adrotate_android" id="adrotate_android" <?php if($edit_banner->os_android == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Android', 'adrotate-pro'); ?></center></label>
					        </td>
					        <td>
					        	<label for="adrotate_other"><center><input tabindex="14" type="checkbox" name="adrotate_other" id="adrotate_other" <?php if($edit_banner->os_other == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Others', 'adrotate-pro'); ?></center></label>
					        </td>
						</tr>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
		<?php } else { ?>
		<input type="hidden" name="adrotate_weight" value="<?php echo $edit_banner->weight;?>" />
		<input type="hidden" name="adrotate_desktop" value="<?php echo $edit_banner->desktop;?>" />
		<input type="hidden" name="adrotate_mobile" value="<?php echo $edit_banner->mobile;?>" />
		<input type="hidden" name="adrotate_tablet" value="<?php echo $edit_banner->tablet;?>" />
		<input type="hidden" name="adrotate_ios" value="<?php echo $edit_banner->ios;?>" />
		<input type="hidden" name="adrotate_android" value="<?php echo $edit_banner->android;?>" />
		<input type="hidden" name="adrotate_other" value="<?php echo $edit_banner->other;?>" />
		<?php } ?>

		<?php if($groups) { ?>
		<h3><?php _e('Groups', 'adrotate-pro'); ?></h3>
		<?php if($permissions['group'] == "Y") { ?>
		<p><em><?php _e('Select where your ad should be visible. If your desired group/location is not listed or the specification is unclear contact your publisher.', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">
			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
				<th>&nbsp;</th>
			</tr>
			</thead>

			<tbody>
			<?php
			$class = '';
			foreach($groups as $group) {
				if(in_array($group->id, $fallback)) continue;

				if($group->adspeed > 0) $adspeed = $group->adspeed / 1000;
		        if($group->modus == 0) $modus[] = __('Default', 'adrotate-pro');
		        if($group->modus == 1) $modus[] = __('Dynamic', 'adrotate-pro').' ('.$adspeed.' '. __('second rotation', 'adrotate-pro').')';
		        if($group->modus == 2) $modus[] = __('Block', 'adrotate-pro').' ('.$group->gridrows.' x '.$group->gridcolumns.' '. __('grid', 'adrotate-pro').')';
				if($group->adwidth > 0 AND $group->adheight > 0) $modus[] = $group->adwidth.'x'.$group->adheight.'px';
		        if($group->geo == 1 AND $adrotate_config['enable_geo'] > 0) $modus[] = __('Geolocation', 'adrotate-pro');
		        if($group->mobile == 1) $modus[] = __('Mobile', 'adrotate-pro');

				$class = ('alternate' != $class) ? 'alternate' : ''; ?>
			    <tr id='group-<?php echo $group->id; ?>' class='<?php echo $class; ?>'>
					<th class="check-column" width="2%"><input type="checkbox" name="groupselect[]" value="<?php echo $group->id; ?>" <?php if(in_array($group->id, $meta_array)) echo "checked"; ?> /></th>
					<td><?php echo $group->id; ?></td>
					<td><strong><?php echo $group->name; ?></strong><br /><span style="color:#999;"><?php echo implode(', ', $modus); ?></span></td>
				</tr>
			<?php
				unset($modus);
			}
			?>
			</tbody>
		</table>
		<?php } else { ?>
		<table class="widefat" style="margin-top: .5em">
			<thead>
			<tr>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
				<th>&nbsp;</th>
			</tr>
			</thead>

			<tbody>
			<?php
			$class = '';
			foreach($groups as $group) {
				if(!in_array($group->id, $meta_array)) continue;

				$class = ('alternate' != $class) ? 'alternate' : ''; ?>
			    <tr id='group-<?php echo $group->id; ?>' class='<?php echo $class; ?>'>
					<td><?php echo $group->id; ?></td>
					<td><strong><?php echo $group->name; ?></strong></td>
				</tr>
			<?php
				unset($modus);
			}
			?>
			</tbody>
		</table>
			<?php foreach($groups as $group) {
				if(in_array($group->id, $meta_array)) echo '<input type="hidden" name="groupselect[]" value="'.$group->id.'" />';
			} ?>

		<?php } } ?>

		<?php if($schedules) { ?>
		<h3><?php _e('Schedules', 'adrotate-pro'); ?></h3>
		<?php if($permissions['schedule'] == "Y") { ?>
		<p><em><?php _e('Select when your ad should be visible. If your desired timeframe is not listed contact your publisher.', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">

			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
		        <th width="20%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
		        <th>&nbsp;</th>
			</tr>
			</thead>

			<tbody>
			<?php
			$class = '';
			foreach($schedules as $schedule) {
				$class = ('alternate' != $class) ? 'alternate' : '';
				if(in_array($schedule->id, $schedule_array)) $class = 'row_green';
				if($schedule->stoptime < $in2days) $class = 'row_red';

				$sdayhour = substr($schedule->daystarttime, 0, 2);
				$sdayminute = substr($schedule->daystarttime, 2, 2);
				$edayhour = substr($schedule->daystoptime, 0, 2);
				$edayminute = substr($schedule->daystoptime, 2, 2);
				$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
				$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';
			?>
	      	<tr id='schedule-<?php echo $schedule->id; ?>' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="scheduleselect[]" value="<?php echo $schedule->id; ?>" <?php if(in_array($schedule->id, $schedule_array)) echo "checked"; ?> /></th>
				<td><?php echo $schedule->id; ?></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
				<td><strong><?php echo stripslashes(html_entity_decode($schedule->name)); ?></strong><br /><span style="color:#999;"><?php _e('Mon:', 'adrotate-pro'); ?> <?php echo ($schedule->day_mon == 'Y') ? $tick : $cross; ?> <?php _e('Tue:', 'adrotate-pro'); ?> <?php echo ($schedule->day_tue == 'Y') ? $tick : $cross; ?> <?php _e('Wed:', 'adrotate-pro'); ?> <?php echo ($schedule->day_wed == 'Y') ? $tick : $cross; ?> <?php _e('Thu:', 'adrotate-pro'); ?> <?php echo ($schedule->day_thu == 'Y') ? $tick : $cross; ?> <?php _e('Fri:', 'adrotate-pro'); ?> <?php echo ($schedule->day_fri == 'Y') ? $tick : $cross; ?> <?php _e('Sat:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sat == 'Y') ? $tick : $cross; ?> <?php _e('Sun:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sun == 'Y') ? $tick : $cross; ?> <?php if($schedule->daystarttime  > 0) { ?><?php _e('Between:', 'adrotate-pro'); ?> <?php echo $sdayhour; ?>:<?php echo $sdayminute; ?> - <?php echo $edayhour; ?>:<?php echo $edayminute; ?> <?php } ?><br /><?php _e('Impression spread:', 'adrotate-pro'); ?> <?php echo ($schedule->spread == 'Y') ? $tick : $cross; ?></span></td>
	      	</tr>
	      	<?php } ?>
			</tbody>

		</table>
		<p><center>
			<span style="border: 1px solid #518257; height: 12px; width: 12px; background-color: #e5faee">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("In use by this advert.", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate-pro"); ?>
		</center></p>
		<?php } else { ?>
		<table class="widefat" style="margin-top: .5em">

			<thead>
			<tr>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
		        <th width="20%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
		        <th>&nbsp;</th>
			</tr>
			</thead>

			<tbody>
			<?php
			$class = '';
			foreach($schedules as $schedule) {
				if(!in_array($schedule->id, $schedule_array)) continue;

				$class = ('alternate' != $class) ? 'alternate' : '';

				$sdayhour = substr($schedule->daystarttime, 0, 2);
				$sdayminute = substr($schedule->daystarttime, 2, 2);
				$edayhour = substr($schedule->daystoptime, 0, 2);
				$edayminute = substr($schedule->daystoptime, 2, 2);
				$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
				$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';
			?>
	      	<tr id='schedule-<?php echo $schedule->id; ?>' class='<?php echo $class; ?>'>
				<td><?php echo $schedule->id; ?></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
				<td><strong><?php echo stripslashes(html_entity_decode($schedule->name)); ?></strong><br /><span style="color:#999;"><?php _e('Mon:', 'adrotate-pro'); ?> <?php echo ($schedule->day_mon == 'Y') ? $tick : $cross; ?> <?php _e('Tue:', 'adrotate-pro'); ?> <?php echo ($schedule->day_tue == 'Y') ? $tick : $cross; ?> <?php _e('Wed:', 'adrotate-pro'); ?> <?php echo ($schedule->day_wed == 'Y') ? $tick : $cross; ?> <?php _e('Thu:', 'adrotate-pro'); ?> <?php echo ($schedule->day_thu == 'Y') ? $tick : $cross; ?> <?php _e('Fri:', 'adrotate-pro'); ?> <?php echo ($schedule->day_fri == 'Y') ? $tick : $cross; ?> <?php _e('Sat:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sat == 'Y') ? $tick : $cross; ?> <?php _e('Sun:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sun == 'Y') ? $tick : $cross; ?> <?php if($schedule->daystarttime  > 0) { ?><?php _e('Between:', 'adrotate-pro'); ?> <?php echo $sdayhour; ?>:<?php echo $sdayminute; ?> - <?php echo $edayhour; ?>:<?php echo $edayminute; ?> <?php } ?><br /><?php _e('Impression spread:', 'adrotate-pro'); ?> <?php echo ($schedule->spread == 'Y') ? $tick : $cross; ?></span></td>
	      	</tr>
	      	<?php } ?>
			</tbody>

		</table>
			<?php foreach($schedules as $schedule) {
				if(in_array($schedule->id, $meta_array)) echo '<input type="hidden" name="scheduleselect[]" value="'.$schedule->id.'" />';
			} ?>
		<?php } } ?>

		<?php if($adrotate_config['enable_geo'] > 0) { ?>
			<h2><?php _e('Geo Targeting', 'adrotate-pro'); ?></h2>
			<?php $cities = unserialize(stripslashes($edit_banner->cities)); ?>
			<?php $states = unserialize(stripslashes($edit_banner->states)); ?>
			<?php $countries = unserialize(stripslashes($edit_banner->countries)); ?>
		<?php if($permissions['geo'] == 'Y') { ?>
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="left-column" class="ajdg-postbox-container">

						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('Enter cities or Metro IDs (DMA)', 'adrotate-pro'); ?></h2>
							<div id="cities" class="ajdg-postbox-content">
								<textarea tabindex="32" name="adrotate_geo_cities" class="geo-cities ajdg-fullwidth" cols="40" rows="6"><?php echo (is_array($cities)) ? implode(', ', $cities) : ''; ?></textarea><br />
								<p><em><?php _e('A comma separated list of Cities or Metro IDs:', 'adrotate-pro'); ?> Amsterdam, New York, Manila, Mexico City, Tokyo.<br /><?php _e('AdRotate does not check the validity of names and assumes the basic english name.', 'adrotate-pro'); ?></em></p>
							</div>
						</div>

						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('Enter States or State ISO3166-2 codes', 'adrotate-pro'); ?></h2>
							<div id="cities" class="ajdg-postbox-content">
								<label for="adrotate_geo_state_required"><input tabindex="33" type="checkbox" name="adrotate_geo_state_required" id="adrotate_geo_state_required" <?php if($edit_banner->state_req == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('The listed cities must be in these states?', 'adrotate-pro'); ?></label><br />
								<textarea tabindex="34" name="adrotate_geo_states" class="geo-states ajdg-fullwidth" cols="40" rows="2"><?php echo (is_array($states)) ? implode(', ', $states) : ''; ?></textarea>
								<p><em><?php _e('A comma separated list of states:', 'adrotate-pro'); ?> Ohio, California, Noord Holland, Normandy.<br /><?php _e('AdRotate does not check the validity of names and assumes the basic english name.', 'adrotate-pro'); ?></em></p>
							</div>
						</div>

					</div>
					<div id="right-column" class="ajdg-postbox-container">

						<div class="ajdg-postbox">
							<h2 class="ajdg-postbox-title"><?php _e('Select Countries and or Regions', 'adrotate-pro'); ?></h2>
							<div id="countries" class="ajdg-postbox-content">
								<div class="adrotate-select ajdg-fullwidth">
							        <?php echo adrotate_select_countries($countries); ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		   	<div class="clear"></div>
		<?php } else { ?>
		<table class="widefat" style="margin-top: .5em">

			<thead>
			<tr>
		        <th width="10%">&nbsp;</th>
		        <th><?php _e('Places', 'adrotate-pro'); ?></th>
			</tr>
			</thead>

			<tbody>
			<tr>
				<th><strong><?php _e('Cities', 'adrotate-pro'); ?></strong></th>
				<td><?php echo (count($cities) > 0) ? implode(', ', $cities) : __('Not configured', 'adrotate-pro'); ?></td>
			</tr>
			<tr>
				<th><strong><?php _e('States', 'adrotate-pro'); ?></strong></th>
				<td><?php echo (count($states) > 0) ? implode(', ', $states) : __('Not configured', 'adrotate-pro'); ?></td>
			</tr>
			<tr>
				<th><strong><?php _e('Countries', 'adrotate-pro'); ?></strong></th>
				<td><?php echo (count($countries) > 0) ? implode(', ', $countries) : __('Not configured', 'adrotate-pro'); ?></td>
			</tr>
			</tbody>

		</table>
		<input type="hidden" name="adrotate_geo_cities" value="<?php echo implode(', ', $cities);?>" />
		<input type="hidden" name="adrotate_geo_state_required" value="<?php echo $edit_banner->state_req;?>" />
		<input type="hidden" name="adrotate_geo_states" value="<?php echo implode(', ', $states);?>" />
		<input type="hidden" name="adrotate_geo_countries" value="<?php echo implode(', ', $countries);?>" />
		<?php } } ?>

		<p class="submit">
			<input tabindex="16" type="submit" name="adrotate_advertiser_ad_submit" class="button-primary" value="<?php _e('Submit ad for review', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate&view=adrotate-advertiser" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>

		</form>

<?php } else { ?>

	<h3><?php _e('Editing and creating adverts is not available right now', 'adrotate-pro'); ?></h3>
	<p><?php _e('The administrator has disabled editing of adverts. Contact your representative if you think this is incorrect.', 'adrotate-pro'); ?></p>

<?php } ?>