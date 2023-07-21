<?php 
global  $post;
if(!empty($display_catagory) && $display_catagory == 'yes' ){
	$terms = get_the_terms( $post->ID, 'product_cat' );
	foreach ($terms as $term) {
		$product_cat_name = $term->name;
		echo '<span class="post-catagory product_cat-'.esc_attr($term->slug).'">'.esc_html($product_cat_name).'</span>';
		break;
	}
}