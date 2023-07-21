<?php
function new2_render_business_title_link($attributes) {
	$id = get_the_ID();
	$title   = get_the_title();
	$title = '<h3 class="font-sans">' . $title . '</h3>';
	$link = get_field('website', $id);

    return '<a href="http://' . $link . '" class="" target="_blank">' . $title . '</a>';
    // return $link;
}