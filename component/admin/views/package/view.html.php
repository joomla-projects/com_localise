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
 * Package View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewPackage extends JViewLegacy
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
		$app           = JFactory::getApplication();
		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->form    = $this->get('Form');
		$this->formftp = $this->get('FormFtp');
		$this->ftp     = JClientHelper::setCredentialsFromRequest('ftp');
		$this->file    = $app->input->get('file');
		$this->fileName = base64_decode($this->file);
		$this->location	= $app->input->get('location');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Set the toolbar
		$this->addToolbar();

		// Prepare the document
		$this->prepareDocument();

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

		$user		= JFactory::getUser();
		$canDo		= JHelperContent::getActions('com_localise', 'component');
		$isNew		= empty($this->item->id);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolbarHelper::title(
			JText::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				$isNew ? JText::_('COM_LOCALISE_HEADER_PACKAGE_NEW') : JText::_('COM_LOCALISE_HEADER_PACKAGE_EDIT')
			),
			'comments-2 langmanager'
		);

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			JToolbarHelper::apply('package.apply');
			JToolbarHelper::save('package.save');
		}

		if (!$isNew && $canDo->get('localise.create'))
		{
			JToolBarHelper::divider();
			JToolbarHelper::modal('fileModal', 'icon-upload', 'COM_LOCALISE_BUTTON_IMPORT_FILE');
			JToolBarHelper::divider();
		}

		JToolbarHelper::custom('package.download', 'out.png', 'out.png', 'COM_LOCALISE_TOOLBAR_PACKAGE_DOWNLOAD', false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel("package.cancel", $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.package', true);
	}

	/**
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::sprintf('COM_LOCALISE_TITLE', JText::_('COM_LOCALISE_TITLE_PACKAGE')));
	}
}
