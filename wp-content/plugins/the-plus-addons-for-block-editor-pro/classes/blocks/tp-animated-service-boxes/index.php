<?php
/* Block : Animated Service Boxes
 * @since : 1.4.3
 */
defined( 'ABSPATH' ) || exit;

function tpgb_animated_service_boxes_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$mainStyleType = (!empty($attributes['mainStyleType'])) ? $attributes['mainStyleType'] : 'image-accordion';
	$imgAcrdnStyle = (!empty($attributes['imgAcrdnStyle'])) ? $attributes['imgAcrdnStyle'] : 'accordion-style-1';
	$imgOrientation = (!empty($attributes['imgOrientation'])) ? $attributes['imgOrientation'] : 'accordion-vertical';
	$slideStyle = (!empty($attributes['slideStyle'])) ? $attributes['slideStyle'] : 'sliding-style-1';
	$articleStyle = (!empty($attributes['articleStyle'])) ? $attributes['articleStyle'] : 'article-box-style-1';
	$activeSlide = (!empty($attributes['activeSlide'])) ? $attributes['activeSlide'] : '';
	$imgFlexGrow = (!empty($attributes['imgFlexGrow'])) ? $attributes['imgFlexGrow'] : '7.5';
	$bannerStyle = (!empty($attributes['bannerStyle'])) ? $attributes['bannerStyle'] : 'info-banner-style-1';
	$bannerOrientation = (!empty($attributes['bannerOrientation'])) ? $attributes['bannerOrientation'] : 'info-banner-left';
	$sectionStyle = (!empty($attributes['sectionStyle'])) ? $attributes['sectionStyle'] : 'hover-section-style-1';
	$sectionImgPreload = (!empty($attributes['sectionImgPreload'])) ? $attributes['sectionImgPreload'] : false;
	$fancyBStyle = (!empty($attributes['fancyBStyle'])) ? $attributes['fancyBStyle'] : 'fancy-box-style-1';
	$serviceEStyle = (!empty($attributes['serviceEStyle'])) ? $attributes['serviceEStyle'] : 'services-element-style-1';
	$portfolioStyle = (!empty($attributes['portfolioStyle'])) ? $attributes['portfolioStyle'] : 'portfolio-style-1';
	$serviceBox = (!empty($attributes['serviceBox'])) ? $attributes['serviceBox'] : [];
	$columns = (!empty($attributes['columns'])) ? $attributes['columns'] : 'md';
	$sbTabColumn = (!empty($attributes['sbTabColumn'])) ? $attributes['sbTabColumn'] : 'sb_t_2';
	$sbMobColumn = (!empty($attributes['sbMobColumn'])) ? $attributes['sbMobColumn'] : 'sb_m_1';
	
	$disIcnImg = (!empty($attributes['disIcnImg'])) ? $attributes['disIcnImg'] : false;
	$disBtn = (!empty($attributes['disBtn'])) ? $attributes['disBtn'] : false;
	$btnStyle = (!empty($attributes['btnStyle'])) ? $attributes['btnStyle'] : 'style-7';
	$btnIconType = (!empty($attributes['btnIconType'])) ? $attributes['btnIconType'] : 'none';
	$btnIconPosition = (!empty($attributes['btnIconPosition'])) ? $attributes['btnIconPosition'] : 'after';
	
	$titleOnClick = (!empty($attributes['titleOnClick'])) ? $attributes['titleOnClick'] : '';
	$titleLinkColor = (!empty($attributes['titleLinkColor'])) ? $attributes['titleLinkColor'] : '';
	
	$titleTagType = (!empty($attributes['titleTagType'])) ? $attributes['titleTagType'] : 'h6';
	$sTitleTagType = (!empty($attributes['sTitleTagType'])) ? $attributes['sTitleTagType'] : 'h6';
	
	$iconStyle = (!empty($attributes['iconStyle'])) ? $attributes['iconStyle'] : 'icon-square';
	$hoverBGOverlay = (!empty($attributes['hoverBGOverlay'])) ? $attributes['hoverBGOverlay'] : 'rgba(0,0,0,0.3)';
	$btnIconStore = (!empty($attributes['btnIconStore'])) ? $attributes['btnIconStore'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}
	
	//Style Class
	$style = $hoverSectionExtra = $orientClass = $sbTabClass = $sbMobClass = '';
	if($mainStyleType=='info-banner' && $bannerStyle=='info-banner-style-1'){
		$orientClass = $bannerOrientation;
	}
	if($mainStyleType=='image-accordion'){
		$style .= $imgAcrdnStyle." ".$imgOrientation;
	}else if($mainStyleType=='sliding-boxes'){
		$style .= $slideStyle;
		$sbTabClass .= " ".$sbTabColumn;
		$sbMobClass .= " ".$sbMobColumn;
	}else if($mainStyleType=='article-box'){
		$style .= $articleStyle;
	}else if($mainStyleType=='info-banner'){
		$style .= $bannerStyle." ".$orientClass;
	}else if($mainStyleType=='hover-section'){
		$style .= $sectionStyle;
		$hoverSectionExtra .= 'hover-section-extra';
	}else if($mainStyleType=='fancy-box'){
		$style .= $fancyBStyle;
	}else if($mainStyleType=='services-element'){
		$style .= $serviceEStyle;
	}else if($mainStyleType=='portfolio'){
		$style .= $portfolioStyle;
	}
	
	$tnslin = 'tpgb-trans-linear'; $tnsease = 'tpgb-trans-ease'; $tnseaseout = 'tpgb-trans-easeinout';
	$relposw = 'tpgb-relative-block'; $relpos = 'tpgb-relative-block';
	$relfposw = 'tpgb-rel-flex'; $absfposw = 'tpgb-abs-flex';
	
	//Column Class
	$list_column = '';
	if( $mainStyleType!='image-accordion' && $mainStyleType!='portfolio' && $mainStyleType!='sliding-boxes'){
		$list_column .= 'tpgb-col-'.esc_attr($columns['xs']);
		$list_column .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$list_column .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$list_column .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}
	
	$port_hover_color=$port_click_text='';
	if($mainStyleType=='portfolio'){
		$port_hover_color='data-phcolor="'.esc_attr($titleLinkColor).'"';
		$port_click_text='data-clicktext="'.esc_attr($titleOnClick).'"';
	}
	
	$output = '' ;
	$featureImgSrc =$featureImgRender= '';
	$i=1;
	$loopItem = '';
	if(!empty($serviceBox)){
		foreach ( $serviceBox as $index => $item ) :
			
			$btnUrl = (isset($item['btnUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['btnUrl']) : (!empty($item['btnUrl']['url']) ? $item['btnUrl']['url'] : '');
			$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($item['btnUrl']);
			
			$getItemTitle = '';
			if(!empty($item['title'])){
				if((!empty($disBtn) || $mainStyleType=='portfolio') && !empty($btnUrl)){
					$target = (!empty($item['btnUrl']['target'])) ? '_blank' : '';
					$nofollow = (!empty($item['btnUrl']['nofollow'])) ? 'nofollow' : '';
					$ariaLabelT = (!empty($item['ariaLabel'])) ? $item['ariaLabel'] : $item['title'];
					$getItemTitle .= '<a class="asb-title-link" style="cursor: pointer" href="'.esc_url($btnUrl).'" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.esc_attr($ariaLabelT).'">';
				}
					$getItemTitle .='<'.Tp_Blocks_Helper::validate_html_tag($titleTagType).' class="asb-title '.esc_attr($tnseaseout).'">';
						$getItemTitle .= wp_kses_post($item['title']);
					$getItemTitle .='</'.Tp_Blocks_Helper::validate_html_tag($titleTagType).'>';
				if((!empty($disBtn) || $mainStyleType=='portfolio') && !empty($btnUrl)){
					$getItemTitle .='</a>';
				}
			}
		
			$getItemSubTitle = '';
			if(!empty($item['subTitle'])){
				$getItemSubTitle .='<'.Tp_Blocks_Helper::validate_html_tag($sTitleTagType).' class="asb-sub-title '.esc_attr($tnseaseout).'">';
					$getItemSubTitle .= wp_kses_post($item['subTitle']);
				$getItemSubTitle .='</'.Tp_Blocks_Helper::validate_html_tag($sTitleTagType).'>';
			}
		
			$getItemDesc = '';
			if(!empty($item['description'])){
				$getItemDesc .= '<div class="asb-desc '.esc_attr($tnseaseout).'">'.wp_kses_post($item['description']).'</div>';
			}
			
			$loop_content_list = $item['contentList'];
			$se_listing='';
			if(!empty($loop_content_list) ){
				$loop_content_list = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($loop_content_list);
				$array=explode("|",$loop_content_list);
				if(!empty($array[1])){
					$se_listing .='<div class="se-liting-ul">';
					foreach($array as $value){							
						$se_listing .='<div class="se-listing" >'.wp_kses_post($value).'</div>';							
					}
					$se_listing .='</div>';
				}else{
					$se_listing ='<div class="se-liting-ul"><div class="se-listing" >'.wp_kses_post($loop_content_list).'</div></div>';
				}
			}
		
			$getIcon = $iconSty= ''; 
			if($mainStyleType!='services-element'){
				$iconSty = $iconStyle;
			}
			$getIcon .= '<span class="asb-icon-image asb-icon '.esc_attr($iconSty).' '.esc_attr($tnseaseout).'">';
				$getIcon .= '<i class="'.esc_attr($item['iconStore']).'"></i>';
			$getIcon .= '</span>';
			
			$getImg = '';
			if(!empty($item['iconType']) && $item['iconType']=='image' && !empty($item['imgStore']['url'])){
				$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'full';
				if(!empty($item['imgStore']['id'])){
					$imgSrc = wp_get_attachment_image($item['imgStore']['id'] , $imageSize,false, ['class' => 'asb-icon-image asb-image '.esc_attr($tnseaseout).' '.esc_attr($iconSty) ]);
				}else if( !empty($item['imgStore']['url']) ){
					$imgUrl = (isset($item['imgStore']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['imgStore']) : $item['imgStore']['url'];
					$imgSrc = '<img class="asb-icon-image asb-image '.esc_attr($iconSty).' '.esc_attr($tnseaseout).'" src="'.esc_url($imgUrl).'"/>';
				}
				$getImg .= $imgSrc;
			}
			$getbutton = '';
			$target = (!empty($item['btnUrl']['target'])) ? '_blank' : '';
			$nofollow = (!empty($item['btnUrl']['nofollow'])) ? 'nofollow' : '';
			$ariaLabelT = (!empty($item['ariaLabel'])) ? esc_attr($item['ariaLabel']) : ((!empty($item['btnText'])) ? esc_attr($item['btnText']) : esc_attr__("Button", 'tpgbp'));
			$getbutton .= '<div class="tpgb-adv-button button-'.esc_attr($btnStyle).'">';
				$getbutton .= '<a href="'.esc_url($btnUrl).'" class="button-link-wrap" role="button" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$link_attr.' aria-label="'.$ariaLabelT.'">';
				if($btnStyle == 'style-8'){
					if($btnIconPosition == 'before'){
						if($btnIconType == 'icon'){
							$getbutton .= '<span class="btn-icon  button-'.esc_attr($btnIconPosition).'">';
								$getbutton .= '<i class="'.esc_attr($btnIconStore).'"></i>';
							$getbutton .= '</span>';
						}
						$getbutton .= wp_kses_post($item['btnText']);
					} 
					if($btnIconPosition == 'after'){
						$getbutton .= wp_kses_post($item['btnText']);
						if($btnIconType == 'icon'){
							$getbutton .= '<span class="btn-icon  button-'.esc_attr($btnIconPosition).'">';
								$getbutton .= '<i class="'.esc_attr($btnIconStore).'"></i>';
							$getbutton .= '</span>';
						}
					}
				}
				if($btnStyle == 'style-7' || $btnStyle == 'style-9' ){
					$getbutton .= wp_kses_post($item['btnText']);
					$getbutton .= '<span class="button-arrow">';
					if($btnStyle == 'style-7'){
						$getbutton .= '<span class="btn-right-arrow"><i class="fas fa-chevron-right"></i></span>';
					}
					if($btnStyle == 'style-9'){
						$getbutton .= '<i class="btn-show fas fa-chevron-right"></i>';
						$getbutton .= '<i class="btn-hide fas fa-chevron-right"></i>';
					}
					$getbutton .= '</span>';
				}
				$getbutton .= '</a>';
			$getbutton .= '</div>';
			
			if($mainStyleType=='image-accordion'){
				$classes = [ 'class' => 'theplus-image-accordion__image-instance loaded'];
			}else if($mainStyleType=='sliding-boxes'){
				$classes = [ 'class' => esc_attr($tnslin)];
			}else{
				$classes =[];
			}
			$fimageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'full';
			if(!empty($item['featureImg']) && !empty($item['featureImg']['id'])){
				$featureImgRender = wp_get_attachment_image($item['featureImg']['id'] , $fimageSize, false, $classes);
				$featureImgSrc = wp_get_attachment_image_src($item['featureImg']['id'] , $fimageSize);
				$featureImgSrc = isset($featureImgSrc[0]) ? $featureImgSrc[0] : '';
			}else if(!empty($item['featureImg']['url'])){
				$featureImg = (isset($item['featureImg']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['featureImg']) : $item['featureImg']['url'];
				if($mainStyleType=='image-accordion'){
					$featureImgRender = '<img class="theplus-image-accordion__image-instance loaded" src="'.esc_url($featureImg).'"/>';
				}else if($mainStyleType=='sliding-boxes'){
					$featureImgRender = '<img src="'.esc_url($featureImg).'" class="'.esc_attr($tnslin).'"/>';
				}else{
					$featureImgRender = '<img src="'.esc_url($featureImg).'"/>';
				}
				$featureImgSrc = $featureImg;
			}else{
				$featureImgRender = '';
				$featureImgSrc = '';
			}
			
			$infobannerBack = '';
			if($mainStyleType=='info-banner' && $bannerStyle=='info-banner-style-1' && !empty($featureImgSrc)){
				$infobannerBack = 'background:url('.esc_url($featureImgSrc).') center/cover';
			}
			
			$active_class='';
			if($mainStyleType=='image-accordion'){
				if($i == $activeSlide){
					$active_class='active_accrodian';
				}
			}
			
			if($mainStyleType=='sliding-boxes' && $i== $activeSlide){
				$active_class="active-slide";
			}else if($mainStyleType=='hover-section' && $i== 1){
				$active_class="active-hover";
			}else if($mainStyleType=='portfolio' && $i== 1){
                $active_class="active-port";
            }
			
			$hover_sec_ovly='';
			if($mainStyleType=='hover-section'){
				$hover_sec_ovly='data-hsboc="'.esc_attr($hoverBGOverlay).'"';
			}
			
			$image_url=$click_url='';
			if($mainStyleType=='portfolio' && ($portfolioStyle == 'portfolio-style-1' || $portfolioStyle == 'portfolio-style-2')){
				$image_url='data-url="'.esc_url($featureImgSrc).'"';
				$click_url='data-clickurl="'.esc_url($btnUrl).'"';
			}
			$reldb = ($mainStyleType=='article-box' ? $relposw : '');
			$loopItem .='<div class="service-item-loop tp-repeater-item-'.esc_attr($item['_key']).' '.esc_attr($active_class).' '.$list_column.''.esc_attr($sbTabClass).''.esc_attr($sbMobClass).' '.esc_attr($reldb).'" '.$hover_sec_ovly.' '.$image_url.' '.$click_url.' '.$port_click_text.' '.$port_hover_color.'>';
				if($mainStyleType=='image-accordion'){
					if(!empty($item['featureImg']) && !empty($item['featureImg']['url'])){
						$loopItem .= $featureImgRender;
					}
					$loopItem .='<div class="asb-content">';
						$loopItem .= $getItemTitle;
						$loopItem .= $getItemSubTitle;
						$loopItem .= $getItemDesc;
						if(!empty($disBtn)) {
							$loopItem .= $getbutton;
						}
					$loopItem .='</div>';
				}
				if($mainStyleType=='sliding-boxes'){
					$loopItem .='<div class="tp-sb-image">';
						if(!empty($item['featureImg']) && !empty($item['featureImg']['url'])){
							$loopItem .= $featureImgRender;
						}
					$loopItem .='</div>';
					$loopItem .='<div class="asb-content '.esc_attr($absfposw).'">';
						$loopItem .= $getItemTitle;
						$loopItem .= $getItemSubTitle;
						$loopItem .= $getItemDesc;
						if(!empty($disBtn)) {
							$loopItem .= $getbutton;
						}
					$loopItem .='</div>';
				}
				if($mainStyleType=='article-box' && $articleStyle=='article-box-style-1'){
					$loopItem .='<div class="article-box-inner-content '.esc_attr($tnseaseout).' '.esc_attr($relposw).'">';
						if($item['featureImg'] && $item['featureImg']['url']){
							$loopItem .='<div class="article-box-img">';
								if(!empty($item['featureImg']) && !empty($item['featureImg']['url'])){
									$loopItem .= $featureImgRender;
								}
							$loopItem .='</div>';
						}
						$loopItem .='<div class="article-overlay">';
							$loopItem .='<div class="article-box-content">';
								$loopItem .= $getItemTitle;
								$loopItem .='<div class="article-hover-content">';	
									$loopItem .= $getItemSubTitle;
									$loopItem .= $getItemDesc;
									if(!empty($disBtn)){
										$loopItem .= $getbutton;
									}
								$loopItem .= '</div>';
							$loopItem .= '</div>';
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='article-box' && $articleStyle=='article-box-style-2'){
					$loopItem .= '<div class="article-box-main '.esc_attr($relfposw).'">';
						$loopItem .= '<div class="article-box-main-wrapper '.esc_attr($relpos).'" style="background:url('.esc_url($featureImgSrc).') center/cover">';
							$loopItem .= '<div class="article-box-front-wrapper '.esc_attr($relfposw).'">';
								if(!empty($disIcnImg) && $item['iconType']=='icon'){
									$loopItem .= $getIcon;
								}
								if(!empty($disIcnImg) && $item['iconType']=='image'){
									$loopItem .= $getImg;
								}
								$loopItem .= $getItemTitle;
								$loopItem .= $getItemSubTitle;
							$loopItem .= '</div>';
							$loopItem .= '<div class="article-box-hover-wrapper">';
								$loopItem .= $getItemDesc;
								if(!empty($disBtn)){
									$loopItem .= $getbutton;
								}
							$loopItem .= '</div>';
						$loopItem .= '</div>';	
					$loopItem .= '</div>';
				}
				if($mainStyleType=='info-banner' && $bannerStyle=='info-banner-style-1'){
					$loopItem .= '<div class="info-banner-content-wrapper '.esc_attr($relposw).'">';
						$loopItem .= '<div class="info-banner-front-content '.esc_attr($tnseaseout).'">';
							if(!empty($disIcnImg) && $item['iconType']=='icon'){
								$loopItem .= $getIcon;
							}
							if(!empty($disIcnImg) && $item['iconType']=='image'){
								$loopItem .= $getImg;
							}
							$loopItem .= $getItemTitle;
							$loopItem .= $getItemSubTitle;
						$loopItem .= '</div>';
						$loopItem .= '<div class="info-banner-back-content '.esc_attr($tnseaseout).'" style="'.$infobannerBack.'">';
							$loopItem .= '<div class="info-banner-back-content-inner">';
								$loopItem .= $getItemDesc;
								if(!empty($disBtn)){
									$loopItem .= $getbutton;
								}
							$loopItem .= '</div>';
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='info-banner' && $bannerStyle=='info-banner-style-2'){
					$loopItem .= '<div class="info-banner-content-wrapper '.esc_attr($relfposw).'">';
						$loopItem .= '<div class="info-front-content '.esc_attr($relfposw).'">';
							if(!empty($disIcnImg) && $item['iconType']=='icon'){
								$loopItem .= $getIcon;
							}
							if(!empty($disIcnImg) && $item['iconType']=='image'){
								$loopItem .= $getImg;
							}
							$loopItem .= $getItemTitle;
							$loopItem .= $getItemSubTitle;
							$loopItem .= $getItemDesc;
							if(!empty($disBtn)){
								$loopItem .= $getbutton;
							}
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='hover-section'){
					$loopItem .= '<div class="hover-section-content-wrapper" data-image="'.esc_url($featureImgSrc).'">';
						if(!empty($disIcnImg) && $item['iconType']=='icon'){
							$loopItem .= $getIcon;
						}
						if(!empty($disIcnImg) && $item['iconType']=='image'){
							$loopItem .= $getImg;
						}
						$loopItem .= $getItemTitle;
						$loopItem .= '<div class="hover-content-inner-hover '.esc_attr($tnslin).'">';
							$loopItem .= $getItemSubTitle;
							$loopItem .= $getItemDesc;
							if(!empty($disBtn)){
								$loopItem .= $getbutton;
							}
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='fancy-box'){
					$loopItem .= '<div class="fancybox-inner-wrapper '.esc_attr($relposw).'">';
						$loopItem .= '<div class="fancybox-image-background '.esc_attr($relposw).'" style="background-image:url('.esc_url($featureImgSrc).')">';
						$loopItem .= '</div>';
						$loopItem .= '<div class="fancybox-inner-content '.esc_attr($relposw).'">';
							$loopItem .= '<div class="fb-content '.esc_attr($relpos).'">';
								$loopItem .= $getItemTitle;
								$loopItem .= $getItemSubTitle;
								$loopItem .= $getItemDesc;
							$loopItem .= '</div>';
							$loopItem .= '<div class="fb-button '.esc_attr($relpos).'">';
								if(!empty($disBtn)){
									$loopItem .= $getbutton;
								}
							$loopItem .= '</div>';
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='services-element' && $serviceEStyle=='services-element-style-1'){
					$loopItem .= '<div class="se-wrapper '.esc_attr($relposw).' '.esc_attr($tnseaseout).'">';
						$loopItem .= '<div class="se-first-section">';
							$loopItem .= '<div class="se-icon">';
								if(!empty($disIcnImg) && $item['iconType']=='icon'){
									$loopItem .= $getIcon;
								}
								if(!empty($disIcnImg) && $item['iconType']=='image'){
									$loopItem .= $getImg;
								}
								$loopItem .= '<div class="se-title-desc">';
									$loopItem .= $getItemTitle;
									$loopItem .= $getItemSubTitle;
									$loopItem .= $getItemDesc;
									if(!empty($disBtn)){
										$loopItem .= $getbutton;
									}
								$loopItem .= '</div>';
							$loopItem .= '</div>';
						$loopItem .= '</div>';
						$loopItem .= '<div class="se-listing-section">';		
							$loopItem .= $se_listing;
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='services-element' && $serviceEStyle=='services-element-style-2'){
					$loopItem .= '<div class="se-wrapper-main">';
						$loopItem .= '<div class="se-wrapper '.esc_attr($tnseaseout).'">';
							$loopItem .= '<div class="se-wrapper-inner">';
								$loopItem .= '<div class="se-icon">';
									if(!empty($disIcnImg) && $item['iconType']=='icon'){
										$loopItem .= $getIcon;
									}
									if(!empty($disIcnImg) && $item['iconType']=='image'){
										$loopItem .= $getImg;
									}
								$loopItem .= '</div>';
								$loopItem .= '<div class="se-content">';
									$loopItem .= $getItemTitle;
									$loopItem .= $getItemSubTitle;
									$loopItem .= $getItemDesc;
									if(!empty($disBtn)){
										$loopItem .= $getbutton;
									}
									$loopItem .= $se_listing;
								$loopItem .= '</div>';
							$loopItem .= '</div>';
						$loopItem .= '</div>';
					$loopItem .= '</div>';
				}
				if($mainStyleType=='portfolio'){
					$loopItem .= $getItemTitle;
				}
			$loopItem .= '</div>';
			if($i==1){
				$first_port_img=$featureImgSrc;
			}
			$i++;
			endforeach;
	}
	
	$data_attr = '';
	if($activeSlide==0 && $mainStyleType=='image-accordion'){
		$data_attr .= 'data-accordion-hover="yes"';
	}
	
    $output .= '<div class="tpgb-animated-service-boxes tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($mainStyleType).' '.esc_attr($style).' '.esc_attr($blockClass).' '.esc_attr($equalHclass).'" '.$data_attr.' '.$equalHeightAtt.'>';
		$output .= '<div class="asb_wrap_list tpgb-row '.esc_attr($hoverSectionExtra).'">';
			if($mainStyleType!='portfolio'){
				$output .= $loopItem;
			}
			if($mainStyleType=='portfolio' && $portfolioStyle=='portfolio-style-1'){
				$output .= '<div class="portfolio-content-wrapper tpgb-col-md-6 tpgb-col-lg-6 tpgb-col-sm-12 tpgb-col-12">';
					$output .= $loopItem;
				$output .= '</div>';
				$output .= '<div class="portfolio-hover-wrapper tpgb-col-md-6 tpgb-col-lg-6 tpgb-col-sm-12 tpgb-col-12 '.esc_attr($relfposw).'">';
					$output .= '<div class="portfolio-hover-image '.esc_attr($relposw).'" style="background:url('.esc_url($first_port_img).')">';
					$output .= '</div>';
				$output .= '</div>';
			}
			if($mainStyleType=='portfolio' && $portfolioStyle=='portfolio-style-2'){
				$output .= '<div class="portfolio-wrapper tpgb-col-md-12 '.esc_attr($relfposw).'" style="background:url('.esc_url($first_port_img).')">';
					$output .= $loopItem;
				$output .= '</div>';
			}
		$output .= '</div>';
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

function tpgb_animated_service_boxes() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'mainStyleType' => [
			'type' => 'string',
			'default' => 'image-accordion',	
		],
		'imgAcrdnStyle' => [
			'type' => 'string',
			'default' => 'accordion-style-1',	
		],
		'imgOrientation' => [
			'type' => 'string',
			'default' => 'accordion-vertical',	
		],
		'slideStyle' => [
			'type' => 'string',
			'default' => 'sliding-style-1',	
		],
		'articleStyle' => [
			'type' => 'string',
			'default' => 'article-box-style-1',	
		],
		'activeSlide' => [
			'type' => 'string',
			'default' => '1',	
		],
		'imgFlexGrow' => [
			'type' => 'string',
			'default' => '7.5',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.image-accordion .service-item-loop.active_accrodian{ flex-grow: {{imgFlexGrow}}; }',
				],
			],
		],
		'bannerStyle' => [
			'type' => 'string',
			'default' => 'info-banner-style-1',	
		],
		'bannerOrientation' => [
			'type' => 'string',
			'default' => 'info-banner-left',	
		],
		'sectionStyle' => [
			'type' => 'string',
			'default' => 'hover-section-style-1',	
		],
		'sectionImgPreload' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'fancyBStyle' => [
			'type' => 'string',
			'default' => 'fancy-box-style-1',	
		],
		'serviceEStyle' => [
			'type' => 'string',
			'default' => 'services-element-style-1',	
		],
		'portfolioStyle' => [
			'type' => 'string',
			'default' => 'portfolio-style-1',	
		],
		'imageSize' => [
			'type' => 'string',
			'default' => 'full',	
		],
		'disBtn' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'btnStyle' => [
			'type' => 'string',
			'default' => 'style-7',	
		],
		'btnIconType'  => [
			'type' => 'string' ,
			'default' => 'none',	
		],
		'btnIconStore' => [
			'type'=> 'string',
			'default'=> '',
		],
		'btnIconPosition' => [
			'type'=> 'string',
			'default'=> 'after',
		],
		'disIcnImg' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'serviceBox' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'title' => [
						'type' => 'string',
						'default' => 'Service'
					],
					'iconType' => [
						'type' => 'string',
						'default' => 'icon',
					],
					'iconStore' => [
						'type'=> 'string',
						'default'=> 'fab fa-whatsapp',
					],
					'imgStore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'imageSize' => [
						'type' => 'string',
						'default' => 'full',	
					],
					'subTitle' => [
						'type' => 'string',
						'default' => ''
					],
					'description' => [
						'type' => 'string',
						'default' => 'Description Text will go here.'
					],
					'featureImg' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'btnText' => [
						'type' => 'string',
						'default' => 'Read More'
					],
					'btnUrl' => [
						'type'=> 'object',
						'default'=> [
							'url' => '',
							'target' => '',
							'nofollow' => ''
						],
					],
					'ariaLabel' => [
						'type' => 'string',
						'default' => '',	
					],
					'contentList' => [
						'type' => 'string',
						'default' => 'Feature 1 | Feature 2 | Feature 3 | Feature 4',
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'title' => 'Marketing',
					'subTitle' => '',
					'iconType' => 'icon',
					'description' =>'Description Text will go here.',
					'iconStore' => 'fas fa-home',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'contentList' => 'Feature 1 | Feature 2 | Feature 3 | Feature 4',
					'btnText' => 'Read More',
					'btnUrl' => [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'ariaLabel' => '',
					'featureImg' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
				],
				[
					'_key' => '1',
					'title' => 'Sales',
					'subTitle' => '',
					'iconType' => 'icon',
					'description' =>'Description Text will go here.',
					'iconStore' => 'fas fa-home',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'contentList' => 'Feature 1 | Feature 2 | Feature 3 | Feature 4',
					'btnText' => 'Read More',
					'btnUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'ariaLabel' => '',
					'featureImg' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
				],
				[
					'_key' => '2',
					'title' => 'Fulfillments',
					'subTitle' => '',
					'iconType' => 'icon',
					'description' =>'Description Text will go here.',
					'iconStore' => 'fas fa-home',
					'imgStore' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'contentList' => 'Feature 1 | Feature 2 | Feature 3 | Feature 4',
					'btnText' => 'Read More',
					'btnUrl'=> [
						'url' => '#',	
						'target' => '',	
						'nofollow' => ''	
					],
					'ariaLabel' => '',
					'featureImg' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
				],
			],
		],
		'tansNmlCss' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'tansHvrCss' => [
			'type' => 'string',
			'default' => '',
			'scopy' => true,
		],
		'textAlign' => [
			'type' => 'string',
			'default' => 'left',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => ['image-accordion','info-banner','hover-section','portfolio']]],
					'selector' => '{{PLUS_WRAP}}.image-accordion .asb-content ,{{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper, {{PLUS_WRAP}}.hover-section .asb_wrap_list.tpgb-row.hover-section-extra, {{PLUS_WRAP}}.portfolio.portfolio-style-1 .asb_wrap_list{ text-align: {{textAlign}}; }',
				],
			],
			'scopy' => true,
		],
		'textAlign2' => [
			'type' => 'string',
			'default' => 'flex-start',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio'], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-2']],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-2 .portfolio-wrapper{ align-items: {{textAlign2}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes']],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .asb-content{ align-items: {{textAlign2}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner'], ['key' => 'bannerStyle', 'relation' => '==', 'value' => 'info-banner-style-2']],
					'selector' => '{{PLUS_WRAP}}.info-banner-style-2 .info-banner-content-wrapper .info-front-content{ align-items: {{textAlign2}}; }',
				],
			],
			'scopy' => true,
		],
		'alignOffset' => [
			'type' => 'string',
			'default' => 'flex-start',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion']],
					'selector' => '{{PLUS_WRAP}}.image-accordion .asb-content{ justify-content: {{alignOffset}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio'], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1']],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .asb_wrap_list.tpgb-row{ align-items: {{alignOffset}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio'], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-2']],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-2 .portfolio-wrapper{ justify-content: {{alignOffset}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner'], ['key' => 'bannerStyle', 'relation' => '==', 'value' => 'info-banner-style-2']],
					'selector' => '{{PLUS_WRAP}}.info-banner-style-2 .info-banner-content-wrapper .info-front-content{ justify-content: {{alignOffset}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes']],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .service-item-loop .asb-content{ justify-content: {{alignOffset}};}',
				],
			],
			'scopy' => true,
		],
		'layoutHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.image-accordion .asb_wrap_list, {{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-wrapper, {{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-image { height: {{layoutHeight}}; } 
					{{PLUS_WRAP}}.portfolio.portfolio-style-2 .portfolio-wrapper, {{PLUS_WRAP}}.article-box-style-2 .article-box-front-wrapper, {{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper, {{PLUS_WRAP}}.info-banner.info-banner-style-2 .info-front-content, {{PLUS_WRAP}}.hover-section{ min-height: {{layoutHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'columns' => [
			'type' => 'object',
			'default' => [ 'md' => 3,'sm' => 4,'xs' => 6 ],
		],
		'sbTabColumn' => [
			'type' => 'string',
			'default' => 'sb_t_2',	
		],
		'sbMobColumn' => [
			'type' => 'string',
			'default' => 'sb_m_1',	
		],
		'columnGap' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes' ]],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .service-item-loop{padding: {{columnGap}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '!=', 'value' => ['image-accordion','sliding-boxes','portfolio'] ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop, {{PLUS_WRAP}}.services-element .se-wrapper-main{padding: {{columnGap}};}',
				],
			],
		],
		
		/* Title Style Start */
		'titleOnClick' => [
			'type' => 'string',
			'default' => 'Click Here',	
		],
		'titleTagType' => [
			'type' => 'string',
			'default' => 'h6',
			'scopy' => true,
		],
		'titleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-title',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio .service-item-loop .asb-title',
				],
			],
			'scopy' => true,
		],
		'titleNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-title{ color: {{titleNmlColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .service-item-loop .asb-title, {{PLUS_WRAP}}.portfolio.portfolio-style-2 .service-item-loop .asb-title{ color: {{titleNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '!=', 'value' => ['hover-section','portfolio'] ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-title{ color: {{titleHvrColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => ['hover-section','portfolio'] ]],
					'selector' => '{{PLUS_WRAP}}.hover-section .service-item-loop.active-hover .asb-title, {{PLUS_WRAP}}.portfolio .service-item-loop.active-port .asb-title{ color: {{titleHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'titleLinkTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio .pf_a_click',
				],
			],
			'scopy' => true,
		],
		'titleLinkColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio .pf_a_click{ color: {{titleLinkColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* Title Style End */
		
		/* SubTitle Style Start */
		'sTitleTagType' => [
			'type' => 'string',
			'default' => 'h6',
			'scopy' => true,
		],
		'subTitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-sub-title',
				],
			],
			'scopy' => true,
		],
		'subTtlNColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-sub-title{ color: {{subTtlNColor}}; }',
				],
			],
			'scopy' => true,
		],
		'subTtlHColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-sub-title{ color: {{subTtlHColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* SubTitle Style End */
		
		/* Desc Style Start */
		'descTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-desc',
				],
			],
			'scopy' => true,
		],
		'descNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-desc{ color: {{descNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'descHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-desc{ color: {{descHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* Desc Style End */
		
		/* Icon/Image Style Start */
		'iconStyle' => [
			'type' => 'string',
			'default' => 'icon-square',	
			'scopy' => true,
		],
		'iconWidth' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'portfolio' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon{ width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'iconImgSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => ['portfolio','services-element'] ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon{ font-size: {{iconImgSize}}; } {{PLUS_WRAP}} .service-item-loop img.asb-image{ height: {{iconImgSize}}; width: {{iconImgSize}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-wrapper .asb-icon-image{ font-size: {{iconImgSize}}; } {{PLUS_WRAP}}.services-element .se-wrapper img.asb-icon-image.asb-image{ height: {{iconImgSize}}; width: {{iconImgSize}}; }',
				],
			],
			'scopy' => true,
		],
		'icnInsetColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-2 .se-icon{ box-shadow:inset 0 0 0 2px {{icnInsetColor}}; } {{PLUS_WRAP}}.services-element.services-element-style-2 .se-wrapper:hover .se-icon{ box-shadow:inset 0 0 0 40px {{icnInsetColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon{ color: {{icnNmlColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-icon .asb-icon-image{ color: {{icnNmlColor}}; } {{PLUS_WRAP}}.services-element.services-element-style-2 .se-wrapper-inner::after{ background-color: {{icnNmlColor}}18; }',
				],
			],
			'scopy' => true,
		],
		'icnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-icon{ color: {{icnHvrColor}}; }',
				],
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-wrapper:hover .asb-icon-image{ color: {{icnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNmlBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon-image',
				],
			],
			'scopy' => true,
		],
		'icnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
				'bgType' => 'color',
				'bgDefaultColor' => '',
				'bgGradient' => (object) [ 'color1' => '#16d03e', 'color2' => '#1f91f3', 'type' => 'linear', 'direction' => '90', 'start' => 5, 'stop' => 80, 'radial' => 'center', 'clip' => false ],
				'overlayBg' => '',
				'overlayBgOpacity' => '',
				'bgGradientOpacity' => ''
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-icon-image',
				],
			],
			'scopy' => true,
		],
		'icnNBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon-image{ border-color: {{icnNBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnHBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [ 
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-icon-image{ border-color: {{icnHBColor}}; }',
				],
			],
			'scopy' => true,
		],
		'icnNBRadius' => [
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
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon-image{border-radius: {{icnNBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'icnHBRadius' => [
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
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-icon-image{border-radius: {{icnHBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'icnNShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop .asb-icon-image',
				],
			],
			'scopy' => true,
		],
		'icnHShadow' => [
			'type' => 'object',
			'default' => (object) [
				'openShadow' => 0,
				'inset' => 0,
				'horizontal' => 0,
				'vertical' => 4,
				'blur' => 8,
				'spread' => 0,
				'color' => "rgba(0,0,0,0.40)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disIcnImg', 'relation' => '==', 'value' => true ],['key' => 'mainStyleType', 'relation' => '!=', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}} .service-item-loop:hover .asb-icon-image',
				],
			],
			'scopy' => true,
		],
		/* Icon/Image Style End */
		
		'marginBottomS1' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-liting-ul{ margin-bottom: {{marginBottomS1}}; }',
				],
			],
			'scopy' => true,
		],
		
		/* List Style Start */
		'listTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-listing',
				],
			],
			'scopy' => true,
		],
		'listColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-listing{ color: {{listColor}}; }',
				],
			],
			'scopy' => true,
		],
		'listDotColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-2 .se-listing:before{ box-shadow:0 0 0 2px {{listDotColor}}; } {{PLUS_WRAP}}.services-element.services-element-style-2 .se-listing:hover:before{ box-shadow:0 0 0 3px {{listDotColor}}; }',
				],
			],
			'scopy' => true,
		],
		/* List Style End */
		
		/* Button Style Start */
		'btnTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0,
				'size' => [ 'md' => '', 'unit' => 'px' ],
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'btnNmlColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap{ color: {{btnNmlColor}}; }',
				],
				(object) [
					'condition' => [
						(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true],
						['key' => 'btnStyle' , 'relation' => '==', 'value' => 'style-7']
					],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-7 .button-link-wrap:after{ border-color: {{btnNmlColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnHvrColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover{ color: {{btnHvrColor}}; }',
				],
			],
			'scopy' => true,
		],
		'btnTSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn' , 'relation' => '==', 'value' =>  true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-top: {{btnTSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'btnBSpace' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-bottom : {{btnBSpace}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconSpacing' => [
			'type' => 'object',
			'default' => [ 
				'md' => 5,
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .button-before { margin-right: {{btnIconSpacing}}; } {{PLUS_WRAP}} .button-link-wrap .button-after { margin-left: {{btnIconSpacing}}; }',
				],
			],
			'scopy' => true,
		],
		'btnIconSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}} .button-link-wrap .btn-icon { font-size: {{btnIconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'btnPadding' => [
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
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' =>true]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{ padding: {{btnPadding}}; }',
				],
			],
			'scopy' => true,
		],
		'btnNormalB' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'btnBRadius' => [
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
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{border-radius: {{btnBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'btnBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'btnShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => 0,
				'vertical' => 8,
				'blur' => 20,
				'spread' => 1,
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
				],
			],
			'scopy' => true,
		],
		'btnHvrB' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'btnHvrBRadius' => [
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
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover{border-radius: {{btnHvrBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'btnHvrBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		'btnHvrShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => '',
				'vertical' => '',
				'blur' => '',
				'spread' => '',
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'disBtn', 'relation' => '==', 'value' => true ], ['key' => 'btnStyle', 'relation' => '==', 'value' => 'style-8' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover',
				],
			],
			'scopy' => true,
		],
		/* Button Style End */
		
		'portS1Margin' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-wrapper{margin: {{portS1Margin}};}',
				],
			],
			'scopy' => true,
		],
		'portS1Padding' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-wrapper{padding: {{portS1Padding}};}',
				],
			],
			'scopy' => true,
		],
		'imgWidthHeight' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-image{ width: {{imgWidthHeight}}; height: {{imgWidthHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'featureImgBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-image',
				],
			],
			'scopy' => true,
		],
		'fImgBRadius' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-image{border-radius: {{fImgBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'fImgShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => '',
				'vertical' => '',
				'blur' => '',
				'spread' => '',
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-hover-image',
				],
			],
			'scopy' => true,
		],
		'visAllCont' => [
			'type' => 'boolean',
			'default' => false,	
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion' ], ['key' => 'visAllCont', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.image-accordion .asb-content { opacity: 1;}',
				],
			],
			'scopy' => true,
		],
		'contentPadding' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion' ]],
					'selector' => '{{PLUS_WRAP}}.image-accordion .asb-content{padding: {{contentPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box' ]],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-content{padding: {{contentPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-content-wrapper{padding: {{contentPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-2 .portfolio-wrapper{padding: {{contentPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner' ], ['key' => 'bannerStyle', 'relation' => '==', 'value' => 'info-banner-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper, .info-banner.info-banner-style-1 .info-banner-back-content-inner, .info-banner-style-2 .info-front-content{padding: {{contentPadding}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-wrapper-main{padding: {{contentPadding}};}',
				],
			],
			'scopy' => true,
		],
		'seContentPadding' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper{padding: {{seContentPadding}};} {{PLUS_WRAP}}.services-element.services-element-style-1 .se-listing-section{padding: 0 {{seContentPadding}};}',
				],
			],
			'scopy' => true,
		],
		'serEls2BG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper',
				],
			],
			'scopy' => true,
		],
		'serEls2HBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper .se-listing-section',
				],
			],
			'scopy' => true,
		],
		'serEls2HBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper .se-listing-section',
				],
			],
			'scopy' => true,
		],
		'serEls2BRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '5',
					"right" => '5',
					"bottom" => '5',
					"left" => '5',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper {border-radius: {{serEls2BRadius}};}',
				],
			],
			'scopy' => true,
		],
		'serEls2HBRadius' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => [
					"top" => '5',
					"right" => '5',
					"bottom" => '5',
					"left" => '5',
				],
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper .se-listing-section {border-radius: {{serEls2HBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'serEls2HShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => '0',
				'vertical' => '0',
				'blur' => '30',
				'spread' => '0',
				'color' => "rgba(0,0,0,0.2)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.services-element.services-element-style-1 .se-wrapper .se-listing-section',
				],
			],
			'scopy' => true,
		],
		'iaS1Margin' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion' ], ['key' => 'imgAcrdnStyle', 'relation' => '==', 'value' => 'accordion-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.image-accordion.accordion-style-2 .service-item-loop{margin: calc( {{iaS1Margin}} / 2);}',
				],
			],
			'scopy' => true,
		],
		'contentMargin' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ], ['key' => 'articleStyle', 'relation' => '==', 'value' => 'article-box-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.article-box.article-box-style-1 .article-overlay{margin: {{contentMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner' ]],
					'selector' => '{{PLUS_WRAP}}.info-banner-style-2 .info-front-content, {{PLUS_WRAP}}.info-banner-style-1 .service-item-loop{margin: {{contentMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-1 .portfolio-content-wrapper{margin: {{contentMargin}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'services-element' ], ['key' => 'serviceEStyle', 'relation' => '==', 'value' => 'services-element-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.services-element .se-wrapper-main, {{PLUS_WRAP}}.services-element.services-element-style-2 .service-item-loop{margin: {{contentMargin}};}',
				],
			],
			'scopy' => true,
		],
		'hoverBGOverlay' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'portfolio' ], ['key' => 'portfolioStyle', 'relation' => '==', 'value' => 'portfolio-style-2' ]],
					'selector' => '{{PLUS_WRAP}}.portfolio.portfolio-style-2 .portfolio-wrapper{ box-shadow: {{hoverBGOverlay}} 0 0 0 2000px inset; }',
				],
			],
			'scopy' => true,
		],
		'articleS1BG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ], ['key' => 'articleStyle', 'relation' => '==', 'value' => 'article-box-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.article-box.article-box-style-1 .article-overlay',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion' ]],
					'selector' => '{{PLUS_WRAP}}.image-accordion .service-item-loop.active_accrodian .asb-content',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes' ]],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .service-item-loop .asb-content',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner' ]],
					'selector' => '{{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper ,{{PLUS_WRAP}}.info-banner-style-2 .info-front-content',
				],
			],
			'scopy' => true,
		],
		'contentBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes' ]],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .service-item-loop',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box' ]],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ]],
					'selector' => '{{PLUS_WRAP}}.article-box.article-box-style-1 .article-overlay, {{PLUS_WRAP}}.article-box-style-2 .article-box-main-wrapper',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner' ]],
					'selector' => '{{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper, {{PLUS_WRAP}}.info-banner-style-2 .info-front-content',
				],
			],
			'scopy' => true,
		],
		'contentBRadius' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'image-accordion' ]],
					'selector' => '{{PLUS_WRAP}}.image-accordion .service-item-loop{border-radius: {{contentBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'sliding-boxes' ]],
					'selector' => '{{PLUS_WRAP}}.sliding-boxes .service-item-loop{border-radius: {{contentBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box' ]],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper{border-radius: {{contentBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ]],
					'selector' => '{{PLUS_WRAP}}.article-box.article-box-style-1 .article-overlay, {{PLUS_WRAP}}.article-box-style-2 .article-box-main-wrapper{border-radius: {{contentBRadius}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner' ]],
					'selector' => '{{PLUS_WRAP}}.info-banner.info-banner-style-1 .info-banner-content-wrapper, {{PLUS_WRAP}}.info-banner-style-2 .info-front-content{border-radius: {{contentBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'fancyNmlOverlay' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box']],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper .fancybox-image-background{ box-shadow: {{fancyNmlOverlay}} 0 0 0 2000px inset; }',
				],
			],
			'scopy' => true,
		],
		'fancyHvrOverlay' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box']],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper:hover .fancybox-image-background{ box-shadow: {{fancyHvrOverlay}} 0 0 0 2000px inset; }',
				],
			],
			'scopy' => true,
		],
		'fancyLineSize' => [
			'type' => 'object',
			'default' => [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box']],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper:after{height: {{fancyLineSize}};}',
				],
			],
			'scopy' => true,
		],
		'fancyLineColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'fancy-box']],
					'selector' => '{{PLUS_WRAP}}.fancy-box .fancybox-inner-wrapper:after{ background: {{fancyLineColor}}; }',
				],
			],
			'scopy' => true,
		],
		
		'olayIBColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner'],(object) ['key' => 'bannerStyle', 'relation' => '==', 'value' => 'info-banner-style-1']],
					'selector' => '{{PLUS_WRAP}}.info-banner-style-1 .info-banner-content-wrapper .info-banner-back-content{ box-shadow: {{olayIBColor}} 0 0 0 2000px inset; }',
				],
			],
			'scopy' => true,
		],
		'olayIBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'info-banner'],(object) ['key' => 'bannerStyle', 'relation' => '==', 'value' => 'info-banner-style-1']],
					'selector' => '{{PLUS_WRAP}}.info-banner-style-1 .info-banner-content-wrapper .info-banner-back-content',
				],
			],
			'scopy' => true,
		],
		'outContentBdr' => [
			'type' => 'object',
			'default' => (object) [
				'openBorder' => 0,
				'type' => '',
				'color' => '',
				'width' => (object) [
					'md' => (object)[
						'top' => '1',
						'left' => '1',
						'bottom' => '1',
						'right' => '1',
					],
					"unit" => "px",
				],			
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ],(object) ['key' => 'articleStyle', 'relation' => '==', 'value' => 'article-box-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.article-box-style-1 .article-box-inner-content',
				],
			],
			'scopy' => true,
		],
		'outContentBRadius' => [
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
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ],(object) ['key' => 'articleStyle', 'relation' => '==', 'value' => 'article-box-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.article-box-style-1 .article-box-inner-content{border-radius: {{outContentBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'outContentShadow' => [
			'type' => 'object',
			'default' => (object) [
				'horizontal' => '',
				'vertical' => '',
				'blur' => '',
				'spread' => '',
				'color' => "rgba(0,0,0,0.27)",
			],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'mainStyleType', 'relation' => '==', 'value' => 'article-box' ],(object) ['key' => 'articleStyle', 'relation' => '==', 'value' => 'article-box-style-1' ]],
					'selector' => '{{PLUS_WRAP}}.article-box-style-1 .article-box-inner-content',
				],
			],
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-animated-service-boxes', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_animated_service_boxes_render_callback'
    ) );
}
add_action( 'init', 'tpgb_animated_service_boxes' );