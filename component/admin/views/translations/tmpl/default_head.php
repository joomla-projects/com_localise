<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<tr>
		<th width="20" class="center hidden-phone">#</th>
		<th width="100" class="center hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_INFORMATION'); ?></th>
		<th width="50" class="center"><?php echo JText::_('COM_LOCALISE_TOOLBAR_PACKAGES_LANGUAGE'); ?></th>
		<th width="100" class="center hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_LANGUAGES_CLIENT'); ?></th>
		<th width="250" class="title"><?php echo JHtml::_('searchtools.sort', 'COM_LOCALISE_HEADING_LANGUAGES_FILES', 'filename', $listDirn, $listOrder); ?></th>
		<th width="120" class="center"><?php echo JHtml::_('searchtools.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_TRANSLATED', 'completed', $listDirn, $listOrder); ?></th>
		<th width="120" class="center"><?php echo JHtml::_('searchtools.sort', 'COM_LOCALISE_HEADING_TRANSLATIONS_PHRASES', 'translated', $listDirn, $listOrder); ?></th>
		<th width="100" class="hidden-phone"><?php echo JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_AUTHOR'); ?></th>
	</tr>
<?php endif; ?>
