<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.client.helper');
jimport('joomla.access.rules');

/**
 * Language model.
 *
 * @since  1.0
 */
class LocaliseModelLanguage extends JModelAdmin
{
	protected $context = 'com_localise.language';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$jinput = JFactory::getApplication()->input;

		$client = $jinput->get('client', 'site', 'cmd');
		$tag    = $jinput->get('tag', '', 'cmd');
		$id     = $jinput->get('id', '0', 'int');

		$this->setState('language.client', $client);
		$this->setState('language.tag', $tag);
		$this->setState('language.id', $id);

		parent::populateState();
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable              A database object
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
	 * @return  mixed               A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_localise.language', 'language', array('control'   => 'jform', 'load_data' => $loadData));

		// Make Client field readonly when the file exists
		if ($this->getState('language.id'))
		{
			$form->setFieldAttribute('client', 'readonly', 'true');

			$client = $form->getValue('client');

			if ($client == "installation")
			{
				$form->setFieldAttribute('locale', 'required', 'false');
				$form->setFieldAttribute('locale', 'disabled', 'true');

				$form->setFieldAttribute('weekEnd', 'disabled', 'true');

				$form->setFieldAttribute('firstDay', 'required', 'false');
				$form->setFieldAttribute('firstDay', 'disabled', 'true');

				$form->setFieldAttribute('calendar', 'required', 'false');
				$form->setFieldAttribute('calendar', 'disabled', 'true');

				$form->setFieldAttribute('authorEmail', 'disabled', 'true');
				$form->setFieldAttribute('authorUrl', 'disabled', 'true');
				$form->setFieldAttribute('copyright', 'disabled', 'true');
			}
		}

		// Calendar and Native Name fields are new in 3.7.x
		if (version_compare(JVERSION, '3.7', 'lt'))
		{
			$form->setFieldAttribute('calendar', 'required', 'false');
			$form->setFieldAttribute('calendar', 'disabled', 'true');

			$form->setFieldAttribute('nativeName', 'required', 'false');
			$form->setFieldAttribute('nativeName', 'disabled', 'true');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   JObject  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_localise.edit.language.data', array());

		// Get the language data.
		$data = empty($data) ? $this->getItem() : new JObject($data);

		$data->joomlacopyright = sprintf("Copyright (C) 2005 - %s Open Source Matters. All rights reserved.", JFactory::getDate()->format('Y'));

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
		if (version_compare(JVERSION, '4.0', 'le') && JError::isError($form))
		{
			$this->setError($form->getMessage());

			return false;
		}

		return $form;
	}

	/**
	 * Method to get the language.
	 *
	 * @param   integer  $pk  The ID of the primary key.
	 *
	 * @return JObject
	 */
	public function getItem($pk = null)
	{
		$id     = $this->getState('language.id');
		$client = $this->getState('language.client');
		$tag    = $this->getState('language.tag');

		$language = new JObject;

		$language->id          = $id;
		$language->client      = $client;
		$language->tag         = $tag;
		$language->checked_out = 0;

		$params = JComponentHelper::getParams('com_localise');
		$language->author      = isset($language->author)
			? $language->author
			: $params->get('author');
		$language->authorEmail = isset($language->authorEmail)
			? $language->authorEmail
			: $params->get('authorEmail');
		$language->authorUrl   = isset($language->authorUrl)
			? $language->authorUrl
			: $params->get('authorUrl');
		$language->copyright   = isset($language->copyright)
			? $language->copyright
			: $params->get('copyright');
		$language->license     = isset($language->license)
			? $language->license
			: $params->get('license');

		if (!empty($id))
		{
			$table = $this->getTable();
			$table->load($id);

			$user = JFactory::getUser($table->checked_out);

			$language->setProperties($table->getProperties());

			if ($language->checked_out == JFactory::getUser()->id)
			{
				$language->checked_out = 0;
			}

			$language->editor   = JText::sprintf('COM_LOCALISE_TEXT_LANGUAGE_EDITOR', $user->name, $user->username);
			$language->writable = LocaliseHelper::isWritable($language->path);

			if (JFile::exists($language->path))
			{
				$xml = simplexml_load_file($language->path);

				if ($xml)
				{
					foreach ($xml->children() as $node)
					{
						if ($node->getName() == 'metadata')
						{
							// Metadata nodes
							foreach ($node->children() as $subnode)
							{
								$property            = $subnode->getName();
								$language->$property = (string) $subnode;
							}
						}
						else
						{
							// Main nodes
							$property = $node->getName();

							if ($property == 'copyright')
							{
								if (isset($language->joomlacopyright))
								{
									$language->copyright[] = (string) $node;
								}
								else
								{
									$language->copyright       = array();
									$language->joomlacopyright = (string) $node;
								}
							}
							else
							{
								$language->$property = (string) $node;
							}
						}
					}

					$language->copyright = implode('<br/>', $language->copyright);
				}
				else
				{
					$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_FILEEDIT', $language->path));
				}
			}
		}

		return $language;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null)
	{
		if ($data['client'] == "installation")
		{
			$form->setFieldAttribute('locale', 'required', 'false');

			$form->setFieldAttribute('firstDay', 'required', 'false');

			$form->setFieldAttribute('calendar', 'required', 'false');
		}

		return parent::validate($form, $data, $group);
	}

	/**
	 * Saves a language
	 *
	 * @param   array  $data  Language data
	 *
	 * @return bool
	 */
	public function save($data = array())
	{
		$id = $this->getState('language.id');
		$tag    = $data['tag'];

		// Trim whitespace in $tag
		$tag = JFilterInput::getInstance()->clean($tag, 'TRIM');

		// Check tag is correct
		if (strpos($tag, '-') == false)
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_LANGUAGE_TAG'));

			return false;
		}

		$partstag = explode('-', $tag);

		if (strlen($partstag[1]) > 2 || strtoupper($partstag[1]) != $partstag[1]
			|| strlen($partstag[0]) > 3 || strtolower($partstag[0]) != $partstag[0])
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_LANGUAGE_TAG'));

			return false;
		}

		// Checks that a custom language name has been entered
		if ($data['name'] == "[Name of language] ([Country name])")
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_LANGUAGE_NAME'));

			return false;
		}

		$client = $data['client'];
		$path   = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.xml";
		$exists = JFile::exists($path);
		$parts = explode('.', $data['version']);
		$small_version = implode('.', array($parts[0],$parts[1]));

		if ($exists && !empty($id) || !$exists && empty($id))
		{
			$text = '';
			$text .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$text .= '<metafile version="' . $small_version . '" client="' . htmlspecialchars($client, ENT_COMPAT, 'UTF-8') . '">' . "\n";
			$text .= "\t" . '<tag>' . htmlspecialchars($tag, ENT_COMPAT, 'UTF-8') . '</tag>' . "\n";
			$text .= "\t" . '<name>' . htmlspecialchars($data['name'], ENT_COMPAT, 'UTF-8') . '</name>' . "\n";
			$text .= "\t" . '<version>' . htmlspecialchars($data['version'], ENT_COMPAT, 'UTF-8') . '</version>' . "\n";
			$text .= "\t" . '<creationDate>' . htmlspecialchars($data['creationDate'], ENT_COMPAT, 'UTF-8') . '</creationDate>' . "\n";
			$text .= "\t" . '<author>' . htmlspecialchars($data['author'], ENT_COMPAT, 'UTF-8') . '</author>' . "\n";

			// AuthorEmail, authorURL are not used in the installation
			if ($client != "installation")
			{
				$text .= "\t" . '<authorEmail>' . htmlspecialchars($data['authorEmail'], ENT_COMPAT, 'UTF-8') . '</authorEmail>' . "\n";
				$text .= "\t" . '<authorUrl>' . htmlspecialchars($data['authorUrl'], ENT_COMPAT, 'UTF-8') . '</authorUrl>' . "\n";
			}

			$text .= "\t" . '<copyright>' . htmlspecialchars($data['joomlacopyright'], ENT_COMPAT, 'UTF-8') . '</copyright>' . "\n";

			// Author copyright is not used in installation. It is present in CREDITS file
			if ($client != "installation")
			{
				$data['copyright'] = explode("\n", $data['copyright']);

				foreach ($data['copyright'] as $copyright)
				{
					if ($copyright)
					{
						$text .= "\t" . '<copyright>' . htmlspecialchars($copyright, ENT_COMPAT, 'UTF-8') . '</copyright>' . "\n";
					}
				}
			}

			$text .= "\t" . '<license>' . htmlspecialchars($data['license'], ENT_COMPAT, 'UTF-8') . '</license>' . "\n";
			$text .= "\t" . '<description>' . htmlspecialchars($data['description'], ENT_COMPAT, 'UTF-8') . '</description>' . "\n";
			$text .= "\t" . '<metadata>' . "\n";
			$text .= "\t\t" . '<name>' . htmlspecialchars($data['name'], ENT_COMPAT, 'UTF-8') . '</name>' . "\n";

			if (version_compare(JVERSION, '3.7', 'ge'))
			{
				$text .= "\t\t" . '<nativeName>' . htmlspecialchars($data['nativeName'], ENT_COMPAT, 'UTF-8') . '</nativeName>' . "\n";
			}

			$text .= "\t\t" . '<tag>' . htmlspecialchars($data['tag'], ENT_COMPAT, 'UTF-8') . '</tag>' . "\n";
			$text .= "\t\t" . '<rtl>' . htmlspecialchars($data['rtl'], ENT_COMPAT, 'UTF-8') . '</rtl>' . "\n";

			// Locale, firstDay and weekEnd are not used in the installation
			if ($client != "installation")
			{
				$text .= "\t\t" . '<locale>' . htmlspecialchars($data['locale'], ENT_COMPAT, 'UTF-8') . '</locale>' . "\n";
				$text .= "\t\t" . '<firstDay>' . htmlspecialchars($data['firstDay'], ENT_COMPAT, 'UTF-8') . '</firstDay>' . "\n";
				$text .= "\t\t" . '<weekEnd>' . htmlspecialchars($data['weekEnd'], ENT_COMPAT, 'UTF-8') . '</weekEnd>' . "\n";

				if (version_compare(JVERSION, '3.7', 'ge'))
				{
					$text .= "\t\t" . '<calendar>' . htmlspecialchars($data['calendar'], ENT_COMPAT, 'UTF-8') . '</calendar>' . "\n";
				}
			}

			$text .= "\t" . '</metadata>' . "\n";
			$text .= "\t" . '<params />' . "\n";
			$text .= '</metafile>' . "\n";

			// Set FTP credentials, if given.
			JClientHelper::setCredentialsFromRequest('ftp');
			$ftp = JClientHelper::getCredentials('ftp');

			// Try to make the file writeable.
			if ($exists && !$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, '0644'))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_WRITABLE', $path));

				return false;
			}

			$return = JFile::write($path, $text);

			// Get the Localise parameters
			$params = JComponentHelper::getParams('com_localise');

			// Get the file save permission
			$fileSavePermission = $params->get('filesavepermission', '0444');

			// Try to make the template file unwriteable.
			if (!$ftp['enabled'] && JPath::isOwner($path) && !JPath::setPermissions($path, $fileSavePermission))
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_UNWRITABLE', $path));

				return false;
			}
			else
			{
				if (!$return)
				{
					$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_FILESAVE', $path));

					return false;
				}
			}

			$id = LocaliseHelper::getFileId($path);

			// Dummy call to populate state
			$this->getState('language.id');

			$this->setState('language.id', $id);

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
		else
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_FILERESET', $path));

			return false;
		}
	}

	/**
	 * Remove languages
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  true for success, false for failure
	 */
	public function delete(&$pks = null)
	{
		$params  = JComponentHelper::getParams('com_languages');
		$id      = $this->getState('language.id');
		$tag     = $this->getState('language.tag');
		$client  = $this->getState('language.client');
		$default = $params->get($client, 'en-GB');
		$path    = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag";

		if ($tag == $default)
		{
			$this->setError(JText::sprintf('COM_LOCALISE_CANNOT_REMOVE_DEFAULT_LANGUAGE', $path));

			return false;
		}

		// Check we're not trying to remove an installed langauge pack
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('name'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('package'))
			->where($db->quoteName('state') . ' = 0')
			->where($db->quoteName('element') . ' = "pkg_' . $tag . '"');
		$db->setQuery($query);
		$installedPack = $db->loadResult('name');

		if ($installedPack != null)
		{
			$this->setError(JText::sprintf('COM_LOCALISE_CANNOT_REMOVE_INSTALLED_LANGUAGE', $tag));

			return false;
		}

		if ($tag == 'en-GB')
		{
			$this->setError(JText::_('COM_LOCALISE_CANNOT_REMOVE_ENGLISH_LANGUAGE'));

			return false;
		}

		if (!JFactory::getUser()->authorise('localise.delete', $this->option . '.' . $id))
		{
			$this->setError(JText::_('COM_LOCALISE_CANNOT_REMOVE_LANGUAGE'));

			return false;
		}

		if (!JFolder::delete($path))
		{
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGES_REMOVE', "$path"));

			return false;
		}

		// Clear UserState for select.tag if the language deleted is selected in the filter.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_localise.select');

		if ($data['tag'] == $tag)
		{
			$data['tag'] = '';
			$app->setUserState('com_localise.select', $data);
		}

		$pks = array($id);

		return parent::delete($pks);
	}

	/**
	 * Method to copy the files from the reference lang to the translation language
	 *
	 * @return  boolean   true if copy is fine, false otherwise
	 *
	 * @since	4.0.17
	 */
	public function copy()
	{
		$app     = JFactory::getApplication();
		$params  = JComponentHelper::getParams('com_localise');
		$data    = $app->input->get('jform', array(), 'array');

		$id      = $app->getUserState('com_localise.edit.language.id');
		$ref_tag = $params->get('reference', 'en-GB');
		$tag     = $data['tag'];
		$client  = $data['client'];

		$fromPath = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$ref_tag/";
		$toPath   = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/";

		// Make sure new language and reference language are different
		// en-GB should be left alone
		if ($tag == $ref_tag || $tag == 'en-GB')
		{
			$app->enqueueMessage(JText::_('COM_LOCALISE_ERROR_LANGUAGE_COPY_FILES_NOT_PERMITTED'), 'error');

			return false;
		}

		// Are there already ini files in the destination folder?
		$inifiles = JFolder::files($toPath, ".ini$");

		if (!empty($inifiles))
		{
			$app->enqueueMessage(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_NOT_ONLY_XML', $client, $tag), 'error');

			return false;
		}

		$refFiles = JFolder::files($fromPath);

		foreach ($refFiles as $file)
		{
			$ext = JFile::getExt($file);

			// We do not want to copy existing xmls
			if ($ext !== 'xml')
			{
				// Changing prefix for the copied files
				$destFile = str_replace($ref_tag, $tag, $file);

				if (!JFile::copy($fromPath . $file, $toPath . $destFile))
				{
					$app->enqueueMessage(JText::Sprintf('COM_LOCALISE_ERROR_LANGUAGE_COULD_NOT_COPY_FILES', $client, $ref_tag, $tag), 'error');

					return false;
				}
			}
		}

		// Modify localise.php to fit new tag
		$refclassname	= str_replace('-', '_', $ref_tag);
		$refclassname	= ucfirst($refclassname);
		$langclassname	= str_replace('-', '_', $tag);
		$langclassname	= ucfirst($langclassname);
		$refComment     = "* " . $ref_tag . " localise class";
		$langComment    = "* " . $tag . " localise class";

		$localisephpPath = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.localise.php";

		if (JFile::exists($localisephpPath))
		{
			$language_data = file_get_contents($localisephpPath);
			$language_data = str_replace($refclassname, $langclassname, $language_data);
			$language_data = str_replace($refComment, $langComment, $language_data);
			JFile::write($localisephpPath, $language_data);
		}

		return true;
	}
}
