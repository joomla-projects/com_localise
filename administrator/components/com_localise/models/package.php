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
jimport('joomla.client.helper');

/**
 * Package Model class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelPackage extends JModelForm
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 */
	protected function populateState() 
	{
		// Get the application
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$name = $app->getUserState('com_localise.package.name');
		$this->setState('package.name', $name);

		$id = $app->getUserState('com_localise.edit.package.id');
		$this->setState('package.id', $id);
	}

	/**
	 * Method to override check-out a row for editing.
	 *
	 * @param  int    The ID of the primary key.
	 * @return  boolean
	 */
	public function checkout($pk = null) 
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('package.id');
		return parent::checkout($pk);
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param  integer  The ID of the primary key.
	 *
	 * @return  boolean
	 */
	public function checkin($pk = null) 
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('package.id');
		return parent::checkin($pk);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param  type   $type    The table type to instantiate
	 * @param  string   $prefix   A prefix for the table class name. Optional.
	 * @param  array  $options Configuration array for model. Optional.
	 * @return  JTable  A database object
	 */
	public function getTable($type = 'Localise', $prefix = 'LocaliseTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param  array  $data    Data for the form.
	 * @param  boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed  A JForm object on success, false on failure
	 * @since  1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$id   = $this->getState('package.id');
		$name = $this->getState('package.name');
		$form = $this->loadForm('com_localise.package', 'package', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) 
		{
			return false;
		}

		$form->setFieldAttribute('translations', 'package', $name, 'translations');

		if (!empty($id)) 
		{
			$form->setFieldAttribute('name', 'readonly', 'true');
			$form->setFieldAttribute('name', 'class', 'readonly');
		}

		// Check for an error.
		if (JError::isError($form)) 
		{
			$this->setError($form->getMessage());
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since  1.6
	 */
	protected function loadFormData() 
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.edit.package.data', array());

		// Get the package data.
		if (empty($data)) 
		{
			$data = $this->getItem();
			$data->title       = JText::_($data->title);
			$data->description = JText::_($data->description);
		}

		return $data;
	}

	/**
	 * Method to get the ftp form.
	 *
	 * @return  mixed  A JForm object on success, false on failure or not ftp
	 */
	public function getFormFtp() 
	{
		// Get the form.
		$form = $this->loadForm('com_localise.ftp', 'ftp');

		if (empty($form)) 
		{
			return false;
		}

		// Check for an error.
		if (JError::isError($form)) 
		{
			$this->setError($form->getMessage());
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the package.
	 *
	 * @return JObject the package
	 */
	public function getItem() 
	{
		$id = $this->getState('package.id'); 
		$package = new JObject();
		$package->checked_out = 0;
		$package->standalone  = true;
		$package->manifest    = null;
		$package->title       = null;
		$package->description = null;

		if (!empty($id)) 
		{
			// If the package exists get it
			$table = $this->getTable();

			if (is_array($id))
			{
				$id = $id[0];
			}

			$table->load($id);
			$package->setProperties($table->getProperties());

			// Get the manifest
			$xml = JFactory::getXML($table->path);

			if ($xml) 
			{
				$manifest = (string)$xml->manifest;
				$client   = (string)$xml->manifest->attributes()->client;
				LocaliseHelper::loadLanguage($manifest, $client);

				// Set up basic information
				$name = basename($table->path);
				$name = substr($name, 0, strlen($name) - 4);
	
				$package->id          = $id;
				$package->name        = $name;
				$package->manifest    = $manifest;
				$package->client      = $client;
				$package->standalone  = substr($manifest, 0, 4) == 'fil_';
				$package->core        = ((string)$xml->attributes()->core) == 'true';
				$package->icon        = (string)$xml->icon;
				$package->title       = (string)$xml->title;
				$package->description = (string)$xml->description;
				$package->license     = (string)$xml->license;
				$package->copyright   = (string)$xml->copyright;
				$package->author      = (string)$xml->author;
				$package->writable    = LocaliseHelper::isWritable($package->path);

				$user = JFactory::getUser($table->checked_out);
				$package->setProperties($table->getProperties());

				if ($package->checked_out == JFactory::getUser()->id) 
				{
					$package->checked_out = 0;
				}

				$package->editor = JText::sprintf('COM_LOCALISE_TEXT_PACKAGE_EDITOR', $user->name, $user->username);

				// Get the translations
				$package->translations  = array();
				$package->administrator = array();

				if ($xml->administrator) 
				{
					foreach ($xml->administrator->children() as $file) 
					{
						$data = (string)$file;

						if ($data) 
						{
							$package->translations[] = "administrator_$data";
						}
						else
						{
							$package->translations[] = "administrator_joomla";
						}

						$package->administrator[] = $data;
					}
				}

				$package->site = array();

				if ($xml->site) 
				{
					foreach ($xml->site->children() as $file) 
					{
						$data = (string)$file;

						if ($data) 
						{
							$package->translations[] = "site_$data";
						}
						else
						{
							$package->translations[] = "site_joomla";
						}

						$package->site[] = $data;
					}
				}

				$package->installation = array();

				if ($xml->installation) 
				{
					foreach ($xml->installation->children() as $file) 
					{
						$data = (string)$file->data();

						if ($data) 
						{
							$package->translations[] = "installation_$data";
						}
						else
						{
							$package->translations[] = "installation_joomla";
						}

						$package->installation[] = $data;
					}
				}
			}
			else
			{
				$package = null;
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILEEDIT'), $table->path);
			}
		}

		return $package;
	}

	/**
	 * Method to save data
	 *
	 * @param  array  the data to save
	 * @return  boolean  success or failure
	 */
	public function save($data) 
	{
		// Get the package name
		$name = $data['name'];

		// Get the package
		$package  = $this->getItem();
		$path     = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$name.xml";
		$manifest = $package->manifest ? $package->manifest : ('fil_localise_package_' . $name);
		$client   = $package->client ? $package->client : 'site';

		if ($package->standalone) 
		{
			$title = $package->title ? $package->title : ('fil_localise_package_' . $name);
			$description = $package->description ? $package->description : ('fil_localise_package_' . $name . '_desc');

			// Prepare text to save for the xml package description
			$text = '';
			$text.= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$text.= '<package>' . "\n";
			$text.= '<title>' . $title . '</title>' . "\n";
			$text.= '<description>' . $description . '</description>' . "\n";
			$text.= '<manifest client="' . $client . '">' . $manifest . '</manifest>' . "\n";
			$text.= '<icon>' . $data['icon'] . '</icon>' . "\n";
			$text.= '<author>' . $data['author'] . '</author>' . "\n";
			$text.= '<copyright>' . $data['copyright'] . '</copyright>' . "\n";
			$text.= '<license>' . $data['license'] . '</license>' . "\n";

			$administrator = array();
			$site          = array();
			$installation  = array();

			foreach ($data['translations'] as $translation) 
			{
				if (preg_match('/^site_(.*)$/', $translation, $matches)) 
				{
					$site[] = $matches[1];
				}

				if (preg_match('/^administrator_(.*)$/', $translation, $matches)) 
				{
					$administrator[] = $matches[1];
				}

				if (preg_match('/^installation_(.*)$/', $translation, $matches)) 
				{
					$installation[] = $matches[1];
				}
			}

			if (count($site)) 
			{
				$text.= '<site>' . "\n";

				foreach ($site as $translation) 
				{
					$text.= '<filename>' . $translation . '.ini</filename>' . "\n";
				}

				$text.= '</site>' . "\n";
			}

			if (count($administrator)) 
			{
				$text.= '<administrator>' . "\n";

				foreach ($administrator as $translation) 
				{
					$text.= '<filename>' . $translation . '.ini</filename>' . "\n";
				}

				$text.= '</administrator>' . "\n";
			}

			if (count($installation)) 
			{
				$text.= '<installation>' . "\n";

				foreach ($installation as $translation) 
				{
					$text.= '<filename>' . $translation . '.ini</filename>' . "\n";
				}

				$text.= '</installation>' . "\n";
			}

			$text.= '</package>' . "\n";

			// Set FTP credentials, if given.
			JClientHelper::setCredentialsFromRequest('ftp');
			$ftp = JClientHelper::getCredentials('ftp');

			// Try to make the file writeable.
			if ($exists && !$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0644')) 
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_WRITABLE', $path));
				return false;
			}

			$return = JFile::write($path, $text);

			// Try to make the file unwriteable.
			if (!$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0444')) 
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_UNWRITABLE', $path));
				return false;
			}
			else if (!$return) 
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILESAVE', $path));
				return false;
			}
		}

		// Save the title and the description in the language file
		$translation_path  = LocaliseHelper::findTranslationPath($client, JFactory::getLanguage()->getTag(), $manifest);
		$translation_id    = LocaliseHelper::getFileId($translation_path);
		$translation_model = JModelLegacy::getInstance('Translation', 'LocaliseModel', array('ignore_request' => true));

		if ($translation_model->checkout($translation_id)) 
		{
			$translation_model->setState('translation.path', $translation_path);
			$translation_model->setState('translation.client', $client);
			$translation = $translation_model->getItem();
			$sections    = LocaliseHelper::parseSections($translation_path);
		}
		else
		{
		}

		$text = '';
		$text.= strtoupper($title) . '="' . str_replace('"', '"_QQ_"', $data['title']) . "\"\n";
		$text.= strtoupper($description) . '="' . str_replace('"', '"_QQ_"', $data['description']) . "\"\n";
		$tag  = JFactory::getLanguage()->getTag();
		$languagePath = JPATH_SITE . "/language/$tag/$tag.$manifest.ini";

		// Try to make the file writeable.
		if ($exists && !$ftp['enabled'] && JPath::isOwner($languagePath) && !JPath::setPermissions($languagePath, '0644')) 
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_WRITABLE', $languagePath));
			return false;
		}

		$return = JFile::write($languagePath, $text);

		// Try to make the file unwriteable.
		if (!$ftp['enabled'] && JPath::isOwner($languagePath) && !JPath::setPermissions($languagePath, '0444')) 
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_UNWRITABLE', $languagePath));
			return false;
		}
		else if (!$return) 
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILESAVE', $languagePath));
			return false;
		}

		$id = LocaliseHelper::getFileId($path);
		$this->setState('package.id', $id);

		// Bind the rules.
		$table = $this->getTable();
		$table->load($id);

		if (isset($data['rules'])) 
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check()) 
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) 
		{
			$this->setError($table->getError());
			return false;
		}

		return true;
	}
} 