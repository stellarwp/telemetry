<?php
/**
 * A collection of methods to enqueue and localize admin assets.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry;

/**
 * A class that enqueues and localizes admin assets.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Admin_Resources {

	const SCRIPT_HANDLE  = 'stellarwp-telemetry-admin';
	const SCRIPT_VERSION = '1.0.0';

	/**
	 * Enqueues the admin resources.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'localize_script' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	/**
	 * Enqueues the admin JS script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		/**
		 * Filters the path to the admin JS script.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path The path to the admin JS script.
		 */
		$script_path = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'script_path', $this->get_asset_path() . 'resources/js/scripts.js' );

		wp_enqueue_script(
			Config::get_hook_prefix() . '/' . self::SCRIPT_HANDLE,
			$script_path,
			[ 'jquery' ],
			self::SCRIPT_VERSION,
			true
		);
	}

	/**
	 * Localizes the admin JS script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localize_script() {
		/**
		 * Filters the data that is passed to the admin JS script.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data The data to pass to the script.
		 */
		$script_data = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'script_data', [
			Config::get_hook_prefix() . '_exit_interview' => [
				'action' => Exit_Interview_Subscriber::get_ajax_action(),
				'nonce'  => wp_create_nonce( Exit_Interview_Subscriber::get_ajax_action() ),
			],
		] );

		wp_localize_script(
			Config::get_hook_prefix() . '/' . self::SCRIPT_HANDLE,
			'stellarwpTelemetry',
			$script_data
		);
	}

	/**
	 * Enqueues the admin CSS styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		/**
		 * Filters the path to the admin CSS styles.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path The path to the CSS file.
		 */
		$style_path = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'style_path', $this->get_asset_path() . 'resources/css/styles.css' );

		wp_enqueue_style(
			Config::get_hook_prefix() . '/' . self::SCRIPT_HANDLE,
			$style_path,
			[],
			self::SCRIPT_VERSION
		);
	}

	/**
	 * Gets the path to the assets folder.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	static public function get_asset_path(): string {
		return plugin_dir_url( __DIR__ );
	}

}
