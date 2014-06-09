<?php
/**
 * @package     Com_Localise
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller class for the localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/localise.php';

		$vName = $this->input->get('view', 'languages');

		if ($vName == 'translations')
		{
			$view     = $this->getView('translations', 'html');
			$packages = $this->getModel('Packages', 'LocaliseModel', array('ignore_request' => true));
			$view->setModel($packages);
		}
		else
		{
			$this->input->set('view', $vName);
		}

		parent::display($cachable, $urlparams);
	}
}
