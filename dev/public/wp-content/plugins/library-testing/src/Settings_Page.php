<?php declare(strict_types=1);

namespace StellarWP\TelemetryLibraryTesting;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core as Telemetry;
use StellarWP\Telemetry\Opt_In\Opt_In_Template;
use StellarWP\Telemetry\Opt_In\Status;

class Settings_Page {

	public function __construct() {
		add_filter( 'stellarwp/telemetry/telemetry-library/last_send_expire_seconds', [ $this, 'reduce_last_send_expire_time' ], 10, 1 );
		add_action( 'admin_menu', [ $this, 'register_options_page' ] );
		add_action( 'admin_init', [ $this, 'save_opt_in_setting_field' ] );
		add_action( 'admin_init', [ $this, 'save_show_modal_setting_field' ] );
		add_action( 'admin_init', [ $this, 'send_event' ] );
		add_action( 'admin_init', [ $this, 'clear_all_database_options' ] );
	}

	public function register_options_page(): void {
		add_options_page( 'Telemetry Library', 'Telemetry', 'manage_options', 'tsplugin', [ $this, 'render_options_page' ] );
	}

	public function render_options_page(): void {
		$plugin_file = Telemetry::instance()->container()->get( Telemetry::PLUGIN_FILE );

		include dirname( $plugin_file ) . '/views/settings.php';
	}

	/**
	 * Saves the "Opt In Status" setting.
	 */
	public function save_opt_in_setting_field(): void {
		$container = Telemetry::instance()->container();

		// Bail early if we're not saving the Opt In Status field.
		if ( ! isset( $_POST['opt-in-status'] ) ) {
			return;
		}

		// Get the saved value as a boolean. Mixed value will be filtered as null.
		$value = filter_input( INPUT_POST, 'opt-in-status', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

		// Bail early if the Opt In Status is mixed.
		if ( is_null( $value ) ) {
			return;
		}

		$container->get( Status::class )->set_status( $value );
	}

	/**
	 * Saves the "Show Opt In Modal" setting.
	 */
	public function save_show_modal_setting_field(): void {
		$container     = Telemetry::instance()->container();
		$stellar_slugs = $_POST['stellar_slugs'] ?? [];

		foreach ( $stellar_slugs as $slug ) {
			$option_name = $container->get( Opt_In_Template::class )->get_option_name( $slug );
			$value       = filter_input( INPUT_POST, 'show_' . $slug . '_modal' ) ?: 0;
			update_option( $option_name, absint( $value ) );
		}
	}

	/**
	 * Reduces the amount of time the plugin waits to send data to the telemetry server.
	 */
	public function reduce_last_send_expire_time(): int {
		return 5 * MINUTE_IN_SECONDS;
	}

	/**
	 * Sends event with the event form.
	 */
	public function send_event(): void {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'telemetry-library-send-event' ) ) {
			return;
		}

		$number = filter_input( INPUT_POST, 'number', FILTER_VALIDATE_INT ) ?: 1;

		// Set up basic event data for each valid event.
		$event_data = [
			'site_url'     => get_option( 'siteurl' ),
			'stellar_slug' => Config::get_stellar_slug(),
		];

		// Get the event key.
		$event_key = filter_input( INPUT_POST, 'event' );

		add_filter( 'stellarwp/telemetry/events_data', static function ( $data ) use ( $event_key ) {

			// If the invalid-event option is used, provide an invalid stellar_slug.
			if ( $event_key === 'invalid-event' ) {
				$data['stellar_slug'] = 'stellar-slug-does-not-exist';
			}

			return $data;
		}, 10, 1 );

		// Send the event.
		for ( $i = 0; $i < $number; $i++ ) {
			do_action( 'stellarwp/telemetry/telemetry-library/event', $event_key, $event_data );
		}
	}

	/**
	 * Clears all telemetry-related options in the database.
	 */
	public function clear_all_database_options() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'telemetry-library-clear-database-options' ) ) {
			return;
		}

		global $wpdb;

		$query = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}options WHERE `option_name` LIKE 'stellarwp_telemetry%%';" );
		$wpdb->query( $query );
	}

}
