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
 * @since  1.0
 */
class LocaliseModelDownloadPackage extends JModelForm
{
	protected $context = 'com_localise.package';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return void
	 */
	protected function populateState()
	{
		// Get the data
		$input      = JFactory::getApplication()->input;
		$name       = $input->get('name');
		$standalone = $input->get('standalone');
		$author     = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.author'), '');
		$copyright  = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.copyright'), '');
		$email      = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.email'), '');
		$url        = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.url'), '');
		$version    = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.version'), '');
		$license    = $input->cookie->getString(JApplicationHelper::getHash($this->context . '.license'), '');

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
	 * @param   array  $data      Form data
	 * @param   bool   $loadData  To be filled
	 *
	 * @return  mixed  JForm object on success, false on failure.
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_localise.downloadpackage',
			'downloadpackage',
			array('control'   => 'jform', 'load_data' => $loadData)
		);

		// Check for an error.
		if (JError::isError($form))
		{
			$this->setError($form->getMessage());

			return false;
		}

		$form->setValue('name', null, $this->getState('downloadpackage.name'));
		$form->setValue('author', null, $this->getState('downloadpackage.author'));
		$form->setValue('copyright', null, $this->getState('downloadpackage.copyright'));
		$form->setValue('email', null, $this->getState('downloadpackage.email'));
		$form->setValue('url', null, $this->getState('downloadpackage.url'));
		$form->setValue('version', null, $this->getState('downloadpackage.version'));
		$form->setValue('license', null, $this->getState('downloadpackage.license'));

		return $form;
	}

	/**
	 * Method to get the Item.
	 *
	 * @return   mixed  JForm object on success, false on failure.
	 */
	public function getItem()
	{
		$item             = new JObject;
		$item->standalone = $this->getState('downloadpackage.standalone');

		return $item;
	}
}
