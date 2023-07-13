<?php
/**
 * Contains all event related functionality.
 *
 * @since TBD
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Events;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Telemetry\Telemetry;

/**
 * The class that handles user triggered events.
 *
 * @since TBD
 *
 * @package StellarWP\Telemetry
 */
class Event {

	/**
	 * An instance of the Telemetry class.
	 *
	 * @since TBD
	 *
	 * @var \StellarWP\Telemetry\Telemetry\Telemetry
	 */
	private $telemetry;

	/**
	 * The class constructor.
	 *
	 * @since TBD
	 *
	 * @param Telemetry $telemetry An instance of the Telemetry class.
	 */
	public function __construct( Telemetry $telemetry ) {
		$this->telemetry = $telemetry;
	}

	/**
	 * Sends an event to the telemetry server.
	 *
	 * @since TBD
	 *
	 * @param string $name The name of the event.
	 * @param array  $data Additional information to include with the event.
	 *
	 * @return bool
	 */
	public function send( string $name, array $data = [] ) {
		$data = [
			'token'        => $this->telemetry->get_token(),
			'stellar_slug' => Config::get_stellar_slug(),
			'event'        => $name,
			'event_data'   => wp_json_encode( $data ),
		];

		/**
		 * Provides the ability to filter event data before it is sent to the telemetry server.
		 *
		 * @since TBD
		 *
		 * @param array $data The data about to be sent.
		 */
		$data = apply_filters( 'stellarwp/telemetry/events_data', $data );

		$response = $this->telemetry->send( $data, $this->get_url() );

		if ( ! isset( $response['status'] ) ) {
			return false;
		}

		return boolval( $response['status'] );
	}

	/**
	 * Gets the url used for sending events.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	protected function get_url() {
		$events_url = Config::get_server_url() . '/events';

		/**
		 * Filters the url used to send events to the telemetry server.
		 *
		 * @since TBD
		 *
		 * @param string $event_url The events endpoint url.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'events_url', $events_url );
	}

}
