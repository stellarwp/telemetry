<?php

namespace StellarWP\Telemetry\Routes;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Telemetry;
use WP_REST_Request;

class Send extends Abstract_Route {

    public function get_endpoint() {
		return '/send';
	}

    public function action() {
		$request = new WP_REST_Request();

		if ( ! wp_verify_nonce( $request->get_params( 'nonce' ), Telemetry::NONCE ) ) {
			$this->send_early_unauthorized();
		}

		Config::get_container()->get( Telemetry::class )->send_data();

		$this->send_early_ok();
	}
}
