<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Template;

abstract class Starter {
	public const OPTION                 = 'stellarwp_telemetry';
	public const CRON_INTERVAL          = WEEK_IN_SECONDS;
	public const PLUGIN_SLUG            = 'stellarwp-telemetry-starter';
	public const PLUGIN_VERSION         = '1.0.0';
	public const TELEMETRY_URL          = 'https://telemetry-api.moderntribe.qa/api/v1';
	public const YES                    = "1";
	public const NO                     = "-1";

	/** @var Template $optin_template */
	protected $optin_template;

	/** @var DataProvider $provider */
	protected $provider;

	/** @var bool $enqueues_applied */
	protected $enqueues_applied = false;

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
		return apply_filters( 'stellarwp_telemetry_cron_hook_name', $this->get_option_name() . '_cron' );
	}

	public function get_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_option_name', $this::OPTION );
	}

	public function get_telemetry_url() {
		return apply_filters( 'stellarwp_telemetry_url', $this::TELEMETRY_URL );
	}

	public function get_telemetry_body() {
		return apply_filters( 'stellarwp_telemetry_body', json_encode( [
			'data' => $this->provider->get_data(),
		] ) );
	}

	public function get_plugin_version(): string {
		return apply_filters( 'stellarwp_telemetry_version', $this::PLUGIN_VERSION );
	}

	public abstract function init(): void;

	public function maybe_add_cronjobs() {
		if ( $this->get_optin_status() ) {
			add_action( 'admin_init', function () {
				// If the cron job is not scheduled, schedule it using as_next_scheduled_action().
				if ( ! as_next_scheduled_action( $this->get_cron_hook_name() ) ) {
					as_schedule_recurring_action( time(), $this->get_cron_interval(), $this->get_cron_hook_name() );
				}
			} );
		}
	}

	public function get_optin_status(): bool {
		$status = true;
		$meta   = $this->get_meta();

		// If any plugin's status is false, we should return false.
		foreach ( $meta as $plugin ) {
			if ( $plugin['optin'] === false ) {
				$status = false;
				break;
			}
		}

		return $status;
	}

	public function get_meta(): array {
		return (array) get_option( $this->get_option_name(), [] );
	}

	public function get_cron_interval() {
		return apply_filters( 'stellarwp_telemetry_cron_interval', $this::CRON_INTERVAL );
	}

	public function is_settings_page(): bool {
		return ( isset( $_GET['page'] ) && $_GET['page'] === $this->get_plugin_slug() );
	}

	public function get_plugin_slug(): string {
		return apply_filters( 'stellarwp_telemetry_plugin_slug', $this::PLUGIN_SLUG );
	}

	public function should_show_optin(): bool {
		$should_show = get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show === $this::YES ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), $this::NO );
		}

		$should_show = ( $should_show === $this::YES );

		return apply_filters( 'stellarwp_telemetry_should_show_optin', $should_show );
	}

	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_show_optin_option_name', $this->get_option_name() . '_show_optin' );
	}

	public function apply_enqueues(): void {
		if ( $this->enqueues_applied ) {
			return;
		}

		// Apply each template enqueues.
		$this->optin_template->enqueue();
		// $this->uninstall_template->enqueue();

		$this->enqueues_applied = true;
	}

	public function run_optin(): void {
		$this->optin_template->render();
	}

	public function get_token(): string {
		$meta = $this->get_meta();

		return apply_filters( 'stellarwp_telemetry_token', $meta['token'] ?? '' );
	}
}