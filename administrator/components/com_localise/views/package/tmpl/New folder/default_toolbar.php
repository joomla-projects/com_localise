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

JRequest::setVar('hidemainmenu', 1);
if ($this->filename) 
{
  JToolbarHelper::title(JText::_('COM_LOCALISE_LOCALISATION_MANAGER') . ' - ' . JText::_('COM_LOCALISE_EDIT_PACKAGE'), 'langmanager.png');
}
else
{
  JToolbarHelper::title(JText::_('COM_LOCALISE_LOCALISATION_MANAGER') . ' - ' . JText::_('COM_LOCALISE_NEW_PACKAGE'), 'langmanager.png');
}
JToolbarHelper::save('package.save');
JToolbarHelper::apply('package.apply');
JToolbarHelper::cancel('package.cancel');
JToolBarHelper::divider();
JToolBarHelper::help('screen.package', true);
