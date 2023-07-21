<?php 
$column_class_1=$column_class_2='';
if(!empty($styleLayout) && $styleLayout=='style-1'){
	$column_class_1='tpgb-col-12 tpgb-col-lg-4 tpgb-col-md-4 tpgb-col-sm-4 tpgb-flex-column tpgb-flex-wrap';
	$column_class_2='tpgb-col-12 tpgb-col-lg-8 tpgb-col-md-8 tpgb-col-sm-8 tpgb-flex-column tpgb-flex-wrap';
}else if(!empty($styleLayout) && $styleLayout=='style-2'){
	$column_class_1='tpgb-col-12 tpgb-col-lg-6 tpgb-col-md-6 tpgb-col-sm-6 tpgb-flex-column tpgb-flex-wrap';
	$column_class_2='tpgb-col-12 tpgb-col-lg-6 tpgb-col-md-6 tpgb-col-sm-6 tpgb-flex-column tpgb-flex-wrap';
}
$bg_attr = '';
if(!empty($layout) && $layout=='metro'){
	$featured_image= get_the_post_thumbnail_url(get_the_ID(), $thumbnail );

	if(!empty($featured_image)){
		$bg_attr = 'style="background:url('.$featured_image.');"';
	}
}

?>
<div class="dynamic-list-content tpgb-dynamic-tran tpgb-d-flex tpgb-flex-wrap tpgb-align-items-center">
	
	<?php if($layout != 'metro') { ?>
	<div class="post-content-image <?php echo esc_attr($column_class_1); ?>">
		<?php include TPGBP_INCLUDES_URL. 'blog/format-image.php'; ?>
	</div>
	<?php } ?>

	<div class="tpgb-content-bottom <?php echo esc_attr($column_class_2); ?>">
		<?php if(!empty($styleLayout)){
			include TPGBP_INCLUDES_URL. 'blog/blog-style-3-layout-'.esc_attr($styleLayout).'.php';
		}else{
			include TPGBP_INCLUDES_URL. 'blog/blog-style-3-layout-style-1.php';
		} ?>

		<?php if(!empty($ShowButton) && $ShowButton == 'yes') { ?>
			<div class="tpgb-adv-button button-<?php echo esc_attr($postBtnsty); ?>"> 
				<a class="button-link-wrap" href="<?php echo esc_url(get_the_permalink()); ?>" > 
					<?php 
						if($postBtnsty == 'style-8'){
							if($btnIconPosi == 'before'){
					?>
							<span class="btn-icon  button-<?php echo esc_attr($btnIconPosi); ?>"> 
								<i class="<?php echo esc_attr($pobtnIconName); ?>" > </i>
							</span>
							<?php echo esc_html($postbtntext); ?>
					<?php
							}else{
					?>
							<?php echo esc_html($postbtntext); ?>
							<span class="btn-icon  button-<?php echo esc_attr($btnIconPosi); ?>"> 
								<i class="<?php echo esc_attr($pobtnIconName); ?>"> </i>
							</span>
					<?php 			
							}
						}else if( $postBtnsty == 'style-7' || $postBtnsty == 'style-9' ){
							echo esc_html($postbtntext);
					?>
						<span class='button-arrow'> 
							<?php if($postBtnsty == 'style-7') { ?> 
								<span class='btn-right-arrow'><i class="fas fa-chevron-right"></i></span>  
							<?php }  if($postBtnsty == 'style-9') { ?>
								<i class="btn-show fas fa-chevron-right"></i>
								<i class="btn-hide fas fa-chevron-right"></i>
							<?php } ?>
						</span>
					<?php
						}
					?>
				</a>
			</div>
		<?php } ?>
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