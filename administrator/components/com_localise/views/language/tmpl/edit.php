<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'language.cancel' || document.formvalidator.isValid(document.id('localise-language-form')))
		{
			Joomla.submitform(task, document.getElementById('localise-language-form'));
		}
	}
</script>

<form action="" method="post" name="adminForm" id="localise-language-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Localise Language -->
		<div class="span12 form-horizontal">
			<fieldset>
				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => $this->ftp ? 'ftp' : 'default')); ?>
					<?php if ($this->ftp) : ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'ftp', JText::_($ftpSets['ftp']->label, true)); ?>
						<?php if (!empty($ftpSets['ftp']->description)):?>
						<p class="tip"><?php echo JText::_($ftpSets['ftp']->description); ?></p>
						<?php endif;?>
						<?php if (JError::isError($this->ftp)): ?>
						<p class="error"><?php echo JText::_($this->ftp->message); ?></p>
						<?php endif; ?>
						<?php foreach($this->formftp->getFieldset('ftp',false) as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endif; ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'default', JText::_($fieldSets['default']->label, true)); ?>
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
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'author', JText::_($fieldSets['author']->label, true)); ?>
						<?php if (!empty($fieldSets['author']->description)):?>
						<p class="tip"><?php echo JText::_($fieldSets['author']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('author') as $field): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_($fieldSets['permissions']->label, true)); ?>
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
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<input type="hidden" name="task" value="" />
					<?php echo JHtml::_('form.token'); ?>

				<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			</fieldset>
		</div>
		<!-- End Localise Language -->
	</div>
</form>
