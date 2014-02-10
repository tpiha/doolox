#!/bin/bash

echo Removing existing \'doolox\' directory

rm -rf doolox


echo Removing new \'doolox\' directory

mkdir doolox


echo Copying files and folders

cp -R app doolox/
cp -R bootstrap doolox/
cp -R public doolox/
cp -R bootstrap doolox/
cp -R tools doolox/
cp -R vendor doolox/
cp CHANGELOG.md doolox/
cp LICENSE doolox/
cp README.md doolox/
cp server.php doolox/
cp TODO.md doolox/


echo Creating installation files and folders

mkdir doolox/users/
mkdir doolox/websites/
touch doolox/app/storage/install
chmod 777 doolox/app/storage/install
cp app/config/app.sample.php doolox/app/config/app.php
cp app/config/database.sample.php doolox/app/config/database.php
cp app/config/doolox.sample.php doolox/app/config/doolox.php
cp app/config/mail.sample.php doolox/app/config/mail.php
chmod 777 doolox/app/config/app.php
chmod 777 doolox/app/config/database.php
chmod 777 doolox/app/config/doolox.php
chmod 777 doolox/app/config/mail.php
rm -rf doolox/app/storage/cache/*
rm -rf doolox/app/storage/logs/*
rm -rf doolox/app/storage/meta/*
rm -rf doolox/app/storage/session/*
rm -rf doolox/app/storage/views/*
chmod -R 777 doolox/app/storage/


echo Creating doolox package and removing extra folders

rm doolox.zip
zip -r doolox doolox > /dev/null 2>&1
rm -rf doolox/


echo Doolox release package created