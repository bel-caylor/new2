<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare( strict_types=1 );

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\Database\Actions;

class EnableBigSqlSelects {
	/**
	 * @since 1.0.0
	 *
	 * Enables mysql big selects for the session using a session system variable.
	 *
	 * This is necessary for hosts that have an arbitrary MAX_JOIN_SIZE limit, which prevents more complex queries from
	 * running properly. Setting SQL_BIG_SELECTS ignores this limit. This is also done by WooCommerce, supporting the
	 * idea that this is a viable option. There also doesn't seem to be a way for hosts to prevent this.
	 *
	 * @see https://dev.mysql.com/doc/refman/5.7/en/server-system-variables.html#sysvar_sql_big_selects
	 * @see https://dev.mysql.com/doc/refman/5.7/en/system-variable-privileges.html
	 *
	 */
	public function set_var() {
		static $bigSelects = false;

		if ( ! $bigSelects ) {
			global $wpdb;

			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1;' );

			$bigSelects = true;
		}
	}
}
