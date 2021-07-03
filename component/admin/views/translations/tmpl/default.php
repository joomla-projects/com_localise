<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'com_localise/localise.css', null, true);
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');

if (version_compare(JVERSION, '4.0', 'ge'))
{
	JHtml::_('behavior.core');
}
else
{
	JHtml::_('behavior.framework', true);
}

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$params     = JComponentHelper::getParams('com_localise');
$ref_tag    = $params->get('reference', 'en-GB');
?>

<form action="<?php echo JRoute::_('index.php?option=com_localise&view=translations');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filter'); ?>
	<?php echo $this->loadTemplate('legend'); ?>
	<?php if ($ref_tag == 'en-GB') : ?>
		<?php echo $this->loadTemplate('references'); ?>
	<?php endif; ?>
	<table class="table table-striped" id="localiseList">
		<thead>
			<?php echo $this->loadTemplate('head'); ?>
		</thead>
		<tfoot>
			<?php echo $this->loadTemplate('foot'); ?>
		</tfoot>
		<tbody>
			<?php echo $this->loadTemplate('body'); ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<!-- End Content -->
</form>
