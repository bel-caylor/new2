<?php
/* Block : Team Member Listing
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_team_member_listing_render_callback( $attributes, $content) {
	$TeamMember = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$style = (!empty($attributes['Style'])) ? $attributes['Style'] : 'style-1';
	$layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
	$DisbleLink = (!empty($attributes['DisLink'])) ? $attributes['DisLink'] : false;
	$TeamMemberR = (!empty($attributes['TeamMemberR'])) ? $attributes['TeamMemberR'] : [];
	$columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'md';
	$TitleTag = (!empty($attributes['TitleTag'])) ? $attributes['TitleTag'] : 'h3';
	$Designation = (!empty($attributes['DesignDis'])) ? $attributes['DesignDis'] : false;
	$DisableIcon = (!empty($attributes['SocialIcon'])) ? $attributes['SocialIcon'] : false;
	$DisableISize = (!empty($attributes['DImgS'])) ? $attributes['DImgS'] : false;
	$FImageTp = (!empty($attributes['FImageTp'])) ? $attributes['FImageTp'] : 'full';
	$ImageSize = (!empty($attributes['ImgSize'])) ? $attributes['ImgSize'] : 'full';
	$CategoryWF = (!empty($attributes['CategoryWF'])) ? $attributes['CategoryWF'] : '';
	$Categoryclass = (!empty($CategoryWF) ? 'tpgb-category-filter' : '' );
	$MaskImg = (isset($attributes['MaskImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['MaskImg']) : (!empty($attributes['MaskImg']['url']) ? $attributes['MaskImg']['url'] : '');
	$ExLImg = (isset($attributes['ExLImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['ExLImg']) : (!empty($attributes['ExLImg']['url']) ? $attributes['ExLImg']['url'] : '');
	$AnimationIMG = (!empty($attributes['AExlImg'])) ? $attributes['AExlImg'] : 'none';
	$AniToggle = (!empty($attributes['HAnimation'])) ? $attributes['HAnimation'] : false;

	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	
	$Default_Img = TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$list_layout='';
	if( $layout=='grid' || $layout=='masonry' ){
		$list_layout = 'tpgb-isotope';
	}else if( $layout =='metro' ){
		$list_layout = 'tpgb-metro';
	}else if( $layout =='carousel' ){
		$list_layout = 'tpgb-carousel splide';	
	}else{
		$list_layout = 'tpgb-isotope';
	}

	$desktop_class = '';
	if( $layout !='carousel' && $layout !='metro' && $columns ){
		$desktop_class .= 'tpgb-col-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$desktop_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$desktop_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}

	$Sliderclass = '';
	$carousel_settings = '';
	if($layout=='carousel'){
		if($slideHoverDots==true && ( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
			$Sliderclass .= ' hover-slider-dots';
		}
		if($outerArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$Sliderclass .= ' outer-slider-arrow';
		}
		if($slideHoverArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$Sliderclass .= ' hover-slider-arrow';
		}
		if( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) ) ){
			$Sliderclass .= ' dots-'.esc_attr($dotsStyle);
		}
		
		$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );
	}

	$cssMasking="";
	if($style == 'style-4'){
		$uid = '.tpgb-block-'.esc_attr($block_id);
		if(!empty($MaskImg)){
			$cssMasking .= esc_attr($uid).'.tpgb-team-member-list.team-style-4 .team-list-content .tpgb-team-profile span.thumb-wrap{mask-image:url('.esc_url($MaskImg).'});-webkit-mask-image:url('.esc_url($MaskImg).');}';
		}
		if(!empty($ExLImg)){			
			$cssMasking .= esc_attr($uid).'.tpgb-team-member-list.team-style-4 .bg-image-layered{background-image:url('.esc_url($ExLImg).');}';
		}
	}
	
	$TeamMember .= (!empty($cssMasking) ? '<style>'.esc_attr($cssMasking).'</style>' :'');
	$TeamMember .= '<div id="'.esc_attr($block_id).'" class="tpgb-block-'.esc_attr($block_id).' tpgb-relative-block  tpgb-team-member-list team-'.esc_attr($style).' '.esc_attr($list_layout).' '.esc_attr($Categoryclass).' '.esc_attr($Sliderclass).' '.esc_attr($blockClass).' " data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-splide=\''.json_encode($carousel_settings).'\'>';
		if(!empty($CategoryWF) && $layout != 'carousel'){
			$TeamMember .= TMCategoryFilter($attributes);
		}
		if( $layout == 'carousel' && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$TeamMember .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
		}
		$TeamMember .= '<div class="post-loop-inner '.($layout=='carousel' ? 'splide__track' : ' tpgb-row').'">';
			if($layout=='carousel'){
				$TeamMember .= '<div class="splide__list">';
			}
			if( !empty($TeamMemberR) ){
				foreach ( $TeamMemberR as $index => $TeamItem ) {
					$TeamName = ( !empty($TeamItem['TName']) ) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($TeamItem['TName']) : '';
					$TeamDesignation = ( !empty($TeamItem['TDesig']) ) ? $TeamItem['TDesig'] : '';
					$ImgId = ( !empty($TeamItem['TImage']) ) ? $TeamItem['TImage'] : '';
					$TeamCUrl = (isset($TeamItem['CusUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['CusUrl']) : (!empty($TeamItem['CusUrl']['url']) ? $TeamItem['CusUrl']['url'] : '');
					$TeamWsUrl = (isset($TeamItem['WsUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['WsUrl']) : (!empty($TeamItem['WsUrl']['url']) ? $TeamItem['WsUrl']['url'] : '');
					$TeamFbUrl = (isset($TeamItem['FbUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['FbUrl']) : (!empty($TeamItem['FbUrl']['url']) ? $TeamItem['FbUrl']['url'] : '');
					$TeamMailUrl = (isset($TeamItem['MailUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['MailUrl']) : (!empty($TeamItem['MailUrl']['url']) ? $TeamItem['MailUrl']['url'] : '');
					$TeamIGUrl = (isset($TeamItem['IGUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['IGUrl']) : (!empty($TeamItem['IGUrl']['url']) ? $TeamItem['IGUrl']['url'] : '');
					$TeamTwUrl = (isset($TeamItem['TwUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['TwUrl']) : (!empty($TeamItem['TwUrl']['url']) ? $TeamItem['TwUrl']['url'] : '');
					$TeamldUrl = (isset($TeamItem['ldUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($TeamItem['ldUrl']) : (!empty($TeamItem['ldUrl']['url']) ? $TeamItem['ldUrl']['url'] : '');
					$TeamCategory = ( !empty($TeamItem['TCateg']) ) ? $TeamItem['TCateg'] : '';
					$CustomTarget = ( !empty($TeamItem['CusUrl']) && !empty($TeamItem['CusUrl']['target']) ) ? "_blank" : '';
					$CustomRel = ( !empty($TeamItem['CusUrl']) && !empty($TeamItem['CusUrl']['nofollow']) ) ? "nofollow" : '';
					$Telephone = ( !empty($TeamItem['TelNum']) ) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($TeamItem['TelNum']) : '';

					$category_filter=$loop_category='';						
					if( !empty($CategoryWF) && !empty($TeamCategory)  && $layout != 'carousel' ){
						$loop_category = explode(',', $TeamCategory);
						foreach( $loop_category as $category ) {
							$category = TM_Media_createSlug($category);
							$category_filter .= $category;
						}
					}

					// Set Default Image Url
					if(empty($ImgId)){
						$ImgId['url'] = $Default_Img;
					}

					$TeamMember .= '<div class="grid-item '.($layout =='carousel' ? 'splide__slide' : esc_attr($desktop_class)).' '.esc_attr($category_filter).'">';							
						$TeamMember .= '<div class="team-list-content tpgb-trans-linear">';
						
								$ImageHTML = $TeamImage = $AttImg = '';
								if(!empty($TeamCUrl) || !empty($ImgId)){
								
									if(!empty($ImgId)){
										$linkImage = '';
										if( $layout !='carousel' && !empty($DisableISize) ){
										
											if(!empty($ImgId['id'])){
												$AttImg .= wp_get_attachment_image($ImgId['id'] , $ImageSize, false);
											}else if(!empty($ImgId['url'])){
												$imgUrl = (isset($ImgId['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgId) : (!empty($ImgId['url']) ? $ImgId['url'] : '');
												$AttImg .= '<img src="'.esc_url($imgUrl).'"  alt="'.esc_attr($TeamName).'"  />';
											}else{
												$AttImg .= '<img src="'.esc_url($Default_Img).'"  alt="'.esc_attr($TeamName).'"  />';
											}
											$TeamImage .= $AttImg;
										}else{
											if( $FImageTp != 'custom' ){
												$ImageSize = $FImageTp;
											}
											if(!empty($ImgId['id'])){
												
												$AttImg .= wp_get_attachment_image($ImgId['id'] , 'full' , false);
											}else if(!empty($ImgId['url'])){
												$imgUrl = (isset($ImgId['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgId) : (!empty($ImgId['url']) ? $ImgId['url'] : '');
												$AttImg .= '<img src="'.esc_url($imgUrl).'"  alt="'.esc_attr($TeamName).'"/>';
											}else{
												$AttImg .= '<img src="'.esc_url($Default_Img).'"  alt="'.esc_attr($TeamName).'" />';
											}
											$TeamImage .= $AttImg;
										}

										$linkImage .= '<div class="tpgb-team-profile">';
											$linkImage .= '<span class="thumb-wrap">'.$TeamImage.'</span>';
										$linkImage .= '</div>';

										if(!empty($DisbleLink)){
											$ImageHTML .= $linkImage;
										}else{
											$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['CusUrl']);
											$ImageHTML .= '<a href="'.esc_url($TeamCUrl).'" target="'.esc_attr($CustomTarget).'" rel="'.esc_attr($CustomRel).'" '.$link_attr.' aria-label="'.esc_attr($TeamName).'">'.$linkImage.'</a>';
										}
									}
								}

								$IconHTML = '';
								if( !empty($DisableIcon) ){
									$Nofollow=$Target="";

									$IconHTML .= '<div class="tpgb-team-social-content">';
										$IconHTML .= '<div class="tpgb-team-social-list">';
											if( !empty($TeamWsUrl) ){
												$wb_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['WsUrl']);
												$Target = ( !empty($TeamItem['WsUrl']) && !empty($TeamItem['WsUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['WsUrl']) && !empty($TeamItem['WsUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="tpgb-team-profile-link">';
													$IconHTML .= '<a href="'.esc_url($TeamWsUrl).'" '.$Target.' '.$Nofollow.' '.$wb_attr.'  aria-label="'.esc_attr__('Site Url','tpgbp').'"><i class="fas fa-globe" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($TeamFbUrl) ){
												$fb_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['FbUrl']);
												$Target = ( !empty($TeamItem['FbUrl']) && !empty($TeamItem['FbUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['FbUrl']) && !empty($TeamItem['FbUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="fb-link">';
													$IconHTML .= '<a href="'.esc_url($TeamFbUrl).'" '.$Target.' '.$Nofollow.' '.$fb_attr.' aria-label="'.esc_attr__('Facebook','tpgbp').'"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($TeamTwUrl) ){
												$tw_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['TwUrl']);
												$Target = ( !empty($TeamItem['TwUrl']) && !empty($TeamItem['TwUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['TwUrl']) && !empty($TeamItem['TwUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="twitter-link">';
													$IconHTML .= '<a href="'.esc_url($TeamTwUrl).'" '.$Target.' '.$Nofollow.' '.$tw_attr.' aria-label="'.esc_attr__('Twitter','tpgbp').'"><i class="fab fa-twitter" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($TeamIGUrl) ){
												$ig_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['IGUrl']);
												$Target = ( !empty($TeamItem['IGUrl']) && !empty($TeamItem['IGUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['IGUrl']) && !empty($TeamItem['IGUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="instagram-link">';
													$IconHTML .= '<a href="'.esc_url($TeamIGUrl).'" '.$Target.' '.$Nofollow.' '.$ig_attr.' aria-label="'.esc_attr__('Instagram','tpgbp').'"><i class="fab fa-instagram" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($TeamMailUrl) ){
												$ml_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['MailUrl']);
												$Target = ( !empty($TeamItem['MailUrl']) && !empty($TeamItem['MailUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['MailUrl']) && !empty($TeamItem['MailUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="mail-link">';
													$IconHTML .= '<a href="'.esc_url($TeamMailUrl).'" '.$Target.' '.$Nofollow.' '.$ml_attr.' aria-label="'.esc_attr__('Mail','tpgbp').'"><i class="fas fa-envelope-square"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($TeamldUrl) ){
												$ld_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['ldUrl']);
												$Target = ( !empty($TeamItem['ldUrl']) && !empty($TeamItem['ldUrl']['target']) ) ? 'target="_blank"' : "";
												$Nofollow = ( !empty($TeamItem['ldUrl']) && !empty($TeamItem['ldUrl']['nofollow']) ) ? 'rel="nofollow"' : "";
												$IconHTML .= '<div class="linkedin-link">';
													$IconHTML .= '<a href="'.esc_url($TeamldUrl).'" '.$Target.' '.$Nofollow.' '.$ld_attr.' aria-label="'.esc_attr__('LinkedIn','tpgbp').'"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
											if( !empty($Telephone) ){
												$IconHTML .= '<div class="Telephone-link">';
													$IconHTML .= '<a href="'.esc_url('tel:'.$Telephone).'" aria-label="'.esc_attr__('Phone No','tpgbp').'"><i class="fas fa-phone" aria-hidden="true"></i></a>';
												$IconHTML .= '</div>';
											}
										$IconHTML .= '</div>';
									$IconHTML .= '</div>';	
								}

							$TitleHTML = '';
							if(!empty($TeamName)){
								$TitleHTML .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($TitleTag).' class="tpgb-post-title">';
									if( !empty($DisbleLink) ){
										$TitleHTML .= wp_kses_post($TeamName);
									}else{
										$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($TeamItem['CusUrl']);
										$TitleHTML .= '<a href="'.esc_attr($TeamCUrl).'" target="'.esc_attr($CustomTarget).'" rel="'.esc_attr($CustomRel).'"  '.$link_attr.'>'.wp_kses_post($TeamName).'</a>';
									}
								$TitleHTML .= '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($TitleTag).'>';
							}

							$DesigHTML = '';
							if( !empty($TeamDesignation) && !empty($Designation) ){
								$DesigHTML .= '<div class="tpgb-member-designation">'.wp_kses_post($TeamDesignation).'</div>';
							}					

							$FinalHTML = '';
							if( $style == 'style-1' ){
								$FinalHTML .= '<div class="post-content-image">';
									$FinalHTML .= $ImageHTML;
									$FinalHTML .= $IconHTML;
								$FinalHTML .= '</div>';
								$FinalHTML .= '<div class="post-content-bottom">';
									$FinalHTML .= $TitleHTML;
									$FinalHTML .= $DesigHTML;
								$FinalHTML .= '</div>';
							}else if( $style == 'style-2' ){
								$FinalHTML .= '<div class="post-content-image">'.$ImageHTML.'</div>';
								$FinalHTML .= '<div class="post-content-bottom">';
									$FinalHTML .= $TitleHTML;
									$FinalHTML .= $DesigHTML;
									$FinalHTML .= $IconHTML;
								$FinalHTML .= '</div>';
							}else if( $style == 'style-3' ){
								$FinalHTML .= '<div class="post-content-image">'.$ImageHTML.'</div>';
								$FinalHTML .= '<div class="post-content-bottom">';
									$FinalHTML .= '<div class="content-table">';
										$FinalHTML .= '<div class="table-cell">';
											$FinalHTML .= $TitleHTML;
										$FinalHTML .= '</div>';
										$FinalHTML .= '<div class="table-cell">';
											$FinalHTML .= $IconHTML;
										$FinalHTML .= '</div>';
									$FinalHTML .= '</div>';
									$FinalHTML .= $DesigHTML;
								$FinalHTML .= '</div>';
							}else if( $style == 'style-4' ){
								$AnimClass = '';
								if($AnimationIMG == 'pulse'){
									$AnimClass = 'image-plus';
									if($AniToggle){
										$AnimClass = 'hover_pulse';
									}
								}else if($AnimationIMG == 'floating'){
									$AnimClass = 'image-floating';
									if($AniToggle){
										$AnimClass = 'hover_floating';
									}
								}else if($AnimationIMG == 'tossing'){
									$AnimClass = 'image-tossing';
									if($AniToggle){
										$AnimClass = 'hover_tossing';
									}
								}else if($AnimationIMG == 'rotating'){
									$AnimClass = 'image-rotating';
									if($AniToggle){
										$AnimClass = 'hover_rotating';
									}
								}
								$FinalHTML .= '<div class="post-content-image">';
									$FinalHTML .= '<div class="bg-image-layered '.esc_attr($AnimClass).'"></div>';
									$FinalHTML .= $ImageHTML;
								$FinalHTML .= '</div>';
								$FinalHTML .= '<div class="post-content-bottom">';
									$FinalHTML .= $TitleHTML;
									$FinalHTML .= $DesigHTML;
									$FinalHTML .= $IconHTML;
								$FinalHTML .= '</div>';
							}

							$TeamMember .= $FinalHTML;
						$TeamMember .= '</div>';
					$TeamMember .= '</div>';
				}
			}
			if($layout=='carousel'){
				$TeamMember .= '</div>';
			}
		$TeamMember .= "</div>";
	$TeamMember .= "</div>";
	
	$TeamMember = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $TeamMember);
	if($layout =='carousel'){
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		if( !empty($arrowCss) ){
			$TeamMember .= $arrowCss;
		}
	}
    return $TeamMember;
}

function tpgb_tp_team_member_listing() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$sliderOpt = [
		'slideColumns' => [
			'type' => 'object',
			'default' => [ 'md' => 4,'sm' => 3,'xs' => 2 ],
		],
	];
	$carousel_options = array_merge($carousel_options,$sliderOpt);
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'Style' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'layout' => [
				'type' => 'string',
				'default' => 'grid',	
			],
			'Alignment' => [
				'type' => 'object',
				'default' => [ 'md' => 'left', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-bottom{text-align:{{Alignment}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-bottom .tpgb-post-title,
						{{PLUS_WRAP}}.tpgb-team-member-list .post-content-bottom .tpgb-member-designation{text-align:{{Alignment}};}',
					],
				],
				'scopy' => true,
			],	

			'TeamMemberR' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'TName' => [
							'type'=> 'string',
							'default'=> 'Team Member',
						],
						'TImage' => [
							'type' => 'object',
							'default' => [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
								'Id' => '',
							],
						],						
						'TDesig' => [
							'type'=> 'string',
							'default'=> 'Manager',
						],
						'TCateg' => [
							'type'=> 'string',
							'default'=> '',
						],
						'CusUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'WsUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'FbUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'MailUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'IGUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'TwUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'ldUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'TelNum' => [
							'type'=> 'string',
							'default'=> '',
						],
					],
				],
				'default' => [
					['TName' =>'John Doe','TImage'=>['url'=>TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'],'TDesig'=>'Director','CusUrl'=>['url'=>''],'WsUrl'=>['url'=>''],'FbUrl'=>['url'=>'#'],'MailUrl'=>['url'=>''],'IGUrl'=>['url'=>'#'],'TwUrl'=>['url'=>''],'ldUrl'=>['url'=>''], 'TelNum' => ''],
				],
			],

			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 4,'xs' => 6 ],
			],
			'columnSpace' => [
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
						'selector' => '{{PLUS_WRAP}} .grid-item{padding:{{columnSpace}};}',
					],
				],
			],

			'TitleTag' => [
				'type' => 'string',
				'default' => 'h3',	
			],
			'FImageTp' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'DesignDis' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'SocialIcon' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'DisLink' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'DImgS' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'ImgSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'CategoryWF' => [
				'type' => 'boolean',
				'default' => False,	
			],
			'TextCat' => [
				'type'=> 'string',
				'default'=> 'All',
			],
			'CatFilterS' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'CatName' => [
				'type'=> 'string',
				'default'=> 'Filters',
			],
			'FilterHs' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'FilterAlig' => [
				'type' => 'string',
				'default' =>  [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-filter-data{text-align:{{FilterAlig}};}',
					],
				],
				'scopy' => true,
			],

			'TitleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-post-title,{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-post-title a',
					],
				],
				'scopy' => true,
			],
			'TNcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-post-title,{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-post-title a{color:{{TNcolor}};}',
					],
				],
				'scopy' => true,
			],
			'THcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover .tpgb-post-title,
							{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover .tpgb-post-title a{color:{{THcolor}};}',
					],
				],
				'scopy' => true,
			],

			'TextTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DesignDis', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-member-designation',
					],
				],
				'scopy' => true,
			],
			'TextNCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DesignDis', 'relation' => '==', 'value' => true]],						
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-member-designation{color:{{TextNCr}};}',
					],
				],
				'scopy' => true,
			],
			'TextHCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'DesignDis', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover .tpgb-member-designation{color:{{TextHCr}};}',
					],
				],
				'scopy' => true,
			],

			'Iconsize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-team-social-content .tpgb-team-social-list > div a{font-size:{{Iconsize}};}',
					],
				],
				'scopy' => true,
			],
			'IconBgsize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-team-social-content .tpgb-team-social-list > div a{width:{{IconBgsize}};height:{{IconBgsize}};line-height:{{IconBgsize}};}',
					],
				],
				'scopy' => true,
			],
			'IconNCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-team-social-content .tpgb-team-social-list > div a{color:{{IconNCr}};}',
					],
				],
				'scopy' => true,
			],
			'IconNBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .grid-item .tpgb-team-social-content .tpgb-team-social-list > div a{background:{{IconNBgCr}};}',
					],
				],
				'scopy' => true,
			],
			'IconHCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-team-social-content .tpgb-team-social-list > div a:hover{color:{{IconHCr}};}',
					],
				],
				'scopy' => true,
			],
			'IconHBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [ (object) ['key' => 'SocialIcon', 'relation' => '==', 'value' => true]],								
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .grid-item .tpgb-team-social-content .tpgb-team-social-list > div a:hover{background:{{IconHBgCr}};}',
					],
				],
				'scopy' => true,
			],

			'MaskImg' => [
				'type' => 'object',
				'default' => [
					'url' => '',
					'Id' => '',
				],				
			],
			'Imagesd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Style', 'relation' => '==', 'value' => 'style-4']],	
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list.team-style-4 .team-list-content .tpgb-team-profile',
					],
				],
				'scopy' => true,
			],
			'ExLImg' => [
				'type' => 'object',
				'default' => [
					'url' => '',
					'Id' => '',
				],
			],
			'AExlImg' => [
				'type' => 'string',
				'default' => 'none',	
				'scopy' => true,
			],
			'HAnimation' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],

			'FIMargin' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image{margin:{{FIMargin}};}',
					],
				],
				'scopy' => true,
			],
			'FIPadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image{padding:{{FIPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'FImgBs' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .tpgb-team-profile img,
									{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image,
									{{PLUS_WRAP}}.tpgb-team-member-list.team-style-2 .tpgb-team-profile{border-radius:{{FImgBs}};}',
					],
				],
				'scopy' => true,
			],
			'InnerBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [					
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image{background:{{InnerBgCr}};}',
					],
				],
				'scopy' => true,
			],

			'NFilter' => [
				'type' => 'object',
				'default' => [
					'openFilter' => false,
				],
				'style' => [
					(object) [					
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image img',
					],
				],
				'scopy' => true,
			],
			'NBoxSd' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .post-content-image',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover .post-content-image img',
					],
				],
			],
			'HBoxSd' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover .post-content-image',
					],
				],
				'scopy' => true,
			],

			'FcatTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-categories .tpgb-filter-list a',
					],
				],
				'scopy' => true,
			],
			'InPadding' => [
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
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
						'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count){padding:{{InPadding}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{padding:{{InPadding}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-3'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
						'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-3 .tpgb-filter-list a{padding:{{InPadding}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
						'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
					],
				],
				'scopy' => true,
			],
			'FCMargin' => [
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-categories .tpgb-filter-list{margin:{{FCMargin}};}',
					],
				],
				'scopy' => true,
			],
			'FCNcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCHBcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .hover-style-1 .tpgb-filter-list a::after{background:{{FCHBcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCHcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:hover,
						{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:focus,
						{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a.active,
						{{PLUS_WRAP}}.tpgb-category-filter .hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{color:{{FCHcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCBgHvrs' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => ['style-2','style-4']]],
						'selector' => '.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',
	
					],
				],
				'scopy' => true,
			],
			'FCHvrBre' => [
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
					],
				],
				'scopy' => true,
			],
			'FcBoxhversd'=> [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
					],
				],
				'scopy' => true,
			],
			'FCBgHs' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
					],
					(object) [
						'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',
					],
				],
				'scopy' => true,
			],
			'FCBgRs' => [
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count){border-radius:{{FCBgRs}};}',
					],
				],
				'scopy' => true,
			],			
			'FcBoxhsd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
					],
				],
				'scopy' => true,
			],
			'FCCategCcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCCategCcr}};}',
					],
				],
				'scopy' => true,
			],
						
			'FCBgTp' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],
			'FcBCrHs' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
					],
				],
				'scopy' => true,
			],	
			'FCBoxSd' => [
				'type' => 'object',
				'default' =>  (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],

			'BoxPadding' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content{padding:{{BoxPadding}};}',
					],
				],
				'scopy' => true,
			],
			'BoxTborder' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'Boxborder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxTborder', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content',
					],
				],
				'scopy' => true,
			],
			'BoxNBrs' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content{border-radius:{{BoxNBrs}};}',
					],
				],
				'scopy' => true,
			],
			'BoxHBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxTborder', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover',
					],
				],
				'scopy' => true,
			],
			'BoxHBrs' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover{border-radius:{{BoxHBrs}};}',
					],
				],
				'scopy' => true,
			],
			'BoxNBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content',
					],
				],
				'scopy' => true,
			],
			'BoxHBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover',
					],
				],
				'scopy' => true,
			],
			'BoxNSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content',
					],
				],
				'scopy' => true,
			],
			'BoxHSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-team-member-list .team-list-content:hover',
					],
				],
				'scopy' => true,
			],

			'MessyCol' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'Column1' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+1){margin-top:{{Column1}};}',
					],
				],
				'scopy' => true,
			],
			'Column2' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+2){margin-top:{{Column2}};}',
					],
				],
				'scopy' => true,
			],
			'Column3' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+3){margin-top:{{Column3}};}',
					],
				],
				'scopy' => true,
			],
			'Column4' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+4){margin-top:{{Column4}};}',
					],
				],
				'scopy' => true,
			],
			'Column5' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+5){margin-top:{{Column5}};}',
					],
				],
				'scopy' => true,
			],
			'Column6' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'MessyCol', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .post-loop-inner .grid-item:nth-child(6n+6){margin-top:{{Column6}};}',
					],
				],
				'scopy' => true,
			],

		];
		
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-team-listing', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_team_member_listing_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_team_member_listing' );

function TMCategoryFilter($attributes){
	$category_filter = '';
	$TeamMemberR = (!empty($attributes['TeamMemberR'])) ? $attributes['TeamMemberR'] : [];

	$filter_style = $attributes['CatFilterS'];
	$filter_hover_style = $attributes["FilterHs"];
	$all_filter_category = (!empty($attributes["TextCat"])) ? $attributes["TextCat"] : esc_html__('All','tpgbp');
	$loop_category = array();
	$count_loop = 0;
	
	foreach ( $TeamMemberR as $TMFilter ) {
		$TMCategory = !empty($TMFilter['TCateg']) ? $TMFilter['TCateg'] : '';
			if(!empty($TMCategory)){
				$loop_category[] = explode(',', $TMCategory);
			}
		$count_loop++;
	}
	$loop_category = TM_Split_Array_Category($loop_category);
	$count_category = array_count_values($loop_category);
	
	$all_category=$category_post_count='';
	if($filter_style=='style-1'){
		$all_category='<span class="tpgb-category-count">'.esc_html($count_loop).'</span>';
	}
	if($filter_style=='style-2' || $filter_style=='style-3'){
		$category_post_count='<span class="tpgb-category-count">'.esc_html($count_loop).'</span>';
	}

	$category_filter .='<div class="tpgb-filter-data '.esc_attr($filter_style).'">';
		if($filter_style=='style-4'){
			$category_filter .= '<span class="tpgb-filters-link">'.esc_html__('Filters','tpgbp').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
		}
			$category_filter .='<div class="tpgb-categories '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';			
				$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></div>';
			
					foreach ( $count_category as $key => $value ) {
						$slug = TM_Media_createSlug($key);								
						$category_post_count = '';
						if($filter_style=='style-2' || $filter_style=='style-3'){
							$category_post_count='<span class="tpgb-category-count">'.esc_html($value).'</span>';
						}
						$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list" data-filter=".'.esc_attr($slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($key).'">'.esc_html($key).'</span></a></div>';
					}

			$category_filter .= '</div>';
	$category_filter .= '</div>';
	
	return $category_filter;

}

function TM_Split_Array_Category($array){
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, TM_Split_Array_Category($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  }
	}
	
	return $result; 
}

function TM_Media_createSlug($str, $delimiter = '-'){
	$slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	return $slug;
}