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

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . ' ' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = JHtml::_('select.option', 'allkeys', JText::_(trim($option)),
						array('option.attr' => 'attributes', 'attr' => 'class="localise-icon inlanguage"')
						);
		}

		$options[] = JHtml::_('select.option', 'allkeys', JText::sprintf('All'),
						array('option.attr' => 'attributes', 'attr' => 'class="allkeys"')
						);

		$options[] = JHtml::_('select.option', 'translatedkeys', JText::sprintf('Translated'),
						array('option.attr' => 'attributes', 'attr' => 'class="translated"')
						);
		$options[] = JHtml::_('select.option', 'untranslatedkeys', JText::sprintf('Untranslated'),
						array('option.attr' => 'attributes', 'attr' => 'class="untranslated"')
						);
		$options[] = JHtml::_('select.option', 'unchangedkeys', JText::sprintf('Unchanged'),
						array('option.attr' => 'attributes', 'attr' => 'class="unchanged"')
						);

		return $options;
	}
}
