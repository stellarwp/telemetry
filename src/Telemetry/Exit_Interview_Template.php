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
			'uninstall_reasons'  => [
				[
					'uninstall_reason_id' => 'confusing',
					'uninstall_reason'    => __( 'I couldn’t understand how to make it work.', 'stellarwp-telemetry' ),
				],
				[
					'uninstall_reason_id' => 'better-plugin',
					'uninstall_reason'    => __( 'I found a better plugin.', 'stellarwp-telemetry' ),
				],
				[
					'uninstall_reason_id' => 'no-feature',
					'uninstall_reason'    => __( 'I need a specific feature it doesn’t provide.', 'stellarwp-telemetry' ),
				],
				[
					'uninstall_reason_id' => 'broken',
					'uninstall_reason'    => __( 'The plugin doesn’t work.', 'stellarwp-telemetry' ),
				],
				[
					'uninstall_reason_id' => 'other',
					'uninstall_reason'    => __( 'Other', 'stellarwp-telemetry' ),
					'show_comment'        => true
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
