<?php 
    $bg_attr = '';
	if(!empty($layout) && $layout=='metro'){
		$featured_image= get_the_post_thumbnail_url(get_the_ID(), $thumbnail );

		if(!empty($featured_image)){
			$bg_attr = 'style="background:url('.$featured_image.');"';
		}
	}
?>

<div class="dynamic-list-content tpgb-dynamic-tran">

	<?php if( $layout != 'metro' ) { include TPGBP_INCLUDES_URL. 'blog/format-image.php'; } ?>
	
	<div class="tpgb-content-bottom">
	
		<?php if(!empty($showPostCategory) && $showPostCategory=='yes'){        
		?>
			<?php include TPGBP_INCLUDES_URL. 'blog/category-'.$postCategoryStyle.'.php'; ?>
		<?php } ?>

		<?php if(!empty($showPostMeta) && $showPostMeta=='yes'){ ?>
			<?php include TPGBP_INCLUDES_URL. 'blog/post-meta-'.$postMetaStyle.'.php'; ?>
		<?php } ?>
		
		<?php if(!empty($ShowTitle) && $ShowTitle=='yes'){
			include TPGBP_INCLUDES_URL. 'blog/post-title.php'; 
		} ?>
		
		<div class="tpgb-post-hover-content">
			<?php if(!empty($showExcerpt) && $showExcerpt=='yes' && get_the_excerpt()){
				include TPGBP_INCLUDES_URL. 'blog/get-excerpt.php';
			} ?>
		</div>
		
	</div>
	<?php if( $layout == 'metro' ) { ?>
		<div class="tpgb-post-featured-img tpgb-dynamic-tran <?php echo esc_attr($imageHoverStyle); ?>">
			<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
				<?php echo '<div class="tpgb-blog-image-metro"  '.$bg_attr.' ></div>'; ?>
			</a>
		</div>
	<?php } ?>
	<?php 
		if($postListing == 'searchList' || $postListing =='search_list') {
			include TPGBP_INCLUDES_URL. 'blog/blog-skeleton.php';
		} 
	?>
</div>
