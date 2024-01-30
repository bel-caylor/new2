<?php
/**
 * Form Field Conditions Handling.
 *
 * @package Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Main plugin class
 */
class KBP_Form_Conditional {
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

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
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ), 10 );
		add_filter( 'kadence_advanced_form_input_attributes', array( $this, 'build_conditional_attribute' ), 10, 2 );
		add_filter( 'render_block', array( $this, 'render_blocks' ), 13, 3 );
	}
	/**
	 * Register the script.
	 */
	public function register_script() {
		wp_register_script( 'kadence-form-conditional', KBP_URL . 'includes/assets/js/kb-conditional-fields.min.js', array(), KBP_VERSION, true );
	}

	/**
	 * Build the conditional attribute from an input attribtues
	 *
	 * @param string $additional_attributes The incoming attributes string.
	 * @param array  $attributes The block attributes.
	 *
	 * @return string
	 */
	public function build_conditional_attribute( $additional_attributes, $attributes ) {
		if ( isset( $attributes['kadenceFieldConditional']['conditionalData']['enable'] ) && true == $attributes['kadenceFieldConditional']['conditionalData']['enable'] && ! empty( $attributes['kadenceFieldConditional']['conditionalData']['rules'] ) ) {
			$condtional_rules = $attributes['kadenceFieldConditional']['conditionalData']['rules'];
			$combine = ! empty( $attributes['kadenceFieldConditional']['conditionalData']['combine'] ) ? $attributes['kadenceFieldConditional']['conditionalData']['combine'] : 'or';
			$action = ! empty( $attributes['kadenceFieldConditional']['conditionalData']['action'] ) ? $attributes['kadenceFieldConditional']['conditionalData']['action'] : 'hide';
			$form_id = ! empty( $attributes['formID'] ) ? $attributes['formID'] : '';
			$container = '.kb-field' . $form_id . $attributes['uniqueID'];

			$rules_group = array();

			foreach ( $condtional_rules as $key => $rule ) {
				$field = 'field' . $form_id . $rule['field'];
				$compare = $this->translate_compare( $rule['compare'] );
				$value = $rule['value'];

				$rules_group[] = array(
					'name' => $field,
					'operator' => $compare,
					'value' => $value,
				);
			}

			$conditions_string = $this->get_conditional_rules_string(
				$action,
				$rules_group,
				$combine,
				$container
			);
			return $additional_attributes . ' data-conditional-rules="' . $conditions_string . '" ';
		}
		return $additional_attributes;
	}

	/**
	 * Build a formatted, json encoded string for the conditional rules.
	 *
	 * @param string $action The action to execture when the rules match.
	 * @param array  $rules The rules that trigger the action. should contain a 'name', 'operator', and 'value'.
	 * @param string $logic The logic to combine the rules together.
	 * @param string $container An optional container query selector to apply the action to in place of the field.
	 *
	 * @return string
	 */
	public function get_conditional_rules_string( $action, $rules, $logic = 'or', $container = '' ) {
		return htmlspecialchars(
			wp_json_encode(
				array(
					'container' => $container,
					'action' => $action,
					'logic' => $logic,
					'rules' => $rules,
				)
			)
		);
	}

	/**
	 * Translate a compare value to one used in the mf-conditionals lib.
	 *
	 * @param string $compare The kadence compare value.
	 *
	 * @return string
	 */
	public function translate_compare( $compare ) {
		$translations = array(
			'not_empty' => 'isnotempty',
			'is_empty' => 'isempty',
			'equals' => 'is',
			'not_equals' => 'isnot',
			'equals_or_greater' => 'equalgreaterthan',
			'equals_or_less' => 'equallessthan',
			'greater' => 'greaterthan',
			'less' => 'lessthan',
		);
		return isset( $translations[ $compare ] ) ? $translations[ $compare ] : $compare;
	}

	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block info.
	 * @param object $wp_block The block class object.
	 */
	public function render_blocks( $block_content, $block, $wp_block ) {		
		$blockattr = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		if ( isset( $blockattr['kadenceFieldConditional']['conditionalData']['enable'] ) && true == $blockattr['kadenceFieldConditional']['conditionalData']['enable'] ) {
			if ( wp_script_is( 'kadence-form-conditional', 'registered' ) && ! wp_script_is( 'kadence-form-conditional', 'enqueued' ) ) {
				wp_enqueue_script( 'kadence-form-conditional' );
			}
		}

		return $block_content;
	}
}

KBP_Form_Conditional::get_instance();
