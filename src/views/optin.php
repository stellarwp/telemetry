<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */
?>
<div id="modal-wrapper" class="stellarwp-telemetry wrapper">
	<section class="stellarwp-telemetry modal">
		<header>
			<img src="<?php echo $args['plugin_logo']; ?>" width="<?php echo $args['plugin_logo_width']; ?>"
			     height="<?php echo $args['plugin_logo_height']; ?>" alt="<?php echo $args['plugin_logo_alt']; ?>"/>
			<h1 class="stellarwp-telemetry-header">We hope you love <?php echo $args['plugin_name']; ?>.</h1>
		</header>
		<main>
			<p>Hi, <?php echo $args['user_name']; ?>! This is an invitation to help our StellarWP community.
				If you opt-in, some data about your usage of <?php echo $args['plugin_name']; ?> and future
				StellarWP Products will be shared with our teams (so they can work their butts off to improve).
				We will also share some helpful info on WordPress, and our products from time to time.
				And if you skip this, that’s okay! Our products still work just fine.</p>
			<ul class="links">
				<li><a href="<?php echo $args['permissions_url']; ?>" target="_blank">What permissions are being
						granted?</a></li>
				<li><a href="<?php echo $args['tos_url']; ?>" target="_blank">Terms of Service</a></li>
				<li><a href="<?php echo $args['privacy_url']; ?>" target="_blank">Privacy Policy</a></li>
			</ul>
		</main>
		<footer>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
				<input type="hidden" name="action" value="stellarwp-telemetry">
				<button id="agree-modal" class="btn-primary" type="submit" name="optin-agreed" value="true">Allow &amp; Continue</button>
				<button id="close-modal" class="btn-text" type="button">Skip</button>
			</form>
		</footer>
	</section>
</div>
