#!/bin/bash

cd /var/www/html
composer install
php web/handler.php