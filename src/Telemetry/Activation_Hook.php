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
		// TODO: optin->show,
		// Check if plugin slug exists within array
		if ( $this->optin_status->plugin_exists( $this->container->get( Core::PLUGIN_SLUG ) ) ) {
			$this->optin_status->add_plugin( $this->container->get( Core::PLUGIN_SLUG ) );

			// TODO: Look for a way to move this to the Plugin.
			// We should display the optin template on next load.
			update_option( $this->get_show_optin_option_name(), "1" );
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
		return apply_filters( 'stellarwp/telemetry/redirect_on_activation', true );
	}

	public function get_redirection_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/redirection_option_name', $this->optin_status->get_option_name() . '_redirection' );
	}

	/**
	 * Determines if the optin modal should be shown to the user.
	 */
	public function should_show_optin(): bool {
		$should_show = (bool) get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), false );
		}

		return apply_filters( 'stellarwp/telemetry/should_show_optin', $should_show );
	}

	/**
	 * Gets the optin option name.
	 */
	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/show_optin_option_name', $this->container->get( Opt_In_Status::class )->get_option_name() . '_show_optin' );
	}
}
