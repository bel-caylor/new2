<?php
/* Block : Posts Listing
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_tp_post_listing_render_callback( $attributes ) {
	$output = '';
	$query_args = tpgb_post_query($attributes);
	$query = new \WP_Query( $query_args );
	
	$block_id = isset($attributes['block_id']) ? $attributes['block_id'] : '';
	$postType = isset($attributes['postType']) ? $attributes['postType'] : '';
	$style = isset($attributes['style']) ? $attributes['style'] : 'style-1';
	$layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
	$style2Alignment = isset($attributes['style2Alignment']) ? $attributes['style2Alignment'] : 'center';
	$style3Alignment = isset($attributes['style3Alignment']) ? $attributes['style3Alignment'] : 'left';
	$styleLayout = isset($attributes['styleLayout']) ? $attributes['styleLayout'] : 'style-1';
	
	$imageHoverStyle = isset($attributes['imageHoverStyle']) ? 'hover-image-'.$attributes['imageHoverStyle'] : 'hover-image-style-1';
	//Title
	$ShowTitle = !empty($attributes['ShowTitle']) ? 'yes' : '';
	$titleTag = isset($attributes['titleTag']) ? $attributes['titleTag'] : 'h3';
	$titleByLimit = isset($attributes['titleByLimit']) ? $attributes['titleByLimit'] : 'default';
	$titleLimit = isset($attributes['titleLimit']) ? $attributes['titleLimit'] : '';
	
	//Excerpt
	$showExcerpt = !empty($attributes['ShowExcerpt']) ? 'yes' : '';
	$excerptByLimit	= isset($attributes['excerptByLimit']) ? $attributes['excerptByLimit'] : 'default';
	$excerptLimit = isset($attributes['excerptLimit']) ? $attributes['excerptLimit'] : 30;
	
	$showPostMeta	= !empty($attributes['ShowPostMeta']) ? 'yes' : '';
	$postMetaStyle	= isset($attributes['postMetaStyle']) ? $attributes['postMetaStyle'] : 'style-1';
	$ShowDate = !empty($attributes['ShowDate']) ? 'yes' : '';
	$ShowAuthor = !empty($attributes['ShowAuthor']) ? 'yes' : '';
	$authorTxt = !empty($attributes['authorTxt']) ? $attributes['authorTxt'] : '';
	$ShowAuthorImg = !empty($attributes['ShowAuthorImg']) ? 'yes' : '';
	$ShowallFilter = !empty($attributes['ShowallFilter']) ? 'yes' : '';
	$taxonomySlug	= !empty($attributes['taxonomySlug']) ? $attributes['taxonomySlug'] : 'category';

	$ShowButton = !empty($attributes['ShowButton']) ? 'yes' : '';
	$postBtnsty = isset($attributes['postBtnsty']) ? $attributes['postBtnsty'] : 'style-7';
	$postbtntext = isset($attributes['postbtntext']) ? $attributes['postbtntext'] : '';
	$pobtnIconType = isset($attributes['pobtnIconType']) ? $attributes['pobtnIconType'] : '';
	$pobtnIconName = isset($attributes['pobtnIconName']) ? $attributes['pobtnIconName'] : '';
	$btnIconPosi = isset($attributes['btnIconPosi']) ? $attributes['btnIconPosi'] : '';
	$postListing = isset($attributes['postListing']) ? $attributes['postListing'] : '';

	$showPostCategory = !empty($attributes['showPostCategory']) ? 'yes' : '';
	$postCategoryStyle = isset($attributes['postCategoryStyle']) ? $attributes['postCategoryStyle'] : 'style-1';
	$catNo = isset($attributes['catNo']) ? $attributes['catNo'] : '';
	$postCategory = isset($attributes['postCategory']) ? $attributes['postCategory'] : '';
	$postTag = isset($attributes['postTag']) ? $attributes['postTag'] : '';
	$excludeCategory = isset($attributes['excludeCategory']) ? $attributes['excludeCategory'] : '';
	$excludeTag = isset($attributes['excludeTag']) ? $attributes['excludeTag'] : '';
	
	$displayPosts = isset($attributes['displayPosts']) ? $attributes['displayPosts'] : 6;
	$offsetPosts = isset($attributes['offsetPosts']) ? $attributes['offsetPosts'] : 0;
	$orderBy = isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date';
	$order = isset($attributes['order']) ? $attributes['order'] : 'desc';
	$ShowFilter = !empty($attributes['ShowFilter']) ? 'yes' : '';
	$childcategory = !empty($attributes['childcategory']) ? $attributes['childcategory'] : false;
	$postLodop = isset($attributes['postLodop']) ? $attributes['postLodop'] : '';
	$postview = isset($attributes['postview']) ? $attributes['postview'] : '';
	$loadbtnText = isset($attributes['loadbtnText']) ? $attributes['loadbtnText'] : '';
	$loadingtxt = isset($attributes['loadingtxt']) ? $attributes['loadingtxt'] : '';
	$allposttext = isset($attributes['allposttext']) ? $attributes['allposttext'] : '';
	$notFoundText = isset($attributes['notFoundText']) ? $attributes['notFoundText'] : '';
	$filterBy =  (!empty($attributes['filterBy'])) ? $attributes['filterBy'] : 'category';
	$disableAnim = !empty($attributes['disableAnim']) ? $attributes['disableAnim'] : false;

	$includePosts = (!empty($attributes['includePosts'])) ? $attributes['includePosts'] :'';
	$excludePosts = (!empty($attributes['excludePosts'])) ? $attributes['excludePosts'] :'';

	$showDots = (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows = (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows = (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	$customQueryId = (!empty($attributes['customQueryId'])) ? $attributes['customQueryId'] : '';
	$showcateTag = (!empty($attributes['showcateTag'])) ? $attributes['showcateTag'] : '';

	$display_thumbnail = !empty($attributes['DisImgSize']) ? $attributes['DisImgSize'] : true;
    $thumbnail = isset($attributes['ImageSize']) ? $attributes['ImageSize'] : 'full';
	
	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}

	$metrocolumns = isset($attributes['metrocolumns']) ? $attributes['metrocolumns'] : [ 'md' => '3' ] ;
	$metroStyle = isset($attributes['metroStyle']) ? $attributes['metroStyle'] : '';

	//Columns
	$column_class = '';
	if($layout!='carousel' && !empty($attributes['columns']) && is_array($attributes['columns'])){
		$column_class .= ' tpgb-col';
		$column_class .= isset($attributes['columns']['md']) ? " tpgb-col-lg-".$attributes['columns']['md'] : ' tpgb-col-lg-3';
		$column_class .= isset($attributes['columns']['sm']) ? " tpgb-col-md-".$attributes['columns']['sm'] : ' tpgb-col-md-4';
		$column_class .= isset($attributes['columns']['xs']) ? " tpgb-col-sm-".$attributes['columns']['xs'] : ' tpgb-col-sm-6';
		$column_class .= isset($attributes['columns']['xs']) ? " tpgb-col-".$attributes['columns']['xs'] : ' tpgb-col-6';
	}
	
	//Classes
	$list_style		= ($style) ? 'dynamic-'.esc_attr($style) : 'dynamic-style-1';
	
	$list_layout	= '';
	if($layout=='grid' || $layout=='masonry'){
		$list_layout = 'tpgb-isotope';
	}else if($layout=='metro'){
		$list_layout = 'tpgb-metro';
	}else if($layout=='carousel'){
		$list_layout = 'tpgb-carousel splide';
	}else{
		$list_layout = 'tpgb-isotope';
	}
	
	$styleLayoutclass ='';
	if(($style=='style-2' || $style=='style-3') && $styleLayout){
		$styleLayoutclass .= 'layout-'.$styleLayout;
	}
	if($style=='style-3' && $style3Alignment){
		$styleLayoutclass .= ' content-align-'.$style3Alignment;
	}
	
	//Carousel Options
	$carousel_settings = '';
	$Sliderclass = '';
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
	
	$classattr = '';
	$classattr .= ' tpgb-block-'.$block_id;
	$classattr .= ' '.$list_style;
	$classattr .= ' '.$list_layout;
	$classattr .= ' '.$styleLayoutclass;

	if(!empty($ShowFilter) && $ShowFilter == 'yes' && $postListing == 'page_listing'){
		$classattr .= ' tpgb-filters';
	}
	if(!empty($childcategory)){
		$classattr .= ' tpgb-child-filter';
	}
	
	if($query->found_posts !=''){
		$total_posts=$query->found_posts;
		$post_offset = (isset($offsetPosts)) ? (int)$offsetPosts : 0;
		$display_posts = (isset($displayPosts)) ? (int)$displayPosts : 0;
		$offset_posts= intval($display_posts + $post_offset);
		$total_posts= intval($total_posts - $offset_posts);	

		if($total_posts!=0 && $postview!=0){
			$load_page= ceil($total_posts/$postview);	
		}else{
			$load_page=1;
		}
		$load_page=$load_page+1;
	}else{
		$load_page=1;
	}
	
	//Set Category Array
	if ( '' !== $postCategory  ) {
		if ( is_string($postCategory )) {
			$category = array();
			$postCategory = json_decode($postCategory);
			if (is_array($postCategory) || is_object($postCategory)) {
				foreach ($postCategory as $value) {
					$category[] = $value->value;
				}
			}
		}
	}
	
	//Set Category Array
	if ( '' !== $postTag  ) {
		if ( is_string($postTag )) {
			$post_Tag = array();
			$postTag = json_decode($postTag);
			if (is_array($postTag) || is_object($postTag)) {
				foreach ($postTag as $value) {
					$post_Tag[] = $value->value;
				}
			}
		}
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

	//data attr For Post Load More & Lazy load
	$postattr =[
		'dyload' => 'postListing',
		'page' => 1,
		'post_type' => $postType,
		'texonomy_category' => $taxonomySlug,
		'style' => $style,
		'display_post' => $displayPosts,
		'styleLayout' => $styleLayout,
		'style2Alignment' => $style2Alignment,
		'style3Alignment' => $style3Alignment,
		'desktop_column' => ( $layout !== 'metro' ) ? $attributes['columns']['md'] : '' ,
		'tablet_column' => ( $layout !== 'metro' ) ? $attributes['columns']['sm'] : '',
		'mobile_column' => ( $layout !== 'metro' ) ? $attributes['columns']['xs'] : '',
		'metro_column' => $metrocolumns,
		'metro_style' => $metroStyle,
		'display_title' => $ShowTitle,
		'titletag' => $titleTag,
		'order_by' => $orderBy,
		'post_order' => $order,
		'filter_category' => $ShowFilter,
		'display_post_meta' => $showPostMeta,
		'display_excerpt' => $showExcerpt,
		'meta_style' => $postMetaStyle,
		'excerptByLimit' => $excerptByLimit,
		'excerptLimit' => $excerptLimit,
		'display_catagory' => $showPostCategory,
		'catNo' => $catNo,
		'post_category_style' => $postCategoryStyle,
		'display_title_by' => $titleByLimit,
		'display_title_limit' => $titleLimit,
		'showdate' => $ShowDate,
		'showauthor' => $ShowAuthor,
		'ShowAuthorImg' => $ShowAuthorImg,
		'displaybuttton' => $ShowButton,
		'postbtntext' => $postbtntext,
		'buttonstyle' => $postBtnsty,
		'pobtnIconType' => $pobtnIconType,
		'pobtnIconName' => $pobtnIconName,
		'btnIconPosi' => $btnIconPosi,
		'imageHoverStyle' => $imageHoverStyle,
		'category' => $category,
		'postTag' => ($postType == 'post') ? $post_Tag : '',
		'includePosts' => $includePosts,
		'excludePosts' => $excludePosts,
		'display_thumbnail' => $display_thumbnail,
		'thumbnail' => $thumbnail,
		'type' => '',
		'authorTxt' => $authorTxt,
		'blockTemplate' => !empty($attributes['blockTemplate']) ? $attributes['blockTemplate'] : '',
		'tpgb_nonce' => wp_create_nonce("theplus-addons-block"),
		'searchTxt' =>  get_search_query(),
		'customQueryId' => $customQueryId,
    	'showcateTag' => $showcateTag
	];
	$postattr = Tpgbp_Pro_Blocks_Helper::tpgb_simple_decrypt( json_encode($postattr), 'ey');
	
	$dypostAttr = [
		'offset_posts' => $offsetPosts,
		'page' => 1,
		'total_page' => $load_page,
		'load_more' => $postview,
		'display_post' => $displayPosts,
		'load_class' => $block_id,
		'loadingtxt' => $loadingtxt,
		'loaded_posts' => $allposttext,
		'layout' => $layout,
		'disableAnim' => $disableAnim,
	];
	$dypostAttr = json_encode($dypostAttr);
	
	$serchAttr = '';
	if($postListing == 'search_list'){
		$serchAttr = 'data-searchAttr= \'' . $postattr . '\' ';
	}

	//Disable Animation Isotop Intially
	if(isset($disableAnim) && !empty($disableAnim)){
		$serchAttr = 'data-anim="no"';
	}

	$ji=1;$col=$tabCol=$moCol='';
	if ( ! $query->have_posts() ) {
		$output .='<div class="tpgb-no-post-list tpgb-no-posts-found">'.esc_html($notFoundText).'</div>';
	}else{
		$output .= '<div id="'.esc_attr($block_id).'" class="tpgb-post-listing '.esc_attr($blockClass).' '.esc_attr($classattr).' '.esc_attr($Sliderclass).' '.esc_attr($equalHclass).' tpgb-relative-block " data-id="'.esc_attr($block_id).'" data-style="'.esc_attr($list_style).'"  data-layout="'.esc_attr($layout).'" data-splide=\'' . json_encode($carousel_settings) . '\'  data-connection="tpgb_search"  '.( $layout == 'metro' ? $metroAttr : '' ).' '.$equalHeightAtt.' >';
			if(!empty($ShowFilter) && $ShowFilter == 'yes' && $layout != 'carousel' && $postListing == 'page_listing'){
				$output .= tpgb_filter_category($attributes);
			}
			if( $layout == 'carousel' && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
				$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
			}
			$output .= '<div id="tpgb_list" class="post-loop-inner '.($layout == 'carousel' ? 'splide__track' : 'tpgb-row').'" '.$serchAttr.' >';
				if($layout == 'carousel'){
					$output .= '<div class="splide__list">';
				}
				while ( $query->have_posts() ) {
					
					$query->the_post();
					$post = $query->post;
					
					// Metro class Layout
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
					//Get Category class
					$category='';
					if(!empty($ShowFilter) && $ShowFilter == 'yes' && $postListing == 'page_listing' ){	
						if($postType=='post'){	
							if($filterBy == 'category'){
								$terms = get_the_terms( $query->ID,'category');
							}else if($filterBy == 'tag'){
								$terms = get_the_terms( $query->ID,'post_tag');
							}
						}else{
							$terms = get_the_terms( $query->ID,$taxonomySlug);
						}
						if ( $terms != null ){
							foreach( $terms as $term ) {
								$category .=' '.esc_attr($term->slug).' ';
								unset($term);
							}
						}
					}
					$output .= '<div class="grid-item '.( $layout=='carousel' ? 'splide__slide' : ( $layout !='metro' ? esc_attr($column_class) : '')).' '.esc_attr($category).' '.( $layout=='metro' ? ' tpgb-metro-'.esc_attr($col).' '.( !empty($tabCol) ? ' tpgb-tab-metro-'.esc_attr($tabCol).''  : '' ).' '.( !empty($moCol) ? ' tpgb-mobile-metro-'.esc_attr($moCol).''  : '' ).' ' : '' ).' ">';
						if(!empty($style) && $style!=='custom' ){
							ob_start();
							include TPGBP_PATH. 'includes/blog/blog-'.esc_attr($style).'.php'; 
							$output .= ob_get_contents();
							ob_end_clean();
						}else if($style=='custom' && $attributes['blockTemplate']!=''){
							ob_start();
								echo Tpgb_Library()->plus_do_block($attributes['blockTemplate']);
							$output .= ob_get_contents();
							ob_end_clean();
						}
					$output .= '</div>';

					$ji++;
				}
				if($layout == 'carousel'){
					$output .= '</div>';
				}
			$output .= '</div>';

			if($postLodop=='pagination' && $layout!='carousel'){
				$output .= tpgb_pagination($query->max_num_pages,'2');
			}else if($postLodop=='load_more' && $layout!='carousel'){
				if(!empty($total_posts) && $total_posts>0){
					$output .= '<div class="tpgb-load-more">';
						$output .= '<a class="post-load-more" data-dypost=\'' .esc_attr($dypostAttr). '\' data-post-option=\'' .esc_attr($postattr). '\'>';
							$output .= wp_kses_post($loadbtnText);
						$output .= '</a>';
					$output .= '</div>';
				}
			}else if($postLodop=='lazy_load' && $layout!='carousel'){
				if(!empty($total_posts) && $total_posts>0){
					$output .= '<div class="tpgb-lazy-load">';
						$output .= '<a class="post-lazy-load" data-dypost=\'' .esc_attr($dypostAttr). '\' data-post-option=\'' .esc_attr($postattr). '\'>';
							$output .= '<div class="tpgb-spin-ring"><div></div><div></div><div></div></div>';
						$output .= '</a>';
					$output .= '</div>';
				}
			}
		$output .= "</div>";
	}

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	if( $layout == 'carousel' ){
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		if( !empty($arrowCss) ){
			$output .= $arrowCss;
		}
	}
	wp_reset_postdata();
	
    return $output;
}

/**
 * Render for the server-side
 */
function tpgb_tp_post_listing() {
	$globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
	$globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$sliderOpt = [
		'slideColumns' => [
			'type' => 'object',
			'default' => [ 'md' => 2,'sm' => 2,'xs' => 1 ],
		],
	];
	$carousel_options = array_merge($carousel_options,$sliderOpt);
	
	$attributesOptions = [
			'block_id' => [
                'type' => 'string',
				'default' => '',
			],
			'postListing' => [
				'type' => 'string',
				'default' => 'page_listing',
			],
			'relatedPost' => [
				'type' => 'string',
				'default' => 'category',
			],
			'postType' => [
				'type' => 'string',
				'default' => 'post',
			],
			'style' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'blockTemplate' => [
				'type' => 'string',
				'default' => '',
			],
			'backendVisi' => [
				'type' => 'boolean',
				'default' => false,
			],
			'layout' => [
				'type' => 'string',
				'default' => 'grid',
			],
			'style2Alignment' => [
				'type' => 'string',		
				'default' => 'center', 	 
			],
			'style3Alignment' => [
				'type' => 'string',
				'default' => 'left',
			],
			'styleLayout' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'minHeight' => [
				'type' => 'object',
				'default' => ['md' => 350, 'unit' => 'px'],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-4 .tpgb-post-featured-img{min-height: {{minHeight}};}',
					],
				],
				'scopy' => true,
			],
			
			'postCategory' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'postTag' => [
				'type' => 'string',
        		'default' => '[]',
			],
			'taxonomySlug' => [
				'type' => 'string',
				'default' => '',
			],
			'includePosts' => [
				'type' => 'string',
				'default' => '',
			],
			'excludePosts' => [
				'type' => 'string',
				'default' => '',
			],
			'displayPosts' => [
				'type' => 'string',
				'default' => 6,
			],
			'offsetPosts' => [
				'type' => 'string',
				'default' => 0,
			],
			'orderBy' => [
				'type' => 'string',
				'default' => 'date',
			],
			'order' => [
				'type' => 'string',
				'default' => 'desc',
			],
			'customQueryId' => [
				'type' => 'string',
				'default' => '',
			],
			'columns' => [
				'type' => 'object',
				'default' => [ 'md' => 6,'sm' => 6,'xs' => 12 ],
			],
			'metrocolumns' => [
				'type' => 'object',
				'default' => [ 'md' => 3,'sm' => 3,'xs' => 3 ],
			],
			'metroStyle' => [
				'type' => 'object',
				'default' => [ 'md' => 'style-1','sm' => 'style-1','xs' => 'style-1' ],
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
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .grid-item{padding: {{columnSpace}};}',
					],
				],
			],
			'ShowFilter' => [
				'type' => 'boolean',
				'default' => false,
			],
			'ShowallFilter' => [
				'type' => 'boolean',
				'default' => true,
			],
			'filterStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'filterHvrStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'TextCat' => [
				'type' => 'string',
				'default' => 'All',
			],
			'CatName' => [
				'type' => 'string',
				'default' => 'Filters',
			],
			'catfilterId' => [
				'type' => 'string',
				'default' => '',
			],
			'filterBy' => [
				'type' => 'string',
				'default' => 'category',
			],
			'filterAlignment' => [
				'type' => 'object',
				'default' => '',
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-filter-data{ text-align: {{filterAlignment}}; }',
					],
				],
	
			], 
			'childcategory' => [
				'type' => 'boolean',
				'default' => false,
			],
			'ShowTitle' => [
				'type' => 'boolean',
				'default' => true,
			],
			
			'titleTag' => [
				'type'=> 'string',
				'default'=> 'h3',
			],
			'titleByLimit' => [
				'type' => 'string',
				'default' => 'default',
			],
			'titleLimit' => [
				'type' => 'string',
				'default' => 30,
			],
			'Showdot' => [
				'type' => 'boolean',
				'default' => false,
			],
			'titleTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => 20, 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowTitle', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-title a',
					],
				],
				'scopy' => true,
			],
			
			'titleNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowTitle', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-title a{color: {{titleNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'titleHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowTitle', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .tpgb-post-title a{color: {{titleHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			
			'ShowExcerpt' => [
				'type' => 'boolean',
				'default' => false,
			],
			
			'excerptByLimit' => [
				'type' => 'string',
				'default' => 'default',
			],
			'excerptLimit' => [
				'type' => 'string',
				'default' => 30,
			],
			'excerptTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
					'size' => [ 'md' => 14, 'unit' => 'px' ],
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowExcerpt', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-excerpt,{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-excerpt p',
					],
				],
				'scopy' => true,
			],
			
			'excerptNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowExcerpt', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-excerpt,{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-excerpt p{color: {{excerptNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'excerptHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowExcerpt', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .tpgb-post-excerpt,{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .tpgb-post-excerpt p{color: {{excerptHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'ShowPostMeta' => [
				'type' => 'boolean',
				'default' => true,
			],
			'ShowDate' => [
				'type' => 'boolean',
				'default' => true,
			],
			'ShowAuthor' => [
				'type' => 'boolean',
				'default' => true,
			],
			'authorTxt' => [
				'type' => 'string',
				'default' => 'By ',
			],
			'ShowAuthorImg' => [
				'type' => 'boolean',
				'default' => true,
			],
			'postMetaStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'postMetaTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowPostMeta', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info > span,{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info > span > a,{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info .post-author-date > a',
					],
				],
				'scopy' => true,
			],
			'postMetaNormalColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowPostMeta', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info > span,{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info > span > a,{{PLUS_WRAP}}.tpgb-post-listing .post-meta-info .post-author-date > a{color: {{postMetaNormalColor}};}',
					],
				],
				'scopy' => true,
			],
			'postMetaHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowPostMeta', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .post-meta-info > span,{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .post-meta-info > span > a,{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .post-meta-info .post-author-date > a{color: {{postMetaHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'DisImgSize' => [
				'type' => 'boolean',
				'default' => false,
			],
			'ImageSize' => [
				'type' => 'string',
				'default' => 'full',
			],
			'postLodop' => [
				'type' => 'string',
				'default' => 'none',
			],
			'postview' => [
				'type'=> 'string',
				'default'=> 3,
			],
			'loadbtnText' => [
				'type' => 'string',
				'default' => 'Load More',
			],
			'loadingtxt' => [
				'type' => 'string',
				'default' => 'Loading...',
			],
			'allposttext' => [
				'type' => 'string',
				'default' => 'All Done',
			],
			'disableAnim' => [
				'type' => 'boolean',
				'default' => false,
			],
			'notFoundText' => [
				'type' => 'string',
				'default' => 'No Posts found',
			],

			'showPostCategory' => [
				'type' => 'boolean',
				'default' => false,
			],
			'showcateTag' => [
				'type' => 'string',
				'default' => 'category',
			],
			'catNo' => [
				'type' => 'string',
				'default' => '',
			],
			'postCategoryStyle' => [
				'type' => 'string',
				'default' => 'style-1',
			],
			'postCategoryTypo' => [
				'type' => 'object',
				'default' => (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a',
					],
				],
				'scopy' => true,
			],
			'catpadding' => [
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
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a{ padding : {{catpadding}}; }',
					],
				],
			],
			'catbetSpa' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a{ margin-left : {{catbetSpa}}; } {{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a:first-child{ margin-left: 0 ; } {{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a:last-child{ margin-right: 0 ; }',
					],
				],

			],
			'postCategoryColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a{color: {{postCategoryColor}};}',
					],
				],
				'scopy' => true,
			],
			'postCategoryHoverColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true]
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category > a:hover{color: {{postCategoryHoverColor}};}',
					],
				],
				'scopy' => true,
			],
			'catBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a',
					],
				],
				'scopy' => true,
			],
			'catBorderHover' => [
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
					],
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a:hover',
					],
				],
				'scopy' => true,
			],
			'cat2BorderHover' => [
				'type' => 'string',
				'default' => '' ,
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}} .tpgb-post-category.cat-style-2 > a:before{ background : {{cat2BorderHover}} }',
					],
				],
				'scopy' => true,
			],
			'catRadius' => [
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
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a{border-radius: {{catRadius}};}',
					],
				],
				'scopy' => true,
			],
			'catRadiusHover' => [
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
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a:hover{border-radius: {{catRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'catBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a',
					],
				],
				'scopy' => true,
			],
			'catBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a:hover',
					],
				],
				'scopy' => true,
			],
			'catBoxShadow' => [
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
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a',
					],
				],
				'scopy' => true,
			],
			'catBoxShadowHover' => [
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
							(object) ['key' => 'showPostCategory', 'relation' => '==', 'value' => true],
							(object) ['key' => 'postCategoryStyle', 'relation' => '==', 'value' => 'style-1']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .tpgb-post-category.cat-style-1 > a:hover',
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a',
					],
				],
				'scopy' => true,
			],
			'InPadding' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter  .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count),
						{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),
						{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,
						{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,
						{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-3 .tpgb-filter-list a,
						{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
					],
				],
				'scopy' => true,
			],
			'FCMargin' => [
				'type' => 'object',
				'default' => (object) [	
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter  .tpgb-categories .tpgb-filter-list{margin:{{FCMargin}};}',
					],
				],
				'scopy' => true,
			],
			'FCNcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCHBcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a.active::after,{{PLUS_WRAP}} .tpgb-category-filter .hover-style-1 .tpgb-filter-list a:hover::after{background:{{FCHBcr}};}',
					],
				],
				'scopy' => true,
			],
			'FCHcr' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:hover,
						{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:focus,
						{{PLUS_WRAP}} .tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a.active,
						{{PLUS_WRAP}} .tpgb-category-filter .hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{color:{{FCHcr}};}',
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => ['style-2','style-4']]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',
	
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => ['style-2','style-4']]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',
	
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count){border-radius:{{FCBgRs}};}',
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
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
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
					],
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
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
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count',
					],
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],
			'FcBCrHs' => [	
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
										(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-4']],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
					],
				],
				'scopy' => true,
			],	
			'FCBoxSd' => [
				'type' => 'object',
				'default' =>  (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}} .tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
					],
				],
				'scopy' => true,
			],
			'contentLeftSpace' => [
				'type' => 'object',
				'default' => [ 'md' => 10, 'unit' => 'px' ],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3.content-align-left .tpgb-content-bottom, {{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3.content-align-left-right .grid-item:nth-child(odd) .tpgb-content-bottom{padding-left: {{contentLeftSpace}};}
						{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3.content-align-right .tpgb-content-bottom, {{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3.content-align-left-right .grid-item:nth-child(even) .tpgb-content-bottom{padding-right: {{contentLeftSpace}};}',
					],
				],
				'scopy' => true,
			],
			'contentBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-1 .dynamic-list-content .tpgb-content-bottom, 
										{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .dynamic-list-content .tpgb-content-bottom,
										{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .dynamic-list-content .tpgb-content-bottom',
					],
				],
				'scopy' => true,
			],
			'contentBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-1 .dynamic-list-content:hover .tpgb-content-bottom, 
										{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .dynamic-list-content:hover .tpgb-content-bottom,
										{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .dynamic-list-content:hover .tpgb-content-bottom',
					],
				],
				'scopy' => true,
			],
			'imageHoverStyle' => [
				'type' => 'string',
				'default' => 'style-1',
				'scopy' => true,
			],
			'imageOverlayBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content .tpgb-post-featured-img > a:before',
					],
				],
				'scopy' => true,
			],
			'imageOverlayBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover .tpgb-post-featured-img > a:before',
					],
				],
				'scopy' => true,
			],
			'imgRadius' => [
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
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .tpgb-post-featured-img{border-radius: {{imgRadius}};}',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .tpgb-post-featured-img{border-radius: {{imgRadius}};}',
					],
				],
				'scopy' => true,
			],
			'imgRadiusHover' => [
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
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .dynamic-list-content:hover .tpgb-post-featured-img{border-radius: {{imgRadiusHover}};}',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .dynamic-list-content:hover .tpgb-post-featured-img{border-radius: {{imgRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'imgBoxShadow' => [
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
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .tpgb-post-featured-img',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .tpgb-post-featured-img',
					],
				],
				'scopy' => true,
			],
			'imgBoxShadowHover' => [
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
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-3 .dynamic-list-content:hover .tpgb-post-featured-img',
					],
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .dynamic-list-content:hover .tpgb-post-featured-img',
					],
				],
				'scopy' => true,
			],
			'imgHeight' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']
						],
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing.dynamic-style-2 .dynamic-list-content .tpgb-post-featured-img img{min-height : {{imgHeight}}; max-height : {{imgHeight}}; }',
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content{padding: {{boxPadding}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content',
					],
				],
				'scopy' => true,
			],
			'boxBorderHover' => [
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
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover',
					],
				],
				'scopy' => true,
			],
			
			'boxBorderRadius' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content{border-radius: {{boxBorderRadius}};}',
					],
				],
				'scopy' => true,
			],
			'boxBorderRadiusHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover{border-radius: {{boxBorderRadiusHover}};}',
					],
				],
				'scopy' => true,
			],
			'boxBg' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content',
					],
				],
				'scopy' => true,
			],
			'boxBgHover' => [
				'type' => 'object',
				'default' => (object) [
					'bgType' => 'color',
					'bgDefaultColor' => '',
					'bgGradient' => (object) [
						"direction" => 90,
					],
				],
				'style' => [
					(object) [
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadow' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content',
					],
				],
				'scopy' => true,
			],
			'boxBoxShadowHover' => [
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
						'selector' => '{{PLUS_WRAP}}.tpgb-post-listing .dynamic-list-content:hover',
					],
				],
				'scopy' => true,
			],
			

			'ShowButton' => [
				'type' => 'boolean',
				'default' => false,
			],
			'postBtnsty' => [
				'type' => 'string',
				'default' => 'style-7',
			],
			'btnAlign' => [
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button{ text-align: {{btnAlign}}; }',
					],
				]
			],
			
			'postbtntext' => [
				'type' => 'string',
				'default' => 'Read More',
			],
			'pobtnIconType' => [
				'type' => 'string',
				'default' => 'icon',
			],
			'pobtnIconName' => [
				'type' => 'string',
				'default' => 'fa fa-angle-right',
			],
			'btnIconPosi' => [
				'type' => 'string',
				'default' => 'after',
				'scopy' => true,
			],
			'pagitypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-pagination a,{{PLUS_WRAP}} .tpgb-pagination span',
					],
				],
				'scopy' => true,
			],
			'pagiColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-pagination a,{{PLUS_WRAP}} .tpgb-pagination span{color : {{pagiColor}}; }',
					],
				],
				'scopy' => true,
			],
			'pagihvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'pagination' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-pagination a:hover,{{PLUS_WRAP}} .tpgb-pagination a:focus,{{PLUS_WRAP}} .tpgb-pagination span.current{color : {{pagihvrColor}}; border-bottom-color: {{pagihvrColor}} }',
					],
				],
				'scopy' => true,
			],
			'btnTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'btncolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{color : {{btncolor}}; }',
					],
				],
				'scopy' => true,
			],
			'btnhvrcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover{color : {{btnhvrcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'btnBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'btnHvrBgtype' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover',
					],
				],
				'scopy' => true,
			],
			'btnBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more',
					],
				],
				'scopy' => true,
			],
			'btnhvrBorder' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover',
					],
				],
				'scopy' => true,
			],
			'btnBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{border-radius : {{btnBradius}} }',
					],
				],
				'scopy' => true,
			],
			'btnhvrBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more:hover{border-radius : {{btnhvrBradius}} }',
					],
				],
				'scopy' => true,
			],
			'allTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .tpgb-post-loaded',
					],
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .tpgb-post-loaded',
					],
				],
				'scopy' => true,
			],
			'allcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-load-more .tpgb-post-loaded{color : {{allcolor}}; }',
					],
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load' ]],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .tpgb-post-loaded{color : {{allcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'spinSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .tpgb-spin-ring div{ width: {{spinSize}}px; height:{{spinSize}}px; }',
					],
				],
				'scopy' => true,
			],
			'spinBSize' => [
				'type' => 'object',
				'default' => [ 
					'md' => '',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .tpgb-spin-ring div{ border-width: {{spinBSize}}px; }',
					],
				],
				'scopy' => true,
			],
			'spinColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'lazy_load']],
						'selector' => '{{PLUS_WRAP}} .tpgb-lazy-load .tpgb-spin-ring div{ border-color: {{spinColor}} transparent transparent transparent ; }',
					],
				],
				'scopy' => true,
			],
			'butTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'butNcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap{color : {{butNcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'buthvrColor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ]],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button:hover .button-link-wrap{color : {{buthvrColor}}; }',
					],
				],
				'scopy' => true,
			],
			'butbgType' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'butHvrbgType' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button:hover .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'pbutBorder' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'pbutHvrBorder' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button:hover .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'butBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap {border-radius : {{butBradius}} }',
					],
				],
				'scopy' => true,
			],
			'butHvrBradius' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button:hover .button-link-wrap {border-radius : {{butHvrBradius}} }',
					],
				],
				'scopy' => true,
			],
			'butBshadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'butHvrBshadow' => [
				'type'=> 'object',
				'default'=> (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button:hover .button-link-wrap',
					],
				],
				'scopy' => true,
			],
			'butpadding' => [
				'type' => 'object',
				'default' => (object) [ 
					'md' => '',
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [
							(object) ['key' => 'ShowButton', 'relation' => '==', 'value' => true ],
							(object) ['key' => 'postBtnsty', 'relation' => '==', 'value' => 'style-8' ]
						],
						'selector' => '{{PLUS_WRAP}}.dynamic-style-3 .tpgb-adv-button .button-link-wrap { padding : {{butpadding}} }',
					],
				],
				'scopy' => true,
			],
			'childTypo' => [
				'type'=> 'object',
				'default'=> (object) [
					'openTypography' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a',
					],
				],
				'scopy' => true,
			],
			'chInPadding' => [
				'type' => 'object',
				'default' => (object) [
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a{padding: {{chInPadding}};}',
					],
				],
				'scopy' => true,
			],
			'chMargin' => [
				'type' => 'object',
				'default' => (object) [	
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],		
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list {margin:{{chMargin}};}',
					],
				],
				'scopy' => true,
			],
			'childcatAlign' => [
				'type' => 'object',
				'default' => [ 'md' => 'center', 'sm' =>  '', 'xs' =>  '' ],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child {justify-content:{{childcatAlign}};}',
					],
				],
				'scopy' => true,
			],
			'chiCatcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a{color: {{chiCatcolor}};}',
					],
				],
				'scopy' => true,
			],
			'chiCatActcolor' => [
				'type' => 'string',
				'default' => '',
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a:hover,{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a.active{ color : {{chiCatActcolor}}; }',
					],
				],
				'scopy' => true,
			],
			'chiCatBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a',
					],
				],
				'scopy' => true,
			],
			'chiCatActBg' => [
				'type' => 'object',
				'default' => (object) [
					'openBg'=> 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a:hover,{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a.active',
					],
				],
				'scopy' => true,
			],
			'chicatBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a',
					],
				],
				'scopy' => true,
			],
			'chicatActBor' => [
				'type' => 'object',
				'default' => (object) [
					'openBorder' => 0,	
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a:hover,{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a.active',
					],
				],
				'scopy' => true,
			],
			'chidCatShadow' => [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a',
					],
				],
				'scopy' => true,
			],
			'chidCatActShadow'=> [
				'type' => 'object',
				'default' => (object) [
					'openShadow' => 0,
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a:hover,{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a.active',
					],
				],
				'scopy' => true,
			],
			'childBradius' => [
				'type' => 'object',
				'default' => (object) [	
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],			
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a { border-radius:{{childBradius}}; }',
					],
				],
				'scopy' => true,
			],
			'childBHradius' => [
				'type' => 'object',
				'default' => (object) [	
					'md' => [
						"top" => '',
						'bottom' => '',
						'left' => '',
						'right' => '',
					],		
					"unit" => 'px',
				],
				'style' => [
					(object) [
						'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
										(object) ['key' => 'childcategory', 'relation' => '==', 'value' => true]],
						'selector' => '{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a:hover,{{PLUS_WRAP}}.tpgb-child-filter .category-filters-child .tpgb-child-list a.active { border-radius:{{childBradius}}; }',
					],
				],
				'scopy' => true,
			],
		];
	
	$attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);
	
	register_block_type( 'tpgb/tp-post-listing', [
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_tp_post_listing_render_callback'
    ] );
}
add_action( 'init', 'tpgb_tp_post_listing' );


function tpgb_post_query($attr){
	
	$include_posts = ($attr['includePosts']) ? explode(',', $attr['includePosts']) : '';
	$exclude_posts = ($attr['excludePosts']) ? explode(',', $attr['excludePosts']) : '';
	
	$query_args = array(
		'post_type'           => $attr['postType'],
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'posts_per_page'      => ( $attr['displayPosts'] ) ? intval($attr['displayPosts']) : -1,
		'orderby'      =>  ($attr['orderBy']) ? $attr['orderBy'] : 'date',
		'order'      => ($attr['order']) ? $attr['order'] : 'desc',
		'post__not_in'  => $exclude_posts,
		'post__in'   => $include_posts,
	);
	
	global $paged;
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	}elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	}else {
		$paged = 1;
	}
	$query_args['paged'] = $paged;
	
	
	$offset = !empty( $attr['offsetPosts'] ) ? absint( $attr['offsetPosts'] ) : 0;
	if ( $offset  && $attr['postLodop']!='pagination') {
		$query_args['offset'] = $offset;
	}else if($offset && $attr['postLodop']=='pagination'){
		$page = max( 1, $paged );
		$offset = ( $page - 1 ) * intval( $attr['displayPosts'] ) + $offset;
		$query_args['offset'] = $offset;
	}
	
	//Category
	if ( '' !== $attr['postCategory']  ) {
		$cat_arr = array();
		if ( is_string($attr['postCategory'] )) {
			$attr['postCategory'] = json_decode($attr['postCategory']);
			if (is_array($attr['postCategory']) || is_object($attr['postCategory'])) {
				foreach ($attr['postCategory'] as $value) {
					$cat_arr[] = $value->value;
				}
			}
		}
		if($attr['postType'] == 'post'){
			$query_args['category__in'] = $cat_arr;
		}else if(!empty($attr['taxonomySlug']) && !empty($cat_arr)){
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $attr['taxonomySlug'],
					'field' => 'term_id',
					'terms' => $cat_arr,
				)
			);
		}
	}

	//Tag
	if ( '' !== $attr['postTag'] ) {
		$tag_arr = array();
		if ( is_string($attr['postTag'] )) {
			$attr['postTag'] = json_decode($attr['postTag']);
			if (is_array($attr['postTag']) || is_object($attr['postTag'])) {
				foreach ($attr['postTag'] as $value) {
					$tag_arr[] = $value->value;
				}
			}
		}
		if($attr['postType'] == 'post'){
			$query_args['tag__in'] = $tag_arr;
		}
	}

	//Archive Posts
	if(!empty($attr["postListing"]) && $attr["postListing"]=='archive_listing'){
		global $wp_query;
		$query_var = $wp_query->query_vars;
		if(isset($query_var['cat'])){
			$query_args['category__in'] = $query_var['cat'];
		}
		if(isset($query_var[$attr["taxonomySlug"]]) && $attr['postType']!=='post'){
					
			$query_args['tax_query'] = array(						
			  array(		
				'taxonomy' => $attr["taxonomySlug"],		
				'field' => 'slug',		
				'terms' => $query_var[$attr["taxonomySlug"]],		
			  ),		
			);		
		}
		if(isset($query_var['tag_id'])){
			$query_args['tag__in'] = $query_var['tag_id'];
		}
		if(isset($query_var["author"])){
			$query_args['author'] = $query_var["author"];
		}
		if(is_search()){
			$search = get_query_var('s');
			$query_args['s'] = $search;
			$query_args['exact'] = false;
		}
	}

	//Related Posts
	if(!empty($attr["postListing"]) && $attr["postListing"]=='related_post'){
		global $post;
		
		if(isset($post->post_type) && $post->post_type =='post'){
			$tag_slug = 'term_id';
			$tags = wp_get_post_tags($post->ID);
		}else{
			$tag_slug = 'slug';
			$tags = isset($post->ID) ? wp_get_post_terms($post->ID,$attr['taxonomySlug']) : [];
		}
		if ($tags && !empty($attr["postListing"]) && ($attr["relatedPost"]=='both' || $attr["relatedPost"]=='tags')) {	
			$tag_ids = array();
			
			foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->$tag_slug;
			
			$query_args['post__not_in'] = array($post->ID);
			if(isset($post->post_type) && $post->post_type =='post'){
				$query_args['tag__in'] = $tag_ids;
			}else{
				$query_args['tax_query'] = array(						
				  array(		
					'taxonomy' => $attr['taxonomySlug'],		
					'field' => 'slug',		
					'terms' => $tag_ids,		
				  ),		
				);
			}
		}
		if(isset($post->post_type) && $post->post_type =='post'){
			$categories_slug = 'cat_ID';
			$categories = get_the_category($post->ID);
		}else{
			$categories_slug = 'slug';
			$categories = isset($post->ID) ? wp_get_post_terms($post->ID,$attr['taxonomySlug']) : [];
		}

		if ($categories && !empty($attr["relatedPost"]) && ($attr["relatedPost"]=='both' || $attr["relatedPost"]=='category')) {	
			$category_ids = array();
			foreach($categories as $category) $category_ids[] = $category->$categories_slug;
			
			$query_args['post__not_in'] = array($post->ID);

			if(isset($post->post_type) && $post->post_type =='post'){
				$query_args['category__in'] = $category_ids;
			}else{
				$query_args['tax_query'] = array(						
				  array(		
					'taxonomy' => $attr['taxonomySlug'],		
					'field' => 'slug',		
					'terms' => $category_ids,
				  ),		
				);
			}
		}
	}

	/*custom query id*/
	if( !empty($attr['customQueryId']) ){
		if( has_filter( $attr['customQueryId'] )) {
			$query_args = apply_filters( $attr['customQueryId'], $query_args);
		}
	}
	/*custom query id*/

	return $query_args;
}

function tpgb_filter_category($attr){
	$query_args = tpgb_post_query($attr);
	$query = new \WP_Query( $query_args );
	
	if ( is_string($attr['postCategory'] )) {
		$cat_arr = array();
		$attr['postCategory'] = json_decode($attr['postCategory']);
		if (is_array($attr['postCategory']) || is_object($attr['postCategory'])) {
			foreach ($attr['postCategory'] as $value) {
				$cat_arr[] = $value->value;
			}
		}
	}

	$category_filter='';
	$taxonomy = !empty($attr["taxonomySlug"]) ? $attr["taxonomySlug"] : 'category';
	if(!empty($attr['ShowFilter'])){
	
		$filter_style=$attr["filterStyle"];
		$filter_hover_style=$attr["filterHvrStyle"];
		$all_filter_category=(!empty($attr["TextCat"])) ? $attr["TextCat"] : esc_html__('All','tpgbp');
		$filter=(!empty($attr["CatName"])) ? $attr["CatName"] : esc_html__('Filters','tpgbp');
		
		if($attr['postType']=='post'){					
			if($attr['filterBy'] == 'category'){
				$terms = get_terms( array('taxonomy' => 'category', 'hide_empty' => true) );
			}else if($attr['filterBy'] == 'tag'){
				$terms = get_terms( array('taxonomy' => 'post_tag','hide_empty' => true,));	
			}
		}else{
			if(!empty($attr['childcategory'])){			
				$terms = get_terms( array('taxonomy' => $taxonomy , 'hide_empty' => true ,'parent' => 0) );
			}else{
				$terms = get_terms( array('taxonomy' => $taxonomy ,'hide_empty' => true	) );
			}
		}
	
		$all_category=$category_post_count='';
		
		if($filter_style=='style-1'){
			$count=$query->post_count;
			$all_category='<span class="tpgb-category-count">'.esc_html($count).'</span>';
		}
		if($filter_style=='style-2' || $filter_style=='style-3'){
			$count=$query->post_count;
			$category_post_count='<span class="tpgb-category-count">'.esc_attr($count).'</span>';
		}
		
		$count_cate = array();
		if($filter_style=='style-2' || $filter_style=='style-3'){
			if($query->have_posts()){
				while ( $query->have_posts() ) {				
					$query->the_post();
					$categories = get_the_terms( $query->ID, $taxonomy );
					
					if($attr['postType']=='post'){					
						if($attr['filterBy'] == 'category'){						
							$categories = get_the_terms( $query->ID, 'category' );
						}else if($attr['filterBy'] == 'tag'){
							$categories = get_the_terms( $query->ID, 'post_tag' );								
						}							
					}else{
						$categories = get_the_terms( $query->ID, $taxonomy );							
					}

					if($categories){
						foreach( $categories as $category ) {
							if(isset($count_cate[$category->slug])){
								$count_cate[$category->slug]= $count_cate[$category->slug] +1;
							}else{
								$count_cate[$category->slug]= 1;
							}
						}
					}
				}
			}
			wp_reset_postdata();
		}
		
		$category_filter .='<div class="tpgb-category-filter">';
			$category_filter .='<div class="tpgb-filter-data '.esc_attr($filter_style).' ">';
				if($filter_style=='style-4'){
					$category_filter .= '<span class="tpgb-filters-link">'.esc_html($filter).'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
				}
				$category_filter .='<div class="tpgb-categories '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
				if(!empty($attr['ShowallFilter']) && $attr['ShowallFilter'] == 'yes') {
					$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></div>';
				}
				if(!empty($attr['childcategory'])){
					$parent = array();
					$cateindex = 0;
				}
				if ( $terms != null ){
					
					foreach( $terms as $term ) {
						$category_post_count='';
						if($filter_style=='style-2' || $filter_style=='style-3'){
							if(isset($count_cate[$term->slug])){
								$count=	$count_cate[$term->slug];
							}else{
								$count = 0;
							}
							$category_post_count='<span class="tpgb-category-count">'.esc_html($count).'</span>';
						}
						if(!empty($attr['childcategory'])){
								
							if($term->parent != 0) {
								
								$parent[$cateindex]['id'] = $term->term_id;
								$parent[$cateindex]['slug'] = $term->slug;
							}else{
								$parent[$cateindex]['id'] = $term->term_id;
								$parent[$cateindex]['slug'] = $term->slug;
							}
							$cateindex++;
						}

						//Get category icon From Acf Field
						$cat_icon = '';
						if($filter_style=='style-3' && $attr['style'] == 'style-5' ){
							if( get_field('category_image', $term )){
								$cat_icon .= '<img src="'.get_field('category_image', $term ).'" />';
							}
							if( get_field('category_icon', $term )){
								$cat_icon .= '<i class="cat-filter-icon '.get_field('category_icon', $term ).'"> </i>' ;
							}
						}
						if(!empty($cat_arr)){							
							if(in_array($term->term_id,$cat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.' '.$cat_icon.' <span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></div>';
								unset($term);
							}
						}else{
							if(empty($excat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.''.$cat_icon.' <span data-hover="'.esc_attr($term->name).'"> '.esc_html($term->name).'</span></a></div>';
								unset($term);
							}else if(!empty($excat_arr) && !in_array($term->term_id,$excat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.''.$cat_icon.' <span data-hover="'.esc_attr($term->name).'"> '.esc_html($term->name).'</span></a></div>';
								unset($term);
							}
						}
					}
				}
				$category_filter .= '</div>';
				if(!empty($attr['childcategory'])){
					if($parent) {
						foreach ($parent as $par) {		
							$child_categories= get_term_children($par['id'], $taxonomy);
							
							if(!empty($child_categories)){
								$category_filter .= '<div class="category-filters-child cate-parent-'.$par['slug'].'">';
									foreach($child_categories as $child) {
										$term = get_term_by( 'id', $child, $taxonomy );
										$cat_thumb_id_child=$featured_image_child='';
										$category_filter .= '<div class="tpgb-child-list"><a href="' . get_term_link( $child, $taxonomy ) . '" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">' .$featured_image_child. $term->name . '</a></div>';
									}
								$category_filter .= '</div>';
							}
						}
					}			
				}
			$category_filter .= '</div>';
		$category_filter .= '</div>';
	}
	return $category_filter;
}

function tpgb_pagination($pages = '', $range = 2){  
	$showitems = ($range * 2)+1;  
	
	global $paged;
	if(empty($paged)) $paged = 1;
	
	if($pages == ''){
		global $wp_query;
		if( $wp_query->max_num_pages <= 1 )
		return;
		
		$pages = $wp_query->max_num_pages;
		/*if(!$pages)
		{
			$pages = 1;
		}*/
		$pages = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	}   
	
	if(1 != $pages){
		$paginate ="<div class=\"tpgb-pagination\">";
		if ( get_previous_posts_link() ){
			$paginate .= '<div class="paginate-prev">'.get_previous_posts_link('<i class="fa fa-long-arrow-left" aria-hidden="true"></i> PREV').'</div>';
		}
		
		for ($i=1; $i <= $pages; $i++){
			if (1 != $pages && ( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
			{
				$paginate .= ($paged == $i)? "<span class=\"current\">".esc_html($i)."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".esc_html($i)."</a>";
			}
		}
		if ( get_next_posts_link() ){
			$paginate .='<div class="paginate-next">'.get_next_posts_link('NEXT <i class="fa fa-long-arrow-right" aria-hidden="true"></i>',1).'</div>';
		}
		
		$paginate .="</div>\n";
		return $paginate;
	}
}