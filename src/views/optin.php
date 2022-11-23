<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */
?>

<div class="stellarwp-telemetry stellarwp-telemetry-modal stellarwp-telemetry-modal--active" data-js="optin-modal">
	<section class="stellarwp-telemetry-modal__inner">
		<header>
			<img src="<?php echo $args['plugin_logo']; ?>" width="<?php echo $args['plugin_logo_width']; ?>" height="<?php echo $args['plugin_logo_height']; ?>" alt="<?php echo $args['plugin_logo_alt']; ?>"/>
			<h1 class="stellarwp-telemetry__title">We hope you love <?php echo $args['plugin_name']; ?>.</h1>
		</header>
		<main>
			<p>Hi, <?php echo $args['user_name']; ?>! This is an invitation to help our StellarWP community.
				If you opt-in, some data about your usage of <?php echo $args['plugin_name']; ?> and future StellarWP Products will be shared with our teams (so they can work their butts off to improve).
				We will also share some helpful info on WordPress, and our products from time to time.
				And if you skip this, that’s okay! Our products still work just fine.</p>
			<ul class="stellarwp-telemetry-links">
				<li><a href="<?php echo $args['permissions_url']; ?>" target="_blank" class="stellarwp-telemetry-links__link">What permissions are being granted?</a></li>
				<li><a href="<?php echo $args['tos_url']; ?>" target="_blank" class="stellarwp-telemetry-links__link">Terms of Service</a></li>
				<li><a href="<?php echo $args['privacy_url']; ?>" target="_blank" class="stellarwp-telemetry-links__link">Privacy Policy</a></li>
			</ul>
		</main>
		<footer>
			<form method="post" action="">
				<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
				<input type="hidden" name="action" value="stellarwp-telemetry">
				<button class="stellarwp-telemetry-btn-primary" type="submit" name="optin-agreed" value="true">Allow &amp; Continue</button>
				<button data-js="close-modal" class="stellarwp-telemetry-btn-text" type="submit" name="optin-agreed" value="false">Skip</button>
			</form>
		</footer>
	</section>
</div>
