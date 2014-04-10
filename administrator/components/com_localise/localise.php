<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_localise')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include helper files
require_once JPATH_COMPONENT . '/helpers/defines.php';
require_once JPATH_COMPONENT . '/helpers/localise.php';

//Get the controller
$controller = JControllerLegacy::getInstance('Localise');

// Execute the task.
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
