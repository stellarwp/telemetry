<?php declare(strict_types=1);

use StellarWP\Telemetry\Config;
use StellarWP\Telemetry\Core as Telemetry;
use StellarWP\Telemetry\Opt_In\Opt_In_Template;
use StellarWP\Telemetry\Opt_In\Status;

$container           = Telemetry::instance()->container();
$opt_in_status_value = $container->get( Status::class )->get();

do_action( 'stellarwp/telemetry/optin', 'telemetry-library' );
?>
<div class="wrap">
	<h1>Telemetry Settings</h1>
	<form method="post" action="">
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="">Opt In Status</label>
				</th>
				<td>
					<label for="active"><input type="radio" value="1" name="opt-in-status" id="active" <?php checked( '1', $opt_in_status_value ); ?>/>Active</label><br/>
					<label for="inactive"><input type="radio" value="0" name="opt-in-status" id="inactive" <?php checked( '2', $opt_in_status_value ); ?>/>Inactive</label><br/>
				</td>
			</tr>
			<?php foreach ( Config::get_all_stellar_slugs() as $stellar_slug => $wp_slug ) { ?>
			<tr>
				<th scope="row">
					<label for="show_modal">Show Opt In Modal (<?php echo esc_html( $stellar_slug ); ?>)</label>
				</th>
				<td>
					<input type="hidden" value="<?php echo esc_html( $stellar_slug ); ?>" name="stellar_slugs[]">
					<input type="checkbox" value="1" name="show_<?php echo esc_html( $stellar_slug ); ?>_modal" id="show_modal" <?php checked( true, $container->get( Opt_In_Template::class )->should_render( $stellar_slug ) ); ?>/><br />
				</td>
			</tr>
			<?php } ?>
		</table>
	<?php submit_button( 'Save Settings' ); ?>
</form>

<h2>Telemetry Events</h2>
<form method="post" action="">
	<?php wp_nonce_field( 'telemetry-library-send-event' ); ?>
	<table class="form-table">
		<tr>
			<th>
				<label for="event">Event to Send</label>
			</th>
			<td>
				<select type="text" name="event" id="event">
					<option value="">Select an Event</option>
					<option value="opt-in">Opt In</option>
					<option value="opt-out">Opt Out</option>
					<option value="post-added">New Post Added</option>
					<option value="invalid-event">Invalid Event</option>
				</select>
				<p class="description">Select an event to send to the telemetry server.</p>
				<p class="description">The <code>Invalid Event</code> option should not be added to the list of site events on the server and should not trigger any errors or warnings on this site.</p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="number">Number of Events to send</label>
			</th>
			<td>
				<input type="number" name="number" id="number" value="1">
				<p class="description">The number of events to be batched and sent in a single request.</p>
			</td>
		</tr>
	</table>
	<?php submit_button( 'Trigger Event(s)' );?>
</form>

<h2>Telemetry Database Data</h2>
<?php
global $wpdb;

$sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}options WHERE `option_name` LIKE 'stellarwp_telemetry%%' ORDER BY `option_id`;" );
$rows = $wpdb->get_results( $sql, ARRAY_A );

if ( count( $rows ) > 0 ) {
	?>
	<table role="presentation" class="license-table wp-list-table widefat fixed striped table-view-list" cellspacing="0">
		<thead>
			<th>ID</th>
			<th>Option Name</th>
			<th>Option Value</th>
			<th>Autoload</th>
		</thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr>
					<td><?php echo $row['option_id']; ?></td>
					<td><?php echo $row['option_name']; ?></td>
					<td><?php echo $row['option_value']; ?></td>
					<td><?php echo $row['autoload']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
} else {
	?>
	<p>No telemetry related options saved to the table yet. Try reloading the page.</p>
	<?php
}
?>

<form method="post" action="">
	<?php wp_nonce_field( 'telemetry-library-clear-database-options' ); ?>
	<?php submit_button( 'Clear Options' );?>
	<p class="description">This removes all telemetry-related items from the database so it behaves as if you had just activated the plugin.</p>
</form>
</div>
