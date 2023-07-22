<?php
/**
 * TPGB Login Register Ajax
 *
 * @since 2.0.0
 * @package TPGBP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Tp_Blocks_Ajax { 

    /**
     * Member Variable
     *
     * @var instance
     */
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

    /**
     * Constructor
     */
	public function __construct() {

        //Register Ajax 
        add_action('wp_ajax_tpgb_register_user', array($this, 'tpgb_register_user'));
        add_action('wp_ajax_nopriv_tpgb_register_user', array($this, 'tpgb_register_user'));

        //Login Ajax
        add_action('wp_ajax_tpgb_login_user', array($this, 'tpgb_login_user'));
        add_action('wp_ajax_nopriv_tpgb_login_user', array($this, 'tpgb_login_user'));

        // Forget Password
        add_action('wp_ajax_tpgb_ajax_forgot_password', array($this, 'tpgb_ajax_forgot_password'));
        add_action('wp_ajax_nopriv_tpgb_ajax_forgot_password', array($this, 'tpgb_ajax_forgot_password'));

        //Set Lost Password Url
        add_action( 'login_form_tpgbreset', array($this, 'tpgb_custom_password_reset') );
        if(!empty($_GET['action']) && $_GET['action']=='tpgbreset'){
            add_action( 'login_form_resetpass',array($this, 'tpgb_custom_password_reset')  );
        }

        /*reset password start*/
        add_action( 'wp_ajax_tpgb_ajax_reset_password',  array($this, 'tpgb_ajax_reset_password')  );
        add_action( 'wp_ajax_nopriv_tpgb_ajax_reset_password', array($this, 'tpgb_ajax_reset_password') );
        
        // Facebook Login
        add_action( 'wp_ajax_tpgb_facebook_login',  array($this, 'tpgb_facebook_login')  );
        add_action( 'wp_ajax_nopriv_tpgb_facebook_login', array($this, 'tpgb_facebook_login') );

        add_action( 'wp_ajax_tpgb_google_pic',  array( $this, 'tpgb_google_pic')  );
        add_action( 'wp_ajax_nopriv_tpgb_google_pic', array( $this , 'tpgb_google_pic' ) );
        
        // Send Magic Link
        add_action( 'wp_ajax_tpgb_send_magic_link',  array( $this, 'tpgb_send_magic_link')  );
        add_action( 'wp_ajax_nopriv_tpgb_send_magic_link', array( $this , 'tpgb_send_magic_link' ) );

        //Set Magic Link
        if(!empty($_GET['action']) && $_GET['action']=='tpgbmagic'){
            $this->tpgb_parse_magic_link();
        }
    }

    /*
     * Check Login Register Activate Or Not
     * @since 2.0.0
     */

    public function tpgb_check_login_activate(){
        $tpgb_normal_blocks_opts=get_option( 'tpgb_normal_blocks_opts' );
        if($tpgb_normal_blocks_opts !== false){
            if( isset($tpgb_normal_blocks_opts['enable_normal_blocks']) && !empty($tpgb_normal_blocks_opts['enable_normal_blocks']) && in_array('tp-login-register',$tpgb_normal_blocks_opts['enable_normal_blocks'])){
                return true;
            }else{
                return false;
            }
        }
    }

    /*
     * Register User Function 
     * @since 2.0.0
     */
    public function tpgb_register_user(){
        if( (!isset( $_POST['tpgb-user-reg-token'] ) || !wp_verify_nonce( $_POST['tpgb-user-reg-token'], 'ajax-login-nonce' ) )  ){		
            $regresponse['data']['nonceCheck'] = ['registered'=> false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )];

            echo wp_send_json($regresponse);
            exit();
        }

        if( !$this->tpgb_check_login_activate() ){

            $regresponse['data']['nonceCheck'] = ['registered'=> false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )];
            echo wp_send_json($regresponse);
            
            exit();
        }

        if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            if ( !get_option( 'users_can_register' ) ) {
                $regresponse['data']['checkRegister'] = ['registered'=>false, 'message'=> esc_html__( 'Enable Membership  Option From Site General Setting', 'tpgbp' )];

                echo wp_send_json($regresponse);
                exit();

            } else {
                $email      = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
                $first_name = isset($_POST['first-name']) ? sanitize_text_field( $_POST['first-name'] ) : '';
                $last_name  = isset($_POST['last-name']) ? sanitize_text_field( $_POST['last-name'] ) : '';
                $username  = isset($_POST['username']) ? sanitize_text_field( $_POST['username'] ) : '';
                $passwordemc  = isset($_POST['repassword']) ? $_POST['repassword'] :  wp_generate_password( 12, true, false ) ;
                $confirmPass = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : $passwordemc ;
                $acf_val = isset($_POST['tpgb_acf']) ? $_POST['tpgb_acf'] : [] ;
                $acf_key = isset($_POST['tpgb_acf_key']) ? $_POST['tpgb_acf_key'] : [] ;
               
                $reCaptchKey = (!empty($options['tpgb_site_key_recaptcha'])) ? $options['tpgb_site_key_recaptcha'] : '';
                $check_captcha = false;
                $action = '';
                $tpgb_file_key = isset($_POST['tpgb_file_key']) ? $_POST['tpgb_file_key'] : '' ;
                $tpgb_file_data = [];
                $acf_arr_file = [];

                if( count($acf_key) == count($acf_val) ) {
                    $acf_arr = array_combine(  $acf_key ,  $acf_val  );
                }else{
                    $regresponse['fieldmissing'] = ['registered'=> false, 'message'=> esc_html__( 'Field Is Missing', 'tpgbp' )];
                    wp_send_json_success($regresponse);
                    exit();
                }

                // File Upload
                if( isset($_FILES['tpgb_file_acf'])){
                    for($i = 0; $i < count($_FILES['tpgb_file_acf']['name']); $i++) {
                       
                        $upload = wp_upload_bits($_FILES['tpgb_file_acf']['name'][$i], null, file_get_contents($_FILES['tpgb_file_acf']['tmp_name'][$i]));
                        
                        $file = $upload['file'];
                        $url = $upload['url'];
                        $type = $upload['type'];

                        $attachment = array(
                            'post_mime_type' => $type ,
                            'post_title' => $_FILES['tpgb_file_acf']['name'][$i],
                            'post_content' => 'File '.$_FILES['tpgb_file_acf']['name'][$i],
                            'post_status' => 'inherit'
                            );

                        $attach_id = wp_insert_attachment( $attachment, $file, 0);
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        $tpgb_file_data[] = $attach_id; 
                    }
                    $acf_arr_file = array_combine( $tpgb_file_key ,  $tpgb_file_data );
                }

                if( is_array($acf_arr_file) && count($acf_arr_file) > 0){
                    $acf_arr = array_merge($acf_arr , $acf_arr_file );
                }
                

                // Check Recaptcha
                if(isset($_POST['recaptchEn']) && $_POST['recaptchEn'] == 'yes' ){
                    if( !isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response']) ){
                        $message = sprintf(__( 'Please check the the captcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                        $regresponse['recaptcha'] = ['registered' => false, 'message'=> $message];					
                        exit;
                    }else{

                        if( isset( $_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']) && !empty($reCaptchKey)  ){

                            $ip = $_SERVER['REMOTE_ADDR'];
                            
                            $url = 'https://www.google.com/recaptcha/api/siteverify';
                            $data = array('secret' => $reCaptchKey, 'response' => $_POST['g-recaptcha-response']);
                            
                            $options = array(
                                'http' => array(
                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                'method'  => 'POST',
                                'content' => http_build_query($data)
                                )
                            );
                            
                            
                            $recaptcha_secret = isset($data['secret']) ? $data['secret'] : '';
                            $recaptcha_respo = isset($data['response']) ? $data['response'] : '';					
                            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $recaptcha_respo);
                            $responseKeys = json_decode($response["body"], true);
                            
                            $resscore=$responseKeys["score"];
                            $check_captcha = true;
                            if(!$responseKeys['success']){
                                $message = sprintf(__( 'Please check the the reCaptcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                                $regresponse['recaptcha'] = ['registered' => false, 'message'=> $message ] ;
                                exit;
                            }
                        }
                    }
                }
                $userRegi = $this->tpgb_ajax_register_user( $email, $first_name, $last_name , $username , $passwordemc , $confirmPass , $acf_arr );
                if(empty($userRegi)){				
                    $regresponse['userRegi'] = ['registered'=>false, 'message'=> esc_html__( 'Username Already Exists.', 'tpgbp' )];		
                }else if ( is_wp_error( $userRegi ) ) {
                    $errors  = $userRegi->get_error_message();
                    $regresponse['userRegi'] = ['registered' => false, 'message'=> $errors ];
                } else {
                   
                    if(!empty($_POST['regiAction'])){

                        $action = explode(',', $_POST['regiAction']);

                        if( in_array('sendemail',$action)){

                            if ( is_multisite() ) {
                                $site_name = get_network()->site_name;
                            } else {
                                $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
                            }

                            $esub =  stripslashes(html_entity_decode($_POST['subject']));
					        $emsg =  stripslashes(html_entity_decode($_POST['emailBody']));
                            $find = array( '/\[tpgb_firstname\]/', '/\[tpgb_lastname\]/', '/\[tpgb_username\]/', '/\[tpgb_email\]/', '/\[tpgb_password\]/' , '/\[tpgb_sitename\]/' );
                            $replacement = array( $first_name,$last_name, $username, $email,$passwordemc , $site_name  );
                            $cmessage = preg_replace( $find, $replacement, $emsg );

                            $esub = preg_replace( $find, $replacement, $esub );

                            $headers = array( 'Content-Type: text/html; charset=UTF-8' );

                            $mail = wp_mail( $email, $esub, $cmessage, $headers );
                            if(!empty($mail)){
                                $message = sprintf(__( 'You have successfully registered to %s. We have emailed your password to the email address you entered.', 'tpgbp' ), get_bloginfo( 'name' ) );
                            }else{
                                $message = sprintf(__( 'The e-mail could not be sent','tpgbp'));
                            }
                            $regresponse['regiAction'][] = [ 'action' => 'sendemail' , 'registered'=>true, 'message'=> $message];

                        }
                        if( in_array('autologin',$action)){
                            $access_info = [];
                            $access_info['user_login']    = !empty($email) ? $email : "";
                            $access_info['user_password'] = !empty($passwordemc) ?  $passwordemc : "";
                            $access_info['rememberme']    = true;
                            $user_signon = wp_signon( $access_info, false );
                            if ( !is_wp_error($user_signon) ){				
                                $regresponse['regiAction'][] = [ 'action' => 'autologin' , 'registered' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'tpgbp')];
                            } else {
                                $regresponse['regiAction'][] = [ 'action' => 'autologin' , 'registered' => false, 'message'=> esc_html__('Registered Successfully, Ops! Login Failed...!', 'tpgbp')];
                            }
                        }
                        if( in_array('redirect',$action)){
                            $regresponse['regiAction'][] = [ 'action' => 'redirect' , 'registered' => true, 'message'=> esc_html__('Redirecting...', 'tpgbp')];
                        }
                    }
                    if(isset($_POST['mailscb']) && $_POST['mailscb'] == 'on' ){
                        $mailApikey = isset($_POST["apiKey"]) ? $_POST["apiKey"] : '';
                        $maillistId = isset($_POST["listId"]) ? $_POST["listId"] : '';

                        $mgroupVal=$mtagVal='';

                        $mc_r_status = 'subscribed';
                        if( isset($_POST['doubleOpt']) && !empty($_POST['doubleOpt']) && $_POST['doubleOpt']=='yes'){
                            $mc_r_status = 'pending';
                        }

                        if(!empty($_POST['TagId']) && sanitize_text_field($_POST['TagId'])){
                            $mtagVal= sanitize_text_field($_POST['TagId']);
                        }

                        $result = json_decode( Tpgbp_Pro_Blocks_Helper::tpgb_mailchimp_subscriber_message($email, $mc_r_status, $maillistId, $mailApikey, array('FNAME' => $first_name,'LNAME' => $last_name),$mgroupVal,$mtagVal ) );
                        
                        $regresponse['mailscb'] = $result;
                    }else{
                        $regresponse['userRegi'] = ['registered' => true, 'message'=> esc_html__('Registered Successfully', 'tpgbp')];
                    }
                    
                }
            }
        }
        wp_send_json_success($regresponse);
        exit();
    }

    public function tpgb_ajax_register_user($email='', $first_name='', $last_name='' , $username='' , $passwordemc='' , $confirmPass='' , $meta_arr = [] ){

        $errors = new \WP_Error();
		$result = '';
	    if ( ! is_email( $email ) ) {
	        $errors->add( 'email', esc_html__( 'The email address you entered is not valid.', 'tpgbp' ) );
	        return $errors;
	    }
	
	    if ( username_exists( $email ) || email_exists( $email ) ) {
	        $errors->add( 'email_exists', esc_html__( 'An account exists with this email address.', 'tpgbp' ) );
	        return $errors;
	    }
		
	    if(!empty($passwordemc)){
            if($passwordemc === $confirmPass){	
                $password = $passwordemc;
            }else{
                $errors->add( 'pass_mismatch', esc_html__( 'Password & Confirm Password Not Match!', 'tpgbp' ) );
                return $errors;
            }
						
		}else{
			$password = wp_generate_password( 12, false );
		}
		
		if(!empty($username)){
			$user_login = !empty($username) ? sanitize_user($username) : '';
		}else{
			$user_login = $email;
		}
		
	    $user_data = array(
	        'user_login'    => $user_login,
	        'user_email'    => $email,
	        'user_pass'     => $password,
	        'first_name'    => $first_name,
	        'last_name'     => $last_name,
	        'nickname'      => $first_name,
	    );

		$user_id_get = username_exists( $user_login );
		
		$user_id='';
		if ( ! $user_id_get ) {
			$user_id = wp_insert_user( $user_data );
            
            if(!empty($meta_arr)){
                foreach( $meta_arr as $key => $val ) {
                    update_user_meta( $user_id , $key , $val, true );
                }
            }

			wp_update_user( array ('ID' => $user_id) ) ;
		}
		
	    return $user_id;
    }

    /*
     * Login User Function 
     *  @since 2.0.0
     */
    public function tpgb_login_user(){

        $loginData = array();
	    parse_str($_POST['loformData'], $loginData);

        if( (!isset( $loginData['tpgb-user-login-token'] ) || !wp_verify_nonce( $loginData['tpgb-user-login-token'], 'tpgb-ajax-login-nonce' ) )  ){		
            echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            exit;
        }

        if( !$this->tpgb_check_login_activate() ){
            echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            exit();
        }

        $access_info = [];		
        $access_info['user_login']    = !empty($loginData['username']) ? sanitize_user($loginData['username']) : "";
        $access_info['user_password'] = !empty($loginData['password']) ? $loginData['password'] : "";
        $access_info['rememberme']  = true;
        
        $user_signon = wp_signon( $access_info );
        
        if ( !is_wp_error($user_signon) ){
            
            $userID = $user_signon->ID;
            wp_set_current_user( $userID, $access_info['user_login'] );
            wp_set_auth_cookie( $userID, true, true );
            
            echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'tpgbp')] );
            
        } else {
            if ( isset( $user_signon->errors['invalid_email'][0] ) ) {
                
                echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Invalid Email..!', 'tpgbp')] );
            } elseif ( isset( $user_signon->errors['invalid_username'][0] ) ) {

                echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Invalid Username..!', 'tpgbp')] );
            } elseif ( isset( $user_signon->errors['incorrect_password'][0] ) ) {
                
                echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Ops! Incorrent Password..!', 'tpgbp')] );
            }
        }
        die();
    }

    /*
     * Forget Password Function 
     *  @since 2.0.0
     */
    public function tpgb_ajax_forgot_password(){
        global $wpdb, $wp_hasher;
        $forpassData = array();
	    parse_str($_POST['lostpassData'], $forpassData);
        
        // decrypt Data
        $forgotdata = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($_POST['tpgbforgotdata']);
	    $forgotdata = (array) json_decode(html_entity_decode($forgotdata));
        if( (!isset( $forgotdata['noncesecure'] ) || !wp_verify_nonce( $forgotdata['noncesecure'], 'tpgb_reset_action' ) )  ){		
            echo wp_json_encode( ['lost_pass'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            exit;
        }
        
        if( !$this->tpgb_check_login_activate() ){
            echo wp_json_encode( ['lost_pass'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            exit();
        }

        if(isset($_POST['recaptchEn']) && $_POST['recaptchEn'] == 'yes' ){
            if( !isset($forgotdata['g-recaptcha-response']) && empty($forgotdata['g-recaptcha-response']) ){
                $message = sprintf(__( 'Please check the the captcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                $regresponse['recaptcha'] = ['registered' => false, 'message'=> $message];					
                exit;
            }else{

                if( isset( $forgotdata['g-recaptcha-response']) && !empty($forgotdata['g-recaptcha-response']) && !empty($reCaptchKey)  ){

                    $ip = $_SERVER['REMOTE_ADDR'];
                    
                    $url = 'https://www.google.com/recaptcha/api/siteverify';
                    $data = array('secret' => $reCaptchKey, 'response' => $forgotdata['g-recaptcha-response']);
                    
                    $options = array(
                        'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                        )
                    );
                    
                    
                    $recaptcha_secret = isset($data['secret']) ? $data['secret'] : '';
                    $recaptcha_respo = isset($data['response']) ? $data['response'] : '';					
                    $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $recaptcha_respo);
                    $responseKeys = json_decode($response["body"], true);
                    
                    $resscore=$responseKeys["score"];
                    $check_captcha = true;
                    if(!$responseKeys['success']){
                        $message = sprintf(__( 'Please check the the reCaptcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                        $regresponse['recaptcha'] = ['lost_pass' => 'could_not_sent', 'message'=> $message ] ;
                        exit;
                    }
                }
            }
        }

        $user_login = isset($forpassData['forget-password']) ? wp_unslash($forpassData['forget-password']) : '';

        if ( empty( $user_login ) || !is_string( $user_login ) ) {      
            echo wp_json_encode( [ 'lost_pass'=>'could_not_sent', 'message'=> sprintf(__( '<strong>ERROR</strong>: Enter a username or email address.','tpgbp' )) ] );
            exit;
        } elseif ( strpos( $user_login, '@' ) ) {
            $user_data = get_user_by( 'email', trim( wp_unslash( $user_login ) ) );
            if ( empty( $user_data ) ) {          
                echo wp_json_encode( [ 'lost_pass'=>'could_not_sent', 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','tpgbp' )) ] );
                exit;
            }
        } else {
            $login = trim( $user_login );
            $user_data = get_user_by( 'login', $login );
            if ( !$user_data ) {			
                echo wp_json_encode( [ 'lost_pass'=>'could_not_sent', 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','tpgbp' )) ] );
                exit;
            }
        }

        if(!empty($user_data->data)){
            $userData = (array) $user_data->data;
            $user_login = $userData['user_login'];
            $user_email = $userData['user_email'];
        }
        $key = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            return $key;
        }
        if ( is_multisite() ) {
            $site_name = get_network()->site_name;
        } else {
            $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        }

        /*forgot password mail*/
        $message='';
        $ctmemailData = ( isset($forgotdata['customEmail']) ) ? (array) $forgotdata['customEmail'] : '' ;
        if(!empty( $ctmemailData) && (!empty( $ctmemailData['ctmEmail']) &&  $ctmemailData['ctmEmail']=='yes')){
                        
            $elsub =  html_entity_decode( $ctmemailData['loSubject']);
            $elmsg =  html_entity_decode( $ctmemailData['loMessage']);
            
            if(!empty($forgotdata["linkOpt"]) && $forgotdata["linkOpt"]=='default'){		
                $tplr_link_get = network_site_url( "wp-login.php?action=rp&key=$key&login=".rawurlencode( $user_login ), 'login' );		
            }else if(!empty($forgotdata["linkOpt"]) && $forgotdata["linkOpt"]=='custom'){
                $fplInkData = [];
                $fplInkData['key'] = $key;
                $fplInkData['redirecturl'] = $forgotdata['reset_url'];
                $fplInkData['forgoturl'] = $forgotdata['forgot_url'];
                $fplInkData['login'] = rawurlencode( $user_login );
            
                $frontdata_key= Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($fplInkData), 'ey');
                
                $tplr_link_get = network_site_url( "wp-login.php?action=tpgbreset&datakey=$frontdata_key", 'login' );
            }
            
            $elfind = array( '/\[tpgb_sitename\]/', '/\[tpgb_username\]/', '/\[tpgb_link\]/' );
            $lrreplacement = array( $site_name,$user_login,$tplr_link_get);		
            $clrmessage = preg_replace( $elfind,$lrreplacement,$elmsg );

            $elsub = preg_replace( $elfind,$lrreplacement,$elsub );
            $lrheaders = array( 'Content-Type: text/html; charset=UTF-8' );
            wp_mail( $user_email, $elsub, $clrmessage, $lrheaders );
            
        } else{ 
            $message = esc_html__( 'Someone has requested a password reset for the following account:','tpgbp' ) . "<br/><br/>";

            $message .= sprintf( esc_html__( 'Site Name: %s','tpgbp' ), $site_name ) . "<br/>";

            $message .= sprintf( esc_html__( 'Username: %s','tpgbp' ), $user_login ) . "<br/><br/>";
            $message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.','tpgbp' ) . "<br/>";
            $message .= esc_html__( 'To reset your password, visit the following address:','tpgbp' );
            
            if(!empty($forgotdata["linkOpt"]) && $forgotdata["linkOpt"]=='default'){
                $relink = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
                $message .= '<a style="color: #2271b1; font-size: 15px; line-height: 25px;" rel="noopener noreferrer" href="'.$relink.'">'.esc_html__( 'Click Here' , 'tpgbp' ).'</a>';
            }else if(!empty($forgotdata["linkOpt"]) && $forgotdata["linkOpt"]=='custom'){
                $fplInkData = [];
                $fplInkData['key'] = $key;
                $fplInkData['redirecturl'] = $forgotdata['reset_url'];
                $fplInkData['forgoturl'] = $forgotdata['forgot_url'];
                $fplInkData['login'] = rawurlencode( $user_login );
                
                $frontdata_key= Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($fplInkData), 'ey');

                $relink = network_site_url( "wp-login.php?action=tpgbreset&datakey=$frontdata_key", 'login' );
                $message .= '<a style="color: #2271b1; font-size: 15px; line-height: 25px;" rel="noopener noreferrer" href="'.$relink.'">'.esc_html__( 'Click Here' , 'tpgbp' ).'</a>';
            }
        }
       
        $title = sprintf( esc_html__( '[%s] Password Reset','tpgbp' ), $site_name );

        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        if(!empty( $ctmemailData) && (!empty( $ctmemailData['ctmEmail']) &&  $ctmemailData['ctmEmail']=='yes')){
            echo wp_json_encode( [ 'lost_pass'=>'confirm', 'message'=> esc_html__('Check your e-mail for the reset password link.','tpgbp') ] );
        }else{
            if ( wp_mail( $user_email, wp_specialchars_decode( $title ), $message , $headers ) ){
                echo wp_json_encode( [ 'lost_pass'=>'confirm', 'message'=> esc_html__('Check your e-mail for the reset password link.','tpgbp') ] );
            }else{
                echo wp_json_encode( [ 'lost_pass'=>'something_wrong', 'message'=> esc_html__('The e-mail could not be sent.','tpgbp') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.','tpgbp') ] );
            }
        }	

        exit;
    }

    /*
     * Set Custom For reset password Url 
     *  @since 2.0.0
     */
    public function tpgb_custom_password_reset() {
        
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            if(!empty($_GET['action']) && $_GET['action']=='tpgbreset'){
                $datakey = isset($_GET['datakey']) ? $_GET['datakey'] : '';
                $forgotdata = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($datakey);
                $forgotdata = json_decode(stripslashes($forgotdata),true);
                $user = check_password_reset_key( $forgotdata['key'], rawurldecode($forgotdata['login']) );
                $forgoturl = $forgotdata['forgoturl'];
                $redirecturl = $forgotdata['redirecturl'];
                $login = $forgotdata['login'];
                $key = $forgotdata['key'];
            }else{
                $forgoturl = isset($_GET['forgoturl']) ? wp_http_validate_url($_GET['forgoturl']) : '';
                $redirecturl ='';
                $login = isset($_GET['login']) ? $_GET['login'] : '';
                $key = isset($_GET['key']) ? $_GET['key'] : '';
                
                $user = check_password_reset_key( $key, $login );
            }
                
            if ( ! $user || is_wp_error( $user ) ) {
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    
                    $redirectUrl = $forgoturl;
                    $redirectUrl = add_query_arg( 'expired', 'expired', $redirectUrl );
                    wp_safe_redirect($redirectUrl);
                } else {
                    $redirectUrl = $forgoturl;
                    $redirectUrl = add_query_arg( 'invalid', 'invalid', $redirectUrl );
                    wp_safe_redirect($redirectUrl);
                }
                exit;
            }
            if(!empty($redirecturl)){	
                $data_res = [];
                $data_res['login'] =  $login;
                $data_res['forgoturl'] = $forgoturl;
                $data_res['key'] = $key;
                
                $data_reskey= Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($data_res), 'ey');
                
                $redirectUrl = $redirecturl;
                $redirectUrl = add_query_arg( 'action', 'tpgbreset', $redirectUrl );
                $redirectUrl = add_query_arg( 'datakey', $data_reskey, $redirectUrl );
                
                wp_safe_redirect($redirectUrl);
            }else{
                wp_safe_redirect(home_url());   
            }
            exit;
        }
    }

    /*
     * Set Custom For reset password
     *  @since 2.0.0
     */
    public function tpgb_ajax_reset_password(){

        $lostpassData = array();
	    parse_str($_POST['lostformData'], $lostpassData);

        $tpgbresetdata = isset($_POST['tpgbresetdata']) ? $_POST['tpgbresetdata'] : '';
        $resetdata = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($tpgbresetdata);
        $resetdata = json_decode(stripslashes($resetdata),true);
        $user_login = isset($resetdata['login']) ? $resetdata['login'] : '';	
        $user_login = urldecode($user_login);
        $user_key = isset($resetdata['key']) ? $resetdata['key'] : '';
        $nonce = isset($resetdata['noncesecure']) ? $resetdata['noncesecure'] : '';
        
        if ( ! wp_verify_nonce( $nonce, 'tpgb_reset_action' ) ){
            die ( 'Security checked!');
        }
        
        if( !$this->tpgb_check_login_activate() ){

            echo wp_json_encode( [ 'reset_pass'=>'expire', 'message'=> esc_html__('SomeThing Wants to Wrong','tpgbp') ] );
            exit();
        }

        if(isset($_POST['recaptchEn']) && $_POST['recaptchEn'] == 'yes' ){
            if( !isset($lostpassData['g-recaptcha-response']) && empty($lostpassData['g-recaptcha-response']) ){
                $message = sprintf(__( 'Please check the the captcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                $regresponse['recaptcha'] = ['registered' => false, 'message'=> $message];					
                exit;
            }else{

                if( isset( $lostpassData['g-recaptcha-response']) && !empty($lostpassData['g-recaptcha-response']) && !empty($reCaptchKey)  ){

                    $ip = $_SERVER['REMOTE_ADDR'];
                    
                    $url = 'https://www.google.com/recaptcha/api/siteverify';
                    $data = array('secret' => $reCaptchKey, 'response' => $lostpassData['g-recaptcha-response']);
                    
                    $options = array(
                        'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                        )
                    );
                    
                    
                    $recaptcha_secret = isset($data['secret']) ? $data['secret'] : '';
                    $recaptcha_respo = isset($data['response']) ? $data['response'] : '';					
                    $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $recaptcha_respo);
                    $responseKeys = json_decode($response["body"], true);
                    
                    $resscore=$responseKeys["score"];
                    $check_captcha = true;
                    if(!$responseKeys['success']){
                        $message = sprintf(__( 'Please check the the reCaptcha form.', 'tpgbp' ), get_bloginfo( 'name' ) );
                        $regresponse['recaptcha'] = ['reset_pass' => 'could_not_sent', 'message'=> $message ] ;
                        exit;
                    }
                }
            }
        }

        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $user = check_password_reset_key( $user_key, $user_login );
    
            if ( ! $user || is_wp_error( $user ) ) {
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                echo wp_json_encode( [ 'reset_pass'=>'expire', 'message'=> esc_html__('The entered key has expired. Please start reset process again.','tpgbp') ] );
                } else {
                    echo wp_json_encode( [ 'reset_pass'=>'invalid', 'message'=> esc_html__('The entered key is invalid. Please start reset process again.','tpgbp') ] );
                }
                exit;
            }
    
            if ( isset( $lostpassData['repassword'] ) ) {
                if ( $lostpassData['repassword'] != $lostpassData['reenpassword'] ) {                
                    echo wp_json_encode( [ 'reset_pass'=>'mismatch', 'message'=> esc_html__('Password does not match. Please try again.','tpgbp') ] );
                    exit;
                }
    
                if ( empty( $lostpassData['repassword'] ) ) {                
                    echo wp_json_encode( [ 'reset_pass'=>'empty', 'message'=> esc_html__('Password Field is Empty. Enter Password.
                    ','tpgbp') ] );                
                    exit;
                }
                
                reset_password( $user, $lostpassData['repassword'] );
                
            echo wp_json_encode( [ 'reset_pass'=>'success', 'message'=> esc_html__('Your password has been changed. Use your new password to sign in.','tpgbp') ] );
            
            } else {
                echo "Invalid request.";
            }
    
            exit;
        }
    }

    /*
     * Social Login With Facebook
     *  @since 2.0.0
     */
    public function tpgb_facebook_login(){
        if(!get_option('users_can_register')){
            echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Registration option not enbled in your general settings.', 'tpgbp' )] );
            die();
        }

        if( (!isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'tpgb-social-login' ) )  ){		
            echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            die();
        }
        
        if( !$this->tpgb_check_login_activate() ){
            echo wp_json_encode( ['registered'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
        }

        $access_token = (!empty( $_POST['accessToken'] )) ? sanitize_text_field( $_POST['accessToken'] ) : '';
        $user_id = (!empty( $_POST['id'] )) ? sanitize_text_field( $_POST['id'] ) : 0;
        $email	=	(isset($_POST['email'])) ? sanitize_email($_POST['email']) : '';
        $name	=	(isset($_POST['name'])) ? sanitize_user( $_POST['name'] ) : '';
        
    
        $fbappId = (!empty($_POST['appId'])) ? $_POST['appId'] : '';
        $fbsecretId = (!empty($_POST['secrId'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key( $_POST['secrId'] ) : '';
        $fbsecretId = str_replace('"', '', $fbsecretId);
        
        $verify_data = $this->tpgb_facebook_verify_data_user( $access_token, $fbappId, $fbsecretId );
        
        if ( empty( $user_id ) || ( $user_id !== $verify_data['data']['user_id'] ) || empty( $verify_data ) || empty( $fbappId ) || empty( $fbsecretId ) || ( $fbappId !== $verify_data['data']['app_id'] ) || ( ! $verify_data['data']['is_valid'] ) ) {
            echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Invalid Authorization', 'tpgbp')] );
            die();
        }
        
        $emailRes = $this->tpgb_facebook_get_user_email( $verify_data['data']['user_id'], $access_token );
        
        if ( !empty( $email ) && ( empty( $emailRes['email'] ) || $emailRes['email'] !== $email ) ) {
            echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Facebook email validation failed', 'tpgbp')] );
            die();
        }
    
        $verify_email = !empty( $email ) && !empty( $emailRes['email'] ) ? sanitize_email( $emailRes['email'] ) : $verify_data['user_id'] . '@facebook.com';
        
        $this->tpgb_login_social_app( $name, $verify_email, 'facebook' );
        
        die();
    }

    /*
     * Verify Facebook data
     *  @since 2.0.0
     */
    public function tpgb_facebook_verify_data_user( $fb_token, $fb_id, $fb_secret ) {
        $fb_api = 'https://graph.facebook.com/oauth/access_token';
        $fb_api = add_query_arg( [
            'client_id'     => $fb_id,
            'client_secret' => $fb_secret,
            'grant_type'    => 'client_credentials',
        ], $fb_api );
    
        $fb_res = wp_remote_get( $fb_api );
    
        if ( is_wp_error( $fb_res ) ) {
            wp_send_json_error();
        }
    
        $fb_response = json_decode( wp_remote_retrieve_body( $fb_res ), true );
    
        $app_token = $fb_response['access_token'];
    
        $debug_token = 'https://graph.facebook.com/debug_token';
        $debug_token = add_query_arg( [
            'input_token'  => $fb_token,
            'access_token' => $app_token,
        ], $debug_token );
    
        $response = wp_remote_get( $debug_token );
    
        if ( is_wp_error( $response ) ) {
            return false;
        }
    
        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /*
     * Get emailId From facebook
     *  @since 2.0.0
     */
    public function tpgb_facebook_get_user_email($user_id, $access_token ){
        $fburl = 'https://graph.facebook.com/' . $user_id;
        $fburl = add_query_arg( [
            'fields'       => 'email',
            'access_token' => $access_token,
        ], $fburl );
        
        $response = wp_remote_get( $fburl );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }

    /*
     * Social Login
     *  @since 2.0.0
     */
    public function tpgb_login_social_app($name, $email, $type = ''){
        $response	= [];
        $user_data	= get_user_by( 'email', $email ); 
        if ( ! empty( $user_data ) && $user_data !== false ) {
            $user_ID = $user_data->ID;
            wp_set_auth_cookie( $user_ID );
            wp_set_current_user( $user_ID, $name );
            do_action( 'wp_login', $user_data->user_login, $user_data );
            echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'tpgbp')] );
        } else {
            
            $password = wp_generate_password( 12, true, false );
            
            $args = [
                'user_login' => $name,
                'user_pass'  => $password,
                'user_email' => $email,
                'first_name' => $name,
            ];
            
            if ( username_exists( $name ) ) {
                $suffix_id = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
                $name  .= $suffix_id;

                $args['user_login'] = strtolower( preg_replace( '/\s+/', '', $name ) );
            }

            $result = wp_insert_user( $args );

            $user_data = get_user_by( 'email', $email );

            if ( $user_data ) {
                $user_ID    = $user_data->ID;
                $user_email = $user_data->user_email;

                $user_meta = array(
                    'login_source' => $type,
                );

                update_user_meta( $user_ID, 'tpgb_login_form', $user_meta );
                            
                if ( wp_check_password( $password, $user_data->user_pass, $user_data->ID ) ) {
                    wp_set_auth_cookie( $user_ID );
                    wp_set_current_user( $user_ID, $name );
                    do_action( 'wp_login', $user_data->user_login, $user_data );
                    echo wp_json_encode( ['loggedin' => true, 'message'=> esc_html__('Login successful, Redirecting...', 'tpgbp')] );
                }
            }
        }
        
        die();  
    }
    
    /*
	 *  Verify Google
     *  @since 2.0.0
     */
    public function tpgb_verify_google_data_user($token, $client_id){
        require_once TPGBP_INCLUDES_URL . 'vendor/autoload.php';

        $clientData = new \Google_Client( array( 'client_id' => $client_id ) );
        $verified = $clientData->verifyIdToken($token);

        if ( $verified ) {
            return $verified;
        } else {
            echo wp_json_encode( ['loggedin' => false, 'message'=> esc_html__('Unauthorized access', 'tpgbp')] );
            die();
        }
    }

    /*
	 *  Google Pic login
     *  @since 2.0.0
     */
    public function tpgb_google_pic(){
			
        // secure credential value from AJAX
        $credential = $guclientId = '';
        if(isset($_POST["googleCre"]) && !empty($_POST["googleCre"])){
            $credential = sanitize_text_field($_POST["googleCre"]);
        }else{
            echo wp_json_encode( ['login' => false, 'message'=> esc_html__('Unauthorized access', 'tpgbp')] );
            exit;
        }

        if( !$this->tpgb_check_login_activate() ){
            echo wp_json_encode( ['login'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
        }

        if(isset($_POST["clientId"]) && !empty($_POST["clientId"])){
            $guclientId  = sanitize_text_field($_POST["clientId"]);
        }else{
            echo wp_json_encode( ['login' => false, 'message'=> esc_html__('clientId Not Set', 'tpgbp')] );
            exit;
        }
        
        $verified = $this->tpgb_verify_google_data_user( $credential, $guclientId );
		
        if ( empty( $verified ) ) {
            echo wp_json_encode( ['login'=>false, 'message'=> esc_html__( 'User not verified by Google', 'tpgbp' )] );
            exit;
        }
		
		if( !empty( $verified ) && isset( $verified['aud'] ) && !empty($verified['aud']) && $verified['aud'] === $guclientId ){
			// verify the ID token
			$curl = curl_init( 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $credential );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			$response = curl_exec( $curl );
			curl_close( $curl );

			// convert the response from JSON string to object
			$response = json_decode($response);

			if (isset($response->error)) {
				echo wp_json_encode( ['login'=>false, 'message'=> $response->error_description] );
			}
			else{
				$this->tpgb_login_social_app( $response->name, $response->email , 'google' );
			}
			exit;
		}
    }
    
    /*
     *  Send Magic Link
     *  @since 2.0.0
     */
    public function tpgb_send_magic_link(){
        $magicData = array();
	    parse_str($_POST['magicData'], $magicData);

        if(isset($_POST['mailData']) && !empty($_POST['mailData']) ){
            $mainData = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($_POST['mailData']);
            $mainData = (array) json_decode(html_entity_decode($mainData));
        }
        if( ( !isset( $mainData['nonce'] ) || !wp_verify_nonce( $mainData['nonce'], 'tpgb-login-magic-link') ) ){
            echo wp_json_encode( ['magicMsg'=> false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
            exit;
        }

        if( !$this->tpgb_check_login_activate() ){
            echo wp_json_encode( ['login'=>false, 'message'=> esc_html__( 'Ooops, something went wrong, please try again later.', 'tpgbp' )] );
        }

        $userlogin = isset($magicData['magic-link']) ? wp_unslash($magicData['magic-link']) : '';

        if ( empty( $userlogin ) || !is_string( $userlogin ) ) {      
            echo wp_json_encode( [ 'magicMsg'=>'empty_username', 'message'=> sprintf(__( '<strong>ERROR</strong>: Enter a username or email address.','tpgbp' )) ] );
            exit;
        } elseif ( strpos( $userlogin, '@' ) ) {
            $user_data = get_user_by( 'email', trim( wp_unslash( $userlogin ) ) );
            if ( empty( $user_data ) ) {          
                echo wp_json_encode( [ 'magicMsg'=> false, 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','tpgbp' )) ] );
                exit;
            }
        } else {
            $login = trim( $userlogin );
            $user_data = get_user_by( 'login', $login );
            if ( !$user_data ) {			
                echo wp_json_encode( [ 'magicMsg'=>'invalidcombo', 'message'=> sprintf(__( '<strong>ERROR</strong>: There is no account with that username or email address.','tpgbp' )) ] );
                exit;
            }
        }

        
        if(!empty($user_data->data)){
            $userData = (array) $user_data->data;
            $user_login = $userData['user_login'];
            $user_email = $userData['user_email'];
        }
        $key = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            return $key;
        }
        if ( is_multisite() ) {
            $site_name = get_network()->site_name;
        } else {
            $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        }

        $elsub =  $mainData['mailsub'];
        $elmsg = $mainData['mailCnt'];
        
        $maglInkData = [];
        $maglInkData['key'] = $key;
        $maglInkData['redirecturl'] =  $mainData['redirectUrl'];
        $maglInkData['login'] = rawurlencode( $user_login );
    
        $frontdata_key= Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($maglInkData), 'ey');
        
        $tplr_link_get = add_query_arg( array(
            'action' => 'tpgbmagic',
            'datakey' => $frontdata_key
        ), $mainData['redirectUrl'] );
        
        $elfind = array( '/\[tpgb_sitename\]/', '/\[tpgb_username\]/', '/\[tpgb_link\]/' , '/\[tpgb_email\]/' );
        $maeplacement = array( $site_name,$user_login,$tplr_link_get , $user_email);		
        $magmessage = preg_replace( $elfind,$maeplacement,$elmsg );
        $magheaders = array( 'Content-Type: text/html; charset=UTF-8' );
        $elsub = preg_replace( $elfind,$maeplacement,$elsub );
        $mlinkSend = wp_mail( $user_email, $elsub, $magmessage, $magheaders );

        if(!empty($mlinkSend)){
            echo wp_json_encode( [ 'magicMsg'=> true , 'message'=> sprintf(__( '<strong> Magic Link Send... </strong>','tpgbp' )) ] );
        }else{
            echo wp_json_encode( [ 'magicMsg'=> false , 'message'=> sprintf(__( '<strong>ERROR</strong>: Ooops, something went wrong, please try again later. ','tpgbp' )) ] );
        }

        exit;
    }

    /*
     *  Parse Magic Link
     *  @since 2.0.0
     */
    public function tpgb_parse_magic_link(){
        $forgoturl = '';
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            if(!empty($_GET['action']) && $_GET['action']=='tpgbmagic'){
                $datakey = isset($_GET['datakey']) ? $_GET['datakey'] : '';
                $forgotdata = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($datakey);
                $forgotdata = json_decode(stripslashes($forgotdata),true);
                $user = check_password_reset_key( $forgotdata['key'], rawurldecode($forgotdata['login']) );
                $redirecturl = $forgotdata['redirecturl'];
                $login = $forgotdata['login'];
                $key = $forgotdata['key'];
            }else{
                $forgoturl = isset($_GET['redirecturl']) ? wp_http_validate_url($_GET['redirecturl']) : '';
                $login = isset($_GET['login']) ? $_GET['login'] : '';
                $key = isset($_GET['key']) ? $_GET['key'] : '';
                
                $user = check_password_reset_key( $key, $login );
            }

            if ( ! $user || is_wp_error( $user ) ) {
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    $redirectUrl = $forgoturl;
                    $redirectUrl = add_query_arg( 'expired', 'expired', $redirectUrl );
                    wp_safe_redirect($redirectUrl);
                } else {
                    $redirectUrl = $forgoturl;
                    $redirectUrl = add_query_arg( 'invalid', 'invalid', $redirectUrl );
                    wp_safe_redirect($redirectUrl);
                }
                
            }else{
                $user_ID = $user->ID;
                wp_set_current_user( $user, $user->name );
                wp_set_auth_cookie( $user_ID, true, true );

                wp_safe_redirect($redirecturl);
            }
        }
    }
    
}
Tp_Blocks_Ajax::get_instance();