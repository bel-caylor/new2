<?php
if($FancyStyle == 'style-1'){
	include TPGBP_INCLUDES_URL."social-feed/fancybox-feed-style-1.php";
}else if($FancyStyle == 'style-2'){
	include TPGBP_INCLUDES_URL."social-feed/fancybox-feed-style-2.php";
}else{
	if(empty($FbAlbum)){
		$PopupTarget=$PopupLink='';
		if( $PopupOption == "Donothing" ){
			$videoURL = '';
		}else if( $PopupOption == "GoWebsite" ){
			$PopupTarget = 'target=_blank rel="noopener noreferrer"';
			$PopupLink = 'href="'.esc_url($videoURL).'"';
		}else if( $PopupOption == "OnFancyBox" ){
			$PopupLink = 'href="'.esc_url($videoURL).'"';
		}
	}

	if( $selectFeed == 'Facebook' && !empty($FbAlbum) ){
		$ij=0;
		if(!empty($videoURL)){
			foreach ($videoURL as $fdata){ 
				$AImg = ( !empty($fdata['images']) && !empty($fdata['images'][0]['source']) ) ? $fdata['images'][0]['source'] : ''; 
				if($ij == 0){ ?>
					<a href="<?php echo esc_url($AImg); ?>" <?php echo $FancyBoxJS; ?> aria-label="<?php echo esc_attr__('Facebook Post','tpgbp'); ?>">
						<img class="reference-thumb tpgb-post-thumb" src="<?php echo esc_url($ImageURL); ?>" alt="<?php echo esc_attr__('Facebook Image','tpgbp'); ?>"/>
					</a>
				<?php }else{ ?>
					<a href="<?php echo esc_url($AImg); ?>" <?php echo $FancyBoxJS; ?> aria-label="<?php echo esc_attr__('Facebook Post','tpgbp'); ?>">
						<img class="hidden-image" src="<?php echo esc_url($AImg); ?>" alt="<?php echo esc_attr__('Facebook Image','tpgbp'); ?>"/>
					</a>
				<?php  }
				$ij++;
			}
		}
	}else if( $selectFeed == 'Instagram' && $IGGP_Type == 'Instagram_Graph' ){
		if( !empty($ImageURL) ){
			if( $Type == 'CAROUSEL_ALBUM' && !empty($IGGP_CAROUSEL)){
				foreach ($IGGP_CAROUSEL as $key => $IGGP){
					$IGGP_MediaType = !empty($IGGP['IGGPImg_Type']) ? $IGGP['IGGPImg_Type'] : 'IMAGE'; 
					$IGGP_MediaURl = !empty($IGGP['IGGPURL_Media']) ? $IGGP['IGGPURL_Media'] : ''; 
					
					$IGGP_CAROUSEL_Class='';
					if($key != 0){
						$IGGP_CAROUSEL_Class = 'IGGP_CAROUSEL_Hidden';
					}
					echo '<a href="'.esc_url($IGGP_MediaURl).'" '.$FancyBoxJS.' data-thumb="'.esc_url($IGGP_MediaURl).'" class="tpgb-soc-img-cls tpgb-relative-block '.esc_attr($IGGP_CAROUSEL_Class).'" aria-label="'.esc_attr__('Instagram Post','tpgbp').'">';
						if($key == 0){
							if($IGGP_MediaType == 'IMAGE' && $style!='style-4'){
								echo '<img class="tpgb-post-thumb" src="'.esc_url($IGGP_MediaURl).'" alt="'.esc_attr__('Instagram Image','tpgbp').'"/>';
							}else if($IGGP_MediaType == 'VIDEO' && $style!='style-4'){
								echo '<video class="tpgb-post-thumb tpgb-feed-video">';
									echo '<source src="'.esc_url($IGGP_MediaURl).'" type="video/mp4">';
								echo '</video>';
							}
						}
					echo '</a>';
					echo $IGGP_Icon;
				}
			}else{
				if($style == "style-1" || $style == "style-2"){ 
					echo '<a '.$PopupLink.$PopupTarget.$FancyBoxJS.' class="tpgb-soc-img-cls tpgb-relative-block" aria-label="'.esc_attr__('Instagram Post','tpgbp').'">';
						echo $IGGP_Icon;
						echo '<img class="tpgb-post-thumb" src="'.esc_url($ImageURL).'" alt="'.esc_attr__('Instagram Image','tpgbp').'"/>';
					echo '</a>';
				}else if($style == "style-3" || $style == "style-4"){
					echo $IGGP_Icon;
					echo '<a '.$PopupLink . $PopupTarget . $FancyBoxJS.' class="tpgb-image-link" aria-label="'.esc_attr__('Instagram Post','tpgbp').'"></a>';
				}
			}
		}
	}else{
		if( ($Type == 'video' || $Type == 'photo') && (!empty($ImageURL)) ){
			if($style == "style-1" || $style == "style-2"){ ?> 
				<a <?php echo $PopupLink . $PopupTarget . $FancyBoxJS; ?> class="tpgb-soc-img-cls tpgb-relative-block" aria-label="<?php echo esc_attr__('Social Media Post','tpgbp'); ?>">
					<?php echo $IGGP_Icon ?>
					<img class="tpgb-post-thumb" src="<?php echo esc_url($ImageURL); ?>" alt="<?php echo esc_attr__('Social Media Image','tpgbp'); ?>"/>
				</a>
			<?php }else if($style == "style-3" || $style == "style-4"){
				echo $IGGP_Icon;
				echo '<a '.$PopupLink . $PopupTarget . $FancyBoxJS.' class="tpgb-image-link" aria-label="'.esc_attr__('Social Media Post','tpgbp').'"></a>';
			}
		} 
	}
}
?>