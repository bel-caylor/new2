<?php
/**
 * Class to Build the Slide Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Slide Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Slide_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'slide';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = false;

	/**
	 * Block determines if style needs to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_style = false;

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

		if ( ! empty( $attributes['textColor'] ) ) {
			$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' h1, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h2, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h3, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h4, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h5, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h6, .wp-block-kadence-slider .kb-slide-' . $unique_id);
			$css->add_property('color', $css->render_color( $attributes['textColor'] ));
		}
		if ( ! empty( $attributes['linkColor'] ) ) {
			$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' a');
			$css->add_property('color', $css->render_color( $attributes['linkColor'] ));
		}
		if ( ! empty( $attributes['linkHoverColor'] ) ) {
			$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' a:hover');
			$css->add_property('color', $css->render_color( $attributes['linkHoverColor'] ));
		}
		// Vertical Align.
		$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' .kb-advanced-slide-inner-wrap');
		if ( ! empty( $attributes['vAlign'][0] ) ) {
			$align = $attributes['vAlign'][0];
			switch ( $align ) {
				case 'top':
					$align = 'flex-start';
					break;
				case 'bottom':
					$align = 'flex-end';
					break;
				case 'middle':
					$align = 'center';
					break;
			}
			$css->add_property('align-items', $align);
		}
		if ( ! empty( $attributes['vAlign'][1] ) ) {
			$css->set_media_state( 'tablet' );
			$align = $attributes['vAlign'][1];
			switch ( $align ) {
				case 'top':
					$align = 'flex-start';
					break;
				case 'bottom':
					$align = 'flex-end';
					break;
				case 'middle':
					$align = 'center';
					break;
			}
			$css->add_property('align-items', $align);
		}
		if ( ! empty( $attributes['vAlign'][2] ) ) {
			$css->set_media_state( 'mobile' );
			$align = $attributes['vAlign'][2];
			switch ( $align ) {
				case 'top':
					$align = 'flex-start';
					break;
				case 'bottom':
					$align = 'flex-end';
					break;
				case 'middle':
					$align = 'center';
					break;
			}
			$css->add_property('align-items', $align);
		}
		$css->set_media_state( 'desktop' );

		/* Overlay */
		$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' .kb-advanced-slide-overlay');

		if( !isset( $attributes['backgroundOverlayType'] ) || ( isset( $attributes['backgroundOverlayType'] ) && $attributes['backgroundOverlayType'] !== 'gradient' ) ) {
			if ( ! empty( $attributes['backgroundOverlay'] ) ) {
				$css->add_property( 'background-color', $css->render_color( $attributes['backgroundOverlay'] ) );
			}

			if ( ! empty( $attributes['overlayBlendMode'] ) ) {
				$css->add_property( 'mix-blend-mode', $attributes['overlayBlendMode'] );
			}
		} else if ( isset( $attributes['backgroundOverlayType'] ) && $attributes['backgroundOverlayType'] === 'gradient' ) {
			$css->add_property( 'background', $attributes['overlayGradient'] );
		}

		if ( isset( $attributes['backgroundOverlayOpacity'] ) ) {
			$css->add_property('opacity', $attributes['backgroundOverlayOpacity']);
		} else {
			$css->add_property('opacity', 0.6 );
		}

		/* Background */
		$background_type = ! empty( $attributes['backgroundType'] ) ? $attributes['backgroundType'] : 'normal';
		$css->set_selector('.wp-block-kadence-slider .kb-slide-' . $unique_id . ' .kb-advanced-slide-inner-wrap');
		switch ( $background_type ) {
			case 'normal':
				if( !empty($attributes['background']) ) {
					$css->add_property( 'background-color', $css->render_color( $attributes['background'] ) );
				}

				if( !empty($attributes['backgroundImg'][0]['img']) ) {
					$css->add_property( 'background-image', 'url(' . $attributes['backgroundImg'][0]['img'] . ')' );
				}

				if( !empty($attributes['backgroundImg'][0]['size']) ) {
					$css->add_property( 'background-size', $attributes['backgroundImg'][0]['size'] );
				}

				if( !empty($attributes['backgroundImg'][0]['position']) ) {
					$css->add_property( 'background-position', $attributes['backgroundImg'][0]['position'] );
				}

				if( !empty($attributes['backgroundImg'][0]['repeat']) ) {
					$css->add_property( 'background-repeat', $attributes['backgroundImg'][0]['repeat'] );
				}
			case 'gradient':
				if ( ! empty( $attributes['backgroundGradient'] ) ) {
					$css->add_property( 'background-color', 'transparent' );
					$css->add_property( 'background-image', $attributes['backgroundGradient'] );
				}
				break;
		}

		return $css->css_output();
	}

}

Kadence_Blocks_Pro_Slide_Block::get_instance();
