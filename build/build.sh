#!/bin/sh
rm -rf packaging && mkdir packaging
rm -rf packages && mkdir packages
cp -r ../component packaging
cp -r ../media packaging
cp ../localise.xml packaging
cp ../install.php packaging
cd packaging
zip -r ../packages/com_localise.zip component/ media/ localise.xml install.php
