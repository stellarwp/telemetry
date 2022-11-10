<?php

namespace StellarWP\Telemetry\Contracts;

interface Redirection {
	public function get_url(): string;
	public function trigger(): void;
}