<?php 
	 $Description_HTML='';
    ob_start();
        echo '<div class="tpgb-sr-content tpgb-trans-linear">';
            include TPGBP_INCLUDES_URL. "social-reviews/social-review-showmore.php";
        echo '</div>';
    $Description_HTML .= ob_get_clean();

    // Start Icon
        $Star_HTML='';
        $Star_HTML .= '<div class="tpgb-sr-star">';
            for ($i=0; $i<$rating; $i++) {
                $Star_HTML .= '<i star-rating="'.esc_attr($i).'" class="'.esc_attr($Icon).' sr-star"></i>';
            }
        $Star_HTML .= '</div>';

    // Username
        $UserName_HTML='';
        $UserName_HTML .= '<div class="tpgb-sr-username tpgb-trans-linear">';
            $UserName_HTML .= '<a class="tpgb-trans-linear" href="'.esc_url($ULink).'" target="_blank" aria-label="'.esc_attr__( 'Review URL', 'tpgbp' ).'">'.esc_html($UName).'</a>';
        $UserName_HTML .= '</div>';

    // logo Image
        $Logo_HTML = '<a href="'.esc_url($PageLink).'" target="_blank" aria-label="'.esc_attr__( 'Page URL', 'tpgbp' ).'"><img class="tpgb-sr-logo" src="'.esc_url($Logo).'" alt="'.esc_attr__( 'Social Logo', 'tpgbp' ).'" /></a>';

    // Time
        $Time_HTML = '<div class="tpgb-sr-time tpgb-trans-linear">'.esc_html($Time).'</div>';
    
    // Profile
        $Profile_HTML = '<img class="tpgb-sr-profile" src="'.esc_url($UImage).'" alt="'.esc_attr__( 'User Profile', 'tpgbp' ).'"/>';