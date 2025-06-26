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
 Name:      adrotate_advertiser_insert_input

 Purpose:   Prepare input form on saving new or updated banners
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_advertiser_insert_input() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_ad')) {
		// Mandatory
		$id = $author = $title = $bannercode = '';
		if(isset($_POST['adrotate_id'])) $id = sanitize_key($_POST['adrotate_id']);
		if(isset($_POST['adrotate_username'])) $author = sanitize_key($_POST['adrotate_username']);
		if(isset($_POST['adrotate_title'])) $title = sanitize_text_field($_POST['adrotate_title']);
		if(isset($_POST['adrotate_bannercode'])) $bannercode = htmlspecialchars(trim($_POST['adrotate_bannercode']), ENT_QUOTES);
		$thetime = current_time('timestamp');

		// Schedule and timeframe variables
		$schedules = $groups = $group_array = '';
		if(isset($_POST['scheduleselect'])) $schedules = $_POST['scheduleselect'];
		if(isset($_POST['groupselect'])) $groups = $_POST['groupselect'];

		// Advert options
		$adrotate_image_current = $type = $weight = '';
		if(isset($_POST['adrotate_image_dropdown'])) $image_dropdown = strip_tags(trim($_POST['adrotate_image_dropdown']));
		if(isset($_POST['adrotate_type'])) $type = strip_tags(htmlspecialchars(trim($_POST['adrotate_type']), ENT_QUOTES));
		if(isset($_POST['adrotate_desktop'])) $desktop = strip_tags(trim($_POST['adrotate_desktop']));
		if(isset($_POST['adrotate_mobile'])) $mobile = strip_tags(trim($_POST['adrotate_mobile']));
		if(isset($_POST['adrotate_tablet'])) $tablet = strip_tags(trim($_POST['adrotate_tablet']));
		if(isset($_POST['adrotate_ios'])) $desktop = strip_tags(trim($_POST['adrotate_ios']));
		if(isset($_POST['adrotate_android'])) $mobile = strip_tags(trim($_POST['adrotate_android']));
		if(isset($_POST['adrotate_other'])) $tablet = strip_tags(trim($_POST['adrotate_other']));
		if(isset($_POST['adrotate_weight'])) $weight = strip_tags(trim($_POST['adrotate_weight']));

		// GeoTargeting
		$cities = $states = $state_req = '';
		$countries = $countries_westeurope = $countries_easteurope = $countries_northamerica = $countries_southamerica = $countries_southeastasia = array();
		if(isset($_POST['adrotate_geo_state_required'])) $state_req = strip_tags(trim($_POST['adrotate_geo_state_required']));
		if(isset($_POST['adrotate_geo_cities'])) $cities = strip_tags(trim($_POST['adrotate_geo_cities']));
		if(isset($_POST['adrotate_geo_states'])) $states = strip_tags(trim($_POST['adrotate_geo_states']));
		if(isset($_POST['adrotate_geo_countries'])) $countries = $_POST['adrotate_geo_countries'];
		if(isset($_POST['adrotate_geo_westeurope'])) $countries_westeurope = array('AD', 'AT', 'BE', 'DK', 'FR', 'DE', 'GR', 'IS', 'IE', 'IT', 'LI', 'LU', 'MT', 'MC', 'NL', 'NO', 'PT', 'SM', 'ES', 'SE', 'CH', 'VA', 'GB');
		if(isset($_POST['adrotate_geo_easteurope'])) $countries_easteurope = array('AL', 'AM', 'AZ', 'BY', 'BA', 'BG', 'HR', 'CY', 'CZ', 'EE', 'FI', 'GE', 'HU', 'LV', 'LT', 'MK', 'MD', 'PL', 'RO', 'RS', 'SK', 'SI', 'TR', 'UA');
		if(isset($_POST['adrotate_geo_northamerica'])) $countries_northamerica = array('AG', 'BS', 'BB', 'BZ', 'CA', 'CR', 'CU', 'DM', 'DO', 'SV', 'GD', 'GT', 'HT', 'HN', 'JM', 'MX', 'NI', 'PA', 'KN', 'LC', 'VC', 'TT', 'US');
		if(isset($_POST['adrotate_geo_southamerica'])) $countries_southamerica = array('AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'GY', 'PY', 'PE', 'SR', 'UY', 'VE');
		if(isset($_POST['adrotate_geo_southeastasia'])) $countries_southeastasia = array('AU', 'BN', 'KH', 'TL', 'ID', 'LA', 'MY', 'MM', 'NZ', 'PH', 'SG', 'TH', 'VN');

		if(current_user_can('adrotate_advertiser')) {
			if(strlen($title) < 1) $title = 'Ad '.$id;

			// Clean up bannercode
			if(preg_match("/%ID%/", $bannercode)) $bannercode = str_replace('%ID%', '%id%', $bannercode);
			if(preg_match("/%ASSET%/", $bannercode)) $bannercode = str_replace('%ASSET%', '%asset%', $bannercode);
			if(preg_match("/%IMAGE%/", $bannercode)) $bannercode = str_replace('%IMAGE%', '%image%', $bannercode);
			if(preg_match("/%TITLE%/", $bannercode)) $bannercode = str_replace('%TITLE%', '%title%', $bannercode);
			if(preg_match("/%RANDOM%/", $bannercode)) $bannercode = str_replace('%RANDOM%', '%random%', $bannercode);

			if($_FILES["adrotate_image"]["size"] > 0 AND $_FILES["adrotate_image"]["size"] <= 512000) {
				$file_path = WP_CONTENT_DIR."/".$adrotate_config['banner_folder']."/";
				$file = explode(".", adrotate_sanitize_file_name($_FILES["adrotate_image"]["name"]));
				$file_name = implode('.', $file);
				$file_extension = array_pop($file);
				$file_mimetype = mime_content_type($_FILES["adrotate_image"]["tmp_name"]);

				if(
					in_array($file_extension, array("jpg", "jpeg", "gif", "png", "svg"))
					AND in_array($file_mimetype, array("image/jpg", "image/pjpeg", "image/jpeg", "image/gif", "image/png", "image/svg"))
				) {
					if ($_FILES["adrotate_image"]["error"] > 0) {
						if($_FILES["adrotate_image"]["error"] == 1 OR $_FILES["adrotate_image"]["error"] == 2) $errorcode = __("File size exceeded.", "adrotate-pro");
						else if($_FILES["adrotate_image"]["error"] == 3) $errorcode = __("Upload incomplete.", "adrotate-pro");
						else if($_FILES["adrotate_image"]["error"] == 4) $errorcode = __("No file uploaded.", "adrotate-pro");
						else if($_FILES["adrotate_image"]["error"] == 6 OR $_FILES["adrotate_image"]["error"] == 7) $errorcode = __("Could not write file to server.", "adrotate-pro");
						else $errorcode = __("An unknown error occured, contact staff.", "adrotate-pro");
						wp_die("<h3>".__("Something went wrong!", "adrotate-pro")."</h3><p>".__("Go back and try again. If the error persists, contact staff.", "adrotate-pro")."</p><p style='color: #f00;'>".$errorcode."</p>");
					} else {
						$image_name = $id."-".$author."-".$thetime."-".$file_name;
						move_uploaded_file($_FILES["adrotate_image"]["tmp_name"], $file_path . $image_name);
					}
				} else {
					wp_die("<h3>".__("Something went wrong!", "adrotate-pro")."</h3><p>".__("Go back and try again. If the error persists, contact staff.", "adrotate-pro")."</p><p style='color: #f00;'>".__("The file was either too large or not in the right format.", "adrotate-pro")."</p>");
				}
			} else {
				$image_name = $image_dropdown;
			}

			// Force image location
			$image = WP_CONTENT_URL."/%folder%/".$image_name;

			// Determine image settings ($image_field has priority!)
			if(strlen($image_name) > 0) {
				$imagetype = "dropdown";
				$image = WP_CONTENT_URL."/%folder%/".$image_name;
			} else {
				$imagetype = "";
				$image = "";
			}

			// Advert options/features
			if($weight != 2 AND $weight != 4 AND $weight != 6 AND $weight != 8 AND $weight != 10) $weight = 6;
			if(isset($desktop) AND strlen($desktop) != 0) $desktop = 'Y';
				else $desktop = 'N';
			if(isset($mobile) AND strlen($mobile) != 0) $mobile = 'Y';
				else $mobile = 'N';
			if(isset($tablet) AND strlen($tablet) != 0) $tablet = 'Y';
				else $tablet = 'N';
			if(isset($os_ios) AND strlen($os_ios) != 0) $os_ios = 'Y';
				else $os_ios = 'N';
			if(isset($os_android) AND strlen($os_android) != 0) $os_android = 'Y';
				else $os_android = 'N';

			// Geo Targeting
			if(isset($state_req) AND strlen($state_req) != 0) $state_req = 'Y';
				else $state_req = 'N';

			if(strlen($cities) > 0) {
				$cities = explode(",", strtolower($cities));
				foreach($cities as $key => $value) {
					$cities_clean[] = trim($value);
					unset($value);
				}
				unset($cities);
				$cities = serialize($cities_clean);
			}

			if(strlen($states) > 0) {
				$states = explode(",", strtolower($states));
				foreach($states as $key => $value) {
					$states_clean[] = trim($value);
					unset($value);
				}
				unset($states);
				$states = serialize($states_clean);
			} else {
				$states = serialize(array());
			}

			$countries = array_merge($countries, $countries_westeurope, $countries_easteurope, $countries_northamerica, $countries_southamerica, $countries_southeastasia);
			$countries = array_unique($countries);
			if(count($countries) == 0) {
				$countries = serialize(array());
			} else {
				foreach($countries as $key => $value) {
					$countries_clean[] = trim($value);
					unset($value);
				}
				unset($countries);
				$countries = serialize($countries_clean);
			}

			// Fetch schedules for the ad
			$schedulemeta = $wpdb->get_results($wpdb->prepare("SELECT `schedule` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0;", $id));
			$schedule_array = array();
			foreach($schedulemeta as $meta) {
				$schedule_array[] = $meta->schedule;
				unset($meta);
			}

			// Add new schedules to this ad
			if(!is_array($schedules)) $schedules = array();
			$insert = array_diff($schedules, $schedule_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'user' => 0, 'schedule' => $value));
			}
			unset($insert, $value);

			// Remove schedules from this ad
			$delete = array_diff($schedule_array, $schedules);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 AND `schedule` = %d;", $id, $value));
			}
			unset($delete, $value, $schedulemeta, $schedule_array);

			// Fetch group records for the ad
			$groupmeta = $wpdb->get_results($wpdb->prepare("SELECT `group` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `user` = 0 AND `schedule` = 0;", $id));
			$group_array = array();
			foreach($groupmeta as $meta) {
				$group_array[] = $meta->group;
				unset($meta);
			}

			// Add new groups to this ad
			if(!is_array($groups)) $groups = array();
			$insert = array_diff($groups, $group_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => $value, 'user' => 0, 'schedule' => 0));
			}
			unset($insert, $value);

			// Remove groups from this ad
			$delete = array_diff($group_array, $groups);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `user` = 0 AND `schedule` = 0;", $id, $value));
			}
			unset($delete, $value, $groupmeta, $group_array);

			// Save the ad to the DB
			$wpdb->update($wpdb->prefix.'adrotate', array('title' => $title, 'bannercode' => $bannercode, 'updated' => $thetime, 'author' => $author, 'imagetype' => $imagetype, 'image' => $image, 'desktop' => $desktop, 'mobile' => $mobile, 'tablet' => $tablet, 'weight' => $weight, 'state_req' => $state_req, 'cities' => $cities, 'states' => $states, 'countries' => $countries), array('id' => $id));

			// Determine status of ad
			$adstate = adrotate_evaluate_ad($id);
			if($adstate == 'error' OR $adstate == 'expired') {
				$action = 502;
				$active = 'a_error';
			} else {
				$action = 306;
				$active = 'queue';
			}
			$wpdb->update($wpdb->prefix.'adrotate', array('type' => $active), array('id' => $id));

			if($action == 306) {
				adrotate_notifications('queued', $id);
			}

			// Fetch records for the ad, see if a publisher is set
			$linkmeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` > 0;", $id));
			$advertiser = wp_get_current_user();

			// Add/update publisher on this ad
			if($linkmeta == 0 AND $advertiser->ID > 0) $wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'user' => $advertiser->ID, 'schedule' => 0));
			if($linkmeta == 1 AND $advertiser->ID > 0) $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."adrotate_linkmeta` SET `user` = $advertiser->ID WHERE `ad` = %d AND `group` = 0 AND `schedule` = 0;", $id));

			adrotate_return('adrotate-advertiser', $action);
			exit;
		} else {
			adrotate_return('adrotate-advertiser', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}
?>
