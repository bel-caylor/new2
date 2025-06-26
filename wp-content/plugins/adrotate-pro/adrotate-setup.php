<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_activate
 Purpose:   Set up AdRotate on your current blog
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_activate($network_wide) {
	if(is_multisite() AND $network_wide) {
		global $wpdb;

		$current_blog = $wpdb->blogid;
 		$blog_ids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs;");

		foreach($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			adrotate_activate_setup();
		}

		switch_to_blog($current_blog);
		return;
	}
	adrotate_activate_setup();
	if(adrotate_is_networked()) add_site_option('adrotate_network_settings', array('primary' => 1, 'site_dashboard' => 'Y'));
}

/*-------------------------------------------------------------
 Name:      adrotate_activate_setup
 Purpose:   Set up AdRotate for first use with default settings and database tables
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_activate_setup() {
	global $wpdb, $userdata;

	if(version_compare(PHP_VERSION, '7.4.0', '<') == -1) {
		deactivate_plugins('/adrotate-pro/adrotate-pro.php');
		wp_die('AdRotate 5.13 and newer requires PHP 7.4 or higher. Your server reports version '.PHP_VERSION.'. Contact your hosting provider about upgrading your server!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to dashboard</a>.');
	} else {
		if(!current_user_can('activate_plugins')) {
			deactivate_plugins('/adrotate-pro/adrotate-pro.php');
			wp_die('You do not have appropriate access to activate this plugin! Contact your administrator!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to dashboard</a>.');
		} else {
			// Set default settings and values
			add_option('adrotate_version', array('current' => ADROTATE_VERSION, 'previous' => ''));
			add_option('adrotate_db_version', array('current' => ADROTATE_DB_VERSION, 'previous' => ''));
			add_option('adrotate_config', array());
			add_option('adrotate_notifications', array());
			add_option('adrotate_crawlers', array());
			add_option('adrotate_advert_status', array('error' => 0, 'expired' => 0, 'expiressoon' => 0, 'expiresweek' => 0, 'normal' => 0, 'unknown' => 0));
			add_option('adrotate_geo_required', 0);
			add_option('adrotate_geo_requests', 0);
			add_option('adrotate_group_css', array());
			add_option('adrotate_header_output', '');
			add_option('adrotate_gam_output', '');
			add_option('adrotate_dynamic_required', 0);
			add_option('adrotate_dynamic_widgets_advert', adrotate_rand(10));
			add_option('adrotate_dynamic_widgets_group', adrotate_rand(10));
			update_option('adrotate_hide_review', current_time('timestamp'));
			update_option('adrotate_hide_birthday', current_time('timestamp'));

			adrotate_database_install();
			adrotate_dummy_data();
			adrotate_check_config();
			adrotate_check_schedules();

			// Set the capabilities for the administrator
			$role = get_role('administrator');
			$role->add_cap("adrotate_advertiser");
			$role->add_cap("adrotate_global_report");
			$role->add_cap("adrotate_ad_manage");
			$role->add_cap("adrotate_ad_delete");
			$role->add_cap("adrotate_group_manage");
			$role->add_cap("adrotate_group_delete");
			$role->add_cap("adrotate_schedule_manage");
			$role->add_cap("adrotate_schedule_delete");
			$role->add_cap("adrotate_moderate");
			$role->add_cap("adrotate_moderate_approve");
			$role->add_cap("adrotate_advertiser_manage");

			// Switch additional roles off
			remove_role('adrotate_advertiser');

			// Attempt to make the some folders
			if(!is_dir(WP_CONTENT_DIR.'/banners')) mkdir(WP_CONTENT_DIR.'/banners', 0755);
			if(!is_dir(WP_CONTENT_DIR.'/reports')) mkdir(WP_CONTENT_DIR.'/reports', 0755);

			// License & updates
			add_option('adrotate_activate', array('status' => 0, 'instance' => md5(strtolower('adrotate-pro'.get_option('siteurl'))), 'activated' => 0, 'deactivated' => 0, 'type' => '', 'key' => '', 'email' => '', 'version' => '', 'created' => current_time('timestamp')));

			// AdRotate Swap
			add_option('adrotate_swap', array('status' => 0, 'instance' => '', 'activated' => 0, 'deactivated' => 0, 'campaign' => array('status' => 0), 'offers' => array(), 'current' => array()));
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_activate_new_blog
 Purpose:   Set up AdRotate for new instance on multisite
 Since:		4.7
-------------------------------------------------------------*/
function adrotate_activate_new_blog($blog_id) {
	if(is_multisite()) {
		global $wpdb;

		$current_blog = $wpdb->blogid;

		switch_to_blog($blog_id);
		adrotate_activate_setup();
		switch_to_blog($current_blog);
		return;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_deactivate
 Purpose:   Deactivate script
 Since:		2.0
-------------------------------------------------------------*/
function adrotate_deactivate($network_wide) {
    if(is_multisite() AND $network_wide) {
	    global $wpdb;

        $current_blog = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs;");

        foreach ($blogids as $blog_id) {
            switch_to_blog($blog_id);
            adrotate_deactivate_setup();
        }
        switch_to_blog($current_blog);
        return;
    }
    adrotate_deactivate_setup();
}

/*-------------------------------------------------------------
 Name:      adrotate_deactivate_setup
 Purpose:   Deactivate script
 Since:		2.0
-------------------------------------------------------------*/
function adrotate_deactivate_setup() {
	global $wp_roles;

	update_option('adrotate_hide_review', current_time('timestamp'));

	// Clean up capabilities from ALL users
	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	foreach($editable_roles as $role => $details) {
		$wp_roles->remove_cap($details['name'], "adrotate_advertiser");
		$wp_roles->remove_cap($details['name'], "adrotate_global_report");
		$wp_roles->remove_cap($details['name'], "adrotate_ad_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_ad_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_group_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_group_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_schedule_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_schedule_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_moderate");
		$wp_roles->remove_cap($details['name'], "adrotate_moderate_approve");
		$wp_roles->remove_cap($details['name'], "adrotate_advertiser_manage");
	}

	// Clean up userroles
	remove_role('adrotate_advertiser');

	// Clean up wp_cron
	wp_clear_scheduled_hook('adrotate_notification');
	wp_clear_scheduled_hook('adrotate_update_check'); // Obsolete
	wp_clear_scheduled_hook('adrotate_empty_trash');
	wp_clear_scheduled_hook('adrotate_auto_delete');
	wp_clear_scheduled_hook('adrotate_evaluate_ads');
	wp_clear_scheduled_hook('adrotate_empty_trackerdata');
}

/*-------------------------------------------------------------
 Name:      adrotatepro_uninstall
 Purpose:   Initiate uninstallation
 Since:		5.8.1
-------------------------------------------------------------*/
function adrotatepro_uninstall($network_wide) {
    if(is_multisite() AND $network_wide) {
	    global $wpdb;

        $current_blog = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs;");

        foreach($blogids as $blog_id) {
            switch_to_blog($blog_id);
			if(!is_plugin_active('adrotate/adrotate.php')) { // Only if AdRotate Banner Manager is not active
				adrotatepro_uninstall_setup();
			}
        }
        switch_to_blog($current_blog);
        return;
    }
    adrotatepro_uninstall_setup();
}

/*-------------------------------------------------------------
 Name:      adrotatepro_uninstall_setup
 Purpose:   Delete the entire AdRotate database and remove the options on uninstall
 Since:		5.8.1
-------------------------------------------------------------*/
function adrotatepro_uninstall_setup() {
	global $wpdb, $wp_roles;

	// Clean up capabilities from ALL users
	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	foreach($editable_roles as $role => $details) {
		$wp_roles->remove_cap($details['name'], "adrotate_advertiser");
		$wp_roles->remove_cap($details['name'], "adrotate_global_report");
		$wp_roles->remove_cap($details['name'], "adrotate_ad_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_ad_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_group_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_group_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_schedule_manage");
		$wp_roles->remove_cap($details['name'], "adrotate_schedule_delete");
		$wp_roles->remove_cap($details['name'], "adrotate_moderate");
		$wp_roles->remove_cap($details['name'], "adrotate_moderate_approve");
		$wp_roles->remove_cap($details['name'], "adrotate_advertiser_manage");
	}

	// Clean up userroles
	remove_role('adrotate_advertiser');

	// Clean up wp_cron
	wp_clear_scheduled_hook('adrotate_notification');
	wp_clear_scheduled_hook('adrotate_update_check'); // Obsolete
	wp_clear_scheduled_hook('adrotate_empty_trash');
	wp_clear_scheduled_hook('adrotate_auto_delete');
	wp_clear_scheduled_hook('adrotate_evaluate_ads');
	wp_clear_scheduled_hook('adrotate_empty_trackerdata');

	// Drop MySQL Tables
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_groups`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_linkmeta`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_stats`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_stats_archive`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_schedule`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");

	// Cleanup user meta
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_is_advertiser';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_notes';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_permissions';");

	// De-activate License & Update stuff
	adrotate_license_deactivate_uninstall();

	// De-activate and unsubscribe from AdRotate Swap
	//adrotate_swap_deactivate_uninstall();

	// Delete Options
	delete_option('adrotate_activate');
	delete_option('adrotate_swap');
	delete_option('adrotate_config');
	delete_option('adrotate_crawlers');
	delete_option('adrotate_version');
	delete_option('adrotate_db_version');
	delete_option('adrotate_geo_required');
	delete_option('adrotate_geo_requests');
	delete_option('adrotate_geo_reset'); // Obsolete since 5.8.9
	delete_option('adrotate_group_css');
	delete_option('adrotate_header_output');
	delete_option('adrotate_gam_output');
	delete_option('adrotate_dynamic_required');
	delete_option('adrotate_dynamic_widgets_advert');
	delete_option('adrotate_dynamic_widgets_group');
	delete_option('adrotate_hide_update');
	delete_option('adrotate_hide_review');
	delete_option('adrotate_hide_license'); // Obsolete in 5.12.2
	delete_option('adrotate_hide_getpro'); // Used in AdRotate Free
	delete_option('adrotate_hide_birthday');
	delete_option('adrotate_advert_status');
	delete_option('adrotate_notifications');
	delete_option('adrotate_dynamic_widgets'); // Obsolete since 5.7
	delete_option('adrotate_db_timer'); // Obsolete since 5.7.3
	delete_option('adrotate_debug'); // Obsolete since 5.8

	delete_site_option('adrotate_network_settings');
}

/*-------------------------------------------------------------
 Name:      adrotate_check_schedules
 Purpose:   Set or reset maintenance schedules for AdRotate
 Since:		3.12.5
-------------------------------------------------------------*/
function adrotate_check_schedules() {
	$firstrun = adrotate_date_start('day');
	if(!wp_next_scheduled('adrotate_notification')) { // Ad notifications
		wp_schedule_event($firstrun + 60, 'daily', 'adrotate_notification');
	}

	if(!wp_next_scheduled('adrotate_empty_trash')) { // Empty the trash
		wp_schedule_event($firstrun + 120, 'daily', 'adrotate_empty_trash');
	}

	if(!wp_next_scheduled('adrotate_auto_delete')) { // Clean adverts and schedules
		wp_schedule_event($firstrun + 180, 'daily', 'adrotate_auto_delete');
	}

	if(!wp_next_scheduled('adrotate_evaluate_ads')) { // Check ads
		wp_schedule_event($firstrun + 240, 'twicedaily', 'adrotate_evaluate_ads');
	}

	if(!wp_next_scheduled('adrotate_empty_trackerdata')) { // Clean trackerdata
		wp_schedule_event($firstrun + 300, 'twicedaily', 'adrotate_empty_trackerdata');
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_check_config
 Purpose:   Default options for AdRotate
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_check_config() {

	$config = get_option('adrotate_config');
	$notifications = get_option('adrotate_notifications');
	$crawlers = get_option('adrotate_crawlers');

	// If empty or no result
	if(!$config) $config = array();
	if(!$notifications) $notifications = array();
	if(!$crawlers) $crawlers = array();

	if(!isset($config['advertiser'])) $config['advertiser'] = 'subscriber';
	if(!isset($config['global_report'])) $config['global_report'] = 'administrator';
	if(!isset($config['ad_manage'])) $config['ad_manage'] = 'administrator';
	if(!isset($config['ad_delete'])) $config['ad_delete'] = 'administrator';
	if(!isset($config['group_manage'])) $config['group_manage'] = 'administrator';
	if(!isset($config['group_delete'])) $config['group_delete'] = 'administrator';
	if(!isset($config['schedule_manage'])) $config['schedule_manage'] = 'administrator';
	if(!isset($config['schedule_delete'])) $config['schedule_delete'] = 'administrator';
	if(!isset($config['advertiser_manage'])) $config['advertiser_manage'] = 'administrator';
	if(!isset($config['moderate'])) $config['moderate'] = 'administrator';
	if(!isset($config['moderate_approve'])) $config['moderate_approve'] = 'administrator';
	if(!isset($config['enable_advertisers']) OR ($config['enable_advertisers'] != 'Y' AND $config['enable_advertisers'] != 'N')) $config['enable_advertisers'] = 'N';
	if(!isset($config['stats']) OR ($config['stats'] < 0 AND $config['stats'] > 5)) $config['stats'] = 1;
	if(!isset($config['enable_admin_stats']) OR ($config['enable_admin_stats'] != 'Y' AND $config['enable_admin_stats'] != 'N')) $config['enable_admin_stats'] = 'Y';
	if(!isset($config['enable_loggedin_impressions']) OR ($config['enable_loggedin_impressions'] != 'Y' AND $config['enable_loggedin_impressions'] != 'N')) $config['enable_loggedin_impressions'] = 'Y';
	if(!isset($config['enable_loggedin_clicks']) OR ($config['enable_loggedin_clicks'] != 'Y' AND $config['enable_loggedin_clicks'] != 'N')) $config['enable_loggedin_clicks'] = 'Y';
	if(!isset($config['enable_geo'])) $config['enable_geo'] = 0;
	if(!isset($config['geo_email'])) $config['geo_email'] = '';
	if(!isset($config['geo_pass'])) $config['geo_pass'] = '';
	if(!isset($config['adblock_disguise'])) $config['adblock_disguise'] = '';
	if(!isset($config['banner_folder'])) $config['banner_folder'] = "banners";
	if(!isset($config['adstxt_file'])) $config['adstxt_file'] = "";
	if(!isset($config['impression_timer']) OR $config['impression_timer'] < 10 OR $config['impression_timer'] > HOUR_IN_SECONDS) $config['impression_timer'] = 60;
	if(!isset($config['click_timer']) OR $config['click_timer'] < 60 OR $config['click_timer'] > DAY_IN_SECONDS) $config['click_timer'] = DAY_IN_SECONDS;
	if(!isset($config['hide_schedules']) OR ($config['hide_schedules'] != 'Y' AND $config['hide_schedules'] != 'N')) $config['hide_schedules'] = 'N';
	if(!isset($config['widgetalign']) OR ($config['widgetalign'] != 'Y' AND $config['widgetalign'] != 'N')) $config['widgetalign'] = 'N';
	if(!isset($config['widgetpadding']) OR ($config['widgetpadding'] != 'Y' AND $config['widgetpadding'] != 'N')) $config['widgetpadding'] = 'N';
	if(!isset($config['w3caching']) OR ($config['w3caching'] != 'Y' AND $config['w3caching'] != 'N')) $config['w3caching'] = 'N';
	if(!isset($config['borlabscache']) OR ($config['borlabscache'] != 'Y' AND $config['borlabscache'] != 'N')) $config['borlabscache'] = 'N';
	if(!isset($config['textwidget_shortcodes']) OR ($config['textwidget_shortcodes'] != 'Y' AND $config['textwidget_shortcodes'] != 'N')) $config['textwidget_shortcodes'] = 'N';
	if(!isset($config['live_preview']) OR ($config['live_preview'] != 'Y' AND $config['live_preview'] != 'N')) $config['live_preview'] = 'Y';
	if(!isset($config['duplicate_adverts_filter']) OR ($config['duplicate_adverts_filter'] != 'Y' AND $config['duplicate_adverts_filter'] != 'N')) $config['duplicate_adverts_filter'] = 'N';
	if(!isset($config['mobile_dynamic_mode']) OR ($config['mobile_dynamic_mode'] != 'Y' AND $config['mobile_dynamic_mode'] != 'N')) $config['mobile_dynamic_mode'] = 'Y';
	if(!isset($config['jquery']) OR ($config['jquery'] != 'Y' AND $config['jquery'] != 'N')) $config['jquery'] = 'N';
	if(!isset($config['jsfooter']) OR ($config['jsfooter'] != 'Y' AND $config['jsfooter'] != 'N')) $config['jsfooter'] = 'Y';
	update_option('adrotate_config', $config);

	if(!isset($notifications['notification_dash']) OR ($notifications['notification_dash'] != 'Y' AND $notifications['notification_dash'] != 'N')) $notifications['notification_dash'] = 'Y';
	if(!isset($notifications['notification_email']) OR ($notifications['notification_email'] != 'Y' AND $notifications['notification_email'] != 'N')) $notifications['notification_email'] = 'N';

	if(!isset($notifications['notification_dash_expired']) OR ($notifications['notification_dash_expired'] != 'Y' AND $notifications['notification_dash_expired'] != 'N')) $notifications['notification_dash_expired'] = 'Y';
	if(!isset($notifications['notification_dash_soon']) OR ($notifications['notification_dash_soon'] != 'Y' AND $notifications['notification_dash_soon'] != 'N')) $notifications['notification_dash_soon'] = 'Y';
	if(!isset($notifications['notification_dash_week']) OR ($notifications['notification_dash_week'] != 'Y' AND $notifications['notification_dash_week'] != 'N')) $notifications['notification_dash_week'] = 'Y';
	if(!isset($notifications['notification_schedules']) OR ($notifications['notification_schedules'] != 'Y' AND $notifications['notification_schedules'] != 'N')) $notifications['notification_schedules'] = 'Y';

	if(!isset($notifications['notification_mail_geo']) OR ($notifications['notification_mail_geo'] != 'Y' AND $notifications['notification_mail_geo'] != 'N')) $notifications['notification_mail_geo'] = 'N';
	if(!isset($notifications['notification_mail_status']) OR ($notifications['notification_mail_status'] != 'Y' AND $notifications['notification_mail_status'] != 'N')) $notifications['notification_mail_status'] = 'Y';
	if(!isset($notifications['notification_mail_queue']) OR ($notifications['notification_mail_queue'] != 'Y' AND $notifications['notification_mail_queue'] != 'N')) $notifications['notification_mail_queue'] = 'N';
	if(!isset($notifications['notification_mail_approved']) OR ($notifications['notification_mail_approved'] != 'Y' AND $notifications['notification_mail_approved'] != 'N')) $notifications['notification_mail_approved'] = 'N';
	if(!isset($notifications['notification_mail_rejected']) OR ($notifications['notification_mail_rejected'] != 'Y' AND $notifications['notification_mail_rejected'] != 'N')) $notifications['notification_mail_rejected'] = 'N';
	if(!isset($notifications['notification_email_publisher'])) $notifications['notification_email_publisher'] = array(get_option('admin_email'));
	update_option('adrotate_notifications', $notifications);

	if(!isset($crawlers) OR count($crawlers) < 1) $crawlers = array("008", "bot", "crawler", "spider", "Accoona-AI-Agent", "alexa", "Arachmo", "B-l-i-t-z-B-O-T", "boitho.com-dc", "Cerberian Drtrs","Charlotte", "cosmos", "Covario IDS", "DataparkSearch","FindLinks", "Holmes", "htdig", "ia_archiver", "ichiro", "inktomi", "igdeSpyder", "L.webis", "Larbin", "LinkWalker", "lwp-trivial", "mabontland", "Mnogosearch", "mogimogi", "Morning Paper", "MVAClient", "NetResearchServer", "NewsGator", "NG-Search", "NutchCVS", "Nymesis", "oegp", "Orbiter", "Peew", "Pompos", "PostPost", "PycURL", "Qseero", "Radian6", "SBIder", "ScoutJet", "Scrubby", "SearchSight", "semanticdiscovery", "ShopWiki", "silk", "Snappy", "Sqworm", "StackRambler", "Teoma", "TinEye", "truwoGPS", "updated", "Vagabondo", "Vortex", "voyager", "VYU2", "webcollage", "Websquash.com", "wf84", "WomlpeFactory", "yacy", "Yahoo! Slurp", "Yahoo! Slurp China", "YahooSeeker", "YahooSeeker-Testing", "YandexImages", "Yeti", "yoogliFetchAgent", "Zao", "ZyBorg", "froogle","looksmart", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "Scooter", "appie", "WebBug", "Spade", "rabaz", "TechnoratiSnoop");
	update_option('adrotate_crawlers', $crawlers);
}

/*-------------------------------------------------------------
 Name:      adrotate_dummy_data
 Purpose:   Install dummy data in empty tables
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_dummy_data() {
	global $wpdb, $current_user;

	// Initial data
	$now = current_time('timestamp');
	$in84days = $now + 7257600;

	$no_ads = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` LIMIT 1;");
	$no_schedules = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_schedule` LIMIT 1;");
	$no_linkmeta = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_linkmeta` LIMIT 1;");

	if(is_null($no_ads) AND is_null($no_schedules) AND is_null($no_linkmeta)) {
		// Demo ad 1
	    $wpdb->insert("{$wpdb->prefix}adrotate", array('title' => 'Demo banner 468x60', 'bannercode' => '&lt;a href=\&quot;http:\/\/ajdg.net/landing.php?src=adrotatepro\&quot;&gt;&lt;img src=\&quot;http://ajdg.solutions/assets/banners/adrotate-468x60.jpg\&quot; width=&quot;468&quot; height=&quot;60&quot; /&gt;&lt;/a&gt;', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => '', 'image' => '', 'tracker' => 'N', 'show_everyone' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'type' => 'active', 'weight' => 6, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'state_req' => 'N', 'cities' => serialize(array()), 'states' => serialize(array()), 'countries' => serialize(array())));
	    $ad_id = $wpdb->insert_id;

		$wpdb->insert("{$wpdb->prefix}adrotate_schedule", array('name' => 'Schedule for ad '.$ad_id, 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'spread_all' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
	    $schedule_id = $wpdb->insert_id;
		$wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));

		unset($ad_id, $schedule_id);

		// Demo ad 2
	    $wpdb->insert("{$wpdb->prefix}adrotate", array('title' => 'Demo banner 728x90', 'bannercode' => '&lt;a href=\&quot;http:\/\/ajdg.net/landing.php?src=adrotatepro\&quot;&gt;&lt;img src=\&quot;http://ajdg.solutions/assets/banners/adrotate-728x90.jpg\&quot; width=&quot;468&quot; height=&quot;60&quot; /&gt;&lt;/a&gt;', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => '', 'image' => '', 'tracker' => 'Y', 'show_everyone' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'type' => 'active', 'weight' => 6, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'state_req' => 'N', 'cities' => serialize(array()), 'states' => serialize(array()), 'countries' => serialize(array())));
	    $ad_id = $wpdb->insert_id;

		$wpdb->insert("{$wpdb->prefix}adrotate_schedule", array('name' => 'Schedule for ad '.$ad_id, 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'spread_all' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
	    $schedule_id = $wpdb->insert_id;
		$wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));

		unset($ad_id, $schedule_id);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_database_install
 Purpose:   Creates database table if it doesnt exist
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_database_install() {
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Initial data
	$charset_collate = $engine = '';
	$now = current_time('timestamp');

	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate` (
	  	`id` mediumint(8) unsigned NOT NULL auto_increment,
	  	`title` varchar(255) NOT NULL DEFAULT '',
	  	`bannercode` longtext NOT NULL,
	  	`thetime` int(15) NOT NULL default '0',
		`updated` int(15) NOT NULL,
	  	`author` varchar(60) NOT NULL default '',
	  	`imagetype` varchar(10) NOT NULL,
	  	`image` varchar(255) NOT NULL,
	  	`tracker` char(1) NOT NULL default 'N',
	  	`show_everyone` char(1) NOT NULL default 'Y',
	  	`desktop` char(1) NOT NULL default 'Y',
	  	`mobile` char(1) NOT NULL default 'Y',
	  	`tablet` char(1) NOT NULL default 'Y',
	  	`os_ios` char(1) NOT NULL default 'Y',
	  	`os_android` char(1) NOT NULL default 'Y',
	  	`type` varchar(10) NOT NULL default '0',
	  	`weight` int(3) NOT NULL default '6',
	  	`autodelete` char(1) NOT NULL default 'N',
	  	`budget` double NOT NULL default '0',
	  	`crate` double NOT NULL default '0',
	  	`irate` double NOT NULL default '0',
	  	`state_req` char(1) NOT NULL default 'N',
		`cities` text NOT NULL,
		`states` text NOT NULL,
		`countries` text NOT NULL,
		PRIMARY KEY  (`id`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_groups` (
		`id` mediumint(8) unsigned NOT NULL auto_increment,
		`name` varchar(255) NOT NULL default '',
		`modus` tinyint(1) NOT NULL default '0',
		`swap` tinyint(1) NOT NULL default '0',
		`fallback` varchar(5) NOT NULL default '0',
		`network` mediumint(8) NOT NULL default '0',
		`cat` longtext NOT NULL,
		`cat_loc` tinyint(1) NOT NULL default '0',
		`cat_par` tinyint(2) NOT NULL default '0',
		`page` longtext NOT NULL,
		`page_loc` tinyint(1) NOT NULL default '0',
		`page_par` tinyint(2) NOT NULL default '0',
		`woo_cat` longtext NOT NULL,
		`woo_loc` tinyint(1) NOT NULL default '0',
		`bbpress` longtext NOT NULL,
		`bbpress_loc` tinyint(1) NOT NULL default '0',
		`mobile` tinyint(1) NOT NULL default '0',
		`geo` tinyint(1) NOT NULL default '0',
		`wrapper_before` longtext NOT NULL,
		`wrapper_after` longtext NOT NULL,
		`align` tinyint(1) NOT NULL default '0',
		`gridrows` int(3) NOT NULL DEFAULT '2',
		`gridcolumns` int(3) NOT NULL DEFAULT '2',
		`admargin` int(2) NOT NULL DEFAULT '0',
		`admargin_bottom` int(2) NOT NULL DEFAULT '0',
		`admargin_left` int(2) NOT NULL DEFAULT '0',
		`admargin_right` int(2) NOT NULL DEFAULT '0',
		`adwidth` varchar(6) NOT NULL DEFAULT '125',
		`adheight` varchar(6) NOT NULL DEFAULT '125',
		`adspeed` int(5) NOT NULL DEFAULT '6000',
		`repeat_impressions` char(1) NOT NULL DEFAULT 'Y',
		PRIMARY KEY  (`id`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_linkmeta` (
		`id` mediumint(8) unsigned NOT NULL auto_increment,
		`ad` int(5) unsigned NOT NULL default '0',
		`group` int(5) unsigned NOT NULL default '0',
		`user` int(5) unsigned NOT NULL default '0',
		`schedule` int(5) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_schedule` (
		`id` int(8) unsigned NOT NULL auto_increment,
		`name` varchar(255) NOT NULL default '',
		`starttime` int(15) unsigned NOT NULL default '0',
		`stoptime` int(15) unsigned NOT NULL default '0',
		`maxclicks` int(15) unsigned NOT NULL default '0',
		`maximpressions` int(15) unsigned NOT NULL default '0',
	  	`spread` char(1) NOT NULL default 'N',
	  	`spread_all` char(1) NOT NULL default 'N',
		`daystarttime` char(4) NOT NULL default '0000',
		`daystoptime` char(4) NOT NULL default '0000',
		`day_mon` char(1) NOT NULL default 'Y',
		`day_tue` char(1) NOT NULL default 'Y',
		`day_wed` char(1) NOT NULL default 'Y',
		`day_thu` char(1) NOT NULL default 'Y',
		`day_fri` char(1) NOT NULL default 'Y',
		`day_sat` char(1) NOT NULL default 'Y',
		`day_sun` char(1) NOT NULL default 'Y',
	  	`autodelete` char(1) NOT NULL default 'N',
		PRIMARY KEY  (`id`),
	    KEY `starttime` (`starttime`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_stats` (
		`id` bigint(9) unsigned NOT NULL auto_increment,
		`ad` int(5) unsigned NOT NULL default '0',
		`group` int(5) unsigned NOT NULL default '0',
		`thetime` int(15) unsigned NOT NULL default '0',
		`clicks` int(15) unsigned NOT NULL default '0',
		`impressions` int(15) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		INDEX `ad` (`ad`),
		INDEX `thetime` (`thetime`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_stats_archive` (
		`id` bigint(9) unsigned NOT NULL auto_increment,
		`ad` int(5) unsigned NOT NULL default '0',
		`group` int(5) unsigned NOT NULL default '0',
		`thetime` int(15) unsigned NOT NULL default '0',
		`clicks` int(15) unsigned NOT NULL default '0',
		`impressions` int(15) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		INDEX `ad` (`ad`),
		INDEX `thetime` (`thetime`)
	) ".$charset_collate.$engine.";");

	dbDelta("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}adrotate_tracker` (
		`id` bigint(9) unsigned NOT NULL auto_increment,
		`ipaddress` varchar(15) NOT NULL default '0',
		`timer` int(15) unsigned NOT NULL default '0',
		`bannerid` int(15) unsigned NOT NULL default '0',
		`stat` char(1) NOT NULL default 'c',
		PRIMARY KEY  (`id`),
	    KEY `ipaddress` (`ipaddress`),
	    KEY `timer` (`timer`)
	) ".$charset_collate.$engine.";");
}

/*-------------------------------------------------------------
 Name:      adrotate_check_upgrade
 Purpose:   Checks if the plugin needs to upgrade stuff
 Since:		3.7.3
-------------------------------------------------------------*/
function adrotate_check_upgrade() {
	if(version_compare(PHP_VERSION, '7.4.0', '<') == -1) {
		deactivate_plugins(plugin_basename('adrotate-pro/adrotate-pro.php'));
		wp_die('AdRotate 5.13 and newer requires PHP 7.4 or higher. Your server reports version '.PHP_VERSION.'. Contact your hosting provider about upgrading your server!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to dashboard</a>.');
	} else {
		$adrotate_db_version = get_option("adrotate_db_version");
		$adrotate_version = get_option("adrotate_version");
		if($adrotate_db_version['current'] < ADROTATE_DB_VERSION) {
			adrotate_database_upgrade();
		}

		if($adrotate_version['current'] < ADROTATE_VERSION) {
			adrotate_core_upgrade();
		}

		adrotate_check_config();
		adrotate_check_schedules();
		adrotate_evaluate_ads();
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_database_upgrade
 Purpose:   Upgrades AdRotate where required
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_database_upgrade() {
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Database type and specs
	$charset_collate = $engine = '';
	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}adrotate%';");
	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$adrotate_db_version = get_option("adrotate_db_version");

	// Database: 	58
	// AdRotate:	4.0
	if($adrotate_db_version['current'] < 58) {
		$wpdb->query("ALTER TABLE `{$wpdb->prefix}adrotate_schedule` CHANGE `dayimpressions` `hourimpressions` int(15) NOT NULL default '0';");
	}

	// Database: 	59
	// AdRotate:	4.1
	if($adrotate_db_version['current'] < 59) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'paid', 'char(1) NOT NULL default \'U\' AFTER `image`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'os_ios', 'char(1) NOT NULL default \'Y\' AFTER `tablet`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'os_android', 'char(1) NOT NULL default \'Y\' AFTER `os_ios`');
		adrotate_del_column("{$wpdb->prefix}adrotate", 'sortorder');
		adrotate_del_column("{$wpdb->prefix}adrotate_groups", 'sortorder');

		if(!in_array("{$wpdb->prefix}adrotate_transactions", $found_tables)) {
			dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_transactions` (
				`id` mediumint(8) unsigned NOT NULL auto_increment,
				`ad` mediumint(8) unsigned NOT NULL default '0',
				`user` mediumint(8) unsigned NOT NULL default '0',
				`reference` varchar(100) NOT NULL,
				`note` longtext NOT NULL,
				`billed` int(15) unsigned NOT NULL default '0',
				`paid` int(15) unsigned NOT NULL default '0',
				`amount` double NOT NULL default '0',
				`budget` char(1) NOT NULL default 'U',
				PRIMARY KEY  (`id`),
			    KEY `ad` (`ad`)
			) ".$charset_collate.$engine.";");
		}
	}

	// Database: 	60
	// AdRotate:	4.2
	if($adrotate_db_version['current'] < 60) {
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");
	}

	// Database: 	61
	// AdRotate:	4.3
	if($adrotate_db_version['current'] < 61) {
		adrotate_del_column("{$wpdb->prefix}adrotate_schedule", 'hourimpressions');
	}

	// Database: 	62
	// AdRotate:	4.4
	if($adrotate_db_version['current'] < 62) {
		// Make sure the table really is gone before creating a new one!
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");

		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_tracker` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ipaddress` varchar(15) NOT NULL default '0',
			`timer` int(15) unsigned NOT NULL default '0',
			`bannerid` int(15) unsigned NOT NULL default '0',
			`stat` char(1) NOT NULL default 'c',
			PRIMARY KEY  (`id`),
		    KEY `ipaddress` (`ipaddress`),
		    KEY `timer` (`timer`)
		) ".$charset_collate.$engine.";");

		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '\_transient\_adrotate\_%'");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '\_transient\_timeout\_adrotate\_%'");
	}

	// Database: 	63
	// AdRotate:	4.5
	if($adrotate_db_version['current'] < 63) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'autodelete', 'char(1) NOT NULL default \'N\' AFTER `weight`');
		adrotate_add_column("{$wpdb->prefix}adrotate_schedule", 'autodelete', 'char(1) NOT NULL default \'N\' AFTER `day_sun`');
	}

	// Database: 	64
	// AdRotate:	4.8
	if($adrotate_db_version['current'] < 64) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'show_everyone', 'char(1) NOT NULL default \'Y\' AFTER `tracker`');
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'repeat_impressions', 'char(1) NOT NULL default \'Y\' AFTER `adspeed`');
	}

	// Database: 	65
	// AdRotate:	5.4
	if($adrotate_db_version['current'] < 65) {
		adrotate_del_column("{$wpdb->prefix}adrotate", 'responsive');
		adrotate_del_column("{$wpdb->prefix}adrotate", 'paid');
		$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'trash'), array('type' => 'bin'));
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_transactions`");
	}

	// Database: 	66
	// AdRotate:	5.8
	if($adrotate_db_version['current'] < 66) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'states', 'text NOT NULL AFTER `cities`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'state_req', 'char(1) NOT NULL default \'N\' AFTER `irate`');
	}

	// Database: 	67
	// AdRotate:	5.8.15
	if($adrotate_db_version['current'] < 67) {
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'swap', 'tinyint(1) NOT NULL default \'0\' AFTER `modus`');
	}

	// Database: 	68
	// AdRotate:	5.8.18
	if($adrotate_db_version['current'] < 68) {
		adrotate_add_column("{$wpdb->prefix}adrotate_schedule", 'spread_all', 'char(1) NOT NULL default \'N\' AFTER `spread`');
	}

	// Database: 	69
	// AdRotate:	5.10
	if($adrotate_db_version['current'] < 69) {
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'woo_cat', 'longtext NOT NULL AFTER `page_par`');
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'woo_loc', 'tinyint(1) NOT NULL default \'0\' AFTER `woo_cat`');
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'bbpress', 'longtext NOT NULL AFTER `woo_loc`');
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'bbpress_loc', 'tinyint(1) NOT NULL default \'0\' AFTER `bbpress`');
	}

	// Database: 	72
	// AdRotate:	5.14
	if($adrotate_db_version['current'] < 72) {
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'network', 'mediumint(8) NOT NULL AFTER `fallback`');
		adrotate_del_column("{$wpdb->prefix}adrotate", 'os_other');
	}

	update_option("adrotate_db_version", array('current' => ADROTATE_DB_VERSION, 'previous' => $adrotate_db_version['current']));
}

/*-------------------------------------------------------------
 Name:      adrotate_core_upgrade
 Purpose:   Upgrades AdRotate where required
 Since:		3.5
-------------------------------------------------------------*/
function adrotate_core_upgrade() {
	global $wpdb, $wp_roles;

	$firstrun = date('U') + HOUR_IN_SECONDS;
	$adrotate_version = get_option("adrotate_version");
	$adrotate_config = get_option('adrotate_config');

	// 4.0
	if($adrotate_version['current'] < 382) {
		$config382 = get_option('adrotate_config');
		if($config382['enable_advertisers'] == 'Y') {
			$advertisers = $wpdb->get_results("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `user` > 0;");
			foreach($advertisers as $advertiser) {
				update_user_meta($advertiser->user, 'adrotate_is_advertiser', 'Y');
				update_user_meta($advertiser->user, 'adrotate_permissions', array('edit' => $config382['enable_editing'], 'mobile' => $config382['enable_mobile_advertisers'], 'geo' => $config382['enable_geo_advertisers']));
				update_user_meta($advertiser->user, 'adrotate_notes', '');
			}
		}
		unset($config382);

		$role = get_role('administrator');
		$role->add_cap("adrotate_advertiser_manage");
	}

	// 4.1
	if($adrotate_version['current'] < 384) {
		// Dummy
	}

	// 4.2
	if($adrotate_version['current'] < 385) {
		wp_clear_scheduled_hook('adrotate_clean_trackerdata');
	}

	// 4.2.1
	if($adrotate_version['current'] < 386) {
		if(!wp_next_scheduled('adrotate_delete_transients')) wp_schedule_event($firstrun, 'hourly', 'adrotate_delete_transients');
	}

	// 4.3
	if($adrotate_version['current'] < 387) {
		delete_option('adrotate_responsive_required');
	}

	// 4.4
	if($adrotate_version['current'] < 388) {
		wp_clear_scheduled_hook('adrotate_delete_transients');
		if(!wp_next_scheduled('adrotate_empty_trackerdata')) wp_schedule_event($firstrun, 'hourly', 'adrotate_empty_trackerdata');
	}

	// 4.5
	if($adrotate_version['current'] < 389) {
		adrotate_check_schedules();
	}

	// 4.7
	if($adrotate_version['current'] < 390) {
		if(!is_dir(WP_CONTENT_DIR.'/banners')) mkdir(WP_CONTENT_DIR.'/banners', 0755);
		if(!is_dir(WP_CONTENT_DIR.'/reports')) mkdir(WP_CONTENT_DIR.'/reports', 0755);
		$config390 = get_option('adrotate_config');
		$config390['banner_folder'] = "banners";
		update_option('adrotate_config', $config390);
	}

	// 5.1
	if($adrotate_version['current'] < 393) {
		$groups = $wpdb->get_results("SELECT `id`, `modus`, `gridcolumns`, `adwidth`, `adheight`, `admargin`, `admargin_bottom`, `admargin_left`, `admargin_right`, `align` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;", ARRAY_A);

		if(count($groups) > 0) {
			foreach($groups as $group) {
				$output_css = "";
				if($group['align'] == 0) { // None
					$group_align = '';
				} else if($group['align'] == 1) { // Left
					$group_align = ' float:left; clear:left;';
				} else if($group['align'] == 2) { // Right
					$group_align = ' float:right; clear:right;';
				} else if($group['align'] == 3) { // Center
					$group_align = ' margin: 0 auto;';
				}

				if($group['modus'] == 0 AND ($group['admargin'] > 0 OR $group['admargin_right'] > 0 OR $group['admargin_bottom'] > 0 OR $group['admargin_left'] > 0 OR $group['align'] > 0)) { // Single ad group
					if($group['align'] < 3) {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px;".$group_align." }\n";
					} else {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$group_align." }\n";
					}
				}

				if($group['modus'] == 1) { // Dynamic group
					if($group['adwidth'] != 'auto') {
						$width = "width:100%; max-width:".$group['adwidth']."px;";
					} else {
						$width = "width:auto;";
					}

					if($group['adheight'] != 'auto') {
						$height = "height:100%; max-height:".$group['adheight']."px;";
					} else {
						$height = "height:auto;";
					}

					if($group['align'] < 3) {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px;".$width." ".$height.$group_align." }\n";
					} else {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$width." ".$height.$group_align." }\n";
					}

					unset($width_sum, $width, $height_sum, $height);
				}

				if($group['modus'] == 2) { // Block group
					if($group['adwidth'] != 'auto') {
						$width_sum = $group['gridcolumns'] * ($group['admargin_left'] + $group['adwidth'] + $group['admargin_right']);
						$grid_width = "min-width:".$group['admargin_left']."px; max-width:".$width_sum."px;";
					} else {
						$grid_width = "width:auto;";
					}

					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$grid_width.$group_align." }\n";
					$output_css .= "\t.b".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px; }\n";
					unset($width_sum, $grid_width, $height_sum, $grid_height);
				}
				$generated_css[$group['id']] = $output_css;
				unset($output_css);
			}
			unset($groups);

			// Check/Merge existing CSS
			$group_css = get_option('adrotate_group_css');
			if(is_array($group_css)) {
				$keys = array_keys($group_css);
				foreach($keys as $i => $key) {
					if (array_key_exists($key, $generated_css)) {
						unset($generated_css[$key]);
					}
				}
				$group_css = array_merge($group_css, $generated_css);
			} else {
				$group_css = $generated_css;
			}

			update_option('adrotate_group_css', $group_css);
		}
	}

	// 5.6
	if($adrotate_version['current'] < 394) {
		delete_option('adrotate_hide_translation');
	}

	// 5.6.5
	if($adrotate_version['current'] < 396) {
		add_option('adrotate_dynamic_widgets_advert', uniqid());
		add_option('adrotate_dynamic_widgets_group', uniqid());
		wp_clear_scheduled_hook('adrotate_update_check');
	}

	// 5.8
	if($adrotate_version['current'] < 397) {
		if(!wp_next_scheduled('adrotate_update_check')) { // Check for updates/set new transient
			wp_schedule_event($firstrun, 'daily', 'adrotate_update_check');
		}
	}

	// 5.8.1
	if($adrotate_version['current'] < 398) {
		delete_option('adrotate_hide_competition');
	}

	// 5.8.2
	if($adrotate_version['current'] < 399) {
		wp_clear_scheduled_hook('adrotate_update_check');
		wp_clear_scheduled_hook('adrotate_empty_bin');
	}

	// 5.8.15
	if($adrotate_version['current'] < 400) {
		delete_option('adrotate_geo_reset');
		add_option('adrotate_swap', array('status' => 0, 'instance' => '', 'activated' => 0, 'deactivated' => 0, 'campaign' => array('status' => 0), 'offers' => array(), 'current' => array()));
	}

	// 5.8.18
	if($adrotate_version['current'] < 401) {
		add_option('adrotate_gam_output', '');
	}

	// 5.12
	if($adrotate_version['current'] < 402) {
		$hide_license = get_option('adrotate_hide_license', 'N');
		if($hide_license == 0 OR $hide_license == false) {
			update_option('adrotate_hide_license', 'N');
		} else {
			update_option('adrotate_hide_license', 'Y');
		}
	}

	update_option("adrotate_version", array('current' => ADROTATE_VERSION, 'previous' => $adrotate_version['current']));
}

/*-------------------------------------------------------------
 Name:      adrotate_create_folders
 Purpose:   Attempt to create essential folders and files
 Since:		5.8.18
-------------------------------------------------------------*/
function adrotate_create_folders() {
	global $adrotate_config;

	if(!is_dir(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'])) {
		mkdir(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'], 0755);
	}

	if(!is_dir(WP_CONTENT_DIR.'/reports')) {
		mkdir(WP_CONTENT_DIR.'/reports', 0755);
		$fprotect = fopen(WP_CONTENT_DIR.'/reports/index.html', 'wb');
		fclose($fprotect);
		unset($fprotect);
	}

	if(!file_exists(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt')){
		$fp = fopen(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt', 'wb');
		fwrite($fp, "# Welcome to your ads.txt file. Add your authorized adverts here, one per line - AdRotate Pro Support\n");
		fwrite($fp, "# Lines like these, starting with a # are comments and can either be removed or kept for reference/management\n\n");
		fwrite($fp, "# Domainname, Publisher ID, Type, Certificate Authority ID\n");
		fclose($fp);
		unset($fp);
	}

	adrotate_return('adrotate-settings', 409, array('tab' => 'maintenance'));
}

/*-------------------------------------------------------------
 Name:      adrotate_empty_trackerdata
 Purpose:   Removes old statistics
 Since:		4.4
-------------------------------------------------------------*/
function adrotate_empty_trackerdata() {
	global $wpdb;

	$clicks = current_time('timestamp') - DAY_IN_SECONDS;
	$impressions = current_time('timestamp') - HOUR_IN_SECONDS;

	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `timer` < {$impressions} AND `stat` = 'i';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `timer` < {$clicks} AND `stat` = 'c';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `ipaddress`  = 'unknown' OR `ipaddress`  = '';");
}

/*-------------------------------------------------------------
 Name:      adrotate_empty_trash
 Purpose:   Delete expired and trashed adverts
 Since:		3.21
-------------------------------------------------------------*/
function adrotate_empty_trash() {
	global $wpdb;

	$threedaysago = current_time('timestamp') - 259200;

	$adverts = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'trash' AND `updated` < {$threedaysago};");
	foreach($adverts as $advert) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats_archive` WHERE `ad` = {$advert->id};");
	}
	unset($adverts);
}


/*-------------------------------------------------------------
 Name:      adrotate_auto_delete
 Purpose:   Auto trash selected adverts and schedules
 Since:		4.5
-------------------------------------------------------------*/
function adrotate_auto_delete() {
	global $wpdb;

	// Auto trash expired adverts
	$now = current_time('timestamp');
	$twentythreehoursago = $now - 82800;

	$adverts = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'expired' AND `autodelete` = 'Y';");
	foreach($adverts as $advert) {
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$advert->id}' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		if($stoptime <= $twentythreehoursago) {
			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'trash', 'updated' => $now), array('id' => $advert->id));
		}
		unset($advert, $stoptime);
	}
	unset($adverts);

	// Auto delete expired schedules
	$schedules = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate_schedule` WHERE `stoptime` <= {$twentythreehoursago} AND `autodelete` = 'Y';");
	foreach($schedules as $schedule) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = {$schedule->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = {$schedule->id};");
	}

}

/*-------------------------------------------------------------
 Name:      adrotate_add_column
 Purpose:   Check if the column exists in the table
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_add_column($table_name, $column_name, $attributes) {
	global $wpdb;

	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) return true;
	}

	$wpdb->query("ALTER TABLE $table_name ADD $column_name " . $attributes.";");

	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) return true;
	}

	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_del_column
 Purpose:   Check if the column exists in the table remove if it does
 Since:		3.8.3.3
-------------------------------------------------------------*/
function adrotate_del_column($table_name, $column_name) {
	global $wpdb;

	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) {
			$wpdb->query("ALTER TABLE $table_name DROP $column;");
			return true;
		}
	}

	return false;
}
?>
