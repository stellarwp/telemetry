<?php

namespace StellarWP\Telemetry;

interface Template {
	public function render();
	public function enqueue();
}