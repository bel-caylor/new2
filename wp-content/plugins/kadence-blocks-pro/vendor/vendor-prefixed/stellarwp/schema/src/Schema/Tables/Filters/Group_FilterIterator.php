<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\Schema\Tables\Filters;

use CallbackFilterIterator;
use Countable;
use FilterIterator;
use Iterator;

class Group_FilterIterator extends FilterIterator implements Countable {

	/**
	 * Groups to filter.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string>
	 */
	private $groups;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $groups Paths to filter.
	 * @param Iterator $iterator Iterator to filter.
	 */
	public function __construct( array $groups, Iterator $iterator ) {
		parent::__construct( $iterator );

		$this->groups = $groups;
	}

	/**
	 * @inheritDoc
	 */
	public function accept(): bool {
		$table = $this->getInnerIterator()->current();

		return in_array( $table::group_name(), $this->groups, true );
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int {
		return iterator_count( new CallbackFilterIterator( $this->getInnerIterator(), function (): bool {
			return $this->accept();
		} ) );
	}
}
