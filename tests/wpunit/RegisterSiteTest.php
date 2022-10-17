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
use StellarWP\Telemetry\Request;

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
		$this->container->bind( ActivationHook::class, DefaultActivationHook::class );
		$this->container->bind( TelemetryProvider::class, DefaultTelemetryProvider::class );
		$this->container->singleton(
			PluginStarter::class,
			function () {
				return new DefaultPluginStarter(
					$this->container->get( ActivationHook::class ),
					new DefaultOptinTemplate(),
					$this->container->get( TelemetryProvider::class )
				);
			}
		);
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

	public function test_it_sends_a_request_to_the_register_site_endpoint() {
		$request = new RegisterSiteRequest();
		$this->assertNotNull( $request->run() );
	}
}
