<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Clauses;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Types\Operator;
use InvalidArgumentException;

/**
 * @since 1.0.0
 */
class Where {

	/**
	 * @var string
	 */
	public $column;

	/**
	 * @var mixed
	 */
	public $value;

	/**
	 * @var string
	 */
	public $comparisonOperator;

	/**
	 * @var string
	 */
	public $logicalOperator;

	/**
	 * @param  string  $column
	 * @param  mixed  $value
	 * @param  string  $comparisonOperator
	 * @param  string|null  $logicalOperator
	 */
	public function __construct( $column, $value, $comparisonOperator, $logicalOperator ) {
		$this->column             = trim( $column );
		$this->value              = $value;
		$this->comparisonOperator = $this->getComparisonOperator( $comparisonOperator );
		$this->logicalOperator    = $logicalOperator ? $this->getLogicalOperator( $logicalOperator ) : '';
	}

	/**
	 * @param  string  $comparisonOperator
	 *
	 * @return string
	 */
	private function getComparisonOperator( $comparisonOperator ) {
		$operators = [
			'<',
			'<=',
			'>',
			'>=',
			'<>',
			'!=',
			'=',
			Operator::LIKE,
			Operator::NOTLIKE,
			Operator::IN,
			Operator::NOTIN,
			Operator::BETWEEN,
			Operator::NOTBETWEEN,
			Operator::ISNULL,
			Operator::NOTNULL
		];

		if ( ! in_array( $comparisonOperator, $operators, true ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Unsupported comparison operator %s. Please use one of the supported operators (%s)',
					$comparisonOperator,
					implode( ',', $operators )
				)
			);
		}

		return $comparisonOperator;
	}

	/**
	 * @param  string  $logicalOperator
	 *
	 * @return string
	 */
	private function getLogicalOperator( $logicalOperator ) {
		$operators = [
			Operator::_AND,
			Operator::_OR
		];

		$logicalOperator = strtoupper( $logicalOperator );

		if ( ! in_array( $logicalOperator, $operators, true ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Unsupported logical operator %s. Please use one of the supported operators (%s)',
					$logicalOperator,
					implode( ',', $operators )
				)
			);
		}

		return $logicalOperator;
	}
}
