<?php if(!empty($showPostCategory) && $showPostCategory=='yes'){
	include TPGBP_INCLUDES_URL. 'blog/category-'.esc_attr($postCategoryStyle).'.php';
}

if(!empty($ShowTitle) && $ShowTitle=='yes'){
	include TPGBP_INCLUDES_URL. 'blog/post-title.php';
} ?>

<div class="tpgb-post-hover-content">
	
	<?php
	if(!empty($showExcerpt) && $showExcerpt=='yes' && get_the_excerpt()){
		include TPGBP_INCLUDES_URL. 'blog/get-excerpt.php';
	}
	
	if(!empty($showPostMeta) && $showPostMeta=='yes'){
		include TPGBP_INCLUDES_URL. 'blog/post-meta-'.esc_attr($postMetaStyle).'.php';
	} ?>
</div>