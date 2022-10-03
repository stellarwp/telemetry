<?php

namespace StellarWP\Telemetry;

interface TelemetryProvider {
	public function get_data(): array;
}