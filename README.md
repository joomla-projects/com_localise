Localise
========

This component was originally created by:

Yoshiki Kozaki, Mohammad Hasani Eghtedar,Christophe Demko, Jean-Marie Simonet and Ifan Evans.

We (the Joomla! Internationalisation Working Group) are forking it for experimental purposes.

If you want to join the effort please contact us!

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



# Travis Status
Develop: [![Build Status](https://travis-ci.org/joomla-projects/com_localise.png)](https://travis-ci.org/joomla-projects/com_localise)

# Requirements
Joomla 3.3 or above is needed to run this component.

# Extension packager
There is available a shell script that will package the extension for install into the Joomla CMS. 

To package, on a *nix system, navigate to the build folder and run ./build.sh. When complete, you will have an installable .zip file under build/packages.
