<?php
/**
 * Handles all hooks/filters related to the cron.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Handles all hooks/filters related to the cron.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Cron_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'register_cron' ] );
		add_action( 'admin_init', [ $this, 'set_cron_schedule' ] );
	}

	/**
	 * Registers required hooks to set up the cron.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_cron() {
		$this->container->get( Cron_Job::class )->admin_init();
	}

	/**
	 * Schedules the cron if the current site is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_cron_schedule() {
		$cron_job  = $this->container->get( Cron_Job::class );
		$optin     = $this->container->get( Opt_In_Status::class );
		$telemetry = $this->container->get( Telemetry::class );

		// Don't set the cron if the optin is not active.
		if ( ! $optin->is_active() ) {

			// User is not opted in, remove any existing scheduled jobs.
			if ( $cron_job->is_scheduled() ) {
				$cron_job->unschedule();
			}

			return;
		}

		// If the site is unregistered, we don't have authority to send data.
		if ( ! $telemetry->is_registered() ) {
			return;
		}

		// If site is registered, schedule it immediately.
		$cron_job->schedule( time() );
	}
}
