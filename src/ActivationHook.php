<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Runnable;

class ActivationHook implements Runnable {
	public const REDIRECT_ON_ACTIVATION = true;

	/**
	 * @var Starter
	 */
	private $starter;

	public function __construct( Starter $starter ) {
		$this->starter = $starter;
	}

	public function run(): void {
		$meta = $this->starter->get_meta();

		// Check if plugin slug exists within array
		if ( ! array_key_exists( $this->starter->get_plugin_slug(), $meta ) ) {
			// If plugin slug does not exist, add it.
			$meta[ $this->starter->get_plugin_slug() ] = [
				'optin' => false,
			];

			// Save option.
			update_option( $this->starter->get_option_name(), $meta );

			// We should display the optin template on next load.
			update_option( $this->starter->get_show_optin_option_name(), "1" );
		}

		// Add redirect option for the user who activated the plugin, if redirection is enabled.
		if ( $this->should_redirect_on_activation() ) {
			// Do not add redirect option if doing a bulk activation.
			if (
				( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
				( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
				return;
			}

			add_option( $this->get_redirection_option_name(), wp_get_current_user()->ID );
		}
	}

	protected function should_redirect_on_activation(): bool {
		return apply_filters( 'stellarwp_telemetry_redirect_on_activation', self::REDIRECT_ON_ACTIVATION );
	}

	public function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_redirection_option_name', $this->starter->get_option_name() . '_redirection' );
	}
}