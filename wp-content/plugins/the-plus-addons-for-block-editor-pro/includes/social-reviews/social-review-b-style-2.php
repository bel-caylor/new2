<div class="<?php echo "social-rb-".esc_attr($Bstyle); ?> tpgb-review tpgb-trans-linear <?php echo esc_attr($BErrClass); ?>" >   
    <div class="tpgb-batch-top">
        <?php if(!empty($BIconHidden2)){ ?>
            <img class="tpgb-sr-logo" src="<?php echo esc_url($BLogo); ?>" alt="<?php echo esc_attr__('Social Logo','tpgbp'); ?>"/>
        <?php } ?>
        <div class="tpgb-batch-contant">
            <div class="tpgb-batch-user"><?php echo esc_html($BUname); ?></div>
            <div class="tpgb-batch-start">
                <?php 
                    echo esc_html($BRating) . " ";
                    for ($i=0; $i<$BRating; $i++) {
                        echo '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($BIcon).' b-star"></i>';
                    } 
                ?>
            </div> 
            <div class="tpgb-batch-total">
                <?php echo esc_html($Btxt1)." ".esc_html($BTotal)." ".esc_html($Btxt2); ?> 
            </div>
        </div>
    </div>
    <?php if(!empty($BMassage)){
		echo esc_html($BType)." - ".wp_kses_post($BMassage);
    } ?>    
</div>