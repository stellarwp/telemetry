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
		add_action( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'event', [ $this, 'send_event' ], 10, 2 );
	}

	/**
	 * Sends an event request to the Telemetry server.
	 *
	 * @since 2.1.0
	 *
	 * @param string $name The name of the event to send.
	 * @param array  $data Additional information to send with the event.
	 */
	public function send_event( string $name, array $data = [] ) {
		$this->container->get( Event::class )->send( $name, $data );
	}

}
