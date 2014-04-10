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
 * Package View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 */
class LocaliseViewPackage extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
 	 * Display the view
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

		// Prepare the document
		$this->prepareDocument();

		// Display the view
		parent::display($tpl);
	}

	protected function addToolbar() 
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user       = JFactory::getUser();
		$isNew      = empty($this->item->id);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolbarHelper::title(JText::sprintf('COM_LOCALISE_HEADER_MANAGER', $isNew ? JText::_('COM_LOCALISE_HEADER_PACKAGE_NEW') : JText::_('COM_LOCALISE_HEADER_PACKAGE_EDIT')), 'langmanager.png');

		// If not checked out, can save the item.
		if (!$checkedOut /*&& $this->item->standalone*/) 
		{
			JToolbarHelper::apply('package.apply');
			JToolbarHelper::save('package.save');
		}

		JToolBarHelper::cancel("package.cancel", $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.package', true);
	}

	protected function prepareDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::sprintf('COM_LOCALISE_TITLE', JText::_('COM_LOCALISE_TITLE_PACKAGE')));
	}
}
