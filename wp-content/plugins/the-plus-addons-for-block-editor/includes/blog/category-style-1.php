<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="tpgb-post-category cat-style-1">
	
	<?php $categories = get_the_terms(get_the_ID() , $taxonomySlug ); 
	
	$i=0;
	if(!empty($categories)) {
		foreach ( $categories as $category ) {
			echo '<a href="'.esc_url(get_category_link($category->term_id)).'" class="'.esc_attr($taxonomySlug).'-'.esc_attr($category->slug). '">'.esc_attr($category->name).'</a>';
		}
	}
	?>
</div>