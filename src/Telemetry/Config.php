<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\ContainerContract\ContainerInterface;

class Config {

	/**
	 * Container object.
	 *
	 * @var ContainerInterface
	 */
	protected static $container;

	/**
	 * Prefix for hook names.
	 *
	 * @var string
	 */
	protected static string $hookprefix = '';

	/**
	 * Get the container.
	 *
	 * @return ContainerInterface
	 */
	public static function get_container() : ContainerInterface {
		if ( self::$container === null ) {
			throw new \RuntimeException( 'You must provide a container via StellarWP\Schema\Config::set_container() before attempting to fetch it.' );
		}

		return self::$container;
	}

	/**
	 * Gets the hook prefix.
	 *
	 * @return string
	 */
	public static function get_hook_prefix(): string {
		return static::$hookprefix;
	}

	/**
	 * Returns whether the container has been set.
	 *
	 * @return bool
	 */
	public static function has_container() : bool {
		return self::$container !== null;
	}

	/**
	 * Resets this class back to the defaults.
	 */
	public static function reset(): void {
		static::$hookprefix = '';
	}

	/**
	 * Set the container object.
	 *
	 * @param ContainerInterface $container Container object.
	 */
	public static function set_container( ContainerInterface $container ) {
		self::$container = $container;
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @param string $prefix
	 *
	 * @return void
	 */
	public static function set_hook_prefix( string $prefix ): void {
		// Make sure the prefix always ends with a separator.
		if ( substr( $prefix, -1 ) !== '_' ) {
			$prefix = $prefix . '_';
		}

		static::$hookprefix = $prefix;
	}

}
