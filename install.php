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
class Com_LocaliseInstallerScript extends JInstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  4.0.32
	 */
	protected $extension = 'com_localise';
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  4.0.32
	 */
	protected $minimumPhp = '5.3.10';
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  4.0.32
	 */
	protected $minimumJoomla = '3.8.0';
}
