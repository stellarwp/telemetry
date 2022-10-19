<?php

namespace StellarWP\Telemetry;

class RegisterSiteRequest implements Request {

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
		$this->starter = $starter;
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp_telemetry_register_site_url' , $this->starter->get_telemetry_url() . '/register-site' );
	}

	public function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_register_site_data' , [
			'email'     => 'dummy@email.com',
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	public function run() {
		$data = $this->get_args();
		$url = $this->get_url();

		$response = wp_remote_post( $url, [
			'body' => $data,
		] );

		return $response;
	}
}