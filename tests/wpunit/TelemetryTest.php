<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\DebugDataProvider;
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
		$this->assertInstanceOf( Telemetry::class, new Telemetry( new DebugDataProvider(), 'stellarwp_telemetry' ) );
	}

	public function test_we_can_register_site() {
		$telemetry = new Telemetry( new DebugDataProvider(), 'stellarwp_telemetry' );

		// Check that the site is not registered
		$this->assertFalse( $telemetry->is_registered() );

		// Register the site
		$this->assertTrue( $telemetry->register_site() );

		// Check that the site is registered
		$this->assertTrue( $telemetry->is_registered() );

		// Check that if the site is already registered, we don't re-register it.
		$this->assertFalse( $telemetry->register_site() );

		// Check that we can re-register the site if we pass true to the force parameter
		$this->assertTrue( $telemetry->register_site( true ) );
	}
}
