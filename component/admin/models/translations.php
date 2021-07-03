<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
use Joomla\Utilities\ArrayHelper;

/**
 * Translations Model class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseModelTranslations extends JModelList
{
	protected $context = 'com_localise.translations';

	protected $translations;

	protected $items;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   3.5
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'filename',
				'completed',
				'translated',
			);
		}

		parent::__construct($config);
	}

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
			$data['select'] = $app->getUserState('com_localise.select');
		}
		else
		{
			$app->setUserState('com_localise.select', $data['select']);
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$search = JFilterInput::getInstance()->clean($search, 'TRIM');
		$search = strtolower($search);

		if ($search)
		{
			$app->setUserState('filter.search', strtolower($search));
		}
		else
		{
			$app->setUserState('filter.search', '');
		}

		$this->setState(
			'filter.storage',
			isset($data['select']['storage']) ? $data['select']['storage'] : ''
		);
		$this->setState(
			'filter.origin',
			isset($data['select']['origin'])  ? $data['select']['origin'] : ''
		);
		$this->setState(
			'filter.state',
			isset($data['select']['state'])   ? $data['select']['state'] : ''
		);
		$this->setState(
			'filter.type',
			isset($data['select']['type'])    ? $data['select']['type'] : ''
		);
		$this->setState(
			'filter.client',
			isset($data['select']['client'])  ? $data['select']['client'] : ''
		);
		$this->setState(
			'filter.tag',
			isset($data['select']['tag'])     ? $data['select']['tag'] :''
		);
		$this->setState(
			'filter.develop',
			isset($data['select']['develop']) ? $data['select']['develop'] :''
		);

		$params    = JComponentHelper::getParams('com_localise');
		$this->setState('params', $params);

		$reference = $params->get('reference', 'en-GB');

		$this->setState('translations.reference', $reference);

		// Call auto-populate parent method
		parent::populateState($ordering, $direction);
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
		$form = JForm::getInstance('com_localise.translations', 'translations', array('control' => 'filters', 'event' => 'onPrepareForm'));

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
		$data = $app->getUserState('com_localise.translations.filter.search', array());

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind(array('search' => $data));
		}

		return $form;
	}

	/**
	 * todo: missing description
	 *
	 * @return void
	 */
	private function scanLocalTranslationsFolders()
	{
		$app = JFactory::getApplication();

		$filter_storage = $this->getState('filter.storage');
		$filter_origin  = $this->getState('filter.origin') ? $this->getState('filter.origin') : '.';
		$reftag         = $this->getState('translations.reference');

		if ($filter_storage != 'global')
		{
			$filter_tag    = $this->getState('filter.tag') ? ("^($reftag|" . $this->getState('filter.tag') . ")$") : '.';
			$filter_search = $app->getUserState('filter.search') ? $app->getUserState('filter.search') : '.';
			$scans         = LocaliseHelper::getScans($this->getState('filter.client'), $this->getState('filter.type'));

			foreach ($scans as $scan)
			{
				// For all scans
				$prefix = $scan['prefix'];
				$suffix = $scan['suffix'];
				$type   = $scan['type'];
				$client = $scan['client'];
				$path   = $scan['path'];
				$folder = $scan['folder'];

				$extensions = JFolder::folders($path, $filter_search);

				foreach ($extensions as $extension)
				{
					if (JFolder::exists("$path$extension/language"))
					{
						// Scan extensions folder
						$tags = JFolder::folders("$path$extension/language", $filter_tag);

						foreach ($tags as $tag)
						{
							$file   = "$path$extension/language/$tag/$tag.$prefix$extension$suffix.ini";
							$origin = LocaliseHelper::getOrigin("$prefix$extension$suffix", $client);

							if (JFile::exists($file) && preg_match("/$filter_origin/", $origin))
							{
								$translation = new JObject(
									array(
										'type' => $type,
										'tag' => $tag,
										'client' => $client,
										'storage' => 'local',
										'filename' => "$prefix$extension$suffix",
										'name' => "$prefix$extension$suffix",
										'refpath' => null,
										'path' => $file,
										'state' => $tag == $reftag ? 'inlanguage' : 'notinreference',
										'writable' => LocaliseHelper::isWritable($file),
										'origin' => $origin
									)
								);
								$this->translations["$client|$tag|$prefix$extension$suffix"] = $translation;
							}
						}
					}
				}
			}
		}
	}

	/**
	 * todo: missing function description
	 *
	 * @return void
	 */
	private function scanGlobalTranslationsFolders()
	{
		$app            = JFactory::getApplication();

		$filter_storage = $this->getState('filter.storage');
		$reftag         = $this->getState('translations.reference');

		if ($filter_storage != 'local')
		{
			// Scan global folder
			$filter_client = $this->getState('filter.client');
			$filter_tag    = $this->getState('filter.tag')    ? ("^($reftag|" . $this->getState('filter.tag') . ")$") : '.';
			$filter_type   = $this->getState('filter.type')   ? $this->getState('filter.type')   : '.';
			$filter_search = $app->getUserState('filter.search') ? $app->getUserState('filter.search') : '.';
			$filter_origin = $this->getState('filter.origin') ? $this->getState('filter.origin') : '.';

			if (empty($filter_client))
			{
				$clients = array('site', 'administrator', 'installation');
			}
			else
			{
				$clients = array($filter_client);
			}

			foreach ($clients as $client)
			{
				// For all selected clients
				$path = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

				if (JFolder::exists($path))
				{
					$tags = JFolder::folders($path, $filter_tag, false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

					foreach ($tags as $tag)
					{
						if (JFile::exists($path . '/' . $tag . '/' . $tag . '.xml'))
						{
							// For all selected tags
							$files = JFolder::files("$path/$tag", "$filter_search.*\.ini$");

							foreach ($files as $file)
							{
								$filename = substr($file, 1 + strlen($tag));

								if ($filename == 'ini')
								{
									$filename = '';
								}
								else
								{
									$filename = substr($filename, 0, strlen($filename) - 4);
								}

								$origin = LocaliseHelper::getOrigin($filename, $client);

								if (preg_match("/$filter_origin/", $origin))
								{
									$prefix = substr($file, 0, 4 + strlen($tag));

									$translation = new JObject(
										array(
											'tag' => $tag,
											'client' => $client,
											'storage' => 'global',
											'refpath' => null,
											'path' => "$path/$tag/$file",
											'state' => $tag == $reftag ? 'inlanguage' : 'notinreference',
											'writable' => LocaliseHelper::isWritable("$path/$tag/$file"),
											'origin' => $origin
										)
									);

									if ($file == "$tag.ini" && preg_match("/$filter_type/", 'joomla'))
									{
										// Scan joomla ini file
										$translation->setProperties(array('type' => 'joomla', 'filename' => 'joomla', 'name' => JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_JOOMLA')));
										$this->translations["$client|$tag|joomla"] = $translation;
									}
									elseif ($file == "$tag.finder_cli.ini" && preg_match("/$filter_type/", 'file'))
									{
										$translation->setProperties(array('type' => 'file', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($file == "$tag.files_joomla.sys.ini" && preg_match("/$filter_type/", 'file'))
									{
										$translation->setProperties(array('type' => 'file', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.com" && preg_match("/$filter_type/", 'component'))
									{
										// Scan component ini file
										$translation->setProperties(array('type' => 'component', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.mod" && preg_match("/$filter_type/", 'module'))
									{
										// Scan module ini file
										$translation->setProperties(array('type' => 'module', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.tpl" && preg_match("/$filter_type/", 'template'))
									{
										// Scan template ini file
										$translation->setProperties(array('type' => 'template', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.plg" && preg_match("/$filter_type/", 'plugin'))
									{
										// Scan plugin ini file
										$translation->setProperties(array('type' => 'plugin', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.pkg" && preg_match("/$filter_type/", 'package'))
									{
										// Scan package ini file
										$translation->setProperties(array('type' => 'package', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
									elseif ($prefix == "$tag.lib" && preg_match("/$filter_type/", 'library'))
									{
										// Scan library ini file
										$translation->setProperties(array('type' => 'library', 'filename' => $filename, 'name' => $filename));
										$this->translations["$client|$tag|$filename"] = $translation;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * todo: missing function description
	 *
	 * @return void
	 */
	private function scanReference()
	{
		$app            = JFactory::getApplication();

		$reftag         = $this->getState('translations.reference');
		$filter_tag     = $this->getState('filter.tag')    ? ("^($reftag|" . $this->getState('filter.tag') . ")$") : '.';
		$filter_search  = $app->getUserState('filter.search') ? $app->getUserState('filter.search') : '.';
		$filter_storage = $this->getState('filter.storage');
		$filter_origin  = $this->getState('filter.origin');
		$filter_client  = $this->getState('filter.client');

		if (empty($filter_client))
		{
			$clients = array('site', 'administrator', 'installation');
		}
		else
		{
			$clients = array($filter_client);
		}

		foreach ($clients as $client)
		{
			$client_folder = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

			if (JFolder::exists($client_folder))
			{
				// Scan joomla files
				$tags = JFolder::folders($client_folder, $filter_tag, false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

				foreach ($tags as $tag)
				{
					if (JFile::exists($client_folder . '/' . $tag . '/' . $tag . '.xml'))
					{
						if (array_key_exists("$client|$reftag|joomla", $this->translations))
						{
							$reftranslation = $this->translations["$client|$reftag|joomla"];

							if (array_key_exists("$client|$tag|joomla", $this->translations))
							{
								$this->translations["$client|$tag|joomla"]->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
							}
							elseif ($filter_storage != 'local')
							{
								$origin = LocaliseHelper::getOrigin("", $client);

								$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.ini";

								$translation = new JObject(
									array(
										'type' => 'joomla',
										'tag' => $tag,
										'client' => $client,
										'storage' => 'global',
										'filename' => 'joomla',
										'name' => JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_JOOMLA'),
										'refpath' => $reftranslation->path,
										'path' => $path,
										'state' => 'unexisting',
										'writable' => LocaliseHelper::isWritable($path),
										'origin' => $origin
									)
								);
								$this->translations["$client|$tag|joomla"] = $translation;
							}
						}
					}
				}

				$files = JFolder::files("$client_folder/$reftag", "\.ini$");

				if ($files)
				{
					foreach ($files as $file)
					{
						$reftaglength = strlen($reftag);

						$name	= substr($file, 0, -4);
						$name	= substr($name, $reftaglength + 1);

						$origin	= LocaliseHelper::getOrigin($name, $client);

						foreach ($tags as $tag)
						{
							if (JFile::exists($client_folder . '/' . $tag . '/' . $tag . '.xml'))
							{
								if (array_key_exists("$client|$reftag|$name", $this->translations))
								{
									$reftranslation = $this->translations["$client|$reftag|$name"];

									if (array_key_exists("$client|$tag|$name", $this->translations))
									{
										$this->translations["$client|$tag|$name"]->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
									}
									else
									{
										$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$name.ini";
										$translation = new JObject(
											array(
												'type' => '',
												'tag' => $tag,
												'client' => $client,
												'storage' => 'global',
												'filename' => $name,
												'name' => $name,
												'refpath' => $reftranslation->path,
												'path' => $path,
												'state' => 'unexisting',
												'writable' => LocaliseHelper::isWritable($path),
												'origin' => 'core'
											)
										);
										$this->translations["$client|$tag|$name"] = $translation;
									}
								}
							}
						}

						if (preg_match("/^$reftag\.(lib.*)\.ini$/", $file, $matches))
						{
							$name   = $matches[1];
							$origin = LocaliseHelper::getOrigin($name, $client);

							foreach ($tags as $tag)
							{
								if (JFile::exists($client_folder . '/' . $tag . '/' . $tag . '.xml'))
								{
									if (array_key_exists("$client|$reftag|$name", $this->translations))
									{
										$reftranslation = $this->translations["$client|$reftag|$name"];

										if (array_key_exists("$client|$tag|$name", $this->translations))
										{
											$this->translations["$client|$tag|$name"]->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
										}
										else
										{
											$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$name.ini";
											$translation = new JObject(
												array(
													'type' => 'library',
													'tag' => $tag,
													'client' => $client,
													'storage' => 'global',
													'filename' => $name,
													'name' => $name,
													'refpath' => $reftranslation->path,
													'path' => $path,
													'state' => 'unexisting',
													'writable' => LocaliseHelper::isWritable($path),
													'origin' => '_thirdparty'
												)
											);
											$this->translations["$client|$tag|$name"] = $translation;
										}
									}
								}
							}
						}
						elseif (preg_match("/^$reftag\.(pkg.*)\.ini$/", $file, $matches))
						{
							$name   = $matches[1];
							$origin = LocaliseHelper::getOrigin($name, $client);

							foreach ($tags as $tag)
							{
								if (JFile::exists($client_folder . '/' . $tag . '/' . $tag . '.xml'))
								{
									if (array_key_exists("$client|$reftag|$name", $this->translations))
									{
										$reftranslation = $this->translations["$client|$reftag|$name"];

										if (array_key_exists("$client|$tag|$name", $this->translations))
										{
											$this->translations["$client|$tag|$name"]->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
										}
										else
										{
											$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$name.ini";
											$translation = new JObject(
												array(
													'type' => 'package',
													'tag' => $tag,
													'client' => $client,
													'storage' => 'global',
													'filename' => $name,
													'name' => $name,
													'refpath' => $reftranslation->path,
													'path' => $path,
													'state' => 'unexisting',
													'writable' => LocaliseHelper::isWritable($path),
													'origin' => '_thirdparty'
												)
											);
											$this->translations["$client|$tag|$name"] = $translation;
										}
									}
								}
							}
						}
						elseif (preg_match("/^$reftag\.(finder_cli)\.ini$/", $file, $matches)
								|| preg_match("/^$reftag\.(files_joomla.sys)\.ini$/", $file, $matches) )
						{
							$name   = $matches[1];
							$origin = LocaliseHelper::getOrigin($name, $client);

							foreach ($tags as $tag)
							{
								if (array_key_exists("$client|$reftag|$name", $this->translations))
								{
									$reftranslation = $this->translations["$client|$reftag|$name"];

									if (array_key_exists("$client|$tag|$name", $this->translations))
									{
										$this->translations["$client|$tag|$name"]->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
									}
									else
									{
										$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$name.ini";
										$translation = new JObject(
											array(
												'type' => 'file',
												'tag' => $tag,
												'client' => $client,
												'storage' => 'global',
												'filename' => $name,
												'name' => $name,
												'refpath' => $reftranslation->path,
												'path' => $path,
												'state' => 'unexisting',
												'writable' => LocaliseHelper::isWritable($path),
												'origin' => 'core'
											)
										);
										$this->translations["$client|$tag|$name"] = $translation;
									}
								}
							}
						}
					}
				}
			}
		}

		// Scan extension files
		$scans = LocaliseHelper::getScans($this->getState('filter.client'), $this->getState('filter.type'));

		foreach ($scans as $scan)
		{
			$prefix = $scan['prefix'];
			$suffix = $scan['suffix'];
			$type   = $scan['type'];
			$client = $scan['client'];
			$path   = $scan['path'];
			$folder = $scan['folder'];

			$extensions = JFolder::folders($path, $filter_search);

			foreach ($extensions as $extension)
			{
				if (array_key_exists("$client|$reftag|$prefix$extension$suffix", $this->translations))
				{
					$reftranslation = $this->translations["$client|$reftag|$prefix$extension$suffix"];
					$tags = JFolder::folders(
						constant('LOCALISEPATH_' . strtoupper($client)) . '/language',
						$filter_tag,
						false,
						false,
						array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX')
					);

					foreach ($tags as $tag)
					{
						$origin = LocaliseHelper::getOrigin("$prefix$extension$suffix", $client);

						if (JFile::exists($client_folder . '/' . $tag . '/' . $tag . '.xml'))
						{
							if (array_key_exists("$client|$tag|$prefix$extension$suffix", $this->translations))
							{
								$this
									->translations["$client|$tag|$prefix$extension$suffix"]
									->setProperties(array('refpath' => $reftranslation->path, 'state' => 'inlanguage'));
							}
							elseif ($filter_storage != 'local' && ($filter_origin == '' || $filter_origin == $origin))
							{
								$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$prefix$extension$suffix.ini";
								$translation = new JObject(
									array(
										'type' => $type,
										'tag' => $tag,
										'client' => $client,
										'storage' => 'global',
										'filename' => "$prefix$extension$suffix",
										'name' => "$prefix$extension$suffix",
										'refpath' => $reftranslation->path,
										'path' => $path, 'state' => 'unexisting',
										'writable' => LocaliseHelper::isWritable($path),
										'origin' => $origin
									)
								);

								$this->translations["$client|$tag|$prefix$extension$suffix"] = $translation;
							}
						}
					}
				}
			}
		}
	}

	/**
	 * todo: missing function description
	 *
	 * @return void
	 */
	private function scanOverride()
	{
		$app = JFactory::getApplication();

		// Scan overrides ini files
		$reftag         = $this->getState('translations.reference');
		$filter_client  = $this->getState('filter.client');
		$filter_tag     = $this->getState('filter.tag') ? ("^($reftag|" . $this->getState('filter.tag') . ")$") : '.';
		$filter_storage = $this->getState('filter.storage');
		$filter_type    = $this->getState('filter.type');
		$filter_origin  = $this->getState('filter.origin') ? $this->getState('filter.origin') : '.';
		$filter_search  = $app->getUserState('filter.search') ? $app->getUserState('filter.search') : '.';

		if ((empty($filter_client) || $filter_client != 'installation')
			&& (empty($filter_storage) || $filter_storage == 'global')
			&& (empty($filter_type) || $filter_type == 'override')
			&& preg_match("/$filter_origin/", '_override') && preg_match("/$filter_search/i", 'override'))
		{
			if (empty($filter_client))
			{
				$clients = array('site', 'administrator');
			}
			else
			{
				$clients = array($filter_client);
			}

			foreach ($clients as $client)
			{
				$tags = JFolder::folders(
									constant('LOCALISEPATH_' . strtoupper($client)) . '/language',
									$filter_tag,
									false,
									false,
									array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'pdf_fonts', 'overrides')
				);

				foreach ($tags as $tag)
				{
					$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/overrides/$tag.override.ini";
					$translation = new JObject(
						array(
							'type' => 'override',
							'tag' => $tag,
							'client' => $client,
							'storage' => 'global',
							'filename' => 'override',
							'name' => 'override',
							'refpath' => $path,
							'path' => $path,
							'state' => 'inlanguage',
							'writable' => LocaliseHelper::isWritable($path),
							'origin' => '_override'
						)
					);
					$this->translations["$client|$tag|override"] = $translation;
				}
			}
		}
	}

	/**
	 * todo: missing function description
	 *
	 * @return array
	 */
	private function getTranslations()
	{
		$app = JFactory::getApplication();

		if (!isset($this->translations))
		{
			$filter_client = $this->getState('filter.client');
			$filter_tag   = $this->getState('filter.tag');
			$filter_develop = $this->getState('filter.develop');

			// Don't try to find translations if filters not set for client and language.
			if (empty($filter_client) || empty($filter_tag))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_LOCALISE_ERROR_CHOOSE_LANG_CLIENT'), 'notice');
				$this->translations = array();

				return $this->translations;
			}

			$gh_data = array();
			$gh_data['github_client'] = $filter_client;

			$get_github_files   = LocaliseHelper::getTargetgithubfiles($gh_data);
			$get_customised_ref = LocaliseHelper::getSourceGithubfiles($gh_data);

			$filter_state = $this->getState('filter.state') ? $this->getState('filter.state') : '.';
			$filter_tag   = $filter_tag   ? ("^" . $filter_tag . "$") : '.';

			$cache_controller = JCacheController::getInstance();

			$key = 'translation-'
				. ($this->getState('filter.client')  ? $this->getState('filter.client') . '-' : '')
				. ($this->getState('filter.storage') ? $this->getState('filter.storage') . '-' : '')
				. ($this->getState('filter.tag')     ? ("^(" . $this->getState('translations.reference') . "|" . $this->getState('filter.tag') . ")$") . '-' : '')
				. ($this->getState('filter.type')    ? $this->getState('filter.type') . '-' : '')
				. ($app->getUserState('filter.search')  ? $app->getUserState('filter.search') . '-' : '')
				. ($this->getState('filter.origin')  ? $this->getState('filter.origin') . '-' : '');

			$key = substr($key, 0, strlen($key) - 1);

			$this->translations = $cache_controller->get($key, 'localise');

			if (!is_array($this->translations))
			{
				$this->translations = array();
				$this->scanLocalTranslationsFolders();
				$this->scanGlobalTranslationsFolders();
				$this->scanReference();
				$this->scanOverride();

				$cache_controller->store($this->translations, $key, 'localise');
			}

			foreach ($this->translations as $key => $translation)
			{
				$model = JModelLegacy::getInstance('Translation', 'LocaliseModel', array('ignore_request' => true));
				$model->setState('translation.id', LocaliseHelper::getFileId($translation->path));
				$model->setState('translation.path', $translation->path);
				$model->setState('translation.refpath', $translation->refpath);
				$model->setState('translation.reference', $this->getState('translations.reference'));
				$model->setState('translation.client', $translation->client);
				$model->setState('translation.tag', $translation->tag);
				$model->setState('translation.filename', $translation->filename);

				$item = $model->getItem();
				$state = count($item->error) ? 'error' : $translation->state;

				if (preg_match("/$filter_state/", $state) && preg_match("/$filter_tag/", $translation->tag))
				{
					$developdata          = $item->developdata;
					$untranslateds_amount = $item->untranslated;
					$translated_news      = $item->translatednews;
					$unchanged_news       = $item->unchangednews;
					$extras_amount        = 0;
					$unrevised_changes    = 0;
					$have_develop         = 0;

					if (!empty($developdata))
					{
						$extras_amount     = $developdata['extra_keys']['amount'];
						$unrevised_changes = $developdata['text_changes']['unrevised'];
					}

					if (($extras_amount > 0 && $extras_amount > $translated_news + $unchanged_news) || $unrevised_changes > 0 || $untranslateds_amount > 0)
					{
						$have_develop = 1;
						$item->complete = 0;
					}

					if ($filter_develop == 'complete' && $item->complete == 0)
					{
						unset($this->translations[$key]);
						continue;
					}
					elseif ($filter_develop == 'incomplete' && $item->complete)
					{
						unset($this->translations[$key]);
						continue;
					}

					if (count($item->error))
					{
						$item->state     = 'error';
						$item->completed = - count($item->error) - 1000;
					}
					elseif ($item->bom != 'UTF-8')
					{
						if ($translation->state == 'notinreference')
						{
							$item->completed = - 500;
						}
						else
						{
							$item->completed = - 400;
						}
					}
					elseif ($translation->state == 'notinreference')
					{
						$item->completed = - 600;
					}
					elseif ($translation->type == 'override')
					{
						$item->completed = 101;
					}
					elseif ($translation->tag == $this->getState('translations.reference'))
					{
						$item->completed = 102;
					}
					elseif ($translation->state == 'unexisting')
					{
						$item->completed = - ($item->total / ($item->total + 1));
					}
					elseif ($item->complete)
					{
						$item->completed = 100;
					}

					$this->translations[$key]->setProperties($item->getProperties());
				}
				else
				{
					unset($this->translations[$key]);
				}
			}

			// Process ordering.
			$listOrder = $this->getState('list.ordering', 'name');
			$listDirn  = $this->getState('list.direction', 'ASC');
			$this->translations = ArrayHelper::sortObjects($this->translations, $listOrder, strtolower($listDirn) === 'desc' ? -1 : 1, true, true);

			$this->translations = array_values($this->translations);
		}

		return $this->translations;
	}

	/**
	 * Get translations
	 *
	 * @return array|mixed
	 */
	public function getItems()
	{
		if (!isset($this->items))
		{
			$translations = $this->getTranslations();
			$count = count($translations);
			$start = $this->getState('list.start');
			$limit = $this->getState('list.limit');

			if ($start > $count)
			{
				$start = 0;
			}

			if ($limit == 0)
			{
				$start = 0;
				$limit = null;
			}

			$this->items = array_slice($translations, $start, $limit);
		}

		return $this->items;
	}

	/**
	 * Get total number of translations
	 *
	 * @return int
	 */
	public function getTotal()
	{
		return count($this->getTranslations());
	}

	/**
	 * todo: missing function description
	 *
	 * @return mixed
	 */
	public function getTotalExist()
	{
		if (!isset($this->_data->total_exist))
		{
			if (!isset($this->_data))
			{
				$this->_data = new stdClass;
			}

			$i = 0;
			$translations = $this->getTranslations();

			foreach ($translations as $translation)
			{
				if ($translation->state != 'unexisting')
				{
					$i++;
				}
			}

			$this->_data->total_exist = $i;
		}

		return $this->_data->total_exist;
	}
}
