<?php
/**
 * An interface that provides an API for all cron jobs.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry\Contracts
 */
namespace StellarWP\Telemetry\Contracts;

/**
 * Interface that provides an API for all cron jobs.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry\Contracts
 */
interface Cron_Job_Interface {
	/**
	 * Determines if a specific cron job has been scheduled.
	 *
	 * @return boolean
	 */
	public function is_scheduled();

	/**
	 * Schedules a cron job.
	 *
	 * @param integer $start The timestamp to start the job.
	 *
	 * @return void
	 */
	public function schedule( int $start );

	/**
	 * Unschedules a cron job.
	 *
	 * @return void
	 */
	public function unschedule();

	/**
	 * Runs the current cron job.
	 *
	 * @return void
	 */
	public function run();
}
