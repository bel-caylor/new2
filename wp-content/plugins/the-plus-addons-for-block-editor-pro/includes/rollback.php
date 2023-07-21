<?php
/**
 * Tpgb Pro Rollback version
 * @since 1.3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!class_exists('Tpgb_Pro_Rollback')){

	class Tpgb_Pro_Rollback {
		
		/**
         * Member Variable
         * @var instance
         */
        private static $instance;
        
		protected $version;
		protected $plugin_slug;
		protected $plugin_name;
		protected $pakg_url;

		const tpgb_activate = 'tpgb_activate';
		const api_url = 'https://store.posimyth.com/wp-json/api/v2/get_versions';

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
			add_action( 'admin_post_tpgb_pro_rollback', [ $this, 'tpgb_pro_rollback_check_func' ] );
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

		public static function get_pro_version_lists(){
			
			$license= get_option( self::tpgb_activate );
			$license_data = Tpgb_Pro_Library::get_instance()->tpgb_activate_status();
			if(!empty($license) && !empty($license['tpgb_activate_key']) && !empty($license_data) && $license_data=='valid'){
				$args = [
					'license' => $license['tpgb_activate_key'],
					'version' => TPGBP_VERSION,
					'url' => home_url(),
				];
				$response = wp_remote_get( self::api_url, [ 'timeout' => 30,	'body' => $args ] );
		
				if ( is_wp_error( $response ) ) {
					return $response;
				}
		
				$response_code = (int) wp_remote_retrieve_response_code( $response );
				if ( 401 === $response_code ) {
					return new \WP_Error( $response_code, $data['message'] );
				}
		
				if ( 200 !== $response_code ) {
					return new \WP_Error( $response_code, esc_html__( 'HTTP Error', 'tpgbp' ) );
				}

				$body_data = json_decode( wp_remote_retrieve_body( $response ), true );
				
				if ( !empty( $body_data ) && is_array( $body_data ) && $body_data['success']==true ) {
					if(!empty($body_data['data']) && isset($body_data['data']['version_list']) && !empty($body_data['data']['version_list']) ){
						return $body_data['data']['version_list'];
					}
				}
				
			}
			return new \WP_Error( 'no_json', esc_html__( 'An error occurred, please try again', 'tpgbp' ) );
			
		}

		public static function get_rollback_versions() {
			$versions_list = get_transient( 'tpgbp_rollback_version_' . TPGBP_VERSION );
			//delete_transient('tpgbp_rollback_version_' . TPGBP_VERSION);
			if ( $versions_list === false ) {
				
				$versions_list= [];
				$versions_data = self::get_pro_version_lists();
				
				if ( is_wp_error( $versions_data ) ) {
					return [];
				}

				$index = 0;
				foreach ( $versions_data as $version ) {
					if ( 25 <= $index ) {
						break;
					}

					$lowercase_version = strtolower( $version );
					$check_rollback_version = ! preg_match( '/(beta|rc|trunk|dev)/i', $lowercase_version );

					$check_rollback_version = apply_filters(
						'tpgb_pro_check_rollback_version',
						$check_rollback_version,
						$lowercase_version
					);

					if ( ! $check_rollback_version ) {
						continue;
					}

					if ( version_compare( $version, TPGBP_VERSION, '>=' ) ) {
						continue;
					}

					$index++;

					$versions_list[] = $version;
				}

				set_transient( 'tpgbp_rollback_version_' . TPGBP_VERSION, $versions_list, DAY_IN_SECONDS );
			}
	
			return $versions_list;
		}

		public function get_rollback_version_package_url( $version ='' ){

			$license = get_option( self::tpgb_activate );
			$license_data = Tpgb_Pro_Library::get_instance()->tpgb_activate_status();
			if(!empty($license) && !empty($license['tpgb_activate_key']) && !empty($license_data) && $license_data=='valid'){
				$args = [
					'license' => $license['tpgb_activate_key'],
					'version' => $version,
					'url' => home_url(),
					'product_name' => 'The Plus Addons for Block Editor'
				];
				$response = wp_remote_get( self::api_url, [ 'timeout' => 30,	'body' => $args ] );
				if ( is_wp_error( $response ) ) {
					return $response;
				}
		
				$response_code = (int) wp_remote_retrieve_response_code( $response );
				if ( 401 === $response_code ) {
					return new \WP_Error( $response_code, $data['message'] );
				}
		
				if ( 200 !== $response_code ) {
					return new \WP_Error( $response_code, esc_html__( 'Not Found Data', 'tpgbp' ) );
				}

				$body_data = json_decode( wp_remote_retrieve_body( $response ), true );
				
				if ( !empty( $body_data ) && is_array( $body_data ) && $body_data['success']==true ) {
					if(!empty($body_data['data']) && isset($body_data['data']['version_list']) && in_array( $version, $body_data['data']['version_list'], true ) ){
						if( isset($body_data['data']['download_list']) && !empty($body_data['data']['download_list']) ){
							if( isset($body_data['data']['download_list'][$version]) ){
								return $body_data['data']['download_list'][$version];
							}
						}
					}
				}
			}
			return new \WP_Error( 'no_json', esc_html__( 'An error occurred, please try again', 'tpgbp' ) );
		}

		public function tpgb_pro_rollback_check_func(){
			check_admin_referer( 'tpgb_pro_rollback' );

			$rv = self::get_rollback_versions();
			if ( empty( $_GET['version'] ) || ! in_array( $_GET['version'], $rv, true ) ) {
				wp_die( esc_html__( 'Error, Try selecting another version.', 'tpgbp' ) );
			}

			$plugin_url = $this->get_rollback_version_package_url( $_GET['version'] );
			if ( is_wp_error( $plugin_url ) ) {
				wp_die( $plugin_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$plugin_slug = basename( TPGBP_FILE__, '.php' );
			
			$this->version = $_GET['version'];
			$this->plugin_name = TPGBP_BASENAME;
			$this->plugin_slug = $plugin_slug;
			$this->pakg_url = $plugin_url;
			
			$plugin_info = [
				'plugin_name' => $this->plugin_name,
				'plugin_slug' => $this->plugin_slug,
				'version' 	  => $this->version,
				'package_url' => $this->pakg_url,
			];

			$this->tpgb_update_plugin();
			$this->tpgb_upgrade_plugin();

			wp_die(
				'', esc_html__( 'Rollback to Previous Version', 'tpgbp' ), [
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
				'title' => '<img src="' . $logo_url . '" alt="tpgb-logo"><div class="tpgb-rb-subtitle">' . esc_html__( 'Rollback to Previous Version', 'tpgbp' ).'</div>',
			];

			$upgrader_plugin = new \Plugin_Upgrader( new \Plugin_Upgrader_Skin( $args ) );
			$upgrader_plugin->upgrade( $this->plugin_name );

		}

	}
	new Tpgb_Pro_Rollback();
}