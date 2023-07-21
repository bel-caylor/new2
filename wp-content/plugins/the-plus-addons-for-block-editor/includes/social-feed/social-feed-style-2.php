<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tpgb-sf-feed tpgb-trans-linear">

    <?php 
        include TPGB_INCLUDES_URL."social-feed/social-feed-ob-style.php";
            echo $Header_html;
        
        if(!empty($Massage)){
            echo $Massage_html;
        }
        
        if(!empty($Description) && empty($DescripBTM)){ 
            include TPGB_INCLUDES_URL."social-feed/feed-Description.php"; 
        }

        if($MediaFilter == 'default' || $MediaFilter == 'ompost' ){
            include TPGB_INCLUDES_URL."social-feed/fancybox-feed.php";
        }

        if(!empty($Description) && !empty($DescripBTM)){ 
            include TPGB_INCLUDES_URL."social-feed/feed-Description.php"; 
        }
            include TPGB_INCLUDES_URL."social-feed/feed-footer.php";   
    ?>

</div>