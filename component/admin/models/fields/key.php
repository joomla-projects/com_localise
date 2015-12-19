<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

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
	 * Method to get the field label.
	 *
	 * @return  string    The field label.
	 */

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since  1.6
	 */
	protected function getLabel()
	{
		$istranslation = (int) $this->element['istranslation'];
		$status        = (string) $this->element['status'];
		$istextchange  = (int) $this->element['istextchange'];

		if ($istextchange == '1')
		{
			$textchange_status     = (int) $this->element['changestatus'];
			$textchange_source     = (string) $this->element['sourcetext'];
			$textchange_target     = (string) $this->element['targettext'];
			$textchange_visible_id = "textchange_visible_id_" . $this->element['name'];
			$textchange_hidded_id  = "textchange_hidded_id_" . $this->element['name'];
			$textchange_source_id  = "textchange_source_id_" . $this->element['name'];
			$textchange_target_id  = "textchange_target_id_" . $this->element['name'];

			if ($textchange_status == '1')
			{
				$textchange_checked = ' checked="checked" ';
			}
			else
			{
				$textchange_checked = '';
			}

			$textchanges_onclick = "javascript:document.id(
							'" . $textchange_hidded_id . "'
							)
							.set(
							'value', document.getElementById('" . $textchange_visible_id . "' ).checked
							);";

			if ($istranslation == '1')
			{
				$title = JText::_('COM_LOCALISE_REVISED');
			}
			else
			{
				$title = 'Grammar case';
			}

			$textchanges_checkbox = '';
			$textchanges_checkbox .= '<div><b>' . $title . '</b><input style="max-width:5%; min-width:5%;" id="';
			$textchanges_checkbox .= $textchange_visible_id;
			$textchanges_checkbox .= '" type="checkbox" ';
			$textchanges_checkbox .= ' name="jform[vtext_changes][]" value="';
			$textchanges_checkbox .= $this->element['name'];
			$textchanges_checkbox .= '" title="' . $title . '" onclick="';
			$textchanges_checkbox .= $textchanges_onclick;
			$textchanges_checkbox .= '" ';
			$textchanges_checkbox .= $textchange_checked;
			$textchanges_checkbox .= '></input></div>';
			$textchanges_checkbox .= '<input id="';
			$textchanges_checkbox .= $textchange_hidded_id;
			$textchanges_checkbox .= '" type="hidden" name="jform[text_changes][';
			$textchanges_checkbox .= $this->element['name'];
			$textchanges_checkbox .= ']" value="';
			$textchanges_checkbox .= $textchange_status;
			$textchanges_checkbox .= '" ></input>';
			$textchanges_checkbox .= '<input id="';
			$textchanges_checkbox .= $textchange_source_id;
			$textchanges_checkbox .= '" type="hidden" name="jform[source_text_changes][';
			$textchanges_checkbox .= $this->element['name'];
			$textchanges_checkbox .= ']" value="';
			$textchanges_checkbox .= htmlspecialchars($textchange_source, ENT_COMPAT, 'UTF-8');
			$textchanges_checkbox .= '" ></input>';
			$textchanges_checkbox .= '<input id="';
			$textchanges_checkbox .= $textchange_target_id;
			$textchanges_checkbox .= '" type="hidden" name="jform[target_text_changes][';
			$textchanges_checkbox .= $this->element['name'];
			$textchanges_checkbox .= ']" value="';
			$textchanges_checkbox .= htmlspecialchars($textchange_target, ENT_COMPAT, 'UTF-8');
			$textchanges_checkbox .= '" ></input>';

			$return = '';

			$return .= '<div><label id="';
			$return .= $this->id;
			$return .= '-lbl" for="';
			$return .= $this->id;
			$return .= '">';
			$return .= $this->element['label'];
			$return .= $textchanges_checkbox;
			$return .= '</label></div>';

			return $return;
		}
		else
		{
			return '<label id="' . $this->id . '-lbl" for="' . $this->id . '">'
						. $this->element['label']
					. '</label>';
		}
	}

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		// Set the class for the label.
		$class         = !empty($this->descText) ? 'key-label hasTooltip fltrt' : 'key-label fltrt';
		$istranslation = (int) $this->element['istranslation'];
		$istextchange  = (int) $this->element['istextchange'];
		$isextraindev  = (int) $this->element['isextraindev'];
		$status        = (string) $this->element['status'];

		$label_id      = $this->id . '-lbl';
		$label_for     = $this->id;
		$textarea_name = $this->name;
		$textarea_id   = $this->id;
		$id            = $this->id;

		if ($istranslation == '1')
		{
			// If a description is specified, use it to build a tooltip.
			if (!empty($this->descText))
			{
				$label = '<label id="' . $label_id . '-lbl" for="' . $label_for . '" class="' . $class . '" title="'
						. htmlspecialchars(htmlspecialchars('::' . str_replace("\n", "\\n", $this->descText), ENT_QUOTES, 'UTF-8')) . '">';
			}
			else
			{
				$label = '<label id="' . $label_id . '-lbl" for="' . $label_for . '" class="' . $class . '">';
			}

			JText::script('COM_LOCALISE_LABEL_TRANSLATION_GOOGLE_ERROR');

			$label .= $this->element['label'] . '<br />' . (string) $this->element['description'];
			$label .= '</label>';

			$onclick = '';
			$button  = '';

			$onclick2 = '';
			$button2  = '';

			if ($status == 'extra')
			{
				$onclick = '';
				$button  = '<span style="width:5%;">'
						. JHtml::_('image', 'com_localise/icon-16-arrow-gray.png', '', array('class' => 'pointer'), true) . '</span>';

				$onclick2 = '';
				$button2  = '<span style="width:5%;">'
						. JHtml::_('image', 'com_localise/icon-16-bing-gray.png', '', array('class' => 'pointer'), true) . '</span>';
				$input  = '';
				$input .= '<textarea name="' . $textarea_name;
				$input .= '" id="' . $textarea_id . '" class="width-45 ' . $status . ' ">';
				$input .= htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
			}
			else
			{
				$token    = JSession::getFormToken();

					$onclick  = "";
					$onclick .= "javascript:document.id('" . $id . "').set('value','";
					$onclick .= addslashes(htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8'));
					$onclick .= "');";
					$onclick .= "document.id('" . $id . "').set('class','width-45 untranslated');";

					$onclick2 = "javascript:AzureTranslator(this, [], 0, '$token');";

				$button   = '';
				$button  .= '<i class="icon-reset hasTooltip return pointer" title="';
				$button  .= JText::_('COM_LOCALISE_TOOLTIP_TRANSLATION_INSERT');
				$button  .= '" onclick="' . $onclick . '"></i>';

				$button2   = '';
				$button2  .= '<input type="hidden" id="' . $id . 'text" value=\'';
				$button2  .= addslashes(htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8')) . '\' />';
				$button2  .= '<i class="icon-translate-bing hasTooltip translate pointer" title="';
				$button2  .= JText::_('COM_LOCALISE_TOOLTIP_TRANSLATION_AZURE');
				$button2  .= '" onclick="' . $onclick2 . '" rel="' . $id . '"></i>';

				$onkeyup = "javascript:";

				if ($istextchange == 1)
				{
					$onkeyup .= "if (this.get('value')=='')
							{
							this.set('class','width-45 untranslated');
							}
							else if (this.get('value')=='"
							. addslashes(htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8'))
							. "')
							{
							this.set('class','width-45 untranslated');
							}
							else if (this.get('value')=='"
							. addslashes(htmlspecialchars($this->element['frozen_task'], ENT_COMPAT, 'UTF-8'))
							. "')
							{
							this.set('class','width-45 untranslated');
							}
							else
							{
							this.set('class','width-45 translated');
							}";
				}
				else
				{
					$onkeyup .= "if (this.get('value')=='')
							{
							this.set('class','width-45 untranslated');
							}
							else if (this.get('value')=='"
							. addslashes(htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8'))
							. "')
							{
							this.set('class','width-45 untranslated');
							}
							else
							{
							this.set('class','width-45 translated');
							}";
				}

				$onfocus = "javascript:this.select();";

				$input  = '';
				$input .= '<textarea name="' . $textarea_name . '" id="' . $textarea_id . '" onfocus="' . $onfocus;
				$input .= '" class="width-45 ' . $status . '" onkeyup="';
				$input .= $onkeyup . '">' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
			}
		}
		else
		{
			// Set the class for the label.
			$class = !empty($this->descText) ? 'key-label hasTooltip fltrt' : 'key-label fltrt';

			// If a description is specified, use it to build a tooltip.
			if (!empty($this->descText))
			{
				$label = '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '" title="'
						. htmlspecialchars(htmlspecialchars('::' . str_replace("\n", "\\n", $this->descText), ENT_QUOTES, 'UTF-8')) . '">';
			}
			else
			{
				$label = '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '">';
			}

			JText::script('COM_LOCALISE_LABEL_TRANSLATION_GOOGLE_ERROR');
			$label .= $this->element['label'] . '<br />' . $this->element['description'];
			$label .= '</label>';

			// Adjusting the stuff when all them are reference keys.
			$readonly = '';
			$textvalue = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');

			if ($istextchange == 1 || $isextraindev == 1)
			{
				// There is no translation task in develop for the reference files in develop.
				$readonly = ' readonly="readonly" ';
				$textvalue = htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8');
			}

			$status = (string) $this->element['status'];

			$onclick = "javascript:document.id(
							'" . $this->id . "'
							)
							.set(
							'value','" . addslashes(htmlspecialchars($this->element['description'], ENT_COMPAT, 'UTF-8')) . "'
							);
							if (document.id('" . $this->id . "').get('value')=='') {document.id('" . $this->id . "').set('class','width-45 untranslated');}
							else {document.id('" . $this->id . "').set('class','width-45 " . $status . "');}";
			$button  = '<i class="icon-reset hasTooltip return pointer"
			 title="' . JText::_('COM_LOCALISE_TOOLTIP_TRANSLATION_INSERT') . '"
			 onclick="' . $onclick . '"></i>';

			// No sense translate the reference keys by the same language.
			$onclick2 = '';
			$button2  = '<span style="width:5%;">'
						. JHtml::_('image', 'com_localise/icon-16-bing-gray.png', '', array('class' => 'pointer'), true) . '</span>';

			if ($istextchange == 1 || $isextraindev == 1)
			{
				// Is read only, so no changes.
				$onkeyup = "";
			}
			else
			{
				$onkeyup = "javascript:";
				$onkeyup .= "if (this.get('value')=='') {this.set('class','width-45 untranslated');}
							else {if (this.get('value')=='" . addslashes(htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'))
							. "') this.set('class','width-45 " . $status . "');
							" . "else this.set('class','width-45 translated');}";
			}

			$input  = '';
			$input .= '<textarea name="' . $this->name . '" id="';
			$input .= $this->id . '"' . $readonly . ' onfocus="this.select()" class="width-45 ';
			$input .= $status;
			$input .= '" onkeyup="' . $onkeyup . '">' . $textvalue;
			$input .= '</textarea>';
		}

		return $button . $button2 . $input;
	}
}
