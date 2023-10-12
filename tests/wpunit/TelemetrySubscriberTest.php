<?php
/**
 * Tests all methods within the Telemetry Subscriber class.
 */

use StellarWP\Telemetry\Config;
use lucatume\WPBrowser\TestCase\WPTestCase;
use StellarWP\Telemetry\Telemetry\Telemetry;
use StellarWP\Telemetry\Telemetry\Telemetry_Subscriber;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;
use StellarWP\Telemetry\Tests\Support\Traits\With_Test_Container;

class TelemetrySubscriberTest extends WPTestCase {

	use With_Uopz;
	use With_Test_Container;

	/**
	 * @dataProvider subscriber_hooks_data_provider
	 */
	public function test_register_adds_necessary_hooks( $hook, $method) {
		$subscriber = new Telemetry_Subscriber( Config::get_container() );

		$hooks = [];

		$this->set_fn_return( 'add_action', function( $hook_name, $function_to_add, $priority = 10, $accepted_args = 0 ) use ( &$hooks ) {
			$hooks[ $hook_name ] = $function_to_add;
		}, true );

		$subscriber->register();

		$this->assertArrayHasKey( $hook, $hooks );
		$this->assertInstanceOf( $method[0], $hooks[$hook][0] );
		$this->assertSame( $method[1], $hooks[$hook][1] );
	}

	public function test_send_async_request() {
		$mock_response = [
			'headers'       => [],
			'body'          => json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
				[
					'status' => 'success',
					'token'  => '1234567890',
				]
			),
			'response'      => [
				'code'    => false,
				'message' => false,
			],
			'cookies'       => [],
			'http_response' => null,
		];

		$call_url  = '';
		$call_args = [];

		$this->set_fn_return( 'wp_remote_request', function( $url, $args ) use ( $mock_response, &$call_url, &$call_args ) {
			$call_url  = $url;
			$call_args = $args;

			return $mock_response;
		}, true );

		$subscriber = new Telemetry_Subscriber( Config::get_container() );

		$subscriber->send_async_request();
	}

	public function subscriber_hooks_data_provider() {
		return [
			[ 'shutdown', [ Telemetry_Subscriber::class, 'send_async_request'] ],
			[ 'wp_ajax_' . Telemetry::AJAX_ACTION, [ Telemetry_Subscriber::class, 'send_telemetry_data'] ],
			[ 'wp_ajax_nopriv_' . Telemetry::AJAX_ACTION, [ Telemetry_Subscriber::class, 'send_telemetry_data'] ],
		];
	}



}
