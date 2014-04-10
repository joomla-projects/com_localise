<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Packages Model class for the Localise component
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelPackages extends JModelList
{
	protected $context = 'com_localise.packages';
	protected $items;
	protected $packages;

	/**
	 * Autopopulate the model
	 */
	protected function populateState($ordering = null, $direction = null) 
	{
		$app  = JFactory::getApplication('administrator');
		$data = JRequest::getVar('filters');

		if (empty($data)) 
		{
			$data = array();
			$data['search'] = $app->getUserState('com_localise.packages.search');
		}
		else
		{
			$app->setUserState('com_localise.packages.search', $data['search']);
		}

		$this->setState('filter.search', isset($data['search']['expr']) ? $data['search']['expr'] : '');
		$params = JComponentHelper::getParams('com_localise');
		$this->setState('params', $params);

		parent::populateState('title', 'asc');
	}

	private function _getPackages() 
	{
		if (!isset($this->packages)) 
		{
			$search = $this->getState('filter.search');
			$this->packages = array();
			$path = JPATH_COMPONENT_ADMINISTRATOR . '/packages';

			if (JFolder::exists($path)) 
			{
				$files = JFolder::files($path, '\.xml$');
				foreach ($files as $file) 
				{
					$model = JModelLegacy::getInstance('Package', 'LocaliseModel', array('ignore_request' => true));
					$id    = LocaliseHelper::getFileId("$path/$file");
					$model->setState('package.id', $id);
					$package = $model->getItem();

					if (empty($search) || preg_match("/$search/i", $package->title)) 
					{
						$this->packages[] = $package;
					}
				}
			}

			$ordering = $this->getState('list.ordering') ? $this->getState('list.ordering') : 'title';
			JArrayHelper::sortObjects($this->packages, $ordering, $this->getState('list.direction') == 'desc' ? -1 : 1);
		}

		return $this->packages;
	}

	public function getItems() 
	{
		if (empty($this->items)) 
		{
			$packages = $this->_getPackages();
			$count    = count($packages);
			$start    = $this->getState('list.start');
			$limit    = $this->getState('list.limit');

			if ($start > $count) 
			{
				$start = 0;
			}
			if ($limit == 0) 
			{
				$start = 0;
				$limit = null;
			}

			$this->items = array_slice($packages, $start, $limit);
		}

		return $this->items;
	}

	public function getTotal() 
	{
		return count($this->_getPackages());
	}

	/**
	 * Method to get the row form.
	 *
	 * @return  mixed  JForm object on success, false on failure.
	 */
	public function getForm() 
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

		$form = JForm::getInstance('com_localise.packages', 'packages', array('control' => 'filters', 'event' => 'onPrepareForm'));

		// Check for an error.
		if (JError::isError($form)) 
		{
			$this->setError($form->getMessage());
			return false;
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.select', array());

		// Bind the form data if present.
		if (!empty($data)) 
		{
			$form->bind(array('select' => $data));
		}

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.packages.search', array());

		// Bind the form data if present.
		if (!empty($data)) 
		{
			$form->bind(array('search' => $data));
		}

		return $form;
	}

	/**
	 * Remove languages
	 *
	 * @return  boolean  true for success, false for failure
	 */
	public function delete($selected) 
	{
		foreach ($selected as $package) 
		{
			$path = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$package.xml";
			if (!JFile::delete($path)) 
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGES_REMOVE', $package));
				return false;
			}
		}

		return true;
	}
}
