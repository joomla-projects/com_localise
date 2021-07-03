<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHTML::_('stylesheet', 'com_localise/localise.css', null, true);

$params            = JComponentHelper::getParams('com_localise');
$ref_tag           = $params->get('reference', 'en-GB');
$allow_develop     = $params->get('gh_allow_develop', 0);
$saved_ref         = $params->get('customisedref', 0);
$source_ref        = $saved_ref;
$istranslation     = $this->item->istranslation;
$installed_version = new JVersion;
$installed_version = $installed_version->getShortVersion();

	if ($saved_ref == 0)
	{
		$source_ref = $installed_version;
	}

	if ($saved_ref != 0 && $allow_develop == 1 && $ref_tag == 'en-GB' && $istranslation == 0)
	{
		JFactory::getApplication()->enqueueMessage(
		JText::sprintf('COM_LOCALISE_NOTICE_EDIT_REFERENCE_HAS_LIMITED_USE', $source_ref),
		'notice');
	}

$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'translation.cancel' || document.formvalidator.isValid(document.id('localise-translation-form')))
		{
			<?php echo $this->form->getField('source')->save(); ?>
			Joomla.submitform(task, document.getElementById('localise-translation-form'));
		}
	}
</script>
<form action="" method="post" name="adminForm" id="localise-translation-form" class="form-validate">
	<?php if ($this->ftp) : ?>
	<fieldset class="panelform">
		<legend><?php echo JText::_($ftpSets['ftp']->label); ?></legend>
		<?php if (!empty($ftpSets['ftp']->description)):?>
		<p class="tip"><?php echo JText::_($ftpSets['ftp']->description); ?></p>
		<?php endif;?>
		<?php if (JError::isError($this->ftp)): ?>
		<p class="error"><?php echo JText::_($this->ftp->message); ?></p>
		<?php endif; ?>
		<ul class="adminformlist">
			<?php foreach($this->formftp->getFieldset('ftp',false) as $field): ?>
			<?php if ($field->hidden): ?>
			<?php echo $field->input; ?>
			<?php else:?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<?php endif; ?>
	<fieldset class="panelform">
		<legend><?php echo JText::_($fieldSets['source']->label); ?></legend>
		<?php if (isset($fieldSets['source']->description)):?>
		<p class="label"><?php echo JText::_($fieldSets['source']->description); ?></p>
		<?php endif;?>
		<div class="clr"></div>
		<div class="editor-border">
			<?php echo $this->form->getInput('source'); ?>
		</div>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
