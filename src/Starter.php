<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Template;

class Starter {
	public const PLUGIN_SLUG = 'stellarwp-telemetry-starter';
	public const PLUGIN_VERSION = '1.0.0';
	public const YES = "1";
	public const NO = "-1";

	/** @var Template */
	protected $optin_template;

	/** @var DataProvider */
	protected $provider;

	/** @var OptInStatus */
	protected $optin_status;

	public function __construct( OptInStatus $optin_status, DataProvider $provider, Template $optin_template ) {
		$this->optin_status   = $optin_status;
		$this->provider       = $provider;
		$this->optin_template = $optin_template;
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
		return apply_filters( 'stellarwp_telemetry_version', self::PLUGIN_VERSION );
	}

	public function is_settings_page(): bool {
		$is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] === $this->get_plugin_slug() );

		return apply_filters(
			'stellarwp_telemetry_is_settings_page',
			$is_settings_page
		);
	}

	public function get_plugin_slug(): string {
		return apply_filters( 'stellarwp_telemetry_plugin_slug', self::PLUGIN_SLUG );
	}

	public function should_show_optin(): bool {
		$should_show = get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show === self::YES ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), self::NO );
		}

		$should_show = ( $should_show === self::YES );

		return apply_filters( 'stellarwp_telemetry_should_show_optin', $should_show );
	}

	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_show_optin_option_name', $this->optin_status->get_option_name() . '_show_optin' );
	}

	public function run_optin(): void {
		$this->optin_template->render();
	}
}