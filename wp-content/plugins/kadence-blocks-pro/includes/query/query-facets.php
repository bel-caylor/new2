<?php

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

class Kadence_Blocks_Pro_Query_Facets {

	public $table_name = 'kbp_query_index';

	public $meta_key = '_kad_query_facets';

	public function __construct() {

	}

	/**
	 * Get an array of all facets with their hash as the key
	 *
	 * @param array $hashes Facet hashes to return
	 *
	 * @return array
	 */
	public function get_facets( $hashes = array() ) {
		$facets = array();

		// Don't get facets from trashed posts
		$raw_post_facets = DB::get_col( DB::table( 'postmeta', 'pmeta' )
		                                  ->select( 'pmeta.meta_value' )
		                                  ->innerJoin( 'posts', 'pmeta.post_id', 'wpposts.ID', 'wpposts' )
		                                  ->where( 'pmeta.meta_key', '_kad_query_facets', '=' )
		                                  ->where( 'wpposts.post_status', 'trash', '!=' )
		                                  ->getSQL() );

		foreach ( $raw_post_facets as $raw_post_facet ) {
			$parsed_facets = $this->parse_facets( $raw_post_facet );

			foreach ( $parsed_facets as $parsed_facet ) {
				if ( empty( $hashes ) || in_array( $parsed_facet['hash'], $hashes ) ) {
					$facets[ $parsed_facet['hash'] ] = array_merge( $parsed_facet['attributes'], array( 'hash' => $parsed_facet['hash'] ) );
				}
			}
		}

		return $facets;
	}

	/**
	 * Get an array of all facets that match given source(s)
	 *
	 * @param array $sources
	 *
	 * @return array
	 */
	public function get_facets_by_source( $sources ) {
		$facets = array();

		foreach ( $this->get_facets() as $facet ) {
			foreach ( $sources as $source ) {
				if ( strpos( $facet['source'], $source ) !== false ) {
					$facets[] = $facet;
				}
			}
		}

		return $facets;
	}

	/**
	 * Convert serialized facet string to array
	 *
	 * @param $attribute_string
	 *
	 * @return mixed
	 */
	public function parse_facets( $attribute_string ) {
		$facets = unserialize( $attribute_string );

		foreach ( $facets as &$facet ) {
			$facet['attributes'] = json_decode( $facet['attributes'], true );
			unset( $facet['attributes']['metadata'] );

			$facet['attributes']['include'] = !empty( $facet['attributes']['include'] ) ? array_column($facet['attributes']['include'], 'value') : array();
			$facet['attributes']['exclude'] = !empty( $facet['attributes']['exclude'] ) ? array_column($facet['attributes']['exclude'], 'value') : array();
			if( $facet['attributes']['source'] === 'taxonomy' ) {
				$facet['attributes']['source'] = $facet['attributes']['source'] . '/' . $facet['attributes']['taxonomy'];
			} else {
				$facet['attributes']['source'] = $facet['attributes']['fieldType'] . '/' . $facet['attributes'][ $facet['attributes']['fieldType'] ];
			}

			$facet['attributes']['children'] = 1;
			$facet['attributes']['logic'] = 'AND';
			$facet['attributes']['parent'] = '';
		}

		return $facets;
	}

	/**
	 * Returns array of facet hashes that are indexed
	 *
	 * @return array
	 */
	public function get_indexed_facets() {
		return DB::get_col( DB::table( $this->table_name )->select( 'hash' )->distinct()->getSQL() );
	}

	/**
	 * Delete facet index for a given hash. If object_id is provided, only delete that object_id for the hash.
	 *
	 * @param $hash
	 * @param $object_id
	 *
	 * @return void
	 */
	public function delete_facet( $hash, $object_id = null ) {
		if ( $object_id === null ) {
			DB::table( $this->table_name )->where( 'hash', $hash, '=' )->delete();
		} else {
			DB::table( $this->table_name )->where( 'hash', $hash, '=' )->where( 'object_id', $object_id, '=' )->delete();
		}
	}

	/**
	 * Update facet index for a given hash.
	 *
	 * @param $hash
	 * @param $object_id
	 *
	 * @return void
	 */
	public function update_facet( $hash, $facet_value, $facet_name, $facet_id ) {
		DB::table( $this->table_name )->where( 'hash', $hash, '=' )->where( 'facet_id', $facet_id, '=' )->update( array(
			'facet_value' => $facet_value,
			'facet_name' => $facet_name,
		) );
	}

}
