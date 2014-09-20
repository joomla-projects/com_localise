<?php
/**
 * @package     Joomla
 * @subpackage  Page Class
 * @copyright   Copyright (C) 2012 - 2014 All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace AcceptanceTester;
/**
 * Class InstallJoomla3Steps
 *
 * @package  AcceptanceTester
 *
 * @link     http://codeception.com/docs/07-AdvancedUsage#StepObjects
 *
 * @since    1.4
 */
class InstallJoomla3Steps extends \AcceptanceTester
{
	/**
	 * @var AcceptanceTester;
	 */
	protected $acceptanceTester;

	/**
	 * Function to InstallJoomla3
	 *
	 * @return void
	 */
	public function installJoomla3()
	{
		$I = $this;
		$this->acceptanceTester = $I;
		$I->amOnPage(\InstallJoomla3ManagerPage::$URL);
		$cfg = $I->getConfig();
		$this->setField('Site Name', $cfg['site_name']);
		$this->setField('Your Email', $cfg['admin_email']);
		$this->setField('Admin Username', $cfg['username']);
		$this->setField('Admin Password', $cfg['password']);
		$this->setField('Confirm Admin Password', $cfg['password']);
		$I->click("//li[@id='database']/a");
		$I->waitForElement("//li[@id='database'][@class='step active']", 30);

		$this->setDatabaseType($cfg['db_type']);
		$this->setField('Host Name', $cfg['db_host']);
		$this->setField('Username', $cfg['db_user']);
		$this->setField('Password', $cfg['db_pass']);
		$this->setField('Database Name', $cfg['db_name']);
		$this->setField('Table Prefix', $cfg['db_prefix']);

		$I->click("//label[@for='jform_db_old1']");

		$I->click("//li[@id='summary']/a");

		if (strtolower($cfg['sample_data']) == "yes")
		{
			$this->setSampleData($cfg['sample_data_file']);
		}
		else
		{
			$this->setSampleData('None');
		}

		$I->click("//a[@title='Install']");
		$I->waitForElement("//input[contains(@onclick, 'Install.removeFolder')]", 60);
	}

	/**
	 * Function to Populate Values
	 *
	 * @param   String  $label  Label of the Field
	 * @param   String  $value  Value of the Field
	 *
	 * @return void
	 */
	private function setField($label, $value)
	{
		$I = $this->acceptanceTester;

		switch ($label)
		{
			case 'Host Name':
				$id = \InstallJoomla3ManagerPage::$dbHost;
				break;
			case 'Username':
				$id = \InstallJoomla3ManagerPage::$dbUsername;
				break;
			case 'Password':
				$id = \InstallJoomla3ManagerPage::$dbPassword;
				break;
			case 'Database Name':
				$id = \InstallJoomla3ManagerPage::$dbName;
				break;
			case 'Table Prefix':
				$id = \InstallJoomla3ManagerPage::$dbPrefix;
				break;
			case 'Site Name':
				$id = \InstallJoomla3ManagerPage::$siteName;
				break;
			case 'Your Email':
				$id = \InstallJoomla3ManagerPage::$adminEmail;
				break;
			case 'Admin Username':
				$id = \InstallJoomla3ManagerPage::$adminUser;
				break;
			case 'Admin Password':
				$id = \InstallJoomla3ManagerPage::$adminPassword;
				break;
			case 'Confirm Admin Password':
				$id = \InstallJoomla3ManagerPage::$adminPasswordConfirm;
				break;
		}

		$I->fillField($id, $value);
	}

	/**
	 * Function to set the Database Type
	 *
	 * @param   String  $value  Value of DB
	 *
	 * @return void
	 */
	private function setDatabaseType($value)
	{
		$I = $this->acceptanceTester;
		$I->click("//div[@id='jform_db_type_chzn']/a/div/b");
		$I->click("//div[@id='jform_db_type_chzn']//ul[@class='chzn-results']/li[contains(translate(.,'" . strtoupper($value) . "', '" . strtolower($value) . "'), '" . strtolower($value) . "')]");
	}

	/**
	 * Function to set Sample Data for Installation
	 *
	 * @param   string  $option  Option Value
	 *
	 * @return void
	 */
	private function setSampleData($option = 'Default')
	{
		$I = $this->acceptanceTester;
		$I->waitForElement("//label[contains(., '" . $option . "')]", 60);
		$I->click("//label[contains(., '" . $option . "')]");
	}
}
