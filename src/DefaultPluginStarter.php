<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {
	public const TELEMETRY_URL = 'https://telemetry-api.moderntribe.qa/api/v1';

	public function __construct( Template $optin_template, TelemetryProvider $provider ) {
		$this->optin_template  = $optin_template;
		$this->provider        = $provider;
	}

	public function init(): void {
		// We don't want to run this on every ajax request.
		if ( wp_doing_ajax() ) {
			return;
		}

		// Add cronjob if it doesn't exist and opted in.
		$this->maybe_add_cronjobs();

		if ( $this->is_settings_page() ) {
			if ( $this->should_show_optin() ) {
				// Apply enqueues.
				$this->apply_enqueues();

				// Run optin.
				$this->run_optin();
			}
		}
	}

	// TODO: Add uninstall hook and remove cronjob if exists and option only has current plugin slug as false, or all are false.
}