<?php
/* Block : Audio player
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_audio_player_callback( $attributes, $content) {
	$audio_player = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $Apstyle = (!empty($attributes['Apstyle'])) ? $attributes['Apstyle'] : 'style-1';
    $SplitText = (!empty($attributes['SplitText'])) ? $attributes['SplitText'] : '';
    $Aprepeater = (!empty($attributes['Aprepeater'])) ? $attributes['Aprepeater'] : [];
    $DefaultVolume = (!empty($attributes['DefaultVolume'])) ? $attributes['DefaultVolume'] : '80';
    $ImageSize = (!empty($attributes['ImageSize'])) ? $attributes['ImageSize'] : 'thumbnail';
   
	$Default_Img = TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
   
    $ap_playlisticon ='<div class="playlistIcon"><i class="fa fa-list" aria-hidden="true"></i></div>';
    $ap_track_txt='<span class="splitTxt"> '.wp_kses_post($SplitText).' </span>';		
    $ap_play_pause ='<div class="tpgb-ap-pp"><div class="play"><i class="fa fa-play" aria-hidden="true"></i></div><div class="pause"><i class="fa fa-pause" aria-hidden="true"></i></div></div>';
    $ap_rew='<div class="rew"><i class="fa fa-backward" aria-hidden="true"></i></div>';
    $ap_fwd='<div class="fwd"><i class="fa fa-forward" aria-hidden="true"></i></div>';
    $ap_endtime ='<div class="durationtime"></div>';
    $ap_currenttime ='<div class="currenttime">00.00</div>';
    $ap_tracker='<div class="tracker"></div>'; 
    $ap_volume='<div class="volumeIcon"><i class="fa fa-volume-up vol-icon-toggle" aria-hidden="true"></i><div class="tpgb-volume-bg"><div class="volume ui-slider ui-slider-vertical ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header ui-corner-all ui-slider-range-max"></div><span class="ui-slider-handle tpgb-trans-easeinout ui-state-default ui-corner-all" tabindex="0"></span></div></div></div>';
	
	$contorls = '<div class="controls"> '.wp_kses_post($ap_rew). wp_kses_post($ap_play_pause). wp_kses_post($ap_fwd).' </div>';
	
    $i=0;
    $ap_trackdetails_title=$ap_trackdetails_artist=$ap_img=$ap_img_rnd='';
    $ap_playlist='<div class="playlist" id="playlist">';

        foreach ( $Aprepeater as $item ) {
            $audiourl=$thumb_img='';
            $AudSource = ( !empty($item['AudSource']) ) ? $item['AudSource'] : 'url';
            $SourceFile = (isset($item['sorself']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['sorself']) : (!empty($item['sorself']['url']) ? $item['sorself']['url'] : '');
            $SourceURL = (isset($item['sorurl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['sorurl']) : (!empty($item['sorurl']['url']) ? $item['sorurl']['url'] : '');
            
            if( $AudSource == 'file' ){
                $audiourl = $SourceFile;
            }else if( $AudSource == 'url' ){
                $audiourl = $SourceURL;
            }
            
            if(isset($item['Imagesource']['dynamic'])){
                $img_url = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['Imagesource']);
                $thumb_img_Render = '<img src="'.esc_url($img_url).'" />';
                $thumb_img = (!empty($img_url)) ? $img_url : $Default_Img;
            }else if(!empty($item['Imagesource']['id'])){
                $image_id = $item['Imagesource']['id'];
                $thumb_img = wp_get_attachment_image_src($image_id, $ImageSize);
                $thumb_img_Render = wp_get_attachment_image($image_id, $ImageSize);
                $thumb_img = !empty($thumb_img[0]) ? $thumb_img[0] : $Default_Img;
            }else if(!empty($attributes['Audioimage']['id'])){
                $image_id = $attributes['Audioimage']['id'];
                $thumb_img = wp_get_attachment_image_src($image_id, $ImageSize);
                $thumb_img_Render = wp_get_attachment_image($image_id, $ImageSize);
                $thumb_img = !empty($thumb_img[0]) ? $thumb_img[0] : $Default_Img;
            }else if(!empty($attributes['Audioimage']['url'])){
                $thumb_img_Render = '<img src="'.esc_url($attributes['Audioimage']['url']).'" />';
                $thumb_img = $attributes['Audioimage']['url'];
            }else{
                $thumb_img_Render = '<img src="'.esc_url($Default_Img).'" />';
                $thumb_img = $Default_Img;
            }
        
            if($i==0){ 
                $ap_trackdetails_title='<span class="title">'.wp_kses_post($item['title']).'</span>';
                $ap_trackdetails_artist='<span class="artist">'.wp_kses_post($item['author']).'</span>';
                
                if(isset($item['Imagesource']['dynamic'])){
                    $img_url = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['Imagesource']);
                    $ap_img_rnd = '<img src="'.esc_url($img_url).'" />';
                    $ap_img = (!empty($img_url)) ? $img_url : $Default_Img;
                }else if(!empty($item['Imagesource']['id'])){
                    $image_id = $item['Imagesource']['id'];
                    $ap_img = wp_get_attachment_image_src($image_id, $ImageSize);
                    $ap_img = !empty($ap_img[0]) ? $ap_img[0] : $Default_Img;
                    $ap_img_rnd = '<img src="'.esc_url($ap_img).'" />';
                }elseif(!empty($attributes['Audioimage']['id'])) {	
                    $image_id = $attributes['Audioimage']['id'];
                    $ap_img = wp_get_attachment_image_src($image_id, $ImageSize);					
                    $ap_img = !empty($ap_img[0]) ? $ap_img[0] : $Default_Img;
                    $ap_img_rnd = '<img src="'.esc_url($ap_img).'" />';
                }else{
                    $ap_img = $Default_Img;
                    $ap_img_rnd = $thumb_img_Render;
                }
            }

            $ap_playlist .= '<div class="tpgb-playlist" audioURL="'.esc_url($audiourl).'" artist="'.wp_kses_post($item['author']).'" data-thumb="'.esc_url($thumb_img).'">'.wp_kses_post($item['title']).'</div>';
            $i++;
        }
    $ap_playlist.='</div>';
	
	$trackDetails = '<div class="trackDetails text-center">'.wp_kses_post($ap_trackdetails_title).wp_kses_post($ap_track_txt).wp_kses_post($ap_trackdetails_artist).'</div>';
	
	$audio_player ='<div class="tpgb-audio-player tpgb-block-'.esc_attr($block_id).' '.esc_attr($Apstyle).' '.esc_attr($blockClass).'" data-id="tpgb-block-'.esc_attr($block_id).'" data-style="'.esc_attr($Apstyle).'" data-apvolume="'.esc_attr($DefaultVolume).'">';

        $audio_player .='<div class="tpgb-audioplay-wrap tpgb-relative-block '.esc_attr($Apstyle).'">';
		
            if($Apstyle == 'style-1'){
                $audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
                    $audio_player .= $ap_playlisticon;
                    $audio_player .= $trackDetails;
                    $audio_player .= $contorls;
                    $audio_player .= $ap_volume;					
                    $audio_player .= $ap_tracker;
                $audio_player .= '</div>';
                $audio_player .= $ap_playlist;
            }else if($Apstyle == 'style-2'){
                $audio_player .='<div class="tpgb-player tpgb-relative-block text-center">';
                    $audio_player .='<div class="main-wrapper-style">';
                        $audio_player .='<div class="controls">';
                            $audio_player .= $ap_play_pause;
                        $audio_player .='</div>';
                        $audio_player .= $ap_tracker;
                        $audio_player .= $ap_volume;
                    $audio_player .='</div>';
                $audio_player .='</div>';
                $audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-3'){
               $audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
					$audio_player .= $ap_playlisticon;
					$audio_player .= '<div class="trackimage">'.$ap_img_rnd.'</div>';
					$audio_player .= $trackDetails;
					$audio_player .=  $contorls;
					$audio_player .= '<div class="ap-time-seek-vol">'.$ap_volume;	
					$audio_player .= '<div class="ap-time">'.$ap_currenttime;
					$audio_player .= $ap_endtime.'</div>';
					$audio_player .= $ap_tracker;
				$audio_player .= '</div></div>';
				$audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-4'){
				$audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
						$audio_player .= $ap_playlisticon;
						$audio_player .= '<div class="ap-title-art">';
						$audio_player .= wp_kses_post($ap_trackdetails_title) .wp_kses_post($ap_trackdetails_artist);
						$audio_player .= '</div>';
						$audio_player .= '<div class="main-wrapper-style">';
							$audio_player .= '<div class="controls">';
								$audio_player .= $ap_play_pause;
							$audio_player .= '</div>';
							$audio_player .= $ap_tracker;
							$audio_player .= $ap_volume;
						$audio_player .= '</div>';
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-5'){
				$audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
						$audio_player .= $ap_playlisticon;						
						$audio_player .= '<div class="ap-st5-img"></div>';						
						$audio_player .= '<div class="ap-st5-content">';
								$audio_player .= '<div class="ap-controls-track">';
                                    $audio_player .= '<div class="controls">'.$ap_play_pause;
                                        $audio_player .= '<div class="ap-nextprev"> '.wp_kses_post($ap_rew).wp_kses_post($ap_fwd).' </div>';
                                    $audio_player .= '</div>';
								$audio_player .= $ap_tracker.'</div>';
								$audio_player .= $trackDetails;
						$audio_player .= '</div>';
						$audio_player .= $ap_volume;
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-6'){
				$audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
						$audio_player .= $ap_playlisticon;
							$audio_player .= '<div class="ap-st5-img">';
                                $audio_player .= '<div class="controls">'.$ap_play_pause; 
                                    $audio_player .= '<div class="ap-nextprev"> '.wp_kses_post($ap_rew).wp_kses_post($ap_fwd).' </div>';
                                $audio_player .= '</div>';
                            $audio_player .= '</div>';
						    $audio_player .= '<div class="ap-st5-content">';								
								$audio_player .= $trackDetails;						
							    $audio_player .= $ap_tracker;								
						    $audio_player .= '</div>';
						$audio_player .= $ap_volume;
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-7'){
                $audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
                    $audio_player .= $contorls;
                    $audio_player .= $ap_tracker;
                    $audio_player .= '<div class="ap-time-title"> '.wp_kses_post($ap_currenttime).wp_kses_post($ap_trackdetails_title).wp_kses_post($ap_endtime).' </div>';
                $audio_player .= '</div>';
                $audio_player .= $ap_playlist;
			}else if($Apstyle == 'style-8'){
				$audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
					$audio_player .= '<div class="tpgb-player-bg-img"></div>';					
					    $audio_player .= '<div class="trackimage">'.$ap_img_rnd.'</div>';
					    $audio_player .= '<div class="trackDetails text-center">'.wp_kses_post($ap_trackdetails_title).wp_kses_post($ap_trackdetails_artist).'</div>';
					    $audio_player .= $contorls;
					    $audio_player .= $ap_tracker;
					$audio_player .= '<div class="ap-time-seek-vol">';
					    $audio_player .= '<div class="ap-time">'.$ap_currenttime;
					$audio_player .= $ap_endtime.'</div>';
				$audio_player .= '</div></div>';
				$audio_player .= $ap_playlist;
			}else if($Apstyle=='style-9'){
				$audio_player .= '<div class="tpgb-player tpgb-relative-block text-center">';
					$audio_player .= '<div class="tpgb-player-hover">';
					$audio_player .= '<div class="tpgb-player-bg-img" >';
                        $audio_player .= '<div class="trackDetails text-center">'.wp_kses_post($ap_trackdetails_title).wp_kses_post($ap_trackdetails_artist).'</div>';
                    $audio_player .= '</div>';
					$audio_player .= $contorls;
				$audio_player .= '</div>';  
				$audio_player .= '</div>';
				$audio_player .= $ap_playlist;
			}
        $audio_player .='</div>';

    $audio_player .='</div>';
	
	$audio_player = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $audio_player);
	
    return $audio_player;
}

function tpgb_tp_audio_player_render() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
        'Apstyle' => [
            'type' => 'string',
            'default' => 'style-1',	
        ],
        'Aprepeater' => [
            'type'=> 'array',
            'repeaterField' => [
                (object) [
                    'title' => [
                        'type' => 'string',
                        'default' => 'Shape of You'
                    ],
                    'author' => [
                        'type' => 'string',
                        'default' => 'Ed Sheeran'
                    ],                    
                    'AudSource' => [
                        'type' => 'string',
                        'default' => 'url',	
                    ],
                    'sorself'=> [
                        'type' => 'object',
                        'default' => [
                            'url' => '',
                            'Id' => '',
                        ],
                    ], 
                    'sorurl'=>[
                        'type'=> 'object',
                        'default'=> [
                            'url' => '',
                            'target' => '',
                            'nofollow' => ''
                        ],
                    ],
                    'Imagesource'=>[
                        'type' => 'object',
                        'default' => [
                            'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
                            'Id' => '',
                        ],
                    ],
                ],
            ],
            'default' => [
                ['_key' => '1','title'=>'Shape of You','author'=>'Ed Sheeran','AudSource'=>'url','Imagesource'=>['url'=>TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg']],
            ],
        ],
        'Audioimage' => [
            'type' => 'object',
            'default' => [
                'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
                'Id' => '',
            ],
        ],
        'ImageSize' => [
            'type' => 'string',
            'default' => 'thumbnail',	
        ],
        'SplitText' => [
            'type'=> 'string',
            'default'=> 'by',
        ],
        'MaxWidth' => [
            'type' => 'object',
            'default' => [ 'md' => '', "unit" => 'px' ],
            'style' => [
                (object) ['selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap{max-width:{{MaxWidth}};margin:0 auto;}'],
            ],
			'scopy' => true,
        ],
        'DefaultVolume' => [
            'type' => 'string',
            'default' => '',
        ],
        
        'SongFont' => [
            'type'=> 'object',
            'default'=> (object) [
				'openTypography' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackDetails .title,{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player .title',
                ],
            ],
			'scopy' => true,
        ],
        'TitleColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackDetails .title,{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player .title{color:{{TitleColor}};}',
                ],
            ],
			'scopy' => true,
        ],

        'AuthorFont' => [
            'type'=> 'object',
            'default'=> (object) [
				'openTypography' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2'],
                                    (object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-7']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackDetails .artist,{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player .artist',
                ],
            ],
			'scopy' => true,
        ],
        'Authorcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2'],
                                    (object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-7']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackDetails .artist,{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player .artist{color:{{Authorcolor}};}',
                ],
            ],
			'scopy' => true,
        ],

        'SplitFont' => [
            'type'=> 'object',
            'default'=> (object) [
				'openTypography' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackDetails .splitTxt,{{PLUS_WRAP}} .tpgb-audioplay-wrap .splitTxt',
                ],
            ],
			'scopy' => true,
        ],
        'SpliTextColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
               (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player .trackDetails .splitTxt{color:{{SpliTextColor}};}',
                ],
            ],
			'scopy' => true,
        ],

        'CBGcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .controls{background:{{CBGcolor}};}'
                ],
            ],
			'scopy' => true,
        ],
        'CBGRadiuss9' => [
            'type' => 'object',
            'default' => (object) ['md' => [ "top" => '', "right" => '', "bottom" => '', "left" => ''],"unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .controls{border-radius:{{CBGRadiuss9}};}',
                ],
            ],
			'scopy' => true,
        ],
        'IconColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2'],
                                    (object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-4']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlistIcon,{{PLUS_WRAP}} .tpgb-audioplay-wrap .volumeIcon .vol-icon-toggle,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls {color:{{IconColor}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .tpgb-player:hover .controls {color:{{IconColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'IconSize' => [
            'type' => 'object',
            'default' => ['md' => '',"unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-2'],
                                    (object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-4']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlistIcon,{{PLUS_WRAP}} .tpgb-audioplay-wrap .volumeIcon .vol-icon-toggle,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .rew,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .fwd,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause{font-size:{{IconSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlayPausecolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) ['selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause{color:{{PlayPausecolor}}; }'],
            ],
			'scopy' => true,
        ],
        'PlayPauseSize' => [
            'type' => 'object',
            'default' => ['md' => '',"unit" => 'px'],
            'style' => [
                (object) ['selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause{font-size:{{PlayPauseSize}};}'],
            ],
			'scopy' => true,
        ],
        'bgsize' => [
            'type' => 'object',
            'default' => ['md' => '',"unit" => 'px'],
            'style' => [
                (object) [                    
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,
                    {{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause{width:{{bgsize}};height:{{bgsize}};line-height:{{bgsize}}}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-ap-pp{width:{{bgsize}};height:{{bgsize}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-5 .controls .tpgb-ap-pp{width:{{bgsize}};height:{{bgsize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'bgtype' => [
            'type' => 'object',
            'default' => (object) [
				'openBg'=> 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4','style-6','style-7','style-9'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-5','style-8']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .tpgb-ap-pp'
                ],                
            ],
			'scopy' => true,
        ],
        'BackgroundType' => [
            'type' => 'object',
            'default' =>  (object) [ 'openBorder' => 0, 'color' => '#fff' ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4','style-7','style-9'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-ap-pp',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .tpgb-ap-pp'
                ],
            ],
			'scopy' => true,
        ],
        'BorderRadius' => [
            'type' => 'object',
            'default' => (object) ['md' => [ "top" => '', "right" => '', "bottom" => '', "left" => ''],"unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-4','style-6','style-7','style-9'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .play,{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .pause{border-radius:{{BorderRadius}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-ap-pp{border-radius:{{BorderRadius}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls .tpgb-ap-pp{border-radius:{{BorderRadius}};}'
                ],
            ],
			'scopy' => true,
        ],
        
        'PlIconcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlistIcon{color:{{PlIconcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlIconSize' => [
            'type' => 'object',
            'default' => [ "md" => '', "unit" => 'px' ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlistIcon{font-size:{{PlIconSize}}; }',
                ],
            ],
			'scopy' => true,
        ],
      
        'VolSize' => [
            'type' => 'object',
            'default' => ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .volumeIcon .vol-icon-toggle{font-size:{{VolSize}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'VolIcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .volumeIcon .vol-icon-toggle{color:{{VolIcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'VolScolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .volume.ui-widget-content{background:{{VolScolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'VolSBgColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-volume-bg{background:{{VolSBgColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'VolSRangeColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .volume .ui-slider-range{background:{{VolSRangeColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CBoxShadow' => [
            'type' => 'object',
            'default' => (object) [
				'openShadow' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-2','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-volume-bg',
                ],
            ],
			'scopy' => true,
        ],
        
        'TTimeSize' => [
            'type' => 'object',
            'default' => [ "md" => '', "unit" => 'px' ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-3']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-3 .currenttime, 
                            {{PLUS_WRAP}} .tpgb-audioplay-wrap.style-3 .durationtime{font-size:{{TTimeSize}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TTimecolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) ['condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-3']],
                        'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-3 .ap-time .currenttime,
                            {{PLUS_WRAP}} .tpgb-audioplay-wrap.style-3 .ap-time .durationtime{color:{{TTimecolor}};}',
                ],
            ],
			'scopy' => true,
        ],

        'TTWidth' => [
            'type' => 'object',
            'default' => ["unit" => '%'],
            'style' => [                
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-5','style-6','style-7','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tracker{width:{{TTWidth}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-2','style-4'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .main-wrapper-style{width:{{TTWidth}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-7' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-7 .ap-time-title{width:{{TTWidth}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-8','style-7'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .controls{width:{{TTWidth}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-8','style-3'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .ap-time-seek-vol{width:{{TTWidth}};}'
                ],
            ],
			'scopy' => true,
        ],
        'TDotColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .ui-slider .ui-slider-handle{background:{{TDotColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Trackcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tracker.ui-widget-content{background:{{Trackcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TFillcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tracker .ui-slider-range{background:{{TFillcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'TIBorder' => [
            'type' => 'object',
            'default' => (object) [
				'openBorder' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackimage img'
                ],   
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-5 .ap-st5-img'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .tpgb-player-bg-img'
                ],
            ],
			'scopy' => true,
        ],
        'TIBorderR' => [
            'type' => 'object',
            'default' => (object) ["unit" => '%'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackimage img{border-radius:{{TIBorderR}};}'
                ],   
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-5 .ap-st5-img{border-radius:{{TIBorderR}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .tpgb-player-bg-img{border-radius:{{TIBorderR}};}'
                ],
            ],
			'scopy' => true,
        ],
        'TIBoxShadow' => [
            'type' => 'object',
            'default' => (object) [
				'openShadow' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-3','style-8'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .trackimage img'
                ],   
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-5 .ap-st5-img'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-9 .tpgb-player-bg-img'
                ],
            ],
			'scopy' => true,
        ],

        'PlPadding' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist{padding:{{PlPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlIMargin' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist{margin:{{PlIMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlOMargin' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist{margin:{{PlOMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PLTypography' => [
            'type'=> 'object',
            'default' => (object) [
				'openTypography' => 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist',
                ],
            ],
			'scopy' => true,
        ],
        'NormalColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist{color:{{NormalColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'NormalTop' => [
            'type' => 'object',
            'default' => ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist{margin-top:{{NormalTop}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlBgCr' => [
            'type' => 'object',
            'default' => (object) [
				'openBg'=> 0,
			],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist'
                ],
            ],
			'scopy' => true,
        ],
        'ActiveColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist.active{color:{{ActiveColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'ActiveTop' => [
            'type' => 'object',
            'default' => ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist.active{margin-top:{{ActiveTop}};}',
                ],
            ],
			'scopy' => true,
        ],
		'HColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist:hover .tpgb-playlist{color:{{HColor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'HAColor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6'] ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist:hover .tpgb-playlist.active{color:{{HAColor}}; }',
                ],
            ],
			'scopy' => true,
        ],
        'HAPlaBgcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist:hover .tpgb-playlist.active{background-color:{{HAPlaBgcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
		
        'PlaBgcolor' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist .tpgb-playlist.active{background-color:{{PlaBgcolor}};}',
                ],
            ],
			'scopy' => true,
        ],
        'Plborder' => [
            'type' => 'object',
            'default' => '',            
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist'
                ],
            ],
			'scopy' => true,
        ],
        'PlborderR' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist{border-radius:{{PlborderR}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlBoxshadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-1','style-3','style-4','style-5','style-6']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .playlist'
                ],
            ],
			'scopy' => true,
        ],

        'PlBgType' => [
            'type' => 'object',
            'default' => (object) [
                'openBg'=> 0,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' =>'style-9' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player'
                ],
            ],
			'scopy' => true,
        ],
        'PBPadding' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => ['style-5' , 'style-7', 'style-8']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player{padding:{{PBPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PBMargin' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player{margin:{{PBMargin}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PlBorderType' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-9']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap .tpgb-player'
                ],
            ],
			'scopy' => true,
        ],
        'PLBgBordeR' => [
            'type' => 'object',
            'default' => (object) ["unit" => 'px'],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => [ 'style-5','style-6', 'style-9']]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-player{border-radius:{{PLBgBordeR}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-6']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-player .ap-st5-content{border-radius:{{PLBgBordeR}};}'
                ],
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-5']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-player .ap-st5-img{border-radius:{{PLBgBordeR}};}'
                ],
            ],
			'scopy' => true,
        ],
        'PBGboxshadow' => [
            'type' => 'object',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '!=', 'value' => 'style-9']],
                    'selector' => '{{PLUS_WRAP}} .tpgb-player'
                ],
            ],
			'scopy' => true,
        ],
        'PBGCSSFilters' => [
            'type' => 'object',
            'default' => [
                'openFilter' => true,
		        'blur' => 8,
            ],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'Apstyle', 'relation' => '==', 'value' => 'style-8' ]],
                    'selector' => '{{PLUS_WRAP}} .tpgb-audioplay-wrap.style-8 .tpgb-player-bg-img',
                ],
            ],
			'scopy' => true,
        ],
    ];
    $attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);

    register_block_type( 'tpgb/tp-audio-player', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_audio_player_callback'
    ));
}
add_action( 'init', 'tpgb_tp_audio_player_render' );