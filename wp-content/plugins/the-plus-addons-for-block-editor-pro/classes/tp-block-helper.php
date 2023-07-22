<?php
/**
 * TPGB Pro Plugin.
 *
 * @package TPGBP
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Tpgbp_Pro_Blocks_Helper {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	
	protected static $load_blocks;
	
	protected static $deactivate_blocks = [];
	
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
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter('tpgb_has_blocks_condition', [ $this, 'tpgb_has_blocks_options'], 10 , 3 );
		//add_filter('tpgb_extra_load_css_js', [ $this, 'tpgb_extra_css_js_loading'], 10 );
		add_filter('tpgb_load_blocks', [ $this, 'init_blocks_load'] );
		add_filter('tpgb_load_localize', [ $this, 'localize_data'] );
		add_filter('tpgb_blocks_register_render', [ $this, 'load_blocks_registers_render'] );
		add_filter('tpgb_blocks_register', [ $this, 'load_blocks_registers_css_js'] );
		add_filter( 'all_plugins', array($this,'tpgb_free_white_label_plugin') );
		add_filter( 'all_plugins', array($this,'tpgb_pro_white_label_plugin') );
		
		add_action('wp_ajax_tpgb_post_load', [ $this, 'tpgb_post_load'] );
		add_action('wp_ajax_nopriv_tpgb_post_load', [ $this, 'tpgb_post_load'] );
		
		add_filter('tpgb_carousel_options', [ $this, 'tpgb_pro_carousel_options'], 10 );
		add_filter('tpgb_hasWrapper', [ $this, 'tpgb_haswrapper_render'], 10, 2 );
		add_filter('tpgb_globalWrapClass', [ $this, 'tpgb_globalwrapper_class'], 10, 2 );
		add_filter('tpgb_globalAnimOut_filter', [ $this, 'tpgb_globalAnimOut_options'], 10, 2 );

		add_action('tpgb_wrapper_inner_before', [ $this, 'tpgb_wrapper_before_render'] );
		add_action('tpgb_wrapper_inner_after', [ $this, 'tpgb_wrapper_after_render'] );
		
		/* Custom Link url attachment Media */
		add_filter( 'attachment_fields_to_edit', [$this, 'tpgb_attachment_field_media'],  10, 2  );
		add_filter( 'attachment_fields_to_save', [$this, 'tpgb_attachment_field_save'] , 10, 2 );
		
		add_action('wp_ajax_tpgb_reviews_load', array($this, 'tpgb_reviews_load'));
		add_action('wp_ajax_nopriv_tpgb_reviews_load', array($this, 'tpgb_reviews_load'));
		
		/*Get Social Reviews Api Token*/
		add_action('wp_ajax_tpgb_socialreview_Gettoken', array($this, 'tpgb_socialreview_Gettoken'));
		add_action('wp_ajax_nopriv_tpgb_socialreview_Gettoken', array($this, 'tpgb_socialreview_Gettoken'));
		
		add_action('wp_ajax_tpgb_feed_load', array($this, 'tpgb_social_feed_load'));
		add_action('wp_ajax_nopriv_tpgb_feed_load', array($this, 'tpgb_social_feed_load'));

		/*Remove Cache Transient*/
		add_action('wp_ajax_Tp_delete_transient', array($this, 'Tp_delete_transient'));
		add_action('wp_ajax_nopriv_Tp_delete_transient', array($this, 'Tp_delete_transient'));
		
		add_filter( 'body_class', array( $this,'tpgb_body_class') );
		add_filter( 'tpgb_event_tracking', array( $this,'event_tracking_attr') );
	}

	/*
	 * Check Event Tracking Array
	 * @since 2.0.9
	 * */
	public function event_tracking_attr(){
		$event_tracking_data = get_option( 'tpgb_connection_data' );
		$eventTrackArr = [
			'switch' => (!empty($event_tracking_data) && isset($event_tracking_data['tpgb_event_tracking']) && $event_tracking_data['tpgb_event_tracking']=== 'disable') ? false : true,
			'google_track' => (!empty($event_tracking_data) && !empty($event_tracking_data['event_track_google'])) ? $event_tracking_data['event_track_google'] : '',
			'facebook_track' => (!empty($event_tracking_data) && !empty($event_tracking_data['event_track_facebook'])) ? $event_tracking_data['event_track_facebook'] : ''
		];
		return $eventTrackArr;
	}

	/*
	 * Preloader enable add body class
	 * @since 1.3.0
	 */
	public function tpgb_body_class( $classes ){
		$enable_normal_blocks = $this->tpgb_get_option('tpgb_normal_blocks_opts','enable_normal_blocks');
		
		if( !empty($enable_normal_blocks) && in_array('tp-preloader',$enable_normal_blocks)){
			$classes[]="tpgb-body-preloader";
		}
		return $classes;
	}
	
	/*
	 * Check Wrapper Div 
	 */
	public function tpgb_haswrapper_render( $wrapper = false, $attr = []){
		if( !empty($attr) ){
		
			$filterEffect = false;
			if((!empty($attr['globalCssFilter']) && !empty($attr['globalCssFilter']['openFilter'])) || (!empty($attr['globalHCssFilter']['openFilter']) && !empty($attr['globalHCssFilter']))){
				$filterEffect = true;
			}
			
			$Plus3DTilt = false;
			if(!empty($attr['Plus3DTilt']) && !empty($attr['Plus3DTilt']['tpgbReset'])){
				$Plus3DTilt = true;
			}
			
			$PlusMouseParallax = false;
			if(!empty($attr['PlusMouseParallax']) && !empty($attr['PlusMouseParallax']['tpgbReset'])){
				$PlusMouseParallax = true;
			}
			$contentHoverEffect = false;
			if(!empty($attr['contentHoverEffect']) && !empty($attr['selectHoverEffect']) ){
				$contentHoverEffect = true;
			}
			$continueAnimation = false;
			if(!empty($attr['continueAnimation']) && !empty($attr['continueAniStyle']) ){
				$continueAnimation = true;
			}
			$globalTooltip = false;
			if(!empty($attr['globalTooltip']) ){
				$globalTooltip = true;
			}
			$advBorderRadius = false;
			if(!empty($attr['advBorderRadius']) && !empty($attr['advBorderRadius']['tpgbReset'])){
				$advBorderRadius = true;
			}

			/** Event Tracking */
			$check_event_tracker = get_option( 'tpgb_connection_data' );
			$eventTracker = false;
			if(!empty($check_event_tracker) && isset($check_event_tracker['tpgb_event_tracking']) && ($check_event_tracker['tpgb_event_tracking']==='enable' && (!empty($attr['etFacebook']) || !empty($attr['etGoogle'])))){
				$eventTracker = true;
			}

			if( !empty($filterEffect) || !empty($Plus3DTilt) || !empty($PlusMouseParallax) || !empty($contentHoverEffect) || !empty($continueAnimation) || !empty($globalTooltip) || !empty($advBorderRadius) || !empty($eventTracker)){
				$wrapper = true;
			}

            if(!empty($attr['PlusMagicScroll']) ){
                $wrapper = true;
            }
			
		}
		return $wrapper;
	}
	
	/*
	 * Global Varible Classes
	 */
	public function tpgb_globalwrapper_class($classes = '', $attr = []){
		if(!empty($attr['contentHoverEffect']) && !empty($attr['selectHoverEffect']) ){
			$classes .= ' tpgb_cnt_hvr_effect cnt_hvr_'.esc_attr($attr['selectHoverEffect']);
		}
		if(!empty($attr['continueAnimation']) && !empty($attr['continueAniStyle']) ){
			$contiExClass = '';
			if(!empty($attr['continueHoverAnimation'])){
				$contiExClass = 'tpgb-hover-'.esc_attr($attr['continueAniStyle']);
			}else{
				$contiExClass = 'tpgb-normal-'.esc_attr($attr['continueAniStyle']);
			}
			$classes .= ' tpgb_continue_animation '.esc_attr($contiExClass);
		}

        if(!empty($attr['PlusMagicScroll']) ){
			$classes .= ' tpgb_magic_scroll';
		}

		$check_event_tracker = get_option( 'tpgb_connection_data' );
		if(!empty($check_event_tracker) && isset($check_event_tracker['tpgb_event_tracking']) && ($check_event_tracker['tpgb_event_tracking']==='enable' && (!empty($attr['etFacebook']) || !empty($attr['etGoogle'])))){
			$classes .= ' tpgb-event-tracker';
		}

		return $classes;
	}

	/*
	 * Wrapper Before Render
	 */
	public function tpgb_wrapper_before_render( $attr = [] ){
		if( empty($attr) ){
			return  '';
		}
		$wrapInnerClass = '';
		
		$Plus3DTilt = false;
		$tiltAttr = '';
		$tiltSetting = [];
		if(!empty($attr['Plus3DTilt']) && !empty($attr['Plus3DTilt']['tpgbReset'])){
			$Plus3DTilt = true;
			$tiltSetting['max'] = (!empty($attr['Plus3DTilt']['tiltMax']) ? $attr['Plus3DTilt']['tiltMax'] : 20);
			$tiltSetting['perspective'] = (!empty($attr['Plus3DTilt']['tiltPerspective']) ? $attr['Plus3DTilt']['tiltPerspective'] : 400);
			$tiltSetting['scale'] = (!empty($attr['Plus3DTilt']['tiltScale']) ? $attr['Plus3DTilt']['tiltScale'] : 1.18);
			$tiltSetting['speed'] = (!empty($attr['Plus3DTilt']['tiltSpeed']) ? $attr['Plus3DTilt']['tiltSpeed'] : 400);
			$tiltSetting['easing'] = (!empty($attr['Plus3DTilt']['tiltEasing']) && $attr['Plus3DTilt']['tiltEasing']!='custom') ? $attr['Plus3DTilt']['tiltEasing'] : (!empty($attr['Plus3DTilt']['tiltEasingCus']) ? $attr['Plus3DTilt']['tiltEasingCus'] : 'cubic-bezier(.03,.98,.52,.99)');
			$tiltSetting['transition'] = true;
			
			$tiltAttr .= 'data-tiltSetting=\'' .htmlspecialchars(json_encode($tiltSetting), ENT_QUOTES, 'UTF-8'). '\'';
			$wrapInnerClass .= ' tpgb-jstilt';
		}
		
		$gblTooltip ='';$contentItem =[]; $gTooltipAttr = '';$ttId = '';
		$globalTooltip = false;
		if( !empty($attr['globalTooltip'])){
			$wrapInnerClass .= ' tpgb-global-tooltip';
			$globalTooltip = true;
			$uniqid=uniqid("tooltip");
			$gblTooltip .= ' data-tippy=""';
			$gblTooltip .= ' data-tippy-interactive="'.($attr['gbltipInteractive'] ? 'true' : 'false').'"';
			$gblTooltip .= ' data-tippy-placement="'.($attr['gbltipPlacement'] ? $attr['gbltipPlacement'] : 'top').'"';
			$gblTooltip .= ' data-tippy-theme=""';
			$gblTooltip .= ' data-tippy-followCursor="'.($attr['gbltipFlCursor']=='default' ? true : $attr['gbltipFlCursor']).'"';
			$gblTooltip .= ' data-tippy-arrow="'.($attr['gbltipArrow'] ? 'true' : 'false').'"';
			$gblTooltip .= ' data-tippy-animation="'.($attr['gbltipAnimation'] ? $attr['gbltipAnimation'] : 'fade').'"';
			$gblTooltip .= ' data-tippy-offset="['.(!empty($attr['gbltipOffset']) ? (int)$attr['gbltipOffset'] : 0).','.(!empty($attr['gbltipDistance']) ? (int)$attr['gbltipDistance'] : 0).']"';

			$gblTooltip .= ' data-tippy-duration="['.(int)$attr['gbltipDurationIn'].','.(int)$attr['gbltipDurationOut'].']"';
			
			if($attr['gblTooltipType']=='content'){
				$contentItem['content'] = (!empty($attr['gblTooltipText']) && preg_match( '/data-tpgb-dynamic=(.*?)\}/', $attr['gblTooltipText'], $route ))  ? self::tpgb_dynamic_val($attr['gblTooltipText']) : (!empty($attr['gblTooltipText']) ? $attr['gblTooltipText'] : '');
			}else{
				$gblTooltipBlock = '';
				ob_start();
					if(!empty($attr['gblblockTemp'])) {
						echo Tpgb_Library()->plus_do_block($attr['gblblockTemp']);
					}
					$gblTooltipBlock = ob_get_contents();
				ob_end_clean();
				
				$contentItem['content'] = $gblTooltipBlock;
			}
			
			$contentItem['trigger'] = (!empty($attr['gbltipTriggers'])  ? $attr['gbltipTriggers'] : 'mouseenter');


			$gTooltipAttr .= 'data-tooltip-opt=\'' .htmlspecialchars(json_encode($contentItem), ENT_QUOTES, 'UTF-8'). '\'';
			$ttId = 'id="global-'.esc_attr($uniqid).'"';
		}
		
		$advBdrNCss = $advBdrHCss = $advBdrAllCss = '';
		$advBorderRadius = false;
		if(!empty($attr['advBorderRadius']) && !empty($attr['advBorderRadius']['tpgbReset'])){
			$advBorderRadius = true;
			$selBdrArea = (!empty($attr['advBorderRadius']['selBdrArea'])) ? $attr['advBorderRadius']['selBdrArea'] : 'background';
			$advBdrUniqueClass = (!empty($attr['advBorderRadius']['advBdrUniqueClass'])) ? $attr['advBorderRadius']['advBdrUniqueClass'] : '';
			$abNlayout = (!empty($attr['advBorderRadius']['abNlayout'])) ? $attr['advBorderRadius']['abNlayout'] : '';
			$advBdrNcustom = (!empty($attr['advBorderRadius']['advBdrNcustom'])) ? $attr['advBorderRadius']['advBdrNcustom'] : '';
			$abHlayout = (!empty($attr['advBorderRadius']['abHlayout'])) ? $attr['advBorderRadius']['abHlayout'] : '';
			$advBdrHcustom = (!empty($attr['advBorderRadius']['advBdrHcustom'])) ? $attr['advBorderRadius']['advBdrHcustom'] : '';

			$defLayout1 = '100% 0% 100% 0% / 100% 0% 100% 0%';
			$defLayout2 = '140% 60% 100% 0% / 68% 60% 40% 32%';
			$defLayout3 = '73% 27% 100% 0% / 73% 60% 40% 27%';
			$defLayout4 = '0% 100% 50% 50% / 50% 100% 0% 50%';
			$defLayout5 = '44% 56% 50% 50% / 57% 32% 68% 43%';
			$defLayout6 = '71% 29% 35% 65% / 33% 23% 77% 67%';
			$defLayout7 = '26% 74% 25% 75% / 80% 31% 69% 20%';
			$defLayout8 = '0% 100% 25% 75% / 100% 100% 0% 0%';
			$defLayout9 = '49% 51% 59% 41% / 63% 0% 100% 37%';

			/* Normal Css */
			if($abNlayout=='custom'){
				if(!empty($advBdrNcustom)){
					if($selBdrArea=='background'){
						$advBdrNCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' .tpgb-block-'.esc_attr($attr['block_id']).' { border-radius: '.esc_attr($advBdrNcustom).'}';
					}else if($selBdrArea=='custom' && !empty($advBdrUniqueClass)){
						$advBdrNCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' '.esc_attr($advBdrUniqueClass).' { border-radius: '.esc_attr($advBdrNcustom).'}';
					}
				}
			}else{
				$abNlayoutType = '';
				if($abNlayout=='layout-1'){
					$abNlayoutType = $defLayout1;
				}else if($abNlayout=='layout-2'){
					$abNlayoutType = $defLayout2;
				}else if($abNlayout=='layout-3'){
					$abNlayoutType = $defLayout3;
				}else if($abNlayout=='layout-4'){
					$abNlayoutType = $defLayout4;
				}else if($abNlayout=='layout-5'){
					$abNlayoutType = $defLayout5;
				}else if($abNlayout=='layout-6'){
					$abNlayoutType = $defLayout6;
				}else if($abNlayout=='layout-7'){
					$abNlayoutType = $defLayout7;
				}else if($abNlayout=='layout-8'){
					$abNlayoutType = $defLayout8;
				}else if($abNlayout=='layout-9'){
					$abNlayoutType = $defLayout9;
				}
				
				if($selBdrArea=='background'){
					$advBdrNCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' .tpgb-block-'.esc_attr($attr['block_id']).' { border-radius: '.esc_attr($abNlayoutType).'}';
				}else if($selBdrArea=='custom' && !empty($advBdrUniqueClass)){
					$advBdrNCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' '.esc_attr($advBdrUniqueClass).'{ border-radius: '.esc_attr($abNlayoutType).'}';
				}
			}

			/* Hover Css */
			if($abHlayout=='custom'){
				if(!empty($advBdrHcustom)){
					if($selBdrArea=='background'){
						$advBdrHCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' .tpgb-block-'.esc_attr($attr['block_id']).':hover{ border-radius: '.esc_attr($advBdrHcustom).'}';
					}else if($selBdrArea=='custom' && !empty($advBdrUniqueClass)){
						$advBdrHCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' '.esc_attr($advBdrUniqueClass).':hover{ border-radius: '.esc_attr($advBdrHcustom).'}';
					}
				}
			}else{
				$abHlayoutType = '';
				if($abHlayout=='layout-1'){
					$abHlayoutType = $defLayout1;
				}else if($abHlayout=='layout-2'){
					$abHlayoutType = $defLayout2;
				}else if($abHlayout=='layout-3'){
					$abHlayoutType = $defLayout3;
				}else if($abHlayout=='layout-4'){
					$abHlayoutType = $defLayout4;
				}else if($abHlayout=='layout-5'){
					$abHlayoutType = $defLayout5;
				}else if($abHlayout=='layout-6'){
					$abHlayoutType = $defLayout6;
				}else if($abHlayout=='layout-7'){
					$abHlayoutType = $defLayout7;
				}else if($abHlayout=='layout-8'){
					$abHlayoutType = $defLayout8;
				}else if($abHlayout=='layout-9'){
					$abHlayoutType = $defLayout9;
				}
				
				if($selBdrArea=='background'){
					$advBdrHCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' .tpgb-block-'.esc_attr($attr['block_id']).':hover{ border-radius: '.esc_attr($abHlayoutType).'}';
				}else if($selBdrArea=='custom' && !empty($advBdrUniqueClass)){
					$advBdrHCss = '.tpgb-wrap-'.esc_attr($attr['block_id']).' '.esc_attr($advBdrUniqueClass).':hover{ border-radius: '.esc_attr($abHlayoutType).'}';
				}
			}

			$advBdrAllCss = $advBdrNCss.' '.$advBdrHCss;
		}

		$check_event_tracker = get_option( 'tpgb_connection_data' );
		$eventTracker = false;
		$eventTattr = '';
		if(!empty($check_event_tracker) && isset($check_event_tracker['tpgb_event_tracking']) && ($check_event_tracker['tpgb_event_tracking']==='enable' && (!empty($attr['etFacebook']) || !empty($attr['etGoogle'])))){
			$eventTracker = true;
			$wrapInnerClass .= ' tpgb-event-tracker-inner';

			$propertiesAttr =[];
			if(!empty($attr['eventProperties'])){
				foreach ( $attr['eventProperties'] as $index => $item ) :
					if(!empty($item['eProName'])){
						$propertiesAttr[] =[
							$item['eProName'] => $item['eProValue']
						];
					} 
				endforeach;
			}
			
			$eAttr = [
				'facebook' => !empty($attr['etFacebook']) ? true : false,
				'fbEventType' => !empty($attr['fbEventType']) ? $attr['fbEventType'] : 'ViewContent',
				'fbCsmEventName' => !empty($attr['fbCsmEventName']) ? $attr['fbCsmEventName'] : '',
				'google' => !empty($attr['etGoogle']) ? true : false,
				'gglEventType' => !empty($attr['gglEventType']) ? $attr['gglEventType'] : 'recommended',
				'gglSelEvent' => !empty($attr['gglSelEvent']) ? $attr['gglSelEvent'] : 'ad_impression',
				'gCsmEventName' => !empty($attr['gCsmEventName']) ? $attr['gCsmEventName'] : '',
				'eventProperties' => $propertiesAttr,
			];
			$eventTattr .= 'data-event-opt=\'' .htmlspecialchars(json_encode($eAttr), ENT_QUOTES, 'UTF-8'). '\'';
		}

		$PlusMouseParallax = false;
		$MouseParallaxAttr = '';
		if(!empty($attr['PlusMouseParallax']) && !empty($attr['PlusMouseParallax']['tpgbReset'])){
			$PlusMouseParallax = true;
			$MouseParallaxAttr .= ' data-speedx="'.(!empty($attr['PlusMouseParallax']['moveX']) ? $attr['PlusMouseParallax']['moveX'] : 30).'"';
			$MouseParallaxAttr .= ' data-speedy="'.(!empty($attr['PlusMouseParallax']['moveY']) ? $attr['PlusMouseParallax']['moveY'] : 30).'"';
			
			$wrapInnerClass .= ' tpgb-parallax-move';
		}
		
		$output = '';
		if(!empty($Plus3DTilt) || !empty($PlusMouseParallax) || !empty($globalTooltip) || !empty($advBorderRadius) || !empty($eventTracker)){
			$output .= '<div class="'.$wrapInnerClass.'" '.$tiltAttr.' '.$MouseParallaxAttr.' '.$gblTooltip.' '.$gTooltipAttr.' '.$ttId.' '.$eventTattr.'>';
		}
		
		if((!empty($attr['globalCssFilter']) && !empty($attr['globalCssFilter']['openFilter'])) || (!empty($attr['globalHCssFilter']['openFilter']) && !empty($attr['globalHCssFilter']))){
			$output .= '<div class="tpgb-cssfilters">';
		}

		if(!empty($advBorderRadius) && !empty($advBdrAllCss)){
			$output .= '<style>'.wp_strip_all_tags($advBdrAllCss).'</style>';
		}

		echo $output;
	}
	
	/*
	 * Wrapper After Render
	 */
	public function tpgb_wrapper_after_render( $attr = [] ){
		if( empty($attr) ){
			return  '';
		}
		
		$Plus3DTilt = false;
		if(!empty($attr['Plus3DTilt']) && !empty($attr['Plus3DTilt']['tpgbReset'])){
			$Plus3DTilt = true;
		}
		$globalTooltip = false;
		if( !empty($attr['globalTooltip'])){
			$globalTooltip = true;
		}
		$advBorderRadius = false;
		if(!empty($attr['advBorderRadius']) && !empty($attr['advBorderRadius']['tpgbReset'])){
			$advBorderRadius = true;
		}

		$check_event_tracker = get_option( 'tpgb_connection_data' );
		$eventTracker = false;
		if(!empty($check_event_tracker) && isset($check_event_tracker['tpgb_event_tracking']) && ($check_event_tracker['tpgb_event_tracking']==='enable' && (!empty($attr['etFacebook']) || !empty($attr['etGoogle'])))){
			$eventTracker = true;
		}

		$PlusMouseParallax = false;
		if(!empty($attr['PlusMouseParallax']) && !empty($attr['PlusMouseParallax']['tpgbReset'])){
			$PlusMouseParallax = true;
		}
		
		$output = '';
		if((!empty($attr['globalCssFilter']) && !empty($attr['globalCssFilter']['openFilter'])) || (!empty($attr['globalHCssFilter']['openFilter']) && !empty($attr['globalHCssFilter']))){
			$output .= '</div>';
		}
		if(!empty($Plus3DTilt) || !empty($PlusMouseParallax) || !empty($globalTooltip) || !empty($advBorderRadius) || !empty($eventTracker)){
			$output .= '</div>';
		}
		
		echo $output;
	}
	
	/*
	 * Animation Out Options
	 */
	public static function tpgbAnimationOutDevice($globalAnim='', $AnimDirect='',$device=''){
		$animationVal = '';
		if($globalAnim=='fadeOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'fadeOut' : 'fadeOut'.$AnimDirect[$device]);
		}else if($globalAnim=='slideOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'slideOutDown' : 'slideOut'.$AnimDirect[$device]);
		}else if($globalAnim=='zoomOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'zoomOut' : 'zoomOut'.$AnimDirect[$device]);
		}else if($globalAnim=='rotateOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'rotateOut' : 'rotateOut'.$AnimDirect[$device]);
		}else if($globalAnim=='flipOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'flipOutX' : 'flipOut'.$AnimDirect[$device]);
		}else if($globalAnim=='lightSpeedOut'){
			$animationVal .= (($AnimDirect[$device]==='' || $AnimDirect[$device]==='default') ? 'lightSpeedOutLeft' : 'lightSpeedOut'.$AnimDirect[$device]);
		}else if($globalAnim=='rollOut'){
			$animationVal .= 'rollOut';
		}
		return $animationVal;
	}
	
	/*
	 * Animation Out Options
	 */
	public function tpgb_globalAnimOut_options( $settings = [], $attr = [] ){
		if( !empty($attr) && !empty($attr['globalAnimOut']) ){
			if(!empty($attr['globalAnimOut']['md']) && $attr['globalAnimOut']['md']!='none'){
				$settings['check'] = true;
				if( !empty($attr['globalAnimDirectOut']) ){
					$settings['md'] = self::tpgbAnimationOutDevice($attr['globalAnimOut']['md'], $attr['globalAnimDirectOut'], 'md');
				}
			}
			if(!empty($attr['globalAnimOut']['sm']) && $attr['globalAnimOut']['sm']!='none'){
				$settings['check'] = true;
				if( !empty($attr['globalAnimDirectOut']) ){
					$settings['sm'] = self::tpgbAnimationOutDevice($attr['globalAnimOut']['sm'], $attr['globalAnimDirectOut'], 'sm');
				}
			}
			if(!empty($attr['globalAnimOut']['xs']) && $attr['globalAnimOut']['xs']!='none'){
				$settings['check'] = true;
				if( !empty($attr['globalAnimDirectOut']) ){
					$settings['xs'] = self::tpgbAnimationOutDevice( $attr['globalAnimOut']['xs'], $attr['globalAnimDirectOut'], 'xs');
				}
			}
		}
		return $settings;
	}
	
	/*
	 * Extra Css Js Load in  
	 
	public function tpgb_extra_css_js_loading( $blocks=[] ){
		$extra_js = [];
		if(tpgb_has_lazyload()){
			$extra_js[] = 'tpgb_lazyLoad'; 
		}
		$blocks = array_merge( $extra_js,$blocks );

		return $blocks;
	}
	*/
	
	/*
	 * Render Block Condition Check 
	 */
	public function tpgb_has_blocks_options( $blocks=[], $options='' , $blockname='' ){
		$pro_blocks = [];
		
		if($blockname=='tpgb/tp-accordion' && !empty($options) && !empty($options['hoverStyle']) && $options['hoverStyle']!='none'){
			$pro_blocks[] = 'tpx-accordion-'.$options['hoverStyle'];
		}

		// TP Audio Player
		if($blockname=='tpgb/tp-audio-player' && !empty($options) ){
			if( !empty($options['Apstyle']) ){
				$pro_blocks[] = 'tpx-audio-player-'.$options['Apstyle'];
			}
		}

		//Carousel Remote
		if($blockname=='tpgb/tp-carousel-remote' && !empty($options)){
			if( isset($options['showDot']) && !empty($options['showDot']) ){
				$pro_blocks[] = 'tpgx-carousel-dot';

				if( isset($options['dotstyle']) && !empty($options['dotstyle']) && $options['dotstyle'] == 'style-2' ){
					$pro_blocks[] = 'tpgx-carousel-tooltip';
				}

			}
			if( isset($options['showpagi']) && !empty($options['showpagi']) ){
				$pro_blocks[] = 'tpgx-carousel-pagination';
			}
			if( isset($options['carobtn']) && !empty($options['carobtn']) ){
				$pro_blocks[] = 'tpgx-carousel-button';
			}else{
				$pro_blocks[] = 'tpgx-carousel-button';
			}
		}

		if(!empty($options) && !empty($options['globalAnimOut']) && ((!empty($options['globalAnimOut']['md']) && $options['globalAnimOut']['md']!='none') || (!empty($options['globalAnimOut']['sm']) && $options['globalAnimOut']['sm']!='none') || (!empty($options['globalAnimOut']['xs']) && $options['globalAnimOut']['xs']!='none'))){
			$pro_blocks[] = 'tpgb-animation';
			if(isset($options['globalAnimOut']['md']) && $options['globalAnimOut']['md']!='none'){
				$pro_blocks[] = 'tpgb-animation-'.$options['globalAnimOut']['md'];
			}
			if(isset($options['globalAnimOut']['sm']) && $options['globalAnimOut']['sm']!='none'){
				$pro_blocks[] = 'tpgb-animation-'.$options['globalAnimOut']['sm'];
			}
			if(isset($options['globalAnimOut']['xs']) && $options['globalAnimOut']['xs']!='none'){
				$pro_blocks[] = 'tpgb-animation-'.$options['globalAnimOut']['xs'];
			}
		}
		
		if(!empty($options) && !empty($options['contentHoverEffect']) && !empty($options['selectHoverEffect'])){
			$pro_blocks[] = 'content-hover-effect';
		}

		// CTA Banner
		if($blockname=='tpgb/tp-cta-banner' ){
			if( !empty($options) && !empty($options['styleType']) ){
				$pro_blocks[] = 'tpx-cta-'.$options['styleType'];
			}else{
				$pro_blocks[] = 'tpx-cta-style-1';
			}
		}

		if((!empty($options) && !empty($options['continueAnimation'])) || ($blockname=='tpgb/tp-dynamic-device' && !empty($options) && !empty($options['iconConAni']))){
			$pro_blocks[] = 'continue-animation';
		}
		if(!empty($options) && !empty($options['tpgbEqualHeight'])){
			$pro_blocks[] = 'equal-height';
		}
		if(!empty($options) && !empty($options['globalTooltip'])){
			$pro_blocks[] = 'global-tooltip';
		}
        if(!empty($options) && !empty($options['PlusMagicScroll']) ){        //Global Magic Scroll
            $pro_blocks[] = 'tpgb-magic-scroll';
            $pro_blocks[] = 'tpgb-magic-scroll-custom';
        }
		/** Tp Hotspot */
		if($blockname =='tpgb/tp-hotspot'){
			if(!empty($options) && !empty($options['hveOverlay'])){
				$pro_blocks[] = 'tpx-hover-overlay';
			}
			if(!empty($options) && !empty($options['pinlistRepeater']) && is_array($options['pinlistRepeater']) ){
				foreach( $options['pinlistRepeater'] as $key => $val ){
					if(!empty($val) && isset($val['contEffect']) && !empty($val['contEffect'])){
						$pro_blocks[] = 'tpgb-plus-hover-effect';
					}
				}
			}
		}
		/*Tp Row Background*/
		if($blockname=='tpgb/tp-row' || $blockname=='tpgb/tp-container'){
			if(!empty($options) && ( !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_color' ) ){	//Animated Color
				$pro_blocks[] = 'tpgb-row-animated-color';   
			}
			
			if( !empty($options) && !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_image' && isset($options['imgeffect']) && !isset($options['craBgeffect']) && !empty($options['imgeffect']) && ( $options['imgeffect'] == 'style-1' || $options['imgeffect'] == 'style-2' ) ) {
				$pro_blocks[] = 'tpgb-image-parallax'; 
			}

			if( !empty($options) && ( (!empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_image' && isset($options['craBgeffect']) && !empty($options['craBgeffect']) && $options['craBgeffect'] = 'columns_animated_bg') || ( !empty($options['midOption']) && $options['midOption'] == 'moving_image' ) ) ){
				$pro_blocks[] = 'tpgb-image-moving';
			}	

			if( !empty($options) && !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_image'  && isset($options['scrollPara']) && !empty($options['scrollPara']) ){
				$pro_blocks[] = 'tpgb-scroll-parallax';
			} 

			if( !empty($options) && !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_video' && !isset($options['videosour'])  && isset($options['youtubeId']) && !empty($options['youtubeId']) ){
				$pro_blocks[] = 'tpgb-youtube-video';
			}

			if( !empty($options) && !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_video' && isset($options['videosour']) && !empty($options['videosour']) && $options['videosour'] == 'vimeo' ){
				$pro_blocks[] = 'tpgb-vimeo-video';
			}

			if( !empty($options) && !empty($options['deepBgopt']) && $options['deepBgopt'] == 'bg_gallery' ){
				$pro_blocks[] = 'tpgb-bg-gallery';
			}

			if( !empty($options) && !empty($options['midOption']) && ( $options['midOption'] == 'mordern_image_effect' ) ){
				$pro_blocks[] = 'tpgb-magic-scroll';
            	$pro_blocks[] = 'tpgb-magic-scroll-custom';
				$pro_blocks[] = 'tpgb-mordern-parallax';
			}
			
			if(!empty($options) && ( !empty($options['scrollPara']) || ( !empty($options['midOption']) && $options['midOption'] == 'mordern_image_effect') ) ){	//scroll Parallax/Mordern Parallax
				$pro_blocks[] = 'tpgb-magic-scroll';
			}

			if( !empty($options) && !empty($options['midOption']) && $options['midOption'] == 'canvas' && isset($options['canvasSty']) && !empty($options['canvasSty']) ){	//Canvas
				$pro_blocks[] = 'tpgb-canvas-particle';
			}

			if( !empty($options) && !empty($options['midOption']) && $options['midOption'] == 'canvas' && isset($options['canvasSty']) && !empty($options['canvasSty']) && $options['canvasSty'] == 'style-4' ){
				$pro_blocks[] = 'tpgb-canvas-particleground';   
			}

			if(!empty($options) && !empty($options['deepBgopt']) && ($options['deepBgopt'] == 'bg_animate_gradient' || $options['deepBgopt'] == 'scroll_animate_color' ) ){	//scroll bg color
				$pro_blocks[] = 'tpgb-scrollbg-animation';   
			}
			if( !empty($options) && (!empty($options['shapeTop']) || !empty($options['shapeBottom'])) ){
				$pro_blocks[] = 'tpx-tp-shape-divider';
			}

			if( !empty($options['midimgList']) && is_array($options['midimgList']) ){
				foreach( $options['midimgList'] as $key => $val ){
					if(!empty($val) && isset($val['modImgeff']) && !empty($val['modImgeff'])){
						$pro_blocks[] = 'tpgb-plus-hover-effect';
					}
				}
			}
		}
		/*Column*/
		if($blockname=='tpgb/tp-column' || $blockname=='tpgb/tp-container-inner'){
			if( !empty($options) && !empty($options['stickycol']) ){
				$pro_blocks[] = 'tpgb-sticky-col';
			}
		}
		/*Media Listing*/
		if($blockname=='tpgb/tp-media-listing') {
			if(!empty($options) && !empty($options['layout']) && $options['layout']=='carousel'){
				$pro_blocks[] = 'carouselSlider';
			}
			if(!empty($options) && !empty($options['layout']) && $options['layout']=='metro'){
				$pro_blocks[] = 'tpx-media-metro-style';
			}

			if( !empty($options) && !empty($options['Category']) ){
				$pro_blocks[] = 'tpgb-category-filter';
			}

			if( !empty($options) && !empty($options['style']) ){
				$pro_blocks[] = 'tpx-media-listing-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-media-listing-style-1';
			}
		}
		/*Post Listing*/
		if($blockname=='tpgb/tp-post-listing'){
			if( !empty($options) && ( !empty($options['ShowFilter']) || ( !empty($options['postLodop']) && ( $options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load'  ) ) ) ){ //Category Filter / Load More / Lazy Load
				$pro_blocks[] = 'tpgb-category-filter';  
			}
			if(!empty($options) && !empty($options['layout']) && $options['layout']=='carousel'){	//Carousel
				$pro_blocks[] = 'carouselSlider';
			}
			if(!empty($options) && !empty($options['style']) && $options['style'] =='style-3' && !empty($options['ShowButton'])){	//Button Group
				$pro_blocks[] = 'tpgb-group-button';   
			}
			if(!empty($options) && !empty($options['postLodop']) && ($options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load')  ){	//Post Load Ajax
				$pro_blocks[] = 'tpgb-post-load-ajax';  
			}
			if( !empty($options) && isset($options['style']) && !empty($options['style']) ){
				$pro_blocks[] = 'tpx-post-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-post-style-1';
			}
			if( !empty($options) && isset($options['layout']) && $options['layout'] == 'metro' ){
				$pro_blocks[] = 'tpx-post-metro';
			}
		}
		/*Product Listing*/
		if($blockname=='tpgb/tp-product-listing') {
			if(!empty($options) && !empty($options['postLodop']) && $options['postLodop'] == 'pagination' ){
				$pro_blocks[] = 'tpgb-pagination';
			}
			if( !empty($options) && ( !empty($options['ShowFilter']) || ( !empty($options['postLodop']) && ( $options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load'  ) ) ) ){ //Category Filter / Load More / Lazy Load
				$pro_blocks[] = 'tpgb-category-filter';  
			}
			if( !empty($options) && !empty($options['layout']) && $options['layout'] == 'carousel'){ 
				$pro_blocks[] = 'carouselSlider';
			}
			if( !empty($options) && !empty($options['postLodop']) && ($options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load')  ){
				$pro_blocks[] = 'tpgb-post-load-ajax';  
			}
			if( !empty($options) && isset($options['style']) && !empty($options['style']) ){
				$pro_blocks[] = 'tpx-product-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-product-style-1';
			}
			if( !empty($options) && isset($options['layout']) && $options['layout'] == 'metro' ){
				$pro_blocks[] = 'tpx-product-metro';
			}
		}
		
		/*Navigation Menu*/
		if($blockname=='tpgb/tp-navigation-builder' && !empty($options) && !empty($options['resmenuType']) && $options['resmenuType']=='swiper'){
			$pro_blocks[] = 'swiperJs';
		}
		if($blockname=='tpgb/tp-navigation-builder' && !empty($options) && !empty($options['accessWeb']) ){
			$pro_blocks[] = 'tpgb-web-access';
		}
		/*Tabs Tours*/
		if($blockname=='tpgb/tp-tabs-tours' && !empty($options) && !empty($options['swiperEffect'])){
			$pro_blocks[] = 'swiperJs';
		}
		if($blockname=='tpgb/tp-tabs-tours' && !empty($options) && !empty($options['tabLayout']) && $options['tabLayout']=='vertical'){
			$pro_blocks[] = 'tpx-tabs-tours-vertical';
		}
		
		/* Scroll Navigation  */
		if($blockname=='tpgb/tp-scroll-navigation'){
			if(!empty($options) && !empty($options['styletype'])){
				$pro_blocks[] = 'tpx-scroll-navigation-'.$options['styletype'];
			}else{
				$pro_blocks[] = 'tpx-scroll-navigation-style-1';
			}
			if(!empty($options) && isset($options['disCounter']) && !empty($options['disCounter'])){
				$pro_blocks[] = 'tpx-display-counter';
			}
			if(!empty($options) && isset($options['scrolloff']) && !empty($options['scrolloff'])){
				$pro_blocks[] = 'tpx-scroll-offset';
			}
		}

		/*switcher*/
		if($blockname=='tpgb/tp-switcher' && !empty($options) && !empty($options['switchStyle'])){
			$pro_blocks[] = 'tpx-switcher-'.$options['switchStyle'];
		}else if($blockname=='tpgb/tp-switcher'){
			$pro_blocks[] = 'tpx-switcher-style-1';
		}
		
		/*Post Navigation*/
		if($blockname=='tpgb/tp-post-navigation'){
			if( !empty($options) && !empty($options['style']) ){
				$pro_blocks[] = 'tpx-post-navigation-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-post-navigation-style-1';
			}
		}
		
		/*Table of Content*/
		if($blockname=='tpgb/tp-table-content'){
			if( !empty($options) && !empty($options['Style']) ){
				$pro_blocks[] = 'tpx-table-content-'.$options['Style'];
			}
		}

		/*Tp Adv Typo*/
		if($blockname=='tpgb/tp-adv-typo'){
			if( !empty($options) && !empty($options['advUnderline']) && $options['advUnderline']=='overlay' ){
				$pro_blocks[] = 'tpx-adv-typo-normal-overlay';
			}
			if( !empty($options) && !empty($options['typoListing']) && $options['typoListing']=='multiple' ){
				$pro_blocks[] = 'tpx-adv-typo-multiple';
			}
		}

		/*Stylist List*/
		if($blockname=='tpgb/tp-stylist-list' && !empty($options)){
			if( !empty($options['hover_bg_style']) ){
				$pro_blocks[] = 'tpx-stylist-list-hover-bg';
			}
			if( !empty($options['hoverInverseEffect']) ){
				$pro_blocks[] = 'tpx-stylist-list-hover-inverse';
			}
		}
		
		/*Creative Image*/
		if($blockname=='tpgb/tp-creative-image' && !empty($options)){	
			if( !empty($options['ScrollParallax']) ){
				$pro_blocks[] = 'tpgb-magic-scroll';
			}
			if( !empty($options['ScrollImgEffect']) ){
				$pro_blocks[] = 'tpx-tp-image-scroll-effect';
			}
			if( !empty($options['showMaskImg']) ){
				$pro_blocks[] = 'tpx-tp-image-mask-img';
			}
			if( !empty($options['ScrollRevelImg']) ){
				$pro_blocks[] = 'tpx-tp-image-animate';
			}
			if( !empty($options['ScrollParallax']) ){
				$pro_blocks[] = 'tpx-tp-image-parallax';
			}
		}
		/*CountDown*/
		if($blockname=='tpgb/tp-countdown'){
			if( !empty($options) && !empty($options['style'])) {
				$pro_blocks[] = 'countdown-'.$options['style'];
			}else{
				$pro_blocks[] = 'countdown-style-1';
			}

			if( !empty($options) && !empty($options['countdownSelection']) && $options['countdownSelection'] == 'numbers') {
				$pro_blocks[] = 'countdown-fakestyle';
			}
		}
		
		/*Cta Banner*/
		if(!empty($options) && !empty($options['hoverStyle']) && $options['hoverStyle']=='hover-tilt'){	
			$pro_blocks[] = 'hoverTilt';
		}
		/*Google Map*/
		if($blockname=='tpgb/tp-google-map' && !empty($options) && !empty($options['contentTgl'])){	
			$pro_blocks[] = 'tpx-google-map-content';
		}

		/*Team Listing*/
		if($blockname=='tpgb/tp-team-listing'){
			if( !empty($options) && !empty($options['layout']) && $options['layout']=='carousel'){
				$pro_blocks[] = 'carouselSlider';
			}

			if( !empty($options) && !empty($options['Style'])){
				$pro_blocks[] = 'tpx-team-list-'.$options['Style'];
			}else{
				$pro_blocks[] = 'tpx-team-list-style-1';
			}
		}

		/*popup builder*/
		if( $blockname=='tpgb/tp-popup-builder' ){

			if( !empty($options) && isset($options['toggleIconStyle']) && !empty($options['toggleIconStyle']) ){
				$pro_blocks[] = 'tpx-humberger-'.$options['toggleIconStyle'].'';
			}else{
				$pro_blocks[] = 'tpx-humberger-style-1';
			}

			if(!empty($options) && isset($options['fixedToggleBtn']) && !empty($options['fixedToggleBtn'])){
				$pro_blocks[] = 'tpx-fixed-popup-toggle';
			}

			if(!empty($options) && isset($options['openStyle']) && $options['openStyle'] == 'slide' ){
				$pro_blocks[] = 'tpx-slide';
			}else if( !empty($options) && isset($options['openStyle']) && $options['openStyle'] == 'push' ){
				$pro_blocks[] = 'tpx-push-content';
			}else if( !empty($options) && isset($options['openStyle']) && $options['openStyle'] == 'slide-along' ){
				$pro_blocks[] = 'tpx-slide-along';
			}else if( !empty($options) && isset($options['openStyle']) && $options['openStyle'] == 'corner-box' ){
				$pro_blocks[] = 'tpx-corner-box';
			}else{
				$pro_blocks[] = 'tpx-popup-effect';
			}

			if( !empty($options) && !isset($options['openStyle']) ){
				$pro_blocks[] = 'tpgb-popup-animation';
			}
			
			if( !empty($options) && !isset($options['openStyle'])  ){
				
				if( isset($options['inAnimation']) && !empty($options['inAnimation']) ) {
					if( $options['inAnimation'] == 'slideInDown' || $options['inAnimation'] == 'slideInLeft' || $options['inAnimation'] == 'slideInRight' || $options['inAnimation'] == 'slideInUp' ){
						$pro_blocks[] = 'tpgb-animation-slideIn';
					}else if( $options['inAnimation'] == 'zoomIn' || $options['inAnimation'] == 'zoomInDown' || $options['inAnimation'] == 'zoomInLeft' || $options['inAnimation'] == 'zoomInRight' || $options['inAnimation'] == 'zoomInUp' ){
						$pro_blocks[] = 'tpgb-animation-zoomIn';
					}else if( $options['inAnimation'] == 'rotateIn' || $options['inAnimation'] == 'rotateInDownLeft' || $options['inAnimation'] == 'rotateInDownRight' || $options['inAnimation'] == 'rotateInUpLeft' || $options['inAnimation'] == 'rotateInUpRight' ){
						$pro_blocks[] = 'tpgb-animation-rotateIn';
					}else if( $options['inAnimation'] == 'flipInX' || $options['inAnimation'] == 'flipInY' ){
						$pro_blocks[] = 'tpgb-animation-flipIn';
					}else if( $options['inAnimation'] == 'lightSpeedInLeft' || $options['inAnimation'] == 'lightSpeedInRight' ){
						$pro_blocks[] = 'tpgb-animation-lightSpeedIn';
					}else if( $options['inAnimation'] == 'bounce' || $options['inAnimation'] == 'flash' || $options['inAnimation'] == 'pulse' || $options['inAnimation'] == 'rubberBand' || $options['inAnimation'] == 'shakeX' || $options['inAnimation'] == 'shakeY' || $options['inAnimation'] == 'headShake' || $options['inAnimation'] == 'swing' || $options['inAnimation'] == 'tada' || $options['inAnimation'] == 'wobble' || $options['inAnimation'] == 'jello' || $options['inAnimation'] == 'heartBeat'  ){
						$pro_blocks[] = 'tpgb-animation-seekers';
					}else if( $options['inAnimation'] == 'rollIn' ){
						$pro_blocks[] = 'tpgb-animation-rollIn';
					}else{
						$pro_blocks[] = 'tpgb-animation-fadeIn';
					}
				} else{
					$pro_blocks[] = 'tpgb-animation-fadeIn';
				}
				
				if( isset($options['outAnimation']) && !empty($options['outAnimation']) ) {
					if( $options['outAnimation'] == 'slideOutDown' || $options['outAnimation'] == 'slideOutLeft' || $options['outAnimation'] == 'slideOutRight' || $options['outAnimation'] == 'slideOutUp' ){ 
						$pro_blocks[] = 'tpgb-animation-slideOut';
					} else if( $options['outAnimation'] == 'zoomOut' || $options['outAnimation'] == 'zoomOutDown' || $options['outAnimation'] == 'zoomOutLeft' || $options['outAnimation'] == 'zoomOutRight' || $options['outAnimation'] == 'zoomOutUp' ){
						$pro_blocks[] = 'tpgb-animation-zoomOut';
					} else if( $options['outAnimation'] == 'rotateOut' || $options['outAnimation'] == 'rotateOutDownLeft' || $options['outAnimation'] == 'rotateOutDownRight' || $options['outAnimation'] == 'rotateOutUpLeft' || $options['outAnimation'] == 'rotateOutUpRight'  ){
						$pro_blocks[] = 'tpgb-animation-rotateOut';
					} else if( $options['outAnimation'] == 'flipOutX' || $options['outAnimation'] == 'flipOutY'  ){
						$pro_blocks[] = 'tpgb-animation-flipOut';
					} else if( $options['outAnimation'] == 'lightSpeedOutLeft' || $options['outAnimation'] == 'lightSpeedOutRight'  ){
						$pro_blocks[] = 'tpgb-animation-lightSpeedOut';
					} else if( $options['outAnimation'] == 'rollOut' ){
						$pro_blocks[] = 'tpgb-animation-rollOut';
					}else{
						$pro_blocks[] = 'tpgb-animation-fadeOut';
					}
				}else{
					$pro_blocks[] = 'tpgb-animation-fadeOut';
				}
			}
		}

		/*social Review*/
		if($blockname=='tpgb/tp-social-reviews' && !empty($options)){
			if(!empty($options['RType']) && $options['RType']=='beach'){
				if(!empty($options['Bstyle'])){
					$pro_blocks[] = 'tpx-beach-'.$options['Bstyle'];
				}else{
					$pro_blocks[] = 'tpx-beach-style-1';
				}
			}else{
				if(!empty($options['style'])){
					$pro_blocks[] = 'tpx-review-'.$options['style'];
				}else{
					$pro_blocks[] = 'tpx-review-style-1';
				}
			}
			if(!empty($options['layout']) && $options['layout'] == 'carousel'){
				$pro_blocks[] = 'carouselSlider';
			}
			if(!empty($options['postLodop']) && ($options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load')){
				$pro_blocks[] = 'review-feed-load';
			}
		}

		/*social Sharing*/
		if($blockname=='tpgb/tp-social-sharing' && !empty($options)){
			if(!empty($options['sociallayout'])){
				$pro_blocks[] = 'tpx-social-sharing-'.$options['sociallayout'];
			}else{
				$pro_blocks[] = 'tpx-social-sharing-horizontal';
			}
		}

		/*social Feed*/
		if($blockname=='tpgb/tp-social-feed' && !empty($options)){
			if(!empty($options['style']) && ($options['style']=='style-3' || $options['style']=='style-4')){
				$pro_blocks[] = 'tpx-social-feed-'.$options['style'];
			}
			if((!empty($options['layout']) && $options['layout'] == 'carousel') || (!empty($options['OnPopup']) && $options['OnPopup']=='OnFancyBox')){
				$pro_blocks[] = 'carouselSlider';
			}
			if(!empty($options['postLodop']) && ($options['postLodop'] == 'load_more' || $options['postLodop'] == 'lazy_load')){
				$pro_blocks[] = 'review-feed-load';
			}
			if( ( !empty($options['layout']) && $options['layout']!='carousel') && ( !empty($options['CategoryWF']) || !empty($options['postLodop'] ) ) ){
				$pro_blocks[] = 'tpgb-category-filter';
			}
		}

		/*Circle Menu*/
		if($blockname=='tpgb/tp-circle-menu' && !empty($options)){
			if(!empty($options['layoutType']) && $options['layoutType']=='straight'){
				$pro_blocks[] = 'tpx-circle-menu-straight';
			}
			if(!empty($options['tglStyle'])){
				$pro_blocks[] = 'tpx-circle-menu-toggle-'.$options['tglStyle'];
			}
			if(!empty($options['overlayColorTgl'])){
				$pro_blocks[] = 'tpx-circle-menu-overlay';
			}
		}

		/* Tp Testimonial  */
		if($blockname=='tpgb/tp-testimonials'){
			if( !empty($options) && !empty($options['telayout']) && $options['telayout']!='carousel' ){
				$pro_blocks[] = 'tpgb_grid_layout';
			}
			
			if( !empty($options) && !empty($options['style']) ){
				$pro_blocks[] = 'tpx-testimonials-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-testimonials-style-1';
			}

			if( ( !empty($options) &&  !empty($options['telayout']) && $options['telayout'] == 'grid' ) || ( isset($options['caroByheight']) && !empty( $options['caroByheight'] ) && $options['caroByheight'] == 'height' ) ){
				$pro_blocks[] = 'tpx-testimonials-scroll';
			}
		}

		/* Advanced Buttons */
		if($blockname=='tpgb/tp-advanced-buttons'){
			if( !empty($options) && !empty($options['btnType'])) {
				if($options['btnType']=='download'){
					if(!empty($options['dwnldStyle'])){
						$pro_blocks[] = 'tpx-adv-btn-dwnld-'.$options['dwnldStyle'];
					}else{
						$pro_blocks[] = 'tpx-adv-btn-dwnld-style-1';
					}
				}else{
					if(!empty($options['ctaStyle'])){
						$pro_blocks[] = 'tpx-adv-btn-cta-'.$options['ctaStyle'];
					}else{
						$pro_blocks[] = 'tpx-adv-btn-cta-style-1';
					}
				}
			}else{
				if(!empty($options['ctaStyle'])){	
					$pro_blocks[] = 'tpx-adv-btn-cta-'.$options['ctaStyle'];
				}else{
					$pro_blocks[] = 'tpx-adv-btn-cta-style-1';
				}
			}
		}

		/* Animated Service Boxes */
		if($blockname=='tpgb/tp-animated-service-boxes'){
			if(!empty($options) && !empty($options['mainStyleType'])){	
				$pro_blocks[] = 'tpx-animated-service-boxes-'.$options['mainStyleType'];
			}else{
				$pro_blocks[] = 'tpx-animated-service-boxes-image-accordion';
			}
			if(!empty($options) && !empty($options['disBtn'])) {
				$pro_blocks[] = 'tpgb-group-button';
			}
		}

		/* Mouse Cursor */
		if($blockname=='tpgb/tp-mouse-cursor' && !empty($options) && !empty($options['cursorType'])){
			$pro_blocks[] = 'tpx-'.$options['cursorType'];
		}

		/* Pricing Table */
		if($blockname=='tpgb/tp-pricing-table'){
			/* Layout */
			if( !empty($options) && !empty($options['style'])) {
				$pro_blocks[] = 'tpx-pricing-table-layout-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-pricing-table-layout-style-1';
			}

			/* Price */
			if( !empty($options) && !empty($options['priceStyle'])) {
				$pro_blocks[] = 'tpx-pricing-table-price-'.$options['priceStyle'];
			}else{
				$pro_blocks[] = 'tpx-pricing-table-price-style-1';
			}

			/* Content */
			if( !empty($options) && !empty($options['contentStyle'])) {
				$pro_blocks[] = 'tpx-pricing-table-content-'.$options['contentStyle'];
			}else{
				$pro_blocks[] = 'tpx-pricing-table-content-wysiwyg';
			}

			/* Ribbon */
			if( !empty($options) && !empty($options['disRibbon'])) {
				$pro_blocks[] = 'tpx-pricing-table-ribbon';
			}
		}

		/* Process Steps */
		if($blockname=='tpgb/tp-process-steps'){
			/* Style */
			if( !empty($options) && !empty($options['style'])) {
				$pro_blocks[] = 'tpx-process-steps-'.$options['style'];
			}else{
				$pro_blocks[] = 'tpx-process-steps-style-1';
			}

			/* Counter */
			if( !empty($options) && !empty($options['displayCounter'])) {
				$pro_blocks[] = 'tpx-process-steps-counter';
			}

			/* Ring BG */
			if( !empty($options) && !empty($options['specialBG'])) {
				$pro_blocks[] = 'tpx-process-steps-ring-bg';
			}
		}

		/* Tp Heading Animation  */
		if($blockname=='tpgb/tp-heading-animation'){
			if(!empty($options) && !empty($options['style']) && $options['style']=='textAnim'){
				if(!empty($options) && !empty($options['textAnimStyle'])){
					$pro_blocks[] = 'tpx-heading-textAnim-'.$options['textAnimStyle'];
				}else{
					$pro_blocks[] = 'tpx-heading-textAnim-style-1';
				}
			}else{
				$pro_blocks[] = 'tpx-heading-animation-highlights';
			}
		}

		/* Tp MailChimp */
		if($blockname=='tpgb/tp-mailchimp'){
			if(!empty($options) && !empty($options['styleType'])){
				$pro_blocks[] = 'tpx-mailchimp-'.$options['styleType'];
			}else{
				$pro_blocks[] = 'tpx-mailchimp-style-1';
			}
			if(!empty($options) && !empty($options['gdprCompli'])){
				$pro_blocks[] = 'tpx-mailchimp-gdpr';
			}
		}

		/* Login Register */
		if($blockname=='tpgb/tp-login-register'){
			if(!empty($options) && !empty($options['formLayout']) && $options['formLayout'] == 'button' ){
				$pro_blocks[] = 'tpx-form-button';
			}

			if(!empty($options) && !empty($options['formType']) && $options['formType'] == 'login-register' ){
				$pro_blocks[] = 'tpx-form-tab';
			}
		}
		
		/* Mobile Menu */
		if($blockname=='tpgb/tp-mobile-menu'){
			/* Style */
			if( !empty($options) && !empty($options['mmStyle'])) {
				$pro_blocks[] = 'tpx-mobile-menu-'.$options['mmStyle'];
			}else{
				$pro_blocks[] = 'tpx-mobile-menu-style-1';
			}

			/* Toggle */
			if( !empty($options) && !empty($options['extraToggle'])) {
				$pro_blocks[] = 'tpx-mobile-menu-toggle';
			}

			/* Indicator */
			if( !empty($options) && !empty($options['pageIndicator'])) {
				$pro_blocks[] = 'tpx-mobile-menu-indicator';
			}
		}

		/* Dynamic Device */
		if($blockname=='tpgb/tp-dynamic-device'){
			/* Type */
			if( !empty($options) && !empty($options['layoutType']) && $options['layoutType']=='carousel' ) {
				if(!empty($options['cDeviceType'])){
					$pro_blocks[] = 'tpx-dynamic-device-'.$options['cDeviceType'];
				}else{
					$pro_blocks[] = 'tpx-dynamic-device-mobile';
				}
			}else{
				if(!empty($options['deviceType'])){
					$pro_blocks[] = 'tpx-dynamic-device-'.$options['deviceType'];
				}else{
					$pro_blocks[] = 'tpx-dynamic-device-mobile';
				}
			}
		}

		/* Coupon Code */
		if($blockname=='tpgb/tp-coupon-code'){
			/* Type */
			if( !empty($options) && !empty($options['couponType']) ) {
				$pro_blocks[] = 'tpx-coupon-code-'.$options['couponType'];
			}else{
				if(!empty($options['standardStyle'])){
					$pro_blocks[] = 'tpx-coupon-code-standard-'.$options['standardStyle'];
				}else{
					$pro_blocks[] = 'tpx-coupon-code-standard-style-1';
				}
				if(!empty($options['actionType']) && $options['actionType']=='popup'){
					$pro_blocks[] = 'tpx-coupon-code-standard-popup';
					if(!empty($options['onScrollBar'])){
						$pro_blocks[] = 'tpx-coupon-code-standard-scrollbar';
					}
				}
			}
		}

		

		/*Plus Extras*/
		if(!empty($options) && !empty($options['Plus3DTilt']) && !empty($options['Plus3DTilt']['tpgbReset']) && $options['Plus3DTilt']['tpgbReset']===1){	//tilt
			$pro_blocks[] = 'tpgb-jstilt';
		}
		if(!empty($options) && !empty($options['PlusMouseParallax']) && !empty($options['PlusMouseParallax']['tpgbReset']) && $options['PlusMouseParallax']['tpgbReset']===1){	//mouse parallax
			$pro_blocks[] = 'tpgb-mouse-parallax';
		}
		$blocks = array_merge($blocks, $pro_blocks);

		return $blocks;
	}
	
	public function localize_data($data){
	
		$fontawesome_pro = Tp_Blocks_Helper::get_extra_option('fontawesome_pro_kit');
		$fontAwesomePro = false;
		if(!empty($fontawesome_pro)){
			$fontAwesomePro = $fontawesome_pro;
		}
		$splineJsSrc = '';
		if (defined('TPGBP_URL')) {
			$splineJsSrc = TPGBP_URL.'assets/js/main/spline-3d-viewer/spline-viewer.js';
		}
		
		$eventTracker = apply_filters( 'tpgb_event_tracking', [] );

		$pro_data = array(
			'splinejsurl' => (!empty($splineJsSrc)) ? esc_url($splineJsSrc) : '',
			'menu_lists' => $this->get_menu_lists(),
			'shapeDivider' => $this->getShapeDivider(),
			'fontawesome' => $fontAwesomePro,
			'dynamic_list' => $this->tpgb_get_dynamic_list(),
			'tpgb_page_list' => $this->tpgb_get_page_list(),
			'tpgb_user_role' => $this->tpgbp_get_user_role(),
			'tpgb_current_user' => $this->tpgbp_get_current_user(),
			'tpgb_developer' => TPGBP_DEVELOPER,
			'tpgb_event_tracking' => $eventTracker['switch'],
			'event_track_google' => $eventTracker['google_track'],
			'event_track_facebook' => $eventTracker['facebook_track'],
		);
		
		$pro_data = array_merge($data, $pro_data);
		return $pro_data;
	}
	
	
	/**
	 * Init Block Load.
	 *
	 * @since 1.0.0
	 */
	public function init_blocks_load($load_blocks) {
	
		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		$pro_load_blocks = array(
			'tp-advanced-chart' => TPGBP_CATEGORY.'/tp-advanced-chart',
			'tp-advanced-buttons' => TPGBP_CATEGORY.'/tp-advanced-buttons',
			'tp-adv-typo' => TPGBP_CATEGORY.'/tp-adv-typo',
			'tp-animated-service-boxes' => TPGBP_CATEGORY.'/tp-animated-service-boxes',
			'tp-anything-carousel' => TPGBP_CATEGORY.'/tp-anything-carousel',
			'tp-audio-player' => TPGBP_CATEGORY.'/tp-audio-player',
			'tp-before-after' => TPGBP_CATEGORY.'/tp-before-after',
			'tp-carousel-remote' => TPGBP_CATEGORY.'/tp-carousel-remote',
			'tp-circle-menu' => TPGBP_CATEGORY.'/tp-circle-menu',
			'tp-coupon-code' => TPGBP_CATEGORY.'/tp-coupon-code',
			'tp-container' => TPGBP_CATEGORY.'/tp-container',
			'tp-cta-banner' => TPGBP_CATEGORY.'/tp-cta-banner',
			'tp-design-tool' => TPGBP_CATEGORY.'/tp-design-tool',
			'tp-dynamic-device' => TPGBP_CATEGORY.'/tp-dynamic-device',
			'tp-expand' => TPGBP_CATEGORY.'/tp-expand',
			'tp-heading-animation' => TPGBP_CATEGORY.'/tp-heading-animation',
			'tp-hotspot' => TPGBP_CATEGORY.'/tp-hotspot',
			'tp-login-register' => TPGBP_CATEGORY.'/tp-login-register',
			'tp-lottiefiles' => TPGBP_CATEGORY.'/tp-lottiefiles',
			'tp-mailchimp' => TPGBP_CATEGORY.'/tp-mailchimp',
			'tp-media-listing' => TPGBP_CATEGORY.'/tp-media-listing',
			'tp-mobile-menu' => TPGBP_CATEGORY.'/tp-mobile-menu',
			'tp-mouse-cursor' => TPGBP_CATEGORY.'/tp-mouse-cursor',
			'tp-navigation-builder' => TPGBP_CATEGORY.'/tp-navigation-builder',
			'tp-popup-builder' => TPGBP_CATEGORY.'/tp-popup-builder',
			'tp-post-navigation' => TPGBP_CATEGORY.'/tp-post-navigation',
			'tp-preloader' => TPGBP_CATEGORY.'/tp-preloader',
			'tp-pricing-table' => TPGBP_CATEGORY.'/tp-pricing-table',
			'tp-process-steps' => TPGBP_CATEGORY.'/tp-process-steps',
			'tp-product-listing' => TPGBP_CATEGORY.'/tp-product-listing',
			'tp-scroll-navigation' => TPGBP_CATEGORY.'/tp-scroll-navigation',
			'tp-scroll-sequence' => TPGBP_CATEGORY.'/tp-scroll-sequence',
			'tp-social-feed' => TPGBP_CATEGORY.'/tp-social-feed',
			'tp-social-sharing' => TPGBP_CATEGORY.'/tp-social-sharing',
			'tp-social-reviews' => TPGBP_CATEGORY.'/tp-social-reviews',
			'tp-spline-3d-viewer' => TPGBP_CATEGORY.'/tp-spline-3d-viewer',
			'tp-switcher' => TPGBP_CATEGORY.'/tp-switcher',
			'tp-table-content' => TPGBP_CATEGORY.'/tp-table-content',
			'tp-team-listing' => TPGBP_CATEGORY.'/tp-team-listing',
			'tp-timeline' => TPGBP_CATEGORY.'/tp-timeline',
			
		);
		
		$pro_load_blocks = array_merge($pro_load_blocks, $load_blocks);
		
		return $pro_load_blocks;
		
	}
	
	/**
	 * Load Register Blocks Css and Js File
	 *
	 * @since 1.0.0
	 */
	public function load_blocks_registers_css_js($load_blocks_css_js){
		$tpgb_pro = TPGBP_PATH . DIRECTORY_SEPARATOR;
		$tpgb_free = TPGB_PATH . DIRECTORY_SEPARATOR;

		$pro_blocks_register = [
			/* 'tpgb_lazyLoad' => [
				'css' => [
					$tpgb_pro .'assets/css/main/lazy_load/tpgb-lazy_load.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/lazyload.min.js',
					$tpgb_pro . 'assets/js/main/lazy_load/tpgb-lazy_load.js',
				],
			], */
			TPGBP_CATEGORY.'/tp-row' => [
				'css' => [
					$tpgb_pro .'assets/css/main/plus-extras/plus-row-bg.css',
					$tpgb_pro .'classes/blocks/tp-row/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/tp-row/tpgb-row.min.js',
				],
			],
			'tpx-tp-shape-divider' => [
				'css' => [
					$tpgb_pro .'assets/css/main/shape-divider/style-shape-divider.css',
				],
			],
			TPGBP_CATEGORY.'/tp-container' => [
				'css' => [
					$tpgb_pro .'assets/css/main/plus-extras/plus-row-bg.css',
					$tpgb_pro .'classes/blocks/tp-container/style.css',
				],
			],
			'tpgb-row-animated-color' => [
				'js' => [
					$tpgb_pro .'assets/js/extra/effect.min.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-row-animate.min.js',
				],
			],
			'tpgb-image-parallax' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-image-parellax.min.js',
				],
			],
			'tpgb-image-moving' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-image-moving.min.js',
				],
			],
			'tpgb-scroll-parallax' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-scroll-parallax.min.js',
				],
			],
			'tpgb-youtube-video' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-video-common.min.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-youtube-video.min.js',
				],
			],
			'tpgb-vimeo-video' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-video-common.min.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-vimeo-video.min.js',
				],
			],
			'tpgb-bg-gallery' => [
				'css' => [
					$tpgb_pro .'assets/css/extra/vegas.min.css',
				],
				'js' => [					
					$tpgb_pro . 'assets/js/extra/vegas.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-imge-slide.min.js',

				],
			],
			'tpgb-magic-scroll' => [
				'js' => [
					$tpgb_pro . 'assets/js/extra/tweenmax/gsap.min.js',
					$tpgb_pro . 'assets/js/extra/scrollmagic/scrollmagic.min.js',
					$tpgb_pro . 'assets/js/extra/scrollmagic/animation.gsap.min.js',
					$tpgb_pro . 'assets/js/extra/scrollmagic/addIndicators.min.js',
				],
			],
            'tpgb-magic-scroll-custom' => [
				'js' => [					
					$tpgb_pro . 'assets/js/main/plus-extras/plus-magic-scroll.min.js',
				],
			],
			'tpgb-scrollbg-animation' => [
				'js' => [
					$tpgb_pro .'assets/js/extra/scrollingBackgroundColor.js',
					$tpgb_pro .'assets/js/extra/scrollmonitor.js',		
					$tpgb_pro . 'assets/js/main/row-background/tpgb-bgscroll-animation.min.js',
				],
			],
			'tpgb-canvas-particle' => [
				'js' => [
					$tpgb_pro . 'assets/js/extra/particles.min.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-canvas.min.js',
				],
			],
			'tpgb-canvas-particleground' => [
				'js' => [
					$tpgb_pro . 'assets/js/extra/jquery.particleground.js',
					$tpgb_pro . 'assets/js/main/row-background/tpgb-canvas-particle-ground.min.js',
				],
			],
			'tpgb-mordern-parallax' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/row-background/tpgb-mordern-parallax.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-column' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-column/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-container-inner' => [
				'css' => [		
					$tpgb_pro .'classes/blocks/tp-container-inner/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-accordion' => [
				'css' => [		
					$tpgb_pro .'classes/blocks/tp-accordion/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
					$tpgb_pro .'assets/js/main/accordion/tpgb-accordion.min.js',
				],
			],
			'tpx-accordion-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-accordion/style-1.css',
				],
			],
			'tpx-accordion-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-accordion/style-2.css',
				],
			],
			TPGBP_CATEGORY.'/tp-accordion-inner' => [],
			TPGBP_CATEGORY.'/tp-advanced-buttons' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/advanced-buttons/tpgb-advanced-buttons.min.js',
				],
			],
			'tpx-adv-btn-cta-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-1.css',
				]
			],
			'tpx-adv-btn-cta-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-2.css',
				]
			],
			'tpx-adv-btn-cta-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-3.css',
				]
			],
			'tpx-adv-btn-cta-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-4.css',
				]
			],
			'tpx-adv-btn-cta-style-5' => [
                'css' => [
                    $tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-5.css',
                ],
                'js' => [
                    $tpgb_pro . 'assets/js/main/advanced-buttons/tpgb-advanced-button-style-5.min.js',
                ],
            ],
			'tpx-adv-btn-cta-style-6' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-6.css',
				]
			],
			'tpx-adv-btn-cta-style-7' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-7.css',
				]
			],
			'tpx-adv-btn-cta-style-8' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-8.css',
				]
			],
			'tpx-adv-btn-cta-style-9' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-9.css',
				]
			],
			'tpx-adv-btn-cta-style-10' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-10.css',
				]
			],
			'tpx-adv-btn-cta-style-11' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-11.css',
				]
			],
			'tpx-adv-btn-cta-style-12' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-12.css',
				]
			],
			'tpx-adv-btn-cta-style-13' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/cta/style-13.css',
				]
			],
			'tpx-adv-btn-dwnld-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/dwnld/style-1.css',
				]
			],
			'tpx-adv-btn-dwnld-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/dwnld/style-2.css',
				]
			],
			'tpx-adv-btn-dwnld-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/dwnld/style-3.css',
				]
			],
			'tpx-adv-btn-dwnld-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/dwnld/style-4.css',
				]
			],
			'tpx-adv-btn-dwnld-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-buttons/dwnld/style-5.css',
				]
			],
			TPGBP_CATEGORY.'/tp-advanced-chart' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-advanced-chart/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/jquery.waypoints.min.js',
					$tpgb_pro . 'assets/js/extra/chart.js',
					$tpgb_pro . 'assets/js/main/advanced-chart/tpgb-adv-chart.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-adv-typo' => [
				'css' => [
					$tpgb_pro .'assets/css/extra/imagerevealbase.css',
					$tpgb_pro .'classes/blocks/tp-adv-typo/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/charming.min.js',
					$tpgb_pro . 'assets/js/extra/imagesloaded.pkgd.min.js',
					$tpgb_pro . 'assets/js/extra/tweenmax/gsap.min.js',
					$tpgb_pro . 'assets/js/extra/imagerevealdemo.js',
					$tpgb_pro . 'assets/js/extra/circletype.min.js',
					$tpgb_pro . 'assets/js/main/adv-typo/adv-typo.min.js',
				],
			],
			'tpx-adv-typo-normal-overlay' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-adv-typo/style-normal-overlay.css',
				],
			],
			'tpx-adv-typo-multiple' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-adv-typo/style-multiple.css',
				],
			],
			TPGBP_CATEGORY.'/tp-animated-service-boxes' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
					$tpgb_pro . 'assets/js/main/animated-service-boxes/tpgb-animated-service-boxes.min.js',
				],
			],
			'tpx-animated-service-boxes-image-accordion' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/image-accordion.css',
				],
			],
			'tpx-animated-service-boxes-sliding-boxes' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/sliding-boxes.css',
				],
			],
			'tpx-animated-service-boxes-article-box' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/article-box.css',
				],
			],
			'tpx-animated-service-boxes-info-banner' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/info-banner.css',
				],
			],
			'tpx-animated-service-boxes-hover-section' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/hover-section.css',
				],
			],
			'tpx-animated-service-boxes-fancy-box' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/fancy-box.css',
				],
			],
			'tpx-animated-service-boxes-services-element' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/services-element.css',
				],
			],
			'tpx-animated-service-boxes-portfolio' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-animated-service-boxes/style/portfolio.css',
				],
			],
			TPGBP_CATEGORY.'/tp-audio-player' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/main/audio-player/tp-audio-player.min.js',
				],
			],
			'tpx-audio-player-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-2.css',
				],
			],
			'tpx-audio-player-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-3.css',
				],
			],
			'tpx-audio-player-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-4.css',
				],
			],
			'tpx-audio-player-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-5.css',
				],
			],
			'tpx-audio-player-style-6' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-6.css',
				],
			],
			'tpx-audio-player-style-7' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-7.css',
				],
			],
			'tpx-audio-player-style-8' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-8.css',
				],
			],
			'tpx-audio-player-style-9' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-audio-player/style-9.css',
				],
			],
			TPGBP_CATEGORY.'/tp-before-after' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-before-after/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/before-after/tpgb-before-after.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-countdown' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-countdown/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/countdown/countdown.min.js',
				],
			],
			'countdown-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-countdown/style-1.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/jquery.downCount.js',
				],
			],
 			'countdown-style-2' => [
				'css' => [
					$tpgb_pro . 'assets/css/extra/flipdown.min.css',
					$tpgb_pro .'classes/blocks/tp-countdown/style-2.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/flipdown.min.js',
				],
			],
			'countdown-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-countdown/style-3.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/progressbar.min.js',
				],
			],
			'countdown-fakestyle' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-countdown/fake-number.css',
				],
			],
			TPGBP_CATEGORY.'/tp-coupon-code' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/html2canvas.min.js',
					$tpgb_pro . 'assets/js/extra/peeljs.js',
					$tpgb_pro . 'assets/js/main/coupon-code/tpgb-coupon-code.min.js',
				],
			],
			'tpx-coupon-code-standard-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style-1.css',
				],
			],
			'tpx-coupon-code-standard-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style-2.css',
				],
			],
			'tpx-coupon-code-standard-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style-3.css',
				],
			],
			'tpx-coupon-code-standard-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style-4.css',
				],
			],
			'tpx-coupon-code-standard-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/style-5.css',
				],
			],
			'tpx-coupon-code-standard-popup' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/popup.css',
				],
			],
			'tpx-coupon-code-standard-scrollbar' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/standard/scrollbar.css',
				],
			],
			'tpx-coupon-code-scratch' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/scratch.css',
				],
			],
			'tpx-coupon-code-peel' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/peel.css',
				],
			],
			'tpx-coupon-code-slideout' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/style.css',
					$tpgb_pro .'classes/blocks/tp-coupon-code/peel-scratch-slide/slideout.css',
				],
			],
			TPGBP_CATEGORY.'/tp-circle-menu' => [
				'css' => [
					$tpgb_free .'assets/css/extra/tippy.css',
					$tpgb_pro .'classes/blocks/tp-circle-menu/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/popper.min.js',
					$tpgb_free .'assets/js/extra/tippy.min.js',
					$tpgb_pro .'assets/js/extra/jquery.circlemenu.js',
					$tpgb_pro .'assets/js/main/circle-menu/tpgb-circle-menu.min.js',
				],
			],
			'tpx-circle-menu-straight' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-circle-menu/straight.css',
				],
			],
			'tpx-circle-menu-overlay' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-circle-menu/overlay.css',
				],
			],
			'tpx-circle-menu-toggle-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-circle-menu/toggle/style-2.css',
				],
			],
			'tpx-circle-menu-toggle-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-circle-menu/toggle/style-3.css',
				],
			],
			TPGBP_CATEGORY.'/tp-data-table' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-data-table/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/jquery.datatables.min.js',
					$tpgb_pro .'assets/js/main/data-table/tpgb-data-table.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-design-tool' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-design-tool/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-dynamic-device' => [
				'css' => [
					$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
					$tpgb_pro .'classes/blocks/tp-dynamic-device/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
					$tpgb_pro . 'assets/js/main/dynamic-devices/tpgb-dynamic-devices.min.js',
				],
			],
			'tpx-dynamic-device-mobile' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-dynamic-device/type/mobile.css',
				],
			],
			'tpx-dynamic-device-tablet' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-dynamic-device/type/tablet.css',
				],
			],
			'tpx-dynamic-device-laptop' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-dynamic-device/type/laptop.css',
				],
			],
			'tpx-dynamic-device-desktop' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-dynamic-device/type/desktop.css',
				],
			],
			'tpx-dynamic-device-custom' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-dynamic-device/type/custom.css',
				],
			],
			TPGBP_CATEGORY.'/tp-expand' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-expand/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
					$tpgb_pro . 'assets/js/main/expand/expand.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-media-listing' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
					$tpgb_pro .'classes/blocks/tp-media-listing/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/jquery.hoverdir.js',
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/extra/imagesloaded.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
					$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
					$tpgb_pro . 'assets/js/main/media-listing/tpgb-media-listing.min.js',
				],
			],
			'tpx-media-listing-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-media-listing/media-style-1.css',
				],
			],
			'tpx-media-listing-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-media-listing/media-style-2.css',
				],
			],
			'tpx-media-listing-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-media-listing/media-style-3.css',
				],
			],
			'tpx-media-listing-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-media-listing/media-style-4.css',
				],
			],
			'tpx-media-metro-style'  => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-media-listing/metro-style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-post-listing' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'classes/blocks/tp-post-listing/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/extra/imagesloaded.pkgd.min.js',
					$tpgb_free . 'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-listing.min.js',
				],
			],
			'tpx-post-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-listing/tp-post-style-1.css',
				],
			],
			'tpx-post-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-listing/tp-post-style-2.css',
				],
			],
			'tpx-post-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-listing/tp-post-style-3.css',
				],
			],
			'tpx-post-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-listing/tp-post-style-4.css',
				],
			],
			'tpx-post-metro' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-listing/tp-post-metro.css',
				],
			],
			TPGBP_CATEGORY.'/tp-switcher' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-switcher/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/switcher/tpgb-switcher.min.js',
				],
			],
			'tpx-switcher-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-switcher/style-1.css',
				],
			],
			'tpx-switcher-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-switcher/style-2.css',
				],
			],
			'tpx-switcher-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-switcher/style-3.css',
				],
			],
			'tpx-switcher-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-switcher/style-4.css',
				],
			],
			TPGBP_CATEGORY.'/tp-switch-inner' => [],
			TPGBP_CATEGORY.'/tp-testimonials' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_free .'assets/css/extra/splide.min.css',
					$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
					$tpgb_pro .'classes/blocks/tp-testimonials/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/splide.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-splide.min.js',
					$tpgb_free . 'assets/js/main/testimonial/tpgb-testimonial.min.js',
				],
			],
			'tpx-testimonials-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-testimonials/tp-testimonials-style-1.css',
				],
			],
			'tpx-testimonials-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-testimonials/tp-testimonials-style-2.css',
				],
			],
			'tpx-testimonials-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-testimonials/tp-testimonials-style-3.css',
				],
			],
			'tpx-testimonials-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-testimonials/tp-testimonials-style-4.css',
				],
			],
			'tpx-testimonials-scroll' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-testimonials/tp-testimonials-scroll.css',
				],
			],
			TPGBP_CATEGORY.'/tp-team-listing' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'assets/css/main/post-listing/post-category-filter.css',
					$tpgb_pro .'classes/blocks/tp-team-listing/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
				],
			],
			'tpx-team-list-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-team-listing/tp-team-list-style-1.css',
				],
			],
			'tpx-team-list-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-team-listing/tp-team-list-style-2.css',
				],
			],
			'tpx-team-list-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-team-listing/tp-team-list-style-3.css',
				],
			],
			'tpx-team-list-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-team-listing/tp-team-list-style-4.css',
				],
			],
			TPGBP_CATEGORY.'/tp-stylist-list' => [
				'css' => [
					$tpgb_free .'assets/css/extra/tippy.css',
					$tpgb_pro .'classes/blocks/tp-stylist-list/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/popper.min.js',
					$tpgb_free .'assets/js/extra/tippy.min.js',
					$tpgb_pro .'assets/js/main/stylist-list/tp-stylist-list.min.js',
				],
			],
			'tpx-stylist-list-hover-bg' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-stylist-list/style-hover-bg.css',
				],
			],
			'tpx-stylist-list-hover-inverse' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-stylist-list/style-hover-inverse.css',
				],
			],
			TPGBP_CATEGORY.'/tp-table-content' => [
				'css' => [
					$tpgb_pro .'assets/css/extra/tocbot.css',
					$tpgb_pro .'classes/blocks/tp-table-content/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/extra/tocbot.min.js',
					$tpgb_free . 'assets/js/main/common-created/tpgb-slidetoggle-block.min.js',
					$tpgb_pro .'assets/js/main/table-content/tp-table-content.min.js',
				],
			],
			'tpx-table-content-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-table-content/style-1.css',
				],
			],
			'tpx-table-content-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-table-content/style-2.css',
				],
			],
			'tpx-table-content-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-table-content/style-3.css',
				],
			],
			'tpx-table-content-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-table-content/style-4.css',
				],
			],
			TPGBP_CATEGORY.'/tp-hotspot' => [
				'css' => [
					$tpgb_free .'assets/css/extra/tippy.css',
					$tpgb_pro .'classes/blocks/tp-hotspot/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/jquery.waypoints.min.js',
					$tpgb_free .'assets/js/extra/popper.min.js',
					$tpgb_free .'assets/js/extra/tippy.min.js',
					$tpgb_pro .'assets/js/main/hotspot/tpgb-hotspot.min.js',
				],
			],
			'tpx-hover-overlay' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-hotspot/hover-overlay.css',
				]
			],
			TPGBP_CATEGORY.'/tp-process-steps' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-process-steps/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/process-steps/tpgb-process-steps.min.js',
				],
			],
			'tpx-process-steps-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-process-steps/style-1.css',
				],
			],
			'tpx-process-steps-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-process-steps/style-2.css',
				],
			],
			'tpx-process-steps-counter' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-process-steps/counter.css',
				],
			],
			'tpx-process-steps-ring-bg' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-process-steps/ring-bg.css',
				],
			],
			TPGBP_CATEGORY.'/tp-product-listing' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'classes/blocks/tp-product-listing/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/extra/imagesloaded.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-listing.min.js',
				],
			],
			'tpx-product-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-product-listing/tp-product-style-1.css',
				],
			],
			'tpx-product-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-product-listing/tp-product-style-2.css',
				],
			],
			'tpx-product-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-product-listing/tp-product-style-3.css',
				],
			],
			'tpx-product-metro' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-product-listing/tp-product-metro.css',
				],
			],
			TPGBP_CATEGORY.'/tp-flipbox' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-flipbox/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-google-map' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-google-map/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/google-map/tpgb-google-map.min.js',
				],
			],
			'tpx-google-map-content' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-google-map/style-content-hover.css',
				],
			],
			TPGBP_CATEGORY.'/tp-anything-carousel' => [
				'css' => [
					$tpgb_free .'assets/css/extra/splide.min.css',
					$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
					$tpgb_pro .'classes/blocks/tp-anything-carousel/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/splide.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-splide.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-carousel-remote' => [
				'css' => [
					$tpgb_free .'assets/css/extra/splide.min.css',
					$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
					$tpgb_pro .'classes/blocks/tp-carousel-remote/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/splide.min.js',
					$tpgb_pro . 'assets/js/main/carousel-remote/tpgb-carousel-remote.min.js',
				],
			],
			'tpgx-carousel-button' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-carousel-remote/tp-sliderbutton.css',
				],
			],
			'tpgx-carousel-dot' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-carousel-remote/tp-caro-dot.css',
				],
			],
			'tpgx-carousel-tooltip' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-carousel-remote/tp-caro-tooltip.css',
				],
			],
			'tpgx-carousel-pagination' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-carousel-remote/tp-pagination.css',
				],
			],
			TPGBP_CATEGORY.'/tp-cta-banner' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style.css',
				],
			],
			'tpx-cta-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-1.css',
				],
			],
			'tpx-cta-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-2.css',
				],
			],
			'tpx-cta-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-3.css',
				],
			],
			'tpx-cta-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-4.css',
				],
			],
			'tpx-cta-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-5.css',
				],
			],
			'tpx-cta-style-6' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-6.css',
				],
			],
			'tpx-cta-style-7' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-7.css',
				],
			],
			'tpx-cta-style-8' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-cta-banner/style-8.css',
				],
			],
			TPGBP_CATEGORY.'/tp-tabs-tours'  => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-tabs-tours/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/main/tabs-tours/plus-tabs-tours.min.js',
				],
			],
			'tpx-tabs-tours-vertical' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-tabs-tours/style-vertical.css',
				],
			],
			TPGBP_CATEGORY.'/tp-tab-item' => [],
			TPGBP_CATEGORY.'/tp-heading-animation' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/heading-animation/heading-animation.min.js',
				],
			],
			'tpx-heading-animation-highlights' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-highlights.css',
				],
			],
			'tpx-heading-textAnim-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-1.css',
				],
			],
			'tpx-heading-textAnim-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-2.css',
				],
			],
			'tpx-heading-textAnim-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-3.css',
				],
			],
			'tpx-heading-textAnim-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-4.css',
				],
			],
			'tpx-heading-textAnim-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-5.css',
				],
			],
			'tpx-heading-textAnim-style-6' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-6.css',
				],
			],
			'tpx-heading-textAnim-style-7' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-7.css',
				],
			],
			'tpx-heading-textAnim-style-8' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-heading-animation/style-textAnim-style-8.css',
				],
			],
			TPGBP_CATEGORY.'/tp-mobile-menu' => [
				'css' => [
					$tpgb_pro .'assets/css/extra/swiper.min.css',
					$tpgb_pro .'classes/blocks/tp-mobile-menu/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/extra/swiper.min.js',
					$tpgb_pro . 'assets/js/main/mobile-menu/tpgb-mobile-menu.min.js',
				],
			],
			'tpx-mobile-menu-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mobile-menu/style-1.css',
				],
			],
			'tpx-mobile-menu-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mobile-menu/style-2.css',
				],
			],
			'tpx-mobile-menu-toggle' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mobile-menu/toggle.css',
				],
			],
			'tpx-mobile-menu-indicator' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mobile-menu/indicator.css',
				],
			],
			TPGBP_CATEGORY.'/tp-navigation-builder' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-navigation-builder/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/main/navigation-builder/tpgb-nav.min.js',
				],
			],
			'tpgb-web-access' => [
				'js' => [
					$tpgb_pro .'assets/js/main/navigation-builder/tpgb-nav-access.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-creative-image' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-creative-image/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/jquery.waypoints.min.js',
					$tpgb_pro .'assets/js/main/creative-image/plus-image-factory.min.js',
				],
			],
			'tpx-tp-image-scroll-effect' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-creative-image/style-scroll-effect.css',
				],
			],
			'tpx-tp-image-mask-img' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-creative-image/style-mask-image.css',
				],
			],
			'tpx-tp-image-animate' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-creative-image/style-animate-image.css',
				],
			],
			'tpx-tp-image-parallax' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-creative-image/style-parallax-image.css',
				],
			],
			TPGBP_CATEGORY.'/tp-social-feed' => [
				'css' => [
					$tpgb_free .'assets/css/extra/jquery.fancybox.min.css',
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'assets/css/main/post-listing/post-category-filter.css',
					$tpgb_pro .'classes/blocks/tp-social-feed/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/jquery.fancybox.min.js',
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
					$tpgb_pro . 'assets/js/main/social-feed/tp-social-feed.min.js',
				],
			],
			'tpx-social-feed-style-3' => [
				'css' => [
					$tpgb_free .'classes/blocks/tp-social-feed/style-3.css',
				],
			],
			'tpx-social-feed-style-4' => [
				'css' => [
					$tpgb_free .'classes/blocks/tp-social-feed/style-4.css',
				],
			],
			TPGBP_CATEGORY.'/tp-social-sharing' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-sharing/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/main/common-created/tpgb-slidetoggle-flex.min.js',
					$tpgb_pro . 'assets/js/main/social-sharing/tpgb-social-sharing.min.js',
				],
			],
			'tpx-social-sharing-horizontal' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-sharing/horizontal.css',
				],
			],
			'tpx-social-sharing-vertical' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-sharing/vertical.css',
				],
			],
			'tpx-social-sharing-toggle' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-sharing/toggle.css',
				],
			],
			TPGBP_CATEGORY.'/tp-social-reviews' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'assets/css/main/post-listing/post-category-filter.css',
					$tpgb_pro .'classes/blocks/tp-social-reviews/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
					$tpgb_pro . 'assets/js/main/social-reviews/tp-social-reviews.min.js',
				],
			],
			'tpx-review-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/review/style-1.css',
				],
			],
			'tpx-review-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/review/style-2.css',
				],
			],
			'tpx-review-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/review/style-3.css',
				],
			],
			'tpx-beach-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/beach.css',
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/style-1.css',
				],
			],
			'tpx-beach-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/beach.css',
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/style-2.css',
				],
			],
			'tpx-beach-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/beach.css',
					$tpgb_pro .'classes/blocks/tp-social-reviews/beach/style-3.css',
				],
			],
			TPGBP_CATEGORY.'/tp-spline-3d-viewer' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-spline-3d-viewer/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-timeline' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-timeline/style.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',				
				],
			],
			TPGBP_CATEGORY.'/tp-timeline-inner' => [],
			TPGBP_CATEGORY.'/tp-popup-builder' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/popup-builder/plus-popup-builder.min.js',
				],
			],
			'tpgb-popup-animation' => [
				'css' => [
					$tpgb_free .'assets/css/extra/animate.min.css',
				],
			],
			'tpx-humberger-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/hubtn-style-1.css',
				],
			],
			'tpx-humberger-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/hubtn-style-2.css',
				],
			],
			'tpx-humberger-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/hubtn-style-3.css',
				],
			],
			'tpx-corner-box' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/corner-box.css',
				],
			],
			'tpx-popup-effect' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/popup-effect.css',
				],
			],
			'tpx-push-content' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/push-content.css',
				],
			],
			'tpx-slide-along' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/slide-along.css',
				],
			],
			'tpx-slide' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/slide.css',
				],
			],
			'tpx-fixed-popup-toggle' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-popup-builder/fixed-toggle.css',
				],
			],
			TPGBP_CATEGORY.'/tp-post-navigation' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-navigation/style.css',
				],
			],
			'tpx-post-navigation-style-1' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'classes/blocks/tp-post-navigation/style-1.css',
				],
			],
			'tpx-post-navigation-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-navigation/style-2.css',
				],
			],
			'tpx-post-navigation-style-3' => [
				'css' => [
					$tpgb_free .'assets/css/extra/bootstrap-grid.min.css',
					$tpgb_pro .'classes/blocks/tp-post-navigation/style-3.css',
				],
			],
			'tpx-post-navigation-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-post-navigation/style-4.css',
				],
			],
			TPGBP_CATEGORY.'/tp-preloader' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-preloader/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/extra/lottie-player.js',
					$tpgb_pro . 'assets/js/main/preloader/tpgb-pre-loader-extra-transition.min.js',
					$tpgb_pro . 'assets/js/main/preloader/tpgb-preloader.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-pricing-table' => [
				'css' => [
					$tpgb_free .'assets/css/extra/tippy.css',					
					$tpgb_pro .'classes/blocks/tp-pricing-table/style.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/popper.min.js',
					$tpgb_free .'assets/js/extra/tippy.min.js',
					$tpgb_pro . 'assets/js/main/pricing-table/tp-pricing-table.min.js',
				],
			],
			'tpx-pricing-table-layout-style-1' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/layout/style-1.css',
				],
			],
			'tpx-pricing-table-layout-style-2' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/layout/style-2.css',
				],
			],
			'tpx-pricing-table-layout-style-3' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/layout/style-3.css',
				],
			],
			'tpx-pricing-table-price-style-1' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/price/style-1.css',
				],
			],
			'tpx-pricing-table-price-style-2' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/price/style-2.css',
				],
			],
			'tpx-pricing-table-price-style-3' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/price/style-3.css',
				],
			],
			'tpx-pricing-table-content-wysiwyg' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/content/wysiwyg.css',
				],
			],
			'tpx-pricing-table-content-stylish' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/content/stylish.css',
				],
			],
			'tpx-pricing-table-ribbon' => [
				'css' => [				
					$tpgb_pro .'classes/blocks/tp-pricing-table/ribbon/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-lottiefiles' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-lottiefiles/style.css',
				],
			],
			TPGBP_CATEGORY.'/tp-mailchimp' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mailchimp/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/main/mailchimp/plus-mailchimp.min.js',
				],
			],
			'tpx-mailchimp-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mailchimp/style-1.css',
				],
			],
			'tpx-mailchimp-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mailchimp/style-2.css',
				],
			],
			'tpx-mailchimp-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mailchimp/style-3.css',
				],
			],
			'tpx-mailchimp-gdpr' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mailchimp/style-gdpr.css',
				],
			],
			TPGBP_CATEGORY.'/tp-mouse-cursor' => [
				'js' => [
					$tpgb_pro . 'assets/js/main/mouse-cursor/tpgb-mouse-cursor.min.js',
				],
			],
			'tpx-mouse-follow-image' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mouse-cursor/type/image.css',
				],
			],
			'tpx-mouse-follow-text' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mouse-cursor/type/text.css',
				],
			],
			'tpx-mouse-follow-circle' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-mouse-cursor/type/circle.css',
				],
			],
			TPGBP_CATEGORY.'/tp-scroll-navigation' => [

				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/extra/pagescroll2id.js',
					$tpgb_pro .'assets/js/main/scroll-nav/tpgb-scrollnav.min.js',
				],
			],
			'tpx-scroll-navigation-style-1' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-navigation-style-1.css',
				],
			],
			'tpx-scroll-navigation-style-2' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-navigation-style-2.css',
				],
			],
			'tpx-scroll-navigation-style-3' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-navigation-style-3.css',
				],
			],
			'tpx-scroll-navigation-style-4' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-navigation-style-4.css',
				],
			],
			'tpx-scroll-navigation-style-5' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-navigation-style-5.css',
				],
			],
			'tpx-display-counter' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-display-counter.css',
				],
			],
			'tpx-scroll-offset' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-navigation/tp-scroll-offset.css',
				],
			],
			TPGBP_CATEGORY.'/tp-scroll-sequence' => [

				'css' => [
					$tpgb_pro .'classes/blocks/tp-scroll-sequence/style.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/scroll-sequence/tpgb-scroll-sequence.min.js',
				],
			],
			'tpgb-category-filter' => [

				'css' => [
					$tpgb_pro .'assets/css/main/post-listing/post-category-filter.css',
				],
			],
			'carouselSlider' => [

				'css' => [
					$tpgb_free .'assets/css/extra/splide.min.css',
					$tpgb_free .'assets/css/main/post-listing/splide-carousel.min.css',
				],
				'js' => [
					$tpgb_free . 'assets/js/extra/splide.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-splide.min.js',
				],
			],
			'tpgb-post-load-ajax' => [

				'css' => [
					$tpgb_pro .'assets/css/main/post-listing/tpgb-post-load.css',
				],
				'js' => [
					$tpgb_pro . 'assets/js/main/post-listing/post-load.min.js',
				],
			],
			'review-feed-load' => [

				'css' => [
					$tpgb_pro .'assets/css/main/social-review-feed/tpgb-review-feed-load.css',
				],
			],
			'hoverTilt' => [

				'js' => [
					$tpgb_pro .'assets/js/extra/jquery.hover3d.min.js',
					$tpgb_pro .'assets/js/main/cta-banner/plus-hover-tilt.min.js',
				],
			],
			'swiperJs' => [
				'css' => [
					$tpgb_pro .'assets/css/extra/swiper.min.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/extra/swiper.min.js',
				],
			],
			'content-hover-effect' => [
				'css' => [
					$tpgb_free .'assets/css/main/plus-extras/plus-content-hover-effect.css',
				],
			],
			'continue-animation' => [
				'css' => [
					$tpgb_pro .'assets/css/main/plus-extras/plus-continue-animation.css',
				],
			],
			'equal-height' => [
				'js' => [
					$tpgb_pro .'assets/js/main/plus-extras/plus-equal-height.min.js',
				],
			],
			'global-tooltip' => [
				'css' => [
					$tpgb_free .'assets/css/extra/tippy.css',
				],
				'js' => [
					$tpgb_free .'assets/js/extra/popper.min.js',
					$tpgb_free .'assets/js/extra/tippy.min.js',
					$tpgb_pro .'assets/js/main/plus-extras/plus-global-tooltip.min.js',
				],
			],
			'tpgb-jstilt' => [
				'js' => [
					$tpgb_pro .'assets/js/extra/vanilla-tilt.min.js',
					$tpgb_pro .'assets/js/main/plus-extras/plus-tilt.min.js',
				],
			],
			'tpgb-mouse-parallax' => [
				'js' => [
					$tpgb_pro .'assets/js/extra/tweenmax/gsap.min.js',
					$tpgb_pro .'assets/js/main/plus-extras/plus-mouse-parallax.min.js',
				],
			],
			'tpgb-sticky-col' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-column/style-sticky-col.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/extra/sticky-sidebar.min.js',
					$tpgb_pro .'assets/js/main/column/tpgb-column-sticky.min.js',
				],
			],
			TPGBP_CATEGORY.'/tp-login-register' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-login-register/style.css',
				],
				'js' => [
					$tpgb_pro .'assets/js/main/login-register/tpgb-login.min.js',
				],
			],
			'tpx-form-tab' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-login-register/tp-form-tab.css',
				],
			],
			'tpx-form-button' => [
				'css' => [
					$tpgb_pro .'classes/blocks/tp-login-register/tp-form-button.css',
				],
			],
			'tpgb_grid_layout' => [
				'js' => [
					$tpgb_free . 'assets/js/extra/isotope.pkgd.min.js',
					$tpgb_pro . 'assets/js/main/post-listing/post-masonry.min.js',
				],
			],
		];

		$load_blocks_css_js = array_merge($load_blocks_css_js,$pro_blocks_register);
		
		return $load_blocks_css_js;
	}
	
	/**
	 * Load Register Blocks Css and Js File
	 *
	 * @since 1.0.0
	 */
	public function load_blocks_registers_render($replace){
	
		$pro_blocks_replace = [
			TPGBP_CATEGORY.'/tp-advanced-buttons' => TPGBP_CATEGORY.'/tp-advanced-buttons',
			TPGBP_CATEGORY.'/tp-advanced-chart' => TPGBP_CATEGORY.'/tp-advanced-chart',
			TPGBP_CATEGORY.'/tp-adv-typo' => TPGBP_CATEGORY.'/tp-adv-typo',
			TPGBP_CATEGORY.'/tp-animated-service-boxes' => TPGBP_CATEGORY.'/tp-animated-service-boxes',
			TPGBP_CATEGORY.'/tp-anything-carousel' => TPGBP_CATEGORY.'/tp-anything-carousel',
			TPGBP_CATEGORY.'/tp-audio-player' => TPGBP_CATEGORY.'/tp-audio-player',
			TPGBP_CATEGORY.'/tp-before-after' => TPGBP_CATEGORY.'/tp-before-after',
			TPGBP_CATEGORY.'/tp-carousel-remote' => TPGBP_CATEGORY.'/tp-carousel-remote',
			TPGBP_CATEGORY.'/tp-circle-menu' => TPGBP_CATEGORY.'/tp-circle-menu',
			TPGBP_CATEGORY.'/tp-coupon-code' => TPGBP_CATEGORY.'/tp-coupon-code',
			TPGBP_CATEGORY.'/tp-cta-banner' => TPGBP_CATEGORY.'/tp-cta-banner',
			TPGBP_CATEGORY.'/tp-container' => TPGBP_CATEGORY.'/tp-container',
			TPGBP_CATEGORY.'/tp-container-inner' => TPGBP_CATEGORY.'/tp-container-inner',
			TPGBP_CATEGORY.'/tp-accordion-inner' => TPGBP_CATEGORY.'/tp-accordion-inner',
			TPGBP_CATEGORY.'/tp-design-tool' => TPGBP_CATEGORY.'/tp-design-tool',
			TPGBP_CATEGORY.'/tp-dynamic-device' => TPGBP_CATEGORY.'/tp-dynamic-device',
			TPGBP_CATEGORY.'/tp-expand' => TPGBP_CATEGORY.'/tp-expand',
			TPGBP_CATEGORY.'/tp-heading-animation' => TPGBP_CATEGORY.'/tp-heading-animation',
			TPGBP_CATEGORY.'/tp-hotspot' => TPGBP_CATEGORY.'/tp-hotspot',
			TPGBP_CATEGORY.'/tp-lottiefiles' => TPGBP_CATEGORY.'/tp-lottiefiles',
			TPGBP_CATEGORY.'/tp-mailchimp' => TPGBP_CATEGORY.'/tp-mailchimp',
			TPGBP_CATEGORY.'/tp-media-listing' => TPGBP_CATEGORY.'/tp-media-listing',
			TPGBP_CATEGORY.'/tp-mobile-menu' => TPGBP_CATEGORY.'/tp-mobile-menu',
			TPGBP_CATEGORY.'/tp-mouse-cursor' => TPGBP_CATEGORY.'/tp-mouse-cursor',
			TPGBP_CATEGORY.'/tp-navigation-builder' => TPGBP_CATEGORY.'/tp-navigation-builder',
			TPGBP_CATEGORY.'/tp-popup-builder' => TPGBP_CATEGORY.'/tp-popup-builder',
			TPGBP_CATEGORY.'/tp-post-navigation' => TPGBP_CATEGORY.'/tp-post-navigation',
			TPGBP_CATEGORY.'/tp-preloader' => TPGBP_CATEGORY.'/tp-preloader',
			TPGBP_CATEGORY.'/tp-pricing-table' => TPGBP_CATEGORY.'/tp-pricing-table',
			TPGBP_CATEGORY.'/tp-process-steps' => TPGBP_CATEGORY.'/tp-process-steps',
			TPGBP_CATEGORY.'/tp-product-listing' => TPGBP_CATEGORY.'/tp-product-listing',
			TPGBP_CATEGORY.'/tp-scroll-navigation' => TPGBP_CATEGORY.'/tp-scroll-navigation',
			TPGBP_CATEGORY.'/tp-scroll-sequence' => TPGBP_CATEGORY.'/tp-scroll-sequence',
			TPGBP_CATEGORY.'/tp-social-feed' => TPGBP_CATEGORY.'/tp-social-feed',
			TPGBP_CATEGORY.'/tp-social-sharing' => TPGBP_CATEGORY.'/tp-social-sharing',
			TPGBP_CATEGORY.'/tp-social-reviews' => TPGBP_CATEGORY.'/tp-social-reviews',
			TPGBP_CATEGORY.'/tp-spline-3d-viewer' => TPGBP_CATEGORY.'/tp-spline-3d-viewer',
			TPGBP_CATEGORY.'/tp-switcher' => TPGBP_CATEGORY.'/tp-switcher',
			TPGBP_CATEGORY.'/tp-switch-inner' => TPGBP_CATEGORY.'/tp-switch-inner',
			TPGBP_CATEGORY.'/tp-table-content' => TPGBP_CATEGORY.'/tp-table-content',
			TPGBP_CATEGORY.'/tp-team-listing' => TPGBP_CATEGORY.'/tp-team-listing',
			TPGBP_CATEGORY.'/tp-timeline' => TPGBP_CATEGORY.'/tp-timeline',
			TPGBP_CATEGORY.'/tp-timeline-inner' => TPGBP_CATEGORY.'/tp-timeline-inner',
			TPGBP_CATEGORY.'/tp-tab-item' => TPGBP_CATEGORY.'/tp-tab-item',
			TPGBP_CATEGORY.'/tp-login-register' => TPGBP_CATEGORY.'/tp-login-register',
		];
		
		$replace = array_merge($pro_blocks_replace, $replace);

		return $replace;
	}
	
	public function include_block($block){
	
		$filename = sprintf('classes/blocks/'.$block.'/index.php');

		if ( ! file_exists( TPGBP_PATH.$filename ) ) {
			return false;
		}

		require TPGBP_PATH.$filename;

		return true;
	}
	
	/*
	 * Get Load activate Block for tpgb
	 * @Array
	 * @since 1.0.0 
	 */
	public static function get_block_enabled() {
	
		$load_enable_block = self::$load_blocks;
		
		if( !empty( $load_enable_block ) ){
			return $load_enable_block;
		}else{
			return;
		}
	}
	
	/*
	 * Get load deactivate Block for tpgb
	 * @Array
	 * @since 1.0.0
	 */
	public static function get_block_deactivate() {
		$load_disable_block = self::$deactivate_blocks;
		
		if( !empty( $load_disable_block ) ) {
			return $load_disable_block;
		}else{
			return;
		}
	}
	
	
	//Get Manu List
    public function get_menu_lists(){
		$menus = wp_get_nav_menus();
		$menu_items = array();
		$menu_items[] = [' ' , esc_html__( 'Select Menu' , 'tpgbp'  ) ];
		foreach ( $menus as $menu ) {
			$menu_items[] = [ $menu->slug , $menu->name];
		}
	
		return $menu_items;
	}
	
	/*
	 * Get Meta Options
	 * @since 1.0.0
	 */
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
	
	/**
	 * Row Shape Divider
	 */
	public function getShapeDivider() {
		$path = TPGB_PATH . 'assets/images/shape-divider';
		$getShapes = glob($path . '/*.svg');
		$shapeLoop = array();
		if (count($getShapes)) {
			foreach ($getShapes as $shape) {
				$shapeLoop[str_replace(array('.svg', $path . '/'), '', $shape)] = file_get_contents($shape);
			}
		}
		return $shapeLoop;
	}
	
	
	/*
	* Dynamic List 
	*
	* @since 1.3.0
	*/
	public static function tpgb_get_dynamic_list(){
		$dynamicList = array();
		$dynamicList['text'][] = [ '' , esc_html__('Select Option','tpgbp') ];
		$dynamicList['text'][] = [ 
			[ 
				'label' => 'Post' , 
				'options' => [ 
					[ 'post-title' , esc_html__('Post Title','tpgbp') ],[ 'post-slug' , esc_html__('Post Slug','tpgbp')], ['post-excerpt' , esc_html__('Post Excerpt','tpgbp')],[ 'post-date' , esc_html__('Post Date','tpgbp')],[ 'post-date-gmt' , esc_html__('Post Date GMT','tpgbp')],[ 'post-modified' ,esc_html__('Post Modified','tpgbp')],[ 'post-modified-gmt' ,esc_html__('Post Modified GMT','tpgbp')],[ 'post-type' ,esc_html__('Post Type','tpgbp')],[ 'post-status' ,esc_html__('Post Status','tpgbp')] 
				] 
			]
		];
		$dynamicList['text'][] = [ 
			[ 
				'label' => 'Archive ' , 
				'options' => [ [ 'archive-title' , esc_html__('Archive Title','tpgbp') ] ,[ 'archive-description' , esc_html__('Archive Description','tpgbp')] ,[ 'archive-url' , esc_html__('Archive Url', 'tpgbp')] ]
			] 
		] ;
		$dynamicList['text'][] = [ 
			[ 
				'label' => 'Author' , 
				'options' => [ [ 'author-name' , esc_html__('Author Name','tpgbp') ] ,[ 'author-id' , esc_html__('Author ID','tpgbp')] ,[ 'author-posts' , esc_html__('Author Posts', 'tpgbp')] ,[ 'author-first-name' , esc_html__('Author First Name','tpgbp')],[ 'author-last-name' , esc_html__('Author Last Name','tpgbp')] ]
			] 
		] ;
		$dynamicList['text'][] = [ 
			[  
				'label' => 'Comment' , 
				'options' => [ [ 'comment-number' , esc_html__('Comment Number','tpgbp') ] , [ 'comment-status' , esc_html__('Comment Status','tpgbp')] ]
			] 
		];
		$dynamicList['text'][] = [ 
			[  
				'label' => 'Site' , 
				'options' => [ [ 'site-title' , esc_html__('Site Title','tpgbp') ] , [ 'site-tagline' , esc_html__('Site Tagline','tpgbp')] ]
			] 
		];
		
		//Woocommerce
		if( class_exists('woocommerce') ){
			$dynamicList['text'][] = [ 
				[  
					'label' => esc_html__('WooCommerce','tpgbp'), 
					'options' => [ [ 'tpgb-product-title-tag' , esc_html__('Product Title','tpgbp')] , [ 'tpgb-product-terms-tag' , esc_html__('Product Terms','tpgbp')],[ 'tpgb-product-price-tag' , esc_html__('Product Price','tpgbp') ] , [ 'tpgb-product-rating-tag' , esc_html__('Product Rating','tpgbp')] , [ 'tpgb-product-sale-tag' , esc_html__('Product Sale','tpgbp')] , [ 'tpgb-product-short-description-tag' , esc_html__('Product Short Description','tpgbp')] , [ 'tpgb-product-sku-tag' , esc_html__('Product SKU','tpgbp')],[ 'tpgb-product-stock-tag' , esc_html__('Product Stock','tpgbp')]]
				] 
			];
		}
		
		$dynamicList['url'][] = [ '' ,esc_html__('Select Option','tpgbp') ];
		$dynamicList['url'][] = [ 
			[ 
				'label' => 'Post' , 
				'options' => [ 
					[ 'post-url' , esc_html__('Post Url','tpgbp') ]
				] 
			]
		];
		$dynamicList['url'][] = [ 
			[ 
				'label' => 'Site' , 
				'options' => [ 
					[ 'site-url' , esc_html__('Site Url','tpgbp') ],
				] 
			]
		];
		$dynamicList['url'][] = [
			[ 
				'label' => 'Author' , 
				'options' => [ 
					[ 'author-post-url' , esc_html__('Author Posts Url','tpgbp') ],
					[ 'author-profile-url' , esc_html__('Author Profile Picture Url','tpgbp') ],
				] 
			]
		];		

		$dynamicList['image'][] = [ '' , esc_html__('Select Option','tpgbp') ];
		$dynamicList['image'][] = [ 
			[ 
				'label' => 'Post' , 
				'options' => [ 
					[ 'post-featured-image' , esc_html__('Featured Image','tpgbp') ]
				] 
			]
		];
		$dynamicList['image'][] = [ 
			[ 
				'label' => 'Site' , 
				'options' => [ 
					[ 'site-logo' , esc_html__('Site Logo','tpgbp') ],
				] 
			]
		];
		$dynamicList['image'][] = [ 
			[ 
				'label' => 'Author' , 
				'options' => [ 
					[ 'author-profile-picture' , esc_html__('Author Profile Picture','tpgbp') ],
					[ 'user-profile-picture' , esc_html__('User Profile Picture','tpgbp') ],
				] 
			]
		];
		
		$dynamicList['color'][] = [ '' , esc_html__('Select Color','tpgbp') ];
		
		/*ACF Plugin*/
		if( class_exists( 'ACF' )){
			if ( function_exists( 'acf_get_field_groups' ) ) {
				$acffield = acf_get_field_groups();
			} else {
				$acffield = apply_filters( 'acf/get_field_groups', [] );
			}
			$keyarr = [];
			foreach ( $acffield as $field_group ) {
				$keyarr[] = $field_group['key'];
			}
			$excluded_field_type = [
				'oembed',
				'gallery',
				'post_object',
				'relationship',
				'google_map',
				'message',
				'accordion',
				'tab',
				'group',
				'repeater',
				'flexible_content',
				'clone',
			];
			foreach ( $acffield as $field_group ) {
				$key = $field_group['key'];
				$title = $field_group['title'];

				$tpgbDyfield = [];
				$tpgbDyUrl = [];
				$tpgbDyImg = [];
				$tpgbDyColor = [];
				
				if ( function_exists( 'acf_get_fields' ) ) {
					if ( isset( $field_group['ID'] ) && ! empty( $field_group['ID'] ) ) {
						$fields = acf_get_fields( $field_group['ID'] );
					} else {
						$fields = acf_get_fields( $field_group );
					}
				} else {
					$fields = apply_filters( 'acf/field_group/get_fields', [], $field_group['id'] );
				}
				
				foreach( $fields as $acf_field ) {
					if ( !empty( $acf_field['name'] ) && ! in_array( $acf_field['type'], $excluded_field_type ) ) {
						$tpgbDyfield[] = [ $acf_field['name'] , $acf_field['label'] ];
					}
					if(!empty( $acf_field['name'] ) && $acf_field['type'] == 'url'){
						$tpgbDyUrl[] = [ $acf_field['name'] , $acf_field['label'] ];
					}
					if(!empty( $acf_field['name'] ) && in_array($acf_field['type'] , ['image','file','gallery'])){
						$tpgbDyImg[] = [ $acf_field['name'] , $acf_field['label'] ];
					}
					if(!empty( $acf_field['name'] ) && $acf_field['type'] == 'color_picker'){
						$tpgbDyColor[] = [ $acf_field['name'] , $acf_field['label'] ];
					}
				}
				$dynamicList['text'][] = [
					[  
						'label' => esc_html('ACF('.$title.')'),
						'options' =>  $tpgbDyfield 
					]
				];
				if(!empty($tpgbDyUrl) ) {
					$dynamicList['url'][] = [
						[ 
							'label' => esc_html('ACF('.$title.')'),
							'options' => $tpgbDyUrl
						]
					];
				}
				if(!empty($tpgbDyImg) ) {
					$dynamicList['image'][] = [ 
						[ 
							'label' => esc_html('ACF('.$title.')'),
							'options' => $tpgbDyImg
						]
					];
				}
				if(!empty($tpgbDyColor) ) {
					$dynamicList['color'][] = [
						[ 
							'label' => esc_html('ACF('.$title.')'),
							'options' => $tpgbDyColor
						]
					];
				}
			}
		}
		
		/*Metabox Plugin*/
		if( class_exists( 'RWMB_Field' ) ){
			$ex_meta = [ 
				'button',
				'button_group',
				'color',
				'file',
				'file_advanced',
				'image',
				'image_advanced',
				'image_select',
				'image_upload',
				'key_value',
				'oembed',
				'osm',
				'post',
				'single_image',
				'slider',
				'switch',
				'taxonomy',
				'taxonomy_advanced',
				'time',
				'video',
				'password',
				'hidden',
				'range',
			];
			$meta_box_registry = (array) rwmb_get_registry( 'meta_box' )->all();
			foreach($meta_box_registry as $meta_field){
				$tpgbmefield = [];
				$tpgbmeUrl = [];
				$tpgbmeImg = [];
				$tpgbmeColor = [];

				$meta_box = (array) $meta_field;
				$meTitle = $meta_box['meta_box']['title'];
				$meta_field = $meta_box['meta_box']['fields'];
				foreach($meta_field as $field){
					if ( ! empty( $field['name'] ) && ! in_array( $field['type'], $ex_meta ) ) {
						$tpgbmefield[] = [ $field['id'] , $field['name'] ];
					}
					if ( ! empty( $field['name'] ) && in_array( $field['type'], ['single_image','image'] ) ) {
						$tpgbmeImg[] = [ $field['id'] , $field['name'] ];
					}
					if ( ! empty( $field['name'] ) && $field['type'] == 'url' ) {
						$tpgbmeUrl[] = [ $field['id'] , $field['name'] ];
					}
					if ( ! empty( $field['name'] ) && $field['type'] == 'color' ) {
						$tpgbmeColor[] = [ $field['id'] , $field['name'] ];
					}
				}
				if(!empty($tpgbmefield)){
					$dynamicList['text'][] = [ 
						[  
							'label' => 'Meta('.$meTitle.')', 
							'options' =>  $tpgbmefield 
						] 
					];
				}
				if(!empty($tpgbmeUrl) ) {
					$dynamicList['url'][] = [
						[ 
							'label' => 'Meta('.$meTitle.')', 
							'options' => $tpgbmeUrl
						]
					];
				}
				if(!empty($tpgbmeImg) ) {
					$dynamicList['image'][] = [ 
						[ 
							'label' => 'Meta('.$meTitle.')', 
							'options' => $tpgbmeImg
						]
					];
				}
				if(!empty($tpgbmeColor) ) {
					$dynamicList['color'][] = [ 
						[ 
							'label' => 'Meta('.$meTitle.')', 
							'options' => $tpgbmeColor
						]
					];
				}
			}
		}
		
		/*Toolset Plugin*/
		if(is_plugin_active( 'types/wpcf.php' )){
			$ex_tool_meta = [
				'file',
				'image',
				'video',
				'embed',
			];
			$groups = wpcf_admin_fields_get_groups();
			foreach($groups AS $group){
				$tpgbtoolfield = [];
				$tpgbtoolUrl = [];
				$tpgbtoolImg = [];
				$tpgbtoolColor = [];
				
				if($group['slug'] !== 'toolset-woocommerce-fields'){
					$fields =  wpcf_admin_fields_get_fields_by_group($group['id']);
					$tooltitle = $group['name'];
					foreach($fields as $key=>$value){
						if ( ! empty( $value['name'] ) && ! in_array( $value['type'], $ex_tool_meta ) ) {
							$tpgbtoolfield[] = [ 'wpcf-'.$value['id'] , $value['name']];
						}
						if(! empty( $value['name'] ) && $value['type'] == 'image' ){
							$tpgbtoolImg[] = [ 'wpcf-'.$value['id'] , $value['name']];
						}
						if(! empty( $value['name'] ) && $value['type'] == 'url' ){
							$tpgbtoolUrl[] = [ 'wpcf-'.$value['id'] , $value['name']];
						}
						if(! empty( $value['name'] ) && $value['type'] == 'colorpicker' ){
							$tpgbtoolColor[] = [ 'wpcf-'.$value['id'] , $value['name']];
						}
					}
				}
				if(!empty($tpgbtoolfield)){
					$dynamicList['text'][] = [
						[  
							'label' => esc_html('Toolset('.$tooltitle.')'),
							'options' =>  $tpgbtoolfield
						]
					];
				}
				if(!empty($tpgbtoolUrl)){
					$dynamicList['url'][] = [
						[ 
							'label' => esc_html('Toolset('.$tooltitle.')'),
							'options' => $tpgbtoolUrl
						]
					];
				}
				if(!empty($tpgbtoolImg)){
					$dynamicList['image'][] = [ 
						[ 
							'label' => esc_html('Toolset('.$tooltitle.')'),
							'options' => $tpgbtoolImg
						]
					];
				}
				if(!empty($tpgbtoolColor)){
					$dynamicList['color'][] = [ 
						[ 
							'label' => esc_html('Toolset('.$tooltitle.')'),
							'options' => $tpgbtoolColor
						]
					];
				}
			}
		}
		
		/*PODS Plugin*/
		if(class_exists('PodsInit')){
			$all_pods = pods_api()->load_pods( [
				'table_info' => true,
				'fields' => true,
			]);
			$groups = [];
			if (!empty($all_pods)) {
				foreach ( $all_pods as $group ) {
					$tpgbpoText = [];
					$tpgbpoUrl = [];
					$tpgbpoImg = [];
					$tpgbpoColor = [];
					if (!empty($group['fields'])) {
						foreach ( $group['fields'] as $field ) {
							if( !empty( $field['name'] ) && in_array( $field['type'], ['text','phone','email','paragraph','wysiwyg','time','oembed'] )){
								$tpgbpoText[] = [ $group['name'] . ':' . $field['pod_id'] . ':' . $field['name'], $field['label']];
							}
							if( !empty( $field['name'] ) && in_array( $field['type'], ['website','phone','email','file'] )){
								$tpgbpoUrl[] = [ $group['name'] . ':' . $field['pod_id'] . ':' . $field['name'], $field['label']];
							}
							if( !empty( $field['name'] ) &&  $field['type'] == 'file'){
								$tpgbpoImg[] = [ $group['name'] . ':' . $field['pod_id'] . ':' . $field['name'], $field['label']];
							}
							if( !empty( $field['name'] ) &&  $field['type'] == 'color'){
								$tpgbpoColor[] =[ $group['name'] . ':' . $field['pod_id'] . ':' . $field['name'], $field['label']];
							}
						}
					}
					if(!empty($tpgbpoText)){
						$dynamicList['text'][] = [
							[
								'label' => esc_html('Pods('.$group['label'].')'),
								'options' => $tpgbpoText,
							]
						];
					}
					if(!empty($tpgbpoUrl)){
						$dynamicList['url'][] = [
							[
								'label' => esc_html('Pods('.$group['label'].')'),
								'options' => $tpgbpoUrl,
							]
						];
					}
					if(!empty($tpgbpoImg)){
						$dynamicList['image'][] = [
							[
								'label' => esc_html('Pods('.$group['label'].')'),
								'options' => $tpgbpoImg,
							]
						];
					}
					if(!empty($tpgbpoColor)){
						$dynamicList['color'][] = [
							[
								'label' => esc_html('Pods('.$group['label'].')'),
								'options' => $tpgbpoColor,
							]
						];
					}
				}
			}
		}
		
		/* Jet Engine Meta Box */
		if ( class_exists( 'Jet_Engine' ) ) {
			$output = array();

			$tpgb_jet_ex = [
				'iconpicker',
				'html',
				'repeater',
				'gallery',
			];

			$tpgb_jet_ex_obj = [
				'tab',
				'accordion',
				'endpoint',
			];

			$tpgb_jet_groups = jet_engine()->meta_boxes->data->raw;
			$uni_groups = jet_engine()->meta_boxes->data->db->query_cache;

			foreach ( $tpgb_jet_groups as $field_group ) {

				$tpgbjetfield = [];
				$tpgbjetUrl = [];
				$tpgbjetImg = [];
				$tpgbjetColor = [];

				foreach ( $field_group['meta_fields'] as $field ) {
					if ( ! in_array( $field['type'], $tpgb_jet_ex ) && ! in_array( $field['object_type'], $tpgb_jet_ex_obj ) ) {
						if( !empty( $field['name'] ) && in_array( $field['type'], ['text','date','time','datetime-local','textarea','wysiwyg','number' , 'radio' , 'select'] )){
							$tpgbjetfield[] = [  $field['name'], $field['title']];
						}
						if(!empty( $field['name'] ) && in_array($field['type'] , ['media','gallery'])){
							$tpgbjetImg[] = [ $field['name'] , $field['title'] ];
						}
						if(!empty( $field['name'] ) && $field['type'] == 'colorpicker'){
							$tpgbjetColor[] = [ $field['name'] , $field['title'] ];
						}
					}
				}

				if(!empty($tpgbjetfield)){
					$dynamicList['text'][] = [
						[
							'label' => esc_html('JetEngine('.$field_group['args']['name'].')'),
							'options' => $tpgbjetfield,
						]
					];
				}
				if(!empty($tpgbjetImg) ) {
					$dynamicList['image'][] = [ 
						[ 
							'label' => esc_html('JetEngine('.$field_group['args']['name'].')'),
							'options' => $tpgbjetImg
						]
					];
				}
				if(!empty($tpgbjetColor) ) {
					$dynamicList['color'][] = [
						[ 
							'label' => esc_html('JetEngine('.$field_group['args']['name'].')'),
							'options' => $tpgbjetColor
						]
					];
				}
			}
		}

		return $dynamicList;
	}
	
	public function get_white_label_option($field){
		$label_options = get_option( 'tpgb_white_label' );	
			$values='';
			if(isset($label_options[$field]) && !empty($label_options[$field])){
				$values=$label_options[$field];
			}	
		return $values;
	}
	
	/* TPGB Free Update white label
 	 * @since 1.0.0
	 */
	public function tpgb_free_white_label_plugin( $all_plugins ){
		$plugin_name = $this->get_white_label_option('tpgb_free_plugin_name');
		$tpgb_plugin_desc = $this->get_white_label_option('tpgb_free_plugin_desc');
		$tpgb_author_name = $this->get_white_label_option('tpgb_free_author_name');
		$tpgb_author_uri = $this->get_white_label_option('tpgb_free_author_uri');
		
		if(!empty($all_plugins[TPGB_BASENAME]) && is_array($all_plugins[TPGB_BASENAME])){
			$all_plugins[TPGB_BASENAME]['Name']           = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[TPGB_BASENAME]['Name'];
			$all_plugins[TPGB_BASENAME]['PluginURI']      = ! empty( $tpgb_author_uri )      ? $tpgb_author_uri       : $all_plugins[TPGB_BASENAME]['PluginURI'];
			$all_plugins[TPGB_BASENAME]['Description']    = ! empty( $tpgb_plugin_desc )     ? $tpgb_plugin_desc      : $all_plugins[TPGB_BASENAME]['Description'];
			$all_plugins[TPGB_BASENAME]['Author']         = ! empty( $tpgb_author_name )   ? $tpgb_author_name    : $all_plugins[TPGB_BASENAME]['Author'];
			$all_plugins[TPGB_BASENAME]['AuthorURI']      = ! empty( $tpgb_author_uri )      ? $tpgb_author_uri       : $all_plugins[TPGB_BASENAME]['AuthorURI'];
			$all_plugins[TPGB_BASENAME]['Title']          = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[TPGB_BASENAME]['Title'];
			$all_plugins[TPGB_BASENAME]['AuthorName']     = ! empty( $tpgb_author_name )   ? $tpgb_author_name    : $all_plugins[TPGB_BASENAME]['AuthorName'];

			return $all_plugins;
		}
	}
	
	/* TPGB Pro Update white label
 	 * @since 1.0.0
	 */
	public function tpgb_pro_white_label_plugin( $all_plugins ){
		$plugin_name = $this->get_white_label_option('tpgb_plugin_name');
		$tpgb_plugin_desc = $this->get_white_label_option('tpgb_plugin_desc');
		$tpgb_author_name = $this->get_white_label_option('tpgb_author_name');
		$tpgb_author_uri = $this->get_white_label_option('tpgb_author_uri');
		
		if(!empty($all_plugins[TPGBP_BASENAME]) && is_array($all_plugins[TPGBP_BASENAME])){
			$all_plugins[TPGBP_BASENAME]['Name']           = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[TPGBP_BASENAME]['Name'];
			$all_plugins[TPGBP_BASENAME]['PluginURI']      = ! empty( $tpgb_author_uri )      ? $tpgb_author_uri       : $all_plugins[TPGBP_BASENAME]['PluginURI'];
			$all_plugins[TPGBP_BASENAME]['Description']    = ! empty( $tpgb_plugin_desc )     ? $tpgb_plugin_desc      : $all_plugins[TPGBP_BASENAME]['Description'];
			$all_plugins[TPGBP_BASENAME]['Author']         = ! empty( $tpgb_author_name )   ? $tpgb_author_name    : $all_plugins[TPGBP_BASENAME]['Author'];
			$all_plugins[TPGBP_BASENAME]['AuthorURI']      = ! empty( $tpgb_author_uri )      ? $tpgb_author_uri       : $all_plugins[TPGBP_BASENAME]['AuthorURI'];
			$all_plugins[TPGBP_BASENAME]['Title']          = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[TPGBP_BASENAME]['Title'];
			$all_plugins[TPGBP_BASENAME]['AuthorName']     = ! empty( $tpgb_author_name )   ? $tpgb_author_name    : $all_plugins[TPGBP_BASENAME]['AuthorName'];

			return $all_plugins;
		}
	}
	
	// Convert Nested Std Object To array
	public function stdToArray($array){
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
						$array[$key] = $this->stdToArray($value);
				}
				if ($value instanceof stdClass) {
						$array[$key] = $this->stdToArray((array)$value);
				}
			}
		}
		if ($array instanceof stdClass) {
			return $this->stdToArray((array)$array);
		}
		return $array;
	}

	/*
	 * Get Post on ajax call Load More & Lazy Load
	 * @since 1.2.1
	 */
	public function tpgb_post_load(){
		global $post;
		ob_start();

		$postdata1 = isset($_POST["option"]) ? wp_unslash( $_POST["option"] ) : '';
		$postdata2 = isset($_POST["dyOpt"]) ? wp_unslash( $_POST["dyOpt"] ) : '';
		//Encrypt Data
		$postdata1 = self::tpgb_check_decrypt_key($postdata1);
		$postdata1 = (array)json_decode($postdata1);

		$postdata = array_merge( $postdata1,$postdata2);
		if(empty($postdata) || !is_array($postdata)){
			ob_get_contents();
			exit;
			ob_end_clean();
		}
		//verify nonce	
		$nonce = (isset($postdata["tpgb_nonce"])) ? wp_unslash( $postdata["tpgb_nonce"] ) : '';
		if ( !isset($postdata["tpgb_nonce"]) || !wp_verify_nonce( $nonce, 'theplus-addons-block' ) ){
			die ( 'Security checked!');
		}
		
		$posttype = isset( $postdata["post_type"] ) ? sanitize_text_field( wp_unslash($postdata["post_type"]) ) : '';
		$taxonomySlug = isset( $postdata["texonomy_category"] ) ? sanitize_text_field( wp_unslash($postdata["texonomy_category"]) ) : '';
		$category = isset( $postdata["category"] ) ? wp_unslash($postdata["category"])  : '';
		$layout = isset( $postdata["layout"] ) ? sanitize_text_field( wp_unslash($postdata["layout"]) ) : '';
		$offset = (isset($postdata["offset"]) && intval($postdata["offset"]) ) ? wp_unslash( $postdata["offset"] ) : '';
		$desktop_column = (isset( $postdata["desktop_column"] )  && intval($postdata["desktop_column"]) ) ? wp_unslash($postdata["desktop_column"]) : '';
		$tablet_column = (isset( $postdata["tablet_column"] )  && intval($postdata["tablet_column"]) ) ? wp_unslash($postdata["tablet_column"]) : '';
		$mobile_column = (isset( $postdata["mobile_column"] )  && intval($postdata["mobile_column"]) ) ? wp_unslash($postdata["mobile_column"]) : '';
		$style = isset( $postdata["style"] ) ? sanitize_text_field( wp_unslash($postdata["style"]) ) : '';
		$styleLayout= isset( $postdata["styleLayout"] ) ? sanitize_text_field( wp_unslash($postdata["styleLayout"]) ) : '';
		$style2Alignment = isset( $postdata['style2Alignment'] ) ? sanitize_text_field( wp_unslash($postdata['style2Alignment']) ) : '';
		$style3Alignment = isset( $postdata['style3Alignment'] ) ? sanitize_text_field( wp_unslash($postdata['style3Alignment']) ) : '';
		$ShowFilter = isset( $postdata["filter_category"] ) ? sanitize_text_field( wp_unslash($postdata["filter_category"]) ) : '';
		$orderBy = isset( $postdata["order_by"] ) ? sanitize_text_field( wp_unslash($postdata["order_by"]) ) : '';
		$order = isset( $postdata["post_order"] ) ? sanitize_text_field( wp_unslash($postdata["post_order"]) ) : '';
		$post_load_more = (isset( $postdata["load_more"] ) && intval($postdata["load_more"]) ) ?  wp_unslash($postdata["load_more"]) : '';
		$paged = (isset( $postdata["page"] ) && intval($postdata["page"]) ) ?  wp_unslash($postdata["page"]) : '';

		$display_post = (isset( $postdata["display_post"] ) && intval($postdata["display_post"]) ) ?  wp_unslash($postdata["display_post"]) : '';
		$showPostMeta = isset( $postdata["display_post_meta"] ) ? sanitize_text_field( wp_unslash($postdata["display_post_meta"]) ) : '';
		$postMetaStyle = isset( $postdata["meta_style"] ) ? sanitize_text_field( wp_unslash($postdata["meta_style"]) ) : '';
		$ShowDate = isset( $postdata["showdate"] ) ? sanitize_text_field( wp_unslash($postdata["showdate"]) ) : '';
		$ShowAuthor = isset( $postdata["showauthor"] ) ? sanitize_text_field( wp_unslash($postdata["showauthor"]) ) : '';
		$ShowAuthorImg = isset( $postdata['ShowAuthorImg'] ) ? sanitize_text_field( wp_unslash($postdata['ShowAuthorImg']) ) : '';
		
		$display_thumbnail = isset( $postdata['display_thumbnail'] ) ? sanitize_text_field( wp_unslash($postdata['display_thumbnail']) ) : '';
		$thumbnail = isset( $postdata['thumbnail'] ) ? sanitize_text_field( wp_unslash($postdata['thumbnail']) ) : 'full';
		
		$postCategoryStyle = isset( $postdata["post_category_style"] ) ? sanitize_text_field( wp_unslash($postdata["post_category_style"]) ) : '';
		$showPostCategory = isset( $postdata["display_catagory"] ) ? sanitize_text_field( wp_unslash($postdata["display_catagory"]) ) : '';

		$ShowTitle = isset( $postdata["display_title"] ) ? sanitize_text_field( wp_unslash($postdata["display_title"]) ) : '';
		$titleTag = isset( $postdata['titletag'] ) ? sanitize_text_field( wp_unslash($postdata['titletag']) ) : '';
		$titleLimit = isset( $postdata["display_title_limit"] ) ? wp_unslash($postdata["display_title_limit"]) : '';
		$titleByLimit = isset( $postdata["display_title_by"] ) ? wp_unslash($postdata["display_title_by"]) : '';
		$filterBy = isset( $postdata['filterBy'] ) ? sanitize_text_field( wp_unslash($postdata['filterBy']) ) : '';

		$showExcerpt = isset( $postdata["display_excerpt"] ) ? sanitize_text_field( wp_unslash($postdata["display_excerpt"]) ) : '';
		$excerptByLimit = isset( $postdata["excerptByLimit"] ) ? wp_unslash($postdata["excerptByLimit"]) : '';
		$excerptLimit = isset( $postdata["excerptLimit"] ) ? wp_unslash($postdata["excerptLimit"]) : '';
		
		$ShowButton = isset( $postdata['displaybuttton'] ) ? sanitize_text_field( wp_unslash($postdata['displaybuttton']) ) : '';
		$postbtntext = isset( $postdata['postbtntext'] ) ? sanitize_text_field( wp_unslash($postdata['postbtntext']) ) : '';
		$postBtnsty = isset( $postdata['buttonstyle'] ) ? sanitize_text_field( wp_unslash($postdata['buttonstyle']) ) : '';
		$pobtnIconType = isset( $postdata['pobtnIconType'] ) ? sanitize_text_field( wp_unslash($postdata['pobtnIconType']) ) : '';
		$pobtnIconName = isset( $postdata['pobtnIconName'] ) ? sanitize_text_field( wp_unslash($postdata['pobtnIconName']) ) : '';
		$btnIconPosi = isset( $postdata['btnIconPosi'] ) ? sanitize_text_field( wp_unslash($postdata['btnIconPosi']) ) : '';
		$imageHoverStyle = isset( $postdata['imageHoverStyle'] ) ? sanitize_text_field( wp_unslash($postdata['imageHoverStyle']) ) :'';
		$postTag =  isset( $postdata['postTag'] ) ? wp_unslash($postdata['postTag']) : '';
		$includePosts = (isset( $postdata["includePosts"] )  && intval($postdata["includePosts"]) ) ? wp_unslash($postdata["includePosts"]) : '';
		$excludePosts = (isset( $postdata["excludePosts"] )  && intval($postdata["excludePosts"]) ) ? wp_unslash($postdata["excludePosts"]): '';
		$display_product = isset( $postdata["disproduct"] ) ? wp_unslash($postdata["disproduct"]) : '';
		$dyload = isset( $postdata["dyload"] ) ? wp_unslash($postdata["dyload"]) : '';

		$metrocolumns = isset( $postdata["metro_column"] ) ? (array) wp_unslash($postdata["metro_column"]) : [' md' => '3'] ;
		$metroStyle = isset($postdata['metro_style']) ?  (array) wp_unslash($postdata['metro_style']) : '';
		$authorTxt = isset($postdata['authorTxt']) ? wp_unslash($postdata['authorTxt']) : '';
		$blockTemplate = !empty( $postdata['blockTemplate'] ) ? sanitize_text_field( wp_unslash($postdata['blockTemplate']) ) : '';
		$searchTxt = isset( $postdata["searchTxt"] ) ? wp_unslash($postdata["searchTxt"]) : '';
		$customQueryId = !empty( $postdata['customQueryId'] ) ? $postdata['customQueryId'] : '';
		$showcateTag = !empty( $postdata['showcateTag'] ) ? sanitize_text_field( wp_unslash($postdata['showcateTag']) ) : '';
		$cuscntType = !empty( $postdata['cuscntType'] ) ? sanitize_text_field( wp_unslash($postdata['cuscntType']) ) : '';
		$block_instance = isset( $postdata['blockArr'] ) ? wp_unslash($postdata['blockArr']) : '';
		$block_instance = $this->stdToArray($block_instance);
		$content = '';
	
		$column_class = '';
		if($layout!='carousel' && $layout!='metro'){
			$column_class .= " tpgb-col-lg-".esc_attr($desktop_column);
			$column_class .= " tpgb-col-md-".esc_attr($tablet_column);
			$column_class .= " tpgb-col-sm-".esc_attr($mobile_column);
			$column_class .= " tpgb-col-".esc_attr($mobile_column);
		}
		
		$args = array(
			'post_type'  => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => $post_load_more,
			'offset' => $offset,
			'orderby' => $orderBy,
			'order'	=> $order,
			'paged' => $paged,
		);
		
		if(!empty($excludePosts)){
			$args['post__not_in'] =  explode(',', $excludePosts);
		}

		if( !empty($searchTxt)){
			$args['s'] = $searchTxt;
		}

		if(!empty($includePosts)){
			$args['post__in'] = explode(',', $includePosts);
		}

		if($posttype == 'post'){
			$args['cat'] = $category;
		}else if($posttype == 'product'){
			if(!empty($category)){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'term_id',
						'terms' => $category,
					),
				);
			}
			if(!empty($postTag)){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_tag',
						'field' => 'term_id',
						'terms' => $postTag,
					),
				);
			}
		}else{
			if (!empty($posttype) && ($posttype !='post' && $posttype !='product')) {
				if ( !empty($taxonomySlug) && $taxonomySlug == 'category' && !empty($category)) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'category',
							'field' => 'term_id',
							'terms' => $category,
						),
					);
				}else{
					if(!empty($category)){
						$args['tax_query'] = array(
							array(
								'taxonomy' => $taxonomySlug,
								'field' => 'term_id',
								'terms' => $category,
							),
						);
					}
				}
			}else{
				$args[$taxonomySlug] = $category;
			}
		}
		
		if((!empty($posttype) && $posttype =='product')){
			if(!empty($display_product) && $display_product=='featured'){
				$args['tax_query']     = array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
					),
				);
			}
			
			if(!empty($display_product) && $display_product=='on_sale'){
				$args['meta_query']     = array(
					'relation' => 'OR',
					array( // Simple products type
						'key'           => '_sale_price',
						'value'         => 0,
						'compare'       => '>',
						'type'          => 'numeric'
					),
					array( // Variable products type
						'key'           => '_min_variation_sale_price',
						'value'         => 0,
						'compare'       => '>',
						'type'          => 'numeric'
					)
				);
			}
			
			if(!empty($display_product) && $display_product=='top_sales'){
				$args['meta_query']     = array(
					array(
						'key' 		=> 'total_sales',
						'value' 	=> 0,
						'compare' 	=> '>',
						)
				);
			}
			
			if(!empty($display_product) && $display_product=='instock'){
				$args['meta_query']     = array(
					array(
						'key' 		=> '_stock_status',
						'value' 	=> 'instock',												
					)
				);
			}
			
			if(!empty($display_product) && $display_product=='outofstock'){
				$args['meta_query']     = array(
					array(
						'key' 		=> '_stock_status',
						'value' 	=> 'outofstock',												
					)
				);
			}

		}
		
		if ( !empty($postTag) && $posttype=='post') {
			$args['tax_query'] = array(
			'relation' => 'AND',
				array(
					'taxonomy'         => 'post_tag',
					'terms'            => $postTag,
					'field'            => 'term_id',
					'operator'         => 'IN',
					'include_children' => true,
				),
			);
		}
		
		if( isset($postdata['type']) && $postdata['type'] == 'searchList'){

			$taxo =  '';
			$catArr = [];
			$meta_keyArr = [];
			$meta_keyArr1 = [];
			if(!empty($postdata['seapara'])){
				foreach($postdata['seapara'] as $item => $val ) {
					if($posttype == 'post' && $item != 'searTxt' ){
						if($item == 'category' && !empty($val)){
							$args['category__in'] = $val;
						} 
						if($item == 'post_tag' && !empty($val) ){
							$args['tag__in'] = $val;
						}
					}else if($posttype == 'product' && $item != 'searTxt' ) {

						$cusField = acf_get_field($item);

						if($item == 'product_tag' ){
							$tags_args=array(
								'taxonomy'     => $item,
								'field'        => 'id',
								'terms'        => $val
							);
						}else if($item == 'product_cat' ){
							$category_args = array(
								'taxonomy'     => $item,
								'field'        => 'id',
								'terms'        => $val
							);
						}else if($item == 'price' ){
							$meta_keyArr[] = array(
								'key' => '_price',
								'value' => $val[0],
								'compare' => 'BETWEEN',
								'type' => 'NUMERIC' 
							);
							

						}else if($item == 'tpgb-datepicker1'){
							$args['date_query'] = array(
								array(
									'after'     => $val[0],
									'before'    => $val[1],
									'inclusive' => true,
								),
							);
						}else if(!empty($cusField)){
							
							if( !empty($val) && is_array($val) && $cusField['type'] != 'date_picker' && $cusField['type'] != 'range' ){
								$meta_keyArr1 = [];
								foreach( $val as $key => $metadata){
									$meta_keyArr1[] = array(
										'key'		=> $item,
										'value'		=> $metadata,
										'compare'	=> 'LIKE'
									);
								}
							}else if($cusField['type'] == 'date_picker' || $cusField['type'] == 'range'){
								$meta_keyArr1[] = array(
									'key'		=> $item,
									'value'		=> ($cusField['type'] == 'date_picker' ? $val : $val[0]  ),
									'compare'   => 'BETWEEN',
        							'type'      => ($cusField['type'] == 'date_picker' ? 'DATE' : 'NUMERIC' ),
								);
							}else if($cusField['type'] == 'text'){
								$meta_keyArr1[] = array(
									'key'		=> $item,
									'value'		=> $val,
									'compare'	=> 'LIKE'
								);
							}else{
								$meta_keyArr1[] = array(
									'key'		=> $item,
									'value'		=> $val,
									'compare'	=> 'IN'
								);
							}
							$meta_keyArr[] =$meta_keyArr1;
						}else {
							$attr_tax = array(
								'taxonomy'     => $item,
								'field'        => 'id',
								'terms'        => $val
							);
						}
						
						$args['tax_query'] = [ 'relation' => 'AND', $category_args, $tags_args , $attr_tax ];

					}else if($item != 'searTxt'){
						$args['tax_query'] = [['taxonomy' => $item, 'field' => 'id', 'terms' => $val]];
					}
				} 
				$args['meta_query'] = array('relation' => 'AND', $meta_keyArr);
			}
			$args['s'] = $postdata['seapara']['searTxt'];
			$args['orderby'] = 'relevance';
			$args['posts_per_page'] =  -1;
		}
		$count=($post_load_more*$paged)-$post_load_more+(int)$display_post+1;

        /*custom query id*/
		if( !empty($customQueryId) ){
			
			if(has_filter( $customQueryId )) {
				$args = apply_filters( $customQueryId, $args);
			}
		}
		/*custom query id*/
		
		$col=$tabCol=$moCol='';

		$loop = new WP_Query($args);

		if ( $loop->have_posts() ) {
			ob_start();
			while ($loop->have_posts()) {
				$loop->the_post();

				if($posttype == 'product' && $postdata['type'] != 'searchList'){
					if($dyload != 'postListing'){
						include TPGBP_PATH ."includes/ajax-load-post/product-style.php";
					}else{
						include TPGBP_PATH ."includes/ajax-load-post/post-listing.php";
					}
				}else{
					include TPGBP_PATH ."includes/ajax-load-post/post-listing.php";
				}
				$count++;
			}
			$content = ob_get_contents();
			ob_end_clean();
		}
		
		wp_reset_postdata();
		echo $content;
		exit();
		ob_end_clean();
	}
	
	/*
	 * Global Carousel Pro Options
	 */
	public function tpgb_pro_carousel_options($options){
		$pro_option = [
			'dotsBorderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-6'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-1 ul.splide__pagination li button.splide__pagination__page{-webkit-box-shadow:inset 0 0 0 8px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};box-shadow: inset 0 0 0 8px {{dotsBorderColor}};} {{PLUS_WRAP}}.dots-style-1 ul.splide__pagination li button.splide__pagination__page.is-active{-webkit-box-shadow:inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}}.dots-style-2 ul.splide__pagination li button.splide__pagination__page, {{PLUS_WRAP}}.dots-style-6 .splide__pagination button{border: 1px solid {{dotsBorderColor}};}{{PLUS_WRAP}}.dots-style-3 .splide__pagination li button{-webkit-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}}.dots-style-3 .splide__pagination li button.is-active{-webkit-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 8px {{dotsBorderColor}};box-shadow: inset 0 0 0 8px {{dotsBorderColor}};}{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button::before{-webkit-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsBorderColor}};box-shadow: inset 0 0 0 1px {{dotsBorderColor}};}{{PLUS_WRAP}}.dots-style-1 ul.splide__pagination li button.splide__pagination__page{background: transparent;color: {{dotsBorderColor}};}',
					],
				],
				'scopy' => true,
			],
			'dotsBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-3','style-4','style-5','style-7'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}}.dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}}.dots-style-3 .splide__pagination li button,{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button::before,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li button,{{PLUS_WRAP}}.dots-style-7 .splide__pagination li button{background:{{dotsBgColor}};}',
					],
				],
				'scopy' => true,
			],
			'dotsActiveBorderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-6'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 .splide__pagination li button.is-active::after{border-color: {{dotsActiveBorderColor}};}{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button.is-active{-webkit-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};}{{PLUS_WRAP}}.dots-style-6 .splide__pagination button::after{color: {{dotsActiveBorderColor}};}',
					],
				],
				'scopy' => true,
			],
			'dotsActiveBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [ 
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-5','style-7'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 .splide__pagination li button.is-active::after,{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button.is-active::before,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li:hover button,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li button.is-active,{{PLUS_WRAP}}.dots-style-7 .splide__pagination li button.is-active{background: {{dotsActiveBgColor}};}',
					],
				],
				'scopy' => true,
			],
			
			'arrowsBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'arrowsStyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-6'] ],
							(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6:before{background:{{arrowsBgColor}};}{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4 .icon-wrap{border-color:{{arrowsBgColor}}}',
					],
				],
				'scopy' => true,
			],
			'arrowsIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4 .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6 .icon-wrap svg{color:{{arrowsIconColor}};}{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2 .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2 .icon-wrap:after,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5 .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5 .icon-wrap:after{background:{{arrowsIconColor}};}',
					],
				],
				'scopy' => true,
			],
			'arrowsHoverBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'arrowsStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4'] ],
							(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:hover,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3:hover .icon-wrap{background:{{arrowsHoverBgColor}};}{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover:before,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover .icon-wrap{border-color:{{arrowsHoverBgColor}};}',
					],
				],
				'scopy' => true,
			],
			'arrowsHoverIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'showArrows', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__arrows.style-1 .splide__arrow.style-1:hover:before,{{PLUS_WRAP}} .splide__arrows.style-3 .splide__arrow.style-3:hover .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-4 .splide__arrow.style-4:hover .icon-wrap,{{PLUS_WRAP}} .splide__arrows.style-6 .splide__arrow.style-6:hover .icon-wrap svg{color:{{arrowsHoverIconColor}};}{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-2 .splide__arrow.style-2:hover .icon-wrap:after,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5:hover .icon-wrap:before,{{PLUS_WRAP}} .splide__arrows.style-5 .splide__arrow.style-5:hover .icon-wrap:after{background:{{arrowsHoverIconColor}};}',
					],
				],
				'scopy' => true,
			],
			
			'centerPadding' => [
				'type' => 'object',
				'default' => (object)[ 'md' => 0,'sm' => 0,'xs' => 0 ],
				'scopy' => true,
			],
			'centerSlideEffect' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'centerslideScale' => [
				'type' => 'string',
				'default' => 1,
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > div{-webkit-transform: scale({{centerslideScale}});-moz-transform: scale({{centerslideScale}});-ms-transform: scale({{centerslideScale}});-o-transform: scale({{centerslideScale}});transform: scale({{centerslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'normalslideScale' => [
				'type' => 'string',
				'default' => 1,
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide  > div{-webkit-transform: scale({{normalslideScale}});-moz-transform: scale({{normalslideScale}});-ms-transform: scale({{normalslideScale}});-o-transform: scale({{normalslideScale}});transform: scale({{normalslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'slideOpacity' => [
				'type' => 'object',
				'default' => (object)[ 'md' => 1,'sm' => 1,'xs' => 1 ],
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide:not(.is-active) > div{opacity:{{slideOpacity}};}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'slideBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'shadow' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > div',
					],
				],
				'scopy' => true,
			],
			'slideheightRatio' => [
				'type' => 'string',
				'default' => '0.5',
				'scopy' => true,
			],
			'trimSpace' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
		];

		return array_merge($options, $pro_option);
	}
	
	/* Attachment Media Image Url Field*/
	public function tpgb_attachment_field_media( $form_fields, $post ) {
		$form_fields['tpgb-gallery-url'] = array(
			'label' => esc_html__('Custom URL','tpgbp'),
			'input' => 'url',
			'value' => get_post_meta( $post->ID, 'tpgb_gallery_url', true ),
			'helps' => esc_html__('Gallery Listing Widget Used Custom Url Media','tpgbp'),
		);
		return $form_fields;
	}
	
	/* Attachment Media Custom Url Field Save*/
	public function tpgb_attachment_field_save( $post, $attachment ) {
		if( isset( $attachment['tpgb-gallery-url'] ) )
			update_post_meta( $post['ID'], 'tpgb_gallery_url', esc_url( $attachment['tpgb-gallery-url'] ) ); 
		
		return $post;
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
	 * Dynamic Fields Content Check Value
	 * @since 1.3.0
	 */
	public static function tpgb_dynamic_val($block_content = '', $block=[]){
		// Get Dynamic Content
		//<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>
		// if(isset($block['blockName']) && !empty($block['blockName'])){
		// 	$data_match = "/<span data-tpgb-dynamic=(.*?)>([^$]+)?<\/span>/";
		// }else{
		// 	$data_match = '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/';
		// }
		
		$data_match = [ '/<span data-tpgb-dynamic=(.*?)>(.*?)<\/span>/' , '/<span data-tpgb-dynamic=(.*?)>([^$]+)?<\/span>/' ];

		foreach($data_match as $datamatch ){
			if(!empty($block_content) && preg_match_all( $datamatch , $block_content, $matches )){
				$dynamicCnt = $matches[0];
				foreach ( $dynamicCnt as $dynamic_entry ) {
					if(preg_match( '/data-tpgb-dynamic=(.*?)\}/', $dynamic_entry, $route )){
						$jsonString = "[".str_replace("'", "", $route[1]). "}]";
						$array = json_decode($jsonString);
						$dynamicpara['field'] =  (array) $array[0];
						$dynamicpara['id'] = get_the_ID();
						
						if(has_filter('tpgb_get_dynamic_content')) {
							$dynamicVal = apply_filters('tpgb_get_dynamic_content', $dynamicpara);

							if(is_array($dynamicVal)){
								$block_content = (!empty($dynamicVal['url']) && isset( $dynamicVal['url']) ? str_replace( $dynamic_entry, $dynamicVal['url'], $block_content ) : ''  ) ;
							}else{
								$block_content = str_replace( $dynamic_entry, $dynamicVal, $block_content );
							}
						}
					}
				}
			}
		}
		
		if(!empty($block_content) && preg_match_all('/(?:[\'"]http(.*?)[\'"]).*?>/', $block_content, $matche)){
			if( !empty($matche) && isset($matche[1]) ){
				foreach ( $matche[1] as $dyncnt ) {
					if(preg_match_all('/tpgb-dynamicurl=(.*?)\!#/', $dyncnt, $mat)){
						if( !empty($mat) && isset($mat[1]) ){
							foreach ( $mat[1] as $dynaurl ) {
								$dynamicpara = [];
								if( !empty($block['attrs'][$dynaurl]) && !empty($block['attrs'][$dynaurl]['dynamic'])){
									$dynamicpara['field'] =  $block['attrs'][$dynaurl]['dynamic'];
									$dynamicpara['id'] = get_the_ID();
									$dynamicVal = apply_filters('tpgb_get_dynamic_content', $dynamicpara);
									if(is_array($dynamicVal) && isset($dynamicpara['field']['type']) && $dynamicpara['field']['type'] == 'image' ){
										if(!empty($dynamicVal['url']) && isset( $dynamicVal['url'])){
											$block_content = str_replace( 'http'.$dyncnt, $dynamicVal['url'] , $block_content );
										}
									}else if(!empty($dynamicVal)){
										$block_content = str_replace( 'http'.$dyncnt, $dynamicVal , $block_content );
									}
								}
								
							}
						}
					}
				}
			}
		}

		return $block_content;
	}
	
	/*
	 * Dynamic Fields Repeater Check Value
	 * @since 1.3.0
	 */
	public static function tpgb_dynamic_repeat_url($options = []){
		$value = '';

		if(!empty($options) && !empty($options['dynamic'])){
			$dynamicpara = [];
			$dynamicpara['field'] =  $options['dynamic'];
			$dynamicpara['id'] = get_the_ID();
			$dynamicVal = apply_filters('tpgb_get_dynamic_content', $dynamicpara);

			if(is_array($dynamicVal) && isset($dynamicpara['field']['type']) && $dynamicpara['field']['type'] == 'image' ){
				if(!empty($dynamicVal['url']) && isset( $dynamicVal['url'])){
					$value = $dynamicVal['url'];
				}else{
					$value = $dynamicVal;
				}
			}else if(!empty($dynamicVal)){
				$value = $dynamicVal;
			}
			
		}
		return $value;
	}
	
	/*
	 * Social Review Get API
	 * @since 1.3.0
	 */
	public function tpgb_socialreview_Gettoken() {
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
	 * Social Review Load
	 * @since 1.3.0
	 */
	public function tpgb_reviews_load(){
		ob_start();
		$result = [];
		$load_attr = isset($_POST["loadattr"]) ? wp_unslash( $_POST["loadattr"] ) : '';
		if(empty($load_attr)){
			ob_get_contents();
				exit;
			ob_end_clean();
		}
		$load_attr = self::tpgb_check_decrypt_key($load_attr);
		$load_attr = json_decode($load_attr,true);
		if(!is_array($load_attr)){
			ob_get_contents();
				exit;
			ob_end_clean();
		}
		
		$nonce = (isset($load_attr["tpgb_nonce"])) ? wp_unslash( $load_attr["tpgb_nonce"] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'theplus-addons-block' ) ){
			die ( 'Security checked!');
		}
		
		$UserFooter = (!empty($load_attr['s2Layout']) ? wp_unslash($load_attr['s2Layout']) : 'layout-1' );
		$load_class = isset( $load_attr["load_class"] ) ? sanitize_text_field( wp_unslash($load_attr["load_class"]) ) : '';
		$review_id = isset( $load_attr["review_id"] ) ? sanitize_text_field( wp_unslash($load_attr["review_id"]) ) : '';
		$style = isset( $load_attr["style"] ) ? sanitize_text_field( wp_unslash($load_attr["style"]) ) : '';
		$layout = isset( $load_attr["layout"] ) ? sanitize_text_field( wp_unslash($load_attr["layout"]) ) : '';
		
		$desktop_column = (isset( $load_attr["desktop_column"] )  && intval($load_attr["desktop_column"]) ) ? wp_unslash($load_attr["desktop_column"]) : '';
		$tablet_column = (isset( $load_attr["tablet_column"] )  && intval($load_attr["tablet_column"]) ) ? wp_unslash($load_attr["tablet_column"]) : '';
		$mobile_column = (isset( $load_attr["mobile_column"] )  && intval($load_attr["mobile_column"]) ) ? wp_unslash($load_attr["mobile_column"]) : '';
		$DesktopClass = isset( $load_attr["DesktopClass"] ) ? sanitize_text_field( wp_unslash($load_attr["DesktopClass"]) ) : '';
		$TabletClass = isset( $load_attr["TabletClass"] ) ? sanitize_text_field( wp_unslash($load_attr["TabletClass"]) ) : '';
		$MobileClass = isset( $load_attr["MobileClass"] ) ? sanitize_text_field( wp_unslash($load_attr["MobileClass"]) ) : '';

		$CategoryWF = isset( $load_attr["categorytext"] ) ? sanitize_text_field( wp_unslash($load_attr["categorytext"]) ) : '';
		$FeedId = (!empty($_POST["FeedId"]) && isset( $load_attr["FeedId"] ) ) ? wp_unslash( preg_split("/\,/", $load_attr["FeedId"]) ) : '';
		
		$txtLimt = isset( $load_attr["TextLimit"] ) ? wp_unslash($load_attr["TextLimit"]) : '';
		$TextCount = isset( $load_attr["TextCount"] ) ? wp_unslash($load_attr["TextCount"]) : '';
		$TextType = isset( $load_attr["TextType"] ) ? wp_unslash($load_attr["TextType"]) : '';
		$TextMore = isset( $load_attr["TextMore"] ) ? wp_unslash($load_attr["TextMore"]) : '';
		$TextDots = isset( $load_attr["TextDots"] ) ? wp_unslash($load_attr["TextDots"]) : '';
		
		$postview = (isset( $load_attr["postview"] )  && intval($load_attr["postview"]) ) ? wp_unslash($load_attr["postview"]) : '';
		$display = (isset( $load_attr["display"] )  && intval($load_attr["display"]) ) ? wp_unslash($load_attr["display"]) : '';
		$disSocialIcon = (isset( $load_attr["disSocialIcon"] ) ) ? wp_unslash($load_attr["disSocialIcon"]) : false;
		$disProfileIcon = (isset( $load_attr["disProfileIcon"] ) ) ? wp_unslash($load_attr["disProfileIcon"]) : false;
		
		$view = isset($_POST["view"]) ? intval($_POST["view"]) : '';	
		$loadFview = isset($_POST["loadFview"]) ? intval($_POST["loadFview"]) : '';
			
		$FinalData = get_transient("SR-LoadMore-".$review_id);
		$FinalDataa = array_slice($FinalData, $view, $loadFview);

		$desktop_class=$tablet_class=$mobile_class='';
		if($layout != 'carousel'){
			$desktop_class .= ' tpgb-col-'.esc_attr($mobile_column);
			$desktop_class .= ' tpgb-col-lg-'.esc_attr($desktop_column);
			$tablet_class = ' tpgb-col-md-'.esc_attr($tablet_column);
			$mobile_class = ' tpgb-col-sm-'.esc_attr($mobile_column);
		}

		foreach ($FinalDataa as $F_index => $Review) {
			$RKey = !empty($Review['RKey']) ? $Review['RKey'] : '';
			$RIndex = !empty($Review['Reviews_Index']) ? $Review['Reviews_Index'] : '';
			$PostId = !empty($Review['PostId']) ? $Review['PostId'] : '';
			$Type = !empty($Review['Type']) ? $Review['Type'] : '';
			$Time = !empty($Review['CreatedTime']) ? $Review['CreatedTime'] : '';
			$UName = !empty($Review['UserName']) ? $Review['UserName'] : '';
			$UImage = !empty($Review['UserImage']) ? $Review['UserImage'] : '';
			$ULink = !empty($Review['UserLink']) ? $Review['UserLink'] : '';
			$PageLink = !empty($Review['PageLink']) ? $Review['PageLink'] : '';
			$Massage = !empty($Review['Massage']) ? $Review['Massage'] : '';
			$Icon = !empty($Review['Icon']) ? $Review['Icon'] : 'fas fa-star';
			$Logo = !empty($Review['Logo']) ? $Review['Logo'] : '';
			$rating = !empty($Review['rating']) ? $Review['rating'] : '';
			$CategoryText = !empty($Review['FilterCategory']) ? $Review['FilterCategory'] : '';
			$ReviewClass = !empty($Review['selectType']) ? ' '.$Review['selectType'] : '';
			$ErrClass = !empty($Review['ErrorClass']) ? $Review['ErrorClass'] : '';
			$PlatformName = !empty($Review['selectType']) ? ucwords(str_replace('custom', '', $Review['selectType'])) : '';	

			$category_filter=$loop_category='';
			if( !empty($CategoryWF) && !empty($CategoryText)  && $layout != 'carousel' ){
				$loop_category = explode(',', $CategoryText);
				foreach( $loop_category as $category ) {
					$category = preg_replace('/[^A-Za-z0-9-]+/', '-', $category);
					$category_filter .=' '.esc_attr($category).' ';
				}
			}
			if(!empty($style)){
				include TPGBP_INCLUDES_URL. 'social-reviews/'.sanitize_file_name('social-review-'.$style.'.php');
			}
		}
		
		$GridData = ob_get_clean();

		$result['success'] = 1;
		$result['TotalReview'] = isset($load_attr['TotalReview']) ? wp_unslash($load_attr['TotalReview']) : '';
		$result['FilterStyle'] = isset($load_attr['FilterStyle']) ? wp_unslash($load_attr['FilterStyle']) : '';
		$result['allposttext'] = isset($load_attr['allposttext']) ? wp_unslash($load_attr['allposttext']) : '';
		$result['HTMLContent'] = $GridData;
		
		return wp_send_json($result);
	}
	
	/* 
	 * Social Feed Load More & Lazy Load
	 * @since 1.3.0
	 */
	public function tpgb_social_feed_load(){
		ob_start();
		$result = [];
		$load_attr = isset($_POST["loadattr"]) ? wp_unslash( $_POST["loadattr"] ) : '';
		
		if(empty($load_attr)){
			ob_get_contents();
			exit;
			ob_end_clean();
		}

		$load_attr = self::tpgb_check_decrypt_key($load_attr);
		$load_attr = json_decode($load_attr, true);
		if(!is_array($load_attr)){
			ob_get_contents();
			exit;
			ob_end_clean();
		}
		
		$nonce = (isset($load_attr["tpgb_nonce"])) ? wp_unslash( $load_attr["tpgb_nonce"] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'theplus-addons-block' ) ){
			die ( 'Security checked!');
		}
		$load_class = isset( $load_attr["load_class"] ) ? sanitize_text_field( wp_unslash($load_attr["load_class"]) ) : '';
		$feed_id = isset( $load_attr["feed_id"] ) ? sanitize_text_field( wp_unslash($load_attr["feed_id"]) ) : '';
		$style = isset( $load_attr["style"] ) ? sanitize_text_field( wp_unslash($load_attr["style"]) ) : 'style-1';
		$layout = isset( $load_attr["layout"] ) ? sanitize_text_field( wp_unslash($load_attr["layout"]) ) : 'grid';

		$desktop_column = (isset( $load_attr["desktop_column"] )  && intval($load_attr["desktop_column"]) ) ? wp_unslash($load_attr["desktop_column"]) : '';
		$tablet_column = (isset( $load_attr["tablet_column"] )  && intval($load_attr["tablet_column"]) ) ? wp_unslash($load_attr["tablet_column"]) : '';
		$mobile_column = (isset( $load_attr["mobile_column"] )  && intval($load_attr["mobile_column"]) ) ? wp_unslash($load_attr["mobile_column"]) : '';
		
		$postview = (isset( $load_attr["postview"] ) && intval($load_attr["postview"]) ) ? wp_unslash($load_attr["postview"]) : '';
		$display = (isset( $load_attr["display"] ) && intval($load_attr["display"]) ) ? wp_unslash($load_attr["display"]) : '';

		$txtLimt = isset( $load_attr["TextLimit"] ) ? wp_unslash($load_attr["TextLimit"]) : '';
		$TextCount = isset( $load_attr["TextCount"] ) ? wp_unslash($load_attr["TextCount"]) : '';
		$TextType = isset( $load_attr["TextType"] ) ? wp_unslash($load_attr["TextType"]) : '';
		$TextMore = isset( $load_attr["TextMore"] ) ? wp_unslash($load_attr["TextMore"]) : '';
		$TextDots = isset( $load_attr["TextDots"] ) ? wp_unslash($load_attr["TextDots"]) : '';
		$FancyStyle = isset( $load_attr["FancyStyle"] ) ? wp_unslash($load_attr["FancyStyle"]) : 'default';
		$DescripBTM = isset( $load_attr["DescripBTM"] ) ? wp_unslash($load_attr["DescripBTM"]) : '';
		$MediaFilter = isset( $load_attr["MediaFilter"] ) ? wp_unslash($load_attr["MediaFilter"]) : 'default';
		$CategoryWF = isset( $load_attr["categorytext"] ) ? wp_unslash($load_attr["categorytext"]) : '';
		$TotalPost = (isset( $load_attr["TotalPost"] )  && intval($load_attr["TotalPost"]) ) ? wp_unslash($load_attr["TotalPost"]) : '';
		$PopupOption = isset( $load_attr["PopupOption"] ) ? wp_unslash($load_attr["PopupOption"]) : 'Donothing';
		
		$block_id = $load_class;
		
		$FinalData = get_transient("SF-LoadMore-".$feed_id);
		$view = isset($_POST["view"]) ? intval($_POST["view"]) : [];	
		$loadFview = isset($_POST["loadFview"]) ? intval($_POST["loadFview"]) : [];

		$FancyBoxJS='';
		if($PopupOption == 'OnFancyBox'){
			$FancyBoxJS = 'data-fancybox="'.esc_attr($load_class).'"';
		}
		
		$desktop_class='';
		if($layout != 'carousel'){
			$desktop_class .= 'tpgb-col-lg-'.esc_attr($desktop_column);
			$desktop_class .= ' tpgb-col-md-'.esc_attr($tablet_column);
			$desktop_class .= ' tpgb-col-sm-'.esc_attr($mobile_column);
			$desktop_class .= ' tpgb-col-'.esc_attr($mobile_column);
		}	
		$FinalDataa=[];
		if( is_array($FinalData) ){
			$FinalDataa = array_slice($FinalData, $view , $loadFview);
		}
		if(!empty($FinalDataa)){
			foreach ($FinalDataa as $F_index => $loadData) {
				$PopupTarget=$PopupLink='';
				$uniqEach = uniqid();
				$PopupSylNum = "{$block_id}-{$F_index}-{$uniqEach}";
				$RKey = !empty($loadData['RKey']) ? $loadData['RKey'] : '';
				$PostId = !empty($loadData['PostId']) ? $loadData['PostId'] : '';
				$selectFeed = !empty($loadData['selectFeed']) ? $loadData['selectFeed'] : '';
				$Massage = !empty($loadData['Massage']) ? $loadData['Massage'] : '';
				$Description = !empty($loadData['Description']) ? $loadData['Description'] : '';
				$Type = !empty($loadData['Type']) ? $loadData['Type'] : '';
				$PostLink = !empty($loadData['PostLink']) ? $loadData['PostLink'] : '';
				$CreatedTime = !empty($loadData['CreatedTime']) ? $loadData['CreatedTime'] : '';
				$PostImage = !empty($loadData['PostImage']) ? $loadData['PostImage'] : '';
				$UserName = !empty($loadData['UserName']) ? $loadData['UserName'] : '';
				$UserImage = !empty($loadData['UserImage']) ? $loadData['UserImage'] : '';
				$UserLink = !empty($loadData['UserLink']) ? $loadData['UserLink'] : '';
				$socialIcon = !empty($loadData['socialIcon']) ? $loadData['socialIcon'] : '';
				$CategoryText = !empty($loadData['FilterCategory']) ? $loadData['FilterCategory'] : '';
				$ErrorClass = !empty($loadData['ErrorClass']) ? $loadData['ErrorClass'] : '';
				$EmbedURL = !empty($loadData['Embed']) ? $loadData['Embed'] : '';
				$EmbedType = !empty($loadData['EmbedType']) ? $loadData['EmbedType'] : '';
				
				$category_filter = $loop_category = '';
				if( !empty($CategoryText)  && $layout !='carousel' ){
					$loop_category = explode(',', $CategoryText);
					foreach( $loop_category as $category ) {
						$category = preg_replace('/[^A-Za-z0-9-]+/', '-', $category);
						$category_filter .=' '.esc_attr($category).' ';
					}
				}

				if($selectFeed == 'Facebook'){
					$Fblikes = !empty($loadData['FbLikes']) ? $loadData['FbLikes'] : 0;
					$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
					$share = !empty($loadData['share']) ? $loadData['share'] : 0;
					$likeImg = TPGB_ASSETS_URL.'assets/images/social-feed/like.png';
					$ReactionImg = TPGB_ASSETS_URL.'assets/images/social-feed/love.png';
				}
				if($selectFeed == 'Twitter'){
					$TwRT = (!empty($loadData['TWRetweet'])) ? $loadData['TWRetweet'] : 0;
					$TWLike = (!empty($loadData['TWLike'])) ? $loadData['TWLike'] : 0;
					
					$TwReplyURL = (!empty($loadData['TwReplyURL'])) ? $loadData['TwReplyURL'] : '';
					$TwRetweetURL = (!empty($loadData['TwRetweetURL'])) ? $loadData['TwRetweetURL'] : '';
					$TwlikeURL = (!empty($loadData['TwlikeURL'])) ? $loadData['TwlikeURL'] : '';
					$TwtweetURL = (!empty($loadData['TwtweetURL'])) ? $loadData['TwtweetURL'] : '';
				}
				if($selectFeed == 'Vimeo'){
					$share = (!empty($loadData['share'])) ? $loadData['share'] : 0;
					$likes = (!empty($loadData['likes'])) ? $loadData['likes'] : 0;
					$comment = (!empty($loadData['comment'])) ? $loadData['comment'] : 0;
				}
				if($selectFeed == 'Youtube'){
					$view = (!empty($loadData['view'])) ? $loadData['view'] : 0;
					$likes = (!empty($loadData['likes'])) ? $loadData['likes'] : 0;
					$comment = (!empty($loadData['comment'])) ? $loadData['comment'] : 0;
					$Dislike = (!empty($loadData['Dislike'])) ? $loadData['Dislike'] : 0;
				}
				if( $Type == 'video' || $Type == 'photo' && $selectFeed != 'Instagram'){
					$videoURL = $PostLink;
					$ImageURL = $PostImage;
				}

				$IGGP_Icon='';
				if($selectFeed == 'Instagram'){
					$IGGP_Type = !empty($loadData['IG_Type']) ? $loadData['IG_Type'] : 'Instagram_Basic';

					if($IGGP_Type == 'Instagram_Graph'){
						$IGGP_Icon = !empty($loadData['IGGP_Icon']) ? $loadData['IGGP_Icon'] : '';
						$likes = !empty($loadData['likes']) ? $loadData['likes']: 0;
						$comment = !empty($loadData['comment']) ? $loadData['comment'] : 0;
						$videoURL = $PostLink;
						$PostLink = !empty($loadData['IGGP_PostLink']) ? $loadData['IGGP_PostLink'] : '';
						$ImageURL = $PostImage;

						$IGGP_CAROUSEL = !empty($loadData['IGGP_CAROUSEL']) ? $loadData['IGGP_CAROUSEL'] : '';
						if( $Type == "CAROUSEL_ALBUM" && $FancyStyle == 'default' ){
							$FancyBoxJS = 'data-fancybox="IGGP-CAROUSEL-'.esc_attr($F_index).'-'.esc_attr($block_id).'-'.esc_attr($uniqEach).'"';
						}else{
							$FancyBoxJS = 'data-fancybox="'.esc_attr($block_id).'"';
						}
					}else if($IGGP_Type == 'Instagram_Basic'){
						$videoURL = $PostLink;
						$ImageURL = $PostImage;
					}
				}
				if(!empty($FbAlbum)){
					$PostLink = !empty($PostLink[0]['link']) ? $PostLink[0]['link'] : 0;
				}
				
				if( ($F_index < $TotalPost) && ( ($MediaFilter == 'default') || ($MediaFilter == 'ompost' && !empty($PostLink) && !empty($PostImage)) || ($MediaFilter == 'hmcontent' &&  empty($PostLink) && empty($PostImage) )) ){
					echo '<div class="grid-item splide__slide '.esc_attr('feed-'.$selectFeed.' '.$desktop_class.' '.$RKey.' '.$category_filter).'" data-index="'.esc_attr($selectFeed.$F_index).'">';				
						if(!empty($style)){
							include TPGBP_INCLUDES_URL. 'social-feed/'.sanitize_file_name('social-feed-'.$style.'.php');
						}
					echo '</div>';
				}
			}
		}
		
		$GridData = ob_get_clean();

		$result['success'] = 1;
		$result['totalFeed'] = isset($load_attr['totalFeed']) ? wp_unslash($load_attr['totalFeed']) : '';
		$result['FilterStyle'] = isset($load_attr['FilterStyle']) ? wp_unslash($load_attr['FilterStyle']) : '';
		$result['allposttext'] = isset($load_attr['allposttext']) ? wp_unslash($load_attr['allposttext']) : '';
		$result['HTMLContent'] = $GridData;
		
		return wp_send_json($result);
		exit();
	}
	
	/*
	 * Remove Cache Transient Data
	 * @since 1.3.0
	 */
	public function Tp_delete_transient() {
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
	 * Get Pages List Login Register block use
	 * @since 1.3.0
	 */
	public function tpgb_get_page_list(){
        $tppage = get_pages();
        $getpageList = [];
        foreach($tppage as $page){
            $pageArr = (array) $page;
            $getpageList[] = [ 'value' => $pageArr['ID'] , 'label' => $pageArr['post_title'] ];
        }
        return $getpageList;
    }
	
	/*
	 * Subscribe Mailchimp Message
	 * @since 1.3.0
	 */
	public static function tpgb_mailchimp_subscriber_message( $email, $status, $list_id, $api_key, $merge_fields = array(),$mc_group_ids ='', $mc_tags_ids = '' ){
		$data = array(
			'apikey'        => $api_key,
			'email_address' => $email,
			'status'        => $status,
		);
		
		if(!empty($merge_fields)){
			$data['merge_fields'] = $merge_fields;
		}
		$mc_group_ids = !empty($mc_group_ids) ? sanitize_text_field( $mc_group_ids ) : '';
		if(!empty($mc_group_ids)){
			$interests = explode( ' | ', trim( $mc_group_ids ) );
			$interests=array_flip($interests);

			foreach($interests as $key => $value){
				$data['interests'][$key] = true;
			}
		}
		$mc_tags_ids = !empty($mc_tags_ids) ? sanitize_text_field( $mc_tags_ids ) : '';
		if(!empty($mc_tags_ids)){
			$data['tags'] = explode( '|', trim($mc_tags_ids) );
		}
		
		$mch_api = curl_init();
	 
		curl_setopt($mch_api, CURLOPT_URL, 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($data['email_address'])));
		curl_setopt($mch_api, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.base64_encode( 'user:'.$api_key )));
		curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true); // return the API response
		curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, 'PUT'); // method PUT
		curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
		curl_setopt($mch_api, CURLOPT_POST, true);
		curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
	 
		$result = curl_exec($mch_api);
		return $result;
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
	 * Flex child css
	 * @since 1.4.0
	 */
	public static function tpgbp_flex_child_css($flexChild  , $selector){
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

	/**
	 * Get User Role List
	 * @since 1.4.0
	 */
	public static function tpgbp_get_user_role(){
		
		global $wp_roles;

		$useroleli = $wp_roles->get_names();

		$tpgbrole = array();
		foreach ( $useroleli as $key => $role_list ) {
			$tpgbrole[] = [ $key , $role_list];
		}
	
		return $tpgbrole;
	}

	/**
	 * Get Class For Metro Layout
	 * @since 1.4.0
	 */
	public static function tpgbp_metro_class($col='1',$metroCol='3',$metrosty='style-1'){
		$i=($col!='') ? $col : 1;
		if(!empty($metroCol)){
			
			//style-3
			if($metroCol=='3' && $metrosty=='style-1'){
				$i=($i<=10) ? $i : ($i%10);			
			}
			if($metroCol=='3' && $metrosty=='style-2'){
				$i=($i<=9) ? $i : ($i%9);			
			}
			if($metroCol=='3' && $metrosty=='style-3'){
				$i=($i<=15) ? $i : ($i%15);			
			}
			if($metroCol=='3' && $metrosty=='style-4'){
				$i=($i<=8) ? $i : ($i%8);			
			}
			//style-4
			if($metroCol=='4' && $metrosty=='style-1'){
				$i=($i<=12) ? $i : ($i%12);			
			}
			if($metroCol=='4' && $metrosty=='style-2'){
				$i=($i<=14) ? $i : ($i%14);			
			}
			if($metroCol=='4' && $metrosty=='style-3'){
				$i=($i<=12) ? $i : ($i%12);			
			}
			//style-5
			if($metroCol=='5'){
				$i=($i<=18) ? $i : ($i%18);			
			}
			//style-6
			if($metroCol=='6'){
				$i=($i<=16) ? $i : ($i%16);			
			}
		}
		return $i;
	}
	
	/**
	 * Equal Height Attribute Function 
	 * @since 1.4.0
	 */
	public static function global_equal_height( $attr ){
		$equalHeight = (!empty($attr['tpgbEqualHeight'])) ? $attr['tpgbEqualHeight'] : false;
		$equalUnqClass = (!empty($attr['equalUnqClass'])) ? $attr['equalUnqClass'] : '';

		$eqlOpt = ''; $equalHeightAttr = '';
		if(!empty($equalHeight)){
			$eqlOpt = esc_attr($equalUnqClass);
			$equalHeightAttr .= ' data-tpgb-equal-height="'.esc_attr($eqlOpt).'"';
		}

		return $equalHeightAttr;
	}

	/**
	 * Get Current User Data For Backend Editor 
	 * @since 2.0.0
	 */
	public static function tpgbp_get_current_user (){
		if( is_user_logged_in()){
			$curtUser = wp_get_current_user();
			$user_info = get_user_meta($curtUser->ID);
			return $user_info;
		}
	}
}

Tpgbp_Pro_Blocks_Helper::get_instance();