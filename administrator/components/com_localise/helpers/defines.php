<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Define constant
$params = JComponentHelper::getParams('com_localise');
define('LOCALISEPATH_SITE', JPATH_SITE);
define('LOCALISEPATH_ADMINISTRATOR', JPATH_ADMINISTRATOR);
define('LOCALISEPATH_INSTALLATION', JPATH_ROOT . '/' . $params->get('installation', 'installation'));
