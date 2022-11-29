<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */
?>

<div class="stellarwp-telemetry stellarwp-telemetry-modal stellarwp-telemetry-modal--active" data-js="optin-modal">
	<section class="stellarwp-telemetry-modal__inner">
		<header>
			<img src="<?php echo esc_url( $args['plugin_logo'] ); ?>" width="<?php echo esc_attr( $args['plugin_logo_width'] ); ?>" height="<?php echo esc_attr( $args['plugin_logo_height'] ); ?>" alt="<?php echo esc_attr( $args['plugin_logo_alt'] ); ?>"/>
			<h1 class="stellarwp-telemetry__title">
				<?php echo esc_attr( $args['heading'] ); ?>
			</h1>
		</header>
		<main>
			<p>
				<?php echo esc_attr( $args['intro'] ); ?>
			</p>
			<ul class="stellarwp-telemetry-links">
				<li>
					<a href="<?php echo esc_url( $args['permissions_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo __( 'What permissions are being granted?', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( $args['tos_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo __( 'Terms of Service', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( $args['privacy_url'] ); ?>" target="_blank" class="stellarwp-telemetry-links__link">
						<?php echo __( 'Privacy Policy', 'stellarwp-telemetry' ); ?>
					</a>
				</li>
			</ul>
		</main>
		<footer>
			<form method="post" action="">
				<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
				<input type="hidden" name="action" value="stellarwp-telemetry">
				<button class="stellarwp-telemetry-btn-primary" type="submit" name="optin-agreed" value="true">
					<?php echo __( 'Allow &amp; Continue', 'stellarwp-telemetry' ); ?>
				</button>
				<button data-js="close-modal" class="stellarwp-telemetry-btn-text" type="submit" name="optin-agreed" value="false">
					<?php echo __( 'Skip', 'stellarwp-telemetry' ); ?>
				</button>
			</form>
		</footer>
	</section>
</div>
