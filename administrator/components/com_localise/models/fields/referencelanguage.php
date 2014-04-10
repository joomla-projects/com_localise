<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
jimport('joomla.filesystem.folder');
include_once JPATH_ADMINISTRATOR . '/components/com_localise/helpers/defines.php';

/**
 * Renders a list of all possible languages (they must have a site, language and installation part)
 * Use instead of the joomla library languages element, which only lists languages for one client
 */
class JFormFieldReferenceLanguage extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'ReferenceLanguage';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions() 
	{
		$admin = JLanguage::getKnownLanguages(LOCALISEPATH_ADMINISTRATOR);
		$site  = JLanguage::getKnownLanguages(LOCALISEPATH_SITE);

		if (JFolder::exists(LOCALISEPATH_INSTALLATION)) 
		{
			$installation = JLanguage::getKnownLanguages(LOCALISEPATH_INSTALLATION);
			$languages    = array_intersect_key($admin, $site, $installation);
		}
		else
		{
			$languages = array_intersect_key($admin, $site);
		}

		foreach ($languages as $i => $language) 
		{
			$languages[$i] = JArrayHelper::toObject($language);
		}

		JArrayHelper::sortObjects($languages, 'name');

		$options = parent::getOptions();

		foreach ($languages as $language) 
		{
			$options[] = JHtml::_('select.option', $language->tag, $language->name);
		}

		return $options;
	}
}
