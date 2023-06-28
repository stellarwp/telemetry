<?php
/**
 * Contains all event related functionality.
 *
 * @since TBD
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Events;

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
	private Telemetry $telemetry;

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
	 * Sends an event request to the Telemetry server.
	 *
	 * @since TBD
	 *
	 * @param string $name The name of the event to send.
	 * @param array  $data Additional information to send with the event.
	 *
	 * @return void
	 */
	public function send( string $name, array $data = [] ) {
		$this->telemetry->send_event( $name, $data );
	}

}
