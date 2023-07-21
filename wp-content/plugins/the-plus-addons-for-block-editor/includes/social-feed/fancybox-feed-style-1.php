<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tpgb-block-<?php echo esc_attr($block_id); ?> fancybox-si fancy-<?php echo esc_attr($FancyStyle); ?>" id="Fancy-<?php echo esc_attr($PopupSylNum); ?>" data-FancyFeedType="<?php echo $selectFeed ?>" >

    <?php
        include TPGB_INCLUDES_URL."social-feed/fancybox-feed-post.php";
        include TPGB_INCLUDES_URL."social-feed/fancybox-header.php"; 
    
        echo '<div class="tpgb-fcb-contant">';
                if(!empty($Massage)){ 
                    echo '<div class="tpgb-fcb-title">'.wp_kses_post($Massage).'</div>';
                } 
                if(!empty($Description)){ 
                    include TPGB_INCLUDES_URL."social-feed/feed-Description.php"; 
                } 
        echo '</div>';
        echo '<div class="tpgb-fcb-footer">';
            include TPGB_INCLUDES_URL."social-feed/feed-footer.php";
            echo '<div class="tpgb-btn-viewpost">
                    <a href="'.esc_url($UserLink).'" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr__('View Post','tpgb').'">'.esc_html__("View post","tpgb").'</a>
                </div>'; 
        echo '</div>';
    ?>

</div>
<?php   include TPGB_INCLUDES_URL."social-feed/popup-type.php";   ?>