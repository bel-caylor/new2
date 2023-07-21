<?php
/**
 * The Plus Gutenberg Pro Loader.
 * @since 1.0.0
 * @package TPGBP_Gutenberg_Pro_Loader
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( !class_exists( 'TPGBP_Gutenberg_Pro_Loader' ) ) {
    
    /**
     * Class TPGBP_Gutenberg_Pro_Loader.
     */
    final class TPGBP_Gutenberg_Pro_Loader {
        
        /**
         * Constructor
         */
        public function __construct() {
		
			$this->load_textdomain();
			
            if( is_admin() ) {
				add_filter( 'plugin_action_links_' . TPGBP_BASENAME, array( $this, 'tpgbp_add_settings_link' ) );
				add_filter( 'plugin_row_meta', array( $this, 'tpgbp_extra_links_plugin_row_meta' ), 10, 2 );
            }
            $this->loader_helper();
			
        }
        
        /**
         * Loads Helper/Other files.
         *
         * @since 1.0.0
         *
         * @return void
         */
        public function loader_helper() {
            //Load CMB2 
			if ( class_exists( 'CMB2_Bootstrap_270' ) ){
                require TPGBP_PATH . 'includes/rollback.php';
				require TPGBP_PATH . 'includes/plus-settings-options.php';
			}
			
			//Load Conditions Rules
			require_once TPGBP_PATH . 'classes/extras/tpgb-conditions-rules.php';
            
            //Load Plus Extras Opt
            require_once TPGBP_PATH . 'classes/extras/tpgb-plus-extras.php';

			//Load Magic Scroll
			require_once TPGBP_PATH . 'classes/extras/tpgb-magic-scroll.php';
            
			//Load Init Blocks Files
			require_once TPGBP_PATH . 'classes/tp-block-helper.php';
			
			//Load Plugin Wp Enqueue Scripts and Styles
            require_once TPGBP_PATH . 'classes/tp-core-init-blocks.php';
			
            //Login Register Custom ajax
            require_once TPGBP_PATH . 'classes/tp-custom-ajax.php';
        }
        
        /**
         * Load The Plus Addon Gutenberg Pro Text Domain.
         * Text Domain : tpgbp
         * @since  1.0.0
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain( 'tpgbp', false, TPGBP_BDNAME . '/lang' );
        }
        
        /**
		 * Adds Links to the plugins page.
		 * @since 2.0.0
		 */
		public function tpgbp_add_settings_link( $links ) {
			
			// Need Help link.
            $need_help = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url('https://store.posimyth.com/get-support/tpag/?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links'), __( 'Need Help?', 'tpgbp' ) );
            $license = sprintf( '<a href="%s" style="color:green;font-weight:600;">%s</a>',  admin_url( 'admin.php?page=tpgb_activate' ), __( 'License', 'tpgbp' ) );
            $links = (array) $links;
            $links[] = $need_help;
            $links[] = $license;

			return $links;
		}

        /**
		 * Adds Extra Links to the plugins row meta.
		 * @since 1.1.0
		 */
		public function tpgbp_extra_links_plugin_row_meta( $plugin_meta, $plugin_file ) {
 
			if ( strpos( $plugin_file, TPGBP_BASENAME ) !== false && current_user_can( 'manage_options' ) ) {
				$new_links = array(
						'docs' => '<a href="'.esc_url('https://theplusblocks.com/docs?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links').'" target="_blank" rel="noopener noreferrer" style="color:green;">'.esc_html__( 'Docs', 'tpgbp' ).'</a>',
                        'video-tutorials' => '<a href="'.esc_url('https://www.youtube.com/c/POSIMYTHInnovations/?sub_confirmation=1').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Video Tutorials', 'tpgbp' ).'</a>',
						'join-community' => '<a href="'.esc_url('https://www.facebook.com/groups/theplus4gutenberg').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Join Community', 'tpgbp' ).'</a>',
						'whats-new' => '<a href="'.esc_url('https://roadmap.theplusblocks.com/updates').'" target="_blank" rel="noopener noreferrer" style="color: orange;">'.esc_html__( 'What\'s New?', 'tpgbp' ).'</a>',
						'req-feature' => '<a href="'.esc_url('https://roadmap.theplusblocks.com/boards/feature-requests').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Request Feature', 'tpgbp' ).'</a>',
						'rate-theme' => '<a href="'.esc_url('https://wordpress.org/support/plugin/the-plus-addons-for-block-editor/reviews/?filter=5').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Rate 5 Stars', 'tpgbp' ).'</a>'
						);
				 
				$plugin_meta = array_merge( $plugin_meta, $new_links );
			}
			 
			return $plugin_meta;
		}
    }
    
    new TPGBP_Gutenberg_Pro_Loader();
}