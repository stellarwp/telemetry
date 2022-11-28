<?php

namespace StellarWP\Telemetry\Routes;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Opt_In_Status;

class Route_Subscriber extends Abstract_Subscriber {

    public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		if ( ! $this->container->get( Opt_In_Status::class )->is_active() ) {
			return;
		}

		register_rest_route( 'stellarwp/telemetry/v1', $this->container->get( Core::PLUGIN_SLUG ) . '/check', [
			'methods' => 'GET',
			'callback' => [ $this, 'check' ],
		] );
	}

	public function check() {
		return $this->container->get( Check::class )->response();
	}
}
