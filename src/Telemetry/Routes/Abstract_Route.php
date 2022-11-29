<?php

namespace StellarWP\Telemetry\Routes;

abstract class Abstract_Route {

	public abstract function get_endpoint();

	protected abstract function action();

	public function register_route() {
		register_rest_route( $this->get_namespace(), $this->get_endpoint(), $this->get_args() );
	}

	public function get_namespace() {
		return 'stellarwp/telemetry/v1';
	}

	public function get_args() {
		return [
			'methods' => 'GET',
			'callback' => [ $this, 'action' ],
		];
	}

	/**
	 * Send an early unauthorized response to the request.
	 *
	 * @since 1.0.0
	 */
	public function send_early_unauthorized() {
		$this->send_early_response( '401 Unauthorized' );
	}

	/**
	 * Send an early OK response to the request.
	 *
	 * @since 1.0.0
	 */
	public function send_early_ok() {
		$this->send_early_response( '200 OK' );
	}

	/**
	 * Sends an early HTTP response to the requester before completing the full action triggered by the request.
	 *
	 * @param string $response_code The response to send to the request.
	 *
	 * @link https://keepcoding.ehsanabbasi.com/php/processing-php-after-sending-200-ok/
	 *
	 * @since 1.0.0
	 */
	protected function send_early_response( $response_code = '200 OK' ) {
		if ( defined( 'ENVIRONMENT' ) && ENVIRONMENT === 'local' ) {
			return;
		}

		// If using FastCGI, do something different.
		if ( function_exists( 'fastcgi_finish_request' ) ) {
			session_write_close();
			fastcgi_finish_request();

			return;
		}

		ignore_user_abort( true );
		ob_start();

		$server_protocol = filter_input( INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING );
		header( $server_protocol . $response_code );

		// Disable compression (in case the content length is compressed).
		header( 'Content-Encoding: none' );
		header( 'Content-Length: ' . ob_get_length() );

		// Close the connection.
		header( 'Connection: close' );

		ob_end_flush();
		ob_flush();
		flush();
	}

}
