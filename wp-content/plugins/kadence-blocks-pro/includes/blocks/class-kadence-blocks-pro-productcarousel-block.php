<?php
/**
 * Class to Build the Product Carousel Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Product Carousel Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Productcarousel_Block extends Kadence_Blocks_Pro_Abstract_Block {

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = 'productcarousel';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = true;

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		parent::__construct();

		add_action( 'rest_api_init', array( $this, 'kadence_wc_register_rest_routes' ), 10 );

	}

	/**
	 * Register Rest API Routes for Woocommerce.
	 */
	function kadence_wc_register_rest_routes() {
		if ( class_exists( 'Kadence_REST_Blocks_Product_Categories_Controller' ) ) {
			$controller = new Kadence_REST_Blocks_Product_Categories_Controller();
			$controller->register_routes();
		}
		if ( class_exists( 'KT_REST_Blocks_Products_Controller' ) ) {
			$controller = new KT_REST_Blocks_Products_Controller();
			$controller->register_routes();
		}
	}

	/**
	 * Builds CSS for block.
	 *
	 * @param array $attributes the blocks attributes.
	 * @param Kadence_Blocks_CSS $css the css class for blocks.
	 * @param string $unique_id the blocks attr ID.
	 * @param string $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( '.kt-blocks-product-carousel-block.kt-blocks-carousel' . $unique_id );

		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		return $css->css_output();
	}

	/**
	 * This block is static, but content can be loaded after the footer.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the block output.
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {

		if ( ! wp_style_is( 'kadence-blocks-product-carousel', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-product-carousel' );
		}
		if ( ! wp_style_is( 'kadence-kb-splide', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-kb-splide' );
		}
		if ( ! wp_script_is( 'kadence-blocks-pro-splide-init', 'enqueued' ) ) {
			wp_enqueue_script( 'kadence-blocks-pro-splide-init' );
		}
		if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
			if ( ! wp_script_is( 'kadence-splide-auto-scroll', 'enqueued' ) ) {
				wp_enqueue_script( 'kadence-splide-auto-scroll' );
				global $wp_scripts;
				$script = $wp_scripts->query( 'kadence-blocks-pro-splide-init', 'registered' );
				if ( $script ) {
					if ( ! in_array( 'kadence-splide-auto-scroll', $script->deps ) ) {
						$script->deps[] = 'kadence-splide-auto-scroll';
					}
				}
			}
		}

		add_filter( 'woocommerce_product_loop_start', array( $this, 'kadence_blocks_pro_product_carousel_remove_wrap' ), 99 );
		add_filter( 'woocommerce_product_loop_end', array( $this, 'kadence_blocks_pro_product_carousel_remove_end_wrap' ), 99 );


		$anchor = !empty( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '';
		$classes = !empty( $attributes['className'] ) ? $attributes['className'] : '';

		$content .= '<div'. $anchor .' class="kt-blocks-product-carousel-block products align' . ( isset( $attributes['align'] ) ? esc_attr( $attributes['align'] ) : 'none' ) . ' kt-blocks-carousel kt-product-carousel-loop kt-blocks-carousel' . ( isset( $attributes['uniqueID'] ) ? esc_attr( $attributes['uniqueID'] ) : 'block-id' ) . ' ' . $classes . '">';
		$content .= $this->kadence_blocks_pro_render_product_carousel_query( $attributes );
		$content .= '</div>';

		remove_filter( 'woocommerce_product_loop_start', array( $this, 'kadence_blocks_pro_product_carousel_remove_wrap' ), 99 );
		remove_filter( 'woocommerce_product_loop_end', array( $this, 'kadence_blocks_pro_product_carousel_remove_end_wrap' ), 99 );

		return $content;
	}

	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {

		// Skip calling parent because this block does not have a dedicated CSS file.
		 parent::register_scripts();

		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		if ( apply_filters( 'kadence_blocks_check_if_rest', false ) && kadence_blocks_is_rest() ) {
			return;
		}
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'includes/assets/js/splide-auto-scroll.min.js', array(), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-product-carousel', KBP_URL . 'dist/style-blocks-productcarousel.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'includes/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
	}

	/**
	 * Add new product warp.
	 */
	public function kadence_blocks_pro_product_carousel_remove_wrap( $content ) {
		return apply_filters( 'kadence_blocks_carousel_woocommerce_product_loop_start', '<ul class="products columns-' . esc_attr( wc_get_loop_prop( 'columns' ) ) . '">' );
	}

	/**
	 * Add new product end wrap.
	 */
	public function kadence_blocks_pro_product_carousel_remove_end_wrap( $content ) {
		return '</ul>';
	}

	/**
	 * Server rendering for Post Block Inner Loop
	 */
	public function kadence_blocks_pro_render_product_carousel_query( $attributes ) {
		$return = '';
		$gap_unit        = ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' );
		$gap             = ( isset( $attributes['columnGap'] ) && is_numeric( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
		$gap_tablet      = ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ? $attributes['columnGapTablet'] : $gap );
		$gap_mobile      = ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ? $attributes['columnGapMobile'] : $gap_tablet );
		$auto_play       = ( isset( $attributes['autoPlay'] ) && ! $attributes['autoPlay'] ? false : true );
		$scroll_speed    = ( isset( $attributes['autoSpeed'] ) ? esc_attr( $attributes['autoSpeed'] ) : '7000' );
		$hover_pause     = ( $scroll_speed == 0 ? 'false' : 'true' );

		$auto_scroll       = ( $auto_play && isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ? true : false );
		$auto_scroll_pause = ( isset( $attributes['autoScrollPause'] ) && ! $attributes['autoScrollPause'] ? 'false' : 'true' );
		$auto_scroll_speed = ( isset( $attributes['autoScrollSpeed'] ) ? esc_attr( $attributes['autoScrollSpeed'] ) : '0.4' );
		$speed = ( $auto_scroll ? $auto_scroll_speed : $scroll_speed );

		$wrap_class   = array( 'kt-product-carousel-wrap', 'splide' );
		$wrap_class[] = 'kt-carousel-arrowstyle-' . ( isset( $attributes['arrowStyle'] ) ? esc_attr( $attributes['arrowStyle'] ) : 'whiteondark' );
		$wrap_class[] = 'kt-carousel-dotstyle-' . ( isset( $attributes['dotStyle'] ) ? esc_attr( $attributes['dotStyle'] ) : 'dark' );
		$slider_data = ' data-slider-anim-speed="' . ( isset( $attributes['transSpeed'] ) ? esc_attr( $attributes['transSpeed'] ) : '400' ) . '" data-slider-scroll="' . ( isset( $attributes['slidesScroll'] ) ? esc_attr( $attributes['slidesScroll'] ) : '1' ) . '" data-slider-dots="' . ( isset( $attributes['dotStyle'] ) && 'none' === $attributes['dotStyle'] ? 'false' : 'true' ) . '" data-slider-arrows="' . ( isset( $attributes['arrowStyle'] ) && 'none' === $attributes['arrowStyle'] ? 'false' : 'true' ) . '" data-slider-hover-pause="' . ( $auto_scroll ? esc_attr( $auto_scroll_pause ) : esc_attr( $hover_pause ) ) . '" data-slider-auto="' . ( $auto_play ? 'true' : 'false' ) . '" data-slider-auto-scroll="' . ( $auto_scroll ? 'true' : 'false' ) . '" data-slider-speed="' . esc_attr( $speed ) . '" data-slider-gap="' . esc_attr( $gap ) . '" data-slider-gap-tablet="' . esc_attr( $gap_tablet ) . '" data-slider-gap-mobile="' . esc_attr( $gap_mobile ) . '" data-slider-gap-unit="' . esc_attr( $gap_unit ) . '"';
		$columns = ( isset( $attributes['postColumns'] ) && is_array( $attributes['postColumns'] ) && 6 === count( $attributes['postColumns'] ) ? $attributes['postColumns'] : array( 2, 2, 2, 2, 1, 1 ) );
		if ( class_exists( 'Kadence\Theme' ) ) {
			if ( ! empty( $attributes['entryStyle'] ) && 'unboxed' === $attributes['entryStyle'] ) {
				$wrap_class[] = 'archive';
				$wrap_class[] = 'content-style-unboxed';
			}
		}
		$return .= '<div class="' . esc_attr( implode( ' ', $wrap_class ) ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '"' . wp_kses_post( $slider_data ) . '>';
		$carousel_init_class = 'kadence-splide-slider-init splide__track';
		$atts = array(
			'class'   => $carousel_init_class,
			'columns' => $columns[2],
			'limit'   => ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 ),
			'orderby' => ( isset( $attributes['orderBy'] ) && ! empty( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'title' ),
			'order'   => ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ? $attributes['order'] : 'ASC' ),
		);
		$type = 'products';
		if ( isset( $attributes['queryType'] ) && 'individual' === $attributes['queryType'] ) {
			$ids = array();
			if ( is_array( $attributes['postIds'] ) ) {
				foreach ( $attributes['postIds'] as $key => $value ) {
					$ids[] = $value;
				}
			}
			$atts['ids'] = implode( ',', $ids );
			$atts['limit'] = -1;
			$atts['orderby'] = 'post__in';
		} else if ( isset( $attributes['queryType'] ) && 'on_sale' === $attributes['queryType'] ) {
			$type = 'sale_products';
		} else if ( isset( $attributes['queryType'] ) && 'best_selling' === $attributes['queryType'] ) {
			$type = 'best_selling_products';
		} else if ( isset( $attributes['queryType'] ) && 'top_rated' === $attributes['queryType'] ) {
			$type            = 'top_rated_products';
			$atts['orderby'] = 'title';
			$atts['order']   = 'ASC';
		}
		if ( ! isset( $attributes['queryType'] ) || ( isset( $attributes['queryType'] ) && 'individual' !== $attributes['queryType'] ) ) {
			if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) && is_array( $attributes['categories'] ) ) {
				$categories = array();
				foreach ( $attributes['categories'] as $key => $value ) {
					$categories[] = $value['value'];
				}
				$atts['category'] = implode( ',', $categories );
				$atts['cat_operator'] = ! empty( $attributes['catOperator'] ) && 'all' === $attributes['catOperator'] ? 'AND' : 'IN';
			}
			if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) && is_array( $attributes['tags'] ) ) {
				$tags = array();
				foreach ( $attributes['tags'] as $key => $value ) {
					$tags[] = $value['value'];
				}
				$atts['tag'] = implode( ',', $tags );
			}
		}
		$atts = apply_filters( 'kadence_blocks_pro_product_carousel_atts', $atts, $attributes );
		if ( class_exists( 'WC_Shortcode_Products' ) ) {
			$shortcode = new WC_Shortcode_Products( $atts, $type );

			$return .= $shortcode->get_content();
		} else {
			$return .= '<p>' . esc_html__( 'WooCommerce Missing', 'kadence-blocks-pro' ) . '</p>';
		}
		$return .= '</div>';

		return $return;
	}
}

Kadence_Blocks_Pro_Productcarousel_Block::get_instance();
