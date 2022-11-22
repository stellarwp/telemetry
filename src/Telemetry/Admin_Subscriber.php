<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Admin_Subscriber extends Abstract_Subscriber {

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', [ $this, 'enqueue_admin_assets' ] );

	}

	/**
	 * Registers required hooks to set up the admin assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		$this->container->get( Admin_Resources::class )->admin_init();
	}

}
