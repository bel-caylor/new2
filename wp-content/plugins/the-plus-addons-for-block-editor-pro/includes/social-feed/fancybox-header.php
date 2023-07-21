<?php 
    echo '<div class="tpgb-fcb-header">';
        if(!empty($UserImage)){
            echo '<img class="tpgb-fcb-profile" src="'.esc_url($UserImage).'" alt="'.esc_attr__('User Profile','tpgbp').'"/>';
        } 
        echo '<div class="tpgb-fcb-usercontact">';
            if(!empty($UserName)){
                echo '<div class="tpgb-fcb-username">
                        <a href="'.esc_url($UserLink).'" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($UserName).'">'.wp_kses_post($UserName).'</a>
                     </div>';
            } 
            if(!empty($CreatedTime)){ 
                echo '<div class="tpgb-fcb-time">
                        <a href="'.esc_url($PostLink).'" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($CreatedTime).'">'.wp_kses_post($CreatedTime).'</a>
                     </div>';
            } 
        echo '</div>';
        if(!empty($socialIcon)){ 
            echo '<div class="tpgb-fcb-logo"><i class="'.esc_attr($socialIcon).'"></i></div>';
        } 
    echo '</div>';
?>