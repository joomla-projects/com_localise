<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author    Mohammad Hasani Eghtedar <m.h.eghtedar@gmail.com>
# copyright Copyright (C) 2012 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<form action="<?php JRoute::_('index.php?option=com_localise'); ?>" method="post" name="adminForm" id="localise-package-form">
  <fieldset>
    <legend><?php echo JText::_('COM_LOCALISE_PACKAGE_DETAIL'); ?></legend>
    <table width="100%" class="paramlist admintable" cellspacing="1">
      <?php foreach($this->form->getFields('package') as $field): ?>
      <?php if ($field->hidden): ?>
      <?php echo $field->input; ?>
      <?php else: ?>
      <tr>
        <td class="paramlist_key" width="40%"><span class="editlinktip"> <?php echo $field->label; ?> </span></td>
        <td class="paramlist_value"><?php echo $field->input; ?></td>
      </tr>
      <?php endif; ?>
      <?php endforeach; ?>
    </table>
  </fieldset>
  <fieldset>
    <legend><?php echo JText::_('COM_LOCALISE_PACKAGE_FILES'); ?></legend>
    <?php
      jimport('joomla.html.pane');
      $pane = & JPane::getInstance('sliders', array('allowAllClose' => 1));
      $this->pane = & $pane;
      echo $pane->startPane("menu-pane");
      if (count($this->extensions['components']) > 0) 
      {
        $this->panel_title = & JText::_('COM_LOCALISE_COMPONENTS');
        $this->panel = 'components';
        $this->header = & JText::_('COM_LOCALISE_PACKAGE_COMPONENT_NAME');
        echo $this->loadTemplate('extensions');
      }
      if (count($this->extensions['modules']) > 0) 
      {
        $this->panel_title = & JText::_('COM_LOCALISE_MODULES');
        $this->panel = 'modules';
        $this->header = & JText::_('COM_LOCALISE_PACKAGE_MODULE_NAME');
        echo $this->loadTemplate('extensions');
      }
      if (count($this->extensions['templates']) > 0) 
      {
        $this->panel_title = & JText::_('COM_LOCALISE_TEMPLATES');
        $this->panel = 'templates';
        $this->header = & JText::_('COM_LOCALISE_PACKAGE_TEMPLATE_NAME');
        echo $this->loadTemplate('extensions');
      }
      if (count($this->extensions['plugins']) > 0) 
      {
        $this->panel_title = & JText::_('COM_LOCALISE_PLUGINS');
        $this->panel = 'plugins';
        $this->header = & JText::_('COM_LOCALISE_PACKAGE_PLUGIN_NAME');
        echo $this->loadTemplate('extensions');
      }
      echo $pane->endPane();
    ?>
  </fieldset>
  <input type="hidden" name="oldfilename" value="<?php echo $this->filename; ?>" />
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
