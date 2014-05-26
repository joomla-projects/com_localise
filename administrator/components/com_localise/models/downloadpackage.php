<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Download package Model class for the Localise component
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelDownloadPackage extends JModelForm
{
	protected $_context = 'com_localise.package';

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState() 
	{
		// Get the data
		$input      = JFactory::getApplication()->input;
		$name       = $input->get('name');
		$standalone = $input->get('standalone');
		$author     = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.author'), '');
		$copyright  = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.copyright'), '');
		$email      = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.email'), '');
		$url        = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.url'), '');
		$version    = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.version'), '');
		$license    = $input->cookie->getString(JApplicationHelper::getHash($this->_context . '.license'), '');

		// Set the state
		$this->setState('downloadpackage.name', $name);
		$this->setState('downloadpackage.standalone', $standalone);
		$this->setState('downloadpackage.author', $author);
		$this->setState('downloadpackage.copyright', $copyright);
		$this->setState('downloadpackage.email', $email);
		$this->setState('downloadpackage.url', $url);
		$this->setState('downloadpackage.version', $version);
		$this->setState('downloadpackage.license', $license);
	}

	/**
	 * Method to get the record form.
	 *
	 * @return  mixed  JForm object on success, false on failure.
	 * @since  1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_localise.downloadpackage', 'downloadpackage', array('control' => 'jform', 'load_data' => $loadData));

		// Check for an error.
		if (JError::isError($form)) 
		{
			$this->setError($form->getMessage());
			return false;
		}

		$form->setValue('name'     , NULL, $this->getState('downloadpackage.name'));  
		$form->setValue('author'   , NULL, $this->getState('downloadpackage.author'));
		$form->setValue('copyright', NULL, $this->getState('downloadpackage.copyright'));
		$form->setValue('email'    , NULL, $this->getState('downloadpackage.email'));
		$form->setValue('url'      , NULL, $this->getState('downloadpackage.url'));
		$form->setValue('version'  , NULL, $this->getState('downloadpackage.version'));
		$form->setValue('license'  , NULL, $this->getState('downloadpackage.license'));

		return $form;
	}

	/**
	 * Method to get the Item.
	 *
	 * @return  mixed  JForm object on success, false on failure.
	 * @since  1.6
	 */
	public function getItem() 
	{
		$item = new JObject();
		$item->standalone = $this->getState('downloadpackage.standalone');

		return $item;
	}
}
