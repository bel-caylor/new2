<div class="grid-item <?php echo ($layout=='carousel' ? "splide__slide" : esc_attr($desktop_class).esc_attr($tablet_class).esc_attr($mobile_class))." ". esc_attr($category_filter)." ".esc_attr($RKey)." ".esc_attr($ReviewClass); ?>">
    <?php include TPGBP_INCLUDES_URL. "social-reviews/social-review-ob-style.php"; ?>

    <div class="tpgb-review tpgb-trans-linear <?php echo esc_attr($ErrClass); ?>">
        <?php 
            echo '<div class="tpgb-sr-header tpgb-trans-linear">';
                if(empty($disProfileIcon)){
                    echo $Profile_HTML;
                }
				echo '<div class="header-inner-content">';
					echo $UserName_HTML;
					echo $Star_HTML;
				echo '</div>';
            echo '</div>';
            echo $Description_HTML; 
        ?>

        <div class="tpgb-sr-bottom tpgb-trans-linear">
			<div class="bottom-left-content">
				<?php 
                    if(empty($disSocialIcon)){  
                        echo $Logo_HTML;
                    }
                ?>
				<div class="tpgb-sr-logotext tpgb-trans-linear" >
					<span class="tpgb-newline tpgb-trans-linear" ><?php echo esc_html__("Posted On ","tpgbp"); ?></span>
					<span class="tpgb-newline tpgb-trans-linear"><?php echo esc_html($PlatformName); ?></span>
				</div>
			</div>
            <?php echo $Time_HTML; ?>
        </div>
    </div>
</div>