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
 * View class for download a package.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseViewDownloadPackage extends JViewLegacy
{
	protected $form;
	protected $item;

	/**
	 * Display the view
	 */
	public function display($tpl = null) 
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
    
		parent::display($tpl);
	}
}
