<?php
/* Block : Info Box
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_infobox_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$layoutType = (!empty($attributes['layoutType'])) ? $attributes['layoutType'] : 'listing';
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$extBtnshow = (!empty($attributes['extBtnshow'])) ? $attributes['extBtnshow'] : false ;
	$verticalCenter = (!empty($attributes['verticalCenter'])) ? $attributes['verticalCenter'] : false;
	$sideImgBorder = (!empty($attributes['sideImgBorder'])) ? $attributes['sideImgBorder'] : false;
	$displayBorder = (!empty($attributes['displayBorder'])) ? $attributes['displayBorder'] : false;
	$dispPinText = (!empty($attributes['dispPinText'])) ? $attributes['dispPinText'] : false;
	$pinText = (!empty($attributes['pinText'])) ? $attributes['pinText'] : 'New';
	$IBoxLinkTgl = (!empty($attributes['IBoxLinkTgl'])) ? $attributes['IBoxLinkTgl'] : false;
	$IBoxLink = (!empty($attributes['IBoxLink']['url'])) ? $attributes['IBoxLink']['url'] : '';
	$target = (!empty($attributes['IBoxLink']['target'])) ? '_blank' : '';
	$nofollow = (!empty($attributes['IBoxLink']['nofollow'])) ? 'nofollow' : '';
	$iconType = (!empty($attributes['iconType'])) ? $attributes['iconType'] : 'icon';
	$iconOverlay = (!empty($attributes['iconOverlay'])) ? $attributes['iconOverlay'] : false;
	$imgOverlay = (!empty($attributes['imgOverlay'])) ? $attributes['imgOverlay'] : false;
	$iconShine = (!empty($attributes['iconShine'])) ? $attributes['iconShine'] : false;
	$IconName = (!empty($attributes['IconName'])) ? $attributes['IconName'] : '';
	$imageName = (!empty($attributes['imageName']['url'])) ? $attributes['imageName'] : '';
	$imageSize = (!empty($attributes['imageSize'])) ? $attributes['imageSize'] : 'full';
	$Title = (!empty($attributes['Title'])) ? $attributes['Title'] : '';
	$Description = (!empty($attributes['Description'])) ? $attributes['Description'] : '';
	$iconstyleType = (!empty($attributes['iconstyleType'])) ? $attributes['iconstyleType'] : 'none';
	$contenthoverEffect = (!empty($attributes['contenthoverEffect'])) ? $attributes['contenthoverEffect'] : '';
	
	$svgIcon = (!empty($attributes['svgIcon'])) ? $attributes['svgIcon'] : '';
	$svgDraw = (!empty($attributes['svgDraw'])) ? $attributes['svgDraw'] : 'delayed';
	$svgstroColor = (!empty($attributes['svgstroColor'])) ? $attributes['svgstroColor'] : '';
	$svgfillColor = (!empty($attributes['svgfillColor'])) ? $attributes['svgfillColor'] : 'none';
	$svgDura = (!empty($attributes['svgDura'])) ? $attributes['svgDura'] : 90;

	$titleType = (!empty($attributes['titleType'])) ? $attributes['titleType'] : 'div';
	$descType = (!empty($attributes['descType'])) ? $attributes['descType'] : 'div';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$count = '';
	$Sliderclass = $arrowCss = '';
	$carousel_settings = '';
	if($layoutType=='carousel'){
		$carousel_settings = Tp_Blocks_Helper::carousel_settings( $attributes );
		$carousel_settings = 'data-splide=\'' . json_encode($carousel_settings) . '\'';
				
		$Sliderclass .= 'tpgb-carousel splide';
		
		$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
		$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
		$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
		$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
		$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
		$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;

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
		
		$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
		$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
		$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
		
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
	}
	
	$imgSrc ='';
	if(!empty($imageName) && !empty($imageName['id'])){
		$imgSrc = wp_get_attachment_image($imageName['id'] , $imageSize, false, ['class' => 'service-icon tpgb-trans-linear']);
	}else if(!empty($imageName['url'])){
		$imgSrc = '<img src="'.esc_url($imageName['url']).'" class="service-icon tpgb-trans-linear" />';
	}
	if(class_exists('Tpgbp_Pro_Blocks_Helper')){
		$IBoxLink = (isset($attributes['IBoxLink']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($attributes['IBoxLink']) : (!empty($attributes['IBoxLink']['url']) ? $attributes['IBoxLink']['url'] : '');
	}
	
	$vcenter='';
	if(!empty($verticalCenter)){
		$vcenter = 'vertical-center';
	}
	
	$sib='';
	if($styleType=='style-1' || $styleType=='style-2'){
		if($iconType!='none' && !empty($sideImgBorder)){
			$sib = 'service-img-border';
		}
	}
	
	$icnOvrlay='';
	if(($styleType=='style-1' || $styleType=='style-2' || $styleType=='style-3') && (!empty($iconOverlay) || !empty($imgOverlay))){
		$icnOvrlay='icon-overlay';
	}
	
	$iconShineShow='';
	if(!empty($iconShine)){
		$iconShineShow='icon-shine-show';
	}
	
	$mlr16='';
	if($styleType=='style-1' && $iconType!='none'){ 
			$mlr16 = 'm-r-16 style-1 '; 
	}else if($styleType=='style-2' && $iconType!='none'){ 
			$mlr16 = 'm-l-16 style-2 ';
	}else if($styleType=='style-4' && $iconType!='none'){ 
			$mlr16 = 'm-r-16';
	}else if($styleType=='style-5' && $iconType!='none'){ 
			$mlr16 = 'service-bg-5';
	}else if($styleType=='style-6' && $iconType!='none'){ 
			$mlr16 = '';
	}
	
	$getIcon = '';
	if(!empty($iconType)){
			$getIcon .='<div class="info-icon-content">';
				if($iconType!='none' && !empty($dispPinText)){
					$getIcon .='<div class="info-pin-text tpgb-trans-easeinout">'.wp_kses_post($pinText).'</div>';
				}
				$getIcon .='<div class="service-icon-wrap tpgb-trans-linear">';
				if($iconType=='icon'){
					$getIcon .='<span class="service-icon tpgb-trans-linear '.esc_attr($iconShineShow).' icon-'.esc_attr($iconstyleType).'">';
					$getIcon .='<i class="'.esc_attr($IconName).'"></i>';
					$getIcon .='</span>';
				}else if($iconType=='image'){
					$getIcon .= $imgSrc;
				}else if($iconType=='svg' && !empty($svgIcon) && !empty($svgIcon['url'])){
					$getIcon .= '<div class="tpgb-draw-svg tpgb-trans-linear" data-id="service-svg-'.esc_attr($block_id).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes">';
						$getIcon .= '<object id="service-svg-'.esc_attr($block_id).'" type="image/svg+xml" role="none" data="'.esc_url($svgIcon['url']).'">';
						$getIcon .= '</object>';
					$getIcon .= '</div>';
				}
				$getIcon .='</div>';
			$getIcon .='</div>';
	}
	
	$getTitle = '';
	if(!empty($Title)){
		if(!$IBoxLinkTgl && !empty($IBoxLink)){
			$link_attr = Tp_Blocks_Helper::add_link_attributes($attributes['IBoxLink']);
			$getTitle .='<a href="'.esc_url($IBoxLink).'" class="service-title tpgb-trans-linear" target="'.esc_attr($target).'"  rel="'.esc_attr($nofollow).'" '.$link_attr.'>'.wp_kses_post($Title).'</a>';
		}else{
			$getTitle .='<'.Tp_Blocks_Helper::validate_html_tag($titleType).' class="service-title tpgb-trans-linear">';
				$getTitle .= wp_kses_post($Title);
			$getTitle .='</'.Tp_Blocks_Helper::validate_html_tag($titleType).'>';
		}
	}
	
	$getDesc = '';
	$getDesc .='<'.Tp_Blocks_Helper::validate_html_tag($descType).' class="service-desc tpgb-trans-linear">';
		$getDesc .= wp_kses_post($Description);
	$getDesc .='</'.Tp_Blocks_Helper::validate_html_tag($descType).'>';
	
	$getBorder='';
	$getBorder .='<div class="service-border"></div>';
	
	$getbutton = '';
	$getbutton .= Tpgb_Blocks_Global_Options::load_plusButton_saves($attributes);

	$cnt_hvr_class = $contenthoverEffect;
		
	if($contenthoverEffect == 'bounce_in'){
		$cnt_hvr_class = 'bounce-in';
	}
	if($contenthoverEffect == 'radial'){
		$cnt_hvr_class = 'shadow_radial';
	}
	
	$getInfoBox='';
	$getInfoBox .='<div class="info-box-inner tpgb-trans-linear tpgb_cnt_hvr_effect tpgb-relative-block tp-info-nc cnt_hvr_'.esc_attr($cnt_hvr_class).'">';
				if(!empty($IBoxLinkTgl) && !empty($IBoxLink)){
					$link_attr = Tp_Blocks_Helper::add_link_attributes($attributes['IBoxLink']);
					$getInfoBox .='<a href="'.esc_url($IBoxLink).'" class="info-box-bg-box tpgb-trans-linear '.esc_attr($icnOvrlay).'" target="'.esc_attr($target).'"  rel="'.esc_attr($nofollow).'" '.$link_attr.'>';
				}else{
					$getInfoBox .='<div class="info-box-bg-box tpgb-trans-linear '.esc_attr($icnOvrlay).'">';
				}
					if($styleType=='style-1'){
						$getInfoBox .='<div class="service-media text-left '.esc_attr($vcenter).'">';
							if($iconType!='none'){
								$getInfoBox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getInfoBox .=$getIcon;
								$getInfoBox .='</div>';
									
							}
							$getInfoBox .='<div class="service-content">';
								$getInfoBox .=$getTitle;
									if(!empty($displayBorder)){
										$getInfoBox .=$getBorder;
									}
								$getInfoBox .=$getDesc;
									if(!empty($extBtnshow)){
										$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
									}
							$getInfoBox .= '</div>';
						$getInfoBox .= '</div>';
					}
					if($styleType=='style-2'){
						$getInfoBox .='<div class="service-media text-right '.esc_attr($vcenter).'">';
							$getInfoBox .='<div class="service-content">';
								$getInfoBox .=$getTitle;
									if(!empty($displayBorder)){
										$getInfoBox .=$getBorder;
									}
								$getInfoBox .=$getDesc;
									if(!empty($extBtnshow)){
										$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
									}
							$getInfoBox .= '</div>';
							if($iconType!='none'){
								$getInfoBox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getInfoBox .=$getIcon;
								$getInfoBox .='</div>';
							}
						$getInfoBox .= '</div>';
					}
					if($styleType=='style-3'){
						$getInfoBox .='<div class="text-alignment">';
							$getInfoBox .='<div class="style-3">';
								if($iconType!='none'){
									$getInfoBox .=$getIcon;
								}
								$getInfoBox .=$getTitle;
								if(!empty($displayBorder)){
									$getInfoBox .=$getBorder;
								}
								$getInfoBox .=$getDesc;
								if(!empty($extBtnshow)){
									$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
								}
							$getInfoBox .= '</div>';
						$getInfoBox .= '</div>';
					}
					if($styleType=='style-4'){
						$getInfoBox .='<div class="service-media text-left '.esc_attr($vcenter).'">';
							if($iconType!='none'){
								$getInfoBox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getInfoBox .=$getIcon;
								$getInfoBox .='</div>';
							}
							$getInfoBox .='<div class="service-content">'.$getTitle.'</div>';
						$getInfoBox .= '</div>';
							if(!empty($displayBorder)){
								$getInfoBox .=$getBorder;
							}
							$getInfoBox .=$getDesc;
							if(!empty($extBtnshow)){
								$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
							}
					}
					if($styleType=='style-5'){
						$getInfoBox .='<div class="service-media  text-left">';
							if($iconType!='none'){
								$getInfoBox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getInfoBox .=$getIcon;
								$getInfoBox .='</div>';
							}
							$getInfoBox .='<div class="style-5-service-content">';
								$getInfoBox .=$getTitle;
								if(!empty($displayBorder)){
									$getInfoBox .=$getBorder;
								}
								$getInfoBox .=$getDesc;
								if(!empty($extBtnshow)){
									$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
								}
							$getInfoBox .= '</div>';
						$getInfoBox .= '</div>';
					}
					if($styleType=='style-6'){
						$getInfoBox .='<div class="style-6 text-center">';
							$getInfoBox .='<div class="info-box-all">';
								$getInfoBox .='<div class="info-box-wrapper">';
									$getInfoBox .='<div class="info-box-content">';
										$getInfoBox .='<div class="info-box-icon-img">';
										if($iconType!='none'){
											$getInfoBox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
												$getInfoBox .=$getIcon;
											$getInfoBox .='</div>';
										}
										$getInfoBox .='</div>';
										$getInfoBox .=$getTitle;
										$getInfoBox .='<div class="info-box-title-hide">'.wp_kses_post($Title).'</div>';
											if(!empty($displayBorder)){
												$getInfoBox .=$getBorder;
											}
											$getInfoBox .=$getDesc;
											if(!empty($extBtnshow)){
												$getInfoBox .='<div class="infobox-btn-block ">'.$getbutton.'</div>';
											}
									$getInfoBox .= '</div>';
								$getInfoBox .= '</div>';
							$getInfoBox .= '</div>';
						$getInfoBox .= '</div>';
					}
				
				if(!empty($IBoxLinkTgl) && !empty($IBoxLink)){
					$getInfoBox .= '</a>';
				}else{
					$getInfoBox .= '</div>';
				}
				
				$getInfoBox .= '<div class="infobox-overlay-color tpgb-trans-linear"></div>';
				
			$getInfoBox .= '</div>';
	
    $output .= '<div class="tpgb-infobox tpgb-relative-block tpgb-trans-linear tpgb-block-'.esc_attr($block_id).' '.esc_attr($Sliderclass).' info-box-'.esc_attr($styleType).' '.esc_attr($blockClass).'" '.$carousel_settings.'>';
		if($layoutType == 'carousel'){
			if( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ){
				$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle, $arrowsPosition);
			}
			$output .= '<div class="post-loop-inner splide__track">';
				$output .= '<div class="splide__list">';
					$output .= getCInfobox($attributes);
				$output .= '</div>';
			$output .= '</div>';
		}else{
			$output .='<div class="post-inner-loop ">';
				$output .=$getInfoBox;
			$output .= '</div>';
		}

		$style = $styleSm = $styleXs = '';
		if(!empty($iconOverlay) || !empty($imgOverlay)){
			$attributes = json_decode(json_encode($attributes), true);
			$boxPadding = (!empty($attributes['boxPadding'])) ? $attributes['boxPadding'] : '';
			
			if($styleType=='style-1'){
				$boxPaddingMd = (!empty($boxPadding['md']) && !empty($boxPadding['md']['left'])) ? $boxPadding['md']['left'] : '15';
				$boxPaddingSm = (!empty($boxPadding['sm']) && !empty($boxPadding['sm']['left'])) ? $boxPadding['sm']['left'] : '';
				$boxPaddingXs = (!empty($boxPadding['xs']) && !empty($boxPadding['xs']['left'])) ? $boxPadding['xs']['left'] : '';
			}
			if($styleType=='style-2'){
				$boxPaddingMd = (!empty($boxPadding['md']) && !empty($boxPadding['md']['right'])) ? $boxPadding['md']['right'] : '15';
				$boxPaddingSm = (!empty($boxPadding['sm']) && !empty($boxPadding['sm']['right'])) ? $boxPadding['sm']['right'] : '';
				$boxPaddingXs = (!empty($boxPadding['xs']) && !empty($boxPadding['xs']['right'])) ? $boxPadding['xs']['right'] : '';
			}
			if($styleType=='style-3'){
				$boxPaddingMd = (!empty($boxPadding['md']) && !empty($boxPadding['md']['top'])) ? $boxPadding['md']['top'] : '15';
				$boxPaddingSm = (!empty($boxPadding['sm']) && !empty($boxPadding['sm']['top'])) ? $boxPadding['sm']['top'] : '';
				$boxPaddingXs = (!empty($boxPadding['xs']) && !empty($boxPadding['xs']['top'])) ? $boxPadding['xs']['top'] : '';
			}
			
			$boxPaddingMd = (!empty($boxPaddingMd)) ? $boxPaddingMd.$boxPadding['unit'] : '15px';
			$boxPaddingSm = (!empty($boxPaddingSm)) ? $boxPaddingSm.$boxPadding['unit'] : '';
			$boxPaddingXs = (!empty($boxPaddingXs)) ? $boxPaddingXs.$boxPadding['unit'] : '';
			
			if($styleType=='style-1'){
				$style .= '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-1 .icon-overlay .m-r-16{left: calc(0% - '.esc_attr($boxPaddingMd).');}';
				$styleSm .= (!empty($boxPaddingSm)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-1 .icon-overlay .m-r-16{left: calc(0% - '.esc_attr($boxPaddingSm).');}' : '';
				$styleXs .= (!empty($boxPaddingXs)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-1 .icon-overlay .m-r-16{left: calc(0% - '.esc_attr($boxPaddingXs).');}' : '';
			}
			if($styleType=='style-2'){
				$style .= '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-2 .icon-overlay .m-l-16{right: calc(0% - '.esc_attr($boxPaddingMd).');}';
				$styleSm .= (!empty($boxPaddingSm)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-2 .icon-overlay .m-l-16{right: calc(0% - '.esc_attr($boxPaddingSm).');}' : '';
				$styleXs .= (!empty($boxPaddingXs)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-2 .icon-overlay .m-l-16{right: calc(0% - '.esc_attr($boxPaddingXs).');}' : '';
			}
			if($styleType=='style-3'){
				$style .= '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-3 .icon-overlay .info-icon-content{top: calc(0% - '.esc_attr($boxPaddingMd).');}';
				$styleSm .= (!empty($boxPaddingSm)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-3 .icon-overlay .info-icon-content{top: calc(0% - '.esc_attr($boxPaddingSm).');}' : '';
				$styleXs .= (!empty($boxPaddingXs)) ? '.tpgb-block-'.esc_attr($block_id).'.tpgb-infobox.info-box-style-3 .icon-overlay .info-icon-content{top: calc(0% - '.esc_attr($boxPaddingXs).');}' : '';
			}
		}

		if(!empty($style) || !empty($styleSm) || !empty($styleXs)){
			$output .= '<style>';
				$output .= $style;
				$output .= (!empty($styleSm)) ? '@media (max-width:1024px) and (min-width:768px){'.$styleSm.'}' : '';
				$output .= (!empty($styleXs)) ? '@media (max-width:767px){'.$styleXs.'}' : '';
			$output .= '</style>';
		}
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	if($layoutType=='carousel' && !empty($arrowCss)){
		$output .= $arrowCss;
	}
    return $output;
}

function getCInfobox($attributes){
	$styleType = (!empty($attributes['styleType'])) ? $attributes['styleType'] : 'style-1';
	$iboxcarousel = (!empty($attributes['iboxcarousel'])) ? $attributes['iboxcarousel'] : [];
	$carouselBtn = (!empty($attributes['carouselBtn'])) ? $attributes['carouselBtn'] : false;
	$carBtnStyle = (!empty($attributes['carBtnStyle'])) ? $attributes['carBtnStyle'] : 'style-7';
	$carBtnIconType = (!empty($attributes['carBtnIconType'])) ? $attributes['carBtnIconType'] : 'none';
	$carBtnIconName = (!empty($attributes['carBtnIconName'])) ? $attributes['carBtnIconName'] : '';
	$carBtnIconPosition = (!empty($attributes['carBtnIconPosition'])) ? $attributes['carBtnIconPosition'] : 'after';

	$verticalCenter = (!empty($attributes['verticalCenter'])) ? $attributes['verticalCenter'] : false;
	$sideImgBorder = (!empty($attributes['sideImgBorder'])) ? $attributes['sideImgBorder'] : false;
	$displayBorder = (!empty($attributes['displayBorder'])) ? $attributes['displayBorder'] : false;

	$iconOverlay = (!empty($attributes['iconOverlay'])) ? $attributes['iconOverlay'] : false;
	$imgOverlay = (!empty($attributes['imgOverlay'])) ? $attributes['imgOverlay'] : false;
	$iconShine = (!empty($attributes['iconShine'])) ? $attributes['iconShine'] : false;

	$svgDraw = (!empty($attributes['svgDraw'])) ? $attributes['svgDraw'] : 'delayed';
	$svgstroColor = (!empty($attributes['svgstroColor'])) ? $attributes['svgstroColor'] : '';
	$svgfillColor = (!empty($attributes['svgfillColor'])) ? $attributes['svgfillColor'] : 'none';
	$svgDura = (!empty($attributes['svgDura'])) ? $attributes['svgDura'] : 90;

	$titleType = (!empty($attributes['titleType'])) ? $attributes['titleType'] : 'div';
	$descType = (!empty($attributes['descType'])) ? $attributes['descType'] : 'div';

	$iconstyleType = (!empty($attributes['iconstyleType'])) ? $attributes['iconstyleType'] : 'none';
	$contenthoverEffect = (!empty($attributes['contenthoverEffect'])) ? $attributes['contenthoverEffect'] : '';

	$vcenter='';
	if(!empty($verticalCenter)){
		$vcenter = 'vertical-center';
	}
	
	$icnOvrlay='';
	if(($styleType=='style-1' || $styleType=='style-2' || $styleType=='style-3') && (!empty($iconOverlay) || !empty($imgOverlay))){
		$icnOvrlay='icon-overlay';
	}
	
	$iconShineShow='';
	if(!empty($iconShine)){
		$iconShineShow='icon-shine-show';
	}

	$cnt_hvr_class = $contenthoverEffect;
		
	if($contenthoverEffect == 'bounce_in'){
		$cnt_hvr_class = 'bounce-in';
	}
	if($contenthoverEffect == 'radial'){
		$cnt_hvr_class = 'shadow_radial';
	}
	$count = '';

	$getCInfobox = '';
	if(!empty($iboxcarousel)){
		foreach ( $iboxcarousel as $index => $item ) :

			$count++;

			$mlr16='';
			if($styleType=='style-1' && $item['iconType']!='none'){ 
				$mlr16 = 'm-r-16 style-1 '; 
			}else if($styleType=='style-2' && $item['iconType']!='none'){ 
				$mlr16 = 'm-l-16 style-2 ';
			}else if($styleType=='style-4' && $item['iconType']!='none'){ 
				$mlr16 = 'm-r-16';
			}else if($styleType=='style-5' && $item['iconType']!='none'){ 
				$mlr16 = 'service-bg-5';
			}else if($styleType=='style-6' && $item['iconType']!='none'){ 
				$mlr16 = '';
			}

			$sib='';
			if($styleType=='style-1' || $styleType=='style-2'){
				if($item['iconType']!='none' && !empty($sideImgBorder)){
					$sib = 'service-img-border';
				}
			}

			$getCTitle = '';
			$gttTitle = (!empty($item['Title'])) ? $item['Title'] : '';
			if(!empty($item['Title'])){
				$getCTitle .='<'.Tp_Blocks_Helper::validate_html_tag($titleType).' class="service-title tpgb-trans-linear">';
					$getCTitle .= wp_kses_post($item['Title']);
				$getCTitle .='</'.Tp_Blocks_Helper::validate_html_tag($titleType).'>';
			}
			
			$getCDesc = '';
			if(!empty($item['Description'])){
				$getCDesc .='<'.Tp_Blocks_Helper::validate_html_tag($descType).' class="service-desc tpgb-trans-linear">';
					$getCDesc .= wp_kses_post($item['Description']);
				$getCDesc .='</'.Tp_Blocks_Helper::validate_html_tag($descType).'>';
			}

			$getCBorder='';
			$getCBorder .='<div class="service-border"></div>';

			$imgCSrc ='';
			$imageName = (!empty($item['imageName']['url'])) ? $item['imageName'] : '';
			$imageSize = (!empty($item['imageSize'])) ? $item['imageSize'] : 'full';
			if(!empty($imageName) && !empty($imageName['id'])){
				$imgCSrc = wp_get_attachment_image($imageName['id'] , $imageSize, false, ['class' => 'service-icon tpgb-trans-linear']);
			}else if(!empty($imageName['url'])){
				$imgCSrc = '<img src="'.esc_url($imageName['url']).'" class="service-icon tpgb-trans-linear" />';
			}
			$getCIcon = '';
			if(!empty($item['iconType'])){
				$getCIcon .='<div class="info-icon-content">';
					if($item['iconType']!='none' && !empty($item['dispPinText'])){
						$getCIcon .='<div class="info-pin-text tpgb-trans-easeinout">'.wp_kses_post($item['pinText']).'</div>';
					}
					$getCIcon .='<div class="service-icon-wrap tpgb-trans-linear">';
						if($item['iconType']=='icon'){
							$getCIcon .='<span class="service-icon tpgb-trans-linear '.esc_attr($iconShineShow).' icon-'.esc_attr($iconstyleType).'">';
							$getCIcon .='<i class="'.esc_attr($item['IconName']).'"></i>';
							$getCIcon .='</span>';
						}else if($item['iconType']=='image'){
							$getCIcon .= $imgCSrc;
						}else if($item['iconType']=='svg' && !empty($item['svgIcon']) && !empty($item['svgIcon']['url'])){
							$getCIcon .= '<div class="tpgb-draw-svg tpgb-trans-linear" data-id="service-svg-'.esc_attr($item['_key']).'" data-type="'.esc_attr($svgDraw).'" data-duration="'.esc_attr($svgDura).'" data-stroke="'.esc_attr($svgstroColor).'" data-fillColor="'.esc_attr($svgfillColor).'" data-fillEnable="yes">';
								$getCIcon .= '<object id="service-svg-'.esc_attr($item['_key']).'" class="info-box-svg" type="image/svg+xml" role="none" data="'.esc_url($item['svgIcon']['url']).'">';
								$getCIcon .= '</object>';
							$getCIcon .= '</div>';
						}
					$getCIcon .='</div>';
				$getCIcon .='</div>';
			}

			$getCbutton = '';
			if(!empty($carouselBtn)){
				$btn_attr = Tp_Blocks_Helper::add_link_attributes($item['btnUrl']);
				$btnText = (!empty($item['btnText'])) ? $item['btnText'] : '';

				$btnUrl = (!empty($item['btnUrl'])) ? $item['btnUrl'] : '';
				$target = (!empty($btnUrl['target'])) ? '_blank' : '';
				$nofollow = (!empty($btnUrl['nofollow'])) ? 'nofollow' : '';

				$getBtnText = '<div class="btn-text">'.wp_kses_post($btnText).'</div>';
				
				$getCbutton .= '<div class="tpgb-adv-button button-'.esc_attr($carBtnStyle).'">';
					$getCbutton .= '<a href="'.esc_url($btnUrl['url']).'" class="button-link-wrap" role="button" target="'.esc_attr($target).'" rel="'.esc_attr($nofollow).'" '.$btn_attr.'>';
					if($carBtnStyle == 'style-8'){
						if($carBtnIconPosition == 'before'){
							if($carBtnIconType == 'icon'){
								$getCbutton .= '<span class="btn-icon  button-'.esc_attr($carBtnIconPosition).'">';
									$getCbutton .= '<i class="'.esc_attr($carBtnIconName).'"></i>';
								$getCbutton .= '</span>';
							}
							$getCbutton .= $getBtnText;
						}
						if($carBtnIconPosition == 'after'){
							$getCbutton .= $getBtnText;
							if($carBtnIconType == 'icon'){
								$getCbutton .= '<span class="btn-icon  button-'.esc_attr($carBtnIconPosition).'">';
									$getCbutton .= '<i class="'.esc_attr($carBtnIconName).'"></i>';
								$getCbutton .= '</span>';
							}
						}
					}
					if($carBtnStyle == 'style-7' || $carBtnStyle == 'style-9' ){
						$getCbutton .= $getBtnText;
						
						$getCbutton .= '<span class="button-arrow">';
						if($carBtnStyle == 'style-7'){
							$getCbutton .= '<span class="btn-right-arrow"><i class="fas fa-chevron-right"></i></span>';
						}
						if($carBtnStyle == 'style-9'){
							$getCbutton .= '<i class="btn-show fas fa-chevron-right"></i>';
							$getCbutton .= '<i class="btn-hide fas fa-chevron-right"></i>';
						}
						$getCbutton .= '</span>';
					}
					$getCbutton .= '</a>';
				$getCbutton .= '</div>';
			}

			$getCInfobox .='<div class="splide__slide info-box-inner tpgb-trans-linear tpgb_cnt_hvr_effect tpgb-relative-block tp-info-nc cnt_hvr_'.esc_attr($cnt_hvr_class).' tp-repeater-item-'.esc_attr($item['_key']).'" data-index="'.esc_attr($count).'">';
				
				$getCInfobox .='<div class="info-box-bg-box tpgb-trans-linear '.esc_attr($icnOvrlay).'">';
					if($styleType=='style-1'){
						$getCInfobox .='<div class="service-media text-left '.esc_attr($vcenter).'">';
							if($item['iconType']!='none'){
								$getCInfobox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getCInfobox .=$getCIcon;
								$getCInfobox .='</div>';
									
							}
							$getCInfobox .='<div class="service-content">';
								$getCInfobox .=$getCTitle;
									if(!empty($displayBorder)){
										$getCInfobox .=$getCBorder;
									}
								$getCInfobox .=$getCDesc;
									if(!empty($carouselBtn)){
										$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
									}
							$getCInfobox .= '</div>';
						$getCInfobox .= '</div>';
					}
					if($styleType=='style-2'){
						$getCInfobox .='<div class="service-media text-right '.esc_attr($vcenter).'">';
							$getCInfobox .='<div class="service-content">';
								$getCInfobox .=$getCTitle;
									if(!empty($displayBorder)){
										$getCInfobox .=$getCBorder;
									}
								$getCInfobox .=$getCDesc;
									if(!empty($carouselBtn)){
										$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
									}
							$getCInfobox .= '</div>';
							if($item['iconType']!='none'){
								$getCInfobox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getCInfobox .=$getCIcon;
								$getCInfobox .='</div>';
							}
						$getCInfobox .= '</div>';
					}
					if($styleType=='style-3'){
						$getCInfobox .='<div class="text-alignment">';
							$getCInfobox .='<div class="style-3">';
								if($item['iconType']!='none'){
									$getCInfobox .=$getCIcon;
								}
								$getCInfobox .=$getCTitle;
								if(!empty($displayBorder)){
									$getCInfobox .=$getCBorder;
								}
								$getCInfobox .=$getCDesc;
								if(!empty($carouselBtn)){
									$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
								}
							$getCInfobox .= '</div>';
						$getCInfobox .= '</div>';
					}
					if($styleType=='style-4'){
						$getCInfobox .='<div class="service-media text-left '.esc_attr($vcenter).'">';
							if($item['iconType']!='none'){
								$getCInfobox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getCInfobox .=$getCIcon;
								$getCInfobox .='</div>';
							}
							$getCInfobox .='<div class="service-content">'.$getCTitle.'</div>';
						$getCInfobox .= '</div>';
							if(!empty($displayBorder)){
								$getCInfobox .=$getCBorder;
							}
							$getCInfobox .=$getCDesc;
							if(!empty($carouselBtn)){
								$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
							}
					}
					if($styleType=='style-5'){
						$getCInfobox .='<div class="service-media  text-left">';
							if($item['iconType']!='none'){
								$getCInfobox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
									$getCInfobox .=$getCIcon;
								$getCInfobox .='</div>';
							}
							$getCInfobox .='<div class="style-5-service-content">';
								$getCInfobox .=$getCTitle;
								if(!empty($displayBorder)){
									$getCInfobox .=$getCBorder;
								}
								$getCInfobox .=$getCDesc;
								if(!empty($carouselBtn)){
									$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
								}
							$getCInfobox .= '</div>';
						$getCInfobox .= '</div>';
					}
					if($styleType=='style-6'){
						$getCInfobox .='<div class="style-6 text-center">';
							$getCInfobox .='<div class="info-box-all">';
								$getCInfobox .='<div class="info-box-wrapper">';
									$getCInfobox .='<div class="info-box-content">';
										$getCInfobox .='<div class="info-box-icon-img">';
										if($item['iconType']!='none'){
											$getCInfobox .='<div class="'.esc_attr($mlr16).' '.esc_attr($sib).'">';
												$getCInfobox .=$getCIcon;
											$getCInfobox .='</div>';
										}
										$getCInfobox .='</div>';
										$getCInfobox .=$getCTitle;
										$getCInfobox .='<div class="info-box-title-hide">'.wp_kses_post($gttTitle).'</div>';
											if(!empty($displayBorder)){
												$getCInfobox .=$getCBorder;
											}
											$getCInfobox .=$getCDesc;
											if(!empty($carouselBtn)){
												$getCInfobox .='<div class="infobox-btn-block ">'.$getCbutton.'</div>';
											}
									$getCInfobox .= '</div>';
								$getCInfobox .= '</div>';
							$getCInfobox .= '</div>';
						$getCInfobox .= '</div>';
					}
				
				$getCInfobox .= '</div>';
				
				$getCInfobox .= '<div class="infobox-overlay-color tpgb-trans-linear"></div>';
				
			$getCInfobox .= '</div>';

		endforeach;
	}

	return $getCInfobox;
}

/**
 * Render for the server-side
 */
function tpgb_tp_infobox() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'layoutType' => [
				'type' => 'string',
				'default' => 'listing',	
			],
			'styleType' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'Alignment' => [
				'type' => 'object',
				'default' => 'center',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}} .text-alignment{ text-align: {{Alignment}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ],
													['key' => 'Alignment', 'relation' => '==', 'value' => 'center' ]],
						'selector' => '{{PLUS_WRAP}} .text-alignment .service-border{ margin-left:auto;margin-right:auto; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ],
													['key' => 'Alignment', 'relation' => '==', 'value' => 'left' ]],
						'selector' => '{{PLUS_WRAP}} .text-alignment .service-border{ margin-right:auto; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-3' ],
													['key' => 'Alignment', 'relation' => '==', 'value' => 'right' ]],
						'selector' => '{{PLUS_WRAP}} .text-alignment .service-border{ margin-left:auto; }',
					],
				],
				'scopy' => true,
			],
			'Title' => [
				'type' => 'string',
				'default' => 'Amazing Feature',	
			],
			'Description' => [
				'type' => 'string',
				'default' => 'Disrupt inspire and think tank, social entrepreneur but preliminary thinking think tank compelling. Inspiring, invest synergy capacity building, white paper; silo, unprecedented challenge B-corp problem-solvers.',	
			],
			'iconType' => [
				'type' => 'string',
				'default' => 'icon',	
			],
			'IconName' => [
				'type'=> 'string',
				'default'=> 'fab fa-angellist',
			],
			'imageName' => [
				'type' => 'object',
				'default' => [],
			],
			'svgIcon' => [
				'type' => 'object',
				'default' => [],
			],
			'imageSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'dispPinText' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'pinText' => [
				'type' => 'string',
				'default' => 'New',	
			],
			'IBoxLink' => [
				'type'=> 'object',
				'default'=> [
					'url' => '',	
					'target' => '',
					'nofollow' => ''
				],
			],
			'IBoxLinkTgl' => [
				'type' => 'boolean',
				'default' => false,	
			],

			'carouselBtn' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'carBtnStyle' => [
				'type' => 'string',
				'default' => 'style-7',	
			],
			'carBtnIconType'  => [
				'type' => 'string' ,
				'default' => 'none',	
			],
			'carBtnIconName' => [
				'type'=> 'string',
				'default'=> '',
			],
			'carBtnIconPosition' => [
				'type'=> 'string',
				'default'=> 'after',
			],

			'iboxcarousel' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'Title' => [
							'type' => 'string',
							'default' => 'Amazing Feature'
						],
						'Description' => [
							'type' => 'string',
							'default' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.'
						],
						'btnText' => [
							'type' => 'string',
							'default' => 'Read more',	
						],
						'btnUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'iconType' => [
							'type' => 'string',
							'default' => 'icon'
						],
						'IconName' => [
							'type'=> 'string',
							'default' => 'fab fa-angellist'
						],
						'imageName' => [
							'type' => 'object',
							'default' => [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
							],
						],
						'svgIcon' => [
							'type' => 'object',
							'default' => [
								'url' => '',
							],
						],
						'dispPinText' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'pinText' => [
							'type' => 'string',
							'default' => 'New',	
						],
						'IBoxLink' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',	
								'target' => '',
								'nofollow' => ''
							],
						],
						'IBoxLinkTgl' => [
							'type' => 'boolean',
							'default' => false,	
						],
					],
				],
				'default' => [
					[
						'_key' => '0',
						'Title' => 'Amazing Feature 1',
						'Description' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.',
						'iconType' => 'icon',
						'IconName'=> 'fab fa-angellist',
						'btnText'=> 'Read More',
						'btnUrl' => ['url'  => ''],
						'imageName' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
						'svgIcon' => [
							'url' => '',
						],
						'dispPinText' => false,
						'pinText' => 'New',
						'IBoxLink' => ['url'  => ''],
						'IBoxLinkTgl' => false
					],
					[
						'_key' => '1',
						'Title' => 'Amazing Feature 2',
						'Description' => 'Lookout flogging bilge rat main sheet bilge water nipper fluke to go on account heave down clap of thunder. Reef sails six pounders skysail code of conduct sloop cog Yellow Jack gunwalls grog blossom starboard.',
						'iconType' => 'icon',
						'IconName'=> 'fab fa-angellist',
						'btnText'=> 'Read More',
						'btnUrl' => ['url'  => ''],
						'imageName' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
						'svgIcon' => [
							'url' => '',
						],
						'dispPinText' => false,
						'pinText' => 'New',
						'IBoxLink' => ['url'  => ''],
						'IBoxLinkTgl' => false
					] 
				]
			],
			'titleType' => [
				'type' => 'string',
				'default' => 'div',
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
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-title',
					],
				],
				'scopy' => true,
			],
			'titlePadding' => [
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
						'selector' => '{{PLUS_WRAP}} .service-title{padding: {{titlePadding}};}',
					],
				],
				'scopy' => true,
			],
			'titleNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-title{ color: {{titleNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'titleHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [ 
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-title{ color: {{titleHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'titleTopSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-title{ margin-top: {{titleTopSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'titleBottomSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '!=', 'value' => 'style-4' ],['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-title{ margin-bottom: {{titleBottomSpace}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => 'style-4' ],['key' => 'Title', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-media{ margin-bottom: {{titleBottomSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'displayBorder' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'displayBdrWidth' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "%"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'displayBorder', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .service-border{ width: {{displayBdrWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'displayBdrHeight' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "px"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'displayBorder', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .service-border{ border-width: {{displayBdrHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'borderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'displayBorder', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}} .service-border{ border-color: {{borderColor}}; }',
					],
				],
				'scopy' => true,
			],
			'descType' => [
				'type' => 'string',
				'default' => 'div',
				'scopy' => true,
			],
			'descTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Description', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-desc',
					],
				],
				'scopy' => true,
			],
			'descPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .service-desc{padding: {{descPadding}};}',
					],
				],
				'scopy' => true,
			],
			'descNmlColor' => [
				'type' => 'string',
				'default' => '',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Description', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .service-desc{ color: {{descNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'descHvrColor' => [
				'type' => 'string',
				'default' => '',	
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Description', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-desc{ color: {{descHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'normalBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			'HoverBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			'overlayNmlBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .infobox-overlay-color{ background: {{overlayNmlBG}}; }',
					],
				],
				'scopy' => true,
			],
			'overlayHvrBG' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .infobox-overlay-color{ background: {{overlayHvrBG}}; }',
					],
				],
				'scopy' => true,
			],
			'boxPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .info-box-bg-box{padding: {{boxPadding}};}',
					],
				],
				'scopy' => true,
			],
			'bgNmlBorder' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "px",
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			'bgHvrBorder' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "px",
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			
			'boxBdrNmlRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .info-box-bg-box,{{PLUS_WRAP}} .infobox-overlay-color{border-radius: {{boxBdrNmlRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBdrHvrRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .info-box-bg-box,{{PLUS_WRAP}} .info-box-inner:hover .infobox-overlay-color{border-radius: {{boxBdrHvrRadius}};}',
					],
				],
				'scopy' => true,
			],
			'nmlboxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			'hvrboxShadow' => [
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
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .info-box-bg-box',
					],
				],
				'scopy' => true,
			],
			'cBtnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'cBtnTextColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap{ color: {{cBtnTextColor}}; }',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle' , 'relation' => '==', 'value' => 'style-7']],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-7 .button-link-wrap:after{ border-color: {{cBtnTextColor}}; }',
					],
				],
				'scopy' => true,
			],
			'cBThoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover{ color: {{cBThoverColor}}; }',
					],
				],
				'scopy' => true,
			],
			'cBtnSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-top: {{cBtnSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'cBtnbottomSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button{ margin-bottom : {{cBtnbottomSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'cIconSpacing' => [
				'type' => 'object',
				'default' => [ 
					'md' => 5,
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .button-link-wrap .button-before { margin-right: {{cIconSpacing}}; } {{PLUS_WRAP}} .button-link-wrap .button-after { margin-left: {{cIconSpacing}}; }',
					],
				],
				'scopy' => true,
			],
			'cBtnIconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .button-link-wrap .btn-icon { font-size: {{cBtnIconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'cBtnPadding' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{ padding: {{cBtnPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'cBtnNormalB' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'cBtnBRadius' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap{border-radius: {{cBtnBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'cBtnBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'cBtnShadow' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'cBtnHvrB' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'cBtnHvrBRadius' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover{border-radius: {{cBtnHvrBRadius}};}',
					],
				],
				'scopy' => true,
			],
			'cBtnHvrBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button.button-style-8 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'cBtnHvrShadow' => [
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
						'condition' => [(object) ['key' => 'layoutType', 'relation' => '==', 'value' => 'carousel'], ['key' => 'carouselBtn', 'relation' => '==', 'value' => true], ['key' => 'btnCarouselStyle', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-adv-button .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'iconstyleType' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'iconSize' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon{ font-size: {{iconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'iconWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
						'selector' => '{{PLUS_WRAP}}  .info-box-inner .service-icon{ width: {{iconWidth}}; height: {{iconWidth}}; line-height: {{iconWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'iconNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon{ color: {{iconNormalColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon{ color: {{iconHoverColor}}; }',
					],
				],
				'scopy' => true,
			],
			'bgNormalColor' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '!=', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon',
					],
				],
				'scopy' => true,
			],
			'bgHoverColor' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '!=', 'value' => 'none' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon',
					],
				],
				'scopy' => true,
			],
			'iconBdrNmlRadius' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ], ['key' => 'iconstyleType', 'relation' => '==', 'value' => 'square' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon{border-radius: {{iconBdrNmlRadius}};}',
					],
				],
				'scopy' => true,
			],
			'iconBdrHvrRadius' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ], ['key' => 'iconstyleType', 'relation' => '==', 'value' => 'square' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon{border-radius: {{iconBdrHvrRadius}};}',
					],
				],
				'scopy' => true,
			],
			'iconBdrNmlType' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => 'solid',
					'disableWidthColor' => true,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .icon-square,{{PLUS_WRAP}} .info-box-inner .icon-rounded',
					],
				],
				'scopy' => true,
			],
			'iconBdrNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .icon-square,{{PLUS_WRAP}} .info-box-inner .icon-rounded{ border-color: {{iconBdrNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'iconBWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .icon-square,{{PLUS_WRAP}} .info-box-inner .icon-rounded{ border-width: {{iconBWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'iconBdrHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .icon-square,{{PLUS_WRAP}} .info-box-inner:hover .icon-rounded{ border-color: {{iconBdrHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'nmlIconShadow' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon',
					],
				],
				'scopy' => true,
			],
			'hvrIconShadow' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['square' , 'rounded'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon',
					],
				],
				'scopy' => true,
			],
			'nmlIcnShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'typeShadow' => 'drop-shadow',
					'horizontal' => 2,
					'vertical' => 3,
					'blur' => 2,
					'color' => "rgba(0,0,0,0.5)",
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['hexagon' , 'pentagon' , 'square-rotate'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon-wrap',
					],
				],
				'scopy' => true,
			],
			'hvrIcnShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'typeShadow' => 'drop-shadow',
					'horizontal' => 2,
					'vertical' => 3,
					'blur' => 2,
					'color' => "rgba(0,0,0,0.5)",
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'icon' ] , ['key' => 'iconstyleType', 'relation' => '==', 'value' => ['hexagon' , 'pentagon' , 'square-rotate'] ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon-wrap',
					],
				],
				'scopy' => true,
			],
			'iconOverlay' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'iconAdjust' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconOverlay', 'relation' => '==', 'value' => true ] , ['key' => 'styleType', 'relation' => '==', 'value' => ['style-1' , 'style-2'] ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox.info-box-style-1 .icon-overlay .m-r-16 , {{PLUS_WRAP}}.tpgb-infobox.info-box-style-2 .icon-overlay .m-l-16 { top: {{iconAdjust}};} ',
					],
				],
				'scopy' => true,
			],
			'iconShine' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'imageWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}}  .info-box-inner .service-icon{ width: {{imageWidth}}; height: {{imageWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'imgNmlBdr' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "px",
					],	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon',
					],
				],
				'scopy' => true,
			],
			'imgBdrNmlRadius' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon{border-radius: {{imgBdrNmlRadius}};}',
					],
				],
				'scopy' => true,
			],
			'normalImageShadow' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon',
					],
				],
				'scopy' => true,
			],
			'nmlImgDpShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'typeShadow' => 'drop-shadow',
					'horizontal' => 2,
					'vertical' => 3,
					'blur' => 2,
					'color' => "rgba(0,0,0,0.5)",
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .service-icon',
					],
				],
				'scopy' => true,
			],
			'imgHvrBdr' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "px",
					],	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon',
					],
				],
				'scopy' => true,
			],
			'imgBdrHvrRadius' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon{border-radius: {{imgBdrHvrRadius}};}',
					],
				],
				'scopy' => true,
			],
			'hoverImgShadow' => [
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
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon',
					],
				],
				'scopy' => true,
			],
			'hvrImgDpShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'typeShadow' => 'drop-shadow',
					'horizontal' => 2,
					'vertical' => 3,
					'blur' => 2,
					'color' => "rgba(0,0,0,0.5)",
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'iconType', 'relation' => '==', 'value' => 'image' ]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner:hover .service-icon',
					],
				],
				'scopy' => true,
			],
			'imgOverlay' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'imgAdjust' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'styleType', 'relation' => '==', 'value' => ['style-1' , 'style-2'] ] , ['key' => 'imgOverlay', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox.info-box-style-1 .icon-overlay .m-r-16 , {{PLUS_WRAP}}.tpgb-infobox.info-box-style-2 .icon-overlay .m-l-16 { top: {{imgAdjust}};}',
					],
				],
				'scopy' => true,
			],
			'pinTextTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-pin-text ',
					],
				],
				'scopy' => true,
			],
			'pinNmlBorder' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "px",
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinTextNmlColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text{ color: {{pinTextNmlColor}}; }',
					],
				],
				'scopy' => true,
			],
			'pinNmlBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinTextNmlRadius' => [
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
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text{border-radius: {{pinTextNmlRadius}};}',
					],
				],
				'scopy' => true,
			],
			'nmlPinShadow' => [
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
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinHvrBorder' => [
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
						'sm' => (object)[ ],
						'xs' => (object)[ ],
						"unit" => "",
					],			
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner:hover .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinTextHvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner:hover .info-pin-text{ color: {{pinTextHvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'pinHvrBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner:hover .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinTextHvrRadius' => [
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
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner:hover .info-pin-text{border-radius: {{pinTextHvrRadius}};}',
					],
				],
				'scopy' => true,
			],
			'hvrPinShadow' => [
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
						'condition' => [(object) ['key' => 'dispPinText', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner:hover .info-pin-text',
					],
				],
				'scopy' => true,
			],
			'pinSize' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text{padding: {{pinSize}};}',
					],
				],
				'scopy' => true,
			],
			'pinHrztlAdj' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text{ left: {{pinHrztlAdj}}; }',
					],
				],
				'scopy' => true,
			],
			'pinVrtclAdj' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-infobox .info-box-inner .info-pin-text{ top: {{pinVrtclAdj}}; }',
					],
				],
				'scopy' => true,
			],
			
			'verticalCenter' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'sideImgBorder' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'bdrRightColor' => [
				'type' => 'string',
				'default' => '',	
				'style' => [
						(object) [
						'condition' => [(object) ['key' => 'sideImgBorder', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .style-1.service-img-border,{{PLUS_WRAP}} .style-2.service-img-border{ color: {{bdrRightColor}}; }',
					],
				],
				'scopy' => true,
			],
			'minHeightTgl' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'minHeight' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'minHeightTgl', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .info-box-inner .info-box-bg-box{ min-height: {{minHeight}};display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-orient: vertical;-webkit-align-items: center;-ms-align-items: center;align-items: center; } {{PLUS_WRAP}}.info-box-style-3 .info-box-inner .info-box-bg-box{ -webkit-justify-content: center;-moz-justify-content: center;-ms-justify-content: center;justify-content: center; } {{PLUS_WRAP}}.info-box-style-2 .info-box-inner .info-box-bg-box{ -webkit-justify-content: flex-end;-moz-justify-content: flex-end;-ms-justify-content: flex-end;justify-content: flex-end; }',
					],
				],
				'scopy' => true,
			],
			'contenthoverEffect' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'shadowColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'contenthoverEffect', 'relation' => '==', 'value' => ['float_shadow','grow_shadow','radial']]],
						'selector' => '{{PLUS_WRAP}} .content_hover_float_shadow:before{background: -webkit-radial-gradient(center, ellipse, {{shadowColor}} 0%, rgba(60, 60, 60, 0) 70%);
							background: radial-gradient(ellipse at 50% 150%,{{shadowColor}} 0%, rgba(60, 60, 60, 0) 70%); } 
							{{PLUS_WRAP}} .content_hover_radial:before{background: -webkit-radial-gradient(center, ellipse at 50% 150%, {{shadowColor}} 0%, rgba(60, 60, 60, 0) 70%);
							background: radial-gradient(ellipse at 50% 150%,{{shadowColor}} 0%, rgba(60, 60, 60, 0) 70%); } 
							{{PLUS_WRAP}} .content_hover_radial:after {background: -webkit-radial-gradient(50% -50%, ellipse, {{shadowColor}} 0%, rgba(0, 0, 0, 0) 80%);
							background: radial-gradient(ellipse at 50% -50%, {{shadowColor}} 0%, rgba(0, 0, 0, 0) 80%);
							} 
							{{PLUS_WRAP}} .content_hover_grow_shadow:hover {-webkit-box-shadow: 0 10px 10px -10px {{shadowColor}};
								-moz-box-shadow: 0 10px 10px -10px {{shadowColor}};
								box-shadow: 0 10px 10px -10px {{shadowColor}};}
							',
					],
				],
				'scopy' => true,
			],

			'dotsBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-3','style-4','style-5','style-7'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}}.dots-style-2 ul.splide__pagination li button.splide__pagination__page,{{PLUS_WRAP}}.dots-style-3 .splide__pagination li button,{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button::before,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li button,{{PLUS_WRAP}}.dots-style-7 .splide__pagination li button{background:{{dotsBgColor}};}',
					],
				],
				'scopy' => true,
			],
			'dotsActiveBorderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-6'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 .splide__pagination li button.is-active::after{border-color: {{dotsActiveBorderColor}};}{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button.is-active{-webkit-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};-moz-box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};box-shadow: inset 0 0 0 1px {{dotsActiveBorderColor}};}{{PLUS_WRAP}}.dots-style-6 .splide__pagination button::after{color: {{dotsActiveBorderColor}};}',
					],
				],
				'scopy' => true,
			],
			'dotsActiveBgColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [ 
							(object) [ 'key' => 'dotsStyle', 'relation' => '==', 'value' => ['style-2','style-4','style-5','style-7'] ],
							(object) [ 'key' => 'showDots', 'relation' => '==', 'value' => true ]
						],
						'selector' => '{{PLUS_WRAP}}.dots-style-2 .splide__pagination li button.is-active::after,{{PLUS_WRAP}}.dots-style-4 .splide__pagination li button.is-active::before,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li:hover button,{{PLUS_WRAP}}.dots-style-5 .splide__pagination li button.is-active,{{PLUS_WRAP}}.dots-style-7 .splide__pagination li button.is-active{background: {{dotsActiveBgColor}};}',
					],
				],
				'scopy' => true,
			],
			'centerPadding' => [
				'type' => 'object',
				'default' => (object)[ 'md' => 0,'sm' => 0,'xs' => 0 ],
				'scopy' => true,
			],
			'centerSlideEffect' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'centerslideScale' => [
				'type' => 'string',
				'default' => 1,
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > div{-webkit-transform: scale({{centerslideScale}});-moz-transform: scale({{centerslideScale}});-ms-transform: scale({{centerslideScale}});-o-transform: scale({{centerslideScale}});transform: scale({{centerslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'normalslideScale' => [
				'type' => 'string',
				'default' => 1,
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'scale' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide  > div{-webkit-transform: scale({{normalslideScale}});-moz-transform: scale({{normalslideScale}});-ms-transform: scale({{normalslideScale}});-o-transform: scale({{normalslideScale}});transform: scale({{normalslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'slideOpacity' => [
				'type' => 'object',
				'default' => (object)[ 'md' => 1,'sm' => 1,'xs' => 1 ],
				'style' => [
						(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide:not(.is-active) > div{opacity:{{slideOpacity}};}{{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
					],
				],
				'scopy' => true,
			],
			'slideBoxShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
					'blur' => 8,
					'color' => "rgba(0,0,0,0.40)",
					'horizontal' => 0,
					'inset' => 0,
					'spread' => 0,
					'vertical' => 4
				],
				'style' => [
					(object) [
						'condition' => [
							(object) [ 'key' => 'centerMode', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'centerSlideEffect', 'relation' => '==', 'value' => 'shadow' ]
						],
						'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > div',
					],
				],
				'scopy' => true,
			],
			'slideheightRatio' => [
				'type' => 'string',
				'default' => '0.5',
				'scopy' => true,
			],
			'trimSpace' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			
			'svgDraw' => [
				'type' => 'string',
				'default' => 'delayed',	
				'scopy' => true,
			],
			'svgDura' => [
				'type' => 'string',
				'default' => '90',
				'scopy' => true,
			],
			'svgmaxWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .service-icon-wrap .tpgb-draw-svg{ max-width: {{svgmaxWidth}}; max-height: {{svgmaxWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'svgstroColor' => [
				'type' => 'string',
				'default' => '#000000',
				'scopy' => true,
			],
			'svgfillColor' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
		);
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption,$globalpositioningOption,$plusButton_options, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-infobox', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_infobox_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_infobox' );