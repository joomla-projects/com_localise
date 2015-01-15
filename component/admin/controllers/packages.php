<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
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
class LocaliseControllerPackages extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $text_prefix  = 'COM_LOCALISE_PACKAGES';

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
		foreach ($ids as $i => $id)
		{
			$path    = LocaliseHelper::getFilePath($id);
			$context = LocaliseHelper::isCorePackage($path) ?
						'package' : 'packagefile';
			$model = JModelLegacy::getInstance($context, 'LocaliseModel', array('ignore_request' => true));
			$model->setState("$context.id", $id);
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

	/**
	 * Export Packages
	 *
	 * @return  void
	 */
	public function export()
	{
		// Check for request forgeries.
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.create', 'com_localise.' . (int) $id))
			{
				// Prune items that you can't export.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('COM_LOCALISE_EXPORT_NOT_PERMITTED'));
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

			// Export the packages.
			if (!$model->export($ids))
			{
				$msg = implode("<br />", $model->getErrors());
				$type = 'error';
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}

	/**
	 * Clone an existing package.
	 *
	 * @return  void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = JFactory::getUser();
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.create', 'com_localise.' . (int) $id))
			{
				// Prune items that you can't clone.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('COM_LOCALISE_ERROR_PACKAGES_CLONE_NOT_PERMITTED'));
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

			// Clone the items.
			if (!$model->duplicate($ids))
			{
				$msg = implode("<br />", $model->getErrors());
				$type = 'error';
			}
			else
			{
				$msg = JText::plural('COM_LOCALISE_N_PACKAGES_DUPLICATED', count($ids));
				$type = 'message';
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}

	/**
	 * Check in override to checkin one record of either package or packagefile.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function checkin()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		// Checkin only first id if more than one ids are present.
		$id = $ids[0];

		$path    = LocaliseHelper::getFilePath($id);
		$context = LocaliseHelper::isCorePackage($path) ?
					'package' : 'packagefile';
		$model = JModelLegacy::getInstance($context, 'LocaliseModel', array('ignore_request' => true));

		$return = $model->checkin($id);

		if ($return === false)
		{
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

			return false;
		}
		else
		{
			// Checkin succeeded.
			$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);

			return true;
		}
	}
}
