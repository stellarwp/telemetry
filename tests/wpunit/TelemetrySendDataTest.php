<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\ActivationHook;
use StellarWP\Telemetry\DefaultActivationHook;
use StellarWP\Telemetry\DefaultOptinTemplate;
use StellarWP\Telemetry\DefaultPluginStarter;
use StellarWP\Telemetry\DefaultTelemetryProvider;
use StellarWP\Telemetry\PluginStarter;
use StellarWP\Telemetry\RegisterSiteRequest;
use StellarWP\Telemetry\TelemetryProvider;
use StellarWP\Telemetry\TelemetrySendDataRequest;

class TelemetrySendDataTest extends WPTestCase {
	/**
	 * @var WpunitTester
	 */
	protected $tester;

	/** @var Container */
	protected $container;

	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.
		$this->container = new Container();
		$this->container->bind( TelemetryProvider::class, DefaultTelemetryProvider::class );
		$this->container->singleton(
			PluginStarter::class,
			function () {
				return new DefaultPluginStarter(
					new DefaultOptinTemplate(),
					$this->container->get( TelemetryProvider::class )
				);
			},
			[ 'init' ]
		);
		$this->container->singleton( ActivationHook::class, DefaultActivationHook::class );
		$this->container->bind( RegisterSiteRequest::class, function () {
			return new RegisterSiteRequest(
				$this->container->get( PluginStarter::class ),
				$this->container->get( TelemetryProvider::class )
			);
		} );

		// Run the activation hook code to register our plugin option.
		$this->container->get( ActivationHook::class )->run();

		// Run the register site request to get a Token.
		$this->container->get( RegisterSiteRequest::class )->run();
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	// Tests
	public function test_it_sends_telemetry_send_data_request() {
		$starter = $this->container->get( PluginStarter::class );
		$request = new TelemetrySendDataRequest( $starter, $this->container->get( TelemetryProvider::class ) );

		// Test we currently have a token
		$this->assertArrayHasKey( 'token', $starter->get_meta() );

		// Test we can send the data
		$this->assertTrue( $request->run() );

		// Test we can send modified data to same site using the same token
		global $wp_version;
		$wp_version = '1.3.3.7';

		$this->assertTrue( $request->run() );

		// TODO: Ideally at this point I would do an assertSee with the 1.3.3.7 text.
	}
}
