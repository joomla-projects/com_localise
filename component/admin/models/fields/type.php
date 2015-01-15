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
class JFormFieldType extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Type';

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

		$options[] = JHtml::_('select.option', 'component', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_COMPONENT'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-component"')
						);
		$options[] = JHtml::_('select.option', 'module', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_MODULE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-module"')
						);
		$options[] = JHtml::_('select.option', 'plugin', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PLUGIN'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-plugin"')
						);
		$options[] = JHtml::_('select.option', 'template', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_TEMPLATE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-template"')
						);
		$options[] = JHtml::_('select.option', 'package', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PACKAGE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-package"')
						);
		$options[] = JHtml::_('select.option', 'library', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_LIBRARY'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-library"')
						);
		$options[] = JHtml::_('select.option', 'file', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_FILE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-file"')
						);
		$options[] = JHtml::_('select.option', 'joomla', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_JOOMLA'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-joomla"')
						);
		$options[] = JHtml::_('select.option', 'override', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_OVERRIDE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-override"')
						);

		return $options;
	}
}
