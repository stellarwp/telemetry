<?php

namespace StellarWP\Telemetry\Contracts;

interface Template {
	public function render(): void;
	public function enqueue(): void;
	public function should_render(): bool;
}