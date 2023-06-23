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
		Config::set_hook_prefix( 'telemetry-library' );
		Config::set_stellar_slug( 'telemetry-library' );
		Config::set_server_url( 'https://telemetry-library.lndo.site/api/v1' );
		Config::set_container( $container );
		Telemetry::instance()->init( __FILE__ );
	}
);
