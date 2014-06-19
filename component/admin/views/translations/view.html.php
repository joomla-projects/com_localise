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
 * Translations View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewTranslations extends JViewLegacy
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
	function display($tpl = null)
	{
		// Get the data
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->form       = $this->get('Form');
		$this->packages   = $this->get('Items', 'Packages');

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

		JToolbarHelper::title(JText::sprintf('COM_LOCALISE_HEADER_MANAGER', JText::_('COM_LOCALISE_HEADER_TRANSLATIONS')), 'comments-2 langmanager');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_localise');
			JToolbarHelper::divider();
		}

		JToolBarHelper::help('screen.translations', true);
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
			'filename'   => JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_NAME'),
			'tag'        => JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_TAG'),
			'path'       => JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_PATH'),
			'completed'  => JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_TRANSLATED'),
			'translated' => JText::_('COM_LOCALISE_HEADING_TRANSLATIONS_PHRASES'),
		);
	}
}
