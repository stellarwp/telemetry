<?php
/**
 * Sets up a basic /send REST route that sends data to the telemetry server.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry\Routes;

use DateTimeImmutable;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Telemetry;
use WP_REST_Request;

/**
 * Sets up a basic /send REST route that sends data to the telemetry server.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Send extends Abstract_Route {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
    public function get_endpoint() {
		return '/send';
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
		$timestamp   = new DateTimeImmutable( $request->get_param( 'timestamp' ) );
		$now         = new DateTimeImmutable();
		$interval    = date_diff( $timestamp, $now );

		// Bail if timestamp from request is more than 30 seconds old.
		if ( $interval->s > 30 ) {
			return;
		}

		if ( ! wp_verify_nonce( $request->get_param( 'nonce' ), Telemetry::NONCE ) ) {
			$this->send_early_unauthorized();
		}

		Config::get_container()->get( Telemetry::class )->send_data();

		$this->send_early_ok();
	}
}
