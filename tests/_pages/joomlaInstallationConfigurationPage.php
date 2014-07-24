<?php

class joomlaInstallationConfigurationPage
{
    // include url of current page
    public static $URL = 'tests/system/joomla-cms/installation/index.php';
    public static $siteName ='#jform_site_name';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
     public static function route($param)
     {
        return static::$URL.$param;
     }


}