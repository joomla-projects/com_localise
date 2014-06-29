<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Packages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseControllerPackages extends JControllerLegacy
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Packages', $prefix = 'LocaliseModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Display View
	 *
	 * @param   boolean  $cachable   Enable cache or not.
	 * @param   array    $urlparams  todo: this params can probably be removed.
	 *
	 * @return  void     Display View
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JFactory::getApplication()->input->set('view', 'packages');
		parent::display($cachable);
	}

	/**
	 * Delete Packages
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $package)
		{
			$id    = LocaliseHelper::getFileId(JPATH_ROOT . '/media/com_localise/packages/' . $package . '.xml');
			$model = $this->getModel('Package');
			$model->setState('package.id', $id);
			$item  = $model->getItem();

			if (!$item->standalone)
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('COM_LOCALISE_ERROR_PACKAGES_DELETE'));
			}

			if (!$user->authorise('core.delete', 'com_localise.' . (int) $id))
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
