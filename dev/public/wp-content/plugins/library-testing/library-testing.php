<?php
/**
 * Plugin Name: Telemetry Library
 * Description: A plugin for working on the telemetry library directly.
 * Version: 1.0
 * Author: StellarWP
 */

use StellarWP\TelemetryLibraryTesting\Container;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core as Telemetry;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

add_action(
	'plugins_loaded',
	static function (): void {
		$container = new Container();
		Config::set_hook_prefix( 'telemetry-prefix' );
		Config::set_stellar_slug( 'telemetry-library' );
		Config::set_server_url( defined( 'TELEMETRY_SERVER_URL' ) ? TELEMETRY_SERVER_URL : 'https://telemetry-dev.stellarwp.com/api/v1' );
		Config::set_container( $container );
		Telemetry::instance()->init( __FILE__ );
	}
);

// Trigger the optin modal on every page load.
add_action(
	'admin_init',
	function () {
		do_action( 'stellarwp/telemetry/optin', 'telemetry-library' );
	}
);

// If the 'Send Events' link was used, send some test events once.
add_action(
	'init',
	function() {
		if ( ! isset( $_GET['send-events'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		do_action( 'stellarwp/telemetry/telemetry-prefix/event', 'opt-in', [ 'one' => 1 ] );
		do_action( 'stellarwp/telemetry/telemetry-prefix/event', 'create-post', [ 'post-title' => 'This is my first post!' ] );
		do_action( 'stellarwp/telemetry/telemetry-prefix/event', 'opt-out', [ 'one' => 1 ] );

		wp_safe_redirect( remove_query_arg( 'send-events' ) );
		exit;
	}
);

/**
 * Adds a helper link to the admin bar for sending a group of events.
 *
 * @param WP_Admin_Bar $admin_bar The adminbar class.
 *
 * @return void
 */
function add_event_send_link( $admin_bar ) {
	global $wp;

	$admin_bar->add_menu(
		[
			'id'    => 'send-events',
			'title' => 'Send Events',
			'href'  => add_query_arg( [ 'send-events' => true ], home_url( $wp->request ) ),
			'meta'  => [
				'title' => __( 'Send Events' ),
			],
		]
	);
}
add_action( 'admin_bar_menu', 'add_event_send_link', 100, 1 );
