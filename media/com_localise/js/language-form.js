(function($){
	$(document).ready(function () {

		/**
		 * Tweak the form for installation languages
		 *
		 * @return  void
		 */
		function enableInstallationMode() {

			// Change attributes
			$('#jform_locale').removeAttr('required').removeClass('required');
			$('#jform_locale-lbl').removeClass('required').find('span.star').remove();
			$('#jform_calendar').removeAttr('required').removeClass('required');
			$('#jform_calendar-lbl').removeClass('required').find('span.star').remove();

			// Hide fields
			$('#jform_locale').closest('.control-group').fadeOut()
			$('#jform_calendar').closest('.control-group').fadeOut();
			$('#jform_firstDay-lbl').closest('.control-group').fadeOut();
			$('#jform_weekEnd-lbl').closest('.control-group').fadeOut();
			$('#jform_authorEmail').closest('.control-group').fadeOut();
			$('#jform_authorUrl').closest('.control-group').fadeOut();
			$('#jform_copyright').closest('.control-group').fadeOut();
		}

		/**
		 * Switch the form to standard mode
		 *
		 * @return  void
		 */
		function disableInstallationMode() {
			var isDisabled = $('#jform_locale-lbl').hasClass('required');

			if (!isDisabled) {

				// Change attributes
				$('#jform_locale').attr('required', 'required').addClass('required');
				$('#jform_locale-lbl').addClass('required').append('<span class=\"star\"> *</span>');
				$('#jform_calendar').attr('required', 'required').addClass('required');
				$('#jform_calendar-lbl').addClass('required').append('<span class=\"star\"> *</span>');

				// Show fields
				$('#jform_locale').closest('.control-group').fadeIn();
				$('#jform_calendar').closest('.control-group').fadeIn();
				$('#jform_firstDay').closest('.control-group').fadeIn();
				$('#jform_weekEnd').closest('.control-group').fadeIn();
				$('#jform_authorEmail').closest('.control-group').fadeIn();
				$('#jform_authorUrl').closest('.control-group').fadeIn();
				$('#jform_copyright').closest('.control-group').fadeIn();
			}
		}

		$('#jform_client').change(function(){
			var client = $(this).val();

			if (client == 'installation') {
				enableInstallationMode();
			} else {
				disableInstallationMode();
			}
		});

		$('#jform_client').trigger('change');
	});
})(jQuery);
