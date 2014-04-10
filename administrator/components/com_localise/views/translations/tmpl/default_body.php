<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app       = JFactory::getApplication('administrator');
$params    = JComponentHelper::getParams('com_localise');
$reference = $params->get('reference', 'en-GB');
$packages  = LocaliseHelper::getPackages();
$user      = JFactory::getUser();
$lang      = JFactory::getLanguage();
?>
<?php foreach ($this->items as $i=>$item): ?>
<?php $canEdit = $user->authorise('localise.edit', 'com_localise' . (isset($item->id) ? ('.'.$item->id) : '')); ?>
<tr class="<?php echo $item->state;?> row<?php echo $i%2; ?>">
	<td width="20" class="center hidden-phone"><?php echo $i + 1; ?></td>
	<td width="120" class="center hidden-phone">
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_STORAGE_'.$item->storage), 'inactive_class'=>'16-'. $item->storage, 'enabled' => false, 'translate'=>false)); ?>
		<?php if ($item->origin=='_thirdparty'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_ORIGIN_THIRDPARTY'),   'inactive_class'=>'16-thirdparty', 'enabled' => false, 'translate'=>false)); ?>
		<?php elseif ($item->origin=='_override'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_ORIGIN_OVERRIDE'),   'inactive_class'=>'16-override', 'enabled' => false, 'translate'=>false)); ?>
		<?php else:?>
		<span class="jgrid hasTooltip" title="<?php echo JText::_($packages[$item->origin]->title) . '::' . JText::_($packages[$item->origin]->description); ?>">
			<span class="state" style="background-image:url(<?php echo JURI::root(true) . $packages[$item->origin]->icon; ?>);">
				<span class="text"/>
			</span>
		</span>
		<?php endif; ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_STATE_'.$item->state, $item->translated, $item->unchanged, $item->total, $item->extra), 'inactive_class'=>'16-'.$item->state, 'enabled' => false, 'translate'=>false)); ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_TYPE_'.$item->type), 'inactive_class'=>'16-'.$item->type, 'enabled' => false, 'translate'=>false)); ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_CLIENT_'.$item->client), 'inactive_class'=>'16-'.$item->client, 'enabled' => false, 'translate'=>false)); ?>
		<?php if ($item->tag==$reference && $item->type!='override'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_REFERENCE'), 'inactive_class'=>'16-reference', 'enabled' => false, 'translate'=>false));?>
		<?php else: ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('enabled'=>false)); ?>
		<?php endif; ?></td>
	<td dir="ltr" class="center"><?php echo $item->tag; ?></td>
	<td dir="ltr">
		<?php if (!empty($item->checked_out)) : ?>
		<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
		<?php else: ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('enabled'=>false)); ?>
		<?php endif; ?>
		<?php if ($item->writable && !$item->error && $canEdit): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('enabled'=>false)); ?>
		<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_localise&task=translation.edit&client='.$item->client.'&tag='.$item->tag.'&filename='.$item->filename.'&storage='.$item->storage.'&id='.LocaliseHelper::getFileId(LocaliseHelper::getTranslationPath($item->client,$item->tag, $item->filename, $item->storage)).($item->filename=='override' ? '&layout=raw' :''));?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_' . ($item->state=='unexisting' ? 'NEW' : 'EDIT')); ?>">
			<?php echo $item->name; ?>
		</a>
		<?php elseif (!$canEdit): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_NOTEDITABLE', substr($item->path, strlen(JPATH_ROOT))), 'inactive_class'=>'16-error', 'enabled' => false, 'translate'=>false)); ?>
		<?php echo $item->name;?>
		<?php elseif (!$item->writable): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_NOTWRITABLE', substr($item->path, strlen(JPATH_ROOT))), 'inactive_class'=>'16-error', 'enabled' => false, 'translate'=>false)); ?>
		<?php echo $item->name;?>
		<?php elseif ($item->filename=='override'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('enabled'=>false)); ?>
		<?php echo $item->name; ?>
		<?php else: ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_ERROR', substr($item->path, strlen(JPATH_ROOT)) , implode(', ',$item->error)), 'inactive_class'=>'16-error', 'enabled' => false, 'translate'=>false)); ?>
		<?php echo $item->name; ?>
		<?php endif;?>
	</td>
	<td dir="ltr">
		<?php if ($item->writable && $canEdit): ?>
		<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_localise&task=translation.edit&client=' . $item->client . '&tag=' . $item->tag . '&filename=' . $item->filename . '&storage=' . $item->storage . '&id=' . LocaliseHelper::getFileId(LocaliseHelper::getTranslationPath($item->client,$item->tag, $item->filename, $item->storage)) . '&layout=raw'); ?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_' . ($item->state=='unexisting' ? 'NEWRAW' : 'EDITRAW')); ?>">
			<?php echo substr($item->path,strlen(JPATH_ROOT));?>
		</a>
		<?php else: ?>
		<?php echo substr($item->path,strlen(JPATH_ROOT)); ?>
		<?php endif;?>
	</td>
	<td width="100" class="center" dir="ltr">
		<?php if ($item->bom != 'UTF-8'): ?>
		<a class="jgrid hasTooltip" href="http://en.wikipedia.org/wiki/UTF-8" title="<?php echo addslashes(htmlspecialchars(JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_UTF8'), ENT_COMPAT, 'UTF-8')); ?>">
			<span class="state icon-16-error"></span>
			<span class="text"></span>
		</a>
		<?php elseif ($item->state == 'error'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_ERROR',substr($item->path,strlen(JPATH_ROOT)) , implode(', ',$item->error)), 'inactive_class'=>'16-error', 'enabled' => false, 'translate'=>false));?>
		<?php elseif ($item->type == 'override'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_TYPE_OVERRIDE'), 'inactive_class'=>'16-override', 'enabled' => false, 'translate'=>false));?>
		<?php elseif ($item->state == 'notinreference'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_STATE_NOTINREFERENCE'), 'inactive_class'=>'16-notinreference', 'enabled' => false, 'translate'=>false));?>
		<?php elseif ($item->state == 'unexisting'): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_STATE_UNEXISTING', $item->translated, $item->unchanged, $item->total, $item->extra), 'inactive_class'=>'16-unexisting', 'enabled' => false, 'translate'=>false));?>
		<?php elseif ($item->tag == $reference): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_REFERENCE'), 'inactive_class'=>'16-reference', 'enabled' => false, 'translate'=>false));?>
		<?php elseif ($item->translated == $item->total || $item->complete): ?>
		<?php echo JHtml::_('jgrid.action', $i, '', array('tip'=>true, 'inactive_title'=>JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_COMPLETE', $item->translated, $item->unchanged, $item->total, $item->extra), 'inactive_class'=>'16-complete', 'enabled' => false, 'translate'=>false));?>
		<?php else: ?>
		<span class="hasTooltip" title="<?php echo $item->translated + $item->unchanged == 0 ? JText::_('COM_LOCALISE_TOOLTIP_TRANSLATIONS_NOTSTARTED') : JText::sprintf('COM_LOCALISE_TOOLTIP_TRANSLATIONS_INPROGRESS', $item->translated, $item->unchanged, $item->total, $item->extra);?>">
			<?php $translated =  $item->total ? intval(100 * $item->translated / $item->total) : 0;?>
			<?php $unchanged =  ($item->translated+$item->unchanged==$item->total)?(100-$translated):($item->total ? intval(100 * $item->unchanged / $item->total) : 0);?>
			<?php if ($item->unchanged):?>
			( <?php echo $translated;?> %+ <?php echo $unchanged;?> %)
			<?php else:?>
			<?php echo $translated;?> %
			<?php endif;?>
			<div style="text-align:left;border:solid silver 1px;width:100px;height:4px;">
				<div class="pull-left" style="height:100%; width:<?php echo $translated;?>% ;background:green;">
				</div>
				<div class="pull-left" style="height:100%; width:<?php echo $unchanged;?>% ;background:orange;">
				</div>
				<div class="pull-left" style="height:100%; width:<?php echo 100-$translated-$unchanged;?>% ;background:red;">
				</div>
			</div>
			<div class="clr"></div>
		</span>
		<?php endif;?>
	</td>
	<td dir="ltr" class="center">
		<?php if ($item->state != 'error'): ?>
		<?php if ($item->state == 'notinreference'): ?>
		<?php echo $item->extra; ?>
		<?php elseif ($item->type == 'override'): ?>
		<?php elseif ($item->tag == $reference): ?>
		<?php echo $item->translated; ?>
		<?php else: ?>
		<?php echo ($item->unchanged ? ("(" . $item->translated . "+" . $item->unchanged . ")") : $item->translated) . "/" . $item->total . ($item->extra ? "+" . $item->extra : ''); ?>
		<?php endif;?>
		<?php endif;?>
	</td>
	<td class="hidden-phone">
		<?php if ($item->state != 'unexisting') : ?>
		<?php $description = ($item->maincopyright ? ($item->maincopyright . '<br/>') : '') . ($item->additionalcopyright ? (str_replace("\n",'<br/>', $item->additionalcopyright) . '<br/>') : '') . ($item->description ? ($item->description . '<br/>') : '') . ($item->version ? ($item->version . '<br/>') : '') . ($item->creationdate ? $item->creationdate : ''); ?>
		<?php if ($description || $item->author) : ?>
		<?php $author = $item->author ? $item->author : JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_AUTHOR') ;?>
		<span class="hasTooltip" title="<?php echo htmlspecialchars($description, ENT_COMPAT, 'UTF-8'); ?>">
			<?php echo $author; ?>
		</span>
		<?php endif; ?>
		<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>
