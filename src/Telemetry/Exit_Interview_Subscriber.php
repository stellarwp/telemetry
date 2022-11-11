<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Exit_Interview_Subscriber extends Abstract_Subscriber {

	const AJAX_ACTION = 'exit-interview';

	public function register(): void {
		add_action( 'admin_footer', [ $this, 'render_exit_interview' ] );
		add_filter( 'plugin_action_links_' . $this->container->get( Core::PLUGIN_BASENAME ), [ $this, 'plugin_action_links' ], 10, 1 );

		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_exit_interview' ] );
	}

	public function render_exit_interview() {
		global $pagenow;

		if ( $pagenow === 'plugins.php' ) {
			$this->container->get( Exit_Interview_Template::class )->maybe_render();
		}
	}

	public function plugin_action_links( $links ) {
		$passed_deactivate = false;
		$deactivate_link   = '';
		$before_deactivate = [];
		$after_deactivate  = [];

		foreach ( $links as $key => $link ) {
			if ( 'deactivate' === $key ) {
				$deactivate_link   = $link;
				$passed_deactivate = true;
				continue;
			}

			if ( ! $passed_deactivate ) {
				$before_deactivate[ $key ] = $link;
			} else {
				$after_deactivate[ $key ] = $link;
			}
		}

		if ( ! empty( $deactivate_link ) ) {
			$deactivate_link .= '<i class="telemetry-plugin-id" data-module-id="' . $this->container->get( Core::PLUGIN_SLUG ) . '"></i>';

			// Append deactivation link.
			$before_deactivate['deactivate'] = $deactivate_link;
		}

		return array_merge( $before_deactivate, $after_deactivate );
	}

	public function ajax_exit_interview() {
		$uninstall_reason = filter_input( INPUT_POST, 'uninstall_reason', FILTER_SANITIZE_STRING );
		$uninstall_reason = ! empty( $uninstall_reason ) ? $uninstall_reason : false;

		if ( ! $uninstall_reason ) {
			wp_send_json_error( 'No reason provided' );
		}

		$comment = filter_input( INPUT_POST, 'comment', FILTER_SANITIZE_STRING );
		$comment = ! empty( $comment ) ? $comment : '';

		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		$nonce = ! empty( $nonce ) ? $nonce : '';

		if ( ! wp_verify_nonce( $nonce, self::AJAX_ACTION ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		// Show errors
		ini_set( 'display_errors', '1' );
		ini_set( 'display_startup_errors', '1' );
		error_reporting( E_ALL );

		$telemetry = $this->container->get( Telemetry::class );
		$telemetry->send_uninstall( $uninstall_reason, $this->container->get( Core::PLUGIN_SLUG ), $comment );

		wp_send_json_success();
	}

}
