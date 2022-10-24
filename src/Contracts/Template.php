<?php

namespace StellarWP\Telemetry\Contracts;

interface Template {
	public function render();
	public function enqueue();
}