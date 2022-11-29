<?php

namespace StellarWP\Telemetry\Routes;

use WP_REST_Request;
use WP_REST_Response;

class Check extends Abstract_Route {

    public function get_endpoint() {
		return '/check';
	}

    public function action( WP_REST_Request $request ) {
		$response = new WP_REST_Response( [ 'results' => 'success' ] );
		$response->set_status( 200 );

		return $response;
	}

}
