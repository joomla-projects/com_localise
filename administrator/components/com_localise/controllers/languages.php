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
 * Languages Controller class for the Localise component
 *
 * @package    Extensions.Components
 * @subpackage Localise
 */
class LocaliseControllerLanguages extends JControllerLegacy
{
	/**
	 * Proxy for getModel.
	 */

	public function &getModel($name = 'Languages', $prefix = 'LocaliseModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
