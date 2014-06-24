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
		//$this->form = $this->get('Form');
		//$this->pagination = $this->get('Pagination');
		//$this->item = $this->form->getData()->toArray();

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
		JHtml::_('bootstrap.modal', 'collapseModal');

		$bar = JToolBar::getInstance('toolbar');

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');

		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_LOCALISE_HEADER_LOAD'));

		// Add planning button
		//JToolbarHelper::addNew('planning.add');
		//JToolbarHelper::deleteList('planning.delete');

		//JToolbarHelper::apply('patient.apply');
		//JToolBarHelper::save('patient.save');

		// Add buttons for evaluations
		JToolBarHelper::custom('process', 'chart', '', JText::_('COM_LOCALISE_BUTTON_LOAD'), false );
		//JToolBarHelper::custom('modal-add-practical', 'bars', '', JText::_('COM_METS_EVALUATIONS_ADD_PRACTICAL'), false );

		// Cancel button
		//JToolBarHelper::cancel('patient.cancel', $isNew ? 'JTOOLBAR_CANCEL'
		//		                                               : 'JTOOLBAR_CLOSE');
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
