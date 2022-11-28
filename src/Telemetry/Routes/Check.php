<?php

namespace StellarWP\Telemetry\Routes;

use WP_REST_Response;

class Check extends Abstract_Route {

    protected function get_endpoint() {
		return '/check';
	}

    public function action() {
		$response = new WP_REST_Response( [ 'results' => 'success' ] );
		$response->set_status( 200 );

		return $response;
	}

}
