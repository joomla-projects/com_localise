<?php
/**
 * @package     Joomla
 * @subpackage  Page Class
 * @copyright   Copyright (C) 2012 - 2014  All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class ExtensionManagerPage
 *
 * @link   http://codeception.com/docs/07-AdvancedUsage#PageObjects
 *
 * @since  1.4
 *
 */
class ExtensionManagerPage
{
	// Include url of current page
	public static $URL = '/administrator/index.php?option=com_installer';

	public static $extensionDirectoryPath = "#install_directory";

	public static $installButton = "//input[contains(@onclick,'Joomla.submitbutton3()')]";

	public static $installSuccessMessage = "//p[contains(text(),'Installing component was successful')]";

	public static $extensionSearch = "//input[@id='filter_search']";

	public static $checkAll = "//input[@onclick='Joomla.checkAll(this)']";

	public static $firstCheck = "//input[@id='cb0']";

	public static $extensionNameLink = "//a[contains(text(),'Name')]";

	public static $extensionTable = "//form[@id='adminForm']//div[@id='j-main-container']//table/tbody/tr[1]/td[2]//span";

	public static $uninstallSuccessMessage = "//p[contains(text(),'successful')]";

	public static $uninstallComponentSuccessMessage = "//p[contains(text(),'Uninstalling component was successful')]";

	public static $noExtensionMessage = "//p[contains(text(),'There are no extensions installed matching your query')]";

	public static $searchResultSpan = "//form[@id='adminForm']/div/table/tbody/tr[1]/td[2]/span";

	public static $searchButton = "//button[@type='submit' and @data-original-title='Search']";
}
