<?php
/**
 * A collection of methods to enqueue and localize admin assets.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Admin;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Exit_Interview\Exit_Interview_Subscriber;

/**
 * A class that enqueues and localizes admin assets.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Resources {

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
		$script_path = $this->get_asset_path() . 'resources/js/scripts.js';

		/**
		 * Filters the path to the admin JS script.
		 *
		 * @since 1.0.0
		 * @deprecated TBD Correct a typo in the handle.
		 *
		 * @param string $script_path The path to the admin JS script.
		 */
		$script_path = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'script_path',
			$script_path,
			'TBD',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/script_path',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the path to the admin JS script.
		 *
		 * @since TBD
		 *
		 * @param string $script_path The path to the admin JS script.
		 */
		$script_path = apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/script_path',
			$script_path
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
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
		$script_data = [
			'exit_interview' => [
				'action' => Exit_Interview_Subscriber::AJAX_ACTION,
				'nonce'  => wp_create_nonce( Exit_Interview_Subscriber::AJAX_ACTION ),
			],
		];

		/**
		 * Filters the data that is passed to the admin JS script.
		 *
		 * @since 1.0.0
		 * @deprecated TBD Correct a typo in the handle.
		 *
		 * @param array $script_data The data to pass to the script.
		 */
		$script_data = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'script_data',
			$script_data,
			'TBD',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/script_data',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the data that is passed to the admin JS script.
		 *
		 * @since TBD
		 *
		 * @param array $script_data The data to pass to the script.
		 */
		$script_data = apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/script_data',
			$script_data
		);

		wp_localize_script(
			self::SCRIPT_HANDLE,
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
		$style_path = $this->get_asset_path() . 'resources/css/styles.css';

		/**
		 * Filters the path to the admin CSS styles.
		 *
		 * @since 1.0.0
		 * @deprecated TBD Correct a typo in the handle.
		 *
		 * @param string $style_path The path to the CSS file.
		 */
		$style_path = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'style_path',
			$style_path,
			'TBD',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/style_path',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the path to the admin CSS styles.
		 *
		 * @since TBD
		 *
		 * @param string $style_path The path to the CSS file.
		 */
		$style_path = apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/style_path',
			$style_path
		);

		wp_enqueue_style(
			self::SCRIPT_HANDLE,
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
	public static function get_asset_path(): string {
		return plugin_dir_url( dirname( __DIR__ ) );
	}

}
