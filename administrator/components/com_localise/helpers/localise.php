<?php
/**
 * @package     Localise
 *
 * @copyright   Copyright (C) 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

/**
 * Localise Helper class
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       4.0
 */
abstract class LocaliseHelper
{
	/**
	 * Array containing the origin information
	 *
	 * @var    array
	 * @since  4.0
	 */
	protected static $origins = array('site' => null, 'administrator' => null, 'installation' => null);

	/**
	 * Array containing the package information
	 *
	 * @var    array
	 * @since  4.0
	 */
	protected static $packages = array();

	/**
	 * Prepares the component submenu
	 *
	 * @param   string  $vName  Name of the active view
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_LOCALISE_SUBMENU_LANGUAGES'),
			'index.php?option=com_localise&view=languages',
			$vName == 'languages'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_LOCALISE_SUBMENU_TRANSLATIONS'),
			'index.php?option=com_localise&view=translations',
			$vName == 'translations'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_LOCALISE_SUBMENU_PACKAGES'),
			'index.php?option=com_localise&view=packages',
			$vName == 'packages'
		);
	}

	/**
	 * Determines if a given path is writable in the current environment
	 *
	 * @param   string  $path  Path to check
	 *
	 * @return  boolean  True if writable
	 *
	 * @since   4.0
	 */
	public static function isWritable($path)
	{
		if (JFactory::getConfig()->get('config.ftp_enable'))
		{
			return true;
		}
		else
		{
			while (!file_exists($path))
			{
				$path = dirname($path);
			}

			return is_writable($path) || JPath::isOwner($path) || JPath::canChmod($path);
		}
	}

	/**
	 * Check if the installation path exists
	 *
	 * @return  boolean  True if the installation path exists
	 *
	 * @since   4.0
	 */
	public static function hasInstallation()
	{
		return is_dir(LOCALISEPATH_INSTALLATION);
	}

	/**
	 * Retrieve the packages array
	 *
	 * @return  array
	 *
	 * @since   4.0
	 */
	public static function getPackages()
	{
		if (empty(static::$packages))
		{
			static::scanPackages();
		}

		return static::$packages;
	}

	/**
	 * Scans the filesystem for language files in each package
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected static function scanPackages()
	{
		$model         = JModelLegacy::getInstance('Packages', 'LocaliseModel', array('ignore_request' => true));
		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);
		$packages       = $model->getItems();

		foreach ($packages as $package)
		{
			static::$packages[$package->name] = $package;

			foreach ($package->administrator as $file)
			{
				static::$origins['administrator'][$file] = $package->name;
			}

			foreach ($package->site as $file)
			{
				static::$origins['site'][$file] = $package->name;
			}

			foreach ($package->installation as $file)
			{
				static::$origins['installation'][$file] = $package->name;
			}
		}
	}

	/**
	 * Retrieves the origin information
	 *
	 * @param   string  $filename  The filename to check
	 * @param   string  $client    The client to check
	 *
	 * @return  string  Origin data
	 *
	 * @since   4.0
	 */
	public static function getOrigin($filename, $client)
	{
		if ($filename == 'override')
		{
			return '_override';
		}

		// If the $origins array doesn't contain data, fill it
		if (empty(static::$origins['site']))
		{
			static::scanPackages();
		}

		if (isset(static::$origins[$client][$filename]))
		{
			return static::$origins[$client][$filename];
		}
		else
		{
			return '_thirdparty';
		}
	}

	/**
	 * Scans the filesystem
	 *
	 * @param   string  $client  The client to scan
	 * @param   string  $type    The extension type to scan
	 *
	 * @return  array
	 *
	 * @since   4.0
	 */
	public static function getScans($client = '', $type = '')
	{
		$params   = JComponentHelper::getParams('com_localise');
		$suffixes = explode(',', $params->get('suffixes', '.sys,.menu'));

		$filter_type   = $type ? $type : '.';
		$filter_client = $client ? $client : '.';
		$scans         = array();

		// Scan installation folders
		if (preg_match("/$filter_client/", 'installation'))
		{
			// TODO ;-)
		}

		// Scan administrator folders
		if (preg_match("/$filter_client/", 'administrator'))
		{
			// Scan administrator components folders
			if (preg_match("/$filter_type/", 'component'))
			{
				$scans[] = array(
					'prefix' => '',
					'suffix' => '',
					'type'   => 'component',
					'client' => 'administrator',
					'path'   => LOCALISEPATH_ADMINISTRATOR . '/components/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => '',
						'suffix' => $suffix,
						'type'   => 'component',
						'client' => 'administrator',
						'path'   => LOCALISEPATH_ADMINISTRATOR . '/components/',
						'folder' => ''
					);
				}
			}

			// Scan administrator modules folders
			if (preg_match("/$filter_type/", 'module'))
			{
				$scans[] = array(
					'prefix' => '',
					'suffix' => '',
					'type'   => 'module',
					'client' => 'administrator',
					'path'   => LOCALISEPATH_ADMINISTRATOR . '/modules/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => '',
						'suffix' => $suffix,
						'type'   => 'module',
						'client' => 'administrator',
						'path'   => LOCALISEPATH_ADMINISTRATOR . '/modules/',
						'folder' => ''
					);
				}
			}

			// Scan administrator templates folders
			if (preg_match("/$filter_type/", 'template'))
			{
				$scans[] = array(
					'prefix' => 'tpl_',
					'suffix' => '',
					'type'   => 'template',
					'client' => 'administrator',
					'path'   => LOCALISEPATH_ADMINISTRATOR . '/templates/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => 'tpl_',
						'suffix' => $suffix,
						'type'   => 'template',
						'client' => 'administrator',
						'path'   => LOCALISEPATH_ADMINISTRATOR . '/templates/',
						'folder' => ''
					);
				}
			}
		}

		// Scan site folders
		if (preg_match("/$filter_client/", 'site'))
		{
			// Scan site components folders
			if (preg_match("/$filter_type/", 'component'))
			{
				$scans[] = array(
					'prefix' => '',
					'suffix' => '',
					'type'   => 'component',
					'client' => 'site',
					'path'   => LOCALISEPATH_SITE . '/components/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => '',
						'suffix' => $suffix,
						'type'   => 'component',
						'client' => 'site',
						'path'   => LOCALISEPATH_SITE . '/components/',
						'folder' => ''
					);
				}
			}

			// Scan site modules folders
			if (preg_match("/$filter_type/", 'module'))
			{
				$scans[] = array(
					'prefix' => '',
					'suffix' => '',
					'type'   => 'module',
					'client' => 'site',
					'path'   => LOCALISEPATH_SITE . '/modules/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => '',
						'suffix' => $suffix,
						'type'   => 'module',
						'client' => 'site',
						'path'   => LOCALISEPATH_SITE . '/modules/',
						'folder' => ''
					);
				}
			}

			// Scan site templates folders
			if (preg_match("/$filter_type/", 'template'))
			{
				$scans[] = array(
					'prefix' => 'tpl_',
					'suffix' => '',
					'type'   => 'template',
					'client' => 'site',
					'path'   => LOCALISEPATH_SITE . '/templates/',
					'folder' => ''
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => 'tpl_',
						'suffix' => $suffix,
						'type'   => 'template',
						'client' => 'site',
						'path'   => LOCALISEPATH_SITE . '/templates/',
						'folder' => ''
					);
				}
			}
		}

		// Scan plugins folders
		if (preg_match("/$filter_type/", 'plugin'))
		{
			$plugin_types = JFolder::folders(JPATH_PLUGINS);

			foreach ($plugin_types as $plugin_type)
			{
				// Scan administrator language folders as this is where plugin languages are installed
				$scans[] = array(
					'prefix' => 'plg_' . $plugin_type . '_',
					'suffix' => '',
					'type'   => 'plugin',
					'client' => 'administrator',
					'path'   => JPATH_PLUGINS . "/$plugin_type/",
					'folder' => '/administrator'
				);

				foreach ($suffixes as $suffix)
				{
					$scans[] = array(
						'prefix' => 'plg_' . $plugin_type . '_',
						'suffix' => $suffix,
						'type'   => 'plugin',
						'client' => 'administrator',
						'path'   => JPATH_PLUGINS . "/$plugin_type/",
						'folder' => '/administrator'
					);
				}
			}
		}

		return $scans;
	}

	/**
	 * Get file ID in the database for the given file path
	 *
	 * @param   string  $path  Path to lookup
	 *
	 * @return  integer  File ID
	 *
	 * @since   4.0
	 */
	public static function getFileId($path)
	{
		static $fileIds = null;

		if (!isset($fileIds))
		{
			$db = JFactory::getDbo();

			$db->setQuery(
				$db->getQuery(true)
					->select($db->quoteName(array('id, path')))
					->from($db->quoteName('#__localise'))
			);

			$fileIds = $db->loadObjectList('path');
		}

		if (is_file($path) || preg_match('/.ini$/', $path))
		{
			if (!array_key_exists($path, $fileIds))
			{
				JTable::addIncludePath(JPATH_COMPONENT . '/tables');

				/* @type  LocaliseTableLocalise  $table */
				$table       = JTable::getInstance('Localise', 'LocaliseTable');
				$table->path = $path;
				$table->store();

				$fileIds[$path] = new stdClass;
				$fileIds[$path]->id = $table->id;
			}

			return $fileIds[$path]->id;
		}
		else
		{
			$id = 0;
		}

		return $id;
	}

	/**
	 * Find a translation file
	 *
	 * @param   string  $client    Client to lookup
	 * @param   string  $tag       Language tag to lookup
	 * @param   string  $filename  Filename to lookup
	 *
	 * @return  string  Path to the requrested file
	 *
	 * @since   4.0
	 */
	public static function findTranslationPath($client, $tag, $filename)
	{
		$path = static::getTranslationPath($client, $tag, $filename, 'local');

		if (!is_file($path))
		{
			$path = static::getTranslationPath($client, $tag, $filename, 'global');
		}

		return $path;
	}

	/**
	 * Get a translation path
	 *
	 * @param   string  $client    Client to lookup
	 * @param   string  $tag       Language tag to lookup
	 * @param   string  $filename  Filename to lookup
	 * @param   string  $storage   Storage location to check
	 *
	 * @return  string  Path to the requrested file
	 *
	 * @since   4.0
	 */
	public static function getTranslationPath($client, $tag, $filename, $storage)
	{
		if ($filename == 'override')
		{
			$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/overrides/$tag.override.ini";
		}
		elseif ($filename == 'joomla')
		{
			$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.ini";
		}
		elseif ($storage == 'global')
		{
			$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$filename.ini";
		}
		else
		{
			$parts     = explode('.', $filename);
			$extension = $parts[0];

			switch (substr($extension, 0, 3))
			{
				case 'com':
					$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/components/$extension/language/$tag/$tag.$filename.ini";

					break;

				case 'mod':
					$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/modules/$extension/language/$tag/$tag.$filename.ini";

					break;

				case 'plg':
					$parts  = explode('_', $extension);
					$group  = $parts[1];
					$plugin = substr($filename, 5 + strlen($group));
					$path   = JPATH_PLUGINS . "/$group/$plugin/language/$tag/$tag.$filename.ini";

					break;

				case 'tpl':
					$template = substr($extension, 4);
					$path     = constant('LOCALISEPATH_' . strtoupper($client)) . "/templates/$template/language/$tag/$tag.$filename.ini";

					break;

				case 'lib':
					$path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$filename.ini";

					if (!is_file($path))
					{
						$path = $client == 'administrator' ? LOCALISEPATH_SITE : LOCALISE_ADMINISTRATOR . "/language/$tag/$tag.$filename.ini";
					}

					break;

				default   :
					$path = '';

					break;
			}
		}

		return $path;
	}

	/**
	 * Load a language file for translating the package name
	 *
	 * @param   string  $extension  The extension to load
	 * @param   string  $client     The client from where to load the file
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	public static function loadLanguage($extension, $client)
	{
		$extension = strtolower($extension);
		$lang      = JFactory::getLanguage();
		$prefix    = substr($extension, 0, 3);

		switch ($prefix)
		{
			case 'com':
				$lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, true)
					|| $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/components/$extension/", null, false, true);

				break;

			case 'mod':
				$lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, true)
					|| $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/modules/$extension/", null, false, true);

				break;

			case 'plg':
				$lang->load($extension, LOCALISEPATH_ADMINISTRATOR, null, false, true)
					|| $lang->load($extension, LOCALISEPATH_ADMINISTRATOR . "/components/$extension/", null, false, true);

				break;

			case 'tpl':
				$template = substr($extension, 4);
				$lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, true)
					|| $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/templates/$template/", null, false, true);

				break;

			case 'lib':
			case 'fil':
			case 'pkg':
				$lang->load($extension, JPATH_ROOT, null, false, true);

				break;
		}
	}

	/**
	 * Parses the sections of a language file
	 *
	 * @param   string  $filename  The filename to parse
	 *
	 * @return  array  Array containing the file data
	 *
	 * @since   4.0
	 */
	public static function parseSections($filename)
	{
		static $sections = array();

		if (!array_key_exists($filename, $sections))
		{
			if (file_exists($filename))
			{
				$error = '';

				if (!defined('_QQ_'))
				{
					define('_QQ_', '"');
				}

				ini_set('track_errors', '1');
				$version = phpversion();

				$contents = file_get_contents($filename);
				$contents = str_replace('_QQ_', '"\""', $contents);
				$strings  = @parse_ini_string($contents, true);

				if (!empty($php_errormsg))
				{
					$error = "Error parsing " . basename($filename) . ": $php_errormsg";
				}

				ini_restore('track_errors');

				if ($strings !== false)
				{
					$default = array();

					foreach ($strings as $key => $value)
					{
						if (is_string($value))
						{
							$default[$key] = $value;

							unset($strings[$key]);
						}
						else
						{
							break;
						}
					}

					if (!empty($default))
					{
						$strings = array_merge(array('Default' => $default), $strings);
					}

					$keys = array();

					foreach ($strings as $section => $value)
					{
						foreach ($value as $key => $string)
						{
							$keys[$key] = $strings[$section][$key];
						}
					}
				}
				else
				{
					$keys = false;
				}

				$sections[$filename] = array('sections' => $strings, 'keys' => $keys, 'error' => $error);
			}
			else
			{
				$sections[$filename] = array('sections' => array(), 'keys' => array(), 'error' => '');
			}
		}

		if (!empty($sections[$filename]['error']))
		{
			$model = JModelLegacy::getInstance('Translation', 'LocaliseModel');
			$model->setError($sections[$filename]['error']);
		}

		return $sections[$filename];
	}
}
