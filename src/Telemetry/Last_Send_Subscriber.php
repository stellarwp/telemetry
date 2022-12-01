<?php
/**
 * Handles all methods related to the Last Send option in the database.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * The subscriber for the Last_Send option.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Last_Send_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'initialize_last_send_option' ] );
	}

	/**
	 * Initializes the option in the options table to track the last time data was sent to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function initialize_last_send_option() {
		return $this->container->get( Last_Send::class )->initialize_option();
	}
}