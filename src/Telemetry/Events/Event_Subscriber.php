<?php
/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since TBD
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Events;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since TBD
 *
 * @package StellarWP\Telemetry
 */
class Event_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'stellarwp/telemetry/event', [ $this, 'send_event' ], 10, 2 );
	}

	/**
	 * Sends an event request to the Telemetry server.
	 *
	 * @since TBD
	 *
	 * @param string $name The name of the event to send.
	 * @param array  $data Additional information to send with the event.
	 */
	public function send_event( string $name, array $data = [] ) {
		$this->container->get( Event::class )->send( $name, $data );
	}

}
