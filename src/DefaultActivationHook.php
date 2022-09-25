<?php

namespace StellarWP\Telemetry;

class DefaultActivationHook implements ActivationHook {
	public function run( PluginStarter $plugin ): void {
		$option = $plugin->get_option();

		if ( empty( $option ) ) {
			// If the telemetry status is empty, display the optin template.
			$plugin->run_optin();
		}
	}
}