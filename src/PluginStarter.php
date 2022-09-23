<?php

namespace StellarWP\Telemetry;

abstract class PluginStarter {
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

	public function activation_hook(): void {
		$this->activation_hook->run( $this );
	}

	public function apply_enqueues(): void {
		$this->optin_template->enqueue();
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
}