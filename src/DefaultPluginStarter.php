<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {

	public function __construct( array $args = [] ) {
		$args                  = wp_parse_args( $args, [
			'activation_hook' => new DefaultActivationHook(),
			'optin_template'  => new DefaultOptinTemplate(),
			'plugin_slug'     => 'stellarwp-telemetry-starter',
			'plugin_version'  => '1.0.0',
			'inherit_option'  => 'freemius_inherit',
		] );

		$this->activation_hook = $args['activation_hook'];
		$this->optin_template  = $args['optin_template'];
		$this->plugin_slug     = $args['plugin_slug'];
		$this->plugin_version  = $args['plugin_version'];
		$this->inherit_option  = $args['inherit_option'];
	}

}