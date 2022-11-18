<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */
?>
<style>
	.stellarwp-telemetry.wrapper {
		position: fixed;
		bottom: 0;
		right: 0;
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		z-index: 999999;
		display: flex;
		justify-content: center;
		align-items: center;
		transition: all 0.3s ease-in-out;
	}

	.stellarwp-telemetry.modal {
		position: absolute;
		z-index: 999999;
		display: flex;
		flex-direction: column;
		padding: 32px;
		width: 90%;
		top: 47px;
		left: 32px;
		background: #fff;
		box-shadow: 0 0 32px rgba(0, 0, 0, 0.1);
		border-radius: 4px;
	}

	.stellarwp-telemetry h1 {
		font-family: 'SF Pro Text', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
		font-size: 18px;
		font-weight: 500;
		line-height: 24px;
		letter-spacing: -0.02em;
		color: #000;
	}

	.stellarwp-telemetry {
		font-family: 'SF Pro Text', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
		font-size: 14px;
		font-weight: 400;
		line-height: 18px;
		color: #4E4E4E;
	}

	.stellarwp-telemetry .btn-primary,
	.stellarwp-telemetry .btn-text {
		padding: 6px 12px;
		background: #0047FF;
		border-radius: 4px;
		color: #fff;
		text-decoration: none;
		border: 0;
		outline: none;
		transition: all 0.1s ease-in-out;
		cursor: pointer;
	}

	.stellarwp-telemetry .btn-primary:hover {
		background: #0032b7;
	}

	.stellarwp-telemetry .btn-text {
		background: #fff;
		color: #000;
	}

	.stellarwp-telemetry .links a:hover,
	.stellarwp-telemetry .btn-text:hover {
		text-decoration: underline;
	}

	.stellarwp-telemetry .links {
		display: flex;
		gap: 16px;
		font-size: 12px;
		line-height: 16px;
	}

	.stellarwp-telemetry .links a {
		color: #0047FF;
		text-decoration: none;
	}
</style>


<script>
	// JS document on ready event
	document.addEventListener("DOMContentLoaded", function () {
		let wrapper = document.getElementById("modal-wrapper");

		document.getElementById("close-modal").addEventListener("click", function (event) {
			event.preventDefault();
			close_modal(wrapper);
		});
	});

	function close_modal(wrapper) {
		wrapper.parentNode.removeChild(wrapper);
	}
</script>

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
			<form method="post" action="">
				<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
				<input type="hidden" name="action" value="stellarwp-telemetry">
				<button id="agree-modal" class="btn-primary" type="submit" name="optin-agreed" value="true">Allow &amp; Continue</button>
				<button id="close-modal" class="btn-text" type="submit" name="optin-agreed" value="false">Skip</button>
			</form>
		</footer>
	</section>
</div>
