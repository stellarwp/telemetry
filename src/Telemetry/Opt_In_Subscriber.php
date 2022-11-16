<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Opt_In_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'stellarwp/telemetry/optin', [ $this, 'maybe_render_optin' ] );
		add_action( 'admin_init', [ $this, 'set_optin_status' ] );
		add_action( 'admin_init', [ $this, 'initialize_optin_option' ] );
	}

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

	public function maybe_render_optin() {
		$this->container->get( Opt_In_Template::class )->maybe_render();
	}

	public function initialize_optin_option() {
		$opt_in_status = $this->container->get( Opt_In_Status::class );
		// Check if plugin slug exists within array
		if ( ! $opt_in_status->plugin_exists( $this->container->get( Core::PLUGIN_SLUG ) ) ) {
			$opt_in_status->add_plugin( $this->container->get( Core::PLUGIN_SLUG ) );

			update_option( $opt_in_status->get_show_optin_option_name(), "1" );
		}
	}

}
