<?php
/**
 * Handles hooking into the WordPress request lifecycle.
 */
namespace StellarWP\Telemetry;

use DateTimeImmutable;
use StellarWP\Telemetry\Contracts\Abstract_Subscriber;
use StellarWP\Telemetry\Routes\Send;

/**
 * Class Telemetry_Subscriber
 *
 * @since 1.0.0
 */
class Telemetry_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'shutdown', [ $this, 'send_telemetry_data' ] );
	}

	/**
	 * Handles sending telemetry data during the 'shutdown' action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_telemetry_data() {
		$last_send = $this->container->get( Last_Send::class );

		// Bail if last send timestamp is not expired.
		if ( ! $last_send->is_expired() ) {
			return;
		}

		// The last send is expired, set a new timestamp.
		$timestamp = new DateTimeImmutable();
		$rows_affected = $last_send->set_new_timestamp( $timestamp );

		// We weren't able to update the timestamp, likely another process updated it first.
		if ( $rows_affected === 0 ) {
			return;
		}

		$nonce           = wp_create_nonce( Telemetry::NONCE );
		$route_namespace = $this->container->get( Send::class )->get_namespace();

		try {
			wp_remote_get( get_rest_url( null, $route_namespace . '/send?nonce=' . $nonce . '&timestamp=' . $timestamp ), [
				'blocking' => false,
				'timeout' => 1
			] );
		} catch ( \Error $e ) {
			return;
		}
	}
}
