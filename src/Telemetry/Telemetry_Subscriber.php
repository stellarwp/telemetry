<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Telemetry_Subscriber extends Abstract_Subscriber {

    public function register() {
		add_action( 'shutdown', [ $this, 'send_telemetry_data' ] );
	}

	public function send_telemetry_data() {
		global $wpdb;


		$sql = $wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
			'stellarwp_telemetry_telemetry_testing_last_send'
		);

		$timestamp = $wpdb->get_var( $sql );
	}

}
