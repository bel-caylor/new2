<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_export_stats
 Purpose:   Export CSV data of given month
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_export_stats() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_ads') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_groups')
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_global')) {
		$id = $type = $start_date = $end_date = $adstats = $csv_emails = '';
		if(isset($_POST['adrotate_export_id'])) $id	= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_id'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_export_type'])) $type = strip_tags(htmlspecialchars(trim($_POST['adrotate_export_type'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_start_date'])) $start_date = strip_tags(trim($_POST['adrotate_start_date'], "\t\n "));
		if(isset($_POST['adrotate_end_date'])) $end_date = strip_tags(trim($_POST['adrotate_end_date'], "\t\n "));
		if(isset($_POST['adrotate_export_format'])) $format = trim($_POST['adrotate_export_format']);
		if(isset($_POST['adrotate_export_addresses'])) $csv_emails = trim($_POST['adrotate_export_addresses']);


		// Sort out start dates
		if(strlen($start_date) > 0) {
			$from_name = $start_date;
			list($start_day, $start_month, $start_year) = explode('-', $start_date);
			$start_date = mktime(0, 0, 0, $start_month, $start_day, $start_year);
		} else {
			$from_name = 'invalid';
			$start_date = 0;
		}

		// Sort out end dates
		if(strlen($end_date) > 0) {
			$until_name = $end_date;
			list($end_day, $end_month, $end_year) = explode('-', $end_date);
			$end_date = mktime(23, 59, 0, $end_month, $end_day, $end_year);
		} else {
			$until_name = 'invalid';
			$end_date = 0;
		}

		// Enddate is too early, reset
		if($end_date <= $start_date) $end_date = $start_date + (30 * DAY_IN_SECONDS); // 30 days

		// Format
		if($format != 'default' AND $format != 'individual') $format = 'default';

		// Email addresses/delivery
		if(strlen($csv_emails) > 0) {
			$csv_emails = explode(',', trim($csv_emails));
			foreach($csv_emails as $csv_email) {
				$csv_email = strip_tags(htmlspecialchars(trim($csv_email), ENT_QUOTES));
				if(strlen($csv_email) > 0) {
					if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $csv_email) ) {
						$clean_advertiser_email[] = $csv_email;
					}
				}
			}
			$emails = array_unique(array_slice($clean_advertiser_email, 0, 3));
			$emailcount = count($emails);
		} else {
			$emails = array();
			$emailcount = 0;
		}


		$adstats = array(); // Store the result
		$generated = array("Generated on ".date_i18n("M d Y, H:i"));

		if($type == "single" OR $type == "group" OR $type == "global") {
			if($type == "single") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}' AND `ad` = %d GROUP BY `thetime` ORDER BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id));

				$filename = "AdRotate_advert_ID_".$id."_".$from_name."_".$until_name.".csv";
				$topic = array("Report for ad '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Impressions", "Clicks");
			}

			if($type == "group") {
				$title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = %d;", $id));
				$filename = "AdRotate_group_ID_".$id."_".$from_name."_".$until_name.".csv";
				$topic = array("Report for group '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);

				if($format == 'individual') {
					$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad`, `title`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate` WHERE `ad` = `{$wpdb->prefix}adrotate`.`id` AND `{$wpdb->prefix}adrotate_stats`.`thetime` >= '{$start_date}' AND `{$wpdb->prefix}adrotate_stats`.`thetime` <= '{$end_date}' AND `group` = %d GROUP BY `ad` ORDER BY `ad` ASC;", $id), ARRAY_A);

					$keys = array("Advert", "Advert ID", "Impressions", "Clicks");
				} else {
					$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}' AND  `group` = %d GROUP BY `thetime` ORDER BY `thetime` ASC;", $id), ARRAY_A);

					$keys = array("Day", "Impressions", "Clicks");
				}
			}

			if($type == "global") {
				$filename = "AdRotate_stats_".$from_name."_".$until_name.".csv";
				$topic = array("Global report");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);

				if($format == 'individual') {
					$ads = $wpdb->get_results("SELECT `ad`, `title`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate` WHERE `ad` = `{$wpdb->prefix}adrotate`.`id` AND `{$wpdb->prefix}adrotate_stats`.`thetime` >= '{$start_date}' AND `{$wpdb->prefix}adrotate_stats`.`thetime` <= '{$end_date}' GROUP BY `ad` ORDER BY `ad` ASC;", ARRAY_A);

					$keys = array("Advert", "Advert ID", "Impressions", "Clicks");
				} else {
					$ads = $wpdb->get_results("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}' GROUP BY `thetime` ORDER BY `thetime` ASC;", ARRAY_A);

					$keys = array("Day", "Impressions", "Clicks");
				}
			}


			if($format == 'individual') {
				$x = 0;
				foreach($ads as $ad) {
					// Prevent gaps in display
					if(empty($ad['impressions'])) $ad['impressions'] = 0;
					if(empty($ad['clicks'])) $ad['clicks'] = 0;

					// Build array
					$adstats[$x]['name'] = stripslashes(htmlspecialchars_decode($ad['title']));
					$adstats[$x]['id'] = $ad['ad'];
					$adstats[$x]['impressions'] = $ad['impressions'];
					$adstats[$x]['clicks'] = $ad['clicks'];
					$x++;
				}
			} else {
				$x = 0;
				foreach($ads as $ad) {
					// Prevent gaps in display
					if(empty($ad['impressions'])) $ad['impressions'] = 0;
					if(empty($ad['clicks'])) $ad['clicks'] = 0;

					// Build array
					$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
					$adstats[$x]['impressions'] = $ad['impressions'];
					$adstats[$x]['clicks'] = $ad['clicks'];
					$x++;
				}
			}
		}

		if($type == "advertiser") { // Global advertiser stats
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $id));

			$x=0;
			foreach($ads as $ad) {
				$title = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = '{$ad->ad}';");
				$startshow = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->ad}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$endshow = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->ad}' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `$wpdb->users`.`ID` = `user` AND `ad` = %d ORDER BY `user_nicename` ASC;", $id));

				$startshow = (is_null($startshow)) ? 0 : $startshow;
				$endshow = (is_null($endshow)) ? 0 : $endshow;
				$stat = adrotate_stats($ad->ad);

				// Prevent gaps in display
				if($stat['impressions'] == 0 OR $stat['clicks'] == 0) {
					$ctr = "0";
				} else {
					$ctr = round((100/$stat['impressions']) * $stat['clicks'],2);
				}

				// Build array
				$adstats[$x]['title'] = $title;
				$adstats[$x]['id'] = $ad->ad;
				$adstats[$x]['startshow'] = date_i18n("M d Y", $startshow);
				$adstats[$x]['endshow']	= date_i18n("M d Y", $endshow);
				$adstats[$x]['impressions']	= $stat['impressions'];
				$adstats[$x]['clicks'] = $stat['clicks'];
				$adstats[$x]['ctr']	= $ctr;
				$x++;
			}

			$filename = "AdRotate_advertiser_".$username.".csv";
			$topic = array("Advertiser report for ".$username);
			$period = array("Period - Not Applicable");
			$keys = array("Title", "Ad ID", "First visibility", "Last visibility", "Clicks", "Impressions", "CTR (%)");
		}

		if($type == "advertiser-single") { // Single advertiser stats
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE (`thetime` >= '{$from}' AND `thetime` <= '{$until}') AND `ad` = %d GROUP BY `thetime` ORDER BY `thetime` ASC;", $id), ARRAY_A);
			$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id));
			$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `$wpdb->users`.`ID` = `user` AND `ad` = %d ORDER BY `user_nicename` ASC;", $id));

			$filename = "AdRotate_stats_advert_ID_".$id."_".$from_name."_".$until_name.".csv";
			$topic = array("Advertiser report for ".$username." for ad '".$title."'");
			$period = array("Period - From: ".$from_name." Until: ".$until_name);
			$keys = array("Day", "Impressions", "Clicks");

			$x=0;
			foreach($ads as $ad) {
				// Prevent gaps in display
				if(empty($ad['impressions'])) $ad['impressions'] = 0;
				if(empty($ad['clicks'])) $ad['clicks'] = 0;

				// Build array
				$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
				$adstats[$x]['impressions'] = $ad['impressions'];
				$adstats[$x]['clicks'] = $ad['clicks'];
				$x++;
			}
		}

		if($adstats) {
			if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
			$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');

			if($fp) {
				fputcsv($fp, $topic);
				fputcsv($fp, $period);
				fputcsv($fp, $generated);
				fputcsv($fp, $keys);
				foreach($adstats as $stat) {
					fputcsv($fp, $stat);
				}

				fclose($fp);

				if($emailcount > 0) {
					$attachments = array(WP_CONTENT_DIR . '/reports/'.$filename);
					$siteurl 	= get_option('siteurl');
					$email 		= get_option('admin_email');

				    $headers = "MIME-Version: 1.0\r\n" .
		    					"From: AdRotate Plugin <".$email.">\r\n" .
		    					"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";

					$subject = __('[AdRotate] CSV Report!', 'adrotate-pro');

					$message = 	"<p>".__('Hello', 'adrotate-pro').",</p>";
					$message .= "<p>".__('Attached in this email you will find the exported CSV file you generated on ', 'adrotate-pro')." $siteurl.</p>";
					$message .= "<p>".__('Have a nice day!', 'adrotate-pro')."<br />";
					$message .= __('Your AdRotate Notifier', 'adrotate-pro')."<br />";
					$message .= "https://ajdg.solutions/products/adrotate-for-wordpress/</p>";

					wp_mail($emails, $subject, $message, $headers, $attachments);

					if($type == "single") adrotate_return('adrotate-statistics', 212, array('view' => 'advert', 'id' => $id));
					if($type == "group") adrotate_return('adrotate-statistics', 212, array('view' => 'group', 'id' => $id));
					if($type == "global") adrotate_return('adrotate-statistics', 212);
					if($type == "advertiser") adrotate_return('adrotate-advertiser', 303);
					if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 303, array('view' => 'report', 'ad' => $id));
					exit;
				}
				if($type == "single") adrotate_return('adrotate-statistics', 215, array('view' => 'advert', 'id' => $id, 'file' => $filename));
				if($type == "group") adrotate_return('adrotate-statistics', 215, array('view' => 'group', 'id' => $id, 'file' => $filename));
				if($type == "global") adrotate_return('adrotate-statistics', 215, array('file' => $filename));
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 215, array('file' => $filename));
				if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 215, array('view' => 'report', 'ad' => $id, 'file' => $filename));
				exit;
			} else {
				if($type == "single") adrotate_return('adrotate-statistics', 507, array('view' => 'advert', 'id' => $id));
				if($type == "group") adrotate_return('adrotate-statistics', 507, array('view' => 'group', 'id' => $id));
				if($type == "global") adrotate_return('adrotate-statistics', 507);
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 507);
				if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 507, array('view' => 'report', 'ad' => $id));
			}
		} else {
			if($type == "single") adrotate_return('adrotate-statistics', 503, array('view' => 'advert', 'id' => $id));
			if($type == "group") adrotate_return('adrotate-statistics', 503, array('view' => 'group', 'id' => $id));
			if($type == "global") adrotate_return('adrotate-statistics', 503);
			if($type == "advertiser") adrotate_return('adrotate-advertiser', 503);
			if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 503, array('view' => 'report', 'ad' => $id));
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export_ads
 Purpose:   Export adverts in various formats
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_export_ads($ids) {
	global $wpdb;

	$where = false;
	if(count($ids) > 1) {
		$where = "`id` = ";
		foreach($ids as $key => $id) {
			$where .= "'{$id}' OR `id` = ";
		}
		$where = rtrim($where, " OR `id` = ");
	}

	if(count($ids) == 1) {
		$where = "`id` = '{$ids[0]}'";
	}

	if($where) {
		$to_export = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE {$where} ORDER BY `id` ASC;", ARRAY_A);
	}

	$adverts = array();
	foreach($to_export as $export) {
		$starttime = $stoptime = 0;
		$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$export['id']."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$export['id']."' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		$export['imagetype'] = (empty($export['imagetype'])) ? '' : $export['imagetype'];
		$export['image'] = (empty($export['image'])) ? '' : $export['image'];
		$export['cities'] = (empty($export['cities'])) ? serialize(array()) : $export['cities'];
		$export['countries'] = (empty($export['countries'])) ? serialize(array()) : $export['countries'];

		$adverts[$export['id']] = array(
			'id' => $export['id'], 'title' => $export['title'], 'bannercode' => stripslashes($export['bannercode']),
			'imagetype' => $export['imagetype'], 'image' => $export['image'],
			'tracker' => $export['tracker'], 'desktop' => $export['desktop'], 'mobile' => $export['mobile'], 'tablet' => $export['tablet'],
			'os_ios' => $export['os_ios'], 'os_android' => $export['os_android'], 
			'weight' => $export['weight'], 'budget' => $export['budget'], 'crate' => $export['crate'], 'irate' => $export['irate'],
			'cities' => $export['cities'], 'countries' => $export['countries'],
			'schedule_start' => $starttime, 'schedule_end' => $stoptime
		);
	}

	if(count($adverts) > 0) {
		$filename = "AdRotate_export_adverts_".date_i18n("mdYHis").".csv";
		if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
		$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');

		if($fp) {
			$plugins = get_plugins();
			$plugin_version = $plugins['adrotate-pro/adrotate-pro.php']['Version'];

			$generated = array('Generated', date_i18n("M d Y, H:i:s"));
			$version = array('Version', 'AdRotate Professional '.$plugin_version);
			$keys = array('id', 'name', 'bannercode', 'imagetype', 'image_url', 'enable_stats', 'show_desktop', 'show_mobile', 'show_tablet', 'show_ios', 'show_android', 'show_otheros', 'weight', 'budget', 'click_rate', 'impression_rate', 'geo_cities', 'geo_countries', 'schedule_start', 'schedule_end');

			fputcsv($fp, $generated);
			fputcsv($fp, $version);
			fputcsv($fp, $keys);
			foreach($adverts as $advert) {
				fputcsv($fp, $advert);
			}

			fclose($fp);

			adrotate_return('adrotate', 215, array('file' => $filename));
			exit;
		}
	} else {
		adrotate_return('adrotate', 509);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_import_ads
 Purpose:   Import adverts from file
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_import_ads() {
	global $wpdb, $current_user, $userdata;

	if(wp_verify_nonce($_POST['adrotate_nonce_tools'], 'adrotate_import')) {
		if(current_user_can('adrotate_ad_manage')) {
			if($_FILES["adrotate_file"]["error"] == 4) {
				adrotate_return('adrotate-settings', 506, array('tab' => 'tools'));
				exit;
			} else if ($_FILES["adrotate_file"]["error"] > 0) {
				adrotate_return('adrotate-settings', 507, array('tab' => 'tools'));
				exit;
			} else if($_FILES["adrotate_file"]["size"] > 4096000) {
				adrotate_return('adrotate-settings', 511, array('tab' => 'tools'));
				exit;
			} else {
				$now = current_time('timestamp');
				$ninetydays = $now + (90 * 86400);

				if($_FILES["adrotate_file"]["type"] == "text/csv") {
					$csv_name = $_FILES["adrotate_file"]["tmp_name"];
					$handle = fopen($csv_name, 'r');

					while($data = fgetcsv($handle, 1000)) {
						if($data[0] == 'Generated' OR $data[0] == 'Version' OR $data[0] == 'id') continue;

						$advert = array(
							'title' => '[import] '.(!empty($data[1])) ? strip_tags(htmlspecialchars_decode(trim($data[1], "\t\n "))) : 'Advert '.$data[0],
							'bannercode' => (!empty($data[2])) ? htmlspecialchars_decode(trim($data[2], "\t\n ")) : '',
							'thetime' => $now,
							'updated' => $now,
							'author' => $current_user->user_login,
							'imagetype' => ($data[3] == "image" OR $data[3] == "dropdown") ? strip_tags(trim($data[3], "\t\n ")) : '',
							'image' => (!empty($data[4])) ? strip_tags(trim($data[4], "\t\n ")) : '',
							'tracker' => ($data[5] == "Y" OR $data[5] == "N" OR $data[5] == "C" OR $data[5] == "I") ? strip_tags(trim($data[5], "\t\n ")) : 'N',
							'desktop' => ($data[6] == "Y" OR $data[6] == "N") ? strip_tags(trim($data[6], "\t\n ")) : 'Y',
							'mobile' => ($data[7] == "Y" OR $data[7] == "N") ? strip_tags(trim($data[7], "\t\n ")) : 'Y',
							'tablet' => ($data[8] == "Y" OR $data[8] == "N") ? strip_tags(trim($data[8], "\t\n ")) : 'Y',
							'os_ios' => ($data[9] == "Y" OR $data[9] == "N") ? strip_tags(trim($data[9], "\t\n ")) : 'Y',
							'os_android' => ($data[10] == "Y" OR $data[10] == "N") ? strip_tags(trim($data[10], "\t\n ")) : 'Y',
							'type' => 'import',
							'weight' => (is_numeric($data[12])) ? strip_tags(trim($data[12], "\t\n ")) : 6,
							'autodelete' => 'N',
							'budget' => (is_numeric($data[13])) ? strip_tags(trim($data[13], "\t\n ")) : 0,
							'crate' => (is_numeric($data[14])) ? strip_tags(trim($data[14], "\t\n ")) : 0,
							'irate' => (is_numeric($data[15])) ? strip_tags(trim($data[15], "\t\n ")) : 0,
							'cities' => (!empty($data[16])) ? strip_tags(trim($data[16], "\t\n ")) : 'a:0:{}',
							'countries' => (!empty($data[17])) ? strip_tags(trim($data[17], "\t\n ")) : 'a:0:{}',
						);
						$wpdb->insert($wpdb->prefix."adrotate", $advert);

						$advert_id = $wpdb->insert_id;
						$schedule = array(
							'name' => 'Schedule for advert '.$advert_id,
							'starttime' => (is_numeric($data[18])) ? strip_tags(trim($data[18], "\t\n ")) : $now,
							'stoptime' => (is_numeric($data[19])) ? strip_tags(trim($data[19], "\t\n ")) : $ninetydays,
							'maxclicks' => 0,
							'maximpressions' => 0,
							'spread' => 'N',
							'spread_all' => 'N',
							'daystarttime' => '0000',
							'daystoptime' => '0000',
							'day_mon' => 'Y',
							'day_tue' => 'Y',
							'day_wed' => 'Y',
							'day_thu' => 'Y',
							'day_fri' => 'Y',
							'day_sat' => 'Y',
							'day_sun' => 'Y',
							'autodelete' => 'N',
						);
						$wpdb->insert($wpdb->prefix."adrotate_schedule", $schedule);

						$schedule_id = $wpdb->insert_id;
						$linkmeta = array(
							'ad' => $advert_id,
							'group' => 0,
							'user' => 0,
							'schedule' => $schedule_id,
						);
						$wpdb->insert($wpdb->prefix."adrotate_linkmeta", $linkmeta);

						unset($advert, $advert, $advert_id, $schedule, $schedule_id, $linkmeta);
					}

					// Delete uploaded file
					unlink($csv_name);
				}

				// Verify all ads
				adrotate_evaluate_ads();

				// return to dashboard
				adrotate_return('adrotate-settings', 216, array('tab' => 'tools'));
				exit;
			}
		} else {
			adrotate_return('adrotate-settings', 500, array('tab' => 'tools'));
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_portable_hash
 Purpose:   Export/import adverts via a portable hash
 Since:		5.8.3
-------------------------------------------------------------*/
function adrotate_portable_hash($action, $data, $what = 'advert') {
	$source = get_option('siteurl');
	if(in_array("aes-128-cbc", openssl_get_cipher_methods())) {
		if($action == 'export') {
			$portable['meta'] = array('type' => $what, 'source' => $source, 'exported' => current_time('timestamp'));
			foreach($data as $key => $value) {
				if(empty($value)) $value = '';
				$advert[$key] = $value;
			}
			$portable['data'] = $advert;
			$portable = serialize($portable);
			return openssl_encrypt($portable, "aes-128-cbc", '983jdnn3idjk02id', false, 'oi1u23kj123hj7jd');
	    }

		if($action == 'import') {
			$data = openssl_decrypt($data, "aes-128-cbc", '983jdnn3idjk02id', false, 'oi1u23kj123hj7jd');
			$data = unserialize($data);
			if(is_array($data)) {
				if(array_key_exists('meta', $data) AND array_key_exists('data', $data)) {
					if($data['meta']['type'] == 'advert' AND $source != $data['meta']['source']) {
						return $data['data'];
					} else if($data['meta']['type'] == 'group') {
						// maybe
					} else if($data['meta']['type'] == 'schedule') {
						// maybe
					} else {
						adrotate_return('adrotate', 511);
					}
				}
			}
			adrotate_return('adrotate', 510);
	    }

	}
}
?>
