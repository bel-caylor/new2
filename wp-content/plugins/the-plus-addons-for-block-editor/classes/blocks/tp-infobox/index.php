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
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$imgSrc ='';
	if(!empty($imageName) && !empty($imageName['id'])){
		$imgSrc = wp_get_attachment_image($imageName['id'] , $imageSize, false, ['class' => 'service-icon tpgb-trans-linear']);
	}else if(!empty($imageName['url'])){
		$imgSrc = '<img src="'.esc_url($imageName['url']).'" class="service-icon tpgb-trans-linear" />';
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
			$getTitle .='<div class="service-title tpgb-trans-linear">'.wp_kses_post($Title).'</div>';
		}
	}
	
	$getDesc = '';
	$getDesc .='<div class="service-desc tpgb-trans-linear">'.wp_kses_post($Description).'</div>';
	
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
	
    $output .= '<div class="tpgb-infobox tpgb-relative-block tpgb-trans-linear tpgb-block-'.esc_attr($block_id).' info-box-'.esc_attr($styleType).' '.esc_attr($blockClass).'">';
		$output .='<div class="post-inner-loop ">';
			$output .=$getInfoBox;
		$output .= '</div>';
    $output .= '</div>';
	
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
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
	if(!empty($style) || !empty($styleSm) || !empty($styleXs)){
		$output .= '<style>';
			$output .= $style;
			$output .= (!empty($styleSm)) ? '@media (max-width:1024px) and (min-width:768px){'.$styleSm.'}' : '';
			$output .= (!empty($styleXs)) ? '@media (max-width:767px){'.$styleXs.'}' : '';
		$output .= '</style>';
	}
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_infobox() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$plusButton_options = Tpgb_Blocks_Global_Options::load_plusButton_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
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
	$attributesOptions = array_merge($attributesOptions,$globalBgOption,$globalpositioningOption,$plusButton_options, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-infobox', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_infobox_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_infobox' );