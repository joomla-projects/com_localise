<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
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
class LocaliseModelLanguage extends JModelForm
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
	 * Method to override check-out a row for editing.
	 *
	 * @param   int  $pk  The ID of the primary key.
	 *
	 * @return  boolean
	 */
	public function checkout($pk = null)
	{
		$app = JFactory::getApplication('administrator');
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $app->getUserState('com_localise.edit.language.id');

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
		$app = JFactory::getApplication('administrator');
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $app->getUserState('com_localise.edit.language.id');

		return parent::checkin($pk);
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
		if (JError::isError($form))
		{
			$this->setError($form->getMessage());

			return false;
		}

		return $form;
	}

	/**
	 * Method to get the language.
	 *
	 * @return JObject
	 */
	public function getItem()
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
								$language->$property = $subnode;
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
									$language->copyright[] = $node;
								}
								else
								{
									$language->copyright       = array();
									$language->joomlacopyright = $node;
								}
							}
							else
							{
								$language->$property = $node;
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
		$client = $data['client'];
		$path   = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.xml";
		$exists = JFile::exists($path);

		if ($exists && !empty($id) || !$exists && empty($id))
		{
			$text = '';
			$text .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$text .= '<metafile version="3.1" client="' . htmlspecialchars($client, ENT_COMPAT, 'UTF-8') . '">' . "\n";
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
			$text .= "\t\t" . '<tag>' . htmlspecialchars($data['tag'], ENT_COMPAT, 'UTF-8') . '</tag>' . "\n";
			$text .= "\t\t" . '<rtl>' . htmlspecialchars($data['rtl'], ENT_COMPAT, 'UTF-8') . '</rtl>' . "\n";

			// Locale, firstDay and weekEnd are not used in the installation
			if ($client != "installation")
			{
				$text .= "\t\t" . '<locale>' . htmlspecialchars($data['locale'], ENT_COMPAT, 'UTF-8') . '</locale>' . "\n";
				$text .= "\t\t" . '<firstDay>' . htmlspecialchars($data['firstDay'], ENT_COMPAT, 'UTF-8') . '</firstDay>' . "\n";
				$text .= "\t\t" . '<weekEnd>' . htmlspecialchars($data['weekEnd'], ENT_COMPAT, 'UTF-8') . '</weekEnd>' . "\n";
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
	 * @return  boolean  true for success, false for failure
	 */
	public function delete()
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

		return true;
	}
}
