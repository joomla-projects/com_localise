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
 * Form Field State class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldKeystatus extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Keystatus';

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

		$attributes .= ' class="filter-select"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = JHtml::_('select.option', '', JText::_(trim($option)),
						array('option.attr' => 'attributes', 'attr' => 'class="filter-select"')
						);
		}

		$options[] = JHtml::_('select.option', 'allkeys', JText::_('JALL'),
						array('option.attr' => 'attributes', 'attr' => 'class="allkeys"')
						);

		$options[] = JHtml::_('select.option', 'translatedkeys', JText::_('COM_LOCALISE_TEXT_TRANSLATION_TRANSLATED'),
						array('option.attr' => 'attributes', 'attr' => 'class="translated"')
						);
		$options[] = JHtml::_('select.option', 'untranslatedkeys', JText::_('COM_LOCALISE_TEXT_TRANSLATION_UNTRANSLATED'),
						array('option.attr' => 'attributes', 'attr' => 'class="untranslated"')
						);
		$options[] = JHtml::_('select.option', 'unchangedkeys', JText::_('COM_LOCALISE_TEXT_TRANSLATION_UNCHANGED'),
						array('option.attr' => 'attributes', 'attr' => 'class="unchanged"')
						);
		$options[] = JHtml::_('select.option', 'textchangedkeys', JText::_('COM_LOCALISE_TEXT_TRANSLATION_TEXTCHANGED'),
						array('option.attr' => 'attributes', 'attr' => 'class="textchanged"')
						);

		return $options;
	}
}
