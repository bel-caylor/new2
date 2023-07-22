<?php 

	// Metro class Layout
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
	if(!empty($ShowFilter) && $ShowFilter == 'yes'){
		$terms = get_the_terms( $loop->ID, $taxonomySlug );
		if ( $terms != null ){
			foreach( $terms as $term ) {
				$category_filter .=' '.esc_attr($term->slug).' ';
				unset($term);
			}
		}
	}
	
	$postListing = $postdata['type'];
	
	if($cuscntType == 'editor'){
		$style = 'custom-skin';
	}


	//grid item loop
	echo '<div class="grid-item  '.( $layout !='metro' ?  'tpgb-col' : '').' '.esc_attr($column_class).' '.esc_attr($category_filter).' '.( $layout=='metro' ? ' tpgb-metro-'.esc_attr($col).' '.( !empty($tabCol) ? ' tpgb-tab-metro-'.esc_attr($tabCol).''  : '' ).' '.( !empty($moCol) ? ' tpgb-mobile-metro-'.esc_attr($moCol).''  : '' ).' ' : '' ).' ">';				
	if(!empty($style)){
		include TPGBP_PATH. 'includes/blog/'.sanitize_file_name('blog-'.$style.'.php');
	}
	
    echo '</div>';
