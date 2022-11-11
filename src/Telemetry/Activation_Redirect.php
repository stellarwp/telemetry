<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Redirection;

class Activation_Redirect implements Redirection {
	public const REDIRECTION_URL = 'options-general.php?page=stellarwp-telemetry-starter';

	/**
	 * @var Activation_Hook
	 */
	protected $activation_hook;

	public function __construct( Activation_Hook $activation_hook ) {
		$this->activation_hook = $activation_hook;
	}

	public function trigger(): void {
		$this->remove_activation_redirect_option();

		exit( wp_safe_redirect( admin_url( $this->get_url() ) ) );
	}

	public function get_url(): string {
		return apply_filters( 'stellarwp/telemetry/activation_redirect', self::REDIRECTION_URL );
	}

	protected function remove_activation_redirect_option(): void {
		delete_option( $this->activation_hook->get_redirection_option_name() );
	}

	public function should_trigger(): bool {
		if ( ! wp_doing_ajax() &&
		     ( intval( get_option( $this->activation_hook->get_redirection_option_name(), false ) ) === wp_get_current_user()->ID )
		) {
			return true;
		}

		return false;
	}
}
