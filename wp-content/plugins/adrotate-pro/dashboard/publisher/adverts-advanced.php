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

<?php
if(is_array($adstxt_content)) {
	$adstxt_content = trim(implode("\n", $adstxt_content));
	$adstxt_content = str_replace("\n\n", "\n", $adstxt_content);
}
?>

<form name="settings" id="post" method="post" action="admin.php?page=adrotate&view=advanced">
	<?php wp_nonce_field('adrotate_nonce','adrotate_nonce_header'); ?>
	
	<h2><?php _e('Google Ad Manager / Adsense Auto-Ads', 'adrotate-pro'); ?></h2>
	<span class="description"><?php _e('Add Googles header code in your websites header into the field below.', 'adrotate-pro'); ?></span>

	<p><textarea name="adrotate_gam" cols="90" rows="12"><?php echo stripslashes($adrotate_gam); ?></textarea><br />
	<span class="description"><?php _e('Make sure you paste the code exactly as provided without alterations. If you have more than 1 header code, please combine them. Read more in the manual', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/support/adrotate-manuals/google-ad-manager/" target="_blank">Google Ad Manager</a>.<br /><?php _e('The body code goes in the AdCode in a new advert as normal.', 'adrotate-pro'); ?></span></p>

	<h2><?php _e('Header Snippet', 'adrotate-pro'); ?></h2>
	<span class="description"><?php _e('Use this field for code from adverts that require a piece of code in the header. Some newer Google AdSense adverts require this.', 'adrotate-pro'); ?></span>
	
	<p><textarea name="adrotate_header" cols="90" rows="10"><?php echo stripslashes($adrotate_header); ?></textarea><br />
	<span class="description"><?php _e('Make sure you paste the code exactly as provided without alterations. If you have more than one snippet paste them under each other on a new line.', 'adrotate-pro'); ?><br />
	<?php _e('If you have multiple snippets but some are identical you should only use one snippet.', 'adrotate-pro'); ?></span></p>
	
	<h2><?php _e('ads.txt', 'adrotate-pro'); ?></h2>
	<span class="description"><?php _e('Add authorized publishers here to make sure the adverts you display are legitimate. Only applies to advertising networks. The use of ads.txt is optional.', 'adrotate-pro'); ?></span>
	
	<p><textarea name="adrotate_adstxt" cols="90" rows="10"><?php echo stripslashes($adstxt_content); ?></textarea><br />
	<span class="description"><?php _e('An authorization consists of 3 or 4 parts; domainname, Publisher ID, Type [, Authority ID]. Comment lines are prefixed with a #.','adrotate-pro'); ?><br /><?php _e('Typos and wrong information may result in missing adverts or the wrong adverts being shown on your website.', 'adrotate-pro'); ?></span></p>
	
	<p class="submit">
	  	<input type="submit" name="adrotate_save_header" class="button-primary" value="<?php _e('Save', 'adrotate-pro'); ?>" />
	</p>
</form>