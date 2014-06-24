<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Package View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewLoad extends JViewLegacy
{
	/**
	* display method of Patient view
	* @return void
	*/
	public function display($tpl = null) 
	{
		// Assign the Data
		$this->state 	= $this->get('State');

		LocaliseHelper::addSubmenu('translations');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("<br />", $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the template
		parent::display($tpl);
	}

	/**
	* Setting the toolbar
	*/
	protected function addToolBar() 
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_LOCALISE_HEADER_LOAD'));

		// Add buttons
		JToolBarHelper::custom('process', 'chart', '', JText::_('COM_LOCALISE_BUTTON_LOAD'), false );
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since 3.0
	 */
	protected function getSortFields()
	{
		return array();
	}
}
