<?php

namespace StellarWP\Telemetry;

interface Request {
	public function get_url(): string;
	public function get_args(): array;
	public function run(): bool;
}