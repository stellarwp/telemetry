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
		$optin     = $this->container->get( Opt_In_Status::class );
		$telemetry = $this->container->get( Telemetry::class );
		$cronjob   = $this->container->get( Cron_Job::class );

		if ( $optin->is_active() ) {
			if ( $telemetry->is_registered() ) {
				if ( ! $cronjob->is_scheduled() ) {
					// If site is registered, but cronjob is not scheduled, schedule it immediately.
					$cronjob->schedule( time() );
				}
			} else {
				// If site is not registered, register it and schedule the cron.
				$telemetry->register_site();
				$cronjob->schedule( time() + ( 5 * MINUTE_IN_SECONDS ) );
			}
		} else {
			// If optin is not active, unschedule the cronjob.
			$cronjob->unschedule();
		}
	}

}
