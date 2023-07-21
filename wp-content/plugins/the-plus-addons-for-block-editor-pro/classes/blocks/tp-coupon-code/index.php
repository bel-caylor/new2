<?php
/**
 * Block : Coupon Code
 * @since 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_coupon_code_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$className = (!empty($attributes['className'])) ? $attributes['className'] :'';
	$align = (!empty($attributes['align'])) ? $attributes['align'] :'';
	$couponType = (!empty($attributes['couponType'])) ? $attributes['couponType'] :'standard';
	$standardStyle = (!empty($attributes['standardStyle'])) ? $attributes['standardStyle'] :'style-1';
	$directionHint = (!empty($attributes['directionHint'])) ? $attributes['directionHint'] :false;
	$couponText = (!empty($attributes['couponText'])) ? $attributes['couponText'] :'';
	$couponCode = (!empty($attributes['couponCode'])) ? $attributes['couponCode'] :'';
	$codeArrow = (!empty($attributes['codeArrow'])) ? $attributes['codeArrow'] :false;
	$copyBtnText = (!empty($attributes['copyBtnText'])) ? $attributes['copyBtnText'] :'';
	$afterCopyText = (!empty($attributes['afterCopyText'])) ? $attributes['afterCopyText'] :'';
	$visitBtnText = (!empty($attributes['visitBtnText'])) ? $attributes['visitBtnText'] :'';
	$popupTitle = (!empty($attributes['popupTitle'])) ? $attributes['popupTitle'] :'';
	$popupDesc = (!empty($attributes['popupDesc'])) ? $attributes['popupDesc'] :'';
	$actionType = (!empty($attributes['actionType'])) ? $attributes['actionType'] :'click';
	
	$tabReverse = (!empty($attributes['tabReverse'])) ? $attributes['tabReverse'] :false;
	$saveCookie = (!empty($attributes['saveCookie'])) ? $attributes['saveCookie'] :false;
	$hideLink = (!empty($attributes['hideLink'])) ? $attributes['hideLink'] :false;
	$linkMaskText = (!empty($attributes['linkMaskText'])) ? $attributes['linkMaskText'] :'';
	$maskLinkList = (!empty($attributes['maskLinkList'])) ? $attributes['maskLinkList'] :[];
	
	$fillPercent = (!empty($attributes['fillPercent'])) ? $attributes['fillPercent'] :'70';
	$slideDirection = (!empty($attributes['slideDirection'])) ? $attributes['slideDirection'] :'left';
	
	$frontContentType = (!empty($attributes['frontContentType'])) ? $attributes['frontContentType'] :'default';
	$frontContent = (!empty($attributes['frontContent'])) ? $attributes['frontContent'] :'';
	$frontTemp = (!empty($attributes['frontTemp'])) ? $attributes['frontTemp'] : '';
	$backContentType = (!empty($attributes['backContentType'])) ? $attributes['backContentType'] :'default';
	$backTitle = (!empty($attributes['backTitle'])) ? $attributes['backTitle'] : '';
	$backDesc = (!empty($attributes['backDesc'])) ? $attributes['backDesc'] : '';
	$backTemp = (!empty($attributes['backTemp'])) ? $attributes['backTemp'] : '';
	
	$onScrollBar = (!empty($attributes['onScrollBar'])) ? $attributes['onScrollBar'] : false;
	$ovBackFilt = (!empty($attributes['ovBackFilt'])) ? $attributes['ovBackFilt'] : false;
	$backBlur = (!empty($attributes['backBlur'])) ? $attributes['backBlur'] : '1';
	$backGscale = (!empty($attributes['backGscale'])) ? $attributes['backGscale'] : '0';
	
	$redirectLink = (!empty($attributes['redirectLink']['url'])) ? $attributes['redirectLink']['url'] : '';
	$target = (!empty($attributes['redirectLink']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['redirectLink']['nofollow'])) ? 'nofollow' : '';
	$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($attributes['redirectLink']);
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$iduu=get_queried_object_id();
	if(!empty($tabReverse)){				
		$uid_ccd = 'ccd'.esc_attr($block_id).esc_attr($iduu);
	}else{
		$uid_ccd = 'ccd'.esc_attr($block_id);
	}
	$clickAction = $visitLink = $codeArrowclass = $tabReverseClass = '';
	$tnsease = 'tpgb-trans-ease';
	$tnseasea = 'tpgb-trans-ease';
	if($couponType=='standard' && ($standardStyle=='style-4' || $standardStyle=='style-5')){
		$tnseasea = 'tpgb-trans-linear';
	}
		
	if($actionType=='click') {
		$clickAction .= 'href="'.esc_url($redirectLink).'"';
		$clickAction .= ' target="'.esc_attr($target).'"';
		$clickAction .= ' nofollow="'.esc_attr($nofollow).'"';
	} else if($actionType=='popup') {
		if(!empty($tabReverse)){
			$tabReverseClass =" tpgb-tab-cop-rev";
			$clickAction .= 'href="#tpgb-block-'.esc_attr($uid_ccd).'"';
		}else{
			$clickAction .= 'href="'.esc_url($redirectLink).'"';
		}
		$clickAction .= ' target="'.esc_attr($target).'"';
		$clickAction .= ' nofollow="'.esc_attr($nofollow).'"';
		$visitLink .= 'href="'.esc_url($redirectLink).'"';
		$visitLink .= ' target="'.esc_attr($target).'"';
		$visitLink .= ' nofollow="'.esc_attr($nofollow).'"';
	}
	$cpnTextCss =$scrollClass= '';
	$coupon_code_attr = [];
	$coupon_code_attr['id'] = $uid_ccd;
	$coupon_code_attr['couponType'] = $couponType;
	if($couponType=='standard') {
		$coupon_code_attr['actionType'] = $actionType;
		$coupon_code_attr['coupon_code'] = $couponCode;
		$coupon_code_attr['copy_btn_text'] = $copyBtnText;
		$coupon_code_attr['after_copy_text'] = $afterCopyText;
		if($codeArrow){
			$codeArrowclass = 'code-arrow';
			$coupon_code_attr['code_arrow'] = 'code-arrow';
		}else {
			$coupon_code_attr['code_arrow'] = '';
		}
		if($actionType=='popup' && !empty($tabReverse)) {
			$coupon_code_attr['extlink'] = esc_url($redirectLink);
		}

		if($standardStyle=='style-1' || $standardStyle=='style-2' || $standardStyle=='style-3'){
			$cpnTextCss = 'tpgb-abs-flex';
		}

		if(!empty($onScrollBar)){
			$scrollClass = 'tpgb-code-scroll';
		}
	}else if($couponType=='scratch') {
		$coupon_code_attr['fillPercent'] = $fillPercent;
	}
	$slide_out_class = '';
	if($couponType=='slideOut') {
		$coupon_code_attr['slideDirection'] = $slideDirection;
		$slide_out_class = ' slide-out-'.esc_attr($slideDirection);
	}

	$bfcss = $afcss = '';
	if($couponType=='standard'){
		if($standardStyle=='style-1'){
			$bfcss = 'tpgb-trans-ease-before';
			$afcss = 'tpgb-trans-ease-after';
		}else if($standardStyle=='style-2'){
			$afcss = 'tpgb-trans-ease-after';
		}else if($standardStyle=='style-5'){
			$bfcss = 'tpgb-trans-easeinout-before';
			$afcss = 'tpgb-trans-easeinout-after';
		}
	}
	
	$coupon_code_attr = htmlspecialchars(json_encode($coupon_code_attr), ENT_QUOTES, 'UTF-8');

	$output = '';
    $output .= '<div id="tpgb-block-'.esc_attr($uid_ccd).'" class="tpgb-coupon-code tpgb-relative-block action-'.esc_attr($actionType).' coupon-code-'.esc_attr($couponType).''.esc_attr($slide_out_class).' tpgb-block-'.esc_attr($block_id).' tpgb-block-'.esc_attr($uid_ccd).' '.esc_attr($blockClass).''.esc_attr($tabReverseClass).'" data-tpgb_cc_settings=\'' .$coupon_code_attr. '\' data-save-cookies="'.esc_attr($saveCookie).'">';
		if($couponType=='standard') {
			 $output .= '<div class="coupon-code-inner '.esc_attr($standardStyle).' '.esc_attr($bfcss).' '.esc_attr($afcss).'">';
				$data = [];
				if($actionType=='click' && !empty($hideLink) && !empty($linkMaskText)){
					foreach($maskLinkList as $item) {
						$hideLinks = !empty($item["linkUrl"]["url"]) ? $item["linkUrl"]["url"] : '';
						$data[]= $hideLinks;
					}
					if(!empty($redirectLink)){
						$data[]= $redirectLink;		
					}
					$data = json_encode($data);
					$output .= '<a class="coupon-btn-link tpgb-hl-links '.esc_attr($tnseasea).' '.esc_attr($bfcss).' '.esc_attr($afcss).'" href="'.esc_attr($linkMaskText).'" data-hlset=\''.$data .'\' '.$link_attr.'>';
				}else{
					$output .= '<a class="coupon-btn-link '.esc_attr($tnsease).' '.esc_attr($bfcss).' '.esc_attr($afcss).'" '.$clickAction.' '.$link_attr.'>';
				}
					if($standardStyle=='style-4' || $standardStyle=='style-5') {
						$output .= '<span class="coupon-icon '.esc_attr($tnsease).'">';
							$output .= '<svg class="tpgb-scissors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path class="'.esc_attr($tnsease).'" d="M396.8 51.2C425.1 22.92 470.9 22.92 499.2 51.2C506.3 58.27 506.3 69.73 499.2 76.8L216.5 359.5C221.3 372.1 224 385.7 224 400C224 461.9 173.9 512 112 512C50.14 512 0 461.9 0 400C0 338.1 50.14 287.1 112 287.1C126.3 287.1 139.9 290.7 152.5 295.5L191.1 255.1L152.5 216.5C139.9 221.3 126.3 224 112 224C50.14 224 0 173.9 0 112C0 50.14 50.14 0 112 0C173.9 0 224 50.14 224 112C224 126.3 221.3 139.9 216.5 152.5L255.1 191.1L396.8 51.2zM160 111.1C160 85.49 138.5 63.1 112 63.1C85.49 63.1 64 85.49 64 111.1C64 138.5 85.49 159.1 112 159.1C138.5 159.1 160 138.5 160 111.1zM112 448C138.5 448 160 426.5 160 400C160 373.5 138.5 352 112 352C85.49 352 64 373.5 64 400C64 426.5 85.49 448 112 448zM278.6 342.6L342.6 278.6L499.2 435.2C506.3 442.3 506.3 453.7 499.2 460.8C470.9 489.1 425.1 489.1 396.8 460.8L278.6 342.6z"/></svg>';
						$output .= '</span>';
					}
					$output .= '<div class="coupon-text '.esc_attr($cpnTextCss).' '.esc_attr($tnsease).'">'.wp_kses_post($couponText).'</div>';
					if($standardStyle!='style-4' && $standardStyle!='style-5') {
						$output .= '<div class="coupon-code">'.wp_kses_post($couponCode).'</div>';
					}
				$output .= '</a>';
				if($actionType=='popup') {
					$output .= '<div class="copy-code-wrappar" role="dialog"></div>';
					$output .= '<div class="ccd-main-modal '.esc_attr($scrollClass).'" role="alert">';
						$output .= '<button class="tpgb-ccd-closebtn '.esc_attr($tnsease).'" role="button"><i class="fas fa-times"></i></button>';
						$output .= '<div class="popup-code-modal">';
							$output .= '<div class="popup-content">';
								$output .= '<div class="content-title">'.wp_kses_post($popupTitle).'</div>';
								$output .= '<div class="content-desc">'.wp_kses_post($popupDesc).'</div>';
							$output .= '</div>';
							
							$output .= '<div class="coupon-code-outer">';
								$output .= '<span class="full-code-text '.esc_attr($codeArrowclass).' '.esc_attr($tnsease).'">'.wp_kses_post($couponCode).'</span>';
								$output .= '<button class="copy-code-btn '.esc_attr($tnsease).'">'.wp_kses_post($copyBtnText).'</button>';
							$output .= '</div>';
							if(!empty($visitBtnText)){
								$output .= '<div class="coupon-store-visit">';
									$output .= '<a class="store-visit-link '.esc_attr($tnsease).'" '.$visitLink.' '.$link_attr.'>'.wp_kses_post($visitBtnText).'</a>';
								$output .= '</div>';
							}
						$output .= '</div>';
					$output .= '</div>';
				}
			 $output .= '</div>';
		}else if($couponType!='standard') {
			$output .= '<div class="coupon-front-side" id="front-side-'.esc_attr($uid_ccd).'">';
				$output .= '<div class="coupon-front-inner">';
					if($couponType=='slideOut' && !empty($directionHint)){
						$output .= '<div class="tpgb-anim-pos-cont '.esc_attr($slide_out_class).'">					
							<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
							 width="67.000000pt" height="34.000000pt" viewBox="0 0 67.000000 34.000000"
							 preserveAspectRatio="xMidYMid meet"><g transform="translate(0.000000,34.000000) scale(0.100000,-0.100000)"
							fill="currentcolor" stroke="none"><path d="M300 250 c0 -64 -9 -86 -25 -60 -3 6 -13 10 -21 10 -29 0 -25 -30 9
							-72 19 -24 38 -53 41 -66 7 -20 14 -22 69 -22 l62 0 14 53 c23 88 24 112 7
							126 -12 10 -16 11 -16 1 0 -10 -3 -10 -15 0 -8 6 -19 9 -24 6 -5 -4 -13 -2
							-16 4 -4 6 -15 8 -26 5 -17 -6 -19 -1 -19 39 0 39 -3 46 -20 46 -18 0 -20 -7
							-20 -70z"/><path d="M91 156 l-32 -24 37 -23 c26 -16 39 -19 42 -11 2 7 17 12 33 12 24 0
							29 4 29 25 0 21 -5 25 -30 25 -16 0 -30 5 -30 10 0 16 -14 12 -49 -14z"/>
							<path d="M590 170 c0 -5 -16 -10 -35 -10 -31 0 -35 -3 -35 -25 0 -22 4 -25 34
							-25 19 0 36 -5 38 -12 3 -8 15 -4 37 11 l33 24 -29 23 c-31 25 -43 29 -43 14z"/>
							</g></svg>
						</div>';
					}
					if($frontContentType=='default' && !empty($frontContent)) {
						$output .= '<div class="coupon-inner-content">';
							$output .= '<h3 class="coupon-front-content">'.wp_kses_post($frontContent).'</h3>';
						$output .= '</div>';
					} else if($frontContentType=='template' && !empty($frontTemp) && $frontTemp!='none' ){
						ob_start();
							if(!empty($frontTemp)) {
								echo Tpgb_Library()->plus_do_block($frontTemp);
							}
						$output .= ob_get_contents();
						ob_end_clean();
					}
				$output .= '</div>';
				$output .= '<div class="coupon-code-overlay"></div>';
			$output .= '</div>';
			
			$output .= '<div class="coupon-back-side">';
				$output .= '<div class="coupon-back-inner">';
					if($backContentType=='default') {
						$output .= '<div class="coupon-back-content">';
						if(!empty($backTitle)) {
							$output .= '<h3 class="coupon-back-title">'.wp_kses_post($backTitle).'</h3>';
						}
						if(!empty($backDesc)) {
							$output .= '<p class="coupon-back-description">'.wp_kses_post($backDesc).'</p>';
						}
						$output .= '</div>';
					} else if($backContentType=='template' && !empty($backTemp) && $backTemp!='none' ){
						ob_start();
							if(!empty($backTemp)) {
								echo Tpgb_Library()->plus_do_block($backTemp);
							}
						$output .= ob_get_contents();
						ob_end_clean();
					}
				$output .= '</div>';
				$output .= '<div class="coupon-code-overlay"></div>';
			$output .= '</div>';
		}
    $output .= '</div>';
	if($couponType=='standard' && $actionType=='popup' && !empty($ovBackFilt)){
		$output .= '<style>.tpgb-block-'.esc_attr($block_id).' .copy-code-wrappar::after{ backdrop-filter: grayscale('.esc_js($backGscale).')  blur('.esc_js($backBlur).'px); }</style>';
	}
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_coupon_code() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'couponType' => [
			'type' => 'string',
			'default' => 'standard',	
		],
		'standardStyle' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'directionHint' => [
			'type'=> 'boolean',
			'default'=> false,	
		],
		'couponText' => [
			'type' => 'string',
			'default' => 'Show Code',	
		],
		'redirectLink' => [
			'type'=> 'object',
			'default'=> [
				'url' => '',	
				'target' => true,
				'nofollow' => true
			],
		],
		
		'codeArrow' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'couponCode' => [
			'type' => 'string',
			'default' => 'PO-SI-MY-TH-007',	
		],
		'actionType' => [
			'type' => 'string',
			'default' => 'click',	
		],
		'popupTitle' => [
			'type' => 'string',
			'default' => 'Here is your coupon code',	
		],
		'popupDesc' => [
			'type' => 'string',
			'default' => 'Use code on site',	
		],
		'copyBtnText' => [
			'type' => 'string',
			'default' => 'Copy Code',	
		],
		'afterCopyText' => [
			'type' => 'string',
			'default' => 'Copied!',	
		],
		'visitBtnText' => [
			'type' => 'string',
			'default' => 'Visit Site',	
		],
		
		'standardConAlign' => [
			'type' => 'object',
			'default' => 'center',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardConAlign', 'relation' => '==', 'value' => 'left' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-3 .coupon-text { justify-content: flex-start; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardConAlign', 'relation' => '==', 'value' => 'center' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-3 .coupon-text { justify-content: center; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardConAlign', 'relation' => '==', 'value' => 'right' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text, {{PLUS_WRAP}} .coupon-code-inner.style-3 .coupon-text { justify-content: flex-end; }',
				],
				(object) [
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link, {{PLUS_WRAP}} .coupon-btn-link .coupon-text{ text-align: {{standardConAlign}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'actionType', 'relation' => '==', 'value' => 'click' ]],
					'selector' => '{{PLUS_WRAP}} .full-code-text{ text-align: {{standardConAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'standardAlign' => [
			'type' => 'object',
			'default' => 'center',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-coupon-code { text-align: {{standardAlign}}; }',
				],
			],
			'scopy' => true,
		],
		/* Standard Extra Start */
		'saveCookie' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'hideLink' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'linkMaskText' => [
			'type' => 'string',
			'default' => '',	
		],
		'maskLinkList' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'label' => [
						'type' => 'string',
						'default' => 'Label'
					],
					'linkUrl' => [
						'type'=> 'object',
						'default'=>[
							'url' => '#',	
							'target' => '',	
							'nofollow' => ''	
						]
					],
				],
			],
			'default' => [
				[
					"_key" => '0',
					"label" => 'Wordpress',
					"linkUrl" => [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
				],
			],
		],
		'tabReverse' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		
		/* Standard Extra End */
		/**** Peel/Scratch/SlideOut Start ****/
		'fillPercent' => [
			'type' => 'string',
			'default' => '70',
		],
		'scratchWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => ['scratch','slideOut','peel'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-scratch, {{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-slideOut, {{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-peel{ max-width: {{scratchWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'scratchHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => ['scratch','slideOut','peel'] ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-scratch, {{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-slideOut, {{PLUS_WRAP}}.tpgb-coupon-code.coupon-code-peel{ height: {{scratchHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'slideDirection' => [
			'type' => 'string',
			'default' => 'left',
		],
		
		'frontContentType' => [
			'type' => 'string',
			'default' => 'default',
		],
		'frontContent' => [
			'type' => 'string',
			'default' => 'Front Content',
		],
		'frontTemp' => [
			'type' => 'string',
			'default' => ''
		],
		'backendVisi' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'backContentType' => [
			'type' => 'string',
			'default' => 'default',
		],
		'backTitle' => [
			'type' => 'string',
			'default' => 'Back Title',
		],
		'backDesc' => [
			'type' => 'string',
			'default' => 'Back Description',
		],
		'backTemp' => [
			'type' => 'string',
			'default' => ''
		],
		'backtempVisi' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		
		'contentBorder' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-front-side, {{PLUS_WRAP}} .coupon-back-side',
				],
			],
			'scopy' => true,
		],
		'contentBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-front-side, {{PLUS_WRAP}} .coupon-back-side{border-radius: {{contentBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'frontTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'frontContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'frontContent', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-front-content',
				],
			],
			'scopy' => true,
		],
		'frontColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'frontContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'frontContent', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-front-content{ color: {{frontColor}}; }',
				],
			],
			'scopy' => true,
		],
		'frontBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-front-side',
				],
			],
			'scopy' => true,
		],
		
		'backTitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'backContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'backTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-back-title',
				],
			],
			'scopy' => true,
		],
		'backTitleColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'backContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'backTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-back-title{ color: {{backTitleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'backDescTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'backContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'backDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-back-description',
				],
			],
			'scopy' => true,
		],
		'backDescColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ],['key' => 'backContentType', 'relation' => '==', 'value' => 'default' ],['key' => 'backDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} h3.coupon-back-description{ color: {{backDescColor}}; }',
				],
			],
			'scopy' => true,
		],
		'backBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '!=', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-back-side',
				],
			],
			'scopy' => true,
		],
		/**** Peel/Scratch/SlideOut End ****/
		
		/**** Standard Start****/
		'buttonTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner .coupon-text',
				],
			],
			'scopy' => true,
		],
		'btnWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => '%',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner{ width: {{btnWidth}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], ['key' => 'actionType', 'relation' => '==', 'value' => 'click' ]],
					'selector' => '{{PLUS_WRAP}}.action-click .full-code-text{ width: {{btnWidth}}; }',
				],
			],
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link, {{PLUS_WRAP}} .coupon-text{padding: {{btnPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link .coupon-text {padding: {{btnPadding}};}',
				],
			],
			'scopy' => true,
		],
		'arrowNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1::before{ border-left-color: {{arrowNColor}}; } {{PLUS_WRAP}} .coupon-code-inner.style-1::after{ border-right-color: {{arrowNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'arrowHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1:hover::before{ border-left-color: {{arrowHColor}}; } {{PLUS_WRAP}} .coupon-code-inner.style-1:hover::after{ border-right-color: {{arrowHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'buttonNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner .coupon-text{ color: {{buttonNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'buttonHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner .coupon-btn-link:hover .coupon-text{ color: {{buttonHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btns2NmlBG' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text{ background: {{btns2NmlBG}}; } {{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text::after{ border-left-color: {{btns2NmlBG}}; }',
				],
			],
			'scopy' => true,
		],
		'btns2Nbdr' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-2 .coupon-text::after{ border-left-color: {{btns2Nbdr}}; }',
				],
			],
			'scopy' => true,
		],
		'btns2HvrBG' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .style-2 .coupon-btn-link:hover .coupon-text{ background: {{btns2HvrBG}}; } {{PLUS_WRAP}} .style-2 .coupon-btn-link:hover .coupon-text::after{ border-left-color: {{btns2HvrBG}}; }',
				],
			],
			'scopy' => true,
		],
		'btns2Hbdr' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-2' ]],
					'selector' => '{{PLUS_WRAP}} .style-2 .coupon-btn-link:hover .coupon-text::after{ border-left-color: {{btns2Hbdr}}; }',
				],
			],
			'scopy' => true,
		],
		'btnScratchNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-5' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link::after{ border-bottom-color: {{btnScratchNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnScratchHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-5' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link:hover::after{ border-bottom-color: {{btnScratchHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'buttonNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-1 .coupon-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-3 .coupon-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link',
				],
			],
			'scopy' => true,
		],
		'buttonHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-1' ]],
					'selector' => '{{PLUS_WRAP}} .style-1 .coupon-btn-link:hover .coupon-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => 'style-3' ]],
					'selector' => '{{PLUS_WRAP}} .style-3 .coupon-btn-link:hover .coupon-text',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link:hover, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link:hover',
				],
			],
			'scopy' => true,
		],
		'btnNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '!=', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link',
				],
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link',
				],
			],
			'scopy' => true,
		],
		'btnHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '!=', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link:hover',
				],
				
				(object) [
					'condition' => [(object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link:hover, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link:hover',
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], (object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-1','style-3'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link, {{PLUS_WRAP}} .coupon-btn-link .coupon-text{border-radius: {{btnNmlBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], (object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link{border-radius: {{btnNmlBRadius}};}',
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], (object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-1','style-3'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link:hover, {{PLUS_WRAP}} .coupon-btn-link:hover .coupon-text{border-radius: {{btnHvrBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ], (object) ['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link:hover{border-radius: {{btnHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'btnNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link',
				],
			],
			'scopy' => true,
		],
		'btnHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-btn-link:hover',
				],
			],
			'scopy' => true,
		],
		
		/* Only Standard style-4 && style-5 start ****/
		'btnIconWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-icon, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-icon{ width: {{btnIconWidth}}; height: {{btnIconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-icon .tpgb-scissors, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-icon .tpgb-scissors{ width: {{btnIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-icon .tpgb-scissors > path, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-icon .tpgb-scissors > path{ fill: {{btnIconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon .tpgb-scissors > path, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon .tpgb-scissors > path{ fill: {{btnIconHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-icon, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-icon',
				],
			],
			'scopy' => true,
		],
		'btnIconHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'standardStyle', 'relation' => '==', 'value' => ['style-4','style-5'] ]],
					'selector' => '{{PLUS_WRAP}} .coupon-code-inner.style-4 .coupon-btn-link:hover .coupon-icon, {{PLUS_WRAP}} .coupon-code-inner.style-5 .coupon-btn-link:hover .coupon-icon',
				],
			],
			'scopy' => true,
		],
		/* Only Standard style-4 && style-5 end ****/
		
		//Copy Code Style Start
		'cCodeTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text',
				],
			],
			'scopy' => true,
		],
		'cCodePadding' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text{ padding: {{cCodePadding}}; }',
				],
			],
			'scopy' => true,
		],
		'cCodeNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text{ color: {{cCodeNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cCodeHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text:hover{ color: {{cCodeHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cCArrowNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .full-code-text.code-arrow::before{ border-left-color: {{cCArrowNColor}}; } {{PLUS_WRAP}} .full-code-text.code-arrow::after{ border-right-color: {{cCArrowNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cCArrowHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} .full-code-text.code-arrow:hover::before{ border-left-color: {{cCArrowHColor}}; } {{PLUS_WRAP}} .full-code-text.code-arrow:hover::after{ border-right-color: {{cCArrowHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cCodeNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text',
				],
			],
			'scopy' => true,
		],
		'cCodeHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text:hover',
				],
			],
			'scopy' => true,
		],
		'cCodeNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text',
				],
			],
			'scopy' => true,
		],
		'cCodeHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text:hover',
				],
			],
			'scopy' => true,
		],
		'cCodeNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text{border-radius: {{cCodeNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cCodeHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text:hover{border-radius: {{cCodeHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cCodeNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text',
				],
			],
			'scopy' => true,
		],
		'cCodeHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ]],
					'selector' => '{{PLUS_WRAP}} span.full-code-text:hover',
				],
			],
			'scopy' => true,
		],
		//Copy Code Style End
		
		//Close Icon Style Start
		'modalWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal{ max-width: {{modalWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'modalHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal{ max-height: {{modalHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'modalPadding' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal{ padding: {{modalPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'modalNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal',
				],
			],
			'scopy' => true,
		],
		'modalHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal:hover',
				],
			],
			'scopy' => true,
		],
		'modalNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal',
				],
			],
			'scopy' => true,
		],
		'modalHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal:hover',
				],
			],
			'scopy' => true,
		],
		'modalNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal{border-radius: {{modalNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'modalHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal:hover{border-radius: {{modalHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'modalNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal',
				],
			],
			'scopy' => true,
		],
		'modalHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal:hover',
				],
			],
			'scopy' => true,
		],
		
		'onScrollBar' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'scrollBarWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '10',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar{ width: {{scrollBarWidth}}; }',
				],
			],
			'scopy' => true,
		],
		
		'thumbBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 1,
				'bgType' => 'color',
				'bgDefaultColor' => '#ff844a',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'thumbRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '10',
					"right" => '10',
					"bottom" => '10',
					"left" => '10',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-thumb{border-radius: {{thumbRadius}};}',
				],
			],
			'scopy' => true,
		],
		'thumbShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-thumb',
				],
			],
			'scopy' => true,
		],
		'trackBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 1,
				'bgType' => 'color',
				'bgDefaultColor' => '#6f1ef150',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		'trackRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '10',
					"right" => '10',
					"bottom" => '10',
					"left" => '10',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-track{border-radius: {{trackRadius}};}',
				],
			],
			'scopy' => true,
		],
		'trackShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ], ['key' => 'onScrollBar', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}} .ccd-main-modal::-webkit-scrollbar-track',
				],
			],
			'scopy' => true,
		],
		
		'cIconWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn{ width: {{cIconWidth}}; height: {{cIconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconSize' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn{ font-size: {{cIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn{ color: {{cIconNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn:hover{ color: {{cIconHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'cIconNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn',
				],
			],
			'scopy' => true,
		],
		'cIconHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn:hover',
				],
			],
			'scopy' => true,
		],
		'cIconNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn',
				],
			],
			'scopy' => true,
		],
		'cIconHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn:hover',
				],
			],
			'scopy' => true,
		],
		'cIconNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn{border-radius: {{cIconNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cIconHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn:hover{border-radius: {{cIconHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'cIconNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn',
				],
			],
			'scopy' => true,
		],
		'cIconHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-ccd-closebtn:hover',
				],
			],
			'scopy' => true,
		],
		//Close Icon Style End
		
		//Popup Title Style Start
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title',
				],
			],
			'scopy' => true,
		],
		'titlePadd' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title{ padding: {{titlePadd}}; }',
				],
			],
			'scopy' => true,
		],
		'titleNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title{ color: {{titleNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title:hover{ color: {{titleHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title',
				],
			],
			'scopy' => true,
		],
		'titleHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title:hover',
				],
			],
			'scopy' => true,
		],
		'titleNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title',
				],
			],
			'scopy' => true,
		],
		'titleHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title:hover',
				],
			],
			'scopy' => true,
		],
		'titleNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title{border-radius: {{titleNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'titleHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupTitle', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-title:hover{border-radius: {{titleHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		//Popup Title Style End
		
		//Popup Desc Style Start
		'descTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc',
				],
			],
			'scopy' => true,
		],
		'descPadd' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc{ padding: {{descPadd}}; }',
				],
			],
			'scopy' => true,
		],
		'descMar' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc{ margin: {{descMar}}; }',
				],
			],
			'scopy' => true,
		],
		'copyBtnNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn{ color: {{copyBtnNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc{ color: {{descNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc:hover{ color: {{descHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc',
				],
			],
			'scopy' => true,
		],
		'descHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc:hover',
				],
			],
			'scopy' => true,
		],
		'descNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc',
				],
			],
			'scopy' => true,
		],
		'descHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc:hover',
				],
			],
			'scopy' => true,
		],
		'descNmlBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc{border-radius: {{descNmlBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'descHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'popupDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .popup-content .content-desc:hover{border-radius: {{descHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		//Popup Desc Style End
		
		//Copy Button Style Start
		'copyBtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn',
				],
			],
			'scopy' => true,
		],
		'copyBtnPadd' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .popup-code-modal .copy-code-btn{ padding: {{copyBtnPadd}}; }',
				],
			],
			'scopy' => true,
		],
		'copyBtnMar' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .popup-code-modal .copy-code-btn{ margin: {{copyBtnMar}}; }',
				],
			],
			'scopy' => true,
		],
		'copyBtnNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn{ color: {{copyBtnNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyBtnHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn:hover{ color: {{copyBtnHColor}}; }',
				],
			],
			'scopy' => true,
		],
		'copyBtnNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn',
				],
			],
			'scopy' => true,
		],
		'copyBtnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn:hover',
				],
			],
			'scopy' => true,
		],
		'copyBtnNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn',
				],
			],
			'scopy' => true,
		],
		'copyBtnHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn:hover',
				],
			],
			'scopy' => true,
		],
		'copyBtnNBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn{border-radius: {{copyBtnNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'copyBtnHBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn:hover{border-radius: {{copyBtnHBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'copyBtnNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn',
				],
			],
			'scopy' => true,
		],
		'copyBtnHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'copyBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-btn:hover',
				],
			],
			'scopy' => true,
		],
		//Copy Button Style End
		
		//Visit Button Style Start
		'visitBtnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link',
				],
			],
			'scopy' => true,
		],
		'visitBtnPadd' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link{ padding: {{visitBtnPadd}}; }',
				],
			],
			'scopy' => true,
		],
		'visitBtnMar' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link{ margin: {{visitBtnMar}}; }',
				],
			],
			'scopy' => true,
		],
		'visitBtnNColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link{ color: {{visitBtnNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'visitBtnHColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link:hover{ color: {{visitBtnHColor}}; }',
				],
			],
		],
		'visitBtnNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link',
				],
			],
			'scopy' => true,
		],
		'visitBtnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link:hover',
				],
			],
			'scopy' => true,
		],
		'visitBtnNmlBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link',
				],
			],
			'scopy' => true,
		],
		'visitBtnHvrBdr' => [
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
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link:hover',
				],
			],
			'scopy' => true,
		],
		'visitBtnNBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link{border-radius: {{visitBtnNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'visitBtnHBRadius' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link:hover{border-radius: {{visitBtnHBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'visitBtnNBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link',
				],
			],
			'scopy' => true,
		],
		'visitBtnHBShadow' => [
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
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ],['key' => 'visitBtnText', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .coupon-store-visit .store-visit-link:hover',
				],
			],
			'scopy' => true,
		],
		//Visit Button Style End
		'mdlOvColor' => [
			'type' => 'string',
			'default' => '',	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'couponType', 'relation' => '==', 'value' => 'standard' ],['key' => 'actionType', 'relation' => '==', 'value' => 'popup' ]],
					'selector' => '{{PLUS_WRAP}} .copy-code-wrappar::after{ background-color: {{mdlOvColor}}; }',
				],
			],
		],
		'ovBackFilt' => [
			'type'=> 'boolean',
			'default'=> false,
		],
		'backBlur' => [
			'type'=> 'string',
			'default'=> '',
		],
		'backGscale' => [
			'type'=> 'string',
			'default'=> '',
		],
		/**** Standard End****/
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-coupon-code', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_coupon_code_render_callback'
    ) );
}
add_action( 'init', 'tpgb_coupon_code' );