<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\AbstractSubscriber;

class CronSubscriber extends AbstractSubscriber {

	public function register(): void {
		add_action( 'admin_init', function () {
			$this->container->get( CronJob::class )->admin_init();
		} );
	}

}
