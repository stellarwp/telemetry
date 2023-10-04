<?php
/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Events;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */
class Event_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since 2.1.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'shutdown', [ $this, 'send_cached_events' ] );
		add_action( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'event', [ $this, 'cache_event' ], 10, 2 );
		add_action( 'wp_ajax_' . Event::AJAX_ACTION, [ $this, 'send_events' ], 10, 1 );
		add_action( 'wp_ajax_nopriv_' . Event::AJAX_ACTION, [ $this, 'send_events' ], 10, 1 );
	}

	/**
	 * Caches an event to be sent during shutdown.
	 *
	 * @since TBD
	 *
	 * @param string $name         The name of the event.
	 * @param array  $data         The data sent along with the event.
	 *
	 * @return void
	 */
	public function cache_event( $name, $data ) {
		$events = [];

		if ( $this->container->has( 'events' ) ) {
			$events = $this->container->get( 'events' );
		}

		$events[] = [
			'name'         => $name,
			'data'         => wp_json_encode( $data ),
			'stellar_slug' => Config::get_stellar_slug(),
		];

		$this->container->bind( 'events', $events );
	}

	/**
	 * Sends the events that have been stored for the current request.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function send_cached_events() {
		if ( ! $this->container->has( 'events' ) ) {
			return;
		}

		$url = admin_url( 'admin-ajax.php' );

		wp_remote_post(
			$url,
			[
				'blocking'  => false,
				'sslverify' => false,
				'body'      => [
					'action' => Event::AJAX_ACTION,
					'events' => $this->container->get( 'events' ),
				],
			]
		);

		$this->container->bind( 'events', [] );
	}

	/**
	 * Send the event to the telemetry server.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function send_events() {
		// Get the passed event array.
		$events = filter_input( INPUT_POST, 'events', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ); // phpcs:ignore WordPressVIPMinimum.Security.PHPFilterFunctions.RestrictedFilter

		$this->container->get( Event::class )->send_batch( $events );
	}
}
