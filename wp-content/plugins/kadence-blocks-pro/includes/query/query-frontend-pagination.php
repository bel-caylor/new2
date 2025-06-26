<?php

class Query_Frontend_Pagination {

	/**
	 * Server rendering for Post Block pagination.
	 *
	 * @param array $attributes the block attritbutes.
	 */
	public static function build( $parsed_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts = 0, &$return = array() ) {
		foreach ( $parsed_blocks as $block ) {
			if ( 'kadence/query-pagination' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
				$attrs            = $block['attrs'];
				$args             = array();
				$args['mid_size'] = 3;
				$args['end_size'] = 1;

				$args = array_merge( $args, self::getBlockPaginationArgs( $attrs ) );

				$return[ $attrs['uniqueID'] ] = self::get_the_posts_pagination(
					$page,
					$max_num_pages,
					apply_filters(
						'kadence_blocks_pagination_args',
						$args
					)
				);
			}
			// Recurse.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				self::build( $block['innerBlocks'], $ql_query_meta, $page, $max_num_pages, $found_posts = 0, $return );
			}
		}

		return $return;
	}

	public static function getBlockPaginationArgs( $attrs ) {
		$args = array();

		if ( ! isset( $attrs['buttonContentType'] ) || $attrs['buttonContentType'] !== 'icon' ) {
			if ( ! empty( $attrs['previousLabel'] ) ) {
				$args['prev_text'] = $attrs['previousLabel'];
			}
			if ( ! empty( $attrs['nextLabel'] ) ) {
				$args['next_text'] = $attrs['nextLabel'];
			}
		} else if ( isset( $attrs['buttonContentType'] ) && $attrs['buttonContentType'] === 'icon' ){
			$nextIcon = isset( $attrs['nextIcon'] ) ? $attrs['nextIcon'] : 'fas_arrow-right';
			$prevIcon = isset( $attrs['previousIcon'] ) ? $attrs['previousIcon'] : 'fas_arrow-left';

			$args['prev_text'] = Kadence_Blocks_Svg_Render::render( $prevIcon, 'currentColor', 1, _x( 'Previous', 'previous set of posts', 'kadence-blocks-pro' ), false);
			$args['next_text'] = Kadence_Blocks_Svg_Render::render( $nextIcon, 'currentColor', 1, _x( 'Next', 'next set of posts', 'kadence-blocks-pro' ), false);
		}

		if( isset( $attrs['showPrevNext'] ) && ! $attrs['showPrevNext'] ) {
			$args['prev_next'] = false;
		}

		return $args;

	}

	public static function get_the_posts_pagination( $page, $max_num_pages, $args = array() ) {
		$navigation = '';

		// Don't print empty markup if there's only one page.
		if ( $max_num_pages > 1 ) {
			// Make sure the nav element has an aria-label attribute: fallback to the screen reader text.
			if ( ! empty( $args['screen_reader_text'] ) && empty( $args['aria_label'] ) ) {
				$args['aria_label'] = $args['screen_reader_text'];
			}

			$args = wp_parse_args(
				$args,
				array(
					'mid_size'           => 1,
					'prev_text'          => _x( 'Previous', 'previous set of posts' ),
					'next_text'          => _x( 'Next', 'next set of posts' ),
					'screen_reader_text' => __( 'Posts navigation' ),
					'aria_label'         => __( 'Posts' ),
					'class'              => 'pagination',
				)
			);

			/**
			 * Filters the arguments for posts pagination links.
			 *
			 * @since 6.1.0
			 *
			 * @param array $args {
			 *     Optional. Default pagination arguments, see paginate_links().
			 *
			 *     @type string $screen_reader_text Screen reader text for navigation element.
			 *                                      Default 'Posts navigation'.
			 *     @type string $aria_label         ARIA label text for the nav element. Default 'Posts'.
			 *     @type string $class              Custom class for the nav element. Default 'pagination'.
			 * }
			 */
			$args = apply_filters( 'the_posts_pagination_args', $args );

			// Make sure we get a string back. Plain is the next best thing.
			if ( isset( $args['type'] ) && 'array' === $args['type'] ) {
				$args['type'] = 'plain';
			}

			// Set up paginated links.
			$links = self::paginate_links( $page, $max_num_pages, $args );

			if ( $links ) {
				$navigation = self::navigation_markup( $links, $args['class'], $args['screen_reader_text'], $args['aria_label'] );
			}
		}

		return $navigation;
	}

	public static function paginate_links( $page, $max_num_pages, $args = '' ) {
		$total   = isset( $max_num_pages ) ? $max_num_pages : 1;
		$current = $page ? $page : 1;

		// tmp values to fix undefined warnings
		$pagenum_link = '';
		$format = '';

		$defaults = array(
			'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
			'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
			'total'              => $total,
			'current'            => $current,
			'aria_current'       => 'page',
			'show_all'           => false,
			'prev_next'          => true,
			'prev_text'          => __( '&laquo; Previous' ),
			'next_text'          => __( 'Next &raquo;' ),
			'end_size'           => 1,
			'mid_size'           => 2,
			'type'               => 'plain',
			'add_args'           => array(), // Array of query args to add.
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! is_array( $args['add_args'] ) ) {
			$args['add_args'] = array();
		}

		// Who knows what else people pass in $args.
		$total = (int) $args['total'];
		if ( $total < 2 ) {
			return;
		}
		$current  = (int) $args['current'];
		$end_size = (int) $args['end_size']; // Out of bounds? Make it the default.
		if ( $end_size < 1 ) {
			$end_size = 1;
		}
		$mid_size = (int) $args['mid_size'];
		if ( $mid_size < 0 ) {
			$mid_size = 2;
		}

		$r          = '';
		$page_links = array();
		$dots       = false;

		if ( $args['prev_next'] && $current && 1 < $current ) {
			$page_links[] = sprintf(
				'<a class="prev page-numbers" href="#" data-page="%s">%s</a>',
				/**
				 * Filters the paginated links for the given archive pages.
				 *
				 * @since 3.0.0
				 *
				 * @param string $link The paginated link URL.
				 */
				apply_filters( 'paginate_links', $current - 1 ),
				$args['prev_text']
			);
		}

		for ( $n = 1; $n <= $total; $n++ ) {
			if ( $n == $current ) {
				$page_links[] = sprintf(
					'<span aria-current="%s" class="page-numbers current">%s</span>',
					esc_attr( $args['aria_current'] ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			} else {
				if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) {
					$page_links[] = sprintf(
						'<a class="page-numbers" href="#" data-page="%s">%s</a>',
						/** This filter is documented in wp-includes/general-template.php */
						apply_filters( 'paginate_links', $n ),
						$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
					);

					$dots = true;
				} elseif ( $dots && ! $args['show_all'] ) {
					$page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';

					$dots = false;
				}
			}
		}

		if ( $args['prev_next'] && $current && $current < $total ) {
			$page_links[] = sprintf(
				'<a class="next page-numbers" href="#" data-page="%s">%s</a>',
				/** This filter is documented in wp-includes/general-template.php */
				apply_filters( 'paginate_links', $current + 1 ),
				$args['next_text']
			);
		}

		switch ( $args['type'] ) {
			case 'array':
				return $page_links;

			case 'list':
				$r .= "<ul class='page-numbers'>\n\t<li>";
				$r .= implode( "</li>\n\t<li>", $page_links );
				$r .= "</li>\n</ul>\n";
				break;

			default:
				$r = implode( "\n", $page_links );
				break;
		}

		/**
		 * Filters the HTML output of paginated links for archives.
		 *
		 * @since 5.7.0
		 *
		 * @param string $r    HTML output.
		 * @param array  $args An array of arguments. See paginate_links()
		 *                     for information on accepted arguments.
		 */
		$r = apply_filters( 'paginate_links_output', $r, $args );

		return $r;
	}

	public static function navigation_markup( $links, $css_class = 'posts-navigation', $screen_reader_text = '', $aria_label = '' ) {
		if ( empty( $screen_reader_text ) ) {
			$screen_reader_text = /* translators: Hidden accessibility text. */ __( 'Posts navigation' );
		}
		if ( empty( $aria_label ) ) {
			$aria_label = $screen_reader_text;
		}

		$template = '<nav class="navigation %1$s" aria-label="%4$s">
			<h2 class="screen-reader-text">%2$s</h2>
			<div class="nav-links">%3$s</div>
		</nav>';

		/**
		 * Filters the navigation markup template.
		 *
		 * Note: The filtered template HTML must contain specifiers for the navigation
		 * class (%1$s), the screen-reader-text value (%2$s), placement of the navigation
		 * links (%3$s), and ARIA label text if screen-reader-text does not fit that (%4$s):
		 *
		 *     <nav class="navigation %1$s" aria-label="%4$s">
		 *         <h2 class="screen-reader-text">%2$s</h2>
		 *         <div class="nav-links">%3$s</div>
		 *     </nav>
		 *
		 * @since 4.4.0
		 *
		 * @param string $template  The default template.
		 * @param string $css_class The class passed by the calling function.
		 * @return string Navigation template.
		 */
		$template = apply_filters( 'navigation_markup_template', $template, $css_class );

		return sprintf( $template, sanitize_html_class( $css_class ), esc_html( $screen_reader_text ), $links, esc_attr( $aria_label ) );
	}
}
