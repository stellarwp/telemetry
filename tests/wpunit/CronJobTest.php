<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\CronJob as CronJobContract;
use StellarWP\Telemetry\Contracts\DataProvider;
use StellarWP\Telemetry\Contracts\Request;
use StellarWP\Telemetry\CronJob;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\TelemetrySendDataRequest;

class CronJobTest extends WPTestCase {
	/**
	 * @var WpunitTester
	 */
	protected $tester;

	/** @var Container */
	protected $container;

	public function setUp(): void {
		// Initialize ActionScheduler
		require_once __DIR__ . '/../../vendor/woocommerce/action-scheduler/action-scheduler.php';

		// Before...
		parent::setUp();

		$this->container = new Container();
		// TODO: This should only require one Telemetry class.
		$this->container->singleton( CronJobContract::class, CronJob::class );
		$this->container->singleton( Request::class, TelemetrySendDataRequest::class );
		$this->container->singleton( DataProvider::class, DebugDataProvider::class );
	}

	public function test_cron_can_be_scheduled() {
		$cron = $this->container->get( CronJobContract::class );

		// Cron should not be scheduled by default
		$this->assertLessThanOrEqual( 0, $cron->is_scheduled() );

		// Schedule the cron
		$cron->schedule( time() );

		// Cron should be scheduled
		$this->assertGreaterThanOrEqual( 1, $cron->is_scheduled() );
	}

	public function test_cron_job_can_be_unscheduled() {
		$cron = $this->container->get( CronJobContract::class );

		// Schedule the cron
		$cron->schedule( time() );

		// Cron should be scheduled
		$this->assertGreaterThanOrEqual( 1, $cron->is_scheduled() );

		// Unschedule the cron
		$cron->unschedule();

		// Cron should not be scheduled
		$this->assertLessThanOrEqual( 0, $cron->is_scheduled() );
	}

	public function test_cron_interval_is_a_week_by_default() {
		$cron = $this->container->get( CronJobContract::class );

		$this->assertEquals( WEEK_IN_SECONDS, $cron->get_cron_interval() );
	}

	public function test_cron_registers_hook_using_admin_init() {
		$cron = $this->container->get( CronJobContract::class );

		// Cron hook should not be registered
		$this->assertFalse( has_action( $cron->get_cron_hook_name() ) );

		// Register the cron hook, this should be done during `admin_init`
		$cron->admin_init();

		// Cron hook should be registered
		$this->assertTrue( has_action( $cron->get_cron_hook_name() ) );
	}
}
