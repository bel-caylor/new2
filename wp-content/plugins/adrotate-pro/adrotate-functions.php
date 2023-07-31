<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2023 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_can_edit
 Purpose:   Return a array of adverts to use on advertiser dashboards
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_can_edit() {
	global $adrotate_config;

	if($adrotate_config['enable_editing'] == 'Y') {
		return true;
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_is_networked
 Purpose:   Determine if AdRotate is network activated
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_is_networked() {
	if(!function_exists('is_plugin_active_for_network')) require_once(ABSPATH.'/wp-admin/includes/plugin.php');

	if(is_plugin_active_for_network('adrotate-pro/adrotate-pro.php')) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_license
 Purpose:   Get the license
 Since:		5.6.2
-------------------------------------------------------------*/
function adrotate_get_license() {
	$a = (adrotate_is_networked()) ? get_site_option('adrotate_activate') : get_option('adrotate_activate');
	return $a;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_human
 Purpose:   Check if visitor is a bot
 Since:		3.11.10
-------------------------------------------------------------*/
function adrotate_is_human() {
	global $adrotate_crawlers;

	if(is_array($adrotate_crawlers)) {
		$crawlers = $adrotate_crawlers;
	} else {
		$crawlers = array();
	}

	$useragent = adrotate_get_useragent();

	$nocrawler = array(true);
	if(strlen($useragent) > 0) {
		foreach($crawlers as $key => $crawler) {
			if(preg_match('/'.$crawler.'/i', $useragent)) $nocrawler[] = false;
		}
	}
	$nocrawler = (!in_array(false, $nocrawler)) ? true : false; // If no bool false in array it's not a bot

	// Returns true if no bot.
	return $nocrawler;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_ios
 Purpose:   Check if OS is iOS
 Since:		4.1
-------------------------------------------------------------*/
function adrotate_is_ios() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;

	if($detect->isiOS() AND !$detect->isAndroidOS()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_android
 Purpose:   Check if OS is Android
 Since:		4.1
-------------------------------------------------------------*/
function adrotate_is_android() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;

	if(!$detect->isiOS() AND $detect->isAndroidOS()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_mobile
 Purpose:   Check if visitor is on a smartphone
 Since:		3.12.6
-------------------------------------------------------------*/
function adrotate_is_mobile() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;

	if($detect->isMobile() AND !$detect->isTablet()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_tablet
 Purpose:   Check if visitor is on a tablet
 Since:		3.16
-------------------------------------------------------------*/
function adrotate_is_tablet() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;

	if($detect->isTablet()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_duplicates
 Purpose:   Remove adverts that already show elsewhere on the page
 Since:		5.5
-------------------------------------------------------------*/
function adrotate_filter_duplicates($selected, $banner_id, $session_page) {
	if(isset($_SESSION['adrotate-duplicate-ads'])) {
		// Set session data
		if(!array_key_exists($session_page, $_SESSION['adrotate-duplicate-ads']) OR ($_SESSION['adrotate-duplicate-ads'][$session_page]['timeout'] < current_time('timestamp'))) {
			$_SESSION['adrotate-duplicate-ads'][$session_page] = array('timeout' => current_time('timestamp'), 'adverts' => array());
		}

		// Remove advert if it's in session data
		if(in_array($banner_id, $_SESSION['adrotate-duplicate-ads'][$session_page]['adverts'])) {
			unset($selected[$banner_id]);
		}
	}

	return $selected;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_schedule
 Purpose:   Weed out ads that are over the limit of their schedule
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_schedule($selected, $banner) {
	global $wpdb, $adrotate_config;

	$now = current_time('timestamp');
	$day = date('D', $now);
	$hour = date('Hi', $now);

	$schedules = $wpdb->get_results("SELECT `{$wpdb->prefix}adrotate_schedule`.* FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` AND `ad` = {$banner->id} ORDER BY `starttime` ASC;");

	$current = array();
	foreach($schedules as $schedule) {
		if($schedule->starttime > $now OR $schedule->stoptime < $now) {
			$current[] = 0;
		} else if(($schedule->day_mon != 'Y' AND $day == 'Mon') OR ($schedule->day_tue != 'Y' AND $day == 'Tue') OR ($schedule->day_wed != 'Y' AND $day == 'Wed') OR ($schedule->day_thu != 'Y' AND $day == 'Thu') OR ($schedule->day_fri != 'Y' AND $day == 'Fri') OR ($schedule->day_sat != 'Y' AND $day == 'Sat') OR ($schedule->day_sun != 'Y' AND $day == 'Sun')) {
			$current[] = 0;
		} else if(($schedule->daystarttime > 0 OR $schedule->daystoptime > 0) AND ($schedule->daystarttime > $hour OR $schedule->daystoptime < $hour)) {
			$current[] = 0;
		} else {
			if($adrotate_config['stats'] == 1 AND $banner->tracker != 'N') {
				$stat = adrotate_stats($banner->id, false, $schedule->starttime, $schedule->stoptime);
				if($schedule->spread_all == 'Y') {
					$group_impressions = $wpdb->get_var("SELECT SUM(`impressions`) FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = {$schedule->id} AND `{$wpdb->prefix}adrotate_linkmeta`.`ad` = `{$wpdb->prefix}adrotate_stats`.`ad` AND `thetime` >= {$schedule->starttime} AND `thetime` <= {$schedule->stoptime};");
				} else {
					$group_impressions = 0;
				}
				$temp_max_impressions = floor($schedule->maximpressions / ($schedule->stoptime - $schedule->starttime) * ($now - $schedule->starttime));

				if(!is_array($stat)) $stat = array('clicks' => 0, 'impressions' => 0);

				if($stat['clicks'] >= $schedule->maxclicks AND $schedule->maxclicks > 0) { // Click limit reached?
					$current[] = 0;
					$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'limit' WHERE `id` = {$banner->id};"); // Set advert expired
				} else if($schedule->spread == 'Y' AND $stat['impressions'] > $temp_max_impressions) { // Impression spread
					$current[] = 0;
				} else if($schedule->spread_all == 'Y' AND $group_impressions > $temp_max_impressions) { // Impression spread all (campaigns)
					$current[] = 0;
				} else if($stat['impressions'] >= $schedule->maximpressions AND $schedule->maximpressions > 0) { // Impression limit reached?
					$current[] = 0;
					$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'limit' WHERE `id` = {$banner->id};"); // Set advert expired
				} else { // Everything else
					$current[] = 1;
				}
			} else {
				$current[] = 1;
			}
		}
	}

	// Remove advert from array if all schedules are false (0)
	if(!in_array(1, $current)) {
		unset($selected[$banner->id]);
	}
	unset($current, $schedules);

	return $selected;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_show_everyone
 Purpose:   Remove adverts that don't show to logged in users
 Since:		4.8
-------------------------------------------------------------*/
function adrotate_filter_show_everyone($selected, $banner) {
	if(($banner->show_everyone == "N") AND is_user_logged_in()) {
		unset($selected[$banner->id]);
	}

	return $selected;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_budget
 Purpose:   Weed out ads that are over the limit of their schedule
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_budget($selected, $banner) {
	global $wpdb;

	if($banner->budget == null) $banner->budget = '0';
	if($banner->crate == null) $banner->crate = '0';
	if($banner->irate == null) $banner->irate = '0';

	if(($banner->budget <= 0 AND $banner->crate > 0) OR ($banner->budget <= 0 AND $banner->irate > 0)) {
		unset($selected[$banner->id]);

		// Set advert expired
		$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'limit' WHERE `id` = {$banner->id};");

		return $selected;
	}
	if($banner->budget > 0 AND $banner->irate > 0) {
		$cpm = number_format($banner->irate / 1000, 4, '.', '');
		$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `budget` = `budget` - {$cpm} WHERE `id` = {$banner->id};");
	}

	return $selected;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_location
 Purpose:   Determine the users location, the ads geo settings and filter out ads
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_filter_location($selected, $banner) {
	// Grab geo data from session
	$geo = $_SESSION['adrotate-geo'];

	if(is_array($geo)) {
		// Good Geo Response?
		if($geo['code'] == 200) {
			$keep = array();
			$cities = unserialize(stripslashes($banner->cities));
			$states = unserialize(stripslashes($banner->states));
			$countries = unserialize(stripslashes($banner->countries));
			if(!is_array($cities)) $cities = array();
			if(!is_array($states)) $states = array();
			if(!is_array($countries)) $countries = array();

			// Match a city
			if(count($cities) > 0) {
				if(count(array_intersect($cities, array($geo['city'], $geo['dma']))) == 0) {
					$keep['city'] = 'N';
				} else {
					$keep['city'] = 'Y';
				}
			}
			// Match a state
			if(count($states) > 0) {
				if(count(array_intersect($states, array($geo['state'], $geo['statecode']))) == 0) {
					$keep['state'] = 'N';
				} else {
					$keep['state'] = 'Y';
				}
			}
			// Match a city in a state
			if(count($cities) > 0 AND count($states) > 0 AND $banner->state_req == 'Y') {
				if($keep['city'] == 'N' AND $keep['state'] = 'N') {
					$keep['state_req'] = 'N';
				} else {
					$keep['state_req'] = 'Y';
				}
			}
			// Match a country
			if(count($countries) > 0) {
				if(!in_array($geo['countrycode'], $countries)) {
					$keep['country'] = 'N';
				} else {
					$keep['country'] = 'Y';
				}
			}

			// Remove advert from pool?
			if(!in_array('Y', $keep)) {
				unset($selected[$banner->id]);
			}
			unset($keep);
		}
	}

	return $selected;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_content
 Purpose:   Deal with quotes, pre and embed codes
 Since:		4.14
-------------------------------------------------------------*/
function adrotate_filter_content($content) {
	// Deal with <blockquote>
    $array = preg_split("/<blockquote>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<blockquote>";
        $array2 = preg_split(",</blockquote>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</blockquote>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

	// Deal with <pre>
    $array = preg_split("/<pre>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<pre>";
        $array2 = preg_split(",</pre>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</pre>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

	// Deal with <code>
    $array = preg_split("/<code>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<code>";
        $array2 = preg_split(",</code>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</code>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

    return $content;
}

/*-------------------------------------------------------------
 Name:      adrotate_session
 Purpose:   Set up a session for Geo Targeting and Duplicate ads
 Since:		5.6.1
-------------------------------------------------------------*/
function adrotate_session() {
	if(!wp_doing_cron() AND !defined('WP_CLI')) {
		global $adrotate_config;

		$geo_required = get_option('adrotate_geo_required');
		if($geo_required > 0 OR $adrotate_config['duplicate_adverts_filter'] == "Y" AND !session_id()) {
			if(version_compare(PHP_VERSION, '7.0.0') >= 0) {
				session_start(array('cache_limiter' => 'nocache', 'read_and_close' => false));
			} else {
				session_cache_limiter('nocache');
				session_start();
			}
			session_write_close();
		}

		if($geo_required > 0) {
			adrotate_geolocation();
		}
		if($adrotate_config['duplicate_adverts_filter'] == "Y") {
			$_SESSION['adrotate-duplicate-ads'] = array();
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_geolocation
 Purpose:   Find the location of the visitor
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_geolocation() {
	if(!wp_doing_cron() AND !defined('WP_CLI') AND !isset($_SESSION['adrotate-geo']) AND adrotate_is_human()) {
		$adrotate_config = get_option('adrotate_config');
		$remote_ip = adrotate_get_remote_ip();
		$geo_result = array();

		$plugin_version = get_plugins();
		$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];
		$useragent = 'AdRotate Pro/'.$plugin_version.';';

		if($adrotate_config['enable_geo'] == 1 OR $adrotate_config['enable_geo'] == 2) { // Telize OR GeoBytes (obsolete)
			$adrotate_config['enable_geo'] = 5; // Override setting, assume AdRotate Geo
			update_option('adrotate_config', $adrotate_config);
		}

		if($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) { // MaxMind
			if($adrotate_config['enable_geo'] == 3) {
				$service_type = 'country';
			}
			if($adrotate_config['enable_geo'] == 4) {
				$service_type = 'city';
			}

			$args = array('timeout' => 5, 'sslverify' => false, 'headers' => array('user-agent' => $useragent, 'Authorization' => 'Basic '.base64_encode($adrotate_config["geo_email"].':'.$adrotate_config["geo_pass"])));
			$response = wp_remote_get('https://geoip.maxmind.com/geoip/v2.1/'.$service_type.'/'.$remote_ip, $args);

			$geo_result['provider'] = 'MaxMind '.$service_type;
		    if(!is_wp_error($response)) {
			    $data = json_decode($response['body'], true);
				if($response['response']['code'] === 200) {
					$geo_result['code'] = '200';
					$geo_result['message'] = 'OK';
					$geo_result['city'] = (isset($data['city']['names']['en'])) ? strtolower($data['city']['names']['en']) : '';
					$geo_result['dma'] = (isset($data['location']['metro_code'])) ? strtolower($data['location']['metro_code']) : '';
					$geo_result['countrycode'] = (isset($data['country']['iso_code'])) ? $data['country']['iso_code'] : '';
					$geo_result['state'] = (isset($data['subdivisions'][0]['names']['en'])) ? strtolower($data['subdivisions'][0]['names']['en']) : '';
					$geo_result['statecode'] = (isset($data['subdivisions'][0]['iso_code'])) ? strtolower($data['subdivisions'][0]['iso_code']) : '';
					$data['lookups_used'] = ($data['code'] == 'IP_ADDRESS_RESERVED') ? 0 : $data['maxmind']['queries_remaining'];
					$data['lookups_used'] = ($data['code'] == 'OUT_OF_QUERIES') ? 0 : $data['maxmind']['queries_remaining'];
				} else {
					$geo_result['code'] = $data['code'];
					$geo_result['message'] = $data['error'];
					$data['lookups_used'] = 0;
				}
			} else {
				$geo_result['code'] = $response->get_error_code();
				$geo_result['message'] = $response->get_error_message($geo_result['code']);
				$data['lookups_used'] = 0;
			}
			update_option('adrotate_geo_requests', $data['lookups_used']);
		}

		if($adrotate_config['enable_geo'] == 5) { // AdRotate Geo
			if(!get_transient('adrotate_api_banned')) {
				$lookups = get_option('adrotate_geo_requests');
				$until_day_end = gmmktime(0, 0, 0) + 86400 - current_time('timestamp');

				// Maybe poke AdRotate Geo to figure out if a lookup should be made
				if($lookups < 1 AND !get_transient('adrotate_geo_reset')) {
					$lookups = 5; // 5-ish attempts to reset quota
				}

				// Do a lookup if there are enough lookups available
				if($lookups > 0) {
					$license = adrotate_get_license();
					$request = array('slug' => "adrotate-pro", 'instance' => (is_array($license)) ? $license['instance'] : '',  'platform' => get_option('siteurl'), 'ip' => $remote_ip, 'et' => microtime(true));

					$args = array('headers' => array('Accept' => 'multipart/form-data'), 'body' => array('r' => serialize($request)), 'user-agent' => $useragent, 'timeout' => 5,	'sslverify' => false);
					$response = wp_remote_post('https://ajdg.solutions/api/geo/6/', $args);

					$geo_result['provider'] = 'AdRotate Geo';
				    if(!is_wp_error($response)) {
					    $data = json_decode($response['body'], true);
						if($response['response']['code'] === 200) {
							$geo_result['code'] = '200';
							$geo_result['message'] = 'OK';
							$geo_result['city'] = (isset($data['city'])) ? strtolower($data['city']) : '';
							$geo_result['dma'] = (isset($data['dma'])) ? strtolower($data['dma']) : '';
							$geo_result['countrycode'] = (isset($data['countrycode'])) ? $data['countrycode'] : '';
							$geo_result['state'] = (isset($data['state'])) ? strtolower($data['state']) : '';
							$geo_result['statecode'] = (isset($data['statecode'])) ? strtolower($data['statecode']) : '';
							$data['lookups_used'] = 30000 - $data['lookups_used'];
						} else {
							$geo_result['code'] = $data['code'];
							$geo_result['message'] = $data['error'];
							$data['lookups_used'] = 0;

							if($geo_result['code'] == 403 AND $geo_result['error'] == 'Invalid or Expired license') { // Expired license
								$adrotate_config['enable_geo'] = 0; // Disable Geo Targeting
								update_option('adrotate_config', $adrotate_config);
							}
							if($geo_result['code'] == 403 AND $geo_result['error'] == 'IP banned') set_transient('adrotate_api_banned', $geo_result['error'], 172790); // User banned
							if($geo_result['code'] == 429) set_transient('adrotate_api_banned', $geo_result['error'], $until_day_end); // No lookups
						}
					} else {
						$geo_result['code'] = $response->get_error_code();
						$geo_result['message'] = $response->get_error_message($geo_result['code']);
						$data['lookups_used'] = 0;
					}
					update_option('adrotate_geo_requests', $data['lookups_used']);
					set_transient('adrotate_geo_reset', 1, $until_day_end);
				}
			}
		}
	    unset($response);

		if($adrotate_config['enable_geo'] == 6) { // CloudFlare
			$geo_result['provider'] = 'CloudFlare';
		    if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
				$geo_result['code'] = '200';
				$geo_result['message'] = 'OK';
				$geo_result['city'] = '';
				$geo_result['dma'] = '';
				$geo_result['countrycode'] = ($_SERVER["HTTP_CF_IPCOUNTRY"] == 'xx') ? '' : $_SERVER["HTTP_CF_IPCOUNTRY"];
				$geo_result['state'] = '';
				$geo_result['statecode'] = '';
			} else {
				$geo_result['code'] = 503;
				$geo_result['message'] = 'Header not found, check if Geo feature in CloudFlare is enabled.';
			}
		}

		if($adrotate_config['enable_geo'] == 7) { // ipstack
			// Does not report lookups
			$args = array('timeout' => 5, 'headers' => array('User-Agent' => $useragent));
			$response = wp_remote_get('http://api.ipstack.com/'.$remote_ip.'?access_key='.$adrotate_config["geo_pass"], $args);

			$geo_result['provider'] = 'ipstack';
		    if(!is_wp_error($response)) {
			    $data = json_decode($response['body'], true);
				if($response['response']['code'] === 200 AND !array_key_exists('error', $data)) {
					$geo_result['code'] = '200';
					$geo_result['message'] = 'OK';
					$geo_result['city'] = (isset($data['city'])) ? strtolower($data['city']) : '';
					$geo_result['dma'] = (isset($data['geoname_id'])) ? strtolower($data['geoname_id']) : '';
					$geo_result['countrycode'] = (isset($data['country_code'])) ? $data['country_code'] : '';
					$geo_result['state'] = (isset($data['region_name'])) ? strtolower($data['region_name']) : '';
					$geo_result['statecode'] = (isset($data['region_code'])) ? strtolower($data['region_code']) : '';
				} else {
					$geo_result['code'] = $data['error']['code'].' '.$data['error']['type'];
					$geo_result['message'] = $data['error']['info'];
				}
			} else {
				$geo_result['code'] = $response->get_error_code();
				$geo_result['message'] = $response->get_error_message($geo_result['code']);
			}
		}
	    unset($response);

		$_SESSION['adrotate-geo'] = $geo_result;
		set_transient('ajdg_geo_response', array('code' => $geo_result['code'], 'message' => $geo_result['message'], 'last_checked' => date_i18n('F j, Y, g:i a')), 21600); // Expire in 6 hours
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_object_to_array
 Purpose:   Convert an object to a array
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_object_to_array($data) {
	if(is_array($data)) {
		return $data;
	}

	if(is_object($data)) {
		$result = array();
		foreach($data as $key => $value) {
			$result[$key] = adrotate_object_to_array($value);
		}
		return $result;
	}
	return $data;
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique
 Purpose:   Filter out duplicate records in multidimensional arrays
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_array_unique($array) {
	if(count($array) > 0) {
		if(is_array($array[0])) {
			$return = array();
			// multidimensional
			foreach($array as $row) {
				if(!in_array($row, $return)) {
					$return[] = $row;
				}
			}
			return $return;
		} else {
			// not multidimensional
			return array_unique($array);
		}
	} else {
		return $array;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_rand
 Purpose:   Generate a random string
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_rand($length = 8) {
	$available_chars = "abcdefghijklmnopqrstuvwxyz";

	$result = '';
	for($i = 0; $i < $length; $i++) {
		$result .= $available_chars[mt_rand(0, 25)];
	}

	return $result;
}

/*-------------------------------------------------------------
 Name:      adrotate_pick_weight
 Purpose:   Sort out and pick a random ad based on weight
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_pick_weight($selected) {
    $ads = array_keys($selected);
    foreach($selected as $banner) {
		$weight[] = $banner->weight;
		unset($banner);
	}

    $sum_of_weight = array_sum($weight)-1;
    if($sum_of_weight < 1) $sum_of_weight = 2;
    $random = mt_rand(0, $sum_of_weight);

    foreach($ads as $key => $var){
        if($random < $weight[$key]){
            return $ads[$key];
        }
        $random = $random - $weight[$key];
    }
    unset($ads, $weight, $sum_of_weight, $random);
}

/*-------------------------------------------------------------
 Name:      adrotate_shuffle
 Purpose:   Randomize and slice an array but keep keys intact
 Since:		3.8.8.3
-------------------------------------------------------------*/
function adrotate_shuffle($array, $amount = 20) {
	if(!is_array($array)) return $array;
	$keys = array_keys($array);
	shuffle($keys);

	$shuffle = array();
	foreach($keys as $key) {
		$shuffle[$key] = $array[$key];
	}
	return $shuffle;
}

/*-------------------------------------------------------------
 Name:      adrotate_select_categories
 Purpose:   Create scrolling menu of all categories.
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_categories($savedcats, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedcats)) $savedcats = explode(',', $savedcats);
	$categories = get_categories(array('child_of' => $parent, 'parent' => $parent,  'orderby' => 'id', 'order' => 'asc', 'hide_empty' => 0));

	if(!empty($categories)) {
		$output = '';
		if($parent == 0) {
			$output .= '<table width="100%">';
			$output .= '<thead><tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			$output .= '<tbody>';
		}
		foreach($categories as $category) {
			if($category->parent > 0) {
				if($category->parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';

			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_categories[]" value="'.$category->cat_ID.'"';
			$output .= (in_array($category->cat_ID, $savedcats)) ? ' checked' : '';
			$output .= '></td><td style="padding: 0px;">'.$indent.$category->name.' ('.$category->category_count.')</td>';

			$output .= '</tr>';
			$output .= adrotate_select_categories($savedcats, $count, $category->parent, $category->cat_ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_pages
 Purpose:   Create scrolling menu of all pages.
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_pages($savedpages, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedpages)) $savedpages = explode(',', $savedpages);
	$pages = get_pages(array('child_of' => $parent, 'parent' => $parent, 'sort_column' => 'ID', 'sort_order' => 'asc'));

	if(!empty($pages)) {
		$output = '';
		if($parent == 0) {
			$output = '<table width="100%">';
			$output .= '<thead><tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			$output .= '<tbody>';
		}
		foreach($pages as $page) {
			if($page->post_parent > 0) {
				if($page->post_parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_pages[]" value="'.$page->ID.'"';
			if(in_array($page->ID, $savedpages)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$indent.$page->post_title.'</td>';
			$output .= '</tr>';
			$output .= adrotate_select_pages($savedpages, $count, $page->post_parent, $page->ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_woo_categories
 Purpose:   Create scrolling menu of all categories.
 Since:		5.10
-------------------------------------------------------------*/
function adrotate_select_woo_categories($savedcats, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedcats)) $savedcats = explode(',', $savedcats);
	$categories = get_categories(array('taxonomy' => 'product_cat', 'child_of' => $parent, 'parent' => $parent,  'orderby' => 'id', 'order' => 'asc', 'hide_empty' => 0));

	if(!empty($categories)) {
		$output = '';
		if($parent == 0) {
			$output .= '<table width="100%">';
			$output .= '<thead><tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			$output .= '<tbody>';
		}
		foreach($categories as $category) {
			if($category->parent > 0) {
				if($category->parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';

			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_woo_categories[]" value="'.$category->cat_ID.'"';
			$output .= (in_array($category->cat_ID, $savedcats)) ? ' checked' : '';
			$output .= '></td><td style="padding: 0px;">'.$indent.$category->name.' ('.$category->category_count.')</td>';

			$output .= '</tr>';
			$output .= adrotate_select_woo_categories($savedcats, $count, $category->parent, $category->cat_ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_bbpress_forums
 Purpose:   Create scrolling menu of all categories.
 Since:		5.10
-------------------------------------------------------------*/
function adrotate_select_bbpress_forums($savedforums, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedforums)) $savedforums = explode(',', $savedforums);
	$forums = get_pages(array('post_type' => 'forum', 'child_of' => $parent, 'parent' => $parent, 'sort_column' => 'ID', 'sort_order' => 'asc'));

	if(!empty($forums)) {
		$output = '';
		if($parent == 0) {
			$output = '<table width="100%">';
			$output .= '<thead><tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			$output .= '<tbody>';
		}
		foreach($forums as $forum) {
			if($forum->post_parent > 0) {
				if($forum->post_parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_bbpress_forums[]" value="'.$forum->ID.'"';
			if(in_array($forum->ID, $savedforums)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$indent.$forum->post_title.'</td>';
			$output .= '</tr>';
			$output .= adrotate_select_bbpress_forums($savedforums, $count, $forum->post_parent, $forum->ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_countries
 Purpose:   List of countries
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_countries() {
	return array(
		// Europe
		'EUROPE' => "Europe",
		'AL' => "Albania",
		'AM' => "Armenia",
		'AD' => "Andorra",
		'AT' => "Austria",
		'AZ' => "Azerbaijan",
		'BY' => "Belarus",
		'BE' => "Belgium",
		'BA' => "Bosnia and Herzegovina",
		'BG' => "Bulgaria",
		'HR' => "Croatia",
		'CY' => "Cyprus",
		'CZ' => "Czech Republic",
		'DK' => "Denmark",
		'EE' => "Estonia",
		'FI' => "Finland",
		'FR' => "France",
		'GE' => "Georgia",
		'DE' => "Germany",
		'GR' => "Greece",
		'HU' => "Hungary",
		'IS' => "Iceland",
		'IE' => "Ireland",
		'IT' => "Italy",
		'LV' => "Latvia",
		'LI' => "Liechtenstein",
		'LT' => "Lithuania",
		'LU' => "Luxembourg",
		'MK' => "Macedonia",
		'MT' => "Malta",
		'MD' => "Moldova",
		'MC' => "Monaco",
		'ME' => "Montenegro",
		'NL' => "the Netherlands",
		'NO' => "Norway",
		'PL' => "Poland",
		'PT' => "Portugal",
		'RO' => "Romania",
		'SM' => "San Marino",
		'RS' => "Serbia",
		'ES' => "Spain",
		'SK' => "Slovakia",
		'SI' => "Slovenia",
		'SE' => "Sweden",
		'CH' => "Switzerland",
		'VA' => "Vatican City",
		'TR' => "Turkey",
		'UA' => "Ukraine",
		'GB' => "United Kingdom",

		// North America
		'NORTHAMERICA' => "North America",
		'AG' => "Antigua and Barbuda",
		'BS' => "Bahamas",
		'BB' => "Barbados",
		'BZ' => "Belize",
		'CA' => "Canada",
		'CR' => "Costa Rica",
		'CU' => "Cuba",
		'DM' => "Dominica",
		'DO' => "Dominican Republic",
		'SV' => "El Salvador",
		'GD' => "Grenada",
		'GT' => "Guatemala",
		'HT' => "Haiti",
		'HN' => "Honduras",
		'JM' => "Jamaica",
		'MX' => "Mexico",
		'NI' => "Nicaragua",
		'PA' => "Panama",
		'KN' => "Saint Kitts and Nevis",
		'LC' => "Saint Lucia",
		'VC' => "Saint Vincent",
		'TT' => "Trinidad and Tobago",
		'US' => "United States",

		// South America
		'SOUTHAMERICA' => "South America",
		'AR' => "Argentina",
		'BO' => "Bolivia",
		'BR' => "Brazil",
		'CL' => "Chile",
		'CO' => "Colombia",
		'EC' => "Ecuador",
		'GY' => "Guyana",
		'PY' => "Paraguay",
		'PE' => "Peru",
		'SR' => "Suriname",
		'UY' => "Uruguay",
		'VE' => "Venezuela",

		// South East Asia + Australia + New Zealand
		'SOUTHEASTASIA' => "Southeast Asia, Australia and New Zealand",
		'AU' => "Australia",
		'BN' => "Brunei",
		'KH' => "Cambodia",
		'TL' => "East Timor (Timor Timur)",
		'ID' => "Indonesia",
		'LA' => "Laos",
		'MY' => "Malaysia",
		'MM' => "Myanmar",
		'NZ' => "New Zealand",
		'PH' => "Philippines",
		'SG' => "Singapore",
		'TH' => "Thailand",
		'VN' => "Vietnam",

		// Misc
		'MISC' => "Rest of the world",
		'AF' => "Afghanistan",
		'DZ' => "Algeria",
		'AO' => "Angola",
		'BH' => "Bahrain",
		'BD' => "Bangladesh",
		'BJ' => "Benin",
		'BT' => "Bhutan",
		'BF' => "Burkina Faso",
		'BI' => "Burundi",
		'CM' => "Cameroon",
		'CV' => "Cape Verde",
		'CF' => "Central African Republic",
		'TD' => "Chad",
		'CN' => "China",
		'KM' => "Comoros",
		'CG' => "Congo (Brazzaville)",
		'CD' => "Congo",
		'CI' => "Cote d'Ivoire",
		'DJ' => "Djibouti",
		'EG' => "Egypt",
		'GQ' => "Equatorial Guinea",
		'ER' => "Eritrea",
		'ET' => "Ethiopia",
		'FJ' => "Fiji",
		'GA' => "Gabon",
		'GM' => "Gambia",
		'GH' => "Ghana",
		'GN' => "Guinea",
		'GW' => "Guinea-Bissau",
		'IN' => "India",
		'IR' => "Iran",
		'IQ' => "Iraq",
		'IL' => "Israel",
		'JP' => "Japan",
		'JO' => "Jordan",
		'KZ' => "Kazakhstan",
		'KE' => "Kenya",
		'KI' => "Kiribati",
		'KP' => "north Korea",
		'KR' => "south Korea",
		'KW' => "Kuwait",
		'KG' => "Kyrgyzstan",
		'LV' => "Latvia",
		'LB' => "Lebanon",
		'LS' => "Lesotho",
		'LR' => "Liberia",
		'LY' => "Libya",
		'MG' => "Madagascar",
		'MW' => "Malawi",
		'MV' => "Maldives",
		'MN' => "Mongolia",
		'ML' => "Mali",
		'MH' => "Marshall Islands",
		'MR' => "Mauritania",
		'MU' => "Mauritius",
		'FM' => "Micronesia",
		'MA' => "Morocco",
		'MZ' => "Mozambique",
		'NA' => "Namibia",
		'NR' => "Nauru",
		'NP' => "Nepal",
		'NE' => "Niger",
		'NG' => "Nigeria",
		'OM' => "Oman",
		'PK' => "Pakistan",
		'PW' => "Palau",
		'PG' => "Papua New Guinea",
		'QA' => "Qatar",
		'RU' => "Russia",
		'RW' => "Rwanda",
		'WS' => "Samoa",
		'ST' => "Sao Tome and Principe",
		'SA' => "Saudi Arabia",
		'SN' => "Senegal",
		'SC' => "Seychelles",
		'SL' => "Sierra Leone",
		'SB' => "Solomon Islands",
		'SO' => "Somalia",
		'ZA' => "South Africa",
		'LK' => "Sri Lanka",
		'SY' => "Syria",
		'SD' => "Sudan",
		'SZ' => "Swaziland",
		'TW' => "Taiwan",
		'TJ' => "Tajikistan",
		'TO' => "Tonga",
		'TM' => "Turkmenistan",
		'TV' => "Tuvalu",
		'TZ' => "Tanzania",
		'TG' => "Togo",
		'TN' => "Tunisia",
		'UG' => "Uganda",
		'AE' => "United Arab Emirates",
		'UZ' => "Uzbekistan",
		'VU' => "Vanuatu",
		'YE' => "Yemen",
		'ZM' => "Zambia",
		'ZW' => "Zimbabwe",

		// Misc
		'SPEC' => "Special Territories and Regions",
		'AW' => "Aruba",
		'KY' => "Cayman Islands",
		'CW' => "Curacao",
		'AW' => "Guam",
		'HK' => "Hong Kong",
		'MO' => "Macao",
		'MQ' => "Martinique",
		'PR' => "Puerto Rico",
		'AW' => "Sint Maarten",
		'VA' => "Holy See (Vatican City)"
	);
}

/*-------------------------------------------------------------
 Name:      adrotate_select_countries
 Purpose:   Create scrolling menu of all countries.
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_select_countries($savedcountries) {
	if(!is_array($savedcountries)) $savedcountries = array();
	$countries = adrotate_countries();

	$output = '<table width="100%">';
	$output .= '<thead>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">(De)select all</td></tr>';
	$output .= '</thead>';

	$output .= '<tbody>';
	$output .= '<tr><td colspan="2" style="padding: 0px;"><em>--- Regions ---</em></td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_westeurope" value="1" /></td><td style="padding: 0px;">West/Central Europe</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_easteurope" value="1" /></td><td style="padding: 0px;">East/Central Europe</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_northamerica" value="1" /></td><td style="padding: 0px;">North America</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_southamerica" value="1" /></td><td style="padding: 0px;">South America</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_southeastasia" value="1" /></td><td style="padding: 0px;">Southeast Asia, Australia and New Zealand</td></tr>';
	foreach($countries as $k => $v) {
		$output .= '<tr>';
		if(strlen($k) > 2) {
			$output .= '<td colspan="2" style="padding: 0px;"><em>--- '.$v.' ---</em></td>';
		} else {
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_countries[]"  value="'.$k.'"';
			$output .= (in_array($k, $savedcountries)) ? ' checked' : '';
			$output .= '></td><td style="padding: 0px;">'.$v.'</td>';
		}
		$output .= '</tr>';
	}
	$output .= '</tbody></table>';
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ads
 Purpose:   Initiate evaluations for errors and determine the ad status
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_evaluate_ads() {
	global $wpdb;

	// Fetch ads
	$ads = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` != 'disabled' AND `type` != 'generator' AND `type` != 'a_empty' AND `type` != 'a_error' AND `type` != 'queue' AND `type` != 'reject' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'empty' ORDER BY `id` ASC;");

	// Determine error states
	$error = $limit = $expired = $expiressoon = $expiresweek = $normal = $unknown = 0;
	foreach($ads as $ad) {
		$result = adrotate_evaluate_ad($ad->id);
		if($result == 'error') {
			$error++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'error' WHERE `id` = {$ad->id};");
		}

		if($result == 'limit') {
			$limit++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'limit' WHERE `id` = {$ad->id};");
		}

		if($result == 'expired') {
			$expired++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'expired' WHERE `id` = {$ad->id};");
		}

		if($result == '2days') {
			$expiressoon++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = '2days' WHERE `id` = {$ad->id};");
		}

		if($result == '7days') {
			$expiresweek++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = '7days' WHERE `id` = {$ad->id};");
		}

		if($result == 'active') {
			$normal++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'active' WHERE `id` = {$ad->id};");
		}

		if($result == 'unknown') {
			$unknown++;
		}
		unset($ad);
	}

	$result = array('error' => $error, 'limit' => $limit, 'expired' => $expired, 'expiressoon' => $expiressoon, 'expiresweek' => $expiresweek, 'normal' => $normal, 'unknown' => $unknown);
	update_option('adrotate_advert_status', $result);
	unset($ads, $result);
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_evaluate_ads
 Purpose:   Initiate automated evaluations for errors and determine the ad status and return to a dashboard
 Since:		3.8.7.1
-------------------------------------------------------------*/
function adrotate_prepare_evaluate_ads() {
	// Verify all ads
	adrotate_evaluate_ads();

	adrotate_return('adrotate-settings', 405, array('tab' => 'maintenance'));
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ad
 Purpose:   Evaluates ads for errors
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_evaluate_ad($ad_id) {
	global $wpdb, $adrotate_config;

	$now = current_time('timestamp');
	$in2days = $now + 172800;
	$in7days = $now + 604800;

	// Fetch ad and its data
	$ad = $wpdb->get_row($wpdb->prepare("SELECT `id`, `bannercode`, `imagetype`, `image`, `tracker`, `desktop`, `mobile`, `tablet`, `budget`, `os_ios`, `os_android`, `os_other`,`crate`, `irate`, `state_req`, `cities`, `states` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $ad_id));

	$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	$has_groups = $wpdb->get_var("SELECT COUNT(`group`) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `schedule` = 0 AND `user` = 0;");
	$has_schedules = $wpdb->get_var("SELECT COUNT(`schedule`) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `group` = 0 AND `user` = 0;");
	$has_advertiser = $wpdb->get_var("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad->id} AND `group` = 0 AND `user` > 0 AND `schedule` = 0;");

	$bannercode = stripslashes(htmlspecialchars_decode($ad->bannercode, ENT_QUOTES));
	$cities = unserialize($ad->cities);
	$states = unserialize($ad->states);

	// Limits exceeded?
	$temp_clicks = $temp_impressions = $temp_max_clicks = $temp_max_impressions = 0;
	$cachekey = "adrotate_schedule_".$ad_id;
	$schedules = wp_cache_get($cachekey);
	if(false === $schedules) {
		$schedules = $wpdb->get_results("SELECT `{$wpdb->prefix}adrotate_schedule`.* FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` AND `ad` = {$ad->id} ORDER BY `starttime` ASC;");
		wp_cache_set($cachekey, $schedules, '', 300);
	}

	foreach($schedules as $schedule) {
		if($now >= $schedule->starttime AND $now <= $schedule->stoptime) {
			$stat = adrotate_stats($ad_id, false, $schedule->starttime, $schedule->stoptime);
			if(!is_array($stat)) $stat = array('clicks' => 0, 'impressions' => 0);

			if($stat['clicks'] > $temp_clicks) $temp_clicks = $stat['clicks'];
			if($stat['impressions'] > $temp_impressions) $temp_impressions = $stat['impressions'];
			$temp_max_clicks = $schedule->maxclicks;
			$temp_max_impressions = $schedule->maximpressions;

			unset($stat);
		}
		unset($schedule);
	}

	// Determine error states
	if(
		strlen($bannercode) < 1 // AdCode empty
		OR ($ad->tracker == 'N' AND $has_advertiser > 0) // Didn't enable stats, DID set a advertiser
		OR (preg_match_all("/(%asset%)/i", $bannercode, $things) AND $ad->image == '' AND $ad->imagetype == '') // Did use %asset% but didn't select an image
		OR (!preg_match_all("/(%asset%)/i", $bannercode, $things) AND $ad->image != '' AND $ad->imagetype != '') // Didn't use %asset% but selected an image
		OR (($ad->image == '' AND $ad->imagetype != '') OR ($ad->image != '' AND $ad->imagetype == '')) // Image and Imagetype mismatch
		OR ($has_advertiser > 0 AND ($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget < 1) // Has advertiser and ran out of budget
		OR $has_schedules == 0 // No Schedules for this ad
		OR ((!preg_match_all('/<(a)[^>](.*?)>/i', $bannercode, $things) OR preg_match_all('/<(ins|script|embed|iframe)[^>](.*?)>/i', $bannercode, $things)) AND ($ad->tracker == 'Y' OR $ad->tracker == 'C')) // Clicks active but no valid link/tag present
		OR ($ad->tracker == 'N' AND ($ad->crate > 0 OR $ad->irate > 0))	// Stats inactive but set a Click|Impression rate
		OR ($has_groups == 0 AND ($ad->desktop == "N" OR $ad->mobile == "N" OR $ad->tablet == "N")) // No groups but has devices (de)selected
		OR ($has_groups > 0 AND ($ad->desktop == "N" AND $ad->mobile == "N" AND $ad->tablet == "N")) // Has group but no devices selected
		OR ($has_groups > 0 AND $ad->os_ios == "N" AND $ad->os_other == "N" AND $ad->os_android == "N") // Has group but no OS selected
		OR ($ad->state_req == "Y" AND count($cities) == 0 AND count($states) == 0) // Geo Targeting, no cities and states
		OR ($ad->state_req == "Y" AND count($cities) > 0 AND count($states) == 0) // Geo Targeting, no cities
		OR ($ad->state_req == "Y" AND count($cities) == 0 AND count($states) > 0) // Geo Targeting, no states
	) {
		return 'error';
	} else if(
		($temp_clicks > $temp_max_clicks AND $temp_max_clicks > 0) // Click limit reached?
		OR ($temp_impressions > $temp_max_impressions AND $temp_max_impressions > 0) // Impression limit reached?
		OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget <= 0) // Ad ran out of money
	){
		unset($temp_clicks, $temp_impressions, $temp_max_clicks, $temp_max_impressions);

		return 'limit';
	} else if(
		$stoptime <= $now // Past the enddate
	){
		return 'expired';
	} else if(
		$stoptime <= $in2days AND $stoptime >= $now	// Expires in 2 days
	){
		return '2days';
	} else if(
		$stoptime <= $in7days AND $stoptime >= $now	// Expires in 7 days
	){
		return '7days';
	} else {
		return 'active';
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_color
 Purpose:   Check if ads are expired and set a color for its end date
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_color($enddate) {
	$now = current_time('timestamp');
	$in2days = $now + 172800;
	$in7days = $now + 604800;

	if($enddate <= $now) {
		return '#CC2900'; // red
	} else if($enddate <= $in2days AND $enddate >= $now) {
		return '#F90'; // orange
	} else if($enddate <= $in7days AND $enddate >= $now) {
		return '#E6B800'; // yellow
	} else {
		return '#009900'; // green
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_is_in_groups
 Purpose:   Build list of groups the ad is in (overview)
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_ad_is_in_groups($id) {
	global $wpdb;

	$output = '';
	$groups	= $wpdb->get_results("
		SELECT
			`{$wpdb->prefix}adrotate_groups`.`name`
		FROM
			`{$wpdb->prefix}adrotate_groups`,
			`{$wpdb->prefix}adrotate_linkmeta`
		WHERE
			`{$wpdb->prefix}adrotate_linkmeta`.`ad` = '".$id."'
			AND `{$wpdb->prefix}adrotate_linkmeta`.`group` = `{$wpdb->prefix}adrotate_groups`.`id`
			AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0
		;");
	if($groups) {
		foreach($groups as $group) {
			$output .= $group->name.", ";
		}
	}
	$output = rtrim($output, ", ");

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_hash
 Purpose:   Generate the adverts clicktracking hash
 Since:		3.9.12
-------------------------------------------------------------*/
function adrotate_hash($ad, $group = 0, $blog_id = 0) {
	global $adrotate_config;

	$timer = $adrotate_config['impression_timer'];
	return base64_encode("$ad,$group,$blog_id,$timer");
}

/*-------------------------------------------------------------
 Name:      adrotate_get_remote_ip
 Purpose:   Get the remote IP from the visitor
 Since:		3.6.2
-------------------------------------------------------------*/
function adrotate_get_remote_ip(){
	if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$remote_ip = $_SERVER["REMOTE_ADDR"];
	} else {
		$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	$buffer = explode(',', $remote_ip, 2);

	return $buffer[0];
}

/*-------------------------------------------------------------
 Name:      adrotate_get_useragent
 Purpose:   Get the useragent from the visitor
 Since:		3.18.3
-------------------------------------------------------------*/
function adrotate_get_useragent(){
	if(isset($_SERVER['HTTP_USER_AGENT'])) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$useragent = trim($useragent, ' \t\r\n\0\x0B');
	} else {
		$useragent = '';
	}

	return $useragent;
}

/*-------------------------------------------------------------
 Name:      adrotate_apply_jetpack_photon
 Purpose:   Use Jetpack Photon if possible
 Since:		4.11
-------------------------------------------------------------*/
function adrotate_apply_jetpack_photon($image) {
	if(class_exists('Jetpack_Photon') AND Jetpack::is_module_active('photon') AND function_exists('jetpack_photon_url')) {
		return jetpack_photon_url($image);
	} else {
		return $image;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_sanitize_file_name
 Purpose:   Clean up file names of files that are being uploaded.
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_sanitize_file_name($filename) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = strtolower(trim($filename, '.-_'));
    return $filename;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_sorted_roles
 Purpose:   Returns all roles and capabilities, sorted by user level. Lowest to highest.
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_get_sorted_roles() {
	global $wp_roles;

	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	$sorted = array();

	foreach($editable_roles as $role => $details) {
		$sorted[$details['name']] = get_role($role);
	}

	$sorted = array_reverse($sorted);

	return $sorted;
}

/*-------------------------------------------------------------
 Name:      adrotate_set_capability
 Purpose:   Grant or revoke capabilities to a role and all higher roles
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_set_capability($lowest_role, $capability){
	$check_order = adrotate_get_sorted_roles();
	$add_capability = false;

	foreach($check_order as $role) {
		if($lowest_role == $role->name) $add_capability = true;
		if(empty($role)) continue;
		$add_capability ? $role->add_cap($capability) : $role->remove_cap($capability) ;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_remove_capability
 Purpose:   Remove the $capability from the all roles
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_remove_capability($capability){
	$check_order = adrotate_get_sorted_roles();

	foreach($check_order as $role) {
		$role = get_role($role->name);
		$role->remove_cap($capability);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications
 Purpose:   Contact admins/moderators about various things
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_notifications($action = false, $adid = false) {
	global $wpdb, $adrotate_config;

	$notifications = get_option("adrotate_notifications");
	$advert_status = get_option('adrotate_advert_status');

	$title = '';
	$message = array();
	$test = (isset($_POST['adrotate_notification_test_submit'])) ? true : false;

	if($test) {
		$title = __('Test notification', 'adrotate-pro');
		$message[] = __('This is a test notification from AdRotate Professional.', 'adrotate-pro');
		$message[] = __('Have a nice day!', 'adrotate-pro');
	} else {
		// Advert status
		if($notifications['notification_mail_status'] == 'Y') {
			$title = __('Status update', 'adrotate-pro');
			if($advert_status['error'] > 0) $message[] = $advert_status['error']." ".__('advert(s) with errors!', 'adrotate-pro');
			if($advert_status['expired'] > 0) $message[] = $advert_status['expired']." ".__('advert(s) expired!', 'adrotate-pro');
			if($advert_status['expiressoon'] > 0) $message[] = $advert_status['expiressoon']." ".__('advert(s) will expire in less than 2 days.', 'adrotate-pro');
			if($advert_status['expiresweek'] > 0) $message[] = $advert_status['expiresweek']." ".__('advert(s) will expire in less than a week.', 'adrotate-pro');
			if($advert_status['unknown'] > 0) $message[] = $advert_status['unknown']." ".__('advert(s) have an unknown status.', 'adrotate-pro');
		}

		// Geo Targeting
		if($notifications['notification_mail_geo'] == 'Y') {
			$geo_lookups = get_option('adrotate_geo_requests');
			if($adrotate_config['enable_geo'] > 2 AND $adrotate_config['enable_geo'] < 6 AND $geo_lookups < 1000) {
				$title = __('Geo targeting', 'adrotate-pro');
				if($geo_lookups > 0) $message[] = __('Your website has less than 1000 lookups left for Geo Targeting. If you run out of lookups, Geo Targeting will stop working.', 'adrotate-pro');
				if($geo_lookups < 1) $message[] = __('Your website has no lookups for Geo Targeting. Geo Targeting is currently not working.', 'adrotate-pro');
			}
		}

		// User (Advertiser) invoked actions (not on a timer)
		if($notifications['notification_mail_queue'] == 'Y') {
			if($action == 'queued') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");
				$queued = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'queue' OR `type` = 'reject';");

				$title = __('Moderation queue', 'adrotate-pro');
				$message[] = __('An advertiser has just queued one of their adverts.', 'adrotate-pro');
				$message[] = "Name '".$name."' (ID: ".$adid.")";
				$message[] = "Awaiting moderation: ".$queued." adverts.";
			}
		}

		if($notifications['notification_mail_approved'] == 'Y') {
			if($action == 'approved') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");

				$title = __('Advert approved', 'adrotate-pro');
				$message[] = __('A moderator has just approved an advert;', 'adrotate-pro');
				$message[] = $name." (ID: ".$adid.")";
			}
		}

		if($notifications['notification_mail_rejected'] == 'Y') {
			if($action == 'rejected') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");

				$title = __('Advert rejected', 'adrotate-pro');
				$message[] = __('A moderator has just rejected advert;', 'adrotate-pro');
				$message[] = $name." (ID: ".$adid.")";
			}
		}
	}

	// Maybe send some alerts (Test or real)
	if(count($message) > 0) {
		if($notifications['notification_email'] == 'Y') {
			adrotate_mail_notifications($notifications['notification_email_publisher'], $title, $message);
			adrotate_return('adrotate-settings', 407, array('tab' => 'notifications'));
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_notifications
 Purpose:   Send emails to appointed recipients
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_mail_notifications($emails, $title, $messages) {
	$messages = implode("\n", $messages);

	$blogname = get_option('blogname');
	$dashboardurl = get_option('siteurl')."/wp-admin/admin.php?page=adrotate";
	$pluginurl = "https://ajdg.solutions/product-category/adrotate-pro/";

	$subject = '[AdRotate Alert] '.$title;

	$message = "<p>".__('Hello', 'adrotate-pro').",</p>";
	$message .= "<p>".__('This notification is sent to you from your website', 'adrotate-pro')." '$blogname'.<br />";
	$message .= "<p>".$messages."</p>";
	$message .= "<p>".__('Access your dashboard here:', 'adrotate-pro')." $dashboardurl<br />";
	$message .= __('Have a nice day!', 'adrotate-pro')."</p>";
	$message .= "<p>".__('Your AdRotate Notifier', 'adrotate-pro')."<br />";
	$message .= "$pluginurl</p>";

	$x = count($emails);
	for($i=0;$i<$x;$i++) {
	    $headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: AdRotate Plugin <".$emails[$i].">" . "\r\n";
		wp_mail($emails[$i], $subject, $message, $headers);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_advertiser
 Purpose:   Email a selected advertiser about his account/adverts/whatever
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_mail_advertiser() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_advertiser')) {
		$author = $_POST['adrotate_username'];
		$useremail = $_POST['adrotate_email'];
		$subject = strip_tags(stripslashes(trim($_POST['adrotate_subject'], "\t\n ")));
		$advert_id	= trim($_POST['adrotate_advert'], "\t\n ");
		$text = strip_tags(stripslashes(trim($_POST['adrotate_message'], "\t\n ")));

		$advert = $wpdb->get_row("SELECT `id`, `title`, `type` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$advert_id};");

		if(strlen($subject) < 1) $subject = "Publisher notification";
		if(strlen($text) < 1) $text = "No message given";

		$sitename = strtolower($_SERVER['SERVER_NAME']);
        if(substr($sitename, 0, 4) == 'www.') $sitename = substr($sitename, 4);

		$siteurl = get_option('siteurl');
		$adurl = $siteurl."/wp-admin/admin.php?page=adrotate-advertiser&view=edit&ad=".$advert->id;

	    $headers = "Content-Type: text/html; charset=UTF-8\r\n"."From: AdRotate Pro <wordpress@$sitename>\r\n";

		$subject = __('[AdRotate]', 'adrotate-pro').' '.$subject;

		$message = "<p>Hello $author,</p>";
		if($advert->id > 0) $message .= "<p>About: ".$advert->id." - ".$advert->title." (".$advert->type.")</p>";
		$message .= "<p>$text</p>";
		$message .= "<p>".__('You can reply to this message by clicking reply in your email client.', 'adrotate-pro')."</p>";
		$message .= "<p>".__('Have a nice day!', 'adrotate-pro')."<br />";
		$message .= __('Your AdRotate Notifier', 'adrotate-pro')."</p>";

		wp_mail($useremail, $subject, $message, $headers);

		adrotate_return('adrotate-advertisers', 223);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_scripts
 Purpose:   Load file uploaded popup
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_scripts() {
	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
    if(strpos($page, 'adrotate') !== false) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('raphael', plugins_url('/library/raphael-min.js', __FILE__), array('jquery'), ADROTATE_VERSION);
		wp_enqueue_script('elycharts', plugins_url('/library/elycharts.min.js', __FILE__), array('jquery', 'raphael'), ADROTATE_VERSION);
		wp_enqueue_script('textatcursor', plugins_url('/library/textatcursor.js', __FILE__), ADROTATE_VERSION);
		wp_enqueue_script('goosebox', plugins_url('/library/goosebox.js', __FILE__), ADROTATE_VERSION);
		wp_enqueue_script('tablesorter', plugins_url('/library/jquery.tablesorter.min.js', __FILE__), array('jquery'), ADROTATE_VERSION);
		wp_enqueue_script('adrotate-tablesorter', plugins_url('/library/jquery.adrotate.tablesorter.js', __FILE__), array('tablesorter'), ADROTATE_VERSION);
		wp_enqueue_script('adrotate-datepicker', plugins_url('/library/jquery.adrotate.datepicker.js', __FILE__), array('jquery'), ADROTATE_VERSION);
	}

	// WP Pointers
	$seen_it = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	if(!in_array('adrotate_pro', $seen_it)) {
		wp_enqueue_script('wp-pointer');
		add_action('admin_print_footer_scripts', 'adrotate_welcome_pointer');
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_styles
 Purpose:   Load file uploaded popup
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_styles() {
	// Keep global for notifications
	wp_enqueue_style('adrotate-admin-stylesheet', plugins_url('library/dashboard.css', __FILE__));

	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
    if(strpos($page, 'adrotate') !== false) {
		wp_enqueue_style('jquery-ui', plugins_url('library/datepicker.css', __FILE__));
	}

	// WP Pointers
	$seen_it = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	if(!in_array('adrotate_pro', $seen_it)) {
		wp_enqueue_style('wp-pointer');
    }
}


/*-------------------------------------------------------------
 Name:      adrotate_dropdown_folder_contents
 Purpose:   List folder contents for dropdown menu
 Since:		5.6
-------------------------------------------------------------*/
function adrotate_dropdown_folder_contents($base_dir, $extensions = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'svg', 'html', 'htm', 'js'), $max_level = 1, $level = 0, $parent = '') {
	$index = array();

	// List the folders and files
	foreach(scandir($base_dir) as $file) {
		if($file == '.' || $file == '..' || $file == '.DS_Store' || $file == 'index.php') continue;

		$dir = $base_dir.'/'.$file;
		if(is_dir($dir)) {
			if($level >= $max_level) continue;
			$index[]= adrotate_dropdown_folder_contents($dir, array('jpg', 'webp', 'svg', 'html', 'htm'), $max_level, $level+1, $file);
		} else {
			$fileinfo = pathinfo($file);
			if(array_key_exists('extension', $fileinfo)) {
				if(in_array($fileinfo['extension'], $extensions)) {
					if($level > 0) $file = $parent.'/'.$file;
					$index[]= $file;
				}
			}
		}
	}
	unset($file);

	// Clean up and sort ascending
	$items = array();
	foreach($index as $key => $item) {
		if(is_array($item)) {
			unset($index[$key]);
			if(count($item) > 0) {
				foreach($item as $k => $v) {
					$index[] = $v;
				}
				unset($k, $v);
			}
		}
	}
	unset($key, $item);
	sort($index);

	return $index;
}

/*-------------------------------------------------------------
 Name:      adrotate_mediapage_folder_contents
 Purpose:   List sub-folder contents for media manager
 Since:		4.9
-------------------------------------------------------------*/
function adrotate_mediapage_folder_contents($asset_folder, $extensions = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'svg', 'html', 'htm', 'js'), $level = 1) {
	$index = $assets = array();

	// Read Banner folder
	if($handle = opendir($asset_folder)) {
	    while(false !== ($file = readdir($handle))) {
	        if($file != "." AND $file != ".." AND $file != "index.php" AND $file != ".DS_Store") {
	            $assets[] = $file;
	        }
	    }
	    closedir($handle);

	    if(count($assets) > 0) {
			$new_level = $level + 1;
//			$extensions = array('jpg', 'jpeg', 'gif', 'png', 'svg', 'swf', 'flv', 'html', 'htm', 'js');

			foreach($assets as $key => $asset) {
				$fileinfo = pathinfo($asset);
				unset($fileinfo['dirname']);
				if(is_dir($asset_folder.'/'.$asset)) { // Read subfolder
					if($level <= 2) { // Not to deep
						$fileinfo['contents'] = adrotate_mediapage_folder_contents($asset_folder.'/'.$asset, $extensions, $new_level);
						$index[] = $fileinfo;
					}
				} else { // It's a file
					if(array_key_exists('extension', $fileinfo)) {
						if(in_array($fileinfo['extension'], $extensions)) {
							$index[] = $fileinfo;
						}
					}
				}
				unset($fileinfo);
			}
			unset($level, $new_level);
		}
	}

	return $index;
}

/*-------------------------------------------------------------
 Name:      adrotate_clean_folder_contents
 Purpose:   Delete unwanted advert assets after uploading a zip file
 Since:		5.8.7
-------------------------------------------------------------*/
function adrotate_clean_folder_contents($asset_folder) {
	$index = $assets = array();

	// Read asset folder
	if($handle = opendir($asset_folder)) {
		$extensions = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'svg', 'html', 'htm', 'js');

	    while(false !== ($asset = readdir($handle))) {
	        if($asset != "." AND $asset != "..") {
				$fileinfo = pathinfo($asset);
				unset($fileinfo['dirname']);
				if(is_dir($asset_folder.'/'.$asset)) { // Read subfolder
					adrotate_clean_folder_contents($asset_folder.'/'.$asset);
					if(count(scandir($asset_folder.'/'.$asset)) == 2) { // Remove empty folder
						adrotate_unlink($asset, $asset_folder);
					}
				} else { // It's a file
					if(array_key_exists('extension', $fileinfo)) {
						if(!in_array($fileinfo['extension'], $extensions)) {
							adrotate_unlink($asset, $asset_folder);
						}
					}
				}
				unset($fileinfo);
	        }
	    }
	    closedir($handle);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_unlink
 Purpose:   Delete a file or folder from the banners folder
 Since:		4.9
-------------------------------------------------------------*/
function adrotate_unlink($asset, $path = '') {
	global $adrotate_config;

	if(!empty($asset)) {
		$access_type = get_filesystem_method();
		if($access_type === 'direct') {
			if($path == "banners") {
				$path = WP_CONTENT_DIR."/".$adrotate_config['banner_folder']."/".$asset;
			} else if($path == "reports") {
				$path = WP_CONTENT_DIR."/reports/".$asset;
			} else {
				$path = $path.'/'.$asset;
			}

			$credentials = request_filesystem_credentials(site_url().'/wp-admin/', '', false, false, array());

			if(!WP_Filesystem($credentials)) {
				return false;
			}

			global $wp_filesystem;

			if(!is_dir($path)) { // It's a file
				if(@unlink($path)) {
					return true;
				} else {
					return false;
				}
			} else { // It's a folder
				if($wp_filesystem->rmdir($path, true)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_return
 Purpose:   Internal redirects
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_return($page, $status, $args = null) {
	if(strlen($page) > 0 AND ($status > 0 AND $status < 1000)) {
		$defaults = array(
			'status' => $status
		);
		$arguments = wp_parse_args($args, $defaults);
		$redirect = 'admin.php?page=' . $page . '&'.http_build_query($arguments);
	} else {
		$redirect = 'admin.php?page=adrotate&status=1'; // Unexpected error
	}

	wp_redirect($redirect);
}

/*-------------------------------------------------------------
 Name:      adrotate_status
 Purpose:   Internal redirects
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_status($status, $args = null) {

	$defaults = array(
		'ad' => '',
		'group' => '',
		'file' => '',
		'error' => ''
	);
	$arguments = wp_parse_args($args, $defaults);

	switch($status) {
		// Management messages
		case '200' :
			echo '<div id="message" class="updated"><p>'. __('Ad saved', 'adrotate-pro') .'</p></div>';
		break;

		case '201' :
			echo '<div id="message" class="updated"><p>'. __('Group saved', 'adrotate-pro') .'</p></div>';
		break;

		case '202' :
			echo '<div id="message" class="updated"><p>'. __('Banner image saved', 'adrotate-pro') .'</p></div>';
		break;

		case '203' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '204' :
			echo '<div id="message" class="updated"><p>'. __('Group deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '205' :
			echo '<div id="message" class="updated"><p>'. __('Advertiser updated', 'adrotate-pro') .'</p></div>';
		break;

		case '206' :
			echo '<div id="message" class="updated"><p>'. __('File/folder deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '208' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) statistics reset', 'adrotate-pro') .'</p></div>';
		break;

		case '209' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) renewed', 'adrotate-pro') .'</p></div>';
		break;

		case '210' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deactivated', 'adrotate-pro') .'</p></div>';
		break;

		case '211' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) activated', 'adrotate-pro') .'</p></div>';
		break;

		case '212' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate-pro') .'</p></div>';
		break;

		case '213' :
			echo '<div id="message" class="updated"><p>'. __('Group including it\'s Ads deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '214' :
			echo '<div id="message" class="updated"><p>'. __('Weight changed', 'adrotate-pro') .'</p></div>';
		break;

		case '215' :
			echo '<div id="message" class="updated"><p>'. __('Export created', 'adrotate-pro') .'. <a href="' . WP_CONTENT_URL . '/reports/'.$arguments['file'].'">Download</a>.</p></div>';
		break;

		case '216' :
			echo '<div id="message" class="updated"><p>'. __('Adverts imported', 'adrotate-pro') .'</div>';
		break;

		case '217' :
			echo '<div id="message" class="updated"><p>'. __('Schedule saved', 'adrotate-pro') .'</div>';
		break;

		case '218' :
			echo '<div id="message" class="updated"><p>'. __('Schedule(s) deleted', 'adrotate-pro') .'</div>';
		break;

		case '219' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) duplicated', 'adrotate-pro') .'</div>';
		break;

		case '220' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) archived', 'adrotate-pro') .'</div>';
		break;

		case '221' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) moved to the trash', 'adrotate-pro') .'</div>';
		break;

		case '222' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) restored from trash', 'adrotate-pro') .'</div>';
		break;

		case '223' :
			echo '<div id="message" class="updated"><p>'. __('Folder created.', 'adrotate-pro') .'</p></div>';
		break;

		case '226' :
			echo '<div id="message" class="updated"><p>'. __('Advert HTML generated and placed in the AdCode field. Configure your advert below. Do not forget to check all settings and add or select a schedule.', 'adrotate-pro') .'</div>';
		break;

		case '227' :
			echo '<div id="message" class="updated"><p>'. __('Header & ads.txt updated.', 'adrotate-pro') .'</div>';
		break;

		// Advertiser messages
		case '300' :
			echo '<div id="message" class="updated"><p>'. __('Your message has been sent. Someone will be in touch shortly.', 'adrotate-pro') .'</p></div>';
		break;

		case '301' :
			echo '<div id="message" class="updated"><p>'. __('Advert submitted for review', 'adrotate-pro') .'</p></div>';
		break;

		case '302' :
			echo '<div id="message" class="updated"><p>'. __('Advert updated and awaiting review', 'adrotate-pro') .'</p></div>';
		break;

		case '303' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate-pro') .'</p></div>';
		break;

		case '304' :
			echo '<div id="message" class="updated"><p>'. __('Ad approved', 'adrotate-pro') .'</p></div>';
		break;

		case '305' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) rejected', 'adrotate-pro') .'</p></div>';
		break;

		case '306' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) queued', 'adrotate-pro') .'</p></div>';
		break;

		// Settings
		case '400' :
			echo '<div id="message" class="updated"><p>'. __('Settings saved', 'adrotate-pro') .'</p></div>';
		break;

		case '401' :
			echo '<div id="message" class="updated"><p>'. __('If any maintenance tasks were missing they have been scheduled', 'adrotate-pro') .'</p></div>';
		break;

		case '405' :
			echo '<div id="message" class="updated"><p>'. __('Ads evaluated and statuses have been corrected where required', 'adrotate-pro') .'</p></div>';
		break;

		case '407' :
			echo '<div id="message" class="updated"><p>'. __('Test notification sent', 'adrotate-pro') .'</p></div>';
		break;

		case '409' :
			echo '<div id="message" class="updated"><p>'. __('AdRotate tried to create a banners, Reports folder and an ads.txt file', 'adrotate-pro') .'</p></div>';
		break;

		// (all) Error messages
		case '500' :
			echo '<div id="message" class="error"><p>'. __('Action prohibited', 'adrotate-pro') .'</p></div>';
		break;

		case '501' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Review the colored ad.', 'adrotate-pro') .'</p></div>';
		break;

		case '502' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Please contact staff.', 'adrotate-pro') .'</p></div>';
		break;

		case '503' :
			echo '<div id="message" class="error"><p>'. __('No data found in selected time period', 'adrotate-pro') .'</p></div>';
		break;

		case '504' :
			echo '<div id="message" class="error"><p>'. __('Database can only be optimized or cleaned once every hour', 'adrotate-pro') .'</p></div>';
		break;

		case '505' :
			echo '<div id="message" class="error"><p>'. __('Form can not be (partially) empty!', 'adrotate-pro') .'</p></div>';
		break;

		case '506' :
			echo '<div id="message" class="error"><p>'. __('No file uploaded.', 'adrotate-pro') .'</p></div>';
		break;

		case '507' :
			echo '<div id="message" class="error"><p>'. __('The file could not be read.', 'adrotate-pro') .'</p></div>';
		break;

		case '509' :
			echo '<div id="message" class="error"><p>'. __('No ads found.', 'adrotate-pro') .'</p></div>';
		break;

		case '510' :
			echo '<div id="message" class="error"><p>'. __('Wrong file type. No file uploaded.', 'adrotate-pro') .'</p></div>';
		break;

		case '511' :
			echo '<div id="message" class="error"><p>'. __('No file selected or file is too large.', 'adrotate-pro') .'</p></div>';
		break;

		case '512' :
			echo '<div id="message" class="error"><p>'. __('There was an error unzipping the file. Please try again later.', 'adrotate-pro') .'<br />Error: '.$arguments['error'].'</p></div>';
		break;

		case '513' :
			echo '<div id="message" class="error"><p>'. __('The advert hash is not usable or is missing required data. Please copy the hash correctly and try again.', 'adrotate-pro') .'</p></div>';
		break;

		case '514' :
			echo '<div id="message" class="error"><p>'. __('The advert hash can not be used on the same site as it originated from or is not a valid hash for importing.', 'adrotate-pro') .'</p></div>';
		break;

		case '515' :
			echo '<div id="message" class="error"><p>'. __('Folder name is empty or too long, please keep it under 100 characters.', 'adrotate-pro') .'</p></div>';
		break;

		case '516' :
			echo '<div id="message" class="error"><p>'. __('Something went wrong creating the folder, try again.', 'adrotate-pro') .'</p></div>';
		break;

		case '517' :
			echo '<div id="message" class="updated"><p>'. __('Something went wrong deleting the file or folder. Make sure the file exists and that your file permissions are correct.', 'adrotate-pro') .'</p></div>';
		break;

		// Licensing
		case '600' :
			echo '<div id="message" class="error"><p>'. __('Invalid request', 'adrotate-pro') .'</p></div>';
		break;

		case '601' :
			echo '<div id="message" class="error"><p>'. __('No license key or email provided', 'adrotate-pro') .'</p></div>';
		break;

		case '602' :
			echo '<div id="message" class="error"><p>'. __('The request did not get through or the response was invalid. Contact support.', 'adrotate-pro') .'<br />'.$arguments['error'].'</p></div>';
		break;

		case '603' :
			echo '<div id="message" class="error"><p>'. __('The email provided is invalid.', 'adrotate-pro') .'</p></div>';
		break;

		case '604' :
			echo '<div id="message" class="error"><p>'. __('Invalid license key.', 'adrotate-pro') .'</p></div>';
		break;

		case '605' :
			echo '<div id="message" class="error"><p>'. __('The purchase matching this product is not complete. Contact support.', 'adrotate-pro') .'</p></div>';
		break;

		case '606' :
			echo '<div id="message" class="error"><p>'. __('No remaining activations for this license. You can manage your license activations from your account on ajdg.solutions.', 'adrotate-pro') .'</p></div>';
		break;

		case '607' :
			echo '<div id="message" class="error"><p>'. __('Could not (de)activate key. Your license needs to be reset. Please contact support.', 'adrotate-pro') .'</p></div>';
		break;

		case '608' :
			echo '<div id="message" class="updated"><p>'. __('Woohoo! Thank you! Your license is now active. You can now use Premium Support and AdRotate Geo.', 'adrotate-pro') .'<br />'. __('Also you will receive updates and support for one year of purchasing and be notified via email when it is time to renew your license.', 'adrotate-pro') .'</p></div>';
		break;

		case '609' :
			echo '<div id="message" class="updated"><p>'. __('Your license is now de-activated', 'adrotate-pro') .'</p></div>';
		break;

		case '610' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your licenses have been reset', 'adrotate-pro') .'</p></div>';
		break;

		case '611' :
			echo '<div id="message" class="error"><p>'. __('This license can not be activated for networks. Please purchase a Developer license.', 'adrotate-pro') .'</p></div>';
		break;

		// Support
		case '701' :
			echo '<div class="ajdg-notification notice"><div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div><div class="ajdg-notification-message"><strong>Support ticket sent.</strong><br />I will be in touch within one or two business days! Meanwhile, please check out the <a href="https://ajdg.solutions/support/adrotate-manuals/?pk_campaign=adrotatepro&pk_keyword=support_banner" target="_blank">AdRotate manuals</a>.<br /><strong>Please do not send multiple messages with the same question. This will clutter up my inbox and delays my response to you!</strong></div></div>';
		break;

		default :
			echo '<div id="message" class="error"><p>'. __('Unexpected error', 'adrotate-pro') .'</p></div>';
		break;
	}

	unset($arguments, $args);
}
?>
