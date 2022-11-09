<?php

namespace StellarWP\Telemetry;

class Core {
	public const PLUGIN_SLUG = 'stellarwp-telemetry-starter';
	public const PLUGIN_VERSION = '1.0.0';
	public const YES = "1";
	public const NO = "-1";

	private static self $instance;

	/**
	 * @return self
	 */
	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init(): void {
		// We don't want to run this on every ajax request.
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( $this->is_settings_page() ) {
			if ( $this->should_show_optin() ) {
				// Run optin.
				$this->run_optin();
			}
		}
	}

	public function get_plugin_version(): string {
		return apply_filters( 'stellarwp/telemetry/version', self::PLUGIN_VERSION );
	}

	public function is_settings_page(): bool {
		$is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] === $this->get_plugin_slug() );

		return apply_filters( 'stellarwp/telemetry/is_settings_page', $is_settings_page );
	}

	public function get_plugin_slug(): string {
		return apply_filters( 'stellarwp/telemetry/plugin_slug', self::PLUGIN_SLUG );
	}

	public function should_show_optin(): bool {
		$should_show = get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show === self::YES ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), self::NO );
		}

		$should_show = ( $should_show === self::YES );

		return apply_filters( 'stellarwp/telemetry/should_show_optin', $should_show );
	}

	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/show_optin_option_name', ( new OptInStatus() )->get_option_name() . '_show_optin' );
	}

	public function run_optin(): void {
		( new OptInTemplate() )->render();
	}
}
