<?php

namespace StellarWP\Telemetry\Contracts;

interface Request {
	public function get_url(): string;
	public function get_args(): array;
}