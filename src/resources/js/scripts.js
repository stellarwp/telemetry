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
