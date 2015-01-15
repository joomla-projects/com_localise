<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$user     = JFactory::getUser();
?>
<?php foreach($this->items as $i => $item) : ?>
	<?php if ($item->name !== 'core') : ?>
		<?php $canEdit = $user->authorise('localise.edit', 'com_localise.'.$item->id); ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td width="20" class="center hidden-phone"><?php echo $i + 1; ?></td>
			<td width="20" class="center hidden-phone">
				<?php if ($item->checked_out) : ?>
					<?php $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0; ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'packages.', $canCheckin); ?>
					<input type="checkbox" id="cb<?php echo $i;?>" class="hidden" name="cid[]" value="<?php echo $item->id;?>">
				<?php else:?>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				<?php endif; ?>
			</td>
			<td>
				<span title="<?php echo JText::_($item->title); ?>" class="hasTooltip localise-icon "></span>
				<?php if (!$canEdit) : ?>
					<span title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_PACKAGES_READONLY'); ?>"  class="hasTooltip localise-icon icon-warning"></span>
					<?php echo JText::sprintf('COM_LOCALISE_TEXT_PACKAGES_TITLE', JText::_($item->title), $item->name); ?>
				<?php elseif ($item->writable && $canEdit) : ?>
					<span class="localise-icon">
					<?php if ($item->core) : ?>
						<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_localise&task=package.edit&id=' . $item->id); ?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_PACKAGES_EDIT'); ?>">
						<?php echo JText::sprintf('COM_LOCALISE_TEXT_PACKAGES_TITLE', JText::_($item->title), $item->name); ?>
						</a>
					<?php else: ?>
						<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_localise&task=packagefile.edit&id=' . $item->id); ?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_PACKAGES_EDIT'); ?>">
						<?php echo JText::sprintf('COM_LOCALISE_TEXT_PACKAGES_TITLE', JText::_($item->title), $item->name); ?>
						</a>
					<?php endif; ?>
					</span>
				<?php else : ?>
					<span title="<?php echo JText::sprintf($canEdit ? 'COM_LOCALISE_TOOLTIP_PACKAGES_NOTWRITABLE':'COM_LOCALISE_TOOLTIP_PACKAGES_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT) + 1)); ?>"  class="hasTooltip localise-icon icon-16-warning">
						<?php echo JText::sprintf('COM_LOCALISE_TEXT_PACKAGES_TITLE',$item->title,$item->name); ?>
					</span>
				<?php endif; ?>
			</td>
			<td>
				<?php echo $item->language; ?>
			</td>
			<td>
				<?php echo $item->version; echo (!empty($item->packversion) ? '.' . $item->packversion : ''); ?>
			</td>
			<td>
				<?php if ($item->core) : ?>
					<span class="icon-16-core"></span>
					<?php echo JText::_('COM_LOCALISE_CORE'); ?>
				<?php else: ?>
					<span class="icon-16-file"></span>
					<?php echo JText::_('COM_LOCALISE_FILE'); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>
<?php endforeach; ?>
