<?php 

    defined( 'ABSPATH' ) || exit;

    if(!empty($blockTemplate)){
        echo Tpgb_Library()->plus_do_block($blockTemplate);
    }
?>