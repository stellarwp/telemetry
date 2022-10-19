<?php

namespace StellarWP\Telemetry;

interface ActivationHook {
	public function run(): void;
}