<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = (isset($this->state->params)) ? $this->state->params : new JObject;
?>
<!-- Begin Sidebar using custom submenu layout -->
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
	<hr />
	<div class="filter-select hidden-phone">
		<h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></h4>
		<?php foreach($this->form->getFieldset('select') as $field): ?>
			<?php if ($field->type != "Spacer") : ?>
				<?php echo $field->input; ?>
				<hr class="hr-condensed"/>
			<?php else : ?>
				<?php echo $field->label; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
<!-- End Sidebar -->
<!-- Begin Content -->
<div id="j-main-container" class="span10">
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => false))); ?>
	<div class="clearfix"></div>

