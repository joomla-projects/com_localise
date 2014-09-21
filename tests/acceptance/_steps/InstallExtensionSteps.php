<?php
/**
 * @package     Joomla
 * @subpackage  Step Class
 * @copyright   Copyright (C) 2012 - 2014 All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace AcceptanceTester;

/**
 * Class InstallExtensionSteps
 *
 * @package  AcceptanceTester
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage#StepObjects
 *
 * @since    1.4
 */
class InstallExtensionSteps extends \AcceptanceTester
{
	/**
	 * Function to Install RedShop1, inside Joomla 3
	 *
	 * @return void
	 */
	public function installExtension()
	{
		$I = $this;
		$this->acceptanceTester = $I;
		$I->amOnPage(\ExtensionManagerPage::$URL);
		$config = $I->getConfig();
		$I->click('Install from Directory');
		$I->fillField(\ExtensionManagerPage::$extensionDirectoryPath, $config['folder']);
		$I->click(\ExtensionManagerPage::$installButton);
		$I->waitForElement(\ExtensionManagerPage::$installSuccessMessage, 60);
		$I->seeElement(\ExtensionManagerPage::$installSuccessMessage);
	}
}
