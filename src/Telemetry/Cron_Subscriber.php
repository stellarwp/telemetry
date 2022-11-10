<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\AbstractSubscriber;

class Cron_Subscriber extends AbstractSubscriber {

	public function register(): void {
		add_action( 'admin_init', function () {
			$this->container->get( Cron_Job::class )->admin_init();
		} );
	}

}
