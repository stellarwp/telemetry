<?php

namespace StellarWP\Telemetry\Contracts;

interface Cron_Job {
	public function is_scheduled(): bool;
	public function schedule( int $start ): void;
	public function unschedule(): void;
	public function run(): void;
}
