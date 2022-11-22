<?php
/**
 * Handles all methods related to rendering the Opt-In template.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Template;

/**
 * Handles all methods related to rendering the Opt-In template.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Opt_In_Template implements Template {
	protected const YES = "1";
	protected const NO = "-1";

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function enqueue(): void {
		// TODO: Once FE template is done, enqueue it here.
	}

	/**
	 * Gets the arguments for configuring how the Opt-In modal is rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_args() {
		/**
		 * Filters the arguments for rendering the Opt-In modal.
		 *
		 * @since 1.0.0
		 *
		 * @param array $optin_args
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'optin_args', [
			'plugin_logo'        => Admin_Resources::get_asset_path() . 'resources/images/stellar-logo.svg',
			'plugin_logo_width'  => 151,
			'plugin_logo_height' => 32,
			'plugin_logo_alt'    => 'StellarWP Logo',
			'plugin_name'        => 'The Events Calendar',
			'user_name'          => wp_get_current_user()->display_name,
			'permissions_url'    => '#',
			'tos_url'            => '#',
			'privacy_url'        => '#',
		] );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		load_template( dirname( __DIR__ ) . '/views/optin.php', true, $this->get_args() );
	}

	/**
	 * Gets the option that determines if the modal should be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function get_option_name() {
		/**
		 * Filters if the Opt-In modal should be rendered.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $show_optin
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'show_optin_option_name', 'stellarwp_telemetry_show_optin' );
	}

	/**
	 * Helper function to determine if the modal should be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function should_render() {
		return (bool) get_option( $this->get_option_name(), false );
	}

	/**
	 * Renders the modal if it should be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_render() {
		if ( $this->should_render() ) {
			$this->render();
		}
	}
}
