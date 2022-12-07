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

		// We're not attempting an action.
		if ( empty( $_POST['_wpnonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( $_POST['_wpnonce'] );

		if ( ! wp_verify_nonce( $nonce, 'stellarwp-telemetry' ) ) {
			return;
		}

		// We're not attempting a telemetry action.
		if ( isset( $_POST['action'] ) && 'stellarwp-telemetry' !== $_POST['action'] ) {
			return;
		}

		// The user did not respond to the opt-in modal.
		if ( ! isset( $_POST['optin-agreed'] ) ) {
			return;
		}

		// User agreed to opt-in to Telemetry.
		if ( 'true' === $_POST['optin-agreed'] ) {
			$this->opt_in();
		}

		// Don't show the opt-in modal again.
		update_option( $this->container->get( Opt_In_Template::class )->get_option_name(), '0' );
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
		$plugin_slug     = $this->container->get( Core::PLUGIN_SLUG );
		$opt_in_template = $this->container->get( Opt_In_Template::class );
		$opt_in_status   = $this->container->get( Opt_In_Status::class );

		// Check if plugin slug exists within array.
		if ( ! $opt_in_status->plugin_exists( $plugin_slug ) ) {
			$opt_in_status->add_plugin( $plugin_slug );

			update_option( $opt_in_template->get_option_name(), '1' );
		}
	}

	/**
	 * Registers the site/user with the telemetry server and sets the opt-in status.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function opt_in() {
		$this->container->get( Opt_In_Status::class )->set_status( true );

		try {
			$this->container->get( Telemetry::class )->register_site();
			$this->container->get( Telemetry::class )->register_user();
		} catch ( \Error $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We don't want to throw errors if the server cannot be reached.
		}
	}
}
