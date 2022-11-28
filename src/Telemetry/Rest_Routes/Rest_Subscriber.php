<?php

namespace StellarWP\Telemetry\Rest_Routes;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Opt_In_Status;
use WP_REST_Request;
use WP_REST_Response;

class Rest_Subscriber extends Abstract_Subscriber {

    public function register() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		if ( ! $this->container->get( Opt_In_Status::class )->is_active() ) {
			return;
		}

		register_rest_route( 'stellarwp/telemetry/v1', $this->container->get( Core::PLUGIN_SLUG ) . '/send', [
			'methods' => 'GET',
			'callback' => [ $this, 'send_data' ],
		] );
	}

	public function send_data( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		return $response;
	}

}
