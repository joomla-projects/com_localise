<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th width="1%" class="nowrap center hidden-phone">#</th>
	<th width="1%" class="hidden-phone"> <?php echo JHtml::_('grid.checkall'); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_REMOTES_LANG', 'lang', $listDirn, $listOrder); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_REMOTES_SCOPE', 'scope', $listDirn, $listOrder); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_REMOTES_TYPE', 'type', $listDirn, $listOrder); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_REMOTES_USER', 'user', $listDirn, $listOrder); ?></th>
	<th><?php echo JHtml::_('grid.sort', 'COM_LOCALISE_HEADING_REMOTES_PROJECT', 'project', $listDirn, $listOrder); ?></th>
</tr>
