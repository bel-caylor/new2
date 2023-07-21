<?php 
	global $product;
	$product_id = get_the_ID();
	
	$image_html = "";
	if(has_post_thumbnail()){
		$image_html = wp_get_attachment_image(get_post_thumbnail_id(), 'shop_catalog');					
	}else if(wc_placeholder_img_src()){
		$image_html = wc_placeholder_img('shop_catalog');
	}
	
	if(!empty($layout) && $layout=='metro'){
		$featured_image= get_the_post_thumbnail_url(get_the_ID(), $thumbnail );

		if(!empty($featured_image)){
			$bg_attr = 'style="background:url('.$featured_image.');"';
		}
	}

	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="product-list-content">
		<?php if($layout!='metro'){ ?>
			<div class="product-content-image">
				<?php
				$out_of_stock_val='';
				if(!empty($out_of_stock)){
					$out_of_stock_val = $out_of_stock;
				}
				if(!empty($DisBadge)){
					do_action('tpgb_product_badge', $out_of_stock_val);
				}
				$attachment_ids = $product->get_gallery_image_ids();
				if ($attachment_ids) { ?>
					<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php the_title_attribute(); ?>" class="product-image">
						<?php 
							if (!get_post_meta( $attachment_ids[0], '_woocommerce_exclude_image', true )){ 
								if(!empty($H_ImageChange) && $H_ImageChange = 'yes'){
						?>
								<span class="product-image hover-image">
									<?php echo wp_get_attachment_image($attachment_ids[0], 'shop_catalog'); ?></span>
								<?php }
						} 
						echo $image_html; ?>
					</a>
					<?php 
				}else{
					if ( has_post_thumbnail() ) { ?>
						<a href="<?php echo esc_url(get_the_permalink()); ?>" class="product-image" aria-label="<?php the_title_attribute(); ?>">
							<?php include TPGBP_INCLUDES_URL."product-listing/format-image.php"; ?>
						</a>
					<?php }else{ ?>
						<div class="product-image">
							<?php echo '<img src="'.esc_url(TPGBP_URL.'assets/images/tpgb-placeholder.jpg').'" alt="">'; ?>
						</div>
					<?php }
				} ?>
				<div class="product-quick-view">
					<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php echo esc_attr__('View','tpgbp'); ?>" class="quick-view-btn">
						<svg class="tpgb-add-cart-svg svg-inline--fa fa-eye fa-w-18" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
							<path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path>
						</svg>
					</a>
				</div>
			</div>
		<?php } ?>
		<div class="post-content-bottom">
			<?php 	
				if(!empty($display_catagory)){
					include TPGBP_INCLUDES_URL."product-listing/post-meta-catagory.php";
				}
				include TPGBP_INCLUDES_URL."product-listing/post-meta-title.php";

				if(!empty($display_rating)){
					include TPGBP_INCLUDES_URL."product-listing/post-rating.php";
				}
			?>

			<div class="hover-content-price">
				<?php 
					include TPGBP_INCLUDES_URL."product-listing/product-price.php"; 
					if(!empty($display_cart_button) && $display_cart_button == 'yes') { ?>
						<div class="wrapper-cart-hover-hidden add-cart-btn">
							<?php $_product = wc_get_product( $product_id );
							if($_product->is_type('simple')){ ?>
								<div class="product-add-to-cart" >
									<a title="<?php echo esc_attr($dcb_single_product); ?>" href="?add-to-cart=<?php echo esc_attr($product_id); ?>" rel="nofollow" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" class="add_to_cart add_to_cart_button product_type_simple ajax_add_to_cart">
										<span class="text">
											<span>
												<?php echo esc_html($dcb_single_product); ?>
											</span>
										</span>
										<span class="icon">
											<span class="sr-loader-icon"></span>
											<span class="check"></span>
										</span>
									</a>
								</div>
							<?php }else{ ?>
								<div class="product-add-to-cart" >
									<a rel="nofollow" href="<?php echo esc_url(get_the_permalink()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" class="add_to_cart add_to_cart_button product_type_simple " data-added-text="">
										<span class="text">
											<span>
												<?php echo esc_html($dcb_variation_product); ?>
											</span>
										</span>
										<span class="icon">
											<span class="sr-loader-icon"></span>
											<span class="check"></span>
										</span>
									</a>
								</div>
							<?php } ?>
						</div>
				<?php } ?>
			</div>
			<?php if($layout=='metro'){ ?> 
				<div class="product-quick-view">
					<a href="<?php echo esc_url(get_the_permalink()); ?>" aria-label="<?php echo esc_attr__('View','tpgbp'); ?>" class="quick-view-btn">
						<svg class="tpgb-add-cart-svg svg-inline--fa fa-eye fa-w-18" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
							<path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path>
						</svg>
					</a>
				</div>
			<?php } ?>
		</div>

		<?php if($layout=='metro') { ?>
			<div class="tpgb-product-bg-image" <?php echo $bg_attr; ?>><?php 

			$out_of_stock_val='';
			if(!empty($out_of_stock)){
				$out_of_stock_val =$out_of_stock;
			}
			if(!empty($DisBadge)){
				do_action('tpgb_product_badge', $out_of_stock_val);	
			}
			if(!empty($DisBadge)){
				do_action('tpgb_product_badge', $out_of_stock_val);	
			}
			?></div>
		<?php } ?>
	</div>
</article>