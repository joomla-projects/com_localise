<?php defined('_JEXEC') or die('Restricted access');
$app = & JFactory::getApplication('administrator');
$k = 0;
foreach($this->extensions[$this->panel] as $name => $extension): ?>

<tr class="row<?php echo $k; ?>">
  <td><?php switch ($extension->administrator): case -2: ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/mini_icon.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_JOOMLA_PACKAGE_FILE_USED'); ?>::Joomla" alt="<?php echo JText::_('COM_LOCALISE_JOOMLA_PACKAGE_FILE_USED'); ?>" />
    <?php break; case -1: ?>
    <img src="components/com_localise/assets/images/disabled.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED'); ?>::<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED_DESC'); ?>" alt="<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED'); ?>" />
    <?php break; default: ?>
    <input type="checkbox" <?php if ($extension->administrator): ?> checked="checked" <?php endif; ?> name="<?php echo $this->panel; ?>[<?php echo $name; ?>][admin]" value="1"/>
    <?php if (count($extension->administrator_packages['home']) > 0): ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/menu/icon-16-frontpage.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_HOME_PACKAGE_FILE_USED'); ?>::<?php echo implode('<br />', $extension->administrator_packages['home']); ?>" alt="<?php echo JText::_('COM_LOCALISE_HOME_PACKAGE_FILE_USED'); ?>" />
    <?php endif; ?>
    <?php if (count($extension->administrator_packages['thirdparty']) > 0): ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/menu/icon-16-user.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_THIRDPARTY_PACKAGE_FILE_USED'); ?>::<?php echo implode('<br />', $extension->administrator_packages['thirdparty']); ?>" alt="<?php echo JText::_('COM_LOCALISE_THIRDPARTY_PACKAGE_FILE_USED'); ?>" />
    <?php endif; ?>
    <?php endswitch; ?></td>
  <td><?php switch ($extension->site): case -2: ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/mini_icon.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_JOOMLA_PACKAGE_FILE_USED'); ?>::Joomla" alt="<?php echo JText::_('COM_LOCALISE_JOOMLA_PACKAGE_FILE_USED'); ?>" />
    <?php break; case -1: ?>
    <img src="components/com_localise/assets/images/disabled.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED'); ?>::<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED_DESC'); ?>" alt="<?php echo JText::_('COM_LOCALISE_PACKAGE_FILE_DISABLED'); ?>" />
    <?php break; default: ?>
    <input type="checkbox" <?php if ($extension->site): ?> checked="checked" <?php endif; ?> name="<?php echo $this->panel; ?>[<?php echo $name; ?>][site]" value="1"/>
    <?php if (count($extension->site_packages['home']) > 0): ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/menu/icon-16-frontpage.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_HOME_PACKAGE_FILE_USED'); ?>::<?php echo implode('<br />', $extension->site_packages['home']); ?>" alt="<?php echo JText::_('COM_LOCALISE_HOME_PACKAGE_FILE_USED'); ?>" />
    <?php endif; ?>
    <?php if (count($extension->site_packages['thirdparty']) > 0): ?>
    <img src="templates/<?php echo $app->getTemplate(); ?>/images/menu/icon-16-user.png" class="hasTip" title="<?php echo JText::_('COM_LOCALISE_THIRDPARTY_PACKAGE_FILE_USED'); ?>::<?php echo implode('<br />', $extension->site_packages['thirdparty']); ?>" alt="<?php echo JText::_('COM_LOCALISE_THIRDPARTY_PACKAGE_FILE_USED'); ?>" />
    <?php endif; ?>
    <?php endswitch; ?></td>
  <td><?php echo $name; ?></td>
</tr>
<?php $k = 1 - $k; endforeach; ?>