<?php 
/**
 * ThePlus Pro Blocks Init
 *
 * Load of all the blocks.
 *
 * @since   1.0.0
 * @package TPGBP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class TPGBP_Pro_Init_Blocks {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	public $tpgb_delay_perf = null;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/**
	 * Constructor
	 * @since 1.2.3
	 */
	public function __construct() {
	
		add_action( 'enqueue_block_assets', array( $this, 'tp_block_assets' ) ); //Frontend load scripts and styles
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) ); //Gutenberg editor load scripts and styles
		add_action( 'admin_enqueue_scripts', array($this, 'tpgbp_admin_enqueue_scripts') );
		add_action('rest_api_init', array($this, 'register_api_hook'));
		add_filter( 'tpgb_get_dynamic_content', array( $this,'tpgb_get_dynamic_content') , 10 , 1 );
		add_filter( 'tpgb_onloader_scripts', array( $this,'tpgb_onloader_scripts_func') , 10 , 3 );
		add_filter( 'tpgb_load_blocks_css_js', array( $this,'tpgb_onloader_scripts_func') , 10 , 2 );
		add_action('wp_head', array($this, 'tpgb_dynamic_css_load'));
	}
	
	/*
	 * Dynamic Css Generate in wp_head Load
	 * @since 1.3.0
	 */
	public function tpgb_dynamic_css_load(){
		$current_Id = get_queried_object_id();
		$ids = [];
		$block_css = '';
		if(class_exists('Tp_Core_Init_Blocks')){
			
			$data = Tp_Core_Init_Blocks::get_instance();
			if(!empty($data) && isset($data->template_ids)){
				$ids = $data->template_ids;
				$ids[]= $current_Id;
			}
			if( !empty($ids) && class_exists('Tp_Generate_Blocks_Css') ){
				$generateClass = new Tp_Generate_Blocks_Css();
				foreach($ids as $post_id){
					$block_css .= $generateClass->generate_dynamic_css( $post_id, true );
				}
			}
		}
		if(!empty($block_css)){
			echo '<style type="text/css">'.$block_css.'</style>';
		}
	}
	
	/*
	 * Admin Enqueue Scripts
	 *
     * @since 1.0.0
	 */
	public function tpgbp_admin_enqueue_scripts(){
		wp_enqueue_script( 'tpgb-pro-admin', TPGBP_URL . 'assets/js/admin/tpgb-pro-admin.min.js',array() , TPGBP_VERSION, true );
	}
	
	/*
	 * Enqueue block styles for both frontend + backend.
	 *
     * @since 2.0.0
	 */
	public function tp_block_assets(){
		$fontawesome_load = Tp_Blocks_Helper::get_extra_option('fontawesome_load');
		$fontawesome_pro = Tp_Blocks_Helper::get_extra_option('fontawesome_pro_kit');
		if((empty($fontawesome_load) || $fontawesome_load=='enable') && !empty($fontawesome_pro)){
			if(is_admin() || !$this->get_delay_css_js()){
				wp_enqueue_script( 'tpgb-font-awesome-pro', 'https://kit.fontawesome.com/'.$fontawesome_pro.'.js', [], TPGBP_VERSION, true );
			}
		}
		
		if( has_block( 'tpgb/tp-audio-player' )){
			wp_enqueue_script( 'jquery-ui-slider' ); //TP Audio Player
		}

		if(has_block( 'tpgb/tp-login-register' )){
			wp_enqueue_script( 'password-strength-meter' ); //Login & Signup
		}
	}
	
	/*
	 * Get Delay Css Js Best Performance
	 * @since 2.0.0
	 */
	public function get_delay_css_js(){
		if($this->tpgb_delay_perf != null ){
			return $this->tpgb_delay_perf;
		}

		$delay_opt = get_option( 'tpgb_delay_css_js' );
		if( isset($delay_opt) && $delay_opt=='true' ){
			$this->tpgb_delay_perf = true;
			return true;
		}else{
			$this->tpgb_delay_perf = false;
			return false;
		}
	}

	/*
	 * On Loader Script Delay
     * @since 2.0.0
	 */
	public function tpgb_onloader_scripts_func( $scripts_loader, $elements= [], $plus_version = TPGBP_VERSION ){
		$loader = [];
		$fontawesome_load = Tp_Blocks_Helper::get_extra_option('fontawesome_load');
		$fontawesome_pro = Tp_Blocks_Helper::get_extra_option('fontawesome_pro_kit');
		if((empty($fontawesome_load) || $fontawesome_load=='enable') && !empty($fontawesome_pro) && $this->get_delay_css_js()){
			$loader['font_pro'] = $fontawesome_pro;
		}

		if( has_block( 'tpgb/tp-audio-player' ) || (!empty($elements) && in_array('tpgb/tp-audio-player',$elements)) ){
			wp_enqueue_script( 'jquery-ui-slider' ); //TP Audio Player
		}

		if( has_block( 'tpgb/tp-login-register' ) || (!empty($elements) && in_array('tpgb/tp-login-register',$elements)) ){
			wp_enqueue_script( 'password-strength-meter' ); //Login & Signup
		}

		if( has_block( 'tpgb/tp-coupon-code' ) || (!empty($elements) && in_array('tpgb/tp-coupon-code',$elements)) ){
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-touch-punch');
		}

		if( has_block( 'tpgb/tp-lottiefiles' ) || (!empty($elements) && in_array('tpgb/tp-lottiefiles',$elements)) ){
			if($this->get_delay_css_js()){
					$loader['lottie'] = TPGBP_URL . 'assets/js/extra/lottie.min.js';
					$loader['tpgbLottie'] = TPGBP_URL . 'assets/js/main/lottiefiles/tpgb-lottiefiles.min.js';
			}else{
					wp_enqueue_script('tpgb-lottie-extra', TPGBP_URL . 'assets/js/extra/lottie.min.js', ['jquery'], $plus_version, true);
					wp_enqueue_script('tpgb-lottie', TPGBP_URL . 'assets/js/main/lottiefiles/tpgb-lottiefiles.min.js', ['tpgb-lottie-extra'], $plus_version, true);
			}
		}
		return array_merge($scripts_loader, $loader);
	}
	
	/**
     * Enqueue block styles and scripts for Backend Editor.
     *
     * @since 1.0.0
     */
    public function editor_assets() {
	
		//Editor BLock build css
		wp_enqueue_style('tpgb-block-editor-css', TPGBP_URL.'assets/css/admin/tpgb-blocks-editor.min.css', array('wp-edit-blocks'),TPGBP_VERSION);
		
		//Editor Block build js
		global $pagenow;
		$scripts_dep = array( 'wp-blocks', 'wp-i18n','wp-plugins', 'wp-element','wp-components');
		if ( 'widgets.php' !== $pagenow && 'customize.php' !== $pagenow ) {
			$scripts_dep = array_merge($scripts_dep, array('wp-editor', 'wp-edit-post'));
			wp_enqueue_script('tpgb-block-editor-js', TPGBP_URL.'assets/js/admin/tpgb-blocks-editor.min.js', $scripts_dep,TPGBP_VERSION, false);
		}
    }
	
	public function register_api_hook(){
		//Get Dynamic content Api
		register_rest_route(
			'tpgb/v1',
			'/tpgb_get_data/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'tpgb_get_dynamic_content'),
					'permission_callback' => function () {
                        return true;
					},
					'args' => array()
				),
			)
		);
		
		//Api for twitter feed
		register_rest_route(
			'tpgb/v1',
			'/theplus_social_feed_twitter/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'tpgb_get_twitter_feed'),
					'permission_callback' => function () {
                        return true;
                    },
					'args' => array()
				),
			)
		);
		
		//Api for google review
		register_rest_route(
			'tpgb/v1',
			'/theplus_social_review_google/',
			array(
				array(
					'methods'  => 'POST',
					'callback' => array($this, 'tpgb_get_google_review'),
					'permission_callback' => function () {
                        return true;
                    },
					'args' => array()
				),
			)
		);
	}
	
	/**
	 * API call For Dynamic Content
	 * @since 1.3.0
	 */
	public static function tpgb_get_dynamic_content($params){
		if(!empty($params) && !empty($params['field'])){
			if(!empty($params['field']['dynamicField'])) {
				$output = '';
				$product = (class_exists('woocommerce')) ? wc_get_product($params['id']) : '';
				switch ( $params['field']['dynamicField'] ) {
					case 'post-title': 
						$output = get_the_title( $params['id']); 
					break;
					case 'post-slug': $output = get_post_field( 'post_name', $params['id'] ); break;
					case 'post-excerpt': $output = self::tpgb_render_post_excerpt( $params['id']); break;
					case 'post-date': 
						if(!empty($params['field']['dateFormat'])){
							$output = (string) date_format( date_create( get_post_field( 'post_date', $params['id'] ) ),$params['field']['dateFormat'] );
						}else{
							$output = get_post_field( 'post_date', $params['id'] );
						}
					break;
					case 'post-date-gmt': 
						if(!empty($params['field']['dateFormat'])){
							$output = (string) date_format( date_create( get_post_field( 'post_date_gmt', $params['id'] ) ),$params['field']['dateFormat'] );
						}else{
							$output = get_post_field( 'post_date_gmt', $params['id'] );
						}
					break;
					case 'post-modified': 
						if(!empty($params['field']['dateFormat'])){
							$output = (string) date_format( date_create( get_post_field( 'post_modified', $params['id'] ) ),$params['field']['dateFormat'] );
						}else{
							$output = get_post_field( 'post_modified', $params['id'] );
						}
					break;
					case 'post-modified-gmt': 
						if(!empty($params['field']['dateFormat'])){
							$output = (string) date_format( date_create( get_post_field( 'post_modified_gmt', $params['id'] ) ),$params['field']['dateFormat'] );
						}else{
							$output = get_post_field( 'post_modified_gmt', $params['id'] );
						}
					break;
					case 'post-type': $output = wp_kses_post(get_post_field( 'post_type', $params['id'] )); break;
					case 'post-status': $output = wp_kses_post(get_post_field( 'post_status', $params['id'] )); break;
					case 'author-name': 
						$author_id = get_post_field( 'post_author', $params['id'] );
						$output = wp_kses_post( get_the_author_meta( 'display_name' , $author_id ) );	
					break;
					case 'archive-title':
						$incContext = !empty($params['field']['incContext']) ? true : false ;
						$arTitle = self::tpgb_archive_title($incContext);
						if(!empty($arTitle)){
							$output = $arTitle;
						}else if(!empty($params['editor'])){
							$output = ($incContext ? 'Archive Type : Archive Title' : 'Archive Title');
						}
					break;
					case 'archive-description': 
						$arDesc = get_the_archive_description();
						if(!empty($arDesc)){
							$output = $arDesc;
						}else if(!empty($params['editor'])){
							$output = 'Archive Description';
						}
					break;
					case 'archive-url': $output = self::tpgb_render_archive_url(); break;
					case 'author-id': $output = wp_kses_post( get_post_field( 'post_author', $params['id'] ) ); break;
					case 'author-posts': 
						$author_id = get_post_field( 'post_author', $params['id'] );
						$output = wp_kses_post( count_user_posts( $author_id ) );
					break;
					case 'author-first-name': 
						$author_id = get_post_field( 'post_author', $params['id'] );
						$output = wp_kses_post( get_the_author_meta( 'first_name', $author_id ) );
					break;
					case 'author-last-name': 
						$author_id = get_post_field( 'post_author', $params['id'] );
						$output = wp_kses_post( get_the_author_meta( 'last_name', $author_id ) );
					break;
					case 'comment-number': 
						$comments_number = get_comments_number( $params['id'] );
						$output = wp_kses_post( number_format_i18n( $comments_number ) );
					break;
					case 'comment-status': $output = wp_kses_post( get_post_field( 'comment_status', $params['id'])); break;
					case 'post-url': $output = get_permalink( $params['id'] ); break;
					case 'site-title': $output = wp_kses_post( get_bloginfo() ); break;
					case 'site-tagline': $output = wp_kses_post( get_bloginfo( 'description' ) ); break;
					case 'tpgb-product-title-tag':
						if ( ! $product ) {
							$output = '';
						}else{
							$output = wp_kses_post( $product->get_title() );
						}
					break;
					case 'tpgb-product-terms-tag':   
						if ( ! $product ) {
							$output = '';
						}else{
							$proCat = !empty($params['field']['proTerm']) ? $params['field']['proTerm'] : '';
							$catSep = !empty($params['field']['termSep']) ? $params['field']['termSep'] : '';
							$value = get_the_term_list( $product->get_id(), $proCat, '', $catSep );

							$output = wp_kses_post( $value );
						}
					break;
					case 'tpgb-product-price-tag':   
						if ( ! $product ) {
							$output = '';
						}else{
							$format = !empty($params['field']['priceFor']) ? $params['field']['priceFor'] : 'both';
							switch ( $format ) {
								case 'both':
									$output = $product->get_price_html();
								break;
								case 'original':
									$output = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
								break;
								case 'sale' && $product->is_on_sale():
									$output = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
								break;
							}
						}
						
					break;
					case 'tpgb-product-rating-tag':
						if ( ! $product ) {
							$output = '';
						}
						else{
							if(!empty($params['field']['ratFormat'])){
								switch ( $params['field']['ratFormat'] ) {
									case 'average_rating':
										$output = $product->get_average_rating();
									break;
									case 'rating_count':
										$output = $product->get_rating_count();
									break;
									case 'review_count':
										$output = $product->get_review_count();
									break;
								}
							}
						}
							
					break;
					case 'tpgb-product-sale-tag':   
						if ( ! $product ) {
							$output = '';
						}else{
							if ( $product->is_on_sale() ) {
								$output = "Sale!" ;
							}
						}
						
					break;
					case 'tpgb-product-short-description-tag':   
						if ( ! $product ) {
							$output = '';
						}else{
							$output = wp_kses_post( $product->get_short_description() );
						}
						
					break;
					case 'tpgb-product-sku-tag': 
						$value = '';  
						if ( ! $product ) {
							$output = '';
						}else{
							if ( $product->get_sku() ) {
								$value = $product->get_sku();
							}
						}
						$output = esc_html( $value );
					break;
					case 'tpgb-product-stock-tag':   
						if ( ! $product ) {
							$output = '';
						}else{
							$stText = !empty($params['field']['stText']) ? true : false ;
							if ( $stText) {
								$output = wc_get_stock_html( $product );
							} else {
								$output = (int) $product->get_stock_quantity();
							}
						}
							
					break;
					default:
						
						$gatVal = '';
						if(is_archive() || (!empty($params['prev_type']) && $params['prev_type'] == 'archives') ){
							$gatVal = get_term_meta( get_queried_object_id(), $params['field']['dynamicField'], true);
							if(empty($gatVal)){
								$gatVal = get_post_meta( get_the_ID(), $params['field']['dynamicField'], true);
							}
						}else if(is_singular() || (!empty($params['prev_type']) && $params['prev_type'] == 'singular')){
							$gatVal = get_post_meta( $params['id'],$params['field']['dynamicField'],true);
						}else{
							$gatVal = get_post_meta( get_the_ID(),$params['field']['dynamicField'],true);
						}
						
						if( empty($gatVal) && strpos($params['field']['dynamicField'], ':' ) !== false && class_exists('PodsInit') ){
							if ( empty($params['field']['dynamicField'] ) ) {
								return false;
							}
							list( $pod_name, $pod_id, $meta_key ) = explode( ':', $params['field']['dynamicField'] );

							$pod = (!empty($pod_name)) ? pods( $pod_name, $params['id'] ) : '';

							if ( false === $pod ) {
								return [];
							}
							if(!empty($pod->field( $meta_key ))){
								$output = $pod->field( $meta_key );
							}else{
								$output = '';
							}
						}else{
							if(is_array($gatVal)){
								foreach($gatVal as $val){
									if(!empty($val[0])){
										$output = $val[0];
									}else{
										$output = '';
									}
								}
							}else{
								$dateformat = DateTime::createFromFormat('Ymd', $gatVal);
								if($dateformat == true && function_exists('get_field')){
									$output = get_field($params['field']['dynamicField']);
								}else{
									$output = $gatVal;
								}
							}
						}
						
					break;
				}
				
				// Set Fallback Text
				if( empty($output) ){
					if( isset($params['field']['advsetting']) && $params['field']['advsetting'] == true){
						if(isset($params['field']['fallBacktxt']) && !empty($params['field']['fallBacktxt']) ){
							$output = $params['field']['fallBacktxt'];
						}else{
							$output = '';
						}
					}
				}

				$finaloutput = '';
				if(!empty($params['field']['titlePre'])){
					$finaloutput .= $params['field']['titlePre'];
				}
				$finaloutput .= $output;
				if(!empty($params['field']['titlePost'])){
					$finaloutput .= $params['field']['titlePost'];
				}
				
				return $finaloutput;
			}
			if(!empty($params['field']['dynamicUrl'])){
				if( (!empty($params['type']) && $params['type'] == 'url') || (!empty($params['field']['type']) && $params['field']['type'] == 'url') ){
					$outputUrl = '';
					switch ( $params['field']['dynamicUrl'] ) {
						case 'post-url': $outputUrl = esc_url( get_permalink( $params['id'])); break;
						case 'site-url': $outputUrl = esc_url( get_bloginfo( 'url' ) ); break;
						case 'author-post-url': 
							$author_id = get_post_field( 'post_author', $params['id'] );
							$outputUrl = get_author_posts_url( $author_id );
						break;
						case 'author-profile-url':  
							$author_id = get_post_field( 'post_author', $params['id'] );
							$outputUrl = get_avatar_url( $author_id );
						break;
						default :
							$acfUrl='';
							if(is_archive() || (!empty($params['prev_type']) && $params['prev_type'] == 'archives') ){
								$acfUrl = get_term_meta( get_queried_object_id(), $params['field']['dynamicUrl'], true);
								if(empty($acfUrl)){
									$acfUrl = get_post_meta( get_the_ID(), $params['field']['dynamicUrl'], true);
								}
							}else if(is_singular() || (!empty($params['prev_type']) && $params['prev_type'] == 'singular')){
								$acfUrl = get_post_field( $params['field']['dynamicUrl'], $params['id'] );
							}else{
								$acfUrl = get_post_meta( get_the_ID(), $params['field']['dynamicUrl'], true);
							}
							
							if( empty($acfUrl) && strpos($params['field']['dynamicUrl'], ':' ) !== false && class_exists('PodsInit') ){

								list( $pod_name, $pod_id, $meta_key ) = explode( ':', $params['field']['dynamicUrl'] );
								$pod = (!empty($pod_name)) ? pods( $pod_name, $params['id'] ) : '';
								
								if ( false === $pod ) {
									return '';
								}
								
								if(!empty($pod->field( $meta_key ))){
									$field = $pod->fields[ $meta_key ];
									$value = $pod->field( $meta_key );
									
									if ( $field && ! empty( $field['type'] ) && !empty($value) ) {

										switch ( $field['type'] ) {
											case 'phone':
												$outputUrl = (!empty($params['editor'])) ? 'https://' . $value : 'tel:' . $value;
												break;
											case 'file':
												$outputUrl = empty( $value['guid'] ) ? '' : $value['guid'];
												break;
											case 'email':
												$outputUrl = (!empty($params['editor'])) ? 'https://' . $value : 'mailto:' . $value;
												break;
											case 'website' :
												$outputUrl = $value;
												break;
										}
									}else if(empty($value) && !empty($params['editor'])){
										$outputUrl = '';
									}else if(empty($value)){
										$outputUrl = '';
									}
								}else if(!empty($params['editor'])){
									$outputUrl = '';
								}else {
									$outputUrl = '';
								}
							}else if( !empty($acfUrl) ){
								$outputUrl = $acfUrl;
							}else if( empty($acfUrl) && !empty($params['editor']) ){
								$outputUrl = '';
							}else if( empty($acfUrl) ){
								$outputUrl = '';
							}
							
						break;
					}

					// Set Fallback Url
					if(empty($outputUrl)){
						if( isset($params['field']['advsetting']) && $params['field']['advsetting'] == true ){
							if(isset($params['field']['fallbackUrl']) && !empty($params['field']['fallbackUrl']) ){
								$outputUrl = $params['field']['fallbackUrl'];
							}else{
								$outputUrl = '#';
							}
						}
					}

					return $outputUrl;
				}
				if( (!empty($params['type']) && $params['type'] == 'image') || (!empty($params['field']['type']) && $params['field']['type'] == 'image') ) {
				
					$outputImg = [];
					$imgSize = !empty($params['field']['imgSize']) ? $params['field']['imgSize'] : 'full';
					switch ( $params['field']['dynamicUrl'] ) {
						case 'post-featured-image':
							$outputImg['id'] = get_post_meta( $params['id'], '_thumbnail_id', true );
							$outputImg['url'] = get_the_post_thumbnail_url( $params['id'], $imgSize );
						break;
						case 'site-logo':
							$custom_logo_id = get_theme_mod( 'custom_logo' );
							$outputImg['id'] = $custom_logo_id;
							if ( $custom_logo_id ) {
								$outputImg['url'] = wp_get_attachment_image_src( $custom_logo_id, 'full' )[0];
							}
						break;
						case 'author-profile-picture': 
							$outputImg['id'] = '';
							$outputImg['url'] = get_avatar_url( (int) get_the_author_meta( 'ID' ) );
						break;
						case 'user-profile-picture':
							$outputImg['id'] = '';
							$outputImg['url'] = get_avatar_url( get_current_user_id() );
						break;
						default :
							
							$imgAcf='';
							if(is_archive() || (!empty($params['prev_type']) && $params['prev_type'] == 'archives') ){
								$imgAcf = get_term_meta( get_queried_object_id(), $params['field']['dynamicUrl'], true);
								if(empty($imgAcf)){
									$imgAcf = get_post_meta( get_the_ID(), $params['field']['dynamicUrl'], true);
								}
							}else if(is_singular() || (!empty($params['prev_type']) && $params['prev_type'] == 'singular')){
								$imgAcf = get_post_field( $params['field']['dynamicUrl'], $params['id']);
							}else{
								$imgAcf = get_post_meta( get_the_ID(), $params['field']['dynamicUrl'], true);
							}

							if(empty($imgAcf) && strpos($params['field']['dynamicUrl'], ':' ) !== false && class_exists('PodsInit') ){
								list( $pod_name, $pod_id, $meta_key ) = explode( ':', $params['field']['dynamicUrl'] );
								$pod = (!empty($pod_name)) ? pods( $pod_name, $params['id'] ) : '';
								
								if ( false === $pod ) {
									return [];
								}
								if(!empty($pod->field( $meta_key ))){
									$image = $pod->field( $meta_key  );
									$outputImg = [
										'id' =>  $image['ID'],
										'url' =>  $image['guid']
									];
								}else{
									$outputImg = [
										'id' => '',
										'url' => ''
									];
								}
							}else if( !empty($imgAcf) && is_array($imgAcf)){
								
								foreach($imgAcf as $acfGall){
									$gaUrl =  wp_get_attachment_image_src( (int) $acfGall , $imgSize );
									$outputImg[] = [
										'id' => $acfGall,
										'url' => $gaUrl[0]
									];
								}
							}else{
								if(!empty($imgAcf)){
									$outputImg['id'] = (int) $imgAcf; 
									$acUrl = wp_get_attachment_image_src( (int) $imgAcf , $imgSize );
									$attchUrl = wp_get_attachment_url((int)$imgAcf);

									if(!empty($acUrl)){
										$outputImg['url'] =  $acUrl[0];
									}else if(!empty($attchUrl)){
										$outputImg['url'] =  $attchUrl;
									}else{
										$outputImg['url'] = '';
									}
								}else{
									$outputImg = [
										'id' => '',
										'url' => ''
									];
								}
							}
						break;
					}

					// Set Fallback Image Url
					if( empty($outputImg) || (isset($outputImg['url']) && empty($outputImg['url'])) ){
						if( isset($params['field']['advsetting']) && $params['field']['advsetting'] == true ){
							if(isset($params['field']['fallbackImg']) && !empty($params['field']['fallbackImg']) && !empty($params['field']['fallbackImg']['url']) ){
								$outputImg = [
									'id' => '',
									'url' => $params['field']['fallbackImg']['url'],
								];
							}else{
								$outputImg = [
									'id' => '',
									'url' => TPGBP_URL.'assets/images/tpdb-placeholder.jpg'
								];
							}
						}else{
							$outputImg = [
								'id' => '',
								'url' => TPGBP_URL.'assets/images/tpdb-placeholder.jpg'
							];
						}
					}

					return $outputImg;
				}
			}
		}
	}
	
	/**
	 * API call Get ThePlus Social Feed Twitter
	 * @since 1.3.0
	 */
	public function tpgb_get_twitter_feed($request){
		$params = $request->get_params();
		 require_once(TPGBP_INCLUDES_URL.'social-feed/TwitterAPIExchange.php');
		try {
			$requestMethod = 'GET';		
			$nTwitter = new TwitterAPIExchange($params['keyPara']);
			$TwResponse = $nTwitter->setGetfield($params['getfield'])->buildOauth( $params['Url'], $requestMethod )->performRequest();
			$TwResponce = json_decode($TwResponse,true);
			return ['success' => true, 'settings' => $TwResponce];
		} catch (Exception $e) {
			return ['success' => false, 'message' => $e->getMessage()];
		} 
	}
	
	/**
	 * API call Get ThePlus Social Review Google
	 * @since 1.3.0
	 */
	public function tpgb_get_google_review($request){
		$params = $request->get_params();
		$Final=[];

		$URL = wp_remote_get($params['Url']);
		$StatusCode = wp_remote_retrieve_response_code($URL);
		$GetDataOne = wp_remote_retrieve_body($URL);
		$Statuscode = array( "HTTP_CODE" => $StatusCode );

		$Response = json_decode($GetDataOne, true);
		if( is_array($Statuscode) && is_array($Response) ){
			$Final = array_merge($Statuscode, $Response);
		}
		return $Final;
	}
	
	/**
	 * Get Post Excert
	 * @since 1.3.0
	 */
	public static function tpgb_render_post_excerpt($arg , $post = null){
		$excerpt = get_post_field( 'post_excerpt', $arg, 'display' );

		return empty( $excerpt ) ? "" : $excerpt;
	}
	
	/**
	 * Get Archive Url
	 * @since 1.3.0
	 */
	public static function tpgb_render_archive_url() {
        $url = '';
        if ( is_category() || is_tag() || is_tax() ) {
            $url = get_term_link( get_queried_object() );
        } elseif ( is_year() ) {
            $url = get_year_link( get_query_var( 'year' ) );
        } elseif ( is_month() ) {
            $url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
        } elseif ( is_day() ) {
            $url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
        } elseif ( is_author() ) {
            $url = get_author_posts_url( get_queried_object_id() );
        } elseif ( is_post_type_archive() ) {
            $url = get_post_type_archive_link( get_post_type() );
        }else{
            $url = '';
        }
		
        return $url;
    }
	
	/**
	 * Get Archive Title
	 * @since 1.3.0
	 */
	public static function tpgb_archive_title($include_context = true){
        $title = '';
        $obj = get_queried_object();
        if ( is_singular() ) {
            $title = get_the_title();
            if ( $include_context ) {
                $post_type_obj = get_post_type_object( get_post_type() );
                $title = sprintf( '%s: %s', $post_type_obj->labels->singular_name, $title );
            }
        } elseif ( is_category() ) {
            $title = single_cat_title( '', false );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Category: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Tag: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_search() ) {
            $title = sprintf( esc_html__( 'Search Results for: %s', 'tpgbp' ), get_search_query() );
            if ( get_query_var( 'paged' ) ) {
                $title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'tpgbp' ), get_query_var( 'paged' ) );
            }
        } elseif ( is_year() ) {
            $title = get_the_date( _x( 'Y', 'yearly archives date format', 'tpgbp' ) );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Year: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_month() ) {
            $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'tpgbp' ) );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Month: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_day() ) {
            $title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'tpgbp' ) );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Day: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>';
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Author: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_tax( 'post_format' ) ) {
            if ( is_tax( 'post_format', 'post-format-aside' ) ) {
                $title = _x( 'Asides', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
                $title = _x( 'Galleries', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
                $title = _x( 'Statuses', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
                $title = _x( 'Audio', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
                $title = _x( 'Chats', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
                $title = _x( 'Images', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
                $title = _x( 'Videos', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
                $title = _x( 'Quotes', 'post format archive title', 'tpgbp' );
            } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
                $title = _x( 'Links', 'post format archive title', 'tpgbp' );
            }
        } elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
            if ( $include_context ) {
                $title = sprintf( esc_html__( 'Archives: %s', 'tpgbp' ), $title );
            }
        } elseif ( is_tax() ) {
            $title = single_term_title( '', false );
            if ( $include_context ) {
                $tax = get_taxonomy( get_queried_object()->taxonomy );
                $title = sprintf( esc_html__( '%1$s: %2$s', 'tpgbp' ), $tax->labels->singular_name, $title );
            }
        } elseif ( is_archive() ) {
            $title = esc_html__( 'Archives', 'tpgbp' );
        } elseif ( is_404() ) {
            $title = esc_html__( 'Page Not Found', 'tpgbp' );
        }else{
            if ( $include_context ) {
                $post_type_obj = get_post_type_object( get_post_type() );
                $arcType = !empty($post_type_obj->labels->singular_name) ? $post_type_obj->labels->singular_name : 'Archives Type';
                $title = sprintf( '%s: %s', $arcType, 'Archives Title' );
            }
        }
        return $title;
    }
}

TPGBP_Pro_Init_Blocks::get_instance();
?>