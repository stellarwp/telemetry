<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {

	public function __construct( array $args = [] ) {
		$args = wp_parse_args( $args, [
			'activation_hook'      => new DefaultActivationHook(),
			'optin_template'       => new DefaultOptinTemplate(),
			'plugin_slug'          => 'stellarwp-telemetry-starter',
			'plugin_version'       => '1.0.0',
			'inherit_option'       => 'freemius_inherit',
			'create_settings_page' => true,
			'activation_redirect'  => 'options-general.php?page=stellarwp-telemetry-starter',
		] );

		$this->activation_hook      = $args['activation_hook'];
		$this->optin_template       = $args['optin_template'];
		$this->plugin_slug          = $args['plugin_slug'];
		$this->plugin_version       = $args['plugin_version'];
		$this->inherit_option       = $args['inherit_option'];
		$this->create_settings_page = $args['create_settings_page'];
		$this->activation_redirect  = $args['activation_redirect'];

		// Create settings page.
		if ( $this->create_settings_page === true ) {
			add_action( 'admin_menu', function () {
				add_submenu_page(
					'options-general.php',
					__( 'StellarWP Telemetry', 'stellarwp-telemetry-starter' ),
					__( 'StellarWP Telemetry', 'stellarwp-telemetry-starter' ),
					'manage_options',
					'stellarwp-telemetry-starter',
					[ $this, 'render_settings_page' ]
				);
			} );
		}

		// Run activation redirect.
		add_action( 'admin_init', function () {
			if ( ! wp_doing_ajax() && intval( get_option( 'stellarwp_telemetry_redirection', false ) ) === wp_get_current_user()->ID ) {
				delete_option( 'stellarwp_telemetry_redirection' );
				wp_safe_redirect( admin_url( $this->get_activation_redirect() ) );
				exit;
			}
		} );
	}

	public function render_settings_page() {
		echo "Hello World";
	}

}