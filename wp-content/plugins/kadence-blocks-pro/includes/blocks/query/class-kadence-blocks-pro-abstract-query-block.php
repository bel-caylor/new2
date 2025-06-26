<?php
/**
 * Class to Build other Query Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build other Query Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Abstract_Query_Block extends Kadence_Blocks_Pro_Abstract_Block {
	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	protected $block_attributes = array();

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_ql_post( $ql_id ) {
		global $kbp_ql_post;
		global $kbp_ql_id;

		$kbp_ql_id = $ql_id;

		if ( $ql_id ) {
			$kbp_ql_post = get_post( $ql_id );

			if ( ! $kbp_ql_post || 'kadence_query' !== $kbp_ql_post->post_type ) {
				return null;
			}

			if ( 'publish' !== $kbp_ql_post->post_status || ! empty( $kbp_ql_post->post_password ) ) {
				return null;
			}
		}
		return $kbp_ql_post;
	}

	public static function get_qlc_post( $qlc_id ) {
		global $kbp_qlc_post;

		if ( $qlc_id ) {
			// Support Query card templates from other post types
			$qlc_template_type = get_post_meta( $qlc_id, '_kad_query_card_template_post_type', true );
			if( false !== $qlc_template_type && '' !== $qlc_template_type ) {
				$qlc_id = get_post_meta( $qlc_id, '_kad_query_card_template_post_id', true );
			}

			$kbp_qlc_post = get_post( $qlc_id );

			if ( ! $kbp_qlc_post || ( 'kadence_query_card' !== $kbp_qlc_post->post_type && $kbp_qlc_post->post_type !== $qlc_template_type ) ) {
				return null;
			}

			if ( 'publish' !== $kbp_qlc_post->post_status || ! empty( $kbp_qlc_post->post_password ) ) {
				return null;
			}
		}
		return $kbp_qlc_post;
	}

	public static function get_q_posts( $ql_id ) {
		$qlc_id = 0;
		$ql_post = self::get_ql_post( $ql_id );

		if ( $ql_post ) {
			$ql_blocks = parse_blocks( $ql_post->post_content );
			$qlc_block = self::find_block_in_inner_blocks_by_name( $ql_blocks[0]['innerBlocks'], 'kadence/query-card' );
			if ( $qlc_block && ! empty( $qlc_block['attrs'] ) && ! empty( $qlc_block['attrs']['id'] ) ) {
				$qlc_id = $qlc_block['attrs']['id'];
			}
		}

		$qlc_post = self::get_qlc_post( $qlc_id );

		return array( $ql_post, $qlc_post, $qlc_id );
	}

	/**
	 * Builds css for inner blocks
	 *
	 * @param array $inner_blocks array of inner blocks.
	 */
	public static function find_block_in_inner_blocks_by_name( $inner_blocks, $block_name ) {
		foreach ( $inner_blocks as $inner_block ) {
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {
				if ( $block_name === $inner_block['blockName'] ) {
					return $inner_block;
				}
				if ( ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$inner_search = self::find_block_in_inner_blocks_by_name( $inner_block['innerBlocks'], $block_name );
					if ( $inner_search ) {
						return $inner_search;
					}
				}
			}
		}
		return null;
	}

	/**
	 * Get attributes from a block saved to meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $prefix The meta prefix.
	 * @return array
	 */
	public function get_block_attributes_from_meta( $post_id, $prefix ) {

		if ( ! empty( $this->block_attributes[ $post_id ] ) ) {
			return $this->block_attributes[ $post_id ];
		}

		$post_meta = get_post_meta( $post_id );
		$form_meta = array();
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $meta_key => $meta_value ) {
				if ( strpos( $meta_key, $prefix ) === 0 && isset( $meta_value[0] ) ) {
					$form_meta[ str_replace( $prefix, '', $meta_key ) ] = maybe_unserialize( $meta_value[0] );
				}
			}
		}

		if ( $this->block_attributes[ $post_id ] = $form_meta ) {
			return $this->block_attributes[ $post_id ];
		}

		return array();
	}
}
