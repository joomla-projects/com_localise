<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author    Mohammad Hasani Eghtedar <m.h.eghtedar@gmail.com>
# copyright Copyright (C) 2012 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Localise Helper class
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');
abstract class LocaliseHelper
{
  static $origins;
  static $packages;
  static public function addSubmenu($vName) 
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
    /*JHtmlSidebar::addEntry(
      JText::_('COM_LOCALISE_SUBMENU_PACKAGES'),
      'index.php?option=com_localise&view=packages',
      $vName == 'packages'
    );*/
  }
  static public function isWritable($path) 
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

	// Check that instalation path exists or not
	static public function hasInstallation() 
	{
		return JFolder::exists(LOCALISEPATH_INSTALLATION);
	}

  static public function getPackages() 
  {
    if (empty(self::$package)) 
    {
      self::scanPackages();
    }
    return self::$packages;
  }
  static protected function scanPackages() 
  {
    self::$origins = array('site' => null, 'administrator' => null, 'installation' => null);
    $model = JModelLegacy::getInstance('Packages', 'LocaliseModel', array('ignore_request' => true));
    $model->setState('list.start', 0);
    $model->setState('list.limit', 0);
    $packages = $model->getItems();
    self::$packages = array();
    foreach ($packages as $package) 
    {
      self::$packages[$package->name] = $package;
      foreach ($package->administrator as $file) 
      {
        self::$origins['administrator'][$file] = $package->name;
      }
      foreach ($package->site as $file) 
      {
        self::$origins['site'][$file] = $package->name;
      }
      foreach ($package->installation as $file) 
      {
        self::$origins['installation'][$file] = $package->name;
      }
    }
  }
  static public function getOrigin($filename, $client) 
  {
    if ($filename == 'override') 
    {
      return '_override';
    }
    if (!isset(self::$origins)) 
    {
      self::scanPackages();
    }
    if (isset(self::$origins[$client][$filename])) 
    {
      return self::$origins[$client][$filename];
    }
    else
    {
      return '_thirdparty';
    }
  }
  static public function getScans($client = '', $type = '') 
  {
    $params = JComponentHelper::getParams('com_localise');
    $suffixes = explode(',',$params->get('suffixes', '.sys,.menu'));

    $filter_type = $type ? $type : '.';
    $filter_client = $client ? $client : '.';
    $scans = array();
    if (preg_match("/$filter_client/", 'installation')) 
    {
      // Scan installation folders
      
    }
    if (preg_match("/$filter_client/", 'administrator')) 
    {
      // Scan administrator folders
      if (preg_match("/$filter_type/", 'component')) 
      {
        // Scan administrator components folders
        $scans[] = array('prefix' => '', 'suffix' => '', 'type' => 'component', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/components/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => '', 'suffix' => $suffix, 'type' => 'component', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/components/', 'folder' => '');
        }
      }
      if (preg_match("/$filter_type/", 'module')) 
      {
        // Scan administrator modules folders
        $scans[] = array('prefix' => '', 'suffix' => '', 'type' => 'module', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/modules/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => '', 'suffix' => $suffix, 'type' => 'module', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/modules/', 'folder' => '');
        }
      }
      if (preg_match("/$filter_type/", 'template')) 
      {
        // Scan administrator templates folders
        $scans[] = array('prefix' => 'tpl_', 'suffix' => '', 'type' => 'template', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/templates/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => 'tpl_', 'suffix' => $suffix, 'type' => 'template', 'client' => 'administrator', 'path' => LOCALISEPATH_ADMINISTRATOR . '/templates/', 'folder' => '');
        }
      }
    }
    if (preg_match("/$filter_client/", 'site')) 
    {
      // Scan site folders
      if (preg_match("/$filter_type/", 'component')) 
      {
        // Scan site components folders
        $scans[] = array('prefix' => '', 'suffix' => '', 'type' => 'component', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/components/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => '', 'suffix' => $suffix, 'type' => 'component', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/components/', 'folder' => '');
        }
      }
      if (preg_match("/$filter_type/", 'module')) 
      {
        // Scan site modules folders
        $scans[] = array('prefix' => '', 'suffix' => '', 'type' => 'module', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/modules/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => '', 'suffix' => $suffix, 'type' => 'module', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/modules/', 'folder' => '');
        }
      }
      if (preg_match("/$filter_type/", 'template')) 
      {
        // Scan site templates folders
        $scans[] = array('prefix' => 'tpl_', 'suffix' => '', 'type' => 'template', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/templates/', 'folder' => '');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => 'tpl_', 'suffix' => $suffix, 'type' => 'template', 'client' => 'site', 'path' => LOCALISEPATH_SITE . '/templates/', 'folder' => '');
        }
      }
    }
    if (preg_match("/$filter_type/", 'plugin')) 
    {
      // Scan plugins folders
      $plugin_types = JFolder::folders(JPATH_PLUGINS);
      foreach ($plugin_types as $plugin_type) 
      {
        // Scan plugins site folders
        // $scans[] = array('prefix' => 'plg_' . $plugin_type . '_', 'suffix' => '', 'type' => 'plugin', 'client' => 'site', 'path' => JPATH_ROOT . "/plugins/$plugin_type/", 'folder' => '');

        // Scan plugins administrator folders
        $scans[] = array('prefix' => 'plg_' . $plugin_type . '_', 'suffix' => '', 'type' => 'plugin', 'client' => 'administrator', 'path' => JPATH_PLUGINS . "/$plugin_type/", 'folder' => '/administrator');
        foreach($suffixes as $suffix) {
          $scans[] = array('prefix' => 'plg_' . $plugin_type . '_', 'suffix' => $suffix, 'type' => 'plugin', 'client' => 'administrator', 'path' => JPATH_PLUGINS . "/$plugin_type/", 'folder' => '/administrator');
        }
      }
    }
    return $scans;
  }

	// Get file id in database with file path
	public static function getFileId($path) 
	{
		static $fileIds = null;

		if (!isset($fileIds)) 
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('id, path');
			$query->from($db->quoteName('#__localise'));

			$db->setQuery($query);
			$fileIds = $db->loadObjectList('path');
		}

		jimport('joomla.filesystem.file');

		if (JFile::exists($path) || preg_match('/.ini$/', $path)) 
		{
			if (!array_key_exists($path, $fileIds)) 
			{
				JTable::addIncludePath(JPATH_COMPONENT . '/tables');
				$table = JTable::getInstance('Localise', 'LocaliseTable');
				$table->path = $path;
				$table->store();
				$fileIds[$path] = new JObject(array('id' => $table->id));
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
   * Gets a list of the actions that can be performed.
   *
   * @param int The file ID.
   *
   * @return JObject
   */
  public static function getActions($fileId = 0) 
  {
    $user = JFactory::getUser();
    $result = new JObject;
    if (empty($fileId)) 
    {
      $assetName = 'com_localise';
    }
    else
    {
      $assetName = 'com_localise.' . (int)$fileId;
    }
    $actions = array('core.admin', 'core.manage', 'localise.create', 'localise.edit', 'localise.delete');
    foreach ($actions as $action) 
    {
      $result->set($action, $user->authorise($action, $assetName));
    }
    return $result;
  }

  /**
   * Find a translation file
   */
  public static function findTranslationPath($client, $tag, $filename) 
  {
    $path = self::getTranslationPath($client, $tag, $filename, 'local');
    if (!JFile::exists($path)) 
    {
      $path = self::getTranslationPath($client, $tag, $filename, 'global');;
    }
    return $path;
  }

  /**
   * Get a translation path
   */
  public static function getTranslationPath($client, $tag, $filename, $storage) 
  {
    $path = null;
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
      $parts = explode('.', $filename);
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
          $parts = explode('_', $extension);
          $group = $parts[1];
          $plugin = substr($filename, 5 + strlen($group));
          $path = JPATH_PLUGINS . "/$group/$plugin/language/$tag/$tag.$filename.ini";
        break;
        case 'tpl':
          $template = substr($extension, 4);
          $path = constant('LOCALISEPATH_' . strtoupper($client)) . "/templates/$template/language/$tag/$tag.$filename.ini";
        break;
        case 'lib':
          $path = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$tag/$tag.$filename.ini";
          if (!JFile::exists($path))
            $path = $client == 'administrator' ? LOCALISEPATH_SITE : LOCALISE_ADMINISTRATOR . "/language/$tag/$tag.$filename.ini";
        break;
      }
    }
    return $path;
  }

  /**
   * Load a language file for translating the package name
   */
  public static function loadLanguage($extension, $client = null) 
  {
    $extension = strtolower($extension);
    $lang = JFactory::getLanguage();
    $prefix = substr($extension, 0, 3);
    switch ($prefix) 
    {
    case 'com':
      $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/components/$extension/", null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), $lang->getDefault(), false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/components/$extension/", $lang->getDefault(), false, false);
    break;
    case 'mod':
      $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/modules/$extension/", null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), $lang->getDefault(), false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/modules/$extension/", $lang->getDefault(), false, false);
    break;
    case 'plg':
      $lang->load($extension, LOCALISEPATH_ADMINISTRATOR, null, false, false) || $lang->load($extension, LOCALISEPATH_ADMINISTRATOR . "/components/$extension/", null, false, false) || $lang->load($extension, LOCALISEPATH_ADMINISTRATOR, $lang->getDefault(), false, false) || $lang->load($extension, LOCALISEPATH_ADMINISTRATOR . "/components/$extension/", $lang->getDefault(), false, false);
    break;
    case 'tpl':
      $template = substr($extension, 4);
      $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/templates/$template/", null, false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)), $lang->getDefault(), false, false) || $lang->load($extension, constant('LOCALISEPATH_' . strtoupper($client)) . "/templates/$template/", $lang->getDefault(), false, false);
    break;
    case 'lib':
    case 'fil':
    case 'pkg':
      $lang->load($extension, JPATH_ROOT, null, false, false) || $lang->load($extension, JPATH_ROOT, $lang->getDefault(), false, false);
    break;
    }
  }
  public static function parseSections($filename) 
  {
    static $sections;
    if (!isset($sections)) 
    {
      $sections = array();
    }
    if (!array_key_exists($filename, $sections)) 
    {
      if (file_exists($filename)) 
      {
        $error = '';
        if (!defined('_QQ_')) define('_QQ_', '"');
        ini_set('track_errors', '1');
        $version = phpversion();
        if ($version >= "5.3.1") 
        {
          $contents = file_get_contents($filename);
          $contents = str_replace('_QQ_', '"\""', $contents);
          $strings = @parse_ini_string($contents, true);
          if (!empty($php_errormsg)) 
          {
            $error = "Error parsing " . basename($filename) . ": $php_errormsg";
          }
        }
        else
        {
          $strings = @parse_ini_file($filename, true);
          if (!empty($php_errormsg)) 
          {
            $error = $php_errormsg;
          }
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
          if ($version == "5.3.0") 
          {
            foreach ($strings as $section => $value) 
            {
              foreach ($value as $key => $string) 
              {
                $strings[$section][$key] = str_replace('_QQ_', '"', $string);
              }
            }
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
