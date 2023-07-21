<?php
/* Block : Navigation Menu
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_navbuilder_render_callback( $attributes, $content) {
	$output = '';
	$block_id = (!empty($attributes['block_id'])) ? $attributes['block_id'] : uniqid("title");
	$menuName = (!empty($attributes['menuName'])) ? $attributes['menuName'] : '';
	$menuLayout = (!empty($attributes['menuLayout'])) ? $attributes['menuLayout'] : 'horizontal';
	$HvrClick = (!empty($attributes['HvrClick'])) ? $attributes['HvrClick'] : 'hover';
	$menuEffect = (!empty($attributes['menuEffect'])) ? $attributes['menuEffect'] : 'style-1';
	$VtitleBar = (!empty($attributes['VtitleBar'])) ? $attributes['VtitleBar'] : false;
	$titleLink = (!empty($attributes['titleLink'])) ? $attributes['titleLink'] : '';
	$navTitle = (!empty($attributes['navTitle'])) ? $attributes['navTitle'] : 'Navigation Menu';
	$prefixIcon = (!empty($attributes['prefixIcon'])) ? $attributes['prefixIcon'] : '';
	$postfixIcon = (!empty($attributes['postfixIcon'])) ? $attributes['postfixIcon'] : '';
	$vSideevent = (!empty($attributes['vSideevent'])) ? $attributes['vSideevent'] : 'normal';
	$menuAlign = (!empty($attributes['menuAlign'])) ? $attributes['menuAlign'] : 'text-left';
	$respoMenu = (!empty($attributes['respoMenu'])) ? $attributes['respoMenu'] : false;
	$resmenuType = (!empty($attributes['resmenuType'])) ? $attributes['resmenuType'] : 'toggle';
	$momenuType = (!empty($attributes['momenuType'])) ? $attributes['momenuType'] : 'normal-menu';
	$mobMenu = (!empty($attributes['mobMenu'])) ? $attributes['mobMenu'] : '';
	$toggleStyle = (!empty($attributes['toggleStyle'])) ? $attributes['toggleStyle'] : 'style-1';
	$toggleAlign = (!empty($attributes['toggleAlign'])) ? $attributes['toggleAlign'] : 'text-left';
	$ctmtoggletype = (!empty($attributes['ctmtoggletype'])) ? $attributes['ctmtoggletype'] : 'custom_icon';
	$openIcon = (!empty($attributes['openIcon'])) ? $attributes['openIcon'] : '';
	$closeIcon = (!empty($attributes['closeIcon'])) ? $attributes['closeIcon'] : '';
	$openImg = (!empty($attributes['openImg'])) ? $attributes['openImg'] : '';
	$closeImg = (!empty($attributes['closeImg'])) ? $attributes['closeImg'] :'';
	$navAlign = (!empty($attributes['navAlign'])) ? $attributes['navAlign'] : 'text-left';
	$iconStyle = (!empty($attributes['iconStyle'])) ? $attributes['iconStyle'] : 'none';
	$navwidth = (!empty($attributes['navwidth'])) ? $attributes['navwidth'] : 'full';
	$Hvreffect = (!empty($attributes['Hvreffect'])) ? $attributes['Hvreffect'] : 'none';
	$menuInver = (!empty($attributes['menuInver'])) ? $attributes['menuInver'] : false;
	$submenuInver = (!empty($attributes['submenuInver'])) ? $attributes['submenuInver'] : false;
	$subMenuindi = (!empty($attributes['subMenuindi'])) ? $attributes['subMenuindi'] : 'none' ;
	$TypeMenu = (!empty($attributes['TypeMenu'])) ? $attributes['TypeMenu'] : '';
	$respotemplate = (!empty($attributes['respotemplate'])) ? $attributes['respotemplate'] : false;
	$resblockTemp = (!empty($attributes['resblockTemp'])) ? $attributes['resblockTemp'] : '';
	$menulastOpen = (!empty($attributes['menulastOpen'])) ? $attributes['menulastOpen'] : false;
	$accessWeb = (!empty($attributes['accessWeb'])) ? $attributes['accessWeb'] : false;
	$closeMenu = (!empty($attributes['closeMenu'])) ? 'yes' : 'no' ;

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$uid = uniqid('Tpgbmobilemenu2285');
	//set Main Menu Hover class
	$menu_hover_class = '';
	if($Hvreffect == 'style-1'){
		$menu_hover_class = 'menu-hover-style-1';
	}else if($Hvreffect == 'style-2'){
		$menu_hover_class = 'menu-hover-style-2';
	}

	//set Menu Inverse Class
	$menu_hover_inverse = '';
	if(!empty($menuInver)){
		$menu_hover_inverse = 'hover-inverse-effect';
	}
	if(!empty($submenuInver)){
		$menu_hover_inverse .= ' submenu-hover-inverse-effect';
	}
	
	$menuopenClass = '';
	if(!empty($menulastOpen) ){
		$menuopenClass = 'tpgb-open-sub-menu-left';
	}
	
	// Set Swiper class
	$swiper_class='';
	$swiper_wrap='';
	$swiper_slide='';
	if(!empty($resmenuType) && $resmenuType=='swiper'){
		$swiper_class=' swiper-container swiper-free-mode';
		$swiper_wrap='swiper-wrapper';
		$swiper_slide='swiper-slide';
	}

	//Navigation Args
	$nav_menu_args=array(
		'menu'           => $menuName,
		'theme_location'    => 'default_navmenu',
		'depth'             => 8,
		'container'         => '',
		'container_class'   => '',
		'echo'             	=> false, 
		'menu_class'        => 'nav navbar-nav '.$menu_hover_class.' '.$menu_hover_inverse.' '.$menuopenClass.' ',
		'fallback_cb'       => false,
		'walker'            => new tpgb_Navigation_NavWalker('')
	);
	if(!empty($respoMenu) && $momenuType!='custom' && !empty($mobMenu)){
		$mobile_nav_menu_args=array(
			'menu'           => $mobMenu,
			'depth'             => 5,
			'container'         => 'div',
			'echo'             	=> false, 
			'container_class'   => 'tpgb-menu-wrap  '.$navAlign.' '.$swiper_wrap.' ',
			'menu_class'        => 'nav navbar-nav '.$swiper_slide.'',
			'fallback_cb'       => false,
			'walker'            => new tpgb_Navigation_NavWalker('')
		);
	}

	//Get Navigation Title Bar For VerticalSide Menu
	$getnavTitle = '';
	$link_attr = Tpgbp_Pro_Blocks_Helper::add_link_attributes($titleLink);
	$getnavTitle .= '<a class="vertical-side-toggle" href='.(!empty($titleLink['url'])  ? esc_url($titleLink['url']) : '#').' '.$link_attr.'>';
		$getnavTitle .= '<span>';
			$getnavTitle .= '<i aria-hidden="true" class="pre-icon '.esc_attr($prefixIcon).'"></i> ';
			$getnavTitle .= wp_kses_post($navTitle);
		$getnavTitle .= '</span>';
		$getnavTitle .= '<i aria-hidden="true" class="post-icon '.esc_attr($postfixIcon).'"></i> ';
	$getnavTitle .= '</a>';

	//get Toggle icon & Image
	$getToogleicon = '';
	$getToogleicon .= '<div class="close-toggle-icon  toggle-icon">';
		if($ctmtoggletype == 'custom_icon'){
			$getToogleicon .= '<i class="'.esc_attr($openIcon).'"> </i>';
		}else{
			$opimgSrc ='';
			if(!empty($openImg) && !empty($openImg['id'])){
				$opimgSrc = wp_get_attachment_image($openImg['id'] , 'full', false);
			}else if(!empty($openImg['url'])){
				$opimgSrc = '<img src="'.esc_url($openImg['url']).'" />';
			}
			$getToogleicon .= $opimgSrc; 
		} 
	$getToogleicon .= '</div>';
	$getToogleicon .= '<div class="open-toggle-icon  toggle-icon">';
		if($ctmtoggletype == 'custom_icon') {
			$getToogleicon .= '<i class="'.esc_attr($closeIcon).'"> </i>';
		}else{
			$cloimgSrc ='';
			if(!empty($closeImg) && !empty($closeImg['id'])){
				$cloimgSrc = wp_get_attachment_image($closeImg['id'] , 'full', false);
			}else if(!empty($closeImg['url'])){
				$cloimgSrc = '<img src="'.esc_url($closeImg['url']).'" />';
			}
			$getToogleicon .= $cloimgSrc;
		}
	$getToogleicon .= '</div>';
	
	// Set Attr For close Sub Menu on click on Body
	$dataAttr = '';
	if(!empty($closeMenu) && $closeMenu == 'yes' ){
		$dataAttr = 'data-mobile-menu-click="'.esc_attr($closeMenu).'"';
	}

	//Get Navmanu output
    $output .= '<div class="tpgb-navbuilder tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).''.(!empty($accessWeb) ? ' tpgb-web-access' : '').'" data-id="Nav1231" >';
		$output .= '<div class="tpgb-nav-wrap '.esc_attr($menuAlign).'" >';
			$output .= '<div class="tpgb-nav-inner menu-'.esc_attr($HvrClick).' menu-'.esc_attr($menuEffect).'  indicator-'.esc_attr( $iconStyle ).' sub-menu-indiacator-'.esc_attr($subMenuindi).' " data-menu_transition="'.esc_attr($menuEffect).'" '.$dataAttr.' >';
				$output .= '<div class="tpgb-normal-menu">';
					$output .= '<div class="tpgb-nav-item '.esc_attr($menuLayout).' toggle-'.esc_attr($vSideevent).' ">';
						if($menuLayout == 'vertical-side' && !empty($VtitleBar)){
							$output .= $getnavTitle;
						}
						if($TypeMenu == 'standard'){	
							$output .= wp_nav_menu( apply_filters( 'tpgb_nav_menu_args', $nav_menu_args , '' ) );
						}else{
							$output .= tpgb_mega_menu($attributes);
							
						}
					$output .= "</div>";
				$output .= "</div>";
				if(!empty($respoMenu) ){
					if($resmenuType != 'swiper') {
						$output .= '<div class="tpgb-mobile-nav-toggle navbar-header  mobile-toggle '.esc_attr($toggleAlign).'  ">';
							$output .= '<div class="tpgb-toggle-menu hamburger-'.esc_attr($resmenuType).' toggle-'.esc_attr($toggleStyle).'" data-target="#'.esc_attr($uid).'">';
								$output .= '<div class="toggle-line">';
									if($toggleStyle != 'style-5') {
										if($toggleStyle == 'style-1'){
											$output .= '<span></span>';
											$output .= '<span></span>';
										}else{
											$output .= '<span></span>';
											$output .= '<span></span>';
											$output .= '<span></span>';
										}
									}else{
										$output .= $getToogleicon;
									}
								$output .= '</div>';
							$output .= '</div>';
						$output .= "</div>";
					}
					$output .= '<div class="tpgb-mobile-menu tpgb-menu-'.esc_attr($resmenuType).' collapse navbar-collapse navigation-'.esc_attr($navwidth).' '.esc_attr($swiper_class).' '.esc_attr($navAlign).' " id="'.esc_attr($uid).'">';
						if($resmenuType == 'off-canvas'){
							$output .= '<a href="javascript:void(0);" class="close-menu"><i class="fas fa-times"></i></a>';
						}
						if($momenuType!='custom' && !empty($mobMenu) && empty($respotemplate) ){
							$output .= wp_nav_menu( apply_filters( 'tpgb_nav_moblie_menu_args', $mobile_nav_menu_args , ''  ) );
						}else if($momenuType=='custom' && empty($respotemplate)){
							if($resmenuType == 'swiper'){
								$output .= '<div class="swiper-wrapper">';
							}
								$output .= tpgb_mega_menu($attributes,1);
							if($resmenuType == 'swiper'){
								$output .= "</div>";
							}
						}
						else if($resmenuType == 'off-canvas' && !empty($respotemplate) && $resblockTemp!='none' ){
							$output .= '<div class="template-wrap">';
								ob_start();
									if(!empty($resblockTemp) ) {
										echo Tpgb_Library()->plus_do_block($resblockTemp);
									}
									$output .= ob_get_contents();
								ob_end_clean();
							$output .= "</div>";
						}
					$output .= "</div>";
				}
			$output .= "</div>";
		$output .= "</div>";
    $output .= "</div>";
	
	$css_rule = '';
	if( !empty($menulastOpen) ){
		$menuNo = (!empty($attributes['menuNo'])) ? $attributes['menuNo'] : '';
		if(is_rtl()){
			$css_rule .='[dir="rtl"] .tpgb-block-'.esc_attr($block_id).' .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu ul.dropdown-menu{right: auto;left: 100% !important;}';
			$css_rule .='[dir="rtl"] .tpgb-block-'.esc_attr($block_id).' .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li { text-align: left; }';
		}else{
			$css_rule .='.tpgb-block-'.esc_attr($block_id).' .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu ul.dropdown-menu{left: auto !important;right: 100%;}.tpgb-block-'.esc_attr($block_id).' .tpgb-nav-item:not(.menu-vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu {left: 0;}.tpgb-block-'.esc_attr($block_id).' .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li { text-align: right; } .tpgb-block-'.esc_attr($block_id).' .sub-menu-indiacator-style-1 .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li .indi-icon .fa{ transform: rotate(180deg);}.tpgb-block-'.esc_attr($block_id).' .sub-menu-indiacator-style-1 .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li .indi-icon{left : 0; right : 100%;}.tpgb-block-'.esc_attr($block_id).' .sub-menu-indiacator-style-2 .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li .indi-icon:before{left: 10px;right:100%;}.tpgb-block-'.esc_attr($block_id).' .sub-menu-indiacator-style-2 .tpgb-nav-item:not(.vertical) .navbar-nav.tpgb-open-sub-menu-left > li:nth-last-child(-n+'.esc_attr($menuNo).') > ul.dropdown-menu > li .indi-icon:after{left: 4px;right:100%;}';
			
		}
		$output .= '<style>'.$css_rule.'</style>';
	}
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	
    return $output;
}

function tpgb_mega_menu($attributes,$att=''){
	$CustomMenu = '';
	$stylecss = '';
	if(!empty($attributes['ItemMenu'])){
		$CustomMenu .= '<ul class="nav navbar-nav '.($attributes['resmenuType']=='swiper' ? 'swiper-slide' : '' ).' '.($attributes['Hvreffect']=='style-1' ? 'menu-hover-style-1' : ($attributes['Hvreffect']=='style-2' ? 'menu-hover-style-2' : '' )  ).' '.(!empty($attributes['menuInver']) ? 'hover-inverse-effect' : '' ).' '.(!empty($attributes['submenuInver']) ? 'submenu-hover-inverse-effect' : '' ).' '.(!empty($attributes['menulastOpen']) ? ' tpgb-open-sub-menu-left' : '' ).' ">';
		$menuArray = $attributes['ItemMenu'];
		$level = 0;
		foreach($attributes['ItemMenu'] as $index => $item){
			
			$depth = $item['depth'];
			$Nextdepth = (!empty($menuArray[intval($index+1)])) ? intval($menuArray[$index+1]['depth']) : '';
			$Prevdepth = (!empty($menuArray[intval($index-1)])) ? intval($menuArray[$index-1]['depth']) : '';
			
			//echo $Prevdepth.'-'.$depth.'-'.$Nextdepth.'</br>';
			$st_child_Li = '';
			if( $depth > 0 ){
				if(($Nextdepth==$depth || $Nextdepth>$depth || $Nextdepth<$depth ) && $Prevdepth!=$depth && $Prevdepth<$depth){
					$level = $level + 1;
					$st_child_Li = '<ul role="menu" class="dropdown-menu">';
				}
				
			}
			
			$st_end_child_Li = $end_child_Li = '';

			if($Nextdepth < $depth) {
				$diff = ((int)$depth - (int)$Nextdepth);
				if($diff >= 1){
					for( $i=0;$i<$diff;$i++ ){
						$end_child_Li .= '</ul></li>';
					}
				}else if($diff===0){
					$end_child_Li .= '</li>';
				}
			}
			
			$name = ''; 
			$itemUrl = '';
			$menuName= '';
			$indiIcon = '';
			$subindiIcon = '';
			//Get Prefix Icon
			$preicon='';
			if(!empty($item['menuiconTy']) && $item['menuiconTy'] == 'icon' ){
				$preicon .= '<span class="tpgb-navicon-wrap"><i class="'.esc_attr($item['preicon']).' nav-menu-icon"></i></span>';
			}else if(!empty($item['menuiconTy']) && $item['menuiconTy'] == 'img'){
				if(!empty($item['menuImg']) && !empty($item['menuImg']['id'])){
					$preicon .= '<span class="tpgb-navicon-wrap">'. wp_get_attachment_image($item['menuImg']['id'] , 'full', true, ['class' => 'nav-menu-img']).'</span>';
				}else if(!empty($item['menuImg']['url'])){
					$preicon .= '<span class="tpgb-navicon-wrap"><img src="'.esc_url($item['menuImg']['url']).'" class="nav-menu-img" alt="'.esc_attr__('icom_img','tpgbp').'" /></span>';
				}
			}

			//Get Label
			$txtLabel = '';
			if(!empty($item['showlabel']) && !empty($item['labeltxt'])){
				$txtLabel.= '<span class="nav-label-text">'.wp_kses_post($item['labeltxt']).'</span>';
			}
			
			//Get Descroption
			$navdesc = '';
			if(!empty($item['navDesc'])){
				$navdesc.= '<span class="tpgb-nav-desc">'.wp_kses_post($item['navDesc']).'</span>';
			}
			
			$LinkFilter = (array) $item['LinkFilter'];
			
			$menuName = ( !empty($LinkFilter) && !empty($LinkFilter['filter']) && !empty($LinkFilter['filter']['label']) ) ?  $LinkFilter['filter']['label'] : ''; 
			
			// Get Page Url from id
			$current_active ='';
			if(!empty($LinkFilter['filter']['url'])){
				$itemUrl = $LinkFilter['filter']['url'];
				if($LinkFilter['filter']['id'] === get_the_ID()){
					$current_active = ' active';
				}
			}else{
				$itemUrl = '#';
			}
			
			$linkAttr = '';
			if(!empty($LinkFilter['filter']) && isset($LinkFilter['filter']['opensInNewTab']) && !empty($LinkFilter['filter']['opensInNewTab'])){
				$linkAttr .= ' target="_blank"';
			}
			
			if($attributes['iconStyle'] == 'style-1' && $depth ==0 && $Nextdepth!='' && $Nextdepth > 0 && $Nextdepth != $depth){
				$indiIcon .= '<span class="indi-icon"><i class="'.($attributes['menuLayout'] == 'vertical-side' ? 'fa fa-angle-right' : 'fa fa-angle-down' ).'"></i></span>';
			}

			if($depth >=1  && $Nextdepth > 1 && $Nextdepth != $depth && $Nextdepth > $depth){
				$subindiIcon .= '<span class="indi-icon">';
					if($attributes['subMenuindi'] == 'style-1'){
						$subindiIcon .= '<i class="fa fa-angle-right"></i>';
					}
				$subindiIcon .= '</span>';
			}
			if(!empty($item['SmenuType']) && $item['SmenuType'] != 'mega-menu' && $item['SmenuType'] == 'link') {
				$name = '<a href="'.esc_url($itemUrl).'" title="'.esc_attr($menuName).'" data-text="'.esc_attr($menuName).'" '.$linkAttr.'>'.$preicon.'<span class="tpgb-title-wrap">'.esc_html($menuName).$txtLabel.$indiIcon.$subindiIcon.$navdesc.'</span></a>';
			}

			$dropdownClass= ($Nextdepth >=2 && ($Nextdepth > $depth) ) ? 'dropdown-submenu menu-item-has-children' : ( ($Nextdepth > $depth) ? 'dropdown menu-item-has-children' : '');
			
			$MegaMenuClass = '';
			if($Nextdepth ===1 ){
				$NextMenu =(!empty($menuArray[$index+1])) ? $menuArray[$index+1] : '';
				if($NextMenu!='' && $NextMenu['SmenuType']=='mega-menu'){
					$MegaMenuClass .=' tpgb-fw';
					if($NextMenu!='' && $NextMenu['megaMType']!=''){
						$MegaMenuClass .=' tpgb-dropdown-'.esc_attr($NextMenu['megaMType']);
					}
					if($NextMenu!='' && $NextMenu['megaMType'] == 'default'){

						$unit = isset($NextMenu['megaMwid']['unit']) && !empty($NextMenu['megaMwid']['unit'] ) ? $NextMenu['megaMwid']['unit'] : 'px';

						// Desktop
						if( isset($NextMenu['megaMwid']['md']) && !empty($NextMenu['megaMwid']['md']) ){
							$stylecss.= '@media (min-width: 1024px) { .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['md'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['md'].$unit.'!important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}

						// Tablet
						if( isset($NextMenu['megaMwid']['sm']) && !empty($NextMenu['megaMwid']['sm']) ){
							$stylecss.= '@media (max-width: 1024px) and (min-width:768px){ .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['sm'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['sm'].$unit.' !important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}else if( isset($NextMenu['megaMwid']['md']) && !empty($NextMenu['megaMwid']['md']) ){
							$stylecss.= '@media (max-width: 1024px) and (min-width:768px){ .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['md'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['md'].$unit.' !important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}
						
						// Mobile
						if( isset($NextMenu['megaMwid']['xs']) && !empty($NextMenu['megaMwid']['xs']) ){
							$stylecss.= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['xs'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['xs'].$unit.' !important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}else if( isset($NextMenu['megaMwid']['sm']) && !empty($NextMenu['megaMwid']['sm']) ){
							$stylecss.= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['sm'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['sm'].$unit.' !important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}else if( isset($NextMenu['megaMwid']['md']) && !empty($NextMenu['megaMwid']['md']) ){
							$stylecss.= '@media (max-width: 767px) { .tpgb-block-'.esc_attr($attributes['block_id']).' .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.tp-repeater-item-'.$item['_key'].'.tpgb-dropdown-default>ul.dropdown-menu{ max-width: '.$NextMenu['megaMwid']['md'].$unit.' !important; min-width: '.$NextMenu['megaMwid']['md'].$unit.' !important; '.( isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign'] == 'default' ? 'right: auto;' : '').'} } ';
						}
					}

				}
				if($NextMenu!='' && $NextMenu['megaMType'] == 'default' && isset($NextMenu['megaMAlign']) && $NextMenu['megaMAlign']=='center'){
					$MegaMenuClass .=' tpgb-dropdown-'.esc_attr($NextMenu['megaMAlign']);
				}
			}
				
			
			$start_Li = "<li class='menu-item depth-".esc_attr($depth)." ".esc_attr($dropdownClass)." ".esc_attr($MegaMenuClass)." ".(!empty($item['classTxt']) ? esc_attr($item['classTxt']) : '')." tp-repeater-item-".esc_attr($item['_key']). $current_active ."' >";
			
			
			if($depth==1 && $item['SmenuType']=='mega-menu' ){
				if(empty($att) || empty($item['moblieMmenu'])){
					$start_Li .= '<div class="tpgb-megamenu-content">';
						if(!empty($item['blockTemp']) && $item['blockTemp']!='none'){
							ob_start();
								if(!empty($item['blockTemp'])) {
									echo Tpgb_Library()->plus_do_block($item['blockTemp']);
								}
							$start_Li .= ob_get_contents();
							ob_end_clean();
						}
					$start_Li .= '</div>';
				} 
				if(!empty($item['moblieMmenu']) && !empty($att)){
					$MLinkFilter = (array) $item['MLinkFilter'];
					$MmenuName = ( !empty($MLinkFilter) && !empty($MLinkFilter['filter']) && !empty($MLinkFilter['filter']['label']) ) ?  $MLinkFilter['filter']['label'] : ''; 
					$MitemUrl = (!empty($MLinkFilter['filter']) && !empty($MLinkFilter['filter']['url'])) ? $MLinkFilter['filter']['url'] : '#';
					$MitemAttr = '';
					if(!empty($MLinkFilter['filter']) && isset($MLinkFilter['filter']['opensInNewTab']) && !empty($MLinkFilter['filter']['opensInNewTab'])){
						$MitemAttr .= ' target="_blank"';
					}
					$start_Li .= '<a href="'.esc_attr($MitemUrl).'" title="'.esc_attr($MmenuName).'" data-text="'.esc_attr($MmenuName).'" '.$MitemAttr.'>'.$preicon.''.$MmenuName.''.$txtLabel.'</a>';
				}
			}

			$end_Li = '';
			if($Nextdepth===$depth && $depth===0 && $Nextdepth===$Prevdepth ){
				$end_Li = '</li>';
			}
			
			$CustomMenu .= $st_end_child_Li.$st_child_Li.$start_Li.$name.$end_Li.$end_child_Li;
			
		}
		$CustomMenu .= '</ul>';
		if(!empty($stylecss)){
			$CustomMenu .= '<style>'.$stylecss.'</style>';
		}
	}
	return $CustomMenu;
}
/**
 * Render for the server-side
 */
function tpgb_tp_navbuilder() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'TypeMenu' => [
                'type' => 'string',
				'default' => 'standard',
			],
			'ItemMenu' => array(
				'type' => 'array',
				'repeaterField' => [
					(object) [
						'depth' => [
							'type' => 'string',
							'default' => '0',
						],
						'name' => [
							'type' => 'string',
							'default' => '',
						],
						'LinkType' => [
							'type' => 'string',
							'default' => 'dynamic',
						],
						'LinkFilter' => [
							'type' => 'object',
							'default' => [],
						],
						'minWidth' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}} > ul.dropdown-menu {min-width: {{minWidth}}px; } ',
								],
							],
						],
						'showlabel' => [
							'type' => 'boolean',
							'default' => false
						],
						'labeltxt' => [
							'type' => 'string',
							'default' => 'New',
						],
						'labelcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}} a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li{{TP_REPEAT_ID}} a .nav-label-text{color : {{labelcolor}};}',
								],
							],
						],
						'labelBgcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}} a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li{{TP_REPEAT_ID}} a .nav-label-text{background-color : {{labelBgcolor}};}',
								],
							],
						],
						'menuiconTy' => [
							'type' => 'string',
							'default' => '',
						],
						'preicon' => [
							'type' => 'string',
							'default' => 'fas fa-home',
						],
						'menuImg' => [
							'type' => 'object',
							'default'=> [
								'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
							],	
						],
						'iconBg' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
								'bgType' => 'color',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '==', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}}>a span.tpgb-navicon-wrap ',
								],
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '!=', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}>a span.tpgb-navicon-wrap ',
								],
							],
						],
						'iconcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}>a span.tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}>a>span.tpgb-navicon-wrap .nav-menu-icon{ color:{{iconcolor}}; }',
								],
							],
						],
						'iconborcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}>a span.tpgb-navicon-wrap,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}>a>span.tpgb-navicon-wrap{ border-color:{{iconborcolor}}; }',
								],
							],
						],
						'iconHvrBg' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
								'bgType' => 'color',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '==', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}}:hover>a span.tpgb-navicon-wrap ',
								],
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '!=', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}:hover>a span.tpgb-navicon-wrap ',
								],
							],
						],
						'iconhvrborcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}:hover>a span.tpgb-navicon-wrap,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}:hover>a>span.tpgb-navicon-wrap{ border-color: {{iconhvrborcolor}}; }',
								],
							],
						],
						'iconHvrcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}:hover>a span.tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}:hover>a>.tpgb-navicon-wrap .nav-menu-icon{ color: {{iconHvrcolor}}; }',
								],
							],
						],
						'iconActBg' => [
							'type' => 'object',
							'default' => (object) [
								'openBg'=> 0,
								'bgType' => 'color',
							],
							'style' => [
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '==', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li{{TP_REPEAT_ID}}.active>a span.tpgb-navicon-wrap',
								],
								(object) [
									'condition' => [(object) ['key' => 'depth', 'relation' => '!=', 'value' => '0']],
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}.active>a span.tpgb-navicon-wrap ',
								],
							],
						],
						'iconActborcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}.active>a span.tpgb-navicon-wrap,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}.active>a>.tpgb-navicon-wrap{  border-color:{{iconActborcolor}}; }',
								],
							],
						],
						'iconActcolor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li{{TP_REPEAT_ID}}.active>a span.tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{{TP_REPEAT_ID}}.active>a>.tpgb-navicon-wrap .nav-menu-icon{ color:{{iconActcolor}}; }',
								],
							],
						],
						'SmenuType' => [
							'type' => 'string',
							'default' => 'link',
						],
						'megaMType' => [
							'type' => 'string',
							'default' => 'default',
						],
						'megaMAlign' => [
							'type'=> 'string',
							'default'=> 'default',
						],
						'megaMwid' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => 'px',
							],
						],
						'classTxt' => [
							'type' => 'string',
							'default' => '',
						],
					], 
				],
				'default' => [ 
					[ '_key'=> 'cvi9', 'depth' => '0', 'LinkType' => 'dynamic','LinkFilter' => (Object)['openFilter' => true], 'preicon' => 'fas fa-home', 'labeltxt' => 'New', 'megaMType' => 'default' , 'menuiconTy' => '' , 'SmenuType' => 'link' ]
				],
			),
			
			'menuName' => [
				'type' => 'string',
				'default' => '',	
			],
			'menuLayout' => [
				'type' => 'string',
				'default' => 'horizontal',	
			],
			'HvrClick' => [
				'type' => 'string',
				'default' => 'hover',	
			],
			'menuEffect'  => [
				'type' => 'string',
				'default' => 'style-1',	
			],
			'VtitleBar' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'vSideevent' => [
				'type' => 'string',
				'default' => 'normal',	
			],
			'navTitle' =>  [
				'type' => 'string',
				'default' => 'Navigation Menu',	
			],
			'titleLink' => [
				'type'=> 'object',
				'default'=> [
					'url' => '#',	
					'target' => '',	
					'nofollow' => ''
				],
			],
			'prefixIcon' => [
				'type' => 'string',
				'default' => '',	
			],
			'postfixIcon' => [
				'type' => 'string',
				'default' => '',	
			],
			'menuAlign' => [
				'type' => 'string',
				'default' => 'text-left',	
			],
			'stickyMenu' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'respoMenu' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'resmenuType' => [
				'type' => 'string',
				'default' => 'toggle',
			],
			'menuWidth' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'respoMenu', 'relation' => '==', 'value' => true],
							['key' => 'resmenuType', 'relation' => '==', 'value' => 'off-canvas']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-mobile-menu.tpgb-menu-off-canvas,{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-mobile-menu.tpgb-menu-off-canvas .navbar-nav{ max-width: {{menuWidth}}px; }',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'respoMenu', 'relation' => '==', 'value' => true],
							['key' => 'navwidth', 'relation' => '==', 'value' => 'custom']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-mobile-menu.tpgb-menu-toggle,{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-mobile-menu.tpgb-menu-toggle .navbar-nav{ width: {{menuWidth}}px; }',
					],
				],
				'scopy' => true,
			],
			'toggleStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'toggleAlign' => [
				'type' => 'string',
				'default' => 'text-left',
			],
			'navAlign'  => [
				'type' => 'string',
				'default' => 'text-left',
			],
			'menuSWidth' => [
				'type' => 'string',
				'default' => 991,
				'style' => [
					(object) [
						'selector' => '@media (min-width: {{menuSWidth}}px ){ {{PLUS_WRAP}} .tpgb-normal-menu {display: block!important;} {{PLUS_WRAP}} .tpgb-mobile-nav-toggle.navbar-header.mobile-toggle,{{PLUS_WRAP}} .tpgb-mobile-menu {display:none;} } @media (max-width:{{menuSWidth}}px ){ {{PLUS_WRAP}} .tpgb-normal-menu {display:none !important;} {{PLUS_WRAP}} .tpgb-mobile-nav-toggle.navbar-header.mobile-toggle {display: -webkit-flex;display: -moz-flex;display: -ms-flex;display: flex;-webkit-align-items: center;-moz-align-items: center;-ms-align-items: center;align-items: center;-webkit-justify-content: flex-end;-moz-justify-content: flex-end;-ms-justify-content: flex-end;justify-content: flex-end;} {{PLUS_WRAP}} .tpgb-mobile-menu  ul.navbar-nav li ul.dropdown-menu li > a span.tpgb-title-wrap{ flex-direction: row; }  }',
					],
				],
				'scopy' => true,
			],
			'momenuType' => [
				'type' => 'string',
				'default' => 'standard',
			],
			'mobMenu' => [
				'type' => 'string',
				'default' => '',
			],
			'closeMenu' => [
				'type' => 'boolean',
				'default' => true,	
			],
			'templateName' => [
				'type' => 'string',
				'default' => '',
			],
			'ctmtoggletype' => [
				'type' => 'string',
				'default' => 'custom_icon',
			],
			'openIcon' => [
				'type' => 'string',
				'default' => 'fas fa-bars',
			],
			'closeIcon' => [
				'type' => 'string',
				'default' => 'fas fa-times',
			],
			'openImg' => [
				'type' => 'object',
				'default'=> [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],
			],
			'closeImg' => [
				'type' => 'object',
				'default'=> [
					'url' => TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg'
				],	
			],
			'menuTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'outPadding' => [ 
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li{ margin: {{outPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'inPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav > li > a{ padding: {{inPadding}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical .navbar-nav > li > a{ padding: {{inPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'iconStyle' => [
				'type' => 'string',
				'default' => 'none',
			],
			'iconSize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [  
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a>.tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-mobile-menu  .navbar-nav li a .nav-menu-icon{font-size: {{iconSize}};}  {{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a>img.nav-menu-img,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li a img.nav-menu-img{ max-width: {{iconSize}};} ',
					]
				],
				'scopy' => true,
			],
			'menuColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a{ color: {{menuColor}}; }',
					],
				],
				'scopy' => true,
			],
			'indiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.indicator-style-1 .tpgb-nav-item .navbar-nav > li.dropdown > a .indi-icon{ color: {{indiColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li:hover >a{ color: {{hvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrindiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.indicator-style-1 .tpgb-nav-item .navbar-nav > li.dropdown:hover > a .indi-icon{ color: {{hvrindiColor}}; }',
					],
				],
				'scopy' => true,
			],
			'Actcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li.active >a,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li:focus >a{ color: {{Actcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'actindiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.indicator-style-1 .tpgb-nav-item .navbar-nav> li.active >a .indi-icon,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li:focus >a .indi-icon{ color: {{actindiColor}}; }',
					],
				],
				'scopy' => true,
			],
			'menuBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'hvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li:hover>a',
					],
				],
				'scopy' => true,
			],
			'actBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li.active >a',
					],
				],
				'scopy' => true,
			],
			'norBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a{ border-radius: {{norBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'HvrBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li:hover >a{ border-radius : {{HvrBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'actBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li.active>a{ border-radius: {{actBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'normalBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'HvrBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav> li:hover >a',
					],
				],
				'scopy' => true,
			],
			'actBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li.active>a',
					],
				],
				'scopy' => true,
			],
			'normalBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'hvrBshadow' => [
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
						'selector' => '{{PLUS_WRAP}}  .tpgb-nav-item .navbar-nav> li:hover >a',
					],
				],
				'scopy' => true,
			],
			'actBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li.active>a',
					],
				],
				'scopy' => true,
			],
			'submenuTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a',
					],
				],
				'scopy' => true,
			],
			'subMenupading' => [
				'type' => 'object',
				'default' => ['md' => ''],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu {padding : {{subMenupading}}; } {{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu .dropdown-menu{left: calc(100% + {{subMenupading}}); top: -{{subMenupading}} }',
					],
				],
				'scopy' => true,
			],
			'subinPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown:not(.tpgb-fw) .dropdown-menu > li{ padding : {{subinPadding}} }',
					],
				],
				'scopy' => true,
			],
			'subMenuindi' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'SmenuBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'subBradius' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'standard']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu { border-radius: {{subBradius}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown.tpgb-fw .dropdown-menu>li,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu{ border-radius: {{subBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'subBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'subBshadow' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'submenuColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu>li>a{ color: {{submenuColor}}; }',
					],
				],
				'scopy' => true,
			],
			
			'saciconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a > .tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:focus > a > .tpgb-navicon-wrap .nav-menu-icon{ color: {{saciconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'snormalBgtype' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li',
					],
				],
				'scopy' => true,
			],
			'shvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li:hover > a{ color: {{shvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'sHvrBgtype' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li:hover',
					],
				],
				'scopy' => true,
			],
			'sActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown ul.dropdown-menu>li.open>a{ color: {{sActcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'sactBgtype' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu li.open',
					],
				],
				'scopy' => true,
			],
			
			'toggleHeight' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-mobile-nav-toggle{min-height : {{toggleHeight}} }',
					],
				],
				'scopy' => true,
			],
			'toggleMargin' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-navbuilder .tpgb-mobile-nav-toggle.navbar-header.mobile-toggle{ margin : {{toggleMargin}} }',
					],
				],
				'scopy' => true,
			],
			'toggleCsize' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'respoMenu', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-5' ],
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5 .close-toggle-icon i{font-size : {{toggleCsize}}; } {{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5,{{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5 .close-toggle-icon img{width : {{toggleCsize}};} ',
					],
				],
				'scopy' => true,
			],
			'toggleOsize' => [
				'type' => 'string',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'respoMenu', 'relation' => '==', 'value' => true ],
							(object) [ 'key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-5' ],
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5 .open-toggle-icon i{font-size : {{toggleOsize}} } {{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5,{{PLUS_WRAP}} .tpgb-toggle-menu.toggle-style-5 .open-toggle-icon img{width : {{toggleOsize}};}',
					],
				],
				'scopy' => true,
			],
			'navwidth' => [
				'type' => 'string',
				'default' => 'full',
				'scopy' => true,
			],
			'colBorder' => [
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
						'condition' => [(object) [ 'key' => 'navwidth', 'relation' => '==', 'value' => 'custom' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu.navigation-custom ',
					],
				],
				'scopy' => true,
			],
			'colBshadow' => [
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
						'condition' => [(object) [ 'key' => 'navwidth', 'relation' => '==', 'value' => 'custom' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu.navigation-custom ',
					],
				],
				'scopy' => true,
			],
			'togglecolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'toggleStyle', 'relation' => '!=', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu .toggle-line span { background: {{togglecolor}}; }',
					],
					(object) [
						'condition' => [(object) [ 'key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu .close-toggle-icon{ color: {{togglecolor}}; }',
					],
				],
				'scopy' => true,
			],
			'acttogglecolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'toggleStyle', 'relation' => '!=', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu.open-menu .toggle-line span { background: {{acttogglecolor}}; }'
					],
					(object) [
						'condition' => [(object) [ 'key' => 'toggleStyle', 'relation' => '==', 'value' => 'style-5' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-toggle-menu .open-toggle-icon{ color: {{acttogglecolor}}; }',
					],
				],
				'scopy' => true,
			],
			'respomenuTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'RepoinPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a {padding : {{RepoinPadding}}; } ',
					],
					(object) [
                        'condition' => [(object) ['key' => 'resmenuType', 'relation' => '==', 'value' => 'off-canvas']],
                        'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .template-wrap{ padding: {{RepoinPadding}}; }',
                    ],
				],
				'scopy' => true,
			],
			'RepoMargin' => [
				'type' => 'object',
				'default' => (object) [ 
					"unit" => 'px',
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a { margin : {{RepoMargin}}; } ',
					],
					(object) [
                        'condition' => [(object) ['key' => 'resmenuType', 'relation' => '==', 'value' => 'off-canvas']],
                        'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .template-wrap{ margin: {{RepoMargin}}; }',
                    ],
				],
				'scopy' => true,
			],
			'resmenuColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a { color: {{resmenuColor}}; }',
					],
				],
				'scopy' => true,
			],
			'moiconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a .nav-menu-icon { color: {{moiconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'moindiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.indicator-style-1 .tpgb-mobile-menu .navbar-nav > li.dropdown > a .indi-icon { color: {{moindiColor}}; }',
					],
				],
				'scopy' => true,
			],
			'resBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a',
					],
				],
				'scopy' => true,
			],
			'ActresColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li.active>a,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li:hover>a { color: {{ActresColor}}; }',
					],
				],
				'scopy' => true,
			],
			'moiconhvColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li.dropdown.open>a .nav-menu-icon { color: {{moiconhvColor}}; }',
					],
				],
				'scopy' => true,
			],
			'moindihvColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.indicator-style-1 .tpgb-mobile-menu .navbar-nav > li.dropdown.open > a .indi-icon { color: {{moindihvColor}}; }',
					],
				],
				'scopy' => true,
			],
			'actresBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li.active>a,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li:hover>a',
					],
				],
				'scopy' => true,
			],
			'resposubTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a',
					],
				],
				'scopy' => true,
			],
			'ReposubPadding' => [
				'type' => 'object',
				'default' => (object) [ 
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a {padding : {{ReposubPadding}}; } ',
					],
				],
				'scopy' => true,
			],
			'momenuBor' => [
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
						"unit" => "px",
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li:not(:last-child)>a',
					],
				],
				'scopy' => true,
			],
			'RepoBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					"unit" => 'px',
					'md' => (object)[
						'top' => '',
						'left' => '',
						'bottom' => '',
						'right' => '',
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li:not(:last-child)>a { border-radius : {{RepoBradius}}; } ',
					],
					(object) [
                        'condition' => [(object) ['key' => 'resmenuType', 'relation' => '==', 'value' => 'off-canvas']],
                        'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .template-wrap{ border-radius: {{RepoBradius}}; }',
                    ],
				],
				'scopy' => true,
			],
			'ressubColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a { color: {{ressubColor}}; }',
					],
				],
				'scopy' => true,
			],
			'msubmenuIco' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a span.indi-icon { color: {{msubmenuIco}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .tpgb-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon:before { border-color: {{msubmenuIco}}; } {{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2  .tpgb-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon:after{ background : {{msubmenuIco}} }',
					],
				],
				'scopy' => true,
			],
			'ressubBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu .tpgb-megamenu-content',
					],
				],
				'scopy' => true,
			],
			'ActressubColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.open > a { color: {{ActressubColor}}; }',
					],
				],
				'scopy' => true,
			],
			'msubmenuHvico' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.open > a span.indi-icon,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li:hover > a span.indi-icon { color: {{msubmenuHvico}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.open > a span.indi-icon:before,{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2  .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li:hover > a span.indi-icon:before{ border-color: {{msubmenuHvico}}; } {{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2  .tpgb-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a .indi-icon:after,{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .tpgb-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.open > a .indi-icon:after{ background : {{msubmenuHvico}} }',
					],
				],
				'scopy' => true,
			],
			'actressubBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.open > a',
					],
				],
				'scopy' => true,
			],
			'Hvreffect' => [
				'type' => 'string',
				'default' => 'none',
				'scopy' => true,
			],
			'borderHgt' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Hvreffect', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-1>li>a:before { height: {{borderHgt}}; }',
					],
				],
				'scopy' => true,
			],
			'borderColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-1>li>a:before,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:before,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:after { background: {{borderColor}}; }',
					],
				],
				'scopy' => true,
			],
			'borderheight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Hvreffect', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:before,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:after { height: {{borderheight}}; }',
					],
				],
				'scopy' => true,
			], 
			'hvrBcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Hvreffect', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:hover:before,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:hover:after { background: {{hvrBcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'borderAlign' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],			
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'Hvreffect', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:before,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.menu-hover-style-2>li>a:after { bottom: {{borderAlign}}; }',
					],
				],
				'scopy' => true,
			],
			'menuInver' => [
				'type' => 'boolean',
				'default' => false,	
				'scopy' => true,
			],
			'seleOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => 1,
					"unit" => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuInver', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.hover-inverse-effect > li.is-hover  { opacity: {{seleOpacity}}; }',
					],
				],
				'scopy' => true,
			],
			'remOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => 0.2,
					"unit" => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuInver', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.is-hover-inverse > li  { opacity: {{remOpacity}}; }',
					],
				],
				'scopy' => true,
			],
			'submenuInver' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'subseleOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => 1,
					"unit" => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'submenuInver', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.submenu-hover-inverse-effect li.dropdown .dropdown-menu > li.is-hover { opacity: {{subseleOpacity}}; }',
					],
				],
				'scopy' => true,
			],
			'subremOpacity' => [
				'type' => 'string',
				'default' => [ 
					'md' => 0.2,
					"unit" => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'submenuInver', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav.is-submenu-hover-inverse li.dropdown .dropdown-menu > li { opacity: {{subremOpacity}}; }',
					],
				],
				'scopy' => true,
			],
			'sindiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-1' ],],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-1 .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon{color : {{sindiColor}} } ' ,
					],
					(object) [
						'condition' => [(object) [ 'key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-2' ],],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon:after,{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon:before{background : {{sindiColor}}; } {{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a .indi-icon:before{ border-color: {{sindiColor}}; background: 0 0; } ' ,
					],
				],
				'scopy' => true,
			],
			'shvrindiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) [ 'key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-1' ],],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-1 .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a .indi-icon{color : {{shvrindiColor}} } ' ,
					],
					(object) [
						'condition' => [(object) [ 'key' => 'subMenuindi', 'relation' => '==', 'value' => 'style-2' ],],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a .indi-icon:after,{{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a .indi-icon:before{background : {{shvrindiColor}}; } {{PLUS_WRAP}} .tpgb-nav-inner.sub-menu-indiacator-style-2 .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a .indi-icon:before{ border-color: {{shvrindiColor}}; background: 0 0; } ' ,
					],
				],
				'scopy' => true,
			],
			'labelTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li a .nav-label-text',
					],
				],
				'scopy' => true,
			],
			'horiOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li a .nav-label-text{right : {{horiOffset}};} {{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li a span .nav-label-text{left : {{horiOffset}}; right:auto;}',
					]
				],
				'scopy' => true,
			],
			'verOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav>li>a .nav-label-text{top : {{verOffset}};}',
					]
				],
				'scopy' => true,
			],
			'lapadding' => [
				'type' => 'object',
				'default' => [ 
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a .nav-label-text{padding : {{lapadding}};}',
					]
				],
				'scopy' => true,
			],
			'labelBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li a .nav-label-text',
					]
				],
				'scopy' => true,
			],
			'laBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a .nav-label-text{border-radius: {{laBradius}}}',
					],
				],
				'scopy' => true,
			],
			'labelBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li a .nav-label-text',
					]
				],
				'scopy' => true,
			],
			'SlabelTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text',
					],
				],
				'scopy' => true,
			],
			'ShoriOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text{right : {{ShoriOffset}};} {{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text{left : {{ShoriOffset}}; right:auto;}',
					]
				],
				'scopy' => true,
			],
			'sverOffset' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text,{{PLUS_WRAP}} .tpgb-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text{top : {{sverOffset}};}',
					]
				],
				'scopy' => true,
			],
			'slapadding' => [
				'type' => 'object',
				'default' => [ 
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text{padding : {{slapadding}};}',
					]
				],
				'scopy' => true,
			],
			'slabelBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text',
					]
				],
				'scopy' => true,
			],
			'slaBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text{border-radius: {{slaBradius}}}',
					],
				],
				'scopy' => true,
			],
			'slabelBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [ 
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu > li > a .nav-label-text',
					]
				],
				'scopy' => true,
			],
			'submenuAlign' => [
				'type' => 'string',
				'default' => 'left',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .navbar-nav li.menu-item-has-children ul.dropdown-menu{ text-align: {{submenuAlign}}; }',
					],
				],
				'scopy' => true,
			],
			'respotemplate' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'resblockTemp' => [
				'type' => 'string',
				'default' => '',
			],
			'sidetitleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
					'size' => [ 'md' => '', 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle',
					],
				],
				'scopy' => true,
			],
			'preISize' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'prefixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle .pre-icon{font-size : {{preISize}}px }',
					],
				],
				'scopy' => true,
			],
			'postISize' => [
				'type' => 'string',
				'default' =>'',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'postfixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle .post-icon{font-size : {{postISize}}px }',
					],
				],
				'scopy' => true,
			],
			'SidetitlePadding' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle{padding : {{SidetitlePadding}} }',
					],
				],
				'scopy' => true,
			],
			'sidetitleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle{color : {{sidetitleColor}} }',
					],
				],
				'scopy' => true,
			],
			'preIcolor'  => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'prefixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle .pre-icon{color : {{preIcolor}} }',
					],
				],
				'scopy' => true,
			],
			'postIcolor'  => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'postfixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle .post-icon{color : {{postIcolor}} }',
					],
				],
				'scopy' => true,
			],
			'titlebarBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle',
					],
				],
				'scopy' => true,
			],
			'titleBradius' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle{border-radius: {{titleBradius}} }',
					],
				],
				'scopy' => true,
			],
			'titleBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle',
					],
				],
				'scopy' => true,
			],
			'TitleBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle',
					],
				],
				'scopy' => true,
			],
			'HsidetitleColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover{color : {{HsidetitleColor}} }',
					],
				],
				'scopy' => true,
			],
			'HvrpreIcolor'  => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'prefixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover .pre-icon{color : {{HvrpreIcolor}} }',
					],
				],
				'scopy' => true,
			],
			'HvrpostIcolor'  => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side'],
							['key' => 'prefixIcon', 'relation' => '!=', 'value' => '']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover .post-icon{color : {{HvrpostIcolor}} }',
					],
				],
				'scopy' => true,
			],
			'Htitlebor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover',
					],
				],
				'scopy' => true,
			],
			'titlehvBradius' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover{border-radius: {{titlehvBradius}} }',
					],
				],
				'scopy' => true,
			],
			'titleHvBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover',
					],
				],
				'scopy' => true,
			],
			'TitlehvBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle:hover',
					],
				],
				'scopy' => true,
			],
			'sidenavWidth' => [
				'type' => 'string',
				'default' => 240,
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav,{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .vertical-side-toggle{max-width : {{sidenavWidth}}px }',
					],
				],
				'scopy' => true,
			],
			'SidenavPadding' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav{padding : {{SidenavPadding}} }',
					],
				],
				'scopy' => true,
			],
			'sidenavborder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav',
					],
				],
				'scopy' => true,
			],
			'sidenavBradius' => [
				'type' => 'object',
				'default' => [ 
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
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav{border-radius: {{sidenavBradius}} }',
					],
				],
				'scopy' => true,
			],
			'snavBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => 'color',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav',
					],
				],
				'scopy' => true,
			],
			'snavBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'menuLayout', 'relation' => '==', 'value' => 'vertical-side']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item.vertical-side .navbar-nav',
					],
				],
				'scopy' => true,
			],
			'descAlign' => [
				'type' => 'string',
				'default' => 'left',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li .tpgb-nav-desc{ text-align: {{descAlign}}; }',
					],
				],
				'scopy' => true,
			],
			'descpadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li .tpgb-nav-desc{ padding: {{descpadding}}; }',
					],
				],
				'scopy' => true,
			],
			'descmargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li .tpgb-nav-desc{ margin: {{descmargin}}; }',
					],
				],
				'scopy' => true,
			],
			'descTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li .tpgb-nav-desc',
					],
				],
				'scopy' => true,

			],
			'descColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li .tpgb-nav-desc{ color: {{descColor}}; }',
					],
				],
				'scopy' => true,
			],
			'menulastOpen' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'menuNo' => [
				'type' => 'string',
				'default' => '',
				'scopy' => true,
			],
			'MiconWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li>a>.tpgb-navicon-wrap{ width: {{MiconWidth}}; height: {{MiconWidth}}; line-height: {{MiconWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'miconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li>a> .tpgb-navicon-wrap',
					],
				], 
				'scopy' => true,
			],
			'iconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a>.tpgb-navicon-wrap .nav-menu-icon{ color: {{iconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'miconBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
						'sm' => (object)[],
						'xs' => (object)[],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li>a>.tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'MinconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li>a>.tpgb-navicon-wrap{ border-radius : {{MinconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'mIconBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li>a>.tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'mhvriconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li:hover>a>.tpgb-navicon-wrap',
					],
				], 
				'scopy' => true,
			],
			'hvrIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li:hover>a>.tpgb-navicon-wrap .nav-menu-icon{ color: {{hvrIconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'hvrBColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li:hover>a>.tpgb-navicon-wrap{ border-color : {{hvrBColor}}; }',
					],
				],
				'scopy' => true,
			],
			'MhvrinconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li:hover>a>.tpgb-navicon-wrap{ border-radius : {{MhvrinconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'mIconHvrBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li:hover>a>.tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'mActiconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.active>a>.tpgb-navicon-wrap',
					],
				], 
				'scopy' => true,
			],
			'actIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li.active>a>.tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav li:focus>a>.nav-menu-icon{ color: {{actIconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'ActBColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.active>a>.tpgb-navicon-wrap{ color : {{ActBColor}}; }',
					],
				],
				'scopy' => true,
			],
			'MActinconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.active>a>.tpgb-navicon-wrap{ border-radius : {{MActinconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'mIconActBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-wrap .tpgb-nav-inner .navbar-nav>li.active>a>.tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'siconSize' => [
				'type' => 'object',
				'default' => [
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [  
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a > .tpgb-navicon-wrap .nav-menu-icon{font-size: {{siconSize}};}  {{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a > .tpgb-navicon-wrap img.nav-menu-img{ max-width: {{siconSize}};} ',
					]
				],
				'scopy' => true,
			],
			'SiconWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a .tpgb-navicon-wrap{ width: {{SiconWidth}}; height: {{SiconWidth}}; line-height: {{SiconWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'siconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'siconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a > .tpgb-navicon-wrap .nav-menu-icon{ color: {{siconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'SiconBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
					'width' => (object) [
						'md' => (object)[
							'top' => '',
							'left' => '',
							'bottom' => '',
							'right' => '',
						],
						'sm' => (object)[],
						'xs' => (object)[],
						"unit" => "",
					],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'suninconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a .tpgb-navicon-wrap{ border-radius : {{suninconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'siconshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'suhvriconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:hover > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'sHiconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:hover > a > .tpgb-navicon-wrap .nav-menu-icon{ color: {{sHiconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'subhvrBColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:hover > a > .tpgb-navicon-wrap .nav-menu-icon{ border-color: {{subhvrBColor}}; }',
					],
				],
				'scopy' => true,
			],
			'subhvrinconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:hover > a .tpgb-navicon-wrap{ border-radius : {{subhvrinconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'sIconHvrBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:hover > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'subActiconBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'saciconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a > .tpgb-navicon-wrap .nav-menu-icon,{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li:focus > a > .tpgb-navicon-wrap .nav-menu-icon{ color: {{saciconColor}}; }',
					],
				],
				'scopy' => true,
			],
			'subActBColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a > .tpgb-navicon-wrap .nav-menu-icon{ border-color: {{subActBColor}}; }',
					],
				],
				'scopy' => true,
			],
			'subActinconBrad' => [
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
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a .tpgb-navicon-wrap{ border-radius : {{subActinconBrad}}; }',
					],
				],
				'scopy' => true,
			],
			'subIconActBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'TypeMenu', 'relation' => '==', 'value' => 'custom']],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .nav li.dropdown .dropdown-menu > li.active > a .tpgb-navicon-wrap',
					],
				],
				'scopy' => true,
			],
			'msubmnAlign' => [
				'type' => 'string',
				'default' => 'left',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-mobile-menu.tpgb-menu-toggle .navbar-nav li .dropdown-menu li a{ justify-content : {{msubmnAlign}}; }',
					],
				],

			],
			'accessWeb' => [
				'type' => 'boolean',
				'default' => false,
				'scopy' => true,
			],
			'borderFocus' => [
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
						'condition' => [(object) ['key' => 'accessWeb', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a:focus,{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu>li>a:focus',
					],
				],
				'scopy' => true,
			],
			'shadowFocus' => [
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
						'condition' => [(object) ['key' => 'accessWeb', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-nav-item .navbar-nav>li>a:focus,{{PLUS_WRAP}} .tpgb-nav-inner .tpgb-nav-item .navbar-nav li.dropdown .dropdown-menu>li>a:focus',
					],
				],
				'scopy' => true,
			],
		];
		
	$attributesOptions = array_merge($attributesOptions, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-navigation-builder', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_navbuilder_render_callback'
    ) );
}
add_action( 'init', 'tpgb_tp_navbuilder' );


function tpgb_get_menu_item(){
	$nav_menu = isset($_POST['menu_slug']) ? sanitize_text_field(wp_unslash($_POST['menu_slug'])) : '';
	$menu_hover_class = isset($_POST['menuHvr']) ? sanitize_text_field(wp_unslash($_POST['menuHvr'])) : '';
	$menu_hover_inverse = isset($_POST['menuInvers']) ? wp_unslash($_POST['menuInvers']) : '';
	$menu_hover_inverse .= ' '.(isset($_POST['submenuInvers']) ? wp_unslash($_POST['submenuInvers']) : '');
	$menu = '';
	if(empty($nav_menu)){
		exit();
	}
	$nav_menu_args=array(
		'menu'           => $nav_menu,
		'theme_location'    => 'default_navmenu',
		'depth'             => 8,
		'container'         => '',
		'container_class'   => '',
		'menu_class'        => 'nav navbar-nav '.$menu_hover_class.' '.$menu_hover_inverse.' ',
		'fallback_cb'       => false,
		'walker'            => new tpgb_Navigation_NavWalker('editor')
	);

	$menu .= wp_nav_menu( apply_filters( 'tpgb_nav_menu_args', $nav_menu_args, $nav_menu, '' ) );
	echo  $menu;

	exit();
}
add_action('wp_ajax_tpgb_get_menu_item', 'tpgb_get_menu_item');

class tpgb_Navigation_NavWalker extends \Walker_Nav_Menu {
	
	public $tpgb_editor;

	public function __construct($tpgb_editor) {

        $this->tpgb_editor = $tpgb_editor;
    }
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$dropdown_menu = "\n$indent<ul role=\"menu\" class=\"dropdown-menu\">\n";
		$dropdown_menu = apply_filters( 'theplus_nav_menu_start_lvl', $dropdown_menu, $indent, $args );
		$output .= $dropdown_menu;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'animate-dropdown menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

			if ( $args->has_children ) {
				if ( $args->theme_location == 'departments-menu' && $depth === 0 ) {
					$class_names .= ' depth-'.$depth.' dropdown-submenu';
				} elseif ( $depth === 0 ) {
					$class_names .= ' depth-'.$depth.' dropdown';
				} else {
					$class_names .= ' depth-'.$depth.' dropdown-submenu';
				}
			}
			$indiIcon='';
			if ( $args->has_children ) {
				if ( $args->theme_location == 'departments-menu' && $depth === 0 ) {
					//$indiIcon .= '<span class="indi-icon"><i class="fa fa-angle-down></i></span>';
				} elseif ( $depth === 0 ) {
					$indiIcon .= '<span class="indi-icon"><i class="fa fa-angle-down"></i></span>';
				} else {
					$indiIcon .= '<span class="indi-icon"><i class="fa fa-angle-down"></i></span>';
				}
			}

			if ( in_array( 'current-menu-item', $classes ) )
				$class_names .= ' active';

			$plus_data_attr = '';
			$tp_megamenu_type = get_post_meta( $item->ID, 'menu-item-tp-megamenu-type', true );
			$tp_menu_alignment = get_post_meta( $item->ID, 'menu-item-tp-menu-alignment', true );
			if( !empty( $tp_megamenu_type ) && $tp_megamenu_type == 'default' ) {
				$tp_dropdown_width = get_post_meta( $item->ID, 'menu-item-tp-dropdown-width', true );
				if( !empty( $tp_dropdown_width ) ) {
					$class_names .= ' plus-dropdown-default';
					$plus_data_attr .= ' data-dropdown-width="'.esc_attr($tp_dropdown_width).'px"';
				}
			}else if( !empty( $tp_megamenu_type ) && $tp_megamenu_type != 'default' ) {
				$class_names .= ' plus-dropdown-'.esc_attr($tp_megamenu_type);
			}
			if( !empty( $tp_megamenu_type ) && $tp_megamenu_type == 'default' ) {
				$class_names .= ' plus-dropdown-menu-'.$tp_menu_alignment;
			}
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .' '.$plus_data_attr.'>';

			$atts = array();
			$atts['title']  = ! empty( $item->title )	? $item->title	: '';
			$atts['target'] = ! empty( $item->target )	? $item->target	: '';
			$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

			// If item has_children add atts to a.
			if ( $args->has_children && $depth === 0 ) {
				$atts['href']   		= $item->url ;				
				//$atts['data-toggle'] = 'dropdown';
				$atts['class']			= 'dropdown-toggle';
				$atts['aria-haspopup']	= 'true';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
			
			if($this->tpgb_editor == 'editor'){
				unset($atts['href']);
			}
			
			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}
			
			if($this->tpgb_editor == 'editor' &&  !empty($indiIcon)){
				$attributes .= ' onclick="( (e) => { var navSideItem = this.parentNode,navSideUl = this.parentNode.parentNode,navSideItemSub = navSideItem.querySelector(\'ul.dropdown-menu\'); if(navSideItem.classList.contains(\'open\')){  navSideItemSub.classList.remove(\'open-menu\');navSideItem.classList.remove(\'open\'); } else{ navSideUl.style.height = \'auto\'; navSideUl.querySelector(\'li.dropdown,li.dropdown-submenu.open\').classList.remove(\'open\');navSideItemSub.classList.add(\'open-menu\');navSideItem.classList.add(\'open\'); navSideUl.querySelector(\'li.dropdown,li.dropdown-submenu.open\').classList.remove(\'open\'); jQuery(navSideItemSub).slideDown(400);  } })()"';
			}
			
			$icon_class_type = get_post_meta( $item->ID, 'menu-item-tp-menu-icon-type', true );
			if(!empty($icon_class_type) && $icon_class_type == 'icon_class' ){
				$icon_class = get_post_meta( $item->ID, 'menu-item-tp-icon-class', true );
				$icon = empty( $icon_class ) ? '' : '<i class="plus-nav-icon-menu ' . esc_attr( $icon_class ) . '"></i>';
			}else if(!empty($icon_class_type) && $icon_class_type == 'icon_image' ){
				$attachment_id = get_post_meta( $item->ID, 'tp-menu-icon-img', true );
				$icon =  wp_get_attachment_image($attachment_id , 'full' , false, ['class' => 'plus-nav-icon-menu icon-img']);
			}else{
				$icon ='';
			}
			
			$tp_text_label = get_post_meta( $item->ID, 'menu-item-tp-text-label', true );
			if(!empty($tp_text_label)){
				$tp_text_label_color = get_post_meta( $item->ID, 'menu-item-tp-label-color', true );
				$tp_text_label_bgcolor = get_post_meta( $item->ID, 'menu-item-tp-label-bg-color', true );
				$label_style = ($tp_text_label_color) ?  'color:'.esc_attr($tp_text_label_color).';' : '';
				$label_style .= ($tp_text_label_bgcolor) ?  'background-color:'.esc_attr($tp_text_label_bgcolor).';' : '';
				
				$label_style = ($label_style) ? 'style="'.$label_style.'"' : '';
				$text_label = '<span class="nav-label-text" '.$label_style.'>'.esc_html($tp_text_label).'</span>';
			}else{
				$text_label ='';
			}
			
			
			
			$item_output = $args->before;
			
			if( 'plus-mega-menu' == $item->object ){
				
			} else {
				if ( ! empty( $item->attr_title ) && !ctype_space($item->attr_title)) {					
					$item_output .= '<a'. $attributes .'><span class="' . esc_attr( $item->attr_title ) . ' "></span> ';
				} else {
					$item_output .= '<a'. $attributes .' data-text="' . esc_attr( $item->title ) . '">';
				}
				
				$item_output .= $icon;
				$item_output .= '<span class="tpgb-title-wrap">';
					$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
					$item_output .= (!empty($item->description)) ? '<span class="tpgb-nav-desc">'.$item->description.'</span>' : '';
				$item_output .= '</span>';
				
				$item_output .= $text_label;
				if($args->has_children && 0 === $depth ){
					$item_output .= $indiIcon;
					$item_output .='</a>';
				}else if($args->has_children && 1 <= $depth ){
					$item_output .= $indiIcon;
					$item_output .='</a>';
				}else{
					$item_output .='</a>';
				}
				$item_output .= $args->after;
			}
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;
			
        $id_field = $this->db_fields['id'];

        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . esc_html__( 'Add a menu', 'tpgbp' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo wp_kses_post( $fb_output );
		}
	}
	
}