<?php
// add_filter( 'allowed_block_types', 'new2satx_allowed_block_types', 10, 2 );
function new2satx_allowed_block_types( $allowed_blocks, $post ) {
	return array(
		'core/image',
		'core/paragraph',
		'core/columns',
		'core/cover',
		'core/heading',
		'core/group',
		'core/list',
		'core/quote',
		'core/shortcode',
		'core/media-text',
		'core/button',
		'core/spacer',
		'core/html',
		'core/separator',
	);
}

