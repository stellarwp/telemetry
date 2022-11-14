# Telemetry Library

A library for Opt-in and Telemetry data to be sent to the StellarWP Telemetry server.

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
