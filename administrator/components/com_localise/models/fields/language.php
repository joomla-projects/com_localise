<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.filesystem.folder');

/**
 * Renders a list of all languages
 * Use instead of the joomla library languages element, which only lists languages for one client
 */
class JFormFieldLanguage extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Language';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput() 
	{
		$attributes = '';

		if ($v = (string)$this->element['onchange']) 
		{
			$attributes.= ' onchange="' . $v . '"';
		}

		$params    = JComponentHelper::getParams('com_localise');
		$reference = $params->get('reference', 'en-GB');
		$admin     = JLanguage::getKnownLanguages(LOCALISEPATH_ADMINISTRATOR);
		$site      = JLanguage::getKnownLanguages(LOCALISEPATH_SITE);

		if (JFolder::exists(LOCALISEPATH_INSTALLATION)) 
		{
			$install = JLanguage::getKnownLanguages(LOCALISEPATH_INSTALLATION);
		}
		else
		{
			$install = array();
		}

		$languages  = array_merge($admin, $site, $install);
		$attributes.= ' class="'.(string) $this->element['class'].($this->value == $reference ? ' iconlist-16-reference"' : '"');

		foreach ($languages as $i => $language) 
		{
			$languages[$i] = JArrayHelper::toObject($language);
		}

		JArrayHelper::sortObjects($languages, 'name');
		$options = array();

		foreach ($this->element->children() as $option) 
		{
			$options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		foreach ($languages as $language) 
		{
			$options[] = JHtml::_('select.option', $language->tag, $language->name, array('option.attr' => 'attributes', 'attr' => 'class="'.($language->tag == $reference ? 'iconlist-16-reference" title="' . JText::_('COM_LOCALISE_TOOLTIP_FIELD_LANGUAGE_REFERENCE') . '"' : '"')));
		}

		$return = JHtml::_('select.genericlist', $options, $this->name, array('id' => $this->id, 'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes));
		return $return;
	}
}
