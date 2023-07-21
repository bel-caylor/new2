<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tpgb-sf-feed tpgb-trans-linear <?php echo esc_attr($ErrorClass); ?>">
    
    <?php 
        include TPGBP_INCLUDES_URL."social-feed/social-feed-ob-style.php";
    
        if($MediaFilter == 'default' || $MediaFilter == 'ompost'){
            include TPGBP_INCLUDES_URL."social-feed/fancybox-feed.php";
        }

        if(!empty($Massage)){
            echo $Massage_html;
        }
        
        if(!empty($Description)){ 
            include TPGBP_INCLUDES_URL."social-feed/feed-Description.php";  
        }       
            echo $Header_html;

            include TPGBP_INCLUDES_URL."social-feed/feed-footer.php"; 
    ?>
</div>
