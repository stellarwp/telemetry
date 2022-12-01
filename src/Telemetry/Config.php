<?php
/**
 * A helper class to provide configuration options for the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\ContainerContract\ContainerInterface;

/**
 * A configuration class to help set up the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Config {

	/**
	 * Container object.
	 *
	 * @since 1.0.0
	 *
	 * @var \StellarWP\ContainerContract\ContainerInterface
	 */
	protected static $container;

	/**
	 * Prefix for hook names.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $hook_prefix = '';

	/**
	 * The url of the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $server_url = 'https://telemetry.stellarwp.com/api/v1';

	/**
	 * Get the container.
	 *
	 * @since 1.0.0
	 *
	 * @throws \RuntimeException
	 *
	 * @return \StellarWP\ContainerContract\ContainerInterface
	 */
	public static function get_container() {
		if ( self::$container === null ) {
			throw new \RuntimeException( 'You must provide a container via StellarWP\Telemetry\Config::set_container() before attempting to fetch it.' );
		}

		return self::$container;
	}

	/**
	 * Gets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_hook_prefix() {
		return static::$hook_prefix;
	}

	/**
	 * Gets the telemetry server url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_server_url() {
		return static::$server_url;
	}

	/**
	 * Returns whether the container has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_container() {
		return self::$container !== null;
	}

	/**
	 * Resets this class back to the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset() {
		static::$hook_prefix = '';
		static::$server_url  = 'https://telemetry.stellarwp.com/api/v1';
	}

	/**
	 * Set the container object.
	 *
	 * @since 1.0.0
	 *
	 * @param \StellarWP\ContainerContract\ContainerInterface $container Container object.
	 *
	 * @return void
	 */
	public static function set_container( ContainerInterface $container ) {
		self::$container = $container;
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix
	 *
	 * @return void
	 */
	public static function set_hook_prefix( string $prefix ) {
		// Make sure the prefix always ends with a separator.
		if ( substr( $prefix, -1 ) !== '/' ) {
			$prefix = $prefix . '/';
		}

		static::$hook_prefix = $prefix;
	}

	/**
	 * Sets the telemetry server url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public static function set_server_url( string $url ) {
		static::$server_url = $url;
	}

}
