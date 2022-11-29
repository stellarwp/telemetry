<?php
/**
 * Handles hooking into the WordPress request lifecycle.
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Class Telemetry_Subscriber
 *
 * @since 1.0.0
 */
class Telemetry_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
    public function register() {
		add_action( 'shutdown', [ $this, 'send_telemetry_data' ] );
	}

	/**
	 * Handles sending telemetry data during the 'shutdown' action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_telemetry_data() {
		if ( ! $this->container->get( Last_Send::class )->is_expired() ) {
			return;
		}
	}

}
