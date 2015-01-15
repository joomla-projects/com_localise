<?php
/**
 * @package     Com_Localise
 * @subpackage  tables
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Localise Table class for the Localise Component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseTableLocalise extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * The title to use for the asset table
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * Checked out status
	 *
	 * @var int
	 */
	public $checked_out = null;

	/**
	 * Checkout out time
	 *
	 * @var date
	 */
	public $checked_out_time = null;

	/**
	 * The asset ID
	 *
	 * @var asset_id
	 */
	public $asset_id = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database connector object
	 */
	public function __construct(&$db)
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

		return 'com_localise.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	protected function _getAssetTitle()
	{
		return basename($this->path);
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  JTable Table object
	 * @param   Integer  $id     Primart key of table
	 *
	 * @return  Integer          Parent asset id for the record
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$db = $this->getDbo();

		// Build the query to get the asset id for the parent category.
		$asset        = JTable::getInstance('asset');
		$name         = basename($this->path);
		$relativePath = substr($this->path, strlen(JPATH_ROOT));

		if (preg_match('/^([^.]*)\..*\.ini$/', $name, $matches) || preg_match('/^([^.]*)\.ini$/', $name, $matches))
		{
			$params              = JComponentHelper::getParams('com_localise');
			$installation_folder = $params->get('installation', 'installation');
			$tag                 = $matches[1];

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
					$component->name  = 'com_localise';
					$component->title = 'com_localise';
					$component->setLocation($root->id, 'last-child');

					if (!$component->check() || !$component->store())
					{
						$this->setError($component->getError());

						return false;
					}
				}

				$asset->name  = "com_localise.$id";
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
				$asset->name  = 'com_localise';
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
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/delete
	 * @since   11.1
	 * @throws  UnexpectedValueException
	 */
	public function delete($pk = null)
	{
		if (!$this->deleteLanguageTranslations())
		{
			return false;
		}

		return parent::delete($pk);
	}

	/**
	 * Delete language translations
	 *
	 * @return  boolean
	 */
	protected function deleteLanguageTranslations()
	{
		$fileName  = basename($this->path);
		$fileInfo  = pathinfo($fileName);
		$extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : null;
		$langTag   = $fileInfo['filename'];

		// Make sure we are deleting a base language. Otherwise avoid to delete
		if ($extension != 'xml')
		{
			return true;
		}

		if ($langTag)
		{
			$db = $this->getDbo();

			$searchTag = $db->quote('%' . $db->escape($langTag, true) . '%');

			$query = $db->getQuery(true)
				->delete('#__localise')
				->where('path LIKE ' . $searchTag);

			$db->setQuery($query);

			if (!$db->execute())
			{
				$this->setError('COM_LOCALISE_ERROR_DELETING_DB_TRANSLATIONS');

				return false;
			}
		}

		return true;
	}
}
