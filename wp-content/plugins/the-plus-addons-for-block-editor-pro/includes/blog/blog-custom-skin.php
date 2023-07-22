<?php 

    if (!defined('ABSPATH')) exit; 
    
    if( isset($block_instance) && !empty($block_instance) ){
        $block_content = (new \WP_Block(
                $block_instance,
                array(
                    'postId'   => get_the_ID(),
                )
            )
        )->render(array('dynamic' => false));
    
       echo $block_content;
    }

?>