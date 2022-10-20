<?php

namespace StellarWP\Telemetry;

interface DataProvider {
	public function get_data(): array;
}