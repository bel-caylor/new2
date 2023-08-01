<?php

/**
 * 3 Column w/ 2 Ads
 * Row Layout with outside column ads and 2 interblock columns.
 *
 * @link https://developer.wordpress.org/block-editor/
 *
 * @package new2
 */

$col1_type = get_field( 'ad1_individual_or_group' );
$img_ad1_mobile = get_field( 'col1_image_mobile' );
// $img_ad1_mobile = $img_ad1_mobile ? $img_ad1_mobile['id'] : 3213;
$col2_type = get_field( 'ad2_individual_or_group' );
$img_ad2_mobile = get_field( 'col2_image_mobile' );
// $img_ad2_mobile = $img_ad2_mobile ? $img_ad2_mobile['id'] : 3213;
?>

<section class="ad-layout-3 flex flex-col md:grid alignfull gap-6 grid-cols-5 lg:grid-cols-6 grid-rows-2 lg:grid-rows-1 w-full max-w-[1456px] mx-auto">
    <div class="grid-ad1">
        <?php 
        $ad_id = get_field( 'col1_desktop_ad_id' );
        if ( $col1_type == 'group') {
            echo adrotate_group($ad_id);
        } else {
            echo adrotate_ad($ad_id);
        }
         ?>
    </div>
    <div class="grid-ad1-mobile">
        <?php 
        $ad_id = get_field( 'col1_mobile_ad_id' );
        if ( $col1_type == 'group') {
            echo adrotate_group($ad_id);
        } else {
            echo adrotate_ad($ad_id);
        } 
        ?>
    </div>
    <div class="grid-cont"><InnerBlocks /></div>
    <div class="grid-ad2">
        <?php 
        $ad_id = get_field( 'col2_desktop_ad_id' );
        if ( $col2_type == 'group') {
            echo adrotate_group($ad_id);
        } else {
            echo adrotate_ad($ad_id);
        } 
        ?>
    </div>
    <div class="grid-ad2-mobile">
        <?php 
        $ad_id = get_field( 'col2_mobile_ad_id' );
        if ( $col2_type == 'group') {
            echo adrotate_group($ad_id);
        } else {
            echo adrotate_ad($ad_id);
        }
        ?>
    </div>
</section>

<style>
    .grid-ad1 {
        grid-area: ad1;
    }
    .grid-ad1-mobile {
        grid-area: ad1-mobile;
    }
    .grid-ad2 {
        grid-area: ad2;
    }
    .grid-cont {
        grid-area: cont;
    }
    .grid-ad1, .grid-ad2 {
        display: none;
    }
    .grid-ad1-mobile, .grid-ad2-mobile {
        max-height: 70vh;
    }
    .grid-ad1-mobile img, .grid-ad2-mobile img {
        max-height:  50vh;
        object-fit: contain;
        /* opacity: 0; */
    }
    /* .grid-ad1-mobile {
        background-image: url(<?php echo $img_ad1_mobile['url'] ?>);
    }
    .grid-ad1-mobile, .grid-ad2-mobile {
        background-attachment: fixed;
        background-size: contain;
        background-repeat: no-repeat;
    } */
    @media (min-width: 768px) {
        .grid-ad1, .grid-ad2 {
            display: block;
        }
        .grid-ad1-mobile, .grid-ad2-mobile {
            display: none;
        }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
        .ad-layout-3 {
            grid-template-areas:
                'cont cont cont cont ad1'
                'cont cont cont cont ad2'
        }
    }
    @media (min-width: 1024px) {
        .ad-layout-3 {
            grid-template-areas:
                'ad1 cont cont cont cont ad2'
        }
    }
    .grid-ad1 img, .grid-ad2 img {
        position: sticky;
        top: 40px;
    }
    .g {
        overflow: visible;
        height: 100%;
    }
    .g-dyn, .g-single {
        position: sticky;
        top: 0;
    }
</style>