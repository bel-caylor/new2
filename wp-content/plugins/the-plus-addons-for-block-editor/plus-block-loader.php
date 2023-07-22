<?php
/**
 * The Plus Gutenberg Loader.
 * @since 1.0.0
 * @package TP_Gutenberg_Loader
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( !class_exists( 'TP_Gutenberg_Loader' ) ) {
    
    /**
     * Class TP_Gutenberg_Loader.
     */
    final class TP_Gutenberg_Loader {
        
        /**
         * Member Variable
         *
         * @var instance
         */
        private static $instance;
        
        public $post_assets_objects = array();

        /**
         *  Initiator
         */
        public static function get_instance() {
            if ( !isset( self::$instance ) ) {
                self::$instance = new self;
            } 
            return self::$instance;
        }
        
        /**
         * Constructor
         */
        public function __construct() {
            
            //check Gutenberg plugin required
            if ( !function_exists( 'register_block_type' ) ) {
                add_action( 'admin_notices', array( $this, 'tpgb_check_gutenberg_req' ) );
                return;
            } 
            
            $this->loader_helper();
            
            add_action( 'plugins_loaded', array( $this, 'tp_plugin_loaded' ) );

            if ( is_admin() ) {
                add_filter( 'plugin_action_links_' . TPGB_BASENAME, array( $this, 'tpgb_settings_pro_link' ) );
                add_filter( 'plugin_row_meta', array( $this, 'tpbg_extra_links_plugin_row_meta' ), 10, 2 );
            }

            // Activation hook For Redirect.
            add_action( 'activated_plugin', array( $this,'tpgb_activate_redirect') );
        }
        
        /**
         * Loads Helper/Other files.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function loader_helper() {
           
			if ( ! class_exists( 'CMB2' ) ){
				require_once TPGB_PATH . 'includes/metabox/init.php';
            }
			
			$option_name='default_tpgb_load_opt';
			$value='1';
			if ( is_admin() && get_option( $option_name ) !== false ) {
			} else if( is_admin() ){
				$default_load=get_option( 'tpgb_normal_blocks_opts' );
				if ( $default_load !== false && $default_load!='') {
					$deprecated = null;
					$autoload = 'no';
					add_option( $option_name,$value, $deprecated, $autoload );
				}else{
					$tpgb_normal_blocks_opts=get_option( 'tpgb_normal_blocks_opts' );
                    if($tpgb_normal_blocks_opts === false){
                        $tpgb_normal_blocks_opts = [];
                    }
					$tpgb_normal_blocks_opts['enable_normal_blocks']= array("tp-accordion","tp-breadcrumbs","tp-blockquote","tp-button","tp-countdown","tp-container","tp-creative-image","tp-data-table","tp-draw-svg","tp-empty-space","tp-flipbox","tp-google-map","tp-heading-title","tp-hovercard","tp-infobox","tp-messagebox","tp-number-counter","tp-pricing-list","tp-pricing-table","tp-pro-paragraph","tp-progress-bar","tp-row","tp-stylist-list","tp-social-icons","tp-tabs-tours","tp-testimonials","tp-video","tp-login-register");
					
					$deprecated = null;
					$autoload = 'no';
					add_option( 'tpgb_normal_blocks_opts',$tpgb_normal_blocks_opts, $deprecated, $autoload );
					add_option( $option_name,$value, $deprecated, $autoload );
                    $action_delay = 'tpgb_delay_css_js';
                    if ( false === get_option($action_delay) ){
                        add_option( $action_delay, 'true' );
                    }
                    $action_defer = 'tpgb_defer_css_js';
                    if ( false === get_option($action_defer) ){
                        add_option( $action_defer, 'true' );
                    }
				}
			}
			
			//Load Conditions Rules
			require_once TPGB_PATH . 'classes/extras/tpgb-conditions-rules.php';
			require TPGB_PATH . 'includes/rollback.php';
            require TPGB_PATH . 'includes/plus-settings-options.php';
            
            // Reusable Short code
            require_once TPGB_PATH . 'classes/extras/tpag-reusable-shortcode.php';

            require_once TPGB_PATH . 'classes/tp-block-helper.php';
        }
        
        /*
         * Files load plugin loaded.
         *
         * @since 1.1.3
         *
         * @return void
         */
        public function tp_plugin_loaded() {
            $this->load_textdomain();
            require_once TPGB_PATH . 'classes/tp-generate-block-css.php';

            require_once TPGB_PATH . 'classes/tp-get-blocks.php';
            require_once TPGB_PATH . 'classes/tp-core-init-blocks.php';
        }
        
        /*
         * Check Gutenberg Plugin Install / Activate Notice
         *
         * @since 1.0.0
         *
         */
        public function tpgb_check_gutenberg_req() {
            
            $notice_class = 'notice notice-error';
            
            $plugin = 'gutenberg/gutenberg.php';
            if ( $this->check_gutenberg_installed( $plugin ) ) {
                if ( !current_user_can( 'activate_plugins' ) ) {
                    return;
                }
                $message              = sprintf( __( '%1$sThe Plus Addons for Block Editor%2$s plugin requires %1$sGutenberg%2$s plugin activated.', 'tpgb' ), '<strong>', '</strong>' );
                $button_text          = __( 'Activate Gutenberg', 'tpgb' );
                $gutenberg_action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
                
            } else {
                
                if ( !current_user_can( 'install_plugins' ) ) {
                    return;
                }
                $message              = sprintf( __( '%1$sThe Plus Addons for Block Editor%2$s plugin requires %1$sGutenberg%2$s plugin installed.', 'tpgb' ), '<strong>', '</strong>' );
                $button_text          = __( 'Install Gutenberg', 'tpgb' );
                $gutenberg_action_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=gutenberg' ), 'install-plugin_gutenberg' );
            }
            
            $button = '<p><a href="' . $gutenberg_action_url . '" class="button-primary">' . $button_text . '</a></p>';
            printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $notice_class ), $message, $button );
            
        }
        
        /**
         * Load The Plus Addon Gutenberg Text Domain.
         * Text Domain : tpgb
         * @since  1.0.0
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'tpgb', false, TPGB_BDNAME . '/lang' );
        }
        
        /**
         * If Check Gutenberg is installed
         *
         * @since 1.0.0
         *
         * @param string $plugin_url Plugin path.
         * @return boolean true | false
         * @access public
         */
        public function check_gutenberg_installed( $plugin_url ) {
            $get_plugins = get_plugins();
            return isset( $get_plugins[ $plugin_url ] );
        }

        /**
		 * Adds Links to the plugins page.
		 * @since 2.0.0
		 */
        public function tpgb_settings_pro_link( $links ){
            // Settings link.
            if ( current_user_can( 'manage_options' ) ) {
                $free_vs_pro = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url('https://theplusblocks.com/free-vs-pro/?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links'), __( 'FREE vs Pro', 'tpgb' ) );
                $links = (array) $links;
                $links[] = $free_vs_pro;
                $need_help = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url('https://store.posimyth.com/get-support/tpag/?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links'), __( 'Need Help?', 'tpgb' ) );
                $links = (array) $links;
                $links[] = $need_help;
            }

            // Upgrade PRO link.
            if ( ! defined('TPGBP_VERSION') ) {
                $pro_link = sprintf( '<a href="%s" target="_blank" style="color: #cc0000;font-weight: 700;" rel="noopener noreferrer">%s</a>', esc_url('https://theplusblocks.com/pricing/'), __( 'Upgrade PRO', 'tpgb' ) );
                $links = (array) $links;
                $links[] = $pro_link;
            }

            return $links;
        }

        /*
         * Adds Extra Links to the plugins row meta.
         * @since 2.0.0
         */
        public function tpbg_extra_links_plugin_row_meta( $plugin_meta = [], $plugin_file =''){

            if ( strpos( $plugin_file, TPGB_BASENAME ) !== false && current_user_can( 'manage_options' ) ) {
				$new_links = array(
						'official-site' => '<a href="'.esc_url('https://theplusblocks.com/?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Visit Plugin site', 'tpgb' ).'</a>',
						'docs' => '<a href="'.esc_url('https://theplusblocks.com/docs?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links').'" target="_blank" rel="noopener noreferrer" style="color:green;">'.esc_html__( 'Docs', 'tpgb' ).'</a>',
						'video-tutorials' => '<a href="'.esc_url('https://www.youtube.com/c/POSIMYTHInnovations/?sub_confirmation=1').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Video Tutorials', 'tpgb' ).'</a>',
						'join-community' => '<a href="'.esc_url('https://www.facebook.com/groups/theplus4gutenberg').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Join Community', 'tpgb' ).'</a>',
						'whats-new' => '<a href="'.esc_url('https://roadmap.theplusblocks.com/updates').'" target="_blank" rel="noopener noreferrer" style="color: orange;">'.esc_html__( 'What\'s New?', 'tpgb' ).'</a>',
						'req-feature' => '<a href="'.esc_url('https://roadmap.theplusblocks.com/boards/feature-requests').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Request Feature', 'tpgb' ).'</a>',
						'rate-plugin-star' => '<a href="'.esc_url('https://wordpress.org/support/plugin/the-plus-addons-for-block-editor/reviews/?filter=5').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Rate 5 Stars', 'tpgb' ).'</a>'
						);
				 
				$plugin_meta = array_merge( $plugin_meta, $new_links );
			}
			 
			return $plugin_meta;
        }

         /*
         * Activation Reset
         * @since 2.0.9
         */
        public function tpgb_activate_redirect($plugin){
            if( $plugin == 'the-plus-addons-for-block-editor/the-plus-addons-for-block-editor.php' ) {
                exit( wp_redirect( admin_url( 'admin.php?page=tpgb_welcome_page' ) ) );
            }
        }
    }
    
    TP_Gutenberg_Loader::get_instance();

    function tpgb_load_data() {
        return TP_Gutenberg_Loader::get_instance();
    }
}