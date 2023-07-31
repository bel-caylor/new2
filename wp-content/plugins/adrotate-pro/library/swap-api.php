<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2012-2021 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark owned by Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
	AJdG Solutions AdRotate Swap Library for AdRotate Pro
---------------------------------------------------------------
 Changelog:
---------------------------------------------------------------
  1.0 - 2 August 2021
 	* Initial RC
-------------------------------------------------------------*/

function adrotate_swap_activate() {
	if(wp_verify_nonce($_POST['adrotate_nonce_swap'], 'adrotate_swap')) {
		adrotate_swap_response('activate');
		adrotate_return('adrotate-swap', 604);
		exit;
	} else {
		adrotate_nonce_error();
		exit;
	}
}

function adrotate_swap_deactivate() {
	if(wp_verify_nonce($_POST['adrotate_nonce_license'], 'adrotate_license')) {
		adrotate_swap_response('deactivate');
		adrotate_return('adrotate-swap', 600);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

function adrotate_swap_deactivate_uninstall() {
	adrotate_swap_response('deactivation', true);
}

function adrotate_swap_response($action = '', $uninstall = false) {
	$swap = get_option('adrotate_swap');
	$pro = get_option('adrotate_activate');
	$swap['instance'] = (strlen($swap['instance']) < 1) ? $pro['instance'] : $swap['instance'];
	$plugins = get_plugins();
	$plugin_version = $plugins['adrotate-pro/adrotate-pro.php']['Version'];

	$request = array('slug' => "adrotate-pro", 'instance' => $swap['instance'], 'platform' => strtolower(get_option('siteurl')), 'action' => $action, 'et' => microtime(true));

	// if($action == 'activate') {
	// 	$request['campaign'] = array('region' => $region, 'category' => $category); // Specs of new campaign
	// } 
	if($action == 'update') {
		// respond with up-to 5 campaigns for publishing, user can choose 1 or more. edit & publish option in dashboard (kinda like advertiser editing)

		$request['campaign_id'] = $campaign_id; // Array of campaign IDs
	} 
	if($action == 'submit_campaign') {
		$request['campaign'] = array('adcode' => '', 'days' => $days, 'region' => $region, 'category' => $category, 'name' => $name, 'email' => $email, 'thanks' => $note); // Specs of new campaign
	} 
	if($action == 'extend_campaign') {
		$request['campaign_id'] = $campaign_id; // Campaign ID
		$request['days'] = $campaign_end; // Number of days to add
	} 
	if($action == 'cancel_campaign') {
		$request['campaign_id'] = $campaign_id; // Campaign ID
	}
	
	$args = array('headers' => array('Accept' => 'multipart/form-data'), 'body' => array('r' => serialize($request)), 'user-agent' => 'AdRotate Pro/'.$plugin_version.';', 'sslverify' => false, 'timeout' => 5);

	$response = wp_remote_post('https://ajdg.solutions/api/swap/1/', $args);
	
	if($uninstall) return; // If uninstall, skip the rest

    if(!is_wp_error($response)) {
		$data = json_decode($response['body'], 1);

	    if($response['response']['code'] === 200) {

			// Check response
			if(is_array($data)) {
				$data['status'] = (array_key_exists('slug', $data)) ? $data['slug'] : '';
				$data['instance'] = (array_key_exists('name', $data)) ? $data['name'] : '';
				$data['activated'] = (array_key_exists('release_date', $data)) ? $data['release_date'] : '';
				$data['deactivated'] = (array_key_exists('version', $data)) ? $data['version'] : 0;

				// Store response
				update_option('adrotate_swap', $data);
			}

			// Show the good news
			set_transient('ajdg_swap_response', array('code' => $response['response']['code'], 'message' => $response['response']['message'], 'last_checked' => date_i18n('F j, Y, g:i a')), 43190); // Expire in 11:50 hours
		} else {
			// Show the bad news
			set_transient('ajdg_swap_response', array('code' => $data['code'], 'message' => $data['error'], 'last_checked' => date_i18n('F j, Y, g:i a')), 43190); // Expire in 11:50 hours
		}
	} 
}
?>