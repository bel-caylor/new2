<?php
/* Block : Tp Popup Builder
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_popup_builder_callback( $settings, $content) {
	
    $block_id	= isset($settings['block_id']) ? $settings['block_id'] : '';
	$cntType = (!empty($settings['cntType'])) ? $settings['cntType'] :'template';
	$popupCnt = (!empty($settings['popupCnt'])) ? $settings['popupCnt'] :'';
    $shortCodeCnt = (!empty($settings['shortCodeCnt'])) ? $settings['shortCodeCnt'] :'';
    $popupDir = (!empty($settings['popupDir'])) ? $settings['popupDir'] :'';
    $inAnimation = (!empty($settings['inAnimation'])) ? $settings['inAnimation'] :'';
    $outAnimation = (!empty($settings['outAnimation'])) ? $settings['outAnimation'] :'';
	
    $off_canvas = '';
    $offsetTime = wp_timezone_string();
    $now        = new DateTime('NOW', new DateTimeZone($offsetTime));
    $flag = true;

    if(!empty($settings['showTime']) && $settings['showTime'] == true) {
        $dateStart  = new DateTime($settings['dateStart'], new DateTimeZone($offsetTime));
        $dateEnd    = new DateTime($settings['dateEnd'], new DateTimeZone($offsetTime));
        if(($dateStart <= $now) && ($now <= $dateEnd)) {
            $flag = true;
        } else {
            $flag = false;
        }
    }
    
    if(!empty($settings['onpageviews']) && !empty($settings['pageViews']) && $settings['pageViews'] != '') {
        $flag = false;
        $_SESSION['pageViews'] = (isset($_SESSION['pageViews'])) ? $_SESSION['pageViews'] + 1 : 1;
        if($_SESSION['pageViews'] >= $settings['pageViews']) {
            $flag = true;
        }
    }

    $time = $days = '';
    if(!empty($settings['showRestricted']) && $settings['showRestricted'] != '') {
        
        $days = (!empty($settings['showXDays']) && $settings['showXDays'] != '') ? $settings['showXDays'] : 1;
        $_SESSION['popViews']   = (isset($_SESSION['popViews'])) ? $_SESSION['popViews'] + 1 : 1;
        if(!isset($_SESSION['dateNow'])) { $_SESSION['dateNow'] = ''; }
        if($_SESSION['popViews'] > $settings['showXTimes']) {
            
            $flag = false;
            if($days > 0) {
                $date = new DateTime('NOW', new DateTimeZone($offsetTime));
                $date = $date->modify("+".$days." day");
                $_SESSION['dateNow'] = ($_SESSION['dateNow'] == '') ? $date : $_SESSION['dateNow'];
                if($now >= $_SESSION['dateNow']) {
                    $_SESSION['popViews'] = 0;
                    $_SESSION['dateNow'] = '';
                }
            }
        } 
    }

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $settings );

    if($flag) {

        $widget_uid = 'canvas-' . $block_id;
        $fixedToggleBtn = ($settings[ "fixedToggleBtn" ] == true) ? 'position-fixed' : '';
        $scrollWindowOffset = ($settings[ "fixedToggleBtn" ] == true && $settings[ 'scrollWindowOffset' ] == true) ? 'scroll-view' : '';
        $scrollTopOffset = ($settings[ "fixedToggleBtn" ] == true && $settings[ 'scrollWindowOffset' ] == true) ? 'data-scroll-view="' . esc_attr($settings[ 'scrollTopOffset' ]) . '"' : '';

        $openStyle = $settings["openStyle"];
        $onbtnClick = !empty($settings["onbtnClick"]) ? 'yes' : 'no';
        $onpageLoad = !empty($settings["onpageLoad"]) ? 'yes' : 'no';
        $loadpodelay = !empty($settings["loadpodelay"]) ? (int) $settings["loadpodelay"] : '';
        $onScroll = !empty($settings["onScroll"]) ? 'yes' : 'no';
        $exitInlet = !empty($settings["exitInlet"]) ? 'yes' : 'no';
        $inactivity = !empty($settings["inactivity"]) ? 'yes' : 'no';
        $onpageviews = !empty($settings["onpageviews"]) ? 'yes' : 'no';
        $prevurl = !empty($settings["prevurl"]) ? 'yes' : 'no';
		$extraclick = !empty($settings["extraclick"]) ? 'yes' : 'no';
		
        $previousUrl = (!empty($settings["prevurl"]) ) ? $settings["previousUrl"]["url"] : '';
        $extraId = (!empty($settings["extraclick"]) ) ? $settings["extraId"] : '';
        $inactivitySec = ( !empty($settings["inactivity"])) ? $settings["inactivitySec"] : '';
        $openDir = $settings[ "openDir" ];
        $scrlBar = ($settings[ "scrlBar" ] != true) ? 'scroll-bar-disable' : '';
        $closeContent = ($settings[ "closeContent" ] == true) ? 'yes' : 'no';
        $bodyClickClose = ($settings[ "bodyClickClose" ] == true) ? 'yes' : 'no';
        if($openStyle == 'corner-box' ) {
            $openDir = $settings[ "cornerBoxDir" ];
        } elseif($openStyle == 'popup' ) {
            $openDir = "center";
        }
		
		// Set Popup class
        $animClass = $dataAttr = '';
        $animData = [];
        if(!empty($openStyle) && $openStyle == 'popup'){
            $animClass .= 'tpgb-view-animation';
            if(!empty($inAnimation)){
                $animData['anime'] = $inAnimation;
            }
            if(!empty($outAnimation)){
                $animData['animeOut'] = $outAnimation;
            }

            if( !empty($settings['inanimDur']) && $settings['inanimDur'] == 'custom' ){
                $animClass .= ' tpgb-anim-dur-custom';
            }else{
                $animClass .= ' tpgb-anim-dur-'.$settings['inanimDur'];
            }
            
            if( !empty($settings['outanimDur']) && $settings['outanimDur'] == 'custom' ){
                $animClass .= ' tpgb-anim-out-dur-custom';
            }else{
                $animClass .= ' tpgb-anim-out-dur-'.$settings['outanimDur'];
            }
            

            $dataAttr =  ' data-animationsetting =\'' . json_encode($animData) . '\' ';
        }
		
        $uid               = uniqid ( "canvas-" );
        $scrollHeight      = (!empty($settings["onScroll"])) ? $settings['scrollHeight'] : '';
        $data_attr         = 'data-settings = {"content_id":"' . esc_attr($block_id) . '","transition":"' . esc_attr ( $openStyle ) . '","direction":"' . esc_attr ( $openDir ) . '","esc_close":"' . esc_attr ( $closeContent ) . '","body_click_close":"' . esc_attr ( $bodyClickClose ) . '","trigger":"' . esc_attr ( $onbtnClick ) . '","onpageLoad":"' . esc_attr ( $onpageLoad ) . '","onpageloadDelay":"' . esc_attr ( $loadpodelay ) . '","onScroll":"' . esc_attr ( $onScroll ) . '","exitInlet":"' . esc_attr ( $exitInlet ) . '","inactivity":"' . esc_attr ( $inactivity ) . '","onpageviews":"' . esc_attr ( $onpageviews ) . '","prevurl":"' . esc_attr ( $prevurl ) . '","extraclick":"' . esc_attr ( $extraclick ) . '","scrollHeight":"'. esc_attr($scrollHeight). '","previousUrl":"'. esc_attr($previousUrl). '","extraId":"'. esc_attr($extraId). '","time":"'. esc_attr($time). '","days":"'. esc_attr($days). '","inactivitySec":"'. esc_attr($inactivitySec). '"}';
        $toggle_content    = '';
        
        $full_width_button = ($settings[ "toggleCanvas" ] == 'button' && ! empty ( $settings[ 'btnFullWidth' ] ) && $settings[ 'btnFullWidth' ] == true) ? 'btn_full_width' : '';
        $full_width_button .= ($settings[ "toggleCanvas" ] == 'button' && ! empty ( $settings[ 'btntabFull' ] ) && $settings[ 'btntabFull' ] == true) ? ' btn_full_tab_width' : '';
        $full_width_button .= ($settings[ "toggleCanvas" ] == 'button' && ! empty ( $settings[ 'btnmoFull' ] ) && $settings[ 'btnmoFull' ] == true) ? ' btn_full_mobile_width' : '';

        if( $settings[ "toggleCanvas" ] == 'button') {
            $toggle_content .= '<div class="offcanvas-toggle-btn toggle-button-style tpgb-trans-easeinout ' . esc_attr ( $fixedToggleBtn ) . ' ' . esc_attr ( $full_width_button ) . '">';
            $before_after = $settings['iconPosition'];
            $btnText = $settings['btnText'];

            if($settings["btnIconStyle"] == 'font_awesome') {
                $icons=$settings["btnIcon"];
            } else {
                $icons='';
            }

            $icons_before = $icons_after = '';
            if($before_after=='before' && !empty($icons)){
                $icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
            }
            if($before_after=='after' && !empty($icons)){
            $icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
            }

            $toggle_content .= $icons_before.'<span class="btn-text">'.wp_kses_post($btnText).'</span>'. $icons_after;
            $toggle_content .= '</div>';
        }
        if( $settings[ "toggleCanvas" ] == 'icon' && !empty($settings[ "toggleIconStyle" ])) {
            if( $settings[ "toggleIconStyle" ] == 'style-1' || $settings[ "toggleIconStyle" ] == 'style-2' || $settings[ "toggleIconStyle" ] == 'style-3' ) {
                $toggle_content .= '<div class="offcanvas-toggle-btn humberger-' . esc_attr ( $settings[ "toggleIconStyle" ] ) . ' ' . esc_attr ( $fixedToggleBtn ) . '">';
                $toggle_content .= '<span class="menu_line menu_line--top"></span>';
                $toggle_content .= '<span class="menu_line menu_line--center"></span>';
                $toggle_content .= '<span class="menu_line menu_line--bottom"></span>';
                $toggle_content .= '</div>';
            } else if( $settings[ "toggleIconStyle" ] == 'custom' ) {
                $toggle_content .= '<div class="offcanvas-toggle-btn humberger-' . esc_attr ( $settings[ "toggleIconStyle" ] ) . ' ' . esc_attr ( $fixedToggleBtn ) . '">';
                $toggle_content .= '<img src="' . esc_url($settings[ 'imgSvg' ][ 'url' ]) . '" class="off-can-img-svg"/>';
                $toggle_content .= '</div>';
            }
        }
        
        $off_canvas .= '<div class="tpgb-block-'.esc_attr($block_id).' tpgb-offcanvas-wrapper tpgb-relative-block ' . esc_attr ( $widget_uid ) . ' ' . esc_attr ( $scrollWindowOffset ) . ' '.esc_attr($blockClass).'" data-canvas-id="' . esc_attr ( $widget_uid ) . '" ' . $data_attr . ' ' . $scrollTopOffset . '>';

        $off_canvas .= '<div class="offcanvas-toggle-wrap tpgb-relative-block">';
        $off_canvas .= $toggle_content;
        $off_canvas .= '</div>';
        
		$content_classes = '';
		if(isset($settings['globalClasses']) && !empty($settings['globalClasses'])){
			$content_classes = $settings['globalClasses'];
		}
		
        $off_canvas .= '<div class="tpgb-block-'.esc_attr( $block_id ).'-canvas tpgb-canvas-content-wrap tpgb-' . esc_attr ( $openDir ) . ' tpgb-' . esc_attr ( $openStyle ) . ' tpgb-popup-'.esc_attr ($popupDir).'  ' . esc_attr ( $scrlBar ) . ' '.esc_attr($animClass).' '.esc_attr($content_classes).'" '.$dataAttr.' >';
		
        if( ! empty ( $settings[ "contentCloseIcon" ] ) && $settings[ "contentCloseIcon" ] == true ) {
            $sticky_btn       = ( ! empty ( $settings[ "closeIconSticky" ] ) && $settings[ "closeIconSticky" ] == true) ? 'sticky-close-btn' : '';
            $close_icon_class = ( ! empty ( $settings[ "closeIconCustom" ] ) && $settings[ "closeIconCustom" ] == true) ? 'off-close-image' : '';

            $off_canvas .= '<div class="tpgb-canvas-header direction-' . esc_attr ( $settings[ "closeIconAlign" ] ) . ' ' . esc_attr ( $sticky_btn ) . '"><div class="tpgb-offcanvas-close tpgb-offcanvas-close-' . esc_attr($block_id) . ' ' . esc_attr ( $close_icon_class ) . '" role="button">';
            if( ! empty ( $settings[ "closeIconCustom" ] ) && $settings[ "closeIconCustom" ] == true && ! empty ( $settings[ 'closeIconCustomSource' ][ 'url' ] ) ) {
                $off_canvas .= '<img src="' . esc_url($settings[ 'closeIconCustomSource' ][ 'url' ]) . '" class="close-custom_img"/>';
            }
            $off_canvas .= '</div></div>';
        }
        $off_canvas .= '<div class="tpgb-content-editor">';
            if($cntType == 'template' && ! empty ( $settings[ 'contentSource' ] )) {
                ob_start();
                    if(!empty($settings['contentSource']) && $settings['contentSource'] != 'none') {
                        echo Tpgb_Library()->plus_do_block($settings[ 'contentSource' ]);
                    }
                    $off_canvas .= ob_get_contents();
                ob_end_clean();
            }else if($cntType == 'content' && !empty($popupCnt) ){
                $off_canvas .= '<p> '.wp_kses_post($popupCnt).' </p>';
            }else if($cntType == 'shortcode' && !empty($shortCodeCnt) ){
                $off_canvas .= do_shortcode($shortCodeCnt);
            }else if($cntType == 'editor'){
                $off_canvas .= $content;
            }

        $off_canvas .= '</div>';
		
        $off_canvas .= '</div>';

        $off_canvas .= '</div>';
        
        if( ! empty ( $settings[ "fixedToggleBtn" ] ) && $settings[ "fixedToggleBtn" ] == true ) {
            $off_canvas .= '<style>';
            $rpos       = 'auto';
            $bpos       = 'auto';
            $ypos       = 'auto';
            $xpos       = 'auto';
            if( $settings[ 'leftAutoD' ] == true ) {
                if( ! empty ( $settings[ 'xPosD' ] ) || $settings[ 'xPosD' ] == '0' ) {
                    $xpos = $settings[ 'xPosD' ] . '%';
                }
            }
            if( $settings[ 'topAutoD' ] == true ) {
                if( ! empty ( $settings[ 'yPosD' ] ) || $settings[ 'yPosD' ] == '0' ) {
                    $ypos = $settings[ 'yPosD' ] . '%';
                }
            }
            if( $settings[ 'bottomAutoD' ] == true ) {
                if( ! empty ( $settings[ 'bottomPosD' ] ) || $settings[ 'bottomPosD' ] == '0' ) {
                    $bpos = $settings[ 'bottomPosD' ] . '%';
                }
            }
            if( $settings[ 'rightAutoD' ] == true ) {
                if( ! empty ( $settings[ 'rightPosD' ] ) || $settings[ 'rightPosD' ] == '0' ) {
                    $rpos = $settings[ 'rightPosD' ] . '%';
                }
            }

            $off_canvas .= '.' . esc_attr ( $widget_uid ) . ' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:' . esc_attr ( $ypos ) . ';bottom:' . esc_attr ( $bpos ) . ';left:' . esc_attr ( $xpos ) . ';right:' . esc_attr ( $rpos ) . ';}';

            if( ! empty ( $settings[ 'responsiveT' ] ) && $settings[ 'responsiveT' ] == true ) {
                $tablet_xpos = 'auto';
                $tablet_ypos = 'auto';
                $tablet_bpos = 'auto';
                $tablet_rpos = 'auto';
                if( $settings[ 'leftAutoT' ] == true ) {
                    if( ! empty ( $settings[ 'xPosT' ] ) || $settings[ 'xPosT' ] == '0' ) {
                        $tablet_xpos = $settings[ 'xPosT' ] . '%';
                    }
                }
                if( $settings[ 'topAutoT' ] == true ) {
                    if( ! empty ( $settings[ 'yPosT' ] ) || $settings[ 'yPosT' ] == '0' ) {
                        $tablet_ypos = $settings[ 'yPosT' ] . '%';
                    }
                }
                if( $settings[ 'bottomAutoT' ] == true ) {
                    if( ! empty ( $settings[ 'bottomPosT' ] ) || $settings[ 'bottomPosT' ] == '0' ) {
                        $tablet_bpos = $settings[ 'bottomPosT' ] . '%';
                    }
                }
                if( $settings[ 'rightAutoT' ] == true ) {
                    if( ! empty ( $settings[ 'rightPosT' ] ) || $settings[ 'rightPosT' ] == '0' ) {
                        $tablet_rpos = $settings[ 'rightPosT' ] . '%';
                    }
                }

                $off_canvas .= '@media (min-width:601px) and (max-width:990px){.' . esc_attr ( $widget_uid ) . ' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:' . esc_attr ( $tablet_ypos ) . ';bottom:' . esc_attr ( $tablet_bpos ) . ';left:' . esc_attr ( $tablet_xpos ) . ';right:' . esc_attr ( $tablet_rpos ) . ';}';

                $off_canvas .= '}';
            }
            if( ! empty ( $settings[ 'responsiveM' ] ) && $settings[ 'responsiveM' ] == true ) {
                $mobile_xpos = 'auto';
                $mobile_ypos = 'auto';
                $mobile_bpos = 'auto';
                $mobile_rpos = 'auto';
                if( $settings[ 'leftAutoM' ] == true ) {
                    if( ! empty ( $settings[ 'xPosM' ] ) || $settings[ 'xPosM' ] == '0' ) {
                        $mobile_xpos = $settings[ 'xPosM' ] . '%';
                    }
                }
                if( $settings[ 'topAutoM' ] == true ) {
                    if( ! empty ( $settings[ 'yPosM' ] ) || $settings[ 'yPosM' ] == '0' ) {
                        $mobile_ypos = $settings[ 'yPosM' ] . '%';
                    }
                }
                if( $settings[ 'bottomAutoM' ] == true ) {
                    if( ! empty ( $settings[ 'bottomPosM' ]) || $settings[ 'bottomPosM' ] == '0' ) {
                        $mobile_bpos = $settings[ 'bottomPosM' ] . '%';
                    }
                }
                if( $settings[ 'rightAutoM' ] == true ) {
                    if( ! empty ( $settings[ 'rightPosM' ] ) || $settings[ 'rightPosM' ] == '0' ) {
                        $mobile_rpos = $settings[ 'rightPosM' ] . '%';
                    }
                }
                $off_canvas .= '@media (max-width:600px){.' . esc_attr ( $widget_uid ) . ' .offcanvas-toggle-wrap .offcanvas-toggle-btn.position-fixed{top:' . esc_attr ( $mobile_ypos ) . ';bottom:' . esc_attr ( $mobile_bpos ) . ';left:' . esc_attr ( $mobile_xpos ) . ';right:' . esc_attr ( $mobile_rpos ) . ';}';

                $off_canvas .= '}';
            }
            $off_canvas .= '</style>';
        }
		if( $bodyClickClose == 'no'){
            $off_canvas .= '<style>.tpgb-block-'.esc_attr( $block_id ).'-canvas-open .tpgb-offcanvas-container:after { display: none;} </style>';
        }
    }
	
	$off_canvas = Tpgb_Blocks_Global_Options::block_Wrap_Render($settings, $off_canvas);
	
    return $off_canvas;
}

function tpgb_tp_popup_builder_render() {
 
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();

    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'contentSource' => [
            'type' => 'string',
            'default' => ''
        ],
		'cntType' => [
            'type' => 'string',
            'default' => 'template'
        ],
        'popupCnt' => [
            'type' => 'string',
            'default' => 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'
        ],
        'shortCodeCnt' => [
            'type' => 'string',
            'default' => ''
        ],
		'backendVisi' => [
            'type' => 'boolean',
            'default' => false
        ],
		'popupDir' => [
            'type' => 'string',
            'default' => 'center'
        ], 
        'toggleCanvas' => [
            'type' => 'string',
            'default' => 'button'
        ],
        'toggleIconStyle' => [
            'type' => 'string',
            'default' => 'style-1'
        ],
        'imgSvg' => [
            'type' => 'object',
            'default' => ''
        ],
        'imgSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'icon'],
                            ['key' => 'toggleIconStyle', 'relation' => '==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg{width: {{imgSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'icon'],
                            ['key' => 'toggleIconStyle', 'relation' => '!==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3{width: {{iconSize}}; height: {{iconSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconWeight' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'icon'],
                            ['key' => 'toggleIconStyle', 'relation' => '!==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1 span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2 span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3 span.menu_line{height: {{iconWeight}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => [
                    "top" => '',
                    "right" => '',
                    "bottom" => '',
                    "left" => '',
                ],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'icon'],
                            ['key' => 'toggleIconStyle', 'relation' => '!==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3{padding: {{iconPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnText' => [
            'type' => 'string',
            'default' => 'Open Me'
        ],
        'btnIconStyle' => [
            'type' => 'string',
            'default' => 'font_awesome'
        ],
        'btnIcon' => [
            'type' => 'string',
            'default' => 'fa fa-chevron-right'
        ],
        'iconPosition' => [
            'type' => 'string',
            'default' => 'after'
        ],
        'btnIconSpacing' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'button'],
                            ['key' => 'btnIconStyle', 'relation' => '!=', 'value' => ''],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon.button-after{margin-left: {{btnIconSpacing}};}{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon.button-before{margin-right: {{btnIconSpacing}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnIconSize' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleCanvas', 'relation' => '==', 'value' => 'button'],
                            ['key' => 'btnIconStyle', 'relation' => '!=', 'value' => ''],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn .btn-icon{font-size: {{btnIconSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnAlign' => [
            'type' => 'object',
            'default' => [ 'md' => 'center', 'sm' =>  'center', 'xs' =>  'center' ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}} .offcanvas-toggle-wrap{text-align: {{btnAlign}};}',
                ],
            ],
        ],
        'openStyle' => [
            'type' => 'string',
            'default' => 'popup',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'openStyle', 'relation' => '!=', 'value' => 'popup']],
                    'selector' => '{{PLUS_WRAP}}-canvas-open {{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-visible{-webkit-transform: translate3d(0,0,0);transform: translate3d(0,0,0);}',
                ],
            ],
			'scopy' => true,
        ],
        'openDir' => [
            'type' => 'string',
            'default' => 'left'
        ],
        'cornerBoxDir' => [
            'type' => 'string',
            'default' => 'top-left'
        ],
        'popupWidth' => [
            'type' => 'object',
            'default' => (object) [
                'md' => 300,
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'openStyle', 'relation' => '==', 'value' => 'popup']],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-center{max-width: {{popupWidth}};}',
                ],
            ],
			'scopy' => true,
        ],
        'popupHeight' => [
            'type' => 'object',
            'default' => (object) [
                'md' => 300,
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'openStyle', 'relation' => '==', 'value' => 'popup']],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-center{max-height: {{popupHeight}};}',
                ],
                (object) [
                    'condition' => [
                        (object) [ 'key' => 'popupDir', 'relation' => '==', 'value' => ['left' , 'right' , 'center'] ],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-center{ margin-top : calc( -{{popupHeight}} /2);}',
                ],
            ],
			'scopy' => true,
        ],
        'openWidth' => [
            'type' => 'object',
            'default' => (object) [
                'md' => 300,
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'openStyle', 'relation' => '!=', 'value' => 'popup']],
                    'selector' => 
                        '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-top,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-bottom{width: 100%;height: {{openWidth}};}{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap{width: {{openWidth}};}{{PLUS_WRAP}}-canvas-open.tpgb-push.tpgb-open.tpgb-left .tpgb-offcanvas-container,{{PLUS_WRAP}}-canvas-open.tpgb-slide-along.tpgb-open.tpgb-left .tpgb-offcanvas-container{-webkit-transform: translate3d({{openWidth}}, 0, 0);transform: translate3d({{openWidth}}, 0, 0);}{{PLUS_WRAP}}-canvas-open.tpgb-push.tpgb-open.tpgb-right .tpgb-offcanvas-container,{{PLUS_WRAP}}-canvas-open.tpgb-slide-along.tpgb-open.tpgb-right .tpgb-offcanvas-container{-webkit-transform: translate3d(-{{openWidth}}, 0, 0);transform: translate3d(-{{openWidth}}, 0, 0);}{{PLUS_WRAP}}-canvas-open.tpgb-push.tpgb-open.tpgb-top .tpgb-offcanvas-container,{{PLUS_WRAP}}-canvas-open.tpgb-slide-along.tpgb-open.tpgb-top .tpgb-offcanvas-container{-webkit-transform: translate3d(0,{{openWidth}}, 0);transform: translate3d( 0,{{openWidth}}, 0);}{{PLUS_WRAP}}-canvas-open.tpgb-push.tpgb-open.tpgb-bottom .tpgb-offcanvas-container,{{PLUS_WRAP}}-canvas-open.tpgb-slide-along.tpgb-open.tpgb-bottom .tpgb-offcanvas-container{-webkit-transform: translate3d(0,-{{openWidth}}, 0);transform: translate3d( 0,-{{openWidth}}, 0);}{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-corner-box{width: {{openWidth}};height: {{openWidth}};}{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-top-left.tpgb-corner-box{-webkit-transform: translate3d(-{{openWidth}},-{{openWidth}},0);transform: translate3d(-{{openWidth}},-{{openWidth}},0);}{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-top-right.tpgb-corner-box{-webkit-transform: translate3d({{openWidth}},-{{openWidth}},0);transform: translate3d({{openWidth}},-{{openWidth}},0);}',
                ],
            ],
			'scopy' => true,
        ],
        'closeContent' => [
            'type' => 'boolean',
            'default' => true
        ],
        'bodyClickClose' => [
            'type' => 'boolean',
            'default' => true
        ],
        'fixedToggleBtn' => [
            'type' => 'boolean',
            'default' => ''
        ],
        'scrollWindowOffset' => [
            'type' => 'boolean',
            'default' => false
        ],
        'scrollTopOffset' => [
            'type' => 'string',
            'default' => '',
        ],
        'leftAutoD' => [
            'type' => 'boolean',
            'default' => true
        ],
        'xPosD' => [
            'type' => 'string',
            'default' => '',
        ],
        'rightAutoD' => [
            'type' => 'boolean',
            'default' => false
        ],
        'rightPosD' => [
            'type' => 'string',
            'default' => '',
        ],
        'topAutoD' => [
            'type' => 'boolean',
            'default' => true
        ],
        'yPosD' => [
            'type' => 'string',
            'default' => '',
        ],
        'bottomAutoD' => [
            'type' => 'boolean',
            'default' => false
        ],
        'bottomPosD' => [
            'type' => 'string',
            'default' => '',
        ],
        'responsiveT' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'leftAutoT' => [
            'type' => 'boolean',
            'default' => false
        ],
        'xPosT' => [
            'type' => 'string',
            'default' => '',
        ],
        'rightAutoT' => [
            'type' => 'boolean',
            'default' => false
        ],
        'rightPosT' => [
            'type' => 'string',
            'default' => '',
        ],
        'topAutoT' => [
            'type' => 'boolean',
            'default' => false
        ],
        'yPosT' => [
            'type' => 'string',
            'default' => '',
        ],
        'bottomAutoT' => [
            'type' => 'boolean',
            'default' => false
        ],
        'bottomPosT' => [
            'type' => 'string',
            'default' => '',
        ],
        'responsiveM' => [
            'type' => 'boolean',
            'default' => false
        ],
        'leftAutoM' => [
            'type' => 'boolean',
            'default' => false
        ],
        'xPosM' => [
            'type' => 'string',
            'default' => '',
        ],
        'rightAutoM' => [
            'type' => 'boolean',
            'default' => false
        ],
        'rightPosM' => [
            'type' => 'string',
            'default' => '',
        ],
        'topAutoM' => [
            'type' => 'boolean',
            'default' => false
        ],
        'yPosM' => [
            'type' => 'string',
            'default' => '',
        ],
        'bottomAutoM' => [
            'type' => 'boolean',
            'default' => false
        ],
        'bottomPosM' => [
            'type' => 'string',
            'default' => '',
        ],
        'contentPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => [
                    "top" => '',
                    "right" => '',
                    "bottom" => '',
                    "left" => '',
                ]
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-content-editor{padding: {{contentPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'contentBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap'
                ],
            ],
			'scopy' => true,
        ],
        'contentRadius' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap{border-radius: {{contentRadius}};}',
                ],
            ],
			'scopy' => true,
        ],
        'contentShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap',
                ],
            ],
			'scopy' => true,
        ],
        'contentShadowH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap:hover',
                ],
            ],
			'scopy' => true,
        ],
        'contentCloseIcon' => [
            'type' => 'boolean',
            'default' => true,
        ],
        'closeIconSticky' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'closeIconCustom' => [
            'type' => 'boolean',
            'default' => false
        ],
        'closeIconCustomSource' => [
            'type' => 'object',
            'default' => ''
        ],
        'closeIconAlign' => [
            'type' => 'string',
            'default' => 'right',
        ],
        'closeContentColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                            ['key' => 'closeIconCustom', 'relation' => '==', 'value' => false],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:before,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:after{border-bottom-color: {{closeContentColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'offCloseImg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                            ['key' => 'closeIconCustom', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .off-close-image .close-custom_img{ width: {{offCloseImg}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                        'condition' => [
                                (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                        ],
                        'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseRadius' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                            (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .off-close-image .close-custom_img{ border-radius: {{openCloseRadius}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                            (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseColorH' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                            ['key' => 'closeIconCustom', 'relation' => '==', 'value' => false],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:hover:before,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:hover:after{ border-bottom-color: {{openCloseColorH}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'closeBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:hover',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseRadiusH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:hover,{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .off-close-image .close-custom_img:hover{ border-radius: {{openCloseRadiusH}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'openCloseShadowH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                            (object) ['key' => 'contentCloseIcon', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap .tpgb-offcanvas-close:hover',
                ],
            ],
			'scopy' => true,
        ],
        'openOverlayBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '.tpgb-offcanvas-content-widget{{PLUS_WRAP}}-canvas-open .tpgb-offcanvas-container:after,.tpgb-offcanvas-content-widget{{PLUS_WRAP}}-canvas-open .edit-post-visual-editor.editor-styles-wrapper:after',
                ],
            ],
			'scopy' => true,
        ],
		'openCssfilter' => [
            'type' => 'object',
            'default' => [
                'openFilter' => false,
                'blur' => 0,
                'brightness' => 100,
                'contrast' => 100,
                'saturate' => 100,
                'hue' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '.tpgb-offcanvas-content-widget{{PLUS_WRAP}}-canvas-open .tpgb-offcanvas-container:after,.tpgb-offcanvas-content-widget{{PLUS_WRAP}}-canvas-open .edit-post-visual-editor.editor-styles-wrapper:after',
                ],
            ],
			'scopy' => true,
        ],
        'iconBorder' => [
            'type' => 'boolean',
            'default' => false,
			'scopy' => true,
        ],
        'iconBorderStyle' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'iconBorder', 'relation' => '==', 'value' => true],
                    ],
                    'selector'  => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
                ],
            ],
			'scopy' => true,
        ],
        'iconColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleIconStyle', 'relation' => '!==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1 span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2 span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3 span.menu_line{background: {{iconColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
                ],
            ],
			'scopy' => true,
        ],
        'iconRadius' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => [
                    "top" => '',
                    "right" => '',
                    "bottom" => '',
                    "left" => '',
                ],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg{border-radius: {{iconRadius}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg',
                ],
            ],
			'scopy' => true,
        ],
        'iconColorH' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'toggleIconStyle', 'relation' => '!==', 'value' => 'custom'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover span.menu_line,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover span.menu_line{background: {{iconColorH}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconBgH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
                ],
            ],
			'scopy' => true,
        ],
        'iconBorderStyleH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'iconBorder', 'relation' => '==', 'value' => true],
                    ],  
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
                ],
            ],
			'scopy' => true,
        ],
        'iconRadiusH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover{border-radius: {{iconRadiusH}};}',
                ],
            ],
			'scopy' => true,
        ],
        'iconShadowH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-1:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-2:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-style-3:hover,{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn.humberger-custom .off-can-img-svg:hover',
                ],
            ],
			'scopy' => true,
        ],
        'btnFullWidth' => [
            'type' => 'boolean',
            'default' => false,
			'scopy' => true,
        ],
        'btnPadding' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => [
                    "top" => '',
                    "right" => '',
                    "bottom" => '',
                    "left" => '',
                ],
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn{padding: {{btnPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnTypo' => [
            'type' => 'object',
            'default' => (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn',
                ],
            ],
			'scopy' => true,
        ],
        'btnBorder' => [
            'type' => 'boolean',
            'default' => false,
			'scopy' => true,
        ],
        'btnBorderStyle' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'btnBorder', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn',
                ],
            ],
			'scopy' => true,
        ],
        'btnTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                        'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn{color: {{btnTextColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn',
                ],
            ],
			'scopy' => true,
        ],
        'btnRadius' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn{border-radius: {{btnRadius}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn',
                ],
            ],
			'scopy' => true,
        ],
        'btnTextColorH' => [
            'type' => 'string',
			'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn:hover{color: {{btnTextColorH}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnBgH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn:hover',
                ],
            ],
			'scopy' => true,
        ],
        'btnBorderStyleH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'btnBorder', 'relation' => '==', 'value' => true],
                            ['key' => 'btnBorderStyle', 'relation' => '!==', 'value' => 'none'],
                    ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn:hover',
                ],
            ],
			'scopy' => true,
        ],
        'btnRadiusH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn:hover{border-radius: {{btnRadiusH}};}',
                ],
            ],
			'scopy' => true,
        ],
        'btnShadowH' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-offcanvas-wrapper .offcanvas-toggle-btn:hover',
                ],
            ],
			'scopy' => true,
        ],
        'scrlBar' => [
            'type' => 'boolean',
            'default' => true,
			'scopy' => true,
        ],
        'scrlWidth' => [
            'type' => 'object',
            'default' => (object) [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
            (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar{width: {{scrlWidth}};}',
                ],
            ],
			'scopy' => true,
        ],
        'scrlBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar',
                ],
            ],
			'scopy' => true,
        ],
        'scrlThumbBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-thumb',
                ],
            ],
			'scopy' => true,
        ],
        'scrlThumbBorderR' => [
            'type' => 'object',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-thumb{border-radius: {{scrlThumbBorderR}};}',
                ],
            ],
			'scopy' => true,
        ],
        'scrlThumbShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-thumb',
                ],
            ],
			'scopy' => true,
        ],
        'trackBg' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-track',
                ],
            ],
			'scopy' => true,
        ],
        'trackBorderR' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-track{border-radius: {{trackBorderR}};}',
                ],
            ],
			'scopy' => true,
        ],
        'trackShadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'scrlBar', 'relation' => '==', 'value' => true],
                    ],
                    'selector' => '.tpgb-canvas-content-wrap{{PLUS_WRAP}}-canvas::-webkit-scrollbar-track',
                ],
            ],
			'scopy' => true,
        ],
        'onbtnClick' => [
            'type' => 'boolean',
	        'default' => true,	
        ],
        'loadpodelay' => [
            'type' => 'string',
	        'default' => '500',
        ],
        'onpageLoad' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'onScroll' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'exitInlet' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'inactivity' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'pageviews' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'prevurl' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'extraclick' => [
            'type' => 'boolean',
	        'default' => false,	
        ],
        'scrollHeight' => [
            'type' => 'string',
	        'default' => 100,
        ],
        'previousUrl' => [
            'type'=> 'object',
            'default'=> [
                'url' => '',
            ],
        ],
        'extraId' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'showTime' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'dateStart' => [
            'type' => 'string',
            'default' => '2020-10-28 02:59',
        ],
        'dateEnd' => [
            'type' => 'string',
            'default' => '2020-10-28 02:59',
        ],
        'showRestricted' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'showXTimes' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'showXDays' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'pageViews' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'inactivitySec' => [
            'type'=> 'string',
            'default'=> '',
        ],
        'popLeftAuto' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'popTopAuto' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'popXPos' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [ (object)  ['key' => 'openStyle', 'relation' => '==', 'value' => 'popup'], 
                                    ['key' => 'popLeftAuto', 'relation' => '==', 'value' => true ] ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-center.tpgb-popup.tpgb-visible{ left: {{popXPos}}%  }',
                ],
            ],
        ],
        'popYPos' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [ (object)  ['key' => 'openStyle', 'relation' => '==', 'value' => 'popup'], 
                                    ['key' => 'popTopAuto', 'relation' => '==', 'value' => true ] ],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-canvas-content-wrap.tpgb-center.tpgb-popup.tpgb-visible{ top: {{popYPos}}%  }',
                ],
            ],
        ],
		
        'inAnimation' => [
            'type' => 'string',
            'default' => 'fadeIn',
        ],
        'inanimDir' => [
            'type' => 'string',
            'default' => '',
        ],
        'inanimDur' => [
            'type' => 'string',
            'default' => 'normal',
        ],
        'custDur' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb_animated.tpgb-anim-dur-custom{-webkit-animation-duration: {{custDur}}s;animation-duration: {{custDur}}s;}',
                ],
            ],
        ],
        'animDelay' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-view-animation{-webkit-animation-delay: {{animDelay}}s;animation-delay: {{animDelay}}s;}',
                ],
            ],
        ],
        'AnimEasing' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'AnimEasing', 'relation' => '!=', 'value' => 'custom' ]],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-view-animation{animation-timing-function: {{AnimEasing}};}',
                ],
            ],
        ],
        'AnimEasCustom' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-view-animation-out{animation-timing-function: {{AnimEasCustom}};}',
                ],
            ],
        ],

        'outAnimation' => [
            'type' => 'string',
            'default' => 'fadeOut',
        ],
        'outanimDir' => [
            'type' => 'string',
            'default' => '',
        ],
        'outanimDur' => [
            'type' => 'string',
            'default' => 'normal',
        ],
        'outcustDur' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb_animated_out.tpgb-anim-out-dur-custom{-webkit-animation-duration: {{outcustDur}}s;animation-duration: {{outcustDur}}s;}',
                ],
            ],
        ],
        'outanimDelay' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-view-animation-out{-webkit-animation-delay: {{outanimDelay}}s;animation-delay: {{outanimDelay}}s;}',
                ],
            ],
        ],
        'outAnimEasing' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'outAnimEasing', 'relation' => '!=', 'value' => 'custom' ]],
                    'selector' => '{{PLUS_WRAP}}-canvas.tpgb-view-animation-out{animation-timing-function: {{outAnimEasing}};}',
                ],
            ],
        ],
        'AnimEasCustomOut' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_BLOCK}}-canvas.tpgb-view-animation-out{animation-timing-function: {{AnimEasCustomOut}};}',
                ],
            ],
        ],
        'btnrespo' => [
            'type' => 'boolean',
	        'default' => false,
        ],
        'btntabFull' => [
            'type' => 'boolean',
	        'default' => false,
        ],
        'btnmoFull' => [
            'type' => 'boolean',
	        'default' => false,
        ],
    ];
        
    $attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-popup-builder', array(
		'attributes' => $attributesOptions,
        'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_popup_builder_callback'
    ));
}
add_action( 'init', 'tpgb_tp_popup_builder_render' );