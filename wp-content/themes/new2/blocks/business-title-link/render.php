<?php
function new2_render_business_title_link($attributes) {
	$id = get_the_ID();
	$title   = get_the_title();
	// $title = '<h3 class="font-sans">' . $title . '</h3>';
	$link = get_field('website', $id);

	if (strncmp($link, 'http', 4) !== 0) {
		// The string doesn't start with 'http'
		$link = 'http://' . $link;
	}

    return '<a href="' . $link . '" class="font-serif text-xl" target="_blank">' . $title . '</a>';
    // return $link;
}