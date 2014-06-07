This document is still a work in progress. Feel free to expand.

# Purpose

The purpose of this document is to explain basic steps to run the system tests for com_localise.

# Background

System tests are run via Selenium Webdriver. They are meant to create and command a browser instance so that we can test basic operations (i.e. install the extension)

# Environment

To run the tests, all you need is a machine able to run JAVA and PHP commands.

## Selenium Server

You may download the Selenium Server JAR file from http://docs.seleniumhq.org/download/. See the paragraph called "Selenium Server (formerly the Selenium RC Server)".
To start the server, from a terminal run the command:

    java -jar selenium-server.jar 
    
P.S. replace "selenium-server.jar" with the exact name of the file you just downloaded.

The Server should be running at this point, if you see a lot of writings on the screen it's fine.

## Test suite

On another terminal you can run the specific test. Please note currently only one test ("InstallationTest") is ready.

To run it, launch the command

    php path/to/com_localise/tests/system/webdriver/tests/installation/InstallTest.php
    
The test itself will launch exceptions if something goes wrong; if it gets to the end without exceptions, everything is fine.
    
# Conclusions

Please let us know if this document is incomplete or incorrect and someone will try to fix it


