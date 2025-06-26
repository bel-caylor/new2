<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

if(!$ad_edit_id) { 
	$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'generator' ORDER BY `id` DESC LIMIT 1;");
	if($edit_id == 0) {
	    $wpdb->insert($wpdb->prefix."adrotate", array('title' => '', 'bannercode' => '', 'thetime' => $now, 'updated' => $now, 'author' => $userdata->user_login, 'imagetype' => 'dropdown', 'image' => '', 'tracker' => 'N', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'type' => 'generator', 'weight' => 6, 'autodelete' => 'N', 'budget' => 0, 'crate' => 0, 'irate' => 0, 'cities' => serialize(array()), 'countries' => serialize(array())));
	    $edit_id = $wpdb->insert_id;
	}
	$ad_edit_id = $edit_id;
}

$edit_banner = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = '$ad_edit_id';");

if($edit_banner) {
	wp_enqueue_media();
	wp_enqueue_script('uploader-hook', plugins_url().'/adrotate-pro/library/uploader-hook.js', array('jquery'));
	?>
	
		<form method="post" action="admin.php?page=adrotate">
		<?php wp_nonce_field('adrotate_generate_ad','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_banner->id;?>" />
	
		<h2><?php _e('Generate Advert Code', 'adrotate-pro'); ?></h2>
		<p><?php _e('Use the Generator if you have received a target url, banner image and/or some separate files with a description on how to use those. The AdRotate Generator will take your bits and pieces and try to generate a working adcode from it. If you have an advert hash from another AdRotate or AdRotate Professional setup you can enter it in the Portability field.', 'adrotate-pro'); ?></p>
		<p><?php _e('If you have received a complete and working ad code / ad tag you do not use the Generator. You can often simply paste that code in the AdCode field when creating your advert.', 'adrotate-pro'); ?></p>
	
		<h2><?php _e('Create your advert', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">
	
			<thead>
			<tr>
		        <th colspan="2"><strong><?php _e('Required', 'adrotate-pro'); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
		        <th valign="top"><?php _e('Banner image', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_fullsize_dropdown">
						<select tabindex="1" id="adrotate_fullsize_dropdown" name="adrotate_fullsize_dropdown" style="min-width: 300px;">
	   						<option value=""><?php _e('Select advert image', 'adrotate-pro'); ?></option>
							<?php
							foreach(adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png', 'mp4')) as $key => $option) {
								echo "<option value=\"$option\">$option</option>";
							}
							?>
						</select> <?php _e('Is your file not listed? Upload it via the AdRotate Media Manager.', 'adrotate-pro'); ?>
					</label>
				</td>
			</tr>
			<tr>
		        <th width="15%" valign="top"><?php _e('Target website', 'adrotate-pro'); ?></th>
		        <td>
			        <label for="adrotate_targeturl"><input tabindex="2" id="adrotate_targeturl" name="adrotate_targeturl" type="text" size="60" class="ajdg-inputfield" value="" autocomplete="off" /> <?php _e('Where does the person clicking the advert go?', 'adrotate-pro'); ?></label>
		        </td>
			</tr>
			</tbody>
			
			<thead>
			<tr>
		        <th colspan="2"><strong><?php _e('Viewports', 'adrotate-pro'); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
		        <th valign="top"><?php _e('Smaller Devices', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_small_dropdown">
						<select tabindex="3" id="adrotate_small_dropdown" name="adrotate_small_dropdown" style="min-width: 300px;">
	   						<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php
							foreach(adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png')) as $key => $option) {
								echo "<option value=\"$option\">$option</option>";
							}
							?>
						</select> <em><?php _e('Smaller smartphones and tablets with a viewport of up to 480px wide (up-to 1440px resolution).', 'adrotate-pro'); ?></em>
					</label>		
				</td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Medium sized Devices', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_medium_dropdown">
						<select tabindex="4" id="adrotate_medium_dropdown" name="adrotate_medium_dropdown" style="min-width: 300px;">
	   						<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php
							foreach(adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png')) as $key => $option) {
								echo "<option value=\"$option\">$option</option>";
							}
							?>
						</select> <em><?php _e('Larger smartphones or Small tablets with a viewport of up to 960px wide (up-to 1536px resolution).', 'adrotate-pro'); ?></em>
					</label>		
				</td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Larger Devices', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_large_dropdown">
						<select tabindex="5" id="adrotate_large_dropdown" name="adrotate_large_dropdown" style="min-width: 300px;">
	   						<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php
							foreach(adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png')) as $key => $option) {
								echo "<option value=\"$option\">$option</option>";
							}
							?>
						</select> <em><?php _e('Small laptops and Larger tablets with a viewport of up to 1280px wide (up-to 2048px resolution).', 'adrotate-pro'); ?></em>
					</label>		
				</td>
			</tr>
			<tr>
		        <td colspan="2"><strong><?php _e('Important:', 'adrotate-pro'); ?></strong> <?php _e('All sizes are optional, but it is highly recommended to use at least the small and medium size. Devices with viewports greater than 1280px will use the full sized banner.', 'adrotate-pro'); ?><br /><?php _e('Are your files not listed? Upload them via the AdRotate Media Manager. For your convenience, use easy to use filenames.', 'adrotate-pro'); ?></td>
			</tr>
			</tbody>
	
			<thead>
			<tr>
		        <th colspan="2"><strong><?php _e('Video adverts', 'adrotate-pro'); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
		        <th valign="top"><?php _e('Aspect Ratio', 'adrotate-pro'); ?></th>
				<td>
					<label for="adrotate_video_ratio">
						<select tabindex="3" id="adrotate_video_ratio" name="adrotate_video_ratio" style="min-width: 300px;">
	   						<option value="0"><?php _e('This is not a video advert', 'adrotate-pro'); ?></option>
							<option value="1">7.8 ratio (ex. 468x60)</option>
							<option value="2">8.1 ratio (ex. 728x90)</option>
							<option value="3">1:1 ratio (ex. 200x200, 250x250, 300x300)</option>
							<option value="4">16:9 ratio (ex. 1920x1080, 1280x720 and 854x480)</option>
							<option value="5">9:16 ratio (ex. 1080x1920, 720x1280 and 480x854)</option>
							<option value="6">4:3 ratio (ex. 320x240, 640x480, 1280x960)</option>
						</select> <em><?php _e('If you are making a video advert. Pick your video resolution, this is used to scale the video for smaller or larger screens.', 'adrotate-pro'); ?></em>
					</label>		
				</td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Sound muted', 'adrotate-pro'); ?></th>
		        <td>
					<label for="adrotate_video_muted"><input tabindex="6" type="checkbox" name="adrotate_video_muted" id="adrotate_video_muted" checked="1" /> <?php _e('Should the sound be muted?', 'adrotate-pro'); ?> <?php _e('(Guidelines say yes!)', 'adrotate-pro'); ?></label>
		        </td>
	 		</tr>
			<tr>
		        <td colspan="2"><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('For now, only MP4 files are supported.', 'adrotate-pro'); ?></td>
			</tr>
			</tbody>

			<thead>
			<tr>
		        <th colspan="2"><strong><?php _e('Optional', 'adrotate-pro'); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
		        <th valign="top"><?php _e('Target window', 'adrotate-pro'); ?></th>
		        <td>
					<label for="adrotate_newwindow"><input tabindex="6" type="checkbox" name="adrotate_newwindow" id="adrotate_newwindow" checked="1" /> <?php _e('Open the advert in a new window?', 'adrotate-pro'); ?> <?php _e('(Recommended)', 'adrotate-pro'); ?></label>
		        </td>
	 		</tr>
	    	<tr>
				<th valign="top"><?php _e('NoFollow', 'adrotate-pro'); ?></th>
		        <td>
					<label for="adrotate_nofollow"><input tabindex="7" type="checkbox" name="adrotate_nofollow" id="adrotate_nofollow" checked="1" /> <?php _e('Tell crawlers and search engines not to follow the target website url?', 'adrotate-pro'); ?> <?php _e('(Recommended)', 'adrotate-pro'); ?></label><br /><em><?php _e('Letting bots (Such as Googlebot) index paid links may negatively affect your SEO and PageRank.', 'adrotate-pro'); ?></em>
		        </td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Alt and Title', 'adrotate-pro'); ?></th>
		        <td>
					<label for="adrotate_title_attr"><input tabindex="8" type="checkbox" name="adrotate_title_attr" id="adrotate_title_attr" /> <?php _e('Add an alt and title attribute based on the asset name?', 'adrotate-pro'); ?></label><br /><em><?php _e('Some bots/crawlers use them as a descriptive measure to see what the code is about.', 'adrotate-pro'); ?></em>
		        </td>
	 		</tr>
			</tbody>
	
			<thead>
			<tr>
		        <th colspan="2"><strong><?php _e('Portability', 'adrotate'); ?></strong></th>
			</tr>
			</thead>
			
			<tbody>
			<tr>
		        <th valign="top"><?php _e('Advert hash', 'adrotate'); ?></th>
		        <td>
					<textarea tabindex="2" id="adrotate_portability" name="adrotate_portability" cols="70" rows="5" class="ajdg-fullwidth" placeholder="<?php _e('To import a ready made advert paste an advert hash from another AdRotate setup.', 'adrotate'); ?>"></textarea>
		        </td>
	 		</tr>
			</tbody>
	
		</table>
	
		<p class="submit">
			<input tabindex="9" type="submit" name="adrotate_generate_submit" class="button-primary" value="<?php _e('Generate and Configure Advert', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a> <?php _e('Always test your adverts before activating them.', 'adrotate-pro'); ?>
		</p>
	
		<p><em><strong><?php _e('Caution:', 'adrotate-pro'); ?></strong> <?php _e('While the Code Generator has been tested and works, code generation, as always, is a interpretation of user input. If you provide the correct bits and pieces, a working advert may be generated. If you leave fields empty or insert the wrong info you probably end up with a broken advert.', 'adrotate-pro'); ?><br /><strong><?php _e('NOTE:', 'adrotate-pro'); ?></strong> <?php _e('If you insert an Advert Hash, all other fields are ignored.', 'adrotate-pro'); ?></em></p>

	</form>
<?php
} else {
	echo adrotate_error('error_loading_item');
}
?>