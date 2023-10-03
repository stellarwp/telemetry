<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Admin\Resources;
use StellarWP\Telemetry\Opt_In\Opt_In_Template;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Tests\Support\Traits\With_Test_Container;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;

class TemplateTest extends WPTestCase {

	use With_Test_Container;
	use With_Uopz;

	public function get_default_template_data() {
		return [
			'plugin_logo'           => Resources::get_asset_path() . 'resources/images/stellar-logo.svg',
			'plugin_logo_width'     => 151,
			'plugin_logo_height'    => 32,
			'plugin_logo_alt'       => 'StellarWP Logo',
			'plugin_name'           => 'StellarWP',
			'plugin_slug'           => 'telemetry-library',
			'user_name'             => 'admin',
			'permissions_url'       => '#',
			'tos_url'               => '#',
			'privacy_url'           => 'https://stellarwp.com/privacy-policy/',
			'opted_in_plugins_text' => 'See which plugins you have opted in to tracking for',
			'opted_in_plugins'      => [],
			'heading'               => 'We hope you love StellarWP.',
			'intro'                 => 'Hi, admin! This is an invitation to help our StellarWP community.
				If you opt-in, some data about your usage of StellarWP and future StellarWP Products will be shared with our teams (so they can work their butts off to improve).
				We will also share some helpful info on WordPress, and our products from time to time.
				And if you skip this, that’s okay! Our products still work just fine.',
		];
	}

	public function test_should_return_basic_defaults() {
		$status = new Status();

		$this->set_fn_return(
			'wp_get_current_user',
			static function () {
				return new WP_User( 1, 'admin' );
			},
			true
		);

		update_option(
			$status->get_option_name(),
			[
				'plugins' => [
					'the-events-calendar' => [
						'wp_slug' => 'the-events-calendar/the-events-calendar.php',
						'optin'   => false,
					],
				],
				'token'   => 'abcd1234',
			]
		);

		$expected = $this->get_default_template_data();
		$actual   = ( new Opt_In_Template( $status ) )->get_args( 'telemetry-library' );

		$this->assertIsArray( $actual );
		$this->assertEquals( $expected, $actual );
	}

	public function test_get_intro() {
		$expected = $this->get_default_template_data();
		$actual   = ( new Opt_In_Template( new Status() ) )->get_intro( 'admin', 'StellarWP' );

		$this->assertIsString( $actual );
		$this->assertEquals( $expected['intro'], $actual );
	}
}
