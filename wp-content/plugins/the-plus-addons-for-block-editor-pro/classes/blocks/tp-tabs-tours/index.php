<?php
/* Block : Tabs And Tours
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_tabs_tours_render_callback( $attributes, $content) {
	
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] :'';
	$tabLayout =  (!empty($attributes['tabLayout'])) ? $attributes['tabLayout'] :'horizontal';
	$activeTab = (!empty($attributes['activeTab'])) ? $attributes['activeTab'] :'1';
	$onhoverTab = (!empty($attributes['onhoverTab'])) ? $attributes['onhoverTab'] :'';
	$carouselId = (!empty($attributes['carouselId'])) ? $attributes['carouselId'] : '';
	$swiperEffect = (!empty($attributes['swiperEffect'])) ? $attributes['swiperEffect'] :false;
	$navAlign =  (!empty($attributes['navAlign'])) ? $attributes['navAlign'] :'text-center';
	$fullwidthIcon = (!empty($attributes['fullwidthIcon'])) ? $attributes['fullwidthIcon'] :false;
	$hide_modile =  (!empty($attributes['hide_modile'])) ? $attributes['hide_modile'] :false;
	$navWidth =  (!empty($attributes['navWidth'])) ? $attributes['navWidth'] :false;
	$underline = (!empty($attributes['underline'])) ? $attributes['underline'] :false;
	$tablistRepeater = (!empty($attributes['tablistRepeater'])) ? $attributes['tablistRepeater'] : [];
	$titleShow =  (!empty($attributes['titleShow'])) ? $attributes['titleShow'] : false;
	$navPosition = (!empty($attributes['navPosition'])) ? $attributes['navPosition'] :'top' ;
	$tabnavResp =  (!empty($attributes['tabnavResp'])) ? $attributes['tabnavResp'] :'';
	$hideToggle = (!empty($attributes['hideToggle'])) ? $attributes['hideToggle'] :false;
	$VerticalAlign = (!empty($attributes['VerticalAlign'])) ? $attributes['VerticalAlign'] :'';
	
	$tabType = (!empty($attributes['tabType'])) ? $attributes['tabType'] : '' ;

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$output = '';
	$tab_nav = '';
	$tab_content = '';

	// Set Swiper Effect
	$swiper_container = '';
	$swiper_wrap = '';
	$swiper_slide = '';
	if($swiperEffect == true && $tabLayout == 'horizontal'){
		$swiper_container = 'swiper-container swiper-free-mode';
		$swiper_wrap = 'swiper-wrapper';
		$swiper_slide = 'swiper-slide swiper-slide-active';	
	}

	// Set Full Width Icon Class
	$full_icon_class = '';
	if($fullwidthIcon == true){
		$full_icon_class = 'full-width-icon';
	}else{
		$full_icon_class = 'normal-width-icon';
	}

	// set class to hide Outer icon on mobile view
	$hide_modile = '';
	if(!empty($hideToggle['md']) && $hideToggle['md'] == true){
		$hide_modile .= ' desc-hide';
	}if( !empty($hideToggle['sm']) && $hideToggle['sm'] == true ){
		$hide_modile .= ' tablet-hide';
	}if(!empty($hideToggle['sm']) && $hideToggle['xs'] == true){
		$hide_modile .= ' mobile-hide';
	}

	//Set class For full width Nav bar
	$full_width_nav = '';
	if($navWidth == true){
		$full_width_nav = 'full-width';
	}

	// set class For UnderLine
	$underline_class = '';
	if($underline == true){
		$underline_class = 'tab-underline';
	}

	//Set responsive class
	$responsive_class = '';
	if($tabnavResp == 'nav_full'){
		$responsive_class = 'nav-full-width';
	}else if($tabnavResp == 'nav_one'){
		$responsive_class = "nav-one-by-one";
	}else if($tabnavResp == 'tab_accordion'){
		$responsive_class = 'mobile-accordion';
	}

	//Set Vertival TabAlign class
	$alignclass = '';
	if($VerticalAlign == 'top'){
		$alignclass = 'align-top';
	}else if($VerticalAlign == 'center'){
		$alignclass = "align-center";
	}else if($VerticalAlign == 'bottom'){
		$alignclass = "align-bottom";
	}
	$i=0;$j=0;

	$dataAttr = '';
	if(!empty($carouselId)){
		$dataAttr .= ' id="tptab_'.esc_attr($carouselId).'"';
		$dataAttr .= ' data-tabs-id="tptab_'.esc_attr($carouselId).'"';
		$dataAttr .= ' data-connection="tpca-'.esc_attr($carouselId).'"';
		$dataAttr .= ' data-extra-conn="tpex-'.esc_attr($carouselId).'"';
	}

	// Output for Tab Navigation
	$nav_loop='';
	if(!empty($tablistRepeater)){ 
		foreach ( $tablistRepeater as $index => $item ) :
			$j++;
			// Set active class
			$active='';
			if($j==$activeTab){
				$active=' active';
			}

			$nav_loop .= '<div class="tpgb-tab-li">';
				$nav_loop .= '<div id="'.(!empty($item['uniqueId']) ? esc_attr($item['uniqueId']) : 'tpag-tab-title-'.esc_attr($block_id).esc_attr($j) ).'" class="tpgb-tab-header tpgb-trans-linear '.esc_attr($active).'" data-tab="'.esc_attr($j).'" role="tab" aria-controls="tpag-tab-content-'.esc_attr($block_id).esc_attr($j).'">';
					if(!empty($item['innerIcon'])){
						$nav_loop .= '<span class="tab-icon-wrap">';
							if($item['iconFonts'] == 'font_awesome') {
								$nav_loop .= '<i class="tab-icon tpgb-trans-linear '.esc_attr($item['innericonName']).'"> </i>';
							}else if($item['iconFonts'] == 'image'){
								if( !empty($item['iconImage']['id']) ){
									$nav_loop .= wp_get_attachment_image($item['iconImage']['id'],$item['iconimageSize']);
								}else if(isset($item['iconImage']['dynamic'])){
									$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['iconImage']);
									$nav_loop .= '<img src="'.esc_url($imgUrl).'" />';
								}
							} 
						$nav_loop .= '</span>';
					}
					if(!empty($titleShow)){
						$nav_loop .= '<span>' .wp_kses_post($item['tabTitle']). '</span>';
					}
					if(!empty($item['outerIcon'])){
						$nav_loop .= '<div class="tab-sep-icon">';
							$nav_loop .= '<i class="tab-between-icon '.(!empty($item['outericonName']) ? esc_attr($item['outericonName']) : '' ).'"> </i>';
						$nav_loop .= '</div>';
					}
				$nav_loop .= '</div>';
			$nav_loop .= '</div>';
			
		endforeach;
	}
	$tab_nav .= '<div class="tpgb-tabs-nav-wrapper '.esc_attr($swiper_wrap).' '.esc_attr($navAlign).' '.($tabLayout=='vertical' ? esc_attr($alignclass) : '').' ">';
		$tab_nav .= '<div class="tpgb-tabs-nav tpgb-trans-linear '.esc_attr($swiper_slide).' '.esc_attr($full_icon_class).' '.esc_attr($hide_modile).' '.esc_attr($full_width_nav).' '.esc_attr($underline_class).' " role="tablist">';
		$tab_nav .= $nav_loop;
		$tab_nav .= '</div>';
	$tab_nav .= '</div>';
	
	//Output tab content
	$content_loop = '';
	if(!empty($tablistRepeater)){ 
		if($tabType == 'editor' ){

			foreach ( $tablistRepeater as $index => $item ) :
				$i++;
			
				// Set active class
				$active='';
				if($i==$activeTab){
					$active=' active';
				}

				// Set Tab Title For responsive accordian
				$content_loop .= '<div class="tab-mobile-title '.esc_attr($active).' '.esc_attr($navAlign).'" data-tab="'.esc_attr($i).'">';
					if(!empty($item['innerIcon'])){
						$content_loop .= '<span class="tab-icon-wrap">';
							if($item['iconFonts'] == 'font_awesome') {
									$content_loop .= '<i class="tab-icon tpgb-trans-linear '.esc_attr($item['innericonName']).'"> </i>';
								}else if($item['iconFonts'] == 'image'){
									if(!empty($item['iconImage']['id'])){
										$content_loop .= wp_get_attachment_image($item['iconImage']['id'],$item['iconimageSize']);
									}else if(isset($item['iconImage']['dynamic'])){
										$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['iconImage']);
										$content_loop .= '<img src="'.esc_url($imgUrl).'" />';
									}
								}
						$content_loop .= '</span>';
					}
					$content_loop .= '<span>'.wp_kses_post($item['tabTitle']).'</span>';
				$content_loop .= '</div>';
			endforeach;
			$content_loop .= $content;

		}else{
			foreach ( $tablistRepeater as $index => $item ) :
				$i++;
			
				// Set active class
				$active='';
				if($i==$activeTab){
					$active=' active';
				}

				// Set Tab Title For responsive accordian
				$content_loop .= '<div class="tab-mobile-title '.esc_attr($active).' '.esc_attr($navAlign).'" data-tab="'.esc_attr($i).'">';
					if(!empty($item['innerIcon'])){
						$content_loop .= '<span class="tab-icon-wrap">';
							if($item['iconFonts'] == 'font_awesome') {
									$content_loop .= '<i class="tab-icon tpgb-trans-linear '.esc_attr($item['innericonName']).'"> </i>';
								}else if($item['iconFonts'] == 'image'){
									if(!empty($item['iconImage']['id'])){
										$content_loop .= wp_get_attachment_image($item['iconImage']['id'],$item['iconimageSize']);
									}else if(isset($item['iconImage']['dynamic'])){
										$imgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['iconImage']);
										$content_loop .= '<img src="'.esc_url($imgUrl).'" />';
									}
								}
						$content_loop .= '</span>';
					}
					$content_loop .= '<span>'.wp_kses_post($item['tabTitle']).'</span>';
				$content_loop .= '</div>';

				$content_loop .= '<div id="tpag-tab-content-'.esc_attr($block_id).esc_attr($i).'" class="tpgb-tab-content '.esc_attr($active).'" data-tab="'.esc_attr($i).'"  role="tabpanel" aria-labelledby="'.(!empty($item['UniqueId']) ? esc_attr($item['UniqueId']) : 'tpag-tab-title-'.esc_attr($block_id).esc_attr($i) ).'">';
					$content_loop .= '<div class ="tpgb-content-editor" >';
						if( !empty($item['contentType']) && $item['contentType'] == 'content'){
							$content_loop .= Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['tabDescription']);
						}else if($item['contentType'] == 'template' && !empty($item['blockTemp'])  && $item['blockTemp']!='none'){
							ob_start();
							if(!empty($item['blockTemp'])) {
								echo Tpgb_Library()->plus_do_block($item['blockTemp']);
							}
							$content_loop .= ob_get_contents();
							ob_end_clean();
						}
					$content_loop .= '</div>';
				$content_loop .= '</div>';
				
			endforeach;
		}
	}

	$tab_content .= '<div class="tpgb-tabs-content-wrapper tpgb-trans-linear">' .$content_loop. '</div>';
	
	$output .= '<div class="tpgb-tabs-tours tpgb-block-'.esc_attr($block_id).'  tab-view-'.esc_attr($tabLayout).' '.esc_attr($blockClass).' ">';
		$output .= '<div class="tpgb-tabs-wrapper tpgb-relative-block tpex-'.(!empty($carouselId) ? esc_attr($carouselId)  : '' ).' '.esc_attr($swiper_container).'  '.esc_attr($responsive_class).' "    data-tab-default="'.esc_attr($activeTab).'" data-tab-hover="'.($onhoverTab == 'click' ? 'no' : 'yes').'" '.$dataAttr.' >';
			if($navPosition == 'top' || $navPosition == 'left'  ){
				$output .= $tab_nav.$tab_content;
			}else{
				$output .= $tab_content.$tab_nav;
			}
		$output .= '</div>';
	$output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_tabs_tours() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
 	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'tabType' => [
				'type' => 'string',
				'default' => '',
			],
			'tablistRepeater' => [
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'tabTitle' => [
							'type' => 'string',
							'default' => 'Tab',	
						],
						'contentType' => [
							'type' => 'string',
							'default' => 'content',	
						],
						'blockTemp' => [
							'type' => 'string',
							'default' => '',	
						],
						'backendVisi' => [
							'type' => 'boolean',
							'default' => true,	
						],
						'tabDescription' => [
							'type' => 'string',
							'default' => 'This is just dummy content. Put your relevant content over here. We want to remind you, smile and passion are contagious, be a carrier.',	
						],
						'innerIcon'  => [
							'type' => 'boolean',
							'default' => false,	
						],
						'iconFonts' => [
							'type' => 'string',
							'default' => 'font_awesome',	
						],
						'iconImage' => [
							'type' => 'object',
							'default'=> [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
							],	
						],
						'iconimageSize' => [
							'type' => 'string',
							'default' => 'full',	
						],
						'outerIcon'  => [
							'type' => 'boolean',
							'default' => false,	
						],
						'innericonName' => [
							'type'=> 'string',
							'default'=> 'fas fa-home',
						],
						'outericonName' => [
							'type'=> 'string',
							'default'=> 'fas fa-home',
						],
						'uniqueId' => [
							'type'=> 'string',
							'default'=> '',
						],
					],
				], 
				'default' => [
					[
						"_key" => '0',
						"tabTitle" => 'Tab 1',
						"tabDescription" => "This is just dummy content. Put your relevant content over here. We want to remind you, smile and passion are contagious, be a carrier.",
						'iconImage' => [
							'url' => '',
						],
						'iconimageSize' => 'full',
						'contentType' => 'content',
						'iconFonts' => 'font_awesome',
						'innericonName' => 'fas fa-home',
						'outericonName' => 'fas fa-home'
					],
					[
						"_key" => '1',
						"tabTitle" => 'Tab 2',
						"tabDescription" => "Enter your relevant content over here. This is just dummy content.  We want to remind you, smile and passion are contagious, be a carrier.",
						'iconImage' => [
							'url' => '',
						],
						'iconimageSize' => 'full',
						'contentType' => 'content',
						'iconFonts' => 'font_awesome',
						'innericonName' => 'fas fa-home',
						'outericonName' => 'fas fa-home'
					],
				],
			],
			'tabLayout' => [
				'type' => 'string',
				'default' => 'horizontal',	
			],
			'navPosition' => [
				'type' => 'string',
				'default' => 'top',
			],
			'swiperEffect' => [
				'type' => 'boolean',
				'default' => false,
			],
			'activeTab' => [
				'type' => 'string',
				'default' => '1',
			],
			'onhoverTab' => [
				'type' => 'string',
				'default' => 'click',
			],
			'carouselId'  => [
				'type' => 'string',
				'default' => '',
			],
			'iconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-icon-wrap { font-size: {{iconSize}};}{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header  .tab-icon-wrap img { max-width: {{iconSize}};}{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title .tab-icon-wrap{ font-size: {{iconSize}}; }{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title .tab-icon-wrap img{ max-width: {{iconSize}};}',
					],
				],
				'scopy' => true,
			],
			'iconwidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-icon{ width:{{iconwidth}}; height:{{iconwidth}}; line-height: {{iconwidth}}; }',
					],
				],
				'scopy' => true,
			],
			'iconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header  .tab-icon-wrap {color: {{iconColor}};}{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title .tab-icon-wrap{color: {{iconColor}};}',
					],
				],
				'scopy' => true,
			],
			'iniconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-icon',
					],
				],
				'scopy' => true,
			],
			'iconBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-icon ',
					],
				],
				'scopy' => true,
			],
			'iconshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-icon ',
					],
				],
				'scopy' => true,
			],
			'iconBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header .tab-icon{border-radius : {{iconBradius}} }',
					],
				],
				'scopy' => true,
			],
			'iconActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header.active .tab-icon ,{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header:hover .tab-icon { color: {{iconActcolor}}; }{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title.active .tab-icon-wrap{ color: {{iconActcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'HviniconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header.active .tab-icon ,{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header:hover .tab-icon ',
					],
				],
				'scopy' => true,
			],
			'iconActBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header.active .tab-icon ,{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header:hover .tab-icon',
					],
				],
				'scopy' => true,
			],
			'iconHshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header.active .tab-icon ,{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header:hover .tab-icon ',
					],
				],
				'scopy' => true,
			],
			'iconHBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header.active .tab-icon ,{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header:hover .tab-icon{border-radius : {{iconHBradius}} }',
					],
				],
				'scopy' => true,
			],
			'iconSpacing' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'fullwidthIcon', 'relation' => '==', 'value' => false]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav:not(.full-width-icon) .tpgb-tab-header .tab-icon-wrap{ padding-right: {{iconSpacing}}; }{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title .tab-icon-wrap{ padding-right: {{iconSpacing}}; }',
						
						
					],
					(object) [
						'condition' => [(object) ['key' => 'fullwidthIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav.full-width-icon .tpgb-tab-header .tab-icon-wrap{ padding-right: 0px ; padding-bottom: {{iconSpacing}}; }',
					],
				],
				'scopy' => true,
			],
			'fullwidthIcon' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'outiconSize' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-sep-icon { font-size: {{outiconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'outiconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header .tab-sep-icon { color: {{outiconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'outiconActColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header.active .tab-sep-icon,{{PLUS_WRAP}} .tpgb-tabs-nav-wrapper .tpgb-tab-header:hover .tab-sep-icon { color: {{outiconActColor}}; }',
					],
				],
				'scopy' => true,
			],
			'outiconSpa' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header .tab-sep-icon{ padding-left: {{outiconSpa}}; } {{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header .tab-sep-icon{ padding-right: {{outiconSpa}}; }',
					],
				],
				'scopy' => true,
			],
			'hideToggle' => [
				'type' => 'object',
				'default' => [ 'md' => false ],
				'scopy' => true,
			],
			'vernavWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'tabLayout', 'relation' => '==', 'value' => 'vertical']],
						'selector' => '{{PLUS_WRAP}}.tab-view-vertical  .tpgb-tabs-nav-wrapper{ width: {{vernavWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'VerticalAlign' => [
				'type' => 'string',
				'default' => 'center',
				'scopy' => true,
			],
			'titleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header,{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title',
					]
				],
				'scopy' => true,
			],
			'navAlign' => [
				'type' => 'string',
				'default' => 'text-center',
				'scopy' => true,
			],
			
			'navWidth' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'titleShow' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'navequalwidth' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'navwidthSize'  => [
				'type' => 'string',
				'default' => [ 
					'md' => 90,
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'navequalwidth', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header{ max-width: {{navwidthSize}}; }{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-li, {{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav{ display: block; }',
					],
				],
				'scopy' => true,
			],
			'titleColor' => [
				'type' => 'string',
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header{ color: {{titleColor}}; }{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title{color: {{titleColor}};}',
						
					],
				],
				'scopy' => true,
			],
			'titleActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header.active,{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header:hover{ color: {{titleActcolor}}; }{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title.active{color: {{titleActcolor}};}',
					]
				],
				'scopy' => true,
			],
			'underline' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'ulineColor' => [
				'type' => 'string',
				'default' => '',
				'style' =>[
					(object) [
						'condition' => [(object) ['key' => 'underline', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav.tab-underline .tpgb-tab-header.active:before{ background: linear-gradient(to right,#fff0 0%,{{ulineColor}}  50%,#fff0 100%); }',						
					],
				],
				'scopy' => true,
			],
			'lineMargin' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'condition' => [(object) ['key' => 'underline', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav.tab-underline .tpgb-tab-header.active:before, {{PLUS_WRAP}}  .tpgb-tabs-nav.tab-underline:before{ margin-top : {{lineMargin}} }',
						
					],
				],
				'scopy' => true,
			],
			'lineWidth' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'condition' => [(object) ['key' => 'underline', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav.tab-underline .tpgb-tab-header.active:before{ width: {{lineWidth}}; }',
						
					],
				],
				'scopy' => true,
			],
			'lineHeight' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'condition' => [(object) ['key' => 'underline', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav.tab-underline .tpgb-tab-header.active:before,{{PLUS_WRAP}}  .tpgb-tabs-nav.tab-underline:before{ height: {{lineHeight}}; }',
						
					],
				],
				'scopy' => true,
			],
			'tabMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header{ margin : {{tabMargin}};}{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title{margin : {{tabMargin}};}',
						
					],
				],
				'scopy' => true,
			],
			'tabPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => 15,'bottom' => 15, 'left'=> 15,'right' => 15],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header{ padding : {{tabPadding}}}{{PLUS_WRAP}} .mobile-accordion .tab-mobile-title{padding : {{tabPadding}};}',
						
					],
				],
				'scopy' => true,
			],
			'navSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'tabLayout', 'relation' => '==', 'value' => 'horizontal']],
						'selector' => '{{PLUS_WRAP}}.tab-view-horizontal .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header{ margin-left: {{navSpace}}; } {{PLUS_WRAP}}.tab-view-horizontal .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-li:first-child .tpgb-tab-header{ margin-left: 0 ; } {{PLUS_WRAP}}.tab-view-horizontal .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-li:last-child .tpgb-tab-header{ margin-right: 0; }',
						
						
					],
					(object) [
						'condition' => [(object) ['key' => 'tabLayout', 'relation' => '==', 'value' => 'vertical']],
						'selector' => '{{PLUS_WRAP}}.tab-view-vertical .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header{ margin-top: {{navSpace}}; } {{PLUS_WRAP}}.tab-view-vertical .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-li:first-child .tpgb-tab-header{ margin-top: 0 ; } {{PLUS_WRAP}}.tab-view-vertical .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-li:last-child .tpgb-tab-header{ margin-bottom: 0; }',
					],
				],
				'scopy' => true,
			],
			'tabBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header',
					],
				],
				'scopy' => true,
			],
			'normalBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header{border-radius : {{normalBradius}} }',
						
					],
				],
				'scopy' => true,
			],
			'tabActborder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header.active',
					],
				],
				'scopy' => true,
			],
			'actBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-nav .tpgb-tab-header.active{border-radius : {{actBradius}} }',
					],
				],
				'scopy' => true,
			],
			'tabbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header',
					],
				],
				'scopy' => true,
			],
			'acttabBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header.active , {{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header:hover',
					],
				],
				'scopy' => true,
			],
			'tabNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header',
					],
				],
				'scopy' => true,
			],
			'tabActBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header.active , {{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav .tpgb-tab-header:hover',
					],
				],
				'scopy' => true,
			],
			'navbarMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav{ margin: {{navbarMargin}} }',
					],
				],
				'scopy' => true,
			],
			'navbarPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav{ padding: {{navbarPadding}} }',
					],
				],
				'scopy' => true,
			],
			'navBoder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav',
					],
				],
				'scopy' => true,
			],
			'navNBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav{border-radius : {{navNBradius}} }',
						
					],
				],
				'scopy' => true,
			],
			'navhvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav:hover',
					],
				],
				'scopy' => true,
			],
			'navhvrBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav:hover{border-radius : {{navhvrBradius}} }',
					],
				],
				'scopy' => true,
			],
			'navbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav-wrapper .tpgb-tabs-nav',
						
					],
				],
				'scopy' => true,
			],
			'navhvrBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav-wrapper .tpgb-tabs-nav:hover',
					],
				],
				'scopy' => true,
			],
			'navNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav-wrapper .tpgb-tabs-nav',
					],
				],
				'scopy' => true,
			],
			'navhvrBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-nav-wrapper .tpgb-tabs-nav:hover',
					],
				],
				'scopy' => true,
			],
			'descTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper .tpgb-tab-content .tpgb-content-editor',
					],
				],
				'scopy' => true,
			],
			'descColor' => [
				'type' => 'string',
				'default' => '',
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper .tpgb-tab-content .tpgb-content-editor,{{PLUS_WRAP}} .tpgb-tabs-content-wrapper .tpgb-tab-content{color: {{descColor}}}',
					],
				],
				'scopy' => true,
			],
			'descMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper{ margin : {{descMargin}}}',
					],
				],
				'scopy' => true,
			],
			'descPadding' => [	
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper{ padding : {{descPadding}}}',
					],
				],
				'scopy' => true,
			],
			'descBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper',
					],
				],
				'scopy' => true,
			],
			'descBRedius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' =>(object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper{border-radius : {{descBRedius}} }',
					],
				],
				'scopy' => true,
			],
			'descbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper',
					],
				],
				'scopy' => true,
			],
			'descboxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tabs-content-wrapper',
					],
				],
				'scopy' => true,
			],
			'navOpacity' => [
				'type' => 'string',
				'default' => 1,
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tab-header{ opacity : {{navOpacity}} }',
					],
				],
				'scopy' => true,
			],
			'navZoom' => [
				'type' => 'string',
				'default' => 1,
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tab-header{ -webkit-transform : scale({{navZoom}});-moz-transform:scale({{navZoom}});-ms-transform:scale({{navZoom}});-o-transform:scale({{navZoom}});transform:scale({{navZoom}}) }',
					],
				],
				'scopy' => true,
			],
			'ActnavOpacity' => [
				'type' => 'string',
				'default' => 1,
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tab-header.active{ opacity : {{ActnavOpacity}} }',
					],
				],
				'scopy' => true,
			],
			'activenavZoom' => [
				'type' => 'string',
				'default' => 1,
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper .tpgb-tab-header.active{ -webkit-transform : scale({{activenavZoom}});-moz-transform:scale({{activenavZoom}});-ms-transform:scale({{activenavZoom}});-o-transform:scale({{activenavZoom}});transform:scale({{activenavZoom}}) }',
					],
				],
				'scopy' => true,
			],
			'tabnavResp' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'navBtnSpace' => [
				'type' => 'string',
				'default' => [ 
					'md' => 1,
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}}.tab-view-horizontal .tpgb-tabs-wrapper.nav-one-by-one .tpgb-tabs-nav .tpgb-tab-header{ margin-top :{{navBtnSpace}} } {{PLUS_WRAP}}.tab-view-horizontal .tpgb-tabs-wrapper.nav-one-by-one .tpgb-tabs-nav .tpgb-tab-li:first-child .tpgb-tab-header{ margin-top : 0 }  {{PLUS_WRAP}}.tab-view-horizontal.tpgb-tabs-wrapper.nav-one-by-one .tpgb-tabs-nav .tpgb-tab-li:last-child .tpgb-tab-header{ margin-bottom : 0 }',
					],
				],
				'scopy' => true,
			],
			'accorBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title',
					],
				],
				'scopy' => true,
			],
			'accorBredius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title{ border-radius : {{accorBredius}} }',
					],
				],
				'scopy' => true,
			],
			'ActaccorBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title.active',
					],
				],
				'scopy' => true,
			],
			'accorBActredius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => (object) ['top' => '','bottom' => '', 'left'=> '','right' => ''],
					"unit" => 'px',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title.active{ border-radius :{{accorBActredius}} }',
					],
				],
				'scopy' => true,
			],
			'accorbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title',
					],
				],
				'scopy' => true,
			],
			'ActaccorBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title.active',
					],
				],
				'scopy' => true,
			],
			'accorboxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title',
					],
				],
				'scopy' => true,
			],
			'ActaccorBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 4,
					'blur' => 8,
					'spread' => 0,
					'color' => "rgba(0,0,0,0.40)",
				],
				'style' =>[
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-tabs-wrapper.mobile-accordion .tab-mobile-title.active',
					],
				],
				'scopy' => true,
			],
		];
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
		register_block_type( 'tpgb/tp-tabs-tours', array(
			'attributes' => $attributesOptions,
			'editor_script' => 'tpgb-block-editor-js',
			'editor_style'  => 'tpgb-block-editor-css',
			'render_callback' => 'tpgb_tp_tabs_tours_render_callback'
    	) );
}
add_action( 'init', 'tpgb_tp_tabs_tours' );