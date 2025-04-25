<?php
/**
 * The API implemented by all subscribers.
 *
 * @package StellarWP\Telemetry\Contracts
 */

namespace StellarWP\Telemetry\Contracts;

/**
 * Interface Subscriber_Interface
 *
 * @package StellarWP\Telemetry\Contracts
 */
interface Subscriber_Interface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register();
}
