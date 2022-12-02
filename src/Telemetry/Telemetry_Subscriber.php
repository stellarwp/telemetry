<?php
/**
 * Handles hooking into the WordPress request lifecycle.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use DateTimeImmutable;
use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Class Telemetry_Subscriber
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Telemetry_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'shutdown', [ $this, 'send_async_request' ] );
		add_action( 'wp_ajax_' . Telemetry::AJAX_ACTION, [ $this, 'send_telemetry_data' ], 10, 1 );
		add_action( 'wp_ajax_nopriv_' . Telemetry::AJAX_ACTION, [ $this, 'send_telemetry_data' ], 10, 1 );
	}

	/**
	 * Sends an async request during the 'shutdown' action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_async_request() {

		if ( ! $this->container->get( Opt_In_Status::class )->is_active() ) {
			return;
		}

		$last_send = $this->container->get( Last_Send::class );

		// Bail if last send timestamp is not expired.
		if ( ! $last_send->is_expired() ) {
			return;
		}

		// The last send is expired, set a new timestamp.
		$timestamp = new DateTimeImmutable();
		$rows_affected = $last_send->set_new_timestamp( $timestamp );

		// We weren't able to update the timestamp, another process may have updated it first.
		if ( $rows_affected === 0 ) {
			return;
		}

		$url   = admin_url( 'admin-ajax.php' );

		wp_remote_post( $url, [
			'blocking' => false,
			'timeout'  => 1,
			'body'     => [
				'action' => Telemetry::AJAX_ACTION,
			],
		] );
	}

	/**
	 * Sends telemetry data to the server.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_telemetry_data() {
		$this->container->get( Telemetry::class )->send_data();
		exit();
	}
}