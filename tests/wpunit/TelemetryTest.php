<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Telemetry;

class TelemetryTest extends WPTestCase {
	/** @var WpunitTester */
	protected $tester;

	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.
	}

	// Tests
	public function test_we_can_instantiate_it() {
		$this->assertInstanceOf( Telemetry::class, new Telemetry() );
	}

	public function test_we_can_register_site() {
		$telemetry = new Telemetry();

		$this->assertTrue( $telemetry->register_site() );
	}
}
