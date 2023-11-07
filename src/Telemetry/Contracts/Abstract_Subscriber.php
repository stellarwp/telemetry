<?php
/**
 * Handles setting up a base for all subscribers.
 *
 * @package StellarWP\Telemetry\Contracts
 */

namespace StellarWP\Telemetry\Contracts;

use StellarWP\ContainerContract\ContainerInterface;

/**
 * Class Abstract_Subscriber
 *
 * @package StellarWP\Telemetry\Contracts
 */
abstract class Abstract_Subscriber implements Subscriber_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor for the class.
	 *
	 * @param ContainerInterface $container The container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}
}
