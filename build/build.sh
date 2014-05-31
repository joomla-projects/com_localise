#!/bin/sh
rm -rf packaging && mkdir packaging
rm -rf packages && mkdir packages
cp -r ../administrator/components/com_localise packaging/admin
cp -r ../media/com_localise packaging/media
cp ../localise.xml packaging/localise.xml
mv packaging/admin/script.php packaging/script.php
cd packaging
zip -r ../packages/com_localise.zip admin/ media/ localise.xml script.php
