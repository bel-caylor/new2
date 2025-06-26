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

<?php $assets = adrotate_mediapage_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder']); ?>
<?php $reports = adrotate_mediapage_folder_contents(WP_CONTENT_DIR."/reports", array("csv")); ?>

<form method="post" action="admin.php?page=adrotate-media" id="file" enctype="multipart/form-data">
	<?php wp_nonce_field('adrotate_save_media','adrotate_nonce'); ?>
	<input type="hidden" name="MAX_FILE_SIZE" value="512000" />

	<h2><?php _e('Upload new file or advert', 'adrotate-pro'); ?></h2>
	<select tabindex="1" id="adrotate_image_location" name="adrotate_image_location" style="min-width: 200px;">
		<option value="<?php echo $adrotate_config['banner_folder']; ?>"><?php echo $adrotate_config['banner_folder']; ?> <?php _e('folder (Default)', 'adrotate-pro'); ?></option>
	<?php
	if(count($assets) > 0) {
		foreach($assets as $asset) {
			if(array_key_exists("contents", $asset)) {
				echo '<option value="'.$adrotate_config['banner_folder'].'/'.$asset['basename'].'">&mdash; '.$asset['basename'].'</option>';
				foreach($asset['contents'] as $level_one) {
					if(array_key_exists("contents", $level_one)) {
						echo '<option value="'.$adrotate_config['banner_folder'].'/'.$asset['basename'].'/'.$level_one['basename'].'">&mdash; &mdash; '.$level_one['basename'].'</option>';
					}
				}		
			}
		}
	}
	?>
	</select>
	<input tabindex="2" type="file" name="adrotate_image" /><br /><em><strong><?php _e('Accepted files:', 'adrotate-pro'); ?></strong> jpg, jpeg, gif, png, svg, html, js and zip. <?php _e('Maximum size is 512Kb per file.', 'adrotate-pro'); ?></em><br /><em><strong><?php _e('Important:', 'adrotate-pro'); ?></strong> <?php _e('Make sure your file has no spaces or special characters in the name. Replace spaces with a - or _.', 'adrotate-pro'); ?><br /><?php _e('Zip files are automatically extracted in the location where they are uploaded and the original zip file will be deleted once extracted.', 'adrotate-pro'); ?></em>

	<p class="submit">
		<input tabindex="3" type="submit" name="adrotate_media_submit" class="button-primary" value="<?php _e('Upload file', 'adrotate-pro'); ?>" />
	</p>

	<h2><?php _e('Create new folder', 'adrotate-pro'); ?></h2>
	<input tabindex="4" id="adrotate_folder" name="adrotate_folder" type="text" size="40" class="ajdg-inputfield" value="" autocomplete="off" />
	<br /><em><strong><?php _e('Important:', 'adrotate-pro'); ?></strong> <?php _e('Folder names can between 1 and 100 characters long. Any special characters are stripped out.', 'adrotate'); ?></em>

	<p class="submit">
		<input tabindex="5" type="submit" name="adrotate_folder_submit" class="button-secondary" value="<?php _e('Create folder', 'adrotate-pro'); ?>" />
	</p>

	<h2><?php _e('Uploaded files in', 'adrotate-pro'); ?> '<?php echo '/'.$adrotate_config['banner_folder']; ?>'</h2>
	<p><?php _e('Upload images and files to the AdRotate Professional banners folder from here, including zipped HTML5 adverts. Files listed here will populate the dropdown menu when editing adverts.', 'adrotate-pro'); ?></p>
	<table class="widefat" style="margin-top: .5em">
	
		<thead>
		<tr>
	        <th><?php _e('Name', 'adrotate-pro'); ?></th>
		</tr>
		</thead>
	
		<tbody>
		<?php
		if(count($assets) > 0) {
			$class = '';
			foreach($assets as $asset) {
				$class = ($class != 'alternate') ? 'alternate' : '';
				
				echo "<tr class=\"$class\">";
				echo "<td>";
				echo $asset['basename'];
				echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'])."&path=banners&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
				if(array_key_exists("contents", $asset)) {
					echo "<small>";
					foreach($asset['contents'] as $level_one) {
						echo "<br />&mdash; ".$level_one['basename'];
						echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'].'/'.$level_one['basename'])."&path=banners&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'].'/'.$level_one['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
						if(array_key_exists("contents", $level_one)) {
							foreach($level_one['contents'] as $level_two) {
								echo "<br />&mdash;&mdash; ".$level_two['basename'];
								echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'].'/'.$level_one['basename'].'/'.$level_two['basename'])."&path=banners&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'].'/'.$level_one['basename'].'/'.$level_two['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
							}		
						}
					}		
					echo "</small>";
				}
				echo "</td>";
				echo "</tr>";
			}
		} else {
			echo "<tr class=\"alternate\">";
			echo "<td><em>".__('No files found!', 'adrotate-pro')."</em></td>";
			echo "</tr>";
		}
		?>
		</tbody>
	</table>
</form>
<p><center><small>
	<?php _e("Make sure the banner images are not in use by adverts when you delete them!", "adrotate-pro"); ?> <?php _e("Deleting a folder deletes everything inside that folder as well!", "adrotate-pro"); ?>
</small></center></p>

<h2><?php _e('Generated reports', 'adrotate-pro'); ?></h2>
<p><em><?php _e("Download or delete reports from here. Reports are generated when exporting stats.", "adrotate-pro"); ?></em></p>
<table class="widefat" style="margin-top: .5em">

	<thead>
	<tr>
        <th><?php _e('Name', 'adrotate-pro'); ?></th>
	</tr>
	</thead>

	<tbody>
	<?php
	if(count($reports) > 0) {
		$class = '';
		foreach($reports as $report) {
			$class = ($class != 'alternate') ? 'alternate' : '';
			
			echo "<tr class=\"$class\">";
			echo "<td>";
			echo $report['basename'];
			echo "<span style=\"float:right;\"><a href=\"".WP_CONTENT_URL."/reports/".$report['basename']."\" title=\"".__('Download', 'adrotate-pro')."\" target=\"_blank\">".__('Download', 'adrotate-pro')."</a> - <a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$report['basename'])."&path=reports&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$report['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
			echo "</td>";
			echo "</tr>";
		}
	} else {
		echo "<tr class=\"alternate\">";
		echo "<td><em>".__('No reports found!', 'adrotate-pro')."</em></td>";
		echo "</tr>";
	}
	?>
	</tbody>
</table>