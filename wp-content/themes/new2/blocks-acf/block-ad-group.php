<?php

/**
 * Ad Group
 * Desktop/Mobile Ad

 *
 * @package new2
 */

$ad_type = get_field( 'ad_type' );
$img_ad1_mobile = get_field( 'col1_image_mobile' );
// $img_ad1_mobile = $img_ad1_mobile ? $img_ad1_mobile['id'] : 3213;
$col2_type = get_field( 'ad2_individual_or_group' );
$img_ad2_mobile = get_field( 'col2_image_mobile' );
// $img_ad2_mobile = $img_ad2_mobile ? $img_ad2_mobile['id'] : 3213;
?>

<div class="grid-ad-desktop">
    <?php 
    $ad_id = get_field( 'desktop_ad_id' );
    if ( $ad_type == 'group') {
        echo adrotate_group($ad_id);
    } else {
        echo adrotate_ad($ad_id);
    }
        ?>
</div>
<div class="grid-ad-mobile">
    <?php 
    $ad_id = get_field( 'mobile_ad_id' );
    if ( $ad_type == 'group') {
        echo adrotate_group($ad_id);
    } else {
        echo adrotate_ad($ad_id);
    } 
    ?>
</div>


<style>
    .grid-ad-desktop {
        display: none;
    }
    @media (min-width: 768px) {
        .grid-ad-desktop {
            display: block;
        }
        .grid-ad-mobile {
            display: none;
        }
    }
</style>