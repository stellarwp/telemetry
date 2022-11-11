<?php declare(strict_types=1);

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Abstract_Subscriber;

class Opt_In_Subscriber extends Abstract_Subscriber {

	public function register(): void {
		add_action( 'stellarwp/telemetry/optin', function () {
			( new Opt_In_Template() )->maybe_render();
		} );
	}

}
