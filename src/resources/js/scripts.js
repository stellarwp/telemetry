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

			// Answer Click
			$exitInterview.on( 'change', '[name="uninstall_reason"]', function () {
				let $this = $(this);
				let $wrapper = $this.closest('li');
				let $reason = $wrapper.find('[name="comment"]');

				$exitInterview.find('ul.uninstall_reasons li.active').removeClass('active');
				$exitInterview.find('ul.uninstall_reasons li [name="comment"]').val('');
				$exitInterview.find('.error-message').hide();

				if ( ! $reason.length ) {
					return;
				}

				$wrapper.addClass('active');
			});

			// Submit Button
			$exitInterview.on( 'click', '[data-js="submit-telemetry"]', function ( e ) {
				e.preventDefault();

				let $form = $('.stellarwp-telemetry.exit-interview').find('form');

				let data = {
					action: stellarwpTelemetry.exit_interview.action,
					nonce: stellarwpTelemetry.exit_interview.nonce,
				};

				// Get uninstall_reason value
				let $reason = $form.find('[name="uninstall_reason"]:checked');

				if ( ! $reason.length ) {
					$exitInterview.find('.error-message').show();
					return;
				}

				data['uninstall_reason_id'] = $reason.data('uninstall-reason-id');
				data['uninstall_reason']    = $reason.val();

				// Get comment value if exists
				let $comment = $reason.closest('li').find('[name="comment"]');

				if ( $comment.length ) {
					if ( ! $comment.val() ) {
						$exitInterview.find('.error-message').show();
						return;
					}

					data['comment'] = $comment.val();
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
