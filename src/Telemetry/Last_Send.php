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

	public const OPTION_NAME = 'stellarwp_telemetry_last_send';

	/**
	 * Initially sets the _last_send option in the options table.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function initialize_option() {
		if ( get_option( self::OPTION_NAME ) === false ) {
			update_option( self::OPTION_NAME, '' );
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

		// No timestamp exists, we'll assume that telemetry data needs to be sent.
		if ( $last_send === '' ) {
			return true;
		}

		$timestamp   = new DateTimeImmutable( $last_send );
		$now         = new DateTimeImmutable();
		$interval    = date_diff( $timestamp, $now );

		/**
		 * Filters the amount of seconds the last send timestamp is valid before it expires.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $expire_days
		 */
		$expire_days = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'last_send_expire_seconds', 7 * DAY_IN_SECONDS );

		return $interval->s >= $expire_days;
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
		$option_name       = self::OPTION_NAME;
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
			self::OPTION_NAME
		);

		return $wpdb->get_var( $sql ) ?? '';
	}

}
