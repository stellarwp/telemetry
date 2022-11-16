<?php

namespace StellarWP\Telemetry;

use lucatume\DI52\Container;
use StellarWP\Telemetry\Contracts\Template;

class Exit_Interview_Template implements Template {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Abstract_Subscriber constructor.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	protected function get_args(): array {
		return apply_filters( 'stellarwp/telemetry/exit_interview_args', [
			'plugin_slug'        => $this->container->get( Core::PLUGIN_SLUG ),
			'plugin_logo'        => plugin_dir_url( __DIR__ ) . 'public/logo.png',
			'plugin_logo_width'  => 151,
			'plugin_logo_height' => 32,
			'plugin_logo_alt'    => 'StellarWP Logo',
			'heading'            => __( 'We’re sorry to see you go.', 'stellarwp-telemetry' ),
			'intro' 		     => __( 'We’d love to know why you’re leaving so we can improve our plugin.', 'stellarwp-telemetry' ),
			'questions'          => [
				[
					'question'   => __( 'I couldn’t understand how to make it work.', 'stellarwp-telemetry' ),
					'show_field' => true
				],
				[
					'question'   => __( 'I found a better plugin.', 'stellarwp-telemetry' ),
					'show_field' => true
				],
				[
					'question'   => __( 'I need a specific feature it doesn’t provide.', 'stellarwp-telemetry' ),
					'show_field' => true
				],
				[
					'question'   => __( 'The plugin doesn’t work.', 'stellarwp-telemetry' ),
					'show_field' => true
				],
				[
					'question'   => __( 'It’s not what I was looking for.', 'stellarwp-telemetry' ),
					'show_field' => true
				]
			],
		] );
	}

	public function render(): void {
		load_template( dirname( __DIR__ ) . '/views/exit-interview.php', true, $this->get_args() );
	}

	public function enqueue(): void {
		// TODO: Implement enqueue() method.
	}

	public function should_render(): bool {
		return apply_filters( 'stellarwp/telemetry/exit_interview_should_render', true );
	}

	public function maybe_render(): void {
		if ( $this->should_render() ) {
			$this->render();
		}
	}
}
