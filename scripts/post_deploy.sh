#!/bin/bash
cd /var/web/html_new/ipost
#aws s3 cp s3://ipost-codedeploy-deployments/development/.env /var/web/html_new/ipost
composer update
php artisan cache:clear
php artisan config:cache
composer dump-autoload