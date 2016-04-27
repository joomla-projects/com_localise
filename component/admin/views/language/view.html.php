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
 * View to edit a language.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewLanguage extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		jimport('joomla.client.helper');

		// Get the data
		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->form    = $this->get('Form');
		$this->formftp = $this->get('FormFtp');
		$this->ftp     = JClientHelper::setCredentialsFromRequest('ftp');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolbar();

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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$canDo = JHelperContent::getActions('com_localise', 'component');

		$user       = JFactory::getUser();
		$isNew      = empty($this->item->id);
		$client     = $this->item->client;
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolbarHelper::title(
			JText::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				$isNew ? JText::_('COM_LOCALISE_HEADER_LANGUAGE_NEW') : JText::_('COM_LOCALISE_HEADER_LANGUAGE_EDIT')
			),
			'icon-comments-2 langmanager'
		);

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			JToolbarHelper::apply('language.apply');
			JToolbarHelper::save('language.save');
		}

		JToolBarHelper::cancel('language.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();

		if ($canDo->get('localise.create') && !$isNew && $client != 'installation')
		{
			JToolbarHelper::custom('language.copy', 'copy.png', 'copy.png', 'COM_LOCALISE_COPY_REF_TO_NEW_LANG', false);
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('screen.language', true);
	}
}
