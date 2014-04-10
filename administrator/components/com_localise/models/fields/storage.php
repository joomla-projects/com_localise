<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author    Mohammad Hasani Eghtedar <m.h.eghtedar@gmail.com>
# copyright Copyright (C) 2012 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.form.formfield');

/**
 * Form Field Place class.
 *
 * @package    Extensions.Components
 * @subpackage  Localise
 */
class JFormFieldStorage extends JFormField
{
  /**
   * The field type.
   *
   * @var    string
   */
  protected $type = 'Storage';

  /**
   * Method to get the field input.
   *
   * @return  string    The field input.
   */
  protected function getInput() 
  {
    $attributes = '';
    if ($v = (string)$this->element['onchange']) 
    {
      $attributes.= ' onchange="' . $v . '"';
    }
    $attributes.= ' class="'.(string) $this->element['class'].' iconlist-16-' . $this->value . '"';
    $options = array();
    foreach ($this->element->children() as $option) 
    {
      $options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
    }
    $options[] = JHtml::_('select.option', 'global', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_GLOBAL'), array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-global"'));
    $options[] = JHtml::_('select.option', 'local', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STORAGE_LOCAL'), array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-local"'));
    $return = JHtml::_('select.genericlist', $options, $this->name, array('id' => $this->id, 'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes));
    return $return;
  }
}
