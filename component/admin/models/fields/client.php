<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Form Field Client class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @since       1.0
 */
class JFormFieldClient extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Client';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 */
	protected function getInput()
	{
		$attributes = '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attributes .= ' disabled="disabled"';
		}

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

		$options[] = JHtml::_('select.option', 'site', JText::_('COM_LOCALISE_OPTION_CLIENT_SITE'),
					array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-site"')
					);

		$options[] = JHtml::_('select.option', 'administrator', JText::_('COM_LOCALISE_OPTION_CLIENT_ADMINISTRATOR'),
					array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-administrator"')
					);

		if (LocaliseHelper::hasInstallation())
		{
			$options[] = JHtml::_('select.option', 'installation', JText::_('COM_LOCALISE_OPTION_CLIENT_INSTALLATION'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-installation"')
						);
		}

		$return = array();

		if ((string) $this->element['readonly'] == 'true')
		{
			$return[] = JHtml::_('select.genericlist', $options, '', array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
			$return[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		else
		{
			$return[] = JHtml::_('select.genericlist', $options, $this->name, array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
		}

		return implode($return);
	}
}
