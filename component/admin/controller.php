<?php
/**
 * @package     Com_Localise
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller class for the localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/localise.php';
		$app = JFactory::getApplication('administrator');

		$vName = $this->input->get('view', 'languages');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		if ($vName == 'translations')
		{
			$view     = $this->getView('translations', 'html');
			$packages = $this->getModel('Packages', 'LocaliseModel', array('ignore_request' => true));
			$view->setModel($packages);
		}
		// Check for edit form.
		elseif ($vName == 'language' && $layout == 'edit'
			&& !$this->checkEditId('com_localise.edit.language', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_localise&view=languages', false));

			return false;
		}
		elseif ($vName == 'translation' && ($layout == 'edit' || $layout == 'raw')
			&& !$this->checkEditId('com_localise.edit.translation', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_localise&view=translations', false));

			return false;
		}
		elseif ($vName == 'packagefile' && $layout == 'edit'
			&& !$this->checkEditId('com_localise.edit.packagefile', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_localise&view=packages', false));

			return false;
		}
		elseif ($vName == 'package' && $layout == 'edit'
			&& !$this->checkEditId('com_localise.edit.package', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_localise&view=packages', false));

			return false;
		}
		else
		{
			$this->input->set('view', $vName);
		}

		parent::display($cachable, $urlparams);
	}
}
