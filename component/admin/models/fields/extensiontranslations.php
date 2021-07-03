<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('groupedlist');

/**
 * Form Field ExtensionTranslations class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldExtensionTranslations extends JFormFieldGroupedList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'ExtensionTranslations';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of JHtml options.
	 */
	protected function getGroups()
	{
		// Remove '.ini' from values
		if (is_array($this->value))
		{
			foreach ($this->value as $key => $val)
			{
				$this->value[$key] = substr($val, 0, -4);
			}
		}

		$xml = simplexml_load_file(JPATH_ROOT . '/media/com_localise/packages/core.xml');
		$coreadminfiles	= (array) $xml->administrator->children();
		$coresitefiles	= (array) $xml->site->children();

		$coresitefiles	= $coresitefiles['filename'];
		$coreadminfiles	= $coreadminfiles['filename'];

		$coreadminfiles	= self::suffix_array_values($coreadminfiles, '.ini');
		$coresitefiles	= self::suffix_array_values($coresitefiles, '.ini');

		$package = (string) $this->element['package'];
		$groups  = array('Site' => array(), 'Administrator' => array());

		foreach (array('Site', 'Administrator') as $client)
		{
			$path = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

			if (JFolder::exists($path))
			{
				$tags = JFolder::folders($path, '.', false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

				if ($tags)
				{
					foreach ($tags as $tag)
					{
						$files = JFolder::files("$path/$tag", ".ini$");

						$files = str_replace($tag . '.', '', $files);
						$files = array_diff($files, $coreadminfiles);
						$files = array_diff($files, $coresitefiles);
						$files = array_diff($files, array('ini'));

						foreach ($files as $file)
						{
							$basename = $file;
							$key      = substr($basename, 0, strlen($basename) - 4);
							$value    = $key;
							$origin   = LocaliseHelper::getOrigin($key, strtolower($client));
							$disabled = $origin != $package && $origin != '_thirdparty';

							$groups[$client][$key] = JHtml::_('select.option', strtolower($client) . '_' . $key, $value, 'value', 'text', false);
						}
					}
				}
			}
		}

		$scans = LocaliseHelper::getScans();

		foreach ($scans as $scan)
		{
			$prefix     = $scan['prefix'];
			$suffix     = $scan['suffix'];
			$type       = $scan['type'];
			$client     = ucfirst($scan['client']);
			$path       = $scan['path'];
			$folder     = $scan['folder'];
			$extensions = JFolder::folders($path);

			foreach ($extensions as $extension)
			{
				// Take off core extensions containing a language folder
				if ($extension != 'mod_version' && $extension != 'mod_multilangstatus' && $extension != 'protostar'
					&& $extension != 'hathor' && $extension != 'isis' && $extension != 'beez3' && $extension != 'languagecode')
				{
					if (JFolder::exists("$path$extension/language"))
					{
						// Scan extensions folder
						$tags = JFolder::folders("$path$extension/language");

						foreach ($tags as $tag)
						{
							$file = "$path$extension/language/$tag/$tag.$prefix$extension$suffix.ini";

							if (JFile::exists($file))
							{
								$origin   = LocaliseHelper::getOrigin("$prefix$extension$suffix", strtolower($client));
								$disabled = $origin != $package && $origin != '_thirdparty';

							/* @ Todo: $disabled prevents choosing some core files when creating package.
							 $groups[$client]["$prefix$extension$suffix"] = JHtml::_(
							'select.option', strtolower($client) . '_' . "$prefix$extension$suffix", "$prefix$extension$suffix", 'value', 'text', $disabled);
							*/
							$groups[$client]["$prefix$extension$suffix"] = JHtml::_(
									'select.option', strtolower($client) . '_' . "$prefix$extension$suffix", "$prefix$extension$suffix", 'value', 'text', false
							);
							}
						}
					}
				}
			}
		}

		foreach ($groups as $client => $extensions)
		{
			if (count($groups[$client]) == 0)
			{
				$groups[$client][] = JHtml::_('select.option', '',  JText::_('COM_LOCALISE_NOTRANSLATION'), 'value', 'text', true);
			}
			else
			{
				ArrayHelper::sortObjects($groups[$client], 'text');
			}
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}

	/**
	 * Method to add a suffix to an array.
	 *
	 * @param   array   $array   An array of core files.
	 * @param   string  $suffix  The suffix to add to each file.
	 *
	 * @return  array   The modified array
	 */
	public static function suffix_array_values($array, $suffix = '')
	{
		if (!is_array($array))
		{
			return false;
		}

		// Suffix the values and respect the keys
		foreach ($array as $key => $value)
		{
			if (!is_string($value))
			{
				continue;
			}

			$array[$key] = $value . $suffix;
		}

		return $array;
	}
}
