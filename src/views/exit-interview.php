<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */
?>

<div class="stellarwp-telemetry exit-interview" data-plugin-slug="<?php echo $args['plugin_slug']; ?>">
	<section class="stellarwp-telemetry modal">
		<img src="<?php echo $args['plugin_logo']; ?>" width="<?php echo $args['plugin_logo_width']; ?>"
			 height="<?php echo $args['plugin_logo_height']; ?>" alt="<?php echo $args['plugin_logo_alt']; ?>"
			 class="plugin-logo">
		<h1 class="stellarwp-telemetry-header">
			<?php echo $args['heading']; ?>
		</h1>
		<div class="intro">
			<?php echo $args['intro']; ?>
		</div>
		<form method="get">
			<ul class="uninstall_reasons">
				<?php foreach ( $args['uninstall_reasons'] as $key => $item ) : ?>
					<li>
						<input type="radio" name="uninstall_reason" id="reason-<?php echo $key; ?>" value="<?php echo $item['uninstall_reason']; ?>"
							   data-uninstall-reason-id="<?php echo $item['uninstall_reason_id']; ?>">
						<label for="reason-<?php echo $key; ?>">
							<?php echo $item['uninstall_reason']; ?>
							<?php if ( isset( $item['show_comment'] ) && $item['show_comment'] ) { ?>
								<textarea name="comment" placeholder="<?php echo __( 'Tell us more...', 'stellarwp-telemetry' ); ?>"></textarea>
							<?php } ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="error-message">
				<?php echo __( 'Please select a reason', 'stellarwp-telemetry' ); ?>
			</div>
			<footer>
				<button data-js="skip-interview" class="btn-grey" type="button">
					<?php echo __( 'Skip', 'stellarwp-telemetry' ); ?>
				</button>
				<button data-js="submit-telemetry" class="btn-primary" type="submit" name="deactivate" value="true">
					<?php echo __( 'Deactivate', 'stellarwp-telemetry' ); ?>
				</button>
			</footer>
		</form>
	</section>
</div>
