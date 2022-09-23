<?php

namespace StellarWP\Telemetry;

class DefaultActivationHook implements ActivationHook {
	public function run( PluginStarter $plugin ): void {
		$telemetry_status = get_option( 'stellar_telemetry', [] );

		if ( empty( $telemetry_status ) ) {
			// If the telemetry status is empty, display the optin template.
			$plugin->run_optin();
		}
	}
}