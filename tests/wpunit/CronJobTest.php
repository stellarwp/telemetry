<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\ActivationHook;
use StellarWP\Telemetry\Contracts\CronJob as CronJobContract;
use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\CronJob;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\OptInStatus;
use StellarWP\Telemetry\OptInTemplate;
use StellarWP\Telemetry\RegisterSiteRequest;
use StellarWP\Telemetry\Starter;
use StellarWP\Telemetry\TelemetrySendDataRequest;

class CronJobTest extends WPTestCase {
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
		$this->container->singleton( ActivationHook::class );
		$this->container->singleton( RegisterSiteRequest::class );
		$this->container->singleton( TelemetrySendDataRequest::class );
		$this->container->bind( CronJobContract::class, function () {
			return new CronJob(
				$this->container->get( OptInStatus::class ),
				$this->container->get( TelemetrySendDataRequest::class )
			);
		} );

		// Run the activation hook code to register our plugin option.
		$this->container->get( ActivationHook::class )->run();

		// Run the register site request to get a Token.
		$this->container->get( RegisterSiteRequest::class )->send();
	}

	public function tearDown(): void {
		// Your tear down methods here.

		// Then...
		parent::tearDown();
	}

	public function test_cron_can_be_scheduled() {
		$cron = $this->container->get( CronJobContract::class );

		// Cron should not be scheduled by default
		$this->assertFalse( $cron->is_scheduled() );

		// Schedule the cron
		$cron->schedule( time() );

		// Cron should be scheduled
		$this->assertTrue( $cron->is_scheduled() );
	}
}
