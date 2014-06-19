#!/bin/sh
rm -rf packaging && mkdir packaging
rm -rf packages && mkdir packages
cp -r ../component/admin packaging/admin
cp -r ../media/ packaging/media
mv packaging/admin/localise.xml packaging/localise.xml
mv packaging/admin/script.php packaging/script.php
cd packaging
zip -r ../packages/com_localise.zip admin/ media/ localise.xml script.php
