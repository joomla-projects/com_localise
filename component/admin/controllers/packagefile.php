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
 * Package Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseControllerPackageFile extends JControllerForm
{
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_list = 'packages';

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		// @todo: $data parameter is unused
		return JFactory::getUser()->authorise('localise.create', $this->option);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('localise.edit', $this->option . '.' . $data[$key]);
	}

	/**
	 * Todo: description missing
	 *
	 * @return void
	 */
	public function download()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$input = $app->input;
		$model   = $this->getModel();

		$data = $input->get('jform', array(), 'array');
		$model->download($data);

		// Redirect to the export view

		/*
		$app  = JFactory::getApplication();
		$name = $app->getUserState('com_localise.package.name');
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/packages/' . $name . '.xml';
		$id   = LocaliseHelper::getFileId($path);
		*/

		// Check if the package exists

		/*
		if (empty($id))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&view=packages', false),
				JText::sprintf('COM_LOCALISE_ERROR_DOWNLOADPACKAGE_UNEXISTING', $name),
				'error'
			);
		}
		else
		{
			$model   = $this->getModel();
			$package = $model->getItem();

			if (!$package->standalone)
			{
				$msg  = JText::sprintf('COM_LOCALISE_NOTICE_DOWNLOADPACKAGE_NOTSTANDALONE', $name);
				$type = 'notice';
			}
			else
			{
				$msg  = '';
				$type = 'message';
			}

			setcookie(JApplicationHelper::getHash($this->context . '.author'), $package->author, time() + 60 * 60 * 24 * 30);
			setcookie(JApplicationHelper::getHash($this->context . '.copyright'), $package->copyright, time() + 60 * 60 * 24 * 30);
			setcookie(JApplicationHelper::getHash($this->context . '.email'), $package->email, time() + 60 * 60 * 24 * 30);
			setcookie(JApplicationHelper::getHash($this->context . '.url'), $package->url, time() + 60 * 60 * 24 * 30);
			setcookie(JApplicationHelper::getHash($this->context . '.version'), $package->version, time() + 60 * 60 * 24 * 30);
			setcookie(JApplicationHelper::getHash($this->context . '.license'), $package->license, time() + 60 * 60 * 24 * 30);

			$this->setRedirect(
				JRoute::_('index.php?option=com_localise&tmpl=component&view=downloadpackage&name=' . $name . '&standalone=' . $package->standalone, false),
				$msg,
				$type
			);
		}
		*/
	}
}
