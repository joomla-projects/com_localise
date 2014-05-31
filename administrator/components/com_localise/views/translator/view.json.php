<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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
