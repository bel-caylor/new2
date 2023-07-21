<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tpgb-sf-feed tpgb-trans-linear">
    <div class="tpgb-sf-contant-img tpgb-trans-easeinout-before" style="background-image: url('<?php echo esc_url($ImageURL); ?>');">
        <?php
            echo '<div class="tpgb-sf-contant tpgb-relative-block tpgb-trans-easeinout">';
            include TPGBP_INCLUDES_URL."social-feed/social-feed-ob-style.php";

            if(!empty($Massage)){
                echo $Massage_html;
            }
            if(!empty($Description)){ 
                include TPGBP_INCLUDES_URL."social-feed/feed-Description.php"; 
            }
                echo $Header_html;
                include TPGBP_INCLUDES_URL."social-feed/feed-footer.php"; 
            echo '</div>';

            include TPGBP_INCLUDES_URL."social-feed/fancybox-feed.php"; 
        ?>
    </div>
</div>