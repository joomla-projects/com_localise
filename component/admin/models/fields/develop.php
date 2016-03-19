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
 * Form Field Place class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldDevelop extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Develop';

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

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$options[] = JHtml::_('select.option', 'complete', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_DEVELOP_COMPLETE'),
					array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-equal"')
					);
		$options[] = JHtml::_('select.option', 'incomplete', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_DEVELOP_INCOMPLETE'),
					array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-changed"')
					);

		return $options;
	}
}
