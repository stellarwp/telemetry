<?php

namespace StellarWP\Telemetry\Routes;

use WP_REST_Response;

class Send extends Abstract_Route {

    protected function get_endpoint() {
		return '/send';
	}

    public function action() {
		$response = new WP_REST_Response( [ 'results' => 'success' ] );
		$response->set_status( 200 );

		return $response;
	}

}
