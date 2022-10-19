<?php

namespace StellarWP\Telemetry;

class TelemetrySendDataRequest implements Request {

	/**
	 * @var TelemetryProvider
	 */
	private $provider;
	/**
	 * @var PluginStarter
	 */
	private $starter;

	public function __construct( PluginStarter $starter, TelemetryProvider $provider ) {
		$this->provider = $provider;
		$this->starter  = $starter;
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp_telemetry_send_data_url', $this->starter->get_telemetry_url() . '/telemetry' );
	}

	public function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_send_data_args', [
			'token'     => $this->starter->get_token(),
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	public function run(): bool {
		$data = $this->get_args();
		$url  = $this->get_url();

		$response = $this->request( $url, $data );
		$data     = $this->parse_response( $response );

		return $data['status'] ?? false;
	}

	private function request( $url, $data ) {
		return wp_remote_post( $url, [
			'body' => $data,
		] );
	}

	private function parse_response( $response ): ?array {
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		// If status is false, return null
		if ( ! $data['status'] ) {
			return null;
		}

		return $data;
	}
}