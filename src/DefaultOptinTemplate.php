<?php

namespace StellarWP\Telemetry;

class DefaultOptinTemplate implements Template {

	public function render() {
		echo "DefaultOptinTemplate::render()";
	}

	public function enqueue() {
		echo "DefaultOptinTemplate::enqueue()";
	}
}