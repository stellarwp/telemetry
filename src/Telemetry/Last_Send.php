<?php
/**
 * Handles all methods for determining when to send telemetry data.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use DateTimeImmutable;

/**
 * Handles all methods for determining when to send telemetry data.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Last_Send {

	/**
	 * Initially sets the _last_send option in the options table.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function initialize_option() {
		if ( get_option( $this->get_option_name() ) === false ) {
			update_option( $this->get_option_name(), '' );
		}
	}

	/**
	 * Checks whether the last send timestamp is expired or not.
	 *
	 * If the timestamp is >= 1 week, the last send is expired.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_expired() {

		$last_send = $this->get_timestamp();

		// No timestamp exists.
		if ( $last_send === '' ) {
			return false;
		}

		$timestamp   = new DateTimeImmutable( $last_send );
		$now         = new DateTimeImmutable();
		$interval    = date_diff( $timestamp, $now );

		/**
		 * Filters the amount of days the last send timestamp is valid before it expires.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $expire_days
		 */
		$expire_days = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'last_send_expire_days', 7 );

		return $interval->days >= $expire_days;
	}

	/**
	 * Sets a new timestamp for the last_send option.
	 *
	 * @param DateTimeImmutable $time
	 *
	 * @return int Number of rows affected.
	 */
	public function set_new_timestamp( DateTimeImmutable $time ) {
		global $wpdb;

		$timestamp         = $time->format( "Y-m-d H:i:s" );
		$option_name       = $this->get_option_name();
		$current_timestamp = $this->get_timestamp();

		/**
		 * Update the timestamp and use the current timestamp to make sure it
		 * is only updated a single time.
		 */
		$result = $wpdb->update(
			$wpdb->options,
			[
				'option_name'  => $option_name,
				'option_value' => $timestamp,
			],
			[
				'option_name'  => $option_name,
				'option_value' => $current_timestamp,
			]
		);

		return $result ?: 0;
	}

	/**
	 * Gets the key used to store the timestamp of the last time data was sent.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_option_name() {
		$plugin_slug = Config::get_container()->get( Core::PLUGIN_SLUG );

		return 'stellarwp_telemetry_' . $plugin_slug . '_last_send';
	}

	/**
	 * Queries the database directly to get the timestamp.
	 *
	 * This avoids any filters being applied than are necessary.
	 *
	 * @since 1.0.0
	 *
	 * @return string The timestamp of the last send.
	 */
	private function get_timestamp() {
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
			$this->get_option_name()
		);

		return $wpdb->get_var( $sql ) ?? '';
	}

}
