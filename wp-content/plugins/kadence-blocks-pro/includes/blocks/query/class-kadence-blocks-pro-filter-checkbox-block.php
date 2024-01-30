<?php
/**
 * Class to Build the Query Filter Checkbox Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query No Results Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Filter_Checkbox_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'filter-checkbox';

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Builds CSS for block.
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $css the css class for blocks.
	 * @param string $unique_id the blocks attr ID.
	 * @param string $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {
		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-checkbox' . $unique_id . ' .checkbox-options' );

		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->render_border_styles( $attributes, 'border' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius' );
		$css->render_typography( $attributes, 'typography' );

		// Colors.
		if ( ! empty( $attributes['backgroundType'] ) && 'gradient' == $attributes['backgroundType'] && ! empty( $attributes['gradient'] ) ) {
			$css->add_property( 'background', $attributes['gradient'] );
		} else if ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-checkbox' . $unique_id . ' .checkbox-options label' );
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		return $css->css_output();
	}

	/**
	 * Return dynamically generated HTML for block
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $unique_id The unique id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$hash = $this->get_hash_from_unique_id( $unique_id );

		$data_attributes = array();

		$data_attributes[] = 'data-uniqueID="' . $unique_id . '"';
		$data_attributes[] = 'data-hash="' . $hash . '"';

		$data_attributes_string = implode( ' ', $data_attributes );

		$classes = array(
			'kadence-query-filter',
			'wp-block-kadence-query-filter-checkbox',
		);
		$classes[] = ! isset( $attributes['showInline'] ) || ( isset( $attributes['showInline'] ) && $attributes['showInline'] ) ? 'inline' : '';
		$classes_str = implode( ' ', $classes );

		$label_html = $this->get_label_html( $attributes );

		$filters = $data && ! empty( $data['filters'] ) ? $data['filters'][ $unique_id ] : '';

		return sprintf(
			'<div class="wp-block-kadence-query-filter-checkbox%s %s" %s><fieldset class="kadence-filter-wrap">%s<div class="checkbox-options">%s</div></fieldset></div>',
			$unique_id,
			$classes_str,
			$data_attributes_string,
			$label_html,
			$filters
		);
	}
}

Kadence_Blocks_Pro_Filter_Checkbox_Block::get_instance();
