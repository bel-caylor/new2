<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package New2SATX
 */

add_theme_support( 'align-wide' );


/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function new2satx_add_editor_styles_support() {
	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Enqueue editor styles.
	add_editor_style( 'dist/styles/main.min.css' );
	add_editor_style( 'https://use.typekit.net/iuc3xnv.css' );
}
add_action( 'after_setup_theme', 'new2satx_add_editor_styles_support' );

/**
 * Update CSS within in Admin (not Gutenberg pages, other editor pages).
 */
function admin_style() {
	wp_enqueue_style( 'admin-styles', get_template_directory_uri() . '/dist/styles/style-editor.css', array(), '1.0.1', 'all' );
}
add_action( 'admin_enqueue_scripts', 'admin_style' );


/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function new2satx_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'new2satx_pingback_header' );


/**
 * Move yoast seo meta to bottom
 */
function yoasttobottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'yoasttobottom' ); // Move Yoast SEO meta to bottom of page.


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function new2satx_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'new2satx_body_classes' );

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


/**
 * Adds custom image sizes to improve responsive images.
 */
function new2satx_custom_add_image_sizes() {
	add_image_size( 'small', 320, 9999 ); // 320px wide unlimited height
	add_image_size( 'medium-small', 960, 9999 ); // 960px wide unlimited height
	add_image_size( 'half', 1600, 9999 ); // 1600px wide unlimited height (used for 50% width images)
	add_image_size( 'full_screen', 2240, 9999 ); // 2240px wide unlimited height (used for 100% width images)
}
add_action( 'after_setup_theme', 'new2satx_custom_add_image_sizes' );

/**
 * Name the new sizes we've created.
 *
 * @param array $sizes Sizes for the image.
 * @return array
 */
function new2satx_custom_add_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'small'        => __( 'Small' ),
			'medium-small' => __( 'Medium Small' ),
			'half'         => __( 'Half' ),
			'full_screen'  => __( 'Full Screen' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'new2satx_custom_add_image_size_names' );


/**
 * Add ACF Options Page
 */
function register_acf_options_pages() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => 'Site Options ',
				'menu_title' => 'Site Options',
				'menu_slug'  => 'new2satx-general-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);
	}
}
add_filter( 'acf/init', 'register_acf_options_pages' );


/**
 * Remove tabindex from Gravity Forms
 */
add_filter( 'gform_tabindex', '__return_false' );


/**
 * Define the toolbar (buttons) which are rendered onto the tinyMCE object
 *
 * @param array $toolbars Define the toolbars.
 */
function my_toolbars( $toolbars ) {

	// Add a new toolbar called "Very Simple"
	// _this toolbar has only 1 row of buttons.
	$toolbars['Very Simple']    = array();
	$toolbars['Very Simple'][1] = array( 'bold', 'italic', 'underline', 'link', 'bullist', 'numlist' );

	// return $toolbars_IMPORTANT!
	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'my_toolbars' );


/**
 * Creating a custom new2satx block category.
 *
 * @param   array $categories     List of block categories.
 * @return  array
 */
function new2satx_block_category( $categories ) {

	$custom_block = array(
		'slug'  => 'new2satx',
		'title' => esc_html__( 'New2SATX', 'new2satx' ),
		'icon'  => 'block-default',
	);

	$categories_sorted    = array();
	$categories_sorted[0] = $custom_block;

	foreach ( $categories as $category ) {
		$categories_sorted[] = $category;
	}

	return $categories_sorted;
}
add_filter( 'block_categories_all', 'new2satx_block_category' );

/**
 * Set up the ACF blocks
 */
function new2satx_acf_register_blocks() {
	// Check function exists.
	if ( function_exists( 'acf_register_block' ) ) {

	}
}
add_action( 'acf/init', 'new2satx_acf_register_blocks' );


/**
 * Change the MCE defaults
 *
 * @param array $in .
 */
function change_mce_defaults( $in ) {
	// Keep the "kitchen sink" open.
	$in['wordpress_adv_hidden'] = false;
	return $in;
}
add_filter( 'tiny_mce_before_init', 'change_mce_defaults' );


/**
 * Gutenberg scripts and styles
 *
 * @link https://www.billerickson.net/wordpress-color-palette-button-styling-gutenberg
 */
function new2satx_gutenberg_scripts() {
	wp_enqueue_script( 'new2satx-editor', get_stylesheet_directory_uri() . '/dist/scripts/editor.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-api', 'wp-edit-post' ), filemtime( get_stylesheet_directory() . '/dist/scripts/editor.js' ), true );
}
add_action( 'enqueue_block_editor_assets', 'new2satx_gutenberg_scripts' );


/**
 * Gfield classes.
 *
 * @param array $classes .
 * @param array $field .
 */
function new2satx_gfield( $classes, $field ) {
	$classes .= ' gfield_type_' . $field->type;
	return $classes;
}
add_filter( 'gform_field_css_class', 'new2satx_gfield', 10, 2 );



/**
 * Prep string for ID.
 *
 * @param string $string .
 */
function prep_string_for_id( $string ) {
	$string = strtolower( $string );
	$string = html_entity_decode( $string );
	$string = preg_replace( '/[\s_]/', '-', $string );
	return $string;
}


/**
 * Walker for nav links.
 */
class Walker_Add_Link_Attributes extends Walker_Nav_Menu {
	/**
	 * Start walker.
	 *
	 * @param string $output .
	 * @param string $item .
	 * @param string $depth .
	 * @param string $args .
	 * @param string $id .
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Filters the CSS classes applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		global $current_post_title;

		$atts           = array();
		$item_slug      = ! empty( $item->title ) ? prep_string_for_id( $item->title ) : '';
		$atts['id']     = 'link__' . $current_post_title . '__menu__' . $args->menu_id . '__' . $item_slug;
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		if ( '_blank' === $item->target && empty( $item->xfn ) ) {
			$atts['rel'] = 'noopener noreferrer';
		} else {
			$atts['rel'] = $item->xfn;
		}
		$atts['href']         = ! empty( $item->url ) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title        Title attribute.
		 *     @type string $target       Target attribute.
		 *     @type string $rel          The rel attribute.
		 *     @type string $href         The href attribute.
		 *     @type string $aria_current The aria-current attribute.
		 * }
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// insert description for top level elements only.
		$description = ( ! empty( $item->description ) && 0 === $depth )
			? '<div class="nav__description">' . $item->description . '</div>' : '';

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . '<span class="nav__link-title">' . $title . '</span>' . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $description;
		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}


// add additional ACF WYSIWYG options
function wpb_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'wpb_mce_buttons_2' );

/*
* Callback function to filter the MCE settings
*/

function my_mce_before_init_insert_formats( $init_array ) {

	$style_formats = array(
		array(
			'title'   => 'Larger Text',
			'block'   => 'span',
			'classes' => 'leading-paragraph',
			'wrapper' => true,

		),
	);
	$init_array['style_formats'] = json_encode( $style_formats );

	return $init_array;

}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );


/**
 * Returns the primary term for the chosen taxonomy set by Yoast SEO
 * or the first term selected.
 *
 * @link https://www.tannerrecord.com/how-to-get-yoasts-primary-category/
 * @param integer $post The post id.
 * @param string  $taxonomy The taxonomy to query. Defaults to category.
 * @return array The term with keys of 'title', 'slug', and 'url'.
 */
function get_primary_taxonomy_term( $post = 0, $taxonomy = 'category' ) {
	if ( ! $post ) {
		$post = get_the_ID();
	}

	$terms        = get_the_terms( $post, $taxonomy );
	$primary_term = array();

	if ( $terms ) {
		$term_display = '';
		$term_slug    = '';
		$term_link    = '';
		if ( class_exists( 'WPSEO_Primary_Term' ) ) {
			$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			$term               = get_term( $wpseo_primary_term );
			if ( is_wp_error( $term ) ) {
				$term_display = $terms[0]->name;
				$term_slug    = $terms[0]->slug;
				$term_link    = get_term_link( $terms[0]->term_id );
			} else {
				$term_display = $term->name;
				$term_slug    = $term->slug;
				$term_link    = get_term_link( $term->term_id );
			}
		} else {
			$term_display = $terms[0]->name;
			$term_slug    = $terms[0]->slug;
			$term_link    = get_term_link( $terms[0]->term_id );
		}
		$primary_term['url']   = $term_link;
		$primary_term['slug']  = $term_slug;
		$primary_term['title'] = $term_display;
	}
	return $primary_term;
}

// remove post title from Yoast breadcrumbs.
add_filter( 'wpseo_breadcrumb_single_link', 'remove_breadcrumb_title' );
function remove_breadcrumb_title( $link_output ) {
	if ( strpos( $link_output, 'breadcrumb_last' ) !== false ) {
		$link_output = '';
	}
	return $link_output;
}



// Remove the filter that strips HTML tags from the menu item description.
remove_filter( 'nav_menu_description', 'strip_tags' );


// Add a function that outputs any HTML tags in the menu item description.
function new2satx_wp_setup_nav_menu_item( $menu_item ) {
	if ( isset( $menu_item->post_type ) ) {
		if ( 'nav_menu_item' == $menu_item->post_type ) {
			$menu_item->description = apply_filters( 'nav_menu_description', $menu_item->post_content );
		}
	}

	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'new2satx_wp_setup_nav_menu_item' );




/**
 * Format a phone number from 5555555555 to 555.555.5555
 *
 * @param string $number
 */
function format_phone_number( $number ) {
	if ( preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $number, $matches ) ) {
		$result = $matches[1] . '.' . $matches[2] . '.' . $matches[3];
		return $result;
	} else {
		return $number;
	}
}

/**
 * Allowed tags for use with wpse_custom_wp_trim_excerpt
 */
function wpse_allowedtags() {
	// Add custom tags to this string
	return '<script>,<style>,<br>,<em>,<i>,<ul>,<ol>,<li>,<a>,<p>,<img>,<video>,<audio>';
}

/**
 * Custom Excerpts handler.
 * Usage get_the_excerpt([post-id])
 * Reference: https://wordpress.stackexchange.com/a/141136
 */
if ( ! function_exists( 'wpse_custom_wp_trim_excerpt' ) ) :

	function wpse_custom_wp_trim_excerpt( $wpse_excerpt ) {
		$raw_excerpt = $wpse_excerpt;
		if ( '' == $wpse_excerpt ) {
			$wpse_excerpt = get_the_content( '' );
			$wpse_excerpt = strip_shortcodes( $wpse_excerpt );
			$wpse_excerpt = apply_filters( 'the_content', $wpse_excerpt );
			$wpse_excerpt = str_replace( ']]>', ']]&gt;', $wpse_excerpt );
			$wpse_excerpt = strip_tags( $wpse_excerpt, wpse_allowedtags() ); /*IF you need to allow just certain tags. Delete if all tags are allowed */

			// Set the excerpt word count and only break after sentence is complete.
				$excerpt_word_count = 50;
				$excerpt_length     = apply_filters( 'excerpt_length', $excerpt_word_count );
				$tokens             = array();
				$excerptOutput      = '';
				$count              = 0;

				// Divide the string into tokens; HTML tags, or words, followed by any whitespace
				preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens );

			foreach ( $tokens[0] as $token ) {

				if ( $count >= $excerpt_length && preg_match( '/[\,\;\?\.\!]\s*$/uS', $token ) ) {
					// Limit reached, continue until , ; ? . or ! occur at the end
					$excerptOutput .= trim( $token );
					break;
				}

				// Add words to complete sentence
				$count++;

				// Append what's left of the token
				$excerptOutput .= $token;
			}

			$wpse_excerpt = trim( force_balance_tags( $excerptOutput ) );

				$excerpt_end  = ' <a href="' . esc_url( get_permalink() ) . '">' . '&nbsp;&raquo;&nbsp;' . sprintf( __( 'Read more about: %s &nbsp;&raquo;', 'wpse' ), get_the_title() ) . '</a>';
				$excerpt_more = apply_filters( 'excerpt_more', ' ' . $excerpt_end );

				// $pos = strrpos($wpse_excerpt, '</');
				// if ($pos !== false)
				// Inside last HTML tag
				// $wpse_excerpt = substr_replace($wpse_excerpt, $excerpt_end, $pos, 0); /* Add read more next to last word */
				// else
				// After the content
				$wpse_excerpt .= $excerpt_more; /*Add read more in new paragraph */

			return $wpse_excerpt;

		}
		return apply_filters( 'wpse_custom_wp_trim_excerpt', $wpse_excerpt, $raw_excerpt );
	}

endif;
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'wpse_custom_wp_trim_excerpt' );

/**
 * Define custom excerpt length.
 *
 * @param array $length Define the excerpt length.
 */
function new2satx_custom_excerpt_length( $length ) {
	return 50;
}
add_filter( 'excerpt_length', 'new2satx_custom_excerpt_length', 999 );

/**
 * Replaces the excerpt "Read More" text by a link.
 *
 * @param array $more Define the excerpt length.
 */
function new2satx_excerpt_more( $more ) {
	global $post;
	return '...';
}
add_filter( 'excerpt_more', 'new2satx_excerpt_more' );
