<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

use Gravity_Forms\Gravity_Forms\Settings\Settings;

/**
 * Specialist Add-On class designed for use by Add-Ons that require form feed settings
 * on the form settings tab.
 *
 * @package GFFeedAddOn
 */

require_once( 'class-gf-addon.php' );

abstract class GFFeedAddOn extends GFAddOn {

	/**
	 * If set to true, Add-On can have multiple feeds configured. If set to false, feed list page doesn't exist and only one feed can be configured.
	 * @var bool
	 */
	protected $_multiple_feeds = true;

	/**
	 * If true, only first matching feed will be processed. Multiple feeds can still be configured, but only one is executed during the submission (i.e. Payment Add-Ons)
	 * @var bool
	 */
	protected $_single_feed_submission = false;

	/**
	 * If $_single_feed_submission is true, $_single_submission_feed will store the current single submission feed as stored by the get_single_submission_feed() method.
	 * @var mixed (bool | Feed Object)
	 */
	protected $_single_submission_feed = false;

	/**
	 * If true, users can configure what order feeds are executed in from the feed list page.
	 * @var bool
	 */
	protected $_supports_feed_ordering = false;

	/**
	 * If true, feeds will be processed asynchronously in the background.
	 *
	 * @since 2.2
	 * @var bool
	 */
	protected $_async_feed_processing = false;

	/**
	 * If true, feeds w/ conditional logic will evaluated on the frontend and a JS event will be triggered when the feed
	 * is applicable and inapplicable.
	 *
	 * @since 2.4
	 * @var bool
	 */
	protected $_supports_frontend_feeds = false;

	/**
	 * If true, maybe_delay_feed() checks will be bypassed allowing the feeds to be processed.
	 * @var bool
	 */
	protected $_bypass_feed_delay = false;

	/**
	 * Indicates if the add-on supports processing feeds multiple times for the same entry.
	 *
	 * @since 2.9.2
	 *
	 * @var bool
	 */
	protected $_supports_feed_reprocessing = true;

	/**
	 * An array of properties relating to the delayed payment functionality.
	 *
	 * Set by passing the array to `$this->add_delayed_payment_support()` in `init()`.
	 *
	 * @since 2.7.14 Was a dynamic property in earlier versions.
	 *
	 * @var array {
	 *     @type string $option_label The label to displayed for the add-ons delay checkbox, in the Post Payment Actions section of the payment add-ons feed configuration page.
	 * }
	 */
	public $delayed_payment_integration = array();

	/**
	 * @var string Version number of the Add-On Framework
	 */
	private $_feed_version = '0.14';
	private $_feed_settings_fields = array();
	private $_current_feed_id = false;

	/**
	 * @since 2.4
	 * @var array Feeds w/ conditional logic that impacts frontend form behavior.
	 */
	private static $_frontend_feeds = array();

	/**
	 * @since 2.4.23
	 *
	 * @var array Tables where table error has been rendered.
	 */
	private $_table_error_rendered = array();

	/**
	 * Gets all active, registered feed add-ons.
	 *
	 * @since 2.9.2
	 *
	 * @return (GFFeedAddOn|GFPaymentAddOn)[]
	 */
	public static function get_registered_feed_addons() {
		$addons = GFAddOn::get_registered_addons( true, true );

		return array_filter( $addons, function ( $addon ) {
			return $addon instanceof GFFeedAddOn;
		} );
	}

	/**
	 * Attaches any filters or actions needed to bootstrap the addon.
	 *
	 * @since 2.5.2
	 */
	public function bootstrap() {
		parent::bootstrap();

		if ( $this->is_feed_edit_page() ) {
			add_action( 'admin_init', array( $this, 'feed_settings_init' ), 20 );
		}
	}

	/**
	 * Plugin starting point. Handles hooks and loading of language files.
	 */
	public function init() {

		parent::init();

		add_filter( 'gform_entry_post_save', array( $this, 'maybe_process_feed' ), 10, 2 );
		add_action( 'gform_after_delete_form', array( $this, 'delete_feeds' ) );
		add_action( 'gform_update_status', array( $this, 'process_feed_when_unspammed' ), 10, 3 );

		// Register GFFrontendFeeds.
		if ( $this->_supports_frontend_feeds && ! has_action( 'gform_register_init_scripts', array( __class__, 'register_frontend_feeds_init_script' ) ) ) {
			// Use priority 20 so other add-ons that support frontend feeds can all load their scripts first.
			add_action( 'gform_register_init_scripts', array( __class__, 'register_frontend_feeds_init_script' ), 20 );
		}

	}

	/**
	 * Override this function to add AJAX hooks or to add initialization code when an AJAX request is being performed
	 */
	public function init_ajax() {

		parent::init_ajax();

		add_action( "wp_ajax_gf_feed_is_active_{$this->get_slug()}", array( $this, 'ajax_toggle_is_active' ) );
		add_action( 'wp_ajax_gf_save_feed_order', array( $this, 'ajax_save_feed_order' ) );

	}

	/**
	 * Override this function to add initialization code (i.e. hooks) for the admin site (WP dashboard)
	 */
	public function init_admin() {

		parent::init_admin();

		add_filter( 'gform_notification_events', array( $this, 'notification_events' ), 10, 2 );
		add_filter( 'gform_notes_avatar', array( $this, 'notes_avatar' ), 10, 2 );
		add_action( 'gform_post_form_duplicated', array( $this, 'post_form_duplicated' ), 10, 2 );

	}

	/**
	 * Performs upgrade tasks when the version of the Add-On changes. To add additional upgrade tasks, override the upgrade() function, which will only get executed when the plugin version has changed.
	 */
	public function setup() {
		// upgrading Feed Add-On base class
		$installed_version = get_option( 'gravityformsaddon_feed-base_version' );
		if ( $installed_version != $this->_feed_version ) {
			$this->upgrade_base( $installed_version );
			update_option( 'gravityformsaddon_feed-base_version', $this->_feed_version );
		}

		parent::setup();
	}

	private function upgrade_base( $previous_version ) {
		global $wpdb;

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$sql = "CREATE TABLE {$wpdb->prefix}gf_addon_feed (
                  id mediumint(8) unsigned not null auto_increment,
                  form_id mediumint(8) unsigned not null,
                  is_active tinyint(1) not null default 1,
                  feed_order mediumint(8) unsigned not null default 0,
                  meta longtext,
                  addon_slug varchar(50),
                  event_type varchar(20),
                  PRIMARY KEY  (id),
                  KEY addon_form (addon_slug,form_id)
                ) $charset_collate;";

		gf_upgrade()->dbDelta( $sql );

	}

	/**
	 * Gets called when Gravity Forms upgrade process is completed. This function is intended to be used internally, override the upgrade() function to execute database update scripts.
	 * @param $db_version - Current Gravity Forms database version
	 * @param $previous_db_version - Previous Gravity Forms database version
	 * @param $force_upgrade - True if this is a request to force an upgrade. False if this is a standard upgrade (due to version change)
	 */
	public function post_gravityforms_upgrade( $db_version, $previous_db_version, $force_upgrade ) {

		// Forcing Upgrade
		if ( $force_upgrade ) {

			$installed_version = get_option( 'gravityformsaddon_feed-base_version' );

			$this->upgrade_base( $installed_version );

			update_option( 'gravityformsaddon_feed-base_version', $this->_feed_version );

		}

		parent::post_gravityforms_upgrade( $db_version, $previous_db_version, $force_upgrade );
	}

	public function scripts() {

		$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		$scripts = array(
			array(
				'handle'  => 'gform_form_admin',
				'enqueue' => array( array( 'admin_page' => array( 'form_settings' ) ) ),
			),
			array(
				'handle'  => 'gform_gravityforms',
				'enqueue' => array( array( 'admin_page' => array( 'form_settings' ) ) ),
			),
			array(
				'handle'  => 'gform_forms',
				'enqueue' => array( array( 'admin_page' => array( 'form_settings' ) ) ),
			),
			array(
				'handle'  => 'json2',
				'enqueue' => array( array( 'admin_page' => array( 'form_settings' ) ) ),
			),
			array(
				'handle'  => 'gform_placeholder',
				'enqueue' => array(
					array(
						'admin_page'  => array( 'form_settings' ),
						'field_types' => array( 'feed_condition' ),
					),
				)
			),
		);

		if ( $this->_supports_feed_ordering ) {
			$scripts[] = array(
				'handle'    => 'gaddon_feedorder',
				'src'       => $this->get_gfaddon_base_url() . "/js/gaddon_feedorder{$min}.js",
				'version'   => GFCommon::$version,
				'deps'      => array( 'jquery', 'jquery-ui-sortable' ),
				'in_footer' => false,
				'enqueue'   => array(
					array(
						'admin_page' => array( 'form_settings' )
					),
				),
			);
		}

		if( $this->_supports_frontend_feeds ) {
			$scripts[] = array(
				'handle'  => 'gaddon_frontend',
				'src'     => $this->get_gfaddon_base_url() . "/js/gaddon_frontend{$min}.js",
				'deps'    => array( 'jquery', 'gform_conditional_logic' ),
				'version' => GFCommon::$version,
				'enqueue' => array( array( $this, 'has_frontend_feeds' ) ),
			);
		}

		return array_merge( parent::scripts(), $scripts );
	}

	public function uninstall() {
		global $wpdb;
		$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}gf_addon_feed WHERE addon_slug=%s", $this->get_slug() );
		$wpdb->query( $sql );

	}

	//-------- Front-end methods ---------------------------

	/**
	 * Determines what feeds need to be processed for the provided entry.
	 *
	 * @since  1.7.7
	 * @since  2.9.4 Updated to save the processing status for each feed of compatible add-ons.
	 *
	 * @access public
	 *
	 * @param array $entry The Entry Object currently being processed.
	 * @param array $form  The Form Object currently being processed.
	 *
	 * @return array $entry
	 */
	public function maybe_process_feed( $entry, $form ) {
		$entry_id = (int) rgar( $entry, 'id' );

		if ( 'spam' === rgar( $entry, 'status' ) ) {
			$this->log_debug( __METHOD__ . "(): Entry #{$entry_id} is marked as spam; not processing feeds." );

			return $entry;
		}

		$this->log_debug( __METHOD__ . "(): Checking for feeds to process for entry #{$entry_id}." );

		$form_id = (int) rgar( $form, 'id' );
		$feeds   = false;

		// If this is a single submission feed, get the first feed. Otherwise, get all feeds.
		if ( $this->_single_feed_submission ) {
			$feed = $this->get_single_submission_feed( $entry, $form );
			if ( $feed ) {
				$feeds = array( $feed );
			}
		} else {
			$feeds = $this->get_feeds( $form_id );
		}

		// Run filters before processing feeds.
		$feeds = $this->pre_process_feeds( $feeds, $entry, $form );

		// If there are no feeds to process, return.
		if ( empty( $feeds ) ) {
			$this->log_debug( __METHOD__ . "(): No feeds to process for entry #{$entry_id}." );

			return $entry;
		}

		// Determine if feed processing needs to be delayed.
		$is_delayed = $this->maybe_delay_feed( $entry, $form );

		// Initialize array of feeds that have been processed.
		$processed_feeds = array();

		// Loop through feeds.
		foreach ( $feeds as $feed ) {

			// Get the feed name.
			$feed_name = $this->get_feed_name( $feed );
			$feed_id   = (int) rgar( $feed, 'id' );

			// If this feed is inactive, log that it's not being processed and skip it.
			if ( ! $feed['is_active'] ) {
				$this->log_debug( __METHOD__ . "(): Feed is inactive, not processing feed (#{$feed_id} - {$feed_name}) for entry #{$entry_id}." );
				continue;
			}

			// If this feed's condition is not met, log that it's not being processed and skip it.
			if ( ! $this->is_feed_condition_met( $feed, $form, $entry ) ) {
				$this->log_debug( __METHOD__ . "(): Feed condition not met, not processing feed (#{$feed_id} - {$feed_name}) for entry #{$entry_id}." );
				continue;
			}

			// process feed if not delayed
			if ( ! $is_delayed ) {

				// If asynchronous feed processing is enabled, add it to the processing queue.
				if ( $this->is_asynchronous( $feed, $entry, $form ) ) {

					// Log that feed processing is being delayed.
					$this->log_debug( __METHOD__ . "(): Adding feed (#{$feed_id} - {$feed_name}) to the processing queue for entry #{$entry_id}." );

					// Add feed to processing queue.
					gf_feed_processor()->push_to_queue(
						array(
							'addon'    => get_class( $this ),
							'feed'     => $feed,
							'entry_id' => $entry_id,
							'form_id'  => $form_id,
						)
					);
					$this->delay_feed( $feed, $entry, $form );

				} else {

					// All requirements are met; process feed.
					$this->log_debug( __METHOD__ . "(): Starting to process feed (#{$feed_id} - {$feed_name}) for entry #{$entry_id}." );
					$result = $this->process_feed( $feed, $entry, $form );
					$this->save_entry_feed_status( $result, $entry_id, $feed_id, $form_id );

					// If returned value from the process feed call is an array containing an id, set the entry to its value.
					if ( (int) rgar( $result, 'id' ) === $entry_id ) {
						$entry = $result;
					}

					$this->post_process_feed( $feed, $entry, $form );
					$this->fulfill_entry( $entry_id, $form_id );

					// Adding this feed to the list of processed feeds
					$processed_feeds[] = $feed_id;
				}

			} else {

				// Log that feed processing is being delayed.
				$this->log_debug( __METHOD__ . "(): Feed processing is delayed, not processing feed (#{$feed_id} - {$feed_name}) for entry #{$entry_id}." );

				// Delay feed.
				$this->delay_feed( $feed, $entry, $form );

			}
		}

		// If any feeds were processed, save the processed feed IDs.
		if ( ! empty( $processed_feeds ) ) {
			GFAPI::update_processed_feeds_meta( $entry_id, $this->get_slug(), $processed_feeds, $form_id );
		}

		// Return the entry object.
		return $entry;

	}

	/**
	 * Retrieves the name of the given feed.
	 *
	 * @since 2.9.9
	 *
	 * @param array  $feed The feed.
	 * @param string $key  Optional. The key used to store the name.
	 *
	 * @return string
	 */
	public function get_feed_name( $feed, $key = '' ) {
		return GFAPI::get_feed_name( $feed, $key );
	}

	/**
	 * Saves the status of the given feed to the "feed_{$feed_id}_status" entry meta.
	 *
	 * @since 2.9.4
	 *
	 * @param null|bool|array|WP_Error|Exception $result   The value returned by `process_feed()`.
	 * @param int                                $entry_id The ID of the entry the feed was processed for.
	 * @param int                                $feed_id  The ID of the feed that was processed.
	 * @param int                                $form_id  The ID of the form the entry and feed belong to.
	 *
	 * @return void
	 */
	public function save_entry_feed_status( $result, $entry_id, $feed_id, $form_id ) {
		if ( is_null( $result ) ) {
			return;
		}

		$status = array(
			'timestamp' => time(),
			'status'    => 'failed',
			'code'      => '',
			'message'   => '',
			'data'      => '',
		);

		if ( $result === true || is_array( $result ) ) {
			$status['status'] = 'success';
		} elseif ( $result instanceof Exception ) {
			$status['code']    = $result->getCode();
			$status['message'] = $result->getMessage();
		} elseif ( is_wp_error( $result ) ) {
			$status['code']    = $result->get_error_code();
			$status['message'] = $result->get_error_message();
			$status['data']    = $result->get_error_data();
		}

		GFAPI::update_entry_feed_status( $entry_id, $feed_id, $status, $form_id );
	}

	/**
	 * Triggers the post_process_feed hooks.
	 *
	 * @since 2.9.4
	 *
	 * @param array $feed  The feed which was processed.
	 * @param array $entry The entry the feed was processed for.
	 * @param array $form  The form the entry and feed belong to.
	 *
	 * @return void
	 */
	public function post_process_feed( $feed, $entry, $form ) {
		$has_action = array();
		if ( has_action( 'gform_post_process_feed' ) ) {
			$has_action[] = 'gform_post_process_feed';
		}

		$gform_slug_post_process_feed = "gform_{$this->get_slug()}_post_process_feed";
		if ( has_action( $gform_slug_post_process_feed ) ) {
			$has_action[] = $gform_slug_post_process_feed;
		}

		if ( empty( $has_action ) ) {
			return;
		}

		$addon = $this;
		$this->log_debug( __METHOD__ . sprintf( '(): Executing functions hooked to %s for feed #%d and entry #%d.', implode( ' and ', $has_action ), rgar( $feed, 'id' ), rgar( $entry, 'id' ) ) );

		/**
		 * Perform a custom action when a feed has been processed.
		 *
		 * @since 2.0
		 *
		 * @param array       $feed  The feed which was processed.
		 * @param array       $entry The current entry object, which may have been modified by the processed feed.
		 * @param array       $form  The current form object.
		 * @param GFFeedAddOn $addon The current instance of the add-on.
		 */
		do_action( 'gform_post_process_feed', $feed, $entry, $form, $addon );
		do_action( $gform_slug_post_process_feed, $feed, $entry, $form, $addon );
	}

	/**
	 * Sets the "{slug}_is_fulfilled" entry meta.
	 *
	 * @since 2.9.4
	 *
	 * @param int $entry_id The entry ID.
	 * @param int $form_id  The form ID.
	 *
	 * @return void
	 */
	public function fulfill_entry( $entry_id, $form_id ) {
		if ( gform_update_meta( $entry_id, "{$this->get_slug()}_is_fulfilled", true, $form_id ) ) {
			$this->log_debug( __METHOD__ . '(): Entry #' . $entry_id . ' marked as fulfilled.' );
		}
	}

	/**
	 * Determines if feed processing is delayed by another add-on.
	 *
	 * Also enables use of the gform_is_delayed_pre_process_feed filter.
	 *
	 * @param array $entry The Entry Object currently being processed.
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return bool
	 */
	public function maybe_delay_feed( $entry, $form ) {
		if ( $this->_bypass_feed_delay || $this instanceof GFPaymentAddOn ) {
			return false;
		}

		$is_delayed = false;
		$slug       = $this->get_slug();

		/**
		 * Allow feed processing to be delayed.
		 *
		 * @param bool $is_delayed Is feed processing delayed?
		 * @param array $form The Form Object currently being processed.
		 * @param array $entry The Entry Object currently being processed.
		 * @param string $slug The Add-On slug e.g. gravityformsmailchimp
		 */
		$is_delayed = apply_filters( 'gform_is_delayed_pre_process_feed', $is_delayed, $form, $entry, $slug );
		$is_delayed = apply_filters( 'gform_is_delayed_pre_process_feed_' . $form['id'], $is_delayed, $form, $entry, $slug );

		return $is_delayed;
	}

	/**
	 * Retrieves the delay setting for the current add-on from the payment feed.
	 *
	 * @param array $payment_feed The payment feed which is being used to process the current submission.
	 *
	 * @return bool|null
	 */
	public function is_delayed( $payment_feed ) {
		$delay = rgar( $payment_feed['meta'], 'delay_' . $this->get_slug() );

		return $delay;
	}

	/**
	 * Determines if feed processing should happen asynchronously.
	 *
	 * @since  2.2
	 * @access public
	 *
	 * @param array $feed  The Feed Object currently being processed.
	 * @param array $form  The Form Object currently being processed.
	 * @param array $entry The Entry Object currently being processed.
	 *
	 * @return bool
	 */
	public function is_asynchronous( $feed, $entry, $form ) {
		if ( $this->_bypass_feed_delay ) {
			return false;
		}

		/**
		 * Allow feed to be processed asynchronously.
		 *
		 * @since 2.2
		 *
		 * @param bool   $is_asynchronous Is feed being processed asynchronously?
		 * @param array  $feed            The Feed Object currently being processed.
		 * @param array  $entry           The Entry Object currently being processed.
		 * @param array  $form            The Form Object currently being processed.
		 */
		$is_asynchronous = gf_apply_filters( array( 'gform_is_feed_asynchronous', $form['id'], $feed['id'] ), $this->_async_feed_processing, $feed, $entry, $form );

		return $is_asynchronous;

	}

	/**
	 * Determines if the add-on supports processing feeds multiple times for the same entry (e.g. by the async processor).
	 *
	 * @since 2.9.2
	 *
	 * @param array $feed  The feed to be processed
	 * @param array $entry The entry being processed.
	 * @param array $form  The form that the entry belongs to
	 *
	 * @return bool
	 */
	public function is_reprocessing_supported( $feed, $entry, $form ) {
		return $this->_supports_feed_reprocessing;
	}

	/**
	 * Processes feed action.
	 *
	 * @since  Unknown
	 * @since  2.9.4 Documented the supported return types for saving the feed status.
	 *
	 * @access public
	 *
	 * @param array $feed  The Feed Object currently being processed.
	 * @param array $entry The Entry Object currently being processed.
	 * @param array $form  The Form Object currently being processed.
	 *
	 * @return void|null|bool|WP_Error|array The returned value determines if the feed status is saved to the "feed_{$feed_id}_status" entry meta.
	 *                                       - void or null when the feed status should not be saved.
	 *                                       - false or a WP_Error when a failed status should be saved.
	 *                                       - true or the entry array when a success status should be saved.
	 */
	public function process_feed( $feed, $entry, $form ) {

		return;
	}

	public function delay_feed( $feed, $entry, $form ) {

		return;
	}

	public function is_feed_condition_met( $feed, $form, $entry ) {

		$feed_meta            = $feed['meta'];
		$is_condition_enabled = rgar( $feed_meta, 'feed_condition_conditional_logic' ) == true;
		$logic                = rgars( $feed_meta, 'feed_condition_conditional_logic_object/conditionalLogic' );

		if ( ! $is_condition_enabled || empty( $logic ) ) {
			return true;
		}

		return GFCommon::evaluate_conditional_logic( $logic, $form, $entry );
	}

	/**
	 * Create nonce for asynchronous feed processing.
	 *
	 * @since  2.2
	 * @access public
	 *
	 * @return string The nonce.
	 */
	public function create_feed_nonce() {

		$action = 'gform_' . $this->get_slug() . '_process_feed';
		$i      = wp_nonce_tick();

		return substr( wp_hash( $i . $action, 'nonce' ), - 12, 10 );

	}

	/**
	 * Verify nonce for asynchronous feed processing.
	 *
	 * @since  1.0
	 * @access public
	 * @param  string $nonce Nonce to be verified.
	 *
	 * @return int|bool
	 */
	public function verify_feed_nonce( $nonce ) {

		$action = 'gform_' . $this->get_slug() . '_process_feed';
		$i      = wp_nonce_tick();

		// Nonce generated 0-12 hours ago.
		if ( substr( wp_hash( $i . $action, 'nonce' ), - 12, 10 ) === $nonce ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		if ( substr( wp_hash( ( $i - 1 ) . $action, 'nonce' ), - 12, 10 ) === $nonce ) {
			return 2;
		}

		// Log that nonce was unable to be verified.
		$this->log_error( __METHOD__ . '(): Aborting. Unable to verify nonce.' );

		return false;

	}

	/**
	 * Retrieves notification events supported by Add-On.
	 *
	 * @access public
	 * @param array $form
	 * @return array
	 */
	public function supported_notification_events( $form ) {

		return array();

	}

	/**
	 * Add notifications events supported by Add-On to notification events list.
	 *
	 * @access public
	 * @param array $events
	 * @param array $form
	 * @return array $events
	 */
	public function notification_events( $events, $form ) {

		/* Get the supported notification events for this Add-On. */
		$supported_events = $this->supported_notification_events( $form );

		/* If no events are supported, return the current array of events. */
		if ( empty( $supported_events ) ) {
			return $events;
		}

		return array_merge( $events, $supported_events );

	}

	//--------  Feed data methods  -------------------------

	/**
	 * Gets the feeds for the specified form id.
	 *
	 * @since Unknown
	 * @since 2.7.17 Added support for decrypting settings fields.
	 *
	 * @param int $form_id The form id to get feeds for.
	 *
	 * @return array Returns an array of feeds for the specified form id.
	 */
	public function get_feeds( $form_id = null ) {
		global $wpdb;

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}gf_addon_feed
                               WHERE addon_slug=%s {$form_filter} ORDER BY `feed_order`, `id` ASC", $this->get_slug()
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $results as &$result ) {
			$result['meta'] = $this->decrypt_feed_meta( json_decode( $result['meta'], true ) );
		}

		return $results;
	}

	/***
	 * Queries and returns all active feeds for this Add-On
	 *
	 * @since 2.4
	 * @since 2.7.17 Added support for decrypting settings fields.
	 *
	 * @param int $form_id The Form Id to get feeds from.
	 *
	 * @return array Returns an array with all active feeds associated with the specified form Id
	 */
	public function get_active_feeds( $form_id = null ) {
		global $wpdb;

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}gf_addon_feed
                               WHERE addon_slug=%s AND is_active=1 {$form_filter} ORDER BY `feed_order`, `id` ASC", $this->get_slug()
		);

		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach ( $results as &$result ) {
			$result['meta'] = $this->decrypt_feed_meta( json_decode( $result['meta'], true ) );
		}

		return $results;
	}

	/**
	 * Gets the feeds for the specified addon slug and form id.
	 *
	 * @since Unknown
	 * @since 2.7.17 Added support for decrypting settings fields.
	 *
	 * @param string $slug The addon slug to get feeds for.
	 * @param int $form_id (optional) The form id to get feeds for. If not specified, all feeds for the specified addon slug will be returned.
	 *
	 * @return array Returns an array of feeds for the specified form id.
	 */
	public function get_feeds_by_slug( $slug, $form_id = null ) {
		global $wpdb;

		if ( ! $this->addon_feed_table_exists() ) {
			$this->show_table_not_exists_error( $wpdb->prefix . 'gf_addon_feed' );
			return array();
		}

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}gf_addon_feed
                               WHERE addon_slug=%s {$form_filter} ORDER BY `feed_order`, `id` ASC", $slug );

		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach( $results as &$result ) {
			$result['meta'] = $this->decrypt_feed_meta( json_decode( $result['meta'], true ) );
		}

		return $results;
	}

	public function get_current_feed() {
		$feed_id = $this->get_current_feed_id();

		return empty( $feed_id ) ? false : $this->get_feed( $feed_id );
	}

	public function get_current_feed_id() {
		if ( $this->_current_feed_id ) {
			return $this->_current_feed_id;
		} elseif ( ! rgempty( 'gf_feed_id' ) ) {
			return rgpost( 'gf_feed_id' );
		} else {
			return rgget( 'fid' );
		}
	}

	/**
	 * Gets a feed by its id.
	 *
	 * @since Unknown
	 * @since 2.7.17 Added support for decrypting settings fields.
	 *
	 * @param int $id The feed id.
	 *
	 * @return array Returns the feed array if found, false otherwise.
	 */
	public function get_feed( $id ) {
		global $wpdb;

		if ( ! $this->addon_feed_table_exists() ) {
			$this->show_table_not_exists_error( $wpdb->prefix . 'gf_addon_feed' );
			return false;
		}

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}gf_addon_feed WHERE id=%d", $id );

		$row = $wpdb->get_row( $sql, ARRAY_A );
		if ( ! $row ) {
			return false;
		}

		$row['meta'] = $this->decrypt_feed_meta( json_decode( $row['meta'], true ) );

		return $row;
	}

	public function get_feeds_by_entry( $entry_id ) {
		$processed_feeds = gform_get_meta( $entry_id, 'processed_feeds' );
		if ( ! $processed_feeds ) {
			return false;
		}

		return rgar( $processed_feeds, $this->get_slug() );
	}

	public function has_feed( $form_id, $meets_conditional_logic = null ) {

		$feeds = $this->get_feeds( $form_id );
		if ( ! $feeds ) {
			return false;
		}

		$has_active_feed = false;

		if ( $meets_conditional_logic ) {
			$form  = GFFormsModel::get_form_meta( $form_id );
			$entry = GFFormsModel::create_lead( $form );
		}

		foreach ( $feeds as $feed ) {
			if ( ! $has_active_feed && $feed['is_active'] ) {
				$has_active_feed = true;
			}

			if ( $meets_conditional_logic && $feed['is_active'] && $this->is_feed_condition_met( $feed, $form, $entry ) ) {
				return true;
			}
		}

		return $meets_conditional_logic ? false : $has_active_feed;
	}

	/**
	 * Decrypts the feed meta row and return the decripted array.
	 *
	 * @since 2.7.17
	 *
	 * @param array $row The feed meta row to decrypt.
	 *
	 * @return array Returns the feed meta row with values decrypted appropriately.
	 */
	private function decrypt_feed_meta( $row ) {

		return $this->get_encryptor()->decrypt_feed_meta( $row );
	}

	public function get_single_submission_feed( $entry = false, $form = false ) {

		if ( ! $entry && ! $form ) {
			return false;
		}

		$feed = false;
		if ( ! empty( $this->_single_submission_feed ) && ( ! $form || $this->_single_submission_feed['form_id'] == $form['id'] ) ) {
			$feed = $this->_single_submission_feed;
		} elseif ( ! empty( $entry['id'] ) ) {
			$feeds = $this->get_feeds_by_entry( $entry['id'] );
			if ( empty( $feeds ) ) {
				$feed = $this->get_single_submission_feed_by_form( $form, $entry );
			} else {
				$feed = $this->get_feed( $feeds[0] );
			}
		} elseif ( $form ) {
			$feed                          = $this->get_single_submission_feed_by_form( $form, $entry );
			$this->_single_submission_feed = $feed;
		}

		return $feed;
	}

	/**
	 * Return the active feed to be used when processing the current entry, evaluating conditional logic if configured.
	 *
	 * @param array $form The current form.
	 * @param array|false $entry The current entry.
	 *
	 * @return bool|array
	 */
	public function get_single_submission_feed_by_form( $form, $entry ) {
		if ( $form ) {
			$feeds = $this->get_feeds( $form['id'] );

			foreach ( $feeds as $_feed ) {
				if ( $_feed['is_active'] && $this->is_feed_condition_met( $_feed, $form, $entry ) ) {

					return $_feed;
				}
			}
		}

		return false;
	}

	/**
	 * Allows the feeds to be filtered before they are processed.
	 *
	 * @since 2.0
	 *
	 * @param false|array $feeds False or an array of feeds for the current form.
	 * @param array       $entry The entry being processed.
	 * @param array       $form  The form the entry and feeds belong to.
	 *
	 * @return false|array
	 */
	public function pre_process_feeds( $feeds, $entry, $form ) {
		$count   = is_array( $feeds ) ? count( $feeds ) : 0;
		$form_id = (int) rgar( $form, 'id' );
		$this->log_debug( __METHOD__ . "(): Found {$count} feeds for form #{$form_id}." );

		/**
		 * Modify feeds before they are processed.
		 *
		 * @since 2.0
		 *
		 * @param false|array $feeds An array of $feed objects
		 * @param array       $entry Current entry for which feeds will be processed
		 * @param array       $form  Current form object.
		 *
		 * @return array An array of $feeds
		 */
		$feeds = apply_filters( 'gform_addon_pre_process_feeds', $feeds, $entry, $form );
		$feeds = apply_filters( "gform_addon_pre_process_feeds_{$form_id}", $feeds, $entry, $form );
		$feeds = apply_filters( "gform_{$this->get_slug()}_pre_process_feeds", $feeds, $entry, $form );
		$feeds = apply_filters( "gform_{$this->get_slug()}_pre_process_feeds_{$form_id}", $feeds, $entry, $form );

		$filtered_count = is_array( $feeds ) ? count( $feeds ) : 0;
		if ( $filtered_count !== $count ) {
			$this->log_debug( __METHOD__ . "(): {$filtered_count} feeds for form #{$form_id} after filters." );
		}

		return $feeds;
	}

	/**
	 * Get default feed name.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @return string
	 */
	public function get_default_feed_name() {

		/**
		 * Query db to look for two formats that the feed name could have been auto-generated with
		 * format from migration to add-on framework: 'Feed ' . $counter
		 * new auto-generated format when adding new feed: $short_title . ' Feed ' . $counter
		 */

		// Set to zero unless a new number is found while checking existing feed names (will be incremented by 1 at the end).
		$counter_to_use = 0;

		// Get Add-On feeds.
		$feeds_to_filter = $this->get_feeds_by_slug( $this->get_slug() );

		// If feeds were found, loop through and increase counter.
		if ( $feeds_to_filter ) {

			// Loop through feeds and look for name pattern to find what to make default feed name.
			foreach ( $feeds_to_filter as $check ) {

				// Get feed name and trim.
				$name = $this->get_feed_name( $check );
				$name = trim( $name );

				// Prepare feed name pattern.
				$pattern = '/(^Feed|^' . $this->_short_title . ' Feed)\s\d+/';

				// Search for feed name pattern.
				preg_match( $pattern,$name,$matches );

				// If matches were found, increase counter.
				if ( $matches ) {

					// Number should be characters at the end after a space
					$last_space = strrpos( $matches[0], ' ' );

					$digit = substr( $matches[0], $last_space );

					// Counter in existing feed name greater, use it instead.
					if ( $digit >= $counter_to_use ){
						$counter_to_use = $digit;
					}

				}

			}

		}

		// Set default feed name
		$value = $this->_short_title . ' Feed ' . ($counter_to_use + 1);

		return $value;

	}

	public function is_unique_feed_name( $name, $form_id ) {
		$feeds = $this->get_feeds( $form_id );
		foreach ( $feeds as $feed ) {
			$feed_name = $this->get_feed_name( $feed );
			if ( strtolower( $feed_name ) === strtolower( $name ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Updates the feed meta
	 *
	 * @since  Unknown
	 *
	 * @since 2.7.17 Added support for encrypting of settings fields.
	 *
	 * @param int $id     Feed ID
	 * @param array $meta Feed meta to be updated
	 *
	 * @return bool
	 */
	public function update_feed_meta( $id, $meta ) {
		global $wpdb;

		$meta = $this->get_encryptor()->encrypt_feed_meta( $meta, $this->get_fields_to_encrypt() );

		$meta = json_encode( $meta );
		$wpdb->update( "{$wpdb->prefix}gf_addon_feed", array( 'meta' => $meta ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );

		return $wpdb->rows_affected > 0;
	}

	public function update_feed_active( $id, $is_active ) {
		global $wpdb;
		$is_active = $is_active ? '1' : '0';

		$wpdb->update( "{$wpdb->prefix}gf_addon_feed", array( 'is_active' => $is_active ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );

		return $wpdb->rows_affected > 0;
	}

	/**
	 * Insert a new feed record.
	 *
	 * @since Unknown
	 *
	 * @since 2.7.17 Added support for encrypting settings fields.
	 *
	 * @param int $form_id    Form ID.
	 * @param bool $is_active If the feed is active or not.
	 * @param array $meta     Feed meta
	 *
	 * @return false|int Returns the ID of the newly created feed or false if the feed table does not exist.
	 */
	public function insert_feed( $form_id, $is_active, $meta ) {
		global $wpdb;

		if ( ! $this->addon_feed_table_exists() ) {
			$this->show_table_not_exists_error( $wpdb->prefix . 'gf_addon_feed' );
			return false;
		}

		$meta = $this->get_encryptor()->encrypt_feed_meta( $meta, $this->get_fields_to_encrypt() );

		$meta = json_encode( $meta );
		$wpdb->insert( "{$wpdb->prefix}gf_addon_feed", array( 'addon_slug' => $this->get_slug(), 'form_id' => $form_id, 'is_active' => $is_active, 'meta' => $meta ), array( '%s', '%d', '%d', '%s' ) );

		return $wpdb->insert_id;
	}

	/**
	 * Get the array of feed settings field names that are configured to be encrypted.
	 *
	 * @since  2.7.16
	 *
	 * @return array Returns an array with all field names that are configured to be encrypted.
	 */
	public function get_fields_to_encrypt() {

		static $cached_fields_to_encrypt;
		if ( rgar( $cached_fields_to_encrypt, $this->_slug ) ) {
			return $cached_fields_to_encrypt[ $this->_slug ];
		}

		$groups = $this->get_feed_settings_fields();

		// Loop through feed settings fields and create array of fields that are configured to be encrypted
		$fields_to_encrypt = array();
		foreach ( $groups as $group ) {
			if ( ! isset( $group['fields'] ) ) {
				continue;
			}
			foreach ( $group['fields'] as $field ) {
				if ( rgar( $field, 'encrypt' ) ) {
					$fields_to_encrypt[] = $field['name'];
				}
			}
		}
		$cached_fields_to_encrypt[ $this->_slug ] = $fields_to_encrypt;

		return $fields_to_encrypt;
	}

	public function delete_feed( $id ) {
		global $wpdb;

		/**
		 * Allows custom actions to be performed just before a feed is deleted from the database.
		 *
		 * @since 2.4.21
		 *
		 * @param int         $id   The ID of the feed being deleted.
		 * @param GFFeedAddOn $this The current instance of the add-on for which the feed is being deleted.
		 */
		do_action( 'gform_pre_delete_feed', $id, $this );
		do_action( "gform_{$this->get_short_slug()}_pre_delete_feed", $id, $this );

		$wpdb->delete( "{$wpdb->prefix}gf_addon_feed", array( 'id' => $id ), array( '%d' ) );
	}

	public function delete_feeds( $form_id = null ) {
		global $wpdb;

		$form_filter = is_numeric( $form_id ) ? $wpdb->prepare( 'AND form_id=%d', absint( $form_id ) ) : '';

		$sql = $wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}gf_addon_feed
                               WHERE addon_slug=%s {$form_filter} ORDER BY `feed_order`, `id` ASC", $this->get_slug()
		);

		$feed_ids = $wpdb->get_col( $sql );

		if ( ! empty( $feed_ids ) ) {
			array_map( array( $this, 'delete_feed' ), $feed_ids );
		}

	}

	/**
	 * Duplicates the feed.
	 *
	 * @since  1.9.15
	 * @access public
	 *
	 * @param int|array $id          The ID of the feed to be duplicated or the feed object when duplicating a form.
	 * @param mixed     $new_form_id False when using feed actions or the ID of the new form when duplicating a form.
	 *
	 * @uses   GFFeedAddOn::can_duplicate_feed()
	 * @uses   GFFeedAddOn::get_feed()
	 * @uses   GFFeedAddOn::insert_feed()
	 * @uses   GFFeedAddOn::is_unique_feed_name()
	 *
	 * @return int New feed ID.
	 */
	public function duplicate_feed( $id, $new_form_id = false ) {

		// Get original feed.
		$original_feed = is_array( $id ) ? $id : $this->get_feed( $id );

		// If feed doesn't exist, exit.
		if ( ! $original_feed || ! $this->can_duplicate_feed( $original_feed ) ) {
			return;
		}

		// Get feed name key.
		$feed_name_key      = rgars( $original_feed, 'meta/feed_name' ) ? 'feed_name' : 'feedName';
		$original_feed_name = $this->get_feed_name( $original_feed, $feed_name_key );

		// Make sure the new feed name is unique.
		$count     = 2;
		$feed_name = $original_feed_name . ' - ' . esc_html__( 'Copy 1', 'gravityforms' );
		while ( ! $this->is_unique_feed_name( $feed_name, $original_feed['form_id'] ) ) {
			$feed_name = $original_feed_name . ' - ' . sprintf( esc_html__( 'Copy %d', 'gravityforms' ), $count );
			$count++;
		}

		// Copy the feed meta.
		$meta                   = $original_feed['meta'];
		$meta[ $feed_name_key ] = $feed_name;

		if ( ! $new_form_id ) {
			$new_form_id = $original_feed['form_id'];
		}

		// Create the new feed.
		return $this->insert_feed( $new_form_id, $original_feed['is_active'], $meta );

	}

	/**
	 * Checks if Addon Feed table exists.
	 *
	 * @since 2.4.23
	 *
	 * @return bool If Addon Feed table exists.
	 */
	private function addon_feed_table_exists() {
		global $wpdb;
		return $this->table_exists( $wpdb->prefix . 'gf_addon_feed' );
	}

	/**
	 * Get the Table does not exist error message.
	 *
	 * @since 2.4.23
	 *
	 * @param string $table The missing table name.
	 */
	private function get_table_not_exists_error( $table ) {
		$status_page_url = admin_url( 'admin.php?page=gf_system_status' );

		return sprintf(
			// translators: %1$s represents the missing table, %2$s is the opening link tag, %3$s is the closing link tag.
			esc_html__( 'The table `%1$s` does not exist. Please visit the %2$sForms > System Status%3$s page and click the "Re-run database upgrade" link (under the Database section) to create the missing table.', 'gravityforms' ),
			esc_html( $table ),
			'<a href="' . esc_attr( $status_page_url ) . '" target="_blank" rel="noopener">',
			'<span class="screen-reader-text">' . esc_html__('(opens in a new tab)', 'gravityforms') . '</span>&nbsp;<span class="gform-icon gform-icon--external-link"></span></a>'
		);
	}

	/**
	 * Output a Table does not exist error message.
	 *
	 * @since 2.4.23
	 *
	 * @param string $table The missing table name.
	 */
	private function show_table_not_exists_error( $table ) {
		// Prevent the error from being displayed more than once.
		if ( ! empty( $this->_table_error_rendered[ $table ] ) ) {
			return;
		}

		$error   = $this->get_table_not_exists_error( $table );
		$classes = $this->is_gravityforms_supported( '2.5-beta' ) ? 'notice notice-error gf-notice' : 'notice notice-error';

		$notice = sprintf(
			'<div class="%s"><p>%s</p></div>',
			esc_attr( $classes ),
			wp_kses_post( $error )
		);

		$this->_table_error_rendered[ $table ] = true;

		if ( ! did_action( 'admin_notices' ) ) {
			add_action(
				'admin_notices',
				function() use ( $notice ) {
					echo $notice;
				}
			);
			return;
		}

		echo $notice;
	}

	/**
	 * Maybe duplicate feeds when a form is duplicated.
	 *
	 * @param int $form_id The ID of the original form.
	 * @param int $new_id The ID of the duplicate form.
	 */
	public function post_form_duplicated( $form_id, $new_id ) {

		$feeds = $this->get_feeds( $form_id );

		if ( ! $feeds ) {
			return;
		}

		foreach ( $feeds as $feed ) {
			$this->duplicate_feed( $feed, $new_id );
		}

	}

	/**
	 * Save order of feeds.
	 *
	 * @since  2.0
	 * @access public
	 *
	 * @param array $feed_order Array of feed IDs in desired order.
	 */
	public function save_feed_order( $feed_order ) {

		global $wpdb;

		// Reindex feed order to start at 1 instead of 0.
		$feed_order = array_combine( range( 1, count( $feed_order ) ), array_values( $feed_order ) );

		// Swap array keys and values.
		$feed_order = array_flip( $feed_order );

		// Update each feed.
		foreach ( $feed_order as $feed_id => $position ) {

			$wpdb->update(
				$wpdb->prefix . 'gf_addon_feed',
				array( 'feed_order' => $position ),
				array( 'id' => $feed_id ),
				array( '%d' ),
				array( '%d' )
			);

		}

	}

	/* Process feeds when an entry is marked as "not spam"
	 *
	 * @since  2.8.1
	 * @access public
	 *
	 * @param int $entry_id The ID of the entry being processed.
	 * @param string $status The status of the entry being processed.
	 * @param string $prev_status The previous status of the entry being processed.
	 */
	public function process_feed_when_unspammed( $entry_id, $status, $prev_status ) {

		// if this is a payment feed, do not process it.
		if ( $this instanceof GFPaymentAddOn ) {
			return;
		}

		$is_unspammed = $prev_status == 'spam' && $status == 'active';
		if ( ! $is_unspammed ) {
			return;
		}

		$this->log_debug( sprintf( __METHOD__ . '(): Entry has been unspammed (ID: %d). Triggering feed processor.', $entry_id ) );

		$entry = GFAPI::get_entry( $entry_id );
		$form  = GFAPI::get_form( $entry['form_id'] );

		$this->set_payment_gateway( $entry, $form );
		$this->maybe_process_feed( $entry, $form );

	}

	/**
	 * Sets $gf_payment_gateway global for the current entry.
	 *
	 * @since 2.8.1
	 *
	 * @param array $entry The entry being processed.
	 * @param array $form  The form that created the entry.
	 *
	 * @return void
	 */
	private function set_payment_gateway( $entry, $form ) {
		if ( ! class_exists( 'GFPaymentAddOn' ) ) {
			return;
		}

		global $gf_payment_gateway;
		$entry_id = rgar( $entry, 'id' );

		if ( ! empty( $gf_payment_gateway[ $entry_id ] ) ) {
			$this->log_debug( __METHOD__ . '(): Already set to ' . $gf_payment_gateway[ $entry_id ] );

			return;
		}

		$gateway = gform_get_meta( $entry_id, 'payment_gateway' );
		if ( ! empty( $gateway ) ) {
			$this->log_debug( __METHOD__ . '(): Setting using payment_gateway entry meta to ' . $gateway );
			$gf_payment_gateway[ $entry_id ] = $gateway;

			return;
		}

		$this->log_debug( __METHOD__ . '(): Evaluating payment add-ons.' );
		$addons = GFAddOn::get_registered_addons( true );

		foreach ( $addons as $addon ) {
			if ( ! $addon instanceof GFPaymentAddOn ) {
				continue;
			}

			$feed = $addon->get_single_submission_feed( $entry, $form );
			if ( empty( $feed ) ) {
				continue;
			}

			$submission_data = $addon->get_submission_data( $feed, $form, $entry );
			if ( empty( $submission_data ) || ! $addon->is_valid_payment_amount( $submission_data, $feed, $form, $entry ) ) {
				continue;
			}

			$slug = $addon->get_slug();
			$this->log_debug( __METHOD__ . '(): Setting to ' . $slug );
			$gf_payment_gateway[ $entry_id ] = $slug;

			return;
		}

		$this->log_debug( __METHOD__ . '(): Submission was not processed by a payment add-on.' );
	}

	//---------- Form Settings Pages --------------------------

	public function form_settings_init() {
		parent::form_settings_init();
	}

	public function ajax_toggle_is_active() {
		check_ajax_referer( 'feed_list', 'nonce' );

		if ( ! $this->current_user_can_any( $this->get_form_settings_capabilities() ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Access denied.', 'gravityforms' ) ) );
		}

		$feed_id   = rgpost( 'feed_id' );
		$is_active = rgpost( 'is_active' );

		if ( $this->update_feed_active( $feed_id, $is_active ) ) {
			wp_send_json_success();
		}

		die();
	}

	public function ajax_save_feed_order() {
		check_ajax_referer( 'gform_feed_order', 'nonce' );

		if ( ! $this->current_user_can_any( $this->get_form_settings_capabilities() ) ) {
			return;
		}

		$addon      = sanitize_text_field( rgpost( 'addon' ) );
		$form_id    = absint( rgpost( 'form_id' ) );
		$feed_order = rgpost( 'feed_order' ) ? rgpost( 'feed_order' ) : array();
		$feed_order = array_map( 'absint', $feed_order );

		if ( $addon == $this->get_slug() ) {
			$this->save_feed_order( $feed_order );
		}
	}

	public function form_settings_sections() {
		return array();
	}

	public function form_settings( $form ) {
		if ( ! $this->_multiple_feeds || $this->is_detail_page() ) {

			// feed edit page
			$feed_id = $this->_multiple_feeds ? $this->get_current_feed_id() : $this->get_default_feed_id( $form['id'] );

			$this->feed_edit_page( $form, $feed_id );
		} else {
			// feed list UI
			$this->feed_list_page( $form );
		}
	}

	/**
	 * Determine if the current view is the screen for editing a form's feed settings for a given add-on.
	 *
	 * This method first evaluates some base criteria (whether we're in the right view of the Gravity Forms admin),
	 * before determining if we're on the feed edit page depending on add-on specific configuration.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function is_feed_edit_page() {
		$base_criteria = (
			rgget( 'view' ) === 'settings'
			&& rgget( 'subview' ) === $this->get_slug()
		);

		if ( ! $base_criteria ) {
			return false;
		}

		return $this->_multiple_feeds ? is_numeric( rgget( 'fid' ) ) : $this->is_feed_list_page();
	}

	public function is_feed_list_page() {
		return ! isset( $_GET['fid'] );
	}

	public function is_detail_page() {
		return ! $this->is_feed_list_page();
	}

	public function form_settings_header() {
		if ( $this->is_feed_list_page() ) {
			$title = $this->form_settings_title();
			$url = add_query_arg( array( 'fid' => 0 ) );
			return $title . " <a class='add-new-h2' href='" . esc_url( $url ) . "'>" . esc_html__( 'Add New', 'gravityforms' ) . '</a>';
		}
	}

	public function form_settings_title() {
		return sprintf( esc_html__( '%s Feeds', 'gravityforms' ), $this->get_short_title() );
	}

	/**
	 * Initialize feed settings page.
	 * Creates new instance of Settings framework.
	 *
	 * @since 2.5
	 */
	public function feed_settings_init() {
		// Get current form.
		$form = ( $this->get_current_form() ) ? $this->get_current_form() : array();
		$form = GFCommon::gform_admin_pre_render( $form );

		// Get current feed ID, feed object.
		$feed_id      = $this->_multiple_feeds ? $this->get_current_feed_id() : $this->get_default_feed_id( rgar( $form, 'id', 0 ) );
		$current_feed = $feed_id ? $this->get_feed( $feed_id ) : array();

		// Initialize new settings renderer.
		$renderer = new Settings(
			array(
				'capability'     => $this->get_form_settings_capabilities(),
				'initial_values' => rgar( $current_feed, 'meta' ),
				'save_callback'  => function( $values ) use ( $feed_id ) {

					// Adjust conditional logic object.
					if ( rgars( $values, 'feed_condition_conditional_logic_object/actionType' ) ) {
						$values['feed_condition_conditional_logic_object'] = array( 'conditionalLogic' => $values['feed_condition_conditional_logic_object'] );
					}

					// Save settings.
					$this->_current_feed_id = $this->save_feed_settings( $feed_id, rgget( 'id' ), $values );

					// If feed IDs do not match, redirect.
					if ( $feed_id !== $this->_current_feed_id && $this->_multiple_feeds ) {
						wp_safe_redirect( esc_url_raw( add_query_arg( array( 'fid' => $this->_current_feed_id ) ) ) );
					}

				},
				'before_fields'  => function() use ( $form ) {
					$script     = sprintf( 'var form = %s;', wp_json_encode( $form ) );
					$entry_meta = $this->get_feed_settings_entry_meta( $form );
					if ( ! empty( $entry_meta ) ) {
						$script .= sprintf( 'var entry_meta = %s;', wp_json_encode( $entry_meta ) );
					}

					$before_fields = sprintf(
					'<input type="hidden" name="gf_feed_id" value="%d" />%s',
						(int) $this->get_current_feed_id(),
						GFCommon::get_inline_script_tag( $script, false )
					);

					/*
					 * Filters the content to be displayed before the feed settings fields.
					 *
					 * @since 2.9.5
					 *
					 * @param string $before_fields The content to be displayed before the feed settings fields.
					 * @param array  $form          The form associated with the feed.
					 */
					return gf_apply_filters( array( 'gform_feed_settings_before_fields', rgar( $form, 'id' ) ), $before_fields, $form );
				},
			)
		);

		// Save renderer to instance.
		$this->set_settings_renderer( $renderer );

		// Set fields.
		$sections = $this->get_feed_settings_fields();
		$sections = $this->prepare_settings_sections( $sections, 'feed_settings' );
		$this->get_settings_renderer()->set_fields( $sections );

		// Set validation message on redirect.
		$this->get_settings_renderer()->set_postback_message_callback( function( $message ) use ( $renderer ) {

			// Get referrer.
			$referrer = rgar( $_SERVER, 'HTTP_REFERER' );

			// If referrer not provided, return.
			if ( ! $referrer ) {
				return $message;
			}

			// Parse URL, get feed ID.
			$query_args = array();
			$referrer = wp_parse_url( $referrer );
			parse_str( rgar( $referrer, 'query' ), $query_args );

			if ( rgar( $query_args, 'fid' ) == '0' && empty( $_POST ) ) {
				return $renderer->get_save_success_message();
			}

			return $message;

		} );

		if ( ! $this->get_settings_renderer()->is_save_postback() ) {
			return;
		}

		$this->get_settings_renderer()->process_postback();
	}

	/**
	 * Returns an array of entry meta fields to be assigned to the JavaScript entry_meta variable used by the feed condition setting.
	 *
	 * @since 2.9
	 *
	 * @param array $form       The form the feed is being created or edited for.
	 * @param array $entry_meta An empty array or the entry meta fields to be assigned to the JavaScript entry_meta variable.
	 *
	 * @return array
	 */
	public function get_feed_settings_entry_meta( $form, $entry_meta = array() ) {
		/**
		 * Allows population of the JavaScript entry_meta variable on the feed configuration page.
		 *
		 * @since 2.9
		 *
		 * @param array       $entry_meta An empty array or the entry meta fields to be assigned to the JavaScript entry_meta variable.
		 * @param array       $form       The form the feed is being created or edited for.
		 * @param GFFeedAddOn $addon      The current add-on instance.
		 */
		return apply_filters( 'gform_entry_meta_pre_render_feed_settings', $entry_meta, $form, $this );
	}

	/**
	 * Render feed edit page.
	 *
	 * @since Unknown
	 *
	 * @param array $form    Current Form object.
	 * @param int   $feed_id Current feed ID.
	 */
	public function feed_edit_page( $form, $feed_id ) {

		// Prepare page title.
		$title = sprintf( '<h3><span>%s</span></h3>', $this->feed_settings_title() );

		// If feed creation is disabled, display configuration message.
		if ( ! $this->can_create_feed() ) {
			printf( '%s<div>%s</div>', $title, $this->configure_addon_message() );
			return;
		}

		// Output required scripts.
		printf( '<script type="text/javascript">%s</script>', GFFormSettings::output_field_scripts( false ) );

		// Render Settings framework or error message.
		if ( ! $this->get_settings_renderer() ) {
			$this->log_debug( __METHOD__ . '(): Could not load add-on settings. Settings renderer not initialized.' );
			printf( '%s<p>%s</p>', $title, esc_html__( 'Unable to render feed settings.', 'gravityforms' ) );

			return;
		}

		$this->get_settings_renderer()->render();
	}

	public function settings( $sections ) {

		parent::settings( $sections );

		?>
		<input type="hidden" name="gf_feed_id" value="<?php echo esc_attr( $this->get_current_feed_id() ); ?>" />
		<?php

	}

	public function feed_settings_title() {
		return esc_html__( 'Feed Settings', 'gravityforms' );
	}

	public function feed_list_page( $form = null ) {
		global $wpdb;

		if ( ! $this->addon_feed_table_exists() ) {
			$this->show_table_not_exists_error( $wpdb->prefix . 'gf_addon_feed' );
			return;
		}

		$action = $this->get_bulk_action();
		if ( $action ) {
			check_admin_referer( 'feed_list', 'feed_list' );
			$this->process_bulk_action( $action );
		}

		$single_action = rgpost( 'single_action' );
		if ( ! empty( $single_action ) ) {
			check_admin_referer( 'feed_list', 'feed_list' );
			$this->process_single_action( $single_action );
		}

		?>

		<div class="gform-settings-panel">
			<header class="gform-settings-panel__header">
				<h4 class="gform-settings-panel__title"><span><?php echo $this->feed_list_title() ?></span></h4>
			</header>

			<div class="gform-settings-panel__content">
				<form id="gform-settings" action="" method="post">
					<?php
					$feed_list = $this->get_feed_table( $form );
					$feed_list->prepare_items();
					$feed_list->display();
					?>

					<!--Needed to save state after bulk operations-->
					<input type="hidden" value="gf_edit_forms" name="page">
					<input type="hidden" value="settings" name="view">
					<input type="hidden" value="<?php echo esc_attr( $this->get_slug() ); ?>" name="subview">
					<input type="hidden" value="<?php echo esc_attr( rgar( $form, 'id' ) ); ?>" name="id">
					<input id="single_action" type="hidden" value="" name="single_action">
					<input id="single_action_argument" type="hidden" value="" name="single_action_argument">
					<?php wp_nonce_field( 'feed_list', 'feed_list' ) ?>
				</form>
			</div>
		</div>

		<script type="text/javascript">
			<?php

				GFCommon::gf_vars();

				if ( $this->_supports_feed_ordering ) {

					// Prepare feed ordering options.
					$feed_order_options = array(
						'addon'  => $this->get_slug(),
						'formId' => rgar( $form, 'id' ),
						'nonce'  => wp_create_nonce( 'gform_feed_order' ),
					);

					echo 'jQuery( document ).ready( function() {
						window.GFFeedOrderObj = new GFFeedOrder( ' . json_encode( $feed_order_options ) . ');
					} );';

				}

			?>
		</script>

	<?php
	}

	public function get_feed_table( $form ) {

		$columns               = $this->feed_list_columns();
		$column_value_callback = array( $this, 'get_column_value' );
		$feeds                 = $this->get_feeds( rgar( $form, 'id' ) );
		$bulk_actions          = $this->get_bulk_actions();
		$action_links          = $this->get_action_links();
		$no_item_callback      = array( $this, 'feed_list_no_item_message' );
		$message_callback      = array( $this, 'feed_list_message' );

		return new GFAddOnFeedsTable( $feeds, $this->get_slug(), $columns, $bulk_actions, $action_links, $column_value_callback, $no_item_callback, $message_callback, $this );
	}

	public function feed_list_title() {
		return $this->form_settings_title();
	}

	public function maybe_save_feed_settings( $feed_id, $form_id ) {

		if ( ! rgpost( 'gform-settings-save' ) ) {
			return $feed_id;
		}

		check_admin_referer( $this->get_slug() . '_save_settings', '_' . $this->get_slug() . '_save_settings_nonce' );

		if ( ! $this->current_user_can_any( $this->get_form_settings_capabilities() ) ) {
			GFCommon::add_error_message( esc_html__( "You don't have sufficient permissions to update the form settings.", 'gravityforms' ) );
			return $feed_id;
		}

		// store a copy of the previous settings for cases where action would only happen if value has changed.
		$feed = $this->get_feed( $feed_id );
		$this->set_previous_settings( rgar( $feed, 'meta' ) );

		$settings = $this->get_posted_settings();
		$sections = $this->get_feed_settings_fields();
		$settings = $this->trim_conditional_logic_vales( $settings, $form_id );

		$is_valid = $this->validate_settings( $sections, $settings );
		$result   = false;

		if ( $is_valid ) {
			$settings = $this->filter_settings( $sections, $settings );
			$feed_id = $this->save_feed_settings( $feed_id, $form_id, $settings );
			if ( $feed_id ) {
				GFCommon::add_message( $this->get_save_success_message( $sections ) );
			} else {
				GFCommon::add_error_message( $this->get_save_error_message( $sections ) );
			}
		} else {
			GFCommon::add_error_message( $this->get_save_error_message( $sections ) );
		}

		return $feed_id;
	}

	public function trim_conditional_logic_vales( $settings, $form_id ) {
		if ( ! is_array( $settings ) ) {
			return $settings;
		}
		if ( isset( $settings['feed_condition_conditional_logic_object'] ) && is_array( $settings['feed_condition_conditional_logic_object'] ) ) {
			$form                                                = GFFormsModel::get_form_meta( $form_id );
			$settings['feed_condition_conditional_logic_object'] = GFFormsModel::trim_conditional_logic_values_from_element( $settings['feed_condition_conditional_logic_object'], $form );
		}

		return $settings;
	}

	public function get_save_success_message( $sections ) {
		if ( ! $this->is_detail_page() )
			return parent::get_save_success_message( $sections );

		$save_button = $this->get_save_button( $sections );

		return isset( $save_button['messages']['success'] ) ? $save_button['messages']['success'] : esc_html__( 'Feed updated successfully.', 'gravityforms' );
	}

	public function get_save_error_message( $sections ) {
		if ( ! $this->addon_feed_table_exists() ) {
			global $wpdb;
			return $this->get_table_not_exists_error( $wpdb->prefix . 'gf_addon_feed' );
		}

		if ( ! $this->is_detail_page() )
			return parent::get_save_error_message( $sections );

		$save_button = $this->get_save_button( $sections );

		return isset( $save_button['messages']['error'] ) ? $save_button['messages']['error'] : esc_html__( 'There was an error updating this feed. Please review all errors below and try again.', 'gravityforms' );
	}

	public function save_feed_settings( $feed_id, $form_id, $settings ) {

		if ( $feed_id ) {
			$this->update_feed_meta( $feed_id, $settings );
			$result = $feed_id;
		} else {
			$result = $this->insert_feed( $form_id, true, $settings );
		}

		/**
		 * Perform a custom action when a feed is saved.
		 *
		 * @param string  $feed_id 	The ID of the feed which was saved.
		 * @param int 	  $form_id 	The current form ID associated with the feed.
		 * @param array   $settings	An array containing the settings and mappings for the feed.
		 * @param GFAddOn $addon 	The current instance of the GFAddOn object which extends GFFeedAddOn or GFPaymentAddOn (i.e. GFCoupons, GF_User_Registration, GFStripe).
		 *
		 * @since 2.4.12.3
		 */
		do_action( 'gform_post_save_feed_settings', $result, $form_id, $settings, $this );

		return $result;
	}

	public function get_feed_settings_fields() {

		if ( ! empty( $this->_feed_settings_fields ) ) {
			return $this->_feed_settings_fields;
		}

		/**
		 * Filter the feed settings fields (typically before they are rendered on the Feed Settings edit view).
		 *
		 * @param array $feed_settings_fields An array of feed settings fields which will be displayed on the Feed Settings edit view.
		 * @param object $addon The current instance of the GFAddon object (i.e. GF_User_Registration, GFPayPal).
		 *
		 * @since 2.0
		 *
		 * @return array
		 */
		$feed_settings_fields = apply_filters( 'gform_addon_feed_settings_fields', $this->feed_settings_fields(), $this );
		$feed_settings_fields = apply_filters( "gform_{$this->get_slug()}_feed_settings_fields", $feed_settings_fields, $this );

		$this->_feed_settings_fields = $this->add_default_feed_settings_fields_props( $feed_settings_fields );

		return $this->_feed_settings_fields;
	}

	public function feed_settings_fields() {
		return array();
	}

	public function add_default_feed_settings_fields_props( $fields ) {

		foreach ( $fields as &$section ) {
			if ( ! rgar( $section, 'fields' ) ) {
				continue;
			}

			foreach ( $section['fields'] as &$field ) {
				switch ( $field['type'] ) {

					case 'hidden':
						$field['hidden'] = true;
						break;
				}

				if ( rgar( $field, 'name' ) === 'feedName' ) {
					$field['default_value'] = $this->get_default_feed_name();
				}
			}
		}

		return $fields;
	}

	private function get_bulk_action() {
		$action = rgpost( 'action' );
		if ( empty( $action ) || $action == '-1' ) {
			$action = rgpost( 'action2' );
		}

		return empty( $action ) || $action == '-1' ? false : $action;
	}

	/***
	 * Override this function to add custom bulk actions
	 */
	public function get_bulk_actions() {
		$bulk_actions = array(
			'delete'    => esc_html__( 'Delete', 'gravityforms' ),
		);

		return $bulk_actions;
	}

	/***
	 * Override this function to process custom bulk actions added via the get_bulk_actions() function
	 *
	 * @param string $action : The bulk action selected by the user
	 */
	public function process_bulk_action( $action ) {
		if ( 'delete' === $action ) {
			$feeds = rgpost( 'feed_ids' );
			if ( is_array( $feeds ) ) {
				foreach ( $feeds as $feed_id ) {
					$this->delete_feed( $feed_id );
				}
			}
		}
		if ( 'duplicate' === $action ) {
			$feeds = rgpost( 'feed_ids' );
			if ( is_array( $feeds ) ) {
				foreach ( $feeds as $feed_id ) {
					$this->duplicate_feed( $feed_id );
				}
			}
		}
	}

	public function process_single_action( $action ) {
		if ( $action == 'delete' ) {
			$feed_id = absint( rgpost( 'single_action_argument' ) );
			$this->delete_feed( $feed_id );
		}
		if ( $action == 'duplicate' ) {
			$feed_id = absint( rgpost( 'single_action_argument' ) );
			$this->duplicate_feed( $feed_id );
		}
	}

	public function get_action_links() {
		$feed_id       = '_id_';
		$edit_url      = add_query_arg( array( 'fid' => $feed_id ) );
		$links         = array(
			'edit'      => '<a href="' . esc_url( $edit_url ) . '">' . esc_html__( 'Edit', 'gravityforms' ) . '</a>',
			'duplicate' => '<a href="#" onclick="gaddon.duplicateFeed(\'' . esc_js( $feed_id ) . '\');" onkeypress="gaddon.duplicateFeed(\'' . esc_js( $feed_id ) . '\');">' . esc_html__( 'Duplicate', 'gravityforms' ) . '</a>',
			'delete'    => '<a class="submitdelete" onclick="javascript: if(confirm(\'' . esc_js( __( 'WARNING: You are about to delete this item.', 'gravityforms' ) ) . esc_js( __( "'Cancel' to stop, 'OK' to delete.", 'gravityforms' ) ) . '\')){ gaddon.deleteFeed(\'' . esc_js( $feed_id ) . '\'); }" onkeypress="javascript: if(confirm(\'' . esc_js( __( 'WARNING: You are about to delete this item.', 'gravityforms' ) ) . esc_js( __( "'Cancel' to stop, 'OK' to delete.", 'gravityforms' ) ) . '\')){ gaddon.deleteFeed(\'' . esc_js( $feed_id ) . '\'); }" style="cursor:pointer;">' . esc_html__( 'Delete', 'gravityforms' ) . '</a>'
		);

		return $links;
	}

	public function feed_list_columns() {
		return array();
	}

	/**
	 * Override this function to change the message that is displayed when the feed list is empty
	 * @return string The message
	 */
	public function feed_list_no_item_message() {
		$url = add_query_arg( array( 'fid' => 0 ) );
		return sprintf( esc_html__( "You don't have any feeds configured. Let's go %screate one%s!", 'gravityforms' ), "<a href='" . esc_url( $url ) . "'>", '</a>' );
	}

	/**
	 * Override this function to force a message to be displayed in the feed list (instead of data). Useful to alert users when main plugin settings haven't been completed.
	 * @return string|false
	 */
	public function feed_list_message() {
		if ( ! $this->can_create_feed() ) {
			return $this->configure_addon_message();
		}

		return false;
	}

	public function configure_addon_message() {

		$settings_label = sprintf( __( '%s Settings', 'gravityforms' ), $this->get_short_title() );
		$settings_link  = sprintf( '<a href="%s">%s</a>', esc_url( $this->get_plugin_settings_url() ), $settings_label );

		return sprintf( __( 'To get started, please configure your %s.', 'gravityforms' ), $settings_link );

	}

	/**
	 * Override this function to prevent the feed creation UI from being rendered.
	 * @return boolean|true
	 */
	public function can_create_feed() {
		return true;
	}

	/**
	 * Override this function to allow the feed to being duplicated.
	 *
	 * @access public
	 * @param int|array $id The ID of the feed to be duplicated or the feed object when duplicating a form.
	 * @return boolean|true
	 */
	public function can_duplicate_feed( $id ) {
		return false;
	}

	public function get_column_value( $item, $column ) {
		if ( is_callable( array( $this, "get_column_value_{$column}" ) ) ) {
			return call_user_func( array( $this, "get_column_value_{$column}" ), $item );
		} elseif ( isset( $item[ $column ] ) ) {
			return $item[ $column ];
		} elseif ( isset( $item['meta'][ $column ] ) ) {
			return $item['meta'][ $column ];
		}
	}


	public function update_form_settings( $form, $new_form_settings ) {
		$feed_id = rgar( $new_form_settings, 'id' );
		foreach ( $new_form_settings as $key => $value ) {
			$form[ $this->get_slug() ]['feeds'][ $feed_id ][ $key ] = $value;
		}

		return $form;
	}

	public function get_default_feed_id( $form_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}gf_addon_feed WHERE addon_slug=%s AND form_id = %d LIMIT 0,1", $this->get_slug(), $form_id );

		$feed_id = $wpdb->get_var( $sql );
		if ( ! $feed_id ) {
			$feed_id = 0;
		}

		return $feed_id;
	}

	public function settings_feed_condition( $field, $echo = true ) {

		$conditional_logic = $this->get_feed_condition_conditional_logic();
		$checkbox_field = $this->get_feed_condition_checkbox( $field );

		$hidden_field = $this->get_feed_condition_hidden_field();
		$instructions = isset( $field['instructions'] ) ? $field['instructions'] : esc_html__( 'Process this feed if', 'gravityforms' );
		$html         = $this->settings_checkbox( $checkbox_field, false );
		$html .= $this->settings_hidden( $hidden_field, false );
		$html .= '<div id="feed_condition_conditional_logic_container"><!-- dynamically populated --></div>';
		$html .= '<script type="text/javascript"> var feedCondition = new FeedConditionObj({' .
			'strings: { objectDescription: "' . esc_attr( $instructions ) . '" },' .
			'logicObject: ' . $conditional_logic .
			'}); </script>';

		if ( $this->field_failed_validation( $field ) ) {
			$html .= $this->get_error_icon( $field );
		}

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	public function get_feed_condition_checkbox( $field ) {
		$checkbox_label = isset( $field['checkbox_label'] ) ? $field['checkbox_label'] : esc_html__( 'Enable Condition', 'gravityforms' );

		$checkbox_field  = array(
			'name'    => 'feed_condition_conditional_logic',
			'type'    => 'checkbox',
			'choices' => array(
				array(
					'label' => $checkbox_label,
					'name'  => 'feed_condition_conditional_logic',
				),
			),
			'onclick' => 'ToggleConditionalLogic( false, "feed_condition" );',
		);

		return $checkbox_field;
	}

	public function get_feed_condition_hidden_field() {
		$conditional_logic = $this->get_feed_condition_conditional_logic();
		$hidden_field = array(
			'name'  => 'feed_condition_conditional_logic_object',
			'type'  => 'hidden',
			'value' => $conditional_logic,
		);
		return $hidden_field;
	}

	public function get_feed_condition_conditional_logic() {
		$conditional_logic_object = $this->get_setting( 'feed_condition_conditional_logic_object' );
		if ( $conditional_logic_object ) {
			$form_id           = rgget( 'id' );
			$form              = GFFormsModel::get_form_meta( $form_id );
			$conditional_logic = json_encode( GFFormsModel::trim_conditional_logic_values_from_element( $conditional_logic_object, $form ) );
		} else {
			$conditional_logic = '{}';
		}
		return $conditional_logic;
	}

	public function validate_feed_condition_settings( $field, $settings ) {
		$checkbox_field = $this->get_feed_condition_checkbox( $field );
		$this->validate_checkbox_settings( $checkbox_field, $settings );

		if ( ! isset( $settings['feed_condition_conditional_logic_object'] ) ) {
			return;
		}

		$conditional_logic_object = $settings['feed_condition_conditional_logic_object'];
		if ( ! isset( $conditional_logic_object['conditionalLogic'] ) ) {
			return;
		}
		$conditional_logic = $conditional_logic_object['conditionalLogic'];
		$conditional_logic_safe = GFFormsModel::sanitize_conditional_logic( $conditional_logic );
		if ( serialize( $conditional_logic ) != serialize( $conditional_logic_safe ) ) {
			$this->set_field_error( $field, esc_html__( 'Invalid value', 'gravityforms' ) );
		}
	}

	public static function add_entry_meta( $form ) {
		$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
		$keys       = array_keys( $entry_meta );
		foreach ( $keys as $key ) {
			array_push( $form['fields'], array( 'id' => $key, 'label' => $entry_meta[ $key ]['label'] ) );
		}

		return $form;
	}

	public function has_feed_condition_field() {

		$fields = $this->settings_fields_only( 'feed' );

		foreach ( $fields as $field ) {
			if ( $field['type'] == 'feed_condition' ) {
				return true;
			}
		}

		return false;
	}

	public function add_delayed_payment_support( $options ) {
		$this->delayed_payment_integration = $options;

		if ( is_admin() ) {
			add_filter( 'gform_addon_feed_settings_fields', array( $this, 'add_post_payment_actions' ), 10, 2 );
		}

		add_action( 'gform_paypal_fulfillment', array( $this, 'paypal_fulfillment' ), 10, 4 );
		add_action( 'gform_trigger_payment_delayed_feeds', array( $this, 'action_trigger_payment_delayed_feeds' ), 10, 4 );
	}

	/**
	 * Add the Post Payments Actions setting to the PayPal feed.
	 *
	 * @since 2.4.13  Call $this->add_post_payment_actions().
	 * @since Unknown
	 *
	 * @param array $feed_settings_fields The PayPal feed settings.
	 *
	 * @return array
	 */
	public function add_paypal_post_payment_actions( $feed_settings_fields ) {
		_deprecated_function( 'add_paypal_post_payment_actions', '2.4.13', 'add_post_payment_actions' );

		if ( ! $this instanceof GFPayPal ) {
			return $feed_settings_fields;
		}

		return $this->add_post_payment_actions( $feed_settings_fields, $this );
	}

	/**
	 * Add the Post Payments Actions setting to the payment add-on feed.
	 *
	 * @since 2.4.13  Added the $addon arg enabling support for other payment add-ons.
	 * @since Unknown
	 *
	 * @param array   $feed_settings_fields The add-on feed settings.
	 * @param GFAddOn $addon                The current instance of the add-on (i.e. GF_User_Registration, GFPayPal).
	 *
	 * @return array
	 */
	public function add_post_payment_actions( $feed_settings_fields, $addon ) {

		if ( ! $addon instanceof GFPaymentAddOn ) {
			return $feed_settings_fields;
		}

		$config = $addon->get_post_payment_actions_config( $this->get_slug() );

		if ( empty( $config ) ) {
			return $feed_settings_fields;
		}

		$form_id = absint( rgget( 'id' ) );
		if ( $this->has_feed( $form_id ) ) {

			$addon_label = rgar( $this->delayed_payment_integration, 'option_label' );
			$choice      = array(
				'label' => $addon_label ? $addon_label : sprintf( esc_html__( 'Process %s feed only when payment is received.', 'gravityforms' ), $this->get_short_title() ),
				'name'  => 'delay_' . $this->get_slug(),
			);

			$field_name = 'post_payment_actions';
			$field      = $this->get_field( $field_name, $feed_settings_fields );

			if ( ! $field ) {

				$fields = array(
					array(
						'name'    => $field_name,
						'label'   => esc_html__( 'Post Payment Actions', 'gravityforms' ),
						'type'    => 'checkbox',
						'choices' => array( $choice ),
						'tooltip' => '<h6>' . esc_html__( 'Post Payment Actions', 'gravityforms' ) . '</h6>' . esc_html__( 'Select which actions should only occur after payment has been received.', 'gravityforms' )
					)
				);

				$setting = rgar( $config, 'setting', 'options' );

				if ( rgar( $config, 'position' ) === 'before' ) {
					$feed_settings_fields = $this->add_field_before( $setting, $fields, $feed_settings_fields );
				} else {
					$feed_settings_fields = $this->add_field_after( $setting, $fields, $feed_settings_fields );
				}

			} else {

				$field['choices'][]   = $choice;
				$feed_settings_fields = $this->replace_field( $field_name, $field, $feed_settings_fields );

			}
		}

		return $feed_settings_fields;
	}

	/**
	 * Triggers processing of feeds delayed by the PayPal add-on.
	 *
	 * @since 2.4.13 Updated to use action_trigger_payment_delayed_feeds().
	 * @since unknown
	 *
	 * @param array  $entry          The entry currently being processed.
	 * @param array  $paypal_config  The payment feed which originated the transaction.
	 * @param string $transaction_id The transaction or subscription ID.
	 * @param string $amount         The transaction amount.
	 */
	public function paypal_fulfillment( $entry, $paypal_config, $transaction_id, $amount ) {
		$this->action_trigger_payment_delayed_feeds( $transaction_id, $paypal_config, $entry );
	}

	/**
	 * Triggers processing of feeds delayed by payment add-ons.
	 *
	 * @since 2.4.13
	 *
	 * @param string     $transaction_id The transaction or subscription ID.
	 * @param array      $payment_feed   The payment feed which originated the transaction.
	 * @param array      $entry          The entry currently being processed.
	 * @param null|array $form           The form currently being processed or null for the legacy PayPal integration.
	 */
	public function action_trigger_payment_delayed_feeds( $transaction_id, $payment_feed, $entry, $form = null ) {
		$this->log_debug( __METHOD__ . '(): Checking fulfillment for transaction ' . $transaction_id . ' for ' . $payment_feed['addon_slug'] );

		$is_fulfilled = gform_get_meta( $entry['id'], "{$this->get_slug()}_is_fulfilled" );
		if ( $is_fulfilled || ! $this->is_delayed( $payment_feed ) ) {
			$this->log_debug( __METHOD__ . '(): Entry ' . $entry['id'] . ' is already fulfilled or feeds are not delayed. No action necessary.' );

			return;
		}

		if ( is_null( $form ) ) {
			$form = GFFormsModel::get_form_meta( $entry['form_id'] );
		}

		$this->_bypass_feed_delay = true;
		$this->maybe_process_feed( $entry, $form );
    }

	//--------------- Notes ------------------

	/**
	 * Writes to the add-on log and adds an entry note when a feed processing error occurs.
	 *
	 * @since 1.9.12
	 *
	 * @param string $error_message The error message.
	 * @param array  $feed          The feed which was being processed when the error occurred.
	 * @param array  $entry         The entry which was being processed when the error occurred.
	 * @param array  $form          The form which was being processed when the error occurred.
	 */
	public function add_feed_error( $error_message, $feed, $entry, $form ) {

		/* Log debug error before we prepend the error name. */
		$backtrace = debug_backtrace();
		$method    = $backtrace[1]['class'] . '::' . $backtrace[1]['function'];
		$this->log_error( $method . '(): ' . $error_message );

		/* Prepend feed name to the error message. */
		$note_error_message = $this->get_feed_name( $feed ) . ': ' . $error_message;

		/* Add error note to the entry. */
		$this->add_note( $entry['id'], $note_error_message, 'error' );

		/* Get Add-On slug */
		$slug = str_replace( 'gravityforms', '', $this->get_slug() );

		/**
		 * Process any error actions.
		 *
		 * @since 1.9.12
		 * @since 2.4.15 Added $error_message as the fourth param.
		 *
		 * @param array  $feed          The feed which was being processed when the error occurred.
		 * @param array  $entry         The entry which was being processed when the error occurred.
		 * @param array  $feed          The form which was being processed when the error occurred.
		 * @param string $error_message The error message.
		 */
		gf_do_action( array( "gform_{$slug}_error", $form['id'] ), $feed, $entry, $form, $error_message );

	}

	// TODO: Review for Deprecation ------------------

	public function get_paypal_feed( $form_id, $entry ) {

		if ( ! class_exists( 'GFPayPal' ) ) {
			return false;
		}

		if ( method_exists( 'GFPayPal', 'get_config_by_entry' ) ) {
			$feed = GFPayPal::get_config_by_entry( $entry );
		} elseif ( method_exists( 'GFPayPal', 'get_config' ) ) {
			$feed = GFPayPal::get_config( $form_id );
		} else {
			$feed = false;
		}

		return $feed;
	}

	public function has_paypal_payment( $feed, $form, $entry ) {

		$products = GFCommon::get_product_fields( $form, $entry );

		$payment_field   = $feed['meta']['transactionType'] === 'product' ? $feed['meta']['paymentAmount'] : $feed['meta']['recurringAmount'];
		$setup_fee_field = rgar( $feed['meta'], 'setupFee_enabled' ) ? $feed['meta']['setupFee_product'] : false;
		$trial_field     = rgar( $feed['meta'], 'trial_enabled' ) ? rgars( $feed, 'meta/trial_product' ) : false;

		$amount       = 0;
		$line_items   = array();
		$discounts    = array();
		$fee_amount   = 0;
		$trial_amount = 0;
		foreach ( $products['products'] as $field_id => $product ) {

			$quantity      = $product['quantity'] ? $product['quantity'] : 1;
			$product_price = GFCommon::to_number( $product['price'] );

			$options = array();
			if ( is_array( rgar( $product, 'options' ) ) ) {
				foreach ( $product['options'] as $option ) {
					$options[] = $option['option_name'];
					$product_price += $option['price'];
				}
			}

			$is_trial_or_setup_fee = false;

			if ( ! empty( $trial_field ) && $trial_field === $field_id ) {

				$trial_amount          = $product_price * $quantity;
				$is_trial_or_setup_fee = true;

			} elseif ( ! empty( $setup_fee_field ) && $setup_fee_field === $field_id ) {

				$fee_amount            = $product_price * $quantity;
				$is_trial_or_setup_fee = true;
			}

			// Do not add to line items if the payment field selected in the feed is not the current field.
			if ( is_numeric( $payment_field ) && $payment_field != $field_id ) {
				continue;
			}

			// Do not add to line items if the payment field is set to "Form Total" and the current field was used for trial or setup fee.
			if ( $is_trial_or_setup_fee && ! is_numeric( $payment_field ) ) {
				continue;
			}

			$amount += $product_price * $quantity;

		}


		if ( ! empty( $products['shipping']['name'] ) && ! is_numeric( $payment_field ) ) {
			$line_items[] = array( 'id'          => '',
			                       'name'        => $products['shipping']['name'],
			                       'description' => '',
			                       'quantity'    => 1,
			                       'unit_price'  => GFCommon::to_number( $products['shipping']['price'] ),
			                       'is_shipping' => 1
			);
			$amount += $products['shipping']['price'];
		}

		return $amount > 0;
	}

	public function is_delayed_payment( $entry, $form, $is_delayed ) {
		if ( $this->get_slug() == 'gravityformspaypal' ) {
			return false;
		}

		$paypal_feed = $this->get_paypal_feed( $form['id'], $entry );
		if ( ! $paypal_feed ) {
			return false;
		}

		$has_payment = self::get_paypal_payment_amount( $form, $entry, $paypal_feed ) > 0;

		return rgar( $paypal_feed['meta'], "delay_{$this->get_slug()}" ) && $has_payment && ! $is_delayed;
	}

	public static function get_paypal_payment_amount( $form, $entry, $paypal_config ) {

		// TODO: need to support old "paypal_config" format as well as new format when delayed payment suported feed addons are released
		$products        = GFCommon::get_product_fields( $form, $entry, true );
		$recurring_field = rgar( $paypal_config['meta'], 'recurring_amount_field' );
		$total           = 0;
		foreach ( $products['products'] as $id => $product ) {

			if ( $paypal_config['meta']['type'] != 'subscription' || $recurring_field == $id || $recurring_field == 'all' ) {
				$price = GFCommon::to_number( $product['price'] );
				if ( is_array( rgar( $product, 'options' ) ) ) {
					foreach ( $product['options'] as $option ) {
						$price += GFCommon::to_number( $option['price'] );
					}
				}

				$total += $price * $product['quantity'];
			}
		}

		if ( 'all' === $recurring_field && ! empty( $products['shipping']['price'] ) ) {
			$total += floatval( $products['shipping']['price'] );
		}

		return $total;
	}



	public function has_frontend_feeds( $form ) {
		$result = $this->register_frontend_feeds( $form );
		return ! empty( $result );
	}

	/***
	 * Registers front end feeds with the private $_frontend_feeds array.
	 *
	 * @since 2.4
	 *
	 * @param array $form The current Form Object.
	 *
	 * @return bool Returns true if one ore more feeds were registered, false if no feeds were registered
	 */
	public function register_frontend_feeds( $form ) {

		// Don't register frontend feeds if $form ID is empty.
		if ( empty( $form['id'] ) ) {
			return false;
		}

		if ( ! isset( self::$_frontend_feeds[ $form['id'] ] ) ) {
			self::$_frontend_feeds[ $form['id'] ] = array();
		}

		$feeds = $this->get_frontend_feeds( $form );

		$this->add_frontend_feeds( $form['id'], $feeds );

		return ! empty( $feeds );
	}

	/***
	 * Loads front end feeds into the private $_frontend_feeds array, making sure not to add duplicate feeds.
	 *
	 * @since 2.4
	 *
	 * @param int   $form_id The current Form Id
	 * @param array $feeds   An array of all feeds to be loaded into the $_frontend_feeds variable
	 */
	public function add_frontend_feeds( $form_id, $feeds ) {

		foreach ( $feeds as $feed ) {
			$filter = array( 'feedId' => $feed['feedId'], 'addonSlug' => $feed['addonSlug'] );
			$found = wp_list_filter( self::$_frontend_feeds[ $form_id ], $filter );

			if ( empty( $found ) ) {
				self::$_frontend_feeds[ $form_id ][] = $feed;
			}
		}
	}

	/***
	 * Gets an array of all feeds eligible to be a Front End Feed.
	 *
	 * @since 2.4
	 *
	 * @param array $form The Form object to get Frontend Feeds from
	 *
	 * @return array An array with feeds eligible to be a Front End Feed. By default only feedId, addonSlug, conditionalLogic and isSingleFeed properties are returned in the array.
	 */
	public function get_frontend_feeds( $form ) {

		if ( ! $this->_supports_frontend_feeds ) {
			return array();
		}

		$feeds = $this->get_active_feeds( $form['id'] );
		if ( empty( $feeds ) ) {
			return array();
		}

		$frontend_feeds = array();

		foreach ( $feeds as $feed ) {

			$_feed = array(
				'feedId'           => $feed['id'],
				'addonSlug'        => $this->get_slug(),
				'conditionalLogic' => rgars( $feed, 'meta/feed_condition_conditional_logic' ) === '0' ? false : rgars( $feed, 'meta/feed_condition_conditional_logic_object/conditionalLogic', false ),
				'isSingleFeed'     => $this->_single_feed_submission,
			);

			$_feed = apply_filters( 'gform_addon_frontend_feed',                           $_feed, $form, $feed );
			$_feed = apply_filters( "gform_addon_frontend_feed_{$form['id']}",             $_feed, $form, $feed );
			$_feed = apply_filters( "gform_{$this->get_slug()}_frontend_feed",                  $_feed, $form, $feed );
			$_feed = apply_filters( "gform_{$this->get_slug()}_frontend_feed_{$form['id']}",    $_feed, $form, $feed );

			$frontend_feeds[] = $_feed;

		}

		return $frontend_feeds;
	}

	/***
	 * Registers frontend feeds by rendering the GFFrontEndFeeds() JS object.
	 *
	 * @since 2.4
	 *
	 * @param array $form The current Form object
	 */
	public static function register_frontend_feeds_init_script( $form ) {

		$feeds = rgar( self::$_frontend_feeds, $form['id'] );
		if ( empty( $feeds ) ) {
			return;
		}

		$args = array(
			'formId' => $form['id'],
			'feeds'  => $feeds,
		);

		$script = sprintf( '; new GFFrontendFeeds( %s );', json_encode( $args ) );

		GFFormDisplay::add_init_script( $form['id'], 'gaddon_frontend_feeds', GFFormDisplay::ON_PAGE_RENDER, $script );

	}

}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class GFAddOnFeedsTable extends WP_List_Table {

	private $_feeds;
	private $_slug;
	private $_columns;
	private $_bulk_actions;
	private $_action_links;

	/**
	 * @var GFFeedAddOn
	 */
	private $_addon_class;

	private $_column_value_callback = array();
	private $_no_items_callback = array();
	private $_message_callback = array();

	function __construct( $feeds, $slug, $columns, $bulk_actions, $action_links, $column_value_callback, $no_items_callback, $message_callback, $addon_class ) {
		$columns = ( is_array( $columns ) ) ? $columns : array();

		$this->_bulk_actions          = $bulk_actions;
		$this->_feeds                 = $feeds;
		$this->_slug                  = $slug;
		$this->_columns               = $columns;
		$this->_column_value_callback = $column_value_callback;
		$this->_action_links          = $action_links;
		$this->_no_items_callback     = $no_items_callback;
		$this->_message_callback      = $message_callback;
		$this->_addon_class           = $addon_class;

		$standard_cols = array(
			'cb'        => esc_html__( 'Checkbox', 'gravityforms' ),
			'is_active' => '',
		);

		$all_cols = array_merge( $standard_cols, $columns );

		$this->_column_headers = array(
			$all_cols,
			array(),
			array(),
			rgar( array_keys( $all_cols ), 2 ),
		);

		parent::__construct(
			array(
				'singular' => esc_html__( 'feed', 'gravityforms' ),
				'plural'   => esc_html__( 'feeds', 'gravityforms' ),
				'ajax'     => false,
			)
		);
	}

	function prepare_items() {
		$this->items = isset( $this->_feeds ) ? $this->_feeds : array();
	}

	function get_columns() {
		return $this->_column_headers[0];
	}

	function get_bulk_actions() {
		return $this->_bulk_actions;
	}

	function no_items() {
		echo call_user_func( $this->_no_items_callback );
	}

	function display_rows_or_placeholder() {
		$message = call_user_func( $this->_message_callback );

		if ( $message !== false ) {
			?>
			<tr class="no-items">
				<td class="colspanchange" colspan="<?php echo $this->get_column_count() ?>">
					<?php echo $message ?>
				</td>
			</tr>
			<?php
		} else {
			parent::display_rows_or_placeholder();
		}

	}

	function column_default( $item, $column ) {

		if ( is_callable( $this->_column_value_callback ) ) {
			$value = call_user_func( $this->_column_value_callback, $item, $column );
		}

		// Adding action links to the first column of the list
		$columns = array_keys( $this->_columns );
		if ( is_array( $columns ) && count( $columns ) > 0 && $columns[0] == $column ) {
			$value = $this->add_action_links( $item, $column, $value );
		}

		return $value;
	}

	function column_cb( $item ) {
		$feed_id = rgar( $item, 'id' );

		return sprintf(
			'<input type="checkbox" name="feed_ids[]" value="%s" />', esc_attr( $feed_id )
		);
	}

	function add_action_links( $item, $column, $value ) {

		/**
		 * Adds action links to feed items
		 *
		 * @param array  $this->_action_links Action links to be filtered.
		 * @param array  $item                The feed item being filtered.
		 * @param string $column              The column ID
		 */
		$actions = apply_filters( $this->_slug . '_feed_actions', $this->_action_links, $item, $column );

		// Replacing _id_ merge variable with actual feed id
		foreach ( $actions as $action => &$link ) {
			$link = str_replace( '_id_', $item['id'], $link );
		}

		if ( ! $this->_addon_class->can_duplicate_feed( $item['id'] ) ) {
			unset( $actions['duplicate'] );
		}

		return sprintf( '%1$s %2$s', $value, $this->row_actions( $actions ) );
	}

	function _column_is_active( $item, $classes, $data, $primary ) {

		// Open cell as a table header.
		echo '<td class="manage-column column-is_active">';

		// Display the active/inactive toggle button.
		if ( rgar( $item, 'is_active' ) ) {
			$class = 'gform-status--active';
			$text  = esc_html__( 'Active', 'gravityforms' );
		} else {
			$class = 'gform-status--inactive';
			$text  = esc_html__( 'Inactive', 'gravityforms' );
		}
		?>
		<button
			type="button"
			class="gform-status-indicator gform-status-indicator--size-sm gform-status-indicator--theme-cosmos <?php echo esc_attr( $class ); ?>"
			onclick="gaddon.toggleFeedActive( this, '<?php echo esc_js( $this->_slug ); ?>', '<?php echo esc_js( $item['id'] ); ?>' );"
			onkeypress="gaddon.toggleFeedActive( this, '<?php echo esc_js( $this->_slug ); ?>', '<?php echo esc_js( $item['id'] ); ?>' );"
		>
			<span class="gform-status-indicator-status gform-typography--weight-medium gform-typography--size-text-xs">
				<?php echo esc_html( $text ); ?>
			</span>
		</button>
		<?php

		// Close cell.
		echo '</td>';

	}
	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 2.5
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {

		if ( ! $this->is_new_button_supported( $which ) ) {
			return;
		}

		printf(
			'<div class="alignright"><a href="%s" class="button">%s</a></div>',
			esc_url( add_query_arg( array( 'fid' => 0 ) ) ),
			esc_html__( 'Add New', 'gravityforms' )
		);

	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @since 2.5
	 *
	 * @param string $which The location.
	 */
	protected function display_tablenav( $which ) {
		if ( ! $this->has_items() && ! $this->is_new_button_supported( $which ) ) {
			return;
		}

		parent::display_tablenav( $which );
	}

	/**
	 * Determines if the add new button is supported in the current location.
	 *
	 * @since 2.5
	 *
	 * @param string $which The location.
	 *
	 * @return bool
	 */
	protected function is_new_button_supported( $which ) {
		return $which === 'top' && $this->_addon_class->can_create_feed();
	}

}
