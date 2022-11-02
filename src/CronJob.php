<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Request;

class CronJob implements Contracts\CronJob {
	public const CRON_INTERVAL = WEEK_IN_SECONDS;
	public const ACTION_NAME   = 'stellarwp_telemetry_cron';

	/** @var Request */
	protected $request;

	public function __construct( Request $request ) {
		// TODO: Change to Telemetry.
		$this->request = $request;
	}

	public function get_cron_interval(): int {
		return apply_filters( 'stellarwp_telemetry_cron_interval', self::CRON_INTERVAL );
	}

	public function get_cron_hook_name(): string {
		return apply_filters( 'stellarwp_telemetry_cron_hook_name', self::ACTION_NAME );
	}

	public function schedule( int $start ): void {
		as_schedule_recurring_action( $start, $this->get_cron_interval(), $this->get_cron_hook_name() );
	}

	public function maybe_schedule( int $start ): void {
		if ( ! $this->is_scheduled() ) {
			$this->schedule( $start );
		}
	}

	protected function register_action(): void {
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
		$this->request->send();
	}

	public function admin_init(): void {
		$this->register_action();
	}

}