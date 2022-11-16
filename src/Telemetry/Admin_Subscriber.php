<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Admin_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'admin_init', [ $this, 'set_optin_status' ] );
		add_action( 'admin_init', [ $this, 'register_cron' ] );
		add_action( 'admin_init', [ $this, 'set_cron_schedule' ] );
	}

	public function register_cron() {
		// Register cronjob hook.
		$this->container->get( Cron_Job::class )->admin_init();
	}

	public function set_cron_schedule() {
		// Cronjob schedule.
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
				// If site is not registered, register it and schedule the cronjob for a 5 minutes from now (for testing).
				$telemetry->register_site();
				$cronjob->schedule( time() + ( 5 * MINUTE_IN_SECONDS ) );
			}
		} else {
			// If optin is not active, unschedule the cronjob.
			$cronjob->unschedule();
		}
	}
}
