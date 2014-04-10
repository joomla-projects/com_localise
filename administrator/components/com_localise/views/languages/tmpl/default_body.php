<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_languages');
$user   = JFactory::getUser();
?>
<?php foreach($this->items as $i => $item): ?>
<?php $canEdit   = $user->authorise('localise.edit', 'com_localise.'.$item->id);?>
<?php $canDelete = $user->authorise('localise.delete', 'com_localise.'.$item->id);?>
<tr class="row<?php echo $i % 2; ?>">
	<td width="20" class="center hidden-phone">
		<?php if ($item->checked_out) : ?>
		<?php echo JHtml::_('jgrid.checkedout', $item->editor, $item->checked_out_time); ?>
		<?php elseif ($canDelete): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_localise&task=language.delete&id='.$item->id.'&client='.$item->client.'&tag='.$item->tag); ?>" class="hasTooltip" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_DELETE'); ?>">
			<i class="icon-16-delete"></i>
		</a>
		<?php endif; ?>
	</td>
	<td>
		<?php if ($item->writable && $canEdit): ?>
		<span title="" class="localise-icon">
			<a href="<?php echo JRoute::_('index.php?option=com_localise&task=language.edit&id='.$item->id.'&client='.$item->client.'&tag='.$item->tag); ?>" class="hasTooltip" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_EDIT'); ?>">
			<?php echo JText::sprintf('COM_LOCALISE_TEXT_LANGUAGES_TITLE', $item->tag, $item->name); ?>
			</a>
		</span>
		<?php else: ?>
		<i class="icon-warning hasTooltip" title="<?php echo JText::sprintf($canEdit ? 'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTWRITABLE':'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT) + 1)); ?>"></i>
		<span title="<?php echo JText::sprintf($canEdit ? 'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTWRITABLE':'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT) + 1)); ?>"  class="hasTooltip">
			<?php echo JText::sprintf('COM_LOCALISE_TEXT_LANGUAGES_TITLE', $item->tag, $item->name); ?>
		</span>
		<?php endif; ?>
	</td>
	<td class="center"><?php echo JText::_(ucfirst($item->client)); ?></td>
	<td class="center"><?php if ($item->tag == $params->get($item->client, 'en-GB') && $item->client != 'installation'): ?>
		<i title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_DEFAULT');?>"  class="hasTooltip icon-16-default"></i>
		<?php endif; ?>
	</td>
	<td class="center">
		<a href="<?php echo JRoute::_('index.php?option=com_localise&view=translations&filters[select][client]=' . $item->client . '&filters[select][tag]=' . $item->tag); ?>" title="<?php echo JText::sprintf('COM_LOCALISE_TOOLTIP_LANGUAGES_NUMBER_OF_FILES', $item->nbfiles); ?>" class="btn btn-micro hasTooltip">
			<i class="icon-16-translations"></i>
		</a>
	</td>
	<td class="center hidden-phone"><?php if (isset($item->version)) echo $item->version; ?></td>
	<td class="center hidden-phone"><?php if (isset($item->creationDate)) echo JHtml::_('date', $item->creationDate, JText::_('DATE_FORMAT_LC3')); ?></td>
	<td class="hidden-phone">
		<span class="hasTooltip" title="<b><?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_AUTHOR_INFORMATION') . '</b><br />' . (isset($item->authorEmail) ? ($item->authorEmail . '<br />') :'') . (isset($item->authorUrl)?$item->authorUrl:''); ?>">
			<?php if (isset($item->author)) echo $item->author; ?>
		</span>
	</td>
</tr>
<?php endforeach; ?>
