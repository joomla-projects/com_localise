<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th width="20" class="center hidden-phone"></th>
	<th class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_LANGUAGES_NAME', 'name', $listDirn, $listOrder); ?></th>
	<th class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_LANGUAGES_TAG', 'tag', $listDirn, $listOrder); ?></th>
	<th class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_LANGUAGES_CLIENT', 'client', $listDirn, $listOrder); ?></th>
	<th class="center"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_FILES'); ?></th>
	<th class="center"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_DEFAULT'); ?></th>
	<th class="center hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_VERSION'); ?></th>
	<th class="center hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_DATE'); ?></th>
	<th class="hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_AUTHOR'); ?></th>
</tr>
