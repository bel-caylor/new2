<?php
function new2_render_business_loop($attributes) {
    $args = array(
		'post_type' => 'business',
        'posts_per_page'=>-1,
        'tax_query' => array(
            array(
                'taxonomy' => 'content',
                'field'    => 'slug',
                'terms'    => $attributes['pageType'],
            ),
        ),
	);
    $businesses = new WP_Query( $args );
    // Render Business Loop
    ob_start();
    echo '<div>';
    echo $attributes['pageType'];
    echo '<ul>';
    foreach($businesses->posts as $business) {
        echo '<li>' . $business->post_title . '</li>';
    }
    echo '</ul>';
    echo '</div>';

	return ob_get_clean();
}