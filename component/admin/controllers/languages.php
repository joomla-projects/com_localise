<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Languages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class LocaliseControllerLanguages extends JControllerLegacy
{
	/**
	 * Method to purge the localise table.
	 *
	 * @return  void
	 *
	 * @since   3.3
	 */
	public function purge()
	{
		$model = $this->getModel('languages');
		$model->purge();
		$this->setRedirect(JRoute::_('index.php?option=com_localise&view=languages', false));
	}
}
