<?php
// $args comes from load_template() in OptinTemplate.php
/** @var array $args */

use StellarWP\Telemetry\Exit_Interview_Subscriber;

?>
<style>
	.stellarwp-telemetry.exit-interview {
		position: fixed;
		visibility: hidden;
		pointer-events: none;
		opacity: 0;
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

	.stellarwp-telemetry.exit-interview.active {
		visibility: visible;
		pointer-events: all;
		opacity: 1;
	}

	.stellarwp-telemetry.modal {
		position: absolute;
		z-index: 999999;
		display: flex;
		flex-direction: column;
		padding: 32px;
		width: 90%;
		max-width: 585px;
		background: #fff;
		box-shadow: 0 0 32px rgba(0, 0, 0, 0.1);
		border-radius: 4px;
		text-align: center;
	}

	.stellarwp-telemetry img.plugin-logo {
		margin: auto;
	}

	.stellarwp-telemetry h1 {
		font-family: 'SF Pro Text', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
		font-size: 18px;
		font-weight: 500;
		line-height: 24px;
		letter-spacing: -0.02em;
		color: #000;
		margin: 1rem 0;
	}

	.stellarwp-telemetry .intro {
		font-size: 12px;
		font-weight: 400;
		line-height: 18px;
		letter-spacing: -0.01em;
		text-align: center;
	}

	.stellarwp-telemetry ul.questions {
		text-align: left;
		width: 85%;
		margin: 1rem auto;
	}

	.stellarwp-telemetry ul.questions li {
		font-size: 14px;
		font-weight: 400;
		line-height: 18px;
		letter-spacing: -0.01em;
		text-align: left;
		margin-bottom: 0.85rem;
		display: flex;
		padding: 0 1rem;
		border-radius: 4px;
	}

	.stellarwp-telemetry ul.questions li.active {
		background: rgb(238 238 238 / 45%);
		padding: 1rem;
	}

	.stellarwp-telemetry ul.questions li:last-child {
		margin-bottom: 0;
	}

	.stellarwp-telemetry ul.questions li input[type="radio"] {
		margin-top: 1px;
		margin-right: 1rem;
	}

	.stellarwp-telemetry ul.questions li label {
		width: 100%;
	}

	.stellarwp-telemetry ul.questions li textarea {
		width: 100%;
		height: 55px;
		border: 1px solid #DFDFDF;
		border-radius: 4px;
		padding: 0.5rem;
		margin-top: 0.5rem;
		display: none;
	}

	.stellarwp-telemetry ul.questions li.active textarea {
		display: block;
	}

	.stellarwp-telemetry .btn-primary,
	.stellarwp-telemetry .btn-grey {
		padding: 8px 12px;
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

	.stellarwp-telemetry .btn-grey {
		background: #eee;
		color: #000;
	}

	.stellarwp-telemetry .btn-grey:hover {
		background: #ddd;
	}
</style>

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
			<ul class="questions">
				<?php foreach ( $args['questions'] as $key => $item ) : ?>
					<li>
						<input type="radio" name="uninstall_reason" id="reason-<?php echo $key; ?>" value="<?php echo $item['question']; ?>">
						<label for="reason-<?php echo $key; ?>">
							<?php echo $item['question']; ?>
							<?php if ( $item['show_field'] ) { ?>
								<textarea name="comment" placeholder="<?php echo __( 'Tell us more...', 'stellarwp-telemetry' ); ?>"></textarea>
							<?php } ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
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

<script>
	(function ( $ ) {

		$('body').find('.stellarwp-telemetry.exit-interview').each( function ( index ) {
			let $exitInterview = $(this);
			let pluginSlug     = $exitInterview.data('plugin-slug');
			let redirectLink   = null;

			// Deactivate Button
			$('body').on( 'click', '#the-list .deactivate > a', function ( e ) {
				if ( 0 === $( this ).next( '[data-plugin-slug].telemetry-plugin-slug' ).length ) {
					return true;
				}

				if ( $( this ).next( '[data-plugin-slug].telemetry-plugin-slug' ).data( 'plugin-slug' ) !== pluginSlug ) {
					return true;
				}

				e.preventDefault();

				redirectLink = $(this).attr('href');
				$exitInterview.addClass('active');

				// Skip Button
				$exitInterview.on( 'click', '[data-js="skip-interview"]', function ( e ) {
					e.preventDefault();
					$exitInterview.removeClass('active');
					window.location.href = redirectLink;
				});

				// Question Click
				$exitInterview.on( 'change', '[name="uninstall_reason"]', function () {
					let $this = $(this);
					let $wrapper = $this.closest('li');
					let $reason = $wrapper.find('[name="comment"]');

					if ( ! $reason.length ) {
						return;
					}

					$exitInterview.find('ul.questions li.active').removeClass('active');
					$exitInterview.find('ul.questions li [name="comment"]').val('');
					$wrapper.addClass('active');
				});

				// Submit Button
				$exitInterview.on( 'click', '[data-js="submit-telemetry"]', function ( e ) {
					e.preventDefault();

					let $form = $('.stellarwp-telemetry.exit-interview').find('form');

					let data = {
						action: '<?php echo Exit_Interview_Subscriber::AJAX_ACTION; ?>',
						nonce: '<?php echo wp_create_nonce( Exit_Interview_Subscriber::AJAX_ACTION ); ?>',
					};

					// Get non empty values and add them to the data object
					$form.serializeArray().forEach( function ( item ) {
						if ( item.value ) {
							data[item.name] = item.value;
						}
					});

					// uninstall_reason is required
					if ( ! data.uninstall_reason ) {
						return;
					}

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: data,
					}).done(function () {
						// Redirect to the plugin page.
						window.location.href = redirectLink;
					});
				} );
			});
		});

	}( jQuery ));
</script>
