<?php
use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

class Query_Frontend_Filters {

	static $return = array();

	static $object_ids = null;

	static $filter_limit = null;

	/**
	 * Server rendering for Post Block filters.
	 *
	 * @param array $parsed_blocks The parsed blocks for the query loop.
	 */
	public static function build( $parsed_blocks, $ql_query_meta, $query_builder, $query_args = array(), $lang = '' ) {
		self::$filter_limit = apply_filters( 'kadence_blocks_pro_query_frontend_filter_limit', 200 );

		foreach ( $parsed_blocks as $block ) {
			self::parse_item_from_block( $block, $ql_query_meta, $query_builder, $query_args, $lang );
			// Recurse.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				self::build( $block['innerBlocks'], $ql_query_meta, $query_builder, $query_args );
			}
		}

		return self::$return;
	}

	public static function get_post_type( $post_type ) {
		if ( ! empty( $post_type[0] ) && $post_type[0] === 'any' ) {
			// Get the post types we allow
			return array_column( kadence_blocks_pro_get_post_types( array( 'exclude_from_search' => false ) ), 'value' );
		}

		return $post_type;
	}

	public static function parse_item_from_block( $block, $ql_query_meta, $query_builder, $query_args, $lang ) {
		// Set defaults / merge options.
		$attrs    = $block['attrs'];
		$ql_facet_meta = $query_builder->facets;
		$unique_id = ! empty( $attrs['uniqueID'] ) ? $attrs['uniqueID'] : '';
		$hash = self::get_hash_from_unique_id( $unique_id, $ql_facet_meta );
		$post_type = self::get_post_type( $ql_query_meta['postType'] );
		$meta_offset = isset( $ql_query_meta['offset'] ) ? $ql_query_meta['offset'] : '';
		$inherit = isset( $ql_query_meta['inherit'] ) ? $ql_query_meta['inherit'] : '';
		$source = ! empty( $attrs['source'] ) ? $attrs['source'] : 'taxonomy' ;
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
		$post_field = ! empty( $attrs['post_field'] ) ? $attrs['post_field'] : 'post_type';

		// Get default source for filter type
		if( empty( $attrs['source'] ) ) {
			if( $block['blockName'] == 'kadence/query-filter-date') {
				$source = 'wordpress';
			} else if( class_exists( 'woocommerce' ) && in_array( $block['blockName'], ['kadence/query-filter-rating', 'kadence/query-filter-range', 'kadence/query-filter-woo-attribute' ] ) ) {
				$source = 'woocommerce';
			} else {
				$source = 'taxonomy';
			}

		}

		// get default post field for filter type
		if( empty( $attrs['post_field'] ) ) {
			if ( $block['blockName'] === 'kadence/query-filter-range' ) {
				$post_field = '_price';
			}
			if ( $block['blockName'] === 'kadence/query-filter-rating' ) {
				$post_field = '_average_rating';
			}
			if ( $block['blockName'] === 'kadence/query-filter-woo-attribute' ) {
				$post_field = '1';
			}
			if( $source === 'woocommerce' && $block['blockName'] !== 'kadence/query-filter-woo-attribute' ) {
				$post_field = '_price';
			}
		}

		if ( 'kadence/query-filter-date' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			self::$return[ $attrs['uniqueID'] ] = '<input type="date" class="kb-filter-date" />';
		} else if ( 'kadence/query-filter-rating' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			self::$return[ $attrs['uniqueID'] ] = self::build_rating_filter( $attrs );
		} else if ( 'kadence/query-filter-range' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			self::$return[ $attrs['uniqueID'] ] = self::build_range_filter( $hash, $attrs );
		} else if ( 'kadence/query-filter-search' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			self::$return[ $attrs['uniqueID'] ] = self::build_search_filter( $attrs );
		} else if ( 'kadence/query-sort' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
			self::$return[ $attrs['uniqueID'] ] = self::build_sort_filter( $attrs, $block, $placeholder, $show_result_count );
		} else if ( ! empty( $block['attrs']['uniqueID'] ) && in_array( $block['blockName'], [ 'kadence/query-filter', 'kadence/query-filter-checkbox', 'kadence/query-filter-buttons', 'kadence/query-filter-woo-attribute' ] ) ) {
			self::$return[ $attrs['uniqueID'] ] = self::build_generic_filter( $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $placeholder, $block, $query_args, $hash, $meta_offset, $lang, $inherit );
		}
	}

	public static function option_format( $value, $label, $count, $show_result_count, $slug = '', $term_order = 0 ) {
		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
			'slug' => $slug,
			'term_order' => $term_order,
		);
	}

	public static function sort_options( &$options_array, $attrs ) {
		$order_direction = ! empty( $attrs['orderDirection'] ) ? $attrs['orderDirection'] : 'DESC';
		$order_by = ! empty( $attrs['orderBy'] ) ? $attrs['orderBy'] : 'name';

		usort( $options_array, function( $a, $b ) use ( $order_direction, $order_by ) {
			$cmp = 0;
			if ( 'results' == $order_by ) {
				$cmp = $a['count'] == $b['count'] ? 0 : ( $a['count'] > $b['count'] ? 1 : -1 );
			} else if( 'term_order' === $order_by )  {
				$cmp = $a['count'] == $b['term_order'] ? 0 : ( $a['term_order'] > $b['term_order'] ? 1 : -1 );
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
				self::sort_options( $options_array[ $i ]['children'], $attrs );
			}
		}
	}

	/**
	 * Get the numeric min / max values for a filter.
	 *
	 * @param string $hash The filter hash.
	 * @param string $post_field The post meta field.
	 */
	public static function get_numeric_min_max( $hash ) {
		// TODO create a fallback for no index queries.
		$min = 0;
		$max = 100;
		$index_query = DB::table( 'kbp_query_index' )->select( 'facet_value' )->where( 'hash', $hash, '=' );
		$results = DB::get_col( DB::remove_placeholder_escape( $index_query->getSQL() ) );

		if ( ! empty( $results ) ) {
			$min = min( $results );
			$max = max( $results );
		}

		return array( $min, $max );
	}

	/**
	 * Get the hash or slug for a filter from a set .
	 *
	 * @param string $unique_id The unique ID.
	 */
	public static function get_hash_from_unique_id( $unique_id, $ql_facet_meta ) {
		$return = '';

		if ( $ql_facet_meta ) {
			foreach ( $ql_facet_meta as $facet_meta ) {
				$facet_attributes = json_decode( $facet_meta['attributes'], true );
				if ( $unique_id == $facet_attributes['uniqueID'] ) {
					$return = ! empty( $facet_attributes['slug'] ) ? $facet_attributes['slug'] : $facet_meta['hash'];
				}
			}
		}

		return $return;
	}

	public static function sort_terms_hierarchically( array &$terms, array &$into, array &$options, $show_result_count, $parent_id = 0, $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}

		foreach ( $terms as $i => $term ) {
			if ( $term->parent == $parent_id ) {
				$into[ $term->term_id ] = $term;
				$options[] = self::option_format( $term->term_id, $term->name, $term->count ?? 0, $show_result_count );
				unset( $terms[ $i ] );
			}
		}

		$i = 0;
		foreach ( $into as $top_term ) {
			$top_term->children = array();
			$options[ $i ]['children'] = array();
			self::sort_terms_hierarchically( $terms, $top_term->children, $options[ $i ]['children'], $show_result_count, $top_term->term_id, $depth + 1 );
			$i++;
		}
	}

	public static function walk_options( $options, $block, &$html, $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}

		$i = 0;
		foreach ( $options as $option ) {
			if ( 'kadence/query-filter-checkbox' === $block['blockName'] || 'kadence/query-filter-woo-attribute' === $block['blockName'] ) {
				$field_name = 'field' . $block['attrs']['uniqueID'];
				$field_id = $field_name . '_' . $i;

				$value = $option['value'];
				$swatch_class = '';
				$swatch_style = '';
				$swatch_map = ! empty( $block['attrs']['swatchMap'] ) ? $block['attrs']['swatchMap'] : array();
				if ( 'kadence/query-filter-woo-attribute' === $block['blockName'] ) {
					$swatch_option = ! empty( $swatch_map[ $option['value'] ] ) ? $swatch_map[ $option['value'] ] : array();
					$swatch_image = ! empty( $swatch_option['swatchImage'] ) ? $swatch_option['swatchImage'] : '';
					$swatch_class = 'has-swatch ' . ( $swatch_image ? 'has-image' : '' );
					$value = $option['slug'];

					// Try to populate color if not set in swatch map.
					if( !isset( $swatch_map[ $option['value'] ] ) ) {
						$swatch_style = 'background-color:' . $option['label'] . '';
					}

				}

				$html .= '
				<div class="kb-radio-check-item ' . $swatch_class . '" data-value="' . $value . '" style="margin-left: '. ( $depth * 20 ) .'px;">
				  <input class="kb-checkbox-style" id="' . $field_id . '" type="checkbox" name="' . $field_name . '[]" value="' . $value . '" style="' . $swatch_style . '">
				  <label for="' . $field_id . '">' . $option['label'] . '</label>
				</div>';
			} else if ( 'kadence/query-filter-buttons' === $block['blockName'] ) {
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
				self::walk_options( $option['children'], $block, $html, $depth + 1 );
			}
			$i++;
		}
	}

	public static function build_search_filter( $attrs ) {
		$placeholder = ! empty( $attrs['placeholder'] ) ? $attrs['placeholder'] : __( 'Search', 'kadence-blocks-pro' );
		$filter = '<div class="kb-filter-search-wrap">';
		$filter .= '<input type="text" class="kb-filter-search" placeholder="' . $placeholder . '"/>';
		$filter .= '<button class="kb-filter-search-btn" aria-label="' . __( 'Search', 'kadence-blocks-pro' ) . '">';
		$filter .= Kadence_Blocks_Svg_Render::render( 'fe_search', 'none', 3, '', true );
		$filter .= '</button>';
		$filter .= '</div>';

		return $filter;
	}

	public static function build_sort_filter( $attrs, $block, $placeholder, $show_result_count  ) {
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
				$options_array[] = self::option_format( $sort_item . '|desc', $label_text, 0, $show_result_count );
			}
			if ( $show_asc ) {
				$label_text = ! empty( $sort_item_data['textAsc'] ) ? $sort_item_data['textAsc'] : $i18n_defaults[ $sort_item ]['asc'];
				$options_array[] = self::option_format( $sort_item . '|asc', $label_text, 0, $show_result_count );
			}
		}

		// Buid the options html.
		$options_html = '';
		self::walk_options( $options_array, $block, $options_html );

		// Buid the select html.
		$filter = '';
		$blank_option = '<option value="">' . $placeholder . '</option>';
		if ( $options_html ) {
			$filter = '<select class="kb-sort">' . $blank_option . $options_html . '</select>';
		}

		return $filter;
	}

	public static function build_range_filter( $hash, $attrs ) {
		[$min, $max] = self::get_numeric_min_max( $hash );

		$css = new Kadence_Blocks_Pro_CSS();

		$slider_color = !empty( $attrs['sliderColor'] ) ? $css->sanitize_color( $attrs['sliderColor'] ) : 'var(--global-palette-9, #C6C6C6)';
		$slider_highlight_color = !empty( $attrs['sliderHighlightColor'] ) ?  $css->sanitize_color( $attrs['sliderHighlightColor'] ) : 'var(--global-palette-2, #2F2FFC)';

		return '<div class="range_container">
				<div class="form_control">
					<div class="form_control_container">
						<div class="form_control_container__label">Min</div>
						<input class="form_control_container__input fromInput" type="number" placeholder="' . $min . '" min="' . $min . '" max="' . $max . '"/>
					</div>
					<div class="form_control_container">
						<div class="form_control_container__label">Max</div>
						<input class="form_control_container__input toInput" type="number" placeholder="' . $max . '" min="' . $min . '" max="' . $max . '"/>
					</div>
				</div>
				<div class="sliders_control">
					<input class="fromSlider" type="range" value="' . $min . '" min="' . $min . '" max="' . $max . '"/>
					<input class="toSlider" data-sliderColor="'. $slider_color . '" data-sliderHighlightColor="'. $slider_highlight_color .'" type="range" value="' . $max . '" min="' . $min . '" max="' . $max . '"/>
					<div class="from-display" aria-role="presentation">' . $min . '</div>
					<div class="to-display" aria-role="presentation">' . $max . '</div>
				</div>
			</div>';

	}

	public static function build_rating_filter( $attrs ) {
		$filter = '';

		$icon = !empty( $attrs['ratingIcon'] ) ? $attrs['ratingIcon'] : 'fas_star';
		$stroke_width = isset( $attrs['iconStrokeWidth'] ) ? $attrs['iconStrokeWidth'] : 1;
		$star = Kadence_Blocks_Svg_Render::render( $icon, 'currentColor', $stroke_width, 'star', false);
		$afterText = !empty( $attrs['afterText'] ) ? $attrs['afterText'] : '';


		if( isset( $attrs['displayType'] ) && $attrs['displayType'] === 'list' ) {

			for( $i = 5; $i > 0; $i-- ) {
				$filter .= '<span class="kbp-ql-rating kbp-ql-rating-'. $i .'" data-value="'. $i .'">';

				for( $j = 0; $j < $i; $j++ ) {
					$filter .= $star;
				}

				$filter .= '</span>';
				$filter .= $afterText;
				$filter .= '<br>';
			}

		} else {
			for( $i = 0; $i < 5; $i++ ) {
				$filter .= '<span class="kbp-ql-rating kbp-ql-rating-single kbp-ql-rating-'. ( $i + 1 ) .'" data-value="'. ( $i + 1 ) .'" data-uid="'. $attrs['uniqueID'] .'">';
				$filter .= $star;
				$filter .= '</span>';
			}
			$filter .= $afterText;
		}

		return $filter;
	}

	public static function build_generic_filter( $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $placeholder, $block, $query_args, $hash, $meta_offset, $lang, $inherit ) {
		$options_array = self::build_options_array( $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, null, $hash, $lang );

		// Apply general sorting here.
		self::sort_options( $options_array, $attrs );

		// Override result counts if needed.
		if( $show_result_count ) {
			self::update_result_counts( $options_array, $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $query_args, $hash, $meta_offset, $lang, $inherit );
		}

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
		self::walk_options( $options_array, $block, $options_html );

		// Buid the select html.
		$filter = '';
		$blank_option = '<option value="">' . $placeholder . '</option>';
		if ( $options_html ) {
			if ( 'kadence/query-filter-checkbox' === $block['blockName'] || 'kadence/query-filter-buttons' === $block['blockName'] || 'kadence/query-filter-woo-attribute' === $block['blockName'] ) {
				$filter = $options_html;
			} else {
				$filter = '<select class="kb-filter">' . $blank_option . $options_html . '</select>';
			}
		}

		return $filter;
	}

	public static function get_object_ids ( $query_args, $meta_offset = 0, $inherit = '' ) {
		if ( null !== static::$object_ids ) {
			return static::$object_ids;
		}

		if ( ! empty( $query_args['post_type'] ) ) {
			$query_args['posts_per_page'] = 500;
			//unset( $query_args['post__in'] );

			//we use the offset arg in our queries as a pseudo page argument for infinite scroll
			//in the case of getting object ids for this query we don't want the page to be factored in, just the offset originally set by the user.
			$query_args['offset'] = $meta_offset;

			$our_wp_query = new WP_Query( $query_args );
			$post_ids = wp_list_pluck( $our_wp_query->posts, 'ID' );
		} else if ( $inherit ) {
			global $wp_query;
			$post_ids = wp_list_pluck( $wp_query->posts, 'ID' );
		} else {
			$post_ids = $query_args;
		}

		static::$object_ids = $post_ids;

		return static::$object_ids;
	}

	public static function build_options_array( $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $object_ids = null, $hash = '', $lang = '' ) {
		$options_array = [];

		// Format include & exclude
		$include_values = self::format_include_exclude_values( $include_values );
		$exclude_values = self::format_include_exclude_values( $exclude_values );

		// There might be a better way to get terms here by using the index if available.
		if ( 'taxonomy' == $source ) {
			//When we need to get accurate counts, as is the case when we've included object_ids, then we need to use a custom query.
			//get_terms does not update counts according to object_ids.
			if ( $hash && $object_ids ) {
				$index_query = DB::table( 'kbp_query_index' )->select( 'facet_value as slug', 'facet_name as name', 'facet_parent as parent', 'facet_order as term_order', 'facet_id as term_id', 'COUNT(*) as count' )
															->where( 'hash', $hash, '=' )
															->whereIn( 'object_id', $object_ids )
															->groupBy( 'facet_value, facet_name, facet_parent, facet_order', 'facet_id' )
															->limit( static::$filter_limit );
				$terms = $index_query->getAll();
			} else {
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'exclude'    => $exclude_values,
						'include'    => $include_values,
						'hide_empty' => true,
						'count'      => $show_result_count,
						'object_ids' => $object_ids,
						'lang'       => $lang,
					)
				);
			}

			if ( $show_children && $show_hierarchical ) {
				$sorted_terms  = array();
				self::sort_terms_hierarchically( $terms, $sorted_terms, $options_array, $show_result_count );
			} elseif ( $terms ) {
				foreach ( $terms as $term ) {
					// Only display this term if it's top level or we've indicated to display children.
					if ( ( $term->parent == 0 || $show_children ) || ( $include_values ) ) {
						$options_array[] = self::option_format( $term->term_id, $term->name, $term->count ?? 0, $show_result_count, '', $term->term_order );
					}
				}
			}
		} else {
			global $wpdb;

			if ( 'wordpress' == $source && 'post_author' == $post_field ) {
				$results = DB::table( 'posts' )
				             ->select( [ 'users.ID' => 'id', 'users.display_name' => 'name' ] )
				             ->selectRaw( 'COUNT(postsTable.post_author) AS count' )
				             ->leftJoin( 'users', 'posts.post_author', 'users.ID' )
				             ->whereIn( 'posts.post_type', $post_type )
				             ->where( 'posts.post_status', 'publish' )
				             ->groupBy( 'posts.post_author' )
				             ->limit( static::$filter_limit );

				if( $object_ids ) {
					$results->whereIn( 'id', $object_ids );
				}

				$results = $results->getAll();

				if ( is_array( $results ) && $results ) {
					$options_array = array_map( function( $result ) use ( $attrs, $show_result_count ){
						return self::option_format( $result->id, $result->name, $result->count, $show_result_count );
					}, $results );
				}
			} else if ( 'woocommerce' == $source) {
				$attribute_slug = '';

				// If post_field is numeric, it's an attribute id
				// If post_field is a string, it's post meta
				if ( is_numeric( $post_field ) ) {
					// 1 could be the default for this, it could just mean "the first attribute", so lets get that instead
					if( $post_field == '1' ) {
						$all_taxonomies = wc_get_attribute_taxonomies();
						if ( empty( $all_taxonomies ) ) {
							return $options_array;
						} else {
							$attribute      = $all_taxonomies[ array_keys( $all_taxonomies )[0] ];
							$attribute_slug = 'pa_' . $attribute->attribute_name;
						}
					} else { // post field is number, but isn't 1, so it's an attribute id
						$attribute = wc_get_attribute( $post_field );
						$attribute_slug = $attribute->slug;
					}

					$results = get_terms( [
						'taxonomy'   => $attribute_slug,
						'hide_empty' => true,
						'object_ids' => $object_ids,
					] );

					if ( is_array( $results ) && $results ) {
						$options_array = array_map( function ( $result ) use ( $attrs, $show_result_count ) {
							return self::option_format( $result->term_id, $result->name, $result->count, $show_result_count, $result->slug );
						}, $results );
					}

				} else {
					$results = DB::table( 'postmeta', 'postmeta' )
					             ->select( 'meta_value' )
					             ->selectRaw( 'COUNT(meta_value) AS count' )
								 ->leftJoin( 'posts', 'posts.id', 'postmeta.post_id', 'posts' )
					             ->whereIn( 'post_type', $post_type )
								 ->where( 'meta_key', $post_field )
								 ->where( 'posts.post_status', 'publish' )
								 ->groupBy( 'postmeta.meta_value' )
					             ->limit( static::$filter_limit );

					if( $object_ids ) {
						$results->whereIn( 'id', $object_ids );
					}

					$results = $results->getAll();
					if ( is_array( $results ) && $results ) {
						$options_array = array_map( function( $result ) use ( $attrs, $show_result_count ){
							return self::option_format( $result->meta_value, $result->meta_value, $result->count, $show_result_count, $result->meta_value );
						}, $results );
					}
				}
			} else {
				$results = DB::table( 'posts', 'posts' )
				             ->select( $post_field )
				             ->selectRaw( 'COUNT(' . $post_field . ') AS count' )
							 ->whereIn( 'post_type', $post_type )
				             ->where( 'post_status', 'publish' )
				             ->groupBy( $post_field )
				             ->limit( static::$filter_limit );

				if ( ! empty( $lang ) ) {
					$results = $results->leftJoin( 'term_relationships', 'term_relationships.object_id', 'posts.ID', 'term_relationships' )
					                   ->leftJoin( 'terms', 'terms.term_id', 'term_relationships.term_taxonomy_id', 'terms' )
					                   ->where( 'terms.slug', $lang );
				}

				if( $object_ids ) {
					$results->whereIn( 'id', $object_ids );
				}

				$results = $results->getAll();

				if( $post_field === 'post_type') {
					$post_types = get_post_types( [ ], 'objects' );
					foreach( $results as $key => $result ) {
						if( isset( $post_types[ $result->post_type ], $post_types[ $result->post_type ]->label ) ) {
							$result->label = $post_types[ $result->post_type ]->label;
						}
					}
				}

				if ( is_array( $results ) && $results ) {
					$options_array = array_map( function( $result ) use ( $post_field, $attrs, $show_result_count ) {
						$label = !empty( $result->label ) ? $result->label : $result->$post_field;
						return self::option_format( $result->$post_field, $label, $result->count, $show_result_count );
					}, $results );
				}
			}
		}

		return $options_array;
	}

	public static function update_result_counts( &$options_array, $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $query_args, $hash, $meta_offset, $lang, $inherit ) {
		$object_ids = self::get_object_ids( $query_args, $meta_offset, $inherit );
		$new_options_array = self::build_options_array( $attrs, $source, $taxonomy, $exclude_values, $include_values, $show_children, $show_hierarchical, $show_result_count, $post_type, $post_field, $object_ids, $hash, $lang );

		$options_array = self::update_options_array_counts_labels( $options_array, $new_options_array );
	}

	public static function update_options_array_counts_labels( $options_array, $new_options_array ) {
		$updated_options_array = array();

		foreach ( $options_array as $item ) {
			$updated_item = $item;

			$second_array_map = array();
			foreach ( $new_options_array as $new_options_array_item ) {
				$second_array_map[ $new_options_array_item['value'] ] = $new_options_array_item;
			}

			if ( isset( $second_array_map[ $item['value'] ] ) ) {
				$updated_item['label'] = $second_array_map[ $item['value'] ]['label'];
				$updated_item['count'] = $second_array_map[ $item['value'] ]['count'];
			} else {
				$updated_item['count'] = 0;
				$updated_item['label'] = preg_replace( '/\(\d+\)/', '(0)', $item['label'] );
			}

			if ( isset( $item['children'] ) && ! empty( $item['children'] ) ) {
				$new_options_array_children = isset( $second_array_map[ $item['value'] ] ) && isset( $second_array_map[ $item['value'] ]['children'] ) && ! empty( $second_array_map[ $item['value'] ]['children'] ) ? $second_array_map[ $item['value'] ]['children'] : array();
				$updated_item['children'] = self::update_options_array_counts_labels( $item['children'], $new_options_array_children );
			}

			$updated_options_array[] = $updated_item;
		}
		return $updated_options_array;
	}

	/**
	 * Format the input we get from the include/exclude fields to an array of ids
	 * Example: product_cat|110
	 *
	 * @param $values
	 *
	 * @return array|mixed|string[]
	 */
	public static function format_include_exclude_values( $values ) {
		if( !empty( $values ) ) {
			return array_map(function ($item) {
				if (strpos($item, "|") !== false) {
					return trim(substr($item, strpos($item, "|") + 1));
				} else {
					return $item;
				}
			}, $values );
		}

		return $values;
	}

}
