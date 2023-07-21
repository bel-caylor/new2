<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package New2SATX
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

			echo '<div class="entry-content rte">';

				the_content();

			echo '</div>';

	endwhile;
endif;

get_footer();
