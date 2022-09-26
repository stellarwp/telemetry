<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {

	public function __construct( array $args = [] ) {
		$args = wp_parse_args( $args, [
			'activation_hook'      => new DefaultActivationHook(),
			'optin_template'       => new DefaultOptinTemplate(),
			'plugin_slug'          => 'stellarwp-telemetry-starter',
			'plugin_version'       => '1.0.0',
			'option_to_inherit'    => 'freemius_inherit',
			'create_settings_page' => true,
			'activation_redirect'  => 'options-general.php?page=stellarwp-telemetry-starter',
		] );

		$this->activation_hook      = $args['activation_hook'];
		$this->optin_template       = $args['optin_template'];
		$this->plugin_slug          = $args['plugin_slug'];
		$this->plugin_version       = $args['plugin_version'];
		$this->option_to_inherit    = $args['option_to_inherit'];
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
			$this->perform_activation_redirect();
		} );

		// Only run if we are in our plugin's settings page.
		if ( $this->is_settings_page() ) {
			if ( $this->should_show_optin() ) {
				// Run optin.
				add_action( 'admin_init', [ $this, 'run_optin' ] );
				// Apply enqueues.
				add_action( 'admin_init', [ $this, 'apply_enqueues' ] );
			}
		}

		// TODO: Add radio button setting in settings page to allow user to opt-out.

		// TODO: If optin status is true, add cronjob if not exists.

		// TODO: Add uninstall hook and remove cronjob if exists and option only has current plugin slug as false, or all are false.
	}

	public function render_settings_page() {
		echo "Hello World";
	}

}