<?php

use Codeception\TestCase\WPTestCase;
use StellarWP\Telemetry\DebugDataProvider;
use StellarWP\Telemetry\OptInStatus;
use StellarWP\Telemetry\Telemetry;

class FiltersTest extends WPTestCase
{
	/** @var Telemetry */
	private $telemetry;

	/** @var OptInStatus */
	private $opt_in;

	public function setUp(): void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
	    $this->telemetry = new Telemetry(
			new DebugDataProvider(),
			'stellarwp_telemetry'
		);

		$this->opt_in = new OptInStatus( $this->telemetry );
    }

	public function test_register_site_url_filter() {
		$this->assertEquals( $this->telemetry::SERVER_URL . '/register-site', $this->telemetry->get_register_site_url() );

		add_filter( 'stellarwp_telemetry_register_site_url', static function() {
			return 'https://example.com';
		} );

		$this->assertEquals( 'https://example.com', $this->telemetry->get_register_site_url() );
	}

	public function test_register_site_data_filter() {
		$this->assertEquals( [
			'user'      => json_encode( $this->telemetry->get_user_details() ),
			'telemetry' => json_encode( $this->telemetry->provider->get_data() ),
		], $this->telemetry->get_register_site_data() );

		add_filter( 'stellarwp_telemetry_register_site_data', static function() {
			return [
				'user'      => 'user',
				'telemetry' => 'telemetry',
			];
		} );

		$this->assertEquals( [
			'user'      => 'user',
			'telemetry' => 'telemetry',
		], $this->telemetry->get_register_site_data() );
	}

	public function test_opt_in_status_filter() {
		$this->telemetry = $this->createPartialMock( Telemetry::class, [ '' ] );

		$this->telemetry = $this->getMockBuilder( Telemetry::class )
			->onlyMethods( [ 'register_site' ] )
			->getMock();

		$this->telemetry->expects( $this->once() )
			->method( 'register_site' )
			->willReturn( true );

		$this->telemetry->register_site();

		$this->assertEquals( $this->opt_in::STATUS_ACTIVE, $this->opt_in->get() );

		add_filter( 'stellarwp_telemetry_opt_in_status', function() {
			return $this->opt_in::STATUS_MIXED;
		} );

		$this->assertEquals( $this->opt_in::STATUS_MIXED, $this->opt_in->get() );
	}
}
