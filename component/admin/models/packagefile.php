<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
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
 *
 * @since       1.0
 */
class LocaliseModelPackageFile extends JModelForm
{
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
	 * @param   int  $pk  The ID of the primary key.
	 *
	 * @return  boolean
	 */
	public function checkout($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('package.id');

		return parent::checkout($pk);
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param   int  $pk  The ID of the primary key.
	 *
	 * @return  boolean
	 */
	public function checkin($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('package.id');

		return parent::checkin($pk);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 */
	public function getTable($type = 'Localise', $prefix = 'LocaliseTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$id   = $this->getState('package.id');
		$name = $this->getState('package.name');
		$form = $this->loadForm('com_localise.packagefile', 'packagefile', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$form->setFieldAttribute('translations', 'packagefile', $name, 'translations');

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
	 * @return   mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_localise.edit.packagefile.data', array());

		// Get the package data.
		if (empty($data))
		{
			$data = $this->getItem();
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
		$package = new JObject;
		$package->checked_out = 0;
		$package->standalone  = true;
		$package->manifest    = null;
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
			$xml = simplexml_load_file($table->path);

			if ($xml)
			{
				$manifest = (string) $xml->manifest;
				//$client   = (string) $xml->manifest->attributes()->client;
				//LocaliseHelper::loadLanguage($manifest, $client);

				// Set up basic information
				$name = basename($table->path);
				$name = substr($name, 0, strlen($name) - 4);

				$package->id          = $id;
				$package->name        = $name;
				$package->manifest    = $manifest;
				//$package->client      = $client;
				//$package->standalone  = substr($manifest, 0, 4) == 'fil_';
				$package->title       = (string) $xml->title;
				$package->version     = (string) $xml->version;
				$package->description = (string) $xml->description;
				$package->language    = (string) $xml->language;
				$package->license     = (string) $xml->license;
				$package->copyright   = (string) $xml->copyright;
				$package->author      = (string) $xml->author;
				$package->authoremail = (string) $xml->authoremail;
				$package->authorurl   = (string) $xml->authorurl;
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
						$data = (string) $file;

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
						$data = (string) $file;

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
	 * @param   array  $data  the data to save
	 *
	 * @return  boolean  success or failure
	 */
	public function save($data)
	{
		// Get the package name
		$name = $data['name'];

		// Get the package
		$package  = $this->getItem();
		$path     = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$name.xml";
		$manifest = $name;
		//$client   = $package->client ? $package->client : 'site';

		if ($package->standalone)
		{
			$title = $name;
			$description = $data['description'];

			$dom = new DOMDocument('1.0', 'utf-8');
			// Create simple XML element and base package tag
			$packageXml = $dom->createElement('package');

			// Add main package information
			$titleElement = $dom->createElement('title', $title);
			$descriptionElement = $dom->createElement('description', $description);
			$manifestElement = $dom->createElement('manifest', $manifest);
			$versionElement = $dom->createElement('version', $data['version']);
			$authorElement = $dom->createElement('author', $data['author']);
			$copyrightElement = $dom->createElement('copyright', $data['copyright']);
			$licenseElement = $dom->createElement('license', $data['license']);
			$authorEmailElement = $dom->createElement('authoremail', $data['authoremail']);
			$authorUrlElement = $dom->createElement('authorurl', $data['authorurl']);
			$languageElement = $dom->createElement('language', $data['language']);
			$copyrightElement = $dom->createElement('copyright', $data['copyright']);

			// Set the client attribute on the manifest element
			//$clientAttribute = $dom->createAttribute('client');
			//$clientAttribute->value = $client;
			//$manifestElement->appendChild($clientAttribute);

			// Add all the elements to the parent <package> tag
			$packageXml->appendChild($titleElement);
			$packageXml->appendChild($descriptionElement);
			$packageXml->appendChild($manifestElement);
			$packageXml->appendChild($versionElement);
			$packageXml->appendChild($authorElement);
			$packageXml->appendChild($copyrightElement);
			$packageXml->appendChild($licenseElement);
			$packageXml->appendChild($authorEmailElement);
			$packageXml->appendChild($authorUrlElement);
			$packageXml->appendChild($languageElement);
			$packageXml->appendChild($copyrightElement);

			$administrator = array();
			$site          = array();

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

			}

			// Add the site language files
			if (count($site))
			{
				$siteXml = $dom->createElement('site');

				foreach ($site as $translation)
				{
					$fileElement = $dom->createElement('filename', $translation . '.ini');
					$siteXml->appendChild($fileElement);
				}

				$packageXml->appendChild($siteXml);
			}

			// Add the administrator language files
			if (count($administrator))
			{
				$adminXml = $dom->createElement('administrator');

				foreach ($administrator as $translation)
				{
					$fileElement = $dom->createElement('filename', $translation . '.ini');
					$adminXml->appendChild($fileElement);
				}

				$packageXml->appendChild($adminXml);
			}

			$dom->appendChild($packageXml);

			// Set FTP credentials, if given.
			JClientHelper::setCredentialsFromRequest('ftp');
			$ftp = JClientHelper::getCredentials('ftp');

			// Try to make the file writeable.
			if (!$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0644'))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_WRITABLE', $path));

				return false;
			}

			// Make the XML look pretty
			$dom->formatOutput = true;
			$formattedXML = $dom->saveXML();

			$return = JFile::write($path, $formattedXML);

			// Try to make the file unwriteable.
			if (!$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0444'))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_UNWRITABLE', $path));

				return false;
			}
			elseif (!$return)
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILESAVE', $path));

				return false;
			}
		}

		/**
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
		//$text .= strtoupper($title) . '="' . str_replace('"', '"_QQ_"', $data['title']) . "\"\n";
		//$text .= strtoupper($description) . '="' . str_replace('"', '"_QQ_"', $data['description']) . "\"\n";
		//$tag  = JFactory::getLanguage()->getTag();
		//$languagePath = JPATH_SITE . "/language/$tag/$tag.$manifest.ini";

		// Try to make the file writeable.
		if (!$ftp['enabled'] && JPath::isOwner($languagePath) && !JPath::setPermissions($languagePath, '0644'))
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_WRITABLE', $languagePath));

			return false;
		}

		//$return = JFile::write($languagePath, $text);

		// Try to make the file unwriteable.
		if (!$ftp['enabled'] && JPath::isOwner($languagePath) && !JPath::setPermissions($languagePath, '0444'))
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_UNWRITABLE', $languagePath));

			return false;
		}
		elseif (!$return)
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILESAVE', $languagePath));

			return false;
		}

		*/
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

	/**
	 * Method to generate and download a package
	 *
	 * @param   array  $data  the data to generate the package
	 *
	 * @return  boolean  success or failure
	 */
	public function download($data){
		//the data could potentially be loaded from the file with $this->getItem() instead of using directly the data from the post


		$administrator = array();
		$site          = array();

		// Delete old files
		$delete = JFolder::files(JPATH_ROOT . '/tmp/', 'com_localise_', false, true);

		if (!empty($delete))
		{
			if (!JFile::delete($delete))
			{
				// JFile::delete throws an error
				$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPDELETE'));

				return false;
			}
		}

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
		}


		$parts = explode('.',$data['version']);
		$small_version = implode('.',array($parts[0],$parts[1]));
		// Prepare text to save for the xml package description
		$text = '';
		$text .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$text .= '<extension type="file" version="'.$small_version.'" method="upgrade">' . "\n";
		$text .= "\t".'<name>' . $data['name'].$data['language'] . '</name>' . "\n";
		$text .= "\t".'<version>' . $data['version'] . '</version>' . "\n";
		$text .= "\t".'<creationDate>' . date('d/m/Y') . '</creationDate>' . "\n";
		$text .= "\t".'<author>' . $data['author'] . '</author>' . "\n";
		$text .= "\t".'<authorEmail>' . $data['authoremail'] . '</authorEmail>' . "\n";
		$text .= "\t".'<authorUrl>' . $data['authorurl'] . '</authorUrl>' . "\n";
		$text .= "\t".'<copyright>' . $data['copyright'] . '</copyright>' . "\n";
		$text .= "\t".'<license>' . $data['license'] . '</license>' . "\n";
		$text .= "\t".'<description><![CDATA[' . $data['description'] . ']]></description>' . "\n";
		$text .= "\t".'<fileset>'. "\n";

		if (count($site))
		{
			$text .= "\t\t". '<files ';
			$text .= 'folder="site/' . $data['language'] . '"';
			$text .= ' target="language/' . $data['language'] . '">' . "\n";
			$site_package_files = array();
			//$site_package_zip_path = JPATH_ROOT . '/tmp/' . uniqid('com_localise_') . '.zip';


			foreach ($site as $translation)
			{
				$file_data = JFile::read(JPATH_ROOT . '/language/' . $data['language'] . '/' . $data['language'] . '.' . $translation . '.ini');

				// @TODO Search also for ini file in the 3pd extension folder
				//if (empty($file_data)
				//{
				//	$file_data = JFile::read(JPATH_ROOT . '/EXTENSION-PATH/language/' . $data['language'] . '/' . $data['language'] . '.' . $translation . '.ini');
			//	}
				if (!empty($file_data))
				{
					$text .= "\t\t\t".'<filename>' . $data['language'] . '.' . $translation . '.ini</filename>' . "\n";
					$site_package_files[] = array('name'=>$data['language'] . '.' . $translation . '.ini','data'=>$file_data);
				}
			}
			/**
			$site_txt .= "\t\t".'<filename file="meta">install.xml</filename>' . "\n";
			$site_txt .= "\t\t".'<filename file="meta">' . $data['language'] . '.xml</filename>' . "\n";
			$site_txt .= "\t".'</files>' . "\n";
			$site_txt .= "\t".'<params />' . "\n";
			$site_txt .= "\t".'</extension>' . "\n";
			$site_package_files[] = array('name'=>'install.xml','data'=>$site_txt);
			$language_data = JFile::read(JPATH_ROOT . '/language/' . $data['language'] . '/' . $data['language'] . '.xml');
			$site_package_files[] = array('name' => $data['language'] . '.xml','data'=>$language_data);
			$language_data = JFile::read(JPATH_ROOT . '/language/' . $data['language'] . '/' . $data['language'] . '.localise.php');
			$site_package_files[] = array('name' => $data['language'] . '.localise.php','data' => $language_data);

			$site_zip_path = JPATH_ROOT . '/tmp/' . uniqid('com_localise_') . '.zip';
			if (!$packager = JArchive::getAdapter('zip'))
			{
				$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ADAPTER'));

				return false;
			}
			else
			{
				if (!$packager->create($site_zip_path, $site_package_files))
				{
					$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPCREATE'));

					return false;
				}
			}
			*/
			$text .= "\t".'</files>' . "\n";

			foreach($site_package_files as $file)
			{
				$main_package_files[]= array('name'=>'site/'. $data['language'] . '/' . $file['name'],'data' => $file['data']);
			}

		}

		if (count($administrator))
		{
			$text .= "\t\t". '<files ';
			$text .= 'folder="admin/' . $data['language'] . '"';
			$text .= ' target="administrator/language/' . $data['language'] . '">' . "\n";

			$admin_package_files = array();

			foreach ($administrator as $translation)
			{
				$file_data = JFile::read(JPATH_ROOT . '/administrator/language/' . $data['language'] . '/' . $data['language'] . '.' . $translation . '.ini');

				// @TODO Search also for ini file in the 3pd extension folder
				//if (empty($file_data)
				//{
				//	$file_data = JFile::read(JPATH_ROOT . '/administrator/EXTENSION-PATH/language/' . $data['language'] . '/' . $data['language'] . '.' . $translation . '.ini');
			//	}
				if(!empty($file_data))
				{
					$text .= "\t\t\t".'<filename>' . $data['language'] . '.' . $translation . '.ini</filename>' . "\n";
					$admin_package_files[] = array('name' => $data['language'] . '.' . $translation . '.ini','data' => $file_data);

				}
			}

			/**
			$admin_txt .= "\t\t".'<filename file="meta">install.xml</filename>' . "\n";
			$admin_txt .= "\t\t".'<filename file="meta">' . $data['language'].'.xml</filename>' . "\n";
			$admin_txt .= "\t".'</files>' . "\n";
			$admin_txt .= "\t".'<params />' . "\n";
			$admin_txt .= "\t".'</extension>' . "\n";
			$admin_package_files[] = array('name'=>'install.xml','data'=>$admin_txt);
			$language_data = JFile::read(JPATH_ROOT . '/administrator/language/' . $data['language'] . '/' . $data['language'] . '.xml');
			$admin_package_files[] = array('name'=>$data['language'] . '.xml','data' => $language_data);
			$language_data = JFile::read(JPATH_ROOT . '/administrator/language/' . $data['language'] . '/' . $data['language'] . '.localise.php');
			$admin_package_files[] = array('name'=>$data['language'] . '.localise.php','data' => $language_data);


			$admin_zip_path = JPATH_ROOT . '/tmp/' . uniqid('com_localise_') . '.zip';
			if (!$packager = JArchive::getAdapter('zip'))
			{
				$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ADAPTER'));

				return false;
			}
			else
			{
				if (!$packager->create($admin_zip_path, $admin_package_files))
				{
					$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPCREATE'));

					return false;
				}
			}
			*/
			$text .= "\t\t".'</files>' . "\n";

			foreach($admin_package_files as $file)
			{
				$main_package_files[]= array('name'=>'admin/'. $data['language'] . '/' . $file['name'],'data' => $file['data']);
			}
		}

		$text .= "\t" . '</fileset>' . "\n";
		$text .= '</extension>' . "\n";

		$main_package_files[] = array('name'=>$data['name'] . $data['language'].'.xml','data'=>$text);

		$ziproot = JPATH_ROOT . '/tmp/' . uniqid('com_localise_main_') . '.zip';


		// Run the packager
		if (!$packager = JArchive::getAdapter('zip'))
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ADAPTER'));

			return false;
		}
		else
		{
			if (!$packager->create($ziproot, $main_package_files))
			{
				$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPCREATE'));

				return false;
			}
		}

		ob_clean();
		$zipdata = JFile::read($ziproot);
		header("Expires: 0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' .$data['name'] . $data['language'] . $data['version'] .' .zip"');
		header('Content-Length: '.strlen($zipdata));
		header("Cache-Control: maxage=1");
		header("Pragma: public");
		header("Content-Transfer-Encoding: binary");
		echo $zipdata;
		exit;
	}
}
