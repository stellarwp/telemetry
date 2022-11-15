<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Opt_In_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'stellarwp/telemetry/optin', [ $this, 'maybe_render_optin' ] );
		add_action( 'admin_init', [ $this, 'initialize_optin_option' ] );
	}

	public function maybe_render_optin() {
		( new Opt_In_Template() )->maybe_render();
	}

	public function initialize_optin_option() {
		$opt_in_status = $this->container->get( Opt_In_Status::class );
		// Check if plugin slug exists within array
		if ( $opt_in_status->plugin_exists( $this->container->get( Core::PLUGIN_SLUG ) ) ) {
			$opt_in_status->add_plugin( $this->container->get( Core::PLUGIN_SLUG ) );

			update_option( $opt_in_status->get_show_optin_option_name(), "1" );
		}
	}

}
