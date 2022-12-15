<?php

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Opt_In\Opt_In_Template;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Tests\Container;

class Opt_In_TemplateTest extends \Codeception\TestCase\WPTestCase {

	public function _setUp() {
		parent::_setUp();

		Config::set_container( new Container() );
	}

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	// Tests
	public function test_it_returns_array_of_opted_in_plugin_names() {
		$opt_in_template = new Opt_In_Template( new Status() );
		$plugin_names    = [ 'Telemetry Starter', 'Learndash' ];
		$option          = [
			'token'   => '',
			'plugins' => [
				'telemetry-starter' => [
					'name'    => 'Telemetry Starter',
					'wp_slug' => 'telemetry-starter',
					'version' => '1.0.0',
					'optin'   => true,
				],
				'tec'               => [
					'name'    => 'The Events Calendar Pro',
					'wp_slug' => 'events-calendar-pro',
					'version' => '1.0.0',
					'optin'   => false,
				],
				'learndash'         => [
					'name'    => 'Learndash',
					'wp_slug' => 'sfwd-lms',
					'version' => '1.0.0',
					'optin'   => true,
				],
			],
		];

		update_option( 'stellarwp_telemetry', $option );

		$this->assertEquals( $plugin_names, $opt_in_template->get_opted_in_plugin_names() );
	}
}
