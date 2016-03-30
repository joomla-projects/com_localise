<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Packages View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewPackagefiles extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $form;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the data
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->form = $this->get('Form');

		LocaliseHelper::addSubmenu('packages');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Prepare the document
		$this->prepareDocument();

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::sprintf('COM_LOCALISE_TITLE', JText::_('COM_LOCALISE_TITLE_PACKAGES')));
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_localise', 'component');

		JToolBarHelper::title(JText::sprintf('COM_LOCALISE_HEADER_MANAGER', JText::_('COM_LOCALISE_HEADER_PACKAGES')), 'install');

		if ($canDo->get('localise.create'))
		{
			JToolbarHelper::addNew('package.add');
		}

		if ($canDo->get('localise.create'))
		{
			JToolbarHelper::addNew('packagefile.add', 'COM_LOCALISE_NEW_FILE_PACKAGE');
		}

		if ($canDo->get('localise.edit'))
		{
			JToolbarHelper::editList('package.edit');
		}

		if ($canDo->get('localise.create') || $canDo->get('localise.edit'))
		{
			JToolbarHelper::divider();
		}

		if ($canDo->get('localise.delete'))
		{
			JToolbarHelper::deleteList('COM_LOCALISE_MSG_PACKAGES_VALID_DELETE', 'packages.delete');
			JToolBarHelper::divider();
		}

		JToolBarHelper::custom('package.download', 'out.png', 'out.png', 'JTOOLBAR_EXPORT', true);
		JToolBarHelper::divider();

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_localise');
			JToolbarHelper::divider();
		}

		JToolBarHelper::help('screen.packages', true);
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
		return array(
			'title' => JText::_('COM_LOCALISE_HEADING_PACKAGES_TITLE'),
		);
	}
}
