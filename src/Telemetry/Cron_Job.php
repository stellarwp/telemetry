<?php

namespace StellarWP\Telemetry;

class Cron_Job implements Contracts\Cron_Job {
	public const CRON_INTERVAL = WEEK_IN_SECONDS;
	public const ACTION_NAME   = 'stellarwp_telemetry_cron';

	/** @var Telemetry */
	protected $telemetry;

	public function __construct( Telemetry $telemetry, string $plugin_path ) {
		$this->telemetry = $telemetry;

		// Load ActionScheduler
		require_once trailingslashit( $plugin_path ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
	}

	public function get_cron_interval(): int {
		return apply_filters( 'stellarwp/telemetry/cron_interval', self::CRON_INTERVAL );
	}

	public function get_cron_hook_name(): string {
		return apply_filters( 'stellarwp/telemetry/cron_hook_name', self::ACTION_NAME );
	}

	public function schedule( int $start ): void {
		if ( $this->is_scheduled() ) {
			return;
		}

		as_schedule_recurring_action( $start, $this->get_cron_interval(), $this->get_cron_hook_name() );
	}

	protected function register_action(): void {
		add_action( $this->get_cron_hook_name(), function () {
			$this->run();
		}, 10, 0 );
	}

	public function is_scheduled( int $time = 0 ): bool {
		if ( $time > 0 ) {
			return as_next_scheduled_action( $this->get_cron_hook_name() ) === $time;
		}

		return as_has_scheduled_action( $this->get_cron_hook_name() );
	}

	public function unschedule(): void {
		as_unschedule_all_actions( $this->get_cron_hook_name() );
	}

	public function run(): void {
		$this->telemetry->send_data();

		// Reschedule the cron.
		$this->schedule( time() + $this->get_cron_interval() );
	}

	public function admin_init(): void {
		$this->register_action();
	}

}
