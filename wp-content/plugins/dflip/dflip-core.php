<?php
/**
 * Common core of DearFlip
 *
 * @since           1.7.6.1
 *
 * @package         dflip
 * @author          DearHive
 * @copyright   (c) Copyright by DearHive
 * @link            https://dearhive.com
 */

// Do not allow direct file access
if ( !defined( 'ABSPATH' ) ) {
  exit( 'Direct script access denied.' );
}

if ( !class_exists( 'DFlip' ) ) {
  /**
   * Main dFlip plugin class.
   *
   * @since   1.0.0
   *
   * @package DFlip
   * @author  Deepak Ghimire
   */
  class DFlip {
    
    /**
     * Holds the singleton class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;
    
    /**
     * Plugin version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.7.35';
    
    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_name = 'dFlip';
    
    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'dflip';
    
    public $plugin_url = "https://wordpress.org/plugins/3d-flipbook-dflip-lite/";
    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;
    
    /**
     * Default values.
     *
     * @since 1.2.6
     *
     * @var string
     */
    public $defaults;
    
    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
      
      $this->settings_text = array();
      $this->external_translate = false;
      // Load the plugin.
      add_action( 'init', array( $this, 'init' ), 0 );
      
    }
    
    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {
      
      $this->defaults = array(
          
          'text_toggle_sound'      => __( "Turn on/off Sound", 'DFLIP' ),
          'text_toggle_thumbnails' => __( "Toggle Thumbnails", 'DFLIP' ),
          'text_toggle_outline'    => __( "Toggle Outline/Bookmark", 'DFLIP' ),
          'text_previous_page'     => __( "Previous Page", 'DFLIP' ),
          'text_next_page'         => __( "Next Page", 'DFLIP' ),
          'text_toggle_fullscreen' => __( "Toggle Fullscreen", 'DFLIP' ),
          'text_zoom_in'           => __( "Zoom In", 'DFLIP' ),
          'text_zoom_out'          => __( "Zoom Out", 'DFLIP' ),
          'text_toggle_help'       => __( "Toggle Help", 'DFLIP' ),
          'text_single_page_mode'  => __( "Single Page Mode", 'DFLIP' ),
          'text_double_page_mode'  => __( "Double Page Mode", 'DFLIP' ),
          'text_download_PDF_file' => __( "Download PDF File", 'DFLIP' ),
          'text_goto_first_page'   => __( "Goto First Page", 'DFLIP' ),
          'text_goto_last_page'    => __( "Goto Last Page", 'DFLIP' ),
          'text_share'             => __( "Share", 'DFLIP' ),
          'text_mail_subject'      => __( "I wanted you to see this FlipBook", 'DFLIP' ),
          'text_mail_body'         => __( "Check out this site {{url}}", 'DFLIP' ),
          'text_loading'           => __( "Loading", 'DFLIP' ),
          'text_open_book'         => __( "Open Book", 'DFLIP' ),
          
          'external_translate' => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Use External Translate',
              'desc'    => 'Use translations from other plugins and skip the translate from dFlip settings.'
          ),
          'more_controls'      => array(
              'std'         => "download,pageMode,startPage,endPage,sound",
              'title'       => 'More Controls - CASE SENSITIVE',
              'desc'        => 'Names of Controls in more Control Bar<br><code>altPrev, pageNumber, altNext, outline, thumbnail, zoomIn, zoomOut, fullScreen,share, more, download,pageMode,startPage,endPage,sound</code>',
              'placeholder' => '',
              'type'        => 'textarea'
          ),
          'hide_controls'      => array(
              'std'         => "",
              'title'       => 'Hide Controls - CASE SENSITIVE',
              'desc'        => 'Names of Controls to be hidden.. ',
              'placeholder' => '',
              'type'        => 'textarea'
          ),
          'scroll_wheel'       => array(
              'std'     => 'true',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'title'   => 'Enable Zoom on Scroll',
              'desc'    => 'Select if zoom on mouse scroll should be active.'
          ),
          'bg_color'           => array(
              'std'         => "#777",
              'title'       => 'Background Color',
              'desc'        => 'Background color in hexadecimal format eg:<code>#FFF</code> or <code>#666666</code>',
              'placeholder' => 'Example: #ffffff',
              'type'        => 'text'
          ),
          'bg_image'           => array(
              'std'            => "",
              'class'          => '',
              'title'          => 'Background Image',
              'desc'           => 'Background image JPEG or PNG format:',
              'placeholder'    => 'Select an image',
              'type'           => 'upload',
              'button-tooltip' => 'Select Background Image',
              'button-text'    => 'Select Image'
          ),
          'height'             => array(
              'std'         => "auto",
              'title'       => 'Container Height',
              'desc'        => 'Height of the flipbook container when in normal mode.<br> <code>500</code>for 500px <br> <code>auto</code>for autofit height <br> <code>100%</code>for 100% height (of parent element, else it will be 320px).',
              'placeholder' => 'Example: 500',
              'type'        => 'text'
          ),
          'padding_left'       => array(
              'std'         => "20",
              'title'       => 'Padding Left',
              'desc'        => 'Gap between book and left-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'padding_right'      => array(
              'std'         => "20",
              'title'       => 'Padding Right',
              'desc'        => 'Gap between book and right-side of container.',
              'placeholder' => 'Example: 50',
              'type'        => 'number'
          ),
          'duration'           => array(
              'std'         => 800,
              'class'       => '',
              'title'       => 'Flip Duration',
              'desc'        => 'Time in milliseconds eg:<code>1000</code>for 1second',
              'placeholder' => 'Example: 1000',
              'type'        => 'number'
          ),
          'zoom_ratio'         => array(
              'std'         => 1.5,
              'title'       => 'Zoom Ratio',
              'desc'        => 'Multiplier for zoom recommended (1.1 - 2)',
              'placeholder' => 'Example: 1.5',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 0.1,
                  'min'  => 1,
                  'max'  => 20
              )
          ),
          'stiffness'          => array(
              'std'         => 3,
              'title'       => 'Paper Stiffness (3D only)',
              'desc'        => 'More value leads to much flat(stiff) paper at rest.. eg: 1 for max curve, 1000 for full flat',
              'placeholder' => 'Example: 3',
              'type'        => 'number',
              'attr'        => array(
                  'step' => 0.1,
                  'min'  => 1,
                  'max'  => 1000
              )
          ),
          'auto_sound'         => array(
              'std'     => 'true',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Sound',
              'desc'    => 'Sound will play from the start.'
          ),
          'enable_download'    => array(
              'std'     => 'true',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable Download',
              'desc'    => 'Enable PDF download'
          ),
          'enable_analytics'   => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable Analytics',
              'desc'    => 'Enable Google Analytics. Analytics code should be added to site before ths can be used.'
          ),
          'webgl'              => array(
              'std'     => 'true',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'WebGL 3D', 'DFLIP' ),
                  'false'  => __( 'CSS 3D/2D', 'DFLIP' )
              ),
              'title'   => '3D or 2D',
              'desc'    => 'Choose the mode of display. WebGL for realistic 3d'
          ),
          'hard'               => array(
              'std'     => 'none',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'cover'  => __( 'Cover Pages', 'DFLIP' ),
                  'all'    => __( 'All Pages', 'DFLIP' ),
                  'none'   => __( 'None', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Hard Pages',
              'desc'    => 'Choose which pages to act as hard.(Only in CSS mode)'
          ),
          'direction'          => array(
              'std'     => 1,
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  1        => __( 'Left to Right', 'DFLIP' ),
                  2        => __( 'Right to Left', 'DFLIP' )
              ),
              'title'   => 'Direction',
              'desc'    => 'Left to Right or Right to Left.'
          ),
          'force_fit'          => array(
              'std'       => 'true',
              'choices'   => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Force Page Fit',
              'desc'      => 'Choose if you want to force the pages to stretch and fit the page size.)',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'source_type'        => array(
              'std'     => 'pdf',
              'choices' => array(
                  'pdf'   => __( 'PDF File', 'DFLIP' ),
                  'image' => __( 'Images', 'DFLIP' )
              ),
              'title'   => 'Book Source Type',
              'desc'    => 'Choose the source of this book. "PDF" for pdf files. "Images" for image files.'
          ),
          'pdf_source'         => array(
              'std'            => "",
              'title'          => 'PDF File',
              'desc'           => 'Choose a PDF File to use as source for the book.',
              'placeholder'    => 'Select a PDF File',
              'type'           => 'upload',
              'button-tooltip' => 'Select a PDF File',
              'button-text'    => 'Select PDF',
              'condition'      => 'dflip_source_type:is(pdf)'
          ),
          'pdf_thumb'          => array(
              'std'            => "",
              'title'          => 'PDF Thumbnail Image',
              'desc'           => 'Choose an image file for PDF thumb.',
              'placeholder'    => 'Select an image',
              'type'           => 'upload',
              'button-tooltip' => 'Select PDF Thumb Image',
              'button-text'    => 'Select Thumb',
              'condition'      => 'dflip_source_type:is(pdf)'
          ),
          'overwrite_outline'  => array(
              'std'       => 'false', //isset mis-interprets 0 and false differently than expected
              'choices'   => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'     => '',
              'title'     => 'Overwrite PDF Outline',
              'desc'      => 'Choose if PDF Outline will overwritten.',
              'condition' => 'dflip_source_type:is(pdf)'
          ),
          'auto_outline'       => array(
              'std'     => 'false', //isset mis-interprets 0 and false differently than expected
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Outline',
              'desc'    => 'Choose if outline will be auto enabled on start.'
          ),
          'auto_thumbnail'     => array(
              'std'     => 'false', //isset mis-interprets 0 and false differently than expected
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Auto Enable Thumbnail',
              'desc'    => 'Choose if thumbnail will be auto enabled on start.Note : Either thumbnail or outline will be active at a time.)'
          ),
          'page_mode'          => array(
              'std'     => '0',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '0'      => __( 'Auto', 'DFLIP' ),
                  '1'      => __( 'Single Page', 'DFLIP' ),
                  '2'      => __( 'Double Page', 'DFLIP' ),
                  //					'3' =>  __( 'Single Page : Booklet' , 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Page Mode',
              'desc'    => 'Choose whether you want single mode or double page mode. Recommended Auto'
          ),
          
          'page_size'         => array(
              'std'     => '0',
              'choices' => array(
                  '0' => __( 'Auto', 'DFLIP' ),
                  '1' => __( 'Single Page', 'DFLIP' ),
                  '2' => __( 'Double Internal Page', 'DFLIP' ),
                  //					'3' =>  __( 'Single Page : Booklet' , 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Page Size',
              'desc'    => 'Choose whether Layout is single page mode or double internal. Recommended Auto if PDF file'
          ),
          'single_page_mode'  => array(
              'std'     => '0',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '0'      => __( 'Auto', 'DFLIP' ),
                  '1'      => __( 'Normal Zoom', 'DFLIP' ),
                  '2'      => __( 'Booklet Mode', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Single Page Mode',
              'desc'    => 'Choose how the single page will behave. If set to Auto, then in mobiles single page mode will be in Booklet mode.'
          ),
          'controls_position' => array(
              'std'     => 'bottom',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'bottom' => __( 'Bottom', 'DFLIP' ),
                  'top'    => __( 'Top', 'DFLIP' ),
                  'hide'   => __( 'Hidden', 'DFLIP' ),
              ),
              'class'   => '',
              'title'   => 'Controls Position',
              'desc'    => 'Choose where you want to display the controls bar or not display at all.'
          ),
          'texture_size'      => array(
              'std'     => '1600',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '1024'   => 1024,
                  '1400'   => 1400,
                  '1600'   => 1600,
                  '1800'   => 1800,
                  '2048'   => 2048
              ),
              'class'   => '',
              'title'   => 'PDF Page Render Size',
              'desc'    => 'Choose the size of image to be generated.',
          ),
          'link_target'       => array(
              'std'     => '2',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  '1'      => "Same Tab",
                  '2'      => "New Tab"
              ),
              'class'   => '',
              'title'   => 'PDF Links Open Target',
              'desc'    => 'Open PDF links in same tab/window or new tab.',
          ),
          'share_prefix'      => array(
              'std'         => "flipbook-",
              'title'       => 'Share Prefix',
              'desc'        => 'List of share prefix to support, separated by comma. First prefix is actively used to share, older are used for backward compatibility with older prefix, dflip- is used if not set',
              'placeholder' => 'Example: book-,flipbook-',
              'type'        => 'text'
          ),
          
          'share_slug' => array(
              'std'     => 'false',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Use flipbook slugs',
              'desc'    => 'While sharing the flipbook, Post slug will be used instead of Post id. <strong>Flipbook share links won\'t work if the slug is changed in future</strong>'
          ),
          
          'attachment_lightbox' => array(
              'std'     => 'true',
              'choices' => array(
                  'true'  => __( 'True', 'DFLIP' ),
                  'false' => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Attachment PDF page auto Lightbox',
              'desc'    => 'When opening attachment page for PDF, display lightbox instead of embedded flipbook.</strong>'
          ),
          
          'range_size' => array(
              'std'     => '524288',
              'choices' => array(
                  'global'  => __( 'Global Setting', 'DFLIP' ),
                  '65536'   => '64KB',
                  '131072'  => '128KB',
                  '262144'  => '256KB',
                  '524288'  => '512KB',
                  '1048576' => '1024KB'
              ),
              'title'   => 'PDF Partial Loading Chunk Size',
              'desc'    => 'Choose the size chunk size to be loaded on demand'
          ),
          'autoplay'   => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable AutoPlay',
              'desc'    => 'Enable AutoPlay in Flipbook'
          ),
          
          'disable_range' => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Disable Partial Loading',
              'desc'    => 'Partial loading, Chunk issues are discovered in some servers and browsers. Disabling range can fix that issue.'
          ),
          
          'autoplay_start'    => array(
              'std'     => 'false',
              'choices' => array(
                  'global' => __( 'Global Setting', 'DFLIP' ),
                  'true'   => __( 'True', 'DFLIP' ),
                  'false'  => __( 'False', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Enable AutoPlay Automatically',
              'desc'    => 'Enable AutoPlay automatically when flipbook loads'
          ),
          'autoplay_duration' => array(
              'std'         => 5000,
              'class'       => '',
              'title'       => 'Autoplay Duration',
              'desc'        => 'Time in milliseconds eg:<code>1000</code>for 1second',
              'placeholder' => 'Example: 5000',
              'type'        => 'number'
          ),
          'thumb_tag_type'    => array(
              'std'     => 'bg',
              'choices' => array(
                  'bg'  => __( 'Div Background', 'DFLIP' ),
                  'img' => __( 'Image', 'DFLIP' )
              ),
              'class'   => '',
              'title'   => 'Book Thumb Type',
              'desc'    => 'Choose Div Background for uniform thumb size, Image for adaptive size.'
          ),
          'pdf_version'       => array(
              'std'     => 'default',
              'choices' => array(
                  'default'              => 'Default Version(2.5.207)',
                  '2.10.377-legacy-dist' => '2.10.377',
                  'beta'                 => 'Upcoming (2.12.313)'
              ),
              'title'   => 'PDF.js Version',
              'desc'    => 'Choose which version of PDF.js rendering Engine to use.'
          ),
          'pages'             => array()
      );
      
      $external_translate = $this->get_config( 'external_translate' );
      $this->external_translate = $external_translate == "true";
      
      // Load admin only components.
      if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        $this->init_admin();
      } else { // Load frontend only components.
        $this->init_front();
      }
      
      // Load global components.
      $this->init_global();
      
    }
    
    /**
     * Loads all admin related files into scope.
     *
     * @since 1.0.0
     */
    public function init_admin() {
      
      include_once( dirname( __FILE__ ) . '/inc/settings.php' );
      
      //include the metaboxes file
      include_once dirname( __FILE__ ) . "/inc/metaboxes.php";
      
      /*
      //register admin style
      wp_register_style(
        $this->plugin_slug . 'admin-style', plugins_url() . '/dflip/assets/css/df-admin.css', array(),
        $this->version
      );
  
      //enqueue the register style
      wp_enqueue_style($this->plugin_slug . 'admin-style');
      */
      
      
    }
    
    /**
     * Loads all frontend user related files
     *
     * @since 1.0.0
     */
    public function init_front() {
      
      //include the shortcode parser
      include_once dirname( __FILE__ ) . "/inc/shortcode.php";
      
      //include the scripts and styles for front end
      add_action( 'wp_enqueue_scripts', array( $this, 'init_front_scripts' ) );
      
      //some custom js that need to be passed
      add_action( 'wp_head', array( $this, 'hook_script' ) );
      
    }
    
    /**
     * Loads all global files into scope.
     *
     * @since 1.0.0
     */
    public function init_global() {
      
      //include the post-type that manages the custom post
      include_once dirname( __FILE__ ) . '/inc/post-type.php';
      
    }
    
    /**
     * Loads all script and style sheets for frontend into scope.
     *
     * @since 1.0.0
     */
    public function init_front_scripts() {
      
      //      add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2 );
      
      //cache for plugin_slug
      $_slug = $this->plugin_slug;
      
      //required for cache busting
      $_version = $this->version;
      
      //register scripts
      wp_register_script( $_slug . '-script', plugins_url( 'assets/js/dflip.min.js', __FILE__ ), array( "jquery" ), $_version, true );
      
      //register scripts
      wp_register_style( $_slug . '-icons-style', plugins_url( 'assets/css/themify-icons.min.css', __FILE__ ), array(), $_version );
      wp_register_style( $_slug . '-style', plugins_url( 'assets/css/dflip.min.css', __FILE__ ), array(), $_version );
      
      
      //enqueue scripts
      wp_enqueue_script( $_slug . '-script' );
      
      //enqueue styles
      wp_enqueue_style( $_slug . '-icons-style' );
      wp_enqueue_style( $_slug . '-style' );
      //		wp_enqueue_style($_slug . '-book-style');
      
      
    }
    
    public function add_defer_attribute( $tag, $handle ) {
      // add script handles to the array below
      //cache for plugin_slug
      $_slug = $this->plugin_slug;
      $scripts_to_defer = array( 'jquery-core', $_slug . '-script', $_slug . '-parse-script' );
      
      foreach ( $scripts_to_defer as $defer_script ) {
        if ( $defer_script === $handle ) {
          return str_replace( ' src', ' data-cfasync="false" src', $tag );
        }
      }
      
      return $tag;
    }
    
    /**
     * Registers a javascript variable into HTML DOM for url access
     *
     * @since 1.0.0
     */
    public function hook_script() {
      
      $data = array(
          'text'             => array(
              'toggleSound'      => $this->get_translate( 'text_toggle_sound' ),
              'toggleThumbnails' => $this->get_translate( 'text_toggle_thumbnails' ),
              'toggleOutline'    => $this->get_translate( 'text_toggle_outline' ),
              'previousPage'     => $this->get_translate( 'text_previous_page' ),
              'nextPage'         => $this->get_translate( 'text_next_page' ),
              'toggleFullscreen' => $this->get_translate( 'text_toggle_fullscreen' ),
              'zoomIn'           => $this->get_translate( 'text_zoom_in' ),
              'zoomOut'          => $this->get_translate( 'text_zoom_out' ),
              'toggleHelp'       => $this->get_translate( 'text_toggle_help' ),
              'singlePageMode'   => $this->get_translate( 'text_single_page_mode' ),
              'doublePageMode'   => $this->get_translate( 'text_double_page_mode' ),
              'downloadPDFFile'  => $this->get_translate( 'text_download_PDF_file' ),
              'gotoFirstPage'    => $this->get_translate( 'text_goto_first_page' ),
              'gotoLastPage'     => $this->get_translate( 'text_goto_last_page' ),
              'share'            => $this->get_translate( 'text_share' ),
              'mailSubject'      => $this->get_translate( 'text_mail_subject' ),
              'mailBody'         => $this->get_translate( 'text_mail_body' ),
              'loading'          => $this->get_translate( 'text_loading' )
          ),
          'moreControls'     => $this->get_config( 'more_controls' ),
          'hideControls'     => $this->get_config( 'hide_controls' ),
          'scrollWheel'      => $this->get_config( 'scroll_wheel' ),
          'backgroundColor'  => $this->get_config( 'bg_color' ),
          'backgroundImage'  => $this->get_config( 'bg_image' ),
          'height'           => $this->get_config( 'height' ),
          'paddingLeft'      => $this->get_config( 'padding_left' ),
          'paddingRight'     => $this->get_config( 'padding_right' ),
          'controlsPosition' => $this->get_config( 'controls_position' ),
          'direction'        => $this->get_config( 'direction' ),
          'duration'         => $this->get_config( 'duration' ),
          'soundEnable'      => $this->get_config( 'auto_sound' ),
          'enableDownload'   => $this->get_config( 'enable_download' ),
          'enableAnalytics'  => $this->get_config( 'enable_analytics' ),
          'webgl'            => $this->get_config( 'webgl' ),
          'hard'             => $this->get_config( 'hard' ),
          'maxTextureSize'   => $this->get_config( 'texture_size' ),
          'rangeChunkSize'   => $this->get_config( 'range_size' ),
          'disableRange'     => $this->get_config( 'disable_range' ) == "true",
          'zoomRatio'        => $this->get_config( 'zoom_ratio' ),
          'stiffness'        => $this->get_config( 'stiffness' ),
          'pageMode'         => $this->get_config( 'page_mode' ),
          'singlePageMode'   => $this->get_config( 'single_page_mode' ),
          'pageSize'         => $this->get_config( 'page_size' ),
          'autoPlay'         => $this->get_config( 'autoplay' ),
          'autoPlayDuration' => $this->get_config( 'autoplay_duration' ),
          'autoPlayStart'    => $this->get_config( 'autoplay_start' ),
          'linkTarget'       => $this->get_config( 'link_target' ),
          'sharePrefix'      => $this->get_config( 'share_prefix' ),
          'pdfVersion'       => $this->get_config( 'pdf_version' )
      );
      
      //registers a variable that stores the location of plugin
      $output = '<script data-cfasync="false"> var dFlipLocation = "' . plugins_url( 'assets/', __FILE__ ) . '"; var dFlipWPGlobal = ' . json_encode( $data ) . ';</script>';
      echo $output;
      
    }
    
    /**
     * Helper method for retrieving config values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.2.6
     *
     */
    public function get_config( $key ) {
      
      $values = is_multisite() ? get_blog_option( null, '_dflip_settings', true ) : get_option( '_dflip_settings', true );
      $value = isset( $values[ $key ] ) ? $values[ $key ] : '';
      
      $default = $this->get_default( $key );
      
      /* set standard value */
      if ( $default !== null ) {
        $value = $this->filter_std_value( $value, $default );
      }
      
      return $value;
      
    }
    
    public function get_translate( $key ) {
      if ( $this->external_translate == true ) {
        return $this->get_default( $key );
      } else {
        return $this->get_config( $key );
      }
    }
    
    /**
     * Helper method for retrieving default values.
     *
     * @param string $key The config key to retrieve.
     *
     * @return string Key value on success, empty string on failure.
     * @since 1.0.0
     *
     */
    public function get_default( $key ) {
      
      $default = isset( $this->defaults[ $key ] ) ? is_array( $this->defaults[ $key ] ) ? isset( $this->defaults[ $key ]['std'] ) ? $this->defaults[ $key ]['std'] : '' : $this->defaults[ $key ] : '';
      
      return $default;
      
    }
    
    /**
     * Helper function to filter standard option values.
     *
     * @param mixed $value Saved string or array value
     * @param mixed $std   Standard string or array value
     *
     * @return    mixed     String or array
     *
     * @access    public
     * @since     1.0.0
     */
    public function filter_std_value( $value = '', $std = '' ) {
      
      $std = maybe_unserialize( $std );
      
      if ( is_array( $value ) && is_array( $std ) ) {
        
        foreach ( $value as $k => $v ) {
          
          if ( '' === $value[ $k ] && isset( $std[ $k ] ) ) {
            
            $value[ $k ] = $std[ $k ];
            
          }
          
        }
        
      } else {
        if ( '' === $value && $std !== null ) {
          
          $value = $std;
          
        }
      }
      
      return $value;
      
    }
    
    /**
     * Helper function to create settings boxes
     *
     * @access    public
     *
     * @param        $key
     * @param null   $setting
     * @param null   $value
     * @param null   $global_key
     * @param string $global_value
     *
     * @since     1.2.6
     *
     */
    public function create_setting( $key, $setting = null, $value = null, $global_key = null, $global_value = '' ) {
      
      $setting = is_null( $setting ) ? $this->defaults[ $key ] : $setting;
      $value = is_null( $value ) ? $this->get_config( $key ) : $value;
      $condition = isset( $setting['condition'] ) ? 'data-condition="' . $setting['condition'] . '"' : '';
      $class = isset( $setting['class'] ) ? $setting['class'] : '';
      $placeholder = isset( $setting['placeholder'] ) ? 'placeholder="' . $setting['placeholder'] . '"' : '';
      
      $global_attr = !is_null( $global_key ) ? 'data-global="' . $global_key . '"' : "";
      
      echo '<div id="dflip_' . $key . '_box" class="dflip-box ' . $class . '" ' . $condition . '>
      <label for="dflip_' . $key . '" class="dflip-label">
				' . $setting['title'] . '
			</label>
			<div class="dflip-desc">
				' . $setting['desc'] . '
			</div>';
      
      if ( isset( $setting['choices'] ) && is_array( $setting['choices'] ) ) {
        
        echo '<div class="dflip-option dflip-select">
				<select name="_dflip[' . $key . ']" id="dflip_' . $key . '" class="" ' . $global_attr . '>';
        
        foreach ( (array) $setting['choices'] as $val => $label ) {
          
          if ( is_null( $global_key ) && $val === "global" ) {
            continue;
          }
          
          echo '<option value="' . $val . '" ' . selected( $value, $val, false ) . '>' . $label . '</option>';
          
          //				}
        }
        echo '</select>';
        
      } else if ( $setting['type'] == 'upload' ) {
        $tooltip = isset( $setting['button-tooltip'] ) ? 'title="' . $setting['button-tooltip'] . '"' : '';
        $button_text = isset( $setting['button-text'] ) ? $setting['button-text'] : 'Select';
        echo '<div class="dflip-option dflip-upload">
				<input ' . $placeholder . ' type="text" name="_dflip[' . $key . ']" id="dflip_' . $key . '"
				       value="' . $value . '"
				       class="widefat dflip-upload-input " ' . $global_attr . '/>
				<a href="javascript:void(0);" id="dflip_upload_' . $key . '"
				   class="dflip_upload_media dflip-button button button-primary light"
				   ' . $tooltip . '>
					' . $button_text . '
				</a>';
      
      } else if ( $setting['type'] == 'textarea' ) {
        echo '<div class="dflip-option">
				<textarea rows="3" cols="40" name="_dflip[' . $key . ']" id="dflip_' . $key . '"
				          class="" ' . $global_attr . '>' . $value . '</textarea>';
      } else {
        $type = isset( $setting['type'] ) ? 'type="' . $setting['type'] . '"' : '';
        $attrHTML = ' ';
        
        if ( isset( $setting['attr'] ) ) {
          foreach ( $setting['attr'] as $attr_key => $attr_value ) {
            $attrHTML .= $attr_key . "=" . $attr_value . " ";
          }
        }
        
        echo '<div class="dflip-option">
				<input  ' . $placeholder . ' value="' . $value . '" ' . $type . $attrHTML . ' name="_dflip[' . $key . ']" id="dflip_' . $key . '" class="" ' . $global_attr . '/>';
      }
      
      if ( !is_null( $global_key ) ) {
        echo '<div class="dflip-global-value"><i>Global:</i>
					<code>' . $global_value . '</code></div>';
      }
      echo '</div>
		</div>';
    
    }
    
    /**
     * Returns the singleton instance of the class.
     *
     * @return object DFlip object.
     * @since 1.0.0
     *
     */
    public static function get_instance() {
      
      if ( !isset( self::$instance ) && !( self::$instance instanceof DFlip ) ) {
        self::$instance = new DFlip();
      }
      
      return self::$instance;
      
    }
    
  }
  
  //Load the dFlip Plugin Class
  $dflip = DFlip::get_instance();
}




/*Avoid PHP closing tag to prevent "Headers already sent"*/
