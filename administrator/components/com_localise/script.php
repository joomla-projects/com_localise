<?php
/**
 * @package     Com_Localise
 * @subpackage  com_localise.script
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package  Localise
 * @since    4.0
 */
class Com_LocaliseInstallerScript
{
	/**
	 * Minimum supported version of the CMS
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $minCmsVersion = '3.3';

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string               $type    The action being performed
	 * @param   JInstallerComponent  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.0
	 */
	public function preflight($type, $parent)
	{
		if (version_compare(JVERSION, $this->minCmsVersion, 'lt'))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_LOCALISE_ERROR_INSTALL_JVERSION', $this->minCmsVersion));

			return false;
		}

		return true;
	}
}
