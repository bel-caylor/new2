<?php 
/**
 * TPGB Pro Settings Options
 * @since 1.0.0
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

class TPgb_Pro_Gutenberg_Settings_Options {
	
	/**
     * Constructor
     * @since 1.0.0
     */
    public function __construct() {
		if(is_admin()){
			add_filter( 'tpgb_extra_options', array( $this,'tpgb_pro_extra_options'));
			remove_action( 'tpgb_free_notice_white_label','tpgb_free_white_label_content' );
			add_filter( 'tpgb_white_label_options', array( $this,'tpgb_pro_white_label_options'));
			add_action( 'admin_head', [ $this, 'tpgb_plus_icon_logo' ] );
		}
		add_action('tpgb_rollback_render_content', [ $this, 'tpgb_pro_rollback_render_content' ], 10 );

		include_once TPGBP_INCLUDES_URL . 'plus-library/tpgb-library.php';
		Tpgb_Pro_Library::get_instance();
		if(is_admin()){
			remove_action( 'tpgb_notice_activate', 'tpgb_activate_content' );
			add_filter( 'tpgb_notice_activate', array( $this,'tpgb_pro_activate_content'));
		}
    }
	
	public function tpgb_plus_icon_logo(){
		$tpgb_white_label = get_option( 'tpgb_white_label' );
		if(!empty($tpgb_white_label['tpgb_plus_logo'])){
			?>
			<style>.wp-menu-image.dashicons-before.dashicons-tpgb-plus-settings{background: url(<?php echo esc_url($tpgb_white_label['tpgb_plus_logo']); ?>);background-size: 22px;background-repeat: no-repeat;background-position: center;}</style>
		<?php }
	}
	
	public function tpgb_pro_extra_options($data){
		$pro_extra_option = array( array(
				'name' => esc_html__('FontAwesome Pro Kit', 'tpgbp'),
				'desc' => sprintf(__('Font Awesome Pro, the web\'s most popular icon set, Enter Your <a href="https://fontawesome.com/kits" target="_blank" rel="noopener noreferrer">Font Awesome Pro Kit ID</a>', 'tpgbp')),
				'default' => '',
				'id' => 'fontawesome_pro_kit',
				'type' => 'text',
				'after_field' => '<div class="tpgb-fontawesome-kit-pro "><span class="dashicons dashicons-image-rotate"></span></div> ',
				'attributes'  => array(
					'autocomplete' => 'off',
					'data-conditional-id'    => 'fontawesome_load',
					'data-conditional-value' => 'enable',
				),
			),
			array(
				'name' => esc_html__('Mailchimp API Key', 'tpgbp'),
				'desc' => esc_html__('Go to your Mailchimp > Account > Extras > API Keys then create a key and paste here', 'tpgbp'),
				'default' => '',
				'id' => 'mailchimp_api',
				'type' => 'text',
				'attributes'  => array(
					'autocomplete' => 'off',
				),
			),
			array(
				'name' => esc_html__('Mailchimp List ID', 'tpgbp'),
				'desc' => esc_html__('Go to your Mailchimp > Audience > Settings > Audience name and defaults > Copy the Audience ID and paste here.', 'tpgbp'),
				'default' => '',
				'id' => 'mailchimp_id',
				'type' => 'text',
				'attributes'  => array(
					'autocomplete' => 'off',
				),
			),
			array(
				'name' => esc_html__('Site Key reCAPTCHA v3','tpgbp'),
				'desc' => sprintf(__('Note: <a href="https://www.google.com/recaptcha/admin#list" target="_blank" rel="noopener noreferrer">reCAPTCHA v3</a> is a free service by Google that protects your website from spam and abuse.', 'tpgbp')),
				'id'   => 'tpgb_site_key_recaptcha',
				'type' => 'text',
			),
			array(
				'name' => esc_html__('Secret Key reCAPTCHA v3','tpgbp'),
				'desc' => sprintf(__('Note: <a href="https://www.google.com/recaptcha/admin#list" target="_blank" rel="noopener noreferrer">reCAPTCHA v3</a> is a free service by Google that protects your website from spam and abuse.', 'tpgbp')),
				'id'   => 'tpgb_secret_key_recaptcha',
				'type' => 'text',
			),
		);
		return array_merge($data, $pro_extra_option);
	}
	
	public function tpgb_pro_white_label_options($data){
	
		$pro_label_option = array(
				array(
					'name' => esc_html__('Plugin Name (Pro Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_plugin_name',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Plugin Name', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => esc_html__('Plugin Description (Pro Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_plugin_desc',
					'type' => 'textarea_small',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Plugin Description', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => esc_html__('Developer / Agency (Pro Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_author_name',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Developer Name', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => esc_html__('Website URL (Pro Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_author_uri',
					'type' => 'text_url',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Website URL', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name'    => esc_html__('Upload Logo / Icon','tpgbp'),
					'desc'    => '',
					'id'      => 'tpgb_plus_logo',
					'type'    => 'file',
					'options' => array(
						'url' => false,
					),
					'query_args' => array(						
						 'type' => array(
							'image/gif',
							'image/jpeg',
							'image/png',
						 ),
					),
					'preview_size' => 'large',
				),
				
				array(
					'name' => esc_html__('Plugin Name (Free Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_free_plugin_name',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Plugin Name', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => 'Plugin Description (Free Version)',
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_free_plugin_desc',
					'type' => 'textarea_small',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Plugin Description', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => esc_html__('Developer / Agency (Free Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_free_author_name',
					'type' => 'text',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Developer Name', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => esc_html__('Website URL (Free Version)', 'tpgbp'),
					'desc' => '',
					'default' => '',
					'id' => 'tpgb_free_author_uri',
					'type' => 'text_url',
					'attributes'  => array(
						'placeholder' => esc_html__('Enter Website URL', 'tpgbp'),
						'autocomplete' => 'off',
					),
				),
				array(
					'name' => '',
					'desc' => esc_html__('Important Note : If you will enable above two force disable option, Both tabs will be hidden for everyone. If you want to get those tabs back, You will need to deactivate plugin and activate again.','tpgbp'),
					'id'   => 'tpgb_hidden_label',
					'type' => 'checkbox',
				),
				array(
					'id'   => 'tpgb_white_label_hidden',
					'type' => 'hidden',
					'default' => 'hidden',
				),
			);
			
		return array_merge($data, $pro_label_option);
	}
	
	public function tpgb_pro_activate_content(){
		$active_status = Tpgb_Pro_Library::tpgb_pro_activate_msg();
		$verify_api = false;
		$verify_type = 'invalid';
		$activate_class = '';
		$activate_info_class = '';
		if(!empty($active_status) && isset($active_status['status']) && $active_status['status']=='valid'){
			$verify_api = true;
			$verify_type = 'valid';
			$activate_class = 'tpgb-table';
			$activate_info_class = 'tpgb-hide';
		}
		$message = '';
		if(!empty($active_status) && isset($active_status['message']) && !empty($active_status['message'])){
			$message = $active_status['message'];
		}
		
		$options = get_option( 'tpgb_activate' );
		$plus_key =$plus_last_key_char='';
		if(isset($options['tpgb_activate_key']) && !empty($options['tpgb_activate_key'])){
			$plus_key = $options['tpgb_activate_key'];
			$plus_last_key_char = substr($plus_key, -4);
		}
	?>
	<div class="tpgb-panel-activate-page">
		<div class="tpgb-panel-row">
			<div class="tpgb-panel-col tpgb-panel-col-65">
				<div class="tpgb-panel-sec tpgb-activate-form tpgb-p-20 tpgb-mb-8 <?php echo esc_attr($activate_class); ?>">
					<form class="cmb-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" id="tpgb_activate" enctype="multipart/form-data" encoding="multipart/form-data">
						<?php wp_nonce_field( 'tpgb_activte_nonce', 'tpgb_activte_nonce' ); ?>
						<div class="cmb2-wrap form-table">
							<div id="cmb2-metabox-tpgb_activate" class="cmb2-metabox cmb-field-list">
								<div class="cmb-row cmb-type-text cmb2-id-tpgb-activate-key table-layout" data-fieldtype="text">
									<div class="cmb-th">
										<label class="tpgb-sec-title" for="tpgb_activate_key"><?php echo esc_html__("License Activation",'tpgbp'); ?></label>
									</div>
									<div class="cmb-td">
										<?php if($verify_api==1 && !empty($plus_key)){ ?>
											<input type="text" class="regular-text tpgb-deactivate-key" name="tpgb_activate_key_de" id="tpgb_activate_key" value="**** **** **** **** <?php echo esc_attr($plus_last_key_char); ?>" placeholder="<?php echo esc_attr__("Enter your Key","tpgbp"); ?>" readonly disabled>
											<input type="hidden" name="action" value="tpgb_license_deactivate">
											<input type="submit" name="submit-key" value="<?php echo esc_attr__("Deactivate","tpgbp"); ?>" class="button-primary tpgb-deactivate-btn">
										<?php }else{ ?>
											<input type="text" class="regular-text" name="tpgb_activate_key" id="tpgb_activate_key" value="<?php echo esc_attr($plus_key); ?>" placeholder="<?php echo esc_attr__("Enter your Key","tpgbp"); ?>">
											<input type="hidden" name="action" value="tpgb_license_activate">
											<input type="submit" name="submit-key" value="<?php echo esc_attr__("Activate","tpgbp"); ?>" class="button-primary">
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</form>
					<?php if(!empty($message)){ ?>
						<div class="tpgb-activate-msg active-<?php echo esc_attr($verify_type); ?>"><?php echo force_balance_tags($message); ?></div>
					<?php } ?>
				</div>
				<div class="tpgb-panel-sec tpgb-activate-info tpgb-p-20 tpgb-mt-8 <?php echo esc_attr($activate_info_class); ?>">
					<div class="tpgb-sec-title"><?php echo esc_html__('Verify your plugin in 4 easy steps :','tpgbp');?></div>					
					<p class="tpgb-sec-desc"><?php echo esc_html__('1. Visit your ','tpgbp'); ?><?php echo '<i><a href="'.esc_url('https://store.posimyth.com/checkout/purchase-history/').'" target="_blank" class="panel-sec-color" rel="noopener noreferrer">Purchase History</a></i>'; ?></p>
					<p class="tpgb-sec-desc"><?php echo esc_html__('2. In the Page of "Purchase History" Go to View Licenses -> Manage Sites.','tpgbp'); ?></p>
					<p class="tpgb-sec-desc"><?php echo esc_html__('3. Add Your Home URL in the form and press "Add Site". Important : Website URL must be Home URL. You can get that by going Settings -> General -> WordPress Address (URL) and copy URL from there.','tpgbp'); ?></p>
					<p class="tpgb-sec-desc"><?php echo esc_html__('4. Now Your License Key will be activated for your Entered Website URL. Use that License key to activate your plugin.','tpgbp'); ?></p>
				</div>
			</div>
			<div class="tpgb-panel-col tpgb-panel-col-35">
				<div class="tpgb-panel-sec tpgb-activate-sidebar tpgb-p-20">
					<ul class="tpgb-activate-support-list tpgb-panel-row">
						<li>
							<a href="https://store.posimyth.com/checkout/purchase-history/" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="35.617" height="38.591"><path data-name="Path 7533" d="M11.961 30.836c-.028 0-.06 0-.088-.005a.679.679 0 01-.5-.344L5.699 20.292a.677.677 0 111.183-.658l5.225 9.4 5.219-5.4a.678.678 0 01.975.941l-5.85 6.06a.691.691 0 01-.49.201z"/><path data-name="Path 7534" d="M23.665 30.837a.681.681 0 01-.486-.206l-5.851-6.06a.678.678 0 01.975-.941l5.217 5.4 5.225-9.4a.678.678 0 111.182.663l-5.671 10.2a.684.684 0 01-.5.341c-.033 0-.062.003-.091.003z"/><g data-name="Group 50"><path data-name="Path 7535" d="M17.812 24.778c-5.626 0-10.194-8.826-10.194-14.585a10.193 10.193 0 1120.386 0c0 5.757-4.56 14.585-10.192 14.585zm0-23.426a8.848 8.848 0 00-8.839 8.839c0 4.984 4.133 13.231 8.839 13.231s8.838-8.246 8.838-13.231a8.848 8.848 0 00-8.838-8.841z"/><path data-name="Path 7536" d="M22.615 38.592c-.754 0-1.526-.005-2.322-.011a372.906 372.906 0 00-4.961 0c-6.341.042-11.355.076-13.825-2.383a5.467 5.467 0 01-1.506-4.046c0-4.437 3.617-11.8 9.46-15.965l.785 1.1C4.925 21.074 1.352 28.03 1.352 32.15a4.126 4.126 0 001.1 3.086c2.072 2.06 7.078 2.028 12.864 1.989 1.622-.011 3.35-.011 4.976 0 5.786.039 10.785.072 12.858-1.989a4.12 4.12 0 001.1-3.086c0-4.118-3.573-11.074-8.888-14.861l.785-1.1c5.842 4.161 9.457 11.526 9.457 15.965a5.479 5.479 0 01-1.507 4.049c-2.145 2.142-6.245 2.389-11.482 2.389z"/></g></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Manage Account','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://store.posimyth.com/helpdesk" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="38.842" height="39.5"><path data-name="Line 81" stroke="#449fdb" d="M0 0h0"/><path data-name="Path 7557" d="M33.575 15.8h-1.317v-4.3c0-4.807-5.348-10.18-12.508-10.18S7.242 6.691 7.242 11.5v4.3H5.925v-4.3C5.925 6.059 11.604 0 19.75 0s13.825 6.059 13.825 11.5z"/><path data-name="Path 7558" d="M38.183 28.966h-5.267a.659.659 0 01-.658-.658V15.141a.659.659 0 01.658-.658h5.267a.659.659 0 01.658.658v13.167a.659.659 0 01-.658.658zm-4.608-1.317h3.95V15.8h-3.95z"/><path data-name="Path 7559" d="M6.583 28.966H1.316a.659.659 0 01-.658-.658V15.141a.659.659 0 01.658-.658h5.267a.659.659 0 01.658.658v13.167a.659.659 0 01-.658.658zM1.975 27.65h3.95V15.8h-3.95z"/><path data-name="Path 7560" d="M19.75 32.542c-6.492 0-11.192-8.864-11.192-14.841v-2.915A11.3 11.3 0 0119.75 3.414a11.145 11.145 0 0111.191 11.338v3.326c0 6.549-4.991 14.46-11.192 14.46zm0-27.807A9.86 9.86 0 009.875 14.79v2.915c0 5.318 4.241 13.52 9.875 13.52 5.318 0 9.875-7.225 9.875-13.143v-3.33A9.844 9.844 0 0019.75 4.731z"/><path data-name="Path 7561" d="M26.333 39.5h-6.181a2.646 2.646 0 01-2.377-2.555v-.154a2.645 2.645 0 012.377-2.56h6.182a.659.659 0 01.658.658v3.95a.659.659 0 01-.659.661zm-6.182-3.95a1.381 1.381 0 00-1.06 1.239v.154a1.381 1.381 0 001.06 1.239h5.524v-2.633h-5.523z"/><path data-name="Path 7562" d="M29.382 38.184h-2.391a.659.659 0 110-1.317h2.391c1.778 0 2.876-2.083 2.876-4.012v-5.2a.659.659 0 111.317 0v5.2c0 2.561-1.603 5.329-4.193 5.329z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Support Center','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://docs.posimyth.com/tpag/" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="39.98" height="39.5"><path data-name="Line 84" stroke="#449fdb" d="M0 0h0"/><path data-name="Path 7568" d="M3.65 39.5C1.842 39.5 0 37.289 0 35.129a.666.666 0 011.333 0c0 1.509 1.349 3.04 2.317 3.04A2.756 2.756 0 006.315 35.4a.666.666 0 011.333 0 2.611 2.611 0 002.665 2.613 2.682 2.682 0 002.665-2.693.666.666 0 011.333 0 2.665 2.665 0 005.33-.019.666.666 0 011.333 0 2.665 2.665 0 005.33-.007.666.666 0 011.333 0 2.665 2.665 0 005.33 0 .666.666 0 111.333 0c0 1.327.694 2.665 2.249 2.665s2.1-1.527 2.1-2.832a.666.666 0 011.333 0c0 2.452-1.411 4.164-3.43 4.164a3.379 3.379 0 01-2.992-1.678 4 4 0 01-6.586-.111 4 4 0 01-6.662.007 3.995 3.995 0 01-6.658.023A4 4 0 017 37.599a4.023 4.023 0 01-3.352 1.9z"/><path data-name="Path 7569" d="M.666 35.793A.667.667 0 010 35.127V5.807a.666.666 0 011.333 0v29.32a.667.667 0 01-.667.666z"/><path data-name="Path 7570" d="M39.312 35.793a.667.667 0 01-.666-.666V5.807a.666.666 0 011.333 0v29.32a.667.667 0 01-.667.666z"/><path data-name="Path 7571" d="M34.648 1.811H5.33a.666.666 0 110-1.333h29.318a.666.666 0 010 1.333z"/><path data-name="Path 7572" d="M.666 6.475A.667.667 0 010 5.808 5.684 5.684 0 015.316.478a.666.666 0 110 1.333 4.385 4.385 0 00-3.983 4 .667.667 0 01-.666.666z"/><path data-name="Path 7573" d="M39.312 6.475a.667.667 0 01-.666-.666 4.394 4.394 0 00-4-4 .666.666 0 010-1.333 5.692 5.692 0 015.336 5.332.667.667 0 01-.67.667z"/><path data-name="Path 7574" d="M32.649 9.806H7.329a.667.667 0 01-.666-.666v-4a.667.667 0 01.666-.666h25.32a.667.667 0 01.666.666v4a.667.667 0 01-.666.666zM7.996 8.474h23.987V5.809H7.996z"/><path data-name="Path 7575" d="M31.982 16.47H7.995a.666.666 0 110-1.333h23.987a.666.666 0 010 1.333z"/><path data-name="Path 7576" d="M31.982 21.801H7.995a.666.666 0 110-1.333h23.987a.666.666 0 010 1.333z"/><path data-name="Path 7577" d="M31.982 27.131H7.995a.666.666 0 110-1.333h23.987a.666.666 0 110 1.333z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Documentation','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://www.youtube.com/playlist?list=PLFRO-irWzXaLK9H5opSt88xueTnRhqvO5" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="39.5" height="39.5"><path data-name="Line 82" stroke="#449fdb" d="M0 0h0"/><path data-name="Path 7563" d="M38.842 39.5H.658A.659.659 0 010 38.842V.658A.659.659 0 01.658 0h38.184a.659.659 0 01.658.658v38.184a.66.66 0 01-.658.658zM1.317 38.183h36.866V1.317H1.317z"/><path data-name="Path 7564" d="M34.233 32.917H5.266a.659.659 0 110-1.317h28.967a.659.659 0 110 1.317z"/><path data-name="Path 7565" d="M13.825 22.908a.655.655 0 01-.658-.659v-15.2a.658.658 0 01.988-.57l13.167 7.6a.658.658 0 010 1.14l-13.167 7.6a.652.652 0 01-.33.089zm.658-14.72V21.11l11.192-6.464-11.192-6.46z"/><path data-name="Path 7566" d="M11.013 35.385a3.127 3.127 0 113.127-3.127 3.129 3.129 0 01-3.127 3.127zm0-4.937a1.81 1.81 0 101.81 1.81 1.813 1.813 0 00-1.81-1.81z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Video Tutorials','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://www.facebook.com/groups/theplus4gutenberg" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="42.5" height="42.5"><path data-name="Line 83" stroke="#449fdb" d="M0 0h0"/><path data-name="Path 7567" d="M38.411 42.5h-9.37a.71.71 0 01-.708-.708V26.208a.71.71 0 01.708-.708h5.307l1.329-4.25h-6.635a.71.71 0 01-.708-.708v-6.11c0-1.941 2.112-3.1 5.651-3.1h1.432v-4.25h-3.2c-3.7 0-6.714 2.7-6.714 6.021v7.437a.71.71 0 01-.708.708h-6.378V25.5h6.375a.71.71 0 01.708.708V41.79a.71.71 0 01-.708.708H4.766A5.077 5.077 0 010 37.894V4.25C0 1.818 2.516 0 4.766 0h33.645A4.128 4.128 0 0142.5 4.25V37.9c0 2.217-1.643 4.6-4.089 4.6zm-8.661-1.417h8.661c1.577 0 2.672-1.68 2.672-3.187V4.25a2.7 2.7 0 00-2.672-2.833H4.766c-1.517 0-3.349 1.264-3.349 2.833V37.9a3.649 3.649 0 003.349 3.188h19.317V26.917h-6.375a.71.71 0 01-.708-.709v-5.666a.709.709 0 01.708-.708h6.375V13.1c0-4.1 3.646-7.437 8.13-7.437h3.911a.71.71 0 01.708.708v5.667a.71.71 0 01-.708.708h-2.14c-1.955 0-4.234.441-4.234 1.683v5.4h6.891a.706.706 0 01.674.919l-1.771 5.667a.711.711 0 01-.677.5H29.75z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Community','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://theplusblocks.com/change-log/" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="35.55" height="39.5"><path data-name="Path 7588" d="M28.308 39.5H.658A.659.659 0 010 38.841v-31.6a.659.659 0 01.658-.658h27.65a.659.659 0 01.658.658v31.6a.66.66 0 01-.658.659zM1.317 38.183H27.65V7.9H1.317z"/><path data-name="Path 7589" d="M33.575 37.525a.654.654 0 01-.635-.488L31.623 32.1a.615.615 0 01-.024-.17v-26a.659.659 0 01.658-.658h2.633a.659.659 0 01.658.658v26a.615.615 0 01-.024.17l-1.314 4.937a.654.654 0 01-.635.488zm-.658-5.68l.658 2.469.658-2.469V6.584h-1.317z"/><path data-name="Path 7590" d="M34.233 11.85h-1.317a.659.659 0 110-1.317h1.317a.659.659 0 010 1.317z"/><path data-name="Path 7591" d="M34.233 31.6h-1.317a.659.659 0 110-1.317h1.317a.659.659 0 010 1.317z"/><path data-name="Path 7592" d="M21.725 7.242a.659.659 0 01-.658-.658V5.267h-2.156a.659.659 0 01-.658-.658c0-2.061-.985-3.292-2.633-3.292h-1.647c-1.648 0-2.633 1.231-2.633 3.292a.659.659 0 01-.658.658H7.9v1.316a.659.659 0 01-1.317 0V4.608a.659.659 0 01.658-.658h2.814C10.309 1.228 12.15 0 13.976 0h1.646c1.825 0 3.663 1.228 3.92 3.95h2.186a.659.659 0 01.658.658v1.975a.663.663 0 01-.661.658z"/><path data-name="Path 7593" d="M24.358 35.55H4.608a.659.659 0 01-.658-.659v-23.7a.659.659 0 01.658-.658h19.75a.659.659 0 01.658.658v23.7a.66.66 0 01-.658.659zM5.267 34.233H23.7V11.85H5.267z"/><path data-name="Path 7594" d="M19.75 17.117H9.216a.659.659 0 010-1.317H19.75a.659.659 0 110 1.317z"/><path data-name="Path 7595" d="M19.75 22.384H9.216a.659.659 0 010-1.317H19.75a.659.659 0 110 1.317z"/><path data-name="Path 7596" d="M19.75 27.65H9.216a.659.659 0 110-1.317H19.75a.659.659 0 110 1.317z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Changelog','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://theplusblocks.com/" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="52.5" height="38.5"><path data-name="Path 7578" d="M47.25 38.5h-42A5.256 5.256 0 010 33.25v-28A5.256 5.256 0 015.25 0h42a5.256 5.256 0 015.25 5.25v28a5.256 5.256 0 01-5.25 5.25zm-42-36.75a3.5 3.5 0 00-3.5 3.5v28a3.5 3.5 0 003.5 3.5h42a3.5 3.5 0 003.5-3.5v-28a3.5 3.5 0 00-3.5-3.5z"/><path data-name="Path 7579" d="M26.665 25.44a7.666 7.666 0 01-5.679-2.539L7.411 8.052a.876.876 0 011.293-1.181l13.577 14.847a6 6 0 008.78 0l13.551-14.83a.876.876 0 011.293 1.183L32.35 22.901a7.676 7.676 0 01-5.685 2.539z"/><path data-name="Path 7580" d="M8.057 32.41a.876.876 0 01-.59-1.521l11.938-10.9a.875.875 0 111.181 1.292l-11.938 10.9a.878.878 0 01-.591.229z"/><path data-name="Path 7581" d="M45.286 32.41a.867.867 0 01-.59-.229l-11.945-10.9a.875.875 0 011.179-1.292l11.946 10.9a.876.876 0 01-.59 1.521z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Subscribe Us','tpgbp'); ?></div>
							</a>	
						</li>
						<li>
							<a href="https://theplusblocks.com/" target="_blank" rel="noopener noreferrer">
							<div class="sidebar_icon"><svg xmlns="http://www.w3.org/2000/svg" width="47.903" height="39.5"><path data-name="Path 7582" d="M7.144 39.5a.841.841 0 01-.6-1.435 28.2 28.2 0 004.8-7.357C4.325 27.7-.004 22.347-.004 16.6-.004 7.446 10.744 0 23.95 0s23.952 7.446 23.952 16.6S37.157 33.2 23.95 33.2a32.26 32.26 0 01-2.632-.114A18.689 18.689 0 017.144 39.5zM23.952 1.681c-12.28 0-22.271 6.691-22.271 14.918 0 5.258 4.244 10.191 11.075 12.872a.841.841 0 01.472 1.094 31.431 31.431 0 01-4.16 7.152 17.3 17.3 0 0011.277-6.065.839.839 0 01.7-.277c1.109.1 2.03.141 2.9.141 12.28 0 22.271-6.69 22.271-14.918S36.227 1.68 23.946 1.68z"/><path data-name="Path 7585" d="M35.716 14.287H12.184a.84.84 0 010-1.681h23.532a.84.84 0 010 1.681z"/><path data-name="Path 7586" d="M35.716 17.649H12.184a.84.84 0 110-1.681h23.532a.84.84 0 010 1.681z"/><path data-name="Path 7587" d="M23.95 21.011H12.184a.84.84 0 110-1.681H23.95a.84.84 0 110 1.681z"/></svg></div>
							<div class="sidebar_title"><?php echo esc_html__('Feedback','tpgbp'); ?></div>
							</a>	
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	/**
	 * TPGB Pro Rollback
	 * @since 1.3.0 
	 */
	public function tpgb_pro_rollback_render_content(){
		$license_data = Tpgb_Pro_Library::get_instance()->tpgb_activate_status();
		if( !empty($license_data) && $license_data=='valid' ){
			$versions = Tpgb_Pro_Rollback::get_rollback_versions();
			if( !empty($versions) ){
				$dropdown_version = '<select class="tpgb-rollback-pro-list">';
				foreach ( $versions as $version ) {
					$dropdown_version .= '<option value="'.esc_attr($version).'">'.esc_html($version).'</option>';
				}
				$dropdown_version .= '</select>';

				echo '<div class="tpgb-rollback-pro-wrapper">';
					echo '<div class="tpgb-rollback-inner">';
						echo '<table class="form-table">
						<tbody>
							<tr class="tpgb_rb_tr">
								<th scope="row">'.esc_html__('Rollback Pro Version','tpgbp').'</th>
								<td>
									<div id="tpgb-rb-id">
									<div class="tpgb-rb-content">
									'.sprintf(
										$dropdown_version . '<a  data-rv-pro-text="' . esc_html__( 'Reinstall', 'tpgbp' ) . ' v{TPGBP_VERSION}" href="#" data-rv-url="%s" class="button tpgb-pro-rollback-button">%s</a>',
										wp_nonce_url( admin_url( 'admin-post.php?action=tpgb_pro_rollback&version=TPGBP_VERSION' ), 'tpgb_pro_rollback' ),
										__( 'Reinstall', 'tpgbp' )
									).'</div>
										<p class="rollback-description" style="color:red">'. esc_html__( 'Note : Please take a backup of your Database and/or Complete Website before rollback.', 'tpgbp' ) .'</p>
									</div>
								</td>
							</tr>
						</tbody>
						</table>';
					echo '</div>';
				echo '</div>';
			}
		}
	}
	
}

// Get it started
$TPgb_Pro_Gutenberg_Settings_Options = new TPgb_Pro_Gutenberg_Settings_Options();
?>