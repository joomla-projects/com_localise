<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Renders a list of all languages
 * Use instead of the joomla library languages element, which only lists languages for one client
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldCoreLanguage extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Corelanguage';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		$attributes = '';

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$params    = JComponentHelper::getParams('com_localise');
		$reference = $params->get('reference', 'en-GB');
		$admin     = JLanguage::getKnownLanguages(LOCALISEPATH_ADMINISTRATOR);
		$site      = JLanguage::getKnownLanguages(LOCALISEPATH_SITE);

		$languages  = array_merge($admin, $site);
		$attributes .= ' class="' . (string) $this->element['class'] . ($this->value == $reference ? ' iconlist-16-reference"' : '"');

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
			$options[] = JHtml::_(
				'select.option',
				$language->tag,
				$language->name,
				array(
					'option.attr' => 'attributes',
					'attr' => 'class="' . ($language->tag == $reference ? 'iconlist-16-reference" title="'
							. JText::_('COM_LOCALISE_TOOLTIP_FIELD_LANGUAGE_REFERENCE') . '"' : '"'
					)
				)
			);
		}

		return $options;
	}
}
