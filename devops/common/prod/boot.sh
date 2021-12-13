#!/usr/bin/env bash

cd /var/www/html/ || exit

composer install
composer update

ARTISAN_ENV=development
php artisan --env=$ARTISAN_ENV optimize:clear
php artisan --env=$ARTISAN_ENV key:generate
php artisan --env=$ARTISAN_ENV migrate
php artisan --env=$ARTISAN_ENV db:seed
php artisan --env=$ARTISAN_ENV db:seed --class=AfterBuildSeeder
php artisan --env=$ARTISAN_ENV storage:link

# Start queue service
service supervisor start
supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*
supervisorctl start laravel-scheduler:*

if service nginx start | grep failed; then
   exit 1
fi

php-fpm --allow-to-run-as-root
