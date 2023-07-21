<?php
/* Block : Countdown
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_countdown_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1' ;
    $countdownSelection = $attributes['countdownSelection'];
    $offset_time = get_option('gmt_offset');
    $offsetTime = wp_timezone_string();
    $now    = new DateTime('NOW', new DateTimeZone($offsetTime));

	$future = '';
    if(!empty($attributes['datetime']) && $attributes['datetime'] != 'Invalid date') {
        $future = new DateTime($attributes['datetime'], new DateTimeZone($offsetTime));
    }
    $now = $now->modify("+1 second");

    if(!empty($attributes['datetime'])) {
        $datetime = $attributes['datetime'];
        $datetime = date('m/d/Y H:i:s', strtotime($datetime) );
    } else {
        $curr_date = date("m/d/Y H:i:s");
		$datetime = date('m/d/Y H:i:s', strtotime($curr_date . ' +1 month'));
    }
    
	if( $countdownSelection == 'scarcity' && !empty($attributes['datetime'])) {
        $rightNow = new DateTime('NOW', new DateTimeZone($offsetTime));
        $rightNow = ($attributes['scarSec'] > 0) ? $rightNow->modify("+".$attributes['scarSec']." minute") : $rightNow;
        $datetime = $rightNow->format('m/d/Y H:i:s');
    }
    
    
    $dataAttr = '';
    $showLabels = (!empty($attributes['showLabels'])) ? $attributes['showLabels'] : '' ;
    $daysText = (!empty($attributes['daysText'])) ? $attributes['daysText'] : 'Days' ;
    $hoursText = (!empty($attributes['hoursText'])) ? $attributes['hoursText'] : 'Hours' ;
    $minutesText = (!empty($attributes['minutesText'])) ? $attributes['minutesText'] : 'Minutes' ;
    $secondsText = (!empty($attributes['secondsText'])) ? $attributes['secondsText'] : 'Seconds' ;
    $flipTheme = (!empty($attributes['flipTheme'])) ? $attributes['flipTheme'] : 'dark' ;
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
    if( ( $countdownSelection == 'scarcity' || $countdownSelection == 'normal' ) && ( !empty($showLabels) && $showLabels == true )) {
        $dataAttr .= 'data-day="'.wp_kses_post($daysText).'" data-hour="'.wp_kses_post($hoursText).'" data-min="'.wp_kses_post($minutesText).'" data-sec="'.wp_kses_post($secondsText).'"';
    }
    
    if($style == 'style-2'){
        $dataAttr .= 'data-filptheme = "'.esc_attr($flipTheme).'"';
    }

    // Set Data For scarcity
    $enloop = !empty($attributes['enaLoop']) ? 'yes' : 'no' ;
    $delayTime = !empty($attributes['delayTime']) ? (float) $attributes['delayTime'] : 0 ;
    $countData = [];

    $countData['style'] = $style;
    $countData['blockId'] = 'tpgb-block-'.esc_attr($block_id).'';
    $countData['type'] = $countdownSelection;
    $countData['expiry']= $attributes['countdownExpiry'];
    
    if($attributes['countdownExpiry'] == 'redirect') {
        $encodedUrl = $attributes['expiryRedirect']['url'];
        $countData['redirect'] = $encodedUrl;
    }
    
    if($attributes['countdownExpiry'] == 'showmsg') {
        $countData['expiryMsg'] = wp_kses_post($attributes['expiryMsg']);
    }

    if($countdownSelection == 'scarcity' ){
        $countData['sctimeli'] = (float) $attributes['scarSec'];
        $countData['storetype'] = $attributes['trackUdata'];
        if( !empty($attributes['trackUdata']) && $countData['storetype'] == 'cookie' &&  $enloop == 'yes'){
            $countData['fakeloop'] = $enloop;
            $countData['delayminit'] = $delayTime;
        }
    }
    
    if($countdownSelection == 'numbers'){
        $fackloop = !empty($attributes['fenaLoop']) ? 'yes' : 'no' ;
        
        $countData['massage'] =  $attributes['fMsg'];
        $countData['initNum']        = (int) $attributes['initNum'];
        $countData['endNum']         = (int) $attributes['endNum'];
        $countData['numRange']       = (int) $attributes['numRange'];
        $countData['changeInterval'] = (int) $attributes['changeInterval'];
        $countData['storetype'] = $attributes['trackUdata'];
        $countData['fenaLoop'] = $fackloop;
    }
    
    $dataAttr .= 'data-countdata= \'' . json_encode($countData) . '\'';

    $output .= '<div class="tp-countdown tpgb-block-'.esc_attr($block_id).' countdown-'.esc_attr($style).' '.esc_attr($blockClass).' tpgb-relative-block " data-offset="'.esc_attr($offset_time).'" '.$dataAttr.'>';
    
    if($countdownSelection == 'normal' ) {
        if($future >= $now && isset($future)) {
            if($style == 'style-1') {
               
                $inline_style = (!empty($attributes["inlineStyle"]) && $attributes["inlineStyle"]==true) ? 'count-inline-style' : '';
                
                $output .= '<div class="tpgb-countdown-counter '.esc_attr($inline_style).'" data-time = "'.esc_attr($datetime).'">';
                $output .= '<div class="count_1">';
                $output .= '<span class="days">00</span>';
                if(!empty($showLabels) && $showLabels==true) {
                    $output .= '<h6 class="days_ref">'.wp_kses_post($daysText).'</h6>';
                }
                $output .= '</div>';
                $output .= '<div class="count_2">';
                $output .= '<span class="hours">00</span>';
                if(!empty($showLabels) && $showLabels==true) {
                    $output .= '<h6 class="hours_ref">'.wp_kses_post($hoursText).'</h6>';
                }
                $output .= '</div>';
                $output .= '<div class="count_3">';
                $output .= '<span class="minutes">00</span>';
                if(!empty($showLabels) && $showLabels==true) {
                    $output .= '<h6 class="minutes_ref">'.wp_kses_post($minutesText).'</h6>';
                }
                $output .= '</div>';
                $output .= '<div class="count_4">';
                $output .= '<span class="seconds last">00</span>';
                if(!empty($showLabels) && $showLabels==true) {
                    $output .= '<h6 class="seconds_ref">'.wp_kses_post($secondsText).'</h6>';
                }
                $output .= '</div></div>';
            
            } elseif($style == 'style-2') {
                $output .= '<div id="tpgb-block-'.esc_attr($block_id).'" class="flipdown tpgb-countdown-counter" data-time='.esc_attr(strtotime($datetime)).'></div>';
            } elseif($style == 'style-3') {
                if(!empty($attributes['datetime'])) {
                    if($countdownSelection == 'scarcity') {
                        $datetime = $rightNow->format('c');
                    } else {
                        $datetime= $future->format('c');
                    }
                } else {
                    $datetime = '2020-08-10T16:42:00';
                }
                
                $output .= '<div class="tpgb-countdown-counter" data-time="'.esc_attr($datetime).'">';
					$output .= '<div class="counter-part"><div class="day-'.esc_attr($block_id).' day" id="dtpgb-block-'.esc_attr($block_id).'"></div></div>';
					$output .= '<div class="counter-part"><div class="hour-'.esc_attr($block_id).' hour" id="htpgb-block-'.esc_attr($block_id).'"></div></div>';
					$output .= '<div class="counter-part"><div class="min-'.esc_attr($block_id).' min" id="mtpgb-block-'.esc_attr($block_id).'"></div></div>';
					$output .= '<div class="counter-part"><div class="sec-'.esc_attr($block_id).' sec" id="stpgb-block-'.esc_attr($block_id).'"></div></div>';
				$output .= '</div>';
            }
        }
    }
    
    if($countdownSelection == 'scarcity' && $style == 'style-1' ){
        $inline_style = (!empty($attributes["inlineStyle"]) && $attributes["inlineStyle"]==true) ? 'count-inline-style' : '';
                
        $output .= '<div class="tpgb-countdown-counter '.esc_attr($inline_style).'" data-time = "'.esc_attr($datetime).'">';
        $output .= '<div class="count_1">';
        $output .= '<span class="days">00</span>';
        if(!empty($showLabels) && $showLabels==true) {
            $output .= '<h6 class="days_ref">'.wp_kses_post($daysText).'</h6>';
        }
        $output .= '</div>';
        $output .= '<div class="count_2">';
        $output .= '<span class="hours">00</span>';
        if(!empty($showLabels) && $showLabels==true) {
            $output .= '<h6 class="hours_ref">'.wp_kses_post($hoursText).'</h6>';
        }
        $output .= '</div>';
        $output .= '<div class="count_3">';
        $output .= '<span class="minutes">00</span>';
        if(!empty($showLabels) && $showLabels==true) {
            $output .= '<h6 class="minutes_ref">'.wp_kses_post($minutesText).'</h6>';
        }
        $output .= '</div>';
        $output .= '<div class="count_4">';
        $output .= '<span class="seconds last">00</span>';
        if(!empty($showLabels) && $showLabels==true) {
            $output .= '<h6 class="seconds_ref">'.wp_kses_post($secondsText).'</h6>';
        }
        $output .= '</div></div>';
    }

    if( $countdownSelection == 'normal' || $countdownSelection == 'scarcity'){
        if($attributes['countdownExpiry'] == 'showtemp') {
            $output .= '<div class="tpgb-countdown-expiry tpgb-hide">';
            ob_start();
            if(!empty($attributes['templates']) && $attributes['templates'] != 'none') {
                echo Tpgb_Library()->plus_do_block($attributes['templates']);
            }
            $loop_content = ob_get_contents();
            ob_end_clean();
            $output .= $loop_content;
            $output .= '</div>';
        }
    }

    if($countdownSelection == 'numbers') {
        $output .= '<div class="tpgb-number-count">';
            $output .= '<span>'.wp_kses_post($attributes['fMsg']).'</span>';
        $output .= '</div>';
    }

    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
}

function tpgb_tp_countdown_render() {
 
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$curr_date = date("Y-m-d h:i:s");
	$curr_date = date('Y-m-d H:i:s', strtotime($curr_date . ' +1 month'));
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'countdownSelection' => [
            'type' => 'string',
            'default' => 'normal',	
        ],
        'style' => [
            'type' => 'string',
            'default' => 'style-1',
        ],
        'scarSec' => [
            'type' => 'string',
            'default' => '90'
        ],
        'trackUdata' => [
            'type' => 'string',
	        'default' => 'normal',
        ],
        'enaLoop' => [
            'type' => 'boolean',
            'default' => false,
        ], 
        'delayTime' => [
            'type' => 'string',
	        'default' => '',
        ],
        'flipTheme' => [
            'type' => 'string',
	        'default' => 'dark',
        ],
        'fenaLoop' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'fMsg' => [
            'type' => 'string',
	        'default' => 'Showing {visible_counter}',
        ],
        'counterMaxWidth' => [
            'type' => 'object',
            'default' => (object) ['md' => ''],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown.tpgb-countdown-counter{ max-width: {{counterMaxWidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'datetime' => [
            'type' => 'string',
            'default' => $curr_date,
        ],
        'changeType' => [
            'type' => 'string',
            'default' => 'decreasing',
        ],
        'initNum' => [
            'type'=> 'string',
            'default'=> 1,
        ],
        'endNum' => [
            'type'=> 'string',
            'default'=> 10,
        ],
        'numRange' => [
            'type'=> 'string',
            'default'=> 10,
        ],
        'changeInterval' => [
            'type'=> 'string',
            'default'=> 1,
        ],
        'countdownExpiry' => [
            'type' => 'string',
            'default' => 'none',
        ],
        'expiryMsg' => [
            'type' => 'string',
            'default' => 'Countdown Has Ended !',
        ],
        'expiryRedirect' => [
            'type'=> 'object',
            'default'=> [
                'url' => '',
                'target' => '',
                'nofollow' => ''
            ],
        ],
        'templates' => [
            'type' => 'string',
            'default' => '',	
        ],
        'backendVisi' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'inlineStyle' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'showLabels' => [
            'type' => 'boolean',
            'default' => true,
        ],
        'daysText' => [
            'type'=> 'string',
            'default'=> 'Days',
        ],
        'hoursText' => [
            'type'=> 'string',
            'default'=> 'Hours',
        ],
        'minutesText' => [
            'type'=> 'string',
            'default'=> 'Minutes',
        ],
        'secondsText' => [
            'type'=> 'string',
            'default'=> 'Seconds',
        ],    
        'counterFontColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div > span{ color: {{counterFontColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor .rotor-leaf figure,{{PLUS_WRAP}} .flipdown .rotor .rotor-top,{{PLUS_WRAP}} .flipdown .rotor .rotor-bottom{ color: {{counterFontColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .progressbar-text .number{ color: {{counterFontColor}}; }',
                ],
            ],
			'scopy' => true,
        ],     
        'counterTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div > span',
                ],
				[
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor .rotor-leaf figure, {{PLUS_WRAP}} .flipdown .rotor .rotor-top, {{PLUS_WRAP}} .flipdown .rotor .rotor-bottom',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .progressbar-text .number',
                ],
            ],
			'scopy' => true,
        ],
        'labelTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1'],
                            ['key' => 'showLabels', 'relation' => '==', 'value' => true]
                        ],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div > h6',
                ],
                [
                    'condition' => [
                        (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2'],
                            ['key' => 'showLabels', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group .rotor-group-heading:before',
                ],
                [
                    'condition' => [
                        (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3'],
                            ['key' => 'showLabels', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .progressbar-text .label',
                ],
            ],
			'scopy' => true,
        ],
        'expiryMsgTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownExpiry', 'relation' => '==', 'value' => 'showmsg']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-expiry',
                ],
            ],
			'scopy' => true,
        ],
        'expiryFontColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownExpiry', 'relation' => '==', 'value' => 'showmsg']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-expiry{ color: {{expiryFontColor}}; }',
                ]
            ],
			'scopy' => true,
        ],
        'daysTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_1 h6{ color: {{daysTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(1) .rotor-group-heading:before{ color: {{daysTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .day .progressbar-text .label{ color: {{daysTextColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'daysBorderColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_1{ border-color: {{daysBorderColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .day svg path:last-child{ stroke: {{daysBorderColor}} }',
                ],
            ],
			'scopy' => true,
        ],
        'daysBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
                'bgType' => 'color',
                'videoSource' => 'local',
                'bgDefaultColor' => '',
                'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_1',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(1) .rotor,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(1) .rotor .rotor-top,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(1) .rotor .rotor-bottom,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(1) .rotor .rotor-leaf figure',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(1)',
                ]
            ],
			'scopy' => true,
        ],
        'hourTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_2 h6{ color: {{hourTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(2) .rotor-group-heading:before{ color: {{hourTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .hour .progressbar-text .label{ color: {{hourTextColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'hourBorderColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_2{ border-color: {{hourBorderColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .hour svg path:last-child{ stroke: {{hourBorderColor}} }',
                ],
            ],
			'scopy' => true,
        ],
        'hourBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
                'bgType' => 'color',
                'videoSource' => 'local',
                'bgDefaultColor' => '',
                'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_2',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(2) .rotor,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(2) .rotor .rotor-top,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(2) .rotor .rotor-bottom,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(2) .rotor .rotor-leaf figure',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(2)',
                ]
            ],
			'scopy' => true,
        ],
        'minTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_3 h6{ color: {{minTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(3) .rotor-group-heading:before{ color: {{minTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .min .progressbar-text .label{ color: {{minTextColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'minBorderColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_3{ border-color: {{minBorderColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .min svg path:last-child{ stroke: {{minBorderColor}} }',
                ],
            ],
			'scopy' => true,
        ],
        'minBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
                'bgType' => 'color',
                'videoSource' => 'local',
                'bgDefaultColor' => '',
                'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_3',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(3) .rotor,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(3) .rotor .rotor-top,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(3) .rotor .rotor-bottom,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(3) .rotor .rotor-leaf figure',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(3)',
                ]
            ],
			'scopy' => true,
        ],
        'secTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_4 h6{ color: {{secTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(4) .rotor-group-heading:before{ color: {{secTextColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .sec .progressbar-text .label{ color: {{secTextColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'secBorderColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [
                        (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']
                    ],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_4{ border-color: {{secBorderColor}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .sec svg path:last-child{ stroke: {{secBorderColor}} }',
                ],
            ],
			'scopy' => true,
        ],
        'secBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
                'bgType' => 'color',
                'videoSource' => 'local',
                'bgDefaultColor' => '',
                'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div.count_4',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(4) .rotor,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(4) .rotor .rotor-top,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(4) .rotor .rotor-bottom,{{PLUS_WRAP}} .flipdown .rotor-group:nth-child(4) .rotor .rotor-leaf figure',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(4)',
                ]
            ],
			'scopy' => true,
        ],
        'padding' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}}.countdown-style-1 .tpgb-countdown-counter > div{ padding: {{padding}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group{ padding: {{padding}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part{ padding: {{padding}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'margin' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}}.countdown-style-1 .tpgb-countdown-counter > div{ margin: {{margin}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group{ margin: {{margin}}; }',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part{ margin: {{margin}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'border' => [
            'type' => 'object',
            'default' => (object) [
               'openBorder' => 0,
                'width' => (object) [
                    'md' => (object)[
                        'top' => '',
                        'left' => '',
                        'bottom' => '',
                        'right' => '',
                    ],
                    "unit" => "",
                ],
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div',
                ],
            ],
			'scopy' => true,
        ],
        'borderR' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}}.countdown-style-1 .tpgb-countdown-counter > div{ border-radius: {{borderR}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'boxShadow' => [
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
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter > div',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .flipdown .rotor-group',
                ],
                [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part',
                ],
            ],
			'scopy' => true,
        ],
        'strokeWidth' => [
            'type' => 'string',
            'default' => 5,
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part svg path:last-child{ stroke-width: {{strokeWidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'trailWidth' => [
            'type' => 'string',
            'default' => 3,
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part svg path:first-child{ stroke-width: {{trailWidth}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'daysTrailColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part .day svg path:first-child{ stroke: {{daysTrailColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'hourTrailColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part .hour svg path:first-child{ stroke: {{hourTrailColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'minTrailColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part .min svg path:first-child{ stroke: {{minTrailColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'secTrailColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part .sec svg path:first-child{ stroke: {{secTrailColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'daysBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(1)',
                ],
            ],
			'scopy' => true,
        ],
        'hourBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(2)',
                ],
            ],
			'scopy' => true,
        ],
        'minBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(3)',
                ],
            ],
			'scopy' => true,
        ],
        'secBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(4)',
                ],
            ],
			'scopy' => true,
        ],
        'daysBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(1):hover',
                ],
            ],
			'scopy' => true,
        ],
        'hourBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(2):hover',
                ],
            ],
			'scopy' => true,
        ],
        'minBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(3):hover',
                ],
            ],
			'scopy' => true,
        ],
        'secBorderH' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .counter-part:nth-child(4):hover',
                ],
            ],
			'scopy' => true,
        ],
        'msgAllign' => [
            'type' => 'object',
	        'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown{ text-align : {{msgAllign}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgpadding' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text{ padding : {{msgpadding}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgmargin' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count{ margin : {{msgmargin}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxtColor' => [
            'type'=> 'string',
            'default'=> '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text{ color : {{msgtxtColor}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxtBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxtBorder' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxtBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text{ border-radius : {{msgtxtBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxtBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxthvrColor' => [
            'type'=> 'string',
            'default'=> '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-text{ color : {{msgtxthvrColor}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxthvrBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxthvrBor' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxthvBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-text{ border-radius : {{msgtxthvBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'msgtxthvrBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-text',
                ],
            ],
            'scopy' => true,
        ],
       'fnumTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
       ],
       'fnumpadding' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter{ padding : {{fnumpadding}} }',
                ],
            ],
            'scopy' => true,
        ],
        'fnumColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter{ color : {{fnumColor}} }',
                ],
            ],
            'scopy' => true,
        ],
        'fnumBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'fnumBorder' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'fnumBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter{ border-radius : {{fnumBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'fnumBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'fnumhvrColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-visiblecounter{ color : {{fnumhvrColor}} }',
                ],
            ],
            'scopy' => true,
        ],
        'fnumhvrBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'fnumhvrBor' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'fnumhvBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-visiblecounter{ border-radius : {{fnumhvBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'fnumhvrBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown .tpgb-number-count:hover .tpgb-fake-visiblecounter',
                ],
            ],
            'scopy' => true,
        ],
        'bgPadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tp-countdown { padding : {{bgPadding}} }',
                ],
            ],
            'scopy' => true,
        ],
        'bgMargin' => [
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
                    'selector' => '{{PLUS_WRAP}}.tp-countdown{ margin : {{bgMargin}} }',
                ],
            ],
            'scopy' => true,
        ],
        'countBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown',
                ],
            ],
            'scopy' => true,
        ],
        'countBorder' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown',
                ],
            ],
            'scopy' => true,
        ],
        'countBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown { border-radius : {{countBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'countBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown ',
                ],
            ],
            'scopy' => true,
        ],
        'counthvrBg' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBg' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown:hover',
                ],
            ],
            'scopy' => true,
        ],
        'counthvrBor' => [
            'type'=> 'object',
            'default'=> (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown:hover',
                ],
            ],
            'scopy' => true,
        ],
        'counthvBradius' => [
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
                    'condition' => [(object) ['key' => 'countdownSelection', 'relation' => '==', 'value' => 'numbers']],
                    'selector' => '{{PLUS_WRAP}}.tp-countdown:hover{ border-radius : {{counthvBradius}} }',
                ],
            ],
            'scopy' => true,
        ],
        'counthvrBsha' => [
            'type'=> 'object',
            'default'=> (object) [
                'openShadow' => 0,
            ],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tp-countdown:hover',
                ],
            ],
            'scopy' => true,
        ],
        'labelpadding' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .days_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .hours_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .minutes_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .seconds_ref{ padding : {{labelpadding}} }',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter.flipdown .rotor-group-heading{ padding : {{labelpadding}} }',
                ],
            ],
        ],
        'labelMargin' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter .days_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .hours_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .minutes_ref,{{PLUS_WRAP}} .tpgb-countdown-counter .seconds_ref{ margin : {{labelMargin}} }',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-countdown-counter.flipdown .rotor-group-heading{ margin : {{labelMargin}} }',
                ],
            ],
        ],
    ];
    
    $attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-countdown', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_countdown_callback'
    ));
}
add_action('init', 'tpgb_tp_countdown_render');