<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Request;
use StellarWP\Telemetry\Contracts\Runnable;

class RegisterSiteRequest implements Request, Runnable {

	/**
	 * @var DataProvider
	 */
	private $provider;
	/**
	 * @var Starter
	 */
	private $starter;
	/**
	 * @var OptInStatus
	 */
	private $optin_status;

	/**
	 * @var array
	 */
	public $response;

	public function __construct( Starter $starter, DataProvider $provider, OptInStatus $optin_status ) {
		$this->provider = $provider;
		$this->starter  = $starter;
		$this->optin_status = $optin_status;
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp_telemetry_register_site_url', $this->starter->get_telemetry_url() . '/register-site' );
	}

	public function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_register_site_data', [
			'email'     => 'dummy@email.com',
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	public function run(): void {
		$data = $this->get_args();
		$url  = $this->get_url();

		$response       = $this->request( $url, $data );
		$this->response = $this->parse_response( $response );

		if ( empty( $this->response['token'] ) ) {
			return;
		}

		$this->save_token( $this->response['token'] );
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

	private function save_token( string $token ): bool {
		$option = array_merge( $this->optin_status->get_option(), [
			'token' => $token,
		] );

		return update_option( $this->optin_status->get_option_name(), $option );
	}
}