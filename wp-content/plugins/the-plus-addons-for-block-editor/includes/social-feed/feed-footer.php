<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo '<div class="tpgb-sf-footer">';
    if($selectFeed == 'Facebook'){
        if(isset($Fblikes)){
            echo '<span class="tpgb-btn-like">';
                if(!empty($showFooterIn) && $style == "style-2" ){
                    echo esc_html__("Like ","tpgb");
                }else{
                    echo '<img src="'.esc_url($likeImg).'" alt="'.esc_attr__('Like','tpgb').'"/>';
                    echo '<img src="'.esc_url($ReactionImg).'" alt="'.esc_attr__('Reaction','tpgb').'"/>';
                }
                echo tpgb_number_short($Fblikes);
            echo '</span>';
        }
        if(isset($comment)){
            echo '<span class="tpgb-btn-comment">';
                if(!empty($showFooterIn) && $style == "style-2" ){
                    echo esc_html__('comment ','tpgb');
                }else{
                    echo '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="comment-alt" class="svg-inline--fa fa-comment-alt fa-w-16 tpgb-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm16 352c0 8.8-7.2 16-16 16H288l-12.8 9.6L208 428v-60H64c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16h384c8.8 0 16 7.2 16 16v288z"></path></svg> ';
                }
                    echo wp_kses_post($comment); 
            echo '</span>';
        }
        if(isset($share)){
            echo '<span class="tpgb-btn-share">';
                if(!empty($showFooterIn) && $style == "style-2" ){
                    echo esc_html__("share ","tpgb");
                }else{
                    echo '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="share" class="svg-inline--fa fa-share fa-w-18 tpgb-svg" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg> ';
                }
                    echo wp_kses_post($share);
            echo '</span>';
        }
    }
echo '</div>';
