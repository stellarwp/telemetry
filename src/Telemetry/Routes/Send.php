<?php

namespace StellarWP\Telemetry\Routes;

class Send extends Abstract_Route {

    public function get_endpoint() {
		return '/send';
	}

    public function action() {
		$this->send_early_ok();
	}
}
