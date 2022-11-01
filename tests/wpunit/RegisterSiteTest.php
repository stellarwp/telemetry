<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\ActivationHook;
use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\OptInStatus;
use StellarWP\Telemetry\OptInTemplate;
use StellarWP\Telemetry\RegisterSiteRequest;
use StellarWP\Telemetry\Starter;

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
		$container = new Container();
		$container->bind( DataProvider::class, DebugDataProvider::class );
		$container->singleton( OptInStatus::class );
		$container->singleton(
			Starter::class,
			static function () use ( $container ) {
				return new Starter(
					$container->get( OptInStatus::class ),
					$container->get( DataProvider::class ),
					new OptInTemplate()
				);
			}
		);
		$container->bind( ActivationHook::class, static function () use ( $container ) {
			return new ActivationHook( $container->get( OptInStatus::class ), $container->get( Starter::class ) );
		} );
		$container->singleton( OptInTemplate::class, static function () use ( $container ) {
			return new OptInTemplate();
		} );

		$this->container = $container;

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

		$this->assertInstanceOf( Starter::class, $this->container->get( Starter::class ) );
	}

	public function test_it_saves_token_on_register_site_request() {
		$starter = $this->container->get( Starter::class );
		$optin_status = $this->container->get( OptInStatus::class );
		$request = new RegisterSiteRequest( $this->container->get( DataProvider::class ), $optin_status );

		// Test we currently have no token.
		$this->assertArrayNotHasKey( 'token', $optin_status->get_option() );

		// Run the request
		$request->run();

		// Test the request was successful.
		$this->assertArrayHasKey( 'status', $request->response );

		// Test we save the user token on run.
		$this->assertArrayHasKey( 'token', $optin_status->get_option() );
	}
}
