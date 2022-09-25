<?php

namespace StellarWP\Telemetry;

class DefaultActivationHook implements ActivationHook {
	public function run( PluginStarter $plugin ): void {
		$option = $plugin->get_option();

		// Check if plugin slug exists within array
		if ( ! array_key_exists( $plugin->get_plugin_slug(), $option ) ) {
			// If plugin slug does not exist, add it.
			$option[ $plugin->get_plugin_slug() ] = [
				'optin' => false,
			];

			// Save option.
			update_option( $plugin->get_option_name(), $option );
		}

		// Add redirect option for the user who activated the plugin, if redirection is enabled.
		if ( $plugin->get_activation_redirect() ) {
			// Do not add redirect option if doing a bulk activation.
			if (
				( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
				( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
				return;
			}

			add_option( 'stellarwp_telemetry_redirection', wp_get_current_user()->ID );
		}
	}
}