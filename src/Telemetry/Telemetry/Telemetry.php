<?php
/**
 * Handles all methods required for sending data to the telemetry server.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */

namespace StellarWP\Telemetry\Telemetry;

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Contracts\Data_Provider;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Opt_In\Status;

/**
 * Handles all methods required for sending data to the telemetry server.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry
 */
class Telemetry {
	public const AJAX_ACTION = 'stellarwp_telemetry_send_data';

	/**
	 * A data provider for gathering the data.
	 *
	 * @since 1.0.0
	 *
	 * @var Data_Provider
	 */
	protected $provider;

	/**
	 * The opt-in status object.
	 *
	 * @since 1.0.0
	 *
	 * @var Status
	 */
	protected $opt_in_status;

	/**
	 * The Telemetry constructor
	 *
	 * @param Data_Provider $provider      The provider that collects the site data.
	 * @param Status        $opt_in_status The opt-in status object.
	 */
	public function __construct( Data_Provider $provider, Status $opt_in_status ) {
		$this->provider      = $provider;
		$this->opt_in_status = $opt_in_status;
	}

	/**
	 * Registers the site with the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $force Force the creation of the site on the server.
	 *
	 * @return boolean
	 */
	public function register_site( bool $force = false ) {
		// If site is already registered and we're not forcing a new registration, bail.
		if ( $this->is_registered() ) {
			if ( false === $force ) {
				return false;
			}
		}

		$response = $this->send( $this->get_register_site_data(), $this->get_register_site_url() );

		return $this->save_token( $response['token'] ?? '' );
	}

	/**
	 * Registers the user with the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_user() {
		try {
			$this->send( $this->get_user_details(), Config::get_server_url() . '/opt-in' );
		} catch ( \Error $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We don't want to throw errors if the server fails.
		}
	}

	/**
	 * Sends the uninstall message to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_slug         The plugin slug.
	 * @param string $uninstall_reason_id The ID for the reason the plugin was deactivated.
	 * @param string $uninstall_reason    Why the user deactivated the plugin.
	 * @param string $comment             The additional comment from the text field shown with the uninstall reason.
	 *
	 * @return void
	 */
	public function send_uninstall( string $plugin_slug, string $uninstall_reason_id, string $uninstall_reason, string $comment = '' ) {
		$response = $this->send(
			[
				'access_token'        => $this->get_token(),
				'plugin_slug'         => $plugin_slug,
				'uninstall_reason_id' => $uninstall_reason_id,
				'uninstall_reason'    => $uninstall_reason,
				'comment'             => $comment,
			],
			$this->get_uninstall_url()
		);
	}

	/**
	 * Sends requests to the telemetry server and parses the response.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data The array of data to send.
	 * @param string $url  The url of the telemetry server.
	 *
	 * @return array|null
	 */
	protected function send( array $data, string $url ) {
		$response = $this->request( $url, $data );

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$response = $this->parse_response( $response );

		if ( empty( $response['status'] ) ) {
			return null;
		}

		return $response;
	}

	/**
	 * Actually sends the request to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  The url of the telemetry server.
	 * @param array  $data The data to send.
	 *
	 * @return array|\WP_Error
	 */
	protected function request( string $url, array $data ) {
		return wp_remote_post(
			$url,
			[
				'body' => $data,
			]
		);
	}

	/**
	 * Parses responses from wp_remote_requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $response The response from a request.
	 *
	 * @return array|null
	 */
	protected function parse_response( array $response ) {
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body, true );

		// If status is false, return null.
		if ( false === ( $data['status'] ?? false ) ) {
			return null;
		}

		return $data;
	}

	/**
	 * Gets the registered site url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_register_site_url() {
		$site_url = Config::get_server_url() . '/register-site';

		/**
		 * Filters the registered site url.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param string $site_url
		 */
		$site_url = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'register_site_url',
			(array) $site_url,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_url',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the registered site url.
		 *
		 * @since 1.0.7
		 *
		 * @param string $site_url
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_url',
			$site_url
		);
	}

	/**
	 * Gets the uninstall url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_uninstall_url() {
		$uninstall_url = Config::get_server_url() . '/uninstall';

		/**
		 * Filters the uninstall url.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param string $uninstall_url
		 */
		$uninstall_url = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'uninstall_url',
			(array) $uninstall_url,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/uninstall_url',
			'Replace missing `/` in handle'
		);


		/**
		 * Filters the uninstall url.
		 *
		 * @since 1.0.7
		 *
		 * @param string $uninstall_url
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/uninstall_url',
			$uninstall_url
		);
	}

	/**
	 * Gets the registered site data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_register_site_data() {
		$register_site_data = [
			'telemetry'     => wp_json_encode( $this->provider->get_data() ),
			'stellar_slugs' => wp_json_encode( $this->opt_in_status->get_opted_in_plugins() ),
		];

		/**
		 * Filters the register site data.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param array $register_site_data
		 */
		$register_site_data = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'register_site_data',
			(array) $register_site_data,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_data',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the register site data.
		 *
		 * @since 1.0.7
		 *
		 * @param array $register_site_data
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_data',
			$register_site_data
		);
	}

	/**
	 * Gets the current user's details.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_user_details() {
		$user      = wp_get_current_user();
		$user_info = [
			'name'        => $user->display_name,
			'email'       => $user->user_email,
			'plugin_slug' => Config::get_stellar_slug(),
		];

		/**
		 * Filters the site user details.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param array $site_user_details
		 */
		$user_info = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'register_site_user_details',
			(array) $user_info,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_user_details',
			'Replace missing `/` in handle'
		);

		/**
		 * Filters the site user details.
		 *
		 * @since 1.0.7
		 *
		 * @param array $site_user_details
		 */
		$user_info = apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/register_site_user_details',
			$user_info
		);

		return [ 'user' => wp_json_encode( $user_info ) ];
	}

	/**
	 * Gets the telemetry option.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_option() {
		return get_option( $this->opt_in_status->get_option_name(), [] );
	}

	/**
	 * Saves the telemetry server's auth token for the site.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token The site token to authenticate the request with.
	 *
	 * @return bool
	 */
	public function save_token( string $token ) {
		$option = array_merge(
			$this->get_option(),
			[
				'token' => $token,
			]
		);

		return update_option( $this->opt_in_status->get_option_name(), $option );
	}

	/**
	 * Determines if the current site is registered on the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_registered() {
		// Check if the site is registered by checking if the token is set.
		$option = $this->get_option();

		return ! empty( $option['token'] );
	}

	/**
	 * Sends data to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function send_data() {
		if ( ! $this->is_registered() ) {
			return false;
		}

		if ( ! $this->opt_in_status->is_active() ) {
			return false;
		}

		$response = $this->send( $this->get_send_data_args(), $this->get_send_data_url() );

		return $response['status'] ?? false;
	}

	/**
	 * Gets the args for sending data to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_send_data_args() {
		$send_data_args = [
			'token'         => $this->get_token(),
			'telemetry'     => wp_json_encode( $this->provider->get_data() ),
			'stellar_slugs' => wp_json_encode( $this->opt_in_status->get_opted_in_plugins() ),
		];

		/**
		 * Filter the args for sending data to the telemetry server.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param array $send_data_args
		 */
		$send_data_args = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'send_data_args',
			(array) $send_data_args,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/send_data_args',
			'Replace missing `/` in handle'
		);

		/**
		 * Filter the args for sending data to the telemetry server.
		 *
		 * @since 1.0.7
		 *
		 * @param array $send_data_args
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/send_data_args',
			$send_data_args
		);
	}

	/**
	 * Gets the URL for sending data to the telemetry server.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_send_data_url() {
		$data_url = Config::get_server_url() . '/telemetry';

		/**
		 * Filter the url for sending data to the telemetry server.
		 *
		 * @since 1.0.0
		 * @deprecated 1.0.7 Correct a typo in the handle.
		 *
		 * @param string $data_url
		 */
		$data_url = apply_filters_deprecated(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . 'send_data_url',
			(array) $data_url,
			'1.0.7',
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/send_data_url',
			'Replace missing `/` in handle'
		);

		/**
		 * Filter the url for sending data to the telemetry server.
		 *
		 * @since 1.0.7
		 *
		 * @param string $data_url
		 */
		return apply_filters(
			'stellarwp/telemetry/' . Config::get_hook_prefix() . '/send_data_url',
			$data_url
		);
	}

	/**
	 * Gets the stored auth token for the current site.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_token() {
		$option = $this->get_option();

		return $option['token'] ?? '';
	}

}
