<?php

namespace StellarWP\Telemetry;

abstract class PluginStarter {
	protected const OPTION = 'stellar_telemetry';

	/** @var Template $optin_template */
	protected $optin_template;

	/** @var ActivationHook $activation_hook */
	protected $activation_hook;

	/** @var string $plugin_slug */
	protected $plugin_slug;

	/** @var string $plugin_version */
	protected $plugin_version;

	/** @var string $inherit_option */
	protected $inherit_option;

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
		// TODO: Add apply_filters
		return $this->plugin_slug;
	}

	public function get_plugin_version(): string {
		return $this->plugin_version;
	}

	public function get_inherit_option(): string {
		return $this->inherit_option;
	}

	public function get_option_name(): string {
		// TODO: apply filters
		return self::OPTION;
	}

	public function get_option(): array {
		return (array) get_option( $this->get_option_name(), [] );
	}

	public function get_activation_redirect(): string {
		// TODO: apply filters
		return $this->activation_redirect;
	}
}