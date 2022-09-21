<?php

namespace StellarWP\Telemetry;

abstract class PluginStarter {
	/** @var Template $optin_template */
	private $optin_template;

	/** @var ActivationHook $activation_hook */
	private $activation_hook;

	/** @var string $plugin_slug */
	private $plugin_slug;

	/** @var string $plugin_version */
	private $plugin_version;

	/** @var string $inherit_option */
	private $inherit_option;

	public function register_hooks(): void {
		$this->activation_hook->register();
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