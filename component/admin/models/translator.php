<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The Translator Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @since       1.6
 */
class LocaliseModelTranslator extends JModelLegacy
{
	/**
	 * todo: add function description
	 *
	 * @return string
	 */
	public function getText()
	{
		$params   = JComponentHelper::getParams('com_localise');
		$clientID = $params->get('clientID');
		$secret   = $params->get('client_secret');

		if (empty($clientID) || empty($secret))
		{
			$this->setError(JText::_('COM_LOCALISE_MISSING_CLIENTID_SECRET'));

			return '';
		}

		$app  = JFactory::getApplication();
		$text = $app->input->getHtml('text');

		if (empty($text))
		{
			$this->setError(JText::_('COM_LOCALISE_MISSING_TEXT'));

			return '';
		}

		$to = $app->input->getCmd('to');

		if (empty($to))
		{
			$this->setError(JText::_('COM_LOCALISE_MISSING_TO_LANGUAGECODE'));

			return '';
		}

		$from = $app->input->getCmd('from');

		class_exists('HTTPTranslator')
		or require dirname(__DIR__) . '/helpers/azuretranslator.php';

		$translator = new HTTPTranslator;

		return $translator->translate($clientID, $secret, $to, $text, $from);
	}
}
