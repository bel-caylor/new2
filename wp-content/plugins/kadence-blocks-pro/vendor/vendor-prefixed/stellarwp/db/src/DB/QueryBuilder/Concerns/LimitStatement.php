<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Concerns;

/**
 * @since 1.0.0
 */
trait LimitStatement {
	/**
	 * @var int
	 */
	protected $limit;

	/**
	 * @param  int  $limit
	 *
	 * @return $this
	 */
	public function limit( $limit ) {
		$this->limit = (int) $limit;

		return $this;
	}

	protected function getLimitSQL() {
		return $this->limit
			? [ "LIMIT {$this->limit}" ]
			: [];
	}
}
