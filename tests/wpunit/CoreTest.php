<?php
/**
 * Handles all tests related to the Core class.
 */
use StellarWP\Telemetry\Config;
use lucatume\WPBrowser\TestCase\WPTestCase;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Tests\Container;

class CoreTest extends WPTestCase {

	public function test_it_throws_exception_without_container() {
		$core = new Core();

		$this->expectException( RuntimeException::class );
		$core->init( '/some/path/to/plugin.php' );
	}

	public function test_it_returns_a_valid_instance() {
		Config::set_container( new Container() );
		$core = Config::get_container()->get( Core::class );
		$this->assertInstanceOf( Core::class, $core->instance() );
	}

	public function test_it_returns_container_interface() {
		Config::set_container( new Container() );
		$core = Config::get_container()->get( Core::class );
		$core->init( '/some/path/to/plugin.php' );

		$this->assertInstanceOf( \StellarWP\ContainerContract\ContainerInterface::class, $core->container() );
	}
}
