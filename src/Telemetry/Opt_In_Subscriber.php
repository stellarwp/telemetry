<?php
/**
 * Handles all actions/filters related to the opt-in.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Class to handle all actions/filters related to the opt-in.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Opt_In_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'optin', [ $this, 'maybe_render_optin' ] );
		add_action( 'admin_init', [ $this, 'set_optin_status' ] );
		add_action( 'admin_init', [ $this, 'initialize_optin_option' ] );
	}

	/**
	 * Sets the opt-in status for the site.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_optin_status() {
		// If GET param is set, handle plugin actions.
		if ( isset( $_GET['action'] ) && 'stellarwp-telemetry' === $_GET['action'] ) {
			// If user opted in, register the site and don't show modal again.
			if ( isset( $_GET['optin-agreed'] ) && 'true' === $_GET['optin-agreed'] ) {
				$this->container->get( Opt_In_Status::class )->set_status( true );
				update_option( $this->container->get( Opt_In_Status::class )->get_show_optin_option_name(), "0" );
			}
		}
	}

	/**
	 * Renders the opt-in modal if it should be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_render_optin() {
		$this->container->get( Opt_In_Template::class )->maybe_render();
	}

	/**
	 * Sets the initial value when the plugin is loaded.
	 *
	 * If the plugin doesn't already have the opt-in option set, we need to set it
	 * so that the opt-in should be shown to the user when the do_action is run.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function initialize_optin_option() {
		$opt_in_status = $this->container->get( Opt_In_Status::class );
		// Check if plugin slug exists within array
		if ( ! $opt_in_status->plugin_exists( $this->container->get( Core::PLUGIN_SLUG ) ) ) {
			$opt_in_status->add_plugin( $this->container->get( Core::PLUGIN_SLUG ) );

			update_option( $opt_in_status->get_show_optin_option_name(), "1" );
		}
	}

}
