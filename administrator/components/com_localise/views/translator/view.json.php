<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author    author Yoshiki Kozaki <info@joomler.net>
# copyright Copyright (C) 2012 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class LocaliseViewTranslator extends JViewLegacy
{
  public function display($tpl=null)
  {
    $results = array();
    $results['success'] = 0;

    $id = JFactory::getApplication()->input->getInt('id');

    if(!JSession::checkToken() || !JFactory::getUser()->authorise('core.edit', 'com_localise.edit.'. $id)){
      return false;
    }

    $text = $this->get('text');
    if(!empty($text))
    {
      $results['success'] = 1;
      $results['text'] = $text;
    }

    echo json_encode($results);
  }
}
