<?php declare(strict_types=1);

namespace StellarWP\Telemetry\Contracts;

use lucatume\DI52\Container;

abstract class Abstract_Subscriber implements Subscriber_Interface {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Abstract_Subscriber constructor.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

}
