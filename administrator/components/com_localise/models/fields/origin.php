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
    if ($v = (string)$this->element['onchange']) 
    {
      $attributes.= ' onchange="' . $v . '"';
    }
    if ($this->value == '_thirdparty') 
    {
      $attributes.= ' class="'.(string) $this->element['class'].' iconlist-16-thirdparty"';
    }
    elseif ($this->value == '_override') 
    {
      $attributes.= ' class="'.(string) $this->element['class'].' iconlist-16-override"';
    }
    else
    {
      $attributes.= ' class="'.(string) $this->element['class'].'"';
    }
    $options = array();
    foreach ($this->element->children() as $option) 
    {
      $options[] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
    }
    $packages = LocaliseHelper::getPackages();
    $packages_options = array();
    foreach ($packages as $package) 
    {
      $packages_options[] = JHtml::_('select.option', $package->name, JText::_($package->title), array('option.attr' => 'attributes', 'attr' => 'class="localise-icon" style="background-image: url(' . JURI::root(true) . $package->icon . ');"'));
      if ($this->value == $package->name) 
      {
        $attributes.= ' style="background-image: url(' . JURI::root(true) . $package->icon . ');"';
      }
    }
    $packages_options = JArrayHelper::sortObjects($packages_options, 'text');
    $thirdparty = JHtml::_('select.option', '_thirdparty', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_THIRDPARTY'), array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-thirdparty"'));
    $override = JHtml::_('select.option', '_override', JText::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_ORIGIN_OVERRIDE'), array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-override"'));
    $return = JHtml::_('select.genericlist', array_merge($options, $packages_options, array($thirdparty), array($override)), $this->name, array('id' => $this->id, 'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes, 'group.items' => null));
    return $return;
  }
}
