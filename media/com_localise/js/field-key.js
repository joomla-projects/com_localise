(function($){
	$(document).ready(function () {

		$('.js-localise-btn-import').click(function(){
			var targetSelector = $(this).attr('data-import'),
				referenceSelector = targetSelector + '-reference',
				$targetField   = $('#' + targetSelector),
				$referenceField   = $('#' + referenceSelector);

			$targetField.val($referenceField.val()).trigger('focusout');
		});

		$('.js-localise-btn-translate').click(function(){
			var token = $(this).attr('data-token');

			AzureTranslator(this, [], 0, token);
		});

		$('.js-localise-field-translation').on('focusout', function(){
			var id = $(this).attr('id'),
				referenceValue = $('#' + id + '-reference').val(),
				originalValue = $('#' + id + '-original').val(),
				value = $(this).val(),
				currentStatus = $(this).attr('data-status'),
				newStatus = currentStatus;

			$(this).removeClass(currentStatus);

			if (currentStatus != 'extra') {
				if (value == '') {
					newStatus = 'untranslated';
				} else if (value === referenceValue) {
					newStatus = 'unchanged';
				} else {
					newStatus = 'translated';
				}
			}

			$(this).addClass(newStatus).attr('data-status', newStatus);
		});
	});
})(jQuery);
