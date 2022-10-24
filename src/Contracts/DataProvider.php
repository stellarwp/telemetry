<?php

namespace StellarWP\Telemetry\Contracts;

interface DataProvider {
	public function get_data(): array;
}