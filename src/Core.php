<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\DataProvider;

class Core {
	public const PLUGIN_SLUG = 'plugin_slug';
	public const YES = "1";
	public const NO = "-1";

	public string $plugin_slug;
	public string $plugin_version;

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
	 * @param string $plugin_version The current version of the plugin.
	 */
	public function init( string $plugin_path, string $plugin_version ): void {
		$this->plugin_slug    = dirname( plugin_basename( $plugin_path ) );
		$this->plugin_version = $plugin_version;

		$container = new Container();
		$container->bind( self::PLUGIN_SLUG, $this->plugin_slug );
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

		$this->container = $container;
	}

	/**
	 * @return lucatume\DI52\Container
	 */
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

	/**
	 * Determines if the optin modal should be shown to the user.
	 */
	public function should_show_optin(): bool {
		$should_show = (bool) get_option( $this->get_show_optin_option_name(), false );

		if ( $should_show ) {
			// Update the option so we don't show the optin again unless something changes this again.
			update_option( $this->get_show_optin_option_name(), self::NO );
		}

		$should_show = ( $should_show === self::YES );

		return apply_filters( 'stellarwp/telemetry/should_show_optin', $should_show );
	}

	/**
	 * Gets the optin option name.
	 */
	public function get_show_optin_option_name(): string {
		return apply_filters( 'stellarwp/telemetry/show_optin_option_name', $this->container->get( OptInStatus::class )->get_option_name() . '_show_optin' );
	}
}
