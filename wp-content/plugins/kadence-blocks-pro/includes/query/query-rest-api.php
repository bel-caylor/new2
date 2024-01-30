<?php
use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * REST API controller class for the query block.
 */
class Kadence_Blocks_Query_Loop_CPT_Rest_Controller extends WP_REST_Posts_Controller {

	/**
	 * page property name.
	 */
	const PROP_PAGE = 'pg';

	/**
	 * frontend property name.
	 */
	const PROP_FRONTEND = 'fe';

	/**
	 * query loop post id property name.
	 */
	const PROP_ID = 'id';

	/**
	 * facets filter property name.
	 */
	const PROP_FACETS = 'fc';

	/**
	 * dates filter property name.
	 */
	const PROP_DATES = 'dt';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/auto-draft',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_auto_draft' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/query',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'query' ),
					'permission_callback' => array( $this, 'query_permissions_check' ),
					'args'                => $this->get_query_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/query',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'query_inherit' ),
					'permission_callback' => array( $this, 'query_permissions_check' ),
					'args'                => $this->get_query_params(),
				),
			)
		);
	}

	/**
	 * Creates an auto draft.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function create_auto_draft( $request ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';

		unset( $_REQUEST['content'], $_REQUEST['excerpt'] );
		$post = get_default_post_to_edit( $this->post_type, true );

		$request->set_param( 'context', 'edit' );

		return $this->prepare_item_for_response( $post, $request );
	}

	public function query_inherit( $request ) {
		$ql_id    = $request->get_param( self::PROP_ID );
		$request_body = json_decode( $request->get_body(), true );
		$inherited_query_vars = $request_body[ $ql_id . '_wp_query_vars' ];
		$inherited_query_hash = $request_body[ $ql_id . '_wp_query_hash' ];

		if( !empty( $inherited_query_hash ) && wp_hash( $inherited_query_vars ) === $inherited_query_hash ) {
			global $wp_query;

			$inherited_query_vars = json_decode( $inherited_query_vars, true );
			$wp_query = new WP_Query( $inherited_query_vars );
		}

		return $this->query( $request );
	}

	/**
	 * Gets the html content and other data for posts retrieved by a query.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function query( $request ) {
		// global $wp_query;
		// Get Query params from post
		// Parse the params from request
		// Merge post and frontend params
		// Run the query
		// Get the post card template
		// Generate html for each post from the query via the card template.

		$page     = (int) $request->get_param( self::PROP_PAGE );
		$frontend = $request->get_param( self::PROP_FRONTEND );
		$ql_id    = $request->get_param( self::PROP_ID );
		$facets   = $request->get_param( self::PROP_FACETS );
		$dates    = $request->get_param( self::PROP_DATES );
		$posts    = array();
		$loading_class = $frontend ? ' loading' : '';

		$return = array(
			'posts' => array(),
			'pagination' => array(),
			'resultCount' => array(),
			'filters' => array(),
			'page' => 0,
			'postCount' => 0,
			'foundPosts' => 0,
			'maxNumPages' => 0,
			'postTypes' => array(),
		);

		[ $ql_post, $qlc_post ] = Kadence_Blocks_Pro_Abstract_Query_Block::get_q_posts( $ql_id );

		$post_content = isset( $ql_post->post_content ) ? $ql_post->post_content : '';
		$parsed_ql_blocks = parse_blocks( $post_content );

		if ( isset( $parsed_ql_blocks[0]['innerBlocks'] ) ) {
			$parsed_ql_blocks = $parsed_ql_blocks[0]['innerBlocks'];
		} else {
			$parsed_ql_blocks = array();
		}

		$ql_query_meta = get_post_meta( $ql_id, '_kad_query_query', true );

		$return['postTypes'] = isset( $ql_query_meta['postType'] ) ? $ql_query_meta['postType'] : array();

		$return['filters'] = $this->filters( $parsed_ql_blocks, $ql_query_meta );

		if ( isset( $qlc_post->post_content ) ) {
			// Break post content into lines.
			$block_lines = explode( PHP_EOL, $qlc_post->post_content );

			// Remove the query block card so it doesn't try and render.
			$template_content_base = preg_replace( '/<!-- wp:kadence\/query-card {.*?} -->/', '', $qlc_post->post_content );
			$template_content_base = str_replace( '<!-- wp:kadence/query-card  -->', '', $template_content_base );
			$template_content_base = str_replace( '<!-- wp:kadence/query-card -->', '', $template_content_base );
			$template_content_base = str_replace( '<!-- /wp:kadence/query-card -->', '', $template_content_base );
		} else {
			$template_content_base = '';
		}

		$post_loop_classes = array( 'kb-query-block-post' );
		$post_loop_classes = apply_filters( 'kadence-blocks-pro-query-post-classes', $post_loop_classes, $template_content_base );

		$query_builder = new Kadence_Blocks_Pro_Query_Index_Query_Builder( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks );

		$posts_in = $query_builder->build_query();

		// If false is returned, no filters were used or not all filters were indexed.
		// If an empty array was returned, there's no results.
		if ( array() === $posts_in ) {
			return rest_ensure_response( $return );
		} else if ( $posts_in !== false ) {
			$ql_query_meta['post__in'] = $posts_in;
		}

		$query_args = $this->build_query_vars_from_query_meta( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks, $query_builder );

		// Use global query if needed.
		$use_global_query = ( isset( $ql_query_meta['inherit'] ) && $ql_query_meta['inherit'] );
		if ( $use_global_query ) {
			global $wp_query;
			$query = clone $wp_query;
			// Don't override the global query if we don't need to.
			if ( ! empty( $query_args['post__in'] ) || ! empty( $query_args['s'] ) || ! empty( $query_args['paged'] ) || ! empty( $query_args['order'] ) || ! empty( $query_args['orderby'] ) ) {
				if ( empty( $query->query ) ) {
					// If global is not set then we fall back to the query args. This shouldn’t really ever happen.
					$query = new WP_Query( $query_args );
				} else {
					// Remove things that we don't want because we are inheriting.
					unset( $query_args['post_type'] );
					unset( $query_args['posts_per_page'] );
					unset( $query_args['offset'] );
					unset( $query_args['tax_query'] );
					$global_query_args = $query->query;
					// Take in tax queries. (Like woocommerce catalog visibility).
					if ( ! empty( $query->tax_query->queries ) ) {
						$tax_query = array(
							'tax_query' => $query->tax_query->queries,
						);
						$global_query_args = array_merge( $query->query, $tax_query );
					}
					// Handle posts per page.
					if ( ! empty( $query->query_vars['posts_per_page'] ) ) {
						$global_query_args['posts_per_page'] = $query->query_vars['posts_per_page'];
					}
					// There's probably a better way to do this. (Ideally we do this pre-query).
					$query = new WP_Query( array_merge( $global_query_args, $query_args ) );
				}
			}
		} else {
			$query = new WP_Query( $query_args );
		}

		if ( $query->have_posts() ) {
			$offset = 0;
			if ( ! $use_global_query && isset( $ql_query_meta['offset'] ) && is_numeric( $ql_query_meta['offset'] ) ) {
				$offset = absint( $ql_query_meta['offset'] );
			}
			$per_page = 0;
			if ( ! $use_global_query && isset( $ql_query_meta['perPage'] ) && is_numeric( $ql_query_meta['perPage'] ) ) {
				$per_page = absint( $ql_query_meta['perPage'] );
			}
			if ( $use_global_query && isset( $query->query_vars['posts_per_page'] ) ) {
				$per_page = absint( $query->query_vars['posts_per_page'] );
			}
			$post_count = $query->post_count;
			$found_posts = ! $offset ? $query->found_posts : $query->found_posts - $offset;
			$max_num_pages = ! $offset ? $query->max_num_pages : ceil( $found_posts / $per_page );

			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id              = get_the_ID();
				$post_type            = get_post_type();
				$filter_block_context = static function( $context ) use ( $post_id, $post_type ) {
					$context['postType'] = $post_type;
					$context['postId']   = $post_id;
					return $context;
				};
				add_filter( 'render_block_context', $filter_block_context );

				// Handle embeds for Query block.
				global $wp_embed;
				$template_content = $wp_embed->run_shortcode( $template_content_base );
				$template_content = $wp_embed->autoembed( $template_content );
				$template_content = do_blocks( $template_content );

				remove_filter( 'render_block_context', $filter_block_context );

				$post_classes = implode( ' ', get_post_class( $post_loop_classes ) );
				$outer_wrapper_start = '<li class="kb-query-item ' . esc_attr( $post_classes ) . esc_attr( $loading_class ) . '"><div class="kb-query-item-flip-back"></div>';
				$outer_wrapper_end = '</li>';
				$posts[] = $outer_wrapper_start . $template_content . $outer_wrapper_end;
			}

			if ( ! isset( $qlc_post->post_content ) ) {
				$posts = array( '' );

				if ( current_user_can( 'edit_others_pages' ) ) {
					$posts = array( __( 'Please select a query card in the editor.', 'kadence-blocks-pro' ) );
				}
			}

			$pagination = $this->pagination( $parsed_ql_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts );
			$result_count = $this->result_count( $parsed_ql_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts, $post_count, $per_page );

			$return = array_merge(
				$return,
				array(
					'posts' => $posts,
					'pagination' => $pagination,
					'resultCount' => $result_count,
					'page' => $page,
					'postCount' => $post_count,
					'foundPosts' => $found_posts,
					'maxNumPages' => $max_num_pages,
					'perPage' => $ql_query_meta['perPage'],
				)
			);
		}

		return rest_ensure_response( $return );
	}

	/**
	 * Permission check for the query.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function query_permissions_check( $request ) {
		return true;
	}

	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_query_params() {
		$query_params  = parent::get_collection_params();
		$query_params[ self::PROP_PAGE ] = array(
			'description' => __( 'The results page requested.', 'kadence-blocks-pro' ),
			'type'        => 'integer',
			'default'     => 1,
		);
		$query_params[ self::PROP_FRONTEND ] = array(
			'description' => __( 'If the request is coming from the frontend block.', 'kadence-blocks-pro' ),
			'type'        => 'boolean',
			'default'     => false,
		);
		$query_params[ self::PROP_ID ] = array(
			'description' => __( 'The query loop post id.', 'kadence-blocks-pro' ),
			'type'        => 'integer',
			'default'     => 0,
		);
		return $query_params;
	}

	/**
	 * Builds a WP_Query args object from a query attribute.
	 * Copy with mods of core's build_query_vars_from_query_block
	 *
	 * @return array WP_Query args.
	 */
	public function build_query_vars_from_query_meta( $ql_query_meta = null, $request = null, $ql_id = null, $parsed_ql_blocks = null, $query_builder = null ) {
		$query = array(
			'post_type'    => 'post',
			'post__not_in' => array(),
		);
		$use_global_query = ( isset( $ql_query_meta['inherit'] ) && $ql_query_meta['inherit'] );

		if ( ! $ql_query_meta ) {
			$ql_query_meta = get_post_meta( $ql_id, '_kad_query_query' );
		}

		// We're missing index and have to manually add taxonomy and other query filters.
		// AKA this is the fallback method when the index is disabled or missing
		if ( $query_builder->missing_index ) {
			$query = array_merge( $query, $this->get_query_args_from_facets( $ql_query_meta, $query_builder->facets, $request, $ql_id, $parsed_ql_blocks ) );
		}
		// Add search & sorting to query.
		if ( ! empty( $_GET[ $ql_id . '_search' ] ) ) {
			$query['s'] = trim( $_GET[ $ql_id . '_search' ] );
		}
		if ( ! empty( $_GET[ $ql_id . '_sort' ] ) ) {
			$order_parts = explode( '|', trim( $_GET[ $ql_id . '_sort' ] ) );
			$query['order'] = count( $order_parts ) == 2 ? strtoupper( $order_parts[1] ) : 'DESC';
			$query['orderby'] = count( $order_parts ) == 2 ? $order_parts[0] : 'DESC';
		}

		// Merge in frontend params.
		if ( isset( $request ) ) {
			$page = (int) $request->get_param( self::PROP_PAGE );
			if ( is_int( $page ) ) {
				$query['paged'] = $page;
			}
		}
		if ( $use_global_query && isset( $query['paged'] ) && $query['paged'] === 1 ) {
			unset( $query['paged'] );
		}

		if ( isset( $ql_query_meta ) ) {
			if ( ! empty( $ql_query_meta['postType'] ) ) {
				$post_type_param = (array) $ql_query_meta['postType'];
				foreach ( $post_type_param as $post_type ) {
					if ( is_post_type_viewable( $post_type ) ) {
						if ( ! is_array( $query['post_type'] ) ) {
							$query['post_type'] = array();
						}
						$query['post_type'][] = $post_type;
					}
				}
			}
			if ( ! empty( $ql_query_meta['taxonomy'] ) ) {
				$taxonomy_param = (array) $ql_query_meta['taxonomy'];
				$terms = array();
				$category = '';
				foreach ( $taxonomy_param as $taxonomy ) {
					$tax_parts = explode( '|', $taxonomy['value'] );
					if ( 1 < count( $tax_parts ) ) {
						$category = $tax_parts[0];
						$term = $tax_parts[1];

						$terms[] = $term;
					}
				}
				if ( $terms && $category ) {
					$query['tax_query'] = array(
						'relation' => 'OR',
						array(
							'taxonomy' => $category,
							'terms' => $terms,
						),
					);
				}
			}
			if ( isset( $ql_query_meta['sticky'] ) && ! empty( $ql_query_meta['sticky'] ) ) {
				$sticky = get_option( 'sticky_posts' );
				if ( 'only' === $ql_query_meta['sticky'] ) {
					/*
					 * Passing an empty array to post__in will return have_posts() as true (and all posts will be returned).
					 * Logic should be used before hand to determine if WP_Query should be used in the event that the array
					 * being passed to post__in is empty.
					 *
					 * @see https://core.trac.wordpress.org/ticket/28099
					 */
					$query['post__in']            = ! empty( $sticky ) ? $sticky : array( 0 );
					$query['ignore_sticky_posts'] = 1;
				} else {
					$query['post__not_in'] = array_merge( $query['post__not_in'], $sticky );
				}
			}

			if ( ! empty( $ql_query_meta['post__in'] ) ) {
				$query['post__in'] = $ql_query_meta['post__in'];
			}

			if ( ! $use_global_query && ! empty( $ql_query_meta['exclude'] ) ) {
				$excluded_post_ids     = array_map( 'intval', $ql_query_meta['exclude'] );
				$excluded_post_ids     = array_filter( $excluded_post_ids );
				$query['post__not_in'] = array_merge( $query['post__not_in'], $excluded_post_ids );
			}
			if ( ! $use_global_query && isset( $ql_query_meta['perPage'] ) && is_numeric( $ql_query_meta['perPage'] ) ) {
				$per_page = absint( $ql_query_meta['perPage'] );
				$offset   = 0;

				if ( isset( $ql_query_meta['offset'] ) && is_numeric( $ql_query_meta['offset'] ) ) {
					$offset = absint( $ql_query_meta['offset'] );
				}

				$query['offset']         = ( $per_page * ( $page - 1 ) ) + $offset;
				$query['posts_per_page'] = $per_page;
			}
			// Migrate `categoryIds` and `tagIds` to `tax_query` for backwards compatibility.
			if ( ! empty( $ql_query_meta['categoryIds'] ) || ! empty( $ql_query_meta['tagIds'] ) ) {
				$tax_query = array();
				if ( ! $use_global_query && ! empty( $ql_query_meta['categoryIds'] ) ) {
					$tax_query[] = array(
						'taxonomy'         => 'category',
						'terms'            => array_filter( array_map( 'intval', $ql_query_meta['categoryIds'] ) ),
						'include_children' => false,
					);
				}
				if ( ! $use_global_query && ! empty( $ql_query_meta['tagIds'] ) ) {
					$tax_query[] = array(
						'taxonomy'         => 'post_tag',
						'terms'            => array_filter( array_map( 'intval', $ql_query_meta['tagIds'] ) ),
						'include_children' => false,
					);
				}
				$query['tax_query'] = $tax_query;
			}
			if ( ! empty( $ql_query_meta['taxQuery'] ) ) {
				$query['tax_query'] = array();
				foreach ( $ql_query_meta['taxQuery'] as $taxonomy => $terms ) {
					if ( is_taxonomy_viewable( $taxonomy ) && ! empty( $terms ) ) {
						$query['tax_query'][] = array(
							'taxonomy'         => $taxonomy,
							'terms'            => array_filter( array_map( 'intval', $terms ) ),
							'include_children' => false,
						);
					}
				}
			}
			// Add meta order if we are not inheriting.
			if ( ! $use_global_query && isset( $ql_query_meta['order'] ) && in_array( strtoupper( $ql_query_meta['order'] ), array( 'ASC', 'DESC' ), true ) && empty( $query['order'] ) ) {
				$query['order'] = strtoupper( $ql_query_meta['order'] );
			}
			if ( ! $use_global_query && isset( $ql_query_meta['orderBy'] ) && empty( $query['orderby'] ) ) {
				$query['orderby'] = $ql_query_meta['orderBy'];
			}
			if (
				! $use_global_query && isset( $ql_query_meta['author'] )
			) {
				if ( is_array( $ql_query_meta['author'] ) ) {
					$query['author__in'] = array_filter( array_map( 'intval', $ql_query_meta['author'] ) );
				} elseif ( is_string( $ql_query_meta['author'] ) ) {
					$query['author__in'] = array_filter( array_map( 'intval', explode( ',', $ql_query_meta['author'] ) ) );
				} elseif ( is_int( $ql_query_meta['author'] ) && $ql_query_meta['author'] > 0 ) {
					$query['author'] = $ql_query_meta['author'];
				}
			}
			if ( ! empty( $ql_query_meta['search'] ) ) {
				$query['s'] = $ql_query_meta['search'];
			}
			if ( ! empty( $ql_query_meta['parents'] ) && is_post_type_hierarchical( $query['post_type'] ) ) {
				$query['post_parent__in'] = array_filter( array_map( 'intval', $ql_query_meta['parents'] ) );
			}
		}

		/**
		 * Filters the arguments which will be passed to `WP_Query` for the Query Loop (Adv) Block.
		 *
		 * Anything to this filter should be compatible with the `WP_Query` API to form
		 * the query context which will be passed down to the Query Loop Block's children.
		 * This can help, for example, to include additional settings or meta queries not
		 * directly supported by the core Query Loop Block, and extend its capabilities.
		 *
		 *
		 * @param array    $query Array containing parameters for `WP_Query` as parsed by the block context.
		 * @param array    $ql_query_meta block meta attributes.
		 * @param int      $ql_id  Current query block id.
		 */
		return apply_filters( 'kadence_blocks_pro_query_loop_query_vars', $query, $ql_query_meta, $ql_id );
	}

	public function getBlockPaginationArgs( $attrs ) {
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

	function get_the_posts_pagination( $page, $max_num_pages, $args = array() ) {
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
			$links = $this->paginate_links( $page, $max_num_pages, $args );

			if ( $links ) {
				$navigation = $this->navigation_markup( $links, $args['class'], $args['screen_reader_text'], $args['aria_label'] );
			}
		}

		return $navigation;
	}

	public function paginate_links( $page, $max_num_pages, $args = '' ) {
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

	public function navigation_markup( $links, $css_class = 'posts-navigation', $screen_reader_text = '', $aria_label = '' ) {
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

	/**
	 * Server rendering for Post Block filters.
	 *
	 * @param array $parsed_ql_blocks The parsed blocks for the query loop.
	 */
	public function filters( $parsed_blocks, $ql_query_meta, &$return = array() ) {
		foreach ( $parsed_blocks as $block ) {
			$this->parse_query_item_from_block( $block, $ql_query_meta, $return );
			// Recurse.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->filters( $block['innerBlocks'], $ql_query_meta, $return );
			}
		}

		return $return;
	}

	/**
	 * Server rendering for Post Block filters.
	 *
	 * @param array $parsed_ql_blocks The parsed blocks for the query loop.
	 */
	public function getBlockFromParsedBlocksByUniqueId( $parsed_blocks, $unique_id ) {
		foreach ( $parsed_blocks as $block ) {
			if ( ! empty( $block['attrs'] ) && ! empty( $block['attrs']['uniqueID'] ) && $unique_id == $block['attrs']['uniqueID'] ) {
				return $block;
			}
			// Recurse.
			$inner_result = false;
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$inner_result = $this->getBlockFromParsedBlocksByUniqueId( $block['innerBlocks'], $unique_id );
			}

			if ( $inner_result ) {
				return $inner_result;
			}
		}

		return false;
	}

	/**
	 * Server rendering for Post Block pagination.
	 *
	 * @param array $attributes the block attritbutes.
	 */
	public function pagination( $parsed_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts = 0, &$return = array() ) {
		foreach ( $parsed_blocks as $block ) {
			if ( 'kadence/query-pagination' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
				$attrs            = $block['attrs'];
				$args             = array();
				$args['mid_size'] = 3;
				$args['end_size'] = 1;

				$args = array_merge( $args, $this->getBlockPaginationArgs( $attrs ) );

				$return[ $attrs['uniqueID'] ] = $this->get_the_posts_pagination(
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
				$this->pagination( $block['innerBlocks'], $ql_query_meta, $page, $max_num_pages, $found_posts = 0, $return );
			}
		}

		return $return;
	}

	/**
	 * Server rendering for Post Block result count block.
	 *
	 * @param array $attributes the block attritbutes.
	 */
	public function result_count( $parsed_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts = 0, $post_count = 0, $per_page = 0, &$return = array() ) {
		foreach ( $parsed_blocks as $block ) {
			if ( 'kadence/query-result-count' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
				$attrs = $block['attrs'];
				$thousands_seperator = ! empty ( $attrs['thousandSeparator'] ) ? $attrs['thousandSeparator'] : ',';

				$start_shown = ( $per_page * ( max( $page - 1, 0 ) ) ) + 1;
				$end_shown = min($found_posts, ( $start_shown + ( $per_page - 1 ) ) );

				$start_show_formatted = number_format( $start_shown, 0, '.', $thousands_seperator );
				$end_show_formatted = number_format( $end_shown, 0, '.', $thousands_seperator );
				$found_posts_formatted = number_format( $found_posts, 0, '.', $thousands_seperator );

				$before_count = ! empty( $attrs['beforeCount'] ) ? $attrs['beforeCount'] : '';
				$through_count = ! empty( $attrs['throughCount'] ) ? $attrs['throughCount'] : '-';
				$between_count = ! empty( $attrs['betweenCount'] ) ? $attrs['betweenCount'] : 'of';
				$after_count = ! empty( $attrs['afterCount'] ) ? $attrs['afterCount'] : 'results';

				$inner_content = '';
				if ( 0 < $found_posts ) {
					$inner_content = $before_count . $start_show_formatted . $through_count . $end_show_formatted . ' ' . $between_count . ' ' . $found_posts_formatted . ' ' . $after_count;
				}

				$return[ $attrs['uniqueID'] ] = $inner_content;
			}
			// Recurse.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->result_count( $block['innerBlocks'], $ql_query_meta, $page, $max_num_pages, $found_posts, $post_count, $per_page, $return );
			}
		}
		return $return;
	}

	public function parse_query_item_from_block( $block, $ql_query_meta, &$return, $page = 0, $max_num_pages = 0, $found_posts = 0 ) {
		// Set defaults / merge options.
		$attrs    = $block['attrs'];
		$post_type = ! empty( $ql_query_meta['postType'] ) ? $ql_query_meta['postType'][0] : 'post';
		$field_type = ! empty( $attrs['fieldType'] ) ? $attrs['fieldType'] : 'post';
		$source = ! empty( $attrs['source'] ) ? $attrs['source'] : 'taxonomy';
		$post_field = ! empty( $attrs['post_field'] ) ? $attrs['post_field'] : 'post_type';
		$taxonomy = ! empty( $attrs['taxonomy'] ) ? $attrs['taxonomy'] : 'category';
		$include = ! empty( $attrs['include'] ) ? $attrs['include'] : array();
		$exclude = ! empty( $attrs['exclude'] ) ? $attrs['exclude'] : array();
		$show_children = isset( $attrs['showChildren'] ) ? $attrs['showChildren'] : true;
		$show_hierarchical = isset( $attrs['hierarchical'] ) ? $attrs['hierarchical'] : false;
		$show_result_count = isset( $attrs['showResultCount'] ) ? $attrs['showResultCount'] : false;
		$include_values = ! $include ? $include : array_map( function( $include_item ) {
			return ! empty( $include_item['value'] ) ? $include_item['value'] : '';
		}, $include );
		$exclude_values = ! $exclude ? $exclude : array_map( function( $exclude_item ) {
			return $exclude_item['value'];
		}, $exclude );
		$placeholder = ! empty( $attrs['placeholder'] ) ? $attrs['placeholder'] : __( 'Select...', 'kadence-blocks-pro' );

		if ( 'kadence/query-filter-date' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			$filter = '<input type="date" class="kb-filter-date" />';

			$return[ $attrs['uniqueID'] ] = $filter;
		} else if ( 'kadence/query-filter-search' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			$placeholder = ! empty( $attrs['placeholder'] ) ? $attrs['placeholder'] : __( 'Search', 'kadence-blocks-pro' );
			$filter = '<div class="kb-filter-search-wrap">';
			$filter .= '<input type="text" class="kb-filter-search" placeholder="' . $placeholder . '"/>';
			$filter .= '<button class="kb-filter-search-btn" aria-label="' . __( 'Search', 'kadence-blocks-pro' ) . '">';
			$filter .= Kadence_Blocks_Svg_Render::render( 'fe_search', 'none', 3, '', true );
			$filter .= '</button>';
			$filter .= '</div>';

			$return[ $attrs['uniqueID'] ] = $filter;
		} else if ( 'kadence/query-sort' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			// Fill the $options_array with the options for the dropdown filter.
			$options_array = array();

			$sort_items = ! empty( $attrs['sortItems'] ) ? $attrs['sortItems'] : array();

			$i18n_defaults = array(
				'post_id' => array(
					'asc' => __('Post ID ascending', 'kadence-blocks-pro'),
					'desc' => __('Post ID descending', 'kadence-blocks-pro'),
				),
				'post_author' => array(
					'asc' => __('Sort by author (A-Z)', 'kadence-blocks-pro'),
					'desc' => __('Sort by author (Z-A)', 'kadence-blocks-pro'),
				),
				'post_date' => array(
					'asc' => __('Sort by oldest', 'kadence-blocks-pro'),
					'desc' => __('Sort by newest', 'kadence-blocks-pro'),
				),
				'post_title' => array(
					'asc' => __('Sort by title (A-Z)', 'kadence-blocks-pro'),
					'desc' => __('Sort by title (Z-A)', 'kadence-blocks-pro'),
				),
				'post_modified' => array(
					'asc' => __('Modified recently', 'kadence-blocks-pro'),
					'desc' => __('Modified last', 'kadence-blocks-pro'),
				),
			);

			foreach ( $sort_items as $sort_item ) {
				$sort_item_data = ! empty( $attrs[ $sort_item ] ) ? $attrs[ $sort_item ] : array();
				$show_desc = $sort_item_data ? ( ! empty( $sort_item_data['showDesc'] ) && $sort_item_data['showDesc'] ) : true;
				$show_asc = $sort_item_data ? ( ! empty( $sort_item_data['showAsc'] ) && $sort_item_data['showAsc'] ) : true;

				if ( $show_desc ) {
					$label_text = ! empty( $sort_item_data['textDesc'] ) ? $sort_item_data['textDesc'] : $i18n_defaults[ $sort_item ]['desc'];
					$options_array[] = $this->option_format( $sort_item . '|desc', $label_text, 0, $attrs );
				}
				if ( $show_asc ) {
					$label_text = ! empty( $sort_item_data['textAsc'] ) ? $sort_item_data['textAsc'] : $i18n_defaults[ $sort_item ]['asc'];
					$options_array[] = $this->option_format( $sort_item . '|asc', $label_text, 0, $attrs );
				}
			}

			// Buid the options html.
			$options_html = '';
			$this->walk_options( $options_array, $block, $options_html );

			// Buid the select html.
			$filter = '';
			$blank_option = '<option value="">' . $placeholder . '</option>';
			if ( $options_html ) {
				$filter = '<select class="kb-sort">' . $blank_option . $options_html . '</select>';
			}
			$return[ $attrs['uniqueID'] ] = $filter;
		} else if ( ( 'kadence/query-filter' === $block['blockName'] || 'kadence/query-filter-checkbox' === $block['blockName'] || 'kadence/query-filter-buttons' === $block['blockName'] ) && ! empty( $block['attrs']['uniqueID'] ) ) {
			$options_array = array();

			// There might be a better way to get terms here by using the index if available.
			if ( 'taxonomy' == $source ) {
				$terms = get_terms(
					array(
						'taxonomy' => $taxonomy,
						'exclude' => $exclude_values,
						'include' => $include_values,
						//'hierarchical' => false,
						'hide_empty' => true,
						'count' => $show_result_count,
					)
				);

				if ( $show_children && $show_hierarchical ) {
					$sorted_terms = array();
					$options_array = array();
					$this->sort_terms_hierarchically( $terms, $sorted_terms, $options_array, $attrs );
				} else if ( $terms ) {
					$options_array = array();
					foreach ( $terms as $term ) {
						// Only display this term if it's top level or we've indicated to display children.
						if ( ( $term->parent == 0 || $show_children ) || ( $include_values ) ) {
							$options_array[] = $this->option_format( $term->term_id, $term->name, $term->count ?? 0, $attrs );
						}
					}
				}
			} else if ( 'wordpress' == $source ) {
				global $wpdb;
				$posts_table = $wpdb->prefix . 'posts';

				$results = array();
				if ( 'post_author' == $post_field ) {

					$results = DB::get_results(
						DB::prepare(
							"SELECT usersTable.ID AS id, usersTable.display_name AS name, COUNT(postsTable.post_author) AS count
							FROM %i AS postsTable
							LEFT JOIN wp_users usersTable
							ON postsTable.post_author = usersTable.ID
							WHERE postsTable.post_type IN (%s)
							AND postsTable.post_status = 'publish'
							GROUP BY postsTable.post_author
							LIMIT 200;",
							$posts_table,
							$post_type
						)
					);

					if ( is_array( $results ) && $results ) {
						$options_array = array_map( function( $result ) use ( $attrs ){
							return $this->option_format( $result->id, $result->name, $result->count, $attrs );
						}, $results );
					}
				} else {
					$results = DB::get_results(
						DB::prepare(
							"SELECT {$post_field}, COUNT({$post_field}) AS count
							FROM %i
							WHERE post_type IN (%s)
							AND post_status = 'publish'
							GROUP BY {$post_field}
							LIMIT 200;",
							$posts_table,
							$post_type
						)
					);

					if ( is_array( $results ) && $results ) {
						$options_array = array_map( function( $result ) use ( $post_field, $attrs ) {
							// return array();
							return $this->option_format( $result->$post_field, $result->$post_field, $result->count, $attrs );
						}, $results );
					}
				}
			}

			// Apply general sorting here.
			$this->sort_options( $options_array, $attrs );

			// Add an "All" option.
			if ( ! empty( $attrs['allOption'] ) && $attrs['allOption'] ) {
				array_unshift(
					$options_array,
					array(
						'value' => '',
						'label' => __( 'All', 'kadence-blocks-pro' ),
					)
				);
			}

			// Apply limiting result count.
			if ( ! empty( $attrs['limitItems'] ) && count( $options_array ) > $attrs['limitItems'] ) {
				$options_array = array_slice( $options_array, 0, $attrs['limitItems']);
			}

			// Buid the options html.
			$options_html = '';
			$this->walk_options( $options_array, $block, $options_html );

			// Buid the select html.
			$filter = '';
			$blank_option = '<option value="">' . $placeholder . '</option>';
			if ( $options_html ) {
				if ( 'kadence/query-filter-checkbox' === $block['blockName'] || 'kadence/query-filter-buttons' === $block['blockName'] ) {
					$filter = $options_html;
				} else {
					$filter = '<select class="kb-filter">' . $blank_option . $options_html . '</select>';
				}
			}

			$return[ $attrs['uniqueID'] ] = $filter;
		}
	}

	public function sort_options( &$options_array, $attrs ) {
		$order_direction = ! empty( $attrs['orderDirection'] ) ? $attrs['orderDirection'] : 'DESC';
		$order_by = ! empty( $attrs['orderBy'] ) ? $attrs['orderBy'] : 'name';

		usort( $options_array, function( $a, $b ) use ( $order_direction, $order_by ) {
			$cmp = 0;
			if ( 'results' == $order_by ) {
				$cmp = $a['count'] == $b['count'] ? 0 : ( $a['count'] > $b['count'] ? 1 : -1 );
			} else {
				$cmp = strcmp( strtolower( $a['label'] ), strtolower( $b['label'] ) );
			}

			if ( 'DESC' == $order_direction ) {
				$cmp = $cmp * -1;
			}
			return $cmp;
		} );

		// Also sort children if present.
		for ( $i = 0; $i < count( $options_array ); $i++ ) {
			if ( ! empty( $options_array[ $i ]['children'] ) ) {
				$this->sort_options( $options_array[ $i ]['children'], $attrs );
			}
		}
	}

	public function sort_terms_hierarchically( array &$terms, array &$into, array &$options, $attrs, $parent_id = 0, $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}

		foreach ( $terms as $i => $term ) {
			if ( $term->parent == $parent_id ) {
				$into[ $term->term_id ] = $term;
				$options[] = $this->option_format( $term->term_id, $term->name, $term->count ?? 0, $attrs );
				unset( $terms[ $i ] );
			}
		}

		$i = 0;
		foreach ( $into as $top_term ) {
			$top_term->children = array();
			$options[ $i ]['children'] = array();
			$this->sort_terms_hierarchically( $terms, $top_term->children, $options[ $i ]['children'], $attrs, $top_term->term_id, $depth + 1 );
			$i++;
		}
	}

	public function walk_options( $options, $block, &$html, $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}
		// <div class="kb-radio-check-item">
		// 	<input class="kb-checkbox-style" type="checkbox" id="field272874381b-af_0" name="field74381b-af[]" value="Option 1">
		// 	<label for="field272874381b-af_0">Option 1</label>
		// </div>

		$i = 0;
		foreach ( $options as $option ) {
			if ( 'kadence/query-filter-checkbox' === $block['blockName'] ) {
				$is_child = $depth > 0;
				$field_name = 'field' . $block['attrs']['uniqueID'];
				$field_id = $field_name . '_' . $i;
				$html .= '
				<div class="kb-radio-check-item">
				  <input class="kb-checkbox-style" id="' . $field_id . '" type="checkbox" name="' . $field_name . '[]" value="' . $option['value'] . '">
				  <label for="' . $field_id . '">' . $option['label'] . '</label>
				</div>';

			} else if ( 'kadence/query-filter-buttons' === $block['blockName'] ) {
				$is_child = $depth > 0;
				$field_name = 'field' . $block['attrs']['uniqueID'];
				$field_id = $field_name . '_' . $i;

				$classes = array( 'kb-button', 'kt-button', 'button', 'kb-query-filter-filter-button' );
				$classes[] = ! empty( $block['attrs']['sizePreset'] ) ? 'kt-btn-size-' . $block['attrs']['sizePreset'] : 'kt-btn-size-standard';
				$classes[] = ! empty( $block['attrs']['widthType'] ) ? 'kt-btn-width-type-' . $block['attrs']['widthType'] : 'kt-btn-width-type-auto';
				$classes[] = ! empty( $block['attrs']['inheritStyles'] ) ? 'kb-btn-global-' . $block['attrs']['inheritStyles'] : 'kb-btn-global-outline';
				$classes[] = ! empty( $block['attrs']['text'] ) ? 'kt-btn-has-text-true' : 'kt-btn-has-text-false';
				$classes[] = ! empty( $block['attrs']['icon'] ) ? 'kt-btn-has-svg-true' : 'kt-btn-has-svg-false';

				$button_args = array(
					'class' => implode( ' ', $classes ),
					'data-value' => $option['value'],
					'id' => $field_id,
				);
				if ( ! empty( $block['attrs']['anchor'] ) ) {
					$button_args['id'] = $block['attrs']['anchor'];
				}
				$button_args['type'] = 'submit';
				if ( ! empty( $block['attrs']['label'] ) ) {
					$button_args['aria-label'] = $block['attrs']['label'];
				}
				$button_wrap_attributes = array();
				foreach ( $button_args as $key => $value ) {
					$button_wrap_attributes[] = $key . '="' . esc_attr( $value ) . '"';
				}
				$button_wrapper_attributes = implode( ' ', $button_wrap_attributes );
				$text       = ! empty( $block['attrs']['text'] ) ? '<span class="kt-btn-inner-text">' . $block['attrs']['text'] . '</span>' : '';
				$svg_icon   = '';
				if ( ! empty( $block['attrs']['icon'] ) ) {
					$type         = substr( $block['attrs']['icon'], 0, 2 );
					$line_icon    = ( ! empty( $type ) && 'fe' == $type ? true : false );
					$fill         = ( $line_icon ? 'none' : 'currentColor' );
					$stroke_width = false;

					if ( $line_icon ) {
						$stroke_width = 2;
					}
					$svg_icon = Kadence_Blocks_Svg_Render::render( $block['attrs']['icon'], $fill, $stroke_width );
				}
				$icon_left  = ! empty( $svg_icon ) && ! empty( $block['attrs']['iconSide'] ) && 'left' === $block['attrs']['iconSide'] ? '<span class="kb-svg-icon-wrap kb-svg-icon-' . esc_attr( $block['attrs']['icon'] ) . ' kt-btn-icon-side-left">' . $svg_icon . '</span>' : '';
				$icon_right = ! empty( $svg_icon ) && ! empty( $block['attrs']['iconSide'] ) && 'right' === $block['attrs']['iconSide'] ? '<span class="kb-svg-icon-wrap kb-svg-icon-' . esc_attr( $block['attrs']['icon'] ) . ' kt-btn-icon-side-right">' . $svg_icon . '</span>' : '';
				$html_tag   = 'button';
				$content    = sprintf( '<%1$s %2$s>%3$s%4$s%5$s</%1$s>', $html_tag, $button_wrapper_attributes, $icon_left, $option['label'], $icon_right );

				$html .= '<div class="btn-inner-wrap">' . $content . '</div>';

			} else {
				$depth_indicator = str_repeat( '- ', $depth );
				$html .= '<option value="' . $option['value'] . '">' . $depth_indicator . $option['label'] . '</option>';
			}

			if ( ! empty( $option['children'] ) ) {
				$this->walk_options( $option['children'], $block, $html, $depth + 1 );
			}
			$i++;
		}
	}

	public function option_format( $value, $label, $count, $attrs ){
		$show_result_count = isset( $attrs['showResultCount'] ) ? $attrs['showResultCount'] : false;

		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
		);
	}

	public function get_query_args_from_facets( $ql_query_meta = null, $ql_facet_meta = null, $request = null, $ql_id = null, $parsed_ql_blocks = null ) {
		$query_args = array();

		// This is the fallback if we can't get indexed filters and need to generate their query args instead.
		//   Find each facet's block from the query loop parsed blocks
		//   Use those block attributes to generate the appropriate query params
		//   Remember the value should be compared to whatever the filter has been set to filter on.

		// A way to grab the filter values of of query params.
		$filter_values = array_filter(
			$_GET,
			function ( $key ) {
				return $key !== '';
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $filter_values as $hash => $value ) {
			foreach ( $ql_facet_meta as $facet_meta ) {
				if ( ( ! empty( $facet_meta['hash'] ) && $hash == $facet_meta['hash'] ) || ( ! empty( $facet_meta['slug'] ) && $hash === $facet_meta['slug'] ) ) {
					$meta_attributes = json_decode( $facet_meta['attributes'], true );
					$unique_id = $meta_attributes['uniqueID'];
					$ql_block = $this->getBlockFromParsedBlocksByUniqueId( $parsed_ql_blocks, $unique_id );
					if ( $ql_block ) {
						$block_attributes = $ql_block['attrs'];
						switch ( $ql_block['blockName'] ) {
							case 'kadence/query-filter-date':
								$date_parts = explode( '-', $value );
								$compare = $block_attributes['comparisonLogic'] ?? '<=';
								$query_args['date_query']['column'] = $block_attributes['post_field'] ?? 'post_date';
								$date_arg_parts = array(
									'year' => (int) $date_parts[0],
									'month' => (int) $date_parts[1],
									'day' => (int) $date_parts[2],
								);

								switch ( $compare ) {
									case '<':
										$query_args['date_query']['before'] = $date_arg_parts;
										break;
									case '<=':
										$query_args['date_query']['before'] = $date_arg_parts;
										$query_args['date_query']['inclusive'] = true;
										break;
									case '>':
										$query_args['date_query']['after'] = $date_arg_parts;
										break;
									case '>=':
										$query_args['date_query']['after'] = $date_arg_parts;
										$query_args['date_query']['inclusive'] = true;
										break;
									case '=':
										$query_args['date_query'][] = $date_arg_parts;
										break;
								}
								break;
							case 'kadence/query-filter':
							case 'kadence/query-filter-checkbox':
							case 'kadence/query-filter-buttons':
								$query_args['tax_query'] = array();
								$taxonomy = $block_attributes['taxonomy'] ?? 'category';
								$relation = $block_attributes['comparisonLogic'] ?? 'OR';
								$terms = explode( ',', $value );
								$tax_query = array();
								array_map( function ( $term ) use ( $taxonomy, $tax_query ) {
									$tax_query[] = array(
										'taxonomy' => $taxonomy,
										'field' => 'term_id',
										'terms' => $term,
									);
								}, $terms );
								$tax_query['relation'] = $relation;

								$query_args['tax_query'] = $tax_query;
								break;

							default:
								# code...
								break;
						}
					}
				}
			}
		}
		return $query_args;
	}
}
