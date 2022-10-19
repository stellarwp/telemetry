<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\Runnable;
use StellarWP\Telemetry\ActivationHook;
use StellarWP\Telemetry\OptInTemplate;
use StellarWP\Telemetry\ExampleStarter;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\Starter;
use StellarWP\Telemetry\RegisterSiteRequest;
use StellarWP\Telemetry\DataProvider;

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
		$this->container->bind( DataProvider::class, DebugDataProvider::class );
		$this->container->singleton(
			Starter::class,
			function () {
				return new ExampleStarter(
					new OptInTemplate(),
					$this->container->get( DataProvider::class )
				);
			},
			[ 'init' ]
		);
		$this->container->singleton( Runnable::class, ActivationHook::class );

		// Run the activation hook code to register our plugin option.
		$this->container->get( Runnable::class )->run();
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

		$this->assertInstanceOf( ExampleStarter::class, $this->container->get( Starter::class ) );
	}

	public function test_it_saves_token_on_register_site_request() {
		$starter = $this->container->get( Starter::class );
		$request = new RegisterSiteRequest( $starter, $this->container->get( DataProvider::class ) );

		// Test we currently have no token.
		$this->assertArrayNotHasKey( 'token', $starter->get_meta() );

		// Run the request
		$request->run();

		// Test the request was successful.
		$this->assertArrayHasKey( 'status', $request->response );

		// Test we save the user token on run.
		$this->assertArrayHasKey( 'token', $starter->get_meta() );
	}
}
