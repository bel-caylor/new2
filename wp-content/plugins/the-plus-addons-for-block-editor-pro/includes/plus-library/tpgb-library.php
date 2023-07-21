<?php
/**
 * TPGB Pro Library
 *
 * @package tpgbp
 * @since 1.0.0
 */
if ( !class_exists( 'Tpgb_Pro_Library' ) ) {

	class Tpgb_Pro_Library {

		static $status = null;

		private static $_instance = null;
		
		static $licence_status = 'tpgbp_license_status',
		    $licence_nonce = 'tpgb_activte_nonce' ,
		    $valid_url = 'https://store.posimyth.com',
			$item_name = 'The Plus Addons for Block Editor',
		    $license_page = 'tpgb_activate';

		const tpgb_activate = 'tpgb_activate';

		public static function instance() {
			return self::$status;
		}
		
		/**
		 * Initiator
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self;
			}
			return self::$_instance;
		}
		
		function __construct() {

			self::$status = get_option( self::$licence_status );
			if(is_admin()){
				$status = $this->tpgb_activate_status();
				if(empty($status) || $status!='valid'){
					add_action( 'admin_notices', array( $this, 'tpgb_pro_licence_notice' ) );
				}
			}
			
			add_action( 'admin_post_tpgb_license_deactivate', array( $this,'tpgb_licence_deactivate_license') );
			add_action( 'admin_post_tpgb_license_activate', array( $this,'tpgb_licence_activate_license') );
		}
		
		public function tpgb_pro_licence_notice() {
		
			$status = $this->tpgb_activate_status();
			if( empty( $status ) ) {
				$admin_notice = '<h4 class="tpgb-notice-head">' . esc_html__( 'Activate The Plus Addons for Block Editor!!!', 'tpgbp' ) . '</h4>';
				$admin_notice .= '<p>' . esc_html__( 'You’re Just One Step Away From Having Fun While Crafting Websites using Block Editor of WordPress aka Gutenberg. Paste Your Licence Key Here. Visit', 'tpgbp' );
				$admin_notice .= sprintf( ' <a href="%s" target="_blank">%s</a>', esc_url('https://store.posimyth.com/'), esc_html__( 'POSIMYTH Store', 'tpgbp' ) ) . esc_html__(' to Generate Your Licence Key.', 'tpgbp' ).'</p>';
				$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', admin_url( 'admin.php?page=' . self::$license_page ) , esc_html__( 'I’ve Got a Licence Key', 'tpgbp' ) ) . '</p>';
				
				echo '<div class="notice notice-errors">'.wp_kses_post($admin_notice).'</div>';
			}else if(!empty($status) && $status=='expired'){
				$admin_notice = '<h4 class="tpgb-notice-head">' . esc_html__( 'Your The Plus Addons for Block Editor Licence is Expired !!!', 'tpgbp' ) . '</h4>';
				$admin_notice .= '<p>' . esc_html__( 'Seems Like Your Licence Key for The Plus Addons for Block Editor is Expired. Visit', 'tpgbp' );
				$admin_notice .= sprintf( ' <a href="%s" target="_blank">%s</a>', esc_url('https://store.posimyth.com/'), esc_html__( 'POSIMYTH Store', 'tpgbp' ) ) . esc_html__(' to Pay Invoices / Change Payment Methods / Manage Your Subscriptions. Please Don’t Hesitate to Reach Us at', 'tpgbp' ). sprintf( ' <a href="%s" target="_blank">%s</a>', esc_url('https://store.posimyth.com/helpdesk'), esc_html__( 'The Plus Gutenberg Support', 'tpgbp' ) ). esc_html__(' if You Have an Issue Regarding Our Products.','tpgbp').'</p>';
				$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', admin_url( 'admin.php?page=' . self::$license_page ) , esc_html__( 'I’ve Got a Licence Key', 'tpgbp' ) ) . '</p>';
				
				echo '<div class="notice notice-warning">'.wp_kses_post($admin_notice).'</div>';
			}
		}
		
		public static function tpgb_licence_activate_license() {

			// listen for our activate button to be clicked
			$submitKey = (isset($_POST["submit-key"]) && !empty($_POST["submit-key"])) ? sanitize_text_field(wp_unslash($_POST['submit-key'])) : '';
			if ( isset($submitKey) && !empty($submitKey) && $submitKey=='Activate' ) {

				// run a quick security check
				if ( ! check_admin_referer( self::$licence_nonce, self::$licence_nonce ) ) {
					return;
				}
				
				// retrieve the license from the database
				if( !isset($_POST['tpgb_activate_key']) || empty($_POST['tpgb_activate_key']) ) {
					wp_redirect( admin_url( 'admin.php?page=' . self::$license_page ) );
					exit;
				}
				
				$license = isset($_POST['tpgb_activate_key']) ? sanitize_key(wp_unslash($_POST['tpgb_activate_key'])) : '';
				
				$license_data = array();
				// data to send in our API request
				$api_params = array(
					'edd_action' => 'activate_license',
					'license' => $license,
					'item_name' => self::$item_name,
					'url' => home_url()
				);
				
				$response = wp_remote_get( self::$valid_url, array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'	  => $api_params
				) );
				
				$message = '';

				// make sure the response came back okay
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

					if ( is_wp_error( $response ) ) {
						$message = $response->get_error_message();
					} else {
						$message = __( 'An Error Occurred, Please Try Again Later.', 'tpgbp' );
					}

				} else {

					$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

					if ( is_array($license_data) && array_key_exists( 'success', $license_data ) && empty(  $license_data['success'] ) ) {

						switch( $license_data['error'] ) {

							case 'expired' :

								$message = sprintf(
									__( 'Your license key expired. <a href="%1$s" target="_blank">Manage Licence</a>', 'tpgbp' ), esc_url('https://store.posimyth.com/checkout/purchase-history/')
								);
								break;

							case 'revoked' :

								$message = sprintf( 
									__( 'Your license key has been disabled. <a href="%1$s" target="_blank">Reach out for Support</a>', 'tpgbp' ), esc_url('https://store.posimyth.com/helpdesk')
								);
								break;

							case 'missing' :
								$message = sprintf( 
									__( 'Invalid license. <a href="%1$s" target="_blank">Reach out for Support</a>', 'tpgbp' ), esc_url('https://store.posimyth.com/helpdesk')
								);
								break;

							case 'invalid' :
							case 'site_inactive' :

								$message = sprintf( 
									__( 'Your license is not active for this URL. <a href="%1$s" target="_blank">Manage Licence</a>', 'tpgbp' ), esc_url('https://store.posimyth.com/checkout/purchase-history/')
								);
								break;

							case 'item_name_mismatch' :
								/* translators: %s: item name */
								$message = sprintf( __( 'This appears to be an invalid license key for %1$s. <a href="%2$s" target="_blank">Manage Licence</a>', 'tpgbp' ), self::$item_name, esc_url('https://store.posimyth.com/checkout/purchase-history/') );
								break;

							case 'no_activations_left':

								$message = sprintf( __( 'Your license key has reached its activation limit. <a href="%1$s" target="_blank">Upgrade Licence</a>', 'tpgbp' ), esc_url('https://store.posimyth.com/checkout/purchase-history/') );
								break;

							default :

								$message = __( 'An Error Occurred, Please Try Again Later.', 'tpgbp' );
								break;
						}

					}else if( !empty($license_data) && $license_data['success'] == true && $license_data['success'] == 'valid' ) {
						$message = __( 'Your License successfully activated.', 'tpgbp' );
					}
					
				}
			
				$update_value = [ 'tpgb_activate_key' => $license ];
				update_option( self::tpgb_activate , $update_value );
				
				$status = [ 'status' => $license_data['license'], 'expired' => isset($license_data['expires']) ? $license_data['expires'] : '', 'message' => $message ];
				update_option( self::$licence_status, $status );
				
				wp_redirect( admin_url( 'admin.php?page=' . self::$license_page ) );
				exit();
				
			}else{
				wp_redirect( admin_url( 'admin.php?page=' . self::$license_page ) );
				exit;
			}
		}

		public static function tpgb_licence_deactivate_license() {

			// listen for our activate button to be clicked
			$submitKey = (isset($_POST["submit-key"]) && !empty($_POST["submit-key"])) ? sanitize_text_field(wp_unslash($_POST["submit-key"])) : '';
			if ( isset($submitKey) && !empty($submitKey) && $submitKey=='Deactivate' ) {

				// run a quick security check
				if ( ! check_admin_referer( self::$licence_nonce, self::$licence_nonce ) ) {
					return;
				} // get out if we didn't click the Activate button

				// retrieve the license from the database
				$license = get_option( self::tpgb_activate );

				if ( !empty( $license ) ) {
					delete_option( self::tpgb_activate );
					delete_option( self::$licence_status );
					delete_transient( 'tpgb_activate_transient' );
					delete_transient('tpgbp_rollback_version_' . TPGBP_VERSION);
				}

				$redirect = admin_url( 'admin.php?page=' . self::$license_page );
				wp_redirect( $redirect );
				exit();
			}
		}
		
		public function tpgb_activate_status() {
		
			$check_status = get_option( self::$licence_status );
			if( !empty($check_status) && $check_status['status'] == 'valid' ) {
				if( !empty($check_status) && !empty($check_status['expired']) && $check_status['expired'] != 'lifetime' ){
					$expired= strtotime($check_status['expired']);
					$today_date = strtotime("today midnight");
					if($today_date >= $expired ){
						$status = [ 'status' => 'expired', 'message' => esc_html__('Your license key expired.','tpgbp') ];
						update_option( self::$licence_status, array_merge($check_status, $status) );
						delete_transient( 'tpgb_activate_transient' );
						delete_transient('tpgbp_rollback_version_' . TPGBP_VERSION);
						return 'expired';
					}
				}
				return 'valid';
			}else if( !empty($check_status) && $check_status['status'] == 'expired' ){
				return 'expired';
			}else{
				return '';
			}
		}
		
		public static function tpgb_pro_activate_msg(){
			$check_status = get_option( self::$licence_status );
			$value = (!empty($check_status['status']) && isset($check_status['status'])) ? $check_status['status'] : '';
			$message = (!empty($check_status['message']) && isset($check_status['message'])) ? $check_status['message'] : '';
			switch( $value ) {

				case 'expired' :
					$message = '<h4 class="tpgb-notice-head">'. __( 'Your Licence Key is Expired !!!','tpgbp' ).'</h4>';
					/* translators: %s: support tpgb */
					$message .= sprintf( __( '<p>Seems Like Your Licence Key for The Plus Addons for Block Editor is Expired. Visit <a href="%1$s" target="_blank">POSIMYTH Store</a> to Pay Invoices / Change Payment Methods / Manage Your Subscriptions. Please Don’t Hesitate to Reach Us at <a href="%2$s" target="_blank">Help Desk</a> if You Have an Issue Regarding Our Products.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/'), esc_url('https://store.posimyth.com/helpdesk') );
					
					break;

				case 'valid' :
					$message = '<div style="width: auto;background-color: #fff;border-radius: 0 0 .25rem .25rem;border: none;margin: 0;"><div style="display: flex;flex-wrap: wrap;padding: 8px 0 8px 15px;border-left: 3px solid #25a964;color: #313131;font-size: 14px;line-height: 26px;align-items: center;"><svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31"><circle data-name="Ellipse 110" cx="15.5" cy="15.5" r="15.5" fill="#56a86a"/>
					<path d="M5.347,9.3,0,3.952,1.085,2.868,5.347,7.053,12.4,0l1.085,1.085Z" transform="translate(8.525 10.85)" fill="#fff"/>
					</svg><strong style="margin-left:15px;">'.esc_html__(' Cheers 🥳','tpgbp').'</strong> '.esc_html__('&nbsp;You have been successfully  activated.','tpgbp').'</div></div>';
					break;
					
				case 'revoked' :
					$message = '<h4 class="tpgb-notice-head">'. __( 'Licence key seems to be revoked due to unknown reasons.','tpgbp' ).'</h4>';
					/* translators: %s: support tpgb */
					$message .= sprintf( __( '<p>Your Licence Key for The Plus Addons for Block Editor is Revoked for Unknown Reason. Visit <a href="%1$s" target="_blank">POSIMYTH Store</a> to Update Your Licence Key / Manage Payments / Pay Invoices. Reach Out to Us at <a href="%2$s" target="_blank">Help Desk</a> for Queries Regarding Our Products.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/'), esc_url('https://store.posimyth.com/helpdesk') );
					break;

				case 'missing' :
					$message = '<h4 class="tpgb-notice-head">'. __( 'It’s Time to Enter Licence Key','tpgbp' ).'</h4>';
					/* translators: %s: store tpgb */
					$message .= sprintf( __( '<p>You’re Just One Step Away From Having Fun While Crafting Websites. Paste Your Licence Key for The Plus Addons for Block Editor Here. Visit <a href="%s" target="_blank">POSIMYTH Store</a> to Update Your Licence Key / Manage Payments / Pay Invoices.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/') );
					break;

				case 'invalid' :
				case 'site_inactive' :
					$message = '<h4 class="tpgb-notice-head">'. __( 'Typo in Licence Key or Site not added in Activation List !','tpgbp' ).'</h4>';
					/* translators: %s: store url */
					$message .= sprintf( __( '<p>We Can’t Find Licence Key You Just Entered in Any of Our Lists. Make Sure You Are Not Adding Any White Spaces With It. If You\'re Having This Issue Repeatedly, Visit <a href="%s" target="_blank">POSIMYTH Store</a> to Confirm Your Licence Key.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/') );
					break;

				case 'item_name_mismatch' :
					$message = '<h4 class="tpgb-notice-head">'. __( 'This License Key Belongs to Some Other Product','tpgbp' ).'</h4>';
					/* translators: %s: store url */
					$message .= sprintf( __( '<p>It Appears That Licence Key You Entered Belongs to Some Other Product from Our Product Collection. In Layman Terms, You Dialed a Wrong Number. Visit <a href="%s" target="_blank">POSIMYTH Store</a> and Verify Your Licence Key for The Plus Addons for Block Editor.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/') );
					break;

				case 'no_activations_left':
					$message = '<h4 class="tpgb-notice-head">'. __( 'You Should’ve Ordered More !!!','tpgbp' ).'</h4>';
					/* translators: %s: store url */
					$message .= sprintf( __( '<p>Unfortunately, Your Activation Quota for Active / Running Websites Built With The Plus Addons for Block Editor  is Over. Like What You’re Using ? Visit <a href="%s" target="_blank">POSIMYTH Store</a> to Upgrade Your Existing Plan and Allow Your Creativity to Bloom.</p>', 'tpgbp' ), esc_url('https://store.posimyth.com/') );
					break;

				default :
					$message = '';
					
					break;
			}
			$check_status['message'] = $message;
			return $check_status;
		}
		
	}
}