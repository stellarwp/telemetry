<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

class Config {

	protected static string $hookprefix = '';

	/**
	 * Gets the hook prefix.
	 *
	 * @return string
	 */
	public static function get_hook_prefix(): string {
		return static::$hookprefix;
	}

	/**
	 * Resets this class back to the defaults.
	 */
	public static function reset(): void {
		static::$hookprefix = '';
	}

	/**
	 * Sets the hook prefix.
	 *
	 * @param string $prefix
	 *
	 * @return void
	 */
	public static function set_hook_prefix( string $prefix ): void {
		static::$hookprefix = $prefix;
	}

}
