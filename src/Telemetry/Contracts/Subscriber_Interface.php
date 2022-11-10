<?php declare(strict_types=1);

namespace StellarWP\Telemetry\Contracts;

interface Subscriber_Interface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register(): void;

}
