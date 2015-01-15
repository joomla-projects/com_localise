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
 * Form Field Search class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldSearch extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Search';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		$html = '<div class="filter-search btn-group pull-left">';
		$html .= '<input class="hasTooltip" type="text" name="' . $this->name . '" id="' . $this->id . '" placeholder="'
				. JText::_($this->element['placeholder']) . '" value="' . $this->value
				. '" title="' . JText::_('JSEARCH_FILTER') . '" onchange="this.form.submit();" />';
		$html .= '</div><div class="btn-group pull-left">';
		$html .= '<button type="submit" class="btn hasTooltip" rel="tooltip" title="' . JText::_('JSEARCH_FILTER_SUBMIT') . '">
				<i class="icon-search"></i></button>';
		$html .= '<button type="button" class="btn hasTooltip" rel="tooltip" title="' . JText::_('JSEARCH_FILTER_CLEAR')
				. '" onclick="document.id(\'' . $this->id . '\').value=\'\';this.form.submit();"><i class="icon-remove"></i></button>';
		$html .= '</div>';

		return $html;
	}
}
