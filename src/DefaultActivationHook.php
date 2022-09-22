<?php

namespace StellarWP\Telemetry;

class DefaultActivationHook implements ActivationHook {
	public function register() {
		echo "DefaultActivationHook::register()";
	}
}