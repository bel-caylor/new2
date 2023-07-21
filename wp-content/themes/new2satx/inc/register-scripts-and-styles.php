<?php
function add_theme_scripts() {

	wp_register_style( 'typekit-fonts', 'https://use.typekit.net/iuc3xnv.css', array(), null, 'all' ); // Typekit Fonts.
	wp_enqueue_style( 'typekit-fonts' );

	wp_register_style( 'plyr', 'https://cdn.plyr.io/3.6.8/plyr.css', array(), '3.6.8', 'all' ); // Plyr CSS.
	wp_enqueue_style( 'plyr' );

	wp_register_script( 'smoothscroll', 'https://cdnjs.cloudflare.com/ajax/libs/smooth-scroll/16.1.3/smooth-scroll.min.js', array(), '16.1.3', true ); // SmoothScroll Polyfill JS.
	wp_enqueue_script( 'smoothscroll' );

	wp_register_style( 'themestyles', get_template_directory_uri() . '/dist/styles/main.min.css', array(), filemtime( get_stylesheet_directory() . '/dist/styles/main.min.css' ), 'all' ); // Main CSS.
	wp_enqueue_style( 'themestyles' );

	wp_register_script( 'font-awesome', 'https://kit.fontawesome.com/09e89b7bde.js', array(), null, false ); // Font awesome.
	wp_enqueue_script( 'font-awesome' );

	wp_register_script( 'plyr-js', 'https://cdn.plyr.io/3.6.8/plyr.js', array(), '3.6.8', true ); // Plyr JS.
	wp_enqueue_script( 'plyr-js' );

	wp_register_script( 'tabslet', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tabslet.js/1.7.3/jquery.tabslet.min.js', array( 'jquery' ), '1.7.3', true ); // Tabslet JS.
	wp_enqueue_script( 'tabslet' );

	wp_register_script( 'vendor', get_template_directory_uri() . '/dist/scripts/vendor.min.js', array( 'jquery' ), '1.0.0.4', true ); // Vendor scripts.
	wp_enqueue_script( 'vendor' );

	wp_register_script( 'themescripts', get_template_directory_uri() . '/dist/scripts/main.min.js', array( 'jquery' ), filemtime( get_stylesheet_directory() . '/dist/scripts/main.min.js' ), true ); // Custom scripts.
	wp_enqueue_script( 'themescripts' );

	wp_register_script( 'slickslider-js', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), '3.5.7', true ); // Slick slider scripts.
	wp_enqueue_script( 'slickslider-js' );

	wp_register_script( 'fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array(), null, true ); // Fancybox scripts.
	wp_enqueue_script( 'fancybox' );

	wp_register_style( 'fancybox-styles', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), null, 'all' ); // Fancybox CSS.
	wp_enqueue_style( 'fancybox-styles' );

	wp_register_style( 'slickslider-styles', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), null, 'all' ); // Slick slider CSS.
	wp_enqueue_style( 'slickslider-styles' );

	wp_register_style( 'slickslider-theme-styles', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), null, 'all' ); // Slick slider CSS.
	wp_enqueue_style( 'slickslider-theme-styles' );

}
if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'add_theme_scripts', 11 );
}
