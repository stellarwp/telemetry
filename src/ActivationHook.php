<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\Runnable;

class ActivationHook implements Runnable {
	public const REDIRECT_ON_ACTIVATION = true;

	/** @var OptInStatus */
	private $optin_status;

	/** @var Container */
	private $container;

	public function __construct( OptInStatus $optin_status, Container $container ) {
		$this->optin_status = $optin_status;
		$this->container    = $container;
	}

	public function run(): void {
		// TODO: optin->show,
		// Check if plugin slug exists within array
		if ( $this->optin_status->plugin_exists( $this->container->get( Core::PLUGIN_SLUG )->get_plugin_slug() ) ) {
			$this->optin_status->add_plugin( $this->container->get( Core::PLUGIN_SLUG )->get_plugin_slug() );

			// TODO: Look for a way to move this to the Plugin.
			// We should display the optin template on next load.
			update_option( $this->container->get( Core::PLUGIN_SLUG )->get_show_optin_option_name(), "1" );
		}

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
		return apply_filters( 'stellarwp/telemetry/redirect_on_activation', self::REDIRECT_ON_ACTIVATION );
	}

	public function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/redirection_option_name', $this->optin_status->get_option_name() . '_redirection' );
	}
}
