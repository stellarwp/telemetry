<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Config;

class ConfigTest extends WPTestCase {

	public function tearDown(): void {
		parent::tearDown();
		Config::reset();
	}

	public function test_it_should_set_prefix() {
		Config::set_hook_prefix( 'some-prefix' );

		$this->assertEquals( 'some-prefix/', Config::get_hook_prefix() );
	}
}
