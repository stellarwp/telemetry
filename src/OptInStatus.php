<?php

namespace StellarWP\Telemetry;

class OptInStatus {
	public const OPTION_NAME = 'stellarwp_telemetry';
	public const STATUS_ACTIVE = 1;
	public const STATUS_INACTIVE = 2;
	public const STATUS_MIXED = 3;

	public function get_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_option_name', self::OPTION_NAME );
	}

	public function get_option(): array {
		return get_option( $this->get_option_name(), [] );
	}

	public function get(): int {
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

		return apply_filters( 'stellarwp_telemetry_optin_status', $status );
	}

	public function get_token(): string {
		$option = $this->get_option();

		return apply_filters( 'stellarwp_telemetry_token', $option['token'] ?? '' );
	}

	public function plugin_exists( string $plugin_slug ): bool {
		$option = $this->get_option();

		return ! array_key_exists( $plugin_slug, $option );
	}

	public function add_plugin( string $plugin_slug, bool $status = false ): bool {
		$option = $this->get_option();

		$option[ $plugin_slug ] = [
			'optin' => $status,
		];

		return update_option( $this->get_option_name(), $option );
	}

	public function set_status( bool $status ): bool {
		$option = $this->get_option();

		foreach ( $option as $plugin_slug => &$plugin ) {
			if ( 'token' === $plugin_slug ) {
				continue;
			}

			$plugin['optin'] = $status;
		}

		return update_option( $this->get_option_name(), $option );
	}

	public function get_status(): string {
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

		return apply_filters( 'stellarwp_telemetry_optin_status_label', $optin_label );
	}

	public function is_active(): bool {
		return $this->get() === self::STATUS_ACTIVE;
	}
}