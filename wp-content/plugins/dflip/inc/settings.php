<?php

/**
 * Author : DeipGroup
 * Date: 8/11/2016
 * Time: 4:15 PM
 *
 * @package dflip
 *
 * @since   dflip 1.2
 */
class DFlip_Settings {
  
  /**
   * Holds the singleton class object.
   *
   * @since 1.2.0
   *
   * @var object
   */
  public static $instance;
  
  public $hook;
  
  /**
   * Holds the base DFlip class object.
   *
   * @since 1.2.0
   *
   * @var object
   */
  public $base;
  
  /**
   * Holds the base DFlip class fields.
   *
   * @since 1.2.0
   *
   * @var object
   */
  public $fields;
  
  /**
   * Primary class constructor.
   *
   * @since 1.2.0
   */
  public function __construct() {
    
    // Load the base class object.
    $this->base = DFlip::get_instance();
    
    add_action( 'admin_menu', array( $this, 'settings_menu' ) );
    
    $this->fields = array_merge( array(), $this->base->defaults );
    
    foreach ( $this->fields as $key => $value ) {
      
      if ( isset( $value['choices'] ) && is_array( $value['choices'] ) && isset( $value['choices']['global'] ) ) {
        unset( $this->fields[ $key ]['choices']['global'] );
      }
      
    }
    
    // Load the metabox hooks and filters.
    //		add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 100);
    
    // Add action to save metabox config options.
    //		add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
  }
  
  /**
   * Creates menu for the settings page
   *
   * @since 1.2
   */
  public function settings_menu() {
    
    $this->hook = add_submenu_page( 'edit.php?post_type=dflip', __( 'dFlip Global Settings', 'DFLIP' ), __( 'Global Settings', 'DFLIP' ), 'manage_options', $this->base->plugin_slug . '-settings',
        array( $this, 'settings_page' ) );
    
    //The resulting page's hook_suffix, or false if the user does not have the capability required.
    if ( $this->hook ) {
      add_action( 'load-' . $this->hook, array( $this, 'update_settings' ) );
      // Load metabox assets.
      add_action( 'load-' . $this->hook, array( $this, 'hook_page_assets' ) );
    }
  }
  
  /**
   * Callback to create the settings page
   *
   * @since 1.2
   */
  public function settings_page() {
    
    $tabs = array(
        'general'   => __( 'General', 'DFLIP' ),
        'translate' => __( 'Translate', 'DFLIP' ),
        //			'controls'  => __( 'Controls' , 'DFLIP' )
    );
    
    //create tabs and content
    ?>

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form id="dflip-settings" method="post" class="dflip-settings postbox">
      
      <?php
      wp_nonce_field( 'dflip_settings_nonce', 'dflip_settings_nonce' );
      submit_button( __( 'Update Settings', 'DFLIP' ), 'primary', 'dflip_settings_submit', false );
      ?>

      <div class="dflip-tabs">
        <ul class="dflip-tabs-list">
          <?php
          //create tabs
          $active_set = false;
          foreach ( (array) $tabs as $id => $title ) {
            ?>
            <li class="dflip-update-hash dflip-tab <?php echo( $active_set == false ? 'dflip-active' : '' ) ?>">
              <a href="#dflip-tab-content-<?php echo $id ?>"><?php echo $title ?></a></li>
            <?php $active_set = true;
          }
          ?>
        </ul>
        <?php
        
        $active_set = false;
        foreach ( (array) $tabs as $id => $title ) {
          ?>
          <div id="dflip-tab-content-<?php echo $id ?>"
                  class="dflip-tab-content <?php echo( $active_set == false ? "dflip-active" : "" ) ?>">
            
            <?php
            $active_set = true;
            
            //create content for tab
            $function = $id . "_tab";
            call_user_func( array( $this, $function ) );
            
            ?>
          </div>
        <?php } ?>
      </div>
    </form>
    <?php
    
  }
  
  public function hook_page_assets() {
    add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_styles_scripts' ) );
  }
  
  /**
   * Loads styles and scripts for our metaboxes.
   *
   * @return null Bail out if not on the proper screen.
   * @since 1.0.0
   *
   */
  public function meta_box_styles_scripts() {
    
    
    // Load necessary metabox styles.
    wp_register_style( $this->base->plugin_slug . '-setting-metabox-style', plugins_url( '../assets/css/metaboxes.css', __FILE__ ), array(), $this->base->version );
    wp_enqueue_style( $this->base->plugin_slug . '-setting-metabox-style' );
    
    // Load necessary metabox scripts.
    wp_register_script( $this->base->plugin_slug . '-setting-metabox-script', plugins_url( '../assets/js/metaboxes.js', __FILE__ ), array( 'jquery', 'jquery-ui-tabs' ), $this->base->version );
    wp_enqueue_script( $this->base->plugin_slug . '-setting-metabox-script' );
    
    wp_enqueue_media();
    
  }
  
  /**
   * Creates the UI for General tab
   *
   * @since 1.0.0
   *
   */
  public function general_tab() {
    
    $this->base->create_setting( 'webgl' );
    unset( $this->base->defaults['hard']['condition'] );
    $this->base->create_setting( 'hard' );
    $this->base->create_setting( 'thumb_tag_type' );
    $this->base->create_setting( 'texture_size' );
    $this->base->create_setting( 'auto_sound' );
    $this->base->create_setting( 'enable_download' );
    $this->base->create_setting( 'enable_analytics' );
    $this->base->create_setting( 'scroll_wheel' );
    
    $this->base->create_setting( 'bg_color' );
    $this->base->create_setting( 'bg_image' );
    $this->base->create_setting( 'height' );
    $this->base->create_setting( 'padding_left' );
    $this->base->create_setting( 'padding_right' );
    $this->base->create_setting( 'duration' );
    $this->base->create_setting( 'page_mode' );
    $this->base->create_setting( 'single_page_mode' );
    $this->base->create_setting( 'zoom_ratio' );
    $this->base->create_setting( 'stiffness' );
    $this->base->create_setting( 'controls_position' );
    $this->base->create_setting( 'direction' );
    $this->base->create_setting( 'more_controls' );
    $this->base->create_setting( 'hide_controls' );
    $this->base->create_setting( 'disable_range' );
    $this->base->create_setting( 'range_size' );
    $this->base->create_setting( 'autoplay' );
    $this->base->create_setting( 'autoplay_duration' );
    $this->base->create_setting( 'autoplay_start' );
    
    
    $this->base->create_setting( 'link_target' );
    $this->base->create_setting( 'share_prefix' );
    $this->base->create_setting( 'share_slug' );
    
    $this->base->create_setting( 'attachment_lightbox' );
    $this->base->create_setting( 'pdf_version' );
    
    ?>

    <!--Clear-fix-->
    <div class="dflip-box"></div>
    
    <?php
  }
  
  
  /**
   * Creates the UI for Translate tab
   *
   * @since 1.0.0
   *
   */
  public function translate_tab() {
    
    $this->base->create_setting( 'external_translate' );
    
    ?>

    <!--Loading-->
    <div id="dflip_text_open_book" class="dflip-box">

      <label for="dflip_text_open_book" class="dflip-label">
        <?php echo "Alternative text for Open Book"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Open Book</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_open_book' ); ?>"
                type="text" name="_dflip[text_open_book]" id="dflip_text_open_book" class=""/>
      </div>

    </div>
    <!--Loading-->
    <div id="dflip_text_loading" class="dflip-box">

      <label for="dflip_text_loading" class="dflip-label">
        <?php echo "Alternative text for Loading message"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Loading</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_loading' ); ?>"
                type="text" name="_dflip[text_loading]" id="dflip_text_loading" class=""/>
      </div>

    </div>

    <!--Turn on/off Sound-->
    <div id="dflip_text_toggle_sound_box" class="dflip-box">

      <label for="dflip_text_toggle_sound" class="dflip-label">
        <?php echo "Turn on/off Sound"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Turn on/off Sound</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_toggle_sound' ); ?>"
                type="text" name="_dflip[text_toggle_sound]" id="dflip_text_toggle_sound" class=""/>
      </div>

    </div>

    <!--Toggle Thumbnails-->
    <div id="dflip_text_toggle_thumbnails_box" class="dflip-box">

      <label for="dflip_text_toggle_thumbnails" class="dflip-label">
        <?php echo "Toggle Thumbnails"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Toggle Thumbnails</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_toggle_thumbnails' ); ?>"
                type="text" name="_dflip[text_toggle_thumbnails]" id="dflip_text_toggle_thumbnails" class=""/>
      </div>

    </div>

    <!--Toggle Outline/Bookmark-->
    <div id="dflip_text_toggle_outline_box" class="dflip-box">

      <label for="dflip_text_toggle_outline" class="dflip-label">
        <?php echo "Toggle Outline/Bookmark"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Toggle Outline/Bookmark</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_toggle_outline' ); ?>"
                type="text" name="_dflip[text_toggle_outline]" id="dflip_text_toggle_outline" class=""/>
      </div>

    </div>

    <!--Previous Page-->
    <div id="dflip_text_previous_page_box" class="dflip-box">

      <label for="dflip_text_previous_page" class="dflip-label">
        <?php echo "Previous Page"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Previous Page</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_previous_page' ); ?>"
                type="text" name="_dflip[text_previous_page]" id="dflip_text_previous_page" class=""/>
      </div>

    </div>

    <!--Next Page-->
    <div id="dflip_text_next_page_box" class="dflip-box">

      <label for="dflip_text_next_page" class="dflip-label">
        <?php echo "Next Page"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Next Page</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_next_page' ); ?>"
                type="text" name="_dflip[text_next_page]" id="dflip_text_next_page" class=""/>
      </div>

    </div>

    <!--Toggle Fullscreen-->
    <div id="dflip_text_toggle_fullscreen_box" class="dflip-box">

      <label for="dflip_text_toggle_fullscreen" class="dflip-label">
        <?php echo "Toggle Fullscreen"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Toggle Fullscreen</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_toggle_fullscreen' ); ?>"
                type="text" name="_dflip[text_toggle_fullscreen]" id="dflip_text_toggle_fullscreen" class=""/>
      </div>

    </div>

    <!--Zoom In-->
    <div id="dflip_text_zoom_in_box" class="dflip-box">

      <label for="dflip_text_zoom_in" class="dflip-label">
        <?php echo "Zoom In"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Zoom In</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_zoom_in' ); ?>"
                type="text" name="_dflip[text_zoom_in]" id="dflip_text_zoom_in" class=""/>
      </div>

    </div>

    <!--Zoom Out-->
    <div id="dflip_text_zoom_out_box" class="dflip-box">

      <label for="dflip_text_zoom_out" class="dflip-label">
        <?php echo "Zoom Out"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Zoom Out</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_zoom_out' ); ?>"
                type="text" name="_dflip[text_zoom_out]" id="dflip_text_zoom_out" class=""/>
      </div>

    </div>

    <!--Toggle Help-->
    <div id="dflip_text_toggle_help_box" class="dflip-box">

      <label for="dflip_text_toggle_help" class="dflip-label">
        <?php echo "Toggle Help"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Toggle Help</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_toggle_help' ); ?>"
                type="text" name="_dflip[text_toggle_help]" id="dflip_text_toggle_help" class=""/>
      </div>

    </div>

    <!--Single Page Mode-->
    <div id="dflip_text_single_page_mode_box" class="dflip-box">

      <label for="dflip_text_single_page_mode" class="dflip-label">
        <?php echo "Single Page Mode"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Single Page Mode</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_single_page_mode' ); ?>"
                type="text" name="_dflip[text_single_page_mode]" id="dflip_text_single_page_mode" class=""/>
      </div>

    </div>

    <!--Double Page Mode-->
    <div id="dflip_text_double_page_mode_box" class="dflip-box">

      <label for="dflip_text_double_page_mode" class="dflip-label">
        <?php echo "Double Page Mode"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Double Page Mode</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_double_page_mode' ); ?>"
                type="text" name="_dflip[text_double_page_mode]" id="dflip_text_double_page_mode" class=""/>
      </div>

    </div>

    <!--Download PDF File-->
    <div id="dflip_text_download_PDF_file_box" class="dflip-box">

      <label for="dflip_text_download_PDF_file" class="dflip-label">
        <?php echo "Download PDF File"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Download PDF File</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_download_PDF_file' ); ?>"
                type="text" name="_dflip[text_download_PDF_file]" id="dflip_text_download_PDF_file" class=""/>
      </div>

    </div>

    <!--Goto First Page-->
    <div id="dflip_text_goto_first_page_box" class="dflip-box">

      <label for="dflip_text_goto_first_page" class="dflip-label">
        <?php echo "Goto First Page"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Goto First Page</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_goto_first_page' ); ?>"
                type="text" name="_dflip[text_goto_first_page]" id="dflip_text_goto_first_page" class=""/>
      </div>

    </div>

    <!--Goto Last Page-->
    <div id="dflip_text_goto_last_page_box" class="dflip-box">

      <label for="dflip_text_goto_last_page" class="dflip-label">
        <?php echo "Goto Last Page"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Goto Last Page</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_goto_last_page' ); ?>"
                type="text" name="_dflip[text_goto_last_page]" id="dflip_text_goto_last_page" class=""/>
      </div>

    </div>

    <!--Share-->
    <div id="dflip_text_share_box" class="dflip-box">

      <label for="dflip_text_share" class="dflip-label">
        <?php echo "Share"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate text for <code>Share</code>" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_share' ); ?>"
                type="text" name="_dflip[text_share]" id="dflip_text_share" class=""/>
      </div>

    </div>

    <!--Share Mail Subject-->
    <div id="dflip_text_mail_subject_box" class="dflip-box">

      <label for="dflip_text_mail_subject" class="dflip-label">
        <?php echo "Share: Mail Subject"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate/Alternative text share mail subject" ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_mail_subject' ); ?>"
                type="text" name="_dflip[text_mail_subject]" id="dflip_text_mail_subject" class=""/>
      </div>

    </div>
    <!--Share Mail Message-->
    <div id="dflip_text_mail_body_box" class="dflip-box">

      <label for="dflip_text_mail_body" class="dflip-label">
        <?php echo "Share: Mail Message"; ?>
      </label>

      <div class="dflip-desc">
        <?php echo "Translate/Alternative text for share mail Message. <code>{{url}}</code>will be replaced with the shareURL." ?>
      </div>

      <div class="dflip-option">
        <input value="<?php echo $this->base->get_config( 'text_mail_body' ); ?>"
                type="text" name="_dflip[text_mail_body]" id="dflip_text_mail_body" class=""/>
      </div>

    </div>

    <!--Clear-fix-->
    <div class="dflip-box"></div>
    <?php
    
  }
  
  /**
   * Update settings
   *
   * @return null Invalid nonce / no need to save
   * @since 1.2.0.1
   *
   */
  public function update_settings() {
    
    // Check form was submitted
    if ( !isset( $_POST['dflip_settings_submit'] ) ) {
      return;
    }
    
    // Check nonce is valid
    if ( !wp_verify_nonce( $_POST['dflip_settings_nonce'], 'dflip_settings_nonce' ) ) {
      return;
    }
    
    $data = $_POST['_dflip'];
    
    if ( is_multisite() ) {
      // Update options
      update_blog_option( null, '_dflip_settings', $data );
    } else {
      // Update options
      update_option( '_dflip_settings', $data );
    }
    // Show confirmation
    add_action( 'admin_notices', array( $this, 'updated_settings' ) );
    
  }
  
  /**
   * display a saved notice
   *
   * @since 1.2.0.1
   */
  public function updated_settings() {
    ?>
    <div class="updated">
      <p><?php _e( 'Settings updated.', 'DFLIP' ); ?></p>
    </div>
    <?php
    
  }
  
  /**
   * Returns the singleton instance of the class.
   *
   * @return object DFlip_Settings object.
   * @since 1.2.0
   *
   */
  public static function get_instance() {
    
    if ( !isset( self::$instance )
         && !( self::$instance instanceof DFlip_Settings ) ) {
      self::$instance = new DFlip_Settings();
    }
    
    return self::$instance;
    
  }
}

// Load the DFlip_Settings class.
$dflip_settings = DFlip_Settings::get_instance();
