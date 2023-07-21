<?php defined( 'ABSPATH' ) || exit; ?>
<div class="post-meta-info post-info-style-2">	
	<?php if(!empty($ShowAuthor) && $ShowAuthor == 'yes')  { ?>
		<span class="post-meta-author tpgb-dynamic-tran"><?php echo wp_kses_post($authorTxt); ?> <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="author" class="tpgb-dynamic-tran"><?php echo get_the_author(); ?></a> </span>
	<?php } ?>
	<?php if(!empty($ShowDate) && $ShowDate == 'yes' && !empty($ShowAuthor) && $ShowAuthor == 'yes') {
	?>
		<span class="tpgb-dynamic-tran">/</span>
	<?php } ?>
	<?php include TPGB_INCLUDES_URL. 'blog/meta-date.php'; ?>
</div>

