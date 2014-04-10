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

class LocaliseModelTranslator extends JModelLegacy
{
  public function getText()
  {
    $params = JComponentHelper::getParams('com_localise');
    $clientID = $params->get('clientID');
    $secret = $params->get('client_secret');

    if(empty($clientID) || empty($secret))
    {
      $this->setError(JText::_('COM_LOCALISE_MISSING_CLIENTID_SECRET'));
      return '';
    }

    $app = JFactory::getApplication();
    $text = $app->input->getHtml('text');
    if(empty($text))
    {
      $this->setError(JText::_('COM_LOCALISE_MISSING_TEXT'));
      return '';
    }

    $to = $app->input->getCmd('to');
    if(empty($to))
    {
      $this->setError(JText::_('COM_LOCALISE_MISSING_TO_LANGUAGECODE'));
      return '';
    }

    $from = $app->input->getCmd('from');

    class_exists('HTTPTranslator')
      or require(dirname(__DIR__).'/helpers/azuretranslator.php');

    $translator = new HTTPTranslator();
    return $translator->translate($clientID, $secret, $to, $text, $from);
  }
}