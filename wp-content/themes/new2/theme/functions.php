<?php
/**
 * new2 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package new2
 */

if ( ! defined( 'NEW2_VERSION' ) ) {
	/*
	 * Set the theme’s version number.
	 *
	 * This is used primarily for cache busting. If you use `npm run bundle`
	 * to create your production build, the value below will be replaced in the
	 * generated zip file with a timestamp, converted to base 36.
	 */
	define( 'NEW2_VERSION', '0.1.0' );
}

if ( ! defined( 'NEW2_TYPOGRAPHY_CLASSES' ) ) {
	/*
	 * Set Tailwind Typography classes for the front end, block editor and
	 * classic editor using the constant below.
	 *
	 * For the front end, these classes are added by the `new2_content_class`
	 * function. You will see that function used everywhere an `entry-content`
	 * or `page-content` class has been added to a wrapper element.
	 *
	 * For the block editor, these classes are converted to a JavaScript array
	 * and then used by the `./javascript/block-editor.js` file, which adds
	 * them to the appropriate elements in the block editor (and adds them
	 * again when they’re removed.)
	 *
	 * For the classic editor (and anything using TinyMCE, like Advanced Custom
	 * Fields), these classes are added to TinyMCE’s body class when it
	 * initializes.
	 */
	define(
		'NEW2_TYPOGRAPHY_CLASSES',
		'prose prose-neutral max-w-none prose-a:text-primary'
	);
}

if ( ! function_exists( 'new2_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function new2_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on new2, use a find and replace
		 * to change 'new2' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'new2', get_template_directory() . '/languages' );

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

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Remove support for block templates.
		// remove_theme_support( 'block-templates' );

		// Add support for custom templates.
		// add_theme_support( 'custom-page-templates' );
	}
endif;
add_action( 'after_setup_theme', 'new2_setup' );


/**	
 * Add Custom Template Functionality
 */
function add_custom_template_support( $post_templates ) {
    $post_templates['template-custom.php'] = 'Custom Template';
    return $post_templates;
}
add_filter( 'theme_page_templates', 'add_custom_template_support' );



/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
// function new2_widgets_init() {
// register_sidebar(
// array(
// 'name'          => esc_html__( 'Sidebar', 'new2' ),
// 'id'            => 'sidebar-1',
// 'description'   => esc_html__( 'Add widgets here.', 'new2' ),
// 'before_widget' => '<section id="%1$s">',
// 'after_widget'  => '</section>',
// 'before_title'  => '<h2>',
// 'after_title'   => '</h2>',
// )
// );
// }
// add_action( 'widgets_init', 'new2_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function new2_scripts() {
	wp_enqueue_style( 'new2-style', get_stylesheet_uri(), array(), NEW2_VERSION );
	wp_enqueue_style( 'new2-style', get_stylesheet_uri(), array(), filemtime( get_template_directory() . '/style.css' ) ); // Main theme styles
	wp_enqueue_style( 'dancing-script', 'https://fonts.googleapis.com/css2?family=Dancing+Script&family=Noto+Sans:wght@100;400;600;800&display=swap', array(), null, 'all' ); // Google Font.

	wp_register_script( 'font-awesome', 'https://kit.fontawesome.com/a91e37762c.js', array(), null, false ); // Font Awesome.
	wp_enqueue_script( 'font-awesome' );

	wp_enqueue_script( 'new2-script', get_template_directory_uri() . '/js/script.min.js', array(), NEW2_VERSION, true );

	// wp_register_script( 'themescripts', get_template_directory_uri() . '/dist/frontend.js', array( 'jquery' ), filemtime( get_stylesheet_directory() . '/dist/frontend.js' ), true ); // Custom scripts.
	// wp_enqueue_script( 'themescripts' );
}
add_action( 'wp_enqueue_scripts', 'new2_scripts' );


/**
 * Enqueue the block editor script.
 */
function new2_enqueue_block_editor_script() {
	wp_enqueue_script(		'new2-editor', get_template_directory_uri() . '/js/block-editor.min.js', array(	'wp-blocks', 'wp-edit-post', ), NEW2_VERSION, true
	);
	wp_register_script( 'font-awesome', 'https://kit.fontawesome.com/a91e37762c.js', array(), null, false ); // Font Awesome.
	wp_enqueue_script( 'font-awesome' );
	
	wp_register_script( 'new2-js', get_template_directory_uri() . '/dist/editor.js', array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-i18n', 'wp-element', 'wp-editor' ) );
	wp_enqueue_style( 'typekit', 'https://use.typekit.net/tgx8twc.css', array(), null, 'all' ); // Typekit.
	// wp_enqueue_style( 'new2-style', get_template_directory_uri() . '/style-editor.css', array(), filemtime( get_template_directory() . '/style-editor.css' ) ); // Main theme styles
	// add_editor_style(get_template_directory_uri() . '/style-editor.css');

	register_block_type(
		'new2/business-loop', array(
			'render_callback' => 'new2_render_business_loop',
		)
	);
	register_block_type(
		'new2/business-title-link', array(
			'render_callback' => 'new2_render_business_title_link',
		)
	);
	register_block_type(
		'new2/church-title-link', array(
			'render_callback' => 'new2_render_church_title_link',
		)
	);	
	register_block_type(
		'new2/church-title-link', array(
			'render_callback' => 'new2_render_church_title_link',
		)
	);	
};

add_action( 'init', 'new2_enqueue_block_editor_script' );

/**
 * Create a JavaScript array containing the Tailwind Typography classes from
 * NEW2_TYPOGRAPHY_CLASSES for use when adding Tailwind Typography support
 * to the block editor.
 */
function new2_admin_scripts() {
	?>
	<script>
		tailwindTypographyClasses = '<?php echo esc_attr( NEW2_TYPOGRAPHY_CLASSES ); ?>'.split(' ');
	</script>
	<?php
}
add_action( 'admin_print_scripts', 'new2_admin_scripts' );


/**
 * Add the Tailwind Typography classes to TinyMCE.
 *
 * @param array $settings TinyMCE settings.
 * @return array
 */
function new2_tinymce_add_class( $settings ) {
	$settings['body_class'] = NEW2_TYPOGRAPHY_CLASSES;
	return $settings;
}
add_filter( 'tiny_mce_before_init', 'new2_tinymce_add_class' );

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
// require get_template_directory() . '/inc/template-functions.php';


/**
 * Functions for custom blocks.
 */
// require get_template_directory() . '/inc/block-functions.php';


/**
 * Enqueue scripts and styles.
 */
// require get_template_directory() . '/inc/register-scripts-and-styles.php';


/**
 * Add Reuseable Blocks button to admin toolbar.
 */
function be_reusable_blocks_admin_menu() {
    add_menu_page( 'Reusable Blocks', 'Reusable Blocks', 'edit_posts', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22 );
}
add_action( 'admin_menu', 'be_reusable_blocks_admin_menu' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';