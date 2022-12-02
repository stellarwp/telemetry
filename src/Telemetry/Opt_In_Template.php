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
	 * The opt-in status object.
	 *
	 * @since 1.0.0
	 *
	 * @var StellarWP\Telemetry\Opt_In_Status
	 */
	protected $opt_in_status;

	/**
	 * The Telemetry constructor
	 *
	 * @param Opt_In_Status $opt_in_status The opt-in status object.
	 */
	public function __construct( Opt_In_Status $opt_in_status ) {
		$this->opt_in_status = $opt_in_status;
	}

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

		$optin_args = [
			'plugin_logo'           => Admin_Resources::get_asset_path() . 'resources/images/stellar-logo.svg',
			'plugin_logo_width'     => 151,
			'plugin_logo_height'    => 32,
			'plugin_logo_alt'       => 'StellarWP Logo',
			'plugin_name'           => 'The Events Calendar',
			'plugin_slug'           => Config::get_container()->get( Core::PLUGIN_SLUG ),
			'user_name'             => wp_get_current_user()->display_name,
			'permissions_url'       => '#',
			'tos_url'               => '#',
			'privacy_url'           => '#',
			'opted_in_plugins_text' => __( 'See which plugins you have opted in to tracking for', 'stellarwp-telemetry' ),
		];

		$optin_args['opted_in_plugins'] = array_map(
			function( $plugin ) {
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin . '/' . $plugin . '.php' );
				return $plugin_data['Name'] ?? $plugin;
			},
			$this->opt_in_status->get_opted_in_plugins()
		);

		$optin_args['heading'] = sprintf( __( 'We hope you love %s.', 'stellarwp-telemetry' ), $optin_args['plugin_name'] );
		$optin_args['intro']   = sprintf(
			__(
				'Hi, %s.! This is an invitation to help our StellarWP community.
				If you opt-in, some data about your usage of %s and future StellarWP Products will be shared with our teams (so they can work their butts off to improve).
				We will also share some helpful info on WordPress, and our products from time to time.
				And if you skip this, that’s okay! Our products still work just fine.',
			'stellarwp-telemetry' ),
			$optin_args['user_name'],
			$optin_args['plugin_name']
		);

		/**
		 * Filters the arguments for rendering the Opt-In modal.
		 *
		 * @since 1.0.0
		 *
		 * @param array $optin_args
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'optin_args', $optin_args );
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
	 * @return string
	 */
	public function get_option_name() {
		$plugin_slug = Config::get_container()->get( Core::PLUGIN_SLUG );

		/**
		 * Filters if the Opt-In modal should be rendered.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $show_optin
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'show_optin_option_name',
			'stellarwp_telemetry_' . $plugin_slug . '_show_optin'
		);
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
