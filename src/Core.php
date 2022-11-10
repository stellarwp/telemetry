<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\DataProvider;

class Core {
	public const PLUGIN_SLUG = 'plugin.slug';
	public const YES = "1";
	public const NO = "-1";

	private Container $container;

	private static self $instance;

	/**
	 * @return self
	 */
	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Sets up the library.
	 *
	 * @param string $plugin_path    The path to the main plugin file.
	 */
	public function init( string $plugin_path ): void {
		$this->init_container( $plugin_path );
	}

	public function container() {
		return $this->container;
	}

	/**
	 * Gets the plugin's version.
	 */
	public function get_plugin_version(): string {
		return $this->plugin_version;
	}

	/**
	 * Gets the plugin's slug.
	 */
	public function get_plugin_slug(): string {
		return $this->plugin_slug;
	}

	/**
	 * Determines if the current page is the plugin's main settings page.
	 */
	public function is_settings_page(): bool {
		$is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] === $this->get_plugin_slug() );

		return apply_filters( 'stellarwp/telemetry/is_settings_page', $is_settings_page );
	}

	private function init_container( string $plugin_path ): void {
		$container = new Container();
		$container->bind( self::PLUGIN_SLUG, dirname( plugin_basename( $plugin_path ) ) );
		$container->bind( DataProvider::class, DebugDataProvider::class );
		$container->bind( ActivationHook::class, static function () use ( $container ) {
			return new ActivationHook( $container->get( OptInStatus::class ), $container );
		} );
		$container->bind( ActivationRedirect::class, static function () use ( $container ) {
			return new ActivationRedirect( $container->get( ActivationHook::class ) );
		} );
		$container->bind( CronJobContract::class, static function () use ( $container ) {
			return new CronJob( $container->get( Telemetry::class ), __DIR__ );
		} );
		$container->bind( OptInTemplate::class, static function () use ( $container ) {
			return new OptInTemplate();
		} );
		$container->bind( Telemetry::class, static function () use ( $container ) {
			return new Telemetry( $container->get( DataProvider::class ), 'stellarwp_telemetry' );
		} );

		// Store the container for later use.
		$this->container = $container;
	}
}
