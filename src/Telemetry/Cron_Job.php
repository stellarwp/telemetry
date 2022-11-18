<?php
/**
 * Provides a collection of methods for setting up cron jobs.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Cron_Job_Interface;

/**
 * A class that sets up the necessary cron jobs for periodically
 * sending site data to the Telemetry server.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Cron_Job implements Cron_Job_Interface {
	public const CRON_INTERVAL = WEEK_IN_SECONDS;
	public const ACTION_NAME   = 'stellarwp_telemetry_cron';

	/**
	 * A Telemetry Instance
	 *
	 * @since 1.0.0
	 *
	 * @var \StellarWP\Telemetry\Telemetry
	 */
	protected $telemetry;

	/**
	 * The class constructor.
	 *
	 * @param \StellarWP\Telemetry\Telemetry $telemetry
	 * @param string $plugin_path The path of the main plugin file.
	 *
	 * @return void
	 */
	public function __construct( Telemetry $telemetry, string $plugin_path ) {
		$this->telemetry = $telemetry;

		// Load ActionScheduler
		require_once trailingslashit( dirname( $plugin_path ) ) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';
	}

	/**
	 * Gets the interval in seconds that should be used when sending site health data.
	 *
	 * @since 1.0.0
	 *
	 * @return integer
	 */
	public function get_cron_interval() {
		/**
		 * Filters the cron interval to use when sending site health data.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $interval The cron interval in seconds.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'cron_interval', self::CRON_INTERVAL );
	}

	/**
	 * Gets the cron hook name that should be used when the cron is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the cron hook.
	 */
	public function get_cron_hook_name() {
		/**
		 * Filters the name of the cron hook to use when the cron is registered.
		 *
		 * @since 1.0.0
		 *
		 * @param string $hook_name The name of the hook.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'cron_hook_name', self::ACTION_NAME );
	}

	/**
	 * Schedules the Action Scheduler task to run.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $start The timestamp (in seconds) to start the task at.
	 *
	 * @return void
	 */
	public function schedule( int $start ) {
		if ( $this->is_scheduled() ) {
			return;
		}

		as_schedule_recurring_action( $start, $this->get_cron_interval(), $this->get_cron_hook_name() );
	}

	/**
	 * Registers the cron action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_action() {
		add_action( $this->get_cron_hook_name(), function () {
			$this->run();
		}, 10, 0 );
	}

	/**
	 * Checks if Action Scheduler has a scheduled action for the cron hook.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $time The timestamp (in seconds) to use when checking Action Scheduler's actions.
	 *
	 * @return bool
	 */
	public function is_scheduled( int $time = 0 ) {
		if ( $time > 0 ) {
			return as_next_scheduled_action( $this->get_cron_hook_name() ) === $time;
		}

		return as_has_scheduled_action( $this->get_cron_hook_name() );
	}

	/**
	 * Unschedules all actions with the cron hook name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function unschedule() {
		as_unschedule_all_actions( $this->get_cron_hook_name() );
	}

	/**
	 * Sends the site health data to the telemetry server and reschedules the next send.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->telemetry->send_data();

		// Reschedule the cron.
		$this->schedule( time() + $this->get_cron_interval() );
	}

	/**
	 * Registers the action with Action Scheduler.
	 *
	 * @since 1.0.0
	 *
	 * @uses 'admin_init'
	 *
	 * @return void
	 */
	public function admin_init(): void {
		$this->register_action();
	}

}
