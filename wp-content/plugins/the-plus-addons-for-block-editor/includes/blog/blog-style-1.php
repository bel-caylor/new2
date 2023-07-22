<?php defined( 'ABSPATH' ) || exit; ?>
<div class="dynamic-list-content tpgb-dynamic-tran">

	<?php include TPGB_INCLUDES_URL. 'blog/format-image.php'; ?>

	<div class="tpgb-content-bottom">

		<?php if(!empty($showPostMeta) && $showPostMeta=='yes'){ ?>
			<?php include TPGB_INCLUDES_URL. 'blog/'.sanitize_file_name('post-meta-'.$postMetaStyle.'.php'); ?>
		<?php } ?>

		<?php if(!empty($ShowTitle) && $ShowTitle=='yes'){
			include TPGB_INCLUDES_URL. 'blog/post-title.php'; 
		} ?>

		<div class="tpgb-post-hover-content">
			<?php if(!empty($showExcerpt) && $showExcerpt=='yes' && get_the_excerpt()){
				include TPGB_INCLUDES_URL. 'blog/get-excerpt.php';
			} ?>
		</div>

	</div>

</div>