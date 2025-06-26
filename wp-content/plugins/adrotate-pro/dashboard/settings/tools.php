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
<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=tools" enctype="multipart/form-data">
<?php wp_nonce_field('adrotate_import','adrotate_nonce_tools'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />
<input type="hidden" name="MAX_FILE_SIZE" value="4096000" />

<h2><?php _e('Tools', 'adrotate-pro'); ?></h2>

<h3><?php _e('Bulk import adverts', 'adrotate-pro'); ?></h3>
<label for="adrotate_file"><input type="file" name="adrotate_file" id="file" size="100" /></label><br />
<em><strong><?php _e('Accepted files:', 'adrotate-pro'); ?></strong> CSV.<br />
<?php _e('Make sure the file is smaller than 4096Kb (up to approximately 1000 adverts in 1 file).', 'adrotate-pro'); ?></em>

<p class="submit">
	<label for="adrotate_import"><input tabindex="2" type="submit" name="adrotate_import" class="button-primary" value="<?php _e('Import', 'adrotate-pro'); ?>" /> <em><?php _e('Click only once!', 'adrotate-pro'); ?></em></label>
</p>

</form>