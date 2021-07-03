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

jimport('joomla.form.formfield');

/**
 * Form Field Place class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldOrigin extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Origin';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		$attributes = '';

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		if ($this->value == '_thirdparty')
		{
			$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-thirdparty"';
		}
		elseif ($this->value == '_override')
		{
			$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-override"';
		}
		elseif ($this->value == 'core')
		{
			$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-core"';
		}
		else
		{
			$attributes .= ' class="' . (string) $this->element['class'] . '"';
		}

		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$packages         = LocaliseHelper::getPackages();
		$packages_options = array();

		/** We took off the packages icons (due to bootstrap implementation)
		 * @Todo: this may need review
		foreach ($packages as $package)
		{
			$packages_options[] = JHtml::_(
				'select.option',
				$package->name,
				JText::_($package->title),
				array(
					'option.attr' => 'attributes',
					'attr' => 'class="localise-icon" style="background-image: url(' . JURI::root(true) . $package->icon . ');"'
				)
			);

			if ($this->value == $package->name)
			{
				$attributes .= ' style="background-image: url(' . JURI::root(true) . $package->icon . ');"';
			}
		}
		*/

		$packages_options = ArrayHelper::sortObjects($packages_options, 'text');
		$thirdparty       = JHtml::_('select.option', '_thirdparty', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_THIRDPARTY'),
							array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-thirdparty"')
							);
		$override         = JHtml::_('select.option', '_override', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_OVERRIDE'),
							array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-override"')
							);
		$core             = JHtml::_('select.option', 'core', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_CORE'),
							array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-core"')
							);
		$return           = JHtml::_('select.genericlist', array_merge($options, $packages_options, array($thirdparty), array($override), array($core)),
							$this->name, array('id' => $this->id, 'list.select' => $this->value, 'option.attr' => 'attributes',
							'list.attr' => $attributes, 'group.items' => null)
							);

		return $return;
	}
}
