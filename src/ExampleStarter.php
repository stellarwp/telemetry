<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Template;

class ExampleStarter extends Starter {
	public function __construct( Template $optin_template, DataProvider $provider ) {
		// TODO: Remove this class, make this all in the Starter class, and create
		//       new abstract methods for before_init(); and after_init();
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