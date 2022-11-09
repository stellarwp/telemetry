<?php

namespace StellarWP\Telemetry;

class Core {
	public const YES = "1";
	public const NO = "-1";

	public string $plugin_slug;
	public string $plugin_version;

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
		$this->plugin_slug    = plugin_basename( $plugin_path );
		$this->plugin_version = $plugin_version;

		// We don't want to run this on every ajax request.
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( $this->is_settings_page() ) {
			if ( $this->should_show_optin() ) {
				// Run optin.
				(new OptInTemplate())->maybe_render();
			}
		}
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
		return apply_filters( 'stellarwp/telemetry/show_optin_option_name', ( new OptInStatus() )->get_option_name() . '_show_optin' );
	}
}
