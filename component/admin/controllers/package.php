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
class LocaliseControllerPackage extends JControllerForm
{
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
		// todo: this feature is not finished.

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

	/**
	 * Method for uploading a file.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function uploadFile()
	{
		$app      = JFactory::getApplication();
		$model    = $this->getModel();
		$upload   = $app->input->files->get('files');

		if ($return = $model->uploadFile($upload))
		{
			$app->enqueueMessage(JText::sprintf('COM_LOCALISE_FILE_UPLOAD_SUCCESS', $upload['name']));
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_LOCALISE_ERROR_FILE_UPLOAD'), 'error');
		}

		$url = 'index.php?option=com_localise&view=packages';
		$this->setRedirect(JRoute::_($url, false));
	}

	/**
	 * Method for uploading a css or a php file in the language xx-XX folder.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function uploadOtherFile()
	{
		$app		= JFactory::getApplication();
		$id			= $app->getUserState('com_localise.edit.package.id');
		$model		= $this->getModel();
		$upload		= $app->input->files->get('files');
		$location	= $app->input->get('location');

		if ($location == "admin")
		{
			$location = LOCALISEPATH_ADMINISTRATOR;
		}
		elseif ($location == "site")
		{
			$location = LOCALISEPATH_SITE;
		}

		if ($return = $model->uploadOtherFile($upload, $location))
		{
			$app->enqueueMessage(JText::sprintf('COM_LOCALISE_OTHER_FILE_UPLOAD_SUCCESS', $upload['name']));
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_LOCALISE_ERROR_OTHER_FILE_UPLOAD'), 'error');
		}

		$url = 'index.php?option=com_localise&task=package.edit&cid[]=' . $id;

		$this->setRedirect(JRoute::_($url, false));
	}
}
