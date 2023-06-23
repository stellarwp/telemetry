<?php declare(strict_types=1);

namespace StellarWP\TelemetryLibraryTesting;

use lucatume\DI52\Container as DI52Container;
use StellarWP\ContainerContract\ContainerInterface;

class Container implements ContainerInterface {

	protected DI52Container $container;

	/**
	 * Container constructor.
	 */
	public function __construct() {
		$this->container = new DI52Container();
	}

	/**
	 * @inheritDoc
	 */
	public function bind( string $id, $implementation = null, ?array $afterBuildMethods = null ) {
		$this->container->bind( $id, $implementation, $afterBuildMethods );
	}

	/**
	 * @inheritDoc
	 */
	public function get( string $id ) {
		return $this->container->get( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function has( string $id ) {
		return $this->container->has( $id );
	}

	/**
	 * @inheritDoc
	 */
	public function singleton( string $id, $implementation = null, ?array $afterBuildMethods = null ) {
		$this->container->singleton( $id, $implementation, $afterBuildMethods );
	}

	/**
	 * Defer all other calls to the container object.
	 */
	public function __call( string $name, mixed $args ): mixed {
		return $this->container->{$name}( ...$args );
	}

}
