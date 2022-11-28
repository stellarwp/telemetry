<?php

namespace StellarWP\Telemetry\Routes;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use StellarWP\Telemetry\Opt_In_Status;

class Route_Subscriber extends Abstract_Subscriber {

    public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		if ( ! $this->container->get( Opt_In_Status::class )->is_active() ) {
			return;
		}

		$this->container->get( Check::class )->register_route();
		$this->container->get( Send::class )->register_route();
	}

}
