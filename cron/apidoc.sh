#!/bin/sh
cd ..
rm vendor/yiisoft/yii2-apidoc/commands/ApiController.php
cp commands/ApiController.php vendor/yiisoft/yii2-apidoc/commands
rm -r ../developmentDocumentation
vendor/bin/apidoc api ../ ../developmentDocumentation
