<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.utilities.utility');

/**
 * Export Package Model class for the Localise component
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
class LocaliseModelExportPackage extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var    string
	 */
	protected $_context = 'com_localise.package';

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState() 
	{
		// Get the data
		$data = JRequest::getVar('jform', array(), 'post', 'array');

		// Initialise variables
		$config        = JFactory::getConfig();
		$cookie_domain = $config->get('config.cookie_domain', '');
		$cookie_path   = $config->get('config.cookie_path', '/');

		// Set the cookies
		setcookie(JApplication::getHash($this->_context . '.author'   ), $data['author']   , time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplication::getHash($this->_context . '.copyright'), $data['copyright'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplication::getHash($this->_context . '.email'    ), $data['email']    , time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplication::getHash($this->_context . '.url'      ), $data['url']      , time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplication::getHash($this->_context . '.version'  ), $data['version']  , time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplication::getHash($this->_context . '.license'  ), $data['license']  , time() + 365 * 86400, $cookie_path, $cookie_domain);

		// Set the state
		$this->setState('exportpackage.name'     , $data['name']);
		$this->setState('exportpackage.author'   , $data['author']);
		$this->setState('exportpackage.copyright', $data['copyright']);
		$this->setState('exportpackage.email'    , $data['email']);
		$this->setState('exportpackage.url'      , $data['url']);
		$this->setState('exportpackage.version'  , $data['version']);
		$this->setState('exportpackage.license'  , $data['license']);
	}

	/**
	 * Get the item
	 */
	public function getItem() 
	{
		// Get variables
		$packageName        = $this->getState('exportpackage.name');
		$packageAuthor      = $this->getState('exportpackage.author');
		$packageCopyright   = $this->getState('exportpackage.copyright');
		$packageAuthorEmail = $this->getState('exportpackage.email');
		$packageAuthorUrl   = $this->getState('exportpackage.url');
		$packageVersion     = $this->getState('exportpackage.version');
		$packageLicense     = $this->getState('exportpackage.license');

		$path = JPATH_COMPONENT_ADMINISTRATOR . "/packages/$packageName.xml";
		$id   = LocaliseHelper::getFileId($path);

		// Check if the package exists
		if (empty($id)) 
		{
			$this->setError('COM_LOCALISE_ERROR_EXPORT_UNEXISTING', $packageName);
			return false;
		}

		// Get the package model
		$model = JModelLegacy::getInstance('Package', 'LocaliseModel');
		$model->setState('package.id', $id);
		$model->setState('package.name', $packageName);
		$package = $model->getItem();var_dump($package); //jexit();

		// Check if the package is correct
		if (count($package->getErrors())) 
		{
			$this->setError(implode('<br />', $package->getErrors()));
			return false;
		}

		// Check if the manifest exists
		$manifest = JPATH_MANIFESTS . '/files/' . $package->manifest . '.xml';
		if (JFile::exists($manifest)) 
		{
			// Get the key name and key description in the manifest
			$xml = JFactory::getXML($manifest);
			if ($xml) 
			{
				$keyName        = (string)$xml->name;
				$keyDescription = (string)$xml->description;
				$element        = $package->manifest;
			}
			else
			{
				$this->setError('COM_LOCALISE_ERROR_EXPORT_MANIFEST', $manifest);
				return false;
			}
		}
		else
		{
			// Create the key name and key description
			$keyName        = "fil_localise_package_${packageName}";
			$keyDescription = "fil_localise_package_${packageName}_desc";
			$element        = "localise_package_${packageName}";
		}

		// Lookup for language files
		$tags = JFolder::folders(JPATH_SITE . '/language', '.', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'pdf_fonts', 'overrides'));

		$files = array();

		foreach ($tags as $i => $tag) 
		{
			$langPath = "language/$tag/$tag.$keyName.ini";

			if (JFile::exists(JPATH_SITE . "/$langPath")) 
			{
				$files[$tag] = array();
				$files[$tag]['name'] = $langPath;
				$files[$tag]['data'] = JFile::read(JPATH_SITE . "/$langPath");
				$files[$tag]['time'] = time();
				$files[$tag . '.manage'] = array();
				$files[$tag . '.manage']['name'] = "language/$tag/$tag.$keyName.manage.ini";

				$lang = JLanguage::getInstance($tag);
				$lang->load('com_localise', JPATH_ADMINISTRATOR, null, false, false) || $lang->load('com_localise', JPATH_ADMINISTRATOR . '/components/com_localise', null, false, false) || $lang->load('com_localise', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false) || $lang->load('com_localise', JPATH_ADMINISTRATOR . '/components/com_localise', $lang->getDefault(), false, false);

				$files[$tag . '.manage']['data'] = strtoupper($keyName) . '="' . sprintf($lang->_('COM_LOCALISE_NAME_PACKAGE'), $lang->_($package->title)) . "\"\n";

				$files[$tag . '.manage']['data'].= strtoupper($keyDescription) . '="' . sprintf($lang->_('COM_LOCALISE_DESCRIPTION_PACKAGE'), $lang->_($package->title)) . "\"\n";

				$files[$tag . '.manage']['time'] = time();
			}
			else
			{
				unset($tags[$i]);
			}
		}

		$files['package'] = array();
		$files['package']['name'] = "packages/$packageName.xml";
		$files['package']['data'] = JFile::read($path);
		$files['package']['time'] = time();

		$files['manifest'] = array();
		$files['manifest']['name'] = $element . '.xml';
		$files['manifest']['data'] = '<?xml version="1.0" encoding="UTF-8"?>
<extension type="file" version="3.1" method="upgrade">
	<name>' . strtoupper($keyName) . '</name>
	<description>' . strtoupper($keyDescription) . '</description>
	<creationDate>' . JFactory::getDate()->format('F j, Y') . '</creationDate>
	<author>' . $packageAuthor . '</author>
	<copyright>' . $packageCopyright . '</copyright>
	<authorEmail>' . $packageAuthorEmail . '</authorEmail>
	<authorUrl>' . $packageAuthorUrl . '</authorUrl>
	<version>' . $packageVersion . '</version>
	<license>' . $packageLicense . '</license>
	<fileset>
		<files target="media/com_localise">
		<file>packages/' . $packageName . '.xml</file>
	</files>
	</fileset>
	<languages>';

		foreach ($tags as $tag) 
		{
			$files['manifest']['data'].= '<language tag="' . $tag . '">language/' . $tag . '/' . $tag . '.' . $keyName . '.ini</language>';
			$files['manifest']['data'].= '<language tag="' . $tag . '">language/' . $tag . '/' . $tag . '.' . $keyName . '.manage.ini</language> ';
		}

		$files['manifest']['data'].= '</languages></extension>';
		$files['manifest']['time'] = time();

		$ziproot = JPATH_ROOT . '/tmp/' . uniqid('com_localise_') . '.zip';

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

		// Run the packager
		if (!$packager = JArchive::getAdapter('zip')) 
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ADAPTER'));
			return false;
		}
		else if (!$packager->create($ziproot, $files)) 
		{
			$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPCREATE'));
			return false;
		}

		// Create item
		$item = new JObject;
		$item->filename = "fil_localise_${packageName}_package" . ($packageVersion ? "-$packageVersion" : '');
		$item->contents = file_get_contents($ziproot);
		return $item;
	}
} 