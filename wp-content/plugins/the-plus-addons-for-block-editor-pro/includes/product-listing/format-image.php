<?php 
$featured_image_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
if( !empty($featured_image_url) ){
	if( $layout == 'grid' ){
		if( !empty($display_thumbnail) ){
			$featured_image = get_the_post_thumbnail(get_the_ID(), $thumbnail);
		}else{
			$featured_image = get_the_post_thumbnail(get_the_ID(), 'tp-image-grid');
		}
	}else if( $layout == 'masonry' ){
		if( !empty($display_thumbnail) ){				
			$featured_image = get_the_post_thumbnail(get_the_ID(), $thumbnail);
		}else{
			$featured_image = get_the_post_thumbnail(get_the_ID(), 'full');
		}
	}else if($layout == 'carousel'){
		if( empty($thumbnail) ){
			$thumbnail = 'full';				
		}else{
			if($thumbnail == 'grid'){
				$thumbnail = 'tp-image-grid';
			}
		}
		$featured_image = get_the_post_thumbnail(get_the_ID(),$thumbnail);
	}else{
		$featured_image = get_the_post_thumbnail(get_the_ID(),'full');
	}
}else{
	$featured_image = '<img src="'.esc_url(TPGB_ASSETS_URL.'assets/images/tpgb-placeholder.jpg').'" alt="'.esc_attr(get_the_title()).'">';
}
?>
<div class="product-featured-image">
	<span class="thumb-wrap">
		<?php echo $featured_image; ?>
	</span>
</div>