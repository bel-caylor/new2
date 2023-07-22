<?php
/* Block : Timeline
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_timeline_render_callback( $attributes, $content) {
	$Timeline = '';
	$block_id = ( !empty($attributes['block_id']) ) ? $attributes['block_id'] : uniqid("title");
	$style = ( !empty($attributes['style']) ) ? $attributes['style'] : 'style-1';
	$MLayout = ( !empty($attributes['MLayout']) ) ? $attributes['MLayout'] : false;
	$RContent = ( !empty($attributes['RContent']) ) ? $attributes['RContent'] : [];
	$Alignment = ( !empty($attributes['Alignment']) ) ? $attributes['Alignment'] : 'center';
	$PinStyle = ( !empty($attributes['PinStyle']) ) ? $attributes['PinStyle'] : 'style-1';
	$StartPin = ( !empty($attributes['StartPin']) ) ? $attributes['StartPin'] : 'none';
	$StartText = ( !empty($attributes['StartText']) ) ? $attributes['StartText'] : '';
	$EndPin = ( !empty($attributes['EndPin']) ) ? $attributes['EndPin'] : 'none';
	$EndImage = ( !empty($attributes['EndImage']) && !empty($attributes['EndImage']['url']) ) ? $attributes['EndImage']['url'] : '';
	$EndText = ( !empty($attributes['EndText']) ) ? $attributes['EndText'] : '';
	$ArrowStyle = ( ($style == 'style-2') ? 'arrow-'.$attributes['ArrowStyle'] : '');
	$Rowclass = ( !empty($attributes['MLayout']) ) ? 'tpgb-row' : '';
	$titledivider = ( !empty($attributes['titledivider']) ) ? $attributes['titledivider'] : false;
	$timeediType = ( !empty($attributes['timeediType']) ) ? $attributes['timeediType'] : '';
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$layout=$Mtype='';
	if( $attributes['MLayout'] ){
		$layout = 'tpgb-isotope';
		$Mtype = 'masonry';
	}

	$Timeline .= '<div id="tpgb_timeline" class="tpgb-block-'.esc_attr($block_id).' tpgb-relative-block tpgb-timeline-list tpgb-layout '.esc_attr($layout).' timeline-'.esc_attr($Alignment).'-align timeline-'.esc_attr($style).' '.esc_attr($blockClass).'" data-id="'.esc_attr($block_id).'" data-masonry-type="'.esc_attr($Mtype).'">';
		$Timeline .= '<div class="'.esc_attr($Rowclass).' post-loop-inner tpgb-relative-block '.esc_attr($ArrowStyle).'" >';

			$Timeline .= '<div class="timeline-track"></div>';
			$Timeline .= '<div class="timeline-track timeline-track-draw"></div>';

			$StartIcon = '';
			if( $StartPin == 'icon' ){
				$StartIcon = '<i class="'.esc_attr($attributes['StartIcon']).' startImg"></i>';
			}else if( $StartPin == 'image' ){
				$StartImage ='';
				if( !empty($attributes['StartImage']) && !empty($attributes['StartImage']['id']) ){
					$StartImgSize = (!empty($attributes['StartImgSize']) ? $attributes['StartImgSize'] : 'full');
					$StartIcon = wp_get_attachment_image( $attributes['StartImage']['id'], $StartImgSize, false, ['class' => 'startImg']);
				}
			}
			if( $StartPin != 'none' ){
				$Timeline .= '<div class="timeline--icon">';
					if( !empty($StartIcon) ){
						$Timeline .= '<div class="tpgb-beginning-icon">'.wp_kses_post($StartIcon).'</div>';								
					}
					if( $StartPin == 'text' && !empty($StartText) ){
						$Timeline .= '<div class="tpgb-timeline-text tpgb-text-start">';
							$Timeline .= '<div class="beginning-text">'.wp_kses_post($StartText).'</div>';
						$Timeline .= '</div>';
					}
				$Timeline .= '</div>';
			}

			if($timeediType == 'editor'){
				$Timeline .= $content;
			}else{
				foreach ( $RContent as $index => $Content ) {
					$PinTitle = (!empty($Content['RTitle'])) ? $Content['RTitle'] : '';
					$RPosition = (!empty($Content['RPosition'])) ? $Content['RPosition'] : 'right';
					$RButton = (!empty($Content['RButton'])) ? $Content['RButton'] : false;
					$RBtnText = (!empty($Content['RBtnText'])) ? $Content['RBtnText'] : 'Read More';
					$RcType =  (!empty($Content['RcType'])) ? $Content['RcType'] : 'image';
					$Rnone =  (!empty($Content['Rnone'])) ? $Content['Rnone'] : 'icon';
					$Rimg = (!empty($Content['Rimg']) && !empty($Content['Rimg']['url'])) ? $Content['Rimg']['url'] : '';
					$ImageSize = (!empty($Content['RimgSize']) ? $Content['RimgSize'] : 'full');
					$Rfimage = (!empty($Content['Rfimage']) && !empty($Content['Rfimage']['url'])) ? $Content['Rfimage']['url'] : '';
					$ImgSize = (!empty($Content['RfImgSize']) ? $Content['RfImgSize'] : 'full');
					$CustomURL = (!empty($Content['RUrl']) && !empty($Content['RUrl']['url'])) ? $Content['RUrl']['url'] : '';
					$Target = (!empty($Content['RUrl']) && !empty($Content['RUrl']['target'])) ? 'target=_blank' : "";
					$Nofollow = (!empty($Content['RUrl']) && !empty($Content['RUrl']['nofollow'])) ? 'rel=nofollow' : "";
					$RcAlign = (!empty($Content['RcAlign'])) ? $Content['RcAlign'] : 'text-right';
					$BTNName = (!empty($Content['RBtnText'])) ? $Content['RBtnText'] : '';
					$RcTitle = (!empty($Content['RcTitle'])) ? $Content['RcTitle'] : '';

					$Timeline .= '<div class="grid-item timeline-item-wrap tp-repeater-item-'.esc_attr($Content['_key']).' timeline-'.esc_attr($Content['RSAlign']).'-content text-pin-position-'. esc_attr($Content['RPosition']) .' ">';
						$Timeline .= '<div class="timeline-inner-block timeline-transition">';
							
							$Timeline .= '<div class="timeline-item '.esc_attr($RcAlign).'">';
								$Timeline .= '<div class="timeline-item-content timeline-transition '.esc_attr($RcAlign).' ">';
									$Timeline .= '<div class="timeline-tl-before timeline-transition"></div>';
										$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($Content['RUrl']);
										if(!empty($RcTitle) && !empty($CustomURL)){
											$Timeline .= '<a class="timeline-item-heading timeline-transition" href="'.esc_url($CustomURL).'" '.esc_attr($Target).' '.esc_attr($Nofollow).' '.$link_attr.'>'. esc_html($RcTitle) .'</a>';
										}else if(!empty($RcTitle)){
											$Timeline .= '<h3 class="timeline-item-heading timeline-transition">'.esc_html($RcTitle).'</h3>';
										}
										if( $style == 'style-2' && !empty($titledivider) ){
											$Timeline .= '<div class="border-bottom '.esc_attr($RcAlign).'" ><hr/></div>';
										}
										$Timeline .= '<div class="timeline-content-image">';
											if( $RcType == 'image' && !empty($Rfimage) ){
												$RImageid = $index;
												if( !empty($Content['Rfimage']['id']) ){
													$RImageid = $Content['Rfimage']['id'];
													$AttImg = wp_get_attachment_image($RImageid,$ImgSize, false, ['class' => 'hover__img']);
													$Timeline .= $AttImg;
												}
											}else if( $RcType == 'iframe' && !empty($Content['RcHTML']) ){
												$Timeline .= $Content['RcHTML'];	
											}else if( $RcType == 'template' && !empty($Content['Rtemplet']) ){
												ob_start();
													if(!empty($Content['Rtemplet'])) {
														echo Tpgb_Library()->plus_do_block($Content['Rtemplet']);
													}
												$Timeline .= ob_get_contents();
												ob_end_clean();
											}
										$Timeline .= '</div>';
										if( !empty($Content['Rdes']) ){
											$Timeline .= '<div class="timeline-item-description timeline-transition">'.wp_kses_post($Content['Rdes']).'</div>';
										}
										if( !empty($Content['RButton']) && !empty($BTNName) ){
											$Timeline .= '<div class="button-style-8 btn'.esc_attr($block_id).'" >';
												$Timeline .= '<a href="'.esc_url($CustomURL).'"  class="button-link-wrap  tpgb-trans-linear" role="button" '.esc_attr($Target).' '.esc_attr($Nofollow).' '.$link_attr.'>'.esc_html($BTNName).'</a>';
											$Timeline .= '</div>';
										}
								$Timeline .= '</div>';
							$Timeline .= '</div>';

							$Timeline .= '<div class="point-icon '.esc_attr($PinStyle).'">';
								$Timeline .= '<div class="timeline-tooltip-wrap">';
									$Timeline .= '<div class="timeline-point-icon">';
										if( $Rnone == 'icon' && !empty($Content['Ricon']) ){
											$Timeline .= '<i class="'.esc_attr($Content['Ricon']).' point-icon-inner"></i>';
										}elseif( $Rnone == 'image' && !empty($Rimg) ){
											$IconImgId = $index;												
											if( !empty($Content['Rimg']) && !empty($Content['Rimg']['id']) ){
												$IconImgId = $Content['Rimg']['id'];
												$AttImg = wp_get_attachment_image($IconImgId,$ImageSize, false, ['class' => 'point-icon-inner']);
												$Timeline .= $AttImg;
											}
										}
									$Timeline .= '</div>';
								$Timeline .= '</div>';
								if( !empty($PinTitle) ){
									$Timeline .= '<div class="timeline-text-tooltip position-'.esc_attr($Content['RPosition']).' timeline-transition">';
										$Timeline .= esc_html($PinTitle);
										$Timeline .= '<div class="tpgb-tooltip-arrow timeline-transition"></div>';
									$Timeline .= '</div>';
								}
							$Timeline .= '</div>';

						$Timeline .= '</div>';
					$Timeline .= '</div>';
				}
			}
				


			$EndIcon = '';
			if( $EndPin == 'icon' ){
				$EndIcon = '<i class="'.esc_attr($attributes['EndIcon']).' EndImg" ></i>';
			}else if( $EndPin == 'image' ){
				if( !empty($attributes['EndImage']) && !empty($attributes['EndImage']['id']) ){
					$EtartImgSize = !empty($attributes['EndImgSize']) ? $attributes['EndImgSize'] : 'full';
					$AttImg = wp_get_attachment_image( $attributes['EndImage']['id'],$EtartImgSize, false, ['class' => 'EndImg'] );
					$EndIcon = $AttImg;
				}
			}
			if( $EndPin != 'none' ){
				$Timeline .= '<div class="timeline--icon">';
					if(!empty($EndIcon) ){
						$Timeline .= '<div class="timeline-end-icon">'.wp_kses_post($EndIcon).'</div>';
					}
					if( $EndPin == 'text' && !empty($EndText) ){
						$Timeline .= '<div class="tpgb-timeline-text tpgb-text-end">';
							$Timeline .= '<div class="end-text">'.esc_html($EndText).'</div>';
						$Timeline .= '</div>';
					}
				$Timeline .= '</div>';
			}

		$Timeline .= '</div>';
	$Timeline .= '</div>';
	
	$Timeline = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $Timeline);
	
    return $Timeline;
}

function tpgb_tp_timeline() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'style' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'MLayout' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'Alignment' => [
				'type' => 'string',
				'default' => 'center',
				'scopy' => true,
			],
			'timeediType' => [
				'type' => 'string',
				'default' => '',
			],
			'RContent' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'Rnone' => [
							'type' => 'string',
							'default' => 'icon',	
						],
						'Ricon' => [
							'type'=> 'string',
							'default'=> 'fa fa-download',
						],
						'Rimg' => [
							'type' => 'object',
							'default' => [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
								'Id' => '',
							],
						],
						'RimgSize' => [
							'type' => 'string',
							'default' => 'full',	
						],
						'RTitle' => [
							'type'=> 'string',
							'default'=> '09-11-2045',
						],
						'RPosition' => [
							'type' => 'string',
							'default' => 'right',	
						],
						'RcTitle' => [
							'type'=> 'string',
							'default'=> 'New Event',
						],
						'Rdes' => [
							'type'=> 'string',
							'default'=> 'Add your event description with style using all our options. You will love to use and add details over here.',
						],

						'RcType' => [
							'type' => 'string',
							'default' => 'image',
						],
						'Rfimage' => [
							'type' => 'object',
							'default' => [
								'url' => '',
								'Id' => '',
							],
						],
						'RfImgSize' => [
							'type' => 'string',
							'default' => 'full',	
						],
						'RUrl' => [
							'type'=> 'object',
							'default'=> [
								'url' => '',
								'target' => '',
								'nofollow' => ''
							],
						],
						'RButton' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'RBtnText' => [
							'type'=> 'string',
							'default'=> '',
						],
						'RcHTML' => [
							'type'=> 'string',
							'default'=> '',
						],
						'Rtemplet' => [
							'type' => 'string',
							'default' => 'right',	
						],
						'backVisi' => [
							'type' => 'boolean',
							'default' => false,	
						],
						'RcAlign' => [
							'type' => 'string',
							'default' => 'text-right',	
						],
						'RSAlign' => [
							'type' => 'string',
							'default' => 'left',
						],
						'RTopspace' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => 'px',
							],						
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}}{margin-top:{{RTopspace}};}'
								],
							],
						],
					],
				],
				'default' => [ 
					['_key'=> '1','Rnone'=>'icon','Ricon'=>'fa fa-calendar','RcTitle'=>'Event 1','RTitle'=>'24-05-1995','RPosition'=>'right','RcType'=>'image','RUrl'=>['url'=>''],'RcAlign'=>'text-right','RSAlign'=>'left','Rdes'=>'Add your event description with style using all our options. You will love to use and add details over here.' ],
					['_key'=> '2','Rnone'=>'icon','Ricon'=>'fa fa-calendar','RcTitle'=>'Event 2','RTitle'=>'24-05-1995','RPosition'=>'left','RcType'=>'image','RUrl'=>['url'=>''],'RcAlign'=>'text-left','RSAlign'=>'right','Rdes'=>'Add your event description with style using all our options. You will love to use and add details over here.' ],
				],
			],
			
			'PinStyle' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'StartPin' => [
				'type' => 'string',
				'default' => 'icon',	
			],
			'StartIcon' => [
				'type'=> 'string',
				'default'=> 'fa fa-angle-double-down',
			],
			'StartImage' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'Id' => '',
				],
			],
			'StartImgSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'StartText' => [
				'type'=> 'string',
				'default'=> 'START',
			],
			

			'EndPin' => [
				'type' => 'string',
				'default' => 'icon',	
			],
			'EndIcon' => [
				'type'=> 'string',
				'default'=> 'fa fa-stop-circle',
			],
			'EndImage' => [
				'type' => 'object',
				'default' => [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					'Id' => '',
				],
			],
			'EndImgSize' => [
				'type' => 'string',
				'default' => 'full',	
			],
			'EndText' => [
				'type'=> 'string',
				'default'=> 'END',
			],

			'TypoLoop' => [
				'type'=> 'object',
				'default'=>  (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .timeline-text-tooltip'
					],
				],
				'scopy' => true,
			],
			'NPinCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-text-tooltip{color:{{NPinCr}};}'],
				],
				'scopy' => true,
			],
			'NPinBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-text-tooltip{background:{{NPinBgCr}};}'],
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-text-tooltip .tpgb-tooltip-arrow{border-color:{{NPinBgCr}};}'],
				],
				'scopy' => true,
			],
			'NBRrs' => [
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
						'selector' => '{{PLUS_WRAP}} .timeline-text-tooltip{border-radius:{{NBRrs}};}'
					],
				],
				'scopy' => true,
			],
			'HPinCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [	
						'selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .timeline-text-tooltip{color:{{HPinCr}};}',
					],
				],
				'scopy' => true,
			],
			'HPinBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .timeline-text-tooltip{background:{{HPinBgCr}};}'
					],
					(object) [
						'selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .tpgb-tooltip-arrow{border-color:{{HPinBgCr}};}'
					],
				],
				'scopy' => true,
			],
			'IconSize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .point-icon-inner{font-size:{{IconSize}};}'
					],
					(object) [
						'selector' => '{{PLUS_WRAP}} img.point-icon-inner{max-width:{{IconSize}};}'
					],
				],
				'scopy' => true,
			],

			'IconCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .point-icon-inner{color:{{IconCr}};}'],
				],
				'scopy' => true,
			],
			'IconBgCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap{background:{{IconBgCr}};}'],
				],
				'scopy' => true,
			],
			'IconBcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap{border-color:{{IconBcr}};}'],
				],
				'scopy' => true,
			],
			'IconBrs' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				 ],
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .timeline-item-wrap .point-icon .timeline-tooltip-wrap{border-radius:{{IconBrs}};}'],
				],
				'scopy' => true,
			],

			'IconHcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .grid-item:hover .point-icon-inner{color:{{IconHcr}};}'],
				],
				'scopy' => true,
			],
			'IconHBgcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap{background:{{IconHBgcr}};}'],
				],
				'scopy' => true,
			],
			'IconHBcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .timeline-item-wrap:hover .point-icon .timeline-tooltip-wrap{border-color:{{IconHBcr}};}'],
				],
				'scopy' => true,
			],

			'TypoTitle' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .timeline-item-heading.timeline-transition'
					],
				],
				'scopy' => true,
			],
			'titledivider' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'NCrTitle' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-item-heading.timeline-transition{color:{{NCrTitle}};}'],
				],
				'scopy' => true,
			],
			'NBCrTitle' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
						'selector' => '{{PLUS_WRAP}}.timeline-style-1 .timeline-tl-before{border-color:{{NBCrTitle}};}'
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2' ]],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap .border-bottom hr{border-top-color:{{NBCrTitle}};}'
					],
				],
				'scopy' => true,
			],
			'HCrTitle' => [
				'type' => 'string',
				'default' => '',
				'style' => [(object) ['selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .timeline-item-heading{color:{{HCrTitle}};}'],
				],
				'scopy' => true,
			],
			'HBCrTitle' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [	
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-1'] ]],					
						'selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .timeline-tl-before{border-color:{{HBCrTitle}};}',									
					],
					(object) [	
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => ['style-2'] ]],
						'selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .border-bottom hr{border-top-color:{{HBCrTitle}};}',
					],
				],
				'scopy' => true,
			],
			'TNborder' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.timeline-style-1 .timeline-tl-before{top:{{TNborder}};}',
					],
				],
				'scopy' => true,
			],

			'TypoCon' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .timeline-item-description'
					],
				],
				'scopy' => true,
			],
			'NCrCon' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-item-wrap .timeline-item-description{color:{{NCrCon}};}'],
				],
				'scopy' => true,
			],
			'HCrcon' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) ['selector' => '{{PLUS_WRAP}} .timeline-item-wrap:hover .timeline-item-description{color:{{HCrcon}};}'],
				],
				'scopy' => true,
			],
			'BtnPad' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .button-style-8 .button-link-wrap{padding:{{BtnPad}};}',
					],
				],
				'scopy' => true,
			],
			'BtnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap'
					]
				],
				'scopy' => true,
			],
			'BtnTop' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8{margin-top:{{BtnTop}};}',
					],
				],
				'scopy' => true,
			],
			'BtnNcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
				   (object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap{color:{{BtnNcr}};}',
					],
				],
				'scopy' => true,
			],
			'BtnNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'BtnBrd' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'BtnBRds' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap{border-radius:{{BtnBRds}};}',
					],
				],
				'scopy' => true,
			],
			'BtnBoxsd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			
			'BtnHcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
				   (object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap:hover{color:{{BtnHcr}};}',
					],
				],
				'scopy' => true,
			],
			'BtnHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'BtnHvrBrd' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'BtnHBSRs' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap:hover{border-radius:{{BtnHBSRs}};}',
					],
				],
				'scopy' => true,
			],
			'BtnHBoxSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .button-style-8 .button-link-wrap:hover',
					],
				],
				'scopy' => true,
			],

			'BGSnBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGSnBr' => [
				'type' => 'object',
				'default' =>  (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGSnBrs' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-content{border-radius:{{BGSnBrs}};}',
					],
				],
				'scopy' => true,
			],
			'BGSnSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGSnAcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
				   (object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-tl-before{border-left-color:{{BGSnAcr}};border-right-color:{{BGSnAcr}};}',
					],
				],
				'scopy' => true,
			],
			'ArrowStyle' => [
				'type' => 'string',
				'default' => 'style-1',	
				'scopy' => true,
			],

			'BGShBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap:hover .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGHvrBr' => [
				'type' => 'object',
				'default' =>  (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap:hover .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGShBrs' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap:hover .timeline-item-content{border-radius:{{BGShBrs}};}',
					],
				],
				'scopy' => true,
			],
			'BGShSd' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap:hover .timeline-item-content',
					],
				],
				'scopy' => true,
			],
			'BGShAcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
				   (object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}}.timeline-style-2 .timeline-item-wrap:hover .timeline-tl-before{border-left-color:{{BGShAcr}};border-right-color:{{BGShAcr}};}',
					],
				],
				'scopy' => true,
			],

			'PinBCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}} .timeline-track{background:{{PinBCr}};}',
					],
				],
				'scopy' => true,
			],
			'SIconsize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}} .tpgb-beginning-icon .startImg{font-size:{{SIconsize}};}',
					],
				],
				'scopy' => true,
			],
			'SImgSize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'image']],
						'selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .tpgb-beginning-icon img.startImg{max-width:{{SImgSize}};}',
					],
				],
				'scopy' => true,
			],
			'PSCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}} .tpgb-beginning-icon .startImg{color:{{PSCr}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start{color:{{PSCr}};}',
					],
				],
				'scopy' => true,
			],
			'TextTypo' => [
				'type'=> 'object',
				'default'=>  (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}}.tpgb-timeline-list .tpgb-timeline-text.tpgb-text-start',
					],
				],
				'scopy' => true,
			],
			'TextPad' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start{padding:{{TextPad}};}',
					],
				],
				'scopy' => true,
			],
			'TextMargin' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start{ margin :{{TextMargin}};}',
					],
				],
				'scopy' => true,
			],
			'TextBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start',
					],
				],
				'scopy' => true,
			],
			'TextBCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start{border-color:{{TextBCr}};}',
					],
				],
				'scopy' => true,
			],
			'TextBRs' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start{border-radius:{{TextBRs}};}',
					],
				],
				'scopy' => true,
			],
			'TextBSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'StartPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-start',
					],
				],
				'scopy' => true,
			],

			'EIconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}} .timeline-end-icon .EndImg{font-size:{{EIconSize}};}',
					],
				],
				'scopy' => true,
			],
			'EImgSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'image']],
						'selector' => '{{PLUS_WRAP}} .timeline-end-icon img.EndImg{max-width:{{EImgSize}};}',
					],
				],
				'scopy' => true,
			],
			'PECr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'icon']],
						'selector' => '{{PLUS_WRAP}} .timeline-end-icon .EndImg{color:{{PECr}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-timeline-text.tpgb-text-end{color:{{PECr}};}',
					],
				],
				'scopy' => true,
			],
			'ETextTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end',
					],
				],
				'scopy' => true,
			],
			'ETextPad' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end{padding:{{ETextPad}};}',
					],
				],
				'scopy' => true,
			],
			'eTextMargin' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						"right" => '',
						"bottom" => '',
						"left" => '',
					],
					"unit" => 'px'
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end{ margin :{{eTextMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'ETextBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end',
					],
				],
				'scopy' => true,
			],
			'ETextCr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end{border-color:{{ETextCr}};}',
					],
				],
				'scopy' => true,
			],
			'ETextBRs' => [
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
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end{border-radius:{{ETextBRs}};}',
					],
				],
				'scopy' => true,
			],
			'ETextBSd' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'EndPin', 'relation' => '==', 'value' => 'text']],
						'selector' => '{{PLUS_WRAP}} .tpgb-text-end',
					],
				],
				'scopy' => true,
			],

			'GapConent' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px'
				],
				'style' => [
					(object) [						
						'selector' => '{{PLUS_WRAP}}.timeline-style-1 .timeline-item-wrap .timeline-item .timeline-tl-before{width:{{GapConent}};}{{PLUS_WRAP}}.timeline-center-align .timeline-item-wrap.timeline-left-content{padding-right:{{GapConent}};}{{PLUS_WRAP}}.timeline-center-align .timeline-right-content{padding-left:{{GapConent}};}{{PLUS_WRAP}}.timeline-left-align .timeline-item-wrap{padding-right:{{GapConent}} !important;}{{PLUS_WRAP}}.timeline-right-align .timeline-item-wrap.timeline-left-content {{PLUS_WRAP}}.timeline-right-align .timeline-item-wrap.timeline-right-content{padding-left:{{GapConent}} !important;}',
					],
				],
				'scopy' => true,
			],

		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-timeline', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_timeline_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_timeline' );