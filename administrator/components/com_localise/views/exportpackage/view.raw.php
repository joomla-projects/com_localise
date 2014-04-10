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
 * Export Package View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseViewExportPackage extends JViewLegacy
{
	protected $item;

	/**
	 * Display the view
	 */
	public function display($tpl = null) 
	{
		$item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/zip');
		JResponse::setHeader('Content-disposition', 'attachment; filename="' . $item->filename . '.zip"; creation-date="' . JFactory::getDate()->toRFC822() . '"', true);
		echo $item->contents;
	}
}
