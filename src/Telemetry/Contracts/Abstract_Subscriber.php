<?php declare(strict_types=1);

namespace StellarWP\Telemetry\Contracts;

use StellarWP\ContainerContract\ContainerInterface;

abstract class Abstract_Subscriber implements Subscriber_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Abstract_Subscriber constructor.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

}
