<?php
/**
 * TPGB Conditions Rules.
 *
 * @package TPGBP
 * @since 1.0.6
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Tpgb_Display_Conditions_Rules {
	
	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;
	
	/**
	 * Display Rules 
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	public static $conditions = [];
	
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
	 * Constructor
	 */
	public function __construct() {
		/*Display Rules Options*/
		add_filter( 'tpgb_display_option', [ $this, 'tpgb_display_option'], 10 );
	}
	
	/*
	 * Display Rules Options
	 * @since 1.0.6
	 */
	public static function tpgb_display_option($option =[]){
		$disoption = [
			'tpgbDisrule' => [
				'type' => 'boolean',
				'default' => false,
			],
			'disRule' => [
				'type' => 'string',
				'default' => 'all',
			],
			'displayRules' => [
				'type'=> 'array',
				'repeaterField' => [
					(object) [
						'displayKey' => [
							'type' => 'string',
							'default' => 'authentication',
						],
						'assigOpr' => [
							'type' => 'string',
							'default' => 'is',
						],
						'tpgb_startdate_value' => [
							'type' => 'time',
							'default' => '',
						],
						'tpgb_enddate_value' => [
							'type' => 'time',
							'default' => '',
						],
						'tpgb_time_value' => [
							'type' => 'time',
							'default' => '',
						],
						'tpgb_day_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_post_type_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_page_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_post_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_taxonomy_archive_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_single_terms_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_author_archive_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_static_page_value' => [
							'type' => 'string',
							'default' => 'home',
						],
						'tpgb_post_type_archive_value' => [
							'type' => 'string',
							'default' => '[]',
						],
						'tpgb_date_archive_value' => [
							'type' => 'string',
							'default' => 'day',
						],
						'tpgb_search_results_value' => [
							'type' => 'string',
							'default' => '',
						],
						'tpgb_authentication_value' => [
							'type' => 'string',
							'default' => 'authenticated',
						],
						'tpgb_role_value' => [
							'type' => 'string',
							'default' => 'administrator',
						],
						'tpgb_os_value' => [
							'type' => 'string',
							'default' => 'iphone',
						],
						'tpgb_browser_value' => [
							'type' => 'string',
							'default' => 'ie',
						],
						'tpgb_single_archive_value' => [
							'type' => 'string',
							'default' => '[]',
						],
					],
				],
				'default' => [ 
					(object)[ "_key" => '0','displayKey' => 'authentication', 'tpgb_authentication_value' => 'authenticated', 'tpgb_role_value' => 'administrator', 'tpgb_os_value' => 'iphone', 'tpgb_browser_value' => 'ie', 'assigOpr' => 'is', 'tpgb_startdate_value' => '2021-10-13', 'tpgb_enddate_value' => '2021-10-15', 'tpgb_time_value' => '12:00', 'tpgb_day_value' => '[]' ,'tpgb_post_type_value' => '[]','tpgb_page_value' => '[]' ,'tpgb_post_value' => '[]' ,'tpgb_taxonomy_archive_value' => '[]', 'tpgb_single_terms_value' => '[]' , 'tpgb_author_archive_value' => '[]', 'tpgb_post_type_archive_value' => '[]', 'tpgb_static_page_value' => 'home', 'tpgb_date_archive_value' => 'day', 'tpgb_search_results_value' => '' , 'tpgb_single_archive_value' => '[]' ]
				],
			],
		];

		return array_merge( $option, $disoption );
	}
	
	/*
	 * Check Display Rules Actions
	 */
	public static function tpgb_rules_actions( $block_id, $attribute ) {
		
		if ( !empty($block_id) && isset($attribute[ 'tpgbDisrule' ]) && !empty($attribute[ 'tpgbDisrule' ]) ) {
			// Set the rules
			if( !empty($attribute['displayRules']) ){
				self::set_rules( $block_id, $attribute['displayRules'] );
			}
			
			if(!empty($attribute['disRule']) ){
				if ( ! self::display_is_visible( $block_id, $attribute['disRule'] ) && !empty($attribute['disRule'])) { // Check the rules
					return false;
				}
			}
		}
		return true;
	}
	
	/*
	 * Check Set Rules
	 */
	public static function set_rules( $id, $rules = [] ) {
		$tpgb_startdate_value = $tpgb_enddate_value = '';
		if ( ! $rules )
			return;
		
		foreach ( $rules as $index => $rule ) {
			$rule = (array)$rule;
			$key = $rule['displayKey'];
			$key_name =null;
			
			if ( array_key_exists( 'tpgb_' . $key . '_name' , $rule ) ) {
				$key_name = $rule['tpgb_' . $key . '_name'];
			}
			
			$check_is_not 	= isset($rule['assigOpr']) ? $rule['assigOpr'] : 'is';
			if(isset($rule['displayKey']) && $rule['displayKey']=='date') {
				$tpgb_startdate_value = isset($rule['tpgb_startdate_value']) ? $rule['tpgb_startdate_value'] : '';
				$tpgb_enddate_value = isset($rule['tpgb_enddate_value']) ? $rule['tpgb_enddate_value'] : '';
				$value = $tpgb_startdate_value.' to '.$tpgb_enddate_value;
			} else {
				$value = isset($rule['tpgb_' . $key . '_value']) ? $rule['tpgb_' . $key . '_value'] : '';
			}
			if ( method_exists('Tpgb_Display_Conditions_Rules', 'tpgb_check_' . $key ) ) {
				$check = call_user_func( ['Tpgb_Display_Conditions_Rules', 'tpgb_check_' . $key], $value, $check_is_not,$key_name );
				self::$conditions[ $id ][ $key . '_' . $rule['_key'] ] = $check;
			}else if ( method_exists('Tpgbp_Display_Conditions_Rules', 'tpgb_check_' . $key ) ) {

				$check = call_user_func( ['Tpgbp_Display_Conditions_Rules', 'tpgb_check_' . $key], $value, $check_is_not,$key_name );
				self::$conditions[ $id ][ $key . '_' . $rule['_key'] ] = $check;
			}
		}
	}
	
	public static function display_is_visible( $id, $relation ) {
		
		if ( ! array_key_exists( $id, self::$conditions ) )
			return;
			
			if ( $relation === 'any' ) {
				if ( ! in_array( true, self::$conditions[ $id ] ) )
					return false;
			} else {
				if ( in_array( false, self::$conditions[ $id ] ) )
					return false;
			}

		return true;
	}
	
	public static function compare_check( $first_value, $second_value, $check_is_not ) {
		switch ( $check_is_not ) {
			case 'is':
				return $first_value == $second_value;
			case 'not':
				return $first_value != $second_value;
			default:
				return $first_value === $second_value;
		}
	}
	
	/**
	 * Check Login Status of visitor
	 */
	public static function tpgb_check_authentication( $value, $check_is_not, $key ) {
		return self::compare_check( is_user_logged_in(), true, $check_is_not );
	}
	
}
Tpgb_Display_Conditions_Rules::get_instance();