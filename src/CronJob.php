<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Request;

class CronJob implements Contracts\CronJob {
	public const CRON_INTERVAL = WEEK_IN_SECONDS;
	public const OPTION_NAME   = 'stellarwp_telemetry_cron';

	/** @var OptInStatus */
	protected $optin_status;
	/** @var Request */
	protected $request;

	public function __construct( OptInStatus $optin_status, Request $request ) {
		$this->optin_status = $optin_status;
		$this->request = $request;
	}

	public function get_cron_interval(): int {
		return apply_filters( 'stellarwp_telemetry_cron_interval', self::CRON_INTERVAL );
	}

	public function get_cron_hook_name(): string {
		return apply_filters( 'stellarwp_telemetry_cron_hook_name', self::OPTION_NAME );
	}

	public function schedule( int $start ): void {
		as_schedule_recurring_action( $start, $this->get_cron_interval(), $this->get_cron_hook_name() );
	}

	public function maybe_schedule_cron_job(): void {
		if ( $this->optin_status->is_active() ) {
			if ( ! $this->is_scheduled() ) {
				$this->schedule( time() );
			}
		}
	}

	public function register_action(): void {
		add_action( $this->get_cron_hook_name(), function () {
			$this->run();
		}, 10, 0 );
	}

	public function is_scheduled(): int {
		return (int) as_next_scheduled_action( $this->get_cron_hook_name() );
	}

	public function unschedule(): void {
		as_unschedule_all_actions( $this->get_cron_hook_name() );
	}

	public function run(): void {
		// Send Telemetry Data
		$this->request->send();
	}
}