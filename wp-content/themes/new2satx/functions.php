<?php
/**
 * New2SATX functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package New2SATX
 */

if ( ! defined( 'KESTREL_BASE_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'KESTREL_BASE_VERSION', '1.0.0' );
}

if ( ! function_exists( 'new2satx_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function new2satx_setup() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'new2satx' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'new2satx_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		/**
		 * Add responsive embeds and block editor styles.
		 */
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'wp-block-styles' );
		add_editor_style( 'style-editor.css' );

	}
endif;
add_action( 'after_setup_theme', 'new2satx_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function new2satx_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'new2satx_content_width', 640 );
}
add_action( 'after_setup_theme', 'new2satx_content_width', 0 );

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/register-scripts-and-styles.php';
require get_template_directory() . '/inc/theme-functions.php';
require get_template_directory() . '/inc/admin-setup.php';

/**
 * Add theme support for WooCommerce.
 */
function mytheme_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

/**
 * Add theme support for page excerpt.
 */
function enable_page_excerpt() {
	add_post_type_support( 'page', array( 'excerpt' ) );
}
add_action( 'init', 'enable_page_excerpt' );


/**
 * Define which headings are allowed in default WYSIWYG.
 * Borrowed from https://www.calliaweb.co.uk/code/modify-tinymce-editor/
 *
 * @param array $init Initialize tinyMCE.
 */
function tiny_mce_remove_unused_formats( $init ) {
	$init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;';
	return $init;
}
add_filter( 'tiny_mce_before_init', 'tiny_mce_remove_unused_formats' );

/**
 * Customize the login screen.
 */
// function customize_login() {
// wp_register_style( 'loginstyles', get_template_directory_uri() . '/dist/styles/login.min.css', array(), '1.0.0.2', 'all' ); // Login CSS.
// wp_enqueue_style( 'loginstyles' );
// }
// add_action( 'login_enqueue_scripts', 'customize_login' );

/**
 * Change the Login Logo URL.
 */
function login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'login_logo_url' );


/**
 * Change the Login Logo URL title.
 */
function login_logo_url_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'login_logo_url_title' );


function add_attribute_to_script_tag( $tag, $handle ) {
	// Add script handles to the array below.
	$scripts_to_defer = array( 'font-awesome' );

	foreach ( $scripts_to_defer as $defer_script ) {
		if ( $defer_script === $handle ) {
			return str_replace( ' src', '  data-search-pseudo-elements defer crossorigin="anonymous" src', $tag );
		}
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'add_attribute_to_script_tag', 10, 2 );
