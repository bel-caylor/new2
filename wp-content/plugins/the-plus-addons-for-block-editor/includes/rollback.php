<?php
/**
 * Tpgb Rollback version
 * @since 1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!class_exists('Tpgb_Rollback')){

	class Tpgb_Rollback {
		
		/**
         * Member Variable
         * @var instance
         */
        private static $instance;
        
		protected $version;
		protected $plugin_slug;
		protected $plugin_name;
		protected $pakg_url;

        /**
         * Initiator
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
			add_action( 'admin_post_tpgb_rollback', [ $this, 'tpgb_rollback_check_func' ] );
        }

		private function rollback_page_style() {
			?>
			<style>
				body#error-page {
					border: 0;
					background: #fff;
					padding: 0;
					border-radius: 5px;
				}
				.wrap {
					position: relative;
					margin: 0 auto;
					border: 2px solid #6f1ef1;
					border-radius: 5px;
					-webkit-box-shadow: 0 0 35px 0 rgb(154 161 171 / 15%);
					box-shadow: 0 0 35px 0 rgb(154 161 171 / 15%);
					padding: 0 20px;
					font-family: Courier, monospace;
					overflow: hidden;
					max-width: 850px;
				}
				.wrap h1 {
					text-align: center;
					color: #fff;
					background: #6f1ef1;
					padding: 60px;
					letter-spacing: 0.8px;
					border-radius: 5px;
				}
				.wrap h1 img {
					display: block;
					max-width: 250px;
					margin: auto auto 35px;
				}
				.tpgb-rb-subtitle{
					font-size: 18px;
    				font-family: monospace;
				}
			</style>
			<?php
		}

		public static function get_rollback_versions() {
			$versions_list = get_transient( 'tpgb_rollback_version_' . TPGB_VERSION );
			if ( $versions_list === false ) {
				
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	
				$plugin_info = plugins_api(
					'plugin_information', [
						'slug' => 'the-plus-addons-for-block-editor',
					]
				);
	
				if ( empty( $plugin_info->versions ) || ! is_array( $plugin_info->versions ) ) {
					return [];
				}
	
				krsort( $plugin_info->versions );
	
				$versions_list = [];
	
				$index = 0;
				foreach ( $plugin_info->versions as $version => $download_link ) {
					if ( 25 <= $index ) {
						break;
					}
	
					$lowercase_version = strtolower( $version );
					$check_rollback_version = ! preg_match( '/(beta|rc|trunk|dev)/i', $lowercase_version );
	
					$check_rollback_version = apply_filters(
						'tpgb_check_rollback_version',
						$check_rollback_version,
						$lowercase_version
					);
	
					if ( ! $check_rollback_version ) {
						continue;
					}
	
					if ( version_compare( $version, TPGB_VERSION, '>=' ) ) {
						continue;
					}
	
					$index++;
					$versions_list[] = $version;
				}
	
				set_transient( 'tpgb_rollback_version_' . TPGB_VERSION, $versions_list, WEEK_IN_SECONDS );
			}
	
			return $versions_list;
		}

		public function tpgb_rollback_check_func(){
			check_admin_referer( 'tpgb_rollback' );

			if ( ! static::update_user_rollback_versions() ) {
				wp_die( esc_html__( 'Rollback versions not allowed', 'tpgb' ) );
			}

			$rv = self::get_rollback_versions();
			if ( empty( $_GET['version'] ) || ! in_array( $_GET['version'], $rv ) ) {
				wp_die( esc_html__( 'Error, Try selecting another version.', 'tpgb' ) );
			}

			$plugin_slug = basename( TPGB_FILE__, '.php' );
			
			$this->version = $_GET['version'];
			$this->plugin_name = TPGB_BASENAME;
			$this->plugin_slug = $plugin_slug;
			$this->pakg_url = sprintf( 'https://downloads.wordpress.org/plugin/%s.%s.zip', $this->plugin_slug, $this->version );
			
			$plugin_info = [
				'plugin_name' => $this->plugin_name,
				'plugin_slug' => $this->plugin_slug,
				'version' 	  => $this->version,
				'package_url' => $this->pakg_url,
			];

			$this->tpgb_update_plugin();
			$this->tpgb_upgrade_plugin();

			wp_die(
				'', esc_html__( 'Rollback to Previous Version', 'tpgb' ), [
					'response' => 200,
				]
			);
		}

		public function tpgb_update_plugin(){
			$update_plugins_data = get_site_transient( 'update_plugins' );

			if ( ! is_object( $update_plugins_data ) ) {
				$update_plugins_data = new \stdClass();
			}

			$plugin_info = new \stdClass();
			$plugin_info->new_version = $this->version;
			$plugin_info->slug = $this->plugin_slug;
			$plugin_info->package = $this->pakg_url;
			$plugin_info->url = 'http://theplusblocks.com/';

			$update_plugins_data->response[ $this->plugin_name ] = $plugin_info;

			// Remove handle beta testers.
			//remove_filter( 'pre_set_site_transient_update_plugins', [ Plugin::instance()->beta_testers, 'check_version' ] );

			set_site_transient( 'update_plugins', $update_plugins_data );
		}

		public function tpgb_upgrade_plugin(){

			require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

			$this->rollback_page_style();

			$logo_url = TPGB_URL . 'assets/images/theplus-logo.png';

			$args = [
				'url' => 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $this->plugin_name ),
				'plugin' => $this->plugin_name,
				'nonce' => 'upgrade-plugin_' . $this->plugin_name,
				'title' => '<img src="' . $logo_url . '" alt="tpgb-logo"><div class="tpgb-rb-subtitle">' . esc_html__( 'Rollback to Previous Version', 'tpgb' ).'</div>',
			];

			$upgrader_plugin = new \Plugin_Upgrader( new \Plugin_Upgrader_Skin( $args ) );
			$upgrader_plugin->upgrade( $this->plugin_name );

		}

		/**
		 * Check current user can access the version control and rollback versions.
		 */
		public static function update_user_rollback_versions() {
			return current_user_can( 'activate_plugins' ) && current_user_can( 'update_plugins' );
		}
	}
	new Tpgb_Rollback();
}