<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>
<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">
		<div id="left-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">
				<h2 class="ajdg-postbox-title"><?php _e('Create campaign', 'adrotate-pro'); ?></h2>
				<div id="your_campaign" class="ajdg-postbox-content">

					<?php if($s['status'] == 0) { ?>

						<form name="request" id="post" method="post" action="admin.php?page=adrotate-swap">
							<?php wp_nonce_field('ajdg_nonce_swap','ajdg_nonce_swap'); ?>
						
							<p><img src="<?php echo plugins_url('../images/icon-swap.png', dirname(__FILE__)); ?>" class="alignleft pro-image" />&raquo; <?php _e('Promote your website on thousands of other websites for a week up to a year.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Share a banner image from your server for other sites to use.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Use a high quality banner with not too much text for the best results.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Use a non-intrusive image for the best chance to be picked by other AdRotate users.', 'adrotate-pro'); ?></p>
						
							<h2><?php _e('Campaign banner', 'adrotate-pro'); ?></h2>
							<p>
								<label for="adrotate_fullsize_dropdown">
									<select tabindex="1" id="adrotate_swap_image" name="adrotate_swap_image" style="min-width: 300px;">
				   						<option value=""><?php _e('Select banner image', 'adrotate-pro'); ?></option>
										<?php
										foreach(adrotate_dropdown_folder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder'], array('jpg', 'jpeg', 'gif', 'png'), 0) as $key => $option) {
											echo "<option value=\"$option\">$option</option>";
										}
										?>
									</select> 
								</label> 
							</p>

							<p><em><small><?php _e('Is your file not listed? Make sure it is a JPG, PNG or GIF image. Upload it via the AdRotate Media Manager.', 'adrotate-pro'); ?></small></em></p>

							<h2><?php _e('Your website category', 'adrotate-pro'); ?></h2>
							<p>
								<label for="adrotate_swap_category">
									<select tabindex="3" id="adrotate_swap_category" name="adrotate_swap_category" style="min-width: 300px;">
										<option value="1"><?php _e('Blog and Personal', 'adrotate-pro'); ?> (<?php _e('Default', 'adrotate-pro'); ?>)</option>
										<option value="2"><?php _e('WordPress', 'adrotate-pro'); ?></option>
										<option value="3"><?php _e('Travel and Tourism', 'adrotate-pro'); ?></option>
										<option value="4"><?php _e('Lifestyle and Design', 'adrotate-pro'); ?></option>
										<option value="5"><?php _e('Food and Cooking', 'adrotate-pro'); ?></option>
										<option value="6"><?php _e('Entertainment', 'adrotate-pro'); ?></option>
										<option value="7"><?php _e('Gaming and eSports', 'adrotate-pro'); ?></option>
										<option value="8"><?php _e('Photography and Art', 'adrotate-pro'); ?></option>
										<option value="9"><?php _e('Technology', 'adrotate-pro'); ?></option>
										<option value="10"><?php _e('News agency', 'adrotate-pro'); ?></option>
										<option value="11"><?php _e('Reviews and Product promotion', 'adrotate-pro'); ?></option>
										<option value="12"><?php _e('Sports', 'adrotate-pro'); ?></option>
										<option value="13"><?php _e('Authors and Writing', 'adrotate-pro'); ?></option>
										<option value="14"><?php _e('E-Commerce and webshops', 'adrotate-pro'); ?></option>
										<option value="15"><?php _e('Finance and Business', 'adrotate-pro'); ?></option>
										<option value="16"><?php _e('Healthcare and Medical', 'adrotate-pro'); ?></option>
										<option value="17"><?php _e('Non-Profit and NGO', 'adrotate-pro'); ?></option>
										<option value="18"><?php _e('Real-estate', 'adrotate-pro'); ?></option>
										<option value="19"><?php _e('Education', 'adrotate-pro'); ?></option>
										<option value="20"><?php _e('Opinion and Column', 'adrotate-pro'); ?></option>
										<option value="21"><?php _e('Government and Politics', 'adrotate-pro'); ?></option>
										<option value="22"><?php _e('Religion', 'adrotate-pro'); ?></option>
										<option value="23"><?php _e('Other', 'adrotate-pro'); ?></option>
										<option value="24"><?php _e('Romance/Erotic (NSFW)', 'adrotate-pro'); ?></option>
										<option value="25"><?php _e('Adult/Porn (NSFW)', 'adrotate-pro'); ?></option>
									</select> <?php _e('What is your website mostly about?', 'adrotate-pro'); ?>
								</label>
							</p>

							<h2><?php _e('Your general region', 'adrotate-pro'); ?></h2>
							<p>
								<label for="adrotate_swap_region">
									<select tabindex="4" id="adrotate_swap_region" name="adrotate_swap_region" style="min-width: 300px;">
				   						<option value="1"><?php _e('Worldwide', 'adrotate-pro'); ?></option>
				   						<option value="2"><?php _e('Europe', 'adrotate-pro'); ?></option>
				   						<option value="3"><?php _e('North America', 'adrotate-pro'); ?></option>
				   						<option value="4"><?php _e('South America', 'adrotate-pro'); ?></option>
				   						<option value="5"><?php _e('Asia', 'adrotate-pro'); ?></option>
				   						<option value="6"><?php _e('Middle East', 'adrotate-pro'); ?></option>
				   						<option value="7"><?php _e('Oceania', 'adrotate-pro'); ?></option>
				   						<option value="8"><?php _e('Africa', 'adrotate-pro'); ?></option>
									</select> <?php _e('Where is your audience mainly located?', 'adrotate-pro'); ?>
								</label>
							</p>

							<h2><?php _e('Campaign duration', 'adrotate-pro'); ?></h2>
							<p>
								<label for="adrotate_swap_duration">
									<select tabindex="5" id="adrotate_swap_days" name="adrotate_swap_days" style="min-width: 300px;">
				   						<option value="30"><?php _e('1 month', 'adrotate-pro'); ?></option>
				   						<option value="60"><?php _e('2 months', 'adrotate-pro'); ?></option>
				   						<option value="90"><?php _e('3 months', 'adrotate-pro'); ?></option>
				   						<option value="168"><?php _e('6 months', 'adrotate-pro'); ?></option>
				   						<option value="364"><?php _e('12 months', 'adrotate-pro'); ?></option>
									</select> <?php _e('For how long should the campaign run?', 'adrotate-pro'); ?>
								</label>
							</p>
							<p><em><small><?php _e('The campaign duration will start from the day it gets picked by an AdRotate user. The publisher is encouraged to keep the campaign active for the duration you choose. However, the campaign can be removed earlier.', 'adrotate-pro'); ?></small></em></p>

							<h2><?php _e('Say thanks (Optional)', 'adrotate-pro'); ?></h2>
							<p><strong><?php _e('Write a short thank you note to whoever picks your campaign.', 'adrotate-pro'); ?></strong><br /><input tabindex="9" name="ajdg_swap_thanks" type="text" class="search-input" style="width:100%;" placeholder="Thank you for choosing my campaign!" autocomplete="off" /></p>
							<p><em><small><?php _e('Please use english and refrain from posting personal info. Do not share private or sensitive information!', 'adrotate-pro'); ?></small></em></p>
						
							<p class="submit">
								<input tabindex="10" type="submit" name="adrotate_swap_submit" class="button-primary" value="<?php _e('Promote my website!', 'adrotate-pro'); ?>" />&nbsp;&nbsp;&nbsp;<em><?php _e('Please click only once, this may take a few seconds!', 'adrotate-pro'); ?></em>
							</p>						

							<p><strong><?php _e('When you submit your campaign the following data will be submitted:', 'adrotate-pro'); ?></strong><br/>
							<em><small><?php _e('Your campaign settings as entered above, website url, some basic WordPress information, your unique identifier code along with your contact information.', 'adrotate-pro'); ?> <?php _e('Other AdRotate users will <u>NOT</u> see identifiable information and only get to see the campaign specifications. However, it should go without saying that since your website url is shared, other AdRotate Swap users may be able to figure out who you are. Always be mindful of what you put on your website!', 'adrotate-pro'); ?></small></em></p>
						</form>
						
					<?php } else if($s['status'] == 1 AND $s['campaign']['status'] > 0) { ?>
					
						<table width="100%">
							<tr>
								<td width="33%">
									<p><center><strong><?php _e('Status', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">active</span></center></p>
									<p><center><strong><?php _e('Clicks', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">412</span></center></p>
								</td>
								<td width="33%">
									<p><center><strong><?php _e('Active on sites', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">21</span></center></p>
									<p><center><strong><?php _e('Impressions', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">76153</span></center></p>
								</td>
								<td>
									<p><center><strong><?php _e('Expires', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">5 Aug, 2020</span></center></p>
									<p><center><strong><?php _e('Click-through-Rate', 'analytics-spam-blocker'); ?></strong><br /><span class="content_large">0,6%</span></center></p>
								</td>
						</table>
	
						<p>Under review based on reports?</p>
						<p>Controls: Cancel, extend</p>
						<p>Response: Form locked with current campaign until it expires.</p>
						<p>Campaign expires: If not picked anywhere after 1 week, if end-date passed -> Form unlocks as blank.</p>
	
						<p>Campaign selection: Offer up-to 20 campaigns, user can pick or report them. Advert goes life with schedule if picked.</p>
						<p>Campaign selection: Select 2 or 3 categories, select language (optional), select region (optional).</p>
	
						<p>Submit campaign: Form fields, website url, website language, instance, creation date.</p>
						<p>Response: Campaign created date.</p>
	
						<p>Periodic check: Check if own campaign active, ask for stats, ask for status, send active campaigns from 3rd party + sum of stats, ask for 20 new campaigns to choose from.</p>
						<p>Response: Campaign status, stats, confirm own campaign remains active if 3rd party campaigns are active. Disable/remove 3rd party campaigns from sites with no active campaign of themselfes. 20 new campaigns datas if available and own campaign is submitted.</p>
			
					<?php } else { ?>

						<p><img src="<?php echo plugins_url('../images/icon-swap.png', dirname(__FILE__)); ?>" class="alignleft pro-image" /><strong><?php _e('Join AdRotate Swap and promote your website on participating websites.', 'adrotate-pro'); ?></strong><br /><?php _e('Create a campaign and send your banner promoting your website to AdRotate Swap so that other AdRotate and AdRotate Pro users can rotate it on their website.', 'adrotate-pro'); ?></a></p>
						
						<p><?php _e('Once you have joined AdRotate Swap this form will be replaced with the campaign form where you can set up your campaign and preferences. When the campaign has been approved and is live this section will show some stats and information for your campaign.', 'adrotate-pro'); ?></p>


						<form name="request" id="post" method="post" action="admin.php?page=adrotate-swap">
							<?php wp_nonce_field('ajdg_nonce_swap','ajdg_nonce_swap'); ?>
						
							<p class="submit">
								<input tabindex="10" type="submit" name="adrotate_swap_register" class="button-primary" value="<?php _e('Join AdRotate Swap!', 'adrotate-pro'); ?>" />&nbsp;&nbsp;&nbsp;<em><?php _e('Please click only once, registration may take a few seconds!', 'adrotate-pro'); ?></em>
							</p>						

							<p><strong><?php _e('When you sign up for AdRotate Swap the following data will be submitted:', 'adrotate-pro'); ?></strong><br/>
							<em><small><?php _e('A unique identifier code, your servers IP address, and the AdRotate plugin version.', 'adrotate-pro'); ?> <?php _e('This information will be updated automatically as needed over time as you use AdRotate Swap and is <u>NOT</u> shared with other users.', 'adrotate-pro'); ?></small></em></p>
						</form>

					<?php }	?>

				</div>
			</div>

		</div>
		<div id="right-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title">Important information</h2>
				<div id="services" class="ajdg-postbox-content">
					<p><strong>What is AdRotate Swap</strong><br />AdRotate Swap is an easy way to promote your website for free on websites that use AdRotate or AdRotate Pro. Every AdRotate and AdRotate Pro user can participate.</p>
					<p><strong>How AdRotate Swap works</strong><br />On this page you'll create your campaign for your website with a few specific settings such as which banner image to use and how long the campaign should run. Your campaign is then submitted to AdRotate Swap. Other AdRotate users will send in their campaigns as well. Every publisher receives a selection of available campaigns to pick from and publish them on their website. Campaigns sent in from AdRotate Pro list higher than others.</p>
					<p>Your campaign may end up on dozens of websites and you can choose campaigns to show on your website. The goal of your campaign is simple; drive traffic to your website. All publishers can choose which campaigns they want to show. Everyone is encouraged to add these campaigns to their regular ad spots so visitors on those websites can easily find your website.</p>
					<p><strong>Why you should participate</strong><br />The more people participate the better this works. Free promotion is never bad. More traffic is almost always good. If you're selling something, you may get more sales. If you rely on advertising revenue, you'll likely have more clicks and thus more revenue. But it also helps with your linkbuilding which may benefit your ranking in search. It's win-win really.</p>
					<p><strong>The goal of your campaign</strong><br />The campaign is to promote your website and get more people to visit your website. The banner you create should reflect that. Make sure your front page is attractive and easy to navigate so visitors do not immediately leave.</p>
					<p><strong>What kind of banner should I use?</strong><br />Stick to a basic image banner. Wallpapers, popups, popunders and other such advanced banners are not supported! The banner you create should look attractive and be of high quality to fit an exact size. Banners should not be intrusive, offensive or rude.</p>
					<p>Use a common banner size such as 468x60, 728x90, 250x250, 120x600, 300x300 or something similar. Using a decent dpi setting is equally important, 72ppi is the low-end but is widely used, 100ppi is a good in-between and 144ppi is good for hi-res and retina displays. Once done, compress your image to be smaller than ~100-120KB. And please, avoid using blinking, flashy and moving images where possible.</p>
					<p><strong>Free but not free</strong><br />While AdRotate Swap is meant to be fun, useful and gives you a lot of freedom, it is not meant to be abused. Campaigns are to promote your website and generate traffic to it. Any campaign that does not have this simple goal or tries to run a poorly designed advert can be reported and deleted at any time. Every publisher can report any campaign before picking it. Certain campaigns may require review before being approved. Please use common sense and be nice, you are getting free promotion after all. Do not abuse that privilege.</p>
				</div>
			</div>

		</div>
	</div>
</div>