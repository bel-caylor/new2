<?php
/* Block : Tp Video
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_video_callback( $settings, $content) {
	
    $block_id    = isset ( $settings[ 'block_id' ] ) ? $settings[ 'block_id' ] : '';
	$anim_styles = isset ( $settings[ 'style' ] ) ? $settings[ 'style' ] : 'style-1';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $settings );

	//Google Schema Attributes
	$mainsch =  $thumbsch =  $titlesch =  $descsch = '';
	if(!empty($settings[ 'markupSch' ])){
		$mainsch = 'itemscope="" itemprop="VideoObject" itemtype="http://schema.org/VideoObject"';
		$thumbsch = 'itemprop="thumbnailUrl"';
		$titlesch = 'itemprop="name"';
		$descsch = 'itemprop="description"';
		$uploadate = date("j F Y");
	}


    $VideoType = $settings[ 'VideoType' ];
    $YoutubeID = $VimeoID   = $mp4Url   = '';
    if(!empty ( $settings[ "YoutubeID" ])) {
        $YoutubeID = (class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($settings[ "YoutubeID" ]) : $settings[ "YoutubeID" ];
		
    }
    if( !empty( $settings["VimeoID"])) {
        $VimeoID = (class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($settings[ "VimeoID" ]) : $settings[ "VimeoID" ];
    }
	
    if( !empty($settings["mp4Url"]['url'])) {
        $mp4Url = (isset($settings[ "mp4Url" ]['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings["mp4Url"]) : $settings[ "mp4Url" ]['url'];
    }
    $icon_effect = '';
    if( ! empty ( $settings[ "ContinueAnim" ] ) && $settings[ "ContinueAnim" ] === true ) {
        if( $settings[ "ContinueAnimHover" ] == true ) {
            $animation_class = 'tpgb-hover-';
        } else {
            $animation_class = 'tpgb-';
        }
        $icon_effect = $animation_class . $settings[ "ContinueAnimEffect" ];
    }
    $video_content   = $banner_url      = $video_space     = $OverlayIconImg_url = $OverlayIconImg     = $image_alt       = $only_image      = $title           = $BannerImg    = '';

    $IconAlign_video = '';
    if( !empty( $settings[ "VideoTitle" ]) || !empty($settings[ "VideoDesc" ])) {
        $title .= '<div class="ts-video-caption-text">';
			$title .=  wp_kses_post($settings["VideoTitle"]); 
			if(! empty ( $settings[ "VideoDesc" ] )){
				$title .= '<div class="tpgb-video-desc" '.$descsch.' >';
					$title .= wp_kses_post($settings["VideoDesc"]) ;
				$title .= '</div>';
			}
		$title .= '</div>';
    }

    if( ! empty ( $settings[ "VideoIcon" ][ "url" ] ) ) {
         if ( isset($settings[ 'VideoIcon' ][ 'id' ] ) ) {
            $VideoIcon      = $settings[ 'VideoIcon' ][ 'id' ];
            $img           = wp_get_attachment_image_src( $VideoIcon, $settings[ 'VideoIconSize' ] );
			$img =  ( isset($img[0]) && !empty($img[0]) ) ? $img[0] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg' ;
            $VideoIcon_icon = ( isset($settings[ "VideoIcon" ]['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper') ) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings[ "VideoIcon" ]) : $img ;
        } else {
            $VideoIcon_icon = (isset($settings[ "BannerImg" ]['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings[ "BannerImg" ]) : (isset($settings[ "BannerImg" ][ "url" ]) ? $settings[ "BannerImg" ][ "url" ] : '');
        }
        $only_image    .= '<img class="ts-video-only-icon" src="'.esc_url($VideoIcon_icon). '" alt="' . esc_attr__( "play-icon", "tpgb" ) . '" />';
    }

    if( ! empty ( $settings[ "OverlayIconImg" ][ "url" ] ) ) {
        $OverlayIconImg_src = $settings[ 'OverlayIconImg' ][ 'id' ];
        $img             = wp_get_attachment_image_src ( $OverlayIconImg_src, $settings[ 'OverlayIconImgSize' ] );
		$img = ( isset($img[0]) && !empty($img[0]) ) ? $img[0] : TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg' ;
		$OverlayIconImg = (isset($settings[ "OverlayIconImg" ]['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings[ "OverlayIconImg" ]) : $img ;

        $image_id  = $settings[ "OverlayIconImg" ][ "id" ];
        $image_alt = get_post_meta ( $image_id, '_wp_attachment_image_alt', TRUE );
        if( ! $image_alt ) {
            $image_alt = get_the_title ( $image_id );
        } else if( ! $image_alt ) {
            $image_alt = 'Plus video thumb';
        }
        $OverlayIconImg_url .= '<div class="tp-video-icon-inner ' . esc_attr ( $icon_effect ) . '"><img class="ts-video-icon" src="' . esc_url ( $OverlayIconImg ) . '"  alt="' . esc_attr ( $image_alt ) . '" /></div>';
    }
    if( !empty ( $settings[ "BannerImg" ][ "url" ] ) ) {
        if ( !empty($settings[ 'BannerImg' ][ 'id' ] ) && isset($settings[ 'BannerImg' ][ 'id' ] ) ) {
            $BannerImg = $settings[ 'BannerImg' ][ 'id' ];
            $img          = wp_get_attachment_image_src( $BannerImg, $settings[ 'BannerImgSize' ] );
			$BannerImg = (!empty($img) && isset($img[0])) ? $img[ 0 ] : '';
        } else {
			$BannerImg = (isset($settings['BannerImg']['dynamic']) && class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($settings[ "BannerImg" ]) :  $settings[ "BannerImg" ][ "url" ];
        }
        $banner_url   .= '<img class="ts-video-image-zoom set-image" src="' . esc_url ( $BannerImg ) . '" alt="'.esc_attr__('video','tpgb').'" /><div class="tp-video-popup-icon"> <div class="tp-video-icon ' . esc_attr ( $icon_effect ) . '"><img class="ts-video-caption" src="' . esc_url ( $OverlayIconImg ) . '" alt="' . esc_attr ( $image_alt ) . '" /></div></div>' . $title;
    }

    $youtube_attr       = $youtube_frame_attr = $video_touchable    = $self_video_attr    = $vimeo_frame_attr   = '';
    if( ! empty ( $settings[ 'autoPlay' ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;autoplay=1&amp;version=3';
            $youtube_attr       = ' allow="autoplay; encrypted-media"  ';
        }
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;autoplay=1';
        }
        if( $VideoType == 'self-hosted' ) {
            $self_video_attr .= ' autoplay';
        }
    }

    if( ! empty ( $settings[ 'loop' ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;loop=1&amp;playlist=' . esc_attr ( $settings[ "YoutubeID" ] );
        }
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;loop=1';
        }
        if( $VideoType == 'self-hosted' ) {
            $self_video_attr .= ' loop ';
        }
    }

    if( ! empty ( $settings[ "controls" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;controls=1';
        }
        if( $VideoType == 'self-hosted' ) {
            $self_video_attr .= ' controls ';
        }
    } else {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;controls=0';
        }
    }
    if( ! empty ( $settings[ "showinfo" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;showinfo=1';
        }
    } else {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;showinfo=0';
        }
    }
    if( ! empty ( $settings[ "ModestBranding" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;modestbranding=1';
        }
    } else {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;modestbranding=0';
        }
    }
    if( ! empty ( $settings[ "rel" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;rel=1';
        }
    } else {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;rel=0';
        }
    }
    $youtube_privacy = '';
    if( ! empty ( $settings[ "yt_privacy" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_privacy .= '-nocookie';
        }
    } else {
        if( $VideoType == 'youtube' ) {
            $youtube_privacy .= '';
        }
    }
    if( ! empty ( $settings[ "muted" ] ) ) {
        if( $VideoType == 'youtube' ) {
            $youtube_frame_attr .= '&amp;mute=1';
        }
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;muted=1';
        }
        if( $VideoType == 'self-hosted' ) {
            $self_video_attr .= ' muted ';
        }
    }
    if( ! empty ( $settings[ "VideoColor" ] ) ) {
        if( $VideoType == 'vimeo' ) {
            $VideoColor      = str_replace ( '#', '', $settings[ 'VideoColor' ] );
            $vimeo_frame_attr .= '&amp;color=' . $VideoColor . ';';
        }
    }
    if( ! empty ( $settings[ "VimeoTitle" ] ) ) {
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;title=1;';
        }
    } else {
        $vimeo_frame_attr .= '&amp;title=0;';
    }
    if( ! empty ( $settings[ "VimeoPortrait" ] ) ) {
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;portrait=1;';
        }
    } else {
        $vimeo_frame_attr .= '&amp;portrait=0;';
    }
    if( ! empty ( $settings[ "VimeoByline" ] ) ) {
        if( $VideoType == 'vimeo' ) {
            $vimeo_frame_attr .= '&amp;byline=1;';
        }
    } else {
        $vimeo_frame_attr .= '&amp;byline=0;';
    }
    if( ! empty ( $settings[ "touchDisable" ] ) ) {
        $video_touchable = ' not-touch ';
    }
    $image_banner         = $settings[ 'image_banner' ];
    $ShowBannerImg = $settings[ 'ShowBannerImg' ];
    if( $image_banner == 'banner_img' ) {
        if( $ShowBannerImg == true ) {
            if( !empty($settings[ 'VideoPopup' ]) ) {
                if( $VideoType == 'youtube' ) {
                    $video_content .= '<a href="https://www.youtube' . $youtube_privacy . '.com/embed/' . esc_attr ( $YoutubeID ) . '" data-fancybox >' . $banner_url . '</a>';
                } else if( $VideoType == 'vimeo' ) {
                    $video_content .= '<a href="https://player.vimeo.com/video/' . esc_attr ( $VimeoID ) . '" data-fancybox >' . $banner_url . '</a>';
                } else if( $VideoType == 'self-hosted' ) {
                    $video_content .= '<a href="' . esc_url ( $mp4Url ) . '" data-fancybox type="video/mp4">' . $banner_url . '</a>';
                }
                $video_space = '';
            } else {
                if( $VideoType == 'youtube' ) {
                    $video_content .= '<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-' . esc_attr( $VideoType ) . '" data-mode="lazyload" data-provider="' .esc_attr( $VideoType ) . '" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="tpgb-video-embed-wrap" ><img class="tpgb-video-thumb" data-object-fit="" '.$thumbsch.' content="'.esc_url( $BannerImg ).'" src="' . esc_url( $BannerImg ) . '" alt="' . esc_attr( "Video Thumbnail" ) . '"><h5 '.$titlesch.' class="tpgb-video-title">' . $title . '</h5><span class="ts-video-lazyload" data-allowfullscreen="" data-class="pt-plus-video-frame fitvidsignore" data-frameborder="0" data-scrolling="no" data-src="https://www.youtube' . $youtube_privacy . '.com/embed/' . esc_attr( $YoutubeID ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1' . $youtube_frame_attr . '"  data-sandbox="allow-scripts allow-same-origin allow-presentation allow-forms" data-width="480" data-height="270"></span><button class="tpgb-video-play-btn ts-video-blay-btn-youtube" type="button">' . $OverlayIconImg_url . '</button>';
					if(!empty($settings[ 'markupSch' ])){
						$video_content .= '<div class="tpgb-video-upload" itemprop="uploadDate" content="'.$uploadate.'"></div><div class="tpgb-video-upload" itemprop="contentUrl" content="https://www.youtube' . $youtube_privacy . '.com/embed/' . esc_attr ( $YoutubeID ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1' . $youtube_frame_attr . '"></div>';
					}
					$video_content .= '</div></div>';
                } else if( $VideoType == 'vimeo' ) {
                    $video_content .= '<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-' . esc_attr ( $VideoType ) . '" data-mode="lazyload" data-provider="' . esc_attr($VideoType) . '" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="tpgb-video-embed-wrap" ><img class="tpgb-video-thumb" data-object-fit="" '.$thumbsch.' content="'.esc_url( $BannerImg ).'" src="' . esc_url( $BannerImg ) . '" alt="' . esc_attr( "Video Thumbnail" ) . '"><h5 '.$titlesch.' class="tpgb-video-title">' . $title . '</h5><span class="ts-video-lazyload" data-allowfullscreen="" data-class="pt-plus-video-frame fitvidsignore" data-frameborder="0" data-scrolling="no" data-src="https://player.vimeo.com/video/' . esc_attr( $VimeoID ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1" data-sandbox="allow-scripts allow-same-origin allow-presentation allow-forms" data-width="480" data-height="270"></span><button class="tpgb-video-play-btn ts-video-blay-btn-youtube" type="button">' . $OverlayIconImg_url . '</button>';
						if(!empty($settings[ 'markupSch' ])){
							$video_content .= '<div class="tpgb-video-upload" itemprop="uploadDate" content="'.$uploadate.'"></div><div class="tpgb-video-upload" itemprop="contentUrl" content="https://player.vimeo.com/video/' . esc_attr ( $VimeoID ) . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1"></div>';
						}
					$video_content .= '</div></div>';
                } else if( $VideoType == 'self-hosted' ) {
                    $video_content .= '<div class="ts-video-wrapper ts-video-hover-effect-zoom ts-type-' . esc_attr( $VideoType ) . '" data-mode="lazyload" data-provider="' . esc_attr($VideoType) . '" id="ts-video-video-6" '.$mainsch.' data-grow=""><div class="tpgb-video-embed-wrap" ><img class="tpgb-video-thumb" data-object-fit="" '.$thumbsch.' content="'.esc_url( $BannerImg ).'" src="' . esc_url( $BannerImg ) . '" alt="' . esc_attr__( "Video Thumbnail",'tpgb' ) . '"><h5 '.$titlesch.' class="tpgb-video-title">' . $title . '</h5><div class="video_container"><video class="tpgb-video-poster" width="100%" poster="' . esc_url( $BannerImg ) . '" controls > <source src="' . esc_url( $mp4Url ) . '" type="video/mp4" ></video></div></span><button class="tpgb-video-play-btn ts-video-blay-btn-youtube" type="button">' . $OverlayIconImg_url . '</button>';
						if(!empty($settings[ 'markupSch' ])){
							$video_content .= '<div class="tpgb-video-upload" itemprop="uploadDate" content="'.$uploadate.'"></div><div class="tpgb-video-upload" itemprop="contentUrl" content="' . esc_url ( $mp4Url ) . '"></div>';
						}
					$video_content .= '</div></div>';
                }
            }
        } else {
			$iframeTitle = (!empty($settings['iframeTitle'])) ? esc_attr($settings['iframeTitle']) : esc_attr__('My Video','tpgb');
            if( $VideoType == 'youtube' ) {
                $video_content .= '<div class="ts-video-wrapper embed-container  ts-type-' . esc_attr( $VideoType ) . '"><iframe width="100%"  src="https://www.youtube' . $youtube_privacy . '.com/embed/' . esc_attr( $YoutubeID ) . '?&amp;autohide=1&amp;showtitle=0' . $youtube_frame_attr . '" ' . $youtube_attr . ' frameborder="0" allowfullscreen title="'.$iframeTitle.'"></iframe></div>';
            } else if( $VideoType == 'vimeo' ) {
                $video_content .= '<div class="ts-video-wrapper embed-container  ts-type-' . esc_attr( $VideoType ) . '"><iframe src="https://player.vimeo.com/video/' . $VimeoID . '?html5=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;' . $vimeo_frame_attr . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen title="'.$iframeTitle.'"></iframe></div>';
            } else if( $VideoType == 'self-hosted' ) {
                $video_content .= '<div class="ts-video-wrapper ts-type-' . esc_attr( $VideoType ) . '"><video ' . esc_attr( $self_video_attr ) . '> <source src="' . esc_url( $mp4Url ) . '" type="video/mp4" ></video></div>';
            }
        }
    } else if( $image_banner == 'only_icon' ) {
            if( $VideoType == 'youtube' ) {
                $video_content .= '<a href="https://www.youtube.com/embed/' . esc_attr( $YoutubeID ) . '" class="tp-video-popup ' . esc_attr( $icon_effect ) . '" data-fancybox >' . $only_image . '</a>';
            } else if( $VideoType == 'vimeo' ) {
                $video_content .= '<a href="https://player.vimeo.com/video/' . esc_attr( $VimeoID ) . '" class="tp-video-popup ' . esc_attr( $icon_effect ) . '" data-fancybox >' . $only_image . '</a>';
            } else if( $VideoType == 'self-hosted' ) {
                $video_content .= '<a href="' . esc_url( $mp4Url ) . '" class="tp-video-popup ' . esc_attr( $icon_effect ) . '" data-fancybox type="video/mp4">' . $only_image . '</a>';
            }
        $IconAlign_video = $settings[ 'IconAlign' ];
    }

    $uid          = uniqid ( 'video_player' );
    $video_player = '<div class="tp-video tpgb-video-box tpgb-block-' . esc_attr($block_id) . ' ' . esc_attr( $uid ) . ' '.esc_attr($blockClass).' ">';
    $video_player .= '<div class="tpgb_video_player tpgb-relative-block ' . esc_attr( $video_touchable ) . ' ' . esc_attr( $video_space ) . ' text-' . esc_attr( $IconAlign_video ) . '">';
	$video_player .= $video_content;
    $video_player .= '</div>';
    $video_player .= '</div>';
	
	$video_player = Tpgb_Blocks_Global_Options::block_Wrap_Render($settings, $video_player);
	
    return $video_player;
}

function tpgb_tp_video_render() {
	
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$attributesOptions = [
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'VideoType' => [
			'type' => 'string',
			'default' => 'youtube',
		],
		'YoutubeID' => [
			'type' => 'string',
			'default' => 'TJ1SDXbij8Y',
		],
		'VimeoID' => [
			'type' => 'string',
			'default' => '27246366',
		],
		'mp4Url' => [
				'type' => 'object',
				'default' => [],
		],
		'autoPlay' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'muted' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'loop' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'controls' => [
				'type' => 'boolean',
				'default' => true,	
		],
		'showinfo' => [
				'type' => 'boolean',
				'default' => true,	
		],
		'touchDisable' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'ModestBranding' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'VideoColor' => [
				'type' => 'string',
				'default' => '',
		],
		'rel' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'yt_privacy' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'VimeoTitle' => [
				'type' => 'boolean',
				'default' => true,	
		],
		'VimeoPortrait' => [
				'type' => 'boolean',
				'default' => true,	
		],
		'VimeoByline' => [
				'type' => 'boolean',
				'default' => true,	
		],
		'image_banner' => [
			'type' => 'string',
			'default' => 'banner_img',
		],
		'VideoIcon' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg'
				],
		],
		'VideoIconSize' => [
			'type' => 'string',
			'default' => 'full',
		],
		'IconAlign' => [
				'type' => 'string',
				'default' => 'center',
		],
		'ShowBannerImg' => [
				'type' => 'boolean',
				'default' => false,	
		],
		'BannerImg' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL. 'assets/images/tpgb-placeholder.jpg'
				],
		],
		'BannerImgSize' => [
			'type' => 'string',
			'default' => 'full',
		],
		'OverlayIconImg' => [
			'type' => 'object',
			'default' => [],
		],
		'OverlayIconImgSize' => [
			'type' => 'string',
			'default' => 'full',
		],
        'VideoTitle' => [
			'type' => 'string',
			'default' => 'Earth from the Moon',
		],
		'markupSch' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'iframeTitle' => [
			'type' => 'string',
			'default' => '',	
		],
        'VideoTitleTypo' => [
            'type'=> 'object',
            'default' => (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ShowBannerImg', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-video-box .ts-video-caption-text',
				],
			],
			'scopy' => true,
        ],
		'TitleColor' => [
			'type' => 'string',
			'default' => '#313131',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ShowBannerImg', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .ts-video-caption-text{ color: {{TitleColor}}; }',
				],
			],
			'scopy' => true,
		],
		'TitleBgColor' => [
			'type' => 'string',
			'default' => '#ffffff',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ShowBannerImg', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .ts-video-caption-text{ background: {{TitleBgColor}}; }',
				],
			],
			'scopy' => true,
		],
		'VideoPopup' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'VideoBorder' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
				'color' => '',
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
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player',
				],
			],
			'scopy' => true,
		],
		'VideoBRadius' => [
			'type' => 'object',
			'default' => (object) [ 
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player{ border-radius: {{VideoBRadius}};overflow:hidden; }',
				],
			],
			'scopy' => true,
		],
		'BoxShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player',
				],
			],
			'scopy' => true,
		],
        'Transform' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-video-box{transform:{{Transform}};}',
				],
			],
			'scopy' => true,
		],
		'VideoBorderH' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => 'solid',
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
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player:hover',
				],
			],
			'scopy' => true,
		],
		'VideoBRadiusH' => [
			'type' => 'object',
			'default' => (object) [ 
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player:hover{ border-radius: {{VideoBRadiusH}}; }',
				],
			],
			'scopy' => true,
		],
		'BoxShadowH' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player:hover',
				],
			],
			'scopy' => true,
		],
		'TransformH' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-video-box:hover{transform:{{TransformH}};};',
				],
			],
			'scopy' => true,
		],
		'ContinueAnim' => [
            'type' => 'boolean',
            'default' => false,	
			'scopy' => true,
        ],
		'ContinueAnimEffect' => [
			'type' => 'string',
			'default' => 'pulse',
			'scopy' => true,
		],
		'ContinueAnimHover' => [
			'type' => 'boolean',
			'default' => false,	
			'scopy' => true,
		],
		'ContinueAnimDur' => [
			'type' => 'string',
			'default' => 2.5,
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'ContinueAnim', 'relation' => '==', 'value' => true],
						(object) ['key' => 'ContinueAnimEffect', 'relation' => '!==', 'value' => 'drop_waves']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player .tp-video-icon-inner,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup-icon .tp-video-icon{ animation-duration: {{ContinueAnimDur}}s; }',
				],
			],
			'scopy' => true,
		],
		'ContinueTransRotate' => [
			'type' => 'string',
			'default' => 'center center',
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'ContinueAnim', 'relation' => '==', 'value' => true],
						(object) ['key' => 'ContinueAnimEffect', 'relation' => '==', 'value' => 'rotating']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player .tp-video-icon-inner,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup-icon .tp-video-icon{-webkit-transform-origin: {{ContinueTransRotate}};-moz-transform-origin:{{ContinueTransRotate}},-ms-transform-origin:{{ContinueTransRotate}};,-o-transform-origin: {{ContinueTransRotate}};,transform-origin: {{ContinueTransRotate}};}',
			   ],
			],
			'scopy' => true,
		],
		'DropWaveColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [
						(object) ['key' => 'ContinueAnim', 'relation' => '==', 'value' => true],
						(object) ['key' => 'ContinueAnimEffect', 'relation' => '==', 'value' => 'drop_waves']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player .tpgb-drop_waves:after,{{PLUS_WRAP}} .tpgb_video_player .tpgb-hover-drop_waves:hover:after{ background: {{DropWaveColor}}; }',
				],
			],
			'scopy' => true,
		],
		'IconRadius' => [
			'type' => 'object',
			'default' => (object) [ 
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player .tp-video-icon-inner,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup-icon .tp-video-icon,{{PLUS_WRAP}} .tpgb_video_player .tpgb-drop_waves:after,{{PLUS_WRAP}} .tpgb_video_player .tpgb-hover-drop_waves:hover:after{border-radius: {{IconRadius}};}',
				],
			],
			'scopy' => true,
		],
		'PlayIconSize' => [
			'type' => 'string',
			'default' => 100,
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb_video_player .tp-video-icon-inner,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup,{{PLUS_WRAP}} .tpgb_video_player .tp-video-popup-icon{max-width: {{PlayIconSize}}px;width: {{PlayIconSize}}px;max-height: {{PlayIconSize}}px;height: {{PlayIconSize}}px;}',
				],
			],
			'scopy' => true,
		],
		'VideoDesc' => [
			'type' => 'string',
			'default' => '',
		],
		'VideoDescTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'VideoDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .ts-video-caption-text .tpgb-video-desc',
				],
			],
			'scopy' => true,
		],
		'DescColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'VideoDesc', 'relation' => '!=', 'value' => '' ]],
					'selector' => '{{PLUS_WRAP}} .ts-video-caption-text .tpgb-video-desc{ color : {{DescColor}} }',
				],
			],
			'scopy' => true,
		],
	];
	
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption);
	register_block_type( 'tpgb/tp-video', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_video_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_video_render' );