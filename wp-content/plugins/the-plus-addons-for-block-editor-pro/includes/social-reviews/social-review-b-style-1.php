<div class="<?php echo "social-rb-".esc_attr($Bstyle); ?> tpgb-review tpgb-trans-linear <?php echo esc_attr($BErrClass); ?>" >
    <div class="tpgb-batch-top">
        <div class="tpgb-batch-user"><?php echo esc_html($BType); ?></div>
        <div class="tpgb-batch-images">
            <?php foreach ($BUImage as $value) { 
                echo '<img class="tpgb-batch-img" src="'.esc_attr($value).'" alt="'.esc_attr__( 'User Profile', 'tpgbp' ).'"/>';
            } ?>
        </div>
    </div>
    <div class="tpgb-batch-rating">
        <div class="tpgb-batch-start">
            <?php 
                echo esc_html($BRating) . " ";
                for ($i=0; $i<$BRating; $i++) {
                    echo '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($BIcon).' b-star"></i>';
                } 
            ?>
        </div>
        <div class="tpgb-batch-total">
            <?php  echo esc_html($Btxt1)." ".esc_html($BTotal)." ".esc_html($Btxt2); ?> 
        </div>
    </div>
    <?php if(!empty($BMassage)){?> 
        <div class="tpgb-batch-Errormsg"><?php echo wp_kses_post($BMassage); ?></div>
    <?php } ?>

</div>
<?php if($BErrClass == "" && !empty($BRecommend)) { ?>
    <div class="tpgb-batch-recommend" >
        <div class="tpgb-batch-recommend-text">
            <?php echo esc_html($Blinktxt)." ".esc_html($BUname); ?> 
        </div>
        <div class="tpgb-batch-button-text">
            <?php 
                    echo '<a href="'.esc_url($BLink).'" class="batch-btn-yes" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($BBtnName).'">'.esc_html($BBtnName).'</a>';

                if(!empty($BSButton)) {
                    echo '<a href="#" class="batch-btn-no" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($Btn2NO).'">'.esc_html($Btn2NO).'</a>';
                } ?>
        </div>
    </div>
<?php } ?>