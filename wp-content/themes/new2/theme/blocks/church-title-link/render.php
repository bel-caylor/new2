<?php
function new2_render_church_title_link($attributes) {
	$id = get_the_ID();
	$title   = get_the_title();
	if (strpos($title, "--") === true) {
		$title = strstr($title, "-", true);
	}
	$link = get_field('website', $id);
	$terms = wp_get_post_terms($id, 'sub_cat');
	$denom_code = $terms[0]->name;
	switch ($denom_code) {
		case "BA":
			$denom = "Baptist";
			break;
		case "CA":
			$denom = "Catholic";
			break;
		case "CC":
			$denom = "Church of Christ";
			break;
		case "EP":
			$denom = "Episcopal";
			break;
		case "JW":
			$denom = "Jewish";
			break;
		case "LD":
			$denom = "Later Day";
			break;
		case "LU":
			$denom = "Lutheran";
			break;
		case "ME":
			$denom = "Methodist";
			break;
		case "MU":
			$denom = "Muslim";
			break;
		case "ND":
			$denom = "Non-denom";
			break;
		case "PE":
			$denom = "Penecostal";
			break;
		case "PB":
			$denom = "Presbyterian";
			break;			
		default: {
			$denom = "";
		}
	}
	ob_start();
	// echo '<pre>';
    // var_dump($denom);
    // echo '</pre>';
	echo '<div class="inline align-middle">';
	echo '	<h3 class="text-lg inline-block font-sans pr-1">';
    echo '	<a href="http://' . $link . '"  class="denom-' . $denom_code . '" target="_blank">' . $title . '</a></h3>';
	echo '	<span class="font-serif inline text-xs whitespace-nowrap">' . $denom . '</span>';
	echo '</div>';
    return ob_get_clean();
}