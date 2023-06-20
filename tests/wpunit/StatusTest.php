<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core;
use StellarWP\Telemetry\Opt_In\Status;
use StellarWP\Telemetry\Tests\Container;

class StatusTest extends WPTestCase {
	private $uopz_set_fn_returns = [];

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
		$good = [
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
						'version' => '1.2.3'
					]
				]
			],
			'one plugin is missing opt-in key'   => [
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
						'version' => '1.2.3'
					]
				]
			],
			'not all plugins opt-in'             => [
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
						'version' => '1.2.3'
					],
					[
						'slug'    => 'acme-learn',
						'version' => '5.6.7'
					]
				],
			],
			'all plugins opt-out' => [
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
			'all plugins opt-in' => [
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
						'version' => '1.2.3'
					],
					[
						'slug'    => 'acme-tickets',
						'version' => '3.4.5'
					],
					[
						'slug'    => 'acme-learn',
						'version' => '5.6.7'
					]
				],
			],
		];
	}

	/**
	 * @dataProvider get_opted_in_plugins_data_provider
	 */
	public function test_get_opted_in_plugins( $option_value, $expected ): void {
		$this->uopz_set_fn_returns[] = 'get_pougin_data';
		uopz_set_return( 'get_plugin_data', static function ( string $plugin ) {
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
		}, true );
		$test_container = new Container();
		$test_container->setVar( Core::SITE_PLUGIN_DIR, '/app/public/wp-content/plugins/' );
		Config::set_container( $test_container );
		$status = new Status();

		update_option( $status->get_option_name(), $option_value );

		$this->assertIsArray( $status->get_opted_in_plugins() );
		$this->assertEquals( $expected, $status->get_opted_in_plugins() );
	}

	/**
	 * @before
	 * @after
	 */
	public function reset_config(): void {
		Config::reset();
	}

	/**
	 * @after
	 */
	public function cleanup_uopz(): void {
		foreach ( $this->uopz_set_fn_returns as $fn ) {
			uopz_unset_return( $fn );
		}
		$this->uopzuopz_set_fn_returns = [];
	}
}
