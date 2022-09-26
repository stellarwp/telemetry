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
			if ( $this->should_redirect_on_activation() &&
			     ! wp_doing_ajax() &&
			     ( intval( get_option( $this->get_redirection_option_name(), false ) ) === wp_get_current_user()->ID )
			) {
				delete_option( $this->get_redirection_option_name() );
				wp_safe_redirect( admin_url( $this->get_activation_redirect() ) );
				exit;
			}
		} );

		// Only run if we are in our plugin's settings page.
		if ( $this->is_settings_page() ) {
			// Run optin.
			add_action( 'admin_init', [ $this, 'run_optin' ] );
			// Apply enqueues.
			add_action( 'admin_init', [ $this, 'apply_enqueues' ] );
		}

		// TODO: If user is in the plugin settings page, check optin status. Hint: use the get_current_screen() function.

		// TODO: If optin status is false, run the optin template.

		// TODO: If optin status is true, add cronjob if not exists.

		// TODO: Add uninstall hook and remove cronjob if exists and option only has current plugin slug as false, or all are false.
	}

	public function render_settings_page() {
		echo "Hello World";
	}

}