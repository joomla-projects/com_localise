<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
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
 * @since  1.0
 */
class LocaliseModelExportPackage extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var    string
	 */
	protected $context = 'com_localise.package';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return void
	 */
	protected function populateState()
	{
		// Get the data
		$data = JFactory::getApplication()->input->post->get('jform', array(), 'array');

		// Initialise variables
		$config        = JFactory::getConfig();
		$cookie_domain = $config->get('config.cookie_domain', '');
		$cookie_path   = $config->get('config.cookie_path', '/');

		// Set the cookies
		setcookie(JApplicationHelper::getHash($this->context . '.author'), $data['author'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplicationHelper::getHash($this->context . '.copyright'), $data['copyright'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplicationHelper::getHash($this->context . '.email'), $data['email'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplicationHelper::getHash($this->context . '.url'), $data['url'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplicationHelper::getHash($this->context . '.version'), $data['version'], time() + 365 * 86400, $cookie_path, $cookie_domain);
		setcookie(JApplicationHelper::getHash($this->context . '.license'), $data['license'], time() + 365 * 86400, $cookie_path, $cookie_domain);

		// Set the state
		$this->setState('exportpackage.name', $data['name']);
		$this->setState('exportpackage.author', $data['author']);
		$this->setState('exportpackage.copyright', $data['copyright']);
		$this->setState('exportpackage.email', $data['email']);
		$this->setState('exportpackage.url', $data['url']);
		$this->setState('exportpackage.version', $data['version']);
		$this->setState('exportpackage.license', $data['license']);
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
		$package = $model->getItem();

		// Check if the package is correct
		if (count($package->getErrors()))
		{
			$this->setError(implode('<br />', $package->getErrors()));

			return false;
		}

		// Check if the manifest exists
		$manifest = JPATH_MANIFESTS . '/files/' . $package->manifest . '.xml';

		if (is_file($manifest))
		{
			// Get the key name and key description in the manifest
			$xml = simplexml_load_file($manifest);

			if ($xml)
			{
				$keyName        = (string) $xml->name;
				$keyDescription = (string) $xml->description;
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
		$tags = JFolder::folders(
			JPATH_SITE . '/language', '.', false, false, array('.svn','CVS','.DS_Store','__MACOSX','pdf_fonts', 'overrides')
		);

		$files = array();

		foreach ($tags as $i => $tag)
		{
			$langPath = "language/$tag/$tag.$keyName.ini";

			if (is_file(JPATH_SITE . "/$langPath"))
			{
				$files[$tag]                     = array();
				$files[$tag]['name']             = $langPath;
				$files[$tag]['data']             = file_get_contents(JPATH_SITE . "/$langPath");
				$files[$tag]['time']             = time();
				$files[$tag . '.manage']         = array();
				$files[$tag . '.manage']['name'] = "language/$tag/$tag.$keyName.manage.ini";

				$lang = JLanguage::getInstance($tag);
				$lang->load('com_localise', JPATH_ADMINISTRATOR, null, false, false)
				|| $lang->load('com_localise', JPATH_ADMINISTRATOR . '/components/com_localise', null, false, false)
				|| $lang->load('com_localise', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
				|| $lang->load('com_localise', JPATH_ADMINISTRATOR . '/components/com_localise', $lang->getDefault(), false, false);

				$files[$tag . '.manage']['data'] = strtoupper($keyName)
					. '="' . sprintf($lang->_('COM_LOCALISE_NAME_PACKAGE'), $lang->_($package->title))
					. "\"\n";

				$files[$tag . '.manage']['data'] .= strtoupper($keyDescription)
					. '="' . sprintf($lang->_('COM_LOCALISE_DESCRIPTION_PACKAGE'), $lang->_($package->title))
					. "\"\n";

				$files[$tag . '.manage']['time'] = time();
			}
			else
			{
				unset($tags[$i]);
			}
		}

		$files['package']         = array();
		$files['package']['name'] = "packages/$packageName.xml";
		$files['package']['data'] = file_get_contents($path);
		$files['package']['time'] = time();

		$files['manifest']         = array();
		$files['manifest']['name'] = $element . '.xml';

		// @Todo: following lines can be moved to a JLayout or to a heredoc or something more nice
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
			$files['manifest']['data'] .= '<language tag="' . $tag . '">language/' . $tag . '/' . $tag . '.' . $keyName . '.ini</language>';
			$files['manifest']['data'] .= '<language tag="' . $tag . '">language/' . $tag . '/' . $tag . '.' . $keyName . '.manage.ini</language> ';
		}

		$files['manifest']['data'] .= '</languages></extension>';
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
		else
		{
			if (!$packager->create($ziproot, $files))
			{
				$this->setError(JText::_('COM_LOCALISE_ERROR_EXPORT_ZIPCREATE'));

				return false;
			}
		}

		// Create item
		$item           = new JObject;
		$item->filename = "fil_localise_${packageName}_package" . ($packageVersion
				? "-$packageVersion"
				: '');
		$item->contents = file_get_contents($ziproot);

		return $item;
	}
}
