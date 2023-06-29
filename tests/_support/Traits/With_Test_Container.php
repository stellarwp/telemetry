<?php

namespace StellarWP\Telemetry\Tests\Support\Traits;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Tests\Container;

trait With_Test_Container {
	/**
	 * @before
	 */
	public function set_container(): void {
		$test_container = new Container();
		$test_container->setVar( Core::SITE_PLUGIN_DIR, '/app/public/wp-content/plugins/' );
		Config::set_container( $test_container );
	}

	/**
	 * @after
	 */
	public function reset_config(): void {
		Config::reset();
	}
}
