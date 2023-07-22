<?php
/**
 * TPGB Reusable Shortcode
 *
 * @package Nexter Extensions
 * @since 2.0.8
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Tpag_Resuable_Shortcode' ) ) {

	class Tpag_Resuable_Shortcode {
		
		const TPGB_SHORTCODE = 'tpgb-reusable'; //Reusable Blocks
		
		/**
		 * Member Variable
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			$this->add_actions_shortcode();
		}
		
		private function add_actions_shortcode(){
			if ( is_admin() ) {
				add_filter( 'manage_wp_block_posts_columns', [ $this, 'admin_columns_shortcode' ],15 );
				add_action( 'manage_wp_block_posts_custom_column', [ $this, 'admin_columns_shortcode_content' ], 15, 2 );
			}

			add_shortcode( self::TPGB_SHORTCODE, [ $this, 'create_shortcode' ] );
		}
		
		public function admin_columns_shortcode( $columns ) {
			$columns['tpgb_shortcode'] = __( 'Shortcode', 'tpgb' );

			return $columns;
		}
	
		public function admin_columns_shortcode_content( $column, $post_id ) {
			if ( 'tpgb_shortcode' === $column ) {
				//translator %s = shortcode, %d = post_id
				$shortcode = esc_attr( sprintf( '[%s id="%d"]', self::TPGB_SHORTCODE, $post_id ) );
				printf( '<input type="text"  style="width:350px" class="nxt-shortcode-input" onfocus="this.select()" value="%s" readonly style="font-size: 12px;"/>', $shortcode );
			}
		}
		
		public function create_shortcode( $option = [] ) {
			if ( empty( $option['id'] ) ) {
				return '';
			}

			if( isset($option['id']) && !empty($option['id']) ){
				if( class_exists('Tpgb_Library') ){
					ob_start();
					return Tpgb_Library()->plus_do_block($option['id']);
					ob_get_clean();
				}
			}
		}
	}
}

Tpag_Resuable_Shortcode::get_instance();