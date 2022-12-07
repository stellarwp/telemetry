<?php
/**
 * Provides an API for rendering templates.
 *
 * @since 1.0.0
 *
 * @package StellarWP\Telemetry\Contracts
 */

namespace StellarWP\Telemetry\Contracts;

/**
 * Interface that provides an API for rendering templates.
 */
interface Template {
	/**
	 * Renders the template.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render();

	/**
	 * Enqueues assets for the rendered template.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue();

	/**
	 * Determines if the template should be rendered.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function should_render();
}
