<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Clauses;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\QueryBuilder;

/**
 * @since 1.0.0
 */
class From {
	/**
	 * @var string|RawSQL
	 */
	public $table;

	/**
	 * @var string
	 */
	public $alias;

	/**
	 * @param  string|RawSQL  $table
	 * @param  string|null  $alias
	 */
	public function __construct( $table, $alias = '' ) {
		$this->table = QueryBuilder::prefixTable( $table );
		$this->alias = is_scalar( $alias ) ? trim( (string) $alias ) : '';
	}
}
