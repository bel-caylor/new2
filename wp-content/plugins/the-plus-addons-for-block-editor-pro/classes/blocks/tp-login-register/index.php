<?php

/**
 * After rendring from the block editor display output on front-end
 */

function tpgb_tp_login_render_callback( $attributes, $content) {
	$output = '';

    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$formType = (!empty($attributes['formType'])) ? $attributes['formType'] : '';
	$formLayout = (!empty($attributes['formLayout'])) ? $attributes['formLayout'] : '';
	$loField = (!empty($attributes['loField'])) ? $attributes['loField'] : []; 
	$regisField = (!empty($attributes['regisField'])) ? $attributes['regisField'] : [];
	$loheading = (!empty($attributes['loheading'])) ? $attributes['loheading'] : '';
	$lopassLabel = (!empty($attributes['lopassLabel'])) ? $attributes['lopassLabel'] : '';
	$loplaceho = (!empty($attributes['loplaceho'])) ? $attributes['loplaceho'] : '';
	$cntAlign = (!empty($attributes['cntAlign'])) ? $attributes['cntAlign'] : '';
	$btnIcon = (!empty($attributes['btnIcon'])) ? $attributes['btnIcon'] : '';
	$btntxt = (!empty($attributes['btntxt'])) ? $attributes['btntxt'] : '';
	$loginTitle = (!empty($attributes['loginTitle'])) ? $attributes['loginTitle'] : '';
	$regisTitle = (!empty($attributes['regisTitle'])) ? $attributes['regisTitle'] : '';
	$blockTemp = (!empty($attributes['blockTemp'])) ? $attributes['blockTemp'] : '';
	$relodTxt = (!empty($attributes['relodTxt'])) ? $attributes['relodTxt'] : '';
	$resuTxt = (!empty($attributes['resuTxt'])) ? $attributes['resuTxt'] : '';
	$reemvalText = (!empty($attributes['reemvalText'])) ? $attributes['reemvalText'] : '';
	$reeroTxt = (!empty($attributes['reeroTxt'])) ? $attributes['reeroTxt'] : '';
	$reAction = (!empty($attributes['reAction'])) ? $attributes['reAction'] : '[]'; 
	$regdirectUrl = (!empty($attributes['regdirectUrl'])) ? $attributes['regdirectUrl'] : ''; 
  	$regemailSub = (!empty($attributes['regemailSub'])) ? $attributes['regemailSub'] : '';
	$regemailMsg = (!empty($attributes['regemailMsg'])) ? $attributes['regemailMsg'] : '';
	$lobtntxt = (!empty($attributes['lobtntxt'])) ? $attributes['lobtntxt'] : '';
	$lobtnIcon = (!empty($attributes['lobtnIcon'])) ? $attributes['lobtnIcon'] : '';
	$lorehide = (!empty($attributes['lorehide'])) ? 'yes' : 'no';
	$stpassreq = (!empty($attributes['stpassreq'])) ? 'yes' : 'no';
	$backArrow = (!empty($attributes['backArrow'])) ? $attributes['backArrow'] : '';
	$btnType = (!empty($attributes['btnType'])) ? $attributes['btnType'] : '';
	$tab1btnTxt = (!empty($attributes['tab1btnTxt'])) ? $attributes['tab1btnTxt'] : '';
	$tab2btnTxt = (!empty($attributes['tab2btnTxt'])) ? $attributes['tab2btnTxt'] : '';
	$dactiveTab = (!empty($attributes['dactiveTab'])) ? $attributes['dactiveTab'] : '';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$recript = '';
	$dataAttr = '';
	$honeypot = false;

	$options = get_option( 'tpgb_connection_data' );
	$list_id = (!empty($options['mailchimp_id'])) ? $options['mailchimp_id'] : '';
	$api_key = (!empty($options['mailchimp_api'])) ? $options['mailchimp_api'] : '';
	$reCaptch_key = (!empty($options['tpgb_site_key_recaptcha'])) ? $options['tpgb_site_key_recaptcha'] : '';

	$losrecaptch = (!empty($attributes['losrecaptch'])) ? 'yes' : 'no';
	$lorecaposition = (!empty($attributes['lorecaposition'])) ? $attributes['lorecaposition'] : '';

	//Set Json Data
	if($formType == 'register' || $formType == 'login-register'){
		$rehtmlTxt = [];
		$rehtmlTxt['blockId'] = $block_id;
		$rehtmlTxt['regloadText'] = '<span class="loading-spinner-reg"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span>'.wp_kses_post($relodTxt).'';
		$rehtmlTxt['emailvali'] = '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>'.wp_kses_post($reemvalText).'';
		$rehtmlTxt['incorrectMsg'] = '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>'.wp_kses_post($reeroTxt).'';
		$rehtmlTxt['succMsg'] = '<span class="loading-spinner-reg"><i class="fas fa-check-circle"></i></span>'.wp_kses_post($resuTxt).'';


		if (is_array($reAction) || is_object($reAction)) {
			foreach ($reAction as $value) {
				$regaction[] = $value['value'];
			}
			$rehtmlTxt['regaction'] = $regaction;
		}
		if(!empty($rehtmlTxt['regaction'])){
			if(in_array( 'redirect' ,$rehtmlTxt['regaction'])){
				$rehtmlTxt['regredireUrl'] = (!empty($regdirectUrl['url'])) ? $regdirectUrl['url'] : '';
			}
			if(in_array( 'sendemail' ,$rehtmlTxt['regaction'])){
				$rehtmlTxt['emailData'] = [
					'subject' => $regemailSub,
					'emailBody' => $regemailMsg,
				];
			}
		}

		// register Json
		
		
		if(!empty($regisField)){
			foreach($regisField as $index => $RegField){
				$recaptchaPos = ( isset( $RegField['recaposition'] ) && !empty( $RegField['recaposition'] ) ) ? $RegField['recaposition']  : '';
				if( $RegField['regisfName'] == 'mailChimp'){
					$rehtmlTxt['mailChimpData'] = [
						'doubleOpt' => (!empty($RegField['doOptin'])) ? 'yes' : 'no',
						'TagId' => (!empty($RegField['mailTag']) && !empty($RegField['tagId'] )) ? $RegField['tagId'] : '',
						'apiKey' => $api_key,
						'listId' => $list_id,
					];
				}
				if( $RegField['regisfName'] == 'recaptcha'){
					$rehtmlTxt['recaptchEn'] = 'yes';
					$rehtmlTxt['recaptchaKey'] = $reCaptch_key;
					$rehtmlTxt['recaptchaPos'] = $recaptchaPos;
					$recript = '<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=tpgb_onLoadReCaptcha"></script>';
				}
				if($RegField['regisfName'] == 'honeypot'){
					$honeypot = true;
				}
			}
		}
		$rehtmlTxt = htmlspecialchars(json_encode($rehtmlTxt), ENT_QUOTES, 'UTF-8');

		$dataAttr .= 'data-registermsgHtml=\'' .$rehtmlTxt. '\' ';
	}
	if($formType == 'login' || $formType == 'login-register'){
		$loglodTxt = (!empty($attributes['loglodTxt'])) ? $attributes['loglodTxt'] : '';
		$losuTxt = (!empty($attributes['losuTxt'])) ? $attributes['losuTxt'] : '';
		$loemvalText = (!empty($attributes['loemvalText'])) ? $attributes['loemvalText'] : '';
		$loeroTxt = (!empty($attributes['loeroTxt'])) ? $attributes['loeroTxt'] : '';
		$rediaflogin = (!empty($attributes['rediaflogin'])) ? $attributes['rediaflogin'] : '[]'; 
		$redirctUrl = (!empty($attributes['redirctUrl'])) ? $attributes['redirctUrl'] : ''; 

		$loginhtmlTxt = [];
		$loginhtmlTxt['blockId'] = $block_id;
		$loginhtmlTxt['loglodTxt'] = '<span class="loading-spinner-reg"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span>'.wp_kses_post($loglodTxt).'';
		$loginhtmlTxt['loemvalText'] = '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>'.wp_kses_post($loemvalText).'';
		$loginhtmlTxt['loeroTxt'] = '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>'.wp_kses_post($loeroTxt).'';
		$loginhtmlTxt['losuTxt'] = '<span class="loading-spinner-reg"><i class="fa-regular fa-circle-check" aria-hidden="true"></i></i></span>'.wp_kses_post($losuTxt).'';
		$logAction = [];
		if ( ( is_array($rediaflogin) || is_object($rediaflogin) )  ) {

			foreach ($rediaflogin as $value) {
				if(!empty($value) && isset($value['value'])){
					$logAction[] = $value['value'];
				}
			}
			
			if( !empty($logAction) && in_array( 'redirect' ,$logAction)){
				$loginhtmlTxt['logdireUrl'] = (!empty($redirctUrl['url'])) ? $redirctUrl['url'] : '';
			}
		}
		
		$loginhtmlTxt = htmlspecialchars(json_encode($loginhtmlTxt), ENT_QUOTES, 'UTF-8');
		$dataAttr .= 'data-loginmsgHtml=\'' .$loginhtmlTxt. '\' ';
	}

	if($formType == 'login' || $formType == 'login-register' || $formType == 'forgot_password' ){
		$lostlodTxt = (!empty($attributes['lostlodTxt'])) ? $attributes['lostlodTxt'] : '';
		$lostuTxt = (!empty($attributes['lostuTxt'])) ? $attributes['lostuTxt'] : '';
		$losteroTxt = (!empty($attributes['losteroTxt'])) ? $attributes['losteroTxt'] : '';
		$cutemail = !empty($attributes['cutemail']) ? 'yes' : 'no' ;
		$lostemSub = (!empty($attributes['lostemSub'])) ? $attributes['lostemSub'] : '';
		$lostpasMsg = (!empty($attributes['lostpasMsg'])) ? $attributes['lostpasMsg'] : '';
		$lostpasspage = (!empty($attributes['lostpasspage'])) ? $attributes['lostpasspage'] : '[]';
		$lostLink = (!empty($attributes['lostLink'])) ? $attributes['lostLink'] : '';
		

		$lostpasshtmlTxt = $msgHtml = $cusData = [];
		$msgHtml['lostlodTxt'] = '<span class="loading-spinner-reg"><i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i></span>'.wp_kses_post($lostlodTxt).'';
		$msgHtml['losteroTxt'] = '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>'.wp_kses_post($losteroTxt).'';
		$msgHtml['lostuTxt'] = '<span class="loading-spinner-reg"><i class="fa-regular fa-circle-check" aria-hidden="true"></i></i></span>'.wp_kses_post($lostuTxt).'';
		
		$lostpasshtmlTxt['msgHtml'] = $msgHtml;

		if($cutemail == 'yes'){
			$cusData['customEmail'] = [
				'ctmEmail' => $cutemail,
				'loSubject' => $lostemSub,
				'loMessage' => $lostpasMsg,
			];
		}
			

		if( $lostLink == 'custom' && !empty($lostpasspage['value'])){	
			$resetUrl = get_permalink($lostpasspage['value']);
			$forgotUrl = get_the_permalink();
		}else{
			$resetUrl = get_the_permalink();
			$forgotUrl = get_the_permalink();
		}

		if(!empty($losrecaptch) && $losrecaptch == 'yes'){
			$lostpasshtmlTxt['blockId'] = 'tpgb-lostpass-recaptch';
			$lostpasshtmlTxt['recaptchEn'] = 'yes';
			$lostpasshtmlTxt['recaptchaKey'] = $reCaptch_key;
			$lostpasshtmlTxt['recaptchaPos'] = 'inline';
			$recript = '<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=tpgb_onLoadReCaptcha"></script>';
		}

		$cusData['linkOpt'] = $lostLink;
		$cusData['reset_url'] =$resetUrl;
		$cusData['forgot_url'] =$forgotUrl;
		$cusData['noncesecure'] = wp_create_nonce( 'tpgb_reset_action' );
		$lostpasshtmlTxt['cumData'] = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( htmlspecialchars(json_encode($cusData), ENT_QUOTES, 'UTF-8') , 'ey');

		$lostpasshtmlTxt = htmlspecialchars(json_encode($lostpasshtmlTxt), ENT_QUOTES, 'UTF-8');
		$dataAttr .= 'data-lostPass=\'' .$lostpasshtmlTxt. '\' ';

	}	

    $output .= '<div class="tpgb-login-register tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).'"  '.$dataAttr.'  >';
		$output .= '<div class="tpgb-login-wrap">';
			if( is_user_logged_in()){
				$curtUser = wp_get_current_user();
				$output .= tpgb_get_acc_login($attributes,$curtUser);
			}else{
				if($formLayout == 'standard-form'){
					$output .= tpgb_getAllForm($formType , $loField , $regisField , $loginTitle , $regisTitle , $blockTemp , $honeypot ,$block_id , $lopassLabel , $loplaceho , 'forget-password' , $loheading , $lobtntxt , $lobtnIcon , $lorehide , $stpassreq , $backArrow , $losrecaptch , $reCaptch_key,$lorecaposition , $tab1btnTxt , $tab2btnTxt , $dactiveTab );
					if($formType == 'forgot_password' && (empty($_GET['action'])) || (!empty($_GET['action']) && $_GET['action'] !='tpgbreset') ){
						$output .= tpgb_getforgetform($lopassLabel , $loplaceho , 'forget-password' , $loheading , $lobtntxt , '','','' , $backArrow,$block_id , $losrecaptch , $lobtnIcon );
					}
				}else if( $formType != 'forgot_password' && ($btnType == 'button-hover' || $btnType == 'button-click') ){
					if($lorehide == 'no' || (!isset($_GET['action']) && empty($_GET['action']) ) ){
						$output .= '<div class="tpgb-formbtn-hover">';
							$output .= '<a class="tpgb-show-button '.($btnType == 'button-hover' ? ' tpgb-form-hover' : ($btnType == 'button-click' ? ' tpgb-form-click' : '' ) ).'" aria-label="'.esc_attr($btntxt).'" >';
								$output .= '<span class="tpgb-button-wrap">';
									$output .= '<i aria-hidden="true" class=" tpgb-hcp-icon '.esc_attr($btnIcon).'"></i>';
									$output .= '<span class="tpgb-button-text"> '.esc_html($btntxt).' </span>';
								$output .= '</span>';
							$output .= "</a>";

							if($btnType == 'button-hover' || $btnType == 'button-click'){
								$output .= '<div class="tpgb-buform-layput">';
									$output .= '<div class="tpgb-form-wrap tpgb-cnt-'.esc_attr($cntAlign).'">';
										$output .= tpgb_getAllForm($formType , $loField , $regisField , $loginTitle , $regisTitle , $blockTemp , $honeypot , $block_id , $lopassLabel , $loplaceho , 'forget-password' , $loheading , $lobtntxt , $lobtnIcon , $lorehide  , $stpassreq , $backArrow , $losrecaptch , $reCaptch_key,$lorecaposition , $tab1btnTxt , $tab2btnTxt , $dactiveTab );
									$output .= "</div>";
								$output .= "</div>";
							}
						$output .= "</div>";
					}else{
						$output .= tpgb_getlostpass_form($stpassreq,$losrecaptch,$block_id,$reCaptch_key,$lorecaposition);
					}
				}else{
					$output .= tpgb_getforgetform($lopassLabel , $loplaceho , 'forget-password' , $loheading , $lobtntxt , '','','' , $backArrow,$block_id , $losrecaptch , '');
				}
			}
		$output .= "</div>";
    $output .= "</div>";

	$output .= $recript;

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
	}

/**
 * Render for the server-side
 */
function tpgb_tp_login() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'formType' => [
			'type' => 'string',
			'default' => 'login',
		],
		'formLayout' => [
			'type' => 'string',
			'default' => 'standard-form',
		],
		'btnType' => [
			'type' => 'string',
			'default' => 'button-click',
		],
		'lostLink' => [
			'type' => 'string',
			'default' => 'default',
		],
		'formAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register{ text-align: {{formAlign}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap{ text-align: {{formAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'loformAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'forgot_password']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-lostpass-form{ text-align: {{loformAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'tab1btnTxt' => [
			'type' => 'string',
			'default' => 'Login',
		],
		'tab2btnTxt' => [
			'type' => 'string',
			'default' => 'Register',
		],
		'dactiveTab' => [
			'type' => 'string',
			'default' => 'login',
		],
		'btnAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'flex-start', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [ (object) ['key' => 'formLayout', 'relation' => '==', 'value' => 'button'] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap{ justify-content : {{btnAlign}}; }',
				],
			],
			'scopy' => true,
		], 
		'cntAlign' => [
			'type' => 'string',
			'default' => 'left',
			'scopy' => true,
		],
		'loField' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'loginfName' => [
						'type' => 'string',
						'default' => 'username',
					],
					'FieldLabel' => [
						'type' => 'string',
						'default' => '',
					],
					'placeholder' => [
						'type' => 'string',
						'default' => '',
					],
					'content' => [
						'type' => 'string',
						'default' => 'Enter your Text',
					],
					'showInline' => [
						'type' => 'boolean',
						'default' => false,
					],
					'regisTxt' => [
						'type' => 'string',
						'default' => 'Sign Up',
					],
					'loBeTxt' => [
						'type' => 'string',
						'default' => '',
					],
					'fieldWidth' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => '%',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-login-form {{TP_REPEAT_ID}}.tpgb-field-group { width: {{fieldWidth}};display: inline-flex;flex-direction: column; }',
							],
						],
						'scopy' => true,
					],
					'socialAlign' => [
						'type' => 'object',
						'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-field-group .tpgn-socialbtn-wrap{justify-content : {{socialAlign}}; }',
							],
						],
						'scopy' => true,
					],
					'facebooklog' => [
						'type' => 'boolean',
						'default' => false,
					],
					'faceAppid' => [
						'type' => 'string',
						'default' => '',
					],
					'faceSecid' => [
						'type' => 'string',
						'default' => '',
					],
					'solayout' => [
						'type' => 'string',
						'default' => 'solayout-1',
					],
					'googloTitle' => [
						'type' => 'boolean',
						'default' => false,
					],
					'googlePic' => [
						'type' => 'boolean',
						'default' => false,
					],
					'gbtnType' => [
						'type' => 'string',
						'default' => 'standard',
					],
					'googlThm' => [
						'type' => 'string',
						'default' => 'outline',
					],
					'gostandshape' => [
						'type' => 'string',
						'default' => 'rectangular',
					],
					'gobtnTxt' => [
						'type' => 'string',
						'default' => 'signin_with',
					],
					'gobctmTxt' => [
						'type' => 'string',
						'default' => '',
					],
					'gobtnSize' => [
						'type' => 'string',
						'default' => 'large',
					],
					'gobctWidth' => [
						'type' => 'number',
						'default' => '',
					],
					'goioshape' => [
						'type' => 'string',
						'default' => 'square',
					],
					'goioSize' => [
						'type' => 'string',
						'default' => 'large',
					],
					'regisLink' => [
						'type' => 'string',
						'default' => 'default',
					],
					'regisUrl' => [
						'type' => 'object',
						'default' => [ 
							'url' => '',
							'target' => '',
						],
					],
					'magiclabel' => [
						'type' => 'string',
						'default' => 'Email',
					],
					'magicplace' => [
						'type' => 'string',
						'default' => 'Enter Mail',
					],
					'mabtnText' => [
						'type' => 'string',
						'default' => 'Send Magic Link',
					],
					'mabmailSub' => [
						'type' => 'string',
						'default' => 'Login Magic Link for [tpgb_sitename]',
					],
					'magicMsg' => [
						'type' => 'string',
						'default' => ' Hello [tpgb_username] <br/> Here`s your one-click login link you requested for your [tpgb_sitename] account <br/> <a style="color : #2271b1; font-size: 15px; line-height: 25px;" href="[tpgb_link]" target="_blank" rel="noopener noreferrer" > Login Now </a> <br/> By clicking on the above button you will access your account from [tpgb_email] <br/><br/> If you haven`t requested for the link, no further action is required, the link will automatically expire in 24 hours.',
					],
					'magicrelink' => [
						'type' => 'string',
						'default' => '[]',
					],
					'loginIcon' => [
						'type' => 'string',
						'default' => '',
					],
					'LfobtniPosi' => [
						'type' => 'string',
						'default' => 'before',
					],
				],
			],
			'default' => [ 
				[ '_key' => '0' , 'loginfName' => 'username', 'FieldLabel' => 'Username' , 'placeholder' => 'Enter Username' , 'fieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'LfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' ],
				[ '_key' => '1' , 'loginfName' => 'password', 'FieldLabel' => 'Password' , 'placeholder' => 'Enter Password' , 'fieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'LfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' ],
				[ '_key' => '2' , 'loginfName' => 'remember-me', 'content' => 'Remember Me' ,'fieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'LfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' ],
				[ '_key' => '3' , 'loginfName' => 'lost-password', 'content' => 'Lost Password' , 'LfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' ],
				[ '_key' => '4' , 'loginfName' => 'login-button', 'content' => 'Log In' , 'fieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'LfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' ]
			],
		],
		'loglodTxt' => [
			'type' => 'string',
			'default' => 'Please Wait...',
		],
		'losuTxt' => [
			'type' => 'string',
			'default' => 'Login successful.',
		],
		'loemvalText' => [
			'type' => 'string',
			'default' => 'Ops! Wrong username or password!',
		],
		'loeroTxt' => [
			'type' => 'string',
			'default' => 'Something went wrong. Please try again.'
		],
		'rediaflogin' => [
			'type' => 'array',
			'default' => [],
		],
		'redirctUrl' => [
			'type'=> 'object',
			'default'=> [
				'url' => '',
				'target' => '',
				'nofollow' => ''
			],
		],
		'regisField' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'regisfName' => [
						'type' => 'string',
						'default' => 'first-name',
					],
					'regfieldLabel' => [
						'type' => 'string',
						'default' => '',
					],
					'replaceholder' => [
						'type' => 'string',
						'default' => '',
					],
					'passToggle' => [
						'type' => 'boolean',
						'default' => false,
					],
					'showIcon' => [
						'type'=> 'string',
						'default'=> 'fas fa-eye',					
					],
					'hideIcon' => [
						'type'=> 'string',
						'default'=> 'fas fa-eye-slash',
					],
					'strongPass' =>  [
						'type' => 'boolean',
						'default' => false,
					],
					'spitopoff' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-form-controls .tpgb-password-show{ top : {{spitopoff}} }',
							],
						],
					],
					'spileftoff' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-form-controls .tpgb-password-show{ right : {{spileftoff}} }',
							],
						],
					],
					'passHint' => [
						'type' => 'boolean',
						'default' => false,
					],
					'passVisi' => [
						'type'=> 'string',
						'default'=> '',
					],
					'passHintlay' => [
						'type'=> 'string',
						'default'=> '',
					],
					'passMeter' => [
						'type' => 'boolean',
						'default' => false,
					],
					'rebtnTxt' => [
						'type'=> 'string',
						'default'=> 'Register',
					],
					'rebtnIcon' => [
						'type'=> 'string',
						'default'=> '',
					],
					'terms' => [
						'type'=> 'string',
						'default'=> '',
					],
					'loginTxt' => [
						'type'=> 'string',
						'default'=> 'Login',
					],
					'loginLink' => [
						'type'=> 'string',
						'default'=> 'default',
					],
					'relogUrl' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
					],
					'beforeTxt' => [
						'type'=> 'string',
						'default'=> 'Already have an account?',
					],
					'passPattern' => [
						'type'=> 'string',
						'default'=> 'pattern-1',
					],
					'refieldWidth' => [
						'type' => 'object',
						'default' => [ 
							'md' => '',
							"unit" => '%',
						],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} .tpgb-register-form {{TP_REPEAT_ID}}.tpgb-field-group { width: {{refieldWidth}};display: inline-flex;flex-direction: column; }',
							],
						],
						'scopy' => true,
					],
					'clickIcon' => [
						'type'=> 'string',
						'default'=> 'fas fa-info-circle',
					],
					'errorMsg' => [
						'type'=> 'string',
						'default'=> '',
					],
					'apiKey' => [
						'type'=> 'string',
						'default'=> '',
					],
					'listId' => [
						'type'=> 'string',
						'default'=> '',
					],
					'facebooklog' => [
						'type' => 'boolean',
						'default' => false,
					],
					'faceAppid' => [
						'type' => 'string',
						'default' => '',
					],
					'faceSecid' => [
						'type' => 'string',
						'default' => '',
					],
					'socialAlign' => [
						'type' => 'object',
						'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
						'style' => [
							(object) [
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}.tpgb-field-group .tpgn-socialbtn-wrap{justify-content : {{socialAlign}}; }',
							],
						],
						'scopy' => true,
					],
					'solayout' => [
						'type' => 'string',
						'default' => 'solayout-1',
					],
					'googlThm' => [
						'type' => 'string',
						'default' => 'light',
					],
					'googloTitle' => [
						'type' => 'boolean',
						'default' => false,
					],
					'googlePic' => [
						'type' => 'boolean',
						'default' => false,
					],
					'gbtnType' => [
						'type' => 'string',
						'default' => 'standard',
					],
					'googlThm' => [
						'type' => 'string',
						'default' => 'outline',
					],
					'gostandshape' => [
						'type' => 'string',
						'default' => 'rectangular',
					],
					'gobtnTxt' => [
						'type' => 'string',
						'default' => 'signin_with',
					],
					'gobctmTxt' => [
						'type' => 'string',
						'default' => '',
					],
					'gobtnSize' => [
						'type' => 'string',
						'default' => 'large',
					],
					'gobctWidth' => [
						'type' => 'number',
						'default' => '',
					],
					'goioshape' => [
						'type' => 'string',
						'default' => 'square',
					],
					'goioSize' => [
						'type' => 'string',
						'default' => 'large',
					],
					'acfKey' => [
						'type' => 'string',
						'default' => '',
					],
					'recaposition' => [
						'type' => 'string',
						'default' => 'inline',
					],
					'RfobtniPosi' => [
						'type' => 'string',
						'default' => 'before',
					],
					'acfField' => [
						'type' => 'string',
						'default' => 'text',
					],
				],
			],
			'default' => [ 
				[ '_key' => '0' , 'regisfName' => 'first-name', 'regfieldLabel' => 'First Name' , 'replaceholder' => 'Enter First Name' , 'refieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'errorMsg' => 'Invalid First Name Value.' , 'showIcon' => 'fas fa-eye' , 'hideIcon' => 'fas fa-eye-slash' , 'RfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' , 'acfField' => 'text'],
				[ '_key' => '1' , 'regisfName' => 'last-name', 'regfieldLabel' => 'Last Name' , 'replaceholder' => 'Enter Last Name' , 'refieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'errorMsg' => 'Invalid Last Name Value.' , 'showIcon' => 'fas fa-eye' , 'hideIcon' => 'fas fa-eye-slash' , 'RfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large', 'acfField' => 'text' ],
				[ '_key' => '2' , 'regisfName' => 'email', 'regfieldLabel' => 'Email' , 'replaceholder' => 'Enter Email' , 'refieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'errorMsg' => 'Invalid Email Address.' , 'showIcon' => 'fas fa-eye' , 'hideIcon' => 'fas fa-eye-slash' , 'RfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' , 'acfField' => 'text' ],
				[ '_key' => '3' , 'regisfName' => 'register-button', 'rebtnTxt' => 'Register' , 'refieldWidth' => ["unit" => '%' , 'md' => ''] , 'googlThm' => 'outline' , 'googloTitle' => false , 'googlePic' => false , 'showIcon' => 'fas fa-eye' , 'hideIcon' => 'fas fa-eye-slash' , 'RfobtniPosi' => 'before' , 'gbtnType' => 'standard' , 'gostandshape' => 'rectangular' , 'gobtnTxt' => 'signin_with' , 'gobctmTxt' => '' , 'gobtnSize' => 'large' , 'gobctWidth' => '' , 'goioshape' => 'square' , 'goioSize' => 'large' , 'acfField' => 'text' ],
				
			],
		],
		'honeypot' => [
			'type' => 'boolean',
			'default' => false,
		],
		'recaptcha' => [
			'type' => 'boolean',
			'default' => false,
		],
		'reAction' => [
			'type' => 'array',
			'default' => [],
		],
		'relodTxt' => [
			'type' => 'string',
			'default' => 'Please wait...',
		],
		'resuTxt' => [
			'type' => 'string',
			'default' => 'Registration Successful.',
		],
		'regdirectUrl' => [
			'type' => 'object',
			'default' => [ 
				'url' => '',
				'target' => '',
			],
		],
		'regemailSub' => [
			'type' => 'string',
			'default' => 'Account Successfully Registered at [tpgb_sitename]',
		],
		'regemailMsg' => [
			'type' => 'string',
			'default' => 'Dear [tpgb_firstname], Welcome! </br> Thank you for registering at [tpgb_sitename], your account have been created. </br></br> Please find your login details below. </br> Username: [tpgb_username] </br> Password: [tpgb_password] </br></br> Thanks, </br> [tpgb_sitename]',
		],
		'reemvalText' => [
			'type' => 'string',
			'default' => 'An account already exists with this email address.',
		],
		'reeroTxt' => [
			'type' => 'string',
			'default' => 'Something went wrong. Please try again.',
		],
		'lopassLabel' => [
			'type' => 'string',
			'default' => 'Username/Email',
		],
		'loplaceho' => [
			'type' => 'string',
			'default' => 'Username/Email',
		],
		'lobtntxt' => [
			'type' => 'string',
			'default' => 'Email Reset Link',
		],
		'lobtnIcon' => [
			'type' => 'string',
			'default' => '',
		],
		'loheading' => [
			'type' => 'string',
			'default' => 'Lost your password?',
		],
		'btnIcon' => [
			'type' => 'string',
			'default' => 'fas fa-user-circle',
		],
		'btntxt' => [
			'type' => 'string',
			'default' => 'Login/Signup',
		],
		'stpassreq' => [
			'type' => 'boolean',
			'default' => false,
		],
		'lorehide' => [
			'type' => 'boolean',
			'default' => false,
		],
		'lostWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-field-group { width: {{lostWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'loginTitle' => [
			'type' => 'string',
			'default' => 'Sign In',
		],
		'regisTitle' => [
			'type' => 'string',
			'default' => 'Create an Account',
		],
		'blockTemp' => [
			'type' => 'string',
			'default' => '',
		],
		'backendVisi' => [
			'type' => 'boolean',
			'default' => false,
		],
		'cutemail' => [
			'type' => 'boolean',
			'default' => false,
		],
		'lostlodTxt' => [
			'type' => 'string',
			'default' => 'Please wait...',
		],
		'lostuTxt' => [
			'type' => 'string',
			'default' => 'Mail sent. Please check your mailbox.',
		],
		'losteroTxt' => [
			'type' => 'string',
			'default' => 'Something went wrong. Please try again.',
		],
		'lostemSub' => [
			'type' => 'string',
			'default' => 'Reset your [tpgb_sitename] Password',
		],
		'lostpasMsg' => [
			'type' => 'string',
			'default' => 'Hello [tpgb_username] </br> We have received a request to reset your password for [tpgb_sitename]. </br> To set your new password please <a style="color : #2271b1; font-size: 15px; line-height: 25px;" href="[tpgb_link]" target="_blank" rel="noopener noreferrer" > click here </a>  </br></br> If you didnt ask for reset, you can safely ignore this email, the link will automatically expire after 24 hours. </br></br> Thanks,</br>[tpgb_sitename] ',
		],
		'lostpasspage' => [
			'type' => 'object',
        	'default' => [],
		],

		// My Account Menu
		'accoutMenu' => [
			'type' => 'boolean',
			'default' => true,
		],
		'showInback' => [
			'type' => 'boolean',
			'default' => false,
		],
		'userProf' => [
			'type' => 'boolean',
			'default' => true,
		],
		'meuserName' => [
			'type' => 'boolean',
			'default' => true,
		],
		'pronamePatt' => [
			'type' => 'string',
			'default' => 'none',
		],
		'meeditPro' => [
			'type' => 'boolean',
			'default' => true,
		],
		'editprotxt' => [
			'type' => 'string',
			'default' => 'Edit Profile',
		],
		'editproIcon' => [
			'type' => 'string',
			'default' => '',
		],
		'logoutBtn' => [
			'type' => 'boolean',
			'default' => true,
		],
		'lotouttxt' => [
			'type' => 'string',
			'default' => 'Logout',
		],
		'lotoutIcon' => [
			'type' => 'string',
			'default' => '',
		],
		'extraMenu' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'extmTitle' => [
						'type' => 'string',
						'default' => 'Menu',
					],
					'extIcon' => [
						'type' => 'string',
						'default' => '',
					],
					'extmLink' => [
						'type'=> 'object',
						'default'=> [
							'url' => '#',
							'target' => '',
							'nofollow' => ''
						],
					],
				],
			],
			'default' => [ 
				[ 
					'extmTitle' => 'Menu 1', 
					'extmLink' => [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
			],
		],

		'losrecaptch' => [
			'type' => 'boolean',
			'default' => false,
		],
		'lorecaposition' => [
			'type' => 'string',
			'default' => 'inline',
		],
		'formlaTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-label',
				],
			],
			'scopy' => true,
		],
		'formlaColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-label,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-label{color:{{formlaColor}};}',
				],
			],
			'scopy' => true,
		],
		'foinFieldtypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input',
				],
			],
			'scopy' => true,
		],
		'formplaColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input::placeholder{ color : {{formplaColor}}; }',
				],
			],
			'scopy' => true,
		],
		'foinAlignment' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input::placeholder,{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input{ text-align : {{foinAlignment}}; }',
				],
			],
			'scopy' => true,
		],
		'foinPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input{ padding : {{foinPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'foinMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input{ margin : {{foinMargin}}; }',
				],
			],
			'scopy' => true,
		],
		'fotxtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input{ color : {{fotxtColor}}; }',
				],
			],
			'scopy' => true,
		],
		'formfiBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input',
				],
			],
			'scopy' => true,
		],
		'formfiBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input',
				],
			],
			'scopy' => true,
		],
		'foinbRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input{ border-radius  : {{foinbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'formfiSh' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input',
				],
			],
			'scopy' => true,
		],
		'focutxtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input:focus{ color : {{focutxtColor}}; }',
				],
			],
			'scopy' => true,
		],
		'formfofiBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input:focus',
				],
			],
			'scopy' => true,
		],
		'formfoBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input:focus',
				],
			],
			'scopy' => true,
		],
		'foinfobRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input:focus{ border-radius  : {{foinfobRad}}; }',
				],
			],
			'scopy' => true,
		],
		'formfoSh' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-login-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-form-controls input:focus,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-form-controls input:focus',
				],
			],
			'scopy' => true,
		],
		'formbtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button',
				],
			],
			'scopy' => true,
		],
		'formbtnWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button{ width:100%; max-width : {{formbtnWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-submit-wrap,{{PLUS_WRAP}} .tpgb-login-form .tpgb-submit-wrap,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-submit-wrap ,{{PLUS_WRAP}} .tpgb-rp-form  .tpgb-submit-wrap,{{PLUS_WRAP}} .tpgb-magic-form .tpgb-submit-wrap,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-submit-wrap { text-align : {{fobtnAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtntxAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button .tpgb-lrbtn-icon,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button .tpgb-lrbtn-icon,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button .tpgb-lrbtn-icon ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button .tpgb-lrbtn-icon,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button .tpgb-lrbtn-icon,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button .tpgb-lrbtn-icon{ justify-content : {{fobtntxAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtniPosi' => [
			'type' => 'string',
			'default' => 'after',	
		],
		'fobtntMargin' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button{ margin : {{fobtntMargin}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnPadding' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					"unit" => "px",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button{ padding : {{fobtnPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'formbtnColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button{ color : {{formbtnColor}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 1,
				'bgType' => 'color',
				'bgDefaultColor' => '#5048e5',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button',
				],
			],
			'scopy' => true,
		],
		'fobtnBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button',
				],
			],
			'scopy' => true,
		],
		'fobtnbRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button{ border-radius  : {{fobtnbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button',
				],
			],
			'scopy' => true,
		],
		'fobHvrcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button:hover,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button:hover ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button:hover,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button:hover{ color : {{fobHvrcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnhvrBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button:hover,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button:hover ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button:hover,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button:hover'
				],
			],
			'scopy' => true,
		],
		'fobtnhvrBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
					'sm' => (object)[ ],
					'xs' => (object)[ ],
					"unit" => "",
				],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button:hover,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button:hover ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button:hover,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button:hover'
				],
			],
			'scopy' => true,
		],
		'fobtnhvrbRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button:hover,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button:hover ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button:hover,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button:hover{ border-radius  : {{fobtnhvrbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'fobtnhvrBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form button.tpgb-register-button:hover,{{PLUS_WRAP}} .tpgb-login-form button.tpgb-login-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-lost-pass-button:hover ,{{PLUS_WRAP}} .tpgb-rp-form  button.tpgb-resetpassword-button:hover,{{PLUS_WRAP}} .tpgb-magic-form button.tpgb-magic-link-button:hover,{{PLUS_WRAP}} .tpgb-lostpass-form button.tpgb-forget-password-button:hover',
				],
			],
			'scopy' => true,
		],
		'foheadTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-forgot-password-label',
				],
			],
			'scopy' => true,
		],
		'foheadColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-forgot-password-label{ color : {{foheadColor}}; }',
				],
			],
			'scopy' => true,
		],
		'foheadMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title,{{PLUS_WRAP}} .tpgb-lostpass-form .tpgb-forgot-password-label{ margin : {{foheadMargin}}; }',
				],
			],
			'scopy' => true,
		],
		'lostpastxtTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group a.tpgb-re-login',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link,{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group a.tpgb-re-login',
				],
			],
			'scopy' => true,
		],
		'lostpastxtAlign' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group.tpgb-login-regtxt{ text-align : {{lostpastxtAlign}} }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group.tpgb-login-regtxt{ text-align : {{lostpastxtAlign}} }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group.tpgb-login-regtxt,{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group.tpgb-login-regtxt{ text-align : {{lostpastxtAlign}}; display : block }',
				],
			],
			'scopy' => true,

		],
		'lostpasColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link{ color : {{lostpasColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group a.tpgb-re-login{ color : {{lostpasColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link,{{PLUS_WRAP}} .tpgb-register-form .tpgb-field-group a.tpgb-re-login{ color : {{lostpasColor}}; }',
				],
			],
			'scopy' => true,
		],
		'txtrightsSpc' => [
			'type' => 'object',
			'default' => [
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link{ margin-left : {{txtrightsSpc}}px; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-re-login{ margin-left : {{txtrightsSpc}}px; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link,{{PLUS_WRAP}} .tpgb-register-form .tpgb-re-login{ margin-left : {{txtrightsSpc}}px; }',
				],
			],
			'scopy' => true,
		],
		'lostpaMargin' => [
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
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link{ margin : {{lostpaMargin}}; } {{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link{ display : block }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link,{{PLUS_WRAP}} .tpgb-register-form .tpgb-re-login{ margin : {{lostpaMargin}}; } {{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-register-link{ display : block }',
				],
			],
			'scopy' => true,
		],
		'fobeforeTxt' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-loginbefore-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-loginbefore-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-loginbefore-text,{{PLUS_WRAP}} .tpgb-register-form .tpgb-loginbefore-text',
				],
			],
			'scopy' => true,
		],
		'beforeColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-loginbefore-text{ color : {{beforeColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-loginbefore-text{ color : {{beforeColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-loginbefore-text,{{PLUS_WRAP}} .tpgb-register-form .tpgb-loginbefore-text{ color : {{beforeColor}}; }',
				],
			],
			'scopy' => true,
		],
		'remebTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-choice-label',
				],
			],
			'scopy' => true,
		],
		'remebColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-choice-label{ color : {{remebColor}}; }',
				],
			],
			'scopy' => true,
		],
		'remeAlignment' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-recheck-wrap , {{PLUS_WRAP}} .tpgb-remember-me { justify-content : {{remeAlignment}}; }',
				],
			],
			'scopy' => true,
		],
		'remebMargin' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-remember-me,{{PLUS_WRAP}}.tpgb-login-register .tpgb-recheck-wrap{ margin : {{remebMargin}}; }',
				],
			],
			'scopy' => true,
		],
		'remebPadding' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-choice-label{ padding : {{remebPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'remebunchColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=checkbox]{ background : {{remebunchColor}}; }',
				],
			],
			'scopy' => true,
		],
		'raduncheBor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=checkbox]{ border-color : {{raduncheBor}}; }',
				],
			],
			'scopy' => true,
		],
		'remebchColor' => [
			'type' => 'string',
			'default' => '#5048e5',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register', 'register']  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=checkbox]:checked, {{PLUS_WRAP}} .tpgb-login-register input[type="checkbox"]:hover:checked,{{PLUS_WRAP}} .tpgb-login-register input[type="checkbox"]:focus:checked{ background : {{remebchColor}}; }',
				],
			],
			'scopy' => true,
		],
		'remebchkColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login','login-register', 'register' ]  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type="checkbox"]:checked::before{ border-color : {{remebchkColor}}; }',
				],
			],
			'scopy' => true,
		],
		'radcheBor' => [
			'type' => 'string',
			'default' => '#5048e5',
			'style' => [
				(object) [
					'condition' => [ (object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login','login-register', 'register' ]  ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=checkbox]:checked, {{PLUS_WRAP}} .tpgb-login-register input[type="checkbox"]:hover:checked,{{PLUS_WRAP}} .tpgb-login-register input[type="checkbox"]:focus:checked{ border-color : {{radcheBor}}; }',
				],
			],
			'scopy' => true,
		],
		'backArrow' => [
			'type' => 'string',
			'default' => 'fas fa-arrow-left',
		],
		'backarrSize' => [
			'type' => 'object',
			'default' => [
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register']  ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-lostpass-form.tpgb-login-lost .tpgb-lpu-back{ font-size : {{backarrSize}}px; }',
				],
			],
			'scopy' => true,
		],
		'backarrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => ['login','login-register']  ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-lostpass-form.tpgb-login-lost .tpgb-lpu-back{ color : {{backarrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'noticmsgTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-regis-noti.active .tpgb-re-response',
				],
			],
			'scopy' => true,
		],
		'notimsgColor' => [
			'type'=> 'string',
			'default'=> '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-regis-noti.active .tpgb-re-response{ color : {{notimsgColor}}; }',
				],
			],
			'scopy' => true,
		],
		'notimsgBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-regis-noti.active',
				],
			],
			'scopy' => true,
		],
		'noticonSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-regis-noti .tpgb-re-response svg{ width : {{noticonSize}} } {{PLUS_WRAP}} .tpgb-regis-noti .tpgb-re-response span.loading-spinner-reg{ font-size : {{noticonSize}} }',
				],
			],
			'scopy' => true,
		],
		'noticonColor' => [
			'type'=> 'string',
			'default'=> '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-regis-noti .tpgb-re-response svg{ fill : {{noticonColor}}; } {{PLUS_WRAP}} .tpgb-regis-noti .tpgb-re-response span.loading-spinner-reg{ color : {{noticonColor}};  }',
				],
			],
			'scopy' => true,
		],
		// user Name
		'menuliTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-log-menu .tpgb-acct-list a.tpgb-acc-link',
				],
			],
			'scopy' => true,
		],
		'menuAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acct-list .tpgb-acc-item{ text-align : {{menuAlign}} }',
				],
			],
			'scopy' => true,
		],
		'menulipad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu{ padding : {{menulipad}}; }',
				],
			],
			'scopy' => true,
		],
		'menulisColor' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acct-list a.tpgb-acc-link{ color : {{menulisColor}} }',
				],
			],
			'scopy' => true,
		],
		'menuhvrlisColor' => [
			'type' => 'object',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acct-list a.tpgb-acc-link:hover{ color : {{menuhvrlisColor}} }',
				],
			],
			'scopy' => true,
		],
		'menuliBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu',
				],
			],
			'scopy' => true,
		],
		'menuhvrliBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu:hover',
				],
			],
			'scopy' => true,
		],
		'menuBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu',
				],
			],
			'scopy' => true,
		],
		'menuhvrBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu:hover',
				],
			],
			'scopy' => true,
		],
		'menulibRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu{ border-radius : {{menulibRad}}; } ',
				],
			],
			'scopy' => true,
		],
		'menuhvrlibRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu:hover{ border-radius : {{menuhvrlibRad}}; } ',
				],
			],
			'scopy' => true,
		],
		'menuliBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu',
				],
			],
			'scopy' => true,
		],
		'menuHvrliBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-log-menu:hover',
				],
			],
			'scopy' => true,
		],
		'uimgright' => [
			'type' => 'object',
			'default' => [
				'md' => '',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-acc-text img.avatar{ margin-right : {{uimgright}}px; } ',
				],
			],
			'scopy' => true,
		],
		'uimgSize' => [
			'type' => 'object',
			'default' => [
				'md' => '',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-acc-text img.avatar{ width : {{uimgSize}}px; height : {{uimgSize}}px; line-height : {{uimgSize}}px; } ',
				],
			],
			'scopy' => true,
		],
		'uimgBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-acc-text img.avatar ',
				],
			],
			'scopy' => true,
		],
		'uimgbRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-acc-text img.avatar{ border-radius : {{uimgbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'uimgBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-acc-text img.avatar ',
				],
			],
			'scopy' => true,
		],

		'userTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-btn .tpgb-acc-text',
				],
			],
			'scopy' => true,
		],
		'menupadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn{ padding : {{menupadding}}; }',
				],
			],
			'scopy' => true,
		],
		'usernColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn{ color : {{usernColor}}; }',
				],
			],
			'scopy' => true,
		],
		'userBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn',
				],
			],
			'scopy' => true,
		],
		'userBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn',
				],
			],
			'scopy' => true,
		],
		'userbRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn{ border-radius : {{userbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'userBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn',
				],
			],
			'scopy' => true,
		],
		'usernHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn:hover{ color : {{usernHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'userHvrBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn:hover',
				],
			],
			'scopy' => true,
		],
		'userhvrBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn:hover',
				],
			],
			'scopy' => true,
		],
		'userbhvrRad' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn:hover{ border-radius : {{userbhvrRad}}; }',
				],
			],
			'scopy' => true,
		],
		'userhvrBsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-account-menu .tpgb-account-btn:hover',
				],
			],
			'scopy' => true,
		],
		'miconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acc-item .tpgb-acc-icon{ font-size : {{miconSize}}; } ',
				],
			],
			'scopy' => true,
		],

		'miconSpacing' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acc-item .tpgb-acc-icon{ margin-right : {{miconSpacing}}; } ',
				],
			],
			'scopy' => true,
		],
		'miconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acc-item .tpgb-acc-icon{ color : {{miconColor}}; } ',
				],
			],
			'scopy' => true,
		],
		'mhvriconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-acc-link:hover .tpgb-acc-icon{ color : {{mhvriconColor}}; } ',
				],
			],
			'scopy' => true,
		],
		// Box Option
		'bmaxWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap{ min-width: {{bmaxWidth}};}',
				],
			],
			'scopy' => true,
		],
		'bmaxHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap{ max-height: {{bmaxHeight}};  min-height: {{bmaxHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'bmargin' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form{ margin : {{bmargin}}; }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover','button-popup'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap .tpgb-lostpass-form{ margin : {{bmargin}}; }',
				],
			],
			'scopy' => true,
		],
		'bpadding' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form{ padding : {{bpadding}};  }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover'] ]],
					'selector' => '{{PLUS_WRAP}}{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap .tpgb-lostpass-form{ padding : {{bpadding}};  }',
				],
			],
			'scopy' => true,
		],
		'bbgType' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap .tpgb-lostpass-form',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form',
				],
			],
			'scopy' => true,
		],
		'boxBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover','button-popup'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ],
						(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'forgot_password' ],
					],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form',
				],
			],
			'scopy' => true,
		],
		'boxborRad' => [
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
					'condition' => [ (object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form{ border-radius : {{boxborRad}}; }',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap .tpgb-lostpass-form{ border-radius : {{boxborRad}}; }',
				],
			],
			'scopy' => true,
		],
		'boxbShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'btnType', 'relation' => '==', 'value' =>  ['button-click','button-hover'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-form-wrap .tpgb-lostpass-form',
				],
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '==', 'value' => 'standard-form' ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-lostpass-form',
				],
			],
			'scopy' => true,
		],

		// Tab Button
		'tabbtnAllign' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-form-tab-wrap .tpgb-form-tabbtn{ text-align: {{tabbtnAllign}}; }',
				],
			],
			'scopy' => true,
		],
		'tabbtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn',
				],
			],
			'scopy' => true,
		],
		'tabbtnPadd' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn{ padding : {{tabbtnPadd}}; }',
				],
			],
			'scopy' => true,
		],
		'tabbtnWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn{ max-width : {{tabbtnWidth}}; min-width : {{tabbtnWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'tabBtcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn{ color : {{tabBtcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'tabtBbg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn',
				],
			],
			'scopy' => true,
		],
		'tabbtBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn',
				],
			],
			'scopy' => true,
		],
		'tabBbRad' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn{ border-radius : {{tabBbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'tabBbsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn',
				],
			],
			'scopy' => true,
		],
		'tabBtActcolor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn.active{ color : {{tabBtActcolor}}; }',
				],
			],
			'scopy' => true,
		],
		'tabtActBbg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn.active',
				],
			],
			'scopy' => true,
		],
		'tabbtActBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn.active',
				],
			],
			'scopy' => true,
		],
		'tabBactbRad' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn.active{ border-radius : {{tabBactbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'tabBactbsha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-tab-wrap .tpgb-ftab-btn.active',
				],
			],
			'scopy' => true,
		],
		'betSpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => 'login-register']],
					'selector' => '{{PLUS_WRAP}} .tpgb-form-tab-wrap .tpgb-form-tabbtn .tpgb-ftab-btn:last-child{ margin-left : {{betSpace}}px; }',
				],
			],
			'scopy' => true,
		],
		'hcpbtntypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button',
				],
			],
			'scopy' => true,
		],
		'hcpbtnmar' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button{ margin : {{hcpbtnmar}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpbtnpadd' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button{ padding : {{hcpbtnpadd}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				'unit' => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-formbtn-hover{ width : {{hcpWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button{ color : {{hcpColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button',
				],
			],
			'scopy' => true,
		],
		'hcpBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button',
				],
			],
			'scopy' => true,
		],
		'hcpBtad' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button{ border-radius : {{hcpBtad}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpbSha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button',
				],
			],
			'scopy' => true,
		],
		'hcpHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover{ color : {{hcpHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpHvrBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover',
				],
			],
			'scopy' => true,
		],
		'hcpHvrBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover',
				],
			],
			'scopy' => true,
		],
		'hcphvrBtad' => [
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
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover{ border-radius : {{hcphvrBtad}}; }',
				],
			],
			'scopy' => true,
		],
		'hcphvrbSha' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover',
				],
			],
			'scopy' => true,
		],
		'hcpbIconsi' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button .tpgb-hcp-icon{ font-size : {{hcpbIconsi}}px; }',
				],
			],
			'scopy' => true,
		],
		'hcprigSpa' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button .tpgb-hcp-icon{  margin-right : {{hcprigSpa}}px; }',
				],
			],
			'scopy' => true,
		],
		'hcpiconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button .tpgb-hcp-icon{ color : {{hcpiconColor}}; }',
				],
			],
			'scopy' => true,
		],
		'hcpihveColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formLayout', 'relation' => '!=', 'value' => 'standard-form']],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-wrap .tpgb-show-button:hover .tpgb-hcp-icon{ color : {{hcpihveColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cutmsgTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field',
				],
			],
			'scopy' => true,
		],
		'cutmsgpadd' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field{ padding : {{cutmsgpadd}}; }',
				],
			],
			'scopy' => true,
		],
		'msgtopOff' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field{ top : {{msgtopOff}}px; }',
				],
			],
			'scopy' => true,
		],
		'cutmsgColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field{ color : {{cutmsgColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cutmsgBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field',
				],
			],
			'scopy' => true,
		],
		'cutmsgBor' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field',
				],
			],
			'scopy' => true,
		],
		'cutmsgBorrad' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-form-controls .tpgb-error-field{ border-radius : {{cutmsgBorrad}}; }',
				],
			],
			'scopy' => true,
		],
		// Terms & Condition

		'termsCondi' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-choice-label',
				],
			],
			'scopy' => true,
		],
		'termcondiTxt' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-choice-label{ color : {{termcondiTxt}}; }',
				],
			],
			'scopy' => true,
		],
		'termsBg' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox { background : {{termsBg}};  } ',
				],
			],
			'scopy' => true,
		],
		'termsBor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox { border-color : {{termsBor}};  } ',
				],
			],
			'scopy' => true,
		],
		'termscheBg' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:checked,{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:hover:checked,{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:focus:checked { background : {{termscheBg}};  } ',
				],
			],
			'scopy' => true,
		],
		'termscheBor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:checked,{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:hover:checked,{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:focus:checked { border-color : : {{termscheBor}};  } ',
				],
			],
			'scopy' => true,
		],
		'termchkColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login-register', 'register' ]  ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-check-terms .tpgb-login-checkbox:checked::before{ border-color : {{termchkColor}}; }',
				],
			]
		],

		// Magic Link
		'magictxtTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-magic-tag',
				],
			],
			'scopy' => true,
		],
		'magictxtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-magic-tag{ color:{{magictxtColor}}; }',
				],
			],
			'scopy' => true,
		],
		'foheadingAlign' => [
			'type' => 'object',
			'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'loginTitle', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title{ text-align: {{foheadingAlign}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'regisTitle', 'relation' => '!=', 'value' => '']],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-wrap .tpgb-form-title{ text-align: {{foheadingAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'extrtxtTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-register-msg,{{PLUS_WRAP}} .tpgb-login-form .tpgb-register-msg',
				],
			],
			'scopy' => true,
		],
		'extrtxtAlign' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-register-msg,{{PLUS_WRAP}} .tpgb-login-form .tpgb-register-msg{ display : block; text-align : {{extrtxtAlign}} }',
				],
			],
			'scopy' => true,
		],
		'extrtMargin' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-lore-exttxt,{{PLUS_WRAP}} .tpgb-login-form .tpgb-lore-exttxt{ margin : {{extrtMargin}} }',
				],
			],
			'scopy' => true,
		],
		'extrtxtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-register-form .tpgb-register-msg,{{PLUS_WRAP}} .tpgb-login-form .tpgb-register-msg{ color : {{extrtxtColor}}; }',
				],
			],
			'scopy' => true,
		],
		'radiounchBg' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=radio]{ background-color : {{radiounchBg}} }',
				],
			],
			'scopy' => true,
		],
		'radiounchBor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=radio]{ border-color : {{radiounchBor}} }',
				],
			],
		],
		'radiocheBg' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=radio]:checked:before{ background-color : {{radiocheBg}} }',
				],
			],
			'scopy' => true,
		],
		'radiocheBor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=radio]:checked{ border-color : {{radiocheBor}} }',
				],
			],
			'scopy' => true,
		],
		'filePadding' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=file]{ padding : {{filePadding}} }',
				],
			],
			'scopy' => true,
		],
		'fileBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,	
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register input[type=file]',
				],
			],
			'scopy' => true,
		],
		'socialBtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook .tpgb-social-logo p ,{{PLUS_WRAP}} .tpgb-btn-google .tpgb-btn-goo span.abcRioButtonContents,{{PLUS_WRAP}} .tpgb-btn-google .tpgb-social-logo.tpgb-goo-dark',
				],
			],
			'scopy' => true,
		],
		'socialBtnpadd' => [
			'type'=> 'object',
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook .tpgb-social-logo , {{PLUS_WRAP}} .tpgb-btn-google .tpgb-btn-goo .tpgb-social-logo{ padding : {{socialBtnpadd}} } ',
				],
			],
			'scopy' => true,
		],
		'socibtnColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook a.tpgb-btn-fb .tpgb-social-logo > p, {{PLUS_WRAP}} .tpgb-btn-google .tpgb-btn-goo .abcRioButtonContents{ color : {{socibtnColor}} !important } ',
				],
			],
			'scopy' => true,
		],
		'socibtnhvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook:hover a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google:hover .tpgb-btn-goo .abcRioButtonContents{ color : {{socibtnhvrColor}} !important } ',
				],
			],
			'scopy' => true,
		],
		'sobtnBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgn-socialbtn-wrap a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google .tpgb-social-logo, {{PLUS_WRAP}} .tpgb-btn-google .abcRioButton',
				],
			],
			'scopy' => true,
		],
		'sohvrbtnBg' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook:hover a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google:hover .tpgb-social-logo , {{PLUS_WRAP}} .tpgb-btn-google:hover .abcRioButton ',
				],
			],
			'scopy' => true,
		],
		'sobtnBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgn-socialbtn-wrap a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google .tpgb-social-logo, {{PLUS_WRAP}} .tpgb-btn-google .abcRioButton',
				],
			],
			'scopy' => true,
		],
		'sobtnhvrBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook:hover a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google:hover .tpgb-social-logo , {{PLUS_WRAP}} .tpgb-btn-google:hover .abcRioButton ',
				],
			],
			'scopy' => true,
		],
		'sobtnbRad' => [
			'type'=> 'object',
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgn-socialbtn-wrap a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google .tpgb-social-logo, {{PLUS_WRAP}} .tpgb-btn-google .abcRioButton{ border-radius: {{sobtnbRad}}; }',
				],
			],
			'scopy' => true,
		],
		'sobtnbhvrRad' => [
			'type'=> 'object',
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook:hover a.tpgb-btn-fb, {{PLUS_WRAP}} .tpgb-btn-google:hover .tpgb-social-logo , {{PLUS_WRAP}} .tpgb-btn-google:hover .abcRioButton{ border-radius: {{sobtnbhvrRad}}; }',
				],
			],
			'scopy' => true,
		],
		'sobtnWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-btn-facebook .tpgb-btn-fb,{{PLUS_WRAP}} .tpgb-btn-facebook, {{PLUS_WRAP}} .tpgb-btn-google .tpgb-social-logo ,.tpgb-btn-google .tpgb-btn-goo .abcRioButtonLightBlue{ width : {{sobtnWidth}} !important; } ',
				],
			],
		],
		'formbtnIcons' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-register-button .tpgb-lrbtn-icon .tpgb-lrbtn,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-button .tpgb-lrbtn-icon .tpgb-lrbtn,{{PLUS_WRAP}}.tpgb-login-register .tpgb-forget-password-button .tpgb-lrbtn-icon .tpgb-lrbtn{ font-size : {{formbtnIcons}} } ',
				],
			],
		],
		'formbtnISpace' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-register-button .tpgb-lrbtn-icon,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-button .tpgb-lrbtn-icon,{{PLUS_WRAP}}.tpgb-login-register .tpgb-forget-password-button .tpgb-lrbtn-icon{ column-gap : {{formbtnISpace}} } ',
				],
			],
		],
		'formIconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-register-button .tpgb-lrbtn-icon .tpgb-lrbtn,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-button .tpgb-lrbtn-icon .tpgb-lrbtn,{{PLUS_WRAP}}.tpgb-login-register .tpgb-forget-password-button .tpgb-lrbtn-icon .tpgb-lrbtn{ color : {{formIconColor}} } ',
				],
			],
		],
		'fbtnHicColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'register' , 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}}.tpgb-login-register .tpgb-register-button:hover .tpgb-lrbtn-icon .tpgb-lrbtn,{{PLUS_WRAP}}.tpgb-login-register .tpgb-login-button:hover .tpgb-lrbtn-icon .tpgb-lrbtn , {{PLUS_WRAP}}.tpgb-login-register .tpgb-forget-password-button:hover .tpgb-lrbtn-icon .tpgb-lrbtn { color : {{fbtnHicColor}} } ',
				],
			],
		],
		'lostxtTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-lost-password',
				],
			],
		],
		'lostxtAlign' => [
			'type' => 'object',
			'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-lost-password{ text-align : {{lostxtAlign}} }',
				],
			],
		],
		'losttxtColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group .tpgb-lost-password{ color : {{losttxtColor}} }',
				],
			],
		],
		'losttxtMar' => [
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
					'condition' => [(object) [ 'key' => 'formType', 'relation' => '==', 'value' => [ 'login-register' , 'login' ]]],
					'selector' => '{{PLUS_WRAP}} .tpgb-login-form .tpgb-field-group.tpgb-lostpass-relink{ margin : {{losttxtMar}} }',
				],
			],
		],
	];

	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-login-register', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_login_render_callback'
    ) );

}
add_action( 'init', 'tpgb_tp_login' );

// Get Text Field
function tpgb_getTextField($label,$placeholder,$type,$error,$in,$honeypot){
	$textField = '';
	if( !empty($label) ){
		$textField .= '<label class="tpgb-form-label" for="'.esc_attr($type).'"> '.esc_html( $label ).' </label>';
	}
	$textField .= '<div class="tpgb-form-controls">' ;
		$textField .= '<input class="tpgb-error-load" type="'.esc_attr($in).'" name="'.esc_attr( $type ).'" id="'.esc_attr( $type ).'" placeholder="'.esc_attr( $placeholder ).'" data-error="'.esc_attr($error).'" required />';
		if(!empty($honeypot)){
			$textField .= '<input type="'.esc_attr($in).'" name="tphoney-'.esc_attr( $type ).'" id="tphoney_'.esc_attr( $type ).'" class="tpgb-honey-input" />';
		}
		
	$textField .= '</div>';

	return $textField;
}


//Get Password Field
function tpgb_getpasswordField($label,$placeholder,$type,$toggle,$sIcon,$hIcon,$strongPass,$passPattern,$passHint,$passVisi,$clickIcon,$passHintlay,$passMeter,$error,$honeypot,$formName){
	$passwordField = '';
	$reg_expre = '';
	
	if(!empty($formName)  && $formName == 'register' && $type == 'password' ){
		$type = 'repassword';
	}

	if(!empty($strongPass) && !empty($passPattern) ){
		if($passPattern == 'pattern-1'){
			$reg_expre='pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"';
		}else if($passPattern == 'pattern-2'){
			$reg_expre='pattern="^(?=.*\d).{4,8}$"';
		}else if($passPattern == 'pattern-3'){
			$reg_expre='pattern="^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*)[0-9a-zA-Z]{6,}$"';
		}else if($passPattern == 'pattern-4'){
			$reg_expre='pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z0-9]+.*).{8,}"';
		}else if($passPattern == 'pattern-5'){
			$reg_expre='pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"';
		}
	}

	//Svg Content
	$queSvg = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" class="svg-inline--fa fa-question-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z"></path></svg>';
	
	if( !empty($label) ){
		$passwordField .= '<label class="tpgb-form-label" for="'.esc_attr($type).'"> '.esc_html($label).' </label>';
	}
	
	$passwordField .= '<div class="tpgb-form-controls tpgb-pass-field">' ;
		$passwordField .= '<input required class="tpgb-form-password '.esc_attr($passVisi).' tpgb-error-load " type="password" id="'.esc_attr( $type ).'" name="'.esc_attr( $type ).'" placeholder="'.esc_attr( $placeholder ).'" required '.( !empty($strongPass) && !empty($passPattern) ? $reg_expre : '' ).' data-error="'.esc_attr($error).'" />';
		if(!empty($honeypot)){
			$passwordField .= '<input type="password" name="tphoney-password" id="tphoney_password" class="tpgb-honey-input" />';
		}
		if(!empty($toggle)){
			$passwordField .= '<span class="tpgb-password-show" data-id="'.esc_attr($type).'" data-hicon="'.esc_attr($hIcon).'" data-sicon="'.esc_attr($sIcon).'">';
				$passwordField .= '<i class="'.esc_attr($sIcon).'"> </i>';
			$passwordField .= '</span>';
		}
		if(!empty($strongPass) && !empty($passHint) && !empty($passVisi) && $passVisi == 'click' ){
			$passwordField .= '<span class="tpgb-passHint">';
				$passwordField .= '<i class="'.esc_attr($clickIcon).'"> </i>';
			$passwordField .= '</span>';
		}
	$passwordField .= '</div>';
	
	$passwordField .= '<div class="tpgb-pass-indicator '.esc_attr($passHintlay).' '.esc_attr($passPattern).' ">';
		if($passPattern=='pattern-1' || $passPattern=='pattern-4' || $passPattern=='pattern-5'){
			$passwordField .= '<div class="tpgb-pass-list"><span class="tp-min-eight-character">'.$queSvg.'</span> <p> '.esc_html__( 'Minimum Eight Characters' , 'tpgbp' ).' </p> </div>';
		}
		if($passPattern=='pattern-1' || $passPattern=='pattern-2' || $passPattern=='pattern-3'){					
			$passwordField .= '<div class="tpgb-pass-list"><span class="tp-one-number">'.$queSvg.'</span> <p> '.esc_html__('1 number (0-9)' , 'tpgbp').' </p> </div>';
		}
		if($passPattern=='pattern-1' || $passPattern=='pattern-3'){
			$passwordField .= '<div class="tpgb-pass-list"><span class="tp-low-lat-case">'.$queSvg.'</span> <p> '.esc_html__('1 letter (Aa-Zz)' , 'tpgbp').' </p> </div>';
		}
		
		if($passPattern=='pattern-1'){					
			$passwordField .= '<div class="tpgb-pass-list"><span class="tp-one-special-char">'.$queSvg.'</span> <p> '.esc_html__('1 Special Character (!@#$%^&*)','tpgbp').' </p> </div>';
		}
		
		if($passPattern=='pattern-2'){
			$passwordField .= '<div class="tpgb-pass-list""><span class="tp-four-eight-character">'.$queSvg.'</span>  <p> '.esc_html__('Four to Eight characters' , 'tpgbp').' </p> </div>';
		}
		
		if($passPattern=='pattern-3'){
			$passwordField .= '<div class="tpgb-pass-list""><span class="tp-min-six-character">'.$queSvg.'</span>  <p> '.esc_html__('Minimum six characters' , 'tpgbp').' </p> </div>';
		}
		
		if($passPattern=='pattern-4' || $passPattern=='pattern-5'){	
			$passwordField .= '<div class="tpgb-pass-list""><span class="tp-low-upper-case">'.$queSvg.'</span>  <p> '.esc_html__('1 lowercase(a-z) & 1 uppercase(A-Z)' , 'tpgbp').' </p> </div>';
		}
		if($passPattern=='pattern-4'){
			$passwordField .= '<div class="tpgb-pass-list""><span class="tp-digit-alpha">'.$queSvg.'</span>  <p> '.esc_html__('1 alphanumeric (1Aa-9Zz)' , 'tpgbp').' </p> </div>';
		}
		
		if($passPattern=='pattern-5'){
			$passwordField .= '<div class="tpgb-pass-list""><span class="tp-number-special">'.$queSvg.'</span>  <p> '.esc_html__('1 number(0-9) Or 1 special character  (!@#$%^&*)' , 'tpgbp').' </p> </div>';
		}
	$passwordField .= '</div>';


	return $passwordField;
}

//Get Button 
function tpgb_getButton($btnText,$foType,$cbtnIcon , $iconPosi){
	$getbtn = '';
	$getbtn .= '<div class="tpgb-submit-wrap">';
		$getbtn .= '<button type="submit" name="wp-submit" class="tpgb-'.esc_attr($foType).'-button"  > ';
			$getbtn .= '<div class="tpgb-lrbtn-icon">';
				if(!empty($cbtnIcon)){
					$getbtn .= '<i class="tpgb-lrbtn '.esc_attr($cbtnIcon).'"></i>';
				}
				$getbtn .= '<span class="icon-'.esc_attr($iconPosi).'"> '.esc_html( $btnText ).' </span>';
			$getbtn .= '</div>';
		$getbtn .= '</button>';
	$getbtn .= '</div>';
	return $getbtn;
}

//Get Login Form
function tpgb_getloginForm($lfield , $Title , $lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon , $backArrow , $block_id , $losrecaptch ){
	$loginForm = '';
	$lostPassRe = $magicLink = false;
	$magicField= $magicmailData = [];
	if(!empty($Title)){
		$loginForm .= '<h2 class="tpgb-form-title"> '.esc_html( $Title).' </h2>';
	}
	
	$loginForm .= '<form class="tpgb-login-form">';
	if( !empty($lfield)){
		foreach($lfield as $index => $LField){
			$loginForm .= '<div class="tpgb-field-group tp-repeater-item-'.esc_attr($LField['_key']).' '.(!empty($LField['loginfName']) && $LField['loginfName'] == 'remember-me' ? ' tpgb-remember-wrap' : ( !empty($LField['loginfName']) && $LField['loginfName'] == 'lost-password' || $LField['loginfName'] == 'register' && isset($LField['showInline']) && !empty($LField['showInline'] ) ? ' tpgb-lostpass-relink' : ''  )  ).' '.(!empty($LField['loginfName']) && $LField['loginfName'] == 'magic-link' ? ' tpgb-magic-active' : '').'  '.( !empty($LField['loginfName']) && $LField['loginfName'] == 'extra-text' ? 'tpgb-lore-exttxt' : '').'  '.( !empty($LField['loginfName']) && $LField['loginfName'] == 'register' ? ' tpgb-login-regtxt' : '').' ">';
				if(!empty($LField['loginfName'])){
					if($LField['loginfName'] == 'username'){
						$loginForm .= tpgb_getTextField($LField['FieldLabel'],$LField['placeholder'],$LField['loginfName'],'','text','');
					}
					if($LField['loginfName'] == 'password'){
						$LpassFieldLabel = ( isset($LField['FieldLabel']) && !empty($LField['FieldLabel'])) ? $LField['FieldLabel'] : '';
						$Lpasspholder = ( isset($LField['placeholder']) && !empty($LField['placeholder'])) ? $LField['placeholder'] : '';

						$loginForm .= tpgb_getpasswordField( $LpassFieldLabel,$Lpasspholder,$LField['loginfName'],'','','','','','','','','','','','','login');
					}

					if($LField['loginfName'] == 'login-button'){

						$LloginIcon = (!empty($LField['loginIcon'])) ? $LField['loginIcon'] : '';
						$LfobtniPosi = ( isset($LField['LfobtniPosi']) && !empty($LField['LfobtniPosi'])) ? $LField['LfobtniPosi'] : '';
 
						$loginForm .= tpgb_getButton($LField['content'],'login' , $LloginIcon , $LfobtniPosi );
					}
					if($LField['loginfName'] == 'remember-me'){
						$loginForm .= '<div class="tpgb-remember-me">';
							$loginForm .= '<input type="checkbox" id="remember-me'.esc_attr($block_id).'" name="rememberme" class="tpgb-login-checkbox" /> ';
							$loginForm .= '<label class="tpgb-choice-label" for="remember-me'.esc_attr($block_id).'"> '.esc_html($LField['content']).' </label>';
						$loginForm .= '</div>';
					}
					if($LField['loginfName'] == 'lost-password' || $LField['loginfName'] == 'register' || $LField['loginfName'] == 'magic-link'){
						if($LField['loginfName'] == 'lost-password'){
							$lostPassRe = true;
							$loginForm .= '<a class="tpgb-lost-password" href="javascript:void(0)" aria-label="'.esc_attr($LField['content']).'" > '.esc_html($LField['content']).' </a>';
						}
						if($LField['loginfName'] == 'register'){
							$loBeTxt = ( isset($LField['loBeTxt']) && !empty($LField['loBeTxt'])) ? $LField['loBeTxt'] : '';
							$regLink = '';
							if( isset($LField['regisLink']) && !empty($LField['regisLink']) ){
								if($LField['regisLink'] == 'default'){
									$regLink = wp_registration_url();
								}else{
									$regLink = ( isset($LField['regisUrl']) && !empty($LField['regisUrl']) && !empty($LField['regisUrl']['url'])  ) ? $LField['regisUrl']['url'] : '';
								}
							}
							$loginForm .= '<div class="tpgb-loginbefore-text">'.esc_html($loBeTxt).'</div>';
							$loginForm .= '<a class="tpgb-register-link" href="'.esc_url($regLink).'" aria-label="'.esc_attr($LField['regisTxt']).'" > '.esc_html($LField['regisTxt']).' </a>';
						}
						if($LField['loginfName'] == 'magic-link'){
							$loginForm .= '<a class="tpgb-magic-tag" href="#" aria-label="'.(!empty($LField['content']) ? esc_attr__($LField['content']) : esc_attr__('Magic Link','tpgbp')).'" > '.(!empty($LField['content']) ? esc_html($LField['content']) : esc_html__('Magic Link','tpgbp')).' </a>';
							
							$magicmailData = [
								'mailsub' => (!empty($LField['mabmailSub'])) ? $LField['mabmailSub'] : '',
								'mailCnt' => (!empty($LField['magicMsg'])) ? $LField['magicMsg'] : '',
								'redirectUrl' => (!empty($LField['magicrelink']) && !empty($LField['magicrelink']['value']) ) ? get_permalink($LField['magicrelink']['value']) :  get_the_permalink() ,
								'nonce' => wp_create_nonce( 'tpgb-login-magic-link' )
							];
							$magicLink = true;
							$magicField = [
								'formlabel' => (!empty($LField['magiclabel'])) ? $LField['magiclabel'] : '',
								'magicplace' => (!empty($LField['magicplace'])) ? $LField['magicplace'] : '',
								'mabtnText' => (!empty($LField['mabtnText'])) ? $LField['mabtnText'] : '',
								'magicmailData' => $magicmailData,
							];
						}
					}
					if($LField['loginfName'] == 'social'){
						$lgooglelog = (isset($LField['googlelog']) && !empty($LField['googlelog'])) ? $LField['googlelog'] : '';
						$lgooglId = (!empty($LField['googlId'])) ? $LField['googlId'] : '';

						$lsolayout = (isset($LField['solayout']) && !empty($LField['solayout'])) ? $LField['solayout'] : '';
						$lfacebooklog = (isset($LField['facebooklog']) && !empty($LField['facebooklog'])) ? $LField['facebooklog'] : '';
						$lfaceAppid = (isset($LField['faceAppid']) && !empty($LField['faceAppid'])) ? $LField['faceAppid'] : '';
						$lfaceSecid = (isset($LField['faceSecid']) && !empty($LField['faceSecid'])) ? $LField['faceSecid'] : '';
						$lsoctmUrl = (isset($LField['soctmUrl']) && !empty($LField['soctmUrl'])) ? $LField['soctmUrl'] : '';
						$lgooglThm = (isset($LField['googlThm']) && !empty($LField['googlThm'])) ? $LField['googlThm'] : '';
						$lgooglePic = (isset($LField['googlePic']) && !empty($LField['googlePic'])) ? $LField['googlePic'] : '';
						$lgbtnType = (isset($LField['gbtnType']) && !empty($LField['gbtnType'])) ? $LField['gbtnType'] : '';
						$lgostandshape = (isset($LField['gostandshape']) && !empty($LField['gostandshape'])) ? $LField['gostandshape'] : '';
						$lgobtnTxt = (isset($LField['gobtnTxt']) && !empty($LField['gobtnTxt'])) ? $LField['gobtnTxt'] : '';
						$lgobtnSize = (isset($LField['gobtnSize']) && !empty($LField['gobtnSize'])) ? $LField['gobtnSize'] : '';
						$lgobctWidth = (isset($LField['gobctWidth']) && !empty($LField['gobctWidth'])) ? (int) $LField['gobctWidth'] : '';
						$lgoioshape = (isset($LField['goioshape']) && !empty($LField['goioshape'])) ? $LField['goioshape'] : '';
						$lgoioSize = (isset($LField['goioSize']) && !empty($LField['goioSize'])) ? $LField['goioSize'] : '';

						$loginForm .= tpgb_getSocialbtn( $lsolayout , $lfacebooklog , $lfaceAppid , $lfaceSecid , $lgooglelog , $lgooglId  , $lsoctmUrl , 'login' , $lgooglThm ,$lgooglePic , $LField['_key'] , $lgbtnType , $lgostandshape , $lgobtnTxt , $lgobtnSize , $lgobctWidth , $lgoioshape , $lgoioSize );
					}
					if($LField['loginfName'] == 'extra-text'){
						$loginForm .= '<span class="tpgb-register-msg" > '.wp_kses_post($LField['content']).' </span>';
					}
				}
			$loginForm .= '</div>';
		}
	}
	//Create nonce Field Ajax check  
	$loginForm .= wp_nonce_field( 'tpgb-ajax-login-nonce', 'tpgb-user-login-token' ,false,false);
	$loginForm .= '<div class="tpgb-regis-noti">';
		$loginForm .= '<div class="tpgb-re-response"></div>';
	$loginForm .= '</div>';
	$loginForm .= '</form>';

	if( isset($lostPassRe) && !empty($lostPassRe)){
		$loginForm .= tpgb_getforgetform($lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , 'tpgb-login-lost','' , '' , $backArrow , $block_id , $losrecaptch , $lobtnIcon );
	}
	if( isset($magicLink) && !empty($magicLink)){
		$loginForm .= tpgb_getforgetform( $magicField['formlabel'] , $magicField['magicplace'] , 'magic-link' , '' , $magicField['mabtnText'] , 'tpgb-login-lost','magic' , $magicmailData , $backArrow , $block_id , $losrecaptch , '' );
	}
	return $loginForm;
}

//Get Check Box Httpgbml
function tpgb_getCheckBox($data,$key , $uid){
	$check = '';
	$check .= '<div class="tpgb-recheck-wrap tpgb-check-'.esc_attr($key).'">';
		$check .= '<input type="checkbox" name="'.esc_attr( $key ).'" id="'.esc_attr( $uid ).'" class="tpgb-login-checkbox" required >';
		$check .= '<label class="tpgb-choice-label" for="'.esc_attr( $uid ).'" > '.esc_html($data).' </label>';
	$check .= '</div>';

	return $check;
}

// Get Choice Html
function tpgb_get_choice_html( $label ,  $type , $data , $name ){
	$tpgbChoice = '';
	
	$tpgbChoice .= '<label class="tpgb-form-label"> '.esc_html( $label ).' </label>';
	$tpgbChoice .= '<div class="tpgb-reg-choice">';
		foreach( $data as $key => $val ){
			$tpgbChoice .= '<div class="tpgb-choice">';
				$tpgbChoice .= '<input id="'.esc_attr($key).'" type="'.esc_attr($type).'" name="'.esc_attr($name).'" value="'.esc_attr($key).'" />';
				$tpgbChoice .= '<label for="'.esc_attr($key).'" class="tpgb-choice-label">'.esc_html($val).'</label>';
			$tpgbChoice .= '</div>';
		}
	$tpgbChoice .= '</div>';

	return $tpgbChoice;
}

// Get Select Drop Down
function tpgb_get_select( $label ,  $type , $data , $name ){
	$tpgbSelect = '';

	$tpgbSelect .= '<label class="tpgb-form-label"> '.esc_html( $label ).' </label>';
	$tpgbSelect .= '<select name="'.esc_attr($name).'" >';
		foreach( $data as $key => $val ){
			$tpgbSelect .= '<option value="'.esc_attr($key).'">'.esc_html($val).'</option>';
		}
	$tpgbSelect .= '</select>';

	return $tpgbSelect;
	
}

//Get Register Form
function tpgb_getregisterForm($refield,$Title,$honeypot,$block_id){
	$regisForm = '';
	
	if(!empty($Title)){
		$regisForm .= '<h2 class="tpgb-form-title"> '.esc_html( $Title).' </h2>';
	}

	$regisForm .= '<form class="tpgb-register-form" method="post" action="" name="tpgb-register-form" enctype="multipart/form-data" >';
	$regisForm .= '<input type="hidden" name="action" value="tpgb_register_user" />';
	if( !empty($refield)){
		foreach($refield as $index => $RField){
			
			$regfieldLabel = (isset($RField['regfieldLabel']) && !empty($RField['regfieldLabel']) ) ? $RField['regfieldLabel'] : '';
			$replaceholder = (isset($RField['replaceholder']) && !empty($RField['replaceholder']) ) ? $RField['replaceholder'] : '';
			$regisfName = (isset($RField['regisfName']) && !empty($RField['regisfName']) ) ? $RField['regisfName'] : '';
			$rerrorMsg = (isset($RField['errorMsg']) && !empty($RField['errorMsg']) ) ? $RField['errorMsg'] : '';

			$regisForm .= '<div class="tpgb-field-group tp-repeater-item-'.esc_attr($RField['_key']).'  '.( isset($RField['regisfName']) && !empty($RField['regisfName']) && $RField['regisfName'] == 'extra-text' ? 'tpgb-lore-exttxt' : '').' '.( isset($RField['regisfName']) && !empty($RField['regisfName']) && $RField['regisfName'] == 'login' ? ' tpgb-login-regtxt' : '').' ">';
				if(!empty($RField['regisfName'])){
					if($RField['regisfName'] == 'first-name'){
						$regisForm .= tpgb_getTextField($regfieldLabel,$replaceholder,$regisfName,$rerrorMsg,'text',$honeypot);
					}
					if($RField['regisfName'] == 'last-name'){
						$regisForm .= tpgb_getTextField($regfieldLabel,$replaceholder,$regisfName,$rerrorMsg,'text',$honeypot);
					}
					if($RField['regisfName'] == 'username'){
						$regisForm .= tpgb_getTextField($regfieldLabel,$replaceholder,$regisfName,$rerrorMsg,'text',$honeypot);
					}
					if($RField['regisfName'] == 'email'){
						$regisForm .= tpgb_getTextField($regfieldLabel,$replaceholder,$regisfName,$rerrorMsg,'email',$honeypot);
					}
					if($RField['regisfName'] == 'password'){
						$RpassToggle = ( isset($RField['passToggle']) && !empty($RField['passToggle'])) ? $RField['passToggle'] : '';
						$RshowIcon = ( isset($RField['showIcon']) && !empty($RField['showIcon']) ) ? $RField['showIcon'] : '';
						$RhideIcon = ( isset($RField['hideIcon']) && !empty($RField['hideIcon']) ) ? $RField['hideIcon'] : '';
						$RstrongPass = ( isset($RField['strongPass']) && !empty($RField['strongPass'])) ? $RField['strongPass'] : '';
						$RpassPattern = ( isset($RField['passPattern']) && !empty($RField['passPattern'])) ? $RField['passPattern'] : '';
						$RpassHint = ( isset($RField['passHint']) && !empty($RField['passHint'])) ? $RField['passHint'] : '';
						$RpassVisi = ( isset($RField['passVisi']) && !empty($RField['passVisi'])) ? $RField['passVisi'] : '';
						$RclickIcon = ( isset($RField['clickIcon']) && !empty($RField['clickIcon'])) ? $RField['clickIcon'] : '';
						$RpassHintlay = ( isset($RField['passHintlay']) && !empty($RField['passHintlay'])) ? $RField['passHintlay'] : '';
						$RpassMeter = ( isset($RField['passMeter']) && !empty($RField['passMeter'])) ? $RField['passMeter'] : '';


						$regisForm .= tpgb_getpasswordField($regfieldLabel,$replaceholder,$regisfName, $RpassToggle , $RshowIcon , $RhideIcon, $RstrongPass , $RpassPattern , $RpassHint , $RpassVisi , $RclickIcon , $RpassHintlay , $RpassMeter, $rerrorMsg,$honeypot,'register');
					}
					if($RField['regisfName'] == 'confirm-password'){
						$regisForm .= tpgb_getpasswordField($regfieldLabel,$replaceholder,$regisfName,'','','','','','','','','','',$rerrorMsg,$honeypot ,'');
					}
					if(!empty($RField['passMeter'])){
						$regisForm .=  '<div class="tpgb-password-strength-wrapper style-1 after-label ">';
							$regisForm .= '<span id="password-strength"></span>';
						$regisForm .= '</div>';
					}
					if($RField['regisfName'] == 'register-button'){
						$RrebtnIcon = ( !empty($RField['rebtnIcon'])) ? $RField['rebtnIcon'] : '';
						$RfobtniPosi = ( isset($RField['RfobtniPosi']) && !empty($RField['RfobtniPosi'])) ? $RField['RfobtniPosi'] : '';

						$regisForm .= tpgb_getButton($RField['rebtnTxt'],'register' , $RrebtnIcon , $RfobtniPosi );
					}
					if($RField['regisfName'] == 'terms'){
						$regisForm .= tpgb_getCheckBox( $RField['terms'] , 'terms' , $RField['_key'] );
					}
					if($RField['regisfName'] == 'mailChimp'){
						$regisForm .= tpgb_getCheckBox('Yes, Please subscribe me for Newsletters.' , 'mailscb' , $RField['_key']);
					}
					
					if( class_exists( 'ACF' ) && $RField['regisfName'] == 'acf-field'){
						$fieldarr = ['image','file'];
						if( isset($RField['acfKey']) && !empty($RField['acfKey']) ){
							$field = acf_get_field( $RField['acfKey'] );
							if( isset( $field['type']) && !empty($field['type']) && ! in_array($field['type'] , $fieldarr ) ){
								$regisForm .= '<input type="hidden" name="tpgb_acf_key[]" value="'.$RField['acfKey'].'" />';
							}
							
							if( !empty($field) && isset( $field['type'] ) && !empty($field['type']) ){
								$acf_placeho =  (isset($field['placeholder']) && !empty($field['placeholder'])) ? $field['placeholder'] : '';
								if( $field['type'] == 'text' || $field['type'] == 'number'){
									$regisForm .= tpgb_getTextField( $field['label'] , $acf_placeho, 'tpgb_acf[]' ,$RField['errorMsg'], $field['type'] , $honeypot);
								} else if( $field['type'] == 'checkbox'){
									if( isset($field['choices']) && !empty($field['choices'])){
										$regisForm .= tpgb_get_choice_html( $field['label'] , $field['type'] , $field['choices'] , 'tpgb_acf['.$RField['acfKey'].'][]'  );
									}
								} else if( $field['type'] == 'radio'){
									if( isset($field['choices']) && !empty($field['choices'])){
										$regisForm .= tpgb_get_choice_html( $field['label'] , $field['type'] , $field['choices'] , 'tpgb_acf[]'  );
									}
								} else if( $field['type'] == 'select'){
									if( isset($field['choices']) && !empty($field['choices'])){
										$regisForm .= tpgb_get_select( $field['label'] , $field['type'] , $field['choices'] , 'tpgb_acf[]'  );
									}
								} else if( $field['type'] == 'image' || $field['type'] == 'file' ){
									$regisForm .= '<input type="hidden" name="tpgb_file_key[]" value="'.$RField['acfKey'].'" />';
									$regisForm .= tpgb_getTextField( $field['label'] , $acf_placeho, 'tpgb_file_acf[]' ,$RField['errorMsg'], 'file' , $honeypot);
								}
							}
						}
					}

					if($RField['regisfName'] == 'login'){ 
						$loginLink = '';
						$regisForm .= '<div class="tpgb-loginbefore-text">';
							$regisForm .= esc_html($RField['beforeTxt']) ;
						$regisForm .= '</div>';
					
						if( isset($RField['loginLink']) && !empty($RField['loginLink']) ){
							if($RField['loginLink'] == 'default'){
								$loginLink = wp_login_url();
							}else{
								$loginLink = ( isset($RField['relogUrl']) && !empty($RField['relogUrl']) && !empty($RField['relogUrl']['url'])  ) ? $RField['relogUrl']['url'] : '';
							}
						}

						$regisForm .= '<a class="tpgb-re-login" aria-label="'.esc_attr($RField['loginTxt']).'" href="'.esc_url($loginLink).'"> '.esc_html($RField['loginTxt']).' </a>';
					}
					if($RField['regisfName'] == 'extra-text'){
						$regisForm .= '<span class="tpgb-register-msg"> '.esc_html($RField['terms']).' </span>';
					}
					if($RField['regisfName'] == 'recaptcha'){
						$regisForm .= '<div class="tpgb-recaptch">';
							$regisForm .= '<div id="tpgb-inline-badge-'.esc_attr($block_id).'"></div>';
						$regisForm .= '</div>';
						$regisForm .= '<div class="tpgb-recaptch-key"></div>';
					}
					if($RField['regisfName'] == 'social'){
						$rgooglelog = (isset($RField['googlelog']) && !empty($RField['googlelog'])) ? $RField['googlelog'] : '';
						$rgooglId = (!empty($RField['googlId'])) ? $RField['googlId'] : '';
						$rsolayout = (isset($RField['solayout']) && !empty($RField['solayout'])) ? $RField['solayout'] : '';
						$rfacebooklog = (isset($RField['facebooklog']) && !empty($RField['facebooklog'])) ? $RField['facebooklog'] : '';
						$rfaceAppid = (isset($RField['faceAppid']) && !empty($RField['faceAppid'])) ? $RField['faceAppid'] : '';
						$rfaceSecid = (isset($RField['faceSecid']) && !empty($RField['faceSecid'])) ? $RField['faceSecid'] : '';
						$rsoctmUrl = (isset($RField['soctmUrl']) && !empty($RField['soctmUrl'])) ? $RField['soctmUrl'] : '';
						$rgooglThm = (isset($RField['googlThm']) && !empty($RField['googlThm'])) ? $RField['googlThm'] : '';
						$rgooglePic = (isset($RField['googlePic']) && !empty($RField['googlePic'])) ? $RField['googlePic'] : '';
						$RgbtnType = (isset($RField['gbtnType']) && !empty($RField['gbtnType'])) ? $RField['gbtnType'] : '';
						$Rgostandshape = (isset($RField['gostandshape']) && !empty($RField['gostandshape'])) ? $RField['gostandshape'] : '';
						$RgobtnTxt = (isset($RField['gobtnTxt']) && !empty($RField['gobtnTxt'])) ? $RField['gobtnTxt'] : '';
						$RgobtnSize = (isset($RField['gobtnSize']) && !empty($RField['gobtnSize'])) ? $RField['gobtnSize'] : '';
						$RgobctWidth = (isset($RField['gobctWidth']) && !empty($RField['gobctWidth'])) ? (int) $RField['gobctWidth'] : '';
						$Rgoioshape = (isset($RField['goioshape']) && !empty($RField['goioshape'])) ? $RField['goioshape'] : '';
						$RgoioSize = (isset($RField['goioSize']) && !empty($RField['goioSize'])) ? $RField['goioSize'] : '';


						$regisForm .= tpgb_getSocialbtn($rsolayout , $rfacebooklog , $rfaceAppid , $rfaceSecid , $rgooglelog , $rgooglId , $rsoctmUrl , 'register' , $rgooglThm  ,$rgooglePic , $RField['_key'] , $RgbtnType , $Rgostandshape , $RgobtnTxt , $RgobtnSize , $RgobctWidth , $Rgoioshape , $RgoioSize );
					}
				}
			$regisForm .= '</div>';
		}
	}
	//Create nonce Field Ajax check  
	$regisForm .= wp_nonce_field( 'ajax-login-nonce', 'tpgb-user-reg-token' ,false,false);
	$regisForm .= '<div class="tpgb-regis-noti">';
		$regisForm .= '<div class="tpgb-re-response"></div>';
	$regisForm .= '</div>';
	$regisForm .= '</form>';
	
	return $regisForm;
}

//Get Social Login Btn
function tpgb_getSocialbtn($solayout , $facebooklog , $faceAppid , $faceSecid , $googlelog , $googlId , $redirUrl , $fType , $googlThm , $googlePic , $id , $gbtnType , $gostandshape , $gobtnTxt , $gobtnSize , $gobctWidth , $goioshape , $goioSize ){
	$socialog = '' ;
	$socialId = [];
	
	$googlePic = (!empty($googlePic)) ? 'yes' : 'no';


	$socialId = [
		'faceAppid' => (!empty($faceAppid)) ? $faceAppid : '',
		'faceSecid' => (!empty($faceSecid)) ? Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($faceSecid), 'ey' ) : '',
		'googlId' => (!empty($googlId)) ? $googlId : '',
		'googlepic' => $googlePic,
		'nonce' => wp_create_nonce('tpgb-social-login'),
		'formType' => $fType,
		'redirUrl' => (!empty($redirUrl) && !empty($redirUrl['url'])) ? $redirUrl['url'] : '',
		'goolthem' => (!empty($googlThm)) ? $googlThm : '',
		'googloTitle' => (!empty($googloTitle)) ? true : false ,
		'uniId' => $id,
		'gbtnType' => $gbtnType,
		'gostandshape' => $gostandshape,
		'gobtnTxt' => $gobtnTxt,
		'gobtnSize' => $gobtnSize,
		'gobctWidth' =>  $gobctWidth,
		'goioshape' => $goioshape,
		'goioSize' => $goioSize

	];

	$socialId = json_encode($socialId);
	
	$socialog .= '<div class="tpgn-socialbtn-wrap tpgb-social-'.(!empty($facebooklog) ? esc_attr($solayout) : '').'" data-socialIds=\'' .$socialId. '\' >';
		if(!empty($facebooklog) && !empty($faceAppid) && !empty($faceSecid) ) {
			$socialog .= '<div class="tpgb-btn-facebook">';
				$socialog .= '<a href="#" class="tpgb-btn-fb" aria-label="'.esc_attr__('Facebook' , 'tpgbp').'">';
					$socialog .= '<div class="tpgb-fb-content">';
						$socialog .= '<div class="tpgb-social-logo">';
							if($solayout == 'solayout-2'){
								$socialog .= '<img src="'.esc_url(''.TPGB_ASSETS_URL.'assets/images/social-review/facebook-white.png').'" alt="FacebookSvg">';
								$socialog .= '<p>Continue with Facebook</p>';
							}else{
								$socialog .= '<img src="'.esc_url(''.TPGB_ASSETS_URL.'assets/images/social-review/facebook-square.svg').'" alt="FacebookSvg">';
								$socialog .= '<p>Log In</p>';
							}
						$socialog .= '</div>';
					$socialog .= '</div>';
				$socialog .= '</a>';
			$socialog .= '</div>';
		}
		if(!empty($googlelog) && !empty($googlId) ) {
			$socialog .= '<div class="tpgb-btn-google">';
				if($googlePic == 'no'){
					$socialog .= '<div class="tpgb-btn-goo-'.esc_attr($id).'" >';
					$socialog .= '</div>';
				}
			$socialog .= '</div>';
		}
	$socialog .= '</div>';

	return $socialog;
}

//Get Login & Register Form
function tpgb_getloginRegis($lofield , $refield , $loginTitle , $regisTitle , $template , $honeypot,$block_id,$lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon , $lorehide , $stpassreq , $backArrow , $losrecaptch , $tab1btnTxt , $tab2btnTxt , $dactiveTab ){
	$loginRegi = '';
	if($lorehide == 'no' || (!isset($_GET['action']) && empty($_GET['action']) ) ){
		$loginRegi .= '<div class="tpgb-form-template">';
			if(!empty($template)) {
				$loginRegi .= '<div class="tpgb-left-temp">';
					ob_start();
						echo Tpgb_Library()->plus_do_block($template);
						$loginRegi .= ob_get_contents();
					ob_end_clean();
				$loginRegi .= '</div>';
			}
		
			$loginRegi .= '<div class="tpgb-form-tab-wrap tpgb-relative-block  '.($template !== '' ? ' tpgb-right-temp' : '' ).'">';
				$loginRegi .= '<div class="tpgb-form-tabbtn">';
					$loginRegi .= '<div class="tpgb-ftab-btn '.($dactiveTab == 'login' ? ' active' : '').'" data-tab="1" > '.wp_kses_post($tab1btnTxt).' </div>';
					$loginRegi .= '<div class="tpgb-ftab-btn '.($dactiveTab == 'register' ? ' active' : '').'" data-tab="2" > '.wp_kses_post($tab2btnTxt).' </div>';
				$loginRegi .= '</div>';

				$loginRegi .= '<div class="tpgb-formtab-content tpgb-relative-block">';
					$loginRegi .= '<div class="tpgb-logintab-content '.($dactiveTab == 'login' ? ' active' : '').'"" data-tab="1" >';
						$loginRegi .= tpgb_getloginForm($lofield , $loginTitle,$lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon , $backArrow , $block_id , $losrecaptch);
					$loginRegi .= '</div>';

					$loginRegi .= '<div class="tpgb-logintab-content '.($dactiveTab == 'register' ? ' active' : '').'" data-tab="2" >';
						$loginRegi .= tpgb_getregisterForm($refield , $regisTitle,$honeypot,$block_id , '');
					$loginRegi .= '</div>';
				$loginRegi .= '</div>';
			$loginRegi .= '</div>';
		$loginRegi .= '</div>';
	}
	return $loginRegi;
}

//Get Lost Password Form
function tpgb_getforgetform($Label , $placeholder , $key , $title , $lobtntxt , $lostClass , $name , $data , $bIcon , $block_id , $lorecap , $lobtnIcon ){
	$lostPass = $mdataAtrr =  '';

	if($name == 'magic' && !empty($data)){
		$mdataAtrr = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($data), 'ey');
		$mdataAtrr = htmlspecialchars(json_encode($mdataAtrr), ENT_QUOTES, 'UTF-8');
		$mdataAtrr = 'data-magicdata=\'' .$mdataAtrr. '\' ';
	}

	$lostPass .= '<form class=" '.($key == 'forget-password' ? 'tpgb-lostpass-form' : 'tpgb-magic-form').'  '.esc_attr($lostClass).'" method="post" '.$mdataAtrr.' >';
		if(!empty($lostClass)){
			$lostPass .= '<a class="tpgb-lpu-back" aria-label="'.esc_attr__('Back Icon' , 'tpgbp').'">';
				$lostPass .='<i class="'.esc_attr($bIcon).'"></i> ';
			$lostPass .= '</a>';
		}
		if(!empty($title)){
			$lostPass .= '<h5 class="tpgb-forgot-password-label">'.esc_html($title).'</h5>';
		}
		
		$lostPass .= '<div class="tpgb-field-group">';
			$lostPass .= tpgb_getTextField($Label , $placeholder , $key , '' , 'text','' );
		$lostPass .= '</div>';

		if(!empty($lorecap) && $lorecap == 'yes'){
			$lostPass .= '<div class="tpgb-field-group">';
				$lostPass .= '<div class="tpgb-recaptch">';
					$lostPass .= '<div id="tpgb-inline-badge-tpgb-lostpass-recaptch"></div>';
				$lostPass .= '</div>';
				$lostPass .= '<div class="tpgb-lorecaptch-key"></div>';
			$lostPass .= '</div>';
		}
		
		$lostPass .= '<div class="tpgb-field-group">';
			$lostPass .= tpgb_getButton($lobtntxt , $key , $lobtnIcon , 'after' );
		$lostPass .= '</div>';

	//Create nonce Field Ajax check 
	$lostPass .= '<div class="tpgb-regis-noti">';
		$lostPass .= '<div class="tpgb-re-response"></div>';
	$lostPass .= '</div>';

	$lostPass .= '</form>';

	return $lostPass;
}

//Get All Login Form
function tpgb_getAllForm($type , $loField , $regisField , $loginTitle , $regisTitle , $blockTemp , $honeypot , $block_id , $lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon , $lorehide , $stpassreq , $backArrow , $losrecaptch , $reCaptch_key,$lorecaposition , $tab1btnTxt , $tab2btnTxt , $dactiveTab ){
	$AllForm = '';
	if($type == 'login' && ( empty($_GET['action'])) || ( !empty($_GET['action']) && $_GET['action'] !='tpgbreset' ) ){
		$AllForm .= tpgb_getloginForm($loField , $loginTitle , $lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon  , $backArrow , $block_id , $losrecaptch );
	}else if( ( isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == 'tpgbreset' ) ){
		if( !empty($lorehide) && $lorehide=='yes') {
			$AllForm .= tpgb_getlostpass_form($stpassreq,$losrecaptch,$block_id,$reCaptch_key,$lorecaposition);
		}else{
			$AllForm .= tpgb_getloginForm($loField , $loginTitle , $lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon  , $backArrow , $block_id , $losrecaptch );
			$AllForm .= tpgb_getlostpass_form($stpassreq,$losrecaptch,$block_id,$reCaptch_key,$lorecaposition);
		}
		
	}
	
	if($type == 'register'){
		$AllForm .= tpgb_getregisterForm($regisField , $regisTitle,$honeypot,$block_id );
	}
	if($type == 'login-register'){
		$AllForm .= tpgb_getloginRegis($loField , $regisField , $loginTitle , $regisTitle , $blockTemp , $honeypot,$block_id , $lopassLabel , $loplaceho , $formName , $loheading , $lobtntxt , $lobtnIcon , $lorehide , $stpassreq , $backArrow , $losrecaptch , $tab1btnTxt , $tab2btnTxt , $dactiveTab );
	}

	return $AllForm;
}

// Get reset Password Form
function tpgb_getlostpass_form($stpassreq,$losrecaptch,$block_id , $reCaptch_key,$lorecaposition){
	$attributes = array();
	$tpgblpForm = '';
	if ( is_user_logged_in() && !current_user_can('editor') ) {
		echo  esc_html__( 'You are already signed in.', 'tpgbp' );
	} else {			
		if ( isset( $_GET['datakey'] )) {
			$forgotresdata = Tpgbp_Pro_Blocks_Helper::tpgb_check_decrypt_key($_GET['datakey']);
			$forgotresdata = json_decode(stripslashes($forgotresdata),true);
			$attributes['login'] = wp_unslash( $forgotresdata['login'] );
			$attributes['key'] = wp_unslash( $forgotresdata['key'] );
			$attributes['forgoturl'] = wp_unslash( $forgotresdata['forgoturl'] );
		}
	}
	if(!empty($attributes)){
		
		$pattenReset='';

		if(!empty($stpassreq) && $stpassreq == 'yes'){
			$pattenReset='pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"';
		}
		
		$forgotres = $forgotreskey = [];
		$forgotres['login'] = $attributes['login'];
		$forgotres['forgoturl'] = $attributes['forgoturl'];
		$forgotres['key'] = $attributes['key'];

		if(!empty($losrecaptch) && $losrecaptch == 'yes'){
			$forgotreskey['blockId'] = 'tpgb-reset-recaptch';
			$forgotreskey['recaptchEn'] = 'yes';
			$forgotreskey['recaptchaKey'] = $reCaptch_key;
			$forgotreskey['recaptchaPos'] = $lorecaposition;
			$recript = '<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=tpgb_onLoadReCaptcha"></script>';
		}

		$forgotres['noncesecure'] = wp_create_nonce( 'tpgb_reset_action' );
		
		$forgotreskey['resetHtml'] = [
			'loadingTxt' => '<span class="loading-spinner-reg"><i class="far fa-times-circle" aria-hidden="true"></i></span>',
			'redirUrl' => $forgotres['forgoturl'],
		];
		$forgotreskey['resetpdata'] = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($forgotres), 'ey' );


		$redataAttr = 'data-lostpassData=\'' .htmlspecialchars(json_encode($forgotreskey), ENT_QUOTES, 'UTF-8'). '\' ';

		$tpgblpForm .= '<form class="tpgb-rp-form" method="post" '.$redataAttr.' >';
			$tpgblpForm .= '<div class="tpgb-field-group">';
					$tpgblpForm .= '<label class="tpgb-form-label" for="repassword"> '.esc_html__('Reset Password' , 'tpgbp').' </label>';
					$tpgblpForm .= '<div class="tpgb-form-controls">';
						$tpgblpForm .= '<input required="" class="tpgb-form-password" type="password" id="repassword" name="repassword" placeholder="'.esc_html__('****' , 'tpgbp').'" required '.$pattenReset.' >';
					$tpgblpForm .= '</div>';
			$tpgblpForm .= '</div>';
			$tpgblpForm .= '<div class="tpgb-field-group">';
				$tpgblpForm .= '<label class="tpgb-form-label" for="repassword">'.esc_html__('Reenter Password' , 'tpgbp').' </label>';
				$tpgblpForm .= '<div class="tpgb-form-controls">';
					$tpgblpForm .= '<input required="" class="tpgb-form-password " type="password" id="reenpassword" name="reenpassword" placeholder="****" >';
				$tpgblpForm .= '</div>';
			$tpgblpForm .= '</div>';

			if(!empty($losrecaptch) && $losrecaptch == 'yes'){
				$tpgblpForm .= '<div class="tpgb-field-group">';
					$tpgblpForm .= '<div class="tpgb-recaptch">';
						$tpgblpForm .= '<div id="tpgb-inline-badge-tpgb-reset-recaptch"></div>';
					$tpgblpForm .= '</div>';
					$tpgblpForm .= '<div class="tpgb-resrecaptch-key"></div>';
				$tpgblpForm .= '</div>';
			}

			$tpgblpForm .= '<div class="tpgb-field-group">';
				$tpgblpForm .= tpgb_getButton( 'Reset Password' , 'resetpassword' , '' , '' );
			$tpgblpForm .= '</div>';

		$tpgblpForm .= '<div class="tpgb-regis-noti">';
			$tpgblpForm .= '<div class="tpgb-re-response"></div>';
		$tpgblpForm .= '</div>';
		$tpgblpForm .= '</form>';
	}

	return $tpgblpForm;
}

// After Login Menu
function tpgb_get_acc_login($attr,$curtUser){
	$accMenu = '';
	$accoutMenu = (!empty($attr['accoutMenu'])) ? 'yes' : 'no';
	$userProf = (!empty($attr['userProf'])) ? 'yes' : 'no';
	$meuserName = (!empty($attr['meuserName'])) ? 'yes' : 'no';
	$meeditPro = (!empty($attr['meeditPro'])) ? 'yes' : 'no';
	$editprotxt = (!empty($attr['editprotxt'])) ? $attr['editprotxt'] : '';
	$editproIcon = (!empty($attr['editproIcon'])) ? $attr['editproIcon'] : '';
	$logoutBtn = (!empty($attr['logoutBtn'])) ? 'yes' : 'no';
	$lotouttxt = (!empty($attr['lotouttxt'])) ? $attr['lotouttxt'] : '';
	$lotoutIcon = (!empty($attr['lotoutIcon'])) ? $attr['lotoutIcon'] : '';
	$extraMenu = (!empty($attr['extraMenu'])) ? $attr['extraMenu'] : [];
	$pronamePatt = (!empty($attr['pronamePatt'])) ? $attr['pronamePatt'] : 'none';

	$arr_params = array( 'action', 'datakey');
	$current_url = remove_query_arg( $arr_params );

	$extMenu = '';
	if(!empty($extraMenu)){
		foreach($extraMenu as $extItem){
			$extmTitle = (!empty($extItem['extmTitle'])) ? $extItem['extmTitle'] : '';
			$extmLink = (!empty($extItem['extmLink'])) ? $extItem['extmLink'] : '';
			$extmIcon = (!empty($extItem['extIcon'])) ? $extItem['extIcon'] : '';
			$exaTag = '';

			if(!empty($extmLink) && !empty($extmTitle) ){
				$link = $extmLink && $extmLink['url'] ? $extmLink['url']   : '#';
				$target = (!empty($extmLink['target'])) ? ' target="_blank"' : '';
				$nofollow = (!empty($extmLink['nofollow'])) ? ' rel="nofollow"' : '';
				$exaTag .='<a href="'.esc_url($link).'" '.esc_attr($target).' '.esc_attr($nofollow).' class="tpgb-acc-link" aria-label="'.esc_attr($extmTitle).'" >';
					if(!empty($extmIcon)){
						$exaTag .= '<span class="tpgb-acc-icon"> <i class="'.esc_attr($extmIcon).'" > </i> </span>';
					}
					$exaTag .= wp_kses_post($extmTitle);
				$exaTag .= '</a>';
			}
			if(!empty($exaTag)){
				$extMenu .= '<div class="tpgb-acc-item">';
					$extMenu .= $exaTag;
				$extMenu .= '</div>';
			}
		}
	}

	if(!empty($accoutMenu) && $accoutMenu == 'yes' ){
		$accMenu .= '<div class="tpgb-account-menu">';
			$accMenu .= '<div class="tpgb-account-btn-wrap">';
				if( (!empty($userProf) && $userProf == 'yes') || (!empty($meuserName) && $meuserName == 'yes') ){
					$accMenu .= '<a class="tpgb-account-btn" aria-expanded="true" >';
						$accMenu .= '<span class="tpgb-acc-text">';
							if(!empty($userProf) && $userProf == 'yes'){
								$accMenu  .= get_avatar( $curtUser->user_email, 128 );
							}									
							if(!empty($meuserName) && $meuserName == 'yes'){

								if($pronamePatt == 'first-name' && isset($curtUser->first_name)){
									$accMenu .= $curtUser->first_name;
								}else if($pronamePatt == 'last-name' && isset($curtUser->last_name)){
									$accMenu .= $curtUser->last_name;
								}else if($pronamePatt == 'username' && isset($curtUser->user_login) ){
									$accMenu .= $curtUser->user_login;
								}else if($pronamePatt == 'first-last' && isset($curtUser->first_name) && isset($curtUser->last_name) ){
									$accMenu .= $curtUser->first_name.' '.$curtUser->last_name;
								}else if($pronamePatt == 'last-first' && isset($curtUser->first_name) && isset($curtUser->last_name) ){
									$accMenu .= $curtUser->last_name.' '.$curtUser->first_name;
								}else{
									$accMenu .= $curtUser->nickname;
								}
							}
						$accMenu .= '</span>';
					$accMenu .= '</a>';
				}
				if( (!empty($meeditPro) && $meeditPro == 'yes' && !empty($editprotxt)) || ( !empty($logoutBtn) && $lotouttxt == 'yes' && !empty($lotouttxt)) || !empty($extraMenu) ){
					$accMenu .= '<div class="tpgb-log-menu">';
						$accMenu .= '<div class="tpgb-acct-list">';
							if(!empty($meeditPro) && $meeditPro == 'yes' && !empty($editprotxt)){
								$accMenu .= '<div class="tpgb-acc-item">';
									$accMenu .= '<a href="'.get_edit_user_link().'" class="tpgb-acc-link" aria-label="'.esc_attr($editprotxt).'" >';
										if(!empty($editproIcon)){
											$accMenu .= '<span class="tpgb-acc-icon"> <i class="'.esc_attr($editproIcon).'" > </i> </span>';
										}
										$accMenu .= $editprotxt;
									$accMenu .= '</a>';
								$accMenu .= '</div>';
							}
							if(!empty( $extMenu )){
								$accMenu .= $extMenu;
							}
							if(!empty($logoutBtn) && $logoutBtn == 'yes' && !empty($lotouttxt) ){
								$accMenu .= '<div class="tpgb-acc-item">';
									$accMenu .= '<a href="'.wp_logout_url( $current_url ).'" class="tpgb-acc-link">';
										if(!empty($lotoutIcon)){
											$accMenu .= '<span class="tpgb-acc-icon"> <i class="'.esc_attr($lotoutIcon).'" > </i> </span>';
										}
										$accMenu .= $lotouttxt;
									$accMenu .= '</a>';
								$accMenu .= '</div>';
							}
						$accMenu .= '</div>';
					$accMenu .= '</div>';
				}
			$accMenu .= '</div>';
		$accMenu .= '</div>';
	}


	return $accMenu;
}