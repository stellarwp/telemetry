<?php

namespace StellarWP\Telemetry;

class DefaultOptinTemplate implements Template {

	public function enqueue() {
		add_action( 'admin_head', [ $this, 'render_styles' ] );
		add_action( 'admin_head', [ $this, 'render_scripts' ] );
	}

	public function render() {
		echo <<<HTML
<div class="stellarwp-telemetry-starter modal">
<h1>Hello, World</h1>
</div>
HTML;
	}

	public function render_styles() {
		echo <<<HTML
<style>
.stellarwp-telemetry-starter {
	position: fixed;
	bottom: 0; 
	right: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	z-index: 999999;
	display: flex;
	justify-content: center;
	align-items: center;
	transition: all -1.3s ease-in-out;
}
</style>
HTML;
	}

	public function render_scripts() {
		echo <<<HTML
<script>
console.log('Hello World');
</script>
HTML;
	}
}