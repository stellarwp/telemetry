<?php

namespace StellarWP\Telemetry;

class DefaultTelemetryProvider implements TelemetryProvider {
	public function get_data(): array {
		$site_health = \WP_Site_Health::get_instance();
		$tests       = \WP_Site_Health::get_tests();
		$data        = [];

		// TODO: Copy the code from the site health class to get the localized script data.

		return apply_filters( 'stellarwp_telemetry_data', $data );
	}
}