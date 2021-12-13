#!/usr/bin/env bash

echo "Sleep 15 seconds"
sleep 30
echo "Init PHP code"

cd /var/www/html

/usr/local/bin/composer --version

/usr/local/bin/composer install
/usr/local/bin/composer dump-autoload

#vars='\$REMOTE_DEBUG_HOST \$REMOTE_DEBUG_PORT \$REMOTE_DEBUG_IDE_KEY'

#export REMOTE_DEBUG_HOST=${REMOTE_DEBUG_HOST} \
#       REMOTE_DEBUG_PORT=${REMOTE_DEBUG_PORT} \
#       REMOTE_DEBUG_IDE_KEY=${REMOTE_DEBUG_IDE_KEY}

#envsubst "$vars" < "/usr/local/etc/php/conf.d/99-xdebug.ini.tpl" > "/usr/local/etc/php/conf.d/99-xdebug.ini"

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
