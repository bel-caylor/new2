<?php
/**
 * TPGB Pro Conditions Rules.
 *
 * @package TPGBP
 * @since 1.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Tpgbp_Display_Conditions_Rules {
	
	/**
	 * Member Variable
	 *
	 * @var instance
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
	 * Check Role of visitor
	 */
	public static function tpgb_check_role( $value, $check_is_not, $key ) {
		$user = wp_get_current_user();
		return self::compare_check( is_user_logged_in() && in_array( $value, $user->roles ), true, $check_is_not );
	}
	
	/**
	 * Check Operating System of visitor
	 */
	public static function tpgb_check_os( $value, $check_is_not, $key ) {
		$os_list = [
			'iphone'            => '(iPhone)',
			'safari'            => '(Safari)',
			'mac_os'            => '(Mac_PowerPC)|(Macintosh)',
			'windows' 			=> 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',			
			'beos'              => 'BeOS',
			'linux'             => '(Linux)|(X11)',			
			'open_bsd'          => 'OpenBSD',
			'qnx'               => 'QNX',			
			'os2'              	=> 'OS/2',
			'search_bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
			'sun_os'            => 'SunOS',			
		];

		return self::compare_check( preg_match('@' . $os_list[ $value ] . '@', $_SERVER['HTTP_USER_AGENT'] ), true, $check_is_not );
	}
	
	/**
	 * Check Browser of visitor
	 */
	public static function tpgb_check_browser( $value, $check_is_not, $key ) {
		$browsers_list = [
			'ie'			=> [
				'MSIE',
				'Trident',
			],
			'chrome'		=> 'Chrome',
			'firefox'		=> 'Firefox',
			'opera'			=> 'Opera',
			'opera_mini'	=> 'Opera Mini',
			'safari'		=> 'Safari',
		];
		
		$display = false;
		if ( $value === 'ie' ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ][1] ) ) {
				$display = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers_list[ $value ] ) ) {
				$display = true;

				// Additional check for Chrome that returns Safari
				if ( $value === 'firefox' || $value === 'safari' ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$display = false;
					}
				}
			}
		}
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Date Interval of visitor
	 */
	public static function tpgb_check_date( $value, $check_is_not, $key ) {
		
		$between = explode( 'to' , preg_replace('/\s+/', '', $value ) );

		if ( ! is_array( $between ) || 2 !== count( $between ) ) 
			return;

		$today 	= date('Y-m-d');
		
		$start_date 	= $between[0];
		$end_date 	= $between[1];		

		$display 	= false;

		if ( \DateTime::createFromFormat( 'Y-m-d', $start_date ) === false || // Make sure it's a date
			 \DateTime::createFromFormat( 'Y-m-d', $end_date ) === false ) // Make sure it's a date
			return;

		$start 	= strtotime( $start_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$end 	= strtotime( $end_date ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$today_date 	= strtotime( $today ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		
		$display = ( ($today_date >= $start ) && ( $today_date <= $end ) );

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Time of visitor
	 */
	public static function tpgb_check_time( $value, $check_is_not, $key ) {
		
		$dateTime = explode(" ",$value);
		$today 	= date('Y-m-d');
		$currntDate = strtotime( $today ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$endDate = strtotime( $dateTime[0] ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

		$sttime = (isset($dateTime[1]) && !empty($dateTime[1]) ) ? $dateTime[1] : '';
		$sttime = str_replace("-", ":", $sttime);
	 	$time 	= date( 'H:i', strtotime( preg_replace('/\s+/', '', $sttime ) ) );
		$now = date( 'H:i', strtotime("now") + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		$display = false;

		if ( \DateTime::createFromFormat( 'H:i', $time ) === false && \DateTime::createFromFormat( 'Y-m-d', $endDate ) === false ) // Make sure it's a valid DateTime format
			return;
		
		$time_ts 	= strtotime( $time );
		$now_ts 	= strtotime( $now );

		$display = ( ($now_ts < $time_ts) && ( $currntDate <= $endDate ) );
		
		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Days of visitor
	 */
	public static function tpgb_check_day( $value, $check_is_not, $key ) {

		$display = false;
		
		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( $_value['value'] === date( 'w' ) ) {
					$display = true; break;
				}
			}
		} else { $display = $value === date( 'w' ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Page of visitor
	 */
	public static function tpgb_check_page( $value, $check_is_not, $key ) {
		$display = false;
		
		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_page( $_value['value'] ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_page( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Post of visitor
	 */
	public static function tpgb_check_post( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_single( $_value['value'] ) || is_singular( $_value['value'] ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_single( $value ) || is_singular( $value); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Post Type of visitor
	 */
	public static function tpgb_check_post_type( $value, $check_is_not, $key ) {
		
		$display = false;
		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value['value'] ) {
				if ( is_singular( $_value['value'] ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_singular( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Static Page of visitor
	 */
	public static function tpgb_check_static_page( $value, $check_is_not, $key ) {

		if ( $value === 'home' ) {
			return self::compare_check( ( is_front_page() && is_home() ), true, $check_is_not );
		} elseif ( $value === 'static' ) {
			return self::compare_check( ( is_front_page() && ! is_home() ), true, $check_is_not );
		} elseif ( $value === 'blog' ) {
			return self::compare_check( ( ! is_front_page() && is_home() ), true, $check_is_not );
		} elseif ( $value === '404' ) {
			return self::compare_check( is_404(), true, $check_is_not );
		}
	}
	
	/**
	 * Check Texonomy Archive of visitor
	 */
	public static function tpgb_check_taxonomy_archive( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				$display = self::tpgb_check_taxonomy_archive_type( $_value['value'] );

				if ( $display ) break;
			}
		} else { $display = self::tpgb_check_taxonomy_archive_type( $value['value'] ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Single Terms of visitor
	 */
	public static function tpgb_check_single_terms($value, $check_is_not, $key){
		$display = false;

		if ( is_array( $value ) && !empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				$display = self::tpgb_check_single_terms_type( $_value['value'] );
				
				if ( $display ) break;
			}
		} else { $display = self::tpgb_check_single_terms_type( $value['value'] ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Single Terms Of Taxonomy
	 */

	public static function tpgb_check_single_terms_type($term){
		
		if ( is_category( $term ) ) {
			return true;
		} else if ( is_tag( $term ) ) {
			return true;
		} else if ( is_tax() ) {
			if ( is_tax( get_queried_object()->taxonomy, $term ) ) {
				return true;
			}
		}

		return false;
	}
	
	/*
	 * Check Taxonomy Archive Type
	 */
	public static function tpgb_check_taxonomy_archive_type( $taxonomy ) {
		
		if ( $taxonomy === 'category' ) {
			return is_category();
		} else if ( $taxonomy === 'post_tag' ) {
			return is_tag();
		} else if ( $taxonomy === '' || empty( $taxonomy ) ) {
			return is_tax() || is_category() || is_tag();
		} else {
			return is_tax( $taxonomy );
		}

		return false;
	}
	
	/**
	 * Check Post Type Archive of visitor
	 */
	public static function tpgb_check_post_type_archive( $value, $check_is_not, $key ) {
		
		$display = false;
		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_post_type_archive( $_value['value'] ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_post_type_archive( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Date Archive of visitor
	 */
	public static function tpgb_check_date_archive( $value, $check_is_not, $key ) {
		
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( self::tpgb_check_date_archive_type( $_value['value'] ) ) {
					$display = true; break;
				}
			}
		} else { $display = is_date( $value ); }

		return self::compare_check( $display, true, $check_is_not );
	}
	
	public static function tpgb_check_date_archive_type( $type ) {
		
		if ( $type === 'day' ) { 
			return is_day();
		} elseif ( $type === 'month' ) { 
			return is_month();
		} elseif ( $type === 'year' ) { 
			return is_year();
		}

		return false;
	}
	
	/**
	 * Check Author Archive of visitor
	 */
	public static function tpgb_check_author_archive( $value, $check_is_not, $key ) {
		$display = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_author( $_value ) ) {
					$display = true; break;
				}
			}
		} else {
			$display = is_author( $value ); 
		}

		return self::compare_check( $display, true, $check_is_not );
	}
	
	/**
	 * Check Single Terms of Archive 
	 */
	public static function tpgb_check_single_archive($value, $check_is_not, $key){
		$display = false;

		if ( is_array( $value ) && !empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				$display = self::tpgb_check_single_archive_type( $_value['value'] );
				
				if ( $display ) break;
			}
		} else { $display = self::tpgb_check_single_archive_type( $value['value'] ); }

		return self::compare_check( $display, true, $check_is_not );
	}

	/**
	 * Check Single Terms of Archive type
	 */

	 public static function tpgb_check_single_archive_type($term){
		if ( in_category( $term ) ) {
			return true;
		} else if ( is_tag( $term ) ) {
			return true;
		} else if ( is_tax() ) {
			if ( is_tax( get_queried_object()->taxonomy, $term ) ) {
				return true;
			}
		}

		return false;
	 }
}
Tpgbp_Display_Conditions_Rules::get_instance();