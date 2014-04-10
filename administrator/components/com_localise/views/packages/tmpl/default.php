<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHTML::_('stylesheet','com_localise/localise.css', null, true);
JHtml::_('formbehavior.chosen', 'select');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$saveOrder  = $listOrder == 'tag';
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function submitbutton(task)
	{
		if (task == 'package.download')
		{
			var s=null;
			for (var i = 0, n = document.adminForm.elements.length; i < n; i++)
			{
				var e = document.adminForm.elements[i];
				if (e.type == 'checkbox' && e.name=='cid[]' && e.checked)
				{
					s = e.value;
					break;
				}
			}
			if (s!=null)
			{
				SqueezeBox.open('index.php?option=com_localise&task=package.download&cid[]='+s, {handler: 'iframe', size: {x: 500, y: 500}});
			}
		}
		else
		{
			submitform(task);
		}
	}
// -->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_localise&view=packages');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filter'); ?>
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
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
	<!-- End Content -->
</form>
