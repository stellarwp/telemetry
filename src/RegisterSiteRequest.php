<?php

namespace StellarWP\Telemetry;

class RegisterSiteRequest implements Request {

	public function get_url(): string {
		// TODO: Implement get_telemetry_server_url() on the PluginStarter, and call it here using DI.
		return 'https://telemetry.moderntribe.qa/register-site';
	}

	public function get_args(): array {
		return [
			'email' => 'dummy@email.com',
			'telemetry' => [],
		];
	}

	public function run() {
		return wp_remote_post( $this->get_url(), [
			'body'    => $this->get_args(),
			'headers' => [
				'Content-Type' => 'application/json',
			],
		] );
	}
}