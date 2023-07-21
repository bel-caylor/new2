<?php
/* Block : Product Listing
 * @since : 1.2.1
 */
defined( 'ABSPATH' ) || exit;

function tpgb_product_listing_callback($attributes, $content) {
	$output = '';
    $block_id 	= (!empty($attributes['block_id']) ? $attributes['block_id'] : uniqid("title"));
	$columns 	= (!empty($attributes['columns']) ? $attributes['columns'] : 'tpgb-col-12');
	$PlType 	= (!empty($attributes['PlType']) ? $attributes['PlType'] : '');

	$blockClass = Tp_Blocks_Helper::block_wrapper_classes( $attributes );

	$equalHeightAtt = Tpgbp_Pro_Blocks_Helper::global_equal_height( $attributes );
	$equalHclass = '';
	if(!empty($equalHeightAtt)){
		$equalHclass = ' tpgb-equal-height';
	}

	$query_args = tpgb_product_get_query_args($attributes);
	$query = new WP_Query($query_args);

	$style 					= isset($attributes['style']) ? $attributes['style'] : 'style-1';
	$layout 				= isset($attributes['layout']) ? $attributes['layout'] : 'grid';
	$display_thumbnail		= !empty($attributes['DisImgSize']) ? $attributes['DisImgSize'] : true;
    $thumbnail 				= isset($attributes['ImageSize']) ? $attributes['ImageSize'] : 'grid';

	$post_title_tag 		= isset($attributes['TitleTag']) ? $attributes['TitleTag'] : 'h3';
	$display_cart_button 	= !empty($attributes['CBDis']) ? 'yes' : '';
	$dcb_single_product 	= !empty($attributes['AddTCText']) ? $attributes['AddTCText'] : '';
	$dcb_variation_product 	= !empty($attributes['SOptext']) ? $attributes['SOptext'] : '';
	$display_catagory 		= !empty($attributes['DisCtg']) ? 'yes' : '';
	$display_rating			= !empty($attributes['DisRtg']) ? $attributes['DisRtg'] : false;

	$OutOfStock 		= !empty($attributes['BadOutStSty']) ? $attributes['BadOutStSty'] : false;
	$DisBadge 			= !empty($attributes['DisBadge']) ? $attributes['DisBadge'] : false;
	$Price_Range 		= !empty($attributes['VPPrice']) ? $attributes['VPPrice'] : false;
	$H_ImageChange 		= !empty($attributes['OnHImgCng']) ?  'yes' : '';
	$prod_cat	 		= !empty($attributes["postCategory"]) ? json_decode($attributes["postCategory"]) : [];
	$tagOptions		= !empty($attributes["tagOptions"]) ? json_decode($attributes["tagOptions"]) : [];
	$filterBy = !empty($attributes["filterBy"]) ? $attributes["filterBy"] : ''; 

	// Load More
	$displayPosts 	= (!empty($attributes['MaxPd']) ? $attributes['MaxPd'] : 0);
	$offsetPosts 	= (!empty($attributes['offsetP']) ? $attributes['offsetP'] : 0);
	$postLodop 		= (!empty($attributes['postLodop']) ? $attributes['postLodop'] : '');
	$postview 		= (isset($attributes['postview']) ? $attributes['postview'] : '');
	$orderBy		= (isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date');
	$order			= (isset($attributes['order']) ? $attributes['order'] : 'desc');
	$loadbtnText 	= (isset($attributes['loadbtnText']) ? $attributes['loadbtnText'] : 'Load More');
	$loadingtxt 	= (isset($attributes['loadingtxt']) ? $attributes['loadingtxt'] : 'Loading...');
	$allposttext 	= (isset($attributes['allposttext']) ? $attributes['allposttext'] : 'All Done');

	// Carousel
	$Rowclass 		= (($layout != 'carousel') ? 'tpgb-row' : '');
	$CategoryWF 	= (!empty($attributes['ShowFilter'])) ?'yes' : '';
	$Categoryclass 	= (( !empty($CategoryWF) && $PlType == "page_listing") ? 'tpgb-category-filter' : '' );
	$showDots 		= (!empty($attributes['showDots'])) ? $attributes['showDots'] : [ 'md' => false ];
	$dotsStyle = (!empty($attributes['dotsStyle'])) ? $attributes['dotsStyle'] : false;
	$slideHoverDots = (!empty($attributes['slideHoverDots'])) ? $attributes['slideHoverDots'] : false;
	$showArrows 	= (!empty($attributes['showArrows'])) ? $attributes['showArrows'] : [ 'md' => false ];
	$arrowsStyle = (!empty($attributes['arrowsStyle'])) ? $attributes['arrowsStyle'] : 'style-1';
	$arrowsPosition = (!empty($attributes['arrowsPosition'])) ? $attributes['arrowsPosition'] : 'top-right';
	$outerArrows 	= (!empty($attributes['outerArrows'])) ? $attributes['outerArrows'] : false;
	$slideHoverArrows = (!empty($attributes['slideHoverArrows'])) ? $attributes['slideHoverArrows'] : false;
	$DisPr = (!empty($attributes['DisPr'])) ? $attributes['DisPr'] : '';

	// Metro Column
	$metrocolumns = isset($attributes['metrocolumns']) ? $attributes['metrocolumns'] : [ 'md' => '3' ] ;
	$metroStyle = isset($attributes['metroStyle']) ? $attributes['metroStyle'] : '';

	$list_layout='';
	if( $layout == 'grid' || $layout == 'masonry' ){
		$list_layout = 'tpgb-isotope';
	}else if( $layout =='carousel' ){
		$list_layout = 'tpgb-carousel splide';	
	}else if( $layout =='metro' ){
		$list_layout = 'tpgb-metro';
	}else{
		$list_layout = 'tpgb-isotope';
	}

	$desktop_class = '';
	if( $layout !='carousel' && $columns ){
		$desktop_class .= 'tpgb-col-'.esc_attr($columns['xs']);
		$desktop_class .= ' tpgb-col-lg-'.esc_attr($columns['md']);
		$desktop_class .= ' tpgb-col-md-'.esc_attr($columns['sm']);
		$desktop_class .= ' tpgb-col-sm-'.esc_attr($columns['xs']);
	}

	// Set Data For Metro Layout
	$metroAttr = [];
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

	//Carousel Options
	$Sliderclass = '';
	$carousel_settings = '';
	if($layout == 'carousel'){
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

	$out_of_stock = '';
	if( !empty($DisBadge) && !empty($OutOfStock) ){
		$out_of_stock = !empty($attributes['BadOutStTxt']) ? $attributes['BadOutStTxt'] : 'Out of Stock';
	}

	$ji=1;$col=$tabCol=$moCol='';

	$output .= '<div id="'.esc_attr($block_id).'" class="tpgb-block-'.esc_attr($block_id).' tpgb-product-listing tpgb-relative-block '.esc_attr($blockClass).' product-'.esc_attr($style).' '.esc_attr($list_layout).' '.esc_attr($Categoryclass).' '.esc_attr($Sliderclass).' '.esc_attr($equalHclass).'" data-style="'.esc_attr($style).'" data-layout="'.esc_attr($layout).'" data-id="'.esc_attr($block_id).'" data-splide=\''.json_encode($carousel_settings).'\' '.$equalHeightAtt.' '.( $layout == 'metro' ? $metroAttr : '' ).' >';
		
		if ( !class_exists('woocommerce') ) {
			$output .= '<h3 class="error-handal">'.esc_html__( "Wondering why it\'s not working? Please install WooCommerce Plugin and create your products to make this section working.", "tpgbp" ).'</h3>';
		}else if ( !$query->have_posts() ) {
			$output .= '<h3 class="error-handal">'.esc_html__( "Products not found", "tpgbp" ).'</h3>';
		}else{		
				if(!empty($CategoryWF) && $layout != 'carousel' && $PlType == "page_listing"){
					$output .= Product_CtgFilter($attributes);
				}

				if($postLodop == 'load_more' || $postLodop == 'lazy_load'){
					if($query->found_posts != ''){
						$total_posts = $query->found_posts;
						$post_offset 	= ((!empty($offsetPosts)) ? (int)$offsetPosts : 0);
						$display_posts 	= ((!empty($displayPosts)) ? (int)$displayPosts : 0);
						$offset_posts 	= intval($display_posts + $post_offset);
						$total_posts 	= intval($total_posts - $offset_posts);	
		
						if($total_posts != 0 && $postview != 0){
							$load_page = ceil($total_posts / $postview);	
						}else{
							$load_page = 1;
						}
							$load_page = $load_page + 1;
					}else{
						$load_page = 1;
					}

					//Set Category Array
					$category = array();
					if ( '' !== $prod_cat) {
						if (is_array($prod_cat) || is_object($prod_cat)) {
							foreach ($prod_cat as $value) {
								$category[] = $value->value;
							}
						}
						
					}

					$pro_Tag = array();
					if ( '' !== $tagOptions) {
						if (is_array($tagOptions) || is_object($tagOptions)) {
							foreach ($tagOptions as $value) {
								$pro_Tag[] = $value->value;
							}
						}
						
					}

					//echo $load_page;
					$postattr = [
						'post_type' => 'product',
						'filterBy' => $filterBy,
						'type' => '',
						'style'	=> $style,
						'desktop_column' => $attributes['columns']['md'],
						'tablet_column' =>  $attributes['columns']['sm'],
						'mobile_column'=>  $attributes['columns']['xs'],
						'metro_column' => $metrocolumns,
						'metro_style' => $metroStyle,
						'order_by' => $orderBy,
						'post_order' => $order,
						'filter_category' => $CategoryWF,
						'category' => $category,
						'postTag' => $pro_Tag,
						'badge' => $DisBadge,
						'out_of_stock' => $out_of_stock,
						'titletag' => $post_title_tag,
						'Feature_img_type' => $thumbnail,
						'cartBtn' => $display_cart_button,
						'variationprice' => $Price_Range,
						'hoverimagepro' => $H_ImageChange,
						'display_thumbnail' => $display_thumbnail,
						'display_catagory' => $display_catagory,
						'display_rating' => $display_rating,
						'dcb_single_product' => $dcb_single_product,
						'dcb_variation_product' => $dcb_variation_product,
						'disproduct' => $DisPr,
						'tpgb_nonce' => wp_create_nonce("theplus-addons-block"),
						'display_post' => $displayPosts,
						'page' => 1,
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
					];
					$dypostAttr = json_encode($dypostAttr);
				}

				$categ_slug = $terms = '';
					
				if($filterBy == 'category'){
					$terms = get_the_terms( $query->ID,'product_cat');
				}else if($filterBy == 'tag'){
					$terms = get_the_terms( $query->ID,'product_tag');
				}
				
				if ( $terms != null ){
					foreach( $terms as $term ) {
						$categ_slug .=' '.esc_attr($term->slug).' ';
						unset($term);
					}
				}
				
				if( $layout == 'carousel' && ( ( isset($showArrows['md']) && !empty($showArrows['md']) ) || ( isset($showArrows['sm']) && !empty($showArrows['sm']) ) || ( isset($showArrows['xs']) && !empty($showArrows['xs']) ) ) ){
					$output .= Tp_Blocks_Helper::tpgb_carousel_arrow($arrowsStyle,$arrowsPosition);
				}
				
				$output .= '<div class="'.esc_attr($Rowclass).' post-loop-inner '.( $layout=='carousel' ? 'splide__track' : '').'">';
					if($layout=='carousel'){
						$output .= '<div class="splide__list">';
					}
					while ($query->have_posts()) {
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

						//category filter
						$category_filter = '';
						if(!empty($CategoryWF) && $layout != 'carousel' && $PlType == "page_listing"){
							if($filterBy == 'category'){
								$terms = get_the_terms( $query->ID,'product_cat');
							}else if($filterBy == 'tag'){
								$terms = get_the_terms( $query->ID,'product_tag');
							}
							
							if($terms != null){
								foreach($terms as $term) {
									$category_filter .=' '.esc_attr($term->slug).' ';
									unset($term);
								}
							}
						}

						$output .= '<div class="grid-item '.($layout =='carousel' ? 'splide__slide' : ( $layout !='metro' ? esc_attr($desktop_class) : '')).' '.esc_attr($category_filter).' '.( $layout=='metro' ? ' tpgb-metro-'.esc_attr($col).' '.( !empty($tabCol) ? ' tpgb-tab-metro-'.esc_attr($tabCol).''  : '' ).' '.( !empty($moCol) ? ' tpgb-mobile-metro-'.esc_attr($moCol).''  : '' ).' ' : '' ).' ">';
							ob_start();
								include TPGBP_PATH."includes/product-listing/product-{$style}.php"; 
								$output .= ob_get_contents();
							ob_end_clean();
						$output .='</div>';

						$ji++;
					}
					if($layout=='carousel'){
						$output .= '</div>';
					}
				$output .= '</div>';
				
				if( $postLodop == 'pagination' && $layout != 'carousel' ){
					$output .= tpgb_Product_pagination($query->max_num_pages,'2');
				}else if( $postLodop == 'load_more' && $layout != 'carousel' ){
					$output .= '<div class="tpgb-load-more">';
						$output .= '<a class="post-load-more" data-dypost=\'' .esc_attr($dypostAttr). '\' data-post-option=\'' .$postattr. '\'>';
							$output .= wp_kses_post($loadbtnText);
						$output .= '</a>';
					$output .= '</div>';
				}else if( $postLodop == 'lazy_load' && $layout != 'carousel' ){
					$output .= '<div class="tpgb-lazy-load">';
						$output .= '<a class="post-lazy-load" data-dypost=\'' .esc_attr($dypostAttr). '\' data-post-option=\'' .$postattr. '\'>';
							$output .= '<div class="tpgb-spin-ring"><div></div><div></div><div></div></div>';
						$output .= '</a>';
					$output .= '</div>';
				}
		}

	$output .= '</div>';

	$output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);
	if($layout =='carousel'){
		$arrowCss = Tp_Blocks_Helper::tpgb_carousel_arrow_css( $showArrows , $block_id );
		if( !empty($arrowCss) ){
			$output .= $arrowCss;
		}
	}
    return $output;
}

function tpgb_product_get_query_args($a) {
	$display_product = $a["DisPr"];
	$post_category = json_decode($a['postCategory']);
	$pro_tag = json_decode($a['proTag']);
	
	//$category = (!empty($cat_arr)) ? implode(',', $cat_arr ) : '';
	$include_products = ($a['IPs']) ? explode(',', $a['IPs']) : [];
	$exclude_products = ($a['EPs']) ? explode(',', $a['EPs']) : [];

	$query_args = array(
		'post_type'           	=> 'product',
		'post_status'         	=> 'publish',
		'ignore_sticky_posts' 	=> true,
		'posts_per_page'      	=> intval($a['MaxPd']),
		'orderby'      			=> $a['orderBy'],
		'order'      			=> $a['order'],
		'post__not_in'        	=> $exclude_products,
		'post__in'        		=> $include_products,
	);

	//Product Category
	if ( !empty($post_category) ) {
		$cat_arr = array();
		if (is_array($post_category) || is_object($post_category)) {
			foreach ($post_category as $value) {
				$cat_arr[] = $value->value;
			}
		}
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => $cat_arr,
			)
		);
	}

	//Product Tag
	if( !empty($pro_tag) ){
		$tag_arr = array();
		if (is_array($pro_tag) || is_object($pro_tag)) {
			foreach ($pro_tag as $value) {
				$tag_arr[] = $value->value;
			}
		}

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_tag',
				'field' => 'term_id',
				'terms' => $tag_arr,
			)
		);
	}

	

	//Related Posts
	if($a["PlType"] == 'related_product'){
		global $post;
		$category_args = [];
		$tags_args = [];
		$tags = get_the_terms( $post->ID, 'product_tag' );

		if ($tags && ($a["RPlType"] == 'both' || $a["RPlType"] == 'tags')) {
			$tag_ids = array();
			foreach ($tags as $term){
				$tag_ids[] = $term->term_id;
			}
			$tags_args = array(
				'taxonomy'     => 'product_tag',
				'field'        => 'id',
				'terms'        => $tag_ids
			);
		}

		$categories = wp_get_post_terms( $post->ID, 'product_cat' );
		if ($categories && ($a["RPlType"] == 'both' || $a["RPlType"] == 'category')) {
			$category_ids = [];
			foreach ( $categories as $key => $term ){
				$check_for_children = get_categories(array('parent' => $term->term_id, 'taxonomy' => 'product_cat'));
				if(empty($check_for_children)){
					$category_ids[] = $term->term_id;
				}
			}
			$category_args =array(
				'taxonomy'     => 'product_cat',
				'field'        => 'id',
				'terms'        => $category_ids
			);
		}
		
		$query_args = array('tax_query' => array('relation'=> 'OR', $category_args, $tags_args));
		$query_args['post__not_in'] = array($post->ID);
	}

	//Archive Products
	if($a["PlType"] == 'archive_listing'){
		global $wp_query;
		$query_var = $wp_query->query_vars;
		if(isset($query_var['product_cat'])){
			$query_args['product_cat'] = $query_var['product_cat'];
		}
		if(isset($query_var['product_tag'])){
			$query_args['product_tag'] = $query_var['product_tag'];
		}
	}

	$offset = $a['offsetP'];
	$offset = ((!empty($offset)) ? absint($offset) : 0);
	
	global $paged;
	if ( get_query_var('paged') ) { 
		$paged = get_query_var('paged');
	}elseif ( get_query_var('page') ) { 
		$paged = get_query_var('page');
	}else { 
		$paged = 1; 
	}

	if ($a['postLodop'] != 'pagination') {
		$query_args['offset'] = $offset;
	}else if($a['postLodop'] == 'pagination'){
		$query_args['paged'] = $paged;
		$page 	= max(1, $paged);
		$offset = ($page - 1) * intval( $a['MaxPd'] ) + $offset;
		$query_args['offset'] = $offset;
	}

	if(!isset($display_product) || empty($display_product)) {
		$display_product = 'all';
	}

	switch($display_product) {
		case 'recent':
			$query_args['meta_query'] = (class_exists('woocommerce')) ? WC()->query->get_meta_query() : '';
			break;
		case 'featured':
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
				),
				/*array(
					'key' 		=> '_visibility',
					'value' 	  => array('catalog', 'visible'),
					'compare'	=> 'IN'
				),
				array(
					'key' 		=> '_featured',
					'value' 	  => 'yes'
				)*/
			);
			break;
		case 'on_sale':
			global $woocommerce;
				$sale_product_ids = wc_get_product_ids_on_sale();
				$meta_query = [];
				$meta_query[] = $woocommerce->query->visibility_meta_query();
				$meta_query[] = $woocommerce->query->stock_status_meta_query();
				$query_args['meta_query'] = $meta_query;
				$query_args['post__in'] = $sale_product_ids;
			break;
		case 'top_rated':
				add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
				$query_args['meta_query'] = WC()->query->get_meta_query();
			break;
		case 'top_sales':
				$query_args['meta_key'] = 'total_sales';
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_query'] = array(
						array(
							//'key' 		=> '_visibility',
							//'value' 	=> array( 'catalog', 'visible' ),
							//'compare' 	=> 'IN'
							'key' 		=> 'total_sales',
							'value' 	=> 0,
							'compare' 	=> '>',
						)
					);
			break;
		case 'instock':
				$query_args['meta_query'] = array(
						array(
							'key' 		=> '_stock_status',
							'value' 	=> 'instock',												
						)
					);
			break;
		case 'outofstock':
				$query_args['meta_query'] = array(
						array(
							'key' 		=> '_stock_status',
							'value' 	=> 'outofstock',												
						)
					);
			break;
	}

	return $query_args;
}

// Category
function Product_CtgFilter($attr){
	$query_args = tpgb_product_get_query_args($attr);
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
	if(!empty($attr['ShowFilter'])){
	
		$filter_style=$attr["filterStyle"];
		$filter_hover_style=$attr["filterHvrStyle"];
		$all_filter_category=(!empty($attr["TextCat"])) ? $attr["TextCat"] : esc_html__('All','tpgbp');
		$filter=(!empty($attr["CatName"])) ? $attr["CatName"] : esc_html__('Filters','tpgbp');
		
		if($attr['filterBy'] == 'category'){
			$terms = get_terms( array('taxonomy' => 'product_cat', 'hide_empty' => true) );
		}else if($attr['filterBy'] == 'tag'){
			$terms = get_terms( array('taxonomy' => 'product_tag','hide_empty' => true,));	
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
					$categories = '';
								
					if($attr['filterBy'] == 'category'){						
						$categories = get_the_terms( $query->ID, 'product_cat' );
					}else if($attr['filterBy'] == 'tag'){
						$categories = get_the_terms( $query->ID, 'product_tag' );								
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
						if(!empty($cat_arr)){							
							if(in_array($term->term_id,$cat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.' <span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></div>';
								unset($term);
							}
						}else{
							if(empty($excat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.' <span data-hover="'.esc_attr($term->name).'"> '.esc_html($term->name).'</span></a></div>';
								unset($term);
							}else if(!empty($excat_arr) && !in_array($term->term_id,$excat_arr)){
								$category_filter .= '<div class="tpgb-filter-list"><a href="#" class="tpgb-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.' <span data-hover="'.esc_attr($term->name).'"> '.esc_html($term->name).'</span></a></div>';
								unset($term);
							}
						}
					}
				}
				$category_filter .= '</div>';
			$category_filter .= '</div>';
		$category_filter .= '</div>';
	}
	return $category_filter;

}

function Product_Array_Category($a){
	if (!is_array($a)) { 
		return FALSE; 
	}

	$res = array(); 
	foreach ($a as $key => $v) { 
		if (is_array($v)) { 
			$res = array_merge($res, Product_Array_Category($v)); 
		}else { 
			$res[$key] = $v; 
		}
	}
	
	return $res; 
}
// Pagination
function tpgb_Product_pagination($pages = '', $range = 2){  
	$showitems = ($range * 2)+1;  
	
	global $paged;
	if(empty($paged)) {
		$paged = 1;
	}
	
	if(empty($pages)){
		global $wp_query;
		if( $wp_query->max_num_pages <= 1 )
		return;
		
		$pages = $wp_query->max_num_pages;
		/*if(!$pages){ $pages = 1; }*/
		$pages = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	}   
	
	if(1 != $pages){
		$paginate ="<div class=\"tpgb-pagination\">";
		if ( get_previous_posts_link() ){
			$paginate .= '<div class="paginate-prev">'.get_previous_posts_link('<i class="fa fa-long-arrow-left" aria-hidden="true"></i> PREV').'</div>';
		}
		
		for ($i=1; $i <= $pages; $i++)
		{
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

//Woocommerce Products
if(class_exists('woocommerce')) {
	function tpgb_out_of_stock() {
		global $post;
	  	$id = $post->ID;
	  	$status = get_post_meta($id, '_stock_status',true);
	  
	  	if ($status == 'outofstock'){
			return true;
	  	} else {
			return false;
	  	}
	}
}

add_action( 'tpgb_product_badge', 'tpgb_product_badge', 3 );
function tpgb_product_badge($out_of_stock_val='') {
	global $post, $product;
		if (tpgb_out_of_stock()) {
		   echo '<span class="badge out-of-stock">'.wp_kses_post($out_of_stock_val).'</span>';
	   } else if ( $product->is_on_sale() ) {
		   if ('discount' == 'discount') {
			   if ($product->get_type() == 'variable') {
				   $available_variations = $product->get_available_variations();								
				   $maximumper = 0;
				   for ($i = 0; $i < count($available_variations); ++$i) {
					   $variation_id=$available_variations[$i]['variation_id'];
					   $variable_product1= new WC_Product_Variation( $variation_id );
					   $regular_price = $variable_product1->get_regular_price();
					   $sales_price = $variable_product1->get_sale_price();
					   $percentage = $sales_price ? round( (($regular_price - $sales_price) / $regular_price) * 100) : 0;
					   if ($percentage > $maximumper) {
						   $maximumper = $percentage;
					   }
				   }
				   echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$maximumper.'%</span>', $post, $product);
			   } else if ($product->get_type() == 'simple'){
				   $percentage = round( (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price() ) * 100);
				   echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
			   } else if ($product->get_type() == 'external'){
				   $percentage = round( (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price() ) * 100);
				   echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
			   }
		   } else {
			   echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.esc_html__( 'Sale','tpgbp' ).'</span>', $post, $product);
		   }
	   }
}

function tpgb_tp_product_listing() {
    $globalBgOption = Tpgb_Blocks_Global_Options::load_bg_options();
    $globalpositioningOption = Tpgb_Blocks_Global_Options::load_positioning_options();
	$globalPlusExtrasOption = Tpgb_Blocks_Global_Options::load_plusextras_options();
	$carousel_options = Tpgb_Blocks_Global_Options::carousel_options();
	$globalEqualHeightOptions = Tpgbp_Plus_Extras_Opt::load_plusEqualHeight_options();
	$sliderOpt = [
		'slideColumns' => [
			'type' => 'object',
			'default' => [ 'md' => 4,'sm' => 3,'xs' => 2 ],
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
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > article, {{PLUS_WRAP}} .splide__list .splide__slide.is-active > div{-webkit-transform: scale({{centerslideScale}});-moz-transform: scale({{centerslideScale}});-ms-transform: scale({{centerslideScale}});-o-transform: scale({{centerslideScale}});transform: scale({{centerslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > article, {{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
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
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide  > article, {{PLUS_WRAP}} .splide__list .splide__slide  > div{-webkit-transform: scale({{normalslideScale}});-moz-transform: scale({{normalslideScale}});-ms-transform: scale({{normalslideScale}});-o-transform: scale({{normalslideScale}});transform: scale({{normalslideScale}});}{{PLUS_WRAP}} .splide__list .splide__slide > article, {{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
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
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide:not(.is-active) > article, {{PLUS_WRAP}} .splide__list .splide__slide:not(.is-active) > div{opacity:{{slideOpacity}};}{{PLUS_WRAP}} .splide__list .splide__slide > article, {{PLUS_WRAP}} .splide__list .splide__slide > div{transition: .3s all linear;}',
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
					'selector' => '{{PLUS_WRAP}} .splide__list .splide__slide.is-active > article,{{PLUS_WRAP}} .splide__list .splide__slide.is-active > div',
				],
			],
			'scopy' => true,
		],
	];
	$carousel_options = array_merge($carousel_options,$sliderOpt);
	
    $attributesOptions = [
        'block_id' => [
            'type' => 'string',
            'default' => '',
        ],
    
	/* Content Layout */
        'PlType' => [
			'type' => 'string',
			'default' => 'page_listing',	
		],
		'RPlType' => [
			'type' => 'string',
			'default' => 'both',	
		],
		'style' => [
			'type' => 'string',
			'default' => 'style-1',	
		],
		'layout' => [
			'type' => 'string',
			'default' => 'grid',	
		],
	/* Content Layout */
	
	/* Content Source */
		'postCategory' => [
			'type' => 'string',
			'default' => '[]',	
		],
		'proTag' => [
			'type' => 'string',
			'default' => '[]',
		],
		'IPs' => [
			'type'=> 'string',
			'default'=> '',
		],
		'EPs' => [
			'type'=> 'string',
			'default'=> '',
		],
		'MaxPd' => [
			'type'=> 'string',
			'default'=> 8,
		],
		'offsetP' => [
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
		'DisPr' => [
			'type' => 'string',
			'default' => 'all',
		],
	/* Content Source */

	/* Columns Manage */
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
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .grid-item{padding:{{columnSpace}};}',
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
	/* Columns Manage */

	/* Filter */
		'ShowFilter' => [
			'type' => 'boolean',
			'default' => false,
		],
		'filterBy' => [
			'type' => 'string',
			'default' => 'category',
		],
		'ShowallFilter' => [
			'type' => 'boolean',
			'default' => true,
		],
		'TextCat' => [
			'type' => 'string',
			'default' => 'All',
		],
		'filterStyle' => [
			'type' => 'string',
			'default' => 'style-1',
		],
		'filterHvrStyle' => [
			'type' => 'string',
			'default' => 'style-1',
		],
		'CatName' => [
			'type' => 'string',
			'default' => 'Filters',
		],
		'filterAlignment' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true]],
					'selector' => '.tpgb-category-filter .tpgb-filter-data{text-align:{{filterAlignment}};}',
				],
			],

		],
	/* Filter */

	/* Extra */
		'TitleTag' => [
			'type' => 'string',
			'default' => 'h3',	
		],
		'VPPrice' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'OnHImgCng' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'DisCtg' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'DisRtg' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'CBDis' => [
			'type' => 'boolean',
			'default' => true,	
		],
		'AddTCText' => [
			'type'=> 'string',
			'default'=> 'Add to cart',
		],
		'SOptext' => [
			'type'=> 'string',
			'default'=> 'Select Options',
		],
		'Compare' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'Wishlist' => [
			'type' => 'boolean',
			'default' => false,	
		],
		'QuickView' => [
			'type' => 'boolean',
			'default' => false,	
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
			'type'=> 'number',
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

	/* Extra */

	/* Title start */
		'TitleTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .post-title,{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .post-title a',
				],
			],
			'scopy' => true,
		],
		'TitleNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .post-title,{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .post-title a{color:{{TitleNCr}};}',
				],
			],
			'scopy' => true,
		],
		'TitleHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .product-list-content:hover .post-title,
					{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .product-list-content:hover .post-title a{color:{{TitleHCr}};}',
				],
			],
		],
		'TitleNBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-3.list-isotope-metro .post-title{background:{{TitleNBgCr}};}',
				],
			],
			'scopy' => true,
		],
		'TitleHBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-3']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-3 .product-list-content:hover .post-title{background:{{TitleHBgCr}};}',
				],
			],
			'scopy' => true,
		],
	/* Title End */

	/* rating start */
		'RatMrg' => [
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
					'condition' => [(object) ['key' => 'DisRtg', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .woocommerce-product-rating{padding:{{RatMrg}};}',
				],
			],
			'scopy' => true,
		],
		'RatingCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisRtg', 'relation' => '==', 'value' => true]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .star-rating span::before,{{PLUS_WRAP}}.tpgb-product-listing .star-rating::before{color:{{RatingCr}};}',
				],
			],
			'scopy' => true,
		],
	/* rating end */

	/* Product Price */
		'PriceMgn' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price{margin:{{PriceMgn}};}',
				],
			],
			'scopy' => true,
		],
		'PriceTypo' => [
			'type'=> 'object',
			'default'=> (object) [ 
				'openTypography' => 0 
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price .amount,{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price .amount .woocommerce-Price-currencySymbol',
				],
			],
			'scopy' => true,
		],
		'PriceNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
			   (object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price .amount,{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price .amount .woocommerce-Price-currencySymbol{color:{{PriceNCr}};}',
				],
			],
			'scopy' => true,
		],
		'PriceHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .wrapper-cart-price .price .amount,{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .wrapper-cart-price .price .amount .woocommerce-Price-currencySymbol{color:{{PriceHCr}};}',
				],
			],
			'scopy' => true,
		],
		'PrePriceTypo' => [
			'type'=> 'object',
			'default'=> (object) [ 
				'openTypography' => 0 
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price del .amount,
						{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .wrapper-cart-price .price del .amount .woocommerce-Price-currencySymbol',
				],
			],
			'scopy' => true,
		],
		'PrePriceNCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
			   (object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .wrapper-cart-price .price del .amount,{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .wrapper-cart-price .price del .amount .woocommerce-Price-currencySymbol{color:{{PrePriceNCr}};}',
				],
			],
			'scopy' => true,
		],
		'PrePriceHCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .wrapper-cart-price .price del .amount,{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .wrapper-cart-price .price del .amount .woocommerce-Price-currencySymbol{color:{{PrePriceHCr}};}',
				],
			],
			'scopy' => true,
		],
	/* Product Price */

	/* Badge start */
		'DisBadge' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'BadOutStSty' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'BadOutStTxt' => [
			'type'=> 'string',
			'default'=> 'Out of Stock',
			'scopy' => true,
		],
		'BadTypo' => [
			'type'=> 'object',
			'default'=> (object) ['openTypography' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'BadOutStSty', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.out-of-stock',
				],
			],
			'scopy' => true,
		],
		'BadCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'BadOutStSty', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.out-of-stock{color:{{BadCr}};}',
				],
			],
			'scopy' => true,
		],
		'BadBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'BadOutStSty', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.out-of-stock{background:{{BadBgCr}};}',
				],
			],
			'scopy' => true,
		],
		'BadBoxSd' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'BadOutStSty', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.out-of-stock',
				],
			],
			'scopy' => true,
		],
		'OnSaleSyl' => [
			'type' => 'boolean',
			'default' => true,	
			'scopy' => true,
		],
		'saleTypo' => [
			'type'=> 'object',
			'default'=> (object) ['openTypography' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'OnSaleSyl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.onsale',
				],
			],
			'scopy' => true,
		],
		'saleCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'OnSaleSyl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.onsale{color:{{saleCr}};}',
				],
			],
			'scopy' => true,
		],
		'saleBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'OnSaleSyl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.onsale{background:{{saleBgCr}};}{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.onsale:before{border-color: transparent transparent transparent {{saleBgCr}};}{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.onsale:after{border-color:{{saleBgCr}} transparent transparent;}',
				],
			],
			'scopy' => true,
		],
		'saleBoxSd' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'DisBadge', 'relation' => '==', 'value' => true ],
									(object) ['key' => 'OnSaleSyl', 'relation' => '==', 'value' => true ]],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content span.badge.out-of-stock',
				],
			],
			'scopy' => true,
		],
	/* Badge End */

	/* Content Alignment */
		'CbAlign' => [
			'type' => 'object',
			'default' => [ 'md' => ''],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner{text-align:{{CbAlign}};}',
				],
			],
			'scopy' => true,
		],
        'CbPadding' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .post-content-bottom{padding:{{CbPadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'CbNBgCr' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .post-content-bottom',
                ],
            ],
			'scopy' => true,
        ],
        'CbHBgCr' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .post-content-bottom',
                ],
            ],
			'scopy' => true,
        ],
        'CbNBocSd' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .post-content-bottom',
                ],
            ],
			'scopy' => true,
        ],
        'CbHBocSd' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .post-content-bottom',
                ],
            ],
			'scopy' => true,
        ],
	/* Content Alignment */

        'PIBgCr' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .product-image:before',
                ],
            ],
			'scopy' => true,
        ],
        'PIHBgCr' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .product-image:before',
                ],
            ],
			'scopy' => true,
        ],
        'PIBRs' => [
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
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .product-content-image{border-radius:{{PIBRs}};}',
                ],
            ],
			'scopy' => true,
        ],
        'PiNBoxSd' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .product-content-image',
                ],
            ],
			'scopy' => true,
        ],
        'PiHBoxSd' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content:hover .product-content-image',
                ],
            ],
			'scopy' => true,
        ],

        'Addpadding' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple{padding:{{Addpadding}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddTypo' => [
            'type'=> 'object',
            'default'=> (object) ['openTypography' => 0],
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple',
                ],
            ],
			'scopy' => true,
        ],
        'AddNCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple{color:{{AddNCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddNICr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-1 .add_to_cart_button span.icon .woo-arrow svg *{fill:{{AddNICr}};}',
                ],
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-1 .add_to_cart_button .icon .sr-loader-icon::after,{{PLUS_WRAP}}.tpgb-product-listing.product-style-1 .add_to_cart_button .icon .check::after,{{PLUS_WRAP}}.tpgb-product-listing.product-style-1 .add_to_cart_button .icon .check::before{background:{{AddNICr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddNBg' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2' ]],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple',
                ],
            ],
			'scopy' => true,
        ],
        'AddHCr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple:hover{color:{{AddHCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddHICr' => [
            'type' => 'string',
            'default' => '',
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-1 .add_to_cart_button:hover span.icon .arrow svg *{fill:{{AddHICr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddHBg' => [
            'type' => 'object',
            'default' => (object) ['openBg'=> 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple:hover',
                ],
            ],
			'scopy' => true,
        ],
        'AddBorder' => [
            'type' => 'object',
            'default' => (object) ['openBorder' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple',
                ],
            ],
			'scopy' => true,
        ],
        'AddBorderCr' => [
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
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple{border-radius:{{AddBorderCr}};}',
                ],
            ],
			'scopy' => true,
        ],
        'AddBoxSd' => [
            'type' => 'object',
            'default' => (object) ['openShadow' => 0],
            'style' => [
                (object) [
                    'condition' => [(object) ['key' => 'style', 'relation' => '!=', 'value' => 'style-2']],
                    'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .product-list-content .add_to_cart.product_type_simple',
                ],
            ],
			'scopy' => true,
        ],
		'AddNQICr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-2 .product-list-content a.quick-view-btn{color:{{AddNQICr}};}',
				],
			],
			'scopy' => true,
		],
		'AddNQIBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-2 .product-list-content a.quick-view-btn{background:{{AddNQIBgCr}};}',
				],
			],
			'scopy' => true,
		],
		'AddHQICr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-2 .product-list-content a.quick-view-btn{color:{{AddHQICr}};}',
				],
			],
			'scopy' => true,
		],
		'AddHQIBgCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'condition' => [(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing.product-style-2 .product-list-content a.quick-view-btn{background:{{AddHQIBgCr}};}',
				],
			],
			'scopy' => true,
		],
		
		'BloopPad' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content{padding:{{BloopPad}};}',
				],
			],
			'scopy' => true,
		],
		'BloopB' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content',
				],
			],
			'scopy' => true,
		],
		'blooprad' => [
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
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content{border-radius: {{blooprad}};}',
				],
			],
			'scopy' => true,
		],
		'BloopNBg' => [
			'type' => 'object',
			'default' => (object) ['openBg'=> 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content',
				],
			],
			'scopy' => true,
		],
		'BloopHBg' => [
			'type' => 'object',
			'default' => (object) ['openBg'=> 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content:hover',
				],
			],
			'scopy' => true,
		],
		'BloopNBsw' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content',
				],
			],
			'scopy' => true,
		],
		'BloopHBsw' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}}.tpgb-product-listing .post-loop-inner .grid-item .product-list-content:hover',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories .tpgb-filter-list a',
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
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true] ],
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-1 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before,{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-3 .tpgb-filter-list a,{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories.hover-style-4 .tpgb-filter-list a{padding:{{InPadding}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter  .tpgb-categories .tpgb-filter-list{margin:{{FCMargin}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .hover-style-1 .tpgb-filter-list a.active::after,{{PLUS_WRAP}}.tpgb-category-filter .hover-style-1 .tpgb-filter-list a:hover::after{background:{{FCHBcr}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count{color:{{FCNcr}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count),{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:after',

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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-4 .tpgb-filter-list a:before{border-top-color:{{FcBCrHs}};}',
				],
			],
			'scopy' => true,
		],
		'FCBgRs' => [
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
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
									(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
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
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
									(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:hover,{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a:focus,{{PLUS_WRAP}}.tpgb-category-filter:not(.hover-style-2) .tpgb-filter-list a.active,{{PLUS_WRAP}}.tpgb-category-filter .hover-style-2 .tpgb-filter-list a span:not(.tpgb-category-count)::before{color:{{FCHcr}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.tpgb-category-list.active span:not(.tpgb-category-count):before',

				],
			],
			'scopy' => true,
		],
		'FCHvrBre' => [
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
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
									(object) ['key' => 'filterHvrStyle', 'relation' => '==', 'value' => 'style-2']],
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before{border-radius:{{FCHvrBre}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a:hover span:not(.tpgb-category-count):before,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.hover-style-2 .tpgb-filter-list a.active span:not(.tpgb-category-count):before',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
									(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.all.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
				],
				(object) [
					'condition' => [(object) ['key' => 'layout', 'relation' => '!=', 'value' => 'carousel'],
									(object) ['key' => 'ShowFilter', 'relation' => '==', 'value' => true],
									(object) ['key' => 'style', 'relation' => '==', 'value' => 'style-1']],
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.active span.tpgb-category-count,{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a:hover span.tpgb-category-count{color:{{FCCategCcr}};}',
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
					'selector' => '{{PLUS_WRAP}}.tpgb-category-filter .tpgb-categories.style-1 .tpgb-filter-list a.all span.tpgb-category-count',
				],
			],
			'scopy' => true,
		],

		'PNFPad' => [
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
					'selector' => '{{PLUS_WRAP}} .error-handal{padding:{{PNFPad}};}',
				],
			],
			'scopy' => true,
		],
		'PNFTypo' => [
			'type'=> 'object',
			'default'=> (object) [
				'openTypography' => 0
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .error-handal',
				],
			],
			'scopy' => true,
		],
		'PNFCr' => [
			'type' => 'string',
			'default' => '',
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .error-handal{color:{{PNFCr}};}',
				],
			],
			'scopy' => true,
		],
		'PNFBgCr' => [
			'type' => 'object',
			'default' => (object) [
				'openBg'=> 0
			],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .error-handal',
				],
			],
			'scopy' => true,
		],
		'PNFB' => [
			'type' => 'object',
			'default' => (object) ['openBorder' => 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .error-handal',
				],
			],
			'scopy' => true,
		],
		'PNFBRs' => [
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
					'selector' => '{{PLUS_WRAP}} .error-handal{border-radius:{{PNFBRs}};}',
				],
			],
			'scopy' => true,
		],
		'PNFBoxSw' => [
			'type' => 'object',
			'default' => (object) ['openShadow' => 0],
			'style' => [
				(object) [
					'selector' => '{{PLUS_WRAP}} .error-handal',
				],
			],
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
					'condition' => [(object) ['key' => 'postLodop', 'relation' => '==', 'value' => 'load_more' ]],
					'selector' => '{{PLUS_WRAP}} .tpgb-load-more .post-load-more{border-radius : {{btnBradius}} }',
				],
			],
			'scopy' => true,
		],
		'btnhvrBradius' => [
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

    ];

    $attributesOptions = array_merge($attributesOptions, $carousel_options, $globalBgOption, $globalpositioningOption, $globalPlusExtrasOption, $globalEqualHeightOptions);

    register_block_type( 'tpgb/tp-product-listing', array(
		'attributes' => $attributesOptions,
		'editor_script' => 'tpgb-block-editor-js',
		'editor_style'  => 'tpgb-block-editor-css',
        'render_callback' => 'tpgb_product_listing_callback'
    ));
}
add_action( 'init', 'tpgb_tp_product_listing' );