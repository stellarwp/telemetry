<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Cron_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'admin_init', function () {
			$this->container->get( Cron_Job::class )->admin_init();
		} );
	}

}
