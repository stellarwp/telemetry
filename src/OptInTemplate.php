<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Template;

class OptInTemplate implements Template {
	protected const YES = "1";
	protected const NO = "-1";

	public function enqueue(): void {
		// TODO: Once FE template is done, enqueue it here.
	}

	protected function get_args(): array {
		return apply_filters( 'stellarwp_telemetry_optin_args', [
			'plugin_logo'        => plugin_dir_url( __DIR__ ) . 'public/logo.png',
			'plugin_logo_width'  => 151,
			'plugin_logo_height' => 32,
			'plugin_logo_alt'    => 'StellarWP Logo',
			'plugin_name'        => 'The Events Calendar',
			'user_name'          => wp_get_current_user()->display_name,
			'permissions_url'    => '#',
			'tos_url'            => '#',
			'privacy_url'        => '#',
		] );
	}

	public function render(): void {
		load_template( __DIR__ . '/views/optin.php', true, $this->get_args() );
	}

	protected function get_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_show_optin_option_name', 'stellarwp_telemetry_show_optin' );
	}

	public function should_render(): bool {
		if ( get_option( $this->get_option_name(), false ) === self::YES ) {
			return true;
		}

		return false;
	}

	public function maybe_render(): void {
		if ( $this->should_render() ) {
			$this->render();
		}
	}
}