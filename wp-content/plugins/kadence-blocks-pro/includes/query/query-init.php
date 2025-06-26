<?php
/**
 * Load all the files for Query block.
 *
 * @package Kadence Blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-query-block.php';
//require_once KBP_PATH . 'includes/blocks/form/class-kadence-blocks-pro-query-input-block.php';

require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-query-children-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-card-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-checkbox-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-buttons-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-date-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-rating-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-range-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-reset-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-search-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-woo-attribute-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-noresults-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-pagination-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-result-count-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-sort-block.php';

require_once KBP_PATH . 'includes/query/query-rest-api.php';
require_once KBP_PATH . 'includes/query/query-cpt.php';
require_once KBP_PATH . 'includes/query/query-card-cpt.php';
require_once KBP_PATH . 'includes/query/index-query-builder.php';

require_once KBP_PATH . 'includes/query/query-frontend-filters.php';
require_once KBP_PATH . 'includes/query/query-frontend-pagination.php';
