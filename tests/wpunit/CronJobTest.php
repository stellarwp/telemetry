<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Contracts\CronJob as CronJobContract;
use StellarWP\Telemetry\CronJob;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\Telemetry;

class CronJobTest extends WPTestCase {
	/** @var CronJobContract */
	protected $cronjob;

	public function setUp(): void {
		// Before...
		parent::setUp();

		$this->cronjob = new CronJob(
			new Telemetry(
				new DebugDataProvider(),
				'stellarwp_telemetry'
			),
			__DIR__ . '/../../'
		);
	}

	public function test_cron_can_be_scheduled() {
		// Cron should not be scheduled by default
		$this->assertFalse( $this->cronjob->is_scheduled() );

		// Schedule the cron
		$this->cronjob->schedule( time() );

		// Cron should be scheduled
		$this->assertTrue( $this->cronjob->is_scheduled() );
	}

	public function test_we_can_schedule_to_a_specific_timestamp() {
		// We can schedule to a specific timestamp
		$time = time() + 3600;
		$this->cronjob->schedule( $time );
		$this->assertTrue( $this->cronjob->is_scheduled( $time ) );
	}

	public function test_cron_job_can_be_unscheduled() {
		// Schedule the cron
		$this->cronjob->schedule( time() );

		// Cron should be scheduled
		$this->assertGreaterThanOrEqual( 1, $this->cronjob->is_scheduled() );

		// Unschedule the cron
		$this->cronjob->unschedule();

		// Cron should not be scheduled
		$this->assertLessThanOrEqual( 0, $this->cronjob->is_scheduled() );
	}

	public function test_cron_interval_is_a_week_by_default() {
		$this->assertEquals( WEEK_IN_SECONDS, $this->cronjob->get_cron_interval() );
	}

	public function test_cron_registers_hook_using_admin_init() {
		// Cron hook should not be registered
		$this->assertFalse( has_action( $this->cronjob->get_cron_hook_name() ) );

		// Register the cron hook, this should be done during `admin_init`
		$this->cronjob->admin_init();

		// Cron hook should be registered
		$this->assertTrue( has_action( $this->cronjob->get_cron_hook_name() ) );
	}

	public function test_cron_reschedules_itself_after_running() {
		// There should be no cron scheduled by default.
		$this->assertFalse( $this->cronjob->is_scheduled() );

		// Run the cron
		$this->cronjob->run();

		// Cron should be scheduled
		$this->assertTrue( $this->cronjob->is_scheduled() );
	}
}
