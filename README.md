# Telemetry Library

A library for Opt-in and Telemetry data to be sent to the StellarWP Telemetry server.

## Table of Contents
- [Telemetry Library](#telemetry-library)
	- [Table of Contents](#table-of-contents)
	- [Installation](#installation)
	- [Integration](#integration)
	- [Opt-In Modal Usage](#opt-in-modal-usage)
		- [Prompting Users on a Settings Page](#prompting-users-on-a-settings-page)
	- [Filter Reference](#filter-reference)
		- [Activation](#activation)
			- [stellarwp/telemetry/redirect_on_activation](#stellarwptelemetryredirect_on_activation)
			- [stellarwp/telemetry/redirection_option_name](#stellarwptelemetryredirection_option_name)
			- [stellarwp/telemetry/should_show_optin](#stellarwptelemetryshould_show_optin)
			- [stellarwp/telemetry/show_optin_option_name](#stellarwptelemetryshow_optin_option_name)
			- [stellarwp/telemetry/activation_redirect](#stellarwptelemetryactivation_redirect)
		- [Core](#core)
			- [stellarwp/telemetry/is_settings_page](#stellarwptelemetryis_settings_page)
		- [Cron](#cron)
			- [stellarwp/telemetry/cron_interval](#stellarwptelemetrycron_interval)
			- [stellarwp/telemetry/cron_hook_name](#stellarwptelemetrycron_hook_name)
		- [Data](#data)
			- [stellarwp/telemetry/data](#stellarwptelemetrydata)
		- [Opt-in Status](#opt-in-status)
			- [stellarwp/telemetry/option_name](#stellarwptelemetryoption_name)
			- [stellarwp/telemetry/optin_status](#stellarwptelemetryoptin_status)
			- [stellarwp/telemetry/token](#stellarwptelemetrytoken)
			- [stellarwp/telemetry/optin_status_label](#stellarwptelemetryoptin_status_label)
		- [Opt-in Template](#opt-in-template)
			- [stellarwp/telemetry/optin_args](#stellarwptelemetryoptin_args)
			- [stellarwp/telemetry/show_optin_option_name](#stellarwptelemetryshow_optin_option_name-1)
		- [Telemetry](#telemetry)
			- [stellarwp/telemetry/register_site_url](#stellarwptelemetryregister_site_url)
			- [stellarwp/telemetry/register_site_data](#stellarwptelemetryregister_site_data)
			- [stellarwp/telemetry/register_site_user_details](#stellarwptelemetryregister_site_user_details)
			- [stellarwp/telemetry/send_data_args](#stellarwptelemetrysend_data_args)
			- [stellarwp/telemetry/send_data_url](#stellarwptelemetrysend_data_url)
			- [stellarwp/telemetry/token](#stellarwptelemetrytoken-1)
## Installation

It's recommended that you install Telemetry as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/telemetry
```


> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Integration
Initialize the library within your main plugin file after plugins are loaded and configure a unique prefix (we suggest you use your plugin slug):
```php
add_action( 'plugins_loaded', 'initialize_telemetry' );

function initialize_telemetry() {
    // Set a unique prefix for actions & filters.
    Config::set_hook_prefix( 'my-custom-prefix' );

    // Initialize the library
    Telemetry::instance()->init( __FILE__ );
}
```

Using a custom hook prefix provides the ability to uniquely filter modal functionality for your plugin's specific instance of the library.

## Opt-In Modal Usage

### Prompting Users on a Settings Page
On each settings page you'd like to prompt the user to opt-in, add a `do_action()`:
```php
do_action( 'stellarwp/telemetry/optin' );
```
The library calls this action to handle registering the required resources needed to render the modal. It will only display the modal for users who haven't yet opted in.

To show the modal on a settings page, add the `do_action()` to the top of your rendered page content:
```php
function my_options_page() {
    do_action( 'stellarwp/telemetry/optin' );
    ?>
    <div>
        <h2>My Plugin Settings Page</h2>
    </div>
    <?php
}
```
_Note: When adding the `do_action`, you may pass additional arguments to the library with an array. There is no functionality at the moment, but we expect to expand the library to accept configuration through the passed array._
```php
do_action( 'stellarwp/telemetry/optin', [ 'plugin_slug' => 'the-events-calendar' ] );
```

## Filter Reference
### Activation
#### stellarwp/telemetry/redirect_on_activation
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/redirection_option_name
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/should_show_optin
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/show_optin_option_name
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/activation_redirect
[Table of Contents](#table-of-contents)
### Core
#### stellarwp/telemetry/is_settings_page
[Table of Contents](#table-of-contents)
### Cron
#### stellarwp/telemetry/cron_interval
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/cron_hook_name
[Table of Contents](#table-of-contents)
### Data
#### stellarwp/telemetry/data
[Table of Contents](#table-of-contents)
### Opt-in Status
#### stellarwp/telemetry/option_name
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/optin_status
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/token
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/optin_status_label
[Table of Contents](#table-of-contents)
### Opt-in Template
#### stellarwp/telemetry/optin_args
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/show_optin_option_name
[Table of Contents](#table-of-contents)
### Telemetry
#### stellarwp/telemetry/register_site_url
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/register_site_data
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/register_site_user_details
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/send_data_args
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/send_data_url
[Table of Contents](#table-of-contents)
#### stellarwp/telemetry/token
[Table of Contents](#table-of-contents)
