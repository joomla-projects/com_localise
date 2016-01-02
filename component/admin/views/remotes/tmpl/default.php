<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHTML::_('stylesheet', 'com_localise/localise.css', null, true);

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
	};

	function fetchLog() {
		jQuery.ajax({
			url: 'index.php?option=com_localise&task=remotes.getlog&tmpl=component',
			success: function(data) {
				var e = jQuery('#log');
				e.html(data);
				e[0].scrollTop = e[0].scrollHeight;
			},

			complete: function() {
				setTimeout(fetchLog, 1000);
			}

		});
	}

	function fetchRemotes() {
		if (document.adminForm.boxchecked.value == 0){
			alert('Please first make a selection from the list');
		}
		else{
			fetchLog();

			Joomla.submitbutton('remotes.getremote')
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_localise&view=remotes');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filter'); ?>

	<div id="log"></div>

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
	<!-- End Content -->
</form>
