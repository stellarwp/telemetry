<?php

namespace StellarWP\Telemetry;

use WP_Debug_Data;

class DefaultTelemetryProvider implements TelemetryProvider {
	public function get_data(): array {
		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		}
		$info = WP_Debug_Data::debug_data();

		$active_plugins = get_option('active_plugins');
		$plugins        = get_plugins();
		$active         = [];

		foreach ( $active_plugins as $active_plugin ){
			if ( isset( $plugins[ $active_plugin ] ) ) {
				$active[ $active_plugin ] = $plugins[ $active_plugin ];
				unset( $plugins[ $active_plugin ] );
			}
		}
		$info['telemetry-active-plugins']['fields'] = $active;
		$info['telemetry-inactive-plugins']['fields'] = $plugins;

		return apply_filters( 'stellarwp_telemetry_data', $info );
	}
}