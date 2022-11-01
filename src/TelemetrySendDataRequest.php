<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Request;
use StellarWP\Telemetry\Contracts\Runnable;

class TelemetrySendDataRequest implements Request, Runnable {

	/** @var DataProvider */
	protected $provider;
	/** @var array */
	public $response;
	/** @var OptInStatus */
	protected $optin_status;

	public function __construct( DataProvider $provider, OptInStatus $optin_status ) {
		$this->provider = $provider;
		$this->optin_status = $optin_status;
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp_telemetry_send_data_url', self::TELEMETRY_URL . '/telemetry' );
	}

	public function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_send_data_args', [
			'token'     => $this->optin_status->get_token(),
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	public function run(): void {
		$data = $this->get_args();
		$url  = $this->get_url();

		$this->response = $this->parse_response( $this->request( $url, $data ) );
	}

	protected function request( $url, $data ) {
		return wp_remote_post( $url, [
			'body' => $data,
		] );
	}

	protected function parse_response( $response ): ?array {
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		// If status is false, return null
		if ( ! $data['status'] ) {
			return null;
		}

		return $data;
	}
}