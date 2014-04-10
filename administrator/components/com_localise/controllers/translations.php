<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Translations Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseControllerTranslations extends JControllerLegacy
{
	/*public function display()
	{
		$translations = & $this->getModel('translations');
		$view = & $this->getView('translations', 'html');

		if (!JFile::exists(LocaliseHelper::getPathMeta($translations->getClient(), $translations->getTag())))
		{
			$view->setLayout('error');
			$app = & JFactory::getApplication('administrator');
			$app->enqueueMessage(JText::sprintf('COM_LOCALISE_THE_LANGUAGE_ISO_TAG_DOES_NOT_EXIST_IN_THIS_CLIENT', $translations->getTag()), 'notice');
		}

		$view->setModel($translations, true);
		$view->display();
	}

	public function setRefTag()
	{
		$translations = & $this->getModel('translations');

		if ($translations->setRefTag())
		{
			$msg = JText::_('COM_LOCALISE_REFERENCE_LANGUAGE_CHANGED');
			$type = 'message';
		}
		else
		{
			$msg = JText::sprintf('COM_LOCALISE_ERROR_CHANGING_REFERENCE_LANGUAGE', $translations->getError());
			$type = 'error';
		}

		$url = "index.php?";
		$url.= "&option=com_localise";
		$url.= "&task=translations.display";
		$url.= "&client=" . $translations->getState('client');
		$url.= "&tag=" . $translations->getState('tag');
		$this->setRedirect($url, $msg, $type);
	}*/
}
