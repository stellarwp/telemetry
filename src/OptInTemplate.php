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

		$url = plugin_dir_url( __DIR__ ) . 'public/logo.png';

		echo <<<HTML
<div class="stellarwp-telemetry-starter wrapper">
	<section class="stellarwp-telemetry-starter modal">
		<header>
			<img src="$url" width="151" height="32" />
			<h1 class="stellarwp-telemetry-starter-header">We hope you love [The Events Calendar].</h1>
		</header>
		<main>
			<p>Hi, [username]! This is an invitation to help our StellarWP community. If you opt-in, some data about your usage of [The Events Calendar] and Future StellarWP Products will be shared with our teams (so they can work their butts off to improve). We will also share some helpful info on WordPress, and our products from time to time. And if you skip this, that’s okay! Our Products still works just fine.</p>
			<ul class="links">
				<li><a href="#">What permissions are being granted?</a></li>
				<li><a href="#">Terms of Service</a></li>
				<li><a href="#">Privacy Policy</a></li>
			</ul>
		</main>
		<footer>
			<button class="btn-primary">Allow &amp; Continue</button>
			<button class="btn-text">Skip</button>
		</footer>
	</section>
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
.stellarwp-telemetry-starter.wrapper {
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

.stellarwp-telemetry-starter.modal {
	position: absolute;
	z-index: 999999;
	display: flex;
	flex-direction: column;
	padding: 32px;
	width: 90%;
	top: 47px;
	left: 32px;
	background: #fff;
	box-shadow: 0 0 32px rgba(0, 0, 0, 0.1);
	border-radius: 4px;
}

.stellarwp-telemetry-starter h1 {
	font-family: 'SF Pro Text', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
	font-size: 18px;
	font-weight: 500;
	line-height: 24px;
	letter-spacing: -0.02em;
	color: #000;
}

.stellarwp-telemetry-starter {
	font-family: 'SF Pro Text', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
	font-size: 14px;
	font-weight: 400;
	line-height: 18px;
	color: #4E4E4E;
}

.stellarwp-telemetry-starter .btn-primary, 
.stellarwp-telemetry-starter .btn-text {
	padding: 6px 12px;
	background: #0047FF;
	border-radius: 4px;
	color: #fff;
}

.stellarwp-telemetry-starter .btn-text {
	background: #fff;
	color: #000;
}

.stellarwp-telemetry-starter .links {
	display: flex;
	gap: 16px;
	font-size: 12px;
	line-height: 16px;
}

.stellarwp-telemetry-starter .links a {
	color: #0047FF;
	text-decoration: none;
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