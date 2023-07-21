<?php
/**
 * Block : Social Embed
 * @since 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_social_embed_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$embedType = (!empty($attributes['embedType'])) ? $attributes['embedType'] : 'facebook';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	$output .= '<div class="tpgb-block-'.esc_attr($block_id).' tpgb-social-embed '.esc_attr($blockClass).'">';

		if($embedType == 'vimeo' || $embedType == 'youtube'){
			$exWidth = (!empty($attributes['exWidth']) ) ? $attributes['exWidth'] : 640;
			$exHeight = (!empty($attributes['exHeight']) ) ? $attributes['exHeight'] : 360;
		}

		if( $embedType == 'facebook' ){
			$type = (!empty($attributes['type'])) ? $attributes['type'] : '';
			$sizeBtn = (!empty($attributes['sizeLB'])) ? $attributes['sizeLB'] : '';

			if( $type == 'comments' ){
				$fbCommentAdd = (!empty($attributes['commentAddURL']) && !empty($attributes['commentAddURL']['url']) ) ? $attributes['commentAddURL']['url'] : '';				
				$targetC = (!empty($attributes['targetC'])) ? $attributes['targetC'] : 'custom';

				if( $targetC == 'currentpage' ){
					$urlFC = (!empty($attributes['urlFC'])) ? $attributes['urlFC'] : 'plain';
					$post_id = get_the_ID();

					if( $urlFC == 'plain' ){
						$PlainURL = get_permalink( $post_id );
						$output .= '<div class="fb-comments tpgb-fb-iframe" data-href="'.esc_url($PlainURL).'" data-width="" data-numposts="'.esc_attr($attributes['countC']).'" data-order-by="'.esc_attr($attributes['orderByC']).'" ></div>';
					}else if( $urlFC == 'pretty' ){
						$PrettyURL = add_query_arg('p', $post_id, home_url());						
						$output .= '<div class="fb-comments tpgb-fb-iframe" data-href="'.esc_url($PrettyURL).'" data-width="" data-numposts="'.esc_attr($attributes['countC']).'" data-order-by="'.esc_attr($attributes['orderByC']).'" ></div>';
					}

				}else{
					$output .= '<div class="fb-comments tpgb-fb-iframe" data-href="'.esc_url($fbCommentAdd).'" data-width="" data-numposts="'.esc_attr($attributes['countC']).'" data-order-by="'.esc_attr($attributes['orderByC']).'" ></div>';
				}
				$output .= '<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>';
			}
			if( $type == 'posts' ){
				$postURL = (!empty($attributes['postURL']) && !empty($attributes['postURL']['url']) ) ? $attributes['postURL']['url'] : '';			
				$wdPost = (!empty($attributes['wdPost'])) ? $attributes['wdPost'] : 500;
				$hgPost = (!empty($attributes['hgPost'])) ? $attributes['hgPost'] : 560;
				$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Facebook Embed','tpgb');

				$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/post.php?href='.esc_url($postURL).'&show_text='.esc_attr($attributes['fullPT']).'&width='.esc_attr($wdPost).'&height='.esc_attr($hgPost).'&appId=" width="'.esc_attr($wdPost).'" height="'.esc_attr($hgPost).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
			}
			if( $type == 'videos' ){
				$videosURL = (!empty($attributes['videosURL']) && !empty($attributes['videosURL']['url']) ) ? $attributes['videosURL']['url'] : '';
				$FullVideo = (!empty($attributes['fullVT'])) ? 'allowFullScreen="'.esc_attr($attributes['wdVideo'].'"') : '';
				$wdVideo = (!empty($attributes['wdVideo'])) ? $attributes['wdVideo'] : 500;
				$hgVideo = (!empty($attributes['hgVideo'])) ? $attributes['hgVideo'] : 560;
				$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Facebook Embed','tpgb');

				$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/video.php?href='.esc_url($videosURL).'&show_text='.esc_attr($attributes['captionVT']).'&width='.esc_attr($wdVideo).'&height='.esc_attr($hgVideo).'&autoplay='.esc_attr($attributes['autoplayVT']).'&appId=" width="'.esc_attr($wdVideo).'" height="'.esc_attr($hgVideo).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" '.$FullVideo.' title="'.$iframeTitle.'"></iframe>';
			}
			if( $type == 'likebutton' ){
				$FBLikeBtn = (!empty($attributes['likeBtnUrl']) && !empty($attributes['likeBtnUrl']['url']) ) ? $attributes['likeBtnUrl']['url'] : '';			
				$facesLBT = (!empty($attributes['facesLBT'])) ? $attributes['facesLBT'] : false;
				$FBHgLike = (!empty($attributes['hgLikeBtn'])) ? $attributes['hgLikeBtn'] : 30;
				$FBwdLike = (!empty($attributes['wdLikeBtn'])) ? $attributes['wdLikeBtn'] : 350; 
				$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Facebook Embed','tpgb');

				if( $attributes['targetLike'] == 'currentpage' ){
					$fmtURLlb = (!empty($attributes['fmtURLlb'])) ? $attributes['fmtURLlb'] : 'plain';
					$post_id = get_the_ID();
					if( $fmtURLlb == 'plain' ){
						$PlainLURL = get_permalink( $post_id );
						$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/like.php?href='.esc_url($PlainLURL).'&layout='.esc_attr($attributes['btnStyleLB']).'&action='.esc_attr($attributes['typeLB']).'&size='.esc_attr($sizeBtn).'&share='.esc_attr($attributes['sBtnLB']).'&height='.esc_attr($FBHgLike).'&show_faces='.esc_attr($facesLBT).'&colorscheme='.esc_attr($attributes['colorSLB']).'&width='.esc_attr($FBwdLike).'&appId=" width="'.esc_attr($FBwdLike).'" height="'.esc_attr($FBHgLike).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
					}else if( $fmtURLlb == 'pretty' ){
						$PrettyLURL = add_query_arg('p', $post_id, home_url());						
						$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/like.php?href='.esc_url($PrettyLURL).'&layout='.esc_attr($attributes['btnStyleLB']).'&action='.esc_attr($attributes['typeLB']).'&size='.esc_attr($sizeBtn).'&share='.esc_attr($attributes['sBtnLB']).'&height='.esc_attr($FBHgLike).'&show_faces='.esc_attr($facesLBT).'&colorscheme='.esc_attr($attributes['colorSLB']).'&width='.esc_attr($FBwdLike).'&appId=" width="'.esc_attr($FBwdLike).'" height="'.esc_attr($FBHgLike).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
					}
				}else{
					$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/like.php?href='.esc_url($FBLikeBtn).'&layout='.esc_attr($attributes['btnStyleLB']).'&action='.esc_attr($attributes['typeLB']).'&size='.esc_attr($sizeBtn).'&share='.esc_attr($attributes['sBtnLB']).'&height='.esc_attr($FBHgLike).'&show_faces='.esc_attr($facesLBT).'&colorscheme='.esc_attr($attributes['colorSLB']).'&width='.esc_attr($FBwdLike).'&appId=" width="'.esc_attr($FBwdLike).'" height="'.esc_attr($FBHgLike).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
				}
			}
			if( $type == 'page' ){
				$uRLP = (!empty($attributes['uRLP']) && !empty($attributes['uRLP']['url']) ) ? $attributes['uRLP']['url'] : '';			
				$wdPage = (!empty($attributes['wdPage'])) ? $attributes['wdPage'] : 340;
				$hgPage = (!empty($attributes['hgPage'])) ? $attributes['hgPage'] : 500;
				$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Facebook Embed','tpgb');			
				
				$output .= '<iframe class="tpgb-fb-iframe" src="https://www.facebook.com/plugins/page.php?href='.esc_url($uRLP).'&tabs='.esc_attr($attributes['layoutP']).'&width='.esc_attr($wdPage).'&height='.esc_attr($hgPage).'&small_header='.esc_attr($attributes['smallHP']).'&hide_cover='.esc_attr($attributes['coverP']).'&show_facepile='.esc_attr($attributes['profileP']).'&hide_cta='.esc_attr($attributes['ctaBtn']).'&lazy=true&adapt_container_width=true&appId=" width="'.esc_attr($wdPage).'" height="'.esc_attr($hgPage).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
			}
			if( $type == 'save' ){
				$saveURL = (!empty($attributes['saveURL']) && !empty($attributes['saveURL']['url']) ) ? $attributes['saveURL']['url'] : '';
							
				$output .= '<div class="fb-save" data-uri="'.esc_url($saveURL).'" data-size="'.esc_attr($sizeBtn).'"></div>';
				$output .= '<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>';
			}
			if( $type == 'share' ){
				$shareURL = (!empty($attributes['shareURL']) && !empty($attributes['shareURL']['url']) ) ? $attributes['shareURL']['url'] : '';
				$shareW = (!empty($attributes['wdShare']) && !empty($attributes['wdShare']) ) ? $attributes['wdShare'] : 100;
				$shareH = (!empty($attributes['hgShare']) && !empty($attributes['hgShare']) ) ? $attributes['hgShare'] : 40;
				$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Facebook Share','tpgb');
				
				$output .= '<iframe src="https://www.facebook.com/plugins/share_button.php?href='.esc_url($shareURL).'&layout='.esc_attr($attributes['shareBtn']).'&size='.esc_attr($sizeBtn).'&width='.esc_attr($shareW).'&height='.esc_attr($shareH).'&appId=" width="'.esc_attr($shareW).'" height="'.esc_attr($shareH).'" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" title="'.$iframeTitle.'"></iframe>';
			}
		}else if( $embedType == 'twitter' ){
			$tweetType = (!empty($attributes['tweetType'])) ? $attributes['tweetType'] : 'timelines';
			$twname = (!empty($attributes['twname'])) ? $attributes['twname'] : 'twitter';
			$twColor = (!empty($attributes['twColor'])) ? 'dark' : 'light';
			$twwidth = (!empty($attributes['twwidth'])) ? $attributes['twwidth'] : '';
			$twconver = (!empty($attributes['twconver'])) ? 'none' : '';
			$twMsg = (!empty($attributes['twMsg'])) ? $attributes['twMsg'] : '';

			if( $tweetType == 'tweets' ){
				$twRepeater = (!empty($attributes['twRepeater'])) ? $attributes['twRepeater'] : [];
				$twCards = (!empty($attributes['twCards'])) ? 'hidden' : '';
				$twAlign = (!empty($attributes['twalign'])) ? $attributes['twalign'] : 'center';
				
				foreach ( $twRepeater as $index => $tweet ) {
					$twURl = (!empty($tweet['tweetURl']) && !empty($tweet['tweetURl']['url'])) ? $tweet['tweetURl']['url'] : '';
					$twMassage = ( !empty($tweet['twMassage']) ? $tweet['twMassage'] : '');

					$output .= '<blockquote class="twitter-tweet" data-theme="'.esc_attr($twColor).'" data-width="'.esc_attr($twwidth).'" data-cards="'.esc_attr($twCards).'" data-align="'.esc_attr($twAlign).'" data-conversation="'.esc_attr($twconver).'" >';
						$output .= '<p lang="en" dir="ltr">'.wp_kses_post($twMassage).'</p>';
						$output .= '<a href="'.esc_attr($twURl).'"></a>';
					$output .= '</blockquote>';
				}
			}
			if( $tweetType == 'timelines' ){
				$twURl = '';
				$twclass = 'twitter-timeline';
				$twGuides = (!empty($attributes['twGuides'])) ? $attributes['twGuides'] : 'profile';
				$twBrCr = (!empty($attributes['twBrCr'])) ? $attributes['twBrCr'] : '';
				$twlimit = (!empty($attributes['twlimit'])) ? $attributes['twlimit'] : '';
				$twstyle = (!empty($attributes['twstyle'])) ? $attributes['twstyle'] : 'linear';
				$twDesign = (!empty($attributes['twDesign'])) ? json_decode($attributes['twDesign']) : [];
				$twheight = ( $twstyle == 'linear' ) ? $attributes['twheight'] : '';

				$DesignBTN = array();
				if (is_array($twDesign) || is_object($twDesign)) {
					foreach ($twDesign as $value) {
						$DesignBTN[] = $value->value;
					}
				}
				$twDesign = json_encode($DesignBTN);

				if($twGuides == 'profile'){
					$twURl = 'https://twitter.com/'.esc_attr($twname);
				}else if($twGuides == 'list'){
					$twURl = (!empty($attributes['twlisturl']) && !empty($attributes['twlisturl']['url'])) ? $attributes['twlisturl']['url'] : '';
				}else if($twGuides == 'likes'){
					$twURl = 'https://twitter.com/'.esc_attr($twname).'/likes';
				}else if($twGuides == 'collection'){
					$twclass = 'twitter-grid';
					$twURl = (!empty($attributes['twCollection']) && !empty($attributes['twCollection']['url'])) ? $attributes['twCollection']['url'] : '';
				}
				$output .= '<a class="'.esc_attr($twclass).'" href="'.esc_url($twURl).'" data-width="'.esc_attr($twwidth).'" data-height="'.esc_attr($twheight).'" data-theme="'.esc_attr($twColor).'" data-chrome="'.esc_attr($twDesign).'" data-border-color="'.esc_attr($twBrCr).'" data-tweet-limit="'.esc_attr($twlimit).'" data-aria-polite="" >'.wp_kses_post($twMsg).'</a>';
			}
			if( $tweetType == 'buttons' ){
				$twbutton = (!empty($attributes['twbutton'])) ? $attributes['twbutton'] : 'follow';
				$twBtnSize = (!empty($attributes['twBtnSize'])) ? $attributes['twBtnSize'] : '';
				$twTweetId = (!empty($attributes['twTweetId'])) ? $attributes['twTweetId'] : '';
				$twicon = (!empty($attributes['twIcon'])) ? '' : '<i class="fab fa-twitter"></i>';
				
				if( $twbutton == 'tweets' ){
					$twVia = (!empty($attributes['twVia'])) ? $attributes['twVia'] : '';
					$twTextBtn = (!empty($attributes['twTextBtn'])) ? $attributes['twTextBtn'] : '';
					$twHashtags = (!empty($attributes['twHashtags'])) ? $attributes['twHashtags'] : '';
					$twTweetUrl = (!empty($attributes['twTweetUrl']) && !empty($attributes['twTweetUrl']['url'])) ? $attributes['twTweetUrl']['url'] : '';

					$output .= '<a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-size="'.esc_attr($twBtnSize).'" data-text="'.esc_attr($twTextBtn).'" data-url="'.esc_url($twTweetUrl).'" data-via="'.esc_attr($twVia).'" data-hashtags="'.esc_attr($twHashtags).'" >'.wp_kses_post($twMsg).'</a></br>';

				}else if( $twbutton == 'follow' ){
					$twCount = (!empty($attributes['twCount'])) ? $attributes['twCount'] : 'false';
					$twHideUname = (!empty($attributes['twHideUname'])) ? 'false' : $attributes['twHideUname'];
					
					$output .= '<a class="twitter-follow-button" href="https://twitter.com/'.esc_attr($twname).'" data-size="'.esc_attr($twBtnSize).'" data-show-screen-name="'.esc_attr($twHideUname).'" data-show-count="'.esc_attr($twCount).'" >'.wp_kses_post($twMsg).'</a></br>';

				}else if( $twbutton == 'message' ){
					$twRId = (!empty($attributes['twRId'])) ? $attributes['twRId'] : '';
					$twMessage = (!empty($attributes['twMessage'])) ? $attributes['twMessage'] : '';
					$twHideUname = (!empty($attributes['twHideUname'])) ? '@' : '';

					$output .= '<a class="twitter-dm-button" href="https://twitter.com/messages/compose?recipient_id='.esc_attr($twRId).'" data-text="'.esc_attr($twMessage).'" data-size="'.esc_attr($twBtnSize).'" data-screen-name="'.esc_attr($twHideUname.$twname).'">'.wp_kses_post($twMsg).'</a>';
				}else if( $twbutton == 'like' ){
					$output .= '<a class="tw-button" href="https://twitter.com/intent/like?tweet_id='.esc_attr($twTweetId).'" >'.wp_kses_post($twicon.' '.$attributes['likeBtn']).'</a>';
				}else if( $twbutton == 'reply' ){
					$output .= '<a class="tw-button" href="https://twitter.com/intent/tweet?in_reply_to='.esc_attr($twTweetId).'">'.wp_kses_post($twicon.' '.$attributes['replyBtn']).'</a>';
				}else if( $twbutton == 'reTweet' ){
					$output .= '<a class="tw-button" href="https://twitter.com/intent/retweet?tweet_id='.esc_attr($twTweetId).'">'.wp_kses_post($twicon.' '.$attributes['reTweetBtn']).'</a>';
				}
			}
			
			$output .= '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
		}else if( $embedType == 'vimeo' ){
			$VmId = (!empty($attributes['viId']) ) ? $attributes['viId'] : '';
			$vmStime = (!empty($attributes['vmStime']) ) ? $attributes['vmStime'] : '';
			$vmColor = (!empty($attributes['vmColor']) ) ? ltrim($attributes['vmColor'], '#') : 'ffffff';
			$VmSelect = json_decode( $attributes['viOption'],true );
			
			$VmALL = [];
			foreach ($VmSelect as $v) {
				$VmALL[] = $v['value'];
			}

			$Vm_FullScreen = ((in_array('fullscreen', $VmALL)) ? 'webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen="true"' : '');
			$Vm_AutoPlay = (in_array('autoplay', $VmALL)) ? 1 : 0;
			$Vm_loop = (in_array('loop', $VmALL)) ? 1 : 0;
			$Vm_Muted = (in_array('muted', $VmALL)) ? 1 : 0;
			$Vm_AutoPause = (in_array('autopause', $VmALL)) ? 1 : 0;
			$Vm_BackGround = (in_array('background', $VmALL)) ? 1 : 0;
			$Vm_Byline = (in_array('byline', $VmALL)) ? 1 : 0;
			$Vm_Speed = (in_array('speed', $VmALL)) ? 1 : 0;
			$Vm_Title = (in_array('title', $VmALL)) ? 1 : 0;
			$Vm_Portrait = (in_array('portrait', $VmALL)) ? 1 : 0;
			$Vm_PlaySinline = (in_array('playsinline', $VmALL)) ? 1 : 0;
			$Vm_Dnt = (in_array('dnt', $VmALL)) ? 1 : 0;
			$Vm_PiP = (in_array('pip', $VmALL)) ? 1 : 0;
			$Vm_transparent = (in_array('transparent', $VmALL)) ? 1 : 0;
			$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Vimeo','tpgb');
	
			$output .= '<iframe class="tpgb-social-vimeo" src="https://player.vimeo.com/video/'.esc_attr($VmId).'?html5=1&amp;title='.esc_attr($Vm_Title).'&amp;byline='.esc_attr($Vm_Byline).'&amp;portrait='.$Vm_Portrait.'&amp;autoplay='.esc_attr($Vm_AutoPlay).'&amp;loop='.esc_attr($Vm_loop).'&amp;muted='.esc_attr($Vm_Muted).'&amp;autopause='.esc_attr($Vm_AutoPause).'&amp;background='.esc_attr($Vm_BackGround).'&amp;playsinline='.esc_attr($Vm_PlaySinline).'&amp;speed='.esc_attr($Vm_Speed).'&amp;dnt='.esc_attr($Vm_Dnt).'&amp;pip='.esc_attr($Vm_PiP).'&amp;transparent='.esc_attr($Vm_transparent).'&amp;color='.esc_attr($vmColor).'&amp;#t='.esc_attr($vmStime).'" width="'.esc_attr($exWidth).'" height="'.esc_attr($exHeight).'" frameborder="0" '.esc_attr($Vm_FullScreen).' title="'.$iframeTitle.'"></iframe>';
			
		}else if( $embedType == 'instagram' ){
			$iGType = (!empty($attributes['iGType']) ) ? $attributes['iGType'] : 'posts';
			$iGId = (!empty($attributes['iGId']) ) ? $attributes['iGId'] : 'CGAvnLcA3zb';
			$IGCap = (empty($attributes['iGCaptione']) ) ? 'data-instgrm-captioned' : '';

			if($iGType == "posts"){
				$ig_id = 'p/'.$iGId;
			}else if($iGType == "reels"){
				$ig_id = 'reel/'.$iGId;
			}else if($iGType == "igtv"){
				$ig_id = 'tv/'.$iGId;
			}

			$output .= '<blockquote class="instagram-media" data-instgrm-version="13" data-instgrm-permalink="https://www.instagram.com/'.esc_attr($ig_id).'/?utm_source=ig_embed" '.esc_attr($IGCap).'></blockquote><script async src="//www.instagram.com/embed.js"></script>';

		}else if( $embedType == 'youtube' ){
			$ytType = (!empty($attributes['ytType']) ) ? $attributes['ytType'] : 'ytSV';
			$ytOption = json_decode( $attributes['ytOption'],true );
			$ytSTime = (!empty($attributes['ytSTime']) ) ? $attributes['ytSTime'] : '';
			$ytETime = (!empty($attributes['ytETime']) ) ? $attributes['ytETime'] : '';	
			$ytlanguage = (!empty($attributes['ytlanguage']) ) ? $attributes['ytlanguage'] : '';	

			$ytSelect = [];
			foreach ($ytOption as $v) {
				$ytSelect[] = !empty($v['value']) ? $v['value'] : '';
			}

			$yt_loop = (in_array('loop', $ytSelect)) ? 1 : 0;
			$yt_fs = (in_array('fs', $ytSelect)) ? 1 : 0;
			$yt_autoplay = (in_array('autoplay', $ytSelect)) ? 1 : 0;
			$Yt_muted = (in_array('mute', $ytSelect)) ? 1 : 0;
			$yt_controls = (in_array('controls', $ytSelect)) ? 1 : 0;
			$yt_disablekb = (in_array('disablekb', $ytSelect)) ? 1 : 0;
			$yt_modestbranding = (in_array('modestbranding', $ytSelect)) ? 1 : 0;
			$yt_playsinline = (in_array('playsinline', $ytSelect)) ? 1 : 0;
			$yt_rel = (in_array('rel', $ytSelect)) ? 1 : 0;
			$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Social Youtube','tpgb');

			$YT_Parameters = 'autoplay='.esc_attr($yt_autoplay).'&mute='.esc_attr($Yt_muted).'&controls='.esc_attr($yt_controls).'&disablekb='.esc_attr($yt_disablekb).'&fs='.esc_attr($yt_fs).'&modestbranding='.esc_attr($yt_modestbranding).'&loop='.esc_attr($yt_loop).'&rel='.esc_attr($yt_rel).'&playsinline='.esc_attr($yt_playsinline).'&start='.esc_attr($ytSTime).'&end='.esc_attr($ytETime).'&hl='.esc_attr($ytlanguage);
			
			if($ytType == "ytSV"){
				$ytVideoId = (!empty($attributes['ytVideoId']) ) ? $attributes['ytVideoId'] : '';
				$ytSrc = 'https://www.youtube-nocookie.com/embed/'.esc_attr($ytVideoId).'?'.esc_attr($YT_Parameters);
			}else if($ytType == "ytPlayV"){
				$ytPlaylistId = (!empty($attributes['ytPlaylistId']) ) ? $attributes['ytPlaylistId'] : '';
				$ytSrc = 'https://www.youtube-nocookie.com/embed?listType=playlist&list='.esc_attr($ytPlaylistId).'&'.esc_attr($YT_Parameters);
			}else if($ytType == "ytUserV"){
				$ytUsername = (!empty($attributes['ytUsername']) ) ? $attributes['ytUsername'] : '';
				$ytSrc = 'https://www.youtube-nocookie.com/embed?listType=user_uploads&list='.esc_attr($ytUsername).'&'.esc_attr($YT_Parameters);
			}
			
			$output .= '<iframe width="'.esc_attr($exWidth).'" height="'.esc_attr($exHeight).'" src='.esc_attr($ytSrc).' frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="'.$iframeTitle.'"></iframe>';

		}else if($embedType == 'googlemap'){
			$mapaccesstoken = (!empty($attributes['mapaccesstoken'])) ? $attributes['mapaccesstoken'] : 'default';	
			$gSearchText = (!empty($attributes['gSearchText'])) ? $attributes['gSearchText'] : 'Goa+India';
			$mapZoom = (!empty($attributes['mapZoom'])) ? (int)$attributes['mapZoom'] : 1;
			$gMHeight = (!empty($attributes['gMHeight'])) ? (int)$attributes['gMHeight'] : 450;
			$iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('Google Map','tpgb');

			if($mapaccesstoken == 'default'){
				$output .= '<iframe class="tpgb-gmap-embed" src="http://maps.google.com/maps?q='.esc_attr($gSearchText).'&z='.esc_attr($mapZoom).'&output=embed" height="'.esc_attr($gMHeight).'" loading="lazy" allowfullscreen frameborder="0" scrolling="no" title="'.$iframeTitle.'"></iframe>';
			}else if($mapaccesstoken == 'accesstoken'){
				$gAccesstoken = (!empty($attributes['gAccesstoken'])) ? $attributes['gAccesstoken'] : '';
				if(!empty($gAccesstoken)){
					$gMapModes = (!empty($attributes['gMapModes'])) ? $attributes['gMapModes'] : 'search';
					$mapViews = (!empty($attributes['mapViews'])) ? $attributes['mapViews'] : 'roadmap';

					if($gMapModes == "place"){
						$output .= '<iframe class="tpgb-gmap-embed" src="https://www.google.com/maps/embed/v1/place?key='.esc_attr($gAccesstoken).'&q='.esc_attr($gSearchText).'&zoom='.esc_attr($mapZoom).'&maptype='.esc_attr($mapViews).'&language=En" height="'.esc_attr($gMHeight).'" loading="lazy" allowfullscreen title="'.$iframeTitle.'"></iframe>';
					}else if($gMapModes == "direction"){
						$gOrigin = (!empty($attributes['gOrigin'])) ? '&origin='.$attributes['gOrigin'] : '&origin=""';
						$gDestination = (!empty($attributes['gDestination'])) ? '&destination='.$attributes['gDestination'] : '&destination=""';
						$gWaypoints = (!empty($attributes['gWaypoints'])) ? '&waypoints='.$attributes['gWaypoints'] : '';
						$gTravelMode = (!empty($attributes['gTravelMode'])) ? $attributes['gTravelMode'] : 'gTravelMode';
						$Gavoid = (!empty($attributes['Gavoid'])) ? '&avoid='.implode("|", $attributes['Gavoid']) : '';
						
						$output .= '<iframe class="tpgb-gmap-embed" src="https://www.google.com/maps/embed/v1/directions?key='.esc_attr($gAccesstoken).esc_attr($gOrigin).esc_attr($gDestination).esc_attr($gWaypoints).esc_attr($Gavoid).'&mode='.esc_attr($gTravelMode).'&zoom='.esc_attr($mapZoom).'&maptype='.esc_attr($mapViews).'&language=En" height="'.esc_attr($gMHeight).'" loading="lazy" allowfullscreen title="'.$iframeTitle.'"></iframe>';
					}else if($gMapModes == "streetview"){
						$gstreetviewText = (!empty($attributes['gstreetviewText'])) ? $attributes['gstreetviewText'] : '';

						$output .= '<iframe class="tpgb-gmap-embed" src="https://www.google.com/maps/embed/v1/streetview?key='.esc_attr($gAccesstoken).'&location='.esc_attr($gstreetviewText).'&heading=210&pitch=10&fov=90" height="'.esc_attr($gMHeight).'" loading="lazy" allowfullscreen title="'.$iframeTitle.'"></iframe>';
					}else if($gMapModes == "search"){
						$output .= '<iframe class="tpgb-gmap-embed" src="https://www.google.com/maps/embed/v1/search?key='.esc_attr($gAccesstoken).'&q='.esc_attr($gSearchText).'&zoom='.esc_attr($mapZoom).'&maptype='.esc_attr($mapViews).'&language=En" height="'.esc_attr($gMHeight).'" loading="lazy" allowfullscreen title="'.$iframeTitle.'"></iframe>';
					}
				}else{
					$output .= 'Enter Access Token';
				}
			}
		}

	$output .= '</div>';	

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	return $output;
}

function tpgb_tp_social_embed() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'embedType' => [
				'type' => 'string',
				'default' => 'facebook',	
			],
			'type' => [
				'type' => 'string',
				'default' => 'videos',	
			],
			'appID' => [
				'type'=> 'string',
				'default'=> '',
			],
			'targetC' => [
				'type'=> 'string',
				'default'=> 'custom',
			],
			'urlFC' => [
				'type'=> 'string',
				'default'=> 'plain',
			],			
			'commentAddURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/',
					'target' => '',
					'nofollow' => ''
				],
			],
			'postURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/posimyth/posts/3054603914561930',
					'target' => '',
					'nofollow' => ''
				],
			],
			'videosURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/posimyth/videos/444986032863860/',
					'target' => '',
					'nofollow' => ''
				],
			],
			'targetLike' => [
				'type'=> 'string',
				'default'=> 'custom',
			],
			'fmtURLlb' => [
				'type'=> 'string',
				'default'=> 'plain',
			],
			'likeBtnUrl' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/posimyth',
					'target' => '',
					'nofollow' => ''
				],
			],
			'saveURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/',
					'target' => '',
					'nofollow' => ''
				],
			],
			'shareURL' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/',
					'target' => '',
					'nofollow' => ''
				],
			],

			'fullPT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'hgPost' => [
				'type' => 'string',
				'default' => '',
			],
			'wdPost' => [
				'type' => 'string',
				'default' => '',
			],

			'fullVT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'autoplayVT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'captionVT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'hgVideo' => [
				'type' => 'string',
				'default' => '',
			],
			'wdVideo' => [
				'type' => 'string',
				'default' => '',
			],

			'countC' => [
				'type'=> 'string',
				'default'=> '',
			],
			'orderByC' => [
				'type'=> 'string',
				'default'=> 'social',
			],

			'typeLB' => [
				'type'=> 'string',
				'default'=> 'like',
			],
			'btnStyleLB' => [
				'type'=> 'string',
				'default'=> 'button',
			],
			'sizeLB' => [
				'type'=> 'string',
				'default'=> 'small',
			],
			'colorSLB' => [
				'type'=> 'string',
				'default'=> 'light',
			],
			'sBtnLB' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'facesLBT' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'hgLikeBtn' => [
				'type' => 'string',
				'default' => '',
			],			
			'wdLikeBtn' => [
				'type' => 'string',
				'default' => '',
			],

			'uRLP' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://www.facebook.com/posimyth',
					'target' => '',
					'nofollow' => ''
				],
			],
			'layoutP' => [
				'type'=> 'string',
				'default'=> 'timeline',
			],
			'smallHP' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'coverP' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'profileP' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'ctaBtn' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'hgPage' => [
				'type' => 'string',
				'default' => '',
			],
			'wdPage' => [
				'type' => 'string',
				'default' => '',
			],
			
			'shareBtn' => [
				'type'=> 'string',
				'default'=> 'button',
			],
			'wdShare' => [
				'type' => 'string',
				'default' => '',
			],
			'hgShare' => [
				'type' => 'string',
				'default' => '',
			],

			'tweetType' => [
				'type' => 'string',
				'default' => 'timelines',	
			],
			'twRepeater' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'tweetURl' => [
							'type'=> 'object',
							'default'=> [
								'url' => 'https://twitter.com/Interior/status/463440424141459456',
								'target' => '',
								'nofollow' => ''
							],
						],
						'twMassage' => [
							'type'=> 'string',
							'default'=> 'Loading',
						],
					],
				],
				'default' => [ 
					['_key'=> 'Tw1','tweetURl'=>['url'=>'https://twitter.com/Interior/status/463440424141459456'],'twMassage'=>'&mdash; Loading']
				],
			],
			'twGuides' => [
				'type' => 'string',
				'default' => 'profile',	
			],
			'twstyle' => [
				'type' => 'string',
				'default' => 'linear',	
			],
			'twCollection' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://twitter.com/TwitterDev/timelines/539487832448843776',
					'target' => '',
					'nofollow' => ''
				],
			],
			'twlisturl' => [
				'type'=> 'object',
				'default'=> [
					'url' => 'https://twitter.com/TwitterDev/lists/national-parks',
					'target' => '',
					'nofollow' => ''
				],
			],
			'twbutton' => [
				'type' => 'string',
				'default' => 'follow',	
			],
			'twname' => [
				'type'=> 'string',
				'default'=> 'TwitterDev',
			],
			'twRId' => [
				'type'=> 'string',
				'default'=> '3805104374',
			],

			'twColor' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'twCards' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'twalign' => [
				'type' => 'string',
				'default' => 'center',	
			],
			'twconver' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'twDesign' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'twBrCr' => [
				'type' => 'string',
				'default' => '',
			],
			'twlimit' => [
				'type' => 'string',
				'default' => '',
			],
			'twwidth' => [
				'type' => 'string',
				'default' => '',
			],
			'twheight' => [
				'type' => 'string',
				'default' => '500',
			],
			'twBtnSize' => [
				'type' => 'string',
				'default' => '',	
			],
			'twTextBtn' => [
				'type'=> 'string',
				'default'=> 'Hello',
			],
			'twTweetUrl' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',
					'target' => '',
					'nofollow' => ''
				],
			],
			'twHashtags' => [
				'type' => 'string',
				'default' => 'Twitter',	
			],
			'twVia' => [
				'type' => 'string',
				'default' => 'Twitter',	
			],	
			'twMessage' => [
				'type'=> 'string',
				'default'=> 'Hello',
			],
			'twTweetId' => [
				'type'=> 'string',
				'default'=> '463440424141459456',
			],
			'twCount' => [
				'type' => 'boolean',
				'default' => true,	
			],	
			'twHideUname' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'twIcon' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'likeBtn' => [
				'type'=> 'string',
				'default'=> 'Like',
			],
			'replyBtn' => [
				'type'=> 'string',
				'default'=> 'Reply',
			],
			'reTweetBtn' => [
				'type'=> 'string',
				'default'=> 'Retweet',
			],
			'twMsg' => [
				'type'=> 'string',
				'default'=> 'Loading',
			],

			'viId' => [
				'type'=> 'string',
				'default'=> '288344114',
			],
			'viOption' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'vmStime' => [
				'type'=> 'string',
				'default'=> '',
			],
			'vmColor' => [
				'type' => 'string',
				'default' => '',
			],
			'exWidth' => [
				'type' => 'string',
				'default' => 640,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'youtube' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed .tpgb-social-yt{width:{{exWidth}}px;}',
					],
				],
				'scopy' => true,
			],
			'exHeight' => [
				'type' => 'string',
				'default' => 360,
			],

			'iGType' => [
				'type' => 'string',
				'default' => 'posts',	
			],
			'iGId' => [
				'type'=> 'string',
				'default'=> 'CGAvnLcA3zb',
			],
			'iGCaptione' => [
				'type' => 'boolean',
				'default' => false,	
			],

			'ytType' => [
				'type' => 'string',
				'default' => 'ytSV',	
			],
			'ytVideoId' => [
				'type'=> 'string',
				'default'=> 'XmtXC_n6X6Q',
			],
			'ytPlaylistId' => [
				'type'=> 'string',
				'default'=> 'PLivjPDlt6ApQgylktXlL2AhuPvRtDiN1S',
			],
			'ytUsername' => [
				'type'=> 'string',
				'default'=> 'NationalGeographic',
			],
			'ytOption' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'ytSTime' => [
				'type'=> 'string',
				'default'=> '',
			],
			'ytETime' => [
				'type'=> 'string',
				'default'=> '',
			],
			'ytlanguage' => [
				'type'=> 'string',
				'default'=> '',
			],
			
			'mapaccesstoken' => [
				'type'=> 'string',
				'default'=> 'default',
			],
			'gAccesstoken' => [
				'type'=> 'string',
				'default'=> '',
			],
			'gMapModes' => [
				'type'=> 'string',
				'default'=> 'place',
			],
			'gSearchText' => [
				'type'=> 'string',
				'default'=> 'Goa+India',
			],
			'gOrigin' => [
				'type'=> 'string',
				'default'=> 'LosAngeles+California+USA',
			],
			'gDestination' => [
				'type'=> 'string',
				'default'=> 'Corona+California+USA',
			],
			'gWaypoints' => [
				'type'=> 'string',
				'default'=> 'Huntington+Beach+California+US | Santa Ana+California+USA',
			],
			'gTravelMode' => [
				'type'=> 'string',
				'default'=> 'driving',
			],
			'gavoidtolls' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'gavoidhighways' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'gstreetviewText' => [
				'type'=> 'string',
				'default'=> '23.0489,72.5160',
			],
			
			'mapViews' => [
				'type'=> 'string',
				'default'=> 'roadmap',
			],
			'mapZoom' => [
				'type'=> 'string',
				'default'=> '5',
			],
			'gMHeight' => [
				'type'=> 'string',
				'default'=> '350',
			],
			'iframeTitle' => [
				'type' => 'string',
				'default' => '',	
			],

			'alignmentBG' => [
				'type' => 'object',
				'default' => [ 'md' => '', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter' ],
										(object) ['key' => 'tweetType', 'relation' => '!=', 'value' => 'tweets' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed{text-align:{{alignmentBG}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '!=', 'value' => 'twitter' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed{text-align:{{alignmentBG}};}',
					],
				],
				'scopy' => true,
			],
			'borderPost' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter' ]],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button',
					],
				],
				'scopy' => true,
			],
			'borderRs' => [
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
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe{border-radius:{{borderRs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter']],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet{border-radius:{{borderRs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button{border-radius:{{borderRs}};}',
					],
				],
				'scopy' => true,
			],
			'boxS' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter']],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button',
					],
				],
				'scopy' => true,
			],
			'borderPostHr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter']],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button:hover',
					],
				],
				'scopy' => true,
			],
			'borderHRs' => [
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
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],				
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe:hover{border-radius:{{borderHRs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter']],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet:hover{border-radius:{{borderHRs}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button:hover{border-radius:{{borderHRs}};}',
					],
				],
				'scopy' => true,
			],
			'boxSHr' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [	
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'facebook' ],
										(object) ['key' => 'type', 'relation' => '==', 'value' => ['posts','videos','page','comments'] ]],					
						'selector' => '{{PLUS_WRAP}} .tpgb-fb-iframe:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => 'twitter']],
						'selector' => '{{PLUS_WRAP}} .twitter-tweet:hover',
					],
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons' ]],
						'selector' => '{{PLUS_WRAP}} .tw-button:hover',
					],
				],
				'scopy' => true,
			],
			'twBtnCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons']],
						'selector' => '{{PLUS_WRAP}} .tw-button{color:{{twBtnCr}};}',
					],
				],
				'scopy' => true,
			],
			'twBtnCrH' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'tweetType', 'relation' => '==', 'value' => 'buttons']],
						'selector' => '{{PLUS_WRAP}} .tw-button:hover{color:{{twBtnCrH}};}',
					],
				],
				'scopy' => true,
			],

			'socialBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed',
					],
				],
				'scopy' => true,
			],
			'embedBr' => [
				'type' => 'object',
				'default' => (object) ['openBorder' => 0],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => ['vimeo','instagram','youtube']]],
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed iframe',
					],
				],
				'scopy' => true,
			],
			'embedBsd' => [
				'type' => 'object',
				'default' => (object) ['openShadow' => 0],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'embedType', 'relation' => '==', 'value' => ['vimeo','instagram','youtube']]],
						'selector' => '{{PLUS_WRAP}}.tpgb-social-embed iframe',
					],
				],
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions,$plusButton_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-social-embed', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_social_embed_render_callback'
    ) );	
}
add_action( 'init', 'tpgb_tp_social_embed' );
