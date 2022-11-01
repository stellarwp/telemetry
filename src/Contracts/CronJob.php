<?php

namespace StellarWP\Telemetry\Contracts;

interface CronJob {
	public function is_scheduled(): int;
	public function schedule( int $start ): void;
	public function unschedule(): void;
	public function run(): void;
}