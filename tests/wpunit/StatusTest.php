<?php
/**
 * Handles all tests related to the Status class.
 */

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Tests\Support\Traits\With_Test_Container;
use StellarWP\Telemetry\Tests\Support\Traits\With_Uopz;

class StatusTest extends WPTestCase {
	use With_Test_Container;
	use With_Uopz;

	public function get_option_data_provider(): array {
		return [
			'empty string'  => [ '', [] ],
			'null'          => [ null, [] ],
			'integer'       => [ 1, [] ],
			'boolean false' => [ false, [] ],
			'boolean true'  => [ true, [] ],
			'object'        => [ new stdClass(), [] ],
			'array'         => [ [ 'foo' => 'bar' ], [ 'foo' => 'bar' ] ],
		];
	}

	/**
	 * @dataProvider get_option_data_provider
	 */
	public function test_get_option( $bad_value, $expected ): void {
		$status = new Status();

		update_option( $status->get_option_name(), $bad_value );

		$this->assertIsArray( $status->get_option() );
		$this->assertEquals( $expected, $status->get_option() );
	}

	public function get_opted_in_plugins_data_provider(): array {
		return [
			'empty'                             => [ [], [] ],
			'missing plugins key'               => [ [ 'token' => 'foo' ], [] ],
			'empty plugins'                     => [ [ 'plugins' => [] ], [] ],
			'one plugin is missing wp_slug key' => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'optin' => true,
						],
					],
				],
				[
					[
						'slug'    => 'acme-commerce',
						'version' => '1.2.3',
					],
				],
			],
			'one plugin is missing opt-in key'  => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
						],
					],
				],
				[
					[
						'slug'    => 'acme-commerce',
						'version' => '1.2.3',
					],
				],
			],
			'not all plugins opt-in'            => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => false,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => true,
						],
					],
				],
				[
					[
						'slug'    => 'acme-commerce',
						'version' => '1.2.3',
					],
					[
						'slug'    => 'acme-learn',
						'version' => '5.6.7',
					],
				],
			],
			'all plugins opt-out'               => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => false,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => false,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => false,
						],
					],
				],
				[],
			],
			'all plugins opt-in'                => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => true,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => true,
						],
					],
				],
				[
					[
						'slug'    => 'acme-commerce',
						'version' => '1.2.3',
					],
					[
						'slug'    => 'acme-tickets',
						'version' => '3.4.5',
					],
					[
						'slug'    => 'acme-learn',
						'version' => '5.6.7',
					],
				],
			],
		];
	}

	/**
	 * @dataProvider get_opted_in_plugins_data_provider
	 */
	public function test_get_opted_in_plugins( $option_value, $expected ): void {
		$this->set_fn_return(
			'get_plugin_data',
			static function ( string $plugin ) {
				if ( strpos( $plugin, 'acme-commerce', true ) ) {
					return [
						'Name'    => 'Acme Commerce',
						'Version' => '1.2.3',
					];
				}

				if ( strpos( $plugin, 'acme-tickets', true ) ) {
					return [
						'Name'    => 'Acme Tickets',
						'Version' => '3.4.5',
					];
				}

				return [
					'Name'    => 'Acme Learn',
					'Version' => '5.6.7',
				];
			},
			true
		);
		$status = new Status();

		update_option( $status->get_option_name(), $option_value );

		$this->assertIsArray( $status->get_opted_in_plugins() );
		$this->assertEquals( $expected, $status->get_opted_in_plugins() );
	}

	public function get_status_data_provider(): array {
		return [
			'empty'                            => [ [], 2 ],
			'missing plugins key'              => [ [ 'token' => 'foo' ], 2 ],
			'empty plugins'                    => [ [ 'plugins' => [] ], 2 ],
			'one plugin is missing opt-in key' => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
						],
					],
				],
				2,
			],
			'not all plugins opt-in'           => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => false,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => true,
						],
					],
				],
				2,
			],
			'all plugins opt-out'              => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => false,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => false,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => false,
						],
					],
				],
				2,
			],
			'all plugins opt-in'               => [
				[
					'plugins' => [
						'acme-commerce' => [
							'wp_slug' => 'acme-commerce/acme-commerce.php',
							'optin'   => true,
						],
						'acme-tickets'  => [
							'wp_slug' => 'acme-tickets/acme-tickets.php',
							'optin'   => true,
						],
						'acme-learn'    => [
							'wp_slug' => 'acme-learn/acme-learn.php',
							'optin'   => true,
						],
					],
				],
				1,
			],
		];
	}

	/**
	 * @dataProvider get_status_data_provider
	 */
	public function test_get_status( $option_value, $expected ): void {
		$status = new Status();

		update_option( $status->get_option_name(), $option_value );

		$this->assertIsInt( $status->get() );
		$this->assertEquals( $expected, $status->get() );
	}

	/**
	 * @dataProvider get_status_data_provider
	 */
	public function test_get_status_label( $option_value, $expected ): void {
		$status = new Status();

		update_option( $status->get_option_name(), $option_value );

		$label = 1 === $expected ? 'Active' : 'Inactive';

		$this->assertEquals( $label, $status->get_status() );
	}

	/**
	 * @dataProvider get_status_data_provider
	 */
	public function test_is_active( $option_value, $expected ): void {
		$status = new Status();

		update_option( $status->get_option_name(), $option_value );

		$is_active = 1 === $expected ? true : false;

		$this->assertIsBool( $status->is_active() );
		$this->assertEquals( $is_active, $status->is_active() );
	}

	public function test_it_gets_saved_token() {
		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		$status = new Status();

		update_option( $status->get_option_name(), $option );

		$this->assertSame( $option['token'], $status->get_token() );
	}

	public function test_it_correctly_returns_if_plugin_exists() {
		$status = new Status();

		$this->assertFalse( $status->plugin_exists( 'some-nonexistent-slug' ) );

		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		update_option( $status->get_option_name(), $option );

		$this->assertTrue( $status->plugin_exists( 'tec' ) );
		$this->assertTrue( $status->plugin_exists( 'the-events-calendar' ) );
		$this->assertTrue( $status->plugin_exists( 'events-calendar-pro' ) );
	}

	public function test_it_adds_plugin_to_option() {
		$status = new Status();
		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		update_option( $status->get_option_name(), $option );

		$plugin_added = $status->add_plugin( 'give', true, 'give-wp' );

		// The option is updated (whether correctly or not).
		$this->assertTrue( $plugin_added );

		// Confirm that plugin now exists in the option.
		$this->assertTrue( $status->plugin_exists( 'give' ) );
	}

	public function test_it_adds_plugin_to_option_without_basename() {
		$status = new Status();
		Config::get_container()->bind( Core::PLUGIN_BASENAME, 'test-plugin/test-plugin.php' );
		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		update_option( $status->get_option_name(), $option );

		$plugin_added = $status->add_plugin( 'give', true );

		// The option is updated (whether correctly or not).
		$this->assertTrue( $plugin_added );

		// Confirm that plugin now exists in the option.
		$this->assertTrue( $status->plugin_exists( 'give' ) );
	}

	public function test_it_removes_plugin_from_option() {
		$status = new Status();
		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		update_option( $status->get_option_name(), $option );

		$plugin_removed = $status->remove_plugin( 'the-events-calendar' );

		// The option is updated (whether correctly or not).
		$this->assertTrue( $plugin_removed );

		// Confirm that plugin now exists in the option.
		$this->assertFalse( $status->plugin_exists( 'the-events-calendar' ) );
	}

	public function test_it_returns_false_if_plugin_not_in_option() {
		$status = new Status();
		$option = [
			'plugins' =>
				[
					'tec'                 =>
						[
							'optin' => true,
						],
					'the-events-calendar' =>
						[
							'wp_slug' => 'the-events-calendar/the-events-calendar.php',
							'optin'   => false,
						],
					'events-calendar-pro' =>
						[
							'wp_slug' => 'events-pro/events-calendar-pro.php',
							'optin'   => false,
						],
				],
			'token'   => 'c9d509e2920d32684b62d91ff48c186386e1321ebca6c9a8b9693037c8451f7b',
		];

		update_option( $status->get_option_name(), $option );

		$plugin_removed = $status->remove_plugin( 'plugin_that_does_not_exist' );

		$this->assertFalse( $plugin_removed );
	}
}
