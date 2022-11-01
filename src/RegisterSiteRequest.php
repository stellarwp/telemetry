<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Request;
use StellarWP\Telemetry\Contracts\Runnable;

class RegisterSiteRequest implements Request, Runnable {
	/** @var DataProvider */
	private $provider;

	/** @var OptInStatus */
	private $optin_status;

	/** @var array */
	public $response;

	public function __construct( DataProvider $provider, OptInStatus $optin_status ) {
		$this->provider     = $provider;
		$this->optin_status = $optin_status;
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp_telemetry_register_site_url', self::TELEMETRY_URL . '/register-site' );
	}

	public function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_register_site_data', [
			'user'      => json_encode( $this->get_user_details() ),
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

	protected function get_user_details(): array {
		$user = wp_get_current_user();

		return apply_filters( 'stellarwp_telemetry_register_site_user_details', [
			'name'  => $user->display_name,
			'email' => $user->user_email,
		] );
	}
}