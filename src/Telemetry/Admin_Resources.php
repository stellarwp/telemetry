<?php

namespace StellarWP\Telemetry;

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
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
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
		$script_path = apply_filters( 'stellarwp/telemetry/script_path', $this->get_asset_path() . 'resources/js/scripts.js' );

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			$script_path,
			[ 'jquery' ],
			self::SCRIPT_VERSION,
			true
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
		$style_path = apply_filters( 'stellarwp/telemetry/style_path', $this->get_asset_path() . 'resources/css/styles.css' );

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
	static public function get_asset_path(): string {
		return plugin_dir_url( __DIR__ );
	}

}
