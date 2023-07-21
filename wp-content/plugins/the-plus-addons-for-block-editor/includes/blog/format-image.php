<?php 
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
	$image_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
	
	if(! empty( $image_url )){
		if(!empty($layout) && $layout=='grid'){
			$featured_image= get_the_post_thumbnail(get_the_ID(), 'tp-image-grid', ['class' => 'tpgb-d-block tpgb-post-img tpgb-dynamic-tran']);
		}else if(!empty($layout) && $layout=='masonry'){
			$featured_image= get_the_post_thumbnail(get_the_ID(), 'full', ['class' => 'tpgb-d-block tpgb-post-img tpgb-dynamic-tran']);
		}else if(!empty($layout) && $layout=='carousel'){
			$featured_image=get_the_post_thumbnail(get_the_ID(), 'tp-image-grid', ['class' => 'tpgb-d-block tpgb-post-img tpgb-dynamic-tran']);
		}else{
			$featured_image=get_the_post_thumbnail(get_the_ID(),'full', ['class' => 'tpgb-d-block tpgb-post-img tpgb-dynamic-tran']);
		}
	}else{
		$featured_image = Tp_Blocks_Helper::get_default_thumb();
		$featured_image = $featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'"  class="tpgb-d-block tpgb-post-img tpgb-dynamic-tran">';
	}
?>
	<div class="tpgb-post-featured-img tpgb-dynamic-tran <?php echo esc_attr($imageHoverStyle); ?>">
		<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
			<?php echo $featured_image; ?>
		</a>
	</div>