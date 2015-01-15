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
 * Translation View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LocaliseViewTranslation extends JViewLegacy
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

		$user		= JFactory::getUser();
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		if ($this->state->get('translation.filename') == 'joomla')
		{
			$filename = $this->state->get('translation.tag') . '.ini';
		}
		else
		{
			$filename = $this->state->get('translation.tag') . '.' . $this->state->get('translation.filename') . '.ini';
		}

		JToolbarHelper::title(
			JText::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				JText::sprintf($this->item->exists ? 'COM_LOCALISE_HEADER_TRANSLATION_EDIT' : 'COM_LOCALISE_HEADER_TRANSLATION_NEW', $filename)
			),
			'comments-2 langmanager'
		);

		if (!$checkedOut)
		{
			JToolbarHelper::apply('translation.apply');
			JToolbarHelper::save('translation.save');
		}

		JToolbarHelper::cancel('translation.cancel');
		JToolBarHelper::divider();
		JToolBarHelper::help('screen.translation', true);
	}
}
