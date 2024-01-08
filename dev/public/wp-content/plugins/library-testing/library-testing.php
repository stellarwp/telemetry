<?php
/**
 * Plugin Name: Telemetry Library
 * Description: A plugin for working on the telemetry library directly.
 * Version: 2.3.0-rc.01
 * Author: StellarWP
 */

use StellarWP\TelemetryLibraryTesting\Container;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core as Telemetry;
use StellarWP\TelemetryLibraryTesting\Settings_Page;

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

// Initialize the Settings Page.
new Settings_Page();
