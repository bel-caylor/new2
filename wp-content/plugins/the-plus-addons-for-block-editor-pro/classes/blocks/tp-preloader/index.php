<?php
/**
 * Block : TP Preloader
 * @since 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_preloader_render_callback( $attributes, $content) {
    $block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
    $preLoader = (!empty($attributes['preLoader'])) ? $attributes['preLoader'] : [];
    $pDefineColor1 = (!empty($attributes['pDefineColor1'])) ? $attributes['pDefineColor1'] : '';
    $pDefineColor2 = (!empty($attributes['pDefineColor2'])) ? $attributes['pDefineColor2'] : '';
    $excludeClass = (!empty($attributes['excludeClass'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['excludeClass']) : '';
    $aniLoadFirst = (!empty($attributes['aniLoadFirst'])) ? $attributes['aniLoadFirst'] : false;
    $alfExclude = (!empty($attributes['alfExclude'])) ? $attributes['alfExclude'] : 'alfheader';
    $customClass = (!empty($attributes['customClass'])) ? $attributes['customClass'] : '';
    $customPos = (!empty($attributes['customPos'])) ? $attributes['customPos'] : 'top';
    $excludeZIndex = (!empty($attributes['excludeZIndex'])) ? $attributes['excludeZIndex'] : '';
    $outTransition = (!empty($attributes['outTransition'])) ? $attributes['outTransition'] : false;
    $loadTime = (!empty($attributes['loadTime'])) ? $attributes['loadTime'] : 'loadtimedefault';
    $loadMinTime = (!empty($attributes['loadMinTime'])) ? $attributes['loadMinTime'] : '';
    $loadMaxTime = (!empty($attributes['loadMaxTime'])) ? $attributes['loadMaxTime'] : '';
	
    $pageLoadTrans = (!empty($attributes['pageLoadTrans'])) ? $attributes['pageLoadTrans'] : 'pageloadfadein';
	$pageLoadSlideDir = (!empty($attributes['pageLoadSlideDir'])) ? $attributes['pageLoadSlideDir'] : 'left';
    $pageLoad4InDir = (!empty($attributes['pageLoad4InDir'])) ? $attributes['pageLoad4InDir'] : 'left';
	
	$postLoadTrans = (!empty($attributes['postLoadTrans'])) ? $attributes['postLoadTrans'] : 'pageloadfadein';
	$postLoadSlideDir = (!empty($attributes['postLoadSlideDir'])) ? $attributes['postLoadSlideDir'] : 'left';
    $postLoad4InDir = (!empty($attributes['postLoad4InDir'])) ? $attributes['postLoad4InDir'] : 'left';
	
    $tpgb4color1 = (!empty($attributes['tpgb4color1'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['tpgb4color1']) : '#ff5a6e';
    $tpgb4color2 = (!empty($attributes['tpgb4color2'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['tpgb4color2']) : '#8072fc';
    $tpgb4color3 = (!empty($attributes['tpgb4color3'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['tpgb4color3']) : '#6f14f1';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$dyCss='';
	$slideinclass=$slideoutclass=$slideinclasseclass=$loadtimedt=$loadminmax='';
	$data_attr = [];
	$data_attr = [
		"post_load_exclude_class"	=> $excludeClass,
		"post_load_opt"	=> (!empty($outTransition)) ? 'enablepostload' : 'disablepostload',
		"loadtime"	=> $loadTime,
		"loadmintime"	=> $loadMinTime,
		"loadmaxtime"	=> $loadMaxTime,
	];
	if($pageLoadTrans=='pageloadslidein' && !empty($pageLoadSlideDir)){
		$slideinclass .= 'tpgb-duo-move-'.$pageLoadSlideDir;
	}
	if($postLoadTrans=='postloadslideout' && !empty($postLoadTrans)){
		$slideoutclass .= 'tpgb-out-duo-move-'.$postLoadSlideDir;
	}
	if($pageLoadTrans=='pageloadtriplesw' || $pageLoadTrans=='pageloadsimple' || $pageLoadTrans=='pageloadduomove' || $postLoadTrans=='postloadtriplesw' || $postLoadTrans=='postloadsimple' || $postLoadTrans=='postloadsduomove'){
		$slideinclasseclass .= 'tpgb-preload-transion4';
	}	
	if($pageLoadTrans=='pageloadtriplesw' && !empty($pageLoad4InDir)){
		$slideinclass .= 'tpgb-tripleswoosh tpgb-4-preload-'.$pageLoad4InDir;
	}
	if($postLoadTrans=='postloadtriplesw' && !empty($postLoad4InDir)){
		$slideoutclass .= 'tpgb-tripleswoosh tpgb-4-postload-'.$postLoad4InDir;
	}
	if($pageLoadTrans=='pageloadsimple' && !empty($pageLoad4InDir)){
		$slideinclass .= 'tpgb-simple tpgb-4-preload-'.$pageLoad4InDir;
	}
	if($postLoadTrans=='postloadsimple' && !empty($postLoad4InDir)){
		$slideoutclass .= 'tpgb-simple tpgb-4-postload-'.$postLoad4InDir;
	}
	if($pageLoadTrans=='pageloadduomove' && !empty($pageLoad4InDir)){
		$slideinclass .= 'tpgb-duomove2 tpgb-4-preload-'.$pageLoad4InDir;
	}
	if($postLoadTrans=='postloadduomove' && !empty($postLoad4InDir)){
		$slideoutclass .= 'tpgb-duomove2 tpgb-4-postload-'.$postLoad4InDir;
	}
	
	
	$data_attr = htmlspecialchars(json_encode($data_attr), ENT_QUOTES, 'UTF-8');
	
	if(!empty($aniLoadFirst) && $alfExclude=='alfcustom' && !empty($customClass) && !empty($excludeZIndex) && !empty($customPos)){
		$topbottom = '';
		if($customPos == 'top'){
			$topbottom = 'top:0';
		}else if($customPos == 'bottom'){
			$topbottom = 'bottom:0';
		}
		$dyCss .='body:not(.tpgb-loaded):not(.tpgb-out-loaded) '.esc_attr($customClass).'{z-index : '.esc_attr($excludeZIndex).';width:100%;position:fixed;'.esc_attr($topbottom).'}';				
	}else if(!empty($aniLoadFirst)&& $alfExclude=='alfheader' && !empty($excludeZIndex)){
		$dyCss .='body:not(.tpgb-loaded):not(.tpgb-out-loaded) header{z-index : '.esc_attr($excludeZIndex).' !important;width:100%;position:fixed;}';
	}
	
	$output=$progressColor='';
    $output .='<div class="tpgb-preloader tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($slideinclass).' '.esc_attr($slideoutclass).' '.esc_attr($slideinclasseclass).'" data-plec=\'' .$data_attr. '\' id="tpgb-preloader">';
	
		if(!empty($preLoader)){
			foreach ( $preLoader as $index => $item1 ) :
				$reProg = '';
				if($item1["preLoadType"]=='progress') {
					if($item1['preLoadType']=='progress' && empty($item1['gradientToggle']) && ($item1['progressType']=='layout-2' || $item1['progressType']=='layout-5')){
						$pb1 = ($item1['pBarColor1'] ? $item1['pBarColor1'] : '#6f14f1');
						$pb2 = ($item1['pBarColor2'] ? $item1['pBarColor2'] : '#7013f2d1');
						$reProg = ".tpgb-block-".esc_attr($block_id)." .percentagelayout.tp-repeater-item-".esc_attr($item1['_key']).", .tpgb-block-".esc_attr($block_id)." .tpgb-progress-loader5.layout-5.tp-repeater-item-".esc_attr($item1['_key'])." .tpgb-pre-5 { background: repeating-linear-gradient(45deg, ".esc_attr($pb1).", ".esc_attr($pb1)." 10px, ".esc_attr($pb2)." 10px, ".esc_attr($pb2)." 20px) }";
					}
					$progressColor .= $reProg;
					
					if(($item1['progressType']=='layout-2')){					
						$plcposclass="";	
						if(!empty($item1['lay02pos'])){
							if($item1['lay02pos']=='top'){
								$plcposclass = 'tpgb-perc-top';							
							}else if($item1['lay02pos']=='bottom'){
								$plcposclass = 'tpgb-perc-bottom';
							} 
						}					
						$output .='<span class="percentagelayout '.esc_attr($plcposclass).' tp-repeater-item-'.esc_attr($item1['_key']).'"></span>';
					}else if($item1['progressType']=='layout-5'){
						$output .='<div class="tpgb-progress-loader5 '.esc_attr($item1['progressType']).'  tp-repeater-item-'.esc_attr($item1['_key']).'">
						<div class="tpgb-pre-5 tpgb-pre-5-in1"></div>
						<div class="tpgb-pre-5 tpgb-pre-5-in2"></div>
						<div class="tpgb-pre-5 tpgb-pre-5-in3"></div>
						<div class="tpgb-pre-5 tpgb-pre-5-in4"></div>
						</div>';
					}
				}
			$progressColor .= $reProg;
			
			endforeach;
		}

		$output .= '<div class="tpgb-loader-wrap">';
		if(!empty($preLoader)){
			foreach ( $preLoader as $index => $item ) :
			$reProg = '';
			if($item['preLoadType']=='image' && !empty($item['imagestore']) && !empty($item['imagestore']['url'])) {
				$imagestore = (isset($item['imagestore']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['imagestore']) : (!empty($item['imagestore']['url']) ? $item['imagestore']['url'] : '');
				if(!empty($item['loaderOnImg'])){
					$output .="<div class='tpgb-img-loader'>
									<div class='tpgb-img-loader-wrap'>
										<span style='background-image: url(".esc_url($imagestore).");' data-no-lazy='1' class='tpgb-img-loader-wrap-in skip-lazy'></span>
									</div>
									<img data-no-lazy='1' class='tpgb-preloader-logo-l-img skip-lazy' alt='Loader Image' src='".esc_url($imagestore)."'>
								</div>";
				} else {
					$output .= '<div class="tpgb-preloader-logo-img">';
						$output .= '<img class="tpgb-preloader-image" src="'.esc_url($imagestore).'"/>';
					$output .= '</div>';
				}
			}else if($item['preLoadType']=='icon' && !empty($item['iconStore'])) {
				$output .= '<div class="tpgb-preloader-logo-icon">';
					$output .= '<span class="tpgb-preloader-icon">';
						$output .= '<i class="'.esc_attr($item['iconStore']).'"></i>';
					$output .= '</span>';
				$output .= '</div>';
			}else if($item['preLoadType']=='text' && !empty($item['textCntnt'])) {
				if(!empty($item['loaderOnText'])){
					$output .='<div class="tpgb-text-loader tp-repeater-item-'.esc_attr($item['_key']).'">'.Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['textCntnt']).'<div class="tpgb-text-loader-inner"><span class="tpgb-inner-load-text">'.Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['textCntnt']).'</span></div></div>';
				}else{
					$output .= '<div class="tpgb-preloader-animated-text tpgb-relative-block tp-repeater-item-'.esc_attr($item['_key']).'">';
						$output .= '<span>'.wp_kses_post($item['textCntnt']).'</span>';
					$output .= '</div>';
				}
			}else if($item['preLoadType']=='preDefine') {
				$prD1 = ($item['pDefineColor1'] ? $item['pDefineColor1'] : '#6f14f1');
				$prD2 = ($item['pDefineColor2'] ? $item['pDefineColor2'] : '#ff844a');
				if($item['aniType']=='1'){
					$output .= '<div class="tpgb-ball-grid-pulse tp-repeater-item-'.esc_attr($item['_key']).'"><div></div><div></div><div></div><div></div><div></div> <div></div><div></div><div></div><div></div></div>';
				}else if($item['aniType']=='2'){
					$output .= '<div class="tpgb-ball-triangle-path tp-repeater-item-'.esc_attr($item['_key']).'"><div></div><div></div><div></div></div>';
				}else if($item['aniType']=='3'){
					$output .= '<div class="tpgb-ball-scale-ripple-multiple tpgb-relative-block tp-repeater-item-'.esc_attr($item['_key']).'"><div></div><div></div><div></div></div>';
				}else if($item['aniType']=='4'){
					$output .= '<div class="tpgb-triangle-skew-spin tpgb-relative-block tp-repeater-item-'.esc_attr($item['_key']).'"><div></div></div>';
				}else if($item['aniType']=='5'){
					$output .= '<div class="tpgb-rounded-triangle tp-repeater-item-'.esc_attr($item['_key']).'"></div>';
				}else if($item['aniType']=='6'){
					$reProg = "@keyframes tpgb_preloader_1{0%{ height: 5px; transform: translateY(0px); background: ".esc_attr($prD1).";} 25%{ height: 30px; transform: translateY(15px); background: ".esc_attr($prD2).";} 50%{ height: 5px; transform: translateY(0px); background: ".esc_attr($prD1).";} 100%{ height: 5px; transform: translateY(0px); background: ".esc_attr($prD1).";}}";
					$progressColor .= $reProg;
					$output .= '<div class="tpgb_preloader_audio_wave tp-repeater-item-'.esc_attr($item['_key']).'"><span></span><span></span><span></span><span></span> <span></span></div>';
				}else if($item['aniType']=='7'){
					if(!empty($item['pDefineColor1'])){
						$reProg = "@keyframes tpgb_typing_loader{0%{background-color: ".esc_attr($item['pDefineColor1']).";box-shadow: 12px 0 0 0 ".esc_attr($item['pDefineColor1'])."33,24px 0 0 0 ".esc_attr($item['pDefineColor1'])."33;}25% {background-color: ".esc_attr($item['pDefineColor1'])."66;box-shadow: 12px 0 0 0 ".esc_attr($item['pDefineColor1'])."33,24px 0 0 0 ".esc_attr($item['pDefineColor1'])."33;}75% {background-color: ".esc_attr($item['pDefineColor1'])."66;box-shadow: 12px 0 0 0 ".esc_attr($item['pDefineColor1'])."33,24px 0 0 0 ".esc_attr($item['pDefineColor1']).";}}";
						$progressColor .= $reProg;
					}
					$output .= '<div class="tpgb_typing_loader tp-repeater-item-'.esc_attr($item['_key']).'"></div>';
				}else if($item['aniType']=='8'){
					$output .= '<div class="tpgb-preloader-help tp-repeater-item-'.esc_attr($item['_key']).'"></div>';
				}else if($item['aniType']=='9'){
					$output .= '<div class="tpgb-preloader-cord tp-repeater-item-'.esc_attr($item['_key']).'">
									<div class="tpgb-cord tpgb-leftMove"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord"><div class="tpgb-ball"></div></div>
									<div class="tpgb-cord tpgb-rightMove"><div class="tpgb-ball"></div></div>
									<div class="tpgb-shadows">
										<div class="tpgb-leftShadow"></div>
											<div></div><div></div><div></div><div></div><div></div>
										<div class="tpgb-rightShadow"></div>
									</div>
								</div>';
				}else if($item['aniType']=='10'){
					$output .= '<div class="tpgb-preloader-dot tp-repeater-item-'.esc_attr($item['_key']).'">
									<span class="tpgb-preloader-dots"></span><span class="tpgb-preloader-dots"></span>
									<span class="tpgb-preloader-dots"></span><span class="tpgb-preloader-dots"></span>
									<span class="tpgb-preloader-dots"></span><span class="tpgb-preloader-dots"></span>
									<span class="tpgb-preloader-dots"></span><span class="tpgb-preloader-dots"></span>
									<span class="tpgb-preloader-dots"></span><span class="tpgb-preloader-dots"></span>
								</div>';
				}else if($item['aniType']=='11'){
					$output .= '<div class="tpgb-preloader-11-main tp-repeater-item-'.esc_attr($item['_key']).'">
									<span class="tpgb-preloader-11 tpgb_dot_1"></span>
									<span class="tpgb-preloader-11 tpgb_dot_2"></span>
									<span class="tpgb-preloader-11 tpgb_dot_3"></span>
									<span class="tpgb-preloader-11 tpgb_dot_4"></span>
								</div>';
				}else if($item['aniType']=='12'){
					if(!empty($item['pDefineColor2'])){
						$reProg ="@keyframes tpgb_preloader_4 { 0% {opacity: 0.3; transform:translateY(0px);-webkit-box-shadow:0px 0px 3px rgba(0, 0, 0, 0.1);-moz-box-shadow:0px 0px 3px rgba(0, 0, 0, 0.1);	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.1);} 50% {opacity: 1; transform: translateY(-10px); background: ".esc_attr($item['pDefineColor2']).";	-webkit-box-shadow:0px 20px 3px rgba(0, 0, 0, 0.05); -moz-box-shadow:0px 20px 3px rgba(0, 0, 0, 0.05);box-shadow: 0px 20px 3px rgba(0, 0, 0, 0.05);} 100%  {opacity: 0.3; transform:translateY(0px);	-webkit-box-shadow:0px 0px 3px rgba(0, 0, 0, 0.1);-moz-box-shadow:0px 0px 3px rgba(0, 0, 0, 0.1); box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.1);}}";
						$progressColor .= $reProg;
					}
					$output .= '<div class="tpgb_preloader_the_shake tp-repeater-item-'.esc_attr($item['_key']).'">
									<span></span><span></span><span></span><span></span><span></span>
								</div>';
				}else if($item['aniType']=='13'){
					$reProg ='@keyframes tpgb_preloader_5 { 0% {transform: rotate(0deg);} 50% {transform: rotate(180deg);background: '.esc_attr($prD2).';} 100% {transform: rotate(360deg);}} @keyframes tpgb_preloader_5_after { 0% {border-top:10px solid '.esc_attr($prD2).';border-bottom:10px solid '.esc_attr($prD2).';} 50% {border-top:10px solid '.esc_attr($prD1).';border-bottom:10px solid '.esc_attr($prD1).';} 100% {border-top:10px solid '.esc_attr($prD2).';border-bottom:10px solid '.esc_attr($prD2).';}}';
					$progressColor .= $reProg;
					$output .= '<div class="tpgb_preloader_spinning_disc_block tp-repeater-item-'.esc_attr($item['_key']).'">
									<div class="tpgb_preloader_spinning_disc"></div>
								</div>';
				}
			} else if($item['preLoadType']=='lottie' && !empty($item['lottieUrl']['url'])) {
				$lottieUrl = (isset($item['lottieUrl']['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($item['lottieUrl']) : (!empty($item['lottieUrl']['url']) ? $item['lottieUrl']['url'] : '');
				$ext = pathinfo($lottieUrl, PATHINFO_EXTENSION);
				if($ext!='json'){
					return '<h3 class="tpgb-posts-not-found">'.esc_html__("Opps! Wrong file format. Only JSON files accepted.",'tpgbp').'</h3>';
				}else{
					$lottieWidth = isset($item['lottieWidth']) ? $item['lottieWidth'] : 300;
					$lottieHeight = isset($item['lottieHeight']) ? $item['lottieHeight'] : 300;
					$lottieSpeed = isset($item['lottieSpeed']) ? $item['lottieSpeed'] : 300;
					$lottieLoopValue='';
					if(!empty($item['lottieLoop'])){
						$lottieLoopValue='loop'; 
					}
					$output .='<lottie-player src="'.esc_url($lottieUrl).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).' speed="'.esc_attr($lottieSpeed).'" autoplay></lottie-player>';
				}	
			} else if($item['preLoadType']=='progress') {
				if($item['preLoadType']=='progress' && empty($item['gradientToggle']) && ($item['progressType']=='layout-1' || $item['progressType']=='layout-3' || $item['progressType']=='layout-4')){
					$pb1 = ($item['pBarColor1']) ? $item['pBarColor1'] : '#6f14f1';
					$pb2 = ($item['pBarColor2']) ? $item['pBarColor2'] : '#7013f2d1';
					$reProg = ".tpgb-block-".esc_attr($block_id)." .tp-repeater-item-".esc_attr($item['_key'])." .tpgb-loadbar, .tpgb-block-".esc_attr($block_id)." .tpgb-progress-loader4.layout-4.tp-repeater-item-".esc_attr($item['_key'])." .tpgb-progress-loader4-in { background: repeating-linear-gradient(45deg, ".esc_attr($pb1).", ".esc_attr($pb1)." 10px, ".esc_attr($pb2)." 10px, ".esc_attr($pb2)." 20px) }";
				}
				$progressColor .= $reProg;
				if($item['progressType']=='layout-1'){
					$output .='<div class="tpgb-progress-loader tp-repeater-item-'.esc_attr($item['_key']).'"><div class="tpgb-percentage tpgb-precent"></div><div class="tpgb-progress-load"><div class="p-trackbar"><div class="tpgb-loadbar"></div></div><div class="tpgb-glow"></div></div></div>';
				}else if($item['progressType']=='layout-3'){
					$percpre=$percpost='';
					if(!empty($item['layo3Prefix'])){
						$percpre='<span class="tpgb-perc-prepostfix tpgb-perc-pre">'.Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['layo3Prefix']).'</span>';
					}
					if(!empty($item['layo3Postfix'])){
						$percpost='<span class="tpgb-perc-prepostfix tpgb-perc-post">'.Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($item['layo3Postfix']).'</span>';
					}
					$output .='<div class="tpgb-progress-loader '.esc_attr($item['progressType']).' tp-repeater-item-'.esc_attr($item['_key']).'">'.$percpre.'<div class="tpgb-percentage tpgb-precent3">0%</div>'.$percpost.'</div>';
				}else if($item['progressType']=='layout-4'){
					$output .='<div class="tpgb-progress-loader4 tpgb-relative-block '.esc_attr($item['progressType']).' tp-repeater-item-'.esc_attr($item['_key']).'"><div class="tpgb-progress-loader4-in tpgb-precent4">0%</div></div>';
				}else if($item['progressType']=='layout-6'){
					$pcEmptyColor = (!empty($item['pcEmptyColor'])) ? $item['pcEmptyColor'] : '#6f14f136';
					$pcFillColor = (!empty($item['pcFillColor'])) ? $item['pcFillColor'] : '#6f14f1';
					$pcStrockSize = (!empty($item['pcStrockSize'])) ? $item['pcStrockSize'] : 4;
					
					$output .='<div class="tpgb-progress-loader6 '.esc_attr($item['progressType']).' tp-repeater-item-'.esc_attr($item['_key']).'">
						<svg class="progress-ring" width="120" height="120">
							<circle class="progress-ring__circle progress-ring1 tpgb-precent6" style="stroke-dasharray: 326.726, 326.726;stroke-dashoffset: 326.726;" stroke="'.esc_attr($pcFillColor).'" stroke-width="'.esc_attr($pcStrockSize).'" fill="transparent" r="52" cx="60" cy="60"/>
						</svg>
						<svg class="progress-ring progress-ring2" width="120" height="120">
							<circle class="progress-ring__circle" style="stroke-dasharray: 326.726, 326.726;stroke-dashoffset:0;" stroke="'.esc_attr($pcEmptyColor).'" stroke-width="'.esc_attr($pcStrockSize).'" fill="transparent" r="52" cx="60" cy="60"/>
						</svg>
						<div class="tpgb-percentage tpgb-precent3"></div>
					</div>';
				}
			}else if($item['preLoadType']=='custom' && !empty($item['customCode'])){
				$output .='<div class="tpgb-preloader-custom">'.wp_kses_post($item['customCode']).'</div>';
			}else if($item['preLoadType']=='shortcode' && !empty($item['preShortCode'])){
				$output .='<div class="tpgb-preloader-custom-shortcode">'.do_shortcode( shortcode_unautop( $item['preShortCode'] ) ).'</div>';
			}
			endforeach;
		}
		$output .= '</div>';
		$output .= '<style>'.$dyCss.$progressColor.'</style>';
		if(!empty($pageLoadTrans) && $pageLoadTrans=='pageloadtriplesw'){
			$output .='<div class="tpgb-preload-reveal-layer-box">
				<div style="background:'.esc_attr($tpgb4color1).'" class="tpgb-preload-reveal-layer"></div>
				<div style="background:'.esc_attr($tpgb4color2).'" class="tpgb-preload-reveal-layer"></div>
				<div style="background:'.esc_attr($tpgb4color3).'" class="tpgb-preload-reveal-layer"></div>
				</div>';
		}else if(!empty($pageLoadTrans) && $pageLoadTrans=='pageloadsimple'){
			$output .='<div class="tpgb-preload-reveal-layer-box"><div style="background:'.esc_attr($tpgb4color1).'" class="tpgb-preload-reveal-layer"></div></div>';
		}else if(($pageLoadTrans && $pageLoadTrans=='pageloadduomove')){
			$output .='<div class="tpgb-preload-reveal-layer-box"><div style="background:'.esc_attr($tpgb4color1).'" class="tpgb-preload-reveal-layer"></div>
			<div style="background:'.esc_attr($tpgb4color2).'" class="tpgb-preload-reveal-layer"></div></div>';
		}
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
  
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_preloader() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
  
	$attributesOptions = array(
		'block_id' => [
			'type' => 'string',
			'default' => '',
		],
		'preLoader' => [
			'type'=> 'array',
			'repeaterField' => [
				(object) [
					'preLoadType' => [
						'type' => 'string',
						'default' => 'image'
					],
					'progressType' => [
						'type' => 'string',
						'default' => 'layout-1'
					],
					'layo3Prefix' => [
						'type' => 'string',
						'default' => ''
					],
					'layo3Postfix' => [
						'type' => 'string',
						'default' => ''
					],
					'lay02pos' => [
						'type' => 'string',
						'default' => 'top'
					],
					'imagestore' => [
						'type' => 'object',
						'default' => [
							'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
						],
					],
					'loaderOnImg' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'iconStore' => [
						'type'=> 'string',
						'default' => 'fas fa-spinner'
					],
					'textCntnt' => [
						'type' => 'string',
						'default' => 'Loading! Please Wait...'
					],
					'loaderOnText' => [
						'type' => 'boolean',
						'default' => false,	
					],
					'lottieUrl' => [
						'type'=> 'object',
						'default'=>[
							'url' => '#',	
							'target' => '',	
							'nofollow' => ''	
						]
					],
					'lottieWidth' => [
						'type' => 'string',
						'default' => '300',
					],
					'lottieHeight' => [
						'type' => 'string',
						'default' => '300',
					],
					'lottieSpeed' => [
						'type' => 'string',
						'default' => '1',
					],
					'aniType' => [
						'type' => 'number',
						'default' => 1,
					],
					'lottieLoop' => [
						'type' => 'boolean',
						'default' => false,
					],
					'customCode' => [
						'type' => 'string',
						'default' => ''
					],
					'preShortCode' => [
						'type' => 'string',
						'default' => ''
					],
					
					'pDefineColor1' => [
						'type' => 'string',
						'default' => '#6f14f1',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '1' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-ball-grid-pulse{{TP_REPEAT_ID}}>div{ background-color: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '2' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-ball-triangle-path{{TP_REPEAT_ID}}>div{ border-color: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '3' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-ball-scale-ripple-multiple{{TP_REPEAT_ID}}>div{ border-color: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '4' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-triangle-skew-spin{{TP_REPEAT_ID}}>div{border-bottom-color: {{pDefineColor1}};}',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '5' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-rounded-triangle{{TP_REPEAT_ID}}{ background-color: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '8' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-preloader-help{{TP_REPEAT_ID}}:after{ background: {{pDefineColor1}}; }
								{{PLUS_WRAP}} .tpgb-preloader-help{{TP_REPEAT_ID}}{ border-color: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '9' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-preloader-cord{{TP_REPEAT_ID}} .tpgb-ball{ background: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '10' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-preloader-dots:before{ background: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '11' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-preloader-11{ background: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '12' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb_preloader_the_shake{{TP_REPEAT_ID}} span{ background: {{pDefineColor1}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '13' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb_preloader_spinning_disc{ background: {{pDefineColor1}}; }',
							],
						],
						'scopy' => true,
					],
					'pDefineColor2' => [
						'type' => 'string',
						'default' => '#ff844a',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '10' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-preloader-dot{{TP_REPEAT_ID}} .tpgb-preloader-dots:after{background: {{pDefineColor2}};}',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '11' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb_dot_1 {background: {{pDefineColor2}};}',
							],
							(object) [
								'condition' => [(object) ['key' => 'preLoadType', 'relation' => '==', 'value' => 'preDefine' ], ['key' => 'aniType', 'relation' => '==', 'value' => '13' ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb_preloader_spinning_disc:after{border-top: 10px {{pDefineColor2}}; border-bottom: 10px {{pDefineColor2}};}',
							],
						],
						'scopy' => true,
					],
					
					'gradientToggle' => [
						'type' => 'boolean',
						'default' => false,
					],
					'pBarColor1' => [
						'type' => 'string',
						'default' => '#6f14f1',
						'scopy' => true,
					],
					'pBarColor2' => [
						'type' => 'string',
						'default' => '#7013f2d1',
						'scopy' => true,
					],
					'gradientColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'gradientToggle', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-loadbar, {{PLUS_WRAP}} {{TP_REPEAT_ID}}.percentagelayout, {{PLUS_WRAP}} .tpgb-progress-loader4.layout-4{{TP_REPEAT_ID}} .tpgb-progress-loader4-in, {{PLUS_WRAP}} .tpgb-progress-loader5.layout-5{{TP_REPEAT_ID}} .tpgb-pre-5{ background: {{gradientColor}}; }',
							],
						],
						'scopy' => true,
					],
					'pBarEClr' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-4' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader4.layout-4{{TP_REPEAT_ID}}{ background-color: {{pBarEClr}}; }',
							],
						],
						'scopy' => true,
					],
					'pBar5size' => [
						'type' => 'object',
						'default' => (object) [ 
							'md' => '3',
							"unit" => 'px',
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-5' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader5.layout-5{{TP_REPEAT_ID}} .tpgb-pre-5-in3, {{PLUS_WRAP}} .tpgb-progress-loader5.layout-5 .tpgb-pre-5-in4 { height : {{pBar5size}}; }
								{{PLUS_WRAP}} .tpgb-progress-loader5.layout-5{{TP_REPEAT_ID}} .tpgb-pre-5-in1, {{PLUS_WRAP}} .tpgb-progress-loader5.layout-5 .tpgb-pre-5-in2 { width : {{pBar5size}}; }',
							],
						],
						'scopy' => true,
					],
					'progressPerTypo' => [
						'type'=> 'object',
						'default'=> (object) [
							'openTypography' => 0,
							'size' => [ 'md' => '', 'unit' => 'px' ],
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-1' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader{{TP_REPEAT_ID}} .tpgb-percentage-load',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} div.tpgb-precent3',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-4' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader4.layout-4{{TP_REPEAT_ID}} .tpgb-progress-loader4-in',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-6' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader6.layout-6{{TP_REPEAT_ID}} .tpgb-percentage.tpgb-percentage-load',
							],
						],
						'scopy' => true,
					],
					'progressPerColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-1' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader{{TP_REPEAT_ID}} .tpgb-percentage-load { color: {{progressPerColor}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} div.tpgb-precent3 { color: {{progressPerColor}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-4' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader4.layout-4{{TP_REPEAT_ID}} .tpgb-progress-loader4-in { color: {{progressPerColor}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-6' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader6.layout-6{{TP_REPEAT_ID}} .tpgb-percentage.tpgb-percentage-load { color: {{progressPerColor}}; }',
							],
						],
						'scopy' => true,
					],
					'textTypo' => [
						'type'=> 'object',
						'default'=> (object) [
							'openTypography' => 0,
							'size' => [ 'md' => '', 'unit' => 'px' ],
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'loaderOnText', 'relation' => '==', 'value' => false ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text{{TP_REPEAT_ID}} span',
							],
							(object) [
								'condition' => [(object) ['key' => 'loaderOnText', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-text-loader{{TP_REPEAT_ID}}, {{PLUS_WRAP}} {{TP_REPEAT_ID}} .tpgb-text-loader-inner',
							],
						],
						'scopy' => true,
					],
					'textColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'loaderOnText', 'relation' => '==', 'value' => false ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text{{TP_REPEAT_ID}} span{ color: {{textColor}}; }',
							],
							(object) [
								'condition' => [(object) ['key' => 'loaderOnText', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-text-loader{{TP_REPEAT_ID}}{ color: {{textColor}}; }',
							],
						],
						'scopy' => true,
					],
					'textLColor' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'loaderOnText', 'relation' => '==', 'value' => true ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-text-loader{{TP_REPEAT_ID}} .tpgb-text-loader-inner { color: {{textLColor}}; }',
							],
						],
						'scopy' => true,
					],
					'progressPreTypo' => [
						'type'=> 'object',
						'default'=> (object) [
							'openTypography' => 0,
							'size' => [ 'md' => '', 'unit' => 'px' ],
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ],['key' => 'layo3Prefix', 'relation' => '!=', 'value' => '' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} span.tpgb-perc-prepostfix.tpgb-perc-pre',
							],
						],
						'scopy' => true,
					],
					'progressPreClr' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ],['key' => 'layo3Prefix', 'relation' => '!=', 'value' => '' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} span.tpgb-perc-prepostfix.tpgb-perc-pre { color: {{progressPreClr}}; }',
							],
						],
						'scopy' => true,
					],
					'progressPostTypo' => [
						'type'=> 'object',
						'default'=> (object) [
							'openTypography' => 0,
							'size' => [ 'md' => '', 'unit' => 'px' ],
						],
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ],['key' => 'layo3Postfix', 'relation' => '!=', 'value' => '' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} span.tpgb-perc-prepostfix.tpgb-perc-post',
							],
						],
						'scopy' => true,
					],
					'progressPostClr' => [
						'type' => 'string',
						'default' => '',
						'style' => [
							(object) [
								'condition' => [(object) ['key' => 'progressType', 'relation' => '==', 'value' => 'layout-3' ],['key' => 'layo3Postfix', 'relation' => '!=', 'value' => '' ] ],
								'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader.layout-3{{TP_REPEAT_ID}} span.tpgb-perc-prepostfix.tpgb-perc-post { color: {{progressPostClr}}; }',
							],
						],
						'scopy' => true,
					],
					'pcEmptyColor' => [
						'type' => 'string',
						'default' => '#6f14f136',
					],
					'pcFillColor' => [
						'type' => 'string',
						'default' => '#6f14f1',
					],
					'pcStrockSize' => [
						'type' => 'string',
						'default' => 4,
					],
				],
			],
			'default' => [
				[
					'_key' => '0',
					'preLoadType' => 'image',
					'progressType' => 'layout-1',
					'layo3Prefix' => '',
					'layo3Postfix' => '',
					'lay02pos' => 'top',
					'imagestore' => [
						'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg',
					],
					'loaderOnImg' => false,
					'iconStore'=> 'fas fa-spinner',
					'textCntnt' => 'Loading! Please Wait...',
					'loaderOnText' => false,
					'textTypo' => (object) [
						'openTypography' => 0,
						'size' => [ 'md' => '', 'unit' => 'px' ],
					],
					'progressPerTypo' => (object) [
						'openTypography' => 0,
						'size' => [ 'md' => '', 'unit' => 'px' ],
					],
					'progressPreTypo' => (object) [
						'openTypography' => 0,
						'size' => [ 'md' => '', 'unit' => 'px' ],
					],
					'progressPostTypo' => (object) [
						'openTypography' => 0,
						'size' => [ 'md' => '', 'unit' => 'px' ],
					],
					'aniType' => 1,
					'lottieUrl' => [
						'url' => '',	
						'target' => '',	
						'nofollow' => ''
					],
					'pDefineColor1' => '#6f14f1',
					'pDefineColor2' => '#ff844a',
					'pBarColor1' => '#6f14f1',
					'pBarColor2' => '#7013f2d1',
					'lottieWidth' => '300',
					'lottieHeight' => '300',
					'lottieSpeed' => '1',
					'lottieLoop' => false,
					'customCode' => '',
					'preShortCode' => '',
				]
			],
		], 
		'backVis' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'aniLoadFirst' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'alfExclude'  => [
			'type' => 'string' ,
			'default' => 'alfheader',	
		],
		'customClass' => [
			'type' => 'string',
			'default' => '',	
		],
		'excludeZIndex' => [
			'type' => 'string',
			'default' => '1234',
		],
		'customPos'  => [
			'type' => 'string' ,
			'default' => 'top',	
		],
		'pageLoadTrans'  => [
			'type' => 'string' ,
			'default' => 'pageloadfadein',	
		],
		'pageLoadSlideDir'  => [
			'type' => 'string' ,
			'default' => 'left',	
		],
		'pageLoad4InDir'  => [
			'type' => 'string' ,
			'default' => 'left',	
		],
		'outTransition' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'postLoadTrans'  => [
			'type' => 'string' ,
			'default' => 'postloadfadeout',	
		],
		'postLoadSlideDir'  => [
			'type' => 'string' ,
			'default' => 'left',	
		],
		'postLoad4InDir'  => [
			'type' => 'string' ,
			'default' => 'left',	
		],
		'excludeClass' => [
			'type' => 'string',
			'default' => '',	
		],
		'loadTime' => [
			'type' => 'string',
			'default' => 'loadtimedefault',
		],
		'loadMinTime' => [
			'type' => 'string',
			'default' => '',
		],
		'loadMaxTime' => [
			'type' => 'string',
			'default' => '',
		],
		'imgWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-img .tpgb-preloader-image,{{PLUS_WRAP}} .tpgb-img-loader .tpgb-preloader-logo-l-img{ max-width : {{imgWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'imgMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-img .tpgb-preloader-image,{{PLUS_WRAP}} .tpgb-img-loader{margin: {{imgMargin}};}',
				],
			],
			'scopy' => true,
		],
		'imgBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-img .tpgb-preloader-image',
				],
			],
			'scopy' => true,
		],
		'imgBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-img .tpgb-preloader-image{border-radius: {{imgBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'imgBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-img .tpgb-preloader-image',
				],
			],
			'scopy' => true,
		],
		'imgNOpacity' => [
			'type' => 'string',
			'default' => '0.3',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-l-img { opacity : {{imgNOpacity}}; }',
				],
			],
			'scopy' => true,
		],
		'imgFOpacity' => [
			'type' => 'string',
			'default' => '1',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-img-loader-wrap .tpgb-img-loader-wrap-in { opacity : {{imgFOpacity}}; }',
				],
			],
			'scopy' => true,
		],
		'imgNFilter' => [
			'type' => 'object',
			'default' =>  [
				'openFilter' => false,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-l-img',
				],
			],
			'scopy' => true,
		],
		'imgFFilter' => [
			'type' => 'object',
			'default' =>  [
				'openFilter' => false,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-img-loader-wrap .tpgb-img-loader-wrap-in',
				],
			],
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon{ font-size : {{iconSize}}; }',
				],
			],
			'scopy' => true,
		],
		'iconPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon{padding: {{iconPadding}};}',
				],
			],
			'scopy' => true,
		],
		'iconMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon{margin: {{iconMargin}};}',
				],
			],
			'scopy' => true,
		],
		'iconColor' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon{ color: {{iconColor}}; }',
				],
			],
			'scopy' => true,
		],
		'iconBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon',
				],
			],
			'scopy' => true,
		],
		'iconBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon',
				],
			],
			'scopy' => true,
		],
		'iconBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon{border-radius: {{iconBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'iconBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-logo-icon .tpgb-preloader-icon',
				],
			],
			'scopy' => true,
		],
		
		'textPadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader, {{PLUS_WRAP}} .tpgb-text-loader-inner{padding: {{textPadding}};}',
				],
			],
			'scopy' => true,
		],
		'textMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader {margin: {{textMargin}};}',
				],
			],
			'scopy' => true,
		],
		'textBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader, {{PLUS_WRAP}} .tpgb-text-loader-inner',
				],
			],
			'scopy' => true,
		],
		'textBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader',
				],
			],
			'scopy' => true,
		],
		'textBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader, {{PLUS_WRAP}} .tpgb-text-loader-inner{border-radius: {{textBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'textBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-preloader-animated-text span, {{PLUS_WRAP}} .tpgb-text-loader, {{PLUS_WRAP}} .tpgb-text-loader-inner',
				],
			],
			'scopy' => true,
		],
		
		'pDefinePadding' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap > div{padding: {{pDefinePadding}};}',
				],
			],
			'scopy' => true,
		],
		'pDefineMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap > div{margin: {{pDefineMargin}};}',
				],
			],
			'scopy' => true,
		],
		/*Progress Bar Start*/
		'progressBarWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '300',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader, {{PLUS_WRAP}} .tpgb-progress-loader4, {{PLUS_WRAP}} .tpgb-progress-loader6 { min-width : {{progressBarWidth}}; }',
				],
			],
			'scopy' => true,
		],
		'progressBarHeight' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '30',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-progress-load, {{PLUS_WRAP}} .tpgb-percentage, {{PLUS_WRAP}} .percentagelayout, {{PLUS_WRAP}} .tpgb-progress-loader4.layout-4 { height : {{progressBarHeight}}; }',
				],
			],
			'scopy' => true,
		],
		'progressMargin' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-progress-loader, {{PLUS_WRAP}} .tpgb-progress-loader4, {{PLUS_WRAP}} .tpgb-progress-loader6{margin: {{progressMargin}};}',
				],
			],
			'scopy' => true,
		],
		'progressbdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-percentage.tpgb-percentage-load',
				],
			],
			'scopy' => true,
		],
		'progressbRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loadbar,.percentagelayout, {{PLUS_WRAP}} .tpgb-percentage.tpgb-percentage-load { border-radius: {{progressbRadius}}; }',
				],
			],
			'scopy' => true,
		],
		/*Progress Bar End*/
		'boxWidth' => [
			'type' => 'object',
			'default' => (object) [ 
				'md' => '300',
				"unit" => 'px',
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap { min-width : {{boxWidth}}; width : {{boxWidth}}; }',
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap{padding: {{boxPadding}};}',
				],
			],
			'scopy' => true,
		],
		'boxBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap',
				],
			],
			'scopy' => true,
		],
		'boxBdr' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap',
				],
			],
			'scopy' => true,
		],
		'boxBRadius' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap{border-radius: {{boxBRadius}};}',
				],
			],
			'scopy' => true,
		],
		'boxBShadow' => [
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
					'selector' => '{{PLUS_WRAP}} .tpgb-loader-wrap',
				],
			],
			'scopy' => true,
		],
		
		'wholeBG' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0,
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}#tpgb-preloader',
				],
			],
			'scopy' => true,
		],
		
		//Trans Effect
		'tpgb4color1'  => [
			'type' => 'string' ,
			'default' => '#ff5a6e',	
			'scopy' => true,
		],
		'tpgb4color2'  => [
			'type' => 'string' ,
			'default' => '#8072fc',	
			'scopy' => true,
		],
		'tpgb4color3'  => [
			'type' => 'string' ,
			'default' => '#6f14f1',	
			'scopy' => true,
		],
	);
	$attributesOptions = array_merge($attributesOptions , $globalBgOption , $globalpositioningOption , $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-preloader', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_preloader_render_callback'
    ) );
}
add_action( 'init', 'tpgb_preloader' );