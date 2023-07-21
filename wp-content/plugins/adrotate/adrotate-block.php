<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2023 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_register_blocks
 Purpose:   Register and load blocks and their configuration
 Since:		5.8.24
-------------------------------------------------------------*/
function adrotate_register_blocks() {
    wp_register_script('adrotate-block', plugins_url('/library/block.js', __FILE__), array('wp-blocks', 'wp-element', 'wp-i18n'));

    register_block_type('adrotate/advert', array('editor_script' => 'adrotate-block', 'render_callback' => 'adrotate_advert_block'));
    register_block_type('adrotate/group', array('editor_script' => 'adrotate-block', 'render_callback' => 'adrotate_group_block'));
}
add_action('init', 'adrotate_register_blocks');

/*-------------------------------------------------------------
 Name:      adrotate_advert_block, adrotate_group_block
 Purpose:   Output advert or group block
 Since:		5.8.24
-------------------------------------------------------------*/
function adrotate_advert_block($attr) {
    if(!isset($attr['advert_id']) OR !is_numeric($attr['advert_id'])) return;
    return adrotate_ad($attr['advert_id']);
}

function adrotate_group_block($attr) {
    if(!isset($attr['group_id']) OR !is_numeric($attr['group_id'])) return;
    return adrotate_group($attr['group_id']);
}

/*-------------------------------------------------------------
 Name:      adrotate_add_block_category
 Purpose:   Add a category for AdRotate blocks in the Block Editor
 Since:		5.8.24
-------------------------------------------------------------*/
function adrotate_add_block_category($categories, $editor_context) {
	if(!empty($editor_context->post)) {
		$categories[] = array(
			'slug'  => 'custom-adrotate',
			'title' => __('AdRotate - Advertisements', 'adrotate'),
			'icon'  => null,
		);
	}

	return $categories;
}
add_filter('block_categories_all', 'adrotate_add_block_category', 10, 2);