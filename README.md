Localise
========

This component was originally created by:

Yoshiki Kozaki, Mohammad Hasani Eghtedar,Christophe Demko, Jean-Marie Simonet and Ifan Evans.

We (the Joomla! Internationalisation Working Group) are forking it for experimental purposes.

If you want to join the effort please contact us!

# Travis Status
Master: [![Build Status](https://api.travis-ci.org/joomla-projects/com_localise.svg?branch=master)](https://travis-ci.org/joomla-projects/com_localise)

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
See testing documentation at [test readme](./tests/system/readme.md)

# Requirements
Joomla 3.3 or above is needed to run this component.

# Extension packager
There are two available ways to package the extension a PHING packager xml file (requires PHING) and a *nix shell script:

## Phing packager

A PHING build file can be found at build/build.xml. After executing it you will have an installable .zip file under build/packages.

## Shell script for *nix systems

To package, on a *nix system, navigate to the build folder and run ./build.sh. When complete, you will have an installable .zip file under build/packages.
