<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\Database;

class Provider {
	/**
	 * @var Actions\EnableBigSqlSelects
	 */
	public $action_enable_big_sql_selects;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'stellarwp_db_pre_query', [ $this, 'enable_big_sql_selects' ] );
	}

	/**
	 * Fires the EnableBigSqlSelects action.
	 */
	public function enable_big_sql_selects() {
		if ( $this->action_enable_big_sql_selects === null ) {
			$this->action_enable_big_sql_selects = new Actions\EnableBigSqlSelects();
		}

		$this->action_enable_big_sql_selects->set_var();
	}
}
