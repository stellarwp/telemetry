<?php

namespace StellarWP\Telemetry;

interface ActivationHook {
	public function run( PluginStarter $plugin ): void;
}