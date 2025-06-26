<?php
/*
Plugin Name: AdRotate Professional
Plugin URI: https://ajdg.solutions/product-category/adrotate-pro/
Update URI: adrotatepro
Author: Arnan de Gans
Author URI: https://www.arnan.me/
Description: AdRotate Pro is the popular choice for monetizing your website with adverts while keeping things simple.
Text Domain: adrotate-pro
Domain Path: /languages/
Version: 5.14.3
License: Limited License

WP requires at least: 4.9
WP tested up to: 6.4.1
CP requires at least: 1.0
CP tested up to: 2.0
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*--- AdRotate values ---------------------------------------*/
define("ADROTATE_VERSION", 402);
define("ADROTATE_DB_VERSION", 72);
$plugin_folder = plugin_dir_path(__FILE__);
/*-----------------------------------------------------------*/

/*--- Load Files --------------------------------------------*/
require_once($plugin_folder.'/adrotate-setup.php');
require_once($plugin_folder.'/adrotate-manage-publisher.php');
require_once($plugin_folder.'/adrotate-manage-advertiser.php');
require_once($plugin_folder.'/adrotate-functions.php');
require_once($plugin_folder.'/adrotate-statistics.php');
require_once($plugin_folder.'/adrotate-portability.php');
require_once($plugin_folder.'/adrotate-output.php');
require_once($plugin_folder.'/adrotate-widgets.php');
require_once($plugin_folder.'/adrotate-widgets-old.php');
if(function_exists('register_block_type')) include_once($plugin_folder.'/adrotate-block.php');
/*--- Network -----------------------------------------------*/
if(adrotate_is_networked()) require_once($plugin_folder.'/adrotate-widget-network.php');
/*-----------------------------------------------------------*/

/*--- Check and Load config ---------------------------------*/
if(is_admin()) adrotate_check_config(); // has to be early
load_plugin_textdomain('adrotate-pro', false, 'adrotate-pro/language');
$adrotate_config = get_option('adrotate_config');
$adrotate_crawlers = get_option('adrotate_crawlers');
$adrotate_version = get_option('adrotate_version');
$adrotate_db_version = get_option('adrotate_db_version');
$adrotate_network = get_site_option('adrotate_network_settings', array('primary' => 1, 'site_dashboard' => 'Y'));
/*-----------------------------------------------------------*/

/*--- Core --------------------------------------------------*/
register_activation_hook(__FILE__, 'adrotate_activate');
register_deactivation_hook(__FILE__, 'adrotate_deactivate');
register_uninstall_hook(__FILE__, 'adrotatepro_uninstall');
add_action('adrotate_notification', 'adrotate_notifications');
add_action('adrotate_evaluate_ads', 'adrotate_evaluate_ads');
add_action('adrotate_empty_trash', 'adrotate_empty_trash');
add_action('adrotate_empty_trackerdata', 'adrotate_empty_trackerdata');
add_action('adrotate_auto_delete', 'adrotate_auto_delete');
add_action('init', 'adrotate_session', 1);
add_action('widgets_init', 'adrotate_widgets');
add_action('widgets_init', 'adrotate_old_widgets');
if(adrotate_is_networked()) add_action('widgets_init', 'adrotate_network_widget'); // Network widget
add_filter('adrotate_apply_photon', 'adrotate_apply_jetpack_photon');
/*-----------------------------------------------------------*/

/*--- Front end ---------------------------------------------*/
if(!is_admin()) {
	add_action('wp_head', 'adrotate_header');
	add_action('wp_enqueue_scripts', 'adrotate_scripts');
	add_shortcode('adrotate', 'adrotate_shortcode');
	if($adrotate_config['textwidget_shortcodes'] == 'Y') add_filter('widget_text', 'do_shortcode');

	// Post Injection
	add_filter('the_content', 'adrotate_inject_posts', 12);
	add_action('woocommerce_before_single_product', 'adrotate_inject_products', 15);
	add_action('woocommerce_after_single_product', 'adrotate_inject_products', 15);
	add_action('bbp_template_before_topics_loop', 'adrotate_inject_forums', 15);
	add_action('bbp_template_after_topics_loop', 'adrotate_inject_forums', 15);
	add_action('bbp_template_before_replies_loop', 'adrotate_inject_forums', 15);
	add_action('bbp_template_after_replies_loop', 'adrotate_inject_forums', 15);
}

// AJAX Callbacks
if($adrotate_config['stats'] == 1){
	add_action('wp_ajax_adrotate_impression', 'adrotate_impression_callback');
	add_action('wp_ajax_nopriv_adrotate_impression', 'adrotate_impression_callback');
	add_action('wp_ajax_adrotate_click', 'adrotate_click_callback');
	add_action('wp_ajax_nopriv_adrotate_click', 'adrotate_click_callback');
}
/*-----------------------------------------------------------*/

/*--- Back end ----------------------------------------------*/
if(is_admin()) {
	/*--- Publisher ---------------------------------------------*/
	if(isset($_POST['adrotate_generate_submit'])) add_action('init', 'adrotate_generate_input');
//	if(isset($_POST['adrotate_swap_submit'])) add_action('init', 'adrotate_generate_campaign');
	if(isset($_POST['adrotate_save_header'])) add_action('init', 'adrotate_save_header');
	if(isset($_POST['adrotate_ad_submit'])) add_action('init', 'adrotate_insert_input');
	if(isset($_POST['adrotate_group_submit'])) add_action('init', 'adrotate_insert_group');
	if(isset($_POST['adrotate_schedule_submit'])) add_action('init', 'adrotate_insert_schedule');
	if(isset($_POST['adrotate_media_submit'])) add_action('init', 'adrotate_insert_media');
	if(isset($_POST['adrotate_folder_submit'])) add_action('init', 'adrotate_insert_folder');
	if(isset($_POST['adrotate_advertiser_submit'])) add_action('init', 'adrotate_insert_advertiser');
	if(isset($_POST['adrotate_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_disabled_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_error_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_notification_test_submit'])) add_action('init', 'adrotate_notifications');
	if(isset($_POST['adrotate_save_options'])) add_action('init', 'adrotate_save_options');
	if(isset($_POST['adrotate_save_network_options'])) add_action('init', 'adrotate_save_network_options');
	if(isset($_POST['adrotate_contact_submit'])) add_action('init', 'adrotate_mail_advertiser');
	if(isset($_POST['adrotate_create_folders_submit'])) add_action('init', 'adrotate_create_folders');
	if(isset($_POST['adrotate_evaluate_submit'])) add_action('init', 'adrotate_prepare_evaluate_ads');
	if(isset($_POST['adrotate_import'])) add_action('init', 'adrotate_import_ads');
	if(isset($_POST['adrotate_export_submit'])) add_action('init', 'adrotate_export_stats');

	/*--- Advertiser --------------------------------------------*/
	if($adrotate_config['enable_advertisers'] == 'Y') {
		if(isset($_POST['adrotate_advertiser_ad_submit'])) add_action('init', 'adrotate_advertiser_insert_input');
		add_action('show_user_profile', 'adrotate_custom_profile_fields');
		add_action('edit_user_profile', 'adrotate_custom_profile_fields');
		add_action('user_new_form', 'adrotate_custom_profile_fields');
		add_action('user_register', 'adrotate_save_profile_fields'); // Update new profile
		add_action('edit_user_profile_update', 'adrotate_save_profile_fields'); // Update others profile
		add_action('personal_options_update', 'adrotate_save_profile_fields'); // Update own profile
	}

	/*--- API and Licensing ---------------------------------*/
	include_once($plugin_folder.'/library/update-api.php');
	add_filter('site_transient_update_plugins', 'adrotate_get_update_information'); // Get update info
	add_filter('plugins_api', 'adrotate_get_plugin_information', 10, 3); // Plugin info popup
	add_filter('all_plugins', 'adrotate_complete_plugin_information', 10, 1); // Plugin info popup
	add_action('upgrader_process_complete', 'adrotate_update_completed', 10, 2); // Finish update in the background

	include_once($plugin_folder.'/library/license-api.php');
	if(isset($_POST['adrotate_license_activate'])) add_action('init', 'adrotate_license_activate');
	if(isset($_POST['adrotate_license_deactivate'])) add_action('init', 'adrotate_license_deactivate');
	if(isset($_POST['adrotate_support_submit'])) add_action('init', 'adrotate_support_request');

/*
	include_once($plugin_folder.'/library/swap-api.php');
	if(isset($_POST['adrotate_swap_register'])) add_action('init', 'adrotate_swap_register');
	if(isset($_POST['adrotate_swap_activate'])) add_action('init', 'adrotate_swap_activate');
	if(isset($_POST['adrotate_swap_deactivate'])) add_action('init', 'adrotate_swap_deactivate');
*/

	/*--- Multisite --------------------------------------------*/
	global $blog_id;
	if((adrotate_is_networked() AND $adrotate_network['site_dashboard'] == "Y") OR $adrotate_network['primary'] == $blog_id OR !adrotate_is_networked()) {
		add_action('admin_menu', 'adrotate_dashboard');
		add_action("admin_enqueue_scripts", 'adrotate_dashboard_scripts');
		add_action("admin_print_styles", 'adrotate_dashboard_styles');
		add_action('admin_notices', 'adrotate_notifications_dashboard');
	}

	if(adrotate_is_networked()) add_action('network_admin_menu', 'adrotate_network_dashboard');

	/*--- Misc --------------------------------------------------*/
	add_action('wpmu_new_blog', 'adrotate_activate_new_blog', 10, 1);
	add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'adrotate_action_links');
}
/*-----------------------------------------------------------*/

/*-------------------------------------------------------------
 Name:      adrotate_dashboard
 Purpose:   Add pages to admin menus
-------------------------------------------------------------*/
function adrotate_dashboard() {
	global $adrotate_config;

	add_menu_page('AdRotate Pro', 'AdRotate Pro', 'adrotate_ad_manage', 'adrotate', 'adrotate_manage_adverts', plugins_url('/images/icon-menu.png', __FILE__), '25.8');
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Manage Adverts', 'adrotate-pro'), __('Manage Adverts', 'adrotate-pro'), 'adrotate_ad_manage', 'adrotate', 'adrotate_manage_adverts');
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Manage Groups', 'adrotate-pro'), __('Manage Groups', 'adrotate-pro'), 'adrotate_group_manage', 'adrotate-groups', 'adrotate_manage_group');
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Manage Schedules', 'adrotate-pro'), __('Manage Schedules', 'adrotate-pro'), 'adrotate_schedule_manage', 'adrotate-schedules', 'adrotate_manage_schedules');
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Manage Files', 'adrotate-pro'), __('Manage Files', 'adrotate-pro'), 'adrotate_ad_manage', 'adrotate-media', 'adrotate_manage_media');
	if($adrotate_config['enable_advertisers'] == 'Y') {
		add_submenu_page('adrotate', 'AdRotate Pro · '.__('Manage Advertisers', 'adrotate-pro'), __('Manage Advertisers', 'adrotate-pro'), 'adrotate_advertiser_manage', 'adrotate-advertisers', 'adrotate_manage_advertisers');
	}
//	add_submenu_page('adrotate', 'AdRotate Pro · AdRotate Swap', __('Manage Campaign', 'adrotate-pro'), 'adrotate_ad_manage', 'adrotate-swap', 'adrotate_swap');
	if($adrotate_config['stats'] == 1) {
		add_submenu_page('adrotate', 'AdRotate Pro · '.__('Statistics', 'adrotate-pro'), __('Statistics', 'adrotate-pro'), 'adrotate_global_report', 'adrotate-statistics', 'adrotate_statistics');
	}
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Premium Support', 'adrotate-pro'), __('Premium Support', 'adrotate-pro'), 'adrotate_ad_manage', 'adrotate-support', 'adrotate_support');
	add_submenu_page('adrotate', 'AdRotate Pro · '.__('Settings', 'adrotate-pro'), __('Settings', 'adrotate-pro'), 'manage_options', 'adrotate-settings', 'adrotate_options');

	if($adrotate_config['enable_advertisers'] == 'Y') {
		$current_user = wp_get_current_user();
		$is_advertiser = get_user_meta($current_user->ID, 'adrotate_is_advertiser', 1);
		if($is_advertiser == 'Y') {
			add_menu_page(__('Advertiser', 'adrotate-pro'), __('Advertiser', 'adrotate-pro'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser', plugins_url('/images/icon-menu.png', __FILE__), '25.9');
			add_submenu_page('adrotate-advertiser', 'AdRotate Pro · '.__('Advertiser', 'adrotate-pro'), __('Advertiser', 'adrotate-pro'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser');
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_network_dashboard
 Purpose:   Add pages to admin menus if AdRotate is network activated
-------------------------------------------------------------*/
function adrotate_network_dashboard() {
	add_menu_page('AdRotate Pro', 'AdRotate Pro', 'manage_network', 'adrotate-network-settings', 'adrotate_network_settings', plugins_url('/images/icon-menu.png', __FILE__));
	add_submenu_page('adrotate-network-settings', 'AdRotate Pro · '.__('Network Settings', 'adrotate-pro'), __('Network Settings', 'adrotate-pro'), 'manage_network', 'adrotate-network-settings', 'adrotate_network_settings');
	add_submenu_page('adrotate-network-settings', 'AdRotate Pro · '.__('License', 'adrotate-pro'), __('License', 'adrotate-pro'), 'manage_network', 'adrotate-network-license', 'adrotate_network_license');
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_adverts
 Purpose:   Admin management page
-------------------------------------------------------------*/
function adrotate_manage_adverts() {
	global $wpdb, $userdata, $blog_id, $adrotate_config;

	$status = $file = $view = $ad_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']); // Exports
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']); // Tab
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($ad_edit_id)) $ad_edit_id = 0;

	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Adverts', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status, array('file' => $file)); ?>

		<?php
		$allbanners = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `weight`, `autodelete`, `desktop`, `mobile`, `tablet`, `budget`, `crate`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE `type` != 'empty' AND `type` != 'a_empty' ORDER BY `id` ASC;");

		$active = $error = $disabled = $queued = $archive = $trash = array();
		foreach($allbanners as $singlebanner) {
			$advertiser = '';
			$starttime = $stoptime = 0;
			$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$singlebanner->id}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
			$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$singlebanner->id}' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
			if($adrotate_config['enable_advertisers'] == 'Y') {
				$advertiser = $wpdb->get_var("SELECT `display_name` FROM `{$wpdb->prefix}adrotate_linkmeta`, `$wpdb->users` WHERE `$wpdb->users`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`user` AND `ad` = '{$singlebanner->id}' AND `group` = '0' AND `schedule` = '0' LIMIT 1;");
			}

			$type = $singlebanner->type;
			if($type == 'active' AND $stoptime <= $now) $type = 'expired';
			if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
			if($type == 'active' AND $stoptime <= $in7days) $type = '7days';

			$title = (strlen($singlebanner->title) == 0) ? 'Advert '.$singlebanner->id.' [temp]' : $singlebanner->title;

			if($type == 'active' OR $type == '7days') {
				$active[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'advertiser' => $advertiser,
					'type' => $type,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'budget' => $singlebanner->budget,
					'crate' => $singlebanner->crate,
					'irate' => $singlebanner->irate,
					'tracker' => $singlebanner->tracker,
					'weight' => $singlebanner->weight,
					'autodelete' => $singlebanner->autodelete,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}

			if($type == 'error' OR $type == 'a_error' OR $type == 'expired' OR $type == '2days' OR $type == 'limit') {
				$error[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'advertiser' => $advertiser,
					'type' => $type,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'tracker' => $singlebanner->tracker,
					'weight' => $singlebanner->weight,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}

			if($type == 'disabled') {
				$disabled[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'advertiser' => $advertiser,
					'type' => $type,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'tracker' => $singlebanner->tracker,
					'weight' => $singlebanner->weight,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}

			if($type == 'queue' OR $type == 'reject') {
				$queued[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'type' => $singlebanner->type,
					'tracker' => $singlebanner->tracker,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'weight' => $singlebanner->weight,
					'budget' => $singlebanner->budget,
					'crate' => $singlebanner->crate,
					'irate' => $singlebanner->irate,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}

			if($type == 'archived') {
				$archive[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'advertiser' => $advertiser,
					'type' => $type,
					'tracker' => $singlebanner->tracker,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'budget' => $singlebanner->budget,
					'crate' => $singlebanner->crate,
					'irate' => $singlebanner->irate,
					'weight' => $singlebanner->weight,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}

			if($type == 'trash') {
				$trash[$singlebanner->id] = array(
					'id' => $singlebanner->id,
					'title' => $title,
					'advertiser' => $advertiser,
					'type' => $type,
					'desktop' => $singlebanner->desktop,
					'mobile' => $singlebanner->mobile,
					'tablet' => $singlebanner->tablet,
					'budget' => $singlebanner->budget,
					'crate' => $singlebanner->crate,
					'irate' => $singlebanner->irate,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}
		}
		?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate');?>"><?php _e('Manage', 'adrotate-pro'); ?></a>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=generator');?>"><?php _e('Advert Generator', 'adrotate-pro'); ?></a>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=addnew');?>"><?php _e('New Advert', 'adrotate-pro'); ?></a>
				<?php if($adrotate_config['enable_advertisers'] == "Y") { ?>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=queue');?>"><?php _e('Queue', 'adrotate-pro'); ?></a>
				<?php } ?>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=archive');?>"><?php _e('Archive', 'adrotate-pro'); ?></a>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=trash');?>"><?php _e('Trash', 'adrotate-pro'); ?></a>
				&nbsp;|&nbsp;<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate&view=advanced');?>"><?php _e('Header & ads.txt', 'adrotate-pro'); ?></a>
			</div>
		</div>

    	<?php

    	if($view == "") {
			if(count($error) > 0) {
				include("dashboard/publisher/adverts-error.php");
			}

			include("dashboard/publisher/adverts-main.php");
			
			if(count($disabled) > 0){
				include("dashboard/publisher/adverts-disabled.php");
			}
	   	} else if($view == "generator") {
			include("dashboard/publisher/adverts-generator.php");
	   	} else if($view == "addnew" OR $view == "edit") {
			include("dashboard/publisher/adverts-edit.php");
		} else if($view == "queue") {
			include("dashboard/publisher/adverts-queue.php");
		} else if($view == "archive") {
			include("dashboard/publisher/adverts-archive.php");
		} else if($view == "trash") {
			include("dashboard/publisher/adverts-trash.php");
	   	} else if($view == "advanced") {
		   	$adrotate_gam = get_option('adrotate_gam_output');
		   	$adrotate_header = get_option('adrotate_header_output');

			if(file_exists(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt')){
				$adstxt_content = file(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt');
			} else {
				$adstxt_content = "";
			}

			include("dashboard/publisher/adverts-advanced.php");
		}
		?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_group
 Purpose:   Manage groups
-------------------------------------------------------------*/
function adrotate_manage_group() {
	global $wpdb, $adrotate_config;

	$status = $view = $group_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['group'])) $group_edit_id = esc_attr($_GET['group']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($group_edit_id)) $group_edit_id = 0;

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);

	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;

	$license = adrotate_get_license();
	$network = get_site_option('adrotate_network_settings');
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Groups', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups');?>"><?php _e('Manage', 'adrotate-pro'); ?></a> |
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=addnew');?>"><?php _e('Add New', 'adrotate-pro'); ?></a>
			</div>
		</div>

    	<?php
	    if ($view == "") {
			include("dashboard/publisher/groups-main.php");
	   	} else if($view == "addnew" OR $view == "edit") {
			include("dashboard/publisher/groups-edit.php");
	   	}
	   	?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_schedules
 Purpose:   Manage schedules for ads
-------------------------------------------------------------*/
function adrotate_manage_schedules() {
	global $wpdb, $adrotate_config;

	$status = $view = $schedule_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['schedule'])) $schedule_edit_id = esc_attr($_GET['schedule']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($schedule_edit_id)) $schedule_edit_id = 0;

	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Schedules', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules');?>"><?php _e('Manage', 'adrotate-pro'); ?></a> |
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=addnew');?>"><?php _e('Add New', 'adrotate-pro'); ?></a>
			</div>
		</div>

    	<?php
	    if ($view == "") {
			include("dashboard/publisher/schedules-main.php");
		} else if($view == "addnew" OR $view == "edit") {
			include("dashboard/publisher/schedules-edit.php");
		}
		?>

		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_statistics
 Purpose:   Advert and Group stats
-------------------------------------------------------------*/
function adrotate_statistics() {
	global $wpdb, $adrotate_config;

	$status = $view = $id = $file = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['id'])) $id = esc_attr($_GET['id']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($id)) $id = 0;

	if(isset($_GET['graph_start']) AND isset($_GET['graph_end'])) {
		list($start_day, $start_month, $start_year) = explode('-', esc_attr($_GET['graph_start']));
		list($end_day, $end_month, $end_year) = explode('-', esc_attr( $_GET['graph_end']));

		$graph_start_date = gmmktime(0, 0, 0, $start_month, $start_day, $start_year);
		$graph_end_date = gmmktime(0, 0, 0, $end_month, $end_day, $end_year);
	} else {
		$graph_end_date = current_time('timestamp');
		$graph_start_date = $graph_end_date - (28 * DAY_IN_SECONDS);
	}
	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Statistics', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status, array('file' => $file)); ?>

		<?php
	    if ($view == "") {
			include("dashboard/publisher/statistics-main.php");
		} else if($view == "advert") {
			include("dashboard/publisher/statistics-advert.php");
		} else if($view == "group") {
			include("dashboard/publisher/statistics-group.php");
		} else if($view == "advertiser") {
			include("dashboard/publisher/statistics-advertiser.php");
		}
		?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_advertisers
 Purpose:   Manage advertisers
-------------------------------------------------------------*/
function adrotate_manage_advertisers() {
	global $wpdb, $userdata, $adrotate_config;

	$status = $view = $user_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['user'])) $user_id = esc_attr($_GET['user']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($user_id)) $user_id = 0;

	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Advertisers', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertisers');?>"><?php _e('Manage', 'adrotate-pro'); ?></a> |
				<a class="row-title" href="<?php echo admin_url('users.php?adrotate');?>"><?php _e('All Users', 'adrotate-pro'); ?></a> |
				<a class="row-title" href="<?php echo admin_url('user-new.php?adrotate');?>"><?php _e('New User', 'adrotate-pro'); ?></a>
			</div>
		</div>

		<?php
		$all_advertisers = get_users(array('fields' => array('ID', 'display_name', 'user_email'), 'meta_key' => 'adrotate_is_advertiser', 'meta_value' => 'Y', 'orderby' => 'ID', 'order' => 'ASC'));

		$advertisers = array();
		foreach($all_advertisers as $advertiser) {
			$has_adverts = $wpdb->get_var("SELECT COUNT(`{$wpdb->prefix}adrotate_linkmeta`.`id`) as `count` FROM `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `user` = '{$advertiser->ID}' AND `ad` = `{$wpdb->prefix}adrotate`.`id` AND `type` != 'empty' AND `type` != 'a_empty';");

			$advertisers[$advertiser->ID] = array(
				'name' => $advertiser->display_name,
				'email' => $advertiser->user_email,
				'has_adverts' => $has_adverts,
			);
			unset($advertiser);
		}

    	if ($view == "" OR $view == "manage") {
			include("dashboard/publisher/advertisers-main.php");
		} else if($view == "profile") {
			include("dashboard/publisher/advertisers-profile.php");
		} else if($view == "contact") {
			include("dashboard/publisher/advertisers-contact.php");
		}
		?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_media
 Purpose:   Manage banner images for ads
-------------------------------------------------------------*/
function adrotate_manage_media() {
	global $wpdb, $adrotate_config;

	$status = $file = $path = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);
	if(isset($_GET['path'])) $path = esc_attr($_GET['path']);

	if(!is_numeric($status)) $status = 0;

	if(strlen($file) > 0 AND wp_verify_nonce($_REQUEST['_wpnonce'], 'adrotate_delete_media_'.$file)) {
		if(adrotate_unlink($file, $path)) {
			$status = 206;
		} else {
			$status = 517;
		}
	}
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Files and Reports', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<?php
		include("dashboard/publisher/media.php");
		?>

		<br class="clear" />

		<?php echo adrotate_trademark(); ?>
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_support
 Purpose:   Get help
-------------------------------------------------------------*/
function adrotate_support() {
	global $wpdb, $adrotate_config;

	$status = $file = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);

	if(!is_numeric($status)) $status = 0;

	$current_user = wp_get_current_user();
	$a = adrotate_get_license();
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Premium Support', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<?php
		include("dashboard/support.php");
		?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_swap
 Purpose:   Promote your website for free
-------------------------------------------------------------*/
/*
function adrotate_swap() {
	global $wpdb, $adrotate_config;

	$status = (isset($_GET['status'])) ? esc_attr($_GET['status']) : '';

	if(!is_numeric($status)) $status = 0;

	$action = (isset($_GET['action'])) ? esc_attr($_GET['action']) : '';
	if($action == 'reg') adrotate_check_upgrade();
	if($action == 'unreg') adrotate_check_upgrade();
	if($action == 'extend') adrotate_check_upgrade();
	if($action == 'cancel') adrotate_check_upgrade();

	$current_user = wp_get_current_user();
	$s = get_option('adrotate_swap');
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('AdRotate Swap', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<?php
		include("dashboard/publisher/swap-main.php");
		?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}
*/

/*-------------------------------------------------------------
 Name:      adrotate_options
 Purpose:   Admin options page
-------------------------------------------------------------*/
function adrotate_options() {
	global $wpdb, $wp_roles;

    $active_tab = (isset($_GET['tab'])) ? esc_attr($_GET['tab']) : 'general';
	$status = (isset($_GET['status'])) ? esc_attr($_GET['status']) : '';

	if(!is_numeric($status)) $status = 0;

	$action = (isset($_GET['action'])) ? esc_attr($_GET['action']) : '';
	if($action == 'update-db') adrotate_check_upgrade();
	if($action == 'reset-tasks') {
		adrotate_check_schedules();
		$status = 401;
	}
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Settings', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

		<h2 class="nav-tab-wrapper">
            <a href="?page=adrotate-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=notifications" class="nav-tab <?php echo $active_tab == 'notifications' ? 'nav-tab-active' : ''; ?>"><?php _e('Notifications', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=stats" class="nav-tab <?php echo $active_tab == 'stats' ? 'nav-tab-active' : ''; ?>"><?php _e('Statistics', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=geo" class="nav-tab <?php echo $active_tab == 'geo' ? 'nav-tab-active' : ''; ?>"><?php _e('Geo Targeting', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=roles" class="nav-tab <?php echo $active_tab == 'roles' ? 'nav-tab-active' : ''; ?>"><?php _e('Access Roles', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=misc" class="nav-tab <?php echo $active_tab == 'misc' ? 'nav-tab-active' : ''; ?>"><?php _e('Miscellaneous', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php _e('Tools', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=maintenance" class="nav-tab <?php echo $active_tab == 'maintenance' ? 'nav-tab-active' : ''; ?>"><?php _e('Maintenance', 'adrotate-pro'); ?></a>
            <a href="?page=adrotate-settings&tab=license" class="nav-tab <?php echo $active_tab == 'license' ? 'nav-tab-active' : ''; ?>"><?php _e('License', 'adrotate-pro'); ?></a>
        </h2>

		<?php
		$adrotate_config = get_option('adrotate_config');

		if($active_tab == 'general') {
			$adrotate_crawlers = get_option('adrotate_crawlers');

			$crawlers = '';
			if(is_array($adrotate_crawlers)) {
				$crawlers = implode(', ', $adrotate_crawlers);
			}

			include("dashboard/settings/general.php");
		} elseif($active_tab == 'notifications') {
			$adrotate_notifications	= get_option("adrotate_notifications");

			$notification_mails = $advertiser_mails = '';
			if(is_array($adrotate_notifications['notification_email_publisher'])) {
				$notification_mails	= implode(', ', $adrotate_notifications['notification_email_publisher']);
			}

			include("dashboard/settings/notifications.php");
		} elseif($active_tab == 'stats') {
			include("dashboard/settings/statistics.php");
		} elseif($active_tab == 'geo') {
			$adrotate_geo_requests = get_option("adrotate_geo_requests", 0);

			include("dashboard/settings/geotargeting.php");
		} elseif($active_tab == 'roles') {
			include("dashboard/settings/roles.php");
		} elseif($active_tab == 'misc') {
			include("dashboard/settings/misc.php");
		} elseif($active_tab == 'tools') {
			include("dashboard/settings/tools.php");
		} elseif($active_tab == 'maintenance') {
			$adrotate_version = get_option('adrotate_version');
			$adrotate_db_version = get_option('adrotate_db_version');
			$advert_status	= get_option("adrotate_advert_status");

			$adevaluate = wp_next_scheduled('adrotate_evaluate_ads');
			$adschedule = wp_next_scheduled('adrotate_notification');
			$trash = wp_next_scheduled('adrotate_empty_trash');
			$tracker = wp_next_scheduled('adrotate_empty_trackerdata');
			$autodelete = wp_next_scheduled('adrotate_auto_delete');

			include("dashboard/settings/maintenance.php");
		} elseif($active_tab == 'license') {
			$adrotate_is_networked = adrotate_is_networked();
			$adrotate_activate = adrotate_get_license();

			include("dashboard/settings/license.php");
		}
		?>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_advertiser
 Purpose:   Advertiser page
-------------------------------------------------------------*/
function adrotate_advertiser() {
	global $wpdb, $adrotate_config;

	$current_user = wp_get_current_user();
	$advertiser_permissions = get_user_meta($current_user->ID, 'adrotate_permissions', 1);
	if(!isset($advertiser_permissions['create'])) $advertiser_permissions['create'] = 'N';
	if(!isset($advertiser_permissions['edit'])) $advertiser_permissions['edit'] = 'N';
	if(!isset($advertiser_permissions['advanced'])) $advertiser_permissions['advanced'] = 'N';
	if(!isset($advertiser_permissions['geo'])) $advertiser_permissions['geo'] = 'N';
	if(!isset($advertiser_permissions['group'])) $advertiser_permissions['group'] = 'N';
	if(!isset($advertiser_permissions['schedules'])) $advertiser_permissions['schedules'] = 'N';

	$status = $view = $ad_edit_id = $filename = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	if(isset($_GET['file'])) $filename = esc_attr($_GET['file']);

	if(!is_numeric($status)) $status = 0;
	if(!is_numeric($ad_edit_id)) $ad_edit_id = 0;

	$now 			= current_time('timestamp');
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Advertiser', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status, array('file' => $filename)); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser');?>"><?php _e('Manage', 'adrotate-pro'); ?></a>
				<?php if($advertiser_permissions['create'] == 'Y') { ?>
				 | <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=addnew');?>"><?php _e('Add New', 'adrotate-pro'); ?></a>
				<?php  } ?>
			</div>
		</div>

		<?php
		if($view == "") {

			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = 0 AND `user` = '%d' ORDER BY `ad` ASC;", $current_user->ID));

			if($ads) {
				$activebanners = $queuebanners = $disabledbanners = false;
				foreach($ads as $ad) {
					$banner = $wpdb->get_row("SELECT `id`, `title`, `type`, `desktop`, `mobile`, `tablet`, `budget`, `crate`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' OR `type` = 'error' OR `type` = 'a_error' OR `type` = 'expired' OR `type` = 'queue' OR `type` = 'reject') AND `id` = '{$ad->ad}';");

					// Skip if no ad
					if(!$banner) continue;

					$starttime = $stoptime = 0;
					$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$banner->id}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
					$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$banner->id}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

					$type = $banner->type;
					if($type == 'active' AND $stoptime <= $now) $type = 'expired';
					if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
					if($type == 'active' AND $stoptime <= $in7days) $type = '7days';

					if($type == 'active' OR $type == '2days' OR $type == '7days' OR $type == 'expired') {
						$activebanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type,
							'desktop' => $banner->desktop,
							'mobile' => $banner->mobile,
							'tablet' => $banner->tablet,
							'firstactive' => $starttime,
							'lastactive' => $stoptime,
							'budget' => $banner->budget,
							'crate' => $banner->crate,
							'irate' => $banner->irate
						);
					}

					if($type == 'disabled') {
						$disabledbanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type
						);
					}

					if($type == 'queue' OR $type == 'reject' OR $type == 'error' OR $type == 'a_error') {
						$queuebanners[$banner->id] = array(
							'id' => $banner->id,
							'title' => $banner->title,
							'type' => $type,
							'desktop' => $banner->desktop,
							'mobile' => $banner->mobile,
							'tablet' => $banner->tablet,
							'budget' => $banner->budget,
							'crate' => $banner->crate,
							'irate' => $banner->irate
						);
					}
				}

				// Show active ads, if any
				if($activebanners) {
					include("dashboard/advertiser/main.php");
				}

				// Show disabled ads, if any
				if($disabledbanners) {
					include("dashboard/advertiser/main-disabled.php");
				}

				// Show queued ads, if any
				if($queuebanners) {
					include("dashboard/advertiser/main-queue.php");
				}

				if($adrotate_config['stats'] == 1) {
					// Gather data for summary report
					$stats = adrotate_prepare_advertiser_report($current_user->ID, $activebanners);
					$stats_graph_month = $wpdb->get_results($wpdb->prepare("SELECT SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate_stats`.`ad` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = '%d' AND `{$wpdb->prefix}adrotate_stats`.`thetime` >= '%d' AND `{$wpdb->prefix}adrotate_stats`.`thetime` <= '%d' GROUP BY `thetime` ORDER BY `thetime` ASC;", $current_user->ID, $monthstart, $monthend), ARRAY_A);

					// Prevent gaps in display
					if(empty($stats['ad_amount'])) $stats['ad_amount'] = 0;
					if(empty($stats['thebest'])) $stats['thebest'] = 0;
					if(empty($stats['theworst'])) $stats['theworst'] = 0;
					if(empty($stats['total_impressions'])) $stats['total_impressions'] = 0;
					if(empty($stats['total_clicks'])) $stats['total_clicks'] = 0;
					if(empty($stats_graph_month['impressions'])) $stats_graph_month['impressions'] = 0;
					if(empty($stats_graph_month['clicks'])) $stats_graph_month['clicks'] = 0;

					// Get Click Through Rate
					$ctr = adrotate_ctr($stats['total_clicks'], $stats['total_impressions']);
					$ctr_graph_month = adrotate_ctr($stats_graph_month['clicks'], $stats_graph_month['impressions']);

					include("dashboard/advertiser/main-summary.php");
				}

			} else {
				?>
				<table class="widefat" style="margin-top: .5em">
					<thead>
						<tr>
							<th><?php _e('Notice', 'adrotate-pro'); ?></th>
						</tr>
					</thead>
					<tbody>
					    <tr>
							<td><?php _e('No ads for user.', 'adrotate-pro'); ?></td>
						</tr>
					</tbody>
				</table>
				<?php
			}
		} else if($view == "addnew" OR $view == "edit") {

			include("dashboard/advertiser/edit.php");

		} else if($view == "report") {

			include("dashboard/advertiser/report.php");

		} else if($view == "message") {

			if(wp_verify_nonce($_REQUEST['_wpnonce'], 'adrotate_email_advertiser')) {
				include("dashboard/advertiser/message.php");
			} else {
				adrotate_nonce_error();
				exit;
			}

		}
		?>
		<br class="clear" />

		<?php adrotate_user_notice(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_network_settings
 Purpose:   Settings specific to network setups
-------------------------------------------------------------*/
function adrotate_network_settings() {
	global $wpdb;

	$status = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);

	if(!is_numeric($status)) $status = 0;
	$adrotate_network = get_site_option('adrotate_network_settings');
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Network Settings', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-network-settings">

			<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>

			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Primary site', 'adrotate-pro'); ?></th>
					<td><label for="adrotate_network_site_dashboard"><input name="adrotate_network_primary" type="text" class="search-input" size="5" value="<?php echo $adrotate_network['primary']; ?>" autocomplete="off" /> <?php _e('Enter the site id for the site you will publish adverts from. This is usually id 1.', 'adrotate-pro'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable dashboard on sites', 'adrotate-pro'); ?></th>
					<td><label for="adrotate_network_site_dashboard"><input type="checkbox" name="adrotate_network_site_dashboard" <?php if($adrotate_network['site_dashboard'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Enabled by default. This allows admins of those sites to create and manage adverts with AdRotate Pro just like you do from your primary site. Caution: May cause group id conflicts when using cross site adverts, use with care!', 'adrotate-pro'); ?></label></td>
				</tr>
			</table>

			<p class="submit">
			  	<input type="submit" name="adrotate_save_network_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
			</p>
		</form>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_network_license
 Purpose:   Network activated license dashboard
-------------------------------------------------------------*/
function adrotate_network_license() {
	global $wpdb;

	$status = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(!is_numeric($status)) $status = 0;
	$adrotate_activate = adrotate_get_license();
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e('Network License', 'adrotate-pro'); ?></h1>
		<hr class="wp-header-end">

		<?php if($status > 0) adrotate_status($status); ?>

	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-network-license">

			<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>

			<span class="description"><?php _e('Activate your AdRotate License here to receive automated updates and enable support via the fast and personal ticket system.', 'adrotate-pro'); ?><br />
			<?php _e('For network activated setups like this you need a Developer License.', 'adrotate-pro'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('License Type', 'adrotate-pro'); ?></th>
					<td>
						<?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'] : __('Not activated - Not eligible for support and updates.', 'adrotate-pro'); ?>
					</td>
				</tr>
				<?php if($adrotate_activate['created'] != 0 AND $adrotate_activate['created'] < current_time('timestamp') - (DAY_IN_SECONDS * 365)) { ?>
				<tr>
					<th valign="top"><?php _e('Notice', 'adrotate-pro'); ?></th>
					<td>
						<strong><?php _e('Your license has expired. In order to continue to receive updates, support and access to AdRotate Geo please get a new license.', 'adrotate-pro'); ?></strong><br /><?php _e('As a thank you for your continued use and support of AdRotate Pro you can get a new license at a special discounted price.', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/support/adrotate-manuals/adrotate-pro-license-renewal/" target="_blank"><?php _e('Get a new license', 'adrotate-pro'); ?> &raquo;</a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th valign="top"><?php _e('License Email', 'adrotate-pro'); ?></th>
					<td>
						<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used in your purchase of AdRotate Pro.', 'adrotate-pro'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Key', 'adrotate-pro'); ?></th>
					<td>
						<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate-pro'); ?></span>
					</td>
				</tr>
				<?php if($adrotate_activate['status'] == 1) { ?>
				<tr>
					<th valign="top"><?php _e('Force de-activate', 'adrotate-pro'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_license_force" /> <span class="description"><?php _e('If your license has expired you may need to force de-activate the old license before you can activate the new key.', 'adrotate-pro'); ?></span>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th valign="top">&nbsp;</th>
					<td>
						<?php if($adrotate_activate['status'] == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate', 'adrotate-pro'); ?>" class="button-primary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate', 'adrotate-pro'); ?>" class="button-secondary" />
						<?php } ?>
					</td>
				</tr>
			</table>
		</form>
		<br class="clear" />

		<?php echo adrotate_trademark(); ?>

	</div>
<?php
}
?>
