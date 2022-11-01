<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\ActivationHook;
use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Runnable;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\OptInStatus;
use StellarWP\Telemetry\OptInTemplate;
use StellarWP\Telemetry\RegisterSiteRequest;
use StellarWP\Telemetry\Starter;
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
		$this->container->bind( DataProvider::class, DebugDataProvider::class );
		$this->container->singleton( OptInStatus::class );
		$this->container->singleton(
			Starter::class,
			function () {
				return new Starter(
					$this->container->get( OptInStatus::class ),
					$this->container->get( DataProvider::class ),
					new OptInTemplate()
				);
			},
			[ 'init' ]
		);
		$this->container->singleton( Runnable::class, ActivationHook::class );
		$this->container->bind( RegisterSiteRequest::class, function () {
			return new RegisterSiteRequest(
				$this->container->get( DataProvider::class ),
				$this->container->get( OptInStatus::class )
			);
		} );

		// Run the activation hook code to register our plugin option.
		$this->container->get( Runnable::class )->run();

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
		$starter = $this->container->get( Starter::class );
		$optin_status = $this->container->get( OptInStatus::class );
		$request = new TelemetrySendDataRequest( $this->container->get( DataProvider::class ), $optin_status );

		// Test we currently have a token
		$this->assertArrayHasKey( 'token', $optin_status->get_option() );

		// Run the request
		$request->run();

		// Test the request was successful.
		$this->assertArrayHasKey( 'status', $request->response );

		// Modify the WP_Version value for our next test.
		global $wp_version;
		$wp_version = '1.3.3.7';

		// Run the request
		$request->run();

		// Test the request was successful.
		$this->assertArrayHasKey( 'status', $request->response );

		// TODO: Ideally at this point I would do an assertSee with the 1.3.3.7 text.
	}
}
