<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\Runnable;

class Activation_Hook implements Runnable {
	/** @var Opt_In_Status */
	private $optin_status;

	/** @var Container */
	private $container;

	public function __construct( Opt_In_Status $optin_status, Container $container ) {
		$this->optin_status = $optin_status;
		$this->container    = $container;
	}

	public function run(): void {
		// Add redirect option for the user who activated the plugin, if redirection is enabled.
		if ( $this->should_redirect_on_activation() ) {
			// Do not add redirect option if doing a bulk activation.
			if (
				( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
				( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
				return;
			}

			update_option( $this->get_redirection_option_name(), wp_get_current_user()->ID );
		}
	}

	protected function should_redirect_on_activation(): bool {
		return apply_filters( 'stellarwp/telemetry/redirect_on_activation', true );
	}

	public function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/redirection_option_name', $this->optin_status->get_option_name() . '_redirection' );
	}
}
