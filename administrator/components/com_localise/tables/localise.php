<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author    Mohammad Hasani Eghtedar <m.h.eghtedar@gmail.com>
# copyright Copyright (C) 2010 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// import Joomla table library
jimport('joomla.database.table');

/**
 * Localise Table class for the Localise Component
 *
 * @package  Extensions.Components
 * @subpackage  Localise
 */
class LocaliseTableLocalise extends JTable
{
  /**
   * Primary Key
   *
   * @var int
   */
  var $id = null;

  /**
   * @var string
   */
  var $path = null;

  /**
   * @var int
   */
  var $checked_out = null;

  /**
   * @var date
   */
  var $checked_out_time = null;

  /**
   * @var asset_id
   */
  var $asset_id = null;

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function LocaliseTableLocalise(&$db) 
  {
    parent::__construct('#__localise', 'id', $db);
  }

  /**
   * Method to compute the default name of the asset.
   * The default name is in the form `table_name.id`
   * where id is the value of the primary key of the table.
   *
   * @return  string
   */
  protected function _getAssetName() 
  {
    $k = $this->_tbl_key;
    return 'com_localise.' . (int)$this->$k;
  }

  /**
   * Method to return the title to use for the asset table.
   *
   * @return  string
   * @since  1.6
   */
  protected function _getAssetTitle() 
  {
    return basename($this->path);
  }

  /**
   * Get the parent asset id for the record
   *
   * @return  int
   */
  protected function _getAssetParentId(JTable $table = NULL, $id = NULL) 
  {
    // Initialise variables.
    $db = $this->getDbo();

    // Build the query to get the asset id for the parent category.
    $asset = JTable::getInstance('asset');
    $name = basename($this->path);
    $relativePath = substr($this->path, strlen(JPATH_ROOT));
    if (preg_match('/^([^.]*)\..*\.ini$/', $name, $matches) || preg_match('/^([^.]*)\.ini$/', $name, $matches)) 
    {
      $params = JComponentHelper::getParams('com_localise');
      $installation_folder = $params->get('installation', 'installation');
      $tag = $matches[1];
      if (preg_match('#^/(administrator|plugins)#', $relativePath)) 
      {
        $id = LocaliseHelper::getFileId(JPATH_ROOT . "/administrator/language/$tag/$tag.xml");
      }
      elseif (preg_match('#^/' . $installation_folder . '#', $relativePath)) 
      {
        $id = LocaliseHelper::getFileId(LOCALISEPATH_INSTALLATION . "/language/$tag/$tag.xml");
      }
      else
      {
        $id = LocaliseHelper::getFileId(JPATH_ROOT . "/language/$tag/$tag.xml");
      }
      $assetName = "com_localise.$id";
      if (!$asset->loadByName($assetName)) 
      {
        $component = JTable::getInstance('asset');
        if (!$component->loadByName('com_localise')) 
        {
          $root = JTable::getInstance('asset');
          $root->rebuild();
          $root->loadByName('root.1');
          $component->name = 'com_localise';
          $component->title = 'com_localise';
          $component->setLocation($root->id, 'last-child');
          if (!$component->check() || !$component->store()) 
          {
            $this->setError($component->getError());
            return false;
          }
        }
        $asset->name = "com_localise.$id";
        $asset->title = $name;
        $asset->setLocation($component->id, 'last-child');
        if (!$asset->check() || !$asset->store()) 
        {
          $this->setError($asset->getError());
          return false;
        }
      }
    }
    else
    {
      if (!$asset->loadByName('com_localise')) 
      {
        $root = JTable::getInstance('asset');
        $root->loadByName('root.1');
        $asset->name = 'com_localise';
        $asset->title = 'com_localise';
        $asset->setLocation($root->id, 'last-child');
        if (!$asset->check() || !$asset->store()) 
        {
          $this->setError($asset->getError());
          return false;
        }
      }
    }
    return $asset->id;
  }

  /**
   * Method to load a row from the database by primary key and bind the fields
   * to the JTable instance properties.
   *
   * @param  mixed  An optional primary key value to load the row by, or an array of fields to match.  If not
   *          set the instance property value is used.
   * @param  boolean  True to reset the default values before loading the new row.
   * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
   * @since  1.0
   * @link  http://docs.joomla.org/JTable/load
   */
  public function load($keys = null, $reset = true) 
  {
    if (!is_array($keys)) 
    {
      // Load by primary key.
      static $instances;
      if (!isset($instances)) 
      {
        $db = $this->getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__localise');
        $db->setQuery($query);
        $instances = $db->loadAssocList('id');
      }
      if (empty($keys)) 
      {
        // If empty, use the value of the current key
        $keyName = $this->getKeyName();
        $keys = array($keyName => $this->$keyName);
      }
      if (array_key_exists($keys, $instances)) 
      {
        return $this->bind($instances[$keys]);
      }
    }
    return parent::load($keys, $reset);
  }
}
