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
 Name:      adrotate_ad
 Purpose:   Show requested ad
-------------------------------------------------------------*/
function adrotate_ad($banner_id, $opt = null) {
	global $wpdb, $adrotate_config, $adrotate_crawlers;

	$output = '';

	if($banner_id) {
		$defaults = array(
			'wrapper' => 'yes', // Group wrapper (yes|no, Default mode)
			'site' => 'no' // Network site (yes|no)
		);
		$options = wp_parse_args($opt, $defaults);

		$license = adrotate_get_license();
		$network = get_site_option('adrotate_network_settings');

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}

		$banner = $wpdb->get_row($wpdb->prepare("SELECT `id`, `title`, `bannercode`, `tracker`, `show_everyone`, `image`, `crate`, `irate`, `budget` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days');", $banner_id));

		if($banner) {
			$selected = array($banner->id => 0);
			$selected = adrotate_filter_show_everyone($selected, $banner);
			$selected = adrotate_filter_schedule($selected, $banner);

			if($adrotate_config['enable_advertisers'] == 'Y' AND ($banner->crate > 0 OR $banner->irate > 0)) {
				$selected = adrotate_filter_budget($selected, $banner);
			}
		} else {
			$selected = false;
		}

		if($selected) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);

			if($options['wrapper'] == 'yes') $output .= '<div class="a'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.'">';
			$output .= adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
			if($options['wrapper'] == 'yes') $output .= '</div>';

			if($adrotate_config['stats'] == 1 AND ($banner->tracker == "Y" OR $banner->tracker == "I")) {
				adrotate_count_impression($banner->id, 0, $options['site']);
			}
		} else {
			$output .= adrotate_error('ad_expired', array('banner_id' => $banner_id));
		}
		unset($banner);

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}

	} else {
		$output .= adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_group
 Purpose:   Group output
-------------------------------------------------------------*/
function adrotate_group($group_ids, $opt = null) {
	global $wpdb, $adrotate_config;

	$output = $group_select = $weightoverride = $mobileoverride = $mobileosoverride = $showoverride = '';
	if($group_ids) {

		$defaults = array(
			'fallback' => 0, // Fallback group ID
			'weight' => 0, // Minimum weight (0, 1-10)
			'site' => 'no' // Network site (yes|no)
		);
		$options = wp_parse_args($opt, $defaults);

		$license = adrotate_get_license();
		$network = get_site_option('adrotate_network_settings');

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}

		$now = current_time('timestamp');

		$group_array = (preg_match('/,/is', $group_ids)) ? explode(",", $group_ids) : array($group_ids);
		$group_array = array_filter($group_array);

		foreach($group_array as $key => $value) {
			$group_select .= " `{$wpdb->prefix}adrotate_linkmeta`.`group` = ".$wpdb->prepare('%d', $value)." OR";
		}
		$group_select = rtrim($group_select, " OR");

		// Grab settings to use from first group
		$group = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `id` = %d;", $group_array[0]));

		if($group) {
			if($group->mobile == 1) {
				if(!adrotate_is_mobile() AND !adrotate_is_tablet()) { // Desktop
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`desktop` = 'Y'";
				} else if(adrotate_is_mobile()) { // Phones
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`mobile` = 'Y'";
				} else if(adrotate_is_tablet()) { // Tablets
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`tablet` = 'Y'";
				}

				if(!adrotate_is_ios() AND !adrotate_is_android()) { // Other OS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_other` = 'Y'";
				} else if(adrotate_is_ios()) { // iOS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_ios` = 'Y'";
				} else if(adrotate_is_android()) { // Android
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_android` = 'Y'";
				}
			}

			$weightoverride = ($options['weight'] > 0) ? "AND `{$wpdb->prefix}adrotate`.`weight` >= {$options['weight']} " : '';
			$options['fallback'] = ($options['fallback'] == 0) ? $group->fallback : $options['fallback'];

			// Get all ads in all selected groups
			$ads = $wpdb->get_results(
				"SELECT
					`{$wpdb->prefix}adrotate`.`id`, `title`, `bannercode`, `image`, `tracker`, `show_everyone`, `weight`,
					`crate`, `irate`, `budget`, `state_req`, `cities`, `states`, `countries`, `{$wpdb->prefix}adrotate_linkmeta`.`group`
				FROM
					`{$wpdb->prefix}adrotate`,
					`{$wpdb->prefix}adrotate_linkmeta`
				WHERE
					({$group_select})
					AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0
					AND `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad`
					{$mobileoverride}
					{$mobileosoverride}
					{$weightoverride}
					AND (`{$wpdb->prefix}adrotate`.`type` = 'active'
						OR `{$wpdb->prefix}adrotate`.`type` = '2days'
						OR `{$wpdb->prefix}adrotate`.`type` = '7days')
				GROUP BY `{$wpdb->prefix}adrotate`.`id`
				ORDER BY `{$wpdb->prefix}adrotate`.`id`;");

			if($ads) {
				foreach($ads as $ad) {
					$selected[$ad->id] = $ad;

					if($adrotate_config['duplicate_adverts_filter'] == 'Y') {

						if (is_home() AND !in_the_loop()) {
					    	$session_page = get_option('page_for_posts');
						} elseif (is_post_type_archive() OR is_category()){
							$session_page = get_query_var('cat');
						} else {
							$session_page = get_the_ID();
						}

						$session_page = 'adrotate-post-'.$session_page;
						$selected = adrotate_filter_duplicates($selected, $ad->id, $session_page);
					}

					$selected = adrotate_filter_show_everyone($selected, $ad);
					$selected = adrotate_filter_schedule($selected, $ad);

					if($adrotate_config['enable_advertisers'] == 'Y' AND ($ad->crate > 0 OR $ad->irate > 0)) {
						$selected = adrotate_filter_budget($selected, $ad);
					}

					if($adrotate_config['enable_geo'] > 0 AND $group->geo == 1) {
						$selected = adrotate_filter_location($selected, $ad);
					}
				}

				$array_count = count($selected);
				if($array_count > 0) {
					$before = $after = '';
					$before = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_before, ENT_QUOTES)));
					$after = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_after, ENT_QUOTES)));

					$output .= '<div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';

					// Kill dynamic mode for mobile users
					if($adrotate_config['mobile_dynamic_mode'] == 'Y' AND $group->modus == 1 AND wp_is_mobile()) {
						$group->modus = 0;
					}

					if($group->modus == 1) { // Dynamic ads
						$i = 1;

						// Limit group to save resources
						$amount = ($group->adspeed >= 10000) ? 10 : 20;

						// Randomize and trim output
						$selected = adrotate_shuffle($selected);
						foreach($selected as $key => $banner) {
							if($i <= $amount) {
								$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);
								$hide = ($i > 1) ? ' style="display: none;"' : '';

								$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-dyn a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.' c-'.$i.'"'.$hide.'>';
								$output .= $before.adrotate_ad_output($banner->id, $group->id, $banner->title, $banner->bannercode, $banner->tracker, $image).$after;
								$output .= '</div>';
								$i++;
							}
						}
					} else if($group->modus == 2) { // Block of ads
						$block_count = $group->gridcolumns * $group->gridrows;
						if($array_count < $block_count) $block_count = $array_count;
						$columns = 1;

						for($i=1;$i<=$block_count;$i++) {
							$banner_id = adrotate_pick_weight($selected);

							$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

							$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-col b'.$adrotate_config['adblock_disguise'].'-'.$group->id.' a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
							$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
							$output .= '</div>';

							if($columns == $group->gridcolumns AND $i != $block_count) {
								$output .= '</div><div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';
								$columns = 1;
							} else {
								$columns++;
							}

							if($adrotate_config['stats'] == 1 AND ($selected[$banner_id]->tracker == "Y" OR $selected[$banner_id]->tracker == "I")) {
								adrotate_count_impression($selected[$banner_id]->id, $group->id, $options['site']);
							}

							// Store advert ID's in session
							if($adrotate_config['duplicate_adverts_filter'] == 'Y') {
								$_SESSION['adrotate-duplicate-ads'][$session_page]['adverts'][] = $banner_id;
							}

							unset($selected[$banner_id]);
						}
					} else { // Default (single ad)
						$banner_id = adrotate_pick_weight($selected);

						$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

						$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
						$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
						$output .= '</div>';

						if($adrotate_config['stats'] == 1 AND ($selected[$banner_id]->tracker == "Y" OR $selected[$banner_id]->tracker == "I")) {
							adrotate_count_impression($selected[$banner_id]->id, $group->id, $options['site']);
						}

						// Store advert ID's in session
						if($adrotate_config['duplicate_adverts_filter'] == 'Y') {
							$_SESSION['adrotate-duplicate-ads'][$session_page]['adverts'][] = $banner_id;
						}
					}

					$output .= '</div>';

					unset($selected, $banner_id);
				} else {
					if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
						switch_to_blog($current_blog);
					}
					$output .= adrotate_fallback($options['fallback'], 'expired', $options['site']);
				}
			} else {
				if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
					switch_to_blog($current_blog);
				}
				$output .= adrotate_fallback($options['fallback'], 'unqualified', $options['site']);
			}
		} else {
			$output .= adrotate_error('group_not_found', array('group_id' => $group_array[0]));
		}

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}

	} else {
		$output .= adrotate_error('group_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_shortcode
 Purpose:   Prepare function requests for calls on shortcodes
-------------------------------------------------------------*/
function adrotate_shortcode($atts, $content = null) {
	global $adrotate_config;

	$banner_id = (!empty($atts['banner'])) ? trim($atts['banner'], "\r\t ") : 0;
	$group_ids = (!empty($atts['group'])) ? trim($atts['group'], "\r\t ") : 0;
	$fallback = (!empty($atts['fallback'])) ? trim($atts['fallback'], "\r\t "): 0; // Optional: for groups (ID)
	$weight	= (!empty($atts['weight']))	? trim($atts['weight'], "\r\t "): 0; // Optional: for groups (0, 1-10)
	$site = (!empty($atts['site'])) ? trim($atts['site'], "\r\t ") : 'no'; // Optional: for networks (yes|no)
	$wrapper = (!empty($atts['wrapper'])) ? trim($atts['wrapper'], "\r\t ") : 'yes'; // Optional: for inline advert (yes|no, single advert only)

	$output = '';
	if($adrotate_config['w3caching'] == "Y") {
		$output .= '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= 'echo adrotate_ad('.$banner_id.', array("wrapper" => "'.$wrapper.'", "site" => "'.$site.'"));';
		}

		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= 'echo adrotate_group('.$group_ids.', array("fallback" => '.$fallback.', "weight" => '.$weight.', "site" => "'.$site.'"));';
		}

		$output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
	} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper')) {
		if(BorlabsCacheHelper()->willFragmentCachingPerform()) {
			$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

			$output .= '<!--[borlabs cache start: '.$borlabsphrase.']--> ';
			if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
				$output .= 'echo adrotate_ad('.$banner_id.', array("wrapper" => "'.$wrapper.'", "site" => '.$site.'));';
			}
			if($banner_id == 0 AND $group_ids > 0) { // Show group
				$output .= 'echo adrotate_group('.$group_ids.', array("fallback" => '.$fallback.', "weight" => '.$weight.', "site" => "'.$site.'"));';
			}
			$output .= ' <!--[borlabs cache end: '.$borlabsphrase.']-->';

			unset($borlabsphrase);
		}
	} else {
		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= adrotate_ad($banner_id, array('wrapper' => $wrapper, 'site' => $site));
		}

		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= adrotate_group($group_ids, array('fallback' => $fallback, 'weight' => $weight, 'site' => $site));
		}
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_posts_cache_wrapper
 Purpose:   Wrap post injection return with caching code?
 Since:		5.10
-------------------------------------------------------------*/
function adrotate_inject_posts_cache_wrapper($group_id) {
	global $adrotate_config;

	if($adrotate_config['w3caching'] == 'Y') {
		$advert_output = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
		$advert_output .= 'echo adrotate_group('.$group_id.');';
		$advert_output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
	} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper')) {
		if(BorlabsCacheHelper()->willFragmentCachingPerform()) {
			$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

			$advert_output = '<!--[borlabs cache start: '.$borlabsphrase.']-->';
			$advert_output .= 'echo adrotate_group('.$group_id.');';
			$advert_output .= '<!--[borlabs cache end: '.$borlabsphrase.']-->';

			unset($borlabsphrase);
		}
	} else {
		$advert_output = adrotate_group($group_id);
	}

	return $advert_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_posts
 Purpose:   Add an advert to a single page or post
-------------------------------------------------------------*/
function adrotate_inject_posts($post_content) {
	global $wpdb, $post;

	$categories_top = $categories_bottom = $categories_inside = array();
	if(is_page()) {
		// Inject ads into pages
		$groups = $wpdb->get_results("SELECT `id`, `page`, `page_loc`, `page_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `page_loc` > 0 AND  `page_loc` < 5;");

		foreach($groups as $group) {
			$pages_more = explode(",", $group->page);

			if(count($pages_more) > 0) {
				if(in_array($post->ID, $pages_more)) {
					if($group->page_loc == 1 OR $group->page_loc == 3) {
						$categories_top[$group->id] = $group->page_par;
					}
					if($group->page_loc == 2 OR $group->page_loc == 3) {
						$categories_bottom[$group->id] = $group->page_par;
					}
					if($group->page_loc == 4) {
						$categories_inside[$group->id] = $group->page_par;
					}
					unset($pages_more, $group);
				}
			}
		}
	}

	if(is_single()) {
		// Inject ads into posts in specified category
		$groups = $wpdb->get_results("SELECT `id`, `cat`, `cat_loc`, `cat_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `cat_loc` > 0 AND `cat_loc` < 5;");
		$wp_categories = wp_get_post_categories($post->ID, array('taxonomy' => 'category', 'fields' => 'ids'));

		foreach($groups as $group) {
			$categories_more = array_intersect($wp_categories, explode(",", $group->cat));

			if(count($categories_more) > 0) {
				if(has_category($categories_more, $post->ID)) {
					if(($group->cat_loc == 1 OR $group->cat_loc == 3)) {
						$categories_top[$group->id] = $group->cat_par;
					}
					if($group->cat_loc == 2 OR $group->cat_loc == 3) {
						$categories_bottom[$group->id] = $group->cat_par;
					}
					if($group->cat_loc == 4) {
						$categories_inside[$group->id] = $group->cat_par;
					}
					unset($categories_more, $group);
				}
			}
		}
	}

	// Advert in front of content
	if(count($categories_top) > 0) {
		$post_content = adrotate_inject_posts_cache_wrapper(array_rand($categories_top)).$post_content;
	}

	// Advert behind the content
	if(count($categories_bottom) > 0) {
		$post_content = $post_content.adrotate_inject_posts_cache_wrapper(array_rand($categories_bottom));
	}

	// Adverts inside the content
	if(count($categories_inside) > 0) {
		// Setup
		$categories_inside = adrotate_shuffle($categories_inside);
	    $post_content_exploded = explode('</p>', $post_content);
		$post_content_count = ceil(count($post_content_exploded));
		$inserted = array();

		// Determine after which paragraphs ads should show
		foreach($categories_inside as $group_id => $group_paragraph) {
			if($group_paragraph == 99) {
				$insert_paragraph = intval($post_content_count / 2); // Middle of content
			} else if($group_paragraph == 100) {
				$insert_paragraph = 3;
			} else if($group_paragraph == 101) {
				$insert_paragraph = 4;
			} else if($group_paragraph == 102) {
				$insert_paragraph = 5;
			} else if($group_paragraph == 103) {
				$insert_paragraph = 6;
			} else if($group_paragraph == 104) {
				$insert_paragraph = 7;
			} else if($group_paragraph == 105) {
				$insert_paragraph = 8;
			} else if($group_paragraph == 110) {
				$insert_paragraph = intval(floor($post_content_count / 3)); // 30% down
			} else if($group_paragraph == 111) {
				$insert_paragraph = intval(floor($post_content_count / 3) * 2); // 60% down
			} else {
				$insert_paragraph = intval($group_paragraph);
			}

			// Create $inserted with paragraphs numbers and link the group to it. This list is leading from this point on.
			if($group_paragraph > 99 AND $group_paragraph < 110) {
				for($i=$insert_paragraph;$i<=($post_content_count-4);$i+=$insert_paragraph) {
					if(!array_key_exists($i, $inserted)) {
						$inserted[$i] = $group_id;
					}
				}
				unset($i);
			} else {
				if(!array_key_exists($insert_paragraph, $inserted)) {
					$inserted[$insert_paragraph] = $group_id;
				}
			}
			unset($group_id, $insert_paragraph, $group_paragraph);
		}

		// Inject ads behind paragraphs based on $inserted created above, IF a group_id is set higher than 0
		foreach($post_content_exploded as $index => $paragraph) {
			$insert_here = $index + 1; // Deal with array offset
			if(array_key_exists($insert_here, $inserted)) {
				if($inserted[$insert_here] > 0) {
					$post_content_exploded[$index] .= adrotate_inject_posts_cache_wrapper($inserted[$insert_here]);
					$inserted[$insert_here] = 0;
				}
			}
			unset($index, $paragraph, $insert_here);
		}

		// Re-assemble post_content and clean up
	    $post_content = implode('', $post_content_exploded);
		unset($post_content_exploded, $post_content_count, $inserted);

	}

	unset($groups, $categories_top, $categories_bottom, $categories_inside);

	return $post_content;
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_products
 Purpose:   Add adverts to a WooCommerce or Classic Commerce product page
 Since:		5.10
-------------------------------------------------------------*/
function adrotate_inject_products() {
	global $wpdb, $product;

	if(function_exists('is_product')) {
		if(is_product()) {
			$categories_top = $categories_bottom = array();

			// Inject ads into posts in specified product category
			$groups = $wpdb->get_results("SELECT `id`, `woo_cat`, `woo_loc` FROM `{$wpdb->prefix}adrotate_groups` WHERE `woo_loc` > 0 AND `woo_loc` < 4;");

			foreach($groups as $group) {
				$categories_more = array_intersect($product->get_category_ids(), explode(",", $group->woo_cat));

				if(count($categories_more) > 0) {
					if($group->woo_loc == 1 OR $group->woo_loc == 3) {
						$categories_top[$group->id] = $categories_more;
					}
					if($group->woo_loc == 2 OR $group->woo_loc == 3) {
						$categories_bottom[$group->id] = $categories_more;
					}
					unset($categories_more, $group);
				}
			}

			// Advert before the content
			if (count($categories_top) > 0 AND current_filter() == 'woocommerce_before_single_product') {
				echo adrotate_inject_posts_cache_wrapper(array_rand($categories_top));
			}

			// Advert behind the content
			if (count($categories_bottom) > 0 AND current_filter() == 'woocommerce_after_single_product') {
				echo adrotate_inject_posts_cache_wrapper(array_rand($categories_bottom));
			}

			unset($groups, $categories_top, $categories_bottom);
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_forums
 Purpose:   Add adverts to a bbPress forum page/topic
 Since:		5.10
-------------------------------------------------------------*/
function adrotate_inject_forums() {
	global $wpdb, $post;

	if(function_exists('is_bbpress')) {
		if(is_bbpress()) {
			$forums_top = $forums_bottom = array();

			// Inject ads into posts in specified forum
			$groups = $wpdb->get_results("SELECT `id`, `bbpress`, `bbpress_loc` FROM `{$wpdb->prefix}adrotate_groups` WHERE `bbpress_loc` > 0 AND `bbpress_loc` < 4;");

			foreach($groups as $group) {
				$forums_more = explode(",", $group->bbpress);

				if(count($forums_more) > 0) {
					if(in_array($post->ID, $forums_more) OR in_array($post->post_parent, $forums_more)) {
						if($group->bbpress_loc == 1 OR $group->bbpress_loc == 3) {
							$forums_top[$group->id] = $group->bbpress;
						}
						if($group->bbpress_loc == 2 OR $group->bbpress_loc == 3) {
							$forums_bottom[$group->id] = $group->bbpress;
						}
					}
					unset($forums_more, $group);
				}
			}

			// Advert before the content
			if(count($forums_top) > 0 AND (current_filter() == 'bbp_template_before_topics_loop' OR current_filter() == 'bbp_template_before_replies_loop')) {
				echo adrotate_inject_posts_cache_wrapper(array_rand($forums_top));
			}

			// Advert behind the content
			if(count($forums_bottom) > 0 AND (current_filter() == 'bbp_template_after_topics_loop' OR current_filter() == 'bbp_template_after_replies_loop')) {
				echo adrotate_inject_posts_cache_wrapper(array_rand($forums_bottom));
			}

			unset($groups, $forums_top, $forums_bottom);
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_preview
 Purpose:   Show preview of selected ad (Dashboard)
-------------------------------------------------------------*/
function adrotate_preview($banner_id) {
	global $wpdb, $adrotate_config;

	if($banner_id) {
		$now = current_time('timestamp');

		$banner = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $banner_id));

		if($banner) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);
			$output = adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
		} else {
			$output = adrotate_error('ad_expired', array('banner_id' => $banner_id));
		}
	} else {
		$output = adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_output
 Purpose:   Prepare the output for viewing
-------------------------------------------------------------*/
function adrotate_ad_output($id, $group, $name, $bannercode, $tracker, $image) {
	global $blog_id, $adrotate_config;

	$banner_output = $bannercode;
	$banner_output = stripslashes(htmlspecialchars_decode($banner_output, ENT_QUOTES));

	if($adrotate_config['stats'] > 0 AND $tracker != "N") {
		if(empty($blog_id) or $blog_id == '') {
			$blog_id = 0;
		}

		if($adrotate_config['stats'] == 1 AND ($tracker == "Y" OR $tracker == "C")) { // Internal tracker
			preg_match_all('/<a[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);
			if(isset($matches[0])) {
				$banner_output = str_replace('<a ', '<a data-track="'.adrotate_hash($id, $group, $blog_id).'" ', $banner_output);
				foreach($matches[0] as $value) {
					if(preg_match('/<a[^>]+class=\"(.+?)\"[^>]*>/i', $value, $regs)) {
					    $result = $regs[1]." gofollow";
						$banner_output = str_ireplace('class="'.$regs[1].'"', 'class="'.$result.'"', $banner_output);
					} else {
						$banner_output = str_ireplace('<a ', '<a class="gofollow" ', $banner_output);
					}
					unset($value, $regs, $result);
				}
			}
		}

		if($adrotate_config['stats'] >= 2 AND $adrotate_config['stats'] <= 5) { // Google Analytics || Matomo
			$click_event = $impression_event = '';
			preg_match_all('/<(?:a|img|iframe)[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);

			if(isset($matches[0])) {
				if($adrotate_config['stats'] == 2) { // Matomo
					if($tracker == "Y" OR $tracker == "C") {
						$click_event = "_paq.push(['trackEvent', 'Adverts', 'Click', '$name']);";
					}
					if($tracker == "Y" OR $tracker == "I") {
						$impression_event = "_paq.push(['trackEvent', 'Adverts', 'Impression', '$name']);";
					}
				}

				if($adrotate_config['stats'] == 3) { // gtag.js (Global Tag for GA4)
					if($tracker == "Y" OR $tracker == "C") {
						$click_event = "gtag('event', 'click', {'advert_name': '$name'});";
					}
					if($tracker == "Y" OR $tracker == "I") {
						$impression_event = "gtag('event', 'impression', {'advert_name': '$name'});";
					}
				}

				if($adrotate_config['stats'] == 5) { // gtm.js (Tag Manager for GA4)
					if($tracker == "Y" OR $tracker == "C") {
						$click_event = "dataLayer.push({'event': 'AdRotatePro', 'advert_interaction': 'click', 'advert_name': '$name'});";
					}
					if($tracker == "Y" OR $tracker == "I") {
						$impression_event = "dataLayer.push({'event': 'AdRotatePro', 'advert_interaction': 'impression', 'advert_name': '$name'});";
					}
				}

				if($adrotate_config['stats'] == 4) { // gtag.js (Global Site Tag - OLD)
					if($tracker == "Y" OR $tracker == "C") {
						$click_event = "gtag('event', 'click', {'event_category': 'Adverts', 'event_label': '$name', 'value': 1.00,  'non_interaction': true});";
					}
					if($tracker == "Y" OR $tracker == "I") {
						$impression_event = "gtag('event', 'impression', {'event_category': 'Adverts', 'event_label': '$name', 'value': 2.00, 'non_interaction': true});";
					}
				}

				// Image banner or Text banner
				if(strlen($click_event) > 0 AND stripos($banner_output, '<a') !== false) {
					if(!preg_match('/<a[^>]+onClick[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a onClick="'.$click_event.'" ', $banner_output);
					}
				}

				// Add tracking pixel (Most ads, including <iframe> and <ins> ads)?
				if(strlen($impression_event) > 0) {
					$banner_output .= '<img class="pixel" style="width:0 !important; height:0 !important;" width="0" height="0" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" onload="'.$impression_event.'" />';
				}
				unset($url, $img, $click_event, $impression_event);
			}
		}
		unset($matches);
	}

	$image = apply_filters('adrotate_apply_photon', $image);

	$banner_output = str_replace('%title%', $name, $banner_output);
	$banner_output = str_replace('%random%', rand(100000,999999), $banner_output);
	$banner_output = str_replace('%asset%', $image, $banner_output);
	$banner_output = str_replace('%id%', $id, $banner_output);
	$banner_output = do_shortcode($banner_output);

	return $banner_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_fallback
 Purpose:   Fall back to the set group or show an error if no fallback is set
-------------------------------------------------------------*/
function adrotate_fallback($group, $case, $site = 'no') {

	$fallback_output = '';
	if($group > 0) {
		$fallback_output = adrotate_group($group, array('site' => $site));
	} else {
		if($case == 'expired') {
			$fallback_output = adrotate_error('ad_expired', array('banner_id' => 'n/a'));
		}

		if($case == 'unqualified') {
			$fallback_output = adrotate_error('ad_unqualified');
		}
	}

	return $fallback_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_header
 Purpose:   Add required CSS to wp_head (action)
-------------------------------------------------------------*/
function adrotate_header() {

	$plugin_version = get_plugins();
	$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];

	$output = "\n<!-- This site is using AdRotate Professional v".$plugin_version." to display their advertisements - https://ajdg.solutions/ -->\n";
	$output .= adrotate_custom_css();

	$header = get_option('adrotate_header_output', false);
	if($header) {
		$header = stripslashes(htmlspecialchars_decode($header, ENT_QUOTES));
		$header = str_replace('%random%', rand(100000,999999), $header);
		$output .= $header."\n";
		unset($header);
	}

	$gam = get_option('adrotate_gam_output', false);
	if($gam) {
		$gam = stripslashes(htmlspecialchars_decode($gam, ENT_QUOTES));
		$gam = str_replace('%random%', rand(100000,999999), $gam);
		$output .= $gam."\n\n";
		unset($gam);
	}
	echo $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_css
 Purpose:   Add group CSS to adrotate_header()
-------------------------------------------------------------*/
function adrotate_custom_css() {
	global $wpdb, $adrotate_config;

	// Grab group settings from primary site
	$generated_css = $network_css = array();
	$license = adrotate_get_license();
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$network = get_site_option('adrotate_network_settings');
		$current_blog = $wpdb->blogid;

		switch_to_blog($network['primary']);
		$network_css = get_option('adrotate_group_css', array());
		switch_to_blog($current_blog);
	}

	$generated_css = array_merge(get_option('adrotate_group_css', array()), $network_css);

	$output = "";
	$output .= "<!-- AdRotate CSS -->\n";
	$output .= "<style type=\"text/css\" media=\"screen\">\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']." { margin:0px; padding:0px; overflow:hidden; line-height:1; zoom:1; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']." img { height:auto; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col { position:relative; float:left; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col:first-child { margin-left: 0; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col:last-child { margin-right: 0; }\n";
	$output .= "\t.woocommerce-page .g".$adrotate_config['adblock_disguise'].", .bbpress-wrapper .g".$adrotate_config['adblock_disguise']." { margin: 20px auto; clear:both; }\n";
	if($generated_css) {
		foreach($generated_css as $group_id => $css) {
			if(strlen($css) > 0) {
				$output .= $css;
			}
		}
		unset($generated_css);
	}
	$output .= "\t@media only screen and (max-width: 480px) {\n";
	$output .= "\t\t.g".$adrotate_config['adblock_disguise']."-col, .g".$adrotate_config['adblock_disguise']."-dyn, .g".$adrotate_config['adblock_disguise']."-single { width:100%; margin-left:0; margin-right:0; }\n";
	$output .= "\t\t.woocommerce-page .g".$adrotate_config['adblock_disguise'].", .bbpress-wrapper .g".$adrotate_config['adblock_disguise']." { margin: 10px auto; }\n";
	$output .= "\t}\n";
	if($adrotate_config['widgetpadding'] == "Y") {
		$advert_string = get_option('adrotate_dynamic_widgets_advert', 'temp_1');
		$group_string = get_option('adrotate_dynamic_widgets_group', 'temp_2');
		$output .= ".ajdg_bnnrwidgets, .ajdg_grpwidgets { overflow:hidden; padding:0; }\n";
		$output .= ".".$advert_string.", .".$group_string." { overflow:hidden; padding:0; }\n";
	}
	$output .= "</style>\n";
	$output .= "<!-- /AdRotate CSS -->\n\n";

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_scripts
 Purpose:   Add required scripts to wp_enqueue_scripts (action)
-------------------------------------------------------------*/
function adrotate_scripts() {
	global $adrotate_config;

	$in_footer = ($adrotate_config['jsfooter'] == "Y") ? true : false;

	if($adrotate_config['jquery'] == 'Y') {
		wp_enqueue_script('jquery', false, false, null, $in_footer);
	}

	if(get_option('adrotate_dynamic_required') > 0) {
		wp_enqueue_script('adrotate-dyngroup', plugins_url('/library/jquery.adrotate.dyngroup.js', __FILE__), false, null, $in_footer);
	}

	if($adrotate_config['stats'] == 1) {
		wp_enqueue_script('adrotate-clicktracker', plugins_url('/library/jquery.adrotate.clicktracker.js', __FILE__), false, null, $in_footer);
		wp_localize_script('adrotate-clicktracker', 'click_object', array('ajax_url' => admin_url('admin-ajax.php')));
		wp_localize_script('adrotate-dyngroup', 'impression_object', array('ajax_url' => admin_url( 'admin-ajax.php')));
	}

	if(!$in_footer) {
		add_action('wp_head', 'adrotate_custom_javascript');
	} else {
		add_action('wp_footer', 'adrotate_custom_javascript', 100);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_javascript
 Purpose:   Add required JavaScript to adrotate_scripts()
-------------------------------------------------------------*/
function adrotate_custom_javascript() {
	global $wpdb, $adrotate_config;

	$groups = $groups_network = array();
	// Grab group settings from primary site
	$network = get_site_option('adrotate_network_settings');
	$license = adrotate_get_license();
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$current_blog = $wpdb->blogid;
		switch_to_blog($network['primary']);
		$groups_network = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
		switch_to_blog($current_blog);
	}

	$groups = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
	$groups = array_merge($groups, $groups_network);

	if(count($groups) > 0) {
		$output = "<!-- AdRotate JS -->\n";
		$output .= "<script type=\"text/javascript\">\n";
		$output .= "jQuery(document).ready(function(){if(jQuery.fn.gslider) {\n";
		foreach($groups as $group) {
			$output .= "\tjQuery('.g".$adrotate_config['adblock_disguise']."-".$group['id']."').gslider({groupid:".$group['id'].",speed:".$group['adspeed'].",repeat_impressions:'".$group['repeat_impressions']."'});\n";
		}
		$output .= "}});\n";
		$output .= "</script>\n";
		$output .= "<!-- /AdRotate JS -->\n\n";
		unset($groups);
		echo $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_profile_fields
 Purpose:   Add profile fields to user creation and editing dashboards
-------------------------------------------------------------*/
function adrotate_custom_profile_fields($user) {
	global $adrotate_config;

    if(current_user_can('adrotate_advertiser_manage') AND $adrotate_config['enable_advertisers'] == 'Y') {
		if($user != 'add-new-user') {
		    $advertiser = get_user_meta($user->ID, 'adrotate_is_advertiser', 1);
		    $permissions = get_user_meta($user->ID, 'adrotate_permissions', 1);
		    // Check for gaps
		    if(empty($advertiser)) $advertiser = 'N';
		    if(empty($permissions)) $permissions = array('create' => 'N', 'edit' => 'N', 'advanced' => 'N', 'geo' => 'N', 'group' => 'N', 'schedule' => 'N');
			if(!isset($permissions['create'])) $permissions['create'] = 'N';
			if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
			if(!isset($permissions['advanced'])) $permissions['advanced'] = 'N';
			if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
			if(!isset($permissions['group'])) $permissions['group'] = 'N';
			if(!isset($permissions['schedule'])) $permissions['schedule'] = 'N';
		    $notes = get_user_meta($user->ID, 'adrotate_notes', 1);
		} else {
			$advertiser = 'N';
			$permissions = array('create' => 'N', 'edit' => 'N', 'advanced' => 'N', 'geo' => 'N', 'group' => 'N', 'schedule' => 'N');
			$notes = '';
		}
		?>
	    <h3><?php _e('AdRotate Advertiser', 'adrotate-pro'); ?></h3>
	    <table class="form-table">
	      	<tr>
		        <th valign="top"><?php _e('Enable', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_is_advertiser"><input tabindex="100" type="checkbox" name="adrotate_is_advertiser" <?php if($advertiser == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Is this user an AdRotate Advertiser?', 'adrotate-pro'); ?></label><br />
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Permissions', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_can_create"><input tabindex="101" type="checkbox" name="adrotate_can_create" <?php if($permissions['create'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can create adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_edit"><input tabindex="102" type="checkbox" name="adrotate_can_edit" <?php if($permissions['edit'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can edit their own adverts?', 'adrotate-pro'); ?></label>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Advert Features', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_can_advanced"><input tabindex="103" type="checkbox" name="adrotate_can_advanced" <?php if($permissions['advanced'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change advanced settings in adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_geo"><input tabindex="104" type="checkbox" name="adrotate_can_geo" <?php if($permissions['geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change Geo Targeting?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_group"><input tabindex="105" type="checkbox" name="adrotate_can_group" <?php if($permissions['group'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change schedules in adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_schedule"><input tabindex="106" type="checkbox" name="adrotate_can_schedule" <?php if($permissions['schedule'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change groups in adverts?', 'adrotate-pro'); ?></label>
		        </td>
	      	</tr>
		    <tr>
				<th valign="top"><label for="adrotate_notes"><?php _e('Notes', 'adrotate-pro'); ?></label></th>
				<td>
					<textarea tabindex="104" name="adrotate_notes" cols="50" rows="5"><?php echo esc_attr($notes); ?></textarea><br />
					<em><?php _e('Also visible in the advertiser profile.', 'adrotate-pro'); ?></em>
					</td>
			</tr>
	    </table>
<?php
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_nonce_error
 Purpose:   Display a formatted error if Nonce fails
-------------------------------------------------------------*/
function adrotate_nonce_error() {
	$message = 'WordPress was unable to verify the authenticity of the url you have clicked. Verify if the url used is valid or log in via your browser.<br />'.
	'Contact AdRotate Pro support if the issue persists: <a href="https://support.ajdg.net" title="AdRotate Support" target="_blank">AJdG Solutions Support</a>.';
	wp_die($message);
}

/*-------------------------------------------------------------
 Name:      adrotate_error
 Purpose:   Show errors for problems in using AdRotate, should they occur
-------------------------------------------------------------*/
function adrotate_error($action, $arg = null) {
	switch($action) {
		// Ads
		case "ad_expired" :
			$result = '<!-- '.sprintf(__('Error, Ad (%s) is not available at this time due to schedule/budgeting/geolocation/mobile restrictions!', 'adrotate-pro'), $arg['banner_id']).' -->';
			return $result;
		break;

		case "ad_unqualified" :
			$result = '<!-- '.__('Either there are no banners, they are disabled or none qualified for this location!', 'adrotate-pro').' -->';
			return $result;
		break;

		case "ad_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no Ad ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		// Groups
		case "group_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no group ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		case "group_not_found" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, group does not exist! Check your syntax!', 'adrotate-pro').' (ID: '.$arg['group_id'].')</span>';
			return $result;
		break;

		// Database
		case "db_error" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('There was an error locating the database tables for AdRotate. Please deactivate and re-activate AdRotate from the plugin page!!', 'adrotate-pro').'<br />'.__('If this does not solve the issue please create a support ticket at', 'adrotate-pro').' <a href="https://support.ajdg.net" target="_blank">Ticket support</a></span>';
			return $result;
		break;

		// Possible XSS or malformed URL
		case "error_loading_item" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('There was an error loading the page. Please try again by reloading the page via the menu on the left.', 'adrotate').'<br />'.__('If the issue persists please seek help at', 'adrotate').' <a href="https://support.ajdg.net" target="_blank">Ticket support</a></span>';
			return $result;
		break;

		// Misc
		default:
			$result = '<span style="font-weight: bold; color: #f00;">'.__('An unknown error occured.', 'adrotate-pro').'</span>';
			return $result;
		break;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_error
 Purpose:   Show errors for problems in using AdRotate
-------------------------------------------------------------*/
function adrotate_dashboard_error() {
	global $wpdb, $adrotate_config;

	$oneyear = current_time('timestamp') - (DAY_IN_SECONDS * 365);

	// License
	$license = adrotate_get_license();
	if($license['status'] == 0) {
		$error['adrotate_license'] = __('You did not yet activate your AdRotate Professional license. Activate and get updates, premium support and access to AdRotate Geo!', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>.';
	}

	// Adverts
	$status = get_option('adrotate_advert_status');
	$adrotate_notifications	= get_option("adrotate_notifications");
	if($adrotate_notifications['notification_dash'] == "Y") {
		if($status['expired'] > 0 AND $adrotate_notifications['notification_dash_expired'] == "Y") {
			$error['advert_expired'] = sprintf(_n('One advert is expired.', '%1$s adverts expired!', $status['expired'], 'adrotate-pro'), $status['expired']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
		if($status['expiressoon'] > 0 AND $adrotate_notifications['notification_dash_soon'] == "Y") {
			$error['advert_soon'] = sprintf(_n('One advert expires in less than 2 days.', '%1$s adverts are expiring in less than 2 days!', $status['expiressoon'], 'adrotate-pro'), $status['expiressoon']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
		if($status['expiresweek'] > 0 AND $adrotate_notifications['notification_dash_week'] == "Y") {
			$error['advert_week'] = sprintf(_n('One advert expires in less than a week.', '%1$s adverts are expiring in less than a week!', $status['expiresweek'], 'adrotate-pro'), $status['expiresweek']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
	}
	if($status['error'] > 0) {
		$error['advert_config'] = sprintf(_n('One advert with configuration errors.', '%1$s adverts have configuration errors!', $status['error'], 'adrotate-pro'), $status['error']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
	}

	// Schedules
	if($adrotate_notifications['notification_dash'] == "Y") {
		if($adrotate_notifications['notification_schedules'] == "Y") {
			$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' ORDER BY `id` ASC;");
			if($schedules) {
				$now = current_time('timestamp');
				$in2days = $now + 172800;
				$schedule_warning = array();

				foreach($schedules as $schedule) {
					if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions == 0) $schedule_warning[] = $schedule->id;
					if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions < 2000) $schedule_warning[] = $schedule->id;
					if($schedule->day_mon == 'N' AND $schedule->day_tue == 'N' AND $schedule->day_wed == 'N' AND $schedule->day_thu == 'N' AND $schedule->day_fri == 'N' AND $schedule->day_sat == 'N' AND $schedule->day_sun == 'N') $schedule_warning[] = $schedule->id;
//					if($schedule->stoptime < $in2days) $schedule_warning[] = $schedule->id;
//					if($schedule->stoptime < $now) $schedule_warning[] = $schedule->id;
				}

				$schedule_warning = count(array_unique($schedule_warning));

				if($schedule_warning > 0) {
					$error['schedule_warning'] = sprintf(_n('One schedule has a warning.', '%1$s schedules have warnings!', $schedule_warning, 'adrotate-pro'), $schedule_warning).' <a href="'.admin_url('admin.php?page=adrotate-schedules').'">'.__('Check schedules', 'adrotate-pro').'</a>.';
				}
			}
			unset($schedule_warning, $schedules);
		}
	}

	// Caching
	if($adrotate_config['w3caching'] == "Y" AND !is_plugin_active('w3-total-cache/w3-total-cache.php')) {
		$error['w3tc_not_active'] = __('You have enabled caching support but W3 Total Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable W3 Total Cache Support', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['w3caching'] == "Y" AND !defined('W3TC_DYNAMIC_SECURITY')) {
		$error['w3tc_no_hash'] = __('You have enable caching support but the W3TC_DYNAMIC_SECURITY definition is not set.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('How to configure W3 Total Cache', 'adrotate-pro').'</a>.';
	}

	if($adrotate_config['borlabscache'] == "Y" AND !is_plugin_active('borlabs-cache/borlabs-cache.php')) {
		$error['borlabs_not_active'] = __('You have enable caching support but Borlabs Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable Borlabs Cache Support', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['borlabscache'] == "Y" AND is_plugin_active('borlabs-cache/borlabs-cache.php')) {
		$borlabs_config = get_option('BorlabsCacheConfigInactive');
		if($borlabs_config['cacheActivated'] == 'yes' AND strlen($borlabs_config['fragmentCaching']) < 1) {
			$error['borlabs_fragment_error'] = __('You have enabled Borlabs Cache support but Fragment caching is not enabled!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=borlabs-cache-fragments').'">'.__('Enable Fragment Caching', 'adrotate-pro').'</a>.';
		}
		unset($borlabs_config);
	}

	// Notifications
	if($adrotate_notifications['notification_email'] == 'Y' AND $adrotate_notifications['notification_mail_geo'] == 'N' AND $adrotate_notifications['notification_mail_status'] == 'N' AND $adrotate_notifications['notification_mail_queue'] == 'N' AND $adrotate_notifications['notification_mail_approved'] == 'N' AND $adrotate_notifications['notification_mail_rejected'] == 'N') {
		$error['mail_not_configured'] = __('You have enabled email notifications but did not select anything to be notified about. You are wasting server resources!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=notifications').'">'.__('Set up notifications', 'adrotate-pro').'</a>.';
	}

	// Geo Related
	$lookups = get_option('adrotate_geo_requests');

	if($license['status'] == 0 AND $adrotate_config['enable_geo'] == 5) {
		$error['geo_license'] = __('The AdRotate Geo service can only be used after you activate your license for this website.', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>!';
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4 OR $adrotate_config['enable_geo'] == 5) AND $lookups > 0 AND $lookups < 1000) {
		$error['geo_almostnolookups'] = sprintf(__('You are running out of Geo Targeting Lookups. You have less than %d remaining lookups.', 'adrotate-pro'), $lookups);
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND $lookups < 1) {
		$error['geo_nolookups'] = __('Geo Targeting is no longer working because you have no more lookups.', 'adrotate-pro');
	}
	if($adrotate_config['enable_geo'] == 5 AND $lookups < 1) {
		$error['geo_nolookups'] = __('AdRotate Geo is no longer working because you have no more lookups for today. This resets at midnight UTC/GMT.', 'adrotate-pro');
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND (strlen($adrotate_config["geo_email"]) < 1 OR strlen($adrotate_config["geo_pass"]) < 1)) {
		$error['geo_maxmind_details'] = __('Geo Targeting is not working because your MaxMind account details are incomplete.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Enter MaxMind account details', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['enable_geo'] == 6 AND !isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
		$error['geo_cloudflare_header'] = __('Geo Targeting is not working. Check if IP Geolocation is enabled in your CloudFlare account.', 'adrotate-pro');
	}
	if($adrotate_config['enable_geo'] == 7 AND strlen($adrotate_config["geo_pass"]) < 1) {
		$error['geo_ipstack_details'] = __('Geo Targeting is not working because your ipstack account API key is missing.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Enter API key', 'adrotate-pro').'</a>.';
	}

	// Misc
	if(!is_writable(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'])) {
		$error['banners_folder'] = __('Your AdRotate Banner folder is not writable or does not exist.', 'adrotate-pro').' <a href="https://ajdg.solutions/support/adrotate-manuals/manage-banner-images/" target="_blank">'.__('Set up your banner folder', 'adrotate-pro').'</a>.';
	}
	if(is_dir(WP_PLUGIN_DIR."/adrotate/")) {
		$error['adrotate_free_version_exists'] = __('You still have the free version of AdRotate installed. Please remove it!', 'adrotate-pro').' <a href="'.admin_url('/plugins.php?s=adrotate&plugin_status=all').'">'.__('Delete AdRotate plugin', 'adrotate-pro').'</a>.';
	}
	if(basename(__DIR__) != 'adrotate' AND basename(__DIR__) != 'adrotate-pro') {
		$error['adrotate_folder_names'] = __('Something is wrong with your installation of AdRotate Pro. Either the plugin is installed twice or your current installation has the wrong folder name. Please install the plugin properly!', 'adrotate-pro').' <a href="https://ajdg.solutions/support/adrotate-manuals/installing-adrotate-on-your-website/" target="_blank">'.__('Installation instructions', 'adrotate-pro').'</a>.';
	}

	$error = (isset($error) AND is_array($error)) ? $error : false;

	return $error;
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications_dashboard
 Purpose:   Show dashboard notifications
-------------------------------------------------------------*/
function adrotate_notifications_dashboard() {
	global $current_user;

	if(current_user_can('adrotate_ad_manage')) {
		$displayname = (strlen($current_user->user_firstname) > 0) ? $current_user->user_firstname : $current_user->display_name;
		$page = (isset($_GET['page'])) ? $_GET['page'] : '';

		// These only show on AdRotate pages
		if(strpos($page, 'adrotate') !== false) {
			if(isset($_GET['hide']) AND $_GET['hide'] == 0) update_option('adrotate_hide_update', current_time('timestamp') + (7 * DAY_IN_SECONDS));
			if(isset($_GET['hide']) AND $_GET['hide'] == 1) update_option('adrotate_hide_review', 1);
			if(isset($_GET['hide']) AND $_GET['hide'] == 2) update_option('adrotate_hide_birthday', current_time('timestamp') + (10 * MONTH_IN_SECONDS));

			// Write a review
			$review_banner = get_option('adrotate_hide_review');
			$license = adrotate_get_license();
			if($license['status'] == 1 AND $review_banner != 1 AND $review_banner < (current_time('timestamp') - (8 * DAY_IN_SECONDS))) {
				$license = (!$license) ? 'single' : strtolower($license['type']);
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">Hello <strong>'.$displayname.'</strong>! You have been using <strong>AdRotate Professional</strong> for a few days. If you like the plugin, please share <strong>your experience</strong> and help promote AdRotate Pro.<br />Tell your followers that you use AdRotate Pro. A <a href="https://twitter.com/intent/tweet?hashtags=wordpress%2Cplugin%2Cadvertising&related=arnandegans%2Cwordpress&text=I%20am%20using%20AdRotate%20for%20@WordPress.%20Check%20it%20out.&url=https%3A%2F%2Fwordpress.org/plugins/adrotate/" target="_blank" class="ajdg-notification-act goosebox">Tweet</a> or <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwordpress.org%2Fplugins%2Fadrotate%2F&amp;src=adrotate" target="_blank" class="ajdg-notification-act goosebox">Facebook Share</a> helps a lot and is super awesome!<br />If you have questions, complaints or something else that does not belong in a review, please use the <a href="'.admin_url('admin.php?page=adrotate-support').'">contact form</a>!</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="https://ajdg.solutions/product/adrotate-pro-'.$license.'/?mtm_campaign=adrotatepro&mtm_keyword=review_notification#tab-reviews" class="ajdg-notification-act button-primary">Review AdRotate</a>';
				echo '		<a href="admin.php?page=adrotate&hide=1" class="ajdg-notification-dismiss">Maybe later</a>';
				echo '	</div>';
				echo '</div>';
			}

			// Birthday
			$birthday_banner = get_option('adrotate_hide_birthday');
			if($birthday_banner < current_time('timestamp') AND date('M', current_time('timestamp')) == 'Feb') {
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/birthday.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">Hey <strong>'.$displayname.'</strong>! Did you know it is Arnan his birtyday this month? February 9th to be exact. Wish him a happy birthday via Telegram!<br />Who is Arnan? He made AdRotate for you - Check out his <a href="https://www.arnan.me/?mtm_campaign=adrotatepro&mtm_keyword=birthday_banner" target="_blank">website</a> or <a href="https://www.arnan.me/donate.html?mtm_campaign=adrotatepro&mtm_keyword=birthday_banner" target="_blank">send a gift</a>.</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="https://t.me/arnandegans" target="_blank" class="ajdg-notification-act button-primary goosebox"><i class="icn-tg"></i>Wish Happy Birthday</a>';
				echo '		<a href="admin.php?page=adrotate&hide=2" class="ajdg-notification-dismiss">Done it</a>';
				echo '	</div>';
				echo '</div>';
			}
		}

		// Advert notifications, errors, important stuff
		$adrotate_has_error = adrotate_dashboard_error();
		if($adrotate_has_error) {
			echo '<div class="ajdg-notification notice" style="">';
			echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
			echo '	<div class="ajdg-notification-message"><strong>AdRotate Professional</strong> has detected '._n('one issue that requires', 'several issues that require', count($adrotate_has_error), 'adrotate-pro').' '.__('your attention:', 'adrotate').'<br />';
			foreach($adrotate_has_error as $error => $message) {
				echo '&raquo; '.$message.'<br />';
			}
			echo '	</div>';
			echo '</div>';
		}

		if(current_user_can('update_plugins')) {
			// Finish update
			// Keep for manual updates
			$adrotate_db_version = get_option("adrotate_db_version");
			$adrotate_version = get_option("adrotate_version");
			if($adrotate_db_version['current'] < ADROTATE_DB_VERSION OR $adrotate_version['current'] < ADROTATE_VERSION) {
				$plugin_version = get_plugins();
				$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];
	
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">Thanks for updating <strong>'.$displayname.'</strong>! You have almost completed updating <strong>AdRotate Professional</strong> to version <strong>'.$plugin_version.'</strong>!<br />To complete the update <strong>click the button on the right</strong>. This may take a few seconds to complete!<br />For an overview of what has changed take a look at the <a href="https://ajdg.solutions/support/adrotate-development/?mtm_campaign=adrotatepro&mtm_keyword=finish_update_notification" target="_blank">development page</a> and usually there is an article on <a href="https://ajdg.solutions/blog/" target="_blank">the blog</a> with more information as well.</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="admin.php?page=adrotate-settings&tab=maintenance&action=update-db" class="ajdg-notification-act button-primary update-button">Finish update</a>';
				echo '	</div>';
				echo '</div>';
			}
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_welcome_pointer
 Purpose:   Show dashboard pointers
-------------------------------------------------------------*/
function adrotate_welcome_pointer() {
	$plugin_version = get_plugins();
	$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];

    $pointer_content = '<h3>AdRotate Professional '.$plugin_version.'</h3>';
    $pointer_content .= '<p>'.__('Thanks for choosing AdRotate Professional. Everything related to AdRotate is in this menu. If you need help getting started take a look at the', 'adrotate-pro').' <a href="http:\/\/ajdg.solutions\/support\/adrotate-manuals\/" target="_blank">'.__('manuals', 'adrotate-pro').'</a> '.__('and', 'adrotate-pro').' <a href="https:\/\/ajdg.solutions\/forums\/forum\/adrotate-for-wordpress\/" target="_blank">'.__('forums', 'adrotate-pro').'</a>. '.__('You can also ask questions via', 'adrotate-pro').' <a href="admin.php?page=adrotate-support">'.__('email', 'adrotate-pro').'</a> '.__('if you have a valid license.', 'adrotate-pro').' These links and more are also available in the help tab in the top right.</p>';

    $pointer_content .= '<p><strong>Ad blockers</strong><br />Disable your ad blocker in your browser so your adverts and dashboard show up correctly. Take a look at this manual to <a href="https://ajdg.solutions/support/adrotate-manuals/configure-adblockers-for-your-own-website/" target="_blank">whitelist your site</a>.</p>';
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#toplevel_page_adrotate').pointer({
				'content':'<?php echo $pointer_content; ?>',
				'position':{ 'edge':'left', 'align':'middle'	},
				close: function() {
	                $.post(ajaxurl, {
	                    pointer:'adrotate_pro',
	                    action:'dismiss-wp-pointer'
	                });
				}
			}).pointer("open");
		});
	</script>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_action_links
 Purpose:	Plugin page link
-------------------------------------------------------------*/
function adrotate_action_links($links) {
	$custom_actions = array();
	$custom_actions['adrotate-help'] = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=adrotate-support'), 'Support');
	$custom_actions['adrotate-news'] = sprintf('<a href="%s">%s</a>', 'https://ajdg.solutions/blog/?mtm_campaign=adrotatepro&mtm_keyword=action_links', 'News');
	$custom_actions['adrotate-ajdg'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/?mtm_campaign=adrotatepro&mtm_keyword=action_links', 'AJdG Solutions');

	return array_merge($custom_actions, $links);
}

/*-------------------------------------------------------------
 Name:      adrotate_user_notice
 Purpose:   Credits shown on user statistics
-------------------------------------------------------------*/
function adrotate_user_notice() {

	echo '<table class="widefat" style="margin-top: .5em">';

	echo '<thead>';
	echo '<tr valign="top">';
	echo '	<th colspan="2">'.__('Your adverts', 'adrotate-pro').'</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	echo '<tr>';
	echo '<td><center><a href="https://ajdg.solutions/product-category/adrotate-pro/?mtm_campaign=adrotatepro&mtm_keyword=credits" title="AdRotate plugin for WordPress"><img src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt="logo-60x60" width="60" height="60" /></a></center></td>';
	echo '<td>'.__('The overall stats do not take adverts from other advertisers into account.', 'adrotate-pro').'<br />'.__('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro').'<br />'.__('Your ads are published with', 'adrotate-pro').' <a href="https://ajdg.solutions/product-category/adrotate-pro/?mtm_campaign=adrotatepro&mtm_keyword=credits" target="_blank">AdRotate Professional for WordPress</a>.</td>';

	echo '</tr>';
	echo '</tbody>';

	echo '</table>';
	echo adrotate_trademark();
}

/*-------------------------------------------------------------
 Name:      adrotate_trademark
 Purpose:   Trademark notice
-------------------------------------------------------------*/
function adrotate_trademark() {
 return '<center><small>AdRotate<sup>&reg;</sup> '.__('is a registered trademark', 'adrotate-pro').'</small></center>';
}
?>
