<?php

namespace StellarWP\Telemetry;

abstract class PluginStarter {
	protected const OPTION = 'stellarwp_telemetry';
	protected const REDIRECT_ON_ACTIVATION = true;

	/** @var Template $optin_template */
	protected $optin_template;

	/** @var ActivationHook $activation_hook */
	protected $activation_hook;

	/** @var string $plugin_slug */
	protected $plugin_slug;

	/** @var string $plugin_version */
	protected $plugin_version;

	/** @var string $option_to_inherit */
	protected $option_to_inherit;

	/** @var string $activation_redirect */
	protected $activation_redirect;

	/** @var string $telemetry_url */
	protected $telemetry_url;

	/** @var bool $enqueues_applied */
	protected $enqueues_applied = false;

	/** @var bool $create_settings_page */
	protected $create_settings_page = false;

	public function activation_hook(): void {
		$this->activation_hook->run( $this );
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

	public function get_plugin_slug(): string {
		return apply_filters( 'stellarwp_telemetry_plugin_slug', $this->plugin_slug );
	}

	public function get_plugin_version(): string {
		return apply_filters( 'stellarwp_telemetry_version', $this->plugin_version );
	}

	public function get_option_to_inherit(): string {
		return apply_filters( 'stellarwp_telemetry_option_to_inherit', $this->option_to_inherit );
	}

	public function get_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_option_name', self::OPTION );
	}

	public function get_meta(): array {
		return (array) get_option( $this->get_option_name(), [] );
	}

	public function get_activation_redirect(): string {
		return apply_filters( 'stellarwp_telemetry_activation_redirect', $this->activation_redirect );
	}

	public function should_redirect_on_activation(): bool {
		return (bool) apply_filters( 'stellarwp_telemetry_redirect_on_activation', self::REDIRECT_ON_ACTIVATION );
	}

	public function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_redirection_option_name', $this->get_option_name() . '_redirection' );
	}

	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_show_optin_option_name', $this->get_option_name() . '_show_optin' );
	}

	public function get_optin_status_option_name() {
		return apply_filters( 'stellarwp_telemetry_optin_status_option_name', $this->get_option_name() . '_optin_status' );
	}

	public function should_show_optin(): bool {
		$should_show = get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show === "1" ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), "-1" );
		}

		$should_show = ( $should_show === "1" );

		return apply_filters( 'stellarwp_telemetry_should_show_optin', $should_show );
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

	public function is_settings_page(): bool {
		return ( isset( $_GET['page'] ) && $_GET['page'] === $this->get_plugin_slug() );
	}

	protected function perform_activation_redirect(): void {
		if ( $this->should_redirect_on_activation() &&
		     ! wp_doing_ajax() &&
		     ( intval( get_option( $this->get_redirection_option_name(), false ) ) === wp_get_current_user()->ID )
		) {
			delete_option( $this->get_redirection_option_name() );
			wp_safe_redirect( admin_url( $this->get_activation_redirect() ) );
			exit;
		}
	}

	public function get_cron_hook_name() {
		return apply_filters( 'stellarwp_telemetry_cron_hook_name', $this->get_option_name() . '_cron' );
	}

	public function get_cron_interval() {
		return apply_filters( 'stellarwp_telemetry_cron_interval', DAY_IN_SECONDS );
	}

	public function get_telemetry_url() {
		return apply_filters( 'stellarwp_telemetry_url', $this->telemetry_url );
	}

	public function get_telemetry_body() {
		// TODO: Use DI to inject the telemetry provider.
		$provider = new DefaultTelemetryProvider();

		return apply_filters( 'stellarwp_telemetry_body', json_encode( [
			'data' => $provider->get_data(),
		] ) );
	}

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

	protected function add_pre_update_filters() {
		add_filter( 'pre_update_option_' . $this->get_optin_status_option_name(), function ( $value, $old_value, $option ) {
			$meta = $this->get_meta();

			// If the value is true, and the old value is false, we want to update the optin status to true on all plugins.
			if ( $value === "1" && $old_value === "-1" ) {
				foreach ( $meta as $plugin_slug => $plugin_meta ) {
					$meta[ $plugin_slug ]['optin'] = true;
				}

				update_option( $this->get_option_name(), $meta );
			}

			// If the old value is true, and the new value is false, we want to update the optin status to false on all plugins.
			if ( $value === "-1" && $old_value === "1" ) {
				foreach ( $meta as $plugin_slug => $plugin_meta ) {
					$meta[ $plugin_slug ]['optin'] = false;
				}

				update_option( $this->get_option_name(), $meta );
			}

			return $value;
		}, 10, 3 );
	}

}