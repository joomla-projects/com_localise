<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.client.helper');
jimport('joomla.access.rules' );

/**
 * Language model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 */
class LocaliseModelLanguage extends JModelForm
{
	protected $context = 'com_localise.language';

	protected function populateState() 
	{
		$jinput = JFactory::getApplication()->input;

		$client = $jinput->get('client', 'site' , 'cmd');
		$tag    = $jinput->get('tag'   , '' , 'cmd');
		$id     = $jinput->get('id'    , '0', 'int');

		$this->setState('language.client', $client);
		$this->setState('language.tag'   , $tag);
		$this->setState('language.id'    , $id);

		parent::populateState();
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
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('language.id');
		return parent::checkout($pk);
	}

	/**
	 * Method to checkin a row.
	 *
	 * @param   integer  The ID of the primary key.
	 *
	 * @return  boolean
	 */
	public function checkin($pk = null) 
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('language.id');
		return parent::checkin($pk);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   type     $type      The table type to instantiate
	 * @param   string   $prefix    A prefix for the table class name. Optional.
	 * @param   array    $options   Configuration array for model. Optional.
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
	 * @return  mixed               A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		return $this->loadForm('com_localise.language', 'language', array('control' => 'jform', 'load_data' => $loadData));
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since  1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_localise.edit.language.data', array());

		// Get the language data.
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		if (empty($data)) 
		{
			$data = new JObject();
			$data->client = $this->getState('language.client');
		}

		$data->joomlacopyright = sprintf("Copyright (C) 2005 - %s Open Source Matters. All rights reserved.", JFactory::getDate()->format('Y'));

		// Fill with component params if data not exist
		$params = JComponentHelper::getParams('com_localise');
		$data->author      = isset($data->author)      ? $data->author      : $params->get('author');
		$data->authorEmail = isset($data->authorEmail) ? $data->authorEmail : $params->get('authorEmail');
		$data->authorUrl   = isset($data->authorUrl)   ? $data->authorUrl   : $params->get('authorUrl');
		$data->copyright   = isset($data->copyright)   ? $data->copyright   : $params->get('copyright');
		$data->license     = isset($data->license)     ? $data->license     : $params->get('license');

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
	 */
	public function getItem() 
	{
		$id     = $this->getState('language.id');
		$client = $this->getState('language.client');
		$tag    = $this->getState('language.tag');

		$language = new JObject();

		$language->id          = $id;
		$language->client      = $client;
		$language->tag         = $tag;
		$language->checked_out = 0;

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
				$xml = JFactory::getXML($language->path);

				if ($xml) 
				{
					foreach ($xml->children() as $node) 
					{
						if ($node->name() == 'metadata') 
						{
							// metadata nodes
							foreach ($node->children() as $subnode) 
							{
								$property = $subnode->name();
								$language->$property = $subnode;
							}
						}
						else
						{
							// main nodes
							$property = $node->name();

							if ($property == 'copyright') 
							{
								if (isset($language->joomlacopyright)) 
								{
									$language->copyright[] = $node;
								}
								else
								{
									$language->copyright = array();
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

	public function save($data = array()) 
	{
		$id     = $this->getState('language.id');
		$tag    = $data['tag'];
		$client = $data['client'];
		$path   = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.xml";
		$exists = JFile::exists($path);

		if ($exists && !empty($id) || !$exists && empty($id)) 
		{
			$text = '';
			$text.= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$text.= '<metafile version="3.1" client="' . htmlspecialchars($client, ENT_COMPAT, 'UTF-8') . '">' . "\n";
			$text.= "\t" . '<tag>' . htmlspecialchars($tag, ENT_COMPAT, 'UTF-8') . '</tag>' . "\n";
			$text.= "\t" . '<name>' . htmlspecialchars($data['name'], ENT_COMPAT, 'UTF-8') . '</name>' . "\n";
			$text.= "\t" . '<description>' . htmlspecialchars($data['description'], ENT_COMPAT, 'UTF-8') . '</description>' . "\n";
			$text.= "\t" . '<version>' . htmlspecialchars($data['version'], ENT_COMPAT, 'UTF-8') . '</version>' . "\n";
			$text.= "\t" . '<creationDate>' . htmlspecialchars($data['creationDate'], ENT_COMPAT, 'UTF-8') . '</creationDate>' . "\n";
			$text.= "\t" . '<author>' . htmlspecialchars($data['author'], ENT_COMPAT, 'UTF-8') . '</author>' . "\n";
			$text.= "\t" . '<authorEmail>' . htmlspecialchars($data['authorEmail'], ENT_COMPAT, 'UTF-8') . '</authorEmail>' . "\n";
			$text.= "\t" . '<authorUrl>' . htmlspecialchars($data['authorUrl'], ENT_COMPAT, 'UTF-8') . '</authorUrl>' . "\n";
			$text.= "\t" . '<copyright>' . htmlspecialchars($data['joomlacopyright'], ENT_COMPAT, 'UTF-8') . '</copyright>' . "\n";

			$data['copyright'] = explode("\n", $data['copyright']);

			foreach ($data['copyright'] as $copyright) 
			{
				if($copyright)
				{
					$text.= "\t" . '<copyright>' . htmlspecialchars($copyright, ENT_COMPAT, 'UTF-8') . '</copyright>' . "\n";
				}
			}

			$text.= "\t" . '<license>' . htmlspecialchars($data['license'], ENT_COMPAT, 'UTF-8') . '</license>' . "\n";

			if ($tag == 'en-GB') 
			{
				$text.= "\t" . '<files>' . "\n";
				$xml  = JFactory::getXML($path);

				foreach ($xml->files->children() as $file) 
				{
					$text.= "\t\t" . '<filename>' . $file . '</filename>' . "\n";
				}

				$text.= "\t" . '</files>' . "\n";
			}
			else
			{
				$text.= "\t" . '<files>' . "\n";
				$xml = JFactory::getXML(constant('LOCALISEPATH_' . strtoupper($client)) . "/language/en-GB/en-GB.xml");

				foreach ($xml->files->children() as $file) 
				{
					$text.= "\t\t" . '<filename>' . str_replace('en-GB', $tag, $file) . '</filename>' . "\n";
				}

				$text.= "\t" . '</files>' . "\n";
			}

			$text.= "\t" . '<metadata>' . "\n";
			$text.= "\t\t" . '<name>' . htmlspecialchars($data['name'], ENT_COMPAT, 'UTF-8') . '</name>' . "\n";
			$text.= "\t\t" . '<tag>' . htmlspecialchars($data['tag'], ENT_COMPAT, 'UTF-8') . '</tag>' . "\n";
			$text.= "\t\t" . '<rtl>' . htmlspecialchars($data['rtl'], ENT_COMPAT, 'UTF-8') . '</rtl>' . "\n";
			$text.= "\t\t" . '<locale>' . htmlspecialchars($data['locale'], ENT_COMPAT, 'UTF-8') . '</locale>' . "\n";
			$text.= "\t\t" . '<firstDay>' . htmlspecialchars($data['firstDay'], ENT_COMPAT, 'UTF-8') . '</firstDay>' . "\n";
    		$text.= "\t" . '</metadata>' . "\n";
			$text.= "\t" . '<params />' . "\n";
			$text.= '</metafile>' . "\n";

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
			else if (!$return) 
			{
				$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGE_FILESAVE', $path));
				return false;
			}

			$id = LocaliseHelper::getFileId($path);
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
			$this->setError(JText::sprintf('COM_LOCALISE_CANNOT_REMOVE_DEFAULT_LANGUAGE', $folder));
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
			$this->setError(JText::sprintf('COM_LOCALISE_ERROR_LANGUAGES_REMOVE', "$path/$folder"));
			return false;
		}

		return true;
	}
} 