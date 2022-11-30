# Telemetry Library

A library for Opt-in and Telemetry data to be sent to the StellarWP Telemetry server.

## Table of Contents
- [Telemetry Library](#telemetry-library)
	- [Table of Contents](#table-of-contents)
	- [Installation](#installation)
	- [Usage Prerequisites](#usage-prerequisites)
	- [Integration](#integration)
	- [Opt-In Modal Usage](#opt-in-modal-usage)
		- [Prompting Users on a Settings Page](#prompting-users-on-a-settings-page)
	- [Server Authentication Flow](#server-authentication-flow)
	- [Filter Reference](#filter-reference)
		- [stellarwp/telemetry/should\_show\_optin](#stellarwptelemetryshould_show_optin)
		- [stellarwp/telemetry/show\_optin\_option\_name](#stellarwptelemetryshow_optin_option_name)
		- [stellarwp/telemetry/cron\_interval](#stellarwptelemetrycron_interval)
		- [stellarwp/telemetry/cron\_hook\_name](#stellarwptelemetrycron_hook_name)
		- [stellarwp/telemetry/data](#stellarwptelemetrydata)
		- [stellarwp/telemetry/option\_name](#stellarwptelemetryoption_name)
		- [stellarwp/telemetry/optin\_status](#stellarwptelemetryoptin_status)
		- [stellarwp/telemetry/optin\_status\_label](#stellarwptelemetryoptin_status_label)
		- [stellarwp/telemetry/optin\_args](#stellarwptelemetryoptin_args)
		- [stellarwp/telemetry/show\_optin\_option\_name](#stellarwptelemetryshow_optin_option_name-1)
		- [stellarwp/telemetry/register\_site\_url](#stellarwptelemetryregister_site_url)
		- [stellarwp/telemetry/register\_site\_data](#stellarwptelemetryregister_site_data)
		- [stellarwp/telemetry/register\_site\_user\_details](#stellarwptelemetryregister_site_user_details)
		- [stellarwp/telemetry/send\_data\_args](#stellarwptelemetrysend_data_args)
		- [stellarwp/telemetry/send\_data\_url](#stellarwptelemetrysend_data_url)
		- [stellarwp/telemetry/token](#stellarwptelemetrytoken)
		- [stellarwp/telemetry/exit\_interview\_args](#stellarwptelemetryexit_interview_args)
## Installation

It's recommended that you install Telemetry as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/telemetry
```


> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Usage Prerequisites
To actually _use_ the telemetry library, you must have a Dependency Injection Container (DI Container) that is compatible with [di52](https://github.com/lucatume/di52) (_We recommend using di52_).

In order to keep this library as light as possible, a container is not included in the library itself. To avoid version compatibility issues, it is also not included as a Composer dependency. Instead, you must include it in your project. We recommend including it via composer [using Strauss](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md), just like you have done with this library.

## Integration
Initialize the library within your main plugin file after plugins are loaded (or anywhere else you see fit). Optionally, you can configure a unique prefix (we suggest you use your plugin slug) so that hooks can be uniquely called for your specific instance of the library.

```php
use StellarWP\Telemetry\Core as Telemetry;

add_action( 'plugins_loaded', 'initialize_telemetry' );

function initialize_telemetry() {
	/**
	 * Configure the container.
	 *
	 * The container must be compatible with stellarwp/container-contract.
	 * See here: https://github.com/stellarwp/container-contract#usage.
	 *
	 * If you do not have a container, we recommend https://github.com/lucatume/di52
	 * and the corresponding wrapper:
	 * https://github.com/stellarwp/container-contract/blob/main/examples/di52/Container.php
	 */
	$container = new Container();
	Config::set_container( $container );

	// Set the full URL for the Telemetry Server API.
	Config::set_server_url( 'https://telemetry.example.com/api/v1' );

	// Optional: Set a unique prefix for actions & filters.
	Config::set_hook_prefix( 'my-custom-prefix' );

    // Initialize the library.
    Telemetry::instance()->init( __FILE__ );
}
```

Using a custom hook prefix provides the ability to uniquely filter functionality of your plugin's specific instance of the library.

## Opt-In Modal Usage

### Prompting Users on a Settings Page
On each settings page you'd like to prompt the user to opt-in, add a `do_action()`. _Be sure to include your hook prefix if you are using one_.
```php
do_action( 'stellarwp/telemetry/my-custom-prefix/optin' );
```
The library calls this action to handle registering the required resources needed to render the modal. It will only display the modal for users who haven't yet opted in.

To show the modal on a settings page, add the `do_action()` to the top of your rendered page content:
```php
function my_options_page() {
    do_action( 'stellarwp/telemetry/my-custom-prefix/optin' );
    ?>
    <div>
        <h2>My Plugin Settings Page</h2>
    </div>
    <?php
}
```
_Note: When adding the `do_action`, you may pass additional arguments to the library with an array. There is no functionality at the moment, but we expect to expand the library to accept configuration through the passed array._
```php
do_action( 'stellarwp/telemetry/my-custom-prefix/optin', [ 'plugin_slug' => 'the-events-calendar' ] );
```

## Server Authentication Flow
TBD

## Filter Reference

If you configured this library to use a hook prefix, note that all hooks will now use this prefix. For example:
```php
add_filter( 'stellarwp/telemetry/my-custom-prefix/should_show_optin', 'my-custom-filter', 10, 1 );
```
### stellarwp/telemetry/should_show_optin
Filters whether the user should be shown the opt-in modal.

**Parameters**: _bool_ `$should_show`

**Default**: `true`

### stellarwp/telemetry/show_optin_option_name
Filters the option name used to store whether the opt-in should be shown.

**Parameters**: _string_ `$option_name`

**Default**: `stellarwp_telemetry_show_optin`

### stellarwp/telemetry/cron_interval
Filters how often data should be sent to the Telemetry server in seconds.

**Parameters**: _integer_ `$interval`

**Default**: `WEEK_IN_SECONDS`
```php
add_filter( 'stellarwp/telemetry/cron_interval', 'send_data_daily', 10, 1 );

function send_data_daily( $interval ) {
	return DAY_IN_SECONDS;
}
```
### stellarwp/telemetry/cron_hook_name
Filters the string used for the cron hook.

**Parameters**: _string_ `$hook_name`

**Default**: `stellarwp_telemetry_cron`

### stellarwp/telemetry/data
Filters the data sent to the Telemetry server.

**Parameters**: _array_ `$info`

**Default**: see output of [debug_data()](https://developer.wordpress.org/reference/classes/wp_debug_data/debug_data/)
```php
// Additional default array values appended to debug_data().
$info['telemetry-active-plugins']['fields']   = $active_plugins;
$info['telemetry-inactive-plugins']['fields'] = $inactive_plugins;
```

**Example**:
```php
add_filter( 'stellarwp/telemetry/data', 'add_total_post_count_to_site_data', 10, 1 );

function add_total_post_count_to_site_data( array $info ) {
	$info['total_post_count'] = wp_count_posts( 'post' );

	return $info;
}
```
### stellarwp/telemetry/option_name
Filter the option name used to store current users' optin status.

**Parameters**: _string_ `$option_name`

**Default**: `stellarwp_telemetry`

### stellarwp/telemetry/optin_status
Filter the optin status of the current site.

**Parameters**: _integer_ `$status`

**Default**: `1`

Each status corresponds with an integer:
```php
1 = 'Active',
2 = 'Inactive',
3 = 'Mixed',
```
### stellarwp/telemetry/optin_status_label
Filter the label used to show the current opt-in status of the site.

**Parameters**: _string_ `$optin_label`

**Default**: see: [stellarwp/telemetry/optin_status](#stellarwptelemetryoptin_status)
### stellarwp/telemetry/optin_args
Filter the arguments passed to the opt-in modal.

**Parameters**: _array_ `$args`

**Default**:
```php
$args = [
	'plugin_logo'        => plugin_dir_url( __DIR__ ) . 'public/logo.png',
	'plugin_logo_width'  => 151,
	'plugin_logo_height' => 32,
	'plugin_logo_alt'    => 'StellarWP Logo',
	'plugin_name'        => 'The Events Calendar',
	'user_name'          => wp_get_current_user()->display_name,
	'permissions_url'    => '#',
	'tos_url'            => '#',
	'privacy_url'        => '#',
];
```
### stellarwp/telemetry/show_optin_option_name
Filter the string used for the option that determines whether the opt-in modal should be shown.

**Parameters**: _string_ `$option_name`

**Default**: `stellarwp_telemetry_show_optin`
### stellarwp/telemetry/register_site_url
Filters the url of the telemetry server that will store the site data when registering a new site.

**Parameters**: _string_ `$url`

**Default**: `https://telemetry-api.moderntribe.qa/api/v1/register-site`
### stellarwp/telemetry/register_site_data
Filters the data that is sent to the telemetry server when registering a new site.

**Parameters**: _array_ `$site_data`

**Default**:
```php
$site_data = [
	'user'      => json_encode( $this->get_user_details() ),
	'telemetry' => json_encode( $this->provider->get_data() ),
];
```
### stellarwp/telemetry/register_site_user_details
Filters the user details that is sent to the telemetry server when registering a new site.

**Parameters**: _array_ `$user_details`

**Default**:
```php
$user_details = [
	'name'  => $user->display_name,
	'email' => $user->user_email,
];
```
### stellarwp/telemetry/send_data_args

**Parameters**: _array_ $data_args

**Default**:
```php
$data_args = [
	'token'     => $this->get_token(),
	'telemetry' => json_encode( $this->provider->get_data() ),
];
```

### stellarwp/telemetry/send_data_url
Filters the URL of the telemetry server used to send the site data.

**Parameters**: _string_ `$url`

**Default**: `https://telemetry-api.moderntribe.qa/api/v1/telemetry`

### stellarwp/telemetry/token
Filters the token used to authenticate requests to the telemetry server.

**Parameters**: _string_ `$token`

### stellarwp/telemetry/exit_interview_args
Filters the arguments used in the plugin deactivation "exit interview" form.

**Parameters**: _array_ `$args`

**Default**:
```php
$args = [
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
];
```
