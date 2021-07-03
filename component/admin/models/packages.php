<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Packages Model class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseModelPackages extends JModelList
{
	protected $context = 'com_localise.packages';

	protected $items;

	protected $packages;

	protected $filter_fields = array('title', 'language', 'version', 'core');

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = JFactory::getApplication();
		$data = $app->input->get('filters', array(), 'array');

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

		$this->setState('filter.title', isset($data['select']['title']) ? $data['select']['title'] : '');

		$this->setState('filter.language', isset($data['select']['language']) ? $data['select']['language'] : '');

		$this->setState('filter.version', isset($data['select']['version']) ? $data['select']['version'] : '');

		$this->setState('filter.core', isset($data['select']['core']) ? $data['select']['core'] : '');

		$params = JComponentHelper::getParams('com_localise');
		$this->setState('params', $params);

		parent::populateState('title', 'asc');
	}

	/**
	 * Get packages
	 *
	 * @return array
	 */
	private function _getPackages()
	{
		if (!isset($this->packages))
		{
			$search = $this->getState('filter.search');
			$this->packages = array();
			$paths = array (
				JPATH_COMPONENT_ADMINISTRATOR . '/packages',
				JPATH_SITE . '/media/com_localise/packages',
			);

			foreach ($paths as $path)
			{
				if (JFolder::exists($path))
				{
					$files = JFolder::files($path, '\.xml$');

					foreach ($files as $file)
					{
						$id    = LocaliseHelper::getFileId("$path/$file");
						$context = LocaliseHelper::isCorePackage("$path/$file") ?
									'package' : 'packagefile';
						$model = JModelLegacy::getInstance($context, 'LocaliseModel', array('ignore_request' => true));
						$model->setState("$context.id", $id);
						$package = $model->getItem();

						if (empty($search) || preg_match("/$search/i", $package->title))
						{
							$this->packages[] = $package;
						}
					}
				}
			}

			$ordering = $this->getState('list.ordering') ? $this->getState('list.ordering') : 'title';
			ArrayHelper::sortObjects($this->packages, $ordering, $this->getState('list.direction') == 'desc' ? -1 : 1);
		}

		return $this->packages;
	}

	/**
	 * Get Items
	 *
	 * @return array|mixed
	 */
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

	/**
	 * Get Total
	 *
	 * @return int
	 */
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
		if (version_compare(JVERSION, '4.0', 'le') && JError::isError($form))
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
	 * Remove packages
	 *
	 * @param   array  $selected  array of selected packages
	 *
	 * @return  boolean  true for success, false for failure
	 */
	public function delete($selected)
	{
		// Sanitize the array.
		$selected = (array) $selected;

		// Get a row instance.
		$table = JTable::getInstance('Localise', 'LocaliseTable');

		foreach ($selected as $packageId)
		{
			$path = LocaliseHelper::getFilePath($packageId);
			$package = JFile::stripExt(basename($path));

			if (!JFile::delete($path))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGES_REMOVE', $package));

				return false;
			}

			if (!$table->delete((int) $packageId))
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	/**
	 * Export packages
	 *
	 * @param   array  $selected  array of selected packages
	 *
	 * @return  boolean  success or failure
	 */
	public function export($selected)
	{
		foreach ($selected as $packageId)
		{
			$path = LocaliseHelper::getFilePath($packageId);
			$package = JFile::stripExt(basename($path));

			if (JFile::exists($path))
			{
				ob_clean();
				$pack = file_get_contents($path);
				header("Expires: 0");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header('Content-Type: application/xml');
				header('Content-Disposition: attachment; filename="' . $package . '.xml"');
				header('Content-Length: ' . strlen($pack));
				header("Cache-Control: maxage=1");
				header("Pragma: public");
				header("Content-Transfer-Encoding: binary");
				echo $pack;
				exit;
			}
			else
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGES_EXPORT', $package));
			}
		}
	}

	/**
	 * Clone packages
	 *
	 * @param   array  $selected  array of selected packages
	 *
	 * @return  boolean  success or failure
	 */
	public function duplicate($selected)
	{
		foreach ($selected as $packageId)
		{
			$path = LocaliseHelper::getFilePath($packageId);
			$package = JFile::stripExt(basename($path));

			if (JFile::exists($path))
			{
				$pack = file_get_contents($path);
				$newpackage = $package . '_' . JFactory::getDate()->format("Y-m-d-H-i-s");
				$newpath = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$newpackage.xml";

				JFile::write($newpath, $pack);
			}
			else
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGES_READ', $package));

				return false;
			}

			if (!JFile::exists($newpath))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGES_CLONE', $package));

				return false;
			}
		}

		return true;
	}
}
