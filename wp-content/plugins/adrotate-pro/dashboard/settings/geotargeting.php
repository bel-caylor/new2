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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=geo">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Geo Targeting', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Target certain areas in the world for better advertising oppurtunities.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Which Geo Service', 'adrotate-pro'); ?></th>
		<td>
			<select name="adrotate_enable_geo">
				<option value="0" <?php if($adrotate_config['enable_geo'] == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate-pro'); ?></option>
				<option value="5" <?php if($adrotate_config['enable_geo'] == 5) { echo 'selected'; } ?>>AdRotate Geo</option>
				<option value="7" <?php if($adrotate_config['enable_geo'] == 7) { echo 'selected'; } ?>>ipstack</option>
				<option value="4" <?php if($adrotate_config['enable_geo'] == 4) { echo 'selected'; } ?>>MaxMind City</option>
				<option value="3" <?php if($adrotate_config['enable_geo'] == 3) { echo 'selected'; } ?>>MaxMind Country</option>
				<option value="6" <?php if($adrotate_config['enable_geo'] == 6) { echo 'selected'; } ?>>CloudFlare</option>
			</select><br />
			<span class="description">
				<p><strong>AdRotate Geo</strong> - <?php _e('30000 free lookups every day, uses GeoLite2 databases from MaxMind!', 'adrotate-pro'); ?><br />
				<em><strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> ipv4/ipv6, Countries, Cities, DMA codes, States and State ISO (3166-2) codes.</em><br />
				<em><strong><?php _e('Scalability:', 'adrotate-pro'); ?></strong> <?php _e('Suitable for small to medium sized websites.', 'adrotate-pro'); ?></em><br /><br />

				<p><strong>ipstack</strong> (<a href="https://ipstack.com/product" target="_blank>"><?php _e('More info', 'adrotate-pro'); ?></a>) - <?php _e('10000 free lookups per month, requires account.', 'adrotate-pro'); ?><br />
				<em><strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> ipv4, Countries, Cities, DMA codes, States and State ISO (3166-2) codes.</em><br />
				<em><strong><?php _e('Scalability:', 'adrotate-pro'); ?></strong> <?php _e('Suitable for small to medium sized websites with a free account - Paid options available.', 'adrotate-pro'); ?></em><br /><br />

				<strong>MaxMind GeoIP2</strong> (<a href="https://www.maxmind.com/en/geoip2-precision-city-service" target="_blank>"><?php _e('More info', 'adrotate-pro'); ?></a>)- <?php _e('The most accurate geo targeting available.', 'adrotate-pro'); ?><br />
				<em><strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> ipv4/ipv6, Countries, States, State ISO (3166-2) codes, Cities and DMA codes.</em><br />
				<em><strong><?php _e('Scalability:', 'adrotate-pro'); ?></strong> <?php _e('Suitable for any size website as long as you have lookups.', 'adrotate-pro'); ?></em><br /><br />

				<strong>CloudFlare IP Geolocation</strong> - <?php _e('Basic geolocation included in every CloudFlare account.', 'adrotate-pro'); ?><br />
				<em><strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> ipv4/ipv6, Countries.</em><br />
				<em><strong><?php _e('Scalability:', 'adrotate-pro'); ?></strong> <?php _e('Suitable for any size website.', 'adrotate-pro'); ?></em>
			</span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Lookups remaining', 'adrotate-pro'); ?></th>
		<td><?php echo $adrotate_geo_requests; ?> <span class="description"><?php _e('This number is provided by the geo service and not checked for accuracy. Not every geo service provides a quota.', 'adrotate-pro'); ?></span></td>
	</tr>
</table>

<h3><?php _e('MaxMind City/Country and ipstack', 'adrotate-pro'); ?></h3>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('User ID', 'adrotate-pro'); ?></th>
		<td><label for="adrotate_geo_email"><input name="adrotate_geo_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_email']; ?>" autocomplete="off" /> <em>Only used for MaxMind accounts</em></label></td>
	</tr>
	<tr>
		<th valign="top"><?php _e('License Key/API Key', 'adrotate-pro'); ?></th>
		<td><label for="adrotate_geo_pass"><input name="adrotate_geo_pass" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_pass']; ?>" autocomplete="off" /> <em>Used for Maxmind and ipstack accounts</em></label></td>
	</tr>
</table>

<?php if($adrotate_config['enable_geo'] > 0) { ?>
	<?php adrotate_geolocation(); ?>
	<h3>Your Geo Targeting Data</h3>
	<p><strong>CAUTION! When you change Geo Services the session needs to refresh. You may have to navigate a page or two with adverts on them for that to happen.</strong><br />
	If re-saving doesn't seem to help, restart your browser and re-visit the dashboard.<br /><br />
	Your IP Address: <?php echo adrotate_get_remote_ip(); ?><br />
	<pre><?php
		if(isset($_SESSION['adrotate-geo'])) {
			print_r($_SESSION['adrotate-geo']);
		} else {
			echo '<p>$_SESSION not found.<br />This may happen if there are no duplicate adverts, or you did not trigger it in the front-end of your site. Sessions are usually a front-end affair only and are generally not used here in the dashboard.</p>';
		}
	?></pre>
	</p>
<?php } ?>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>