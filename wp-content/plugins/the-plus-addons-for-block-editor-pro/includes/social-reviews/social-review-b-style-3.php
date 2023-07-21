<div class="<?php echo "social-rb-".esc_attr($Bstyle); ?> tpgb-review tpgb-trans-linear <?php echo esc_attr($BErrClass); ?>" >   
    <div class="tpgb-batch-top">
        <div class="tpgb-batch-number">
            <span><?php echo esc_html($BRating); ?></span>
        </div>
        <div class="tpgb-batch-contant">
            <div class="tpgb-batch-user"><?php echo esc_html($BType); ?></div>
            <?php if(!empty($BRecommend)){ ?>
               <div class="tpgb-batch-total"><?php echo esc_html($Btxt1); ?></div>
            <?php } ?>
            <div class="tpgb-batch-start">
                <?php 
                    for ($i=0; $i<$BRating; $i++) {
                        echo '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($BIcon).' b-star"></i>';
                    } 
                ?>
            </div>
        </div>
    </div>
    <?php 
        if(!empty($BMassage)){
            echo esc_html($BType)." - ".wp_kses_post($BMassage);
        } 
    ?>
</div>