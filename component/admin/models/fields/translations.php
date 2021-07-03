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
 * Form Field Translations class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldTranslations extends JFormFieldGroupedList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Translations';

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

		$package = (string) $this->element['package'];
		$groups  = array('Site' => array(), 'Administrator' => array(), 'Installation' => array());

		foreach (array('Site', 'Administrator', 'Installation') as $client)
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

						foreach ($files as $file)
						{
							$basename = substr($file, strlen($tag) + 1);

							if ($basename == 'ini')
							{
								$key      = 'joomla';
								$value    = JText::_('COM_LOCALISE_TEXT_TRANSLATIONS_JOOMLA');
								$origin   = LocaliseHelper::getOrigin('', strtolower($client));
								$disabled = $origin != $package && $origin != '_thirdparty';
							}
							else
							{
								$key      = substr($basename, 0, strlen($basename) - 4);
								$value    = $key;
								$origin   = LocaliseHelper::getOrigin($key, strtolower($client));
								$disabled = $origin != $package && $origin != '_thirdparty';
							}

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
				if (JFolder::exists("$path$extension$folder/language"))
				{
					// Scan extensions folder
					$tags = JFolder::folders("$path$extension$folder/language");

					foreach ($tags as $tag)
					{
						$file = "$path$extension$folder/language/$tag/$tag.$prefix$extension$suffix.ini";

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

		foreach ($groups as $client => $extensions)
		{
			ArrayHelper::sortObjects($groups[$client], 'text');
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
