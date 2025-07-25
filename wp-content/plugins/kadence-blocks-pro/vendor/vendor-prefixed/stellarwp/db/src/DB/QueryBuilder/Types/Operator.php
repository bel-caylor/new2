<?php
/**
 * @license GPL-2.0
 *
 * Modified by kadencewp on 22-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\Types;

/**
 * @since 1.0.0
 */
class Operator extends Type {
	// _AND and _OR constants are prefixed with underscore to be compatible with PHP 5.6
	const _AND = 'AND';
	const _OR = 'OR';
	const ON = 'ON';
	const BETWEEN = 'BETWEEN';
	const NOTBETWEEN = 'NOT BETWEEN';
	const EXISTS = 'EXISTS';
	const NOTEXISTS = 'NOT EXISTS';
	const IN = 'IN';
	const NOTIN = 'NOT IN';
	const LIKE = 'LIKE';
	const NOTLIKE = 'NOT LIKE';
	const NOT = 'NOT';
	const ISNULL = 'IS NULL';
	const NOTNULL = 'IS NOT NULL';
}
