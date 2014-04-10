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
 * Package Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseControllerPackage extends JControllerForm
{
	protected $_context = 'com_localise.package';

	public function __construct($config = array()) 
	{
		parent::__construct($config);

		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the id
		$cid = JRequest::getVar('cid', array(), 'default', 'array');
		$cid = count($cid) ? $cid[0] : '';
		if (!empty($cid)) 
		{
			// From the packages view
			$path = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$cid.xml"; 
			$name = $cid;
			$id   = LocaliseHelper::getFileId($path);
		}
		else
		{
			// From the package view
			$data = JRequest::getVar('jform', array(), 'default', 'array');
			$id   = $data['id'];
			$name = $data['name'];
		}

		// Set the id, and path in the session
		$app->setUserState('com_localise.edit.package.id', $id);
		$app->setUserState('com_localise.package.name', $name);

		// Set the id and unset the cid
		if (!empty($id) && JRequest::getVar('task') == 'add') 
		{
			JRequest::setVar('task', 'edit');
		}

		JRequest::setVar('id', $id);
		JRequest::setVar('cid', array());
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param  array  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function _allowAdd($data = array()) 
	{
		return JFactory::getUser()->authorise('localise.create', $this->_option);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param  array  An array of input data.
	 * @param  string  The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function _allowEdit($data = array(), $key = 'id') 
	{
		return JFactory::getUser()->authorise('localise.edit', $this->_option . '.' . $data[$key]);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param  string  The model name. Optional.
	 * @param  string  The class prefix. Optional.
	 * @param  array  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Package', $prefix = 'LocaliseModel', $config = array('ignore_request' => false)) 
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function download() 
	{
		// Redirect to the export view
		$app  = JFactory::getApplication();
		$name = $app->getUserState('com_localise.package.name');
		$path = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$name.xml";
		$id   = LocaliseHelper::getFileId($path);

		// Check if the package exists
		if (empty($id)) 
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->_option . '&view=packages', false), JText::sprintf('COM_LOCALISE_ERROR_DOWNLOADPACKAGE_UNEXISTING', $name), 'error');
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

			setcookie(JApplication::getHash($this->_context . '.author'   ), $package->author   , time()+60*60*24*30);
			setcookie(JApplication::getHash($this->_context . '.copyright'), $package->copyright, time()+60*60*24*30);
			setcookie(JApplication::getHash($this->_context . '.email'    ), $package->email    , time()+60*60*24*30);
			setcookie(JApplication::getHash($this->_context . '.url'      ), $package->url      , time()+60*60*24*30);
			setcookie(JApplication::getHash($this->_context . '.version'  ), $package->version  , time()+60*60*24*30);
			setcookie(JApplication::getHash($this->_context . '.license'  ), $package->license  , time()+60*60*24*30);

			$this->setRedirect(JRoute::_('index.php?option=com_localise&tmpl=component&view=downloadpackage&name=' . $name . '&standalone=' . $package->standalone, false), $msg, $type);
		}
	}
}
