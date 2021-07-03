<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();
$params    = JComponentHelper::getParams('com_localise');
$ref_tag   = $params->get('reference', 'en-GB');
$isNew     = empty($this->item->id);
$tag       = $this->item->tag ;
$client    = $this->item->client;

JHtml::_('script', 'media/com_localise/js/language-form.js', false, false, false, false);
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

<form action="<?php echo JRoute::_('index.php?option=com_localise&view=language&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="localise-language-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Localise Language -->
		<div class="span12 form-horizontal">
			<?php if ($isNew) : ?>
				<p><em><?php echo JText::_('COM_LOCALISE_COPY_REF_TO_NEW_LANG_FIRSTSAVE'); ?></em><p>
			<?php elseif (!$isNew && $client != 'installation') : ?>
				<p><em> <?php echo JText::sprintf('COM_LOCALISE_COPY_REF_TO_NEW_LANG_TIP', $ref_tag, $tag); ?></em></p>
			<?php endif; ?>
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

						<div class="span6">
						<?php if (!empty($fieldSets['default']->description)) : ?>
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
						<?php if (!empty($fieldSets['meta']->description)) : ?>
							<p class="tip"><?php echo JText::_($fieldSets['meta']->description); ?></p>
						<?php endif;?>
							<?php foreach ($this->form->getFieldset('meta') as $field) : ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach; ?>
							<?php if (version_compare(JVERSION, '3.7', 'ge')) : ?>
								<?php foreach ($this->form->getFieldset('metanew') as $field) : ?>
									<div class="control-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach; ?>
							<?php endif;?>
						</div>

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
