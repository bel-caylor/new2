<?php

/**
 * Advanced Form Ajax Handing.
 *
 * @package Kadence Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class KBP_Ajax_Advanced_Form {

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
		add_filter( 'kadence_advanced_form_actions', array( $this, 'after_submit_actions' ), 10, 5 );
	}

	public function after_submit_actions( $submission_results, $actions, $form_args, $processed_fields, $post_id ) {

		$actions = isset( $form_args['attributes']['actions'] ) ? $form_args['attributes']['actions'] : array( 'email' );

		$submit_actions = new Kadence_Blocks_Pro_Advanced_Form_Submit_Actions( $form_args, $processed_fields, $post_id );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'sendinblue':
					$submit_actions->sendinblue();
					break;
				case 'mailchimp':
					$submit_actions->mailchimp();
					break;
				case 'convertkit':
					$submit_actions->convertkit();
					break;
				case 'activecampaign':
					$submit_actions->activecampaign();
					break;
				case 'webhook':
					$submit_actions->webhook();
					break;
				case 'entry':
					$entry_results = $submit_actions->entry( $post_id );
					$results_data = array_diff_key( $entry_results, array_flip( array( 'success' ) ) );

					$submission_results['success'] = $entry_results['success'] ? $submission_results['success'] : $entry_results['success'];
					$submission_results = array_merge( $submission_results, $results_data );
					break;
				case 'autoEmail':
					$submit_actions->autoEmail();
					break;
			}
		}

		return $submission_results;
	}
}

KBP_Ajax_Advanced_Form::get_instance();
