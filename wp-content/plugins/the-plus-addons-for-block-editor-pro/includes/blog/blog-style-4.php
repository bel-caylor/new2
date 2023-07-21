<?php 
	$featured_image=get_the_post_thumbnail_url(get_the_ID(),'full');
	$bg_attr='';
	if($featured_image){
		$bg_attr='style="background:url('.esc_url($featured_image).') #f7f7f7;"';
	}
?>
<div class="dynamic-list-content">		
	<div class="tpgb-post-featured-img tpgb-dynamic-tran <?php echo esc_attr($imageHoverStyle); ?>">
		<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
			<div class="tpgb-post-img tpgb-dynamic-tran" <?php echo $bg_attr; ?>></div>
		</a>
	</div>
	<div class="tpgb-content-bottom">
		<?php
		if($showPostCategory=='yes'){
			include TPGBP_INCLUDES_URL. 'blog/category-'.esc_attr($postCategoryStyle).'.php';
		}
		if(!empty($ShowTitle) && $ShowTitle=='yes'){
			include TPGBP_INCLUDES_URL. 'blog/post-title.php';
		}
		?>
		
		<div class="tpgb-post-hover-content">
			<?php if(!empty($showExcerpt) && $showExcerpt=='yes' && get_the_excerpt()){
				include TPGBP_INCLUDES_URL. 'blog/get-excerpt.php';
			}
			
			if(!empty($showPostMeta) && $showPostMeta=='yes'){
				include TPGBP_INCLUDES_URL. 'blog/post-meta-'.esc_attr($postMetaStyle).'.php';
			} ?>
		</div>
	</div>
	<?php 
		if($postListing == 'searchList' || $postListing =='search_list') {
			include TPGBP_INCLUDES_URL. 'blog/blog-skeleton.php';
		} 
	?>
</div>
