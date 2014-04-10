<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th width="20" class="center hidden-phone">#</th>
	<th width="100" class="center hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_INFORMATION'); ?></th>
	<th width="50" class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_TAG', 'tag', $listDirn, $listOrder); ?></th>
	<th width="250" class="title"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_NAME', 'filename', $listDirn, $listOrder); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_PATH', 'path', $listDirn, $listOrder); ?></th>
	<th width="120" class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_TRANSLATED', 'completed', $listDirn, $listOrder); ?></th>
	<th width="120" class="center"><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_PHRASES', 'translated', $listDirn, $listOrder); ?></th>
	<th width="100" class="hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_AUTHOR'); ?></th>
</tr>