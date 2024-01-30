<?php
/**
 * Query Builder
 *
 * @package Kadence Blocks Pro
 */

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\WhereQueryBuilder;

class Kadence_Blocks_Pro_Query_Index_Query_Builder {

	public $query_meta;

	public $request;
	public $ql_id;
	public $parsed_ql_blocks;
	public $missing_index = false;

	public $filters = array();
	public $facets = array();
	public $global_compare = 'AND';
	public $extra_results = null;

	public function __construct( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks ) {
		$this->query_meta       = $ql_query_meta;
		$this->request          = $request;
		$this->ql_id            = $ql_id;
		$this->parsed_ql_blocks = $parsed_ql_blocks;
		$this->facets           = $this->get_facets();
		$this->filters          = $this->get_filters();
		$this->global_compare    = ! empty( $this->query_meta['comparisonLogic'] ) ? $this->query_meta['comparisonLogic'] : 'AND';
	}

	public function build_query() {
		$index_query = DB::table( 'kbp_query_index' )->select( 'object_id' );

		// No facets or filters found.
		if ( empty( $this->filters ) || empty( $this->facets ) ) {
			return false;
		}

		// Bail out if indexing is disabled.
		if ( apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false ) ) {
			$this->missing_index = true;
			return false;
		}
		// Check if supplied filters are indexed.
		if ( ! $this->all_filters_are_indexed() ) {

			$queue   = new Kadence_Blocks_Pro_Query_Indexer_Process();
			$indexer = new Kadence_Blocks_Pro_Query_Indexer( $queue );
			$indexer->potentially_reindex_facets();

			// We have to manually build the query.
			$this->missing_index = true;
			return false;
		}

		// Create the query for the index.
		foreach ( $this->filters as $hash => $filter ) {
			switch ( $this->facets[ $hash ]['type'] ) {
				case 'query-filter-date':
					$this->filter_date( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter':
					$this->filter_dropdown( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-checkbox':
				case 'query-filter-checkbox':
					$this->filter_dropdown( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter-buttons':
					$this->filter_buttons( $index_query, $this->facets[ $hash ] );
				case 'default':
					break;
			}
		}

		// If the global compare is AND, each filter should have run it's own seperate query, doing an array_intersect on $extra_results each time
		// At this point $extra_results should be only the results from those intersections and we can return.
		// If the global compare is OR, each filter should have added a chained where clause to $index_query
		// In this case there may still have been some filters with local AND comparison, those ran seperate queries and array_merged their results into $extra_results.
		// They can now be orWhereIn'ed to end of the query chain.
		if ( $this->global_compare === 'AND' && $this->extra_results !== null ) {
			return $this->extra_results;
		} else if( $this->extra_results && $this->extra_results !== null ) {
			$index_query->orWhereIn( 'object_id', $this->extra_results );
		}

		return DB::get_col( DB::remove_placeholder_escape( $index_query->getSQL() ) );
	}

	public function filter_date( &$index_query, $facet ) {
		$value      = $this->filters[ $facet['hash'] ];
		$comparison = ! empty( $facet['comparisonLogic'] ) ? $facet['comparisonLogic'] : '<=';

		// @todo: if date is from term or post_title, use something like str_to_date(`facet_name`, '%M %e, %Y') instead of date(`facet_name`)

		$query_to_use = &$index_query;
		if ( $this->global_compare === 'AND' ) {
			$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		}

		$query_to_use->orWhere( function ( WhereQueryBuilder $builder ) use ( $facet, $comparison, $value ) {
			$builder
				->where( 'hash', $facet['hash'], '=' )
				->where( 'facet_name', $value, $comparison );
		} );

		if ( $this->global_compare === 'AND' ) {
			$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
			$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
		}
	}

	/**
	 * @param $index_query
	 * @param $facet
	 *
	 * @return void
	 */
	public function filter_dropdown( &$index_query, $facet, $fallback_compare = 'OR' ) {
		$values        = explode( ',', $this->filters[ $facet['hash'] ] );
		$index_field   = $facet['source'] === 'wordpress' ? 'facet_value' : 'facet_id';
		$local_compare = ! empty( $facet['comparisonLogic'] ) ? $facet['comparisonLogic'] : $fallback_compare;

		if ( $local_compare === 'AND' ) {
			$value_result_arrays = array();
			foreach ( $values as $value ) {
				$value_query = DB::table( 'kbp_query_index' )->select( 'object_id' )
				->where( 'hash', $facet['hash'], '=' )
				->where( $index_field, $value, '=' );

				$value_result_arrays[] = DB::get_col( DB::remove_placeholder_escape( $value_query->getSQL() ) );
			}
			$value_results = array_intersect( ...$value_result_arrays );
			if ( $this->global_compare === 'AND' ) {
				$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $value_results ) : $value_results;
			} else {
				$this->extra_results = $this->extra_results !== null ? array_merge( $this->extra_results, $value_results ) : $value_results;
			}
		} else {
			$query_to_use = &$index_query;
			if ( $this->global_compare === 'AND' ) {
				$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
			}

			foreach ( $values as $value ) {
				$query_to_use
					->orWhere( function ( WhereQueryBuilder $builder ) use ( $facet, $index_field, $value ) {
						$builder
							->where( 'hash', $facet['hash'], '=' )
							->where( $index_field, $value, '=' );
					});
			}

			if ( $this->global_compare === 'AND' ) {
				$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
				$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
			}
		}
	}

	/**
	 * @param $index_query
	 * @param $facet
	 *
	 * @return void
	 */
	public function filter_buttons( &$index_query, $facet ) {
		$values      = explode( ',', $this->filters[ $facet['hash'] ] );
		$index_field = $facet['source'] === 'wordpress' ? 'facet_value' : 'facet_id';

		$query_to_use = &$index_query;
		if ( $this->global_compare === 'AND' ) {
			$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		}

		foreach ( $values as $value ) {
			$query_to_use->orWhere( function ( WhereQueryBuilder $builder ) use ( $facet, $index_field, $value ) {
				$builder
					->where( 'hash', $facet['hash'], '=' )
					->where( $index_field, $value, '=' );
			} );
		}

		if ( $this->global_compare === 'AND' ) {
			$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
			$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
		}
	}


	/**
	 * Get all filters that have been sent with the request
	 *
	 * @return array
	 */
	public function get_filters() {
		$return = array();

		foreach ( $_GET as $key => $value ) {
			if ( ! empty( $this->facets[ $key ] ) ) {
				$return[ $key ] = $value;
			} else {
				foreach( $this->facets as $hash => $data ) {
					if( isset( $data['slug'] ) && $data['slug'] === $key ) {
						$return[ $hash ] = $value;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Pull the facets from the query block. Only keep the ones that are included in the request
	 *
	 * @return array
	 */
	public function get_facets() {
		$return = array();
		$facets = get_post_meta( $this->ql_id, '_kad_query_facets', true );

		if ( false !== $facets ) {
			foreach ( $facets as $key => $facet ) {
				$return[ $facet['hash'] ] = array_merge(
					array(
						'attributes' => $facet['attributes'],
						'hash'       => $facet['hash'],
					),
					json_decode( $facet['attributes'], true )
				);
			}
		}

		return $return;
	}

	/**
	 * Check if supplied filters are indexed. If not, we have to query the DB directly, instead of using the index
	 *
	 * @return bool
	 */
	public function all_filters_are_indexed() {
		$hashes = DB::get_col( DB::table( 'kbp_query_index' )
			->select( 'hash' )
			->whereIn( 'hash', array_column( $this->facets, 'hash' ) )
			->distinct()
			->getSQL() );

		// If index is missing for supplied hashes
		if ( count( $hashes ) !== count( $this->facets ) ) {
			return false;
		}

		return true;

	}

	public function get_fallback_query() {
		$fallback_query = DB::table( 'posts' )->select( 'object_id' );

		// Create the query for the index
		foreach ( $this->facets as $facet ) {
			switch ( $facet['type'] ) {
				case 'query-filter-date':
					// SELECT * FROM `wp_terms` WHERE  DATE(`slug`) > '2013-01-01'
//					$this->filter_date( $fallback_query, $facet );
					break;
				case 'query-filter':
//					$this->filter_dropdown( $fallback_query, $facet );
					break;
				case 'default':
					break;
			}
		}

		return $fallback_query->getSQL();
	}
}
