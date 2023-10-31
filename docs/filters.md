# Filter Reference

## Table of Contents
- [Filter Reference](#filter-reference)
	- [Table of Contents](#table-of-contents)
		- [stellarwp/telemetry/{hook-prefix}/should\_show\_optin](#stellarwptelemetryhook-prefixshould_show_optin)
		- [stellarwp/telemetry/{hook-prefix}/option\_name](#stellarwptelemetryhook-prefixoption_name)
		- [stellarwp/telemetry/{hook-prefix}/optin\_status](#stellarwptelemetryhook-prefixoptin_status)
		- [stellarwp/telemetry/{hook-prefix}/optin\_status\_label](#stellarwptelemetryhook-prefixoptin_status_label)
		- [stellarwp/telemetry/optin\_args](#stellarwptelemetryoptin_args)
		- [stellarwp/telemetry/{stellar\_slug}/optin\_args](#stellarwptelemetrystellar_slugoptin_args)
		- [stellarwp/telemetry/{hook-prefix}/show\_optin\_option\_name](#stellarwptelemetryhook-prefixshow_optin_option_name)
		- [stellarwp/telemetry/{hook-prefix}/register\_site\_url](#stellarwptelemetryhook-prefixregister_site_url)
		- [stellarwp/telemetry/{hook-prefix}/register\_site\_data](#stellarwptelemetryhook-prefixregister_site_data)
		- [stellarwp/telemetry/{hook-prefix}/register\_site\_user\_details](#stellarwptelemetryhook-prefixregister_site_user_details)
		- [stellarwp/telemetry/{hook-prefix}/send\_data\_args](#stellarwptelemetryhook-prefixsend_data_args)
		- [stellarwp/telemetry/{hook-prefix}/send\_data\_url](#stellarwptelemetryhook-prefixsend_data_url)
		- [stellarwp/telemetry/{hook-prefix}/last\_send\_expire\_seconds](#stellarwptelemetryhook-prefixlast_send_expire_seconds)
		- [stellarwp/telemetry/exit\_interview\_args](#stellarwptelemetryexit_interview_args)
		- [stellarwp/telemetry/{stellar\_slug}/exit\_interview\_args](#stellarwptelemetrystellar_slugexit_interview_args)
		- [stellarwp/telemetry/{hook-prefix}/event\_data](#stellarwptelemetryhook-prefixevent_data)
		- [stellarwp/telemetry/{hook-prefix}/events\_url](#stellarwptelemetryhook-prefixevents_url)
	- [Action Reference](#action-reference)
		- [stellarwp/telemetry/optin](#stellarwptelemetryoptin)
		- [stellarwp/telemetry/{hook-prefix}/optin](#stellarwptelemetryhook-prefixoptin)
		- [stellarwp/telemetry/{hook-prefix}/event](#stellarwptelemetryhook-prefixevent)

If you configured this library to use a hook prefix, note that all hooks will now use this prefix. For example:
```php
add_filter( 'stellarwp/telemetry/my-custom-prefix/should_show_optin', 'my-custom-filter', 10, 1 );
```
### stellarwp/telemetry/{hook-prefix}/should_show_optin
Filters whether the user should be shown the opt-in modal.

**Parameters**: _bool_ `$should_show`

**Default**: `true`

### stellarwp/telemetry/{hook-prefix}/option_name
Filter the option name used to store current users' optin status.

**Parameters**: _string_ `$option_name`

**Default**: `stellarwp_telemetry`

### stellarwp/telemetry/{hook-prefix}/optin_status
Filter the optin status of the current site.

**Parameters**: _integer_ `$status`

**Default**: `1`

Each status corresponds with an integer:
```php
1 = 'Active',
2 = 'Inactive',
3 = 'Mixed',
```
### stellarwp/telemetry/{hook-prefix}/optin_status_label
Filter the label used to show the current opt-in status of the site.

**Parameters**: _string_ `$optin_label`

**Default**: see: [stellarwp/telemetry/optin_status](#stellarwptelemetryoptin_status)
### stellarwp/telemetry/optin_args
Filter the arguments passed to the opt-in modal.

**Parameters**: _array_ `$args`, _string_ `$stellar_slug`

**Default**:
```php
$args = [
	'plugin_logo'           => Resources::get_asset_path() . 'resources/images/stellar-logo.svg',
	'plugin_logo_width'     => 151,
	'plugin_logo_height'    => 32,
	'plugin_logo_alt'       => 'StellarWP Logo',
	'plugin_name'           => 'The Events Calendar',
	'plugin_slug'           => $stellar_slug,
	'user_name'             => wp_get_current_user()->display_name,
	'permissions_url'       => '#',
	'tos_url'               => '#',
	'privacy_url'           => '#',
	'opted_in_plugins_text' => __( 'See which plugins you have opted in to tracking for', 'stellarwp-telemetry' ),
	'heading'               => __( 'We hope you love {plugin_name}.', 'stellarwp-telemetry' ),
	'intro'                 => __( 'Hi, {user_name}.! This is an invitation to help our StellarWP community. If you opt-in, some data about your usage of {plugin_name} and future StellarWP Products will be shared with our teams (so they can work their butts off to improve). We will also share some helpful info on WordPress, and our products from time to time. And if you skip this, that’s okay! Our products still work just fine.', 'stellarwp-telemetry' ),
];
```
### stellarwp/telemetry/{stellar_slug}/optin_args
This filter will be deprecated in future versions. Use [stellarwp/telemetry/optin_args](#stellarwptelemetrystellar_slugoptin_args) instead.

### stellarwp/telemetry/{hook-prefix}/show_optin_option_name
Filters the string used for the option that determines whether the opt-in modal should be shown.

**Parameters**: _string_ `$option_name`

**Default**: `stellarwp_telemetry_{plugin_slug}_show_optin`
### stellarwp/telemetry/{hook-prefix}/register_site_url
Filters the url of the telemetry server that will store the site data when registering a new site.

**Parameters**: _string_ `$url`

**Default**: `https://telemetry.example.com/api/v1/register-site`
### stellarwp/telemetry/{hook-prefix}/register_site_data
Filters the data that is sent to the telemetry server when registering a new site.

**Parameters**: _array_ `$site_data`

**Default**:
```php
$site_data = [
	'telemetry' => json_encode( $this->provider->get_data() ),
];
```
### stellarwp/telemetry/{hook-prefix}/register_site_user_details
Filters the user details that is sent to the telemetry server when registering a new site.

**Parameters**: _array_ `$user_details`

**Default**:
```php
$user_details = [
	'name'  => $user->display_name,
	'email' => $user->user_email,
	'plugin_slug' => Config::get_container()->get( Core::PLUGIN_SLUG ),
];
```
### stellarwp/telemetry/{hook-prefix}/send_data_args

**Parameters**: _array_ $data_args

**Default**:
```php
$data_args = [
	'token'     => $this->get_token(),
	'telemetry' => json_encode( $this->provider->get_data() ),
];
```

### stellarwp/telemetry/{hook-prefix}/send_data_url
Filters the full url to use when sending data to the telemetry server.

**Parameters**: _string_ `$url`

**Default**: `https://telemetry.example.com/api/v1/telemetry`

### stellarwp/telemetry/{hook-prefix}/last_send_expire_seconds
Filters how often the library should send site health data to the telemetry server.

**Parameters**: _integer_ `$seconds`

**Default**: `7 * DAY_IN_SECONDS`

### stellarwp/telemetry/exit_interview_args
Filters the arguments used in the plugin deactivation "exit interview" form.

**Parameters**: _array_ `$args`, _string_ `$stellar_slug`

**Default**:
```php
$args = [
	'plugin_slug'        => $stellar_slug,
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

### stellarwp/telemetry/{stellar_slug}/exit_interview_args
This filter will be deprecated in future versions. Use [stellarwp/telemetry/exit_interview_args](#stellarwptelemetrystellar_slugexit_interview_args) instead.

### stellarwp/telemetry/{hook-prefix}/event_data
Filters the array of data sent along with the event.

**Parameters**: _array_ `$data`

**Default**:
```php
$data = [
	'token'        => $this->telemetry->get_token(),
	'stellar_slug' => Config::get_stellar_slug(),
	'event'        => $name,
	'event_data'   => wp_json_encode( $data ),
];
```
### stellarwp/telemetry/{hook-prefix}/events_url
Filters the event URL used when sending events to the Telemetry server.

**Parameters**: _string_ `$url`

**Default**: `https://telemetry.stellarwp.com/api/v1/events`

## Action Reference

### stellarwp/telemetry/optin

**Parameters**: _string_ `$stellar_slug` The stellar slug of the plugin for which the modal should be shown.
### stellarwp/telemetry/{hook-prefix}/optin
This filter will be deprecated in future versions. Use [stellarwp/telemetry/optin](#stellarwptelemetryoptin) instead.

### stellarwp/telemetry/{hook-prefix}/event
Sends a site event to the Telemetry server.

**Parameters**:
- _string_ `$event` The name of the event.
- _array_ `$data` A set of data that should be passed along with the event.
