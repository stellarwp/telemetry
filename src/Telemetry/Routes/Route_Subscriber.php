<?php
/**
 * Hooks into the WordPress request lifecycle.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry\Routes;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use StellarWP\Telemetry\Opt_In_Status;

/**
 * Hooks into the WordPress request lifecycle.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Route_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers the REST routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		if ( ! $this->container->get( Opt_In_Status::class )->is_active() ) {
			return;
		}

		$this->container->get( Check::class )->register_route();
		$this->container->get( Send::class )->register_route();
	}

}
