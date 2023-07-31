<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2021 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

function adrotate_widgets() {
	register_widget('adrotate_advert_widget');
	register_widget('adrotate_group_widget');
}

/*-------------------------------------------------------------
 Name:      ajdg_grp_widgets
 Since:		3.19
-------------------------------------------------------------*/
class adrotate_advert_widget extends WP_Widget {

	/*-------------------------------------------------------------
	 Purpose:   Construct the widget
	-------------------------------------------------------------*/
	public function __construct() {
		$widget_string = get_option('adrotate_dynamic_widgets_advert', 'temp_1');
		$widget_ops = array( 
			'classname' => $widget_string,
			'description' => 'Show a single advert in any widget area.',
		);
		parent::__construct($widget_string, 'AdRotate Advert', $widget_ops);
	}

	/*-------------------------------------------------------------
	 Purpose:   Display the widget
	-------------------------------------------------------------*/
	public function widget($args, $instance) {
		global $adrotate_config, $blog_id;

		extract($args);
		if(empty($instance['title'])) $instance['title'] = '';
		if(empty($instance['adid'])) $instance['adid'] = 0;
		$instance['before'] = (empty($instance['before'])) ? '' : stripslashes(htmlspecialchars_decode($instance['before'], ENT_QUOTES));
		$instance['after'] = (empty($instance['after'])) ? '' : stripslashes(htmlspecialchars_decode($instance['after'], ENT_QUOTES));

        $title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if($title) {
			echo $before_title . $title . $after_title;
		}
		
		if($adrotate_config['widgetalign'] == 'Y') echo '<ul><li>';

		if($adrotate_config['w3caching'] == 'Y') {
			echo '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
			echo 'echo '.$instance['before'].'adrotate_ad('.$instance['adid'].', true)'.$instance['after'].';';
			echo '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
		} else if($adrotate_config['borlabscache'] == "Y") {
			if(function_exists('BorlabsCacheHelper') AND BorlabsCacheHelper()->willFragmentCachingPerform()) {
				$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();
		
				echo '<!--[borlabs cache start: '.$borlabsphrase.']-->';
				echo 'echo '.$instance['before'].'adrotate_ad('.$instance['adid'].', true)'.$instance['after'].';';
				echo ' <!--[borlabs cache end: '.$borlabsphrase.']-->';
	
				unset($borlabsphrase);
			}
		} else {
			echo $instance['before'].adrotate_ad($instance['adid'], true).$instance['after'];
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
		$new_instance['adid'] = strip_tags($new_instance['adid']);
		$new_instance['before'] = htmlspecialchars(trim($new_instance['before'], "\t\n "), ENT_QUOTES);
		$new_instance['after'] = htmlspecialchars(trim($new_instance['after'], "\t\n "), ENT_QUOTES);

		$instance = wp_parse_args($new_instance, $old_instance);

		return $instance;
	}

	/*-------------------------------------------------------------
	 Purpose:   Display the widget options for admins
	-------------------------------------------------------------*/
	public function form($instance) {
		global $wpdb;

		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$title = $adid = $before = $after = '';
		extract($instance);
		$title = esc_attr( $title );
		$adid = esc_attr( $adid );
		$before = esc_attr( $before );
		$after = esc_attr( $after );

		$adverts = $adverts_local = array();

		$adverts_local = $wpdb->get_results("SELECT `id`, `title` FROM `{$wpdb->prefix}adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days') ORDER BY `id` ASC;");
		foreach($adverts_local as $local) {
			$adverts[$local->id] = $local->title;
		}
		unset($adverts_local);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):', 'adrotate-pro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			<br />
			<small><?php _e('HTML will be stripped out.', 'adrotate-pro'); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('adid'); ?>"><?php _e('Advert:', 'adrotate-pro'); ?></label><br />
			<select id="<?php echo $this->get_field_id('adid'); ?>" name="<?php echo $this->get_field_name('adid'); ?>">
			<?php if($adverts) { ?>
				<option value="0"><?php _e('-- Choose an advert --', 'adrotate-pro'); ?></option>
				<?php foreach($adverts as $id => $title) { ?>
			        <option value="<?php echo $id;?>" <?php if($adid == $id) { echo 'selected'; } ?>><?php echo $id;?> - <?php echo $title;?></option>
	 			<?php } ?>
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

/*-------------------------------------------------------------
 Name:      ajdg_grpwidgets
 Since:		3.19
-------------------------------------------------------------*/
class adrotate_group_widget extends WP_Widget {

	/*-------------------------------------------------------------
	 Purpose:   Construct the widget
	-------------------------------------------------------------*/
	public function __construct() {

		$widget_string = get_option('adrotate_dynamic_widgets_group', 'temp_2');
		$widget_ops = array( 
			'classname' => $widget_string,
			'description' => 'Show a group of adverts in any widget area.',
		);
		parent::__construct($widget_string, 'AdRotate Group', $widget_ops);
	}

	/*-------------------------------------------------------------
	 Purpose:   Display the widget
	-------------------------------------------------------------*/
	public function widget($args, $instance) {
		global $adrotate_config, $post, $blog_id;

		extract($args);
		if(empty($instance['title'])) $instance['title'] = '';
		if(empty($instance['groupid'])) $instance['groupid'] = 0;
		if(empty($instance['categories'])) $instance['categories'] = '';
		if(empty($instance['pages'])) $instance['pages'] = '';
		$instance['before'] = (empty($instance['before'])) ? '' : stripslashes(htmlspecialchars_decode($instance['before'], ENT_QUOTES)).'.';
		$instance['after'] = (empty($instance['after'])) ? '' : '.'.stripslashes(htmlspecialchars_decode($instance['after'], ENT_QUOTES));

		// Determine post injection
		if($instance['categories'] != '' OR $instance['pages'] != '') {
			$show = false;
			
			$categories = explode(",", $instance['categories']);
			$pages = explode(",", $instance['pages']);

			if(is_page($pages) OR is_category($categories) OR in_category($categories)) {
				$show = true;
			}
		} else {
			$show = true;
		}
		
		if($show) {
			$title = apply_filters('widget_title', $instance['title']);

			echo $before_widget;
			if($title) {
				echo $before_title . $title . $after_title;
			}
			
			if($adrotate_config['widgetalign'] == 'Y') echo '<ul><li>';

			if($adrotate_config['w3caching'] == 'Y') {
				echo '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
				echo 'echo '.$instance['before'].'adrotate_group('.$instance['groupid'].')'.$instance['after'].';';
				echo '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

			} else if($adrotate_config['borlabscache'] == "Y") {
				if(function_exists('BorlabsCacheHelper') AND BorlabsCacheHelper()->willFragmentCachingPerform()) {
					$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();
			
					echo '<!--[borlabs cache start: '.$borlabsphrase.']-->';
					echo 'echo '.$instance['before'].'adrotate_group('.$instance['groupid'].')'.$instance['after'].';';
					echo ' <!--[borlabs cache end: '.$borlabsphrase.']-->';
		
					unset($borlabsphrase);
				}
			} else {
				echo $instance['before'].adrotate_group($instance['groupid']).$instance['after'];
			}
					
			if($adrotate_config['widgetalign'] == 'Y') echo '</li></ul>';
			
			echo $after_widget;
		}
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

		$group = $wpdb->get_row("SELECT `cat`, `cat_loc`, `page`, `page_loc` FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = {$new_instance['groupid']};");

		$new_instance['categories'] = ($group->cat_loc == 5) ? $group->cat : ''; // Post injection
		$new_instance['pages'] = ($group->page_loc == 5) ? $group->page : ''; // Page injection

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
		
		$title = $groupid = $categories = $pages = $before = $after = '';
		extract($instance);
		$title = esc_attr($title);
		$groupid = esc_attr($groupid);
		$categories = esc_attr($categories);
		$pages = esc_attr($pages);
		$before = esc_attr( $before );
		$after = esc_attr( $after );
		
		$groups = $groups_local = array();

		$groups_local = $wpdb->get_results("SELECT `id`, `name` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");
		foreach($groups_local as $local) {
			$groups[$local->id] = $local->name;
		}
		?>
		<?php if($categories != '' OR $pages != '') { ?>
		<p><?php _e('NOTE: This widget has Post Injection enabled!', 'adrotate-pro'); ?></p>
		<?php } ?>
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
				<option value="0"><?php _e('-- Choose a group --', 'adrotate-pro'); ?></option>
				<?php foreach($groups as $id => $title) { ?>
			        <option value="<?php echo $id;?>" <?php if($groupid == $id) { echo 'selected'; } ?>><?php echo $id;?> - <?php echo $title; ?></option>
	 			<?php } ?>
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