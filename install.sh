#!/bin/sh
chmod -R 777 runtime
chmod -R 777 web/assets
mkdir web/upload
chmod -R 777 web/upload
chmod 777 ./composer.phar
./composer.phar self-update
./composer.phar update