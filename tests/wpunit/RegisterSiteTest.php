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

class RegisterSiteTest extends WPTestCase {
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

		// Run the activation hook code to register our plugin option.
		$this->container->get( ActivationHook::class )->run();
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	// Tests
	public function test_it_works() {
		$post = static::factory()->post->create_and_get();

		$this->assertInstanceOf( WP_Post::class, $post );

		$this->assertInstanceOf( DefaultPluginStarter::class, $this->container->get( PluginStarter::class ) );
	}

	public function test_it_saves_token_on_register_site_request() {
		$starter = $this->container->get( PluginStarter::class );
		$request = new RegisterSiteRequest( $starter, $this->container->get( TelemetryProvider::class ) );

		// Test we currently have no token.
		$this->assertArrayNotHasKey( 'token', $starter->get_meta() );

		// Test we save the user token on run.
		$this->assertTrue( $request->run() );
		$this->assertArrayHasKey( 'token', $starter->get_meta() );
	}
}
