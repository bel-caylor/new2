<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if($selectFeed == 'Vimeo' || $selectFeed == 'Youtube'){ 
        echo '<div class="tpgb-fcb-container">
                <iframe class="responsive-iframe" src="'.esc_url($EmbedURL).'" title="'.esc_attr__('Social Feed','tpgbp').'"></iframe>
             </div>';
    }else if($selectFeed == 'Facebook'){
        if($EmbedURL == 'Alb' && !empty($FbAlbum)){ 
            $ij = 0;
            $albumSize = count($videoURL);
            $uniqId = uniqid('f-');
            if( $albumSize > 1 ){
                foreach ( $videoURL as $index => $fdata ){
                    $AImg = (!empty($fdata['images'])) ? $fdata['images'][0]['source'] : []; 
                    if( $ij == 0 ){
                        echo '<a href="'.esc_url($AImg).'" data-fancybox="'.esc_attr($uniqId).'" aria-label="'.esc_attr__('Facebook Post','tpgbp').'">
                                <img class="reference-thumb tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Facebook Image','tpgbp').'"/>
                            </a>';
                    }else{ 
                        echo '<a href="'.esc_url($AImg).'" data-fancybox="'.esc_attr($uniqId).'" aria-label="'.esc_attr__('Facebook Post','tpgbp').'">
                                <img class="hidden-image" src="'.esc_url($AImg).'" alt="'.esc_attr__('Facebook Image','tpgbp').'"/>
                            </a>';
                    }
                $ij++;
                }
            } else {
                echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Facebook Image','tpgbp').'"/>';
            }
        }else if( $EmbedType == 'video' && empty($FbAlbum) ){
            echo '<div class="tpgb-fcb-container">
                    <iframe class="responsive-iframe" src="'.esc_url($videoURL).'" title="'.esc_attr__('Social Feed','tpgbp').'"></iframe>
                </div>';
        }else {
            echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Facebook Image','tpgbp').'"/>';
        }
    }else if($selectFeed == 'Instagram'){
        if($IGGP_Type == 'Instagram_Graph'){
            if( $Type == "CAROUSEL_ALBUM" ){
				$car=[
					'updateOnMove'	=> true,
					'direction'		=> 'ltr',
					'start'			=> 0,
					'autoplay'		=> false,
					'speed'			=> 1000,
					'drag'			=> true,
					'type'			=> 'slide',
					'pauseOnHover'	=> false,
					'pagination'	=> false,
					'arrows'		=> true,
					'padding'		=> 0,
					'perMove'		=> 1,
					'perPage'		=> 1,
					'perPage'		=> 1,
					'perPage'		=> 1,
					'perPage'		=> 1,
					'focus'			=> 0,
					'type'			=> 'loop'
				];
				$ncar = 'data-splide=\''.json_encode($car).'\'' ;
                echo "<div class='IGGP-wrap tpgb-carousel splide' ".$ncar."> 
                        <div class='IGGP-slider splide__track'>
							<div class='splide__list'>";
							if(!empty($IGGP_CAROUSEL)){
								foreach ($IGGP_CAROUSEL as $key => $IGGP){
									$IGGP_MediaType = !empty($IGGP['IGGPImg_Type']) ? $IGGP['IGGPImg_Type'] : 'IMAGE'; 
									$IGGP_MediaURl = !empty($IGGP['IGGPURL_Media']) ? $IGGP['IGGPURL_Media'] : ''; 
									echo "<div class='slide-item splide__slide'>";
										if($IGGP_MediaType == 'IMAGE'){
											echo "<img src='".esc_url($IGGP_MediaURl)."' class='tpgb-fcb-thumb' data-lazy='".esc_url($IGGP_MediaURl)."' alt='".esc_attr($key)."' >";
										}else if($IGGP_MediaType == 'VIDEO'){
											echo '<video class="tpgb-post-thumb tpgb-feed-video" controls>';
												echo '<source src="'.esc_url($IGGP_MediaURl).'" type="video/mp4">';
											echo '</video>';
										}
									echo "</div>";
								}   
							}
                    echo "</div>
						</div>
                    </div>";
            }else if( $Type == 'IMAGE' ){
                echo '<img class="tpgb-fcb-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Instagram Image','tpgbp').'"/>';
            }else if( $Type == 'VIDEO' ){
                echo '<div class="tpgb-fcb-container">
                        <iframe class="responsive-iframe" src="'.esc_url($videoURL).'" frameborder="0" title="'.esc_attr__('Social Feed','tpgbp').'"></iframe>
                    </div>';
            }
        }else{
            echo '<img class="tpgb-fcb-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Instagram Image','tpgbp').'"/>';
        }
    }else if(!empty($ImageURL)){
        echo '<img class="tpgb-fcb-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Social Media Image','tpgbp').'"/>';
    }
?>