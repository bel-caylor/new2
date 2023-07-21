<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$Iconlogo = '<div class="tpgb-sf-logo">
					<a href="'.esc_url($PostLink).'" class="tpgb-sf-logo-link" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr__('Post URL','tpgbp').'">
						<i class="'.esc_attr($socialIcon).'"></i>
					</a>
				</div>';

	ob_start();
    	echo '<div class="tpgb-sf-header">';
    		if(!empty($UserImage)){
    			echo '<div class="tpgb-sf-profile"><img class="tpgb-sf-logo" src="'.esc_url($UserImage).'" alt="'.esc_attr__('User Profile','tpgbp').'"/></div>';
    		} 
    		echo '<div class="tpgb-sf-usercontact">';
    			if(!empty($UserName)){
    				echo '<div class="tpgb-sf-username">
							<a href="'.esc_url($UserLink).'" target="_blank" rel="noopener noreferrer" aria-label="'.esc_attr($UserName).'">'.wp_kses_post($UserName).'</a></div>';
    			} 
    			if(!empty($CreatedTime)){
    				echo '<div class="tpgb-sf-time">
							<a href="'.esc_url($PostLink).'" target="_blank" rel="noopener noreferrer" alt="'.esc_attr__('Post URL','tpgbp').'">'.wp_kses_post($CreatedTime).'</a></div>';
    			}   
    		echo '</div>';
    		if( (!empty($socialIcon) && $style != "style-3") || (empty($ImageURL) && $style == "style-3") ){
    			echo $Iconlogo;
    		}
    	echo '</div>';
    $Header_html = ob_get_clean();

	// Title
	$Massage_html='';
	if(!empty($ShowTitle)){
		ob_start();
			echo '<div class="tpgb-title">'.wp_kses_post($Massage).'</div>';
		$Massage_html = ob_get_clean();
	}