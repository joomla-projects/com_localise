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
 * Form Field Legend class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldLegend extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Legend';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		$return = '<table class="pull-left">';
		$return .= '<tr><td><input class="translated" size="30" type="text" value="' . JText::_('COM_LOCALISE_TEXT_TRANSLATION_TRANSLATED')
					. '" readonly="readonly"/></td></tr>';
		$return .= '<tr><td><input class="unchanged" size="30"  type="text" value="' . JText::_('COM_LOCALISE_TEXT_TRANSLATION_UNCHANGED')
					. '" readonly="readonly"/></td></tr>';
		$return .= '<tr><td><input class="untranslated" size="30"  type="text" value="' . JText::_('COM_LOCALISE_TEXT_TRANSLATION_UNTRANSLATED')
					. '" readonly="readonly"/></td></tr>';
		$return .= '<tr><td><input class="extra" size="30" type="text" value="' . JText::_('COM_LOCALISE_TEXT_TRANSLATION_NOTINREFERENCE')
					. '" readonly="readonly"/></td></tr>';
		$return .= '</table>';

		return $return;
	}
}
