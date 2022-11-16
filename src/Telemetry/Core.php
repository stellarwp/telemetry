<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\Cron_Job_Interface;
use StellarWP\Telemetry\Contracts\Data_Provider;

class Core {
	public const PLUGIN_SLUG     = 'plugin.slug';
	public const PLUGIN_BASENAME = 'plugin.basename';

	private array $subscribers = [
		Admin_Subscriber::class,
		Cron_Subscriber::class,
		Opt_In_Subscriber::class,
		Exit_Interview_Subscriber::class,
	];

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
		$container->bind( self::PLUGIN_BASENAME, plugin_basename( $plugin_path ) );
		$container->bind( Data_Provider::class, Debug_Data_Provider::class );
		$container->bind( Activation_Hook::class, static function () use ( $container ) {
			return new Activation_Hook( $container->get( Opt_In_Status::class ), $container );
		} );
		$container->bind( Activation_Redirect::class, static function () use ( $container ) {
			return new Activation_Redirect( $container->get( Activation_Hook::class ) );
		} );
		$container->bind( Cron_Job::class, static function () use ( $container, $plugin_path ) {
			return new Cron_Job( $container->get( Telemetry::class ), $plugin_path );
		} );
		$container->bind( Opt_In_Template::class, static function () use ( $container ) {
			return new Opt_In_Template();
		} );
		$container->bind( Exit_Interview_Template::class, static function () use ( $container ) {
			return new Exit_Interview_Template( $container );
		} );
		$container->bind( Telemetry::class, static function () use ( $container ) {
			return new Telemetry( $container->get( Data_Provider::class ), 'stellarwp_telemetry' );
		} );
		$container->bind( Admin_Resources::class, static function () {
			return new Admin_Resources();
		} );

		// Store the container for later use.
		$this->container = $container;

		foreach ( $this->subscribers as $subscriber_class ) {
			( new $subscriber_class( $this->container ) )->register();
		}
	}
}
