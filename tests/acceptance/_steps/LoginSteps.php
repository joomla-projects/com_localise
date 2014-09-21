<?php
/**
 * @package     Joomla
 * @subpackage  Step Class
 * @copyright   Copyright (C) 2012 - 2014 All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace AcceptanceTester;
/**
 * Class LoginSteps
 *
 * @package  AcceptanceTester
 *
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage#StepObjects
 *
 * @since    1.0
 */
class LoginSteps extends \AcceptanceTester
{
	/**
	 * Function to execute an Admin Login for Joomla3
	 *
	 * @return void
	 */
	public function doAdminLogin()
	{
		$I = $this;
		$this->acceptanceTester = $I;
		$I->amOnPage(\LoginManagerPage::$URL);
		$config = $I->getConfig();
		$I->fillField(\LoginManagerPage::$userName, $config['username']);
		$I->fillField(\LoginManagerPage::$password, $config['password']);
		$I->click('Log in');
		$I->see('Control Panel');
	}
}
