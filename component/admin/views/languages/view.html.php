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
 * Languages View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewLanguages extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $form;

	protected $minCmsVersion = '3.8';

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
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->form       = $this->get('Form');

		if (version_compare(JVERSION, $this->minCmsVersion, 'lt'))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_LOCALISE_ERROR_INSTALL_JVERSION', $this->minCmsVersion), 'warning');

			return false;
		}

		LocaliseHelper::addSubmenu('languages');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Display the view
		parent::display($tpl);
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

		JToolbarHelper::title(JText::sprintf('COM_LOCALISE_HEADER_MANAGER', JText::_('COM_LOCALISE_HEADER_LANGUAGES')), 'comments-2 langmanager');

		if ($canDo->get('localise.create'))
		{
			JToolbarHelper::addNew('language.add');
			JToolbarHelper::custom('languages.purge', 'purge', 'purge', 'COM_LOCALISE_PURGE', false, false);
			JToolbarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_localise');
			JToolbarHelper::divider();
		}

		JToolBarHelper::help('screen.languages', true);

		JHtmlSidebar::setAction('index.php?option=com_localise&view=languages');
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
			'name'   => JText::_('COM_LOCALISE_HEADING_LANGUAGES_NAME'),
			'tag'    => JText::_('COM_LOCALISE_HEADING_LANGUAGES_TAG'),
			'client' => JText::_('COM_LOCALISE_HEADING_LANGUAGES_CLIENT'),
		);
	}
}
