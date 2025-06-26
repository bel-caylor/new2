<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2024 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotate_network_widget() {
	register_widget('ajdg_nwidgets');
}

/*-------------------------------------------------------------
 Name:      ajdg_nwidgets
 Since:		3.19
-------------------------------------------------------------*/
class ajdg_nwidgets extends WP_Widget {

	/*-------------------------------------------------------------
	 Purpose:   Construct the widget
	-------------------------------------------------------------*/
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'ajdg_nwidgets',
			'description' => 'Show a group of adverts from the network in any widget area.',
		);
		parent::__construct( 'ajdg_nwidgets', 'AdRotate Network', $widget_ops );
	}

	/*-------------------------------------------------------------
	 Purpose:   Display the widget
	-------------------------------------------------------------*/
	public function widget($args, $instance) {
		global $adrotate_config, $post, $blog_id;

		extract($args);
		if(empty($instance['title'])) $instance['title'] = '';
		if(empty($instance['groupid'])) $instance['groupid'] = 0;
		$instance['before'] = (empty($instance['before'])) ? '' : stripslashes(htmlspecialchars_decode($instance['before'], ENT_QUOTES)).'.';
		$instance['after'] = (empty($instance['after'])) ? '' : '.'.stripslashes(htmlspecialchars_decode($instance['after'], ENT_QUOTES));

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if($title) {
			echo $before_title . $title . $after_title;
		}
		
		if($adrotate_config['widgetalign'] == 'Y') echo '<ul><li>';

		if($adrotate_config['w3caching'] == 'Y') {
			echo '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
			echo 'echo '.$instance['before'].'adrotate_group('.$instance['groupid'].', 0, 0, 1)'.$instance['after'].';';
			echo '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
		} else if($adrotate_config['borlabscache'] == "Y") {
			if(function_exists('BorlabsCacheHelper') AND BorlabsCacheHelper()->willFragmentCachingPerform()) {
				$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();
		
				echo '<!--[borlabs cache start: '.$borlabsphrase.']--> ';
				echo $instance['before'].adrotate_group($instance['groupid'], 0, 0, 1).$instance['after'];
				echo ' <!--[borlabs cache end: '.$borlabsphrase.']-->';
	
				unset($borlabsphrase);
			}
		} else {
			echo $instance['before'].adrotate_group($instance['groupid'], 0, 0, 1).$instance['after'];
		}
				
		if($adrotate_config['widgetalign'] == 'Y') echo '</li></ul>';
		
		echo $after_widget;
	}

	/*-------------------------------------------------------------
	 Purpose:   Save the widget options per instance
	-------------------------------------------------------------*/
	public function update($new_instance, $old_instance) {
		global $wpdb;

		$new_instance['title'] = strip_tags($new_instance['title']);
		$new_instance['groupid'] = strip_tags($new_instance['groupid']);
		$new_instance['before'] = htmlspecialchars(trim($new_instance['before'], "\t\n "), ENT_QUOTES);
		$new_instance['after'] = htmlspecialchars(trim($new_instance['after'], "\t\n "), ENT_QUOTES);

		// Grab group settings from primary site
		$networked = get_site_option('adrotate_network_settings');
		if($new_instance['network'] == 1 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($networked['primary']);
		}

		$group = $wpdb->get_row("SELECT `cat`, `cat_loc`, `page`, `page_loc` FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = {$new_instance['groupid']};");

		if(adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}

		$instance = wp_parse_args($new_instance, $old_instance);

		return $instance;
	}

	/*-------------------------------------------------------------
	 Purpose:   Display the widget options for admins
	-------------------------------------------------------------*/
	public function form($instance) {
		global $wpdb, $blog_id;

		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$license = (!adrotate_is_networked()) ? get_option('adrotate_activate') : get_site_option('adrotate_activate');
		
		$title = $groupid = $categories = $pages = $before = $after = '';
		extract($instance);
		$title = esc_attr($title);
		$groupid = esc_attr($groupid);
		$before = esc_attr($before);
		$after = esc_attr($after);
		
		$groups = $groups_network = array();

		// Grab group settings from primary site
		$networked = get_site_option('adrotate_network_settings');

		if(adrotate_is_networked() AND $license['type'] == 'Developer') {
			// Get groups from network
			if($networked['primary'] != $blog_id) {
				$current_blog = $wpdb->blogid;
				switch_to_blog($networked['primary']);

				$groups_network = $wpdb->get_results("SELECT `id`, `name` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");

				foreach($groups_network as $group) {
					$groups[$group->id] = $group->name;
				}

				switch_to_blog($current_blog);
			}
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):', 'adrotate-pro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			<br />
			<small><?php _e('HTML will be stripped out.', 'adrotate-pro'); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('groupid'); ?>"><?php _e('Group:', 'adrotate-pro'); ?></label><br />
			<select id="<?php echo $this->get_field_id('groupid'); ?>" name="<?php echo $this->get_field_name('groupid'); ?>">
			<?php if($groups) { ?>
			        <option value="0">-- Groups from network --</option>
				<?php foreach($groups as $id => $title) { ?>
			        <option value="<?php echo $id;?>" <?php if($groupid == $id) { echo 'selected'; } ?>><?php echo $id;?> - <?php echo $title; ?></option>
	 			<?php } ?>
			<?php } else { ?>
			        <option value="0" disabled="1">-- No groups found --</option>
			<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('before'); ?>"><?php _e('Wrapper before:', 'adrotate-pro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('before'); ?>" name="<?php echo $this->get_field_name('before'); ?>" type="text" value="<?php echo $before; ?>" />
			<br /><label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('Wrapper after:', 'adrotate-pro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('after'); ?>" name="<?php echo $this->get_field_name('after'); ?>" type="text" value="<?php echo $after; ?>" />
			<br /><small><?php _e('Simple HTML to center an advert or apply a paragraph for example.', 'adrotate-pro'); ?></small>
		</p>
<?php
	}
}
?>