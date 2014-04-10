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

jimport('joomla.application.component.modelitem');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Package Model class for the Localise component
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelPackage extends JModelItem
{
  public function &getFilename() 
  {
    $filename = $this->getState('filename');
    return $filename;
  }
  public static function &getCoreFiles() 
  {
    $corefiles = array();
    $corefiles['administrator'] = array();
    $path = JPATH_ADMINISTRATOR . DS . 'languages' . DS . 'en-GB';
    $files = JFolder::files($path . DS . 'language' . DS . $tag, $tag . '\.[^.]+\.ini');
    foreach ($files as $file) 
    {
      $model = JModel::getInstance('translation', 'LocaliseModel', array('ignore_request' => true));
      $model->setState('path', JPATH_ADMINISTRATOR . DS . 'languages' . DS . 'en-GB' . DS . $file);
      if ($model->getCore()) 
      {
        $corefiles['administrator'][$file] = 1;
      }
    }
    $corefiles['site'] = array();
    $path = JPATH_SITE . DS . 'languages' . DS . 'en-GB';
    $files = JFolder::files($path . DS . 'language' . DS . $tag, $tag . '\.[^.]+\.ini');
    foreach ($files as $file) 
    {
      $model = JModel::getInstance('translation', 'LocaliseModel', array('ignore_request' => true));
      $model->setState('path', JPATH_ADMINISTRATOR . DS . 'languages' . DS . 'en-GB' . DS . $file);
      if ($model->getCore()) 
      {
        $corefiles['site'][$file] = 1;
      }
    }
    return $corefiles;
  }

  /**
   * Method to get the extensions.
   *
   * @access  public
   * @return  array  array of component object
   * @since  1.6
   */
  public function &getExtensions() 
  {
    $core = array();
    $allfolders = array();
    $clients = array('administrator', 'site');
    $extensions = array();
    $extensions['components'] = array();
    $extensions['modules'] = array();
    $extensions['plugins'] = array();
    $extensions['templates'] = array();
    foreach ($clients as $client) 
    {
      $allfolders[$client] = array();
      $core[$client] = array();

      // 1. scan global folders
      $tags = & JFolder::folders(LocaliseHelper::getLanguagesPath($client), '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'pdf_fonts', 'overrides'));
      foreach ($tags as $tag) 
      {
        $path = 'language' . DS . $tag;
        if ($client == 'administrator') 
        {
          $path = 'administrator' . DS . $path;
        }
        $allfolders[$client][] = $path;
      }

      // 2. scan extensions folders
      $extensions_folders = array('components', 'modules', 'templates');
      $absolute = constant('JPATH_' . strtoupper($client));
      foreach ($extensions_folders as $type) 
      {
        $exts = & JFolder::folders($absolute . DS . $type, '.', false, false);
        foreach ($exts as $extension) 
        {
          $folder = $absolute . DS . $type . DS . $extension . DS . 'language';
          if (JFolder::exists($folder)) 
          {
            $tags = & JFolder::folders($folder, '.', false, false);
            foreach ($tags as $tag) 
            {
              $path = $type . DS . $extension . DS . 'language' . DS . $tag;
              if (JFolder::exists($path . DS . $tag . "." . $extension . ".ini") || JFolder::exists($path . DS . $tag . ".tpl_" . $extension . ".ini")) 
              {
                if ($client == 'administrator') 
                {
                  $path = 'administrator' . DS . $path;
                }
                $allfolders[$client][] = $path;
              }
            }
          }
        }
      }
    }

    // 3. Scan plugin folders
    $groups = & JFolder::folders(JPATH_SITE . DS . 'plugins', '.', false, false);
    foreach ($groups as $group) 
    {
      $plugins = & JFolder::folders(JPATH_SITE . DS . 'plugins' . DS . $group, '.', false, false);
      foreach ($plugins as $plugin) 
      {
        $folder = JPATH_SITE . DS . 'plugins' . DS . $group . DS . $plugin . DS . 'language';
        if (JFolder::exists($folder)) 
        {
          $tags = & JFolder::folders($folder, '.', false, false);
          foreach ($tags as $tag) 
          {
            $path = 'plugins' . DS . $group . DS . $plugin . DS . 'language' . DS . $tag;
            $allfolders['administrator'][] = $path;
            $allfolders['site'][] = $path;
          }
        }
      }
    }
    foreach ($allfolders as $client => $paths) 
    {
      foreach ($paths as $path) 
      {
        $files = JFolder::files(JPATH_SITE . DS . $path, '[^.]+\.[^.]+\.ini');
        foreach ($files as $file) 
        {
          preg_match('/([^.]+)\.([^.]+)\.ini/', $file, $matches);
          $name = & $matches[2];
          if (!array_key_exists($name, $core[$client])) 
          {
            $start = substr($name, 0, 3);
            switch ($start) 
            {
            case 'com':
              $type = 'components';
            break;
            case 'mod':
              $type = 'modules';
            break;
            case 'plg':
              $type = 'plugins';
            break;
            case 'tpl':
              $type = 'templates';
            break;
            default:
              $type = 'error';
            }
            if ($type != 'error') 
            {
              $model = JModel::getInstance('Translation', 'LocaliseModel', array('ignore_request' => true));
              $model->setState('path', $path . DS . $file);
              if ($model->getCore()) 
              {
                $core[$client][$name] = $name;
              }
              if (array_key_exists($name, $core[$client])) 
              {
                if (array_key_exists($name, $extensions[$type])) 
                {
                  $extensions[$type][$name]->$client = - 2;
                  $package_field = $client . "_package";
                  if ($extensions[$type][$name]->site < 0 && $extensions[$type][$name]->administrator < 0) 
                  {
                    unset($extensions[$type][$name]);
                  }
                }
              }
              else
              {
                if (!array_key_exists($name, $extensions[$type])) 
                {
                  $extensions[$type][$name] = new JObject();
                  $extensions[$type][$name]->administrator = - 1;
                  $extensions[$type][$name]->site = - 1;
                }
                $extensions[$type][$name]->$client = 0;
              }
            }
          }
        }
      }
    }
    $filename = $this->getFilename();
    if ($filename) 
    {
      $this->_scanManifest(JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'manifests' . DS . $filename . '.xml', $extensions);
    }
    $types = array('components', 'modules', 'plugins', 'templates');
    foreach ($types as $type) 
    {
      foreach ($extensions[$type] as $name => $object) 
      {
        $object->site_packages = array('home' => array(), 'thirdparty' => array());
        $object->administrator_packages = array('home' => array(), 'thirdparty' => array());
      }
    }
    if (JFolder::exists(JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'manifests')) 
    {
      $packages = JFolder::files(JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'manifests', '\.*\.xml', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
      foreach ($packages as $package) 
      {
        if (basename($package, '.xml') != $filename) 
        {
          $this->_scanOtherManifest($package, $extensions, 'home');
        }
      }
    }
    if (JFolder::exists(JPATH_SITE . DS . 'files' . DS . 'com_localise')) 
    {
      $packages = JFolder::files(JPATH_SITE . DS . 'files' . DS . 'com_localise', '\.*\.xml', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
      foreach ($packages as $package) 
      {
        $this->_scanOtherManifest($package, $extensions, 'thirdparty');
      }
    }
    foreach ($types as $type) 
    {
      ksort($extensions[$type]);
    }
    return $extensions;
  }
  protected function _scanManifest($path, &$extensions) 
  {
    jimport('joomla.filesystem.file');
    $xml = & JFactory::getXMLParser('Simple');
    if (JFile::exists($path)) 
    {
      if ($xml->loadFile($path)) 
      {
        $children = & $xml->document->children();
        foreach ($children as $child) 
        {
          $type = $child->name();
          if (in_array($type, array('components', 'modules', 'plugins', 'templates'))) 
          {
            foreach ($child->children() as $component) 
            {
              $attr = & $component->attributes();
              if (!array_key_exists('client', $attr) || !array_key_exists('name', $attr)) throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
              if (array_key_exists($attr['name'], $extensions[$type])) 
              {
                switch ($attr['client']) 
                {
                case 'site':
                  if ($extensions[$type][$attr['name']]->site >= 0) 
                  {
                    $extensions[$type][$attr['name']]->site = 1;
                  }
                break;
                case 'administrator':
                  if ($extensions[$type][$attr['name']]->administrator >= 0) 
                  {
                    $extensions[$type][$attr['name']]->administrator = 1;
                  }
                break;
                case 'both':
                  if ($extensions[$type][$attr['name']]->site >= 0) 
                  {
                    $extensions[$type][$attr['name']]->site = 1;
                  }
                  if ($extensions[$type][$attr['name']]->administrator >= 0) 
                  {
                    $extensions[$type][$attr['name']]->administrator = 1;
                  }
                break;
                default:
                  throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
                }
              }
            }
          }
          else
          {
            throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
          }
        }
      }
      else
      {
        throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
      }
    }
    else
    {
      throw new JException(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILE_DOES_NOT_EXIST'), $path);
    }
  }
  protected function _scanOtherManifest($path, &$extensions, $where) 
  {
    jimport('joomla.filesystem.file');
    $xml = & JFactory::getXMLParser('Simple');
    if (JFile::exists($path)) 
    {
      if ($xml->loadFile($path)) 
      {
        $children = & $xml->document->children();
        foreach ($children as $child) 
        {
          $type = $child->name();
          if (in_array($type, array('components', 'modules', 'plugins', 'templates'))) 
          {
            foreach ($child->children() as $component) 
            {
              $attr = & $component->attributes();
              if (!array_key_exists('client', $attr) || !array_key_exists('name', $attr)) throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
              if (array_key_exists($attr['name'], $extensions[$type])) 
              {
                switch ($attr['client']) 
                {
                case 'site':
                  if ($extensions[$type][$attr['name']]->site >= 0) 
                  {
                    $extensions[$type][$attr['name']]->site_packages[$where][] = basename($path, '.xml');
                  }
                break;
                case 'administrator':
                  if ($extensions[$type][$attr['name']]->administrator >= 0) 
                  {
                    $extensions[$type][$attr['name']]->administrator_packages[$where][] = basename($path, '.xml');
                  }
                break;
                case 'both':
                  if ($extensions[$type][$attr['name']]->site >= 0) 
                  {
                    $extensions[$type][$attr['name']]->site_packages[] = basename($path, '.xml');
                  }
                  if ($extensions[$type][$attr['name']]->administrator >= 0) 
                  {
                    $extensions[$type][$attr['name']]->administrator_packages[$where][] = basename($path, '.xml');
                  }
                break;
                default:
                  throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
                }
              }
            }
          }
          else
          {
            throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
          }
        }
      }
      else
      {
        throw new JException(JText::sprintf('COM_LOCALISE_ERROR_EDITING_PACKAGE_FILE'), $path);
      }
    }
    else
    {
      throw new JException(JText::sprintf('COM_LOCALISE_ERROR_PACKAGE_FILE_DOES_NOT_EXIST'), $path);
    }
  }
  protected function &_getAllComponents() 
  {
    jimport('joomla.filesystem.folder');
    $names = & JFolder::folders(JPATH_ADMINISTRATOR . DS . 'components');
    $components = array();
    foreach ($names as $name) 
    {
      $component = new JObject();
      $component->name = $name;
      $component->site = 0;
      $component->admin = 0;
      $components[$component->name] = $component;
    }
    return $components;
  }

  /**
   * Method to get the form.
   *
   * @access  public
   * @return  mixed  JForm object on success, false on failure.
   * @since  1.6
   */
  public function &getForm() 
  {
    // Initialize variables.
    $app = & JFactory::getApplication();
    $false = false;

    // Get the form.
    jimport('joomla.form.form');
    JForm::addFormPath(JPATH_COMPONENT . '/models/package/forms');
    $form = & JForm::getInstance('package_form', null, false, array('array' => true));

    /*    if ($this->getState('filename')) {
    $form->load('edit', true, true);
    } else {
    $form->load('add', true, true);
    }
    */
    $form->load('add', true, true);

    // Check for an error.
    if (JError::isError($form)) 
    {
      $this->setError($form->getMessage());
      return $false;
    }
    $data = array();
    $data['filename'] = $this->getFilename();
    $form->bind($data);
    return $form;
  }
  function save() 
  {
    $filename = $this->getState('filename');
    $oldfilename = $this->getState('oldfilename');
    $data = array();
    $data[] = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $data[] = "<package>\n";
    $data[] = "\t<components>\n";
    $components = $this->getState('components');
    foreach ($components as $name => $component) 
    {
      $new_data = "\t\t<component name=\"" . $name . "\" client=\"";
      if (array_key_exists('admin', $component)) 
      {
        if (array_key_exists('site', $component)) 
        {
          $new_data.= "both";
        }
        else
        {
          $new_data.= "administrator";
        }
      }
      else
      {
        $new_data.= "site";
      }
      $new_data.= "\" />\n";
      $data[] = $new_data;
    }
    $data[] = "\t</components>\n";
    $data[] = "\t<modules>\n";
    $modules = $this->getState('modules');
    foreach ($modules as $name => $module) 
    {
      $new_data = "\t\t<module name=\"" . $name . "\" client=\"";
      if (array_key_exists('admin', $module)) 
      {
        if (array_key_exists('site', $module)) 
        {
          $new_data.= "both";
        }
        else
        {
          $new_data.= "administrator";
        }
      }
      else
      {
        $new_data.= "site";
      }
      $new_data.= "\" /> \n";
      $data[] = $new_data;
    }
    $data[] = "\t</modules>\n";
    $data[] = "\t<templates>\n";
    $templates = $this->getState('templates');
    foreach ($templates as $name => $template) 
    {
      $new_data = "\t\t <template name=\"" . $name . "\" client=\"";
      if (array_key_exists('admin', $template)) 
      {
        if (array_key_exists('site', $template)) 
        {
          $new_data.= "both";
        }
        else
        {
          $new_data.= "administrator";
        }
      }
      else
      {
        $new_data.= "site";
      }
      $new_data.= "\" /> \n";
      $data[] = $new_data;
    }
    $data[] = "\t</templates>\n";
    $data[] = "\t<plugins>\n";
    $plugins = $this->getState('plugins');
    foreach ($plugins as $name => $plugin) 
    {
      $new_data = "\t\t<plugin name=\"" . $name . "\" client=\"";
      if (array_key_exists('admin', $plugin)) 
      {
        if (array_key_exists('site', $plugin)) 
        {
          $new_data.= "both";
        }
        else
        {
          $new_data.= "administrator";
        }
      }
      else
      {
        $new_data.= "site";
      }
      $new_data.= "\" /> \n";
      $data[] = $new_data;
    }
    $data[] = "\t</plugins>\n";
    $data[] = "</package>\n";
    $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'manifests' . DS . $filename . ".xml";
    $ret = JFile::write($path, $data);
    if ($ret) 
    {
      if ($oldfilename) 
      {
        $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'manifests' . DS . $oldfilename . ".xml";
        $ret = JFile::delete($path);
        $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'meta' . DS . $oldfilename . ".xml";
        if (JFile::exists($path)) 
        {
          JFile::move($path, JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'meta' . DS . $filename . ".xml");
        }
        $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'full' . DS . $oldfilename . ".xml";
        if (JFile::exists($path)) 
        {
          JFile::move($path, JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'full' . DS . $filename . ".xml");
        }
        $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'site' . DS . $oldfilename . ".xml";
        if (JFile::exists($path)) 
        {
          JFile::move($path, JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'site' . DS . $filename . ".xml");
        }
        $path = JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'administrator' . DS . $oldfilename . ".xml";
        if (JFile::exists($path)) 
        {
          JFile::move($path, JPATH_ROOT . DS . 'media' . DS . 'com_localise' . DS . 'zip' . DS . 'administrator' . DS . $filename . ".xml");
        }
      }
    }
    else
    {
      $this->setError(JText::sprintf('COM_LOCALISE_ERROR_SAVING_PACKAGE_FILE', $path));
    }
    return $ret;
  }
  protected function _populateState() 
  {
    $fields = & JRequest::getVar('package_form');
    if ($fields) 
    {
      $this->setState('filename', $fields['filename']);
    }
    else
    {
      $cid = JRequest::getVar('cid', array(''));
      $this->setState('filename', JRequest::getCmd('filename', $cid[0]));
    }
    $this->setState('oldfilename', JRequest::getCmd('oldfilename', ''));
    $components = & JRequest::getVar('components', array());
    $this->setState('components', $components);
    $modules = & JRequest::getVar('modules', array());
    $this->setState('modules', $modules);
    $templates = & JRequest::getVar('templates', array());
    $this->setState('templates', $templates);
    $plugins = & JRequest::getVar('plugins', array());
    $this->setState('plugins', $plugins);
  }
} 