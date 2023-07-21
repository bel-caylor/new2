<?php
/* Block : Mailchimp
 * @since : 2.0.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_mailchimp_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$nameIconType = (!empty($attributes['nameIconType'])) ? $attributes['nameIconType'] : 'fontAwesome';
	$nameIcon = (!empty($attributes['nameIcon'])) ? $attributes['nameIcon'] : '';
	$DFirstName = (!empty($attributes['DFirstName'])) ? $attributes['DFirstName'] : false;
	$fNameField = (!empty($attributes['fNameField'])) ? $attributes['fNameField'] : '';
	$DLastName = (!empty($attributes['DLastName'])) ? $attributes['DLastName'] : false;
	$lNameField = (!empty($attributes['lNameField'])) ? $attributes['lNameField'] : '';
	$DBirthField = (!empty($attributes['DBirthField'])) ? $attributes['DBirthField'] : false;
	$monthField = (!empty($attributes['monthField'])) ? $attributes['monthField'] : '';
	$dayField = (!empty($attributes['dayField'])) ? $attributes['dayField'] : '';
	$DPhoneField = (!empty($attributes['DPhoneField'])) ? $attributes['DPhoneField'] : false;
	$phoneField = (!empty($attributes['phoneField'])) ? $attributes['phoneField'] : '';
	$emailIconType = (!empty($attributes['emailIconType'])) ? $attributes['emailIconType'] : 'fontAwesome';
	$emailIcon = (!empty($attributes['emailIcon'])) ? $attributes['emailIcon'] : '';
	$emailField = (!empty($attributes['emailField'])) ? $attributes['emailField'] : '';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'none';
	$iconPosition = (!empty($attributes['iconPosition'])) ? $attributes['iconPosition'] : 'iconRight';
	$btnIcon = (!empty($attributes['btnIcon'])) ? $attributes['btnIcon'] : '';
	$btnName = (!empty($attributes['btnName'])) ? $attributes['btnName'] : '';
	$gdprSub = (!empty($attributes['gdprSub'])) ? $attributes['gdprSub'] : '';
	$gdprCompli =  (!empty($attributes['gdprCompli'])) ? 'yes' : 'no';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$ariaLabelT = (!empty($attributes['ariaLabel'])) ? esc_attr($attributes['ariaLabel']) : ((!empty($btnName)) ? esc_attr($btnName) : esc_attr__("Button", 'tpgbp'));

	$redirect_thankyou='';
	if(!empty($attributes["thankYouPage"])){
		$redirect_thankyou = (isset($attributes['thankYouPageLink']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['thankYouPageLink']) : (!empty($attributes['thankYouPageLink']['url']) ? $attributes['thankYouPageLink']['url'] : '');
	}
	
	$settings =array();
	$settings['loading'] = (!empty($attributes['loadingMessage'])) ? $attributes['loadingMessage'] : '';
	$settings['incorrect'] = (!empty($attributes['incorrectEmail'])) ? $attributes['incorrectEmail'] : '';
	$settings['gdprerrorMsg'] = (!empty($attributes['gdpError'])) ? $attributes['gdpError'] : '';
	$settings['success'] = (!empty($attributes['successMessage'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['successMessage']) : '';
	$settings['pending'] = (!empty($attributes['mDoubleOpt']) && !empty($attributes['msgDouble'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['msgDouble']) : '';
	$settings_opt = json_encode($settings);
	
	//Gdpr Checkbox
	$getgdprCom = '';
	if($gdprCompli == 'yes'){
		$getgdprCom .= '<div class="tpgb-gdpr-wrap">';
			$getgdprCom .= '<input type="checkbox" name="tpgb_gdpr_check" id="tpgbgppr" class="tpgb-gdpr-checkbox">';
			$getgdprCom .= '<label class="tpgb-gdpr-label" for="tpgbgppr"> </label>';
			if(!empty($gdprSub)){
				$getgdprCom .= '<label for="tpgbgppr" class="tpgb-gdpr-desc">'.wp_kses_post($gdprSub).'</label>';
			}
		$getgdprCom .= '</div>';
	}

    $output .= '<div class="tpgb-mailchimp tpgb-relative-block tpgb-block-'.esc_attr($block_id).' form-'.esc_attr($styleType).' '.esc_attr($blockClass).'">';
		$output .='<form action="'.site_url().'/wp-admin/admin-ajax.php" class="tpgb-mailchimp-form tpgb-mail-'.esc_attr($block_id).' text-center text--tablet text--mobile" data-id="'.esc_attr($block_id).'" data-thank-you="'.esc_attr($redirect_thankyou).'" data-mail-option=\'' . $settings_opt . '\'>';
			$output .='<div class="plus-newsletter-input-wrapper">';
				
				if($styleType!='style-1'){
					if($styleType=='style-2' && !empty($DFirstName) && $nameIconType=='fontAwesome'){
						$output .='<span class="prefix-icon">';
							$output .='<i class="'.esc_attr($nameIcon).'"></i>';
						$output .='</span>';
					}
					if(($styleType=='style-2' || $styleType=='style-3') && !empty($DFirstName)){
						$output .='<input type="text" name="FNAME" placeholder="'.esc_html($fNameField).'" required class="form-control tp-mailchimp-first-name"/>';
					}
					if($styleType=='style-3' && !empty($DLastName)){
						$output .='<input type="text" name="LNAME" placeholder="'.esc_html($lNameField).'" required class="form-control tp-mailchimp-last-name"/>';
					}
					if($styleType=='style-3' && !empty($DBirthField)){
						$output .='<input type="number" name="BIRTHMONTH" placeholder="'.esc_html($monthField).'" required class="form-control tp-mailchimp-birth-month" min="1" max="12"/>';
						$output .='<input type="number" name="BIRTHDAY" placeholder="'.esc_html($dayField).'" required class="form-control tp-mailchimp-birth-day" min="01" max="31"/>';
					}
					if($styleType=='style-3' && !empty($DPhoneField)){
						$output .='<input type="text" name="PHONE" placeholder="'.esc_html($phoneField).'" required class="form-control tp-mailchimp-phone"/>';
					}
				}
				
				if($styleType!='style-3' && $emailIconType=='fontAwesome'){
					$output .='<span class="prefix-icon">';
						$output .='<i class="'.esc_attr($emailIcon).'"></i>';
					$output .='</span>';
				}
				$output .='<input type="email" name="email" placeholder="'.esc_html($emailField).'" required="" class="form-control tp-mailchimp-email"/>';
				
				if(!empty($attributes['mDoubleOpt'])){
					$output .='<input type="hidden" name="mc_double_opt" value="pending" />';
				}
				
				if((!empty($attributes["mGroup"])) && !empty($attributes["mGroupIds"])){
					$catIds = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes["mGroupIds"]);
					$output .='<input type="hidden" name="mc_group_ids" value="'.esc_attr($catIds).'" />';
				}
				if((!empty($attributes["mTags"])) && !empty($attributes["mTagsIds"])){
					$tagIds = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes["mTagsIds"]);
					$output .='<input type="hidden" name="mc_tags_ids" value="'.esc_attr($tagIds).'" />';
				}
				
				$output .='<input type="hidden" name="action" value="tpgb_mailchimp_subscribe"/>';
				
				if($styleType=='style-3'){
					$output .= $getgdprCom;
				}

				$output .='<button class="subscribe-btn-submit" aria-label="'.$ariaLabelT.'">';
					if($iconType=='fontAwesome' && $iconPosition=='iconLeft'){
						$output .='<span class="subscribe-btn-icon btn-before">';
							$output .='<i class="'.esc_attr($btnIcon).'" aria-hidden="true"></i>';
						$output .='</span>';
					}
					$output .= wp_kses_post($btnName);
					if($iconType=='fontAwesome' && $iconPosition=='iconRight'){
						$output .='<span class="subscribe-btn-icon btn-after">';
							$output .='<i class="'.esc_attr($btnIcon).'" aria-hidden="true"></i>';
						$output .='</span>';
					}
				$output .='</button>';
				
			$output .= '</div>';
			if($styleType!='style-3'){
				$output .= $getgdprCom;
			}
			
			$output .='<input type="hidden" name="tpgb_gdpr" value="'.esc_attr($gdprCompli).'"/>';
			$output .='<div class="tpgb-notification"><div class="subscribe-response"></div></div>';
			
		$output .='</form>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_mailchimp() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'styleType' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'Alignment' => [
				'type' => 'object',
				'default' => 'center',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control{ text-align: {{Alignment}}; }',
					],
				],
				'scopy' => true,
			],
			'DFirstName' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'fNameField' => [
				'type' => 'string',
				'default' => 'Enter First Name',	
			],
			'fNameWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ] , ['key' => 'DFirstName', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-first-name{ width: {{fNameWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'DLastName' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'lNameField' => [
				'type' => 'string',
				'default' => 'Enter Last Name',	
			],
			'lNameWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ] , ['key' => 'DLastName', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-last-name{ width: {{lNameWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'DBirthField' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'monthField' => [
				'type' => 'string',
				'default' => 'MM',	
			],
			'dayField' => [
				'type' => 'string',
				'default' => 'DD',	
			],
			'birthFWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DBirthField', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-birth-day , {{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-birth-month{ width: {{birthFWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'DPhoneField' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'phoneField' => [
				'type' => 'string',
				'default' => '8882229999',	
			],
			'phoneFWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DPhoneField', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-phone{ width: {{phoneFWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'nameIconType' => [
				'type' => 'string',
				'default' => 'fontAwesome',	
			],
			'nameIcon' => [
				'type'=> 'string',
				'default'=> 'far fa-user',
			],
			'emailField' => [
				'type' => 'string',
				'default' => 'Enter Email Address',	
			],
			'emailIconType' => [
				'type' => 'string',
				'default' => 'fontAwesome',	
			],
			'emailIcon' => [
				'type'=> 'string',
				'default'=> 'far fa-envelope',
			],
			'emailFWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .tpgb-mailchimp-form input.form-control.tp-mailchimp-email{ width: {{emailFWidth}}; }',
					],
				],
				'scopy' => true,
			],
			
			'mGroup' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'mGroupIds' => [
				'type' => 'string',
				'default' => '',	
			],
			'mTags' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'mTagsIds' => [
				'type' => 'string',
				'default' => '',	
			],
			'mDoubleOpt' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'msgDouble' => [
				'type' => 'string',
				'default' => 'Thanks for Subscribing with us. Please Check Email and Confirm to Subscribe.',	
			],
			
			'btnName' => [
				'type' => 'string',
				'default' => 'Subscribe',	
			],
			'ariaLabel' => [
				'type' => 'string',
				'default' => '',	
			],
			'iconType' => [
				'type' => 'string',
				'default' => 'none',	
			],
			'btnIcon' => [
				'type'=> 'string',
				'default'=> 'fa fa-chevron-right',
			],
			'iconPosition' => [
				'type' => 'string',
				'default' => 'iconRight',
				'scopy' => true,
			],
			'iconSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome']],
						'selector' => '{{PLUS_WRAP}} .subscribe-btn-icon.btn-before { margin-right: {{iconSpace}}; } {{PLUS_WRAP}} .subscribe-btn-icon.btn-after { margin-left: {{iconSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'iconSize' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'fontAwesome']],
						'selector' => '{{PLUS_WRAP}} .subscribe-btn-icon { font-size: {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'btnWidth' => [
				'type' => 'object',
				'default' => ['md' => '','unit' => '%'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 button.subscribe-btn-submit{ width: {{btnWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'buttonAlign' => [
				'type' => 'object',
				'default' => 'center',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 .subscribe-btn-submit{ float: {{buttonAlign}}; display: block; margin: 0 auto; margin-top: 10px;}',
					],
				],
				'scopy' => true,
			],
			'thankYouPage' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'thankYouPageLink' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',	
					'target' => '',	
					'nofollow' => ''
				],
			],
			'loadingMessage' => [
				'type' => 'string',
				'default' => 'Please wait..',	
			],
			'incorrectEmail' => [
				'type' => 'string',
				'default' => 'Incorrect email address.',	
			],
			'successMessage' => [
				'type' => 'string',
				'default' => 'Thank You for subscribing. Please check mailbox and confirm your subscription.',	
			],
			'prefixIconSize' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper span.prefix-icon { font-size: {{prefixIconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'iconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper span.prefix-icon { color: {{iconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'prefixIconAdjust' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper span.prefix-icon { margin-top: {{prefixIconAdjust}}; }',
					],
				],
				'scopy' => true,
			],
			'fieldTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper input.form-control',
					],
				],
				'scopy' => true,
			],
			'placeHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} input.form-control::placeholder { color: {{placeHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'innerPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control{padding: {{innerPadding}};}',
					],
				],
				'scopy' => true,
			],
			'outerPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control{margin: {{outerPadding}};}',
					],
				],
				'scopy' => true,
			],
			'textNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control { color: {{textNormalColor}}; }',
					],
				],
				'scopy' => true,
			],
			'normalBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'videoSource' => 'local',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper input.form-control',
					],
				],
				'scopy' => true,
			],
			'bgNormalB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 1,
					'type' => 'solid',
					'color' => '#d3d3d3',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						"unit" => 'px',
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control',
					],
				],
				'scopy' => true,
			],
			'BNormalRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control{border-radius:  {{BNormalRadius}};}',
					],
				],
				'scopy' => true,
			],
			'normalboxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => true,
					'inset' => 0,
					'horizontal' => 0,
					'vertical' => 3,
					'blur' => 20,
					'spread' => -10,
					'color' => "#d3d3d3",
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control',
					],
				],
				'scopy' => true,
			],
			'textFocusColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control:focus { color: {{textFocusColor}}; }',
					],
				],
				'scopy' => true,
			],
			'focusBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'videoSource' => 'local',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp .plus-newsletter-input-wrapper input.form-control:focus',
					],
				],
				'scopy' => true,
			],
			'bgFocusB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
						'color' => '',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						'unit' => 'px',
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control:focus',
					],
				],
				'scopy' => true,
			],
			'BFocusRadius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control:focus{border-radius:  {{BFocusRadius}};}',
					],
				],
				'scopy' => true,
			],
			'focusboxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form input.form-control:focus',
					],
				],
				'scopy' => true,
			],
			'btnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'btnName', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit',
					],
				],
				'scopy' => true,
			],
			'btnPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '15',
						"right" => '25',
						"bottom" => '15',
						"left" => '25',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit {padding: {{btnPadding}};}',
					],
				],
				'scopy' => true,
			],
			'btnMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					'unit' => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit {margin: {{btnMargin}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-2 button.subscribe-btn-submit {margin: {{btnMargin}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 button.subscribe-btn-submit {margin: {{btnMargin}};}',
					],
				],
				'scopy' => true,
			],
			'btnTextNmlClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit { color: {{btnTextNmlClr}}; }',
					],
				],
				'scopy' => true,
			],
			'btnNormalBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 1,
					'bgType' => 'color',
					'videoSource' => 'local',
					'bgDefaultColor' => '#cd2653',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit',
					],
				],
				'scopy' => true,
			],
			'btnNormalB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
						'color' => '',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						'unit' => 'px',
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit',
					],
				],
				'scopy' => true,
			],
			'btnNmlBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit{border-radius: {{btnNmlBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'normalbtnShadow' => [
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
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit',
					],
				],
				'scopy' => true,
			],
			'btnTextHvrClr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit:hover { color: {{btnTextHvrClr}}; }',
					],
				],
				'scopy' => true,
			],
			'btnHoverBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
					'videoSource' => 'local',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
					'overlayBg' => '',
					'overlayBgOpacity' => '',
					'bgGradientOpacity' => ''
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit:hover',
					],
				],
				'scopy' => true,
			],
			'btnHoverB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
						'color' => '',
					'width' => (object) [
						'md' => (object)[
							'top' => '1',
							'left' => '1',
							'bottom' => '1',
							'right' => '1',
						],
						'unit' => 'px',
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit:hover',
					],
				],
				'scopy' => true,
			],
			'btnHvrBRadius' => [
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
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit:hover{border-radius: {{btnHvrBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'hoverbtnShadow' => [
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
						'selector' => '{{PLUS_WRAP}} button.subscribe-btn-submit:hover',
					],
				],
				'scopy' => true,
			],
			'resMsgTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-notification',
					],
				],
				'scopy' => true,
			],
			'resMsgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-notification { color: {{resMsgColor}}; }',
					],
				],
				'scopy' => true,
			],
			'resLoadingBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-notification { background: {{resLoadingBG}}; }',
					],
				],
				'scopy' => true,
			],
			'resSuccessBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-notification.success-msg { background: {{resSuccessBG}}; }',
					],
				],
				'scopy' => true,
			],
			'formMaxWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mailchimp-form { max-width : {{formMaxWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'mailCalign' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ], ['key' => 'mailCalign', 'relation' => '==', 'value' => 'left' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 form.tpgb-mailchimp-form { margin-left: 0; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ], ['key' => 'mailCalign', 'relation' => '==', 'value' => 'center' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 form.tpgb-mailchimp-form { margin: 0 auto; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ], ['key' => 'mailCalign', 'relation' => '==', 'value' => 'right' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-mailchimp.form-style-3 form.tpgb-mailchimp-form {  margin-right: 0; }',
					],
				],
				'scopy' => true,
			],

			'gdprCompli' => [
				'type' => 'boolean',
				'default' => false,
			],
			'gdprSub' => [
				'type' => 'string',
				'default' => 'You Must Accept The Terms Of Service',
			],
			'gdpError' => [
				'type' => 'string',
				'default' => 'Please Check Required.',
			],
			'gdprtxtTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-desc',
					],
				],
				'scopy' => true,	
			],
			'gdprtxtColor' => [
				'type'=> 'string',
				'default'=> '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-desc{ color : {{gdprtxtColor}}; }',
					],
				],
				'scopy' => true,
			],
			'gdtermsBg' => [
				'type'=> 'string',
				'default'=> '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-label{ background : {{gdtermsBg}}; }',
					],
				],
				'scopy' => true,
			],
			'gdtermsBor' => [
				'type'=> 'string',
				'default'=> '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-label{ border-color : {{gdtermsBor}} }',
					],
				],
				'scopy' => true,
			],
			'gdtermicon' => [
				'type'=> 'string',
				'default'=> '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-label::before,{{PLUS_WRAP}} .tpgb-gdpr-wrap .tpgb-gdpr-label::after{ background : {{gdtermicon}}; }',
					],
				],
				'scopy' => true,
			],
			'gdprPadd' => [
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
						'condition' => [(object) ['key' => 'gdprCompli', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-gdpr-wrap { padding : {{gdprPadd}}; }',
					],
				],
				'scopy' => true,
			],
		);
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-mailchimp', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_mailchimp_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_mailchimp' );

function tpgb_mailchimp_subscribe(){
	$options = get_option( 'tpgb_connection_data' );
	$list_id = (!empty($options['mailchimp_id'])) ? $options['mailchimp_id'] : '';
	$api_key = (!empty($options['mailchimp_api'])) ? $options['mailchimp_api'] : '';

	$FNAME=$LNAME=$BIRTHDAY=$PHONE='';	
	$chimp_field = array();
	if(!empty($_POST['FNAME'])){
		$FNAME = sanitize_text_field( wp_unslash( $_POST['FNAME'] ) );
		$chimp_field['FNAME'] =$FNAME;
	}
	if(!empty($_POST['LNAME'])){
		$LNAME = sanitize_text_field(  wp_unslash( $_POST['LNAME'] ) );
		$chimp_field['LNAME'] =$LNAME;
	}
	if(!empty($_POST['BIRTHDAY']) && !empty($_POST['BIRTHMONTH'])){
		$BIRTHDAY = sanitize_text_field( wp_unslash( $_POST['BIRTHMONTH'] ) ) . '/' . sanitize_text_field( wp_unslash($_POST['BIRTHDAY']) );
		$chimp_field['BIRTHDAY'] =$BIRTHDAY;
	}
	if(!empty($_POST['PHONE'])){
		$PHONE = wp_unslash( $_POST['PHONE'] );
		$chimp_field['PHONE'] =$PHONE;
	}
	
	$mc_status = 'subscribed';
	if(!empty($_POST['mc_double_opt']) && $_POST['mc_double_opt']=='pending'){
		$mc_status = 'pending';
	}

	$mc_group_ids = !empty($_POST['mc_group_ids']) ? sanitize_text_field( $_POST['mc_group_ids'] ) : '';
	
	$mc_tags_ids = !empty($_POST['mc_tags_ids']) ? sanitize_text_field( $_POST['mc_tags_ids'] ) : '';
	
	if( isset($_POST['tpgb_gdpr']) && ( ( $_POST['tpgb_gdpr'] == 'no' ) || ( $_POST['tpgb_gdpr'] == 'yes' && isset( $_POST['tpgb_gdpr_check'] ) && $_POST['tpgb_gdpr_check'] == 'on' ) )){
		$result = json_decode( Tpgbp_Pro_Blocks_Helper::tpgb_mailchimp_subscriber_message($_POST['email'], $mc_status, $list_id, $api_key, $chimp_field,$mc_group_ids, $mc_tags_ids ) );
	}else{
		echo 'please-check';
		die;
	}
	
	if( isset($result->status) && !empty($result->status) ){
		if( $result->status == 400 ){
			echo 'incorrect';
		} else if( $result->status == 'subscribed' ){
			echo 'correct';
		} else if( $result->status == 'pending' ){
			echo 'pending';
		} else {
			echo 'not-verify';
		}
	}else{
		echo 'not-verify';
	}
	die;
}
add_action('wp_ajax_tpgb_mailchimp_subscribe','tpgb_mailchimp_subscribe');
add_action('wp_ajax_nopriv_tpgb_mailchimp_subscribe', 'tpgb_mailchimp_subscribe');