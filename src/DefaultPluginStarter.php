<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {

	public function __construct( ActivationHook $activation_hook, Template $optin_template ) {
		$this->activation_hook = $activation_hook;
		$this->optin_template  = $optin_template;
		$this->plugin_slug     = 'stellarwp_telemetry_starter';
		$this->plugin_version  = '1.0.0';
		$this->inherit_option  = 'freemius_inherit';
	}

}