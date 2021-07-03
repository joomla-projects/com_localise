<?php
/**
 * @package     Com_Localise
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_localise'))
{
	if (version_compare(JVERSION, '4.0', 'ge'))
	{
		throw new NotAllowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
	}
	else
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

// Include helper files
require_once JPATH_COMPONENT . '/helpers/defines.php';
require_once JPATH_COMPONENT . '/helpers/localise.php';

// Load Composer Autoloader
require_once JPATH_COMPONENT_ADMINISTRATOR . '/vendor/autoload.php';

// Get the controller
$controller = JControllerLegacy::getInstance('Localise');

// Execute the task.
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
