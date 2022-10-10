<?php

namespace StellarWP\Telemetry;

class DefaultPluginStarter extends PluginStarter {

	public function __construct( array $args = [] ) {
		$args = wp_parse_args( $args, [
			'activation_hook'     => new DefaultActivationHook(),
			'optin_template'      => new DefaultOptinTemplate(),
			'plugin_slug'         => 'stellarwp-telemetry-starter',
			'plugin_version'      => '1.0.0',
			'activation_redirect' => 'options-general.php?page=stellarwp-telemetry-starter',
			'telemetry_url'       => getenv( 'STELLARWP_TELEMETRY_URL' ) ?: '',
		] );

		$this->activation_hook     = $args['activation_hook'];
		$this->optin_template      = $args['optin_template'];
		$this->plugin_slug         = $args['plugin_slug'];
		$this->plugin_version      = $args['plugin_version'];
		$this->activation_redirect = $args['activation_redirect'];
		$this->telemetry_url       = $args['telemetry_url'];

		$this->register_cronjob_handlers();

		add_action( 'admin_init', function () {
			// We don't want to run this on every ajax request.
			if ( wp_doing_ajax() ) {
				return;
			}

			// Run activation redirect.
			$this->perform_activation_redirect();

			// Add cronjob if it doesn't exist and opted in.
			$this->maybe_add_cronjobs();

			if ( $this->is_settings_page() ) {
				if ( $this->should_show_optin() ) {
					// Apply enqueues.
					$this->apply_enqueues();

					// Run optin.
					$this->run_optin();
				}
			}

			// Add pre update filters for the new options
			$this->add_pre_update_filters();
		} );

		// TODO: Add uninstall hook and remove cronjob if exists and option only has current plugin slug as false, or all are false.
	}

}