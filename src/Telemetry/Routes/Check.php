<?php
/**
 * Sets up a basic /check REST route.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry\Routes;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Sets up a basic /check REST route.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Check extends Abstract_Route {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_endpoint() {
		return '/check';
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function action( WP_REST_Request $request ) {
		$response = new WP_REST_Response( [ 'results' => 'success' ] );
		$response->set_status( 200 );

		return $response;
	}

}
