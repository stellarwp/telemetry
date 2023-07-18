<?php
/**
 * Provides the correct aliasing necessary for VS Code intelephense to interpret Codeception assert methods.
 *
 * @see https://github.com/lucatume/wp-browser/issues/513
 */

namespace tad\WPBrowser\Compat\Codeception;

class Unit extends \tad\WPBrowser\Compat\Codeception\Version2\Unit{}
