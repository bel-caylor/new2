<?php
/* Block : LottieFiles Animation
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_lottiefiles_render_callback( $attributes, $content) {
	$lottie = '';
	$block_id = !empty($attributes['block_id']) ? $attributes['block_id'] : uniqid("title");
	$JSONInput = !empty($attributes['JSONInput']) ? $attributes['JSONInput'] : 'code';
	$JSONURL = (isset($attributes['JSONURL']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['JSONURL']) : (!empty($attributes['JSONURL']['url']) ? $attributes['JSONURL']['url'] : '');
	$JSONCode =	!empty($attributes['JSINCode']) ? $attributes['JSINCode'] : '';
	$play_action_on = !empty($attributes['PlayOn']) ? $attributes['PlayOn'] : '';
	$loopT = !empty($attributes['LoopA']) ? $attributes['LoopA'] : false;
	$StartTimeT = !empty($attributes['STimeT']) ? $attributes['STimeT'] : false;
	$EndTimeT = !empty($attributes['EndTimeT']) ? $attributes['EndTimeT'] : false;
	$HeadingT = !empty($attributes['HeadingT']) ? $attributes['HeadingT'] : false;
	$DescriptionT = !empty($attributes['DescriptionT']) ? $attributes['DescriptionT'] : false;
	$URLT = !empty($attributes['URLT']) ? $attributes['URLT'] : false;
	$URLType = !empty($attributes['URLType']) ? $attributes['URLType'] : 'normal';
	$URLN =	(!empty($attributes['URLN']) && !empty($attributes['URLN']['url'])) ? $attributes['URLN']['url'] : '';	
	$URLD = (!empty($attributes['URLD']) && !empty($attributes['URLD']['url'])) ? $attributes['URLD']['url'] : '';
	$Delay = !empty($attributes['Delay']) ? (int) $attributes['Delay'] : 1000;
	$bm_scrollbased = !empty($attributes['OnHeight']) ? $attributes['OnHeight'] : 'bm_custom';
	$bm_section_duration = !empty($attributes['Duration']['md']) ? $attributes['Duration']['md'] : 500;
	$bm_section_offset = !empty($attributes['Offset']['md']) ? $attributes['Offset']['md'] : 0;
	$options = array();
	$anim_renderer = !empty($attributes['Renderer']) ? $attributes['Renderer'] : 'svg';
	$content_align = !empty($attributes['CAlignment']) ? $attributes['CAlignment'] : 'center';
	$max_width = !empty($attributes["MaxWidth"]['md']) ? $attributes["MaxWidth"]['md'] : '100%';
	$minimum_height = !empty($attributes["MinHeight"]['md']) ? $attributes["MinHeight"]['md'] : '';
	$speed = !empty($attributes['PlaySpeed']) ? (float) $attributes['PlaySpeed'] : 0.5;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$bm_start_time=$bm_end_time='';
	if(!empty($StartTimeT)){
		$bm_start_time = (!empty($attributes['STime'])) ? (float) $attributes['STime'] : 1;
	}
	if(!empty($EndTimeT)){
		$bm_end_time = (!empty($attributes['EndTime'])) ? (float) $attributes['EndTime'] : 100;
	} 

	$loop=$loopT;
	if(!empty($loopT)){
		$loop = (!empty($attributes['TotalLps'])) ? $attributes['TotalLps'] - 1 : '';
	} 	// total loop

	$autoplay_viewport=$autostop_viewport=false;
	if( $play_action_on == 'viewport' ){
		$autoplay_viewport = true;
		$autostop_viewport = true;
	}

	$id = uniqid("title");
	$uid= uniqid();
		
	$options = array(
		'id'      				=> $uid,
		'container_id'      	=> $id,
		'autoplay_viewport' 	=> $autoplay_viewport,
		'autostop_viewport' 	=> $autostop_viewport,
		'loop'              	=> $loop,
		'width'             	=> $max_width,
		'height'            	=> $minimum_height,
		'lazyload'          	=> false,
		'playSpeed'          	=> $speed,
		'play_action' 			=> $play_action_on,
		'bm_scrollbased' 		=> $bm_scrollbased,
		'bm_section_duration' 	=> $bm_section_duration,
		'bm_section_offset' 	=> $bm_section_offset,
		'bm_start_time' 		=> $bm_start_time,
		'bm_end_time' 			=> $bm_end_time,
	);
	
	if (!empty($JSONCode) && $JSONInput == 'code') {
		$options['animation_data'] = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($JSONCode);
	}

	if (!isset($options['autoplay_onload'])) {
		$options['autoplay_onload'] = true;
	}	

	$options['renderer'] = $anim_renderer;

	$classes = '';
	if(!empty($anim_renderer)){
		$classes .= ' renderer-'.$anim_renderer;
	}

	$style_atts = '';
	if ( $anim_renderer == 'html' ) {
		$style_atts .= '.tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{position: relative;}';
	}
	
	if(( isset($content_align['md']) && !empty($content_align['md']) && $content_align[ 'md'] == 'right' )) {
		$style_atts .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{margin-left: auto;} } ';
	}
	if( isset($content_align['sm']) && !empty($content_align['sm']) && $content_align['sm'] == 'right'){
		$style_atts .= '@media (max-width: 1024px) and (min-width:768px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{margin-left: auto} } ';
	}
	if( isset($content_align['xs']) && !empty($content_align['xs']) && $content_align['xs'] == 'right'){
		$style_atts .= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{margin-left: auto} } ';
	}
	if(( isset($content_align['md']) && !empty($content_align['md']) && $content_align['md'] == 'center' )){
		$style_atts .= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{margin-left: auto; margin-right: auto;} }';
	}
	if( isset($content_align['sm']) && !empty($content_align['sm']) && $content_align['sm'] == 'center' ) {
		$style_atts .= '@media (max-width: 1024px) and (min-width:768px) { .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles.
		tpgb-bodymovin{margin-left: auto;margin-right: auto;} } ';
	}
	if( isset($content_align['xs']) && !empty($content_align['xs']) && $content_align['xs'] == 'center' ) {
		$style_atts .= '@media (max-width: 767px){ .tpgb-block-'.esc_attr($block_id).'.tpgb-lottiefiles .tpgb-bodymovin{margin-left: auto; margin-right: auto;} }';
	}

	if ( !empty($JSONURL) && $JSONInput == 'url' ) {
		$ext = pathinfo($JSONURL, PATHINFO_EXTENSION);
		if( $ext != 'json' ){
			$lottie .= '<h3 class="tpgb-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.", 'tpgbp').'</h3>';				
		}else{
			$options['json_url'] = esc_url($JSONURL);
		}
	}

	$lottie .= '<div class="tpgb-block-'.esc_attr($block_id).' tpgb-lottiefiles '.esc_attr($blockClass).'">';
			$settings_opt='';
			if( !empty($JSONCode) || !empty($JSONURL) ){
				$settings_opt .= ' data-settings="'.htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8').'"';
				$settings_opt .= ' data-editor-load="yes"';
				$settings_opt .= ' data-popup-load="yes"';
				$ariaLabel = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : esc_attr__('Animation Icon','tpgbp');

				if( $URLT && $URLType == 'normal' && !empty($URLN) ) {
					$NTarget = (!empty($attributes['URLN']) && !empty($attributes['URLN']['target'])) ? 'target="_blank"' : "";
					$NNofollow = (!empty($attributes['URLN']) && !empty($attributes['URLN']['nofollow'])) ? 'rel="nofollow"' : "";
					$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['URLN']);

					$lottie .='<a class="tpgb-bodymovin-link" href="'.esc_url($URLN).'" '.$NTarget.' '.$NNofollow.' '.$link_attr.' data-delay="'.esc_attr($Delay).'" aria-label="'.$ariaLabel.'">';
				}else if( $URLT && $URLType == 'dynamic' && !empty($URLD) ){
					$NTarget = (!empty($attributes['URLD']) && !empty($attributes['URLD']['target'])) ? 'target="_blank"' : "";
					$NNofollow = (!empty($attributes['URLD']) && !empty($attributes['URLD']['nofollow'])) ? 'rel="nofollow"' : "";
					$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['URLD']);

					$lottie .='<a class="tpgb-bodymovin-link" href="'.esc_url($URLD).'" '.$NTarget.' '.$NNofollow.' '.$link_attr.' data-delay="'.esc_attr($Delay).'" aria-label="'.$ariaLabel.'">';
				}

					$lottie .= '<div class="tpgb-lottiefile-hd">';
						$lottie .= '<div id="'.esc_attr($id).'" class="tpgb-bodymovin '.esc_attr($classes).' " '.$settings_opt.'>';
						$lottie .= '</div>';

							if( !empty($HeadingT) && !empty($attributes['HText']) ){
								$lottie .= '<div class="tpgb-lottiefile-heading">'.wp_kses_post($attributes['HText']).'</div>';
							}
							if( !empty($DescriptionT) && !empty($attributes['DText']) ){
								$lottie .= '<div class="tpgb-lottiefile-description">'.wp_kses_post($attributes['DText']).'</div>';
							}

					$lottie .= '</div>';

				if( $URLT && (!empty($URLN) || !empty($URLD)) ){
					$lottie .='</a>';
				}
			
			}else{
				$lottie .='<h3 class="tpgb-posts-not-found">'.esc_html__( "JSON Parse Not Working", 'tpgbp' ).'</h3>';
			}
		if(!empty($style_atts)){
			$lottie .= '<style>'.$style_atts.'</style>';
		}
    $lottie .= '</div>';
	
	$lottie = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $lottie);

    return $lottie;
}


function tpgb_tp_lottiefiles() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],			
			'JSONInput' => [
				'type' => 'string',
				'default' => 'url',	
			],
			'JSONURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://assets7.lottiefiles.com/packages/lf20_UlPcA57JIG.json',
					'target' => '',
					'nofollow' => ''
				],
			],
			'Popup' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'JSINCode' => [
				'type'=> 'string',
				'default'=> '',
			],

			'PlayOn' => [
				'type' => 'string',
				'default' => 'autoplay',	
			],			
			'LoopA' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'TotalLps' => [
				'type'=> 'string',
				'default'=> '',
			],
			'PlaySpeed' => [
				'type' => 'string',
				'default' => 0.5,
			],
			'OnHeight' => [
				'type' => 'string',
				'default' => 'bm_custom',	
			],
			'Duration' => [
				'type' => 'object',
				'default' => [ 
					'md' => 0,
					"unit" => 'px',
				],
			],
			'Offset' => [
				'type' => 'object',
				'default' => [
					'md' => 0,
					"unit" => 'px'
				],
				
			],
			'STimeT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'STime' => [
				'type'=> 'string',
				'default'=> '',
			],
			'EndTimeT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'EndTime' => [
				'type'=> 'string',
				'default'=> '',
			],
			'URLT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'URLType' => [
				'type' => 'string',
				'default' => 'normal',	
			],
			'URLN' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',
					'target' => '',
					'nofollow' => ''
				],
			],
			'URLD' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',
					'target' => '',
					'nofollow' => ''
				],
			],
			'HeadingT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'HText' => [
				'type'=> 'string',
				'default'=> 'Heading',
			],
			'DescriptionT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'DText' => [
				'type'=> 'string',
				'default'=> 'Description',
			],
			'Renderer' => [
				'type' => 'string',
				'default' => '',	
			],
			'Delay' => [
				'type' => 'string',
				'default' => '',
			],
			'ariaLabel' => [
				'type' => 'string',
				'default' => '',
			],
			'CAlignment' => [
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-lottiefiles .tpgb-bodymovin,{{PLUS_WRAP}}.tpgb-lottiefiles .tpgb-lottiefile-heading,{{PLUS_WRAP}}.tpgb-lottiefiles .tpgb-lottiefile-description{text-align:{{CAlignment}};}',
					],
				],
				'scopy' => true,
			],
			'MaxWidth' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}} .tpgb-bodymovin{max-width:{{MaxWidth}};}',
					],
				],
				'scopy' => true,
			],
			'MinHeight' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}} .tpgb-bodymovin{min-height:{{MinHeight}};}',
					],
				],
				'scopy' => true,
			],

			'HMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-heading{margin:{{HMargin}};}',
					],
				],
				'scopy' => true,
			],
			'HPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-heading{padding:{{HPadding}};}',
					],
				],
				'scopy' => true,
			],
			'HTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-heading',
					],
				],
				'scopy' => true,
			],
			'HNCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-heading{color:{{HNCr}};}',
					],
				],
				'scopy' => true,
			],
			'HNbgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-heading{background:{{HNbgCr}};}',
					],
				],
				'scopy' => true,
			],
			'HHCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover .tpgb-lottiefile-heading{color:{{HHCr}};}',
					],
				],
				'scopy' => true,
			],
			'HHbgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover .tpgb-lottiefile-heading{background:{{HHbgCr}};}',
					],
				],
				'scopy' => true,
			],

			'DMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-description{margin:{{DMargin}};}',
					],
				],
				'scopy' => true,
			],
			'DPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-description{padding:{{DPadding}};}',
					],
				],
				'scopy' => true,
			],
			'DTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-description',
					],
				],
				'scopy' => true,
			],
			'DNCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-description{color:{{DNCr}};}',
					],
				],
				'scopy' => true,
			],
			'DNbgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd .tpgb-lottiefile-description{background:{{DNbgCr}};}',
					],
				],
				'scopy' => true,
			],
			'DHCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover .tpgb-lottiefile-description{color:{{DHCr}};}',
					],
				],
				'scopy' => true,
			],
			'DHbgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover .tpgb-lottiefile-description{background:{{DHbgCr}};}',
					],
				],
				'scopy' => true,
			],

			'CBPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd{padding:{{CBPadding}};}',
					],
				],
				'scopy' => true,
			],
			'CBMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd{margin:{{CBMargin}};}',
					],
				],
				'scopy' => true,
			],

			'CBnBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd',
					],
				],
				'scopy' => true,
			],
			'CBnB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd',
					],
				],
				'scopy' => true,
			],
			'CBnBrs' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => ['top' => '', 'bottom' => '', 'left' => '', 'right' => ''],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd{border-radius:{{CBnBrs}};}',
					],
				],
				'scopy' => true,
			],
			'CBnBoxS' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd',
					],
				],
				'scopy' => true,
			],

			'CBhBg' => [
				'type' => 'object',
				'default' =>( object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover',
					],
				],
				'scopy' => true,
			],
			'CBhB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover',
					],
				],
				'scopy' => true,
			],
			'CBhBrs' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover{border-radius:{{CBhBrs}};}',
					],
				],
				'scopy' => true,
			],
			'CBhBoxS' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'HeadingT', 'relation' => '==', 'value' => true ],
										(object) ['key' => 'DescriptionT', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover',
					],
				],
				'scopy' => true,
			],

			'NFilter' => [
				'type' => 'object',
				'default' =>  [
					'openFilter' => false,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd,{{PLUS_WRAP}} .tpgb-bodymovin',
					],
				],
				'scopy' => true,
			],				
			'NOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd,{{PLUS_WRAP}} .tpgb-bodymovin{opacity:{{NOpacity}};}',
					],
				],
				'scopy' => true,
			],
			'TDuration' => [
				'type' => 'string',
				'default' => '',		
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd,{{PLUS_WRAP}} .tpgb-bodymovin{transition:{{TDuration}}s;}',
					],
				],
				'scopy' => true,
			],
			'HFilter' => [
				'type' => 'object',
				'default' => [
					'openFilter' => false,
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover,{{PLUS_WRAP}} .tpgb-bodymovin:hover',
					],
				],
				'scopy' => true,
			],
			'HOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],	
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-lottiefile-hd:hover,{{PLUS_WRAP}} .tpgb-bodymovin:hover{opacity:{{HOpacity}};}',
					],
				],
				'scopy' => true,
			],
			
		];
		
	$attributesOptions = array_merge($attributesOptions , $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-lottiefiles', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_lottiefiles_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_lottiefiles' );