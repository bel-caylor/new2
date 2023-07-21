<?php

// Register the main script for the block
function gutenberg_block_editor_scripts() {
	wp_register_script(
		'new2-js',
		get_template_directory_uri() . '/dist/editor.js',
		array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-i18n', 'wp-element', 'wp-editor' )
	);
	
	wp_enqueue_script( 'new2-js' );
}
add_action( 'enqueue_block_editor_assets', 'gutenberg_block_editor_scripts' );