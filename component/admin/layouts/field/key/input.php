<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * ------------------
 * @param   boolean           $autofocus             Autofocus on this field
 * @param   string            $class                 CSS class to apply
 * @param   boolean           $debug                 Is debug enabled for this field?
 * @param   string            $description           Description text of the field
 * @param   boolean           $disabled              Does this field need to be disabled?
 * @param   SimpleXMLElement  $element               The object of the <field /> XML element that describes the form field.
 * @param   JFormField        $field                 Object to access to the field properties
 * @param   boolean           $hidden                Hidden attribute of the field
 * @param   boolean           $hiddenLabel           Do we want to hide label?
 * @param   string            $hint                  Field hint
 * @param   string            $id                    DOM id of the element
 * @param   string            $label                 Label text of the field
 * @param   boolean           $multiple              Allow to enter multiple values?
 * @param   string            $name                  Name of the field to display
 * @param   string            $onchange              onchange attribute of the field
 * @param   string            $onclick               onclick attribute of the field
 * @param   boolean           $readonly              Do not allow to modify field value
 * @param   boolean           $required              Is this field required?
 * @param   integer           $size                  Size for the input element
 * @param   mixed             $value                 Value of the field
 *
 */

JHtml::_('jquery.framework');
JHtml::_('script', 'media/com_localise/js/field-key.js', false, false, false, false);

$attributes = array();

$status = (string) $element['status'];
$statusClass = ($value == '' ? 'untranslated' : ($value == $element['description'] ? $status : 'translated'));

// Manually handled attributes
$attributes['data-status']   = $statusClass;
$attributes['class']         = trim($class . ' js-localise-field-translation width-45 ' . $statusClass);
$attributes['id']            = $id;
$attributes['name']          = $name;
$attributes['readonly']      = $readonly ? 'readonly' : null;
$attributes['disabled']      = $disabled ? 'disabled' : null;
$attributes['required']      = $required ? 'required' : null;
$attributes['aria-required'] = $required ? 'true' : null;
$attributes['onchange']      = $onchange ? (string) $onchange : null;
$attributes['placeholder']   = $hint;

// Clean null attributes
foreach ($attributes as $attributeName => $attributeValue)
{
	if (null === $attributeValue)
	{
		unset($attributes[$attributeName]);
	}
}

$safeValue     = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
$safeReference = htmlspecialchars($element['description'], ENT_COMPAT, 'UTF-8');
?>

<?php if ($status != 'extra') : ?>
	<i class="icon-reset hasTooltip pointer js-localise-btn-import" data-import="<?php echo $id; ?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_TRANSLATION_INSERT'); ?>"></i>
	<i class="icon-translate-bing hasTooltip pointer js-localise-btn-translate" data-translate="<?php echo $id; ?>" data-token="<?php echo JSession::getFormToken(); ?>" title="<?php echo JText::_('COM_LOCALISE_TOOLTIP_TRANSLATION_AZURE'); ?>" ></i>
	<textarea id="<?php echo $id; ?>-reference" style="display: none;"><?php echo $safeReference; ?></textarea>
	<textarea id="<?php echo $id; ?>-original" style="display: none;"><?php echo $safeValue; ?></textarea>
<?php endif; ?>
<textarea <?php echo JArrayHelper::toString($attributes); ?> ><?php echo $safeValue; ?></textarea>
