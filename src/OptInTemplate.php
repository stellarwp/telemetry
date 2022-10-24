<?php

namespace StellarWP\Telemetry;

use StellarWP\Telemetry\Contracts\Template;

class OptInTemplate implements Template {
	protected const YES = "1";
	protected const NO = "-1";

	public function enqueue(): void {
		// TODO: Once FE template is done, enqueue it here.
	}

	public function render(): void {
		$this->render_styles();
		$this->render_scripts();

		echo <<<HTML
<div class="stellarwp-telemetry-starter modal">
<h1>Hello, World</h1>
</div>
HTML;
	}

	protected function get_option_name(): string {
		return apply_filters( 'stellarwp_telemetry_show_optin_option_name', 'stellarwp_telemetry_show_optin' );
	}

	public function should_render(): bool {
		if ( get_option( $this->get_option_name(), false ) === self::YES ) {
			return true;
		}

		return false;
	}

	public function maybe_render(): void {
		if ( $this->should_render() ) {
			$this->render();
		}
	}

	public function render_styles(): void {
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

	public function render_scripts(): void {
		echo <<<HTML
<script>
console.log('Hello World');
</script>
HTML;
	}
}