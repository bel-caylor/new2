<?php
/**
 * Class to Build the Slider Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Slider Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Slider_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'slider';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = true;

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
	 * @param array $attributes the blocks attributes.
	 * @param Kadence_Blocks_Pro_CSS $css the css class for blocks.
	 * @param string $unique_id the blocks attr ID.
	 * @param string $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {

		$this->enqueue_script( 'kadence-blocks-pro-slider-init' );
		$this->enqueue_style( 'kadence-kb-splide' );

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$margin_unit = ( isset( $attributes['marginUnit'] ) && ! empty( $attributes['marginUnit'] ) ? $attributes['marginUnit'] : 'px' );
		$padding_unit = ( isset( $attributes['paddingUnit'] ) && ! empty( $attributes['paddingUnit'] ) ? $attributes['paddingUnit'] : 'px' );
		$height_unit = ( isset( $attributes['heightUnit'] ) && ! empty( $attributes['heightUnit'] ) ? $attributes['heightUnit'] : 'px' );

		if (isset($attributes['heightType']) && 'fixed' === $attributes['heightType'] && isset($attributes['minHeight']) && is_array($attributes['minHeight'])) {
			$css->set_selector('.kb-advanced-slider-' . $unique_id . ' .kb-slider-size-fixed .kb-advanced-slide-inner-wrap');
			if( !empty($attributes['minHeight'][0]) ){
				$css->add_property('min-height', $attributes['minHeight'][0] . $height_unit);
			}

			if (!empty($attributes['minHeight'][1])) {
				$css->set_media_state('tablet');
				$css->add_property('min-height', $attributes['minHeight'][1] . $height_unit);
			}

			if (!empty($attributes['minHeight'][2])) {
				$css->set_media_state('mobile');
				$css->add_property('min-height', $attributes['minHeight'][2] . $height_unit);
			}
			$css->set_media_state('desktop');
		}
		if (isset($attributes['maxWidth']) && is_array($attributes['maxWidth']) ) {
			$css->set_selector('.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner');
			if(!empty($attributes['maxWidth'][0])) {
				$css->add_property( 'max-width', $attributes['maxWidth'][0] . ( isset( $attributes['widthUnit'] ) && ! empty( $attributes['widthUnit'] ) ? $attributes['widthUnit'] : 'px' ) );
			}

			if (!empty($attributes['maxWidth'][1])) {
				$css->set_media_state('tablet');
				$css->add_property('max-width', $attributes['maxWidth'][1] . (isset($attributes['widthUnit']) && !empty($attributes['widthUnit']) ? $attributes['widthUnit'] : 'px'));
			}

			if (!empty($attributes['maxWidth'][2])) {
				$css->set_media_state('mobile');
				$css->add_property('max-width', $attributes['maxWidth'][2] . (isset($attributes['widthUnit']) && !empty($attributes['widthUnit']) ? $attributes['widthUnit'] : 'px'));
			}
			$css->set_media_state('desktop');
		}

		$css->set_selector('.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner-wrap');
		$padding = array(
			'padding' => !empty( $attributes['padding'][0]['desk'] ) ? $attributes['padding'][0]['desk'] : [ '', '', '', '' ],
			'tabletPadding' => !empty( $attributes['padding'][0]['tablet'] ) ? $attributes['padding'][0]['tablet'] : [ '', '', '', '' ],
			'mobilePadding' => !empty( $attributes['padding'][0]['mobile'] ) ? $attributes['padding'][0]['mobile'] : [ '', '', '', '' ],
			'paddingType' => $padding_unit
		);
		$css->render_measure_output( $padding, 'padding', 'padding' );

		$css->set_selector('.kb-advanced-slider-' . $unique_id);
		$margin = array(
			'margin' => !empty( $attributes['margin'][0]['desk'] ) ? $attributes['margin'][0]['desk'] : [ '', '', '', '' ],
			'tabletMargin' => !empty( $attributes['margin'][0]['tablet'] ) ? $attributes['margin'][0]['tablet'] : [ '', '', '', '' ],
			'mobileMargin' => !empty( $attributes['margin'][0]['mobile'] ) ? $attributes['margin'][0]['mobile'] : [ '', '', '', '' ],
			'marginType' => $margin_unit
		);
		$css->render_measure_output( $margin, 'margin', 'margin' );

		return $css->css_output();
	}
	/**
	 * Build HTML for dynamic blocks
	 *
	 * @param $attributes
	 * @param $unique_id
	 * @param $content
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return mixed
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		if ( ! empty( $attributes['kbVersion'] ) && $attributes['kbVersion'] > 1 ) {
			$outer_classes = array( 'kb-advanced-slider', 'kb-advanced-slider-' . $unique_id );
			$wrapper_args = array(
				'class' => implode( ' ', $outer_classes ),
			);
			$dot_style = ! empty( $attributes['dotStyle'] ) ? $attributes['dotStyle'] : 'dark';
			$arrow_style = ! empty( $attributes['arrowStyle'] ) ? $attributes['arrowStyle'] : 'whiteondark';
			$height_type = ! empty( $attributes['heightType'] ) ? $attributes['heightType'] : 'ratio';
			$ratio = ! empty( $attributes['sliderRatio'][0] ) ? $attributes['sliderRatio'][0] : '12-5';
			$fade = isset( $attributes['fade'] ) && false == $attributes['fade'] ? 'false' : 'true';
			$hover_pause = isset( $attributes['hoverPause'] ) && true == $attributes['hoverPause'] ? 'true' : 'false';
			$dragging = isset( $attributes['dragging'] ) && false == $attributes['dragging'] ? 'false' : 'true';
			$auto_play = isset( $attributes['autoPlay'] ) && false == $attributes['autoPlay'] ? 'false' : 'true';
			$anim_speed = isset( $attributes['transSpeed'] ) && is_numeric( $attributes['transSpeed'] ) ? $attributes['transSpeed'] : 400;
			$auto_speed = isset( $attributes['autoSpeed'] ) && is_numeric( $attributes['autoSpeed'] ) ? $attributes['autoSpeed'] : 7000;
			$tab_ratio = ! empty( $attributes['sliderRatio'][1] ) ? $attributes['sliderRatio'][1] : 'inherit';
			$mobile_ratio = ! empty( $attributes['sliderRatio'][2] ) ? $attributes['sliderRatio'][2] : 'inherit';
			$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );
			$content = sprintf( '<div %1$s><div class="kb-advanced-slider-inner-contain kb-adv-slider-html-version-2"><div class="kb-blocks-advanced-carousel kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '"><div class="kb-blocks-advanced-slider-init kb-slider-loading kb-blocks-slider kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . ' kb-slider-size-' . esc_attr( $height_type ) . ' kb-slider-ratio-' . esc_attr( $ratio ) . ' kb-slider-tab-ratio-' . esc_attr( $tab_ratio ) . ' kb-slider-mobile-ratio-' . esc_attr( $mobile_ratio ) . ' splide kb-splide" data-slider-anim-speed="' . esc_attr( $anim_speed ) . '" data-slider-type="slider" data-slider-scroll="1" data-slider-fade="' . esc_attr( $fade ) . '" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="' . esc_attr( $hover_pause ) . '" data-slider-auto="' . esc_attr( $auto_play ) . '"  data-dragging="' . esc_attr( $dragging ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '"><div class="splide__track"><div class="splide__list">%2$s</div></div></div></div></div></div>', $wrapper_attributes, $content );
		}
		return $content;
	}

	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {

		// Skip calling parent because this block does not have a dedicated CSS file.
		parent::register_scripts();

		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		if ( apply_filters( 'kadence_blocks_check_if_rest', false ) && kadence_blocks_is_rest() ) {
			return;
		}
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-slider-init', KBP_URL . 'includes/assets/js/kb-splide-slider-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
	}
}

Kadence_Blocks_Pro_Slider_Block::get_instance();
