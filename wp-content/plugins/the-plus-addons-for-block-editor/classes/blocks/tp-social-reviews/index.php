<?php
/**
 * Block : Social Reviews
 * @since 2.0.2
 */
defined( 'ABSPATH' ) || exit;

function tpgb_social_reviews_callback($attributes, $content) {
	$reviews = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $review_id = (!empty($attributes['review_id'])) ? $attributes['review_id'] : uniqid("review");

    $layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
    $RType = (!empty($attributes['RType'])) ? $attributes['RType'] : 'review';
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
    $columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'tpgb-col-12';
    $Rowclass = ($layout != 'carousel') ? 'tpgb-row' : '';
    
    $Repeater = (!empty($attributes['Rreviews'])) ? $attributes['Rreviews'] : [];
    $RefreshTime = (!empty($attributes['TimeFrq'])) ? $attributes['TimeFrq'] : '3600';
    $TimeFrq = array( 'TimeFrq' => $RefreshTime );
    $OverlayImage = (!empty($attributes['OverlayImage'])) ? "overlayimage" : "";

    $FeedId = (!empty($attributes['FeedId'])) ? preg_split("/\,/", $attributes['FeedId']) : [];
	$ShowFeedId = (!empty($attributes['ShowFeedId'])) ? $attributes['ShowFeedId'] : false;
	
    $txtLimt = (!empty($attributes['TextLimit']) ? $attributes['TextLimit'] : false );
	$TextCount = (!empty($attributes['TextCount']) ? $attributes['TextCount'] : 100 );
	$TextType = (!empty($attributes['TextType']) ? $attributes['TextType'] : 'char' );
	$TextMore = (!empty($attributes['TextMore']) ? $attributes['TextMore'] : 'Show More' );
	$TextLess = (!empty($attributes['TextLess']) ? $attributes['TextLess'] : 'Show Less' );
	$TextDots = (!empty($attributes['TextDots']) ? '...' : '' );
	$UserFooter = (!empty($attributes['s2Layout']) ? $attributes['s2Layout'] : 'layout-1' );

	$Performance = !empty($attributes['perf_manage']) ? $attributes['perf_manage'] : false;
    $disSocialIcon = !empty($attributes['disSocialIcon']) ? $attributes['disSocialIcon'] : false;
	$disProfileIcon = !empty($attributes['disProfileIcon']) ? $attributes['disProfileIcon'] : false;

    $blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

    $list_layout='';
	if( $layout=='grid' || $layout=='masonry' ){
		$list_layout = 'tpgb-isotope';
	}else{
		$list_layout = 'tpgb-isotope';
	}
	$desktop_class=$tablet_class=$mobile_class= '';
	if( $layout !='carousel' && $columns ){
		$desktop_class .= ' tpgb-col-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$tablet_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$mobile_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}
	
	$nFeedId = [];
	if(!empty($ShowFeedId)){
		$nFeedId = $FeedId;
	}
	
	$NormalScroll="";
	$cntScBr = !empty($attributes['cntScBr']) ? true : false;
	$sbheight = !empty($attributes['scrlHeight']) ? $attributes['scrlHeight'] : 100;
	if( !empty($cntScBr)){
		$ScrollData = array(
			'className'     => 'tpgb-normal-scroll',
			'ScrollOn'      => $cntScBr,
			'Height'        => (int)$sbheight,
			'TextLimit'     => $txtLimt,
		);
		$NormalScroll = json_encode($ScrollData, true);
	}
	$txtlimitData='';
	if(!empty($txtLimt)){
		$txtlimitDataa = array(
				'showmoretxt'     => $TextMore,
				'showlesstxt'     => $TextLess,
			);
	   $txtlimitData = json_encode($txtlimitDataa, true);
	}

    $reviews .= '<div class="tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' tpgb-social-reviews tpgb-relative-block '.esc_attr($list_layout).'" id="'.esc_attr($block_id).'" data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-rid="'.esc_attr($review_id).'" data-scroll-normal="'.esc_attr($NormalScroll).'" data-textlimit="'.esc_attr($txtlimitData).'">';

        if( $layout == 'carousel' &&  ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) )){
            if(isset($showArrows) && !empty($showArrows)){
                $reviews .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
            }
        }

        if($RType == "review"){
			$FinalData = [];
			$Perfo_transient = get_transient("SR-Performance-".$review_id);
			if( ($Performance == false) || ($Performance == true && $Perfo_transient === false) ){
				$AllData = [];
				foreach ($Repeater as $index => $R) {
					$RRT = (!empty($R['ReviewsType'])) ? $R['ReviewsType'] : 'facebook';
					$R = array_merge($TimeFrq,$R);

					if($RRT == 'facebook'){
						$AllData[] = tpgb_Facebook_Reviews($R,$attributes);
					}
				}
				if(!empty($AllData)){
					foreach($AllData as $key => $val){
						foreach($val as $key => $vall){ 
							$FinalData[] =  $vall; 
						}
					}
				}
				$Reviews_Index = array_column($FinalData, 'Reviews_Index');
				array_multisort($Reviews_Index, SORT_ASC, $FinalData);	 
				set_transient("SR-Performance-$review_id", $FinalData, $RefreshTime);
			}else{
				$FinalData = get_transient("SR-Performance-".$review_id);
			}
			
			if(!empty($FinalData)){
				
				foreach ($FinalData as $index => $data) {
					$PostId = !empty($data['PostId']) ? $data['PostId'] : [];
					if(in_array($PostId, $nFeedId)){
						unset($FinalData[$index]);
					}
				}
				
                $reviews .= '<div class="'.esc_attr($Rowclass).' post-loop-inner social-reviews-'.esc_attr($style).' '.esc_attr($OverlayImage).'" >';
                    foreach ($FinalData as $F_index => $Review) {
                        $RKey = (!empty($Review['RKey'])) ? $Review['RKey'] : '';
                        $RIndex = (!empty($Review['Reviews_Index'])) ? $Review['Reviews_Index'] : '';
                        $PostId = (!empty($Review['PostId'])) ? $Review['PostId'] : '';
                        $Type = (!empty($Review['Type'])) ? $Review['Type'] : '';
                        $Time = (!empty($Review['CreatedTime'])) ? $Review['CreatedTime'] : '';
                        $UName = (!empty($Review['UserName'])) ? $Review['UserName'] : '';
                        $UImage = (!empty($Review['UserImage'])) ? $Review['UserImage'] : '';
                        $ULink = (!empty($Review['UserLink'])) ? $Review['UserLink'] : '';
                        $PageLink = (!empty($Review['PageLink'])) ? $Review['PageLink'] : '';
                        $Massage = (!empty($Review['Massage'])) ? $Review['Massage'] : '';
                        $Icon = (!empty($Review['Icon'])) ? $Review['Icon'] : 'fas fa-star';
                        $Logo = (!empty($Review['Logo'])) ? $Review['Logo'] : '';
                        $rating = (!empty($Review['rating'])) ? $Review['rating'] : '';
                        $CategoryText = (!empty($Review['FilterCategory'])) ? $Review['FilterCategory'] : '';
                        $ReviewClass = (!empty($Review['selectType'])) ? ' '.$Review['selectType'] : '';
                        $ErrClass = (!empty($Review['ErrorClass']) ? $Review['ErrorClass'] : '');
                        $PlatformName = (!empty($Review['selectType'])) ? ucwords(str_replace('custom', '', $Review['selectType'])) : '';
                    
                        if(!in_array($PostId, $nFeedId)){
                            ob_start();
                                include TPGB_PATH. "includes/social-reviews/".sanitize_file_name('social-review-'.$style.'.php');
                                $reviews .= ob_get_contents();
                            ob_end_clean();
                        }
                    }
                
                $reviews .='</div>';
            }else{
                $reviews .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgb').'</div>';
            }
			
			
        }

    $reviews .='</div>';

    return $reviews;
}

function tpgb_Facebook_Reviews($RData,$attr){
    $Key = (!empty($RData['_key']) ? $RData['_key'] : '');
    $Token = (!empty($RData['Token']) ? $RData['Token'] : '');
    $PageId = (!empty($RData['FbPageId']) ? $RData['FbPageId'] : '');
    $FbRType = (!empty($RData['FbRType']) ? $RData['FbRType'] : '');
    $MaxR = (!empty($RData['MaxR']) ? $RData['MaxR'] : 6);
    $Ricon = (!empty($RData['icons']) ? $RData['icons'] : 'fas fa-star');
    $TimeFrq = (!empty($RData['TimeFrq']) ? $RData['TimeFrq'] : '');
	$RCategory = !empty($RData['RCategory']) ? $RData['RCategory'] : '';
	$ReviewsType = !empty($RData['ReviewsType']) ? $RData['ReviewsType'] : '';
	$Fb_Icon = TPGB_ASSETS_URL.'assets/images/social-review/facebook.svg';
	$FBNagative = !empty($attr['FBNagative']) ? $attr['FBNagative'] : 1;

    $API = "https://graph.facebook.com/v9.0/{$PageId}?access_token={$Token}&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit($MaxR),overall_star_rating,rating_count";
	
    $Fbdata=$FbArr=[];

    $GetAPI = get_transient("Fb-R-Url-$Key");
    $GetTime = get_transient("Fb-R-Time-$Key");
    if( $GetAPI != $API || $GetTime != $TimeFrq ){
        $Fbdata = tpgb_Review_Api($API);
        $Fbdata = json_encode($Fbdata);
        set_transient("Fb-R-Url-$Key", $API, $TimeFrq);
        set_transient("Fb-R-Data-$Key", $Fbdata, $TimeFrq);
        set_transient("Fb-R-Time-$Key", $TimeFrq, $TimeFrq);
    }else{
        $Fbdata = get_transient("Fb-R-Data-$Key");
    }
    
    if(!is_array($Fbdata)){
        $Fbdata = json_decode($Fbdata,true);
    }

    $Fb_status = (!empty($Fbdata['HTTP_CODE']) ? $Fbdata['HTTP_CODE'] : 400);
    if($Fb_status == 200){
        $Rating = (!empty($Fbdata['ratings']) && !empty($Fbdata['ratings']['data']) ? $Fbdata['ratings']['data'] : []);
        foreach ($Rating as $index => $Data){
            $FB = (!empty($Data['reviewer']) ? $Data['reviewer'] : '');
            $RT = (!empty($Data['recommendation_type']) ? $Data['recommendation_type'] : '');
			$Userlink = (!empty($Data['open_graph_story']) && !empty($Data['open_graph_story']['id']) ?$Data['open_graph_story']['id'] : '');
            $FType = (($FbRType == 'default') ? $RT : $FbRType);
			$rating = 5;
			if($RT == "negative"){
				$rating = $FBNagative;
			}
			
            if($FType == $RT){
                $FbArr[] = array(
                    "Reviews_Index"	=> $index,
                    "PostId"		=> (!empty($FB['id']) ? $FB['id'] : ''),
                    "Type" 			=> $RT,
                    "CreatedTime" 	=> (!empty($Data['created_time']) ? tpgb_Review_Time($Data['created_time']) : ''),
                    "UserName" 		=> (!empty($FB['name']) ? $FB['name'] : ''),
                    "UserImage" 	=> (!empty($FB['picture']) && !empty($FB['picture']['data']['url']) ? $FB['picture']['data']['url'] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'),
                    "UserLink"  	=> "https://www.facebook.com/$Userlink",
					"PageLink"  	=> "https://www.facebook.com/{$PageId}/reviews",
                    "Massage" 		=> (!empty($Data['review_text']) ? $Data['review_text'] : ''),
                    "Icon" 	        => $Ricon,
                    "rating"        => $rating,
                    "Logo"          => $Fb_Icon,
                    "selectType"    => $ReviewsType,
                    "FilterCategory"=> $RCategory,
                    "RKey" 			=> "tp-repeater-item-$Key",
                );
            }

        }
    }else{
        $FbArr[] = tpgb_Review_Error_array( $Fbdata, $Key, $Fb_Icon, $ReviewsType, $RCategory );
    }
    return $FbArr;
}

function tpgb_Review_Time($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
 
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
 
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
 
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function tpgb_Review_Api($API){
	$Final=[];

	$URL = wp_remote_get($API);
	$StatusCode = wp_remote_retrieve_response_code($URL);
	$GetDataOne = wp_remote_retrieve_body($URL);
	$Statuscode = array( "HTTP_CODE" => $StatusCode );

	$Response = json_decode($GetDataOne, true);
	if( is_array($Statuscode) && is_array($Response) ){
		$Final = array_merge($Statuscode, $Response);
	}
	return $Final;
}

function tpgb_Review_Error_array( $Data, $RKey, $Icon, $ReviewsType, $RCategory ){
	$Message='';
	if( !empty($Data) && !empty($Data['error_message']) ){
		$Message = $Data['error_message'];
	}else if( !empty($Data) && !empty($Data['error']) && !empty($Data['error']['Message_Errorcurl']) ){
		$Message = $Data['error']['Message_Errorcurl'];
	}else if( !empty($Data) && !empty($Data['error']) ){ 	/* new */
		$Message = $Data['error']['message'];
	}else if( !empty($Data) && !empty($Data['status']) ){	/* new */
		$Message = $Data['status'];
	}else{
		$Message = 'Something Wrong';
	}

	return  array(
		"Reviews_Index" => 1,
		"ErrorClass"    => "danger-error",
		"CreatedTime" 	=> !empty($Data['status']) ? $Data['status'] : '',
		"Massage" 		=> $Message,
		"UserName" 		=> !empty($Data['HTTP_CODE']) ? 'Error No : '.$Data['HTTP_CODE'] : '',
		"UserImage" 	=> $Icon,
		"Logo"          => $Icon,
		"selectType"    => $ReviewsType,
		"FilterCategory"=> $RCategory,
		"RKey" 			=> "tp-repeater-item-{$RKey}",
	);
}

function tpgb_social_reviews() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();

    $Ruid ='F'.substr(uniqid(),-4);
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
		'review_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'layout' => [
            'type'=> 'string',
            'default'=> 'grid',
        ],
        'RType' => [
            'type'=> 'string',
            'default'=> 'review',
        ],
        'style' => [
            'type'=> 'string',
            'default'=> 'style-1',
        ],
		's2Layout' => [
            'type'=> 'string',
            'default'=> 'layout-1',
        ],
        'Rreviews' => [
            'type'=> 'array',
            'repeaterField' => [
                (object) [
                    'ReviewsType' => [
                        'type' => 'string',
                        'default' => 'facebook',	
                    ],
                    'Token' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'FbPageId' => [
                        'type'=> 'string',
                        'default'=> '',
                    ],
                    'FbRType' => [
                        'type' => 'string',
                        'default' => 'default',	
                    ],
                    'icons' => [
                        'type'=> 'string',
                        'default'=> 'fas fa-star',
                    ],
                    'RCategory' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'MaxR' => [
                        'type' => 'string',
                        'default' => 6,	
                    ],

                ],
            ],
            'default' => [ 
                [ 
                    '_key'=> $Ruid,
                    'ReviewsType' => 'facebook',
                    'MaxR' => 6, 
                    'FbRType' => 'default',
                ],
            ],
        ],

        'columns' => [
            'type' => 'object',
            'default' => [ 'md' => 4,'sm' => 4,'xs' => 6 ],
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .post-loop-inner .grid-item{padding:{{columnSpace}};}',
                ],
            ],
			'scopy' => true,
        ],
		
        'FBNagative' => [
            'type' => 'string',
            'default' => '1',
        ],
		'ShowFeedId' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'FeedId' => [
            'type' => 'string',
            'default' => '',	
        ],
		
		/* Review Extra Option Start*/
        'TimeFrq' => [
            'type' => 'string',
            'default' => '3600',
        ],
        'TextLimit' => [
            'type' => 'boolean',
            'default' => true,
        ],
        'TextType' => [
            'type' => 'string',
            'default' => 'char',	
        ],
        'TextMore' => [
            'type' => 'string',
            'default' => 'Show More',	
        ],
        'TextCount' => [
            'type' => 'string',
            'default' => 100,	
        ],
        'TextDots' => [
            'type' => 'boolean',
            'default' => true,
        ],
		'cntScBr' => [
            'type' => 'boolean',
            'default' => false,
        ],
		'scrlHeight' => [
            'type' => 'string',
            'default' => '',
        ],
        'disSocialIcon' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'disProfileIcon' => [
            'type' => 'boolean',
            'default' => false,
        ],
		/* Review Extra Option End*/
		'perf_manage' => [
            'type' => 'boolean',
            'default' => false,
        ],
		
		/* Universal Style Start */
        'UnnameTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 
                'openTypography' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-username a',
                ],
            ],
			'scopy' => true,
        ],
        'UnMsgTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 
                'openTypography' => 0
             ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-content',
                ],
            ],
			'scopy' => true,
        ],
        'UnPostOnTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-newline:nth-child(n)',
                ],
            ],
			'scopy' => true,
        ],
        'UnTimeTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 
                'openTypography' => 0
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-time',
                ],
            ],
			'scopy' => true,
        ],
        
        'UnnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-username a{color:{{UnnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-content{color:{{UnMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-newline:nth-child(n){color:{{UnPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-time{color:{{UnTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnHnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover .tpgb-sr-username a{color:{{UnHnameCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .review-s3-wrap:hover .tpgb-sr-username a{color:{{UnHnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnHMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover .tpgb-sr-content{color:{{UnHMassageCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .review-s3-wrap:hover .tpgb-sr-content{color:{{UnHMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnHPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover .tpgb-newline:nth-child(n){color:{{UnHPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnHTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover .tpgb-sr-time{color:{{UnHTimeCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .review-s3-wrap:hover .tpgb-sr-time{color:{{UnHTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
		
		/* Box Background Option Added */
        'BgBoxPadding' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .grid-item .tpgb-review{padding:{{BgBoxPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnNBg' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBg'=> 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'UnNB' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBorder' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'UnNBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review{border-radius:{{UnNBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnNBs' => [
            'type' => 'object',
            'default' => (object) [ 
                'openShadow' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'UnHBg' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBg'=> 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'UnHB' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBorder' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'UnHBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review:hover{border-radius:{{UnHBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'UnHBs' => [
            'type' => 'object',
            'default' => (object) [ 
                'openShadow' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
		
        'BgHpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-header{padding:{{BgHpd}};}',
                ],
            ],
			'scopy' => true,
        ],
		
        'BgFpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-bottom{padding:{{BgFpd}};}',
                ],
            ],
			'scopy' => true,
        ], 
        
        'topBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-header',
                ],
            ],
			'scopy' => true,
        ],
        'topB' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-header',
                ],
            ],
			'scopy' => true,
        ],
		
        'BottomBg' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3'] ],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-bottom',
                ],
            ],
			'scopy' => true,
        ],
        'BottomB' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-bottom',
                ],
            ],
			'scopy' => true,
        ],
		
		'StarIconPdg' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-star{padding:{{StarIconPdg}};}',
                ],
            ],
			'scopy' => true,
        ],
        'StarIconCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-star .sr-star{color:{{StarIconCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'StarIconspace' => [
            'type' => 'object',
            'default' => [ 
                'md' => "",
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-star .sr-star{width:{{StarIconspace}};}',
                ],
            ],
			'scopy' => true,
        ],
        'StarIconsize' => [
            'type' => 'object',
            'default' => [ 
                'md' => "",
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-star .sr-star{font-size:{{StarIconsize}};}',
                ],
            ],
			'scopy' => true,
        ],
		
		'BgBpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-content{padding:{{BgBpd}};}',
                ],
            ],
			'scopy' => true,
        ],
		'uNamePdg' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-username{padding:{{uNamePdg}};}',
                ],
            ],
			'scopy' => true,
        ],
		'uNameMrg' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-username{margin:{{uNameMrg}};}',
                ],
            ],
			'scopy' => true,
        ],
		'OverlayImage' => [
            'type' => 'boolean',
            'default' => false,
        ],
		'oImgPos' => [
            'type' => 'object',
            'default' => [ 
                'md' => "",
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3'],
                                    (object) ['key' => 'OverlayImage', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .social-reviews-style-1.overlayimage img.tpgb-sr-profile{left:{{oImgPos}};} {{PLUS_WRAP}} .social-reviews-style-2.overlayimage img.tpgb-sr-profile{top:{{oImgPos}};}',
                ],
            ],
			'scopy' => true,
        ],
		'pImgBdr' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-profile',
                ],
            ],
			'scopy' => true,
        ],
        'BgPRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-profile{border-radius:{{BgPRs}};}',
                ],
            ],
			'scopy' => true,
        ],
		'pImgBShadow' => [
            'type' => 'object',
            'default' => (object) [ 
                'openShadow' => 0 
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-sr-profile',
                ],
            ],
			'scopy' => true,
        ],
		/* Universal Style End */

        'FbnameTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-username a',
                ],
            ],
			'scopy' => true,
        ],
        'FbMsgTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-content',
                ],
            ],
			'scopy' => true,
        ],
        'FbPostOnTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-logotext',
                ],
            ],
			'scopy' => true,
        ],
        'FbTimeTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-time',
                ],
            ],
			'scopy' => true,
        ],
        'FbnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-sr-username a{color:{{FbnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-sr-content{color:{{FbMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-sr-time{color:{{FbTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-sr-logotext .tpgb-newline:nth-child(n){color:{{FbPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-review:hover .tpgb-sr-username a{color:{{FbHnameCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .review-s3-wrap:hover .tpgb-sr-username a{color:{{FbHnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-review:hover .tpgb-sr-content{color:{{FbHMassageCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .review-s3-wrap:hover .tpgb-sr-content{color:{{FbHMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-review:hover .tpgb-sr-time{color:{{FbHTimeCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .review-s3-wrap:hover .tpgb-sr-time{color:{{FbHTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-review:hover .tpgb-sr-logotext{color:{{FbHPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbBpadding' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review{padding:{{FbBpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbNBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'FbNB' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'FbCRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review{border-radius:{{FbCRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'FbBHpadding' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}} .facebook .tpgb-review:hover{padding:{{FbBHpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'FbHB' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'FbBHRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review:hover{border-radius:{{FbBHRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'FbPRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-profile{border-radius:{{FbPRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbHpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-header{padding:{{FbHpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbBpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-content{padding:{{FbBpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'FbFpd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .facebook .tpgb-sr-bottom{padding:{{FbFpd}};}',
                ],
            ],
			'scopy' => true,
        ],

        'SmTxtTypo' => [
            'type'=> 'object',
            'default'=> (object) ['openTypography' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message a.readbtn',
                ],
            ],
			'scopy' => true,
        ],

        'SmTxtNCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message a.readbtn{color:{{SmTxtNCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'SlTxtNCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message.show-less a.readbtn{color:{{SlTxtNCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'DotTxtNCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message .sf-dots{color:{{DotTxtNCr}};}',
                ],
            ],
			'scopy' => true,
        ],

        'SmTxtHCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message a.readbtn:hover{color:{{SmTxtHCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'SlTxtHCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message.show-less a.readbtn:hover{color:{{SlTxtHCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'DotTxtHCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'TextLimit', 'relation' => '==', 'value' => true ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-message:hover .sf-dots{color:{{DotTxtHCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        
        'CategoryWF' => [
            'type' => 'boolean',
            'default' => False,	
        ],

        'postLodop' => [
            'type' => 'string',
            'default' => 'none',
        ],
    ];

    $attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-social-reviews', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_social_reviews_callback'
    ));
}
add_action( 'init', 'tpgb_social_reviews' );