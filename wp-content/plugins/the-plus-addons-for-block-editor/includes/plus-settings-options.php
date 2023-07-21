<?php 
/**
 * TPGB Gutenberg Settings Options
 * @since 1.0.0
 *
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * White Label Content
 * @since 1.0.0
 */
function tpgb_free_white_label_content(){
	echo '<div class="tp-pro-note-title"><p style="margin-bottom:50px;">'.esc_html__('White Label our plugin and setup client\'s branding all around. You can update name, description, Icon and even hide the menu from dashboard. Get our pro version to have access of this feature.','tpgb').'</p></div>
		<div style="text-align:center;">
			<img style="width:55%;" src="'.esc_url(TPGB_URL .'assets/images/white-lable.png').'" alt="'.esc_attr__('White Lable','tpgb').'" class="panel-plus-white-lable" />
		</div>';
	/*echo '<div class="tp-pro-note-link"><a href="'.esc_url('theplusblocks.com/free-vs-pro/').'" target="_blank">'.esc_html__('Compare Free vs Pro','tpgb').'</a></div>';*/
}
add_action('tpgb_free_notice_white_label', 'tpgb_free_white_label_content' );

/**
 * Activate Content
 * @since 1.1.2
 */
function tpgb_activate_content(){
	echo '<div class="tp-pro-note-title"><p style="margin-bottom:40px;">'.esc_html__('Upgrade to Pro version to get lots more Features and Lot more.','tpgb').'</p></div>
		<div style="text-align:center;">
			<img style="width:55%;" src="'.esc_url(TPGB_URL .'assets/images/activate.png').'" alt="'.esc_attr__('Activate','tpgb').'" class="panel-plus-activate" />
		</div>';
	/*echo '<div class="tp-pro-note-link"><a href="'.esc_url('theplusblocks.com/free-vs-pro/').'" target="_blank">'.esc_html__('Compare Free vs Pro','tpgb').'</a></div>';*/
}
add_action('tpgb_notice_activate', 'tpgb_activate_content' );

/**
 * Rollback Content
 *  @since 1.3.0
 */
function tpgb_rollback_render_content_func(){
	$dropdown_version = '<select class="tpgb-rollback-list">';

	foreach ( Tpgb_Rollback::get_rollback_versions() as $version ) {
		$dropdown_version .= '<option value="'.esc_attr($version).'">'.esc_html($version).'</option>';
	}
	$dropdown_version .= '</select>';

	echo '<div class="tpgb-rollback-wrapper">';
		echo '<div class="tpgb-rollback-inner">';
			echo '<h2>'.esc_html__('Rollback to Previous Version','tpgb').'</h2>';
			echo '<table class="form-table">
			<tbody>
				<tr class="tpgb_rb_tr">
					<th scope="row">'.esc_html__('Rollback Free Version','tpgb').'</th>
					<td>
						<div id="tpgb-rb-id">
						<div class="tpgb-rb-content">
						'.sprintf(
							$dropdown_version . '<a  data-rv-text="' . esc_html__( 'Reinstall', 'tpgb' ) . ' v{TPGB_VERSION}" href="#" data-rv-url="%s" class="button tpgb-rollback-button">%s</a>',
							wp_nonce_url( admin_url( 'admin-post.php?action=tpgb_rollback&version=TPGB_VERSION' ), 'tpgb_rollback' ),
							__( 'Reinstall', 'tpgb' )
						).'</div>
							<p class="rollback-description" style="color:red">'. esc_html__( 'Note : Please take a backup of your Database and/or Complete Website before rollback.', 'tpgb' ) .'</p>
						</div>
					</td>
				</tr>
			</tbody>
			</table>';
		echo '</div>';
	echo '</div>';
}
add_action('tpgb_rollback_render_content', 'tpgb_rollback_render_content_func');

class Tpgb_Gutenberg_Settings_Options {
	
	/**
     * Option key, and option page slug
     * @var string V1.0.0
     */
    private $key = 'tpgb_gutenberg_options';
	
	/**
     * Array of meta boxes/fields
     * @var array
     */
    protected $option_metabox = array();
    
	protected $fields = array();
	
	/**
     * Setting Name/Title
     * @var string
     */
    protected $setting_name = '';
	
	/**
     * Options Page hook
     * @var string
     */
    protected $options_page = '';
    protected $options_pages = array();
    protected $block_lists = [];
	
	/**
     * Constructor
     * @since 1.0.0
     */
    public function __construct()
    {
		if( is_admin() ){
			add_action( 'admin_enqueue_scripts', [ $this,'tpgb_options_scripts'] );
			
			if(defined('TPGBP_VERSION')){
				$options = get_option( 'tpgb_white_label' );
				$this->setting_name = (!empty($options['tpgb_plugin_name'])) ? $options['tpgb_plugin_name'] : __('The Plus Settings','tpgb');
			}else{
				$this->setting_name = esc_html__('The Plus Settings', 'tpgb');
			}
		
			require_once TPGB_PATH.'includes/metabox/cmb2-conditionals.php';
			// Set our CMB fields
			$this->fields = array();
			$this->block_listout();
			add_action( 'admin_post_tpgb_blocks_opts_save', array( $this,'tpgb_blocks_opts_save_action') );
			add_action('wp_ajax_tpgb_block_search', array($this, 'tpgb_block_search'));
			
			add_action( 'wp_ajax_tpgb_is_block_used_not', array( $this, 'tpgb_is_block_used_not_fun' ) );
			add_action( 'wp_ajax_tpgb_unused_disable_block', array( $this, 'tpgb_disable_unsed_block_fun' ) );
			
			add_action( 'admin_post_tpgb_default_blocks_opts_save', array( $this,'tpgb_default_blocks_opts_save_action') );

			add_action( 'wp_ajax_tpgb_performance_opt_cache', array( $this,'tpgb_performance_opt_cache_save_action') );
			add_filter( 'admin_body_class', array( $this,'tpgb_performance_admin_classes' ) );
		}
    }
	
	/*
	 * Performance Cache Data in body class
	 * @since 1.4.0
	 */
	public function tpgb_performance_admin_classes( $classes ){
		
		if(is_admin() && function_exists('get_current_screen')){
			$my_current_screen = get_current_screen();

			if ( isset( $my_current_screen->base ) && 'the-plus-settings_page_tpgb_performance' === $my_current_screen->base ) {
				$cacheVal = get_option('tpgb_performance_cache');
				if( !empty($cacheVal) ){
					$classes .= ' perf-'.$cacheVal.' ';
				}
				$delayVal = get_option('tpgb_delay_css_js');
				if( $delayVal != '' ){
					$classes .= ' perf-delay-'.$delayVal.' ';
				}
				$deferVal = get_option('tpgb_defer_css_js');
				if( $deferVal != '' ){
					$classes .= ' perf-defer-'.$deferVal.' ';
				}
			}
		}
	 
		return $classes;
	}

	/**
     * load scripts
     * @since 1.0.0
     */
	public function tpgb_options_scripts() {
		wp_enqueue_script( 'cmb2-conditionals', TPGB_URL .'includes/metabox/cmb2-conditionals.js', array() );
	}
	
	/**
     * Initiate our hooks
     * @since 1.0.0
     */
	public function hooks() {
		if( is_admin() ){
       		add_action('admin_init', array( $this,'init' ) );
        	add_action('admin_menu', array( $this, 'add_options_page' ));
		}
    }
	
	/**
     * Register our setting to WP
     * @since  1.0.0
     */
    public function init()
    {
        $option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
            register_setting($option_tab['id'], $option_tab['id']);
        }
    }
	
	/**
     * Add menu options page
     * @since 1.0.0
     */
    public function add_options_page()
    {
		 $option_tabs = self::option_fields();
		
		foreach ($option_tabs as $index => $option_tab) {
			if($index == 0){
				$this->options_pages[] = add_menu_page($this->setting_name, $this->setting_name, 'manage_options', $option_tab['id'], array(
					$this,
					'admin_page_display'
				),'dashicons-tpgb-plus-settings');
			
				add_submenu_page($option_tabs[0]['id'], $this->setting_name, $option_tab['title'], 'manage_options', $option_tab['id'], array(
					$this,
					'admin_page_display'
				));
			}else{
				if(isset($option_tabs) && $option_tab['id'] != "tpgb_white_label" && $option_tab['id'] != "tpgb_activate"){
					$this->options_pages[] = add_submenu_page($option_tabs[0]['id'], $this->setting_name, $option_tab['title'], 'manage_options', $option_tab['id'], array(
						$this,
						'admin_page_display'
					));
					if($option_tab['title'] =='Plus Blocks'){
						add_submenu_page(
							$option_tabs[0]['id'],
							$this->setting_name,
							esc_html__( 'Reusable Blocks', 'tpgb' ),
							'edit_posts',
							'edit.php?post_type=wp_block',
							''
						);
					}
				}else{
					$label_options=get_option( 'tpgb_white_label' );	
					if( ((empty($label_options['tpgb_hidden_label']) || $label_options['tpgb_hidden_label']!='on') && ($option_tab['id'] == "tpgb_white_label" || $option_tab['id'] == "tpgb_activate")) || !defined('TPGBP_VERSION')){
						$this->options_pages[] = add_submenu_page($option_tabs[0]['id'], $this->setting_name, $option_tab['title'], 'manage_options', $option_tab['id'], array(
							$this,
							'admin_page_display'
						));
					}
				}
			}
		}
    }
	
	/*
	 * Save Performance Cache Option 
	 * @since 1.4.0
	 */
	public function tpgb_performance_opt_cache_save_action(){
		check_ajax_referer('tpgb-addons', 'security');
		
		if((isset($_POST['perf_caching']) && !empty($_POST['perf_caching'])) || isset($_POST['delay_js']) || isset($_POST['defer_js'])){
			$action_page = 'tpgb_performance_cache';
			$perf_caching = wp_unslash( sanitize_text_field( $_POST['perf_caching'] ) );
			if ( FALSE === get_option($action_page) ){
				add_option( $action_page, $perf_caching );
			}else{
				update_option( $action_page, $perf_caching );
			}
			
			$action_page = 'tpgb_delay_css_js';
			$delay_js = wp_unslash( sanitize_text_field( $_POST['delay_js'] ) );
			if ( FALSE === get_option($action_page) ){
				add_option( $action_page, $delay_js );
			}else{
				update_option( $action_page, $delay_js );
			}
			$action_page = 'tpgb_defer_css_js';
			$defer_js = wp_unslash( sanitize_text_field( $_POST['defer_js'] ) );
			if ( FALSE === get_option($action_page) ){
				add_option( $action_page, $defer_js );
			}else{
				update_option( $action_page, $defer_js );
			}
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/*
	 * Save Default gutenberg blocks 
	 * @since 1.4.4
	 */
	public function tpgb_default_blocks_opts_save_action(){
		$action_page = 'tpgb_default_load_blocks';
		
		if(isset($_POST["submit-key"]) && !empty($_POST["submit-key"]) && $_POST["submit-key"]=='Save'){
			
			if ( ! isset( $_POST['nonce_tpgb_default_load_blocks'] ) || ! wp_verify_nonce( sanitize_key($_POST['nonce_tpgb_default_load_blocks']), 'nonce_tpgb_default_blocks_action' ) ) {
			   wp_redirect( esc_url(admin_url('admin.php?page='.$action_page)) );
			} else {
				if ( FALSE === get_option($action_page) ){
					$default_value = ['enable_default_blocks' => ''];
					if(isset($_POST['enable_default_blocks']) && !empty($_POST['enable_default_blocks'])){
						if(is_array($_POST['enable_default_blocks'])){
							$default_value['enable_default_blocks'] = map_deep( wp_unslash( $_POST['enable_default_blocks'] ), 'sanitize_text_field' );
						}else{
							$default_value['enable_default_blocks'] = sanitize_text_field($_POST['enable_default_blocks']);
						}
						if(in_array('core/navigation', $default_value['enable_default_blocks'])){
							$default_value['enable_default_blocks'][] = 'core/navigation-link';
							$default_value['enable_default_blocks'][] = 'core/navigation-submenu';
						}
						if(in_array('core/social-links', $default_value['enable_default_blocks'])){
							$default_value['enable_default_blocks'][] = 'core/social-link';
						}
					}
					add_option($action_page,$default_value);
					wp_redirect( esc_url(admin_url('admin.php?page='.$action_page)) );
				}else{
					$gutenberg_block = get_option($action_page);
					if(isset($_POST['enable_default_blocks']) && !empty($_POST['enable_default_blocks'])){
						if(is_array($_POST['enable_default_blocks'])){
							$gutenberg_block = array('enable_default_blocks' => map_deep( wp_unslash( $_POST['enable_default_blocks'] ), 'sanitize_text_field' ));
						}else{
							$gutenberg_block = array('enable_default_blocks' => sanitize_text_field($_POST['enable_default_blocks']) );
						}
						if(in_array('core/navigation', $gutenberg_block['enable_default_blocks'])){
							$gutenberg_block['enable_default_blocks'][] = 'core/navigation-link';
							$gutenberg_block['enable_default_blocks'][] = 'core/navigation-submenu';
						}
						if(in_array('core/social-links', $gutenberg_block['enable_default_blocks'])){
							$gutenberg_block['enable_default_blocks'][] = 'core/social-link';
						}
						update_option( $action_page, $gutenberg_block );
						wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
					}else if(empty($_POST['enable_default_blocks'])){
						$gutenberg_block = array('enable_default_blocks' => '');
						update_option( $action_page, $gutenberg_block );
						wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
					}else{
						wp_redirect(esc_url( admin_url('admin.php?page='.$action_page) ) );
					}
				}
			}
		}else{
			wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
		}
	}

	public function tpgb_blocks_opts_save_action() {
		$action_page = 'tpgb_normal_blocks_opts';
		if(isset($_POST["submit-key"]) && !empty($_POST["submit-key"]) && $_POST["submit-key"]=='Save'){
			
			if ( ! isset( $_POST['nonce_tpgb_normal_blocks_opts'] ) || ! wp_verify_nonce( sanitize_key($_POST['nonce_tpgb_normal_blocks_opts']), 'nonce_tpgb_normal_blocks_action' ) ) {
			   wp_redirect( esc_url(admin_url('admin.php?page='.$action_page)) );
			} else {
			Tpgb_Library()->remove_backend_dir_files();
				if ( FALSE === get_option($action_page) ){
					$default_value = array('enable_normal_blocks' => '');
					add_option($action_page,$default_value);
					wp_redirect( esc_url(admin_url('admin.php?page=tpgb_normal_blocks_opts')) );
				}
				else{
					if(isset($_POST['enable_normal_blocks']) && !empty($_POST['enable_normal_blocks'])){
						if(is_array($_POST['enable_normal_blocks'])){
							$update_value = array('enable_normal_blocks' => map_deep( wp_unslash( $_POST['enable_normal_blocks'] ), 'sanitize_text_field' ));
						}else{
							$update_value = array('enable_normal_blocks' => sanitize_text_field($_POST['enable_normal_blocks']) );
						}
						update_option( $action_page, $update_value );
						wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
					}else if(empty($_POST['enable_normal_blocks'])){
						$update_value = array('enable_normal_blocks' => '');
						update_option( $action_page, $update_value );
						wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
					}else{
						wp_redirect(esc_url( admin_url('admin.php?page='.$action_page) ) );
					}
				}
			}
			
		}else{
			wp_redirect( esc_url( admin_url('admin.php?page='.$action_page) ) );
		}
	}
	
	public function block_listout(){
		$this->block_lists = [
				'tp-accordion' => [
					'label' => esc_html__('Accordion','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/accordion/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31 6H5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM5 4a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H5ZM31 21H5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1ZM5 19a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3H5Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.293 8.293a1 1 0 0 1 1.414 0L10 10.586l2.294-2.293a1 1 0 0 1 1.414 1.414l-3.001 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 0 1 .001-1.414ZM6.293 23.293a1 1 0 0 1 1.414 0L10 25.586l2.294-2.293a1 1 0 1 1 1.414 1.414l-3.001 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 0 1 .001-1.414Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['accordion', 'tabs', 'toggle', 'faq', 'collapse', 'show hide content', 'Tiles'],
				],
				'tp-advanced-buttons' => [
					'label' => esc_html__('Advanced Buttons', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/advanced-button/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M17 7a1 1 0 0 0-1 1v7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h23a2 2 0 0 0 2-2v-7h4a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H17Zm10 12H17a1 1 0 0 1-1-1v-1H4v9h23v-7Zm-9-2V9h14v8H18ZM7 22a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Zm14-10a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2h-8Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27 7h4l-1 2h-4l1-2Zm-16 8h4l-1 2h-4l1-2Zm20 4 1-2h-4l-1 2h4ZM19 7h3l-1 2h-3l1-2ZM8 15H5l-1 2h3l1-2Zm12 2h3l-1 2h-3l1-2ZM9 28l1-2H6l-1 2h4Zm4 0 1-2h4l-1 2h-4Zm13-2h-4l-1 2h4l1-2ZM2 19h2v4H2v-4Zm16-8h-2v4h2v-4Zm14 0h2v4h-2v-4Zm-3 10h-2v4h2v-4Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Button', 'CTA', 'link', 'creative button', 'Call to action', 'Marketing Button'],
				],
				'tp-advanced-chart' => [
					'label' => esc_html__('Advanced Chart', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/advanced-charts/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 4a1 1 0 0 1 1 1v23a2 2 0 0 0 2 2h27a1 1 0 1 1 0 2H6a4 4 0 0 1-4-4V5a1 1 0 0 1 1-1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M28.673 10.26a1 1 0 0 1 .067 1.413l-10 11a1 1 0 0 1-1.412.067l-4.614-4.195-4.366 6.985a1 1 0 0 1-1.696-1.06l5-8a1 1 0 0 1 1.52-.21l4.76 4.327 9.328-10.26a1 1 0 0 1 1.413-.067Z" fill="#1DD8A7"/></svg>',
				],
				'tp-adv-typo' => [
					'label' => esc_html__('Advanced Typography', 'tpgb'),
					'demoUrl' => '',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 31a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 19a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM30 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM30 31a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM30 19a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM18 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM18 31a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M11 12a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-1h-4v10h1a1 1 0 1 1 0 2h-4a1 1 0 1 1 0-2h1V13h-4v1a1 1 0 1 1-2 0v-2Z" fill="#1DD8A7"/><path d="M29 22h2v4h-2v-4ZM29 10h2v4h-2v-4ZM5 10h2v4H5v-4ZM5 22h2v4H5v-4ZM14 29v2h-4v-2h4ZM14 5v2h-4V5h4ZM26 29v2h-4v-2h4ZM26 5v2h-4V5h4Z" fill="#5900E7"/></svg>',
					'keyword' => ['adv','text','typo'],
				],
				'tp-animated-service-boxes' => [
					'label' => esc_html__('Animated Service Boxes', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/animated-service-boxes/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.085 21.591c-.59 1.12-.897 2.397-1.02 3.412C16 25.551 15.553 26 15 26c-.552 0-.99-.45-1.065-.998-.207-1.5-.88-2.421-1.657-3C11.292 21.268 10.024 21 9 21a1 1 0 1 1 0-2c1.31 0 3.041.332 4.472 1.398.505.376.96.834 1.339 1.38.145-.378.312-.754.504-1.12C16.325 18.743 18.11 17 21 17a1 1 0 1 1 0 2c-1.91 0-3.125 1.091-3.915 2.591Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6 10a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V12a2 2 0 0 0-2-2h-6.452V8H30a4 4 0 0 1 4 4v16a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V12a4 4 0 0 1 4-4h17.5v2H6Z" fill="#5900E7"/></svg>',
				],
				'tp-audio-player' => [
					'label' => esc_html__('Audio Player','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/audio-player/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31.6 2.2a1 1 0 0 1 .4.8v8.075a1 1 0 0 1-.721.96l-13.713 3.976-3.79 1.092a1 1 0 0 1-1.277-.961V8.067a1 1 0 0 1 .722-.96l17.5-5.068a1 1 0 0 1 .88.162ZM14.5 8.819v5.995l2.512-.724L30 10.324V4.33L14.499 8.818Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M31 10.075a1 1 0 0 1 1 1v13.458a1 1 0 1 1-2 0V11.075a1 1 0 0 1 1-1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M26.75 21.308c-1.8 0-3.249 1.45-3.249 3.225 0 1.776 1.45 3.225 3.25 3.225S30 26.31 30 24.533c0-1.775-1.45-3.225-3.25-3.225Zm-5.249 3.225c0-2.89 2.356-5.225 5.25-5.225 2.893 0 5.249 2.334 5.249 5.225 0 2.891-2.356 5.225-5.25 5.225-2.893 0-5.249-2.334-5.249-5.225Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M13.499 14.317a1 1 0 0 1 1 1v13.458a1 1 0 1 1-2 0V15.317a1 1 0 0 1 1-1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9.25 25.55C7.45 25.55 6 27 6 28.775 6 30.551 7.45 32 9.25 32s3.249-1.45 3.249-3.225c0-1.776-1.45-3.225-3.25-3.225ZM4 28.775c0-2.891 2.356-5.225 5.25-5.225 2.893 0 5.249 2.334 5.249 5.225 0 2.891-2.356 5.225-5.25 5.225C6.357 34 4 31.666 4 28.775Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['audio player', 'music player'],
				],
				'tp-before-after' => [
					'label' => esc_html__('Before After', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/before-after/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M19 4a1 1 0 1 0-2 0v28a1 1 0 1 0 2 0V4ZM6 6a3 3 0 0 0-3 3v18a3 3 0 0 0 3 3h8a1 1 0 1 0 0-2H6a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h8a1 1 0 1 0 0-2H6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M23 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0 22a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm2-21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm1 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm2-21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm1 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm2-21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm1 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm0-18a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm1 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm1 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" fill="#1DD8A7"/></svg>'
				],
				'tp-blockquote' => [
					'label' => esc_html__('Blockquote','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/blockquote/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.855 3.998c.074.55-.312 1.052-.855 1.163a14.983 14.983 0 0 0-8.26 4.812 15.121 15.121 0 0 0-3.7 8.857c-.04.55-.487.996-1.04.992-.552-.004-1-.455-.963-1.006A17.138 17.138 0 0 1 7.239 8.645a16.98 16.98 0 0 1 9.49-5.48c.544-.107 1.052.282 1.126.833Z" fill="#1DD8A7"/><rect x="4" y="17.92" width="13" height="13.08" rx="2" stroke="#5900E7" stroke-width="2"/><path fill-rule="evenodd" clip-rule="evenodd" d="M33.855 3.998c.074.55-.312 1.052-.855 1.163a14.983 14.983 0 0 0-8.26 4.812 15.12 15.12 0 0 0-3.7 8.857c-.04.55-.487.996-1.04.992-.552-.004-1-.455-.963-1.006a17.138 17.138 0 0 1 4.202-10.171 16.98 16.98 0 0 1 9.49-5.48c.544-.107 1.052.282 1.126.833Z" fill="#1DD8A7"/><rect x="20" y="17.92" width="13" height="13.08" rx="2" stroke="#5900E7" stroke-width="2"/></svg>',
					'keyword' => ['blockquote', 'Block Quotation', 'Citation', 'Pull Quotes'],
				],
				'tp-breadcrumbs' => [
					'label' => esc_html__('Breadcrumbs','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/breadcrumb-bar/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M24.24 7.042a1.002 1.002 0 0 1 1.246.67l2.918 9.714c.113.374.113.774 0 1.149l-2.918 9.713a1.002 1.002 0 0 1-1.918-.575L26.486 18l-2.918-9.712a1 1 0 0 1 .671-1.246ZM29.751 7.042a1.002 1.002 0 0 1 1.247.67l2.918 9.714c.112.374.112.774 0 1.149l-2.918 9.713a1.002 1.002 0 0 1-1.918-.575L31.997 18 29.08 8.288a1 1 0 0 1 .671-1.246Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.899 9H5a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h11.899a1 1 0 0 0 .94-.658l2.909-8a1 1 0 0 0 0-.684l-2.91-8a1 1 0 0 0-.94-.658ZM5 7a3 3 0 0 0-3 3v16a3 3 0 0 0 3 3h11.899a3 3 0 0 0 2.82-1.975l2.908-8a3 3 0 0 0 0-2.05l-2.909-8A3 3 0 0 0 16.898 7H5Z" fill="#5900E7"/></svg>',
					'keyword' => ['breadcrumbs bar', 'breadcrumb trail', 'navigation', 'site navigation', 'breadcrumb navigation']
				],
				'tp-button' => [
					'label' => esc_html__('Tp Button','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/button/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M2 8.03C2 5.805 3.79 4 6 4h24c2.21 0 4 1.804 4 4.03v8.061c0 2.226-1.79 4.03-4 4.03h-4l-1-2.014h5c1.105 0 2-.903 2-2.016v-8.06a2.008 2.008 0 0 0-2-2.016H6c-1.105 0-2 .902-2 2.015v8.061c0 1.113.895 2.016 2 2.016h11v2.015H6c-2.21 0-4-1.805-4-4.03V8.03Z" fill="#1DD8A7"/><rect x="9" y="11.053" width="18" height="2.008" rx="1.004" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M15.114 17.8c-.549-1.612.982-3.154 2.581-2.602l12.72 4.395c1.89.652 1.81 3.372-.115 3.91l-5.435 1.521-1.51 5.476c-.534 1.939-3.233 2.02-3.88.116L15.114 17.8Zm2.904-.325a.51.51 0 0 0-.645.65l3.493 10.265a.507.507 0 0 0 .97-.03l1.378-5 4.963-1.388c.48-.135.5-.815.028-.978l-10.186-3.52Z" fill="#5900E7"/></svg>',
					'keyword' => ['Button', 'CTA', 'link', 'creative button', 'Call to action', 'Marketing Button']
				],
				'tp-anything-carousel' => [
					'label' => esc_html__('Carousel Anything','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/carousel-anything/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M30 8h-3V6h3a4 4 0 0 1 4 4v12a4 4 0 0 1-4 4h-3v-2h3a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2ZM6 8h3V6H6a4 4 0 0 0-4 4v12a4 4 0 0 0 4 4h3v-2H6a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25 6H11a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM11 4a3 3 0 0 0-3 3v18a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H11Z" fill="#5900E7"/><rect x="11" y="22" width="14" height="2" rx="1" fill="#1DD8A7"/><circle cx="18" cy="31" r="1" fill="#5900E7"/><circle cx="25" cy="31" r="1" fill="#1DD8A7"/><circle cx="11" cy="31" r="1" fill="#1DD8A7"/></svg>',
					'keyword' => ['carousel anything', 'slider', 'slideshow'],
				],
				'tp-carousel-remote' => [
					'label' => esc_html__('Carousel Remote','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/carousel-remote/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><circle cx="13" cy="31" r="1" fill="#1DD8A7"/><circle cx="23" cy="31" r="1" fill="#1DD8A7"/><circle cx="18" cy="31" r="1" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25 6H11a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM11 4a3 3 0 0 0-3 3v18a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H11Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M29.295 15.707a1 1 0 0 1 1.413-1.414l2.995 2.992.008.008a1 1 0 0 1 0 1.415l-3.004 3a1 1 0 1 1-1.414-1.415L31.59 18l-2.294-2.293ZM6.709 15.707a1 1 0 1 0-1.414-1.414L2.3 17.285a1 1 0 0 0-.007 1.422l3.003 3a1 1 0 1 0 1.413-1.415L4.415 18l2.294-2.293Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['carousel remote', 'slider controller'],
				],
				'tp-circle-menu' => [
					'label' => esc_html__('Circle Menu', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/circle-menu/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 29c6.075 0 11-4.925 11-11S24.075 7 18 7 7 11.925 7 18s4.925 11 11 11Zm0 2c7.18 0 13-5.82 13-13S25.18 5 18 5 5 10.82 5 18s5.82 13 13 13Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM6 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM18 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['circle menu', 'compact menu', 'mobile menu']
				],
				'tp-code-highlighter' => [
					'label' => esc_html__('Code Highlighter', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/source-code-syntax-highlighter',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M30 5H6a1 1 0 0 0-1 1v4h26V6a1 1 0 0 0-1-1Zm3 5V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3V10Zm-2 2H5v18a1 1 0 0 0 1 1h24a1 1 0 0 0 1-1V12Z" fill="#5900E7"/><rect x="7.5" y="6.5" width="3" height="1" rx=".5" fill="#D9D9D9" stroke="#1DD8A7"/><rect x="13.5" y="6.5" width="3" height="1" rx=".5" fill="#D9D9D9" stroke="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.2 21.4a1 1 0 0 1 1.4-.2l4 3a1 1 0 0 1-1.2 1.6l-4-3a1 1 0 0 1-.2-1.4Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.707 17.293a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0ZM28.8 21.4a1 1 0 0 0-1.4-.2l-4 3a1 1 0 0 0 1.2 1.6l4-3a1 1 0 0 0 .2-1.4Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M23.293 17.293a1 1 0 0 0 0 1.414l4 4a1 1 0 0 0 1.414-1.414l-4-4a1 1 0 0 0-1.414 0ZM21.272 15.405a1 1 0 0 1 .51 1.32l-4.549 10.27a1 1 0 1 1-1.828-.81l4.548-10.27a1 1 0 0 1 1.32-.51Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['prism', 'Source code beautifier', 'code Highlighter',  'syntax Highlighter', 'Custom Code', 'CSS', 'JS', 'PHP', 'HTML', 'React']
				],
				'tp-countdown' => [
					'label' => esc_html__('Countdown','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/countdown/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect width="2.007" height="9.031" rx="1.004" transform="matrix(-.70956 .70464 -.70956 -.70464 34 10.864)" fill="#1DD8A7"/><rect x="17.105" y="9.5" width="2.014" height="10" rx="1.007" fill="#1DD8A7"/><rect x="27.175" y="17.5" width="2" height="10.07" rx="1" transform="rotate(90 27.175 17.5)" fill="#1DD8A7"/><rect width="2.007" height="9.031" rx="1.004" transform="matrix(.70956 .70464 .70956 -.70464 2 10.864)" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.112 29.5c6.117 0 11.077-4.925 11.077-11s-4.96-11-11.077-11c-6.118 0-11.077 4.925-11.077 11s4.96 11 11.077 11Zm0 2c7.23 0 13.09-5.82 13.09-13s-5.86-13-13.09-13c-7.23 0-13.091 5.82-13.091 13s5.86 13 13.09 13Z" fill="#5900E7"/></svg>',
					'keyword' => ['Countdown', 'countdown timer', 'timer', 'Scarcity Countdown', 'Urgency Countdown', 'Event countdown', 'Sale Countdown', 'chronometer', 'stopwatch']
				],
				'tp-container' => [
					'label' => esc_html__('TP Container','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/countdown/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31 5H5v26h26V5ZM5 3a2 2 0 0 0-2 2v26a2 2 0 0 0 2 2h26a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9 15.002V15h.001l.002-.003L9 15V15h-.002l-.864.5A.995.995 0 0 1 8 15v-1h2v2H9a.996.996 0 0 1-.5-.134l.5-.864ZM26 16v-2h2v1a.996.996 0 0 1-.134.5l-.864-.5H27v-.001l-.003-.002L27 15H27v.002l.5.864A.996.996 0 0 1 27 16h-1Zm2-6h-2V8h1c.182 0 .353.049.5.134l-.5.864V9h-.001l-.002.003L27 9V9h.002l.864-.5A.996.996 0 0 1 28 9v1ZM10 8H9a.995.995 0 0 0-.5.134l.5.864V9h-.002l-.864-.5A.995.995 0 0 0 8 9v1h2V8ZM9 9h.001l.002.003L9 9V9Zm-1 4h2v-2H8v2Zm3-5v2h2V8h-2Zm3 0v2h2V8h-2Zm3 0v2h2V8h-2Zm3 0v2h2V8h-2Zm3 0v2h2V8h-2Zm5 3h-2v2h2v-2Zm-3 5v-2h-2v2h2Zm-3 0v-2h-2v2h2Zm-3 0v-2h-2v2h2Zm-3 0v-2h-2v2h2Zm-3 0v-2h-2v2h2ZM9 27.002V27h.001l.002-.003L9 27V27h-.002l-.864.5A.995.995 0 0 1 8 27v-1.167h2V26h.167v2H9a.995.995 0 0 1-.5-.134l.5-.864Zm5.833.998v-2H15v-.167h2V27a.996.996 0 0 1-.134.5l-.864-.5H16v-.001l-.003-.002L16 27H16v.002l.5.864A.996.996 0 0 1 16 28h-1.167ZM17 21.167h-2V21h-.167v-2H16c.182 0 .353.049.5.134l-.5.864V20h-.001l-.002.003L16 20V20h.002l.864-.5A.996.996 0 0 1 17 20v1.167ZM10.167 19H9a.995.995 0 0 0-.5.134l.5.864V20h-.002l-.864-.5A.995.995 0 0 0 8 20v1.167h2V21h.167v-2ZM9 20h.001l.002.003L9 20V20Zm-1 4.667h2v-2.334H8v2.334ZM11.333 19v2h2.334v-2h-2.334ZM17 22.333h-2v2.334h2v-2.334ZM13.667 28v-2h-2.334v2h2.334ZM20 27l-.001.002-.5.864A.996.996 0 0 0 20 28h1.167v-2H21v-.167h-2V27c0 .182.049.353.134.5l.864-.5H20ZM20 27v-.001l.003-.002L20 27H20Zm5.833 0v1H27a.996.996 0 0 0 .5-.134l-.5-.864V27h.002l.864.5A.996.996 0 0 0 28 27v-1.167h-2V26h-.167v1ZM27 27h-.001l-.002-.003L27 27V27Zm0-5.833h1V20a.996.996 0 0 0-.134-.5l-.864.5H27v-.002l.5-.864A.996.996 0 0 0 27 19h-1.167v2H26v.167h1ZM27 20v.001l-.003.002L27 20H27Zm-7-1h1.167v2H21v.167h-2V20c0-.182.049-.353.134-.5l.864.5H20v.001l.003.002L20 20H20v-.002l-.5-.864A.996.996 0 0 1 20 19Zm0 5.667h-1v-2.334h2v2.334h-1ZM22.333 20v-1h2.334v2h-2.334v-1ZM27 22.333h1v2.334h-2v-2.334h1ZM24.667 27v1h-2.334v-2h2.334v1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['container','flex-wrap','flex-based','full-width']
				],
				'tp-coupon-code' => [
					'label' => esc_html__('Coupon Code', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/coupon-code/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M11 9H4a2 2 0 0 0-2 2v2.5c1.422 0 4.267.9 4.267 4.5S3.422 22.5 2 22.5V25a2 2 0 0 0 2 2h7l1.5-1.5L14 27h18a2 2 0 0 0 2-2v-2.5c-1.422 0-4.267-.9-4.267-4.5s2.845-4.5 4.267-4.5V11a2 2 0 0 0-2-2H14l-1.5 1.5L11 9Zm3.828 2-1.267 1.268a1.5 1.5 0 0 1-2.122 0L10.172 11H4v.831a6.387 6.387 0 0 1 1.876.976c1.4 1.064 2.39 2.785 2.39 5.193 0 2.408-.99 4.13-2.39 5.193A6.387 6.387 0 0 1 4 24.169V25h6.172l1.267-1.268a1.5 1.5 0 0 1 2.122 0L14.828 25H32v-.831a6.387 6.387 0 0 1-1.876-.976c-1.4-1.064-2.39-2.785-2.39-5.193 0-2.408.99-4.13 2.39-5.193A6.387 6.387 0 0 1 32 11.831V11H14.828Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 14a1 1 0 0 1 1 1v1.5a1 1 0 1 1-2 0V15a1 1 0 0 1 1-1Zm0 4.5a1 1 0 0 1 1 1V21a1 1 0 1 1-2 0v-1.5a1 1 0 0 1 1-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Coupon Code', 'Promo Code', 'Offers' , 'Discounts', 'Sales', 'Copy Coupon Code']
				],
				'tp-creative-image' => [
					'label' => esc_html__('TP Image','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/creative-images/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31 6H5a1 1 0 0 0-1 1v22a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM5 4a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H5Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M21.106 12.553a1 1 0 0 1 1.801.026l6.5 14a1 1 0 0 1-1.814.842l-5.626-12.118-4.572 9.144a1 1 0 0 1-1.79 0l-2.685-5.37-5.062 8.438a1 1 0 0 1-1.715-1.03l6-10a1 1 0 0 1 1.752.068l2.605 5.211 4.606-9.211ZM10 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Creative image', 'Image', 'Animated Image', 'ScrollReveal', 'scrolling image', 'decorative image', 'image effect', 'Photo', 'Visual']
				],
				'tp-cta-banner' => [
					'label' => esc_html__('CTA Banner','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/cta-banner/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M2 7.934C2 5.761 3.79 4 6 4h24c2.21 0 4 1.761 4 3.934v5.914c0 2.173-1.79 3.934-4 3.934h-5v-1.967h5c1.105 0 2-.88 2-1.967V7.934c0-1.086-.895-1.967-2-1.967H6c-1.105 0-2 .88-2 1.967v5.914c0 1.087.895 1.967 2 1.967h10.5v1.967H6c-2.21 0-4-1.761-4-3.934V7.934Z" fill="#1DD8A7"/><rect x="20" y="7.917" width="10" height="3.917" rx="1.5" fill="#1DD8A7"/><rect x="6" y="8.896" width="12" height="1.959" rx=".979" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30.846 23.068 16.654 10.76l.21 18.625 4.615-4.808a4.029 4.029 0 0 1 2.737-1.23l6.63-.279ZM17.976 9.284c-1.299-1.127-3.341-.206-3.322 1.498l.21 18.625c.02 1.769 2.22 2.615 3.454 1.328l4.615-4.807a2.015 2.015 0 0 1 1.369-.615l6.63-.28c1.799-.076 2.588-2.269 1.237-3.44L17.977 9.284Z" fill="#5900E7"/></svg>',
					'keyword' => ['advertisement', 'banner', 'advertisement banner', 'ad manager', 'announcement', 'announcement banner']
				],
				'tp-data-table' => [
					'label' => esc_html__('Data Table','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/data-table/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31 6H5a1 1 0 0 0-1 1v22a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM5 4a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H5Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8 16v2h7v-2H8Zm-.5-2A1.5 1.5 0 0 0 6 15.5v3A1.5 1.5 0 0 0 7.5 20h8a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-8ZM21 16v2h7v-2h-7Zm-.5-2a1.5 1.5 0 0 0-1.5 1.5v3a1.5 1.5 0 0 0 1.5 1.5h8a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-8ZM21 24v2h7v-2h-7Zm-.5-2a1.5 1.5 0 0 0-1.5 1.5v3a1.5 1.5 0 0 0 1.5 1.5h8a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-8ZM8 24v2h7v-2H8Zm-.5-2A1.5 1.5 0 0 0 6 23.5v3A1.5 1.5 0 0 0 7.5 28h8a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-8Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Data table', 'datatable', 'grid', 'csv table', 'table', 'tabular layout', 'Table Showcase']
				],
				'tp-dark-mode' => [
					'label' => esc_html__('Dark Mode','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/dark-mode/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 32c7.732 0 14-6.268 14-14S25.732 4 18 4 4 10.268 4 18s6.268 14 14 14Zm0 2c8.837 0 16-7.163 16-16S26.837 2 18 2 2 9.163 2 18s7.163 16 16 16Z" fill="#5900E7"/><path d="M9 21.43a9.673 9.673 0 1 0 7.7-13.354A7.991 7.991 0 0 1 9 21.43Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['dark', 'light', 'darkmode', 'dual']
				],
				'tp-design-tool' => [
					'label' => esc_html__('Design Tool','tpgb'),
					'demoUrl' => '',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 2.985a1 1 0 0 0-1 1V14.99c0 .057.005.112.014.166A3.003 3.003 0 0 0 18 20.994a3 3 0 0 0 .986-5.837c.01-.054.014-.11.014-.166V3.985a1 1 0 0 0-1-1Zm1 15.007a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.264 3.008c.767-1.344 2.705-1.344 3.473 0l6.8 11.909c.406.709.314 1.527-.033 2.13-.53.921-1.207 2.569-1.79 4.452-.58 1.877-1.04 3.907-1.175 5.58-.082 1.025-.92 1.919-2.039 1.919h-7c-1.118 0-1.957-.894-2.04-1.919-.133-1.673-.594-3.703-1.174-5.58-.582-1.883-1.26-3.53-1.79-4.452-.347-.604-.439-1.421-.034-2.13l6.802-11.909ZM18 4.001l-6.801 11.908v.001l-.002.004a.102.102 0 0 0-.002.026c.002.028.011.068.035.109.636 1.105 1.364 2.91 1.967 4.86.604 1.955 1.108 4.138 1.257 6.01a.107.107 0 0 0 .03.068.047.047 0 0 0 .014.01h7.004a.047.047 0 0 0 .014-.01.107.107 0 0 0 .03-.068c.15-1.872.653-4.055 1.258-6.01.602-1.95 1.33-3.755 1.966-4.86a.246.246 0 0 0 .035-.11l-.002-.025-.001-.004L18 4Z" fill="#5900E7"/><rect x="9" y="28" width="18" height="5" rx="2" stroke="#5900E7" stroke-width="2"/></svg>',
					'keyword' => ['design','tool']
				],
				'tp-draw-svg' => [
					'label' => esc_html__('Draw SVG','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/draw-svg/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M16.08 11.71c.287-.443.896-.56 1.36-.26.464.298.607.9.32 1.342L5.2 32.198c-.288.443-.896.56-1.36.261-.464-.299-.608-.9-.321-1.343L16.08 11.71Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18 10.974c1.662 0 3-1.342 3-2.987A2.993 2.993 0 0 0 18 5c-1.662 0-3 1.342-3 2.987a2.993 2.993 0 0 0 3 2.987Zm0 2c2.761 0 5-2.233 5-4.987A4.993 4.993 0 0 0 18 3c-2.761 0-5 2.233-5 4.987a4.993 4.993 0 0 0 5 4.987Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.138 19.7c.374-.403 1.003-.425 1.431-.08a15.025 15.025 0 0 0 18.86.002c.429-.346 1.057-.324 1.431.08.376.405.353 1.04-.074 1.39a17.028 17.028 0 0 1-10.787 3.85 17.028 17.028 0 0 1-10.786-3.85c-.428-.35-.45-.986-.075-1.392Z" fill="#1DD8A7"/><path d="M19.889 11.588c-.287-.444-.896-.561-1.36-.263-.465.3-.608.901-.32 1.345L30.8 32.123c.287.444.896.561 1.36.263.464-.3.607-.901.32-1.345L19.889 11.588Z" fill="#5900E7"/></svg>',
					'keyword' => ['Draw SVG', 'Draw Icon', 'illustration', 'animated svg', 'animated icons', 'Lottie animations', 'Lottie files', 'effects', 'image effect']
				],
				'tp-dynamic-device' => [
					'label' => esc_html__('Dynamic Device','tpgb'),
					'demoUrl' => '',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
					<rect x="15" y="25" width="2" height="6" rx="1" fill="#1DD8A7"/>
					<rect x="21" y="29" width="2" height="10" rx="1" transform="rotate(90 21 29)" fill="#1DD8A7"/>
					<path fill-rule="evenodd" clip-rule="evenodd" d="M2 8.14286C2 6.40711 3.34315 5 5 5H26C27.6569 5 29 6.40711 29 8.14286V13H27V8.14286C27 7.56427 26.5523 7.09524 26 7.09524H5C4.44772 7.09524 4 7.56427 4 8.14286V23.8571C4 24.4357 4.44771 24.9048 5 24.9048H23V27H5C3.34315 27 2 25.5929 2 23.8571V8.14286Z" fill="#5900E7"/>
					<rect x="23" y="12" width="10" height="18" rx="1" stroke="#1DD8A7" stroke-width="2"/>
					</svg>',
					'keyword' => ['dynamic device', 'website mockups', 'portfolio', 'desktop view', 'tablet view', 'mobile view']
				],
				'tp-empty-space' => [
					'label' => esc_html__('TP Spacer','tpgb'),
					'demoUrl' => '',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 3a1 1 0 0 0-2 0v9a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V3a1 1 0 1 0-2 0v9a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V3Zm24 30a1 1 0 1 0 2 0v-9a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v9a1 1 0 1 0 2 0v-9a1 1 0 0 1 1-1h22a1 1 0 0 1 1 1v9Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8.5 17a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm6 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm11.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm-5.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Spacer', 'Divider', 'Spacing','empty space']
				],
				'tp-external-form-styler' => [
					'label' => esc_html__('External Form Styler','tpgb'),
					'demoUrl' => '#',
					'docUrl' => '#',
					'videoUrl' => '#',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M4.1 31.201c-.3 0-.7-.2-.9-.499-.3-.499-.1-1.098.3-1.397l12.6-7.487c.5-.3 1.1-.1 1.4.3.3.499.1 1.098-.3 1.397L4.6 31.102c-.2.1-.3.1-.5.1ZM32 31.201c-.2 0-.3 0-.5-.1l-12.7-7.486c-.5-.3-.6-.898-.4-1.397.3-.5.9-.6 1.4-.4l12.7 7.487c.5.3.6.898.4 1.397-.3.3-.6.5-.9.5Z" fill="#5900E7"/><path d="M18 24.114c-.2 0-.4 0-.5-.1L3.6 15.73c-.5-.3-.6-.898-.3-1.397.2-.5.8-.6 1.3-.3L18 22.018l13.5-7.986c.5-.3 1.1-.1 1.4.4.3.498.1 1.097-.4 1.397l-14 8.285c-.1-.1-.3 0-.5 0Z" fill="#1DD8A7"/><path d="M29.7 32H6.3c-1.1 0-2.2-.4-3-1.098-.8-.699-1.3-1.697-1.3-2.795V14.831c0-.4.1-.799.3-1.198.2-.3.5-.6.9-.899L17.5 4.15c.3-.2.7-.2 1 0l14.3 8.584c.4.2.7.5.9.899.2.4.3.798.3 1.198v13.276c0 .998-.5 2.096-1.3 2.795-.8.699-1.9 1.098-3 1.098ZM18 6.246 4.2 14.531c-.1 0-.1.1-.2.2V28.207c0 .499.2.898.6 1.297.5.3 1.1.5 1.7.5h23.3c.6 0 1.3-.2 1.7-.6.4-.399.6-.798.6-1.297V14.631c0-.1-.1-.1-.2-.2L18 6.246Z" fill="#5900E7"/></svg>',
					'keyword' => ['form', 'contect form', 'everest', 'gravity', 'wpform','Contact Form 7', 'contact form', 'form', 'feedback', 'subscribe', 'newsletter', 'contact us', 'custom form', 'popup form', 'cf7']
				],
				'tp-expand' => [
					'label' => esc_html__('Expand','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/expand/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.715 18.531V5c0-.552.443-1 .988-1h13.833c.545 0 .988.448.988 1v12.469L24.4 23H9.192l2.523-4.469ZM29.5 18l-3.668 6.496a.986.986 0 0 1-.858.504H7.49c-.758 0-1.234-.83-.857-1.496L9.739 18V5c0-1.657 1.327-3 2.964-3h13.833C28.173 2 29.5 3.343 29.5 5v13Z" fill="#5900E7"/><path d="M10.727 31a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM10.727 28a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM10.727 34a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM13.691 34a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.443 1 .988 1ZM16.655 34a.994.994 0 0 0 .989-1c0-.552-.443-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM19.62 34a.994.994 0 0 0 .988-1c0-.552-.443-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM22.584 34a.994.994 0 0 0 .988-1c0-.552-.443-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM25.548 34a.994.994 0 0 0 .988-1c0-.552-.443-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM28.512 34a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM28.512 31a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM28.512 28a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1ZM28.512 25a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1a.994.994 0 0 0-.988 1c0 .552.442 1 .988 1Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M19.387 14.046a2.064 2.064 0 0 1 .59-1.447 2.018 2.018 0 0 1 1.43-.599h2.14a.994.994 0 0 0 .988-1c0-.552-.442-1-.988-1h-2.14c-.526 0-1.046.105-1.53.308a3.992 3.992 0 0 0-1.297.878 4.048 4.048 0 0 0-.866 1.313c-.2.49-.303 1.016-.303 1.548v4.933c0 .552.442 1 .988 1a.994.994 0 0 0 .988-1v-4.934Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M14.97 15.5a1.008 1.008 0 0 0-.002 1.414l2.737 2.773a.98.98 0 0 0 1.398 0 1.009 1.009 0 0 0 0-1.414l-2.737-2.772a.98.98 0 0 0-1.397 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.706 19.688a.98.98 0 0 0 1.397-.001l2.737-2.773a1.008 1.008 0 0 0 0-1.414.98.98 0 0 0-1.398 0l-2.737 2.773a1.009 1.009 0 0 0 0 1.415Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Expand', 'read more', 'show hide content', 'Expand tabs', 'show more', 'toggle', 'Excerpt']
				],
				'tp-flipbox' => [
					'label' => esc_html__('Flipbox','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/flipbox/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M29 4 7 6a1 1 0 0 0-1 1v22a1 1 0 0 0 1 1l22 2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1ZM7 4a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3l22 2a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3L7 4Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.998 11.833 12 9.338 9.437 9l.443.802c-.507.398-.933.9-1.25 1.477A5.102 5.102 0 0 0 8 13.74c0 .864.217 1.712.63 2.46A4.814 4.814 0 0 0 10.347 18l.783-1.42a3.21 3.21 0 0 1-1.146-1.2 3.401 3.401 0 0 1-.42-1.64c0-.576.145-1.142.42-1.64a3.25 3.25 0 0 1 .686-.862l.327.595ZM14.002 16.167 13 18.662l2.563.338-.443-.802c.507-.398.933-.9 1.25-1.477.413-.748.63-1.597.63-2.46 0-.864-.217-1.712-.63-2.46A4.814 4.814 0 0 0 14.653 10l-.783 1.42a3.21 3.21 0 0 1 1.146 1.2c.275.5.42 1.065.42 1.64 0 .576-.145 1.142-.42 1.64a3.25 3.25 0 0 1-.686.862l-.327-.595Z" fill="#1DD8A7"/><path d="M8 26a1 1 0 0 1 1-1l18 1a1 1 0 1 1 0 2L9 27a1 1 0 0 1-1-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['flipbox', 'flip', 'flip image', 'flip card', 'action box', 'flipbox 3D', 'card'],
				],
				'tp-google-map' => [
					'label' => esc_html__('Google Map','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/google-maps/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M23.207 12.793a1 1 0 0 1 0 1.414l-17.5 17.5a1 1 0 0 1-1.414-1.414l17.5-17.5a1 1 0 0 1 1.414 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M28.707 31.707a1 1 0 0 1-1.414 0l-11-11a1 1 0 0 1 1.414-1.414l11 11a1 1 0 0 1 0 1.414Z" fill="#1DD8A7"/><path d="M16 14.629C16 17.767 14.014 20 11.082 20 8.27 20 6 17.543 6 14.5S8.27 9 11.082 9c1.369 0 2.52.543 3.408 1.44l-1.383 1.439c-1.81-1.89-5.175-.47-5.175 2.621 0 1.918 1.416 3.473 3.15 3.473 2.012 0 2.766-1.561 2.885-2.37h-2.885V13.71h4.838c.047.282.08.553.08.919ZM28.5 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7 8a2 2 0 0 0-2 2v19a2 2 0 0 0 2 2h19a2 2 0 0 0 2-2V15.5h2V29a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V10a4 4 0 0 1 4-4h14.5v2H7Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M22.35 4.268C23.387 3.488 24.758 3 26.5 3c1.742 0 3.113.488 4.15 1.268 1.025.77 1.663 1.78 2.042 2.742.705 1.794.074 3.635-.634 5.03l-4.666 9.201a1 1 0 0 1-1.784 0l-4.666-9.201c-.707-1.395-1.34-3.236-.634-5.03.379-.963 1.017-1.971 2.042-2.742Zm1.273 1.55c-.66.497-1.107 1.173-1.387 1.886-.392.997-.094 2.188.56 3.475l3.704 7.306 3.705-7.306c.653-1.287.95-2.478.559-3.475-.28-.713-.726-1.39-1.387-1.886-.647-.487-1.56-.847-2.877-.847-1.316 0-2.23.36-2.877.847Z" fill="#5900E7"/></svg>',
					'keyword' => ['Map', 'Maps', 'Google Maps', 'g maps', 'location map', 'map iframe', 'embed']
				],
				'tp-heading-animation' => [
					'label' => esc_html__('Heading Animation','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/heading-animation/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="4" y="32" width="28" height="2" rx="1" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M4 3a1 1 0 0 1 1-1h8a1 1 0 1 1 0 2h-3v14a7.999 7.999 0 1 0 16 0V4h-3a1 1 0 1 1 0-2h8a1 1 0 1 1 0 2h-3v14a10 10 0 0 1-20 0V4H5a1 1 0 0 1-1-1Z" fill="#5900E7"/></svg>',
					'keyword' => ['Heading Animation', 'Animated Heading', 'Animation Text', 'Animated Text', 'Text Animation']
				],
				'tp-heading-title' => [
					'label' => esc_html__('TP Heading','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/heading-title/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill="#1DD8A7" d="M7 17h22v2H7z"/><path fill="#5900E7" d="M9 3v30H7V3zM29 3v30h-2V3z"/><rect x="3" y="31" width="10" height="2" rx="1" fill="#1DD8A7"/><rect x="23" y="3" width="10" height="2" rx="1" fill="#1DD8A7"/><rect x="3" y="3" width="10" height="2" rx="1" fill="#1DD8A7"/><rect x="23" y="31" width="10" height="2" rx="1" fill="#1DD8A7"/></svg>',
					'keyword' => ['Heading', 'Title', 'Text', 'Heading title', 'Headline']
				],
				'tp-hotspot' => [
					'label' => esc_html__('Hotspot','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/hotspot-pin-point/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M21.106 14.553a1 1 0 0 1 1.801.026l6.5 12a1 1 0 0 1-1.814.842l-5.626-11.118-4.572 8.144a1 1 0 0 1-1.79 0l-2.685-5.37-5.062 8.438a1 1 0 0 1-1.715-1.03l6-10a1 1 0 0 1 1.752.068l2.605 5.21 4.606-7.21Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6 10a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V12a2 2 0 0 0-2-2h-8.5V8H30a4 4 0 0 1 4 4v16a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V12a4 4 0 0 1 4-4h7.5v2H6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.77 11.764a1 1 0 0 1 1 1v3.779a1 1 0 0 1-2 0v-3.779a1 1 0 0 1 1-1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.77 6a2.77 2.77 0 1 0 0 5.54 2.77 2.77 0 0 0 0-5.54ZM13 8.77a4.77 4.77 0 1 1 9.54 0 4.77 4.77 0 0 1-9.54 0Z" fill="#1DD8A7"/></svg>',
					'keyword' => [ 'Image hotspot', 'maps', 'pin' ],
				],
				'tp-hovercard' => [
					'label' => esc_html__('Hover Card','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/advanced-hover-card-animations/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M16.325 18.907c-.548-1.6.983-3.13 2.582-2.582l12.72 4.361c1.889.648 1.809 3.347-.116 3.881l-5.434 1.51-1.51 5.434c-.534 1.925-3.233 2.005-3.88.116l-4.362-12.72Zm2.905-.322a.508.508 0 0 0-.645.645l3.492 10.187a.508.508 0 0 0 .97-.029l1.379-4.962 4.962-1.378a.508.508 0 0 0 .029-.97L19.23 18.584Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M20.422 6.132a10.67 10.67 0 1 0-4.962 20.24c.561.012 1.049.428 1.09.988.04.56-.381 1.05-.942 1.047A12.703 12.703 0 1 1 28.4 16.1c-.018.561-.525.963-1.082.9-.558-.061-.955-.565-.946-1.126a10.67 10.67 0 0 0-5.951-9.742Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Hover Card', 'Card', 'Business Card'],
				],
				'tp-infobox' => [
					'label' => esc_html__('Infobox','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/infobox/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M32 6H4v24h28V6ZM4 4a2 2 0 0 0-2 2v24a2 2 0 0 0 2 2h28a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17 18v6.93A7.001 7.001 0 0 1 18 11a7 7 0 0 1 1 13.93V18a1 1 0 1 0-2 0Zm1 9a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-11a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Infobox', 'Information', 'Info box', 'card', 'info']
				],
				'tp-interactive-circle-info' => [
					'label' => esc_html__('Interactive Circle Info','tpgb'),
					'demoUrl' => '',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 30.692c7.01 0 12.692-5.682 12.692-12.692S25.01 5.308 18 5.308 5.308 10.99 5.308 18 10.99 30.692 18 30.692ZM18 33c8.284 0 15-6.716 15-15 0-8.284-6.716-15-15-15C9.716 3 3 9.716 3 18c0 8.284 6.716 15 15 15Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27.94 12.041a1.154 1.154 0 0 0-1.577-.422l-6.324 3.652a1.154 1.154 0 1 0 1.154 1.998l6.324-3.651a1.154 1.154 0 0 0 .422-1.577ZM8.08 12.19a1.154 1.154 0 0 1 1.576-.423l6.215 3.589a1.154 1.154 0 1 1-1.154 1.998l-6.215-3.588a1.154 1.154 0 0 1-.423-1.577Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M29.538 12.23a1.154 1.154 0 1 0 0-2.307 1.154 1.154 0 0 0 0 2.308Zm0 2.309a3.462 3.462 0 1 0 0-6.924 3.462 3.462 0 0 0 0 6.924ZM6.462 12.23a1.154 1.154 0 1 0 0-2.307 1.154 1.154 0 0 0 0 2.308Zm0 2.309a3.462 3.462 0 1 0 0-6.924 3.462 3.462 0 0 0 0 6.924Z" fill="#1DD8A7"/><circle cx="18" cy="18" r="2.462" stroke="#5900E7" stroke-width="2"/></svg>',
					'keyword' => ['interactive circle', 'interactive', 'circle', 'info']
				],
				'tp-login-register' => [
					'label' => esc_html__('Login Register','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/lottiefiles-animations/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 36 36" xmlns:v="https://vecta.io/nano"><mask id="A" fill="#fff"><path fill-rule="evenodd" d="M22 14a4 4 0 1 1-8 0 4 4 0 1 1 8 0zm-.178 4.625C23.153 17.525 24 15.861 24 14a6 6 0 1 0-12 0c0 1.861.848 3.525 2.178 4.625a12 12 0 0 0-8.009 9.372l1.972.334A10 10 0 0 1 18.066 20a10 10 0 0 1 9.815 8.46l1.976-.308a12 12 0 0 0-8.035-9.527z"/></mask><path fill-rule="evenodd" d="M22 14a4 4 0 1 1-8 0 4 4 0 1 1 8 0zm-.178 4.625C23.153 17.525 24 15.861 24 14a6 6 0 1 0-12 0c0 1.861.848 3.525 2.178 4.625a12 12 0 0 0-8.009 9.372l1.972.334A10 10 0 0 1 18.066 20a10 10 0 0 1 9.815 8.46l1.976-.308a12 12 0 0 0-8.035-9.527z" fill="#000"/><path d="M21.822 18.625l-1.275-1.541-2.77 2.292 3.408 1.145.637-1.896zm-7.645 0l.637 1.896 3.408-1.145-2.77-2.292-1.275 1.541zm-3.892 2.183L9 19.277h0l1.286 1.532zm-4.117 7.188l-1.972-.334-.334 1.972 1.972.334.334-1.972zm1.972.334l-.334 1.972 1.972.334.334-1.972-1.972-.334zm3.431-5.99l-1.286-1.532h0l1.286 1.532zM18.066 20l.013-2h0l-.013 2zm6.463 2.425l1.306-1.515h0l-1.306 1.515zm3.352 6.035l-1.976.308.308 1.976 1.976-.308-.308-1.976zm1.976-.308l.308 1.976 1.976-.308-.308-1.976-1.976.308zm-4.023-7.242l-1.306 1.515h0l1.306-1.515zM18 20a6 6 0 0 0 6-6h-4a2 2 0 0 1-2 2v4zm-6-6a6 6 0 0 0 6 6v-4a2 2 0 0 1-2-2h-4zm6-6a6 6 0 0 0-6 6h4a2 2 0 0 1 2-2V8zm6 6a6 6 0 0 0-6-6v4a2 2 0 0 1 2 2h4zm-.903 6.166C24.867 18.702 26 16.482 26 14h-4c0 1.24-.562 2.347-1.453 3.084l2.55 3.082zM26 14a8 8 0 0 0-8-8v4a4 4 0 0 1 4 4h4zm-8-8a8 8 0 0 0-8 8h4a4 4 0 0 1 4-4V6zm-8 8c0 2.482 1.133 4.702 2.903 6.166l2.55-3.082C14.562 16.347 14 15.24 14 14h-4zm1.571 8.34a10 10 0 0 1 3.244-1.82l-1.274-3.792A14 14 0 0 0 9 19.277l2.572 3.064zm-3.431 5.99a10 10 0 0 1 3.431-5.99L9 19.277a14 14 0 0 0-4.803 8.386l3.944.668zm.334-1.972l-1.972-.334-.668 3.944 1.972.334.668-3.944zm1.811-5.55a12 12 0 0 0-4.117 7.188l3.944.668a8 8 0 0 1 2.745-4.792l-2.572-3.064zM18.079 18a12 12 0 0 0-7.793 2.808l2.572 3.064A8 8 0 0 1 18.052 22l.026-4zm7.756 2.91A12 12 0 0 0 18.079 18l-.026 4a8 8 0 0 1 5.171 1.94l2.611-3.03zm4.023 7.242a12 12 0 0 0-4.023-7.242l-2.611 3.03a8 8 0 0 1 2.682 4.828l3.952-.616zm-.308-1.976l-1.976.308.616 3.952 1.976-.308-.616-3.952zm-5.02-3.75a10 10 0 0 1 3.352 6.035l3.952-.616a14 14 0 0 0-4.693-8.449l-2.611 3.03zm-3.343-1.904a10 10 0 0 1 3.343 1.904l2.611-3.03a14 14 0 0 0-4.681-2.666l-1.274 3.792z" fill="#1dd8a7" mask="url(#A)"/><path fill-rule="evenodd" d="M18 32c7.732 0 14-6.268 14-14S25.732 4 18 4 4 10.268 4 18s6.268 14 14 14zm0 2c8.837 0 16-7.163 16-16S26.837 2 18 2 2 9.163 2 18s7.163 16 16 16z" fill="#5900e7"/></svg>',
					'keyword' => ['login', 'register', 'Sign up']
				],
				'tp-lottiefiles' => [
					'label' => esc_html__('LottieFiles Animation','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/lottiefiles-animations/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M25.41 3H10.59A7.59 7.59 0 0 0 3 10.59v14.82A7.59 7.59 0 0 0 10.59 33h14.82A7.59 7.59 0 0 0 33 25.41V10.59A7.59 7.59 0 0 0 25.41 3Z" fill="#00DDB3"/><path d="M26.4 10c-5.56 0-7.62 3.562-9.277 6.424l-1.083 1.83c-1.754 3.035-3.065 4.88-6.443 4.88-.21 0-.418.037-.611.11-.194.071-.37.177-.519.31-.148.133-.265.29-.346.465a1.305 1.305 0 0 0 0 1.096c.081.174.199.332.347.465.3.268.705.42 1.129.42 5.563 0 7.623-3.562 9.28-6.424l1.08-1.83c1.757-3.036 3.068-4.88 6.444-4.88.21 0 .418-.037.612-.109a1.62 1.62 0 0 0 .518-.31c.149-.133.267-.291.347-.465.08-.174.122-.36.122-.55 0-.38-.17-.744-.47-1.012-.3-.269-.706-.42-1.13-.42Z" fill="#fff"/></svg>',
					'keyword' => ['animation', 'lottie', 'files']
				],
				'tp-mailchimp' => [
					'label' => esc_html__('Mailchimp','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/mailchimp/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M25.061 17.166a2.37 2.37 0 0 1 .616 0c.11-.256.13-.697.03-1.177-.148-.713-.348-1.145-.76-1.078-.413.068-.429.584-.281 1.298.083.401.231.744.397.957h-.002Zm-3.547.567c.296.133.477.217.548.142.125-.13-.23-.628-.803-.875a2.066 2.066 0 0 0-2.027.246c-.199.146-.385.349-.358.472.056.25.662-.181 1.497-.233.463-.03.847.117 1.143.248Zm-.596.342c-.601.095-.994.437-.892.675.06.023.077.054.345-.054.395-.15.822-.195 1.24-.13.193.022.285.034.327-.033.096-.148-.378-.535-1.02-.458Zm3.588 1.143c.224-.46-.722-.93-.948-.468-.225.463.724.927.949.466l-.001.002Zm1.037-1.368c-.508-.009-.527 1.056-.017 1.064.509.01.528-1.056.018-1.066l-.001.002Zm-14.491 5.272c-.088.02-.398.097-.561-.157-.345-.535.735-1.362.198-2.39-.603-1.167-1.842-.905-2.321-.37-.577.641-.578 1.573-.332 1.609.283.038.27-.433.49-.777a.853.853 0 0 1 .873-.376c.11.021.215.064.309.127.767.507.09 1.187.15 1.912.093 1.115 1.22 1.094 1.43.602a.14.14 0 0 0-.013-.156c.002.06.045-.087-.222-.026l-.002.002Zm19.851-1.14c-.222-.784-.17-.617-.449-1.372.162-.245 1.013-1.603-.203-2.89-.69-.73-2.246-1.105-2.723-1.238-.099-.761.309-3.922-1.425-5.546 1.377-1.44 2.236-3.026 2.234-4.387-.004-2.616-3.189-3.407-7.115-1.768l-.83.356c-.005-.004-1.505-1.488-1.527-1.508C14.39-.308.396 15.38 4.87 19.192l.978.836a4.883 4.883 0 0 0-.272 2.239c.223 2.231 2.385 4.037 4.473 4.034 3.824 8.89 17.744 8.906 21.347.2.115-.298.603-1.644.603-2.831 0-1.188-.668-1.689-1.095-1.689h-.002Zm-20.93 3.217c-1.511-.04-3.143-1.413-3.306-3.04-.408-4.097 4.919-5.03 5.564-.824.3 1.98-.31 3.908-2.26 3.862l.002.002Zm-1.226-7.63c-1.004.197-1.889.77-2.43 1.567-.324-.272-.928-.802-1.033-1.003-.862-1.66.943-4.877 2.205-6.695 3.12-4.493 8.007-7.892 10.27-7.275.368.104 1.586 1.529 1.586 1.529s-2.262 1.266-4.359 3.03c-2.825 2.195-4.96 5.385-6.239 8.847Zm15.822 6.758s-2.367.354-4.604-.472c.412-1.347 1.789.407 6.385-.923 1.013-.292 2.343-.868 3.378-1.694.223.52.381 1.064.472 1.623.242-.044.944-.035.758 1.21-.218 1.327-.777 2.404-1.718 3.396a7.081 7.081 0 0 1-2.063 1.558c-.433.228-.883.42-1.347.573-3.545 1.168-7.173-.116-8.346-2.873a4.452 4.452 0 0 1-.235-.651c-.499-1.818-.075-3.998 1.248-5.37.081-.088.164-.19.164-.32a.567.567 0 0 0-.127-.304c-.464-.676-2.066-1.83-1.744-4.064.232-1.604 1.622-2.734 2.919-2.667l.331.02c.562.033 1.053.106 1.515.125.775.034 1.47-.08 2.295-.772.278-.234.501-.437.878-.502a1.145 1.145 0 0 1 .9.15c.663.443.756 1.519.79 2.304.02.45.073 1.537.092 1.846.042.713.227.814.603.936.212.07.408.122.696.204.875.248 1.391.5 1.723.823.168.167.279.385.313.62.104.76-.584 1.698-2.405 2.55-3.093 1.449-6.204.966-6.655.914-1.334-.18-2.095 1.559-1.295 2.75 1.5 2.232 8.107 1.336 10.026-1.427.046-.066.008-.106-.048-.066-2.767 1.91-6.429 2.553-8.509 1.737-.316-.124-.975-.43-1.055-1.114 2.887.901 4.702.05 4.702.05s.135-.187-.037-.17ZM14.51 11.418c1.109-1.293 2.475-2.417 3.698-3.049a.048.048 0 0 1 .059.008.05.05 0 0 1 .007.06c-.096.177-.284.556-.343.844a.05.05 0 0 0 .02.054.05.05 0 0 0 .056 0c.761-.524 2.085-1.085 3.246-1.157a.05.05 0 0 1 .051.033c.004.01.004.022.001.033a.052.052 0 0 1-.018.026c-.192.15-.363.323-.51.518a.051.051 0 0 0-.005.051.05.05 0 0 0 .044.028c.815.006 1.964.294 2.715.718.05.028.015.127-.042.115-4.607-1.065-8.152 1.238-8.909 1.792a.05.05 0 0 1-.065-.009.051.051 0 0 1 0-.065h-.005Z" fill="#5900E7"/></svg>',
					'keyword' => ['Mailchimp', 'Mailchimp addon', 'subscribe form']
				],
				'tp-media-listing' => [
					'label' => esc_html__('Media Listing','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-listing/#image-gallery',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 5h12v2H12V5Zm-2 2V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2h1a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V13a2 2 0 0 1 2-2h2V9a2 2 0 0 1 2-2h1Zm14 2H9v2h18V9h-3ZM9 13h22v18H5V13h4Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M14.132 26.17a1.32 1.32 0 0 1 .159.242c.204.399.204.885-.084 1.238-.349.427-.985.495-1.359.09a7.007 7.007 0 0 1-.262-.303l-.02-.024a7 7 0 1 1 10.937-.087l-.02.025a6.991 6.991 0 0 1-.256.306c-.368.412-1.005.354-1.36-.068-.294-.348-.302-.834-.104-1.236a1.364 1.364 0 0 1 .154-.245 5 5 0 1 0-7.785.062Zm6.051-1.113c-.296.315-.741.261-1.042-.012a.98.98 0 0 1-.21-.27l-.15-.288c-.12-.227-.08-.492.006-.745a2.903 2.903 0 0 1 .133-.327 1.01 1.01 0 0 0 .066-.63l-.006-.025a1.01 1.01 0 1 0-1.902.65l.066.154c.023.057.045.115.064.173.085.254.123.52.003.745l-.153.287a.979.979 0 0 1-.21.27c-.304.27-.75.321-1.044.005a3 3 0 1 1 4.38.013Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Video Gallery', 'Image Gallery', 'Video Carousel', 'Image Carousel', 'Video Listing', 'Image Listing', 'Youtube', 'Vimeo','media gallery']
				],
				'tp-messagebox' => [
					'label' => esc_html__('Message box','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/message-box/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M16.125 8.996a1.879 1.879 0 1 1 3.75 0l-.813 13.006a1.064 1.064 0 0 1-2.124 0l-.813-13.006Z" fill="#1DD8A7"/><circle cx="18" cy="27" r="2" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30 5H6a1 1 0 0 0-1 1v24a1 1 0 0 0 1 1h24a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM6 3a3 3 0 0 0-3 3v24a3 3 0 0 0 3 3h24a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H6Z" fill="#5900E7"/></svg>',
					'keyword' => ['Message box', 'Notification box', 'alert box']
				],
				'tp-mobile-menu' => [
					'label' => esc_html__('Mobile Menu','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/mobile-menu/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M25 4H11a1 1 0 0 0-1 1v26a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1ZM11 2a3 3 0 0 0-3 3v26a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3H11Z" fill="#5900E7"/><path d="M22 27.8a.8.8 0 0 1 .8-.8h.4a.8.8 0 0 1 .8.8v.4a.8.8 0 0 1-.8.8h-.4a.8.8 0 0 1-.8-.8v-.4ZM12 27.8a.8.8 0 0 1 .8-.8h.4a.8.8 0 0 1 .8.8v.4a.8.8 0 0 1-.8.8h-.4a.8.8 0 0 1-.8-.8v-.4ZM16 27.6a1.6 1.6 0 0 1 1.6-1.6h.8a1.6 1.6 0 0 1 1.6 1.6v.8a1.6 1.6 0 0 1-1.6 1.6h-.8a1.6 1.6 0 0 1-1.6-1.6v-.8Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['mobile menu', 'menu']
				],
				'tp-mouse-cursor' => [
					'label' => esc_html__('Mouse Cursor','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/mouse-cursor-icon-block/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M13.314 4.06a5.5 5.5 0 1 0-1.592 10.885c.547.078 1.015.498 1.037 1.05.023.552-.409 1.024-.958.972a7.5 7.5 0 1 1 7.475-4.254c-.236.5-.863.629-1.326.328-.463-.301-.585-.918-.373-1.428a5.5 5.5 0 0 0-4.263-7.552Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30.845 23.547 13.54 10.187l1.668 21.815 5.111-6.175a4 4 0 0 1 2.65-1.426l7.876-.854ZM14.763 8.604c-1.371-1.06-3.349.008-3.217 1.735l1.667 21.815c.137 1.794 2.388 2.509 3.535 1.123l5.112-6.174a2 2 0 0 1 1.325-.713l7.876-.854c1.79-.195 2.438-2.536 1.006-3.572L14.763 8.604Z" fill="#5900E7"/></svg>',
					'keyword' => ['mouse', 'cursor', 'animated cursor', 'mouse cursor', 'pointer']
				],
				'tp-navigation-builder' => [
					'label' => esc_html__('Navigation Menu','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/navigation-menu/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="12" y="10.5" width="18" height="2" rx="1" fill="#1DD8A7"/><rect x="6" y="10.5" width="4" height="2" rx="1" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5 8h26a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Zm-3 6V9a3 3 0 0 1 3-3h26a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3h-4v10a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V14Zm23 3H4v10a1 1 0 0 0 1 1h19a1 1 0 0 0 1-1V17Z" fill="#5900E7"/></svg>',
					'keyword' => ['navigation menu', 'mega menu', 'header builder', 'sticky menu', 'navigation bar', 'header menu', 'menu', 'navigation builder','vertical menu', 'swiper menu']
				],
				'tp-number-counter' => [
					'label' => esc_html__('Number Counter','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/number-counter/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M21 10V5h-2v5a4 4 0 0 0 8 0V5h-2v5a2 2 0 1 1-4 0Zm-6 6v4a2 2 0 1 1-4 0v-4a2 2 0 1 1 4 0Zm-6 0a4 4 0 0 1 8 0v4a4 4 0 0 1-8 0v-4Zm14 5h2v10h-2v-6a5 5 0 0 1-3 1v-1.99a3.01 3.01 0 0 0 3-2.772V21Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M31 5H5v26h26V5ZM5 3a2 2 0 0 0-2 2v26a2 2 0 0 0 2 2h26a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Z" fill="#5900E7"/></svg>',
					'keyword' => ['number counter', 'counter', 'animated counter', 'Odometer']
				],
				'tp-popup-builder' => [
					'label' => esc_html__('Popup Builder','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/popup-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 12a2 2 0 0 1 2-2h2V8H7a4 4 0 0 0-4 4v12a4 4 0 0 0 4 4h7v2h-2a1 1 0 1 0 0 2h12a1 1 0 1 0 0-2h-2v-2h7a4 4 0 0 0 4-4V12a4 4 0 0 0-4-4h-1v2h1a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V12Zm15 16h-4v2h4v-2Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M13 9v4h3V9h-3Zm-.5-2A1.5 1.5 0 0 0 11 8.5v5a1.5 1.5 0 0 0 1.5 1.5h4a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 16.5 7h-4Z" fill="#1DD8A7"/><rect x="19.5" y="8.5" width="5" height="1" rx=".5" stroke="#1DD8A7"/><rect x="19.5" y="12.5" width="5" height="1" rx=".5" stroke="#1DD8A7"/><path d="M18 22.3c-.3 0-.5-.1-.7-.3l-2.7-2.8c-.3-.2-.6-.3-.9-.3h-3.5c-.4 0-.8-.1-1.2-.2-.4-.2-.7-.4-1-.7-.3-.3-.5-.7-.7-1.1-.2-.4-.3-.9-.3-1.3V6.3c0-.5.1-.9.2-1.3.2-.4.4-.7.7-1 .3-.3.7-.5 1-.7.5-.2.9-.3 1.3-.3h15.5c.4 0 .8.1 1.2.2.4.2.7.4 1 .7.3.3.5.7.7 1.1.3.4.4.8.4 1.3v9.3c0 .4-.1.8-.2 1.2-.2.4-.4.8-.7 1.1-.3.3-.7.5-1 .7-.3.2-.8.2-1.2.2h-3.5c-.3 0-.6.1-.8.4L18.7 22c-.2.2-.4.3-.7.3Zm-7.8-5.4h3.5c.9 0 1.7.4 2.3 1l2 2 2-2c.6-.6 1.4-1 2.3-1h3.5c.2 0 .3 0 .5-.1.1-.1.3-.2.4-.3.1-.1.2-.3.3-.4.1-.2.1-.3.1-.5V6.3c0-.2 0-.3-.1-.5s-.2-.3-.3-.4c-.1-.1-.2-.2-.4-.3-.2-.1-.4-.1-.5-.1H10.2c-.1 0-.3 0-.4.1-.2.1-.3.1-.4.3-.2.1-.2.2-.3.4-.1.1-.1.3-.1.5v9.3c0 .2 0 .3.1.5s.2.3.3.4c.1.1.2.2.4.3.1 0 .3.1.4.1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['popup', 'pop up', 'alertbox', 'offcanvas', 'modal box', 'modal popup']
				],
				'tp-post-author' => [
					'label' => esc_html__('Post Author', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 5a6.947 6.947 0 1 0 0 13.894A6.947 6.947 0 0 0 18 5Zm-8.947 6.947a8.947 8.947 0 1 1 17.894 0 8.947 8.947 0 0 1-17.894 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.839 22.733a13.106 13.106 0 0 1 9.267-3.839h3.788A13.106 13.106 0 0 1 33 32a1 1 0 1 1-2 0 11.106 11.106 0 0 0-11.106-11.106h-3.788A11.106 11.106 0 0 0 5 32a1 1 0 1 1-2 0c0-3.476 1.38-6.81 3.839-9.267Z" fill="#5900E7"/></svg>',
					'keyword' => ['post author', 'author','user info']
				],
				'tp-post-comment' => [
					'label' => esc_html__('Post Comments', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M18 32c-.3 0-.5-.1-.7-.3l-4.1-4.2c-.2-.2-.5-.4-.8-.5-.3-.1-.6-.2-.9-.2H6.3c-1.2 0-2.3-.5-3.1-1.3-.8-.8-1.2-1.9-1.2-3.1v-14c0-1.2.4-2.3 1.3-3.1.8-.8 1.9-1.3 3-1.3h23.3c1.1 0 2.3.5 3.1 1.3.9.8 1.3 1.9 1.3 3.1v14c0 1.2-.4 2.3-1.3 3.1-.8.8-1.9 1.3-3.1 1.3h-5.2c-.3 0-.6.1-.9.2-.3.1-.5.3-.8.5l-4.1 4.2c-.1.2-.3.3-.6.3ZM6.3 6c-.6 0-1.2.2-1.6.7-.4.4-.7 1.1-.7 1.7v14c0 .6.3 1.3.7 1.7.4.4 1 .7 1.6.7h5.2c.6 0 1.1.1 1.7.3.6.2 1 .5 1.4 1l3.4 3.4 3.4-3.4c.4-.4.9-.7 1.4-1 .5-.2 1.1-.3 1.7-.3h5.2c.6 0 1.2-.2 1.6-.7.4-.4.7-1.1.7-1.7v-14c0-.6-.3-1.3-.7-1.7-.4-.4-1-.7-1.6-.7H6.3Z" fill="#5900E7"/><circle cx="18" cy="15" r="1.5" fill="#1DD8A7"/><circle cx="9.5" cy="15" r="1.5" fill="#1DD8A7"/><circle cx="26.5" cy="15" r="1.5" fill="#1DD8A7"/></svg>',
					'keyword' => ['post comments', 'comments','comments area']
				],
				'tp-post-content' => [
					'label' => esc_html__('Post Content', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.643 4.659c.402-.43.933-.659 1.47-.659h8.63v6.283c0 .907.337 1.79.954 2.45a3.26 3.26 0 0 0 2.382 1.05H28v15.884c0 .64-.238 1.24-.643 1.674-.402.43-.933.659-1.47.659H10.113a2.014 2.014 0 0 1-1.471-.659A2.456 2.456 0 0 1 8 29.667V6.333c0-.64.238-1.24.643-1.674Zm19.222 7.124-7.121-7.328v5.828c0 .42.156.809.415 1.085.256.275.589.415.92.415h5.786ZM10.114 2A4.013 4.013 0 0 0 7.18 3.294 4.456 4.456 0 0 0 6 6.334v23.333c0 1.128.418 2.222 1.181 3.04A4.013 4.013 0 0 0 10.114 34h15.772a4.013 4.013 0 0 0 2.933-1.294A4.456 4.456 0 0 0 30 29.666v-18.15a1 1 0 0 0-.283-.696l-8.275-8.517A1 1 0 0 0 20.725 2H10.114Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.336 25.783a1 1 0 0 1 1-1h13.04a1 1 0 1 1 0 2h-13.04a1 1 0 0 1-1-1ZM10.375 18.442a1 1 0 0 1 1-1h13.04a1 1 0 1 1 0 2h-13.04a1 1 0 0 1-1-1ZM10.336 11.1a1 1 0 0 1 1-1h3.651a1 1 0 1 1 0 2h-3.65a1 1 0 0 1-1-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['content', 'post content', 'post excerpt', 'archive description']
				],
				'tp-post-image' => [
					'label' => esc_html__('Post Image', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M21.106 12.553a1 1 0 0 1 1.801.026l6.5 14a1 1 0 0 1-1.814.842l-5.626-12.118-4.572 9.144a1 1 0 0 1-1.79 0l-2.685-5.37-5.062 8.438a1 1 0 0 1-1.715-1.03l6-10a1 1 0 0 1 1.752.068l2.605 5.211 4.606-9.211ZM10 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm0 2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6 6a2 2 0 0 0-2 2v20a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V17a1 1 0 1 1 2 0v11a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h15a1 1 0 1 1 0 2H6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M29 4a1 1 0 0 0-1 1v3h-3a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3V5a1 1 0 0 0-1-1Z" fill="#5900E7"/></svg>',
					'keyword' => ['post featured image', 'post image', 'featured image']
				],
				'tp-post-listing' => [
					'label' => esc_html__('Post Listing', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-listing/#blog-listing',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 13a2 2 0 0 1 2-2h2V9H7a4 4 0 0 0-4 4v12a4 4 0 0 0 4 4h7v2h-2a1 1 0 1 0 0 2h12a1 1 0 1 0 0-2h-2v-2h7a4 4 0 0 0 4-4V13a4 4 0 0 0-4-4h-2v2h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V13Zm15 16h-4v2h4v-2Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M13 9v2h10V9H13Zm-.5-2A1.5 1.5 0 0 0 11 8.5v3a1.5 1.5 0 0 0 1.5 1.5h11a1.5 1.5 0 0 0 1.5-1.5v-3A1.5 1.5 0 0 0 23.5 7h-11Z" fill="#1DD8A7"/><rect x="11" y="16" width="14" height="2" rx="1" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M26 5H10a1 1 0 0 0-1 1v15a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM10 3a3 3 0 0 0-3 3v15a3 3 0 0 0 3 3h16a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H10Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['blog listing', 'article listing','custom post listing','blog view','post listing','masonry','carousel','content view','blog item listing','grid', 'post listing', 'related posts', 'archive posts', 'post list', 'post grid', 'post masonry','post carousel', 'post slider']
				],
				'tp-post-meta' => [
					'label' => esc_html__('Post Meta Info', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 32c7.732 0 14-6.268 14-14S25.732 4 18 4 4 10.268 4 18s6.268 14 14 14Zm0 2c8.837 0 16-7.163 16-16S26.837 2 18 2 2 9.163 2 18s7.163 16 16 16Z" fill="#5900E7"/><rect x="13" y="25" width="10" height="2" rx="1" fill="#1DD8A7"/><rect x="19" y="15" width="12" height="2" rx="1" transform="rotate(90 19 15)" fill="#1DD8A7"/><rect x="19" y="17" width="4" height="2" rx="1" transform="rotate(-180 19 17)" fill="#1DD8A7"/><circle cx="17" cy="11" r="2" fill="#1DD8A7"/></svg>',
					'keyword' => ['post category', 'post tags', 'post meta info', 'meta info', 'post date', 'post comment', 'post author']
				],
				'tp-post-navigation' => [
					'label' => esc_html__('Post Navigation', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M13 24a1 1 0 0 1 1-1h19a1 1 0 1 1 0 2H14a1 1 0 0 1-1-1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M33.694 23.28a1 1 0 0 1 .026 1.414l-6.75 7a1 1 0 1 1-1.44-1.388l6.75-7a1 1 0 0 1 1.414-.026Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25.566 16.28a1 1 0 0 1 1.414.026l6.74 7a1 1 0 0 1-1.44 1.388l-6.74-7a1 1 0 0 1 .026-1.414Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M23 12a1 1 0 0 1-1 1H3a1 1 0 1 1 0-2h19a1 1 0 0 1 1 1Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M2.306 11.28a1 1 0 0 0-.026 1.414l6.75 7a1 1 0 1 0 1.44-1.388l-6.75-7a1 1 0 0 0-1.414-.026Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.434 4.28a1 1 0 0 0-1.414.026l-6.74 7a1 1 0 0 0 1.44 1.388l6.74-7a1 1 0 0 0-.026-1.414Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['previous next', 'post previous next', 'post navigation']
				],
				'tp-post-title' => [
					'label' => esc_html__('Post Title', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/blog-builder/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="4" y="32" width="28" height="2" rx="1" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M4 3a1 1 0 0 1 1-1h8a1 1 0 1 1 0 2h-3v14a7.999 7.999 0 1 0 16 0V4h-3a1 1 0 1 1 0-2h8a1 1 0 1 1 0 2h-3v14a10 10 0 0 1-20 0V4H5a1 1 0 0 1-1-1Z" fill="#5900E7"/></svg>',
					'keyword' => ['post title', 'page title', 'archive title']
				],
				'tp-pricing-list' => [
					'label' => esc_html__('Pricing List','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/pricing-list/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M29 4H7a1 1 0 0 0-1 1v26a1 1 0 0 0 1 1h22a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1ZM7 2a3 3 0 0 0-3 3v26a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3H7Z" fill="#5900E7"/><path d="M12.326 10.96a.326.326 0 0 1-.326-.326V8.54a.5.5 0 0 1 .5-.5h.718c.234 0 .433.042.598.127.165.083.29.2.377.35.087.147.131.319.131.512a.983.983 0 0 1-.133.514.895.895 0 0 1-.384.346 1.353 1.353 0 0 1-.606.124h-.776V9.52h.67a.69.69 0 0 0 .311-.062.419.419 0 0 0 .184-.172.518.518 0 0 0 .062-.256.505.505 0 0 0-.062-.253.403.403 0 0 0-.184-.17.704.704 0 0 0-.313-.061h-.44v2.089c0 .18-.146.326-.327.326ZM15.074 10.96a.326.326 0 0 1-.326-.326V8.54a.5.5 0 0 1 .5-.5h.718c.233 0 .432.04.597.118a.85.85 0 0 1 .378.332c.087.143.131.31.131.504s-.044.36-.133.5a.847.847 0 0 1-.384.32 1.487 1.487 0 0 1-.606.11h-.815V9.43h.71a.857.857 0 0 0 .31-.049.365.365 0 0 0 .184-.145.44.44 0 0 0 .062-.241.457.457 0 0 0-.062-.245.374.374 0 0 0-.185-.152.799.799 0 0 0-.312-.052h-.44v2.089c0 .18-.147.326-.327.326Zm1.34-1.329.499.862a.311.311 0 1 1-.54.309l-.662-1.17h.704ZM17.86 8.04c.18 0 .326.146.326.326v2.268a.326.326 0 0 1-.653 0V8.366c0-.18.146-.326.326-.326ZM21.348 8.702c.082.181-.078.36-.277.36h-.056c-.132 0-.236-.102-.303-.215a.556.556 0 0 0-.143-.163.642.642 0 0 0-.202-.102.813.813 0 0 0-.246-.036.764.764 0 0 0-.417.113.73.73 0 0 0-.276.325 1.227 1.227 0 0 0-.098.516c0 .208.033.383.098.525a.72.72 0 0 0 .277.32.774.774 0 0 0 .412.109c.087 0 .168-.011.242-.033a.654.654 0 0 0 .2-.095.562.562 0 0 0 .146-.156c.07-.11.175-.208.306-.207h.054c.203.001.36.183.267.363l-.014.027a1.176 1.176 0 0 1-.273.332c-.113.097-.249.174-.407.23a1.573 1.573 0 0 1-.532.085c-.275 0-.521-.059-.738-.177a1.288 1.288 0 0 1-.513-.512 1.642 1.642 0 0 1-.187-.811c0-.318.064-.59.19-.813.127-.223.299-.393.516-.51A1.51 1.51 0 0 1 20.106 8c.18 0 .345.024.498.071.153.048.29.117.408.208a1.086 1.086 0 0 1 .336.423ZM22.331 10.96a.417.417 0 0 1-.417-.417V8.456c0-.23.186-.416.416-.416h1.41a.255.255 0 0 1 0 .509h-1.173v.696h1.065a.255.255 0 1 1 0 .509h-1.065v.697h1.178a.255.255 0 0 1 0 .51h-1.414Z" fill="#1DD8A7"/><rect x="13" y="17" width="15" height="2" rx="1" fill="#1DD8A7"/><rect x="13" y="27" width="15" height="2" rx="1" fill="#1DD8A7"/><path d="M9.374 20v-4h.304v4h-.304Zm.806-2.68a.385.385 0 0 0-.191-.293c-.112-.07-.265-.105-.458-.105-.13 0-.24.015-.331.047a.47.47 0 0 0-.207.126.274.274 0 0 0-.07.185.231.231 0 0 0 .042.15c.032.042.076.08.131.11.056.03.12.057.193.08.073.022.15.04.233.057l.341.068c.165.032.317.073.456.125.138.052.257.116.359.192.101.076.18.166.235.27a.737.737 0 0 1 .087.354.789.789 0 0 1-.178.51 1.118 1.118 0 0 1-.503.332 2.356 2.356 0 0 1-.791.117c-.306 0-.573-.04-.8-.118a1.162 1.162 0 0 1-.53-.352.936.936 0 0 1-.198-.58h.776a.445.445 0 0 0 .11.27c.065.07.152.123.26.16.11.035.234.053.373.053.135 0 .253-.017.353-.05a.55.55 0 0 0 .235-.139.29.29 0 0 0 .084-.205.242.242 0 0 0-.076-.18.59.59 0 0 0-.219-.126 2.536 2.536 0 0 0-.35-.094l-.413-.087c-.32-.066-.572-.168-.757-.308a.665.665 0 0 1-.276-.564.75.75 0 0 1 .183-.508c.125-.145.296-.258.513-.339.218-.081.464-.122.741-.122.282 0 .527.04.737.122.211.081.375.194.493.34a.802.802 0 0 1 .181.502h-.768ZM10.996 26.992l-.155.543H8l.153-.543h2.843ZM9.519 30l-1.45-1.59.003-.488h.716c.16 0 .293-.027.4-.082a.564.564 0 0 0 .242-.223.649.649 0 0 0 .083-.32.603.603 0 0 0-.172-.44c-.111-.114-.296-.171-.553-.171H8L8.174 26h.614c.36 0 .66.05.9.15.242.1.423.24.543.424.121.184.183.403.184.656a1.373 1.373 0 0 1-.123.59c-.079.168-.204.308-.375.42-.17.112-.39.196-.663.25l-.034.012 1.297 1.463V30h-.998ZM11 26l-.159.55H8.502l.159-.55H11Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Pricing list', 'Item price', 'price card', 'Price Guide', 'price box']
				],
				'tp-pricing-table' => [
					'label' => esc_html__('Pricing Table','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/pricing-table/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M27 4H9v28h18V4ZM9 2a2 2 0 0 0-2 2H3a1 1 0 0 0-1 1v26a1 1 0 0 0 1 1h4a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2h4a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-4a2 2 0 0 0-2-2H9Zm20 4v24h3V6h-3ZM4 6h3v24H4V6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.518 10.655c-.427 0-.822.158-1.101.417a1.248 1.248 0 0 0-.417.913c.002.326.142.655.418.91.28.258.674.415 1.1.415h.188a1 1 0 0 1 0 2h-.188a3.623 3.623 0 0 1-2.456-.945A3.247 3.247 0 0 1 14 11.99l1-.004h-1c0-.909.39-1.764 1.058-2.382a3.624 3.624 0 0 1 2.46-.948h2.69a1 1 0 1 1 0 2h-2.69Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.706 14.31a1 1 0 0 1 1-1h.787c.907.003 1.79.338 2.453.953A3.246 3.246 0 0 1 22 16.64c0 .907-.39 1.76-1.056 2.377a3.615 3.615 0 0 1-2.454.947h-2.659a1 1 0 1 1 0-2h2.66c.423 0 .816-.157 1.095-.415a1.24 1.24 0 0 0 .414-.91c0-.325-.14-.655-.415-.912a1.622 1.622 0 0 0-1.097-.418h-.782a1 1 0 0 1-1-1Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M17.847 17.965a1 1 0 0 1 1 1v1.655a1 1 0 1 1-2 0v-1.655a1 1 0 0 1 1-1ZM17.847 7a1 1 0 0 1 1 1v1.656a1 1 0 1 1-2 0V8a1 1 0 0 1 1-1Z" fill="#1DD8A7"/><rect x="12" y="24" width="12" height="2" rx="1" fill="#1DD8A7"/><rect x="12" y="27" width="12" height="2" rx="1" fill="#1DD8A7"/></svg>',
					'keyword' => ['Pricing table', 'pricing list', 'price table', 'plans table', 'pricing plans', 'dynamic pricing', 'price comparison', 'Plans & Pricing Table', 'Price Chart']
				],
				'tp-preloader' => [
					'label' => esc_html__('Pre Loader','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/preloader',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><circle cx="6" cy="18" r="4" fill="#5900E7"/><circle cx="9.515" cy="8.808" r="3.5" transform="rotate(45 9.515 8.808)" fill="#5900E7"/><circle cx="19" cy="5" r="3" transform="rotate(90 19 5)" fill="#5900E7"/><circle cx="28.607" cy="9.515" r="2.5" transform="rotate(135 28.607 9.515)" fill="#1DD8A7"/><circle cx="32" cy="18" r="2" fill="#1DD8A7"/><circle cx="28.252" cy="28.251" r="1.502" transform="rotate(45 28.252 28.251)" fill="#1DD8A7"/><circle cx="18" cy="33" transform="rotate(90 18 33)" fill="#1DD8A7" r="1"/><circle cx="7.04" cy="28.96" r=".5" transform="rotate(135 7.04 28.96)" fill="#1DD8A7"/></svg>',
					'keyword' => [ 'pre loader', 'loader', 'loading' ],
				],
				'tp-pro-paragraph' => [
					'label' => esc_html__('TP Paragraph','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/advance-text-block/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="19" y="2" width="2" height="32" rx="1" fill="#5900E7"/><rect x="25" y="2" width="2" height="32" rx="1" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8.05 6.05A7 7 0 0 1 13 4V2a9 9 0 1 0 0 18h6v-2h-6A7 7 0 0 1 8.05 6.05ZM31 2a1 1 0 1 1 0 2H13V2h18Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Paragraph', 'wysiwyg', 'editor', 'editor block', 'textarea', 'text area', 'text editor'],
				],
				'tp-process-steps' => [
					'label' => esc_html__('Process Steps','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/process-steps/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><circle cx="29" cy="29" r="3" stroke="#1DD8A7" stroke-width="2"/><path fill-rule="evenodd" clip-rule="evenodd" d="M29.682 9.318A4.5 4.5 0 0 0 26.5 8V6a6.5 6.5 0 0 1 0 13v-2a4.5 4.5 0 0 0 3.182-7.682ZM7.318 20.318A4.5 4.5 0 0 1 10.5 19v-2a6.5 6.5 0 1 0 0 13v-2a4.5 4.5 0 0 1-3.182-7.682Z" fill="#5900E7"/><path d="M10 6h17v2H10V6ZM10.5 17H15v2h-4.5v-2ZM10.5 28H23v2H10.5v-2ZM21 17h5.5v2H21v-2Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M20.293 25.293a1 1 0 0 1 1.414 0l3.003 3a1 1 0 1 1-1.414 1.414l-3.003-3a1 1 0 0 1 0-1.414Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.71 28.293a1 1 0 0 1 0 1.414l-3.003 3a1 1 0 1 1-1.414-1.414l3.003-3a1 1 0 0 1 1.414 0Z" fill="#5900E7"/><circle cx="18" cy="18" r="3" stroke="#1DD8A7" stroke-width="2"/><circle cx="7" cy="7" r="3" stroke="#1DD8A7" stroke-width="2"/></svg>',
					'keyword' => ['Process steps', 'post timeline', 'step process', 'steps form', 'Steppers', 'timeline', 'Progress Tracker']
				],
				'tp-product-listing' => [
					'label' => esc_html__('Product Listing','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-listing/#woo-listing',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M27 4H9v28h18V4ZM9 2a2 2 0 0 0-2 2H3a1 1 0 0 0-1 1v26a1 1 0 0 0 1 1h4a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2h4a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-4a2 2 0 0 0-2-2H9Zm20 4v24h3V6h-3ZM4 6h3v24H4V6Z" fill="#5900E7"/><path d="M12 26a1 1 0 0 1 1-1h10a1 1 0 1 1 0 2H13a1 1 0 0 1-1-1Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.072 9c-.292 0-.567.113-.764.304a.97.97 0 0 0-.3.696v1.153a1 1 0 1 1-2 0V10c0-.806.33-1.572.908-2.132a3.122 3.122 0 0 1 3.318-.643c.37.147.708.366.993.643.286.277.515.607.672.974.156.366.237.76.237 1.158v1.147a1 1 0 1 1-2 0V10a.946.946 0 0 0-.076-.372.997.997 0 0 0-.224-.324 1.07 1.07 0 0 0-.346-.224 1.122 1.122 0 0 0-.418-.08Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="m14.974 12.153-.966 6.141a.565.565 0 0 0 .014.25.6.6 0 0 0 .127.229c.06.068.137.126.227.166.09.04.188.061.29.061h6.676c.1 0 .198-.022.287-.062a.657.657 0 0 0 .224-.166.594.594 0 0 0 .125-.227.561.561 0 0 0 .014-.248l-.002-.013-.958-6.13h-6.058ZM21.344 20v1h-6.678a2.71 2.71 0 0 1-1.107-.236 2.661 2.661 0 0 1-.912-.67 2.597 2.597 0 0 1-.546-.995 2.564 2.564 0 0 1-.067-1.128l.971-6.175c.08-.469.328-.888.692-1.187a2.017 2.017 0 0 1 1.283-.456h.002l-.003 1v-1h6.051c.466 0 .92.16 1.282.458.362.298.61.717.689 1.185l.002.013.962 6.161v-.006l-.987.166.988-.154v-.006c.062.375.04.76-.065 1.125-.105.367-.29.706-.542.993a2.655 2.655 0 0 1-.908.673 2.706 2.706 0 0 1-1.104.239l-.003-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Product', 'Woocommerce']
				],
				'tp-progress-bar' => [
					'label' => esc_html__('Progress Bar','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/progress-bar/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M31 6H5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM5 4a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H5ZM31 21H5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1ZM5 19a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h26a3 3 0 0 0 3-3v-7a3 3 0 0 0-3-3H5Z" fill="#5900E7"/><path d="M6 10.5A2.5 2.5 0 0 1 8.5 8h14a2.5 2.5 0 0 1 0 5h-14A2.5 2.5 0 0 1 6 10.5ZM6 25.5A2.5 2.5 0 0 1 8.5 23h6a2.5 2.5 0 0 1 0 5h-6A2.5 2.5 0 0 1 6 25.5Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Progress bar', 'progressbar', 'status bar', 'progress indicator', 'scroll progress', 'process progress bar', 'Progress Tracker']
				],
				'tp-row' => [
					'label' => esc_html__('TP Row','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/row/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 27H5v2a2 2 0 0 0 2 2h2v-4Zm-6-2v4a4 4 0 0 0 4 4h4v-8H3ZM9 5H6a1 1 0 0 0-1 1v3h4V5ZM6 3a3 3 0 0 0-3 3v5h8V3H6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M20 27h-4v4h4v-4Zm-6-2v8h8v-8h-8ZM20 5h-4v4h4V5Zm-6-2v8h8V3h-8Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18 16H5v4h13v-4ZM3 14v8h17v-8H3ZM31 27h-4v4h3a1 1 0 0 0 1-1v-3Zm-6-2v8h5a3 3 0 0 0 3-3v-5h-8ZM31 5h-4v4h4V5Zm-6-2v8h8V5a2 2 0 0 0-2-2h-6Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M20.317 17.27a1 1 0 0 1 1.414.047l2.803 3a1 1 0 0 1-1.461 1.366l-2.804-3a1 1 0 0 1 .048-1.414Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.484 14.27a1 1 0 0 1 .048 1.412l-2.801 3a1 1 0 0 1-1.462-1.364l2.802-3a1 1 0 0 1 1.413-.049Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M33 18a1 1 0 0 1-1 1H21a1 1 0 1 1 0-2h11a1 1 0 0 1 1 1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Row', 'layout'],
				],
				'tp-site-logo' => [
					'label' => esc_html__('Site Logo','tpgb'),
					'demoUrl' => '#',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M23.375 28.254c.245-.393.478-.811.695-1.254H19v4.882c1.518-.357 3.066-1.533 4.375-3.628ZM30.5 27h-4.23c-1.8 4.226-4.833 7-8.27 7-3.437 0-6.47-2.774-8.27-7H5.5a1 1 0 1 1 0-2h3.505a23.815 23.815 0 0 1-.986-6H3.551a1 1 0 1 1 0-2h4.468c.083-2.141.428-4.168.986-6H5.5a1 1 0 1 1 0-2h4.23c1.8-4.226 4.833-7 8.27-7 3.437 0 6.47 2.774 8.27 7h4.23a1 1 0 1 1 0 2h-3.505c.557 1.832.903 3.859.986 6H32.5a1 1 0 1 1 0 2h-4.52a23.814 23.814 0 0 1-.985 6H30.5a1 1 0 1 1 0 2Zm-4.52-10c-.092-2.191-.481-4.224-1.086-6H19v6h6.98ZM19 19h6.98c-.092 2.191-.481 4.224-1.086 6H19v-6Zm-2-2v-6h-5.894c-.605 1.776-.994 3.809-1.085 6H17Zm-6.98 2H17v6h-5.894c-.605-1.776-.994-3.809-1.085-6Zm2.605 9.254A14.11 14.11 0 0 1 11.93 27H17v4.882c-1.518-.357-3.066-1.533-4.375-3.628ZM19 9h5.07c-.217-.443-.45-.861-.695-1.254-1.31-2.095-2.857-3.271-4.375-3.628V9Zm-7.07 0c.217-.443.45-.861.695-1.254 1.31-2.095 2.857-3.271 4.375-3.628V9h-5.07Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18 32c7.732 0 14-6.268 14-14S25.732 4 18 4 4 10.268 4 18s6.268 14 14 14Zm0 2c8.837 0 16-7.163 16-16S26.837 2 18 2 2 9.163 2 18s7.163 16 16 16Z" fill="#5900E7"/></svg>',
					'keyword' => ['site logo','logo','dual logo'],
				],
				'tp-stylist-list' => [
					'label' => esc_html__('Stylish List','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/stylish-list/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><circle cx="5" cy="29" r="3" fill="#1DD8A7"/><rect x="10" y="28" width="24" height="2" rx="1" fill="#5900E7"/><circle cx="5" cy="7" r="3" fill="#1DD8A7"/><rect x="10" y="6" width="24" height="2" rx="1" fill="#5900E7"/><circle cx="5" cy="18" r="3" fill="#1DD8A7"/><rect x="10" y="17" width="24" height="2" rx="1" fill="#5900E7"/></svg>',
					'keyword' => ['Stylish list', 'listing', 'item listing'],
				],
				'tp-scroll-navigation' => [
					'label' => esc_html__('Scroll Navigation','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/one-page-scroll-navigation/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M18 4.936 8.604 15.004h18.795L18.001 4.936Zm.732-2.148a1 1 0 0 0-1.462 0L5.57 15.322c-.597.64-.143 1.682.731 1.682H29.7c.874 0 1.327-1.043.73-1.682L18.733 2.788Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18 31.073 8.604 21.004h18.795l-9.397 10.07Zm.732 2.148a1 1 0 0 1-1.462 0L5.57 20.687c-.597-.64-.143-1.683.731-1.683H29.7c.874 0 1.327 1.043.73 1.683L18.733 33.22Z" fill="#5900E7"/></svg>',
					'keyword' => ['Scroll navigation', 'slide show', 'slideshow', 'vertical slider'],
				],
				'tp-scroll-sequence' => [
					'label' => esc_html__('Scroll Sequence','tpgb'),
					'demoUrl' => '',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'pro',
					'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M19 6.06189C22.9463 6.55399 26 9.92038 26 14H21V16H26V22C26 26.4183 22.4183 30 18 30C13.5817 30 10 26.4183 10 22V16H15V14H10C10 9.92038 13.0537 6.55399 17 6.06189V11H19V6.06189ZM8 14C8 8.47715 12.4772 4 18 4C23.5228 4 28 8.47715 28 14V22C28 27.5228 23.5228 32 18 32C12.4772 32 8 27.5228 8 22V14Z" fill="#5900E7"/><mask id="path-2-inside-1_188_159" fill="white"><path fill-rule="evenodd" clip-rule="evenodd" d="M17 26.1312L17 23C17 22.4477 17.4477 22 18 22C18.5523 22 19 22.4477 19 23L19 26.1314L20.4454 25.1678C20.9049 24.8614 21.5258 24.9856 21.8322 25.4451C22.1385 25.9046 22.0143 26.5255 21.5548 26.8319L18.0001 29.2017L14.4454 26.8319C13.9859 26.5255 13.8617 25.9046 14.1681 25.4451C14.4744 24.9856 15.0953 24.8614 15.5548 25.1678L17 26.1312Z"/></mask><path d="M17 26.1312L15.8906 27.7953L19 29.8683L19 26.1312L17 26.1312ZM17 23L15 23L15 23L17 23ZM19 23L17 23L17 23L19 23ZM19 26.1314L17 26.1314L17 29.8684L20.1094 27.7955L19 26.1314ZM20.4454 25.1678L19.336 23.5037L19.336 23.5037L20.4454 25.1678ZM21.8322 25.4451L20.1681 26.5545L20.1681 26.5545L21.8322 25.4451ZM21.5548 26.8319L22.6642 28.496L22.6642 28.496L21.5548 26.8319ZM18.0001 29.2017L16.8907 30.8658L18.0001 31.6054L19.1095 30.8658L18.0001 29.2017ZM14.4454 26.8319L13.336 28.496L13.336 28.496L14.4454 26.8319ZM14.1681 25.4451L15.8322 26.5545L15.8322 26.5545L14.1681 25.4451ZM15.5548 25.1678L16.6642 23.5037L16.6642 23.5037L15.5548 25.1678ZM19 26.1312L19 23L15 23L15 26.1312L19 26.1312ZM19 23C19 23.5523 18.5523 24 18 24V20C16.3431 20 15 21.3431 15 23H19ZM18 24C17.4477 24 17 23.5523 17 23H21C21 21.3431 19.6569 20 18 20V24ZM17 23L17 26.1314L21 26.1314L21 23L17 23ZM19.336 23.5037L17.8906 24.4673L20.1094 27.7955L21.5548 26.8319L19.336 23.5037ZM23.4963 24.3357C22.5772 22.9571 20.7146 22.5846 19.336 23.5037L21.5548 26.8319C21.0953 27.1382 20.4744 27.014 20.1681 26.5545L23.4963 24.3357ZM22.6642 28.496C24.0428 27.5769 24.4153 25.7143 23.4963 24.3357L20.1681 26.5545C19.8617 26.095 19.9859 25.4741 20.4454 25.1678L22.6642 28.496ZM19.1095 30.8658L22.6642 28.496L20.4454 25.1678L16.8907 27.5376L19.1095 30.8658ZM13.336 28.496L16.8907 30.8658L19.1095 27.5376L15.5548 25.1678L13.336 28.496ZM12.504 24.3357C11.5849 25.7143 11.9574 27.5769 13.336 28.496L15.5548 25.1678C16.0143 25.4741 16.1385 26.095 15.8322 26.5545L12.504 24.3357ZM16.6642 23.5037C15.2856 22.5846 13.423 22.9571 12.504 24.3357L15.8322 26.5545C15.5258 27.014 14.9049 27.1382 14.4454 26.8319L16.6642 23.5037ZM18.1094 24.4671L16.6642 23.5037L14.4454 26.8319L15.8906 27.7953L18.1094 24.4671Z" fill="#1DD8A7" mask="url(#path-2-inside-1_188_159)"/>
					<path fill-rule="evenodd" clip-rule="evenodd" d="M19 17V13C19 12.4477 18.5523 12 18 12C17.4477 12 17 12.4477 17 13V17C17 17.5523 17.4477 18 18 18C18.5523 18 19 17.5523 19 17ZM18 10C16.3431 10 15 11.3431 15 13V17C15 18.6569 16.3431 20 18 20C19.6569 20 21 18.6569 21 17V13C21 11.3431 19.6569 10 18 10Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Cinematic Scroll Image Animation', 'Video Scroll Sequence', 'Image Scroll Sequence'],
				],
				'tp-search-bar' => [
					'label' => esc_html__('Search Bar', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/advanced-wp-ajax-searchbar/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M21.401 21.416a1 1 0 0 1 1.414 0l9.892 9.877a1 1 0 1 1-1.414 1.415l-9.891-9.878a1 1 0 0 1 0-1.414Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M14.616 5c-1.902 0-3.76.562-5.342 1.617a9.588 9.588 0 0 0-1.46 14.77 9.621 9.621 0 0 0 10.477 2.087 9.613 9.613 0 0 0 4.317-3.534 9.592 9.592 0 0 0-4.31-14.209A9.628 9.628 0 0 0 14.616 5Zm-6.452-.046a11.626 11.626 0 0 1 14.668 1.447 11.599 11.599 0 0 1 3.4 8.209 11.59 11.59 0 0 1-1.962 6.443A11.63 11.63 0 0 1 6.4 22.802a11.597 11.597 0 0 1-2.515-12.643c.88-2.12 2.369-3.93 4.279-5.205Z" fill="#5900E7"/></svg>',
					'keyword' => ['search', 'post search','WordPress Search Bar', 'Find', 'Search Tool', 'SearchWP'],
				],
				'tp-social-icons' => [
					'label' => esc_html__('Social Icon','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/social-icon/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M10.002 6.41A3.583 3.583 0 0 0 6.412 10a3.583 3.583 0 0 0 3.59 3.59A3.583 3.583 0 0 0 13.59 10a3.583 3.583 0 0 0-3.588-3.59Zm0 5.924A2.338 2.338 0 0 1 7.668 10a2.336 2.336 0 0 1 2.334-2.334A2.336 2.336 0 0 1 12.335 10a2.338 2.338 0 0 1-2.333 2.334Zm4.572-6.07a.835.835 0 0 1-.837.837.837.837 0 1 1 .837-.837Zm2.377.85c-.053-1.122-.31-2.115-1.13-2.934-.82-.818-1.812-1.075-2.934-1.13-1.155-.066-4.619-.066-5.774 0C5.994 3.101 5 3.357 4.18 4.176c-.822.818-1.075 1.812-1.13 2.933-.066 1.156-.066 4.62 0 5.776.052 1.122.308 2.116 1.13 2.934.821.818 1.811 1.075 2.933 1.13 1.155.066 4.619.066 5.774 0 1.122-.052 2.115-.308 2.933-1.13.819-.819 1.075-1.812 1.13-2.934.066-1.155.066-4.617 0-5.773Zm-1.493 7.013a2.362 2.362 0 0 1-1.33 1.33c-.922.366-3.108.282-4.126.282-1.019 0-3.208.08-4.126-.282a2.362 2.362 0 0 1-1.33-1.33c-.366-.922-.282-3.109-.282-4.127 0-1.018-.081-3.208.281-4.127a2.362 2.362 0 0 1 1.33-1.33c.922-.366 3.108-.282 4.127-.282 1.018 0 3.207-.08 4.125.281a2.362 2.362 0 0 1 1.33 1.331c.366.922.282 3.109.282 4.127 0 1.018.084 3.208-.281 4.127ZM30.903 21.034A6.885 6.885 0 0 0 25.997 19a6.946 6.946 0 0 0-6.013 10.406L19 33l3.678-.966a6.917 6.917 0 0 0 3.316.844h.003c3.822 0 7.003-3.112 7.003-6.937 0-1.854-.788-3.594-2.097-4.907ZM25.997 31.71a5.755 5.755 0 0 1-2.938-.803l-.209-.125-2.181.572.581-2.128-.137-.219a5.748 5.748 0 0 1-.882-3.069A5.774 5.774 0 0 1 26 20.172c1.54 0 2.988.6 4.075 1.69 1.088 1.091 1.756 2.538 1.753 4.079 0 3.18-2.653 5.768-5.831 5.768Zm3.162-4.318c-.171-.088-1.025-.507-1.184-.563-.16-.06-.275-.087-.39.088a9.978 9.978 0 0 1-.55.68c-.1.116-.204.132-.376.045-1.018-.51-1.687-.91-2.359-2.063-.178-.306.178-.284.51-.947.056-.115.027-.215-.016-.303-.044-.087-.39-.94-.535-1.287-.14-.338-.284-.291-.39-.297-.1-.006-.216-.006-.331-.006a.642.642 0 0 0-.463.215c-.16.175-.606.594-.606 1.447 0 .853.622 1.678.706 1.794.087.115 1.222 1.865 2.962 2.619 1.1.475 1.532.515 2.082.434.334-.05 1.025-.419 1.168-.825.144-.406.144-.753.1-.825-.04-.078-.156-.122-.328-.206Z" fill="#1DD8A7"/><path d="M31.5 3h-11A1.5 1.5 0 0 0 19 4.5v11a1.5 1.5 0 0 0 1.5 1.5h4.29v-4.76h-1.97V10h1.97V8.293c0-1.943 1.155-3.015 2.926-3.015.848 0 1.735.15 1.735.15v1.907h-.977c-.963 0-1.263.598-1.263 1.21V10h2.15l-.344 2.24H27.21V17H31.5a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 31.5 3ZM16.707 22.565a1.774 1.774 0 0 0-1.237-1.266C14.378 21 10 21 10 21s-4.378 0-5.47.3a1.774 1.774 0 0 0-1.237 1.265C3 23.68 3 26.01 3 26.01s0 2.33.293 3.445c.16.616.635 1.081 1.237 1.246C5.622 31 10 31 10 31s4.378 0 5.47-.3a1.747 1.747 0 0 0 1.237-1.244C17 28.338 17 26.01 17 26.01s0-2.329-.293-3.445Zm-8.139 5.56v-4.23l3.66 2.115-3.66 2.115Z" fill="#5900E7"/></svg>',
					'keyword' => ['Social Icon', 'Icon', 'link']
				],
				'tp-social-embed' => [
					'label' => esc_html__('Social Embed','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/social-embed',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="4" y="4" width="28" height="28" rx="1" stroke="#5900E7" stroke-width="2"/><path fill-rule="evenodd" clip-rule="evenodd" d="M19.405 14.048a1.637 1.637 0 0 1 1.013.073c.32.131.584.36.753.65l1.829-.97a3.61 3.61 0 0 0-1.758-1.518 3.818 3.818 0 0 0-2.362-.17 3.677 3.677 0 0 0-1.977 1.246c-.496.613-.765 1.865-.765 2.641v3H14a1 1 0 1 0 0 2h2.138v10h2.092V21h2.139a1 1 0 0 0 0-2H18.23v-3h-2.092 2.092c0-.333.116-1.155.328-1.418.213-.263.511-.45.848-.534Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['iframe', 'facebook feed', 'facebook comments', 'facebook like', 'facebook share', 'facebook page' ]
				],
				'tp-social-feed' => [
					'label' => esc_html__('Social Feed','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/social-feed/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 31a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9.888 16.37A17.998 17.998 0 0 0 4 15.028C3.45 14.998 3 14.552 3 14s.448-1.003 1-.975A19.999 19.999 0 0 1 22.975 32c.028.552-.423 1-.975 1s-.997-.448-1.028-1A17.998 17.998 0 0 0 9.888 16.37Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M13.715 7.131A28 28 0 0 0 4 5.018C3.448 4.998 3 4.552 3 4a.975.975 0 0 1 1-.983A30 30 0 0 1 32.983 32 .975.975 0 0 1 32 33c-.552 0-.998-.448-1.018-1A27.997 27.997 0 0 0 13.715 7.131Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['feed', 'facebook', 'google', 'youtube', 'social', 'posts', 'instagram','vimeo']
				],
				'tp-social-sharing' => [
					'label' => esc_html__('Social Sharing','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/social-sharing/',
					'docUrl' => '#',
					'videoUrl' => '#',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="11" y="19.732" width="2" height="15.52" rx="1" transform="rotate(-60 11 19.732)" fill="#1DD8A7"/><rect width="2" height="15.349" rx="1" transform="scale(1 -1) rotate(-60 -8.234 -17.455)" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M28 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2a5 5 0 1 0 0-10 5 5 0 0 0 0 10ZM28 31a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2a5 5 0 1 0 0-10 5 5 0 0 0 0 10ZM8 21a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0 2a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" fill="#5900E7"/></svg>',
					'keyword' => ['Social Sharing', 'Social Media Sharing']
				],
				'tp-social-reviews' => [
					'label' => esc_html__('Social Reviews','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/social-reviews/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.826 24.31a.2.2 0 0 1 .348 0l.733 1.3a.2.2 0 0 0 .135.098l1.463.295a.2.2 0 0 1 .108.331l-1.01 1.1a.2.2 0 0 0-.052.157l.172 1.483a.2.2 0 0 1-.282.205l-1.358-.62a.2.2 0 0 0-.166 0l-1.358.62a.2.2 0 0 1-.282-.204l.172-1.483a.2.2 0 0 0-.052-.159l-1.01-1.099a.2.2 0 0 1 .108-.331l1.463-.296a.2.2 0 0 0 .135-.097l.733-1.3Zm8 0a.2.2 0 0 1 .348 0l.733 1.3a.2.2 0 0 0 .135.098l1.463.295a.2.2 0 0 1 .108.331l-1.01 1.1a.2.2 0 0 0-.052.157l.172 1.483a.2.2 0 0 1-.282.205l-1.358-.62a.2.2 0 0 0-.166 0l-1.358.62a.2.2 0 0 1-.282-.204l.172-1.483a.2.2 0 0 0-.052-.159l-1.01-1.099a.2.2 0 0 1 .108-.331l1.463-.296a.2.2 0 0 0 .135-.097l.733-1.3Zm8.348 0a.2.2 0 0 0-.348 0l-.733 1.3a.2.2 0 0 1-.135.098l-1.463.295a.2.2 0 0 0-.108.331l1.01 1.1a.2.2 0 0 1 .052.157l-.172 1.483a.2.2 0 0 0 .282.205l1.358-.62a.2.2 0 0 1 .166 0l1.358.62a.2.2 0 0 0 .282-.204l-.172-1.483a.2.2 0 0 1 .052-.159l1.01-1.099a.2.2 0 0 0-.108-.331l-1.463-.296a.2.2 0 0 1-.135-.097l-.733-1.3Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10 5h16v16h-7v-6h1.5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H19v-2h-.001A1 1 0 0 1 20 9.999V10h1V8h-1a3 3 0 0 0-3 3v2h-1.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5H17v6h-7V5ZM8 21V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v16h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h3Zm2 2H5v8h26v-8H10Z" fill="#5900E7"/></svg>',
					'keyword' => ['social', 'reviews', 'rating', 'stars', 'badges']
				],
				'tp-smooth-scroll' => [
					'label' => esc_html__('Smooth Scroll','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/smooth-scroll/',
					'docUrl' => '#',
					'videoUrl' => '#',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.371 2.22a1 1 0 0 1 1.25-.001l10.004 8a1 1 0 1 1-1.25 1.562l-9.379-7.5-9.371 7.5a1 1 0 0 1-1.25-1.562l9.996-8ZM17.371 33.78a1 1 0 0 0 1.25.001l10.004-8a1 1 0 1 0-1.25-1.562l-9.379 7.5-9.371-7.5a1 1 0 0 0-1.25 1.562l9.996 8Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M19 11h-2a4 4 0 0 0-4 4v6a4 4 0 0 0 4 4h2a4 4 0 0 0 4-4v-6a4 4 0 0 0-4-4Zm-2-2a6 6 0 0 0-6 6v6a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-6a6 6 0 0 0-6-6h-2Z" fill="#1DD8A7"/><path d="M17 14a1 1 0 1 1 2 0v2a1 1 0 1 1-2 0v-2Z" fill="#1DD8A7"/></svg>',
				],
				'tp-switcher' => [
					'label' => esc_html__('Switcher','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/switcher/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M26 5H10a5 5 0 0 0 0 10h16a5 5 0 0 0 0-10ZM10 3a7 7 0 0 0 0 14h16a7 7 0 1 0 0-14H10ZM26 21H10a5 5 0 0 0 0 10h16a5 5 0 0 0 0-10Zm-16-2a7 7 0 1 0 0 14h16a7 7 0 1 0 0-14H10Z" fill="#5900E7"/><path d="M30 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10 28a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Switcher', 'on/off', 'switch control', 'toggle', 'true/false', 'toggle switch', 'state', 'binary']
				],
				'tp-table-content' => [
					'label' => esc_html__('Table of Contents','tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/table-of-contents/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><rect x="2" y="30" width="32" height="2" rx="1" fill="#5900E7"/><rect x="2" y="4" width="32" height="2" rx="1" fill="#5900E7"/><rect x="12" y="13" width="22" height="2" rx="1" fill="#1DD8A7"/><rect x="12" y="21" width="22" height="2" rx="1" fill="#1DD8A7"/><path d="M9.572 17.412a1 1 0 0 1 0 1.176L3.81 26.513c-.57.783-1.809.38-1.809-.588v-15.85c0-.968 1.24-1.371 1.809-.588l5.763 7.925Z" fill="#1DD8A7"/></svg>',
					'keyword' => [ 'Table of Contents', 'Contents', 'toc', 'index', 'listing', 'appendix' ]
				],
				'tp-tabs-tours' => [
					'label' => esc_html__('Tabs Tours', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/tabs-tours/',
					'docUrl' => '#doc',
					'videoUrl' => '#video',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 6a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h16.5v2H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v2.417a1 1 0 0 0 1 1h16a3 3 0 0 1 3 3v5.791h-2v-5.791a1 1 0 0 0-1-1H11a3 3 0 0 1-3-3V7a1 1 0 0 0-1-1H5Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27 30a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" fill="#1DD8A7"/><rect width="1.98" height="7.92" rx=".99" transform="matrix(1 0 0 1 26.01 21.04)" fill="#1DD8A7"/><rect width="1.98" height="7.92" rx=".99" transform="matrix(0 1 -1 0 30.96 24.01)" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M20 7a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v2a1 1 0 1 1-2 0V7a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 1 1-2 0V7ZM11 7a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3v2a1 1 0 1 1-2 0V7a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 1 1-2 0V7Z" fill="#5900E7"/></svg>',
					'keyword' => ['Tabs', 'Tours', 'tab content', 'pills', 'toggle']
				],
				'tp-team-listing' => [
					'label' => esc_html__('Team Member', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-listing/#team-member',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.731 14.015a5.811 5.811 0 0 1 2.336-.491H9.64c.56 0 1.015.462 1.015 1.03 0 .57-.455 1.032-1.015 1.032H8.067a3.81 3.81 0 0 0-1.532.322 4.029 4.029 0 0 0-1.309.927 4.473 4.473 0 0 0-1.197 3.07c0 .569-.454 1.03-1.015 1.03-.56 0-1.014-.461-1.014-1.03 0-1.68.63-3.298 1.762-4.497a6.059 6.059 0 0 1 1.97-1.393Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.76 15.973a3.81 3.81 0 0 0-1.67-.387H7.506c-.56 0-1.014-.462-1.014-1.031 0-.57.454-1.031 1.014-1.031h1.581c.882 0 1.752.201 2.55.59a6.1 6.1 0 0 1 2.069 1.653c.356.44.293 1.09-.14 1.451a1.003 1.003 0 0 1-1.427-.142 4.069 4.069 0 0 0-1.38-1.103Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M8.906 8.569c-1.232 0-2.302 1.07-2.302 2.48 0 1.411 1.07 2.482 2.302 2.482 1.233 0 2.303-1.07 2.303-2.481 0-1.41-1.07-2.481-2.303-2.481Zm-4.33 2.48c0-2.468 1.9-4.542 4.33-4.542 2.431 0 4.332 2.074 4.332 4.543 0 2.468-1.9 4.543-4.332 4.543-2.43 0-4.33-2.075-4.33-4.543Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25.344 14.547c0-.569.454-1.03 1.015-1.03h1.581c.802 0 1.596.167 2.335.491a6.05 6.05 0 0 1 1.968 1.393 6.404 6.404 0 0 1 1.303 2.07c.301.77.455 1.595.454 2.427 0 .57-.456 1.03-1.016 1.03-.56-.001-1.014-.463-1.013-1.033a4.57 4.57 0 0 0-.31-1.665 4.34 4.34 0 0 0-.883-1.402 4.024 4.024 0 0 0-1.308-.927 3.803 3.803 0 0 0-1.53-.323H26.36c-.56 0-1.015-.461-1.015-1.03Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M26.919 13.517h1.574c.56 0 1.015.461 1.015 1.03 0 .57-.455 1.031-1.015 1.031H26.92m0-2.061c-.884 0-1.755.203-2.554.593l2.554-.593Zm0 2.061c-.577 0-1.149.133-1.676.39-.527.258-1 .635-1.382 1.108-.356.44-.995.504-1.428.142a1.042 1.042 0 0 1-.14-1.45 6.117 6.117 0 0 1 2.072-1.658" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M27.1 8.562c-1.232 0-2.302 1.07-2.302 2.48 0 1.412 1.07 2.482 2.302 2.482 1.233 0 2.303-1.07 2.303-2.481 0-1.41-1.07-2.481-2.303-2.481Zm-4.331 2.48c0-2.468 1.9-4.542 4.331-4.542 2.431 0 4.332 2.074 4.332 4.543 0 2.468-1.9 4.543-4.332 4.543-2.43 0-4.331-2.075-4.331-4.543Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M13.999 21.102a7.241 7.241 0 0 1 2.913-.613h2.05c2.024 0 3.956.851 5.373 2.352 1.417 1.5 2.205 3.525 2.205 5.628 0 .57-.454 1.031-1.014 1.031s-1.014-.461-1.014-1.03c0-1.584-.595-3.094-1.64-4.201-1.045-1.106-2.452-1.719-3.91-1.719h-2.05c-.723 0-1.439.15-2.109.445a5.532 5.532 0 0 0-1.798 1.274 5.952 5.952 0 0 0-1.21 1.921 6.254 6.254 0 0 0-.426 2.278c.001.57-.453 1.032-1.013 1.032-.56 0-1.015-.46-1.015-1.03a8.344 8.344 0 0 1 .568-3.04 8.018 8.018 0 0 1 1.632-2.588 7.56 7.56 0 0 1 2.458-1.74Z" fill="#5900E7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.003 13.437c-1.777 0-3.289 1.538-3.289 3.526 0 1.988 1.512 3.526 3.29 3.526 1.777 0 3.289-1.538 3.289-3.526 0-1.988-1.512-3.526-3.29-3.526Zm-5.318 3.526c0-3.046 2.343-5.588 5.318-5.588 2.977 0 5.319 2.542 5.319 5.588 0 3.045-2.342 5.587-5.319 5.587-2.975 0-5.318-2.542-5.318-5.587Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['Team Member Gallery', 'Team Gallery', 'Team Member Carousel']
				],
				'tp-testimonials' => [
					'label' => esc_html__('Testimonials', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-listing/testimonials/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'freemium',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.826 24.31a.2.2 0 0 1 .348 0l.733 1.3a.2.2 0 0 0 .135.098l1.463.295a.2.2 0 0 1 .108.331l-1.01 1.1a.2.2 0 0 0-.052.157l.172 1.484a.2.2 0 0 1-.282.204l-1.358-.62a.2.2 0 0 0-.166 0l-1.358.62a.2.2 0 0 1-.282-.204l.172-1.483a.2.2 0 0 0-.052-.159l-1.01-1.099a.2.2 0 0 1 .108-.331l1.463-.296a.2.2 0 0 0 .135-.097l.733-1.3Zm8 0a.2.2 0 0 1 .348 0l.733 1.3a.2.2 0 0 0 .135.098l1.463.295a.2.2 0 0 1 .108.331l-1.01 1.1a.2.2 0 0 0-.052.157l.172 1.484a.2.2 0 0 1-.282.204l-1.358-.62a.2.2 0 0 0-.166 0l-1.358.62a.2.2 0 0 1-.282-.204l.172-1.483a.2.2 0 0 0-.052-.159l-1.01-1.099a.2.2 0 0 1 .108-.331l1.463-.296a.2.2 0 0 0 .135-.097l.733-1.3Zm8.348 0a.2.2 0 0 0-.348 0l-.733 1.3a.2.2 0 0 1-.135.098l-1.463.295a.2.2 0 0 0-.108.331l1.01 1.1a.2.2 0 0 1 .052.157l-.172 1.484a.2.2 0 0 0 .282.204l1.358-.62a.2.2 0 0 1 .166 0l1.358.62a.2.2 0 0 0 .282-.204l-.172-1.483a.2.2 0 0 1 .052-.159l1.01-1.099a.2.2 0 0 0-.108-.331l-1.463-.296a.2.2 0 0 1-.135-.097l-.733-1.3Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10 5h16v16h-1.222c-.31-1.372-.937-2.638-1.828-3.657a7.064 7.064 0 0 0-2.027-1.612 4 4 0 1 0-5.846 0 7.064 7.064 0 0 0-2.027 1.612c-.891 1.02-1.518 2.285-1.828 3.657H10V5Zm3.288 16h9.424a6.255 6.255 0 0 0-1.267-2.34C20.48 17.558 19.233 17 18 17c-1.233 0-2.48.558-3.445 1.66A6.255 6.255 0 0 0 13.288 21ZM11 23H5v8h26v-8H11Zm7-8a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM8 21V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v16h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h3Z" fill="#5900E7"/></svg>',
					'keyword' => ['Testimonials', 'testimonial', 'slider', 'client reviews', 'ratings']
				],
				'tp-timeline' => [
					'label' => esc_html__('Timeline', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/timeline/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'pro',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path d="M17 3a1 1 0 1 1 2 0v30a1 1 0 1 1-2 0V3Z" fill="#5900E7"/><path d="M20 27a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M31 5h-6.991l-1.708 4 1.708 4H31V5ZM20 9l2.27 4.99A2 2 0 0 0 24.01 15H31a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-6.991a2 2 0 0 0-1.738 1.01L20 9ZM5 23h6.991l1.708 4-1.708 4H5v-8Zm11 4-2.27 4.99A2 2 0 0 1 11.99 33H5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h6.991a2 2 0 0 1 1.738 1.01L16 27Z" fill="#5900E7"/><path d="M25 9a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1ZM11 27a1 1 0 0 0-1-1H7a1 1 0 1 0 0 2h3a1 1 0 0 0 1-1Z" fill="#1DD8A7"/></svg>',
					'keyword' => ['timeline']
				],
				'tp-video' => [
					'label' => esc_html__('TP Video', 'tpgb'),
					'demoUrl' => 'https://theplusblocks.com/plus-blocks/video/',
					'docUrl' => '',
					'videoUrl' => '',
					'tag' => 'free',
					'icon' => '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill-rule="evenodd" clip-rule="evenodd" d="M27 6H5a1 1 0 0 0-1 1v18a1 1 0 0 0 1 1h22a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1ZM5 4a3 3 0 0 0-3 3v18a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H5Z" fill="#5900E7"/><path d="M17.752 15.168a1 1 0 0 1 0 1.664l-3.197 2.131A1 1 0 0 1 13 18.131v-4.263a1 1 0 0 1 1.555-.832l3.197 2.132Z" fill="#1DD8A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M33 8a1 1 0 0 1 1 1v19a4 4 0 0 1-4 4H7a1 1 0 1 1 0-2h23a2 2 0 0 0 2-2V9a1 1 0 0 1 1-1Z" fill="#5900E7"/></svg>',
					'keyword' => ['Video', 'youtube video', 'vimeo video', 'video player', 'mp4 player', 'web player', 'youtube content', 'Youtube embed', 'youtube iframe']
				],
			];
	}
	
	/* Get Block Filter Search Ajax
	 * @since v1.0.0 
	 */
	public function tpgb_block_search(){
		check_ajax_referer('tpgb-addons', 'security');
		if(isset($_POST['filter']) && !empty($_POST['filter'])){
			$this->block_listout();
			$filter_block =[];
			if(!empty($this->block_lists)){
				
				foreach($this->block_lists as $key => $block){
					$label = strtolower($block['label']);
					$filter_block[$key] = $block;
					$filter_block[$key]['filter'] = 'no';
					if(!empty($block['keyword'])){
						foreach($block['keyword'] as $keyword){
							$key_word= strtolower($keyword);
							if(strpos($key_word, sanitize_text_field($_POST['filter'])) !== false){
								$filter_block[$key]['filter'] = 'yes';
							}
						}
					}
					if(strpos($label, sanitize_text_field($_POST['filter'])) !== false){
						$filter_block[$key]['filter'] = 'yes';
					}
				}
			}
			$this->block_lists = $filter_block;
			
		}else{
			$this->block_listout();
		}
		
		$output = $this->tpgb_block_list_rendered();
		echo $output;
		exit();
	}
	
	/*
	 * Gutenberg default block list
	 * @since 1.4.4
	 */
	private function tpgb_default_block_list_rendered(){
		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$output = '';
		$get_blocks_save = get_option( 'tpgb_default_load_blocks' );
		$save_block ='';
		if(!empty($get_blocks_save['enable_default_blocks'])){
			$save_block = $get_blocks_save['enable_default_blocks'];
		}
		$deactivate_block_list = [];
		if( !empty($block_types) ){
			foreach($block_types as $key => $block){
				if(str_contains($key, 'core/')){
					if( $key !='core/missing' && $key !='core/block'&& $key !='core/widget-group' && $key != 'core/navigation-link' && $key != 'core/navigation-submenu' && $key != 'core/social-link' && !empty($block->title)){
						$pass_key = str_replace( 'core/', 'core-', $key );
						$output .='<div class="tpgb-panel-col tpgb-panel-col-25">';
							$output .='<div class="plus-block-list-wrap" data-list-block="'.esc_attr($pass_key).'">';
								$output .='<div class="plus-block-list-inner">';
									$output .='<span>'.esc_html($block->title).'</span>';
									if($key==='core/navigation'){
										$output .='<div class="plus-sub-child-block">'.esc_html__('(Submenu, Custom Links)','tpgb').'</div>';
									}
									if($key==='core/social-links'){
										$output .='<div class="plus-sub-child-block">'.esc_html__('(Social Icon)','tpgb').'</div>';
									}
								$output .='</div>';
								$checked = '';
								if(!empty($save_block) && in_array($key, $save_block)){
									$checked = 'checked="checked"';
								}else if(empty($get_blocks_save)){
									$checked = 'checked="checked"';
								}else{
									$deactivate_block_list[] = $key;
									if($key==='core/navigation'){
										$deactivate_block_list[] = 'core/navigation-link';
										$deactivate_block_list[] = 'core/navigation-submenu';
									}
									if($key==='core/social-links'){
										$deactivate_block_list[] = 'core/social-link';
									}
								}
								$output .='<div class="block-check-wrap"><input type="checkbox" class="block-list-checkbox" name="enable_default_blocks[]" id="'.esc_attr(str_replace("/","-",$key)).'" value="'.esc_attr($key).'" '.$checked.'> <label for="'.esc_attr(str_replace("/","-",$key)).'"></label></div>';
							$output .='</div>';
						$output .='</div>';
					}
				}
			}
			
			if(!empty($deactivate_block_list) && !empty($get_blocks_save) ){
				$get_blocks_save['disable_default_blocks'] = $deactivate_block_list;
				update_option( 'tpgb_default_load_blocks', $get_blocks_save );
			}
		}

		return $output;
	}

	private function tpgb_block_list_rendered(){
		$block_list = $this->block_lists;
		$output ='';
		$get_blocks_save = get_option( 'tpgb_normal_blocks_opts' );
		$save_block ='';
		if(!empty($get_blocks_save['enable_normal_blocks'])){
			$save_block = $get_blocks_save['enable_normal_blocks'];
		}
		
		if(!empty($block_list)){
			foreach ($block_list as $key => $block){
				$filter_class = '';
				if(!empty($block['filter'])){
					$filter_class = 'filter-block-'.esc_attr($block['filter']);
				}
				$output .='<div class="tpgb-panel-col tpgb-panel-col-25 block-'.esc_attr($block['tag']).' '.esc_attr($filter_class).'">';
					$output .='<div class="plus-block-list-wrap" data-list-block="'.esc_attr($key).'">';
						$output .='<div class="block-pin-free-pro">'.esc_html($block['tag']).'</div>';
						$output .='<div class="plus-block-list-inner">';
							$output .= (!empty($block['icon'])) ? '<span class="block-icon">'.$block['icon'].'</span>' : '';
							$output .='<span>'.esc_html($block['label']).'</span>';
							$output .='<span class="block-group-info">';
								$output .='<span class="block-hover-info">';
								$output .='<svg xmlns="http://www.w3.org/2000/svg" width="3.75" height="10.8" viewBox="0 0 4.449 11.917"><defs/><path d="M3.604 3.602H.061v1.5h.962v5.364H0v1.435h1.021v.01h2.583v-.01h.845v-1.429h-.845z" data-name="Path 348"/><path d="M2.294 2.582A1.294 1.294 0 102.329 0a1.293 1.293 0 10-.035 2.582z" data-name="Path 349"/></svg>';
							$output .='</span>';
							$output .='<a href="'.esc_url($block['demoUrl']).'" target="_blank" class="block-hover-details block-info-demo">';
								$output .='<svg xmlns="http://www.w3.org/2000/svg" width="10" height="9.009"><defs/><path d="M9.755.257a.787.787 0 00-.589-.255H.833a.788.788 0 00-.589.255A.851.851 0 000 .866v5.891a.851.851 0 00.245.612.788.788 0 00.589.254h2.833a1.171 1.171 0 01-.083.422 3.905 3.905 0 01-.167.379.614.614 0 00-.083.238.339.339 0 00.1.244.314.314 0 00.234.1h2.665a.314.314 0 00.234-.1.339.339 0 00.1-.244.635.635 0 00-.083-.235 4.052 4.052 0 01-.167-.384 1.18 1.18 0 01-.083-.42h2.833a.787.787 0 00.588-.254.851.851 0 00.245-.612V.867a.851.851 0 00-.245-.61zm-.422 5.116a.17.17 0 01-.049.122.158.158 0 01-.117.051H.833a.157.157 0 01-.117-.051.17.17 0 01-.049-.122v-4.5A.17.17 0 01.716.751.158.158 0 01.833.7h8.334a.157.157 0 01.117.051.17.17 0 01.049.122v4.5z"/></svg>';
							$output .='</a>';
							/*$output .='<a href="'.esc_url($block['docUrl']).'" target="_blank" class="block-hover-details block-info-doc">';
								$output .='<svg xmlns="http://www.w3.org/2000/svg" width="8.053" height="10.166" viewBox="0 0 8.053 10.166">
									<g transform="translate(-41.796)">
										<g transform="translate(42.06 1.188)">
										  <path d="M226.884,303.02l-2.231,2.218v-1.69a.528.528,0,0,1,.528-.528Z" transform="translate(-220.296 -296.551)"/>
										</g>
										<g transform="translate(41.796)">
										  <path d="M46.39,45.813h-3.8a.792.792,0,0,1-.792-.792V37.363a.792.792,0,0,1,.792-.792h5.545a.792.792,0,0,1,.792.792v5.928a.264.264,0,0,1-.079.185h-.013L46.575,45.72A.264.264,0,0,1,46.39,45.813Zm-3.8-8.713a.264.264,0,0,0-.264.264V45.02a.264.264,0,0,0,.264.264h3.7l2.112-2.1V37.363a.264.264,0,0,0-.264-.264Z" transform="translate(-41.796 -35.647)"/>
										  <path d="M214.468,295.344a.264.264,0,0,1-.264-.264v-1.716a.792.792,0,0,1,.792-.792h1.716a.264.264,0,1,1,0,.528H215a.264.264,0,0,0-.264.264v1.716A.264.264,0,0,1,214.468,295.344Z" transform="translate(-209.847 -285.179)"/>
										  <path d="M137.2,206.9h-3.656a.269.269,0,1,1,0-.528H137.2a.269.269,0,1,1,0,.528Z" transform="translate(-131.798 -201.152)" />
										  <path d="M137.2,154.65h-3.656a.269.269,0,1,1,0-.528H137.2a.269.269,0,1,1,0,.528Z" transform="translate(-131.798 -150.227)" />
										  <path d="M137.2,102.406h-3.656a.269.269,0,1,1,0-.528H137.2a.269.269,0,1,1,0,.528Z" transform="translate(-131.798 -99.304)" />
										  <path d="M85.232,7.512a.264.264,0,0,1-.264-.264V1.056A.528.528,0,0,0,84.44.528H78.631a.264.264,0,1,1,0-.528H84.44A1.056,1.056,0,0,1,85.5,1.056V7.248A.264.264,0,0,1,85.232,7.512Z" transform="translate(-77.443)" />
										</g>
									</g>
									</svg>';
							$output .='</a>';*/
							$output .='<a href="'.esc_url($block['videoUrl']).'" target="_blank" class="block-hover-details block-info-video">';
								$output .='<svg xmlns="http://www.w3.org/2000/svg" width="7.801" height="10.037"><defs/><path d="M.4 10.036a.4.4 0 01-.4-.4V.399A.4.4 0 01.62.065l7 4.618a.4.4 0 01.181.334.4.4 0 01-.181.334l-7 4.619a.4.4 0 01-.22.066zm.4-8.894v7.749l5.874-3.876z"/></svg>';
							$output .='</a>';
							$output .='</span>';
						$output .='</div>';
						$pro_disable = '';
						if(!defined('TPGBP_VERSION') && $block['tag']=='pro'){
							$pro_disable = 'disabled="disabled"';
						}
						$checked = '';
						if(!empty($save_block) && in_array($key, $save_block)){
							$checked = 'checked="checked"';
						}
						
						$output .='<div class="block-check-wrap"><input type="checkbox" class="block-list-checkbox" name="enable_normal_blocks[]" id="'.esc_attr($key).'" value="'.esc_attr($key).'" '.$checked.' '.$pro_disable.'> <label for="'.esc_attr($key).'"></label></div>';
					$output .='</div>';
				$output .='</div>';
			}
		}
		return $output;
	}
	
	/**
     * Theplus Gutenberg Display Page
     * @since  1.0.0
     */
    public function admin_page_display() {
		$option_tabs = self::option_fields();
		$tab_forms   = array();
		
		$output ='';
		
		$output .='<div class="'.esc_attr($this->key).'">';
			
			$output .='<div id="tpgb-setting-header-wrapper">';
				$output .='<div class="tpgb-head-inner">';
				
					$options = get_option( 'tpgb_white_label' );
					if(defined('TPGBP_VERSION') && (!empty($options['tpgb_plus_logo']))){
						$output .='<img src="'.esc_url($options['tpgb_plus_logo']).'" style="max-width:150px;"/>';
					}else{
						$output .='<svg xmlns="http://www.w3.org/2000/svg" width="250" viewBox="0 0 976.07 265.4"><defs/><g fill="#fff"><g opacity=".4"><path d="M139.14 108.15h-12.86v18.12h-18.13v12.86h18.13v18.13h12.86v-18.13h18.11v-12.86h-18.11v-18.12"/><path d="M132.7 0H84.18v126.27H45v12.86h39.18v18.12H97V12.86h35.7a35.73 35.73 0 0135.66 35.46v24.86h12.86V48.31A48.56 48.56 0 00132.7 0"/><path d="M139.13 45h-12.86v39.18h-18.12V97h144.39v35.7a35.73 35.73 0 01-35.46 35.66h-24.85v12.86h24.88a48.53 48.53 0 0048.29-48.52V84.18H139.13V45"/><path d="M181.23 108.15h-12.86v144.39h-35.66a35.73 35.73 0 01-35.66-35.46v-24.85H84.19v24.87a48.53 48.53 0 0048.52 48.3h48.52V139.13h39.15v-12.86h-39.15v-18.12"/><path d="M73.18 84.19H48.31A48.56 48.56 0 000 132.71v48.52h126.27v39.15h12.86v-39.15h18.12v-12.86H12.86v-35.66a35.72 35.72 0 0135.47-35.66h24.85V84.19"/></g><path d="M97 12.86h35.7a35.73 35.73 0 0135.66 35.46v24.86h12.86V48.31A48.56 48.56 0 00132.7 0H84.18v157.25H97z"/><path d="M170.3 126.27h-44v12.86h42.09v113.41h-35.68a35.73 35.73 0 01-35.66-35.46v-24.85H84.19v24.87a48.53 48.53 0 0048.52 48.3h48.52V126.27zM393.3 44.93h-79.65v16.32h29.55v81.46h20.15V61.25h29.95V44.93M490 44.93h-20.14v39.62h-39.29V44.93h-20.15v97.78h20.15V100.8h39.29v41.91H490V44.93M578.65 44.93h-65.41v97.78h65.54v-16.18h-45.39v-26.2h38.68V84.55h-38.68v-23.3h45.26V44.93M656.28 91.94V61.25h18.46q7.46.14 11.69 4.57t4.23 11.61q0 7-4.19 10.75t-12.2 3.76h-18m18-47h-38.14v97.78h20.15v-34.46h17.66q17.38 0 27.23-8.3T711 77.3a31.94 31.94 0 00-4.5-16.89A29.72 29.72 0 00693.65 49a44.1 44.1 0 00-19.38-4M750.63 44.93h-20.15v97.78h62.93v-16.18h-42.78v-81.6M883 44.93h-20.22V110q-.27 17.86-17.26 17.87-8.13 0-12.73-4.4t-4.6-14V44.93h-20.14v65.14q.19 15.86 10.27 24.92t27.2 9.06q17.38 0 27.43-9.33t10-25.39v-64.4M940.14 43.59a48.23 48.23 0 00-18.77 3.49 29.2 29.2 0 00-12.83 9.7 23.62 23.62 0 00-4.46 14.14q0 15.24 16.65 24.24a102.14 102.14 0 0016.59 6.69q10.47 3.39 14.5 6.44a10.33 10.33 0 014 8.76 9.51 9.51 0 01-4 8.17q-4 2.91-11.21 2.92-19.33 0-19.34-16.19h-20.22a29 29 0 004.87 16.66 32.51 32.51 0 0014.14 11.31 50 50 0 0020.55 4.13q16.25 0 25.86-7.22t9.6-19.91a25.53 25.53 0 00-7.86-19.07q-7.86-7.66-25.05-12.85-9.33-2.8-14.16-6t-4.81-8a9.86 9.86 0 014.1-8.23q4.09-3.12 11.48-3.12 7.66 0 11.89 3.72t4.23 10.45h20.15a28 28 0 00-4.54-15.72 29.72 29.72 0 00-12.72-10.71 44 44 0 00-18.67-3.79M354.12 216.21a6.32 6.32 0 01-4.23-1.39 4.79 4.79 0 01-1.65-3.86q0-5.66 9.65-5.65H362v6.3a7.7 7.7 0 01-3.21 3.32 9.11 9.11 0 01-4.68 1.28m1.7-27.28a15 15 0 00-6.15 1.25 11.2 11.2 0 00-4.42 3.39 7.24 7.24 0 00-1.63 4.44h5.28a4.18 4.18 0 011.9-3.43 7.62 7.62 0 014.71-1.42 6.6 6.6 0 014.85 1.63 5.91 5.91 0 011.65 4.39v2.41h-5.1q-6.61 0-10.26 2.66a8.68 8.68 0 00-3.64 7.45 8.3 8.3 0 002.91 6.51 10.87 10.87 0 007.45 2.57 11.72 11.72 0 008.74-3.8 11.14 11.14 0 00.74 3.23h5.51v-.45a17.29 17.29 0 01-1.08-6.73V198.9a9.6 9.6 0 00-3.12-7.31q-3-2.66-8.32-2.66M391.65 216.32a7 7 0 01-6-2.95q-2.16-3-2.16-8.17 0-5.85 2.16-8.83a7 7 0 016-3 7.74 7.74 0 017.24 4.34v14.11a7.67 7.67 0 01-7.3 4.48m12.55-39.7H399v16a11.4 11.4 0 00-17.32.61q-3.35 4.31-3.35 11.37v.4q0 7 3.36 11.41a10.52 10.52 0 008.71 4.37 10.71 10.71 0 008.77-3.86l.25 3.29h4.83v-43.57M428.5 216.32a7 7 0 01-6-2.95c-1.44-2-2.15-4.69-2.15-8.17 0-3.9.71-6.84 2.15-8.83a7 7 0 016-3 7.73 7.73 0 017.24 4.34v14.11a7.67 7.67 0 01-7.3 4.48m12.55-39.7h-5.19v16a11.4 11.4 0 00-17.32.61q-3.35 4.31-3.35 11.37v.4q0 7 3.37 11.41a10.49 10.49 0 008.7 4.37 10.71 10.71 0 008.77-3.86l.25 3.29h4.83v-43.57M465.87 216.49a7.6 7.6 0 01-6.35-3.09 13.14 13.14 0 01-2.37-8.2c0-3.84.8-6.8 2.38-8.86a8 8 0 0112.65 0 13.11 13.11 0 012.4 8.19q0 5.66-2.36 8.79a7.53 7.53 0 01-6.35 3.13m-.06-27.56a13.45 13.45 0 00-7.22 2 13.19 13.19 0 00-4.94 5.56 18.07 18.07 0 00-1.78 8.12v.37q0 7.16 3.88 11.49a12.9 12.9 0 0010.12 4.35 13.54 13.54 0 007.33-2 13 13 0 004.91-5.55 18.18 18.18 0 001.72-8v-.37q0-7.22-3.87-11.55a13 13 0 00-10.15-4.35M504.82 188.93a11.2 11.2 0 00-9.2 4.43l-.17-3.86h-5v30.71h5.25v-21.88a9.32 9.32 0 013-3.58 7.59 7.59 0 014.49-1.36 6.09 6.09 0 014.64 1.59 7.1 7.1 0 011.52 4.91v20.32h5.25v-20.29q-.09-11-9.82-11M537.75 188.93a12.48 12.48 0 00-8.16 2.61 8 8 0 00-3.19 6.39 6.79 6.79 0 001.12 3.92 9.06 9.06 0 003.46 2.84 26.74 26.74 0 006.33 2 15.38 15.38 0 015.59 2 3.87 3.87 0 011.61 3.31 3.7 3.7 0 01-1.81 3.22 8.48 8.48 0 01-4.78 1.2 8.22 8.22 0 01-5.21-1.54 5.45 5.45 0 01-2.11-4.19h-5.25a8.73 8.73 0 001.6 5.06 10.57 10.57 0 004.44 3.65 15.47 15.47 0 006.53 1.31 13.83 13.83 0 008.54-2.48 7.87 7.87 0 003.3-6.6 7.26 7.26 0 00-1.18-4.19 9.33 9.33 0 00-3.62-2.94 28.72 28.72 0 00-6.37-2.1 18.57 18.57 0 01-5.44-1.84 3.2 3.2 0 01-1.5-2.87 3.94 3.94 0 011.61-3.26 7.15 7.15 0 014.49-1.25 6.85 6.85 0 014.61 1.56 4.74 4.74 0 011.83 3.72h5.28a8.56 8.56 0 00-3.25-6.9 12.81 12.81 0 00-8.47-2.7M591.81 176q-4.81 0-7.46 2.69c-1.76 1.81-2.64 4.35-2.64 7.64v3.15h-4.86v4.06h4.86v26.65H587v-26.63h6.56v-4.06H587v-3.25a6 6 0 011.39-4.28 5.1 5.1 0 013.95-1.5 15 15 0 012.83.26l.29-4.23a13.88 13.88 0 00-3.61-.48M616 216.49a7.6 7.6 0 01-6.35-3.09 13.14 13.14 0 01-2.37-8.2c0-3.84.8-6.8 2.39-8.86a8 8 0 0112.64 0 13.11 13.11 0 012.4 8.19c0 3.77-.79 6.7-2.35 8.79a7.56 7.56 0 01-6.36 3.13m-.06-27.56a13.47 13.47 0 00-7.22 2 13.25 13.25 0 00-4.94 5.56 18.2 18.2 0 00-1.78 8.12v.37q0 7.16 3.88 11.49a12.91 12.91 0 0010.12 4.35 13.54 13.54 0 007.33-2 13 13 0 004.91-5.55 18.18 18.18 0 001.72-8v-.37q0-7.22-3.87-11.55a13 13 0 00-10.15-4.35M653.11 188.93a8.16 8.16 0 00-7.32 4.12l-.09-3.55h-5.1v30.71h5.25v-21.8c1.23-2.93 3.56-4.4 7-4.4a16.08 16.08 0 012.58.2v-4.88a5.45 5.45 0 00-2.33-.4M698.89 178.32q-7.89 0-12.25 5.14t-4.36 14.47v3.6a23.32 23.32 0 002.2 10.14 15.83 15.83 0 006.06 6.74 17 17 0 009 2.37 23.78 23.78 0 008.76-1.49 12.57 12.57 0 005.86-4.5v-15.16h-15v4.46h9.56v9.26a8.17 8.17 0 01-3.8 2.29 18.72 18.72 0 01-5.36.68 10.3 10.3 0 01-8.6-4.1q-3.18-4.11-3.18-11.07v-3.38q0-7.29 2.85-11.15t8.3-3.85q8.24 0 9.77 8.23h5.45a14.46 14.46 0 00-4.83-9.38q-3.94-3.3-10.41-3.3M750.52 189.5h-5.25v22.34q-2 4.49-8 4.48-5.68 0-5.68-7V189.5h-5.25v20c0 3.73.88 6.55 2.6 8.45s4.2 2.85 7.48 2.85q5.93 0 9-3.61l.12 3h5V189.5M769.69 182.07h-5.25v7.43h-5.59v4.06h5.59v19a9.17 9.17 0 001.79 6.05 6.53 6.53 0 005.31 2.13 15.19 15.19 0 004-.57V216a12.46 12.46 0 01-2.5.34 3.24 3.24 0 01-2.61-.92 4.28 4.28 0 01-.77-2.77v-19.09h5.74v-4.06h-5.74v-7.43M789.71 202.05a11.24 11.24 0 012.67-6.49 7 7 0 015.34-2.31 6.59 6.59 0 015.22 2.2 10 10 0 012.16 6.2v.4h-15.39m8-13.12a12.31 12.31 0 00-6.8 2 13.6 13.6 0 00-4.88 5.62 18.35 18.35 0 00-1.75 8.16v1q0 6.87 3.92 11a13.43 13.43 0 0010.16 4.1q7.73 0 11.58-5.93l-3.21-2.5a11.77 11.77 0 01-3.38 3 9.18 9.18 0 01-4.71 1.13 8.33 8.33 0 01-6.45-2.79 10.92 10.92 0 01-2.66-7.34h20.81v-2.19q0-7.35-3.32-11.29t-9.31-3.95M834.82 188.93a11.18 11.18 0 00-9.19 4.43l-.17-3.86h-5v30.71h5.25v-21.88a9.23 9.23 0 013-3.58 7.56 7.56 0 014.48-1.36 6.08 6.08 0 014.64 1.59 7 7 0 011.52 4.91v20.32h5.25v-20.29q-.07-11-9.82-11M869.4 216.32a7.92 7.92 0 01-7.52-4.82v-13.29a7.71 7.71 0 017.47-4.82 6.85 6.85 0 016 2.92q2.07 2.92 2.07 8.29 0 5.88-2.1 8.8a6.82 6.82 0 01-5.88 2.92m-7.52-39.7h-5.25v43.59h4.83l.25-3.55a10.7 10.7 0 009 4.12 10.32 10.32 0 008.68-4.27q3.24-4.27 3.24-11.31v-.46q0-7.35-3.19-11.58a10.41 10.41 0 00-8.79-4.23 10.66 10.66 0 00-8.77 3.95v-16.26M897.4 202.05a11.24 11.24 0 012.67-6.49 7 7 0 015.33-2.31 6.6 6.6 0 015.23 2.2 10 10 0 012.15 6.2v.4H897.4m8-13.12a12.27 12.27 0 00-6.79 2 13.62 13.62 0 00-4.89 5.62 18.49 18.49 0 00-1.74 8.16v1q0 6.87 3.91 11a13.45 13.45 0 0010.17 4.1q7.71 0 11.58-5.93l-3.21-2.5a11.9 11.9 0 01-3.38 3 9.2 9.2 0 01-4.71 1.13 8.37 8.37 0 01-6.46-2.79 10.92 10.92 0 01-2.65-7.34H918v-2.19q0-7.35-3.32-11.29t-9.31-3.95M940.69 188.93a8.16 8.16 0 00-7.32 4.12l-.08-3.55h-5.11v30.71h5.25v-21.8q1.85-4.4 7-4.4a15.9 15.9 0 012.58.2v-4.88a5.45 5.45 0 00-2.33-.4M963.5 216.32a6.9 6.9 0 01-6-3q-2.13-3-2.13-8.16 0-5.85 2.15-8.83a7 7 0 016-3 7.77 7.77 0 017.24 4.4v14a7.68 7.68 0 01-7.29 4.51m-1.25-27.39a10.61 10.61 0 00-8.8 4.25q-3.32 4.23-3.32 11.42 0 7.49 3.33 11.83a10.49 10.49 0 008.73 4.35 10.78 10.78 0 008.6-3.64v2.65a8.23 8.23 0 01-2.14 6.07 8 8 0 01-6 2.16 10.27 10.27 0 01-8.26-4.17l-2.67 3.23a11.12 11.12 0 004.85 3.88 15.75 15.75 0 006.5 1.42q6 0 9.5-3.43t3.53-9.4v-30h-4.8l-.25 3.41a10.57 10.57 0 00-8.77-4"/></g></svg>';
					}
					$output .='<div class="tpgb-panel-head-inner">';
						$output .='<h2 class="tpgb-head-setting-panel">'.esc_html__('Setting Panel','tpgb').'</h2>';
						$output .='<div class="tpgb-current-version"> '.esc_html__('Version','tpgb').' '.TPGB_VERSION.'</div>';
					$output .='</div>';
				$output .='</div>';
			$output .='</div>';
			
			if( "tpgb_gutenberg_settings" != $_GET['page'] ) {
				$output .='<div class="tpgb-nav-tab-wrapper">';
					$output .='<div class="nav-tab-wrapper">';
						ob_start();
						foreach ($option_tabs as $option_tab):
							$tab_slug  = $option_tab['id'];
							$nav_class = 'nav-tab';
							if ($tab_slug == $_GET['page']) {
								$nav_class .= ' nav-tab-active'; //add active class to current tab
								$tab_forms[] = $option_tab; //add current tab to forms to be rendered
							}
							$navicon = '';
							if($tab_slug == "tpgb_welcome_page"){
								$navicon = '<svg class="tab-nav-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" preserveAspectRatio="none">
											<path d="M109.148 120h-36c-1.104 0-2-0.9-2-2v-34h-20v34c0 1.1-0.896 2-2 2h-36c-1.104 0-2-0.9-2-2v-62h-8.648c-0.832 0-1.576-0.512-1.868-1.288-0.296-0.776-0.080-1.652 0.54-2.204l57.324-51c0.736-0.656 1.836-0.676 2.596-0.056l14.060 11.54v-10.992c0-1.104 0.896-2 2-2h20c1.1 0 2 0.896 2 2v31.648l19.74 18.908c0.588 0.568 0.776 1.432 0.472 2.192-0.308 0.756-1.044 1.252-1.86 1.252h-6.356v62c0 1.1-0.896 2-2 2zM75.148 116h32v-62c0-1.104 0.896-2 2-2h3.376l-16.756-16.056c-0.396-0.376-0.612-0.9-0.612-1.444v-30.5h-16v13.22c0 0.772-0.44 1.48-1.144 1.808-0.7 0.328-1.528 0.232-2.124-0.26l-16-13.136-52.124 46.368h5.396c1.104 0 2 0.896 2 2v62h32v-34c0-1.1 0.896-2 2-2h24c1.104 0 2 0.9 2 2v34h-0.012z"></path>
										</svg>';
							}
							if($tab_slug == "tpgb_normal_blocks_opts"){
								$navicon = '<svg class="tab-nav-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" preserveAspectRatio="none">
									<path d="M118 110h-116c-1.104 0-2-0.9-2-2v-96c0-1.104 0.896-2 2-2h116c1.1 0 2 0.896 2 2v96c0 1.1-0.9 2-2 2zM4 106h112v-92h-112v92z"></path>
									<path d="M116 34h-112c-1.104 0-2-0.896-2-2s0.896-2 2-2h112c1.1 0 2 0.896 2 2s-0.9 2-2 2z"></path>
									<path d="M46.904 97.048c-0.412 0-0.824-0.132-1.172-0.384-0.704-0.508-1-1.416-0.732-2.236l4.932-15.304-12.896-9.516c-0.696-0.516-0.984-1.416-0.712-2.24s1.036-1.38 1.9-1.38h15.916l4.916-15.136c0.536-1.648 3.268-1.648 3.804 0l4.916 15.136h15.916c0.864 0 1.628 0.56 1.904 1.38 0.264 0.82-0.016 1.724-0.716 2.24l-12.896 9.516 4.928 15.236c0.264 0.824-0.032 1.728-0.732 2.236s-1.648 0.508-2.348 0l-12.876-9.32-12.88 9.384c-0.348 0.256-0.76 0.388-1.172 0.388zM44.3 70l9.164 6.756c0.692 0.508 0.98 1.408 0.716 2.228l-3.488 10.828 9.088-6.616c0.7-0.516 1.648-0.516 2.348-0.008l9.088 6.584-3.484-10.776c-0.264-0.82 0.024-1.712 0.712-2.224l9.164-6.76h-11.288c-0.868 0-1.636-0.564-1.908-1.388l-3.464-10.664-3.464 10.664c-0.268 0.824-1.036 1.388-1.904 1.388h-11.284v-0.012z"></path>
									<path d="M15.956 23c0 1.381-1.119 2.5-2.5 2.5s-2.5-1.119-2.5-2.5c0-1.381 1.119-2.5 2.5-2.5s2.5 1.119 2.5 2.5z"></path>
									<path d="M25.956 23c0 1.381-1.119 2.5-2.5 2.5s-2.5-1.119-2.5-2.5c0-1.381 1.119-2.5 2.5-2.5s2.5 1.119 2.5 2.5z"></path>
									<path d="M35.956 23c0 1.381-1.119 2.5-2.5 2.5s-2.5-1.119-2.5-2.5c0-1.381 1.119-2.5 2.5-2.5s2.5 1.119 2.5 2.5z"></path>
								</svg>';
							}
							if($tab_slug == "tpgb_connection_data"){
								$navicon = '<svg class="tab-nav-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120">
									<g id="icomoon-ignore"><line stroke-width="1" stroke="#449FDB" opacity=""></line>
									</g>
									<path d="M66.968 91.64c-0.1 0-0.208-0.008-0.312-0.024-0.752-0.116-1.372-0.656-1.592-1.384l-8.488-27.32-27.32-8.488c-0.728-0.228-1.26-0.848-1.38-1.6s0.2-1.504 0.82-1.944l23.36-16.516-0.364-28.604c-0.008-0.76 0.416-1.464 1.088-1.808 0.68-0.344 1.496-0.276 2.104 0.18l22.924 17.104 27.084-9.18c0.72-0.244 1.516-0.060 2.056 0.48 0.54 0.536 0.728 1.332 0.48 2.056l-9.18 27.092 17.096 22.924c0.46 0.608 0.528 1.424 0.18 2.1-0.352 0.676-1.072 1.080-1.808 1.092l-28.608-0.368-16.516 23.352c-0.368 0.544-0.984 0.856-1.624 0.856zM34.312 51.808l24.452 7.6c0.628 0.196 1.12 0.688 1.316 1.316l7.596 24.448 14.784-20.908c0.38-0.536 1.016-0.828 1.656-0.844l25.608 0.328-15.308-20.52c-0.392-0.528-0.5-1.216-0.292-1.84l8.224-24.252-24.256 8.224c-0.624 0.212-1.312 0.1-1.836-0.292l-20.52-15.308 0.328 25.608c0.008 0.66-0.308 1.28-0.844 1.66l-20.908 14.78z"></path>
									<path d="M6.252 116.264c-0.512 0-1.024-0.196-1.416-0.584-0.78-0.776-0.78-2.048 0-2.828l41.356-41.352c0.78-0.78 2.048-0.78 2.828 0s0.78 2.048 0 2.828l-41.356 41.352c-0.392 0.392-0.904 0.584-1.412 0.584z"></path>
								</svg>';
							}
							if($tab_slug == "tpgb_performance"){
								$navicon = '<svg class="tab-nav-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120" preserveAspectRatio="none">
									<path d="M69.532 99.592h-19.064c-21.288 0-21.532-56.236-21.532-58.624 0-16.388 16.056-32.804 30.464-37.284 0.392-0.12 0.8-0.12 1.192 0 12.768 3.972 30.464 18.988 30.464 36.848 0.004 2.408-0.24 59.060-21.524 59.060zM60 7.692c-11.5 3.892-27.064 18.132-27.064 33.272 0 21.12 4.756 54.624 17.532 54.624h19.064c13.252 0 17.532-37.096 17.532-55.064-0.004-15.448-15.792-29.024-27.064-32.832z"></path>
									<path d="M35.156 116.408c-0.152 0-0.3-0.016-0.448-0.056-0.632-0.148-1.16-0.592-1.404-1.196l-10.312-25.316c-0.316-0.768-0.116-1.656 0.496-2.224l11.016-10.188c0.808-0.752 2.072-0.704 2.828 0.108 0.752 0.812 0.7 2.080-0.112 2.828l-10 9.252 8.72 21.404 16.612-14.916c0.82-0.736 2.084-0.672 2.824 0.156 0.744 0.82 0.668 2.084-0.152 2.828l-18.732 16.812c-0.372 0.328-0.848 0.508-1.336 0.508z"></path>
									<path d="M84.844 116.408c-0.488 0-0.964-0.18-1.336-0.516l-18.728-16.812c-0.824-0.74-0.896-2-0.152-2.828 0.736-0.82 2-0.888 2.824-0.148l16.608 14.916 8.72-21.4-10-9.252c-0.812-0.752-0.864-2.016-0.112-2.828s2.016-0.86 2.828-0.112l11.016 10.196c0.612 0.568 0.808 1.448 0.496 2.22l-10.312 25.316c-0.244 0.604-0.764 1.052-1.404 1.196-0.148 0.036-0.3 0.052-0.448 0.052z"></path>
								</svg>';
							}
							
							if($tab_slug == "tpgb_custom_css_js"){
								$navicon = '<svg class="tab-nav-icon" xmlns="http://www.w3.org/2000/svg" width="36" height="30" viewBox="0 0 36 30">
									  <g transform="translate(0 -2.5)">
										<path d="M35.4,32.5H.6a.6.6,0,0,1-.6-.6V3.1a.6.6,0,0,1,.6-.6H35.4a.6.6,0,0,1,.6.6V31.9A.6.6,0,0,1,35.4,32.5ZM1.2,31.3H34.8V3.7H1.2Z"/>
										<path d="M34.7,8.7H1.1a.6.6,0,0,1,0-1.2H34.7a.6.6,0,0,1,0,1.2Z" transform="translate(0.1 1)"/>
										<path d="M11.153,26.763a.6.6,0,0,1-.509-.281L6.08,19.233a.6.6,0,0,1,.025-.676l4.564-6.187a.6.6,0,0,1,.965.713l-4.32,5.858,4.345,6.9a.6.6,0,0,1-.506.917Z" transform="translate(1.198 1.925)"/>
										<path d="M20.338,26.764a.6.6,0,0,1-.509-.918l4.345-6.9-4.32-5.858a.6.6,0,1,1,.965-.713l4.564,6.187a.606.606,0,0,1,.025.676l-4.564,7.25A.6.6,0,0,1,20.338,26.764Z" transform="translate(3.947 1.925)"/>
										<path d="M12.838,25.151a.615.615,0,0,1-.293-.076.6.6,0,0,1-.23-.817l5.938-10.625a.6.6,0,1,1,1.048.584L13.362,24.844A.6.6,0,0,1,12.838,25.151Z" transform="translate(2.448 2.165)"/>
										<g transform="translate(3.287 5.65)">
										  <circle id="Ellipse_29" data-name="Ellipse 29" cx="0.75" cy="0.75" r="0.75"/>
										  <circle id="Ellipse_30" data-name="Ellipse 30" cx="0.75" cy="0.75" r="0.75" transform="translate(3)"/>
										  <circle id="Ellipse_31" data-name="Ellipse 31" cx="0.75" cy="0.75" r="0.75" transform="translate(6)"/>
										</g>
									  </g>
									</svg>';
							}
							
							if($tab_slug == "tpgb_activate"){
								$navicon = '<svg class="tab-nav-icon" xmlns="http://www.w3.org/2000/svg" width="15.204" height="28.507" viewBox="0 0 15.204 28.507"><g transform="translate(22.204) rotate(90)"><path d="M10.967,22.2H2.553A2.969,2.969,0,0,1,0,19.521V8.732C0,7.663.979,7,2.553,7h8.413C12.43,7,13.3,7.648,13.3,8.732v3.019H28.032a.476.476,0,0,1,.475.475v4.751a.477.477,0,0,1-.475.475H13.3V19.52A2.749,2.749,0,0,1,10.967,22.2ZM2.553,7.95c-.164,0-1.6.022-1.6.782V19.521a2.061,2.061,0,0,0,1.6,1.732h8.413c.67,0,1.386-1.016,1.386-1.731V16.977a.476.476,0,0,1,.475-.475H27.557V12.7H12.828a.475.475,0,0,1-.475-.475V8.733c0-.519-.467-.783-1.386-.783Z"></path><path d="M18.826,14.95H15.975a.475.475,0,1,1,0-.95h2.851a.475.475,0,0,1,0,.95Z" transform="translate(-0.771 -0.348)"></path><path d="M23.826,14.95H20.975a.475.475,0,0,1,0-.95h2.851a.475.475,0,0,1,0,.95Z" transform="translate(-1.02 -0.348)"></path><path d="M27.876,14.95h-1.9a.475.475,0,1,1,0-.95h1.9a.475.475,0,0,1,0,.95Z" transform="translate(-1.269 -0.348)"></path><path d="M5.376,18.552h-1.9A.476.476,0,0,1,3,18.077v-7.6A.475.475,0,0,1,3.475,10h1.9a.475.475,0,0,1,.475.475v7.6A.476.476,0,0,1,5.376,18.552ZM3.95,17.6H4.9V10.95H3.95Z" transform="translate(-0.149 -0.149)"></path></g></svg>';
							}
							
							if($tab_slug == "tpgb_white_label"){
								$navicon = '<svg xmlns="http://www.w3.org/2000/svg" width="30.152" height="27.537" class="tab-nav-icon"><defs/><path d="M.357 17.047L18.153 3.136a.466.466 0 01.353-.093l5.184.765a3.594 3.594 0 013.237 4.155l-.616 5.078a.456.456 0 01-.178.312L8.353 27.252a.462.462 0 01-.747-.36l-.055-7.239-6.908-1.775a.466.466 0 01-.286-.83zm18.21-13.06l-16.885 13.2 6.444 1.657a.46.46 0 01.348.444l.051 6.653 16.89-13.203.593-4.889a2.672 2.672 0 00-2.443-3.125z"/><path fill="#8072fc" d="M19.593 6.858a2.779 2.779 0 11-.478 3.9 2.783 2.783 0 01.478-3.9zm2.851 3.648a1.852 1.852 0 10-2.6-.319 1.853 1.853 0 002.601.32z"/><path d="M12.676 16.82l4.377-3.421a.463.463 0 11.57.73l-4.377 3.422a.463.463 0 01-.57-.73zM10.964 14.632l4.378-3.422a.463.463 0 01.57.73l-4.377 3.421a.463.463 0 01-.57-.73z"/></svg>';
							}
							
							$label_options=get_option( 'tpgb_white_label' );	
							if( (empty($label_options['tpgb_hidden_label']) || $label_options['tpgb_hidden_label']!='on') && ($tab_slug == "tpgb_white_label" || $tab_slug == "tpgb_activate")){
							?>
							<a class="<?php echo esc_attr($nav_class); ?>" href="<?php menu_page_url($tab_slug); ?>">
								<span><?php echo $navicon; ?></span>
								<span><?php echo esc_html($option_tab['title']); ?></span>
							</a>
							<?php 
							}else if(($tab_slug != "tpgb_white_label" && $tab_slug != "tpgb_activate") || !defined('TPGBP_VERSION')){
							?>							
							<a class="<?php echo esc_attr($nav_class); ?>" href="<?php menu_page_url($tab_slug); ?>">
								<span><?php echo $navicon; ?></span>
								<span><?php echo esc_html($option_tab['title']); ?></span>
							</a>
							<?php  }
						endforeach;
						$out = ob_get_clean();
						$output .= $out;
					$output .='</div>';
				$output .='</div>';
			
				/*Content Options*/
				$output .='<div class="tpgb-settings-form-wrapper form-'.esc_attr($tab_forms[0]['id']).'">';
				
					if(!empty($tab_forms)){
						ob_start();
						foreach ($tab_forms as $tab_form):
							if($tab_form['id']=='tpgb_normal_blocks_opts'){
								echo '<div class="tpgb-panel-plus-block-page">';
									
									/*block filter*/
									echo '<div class="tpgb-panel-row tpgb-mt-50">';
										echo '<div class="tpgb-panel-col tpgb-panel-col-100">';
											echo '<div class="panel-plus-block-filter">';
												echo '<div class="tpgb-block-filters-check">';
													echo '<label class="panel-block-head panel-block-check-all"><span><svg xmlns="http://www.w3.org/2000/svg" width="23.532" height="20.533" viewBox="0 0 23.532 20.533">
														  <path d="M6.9,15.626,0,8.73,2.228,6.5,6.9,11.064,17.729,0,20,2.388Z" transform="translate(4.307) rotate(16)"/>
														</svg></span><input type="checkbox" id="block_check_all" /> '.esc_html__('Enable All','tpgb').'</label>';
													echo '<div class="panel-block-head panel-block-filters">';
														echo '<select class="blocks-filter">';
															echo '<option value="all">'.esc_html__('All','tpgb').'</option>';
															echo '<option value="free">'.esc_html__('Free','tpgb').'</option>';
															echo '<option value="freemium">'.esc_html__('Freemium','tpgb').'</option>';
															echo '<option value="pro">'.esc_html__('Pro','tpgb').'</option>';
														echo '</select>';
													echo '</div>';
												echo '</div>';
												echo '<div class="tpgb-block-filters-search">';
													echo '<div class="tpgb-scan-unused-blocks">';
														echo '<div class="tpgb-scanning-blocks">'.esc_html__('Scan Unused Blocks','tpgb').'</div>';
														echo '<div class="tpgb-unused-disable-blocks">'.esc_html__('Disable Unused Blocks','tpgb').'</div>';
													echo '</div>';
													echo '<label class="tpgb-filter-block-search"><input type="text" class="block-search" placeholder="'.esc_attr__("Search Blocks..","tpgb").'" /></label>';
												echo '</div>';
											echo '</div>';
										echo '</div>';
									echo '</div>';
									/*block filter*/
									
									/*block listing*/
									echo '<form class="cmb-form" action="'.esc_url( admin_url('admin-post.php') ).'" method="post" id="tpgb_normal_blocks_opts" enctype="multipart/form-data" encoding="multipart/form-data">';
										wp_nonce_field( 'nonce_tpgb_normal_blocks_action', 'nonce_tpgb_normal_blocks_opts' );
										
										$is_pro = '';
										if (!defined('TPGBP_VERSION')) {
											$is_pro = 'plus-block-pro';
										}
										
										echo '<div class="tpgb-panel-row tpgb-mt-50 plus-block-list '.esc_attr($is_pro).'">';
											echo $this->tpgb_block_list_rendered();
										echo '</div>';
										echo '<input type="hidden" name="action" value="tpgb_blocks_opts_save">';
										echo '<input type="submit" name="submit-key" value="Save" class="button-primary tpgb-submit-block">';
									echo '</form>';
									/*block listing*/
								echo '</div>';
							}
							if($tab_form['id']=='tpgb_default_load_blocks'){
								echo '<div class="tpgb-panel-plus-block-page">';
								
									/*default block filter*/
									echo '<div class="tpgb-panel-row tpgb-mt-50">';
										echo '<div class="tpgb-panel-col tpgb-panel-col-100">';
										echo '<h3 class="tpgb-block-list-title">'.esc_html__('Gutenberg Default Blocks Manager','tpgb').'</h3>
								<p class="tpgb-block-list-desc">'.esc_html__('You may enable/disable Default Gutenberg Blocks as well as scan blocks to auto disable all at once.','tpgb').'</p>';
											echo '<div class="panel-plus-block-filter">';
												echo '<div class="tpgb-block-filters-check">';
													echo '<label class="panel-block-head panel-block-check-all"><span><svg xmlns="http://www.w3.org/2000/svg" width="23.532" height="20.533" viewBox="0 0 23.532 20.533">
														  <path d="M6.9,15.626,0,8.73,2.228,6.5,6.9,11.064,17.729,0,20,2.388Z" transform="translate(4.307) rotate(16)"/>
														</svg></span><input type="checkbox" id="block_check_all" /> '.esc_html__('Enable All','tpgb').'</label>';
												echo '</div>';
												echo '<div class="tpgb-block-filters-search">';
													echo '<div class="tpgb-scan-unused-blocks">';
														echo '<div class="tpgb-scanning-blocks">'.esc_html__('Scan Unused Blocks','tpgb').'</div>';
														echo '<div class="tpgb-unused-disable-blocks">'.esc_html__('Disable Unused Blocks','tpgb').'</div>';
													echo '</div>';
												echo '</div>';
											echo '</div>';
										echo '</div>';
									echo '</div>';
									/*default block filter*/
									
									/*default block listing*/
									echo '<form class="cmb-form" action="'.esc_url( admin_url('admin-post.php') ).'" method="post" id="tpgb_default_load_blocks" enctype="multipart/form-data" encoding="multipart/form-data">';
										wp_nonce_field( 'nonce_tpgb_default_blocks_action', 'nonce_tpgb_default_load_blocks' );
										
										echo '<div class="tpgb-panel-row tpgb-mt-50 plus-block-list">';
											echo $this->tpgb_default_block_list_rendered();
										echo '</div>';
										echo '<input type="hidden" name="action" value="tpgb_default_blocks_opts_save">';
										echo '<input type="submit" name="submit-key" value="Save" class="button-primary tpgb-submit-block">';
									echo '</form>';
									/*default block listing*/
								echo '</div>';
							}

							if($tab_form['id']=='tpgb_white_label'){
								do_action('tpgb_free_notice_white_label');
							}else if($tab_form['id']=='tpgb_activate'){
								do_action('tpgb_notice_activate');
							}
							
							if(defined('TPGBP_VERSION') && $tab_form['id']=='tpgb_white_label'){
								cmb2_metabox_form($tab_form, $tab_form['id']);
							}else if($tab_form['id']!='tpgb_welcome_page' && $tab_form['id']!='tpgb_normal_blocks_opts' && $tab_form['id']!='tpgb_activate' && $tab_form['id']!='tpgb_white_label' && $tab_form['id']!='tpgb_default_load_blocks'){
								cmb2_metabox_form($tab_form, $tab_form['id']);
								if($tab_form['id']=='tpgb_connection_data'){
									do_action('tpgb_rollback_render_content');
								}
							}else if($tab_form['id']=='tpgb_welcome_page'){
								include_once TPGB_INCLUDES_URL.'welcome-page.php';
							}
						endforeach;
						$out = ob_get_clean();
						$output .= $out;
					}
				$output .='</div>';
			}
			
		$output .='</div>';
		
		echo $output;
		
		if(defined('TPGBP_VERSION')){
			$current_screen = get_current_screen();
			$label_options=get_option( 'tpgb_white_label' );
			if( !empty($label_options) && isset($label_options['tpgb_hidden_label']) && $label_options['tpgb_hidden_label']=='on' ){
				$white_title = (isset($label_options['tpgb_plugin_name']) && !empty($label_options['tpgb_plugin_name'])) ? sanitize_title($label_options['tpgb_plugin_name']).'_page_tpgb_white_label' : 'theplus-gutenberg_page_tpgb_white_label';
				$active_title = (isset($label_options['tpgb_plugin_name']) && !empty($label_options['tpgb_plugin_name'])) ? sanitize_title($label_options['tpgb_plugin_name']).'_page_tpgb_activate' : 'theplus-gutenberg_page_tpgb_activate';
				if( is_admin() && !empty($current_screen) && ($current_screen->id === $white_title || $current_screen->id === $active_title)) {
					wp_safe_redirect( admin_url( 'admin.php?page=tpgb_welcome_page' ) );
					exit;
				}
			}
		}
	}
	
	
	
	/**
     * Gutenberg options metabox and field configuration
     * @since  1.0.0
     * @return array
     */
    public function option_fields($verify_api='') {
		// Only need to initiate the array once per page-load
        if (!empty($this->option_metabox)) {
            return $this->option_metabox;
        }
		
		$this->option_metabox[] = array(
            'id' => 'tpgb_welcome_page',
            'title' => esc_html__('Welcome', 'tpgb'),
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'tpgb_welcome_page'
                )
            ),
            'show_names' => true,
            'fields' => ''
        );
		
		$this->option_metabox[] = array(
            'id' => 'tpgb_normal_blocks_opts',
            'title' => esc_html__('Plus Blocks', 'tpgb'),
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'tpgb_normal_blocks_opts'
                )
            ),
			'show_names' => true,
            'fields' => '',
        );
		
		$extra_options= array(
			array(
				'name' => esc_html__('Google Map API Key', 'tpgb'),
				'desc' => esc_html__('NOTE : Turn Off this key If you theme already have google key option. So, It will not generate error in console for multiple google map keys.', 'tpgb'),
				'default' => '',
				'id' => 'gmap_api_switch',
				'type' => 'select',
				'show_option_none' => false,
				'default' => 'enable',
				'options' => array(
					'enable' => esc_html__('Show', 'tpgb'),
					'disable' => esc_html__('Hide', 'tpgb'),
				),
			),
			array(
				'name' => esc_html__('Google Map API Key', 'tpgb'),
				'desc' => sprintf(__('This field is required if you want to use Advance Google Map element. You can obtain your own Google Maps Key here: (<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" rel="noopener noreferrer" >Click Here</a>)', 'tpgb')),
				'default' => '',
				'id' => 'googlemap_api',
				'type' => 'text',
				'attributes' => array(
					'data-conditional-id'    => 'gmap_api_switch',
					'data-conditional-value' => 'enable',
				),
			),
			array(
				'name' => esc_html__('Google Fonts','tpgb'),
				'desc' => esc_html__('Note : If you disable this, It will not load Google Font on site as well as stop showing values in Font Family Selection.','tpgb'),
				'id'   => 'gfont_load',
				'type' => 'select',
				'show_option_none' => false,
				'default' => 'enable',
				'options' => array(
					'enable' => esc_html__('Enable', 'tpgb'),
					'disable' => esc_html__('Disable', 'tpgb'),
				),
			),
			array(
				'name' => esc_html__('Beta Version','tpgb'),
				'desc' => esc_html__('Note : If you enable, You will be considered as a beta user and You will get beta update notice directly in your plugins update area. This works only for Pro Version of plugin, It will available in Free version soon.','tpgb'),
				'id'   => 'beta_version',
				'type' => 'select',
				'show_option_none' => false,
				'default' => 'disable',
				'options' => array(
					'disable' => esc_html__('Disable', 'tpgb'),
					'enable' => esc_html__('Enable', 'tpgb'),
				),
			),
			array(
				'name' => esc_html__('Show The Plus Performance in Admin Bar','tpgb'),
				'desc' => esc_html__('This option will show The Plus Performance option on WordPress top Admin Bar, which allows you to purge assets for individual or all pages directly from any page.','tpgb'),
				'id'   => 'assets_performance',
				'type' => 'select',
				'show_option_none' => false,
				'default' => 'disable',
				'options' => array(
					'disable' => esc_html__('Disable', 'tpgb'),
					'enable' => esc_html__('Enable', 'tpgb'),
				),
			),
			array(
				'name' => esc_html__('Font Awesome Loading','tpgb'),
				'desc' => esc_html__('Note : If you disable this, It will stop loading at frontend throughout your website.','tpgb'),
				'id'   => 'fontawesome_load',
				'type' => 'select',
				'show_option_none' => false,
				'default' => 'enable',
				'options' => array(
					'enable' => esc_html__('Enable', 'tpgb'),
					'disable' => esc_html__('Disable', 'tpgb'),
				),
			),
		);
		
		if(has_filter('tpgb_extra_options')) {
			$extra_options = apply_filters('tpgb_extra_options', $extra_options);
		}
		
		$this->option_metabox[] = array(
            'id' => 'tpgb_connection_data',
            'title' => esc_html__('Extra Options', 'tpgb'),
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'tpgb_connection_data'
                )
            ),
            'show_names' => true,
            'fields' => $extra_options,
        );
		
		$this->option_metabox[] = array(
            'id' => 'tpgb_performance',
            'title' => esc_html__('Performance', 'tpgb'),
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'tpgb_performance'
                )
            ),
            'show_names' => true,
            'fields' => ''
        );
		
		$this->option_metabox[] = array(
            'id' => 'tpgb_custom_css_js',
            'title' => esc_html__('Custom', 'tpgb'),
            'show_on' => array(
                'key' => 'options-page',
                'value' => array(
                    'tpgb_custom_css_js'
                )
            ),
            'show_names' => true,
            'fields' => array(				
				array( 
					'name' => esc_html__( 'Custom CSS', 'tpgb' ),
					'desc' => esc_html__( 'Add Your Custom CSS Styles', 'tpgb' ),
					'id' => 'tpgb_custom_css_editor',
					'type' => 'textarea_code',
					'default' => '',
				),
				array( 
					'name' => esc_html__( 'Custom JS', 'tpgb' ),
					'desc' => esc_html__( 'Add Your Custom JS Scripts', 'tpgb' ),
					'id' => 'tpgb_custom_js_editor',
					'type' => 'textarea_code',
					'default' => '',
				),
				array(
					'id'   => 'tpgb_custom_hidden',
					'type' => 'hidden',
					'default' => 'hidden',
				),
			),
        );
		
		$this->option_metabox[] = array(
			'id' => 'tpgb_activate',
			'title' => esc_html__('Activate', 'tpgb'),
			'show_on' => array(
				'key' => 'options-page',
				'value' => array(
					'tpgb_activate'
				)
			),
			'show_names' => true,
			'fields' => '',
		);
		
		$white_label_options=[];
		if(has_filter('tpgb_white_label_options')) {
			$white_label_options = apply_filters('tpgb_white_label_options', $white_label_options);
		}
		
		$this->option_metabox[] = array(
			'id' => 'tpgb_white_label',
			'title' => esc_html__('White Label', 'tpgb'),
			'show_on' => array(
				'key' => 'options-page',
				'value' => array(
					'tpgb_white_label'
				)
			),
			'show_names' => true,
			'fields' => $white_label_options,
		);

		$this->option_metabox[] = array(
			'id' => 'tpgb_default_load_blocks',
			'title' => esc_html__('Gutenberg Default Blocks', 'tpgb'),
			'show_on' => array(
				'key' => 'options-page',
				'value' => array(
					'tpgb_default_load_blocks'
				)
			),
			'show_names' => true,
			'fields' => '',
		);

		return $this->option_metabox;
	}
	
	/**
     * get options Key/Tab
     * @since  1.0.0
     * @return array
     */
	public function get_option_key($field_id)
    {
        $option_tabs = $this->option_fields();
        foreach ($option_tabs as $option_tab) {
            foreach ($option_tab['fields'] as $field) {
                if ($field['id'] == $field_id) {
                    return $option_tab['id'];
                }
            }
        }
        return $this->key;
    }
	
	/**
     * Public getter method for retrieving protected/private variables
     * @since  1.0.0
     * @param  string  $field Field to retrieve
     * @return mixed          Field value or exception is thrown
     */
    public function __get($field)
    {
        
        // Allowed fields to retrieve
        if (in_array($field, array('key','fields','title','options_page'), true)) {
            return $this->{$field};
        }
        if ('option_metabox' === $field) {
            return $this->option_fields();
        }
        
		throw new Exception( sprintf( esc_html__( 'Invalid property: %1$s', 'tpgb' ), $field ) );
    }

	public function get_post_statuses_sql(){
		$statuses = array_map(
			function( $item ){
				return esc_sql( $item );
			},
			array( 'publish', 'private', 'pending', 'future', 'draft' )
		);
		return "'" . implode( "', '", $statuses ) . "'";
	}

	/*
	 * Scan Unused Blocks
	 * @since 1.3.1
	 */
	public function tpgb_is_block_used_not_fun(){
		if( defined('DOING_AJAX') && DOING_AJAX && isset( $_POST['nonce'] ) && !empty($_POST['nonce']) && wp_verify_nonce( $_POST['nonce'], 'tpgb_scan_nonce_' . get_current_user_id() ) ){
			global $wpdb;
			$block_scan =[];
			
			if(isset($_POST['default_block']) && $_POST['default_block']=='false'){
				$this->block_listout();
				if(!empty($this->block_lists)){
					foreach($this->block_lists as $key => $block){
						$found_in_posts = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status IN ( {$this->get_post_statuses_sql()} ) AND post_content LIKE '%<!-- wp:tpgb/" . $key . "%' LIMIT 1");
						
						$block_scan[$key]= $found_in_posts ? 1 : 0;
						if( ! $found_in_posts ){
							$found_in_widgets = $wpdb->get_var("SELECT option_id FROM {$wpdb->options} WHERE option_name = 'widget_block' AND option_value LIKE '%<!-- wp:tpgb/" . $key . "%' LIMIT 1");
							$block_scan[$key]= $found_in_widgets ? 1 : 0;
						}
					}
				}
			}else if(isset($_POST['default_block']) && $_POST['default_block']!='' && $_POST['default_block']==true){
				$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
				if( !empty($block_types) ){
					foreach($block_types as $key => $block){
						if(str_contains($key, 'core/')){
							if( $key !='core/missing' && $key !='core/block'&& $key !='core/widget-group' && !empty($block->title) ){
								$core_key = str_replace( 'core/', '', $key );
								$core_key = esc_sql( $core_key );
								$pass_key = str_replace( 'core/', 'core-', $key );
								$found_in_posts = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status IN ( {$this->get_post_statuses_sql()} ) AND post_content LIKE '%<!-- wp:" . $core_key . "%' LIMIT 1");
						
								$block_scan[$pass_key]= $found_in_posts ? 1 : 0;
								if( ! $found_in_posts ){
									$found_in_widgets = $wpdb->get_var("SELECT option_id FROM {$wpdb->options} WHERE option_name = 'widget_block' AND option_value LIKE '%<!-- wp:" . $core_key . "%' LIMIT 1");
									$block_scan[$pass_key]= $found_in_widgets ? 1 : 0;
								}
							}
						}
					}
				}
			}
			wp_send_json($block_scan);
			exit;
		}
		exit;
	}

	/*
	 * Unused Disable Blocks
	 * @since 1.4.4
	 */
	public function tpgb_disable_unsed_block_fun(){
		if( defined('DOING_AJAX') && DOING_AJAX && isset( $_POST['nonce'] ) && !empty($_POST['nonce']) && wp_verify_nonce( $_POST['nonce'], 'tpgb_disable_nonce_' . get_current_user_id() ) ){
			
			if(!isset($_POST['blocks']) || empty($_POST['blocks'])){
				echo 0;
				exit;
			}
			if(isset($_POST['default_block']) && $_POST['default_block']!='' && $_POST['default_block']=='false'){
				$blocks = json_decode(stripslashes($_POST['blocks']),true);
				$action_page = 'tpgb_normal_blocks_opts';
				$update_block = [];
				if(is_array($blocks)){
					foreach($blocks as $key => $val){
						if($val===1){
							$update_block[] = $key;
						}
					}
					$update_value = array( 'enable_normal_blocks' => map_deep( wp_unslash( $update_block ), 'sanitize_text_field' ) );
					update_option( $action_page, $update_value );
					Tpgb_Library()->remove_backend_dir_files();
					echo 1;
					exit;
				}
			}else if(isset($_POST['default_block']) && $_POST['default_block']!='' && $_POST['default_block']==true){
				$blocks = json_decode(stripslashes($_POST['blocks']),true);
				$action_page = 'tpgb_default_load_blocks';
				$update_block = [];
				$deactive_block = [];
				if(is_array($blocks)){
					foreach($blocks as $key => $val){
						$pass_key = str_replace( 'core-', 'core/', $key );
						if($val===1){
							$update_block[] = $pass_key;
						}else if($val===0){
							$deactive_block[] = $pass_key;
							if($pass_key === "core/navigation"){
								$deactive_block[] = 'core/navigation-link';
								$deactive_block[] = 'core/navigation-submenu';
							}
							if($pass_key === "core/social-links"){
								$deactive_block[] = 'core/social-link';
							}
						}
					}
					
					$update_value = array( 'enable_default_blocks' => map_deep( wp_unslash( $update_block ), 'sanitize_text_field' ) );
					if(!empty($deactive_block) ){
						$update_value['disable_default_blocks'] = $deactive_block;
					}
					update_option( $action_page, $update_value );
					echo 1;
					exit;
				}
			}
			
		}
		echo 0;
		exit;
	}
}

// Get it started
$Tpgb_Gutenberg_Settings_Options = new Tpgb_Gutenberg_Settings_Options();
$Tpgb_Gutenberg_Settings_Options->hooks();
?>