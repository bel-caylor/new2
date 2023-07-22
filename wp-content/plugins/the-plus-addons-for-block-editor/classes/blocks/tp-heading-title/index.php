<?php
/* Block : Heading Title
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_limit_words($string, $word_limit){
	$words = explode(" ",$string);
	return implode(" ",array_splice($words,0,$word_limit));
}
function tpgb_tp_heading_title_render_callback( $attributes, $content) {
	$output = '';
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$style = (!empty($attributes['style'])) ? $attributes['style'] : 'style-1';
	$headingType = (!empty($attributes['headingType'])) ? $attributes['headingType'] : 'default';
	$Title = (!empty($attributes['Title'])) ? $attributes['Title'] : '';
	$titleType = (!empty($attributes['titleType'])) ? $attributes['titleType'] : 'h3';
	$subTitle = (!empty($attributes['subTitle'])) ? $attributes['subTitle'] : '';
	$subTitleType = (!empty($attributes['subTitleType'])) ? $attributes['subTitleType'] : 'h3';
	$extraTitle = (!empty($attributes['extraTitle'])) ? $attributes['extraTitle'] : '';
	$ETPosition = (!empty($attributes['ETPosition'])) ? $attributes['ETPosition'] : 'afterTitle';
	$subTitlePosition = (!empty($attributes['subTitlePosition'])) ? $attributes['subTitlePosition'] : 'onBottonTitle';
	
	$limitTgl = (!empty($attributes['limitTgl'])) ? $attributes['limitTgl'] : false;
	$titleLimit = (!empty($attributes['titleLimit'])) ? $attributes['titleLimit'] : false;
	$titleLimitOn = (!empty($attributes['titleLimitOn'])) ? $attributes['titleLimitOn'] : 'char';
	$titleCount = (!empty($attributes['titleCount'])) ? $attributes['titleCount'] : '3';
	$titleDots = (!empty($attributes['titleDots'])) ? $attributes['titleDots'] : false;
	
	$subTitleLimit = (!empty($attributes['subTitleLimit'])) ? $attributes['subTitleLimit'] : false;
	$subTitleLimitOn = (!empty($attributes['subTitleLimitOn'])) ? $attributes['subTitleLimitOn'] : 'char';
	$subTitleCount = (!empty($attributes['subTitleCount'])) ? $attributes['subTitleCount'] : '3';
	$subTitleDots = (!empty($attributes['subTitleDots'])) ? $attributes['subTitleDots'] : false;

	$splitType = (!empty($attributes['splitType'])) ? $attributes['splitType'] : 'words';
	$aniEffect = (!empty($attributes['aniEffect'])) ? $attributes['aniEffect'] : 'default';
	$aniPosition = (!empty($attributes['aniPosition'])) ? $attributes['aniPosition'] : [];
	$animationScale = (!empty($attributes['animationScale'])) ? $attributes['animationScale'] : [];
	$animationRotate = (!empty($attributes['animationRotate'])) ? $attributes['animationRotate'] : [];
	$extrOpt = (!empty($attributes['extrOpt'])) ? $attributes['extrOpt'] : [];
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );
	
	$getExtraTitle = '';
	if(!empty($extraTitle)){
		$getExtraTitle .='<span class="title-s ">'.wp_kses_post($extraTitle).'</span>';
	}
	
	$getTitle = '';
	if($headingType=='page'){
		$Title = get_the_title();
	}
	$getTitle .='<div class="head-title ">';
		$getTitle .='<'.Tp_Blocks_Helper::validate_html_tag($titleType).' class="heading-title">';
			if($style=='style-1' && $ETPosition=='beforeTitle'){
				$getTitle .= $getExtraTitle;
			}
				if(!empty($limitTgl) && !empty($titleLimit)){
					$Title = (class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($Title) : $Title;
					if($titleLimitOn=='char'){												
						$getTitle .= substr($Title,0,$titleCount);
						if(!empty($titleDots) && strlen($Title) > $titleCount){
							$getTitle .= '...';
						}
					}else if($titleLimitOn=='word'){
						$getTitle .= tpgb_limit_words($Title,$titleCount);
						if(!empty($titleDots) && str_word_count($Title) > $titleCount){
							$getTitle .= '...';
						}
					}
				}else{
					$getTitle .= wp_kses_post($Title);
				}
			if($style=='style-1' && $ETPosition=='afterTitle'){
				$getTitle .= $getExtraTitle;
			}
		$getTitle .='</'.Tp_Blocks_Helper::validate_html_tag($titleType).'>';
	$getTitle .='</div>';
	
	$style_8_sep = '';
	$style_8_sep .='<div class="seprator sep-l">';
		$style_8_sep .='<span class="title-sep sep-l"></span>';
		$style_8_sep .='<div class="sep-dot">.</div>';
		$style_8_sep .='<span class="title-sep sep-r"></span>';
	$style_8_sep .='</div>';
	
	$style_3_sep = '';
	$style_3_sep .='<div class="seprator sep-l">';
		$style_3_sep .='<span class="title-sep sep-l"></span>';
		if(isset($attributes['imgName']) && isset($attributes['imgName']['url']) && $attributes['imgName']['url']!=''){
			$imgSrc ='';
			if(!empty($attributes['imgName']['id'])){
				$imgSrc = wp_get_attachment_image( $attributes['imgName']['id'] , 'full' );
			}else if(!empty($attributes['imgName']['url'])){
				$imgSrc = '<img src="'.esc_url($attributes['imgName']['url']).'"  alt="'.esc_attr__('image seprator','tpgb').'" />';
			}
			$style_3_sep .='<div class="sep-mg">';
				$style_3_sep .= $imgSrc;
			$style_3_sep .='</div>';
		}
		$style_3_sep .='<span class="title-sep sep-r"></span>';
	$style_3_sep .='</div>';
	
	$getSubTitle = '';
	if(!empty($subTitle)){
		$getSubTitle .= '<div class="sub-heading ">';
			$getSubTitle .= '<'.Tp_Blocks_Helper::validate_html_tag($subTitleType).' class="heading-sub-title">';
				if(!empty($limitTgl) && !empty($subTitleLimit)){
					$subTitle = (class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($subTitle) : $subTitle;
					if($subTitleLimitOn=='char'){												
						$getSubTitle .= substr($subTitle,0,$subTitleCount);
						if(!empty($subTitleDots) && strlen($subTitle) > $subTitleCount){
							$getSubTitle .= '...';
						}
					}else if($subTitleLimitOn=='word'){
						$getSubTitle .= tpgb_limit_words($subTitle,$subTitleCount);
						if(!empty($subTitleDots) && str_word_count($subTitle) > $subTitleCount){
							$getSubTitle .= '...';
						}
					}
				}else{
					$getSubTitle .= wp_kses_post($subTitle);
				}
			$getSubTitle .= '</'.Tp_Blocks_Helper::validate_html_tag($subTitleType).'>';
			$getSubTitle .= '</div>';
	}

	$styleCss = $styleMD = $styleSM = $styleXS =  '';
	$Alignment = (!empty($attributes['Alignment'])) ? $attributes['Alignment'] : '';
	if($style=='style-3' || $style=='style-6' || $style=='style-8'){
		if(!empty($Alignment['md'])){
			if($Alignment['md'] == 'left'){
				if($style=='style-6'){
					$styleMD = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:15px; right: auto; }';
				}else{
					$styleMD = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-left: 0; margin-right: auto; }';
				}
			}else if($Alignment['md'] == 'center'){
				if($style=='style-6'){
					$styleMD = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: -30px; left:auto; right: auto; }';
				}else{
					$styleMD = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin: 0 auto; }';
				}
			}else if($Alignment['md'] == 'right'){
				if($style=='style-6'){
					$styleMD = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:auto; right: 15px; }';
				}else{
					$styleMD = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-right: 0; margin-left: auto; }';
				}
			}
		}
		if(!empty($Alignment['sm'])){
			if($Alignment['sm'] == 'left'){
				if($style=='style-6'){
					$styleSM = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:15px; right: auto; }';
				}else{
					$styleSM = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-left: 0; margin-right: auto; }';
				}
			}else if($Alignment['sm'] == 'center'){
				if($style=='style-6'){
					$styleSM = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: -30px; left:auto; right: auto; }';
				}else{
					$styleSM = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin: 0 auto; }';
				}
			}else if($Alignment['sm'] == 'right'){
				if($style=='style-6'){
					$styleSM = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:auto; right: 15px; }';
				}else{
					$styleSM = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-right: 0; margin-left: auto; }';
				}
			}
		}
		if(!empty($Alignment['xs'])){
			if($Alignment['xs'] == 'left'){
				if($style=='style-6'){
					$styleXS = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:15px; right: auto; }';
				}else{
					$styleXS = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-left: 0; margin-right: auto; }';
				}
			}else if($Alignment['xs'] == 'center'){
				if($style=='style-6'){
					$styleXS = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: -30px; left:auto; right: auto; }';
				}else{
					$styleXS = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin: 0 auto; }';
				}
			}else if($Alignment['xs'] == 'right'){
				if($style=='style-6'){
					$styleXS = '.tpgb-block-'.esc_attr($block_id).'.heading-style-6 .head-title:after { margin-left: 0; left:auto; right: 15px; }';
				}else{
					$styleXS = '.tpgb-block-'.esc_attr($block_id).' .seprator { margin-right: 0; margin-left: auto; }';
				}
			}
		}
		$styleCss .= (!empty($styleMD)) ? $styleMD : '';
		$styleCss .= (!empty($styleSM)) ? '@media (max-width:1024px){'.$styleSM.'}' : '';
		$styleCss .= (!empty($styleXS)) ? '@media (max-width:767px){'.$styleXS.'}' : '';
	}
	
    $output .= '<div class="tpgb-heading-title tpgb-relative-block heading_style tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' heading-'.esc_attr($style).'">';
		if($style!='style-9'){
			$output .='<div class="sub-style">';
				if($style=='style-5'){
					$output .='<div class="vertical-divider top"></div>';
				}
				if($subTitlePosition=='onBottonTitle'){
					if(!empty($Title)){
						$output .=$getTitle;
					}
					if($style=='style-3' && !empty($Title)){
						$output .=$style_3_sep;
					}
					if($style=='style-8' && !empty($Title)){
						$output .=$style_8_sep;
					}
				}
				if($subTitlePosition=='onTopTitle'){
					$output .=$getSubTitle;
				}
				
				if($subTitlePosition=='onBottonTitle'){
					$output .=$getSubTitle;
				}
				if($subTitlePosition=='onTopTitle'){
					if(!empty($Title)){
						$output .=$getTitle;
					}
					if($style=='style-3' && !empty($Title)){
						$output .=$style_3_sep;
					}
					if($style=='style-8' && !empty($Title)){
						$output .=$style_8_sep;
					}
				}
				if($style=='style-5'){
					$output .='<div class="vertical-divider bottom"></div>';
				}
			$output .= '</div>';
		}else{
			$splitClass = 'tpgb-split-'.$splitType;
			$nSplitType = ($splitType=='lines') ? 'lines,chars' : $splitType;
			$annimtypedtaattr = ' data-animsplit-type="'.$nSplitType.'"';
			$htaattr =[
				'effect' => $aniEffect,
				'x' => (!empty($aniPosition) && !empty($aniPosition['tpgbReset']) && !empty($aniPosition['aniPositionX'])) ? (int)$aniPosition['aniPositionX'] : 0,
				'y' => (!empty($aniPosition) && !empty($aniPosition['tpgbReset']) && !empty($aniPosition['aniPositionY'])) ? (int)$aniPosition['aniPositionY'] : 0,

				'scaleX' => (!empty($animationScale) && !empty($animationScale['tpgbReset']) && !empty($animationScale['animationScaleX'])) ? (int)$animationScale['animationScaleX'] : 0,
				'scaleY' => (!empty($animationScale) && !empty($animationScale['tpgbReset']) && !empty($animationScale['animationScaleY'])) ? (int)$animationScale['animationScaleY'] : 0,
				'scaleZ' => (!empty($animationScale) && !empty($animationScale['tpgbReset']) && !empty($animationScale['animationScaleZ'])) ? (int)$animationScale['animationScaleZ'] : 0,
				'rotationX' => (!empty($animationRotate) && !empty($animationRotate['tpgbReset']) && !empty($animationRotate['animationRotateX'])) ? (int)$animationRotate['animationRotateX'] : 0,
				'rotationY' => (!empty($animationRotate) && !empty($animationRotate['tpgbReset']) && !empty($animationRotate['animationRotateY'])) ? (int)$animationRotate['animationRotateY'] : 0,
				'rotationZ' => (!empty($animationRotate) && !empty($animationRotate['tpgbReset']) && !empty($animationRotate['animationRotateZ'])) ? (int)$animationRotate['animationRotateZ'] : 0,

				'opacity' => (!empty($extrOpt) && !empty($extrOpt['tpgbReset']) && !empty($extrOpt['animationOpacity'])) ? (float)$extrOpt['animationOpacity'] : 0,
				'speed' => (!empty($extrOpt) && !empty($extrOpt['tpgbReset']) && !empty($extrOpt['animationSpeed'])) ? (float)$extrOpt['animationSpeed'] : 1,
				'delay' => (!empty($extrOpt) && !empty($extrOpt['tpgbReset']) && !empty($extrOpt['animationDelay'])) ? (float)$extrOpt['animationDelay'] : 0.02,
			];
			$htaattrbunch= 'data-aniattrht = '.json_encode($htaattr);
			$output .='<'.Tp_Blocks_Helper::validate_html_tag($titleType).' class="sub-style '.esc_attr($splitClass).'" '.$annimtypedtaattr.' '.$htaattrbunch.'>';
				$Title = (class_exists('Tpgbp_Pro_Blocks_Helper')) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($Title) : $Title;
				$output .= wp_kses_post($Title);
			$output .='</'.Tp_Blocks_Helper::validate_html_tag($titleType).'>';
		}
		if(!empty($styleCss)){
			$output .= '<style>'.$styleCss.'</style>';
		}
	$output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_heading_title() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => array(
                'type' => 'string',
				'default' => '',
			),
			'style' => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'splitType' => [
				'type' => 'string',
				'default' => 'words',
			],
			'Title' => [
				'type' => 'string',
				'default' => 'Main Heading',
			],
			'subTitle' => [
				'type' => 'string',
				'default' => 'It’s Sub Heading',
			],
			'extraTitle' => [
				'type' => 'string',
				'default' => 'I am Extra',
			],
			'ETPosition' => [
				'type' => 'string',
				'default' => 'afterTitle',	
			],
			
			'headingType' => [
				'type' => 'string',
				'default' => 'default',	
			],
			'Alignment' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-heading-title{ text-align: {{Alignment}}; }',
					],
				],
				'scopy' => true,
			],
			'limitTgl' => [
				'type' => 'boolean',
				'default' => false,
			],
			'titleLimit' => [
				'type' => 'boolean',
				'default' => false,
			],
			'titleLimitOn' => [
				'type' => 'string',
				'default' => 'char',	
			],
			'titleCount' => [
				'type' => 'string',
				'default' => '3',	
			],
			'titleDots' => [
				'type' => 'boolean',
				'default' => false,
			],
			'subTitleLimit' => [
				'type' => 'boolean',
				'default' => false,
			],
			'subTitleLimitOn' => [
				'type' => 'string',
				'default' => 'char',	
			],
			'subTitleCount' => [
				'type' => 'string',
				'default' => '3',	
			],
			'subTitleDots' => [
				'type' => 'boolean',
				'default' => false,
			],
			'subTitlePosition' => [
				'type' => 'string',
				'default' => 'onBottonTitle',	
			],
			'aniEffect' => [
				'type' => 'string',
				'default' => 'default',	
			],
			'aniPosition' => [
				'type' => 'object',
				'default' => [
					'aniPositionX' => '',
					'aniPositionY' => '',
				],	
			],
			'animationScale' => [
				'type' => 'object',
				'default' => [
					'animationScaleX' => '',
					'animationScaleY' => '',
					'animationScaleZ' => '',
				],	
			],
			'animationRotate' => [
				'type' => 'object',
				'default' => [
					'animationRotateX' => '',
					'animationRotateY' => '',
					'animationRotateZ' => '',
				],	
			],
			'extrOpt' => [
				'type' => 'object',
				'default' => [
					'animationOpacity' => '',
					'animationSpeed' => '',
					'animationDelay' => '',
				],	
			],
			
			'imgName' => [
				'type' => 'object',
				'default' => [
					'url' => '',
					'Id' => '',
				],
			],
			'sepColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
						(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-heading-title .title-sep{ border-color: {{sepColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-4 .heading-title:after,{{PLUS_WRAP}}.heading-style-4 .heading-title:before{ background: {{sepColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-5 .vertical-divider{ background-color: {{sepColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-8 .title-sep{ border-color: {{sepColor}}; }',
					],
				],
				'scopy' => true,
			],
			'sepWidth' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "%"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-3 .title-sep{ width: {{sepWidth}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'imgName.url', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-3 .seprator{ width: {{sepWidth}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-8 .seprator{ width: {{sepWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'sepHeight' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "px"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-3 .title-sep{ border-width: {{sepHeight}}; }',
					],
				],
				'scopy' => true,
			],
			
			'topSepHeight' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "px"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-4 .heading-title:before{ height: {{topSepHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'bottomSepHeight' => [
				'type' => 'object',
				'default' => ["md" => "","unit" => "px"],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-4 .heading-title:after{ height: {{bottomSepHeight}}; }',
					],
				],
				'scopy' => true,
			],
			'sepDotColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-6 .head-title:after{ color: {{sepDotColor}}; text-shadow:15px 0 {{sepDotColor}},-15px 0 {{sepDotColor}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-8' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-8 .sep-dot{ color: {{sepDotColor}}; }',
					],
				],
				'scopy' => true,
			],
			'septopspa' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-6' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-6 .head-title:after{ top : {{septopspa}}px; }',
					]
				],
				'scopy' => true,
			],
			'titleType' => [
				'type' => 'string',
				'default' => 'h3',
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
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '!=', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title,{{PLUS_WRAP}}.heading_style .heading-title>a',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style > div',
					],
				],
				'scopy' => true,
			],
			'titleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '!=', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title,{{PLUS_WRAP}}.heading_style .heading-title>a{ color: {{titleColor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style > div { color: {{titleColor}}; }',
					],
				],
				'scopy' => true,
			],
			'titleMargin' => [
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title{margin: {{titleMargin}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style { margin: {{titleMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'titlePadd' => [
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title{padding: {{titlePadd}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style { padding: {{titlePadd}}; }',
					],
				],
				'scopy' => true,
			],
			'titleB' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'type' => '',
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style',
					],
				],
				'scopy' => true,
			],
			'titleBRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title{border-radius: {{titleBRadius}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style { border-radius: {{titleBRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'titleBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style',
					],
				],
				'scopy' => true,
			],
			'titleShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-title',
					],
					(object) [
						'condition' => [(object) ['key' => 'Title', 'relation' => '!=', 'value' => '' ], ['key' => 'style', 'relation' => '==', 'value' => 'style-9' ]],
						'selector' => '{{PLUS_WRAP}}.heading-style-9 .sub-style',
					],
				],
				'scopy' => true,
			],
			'subTitleType' => [
				'type' => 'string',
				'default' => 'h3',
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
						'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-sub-title,{{PLUS_WRAP}}.heading_style .heading-sub-title>a',
					],
				],
				'scopy' => true,
			],
			'subTitleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'subTitle', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-sub-title,{{PLUS_WRAP}}.heading_style .heading-sub-title>a{ color: {{subTitleColor}}; }',
					],
				],
				'scopy' => true,
			],
			'subTitleMargin' => [
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
						'selector' => '{{PLUS_WRAP}}.heading_style .heading-sub-title{margin: {{subTitleMargin}};}',
					],
				],
				'scopy' => true,
			],
			'extraTitleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'extraTitle', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .title-s,{{PLUS_WRAP}}.heading_style .title-s>a',
					],
				],
				'scopy' => true,
			],
			'extraTitleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'extraTitle', 'relation' => '!=', 'value' => '' ]],
						'selector' => '{{PLUS_WRAP}}.heading_style .title-s,{{PLUS_WRAP}}.heading_style .title-s>a{ color: {{extraTitleColor}}; }',
					],
				],
				'scopy' => true,
			],
			
		);
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-heading-title', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_heading_title_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_heading_title' );