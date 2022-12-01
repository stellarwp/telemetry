<?php
/**
 * Handles setting up the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\ContainerContract\ContainerInterface;
use StellarWP\Telemetry\Contracts\Data_Provider;

/**
 * The core class of the library.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Core {
	public const PLUGIN_SLUG     = 'plugin.slug';
	public const PLUGIN_BASENAME = 'plugin.basename';

	/**
	 * The subscriber class names that should be registered in the container.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	private array $subscribers = [
		Exit_Interview_Subscriber::class,
		Last_Send_Subscriber::class,
		Opt_In_Subscriber::class,
		Telemetry_Subscriber::class,
		Admin_Subscriber::class,
	];

	/**
	 * The container that should be used for loading library resources.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * The current instance of the library.
	 *
	 * @since 1.0.0
	 *
	 * @var self
	 */
	private static self $instance;

	/**
	 * Returns the current instance or creates one to return.
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initializes the library.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_path The path to the main plugin file.
	 *
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	public function init( string $plugin_path ) {
		if ( ! Config::has_container() ) {
			throw new \RuntimeException( 'You must call StellarWP\Telemetry\Config::set_container() before calling StellarWP\Telemetry::init().' );
		}

		$this->init_container( $plugin_path );
	}

	/**
	 * Gets the container.
	 *
	 * @since 1.0.0
	 *
	 * @return \StellarWP\ContainerContract\ContainerInterface
	 */
	public function container() {
		return $this->container;
	}

	/**
	 * Gets the plugin's slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_slug() {
		return $this->container->get( self::PLUGIN_SLUG );
	}

	/**
	 * Initializes the container with library resources.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_path The path of the plugin.
	 *
	 * @return void
	 */
	private function init_container( string $plugin_path ): void {
		$container = Config::get_container();

		$container->bind( self::PLUGIN_SLUG, dirname( plugin_basename( $plugin_path ) ) );
		$container->bind( self::PLUGIN_BASENAME, plugin_basename( $plugin_path ) );
		$container->bind( Data_Provider::class, Debug_Data_Provider::class );
		$container->bind( Opt_In_Template::class, static function () {
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
