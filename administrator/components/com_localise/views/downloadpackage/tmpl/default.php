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
?>
<script type="text/javascript">
<!--
	function submitbutton(task)
	{
		if (task == 'package.cancel' || document.formvalidator.isValid(document.id('localise-downloadpackage-form')))
		{
			submitform(task);
		}
	}
// -->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_localise&view=exportpackage&format=raw');?>" method="post" name="adminForm" id="localise-downloadpackage-form" class="form-validate">
	<div class="row-fluid">
		<div class="span12 form-horizontal">
			<?php if ($this->item->standalone) : ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_LOCALISE_GROUP_DOWNLOADPACKAGE');?></legend>
				<?php foreach($this->form->getFieldset('default') as $field): ?>
				<div class="control-group">
					<?php if (!$field->hidden): ?>
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<?php endif; ?>
					<div class="controls">
					<?php echo $field->input; //for submit button: window.top.setTimeout('window.parent.SqueezeBox.close()', 2000);?>
					</div>
				</div>
				<?php endforeach; ?>
				<button type="button" class="btn btn-primary" onclick="javascript: submitbutton('display');"><?php echo JText::_('JSubmit');?></button>
			<?php endif;?>
				<button type="button" class="btn" onclick="javascript: window.parent.SqueezeBox.close();"><?php echo JText::_('JCancel');?></button>
			</fieldset>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
