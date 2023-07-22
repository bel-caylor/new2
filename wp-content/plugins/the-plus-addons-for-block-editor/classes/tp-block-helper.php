<?php
/**
 * TPGB Core Plugin.
 *
 * @package TPGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tp_Blocks_Helper.
 *
 * @package TPGB
 */
class Tp_Blocks_Helper {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	protected static $get_load_block;
	
	protected static $get_block_deactivate =[];
	
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
	 */
	public function __construct() {
		add_action('plugins_loaded', array($this, 'init_blocks_load'));
		add_action('wp_head', array($this,'custom_css_js_load'));
		add_filter('upload_mimes', array($this,'tpgb_mime_types') );
		if(is_admin()){
			add_action( 'wp_ajax_tpgb_cross_cp_import', array( $this, 'cross_copy_paste_media_import' ) );
		}

		/*Get Social Reviews Api Token*/
		add_action('wp_ajax_tpgb_f_socialreview_Gettoken', array($this, 'tpgb_f_socialreview_Gettoken'));
		add_action('wp_ajax_nopriv_tpgb_f_socialreview_Gettoken', array($this, 'tpgb_f_socialreview_Gettoken'));

		/*Remove Cache Transient*/
		if(is_admin()){
			add_action('wp_ajax_Tp_f_delete_transient', array($this, 'Tp_f_delete_transient'));
			add_action('wp_ajax_nopriv_Tp_f_delete_transient', array($this, 'Tp_f_delete_transient'));
		}
	}
	
	/* Load Custom Css and Js
	 * @since 1.0.0
	 */
	public function custom_css_js_load(){
		$get_custom_css_js=get_option( 'tpgb_custom_css_js' );
	
		$load_css_js='';
		//Load Custom Style
		if(!empty($get_custom_css_js['tpgb_custom_css_editor'])){
			$get_css=$get_custom_css_js['tpgb_custom_css_editor'];
			
			// Remove comments
			$get_css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $get_css);
			// Remove space after colons
			$get_css = str_replace(': ', ':', $get_css);
			// Remove whitespace
			$get_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $get_css);
			//Remove Last Semi colons
			$get_css = preg_replace('/;}/', '}', $get_css);
			
			$load_css_js .='<style type="text/css">';
			$load_css_js .= $get_css;
			$load_css_js .='</style>';
		}
		
		//Load Custom Script
		if(!empty($get_custom_css_js['tpgb_custom_js_editor'])){
			$get_js= $get_custom_css_js['tpgb_custom_js_editor'];
			$load_css_js .= wp_print_inline_script_tag($get_js);
		}
		echo $load_css_js;
	}
	
	/*
	 * SVG Upload Mime types
	 * @since 1.0.0
	 */
	public function tpgb_mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	
	public static function get_extra_option($field){
		$options=get_option( 'tpgb_connection_data' );	
			$values='';
			if(isset($options[$field]) && !empty($options[$field])){
				$values=$options[$field];
			}	
		return $values;
	}
	
	/**
	 * Init Block Load.
	 *
	 * @since 1.0.0
	 */
	public function init_blocks_load() {
		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		include_once 'global-options/tp-global-options.php';
		
		$load_blocks = array(
			'tp-accordion' => TPGB_CATEGORY.'/tp-accordion',
			'tp-blockquote' => TPGB_CATEGORY.'/tp-blockquote',
			'tp-breadcrumbs' => TPGB_CATEGORY.'/tp-breadcrumbs',
			'tp-button' => TPGB_CATEGORY.'/tp-button',
			'tp-code-highlighter' => TPGB_CATEGORY.'/tp-code-highlighter',
			'tp-countdown' => TPGB_CATEGORY.'/tp-countdown',
			'tp-container' => TPGB_CATEGORY.'/tp-container',
			'tp-creative-image' => TPGB_CATEGORY.'/tp-creative-image',
			'tp-data-table' => TPGB_CATEGORY.'/tp-data-table',
			'tp-draw-svg' => TPGB_CATEGORY.'/tp-draw-svg',
			'tp-dark-mode' => TPGB_CATEGORY.'/tp-dark-mode',
			'tp-empty-space' => TPGB_CATEGORY.'/tp-empty-space',
			'tp-external-form-styler' => TPGB_CATEGORY.'/tp-external-form-styler',
			'tp-flipbox' => TPGB_CATEGORY.'/tp-flipbox',
			'tp-google-map' => TPGB_CATEGORY.'/tp-google-map',
			'tp-heading-title' => TPGB_CATEGORY.'/tp-heading-title',
			'tp-hovercard' => TPGB_CATEGORY.'/tp-hovercard',
			'tp-infobox' => TPGB_CATEGORY.'/tp-infobox',
			'tp-interactive-circle-info' => TPGB_CATEGORY.'/tp-interactive-circle-info',
			'tp-messagebox' => TPGB_CATEGORY.'/tp-messagebox',
			'tp-number-counter' => TPGB_CATEGORY.'/tp-number-counter',
			'tp-post-author' => TPGB_CATEGORY.'/tp-post-author',
			'tp-post-comment' => TPGB_CATEGORY.'/tp-post-comment',
			'tp-post-content' => TPGB_CATEGORY.'/tp-post-content',
			'tp-post-image' => TPGB_CATEGORY.'/tp-post-image',
			'tp-post-listing' => TPGB_CATEGORY.'/tp-post-listing',
			'tp-post-meta' => TPGB_CATEGORY.'/tp-post-meta',
			'tp-post-title' => TPGB_CATEGORY.'/tp-post-title',
			'tp-pricing-list' => TPGB_CATEGORY.'/tp-pricing-list',
			'tp-pricing-table' => TPGB_CATEGORY.'/tp-pricing-table',
			'tp-pro-paragraph' => TPGB_CATEGORY.'/tp-pro-paragraph',
			'tp-progress-bar' => TPGB_CATEGORY.'/tp-progress-bar',
			'tp-progress-tracker' => TPGB_CATEGORY.'/tp-progress-tracker',
			'tp-row' => TPGB_CATEGORY.'/tp-row',
			'tp-search-bar' => TPGB_CATEGORY.'/tp-search-bar',
			'tp-site-logo' => TPGB_CATEGORY.'/tp-site-logo',
			'tp-stylist-list' => TPGB_CATEGORY.'/tp-stylist-list',
			'tp-social-icons' => TPGB_CATEGORY.'/tp-social-icons',
			'tp-social-feed' => TPGB_CATEGORY.'/tp-social-feed',
			'tp-social-reviews' => TPGB_CATEGORY.'/tp-social-reviews',
			'tpgb-settings' => TPGB_CATEGORY.'/tpgb-settings',
			'tp-smooth-scroll' => TPGB_CATEGORY.'/tp-smooth-scroll',
			'tp-social-embed' => TPGB_CATEGORY.'/tp-social-embed',
			'tp-tabs-tours' => TPGB_CATEGORY.'/tp-tabs-tours',
			'tp-testimonials' => TPGB_CATEGORY.'/tp-testimonials',
			'tp-video' => TPGB_CATEGORY.'/tp-video',
		);
		
		if(has_filter('tpgb_load_blocks')) {
			$load_blocks = apply_filters('tpgb_load_blocks', $load_blocks);
		}
		
		$enable_normal_blocks = $this->tpgb_get_option('tpgb_normal_blocks_opts','enable_normal_blocks');
		
			if(!empty($enable_normal_blocks)){
				self::$get_load_block = $enable_normal_blocks;
				self::$get_load_block[] = 'tpgb-settings';
				$this->include_block( 'tpgb-settings' );
				
				foreach ( $load_blocks as $block_id => $block ) {
					if(in_array($block_id,$enable_normal_blocks)){
						$this->include_block( $block_id );
						if(!empty($block_id) && $block_id=='tp-row'){
							self::$get_load_block[] = 'tp-column';
							$this->include_block( 'tp-column' );
						}
						if(!empty($block_id) && $block_id=='tp-container'){
							self::$get_load_block[] = 'tp-container-inner';
							$this->include_block( 'tp-container-inner' );
						}
						if(!empty($block_id) && $block_id=='tp-accordion'){
							self::$get_load_block[] = 'tp-accordion-inner';
							$this->include_block( 'tp-accordion-inner' );	
						}
						if(!empty($block_id) && $block_id=='tp-tabs-tours'){
							self::$get_load_block[] = 'tp-tab-item';
							$this->include_block( 'tp-tab-item' );	
						}
						if ( defined('TPGBP_VERSION') ) {
							if(!empty($block_id) && $block_id=='tp-switcher'){
								self::$get_load_block[] = 'tp-switch-inner';
								$this->include_block( 'tp-switch-inner' );	
							}
							if(!empty($block_id) && $block_id=='tp-timeline'){
								self::$get_load_block[] = 'tp-timeline-inner';
								$this->include_block( 'tp-timeline-inner' );	
							}
						}
					}
				}
				
				$deactivate_block =array();
				foreach ( $load_blocks as $block_id => $block ) {
					if(!in_array($block_id,$enable_normal_blocks) && $block_id!='tpgb-settings'){
						$deactivate_block[] = $block_id;
					}
				}
				if(!in_array('tp-row',$enable_normal_blocks)){
					$deactivate_block[] = 'tp-column';
				}
				if(!in_array('tp-container',$enable_normal_blocks)){
					$deactivate_block[] = 'tp-container-inner';
				}
				if(!in_array('tp-accordion',$enable_normal_blocks)){
					$deactivate_block[] = 'tp-accordion-inner';
				}
				if(!in_array('tp-tabs-tours',$enable_normal_blocks)){
					$deactivate_block[] = 'tp-tab-item';
				}
				if ( defined('TPGBP_VERSION') ) {
					if(!in_array('tp-switcher',$enable_normal_blocks)){
						$deactivate_block[] = 'tp-switch-inner';
					}
					if(!in_array('tp-timeline',$enable_normal_blocks)){
						$deactivate_block[] = 'tp-timeline-inner';
					}
				}
				self::$get_block_deactivate = $deactivate_block;
			}else{
				foreach ( $load_blocks as $block_id => $block ) {
					self::$get_load_block[] = $block_id;
					$this->include_block( $block_id );
					if(!empty($block_id) && $block_id=='tp-row'){
						self::$get_load_block[] = 'tp-column';
						$this->include_block( 'tp-column' );
					}
					if(!empty($block_id) && $block_id=='tp-container'){
						self::$get_load_block[] = 'tp-container-inner';
						$this->include_block( 'tp-container-inner' );
					}
					if(!empty($block_id) && $block_id=='tp-accordion'){
						self::$get_load_block[] = 'tp-accordion-inner';
						$this->include_block( 'tp-accordion-inner' );	
					}
					if(!empty($block_id) && $block_id=='tp-tabs-tours'){
						self::$get_load_block[] = 'tp-tab-item';
						$this->include_block( 'tp-tab-item' );	
					}
					if ( defined('TPGBP_VERSION') ) {
						if(!empty($block_id) && $block_id=='tp-switcher'){
							self::$get_load_block[] = 'tp-switch-inner';
							$this->include_block( 'tp-switch-inner' );	
						}
						if(!empty($block_id) && $block_id=='tp-timeline'){
							self::$get_load_block[] = 'tp-timeline-inner';
							$this->include_block( 'tp-timeline-inner' );	
						}
					}
				}
			}
	}
	
	/**
	 * Load Block Include Required File
	 * @since 1.0.0
	 */
	public function include_block($block_id){
		$filename = sprintf('classes/blocks/'.esc_attr($block_id).'/index.php');
		
		$block_path = TPGB_PATH;
		if (defined('TPGBP_VERSION') && defined('TPGBP_PATH')) {
			$block_path = TPGBP_PATH;
		}
		
		if ( file_exists( $block_path.$filename ) ) {
			require $block_path.$filename;
			return true;
		}else if( file_exists( TPGB_PATH.$filename ) ){
			require TPGB_PATH.$filename;
			return true;
		}else{
			return false;
		}
		
	}
	
	/*
	 * Get load activate Block for tpgb
	 *	@Array
	 */
	public static function get_block_enabled(){
		$load_enable_block = self::$get_load_block;
		
		if(!empty($load_enable_block)){
			return $load_enable_block;
		}else{
			return;
		}
	}
	
	/*
	 * Get load deactivate Block for tpgb
	 *	@Array
	 */
	public static function get_block_deactivate(){
		$load_disable_block = self::$get_block_deactivate;
		$get_default_blocks = get_option( 'tpgb_default_load_blocks' );
		if(!empty($get_default_blocks) && !empty($get_default_blocks['disable_default_blocks']) ){
				$load_disable_block = array_merge($load_disable_block,$get_default_blocks['disable_default_blocks']);
		}
		if(!empty($load_disable_block)){
			return $load_disable_block;
		}else{
			return;
		}
	}
	
	public static function get_post_type_list(){
		$args = array(
			'public'   => true,
			'show_ui' => true
		);	 
		$post_types = get_post_types( $args, 'objects' );
		$options = array();
		foreach ( $post_types  as $post_type ) {
			$exclude = array( 'attachment', 'elementor_library' , 'e-landing-page' , 'nxt_builder' );
			if( TRUE === in_array( $post_type->name, $exclude ) )
			  continue;
		  
			$options[] = [$post_type->name,$post_type->label]; 
		}
		
		return $options;
	}
	
	/**
	 * Get Image size information for all currently-registered image sizes
	 */
	public static function get_image_sizes() {

		global $_wp_additional_image_sizes;

		$sizes       = get_intermediate_image_sizes();
		$image_sizes = array();

		$image_sizes[] = [ 'full', esc_html__( 'Full', 'tpgb' ) ];

		foreach ( $sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$image_sizes[] = [ $size, ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ) ];
			} else {
				$image_sizes[] = [ $size, sprintf(
						'%1$s (%2$sx%3$s)',
						ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
						$_wp_additional_image_sizes[ $size ]['width'],
						$_wp_additional_image_sizes[ $size ]['height']
					) ];
			}
		}

		$image_sizes = apply_filters( 'tpgb_image_sizes', $image_sizes );

		return $image_sizes;
	}
	
	public function tpgb_get_option($options,$field){
		
		$tpgb_options=get_option( $options );
		$values='';
		if($tpgb_options){
			if(isset($tpgb_options[$field]) && !empty($tpgb_options[$field])){
				$values=$tpgb_options[$field];
			}
		}
		return $values;
	}
	
	public static function get_default_thumb(){
		return TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg';
	}
	
	/*-contact form 7 start-*/
	public static function get_contact_form_post() {
		$contact_forms = array();
		$cf7 = get_posts('post_type="wpcf7_contact_form"&numberposts=-1');
		if ($cf7) {
			$contact_forms[0] = ['','Select Form', 'tpgb'];
				foreach ($cf7 as $cform) {
					$contact_forms[] = [$cform->ID,$cform->post_title];
				}
		} else {
			$contact_forms[0] = ['',"No contact forms found",'tpgb'];
		}
		return $contact_forms;
	}
	/*-contact form 7 end-*/
	
	/*-everest form start-*/
	public static function get_everest_form_post() {
		$everest_form = array();
		$ev_form = get_posts('post_type="everest_form"&numberposts=-1');
			if ($ev_form) {
				$everest_form[0]  = ['', esc_html__( 'Select Form', 'tpgb' )];
				foreach ($ev_form as $evform) {
					$everest_form[] = [$evform->ID,$evform->post_title];
				}
			} else {
				$everest_form[0] = ['', esc_html__('No everest forms found', 'tpgb')];
			}
		return $everest_form;
	}
	/*-everest form end-*/
	
	/*-gravity form start-*/
	public static function get_gravity_form_post() {
		$g_form_options = [];
		if ( class_exists( 'GFCommon' ) ) {
		 $gravity_forms = \RGFormsModel::get_forms( null, 'title' );
			$g_form_options [0]  = ['', esc_html__( 'Select Form', 'tpgb' )];
			if ( ! empty( $gravity_forms ) && ! is_wp_error( $gravity_forms ) ) {
				foreach ( $gravity_forms as $form ) {   
					$g_form_options[] = [$form->id,$form->title];
				}
			}
		} else {
			$g_form_options [0]  = ['', esc_html__( 'Form Not Found!', 'tpgb' ) ];
		}
		return $g_form_options;
	}
	/*-gravity form end-*/
	
	/*-ninja form start-*/
	public static function get_ninja_form_post() {
        $options = array();
        if ( class_exists( 'Ninja_Forms' ) ) {
            $contact_forms = Ninja_Forms()->form()->get_forms();
            if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {
                $options[0]  = ['', esc_html__( 'Select Ninja Form', 'tpgb' )];
                foreach ( $contact_forms as $form ) {   
                    //$options[ $form->get_id() ] = $form->get_setting( 'title' );
					$options[] = [$form->get_id(),$form->get_setting( 'title' )];
                }
            }
        } else {
            $options[0] = ['', esc_html__( 'Create a Form First', 'tpgb' )];
        }
        return $options;
    }
	/*-ninja form end-*/
	
	/*-wpforms start-*/
	public static function get_wpforms_form_post() {
        $options = array();
        if ( class_exists( '\WPForms\WPForms' ) ) {
            $args = array(
                'post_type'         => 'wpforms',
                'posts_per_page'    => -1
            );
            $contact_forms = get_posts( $args );
            if ( ! empty( $contact_forms ) && ! is_wp_error( $contact_forms ) ) {
                $options[0] = ['', esc_html__( 'Select a WPForm', 'tpgb' )];
                foreach ( $contact_forms as $post ) {   
                    //$options[ $post->ID ] = $post->post_title;
					$options[] = [$post->ID,$post->post_title];
                }
            }
        } else {
            $options[0] = ['', esc_html__( 'Create a Form First', 'tpgb' )];
        }
        return $options;
    }
	/*-wpforms end-*/
	
	/* Generate HTML of Breadcrumbs */
	public static function theplus_breadcrumbs( $icontype='', $sepIconType='', $icons='', $homeTitle='', $sepIcons='', $activeTextDefault='',$breadcrumbs_last_sec_tri_normal='', $bdToggleHome='', $bdToggleParent='', $bdToggleCurrent='', $letterLimitParent='', $letterLimitCurrent='', $markupSch =false, $ctmHomeurl=[] ) {
		
        if($homeTitle != '') {
            $text['home'] = $homeTitle;
        } else {
            $text['home'] = 'Home';
        }
        $text['category'] = esc_html__('Archive by "%s"', 'tpgb'); 
        $text['search']   = esc_html__('Search Results for "%s"', 'tpgb');
        $text['tag']      = esc_html__('Posts Tagged "%s"', 'tpgb');
        $text['author']   = esc_html__('Articles Posted by %s', 'tpgb');
        $text['404']      = esc_html__('Error 404', 'tpgb');
        $showCurrent = 1; 
        $showOnHome  = 1; 
        $delimiter   = ' <span class="del"></span> '; 
        
		$schemaArr = [ 
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => [],
		];
		$breadposi = 0;
        if($bdToggleCurrent == 'on-off-current'){
            if($breadcrumbs_last_sec_tri_normal != '') {
                if($activeTextDefault != '') {
                    $before = '<span class="current_active normal"><div class="current_tab_sec">';
                } else {
                    $before = '<span class="current normal"><div class="current_tab_sec">'; 
                }
            } else {
                if($activeTextDefault != '') {
                    $before = '<span class="current_active"><div class="current_tab_sec">';
                } else {
                    $before = '<span class="current"><div class="current_tab_sec">'; 
                }
            }
        } else {
            if($breadcrumbs_last_sec_tri_normal != '') {
                if($activeTextDefault != ''){
                    $before = '<span class="current_active normal on-off-current"><div class="current_tab_sec">';
                } else {
                    $before = '<span class="current normal on-off-current"><div class="current_tab_sec">'; 
                }
            } else {
                if($activeTextDefault != ''){
                    $before = '<span class="current_active on-off-current"><div class="current_tab_sec">';
                } else {
                    $before = '<span class="current on-off-current"><div class="current_tab_sec">'; 
                }
            }			
        }
       
        $after = '</div></span>';
        
        $icons_content = '';
        if($icontype=='icon' && $icons != ''){
            $icons_content = '<i class=" '.esc_attr($icons).' bread-home-icon" ></i>';
        }
        if($icontype=='image' && $icons != ''){
            $icons_content = '<img class="bread-home-img" src="'.esc_url($icons).'" />';
        }
        $icons_sep_content ='';
        if($sepIconType=='sep_icon' && $sepIcons != ''){
                $icons_sep_content = '<i class=" '.esc_attr($sepIcons).' bread-sep-icon" ></i>';
        }
        if($sepIconType=='sep_image' && $sepIcons != ''){
            $icons_sep_content = '<img class="bread-sep-icon" src="'.esc_url($sepIcons).'" />';		
        }
        
        global $post;
		$homeLink = ( !empty($ctmHomeurl) && !empty($ctmHomeurl['url']) ) ? $ctmHomeurl['url'] : home_url().'/';
        $linkBefore = '<span>';
        $linkAfter = '</span>';
        if($icons_content != '' || $icons_sep_content != '' ||  $text['home'] != ''){
            if($bdToggleHome != '' && $bdToggleHome == true) {
				$link_attr = Tp_Blocks_Helper::add_link_attributes($ctmHomeurl);
                $home_link = '<span class="bc_home"><a class="home_bread_tab" href="%1$s" target="'.((!empty($ctmHomeurl) && !empty($ctmHomeurl['target'])) ? '_blank' : '').'" '.$link_attr.' >'.$icons_content.'%2$s'.$icons_sep_content.'</a>' . $linkAfter;
            } else {
                $home_link = '';
            }
            $home_delimiter = ' <span class="del"></span> ';
        } else {
            $home_link = $home_delimiter = '';
        }
        if($bdToggleParent != '' && $bdToggleParent = true) {
                $link = '<span class="bc_parent"><a class="parent_sub_bread_tab" href="%1$s">%2$s'.$icons_sep_content.'</a>' . $linkAfter;
        } else {			
                $link = '';
        }
        
        if (is_home() || is_front_page()) {
            if ($showOnHome == 1) $crumbs_output = '<nav id="breadcrumbs"><a href="' . esc_url(home_url()) . '">'.$icons_content . esc_html($text['home']) . '</a></nav>';
			$schemaArr['itemListElement'][] = array(
				"@type" => "ListItem",
				"position" => ++$breadposi,
				"name" => $text['home'],
				"item" => esc_url(home_url())
			);
        } else {
            $crumbs_output ='<nav id="breadcrumbs">' . sprintf($home_link, $homeLink, $text['home']) . $home_delimiter;
            if ( is_category() ) {
                $thisCat = get_category(get_query_var('cat'), false);
                if ($thisCat->parent != 0) {
                    $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$schemaArr['itemListElement'][] = array(
						"@type" => "ListItem",
						"position"=> ++$breadposi,
						"name" => $text['category'],
						"item" => get_category_link($thisCat->term_id)
					);
                    $cats = str_replace('<a', $linkBefore . '<a', $cats);
                    $cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);
                    $crumbs_output .= $cats;
                }
                $crumbs_output .= $before . sprintf($text['category'], single_cat_title('', false)) . $after;
            } elseif ( is_search() ) {
                $crumbs_output .= $before . sprintf($text['search'], get_search_query()) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => $text['search'],
					"item" => site_url().'/'.get_search_query()
				);
            }
            elseif (is_singular('topic') ){
                $post_type = get_post_type_object(get_post_type());
                printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => $post_type->labels->singular_name,
					"item" => $homeLink . '/forums/', $post_type->labels->singular_name
				);
            }
            /* in forum, add link to support forum page template */
            elseif (is_singular('forum')){
                $post_type = get_post_type_object(get_post_type());
                printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => $post_type->labels->singular_name,
					"item" => $homeLink . '/forums/', $post_type->labels->singular_name
				);
            }
            elseif (is_tax('topic-tag')){
                $post_type = get_post_type_object(get_post_type());
                printf($link, $homeLink . '/forums/', $post_type->labels->singular_name);
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => $post_type->labels->singular_name,
					"item" => $homeLink . '/forums/', $post_type->labels->singular_name
				);
            }
            elseif ( is_day() ) {
                $crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
                $crumbs_output .= sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
                $crumbs_output .= $before . esc_html(get_the_time('d')) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => get_the_time('d'),
					"item" => get_month_link(get_the_time('Y'),get_the_time('m'))
				);
            } elseif ( is_month() ) {
                $crumbs_output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
                $crumbs_output .= $before . esc_html(get_the_time('F')) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => get_the_time('d'),
					"item" => get_year_link(get_the_time('Y'))
				);
            } elseif ( is_year() ) {
                $crumbs_output .= $before . esc_html(get_the_time('Y')) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => get_the_time('d'),
				);
            } elseif ( (is_single() && !is_attachment()) ) {
				
                if ( 'product' === get_post_type( $post ) ) {
                    
                    $terms_cate = wc_get_product_terms(
                        $post->ID,
                        'product_cat',
                        apply_filters(
                            'woocommerce_breadcrumb_product_terms_args',
                            array(
                                'orderby' => 'parent',
                                'order'   => 'DESC',
                            )
                        )
                    );
    
                    if ( $terms_cate ) {
                        $first_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms_cate[0], $terms_cate );
                        $ancestors = get_ancestors( $first_term->term_id, 'product_cat' );
                        $ancestors = array_reverse( $ancestors );
						
                        foreach ( $ancestors as $ancestor ) {
                            $ancestor = get_term( $ancestor, 'product_cat' );
    
                            if ( ! is_wp_error( $ancestor ) && $ancestor ) {
								
                                $crumbs_output .= sprintf($link, get_term_link( $ancestor ), $ancestor->name);
								$schemaArr['itemListElement'][] = array(
									"@type" => "ListItem",
									"position"=> ++$breadposi,
									"name" =>  $ancestor->name,
									"item" => get_term_link( $ancestor )
								);
                            }
                        }
                        if($bdToggleCurrent == 'on-off-current'){
                            $crumbs_output .= sprintf($link, get_term_link( $first_term ), $first_term->name);
							$schemaArr['itemListElement'][] = array(
								"@type" => "ListItem",
								"position"=> ++$breadposi,
								"name" =>  $first_term->name,
								"item" => get_term_link( $first_term )
							);
                        }else{
                            $crumbs_output .= $linkBefore . '<a href="'.esc_url(get_term_link( $first_term )). '">'.esc_html($first_term->name).'</a>' . $linkAfter;
							$schemaArr['itemListElement'][] = array(
								"@type" => "ListItem",
								"position"=> ++$breadposi,
								"name" => $first_term->name ,
								"item" => get_term_link( $first_term )
							);
                        }
                    }
                    
                    if($letterLimitCurrent != '0'){
                        if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .substr(get_the_title(),0,$letterLimitCurrent). $after;
                    }else{
                        if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .get_the_title(). $after;
                    }
                } else if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
					if($bdToggleParent != '' && $bdToggleParent = true){
						$crumbs_output .= $linkBefore . '<a href="'.esc_url($homeLink). '?post_type=' . esc_attr($slug["slug"]) . '">'.esc_html($post_type->labels->singular_name).$icons_sep_content.'</a>' . $linkAfter;
					}
                    if($letterLimitCurrent != '0'){
                        if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .substr(get_the_title(),0,$letterLimitCurrent). $after;
                    }else{
                        if ($showCurrent == 1) $crumbs_output .= $delimiter . $before .get_the_title(). $after;
                    }
					$schemaArr['itemListElement'][] = array(
						"@type" => "ListItem",
						"position"=> ++$breadposi,
						"name" => $post_type->labels->singular_name,
						"item" => $homeLink.'?post_type=' . esc_attr($slug["slug"])
					);
                } else {
                    $cat = get_the_category();
                    if(isset($cat[0])) {
                        $cat =  $cat[0];
                        $cats = get_category_parents($cat, TRUE, $delimiter);
                        if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
                        $cats = str_replace('<a', $linkBefore . '<a', $cats);
                        $cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);						
                        if($bdToggleParent != '' && $bdToggleParent == true) {
                            $crumbs_output .= $cats;
							$schemaArr['itemListElement'][] = array(
								"@type" => "ListItem",
								"position"=> ++$breadposi,
								"name" => $cat->term_id,
								"item" => get_category_link($cat->term_id)
							);
                        }else{
                            $crumbs_output .='';
							$schemaArr['itemListElement'][] = array(
								"@type" => "ListItem",
								"position"=> ++$breadposi,
								"name" =>get_the_title(),
								"item" => get_the_permalink()
							);
                        }						
                        
                        if($letterLimitCurrent != '0'){
                            if ($showCurrent == 1) $crumbs_output .= $before . substr(get_the_title(),0,$letterLimitCurrent) . $after;
                        }else{
                            if ($showCurrent == 1) $crumbs_output .= $before . get_the_title() . $after;
                        }
                    }
                }
            } elseif ( class_exists('WooCommerce') && is_product_category() ){
				
				$current_term = $GLOBALS['wp_query']->get_queried_object();
				
				$permalinks   = wc_get_permalink_structure();
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page    = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
					$crumbs_output .= sprintf($link, get_permalink( $shop_page ), get_the_title( $shop_page ));
				}

				if($bdToggleParent != '' && $bdToggleParent = true) {

					$ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
					$ancestors = array_reverse( $ancestors );

					$link = '<span class="bc_parent"><a class="parent_sub_bread_tab" href="%1$s">%2$s'.$icons_sep_content.'</a>' . $linkAfter;

					foreach ( $ancestors as $ancestor ) {
						$ancestor = get_term( $ancestor,'product_cat' );

						
						if ( ! is_wp_error( $ancestor ) && $ancestor ) {
							$crumbs_output .= sprintf($link, get_term_link( $ancestor ), $ancestor->name);
						}
					}
					
				}

				if($current_term && $bdToggleCurrent == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal"><div class="current_tab_sec">'. esc_html($current_term->name) . '</div></span>';
				}
				
			} elseif ( class_exists('WooCommerce') && is_product_tag() ){
				
				$current_term = $GLOBALS['wp_query']->get_queried_object();
				
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_page    = get_post( $shop_page_id );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop.
				if ( $shop_page_id && $shop_page && isset( $permalinks['product_base'] ) && strstr( $permalinks['product_base'], '/' . $shop_page->post_name ) && intval( get_option( 'page_on_front' ) ) !== $shop_page_id ) {
					$crumbs_output .= sprintf($link, get_permalink( $shop_page ), get_the_title( $shop_page ));
				}

				if($current_term && $bdToggleCurrent == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal"><div class="current_tab_sec">'. esc_html($current_term->name) . '</div></span>';
				}
				
			} elseif ( class_exists('WooCommerce') && is_shop()){
				
				if ( intval( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) {
					
					return;
				}
		
				$_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
		
				if ( ! $_name ) {
					$product_post_type = get_post_type_object( 'product' );
					$_name             = $product_post_type->labels->name;
				}
				
				//$this->add_crumb( $_name, get_post_type_archive_link( 'product' ) );
				if($bdToggleCurrent == 'on-off-current'){
					$crumbs_output .= '<span class="current_active normal "><div class="current_tab_sec">'. esc_html($_name  ) . '</div></span>';
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type = get_post_type_object(get_post_type());
				if(!empty($post_type) && isset($post_type->labels) && isset($post_type->labels->singular_name)){
					$crumbs_output .= $before . esc_html($post_type->labels->singular_name) . $after;
				}
            } elseif ( is_attachment() ) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                if($cat) {
                    $cat = $cat[0];
                    $cats = get_category_parents($cat, TRUE, $delimiter);
                    $cats = str_replace('<a', $linkBefore . '<a', $cats);
                    $cats = str_replace('</a>', $icons_sep_content.'</a>' . $linkAfter, $cats);
                    $crumbs_output .= $cats;
					
					$schemaArr['itemListElement'][] = array(
						"@type" => "ListItem",
						"position"=> ++$breadposi,
						"name" => $cat[0]['term_id'],
						"item" => get_category_link($cat[0]['term_id'])
					);
                   
					printf($link, get_permalink($parent), $parent->post_title);
                    if ($showCurrent == 1) $crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
                }
            } elseif ( is_page() && !$post->post_parent ) {
                if ($showCurrent == 1) $crumbs_output .= $before . esc_html(get_the_title()) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" => get_the_title(),
					"item" => ''
				);
            } elseif ( is_page() && $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
				$posi = ++$breadposi;
                while ($parent_id) {
					$posi++;
                    $page = get_page($parent_id);
                    $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					$schemaArr['itemListElement'][] = array(
						"@type" => "ListItem",
						"position"=> $posi,
						"name" => get_the_title($page->ID),
						"item" => get_permalink($page->ID)
					);
                    $parent_id  = $page->post_parent;
                }
				$breadposi = $posi;
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    $crumbs_output .= $breadcrumbs[$i];
                    if ($i != count($breadcrumbs)-1) $crumbs_output .= $delimiter;
                }
                if ($showCurrent == 1){
					$crumbs_output .= $delimiter . $before . esc_html(get_the_title()) . $after;
					$schemaArr['itemListElement'][] = array(
						"@type" => "ListItem",
						"position"=> ++$breadposi,
						"name" => get_the_title(),
						"item" => get_permalink()
					);
				}
            } elseif ( is_tag() ) {
                $crumbs_output .= $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" =>$text['tag'],
					"item" => get_permalink()
				);
            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata($author);
                $crumbs_output .= $before . sprintf($text['author'], $userdata->display_name) . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" =>$text['tag'],
					"item" => $userdata->user_url
				);
            } elseif ( is_404() ) {
                $crumbs_output .= $before . $text['404'] . $after;
				$schemaArr['itemListElement'][] = array(
					"@type" => "ListItem",
					"position"=> ++$breadposi,
					"name" =>$text['404'],
				);
            }
            if ( get_query_var('paged') ) {
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ' (';
                    $crumbs_output .= '<span class="del"></span>'.esc_html__('Page', 'tpgb') . ' ' . get_query_var('paged');
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $crumbs_output .= ')';
            }
            $crumbs_output .= '</nav>';
			
        }
		if( !empty($markupSch) ){
			$encoded_data = wp_json_encode( $schemaArr );
			$crumbs_output .= '<script type="application/ld+json">'.$encoded_data.'</script>';
		}
        return $crumbs_output;
	}
	
	/* Get Taxonomie  Slug
	 * @since 1.1.0
	 */
	public static function tpgb_get_post_taxonomies() {
		$args = array(
			'public'   => true,
			'show_ui' => true
		);
		$output = 'objects'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$cat_list = array();
		$cat_list[] = ['' , 'Select Taxonomy'];
		$taxonomies = get_taxonomies( $args, $output, $operator );
		if ( $taxonomies ) {
			
			foreach ( $taxonomies  as $taxonomy ) {
				$exclude = array( 'nxt_builder_category' );
				if( TRUE === in_array( $taxonomy->name, $exclude ) )
					continue;
					
				$cat_list[] = [ $taxonomy->name , $taxonomy->label ];
				
				
			}
			
		}
		return $cat_list;
	}
	
	/*
	 * Get Common Classes Block Options
	 * @since 1.1.1
	 */
	public static function block_wrapper_classes( $attr ){
		$className = (!empty($attr['className'])) ? $attr['className'] :'';
		$align = (!empty($attr['align'])) ? $attr['align'] :'';
		
		$blockClass = '';
		if(!empty($className)){
			$blockClass .= $className;
		}
		if(!empty($align)){
			$blockClass .= ' align'.$align;
		}
		
		return $blockClass;
	}

	/*
	 * Get Carousel Settings Block Options
	 * @since 1.1.2
	 */
	public static function carousel_settings( $attr ){	
		$cenpadding = isset( $attr['centerPadding'] ) ? (array) $attr['centerPadding'] : '';
		
		$settings =[
			'updateOnMove' => true,
			'direction' => isset( $attr['sliderMode'] ) && $attr['sliderMode'] == 'vertical'  ? 'ttb' : 'ltr',
			'start' => isset( $attr['initialSlide'] ) ? $attr['initialSlide'] : 0,
			'autoplay' => isset( $attr['slideAutoplay'] ) ? $attr['slideAutoplay'] : false,
			'speed' => isset( $attr['slideSpeed'] ) ? (int)$attr['slideSpeed'] : 1500,
			'interval' => isset( $attr['slideAutoplaySpeed'] ) ? (int)$attr['slideAutoplaySpeed'] : '',
			'drag' => isset( $attr['slideDraggable']['md'] ) ? $attr['slideDraggable']['md'] : false  ,
			'type' => !empty( $attr['slideInfinite'] ) ? 'loop' : 'slide',
			'pauseOnHover' => isset( $attr['slideHoverPause'] ) ? $attr['slideHoverPause'] : false,
			'pagination' => isset( $attr['showDots']['md'] ) ? $attr['showDots']['md'] : false ,
			'arrows' => ( !empty($attr['showArrows']['md']) || !empty($attr['showArrows']['sm']) || !empty($attr['showArrows']['xs']) ) ? true : false,
			'padding' =>  isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '',
			'perMove' => isset( $attr['slideScroll']['md'] ) ? (int)$attr['slideScroll']['md']  : 1,
			'perPage' => isset( $attr['slideColumns']['md'] ) ? (int)$attr['slideColumns']['md'] : 1,
			'wheel'   => isset( $attr['slidewheel'] ) ? $attr['slidewheel'] : false,
			'releaseWheel' => isset( $attr['slidewheel'] ) ? $attr['slidewheel'] : false,
			'waitForTransition' => isset( $attr['waitfortras'] ) ? $attr['waitfortras'] : false,
			'keyboard' => isset( $attr['slidekeyNav'] ) ? $attr['slidekeyNav'] : false,
			'breakpoints' => [
				'1024' => [
					'pagination' => ( !isset($attr['showDots']['sm']) ) ? $attr['showDots']['md'] : ( isset($attr['showDots']['sm'])  ? $attr['showDots']['sm'] : false ) ,

					'drag' => ( !isset($attr['slideDraggable']['sm']) ) ? $attr['slideDraggable']['md'] : ( isset($attr['slideDraggable']['sm'])  ? $attr['slideDraggable']['sm'] : false ),


					'padding' => ( !isset( $cenpadding['sm']) ) ? (isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '') : ( isset($cenpadding['sm'])  ? $cenpadding['sm'] : '' ),

					'perMove' => ( !isset($attr['slideScroll']['sm']) ) ? (int)$attr['slideScroll']['md'] : ( isset($attr['slideScroll']['sm'])  ? (int)$attr['slideScroll']['sm'] : 1 ) ,
					
					'perPage' =>  ( !isset( $attr['slideColumns']['sm']) ) ? $attr['slideColumns']['md'] : ( isset($attr['slideColumns']['sm'])  ? $attr['slideColumns']['sm'] : 1 ),
				],
				'767' => [
					'pagination' => ( !isset($attr['showDots']['xs']) ) ? ( (!isset($attr['showDots']['sm'])) ? $attr['showDots']['md'] : $attr['showDots']['sm'] ) : (isset($attr['showDots']['xs']) ? $attr['showDots']['xs'] : false),

					'drag' => ( !isset($attr['slideDraggable']['xs']) ) ? ( (!isset($attr['slideDraggable']['sm'])) ? $attr['slideDraggable']['md'] : $attr['slideDraggable']['sm'] ) : (isset($attr['slideDraggable']['xs']) ? $attr['slideDraggable']['xs'] : false),

					'padding' =>  ( !isset($cenpadding['xs']) ) ? ( (!isset($cenpadding['sm'])) ? (isset( $cenpadding['md'] ) ? (int) $cenpadding['md'] : '') : $cenpadding['sm'] ) : (isset($cenpadding['xs']) ? $cenpadding['xs'] : ''),

					'perMove' => ( !isset($attr['slideScroll']['xs']) ) ? ( (!isset($attr['slideScroll']['sm'])) ? (int)$attr['slideScroll']['md'] : (int)$attr['slideScroll']['sm'] ) : (isset($attr['slideScroll']['xs']) ? (int)$attr['slideScroll']['xs'] : 1),

					'perPage' =>  ( !isset($attr['slideColumns']['xs']) ) ? ( (!isset($attr['slideColumns']['sm'])) ? $attr['slideColumns']['md'] : $attr['slideColumns']['sm'] ) : (isset($attr['slideColumns']['xs']) ? $attr['slideColumns']['xs'] : 1),
				]
			],
		];

		if(isset($attr['centerMode']['md']) && $attr['centerMode']['md'] == true){
			$settings['focus'] =  'center';
		}else if(isset( $attr['slideScroll']['md'] ) && $attr['slideScroll']['md'] == 1){
			$settings['focus'] =  0;
		}else{
			$settings['focus'] =  false;
		}
		
		
		if(isset($attr['centerMode']['sm']) && $attr['centerMode']['sm'] == true){
			$settings['breakpoints']['1024']['focus'] =  'center';
		}else if(!isset( $attr['centerMode']['sm']) && !isset( $attr['slideScroll']['sm']) ){
			$settings['breakpoints']['1024']['focus'] =  $settings['focus'];
		}else if(isset( $attr['slideScroll']['sm'] ) && $attr['slideScroll']['sm'] == 1){
			$settings['breakpoints']['1024']['focus'] =  0;
		}else{
			$settings['breakpoints']['1024']['focus'] =  false;
		}

		
		if(isset($attr['centerMode']['xs']) && $attr['centerMode']['xs'] == true){
			$settings['breakpoints']['767']['focus'] =  'center';
		}else if(!isset( $attr['centerMode']['xs']) && !isset( $attr['slideScroll']['xs']) ){
			$settings['breakpoints']['767']['focus'] =  $settings['breakpoints']['1024']['focus'];
		}else if(isset( $attr['slideScroll']['xs'] ) && $attr['slideScroll']['xs'] == 1){
			$settings['breakpoints']['767']['focus'] =  0;
		}else{
			$settings['breakpoints']['767']['focus'] =  false;
		}

		if( (isset($attr['centerMode']['md']) && $attr['centerMode']['md'] == true) || (isset($attr['centerMode']['sm']) && $attr['centerMode']['sm'] == true) || (isset($attr['centerMode']['xs']) && $attr['centerMode']['xs'] == true) ){
			if(isset($attr['trimSpace']) && $attr['trimSpace'] == true){
				$settings['trimSpace'] =  true;
			}else{
				$settings['trimSpace'] =  false;
			}
		}

		if(isset( $attr['sliderMode'])  &&  $attr['sliderMode'] == 'vertical' ){
			$settings['heightRatio'] = isset( $attr['slideheightRatio']) ? $attr['slideheightRatio'] : '';
		}

		return $settings;
	}
	
	/*
	 * Get Carousel Custom dots Block Options
	 * 	@since 1.1.2
	 */
	public static function tpgb_carousel_arrow($arrowsStyle , $arrowsPosition='' ){
		$arrow = '';
		$arrow .= '<div class="splide__arrows '.esc_attr($arrowsStyle).'">';
			$arrow .= '<button class="splide__arrow splide__arrow--prev '.esc_attr($arrowsStyle).' '.($arrowsStyle == 'style-3' || $arrowsStyle == 'style-4' ? esc_attr($arrowsPosition) : '').' ">';
				if($arrowsStyle == 'style-2' || $arrowsStyle == 'style-5' ){
					$arrow .= '<span class="icon-wrap"></span>';
				}else if($arrowsStyle == 'style-3' || $arrowsStyle == 'style-4'){
					$arrow .= '<span class="icon-wrap"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="angle-left" class="svg-inline--fa fa-angle-left fa-w-6" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M4.2 247.5L151 99.5c4.7-4.7 12.3-4.7 17 0l19.8 19.8c4.7 4.7 4.7 12.3 0 17L69.3 256l118.5 119.7c4.7 4.7 4.7 12.3 0 17L168 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 264.5c-4.7-4.7-4.7-12.3 0-17z"></path></svg></span>';
				}else if($arrowsStyle == 'style-6'){
					$arrow .= '<span class="icon-wrap"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="long-arrow-left" class="svg-inline--fa fa-long-arrow-left fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M152.485 396.284l19.626-19.626c4.753-4.753 4.675-12.484-.173-17.14L91.22 282H436c6.627 0 12-5.373 12-12v-28c0-6.627-5.373-12-12-12H91.22l80.717-77.518c4.849-4.656 4.927-12.387.173-17.14l-19.626-19.626c-4.686-4.686-12.284-4.686-16.971 0L3.716 247.515c-4.686 4.686-4.686 12.284 0 16.971l131.799 131.799c4.686 4.685 12.284 4.685 16.97-.001z"></path></svg></span>';
				}
			$arrow .= '</button>';
			$arrow .= '<button class="splide__arrow splide__arrow--next '.esc_attr($arrowsStyle).' '.($arrowsStyle == 'style-3' || $arrowsStyle == 'style-4' ? esc_attr($arrowsPosition) : '').'">';
				if($arrowsStyle == 'style-2' || $arrowsStyle == 'style-5' ){
					$arrow .= '<span class="icon-wrap"></span>';
				}else if($arrowsStyle == 'style-3' || $arrowsStyle == 'style-4'){
					$arrow .= '<span class="icon-wrap"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="angle-right" class="svg-inline--fa fa-angle-right fa-w-6" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path fill="currentColor" d="M187.8 264.5L41 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 392.7c-4.7-4.7-4.7-12.3 0-17L122.7 256 4.2 136.3c-4.7-4.7-4.7-12.3 0-17L24 99.5c4.7-4.7 12.3-4.7 17 0l146.8 148c4.7 4.7 4.7 12.3 0 17z"></path></svg></span>';
				}else if($arrowsStyle == 'style-6'){
					$arrow .= '<span class="icon-wrap"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="long-arrow-alt-right" class="svg-inline--fa fa-long-arrow-alt-right fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M340.485 366l99.03-99.029c4.686-4.686 4.686-12.284 0-16.971l-99.03-99.029c-7.56-7.56-20.485-2.206-20.485 8.485v71.03H12c-6.627 0-12 5.373-12 12v32c0 6.627 5.373 12 12 12h308v71.03c0 10.689 12.926 16.043 20.485 8.484z"></path></svg></span>';
				}
			$arrow .= '</button>';
		$arrow .=  '</div>';

		return $arrow;
	}
	
	/*
	 * Get Carousel Arrows Css
	 * 	@since 1.1.2
	 */
	public static function tpgb_carousel_arrow_css($showArrows , $block_id ){
		$arrowCss = '';
		if( isset($showArrows['md']) &&  $showArrows['md'] === true){
			$arrowCss .= '.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: block }';
			if( isset($showArrows['sm']) && $showArrows['sm'] === false){
				$arrowCss .= '@media (max-width:1024px){.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: none } }';
			}
			if( isset($showArrows['xs']) && $showArrows['xs'] === false){
				$arrowCss .= '@media (max-width:767px){.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: none } }';
			}
		}
		if( isset($showArrows['sm']) && $showArrows['sm'] === true ){
			$arrowCss .= '@media (max-width:1024px){.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: block } }';
			if( isset($showArrows['xs']) && $showArrows['xs'] === false){
				$arrowCss .= '@media (max-width:767px){.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: none } }';
			}
		}
		if( isset($showArrows['xs']) && $showArrows['xs'] === true){
			$arrowCss .= '@media (max-width:767px){.tpgb-block-'.esc_attr($block_id).' .splide__arrows{display: block } }';
		}

		return "<style>".$arrowCss."</style>";
	}
	
	/**
	 * Cross copy paste media import
	 * @since  1.1.0
	 */
	public static function cross_copy_paste_media_import() {
		
		check_ajax_referer( 'tpgb-addons', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				__( 'Not a Valid', 'tpgb' ),
				403
			);
		}
		require_once TPGB_PATH . 'classes/global-options/tp-import-media.php';
		$media_import = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';
		
		if ( empty( $media_import ) ) {
			wp_send_json_error( __( 'Empty Content.', 'tpgb' ) );
		}

		$media_import = array( json_decode( $media_import, true ) );
		$media_import = self::tp_import_media_copy_content( $media_import );

		wp_send_json_success( $media_import );
	}
	
	/**
	 * Recursively data.
	 *
	 * Accept any type of data and a callback function. The callback
	 * function runs recursively for each data and his child data.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 */
	public static function tp_import_media_copy_content( $data_import ){
		return self::array_recursively_data(
			$data_import,
			function( $block_data ) {
				
				$elements = self::block_data_instance( $block_data );
				
				return $elements;
			}
		);
	}
	
	/*
	 * Block Data inner Block Instance
	 *
	 * @since 1.1.3
	 */
	public static function block_data_instance( array $block_data, array $args = [], $block_args = null ){

		if ( $block_data['name'] && $block_data['clientId'] && $block_data['attributes'] ) {
		
			foreach($block_data['attributes'] as $block_key => $block_val) {
				
				if( isset( $block_val['url'] ) && isset( $block_val['id'] ) && !empty( $block_val['url'] ) ){
					$new_media = Tpgb_Import_Images::media_import( $block_val );
					$block_data['attributes'][$block_key] = $new_media;
				}else if(isset( $block_val['url'] ) && !empty( $block_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $block_val['url'])) {
					$new_media = Tpgb_Import_Images::media_import( $block_val );
					$block_data['attributes'][$block_key] = $new_media;
				}else if(is_array($block_val) && !empty($block_val)){
					if( !array_key_exists("md",$block_val) && !array_key_exists("openTypography",$block_val) && !array_key_exists("openBorder",$block_val) && !array_key_exists("openShadow",$block_val) && !array_key_exists("openFilter",$block_val)  ){
						foreach($block_val as $key => $val) {
							if(is_array($val) && !empty($val)){
								
								if( isset( $val['url'] ) && ( isset( $val['Id'] ) || isset( $val['id'] ) ) && !empty( $val['url'] ) ){
									$new_media = Tpgb_Import_Images::media_import( $val );
									$block_data['attributes'][$block_key][$key] = $new_media;
								}else if( isset( $val['url'] ) && !empty( $val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $val['url']) ) {
									$new_media = Tpgb_Import_Images::media_import( $val );
									$block_data['attributes'][$block_key][$key] = $new_media;
								}else{
									foreach($val as $sub_key => $sub_val) {
										if( isset( $sub_val['url'] ) && ( isset( $sub_val['Id'] ) || isset( $sub_val['id'] ) ) && !empty( $sub_val['url'] ) ){
											$new_media = Tpgb_Import_Images::media_import( $sub_val );
											$block_data['attributes'][$block_key][$key][$sub_key] = $new_media;
										}else if( isset( $sub_val['url'] ) && !empty( $sub_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $sub_val['url'])) {
											$new_media = Tpgb_Import_Images::media_import( $sub_val );
											$block_data['attributes'][$block_key][$key][$sub_key] = $new_media;
										}else if(is_array($sub_val) && !empty($sub_val)){
											foreach($sub_val as $sub_key1 => $sub_val1) {
												if( isset( $sub_val1['url'] ) && ( isset( $sub_val1['Id'] ) || isset( $sub_val1['id'] ) ) && !empty( $sub_val1['url'] ) ){
													$new_media = Tpgb_Import_Images::media_import( $sub_val1 );
													$block_data['attributes'][$block_key][$key][$sub_key][$sub_key1] = $new_media;
												}else if( isset( $sub_val1['url'] ) && !empty( $sub_val1['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $sub_val1['url'])) {
													$new_media = Tpgb_Import_Images::media_import( $sub_val1 );
													$block_data['attributes'][$block_key][$key][$sub_key][$sub_key1] = $new_media;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $block_data;
	}
	
	/**
	 * Recursively data.
	 *
	 * Accept any type of data and a callback function. The callback
	 * function runs recursively for each data and his child data.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 */
	public static function array_recursively_data( $data, $callback, $args = [] ) {
		if ( isset( $data['name'] ) ) {
			if ( ! empty( $data['innerBlocks'] ) ) {
				$data['innerBlocks'] = self::array_recursively_data( $data['innerBlocks'], $callback, $args );
			}

			return call_user_func( $callback, $data, $args );
		}

		foreach ( $data as $block_key => $block_value ) {
			$block_data = self::array_recursively_data( $data[ $block_key ], $callback, $args );

			if ( null === $block_data ) {
				continue;
			}

			$data[ $block_key ] = $block_data;
		}

		return $data;
	}
	
	/*
	 * Custom Font Load
	 * @since 1.2.0
	 */
	public static function tpgb_custom_font(){
		$system_fonts = [
			'id' => 'tpgb-system-fonts',
			'title' => __('System', 'tpgb'),
			'options' => apply_filters('tpgb-system-fonts-list', [
				(object)['label' => __('Default','tpgb'), 'value' => '' ],
				(object)['label' => __('Arial','tpgb'), 'value' => 'Arial' ],
				(object)['label' => __('Georgia','tpgb'), 'value' => 'Georgia' ],
				(object)['label' => __('Helvetica','tpgb'), 'value' => 'Helvetica' ],
				(object)['label' => __('Tahoma','tpgb'), 'value' => 'Tahoma' ],
				(object)['label' => __('Times New Roman','tpgb'), 'value' => 'Times New Roman' ],
				(object)['label' => __('Trebuchet MS','tpgb'), 'value' => 'Trebuchet MS' ],
				(object)['label' => __('Verdana','tpgb'), 'value' => 'Verdana' ],
			]),
		];
		$custom_fonts = [ 
			'id' => 'tpgb-custom-fonts',
			'title' => __('Custom Fonts', 'tpgb'),
			'options' => apply_filters('tpgb-custom-fonts-list', []),
		];
		/*Custom Fonts*/
		if(class_exists('Bsf_Custom_Fonts_Taxonomy')){
			$fonts = Bsf_Custom_Fonts_Taxonomy::get_fonts();
			if(!empty($fonts)){
				foreach ( $fonts as $font => $values ) {
					$custom_fonts[ 'options' ][] = (object)['label' => $font, 'value' => $font ];
				}
			}
		}
		/*Use any Font*/
		if(function_exists('uaf_get_font_families')){
			$uaf_fonts = uaf_get_font_families();
			if(!empty($uaf_fonts)){
				foreach ( $uaf_fonts as $font => $values ) {
					$custom_fonts[ 'options' ][] = (object)['label' => $values, 'value' => $values ];
				}
			}
		}
		if( !empty($custom_fonts['options']) ){
			return wp_json_encode(array_merge($system_fonts,$custom_fonts));
		}else{
			return wp_json_encode($system_fonts);
		}
		return false;
	}
	
	/*
	 * Check Html Tag
	 * @since 1.2.1
	 */
	public static function tpgb_html_tag_check(){
		return [ 'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'a',
			'span',
			'p',
			'header',
			'footer',
			'article',
			'aside',
			'main',
			'nav',
			'section',
			'tr',
			'th',
			'td'
		];
	}
	
	/*
	 * Validate Html Tag
	 * @since 1.2.1
	 */
	public static function validate_html_tag( $check_tag ) {
		return in_array( strtolower( $check_tag ), self::tpgb_html_tag_check() ) ? $check_tag : 'div';
	}

	/*
	 * Add Link Custom Attribute
	 * @since 1.3.1
	 */
	public static function add_link_attributes( $fieldname=[], $separator = ',' ) {
		if(!empty($fieldname) && is_array($fieldname) && isset($fieldname['attr']) && !empty($fieldname['attr'])){
			$output = [];
			$custom_attr = $fieldname['attr'];
			
			$attributes = explode( $separator, $custom_attr );
			foreach ( $attributes as $attribute ) {
				$key_val = explode( '|', $attribute );

				$attr_key = mb_strtolower( $key_val[0] );

				// Remove any not allowed characters.
				preg_match( '/[-_a-z0-9]+/', $attr_key, $key_matches );

				if ( empty( $key_matches[0] ) ) {
					continue;
				}

				$attr_key = $key_matches[0];

				// Avoid Javascript events and unescaped href.
				if ( 'on' === substr( $attr_key, 0, 2 ) || 'href' === $attr_key ) {
					continue;
				}

				if ( isset( $key_val[1] ) ) {
					$attr_value = trim( $key_val[1] );
				} else {
					$attr_value = '';
				}

				$output[ $attr_key ] = $attr_value;
			}

			return self::link_render_html_attributes($output);
		}

		return '';
	}

	/*
	 * Html Render Attributes
	 * @since 1.3.1
	 */
	public static function link_render_html_attributes( array $attributes ) {
		$html_attr = [];

		foreach ( $attributes as $key => $values ) {
			if ( is_array( $values ) ) {
				$values = implode( ' ', $values );
			}

			$html_attr[] = sprintf( '%1$s="%2$s"', $key, esc_attr( $values ) );
		}

		return implode( ' ', $html_attr );
	}

	/*
	* DECRIPT
	* @since 1.2.1
	*/
	public static function tpgb_check_decrypt_key($key){
		$decrypted = self::tpgb_simple_decrypt( $key, 'dy' );
		return $decrypted;
	}
	
	/*
	* ENCRYPT
	* @since 1.2.1
	*/
	public static function tpgb_simple_decrypt($string, $action = 'dy'){
		// you may change these values to your own
		$tppk=get_option( 'tpgb_activate' );
		$secret_key = ( isset($tppk['tpgb_activate_key']) && !empty($tppk['tpgb_activate_key']) ) ? $tppk['tpgb_activate_key'] : 'PO$_key';
		$secret_iv = 'PO$_iv';

		$output = false;
		$encrypt_method = "AES-128-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if( $action == 'ey' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'dy' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}

		return $output;
	}

	/*
	 * Social Review Get API
	 * @since 1.4.8
	 */
	public function tpgb_f_socialreview_Gettoken() {
		$result = [];
		check_ajax_referer('tpgb-addons', 'tpgb_nonce');
		$get_json = wp_remote_get("https://theplusaddons.com/wp-json/template_socialreview_api/v2/socialreviewAPI?time=".time());
		if ( is_wp_error( $get_json ) ) {
			wp_send_json_error( array( 'messages' => 'something wrong in API' ) );
		}else{
			$URL_StatusCode = wp_remote_retrieve_response_code($get_json);
			if($URL_StatusCode == 200){
				$getdata = wp_remote_retrieve_body($get_json);
				$result['SocialReview'] = json_decode($getdata, true);
				$result['success'] = 1;
				wp_send_json($result);
			}
		}
		wp_send_json_error( array( 'messages' => 'something wrong in API' ) );
	}

	/*
	 * Remove Cache Transient Data
	 * @since 1.4.8
	 */
	public function Tp_f_delete_transient() {
		$result = [];
		check_ajax_referer('tpgb-addons', 'tpgb_nonce');
		
		global $wpdb;
			$table_name = $wpdb->prefix . "options";
			$DataBash = $wpdb->get_results( "SELECT * FROM $table_name" );
			$blockName = !empty($_POST['blockName']) ? sanitize_text_field(wp_unslash($_POST['blockName'])) : '';
			
			if($blockName == 'SocialFeed'){
				$transient = array(
					// facebook
						'Fb-Url-','Fb-Time-','Data-Fb-',
					// vimeo
						'Vm-Url-', 'Vm-Time-', 'Data-Vm-',
					// Instagram basic
						'IG-Url-', 'IG-Profile-', 'IG-Time-', 'Data-IG-',	
					// Instagram Graph
						'IG-GP-Url-', 'IG-GP-Time-', 'IG-GP-Data-', 'IG-GP-UserFeed-Url-', 'IG-GP-UserFeed-Data-', 'IG-GP-Hashtag-Url-', 'IG-GP-HashtagID-data-', 'IG-GP-HashtagData-Url-', 'IG-GP-Hashtag-Data-', 'IG-GP-story-Url-', 'IG-GP-story-Data-', 'IG-GP-Tag-Url-', 'IG-GP-Tag-Data-',
					// Tweeter
						'Tw-BaseUrl-', 'Tw-Url-', 'Tw-Time-', 'Data-tw-',
					// Youtube
						'Yt-user-', 'Yt-user-Time-', 'Data-Yt-user-', 'Yt-Url-', 'Yt-Time-', 'Data-Yt-', 'Yt-C-Url-', 'Yt-c-Time-', 'Data-c-Yt-',
					// loadmore
						'SF-Loadmore-',
					// Performance
						'SF-Performance-'
				);
			}else if($blockName == 'SocialReview'){
				$transient = array(
					// Facebook
						'Fb-R-Url-', 'Fb-R-Time-', 'Fb-R-Data-',
					// Google
						'G-R-Url-', 'G-R-Time-', 'G-R-Data-',
					// loadmore
						'SR-LoadMore-',
					// Performance
						'SR-Performance-',
					// Beach
						'Beach-Url-', 'Beach-Time-', 'Beach-Data-',
				);
			}
			foreach ($DataBash as $First) {
				foreach ($transient as $second) {
					$Find_Transient = !empty($First->option_name) ? strpos( $First->option_name, $second ) : '';
					if(!empty($Find_Transient)){
						$wpdb->delete( $table_name, array( 'option_name' => $First->option_name ) );
					}
				}
			}
			
		$result['success'] = 1;
		$result['blockName'] = $blockName;
		echo wp_send_json($result);
	}

	/*
	 * Flex child css
	 * @since 1.4.0
	 */
	public static function tpgb_flex_child_css($flexChild  , $selector){
		$flexChildCss = '';
		foreach($flexChild as $index => $item){
			$childSele = ''.$selector.'('.($index+1).')';
			
			$item = (array) $item;

			$flexChildCss .= ( isset($item['flexShrink']['md']) && $item['flexShrink']['md']!='' ) ? $childSele.'{ flex-shrink : '.$item['flexShrink']['md'].' }' : '' ;
			$flexChildCss .= ( isset($item['flexGrow']['md']) && $item['flexGrow']['md']!='' ) ? $childSele.'{ flex-grow : '.$item['flexGrow']['md'].' }' : '' ;
			$flexChildCss .= ( isset($item['flexBasis']['md']) && $item['flexBasis']['md']!='' ) ? $childSele.'{ flex-basis : '.$item['flexBasis']['md'].$item['flexBasis']['unit'].' }' : '' ;
			$flexChildCss .= ( isset($item['flexselfAlign']['md']) && $item['flexselfAlign']['md']!='' ) ? $childSele.'{ align-self : '.$item['flexselfAlign']['md'].' }' : '' ;
			$flexChildCss .= ( isset($item['flexOrder']['md']) && $item['flexOrder']['md']!='' ) ? $childSele.'{ order : '.$item['flexOrder']['md'].' }' : '' ;
			
			//Tablet Css
			if( isset($item['flexShrink']['sm']) && $item['flexShrink']['sm']!='' ){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-shrink : '.$item['flexShrink']['sm'].' } }';
			} else if( isset($item['flexShrink']['md']) && $item['flexShrink']['md']!='' ){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-shrink : '.$item['flexShrink']['md'].' } }';
			}

			if(isset($item['flexGrow']['sm']) && $item['flexGrow']['sm']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-grow : '.$item['flexGrow']['sm'].' } }';
			} else if(isset($item['flexGrow']['md']) && $item['flexGrow']['md']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-grow : '.$item['flexGrow']['md'].' } }';
			}

			if(isset($item['flexBasis']['sm']) && $item['flexBasis']['sm']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-basis : '.$item['flexBasis']['sm'].$item['flexBasis']['unit'].' } }';
			}else if(isset($item['flexBasis']['md']) && $item['flexBasis']['md']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ flex-basis : '.$item['flexBasis']['md'].$item['flexBasis']['unit'].' } }';
			}

			if(isset($item['flexselfAlign']['sm']) && $item['flexselfAlign']['sm']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ align-self: '.$item['flexselfAlign']['sm'].' } }';
			}else if(isset($item['flexselfAlign']['md']) && $item['flexselfAlign']['md']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ align-self: '.$item['flexselfAlign']['md'].' } }';
			}


			if(isset($item['flexOrder']['sm']) && $item['flexOrder']['sm']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ order : '.$item['flexOrder']['sm'].' } }';
			}else if(isset($item['flexOrder']['md']) && $item['flexOrder']['md']!=''){
				$flexChildCss .= '@media (max-width: 1024px) {'.$childSele.'{ order : '.$item['flexOrder']['md'].' } }';
			}

			// moblie Css
			if( isset($item['flexShrink']['xs']) && $item['flexShrink']['xs']!='' ){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-shrink : '.$item['flexShrink']['xs'].' } }';
			}else if( isset($item['flexShrink']['sm']) && $item['flexShrink']['sm']!='' ){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-shrink : '.$item['flexShrink']['sm'].' } }';
			} else if( isset($item['flexShrink']['md']) && $item['flexShrink']['md']!='' ){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-shrink : '.$item['flexShrink']['md'].' } }';
			}

			if( isset($item['flexGrow']['xs']) && $item['flexGrow']['xs']!='' ){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-grow : '.$item['flexGrow']['xs'].' } }';
			}else if(isset($item['flexGrow']['sm']) && $item['flexGrow']['sm']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-grow : '.$item['flexGrow']['sm'].' } }';
			} else if(isset($item['flexGrow']['md']) && $item['flexGrow']['md']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-grow : '.$item['flexGrow']['md'].' } }';
			}

			if(isset($item['flexBasis']['xs']) && $item['flexBasis']['xs']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-basis : '.$item['flexBasis']['xs'].$item['flexBasis']['unit'].' } }';
			}else if(isset($item['flexBasis']['sm']) && $item['flexBasis']['sm']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-basis : '.$item['flexBasis']['sm'].$item['flexBasis']['unit'].' } }';
			}else if(isset($item['flexBasis']['md']) && $item['flexBasis']['md']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ flex-basis : '.$item['flexBasis']['md'].$item['flexBasis']['unit'].' } }';
			}

			if(isset($item['flexOrder']['xs']) && $item['flexOrder']['xs']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ order : '.$item['flexOrder']['xs'].' } }';
			}else if(isset($item['flexOrder']['sm']) && $item['flexOrder']['sm']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ order : '.$item['flexOrder']['sm'].' } }';
			}else if(isset($item['flexOrder']['md']) && $item['flexOrder']['md']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ order : '.$item['flexOrder']['md'].' } }';
			}

			if( isset($item['flexselfAlign']['xs']) && $item['flexselfAlign']['xs']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ align-self: '.$item['flexselfAlign']['xs'].' } }';
			}else if(isset($item['flexselfAlign']['sm']) && $item['flexselfAlign']['sm']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ align-self: '.$item['flexselfAlign']['sm'].' } }';
			}else if(isset($item['flexselfAlign']['md']) && $item['flexselfAlign']['md']!=''){
				$flexChildCss .= '@media (max-width: 767px) {'.$childSele.'{ align-self: '.$item['flexselfAlign']['md'].' } }';
			}
		}
		return $flexChildCss;
	}
}

Tp_Blocks_Helper::get_instance();