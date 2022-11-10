<?php declare(strict_types=1);

namespace StellarWP\Telemetry\Contracts;

interface SubscriberInterface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register(): void;

}
