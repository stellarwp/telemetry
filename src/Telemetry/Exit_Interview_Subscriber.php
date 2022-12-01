<?php
/**
 * A class that handles displaying an "Exit Interview" for users deactivating the plugin.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * A class that handles displaying an "Exit Interview" for users deactivating the plugin.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Exit_Interview_Subscriber extends Abstract_Subscriber {

	const AJAX_ACTION = 'exit-interview';

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_footer', [ $this, 'render_exit_interview' ] );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_exit_interview' ] );

		add_filter( 'network_admin_plugin_action_links_' . $this->container->get( Core::PLUGIN_BASENAME ), [ $this, 'plugin_action_links' ], 10, 1 );
		add_filter( 'plugin_action_links_' . $this->container->get( Core::PLUGIN_BASENAME ), [ $this, 'plugin_action_links' ], 10, 1 );
	}

	/**
	 * Possibly renders the exit interview if the user is on the plugins list page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_exit_interview() {
		global $pagenow;

		if ( $pagenow === 'plugins.php' ) {
			$this->container->get( Exit_Interview_Template::class )->maybe_render();
		}
	}

	/**
	 * Handles the ajax request for submitting "Exit Interivew" form data.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function ajax_exit_interview() {
		$uninstall_reason_id = filter_input( INPUT_POST, 'uninstall_reason_id', FILTER_SANITIZE_STRING );
		$uninstall_reason_id = ! empty( $uninstall_reason_id ) ? $uninstall_reason_id : false;
		if ( ! $uninstall_reason_id ) {
			wp_send_json_error( 'No reason id provided' );
		}

		$uninstall_reason = filter_input( INPUT_POST, 'uninstall_reason', FILTER_SANITIZE_STRING );
		$uninstall_reason = ! empty( $uninstall_reason ) ? $uninstall_reason : false;
		if ( ! $uninstall_reason ) {
			wp_send_json_error( 'No reason provided' );
		}

		$plugin_slug = filter_input( INPUT_POST, 'plugin_slug', FILTER_SANITIZE_STRING );

		$comment = filter_input( INPUT_POST, 'comment', FILTER_SANITIZE_STRING );
		$comment = ! empty( $comment ) ? $comment : '';

		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		$nonce = ! empty( $nonce ) ? $nonce : '';

		if ( ! wp_verify_nonce( $nonce, self::AJAX_ACTION ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		$telemetry = $this->container->get( Telemetry::class );
		$telemetry->send_uninstall( $plugin_slug, $uninstall_reason_id, $uninstall_reason, $comment );

		wp_send_json_success();
	}

	/**
	 * Adds an <i> element after the "deactivate" link on the plugin list table so that it can be targeted by JS to trigger the interview modal.
	 *
	 * The deactivation is deferred to the modal displayed.
	 *
	 * @param array $links
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
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
			$deactivate_link .= '<i class="telemetry-plugin-slug" data-plugin-slug="' . $this->container->get( Core::PLUGIN_SLUG ) . '"></i>';

			// Append deactivation link.
			$before_deactivate['deactivate'] = $deactivate_link;
		}

		return array_merge( $before_deactivate, $after_deactivate );
	}

}
