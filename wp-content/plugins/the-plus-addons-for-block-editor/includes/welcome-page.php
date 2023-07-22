<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
echo '<div class="tpgb-panel-welcome-page">';
	echo '<div class="tpgb-panel-row">';
		echo '<div class="tpgb-panel-col tpgb-panel-col-35">';
			/*Welcome User Info*/
			echo '<div class="tpgb-welcome-user-info tpgb-p-20 tpgb-mb-8">';
				echo '<div class="tpgb-user-info">';
					$user = wp_get_current_user();
					if ( $user ){
						echo '<img src="'.esc_url( get_avatar_url( $user->ID ) ).'" class="tpgb-avatar-img" />';
					}
					echo '<div class="tpgb-welcom-author-name">'.esc_html__('Welcome, ','tpgb').esc_html($user->display_name).'</div>';
				echo '</div>';
				echo '<div class="tpgb-sec-subtitle tpgb-text-white tpgb-mt-8">'.esc_html__('Let\'s build most amazing gutenberg websites using blocks of The Plus Addons for Gutenberg.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border tpgb-bg-white"></div>';
				echo '<ul class="tpgb-panel-list">';
					echo '<li>'.esc_html__("35+ Free Blocks",'tpgb').'</li>';
					echo '<li>'.esc_html__("15+ Freemium Blocks",'tpgb').'</li>';
					echo '<li>'.esc_html__("30+ Pro Blocks",'tpgb').'</li>';
					echo '<li>'.esc_html__("10+ Special Features",'tpgb').'</li>';
					echo '<li>'.esc_html__("5+ UI Templates",'tpgb').'</li>';
					echo '<li>'.esc_html__("300+ UI Design Blocks",'tpgb').'</li>';
				echo '</ul>';
				echo '<div class="tpgb-mt-8">'.esc_html__("And It's just a start. Stay Tuned.",'tpgb').'</div>';
				echo '<a href="'.esc_url('https://theplusblocks.com/free-vs-pro/').'" class="tpgb-panel-btn-outline tpgb-text-white" title="'.esc_attr__('Free Vs Pro','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Free Vs Pro','tpgb').'</a>';
				echo '<small class="tpgb-notice-text tpgb-mt-8">'.esc_html__('* Free is Better but Pro is the best. Try it with 30 days refund policy.','tpgb').'</small>';
			echo '</div>';
			/*Welcome User Info*/
			/*Welcome Document*/
			echo '<div class="tpgb-panel-sec tpgb-welcome-doc tpgb-p-20 tpgb-mt-8 tpgb-mb-8">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Documentation','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('We wrote every details for you.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('Looking forward to knowing more about each widget & features we provide? That is the best way to learn before you start implementation or to find a solution of any issue you are having in the process. We have documented all possible points there.','tpgb').'</div>';
				echo '<a href="'.esc_url('https://docs.posimyth.com/tpag/').'" class="tpgb-panel-btn" title="'.esc_attr__('Read Documentation','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Read Documentation','tpgb').'</a>';
			echo '</div>';
			/*Welcome Document*/
			/*Welcome System Requirement*/
			$check_right_req = '<svg xmlns="http://www.w3.org/2000/svg" width="23.532" height="20.533" viewBox="0 0 23.532 20.533">
				  <path d="M6.9,15.626,0,8.73,2.228,6.5,6.9,11.064,17.729,0,20,2.388Z" transform="translate(4.307) rotate(16)"/>
				</svg>';
			$check_wrong_req = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="19.633" viewBox="0 0 20 19.633">
			  <g transform="translate(-102.726 -8.677)">
				<path id="Path_7597" data-name="Path 7597" d="M0,0,3.551.047,3.864,23.9.313,23.854Z" transform="translate(102.726 11.41) rotate(-45)"/>
				<path id="Path_7598" data-name="Path 7598" d="M0,0,23.854.313,23.9,3.864.047,3.551Z" transform="translate(103.093 25.578) rotate(-45)"/>
			  </g>
			</svg>';
			
			echo '<div class="tpgb-panel-sec tpgb-welcome-sys-req tpgb-p-20 tpgb-mt-8">';
				echo '<div class="tpgb-sec-title">'.esc_html__('System Requirement','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Configuration needed to work smoothly.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				
				$php_check_req ='';
				if (version_compare(phpversion(), '7.1', '>')) {
					$php_check_req = '<span class="check-req-right">'.$check_right_req.'</span>';
				}else{
					$php_check_req = '<span class="check-req-wrong">'.$check_wrong_req.'</span>';
				}
				echo '<div class="sys-req-label"><span>'.esc_html__('PHP Version : ','tpgb').phpversion().esc_html__(' Check','tpgb').'</span>'.$php_check_req.'</div>';
				
				$memory_check_req ='';
				$memory_limit = ini_get('memory_limit');
				if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
					if ($matches[2] == 'M') {
						$memory_limit = $matches[1] * 1024 * 1024;
					} else if ($matches[2] == 'K') {
						$memory_limit = $matches[1] * 1024;
					}
				}
				
				if ($memory_limit >= 256 * 1024 * 1024) {
					$memory_check_req = '<span class="check-req-right">'.$check_right_req.'</span>';
				}else{
					$memory_check_req = '<span class="check-req-wrong">'.$check_wrong_req.'</span>';
				}
				echo '<div class="sys-req-label"><span>'.esc_html__('Memory Limit : ','tpgb'). ini_get('memory_limit').'</br>'.esc_html__('(Required 256M)','tpgb').'</span>'.$memory_check_req.'</div>';
				
				$gzip_check_req = '';
				if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
					$gzip_check_req = '<span class="check-req-right">'.$check_right_req.'</span>';
				}else{
					$gzip_check_req = '<span class="check-req-wrong">'.$check_wrong_req.'</span>';
				}
				echo '<div class="sys-req-label tpgb-bm-0"><span>'.esc_html__('GZIp Enabled :','tpgb').'</span>'.$gzip_check_req.'</div>';
				/*echo '<a href="#" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('Know More & Resolve','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Know More & Resolve?','tpgb').'</a>';*/
			
			echo '</div>';
			/*Welcome System Requirement*/
		echo '</div>';
		echo '<div class="tpgb-panel-col tpgb-panel-col-65">';
			/*Welcome Change log*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-welcome-changelog tpgb-mb-8">';
				echo '<div class="tpgb-sec-title">'.esc_html__('What’s New?','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Stay tuned at this place as We are working day and night for new blocks, features, design layouts, bug fixes and compatibility with WordPress Eco System.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';

				echo '<div class="tpgb-changelog-list">';
					echo '<div class="changelog-date">'.esc_html__('18 July 2023','tpgb').' <span class="changelog-version">'.esc_html__('3.0.4','tpgb').'</span></div>';
					echo '<ul class="changelog-list">';
						echo '<li>'.esc_html__('Fix : Block Context Error Bug Fix','tpgb').'</li>';
					echo '</ul>';
				echo '</div>';

				echo '<div class="tpgb-changelog-list">';
					echo '<div class="changelog-date">'.esc_html__('17 July 2023','tpgb').' <span class="changelog-version">'.esc_html__('3.0.3','tpgb').'</span></div>';
					echo '<ul class="changelog-list">';
						echo '<li>'.esc_html__('Added : Info Box : Carousel Option','tpgb').'</li>';
						echo '<li>'.esc_html__('Added : Global Options : Gradient Color Field','tpgb').'</li>';
						echo '<li>'.esc_html__('Added : Global Options : Disable Global options Completely - No Assets will be loaded on Frontend - #7790','tpgb').'</li>';
						echo '<li>'.esc_html__('Compatibility : MemberPress Courses : Courses and Lessons Page Compatibility','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Search Bar : On First load Results Visibility Bug','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : FSE Theme Editor : All Plus Blocks Advanced Tab : Custom CSS Field Bug','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Carousel Option : Arrow Responsive Bug','tpgb').'</li>';
					echo '</ul>';
				echo '</div>';

				echo '<div class="tpgb-changelog-list">';
					echo '<div class="changelog-date">'.esc_html__('04 July 2023','tpgb').' <span class="changelog-version">'.esc_html__('3.0.2','tpgb').'</span></div>';
					echo '<ul class="changelog-list">';
						echo '<li>'.esc_html__('Added : Container : New Property Align Item normal & Justify content Space Evenly Options add.','tpgb').'</li>';
						echo '<li>'.esc_html__('Added : Container : Add Reverse Column Options with Responsive.','tpgb').'</li>';
						echo '<li>'.esc_html__('Added : Info box : Add Padding Option for Title and Description','tpgb').'</li>';
						echo '<li>'.esc_html__('Added : Social Icons : Add Padding Option for Style-1, Style-2 and Style-14','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Container : Equal Column Bug Fix in Backend','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Post Meta : Dynamic Field Bug Fix','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Social Feed : Post URL Bug Fix','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : Core Blocks : RSS, Archives, Latest Comments, Tag Cloud Bug Fix','tpgb').'</li>';
						echo '<li>'.esc_html__('Fix : More Improvement and Bug Fix','tpgb').'</li>';
					echo '</ul>';
				echo '</div>';

				echo '<a href="'.esc_url('https://roadmap.theplusblocks.com/updates').'" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('change log','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Full Change log','tpgb').'</a>';
			echo '</div>';
			/*Welcome Change log*/
			/*Welcome FAQ*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-welcome-faq tpgb-mt-8">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Frequently Asked Questions','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('You might have some, We have tried to answer them all.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-faq-section">';
					echo '<div class="faq-title"><span>'.esc_html__('How to get Help/Support?','tpgb').'</span><span class="faq-icon-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="9.4" height="6.1" viewBox="0 0 9.4 6.1"><path d="M6.7,8.1,2,3.4,3.4,2,6.7,5.3,10,2l1.4,1.4Z" transform="translate(11.4 8.1) rotate(180)"/></svg></span></div>';
					echo '<div class="faq-content">'.sprintf( __( 'You can have a look at <a href="%1$s" target="_blank" rel="noopener noreferrer" class="panel-sec-color">our documentation</a> get <a href="%2$s" target="_blank" class="panel-sec-color" rel="noopener noreferrer">Free support</a> using WordPress.org, and/or Join our <a href="%3$s" target="_blank" class="panel-sec-color" rel="noopener noreferrer">Facebook channel</a> to get help from community.', 'tpgb' ), 'https://docs.posimyth.com/tpag/','https://wordpress.org/support/plugin/the-plus-addons-for-block-editor/','https://www.facebook.com/groups/theplus4gutenberg/' ).'</div>';
				echo '</div>';
				echo '<div class="tpgb-faq-section">';
					echo '<div class="faq-title"><span>'.esc_html__('What is this Performance option about?','tpgb').'</span><span class="faq-icon-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="9.4" height="6.1" viewBox="0 0 9.4 6.1"><path d="M6.7,8.1,2,3.4,3.4,2,6.7,5.3,10,2l1.4,1.4Z" transform="translate(11.4 8.1) rotate(180)"/></svg></span></div>';
					echo '<div class="faq-content">'.esc_html__('First of all, Performance is our highest priority. We have setup caching architecture in which, It generates One CSS & One JS files for each page. Those files combine & minify code of blocks used on those pages in single file. That means, If you have Just one block from The Plus Addons on your page, It will make one CSS file and One JS file which will have that block’s assets. So, It will make your plugin 100% modular with highest possible performance.','tpgb').'</div>';
				echo '</div>';
				echo '<div class="tpgb-faq-section">';
					echo '<div class="faq-title"><span>'.esc_html__('When and Why Need to remove cache?','tpgb').'</span><span class="faq-icon-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="9.4" height="6.1" viewBox="0 0 9.4 6.1"><path d="M6.7,8.1,2,3.4,3.4,2,6.7,5.3,10,2l1.4,1.4Z" transform="translate(11.4 8.1) rotate(180)"/></svg></span></div>';
					echo '<div class="faq-content">'.esc_html__('When you make any updated in your page and If that is not reflecting properly in frontend, You need to remove cache. When you click on Purge All Cache, It will remove all those individual files and It will start creating those files when you visit that page for first time.','tpgb').'</div>';
				echo '</div>';
				echo '<div class="tpgb-faq-section">';
					echo '<div class="faq-title"><span>'.esc_html__('What If I want to remove cache of only one page?','tpgb').'</span><span class="faq-icon-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="9.4" height="6.1" viewBox="0 0 9.4 6.1"><path d="M6.7,8.1,2,3.4,3.4,2,6.7,5.3,10,2l1.4,1.4Z" transform="translate(11.4 8.1) rotate(180)"/></svg></span></div>';
					echo '<div class="faq-content">'.esc_html__('You can do that from admin bar of page editor. That is available on top for all pages you check from front end. You will see option “The Plus Performance” -> “Purge Current Page”.','tpgb').'</div>';
				echo '</div>';
				echo '<div class="tpgb-faq-section">';
					echo '<div class="faq-title"><span>'.esc_html__('Site is not working well even after removing cache at frontend.','tpgb').'</span><span class="faq-icon-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="9.4" height="6.1" viewBox="0 0 9.4 6.1"><path d="M6.7,8.1,2,3.4,3.4,2,6.7,5.3,10,2l1.4,1.4Z" transform="translate(11.4 8.1) rotate(180)"/></svg></span></div>';
					echo '<div class="faq-content">'.esc_html__('If your website is not working well even after removing cache from above button. You need to check your 3rd party caching plugin and remove cache from there. After that try to remove your browser cache by pressing Hard Reload and/or try on incognito mode.','tpgb').'</div>';
				echo '</div>';
				echo '<a href="#" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('More FAQs','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('More FAQs','tpgb').'</a>';
			echo '</div>';
			/*Welcome FAQ*/
		echo '</div>';
	echo '</div>';
	/*Video Tutorial*/
	/*echo '<div class="tpgb-panel-row tpgb-mt-50">';
		echo '<div class="tpgb-panel-col tpgb-panel-col-100">';
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-welcome-video">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Video Tutorials','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Checkout Few of our latest video tutorials','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				
				echo '<div class="tpgb-panel-row tpgb-panel-relative">';
					echo '<a href="#" class="tpgb-more-video" target="_blank" rel="noopener noreferrer">'.esc_html__("Our Full Playlist",'tpgb').'</a>';
					echo '<div class="tpgb-panel-col tpgb-panel-col-25">';
						echo '<a href="#" class="tpgb-panel-video-list" target="_blank" rel="noopener noreferrer">';
							echo '<img src="'.esc_url(TPGB_URL.'/assets/images/video-tutorial/video-1.jpg').'" />';
						echo '</a>';
					echo '</div>';
					echo '<div class="tpgb-panel-col tpgb-panel-col-25">';
						echo '<a href="#" class="tpgb-panel-video-list" target="_blank" rel="noopener noreferrer">';
							echo '<img src="'.esc_url(TPGB_URL.'/assets/images/video-tutorial/video-1.jpg').'" />';
						echo '</a>';
					echo '</div>';
					echo '<div class="tpgb-panel-col tpgb-panel-col-25">';
						echo '<a href="#" class="tpgb-panel-video-list" target="_blank" rel="noopener noreferrer">';
							echo '<img src="'.esc_url(TPGB_URL.'/assets/images/video-tutorial/video-1.jpg').'" />';
						echo '</a>';
					echo '</div>';
					echo '<div class="tpgb-panel-col tpgb-panel-col-25">';
						echo '<a href="#" class="tpgb-panel-video-list" target="_blank" rel="noopener noreferrer">';
							echo '<img src="'.esc_url(TPGB_URL.'/assets/images/video-tutorial/video-1.jpg').'" />';
						echo '</a>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';*/
	/*Video Tutorial*/
	/*Welcome Bottom Section*/
	echo '<div class="tpgb-panel-row tpgb-mt-8 tp-mt-8-remove">';
		echo '<div class="tpgb-panel-col tpgb-panel-col-50" style="align-content: flex-start;">';
			/*technical support*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-mb-8 tpgb-welcome-tech-sup">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Technical Support','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Let’s find a solution for your question.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('Tried everything but not found a solution? Our support team is always there for your backup. Just a few quick details to ','tpgb').'<a href="'.esc_url('https://docs.posimyth.com/tpag/steps-to-follow-before-submitting-a-support-ticket/').'" target="_blank" rel="noopener noreferrer" class="panel-sec-color">'.esc_html__('check at here before submitting ticket.','tpgb').'</a>'.esc_html__(' You may read our ','tpgb').'<a href="#" target="_blank" rel="noopener noreferrer" class="panel-sec-color">'.esc_html__('Support Policy','tpgb').'</a>'. esc_html__(' to find out, Which things are covered.','tpgb').'</div>';
				
				echo '<div class="support-point tpgb-mt-8"><span><svg id="Support" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25"><path d="M24.167,9.167h-2.5V7.38C21.667,3.966,17.779,0,12.5,0S3.333,3.968,3.333,7.38V9.167H.833C0,9.167,0,9.712,0,10v8.333c0,.288,0,.833.833.833h2.5c.833,0,.833-.545.833-.833V7.381c0-2.871,3.717-6.339,8.333-6.339s8.333,3.467,8.333,6.339V20.9c0,1.113-.9,2.435-2.017,2.435H17.5V22.5c0-.288,0-.833-.833-.833H12.973a1.635,1.635,0,0,0-1.513,1.717A1.607,1.607,0,0,0,12.973,25h3.693c.833,0,.833-.544.833-.833h1.317a3.079,3.079,0,0,0,2.85-3.268V19.167h2.5c.833,0,.833-.545.833-.833V10C25,9.711,25,9.167,24.167,9.167Z" fill="#666"/><path d="M41.209,17.68V16.04c0-3.127-3.992-5.672-8.6-5.672S24,12.92,24,16.057v1.437c0,2.978,3.752,7.4,8.6,7.4,4.5,0,8.6-3.966,8.6-7.212Z" transform="translate(-20.104 -8.685)" fill="#666"/></svg></span><span>'.esc_html__('Support Time : Mon-Fri | 9 AM to 6 PM (Time Zone :UTC+5:30)','tpgb').'</span></div>';
				
				echo '<div class="support-point tpgb-mb-8"><span><svg xmlns="http://www.w3.org/2000/svg" width="25" height="24.936" viewBox="0 0 25 24.936"><path d="M19.617,0a5.141,5.141,0,0,0-4.185,2.111,1.038,1.038,0,0,0,.5,1.6,11.444,11.444,0,0,1,2.8,1.406L17.809,6.35a9.853,9.853,0,0,0-10.993,0L5.889,5.117A11.371,11.371,0,0,1,8.735,3.694a1.041,1.041,0,0,0,.506-1.608A5.149,5.149,0,0,0,5.071,0,5.218,5.218,0,0,0,1,8.5a1.04,1.04,0,0,0,.808.385c.019,0,.039,0,.058,0a1.034,1.034,0,0,0,.817-.477A11.541,11.541,0,0,1,5.061,5.743l.926,1.235a9.832,9.832,0,0,0-1.44,13.64L2.018,24.111a.52.52,0,0,0,.119.726.514.514,0,0,0,.3.1.519.519,0,0,0,.421-.216l2.385-3.3a9.834,9.834,0,0,0,14.13,0l2.385,3.3a.519.519,0,1,0,.841-.608l-2.526-3.494a9.832,9.832,0,0,0-1.44-13.64l.925-1.235a11.584,11.584,0,0,1,2.4,2.709,1.039,1.039,0,0,0,.812.48l.065,0a1.037,1.037,0,0,0,.8-.375A5.224,5.224,0,0,0,19.617,0ZM17.874,9.718l-5.2,5.195a.515.515,0,0,1-.734,0L8.828,11.8a.52.52,0,1,1,.735-.735l2.75,2.75L17.14,8.984a.519.519,0,1,1,.733.735Z" transform="translate(0.156 0)" fill="#666"/></svg></span><span>'.esc_html__('Average Response Time : 24 Hours (Weekdays)','tpgb').'</span></div>';
				
				echo '<a href="'.esc_url('http://m.me/tpagutenberg/').'" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('Live Chat','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Live Chat','tpgb').'</a>';
				echo '<a href="'.esc_url('https://wordpress.org/support/plugin/the-plus-addons-for-block-editor/').'" class="tpgb-panel-btn-outline-2 tpgb-mt-8" title="'.esc_attr__('Free Support','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Free Support','tpgb').'</a>';
			echo '</div>';
			/*technical support*/
			/*Social*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-mt-8 tpgb-welcome-social">';
				echo '<div class="tpgb-sec-title">'.esc_html__('We are social','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Join us to get regular Social Updates.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('Get to know about plugin updates, tips & tricks, New Offers and lots more from our social media accounts.','tpgb').'</div>';
				echo '<a href="'.esc_url('https://www.facebook.com/tpagutenberg/').'" class="tpgb-panel-social tpgb-mt-8" title="'.esc_attr__('facebook','tpgb').'" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="8.356" height="16" viewBox="0 0 8.356 16"><path d="M85.422,16V8.711h2.489l.356-2.844H85.422V4.089c0-.8.267-1.422,1.422-1.422h1.511V.089C88,.089,87.111,0,86.133,0a3.431,3.431,0,0,0-3.644,3.733V5.867H80V8.711h2.489V16Z" transform="translate(-80)" fill="#6f14f1" fill-rule="evenodd"/></svg></a>';
				echo '<a href="'.esc_url('https://www.instagram.com/tpagutenberg/').'" class="tpgb-panel-social tpgb-mt-8" title="'.esc_attr__('instagram','tpgb').'" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M10,1.778a30.662,30.662,0,0,1,4,.111,5.154,5.154,0,0,1,1.889.333,3.9,3.9,0,0,1,1.889,1.889A5.154,5.154,0,0,1,18.111,6c0,1,.111,1.333.111,4a30.662,30.662,0,0,1-.111,4,5.154,5.154,0,0,1-.333,1.889,3.9,3.9,0,0,1-1.889,1.889A5.154,5.154,0,0,1,14,18.111c-1,0-1.333.111-4,.111a30.662,30.662,0,0,1-4-.111,5.154,5.154,0,0,1-1.889-.333,3.9,3.9,0,0,1-1.889-1.889A5.154,5.154,0,0,1,1.889,14c0-1-.111-1.333-.111-4a30.662,30.662,0,0,1,.111-4,5.154,5.154,0,0,1,.333-1.889A3.991,3.991,0,0,1,3,3a1.879,1.879,0,0,1,1.111-.778A5.154,5.154,0,0,1,6,1.889a30.662,30.662,0,0,1,4-.111M10,0A32.83,32.83,0,0,0,5.889.111,6.86,6.86,0,0,0,3.444.556,4.35,4.35,0,0,0,1.667,1.667,4.35,4.35,0,0,0,.556,3.444,5.063,5.063,0,0,0,.111,5.889,32.83,32.83,0,0,0,0,10a32.83,32.83,0,0,0,.111,4.111,6.86,6.86,0,0,0,.444,2.444,4.35,4.35,0,0,0,1.111,1.778,4.35,4.35,0,0,0,1.778,1.111,6.86,6.86,0,0,0,2.444.444A32.83,32.83,0,0,0,10,20a32.83,32.83,0,0,0,4.111-.111,6.86,6.86,0,0,0,2.444-.444,4.662,4.662,0,0,0,2.889-2.889,6.86,6.86,0,0,0,.444-2.444C19.889,13,20,12.667,20,10a32.83,32.83,0,0,0-.111-4.111,6.86,6.86,0,0,0-.444-2.444,4.35,4.35,0,0,0-1.111-1.778A4.35,4.35,0,0,0,16.556.556,6.86,6.86,0,0,0,14.111.111,32.83,32.83,0,0,0,10,0m0,4.889A5.029,5.029,0,0,0,4.889,10,5.111,5.111,0,1,0,10,4.889m0,8.444A3.274,3.274,0,0,1,6.667,10,3.274,3.274,0,0,1,10,6.667,3.274,3.274,0,0,1,13.333,10,3.274,3.274,0,0,1,10,13.333m5.333-9.889a1.222,1.222,0,1,0,1.222,1.222,1.233,1.233,0,0,0-1.222-1.222" fill="#6f14f1" fill-rule="evenodd"/></svg></a>';
				echo '<a href="'.esc_url('https://www.youtube.com/channel/UCPChp9hLnrKX9FY1ZzFCyJw').'" class="tpgb-panel-social tpgb-mt-8" title="'.esc_attr__('youtube','tpgb').'" target="_blank" rel="noopener noreferrer"><svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="youtube" class="svg-inline--fa fa-youtube fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" width="23" viewBox="0 0 576 512"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" fill="#6f14f1"></path></svg></a>';
				echo '<a href="'.esc_url('https://www.facebook.com/groups/theplus4gutenberg/').'" class="tpgb-panel-social tpgb-mt-8" title="'.esc_attr__('Facebook Community','tpgb').'" target="_blank" rel="noopener noreferrer"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users" class="svg-inline--fa fa-users fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" width="23" viewBox="0 0 640 512"><path d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" fill="#6f14f1" fill-rule="evenodd"></path></svg></a>';
			echo '</div>';
			/*Social*/
		echo '</div>';
		echo '<div class="tpgb-panel-col tpgb-panel-col-50">';
			/*Rate Us*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-mb-8 tpgb-welcome-rate-us">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Rate Us','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Maximum Rating motivate us to do more and better.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('Sharing your kind words about us and our widget means a lot to us. You can share your review at below.','tpgb').'</div>';
				echo '<a href="'.esc_url('https://wordpress.org/plugins/the-plus-addons-for-block-editor/#reviews').'" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('Rate on WordPress','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Rate on WordPress','tpgb').'</a>';
				echo '<a href="'.esc_url('https://www.facebook.com/tpagutenberg/reviews/').'" class="tpgb-panel-btn-outline-2 tpgb-mt-8" title="'.esc_attr__('Rate on Facebook','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Rate on Facebook','tpgb').'</a>';
			echo '</div>';
			/*Rate Us*/
			/*feedback*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-mt-8 tpgb-mb-8 tpgb-welcome-feedback">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Any Feedback or Suggestions?','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('We love constructive views of yours.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('We always believe in improving and growth. Your all kind of suggestions for new blocks, Features, Improvements, Customer Service Suggestions and anything else are most welcome. We do appreciate your time.','tpgb').'</div>';
				echo '<a href="'.esc_url('http://m.me/tpagutenberg/').'" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('Share Feedback','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Share Feedback','tpgb').'</a>';
			echo '</div>';
			/*feedback*/
			/*subscriber*/
			echo '<div class="tpgb-panel-sec tpgb-p-20 tpgb-mt-8 tpgb-welcome-subscriber">';
				echo '<div class="tpgb-sec-title">'.esc_html__('Join 14,573 Subscribers','tpgb').'</div>';
				echo '<div class="tpgb-sec-subtitle">'.esc_html__('Get latest updates, Offers and more on your email.','tpgb').'</div>';
				echo '<div class="tpgb-sec-border"></div>';
				echo '<div class="tpgb-sec-desc">'.esc_html__('Want to join our newsletter? We share tricks & tips related to Product and WordPress itself. On top of that, You will get timely notifications of new plugin updates and lots more.','tpgb').'</div>';
				echo '<a href="https://theplusblocks.com/#ft-subscribe" class="tpgb-panel-btn tpgb-mt-8" title="'.esc_attr__('Subscribe Us','tpgb').'" target="_blank" rel="noopener noreferrer">'.esc_html__('Subscribe Us','tpgb').'</a>';
			echo '</div>';
			/*subscriber*/
		echo '</div>';
	echo '</div>';
	/*Welcome Bottom Section*/
echo '</div>';
?>