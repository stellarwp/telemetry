<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Template;

class Starter {
	public const CRON_INTERVAL = WEEK_IN_SECONDS;
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

		// Add cronjob if it doesn't exist and opted in.
		$this->maybe_register_cron_job();

		if ( $this->is_settings_page() ) {
			if ( $this->should_show_optin() ) {
				// Run optin.
				$this->run_optin();
			}
		}
	}

	public function register_cronjob_handlers(): void {
		add_action( $this->get_cron_hook_name(), function () {
			if ( empty( $this->get_telemetry_url() ) ) {
				return;
			}

			wp_remote_post( $this->get_telemetry_url(), [
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => $this->get_telemetry_body(),
			] );
		}, 10, 0 );
	}

	public function get_cron_hook_name() {
		return apply_filters( 'stellarwp_telemetry_cron_hook_name', $this->optin_status->get_option_name() . '_cron' );
	}

	public function get_telemetry_body() {
		return apply_filters( 'stellarwp_telemetry_body', json_encode( [
			'data' => $this->provider->get_data(),
		] ) );
	}

	public function get_plugin_version(): string {
		return apply_filters( 'stellarwp_telemetry_version', self::PLUGIN_VERSION );
	}

	public function maybe_register_cron_job(): void {
		if ( $this->optin_status::STATUS_ACTIVE === $this->optin_status->get() ) {
			if ( ! as_next_scheduled_action( $this->get_cron_hook_name() ) ) {
				$this->register_cron_job( time() );
			}
		}
	}

	public function register_cron_job( int $start ): int {
		return as_schedule_recurring_action( $start, $this->get_cron_interval(), $this->get_cron_hook_name() );
	}

	public function get_cron_interval() {
		return apply_filters( 'stellarwp_telemetry_cron_interval', self::CRON_INTERVAL );
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