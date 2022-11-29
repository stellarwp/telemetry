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
use StellarWP\ContainerContract\ContainerInterface;

/**
 * Handles all methods for determining when to send telemetry data.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Last_Send {

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
