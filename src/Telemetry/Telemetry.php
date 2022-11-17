<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Data_Provider;

class Telemetry {
	public const SERVER_URL = 'https://telemetry-api.moderntribe.qa/api/v1';

	/** @var Data_Provider */
	protected $provider;

	/** @var string */
	protected $option_name;

	public function __construct( Data_Provider $provider, string $option_name ) {
		$this->provider    = $provider;
		$this->option_name = $option_name;
	}

	public function register_site( bool $force = false ): bool {
		// If site is already registered and we're not forcing a new registration, bail.
		if ( $this->is_registered() ) {
			if ( $force === false ) {
				return false;
			}
		}

		$response = $this->send( $this->get_register_site_data(), $this->get_register_site_url() );

		return $this->save_token( $response['token'] ?? '' );
	}

	public function send_uninstall( string $plugin_slug, string $uninstall_reason_id, string $uninstall_reason, string $comment = '' ) {
		$response = $this->send(
			[
				'access_token'        => $this->get_token(),
				'plugin_slug'         => $plugin_slug,
				'uninstall_reason_id' => $uninstall_reason_id,
				'uninstall_reason'    => $uninstall_reason,
				'comment'             => $comment,
			],
			$this->get_uninstall_url()
		);
	}

	protected function send( array $data, string $url ): ?array {
		$response = $this->request( $url, $data );
		$response = $this->parse_response( $response );

		if ( empty( $response['status'] ) ) {
			return null;
		}

		return $response;
	}

	protected function request( string $url, array $data ) {
		return wp_remote_post( $url, [
			'body' => $data,
		] );
	}

	protected function parse_response( array $response ): ?array {
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		// If status is false, return null
		if ( false === $data['status'] ?? false ) {
			return null;
		}

		return $data;
	}

	protected function get_register_site_url(): string {
		return apply_filters( 'stellarwp/telemetry/register_site_url', self::SERVER_URL . '/register-site' );
	}

	protected function get_uninstall_url(): string {
		return apply_filters( 'stellarwp/telemetry/uninstall_url', self::SERVER_URL . '/uninstall' );
	}

	protected function get_register_site_data(): array {
		return apply_filters( 'stellarwp/telemetry/register_site_data', [
			'user'      => json_encode( $this->get_user_details() ),
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	protected function get_user_details(): array {
		$user = wp_get_current_user();

		return apply_filters( 'stellarwp/telemetry/register_site_user_details', [
			'name'  => $user->display_name,
			'email' => $user->user_email,
		] );
	}

	protected function get_option(): array {
		return get_option( $this->option_name, [] );
	}

	public function save_token( string $token ): bool {
		$option = array_merge( $this->get_option(), [
			'token' => $token,
		] );

		return update_option( $this->option_name, $option );
	}

	public function is_registered(): bool {
		// Check if the site is registered by checking if the token is set.
		$option = $this->get_option();

		return ! empty( $option['token'] );
	}

	public function send_data(): bool {
		if ( ! $this->is_registered() ) {
			return false;
		}

		$response = $this->send( $this->get_send_data_args(), $this->get_send_data_url() );

		return $response['status'] ?? false;
	}

	protected function get_send_data_args(): array {
		return apply_filters( 'stellarwp/telemetry/send_data_args', [
			'token'     => $this->get_token(),
			'telemetry' => json_encode( $this->provider->get_data() ),
		] );
	}

	protected function get_send_data_url(): string {
		return apply_filters( 'stellarwp/telemetry/send_data_url', self::SERVER_URL . '/telemetry' );
	}

	protected function get_token(): string {
		$option = $this->get_option();

		return apply_filters( 'stellarwp/telemetry/token', $option['token'] ?? '' );
	}

}
