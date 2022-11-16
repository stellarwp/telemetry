<?php

namespace StellarWP\Telemetry;

class Admin_Resources {

	const SCRIPT_HANDLE  = 'stellarwp-telemetry-admin';
	const SCRIPT_VERSION = '1.0.0';

	public function enqueue() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

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

	public function enqueue_styles() {
		$style_path = apply_filters( 'stellarwp/telemetry/style_path', $this->get_asset_path() . 'resources/css/styles.css' );

		wp_enqueue_style(
			self::SCRIPT_HANDLE,
			$style_path,
			[],
			self::SCRIPT_VERSION
		);
	}

	static public function get_asset_path() {
		return plugin_dir_url( __DIR__ );
	}

}
