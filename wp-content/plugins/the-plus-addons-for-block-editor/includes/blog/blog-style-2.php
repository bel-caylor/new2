<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="dynamic-list-content tpgb-dynamic-tran">

	<div class="post-content-image">
		<?php include TPGB_INCLUDES_URL. 'blog/format-image.php'; ?>
		<?php if($showPostCategory=='yes' && $styleLayout=='style-2'){ ?>
			<?php include TPGB_INCLUDES_URL. 'blog/'.sanitize_file_name('category-'.$postCategoryStyle.'.php'); ?>
		<?php } ?>
	</div>

	<div class="tpgb-content-bottom <?php echo ($style2Alignment=='center') ? 'text-center' : 'text-left'; ?>">
		<?php
		if($showPostCategory=='yes' && $styleLayout=='style-1'){
			include TPGB_INCLUDES_URL. 'blog/'.sanitize_file_name('category-'.$postCategoryStyle.'.php');
		}
		if(!empty($ShowTitle) && $ShowTitle=='yes'){
			include TPGB_INCLUDES_URL. 'blog/post-title.php';
		}
		?>
		<div class="tpgb-post-hover-content">
			<?php
			if(!empty($showExcerpt) && $showExcerpt=='yes' && get_the_excerpt()){
				include TPGB_INCLUDES_URL. 'blog/get-excerpt.php';
			}

			if(!empty($showPostMeta) && $showPostMeta=='yes'){
				include TPGB_INCLUDES_URL. 'blog/'.sanitize_file_name('post-meta-'.$postMetaStyle.'.php');
			}
			?>
		</div>
	</div>

</div>
