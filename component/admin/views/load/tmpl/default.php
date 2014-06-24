<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'com_localise/localise.css', null, true);
JHtml::_('formbehavior.chosen', 'select');

$document	= JFactory::getDocument();
$document->addScript("components/com_localise/js/load.js");

?>
<form action="<?php echo JRoute::_('index.php?option=com_localise&view=load');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div>

		<div id="process">
			<p class="text" id="title"><?php echo JText::_('Click button to start'); ?></p>
			<!-- @@ TODO: add progress bar
				<div id="pb4" data-progress="1%"></div>
			-->
			<div id="counter">
				<i><small><b><span id="currItem">0</span></b> items /
				<b><span id="totalItems">0</span></b> items</small></i>
			</div>
		</div>

		<div>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
	<!-- End Content -->
</form>
