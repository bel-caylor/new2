<?php
/* Block : Posts Search Bar
 * @since : 1.3.0
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_search_bar_render_callback( $attr, $content) {
	$output = '';
	$block_id = (!empty($attr['block_id'])) ? $attr['block_id'] : uniqid("title");
	$placeholder = (!empty($attr['placeholder'])) ? $attr['placeholder'] : '';
	$showButn = (!empty($attr['showButn'])) ? $attr['showButn'] : '';
	$searchField = (!empty($attr['searchField'])) ? $attr['searchField'] : [];
	$searchType =  (!empty($attr['searchType'])) ? $attr['searchType'] : 'otheroption';
	$searchBtn =  (!empty($attr['searchBtn'])) ? $attr['searchBtn'] : [];
	$iconType =  (!empty($attr['iconType'])) ? $attr['iconType'] : 'fontAwesome';
	$searchIcon =  (!empty($attr['searchIcon'])) ? $attr['searchIcon'] : '';
	$resultStyle = (!empty($attr['resultStyle'])) ? $attr['resultStyle'] : 'style-1';
	$resultVisSet =  (!empty($attr['resultVisSet'])) ? $attr['resultVisSet'] : [];
	$resAreaLink =  (!empty($attr['resAreaLink'])) ? $attr['resAreaLink'] : [];
	$textLimit =  (!empty($attr['textLimit'])) ? $attr['textLimit'] : [];
	$acfFilter =  (!empty($attr['acfFilter'])) ? $attr['acfFilter'] : [];
	$genericFilter =  (!empty($attr['genericFilter'])) ? $attr['genericFilter'] : [];
	$searchLabel = (!empty($attr['searchLabel'])) ? $attr['searchLabel'] : '';
	$inputDis = (!empty($attr['inputDis'])) ? $attr['inputDis'] : false;
	$postNFmessage = (!empty($attr['postNFmessage'])) ? $attr['postNFmessage'] : '';
	$preSuggest = (!empty($attr['preSuggest'])) ? $attr['preSuggest'] : false;
	$suggestText = (!empty($attr['suggestText'])) ? $attr['suggestText'] : '';
	$overlayTgl = (!empty($attr['overlayTgl'])) ? $attr['overlayTgl'] : false;

	$ttlResText = (!empty($resultVisSet['enTcount']) && !empty($resultVisSet['tResText'])) ? $resultVisSet['tResText'] : '';

	$postCount = (!empty($attr['postCount'])) ? (int)$attr['postCount'] : 3;
	$blockTemplate = (!empty($attr['blockTemplate'])) ? $attr['blockTemplate'] : '';

	$includeTerms = (!empty($attr['includeTerms'])) ? json_decode($attr['includeTerms']) : '';
	$excludeTerms = (!empty($attr['excludeTerms'])) ? json_decode($attr['excludeTerms']) : '';
	$taxonomySlug = (!empty($attr['taxonomySlug'])) ? $attr['taxonomySlug'] : '';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attr );

	$disInputClass = (!empty($inputDis)) ? 'tpgb-ser-input-dis' : '';

	$allResultLoad = false;
	$onLoadAttr = $lsearchData = [];

	// Set Field In Filter Area
	$filterField = '';
	if(!empty($searchField)){
		foreach ($searchField as $index => $item ) {
			$FieldValue='';
			$sourceType = !empty($item['sourceType']) ? $item['sourceType'] : '';
			$PostData = !empty($item['postType']) ? $item['postType'] : array('post');
			$taxonomyData = !empty($item['taxonomy']) ? $item['taxonomy'] : '';
			$showsubcat = !empty($item['showSubCat']) ? $item['showSubCat'] : '';
			$phAllResult = !empty($item['phAllResult']) ? $item['phAllResult'] : false;
			$DataArray=[];
			
			if(($sourceType == 'post') && !empty($item['postType']) && (!empty($PostData) && is_array($PostData) || is_object($PostData))){
				
				foreach ($PostData as $value) {
					$count = wp_count_posts($value['value']);
					$countNum =  !empty($count->publish) ? $count->publish : 0;
					$DataArray[$value['value']] = ['name'=>ucfirst($value['value']), 'count'=>$countNum];
				}
				if(!empty($DataArray)){
					//$tDataA = count($DataArray);
					//if($tDataA > 1){
						$FieldValue .= tpgb_search_drop_down($DataArray, 'post', $block_id, $taxonomy='', $item, $inputDis);
					//}
				}
				
			}else if($sourceType == 'taxonomy' && !empty($taxonomyData)) {
				$cat_args = ['taxonomy'=>$taxonomyData, 'parent' => 0, 'hide_empty'=>false];
				$tax_terms = get_categories($cat_args);

				if(!empty($phAllResult)){
					$allResultLoad = true;
					$lsearchData = [
						's' => '',
						'taxonomy' => $taxonomyData,
						'cat' => 'all'
					];
				}
				
				foreach ($tax_terms as $index => $value) {
					$Name = !empty($value->name) ? $value->name : '';
					$Number = !empty($value->category_count) ? $value->category_count : 0;
					$TermId = !empty($value->term_id) ? $value->term_id : '';

					$DataArray[$TermId] = ['name'=>$Name,'count'=>$Number,'parent'=>''];
					if($taxonomyData == 'category' && $showsubcat == 'yes'){
						$args2 = array(
							'taxonomy'     => $taxonomyData,
							'child_of'     => 0,
							'parent'       => $TermId,
							'orderby'      => 'name',
							'show_count'   => 1,
							'pad_counts'   => 0,
							'hierarchical' => 1,
							'title_li'     => '',
							'hide_empty'   => 0
						);
						$tax_terms2 = get_categories($args2);
						foreach ($tax_terms2 as $one) {
							$Oname = !empty($one->name) ? $one->name :''; 
							$Ocount = !empty($one->count) ? $one->count :''; 
							$DataArray[$one->term_id] = ['name'=>' - '.ucwords($Oname),'count'=>$Ocount,'parent'=>$Name];
						}
					}
				}
				if(!empty($DataArray)){
					
					$FieldValue .= tpgb_search_drop_down($DataArray, 'category', $block_id, $taxonomy=$taxonomyData, $item, $inputDis);
				}
			}
			if(!empty($FieldValue)){
				$filterField .= '<div class="tpgb-post-dropdown">'.$FieldValue.'</div>';
			}
		}
	}
	
	// Result Attributes
	$ResultOnOff = [];
	if($resultStyle=='custom'){
		$ResultOnOff = [
			'errormsg' => !empty($postNFmessage) ? $postNFmessage : 'Sorry, But Nothing Matched Your Search Terms.'
		];
	}else{
		$ResultOnOff = [
			'ONTitle' => !empty($resultVisSet['enTitle']) ? 1 : 0,
			'ONContent' => !empty($resultVisSet['enContent']) ? 1 : 0,
			'ONThumb' => !empty($resultVisSet['enThumb']) ? 1 : 0,
			'ONPrice' => !empty($resultVisSet['enPrice']) ? 1 : 0,
			'ONShortDesc' => !empty($resultVisSet['enSdesc']) ? 1 : 0,
			'TotalResult' => !empty($resultVisSet['enTcount']) ? 1 : 0,
			'TotalResultTxt' => $ttlResText,
	
			'ResultlinkOn' => !empty($resAreaLink['resLinkEn']) ? 1 : 0,
			'Resultlinktarget' => !empty($resAreaLink['resLinkTarget']) ? $resAreaLink['resLinkTarget'] : '',
	
			'TxtTitle' => !empty($textLimit['titleLimit']) ? 1 : 0,
			'texttype' => !empty($textLimit['limitOnTitle']) ? $textLimit['limitOnTitle'] : 'char',
			'textcount' => !empty($textLimit['titleLmtCnt']) ? $textLimit['titleLmtCnt'] : 100,
			'textdots'=> !empty($textLimit['titleDisplayDot']) ? $textLimit['titleDisplayDot'] : '',
			'Txtcont' => !empty($textLimit['contentLimit']) ? 1 : 0,
			'ContType' => !empty($textLimit['limitOnContent']) ? $textLimit['limitOnContent'] : 'char',
			'ContCount' => !empty($textLimit['contentLmtCnt']) ? $textLimit['contentLmtCnt'] : 100,
			'ContDots'=> !empty($textLimit['contentDisplayDot']) ? $textLimit['contentDisplayDot'] : '',
	
			'errormsg' => !empty($postNFmessage) ? $postNFmessage : 'Sorry, But Nothing Matched Your Search Terms.'
		];
	}
	$lresultSetting = $ResultOnOff;
	$ResultOnOff = htmlspecialchars(json_encode($ResultOnOff), ENT_QUOTES, 'UTF-8');
	
	$AcfData = [
		'ACFEnable' => !empty($acfFilter) ? 1 : 0,
		'ACFkey' => !empty($acfFilter['acfKey']) ? $acfFilter['acfKey'] : '',
	];
	$lacfData = $AcfData;
	$AcfData = json_encode($AcfData, true);

	$PageStyle = isset($attr['loadOptions']) ? $attr['loadOptions'] : 'none';
	$LoadPage = !empty($attr['loadMoreCounter']) ? 1 : 0;
	$PageData = [];	
	if($PageStyle == 'pagination'){
		$PageData = array(
			'Pagestyle' => $PageStyle,
			'Pcounter' => !empty($attr['counterEnable']) ? 1 : 0,
			'PClimit' => !empty($attr['counterLimit']) ? $attr['counterLimit'] : 5,
			'PNavigation' => !empty($attr['arrowNav']) ? 1 : 0,	
			'PNxttxt' => !empty($attr['cNextText']) ? $attr['cNextText'] : '',
			'PPrevtxt' => !empty($attr['cPrevText']) ? $attr['cPrevText'] : '',
			'PNxticonType' => !empty($attr['cNextIconType']) ? $attr['cNextIconType'] : 'none',
			'PNxticon' => !empty($attr['cNextIcon']) ? $attr['cNextIcon'] : '',
			'PPreviconType' => !empty($attr['cPrevIconType']) ? $attr['cPrevIconType'] : 'none',
			'PPrevicon' => !empty($attr['cPrevIcon']) ? $attr['cPrevIcon'] : '',
			'Pstyle' => !empty($attr['counterStyle']) ? $attr['counterStyle'] : 'center',
		);
	}else{
		$PageData = array(
			'Pagestyle' => $PageStyle,
			'loadbtntxt' => !empty($attr['loadbtnText']) ? $attr['loadbtnText'] : '',
			'loadingtxt' => !empty($attr['loadingtxt']) ? $attr['loadingtxt'] : '',
			'loadedtxt' => !empty($attr['allposttext']) ? $attr['allposttext'] : '',
			'loadnumber' => !empty($attr['postview']) ? $attr['postview'] : '',
			'loadpage' => $LoadPage,
			'loadPagetxt' => !empty($attr['counterText']) ? $attr['counterText'] : '',
		);
	}
	$lpagesetting = $PageData;
	$PageJson = json_encode($PageData, true);

	$GFilter=[];
	if(!empty($genericFilter)){
		$GFilter = array(
			'GFEnable'=> 1,
			'GFSType' => $searchType,
			'GFTitle' => !empty($genericFilter['searchTitle']) ? 1 : 0,
			'GFContent' => !empty($genericFilter['searchContent']) ? 1 : 0,
			'GFName' => !empty($genericFilter['searchPermalink']) ? 1 : 0,
			'GFExcerpt' => !empty($genericFilter['searchExcerpt']) ? 1 : 0,
			'GFCategory' => !empty($genericFilter['searchCategory']) ? 1 : 0,
			'GFTags' => !empty($genericFilter['searchTags']) ? 1 : 0,
		);
	}else{
		$GFilter = array('GFEnable'=> 0,'GFSType' => $searchType);
	}
	$lGFilter = $GFilter;
	$GFarray = json_encode($GFilter, true);
	
	$SpecialCTP = !empty($attr['specificCTP']) ? 1 : 0;
	$SpecialCTPType = !empty($attr['ctpType']) ? $attr['ctpType'] : 'post';
	
	$Defa_Postype=$Defa_tex=[];
	$temp=!empty($attr['searchField']) ? $attr['searchField'] : [];
	if(!empty($temp)){
		foreach($temp as $idx => $item){
			$STY = !empty($item['sourceType']) ? $item['sourceType'] : array('post'); 
			if($STY == 'post' && !empty($item['postType'])){
				foreach($item['postType'] as $item1){
					//$Defa_Postype[] = $item1;
					$Defa_Postype[] = $item1['value'];
				}
			}
		}
	}

	$DefaultSettingg = array(
		'Def_Post' => $Defa_Postype,
		'Def_Tex' => '',
		'SpecialCTP' => $SpecialCTP,
		'SpecialCTPType' => $SpecialCTPType,
		'excludeTerms' => $excludeTerms,
		'includeTerms' => $includeTerms,
		'taxonomySlug' => $taxonomySlug,
	);
	$lDefaultData = $DefaultSettingg;
	$DefaultSetting = json_encode( $DefaultSettingg, true);
	
	$suggest=$suggestlist="";
	if(!empty($preSuggest)){
		$suggestlist = 'list="tpgb-input-suggestions"';
		$sugExplod = explode("|", $suggestText);
		$suggest .= '<datalist id="tpgb-input-suggestions">';
			foreach ($sugExplod as $two) {
				$suggest .= '<option value="'.ltrim(rtrim($two)).'">';
			}
		$suggest .= '</datalist>';
	}
	
	$scrollclass = !empty($attr['scrollBar']) ? 'tpgb-search-scrollbar' : '';
	$Rcolumn='';
	if($resultStyle=='style-2' || $resultStyle=='custom'){
		$Rcolumn = 'tpgb-col-12 ';
		$Rcolumn .= isset($attr['columns']['md']) ? " tpgb-col-lg-".$attr['columns']['md'] : ' tpgb-col-lg-3';
		$Rcolumn .= isset($attr['columns']['sm']) ? " tpgb-col-md-".$attr['columns']['sm'] : ' tpgb-col-md-4';
		$Rcolumn .= isset($attr['columns']['xs']) ? " tpgb-col-sm-".$attr['columns']['xs'] : ' tpgb-col-sm-6';
		
	}else{
		$Rcolumn = 'tpgb-col-12 tpgb-col-lg-12 tpgb-col-md-12 tpgb-col-sm-12 tpgb-col-12';
	}

	//Set parameter
	$dataattr=[];
	$dataattr['ajax'] = !empty($attr['ajaxsearch']) ? 'yes' : 'no';
	$dataattr['ajaxsearchCharLimit'] = !empty($attr['searchClimit']) ? $attr['searchClimit'] : 2;
	$dataattr['nonce'] = wp_create_nonce("tpgb-searchbar");
	$dataattr['style'] = $resultStyle;
	$dataattr['tempid'] = $blockTemplate;
	$dataattr['styleColumn'] = $Rcolumn;
	$dataattr['post_page'] = $postCount;
	$dataattr['Postype_Def'] = $Defa_Postype;
	$dataattr = htmlspecialchars(json_encode($dataattr), ENT_QUOTES, 'UTF-8');

	if(!empty($resultStyle) && $resultStyle=='custom' && isset($attr['blockTemplate']) && !empty($attr['blockTemplate'])){
		Tpgb_Library()->plus_do_block($attr['blockTemplate']);
	}

	$output .= '<div class="tpgb-search-bar tpgb-relative-block tpgb-block-'.esc_attr($block_id).' '.esc_attr($blockClass).' '.esc_attr($disInputClass).'" data-id="'.esc_attr($block_id).'" data-ajax_search= \'' .$dataattr. '\' data-result-setting= \''.$ResultOnOff.'\' data-genericfilter='.$GFarray.' data-pagination-data= \''.$PageJson.'\' data-acfdata='.esc_attr($AcfData).' data-default-data= \''.$DefaultSetting.'\'>';
		
		if(!empty($overlayTgl)){
			$output .= '<div class="tpgb-rental-overlay"></div>';
		}	
	
		$output .= '<form class="tpgb-search-form" method="get" action="'.esc_url(site_url()).'">';
			$output .= '<div class="tpgb-form-field tpgb-row">';
				if(empty($inputDis)){
					$output .= '<div class="tpgb-input-field">';
						$output .= '<div class="tpgb-input-label-field">';
							if(!empty($searchLabel)){
								$output .= '<label class="tpgb-search-label tpgb-trans-linear">'.esc_html( $searchLabel ).'</label>';
							}
						$output .= '</div>';
						$output .= '<div class="tpgb-input-inner-field">';
							$output .= '<input name="s" '.$suggestlist.' id="seatxt-'.esc_attr($block_id).'" class="tpgb-search-input" type="text" name="search" placeholder="'.esc_attr($placeholder).'" autocomplete="off" />';
							$output .= $suggest;
							if($iconType=='fontAwesome' && !empty($searchIcon)) {
								$output .= '<span class="tpgb-search-input-icon"><i class="'.esc_attr($searchIcon).'"></i></span>';
							}
							$output .= '<div class="tpgb-ajx-loading"><div class="tpgb-spinner-loader"></div></div>';
							$output .= '<span class="tpgb-close-btn"><i class="fas fa-times-circle"></i></span>';
						$output .= '</div>';
					$output .= '</div>';
				}
				
				$output .= $filterField;
				
				if(!empty($searchBtn)) {
					$GetMedia='';
					if(!empty($searchBtn['searchBtnTgl'])){
						if($searchBtn['sBtnIconType'] == 'fontAwesome' && !empty($searchBtn['sBtnIcon']) ){
							$GetMedia .= '<span class="tpgb-button-icon"><i class="'.esc_attr($searchBtn['sBtnIcon']).'"></i></span>';
						}else if($searchBtn['sBtnIconType'] == 'image' && !empty($searchBtn['imgField']['url'])){
							$GetMedia .= '<span class="tpgb-button-Image"><img src="'.esc_url($searchBtn['imgField']['url']).'" class="tpgb-button-ImageTag"></span>';
						}
						$output .= '<div class="tpgb-btn-wrap">';
							$output .= '<button class="tpgb-search-btn" name="submit" >';
								$output .= ($searchBtn['sIconPos'] == 'before') ? $GetMedia : '';
								if(!empty($searchBtn['sBtnText'])){
									$output .= '<span class="tpgb-search-btn-txt '.esc_attr($searchBtn['sIconPos']).'">'.esc_html($searchBtn['sBtnText']).'</span>';
								}
								$output .= ($searchBtn['sIconPos'] == 'after') ? $GetMedia : '';
							$output .= '</button>';
						$output .= '</div>';
					}
				}
				if(!empty($SpecialCTP)){
					$output .= '<input type="hidden" name="post_type" value="'.esc_attr($SpecialCTPType).'" />';
				}
			$output .= '</div>';
		$output .= '</form>';

		$onLoadData = $lSearchRes = $lPagnation = $pageColumn = $lloadmore = $lloadmorepage = $llazyload = $ttlPostCount = '';
		$lStyle = '';
		if(!empty($allResultLoad)){
			$onLoadAttr = [
				'searchData' => $lsearchData,
				'text' => '',
				'postper' => $postCount,
				'GFilter' => $lGFilter,
				'ACFilter' => $lacfData,
				'styleColumn' => $Rcolumn,
				'style' => $resultStyle,
				'tempId' => $blockTemplate,
				'ResultData' => $lpagesetting,
				'DefaultData' => $lDefaultData,
				'resultSetting' => $lresultSetting
			];
			$onLoadData = tpgb_search($onLoadAttr);
			if(!empty($onLoadData) && !empty($onLoadData['posts'])){
				$lStyle = 'style="display: block"';
				$itemPost = '';
				foreach ( $onLoadData['posts'] as $index => $post ) :
					$itemPost .= $post;
				endforeach;
				$lSearchRes .='<div class="tpgb-search-slider tpgb-row">'.$itemPost.'</div>';

				if(isset($onLoadData['pagination']) && !empty($onLoadData['pagination'])){
					$lPagnation = $onLoadData['pagination'];
					$pageColumn = 'data-pageColumn="'.esc_attr($onLoadData['columns']).'"';

				}
				if(isset($onLoadData['lazymore']) && !empty($onLoadData['lazymore'])){
					$llazyload = $onLoadData['lazymore'];
				}
				if(isset($onLoadData['loadmore']) && !empty($onLoadData['loadmore'])){
					$lloadmore = $onLoadData['loadmore'];
				}
				if(isset($onLoadData['loadmore_page']) && !empty($onLoadData['loadmore_page'])){
					$lloadmorepage = $onLoadData['loadmore_page'];
				}

				if(isset($onLoadData['post_count']) && !empty($onLoadData['post_count'])){
					$ttlPostCount = $onLoadData['post_count'].' '.$ttlResText;
				}
			}
		}
		
		$output .= '<div class="tpgb-search-area '.esc_attr($resultStyle).'" '.$lStyle.'>';
			$output .= '<div class="tpgb-search-error"></div>';
			$output .= '<div class="tpgb-search-header tpgb-trans-linear">';
				if(!empty($resultVisSet['enTcount'])){
					$output .= '<div class="tpgb-search-resultcount">'.wp_kses_post($ttlPostCount).'</div>';
				}
				if( ($PageStyle == 'pagination') || ($PageStyle == 'load_more' && !empty($LoadPage)) ){
					$output .= '<div class="tpgb-search-pagina" '.$pageColumn.'>'.wp_kses_post($lloadmorepage).wp_kses_post($lPagnation).'</div>';
				}
			$output .= '</div>';
			$output .= '<div class="tpgb-search-list">';
				$output .= '<div class="tpgb-search-list-inner '.esc_attr($scrollclass).'">'.wp_kses_post($lSearchRes).'</div>';
			$output .= '</div>';
			if($PageStyle == 'load_more'){
				$output .= '<div class="tpgb-load-more">'.wp_kses_post($lloadmore).'</div>';
			}else if($PageStyle == 'lazy_load'){
				$output .= '<div class="tpgb-lazy-load">'.wp_kses_post($llazyload).'</div>';
			}
		$output .= '</div>';
		
    $output .= '</div>';
	
	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attr, $output);

    return $output;
	}

/**
 * Render for the server-side
 */
function tpgb_search_bar() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
    $globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	
	$attributesOptions = array(
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'showButn' => [
				'type' => 'boolean',
				'default' => false,	
			],
			'searchField' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'sourceType' => [
							'type' => 'string',
							'default' => '',
						],
						'postType' => [
							'type' => 'string',
        					'default' => '',
						],
						'fieldPlaceH' => [
							'type' => 'string',
        					'default' => 'All Post',
						],
						'phAllResult' => [
							'type' => 'boolean',
							'default' => false,
						],
						'taxonomy' => [
							'type' => 'string',
							'default' => '',
						],
						'showCount' => [
							'type' => 'boolean',
							'default' => false,
						],
						'showSubCat' => [
							'type' => 'boolean',
							'default' => false,
						],
					],
				],
				'default' => [ 
					[ 'sourceType' => '', 'fieldTitle' => '' , 'postType' => '', 'fieldPlaceH' => 'All Post', 'phAllResult' => false, 'taxonomy' => '' , 'layout' => 'drop_down' , 'showCount' => true ]
				],
			],
			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 4,'xs' => 6 ],
			],
			'columnSpace' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => [
						"top" => 15,
						"right" => 15,
						"bottom" => 15,
						"left" => 15,
					],
					"unit" => 'px',
				],
			],
			'inputDis' => [
				'type' => 'boolean',
        		'default' => false,
			],
			'searchLabel' => [
				'type' => 'string',
        		'default' => '',
			],
			'placeholder' => [
				'type' => 'string',
				'default' => 'Type your keyword to search...',
			],
			'iconType' => [
				'type' => 'string',
				'default' => 'fontAwesome',	
			],
			'searchIcon' => [
				'type'=> 'string',
				'default'=> 'fas fa-search',
			],
			'searchType' => [
				'type' => 'string',
				'default' => 'otheroption',	
			],
			'genericFilter' => [
				'type' => 'object',
				'default' => [
					'searchTitle' => true,
				],	
			],
			'acfFilter' => [
				'type' => 'object',
				'default' => [],	
			],
			
			'resultStyle' => [
				'type' => 'string',
        		'default' => 'style-1',
			],
			'blockTemplate' => [
				'type' => 'string',
				'default' => '',
			],
			'postCount' => [
				'type' => 'string',
        		'default' => '3',
			],
			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 4,'xs' => 6 ],
			],
			'resultVisSet' => [
				'type' => 'object',
				'default' => [
					'enTitle' => true,
					'enContent' => true,
					'enThumb' => true,
					'enPrice' => true,
					'enSdesc' => true,
					'enTcount' => true,
					'tResText' => 'Results',
				],	
			],
			'textLimit' => [
				'type' => 'object',
				'default' => [
					'open' => 0,
					'titleLimit' => false,
					'contentLimit' => false,
				],	
			],
			'resAreaLink' => [
				'type' => 'object',
				'default' => [
					'resLinkEn' => true,
					'resLinkTarget' => '_blank',
				],	
			],
			'scrollBar' => [
				'type' => 'boolean',
        		'default' => false,
			],
			'scBarHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar{ height: {{scBarHeight}}; }',
					],
				],
			],
			'loadOptions' => [
				'type' => 'string',
        		'default' => '',
			],
			'counterEnable' => [
				'type' => 'boolean',
        		'default' => true,
			],
			'counterLimit' => [
				'type' => 'string',
        		'default' => '',
			],
			
			'arrowNav' => [
				'type' => 'boolean',
        		'default' => true,
			],
			'counterStyle' => [
				'type' => 'string',
        		'default' => 'center',
			],
			'cNextText' => [
				'type' => 'string',
        		'default' => 'Next',
			],
			'cNextIconType' => [
				'type' => 'string',
        		'default' => 'none',
			],
			'cNextIcon' => [
				'type'=> 'string',
				'default'=> 'fas fa-arrow-right',
			],
			'cPrevText' => [
				'type' => 'string',
        		'default' => 'Prev',
			],
			'cPrevIconType' => [
				'type' => 'string',
        		'default' => 'none',
			],
			'cPrevIcon' => [
				'type'=> 'string',
				'default'=> 'fas fa-arrow-left',
			],
			
			'loadbtnText' => [
				'type'=> 'string',
				'default'=> 'Load More',
			],
			'loadingtxt' => [
				'type'=> 'string',
				'default'=> 'Loading...',
			],
			'allposttext' => [
				'type' => 'string',
				'default' => 'All Done',
			],
			'postview' => [
				'type'=> 'string',
				'default'=> 3,
			],
			'loadMoreCounter' => [
				'type' => 'boolean',
				'default' => true,
			],
			'counterText' => [
				'type' => 'string',
				'default' => 'Totals:',
			],
			
			'serAlign' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-form-field{ justify-content: {{serAlign}}; }',
					],
				],
				'scopy' => true,
			],
			'taxonomySlug' => [
				'type' => 'string',
				'default' => '',
			],
			'includeTerms' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'excludeTerms' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'ajaxsearch' => [
				'type' => 'boolean',
				'default' => false,
			],
			'searchClimit' => [
				'type'=> 'string',
				'default'=> 3,
			],
			'preSuggest' => [
				'type' => 'boolean',
				'default' => false,
			],
			'suggestText' => [
				'type' => 'string',
				'default' => '',
			],
			'specificCTP' => [
				'type' => 'boolean',
				'default' => false,
			],
			'ctpType' => [
				'type' => 'string',
				'default' => 'post',
			],
			'searchBtn' => [
				'type' => 'object',
				'default' => ['searchBtnTgl' => true, 'sBtnText' => 'Search', 'sBtnIconType' => 'fontAwesome', 'sBtnIcon' => 'fas fa-search', 'sIconPos' => 'before', 'imgField'=> ['id' => '' , 'url' => '']],	
			],
			'postNFmessage' => [
				'type' => 'string',
				'default' => 'Sorry, No Results Were Found.',
			],
			'backVis' => [
				'type' => 'boolean',
				'default' => false,
			],
			
			/* Label Start */
			'labelTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label{ padding : {{labelPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'labelMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label{ margin : {{labelMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'labelNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label{ color: {{labelNColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'labelHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover .tpgb-search-label{ color: {{labelHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'labelNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelNRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label{ border-radius : {{labelNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'labelHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover .tpgb-search-label{ border-radius : {{labelHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'labelNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			'labelHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover .tpgb-search-label',
					],
				],
				'scopy' => true,
			],
			/* Label End */
			
			/* Search Box Start */
			'inputTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input ',
					],
				],
				'scopy' => true,
			],
			'closeSpinIcon' => [
				'type' => 'object',
				'groupField' => [
					(object) [
						'cIconSize' => [
							'type' => 'object',
							'default' => [
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-close-btn{ font-size: {{cIconSize}}; }',
								],
							],
							'scopy' => true,
						],
						'cIconColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-close-btn{ color: {{cIconColor}}; } ',
								],
							],
							'scopy' => true,
						],
						'spinnerSize' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-ajx-loading .tpgb-spinner-loader{ width: {{spinnerSize}}; height: {{spinnerSize}}; }',
								],
							],
							'scopy' => true,
						],
						'spinnerColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}} .tpgb-ajx-loading .tpgb-spinner-loader{ border-top-color: {{spinnerColor}}; } ',
								],
							],
							'scopy' => true,
						],
					],
				],
				'default' => [
					'cIconSize' => ['md' => '', 'unit'=> 'px'],
					'cIconColor' => '',
					'spinnerSize' => ['md' => '', 'unit'=> 'px'],
					'spinnerColor' => ''
				],	
			],
			'inputPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input{ padding : {{inputPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'inputWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-input-field{ flex: unset; width: {{inputWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'searchIconSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input-icon{ font-size: {{searchIconSize}}; }',
					],
				],
				'scopy' => true,
			],
			'intextColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input{ color: {{intextColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'intxtFcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus { color: {{intxtFcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'intPHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input::placeholder{ color: {{intPHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'intPHFColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus::placeholder { color: {{intPHFColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'intIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input-icon{ color: {{intIconColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'intIconFColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus + .tpgb-search-input-icon { color: {{intIconFColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'inbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input ',
					],
				],
				'scopy' => true,
			],
			'inFbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus ',
					],
				],
				'scopy' => true,
			],
			'inNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input ',
					],
				],
				'scopy' => true,
			],
			'inFBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus ',
					],
				],
				'scopy' => true,
			],
			'inBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input{ border-radius : {{inBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'inFBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus{ border-radius : {{inFBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'inNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input ',
					],
				],
				'scopy' => true,
			],
			'inFBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-input:focus ',
					],
				],
				'scopy' => true,
			],
			'inBoxPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field{ padding : {{inBoxPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'inBoxMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field{ margin : {{inBoxMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'inBoxNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field',
					],
				],
				'scopy' => true,
			],
			'inBoxHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field:hover',
					],
				],
				'scopy' => true,
			],
			'inBoxNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field',
					],
				],
				'scopy' => true,
			],
			'inBoxHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field:hover',
					],
				],
				'scopy' => true,
			],
			'inBoxNRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field{ border-radius : {{inBoxNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'inBoxHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field:hover{ border-radius : {{inBoxHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'inBoxNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field',
					],
				],
				'scopy' => true,
			],
			'inBoxHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-input-field:hover',
					],
				],
				'scopy' => true,
			],
			/* Search Box End */
			
			/* Dropdown Start */
			'SelectTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-select, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'selSpinIcon' => [
				'type' => 'object',
				'groupField' => [
					(object) [
						'cIconSize' => [
							'type' => 'object',
							'default' => [
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}}.tpgb-ser-input-dis .tpgb-search-form .tpgb-close-btn{ font-size: {{cIconSize}}; }',
								],
							],
							'scopy' => true,
						],
						'cIconColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}}.tpgb-ser-input-dis .tpgb-search-form .tpgb-close-btn{ color: {{cIconColor}}; } ',
								],
							],
							'scopy' => true,
						],
						'spinnerSize' => [
							'type' => 'object',
							'default' => [ 
								'md' => '',
								"unit" => 'px',
							],
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}}.tpgb-ser-input-dis .tpgb-ajx-loading .tpgb-spinner-loader{ width: {{spinnerSize}}; height: {{spinnerSize}}; }',
								],
							],
							'scopy' => true,
						],
						'spinnerColor' => [
							'type' => 'string',
							'default' => '',
							'style' => [
								(object) [
									'selector' => '{{PLUS_WRAP}}.tpgb-ser-input-dis .tpgb-ajx-loading .tpgb-spinner-loader{ border-top-color: {{spinnerColor}}; } ',
								],
							],
							'scopy' => true,
						],
					],
				],
				'default' => [
					'cIconSize' => ['md' => '', 'unit'=> 'px'],
					'cIconColor' => '',
					'spinnerSize' => ['md' => '', 'unit'=> 'px'],
					'spinnerColor' => ''
				],	
			],
			'selectPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown{ padding : {{selectPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'selectWid' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown{ width : {{selectWid}}; }',
					],
				],
				'scopy' => true,
			],
			'seletxtColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-select, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown-menu{ color: {{seletxtColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'seletxtHcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-select, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu { color: {{seletxtHcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'seleIcnColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-select .tpgb-dd-icon{ color: {{seleIcnColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'seleIcnHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown:hover .tpgb-dd-icon{ color: {{seleIcnHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'seletxtHcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-select, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu { color: {{seletxtHcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'selebgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'seleHbgType' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'seleNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'seleHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'seleBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu{ border-radius : {{seleBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'seleHBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu{ border-radius : {{seleHBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'seleNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			'seleHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover, {{PLUS_WRAP}} .tpgb-form-field .tpgb-sbar-dropdown:hover .tpgb-sbar-dropdown-menu',
					],
				],
				'scopy' => true,
			],
			
			'selMHtxtcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu .tpgb-searchbar-li:hover{ color: {{selMHtxtcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'selMHBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu .tpgb-searchbar-li:hover',
					],
				],
				'scopy' => true,
			],
			'selMHshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu .tpgb-searchbar-li:hover',
					],
				],
				'scopy' => true,
			],
			
			'selScBarBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar',
					],
				],
				'scopy' => true,
			],
			'selScBarWid' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar{ width : {{selScBarWid}}; }',
					],
				],
				'scopy' => true,
			],
			'selThumbBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'selThumbBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-thumb{ border-radius : {{selThumbBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'selThumbshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'selTrackBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
			'selTrackBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-track{ border-radius : {{selTrackBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'selTrackshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-sbar-dropdown .tpgb-sbar-dropdown-menu::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
			
			'selBoxPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown{ padding : {{selBoxPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'selBoxMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown{ margin : {{selBoxMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'selBoxNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown',
					],
				],
				'scopy' => true,
			],
			'selBoxHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown:hover',
					],
				],
				'scopy' => true,
			],
			'selBoxNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown',
					],
				],
				'scopy' => true,
			],
			'selBoxHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown:hover',
					],
				],
				'scopy' => true,
			],
			'selBoxNRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown{ border-radius : {{selBoxNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'selBoxHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown:hover{ border-radius : {{selBoxHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'selBoxNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown',
					],
				],
				'scopy' => true,
			],
			'selBoxHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-post-dropdown:hover',
					],
				],
				'scopy' => true,
			],
			/* Dropdown End */
			
			/* Button Start */
			'btnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn',
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn{ padding : {{btnPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'btnMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn{ margin : {{btnMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'btnIcnSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-btn .tpgb-button-icon{ font-size: {{btnIcnSize}}; }',
					],
				],
				'scopy' => true,
			],
			'btnIcnSpace' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-btn-txt.before{ padding-left : {{btnIcnSpace}}; } {{PLUS_WRAP}} .tpgb-search-btn-txt.after{ padding-right : {{btnIcnSpace}}; }',
					],
				],
				'scopy' => true,
			],
			'sbtnColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn { color: {{sbtnColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'sbtnHcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn:hover { color: {{sbtnHcolor}}; } ',
					],
				],
				'scopy' => true,
			],
			'btnIconColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-btn .tpgb-button-icon { color: {{btnIconColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'btnIconHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-btn:hover .tpgb-button-icon { color: {{btnIconHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'sbtnBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn',
					],
				],
				'scopy' => true,
			],
			'sbtnHbg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn:hover',
					],
				],
				'scopy' => true,
			],
			'sbtnBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn',
					],
				],
				'scopy' => true,
			],
			'sbtnHborder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn:hover',
					],
				],
				'scopy' => true,
			],
			'sbtnBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn{ border-radius : {{sbtnBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'sbtnHBradius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn:hover{ border-radius : {{sbtnHBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'sbtnBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn',
					],
				],
				'scopy' => true,
			],
			'sbtnHshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-search-btn:hover',
					],
				],
				'scopy' => true,
			],
			'btnBoxPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap{ padding : {{btnBoxPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBoxMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap{ margin : {{btnBoxMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBoxNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap',
					],
				],
				'scopy' => true,
			],
			'btnBoxHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'btnBoxNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap',
					],
				],
				'scopy' => true,
			],
			'btnBoxHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap:hover',
					],
				],
				'scopy' => true,
			],
			'btnBoxNRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap{ border-radius : {{btnBoxNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBoxHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap:hover{ border-radius : {{btnBoxHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBoxNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap',
					],
				],
				'scopy' => true,
			],
			'btnBoxHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-btn-wrap:hover',
					],
				],
				'scopy' => true,
			],
			
			/* Results Box Start */
			'rAreaTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area',
					],
				],
				'scopy' => true,
			],
			'rAreaPadding' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area{ padding : {{rAreaPadding}} ; }',
					],
				],
				'scopy' => true,
			],
			'rAreaMargin' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area{ margin : {{rAreaMargin}} ; }',
					],
				],
				'scopy' => true,
			],
			'rAreaWidth' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area{ width: {{rAreaWidth}}; }',
					],
				],
				'scopy' => true,
			],
			'rAreaNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area{ color: {{rAreaNColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'rAreaHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover { color: {{rAreaHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'rAreaNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area',
					],
				],
				'scopy' => true,
			],
			'rAreaHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover ',
					],
				],
				'scopy' => true,
			],
			'rAreaNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area',
					],
				],
				'scopy' => true,
			],
			'rAreaHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover',
					],
				],
				'scopy' => true,
			],
			'rAreaNRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area{ border-radius : {{rAreaNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'rAreaHRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover{ border-radius : {{rAreaHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'rAreaNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area',
					],
				],
				'scopy' => true,
			],
			'rAreaHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover',
					],
				],
				'scopy' => true,
			],
			/* Results Box End */
			
			/* Results Heading Start */
			'rHeadingTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header .tpgb-search-resultcount',
					],
				],
				'scopy' => true,
			],
			'rHeadingPadding' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header{ padding : {{rHeadingPadding}} ; }',
					],
				],
				'scopy' => true,
			],
			'rHeadingMargin' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header{ margin : {{rHeadingMargin}} ; }',
					],
				],
				'scopy' => true,
			],
			'rHeadCntNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header .tpgb-search-resultcount{ color: {{rHeadCntNColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'rHeadCntHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-header .tpgb-search-resultcount{ color: {{rHeadCntHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'rHeadingNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			'rHeadingHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			'rHeadingNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			'rHeadingHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			'rHeadingNRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header{ border-radius : {{rHeadingNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'rHeadingHRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-header{ border-radius : {{rHeadingHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'rHeadingNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			'rHeadingHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-header',
					],
				],
				'scopy' => true,
			],
			/* Results Heading End */
			
			/* Results Content Start*/
			'rContentPadding' => [
				'type' => 'object',
				'default' => [
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
								'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
								'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-title{ padding: {{titlePadding}}; }',
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
								'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
								'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-excerpt{ padding: {{contentPadding}}; }',
							],
						],
						'scopy' => true,
					],
					'wooPricePadding' => [
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
								'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
								'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-price{ padding: {{wooPricePadding}}; }',
							],
						],
						'scopy' => true,
					],
					'wooDescPadding' => [
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
								'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
								'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-shortDesc{ padding: {{wooDescPadding}}; }',
							],
						],
						'scopy' => true,
					],
				],	
			],
			'titleTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-title',
					],
				],
				'scopy' => true,
			],
			'contentTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-excerpt',
					],
				],
				'scopy' => true,
			],
			'wooPriceTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-price',
					],
				],
				'scopy' => true,
			],
			'wooDescTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-shortDesc',
					],
				],
				'scopy' => true,
			],
			'titleNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-title{ color: {{titleNColor}}; }',
					],
				],
				'scopy' => true,
			],
			'titleHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-ser-item:hover .tpgb-serpost-title{ color: {{titleHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'contentNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-excerpt{ color: {{contentNColor}}; }',
					],
				],
				'scopy' => true,
			],
			'contentHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-ser-item:hover .tpgb-serpost-excerpt{ color: {{contentHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'wPriceNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-price{ color: {{wPriceNColor}}; }',
					],
				],
				'scopy' => true,
			],
			'wPriceHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-ser-item:hover .tpgb-serpost-price{ color: {{wPriceHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'wShortDescNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-serpost-shortDesc{ color: {{wShortDescNColor}}; }',
					],
				],
				'scopy' => true,
			],
			'wShortDescHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-ser-item:hover .tpgb-serpost-shortDesc{ color: {{wShortDescHColor}}; }',
					],
				],
				'scopy' => true,
			],
			
			'resConNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item',
					],
				],
				'scopy' => true,
			],
			'resConHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item:hover',
					],
				],
				'scopy' => true,
			],
			'resConNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item',
					],
				],
				'scopy' => true,
			],
			'resConHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item:hover',
					],
				],
				'scopy' => true,
			],
			'resConNRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item{ border-radius : {{resConNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'resConHRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item:hover{ border-radius : {{resConHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'resConNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item',
					],
				],
				'scopy' => true,
			],
			'resConHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-slider .tpgb-ser-item:hover',
					],
				],
				'scopy' => true,
			],
			
			'rCImgPadding' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list .tpgb-item-image{ padding: {{rCImgPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'rCImageWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list .tpgb-serpost-thumb{ width: {{rCImageWidth}};  } ',
					],
				],
				'scopy' => true,
			],
			'rCImageBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list .tpgb-item-image',
					],
				],
				'scopy' => true,
			],
			'rCImageRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list .tpgb-item-image{ border-radius : {{rCImageRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'rCImageBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list .tpgb-item-image',
					],
				],
				'scopy' => true,
			],
			
			'resBoxPadding' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list{ padding: {{resBoxPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'resBoxMargin' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list{ margin: {{resBoxMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'resBoxNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			'resBoxHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			'resBoxNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			'resBoxHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			'resBoxNRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list{ border-radius : {{resBoxNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'resBoxHRadius' => [
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
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-list{ border-radius : {{resBoxHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'resBoxNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			'resBoxHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ajaxsearch', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area:hover .tpgb-search-list',
					],
				],
				'scopy' => true,
			],
			/* Results Content End*/
			
			/* Pagination Start */
			'pagitypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink',
					],
				],
				'scopy' => true,
			],
			'pagiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink { color: {{pagiColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'pagiHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink:hover { color: {{pagiHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'pagiActColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.active { color: {{pagiActColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'pagiBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink',
					],
				],
				'scopy' => true,
			],
			'pagiHBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink:hover',
					],
				],
				'scopy' => true,
			],
			'pagiActBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.active ',
					],
				],
				'scopy' => true,
			],
			'pagiBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink',
					],
				],
				'scopy' => true,
			],
			'pagiHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink:hover',
					],
				],
				'scopy' => true,
			],
			'pagiActbor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.active',
					],
				],
				'scopy' => true,
			],
			
			'nxtBtnNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next { color: {{nxtBtnNColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'nxtBtnHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next:hover{ color: {{nxtBtnHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'nxtBtnNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next',
					],
				],
				'scopy' => true,
			],
			'nxtBtnHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next:hover',
					],
				],
				'scopy' => true,
			],
			'nxtBtnNBdr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next',
					],
				],
				'scopy' => true,
			],
			'nxtBtnHBdr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.next:hover',
					],
				],
				'scopy' => true,
			],
			'preBtnNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev { color: {{preBtnNColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'preBtnHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev:hover{ color: {{preBtnHColor}}; } ',
					],
				],
				'scopy' => true,
			],
			'preBtnNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev',
					],
				],
				'scopy' => true,
			],
			'preBtnHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
					'bgType' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev:hover',
					],
				],
				'scopy' => true,
			],
			'preBtnNBdr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev',
					],
				],
				'scopy' => true,
			],
			'preBtnHBdr' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'pagination']],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-pagelink.prev:hover',
					],
				],
				'scopy' => true,
			],
			/* Pagination End */
			
			/* Load More/Lazy Load Start */
			'loadMPadding' => [
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
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'load_more']],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{ padding: {{loadMPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMMargin' => [
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
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'load_more']],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{ margin: {{loadMMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'load_more']],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'loadAllTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => ['load_more','lazy_load']]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-loaded',
					],
				],
				'scopy' => true,
			],
			'loadAllTxColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => ['load_more','lazy_load']]],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-loaded{ color: {{loadAllTxColor}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMTxColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'load_more']],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{ color: {{loadMTxColor}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMTxHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'load_more']],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover{ color: {{loadMTxHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'loadMHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover',
					],
				],
				'scopy' => true,
			],
			'loadMBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'loadMHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover',
					],
				],
				'scopy' => true,
			],
			'loadMRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{ border-radius : {{loadMRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover{ border-radius : {{loadMHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'loadMBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'loadMHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover',
					],
				],
				'scopy' => true,
			],
			
			'lazySpinColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .post-lazy-load .tpgb-spin-ring div{ border-color: {{lazySpinColor}} transparent transparent transparent; }',
					],
				],
				'scopy' => true,
			],
			'lazySpinWidth' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .post-lazy-load .tpgb-spin-ring div{ width: {{lazySpinWidth}}; height: {{lazySpinWidth}}; } ',
					],
				],
				'scopy' => true,
			],
			'lazySpinBdr' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'loadOptions', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .post-lazy-load .tpgb-spin-ring div{ border-width: {{lazySpinBdr}}; } ',
					],
				],
				'scopy' => true,
			],
			/* Load More/Lazy Load End */
			
			/* Overlay Option Start */
			'overlayTgl' => [
				'type' => 'boolean',
        		'default' => false,
			],
			'overlayBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'overlayTgl', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-search-bar .tpgb-rental-overlay',
					],
				],
				'scopy' => true,
			],
			/* Overlay Option End */
			
			/* Background Option Start */
			'formAlign' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form .tpgb-form-field{ align-items: {{formAlign}};}',
					],
				],
				'scopy' => true,
			],
			'formPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form{ padding: {{formPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'formMargin' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form{ margin: {{formMargin}}; }',
					],
				],
				'scopy' => true,
			],
			'formNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form',
					],
				],
				'scopy' => true,
			],
			'formHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover',
					],
				],
				'scopy' => true,
			],
			'formNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form',
					],
				],
				'scopy' => true,
			],
			'formHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover',
					],
				],
				'scopy' => true,
			],
			'formNRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form{ border-radius : {{formNRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'formHRadius' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover{ border-radius : {{formHRadius}}; }',
					],
				],
				'scopy' => true,
			],
			'formNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form',
					],
				],
				'scopy' => true,
			],
			'formHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-form:hover',
					],
				],
				'scopy' => true,
			],
			/* Background Option Start */
			
			/* Scroll Bar Start */
			'sAreaScBarBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar',
					],
				],
				'scopy' => true,
			],
			'sAreaScBarWid' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar{ width : {{sAreaScBarWid}}; }',
					],
				],
				'scopy' => true,
			],
			'sAreaThumbBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'sAreaThumbBradius' => [
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
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-thumb{ border-radius : {{sAreaThumbBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'sAreaThumbshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-thumb',
					],
				],
				'scopy' => true,
			],
			'sAreaTrackBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
			'sAreaTrackBradius' => [
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
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-track{ border-radius : {{sAreaTrackBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'sAreaTrackshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'scrollBar', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-search-scrollbar::-webkit-scrollbar-track',
					],
				],
				'scopy' => true,
			],
			/* Scroll Bar End */
			
			/* Error Option Start */
			'errorTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-error',
					],
				],
				'scopy' => true,
			],
			'errorPadding' => [
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
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error{ padding: {{errorPadding}}; }',
					],
				],
				'scopy' => true,
			],
			'errorNColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error{ color: {{errorNColor}}; }',
					],
				],
				'scopy' => true,
			],
			'errorHColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error:hover{ color: {{errorHColor}}; }',
					],
				],
				'scopy' => true,
			],
			'errorNBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error',
					],
				],
				'scopy' => true,
			],
			'errorHBG' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error:hover',
					],
				],
				'scopy' => true,
			],
			'errorNBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error',
					],
				],
				'scopy' => true,
			],
			'errorHBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error:hover',
					],
				],
				'scopy' => true,
			],
			'errorNBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error',
					],
				],
				'scopy' => true,
			],
			'errorHBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-search-area .tpgb-search-error:hover',
					],
				],
				'scopy' => true,
			],
			/* Error Option End */
		);
	
	$attributesOptions = array_merge($attributesOptions	, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption);
	
	register_block_type( 'tpgb/tp-search-bar', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_search_bar_render_callback'
    ) );
}
add_action( 'init', 'tpgb_search_bar' );

//Get Html For Select Drop Down
function tpgb_search($onLoadAttr = []){
	$new_Post = (!empty($onLoadAttr)) ? $onLoadAttr : $_POST;

	$searchData=[];	
	if(!empty($onLoadAttr)){
		$searchData = $new_Post['searchData'];	
	}else{
		parse_str($new_Post['searchData'], $searchData);
	
		if(!isset($new_Post['nonce']) || empty($new_Post['nonce']) || ! wp_verify_nonce( $new_Post['nonce'], 'tpgb-searchbar' )){	
			die ('Security checked!');
		}
	}
	
	$style = !empty($new_Post['style']) ? $new_Post['style'] : 'style-1';
	$tempId = !empty($new_Post['tempId']) ? $new_Post['tempId'] : '';
	$styleColumn = !empty($new_Post['styleColumn']) ? $new_Post['styleColumn'] : 'tpgb-col-12 tpgb-col-lg-12 tpgb-col-md-12 tpgb-col-sm-12 tpgb-col-12';
	$DefaultData = !empty($new_Post['DefaultData']) ? $new_Post['DefaultData'] : '';

	$SpecialCTP = (!empty($DefaultData) && !empty($DefaultData['specificCTP'])) ? 1 : 0;
	if( !empty($DefaultData) && !empty($DefaultData['Def_Post']) ){
		$Def_post = $DefaultData['Def_Post'];
	}else if( !empty($DefaultData) && !empty($SpecialCTP) ){
		$Def_post = (!empty($DefaultData) && !empty($DefaultData['ctpType'])) ? $DefaultData['ctpType'] : 'post';
	}else{
		$Def_post = 'any';
	}
	
	$Enable_DefaultStxt=0;
	$PostType='';
	if(!empty($searchData) && !empty($searchData['post_type'])){
		$PostType = sanitize_text_field($searchData['post_type']);
	}else{
		$Enable_DefaultStxt=1;
		$PostType = $Def_post;
	}
	
	// $PostType = (!empty($searchData) && !empty($searchData['post_type'])) ? sanitize_text_field($searchData['post_type']) : $Def_post;
	$postper = !empty($new_Post['postper']) ? intval($new_Post['postper']) : 3;
	
	$GFilter = !empty($new_Post['GFilter']) ? $new_Post['GFilter'] : [];
	$GFSType = !empty($GFilter['GFSType']) ? sanitize_text_field($GFilter['GFSType']) : 'otheroption';

	$ACFEnable = !empty($new_Post['ACFilter']['ACFEnable']) ? $new_Post['ACFilter']['ACFEnable'] : 0;
	$ACF_Key = !empty($new_Post['ACFilter']['ACFkey']) ? $new_Post['ACFilter']['ACFkey'] : '';
	
	if($PostType == 'product' && !class_exists('woocommerce')){
		$response['error'] = 1;
		$response['message'] = 'woocommerce checked!';
		wp_send_json_success($response);
		die();
	}
	$resultSetting = !empty($new_Post['resultSetting']) ? $new_Post['resultSetting'] : [];
	$ResultData = !empty($new_Post['ResultData']) ? $new_Post['ResultData'] : [];
	$Pagestyle = !empty($ResultData['Pagestyle']) ? $ResultData['Pagestyle'] : 'none';
	
	$response = array(
		'error' => false,
		'post_count' => 0,
		'message' => '',
		'posts' => null,
	);

	if(isset($searchData['taxonomy']) && !empty($searchData['taxonomy'])){
		$taxonomy_name = $searchData['taxonomy'];
		$taxonomy = get_taxonomy( $taxonomy_name );
		if ( $taxonomy && ! empty( $taxonomy->object_type ) ) {
			$post_types = $taxonomy->object_type;
			$PostType = $post_types[0];
		} else {
			$PostType = 'any';
		}
	}
	
	$query_args = array(
		'post_type' => $PostType,
		'suppress_filters' => false,
		'ignore_sticky_posts' => true,
		'orderby' => 'relevance',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);
	
	$seaposts=[];
	if(!empty($new_Post['text'])){
		global $wpdb;
		$sqlContent = $new_Post['text'];
		if( !empty($ACFEnable) || (!empty($GFilter['GFEnable']) )){
			$AllData=$GTitle=$GExcerpt=$Gcontent=$GName=$PCat=$PTag=$ACFData=[];
			
			$Result = ($GFSType == 'fullMatch') ? "{$wpdb->esc_like($sqlContent)}" : "%{$wpdb->esc_like($sqlContent)}%";

			$Publish = $wpdb->prepare(" AND {$wpdb->posts}.post_status= %s ", 'publish');
			
			$DType='';
			if(!empty($PostType)){
				if(!empty($Enable_DefaultStxt)){
					$DType='';
				}else{
					$DType = $wpdb->prepare(" AND post_type = %s", $PostType);
				}
			}else{
				$DType = " AND post_type IN ('post','page','product')";
			}
			
			if(!empty($GFilter['GFEnable'])){
				if(!empty($GFilter['GFTitle'])){ 
					$GTitle = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_title LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFExcerpt'])){
					$GExcerpt = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_excerpt LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFContent'])){
					$Gcontent = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_content LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFName'])){
					$GName = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_name LIKE %s {$Publish} {$DType}", $Result));
				}
				if(!empty($GFilter['GFCategory']) && $PostType != 'page'){
					$CatTaxonomy='';
					$CatPT=$PostType;
					$CatType='category_name';
					if($PostType == 'post'){
						$CatTaxonomy = 'category';
					}else if($PostType == 'product'){
						$CatTaxonomy=$CatType='product_cat';
					}else{
						$CatTaxonomy = 'any';
						$CatPT = 'post';
					}
					
					$PCat = query_posts( array(
						'taxonomy' 		=> $CatTaxonomy,
						'post_type'		=> $CatPT,
						$CatType	 	=> $sqlContent,
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'orderby' 		=> 'name',
						'order'			=> 'ASC',
						'hide_empty'	=> 0,				
					) );
				}
				
				if(!empty($GFilter['GFTags']) && $PostType != 'page') {
					$TagTaxonomy=$TagType='';
					$TagPT=$PostType;
					if($PostType == 'post'){
						$TagTaxonomy = 'post_tag';
						$TagType = 'tag';
					}else if($PostType == 'product'){
						$TagTaxonomy = 'product_tag';
						$TagType = 'product_tag';
					}
					
					$PTag = query_posts( array(
						'taxonomy' 		=> $TagTaxonomy,
						'post_type'		=> $TagPT,
						$TagType		=> $sqlContent,
						'post_status' 	=> 'publish',
						'posts_per_page' => -1,
						'orderby' 		=> 'name',
						'order'			=> 'ASC',
						'hide_empty' 	=> 0,
					) );
				}
			}
			
			if( class_exists('acf') && !empty($ACFEnable) && !empty($ACF_Key) ){
				$ACFPrepare = $wpdb->prepare("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID {$Publish}");
				$AcfPost = $wpdb->get_results($ACFPrepare);
				foreach ($AcfPost as $key => $one) {
					$PostID = !empty($one->ID) ? $one->ID : '';
					$GetData = acf_get_field($ACF_Key)['key'];
					$ACFone = get_field($GetData, $PostID);
					if(!empty($ACFone)){
						$ACFArray = explode("|", $ACFone);
						foreach ($ACFArray as $two) {
							$ACFtxt = ltrim(rtrim($two));
							if( ($GFSType == 'otheroption') && str_contains(strtolower($ACFtxt), strtolower($sqlContent)) ){
								$ACFData[] = $one->ID;
							}else if( ($GFSType == 'fullMatch') && (strtolower($ACFtxt) == strtolower($sqlContent)) ){
								$ACFData[] = $one->ID;
							}
						}
					}
				}
			}
			
			array_push( $AllData, $GTitle, $GExcerpt, $Gcontent, $GName, $PCat, $PTag, $ACFData );
			
			$TmpPostID=[];
			if(!empty($AllData)){
				foreach($AllData as $one) {
					if(!empty($one)){
						foreach($one as $two){
							if( !empty($GFilter['GFEnable']) && !empty($two->ID)){
								$TmpPostID[] = $two->ID;
							}else if( !empty($ACFEnable) && !empty($two) ){
								$TmpPostID[] = $two;
							}
						}
					}
				}
			}
			
			if( !empty($TmpPostID) ){
				$query_args['post__in'] = $TmpPostID;
			}else{
				$query_args['post__in'] = [0];
			}
		}else{
			$query_args['s'] = $sqlContent;
			
		}
	}
	$tax_query = [];
	if($PostType == 'product'){
		$tax_query = ['relation' => 'AND',
			[
				'taxonomy' => 'product_visibility',
				'field' => 'name',
				'terms' => ['exclude-from-search', 'exclude-from-catalog'],
				'operator' => 'NOT IN',
			],
		];
	}
	if(!empty($searchData['taxonomy']) && !empty($searchData['cat']) && $searchData['cat']!='all' ){
		$tax_query = [
			[
				'taxonomy' => $searchData['taxonomy'],
				'field' => 'term_id',
				'terms' => $searchData['cat'] 
			]
		];
	}

	if(!empty($DefaultData['includeTerms']) && !empty($DefaultData['taxonomySlug'])){
		$cat_arr = [];
		if (is_array($DefaultData['includeTerms']) || is_object($DefaultData['includeTerms'])) {
			foreach ($DefaultData['includeTerms'] as $inValue) {
				$cat_arr[] = $inValue['value'];
			}
		}

		if(!empty($cat_arr)){
			$tax_query[] = array(
				'taxonomy' => $DefaultData['taxonomySlug'],
				'field' => 'term_id',
				'terms' => $cat_arr,
				'include_children' => true, 
				'operator' => 'IN'
			);
		}
	}
	if(!empty($DefaultData['excludeTerms']) && !empty($DefaultData['taxonomySlug']) ){
		$excat_arr = [];
		if (is_array($DefaultData['excludeTerms']) || is_object($DefaultData['excludeTerms'])) {
			foreach ($DefaultData['excludeTerms'] as $inValue) {
				$excat_arr[] = $inValue['value'];
			}
		}
		
		if(!empty($excat_arr)){
			$tax_query[] = array(
				'taxonomy' => $DefaultData['taxonomySlug'],
				'field' => 'term_id',
				'terms' => $excat_arr,
				'include_children' => true, 
				'operator' => 'NOT IN'
			);
		}
	}

	if(!empty($tax_query) ){
		$query_args['tax_query'] = [ 'relation' => 'AND', $tax_query ];
	}
	if($Pagestyle !== 'none'){
		$offset = !empty($new_Post['offset']) ? $new_Post['offset'] : '';
		$loadmore_Post = !empty($new_Post['loadNumpost']) ? $new_Post['loadNumpost'] : $postper;
		
		$query_args['offset'] = $offset;
		if($Pagestyle == 'pagination'){
			$query_args['posts_per_page'] = $postper;
		}else if($Pagestyle == 'load_more'){
			$query_args['posts_per_page'] = $loadmore_Post;
		}else if($Pagestyle == 'lazy_load'){
			$query_args['posts_per_page'] = $loadmore_Post;
		}
		
	}else{
		$query_args['posts_per_page'] = $postper;
	}
	
	$seaposts = new WP_Query($query_args);
	
	$totalFind = $seaposts->found_posts;
	
	$response['posts']  = array();
	$response['limit_query'] = $postper;
	
	$response['columns']  = ceil($totalFind / $postper);
	$response['post_count']  = $totalFind;
	$response['total_count']  = $totalFind;
	
	if($Pagestyle == 'pagination' && $response['limit_query'] < $response['post_count']){
		$response['pagination'] = '';
		$Pcounter = !empty($ResultData['Pcounter']) ? $ResultData['Pcounter'] : 0;
		$PClimit = !empty($ResultData['PClimit']) ? $ResultData['PClimit'] : 5;
		$PNavigation = !empty($ResultData['PNavigation']) ? $ResultData['PNavigation'] : 0;
		$PNxttxt = !empty($ResultData['PNxttxt']) ? $ResultData['PNxttxt'] : '';
		$PPrevtxt = !empty($ResultData['PPrevtxt']) ? $ResultData['PPrevtxt'] : '';
		$PNxticon = !empty($ResultData['PNxticon']) ? $ResultData['PNxticon'] : '';
		$PNxticonType = !empty($ResultData['PNxticonType']) ? $ResultData['PNxticonType'] : 'none';
		$PPrevicon = !empty($ResultData['PPrevicon']) ? $ResultData['PPrevicon'] : '';
		$PPreviconType = !empty($ResultData['PPreviconType']) ? $ResultData['PPreviconType'] : 'none';
		$Pstyle = !empty($ResultData['Pstyle']) ? $ResultData['Pstyle'] : 'center';
		$next=$prev=$BtnNum='';
		if(!empty($PNavigation)){
			$next .= '<button class="tpgb-pagelink prev" data-prev="1" >';
				$next .= ($PPreviconType=='fontAwesome' && !empty($PPrevicon)) ? '<span class="tpgb-prev-icon"> <i class="'.esc_attr($PPrevicon).' tpgb-title-icon"></i> </span>' : '';
				$next .= (!empty($PPrevtxt)) ? '<span class="tpgb-prev-txt">'.esc_html($PPrevtxt).'</span>' :'';
			$next .= '</button>';
		}
		if(!empty($Pcounter)){
			if($response['columns'] <= $PClimit){
				for ($i=0; $i<$PClimit; $i++){
					if($i < $response['columns']){
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tpgb-pagelink tpgb-ajax-page '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}
				}
			}else{
				for ($i=0; $i<$response['columns']; $i++){
					if($i < $PClimit){
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tpgb-pagelink tpgb-ajax-page '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}else{
						$active = (($i+1) == 1) ? 'active' : '';
						$BtnNum .= '<button class="tpgb-pagelink tpgb-ajax-page tp-hide '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
					}
				}
			}
		}else{
			for ($i=0; $i<$response['columns']; $i++){
				$active = (($i+1) == 1) ? 'active' : '';
				$BtnNum .= '<button class="tpgb-pagelink tpgb-ajax-page tp-hide '.esc_attr($active).'" data-page="'.esc_attr($i+1).'" >'.esc_html($i+1).'</button>';
			}
		}
		if(!empty($PNavigation)){
			$prev .= '<button class="tpgb-pagelink next" data-next="1">';
				$prev .= !empty($PNxttxt) ? '<span class="tpgb-next-txt">'.esc_html($PNxttxt).'</span>' : '';
				$prev .= ($PNxticonType=='fontAwesome' && !empty($PNxticon)) ? '<span class="tpgb-next-icon"> <i class="'.esc_attr($PNxticon).' tpgb-title-icon"></i> </span>' : '';
				$prev .= '</button>';
		}
		if($Pstyle == 'after'){
			$response['pagination'] .= $next . $prev . $BtnNum;
		}else if($Pstyle == 'center'){
			$response['pagination'] .= $next . $BtnNum . $prev;
		}else if($Pstyle == 'before'){
			$response['pagination'] .= $BtnNum . $next . $prev;
		}
	}else if($Pagestyle == 'load_more'){
		$BtnTxt = !empty($ResultData['loadbtntxt']) ? $ResultData['loadbtntxt'] : 0;
		$response['loadmore'] = ($totalFind > $postper) ? '<a class="post-load-more" data-page="1" >'.esc_html($BtnTxt).'</a>' : '';
		$LoadPage = !empty($ResultData['loadpage']) ? $ResultData['loadpage'] : 0;
		if(!empty($LoadPage)){
			$PageHtml = '';
			$Pagetxt = !empty($ResultData['loadPagetxt']) ? $ResultData['loadPagetxt'] : '';
			$loadnumber = !empty($ResultData['loadnumber']) ? $ResultData['loadnumber'] : $postper;
			//$Numbcount = ceil($totalFind / $loadnumber);
			if($totalFind == 1){
				$Numbcount = 1;
			}else{
				$Numbcount = ceil( ($totalFind - $postper) / $loadnumber ) + 1;
			}

			$PageHtml .= '<span class="tpgb-page-link" >'.esc_html($Pagetxt).'</span>';
			$PageHtml .= '<button class="tpgb-pagelink tpgb-load-page" data-page="1" ><span class="tpgb-load-number" > 1 </span> / '.esc_html(abs($Numbcount)).' </button>';
			
			$response['loadmore_page'] = $PageHtml;
		}
	}else if($Pagestyle == 'lazy_load'){
		$response['lazymore'] = '<a class="post-lazy-load" data-page="1"><div class="tpgb-spin-ring"><div></div><div></div><div></div><div></div></div></a>';
	}
	
	$ci = 0;
	if($style == 'custom'){
		if(!empty($tempId)){
			if ( $seaposts->have_posts() ) {
				while ($seaposts->have_posts()) {
					ob_start();
					$seaposts->the_post();
					echo '<div class="tpgb-ser-item tpgb-trans-linear '.esc_attr($styleColumn).'">';
						echo Tpgb_Library()->plus_do_block($tempId);
					echo '</div>';
	
					$searchPostOp = ob_get_contents();
					ob_end_clean();
					$response['posts'][$ci] = $searchPostOp;
					$ci++;
				}
			}
		}else{
			$searchReusError = '<div class="tpgb-ser-item tpgb-trans-linear '.esc_attr($styleColumn).'">';
				$searchReusError .= 'You have '.esc_html($totalFind).' result(s) but select reusable block for layout';
			$searchReusError .= '</div>';

			$response['posts'][$ci] = $searchReusError;
		}
	}else{
		foreach ($seaposts->posts as $key => $post){
			$product='';
			if($PostType == 'product'){
				$product = wc_get_product($post->ID);
			}

			$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');

			$postTitle       	= !empty($post) ? $post->post_title : '';
			$postLink        	= !empty($post) ? get_permalink($post) : '';
			$postContent     	= !empty($post) ? $post->post_excerpt : '';
			$postThumb		 	= $url;
			$postType		 	= $PostType;
			$postWo_Price	 	= !empty($product) ? $product->get_price_html() : '';
			$postWo_shortDesc	= !empty($product) ? $product->get_short_description() : '';

			$LinkEnale = ($resultSetting && $resultSetting['ResultlinkOn']) ? $resultSetting['ResultlinkOn'] : '';
			$Resultlinktarget = ($LinkEnale && $resultSetting && $resultSetting['Resultlinktarget']) ? 'target="'.esc_attr($resultSetting['Resultlinktarget']).'"' : '';
			$Resultlink = ($LinkEnale && $postLink) ? 'href="'.esc_url($postLink).'"' : '';

			if(!empty($resultSetting['TxtTitle'])){
				$txtCount = (!empty($resultSetting['textcount'])) ? $resultSetting['textcount'] : 100;
				$txtdot = (!empty($resultSetting['textdots'])) ? $resultSetting['textdots'] : '';
					
				if($resultSetting['texttype'] == "char"){
					$ttlDots = '';
					if(strlen($postTitle) > $txtCount){
						$ttlDots = '...';
					}
					$postTitle = substr($postTitle,0,$txtCount).$ttlDots;
				}else if($resultSetting['texttype'] == "word"){
					$ttlDots = '';
					if(str_word_count($postTitle) > $txtCount){
						$ttlDots = '...';
					}
					$words = explode(" ",$postTitle);
					$postTitle = implode(" ",array_splice($words,0,$txtCount)).$ttlDots;
				}
			}

			if(!empty($resultSetting['Txtcont'])){
				$contcount = (!empty($resultSetting['ContCount'])) ? $resultSetting['ContCount'] : 100;
				$txtdotc = (!empty($resultSetting['ContDots'])) ? $resultSetting['ContDots'] : '';
				if($resultSetting['ContType'] == "char"){
					$cntDots = '';
					if(str_word_count($postContent) > $contcount){
						$cntDots = '...';
					}
					$postContent = substr($postContent,0,$contcount).$cntDots;
				}else if($resultSetting['ContType'] == "word"){
					$cntDots = '';
					if(str_word_count($postContent) > $contcount){
						$cntDots = '...';
					}
					$words = explode(" ",$postContent);
					$postContent = implode(" ",array_splice($words,0,$contcount)).$cntDots;
				}
			}

			$searchPostOp = '<div class="tpgb-ser-item tpgb-trans-linear '.esc_attr($styleColumn).'">';
				$searchPostOp .= '<a class="tpgb-serpost-link tpgb-trans-easeinout" '.$Resultlink.' '.$Resultlinktarget.' >';
					if(!empty($resultSetting['ONThumb']) && !empty($postThumb)){
						$searchPostOp .= '<div class="tpgb-serpost-thumb">';
							$searchPostOp .= '<img class="tpgb-item-image" src='.esc_url($postThumb).'>';
						$searchPostOp .= '</div>';
					}
					$searchPostOp .= '<div class="tpgb-serpost-wrap">';
						if( (!empty($resultSetting['ONTitle']) && !empty($postTitle)) || (!empty($resultSetting['ONPrice']) && !empty($postWo_Price)) ){
							$searchPostOp .= '<div class="tpgb-serpost-inner-wrap">';
								if(!empty($resultSetting['ONTitle']) && !empty($postTitle)){
									$searchPostOp .= '<div class="tpgb-serpost-title">'.wp_kses_post($postTitle).'</div>';
								}
								if(!empty($resultSetting['ONPrice']) && !empty($postWo_Price)){
									$searchPostOp .= '<div class="tpgb-serpost-price">'.wp_kses_post($postWo_Price).'</div>';
								}
							$searchPostOp .= '</div>';
						}
						if(!empty($resultSetting['ONContent']) && !empty($postContent)){
							$searchPostOp .= '<div class="tpgb-serpost-excerpt">'.wp_kses_post($postContent).'</div>';
						}
						if(!empty($resultSetting['ONShortDesc']) && !empty($postWo_shortDesc)){
							$searchPostOp .= '<div class="tpgb-serpost-shortDesc">'.wp_kses_post($postWo_shortDesc).'</div>';
						}
					$searchPostOp .= '</div>';
				$searchPostOp .= '</a>';
			$searchPostOp .= '</div>';

			$response['posts'][$key] = $searchPostOp;
		}
	}

	if(!empty($onLoadAttr)){
		return $response;
	}else{
		wp_reset_postdata();
		wp_send_json_success($response);
	}
}
add_action('wp_ajax_tpgb_search', 'tpgb_search');
add_action('wp_ajax_nopriv_tpgb_search', 'tpgb_search');

// Dynamic Select Down
function tpgb_search_drop_down($data, $name, $id, $taxo, $repeater, $inputDis){
	$select = '';
	$showCnt = !empty($repeater['showCount']) ? 'yes' : 'no';
	$label = !empty($repeater['fieldLabel']) ? $repeater['fieldLabel'] : '';
	$placeH = !empty($repeater['fieldPlaceH']) ? $repeater['fieldPlaceH'] : '';
	$phAllResult = !empty($repeater['phAllResult']) ? $repeater['phAllResult'] : false;
	$sourceType = !empty($repeater['sourceType']) ? $repeater['sourceType'] : '';
	
	if($taxo != ''){
		$select .= '<input name="taxonomy" type="hidden" value="'.esc_attr($taxo).'">';
	}
	if(!empty($label)){
		$select .= '<label class="tpgb-search-label tpgb-trans-linear">'.esc_html( $label ).'</label>';
	}
	
	$DatName='';
	if($name == 'post'){
		$DatName = 'post_type';
	}else if($name == 'category'){
		$DatName = 'cat';
	}

	$selectLoader = '';
	if(!empty($inputDis)){
		$selectLoader = '<div class="tpgb-ajx-loading"><div class="tpgb-spinner-loader"></div></div><span class="tpgb-close-btn"><i class="fas fa-times-circle" aria-hidden="true"></i></span>';
	}
	$allResId = (!empty($phAllResult) && $sourceType == 'taxonomy') ? 'all' : '';
	
	$select .= '<div class="tpgb-sbar-dropdown">';
		$select .= '<div class="tpgb-select">';
			$select .= '<span class="search-selected-text">'.esc_html($placeH).'</span><span class="tpgb-dd-icon tpgb-trans-easeinout"><i class="fas fa-chevron-down"></i></span>';
			$select .= $selectLoader;
		$select .= '</div>';
		$select .= '<input type="hidden" name="'.esc_attr($DatName).'" id="'.esc_attr($DatName).'" >';
		$select .= '<ul class="tpgb-sbar-dropdown-menu">';
			$select .= '<li id="'.esc_attr($allResId).'" class="tpgb-searchbar-li">'.esc_html($placeH).'</li>';
			foreach($data as $key => $label){
				$LName = !empty($label['name']) ? $label['name'] : '';
				$Lcount = !empty($label['count']) ? $label['count'] : 0;

				$select .= '<li id="'.$key.'" class="tpgb-searchbar-li" >';
					if($showCnt=='yes') {
						$select .= esc_html($LName) .' ('.esc_html($Lcount).')';
					}else {
						$select .= esc_html($LName);
					}
					
				$select .= '</li>';
			}
		$select .= '</ul>';
	$select .= '</div>';
			
	return $select;
}