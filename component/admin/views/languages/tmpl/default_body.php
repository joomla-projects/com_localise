<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_languages');
$user   = JFactory::getUser();

JFactory::getDocument()->addScriptDeclaration("
	(function($){
		$(document).ready(function () {
			$('.js-list-delete-item').click(function(e){

				var form   = $('#adminForm');
				var client = $(this).attr('data-client');
				var id     = $(this).attr('data-id');
				var tag    = $(this).attr('data-tag');

				// Assign task
				form.find('input[name=task]').val('language.delete');

				// New fields for required data
				form.append('<input type=\"hidden\" name=\"client\" value=\"' + client + '\">');
				form.append('<input type=\"hidden\" name=\"id\" value=\"' + id + '\">');
				form.append('<input type=\"hidden\" name=\"tag\" value=\"' + tag + '\">');

				// Submit the form
				if (confirm('" . JText::_('COM_LOCALISE_MSG_LANGUAGES_VALID_DELETE') . "'))
				{
					form.trigger('submit');
				}

				// Avoid the standard link action
				e.preventDefault();
			});
		});
	})(jQuery);
");
?>
<?php foreach($this->items as $i => $item): ?>
	<?php $canEdit   = $user->authorise('localise.edit', 'com_localise.'.$item->id);?>
	<?php $canDelete = $user->authorise('localise.delete', 'com_localise.'.$item->id);?>
	<tr class="row<?php echo $i % 2; ?>">
		<td width="20" class="center hidden-phone">
			<?php if ($item->checked_out) : ?>
				<?php $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0; ?>
				<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'languages.', $canCheckin); ?>
				<input type="checkbox" id="cb<?php echo $i; ?>" class="hidden" name="cid[]" value="<?php echo $item->id; ?>">
			<?php elseif ($canDelete): ?>
				<a href="#" data-id="<?php echo $item->id; ?>" data-client="<?php echo $item->client; ?>" data-tag="<?php echo $item->tag; ?>" class="hasTooltip js-list-delete-item" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_DELETE'); ?>">
					<i class="icon-16-delete"></i>
				</a>
			<?php endif; ?>
		</td>
		<td>
			<?php if ($item->writable && $canEdit): ?>
				<span title="" class="localise-icon">
					<a href="<?php echo JRoute::_('index.php?option=com_localise&task=language.edit&id='.$item->id.'&client='.$item->client.'&tag='.$item->tag); ?>" class="hasTooltip" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_EDIT'); ?>">
					<?php echo $item->name; ?>
					</a>
				</span>
			<?php else: ?>
				<i class="icon-warning hasTooltip" title="<?php echo JText::sprintf($canEdit ? 'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTWRITABLE':'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT) + 1)); ?>"></i>
				<span title="<?php echo JText::sprintf($canEdit ? 'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTWRITABLE':'COM_LOCALISE_TOOLTIP_LANGUAGES_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT) + 1)); ?>">
					<?php echo $item->name; ?>
				</span>
			<?php endif; ?>
		</td>
		<td class="center">
			<?php echo $item->tag; ?>
		</td>
		<td class="center">
			<?php echo JText::_(ucfirst($item->client)); ?>
		</td>
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_localise&view=translations&filters[select][client]=' . $item->client . '&filters[select][tag]=' . $item->tag); ?>" class="btn btn-micro hasTooltip">
				<i class="icon-16-translations"></i>
			</a>
		</td>
		<td class="center">
			<?php if ($item->tag == $params->get($item->client, 'en-GB') && $item->client != 'installation'): ?>
				<i title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_DEFAULT');?>"  class="hasTooltip icon-16-default"></i>
			<?php endif; ?>
		</td>
		<td class="center hidden-phone">
			<?php if (isset($item->version)) :
					echo $item->version;
				endif;
			?>
		</td>
		<td class="center hidden-phone">
			<?php if (isset($item->creationDate)) :
					echo $item->creationDate;
				endif;
			?>
		</td>
		<td class="hidden-phone">
			<span class="hasTooltip" title="<b><?php echo JText::_('COM_LOCALISE_TOOLTIP_LANGUAGES_AUTHOR_INFORMATION') . '</b><br />' . (isset($item->authorEmail) ? ($item->authorEmail . '<br />') :'') . (isset($item->authorUrl)?$item->authorUrl:''); ?>">
				<?php if (isset($item->author)) :
						echo $item->author;
					endif;
				?>
			</span>
		</td>
	</tr>
<?php endforeach; ?>
