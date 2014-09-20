Localise
========

This component was originally created by:

Yoshiki Kozaki, Mohammad Hasani Eghtedar,Christophe Demko, Jean-Marie Simonet and Ifan Evans.

We (the Joomla! Internationalisation Working Group) are forking it for experimental purposes.

If you want to join the effort please contact us!

# Travis Status
Master: [![Build Status](https://api.travis-ci.org/joomla-projects/com_localise.svg?branch=master)](https://travis-ci.org/joomla-projects/com_localise)

Develop: [![Build Status](https://travis-ci.org/joomla-projects/com_localise.svg?branch=develop)](https://travis-ci.org/joomla-projects/com_localise)

# The goal
With the new com_localise we are trying to solve the following needs:

* the tool should help to automate the releases of language packages and sends them to the download page, language update servers...
* the tool should help to warn translators (maybe e-mail them) when new strings when the main en-GB files get changed in the blessed repository
* the tool could be flexible enough that allows translators to work with their preferred translation tool: com_localise, transifex, crowdin...
* the tool could be Git based, but on it's base, not in it's interface (because translators are not necessarily developers)
* and maybe, the tool could become something that can be used too by 3rd party extension developers to want to get support from the Joomla community translating their open source solutions. Something like JED, maybe?

# actors
The following image details the actors and use cases of the application:

![image](https://raw.githubusercontent.com/joomla-projects/translate-joomla/master/images/structure/actors.png)



# Todo's (Tasks)
* Code Style tasks and refactoring: https://github.com/joomla-projects/com_localise/issues?milestone=1&state=open
* Fix bugs: https://github.com/joomla-projects/com_localise/issues?milestone=2&state=open
* System testing tasks: https://github.com/joomla-projects/com_localise/issues?milestone=4&state=open
* New features tasks: https://github.com/joomla-projects/com_localise/issues?milestone=3&state=open

# Tests

## System Tests
See testing documentation for the system tests at [tests/system/readme.md](./tests/system/readme.md)


## PHP_CodeSniffer
All PHP files except for layout files (located in a `/tmpl` directory) should be formatted to follow the [Joomla! Coding Standards](http://joomla.github.io/coding-standards/).  These are validated by using PHP_CodeSniffer.  You can run the PHP_CodeSniffer in one of the following manners:

* Using Ant:
    * From the command line, you can use Ant by running `ant -f .travis.xml` from the repository root
* Using Phing:
    * From the command line, you can use Phing by running `phing -f .travis.xml` from the repository root
* PHP Script:
    * From the command line, you can run a custom PHP script by running `php .travis/phpcs.php` from the repository root
    * To use this script, you must have [Composer](https://getcomposer.org/) installed on your system and must run the `composer install` command from the repository root
    * This is the script utilized by Travis-CI

# Requirements
Joomla 3.3 or above is needed to run this component.

# Extension packager
There are two available ways to package the extension a PHING packager xml file (requires PHING) and a *nix shell script:

## Phing packager

A PHING build file can be found at build/build.xml. After executing it you will have an installable .zip file under build/packages.

## Shell script for *nix systems

To package, on a *nix system, navigate to the build folder and run ./build.sh. When complete, you will have an installable .zip file under build/packages.

# Testing with Codeception

Get codeception phar:

```
wget http://codeception.com/codecept.phar .
```

Run the BootStrap Command:

```
php ./codecept.phar boostrap
```

Build codeception testers classes:

```
php ./codecept.phar build
```

Rename tests/acceptance.suite.dist.yml to tests/acceptance.suite.yml

Modify the configuration at tests/acceptance.suite.yml to fit your server details. Find the instructions in the same file: https://github.com/redCOMPONENT-COM/redSHOP/blob/develop/tests/acceptance.suite.dist.yml#L3

Run Selenium server:

```
# Download
curl -O http://selenium-release.storage.googleapis.com/2.41/selenium-server-standalone-2.41.0.jar

# And start the Selenium Server
java -Xms40m -Xmx256m -jar /Applications/XAMPP/xamppfiles/htdocs/selenium/selenium-server-standalone-2.41.0.jar
```


Execute the tests:

```
php codecept.phar run

; Or with --steps to see a step-by-step report on the performed actions.
php codecept.phar run --steps

; Or with --html. This command will run all tests for all suites, displaying the steps, and building HTML and XML reports. Reports will be store in tests/_output/ directory.
php codecept.phar run --html
```

## Firefox Addons
To generate tests really fast you can use these firefox addons:

- Selenium IDE (records your screen)
- Selenium IDE Codeception Formatter (Export your Selenium IDE test to Codeception language)
