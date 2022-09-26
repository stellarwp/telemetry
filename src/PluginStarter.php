<?php

namespace StellarWP\Telemetry;

abstract class PluginStarter {
	protected const OPTION = 'stellar_telemetry';
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

	public function get_option(): array {
		return (array) get_option( $this->get_option_name(), [] );
	}

	public function get_activation_redirect(): string {
		return apply_filters( 'stellarwp_telemetry_activation_redirect', $this->activation_redirect );
	}

	public function should_redirect_on_activation(): bool {
		return (bool) apply_filters( 'stellarwp_telemetry_redirect_on_activation', self::REDIRECT_ON_ACTIVATION );
	}

	function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_redirection_option_name', $this->get_plugin_slug() . '_redirection' );
	}
}