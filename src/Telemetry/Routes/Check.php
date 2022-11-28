<?php

namespace StellarWP\Telemetry\Routes;

use WP_REST_Response;

class Check {

    public function response() {
		$response = new WP_REST_Response( [ 'results' => 'success' ] );
		$response->set_status( 200 );

		return $response;
	}

}
