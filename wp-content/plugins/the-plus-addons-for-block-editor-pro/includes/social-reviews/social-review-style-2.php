<div class="grid-item <?php echo ($layout=='carousel' ? "splide__slide" : esc_attr($desktop_class).esc_attr($tablet_class).esc_attr($mobile_class))." ". esc_attr($category_filter)." ".esc_attr($RKey)." ".esc_attr($ReviewClass); ?>">
    <?php include TPGBP_INCLUDES_URL. "social-reviews/social-review-ob-style.php"; ?>

    <div class="tpgb-review tpgb-trans-linear <?php echo esc_attr($ErrClass); ?>" >
        <?php
            echo '<div class="tpgb-sr-header tpgb-trans-linear">';
                if(empty($disProfileIcon)){
                    echo $Profile_HTML;
                }
                if($UserFooter == 'layout-1'){
                    echo $UserName_HTML;
                } 
                echo $Star_HTML; 
            echo '</div>';
            echo $Description_HTML; 
        ?>

        <div class="tpgb-sr-bottom tpgb-trans-linear">
            <?php 
                if($UserFooter == 'layout-2'){ 
                    echo $UserName_HTML;
                    echo $Time_HTML;
                } 
            ?>
            <div class="tpgb-sr-bottom-logo" >
                <?php 
                    if(empty($disSocialIcon)){
                        echo $Logo_HTML;
                    }
                ?>
                <div class="tpgb-sr-logotext tpgb-trans-linear" >
                    <span class="tpgb-newline tpgb-trans-linear" >
                        <?php echo esc_html__("Posted On ", "tpgbp").esc_html($PlatformName); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>