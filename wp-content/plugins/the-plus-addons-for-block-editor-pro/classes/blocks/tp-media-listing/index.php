<?php
/* Block : Media Listing
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_Media_listing_render_callback( $attributes, $content) {
	$Gallery = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$GalleryType = (!empty($attributes['GalleryType'])) ? $attributes['GalleryType'] : 'image';
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$layout = (!empty($attributes['layout'])) ? $attributes['layout'] : 'grid';
	$Imgoption = (!empty($attributes['Imgoption'])) ? $attributes['Imgoption'] : 'normal';
	$NImage = (!empty($attributes['NAddImg'])) ? $attributes['NAddImg'] : '';	
	$ImgEffect = (!empty($attributes['ImgHE'])) ? $attributes['ImgHE'] : 'style-1';
	$columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'md';
	$DisplayTitle = (!empty($attributes['Dtitle'])) ? $attributes['Dtitle'] : false;
	$TitleTag = (!empty($attributes['TitleTag'])) ? $attributes['TitleTag'] : 'h3';
	$Playout = (!empty($attributes['Playout'])) ? $attributes['Playout'] : 'default';
	$ImgRepeater = (!empty($attributes['ImgRepeater'])) ? $attributes['ImgRepeater'] : [];
	$Category = (!empty($attributes['Category'])) ? $attributes['Category'] : false;
	$Categoryclass =( !empty($Category) ? 'tpgb-category-filter' : '');
	$ImgSize = (!empty($attributes['ImgSize'])) ? $attributes['ImgSize'] : 'full';
	$DisContent = (!empty($attributes['Dcontent'])) ? $attributes['Dcontent'] : false;
	
	$Boxlink = (!empty($attributes['Boxlink'])) ? $attributes['Boxlink'] : false;
	$FCusURl = (!empty($attributes['FCusURl'])) ? $attributes['FCusURl'] : false;
	
	$DisBtns4 = (!empty($attributes['DisBtns4'])) ? $attributes['DisBtns4'] : false;
	$Btns4txt = (!empty($attributes['Btns4txt'])) ? $attributes['Btns4txt'] : '';
	
	$TitleFancy = (!empty($attributes['TitleFancy'])) ? $attributes['TitleFancy'] : false;
	$Rowclass = ($layout!='carousel') ? 'tpgb-row' : '';

	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$metrocolumns = isset($attributes['metrocolumns']) ? $attributes['metrocolumns'] : [ 'md' => '3' ] ;
	$metroStyle = isset($attributes['metroStyle']) ? $attributes['metroStyle'] : [ 'md' => 'style-1' ] ;

	$list_layout = '';
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
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$desktop_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$desktop_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-'.esc_attr($columns['xs']);
	}

	// Set Data For Metro Layout
	$metroAttr = []; $total = '';
	if( $layout == 'metro' ){
		if( isset($metrocolumns['md']) && !empty($metrocolumns['md']) ){
			$metroAttr['metro_col'] = $metrocolumns['md'];
		}
		
		if( isset($metrocolumns['sm']) && !empty($metrocolumns['sm']) ){
			$metroAttr['tab_metro_col'] = $metrocolumns['sm'];
		}else if( isset($metrocolumns['md']) && !empty($metrocolumns['md']) ){
			$metroAttr['tab_metro_col'] =  $metrocolumns['md'];
		}

		if( isset($metrocolumns['xs']) && !empty($metrocolumns['xs']) ){
			$metroAttr['mobile_metro_col'] = $metrocolumns['xs'];
		}else if( isset($metrocolumns['sm']) && !empty($metrocolumns['sm']) ){
			$metroAttr['mobile_metro_col'] =  $metrocolumns['sm'];
		}else if( isset($metrocolumns['md']) && !empty($metrocolumns['md']) ){
			$metroAttr['mobile_metro_col'] =  $metrocolumns['md'];
		}

		if( isset($metroStyle['md']) && !empty($metroStyle['md']) ){
			$metroAttr['metro_style'] = $metroStyle['md'];
		}

		if( isset($metroStyle['sm']) && !empty($metroStyle['sm']) ){
			$metroAttr['tab_metro_style'] =  $metroStyle['sm'];
		}else if( isset($metroStyle['md']) && !empty($metroStyle['md']) ){
			$metroAttr['tab_metro_style'] = $metroStyle['md'];
		}

		if( isset($metroStyle['xs']) && !empty($metroStyle['xs']) ){
			$metroAttr['mobile_metro_style'] = $metroStyle['xs'];
		}else if( isset($metroStyle['sm']) && !empty($metroStyle['sm']) ){
			$metroAttr['mobile_metro_style'] = $metroStyle['sm'];
		}else if( isset($metroStyle['md']) && !empty($metroStyle['md']) ){
			$metroAttr['mobile_metro_style'] =  $metroStyle['md'];
		}
		$metroAttr = 'data-metroAttr= \'' .json_encode($metroAttr) . '\' ';
	}

	$Sliderclass = '';
	$carousel_settings='';
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
	
	$fancybox_settings = tpgb_gallery_fancybox($attributes);
	$ji=1;$col=$tabCol=$moCol='';
	$Gallery .= '<div id="'.esc_attr($block_id).'" class="tpgb-block-'.esc_attr($block_id).' tpgb-relative-block  tpgb-gallery-list '.esc_attr($list_layout).' gallery-'.esc_attr($style).' hover-image-'.esc_attr($ImgEffect).' '.esc_attr($Sliderclass).' '.esc_attr($Categoryclass).' '.esc_attr($blockClass).' " data-style="'.esc_attr($style).'" data-id="'.esc_attr($block_id).'" data-layout="'.esc_attr($layout).'" data-fancy-option=\''.json_encode($fancybox_settings).'\' data-splide=\''.json_encode($carousel_settings).'\' '.( $layout == 'metro' ? $metroAttr : '' ).' >';

		$Gallery .= CategoryFilter($attributes);
		
		if( $layout == 'carousel' && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
			$Gallery .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
		}
		$Gallery .= '<div class="'.esc_attr($Rowclass).' post-loop-inner '.($layout == 'carousel' ? 'splide__track' : '').'">';
			if($layout == 'carousel'){
				$Gallery .= '<div class="splide__list">';
			}
			$OptionStyle = [];
			if( $GalleryType == 'image' && $Imgoption == 'normal' ){
				if( !empty($NImage) ){
					$dygallery = end($NImage);

					$OptionStyle = (isset($dygallery['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($dygallery) : $NImage;
				}else{
					$Gallery .= '<h3 class="tpgb-posts-not-found">'.esc_html__('Please select a multiple images Gallery','tpgbp').'</h3>';
				}
			}else if( $GalleryType == 'video' || $Imgoption == 'repeater' ){
				if( !empty($ImgRepeater) ){
					$OptionStyle = $ImgRepeater;
				}else{
					$Gallery .= '<h3 class="tpgb-posts-not-found">'.esc_html__('Please select a multiple images Gallery','tpgbp').'</h3>';
				}
			}
			
			if( is_array($OptionStyle) ){
				if($GalleryType == 'image' && $Imgoption == 'normal') {
					$dataSubjectsValue = array_column($OptionStyle, 'url');
					if (in_array(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg', $dataSubjectsValue)) {
						$OptionStyle = array_splice($OptionStyle,1);
					}

					if( empty($OptionStyle) ){
						$OptionStyle = array( array( 'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg' ,'Id' => '' ) );
					}
				}
			}else{
				$OptionStyle = [ [ 'url' => $OptionStyle  ] ];
			}

			foreach ( $OptionStyle as $index => $ImgData ) {
				$OnlyType='';
				$StyleTitle='';
				$GalleryImage='';
				$ImgUrl=$ImgId=$FancyImg='';
				$VideoUrl='';
				$Customurl=$CustomTarget=$CustomRel='';
				$imgdaata = !empty($ImgData['id']) ? get_post( $ImgData['id']) : '';
				$title=$description=$caption=$link_attr ='';

				/*Media Normal*/
				if( $GalleryType == 'image' && $Imgoption == 'normal' ){
					$ImgUrl=$FancyImg= (!empty($ImgData['url'])) ? $ImgData['url'] :  TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg' ;

					if( !empty($ImgData['id']) ){
						$Imgid = $ImgData['id'];
						$AttImg = wp_get_attachment_image_src($Imgid,$ImgSize);
						$ImgUrl = (!empty($AttImg) && isset($AttImg[0])) ? $AttImg[0] :  TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg';
						if( $layout != 'metro' ){
							if(!empty($AttImg)){
								$OnlyType = wp_get_attachment_image($Imgid,$ImgSize);
							}else{
								$OnlyType = '<img src="'. esc_url($ImgData['url']) .'" alt="'. esc_attr__('gallery','tpgbp') .'" />';
							}
						}else{
							$OnlyType = 'style="background: url('.$ImgUrl.');"';
						}
					}else{
						if( $layout != 'metro' ){
							$OnlyType = '<img src="'. esc_url($ImgUrl) .'" alt="'. esc_attr__('gallery','tpgbp') .'" />';
						}else{
							$OnlyType = 'style="background: url('.$ImgUrl.');"';
						}
						
					}
					if(!empty($imgdaata)){
						if( !empty($Imgid) ){
							$attchUrl = get_post_meta($Imgid, 'tpgb_gallery_url', true);
							if(!empty($attchUrl)){
								$Customurl=get_post_meta($Imgid, 'tpgb_gallery_url', true);
							}else{
								$Customurl="";
							}
						}
					
						$title=!empty($imgdaata) ? $imgdaata->post_title : '';
						$caption=!empty($imgdaata) ? $imgdaata->post_content : '';
					}
					
				}else if( $GalleryType == 'video' || $Imgoption == 'repeater' ){
				/*Repeater*/
					$ImgUrl=$FancyImg= (isset($ImgData['Rimg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['Rimg']) : (!empty($ImgData['Rimg']['url']) ? $ImgData['Rimg']['url'] : '');

					if(isset($ImgData['Rimg']['dynamic'])){
						$ImgUrl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['Rimg']);
						$OnlyType = '<img src="'.esc_url($ImgUrl).'" alt="'.esc_attr__('img','tpgbp').'" />';
					}else if( !empty($ImgData['Rimg']) && !empty($ImgData['Rimg']['id'] )){
						$ImgId = ( !empty($ImgData['Rimg']) ? $ImgData['Rimg']['id'] : '');
						$AttImg = wp_get_attachment_image_src($ImgId,$ImgSize);
						$ImgUrld = (!empty($AttImg)) ? $AttImg[0] : '';
						if( $layout != 'metro' ){
							if(!empty($ImgUrld)){
								$ImgUrl = $ImgUrld;
								$OnlyType = wp_get_attachment_image($ImgId,$ImgSize);
							}else{
								$OnlyType = '<img src="'.esc_url($ImgUrl).'" alt="media" />';
							}
						}else{
							$OnlyType = 'style="background: url('.$ImgUrld.');"';
						}
					}else{
						if( $layout != 'metro' ){
							$OnlyType = '<img src="'.esc_url($ImgUrl).'" alt="'.esc_attr($ImgId).'" />';
						}else{
							$OnlyType = 'style="background: url('.$ImgUrl.');"';
						}
					}
					
					$title = (!empty($ImgData['Rtitle']) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($ImgData['Rtitle']) : '');
					$caption = (!empty($ImgData['RCaption']) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($ImgData['RCaption']) : '');
					$RlconImg = (!empty($ImgData['RselImg']) ? $ImgData['RselImg'] :'none');	
					$RIcon = (!empty($ImgData['RIcon']) ? $ImgData['RIcon'] : '');
					$RIconimg = (isset($ImgData['RIimg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['RIimg']) : (!empty($ImgData['RIimg']['url']) ? $ImgData['RIimg']['url'] : '');
					$Customurl = (isset($ImgData['Rurl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['Rurl']) : (!empty($ImgData['Rurl']['url']) ? $ImgData['Rurl']['url'] : '');
					$CustomTarget = ( !empty($ImgData['Rurl']) && isset($ImgData['Rurl']['target']) && !empty($ImgData['Rurl']['target'])  ? 'target="_blank"' : '');
					$CustomRel = ( !empty($ImgData['Rurl']) && isset($ImgData['Rurl']['nofollow']) && !empty($ImgData['Rurl']['nofollow'])  ? 'rel="nofollow"' : '');
					
					
					if(!empty($ImgData['Rurl'])){
						$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($ImgData['Rurl']);
					}
					
					if( $GalleryType == 'video' ){
						if( $ImgData['VSource'] == 'self-hosted' ){
							$VideoUrl = (isset($ImgData['RVideo']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['RVideo']) : (!empty($ImgData['RVideo']['url']) ? $ImgData['RVideo']['url'] : '');
						}else if( $ImgData['VSource'] == 'youtube' ){
							$ytUrl = (!empty($ImgData['YouTubeId'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($ImgData['YouTubeId']) : '';
							$VideoUrl = ( !empty($ytUrl) ) ? 'https://www.youtube.com/embed/'.$ytUrl : '';
						}else if( $ImgData['VSource'] == 'vimeo' ){
							$vimeoUrl = (!empty($ImgData['VimeoId'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($ImgData['VimeoId']) : '';
							$VideoUrl = ( !empty($vimeoUrl) ) ? 'https://player.vimeo.com/video/'.$vimeoUrl : '';
						}											
						$Customurl = (isset($ImgData['Rurl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($ImgData['Rurl']) : (!empty($ImgData['Rurl']['url']) ? $ImgData['Rurl']['url'] : '');
						$ImgUrl = ( !empty($VideoUrl) ? $VideoUrl : $ImgUrl );
						$FancyImg = ( !empty($VideoUrl) ? $VideoUrl : $FancyImg );
					}
				}

				$category_filter=$loop_category='';
				if( !empty($Category) && !empty($ImgData['RCategory'])  && $layout!='carousel' ){
					$loop_category = explode(',', $ImgData['RCategory']);
					foreach( $loop_category as $category ) {
						$category = Media_createSlug($category);
						$category_filter .=' '.esc_attr($category).' ';
					}
				}
				
				$FancyBoxJS = '';
				if($Playout == 'default'){
					$FancyBoxJS = ' data-fancybox = "'.esc_attr($block_id).'" ';
				}
				$TitlePopup = (!empty($TitleFancy)) ? 'data-caption="'.esc_attr($title).'"' : '';
				$ThumFancyImg = (!empty($ImgUrl)) ? '<img class="fancy-img" src="'.esc_url($ImgUrl).'" />' : '';


				if( $layout == 'metro' ){
					if( ( isset($metrocolumns['md']) && !empty($metrocolumns['md']) ) && ( isset($metroStyle['md']) && !empty($metroStyle['md']) ) ){
						$col= Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($ji , $metrocolumns['md'] , $metroStyle['md'] );
					}
					if( ( isset($metrocolumns['sm']) && !empty($metrocolumns['sm']) ) && ( isset($metroStyle['sm']) && !empty($metroStyle['sm']) ) ){
						$tabCol = Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($ji , $metrocolumns['sm'] , $metroStyle['sm'] );
					}
					if( ( isset($metrocolumns['xs']) && !empty($metrocolumns['xs']) ) && ( isset($metroStyle['xs']) && !empty($metroStyle['xs']) ) ){
						$moCol = Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($ji , $metrocolumns['xs'] , $metroStyle['xs'] );
					}
				}

				$Gallery .= '<div class="grid-item metro-item-'.esc_attr($index).' '.($layout=='carousel' ? 'splide__slide' : $desktop_class).' '.$category_filter.' '.( $layout=='metro' ? ' tpgb-metro-'.esc_attr($col).' '.( !empty($tabCol) ? ' tpgb-tab-metro-'.esc_attr($tabCol).''  : '' ).' '.( !empty($moCol) ? ' tpgb-mobile-metro-'.esc_attr($moCol).''  : '' ).' ' : '' ).' ">';

					if(!empty($Boxlink) && $Playout != 'no'){
						$Gallery .= '<a href="'.(!empty($FCusURl) ? esc_url($Customurl) : esc_url($FancyImg) ).'" class="tpgb-gallery-list-content tpgb-trans-easeinout" '.$FancyBoxJS.' '.$TitlePopup.' '.$link_attr.' aria-label="'.esc_attr($title).'">';
					}else{
						if( $style != 'style-4' && !empty($Customurl) ){
							$Gallery .= '<a href="'. esc_url($Customurl).'" class="tpgb-gallery-list-content tpgb-trans-easeinout" '.$CustomTarget.'  '.$CustomRel.'  '.$TitlePopup.' '.$link_attr.' aria-label="'.esc_attr($title).'">';
						}else{
							if($style == 'style-4' && !empty($Customurl) && !empty($FCusURl)){
								$Gallery .= '<a href="'. esc_url($Customurl).'" class="tpgb-gallery-list-content tpgb-trans-easeinout" '.$CustomTarget.'  '.$CustomRel.'  '.$TitlePopup.' '.$link_attr.'  aria-label="'.esc_attr($title).'">';
							}else{
								$Gallery .= '<div class="tpgb-gallery-list-content tpgb-trans-easeinout">';
							}
						}
					}

						$Gallery .= '<div class="post-content-image">';
							
							if( $layout !== 'metro'  ){
								$GalleryImage .= '<div class="tpgb-gallery-image tpgb-trans-easeinout-before">';
									$GalleryImage .= '<span class="thumb-wrap">'.$OnlyType.'</span>';
								$GalleryImage .= '</div>';
							}else{
								$GalleryImage .= '<div class="tpgb-metro-gallery-image" '.$OnlyType.' >';
								$GalleryImage .= '</div>';
							}
							$TagAImg = ''; 
							if(!empty($Boxlink) && $Playout != 'no' ){
								$TagAImg .= $GalleryImage;
							}else{
								if($Playout == 'default'){
									$S4fancy = ($style == 'style-4') ? $FancyBoxJS :'';
									$TagAImg .= '<a href="'.esc_url($ImgUrl).'" '.$S4fancy.' target="_blank">'.$GalleryImage.'</a>';
								}else{
									$TagAImg .= $GalleryImage;
								}
							}

							if($style == 'style-1' || $style == 'style-3'){
								$Gallery .= $GalleryImage;
							}elseif($style == 'style-2'){
								$Gallery .= $TagAImg;
							}elseif($style == 'style-4'){
								$Gallery .= $TagAImg;
							}
						$Gallery .= '</div>';
					
						$Gallery .= '<div class="post-content-center">';

							if( !empty($title) && $DisplayTitle ){
								$StyleTitle .= '<'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($TitleTag).' class="post-title tpgb-trans-easeinout">';
									$StyleTitle .= wp_kses_post($title);
								$StyleTitle .= '</'.Tpgbp_Pro_Blocks_Helper::validate_html_tag($TitleTag).'>';
							}
							
							$SearchIcon = '';
							if( !empty($attributes['Disicon']) && $attributes['PopupNone'] == 'image' && !empty($attributes['CutIcon']['url']) ){
								$SearchIcon .= $ThumFancyImg;
								$SearchIcon .='<img class="pop-icon" src="'.esc_url($attributes['CutIcon']['url']).'" />';
							}else if( !empty($attributes['Disicon']) && $attributes['PopupNone'] == 'icon' ){
								$SearchIcon .= $ThumFancyImg;
								$SearchIcon .='<i class="'.esc_attr($attributes['PopupIcon']).' pop-icon"></i>';
							}

							$Posthover = '';
							if($style == 'style-1' || $style == 'style-2' || $style == 'style-3'){
								$Posthover .= '<div class="meta-search-icon">';
									if( !empty($Boxlink) || $Playout == 'no' ){
										$Posthover .= '<span>'.$SearchIcon.'</span>';
									}else{
										$Posthover .= '<a href="'.esc_url($FancyImg).'" '.$FancyBoxJS.' '.$TitlePopup.'>'.$SearchIcon.'</a>';
									}
								$Posthover .= '</div>';
							}

							$Addclass ='';
							if($style == 'style-1' || $style == 'style-3' || $style == 'style-4'){
								$Addclass = 'post-hover-content';
							}else if($style == 'style-2'){
								$Addclass = 'post-zoom-icon';
							}

							$Icon = '';
							if( $GalleryType == 'video' || $Imgoption == 'repeater' ){
								if( $RlconImg == 'icon' && !empty($RIcon) ){
									$Icon .= '<div class="gallery-list-icon">';
										$Icon .= '<i class="'.esc_attr($RIcon).'"></i>';
									$Icon .= '</div>';
								}else if( $RlconImg == 'image' && !empty($RIconimg) ){
									$Icon .= '<div class="icon-img-R">';
										$Icon .= '<img src="'.esc_url($RIconimg).'" />';
									$Icon .= '</div>';
								}
							}
							
							$imgcaption = '';
							if( !empty($caption) && !empty($DisContent) ){
								$imgcaption = '<div class="entry-content tpgb-trans-easeinout">'.wp_kses_post($caption).'</div>';
							}

							$Style4URL='';
							if( !empty($DisBtns4) ){
								// if( !empty($Boxlink) && !empty($ImgData['Rurl']['url']) ){
								// 	$Style4URL .= '<div class="gallery-btn-link">'.wp_kses_post($Btns4txt).'</div>';
								// }else{
									$Style4URL .= '<a href="'.esc_url($Customurl).'" class="gallery-btn-link" '.$CustomTarget.' '.$CustomRel.' >'.wp_kses_post($Btns4txt).'</a>';
								//}
							}

							$PostHovEffect = '';
							if($style == 'style-1'){
								$PostHovEffect .= '<div class="'.esc_attr($Addclass).'">';											
									$PostHovEffect .= $Posthover;
									$PostHovEffect .= $Icon;
									$PostHovEffect .= $StyleTitle;												
									$PostHovEffect .= $imgcaption;											
								$PostHovEffect .= '</div>';
							}else if($style == 'style-2'){
								$PostHovEffect .= '<div class="'.esc_attr($Addclass).'">';
									$PostHovEffect .= $Icon;
									$PostHovEffect .= $Posthover;
								$PostHovEffect .= '</div>';
								if( $DisplayTitle || $DisContent ){
									$PostHovEffect .= '<div class="post-content-bottom">';
										$PostHovEffect .= $StyleTitle;
										if( !empty($caption) && $DisContent ){
											$PostHovEffect .= '<div class="post-hover-content">'.$imgcaption.'</div>';
										}
									$PostHovEffect .= '</div>';	
								}
							}else if($style == 'style-3'){
								$PostHovEffect .= '<div class="'.esc_attr($Addclass).'">';
									$PostHovEffect .= $Posthover;
									$PostHovEffect .= $StyleTitle;
									$PostHovEffect .= $imgcaption;
								$PostHovEffect .= '</div>';
							}else if($style == 'style-4'){
								$PostHovEffect .= '<div class="'.esc_attr($Addclass).'">';
									$PostHovEffect .= $Icon;
									$PostHovEffect .= $StyleTitle;
									if( $imgcaption || $Style4URL ){
										$PostHovEffect .= '<div class="entry-content tpgb-trans-easeinout">';
											$PostHovEffect .= wp_kses_post($imgcaption);
											$PostHovEffect .= $Style4URL;
										$PostHovEffect .= '</div>';
									}
								$PostHovEffect .= '</div>';
							}
							$Gallery .= $PostHovEffect;
						$Gallery .= '</div>';

					//$Gallery .= (!empty($Boxlink) && $Playout != 'no' ) ? '</a>' : '</div>';
					if(!empty($Boxlink) && $Playout != 'no'){
						$Gallery .= '</a>';
					}else{
						if( $style != 'style-4' && !empty($Customurl)){
							$Gallery .= '</a>';
						}else{
							if($style == 'style-4' && !empty($Customurl) && !empty($FCusURl)){
								$Gallery .= '</a>';
							}else{
								$Gallery .= '</div>';
							}
						}
					}

				$Gallery .= '</div>';

				$ji++;
			}

			if($layout == 'carousel'){
				$Gallery .= '</div>';
			}
		$Gallery .= '</div>';

	$Gallery .= '</div>';
	
	$Gallery = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $Gallery);
	if( $layout == 'carousel' ){
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		if( !empty($arrowCss) ){
			$Gallery .= $arrowCss;
		}
	}
    return $Gallery;
}

function tpgb_gallery_fancybox($attr){
	$FancyData = (!empty($attr['FancyOption'])) ? json_decode($attr['FancyOption']) : [];

	$button = array();
	if (is_array($FancyData) || is_object($FancyData)) {
		foreach ($FancyData as $value) {
			$button[] = $value->value;
		}
	}

	$fancybox = array();
	$fancybox['loop'] = $attr['LoopFancy'];
	$fancybox['infobar'] = $attr['infobar'];
	$fancybox['arrows'] = $attr['ArrowsFancy'];
	$fancybox['animationEffect'] = $attr['AnimationFancy'];
	$fancybox['animationDuration'] = $attr['DurationFancy'];
	$fancybox['transitionEffect'] = $attr['TransitionFancy'];
	$fancybox['transitionDuration'] = $attr['TranDuration'];
	$fancybox['button'] = $button;
	
	return $fancybox;
}

function CategoryFilter($attributes){
	$category_filter = '';	
	$GalleryType = (!empty($attributes['GalleryType'])) ? $attributes['GalleryType'] : 'image';
	$Imgoption = (!empty($attributes['Imgoption'])) ? $attributes['Imgoption'] : 'normal';
	$ImageRepeater = (!empty($attributes['ImgRepeater'])) ? $attributes['ImgRepeater'] : [];
		
	if( ($GalleryType == 'video' || $Imgoption == 'repeater') && !empty($attributes['Category']) && $attributes['layout'] !=='carousel' ){

		$filter_style = $attributes['CatFilterS'];
		$filter_hover_style = $attributes["FilterHs"];
		$all_filter_category = (!empty($attributes["TextCat"])) ? $attributes["TextCat"] : esc_html__('All','tpgbp');

		$loop_category = array();
		$count_loop = 0;
		
		foreach ( $ImageRepeater as $item ) {
			$RCategory = !empty($item['RCategory']) ? $item['RCategory'] : '';
				if(!empty($RCategory)){
					$loop_category[] = explode(',', $RCategory);
				}
			$count_loop++;
		}		
		$loop_category = Split_Array_Category($loop_category);
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

				if ( ($GalleryType == 'video' || $Imgoption == 'repeater') && !empty($attributes['ImgRepeater']) ) {
					foreach ( $count_category as $key => $value ) {
						$slug = '.'.Media_createSlug($key);	
						$category_post_count='';
						if($filter_style=='style-2' || $filter_style=='style-3'){
							$category_post_count='<span class="tpgb-category-count">'.esc_html($value).'</span>';
						}
						if(!empty($post_category)){
							if(in_array($term->term_id,$post_category)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list" data-filter="'.esc_attr($slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($key).'">'.esc_html($key).'</span></a></div>';
								unset($term);
							}
						}else{
							$category_filter .= '<div class="tpgb-filter-list">';
								$category_filter .= '<a href="#" class="tpgb-category-list" data-filter = "'.esc_attr($slug).'">';
									$category_filter .= $category_post_count;
									$category_filter .=	'<span data-hover="'.esc_attr($key).'">'.esc_html($key).'</span>';
								$category_filter .=	'</a>';
							$category_filter .= '</div>';	
							unset($term);
						}
					}
				}
			$category_filter .= '</div>';
		$category_filter .= '</div>';

	}
	return $category_filter;
}

function Split_Array_Category($array){
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, Split_Array_Category($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  }
	}
	
	return $result; 
}

function Media_createSlug($str, $delimiter = '-'){
	$slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	return $slug;
}

function tpgb_tp_Media_listing() {
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
			'GalleryType' => [
				'type' => 'string',
				'default' => 'image',	
			],
			'style' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'layout' => [
				'type' => 'string',
				'default' => 'grid',
			],
			'Playout' => [
				'type' => 'string',
				'default' => 'default',	
			],
			
			'Imgoption' => [
				'type' => 'string',
				'default' => 'normal',	
			],
			'NAddImg' => [
				'type' => 'array',
				'default' => [
					[ 
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						'Id' => '',
					],
				],
			],
			'ImgRepeater' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'VSource' => [
							'type'=> 'string',
							'default'=> 'self-hosted',
						],
						'RVideo' => [
							'type' => 'object',
							'default' => [
								'url' => '',
								'Id' => '',
							],
						],
						'YouTubeId' => [
							'type'=> 'string',
							'default'=> '2ReiWfKUxIM',
						],
						'VimeoId' => [
							'type'=> 'string',
							'default'=> '87591302',
						],
						'Rimg' => [
							'type' => 'object',
							'default' => [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
								'Id' => '',
							],
						],
						'Rtitle' => [
							'type'=> 'string',
							'default'=> 'Image',
						],
						'RCategory' => [
							'type'=> 'string',
							'default'=> '',
						],
						'RCaption' => [
							'type'=> 'string',
							'default'=> 'I am text block',
						],
						'RselImg' => [
							'type' => 'string',
							'default' => 'none',	
						],
						'RIcon' => [
							'type'=> 'string',
							'default'=> '',
						],
						'RIimg' => [
							'type' => 'object',
							'default' => [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
								'Id' => '',
							],
						],
						'Rurl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
					],
				],
				'default' => [ 
					['Rtitle' =>'Image','RCaption' => 'I am text block','Rimg'=>['url'=>TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'],'VSource'=>'self-hosted','YouTubeId'=>'2ReiWfKUxIM','VimeoId'=>'87591302'],
					['Rtitle' =>'Image','RCaption' => 'I am text block','Rimg'=>['url'=>TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'],'VSource'=>'self-hosted','YouTubeId'=>'2ReiWfKUxIM','VimeoId'=>'87591302']
				],
			],

			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 4,'xs' => 6 ],
			],
			'colMetro' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 3,'xs' => 6 ],
			],
			'MetroSty' => [
				'type' => 'object',
				'default' => [ 'md' => 'style-1','sm' => 'style-1','xs' => 'style-1' ],
			],
			'columnSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',			
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item{padding:{{columnSpace}};}',
					],
				],
			],
			'metrocolumns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 3 ,'xs' => 3 ],
			],
			'metroStyle' => [
				'type' => 'object',
				'default' => [ 'md' => 'style-1','sm' => 'style-1','xs' => 'style-1' ],
			],
			'Dtitle' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'TitleTag' => [
				'type' => 'string',
				'default' => 'h3',	
			],
			'DImgS' => [
				'type' => 'boolean',
				'default' => False,	
			],
			'ImgSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'Dcontent' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'Boxlink' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'FCusURl' => [
				'type' => 'boolean',
				'default' => False,	
			],
			'DisBtns4' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'Btns4txt' => [
				'type'=> 'string',
				'default'=> '+ Learn more',
			],
			'BtnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'DisBtns4', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-4 .gallery-btn-link',
					],
				],
				'scopy' => true,
			],
			'BtnCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'DisBtns4', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-4 .gallery-btn-link{color:{{BtnCr}};border-bottom:1px solid {{BtnCr}}; }',
					],
				],
				'scopy' => true,
			],

			'Disicon' => [
				'type' => 'boolean',
				'default' => true,	
			],				
			'PopupNone' => [
				'type' => 'string',
				'default' => 'icon',
			],
			'PopupIcon' => [
				'type'=> 'string',
				'default'=> 'fa fa-search-plus',
			],
			'CutIcon' => [
				'type' => 'object',
				'default' => [
					'url' => '',
					'Id' => '',
				],
			],
			'Iconsize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true],
										(object) ['key' => 'PopupNone', 'relation' => '!=', 'value' => 'none']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .meta-search-icon .pop-icon{max-width:{{Iconsize}};font-size:{{Iconsize}};}',
					],
				],
				'scopy' => true,
			],
			'NIcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true],
										(object) ['key' => 'PopupNone', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .meta-search-icon a{color:{{NIcolor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true],
										(object) ['key' => 'PopupNone', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .meta-search-icon{color:{{NIcolor}};}',
					],
				],
				'scopy' => true,
			],
			'HIcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true],
										(object) ['key' => 'PopupNone', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content .meta-search-icon:hover a{color:{{HIcolor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true],
										(object) ['key' => 'PopupNone', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content .meta-search-icon:hover{color:{{HIcolor}};}',
					],
				],
				'scopy' => true,
			],
			'BSpace' => [
				'type' => 'object',
				'default' => [
					'md' => '',			
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-4'],
										(object) ['key' => 'Disicon', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .meta-search-icon{margin-bottom:{{BSpace}};}',
					],
				],
				'scopy' => true,
			],

			'ExtIcon' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Imgoption', 'relation' => '==', 'value' => 'repeater']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .gallery-list-icon{font-size:{{ExtIcon}};}',
					],
				],
				'scopy' => true,
			],
			'ExtIconCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Imgoption', 'relation' => '==', 'value' => 'repeater']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .gallery-list-icon{color:{{ExtIconCr}};}',
					],
				],
				'scopy' => true,
			],
			'ExtIconHCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Imgoption', 'relation' => '==', 'value' => 'repeater']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content:hover .gallery-list-icon{color:{{ExtIconHCr}};}',
					],
				],
				'scopy' => true,
			],
			'ExtTops' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Imgoption', 'relation' => '==', 'value' => 'repeater']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .gallery-list-icon{padding-top:{{ExtTops}};}',
					],
				],
				'scopy' => true,
			],
			'ExtBots' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Imgoption', 'relation' => '==', 'value' => 'repeater']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .gallery-list-icon{padding-bottom:{{ExtBots}};}',
					],
				],
				'scopy' => true,
			],

			'TitleTypo' => [
				'type'=> 'object',
				'default'=>  (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .post-title',
					],
				],
				'scopy' => true,
			],
			'TNcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .post-title{color:{{TNcolor}};}',
					],
				],
				'scopy' => true,
			],
			'THcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3']],
										(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true] ],	
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content .post-title:hover{color:{{THcolor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true]],	
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content:hover .post-title{color:{{THcolor}};}',
					],
				],
				'scopy' => true,
			],
			'TtopS' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .post-title{margin-top:{{TtopS}};}',
					],
				],
				'scopy' => true,
			],
			'TBspc' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dtitle', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .post-title{margin-bottom:{{TBspc}};}',
					],
				],
				'scopy' => true,
			],

			'ExTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .entry-content',									
					],
				],
				'scopy' => true,
			],
			'ExNcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .entry-content{color:{{ExNcolor}};}',
					],
				],
				'scopy' => true,
			],
			'ExHcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1','style-2','style-3']],
										(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],	
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content .entry-content:hover{color:{{ExHcolor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4'],
										(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],	
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .tpgb-gallery-list-content:hover .entry-content{color:{{ExHcolor}};}',
					],
				],
				'scopy' => true,
			],
			'ExtopS' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .entry-content{margin-top:{{ExtopS}};}',
					],
				],
				'scopy' => true,
			],
			'ExBspc' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Dcontent', 'relation' => '==', 'value' => true] ],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .entry-content{margin-bottom:{{ExBspc}};}',
					],
				],
				'scopy' => true,
			],

			'Category' => [
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
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-filter-data{text-align:{{FilterAlig}};}',
					],
				],
			],

			'Nbgcolor' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-1 .tpgb-gallery-list-content .post-content-center',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-2 .tpgb-gallery-list-content .post-content-bottom',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-3 .tpgb-gallery-list-content .post-content-center',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-4 .post-content-center .post-hover-content',
					],
				],
				'scopy' => true,
			],
			'Hbgcolor' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-1 .tpgb-gallery-list-content:hover .post-content-center',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-2 .tpgb-gallery-list-content:hover .post-content-bottom',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-3 .tpgb-gallery-list-content:hover .post-content-center',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list.gallery-style-4 .post-content-center:hover .post-hover-content',
					],
				],
				'scopy' => true,
			],

			'ImgHE' => [
				'type' => 'string',
				'default' => 'style-1',	
				'scopy' => true,
			],
			'FiNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content .tpgb-gallery-image:before,{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content .tpgb-metro-gallery-image:before',
					],
				],
				'scopy' => true,
			],
			'Nfilter' => [
				'type' => 'object',
				'default' => [
					'openFilter' => false,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content .tpgb-gallery-image img,{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content .tpgb-metro-gallery-image',
					],
				],
				'scopy' => true,
			],
			'FiHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content:hover .tpgb-gallery-image:before,{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content:hover .tpgb-metro-gallery-image:before',
					],
				],
				'scopy' => true,
			],
			'Hfilter' => [
				'type' => 'object',
				'default' => [
					'openFilter' => false,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content:hover .tpgb-gallery-image img,{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-gallery-list-content:hover .tpgb-metro-gallery-image',
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
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a',
					],
				],
				'scopy' => true,
			],
			'InPadding' => [
				'type' => 'object',
				'default' => (object) [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],					
						'selector' => '{{PLUS_WRAP}} .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count),
									{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),
									{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,
									{{PLUS_WRAP}} .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,
									{{PLUS_WRAP}} .tpgb-categories.hover-style-3 .tpgb-filter-list a,
									{{PLUS_WRAP}} .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
					],
				],
				'scopy' => true,
			],
			'FCMargin' => [
				'type' => 'object',
				'default' => (object) [	
					'md' => '',			
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],
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
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a,
									{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCHBcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-1'],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => ['style-2','style-4']]],
						'selector' => '.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',
	
					],
				],
				'scopy' => true,
			],
			'FCHvrBre' => [
				'type' => 'object',
				'default' => (object) [
					'md' => '',			
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
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
						'selector' => '.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
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
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => ['style-2','style-4']],
										(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),
									{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',

					],
				],
				'scopy' => true,
			],

			'FCBgRs' => [
				'type' => 'object',
				'default' => (object) [
					'md' => '',				
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-2'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
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
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
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
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true]],
						'selector' => '.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,.tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count',
					],
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
										(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],
			'FcBCrHs' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Category', 'relation' => '==', 'value' => true],
									(object) ['key' => 'FilterHs', 'relation' => '==', 'value' => 'style-4'],
									(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
					],
				],
				'scopy' => true,
			],	
			'FCBoxSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],

			'BoxT' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'BoxB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content',
					],
				],
				'scopy' => true,
			],
			'BoxNBrs' => [
				'type' => 'object',
				'default' =>  (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content{border-radius:{{BoxNBrs}};}',
					],
				],
				'scopy' => true,
			],
			'boxHvebor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content:hover',
					],
				],
				'scopy' => true,
			],
			'BoxHBrs' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
					],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content:hover{border-radius:{{BoxHBrs}};}',
					],
				],
				'scopy' => true,
			],
			'BoxNsd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content',
					],
				],
				'scopy' => true,
			],
			'BoxHsd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'BoxT', 'relation' => '==', 'value' => true]],					
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .post-loop-inner .grid-item .tpgb-gallery-list-content:hover',
					],
				],
				'scopy' => true,
			],

			'FancyOption' => [
				'type' => 'string',
        		'default' => '[]',
				'scopy' => true,
			],
			'LoopFancy' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'infobar' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'ArrowsFancy' => [
				'type' => 'boolean',
				'default' => true,
				'scopy' => true,
			],
			'TitleFancy' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'AnimationFancy' => [
				'type' => 'string',
				'default' => 'zoom',
				'scopy' => true,
			],
			'DurationFancy' => [
				'type' => 'string',
				'default' => 366,
				'scopy' => true,
			],
			'TransitionFancy' => [
				'type' => 'string',
				'default' => 'slide',
				'scopy' => true,
			],
			'TranDuration' => [
				'type' => 'string',
				'default' => 366,
				'scopy' => true,
			],
			'ThumbsOption' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'ThumbsBrCr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Playout', 'relation' => '==', 'value' => 'default'],
										(object) ['key' => 'ThumbsOption', 'relation' => '==', 'value' => true]],
						'selector' => '.fancybox-thumbs__list a.fancybox-thumbs-active:before,.fancybox-thumbs__list a:before',
					],
				],
				'scopy' => true,
			],
			'ThumbsBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Playout', 'relation' => '==', 'value' => 'default'],
										(object) ['key' => 'ThumbsOption', 'relation' => '==', 'value' => true]],
						'selector' => '.fancybox-thumbs .fancybox-thumbs__list',
					],
				],
				'scopy' => true,
			],

			'PNFpad' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found{padding:{{PNFpad}};}',
					],
				],
				'scopy' => true,
			],
			'PNFtypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found',
					],
				],
				'scopy' => true,
			],
			'PNFcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found{color:{{PNFcr}};}',
					],
				],
				'scopy' => true,
			],
			'PNFbg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found',
					],
				],
				'scopy' => true,
			],
			'PNFBr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found',
					],
				],
				'scopy' => true,
			],
			'PNFBs' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-gallery-list .tpgb-posts-not-found',
					],
				],
				'scopy' => true,
			],

		];
		
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-media-listing', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_Media_listing_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_Media_listing' );