<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$user = get_user_by('id', $user_id);
$alladverts = $wpdb->get_results("SELECT `ad`, `title`, `type` FROM `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `user` = '{$user->ID}' AND `{$wpdb->prefix}adrotate`.`id` = `ad` AND `type` != 'empty' AND `type` != 'a_empty' ORDER BY `{$wpdb->prefix}adrotate`.`id` ASC;");
$status = array('active' => 'Active', 'queue' => 'Awaiting Moderation', 'archived' => 'Archived', 'trash' => 'Trashed');
?>
<h2><?php _e('Contact your Advertiser', 'adrotate-pro'); ?></h2>

<form name="request" id="post" method="post" action="admin.php?page=adrotate-advertisers">
	<?php wp_nonce_field('adrotate_email_advertiser','adrotate_nonce'); ?>
	<input type="hidden" name="adrotate_username" value="<?php echo $user->display_name;?>" />
	<input type="hidden" name="adrotate_email" value="<?php echo $user->user_email;?>" />

	<table class="widefat" style="margin-top: .5em">
		<tbody>
		    <tr>
				<th width="15%"><?php _e('Subject', 'adrotate-pro'); ?></th>
				<td><label for="adrotate_subject"><input tabindex="1" id="adrotate_subject" name="adrotate_subject" type="text" size="50" class="ajdg-inputfield" value="" autocomplete="off" /></label>
</td>
			</tr>
		    <tr>
				<th width="15%"><?php _e('Advert/Campaign', 'adrotate-pro'); ?></th>
				<td>				
					<label for="adrotate_advert">
					<select tabindex="2" id="adrotate_advert" name="adrotate_advert" style="min-width: 200px;">
						<option value=""><?php _e('No advert in particular', 'adrotate-pro'); ?></option>
						<?php foreach($alladverts as $advert) { ?>
							<option value="<?php echo $advert->ad; ?>"><?php echo $advert->ad; ?> - <?php echo $advert->title; ?> (<?php echo $status[$advert->type]; ?>)</option>
						<?php } ?>
					</select>
					</label>
				</td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Short message', 'adrotate-pro'); ?></th>
				<td><textarea tabindex="3" name="adrotate_message" cols="50" rows="5"></textarea></td>
			</tr>
		</tbody>
	</table>

	<p class="submit">
		<input tabindex="4" type="submit" name="adrotate_contact_submit" class="button-primary" value="<?php _e('Send', 'adrotate-pro'); ?>" />
		<a href="admin.php?page=adrotate-advertisers" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
	</p>

</form>