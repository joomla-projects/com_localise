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
 * Form Field Key class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class JFormFieldKey extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Key';

	/**
	 * Layout to render the label
	 *
	 * @var  string
	 */
	protected $renderLabelLayout = 'field.key.label';

	/**
	 * Layout to render the field input
	 *
	 * @var  string
	 */
	protected $inputLayout = 'field.key.input';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		return JLayoutHelper::render($this->getInputLayout(), $this->getLayoutData());
	}

	/**
	 * Method to get the layout to use to render the field input
	 *
	 * @return  string
	 */
	protected function getInputLayout()
	{
		return !empty($this->element['layout']) ? (string) $this->element['layout'] : $this->inputLayout;
	}

	/**
	 * Get the data that is going to be passed to the layout
	 *
	 * @return  array
	 */
	protected function getLayoutData()
	{
		// Label preprocess
		$label = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$label = $this->translateLabel ? JText::_($label) : $label;

		// Description preprocess
		$description = !empty($this->description) ? $this->description : null;
		$description = !empty($description) && $this->translateDescription ? JText::_($description) : $description;

		$hiddenLabel = empty($options['hiddenLabel']) && $this->getAttribute('hiddenLabel');

		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		$debug    = !empty($this->element['debug']) ? ((string) $this->element['debug'] === 'true') : false;

		return array(
			'autofocus'   => $this->autofocus,
			'class'       => trim($this->class . ' form-field'),
			'debug'       => $debug,
			'description' => $description,
			'disabled'    => $this->disabled,
			'element'     => $this->element,
			'field'       => $this,
			'hidden'      => $this->hidden,
			'hiddenLabel' => $hiddenLabel,
			'hint'        => $hint,
			'id'          => $this->id,
			'label'       => $label,
			'multiple'    => $this->multiple,
			'name'        => $this->name,
			'onchange'    => $this->onchange,
			'onclick'     => $this->onclick,
			'readonly'    => $this->readonly,
			'required'    => $this->required,
			'size'        => $this->size,
			'value'       => $this->value
		);
	}
}
