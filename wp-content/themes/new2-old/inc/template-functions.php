<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package new2
 */

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function new2_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'new2_pingback_header' );

/**
 * Disables lazyloading for svg files.
 *
 * @param string $src .
 */
function rocket_lazyload_exclude_src( $src ) {
	$src[] = '.svg';

	return $src;
}
add_filter( 'rocket_lazyload_excluded_src', 'rocket_lazyload_exclude_src' );
