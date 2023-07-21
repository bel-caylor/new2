<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

   $PopupLinkTar = '';
    if( $PopupOption == "GoWebsite" ){
		$PopupLinkTar .= 'href="'.esc_url($videoURL).'"';
        $PopupLinkTar .= ' target=_blank" rel="noopener noreferrer"';
       
    }

    if(!empty($ImageURL)){ 
        if( $style == "style-1" || $style == "style-2" ){
            if( $PopupOption == "Donothing" || $PopupOption == "GoWebsite" ){
                echo '<a '.$PopupLinkTar.' class="tpgb-soc-img-cls tpgb-relative-block" aria-label="'.esc_attr__('Post URL','tpgb').'">';
					if(preg_match_all( '/https:\/\/video.(.*)/' , $ImageURL, $matches )){
						if(!empty($matches) && !empty($matches[0])){
							echo '<video class="tpgb-post-thumb tpgb-feed-video">';
								echo '<source src="'.esc_url($ImageURL).'" type="video/mp4">';
							echo '</video>';
						}else{
							echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Post Thumbnail','tpgb').'"/>';
						}
					}else{
						echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Post Thumbnail','tpgb').'"/>';
					}
					
                echo '</a>';
            }else if( $PopupOption == "OnFancyBox" ){
                echo '<a href="javascript:;" '.$FancyBoxJS.' class="tpgb-soc-img-cls tpgb-relative-block" data-src="#Fancy-'.esc_attr($PopupSylNum).'" aria-label="'.esc_attr__('Popup Gallery','tpgb').'">';
                    if(preg_match_all( '/https:\/\/video.(.*)/' , $ImageURL, $matches )){
						if(!empty($matches) && !empty($matches[0])){
							echo '<video class="tpgb-post-thumb tpgb-feed-video">';
								echo '<source src="'.esc_url($ImageURL).'" type="video/mp4">';
							echo '</video>';
						}else{
							echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Post Thumbnail','tpgb').'"/>';
						}
					}else{
						echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Post Thumbnail','tpgb').'"/>';
					}
                echo '</a>';
            }
        }else if( $style == "style-3" || $style == "style-4" ){ 
            if( $PopupOption == "Donothing" || $PopupOption == "GoWebsite" ){
                echo '<a '.$PopupLinkTar.' class="tpgb-image-link tpgb-soc-img-cls tpgb-relative-block" '.$FancyBoxJS.' aria-label="'.esc_attr__('Post URL','tpgb').'"></a>';
            }else if( $PopupOption == "OnFancyBox" ){
                echo '<a href="javascript:;" class="tpgb-image-link tpgb-soc-img-cls tpgb-relative-block" '.$FancyBoxJS.' data-src="#Fancy-'.esc_attr($PopupSylNum).'" aria-label="'.esc_attr__('Popup Gallery','tpgb').'"></a>';
            }
        }
    }