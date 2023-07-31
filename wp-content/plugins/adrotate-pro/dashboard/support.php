<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$adverts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `title` != '' ORDER BY `id` ASC;");
?>
<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">
		<div id="left-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('Getting help with AdRotate Professional', 'adrotate-pro'); ?></h2>
				<div id="news" class="ajdg-postbox-content">
					<p><img src="<?php echo plugins_url('/images/icon-support.png', dirname(__FILE__)); ?>" class="alignleft pro-image" /><?php _e('New to AdRotate Banner Manager? Is something not working the way you expect it to? When you need help with AdRotate or AdRotate Pro you can check the manuals and guides on my website. Also there is a lot of information on the support forum asked by other users. Chances are your question has already been asked and answered!', 'adrotate-pro'); ?></p>

					<p><strong><?php _e('Premium support', 'adrotate-pro'); ?></strong><br /><em><?php _e('Use the form on the right to create a ticket. Or visit the ticket support page.', 'adrotate-pro'); ?> <a href="https://support.ajdg.net/" target="_blank"><?php _e('Ticket support', 'adrotate-pro'); ?> &raquo;</a></em></p>
					<p><a href="https://ajdg.solutions/support/adrotate-manuals/?mtm_campaign=adrotatefree&mtm_keyword=support_page&mtm_content=manuals_link" target="_blank"><strong><?php _e('AdRotate manuals and guides', 'adrotate-pro'); ?></strong></a><br /><em><?php _e('Take a look at the AdRotate Manuals.', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/support/adrotate-manuals/?mtm_campaign=adrotatefree&mtm_keyword=support_page&mtm_content=manuals_link" target="_blank"><?php _e('View knowledgebase', 'adrotate-pro'); ?> &raquo;</a></em></p>
					<p><a href="https://ajdg.solutions/forums/" target="_blank"><strong><?php _e('AdRotate support forum', 'adrotate-pro'); ?></strong></a><br /><em><?php _e('Ask anything about AdRotate here on the forum.', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/forums/" target="_blank"><?php _e('View topics', 'adrotate-pro'); ?> &raquo;</a></em></p>
					<p><a href="https://support.ajdg.net/" target="_blank"><strong><?php _e('Security issues', 'adrotate-pro'); ?></strong></a><br /><em><?php _e('Found a security flaw?', 'adrotate-pro'); ?> <a href="https://support.ajdg.net/" target="_blank"><?php _e('Report it', 'adrotate-pro'); ?> &raquo;</a></em></p>
				</div>
			</div>

			<div class="ajdg-postbox">
				<h2 class="ajdg-postbox-title">More plugins and services</h2>
				<div id="services" class="ajdg-postbox-content">
					<p>Check out these and more services in more details on my website. I also make more plugins. If you like AdRotate - Maybe you like some of those as well. Take a look at the <a href="https://ajdg.solutions/plugins/?mtm_campaign=adrotatepro&mtm_keyword=support_page" target="_blank">plugins</a> and overall <a href="https://ajdg.solutions/pricing/?mtm_campaign=adrotatepro&mtm_keyword=support_page" target="_blank">pricing</a> page for more.</p>
					<table width="100%">
						<tr>
							<td width="33%">
								<div class="ajdg-sales-widget" style="display: inline-block; margin-right:2%;">
									<a href="https://ajdg.solutions/product/adrotate-html5-setup-service/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/html5-service.jpg", dirname(__FILE__)); ?>" alt="HTML5 Advert setup" width="228" height="120"></div></a>
									<a href="https://ajdg.solutions/product/adrotate-html5-setup-service/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="title">HTML5 Advert setup</div></a>
									<div class="sub_title">Professional service</div>
									<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/adrotate-html5-setup-service/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank">Learn more</a></div>
									<hr>
									<div class="description">Do you have a HTML5 advert but can’t get it to work in AdRotate Pro? I’ll install and configure it for you.</div>
								</div>							
							</td>
							<td width="33%">
								<div class="ajdg-sales-widget" style="display: inline-block; margin-right:2%;">
									<a href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/wordpress-maintenance.jpg", dirname(__FILE__)); ?>" alt="WordPress Maintenance" width="228" height="120"></div></a>
									<a href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="title">Maintenance</div></a>
									<div class="sub_title">Professional service</div>
									<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/wordpress-maintenance-and-updates/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank">Get started</a></div>
									<hr>								
									<div class="description">Get all the latest updates for WordPress and plugins. Maintenance, delete spam and clean up files.</div>
								</div>
							</td>
							<td>
								<div class="ajdg-sales-widget" style="display: inline-block;">
									<a href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="header"><img src="<?php echo plugins_url("/images/offers/single-page-checkout.jpg", dirname(__FILE__)); ?>" alt="WooCommerce Single Page Checkout" width="228" height="120"></div></a>
									<a href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank"><div class="title">Single Page Checkout</div></a>
									<div class="sub_title">WooCommerce Plugin</div>
									<div class="cta"><a role="button" class="cta_button" href="https://ajdg.solutions/product/woocommerce-single-page-checkout/?mtm_campaign=adrotatepro&mtm_keyword=info_page" target="_blank">View product page</a></div>
									<hr>
									<div class="description">Merge your cart and checkout pages into one single page in seconds with no setup required at all.</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

		</div>
		<div id="right-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">
				<h2 class="ajdg-postbox-title"><?php _e('Premium Support Ticket', 'adrotate-pro'); ?></h2>
				<div id="support" class="ajdg-postbox-content">
					<?php if($a['status'] == 1 AND $a['created'] < (current_time('timestamp') - (DAY_IN_SECONDS * 365))) { ?>
						<p><img src="<?php echo plugins_url('/images/icon-contact.png', dirname(__FILE__)); ?>" class="alignleft pro-image" /><strong><?php _e('Your license has expired. In order to continue to receive updates, support and access to AdRotate Geo please get a new license.', 'adrotate-pro'); ?></strong><br /><?php _e('As a thank you for your continued use and support of AdRotate Professional you can get a new license at a special discounted price.', 'adrotate-pro'); ?></a>
						<p><?php _e('Licenses are valid for 1 year from purchasing. Activating a new license key will re-enable the Premium Support contact form and lets you use AdRotate Geo again.', 'adrotate-pro'); ?></a>
</p>

						<p class="submit">
							<a href="https://ajdg.solutions/support/adrotate-manuals/adrotate-pro-license-renewal/?mtm_campaign=adrotatepro&mtm_keyword=support_page&mtm_content=renewal_link" target="_blank" class="button-primary"><?php _e('Get your new License', 'adrotate-pro'); ?></a>	
							<?php if(adrotate_is_networked()) { ?>
								<a href="<?php echo network_admin_url('admin.php?page=adrotate'); ?>" class="button"><?php _e('Replace License key', 'adrotate-pro'); ?></a>
							<?php } else { ?>
								<a href="<?php echo admin_url('admin.php?page=adrotate-settings&tab=license'); ?>" class="button"><?php _e('I have a new License key already', 'adrotate-pro'); ?></a>	
							<?php } ?>
							<a href="https://support.ajdg.net/" class="button" target="_blank"><?php _e('I need help', 'adrotate-pro'); ?></a>	
						</p>
					<?php } else if($a['status'] == 1 AND $a['created'] > (current_time('timestamp') - (DAY_IN_SECONDS * 365))) { ?>					
						<form name="request" id="post" method="post" action="admin.php?page=adrotate">
							<?php wp_nonce_field('ajdg_nonce_support_request','ajdg_nonce_support'); ?>
						
							<p><img src="<?php echo plugins_url('/images/icon-contact.png', dirname(__FILE__)); ?>" class="alignleft pro-image" />&raquo; <?php _e('What went wrong? Or what are you trying to do?', 'adrotate-pro'); ?><br />&raquo; <?php _e('Include error messages and/or relevant information.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Try to remember any actions that may cause the problem.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Any code/HTML will be stripped from your message.', 'adrotate-pro'); ?></p>
						
							<h2><?php _e('Your question', 'adrotate-pro'); ?></h2>
							<p><label for="ajdg_support_username"><strong><?php _e('Your name:', 'adrotate-pro'); ?></strong><br /><input tabindex="1" name="ajdg_support_username" type="text" class="search-input" style="width:100%;" value="<?php echo $current_user->display_name;?>" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_email"><strong><?php _e('Your Email Address:', 'adrotate-pro'); ?></strong><br /><input tabindex="2" name="ajdg_support_email" type="text" class="search-input" style="width:100%;" value="<?php echo $current_user->user_email;?>" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_subject"><strong><?php _e('Subject:', 'adrotate-pro'); ?></strong><br /><input tabindex="3" name="ajdg_support_subject" type="text" class="search-input" style="width:100%;" value="" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_message"><strong><?php _e('Problem description / Question:', 'adrotate-pro'); ?></strong><br /><textarea tabindex="4" name="ajdg_support_message" style="width:100%; height:100px;"></textarea></label></p>

							<p><label for="ajdg_support_advert"><strong><?php _e('Advert I need help with:', 'adrotate-pro'); ?></strong> 
								<select tabindex="16" name="ajdg_support_advert">
							        <option value="0"><?php _e('-- Optionally select an advert --', 'adrotate-pro'); ?></option>
								<?php if($adverts) { ?>
									<?php foreach($adverts as $advert) { ?>
								        <option value="<?php echo $advert->id;?>"><?php echo $advert->id;?> - <?php echo stripslashes($advert->title);?></option>
									<?php } ?>
								<?php } ?>
									</select> <span class="ajdg-tooltip">[?]<span class="ajdg-tooltiptext ajdg-tooltip-top">Selecting an advert will include the ad settings for Arnan to test on his website. Does not include group or schedule data.</span></span>
							</label></p>

							<p><label for="ajdg_support_account"><input tabindex="5" name="ajdg_support_account" id="ajdg_support_account" type="checkbox" /> <?php _e('Please log in to my website and take a look.', 'adrotate-pro'); ?> <span class="ajdg-tooltip">[?]<span class="ajdg-tooltiptext ajdg-tooltip-top">Checking this option will create a (temporary) account for Arnan to log in and take a look at your setup.</span></span></label></p>
<!-- 							<p><label for="ajdg_support_copy"><input tabindex="5" name="ajdg_support_copy" id="ajdg_support_copy" type="checkbox" /> <?php _e('Send me a copy of this message.', 'adrotate-pro'); ?></label></p> -->

							<h2><?php _e('Your feedback (optional)', 'adrotate-pro'); ?></h2>
							<p><label for="ajdg_support_favorite"><strong><?php _e('Favorite feature in AdRotate Professional?', 'adrotate-pro'); ?></strong><br /><input tabindex="6" name="ajdg_support_favorite" type="text" class="search-input" style="width:100%;" value="" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_feedback"><strong><?php _e('Which feature do you think should be improved?', 'adrotate-pro'); ?></strong><br /><input tabindex="7" name="ajdg_support_feedback" type="text" class="search-input" style="width:100%;" value="" autocomplete="off" /></label></p>
</span></label></p>
						
							<p><strong><?php _e('When you send this form the following data will be submitted:', 'adrotate-pro'); ?></strong><br/>
							<em><?php _e('Your name, Account email address, Your website url and some basic WordPress information will be included with the message.', 'adrotate-pro'); ?><br /><?php _e('This information is treated as confidential and is mandatory.', 'adrotate-pro'); ?></em></p>
						

							<p class="submit">
								<input tabindex="8" type="submit" name="adrotate_support_submit" class="button-primary" value="<?php _e('Create ticket', 'adrotate-pro'); ?>" />&nbsp;&nbsp;&nbsp;<em><?php _e('Please use English or Dutch only!', 'adrotate-pro'); ?></em>
							</p>

							<p><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('Creating multiple tickets with the same question will put you at the very end of my support priorities. Please do not send the same message more than once here or elsewhere. Thank you!', 'adrotate-pro'); ?></p>
						
						</form>
			
					<?php } else { ?>
						<p><img src="<?php echo plugins_url('/images/icon-contact.png', dirname(__FILE__)); ?>" class="alignleft pro-image" /><?php _e('When you activate your AdRotate Professional license you can use fast email support right from this page. No more queueing up in the forums. Email support always gets priority over the forums and is checked almost every workday.', 'adrotate-pro'); ?></p>

						<p class="submit">
							<?php if(adrotate_is_networked()) { ?>
								<a href="<?php echo network_admin_url('admin.php?page=adrotate'); ?>" class="button-primary"><?php _e('Activate License', 'adrotate-pro'); ?></a>
							<?php } else { ?>
								<a href="<?php echo admin_url('admin.php?page=adrotate-settings&tab=license'); ?>" class="button-primary"><?php _e('Activate License', 'adrotate-pro'); ?></a>	
							<?php } ?>
							<a href="https://support.ajdg.net/" class="button" target="_blank"><?php _e('I need help with my license', 'adrotate-pro'); ?></a>	
						</p>
					<?php }	?>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="clear"></div>
<p><?php echo adrotate_trademark(); ?></p>