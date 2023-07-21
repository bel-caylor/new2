<?php if(!empty($ShowDate) && $ShowDate == 'yes') { ?>
    <span class="post-meta-date"><a href="<?php echo esc_url(get_the_permalink()); ?> " class="tpgb-dynamic-tran post-entry-date"><?php echo get_the_date(); ?></a></span>
<?php } ?>