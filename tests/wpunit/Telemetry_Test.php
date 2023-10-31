<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Data_Providers\Null_Data_Provider;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Telemetry\Telemetry;
use StellarWP\Telemetry\Tests\Support\Traits\With_Test_Container;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;

class Telemetry_Test extends WPTestCase {
	use With_Test_Container;
	use With_Uopz;

	/**
	 * It should register site with blocking request
	 *
	 * @test
	 */
	public function should_register_site_with_blocking_request(): void {
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
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->register_site();

		$this->assertEquals( Config::get_server_url() . '/register-site', $call_url );
		$this->assertArrayHasKey( 'blocking', $call_args );
		$this->assertTrue( $call_args['blocking'] );
		$this->assertEquals( '1234567890', $telemetry->get_token() );
	}

	/**
	 * It should not save token if site registration fails
	 *
	 * @test
	 */
	public function should_not_save_token_if_site_registration_fails(): void {
		$mock_response = new \WP_Error( 'for reasons' );
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->register_site();

		$this->assertEmpty( $telemetry->get_token() );
	}

	/**
	 * It should register user with non-blocking request
	 *
	 * @test
	 */
	public function should_register_user_with_non_blocking_request(): void {
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
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->register_user();

		$this->assertEquals( Config::get_server_url() . '/opt-in', $call_url );
		$this->assertArrayHasKey( 'blocking', $call_args );
		$this->assertFalse( $call_args['blocking'] );
	}

	/**
	 * It should send_uninstall with non-blocking request
	 *
	 * @test
	 */
	public function should_send_uninstall_with_non_blocking_request(): void {
		$mock_response = [
			'headers'       => [],
			'body'          => '',
			'response'      => [
				'code'    => false,
				'message' => false,
			],
			'cookies'       => [],
			'http_response' => null,
		];
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->send_uninstall( 'acme-tickets', 'reasons', 'For reasons' );

		$this->assertEquals( Config::get_server_url() . '/uninstall', $call_url );
		$this->assertArrayHasKey( 'blocking', $call_args );
		$this->assertFalse( $call_args['blocking'] );
	}

	/**
	 * It should send data with blocking request
	 *
	 * @test
	 */
	public function should_send_data_with_blocking_request(): void {
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
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$status    = new Status();
		$telemetry = new Telemetry( new Null_Data_Provider(), $status );

		$status->set_status( true );

		$telemetry->save_token( '2389' );

		$sent = $telemetry->send_data();

		$this->assertEquals( Config::get_server_url() . '/telemetry', $call_url );
		$this->assertArrayHasKey( 'blocking', $call_args );
		$this->assertTrue( $call_args['blocking'] );
		$this->assertTrue( $sent );
	}

	/**
	 * It should not send data when site has not opted in (not authorized).
	 *
	 * @test
	 */
	public function should_not_send_data_without_authorization(): void {
		$status    = new Status();
		$telemetry = new Telemetry( new Null_Data_Provider(), $status );

		$status->set_status( false );

		$telemetry->save_token( 'abc1234' );

		$sent = $telemetry->send_data();

		$this->assertFalse( $sent );
	}

	/**
	 * It should include the opt-in user when sending data.
	 *
	 * @test
	 */
	public function should_send_data_with_optin_user(): void {
		wp_set_current_user( 1 );

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
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->register_user();
		$telemetry->save_token( '2389' );
		$sent = $telemetry->send_data();

		$this->assertEquals( Config::get_server_url() . '/telemetry', $call_url );
		$this->assertArrayHasKey( 'opt_in_user', json_decode( $call_args['body']['telemetry'], true ) );
		$this->assertTrue( $sent );
	}

	/**
	 * It should include the opt-in user when sending data.
	 *
	 * @test
	 */
	public function should_send_data_without_optin_user_if_missing(): void {
		wp_set_current_user( 1 );

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
		$call_url      = null;
		$call_args     = null;
		$this->set_fn_return(
			'wp_remote_post',
			static function ( string $url, array $args ) use ( $mock_response, &$call_url, &$call_args ) {
				$call_url  = $url;
				$call_args = $args;

				return $mock_response;
			},
			true
		);

		$opt_in_status = new Status();
		$telemetry     = new Telemetry( new Null_Data_Provider(), $opt_in_status );

		$opt_in_status->set_status( true );

		$telemetry->register_user();
		$telemetry->save_token( '2389' );

		delete_option( Status::OPTION_NAME_USER_INFO );

		$sent = $telemetry->send_data();

		$this->assertEquals( Config::get_server_url() . '/telemetry', $call_url );
		$this->assertArrayNotHasKey( 'opt_in_user', json_decode( $call_args['body']['telemetry'], true ) );
		$this->assertTrue( $sent );
	}
}
