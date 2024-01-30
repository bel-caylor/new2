<?php

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

class Kadence_Blocks_Pro_Query_Indexer {

	/**
	 * Instance of the Queue worker
	 *
	 * @var Kadence_Blocks_Pro_Query_Indexer_Process
	 */
	public $queue_worker;

	/**
	 * Instance of the Query Facets class
	 *
	 * @var Kadence_Blocks_Pro_Query_Facets
	 */
	public $query_facets;

	/**
	 * Is a save post request
	 *
	 * @var boolean
	 */
	public $is_saving_post = false;

	/**
	 * Is heartbeat request
	 *
	 * @var boolean
	 */
	public $is_heartbeat = false;

	/**
	 * Keys to exclude from indexing
	 *
	 * @var string[]
	 */
	public $exclude = [
		'_kad_query_facets',
		'_wp_desired_post_slug',
		'_edit_last',
		'_encloseme',
		'_edit_lock',
		'_wp_page_template',
		'_wp_trash_meta_status',
		'_wp_trash_meta_time',
	];


	public function __construct( $queue_worker ) {
		require_once( __DIR__ . '/query-facets.php' );

		$this->queue_worker = $queue_worker;
		$this->query_facets = new Kadence_Blocks_Pro_Query_Facets();

		// Post
		add_action( 'save_post', [ $this, 'save_post' ], PHP_INT_MAX - 10 );
		add_action( 'delete_post', [ $this, 'delete_post' ] );
		add_filter( 'wp_insert_post_parent', [ $this, 'insert_post' ], 10, 4 );

		// Post meta
		add_action( 'heartbeat_tick', [ $this, 'is_heartbeat' ] );
		add_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], PHP_INT_MAX - 10, 4 );
		add_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ], PHP_INT_MAX - 10, 4 );

		// Terms
		add_action( 'edited_term', [ $this, 'edit_term' ], PHP_INT_MAX - 10, 3 );
		add_action( 'delete_term', [ $this, 'delete_term' ], 10, 4 );
		add_action( 'set_object_terms', [ $this, 'set_object_terms' ], PHP_INT_MAX - 10 );
	}

	/**
	 * Reindex post on update
	 *
	 * @param $post_id
	 */
	public function save_post( $post_id ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		     false !== wp_is_post_revision( $post_id ) ||
		     'auto-draft' === get_post_status( $post_id ) ||
		     'kadence_query' === get_post_type( $post_id ) ) {
			return;
		}

		$this->index_single_object( $post_id, 'post' );
		$this->is_saving_post = false;

	}

	/**
	 * Deleted post from index
	 *
	 * @param $post_id
	 */
	public function delete_post( $post_id ) {

		$sources = array( 'post_field/', 'post_meta/', 'taxonomy/' );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( empty( $facets ) ) {
			return;
		}

		// Delete this post from each facet that has indexed it
		foreach ( $facets as $facet ) {
			DB::table( $this->query_facets->table_name )
			  ->where( 'hash', $facet['hash'] )
			  ->where( 'object_id', $post_id )
			  ->delete();
		}
	}

	/**
	 * Prevent set_object_terms() to index wp_insert_post.
	 *
	 * @param int   $post_parent Post parent ID.
	 * @param int   $post_id     Post ID.
	 * @param array $new_postarr Array of parsed post data.
	 * @param array $postarr     Array of sanitized, but otherwise unmodified post data.
	 */
	public function insert_post( $post_parent, $post_id, $new_postarr, $postarr ) {

		$this->is_saving_post = true;

		return $post_parent;

	}

	/**
	 * Prevent heartbeat from trigger an index
	 */
	public function is_heartbeat() {
		$this->is_heartbeat = true;
	}

	/**
	 * Reindex on post meta update
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Post ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value.
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key === '_kad_query_facets' ) {
			$this->potentially_reindex_facets();

			return;
		}

		if ( $this->is_saving_post || $this->is_heartbeat ) {
			return;
		}

		if ( in_array( $meta_key, $this->exclude, true ) ) {
			$this->log_action( 'excluded', $meta_key );

			return;
		}

		$this->log_action( 'updated_post_meta', array( $meta_id, $object_id, $meta_key, $meta_value ) );

		$this->index_single_object( $object_id, 'post' );

	}

	/**
	 * Handle term changes
	 *
	 * @access public
	 *
	 * @param int    $term_id  Term id.
	 * @param int    $tt_id    Term taxonomy  id.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function edit_term( $term_id, $tt_id, $taxonomy ) {

		$this->log_action( 'edit_term', $term_id );

		// For term object type.
		$this->index_single_object( $term_id, 'term' );

		// Query facets.
		$sources = array( 'taxonomy/' . $taxonomy );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( empty( $facets ) ) {
			return;
		}

		$term = get_term( $term_id, $taxonomy );
		$slug = sanitize_title( $term->slug );

		foreach ( $facets as $facet ) {
			$this->query_facets->update_facet( $facet['hash'], $slug, $term->name, $term_id );
		}
	}

	/**
	 * Handle term deletion
	 *
	 * @access public
	 *
	 * @param int    $term_id      Term id.
	 * @param int    $tt_id        Term taxonomy id.
	 * @param string $taxonomy     Taxonomy slug.
	 * @param mixed  $deleted_term Copy of the already-deleted term, in the form specified by the parent function.
	 */
	public function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {
		$sources = array( 'taxonomy/' . $taxonomy );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( ! empty( $facets ) ) {
			foreach ( $facets as $facet ) {
				DB::table( $this->query_facets->table_name )
				  ->where( 'hash', $facet['hash'] )
				  ->where( 'object_id', $term_id )
				  ->delete();
			}
		}
	}

	/**
	 * Support for manual taxonomy associations
	 *
	 * @access public
	 *
	 * @param int $object_id Term id.
	 */
	public function set_object_terms( $object_id ) {

		if ( $this->is_saving_post ) {
			return;
		}

		$this->index_single_object( $object_id, 'post' );

	}

	public function potentially_reindex_facets( $force = false ) {
		$disable_index = apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false );
		if ( $disable_index ) {
			return;
		}

		$indexed            = $this->query_facets->get_indexed_facets();
		$should_be_in_index = $this->query_facets->get_facets();

		$missing              = array_diff_key( $should_be_in_index, array_flip( $indexed ) );
		$shouldnt_be_in_index = $force ? $indexed : array_diff_key( array_flip( $indexed ), $should_be_in_index );

		foreach ( $shouldnt_be_in_index as $facet_hash => $value ) {
			$this->query_facets->delete_facet( $facet_hash );
		}

		foreach ( $missing as $missing_facet ) {
			$this->queue_worker->push_to_queue( $missing_facet );
		}

		if ( count( $missing ) > 0 ) {
			// Save and dispatch the queue
			$this->queue_worker->save()->dispatch();
		}
	}

	/**
	 * Reindex all facets (or provided facet hashes)
	 *
	 * @param $facet_ids
	 *
	 * @return void
	 */
	public function index_facets( $facet_hashes = array() ) {

		$facets = $this->query_facets->get_facets( $facet_hashes );

		// Push items to the queue
//		foreach ( $facets as $facet ) {
//			$this->queue_worker->push_to_queue( $facet );
//		}

		$example_facet = json_decode( '{"name":"Post Tags","hash":"post_tags","title":"","action":"filter","filter_type":"select","source":"taxonomy","taxonomy":"post_tag","parent":"","include":[],"exclude":[],"logic":"AND","multiple":0,"hierarchical":0,"children":1,"show_empty":1,"show_count":1,"limit":10,"orderby":"count","order":"DESC","select_placeholder":"None","combobox":0}', true );
		$this->queue_worker->push_to_queue( $example_facet );

		// Save and dispatch the queue
		$this->queue_worker->save()->dispatch();
	}

	public function index_single_object( $object_id, $type ) {

		if ( empty( $object_id ) ) {
			return;
		}

		$facets = $this->query_facets->get_facets();

		// Foreach facet
		foreach ( $facets as $facet ) {
			if ( $type !== 'post' ) {
				continue;
			}

			// Delete object_id rows from current facet.
			$this->query_facets->delete_facet( $facet['hash'], $object_id );

			// Pass object ID and facet to process_objects()
			$this->process_objects( $facet, (array) $object_id, false );
		}

	}

	public function log_action( $action = '', $data = array() ) {
		if( ! defined( 'KB_DEBUG' ) || ! KB_DEBUG) {
			return;
		}

		error_log( $action . ' --  ' . json_encode( $data ) );
	}

	public function get_objects( $facet ) {
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		return $this->query_posts( $facet, $source );
	}

	/**
	 * Query post ids to index.
	 *
	 * @access public
	 *
	 * @param array  $facet  Holds facet settings.
	 * @param string $source Facet source type.
	 *
	 * @return array of post ids
	 */
	public function query_posts( $facet, $source ) {

		global $wp_taxonomies;

		$post_types = get_post_types( array( 'public' => true, 'show_in_rest' => true ) );
		unset( $post_types['attachment'] );
		$post_types = array_keys( $post_types );

		$this->log_action( 'Index Post Type', $post_types );

		if ( 'taxonomy' === $source && isset( $wp_taxonomies[ $facet['taxonomy'] ] ) ) {

			$taxonomy   = $wp_taxonomies[ $facet['taxonomy'] ];
			$post_types = $taxonomy->object_type;

		}

		$query_args = [
			'post_type'        => $post_types,
			'post_status'      => 'any',
			'posts_per_page'   => - 1,
			'fields'           => 'ids',
			'orderby'          => 'ID',
			'cache_results'    => false,
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'lang'             => '',
		];

		$query_args = apply_filters( 'kadence_blocks_pro_query_index_args', $query_args, 'post', $facet );

		$posts = (array) ( new \WP_Query( $query_args ) )->posts;

		wp_reset_postdata();

		return $posts;

	}

	/**
	 * Process object ids to index
	 *
	 * @access public
	 *
	 * @param array $object_ids Holds Object ids to index.
	 * @param array $facet      Holds facet settings.
	 * @param array $key        Facet key in queue (cron task).
	 */
	public function process_objects( $facet, $object_ids = array(), $background_task = true ) {
		$this->log_action( 'process_objects', $object_ids );
		$this->log_action( 'process_objects', $facet );

		// If we don't have objects yet, fetch them and delete existing facet index.
		if ( ! empty( $facet['objects'] ) ) {
			$object_ids = $facet['objects'];
		} elseif ( ! empty( $object_ids ) ) {
			$facet['objects'] = $object_ids;
		} else {
			$facet['objects'] = $this->get_objects( $facet );
			$object_ids       = $facet['objects'];
		}

		if ( empty( $object_ids ) ) {
			return false;
		}

		$offset = isset( $facet['offset'] ) ? $facet['offset'] : 0;
		if ( $offset ) {
			$object_ids = array_slice( $object_ids, max( 0, $facet['offset'] - 1 ) );
		}

		foreach ( $object_ids as $index => $object_id ) {

			// If we reach limit while indexing.
			if ( $background_task && ( $this->queue_worker->time_exceeded_public() || $this->queue_worker->memory_exceeded_public() ) ) {
				$this->log_action( 'Memory or time limit exceeded. Requeuing.' );

				$facet['offset'] = $offset + $index;

				// when we return the modified item, it will be re-queued for the next pass through.
				// "offset" is now included, so we can resume on the previous index.
				return $facet;
			}

			// Hook in for 3rd party plugins to add to the index.
			$rows = apply_filters( 'kadence_blocks_pro_query_index_object', [], $object_id, $facet );

			// We need to index the object.
			if ( empty( $rows ) ) {
				$rows = $this->fetch_rows( $object_id, $facet );
			}

			foreach ( $rows as $row ) {
				$row = $this->format( $row, $object_id, $facet );
				$this->insert_row( $row );
			}
		}

		return false;

	}

	/**
	 * Get rows given object and facet data
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id
	 * @param array   $facet     Holds metadata
	 */
	public function fetch_rows( $object_id, $facet ) {

		$rows   = [];
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		switch ( $source ) {
			case 'taxonomy':
				$rows = $this->taxonomy_terms( $object_id, $facet );
				break;
			case 'post_field':
				$rows = $this->index_post_field( $object_id, $facet );
				break;
			case 'post_meta':
				$rows = $this->index_metadata( $object_id, $facet );
				break;
		}

		return $rows;

	}

	/**
	 * Indexing taxonomy terms
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function taxonomy_terms( $object_id, $facet ) {

		$added  = [];
		$output = [];

		$query_args = [
			'object_ids' => $object_id,
			'taxonomy'   => $facet['taxonomy'],
			'include'    => array_map( 'intval', (array) $facet['include'] ),
			'exclude'    => array_map( 'intval', (array) $facet['exclude'] ),
			'parent'     => $facet['parent'] ? (int) $facet['parent'] : '',
			'lang'       => '',
		];

		$terms = (array) ( new \WP_Term_Query( $query_args ) )->terms;

		foreach ( $terms as $term ) {

			// Prevent duplicate terms.
			if ( isset( $added[ $term->term_id ] ) ) {
				continue;
			}

			// Do not index parent term.
			if ( $term->term_id === $query_args['parent'] ) {
				continue;
			}

			// Set parent id to root parent if children of parent.
			if ( $term->parent === $query_args['parent'] ) {
				$term->parent = 0;
			}

			// Set parent id to root parent if included term without included parent.
			if (
				in_array( $term->term_id, $query_args['include'], true ) &&
				! in_array( $term->parent, $query_args['include'], true )
			) {
				$term->parent = 0;
			}

			$added[ $term->term_id ] = true;

			$output[] = [
				'facet_value'  => $term->slug,
				'facet_name'   => $term->name,
				'facet_id'     => $term->term_id,
				'facet_parent' => $term->parent,
				'facet_order'  => $term->term_order,
			];

			$parent_terms = $this->get_parent_terms( $term, $query_args, $facet );

			// Index child parents to count all childs attached to a parent.
			foreach ( $parent_terms as $parent_term ) {

				if ( isset( $added[ $parent_term->term_id ] ) ) {
					continue;
				}

				$added[ $parent_term->term_id ] = true;

				$output[] = [
					'facet_value'  => $parent_term->slug,
					'facet_name'   => $parent_term->name,
					'facet_id'     => $parent_term->term_id,
					'facet_parent' => $parent_term->parent,
					'facet_order'  => $parent_term->term_order,
				];

			}
		}

		return $output;

	}

	/**
	 * Get parent terms given a term and facet.
	 *
	 * @access public
	 *
	 * @param object $term       Child term.
	 * @param array  $query_args WP_Term_Query args.
	 * @param array  $facet      Facet metadata.
	 */
	public function get_parent_terms( $term, $query_args, $facet ) {
		if ( ! $term->parent  || !isset( $facet['hierarchical'] ) ) {
			return [];
		}

		if ( ! $facet['hierarchical'] && 'hierarchy' !== $facet['type'] ) {
			return [];
		}

		$ancestors = get_ancestors( $term->term_id, $query_args['taxonomy'] );

		// include & exclude terms from filter settings.
		if ( ! empty( $query_args['exclude'] ) ) {
			$ancestors = array_diff( $ancestors, $query_args['exclude'] );
		} elseif ( ! empty( $query_args['include'] ) ) {
			$ancestors = array_intersect( $ancestors, $query_args['include'] );
		}

		if ( empty( $ancestors ) ) {
			return [];
		}

		$parent_terms = get_terms(
			[
				'taxonomy'   => $query_args['taxonomy'],
				'include'    => $ancestors,
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $parent_terms ) ) {
			return [];
		}

		return $parent_terms;

	}

	/**
	 * Index post field
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function index_post_field( $object_id, $facet ) {

		$post = get_post( $object_id );

		if ( ! isset( $post->{$facet['post_field']} ) ) {
			return [];
		}

		$value = $post->{$facet['post_field']};
		$name  = $value;

		if ( 'post_author' === $facet['post_field'] ) {

			$name = '';
			$user = get_userdata( $value );

			if ( isset( $user->display_name ) ) {
				$name = $user->display_name;
			}
		} elseif ( 'post_type' === $facet['post_field'] ) {

			$name = '';
			$type = get_post_type_object( $value );

			if ( isset( $type->labels->name ) ) {
				$name = $type->labels->name;
			}
		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $name,
			],
		];

	}

	/**
	 * Index metadata (post, user, term)
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function index_metadata( $object_id, $facet ) {

		$output = [];
		$values = get_metadata( $facet['field_type'], $object_id, $facet['meta_key'] );

		foreach ( (array) $values as $value ) {

			$output[] = [
				'facet_value' => $value,
				'facet_name'  => $value,
			];

		}

		return $output;

	}

	/**
	 * Format column values
	 *
	 * @access public
	 *
	 * @param array   $columns   Holds row columns.
	 * @param integer $object_id Object to index.
	 * @param array   $facet     Facet metadata.
	 */
	public function format( $columns, $object_id, $facet ) {
		return wp_parse_args(
			$columns,
			[
				'object_id'    => $object_id,
				'hash'         => $facet['hash'],
				'facet_value'  => '',
				'facet_name'   => '',
				'facet_id'     => 0,
				'facet_parent' => 0,
				'facet_order'  => 0,
			]
		);
	}

	/**
	 * Insert row into index table
	 *
	 * @access public
	 *
	 * @param array $columns Columns to insert
	 */
	public function insert_row( $columns ) {
		if ( ! is_array( $columns ) || '' === $columns['facet_value'] || ! is_scalar( $columns['facet_value'] ) ) {
			return;
		}

		DB::table( $this->query_facets->table_name )
		  ->insert( [
			  'object_id'    => $columns['object_id'],
			  'hash'         => $columns['hash'],
			  'facet_value'  => $this->sanitize_facet_value( $columns['facet_value'] ),
			  'facet_name'   => $columns['facet_name'],
			  'facet_id'     => $columns['facet_id'],
			  'facet_parent' => $columns['facet_parent'],
			  'facet_order'  => $columns['facet_order']
		  ] );
	}

	/**
	 * Sanitize facet
	 *
	 * @access public
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function sanitize_facet_value( $str ) {

		if ( is_numeric( $str ) && ! is_int( $str ) ) {
			return (float) $str + 0;
		}

		$str = remove_accents( $str );
		$str = strip_tags( $str );

		// Convert entities to hyphens.
		$str = str_replace( [ '%c2%a0', '%e2%80%93', '%e2%80%94' ], '-', $str );
		$str = str_replace( [ '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ], '-', $str );
		$str = preg_replace( '/&.+?;/', '', $str );
		$str = preg_replace( '/\s+/', '-', $str );
		$str = preg_replace( '|-+|', '-', $str );
		$str = str_replace( [ ',', '.' ], '-', $str );
		$str = strtolower( $str );

		// Limit facet value in case of super long name
		if ( 150 < strlen( $str ) ) {
			$str = md5( $str );
		}

		return $str;

	}
}
