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
<form name="banners" id="post" method="post" action="admin.php?page=adrotate-advertisers">

	<h2><?php _e('Advertisers', 'adrotate-pro'); ?></h2>
	<em><?php _e('These are users marked as Advertisers. You can add more advertisers by editing existing users or creating new users.', 'adrotate-pro'); ?></em>

	<table class="widefat tablesorter manage-advertisers-main" style="margin-top: .5em">
		<thead>
		<tr>
			<th><?php _e('Name', 'adrotate-pro'); ?></th>
			<th width="25%"><?php _e('Email', 'adrotate-pro'); ?></th>
			<th width="5%"><?php _e('Adverts', 'adrotate-pro'); ?></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if (count($advertisers) > 0) {
		$class = '';
		foreach($advertisers as $user_id => $user) {			
			$errorclass = '';
			if($user['has_adverts'] <= 0) $errorclass = ' row_blue';

			$class = ('alternate' != $class) ? 'alternate' : '';
			$class = ($errorclass != '') ? $errorclass : $class;
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<td><strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertisers&view=profile&user='.$user_id);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo $user['name'];?></a></strong> - <a href="<?php echo admin_url('/admin.php?page=adrotate-statistics&view=advertiser&id='.$user_id);?>" title="<?php _e('Stats', 'adrotate-pro'); ?>"><?php _e('Stats', 'adrotate-pro'); ?></a></td>
				<td><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertisers&view=contact&user='.$user_id);?>" title="<?php _e('Contact', 'adrotate-pro'); ?>"><?php echo $user['email']; ?></a></td>
				<td><center><?php echo $user['has_adverts']; ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-advertisers'>
			<td colspan="3"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #8dcede">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has no adverts", "adrotate-pro"); ?>
</center></p>
</form>