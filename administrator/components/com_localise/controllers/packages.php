<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Packages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseControllerPackages extends JControllerLegacy
{
	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'Packages', $prefix = 'LocaliseModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function display($cachable = false) 
	{
		JRequest::setVar('view', 'packages');
		parent::display($cachable);
	}

	public function delete() 
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids  = JRequest::getVar('cid', array(), '', 'array');

		// Access checks.
		foreach ($ids as $i => $package) 
		{
			$id    = LocaliseHelper::getFileId(JPATH_ROOT . "/media/com_localise/packages/$package.xml");
			$model = $this->getModel('Package');
			$model->setState('package.id', $id);
			$item  = $model->getItem();

			if (!$item->standalone) 
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('COM_LOCALISE_ERROR_PACKAGES_DELETE'));
			}

			if (!$user->authorise('core.delete', 'com_localise.' . (int)$id)) 
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) 
		{
			$msg = JText::_('JERROR_NO_ITEMS_SELECTED');
			$type = 'error';
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Remove the items.
			if (!$model->delete($ids)) 
			{
				$msg = implode("<br />", $model->getErrors());
				$type = 'error';
			}
			else
			{
				$msg = JText::sprintf('JCONTROLLER_N_ITEMS_DELETED', count($ids));
				$type = 'message';
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}
}
