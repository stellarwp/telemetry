<?php
/**
 * Handles all tests related to event sending.
 */

use lucatume\WPBrowser\TestCase\WPTestCase;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Data_Providers\Null_Data_Provider;
use StellarWP\Telemetry\Events\Event;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Telemetry\Telemetry;
use StellarWP\Telemetry\Tests\Support\Traits\With_Test_Container;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;

class EventTest extends WPTestCase {

	use With_Test_Container;
	use With_Uopz;

	/**
	 * It should successfully send event.
	 *
	 * @test
	 */
	public function should_send_event(): void {
		$mock_response = [
			'headers'       => [],
			'body'          => json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
				[
					'status'  => 'true',
					'message' => 'success',
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
		$event         = new Event( $telemetry );

		$opt_in_status->set_status( true );

		$telemetry->save_token( 'abcd1234' );

		$event_data = [
			'token'        => 'abcd1234',
			'stellar_slug' => Config::get_stellar_slug(),
			'event'        => 'my-event',
			'event_data'   => wp_json_encode(
				[
					'some' => 'data',
				]
			),
		];

		$sent = $event->send( 'my-event', [ 'some' => 'data' ] );

		$this->assertEquals( Config::get_server_url() . '/events', $call_url );
		$this->assertSame( $event_data, $call_args['body'] );
		$this->assertTrue( $sent );
	}

	/**
	 * It should return false with failed event send.
	 *
	 * @test
	 */
	public function should_return_false_with_failed_send() {
		$mock_response = [
			'headers'       => [],
			'body'          => json_encode([]), // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
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
		$event         = new Event( $telemetry );

		$opt_in_status->set_status( true );

		$telemetry->save_token( 'abcd1234' );

		$event_data = [
			'token'        => 'abcd1234',
			'stellar_slug' => Config::get_stellar_slug(),
			'event'        => 'my-event',
			'event_data'   => wp_json_encode(
				[
					'some' => 'data',
				]
			),
		];

		$sent = $event->send( 'my-event', [ 'some' => 'data' ] );

		$this->assertEquals( Config::get_server_url() . '/events', $call_url );
		$this->assertSame( $event_data, $call_args['body'] );
		$this->assertFalse( $sent );
	}


	/**
	 * It should successfully send a batch of events.
	 *
	 * @test
	 */
	public function should_send_batched_events() {
		$mock_response = [
			'headers'       => [],
			'body'          => json_encode( // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
				[
					'status'  => 'true',
					'message' => 'success',
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
		$event         = new Event( $telemetry );

		$opt_in_status->set_status( true );

		$telemetry->save_token( 'abcd1234' );

		$events = [
			[
				'stellar_slug' => 'the-events-calendar',
				'event'        => 'event-created',
				'event_data'   => wp_json_encode(
					[
						'title' => 'Annual Golf Tournament',
					]
				),
			],
			[
				'stellar_slug' => 'give',
				'event'        => 'published-donation-form',
				'event_data'   => wp_json_encode(
					[
						'donation_amount' => '50000',
					]
				),
			]
		];

		$expected_args = [
			'token' => 'abcd1234',
			'events' => $events,
		];

		$sent = $event->send_batch( $events );

		$this->assertEquals( Config::get_server_url() . '/events', $call_url );
		$this->assertSame( $expected_args, $call_args['body'] );
		$this->assertTrue( $sent );
	}

	/**
	 * It should successfully send a batch of events.
	 *
	 * @test
	 */
	public function should_return_false_with_failed_batch_send() {
		$mock_response = [
			'headers'       => [],
			'body'          => json_encode([]), // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
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
		$event         = new Event( $telemetry );

		$opt_in_status->set_status( true );

		$telemetry->save_token( 'abcd1234' );

		$events = [
			[
				'stellar_slug' => 'the-events-calendar',
				'event'        => 'event-created',
				'event_data'   => wp_json_encode(
					[
						'title' => 'Annual Golf Tournament',
					]
				),
			],
			[
				'stellar_slug' => 'give',
				'event'        => 'published-donation-form',
				'event_data'   => wp_json_encode(
					[
						'donation_amount' => '50000',
					]
				),
			]
		];

		$sent = $event->send_batch( $events );

		$this->assertFalse( $sent );
	}
}
