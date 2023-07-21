<div class="post-meta-info post-info-style-3">
	<div class="post-author-detail">
		<?php if(!empty($ShowAuthorImg) && $ShowAuthorImg == 'yes') { ?>
			<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','tpgbp'); ?>" class="tpgb-dynamic-tran post-author-avatar"><?php global $user;  echo get_avatar( get_the_author_meta('ID'), 45); ?></a>
		<?php } ?>
		<div class="post-author-date">
			<?php if(!empty($ShowAuthor) && $ShowAuthor == 'yes')  { ?>
				<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','tpgbp'); ?>" class="tpgb-dynamic-tran post-author-name"><?php the_author_meta('display_name'); ?></a>
			<?php } ?>
			<?php include TPGBP_INCLUDES_URL. 'blog/meta-date.php'; ?>
		</div>
	</div>
</div>

