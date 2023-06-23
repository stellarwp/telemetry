<?php declare(strict_types=1);

/**
 * @param string $name
 * @param mixed  $default
 */
function app_env( string $name, mixed $default = null ): mixed {
	$env = getenv( $name );
	if ( $env === false ) {
		return $default;
	}

	$env_str = strtolower( trim( $env ) );
	if ( $env_str === 'false' || $env_str === 'true' ) {
		return filter_var( $env_str, FILTER_VALIDATE_BOOLEAN );
	}

	if ( is_numeric( $env ) ) {
		return $env - 0;
	}

	return $env;
}

/**  Parse the LANDO INFO  */
$lando_info = json_decode( app_env( 'LANDO_INFO' ) );

/** Get the database config */
$database_config = $lando_info->database;
/** The name of the database for WordPress */
define( 'DB_NAME', $database_config->creds->database );
/** MySQL database username */
define( 'DB_USER', $database_config->creds->user );
/** MySQL database password */
define( 'DB_PASSWORD', $database_config->creds->password );
/** MySQL hostname */
define( 'DB_HOST', $database_config->internal_connection->host );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_HOME', app_env( 'WP_HOME', 'https://telemetry-library.lndo.site' ) );
define( 'WP_SITEURL', app_env( 'WP_SITEURL', 'https://telemetry-library.lndo.site/wp' ) );
define( 'WP_CONTENT_URL', sprintf( '%s/wp-content', WP_HOME ) );
define( 'WP_CONTENT_DIR', __DIR__ . '/wp-content' );
define( 'TELEMETRY_SERVER_URL', app_env( 'TELEMETRY_SERVER_URL', 'https://telemetry-dev.stellarwp.com/api/v1' ) );

$table_prefix = app_env( 'DB_PREFIX', 'wp_' );  // phpcs:ignore

define( 'WP_DISABLE_FATAL_ERROR_HANDLER', app_env( 'WP_DISABLE_FATAL_ERROR_HANDLER', true ) );
define( 'WP_DEBUG', app_env( 'WP_DEBUG', true ) );
define( 'SCRIPT_DEBUG', app_env( 'SCRIPT_DEBUG', true ) );
define( 'DISABLE_WP_CRON', app_env( 'DISABLE_WP_CRON', true ) );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wp/' );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) || ! WP_DEBUG_DISPLAY ) {
	ini_set( 'display_errors', '0' );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_wp_hook(
		'enable_wp_debug_mode_checks',
		static function ( $ret ) {
			if ( WP_DEBUG_LOG && is_string( WP_DEBUG_LOG ) ) {
				ini_set( 'error_log', WP_DEBUG_LOG );
			}

			return $ret;
		},
		11
	);
}
