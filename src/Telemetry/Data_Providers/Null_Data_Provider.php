<?php
/**
 * A data provider that provides no data, used for testing.
 *
 * @since   TBD
 *
 * @package StellarWP\Telemetry\Data_Providers;
 */

namespace StellarWP\Telemetry\Data_Providers;

use StellarWP\Telemetry\Contracts\Data_Provider;

/**
 * Class Null_Data_Provider.
 *
 * @since   TBD
 *
 * @package StellarWP\Telemetry\Data_Providers;
 */
class Null_Data_Provider implements Data_Provider {

	/**
	 * {@inheritDoc}
	 *
	 * @since   TBD
	 */
	public function get_data(): array {
		return [];
	}
}
