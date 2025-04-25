<?php
/**
 * Handles all tests related to the Config class.
 */
use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Last_Send\Last_Send;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;

class LastSendTest extends WPTestCase {

	use With_Uopz;

	public function test_it_initializes_option() {
		$last_send = new Last_Send();

		$option = get_option( Last_Send::OPTION_NAME );

		$this->assertFalse( $option );

		$last_send->initialize_option();

		$option = get_option( Last_Send::OPTION_NAME );

		$this->assertIsString( $option );
	}

	public function test_is_expired_with_empty_value() {

		$last_send = new Last_Send();

		update_option( Last_Send::OPTION_NAME, '' );

		$this->assertTrue( $last_send->is_expired() );
	}

	public function test_is_expired_with_past_timestamp() {

		$last_send = new Last_Send();

		update_option( Last_Send::OPTION_NAME, DateTimeImmutable::createFromFormat( 'Y-m-d', '2020-01-01' )->format( 'Y-m-d H:i:s' ) );

		$this->assertTrue( $last_send->is_expired() );

	}

	public function test_set_new_timestamp() {

		$last_send = new Last_Send();
		$time = new DateTimeImmutable();

		$last_send->initialize_option();
		$result = $last_send->set_new_timestamp( $time );

		$this->assertIsInt( $result );
		$this->assertSame( 1, $result );

		$actual = $last_send->get_timestamp();

		$this->assertSame( $time->format( 'Y-m-d H:i:s' ), $actual );

	}

	public function test_set_new_timestamp_returns_on_false_result() {
		$last_send = new Last_Send();

		$this->set_class_fn_return( wpdb::class, 'update', function() {
			return false;
		}, true);

		$time = new DateTimeImmutable();
		$actual = $last_send->set_new_timestamp( $time );

		$this->assertSame( 0, $actual );
	}
}
