<?php

namespace StellarWP\Telemetry\Contracts;

interface Data_Provider {
	public function get_data(): array;
}
