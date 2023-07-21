<?php 
if($posttype == 'product'){
	$b_dis_badge_switch	 	= isset( $postdata['badge'] ) ? sanitize_text_field( wp_unslash($postdata['badge']) ) : '';
	$out_of_stock	 		= isset( $postdata['out_of_stock'] ) ? sanitize_text_field( wp_unslash($postdata['out_of_stock']) ) : '';
	$post_title_tag 		= isset( $postdata['titletag'] ) ? sanitize_text_field( wp_unslash($postdata['titletag']) ) : '';

	$thumbnail	= isset( $postdata['Feature_img_type'] ) ? sanitize_text_field( wp_unslash($postdata['Feature_img_type']) ) : '';
	$display_cart_button	= isset( $postdata["cartBtn"] ) ? sanitize_text_field( wp_unslash($postdata["cartBtn"]) ) : '' ;
	$variation_price_on		= isset( $postdata["variationprice"] ) ? sanitize_text_field( wp_unslash($postdata["variationprice"]) ) :  '';
	$H_ImageChange 			= isset( $postdata["hoverimagepro"] ) ? sanitize_text_field( wp_unslash($postdata["hoverimagepro"]) ) :  '';
	$display_thumbnail		= isset( $postdata["display_thumbnail"] ) ? sanitize_text_field( wp_unslash($postdata["display_thumbnail"]) ) :  '';
	$display_catagory 	 	= isset( $postdata["display_catagory"] ) ? sanitize_text_field( wp_unslash($postdata["display_catagory"]) ) : '' ;
	$display_rating			= isset( $postdata["display_rating"] ) ? sanitize_text_field( wp_unslash($postdata["display_rating"]) ) : '' ;
	$dcb_single_product		= isset( $postdata['dcb_single_product'] ) ? sanitize_text_field( wp_unslash($postdata['dcb_single_product']) ) : '';
	$dcb_variation_product 	= isset( $postdata['dcb_variation_product'] ) ? sanitize_text_field( wp_unslash($postdata['dcb_variation_product']) ) : '';
}
if( $layout == 'metro' ){
	if( ( isset($metrocolumns['md']) && !empty($metrocolumns['md']) ) && ( isset($metroStyle['md']) && !empty($metroStyle['md']) ) ){
		$col= Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($count , $metrocolumns['md'] , $metroStyle['md'] );
	}
	if( ( isset($metrocolumns['sm']) && !empty($metrocolumns['sm']) ) && ( isset($metroStyle['sm']) && !empty($metroStyle['sm']) ) ){
		$tabCol = Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($count , $metrocolumns['sm'] , $metroStyle['sm'] );
	}
	if( ( isset($metrocolumns['xs']) && !empty($metrocolumns['xs']) ) && ( isset($metroStyle['xs']) && !empty($metroStyle['xs']) ) ){
		$moCol = Tpgbp_Pro_Blocks_Helper::tpgbp_metro_class($count , $metrocolumns['xs'] , $metroStyle['xs'] );
	}
}
//category filter
$category_filter='';
if(!empty($ShowFilter) && $ShowFilter == 'yes' ){
	if(!empty($filterBy)){
		if($filterBy == 'category'){
			$terms = get_the_terms($loop->ID, 'product_cat');
		}else{
			$terms = get_the_terms( get_the_ID(), 'product_tag');
		}
	}
	if ( $terms != null ){
		foreach( $terms as $term ) {
			$category_filter .=' '.esc_attr($term->slug).' ';
			unset($term);
		}
	}
}
//grid item loop
echo '<div class="grid-item tpgb-col '.esc_attr($column_class).' '.esc_attr($category_filter).' '.( $layout=='metro' ? ' tpgb-metro-'.esc_attr($col).' '.( !empty($tabCol) ? ' tpgb-tab-metro-'.esc_attr($tabCol).''  : '' ).' '.( !empty($moCol) ? ' tpgb-mobile-metro-'.esc_attr($moCol).''  : '' ).' ' : '' ).' ">';
	include TPGBP_PATH ."includes/product-listing/product-{$style}.php";
echo '</div>';