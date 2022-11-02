<?php

use Codeception\TestCase\WPTestCase;
use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\CronJob as CronJobContract;
use StellarWP\Telemetry\CronJob;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\Telemetry;

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

		$this->container = new Container();
		$this->container->singleton( Telemetry::class, function () {
			return new Telemetry( new DebugDataProvider(), 'stellarwp_telemetry' );
		} );
		$this->container->singleton( CronJobContract::class, function () {
			return new CronJob( $this->container->get( Telemetry::class ), __DIR__ . '/../../' );
		} );
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

	public function test_we_can_schedule_to_a_specific_timestamp() {
		$cron = $this->container->get( CronJobContract::class );

		// We can schedule to a specific timestamp
		$time = time() + 3600;
		$cron->schedule( $time );
		$this->assertTrue( $cron->is_scheduled( $time ) );
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

	public function test_cron_reschedules_itself_after_running() {
		$cron = $this->container->get( CronJobContract::class );

		// There should be no cron scheduled by default.
		$this->assertFalse( $cron->is_scheduled() );

		// Run the cron
		$cron->run();

		// Cron should be scheduled
		$this->assertTrue( $cron->is_scheduled() );
	}
}
