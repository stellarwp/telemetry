<?php

namespace StellarWP\Telemetry\Routes;

abstract class Abstract_Route {

	protected abstract function get_endpoint();

	protected abstract function action();

	public function register_route() {
		register_rest_route( $this->get_namespace(), $this->get_endpoint(), $this->get_args() );
	}

	protected function get_namespace() {
		return 'stellarwp/telemetry/v1';
	}

	protected function get_args() {
		return [
			'methods' => 'GET',
			'callback' => [ $this, 'action' ],
		];
	}

}
