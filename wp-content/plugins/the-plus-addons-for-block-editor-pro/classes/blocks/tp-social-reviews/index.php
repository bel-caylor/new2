<?php
/**
 * Block : Social Reviews
 * @since 2.0.2
 */
defined( 'ABSPATH' ) || exit;

function tpgbp_social_reviews_callback($attributes, $content) {
	$reviews = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $review_id = (!empty($attributes['review_id'])) ? $attributes['review_id'] : uniqid("review");

    $layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
    $RType = (!empty($attributes['RType'])) ? $attributes['RType'] : 'review';
    $style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
    $columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'tpgb-col-12';
    $Rowclass = ($layout != 'carousel') ? 'tpgb-row' : '';

    $showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
    $slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
    
    $Repeater = (!empty($attributes['Rreviews'])) ? $attributes['Rreviews'] : [];
    $RefreshTime = (!empty($attributes['TimeFrq'])) ? $attributes['TimeFrq'] : '3600';
    $TimeFrq = array( 'TimeFrq' => $RefreshTime );
    $OverlayImage = (!empty($attributes['OverlayImage'])) ? "overlayimage" : "";

    $FeedId = (!empty($attributes['FeedId'])) ? preg_split("/\,/", $attributes['FeedId']) : [];
	$ShowFeedId = (!empty($attributes['ShowFeedId'])) ? $attributes['ShowFeedId'] : false;
	$CategoryWF = (!empty($attributes['CategoryWF'])) ? $attributes['CategoryWF'] : '';
	$Categoryclass = (!empty($CategoryWF) ? 'tpgb-filter' : '' );

    $Postdisplay = (!empty($attributes['Postdisplay']) ? (int)$attributes['Postdisplay'] : '');
	$postLodop = (!empty($attributes['postLodop']) ? $attributes['postLodop'] : '');
	$postview = (!empty($attributes['postview']) ? $attributes['postview'] : 1);
	$loadbtnText = (!empty($attributes['loadbtnText']) ? $attributes['loadbtnText'] : '');
	$loadingtxt = (!empty($attributes['loadingtxt']) ? $attributes['loadingtxt'] : '');
    $allposttext = (!empty($attributes['allposttext']) ? $attributes['allposttext'] : '');
    
    $txtLimt = (!empty($attributes['TextLimit']) ? $attributes['TextLimit'] : false );
	$TextCount = (!empty($attributes['TextCount']) ? $attributes['TextCount'] : 100 );
	$TextType = (!empty($attributes['TextType']) ? $attributes['TextType'] : 'char' );
	$TextMore = (!empty($attributes['TextMore']) ? $attributes['TextMore'] : 'Show More' );
	$TextLess = (!empty($attributes['TextLess']) ? $attributes['TextLess'] : 'Show Less' );
	$TextDots = (!empty($attributes['TextDots']) ? '...' : '' );
	$UserFooter = (!empty($attributes['s2Layout']) ? $attributes['s2Layout'] : 'layout-1' );

    $arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : 'style-1';
	$Performance = !empty($attributes['perf_manage']) ? $attributes['perf_manage'] : false;

	$disSocialIcon = !empty($attributes['disSocialIcon']) ? $attributes['disSocialIcon'] : false;
	$disProfileIcon = !empty($attributes['disProfileIcon']) ? $attributes['disProfileIcon'] : false;

    $blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

    $equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}

    $list_layout='';
	if( $layout=='grid' || $layout=='masonry' ){
		$list_layout = 'tpgb-isotope';
	}else if( $layout =='carousel' ){
		$list_layout = 'tpgb-carousel splide ';	
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

	$Sliderclass = '';
	if($slideHoverDots==true && ( ( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
		$Sliderclass .= ' hover-slider-dots';
	}
	if($outerArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' outer-slider-arrow';
	}
	if($slideHoverArrows==true && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
		$Sliderclass .= ' hover-slider-arrow';
	}
    if( $layout =='carousel' && (( isset($showDots['md']) && !empty($showDots['md']) ) || ( isset($showDots['sm']) && !empty($showDots['sm']) ) || ( isset($showDots['xs']) && !empty($showDots['xs']) )) ){
		$Sliderclass .=' dots-'.esc_attr($dotsStyle);
	}

	$carousel_settings = '';
	if($layout=='carousel'){
		$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );
        $carousel_settings = 'data-splide=\''.json_encode($carousel_settings).'\' ';
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

    $reviews .= '<div class="tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' tpgb-social-reviews tpgb-relative-block '.esc_attr($list_layout).' '.esc_attr($Categoryclass).' '.esc_attr($Sliderclass).' '.esc_attr($equalHclass).'" id="'.esc_attr($block_id).'" data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-rid="'.esc_attr($review_id).'" '.$carousel_settings.' data-scroll-normal="'.esc_attr($NormalScroll).'" data-textlimit="'.esc_attr($txtlimitData).'" '.$equalHeightAtt.'>';

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
						$AllData[] = tpgbp_Facebook_Reviews($R,$attributes);
					}else if($RRT == 'google'){
						$AllData[] = tpgbp_Google_Reviews($R);
					}else if($RRT == 'custom'){
						$AllData[] = tpgbp_Custom_Reviews($R);
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
				
				if(!empty($CategoryWF) && $layout != 'carousel'){
					$FilterTotal='';
					if($postLodop=='load_more' || $postLodop=='lazy_load'){
						$FilterTotal = $Postdisplay;
					}else{
						$FilterTotal = count($FinalData);
					}
					
					$reviews .= tpgbp_Reviews_Category($FilterTotal, $FinalData,$attributes);
				}
				
				if($layout != 'carousel' && ($postLodop=='load_more' || $postLodop=='lazy_load')){
					$totalReviews = (count($FinalData));
					$trans_store = get_transient("SR-LoadMore-".$review_id);
					
					if( $trans_store === false){
						set_transient("SR-LoadMore-".$review_id, $FinalData , $RefreshTime);
					}else if(!empty($trans_store) && is_array($trans_store) && count($trans_store)!=$totalReviews){
						set_transient("SR-LoadMore-".$review_id, $FinalData , $RefreshTime);
					}
					
					$FinalData = array_slice($FinalData, 0, $Postdisplay);
					
					$postattr =[
						'load_class'	=> esc_attr($block_id),
						'review_id'		=> esc_attr($review_id),
						'layout'		=> esc_attr($layout),
						'style'			=> esc_attr($style),
						's2Layout'		=> esc_attr($UserFooter),
						'desktop_column'=> esc_attr($attributes['columns']['md']),
						'tablet_column'	=> esc_attr($attributes['columns']['sm']),
						'mobile_column'	=> esc_attr($attributes['columns']['xs']),
						'DesktopClass'	=> esc_attr($desktop_class),
						'TabletClass'	=> esc_attr($tablet_class),
						'MobileClass'	=> esc_attr($mobile_class),
						'TimeFrq'		=> esc_attr($attributes['TimeFrq']),
						'FeedId'		=> $nFeedId,
						'categorytext'	=> esc_attr($CategoryWF),
						'TextLimit'		=> esc_attr($txtLimt),
						'TextCount'		=> esc_attr($TextCount),
						'TextType'		=> esc_attr($TextType),
						'TextMore'		=> esc_attr($TextMore),
						'TextLess'		=> esc_attr($TextLess),
						'TextDots'		=> esc_attr($TextDots),
						'loadingtxt'	=> esc_attr($loadingtxt),
						'allposttext'	=> esc_attr($allposttext),
						'TotalReview'	=> esc_attr($totalReviews),
						'postview'		=> esc_attr((int)$postview),
						'display'		=> esc_attr($Postdisplay),
						'disSocialIcon'	=> esc_attr($disSocialIcon),
						'disProfileIcon'=> esc_attr($disProfileIcon),
						'FilterStyle'	=> esc_attr($attributes['CatFilterS']),
						'tpgb_nonce' 	=> wp_create_nonce("theplus-addons-block"),
					];
					$data_loadkey = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($postattr), 'ey' );
				}
				
                $reviews .= '<div class="'.esc_attr($Rowclass).' post-loop-inner '.($layout == 'carousel' ? ' splide__track ' : '').' social-reviews-'.esc_attr($style).' '.esc_attr($OverlayImage).'" >';
                if($layout =='carousel'){
                    $reviews .= '<div class="splide__list">';
                }
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

                        $category_filter=$loop_category='';
                        if( !empty($CategoryWF) && !empty($CategoryText)  && $layout != 'carousel' ){
                            $loop_category = explode(',', $CategoryText);
                            foreach( $loop_category as $category ) {
                                $category = tpgbp_Reviews_Media_createSlug($category);
                                $category_filter .=' '.esc_attr($category).' ';
                            }
                        }
                    
                        if(!in_array($PostId, $nFeedId)){
                            ob_start();
                                include TPGBP_PATH. "includes/social-reviews/social-review-{$style}.php";
                                $reviews .= ob_get_contents();
                            ob_end_clean();
                        }
                    }
                if($layout =='carousel'){
                    $reviews .='</div>';
                }
                $reviews .='</div>';
				if( !empty($totalReviews) && $totalReviews > $Postdisplay ){
					if($postLodop=='load_more' && $layout != 'carousel'){
						$reviews .= '<div class="tpgb-review-load-more" style="margin:20px">';
							$reviews .= '<a class="review-load-more" aria-label="'.esc_attr($loadbtnText).'" data-loadingtxt="'.esc_attr($loadingtxt).'" data-layout="'.esc_attr($layout).'"  data-loadclass="'.esc_attr($block_id).'" data-totalreviews="'.esc_attr($totalReviews).'" data-display="'.esc_attr($Postdisplay).'" data-loadview="'.esc_attr($postview).'" data-loadattr= \'' . $data_loadkey . '\'>'.esc_html($loadbtnText).'</a>';
						$reviews .= '</div>';
					}else if($postLodop=='lazy_load' && $layout!='carousel'){
						$reviews .= '<div class="tpgb-review-lazy-load">';
							$reviews .= '<a class="review-lazy-load" aria-label="'.esc_attr($loadingtxt).'" data-loadingtxt="'.esc_attr($loadingtxt).'" data-lazylayout="'.esc_attr($layout).'" data-lazyclass="'.esc_attr($block_id).'" data-totalreviews="'.esc_attr($totalReviews).'" data-display="'.esc_attr($Postdisplay).'" data-lazyview="'.esc_attr($postview).'" data-lazyattr= \'' . $data_loadkey . '\'>';
								$reviews .= '<div class="tpgb-spin-ring"><div></div><div></div><div></div></div>';
							$reviews .= '</a>';
						$reviews .= '</div>';
					}
				}
				
            }else{
                $reviews .= '<div class="error-handal">'.esc_html__('All Social Feed','tpgbp').'</div>';
            }
			
			
        }else if($RType == "beach"){
            $Bstyle = (!empty($attributes['Bstyle'])) ? $attributes['Bstyle'] : 'style-1';
            $BRecommend = (!empty($attributes['BRecommend']) ? $attributes['BRecommend'] : "");
            $BSButton = (!empty($attributes['BSButton']) ? $attributes['BSButton'] : "");
            $BBtnName = (!empty($attributes['BBtnName']) ? $attributes['BBtnName'] : "");
            $Btxt1 = (!empty($attributes['Btxt1']) ? $attributes['Btxt1'] : "");
            $Btxt2 = (!empty($attributes['Btxt2']) ? $attributes['Btxt2'] : "");
            $Blinktxt = (!empty($BRecommend) && !empty($attributes['Blinktxt']) ? $attributes['Blinktxt'] : "");
            $Btn2NO = (!empty($BRecommend) && !empty($attributes['BBtnTName']) ? $attributes['BBtnTName'] : "");
            $BIcon = (!empty($attributes['BDyIcon']) ? $attributes['BDyIcon'] : "fas fa-star" );
            $BIconHidden2 = (!empty($attributes['IconHidden']) ? $attributes['IconHidden'] : false);

            $BeachData = tpgbp_Beach_Reviews($attributes);
            $Beach = (!empty($BeachData[0]) ? $BeachData[0] : []);

            $BTotal = (!empty($Beach['Total']) ? $Beach['Total'] : "");
            $BLink = (!empty($Beach['UserLink']) ? $Beach['UserLink'] : "");
            $BLogo = (!empty($Beach['Logo']) ? $Beach['Logo'] : "");
            $BType = (!empty($Beach['Type']) ? $Beach['Type'] : "");
            $BUname = (!empty($Beach['Username']) ? $Beach['Username'] : "");
            $BUImage = (!empty($Beach['UserImage']) ? $Beach['UserImage'] : []);
            $BRating = (!empty($Beach['Rating']) ? $Beach['Rating'] : "");
            $BErrClass = (!empty($Beach['ErrorClass']) ? $Beach['ErrorClass'] : "");
            $BMassage = (!empty($Beach['Massage']) ? $Beach['Massage'] : "");

            ob_start();
                include TPGBP_PATH. "includes/social-reviews/social-review-b-{$Bstyle}.php";
                $reviews .= ob_get_contents();
            ob_end_clean();

        }

    $reviews .='</div>';

    if($layout == 'carousel'){
		//Show Arrow Media Css
		$arrowCss = '';
		if($arrowCss != ''){
			$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		}
		$reviews .= $arrowCss;
	}

    return $reviews;
}

function tpgbp_Facebook_Reviews($RData,$attr){
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
        $Fbdata = tpgbp_Review_Api($API);
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
                    "CreatedTime" 	=> (!empty($Data['created_time']) ? tpgbp_Review_Time($Data['created_time']) : ''),
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
        $FbArr[] = tpgbp_Review_Error_array( $Fbdata, $Key, $Fb_Icon, $ReviewsType, $RCategory );
    }
    return $FbArr;
}
function tpgbp_Google_Reviews($RData){
    $Key = (!empty($RData['_key']) ? $RData['_key'] : '');
    $Token = (!empty($RData['Token']) ? $RData['Token'] : '');
    $GPlace = (!empty($RData['GPlaceID']) ? $RData['GPlaceID'] : '');
    $TimeFrq = (!empty($RData['TimeFrq']) ? $RData['TimeFrq'] : 3600);
    $Ricon = (!empty($RData['icons']) ? $RData['icons'] : 'fas fa-star');
    $MaxR = (!empty($RData['MaxR']) ? $RData['MaxR'] : '');
	$ReviewsType = !empty($RData['ReviewsType']) ? $RData['ReviewsType'] : '';
	$RCategory = !empty($RData['RCategory']) ? $RData['RCategory'] : '';
	$GG_Icon = TPGB_ASSETS_URL.'assets/images/social-review/google.webp';
	$GLanguage = !empty($RData['GLanguage']) ? $RData['GLanguage'] : 'en';
    $Gdata=$GArr=[];

    $API = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$GPlace}&key={$Token}&language={$GLanguage}";

    $GetAPI = get_transient("G-R-Url-$Key");
    $GetTime = get_transient("G-R-Time-$Key");
    if( $GetAPI != $API || $GetTime != $TimeFrq ){
        $Gdata = tpgbp_Review_Api($API);
        $Gdata = json_encode($Gdata);
        set_transient("G-R-Url-$Key", $API, $TimeFrq);
        set_transient("G-R-Time-$Key", $TimeFrq, $TimeFrq);
        set_transient("G-R-Data-$Key", $Gdata, $TimeFrq);
    }else{
        $Gdata = get_transient("G-R-Data-$Key");
    }
    if(!is_array($Gdata)){
        $Gdata = json_decode($Gdata,true);
    }

    $G_status = (!empty($Gdata['HTTP_CODE']) ? $Gdata['HTTP_CODE'] : 400);
    if($G_status == 200 && empty($Gdata['error_message']) && $Gdata['status'] == 'OK'){
		
        $GR = !empty($Gdata['result']['reviews']) ? $Gdata['result']['reviews'] : [];
		$PlaceName = strtolower(str_replace(' ', '_', $Gdata['result']['name']));
		$PlaceURL = !empty($Gdata['result']['url']) ? $Gdata['result']['url'] : '';
		
		$GG_Databash = get_option("gutenberg_google_review_{$PlaceName}");
		if ( !empty($GR) && (empty($GG_Databash) || $GG_Databash == false) ) {
			add_option( "gutenberg_google_review_{$PlaceName}", $GR, "", "yes" );
		}else if( !empty($GR) && !empty($GG_Databash) ) {
			$AarayTemp = [];
			foreach ($GG_Databash as $i1 => $Gdata){
				$AarayTemp[] = $Gdata['author_url'];
			}
			
			foreach ($GR as $i1 => $DataOne){
				$AuthorUrlOne = !empty($DataOne['author_url']) ? $DataOne['author_url'] : [];
				foreach ($GG_Databash as $i2 => $DataTwo){
					$AuthorUrlTwo = !empty($DataTwo['author_url']) ? $DataTwo['author_url'] : [];
					if( $AuthorUrlOne != $AuthorUrlTwo ){
						if( !in_array( $AuthorUrlOne, $AarayTemp ) ){
							$AarayTemp[] = $DataOne['author_url'];
							$GG_Databash[] = array(
								"author_name" 				=> !empty($DataOne['author_name']) ? $DataOne['author_name'] : '',
								"author_url" 				=> !empty($DataOne['author_url']) ? $DataOne['author_url'] : '',
								"language" 					=> !empty($DataOne['language']) ? $DataOne['language'] : 'en',
								"profile_photo_url" 		=> !empty($DataOne['profile_photo_url']) ? $DataOne['profile_photo_url'] : '',
								"rating" 					=> !empty($DataOne['rating']) ? $DataOne['rating'] : '',
								"relative_time_description" => !empty($DataOne['relative_time_description']) ? $DataOne['relative_time_description'] : '',
								"text" 						=> !empty($DataOne['text']) ? $DataOne['text'] : '',
								"time" 						=> !empty($DataOne['time']) ? $DataOne['time'] : '',
							);
							update_option( "gutenberg_google_review_{$PlaceName}", $GG_Databash);
						}
					}
				}
			}
			$GR = $GG_Databash;
		}
		
        foreach ($GR as $index => $G){
            if($index < $MaxR){
                $UnqURl = explode('/', trim($G['author_url']));
                $UnqName = explode(' ', trim($G['author_name'])); 
                $Time = (!empty($G['relative_time_description']) ? $G['relative_time_description'] : '');

                $GArr[] = array(
                    "Reviews_Index"	=> $index,
                    "PostId"		=> (!empty($UnqName[0]) && !empty($UnqURl[5]) ? $UnqName[0].'-'.substr($UnqURl[5], 0, 10) : ''),
                    "Type" 			=> "",
                    "CreatedTime" 	=> $Time,
                    "UserName" 		=> (!empty($G['author_name']) ? $G['author_name'] : ''),
                    "UserImage" 	=> (!empty($G['profile_photo_url']) ? $G['profile_photo_url'] : ''),
                    "UserLink" 	    => (!empty($G['author_url']) ? $G['author_url'] : ''),
					"PageLink"  	=> $PlaceURL,
                    "Massage" 		=> (!empty($G['text']) ? $G['text'] : ''),
                    "Icon" 	        => $Ricon,
                    "rating"        => (!empty($G['rating']) ? $G['rating'] : ''),
                    "Logo"          => $GG_Icon,
                    "selectType"    => $ReviewsType,
                    "FilterCategory"=> $RCategory,
                    "RKey" 			=> "tp-repeater-item-$Key",
                );
            }
        }
    }else{
        $GArr[] = tpgbp_Review_Error_array( $Gdata, $Key, $GG_Icon, $ReviewsType, $RCategory );
    }
    
    return $GArr;
}
function tpgbp_Custom_Reviews($RData){
    $Key = (!empty($RData['_key']) ? $RData['_key'] : '');
    $MaxR = (!empty($RData['MaxR']) ? $RData['MaxR'] : '');   
    $CType = (!empty($RData['CPFname']) ? $RData['CPFname'] : 'facebook'); 
    $Ricon = (!empty($RData['icons']) ? $RData['icons'] : 'fas fa-star');
    
    $Name=[];
    if(!empty($RData['Cuname'])){
        $Cuname = explode('|', $RData['Cuname']);
        foreach ($Cuname as $D){ $Name[] = array("Name"=> $D); }
    }else{
        $Name[] = array("Name"=> "Gabriel");
    }

    $Massage=[];
    if(!empty($RData['Cmassage'])){
        $Cmassage = explode('|', $RData['Cmassage']);
        foreach ($Cmassage as $D){ $Massage[] = array("Message"=> $D); }
    }
    
    $Date=[];
    if(!empty($RData['Cdate'])){
        $Cdate = explode('|', $RData['Cdate']);
        foreach ($Cdate as $D){ $Date[] = array("Date"=>$D); }
    }

    $Star=[];
    if(!empty($RData['Cstar'])){
        $Cstar = explode('|', $RData['Cstar']);
        foreach ($Cstar as $D){
			$nStar = ($D > 5 ? 5 : $D);
			$Star[] = array("Star"=>$nStar); 
	}
    }

    $Platform=$logo="";
    if($CType == 'custom'){
        $Platform = (!empty($RData['CcuSname']) ? $RData['CcuSname'] : []);
        $logo = ((!empty($RData['CImg']) && !empty($RData['CImg']['url'])) ? $RData['CImg']['url'] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg');
    }else if($CType == 'facebook'){
        $Platform = $CType;
        $logo = TPGB_ASSETS_URL.'assets/images/social-review/facebook.svg';
    }else if($CType == 'google'){
        $Platform = $CType;
        $logo = TPGB_ASSETS_URL.'assets/images/social-review/google.webp';
    }
    
    $PImg=[];
    if(!empty($RData['CUImg']) && empty($RData['CUImg']['url'])){
        foreach ($RData['CUImg'] as $D){ 
            $PImg[] = array("Profile" => $D['url']); 
        }
    }

    $All = [];
    foreach ($Name as $key => $value){
        $FImg = (!empty($PImg[$key]) ? $PImg[$key] : array("Profile" => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg') );
        $FMsg = (!empty($Massage[$key]) ? $Massage[$key] : array("Message"=> "Good") );
        $FStar = (!empty($Star[$key]) ? $Star[$key] : array("Star"=> "3") );
        $FDate = (!empty($Date[$key]) ? $Date[$key] : array("Date"=> "3 day ago") );

        $All[] = array_merge( (array)$value,$FMsg,$FDate,$FStar,$FImg );
    }

    if($CType == 'custom'){ 
        $Platform = "custom $Platform"; 
    }

    $Arr=[];
    if(!empty($All)){
        foreach ($All as $i => $v){
            if($i < $MaxR){
				$C_Name = explode(' ', trim($v['Name']));
				$C_MSG = explode(' ', trim($v['Message']));
				$Arr[] = array(
					"Reviews_Index"	     => $i,
					"PostId"		     => (!empty($C_Name[0]) && !empty($C_MSG[0]) ? $C_Name[0].$C_MSG[0] : ''),
					"UserName"           => !empty($v['Name']) ? $v['Name'] : '',
					"UserImage" 	     => !empty($v['Profile']) ? $v['Profile'] : TPGB_ASSETS_URL.'images/tpgb-placeholder.jpg',
					"Massage"            => $v['Message'],
					"CreatedTime"        => $v['Date'],
					"Icon" 	             => $Ricon,
					"rating"             => $v['Star'],
					"selectType"         => $Platform,
					"FilterCategory"     => !empty($RData['RCategory']) ? $RData['RCategory'] : '',
					"Logo"               => $logo,
					"RKey" 			     => "tp-repeater-item-$i",
				);
			}
        }
    }
   
    return $Arr;
}

function tpgbp_Beach_Reviews($attr){
	$block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
    $BType = (!empty($attr['BType'])) ? $attr['BType'] : '';
    $BToken = (!empty($attr['BToken'])) ? $attr['BToken'] : '';
    $BPPId = (!empty($attr['BPPId'])) ? $attr['BPPId'] : '';
    $FTitle = (!empty($attr['BTypeFacebook'])) ? $attr['BTypeFacebook'] : '';
    $GTitle = (!empty($attr['BTypeGoogle'])) ? $attr['BTypeGoogle'] : '';
	$BTimeFrq = !empty($attr['beach_TimeFrq']) ? $attr['beach_TimeFrq'] : '3600' ;
    $API = "";
    $Arr = [];

    if($BType == "b-facebook"){
        $API = "https://graph.facebook.com/v9.0/{$BPPId}?access_token={$BToken}&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(100),overall_star_rating,rating_count,username";
        $Type = $FTitle;
        $Logo = TPGB_ASSETS_URL.'assets/images/social-review/facebook.svg';
    }else if($BType == "b-google"){
        $API = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$BPPId}&key={$BToken}";
        $Type = $GTitle;
        $Logo = TPGB_ASSETS_URL.'assets/images/social-review/google.webp';
    }

	$Data=[];
	$BGetAPI = get_transient("Beach-Url-$block_id");
	$BGetTime = get_transient("Beach-Time-$block_id");
	if( $BGetAPI != $API || $BGetTime != $BTimeFrq ){
		$Data = tpgbp_Review_Api($API);
        $Data = json_encode($Data);
		set_transient("Beach-Url-$block_id", $API, $BTimeFrq);
		set_transient("Beach-Time-$block_id", $BTimeFrq, $BTimeFrq);
		set_transient("Beach-Data-$block_id", $Data, $BTimeFrq);
	}else{
		$Data = get_transient("Beach-Data-$block_id");
	}

    if(!is_array($Data)){
        $Data = json_decode($Data,true);
    }

    $B_status = (!empty($Data['HTTP_CODE']) ? $Data['HTTP_CODE'] : 400);
    $B_Error = (empty($Data['error_message']) ? "" : $Data['error_message']);
    if($B_status == 200 && empty($B_Error)){
        $Image = [];
        $totalCountt = 0;
        if($BType == "b-facebook"){
            $uname = (!empty($Data['username']) ? $Data['username'] : '');
            //$Rating = (!empty($Data['rating_count']) ? $Data['rating_count'] : 5);
			$Rating = !empty($Data['overall_star_rating']) ? $Data['overall_star_rating'] : '';
            $link = "https://www.facebook.com/$BPPId";
			
            $RatingImg = (!empty($Data['ratings']) && !empty($Data['ratings']['data']) ? $Data['ratings']['data'] : []);
            $totalCountt = count($Data);

            foreach ($RatingImg as $index => $Bdata){
                if($index > 3){ break; }
                $FB = (!empty($Bdata['reviewer']) ? $Bdata['reviewer'] : '');
                $Image[] = (!empty($FB['picture']) && !empty($FB['picture']['data']['url']) ? $FB['picture']['data']['url'] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg');
            }
        }
       
        if($BType == "b-google"){
            $totalCountt = (!empty($Data['result']['user_ratings_total']) ?$Data['result']['user_ratings_total'] : 0);
            $uname = (!empty($Data['result']['name']) ? $Data['result']['name'] : '');
            $Rating = (!empty($Data['result']['rating']) ? $Data['result']['rating'] : '');
            $link = "https://www.google.com/search?q=$uname";

            $GR = (!empty($Data['result']) ? $Data['result']['reviews'] : []);

            foreach ($GR as $index => $GI){
                
                if($index > 3){ break; }
                $Image[] = (!empty($GI['profile_photo_url']) ? $GI['profile_photo_url'] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg');
            }          


        }
       
        
        if( ($BType == "b-facebook" && !empty($Rating)) || $BType == "b-google" ){
			$Arr[] = array(
				"Total"         => $totalCountt,
				"Username" 	    => $uname,
				"UserImage" 	=> $Image,
				"UserLink"  	=> $link,
				"Type"          => $Type,
				"Logo"          => $Logo,
				"Rating" 	    => $Rating,
			);
		}else{
			$Arr[] = array(
				"Total" 		=> 0,
				"Type" 			=> 'Oops',     
				"Massage" 		=> "Error : Your facebook account doesn't provide overall ratings due to insufficient reviews on your page. ",
				"UserImage" 	=> array($Logo,$Logo,$Logo,$Logo),
				"ErrorClass"    => "danger-error",
				"Logo"          => $Logo,
			);
		}

    }else{
        $Error = (!empty($Data['error']) ? $Data['error'] : '');
        if($BType == "b-facebook"){
            $Etype = (!empty($Error['type']) ? $Error['type'] : '');
            if( !empty($Error['message']) ){
				$message = str_replace( ". ", "<br/>", $Error['message'] );
			}else if( !empty($Error['Message_Errorcurl']) ){
				$message = $Error['Message_Errorcurl'];
			}else{
				$message = 'Something Wrong';
			}
        }
        if($BType == "b-google"){
            $Etype = (!empty($Data['status']) ? $Data['status'] : '');
            $message = (!empty($Data['error_message']) ? str_replace(", ","<br/>",$Data['error_message']) : '');  
        }
        
        $Arr[] = array(
            "Total" 		=> $Etype,
            "Type" 			=> (!empty($Data['HTTP_CODE']) ? "Error No : ".$Data['HTTP_CODE'] : ''),     
            "Massage" 		=> $message,
            "UserImage" 	=> array($Logo,$Logo,$Logo,$Logo),
            "ErrorClass"    => "danger-error",
            "Logo"          => $Logo,
        );
    }
    
    return $Arr;
}

function tpgbp_Review_Time($datetime, $full = false) {
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

function tpgbp_Review_Api($API){
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


function tpgbp_Reviews_Category($count, $allreview,$arr){
	$category_filter = '';
	$TeamMemberR = (!empty($arr['Rreviews'])) ? $arr['Rreviews'] : [];  // repeater name
	
	$CategoryWF = !empty($arr['CategoryWF']) ? $arr['CategoryWF'] : false;	
	
	if(!empty($CategoryWF)){
		$filter_style = !empty($arr['CatFilterS']) ? $arr['CatFilterS'] : 'style-1';	
		$filter_hover_style = !empty($arr['FilterHs']) ? $arr['FilterHs'] : 'style-1';
		$all_filter_category = (!empty($arr["TextCat"])) ? $arr["TextCat"] : esc_html__('All','tpgbp');
		$loop_category = [];
		foreach ( $TeamMemberR as $TMFilter ) {
			$TMCategory = !empty($TMFilter['RCategory']) ? $TMFilter['RCategory'] : '';  // repeater category name
				if(!empty($TMCategory)){
					$loop_category[] = explode(',', $TMCategory);
				}
		}
		$loop_category = tpgbp_Reviews_Split_Array_Category($loop_category);
		$count_category = array_count_values($loop_category);
		$all_category=$category_post_count='';
		if($filter_style=='style-1'){
			$all_category='<span class="tpgb-category-count">'.esc_html($count).'</span>';
		}
		if($filter_style=='style-2' || $filter_style=='style-3'){
			$category_post_count='<span class="tpgb-category-count">'.esc_html($count).'</span>';
		}
		$category_filter .='<div class="tpgb-category-filter">';
			$category_filter .='<div class="tpgb-filter-data '.esc_attr($filter_style).'">';
			
			if($filter_style=='style-4'){
				$category_filter .= '<span class="tpgb-filters-link">'.esc_html__('Filters','tpgbp').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
			}
			
				$category_filter .='<div class="tpgb-categories '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list active all" data-filter="*" aria-label=".'.esc_attr($all_filter_category).'">'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></div>';

					foreach ( $loop_category as $i => $key ) {
						$slug = tpgbp_Reviews_Media_createSlug($key) ;		
						$category_post_count = '';
						if($filter_style == 'style-2' || $filter_style == 'style-3'){
							$CategoryCount=0;
							foreach ($allreview as $index => $value) {
								$CategoryName = !empty($value['FilterCategory']) ? $value['FilterCategory'] : '';
								$nCatName = explode(',', $CategoryName);
								if(in_array($key, $nCatName) && $index < $count){
									$CategoryCount++;
								}
							}
							$category_post_count = '<span class="tpgb-category-count">'.esc_html($CategoryCount).'</span>';
						}

						$category_filter .= '<div class="tpgb-filter-list">';
							$category_filter .= '<a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($slug).'" aria-label="'.esc_attr($key).'">';
								$category_filter .= $category_post_count;
								$category_filter .= '<span data-hover="'.esc_attr($key).'">';
									$category_filter .= esc_html($key);
								$category_filter .= '</span>';
							$category_filter .= '</a>';
						$category_filter .= '</div>';
					}
				$category_filter .= '</div>';
			$category_filter .= '</div>';
		$category_filter .= '</div>';
	}
	return $category_filter;
}
function tpgbp_Reviews_Split_Array_Category($array){
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array();
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, tpgbp_Reviews_Split_Array_Category($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  }
	}
	
	return $result; 
}
function tpgbp_Reviews_Media_createSlug($str, $delimiter = '-'){
	$slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	return $slug;
}
function tpgbp_Review_Error_array( $Data, $RKey, $Icon, $ReviewsType, $RCategory ){
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

function tpgbp_social_reviews() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
    $carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
    $globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	
	$sliderOpt = [
		'slideColumns' => [
			'type' => 'object',
			'default' => [ 'md' => '3','sm' => '3','xs' => '2' ],
		],
	];
	$carousel_options = array_merge($carousel_options,$sliderOpt);

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
        'Bstyle' => [
            'type'=> 'string',
            'default'=> 'style-1',
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
					'GLanguage' => [
                        'type' => 'string',
                        'default' =>'en',	
                    ],
                    'FbPageId' => [
                        'type'=> 'string',
                        'default'=> '',
                    ],
                    'FbRType' => [
                        'type' => 'string',
                        'default' => 'default',	
                    ],
                    'GPlaceID' => [
                        'type'=> 'string',
                        'default'=> '',
                    ],
                    'CUImg' => [
                        'type' => 'array',
                        'default' => [
                            [ 
                                'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
                                'Id' => '',
                            ],
                        ],
                    ],
                    'Cuname' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],

                    'Cmassage' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'CPFname' => [
                        'type' => 'string',
                        'default' =>'facebook',	
                    ],
                    'CcuSname' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'CImg' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'Cdate' => [
                        'type' => 'string',
                        'default' =>'',	
                    ],
                    'Cstar' => [
                        'type' => 'string',
                        'default' =>'',	
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
                    'GLanguage' => 'en',
                    'MaxR' => 6,
                    'CUImg'=> [ (object)[ 'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg' ] ], 
                    'FbRType' => 'default',
                ],
            ],
        ],

        'BType' => [
            'type'=> 'string',
            'default'=> 'b-facebook',
        ],
		'BTypeFacebook' => [
            'type'=> 'string',
            'default'=> 'Facebook Review',
        ],
		'BTypeGoogle' => [
            'type'=> 'string',
            'default'=> 'Google Review',
        ],
        'BToken' => [
            'type' => 'string',
            'default' =>'',	
        ],
        'BPPId' => [
            'type' => 'string',
            'default' => '',	
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
                    'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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

        'Btxt1' => [
            'type'=> 'string',
            'default'=> 'Recommended by',
        ],
        'Btxt2' => [
            'type'=> 'string',
            'default'=> 'people',
        ],
		
        'BRecommend' => [
            'type' => 'boolean',
            'default' => true,	
        ],
        'BSButton' => [
            'type' => 'boolean',
            'default' => true,	
        ],
        'Blinktxt' => [
            'type'=> 'string',
            'default'=> 'Would you recommend ',
        ],
        'BBtnName' => [
            'type'=> 'string',
            'default'=> 'YES',
        ],
        'BBtnTName' => [
            'type'=> 'string',
            'default'=> 'NO',
        ],
        'IconHidden' => [
            'type' => 'boolean',
            'default' => true,	
        ],
		 'beach_TimeFrq' => [
            'type'=> 'string',
            'default'=> '3600',
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

        'GnameTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-username a',
                ],
            ],
			'scopy' => true,
        ],
        'GMsgTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-content',
                ],
            ],
			'scopy' => true,
        ],
        'GPostOnTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-logotext',
                ],
            ],
			'scopy' => true,
        ],
        'GTimeTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-time',
                ],
            ],
			'scopy' => true,
        ],
        'GNnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-username a{color:{{GNnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GNMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-content{color:{{GNMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GNTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-time{color:{{GNTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GNPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-logotext{color:{{GNPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .tpgb-review:hover .tpgb-sr-username a{color:{{GHnameCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .review-s3-wrap:hover .tpgb-sr-username a{color:{{GHnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .tpgb-review:hover .tpgb-sr-content{color:{{GHMassageCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .review-s3-wrap:hover .tpgb-sr-content{color:{{GHMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                     'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .tpgb-review:hover .tpgb-sr-time{color:{{GHTimeCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .google .review-s3-wrap:hover .tpgb-sr-time{color:{{GHTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .google .tpgb-review:hover .tpgb-sr-logotext{color:{{GHPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GNBpadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review{padding:{{GNBpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GNBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'GNBr' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'GNBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'GNRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review{border-radius:{{GNRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHBpadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review:hover{padding:{{GHBpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'GHBr' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'GHRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review:hover{border-radius:{{GHRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'GPRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .google .tpgb-sr-profile{border-radius:{{GPRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GHpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .grid-item.google .tpgb-sr-header{padding:{{GHpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GBpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .grid-item.google .tpgb-sr-content{padding:{{GBpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'GFpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .grid-item.google .tpgb-sr-bottom{padding:{{GFpd}};}',
                ],
            ],
			'scopy' => true,
        ],

        'CnameTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-username a',
                ],
            ],
			'scopy' => true,
        ],
        'CMsgTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-content',
                ],
            ],
			'scopy' => true,
        ],
        'CTimeTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-time',
                ],
            ],
			'scopy' => true,
        ],
        'CPostOnTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-logotext',
                ],
            ],
			'scopy' => true,
        ],
        'CnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-username a{color:{{CnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                    (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-content{color:{{CMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-time{color:{{CTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-logotext{color:{{CPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
		
        'CHnameCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-sr-username a{color:{{CHnameCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .custom .review-s3-wrap:hover .tpgb-sr-username a{color:{{CHnameCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CHMassageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-sr-content{color:{{CHMassageCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .custom .review-s3-wrap:hover .tpgb-sr-content{color:{{CHMassageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CHTimeCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                     'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '!=', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-sr-time{color:{{CHTimeCr}};}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .custom .review-s3-wrap:hover .tpgb-sr-time{color:{{CHTimeCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CHPostONCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-sr-logotext{color:{{CHPostONCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusNBpadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review{padding:{{CusNBpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CNBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CBBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-header',
                ],
            ],
			'scopy' => true,
        ],
        'CusNBr' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CusNCRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review{border-radius:{{CusNCRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusNBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CusHBpadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-review{padding:{{CusHBpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CHBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CHBBg' => [
            'type' => 'object',
            'default' => (object) [ 'openBg'=> 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ],
                                    (object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-sr-header',
                ],
            ],
			'scopy' => true,
        ],
        'CusHBr' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CusHCRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-review{border-radius:{{CusHCRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusHBs' => [
            'type' => 'object',
            'default' => (object) [ 'openShadow' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'review' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-review:hover .tpgb-review',
                ],
            ],
			'scopy' => true,
        ],
        'CusPRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-profile{border-radius:{{CusPRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusHpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-header{padding:{{CusHpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusBpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-content{padding:{{CusBpd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CusFpd' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .custom .tpgb-sr-bottom{padding:{{CusFpd}};}',
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

        // Beach
        'BDyIcon' => [
            'type'=> 'string',
            'default'=> 'fas fa-star',
        ],
        'Bboxwidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => '%',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-review{width:{{Bboxwidth}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AvrageTxtCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-number span{color:{{AvrageTxtCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AvrageCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-number span{background:{{AvrageCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AvragePadding' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-number span{padding:{{AvragePadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review .tpgb-batch-user',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-total',
                ],
            ],
			'scopy' => true,
        ],
        'BRbyCr' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review .tpgb-batch-total',
                ],
            ],
			'scopy' => true,
        ],
        'TCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review .tpgb-batch-user{color:{{TCr}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-total{color:{{TCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TRbyCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review .tpgb-batch-total{color:{{TRbyCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Imgsize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-1 .tpgb-batch-img{width:{{Imgsize}}; height:{{Imgsize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'ImgBorder' => [
            'type' => 'object',
            'default' => (object) ['openBorder' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-1 .tpgb-batch-img',
                ],
            ],
			'scopy' => true,
        ],
        'ImgBS' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-1 .tpgb-batch-img',
                ],
            ],
			'scopy' => true,
        ],
        'BSISize' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-sr-logo{margin:{{BSISize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BSITopB' => [
            'type' => 'object',
            'default' => [ 
                'md' => "",
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-sr-logo{top:{{BSITopB}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BstarBgCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-batch-start{background:{{BstarBgCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BstarBr' => [
            'type' => 'object',
            'default' => (object) ['openBorder' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-batch-start',
                ],
            ],
			'scopy' => true,
        ],
        'BstarRsBr' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-batch-start{border-radius:{{BstarRsBr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BiconPadd' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .social-rb-style-2 .tpgb-batch-start{padding:{{BiconPadd}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BstarCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-start{color:{{BstarCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BstarIsize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'px',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-start{font-size:{{BstarIsize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BstarIwidth' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
                "unit" => 'em',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .b-star{width:{{BstarIwidth}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BTCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review{background-color:{{BTCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TBr' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBorder' => 0
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-top',
                ],
            ],
			'scopy' => true,
        ],
        'TBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review{border-radius:{{TBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BTHCr' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover',
                ],
            ],
			'scopy' => true,
        ],
        'THBr' => [
            'type' => 'object',
            'default' => (object) [ 
                'openBorder' => 0
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-3' ]],
                    'selector' => '{{PLUS_WRAP}} .social-rb-style-3 .tpgb-batch-top:hover',
                ],
            ],
			'scopy' => true,
        ],
        'THBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review:hover{border-radius:{{THBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
		
		'recMAlign' => [
            'type' => 'string',
            'default' =>  'center',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach'], (object) ['key' => 'recMAlign', 'relation' => '==', 'value' => 'left'], (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{justify-content: flex-start;}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach'], (object) ['key' => 'recMAlign', 'relation' => '==', 'value' => 'center'], (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{justify-content: center;}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach'], (object) ['key' => 'recMAlign', 'relation' => '==', 'value' => 'right'], (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{justify-content: flex-end;}',
                ],
				(object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach'], (object) ['key' => 'recMAlign', 'relation' => '==', 'value' => 'justify'], (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{justify-content: space-between;}',
                ],
            ],
			'scopy' => true,
        ],
        'RBTypo' => [
            'type'=> 'object',
            'default'=> (object) [ 'openTypography' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend .tpgb-batch-recommend-text',
                ],
            ],
			'scopy' => true,
        ],
        'RTCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{color:{{RTCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'RBCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{background-color:{{RBCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'RBr' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend',
                ],
            ],
			'scopy' => true,
        ],
        'RRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-batch-recommend{border-radius:{{RRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
									(object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-yes{color:{{BtnOCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOtypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-yes',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOBg' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-yes{background-color:{{BtnOBg}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOB' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-yes',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-yes{border-radius:{{BtnOBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnOMargin' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-recommend a.batch-btn-yes{margin:{{BtnOMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        
        'BtnTtypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-no',
                ],
            ],
			'scopy' => true,
        ],
        'BtnTCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
               (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-no{color:{{BtnTCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnTBg' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-no{background-color:{{BtnTBg}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnTB' => [
            'type' => 'object',
            'default' => (object) [ 'openBorder' => 0 ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-no',
                ],
            ],
			'scopy' => true,
        ],
        'BtnTBRs' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .batch-btn-no{border-radius:{{BtnTBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'BtnTMargin' => [
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
                    'condition' => [(object) ['key' => 'RType', 'relation' => '==', 'value' => 'beach' ],
                                    (object) ['key' => 'Bstyle', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-social-reviews .tpgb-batch-recommend .batch-btn-no{margin:{{BtnTMargin}};}',
                ],
            ],
			'scopy' => true,
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

        'Postdisplay' => [
            'type' => 'string',
            'default' => 6,
        ],
        'postLodop' => [
            'type' => 'string',
            'default' => 'none',
        ],
        'pagitypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-pagination a,{{PLUS_WRAP}} .tpgb-pagination span',
                ],
            ],
			'scopy' => true,
        ],
        'postview' => [
            'type'=> 'string',
            'default'=> 1,
        ],
        'loadbtnText' => [
            'type' => 'string',
            'default' => 'Load More',
        ],
        'loadingtxt' => [
            'type' => 'string',
            'default' => 'Loading...',
        ],
        'allposttext' => [
            'type' => 'string',
            'default' => 'All Done',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count){padding:{{InPadding}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
                                    (object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{padding:{{InPadding}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-3'],
                                    (object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-3 .tpgb-filter-list a{padding:{{InPadding}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
                                    (object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],	
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list{margin:{{FCMargin}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a.active::after,{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a:hover::after{background:{{FCHBcr}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:hover,{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:focus,{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a.active,{{PLUS_WRAP}} .tpgb-category-filter .hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{color:{{FCHcr}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',

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
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
                                    (object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
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
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
                                    (object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
                                    (object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
                                    (object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count){border-radius:{{FCBgRs}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'CatFilterS', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
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
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'CategoryWF', 'relation' => '==', 'value' => true],
									(object) ['key' => 'CatFilterS', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
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
                    'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
                ],
            ],
			'scopy' => true,
        ],

        'pagitypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-pagination a,{{PLUS_WRAP}} .tpgb-pagination span',
                ],
            ],
			'scopy' => true,
        ],
        'pagiColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-pagination a,{{PLUS_WRAP}} .tpgb-pagination span{color : {{pagiColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'pagihvrColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-pagination a:hover,{{PLUS_WRAP}} .tpgb-pagination a:focus,{{PLUS_WRAP}} .tpgb-pagination span.current{color : {{pagihvrColor}}; border-bottom-color: {{pagihvrColor}} }',
                ],
            ],
			'scopy' => true,
        ],
        'btnTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more',
                ],
            ],
			'scopy' => true,
        ],
        'btncolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more{color : {{btncolor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'btnBgtype' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more',
                ],
            ],
			'scopy' => true,
        ],
        'btnBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more',
                ],
            ],
			'scopy' => true,
        ],
        'btnBradius' => [
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
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more{border-radius : {{btnBradius}} }',
                ],
            ],
			'scopy' => true,
        ],
        'btnhvrcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more:hover{color : {{btnhvrcolor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'btnHvrBgtype' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more:hover',
                ],
            ],
			'scopy' => true,
        ],
        'btnhvrBorder' => [
            'type' => 'object',
            'default' => (object) [
                'openBorder' => 0,	
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more:hover',
                ],
            ],
			'scopy' => true,
        ],
        'btnhvrBradius' => [
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
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .review-load-more:hover{border-radius : {{btnhvrBradius}} }',
                ],
            ],
			'scopy' => true,
        ],
        'allTypo' => [
            'type'=> 'object',
            'default'=> (object) [
                'openTypography' => 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .tpgb-review-loaded',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-lazy-load .tpgb-review-loaded',
                ],
            ],
			'scopy' => true,
        ],
        'allcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-load-more .tpgb-review-loaded{color : {{allcolor}}; }',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-lazy-load .tpgb-review-loaded{color : {{allcolor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'spinSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-lazy-load .tpgb-spin-ring div{ width: {{spinSize}}px; height:{{spinSize}}px; }',
                ],
            ],
			'scopy' => true,
        ],
        'spinBSize' => [
            'type' => 'object',
            'default' => [ 
                'md' => '',
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-lazy-load .tpgb-spin-ring div{ border-width: {{spinBSize}}px; }',
                ],
            ],
			'scopy' => true,
        ],
        'spinColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-review-lazy-load .tpgb-spin-ring div{ border-color: {{spinColor}} transparent transparent transparent ; }',
                ],
            ],
			'scopy' => true,
        ],


    ];

    $attributesOptions = array_merge($attributesOptions,$carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);

    register_block_type( 'tpgb/tp-social-reviews', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgbp_social_reviews_callback'
    ));
}
add_action( 'init', 'tpgbp_social_reviews' );