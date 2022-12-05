<?php
/**
 * Handles the Opt-in status for the site.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

/**
 * Class for handling the Opt-in status for the site.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Opt_In_Status {
	public const OPTION_NAME = 'stellarwp_telemetry';
	public const STATUS_ACTIVE = 1;
	public const STATUS_INACTIVE = 2;
	public const STATUS_MIXED = 3;

	/**
	 * Gets the option name used to store the opt-in status.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_option_name() {
		/**
		 * Filters the option name used to store the opt-in status.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $option_name
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'option_name', self::OPTION_NAME );
	}

	/**
	 * Gets the current opt-in status.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_option() {
		return get_option( $this->get_option_name(), [] );
	}

	/**
	 * Gets the current Opt-in status.
	 *
	 * The status is stored as an integer because there are multiple possible statuses:
	 * 1 = Active
	 * 2 = Inactive
	 * 3 = Mixed
	 *
	 * @since 1.0.0
	 *
	 * @return integer The status value.
	 */
	public function get() {
		$status = self::STATUS_ACTIVE;
		$option = $this->get_option();

		foreach ( $option as $plugin_slug => $plugin ) {
			if ( 'token' === $plugin_slug ) {
				continue;
			}

			// If a plugin's status is false, we set the status as inactive.
			if ( $plugin['optin'] === false ) {
				$status = self::STATUS_INACTIVE;
				continue;
			}

			// If another plugin's status is true and the status is already inactive, we set the status as mixed.
			if ( $plugin['optin'] === true && $status === self::STATUS_INACTIVE ) {
				$status = self::STATUS_MIXED;
				break;
			}
		}

		/**
		 * Filters the opt-in status value.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $status The opt-in status value.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'optin_status', $status );
	}

	/**
	 * Gets the site auth token.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_token() {
		$option = $this->get_option();

		/**
		 * Filters the site auth token.
		 *
		 * @since 1.0.0
		 *
		 * @param string $token The site's auth token.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'token', $option['token'] ?? '' );
	}

	/**
	 * Determines if the plugin slug exists in the opt-in option array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_slug
	 *
	 * @return boolean
	 */
	public function plugin_exists( string $plugin_slug ) {
		$option = $this->get_option();

		return array_key_exists( $plugin_slug, $option );
	}

	/**
	 * Adds a plugin slug to the opt-in option array.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $plugin_slug The slug to add to the option.
	 * @param boolean $status      The opt-in status for the plugin slug
	 *
	 * @return boolean
	 */
	public function add_plugin( string $plugin_slug, bool $status = false ) {
		$option = $this->get_option();

		$option[ $plugin_slug ] = [
			'optin' => $status,
		];

		return update_option( $this->get_option_name(), $option );
	}

	/**
	 * Removes a plugin slug from the opt-in option array.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $plugin_slug The slug to remove from the option.
	 *
	 * @return boolean
	 */
	public function remove_plugin( string $plugin_slug ) {
		$option = $this->get_option();

		// Bail early if the slug does not exist in the option.
		if ( ! isset( $option[ $plugin_slug ] ) ) {
			return false;
		}

		unset( $option[ $plugin_slug ] );

		return update_option( $this->get_option_name(), $option );
	}

	/**
	 * Get an array of opted-in plugins.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_opted_in_plugins() {
		$option = $this->get_option();
		$opted_in_plugins = [];

		foreach ( $option as $plugin_slug => $plugin ) {
			if ( 'token' === $plugin_slug ) {
				continue;
			}

			if ( $plugin['optin'] === true ) {
				$opted_in_plugins[] = $plugin_slug;
			}
		}

		return $opted_in_plugins;
	}

	/**
	 * Sets the opt-in status option for the site.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $status
	 *
	 * @return boolean
	 */
	public function set_status( bool $status ) {
		$option = $this->get_option();

		foreach ( $option as $plugin_slug => &$plugin ) {
			if ( 'token' === $plugin_slug ) {
				continue;
			}

			$plugin['optin'] = $status;
		}

		return update_option( $this->get_option_name(), $option );
	}

	/**
	 * Gets the site's opt-in status label.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_status() {
		switch ( $this->get() ) {
			case self::STATUS_ACTIVE:
				$optin_label = __( 'Active', 'stellarwp-telemetry-starter' );
				break;
			case self::STATUS_INACTIVE:
				$optin_label = __( 'Inactive', 'stellarwp-telemetry-starter' );
				break;
			case self::STATUS_MIXED:
				$optin_label = __( 'Mixed', 'stellarwp-telemetry-starter' );
				break;
		}

		/**
		 * Filters the opt-in status label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $optin-Label
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'optin_status_label', $optin_label );
	}

	/**
	 * Determines if the opt-in status is active.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_active(): bool {
		return $this->get() === self::STATUS_ACTIVE;
	}
}
