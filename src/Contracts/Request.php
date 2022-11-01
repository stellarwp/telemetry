<?php

namespace StellarWP\Telemetry\Contracts;

interface Request {
	public const TELEMETRY_URL = 'https://telemetry-api.moderntribe.qa/api/v1';

	public function get_url(): string;
	public function get_args(): array;
}