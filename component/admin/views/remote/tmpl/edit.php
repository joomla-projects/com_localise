<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');
JHtml::_('jquery.framework');

$fieldSets = $this->form->getFieldsets();

JFactory::getDocument()->addScriptDeclaration("
	(function($){
		$(document).ready(function () {
			$('.fileupload').click(function(e){

				var form   = $('#filemodalForm');

				// Assign task
				form.find('input[name=task]').val('package.uploadOtherFile');

				// Submit the form
				if (confirm('" . JText::_('COM_LOCALISE_MSG_FILES_VALID_IMPORT') . "'))
				{
					form.trigger('submit');
				}

				// Avoid the standard link action
				e.preventDefault();
			});
		});
	})(jQuery);
");
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if ((task == 'package.apply' || task == 'package.save') && document.formvalidator.isValid(document.id('localise-package-form')))
		{
			if (confirm(Joomla.JText._('COM_LOCALISE_MSG_CONFIRM_PACKAGE_SAVE')))
			{
				Joomla.submitform(task, document.getElementById('localise-package-form'));
			}
		}
		else if (task == 'package.cancel' || task == 'package.download')
		{
			Joomla.submitform(task, document.getElementById('localise-package-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_localise&view=remote&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="localise-package-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Localise Package -->
		<div class="span12 form-horizontal">
			<fieldset>
						<div class="span6">
							<?php if (!empty($fieldSets['default']->description)):?>
								<p class="tip"><?php echo JText::_($fieldSets['default']->description); ?></p>
							<?php endif;?>
							<?php foreach($this->form->getFieldset('default') as $field): ?>
									<div class="control-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
							<?php endforeach; ?>
						</div>
						<div class="span6">
							<?php echo JText::_($fieldSets['translations']->label); ?>
							<?php if (!empty($fieldSets['translations']->description)):?>
									<p class="tip"><?php echo JText::_($fieldSets['translations']->description); ?></p>
							<?php endif;?>
							<?php foreach($this->form->getFieldset('translations') as $field): ?>
									<div class="control-group form-vertical">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
							<?php endforeach; ?>
						</div>
						<?php if (!empty($fieldSets['permissions']->description)):?>
								<p class="tip"><?php echo JText::_($fieldSets['permissions']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('permissions') as $field): ?>
								<div class="control-group form-vertical">
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
						<?php endforeach; ?>

					<input type="hidden" name="task" value="" />

					<?php echo JHtml::_('form.token'); ?>

			</fieldset>
		</div>
		<!-- End Localise Package -->
	</div>
</form>
